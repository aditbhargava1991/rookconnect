<?php
$category = '';
$equ_description = '';
$type = '';
$make = '';
$model = '';
$submodel = '';
$model_year = '';
$label = '';
$staff = '';
$total_kilometres = '';
$style = '';
$vehicle_size = '';
$color = '';
$trim = '';
$fuel_type = '';
$tire_type = '';
$drive_train = '';
$serial_number = '';
$unit_number = '';
$vin_number = '';
$licence_plate = '';
$nickname = '';
$year_purchased = '';
$mileage = '';
$hours_operated = '';
$cost = '';
$cnd_cost_per_unit = '';
$usd_cost_per_unit = '';
$finance = '';
$lease = '';
$insurance = '';
$insurance_contact = '';
$insurance_phone = '';
$hourly_rate = '';
$daily_rate = '';
$semi_monthly_rate = '';
$monthly_rate = '';
$field_day_cost = '';
$field_day_billable = '';
$hr_rate_work = '';
$hr_rate_travel = '';
$next_service_date = '';
$next_service = '';
$next_serv_desc = '';
$service_location = '';
$last_oil_filter_change_date = '';
$last_oil_filter_change = '';
$next_oil_filter_change_date = '';
$next_oil_filter_change = '';
$last_insp_tune_up_date = '';
$last_insp_tune_up = '';
$next_insp_tune_up_date = '';
$next_insp_tune_up = '';
$tire_condition = '';
$last_tire_rotation_date = '';
$last_tire_rotation = '';
$next_tire_rotation_date = '';
$next_tire_rotation = '';
$reg_renewal_date = '';
$insurance_renewal = '';
$location = '';
$lsd = '';
$status = '';
$ownership_status = '';
$quote_description = '';
$notes = '';
$cviprenewal = '';

