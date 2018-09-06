<?php include_once('../include.php');
checkAuthorised('staff');
if(!empty($_POST['submit_rate_card'])) {
	$staff_id = $_POST['submit_rate_card'];
	include('../Staff/save_staff_rate_card.php');
	//echo '<script>parent.window.location.reload();</script>';
}
$rc_field_config = ','.get_config($dbc, 'staff_rate_fields').',';
if(str_replace(',','',$rc_field_config) == '') {
	$rc_field_config = ",annual,monthly,hourly,";
}
$view_access = tile_visible($dbc, 'rate_card');
$edit_access = vuaed_visible_function($dbc, 'rate_card');
$subtab_access = check_subtab_persmission($dbc, 'rate_card', ROLE, 'staff');

$staff_id = !empty($_GET['id']) ? $_GET['id'] : $_GET['contactid'];
$staff_heading = get_staff($dbc, $staff_id);
?>
<script type="text/javascript">
function addRateCard() {
	var cur_row_i = parseInt($('[name="cur_row_i"]').val());

	destroyInputs('.rate_card_table');
	var block = $('.rate_card_table .rate_card_row').last();
	var clone = $(block).clone();
	clone.find('input,select').val('');
	clone.find('[name^=alert_staff]').prop('name', 'alert_staff_'+cur_row_i+'[]');
	clone.find('[name="ratecard_row_i[]"]').val(cur_row_i);
	block.after(clone);
	initInputs('.rate_card_table');

	$('[name="cur_row_i"]').val((cur_row_i+1));
}
function removeRateCard(img) {
	if($('.rate_card_table .rate_card_row').length <= 1) {
		addRateCard();
	}
	$(img).closest('tr').remove();
}
</script>
<?php if($_GET['from_type'] == 'dashboard') { ?>
	<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
<?php } ?>
	<div class="rate_card_div" <?= $view_access > 0 && $subtab_access ? '' : 'style="display: none;"' ?>>
		<h3>Rate Card<?= !empty($staff_heading) ? ' - '.$staff_heading : '' ?></h3>
		<table id="no-more-tables" class="table table-bordered rate_card_table">
			<tr class="hidden-xs hidden-sm">
				<?php if(strpos($rc_field_config, ',start_end_dates,') !== false) { ?>
					<th>Start Date</th>
					<th>End Date</th>
				<?php } if(strpos($rc_field_config, ',reminder_alerts,') !== false) { ?>
					<th>Alert Date</th>
					<th>Alert Staff</th>
				<?php } ?>
				<?php if(strpos($rc_field_config, ',category,') !== false) { ?><th>Category</th><?php } ?>
				<?php if(strpos($rc_field_config, ',annual,') !== false) { ?><th>Annual Rate</th><?php } ?>
				<?php if(strpos($rc_field_config, ',monthly,') !== false) { ?><th>Monthly Rate</th><?php } ?>
				<?php if(strpos($rc_field_config, ',semi_month,') !== false) { ?><th>Semi-Monthly Rate</th><?php } ?>
				<?php if(strpos($rc_field_config, ',weekly,') !== false) { ?><th>Weekly Rate</th><?php } ?>
				<?php if(strpos($rc_field_config, ',daily,') !== false) { ?><th>Daily Rate</th><?php } ?>
				<?php if(strpos($rc_field_config, ',hourly,') !== false) { ?><th>Hourly Rate</th><?php } ?>
				<?php if(strpos($rc_field_config, ',hourly_work,') !== false) { ?><th>Hourly Rate (Work)</th><?php } ?>
				<?php if(strpos($rc_field_config, ',hourly_travel,') !== false) { ?><th>Hourly Rate (Travel)</th><?php } ?>
				<?php if(strpos($rc_field_config, ',field_day_actual,') !== false) { ?><th>Field Day Rate (Actual Cost)</th><?php } ?>
				<?php if(strpos($rc_field_config, ',field_day_bill,') !== false) { ?><th>Field Day Rate (Billable Rate)</th><?php } ?>
				<?php if(strpos($rc_field_config, ',cost,') !== false) { ?><th>Cost</th><?php } ?>
				<?php if(strpos($rc_field_config, ',price_admin,') !== false) { ?><th>Admin Price</th><?php } ?>
				<?php if(strpos($rc_field_config, ',price_wholesale,') !== false) { ?><th>Wholesale Price</th><?php } ?>
				<?php if(strpos($rc_field_config, ',price_commercial,') !== false) { ?><th>Commercial Price</th><?php } ?>
				<?php if(strpos($rc_field_config, ',price_client,') !== false) { ?><th>Client Price</th><?php } ?>
				<?php if(strpos($rc_field_config, ',minimum,') !== false) { ?><th>Minimum Billable</th><?php } ?>
				<?php if(strpos($rc_field_config, ',unit_price,') !== false) { ?><th>Unit Price</th><?php } ?>
                <?php if(strpos($rc_field_config, ',unit_cost,') !== false) { ?><th>>Unit Cost</th><?php } ?>
				<?php if(strpos($rc_field_config, ',rent_price,') !== false) { ?><th>Rent Price</th><?php } ?>
				<?php if(strpos($rc_field_config, ',rent_days,') !== false) { ?><th>Rental Days</th><?php } ?>
				<?php if(strpos($rc_field_config, ',rent_weeks,') !== false) { ?><th>>Rental Weeks</th><?php } ?>
				<?php if(strpos($rc_field_config, ',rent_months,') !== false) { ?><th>Rental Months</th><?php } ?>
				<?php if(strpos($rc_field_config, ',rent_years,') !== false) { ?><th>Rental Years</th><?php } ?>
				<?php if(strpos($rc_field_config, ',num_days,') !== false) { ?><th>Number of Days</th><?php } ?>
				<?php if(strpos($rc_field_config, ',num_hours,') !== false) { ?><th>Number of Hours</th><?php } ?>
				<?php if(strpos($rc_field_config, ',num_kms,') !== false) { ?><th>Number of Kilometres</th><?php } ?>
				<?php if(strpos($rc_field_config, ',num_miles,') !== false) { ?><th>Number of Miles</th><?php } ?>
				<?php if(strpos($rc_field_config, ',fee,') !== false) { ?><th>Fee</th><?php } ?>
				<?php if(strpos($rc_field_config, ',hours_estimated,') !== false) { ?><th>Estimated Hours</th><?php } ?>
				<?php if(strpos($rc_field_config, ',hours_actual,') !== false) { ?><th>Actual Hours</th><?php } ?>
				<?php if(strpos($rc_field_config, ',service_code,') !== false) { ?><th>Service Code</th><?php } ?>
				<?php if(strpos($rc_field_config, ',description,') !== false) { ?><th>Rate Description</th><?php } ?>
				<?php if(strpos($rc_field_config, ',work_desc,') !== false) { ?><th>Work Description</th><?php } ?>
				<?php if($edit_access > 0 && $subtab_access) { ?>
					<th>Function</th>
				<?php } ?>
			</tr><?php

            $query = mysqli_query($dbc, "SELECT * FROM `staff_rate_table` WHERE `staff_id`='$staff_id' AND `deleted`=0");
			$row = mysqli_fetch_array($query);
			$row_i = 0;
			do { ?>
				<tr class="rate_card_row">
					<input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?> type="hidden" name="ratecardid[]" value="<?= $row['rate_id'] ?>">
					<input type="hidden" name="ratecard_row_i[]" value="<?= $row_i ?>">
					<?php if(strpos($rc_field_config, ',start_end_dates,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Start Date"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control datepicker' type='text' name='start_date[]' value='<?php echo $row['start_date']; ?>'></td>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="End Date"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control datepicker' type='text' name='end_date[]' value='<?php echo $row['end_date']; ?>'></td>
					<?php }
					if(strpos($rc_field_config, ',reminder_alerts,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Alert Date"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control datepicker' type='text' name='alert_date[]' value='<?php echo $row['alert_date']; ?>'></td>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Alert Staff">
							<select <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  name="alert_staff_<?= $row_i ?>[]" multiple data-placeholder="Select Staff..." class="form-control chosen-select-deselect">
								<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
								foreach($staff_list as $staffid) {
									echo '<option value="'.$staffid.'" '.(strpos(','.$row['alert_staff'].',',','.$staffid.',') !== FALSE ? 'selected' : '').'>'.get_contact($dbc, $staffid).'</option>';
								} ?>
							</select>
						</td>
					<?php }
					if(strpos($rc_field_config, ',category,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Category"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='text' name='category[]' value='<?php echo $row['category']; ?>'></td>
					<?php }
					if(strpos($rc_field_config, ',annual,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Annual Rate"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='annual[]' value='<?php echo $row['annual']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',monthly,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Monthly Rate"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='monthly[]' value='<?php echo $row['monthly']; ?>' min='0' step='any'></th>
					<?php }
					if(strpos($rc_field_config, ',semi_month,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Semi-Monthly Rate"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='semi_month[]' value='<?php echo $row['semi_month']; ?>' min='0' step='any'></th>
					<?php }
					if(strpos($rc_field_config, ',weekly,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Weekly Rate"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='weekly[]' value='<?php echo $row['weekly']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',daily,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Daily Rate"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='daily[]' value='<?php echo $row['daily']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',hourly,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Hourly Rate"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='hourly[]' value='<?php echo $row['hourly']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',hourly_work,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Hourly Rate (Work)"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='hourly_work[]' value='<?php echo $row['hourly_work']; ?>' min='0' step='any'></th>
					<?php }
					if(strpos($rc_field_config, ',hourly_travel,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Hourly Rate (Travel)"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='hourly_travel[]' value='<?php echo $row['hourly_travel']; ?>' min='0' step='any'></th>
					<?php }
					if(strpos($rc_field_config, ',field_day_actual,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Field Day Rate (Actual Cost)"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='field_day_actual[]' value='<?php echo $row['field_day_actual']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',field_day_bill,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Field Day Rate (Billable Rate)"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='field_day_billable[]' value='<?php echo $row['field_day_bill']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',cost,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Cost"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='cost[]' value='<?php echo $row['cost']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',price_admin,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Admin Price"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='price_admin[]' value='<?php echo $row['price_admin']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',price_wholesale,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Wholesale Price"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='price_wholesale[]' value='<?php echo $row['price_wholesale']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',price_commercial,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Commercial Price"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='price_commercial[]' value='<?php echo $row['price_commercial']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',price_client,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Client Price"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='price_client[]' value='<?php echo $row['price_client']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',minimum,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Minimum Billable"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='minimum[]' value='<?php echo $row['minimum']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',unit_price,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Unit Price"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='unit_price[]' value='<?php echo $row['unit_price']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',unit_cost,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Unit Cost"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='unit_cost[]' value='<?php echo $row['unit_cost']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',rent_price,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Rent Price"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='rent_price[]' value='<?php echo $row['rent_price']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',rent_days,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Rental Days"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='rent_days[]' value='<?php echo $row['rent_days']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',rent_weeks,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Rental Weeks"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='rent_weeks[]' value='<?php echo $row['rent_weeks']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',rent_months,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Rental Months"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='rent_months[]' value='<?php echo $row['rent_months']; ?>' min='0' step='any'>
					<?php }
					if(strpos($rc_field_config, ',rent_years,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Rental Years"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='rent_years[]' value='<?php echo $row['rent_years']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',num_days,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Number of Days"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='num_days[]' value='<?php echo $row['num_days']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',num_hours,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Number of Hours"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='num_hours[]' value='<?php echo $row['num_hours']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',num_kms,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Number of Kilometres"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='num_kms[]' value='<?php echo $row['num_kms']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',num_miles,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Number of Miles"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='number' name='num_miles[]' value='<?php echo $row['num_miles']; ?>' min='0' step='any'></td>
					<?php }
					if(strpos($rc_field_config, ',fee,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Fee"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='text' name='fee[]' value='<?php echo $row['fee']; ?>'></td>
					<?php }
					if(strpos($rc_field_config, ',hours_estimated,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Estimated Hours"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='text' name='hours_estimated[]' value='<?php echo $row['hours_estimated']; ?>'></td>
					<?php }
					if(strpos($rc_field_config, ',hours_actual,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Actual Hours"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='text' name='hours_actual[]' value='<?php echo $row['hours_actual']; ?>'></td>
					<?php }
					if(strpos($rc_field_config, ',service_code,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Service Code"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='text' name='service_code[]' value='<?php echo $row['service_code']; ?>'></td>
					<?php }
					if(strpos($rc_field_config, ',description,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Rate Description"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='text' name='description[]' value='<?php echo $row['description']; ?>'></td>
					<?php }
					if(strpos($rc_field_config, ',work_desc,') !== false) { ?>
						<td <?= $edit_access > 0 && $subtab_access ? '' : 'class="field-disabled"' ?> data-title="Work Description"><input <?= $edit_access > 0 && $subtab_access ? '' : 'readonly' ?>  class='form-control' type='text' name='work_desc[]' value='<?php echo $row['work_desc']; ?>'></td>
					<?php } ?>
					<?php if($edit_access > 0 && $subtab_access) { ?>
						<td data-title="Function">
			                <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addRateCard();">
			                <img src="../img/remove.png" class="inline-img pull-right" onclick="removeRateCard(this);">
						</td>
					<?php } ?>
				</tr>
				<?php $row_i++;
			} while($row = mysqli_fetch_array($query)); ?>
		</table>
		<input type="hidden" name="cur_row_i" value="<?= $row_i ?>">

		<?php if($_GET['from_type'] == 'dashboard' && $edit_access > 0 && $subtab_access) { ?>
			<div class="form-group pull-right">
				<a href="?" class="btn brand-btn">Cancel</a>
				<button type="submit" name="submit_rate_card" value="<?= $_GET['id'] ?>" class="btn brand-btn">Submit</button>
			</div>
		<?php } ?>
	</div>
<?php if($_GET['from_type'] == 'dashboard') { ?>
	</form>
<?php } ?>