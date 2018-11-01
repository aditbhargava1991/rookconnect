<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if (isset($_POST['submit'])) {

    if($_POST['ownership_status'] == 'Other') {
        $ownership_status = filter_var($_POST['new_ownership_status'],FILTER_SANITIZE_STRING);
    } else {
        $ownership_status = filter_var($_POST['ownership_status'],FILTER_SANITIZE_STRING);
    }

    if($_POST['make'] == 'Other') {
        $make = filter_var($_POST['make_name'],FILTER_SANITIZE_STRING);
    } else {
        $make = filter_var($_POST['make'],FILTER_SANITIZE_STRING);
    }

    if($_POST['cargo'] == 'Other') {
        $cargo = filter_var($_POST['new_cargo'],FILTER_SANITIZE_STRING);
    } else {
        $cargo = filter_var($_POST['cargo'],FILTER_SANITIZE_STRING);
    }

    if($_POST['lessor'] == 'Other') {
        $lessor = filter_var($_POST['new_lessor'],FILTER_SANITIZE_STRING);
    } else {
        $lessor = filter_var($_POST['lessor'],FILTER_SANITIZE_STRING);
    }

    if($_POST['group'] == 'Other') {
        $group = filter_var($_POST['new_group'],FILTER_SANITIZE_STRING);
    } else {
        $group = filter_var($_POST['group'],FILTER_SANITIZE_STRING);
    }

    if($_POST['use'] == 'Other') {
        $use = filter_var($_POST['new_use'],FILTER_SANITIZE_STRING);
    } else {
        $use = filter_var($_POST['use'],FILTER_SANITIZE_STRING);
    }

    $equ_description = filter_var(htmlentities($_POST['equ_description']),FILTER_SANITIZE_STRING);
    $quote_description = filter_var(htmlentities($_POST['quote_description']),FILTER_SANITIZE_STRING);
    $notes = filter_var(htmlentities($_POST['notes']),FILTER_SANITIZE_STRING);

    $category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
    $type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);
	if($type == 'Other') {
		$type = filter_var($_POST['type_name'],FILTER_SANITIZE_STRING);
	}
    $model = filter_var($_POST['model'],FILTER_SANITIZE_STRING);
    $submodel = filter_var($_POST['submodel'],FILTER_SANITIZE_STRING);
    $model_year = filter_var($_POST['model_year'],FILTER_SANITIZE_STRING);
    $label = filter_var($_POST['label'],FILTER_SANITIZE_STRING);
	$total_kilometres = filter_var($_POST['total_kilometres'],FILTER_SANITIZE_STRING);
	$leased = filter_var($_POST['leased'],FILTER_SANITIZE_STRING);
    $style = filter_var($_POST['style'],FILTER_SANITIZE_STRING);
    $vehicle_size = filter_var($_POST['vehicle_size'],FILTER_SANITIZE_STRING);
    $color = filter_var($_POST['color'],FILTER_SANITIZE_STRING);
    $trim = filter_var($_POST['trim'],FILTER_SANITIZE_STRING);
    $fuel_type = filter_var($_POST['fuel_type'],FILTER_SANITIZE_STRING);
    $tire_type = filter_var($_POST['tire_type'],FILTER_SANITIZE_STRING);
    $drive_train = filter_var($_POST['drive_train'],FILTER_SANITIZE_STRING);
    $serial_number = filter_var($_POST['serial_number'],FILTER_SANITIZE_STRING);
    $part_serial_names = filter_var(implode('#*#',$_POST['part_serial_names']),FILTER_SANITIZE_STRING);
    $part_serials = filter_var(implode('#*#',$_POST['part_serials']),FILTER_SANITIZE_STRING);
    $unit_number = filter_var($_POST['unit_number'],FILTER_SANITIZE_STRING);
    $vin_number = filter_var($_POST['vin_number'],FILTER_SANITIZE_STRING);
    $licence_plate = filter_var($_POST['licence_plate'],FILTER_SANITIZE_STRING);
    $nickname = filter_var($_POST['nickname'],FILTER_SANITIZE_STRING);
    $year_purchased = filter_var($_POST['year_purchased'],FILTER_SANITIZE_STRING);
    $mileage = filter_var($_POST['mileage'],FILTER_SANITIZE_STRING);
    $hours_operated = filter_var($_POST['hours_operated'],FILTER_SANITIZE_STRING);
    $cost = filter_var($_POST['cost'],FILTER_SANITIZE_STRING);
    $cnd_cost_per_unit = filter_var($_POST['cnd_cost_per_unit'],FILTER_SANITIZE_STRING);
    $usd_cost_per_unit = filter_var($_POST['usd_cost_per_unit'],FILTER_SANITIZE_STRING);
    $finance = filter_var($_POST['finance'],FILTER_SANITIZE_STRING);
    $lease = filter_var($_POST['lease'],FILTER_SANITIZE_STRING);
    $insurance = filter_var($_POST['insurance'],FILTER_SANITIZE_STRING);
    $hourly_rate = filter_var($_POST['hourly_rate'],FILTER_SANITIZE_STRING);
    $daily_rate = filter_var($_POST['daily_rate'],FILTER_SANITIZE_STRING);
    $semi_monthly_rate = filter_var($_POST['semi_monthly_rate'],FILTER_SANITIZE_STRING);
    $monthly_rate = filter_var($_POST['monthly_rate'],FILTER_SANITIZE_STRING);
    $field_day_cost = filter_var($_POST['field_day_cost'],FILTER_SANITIZE_STRING);
    $field_day_billable = filter_var($_POST['field_day_billable'],FILTER_SANITIZE_STRING);
    $hr_rate_work = filter_var($_POST['hr_rate_work'],FILTER_SANITIZE_STRING);
    $hr_rate_travel = filter_var($_POST['hr_rate_travel'],FILTER_SANITIZE_STRING);
    $follow_up_date = filter_var($_POST['follow_up_date'],FILTER_SANITIZE_STRING);
    $follow_up_staff = filter_var(implode(',',$_POST['follow_up_staff']),FILTER_SANITIZE_STRING);
    $next_service_date = filter_var($_POST['next_service_date'],FILTER_SANITIZE_STRING);
    $next_service = filter_var($_POST['next_service'],FILTER_SANITIZE_STRING);
    $next_serv_desc = filter_var($_POST['next_serv_desc'],FILTER_SANITIZE_STRING);
    $service_location = filter_var($_POST['service_location'],FILTER_SANITIZE_STRING);
    $last_oil_filter_change_date = filter_var($_POST['last_oil_filter_change_date'],FILTER_SANITIZE_STRING);
    $last_oil_filter_change = filter_var($_POST['last_oil_filter_change'],FILTER_SANITIZE_STRING);
    $last_oil_filter_change_hrs = filter_var($_POST['last_oil_filter_change_hrs'],FILTER_SANITIZE_STRING);
    $next_oil_filter_change_date = filter_var($_POST['next_oil_filter_change_date'],FILTER_SANITIZE_STRING);
    $next_oil_filter_change = filter_var($_POST['next_oil_filter_change'],FILTER_SANITIZE_STRING);
    $next_oil_filter_change_hrs = filter_var($_POST['next_oil_filter_change_hrs'],FILTER_SANITIZE_STRING);
    $last_insp_tune_up_date = filter_var($_POST['last_insp_tune_up_date'],FILTER_SANITIZE_STRING);
    $last_insp_tune_up = filter_var($_POST['last_insp_tune_up'],FILTER_SANITIZE_STRING);
    $last_insp_tune_up_hrs = filter_var($_POST['last_insp_tune_up_hrs'],FILTER_SANITIZE_STRING);
    $next_insp_tune_up_date = filter_var($_POST['next_insp_tune_up_date'],FILTER_SANITIZE_STRING);
    $next_insp_tune_up = filter_var($_POST['next_insp_tune_up'],FILTER_SANITIZE_STRING);
    $next_insp_tune_up_hrs = filter_var($_POST['next_insp_tune_up_hrs'],FILTER_SANITIZE_STRING);
    $tire_condition = filter_var($_POST['tire_condition'],FILTER_SANITIZE_STRING);
    $last_tire_rotation_date = filter_var($_POST['last_tire_rotation_date'],FILTER_SANITIZE_STRING);
    $last_tire_rotation = filter_var($_POST['last_tire_rotation'],FILTER_SANITIZE_STRING);
    $last_tire_rotation_hrs = filter_var($_POST['last_tire_rotation_hrs'],FILTER_SANITIZE_STRING);
    $next_tire_rotation_date = filter_var($_POST['next_tire_rotation_date'],FILTER_SANITIZE_STRING);
    $next_tire_rotation = filter_var($_POST['next_tire_rotation'],FILTER_SANITIZE_STRING);
    $next_tire_rotation_hrs = filter_var($_POST['next_tire_rotation_hrs'],FILTER_SANITIZE_STRING);
    $reg_renewal_date = filter_var($_POST['reg_renewal_date'],FILTER_SANITIZE_STRING);
    $insurance_renewal = filter_var($_POST['insurance_renewal'],FILTER_SANITIZE_STRING);
    $region = filter_var(implode('*#*',array_filter($_POST['region'])),FILTER_SANITIZE_STRING);
    $location = filter_var(implode('*#*',array_filter($_POST['location'])),FILTER_SANITIZE_STRING);
    $location_cookie = filter_var($_POST['location_cookie'],FILTER_SANITIZE_STRING);
    $classification = filter_var(implode('*#*',array_filter($_POST['classification'])),FILTER_SANITIZE_STRING);
    $current_address = filter_var($_POST['current_address'],FILTER_SANITIZE_STRING);
    $lsd = filter_var($_POST['lsd'],FILTER_SANITIZE_STRING);
    $status = filter_var($_POST['status'],FILTER_SANITIZE_STRING);
	$cviprenewal = filter_var($_POST['cviprenewal'],FILTER_SANITIZE_STRING);
	$insurance_contact = filter_var($_POST['insurance_contact'],FILTER_SANITIZE_STRING);
	$insurance_phone = filter_var($_POST['insurance_phone'],FILTER_SANITIZE_STRING);
	$insurance_card = filter_var($_POST['insurance_card_hidden'],FILTER_SANITIZE_STRING);
	$assigned_status = filter_var($_POST['assigned_status'],FILTER_SANITIZE_STRING);
	$volume = filter_var($_POST['volume'],FILTER_SANITIZE_STRING);
    $vehicle_access_code = filter_var($_POST['vehicle_access_code'],FILTER_SANITIZE_STRING);
    $staff = filter_var(implode(',',$_POST['staff']),FILTER_SANITIZE_STRING);

	if(!empty($_FILES['insurance_card']['name'])) {
		$filename = $_FILES['insurance_card']['name'];
		$file = $_FILES['insurance_card']['tmp_name'];
		if (!file_exists('download')) {
			mkdir('download', 0777, true);
		}
		$basefilename = $filename = preg_replace('/[^A-Za-z0-9\.]/','_',$filename);
		$i = 0;
		while(file_exists('download/'.$filename)) {echo $filename;
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basefilename);
		}
		move_uploaded_file($file, "download/".$filename);
		$insurance_card = $filename;
	}
	$registration_card = filter_var($_POST['registration_card_hidden'],FILTER_SANITIZE_STRING);
	if(!empty($_FILES['registration_card']['name'])) {
		$filename = $_FILES['registration_card']['name'];
		$file = $_FILES['registration_card']['tmp_name'];
		if (!file_exists('download')) {
			mkdir('download', 0777, true);
		}
		$basefilename = $filename = preg_replace('/[^A-Za-z0-9\.]/','_',$filename);
		$i = 0;
		while(file_exists('download/'.$filename)) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basefilename);
		}
		move_uploaded_file($file, "download/".$filename);
		$registration_card = $filename;
	}

    if(empty($_POST['equipmentid'])) {
        $query_insert_equipment = "INSERT INTO `equipment` (`equ_description`, `assigned_status`, `category`, `type`, `make`, `model`, `submodel`, `model_year`, `label`, `total_kilometres`, `leased`, `style`, `vehicle_size`, `color`, `trim`, `fuel_type`, `tire_type`, `drive_train`, `serial_number`, `part_serial_names`, `part_serials`, `unit_number`, `vin_number`, `licence_plate`, `nickname`, `year_purchased`, `mileage`, `hours_operated`, `cost`, `cnd_cost_per_unit`, `usd_cost_per_unit`, `finance`, `lease`, `insurance`, `insurance_contact`, `insurance_phone`, `hourly_rate`, `daily_rate`, `semi_monthly_rate`, `monthly_rate`, `field_day_cost`, `field_day_billable`, `hr_rate_work`, `hr_rate_travel`, `follow_up_date`, `follow_up_staff`, `next_service_date`, `next_service`, `next_serv_desc`, `service_location`, `last_oil_filter_change_date`, `last_oil_filter_change`, `last_oil_filter_change_hrs`, `next_oil_filter_change_date`, `next_oil_filter_change`, `next_oil_filter_change_hrs`, `last_insp_tune_up_date`, `last_insp_tune_up`, `last_insp_tune_up_hrs`, `next_insp_tune_up_date`, `next_insp_tune_up`, `next_insp_tune_up_hrs`, `tire_condition`, `last_tire_rotation_date`, `last_tire_rotation`, `last_tire_rotation_hrs`, `next_tire_rotation_date`, `next_tire_rotation`, `next_tire_rotation_hrs`, `reg_renewal_date`, `insurance_renewal`, `location`, `location_cookie`, `current_address`, `lsd`, `status`, `volume`, `ownership_status`, `quote_description`, `notes`, `cvip_renewal_date`, `insurance_file`, `registration_file`, `region`, `classification`, `vehicle_access_code`, `cargo`, `lessor`, `group`, `use`, `staffid`) VALUES ('$equ_description', '$assigned_status', '$category', '$type', '$make', '$model', '$submodel', '$model_year', '$label', '$total_kilometres', '$leased', '$style', '$vehicle_size', '$color', '$trim', '$fuel_type', '$tire_type', '$drive_train', '$serial_number', '$part_serial_names', '$part_serials', '$unit_number', '$vin_number', '$licence_plate', '$nickname', '$year_purchased', '$mileage', '$hours_operated', '$cost', '$cnd_cost_per_unit', '$usd_cost_per_unit', '$finance', '$lease', '$insurance', '$insurance_contact', '$insurance_phone', '$hourly_rate', '$daily_rate', '$semi_monthly_rate', '$monthly_rate', '$field_day_cost', '$field_day_billable', '$hr_rate_work', '$hr_rate_travel', '$follow_up_date', '$follow_up_staff', '$next_service_date', '$next_service', '$next_serv_desc', '$service_location', '$last_oil_filter_change_date', '$last_oil_filter_change', '$last_oil_filter_change_hrs', '$next_oil_filter_change_date', '$next_oil_filter_change', '$next_oil_filter_change_hrs', '$last_insp_tune_up_date', '$last_insp_tune_up', '$last_insp_tune_up_hrs', '$next_insp_tune_up_date', '$next_insp_tune_up', '$next_insp_tune_up_hrs', '$tire_condition', '$last_tire_rotation_date', '$last_tire_rotation', '$last_tire_rotation_hrs', '$next_tire_rotation_date', '$next_tire_rotation', '$next_tire_rotation_hrs', '$reg_renewal_date', '$insurance_renewal', '$location', '$location_cookie', '$current_address', '$lsd', '$status', '$volume', '$ownership_status', '$quote_description', '$notes', '$cviprenewal', '$insurance_card', '$registration_card', '$region', '$classification', '$vehicle_access_code', '$cargo', '$lessor', '$group', '$use', '$staff')";
        $result_insert_equipment = mysqli_query($dbc, $query_insert_equipment);
		    $equipmentid = mysqli_insert_id($dbc);
        $url = 'Added';
        $before_change = '';
        $history = "New equipment Added. <br />";
        add_update_history($dbc, 'equipment_history', $history, '', $before_change);
    } else {
        $equipmentid = $_POST['equipmentid'];
        $query_update_equipment = "UPDATE `equipment` SET `equ_description` = '$equ_description', `assigned_status`='$assigned_status', `category` = '$category', `type` = '$type', `make` = '$make', `model` = '$model', `submodel` = '$submodel', `model_year` = '$model_year', `label` = '$label', `total_kilometres` = '$total_kilometres', `leased` = '$leased', `style` = '$style', `vehicle_size` = '$vehicle_size', `color` = '$color', `trim` = '$trim', `fuel_type` = '$fuel_type', `tire_type` = '$tire_type', `drive_train` = '$drive_train', `serial_number` = '$serial_number', `part_serial_names` = '$part_serial_names', `part_serials` = '$part_serials', `unit_number` = '$unit_number', `vin_number` = '$vin_number', `licence_plate` = '$licence_plate', `nickname` = '$nickname', `year_purchased` = '$year_purchased', `mileage` = '$mileage', `hours_operated` = '$hours_operated', `cost` = '$cost', `cnd_cost_per_unit` = '$cnd_cost_per_unit', `usd_cost_per_unit` = '$usd_cost_per_unit', `finance` = '$finance', `lease` = '$lease', `insurance` = '$insurance', `insurance_contact` = '$insurance_contact', `insurance_phone` = '$insurance_phone', `hourly_rate` = '$hourly_rate', `daily_rate` = '$daily_rate', `semi_monthly_rate` = '$semi_monthly_rate', `monthly_rate` = '$monthly_rate', `field_day_cost` = '$field_day_cost', `field_day_billable` = '$field_day_billable', `hr_rate_work` = '$hr_rate_work', `hr_rate_travel` = '$hr_rate_travel', `follow_up_date` = '$follow_up_date', `follow_up_staff` = '$follow_up_staff', `next_service_date` = '$next_service_date', `next_service` = '$next_service', `next_serv_desc` = '$next_serv_desc', `service_location` = '$service_location', `last_oil_filter_change_date` = '$last_oil_filter_change_date', `last_oil_filter_change` = '$last_oil_filter_change', `last_oil_filter_change_hrs` = '$last_oil_filter_change_hrs', `next_oil_filter_change_date` = '$next_oil_filter_change_date', `next_oil_filter_change` = '$next_oil_filter_change', `next_oil_filter_change_hrs` = '$next_oil_filter_change_hrs', `last_insp_tune_up_date` = '$last_insp_tune_up_date', `last_insp_tune_up` = '$last_insp_tune_up', `last_insp_tune_up_hrs` = '$last_insp_tune_up_hrs', `next_insp_tune_up_date` = '$next_insp_tune_up_date', `next_insp_tune_up` = '$next_insp_tune_up', `next_insp_tune_up_hrs` = '$next_insp_tune_up_hrs', `tire_condition` = '$tire_condition', `last_tire_rotation_date` = '$last_tire_rotation_date', `last_tire_rotation` = '$last_tire_rotation', `last_tire_rotation_hrs` = '$last_tire_rotation_hrs', `next_tire_rotation_date` = '$next_tire_rotation_date', `next_tire_rotation` = '$next_tire_rotation', `next_tire_rotation_hrs` = '$next_tire_rotation_hrs', `reg_renewal_date` = '$reg_renewal_date', `insurance_renewal` = '$insurance_renewal', `location` = '$location', `location_cookie` = '$location_cookie', `current_address` = '$current_address', `lsd` = '$lsd', `status` = '$status', `volume` = '$volume', `ownership_status` = '$ownership_status', `quote_description` = '$quote_description', `notes` = '$notes', `cvip_renewal_date` = '$cviprenewal', `insurance_file`='$insurance_card', `registration_file`='$registration_card', `region`='$region', `classification` = '$classification', `vehicle_access_code` = '$vehicle_access_code', `cargo` = '$cargo', `lessor` = '$lessor', `group` = '$group', `use` = '$use', `staffid` = '$staff' WHERE `equipmentid` = '$equipmentid'";
        $result_update_equipment	= mysqli_query($dbc, $query_update_equipment);
        $url = 'Updated';
        $before_change = '';
        $history = "Equipment is been updated. <br />";
        add_update_history($dbc, 'equipment_history', $history, '', $before_change);
    }
    if($_POST['equipment_image_delete'] == 1) {
        $before_change = capture_before_change($dbc, 'equipment', 'equipment_image', 'equipmentid', $equipmentid);
        mysqli_query($dbc, "UPDATE `equipment` SET `equipment_image` = '' WHERE `equipmentid` = '$equipmentid'");
        $history = capture_after_change('equipment_image', '');
        add_update_history($dbc, 'equipment_history', $history, '', $before_change);
    }
    if(!empty($_FILES['equipment_image']['name'])) {
        $filename = $_FILES['equipment_image']['name'];
        $file = $_FILES['equipment_image']['tmp_name'];
        if (!file_exists('download')) {
            mkdir('download', 0777, true);
        }
        $basefilename = $filename = preg_replace('/[^A-Za-z0-9\.]/','_',$filename);
        $i = 0;
        while(file_exists('download/'.$filename)) {
            $filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basefilename);
        }
        move_uploaded_file($file, "download/".$filename);
        $equipment_image = $filename;

        $before_change = capture_before_change($dbc, 'equipment', 'equipment_image', 'equipmentid', $equipmentid);
        mysqli_query($dbc, "UPDATE `equipment` SET `equipment_image` = '$filename' WHERE `equipmentid` = '$equipmentid'");
        $history = capture_after_change('equipment_image', $filename);
        add_update_history($dbc, 'equipment_history', $history, '', $before_change);
    }

	if(!empty($_POST['reg_reminder_staff'])) {
		$contactid = implode(',',$_POST['reg_reminder_staff']);
		$reminder_date = filter_var($_POST['reg_reminder_date'],FILTER_SANITIZE_STRING);
		$reminder_time = '08:00:00';
		$reminder_type = 'Equipment Registration';
		$verify = 'equipment#*#reg_renewal_date#*#equipmentid#*#'.$equipmentid.'#*#'.$reg_renewal_date.'#*#GREATER';
		$subject = get_config($dbc, 'equipment_remind_subject');
		$body = filter_var(htmlentities(html_entity_decode(get_config($dbc, 'equipment_remind_body')).'<br />Click <a href="'.WEBSITE_URL.'/Equipment/add_equipment.php?equipmentid='.$equipmentid.'">here</a> to view the equipment.'),FILTER_SANITIZE_STRING);
		$sender = get_config($dbc, 'equipment_remind_sender');
		if($sender == '') {
			$sender = get_email($dbc, $_SESSION['contactid']);
		}
        mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$contactid' AND `src_table` = 'equipment_registration' AND `src_tableid` = '$equipmentid'");
		mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `recipient`, `reminder_date`, `reminder_time`, `reminder_type`, `verify`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
			VALUES ('$contactid', '', '$reminder_date', '$reminder_time', '$reminder_type', '$verify', '$subject', '$body', '$sender', 'equipment_registration', '$equipmentid')");
	}
	if(!empty($_POST['ins_reminder_staff'])) {
		$contactid = implode(',',$_POST['ins_reminder_staff']);
		$reminder_date = filter_var($_POST['ins_reminder_date'],FILTER_SANITIZE_STRING);
		$reminder_time = '08:00:00';
		$reminder_type = 'Equipment Insurance';
		$verify = 'equipment#*#insurance_renewal#*#equipmentid#*#'.$equipmentid.'#*#'.$reg_renewal_date.'#*#GREATER';
		$subject = get_config($dbc, 'equipment_remind_subject');
		$body = filter_var(htmlentities(html_entity_decode(get_config($dbc, 'equipment_remind_body')).'<br />Click <a href="'.WEBSITE_URL.'/Equipment/add_equipment.php?equipmentid='.$equipmentid.'">here</a> to view the equipment.'),FILTER_SANITIZE_STRING);
		$sender = get_config($dbc, 'equipment_remind_sender');
		if($sender == '') {
			$sender = get_email($dbc, $_SESSION['contactid']);
		}
        mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$contactid' AND `src_table` = 'equipment_insurance' AND `src_tableid` = '$equipmentid'");
		mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `recipient`, `reminder_date`, `reminder_time`, `reminder_type`, `verify`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
			VALUES ('$contactid', '', '$reminder_date', '$reminder_time', '$reminder_type', '$verify', '$subject', '$body', '$sender', 'equipment_insurance', '$equipmentid')");
	}

	// if($_POST['submit'] == 'Submit') {
	// 	echo '<script type="text/javascript"> window.location.replace("?category='.$category.'"); </script>';
	// } else {
	// 	ob_clean();
	// 	header('Location:'.$_POST['submit'].'?equipmentid='.$equipmentid);
	// }

   // mysqli_close($dbc); //Close the DB Connection
}

