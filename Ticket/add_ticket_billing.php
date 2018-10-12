<div class="clearfix"></div>
<script type="text/javascript">
$(document).ready(function() {
	if($('[name="services_cost"]').val() == '' || $('[name="services_cost"]').val() == undefined || $('[name="services_cost"]').val() == '0.00') {
		setBilling();
	}
});
function updateTotalTimeEstimate() {
	var total_minutes = 0;
	$('[name="service_time_estimate"]').each(function() {
		var time_estimate = $(this).val();
		var minutes = time_estimate.split(':');
		minutes = (parseInt(minutes[0])*60) + parseInt(minutes[1]);
		total_minutes += minutes;
	});
	var new_hours = parseInt(total_minutes / 60);
	var new_minutes = parseInt(total_minutes % 60);
	new_hours = new_hours.toString().length > 1 ? new_hours : '0'+new_hours.toString();
	new_minutes = new_minutes.toString().length > 1 ? new_minutes : '0'+new_minutes.toString();
	total_time_estimate = new_hours+':'+new_minutes;

	$('[name="total_service_time_estimate"]').val(total_time_estimate);
}
</script>
<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Billing</h3>') : '' ?>
<div id="no-more-tables">
	<?php $view_totals = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_service_total');
	$bill = $_SERVER['DBC']->query("SELECT `businessid`, `agentid`, `clientid`, `services_cost`, `service_cost_manual`, `billing_discount_type`, `billing_discount` FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
	$billing_lines = [];
	$customer_rates = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `services`, `staff`, `staff_position` FROM `rate_card` WHERE `clientid` IN ('$rate_contact', '{$bill['businessid']}', '{$bill['clientid']}') AND `clientid` != '' AND `deleted`=0 ORDER BY `clientid`='$rate_contact' DESC"));
	if(strpos($value_config,',Billing Services,') !== FALSE && (!isset($_GET['tab_only']) || $_GET['tab_only'] == 'services')) {
		$service_rates = explode('**',$customer_rates['services']);
		$services = $_SERVER['DBC']->query("SELECT `serviceid`, `service_qty`, `service_discount`, `service_discount_type`, `service_time_estimate`, `service_fuel_charge` FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
		$service_delivers = [];
		$service_deliver_id = [];
		$service_delivers = [];
		$service_deliver_id = [];
		$service = explode(',',$services['serviceid']);
		$service_surcharge = explode(',',$services['service_fuel_charge']);
        foreach($service as $id) {
            $service_delivers[] = '';
            $service_deliver_id[] = '';
        }
		$qty = explode(',',$services['service_qty']);
		$time_estimate = explode(',',$services['service_time_estimate']);
		$discount_type = explode(',',$services['service_discount_type']);
		$discount = explode(',',$services['service_discount']);
		$total_time_estimate = 0;
        $delivery_services = $_SERVER['DBC']->query("SELECT `location_name`,`client_name`,`serviceid`, `est_time`, `id`, `surcharge`, `service_discount`, `service_discount_type` FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 ORDER BY `sort`");

        $stop_number = 0;
        while($delivery_service = $delivery_services->fetch_assoc()) {
            $stop_number++;
            $delivery_serviceid = explode(',',$delivery_service['serviceid']);
            foreach($delivery_serviceid as $id) {
                $service_delivers[] = 'Delivery #'.$stop_number.': '.(empty($delivery_service['client_name']) ? $delivery_service['location_name'] : $delivery_service['client_name']);

                $service_deliver_id[] = $delivery_service['id'];
                $service[] = $id;
                $service_surcharge[] = explode(',',$delivery_service['surcharge'])[$i];
                $discount[] = explode(',',$delivery_service['service_discount'])[$i];
                $discount_type[] = explode(',',$delivery_service['service_discount_type'])[$i];

                $qty[] = 1;
                $time_estimate[] = $delivery_service['est_tiime'] / count($delivery_serviceid);
                $discount_type[] = '';
                $discount[] = 0;
            }
        }
		foreach($service as $i => $id) {
			if($id > 0) {
				$service = $_SERVER['DBC']->query("SELECT `category`, `service_type`, `heading`, `estimated_hours` FROM `services` WHERE `serviceid`='$id'")->fetch_assoc();
				$rate = 0;
				foreach($service_rates as $rate_line) {
					$rate_line = explode('#',$rate_line);
					if($rate_line[0] == $id) {
						$rate = $rate_line[1];
					}
				}
				if($rate == 0) {
					$rate = $_SERVER['DBC']->query("SELECT `cust_price` FROM `company_rate_card` WHERE `item_id`='$id' AND `tile_name` LIKE 'Services' AND `deleted`=0 AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-99-99') > NOW() ORDER BY `start_date` DESC")->fetch_assoc()['cust_price'];
				}
				$description = (!empty($service_delivers[$i]) ? $service_delivers[$i].': ' : '').($service['category'].(!empty($service['service_type']) ? (!empty($service['category']) ? ' ('.$service['service_type'].')' : $service['service_type']) : '').(!empty($service['category'].$service['service_type']) ? ': ' : '').$service['heading']);
				if(empty($time_estimate[$i])) {
					$estimated_hours = empty($service['estimated_hours']) ? '00:00' : $service['estimated_hours'];
					$minutes = explode(':', $estimated_hours);
					$minutes = ($minutes[0]*60) + $minutes[1];
					$minutes = $qty[$i] * $minutes;
					$new_hours = $minutes / 60;
					$new_minutes = $minutes % 60;
					$new_hours = sprintf('%02d', $new_hours);
					$new_minutes = sprintf('%02d', $new_minutes);
					$time_estimate[$i] = $new_hours.':'.$new_minutes;
				}
                $surcharge = $service_surcharge[$i];
				$minutes = explode(':', $time_estimate[$i]);
				$minutes = ($minutes[0]*60) + $minutes[1];
				$total_time_estimate += $minutes;
                if(!empty($service_delivers[$i])) {
                    $billing_lines[] = ['surcharge_edit'=>'name="surcharge" data-table="ticket_schedule" data-id="'.$service_deliver_id[$i].'" data-id-field="id" data-concat=","', 'discount_edit'=>'name="service_discount" data-table="ticket_schedule" data-id="'.$service_deliver_id[$i].'" data-id-field="id" data-concat=","', 'type_edit'=>'name="service_discount_type" data-table="ticket_schedule" data-id="'.$service_deliver_id[$i].'" data-id-field="id" data-concat=","', 'rate'=>$rate, 'surcharge'=>$surcharge, 'description'=>$description, 'qty'=>(empty($qty[$i]) ? '1' : $qty[$i]), 'discount_type'=>$discount_type[$i], 'discount'=>$discount[$i], 'service_time_estimate'=>$time_estimate[$i], 'serviceid'=>$id];
                } else {
                    $billing_lines[] = ['surcharge_edit'=>'name="service_fuel_charge" data-table="tickets" data-id="'.$ticketid.'" data-id-field="ticketid" data-concat=","', 'discount_edit'=>'name="service_discount" data-table="tickets" data-id="'.$ticketid.'" data-id-field="ticketid" data-concat=","', 'type_edit'=>'name="service_discount_type" data-table="tickets" data-id="'.$ticketid.'" data-id-field="ticketid" data-concat=","', 'rate'=>$rate, 'surcharge'=>$surcharge, 'description'=>$description, 'qty'=>(empty($qty[$i]) ? '1' : $qty[$i]), 'discount_type'=>$discount_type[$i], 'discount'=>$discount[$i], 'service_time_estimate'=>$time_estimate[$i], 'serviceid'=>$id];
                }
			}
		}
		$new_hours = $total_time_estimate / 60;
		$new_minutes = $total_time_estimate % 60;
		$new_hours = sprintf('%02d', $new_hours);
		$new_minutes = sprintf('%02d', $new_minutes);
		$total_time_estimate = $new_hours.':'.$new_minutes;
	}
	if(strpos($value_config,',Billing Staff,') !== FALSE && (!isset($_GET['tab_only']) || $_GET['tab_only'] == 'staff_list')) {
		$staff_rates = explode('**',$customer_rates['staff']);
		$position_rates = explode('**',$customer_rates['staff_position']);
		$staff = mysqli_query($dbc,"SELECT 'ticket_time_list' `table`, 'id' `table_id`, `id`, `created_by`, TIME_TO_SEC(`time_length`) / 3600 `qty`, 0 `regular`, 0 `overtime`, 0 `travel`, `discount`, `discount_type`, LEFT(`created_date`,10) `description`, '' `position` FROM `ticket_time_list` WHERE `ticketid`='$ticketid' AND `time_type`='Manual Time' AND `deleted`=0 UNION
			SELECT 'ticket_attached' `table`, 'id' `table_id`, `id`, `item_id` `created_by`, IF(`hours_set` > 0,`hours_set`,IF(`hours_tracked` > 0,`hours_tracked`,`qty`)) `qty`,  `hours_estimated` `regular`, `hours_ot` `overtime`, `hours_travel` `travel`, `discount`, `discount_type`, LTRIM(CONCAT(`position`,' ',`date_stamp`)) `description`, `position` FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `src_table` LIKE 'Staff%' UNION
			SELECT 'ticket_timer' `table`, 'tickettimerid' `table_id`, `tickettimerid` `id`, `created_by`, TIME_TO_SEC(`timer`) / 3600 ``, 0 `regular`, 0 `overtime`, 0 `travel`, `discount`, `discount_type`, LEFT(`created_date`,10) `description`, '' `position` FROM `ticket_timer` WHERE `ticketid`='$ticketid' AND `deleted` = 0");
		while($staff_time = mysqli_fetch_assoc($staff)) {
			$staff_name = $_SERVER['DBC']->query("SELECT `first_name`, `last_name`, `position` FROM `contacts` WHERE `contactid`='{$staff_time['created_by']}'")->fetch_assoc();
			$rate = ['hourly'=>0];
			$position = $staff_time['position'] ?: $$staff_name['position'];
			$position_id = $_SERVER['DBC']->query("SELECT `position_id` FROM `positions` WHERE `name`='$position' AND `deleted`=0")->fetch_assoc();
			foreach($staff_rates as $rate_line) {
				$rate_line = explode('#',$rate_line);
				if($rate_line[0] == $staff_time['created_by']) {
					$rate['hourly'] = $rate_line[1];
				}
			}
			if($rate['hourly'] == 0) {
				foreach($position_rates as $rate_line) {
					$rate_line = explode('#',$rate_line);
					if($rate_line[0] == $position) {
						$rate['hourly'] = $rate_line[1];
					}
				}
			}
			if($rate['hourly'] == 0) {
				$rate['hourly'] = $_SERVER['DBC']->query("SELECT `hourly` FROM `staff_rate_table` WHERE CONCAT(',',`staff_id`,',') LIKE '%,{$staff_time['created_by']},%' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')")->fetch_assoc()['hourly'];
			}
			if($rate['hourly'] == 0) {
				$rate['hourly'] = $_SERVER['DBC']->query("SELECT `hourly` FROM `position_rate_table` WHERE `position_id`='$position_id' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')")->fetch_assoc()['hourly'];
			}
			if($rate['hourly'] == 0) {
				if($rate_card_name == '') {
					$rate_card_name = '%';
				}
				$rate = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT MAX(IF(`rate_card_types`='Regular',`hourly`,0)) `regular`, MAX(IF(`rate_card_types`='Overtime',`hourly`,0)) `overtime`, MAX(IF(`rate_card_types`='Travel',`hourly`,0)) `travel`, MAX(`hourly`) `hourly`, MAX(`daily`) `daily` FROM `company_rate_card` WHERE DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') AND `rate_card_name` LIKE '$rate_card_name' AND `deleted`=0 AND `description`='$position' HAVING MAX(`hourly`) > 0"));
			}
			if($staff_time['qty'] > 0 && !($staff_time['regular'] > 0)) {
				$billing_lines[] = ['discount_edit'=>'name="discount" data-table="'.$staff_time['table'].'" data-id="'.$staff_time['id'].'" data-id-field="'.$staff_time['table_id'].'"', 'type_edit'=>'name="discount_type" data-table="'.$staff_time['table'].'" data-id="'.$staff_time['id'].'" data-id-field="'.$staff_time['table_id'].'"', 'rate'=>($rate['regular'] > 0 ? $rate['regular'] : $rate['hourly']), 'description'=>trim($staff_time['description'].' '.decryptIt($staff_name['first_name']).' '.decryptIt($staff_name['last_name'])), 'qty'=>round($staff_time['qty'],2), 'discount_type'=>$staff_time['discount'], 'discount'=>$staff_time['discount_type']];
			}
			if($staff_time['regular'] > 0) {
				$billing_lines[] = ['discount_edit'=>'name="discount" data-table="'.$staff_time['table'].'" data-id="'.$staff_time['id'].'" data-id-field="'.$staff_time['table_id'].'"', 'type_edit'=>'name="discount_type" data-table="'.$staff_time['table'].'" data-id="'.$staff_time['id'].'" data-id-field="'.$staff_time['table_id'].'"', 'rate'=>($rate['regular'] > 0 ? $rate['regular'] : $rate['hourly']), 'description'=>trim($staff_time['description'].' '.decryptIt($staff_name['first_name']).' '.decryptIt($staff_name['last_name']).': Regular'), 'qty'=>round($staff_time['regular'],2), 'discount_type'=>$staff_time['discount'], 'discount'=>$staff_time['discount_type']];
			}
			if($staff_time['overtime'] > 0) {
				$billing_lines[] = ['discount_edit'=>'name="discount" data-table="'.$staff_time['table'].'" data-id="'.$staff_time['id'].'" data-id-field="'.$staff_time['table_id'].'"', 'type_edit'=>'name="discount_type" data-table="'.$staff_time['table'].'" data-id="'.$staff_time['id'].'" data-id-field="'.$staff_time['table_id'].'"', 'rate'=>($rate['overtime'] > 0 ? $rate['overtime'] : $rate['hourly'] * 1.5), 'description'=>trim($staff_time['description'].' '.decryptIt($staff_name['first_name']).' '.decryptIt($staff_name['last_name']).': Overtime'), 'qty'=>round($staff_time['overtime'],2), 'discount_type'=>$staff_time['discount'], 'discount'=>$staff_time['discount_type']];
			}
			if($staff_time['travel'] > 0) {
				$billing_lines[] = ['discount_edit'=>'name="discount" data-table="'.$staff_time['table'].'" data-id="'.$staff_time['id'].'" data-id-field="'.$staff_time['table_id'].'"', 'type_edit'=>'name="discount_type" data-table="'.$staff_time['table'].'" data-id="'.$staff_time['id'].'" data-id-field="'.$staff_time['table_id'].'"', 'rate'=>($rate['travel'] > 0 ? $rate['travel'] : ($rate['regular'] > 0 ? $rate['regular'] : $rate['hourly'])), 'description'=>trim($staff_time['description'].' '.decryptIt($staff_name['first_name']).' '.decryptIt($staff_name['last_name']).': Travel'), 'qty'=>round($staff_time['travel'],2), 'discount_type'=>$staff_time['discount'], 'discount'=>$staff_time['discount_type']];
			}
		}
	}
	if(strpos($value_config,',Billing Inventory,') !== FALSE && (!isset($_GET['tab_only']) || $_GET['tab_only'] == 'inventory')) {
		$misc = mysqli_query($dbc,"SELECT 'ticket_attached' `table`, 'id' `table_id`, `id`, `inventory`.`name`, `inventory`.`product_name`, `inventory`.`part_no`, `qty`, `discount`, `discount_type`, IFNULL(NULLIF(`ticket_attached`.`rate`,''),`inventory`.`final_retail_price`) `rate` FROM `ticket_attached` LEFT JOIN `inventory` ON `item_id`=`inventory`.`inventoryid` WHERE `ticketid`='$ticketid' AND `ticket_attached`.`deleted`=0 AND `src_table` LIKE 'inventory'");
		while($item = mysqli_fetch_assoc($misc)) {
			if($item['qty'] > 0) {
				$billing_lines[] = ['discount_edit'=>'name="discount" data-table="'.$item['table'].'" data-id="'.$item['id'].'" data-id-field="'.$item['table_id'].'"', 'type_edit'=>'name="discount_type" data-table="'.$item['table'].'" data-id="'.$item['id'].'" data-id-field="'.$staff_time['table_id'].'"', 'rate'=>$item['rate'], 'description'=>$item['product_name'].' '.$item['name'].' '.$item['part_no'], 'qty'=>$item['qty'], 'discount_type'=>$item['discount'], 'discount'=>$item['discount_type']];
			}
		}
	}
	if(strpos($value_config,',Billing Misc,') !== FALSE && (!isset($_GET['tab_only']) || $_GET['tab_only'] == 'misc_item')) {
		$misc = mysqli_query($dbc,"SELECT 'ticket_attached' `table`, 'id' `table_id`, `id`, `qty`, `discount`, `discount_type`, `description`, `rate` FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `src_table` LIKE 'misc_item'");
		while($item = mysqli_fetch_assoc($misc)) {
			if($item['qty'] > 0) {
				$billing_lines[] = ['discount_edit'=>'name="discount" data-table="'.$item['table'].'" data-id="'.$item['id'].'" data-id-field="'.$item['table_id'].'"', 'type_edit'=>'name="discount_type" data-table="'.$item['table'].'" data-id="'.$item['id'].'" data-id-field="'.$staff_time['table_id'].'"', 'rate'=>$item['rate'], 'description'=>$item['description'], 'qty'=>$item['qty'], 'discount_type'=>$item['discount'], 'discount'=>$item['discount_type']];
			}
		}
	} ?>
	<?php if($generate_pdf) {
		ob_clean();
	} ?>
	<table class="table table-bordered billing">
		<tr class="hidden-sm hidden-xs">
			<th>Description</th>
			<th><?= strpos($value_config,',Service # of Rooms,') !== FALSE ? '# of Rooms' : 'Quantity' ?></th>
			<?php if(strpos($value_config, ',Service Estimated Hours,') !== FALSE) { ?>
				<th>Time Estimate</th>
			<?php } ?>
			<?php if(strpos($value_config, ',Billing Surcharge,') !== FALSE && ($access_any > 0 || $view_totals)) { ?>
				<th>Fuel Service Charge</th>
			<?php } ?>
			<?php if(strpos($value_config, ',Billing Discount,') !== FALSE && ($access_any > 0 || $view_totals)) { ?>
				<th>Discount</th>
			<?php } ?>
			<?php if($access_any > 0 || $view_totals) { ?>
				<th>Total</th>
			<?php } ?>
		</tr>
		<?php foreach($billing_lines as $line) {
			$line_surcharge = 0;
			if($line['surcharge_type'] == '$') {
				$line_surcharge = $line['surcharge'];
			} else {
				$line_surcharge = $line['qty'] * $line['rate'] * $line['surcharge'] / 100;
			}
			$line_discount = 0;
			if($line['discount_type'] == '%') {
				$line_discount = ($line['qty'] * $line['rate'] + $line_surcharge) * $line['discount'] / 100;
			} else {
				$line_discount = $line['discount'];
			} ?>
			<tr>
				<input type="hidden" name="billing_sub" value="<?= $line['qty'] * $line['rate'] ?>">
				<td data-title="Description"><?= $line['description'] ?></td>
				<td data-title="<?= strpos($value_config,',Service # of Rooms,') !== FALSE ? '# of Rooms' : 'Quantity' ?>"><?= $line['qty'] ?></td>
				<?php if(strpos($value_config, ',Service Estimated Hours,') !== FALSE) { ?>
					<td data-title="Time Estimate"><input name="service_time_estimate" <?= $access_any > 0 ? 'data-table="tickets" data-id="'.$ticketid.'" data-id-field="ticketid" data-concat=","' : 'disabled' ?> value="<?= $line['service_time_estimate'] ?>" data-serviceid="<?= $line['serviceid'] ?>" type="text" class="timepicker-5 form-control" onchange="updateTotalTimeEstimate();" /></td>
				<?php } ?>
				<?php if(strpos($value_config, ',Billing Surcharge,') !== FALSE && ($access_any > 0 || $view_totals)) { ?>
					<td data-title="Fuel Service Charge">
						<?php if($access_any > 0 && isset($line['surcharge_edit'])) { ?>
							<div class="col-sm-11">
								<input type="number" step="any" value="<?= $line['surcharge'] ?>" <?= $line['surcharge_edit'] ?> class="form-control surcharge" placeholder="Service Charge">
							</div>
							<div class="col-sm-1">
								<!--<select class="charge" <?= $line['type_edit'] ?> data-placeholder="Charge Type">
									<option <?= $line['surcharge_type'] == '$' ? '' : 'selected' ?> value="%">%</option>
									<option <?= $line['surcharge_type'] == '$' ? 'selected' : '' ?> value="$">$</option>
								</select>-->
                                %
							</div>
						<?php } else if($line['surcharge_type'] == '$') {
							echo '$'.number_format($line_surcharge,2);
						} else {
							echo number_format($line['surcharge'],2).'%';
						} ?>
					</td>
				<?php } ?>
				<?php if(strpos($value_config, ',Billing Discount,') !== FALSE && ($access_any > 0 || $view_totals)) { ?>
					<td data-title="Discount">
						<?php if($access_any > 0) { ?>
							<div class="col-sm-6">
								<input type="number" step="any" value="<?= $line['discount'] ?>" <?= $line['discount_edit'] ?> class="form-control discount" placeholder="Discount">
							</div>
							<div class="col-sm-6">
								<select class="chosen-select-deselect discount_type" <?= $line['type_edit'] ?> data-placeholder="Discount Type"><option />
									<option <?= $line['discount_type'] == '%' ? '' : 'selected' ?> value="$">$ Discount</option>
									<option <?= $line['discount_type'] == '%' ? 'selected' : '' ?> value="%">% Discount</option>
								</select>
							</div>
						<?php } else if($line['discount_type'] == '%') {
							echo number_format($line['discount'],2).'%';
						} else {
							echo '$'.number_format($line_discount,2);
						} ?>
					</td>
				<?php } ?>
				<?php if($access_any > 0 || $view_totals) { ?>
					<td data-title="Total">$<?= number_format($line['qty'] * $line['rate'] + $line_surcharge - $line_discount,2) ?></td>
				<?php } ?>
			</tr>
		<?php } ?>
	</table>
	<?php if($generate_pdf) {
		$pdf_contents[] = ['', ob_get_contents()];
	} ?>
	<?php if($access_any > 0 && !isset($_GET['tab_only'])) {
		$total_discount = 0;
		if(strpos($value_config, ',Billing Total Discount,') !== FALSE) {
			if($_GET['from_type'] == 'customer_rate_services' && !($ticketid > 0)) {
				$billing_contact = !empty($_GET['bid']) ? $_GET['bid'] : $_GET['clientid'];
				if($billing_contact > 0) {
					$billing_contact = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$billing_contact'"));
					$bill['billing_discount_type'] = $billing_contact['discount_type'];
					$bill['billing_discount'] = $billing_contact['discount_value'];
				}
			} ?>
			<div class="form-group">
				<label class="col-sm-4">Total <?= TICKET_NOUN ?> Discount:</label>
				<div class="col-sm-8">
					<div class="col-sm-6">
						<input type="text" class="form-control" name="billing_discount" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $bill['billing_discount'] ?>">
					</div>
					<div class="col-sm-6">
						<select class="chosen-select-deselect" name="billing_discount_type" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid"><option />
							<option <?= $bill['billing_discount_type'] == '%' ? '' : 'selected' ?> value="$">$ Discount</option>
							<option <?= $bill['billing_discount_type'] == '%' ? 'selected' : '' ?> value="%">% Discount</option>
						</select>
					</div>
				</div>
			</div>
		<?php }
		if(strpos($value_config, ',Billing Total,') !== FALSE) { ?>
			<div class="form-group">
				<label class="col-sm-4">Total:</label>
				<div class="col-sm-8"><div class="col-sm-12">
					<input type="hidden" name="billing_discount_type" value="<?= $bill['billing_discount_type'] ?>">
					<input type="hidden" name="billing_discount" value="<?= $bill['billing_discount'] ?>">
					<input type="text" class="form-control" name="services_cost" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-manual-field="service_cost_manual" data-manual="<?= $bill['service_cost_manual'] ?>" value="<?= $bill['services_cost'] ?>">
				</div></div>
			</div>
		<?php }
	} else if($view_totals && !isset($_GET['tab_only'])) { ?>
		<div class="form-group">
			<label class="col-sm-4">Total:</label>
			<div class="col-sm-8"><div class="col-sm-12">
				<input type="hidden" name="billing_discount_type" value="<?= $bill['billing_discount_type'] ?>">
				<input type="hidden" name="billing_discount" value="<?= $bill['billing_discount'] ?>">
				<input type="text" disabled class="form-control" name="services_cost" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-manual-field="service_cost_manual" data-manual="<?= $bill['service_cost_manual'] ?>" value="<?= $bill['services_cost'] ?>">
			</div></div>
		</div>
		<?php $pdf_conents[] = ['Total', $bill['services_cost']]; ?>
	<?php } else if(!isset($_GET['tab_only'])) { ?>
		<input type="hidden" name="billing_discount_type" value="<?= $bill['billing_discount_type'] ?>">
		<input type="hidden" name="billing_discount" value="<?= $bill['billing_discount'] ?>">
		<input type="hidden" name="services_cost" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-manual="<?= $bill['service_cost_manual'] ?>" value="<?= $bill['services_cost'] ?>">
	<?php } ?>

	<?php if(strpos($value_config, ',Service Estimated Hours,') !== FALSE && !isset($_GET['tab_only'])) { ?>
		<div class="form-group">
			<label class="col-sm-4">Total Time Estimate:</label>
			<div class="col-sm-8"><div class="col-sm-12">
				<input type="text" name="total_service_time_estimate" class="form-control" value="<?= $total_time_estimate ?>" disabled />
			</div></div>
		</div>
		<?php $pdf_conents[] = ['Total Time Estimate', $total_time_estimate]; ?>
	<?php } ?>
</div>