$handle = fopen($_FILES['upload']['tmp_name'], "r");
$titles = [];
$row = 0;
$error_rows = [];
while($row = fgetcsv($handle)) {
	$row++;
	if(empty($titles)) {
		$titles = $row;
	} else {
		foreach($titles as $i => $field) {
			switch($field) {
				case 'Category':
					$category = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Description':
					$equ_description = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Type':
					$type = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Make':
					$make = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Model':
					$model = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Unit of Measure':
					$submodel = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Model Year':
					$model_year = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Equipment Label':
					$label = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Total Kilometres':
					$total_kilometres = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Style':
					$style = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Vehicle Size':
					$vehicle_size = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Color':
					$color = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Trim':
					$trim = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Staff':
					$staff = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Fuel Type':
					$fuel_type = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Tire Type':
					$tire_type = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Drive Train':
					$drive_train = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Serial #':
					$serial_number = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Unit #':
					$unit_number = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'VIN #':
					$vin_number = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Licence Plate':
					$licence_plate = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Nickname':
					$nickname = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Year Purchased':
					$year_purchased = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Mileage':
					$mileage = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Hours Operated':
					$hours_operated = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Cost':
					$cost = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'CDN Cost Per Unit':
					$cdn_cost_per_unit = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'USD Cost Per Unit':
					$usd_cost_per_unit = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Finance':
					$finance = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Lease':
					$lease = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Insurance Company':
					$insurance = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Insurance Contact':
					$insurance_contact = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Insurance Phone':
					$insurance_phone = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Hourly Rate':
					$hourly_rate = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Daily Rate':
					$daily_rate = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Semi Monthly Rate':
					$semi_monthly_rate = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Monthly Rate':
					$monthly_rate = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Field Day Cost':
					$field_day_cost = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Field Day Billable':
					$field_day_billable = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'HR Rate Work':
					$hr_rate_work = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'HR Rate Travel':
					$hr_rate_travel = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Next Service Date':
					$next_service_date = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Next Service Hours':
					$next_service = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Next Service Description':
					$next_serv_desc = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Service Location':
					$service_location = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Last Oil Filter Change (date)':
					$last_oil_filter_change_date = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Last Oil Filter Change (km)':
					$last_oil_filter_change = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Next Oil Filter Change (date)':
					$next_oil_filter_change_date = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Next Oil Filter Change (km)':
					$next_oil_filter_change = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Last Inspection & Tune Up (date)':
					$last_insp_tune_up_date = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Last Inspection & Tune Up (km)':
					$last_insp_tune_up = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Next Inspection & Tune Up (date)':
					$next_insp_tune_up_date = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Next Inspection & Tune Up (km)':
					$next_insp_tune_up = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Tire Condition':
					$tire_condition = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Last Tire Rotation (date)':
					$last_tire_rotation_date = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Last Tire Rotation (km)':
					$last_tire_rotation = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Next Tire Rotation (date)':
					$next_tire_rotation_date = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Next Tire Rotation (km)':
					$next_tire_rotation = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Registration Renewal Date':
					$reg_renewal_date = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Insurance Renewal Date':
					$insurance_renewal = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Location':
					$location = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'LSD':
					$lsd = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Status':
					$status = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Ownership Status':
					$ownership_status = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Quote Description':
					$quote_description = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Notes':
					$notes = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'CVIP Ticket Renewal Date':
					$cviprenewal = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Vehicle Access Code':
					$vehicle_access_code = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Cargo':
					$cargo = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Lessor':
					$lessor = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Group':
					$group = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
				case 'Use':
					$use = filter_var($row[$i],FILTER_SANITIZE_STRING);
					break;
			}
		}
		
		$query_insert_equipment = "INSERT INTO `equipment` (`equ_description`, `category`, `type`, `make`, `model`, `submodel`, `model_year`, `label`, `total_kilometres`, `style`, `vehicle_size`, `color`, `trim`, `fuel_type`, `tire_type`, `drive_train`, `serial_number`, `unit_number`, `vin_number`, `licence_plate`, `nickname`, `year_purchased`, `mileage`, `hours_operated`, `cost`, `cnd_cost_per_unit`, `usd_cost_per_unit`, `finance`, `lease`, `insurance`, `insurance_contact`, `insurance_phone`, `hourly_rate`, `daily_rate`, `semi_monthly_rate`, `monthly_rate`, `field_day_cost`, `field_day_billable`, `hr_rate_work`, `hr_rate_travel`, `next_service_date`, `next_service`, `next_serv_desc`, `service_location`, `last_oil_filter_change_date`, `last_oil_filter_change`, `next_oil_filter_change_date`, `next_oil_filter_change`, `last_insp_tune_up_date`, `last_insp_tune_up`, `next_insp_tune_up_date`, `next_insp_tune_up`, `tire_condition`, `last_tire_rotation_date`, `last_tire_rotation`, `next_tire_rotation_date`, `next_tire_rotation`, `reg_renewal_date`, `insurance_renewal`, `location`, `lsd`, `status`, `ownership_status`, `quote_description`, `notes`, `cvip_renewal_date`, `vehicle_access_code`, `cargo`, `lessor`, `group`, `use`, `staffid`)
			VALUES ('$equ_description', '$category', '$type', '$make', '$model', '$submodel', '$model_year', '$label', '$total_kilometres', '$style', '$vehicle_size', '$color', '$trim', '$fuel_type', '$tire_type', '$drive_train', '$serial_number', '$unit_number', '$vin_number', '$licence_plate', '$nickname', '$year_purchased', '$mileage', '$hours_operated', '$cost', '$cnd_cost_per_unit', '$usd_cost_per_unit', '$finance', '$lease', '$insurance', '$insurance_contact', '$insurance_phone', '$hourly_rate', '$daily_rate', '$semi_monthly_rate', '$monthly_rate', '$field_day_cost', '$field_day_billable', '$hr_rate_work', '$hr_rate_travel', '$next_service_date', '$next_service', '$next_serv_desc', '$service_location', '$last_oil_filter_change_date', '$last_oil_filter_change', '$next_oil_filter_change_date', '$next_oil_filter_change', '$last_insp_tune_up_date', '$last_insp_tune_up', '$next_insp_tune_up_date', '$next_insp_tune_up', '$tire_condition', '$last_tire_rotation_date', '$last_tire_rotation', '$next_tire_rotation_date', '$next_tire_rotation', '$reg_renewal_date', '$insurance_renewal', '$location', '$lsd', '$status', '$ownership_status', '$quote_description', '$notes', '$cviprenewal', '$vehicle_access_code', '$cargo', '$lessor', '$group', '$use', '$staff')";
		if(!mysqli_query($dbc, $query_insert_equipment)) {
			$error_rows[] = $row;
			echo "<script> console.log('Row $row Error: ".mysqli_error($dbc)."'); </script>";
			echo "<script> console.log(\"".str_replace(['`',"\n"],'',$query_insert_equipment)."\"); </script>";
		}
	}
}
if(count($error_rows) > 0) {
	echo "<script> alert('Errors were encountered on the following rows:\\n";
	echo implode(', ',$error_rows);
	echo "\\nPlease check your spreadsheet and try again.') </script>";
}