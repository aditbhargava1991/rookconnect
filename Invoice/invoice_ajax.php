<?php include('../include.php');
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
	$ticketid = $_POST['ticketid'];

	$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	if($ticket['ticketid'] > 0) { ?>
		<?php foreach(explode(',',$ticket['serviceid']) as $i => $service) {
			if($service > 0) {
				$qty = explode(',',$ticket['service_qty'])[$i];
				$fuel = explode(',',$ticket['service_fuel_charge'])[$i];
				$discount = explode(',',$ticket['service_discount'])[$i];
				$dis_type = explode(',',$ticket['service_discount_type'])[$i];
				$price = 0;
				$customer_rate = $dbc->query("SELECT `services` FROM `rate_card` WHERE `clientid`='' AND `deleted`=0 AND `on_off`=1")->fetch_assoc();
				foreach(explode('**',$customer_rate['services']) as $service_rate) {
					$service_rate = explode('#',$service_rate);
					if($service == $service_rate[0] && $service_rate[1] > 0) {
						$price = $service_rate[1];
					}
				}
				if(!($price > 0)) {
					$service_rate = $dbc->query("SELECT `cust_price`, `admin_fee` FROM `company_rate_card` WHERE `deleted`=0 AND `item_id`='$service' AND `tile_name` LIKE 'Services' AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) AND `cust_price` > 0")->fetch_assoc();
					$price = $service_rate['cust_price'];
				}
				$price_total = ($price * $qty + $fuel);
				$price_total -= ($dis_type == '%' ? $discount / 100 * $price_total : $discount);
				$service_details = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$service'")); ?>
				<div class="dis_service gap-top">
					<div class="col-sm-2">
						<label>Service:</label>
					</div>
					<div class="col-sm-6">
						<input type="text" readonly name="service_name[]" value="<?= (!empty($service_details['category']) ? $service_details['category'].': ' : '').$service_details['heading'] ?>" class="form-control">
					</div>
					<div class="col-sm-4">
						<input type="text" readonly name="fee[]" value="<?= $price_total ?>" class="form-control fee" />
					</div>
					<input type="hidden" name="service_ticketid[]" value="<?= $ticketid ?>">
					<input type="hidden" readonly name="serviceid[]" value="<?= $service ?>" class="form-control serviceid">
					<input type="hidden" readonly name="gst_exempt[]" value="0" class="form-control gstexempt" />
				</div>
			<?php }
		}
		$ticket_lines = $dbc->query("SELECT * FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `src_table` LIKE 'Staff%'");
		while($line = $ticket_lines->fetch_assoc()) {
			$description = get_contact($dbc, $line['item_id']).' - '.$line['position'];
			$qty = !empty($line['hours_set']) ? $line['hours_set'] : $line['hours_tracked'];
			$price = $dbc->query("SELECT * FROM `company_rate_card` WHERE `deleted`=0 AND (`cust_price` > 0 OR `hourly` > 0) AND ((`tile_name`='Staff' AND (`item_id`='".$line['item_id']."' OR `description`='all_staff')) OR (`tile_name`='Position' AND (`description`='".$line['position']."' OR `item_id`='".get_field_value('position_id','positions','name',$line['position'])."')))")->fetch_assoc();
			$price = $price['cust_price'] > 0 ? $price['cust_price'] : $price['hourly']; ?>
			<div class="dis_misc">
				<input type="hidden" name="misc_ticketid[]" value="<?= $ticketid ?>">
				<input type="hidden" readonly name="misc_item[]" value="<?= $description ?>" class="form-control misc_name">
				<input type="hidden" readonly name="misc_price[]" value="<?= $price ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_price">
				<input type="hidden" readonly name="misc_qty[]" value="<?= $qty ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_qty">
				<input type="hidden" readonly name="misc_total[]" value="<?= $price * $qty ?>" class="form-control misc_total">
			</div>
		<?php }
		$ticket_lines = $dbc->query("SELECT * FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `src_table` LIKE 'misc_item'");
		while($line = $ticket_lines->fetch_assoc()) {
			$description = get_contact($dbc, $line['description']);
			$qty = $line['qty'];
			$price = $line['rate']; ?>
			<div class="dis_misc">
				<input type="hidden" name="misc_ticketid[]" value="<?= $ticketid ?>">
				<input type="hidden" readonly name="misc_item[]" value="<?= $description ?>" class="form-control misc_name">
				<input type="hidden" readonly name="misc_price[]" value="<?= $price ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_price">
				<input type="hidden" readonly name="misc_qty[]" value="<?= $qty ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_qty">
				<input type="hidden" readonly name="misc_total[]" value="<?= $price * $qty ?>" class="form-control misc_total">
			</div>
		<?php }
	}
}