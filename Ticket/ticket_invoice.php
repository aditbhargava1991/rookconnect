<?php include_once('../Equipment/region_location_access.php');
$ticket_accounting_fields = explode(',', get_config($dbc, 'ticket_accounting_fields'));
$value_config = ','.get_config($dbc, 'project_admin_fields').',';

if($_GET['status'] == 'unbilled') {
    if(isset($_POST['display_all_accounting'])) {
        $_POST['search_region'] = '';
        $_POST['search_classification'] = '';
        $_POST['search_business'] = '';
        $_POST['search_all_contact'] = '';
        $_POST['search_contact'] = '';
        $_POST['search_contact_type'] = '';
        $_POST['search_ticket_type'] = '';
        $_POST['search_start_date'] = '';
        $_POST['search_end_date'] = '';
    }

    $filter_query = '';
    if(!empty($_POST['search_region'])) {
        $filter_query .= " AND `tickets`.`region` = '".$_POST['search_region']."'";
    }
    if(!empty($_POST['search_classification'])) {
        $filter_query .= " AND `tickets`.`classification` = '".$_POST['search_classification']."'";
    }
    if(!empty($_POST['search_business'])) {
        $filter_query .= " AND `tickets`.`businessid` = '".$_POST['search_business']."'";
    }
    if(!empty($_POST['search_contact'])) {
        $filter_query .= " AND CONCAT(',',`tickets`.`clientid`,',') LIKE '%,".$_POST['search_contact'].",%'";
    }
    if(!empty($_POST['search_all_contact'])) {
        $filter_query .= " AND (`tickets`.`businessid` = '".$_POST['search_all_contact']."' OR CONCAT(',',`tickets`.`clientid`,',') LIKE '%,".$_POST['search_all_contact'].",%')";
    }
    if(!empty($_POST['search_contact_type'])) {
        $filter_query .= " AND `tickets`.`ticketid` IN (SELECT `ticketid` FROM `tickets` LEFT JOIN `contacts` ON `tickets`.`businessid`=`contacts`.`contactid` OR CONCAT(',',`tickets`.`clientid`,',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') WHERE `contacts`.`category`='".$_POST['search_contact_type']."')";
    }
    if(!empty($_POST['search_ticket_type'])) {
        $filter_query .= " AND `tickets`.`ticket_type` = '".$_POST['search_ticket_type']."'";
    }
    if(!empty($_POST['search_start_date']) && !empty($_POST['search_end_date'])) {
        $filter_query .= " AND ('".$_POST['search_start_date']."' BETWEEN `tickets`.`to_do_date` AND IFNULL(NULLIF(`tickets`.`to_do_end_date`,''),`tickets`.`to_do_date`) OR '".$_POST['search_end_date']."' BETWEEN `tickets`.`to_do_date` AND IFNULL(NULLIF(`tickets`.`to_do_end_date`,''),`tickets`.`to_do_date`) OR `tickets`.`to_do_date` BETWEEN '".$_POST['search_start_date']."' AND '".$_POST['search_end_date']."' OR IFNULL(NULLIF(`tickets`.`to_do_end_date`,''),`tickets`.`to_do_date`) BETWEEN '".$_POST['search_start_date']."' AND '".$_POST['search_end_date']."' OR (SELECT COUNT(`id`) FROM `ticket_schedule` WHERE `tickets`.`ticketid` = `ticket_schedule`.`ticketid` AND `deleted` = 0 AND ('".$_POST['search_start_date']."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(NULLIF(`ticket_schedule`.`to_do_end_date`,''),`ticket_schedule`.`to_do_date`) OR '".$_POST['search_end_date']."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(NULLIF(`ticket_schedule`.`to_do_end_date`,''),`ticket_schedule`.`to_do_date`) OR `ticket_schedule`.`to_do_date` BETWEEN '".$_POST['search_start_date']."' AND '".$_POST['search_end_date']."' OR IFNULL(NULLIF(`ticket_schedule`.`to_do_end_date`,''),`ticket_schedule`.`to_do_date`) BETWEEN '".$_POST['search_start_date']."' AND '".$_POST['search_end_date']."')) > 0)";
    } else if(!empty($_POST['search_start_date'])) {
        $filter_query .= " AND ('".$_POST['search_start_date']."' BETWEEN `tickets`.`to_do_date` AND IFNULL(NULLIF(`tickets`.`to_do_end_date`,''),`tickets`.`to_do_date`) OR `tickets`.`to_do_date` >= '".$_POST['search_start_date']."' OR (SELECT COUNT(`id`) FROM `ticket_schedule` WHERE `tickets`.`ticketid` = `ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted` = 0 AND ('".$_POST['search_start_date']."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(NULLIF(`ticket_schedule`.`to_do_end_date`,''),`ticket_schedule`.`to_do_date`) OR `ticket_schedule`.`to_do_date` >= '".$_POST['search_start_date']."')) > 0)";
    } else if(!empty($_POST['search_end_date'])) {
        $filter_query .= " AND ('".$_POST['search_end_date']."' BETWEEN `tickets`.`to_do_date` AND IFNULL(NULLIF(`tickets`.`to_do_end_date`,''),`tickets`.`to_do_date`) OR IFNULL(NULLIF(`tickets`.`to_do_end_date`,''),`tickets`.`to_do_date`) <= '".$_POST['search_end_date']."' OR (SELECT COUNT(`id`) FROM `ticket_schedule` WHERE `tickets`.`ticketid` = `ticket_schedule`.`ticketid` AND `ticket_schedule`.`deleted` = 0 AND ('".$_POST['search_end_date']."' BETWEEN `ticket_schedule`.`to_do_date` AND IFNULL(NULLIF(`ticket_schedule`.`to_do_end_date`,''),`ticket_schedule`.`to_do_date`) OR IFNULL(NULLIF(`ticket_schedule`.`to_do_end_date`,''),`ticket_schedule`.`to_do_date`) <= '".$_POST['search_end_date']."')) > 0)";
    }
    ?>

    <?php if(!empty(array_filter($ticket_accounting_fields))) { ?>
        <script type="text/javascript">
        $(document).ready(function() {
            filterRegLoc();
        });
        $(document).on('change', 'select[name="search_region"],select[name="search_classification"]', function() { filterRegLoc(); });
        function filterRegLoc() {
            var region = $('[name="search_region"]').val();
            var classification = $('[name="search_classification"]').val();

            var filter_query = '';
            if(region != undefined && region != '') {
                $('[name="search_classification"] option[data-regions]').hide();
                $('[name="search_classification"] option[data-regions]').each(function() {
                    var class_regions = $(this).data('regions');
                    if(class_regions.length == 0) {
                        class_regions = [""];
                    }
                    if(class_regions.indexOf(region) != -1) {
                        $(this).show();
                    }
                });
                filter_query += '[data-region="'+region+'"]';
            } else {
                $('[name="search_classification"] option').show();
            }
            if(classification != undefined && classification != '') {
                filter_query += '[data-classification"'+classification+'"]';
            }
            if(filter_query != '') {
                $('[name="search_business"] option').hide();
                $('[name="search_business"] option'+filter_query).show();
                $('[name="search_all_contact"] option').hide();
                $('[name="search_all_contact"] option'+filter_query).show();
                $('[name="search_contact"] option').hide();
                $('[name="search_contact"] option'+filter_query).show();
            } else {
                $('[name="search_business"] option').show();
                $('[name="search_all_contact"] option').show();
                $('[name="search_contact"] option').show();
            }
            $('[name="search_classification"]').trigger('change.select2');
            $('[name="search_business"]').trigger('change.select2');
            $('[name="search_all_contact"]').trigger('change.select2');
            $('[name="search_contact"]').trigger('change.select2');
        }
        </script>
        <form name="search_accounting" method="post" action="" class="form-horizontal" role="form">
            <?php if(in_array('Region',$ticket_accounting_fields)) { ?>
                <div class="gap-bottom col-sm-6 col-xs-12">
                    <div class="col-sm-4">
                        <label class="control-label">Region:</label>
                    </div>
                    <div class="col-sm-8">
                        <select name="search_region" data-placeholder="Select a Region" class="chosen-select-deselect">
                            <option></option>
                            <?php $region_list = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
                            foreach ($region_list as $con_region) {
                                if(in_array($con_region, $allowed_regions)) {
                                    echo "<option ".($_POST['search_region'] == $con_region ? 'selected' : '')." value='$con_region'>$con_region</option>";
                                }
                            } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('Classification',$ticket_accounting_fields)) { ?>
                <div class="gap-bottom col-sm-6 col-xs-12">
                    <div class="col-sm-4">
                        <label class="control-label">Classification:</label>
                    </div>
                    <div class="col-sm-8">
                        <select name="search_classification" data-placeholder="Select a Classification" class="chosen-select-deselect">
                            <option></option>
                            <?php $class_regions = explode(',',get_config($dbc, '%_class_regions', true, ','));
                            $contact_classifications = [];
                            $classification_regions = [];
                            foreach(explode(',',get_config($dbc, '%_classification', true, ',')) as $i => $contact_classification) {
                                $row = array_search($contact_classification, $contact_classifications);
                                if($class_regions[$i] == 'ALL') {
                                    $class_regions[$i] = '';
                                }
                                if($row !== FALSE && $class_regions[$i] != '') {
                                    $classification_regions[$row][] = $class_regions[$i];
                                } else {
                                    $contact_classifications[] = $contact_classification;
                                    $classification_regions[] = array_filter([$class_regions[$i]]);
                                }
                            }
                            foreach ($contact_classifications as $i => $con_classification) {
                                $hidden_classification = '';
                                if(!empty($_POST['search_region']) && !in_array($_POST['search_region'], $classification_regions[$i]) && !empty($classification_regions[$i])) {
                                    $hidden_classification = 'style="display:none;"';
                                }
                                echo "<option ".($_POST['search_classification'] == $con_classification ? 'selected' : '')." data-regions='".json_encode($classification_regions[$i])."' value='$con_classification' $hidden_classification>$con_classification</option>";
                            } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('Business',$ticket_accounting_fields)) { ?>
                <div class="gap-bottom col-sm-6 col-xs-12">
                    <div class="col-sm-4">
                        <label class="control-label"><?= BUSINESS_CAT ?>:</label>
                    </div>
                    <div class="col-sm-8">
                        <select name="search_business" data-placeholder="Select a <?= BUSINESS_CAT ?>" class="chosen-select-deselect">
                            <option></option>
                            <?php $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contacts`.`contactid`, `contacts`.`name`, `contacts`.`region`, `contacts`.`con_locations`, `contacts`.`classification` FROM `contacts` LEFT JOIN `tickets` ON `contacts`.`contactid`=`tickets`.`businessid` WHERE `contacts`.`category`='".BUSINESS_CAT."' AND `contacts`.`status` > 0 AND `contacts`.`deleted`=0 AND `tickets`.`deleted`=0"));
                            foreach($contact_list as $row) { ?>
                                <option data-region="<?= $row['region'] ?>" data-location="<?= $row['con_locations'] ?>" data-classification="<?= $row['classification'] ?>" <?= $row['contactid'] == $_POST['search_business'] ? 'selected' : '' ?> value="<?= $row['contactid'] ?>"><?= $row['full_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('All Contact',$ticket_accounting_fields)) { ?>
                <div class="gap-bottom col-sm-6 col-xs-12">
                    <div class="col-sm-4">
                        <label class="control-label"><?= CONTACTS_TILE ?>:</label>
                    </div>
                    <div class="col-sm-8">
                        <select name="search_all_contact" data-placeholder="Select <?= CONTACTS_NOUN ?>" class="chosen-select-deselect">
                            <option></option>
                            <?php $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`name`, `contacts`.`region`, `contacts`.`con_locations`, `contacts`.`classification` FROM `contacts` LEFT JOIN `tickets` ON `tickets`.`businessid`=`contacts`.`contactid` OR CONCAT(',',`tickets`.`clientid`,',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') WHERE `contacts`.`status` > 0 AND `contacts`.`deleted`=0 AND `tickets`.`deleted`=0"));
                            foreach($contact_list as $row) { ?>
                                <option data-region="<?= $row['region'] ?>" data-location="<?= $row['con_locations'] ?>" data-classification="<?= $row['classification'] ?>" <?= $row['contactid'] == $_POST['search_all_contact'] ? 'selected' : '' ?> value="<?= $row['contactid'] ?>"><?= $row['full_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('Contact',$ticket_accounting_fields)) { ?>
                <div class="gap-bottom col-sm-6 col-xs-12">
                    <div class="col-sm-4">
                        <label class="control-label"><?= CONTACTS_TILE ?>:</label>
                    </div>
                    <div class="col-sm-8">
                        <select name="search_contact" data-placeholder="Select <?= CONTACTS_NOUN ?>" class="chosen-select-deselect">
                            <option></option>
                            <?php $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contacts`.`contactid`, `contacts`.`first_name`, `contacts`.`last_name`, `contacts`.`name`, `contacts`.`region`, `contacts`.`con_locations`, `contacts`.`classification` FROM `contacts` LEFT JOIN `tickets` ON CONCAT(',',`tickets`.`clientid`,',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') WHERE `contacts`.`status` > 0 AND `contacts`.`deleted`=0 AND `tickets`.`deleted`=0"));
                            foreach($contact_list as $row) { ?>
                                <option data-region="<?= $row['region'] ?>" data-location="<?= $row['con_locations'] ?>" data-classification="<?= $row['classification'] ?>" <?= $row['contactid'] == $_POST['search_contact'] ? 'selected' : '' ?> value="<?= $row['contactid'] ?>"><?= $row['full_name'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('Ticket Contact Type',$ticket_accounting_fields)) { ?>
                <div class="gap-bottom col-sm-6 col-xs-12">
                    <div class="col-sm-4">
                        <label class="control-label"><?= CONTACTS_NOUN ?> Type:</label>
                    </div>
                    <div class="col-sm-8">
                        <select name="search_contact_type" data-placeholder="Select <?= CONTACTS_NOUN ?> Type" class="chosen-select-deselect">
                            <option></option>
                            <?php foreach(explode(',',get_config($dbc, 'contacts_tabs')) as $row) { ?>
                                <option <?= $row == $_POST['search_contact_type'] ? 'selected' : '' ?> value="<?= $row ?>"><?= $row ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('Ticket Type',$ticket_accounting_fields)) { ?>
                <div class="gap-bottom col-sm-6 col-xs-12">
                    <div class="col-sm-4">
                        <label class="control-label"><?= TICKET_NOUN ?> Type:</label>
                    </div>
                    <div class="col-sm-8">
                        <select name="search_ticket_type" data-placeholder="Select a <?= TICKET_NOUN ?> Type" class="chosen-select-deselect">
                            <option></option>
                            <?php foreach($ticket_tabs as $type_key => $type_label) { ?>
                                <option <?= $type_key == $_POST['search_ticket_type'] ? 'selected' : '' ?> value="<?= $type_key ?>"><?= $type_label ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('Dates',$ticket_accounting_fields)) { ?>
                <div class="gap-bottom col-sm-6 col-xs-12">
                    <div class="col-sm-4">
                        <label class="control-label">Start Date:</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" name="search_start_date" class="form-control datepicker" value="<?= $_POST['search_start_date'] ?>">
                    </div>
                </div>
                <div class="gap-bottom col-sm-6 col-xs-12">
                    <div class="col-sm-4">
                        <label class="control-label">End Date:</label>
                    </div>
                    <div class="col-sm-8">
                        <input type="text" name="search_end_date" class="form-control datepicker" value="<?= $_POST['search_end_date'] ?>">
                    </div>
                </div>
            <?php } ?>
            <div class="col-xs-12 text-right">
                <button type="submit" name="search_accounting" value="Search" class="btn brand-btn mobile-block">Search</button>
                <button type="submit" name="display_all_accounting" value="Display All" class="btn brand-btn mobile-block">Display All</button>
            </div>
            <div class="clearfix"></div>
        </form>
    <?php } ?>

	<h4>Create Invoices</h4>
	<script>
	function create_invoice(id) {
		$.post('../Ticket/ticket_ajax_all.php?action=ticket_invoice',{ticketid:id}, function(response) {
			window.location = response;
		});
	}
	function multi_invoice() {
		var id_list = [];
		$('.invoice_ticket:checked').each(function() {
			id_list.push(this.value);
		});
		create_invoice(id_list.join(','));
	}
	function revert_to_admin(link) {
		$.post('../Ticket/ticket_ajax_all.php?action=revert_to_admin', {ticketid: $(link).closest('td').data('id')});
		$(link).closest('tr').hide();
	}
	</script>
	<?php $invoice_list = $dbc->query("SELECT `tickets`.*, `tickets`.`to_do_date` `ticket_date` FROM `tickets` LEFT JOIN `invoice` ON CONCAT(',',`invoice`.`ticketid`,',') LIKE CONCAT('%,',`tickets`.`ticketid`,',%') WHERE `tickets`.`ticket_type` IN ('".implode("','",$ticket_conf_list)."') AND `invoice`.`invoiceid` IS NULL AND `tickets`.`deleted`=0 ".(in_array('Administration',$db_config) ?"AND IFNULL(`approvals`,'') != ''" : '').$filter_query);
	if($invoice_list->num_rows > 0) { ?>
		<button class="btn brand-btn pull-right" onclick="multi_invoice(); return false;">Create Invoice for Selected</button>
		<table class="table table-bordered">
			<tr>
				<th>Date</th>
				<th><?= TICKET_NOUN ?></th>
				<?php if(strpos($value_config, ',Services,') !== FALSE) { ?>
					<th>Services</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Sub Totals per Service,') !== FALSE) { ?>
					<th>Sub Totals per Service</th>
				<?php } ?>
                <?php if(strpos($value_config, ',Additional KM Charge,') !== FALSE) { ?>
                    <th>Additional KM Charge</th>
                <?php } ?>
				<?php if(strpos($value_config, ',Staff Tasks,') !== FALSE) { ?>
					<th>Staff</th>
					<th><?= TASK_TILE ?></th>
					<th>Hours</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Inventory,') !== FALSE) { ?>
					<th>Inventory</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Materials,') !== FALSE) { ?>
					<th>Materials</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Misc Item,') !== FALSE) { ?>
					<th>Miscellaneous</th>
				<?php } ?>
				<th>Total</th>
				<th>Notes</th>
				<?php if(strpos($value_config, ',Extra Billing,') !== FALSE) { ?>
					<th>Extra Billing</th>
				<?php } ?>
				<th style="width: 20em;">Invoice</th>
			</tr>
			<?php while($invoice = $invoice_list->fetch_assoc()) {
				$active = 0;
				$total_cost = 0.00;
				$services_cost_num = [];
				$services_cost = [];
				$services = [];
				$qty = explode(',',$invoice['service_qty']);
                $cust_rate_card = $dbc->query("SELECT * FROM `rate_card` WHERE `clientid`='".$invoice['businessid']."' AND `deleted`=0 AND `on_off`=1")->fetch_assoc();
				foreach(explode(',',$invoice['serviceid']) as $i => $service) {
					if($service > 0) {
						$service = $dbc->query("SELECT `services`.`serviceid`, `services`.`heading`, `rate`.`cust_price` FROM `services` LEFT JOIN `company_rate_card` `rate` ON `services`.`serviceid`=`rate`.`item_id` AND `rate`.`tile_name` LIKE 'Services' WHERE `services`.`serviceid`='$service' AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) AND `cust_price` > 0")->fetch_assoc();
                        $service_rate = 0;
                        foreach(explode('**',$cust_rate_card['services']) as $service_cust_rate) {
                            $service_cust_rate = explode('#',$service_cust_rate);
                            if($service_cust_rate[0] == $service['serviceid']) {
                                $service_rate = $service_cust_rate[1];
                            }
                        }
						$services[] = $service['heading'].($qty[$i] > 0 ? ' x '.$qty[$i] : '');
						$services_cost_num[] = ($qty[$i] > 0 ? $qty[$i] : 1) * ($service_rate > 0 ? $service_rate : $service['cust_price']);
						$services_cost[] = number_format(($qty[$i] > 0 ? $qty[$i] : 1) * ($service_rate > 0 ? $service_rate : $service['cust_price']),2);
					}
				}
                $status_list = [];
                $date_list = [];
                $sql = "SELECT * FROM `ticket_schedule` WHERE `ticketid` = '{$invoice['ticketid']}' AND `deleted` = 0 AND `type` NOT IN ('origin','destination')";
                if($project_admin_multiday_tickets == 1) {
                    $sql .= " AND `to_do_date` = '{$invoice['to_do_date']}'";
                }
                $query = mysqli_query($dbc, $sql);
                while($sched_line = $query->fetch_assoc()) {
                    $status_list[] = (empty($sched_line['client_name']) ? $sched_line['location_name'] : $sched_line['client_name']).': '.$sched_line['status'];
                    $date_list[] = $sched_line['to_do_date'];
                    foreach(explode(',',$sched_line['serviceid']) as $i => $service) {
                        if($service > 0) {
                            $service = $dbc->query("SELECT `services`.`serviceid`, `services`.`heading`, `rate`.`cust_price` FROM `services` LEFT JOIN `company_rate_card` `rate` ON `services`.`serviceid`=`rate`.`item_id` AND `rate`.`tile_name` LIKE 'Services' WHERE `services`.`serviceid`='$service' AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) AND `cust_price` > 0")->fetch_assoc();
                            $service_rate = 0;
                            foreach(explode('**',$cust_rate_card['services']) as $service_cust_rate) {
                                $service_cust_rate = explode('#',$service_cust_rate);
                                if($service_cust_rate[0] == $service['serviceid']) {
                                    $service_rate = $service_cust_rate[1];
                                }
                            }
                            $services[] = (empty($sched_line['client_name']) ? $sched_line['location_name'] : $sched_line['client_name']).': '.$service['heading'].($qty[$i] > 0 ? ' x '.$qty[$i] : '');
                            $services_cost_num[] = ($qty[$i] > 0 ? $qty[$i] : 1) * ($service_rate > 0 ? $service_rate : $service['cust_price']);
                            $services_cost[] = number_format(($qty[$i] > 0 ? $qty[$i] : 1) * ($service_rate > 0 ? $service_rate : $service['cust_price']),2);
                        }
                    }
                }
				$pdf_name = '../Invoice/Download/invoice_'.$invoice['invoiceid'].'.pdf'; ?>
				<tr>
					<td data-title="Date"><?= empty($invoice['to_do_date']) ? implode(', ',array_unique($date_list)) : $invoice['to_do_date'] ?></td>
					<td data-title="<?= TICKET_NOUN ?>"><?php if($tile_security['edit'] > 0) { ?><a href="<?= WEBSITE_URL ?>/Ticket/index.php?edit=<?= $invoice['ticketid'] ?><?= (empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']) ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true','auto',true,true); return false;"><?= get_ticket_label($dbc, $invoice) ?></a><?php } else { echo get_ticket_label($dbc, $invoice); } ?></td>
					<?php if(strpos($value_config, ',Services,') !== FALSE) {
						foreach($services_cost_num as $cost_amt) {
							$total_cost += $cost_amt;
						} ?>
						<td data-title="Services"><?= implode('<br />',$services) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Sub Totals per Service,') !== FALSE) { ?>
						<td data-title="Sub Totals per Service"><?= implode('<br />',$services_cost) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Additional KM Charge,') !== FALSE) {
                        $travel_km = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`hours_travel`) `travel_km` FROM `ticket_attached` WHERE `ticketid` = '".$invoice['ticketid']."' AND `deleted` = 0"))['travel_km'];
                        $total_travel_km = 0;
						foreach($services_cost_num as $cost_amt) {
                            $total_travel_km += ($travel_km * $cost_amt);
                        }
                        $total_cost += $total_travel_km; ?>
						<td data-title="Additional KM Charge"><?= number_format($total_travel_km,2) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Staff Tasks,') !== FALSE) {
						$staff_tasks_staff = [];
						$staff_tasks_task = [];
						$staff_tasks_hours = [];
						$sql = "SELECT * FROM `ticket_attached` WHERE `ticketid` = '{$invoice['ticketid']}' AND `deleted` = 0 AND `src_table` = 'Staff_Tasks'";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `date_stamp` = '{$invoice['ticket_date']}'";
						}
						$query = mysqli_query($dbc, $sql);
						while($row = mysqli_fetch_assoc($query)) {
							$staff_tasks_staff[] = get_contact($dbc, $row['item_id']);
							$staff_tasks_task[] = $row['position'];
							$staff_tasks_hours[] = number_format($row['hours_tracked'],2);
						} ?>
						<td data-title="Staff"><?= implode("<br />", $staff_tasks_staff) ?></td>
						<td data-title="Task"><?= implode("<br />", $staff_tasks_task) ?></td>
						<td data-title="Hours"><?= implode("<br />", $staff_tasks_hours) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Inventory,') !== FALSE) {
						$inventory = [];
						$sql = "SELECT * FROM `ticket_attached` WHERE `ticketid` = '{$invoice['ticketid']}' AND `deleted` = 0 AND `src_table` = 'inventory'";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `date_stamp` = '{$invoice['ticket_date']}'";
						}
						$query = mysqli_query($dbc, $sql);
						while($row = mysqli_fetch_assoc($query)) {
							if($row['description'] != '') {
								$inventory[] = $row['description'].': '.round($row['qty'],3).' @ $'.number_format($row['rate'],2).': $'.number_format($row['qty'] * $row['rate'],2);
								$total_cost += $row['qty'] * $row['rate'];
							} else {
								$inv_row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `product_name`, `name`, `final_retail_price` FROM `inventory` WHERE `inventoryid` = '{$row['item_id']}'"));
								$inventory[] = (empty($inv_row['product_name']) ? $inv_row['name'] : $inv_row['product_name']).': '.round($row['qty'],3).' @ $'.number_format($row['rate'] > 0 ? $row['rate'] : $inv_row['final_retail_price'],2).': $'.number_format($row['qty'] * $inv_row['final_retail_price'],2);
								$total_cost += $row['qty'] * ($row['rate'] > 0 ? $row['rate'] : $inv_row['final_retail_price']);
							}
						} ?>
						<td data-title="Inventory"><?= implode("<br />", $inventory) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Materials,') !== FALSE) {
						$materials = [];
						$sql = "SELECT * FROM `ticket_attached` WHERE `ticketid` = '{$invoice['ticketid']}' AND `deleted` = 0 AND `src_table` = 'material'";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `date_stamp` = '{$invoice['ticket_date']}'";
						}
						$query = mysqli_query($dbc, $sql);
						while($row = mysqli_fetch_assoc($query)) {
							if($row['description'] != '') {
								$materials[] = $row['description'].': '.round($row['qty'],3);
							} else {
								$materials[] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `material` WHERE `materialid` = '{$row['item_id']}'"))['name'].': '.round($row['qty'],3);
							}
						} ?>
						<td data-title="Materials"><?= implode("<br />", $materials) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Misc Item,') !== FALSE) {
						$misc = [];
						$sql = "SELECT * FROM `ticket_attached` WHERE `ticketid` = '{$invoice['ticketid']}' AND `deleted` = 0 AND `src_table` = 'misc_item'";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `date_stamp` = '{$invoice['ticket_date']}'";
						}
						$query = mysqli_query($dbc, $sql);
						while($row = mysqli_fetch_assoc($query)) {
							$misc[] = $row['description'].': '.round($row['qty'],3).' @ $'.number_format($row['rate'],2).': $'.number_format($row['qty'] * $row['rate'],2);
							$total_cost += $row['qty'] * $row['rate'];
						} ?>
						<td data-title="Miscellaneous"><?= implode("<br />", $misc) ?></td>
					<?php } ?>
					<td data-title="Total">$<?= number_format($total_cost,2); ?></td>
					<td data-title="Notes"><?php $notes = $dbc->query("SELECT * FROM `ticket_comment` WHERE `ticketid`='{$invoice['ticketid']}' AND `type`='administration_note' AND `deleted`=0") ?></td>
					<?php if(strpos($value_config, ',Extra Billing,') !== FALSE) {
						$sql = "SELECT COUNT(*) `num` FROM `ticket_comment` WHERE `ticketid` = '{$invoice['ticketid']}' AND '{$invoice['ticketid']}' > 0 AND `type` = 'service_extra_billing' AND `deleted` = 0";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `created_date` = '{$invoice['ticket_date']}'";
						}
						$extra_billing = mysqli_fetch_assoc(mysqli_query($dbc, $sql)); ?>
						<td data-title="Extra Billing"><?= $extra_billing['num'] > 0 ? '<img class="inline-img small no-toggle" title="Extra Billing" src="../img/icons/ROOK-status-paid.png">' : '' ?></td>
					<?php } ?>
					<td data-title="Invoice" data-id="<?= $invoice['ticketid'] ?>"><a href="" onclick="create_invoice($(this).closest('td').data('id')); return false;">Create</a> <label class="form-checkbox any-width"><input type="checkbox" class="invoice_ticket" name="ticketid" value="<?= $invoice['ticketid'] ?>">Select</label><?= in_array('Administration',$db_config) ? ' <a href="" onclick="revert_to_admin(this); return false;">Back to Admin</a>' : '' ?></td>
				</tr>
			<?php } ?>
		</table>
		<button class="btn brand-btn pull-right" onclick="multi_invoice(); return false;">Create Invoice for Selected</button>
	<?php } else {
		echo '<h3>No Unbilled '.TICKET_TILE.' Found</h3>';
	} ?>