?>
<script type="text/javascript">
$(document).ready(function () {
    <?php if($_GET['view'] == 'readonly') { ?>
        destroyInputs();
        $('.chosen-select-deselect,input[type=text]').each(function() {
            $(this).after($(this).find('option:selected').text());
        }).removeClass('chosen-select-deselect').hide();
        $('input[type=text]').each(function() {
            $(this).after($(this).val());
        }).hide();
        $('input[type=file]').hide();
        $('img[src*=ROOK-add-icon],img[src*=remove]').hide();
    <?php } ?>
	<?php if(isset($_GET['target_field'])) { ?>
		$('[name=<?= $_GET['target_field'] ?>]').closest('.panel').find('a[href^=#collapse_]').click();
		$('[name=<?= $_GET['target_field'] ?>]').focus();
	<?php } ?>

	$("#form1").submit(function( event ) {
        var category = $("[name=category]").last().val();
		var sub_category = $("#sub_category").val();

		var code = $("input[name=code]").val();
		var name = $("input[name=name]").val();
		var category_name = $("input[name=category_name]").val();
		var sub_category_name = $("input[name=sub_category_name]").val();

		if ((code != undefined && code == '') || (category != undefined && category == '') || (sub_category != undefined && sub_category == '') || (name != undefined && name == '')) {
			alert("Please make sure you have filled in all of the required fields.");
			return false;
		}
		if(((category == 'Other') && (category_name == '')) || ((sub_category == 'Other') && (sub_category_name == ''))) {
			//alert("Please make sure you have filled in all of the required fields.");
			//return false;
		}
	});

	$("#make").change(function() {
		if($( "#make option:selected" ).text() == 'Other') {
				$( "#make_name" ).closest('.form-group').show();
		} else {
			$( "#make_name" ).closest('.form-group').hide();
		}
	});
	$("#model").change(function() {
		if($( "#model option:selected" ).text() == 'Other') {
				$( "#model_other" ).closest('.form-group').show();
				$( "#model_other" ).removeAttr('disabled').focus();
		} else {
			$( "#model_other" ).attr('disabled','disabled');
			$( "#model_other" ).closest('.form-group').hide();
		}
	});
	$("#color").change(function() {
		if($( "#color option:selected" ).text() == 'Other') {
				$( "#color_other" ).closest('.form-group').show();
				$( "#color_other" ).removeAttr('disabled').focus();
		} else {
			$( "#color_other" ).attr('disabled','disabled');
			$( "#color_other" ).closest('.form-group').hide();
		}
	});
	$("#ownership_status").change(function() {
		if($( "#ownership_status option:selected" ).text() == 'Other') {
				$( "#new_ownership_status" ).show();
		} else {
			$( "#new_ownership_status" ).hide();
		}
	});
	$("#type").change(function() {
		if($( "#type option:selected" ).text() == 'Other') {
				$( "#type_name" ).show();
		} else {
			$( "#type_name" ).hide();
		}
	});
    $("#cargo").change(function() {
        if($( "#cargo option:selected" ).text() == 'Other') {
                $( "#new_cargo" ).show();
        } else {
            $( "#new_cargo" ).hide();
        }
    });
    $("#lessor").change(function() {
        if($( "#lessor option:selected" ).text() == 'Other') {
                $( "#new_lessor" ).show();
        } else {
            $( "#new_lessor" ).hide();
        }
    });
    $("#group").change(function() {
        if($( "#group option:selected" ).text() == 'Other') {
                $( "#new_group" ).show();
        } else {
            $( "#new_group" ).hide();
        }
    });
    $("#use").change(function() {
        if($( "#use option:selected" ).text() == 'Other') {
                $( "#new_use" ).show();
        } else {
            $( "#new_use" ).hide();
        }
    });

    // $("[name=region]").change(function() {
    //     $('[name=location]').val('');
    //     if(this.value != '') {
    //         $('[name=location]').find('option').hide();
    //         $('[name=location]').find('[data-region="'+this.value+'"]').show();
    //     } else {
    //         $('[name=location]').find('option').show();
    //     }
    //     $('[name=location]').trigger('change.select2');
    // });
    // $("[name=location]").change(function() {
    //     if(this.value != '') {
    //         $('[name=region]').val($(this).find('option:selected').data('region')).trigger('change.select2');
    //     }
    // });

    // Active tabs
    $('[data-tab-target]').click(function() {
        $('.main-screen .main-screen').scrollTop($('#tab_section_'+$(this).data('tab-target')).offset().top + $('.main-screen .main-screen').scrollTop() - $('.main-screen .main-screen').offset().top);
        return false;
    });
    setTimeout(function() {
        $('.main-screen .main-screen').scroll(function() {
            var screenTop = $('.main-screen .main-screen').offset().top + 10;
            var screenHeight = $('.main-screen .main-screen').innerHeight();
            $('.active.blue').removeClass('active blue');
            $('.tab-section').filter(function() { return $(this).offset().top + this.clientHeight > screenTop && $(this).offset().top < screenTop + screenHeight; }).each(function() {
                $('[data-tab-target='+$(this).attr('id').replace('tab_section_','')+']').find('li').addClass('active blue');
            });
        });
        $('.main-screen .main-screen').scroll();
    }, 500);
});

