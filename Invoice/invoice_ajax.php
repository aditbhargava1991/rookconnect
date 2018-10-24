<?php
include('../include.php');
ob_clean();

if(!empty($_GET['action']) && $_GET['action'] == 'update_status') {
    $invoiceid = $_GET['invoice'];
    $status = $_GET['status'];
    $status_history = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' changed status to '.$status.' at '.date('Y-m-d h:i');
	if($status == 'Archived') {
		$query_update = "UPDATE `invoice` SET deleted = '1', status = '$status', status_history = '$status_history' WHERE invoiceid='$invoiceid AND `invoiceid` NOT IN (SELECT `invoiceid` FROM `invoice_patient` WHERE `paid`!='On Account' AND `paid`!='' AND `paid` IS NOT NULL UNION SELECT `invoiceid` FROM `invoice_insurer` WHERE `paid`='Yes')";
	} else if($status == 'Voided') {
		$query_update = "UPDATE `invoice` SET status = '$status', status_history = '$status_history' WHERE invoiceid='$invoiceid AND `invoiceid` NOT IN (SELECT `invoiceid` FROM `invoice_patient` WHERE `paid`!='On Account' AND `paid`!='' AND `paid` IS NOT NULL UNION SELECT `invoiceid` FROM `invoice_insurer` WHERE `paid`='Yes')";
	} else {
		$query_update = "UPDATE `invoice` SET status = '$status', status_history = '$status_history' WHERE invoiceid='$invoiceid'";
	}
    $result_update = mysqli_query($dbc, $query_update);
}
if(!empty($_GET['fill']) && $_GET['fill'] == 'retrieve_injuries') {
    $contactid = $_GET['contactid'];
    $each_injury = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `patient_injury` WHERE `contactid` = '".$contactid."' AND discharge_date IS NULL AND deleted = 0"),MYSQLI_ASSOC);
    echo '<option></option>';
    foreach ($each_injury as $injury) {
        $total_injury = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`bookingid`) as total_injury FROM `booking` WHERE `injuryid` = '".$injury['injuryid']."'"));

        $treatment_plan = get_all_from_injury($dbc, $injury['injuryid'], 'treatment_plan');
        $final_treatment_done = '';
        if ($treatment_plan != '') {
            $final_treatment_done = ' : '.($total_injury['total_injury']+1).'/'.$treatment_plan;
        }

        echo "<option value='".$injury['injuryid']."'>".$injury['injury_type'].' : '.$injury['injury_name'].' : '.$injury['injury_date'].$final_treatment_done."</option>";
    }
}
if(!empty($_GET['action']) && $_GET['action'] == 'invoice_values') {
    $current_tile_name = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
    $field_name = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
    $table_name = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
    $id_field = filter_var($_POST['id_field'],FILTER_SANITIZE_STRING);
    $invoiceid = filter_var($_POST['invoiceid'],FILTER_SANITIZE_STRING);
    $line_id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
    $field_value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);

	//Create invoices and line items
    if($table_name == 'invoice' && $_POST['invoiceid'] > 0) {
		$id_field = 'invoiceid';
		$line_id = filter_var($_POST['invoiceid'],FILTER_SANITIZE_STRING);
	} else if($table_name == 'invoice') {
		$today_date = date('Y-m-d');
		mysqli_query($dbc, "INSERT INTO `invoice` (`invoice_type`, `invoice_date`, `tile_name`, `created_by`) VALUES ('New', '$today_date', '$current_tile_name', '".$_SESSION['contactid']."')");
		$line_id = mysqli_insert_id($dbc);
		echo $line_id;
	}
	mysqli_query($dbc, "UPDATE `invoice_payment` LEFT JOIN `invoice` ON `invoice_payment`.`invoiceid`=`invoice`.`invoiceid` LEFT JOIN `invoice_lines` ON `invoice_lines`.`line_id`=`invoice_payment`.`line_id` SET `invoice_payment`.`contactid`=`invoice`.`patientid`, `invoice_payment`.`deleted`=IF(`invoice_payment`.`deleted`=0,IFNULL(`invoice_lines`.`deleted`,`invoice`.`deleted`),1) WHERE `invoice`.`invoiceid`='$invoiceid'");

	//Prevent changes to invoices from previous days
	$current_invoice = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `invoiceid` FROM `invoice` WHERE '$invoiceid' IN (`invoiceid`, `invoiceid_src`) AND `invoice_date`=DATE(NOW())"));
	if($current_invoice['invoiceid'] > 0) {
		$invoiceid = $current_invoice['invoiceid'];
	} else {
		mysqli_query($dbc, "INSERT INTO `invoice` (`invoice_type`, `tile_name`, `invoiceid_src`, `businessid`, `patientid`, `projectid`, `therapistsid`, `service_date`, `pricing`, `delivery_address`, `created_by`, `invoice_date`) SELECT 'Adjustment', `tile_name`, '$invoiceid', `businessid`, `patientid`, `projectid`, `therapistsid`, `service_date`, `pricing`, `delivery_address`, '".$_SESSION['contactid']."', DATE(NOW()) FROM `invoice` WHERE '$invoiceid' IN (`invoiceid`,`invoiceid_src`) AND `invoice_date`=(SELECT MAX(`invoice_date`) FROM `invoice` WHERE '$invoiceid' IN (`invoiceid`,`invoiceid_src`))");
		$invoiceid = mysqli_insert_id($dbc);
	}
	$invoiceid_src = $current_invoice['invoiceid'];

	//Update inventory quantity
	if($table_name == 'invoice_lines' && $field_name='quantity' && $line_id > 0) {
		$line = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`quantity`) qty, `item`.`item_id`, `item`.`unit_price` FROM `invoice_lines` line LEFT JOIN `invoice_lines` item ON line.`item_id`=item.`item_id` AND line.`category`=item.`category` AND line.`invoiceid`=item.`invoiceid` WHERE item.`line_id`='$line_id' GROUP BY item.`item_id`"));
		$change = $field_value - $line['qty'];
		$inventory = $line['item_id'];
		$price = $line['unit_price'];
		mysqli_query($dbc, "UPDATE `inventory` SET `current_stock` = `current_stock` - $change WHERE `inventoryid` = '$inventory'");
		mysqli_query($dbc, "INSERT INTO `report_inventory` (`invoiceid`, `inventoryid`, `type`, `quantity`, `sell_price`, `today_date`) VALUES ('$invoiceid', '$inventory', '', '$change', '$price', DATE(NOW()))");

		//Send an e-mail if the item is low on stock --DISABLED
		// $item = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `current_stock`, `min_bin` FROM `inventory` WHERE `inventoryid`='$inventory'"));
		// if($item['current_stock'] < $item['min_bin']) {
			// $to = get_config($dbc, 'minbin_email');
			// $subject = 'Inventory Min Bin Email';

			// $message = $inventory_desc.' is reduced to min bin. Please check that.';
			// $message = "<br><br><a href='".WEBSITE_URL."/Inventory/add_inventory.php?inventoryid=".$inventoryid."'>Click to View Product</a>";
			// try {
				// send_email('', $to, '', '', $subject, $message, '');
			// } catch(Exception $e) { }
		// }
	} else if(!($line_id > 0) || $invoiceid_src != $invoiceid) {
		mysqli_query($dbc, "INSERT INTO `$table_name` (`invoiceid`) VALUES ($invoiceid)");
		$line_id = mysqli_insert_id($dbc);
		if(!empty($category)) {
			mysqli_query($dbc, "UPDATE `$table_name` SET `category`='$category' WHERE `$id_field`='$line_id'");
		}
		$attach_field = filter_var($_POST['attach_field'],FILTER_SANITIZE_STRING);
		$attach_id = filter_var($_POST['attach_id'],FILTER_SANITIZE_STRING);
		if(!empty($attach_field)) {
			mysqli_query($dbc, "UPDATE `$table_name` SET `$attach_field`='$attach_id' WHERE `$id_field`='$line_id'");
		}
		echo $line_id;
	}

	if(is_array($field_value)) {
		$field_value = implode(',',$field_value).',';
	} else if($table_name == 'invoice_lines' && in_array($field_name, ['quantity','sub_total','pst','gst','total'])) {
		$field_value -= mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(`$field_name`) `result` FROM `invoice_lines` WHERE `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE '$invoiceid_src' IN (`invoiceid`,`invoiceid_src`) AND `deleted`=0) AND `category`='$category' AND `item_id` IN (SELECT `item_id` FROM `invoice_lines` WHERE `line_id`='$line_id')"))['result'];
	}
	mysqli_query($dbc, "UPDATE `$table_name` SET `$field_name` = '$field_value' WHERE `$id_field` = '$line_id'");
} else if($_GET['action'] == 'get_address') {
	$contactid = filter_var($_GET['contactid'],FILTER_SANITIZE_STRING);
	echo get_address($dbc, $contactid);
} else if($_GET['action'] == 'book_appt') {
	$contactid = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);
	$injuryid = filter_var($_POST['injuryid'],FILTER_SANITIZE_STRING);
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$start = filter_var($_POST['start'],FILTER_SANITIZE_STRING);
	$end = filter_var($_POST['end'],FILTER_SANITIZE_STRING);
	$type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `booking` (`today_date`, `patientid`, `injuryid`, `therapistsid`, `appoint_date`, `end_appoint_date`, `type`, `create_by`)
		VALUES ('".date('Y-m-d')."', '$contactid', '$injuryid', '$staff', '$start', '$end', '$type', '".get_contact($dbc, $_SESSION['contactid'])."')");
} else if($_GET['action'] == 'send_survey') {
	$contact = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$invoice = filter_var($_POST['invoice'],FILTER_SANITIZE_STRING);
	if($_POST['survey'] > 0) {
		$survey = filter_var($_POST['survey'],FILTER_SANITIZE_STRING);
        $send_date = date('Y-m-d');
        $query_insert_inventory = "INSERT INTO `crm_feedback_survey_result` (`surveyid`, `patientid`, `therapistid`, `send_date`) VALUES	('$survey', '$contact', '$staff', '$send_date')";
        $result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);
        $surveyresultid = mysqli_insert_id($dbc);

        $survey_link = WEBSITE_URL.'/CRM/feedback_survey.php?s='.$surveyresultid;

        $feedback_survey_email_body = html_entity_decode(get_config($dbc, 'feedback_survey_email_body'));

        $email_body = str_replace("[Customer Name]", get_contact($dbc, $contact), $feedback_survey_email_body);
        $email_body = str_replace("[Survey Link]", $survey_link, $email_body);
        $email = get_email($dbc, $contact);
        $subject = get_config($dbc, 'feedback_survey_email_subject');

        send_email('', $email, '', '', $subject, $email_body, '');

        $query_update_booking = "UPDATE `invoice` SET `survey` = '$surveyid' WHERE `invoiceid` = '$invoice'";
        $result_update_booking = mysqli_query($dbc, $query_update_booking);
	} else if($_POST['survey'] == 'recommendation') {
		$send_date = date('Y-m-d');
		$query_insert = "INSERT INTO `crm_recommend` (`patientid`, `therapistid`, `send_date`) VALUES	('$contact', '$staff', '$send_date')";
		$result = mysqli_query($dbc, $query_insert);
		$recommendid = mysqli_insert_id($dbc);

		$link = WEBSITE_URL.'/CRM/recommend_request.php?s='.$recommendid;

		$email_body = str_replace(["[Customer Name]","[Link]"], [get_contact($dbc, $contact),$link], html_entity_decode(get_config($dbc, 'crm_recommend_body')));
		$email = get_email($dbc, $contact);
		$subject = get_config($dbc, 'crm_recommend_subject');
		$from_address = get_config($dbc, 'crm_recommend_address');

		try {
			send_email($from_address, $email, '', '', $subject, $email_body, '');
		} catch  (Exception $e) {
			echo "<script> alert('Unable to send email to patient.') </script>";
		}
	} else if($_POST['survey'] == 'massage') {
		$email_body = "Dear Valued Client,<br><br><br>";
		$email_body .= "The center of our attention is to consistently provide quality therapy, and our reputation is built on our ability to not only meet, but exceed your expectations. <br><br>
		Your customized massage plan was setup to optimize your results and minimize pain and discomfort.  We truly care about our patients and we hope you are feeling your best. We make your healing a priority, and we hope you’ll continue with us in the future. <br><br>
		We will facilitate your total recovery and allow you to get back to the activities you love without fear of injury.";
		$email_body .= "We hope to hear from you soon.<br><br>
		Please e-mail or call us at 403-295-8590.<br><br>
		Warmest regards,<br>
		Your Nose Creek Sport Physical Therapy<br>
		and Massage Therapy Team";

		//Mail
		$email = get_email($dbc, $contact);
		$subject = 'Follow Up Email From Nose Creek Sport Physical Therapy';

		send_email('', $email, '', '', $subject, $email_body, '');
	} else if($_POST['survey'] == 'physio') {
		$email_body = "Dear Valued Client,<br><br><br>";
		$email_body .= "The center of our attention is to consistently provide quality therapy, and our reputation is built on taking extra care of your health and well-being.<br><br>
		Your customized treatment plan was setup to optimize your results and minimize the chance of reinjury.  We truly care about our clients and when they fail to finish their program we become concerned.  We haven't seen you in the clinic for over a week and as experience has taught us, although you may be pain free and feeling better, failing to totally complete your rehab program will not give you the ideal long term results.<br><br>
		We hope you will make your healing a priority, and work with us to complete your program.  If you are pain free and feel you are ready for graduation, give us a call so we can assess and make sure you are ready to return to activity. This will prevent re-injury and set you up for success.  We will facilitate your total recovery and allow you to get back to the activities you love without fear of relapse. <br><br>";
		$email_body .= "We hope to hear from you soon.<br><br>
		Please e-mail or call us at 403-295-8590.<br><br>
		Warmest regards,<br>
		Your Nose Creek Sport Physical Therapy<br>
		and Massage Therapy Team";

		//Mail
		$email = get_email($dbc, $contact);
		$subject = 'Follow Up Email From Nose Creek Sport Physical Therapy';

		send_email('', $email, '', '', $subject, $email_body, '');
	}
} else if($_GET['action'] == 'general_config') {
	$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$value = $_POST['value'];
	if(is_array($value)) {
		$value = implode(',', $value);
	}
	$value = filter_var($value, FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT '$name' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='$name') num WHERE num.rows=0");echo "INSERT INTO `general_configuration` SELECT '$name' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='$name') num WHERE num.rows=0";
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$value' WHERE `name`='$name'");
} else if($_GET['action'] == 'load_ticket_details') {
	$value_config = ','.get_config($dbc, 'project_admin_fields').',';
	$ticketid = $_POST['ticketid'];

	$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `tickets`.*, SUM(`ticket_schedule`.`id`) `deliveries` FROM `tickets` LEFT JOIN `ticket_schedule` ON `tickets`.`ticketid`=`ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted`=0 AND IFNULL(`ticket_schedule`.`serviceid`,'') != '' WHERE `tickets`.`ticketid` = '$ticketid'"));
	$total_service_price = 0;
	if($ticket['ticketid'] > 0) { ?>
		<?php if(!empty($ticket['serviceid']) || $ticket['deliveries'] > 0) { ?>
			<div class="form-group clearfix hide-titles-mob">
				<label class="col-sm-3 text-center">Category</label>
				<label class="col-sm-5 text-center">Service Name</label>
				<label class="col-sm-2 text-center">Qty</label>
				<label class="col-sm-2 text-center">Fee</label>
			</div>
		<?php }
        $businessid = $ticket['businessid'];
        $clientid = implode("','",explode(',',$ticket['clientid']));
        $business_rates = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `services`, `staff`, `staff_position` FROM `rate_card` WHERE `clientid` IN ('$rate_contact', '$businessid', '$clientid') AND `clientid` != '' AND `deleted`=0 ORDER BY `clientid`='$rate_contact' DESC"));
        $service_rates = explode('**',$business_rates['services']);
        $serviceid = [];
        $srv_qty = [];
        $srv_fuel = [];
        $srv_discount = [];
        $srv_dis_type = [];
		foreach(explode(',',$ticket['serviceid']) as $i => $service) {
			if($service > 0) {
                $serviceid[] = $service;
				$srv_qty[] = explode(',',$ticket['service_qty'])[$i] > 0 ? explode(',',$ticket['service_qty'])[$i] : 1;
				$srv_fuel[] = explode(',',$ticket['service_fuel_charge'])[$i];
				$srv_discount[] = explode(',',$ticket['service_discount'])[$i];
				$srv_dis_type[] = explode(',',$ticket['service_discount_type'])[$i];
            }
        }
        $ticket_deliveries = $dbc->query("SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 ORDER BY `sort`");
        while($ticket = $ticket_deliveries->fetch_assoc()) {
            foreach(explode(',',$ticket['serviceid']) as $i => $service) {
                if($service > 0) {
                    $serviceid[] = $service;
                    $srv_qty[] = 1;
                    $srv_fuel[] = explode(',',$ticket['surcharge'])[$i];
                    $srv_discount[] = explode(',',$ticket['service_discount'])[$i];
                    $srv_dis_type[] = explode(',',$ticket['service_discount_type'])[$i];
                }
            }
        }
        foreach($serviceid as $i => $service) {
            $qty = $srv_qty[$i];
            $discount = $srv_discount[$i];
            $dis_type = $srv_dis_type[$i];
            $fuel = $srv_fuel[$i];
            $price = 0;
            foreach($service_rates as $rate_line) {
                $rate_line = explode('#',$rate_line);
                if($rate_line[0] == $service) {
                    $price = $rate_line[1];
                }
            }
            if($price == 0) {
                $price = $_SERVER['DBC']->query("SELECT `cust_price` FROM `company_rate_card` WHERE `item_id`='$service' AND `tile_name` LIKE 'Services' AND `deleted`=0 AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-99-99') > NOW() ORDER BY `start_date` DESC")->fetch_assoc()['cust_price'];
            }
            $price += ($fuel / $qty);
            $price -= (($dis_type == '%' ? $discount / 100 * $price_total : $discount) / $qty);
            $total_service_price += $price;
            $service_details = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$service'")); ?>
            <div class="dis_service form-group">
                <div class="col-sm-3">
                    <input type="text" readonly name="service_cat[]" value="<?= $service_details['category'] ?>" class="form-control">
                </div>
                <div class="col-sm-5">
                    <input type="text" readonly name="service_name[]" value="<?= $service_details['heading'] ?>" class="form-control">
                </div>
                <div class="col-sm-2">
                    <input type="text" readonly name="srv_qty[]" value="<?= $qty ?>" class="form-control qty" />
                </div>
                <div class="col-sm-2">
                    <input type="text" readonly name="fee[]" value="<?= $price ?>" class="form-control fee" />
                </div>
                <input type="hidden" name="service_ticketid[]" value="<?= $ticketid ?>">
                <input type="hidden" readonly name="serviceid[]" value="<?= $service ?>" class="form-control serviceid">
                <input type="hidden" readonly name="gst_exempt[]" value="<?= $service_details['gst_exempt'] ?>" class="form-control gstexempt" />
            </div>
        <?php }
		$ticket_lines = $dbc->query("SELECT * FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `src_table` LIKE 'Staff%'");
		$misc_headings = false;
		if(mysqli_num_rows($ticket_lines) > 0) {
			$misc_headings = true; ?>
			<div class="form-group clearfix hide-titles-mob">
				<label class="col-sm-5 text-center">Product Name</label>
				<label class="col-sm-3 text-center">Price</label>
                <label class="col-sm-1 text-center">Qty</label>
                <label class="col-sm-3 text-center">Total</label>
			</div>
		<?php }
		while($line = $ticket_lines->fetch_assoc()) {
			$description = get_contact($dbc, $line['item_id']).' - '.$line['position'];
			$qty = !empty($line['hours_set']) ? $line['hours_set'] : $line['hours_tracked'];
			$price = $dbc->query("SELECT * FROM `company_rate_card` WHERE `deleted`=0 AND (`cust_price` > 0 OR `hourly` > 0) AND ((`tile_name`='Staff' AND (`item_id`='".$line['item_id']."' OR `description`='all_staff')) OR (`tile_name`='Position' AND (`description`='".$line['position']."' OR `item_id`='".get_field_value('position_id','positions','name',$line['position'])."')))")->fetch_assoc();
			$price = $price['cust_price'] > 0 ? $price['cust_price'] : $price['hourly']; ?>
			<div class="dis_misc form-group">
				<div class="col-sm-5">
					<input type="text" readonly name="misc_item[]" value="<?= $description ?>" class="form-control misc_name">
				</div>
				<div class="col-sm-3">
					<input type="text" readonly name="misc_price[]" value="<?= $price ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_price">
				</div>
				<div class="col-sm-1">
					<input type="text" readonly name="misc_qty[]" value="<?= $qty ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_qty">
				</div>
				<div class="col-sm-3">
					<input type="text" readonly name="misc_total[]" value="<?= $price * $qty ?>" class="form-control misc_total">
				</div>
				<input type="hidden" name="misc_ticketid[]" value="<?= $ticketid ?>">
			</div>
		<?php }
		$ticket_lines = $dbc->query("SELECT * FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `src_table` LIKE 'misc_item'");
		if(mysqli_num_rows($ticket_lines) > 0 && !$misc_headings) {
			$misc_headings = true; ?>
			<div class="form-group clearfix hide-titles-mob">
				<label class="col-sm-5 text-center">Product Name</label>
				<label class="col-sm-3 text-center">Price</label>
                <label class="col-sm-1 text-center">Qty</label>
                <label class="col-sm-3 text-center">Total</label>
			</div>
		<?php }
		while($line = $ticket_lines->fetch_assoc()) {
			$description = get_contact($dbc, $line['description']);
			$qty = $line['qty'];
			$price = $line['rate']; ?>
			<div class="dis_misc form-group">
				<div class="col-sm-5">
					<input type="text" readonly name="misc_item[]" value="<?= $description ?>" class="form-control misc_name">
				</div>
				<div class="col-sm-3">
					<input type="text" readonly name="misc_price[]" value="<?= $price ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_price">
				</div>
				<div class="col-sm-1">
					<input type="text" readonly name="misc_qty[]" value="<?= $qty ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_qty">
				</div>
				<div class="col-sm-3">
					<input type="text" readonly name="misc_total[]" value="<?= $price * $qty ?>" class="form-control misc_total">
				</div>
				<input type="hidden" name="misc_ticketid[]" value="<?= $ticketid ?>">
			</div>
		<?php }
        if(strpos($value_config, ',Additional KM Charge,') !== FALSE) {
            $travel_km = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`hours_travel`) `travel_km` FROM `ticket_attached` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0"))['travel_km'];
            $total_travel_km = $total_service_price * $travel_km;
            if($total_travel_km > 0) {
				if(!$misc_headings) {
					$misc_headings = true; ?>
					<div class="form-group clearfix hide-titles-mob">
						<label class="col-sm-5 text-center">Product Name</label>
						<label class="col-sm-3 text-center">Price</label>
		                <label class="col-sm-1 text-center">Qty</label>
		                <label class="col-sm-3 text-center">Total</label>
					</div>
				<?php }
                $description = 'Additional KM Charge';
                $qty = 1;
                $price = $total_travel_km; ?>
				<div class="dis_misc form-group">
					<div class="col-sm-5">
						<input type="text" readonly name="misc_item[]" value="<?= $description ?>" class="form-control misc_name">
					</div>
					<div class="col-sm-3">
						<input type="text" readonly name="misc_price[]" value="<?= $price ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_price">
					</div>
					<div class="col-sm-1">
						<input type="text" readonly name="misc_qty[]" value="<?= $qty ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_qty">
					</div>
					<div class="col-sm-3">
						<input type="text" readonly name="misc_total[]" value="<?= $price * $qty ?>" class="form-control misc_total">
					</div>
					<input type="hidden" name="misc_ticketid[]" value="<?= $ticketid ?>">
				</div>
            <?php }
        }
	}
} else if($_GET['action'] == 'void_invoice') {
    $invoiceid = preg_replace('/[^0-9]/', '', $_POST['invoiceid']);
    mysqli_query($dbc, "UPDATE `invoice` SET `status`='Void' WHERE `invoiceid`='$invoiceid'");
}
if(!empty($_GET['action']) && $_GET['action'] == 'export_pos_file') {
    $invoiceid = $_GET['invoice'];
    //invoice HTML
    $point_of_sell = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));
	if(empty($posid)) {
		$posid = $invoiceid;
	}
	$contactid		= $point_of_sell['patientid'];
	$couponid		= (isset($point_of_sell['couponid']) ? $point_of_sell['couponid'] : '');
	$coupon_value	= (isset($point_of_sell['coupon_value']) ? $point_of_sell['coupon_value'] : '');
	$dep_total		= (isset($point_of_sell['deposit_total']) ? $point_of_sell['deposit_total'] : '');
	$updatedtotal	= (isset($point_of_sell['updatedtotal']) ? $point_of_sell['updatedtotal'] : '');
	$customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid='$contactid'"));

	//Tax
	$point_of_sell_product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(gst) AS total_gst, SUM(pst) AS total_pst FROM invoice_lines WHERE invoiceid='$invoiceid'"));

	$get_pos_tax = get_config($dbc, 'pos_tax');
	$pdf_tax = '';
	$pdf_tax_number = '';
	$gst_rate = 0;
	$pst_rate = 0;
	if($get_pos_tax != '') {
		$pos_tax = explode('*#*',$get_pos_tax);

		$total_count = mb_substr_count($get_pos_tax,'*#*','UTF-8');
		for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
			$pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

			if (strcasecmp($pos_tax_name_rate[0], 'gst') == 0) {
				$taxrate_value = $point_of_sell['gst_amt'];
	            $gst_rate = $pos_tax_name_rate[1];
			}
			if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0) {
				$taxrate_value = $point_of_sell['pst_amt'];
	            $pst_rate = $pos_tax_name_rate[1];
			}

			if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {

			} else {
				//$pdf_tax .= $pos_tax_name_rate[0] .' : '.$pos_tax_name_rate[1].'% : $'.$taxrate_value.'<br>';
				$pdf_tax .= '<tr><td align="right" width="75%" colspan="10"><strong>'.$pos_tax_name_rate[0] .'['.$pos_tax_name_rate[1].'%]['.$pos_tax_name_rate[2].']</strong></td><td align="right" border="1" width="25%" style="" colspan="2">$'.$taxrate_value.'</td></tr>';
			}

			$pdf_tax_number .= $pos_tax_name_rate[0].' ['.$pos_tax_name_rate[2].'] <br>';

			if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {
				$client_tax_number = $pos_tax_name_rate[0].' ['.$tax_exemption_number.']';
			}
		}
	}
	//Tax

	$invoice_footer = get_config($dbc, 'invoice_footer');
	if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_footer_'.$point_of_sell['type']))) {
	    $invoice_footer = get_config($dbc, 'invoice_footer_'.$point_of_sell['type']);
	}
	$payment_type = explode('#*#', $point_of_sell['payment_type']);

	$invoice_logo = get_config($dbc, 'invoice_logo');
	if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_logo_'.$point_of_sell['type']))) {
	    $invoice_logo = get_config($dbc, 'invoice_logo_'.$point_of_sell['type']);
	}
	$logo = 'download/'.$invoice_logo;
	if(!file_exists($logo)) {
	    $logo = dirname(__DIR__).'/POSAdvanced/'.$logo;
	    if(!file_exists('../POSAdvanced/download/'.$invoice_logo)) {
	        $logo = '';
	    }
	}else{
		$logo = dirname(__FILE__).'/'.$logo;
	}

	$invoice_header = get_config($dbc, 'invoice_header');
	if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_header_'.$point_of_sell['type']))) {
	    $invoice_header = get_config($dbc, 'invoice_header_'.$point_of_sell['type']);
	}
	DEFINE('POS_LOGO', $logo);
	DEFINE('INVOICE_HEADER', $invoice_header);
	DEFINE('INVOICE_FOOTER', $invoice_footer);
	DEFINE('INVOICE_DATE', $point_of_sell['invoice_date']);
	DEFINE('INVOICEID', $posid);
	DEFINE('COMPANY_SOFTWARE_NAME', $company_software_name);
	DEFINE('SHIP_DATE', $point_of_sell['ship_date']);
	DEFINE('SALESPERSON', decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']));
	DEFINE('PAYMENT_TYPE', $payment_type[0]);

	$html = '';

	$image_file = POS_LOGO;
	if(file_get_contents($image_file)) {
		$image_file = $image_file;
	} else {
		$image_file = dirname(__DIR__).'/Point of Sale/'.$image_file;
	}

	$type = pathinfo($image_file, PATHINFO_EXTENSION);
	$dataimg = file_get_contents($image_file);
	$imgbase64 = 'data:image/' . $type . ';base64,' . base64_encode($dataimg);
	$stripAddress = html_entity_decode($invoice_header, ENT_QUOTES, "UTF-8");
	$html .= '<table style="width:100%;" id="invoiceData">
				<tr rowspan="2">
					<td colspan="4" align="left"><img src="'.$imgbase64.'" width="70" height="70" style=""/>
					</td>
					<td align="right" colspan="8" style="text-align:right;">'.$stripAddress.'</td>
				</tr>
			</table></br>';

	if($_GET['format']=='xsl'){
		//$html .= '<p style="text-align:left;">Box 2052, Sundre, AB, T0M 1X0<br>Phone: 403-638-4030<br>Fax: 403-638-4001<br>Email: info@highlandprojects.com<br>Work Ticket# : </p>';
		if($point_of_sell['invoice_date'] !== '') {
			$tdduedate = '<td>'.date('Y-m-d', strtotime($roww['invoice_date'] . "+30 days")).'</td>';
			$thduedate = '<td>Due Date</td>';
		} else { $tdduedate = ''; $thduedate = ''; }
		$html .= '<table style="width:100%;" id="invoiceData">
					<tr>
						<td style="text-align:left;">
							<table style="width:100%;"><tr><td colspan="6">BILL TO :</td></tr><tr><td colspan="6">'.decryptIt($customer['name']).' '.decryptIt($customer['first_name']).' '.decryptIt($customer['last_name']).($customer['mailing_address']!='' ? '<br>'.$customer['mailing_address']:'').($customer['city']!='' ? '<br>'.$customer['city'].', '.$customer['state'].' '.$customer['zip_code']:'').(decryptIt($customer['cell_phone'])!='' ? '<br>'.decryptIt($customer['cell_phone']):'').(decryptIt($customer['email_address'])!='' ? '<br>'.decryptIt($customer['email_address']):'').'</td></tr>
							</table>
						</td>

						<td colspan ="6" style="text-align:right;">
							<table style="width:100%;"><tr><td align="right" colspan ="6">INVOICE # : '.$invoiceid.'</td></tr><tr><td align="right"  colspan ="6">INVOICE DATE : '.$point_of_sell['invoice_date'].'<br>DUE DATE : '.$point_of_sell['due_date'].'</td></tr>
							</table>
						</td>
					</tr>
				</table>';

		$html .= '<br /><table border="1px" style="width:100%; padding:3px; border:1px solid grey;">
				<tr nobr="true"><td style="text-align:center;" colspan="4">ORDERED BY</td><td style="text-align:center;" colspan="4">P.O. NO.</td><td style="text-align:center;" colspan="4">Area</tr>
		<tr><td style="text-align:center;" colspan="4">'.SALESPERSON.'</td><td style="text-align:center;" colspan="4">'.$point_of_sell['po_num'].'</td><td style="text-align:center;" colspan="4">'.$point_of_sell['area'].'</td></tr>
		</table><br />';

		$html .= '<table border="0x" style="width:100%;padding:3px;">
			<tr nobr="true" style="color:black;  width:22%; border:1px solid grey;">';

		$html .= '<th colspan="2" align="left">TICKET NO.</th><th colspan="2" align="left">LOCATION</th><th colspan="2" align="left">DESCRIPTION</th><th colspan="2" align="left">HRS - QTY</th><th colspan="2" align="left">RATE</th><th colspan="2" align="right">AMOUNT</th></tr>';
		// START INVENTORY & MISC PRODUCTS
		$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'inventory' AND item_id IS NOT NULL");
		$result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product'");
		$return_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`returned_qty`) FROM `invoice_lines` WHERE `invoiceid`='$invoiceid'"))[0];
		$returned_amt = 0;
		$num_rows = mysqli_num_rows($result);
		$num_rows2 = mysqli_num_rows($result2);

		if($num_rows > 0 || $num_rows2 > 0) {
			while ( $row = mysqli_fetch_array ( $result ) ) {
				$inventoryid	= $row['item_id'];
				$price			= $row['unit_price'];
				$quantity		= $row['quantity'];
				$returned		= $row['returned_qty'];

				if ( $inventoryid != '' ) {
					$amount = $price*($quantity-$returned);

					$html .= '<tr>';
						// Don't display Part# for SEA
						//if ( $rookconnect !== 'sea' ) {
							$html .= '<td colspan="2"></td><td colspan="2">' . get_inventory ( $dbc, $inventoryid, 'part_no' ) . '</td>';
						//}
						$html .= '<td colspan="2">' . get_inventory ( $dbc, $inventoryid, 'name' ) . '</td>';
						$html .= '<td colspan="2">' . number_format($quantity,0) . '</td>';
						if($return_result > 0) {
							$html .= '<td colspan="2">'.$returned.'</td>';
						}
						$html .= '<td colspan="2">$'. $price . '</td>';
						$html .= '<td style="text-align:right; " colspan="2" align="right">$'.number_format($amount,2).'</td>';
					$html .= '</tr>';
				}

		        $returned_amt += $price * $returned;
			}

			$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product'");
			while($row = mysqli_fetch_array( $result )) {
				$misc_product = $row['misc_product'];
				$price = $row['unit_price'];
				$qty = $row['quantity'];
				$returned = $row['returned_qty'];

				if($misc_product != '') {
					$html .= '<tr>';
					$html .=  '<td colspan="2"></td><td colspan="2">Not Available</td>';
					$html .=  '<td colspan="2">'.$misc_product.'</td>';
					$html .=  '<td colspan="2">'.number_format($qty,0).'</td>';
					if($return_result > 0) {
						$html .= '<td colspan="2">'.$returned.'</td>';
					}
					$html .=  '<td colspan="2">$'.$price.'</td>';
					$html .=  '<td style="text-align:right; " colspan="2" align="right">$'.$price * ($qty - $returned).'</td>';
					$html .= '</tr>';
				}
			}
		}
		// END INVENTORY AND MISC PRODUCTS

		// START PRODUCTS
		$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'package' AND item_id IS NOT NULL");
		$num_rows3 = mysqli_num_rows($result);
		if($num_rows3 > 0) {
			while($row = mysqli_fetch_array( $result )) {
				$inventoryid = $row['item_id'];
				$price = $row['unit_price'];
				$quantity = $row['quantity'];
				$returned = $row['returned_qty'];

				if($inventoryid != '') {
					$amount = $price*($quantity-$returned);
					$html .= '<tr>';
					$html .=  '<td colspan="2"></td><td colspan="2">'.get_products($dbc, $inventoryid, 'category').'</td>';
					$html .=  '<td colspan="2">'.get_products($dbc, $inventoryid, 'heading').'</td>';
					$html .=  '<td colspan="2">'.number_format($quantity,0).'</td>';
					if($return_result > 0) {
						$html .= '<td colspan="2">'.$returned.'</td>';
					}
					$html .=  '<td colspan="2">$'.$price.'</td>';
					$html .=  '<td style="text-align:right; " colspan="2" align="right">$'.number_format($amount,2).'</td>';
					$html .= '</tr>';
				}
			}
		}
		// END PRODUCTS

		// START SERVICES
		$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'service' AND item_id IS NOT NULL");
		$num_rows4 = mysqli_num_rows($result);
		if($num_rows4 > 0) {
			while($row = mysqli_fetch_array( $result )) {
				$inventoryid = $row['item_id'];
				$price = $row['unit_price'];
				$quantity = $row['quantity'];
				$returned = $row['returned_qty'];

				if($inventoryid != '') {
					$amount = $price*($quantity-$returned);
					$html .= '<tr>';
					$html .=  '<td colspan="2"></td><td colspan="2">'.get_services($dbc, $inventoryid, 'category').'</td>';
					$html .=  '<td colspan="2">'.get_services($dbc, $inventoryid, 'heading').'</td>';
					$html .=  '<td colspan="2">'.number_format($quantity,0).'</td>';
					if($return_result > 0) {
						$html .= '<td colspan="2">'.$returned.'</td>';
					}
					$html .=  '<td colspan="2">$'.$price.'</td>';
					$html .=  '<td style="text-align:right; " colspan="2" align="right">$'.number_format($amount,2).'</td>';
					$html .= '</tr>';
				}
			}
		}
		// END SERVICES

		// START VPL
		$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'vpl' AND item_id IS NOT NULL");
		$num_rows5 = mysqli_num_rows($result);
		if($num_rows5 > 0) {
			while($row = mysqli_fetch_array( $result )) {
				$inventoryid = $row['item_id'];
				$price = $row['unit_price'];
				$quantity = $row['quantity'];
				$returned = $row['returned_qty'];

				if($inventoryid != '') {
					$amount = $price*($quantity-$returned);

					$html .= '<tr>';
					$html .=  '<td colspan="2"></td><td colspan="2">'.get_vpl($dbc, $inventoryid, 'part_no').'</td>';
					$html .=  '<td colspan="2">'.get_vpl($dbc, $inventoryid, 'name').'</td>';
					$html .=  '<td colspan="2">'.number_format($quantity,0).'</td>';
					if($return_result > 0) {
						$html .= '<td colspan="2">'.$returned.'</td>';
					}
					$html .=  '<td colspan="2">$'.$price.'</td>';
					$html .=  '<td style="text-align:right; " colspan="2" align="right">$'.number_format($amount,2).'</td>';
					$html .= '</tr>';
				}
			}
		}
		// END VPL

		// START TIME SHEET
		$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'time_cards' AND item_id IS NOT NULL");
		$num_rows6 = mysqli_num_rows($result);
		if($num_rows6 > 0) {
			while($row = mysqli_fetch_array( $result )) {
				$amount = $row['sub_total'];

				$html .= '<tr>';
				$html .=  '<td colspan="2"></td><td colspan="2">'.$row['heading'].'</td>';
				$html .=  '<td colspan="2">'.number_format($row['quantity'],0).'</td>';
				$html .=  '<td colspan="2">$'.$row['unit_price'].'</td>';
				$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}
		// START TIME SHEET
		$html .= '</table>';

		if($client_tax_number != '') {
			$html .= '<br>Tax Exemption Number : '.$point_of_sell['tax_exemption_number'];
		}
		$html .= '
				<br><br>
				<table border="0" cellpadding="2" style="width:100%;">';
				if ( !empty($couponid) || $coupon_value!=0 ) {
					$html .= '<tr><td style="text-align:right;" width="90%" colspan="10"><strong>Coupon Value</strong></td><td align="right" border="1" width="10%" style="" colspan="2">$'.$point_of_sell['coupon_value'].'</td></tr>';
				}
				if($point_of_sell['discount'] != '' && $point_of_sell['discount'] != 0) {
					$html .= '<tr><td align="right" width="82%" colspan="10"><strong>Total Before Discount</strong></td><td align="right" border="1" width="10%" style="" colspan="2">$'.$point_of_sell['total_price'].'</td></tr>';
					$html .= '<tr><td align="right" width="90%" colspan="10"><strong>Discount Value</strong></td><td align="right" border="1" width="10%" style="" colspan="2">$'.$point_of_sell['discount'].'</td></tr>';
					$html .= '<tr><td align="right" width="90%" colspan="10"><strong>Total After Discount</strong></td><td align="right" border="1" width="10%" style="" colspan="2">$'.number_format($point_of_sell['total_price'] - $point_of_sell['discount'], 2).'</td></tr>';
				} else {
					$html .= '<tr><td align="right" width="90%" colspan="10"><strong>Sub Total</strong></td><td align="right" border="1" width="10%" style="" colspan="2">$'.number_format($point_of_sell['total_price'], 2).'</td></tr>';
				}
				if($point_of_sell['delivery'] != '' && $point_of_sell['delivery'] != 0) {
					$html .= '<tr><td align="right" width="90%" colspan="10"><strong>Delivery</strong></td><td align="right" border="1" width="10%" style="" colspan="2">$'.number_format($point_of_sell['delivery'],2).'</td></tr>';
				}
				if($point_of_sell['assembly'] != '' && $point_of_sell['assembly'] != 0) {
					$html .= '<tr><td align="right" width="90%" colspan="10"><strong>Assembly</strong></td><td align="right" border="1" width="10%" style="" colspan="2">$'.number_format($point_of_sell['assembly'],2).'</td></tr>';
				}

				if($pdf_tax != '') {
					$html .= $pdf_tax;
					//$html .= '<tr><td style="text-align:right;" width="75%"><strong>Tax</strong></td><td width="25%" style="text-align:right;">'.$pdf_tax.'</td></tr>';
				}

				$total_returned_amt = 0;
		        if($returned_amt != 0) {
					$total_tax_rate = ($gst_rate/100) + ($pst_rate/100);
		            $total_returned_amt = $returned_amt + ($returned_amt * $total_tax_rate);
		            $html .= '<tr><td align="right" width="90%" colspan="10"><strong>Returned Total (Including Tax)</strong></td><td align="right" border="1" width="10%" style="" colspan="2">$'.$total_returned_amt.'</td></tr>';
				}


				$html .= '<tr><td align="right" width="90%" colspan="10"><strong>Total</strong></td><td align="right" border="1" width="10%" style="" colspan="2">$'.number_format($point_of_sell['final_price'] - $total_returned_amt, 2).'</td></tr>';
				if($point_of_sell['deposit_paid'] > 0) {
					$html .='<tr><td align="right" width="90%" colspan="10"><strong>Deposit Paid</strong></td><td align="right" border="1" width="10%" style="" colspan="2">$'.$point_of_sell['deposit_paid'].'</td></tr>';
					$html .='<tr><td align="right" width="90%" colspan="10"><strong>Updated Total</strong></td><td align="right" border="1" width="10%" style="" colspan="2">$'.$point_of_sell['updatedtotal'].'</td></tr>';
				}

				$html .= '</table><br><br>';


		$html .= '<br />';

		$html .= $comment.'<br>';
		$html = str_replace('[[FINAL_PRICE]]','$'.number_format($point_of_sell['final_price'] - $total_returned_amt,2),$html);

		header("Pragma: public");
	    header("Expires: 0");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
	    header("Content-Type: application/force-download");
	    header("Content-Type: application/octet-stream");
	    header("Content-Type: application/download");
		header('Content-Disposition: attachment; filename=invoice_'.$invoiceid.'.xls');
	}
	if($_GET['format']=='xml'){
		$stripAddress = strip_tags((string)html_entity_decode($invoice_header));
		$xml = new SimpleXMLElement('<xml/>');
		$first_name = decryptIt($customer['first_name']);
		$last_name = decryptIt($customer['last_name']);
		$cell_phone = decryptIt($customer['cell_phone']);
		$email_address = decryptIt($customer['email_address']);
		$countItems = 1;
		$billfrom = $xml->addChild('bill_from');
	    $billfrom->addChild('office_address', $stripAddress);
		$billto = $xml->addChild('bill_to');
	    $customer = $billto->addChild('customer');
	    $customer->addChild('first_name', $first_name);
	    $customer->addChild('last_name', $last_name);
	    $address = $billto->addChild('billing_address');
	    $address->addChild('mailing_address', $customer['mailing_address']);
	    $address->addChild('city', $customer['city']);
	    $address->addChild('state', $customer['state']);
	    $address->addChild('zip_code', $customer['zip_code']);
	    $address->addChild('cell_phone', $cell_phone);
	    $address->addChild('email_address', $email_address);

	    $invoice = $xml->addChild('invoice');
	    $invoice->addChild('contract_msa', $point_of_sell['contract']);
	    $invoice->addChild('invoice_id', $invoiceid);
	    $invoice->addChild('invoice_date', $point_of_sell['invoice_date']);
	    $invoice->addChild('due_date', $point_of_sell['due_date']);

	    $detail = $xml->addChild('detail');
	    $detail->addChild('ordered_by', SALESPERSON);
	    $detail->addChild('po_number', $point_of_sell['po_num']);
	    $detail->addChild('area', $point_of_sell['area']);

		$items = $xml->addChild('items');

		// START INVENTORY & MISC PRODUCTS
		$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'inventory' AND item_id IS NOT NULL");
		$result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product'");
		$return_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`returned_qty`) FROM `invoice_lines` WHERE `invoiceid`='$invoiceid'"))[0];
		$returned_amt = 0;
		$num_rows = mysqli_num_rows($result);
		$num_rows2 = mysqli_num_rows($result2);

		if($num_rows > 0 || $num_rows2 > 0) {
			$j = $countItems;
			while ( $row = mysqli_fetch_array ( $result ) ) {
				$inventoryid	= $row['item_id'];
				$price			= $row['unit_price'];
				$quantity		= $row['quantity'];
				$returned		= $row['returned_qty'];

				if ( $inventoryid != '' ) {
		        	$j++;
					$amount = $price*($quantity-$returned);
					$items.$countItems = $items->addChild('items'.$countItems);
				    $items.$countItems->addChild('ticket_no.', '');
				    $part_no = get_inventory ( $dbc, $inventoryid, 'part_no' );
				    $items.$countItems->addChild('location', $part_no);
				    $partName = get_inventory ( $dbc, $inventoryid, 'name' );
				    $items.$countItems->addChild('description', $partName);
				    $items.$countItems->addChild('hours_quantity', number_format($quantity,0));
				    $items.$countItems->addChild('rate', '$'.$price);
				    $items.$countItems->addChild('amount', '$'.number_format($amount,2));
				}
		        $returned_amt += $price * $returned;
				$countItems = $j;
			}

			$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product'");
			while($row = mysqli_fetch_array( $result )) {
				$misc_product = $row['misc_product'];
				$price = $row['unit_price'];
				$qty = $row['quantity'];
				$returned = $row['returned_qty'];

				if($misc_product != '') {
					$j++;
					$items.$countItems = $items->addChild('items'.$countItems);
				    $items.$countItems->addChild('ticket_no.', '');
				    $items.$countItems->addChild('location', 'Not Available');
				    $items.$countItems->addChild('description', $misc_product);
				    $items.$countItems->addChild('hours_quantity', number_format($quantity,0));
				    $items.$countItems->addChild('rate', '$'.$price);
				    $amount = $price * ($qty - $returned);
				    $items.$countItems->addChild('amount', '$'.$amount);
				}
				$countItems = $j;
			}
		}
		// END INVENTORY AND MISC PRODUCTS

		// START PRODUCTS
		$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'package' AND item_id IS NOT NULL");
		$num_rows3 = mysqli_num_rows($result);
		if($num_rows3 > 0) {
			while($row = mysqli_fetch_array( $result )) {
				$inventoryid = $row['item_id'];
				$price = $row['unit_price'];
				$quantity = $row['quantity'];
				$returned = $row['returned_qty'];

				if($inventoryid != '') {
					$j++;
					$amount = $price*($quantity-$returned);

					$items.$countItems = $items->addChild('items'.$countItems);
				    $items.$countItems->addChild('ticket_no.', '');
				    $category = get_products($dbc, $inventoryid, 'category');
				    $items.$countItems->addChild('location', $category);
				    $heading = get_products($dbc, $inventoryid, 'heading');
				    $items.$countItems->addChild('description', $heading);
				    $items.$countItems->addChild('hours_quantity', number_format($quantity,0));
				    $items.$countItems->addChild('rate', '$'.$price);
				    $items.$countItems->addChild('amount', '$'.number_format($amount,2));
				}
				$countItems = $j;
			}
		}
		// END PRODUCTS

		// START SERVICES
		$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'service' AND item_id IS NOT NULL");
		$num_rows4 = mysqli_num_rows($result);
		if($num_rows4 > 0) {
			while($row = mysqli_fetch_array( $result )) {
				$inventoryid = $row['item_id'];
				$price = $row['unit_price'];
				$quantity = $row['quantity'];
				$returned = $row['returned_qty'];

				if($inventoryid != '') {
					$j++;
					$amount = $price*($quantity-$returned);

					$items.$countItems = $items->addChild('items'.$countItems);
				    $items.$countItems->addChild('ticket_no.', '');
				    $category = get_services($dbc, $inventoryid, 'category');
				    $items.$countItems->addChild('location', $category);
				    $heading = get_services($dbc, $inventoryid, 'heading');
				    $items.$countItems->addChild('description', $heading);
				    $items.$countItems->addChild('hours_quantity', number_format($quantity,0));
				    $items.$countItems->addChild('rate', '$'.$price);
				    $items.$countItems->addChild('amount', '$'.number_format($amount,2));
				}
				$countItems = $j;
			}
		}
		// END SERVICES

		// START VPL
		$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'vpl' AND item_id IS NOT NULL");
		$num_rows5 = mysqli_num_rows($result);
		if($num_rows5 > 0) {
			while($row = mysqli_fetch_array( $result )) {
				$inventoryid = $row['item_id'];
				$price = $row['unit_price'];
				$quantity = $row['quantity'];
				$returned = $row['returned_qty'];

				if($inventoryid != '') {
					$j++;
					$amount = $price*($quantity-$returned);

					$items.$countItems = $items->addChild('items'.$countItems);
				    $items.$countItems->addChild('ticket_no.', '');
				    $part_no = get_vpl($dbc, $inventoryid, 'part_no');
				    $items.$countItems->addChild('location',$part_no);
				    $partName = get_vpl($dbc, $inventoryid, 'name');
				    $items.$countItems->addChild('description', $partName);
				    $items.$countItems->addChild('hours_quantity', number_format($quantity,0));
				    $items.$countItems->addChild('rate', '$'.$price);
				    $items.$countItems->addChild('amount', '$'.number_format($amount,2));
				}
				$countItems = $j;
			}
		}
		// END VPL

		// START TIME SHEET
		$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'time_cards' AND item_id IS NOT NULL");
		$num_rows6 = mysqli_num_rows($result);
		if($num_rows6 > 0) {
			while($row = mysqli_fetch_array( $result )) {
				$j++;
				$amount = $row['sub_total'];

				$items.$countItems = $items->addChild('items'.$countItems);
			    $items.$countItems->addChild('ticket_no.', '');
			    $items.$countItems->addChild('location',$row['heading']);
			    $items.$countIteems->addChild('description', '');
			    $items.$countItems->addChild('hours_quantity', number_format($row['quantity'],0));
			    $items.$countItems->addChild('rate', '$'.$row['unit_price']);
			    $items.$countItems->addChild('amount', '$'.number_format($amount,2));
				$countItems = $j;
			}
		}
		// START TIME SHEET
		if($client_tax_number != '') {
			$tax_exemption_number = $xml->addChild('tax_exemption_number', $point_of_sell['tax_exemption_number']);
		}
		$tax_exemption_number = $xml->addChild('tax_exemption_number', $point_of_sell['tax_exemption_number']);

		if ( !empty($couponid) || $coupon_value!=0 ) {
			$coupon_value = $xml->addChild('coupon_value', $point_of_sell['coupon_value']);
		}
		if($point_of_sell['discount'] != '' && $point_of_sell['discount'] != 0) {
			$total_before_discount = $xml->addChild('total_before_discount', $point_of_sell['total_price']);
			$discount_value = $xml->addChild('discount_value', $point_of_sell['discount']);
			$total_after_discount = $xml->addChild('total_after_discount', number_format($point_of_sell['total_price'] - $point_of_sell['discount'], 2));
		} else {
			$subtotal = $xml->addChild('subtotal', number_format($point_of_sell['total_price'], 2));
		}
		if($point_of_sell['delivery'] != '' && $point_of_sell['delivery'] != 0) {
			$delivery = $xml->addChild('delivery', number_format($point_of_sell['delivery'],2));
		}
		if($point_of_sell['assembly'] != '' && $point_of_sell['assembly'] != 0) {
			$assembly = $xml->addChild('assembly', number_format($point_of_sell['assembly'],2));
		}

		if($pdf_tax != '') {
			$html .= $pdf_tax;
			//$tax = $xml->addChild('tax', $pdf_tax);
		}

		$total_returned_amt = 0;
        if($returned_amt != 0) {
			$total_tax_rate = ($gst_rate/100) + ($pst_rate/100);
            $total_returned_amt = $returned_amt + ($returned_amt * $total_tax_rate);
            $returned_total_including_tax = $xml->addChild('returned_total_including_tax', '$'.$total_returned_amt);
		}

	   	$xml->addChild('total', '$'.number_format($point_of_sell['final_price'] - $total_returned_amt, 2));

		if($point_of_sell['deposit_paid'] > 0) {
			$xml->addChild('deposite_paid', '$'.$point_of_sell['deposit_paid']);
			$xml->addChild('updated_total', '$'.$point_of_sell['updatedtotal']);
		}

		$html = $xml->asXML();
		Header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename=invoice_'.$invoiceid.'.xml');
	}
	echo $html;die;
} else if($_GET['action'] == 'get_tax_exempt') {
    echo get_field_value('client_tax_exemption', 'contacts', 'contactid', $_POST['contactid']);
}