<?php } else { ?>
	<?php $invoice_list = $dbc->query("SELECT `tickets`.*, `tickets`.`to_do_date` `ticket_date`, `invoice`.`invoiceid`, `invoice`.`invoice_date`, `invoice`.`status` `inv_status`, `invoice`.`final_price` FROM `invoice` LEFT JOIN `tickets` ON CONCAT(',',`invoice`.`ticketid`,',') LIKE CONCAT('%,',`tickets`.`ticketid`,',%') WHERE `invoice`.`deleted`=0 AND `tickets`.`deleted`=0 ORDER BY `invoiceid` DESC LIMIT 0,25");
	if($invoice_list->num_rows > 0) { ?>
		<h3>Top 25 <?= TICKET_NOUN ?> Invoices</h3>
		<h4>To see more Invoices, go to the <?= tile_visible($dbc, 'posadvanced') ? '<a href="../POSAdvanced/invoice_main.php">'.POS_ADVANCE_TILE.'</a>' : (tile_visible($dbc, 'check_out') ? '<a href="../Invoice/invoice_main.php">Check Out</a>' : 'Point of Sale') ?> tile.</h4>
		<table class="table table-bordered">
			<tr>
				<th>Date</th>
				<th><?= TICKET_NOUN ?></th>
				<?php if(strpos($value_config, ',Services,') !== FALSE) { ?>
					<th>Services</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Sub Totals per Service,') !== FALSE) { ?>
					<th>Sub Totals per Service</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Additional KM Charge,') !== FALSE) { ?>
					<th>Additional KM Charge</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Staff Tasks,') !== FALSE) { ?>
					<th>Staff</th>
					<th><?= TASK_TILE ?></th>
					<th>Hours</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Inventory,') !== FALSE) { ?>
					<th>Inventory</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Materials,') !== FALSE) { ?>
					<th>Materials</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Misc Item,') !== FALSE) { ?>
					<th>Miscellaneous</th>
				<?php } ?>
				<th>Total</th>
				<th>Notes</th>
				<?php if(strpos($value_config, ',Extra Billing,') !== FALSE) { ?>
					<th>Extra Billing</th>
				<?php } ?>
				<th>Invoice #</th>
				<th>Status</th>
				<th>Total Price</th>
				<th>Invoice</th>
			</tr>
			<?php while($invoice = $invoice_list->fetch_assoc()) {
				$active = 0;
				$total_cost = 0.00;
				$services_cost_num = [];
				$services_cost = [];
				$services = [];
				$qty = explode(',',$invoice['service_qty']);
                $cust_rate_card = $dbc->query("SELECT * FROM `rate_card` WHERE `clientid`='".$invoice['businessid']."' AND `deleted`=0 AND `on_off`=1")->fetch_assoc();
				foreach(explode(',',$invoice['serviceid']) as $i => $service) {
					if($service > 0) {
						$service = $dbc->query("SELECT `services`.`serviceid`, `services`.`heading`, `rate`.`cust_price` FROM `services` LEFT JOIN `company_rate_card` `rate` ON `services`.`serviceid`=`rate`.`item_id` AND `rate`.`tile_name` LIKE 'Services' WHERE `services`.`serviceid`='$service' AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) AND `cust_price` > 0")->fetch_assoc();
                        $service_rate = 0;
                        foreach(explode('**',$cust_rate_card['services']) as $service_cust_rate) {
                            $service_cust_rate = explode('#',$service_cust_rate);
                            if($service_cust_rate[0] == $service['serviceid']) {
                                $service_rate = $service_cust_rate[1];
                            }
                        }
						$services[] = $service['heading'].($qty[$i] > 0 ? ' x '.$qty[$i] : '');
						$services_cost_num[] = ($qty[$i] > 0 ? $qty[$i] : 1) * ($service_rate > 0 ? $service_rate : $service['cust_price']);
						$services_cost[] = number_format(($qty[$i] > 0 ? $qty[$i] : 1) * ($service_rate > 0 ? $service_rate : $service['cust_price']),2);
					}
				}
				$pdf_name = '../Invoice/Download/invoice_'.$invoice['invoiceid'].'.pdf'; ?>
				<tr>
					<td data-title="Date"><?= $invoice['ticket_date'] ?></td>
					<td data-title="<?= TICKET_NOUN ?>"><?php if($tile_security['edit'] > 0) { ?><a href="<?= WEBSITE_URL ?>/Ticket/index.php?edit=<?= $invoice['ticketid'] ?><?= (empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']) ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true','auto',true,true); return false;"><?= get_ticket_label($dbc, $invoice) ?></a><?php } else { echo get_ticket_label($dbc, $invoice); } ?></td>
					<?php if(strpos($value_config, ',Services,') !== FALSE) {
						foreach($services_cost_num as $cost_amt) {
							$total_cost += $cost_amt;
						} ?>
						<td data-title="Services"><?= implode('<br />',$services) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Sub Totals per Service,') !== FALSE) { ?>
						<td data-title="Sub Totals per Service"><?= implode('<br />',$services_cost) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Additional KM Charge,') !== FALSE) {
                        $travel_km = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`hours_travel`) `travel_km` FROM `ticket_attached` WHERE `ticketid` = '".$invoice['ticketid']."' AND `deleted` = 0"))['travel_km'];
                        $total_travel_km = 0;
						foreach($services_cost_num as $cost_amt) {
                            $total_travel_km += ($travel_km * $cost_amt);
                        }
                        $total_cost += $total_travel_km; ?>
						<td data-title="Additional KM Charge"><?= number_format($total_travel_km,2) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Staff Tasks,') !== FALSE) {
						$staff_tasks_staff = [];
						$staff_tasks_task = [];
						$staff_tasks_hours = [];
						$sql = "SELECT * FROM `ticket_attached` WHERE `ticketid` = '{$invoice['ticketid']}' AND `deleted` = 0 AND `src_table` = 'Staff_Tasks'";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `date_stamp` = '{$invoice['ticket_date']}'";
						}
						$query = mysqli_query($dbc, $sql);
						while($row = mysqli_fetch_assoc($query)) {
							$staff_tasks_staff[] = get_contact($dbc, $row['item_id']);
							$staff_tasks_task[] = $row['position'];
							$staff_tasks_hours[] = number_format($row['hours_tracked'],2);
						} ?>
						<td data-title="Staff"><?= implode("<br />", $staff_tasks_staff) ?></td>
						<td data-title="Task"><?= implode("<br />", $staff_tasks_task) ?></td>
						<td data-title="Hours"><?= implode("<br />", $staff_tasks_hours) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Inventory,') !== FALSE) {
						$inventory = [];
						$sql = "SELECT * FROM `ticket_attached` WHERE `ticketid` = '{$invoice['ticketid']}' AND `deleted` = 0 AND `src_table` = 'inventory'";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `date_stamp` = '{$invoice['ticket_date']}'";
						}
						$query = mysqli_query($dbc, $sql);
						while($row = mysqli_fetch_assoc($query)) {
							if($row['description'] != '') {
								$inventory[] = $row['description'].': '.round($row['qty'],3).' @ $'.number_format($row['rate'],2).': $'.number_format($row['qty'] * $row['rate'],2);
								$total_cost += $row['qty'] * $row['rate'];
							} else {
								$inv_row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `product_name`, `name`, `final_retail_price` FROM `inventory` WHERE `inventoryid` = '{$row['item_id']}'"));
								$inventory[] = (empty($inv_row['product_name']) ? $inv_row['name'] : $inv_row['product_name']).': '.round($row['qty'],3).' @ $'.number_format($row['rate'] > 0 ? $row['rate'] : $inv_row['final_retail_price'],2).': $'.number_format($row['qty'] * $inv_row['final_retail_price'],2);
								$total_cost += $row['qty'] * ($row['rate'] > 0 ? $row['rate'] : $inv_row['final_retail_price']);
							}
						} ?>
						<td data-title="Inventory"><?= implode("<br />", $inventory) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Materials,') !== FALSE) {
						$materials = [];
						$sql = "SELECT * FROM `ticket_attached` WHERE `ticketid` = '{$invoice['ticketid']}' AND `deleted` = 0 AND `src_table` = 'material'";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `date_stamp` = '{$invoice['ticket_date']}'";
						}
						$query = mysqli_query($dbc, $sql);
						while($row = mysqli_fetch_assoc($query)) {
							if($row['description'] != '') {
								$materials[] = $row['description'].': '.round($row['qty'],3);
							} else {
								$materials[] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `material` WHERE `materialid` = '{$row['item_id']}'"))['name'].': '.round($row['qty'],3);
							}
						} ?>
						<td data-title="Materials"><?= implode("<br />", $materials) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Misc Item,') !== FALSE) {
						$misc = [];
						$sql = "SELECT * FROM `ticket_attached` WHERE `ticketid` = '{$invoice['ticketid']}' AND `deleted` = 0 AND `src_table` = 'misc_item'";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `date_stamp` = '{$invoice['ticket_date']}'";
						}
						$query = mysqli_query($dbc, $sql);
						while($row = mysqli_fetch_assoc($query)) {
							$misc[] = $row['description'].': '.round($row['qty'],3).' @ $'.number_format($row['rate'],2).': $'.number_format($row['qty'] * $row['rate'],2);
							$total_cost += $row['qty'] * $row['rate'];
						} ?>
						<td data-title="Miscellaneous"><?= implode("<br />", $misc) ?></td>
					<?php } ?>
					<td data-title="Total">$<?= number_format($total_cost,2); ?></td>
					<td data-title="Notes"><?php $notes = $dbc->query("SELECT * FROM `ticket_comment` WHERE `ticketid`='{$invoice['ticketid']}' AND `type`='administration_note' AND `deleted`=0") ?></td>
					<?php if(strpos($value_config, ',Extra Billing,') !== FALSE) {
						$sql = "SELECT COUNT(*) `num` FROM `ticket_comment` WHERE `ticketid` = '{$invoice['ticketid']}' AND '{$invoice['ticketid']}' > 0 AND `type` = 'service_extra_billing' AND `deleted` = 0";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `created_date` = '{$invoice['ticket_date']}'";
						}
						$extra_billing = mysqli_fetch_assoc(mysqli_query($dbc, $sql)); ?>
						<td data-title="Extra Billing"><?= $extra_billing['num'] > 0 ? '<img class="inline-img small no-toggle" title="Extra Billing" src="../img/icons/ROOK-status-paid.png">' : '' ?></td>
					<?php } ?>
					<td data-title="Invoice #">#<?= $invoice['invoiceid'].' '.$invoice['invoice_date'] ?></td>
					<td data-title="Status"><?php switch($invoice['inv_status']) {
                        case 'Completed':
                            echo 'Paid';
                            break;
                        case 'Void':
                            echo 'Voided';
                            break;
                        case 'Saved':
                            echo 'Unbilled';
                            break;
                        case 'Posted':
                        default:
                            echo 'Accounts Receivable';
                            break;
                    } ?></td>
					<td data-title="Total Price"><?= $invoice['final_price'] ?></td>
					<td data-title="Invoice"><?php if(file_exists($pdf_name)) { ?><a href="<?= $pdf_name ?>" target="_blank"><img src="../img/pdf.png" class="inline-img">Invoice #<?= $invoice['invoiceid'] ?></a><?php } ?></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<h3>No Invoices Found</h3>';
	} ?>
<?php } ?>