</script>
<?php $equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$category = $_GET['category'];
if(empty($category)) {
	$category = explode(',', trim(get_config($dbc, 'equipment_tabs'),','))[0];
}
include_once('../Equipment/region_location_access.php');

$equ_description = '';
$type = '';
$make = '';
$model = '';
$submodel = '';
$model_year = '';
$label = '';
$total_kilometres = '';
$leased = '';
$style = '';
$vehicle_size = '';
$color = '';
$trim = '';
$fuel_type = '';
$tire_type = '';
$drive_train = '';
$serial_number = '';
$part_serial_names = '';
$part_serials = '';
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
$insurance_card = '';
$hourly_rate = '';
$daily_rate = '';
$semi_monthly_rate = '';
$monthly_rate = '';
$field_day_cost = '';
$field_day_billable = '';
$hr_rate_work = '';
$hr_rate_travel = '';
$follow_up_date = '';
$follow_up_staff = '';
$next_service_date = '';
$next_service = '';
$next_serv_desc = '';
$service_location = '';
$last_oil_filter_change_date = '';
$last_oil_filter_change = '';
$last_oil_filter_change_hrs = '';
$next_oil_filter_change_date = '';
$next_oil_filter_change = '';
$next_oil_filter_change_hrs = '';
$last_insp_tune_up_date = '';
$last_insp_tune_up = '';
$last_insp_tune_up_hrs = '';
$next_insp_tune_up_date = '';
$next_insp_tune_up = '';
$next_insp_tune_up_hrs = '';
$tire_condition = '';
$last_tire_rotation_date = '';
$last_tire_rotation = '';
$last_tire_rotation_hrs = '';
$next_tire_rotation_date = '';
$next_tire_rotation = '';
$next_tire_rotation_hrs = '';
$registration_card = '';
$reg_renewal_date = '';
$insurance_renewal = '';
$region = '';
$location = '';
$classification = '';
$current_address = '';
$lsd = '';
$status = '';
$volume = '';
$ownership_status = '';
$assigned_status = '';
$quote_description = '';
$notes = '';
$cviprenewal = '';
$vehicle_access_code = '';
$cargo = '';
$lessor = '';
$group = '';
$use = '';
$staff = '';
$equipment_image = '';
$total_billed = 0;
$total_hours = 0;
$total_expenses = 0;

