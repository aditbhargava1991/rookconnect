<div class='main_frame' id='no-more-tables'>
	<?php
	$id = false;
	if(isset($_GET['id']))
		$id = is_numeric($_GET['id']) ? $_GET['id'] : false;

	$sql = "SELECT * FROM `equipment_rate_table` WHERE `rate_id`='$id'";
	$row = mysqli_fetch_array(mysqli_query($dbc, $sql));
	if($id === false || mysqli_num_rows($result) > 0):
		$equip_sql = "SELECT CONCAT('Unit: ',`unit_number`,', VIN:',`vin_number`,', ',`make`,' ',`model`) name, CONCAT('Unit: ',`unit_number`,', VIN:',`vin_number`,', ',`make`,' ',`model`) description, `equipmentid` id FROM `equipment` WHERE `equipmentid`='{$row['equipment_id']}";
		$equip_row = mysqli_fetch_array(mysqli_query($dbc, $equip_sql)); ?>
		<div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_abi" >Rate Card Info<span class="glyphicon glyphicon-minus"></span></a>
                </h4>
            </div>

            <div id="collapse_abi" class="panel-collapse collapse in">
                <div class="panel-body">
					<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Equipment:</label>
					<div class='col-sm-8'><input class='form-control' type='text' name='equip_id' value='<?php echo $equip_row['name']; ?>'></div></div>
					<?php $field_config = get_config($dbc, 'equipment_rate_fields');
					if(str_replace(',','',$field_config) == '') {
						$field_config = ",annual,monthly,hourly,";
					}
					if(strpos($field_config, ',start_end_dates,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Start Date</label>
						<div class='col-sm-8'><input class='form-control datepicker' type='text' name='start_date' value='<?php echo $row['start_date']; ?>'></div></div>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>End Date</label>
						<div class='col-sm-8'><input class='form-control datepicker' type='text' name='end_date' value='<?php echo $row['end_date']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',reminder_alerts,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Alert Date</label>
						<div class='col-sm-8'><input class='form-control datepicker' type='text' name='alert_date' value='<?php echo $row['alert_date']; ?>'></div></div>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Alert Staff</label>
						<div class='col-sm-8'>
							<select name="alert_staff[]" multiple data-placeholder="Select Staff..." class="form-control chosen-select-deselect">
								<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
								foreach($staff_list as $staffid) {
									echo '<option value="'.$staffid.'" '.(strpos(','.$row['alert_staff'].',',','.$staffid.',') !== FALSE ? 'selected' : '').'>'.get_contact($dbc, $staffid).'</option>';
								} ?>
							</select>
						</div></div>
					<?php }
					if(strpos($field_config, ',annual,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Annual Rate</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='annual' value='<?php echo $row['annual']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',monthly,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Monthly Rate</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='monthly' value='<?php echo $row['monthly']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',semi_month,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Semi-Monthly Rate</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='semi_month' value='<?php echo $row['semi_month']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',weekly,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Weekly Rate</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='weekly' value='<?php echo $row['weekly']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',daily,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Daily Rate</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='daily' value='<?php echo $row['daily']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',hourly,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Hourly Rate</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='hourly' value='<?php echo $row['hourly']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',hourly_work,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Hourly Rate (Work)</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='hourly_work' value='<?php echo $row['hourly_work']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',hourly_travel,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Hourly Rate (Travel)</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='hourly_travel' value='<?php echo $row['hourly_travel']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',field_day_actual,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Field Day Rate (Actual Cost)</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='field_day_actual' value='<?php echo $row['field_day_actual']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',field_day_bill,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Field Day Rate (Billable Rate)</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='field_day_billable' value='<?php echo $row['field_day_bill']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',cost,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Cost</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='cost' value='<?php echo $row['cost']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',price_admin,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Admin Price</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='price_admin' value='<?php echo $row['price_admin']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',price_wholesale,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Wholesale Price</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='price_wholesale' value='<?php echo $row['price_wholesale']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',price_commercial,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Commercial Price</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='price_commercial' value='<?php echo $row['price_commercial']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',price_client,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Client Price</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='price_client' value='<?php echo $row['price_client']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',minimum,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Minimum Billable</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='minimum' value='<?php echo $row[minimum]; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',unit_price,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Unit Price</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='unit_price' value='<?php echo $row['unit_price']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',unit_cost,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Unit Cost</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='unit_cost' value='<?php echo $row['unit_cost']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',rent_price,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rent Price</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='rent_price' value='<?php echo $row['rent_price']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',rent_days,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rental Days</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='rent_days' value='<?php echo $row['rent_days']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',rent_weeks,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rental Weeks</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='rent_weeks' value='<?php echo $row['rent_weeks']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',rent_months,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rental Months</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='rent_months' value='<?php echo $row['rent_months']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',rent_years,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rental Years</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='rent_years' value='<?php echo $row['rent_years']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',num_days,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Number of Days</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='num_days' value='<?php echo $row['num_days']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',num_hours,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Number of Hours</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='num_hours' value='<?php echo $row['num_hours']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',num_kms,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Number of Kilometres</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='num_kms' value='<?php echo $row['num_kms']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',num_miles,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Number of Miles</label>
						<div class='col-sm-8'><input class='form-control' type='number' name='num_miles' value='<?php echo $row['num_miles']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',fee,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Fee</label>
						<div class='col-sm-8'><input class='form-control' type='text' name='fee' value='<?php echo $row['fee']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',hours_estimated,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Estimated Hours</label>
						<div class='col-sm-8'><input class='form-control' type='text' name='hours_estimated' value='<?php echo $row['hours_estimated']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',hours_actual,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Actual Hours</label>
						<div class='col-sm-8'><input class='form-control' type='text' name='hours_actual' value='<?php echo $row['hours_actual']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',service_code,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Service Code</label>
						<div class='col-sm-8'><input class='form-control' type='text' name='service_code' value='<?php echo $row['service_code']; ?>'></div></div>
					<?php }
					if(strpos($field_config, ',description,') !== false) { ?>
						<div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rate Description</label>
						<div class='col-sm-8'><input class='form-control' type='text' name='description' value='<?php echo $row['description']; ?>'></div></div>
					<?php } ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
</div>