if(!empty($_GET['edit']))   {

    $equipmentid = $_GET['edit'];
	$get_equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT `equipment`.*, SUM(`equipment_expenses`.`total`) expense_total, `invoiced_hours`, `invoiced_amt`, 0 `invoiced_daily` FROM `equipment` LEFT JOIN (SELECT `item_id` `equipmentid`, SUM(`hours_estimated`) `invoiced_hours`, SUM(`hours_estimated` * `rate`) `invoiced_amt` FROM `ticket_attached` WHERE `src_table` LIKE 'equipment' AND `deleted`=0 GROUP BY `item_id`) `invoiced` ON `equipment`.`equipmentid`=`invoiced`.`equipmentid` LEFT JOIN `equipment_expenses` ON `equipment`.`equipmentid`=`equipment_expenses`.`equipmentid` AND `equipment_expenses`.`status` != 'Rejected' WHERE `equipment`.`equipmentid`='".$equipmentid."'"));

    $equ_description = $get_equipment['equ_description'];
    $category = $get_equipment['category'];
    $type = $get_equipment['type'];
    $make = $get_equipment['make'];
    $model = $get_equipment['model'];
    $submodel = $get_equipment['submodel'];
    $model_year = $get_equipment['model_year'];
    $label = $get_equipment['label'];
    $total_kilometres = $get_equipment['total_kilometres'];
    $leased = $get_equipment['leased'];
    $style = $get_equipment['style'];
    $vehicle_size = $get_equipment['vehicle_size'];
    $color = $get_equipment['color'];
    $trim = $get_equipment['trim'];
    $fuel_type = $get_equipment['fuel_type'];
    $tire_type = $get_equipment['tire_type'];
    $drive_train = $get_equipment['drive_train'];
    $serial_number = $get_equipment['serial_number'];
    $part_serial_names = $get_equipment['part_serial_names'];
    $part_serials = $get_equipment['part_serials'];
    $unit_number = $get_equipment['unit_number'];
    $vin_number = $get_equipment['vin_number'];
    $licence_plate = $get_equipment['licence_plate'];
    $nickname = $get_equipment['nickname'];
    $year_purchased = $get_equipment['year_purchased'];
    $mileage = $get_equipment['mileage'];
    $hours_operated = $get_equipment['hours_operated'];
    $cost = $get_equipment['cost'];
    $cnd_cost_per_unit = $get_equipment['cnd_cost_per_unit'];
    $usd_cost_per_unit = $get_equipment['usd_cost_per_unit'];
    $finance = $get_equipment['finance'];
    $lease = $get_equipment['lease'];
    $insurance = $get_equipment['insurance'];
    $insurance_contact = $get_equipment['insurance_contact'];
    $insurance_phone = $get_equipment['insurance_phone'];
    $insurance_card = $get_equipment['insurance_file'];
    $hourly_rate = $get_equipment['hourly_rate'];
    $daily_rate = $get_equipment['daily_rate'];
    $semi_monthly_rate = $get_equipment['semi_monthly_rate'];
    $monthly_rate = $get_equipment['monthly_rate'];
    $field_day_cost = $get_equipment['field_day_cost'];
    $field_day_billable = $get_equipment['field_day_billable'];
    $hr_rate_work = $get_equipment['hr_rate_work'];
    $hr_rate_travel = $get_equipment['hr_rate_travel'];
    $follow_up_date = $get_equipment['follow_up_date'];
    $follow_up_staff = $get_equipment['follow_up_staff'];
    $next_service_date = $get_equipment['next_service_date'];
    $next_service = $get_equipment['next_service'];
    $next_serv_desc = $get_equipment['next_serv_desc'];
    $service_location = $get_equipment['service_location'];
    $last_oil_filter_change_date = $get_equipment['last_oil_filter_change_date'];
    $last_oil_filter_change = $get_equipment['last_oil_filter_change'];
    $last_oil_filter_change_hrs = $get_equipment['last_oil_filter_change_hrs'];
    $next_oil_filter_change_date = $get_equipment['next_oil_filter_change_date'];
    $next_oil_filter_change = $get_equipment['next_oil_filter_change'];
    $next_oil_filter_change_hrs = $get_equipment['next_oil_filter_change_hrs'];
    $last_insp_tune_up_date = $get_equipment['last_insp_tune_up_date'];
    $last_insp_tune_up = $get_equipment['last_insp_tune_up'];
    $last_insp_tune_up_hrs = $get_equipment['last_insp_tune_up_hrs'];
    $next_insp_tune_up_date = $get_equipment['next_insp_tune_up_date'];
    $next_insp_tune_up = $get_equipment['next_insp_tune_up'];
    $next_insp_tune_up_hrs = $get_equipment['next_insp_tune_up_hrs'];
    $tire_condition = $get_equipment['tire_condition'];
    $last_tire_rotation_date = $get_equipment['last_tire_rotation_date'];
    $last_tire_rotation = $get_equipment['last_tire_rotation'];
    $last_tire_rotation_hrs = $get_equipment['last_tire_rotation_hrs'];
    $next_tire_rotation_date = $get_equipment['next_tire_rotation_date'];
    $next_tire_rotation = $get_equipment['next_tire_rotation'];
    $next_tire_rotation_hrs = $get_equipment['next_tire_rotation_hrs'];
    $registration_card = $get_equipment['registration_file'];
    $reg_renewal_date = $get_equipment['reg_renewal_date'];
    $insurance_renewal = $get_equipment['insurance_renewal'];
    $region = $get_equipment['region'];
    $location = $get_equipment['location'];
    $location_cookie = $get_equipment['location_cookie'];
    $classification = $get_equipment['classification'];
    $current_address = $get_equipment['current_address'];
    $lsd = $get_equipment['lsd'];
    $status = $get_equipment['status'];
    $volume = $get_equipment['volume'];
    $ownership_status = $get_equipment['ownership_status'];
    $assigned_status = $get_equipment['assigned_status'];
    $quote_description = $get_equipment['quote_description'];
    $notes = $get_equipment['notes'];
    $cviprenewal = $get_equipment['cvip_renewal_date'];
    $vehicle_access_code = $get_equipment['vehicle_access_code'];
    $cargo = $get_equipment['cargo'];
    $lessor = $get_equipment['lessor'];
    $group = $get_equipment['group'];
    $use = $get_equipment['use'];
    $staff = $get_equipment['staffid'];
    $equipment_image = $get_equipment['equipment_image'];

	$total_billed = $get_equipment['invoiced_amt'];
	$total_hours = $get_equipment['invoiced_hours'];
	$total_expenses = $get_equipment['expense_total'];
}

$accordion_list = [];
$query = mysqli_query($dbc,"SELECT accordion FROM field_config_equipment WHERE  tab='$category' AND accordion IS NOT NULL AND `order` IS NOT NULL ORDER BY `order`");
if(mysqli_num_rows($query) == 0) {
    $query = mysqli_query($dbc,"SELECT accordion FROM field_config_equipment WHERE  tab='' AND accordion IS NOT NULL AND `order` IS NOT NULL ORDER BY `order`");
}
while($row = mysqli_fetch_array($query)) {
    $accordion_list[] = $row['accordion'];
}
?>

<?php if($_GET['iframe_slider'] != 1) { ?>
    <div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
        <ul>
            <a href="?category=<?= $category ?>"><li>Back to Dashboard</li></a>
            <?php foreach($accordion_list as $accordion) { ?>
                <a href="" data-tab-target="<?= config_safe_str($accordion) ?>"><li><?= $accordion ?></li></a>
            <?php } ?>
        </ul>
    </div>
<?php } ?>

<div class="scale-to-fill has-main-screen" style="overflow: hidden;">
    <div class="main-screen standard-body form-horizontal">
        <div class="standard-body-title">
            <h3><?= $_GET['subtab'] == 'overview' ? 'Equipment Overview: Unit #'.$unit_number : (!empty($_GET['edit']) ? 'Edit Equipment: Unit #'.$unit_number : 'Add New Equipment') ?>
            <?php if(IFRAME_PAGE) { ?>
                <a href="../blank_loading_page.php" class="pull-right"><img src="../img/icons/cancel.png" class="inline-img"></a>
            <?php } ?>
            <?php if(vuaed_visible_function($dbc, 'equipment') && $_GET['view'] == 'readonly') { ?>
                <a href="index.php?edit=<?= $_GET['edit'] ?>&subtab=edit" <?= IFRAME_PAGE ? 'target="_blank"' : '' ?> class="pull-right"><img src="../img/icons/ROOK-edit-icon.png" class="inline-img no-toggle" title="Edit Equipment"></a>
            <?php } ?>
            <?php $quick_actions = explode(',',get_config($dbc, 'equipment_quick_action_icons'));
            if($equipmentid > 0) {
                echo '<span class="pull-right action-icons  ">';
                echo in_array('reminder', $quick_actions) ? '<span title="Schedule Reminder" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/quick_action_reminders.php?tile=equipment&id='.$equipmentid.'\'); return false;"><img title="Schedule Reminder" src="../img/icons/ROOK-reminder-icon.png" class="inline-img no-toggle" onclick="return false;"></span>' : '';
                echo in_array('archive', $quick_actions) && vuaed_visible_function($dbc, 'equipment') == 1 ? '<span title="Archive Equipment"><a href="'.WEBSITE_URL.'/delete_restore.php?action=delete&equipmentid='.$equipmentid.'" onclick="return confirm(\'Are you sure?\');"><img src="../img/icons/trash-icon-red.png" title="Archive Equipment" class="inline-img no-toggle"></a></span>' : '';
                echo '</span>';
            } ?>
            <?php if(IFRAME_PAGE) { ?>
                <a href="index.php?edit=<?= $_GET['edit'] ?>&subtab=<?= empty($_GET['subtab']) ? 'edit' : $_GET['subtab'] ?>&view=<?= $_GET['view'] ?>" target="_top" class="pull-right"><img src="../img/icons/ROOK-FullScreen-icon.png" class="inline-img no-toggle" title="Open Full Screen"></a>
            <?php } ?></h3>
        </div>

        <div class="standard-body-content" style="padding: 0.5em;">
    		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

                <input type="hidden" id="equipmentid"   name="equipmentid" value="<?php echo $equipmentid ?>" />
                <input type="hidden" id="category"	name="category" value="<?php echo $category ?>" />

                <!-- List fields that are updated on other pages -->
                <input name="mileage" type="hidden" value="<?= $mileage ?>" />
                <input name="hours_operated" type="hidden" value="<?= $hours_operated ?>" />
                <input name="last_oil_filter_change_date" type="hidden" value="<?= $last_oil_filter_change_date ?>" />
                <input name="last_oil_filter_change" type="hidden" value="<?= $last_oil_filter_change ?>" />
                <input name="last_oil_filter_change_hrs" type="hidden" value="<?= $last_oil_filter_change_hrs ?>" />
                <input name="next_oil_filter_change_date" type="hidden" value="<?= $next_oil_filter_change_date ?>" />
                <input name="next_oil_filter_change" type="hidden" value="<?= $next_oil_filter_change ?>" />
                <input name="next_oil_filter_change_hrs" type="hidden" value="<?= $next_oil_filter_change_hrs ?>" />
                <input name="last_insp_tune_up_date" type="hidden" value="<?= $last_insp_tune_up_date ?>" />
                <input name="last_insp_tune_up" type="hidden" value="<?= $last_insp_tune_up ?>" />
                <input name="last_insp_tune_up_hrs" type="hidden" value="<?= $last_insp_tune_up_hrs ?>" />
                <input name="next_insp_tune_up_date" type="hidden" value="<?= $next_insp_tune_up_date ?>" />
                <input name="next_insp_tune_up" type="hidden" value="<?= $next_insp_tune_up ?>" />
                <input name="next_insp_tune_up_hrs" type="hidden" value="<?= $next_insp_tune_up_hrs ?>" />
                <input name="last_tire_rotation_date" type="hidden" value="<?= $last_tire_rotation_date ?>" />
                <input name="last_tire_rotation" type="hidden" value="<?= $last_tire_rotation ?>" />
                <input name="last_tire_rotation_hrs" type="hidden" value="<?= $last_tire_rotation_hrs ?>" />
                <input name="next_tire_rotation_date" type="hidden" value="<?= $next_tire_rotation_date ?>" />
                <input name="next_tire_rotation" type="hidden" value="<?= $next_tire_rotation ?>" />
                <input name="next_tire_rotation_hrs" type="hidden" value="<?= $next_tire_rotation_hrs ?>" />

                <?php if($_GET['subtab'] == 'overview') {
                    $value_config = get_config($dbc, 'equipment_overview_'.config_safe_str($category));
                    include ('add_equipment_fields.php');
                } else {
                    foreach($accordion_list as $accordion) { ?>
                        <div id="tab_section_<?= config_safe_str($accordion) ?>" class="tab-section col-sm-12">
                            <h4><?= $accordion ?></h4>
                            <?php $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='$category' AND accordion='$accordion'"));
                            if(empty($get_field_config['equipment'])) {
                                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment FROM field_config_equipment WHERE tab='' AND accordion='$accordion'"));
                            }
                            $value_config = ','.$get_field_config['equipment'].',';

                            include ('add_equipment_fields.php'); ?>
                            <div class="clearfix"></div><hr>
                        </div>
                    <?php }
                } ?>

                <?php if($_GET['view'] !== 'readonly') { ?>
                    <div class="form-group">
                        <div class="col-sm-6">
                            <p><span class="brand-color"><em>Required Fields *</em></span></p>
                        </div>
                        <div class="col-sm-6">
                            <div class="pull-right">
                                <a href="?category=<?= $category ?>" class="btn brand-btn">Back</a>
                                <button type="submit" name="submit" value="Submit" class="btn brand-btn" onclick="return presave();">Submit</button>
                            </div>
                        </div>
                    </div>
                <?php } ?>

    		</form>
        </div>
    </div>
</div>
<script>
  function presave() {
    var flag = 0;
    var firsttarget = '';
  	$('.mandatory').each(function() {
  			var target = this;
  				if($(target).val() != null && $(target).val().length === 0) {
            if(flag == 0) {
              $firsttarget = $(this);
            }

  					if($(target).is('select')) {
  						var select2 = $(target).next('.select2');
  						$(select2).find('.select2-selection').css('background-color', 'red');
  						$(select2).find('.select2-selection__placeholder').css('color', 'white');
  					} else {
  						$(target).css('background-color', 'red');
  					}

            flag = 1;
  			}
        else {
          if($(target).is('select')) {
            var select2 = $(target).next('.select2');
            $(select2).find('.select2-selection').css('background-color', 'white');
          } else {
            $(target).css('background-color', 'white');
          }
        }
  	});

    var currenttop = $firsttarget.offset().top;
    if(flag == 1) {
        alert("Please fill in the required fields");
        $('.main-screen .main-screen').scrollTop($('.standard-body-content').scrollTop() + currenttop - $('.standard-body-content').offset().top - 30);
        return false;
    }

    return true;
  }
</script>
