<?php include_once('../include.php');
error_reporting(0);
checkAuthorised('calendar_rook');
if (isset($_POST['submit'])) {
    $today_date = date('Y-m-d');
    $patientid = filter_var($_POST['appt_patientid'],FILTER_SANITIZE_STRING);
    $injuryid = filter_var($_POST['appt_injuryid'],FILTER_SANITIZE_STRING);
    $therapistsid = filter_var(implode('*#*',$_POST['appt_therapistsid']),FILTER_SANITIZE_STRING);
    $serviceid = filter_var(implode('*#*',$_POST['serviceid']),FILTER_SANITIZE_STRING);
    $type = filter_var(implode('*#*',$_POST['appt_type']),FILTER_SANITIZE_STRING);
	$appointment_starts = [];
	$appointment_ends = [];
	foreach($_POST['appt_appoint_date'] as $i => $start) {
		$appointment_starts[] = date('Y-m-d H:i:s', strtotime($start));
		$appointment_ends[] = date('Y-m-d H:i:s', strtotime($_POST['appt_end_appoint_date'][$i]));
	}
    $appoint_date = filter_var(implode('*#*',$appointment_starts),FILTER_SANITIZE_STRING);
    $end_appoint_date = filter_var(implode('*#*',$appointment_ends),FILTER_SANITIZE_STRING);
    $follow_up_call_status = filter_var($_POST['appt_follow_up_call_status'],FILTER_SANITIZE_STRING);
    $notes = filter_var(htmlentities($_POST['appt_notes']),FILTER_SANITIZE_STRING);

    if ($patientid == 'NEW_CLIENT') {
        $client_type = mysqli_fetch_array(mysqli_query($dbc, "SELECT `client_type` FROM `field_config_calendar_booking`"))['client_type'];

        $new_first_name = encryptIt(filter_var($_POST['new_client_firstname'],FILTER_SANITIZE_STRING));
        $new_last_name = encryptIt(filter_var($_POST['new_client_lastname'],FILTER_SANITIZE_STRING));
        $new_home_phone = encryptIt(filter_var($_POST['new_client_homephone'],FILTER_SANITIZE_STRING));
        $new_cell_phone = encryptIt(filter_var($_POST['new_client_cellphone'],FILTER_SANITIZE_STRING));
        $new_business_phone = encryptIt(filter_var($_POST['new_client_businessphone'],FILTER_SANITIZE_STRING));
        $new_email_address = encryptIt(filter_var($_POST['new_client_email'],FILTER_SANITIZE_STRING));

        //All contact tiles in software
        $contact_tiles = ['contacts' => 'contacts', 'contacts_inbox' => 'contacts', 'contacts3' => 'contacts3', 'contacts_rolodex' => 'contactsrolodex', 'client_info' => 'clientinfo', 'members' => 'members', 'vendors' => 'vendors'];

        //Check if the contact tiles are enabled
        $enabled_contact_tiles = [];
        foreach($contact_tiles as $contact_tile => $tile_folder_name) {
            if(tile_enabled($contact_tile) && !in_array($tile_folder_name, $enabled_contact_tiles)) {
                $enabled_contact_tiles[] = $tile_folder_name;
            }
        }

        //If there are more than one enabled contact tiles, check if the category exists in the field config of that contact tile. If the category doesn't exist, remove the tile from the list. Check backwards as we want to remove from the end of the array first as these are the lower priority contact tiles. If we hit a count of 1 enabled tile, the loop will end and we will insert into that tile.
        if(count($enabled_contact_tiles) > 1) {
            for($tile_count = (count($enabled_contact_tiles) - 1); $tile_count >= 0 && count($enabled_contact_tiles) > 1; $tile_count--) {
                $tile_config = get_config($dbc, $enabled_contact_tiles[$tile_count].'_tabs');
                if(strpos(','.$tile_config.',', ','.$client_type.',') === FALSE) {
                    unset($enabled_contact_tiles[$tile_count]);
                }
            }
        }

        //If count of the enabled tiles after the category check is still greater than 1, we will insert it into the first value in the array, if there are only 1 enabled tiles remaining then we will insert into that tile. If no tiles are enabled then it will isnert into the contacts tile.
        if(count($enabled_contact_tiles) >= 1) {
            $insert_contact_tile = $enabled_contact_tiles[0];
        } else {
            $insert_contact_tile = 'contacts';
        }

        mysqli_query($dbc, "INSERT INTO `contacts` (`tile_name`, `category`, `first_name`, `last_name`, `home_phone`, `cell_phone`, `office_phone`, `email_address`) VALUES ('$insert_contact_tile', '$client_type', '$new_first_name', '$new_last_name', '$new_home_phone', '$new_cell_phone', '$new_office_phone', '$new_email_address')");
        $patientid = mysqli_insert_id($dbc);
    }

    if (empty($_POST['bookingid'])) {
        $query = "INSERT INTO `booking` (`today_date`, `patientid`, `therapistsid`, `injuryid`, `appoint_date`, `end_appoint_date`, `type`, `serviceid`, `follow_up_call_status`, `notes`) VALUES ('$today_date', '$patientid', '$therapistsid', '$injuryid', '$appoint_date', '$end_appoint_date', '$type', '$serviceid', '$follow_up_call_status', '$notes')";
        $result = mysqli_query($dbc, $query);
        $bookingid = mysqli_insert_id($dbc);
    } else {
        $bookingid = $_POST['bookingid'];
        $query = "UPDATE `booking` SET `patientid` = '$patientid', `therapistsid` = '$therapistsid', `injuryid` = '$injuryid', `appoint_date` = '$appoint_date', `end_appoint_date` = '$end_appoint_date', `type` = '$type', `serviceid` = '$serviceid', `follow_up_call_status` = '$follow_up_call_status', `notes` = '$notes' WHERE `bookingid` = '$bookingid'";
        $result = mysqli_query($dbc, $query);
    }

    $query = $_GET;
    unset ($query['bookingid']);
    $query['action'] = 'view';
    echo '<script> window.location.replace("?'.http_build_query($query).'&bookingid='.$bookingid.'");</script>';
}
if (isset($_POST['appt_status'])) {
    $bookingid = $_POST['bookingid'];
    $current_time = date('H:i:s');
    $follow_up_call_status = filter_var($_POST['appt_status'],FILTER_SANITIZE_STRING);

    $query = "UPDATE `booking` SET `appoint_time` = '$current_time', `follow_up_call_status` = '$follow_up_call_status' WHERE `bookingid` = '$bookingid'";
    $result = mysqli_query($dbc, $query);

    $get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_calendar_booking`"));
    $enabled_fields = ','.$get_field_config['enabled_fields'].',';
    if ($_POST['appt_status'] == 'Arrived' && strpos($enabled_fields, ',checkin,') !== FALSE) {
        $today_date = date('Y-m-d');
        $follow_up_call_status = 'Arrived';
        $appoint_date = get_patient_from_booking($dbc, $bookingid, 'appoint_date');
        $service_date = explode(' ', $appoint_date);
        $final_service_date = $service_date[0];

        $query_insert_invoice = "INSERT INTO invoice (`invoice_type`, `bookingid`, `injuryid`, `service_date`, `invoice_date`, `patientid`, `therapistsid`)
        SELECT  'Saved',
                $bookingid,
                injuryid,
                '$final_service_date',
                '$today_date',
                patientid,
                therapistsid
        from booking WHERE bookingid = '$bookingid'";
        $result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
    }
}
?>

<?php
$bookingid = '';
if ($_GET['bookingid'] == 'NEW') {
    $bookingid = '';
} else {
    $bookingid = $_GET['bookingid'];
}

$action = 'view';
if ($_GET['action'] == 'edit') {
    $action = 'edit';
}

$status_types = '';
$enabled_fields = '';
$client_type = '';
$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_calendar_booking`"));
if (!empty($get_field_config)) {
    $status_types = explode(',', $get_field_config['status_types']);
    $enabled_fields = ','.$get_field_config['enabled_fields'].',';
    $new_client_fields = ','.$get_field_config['new_client_fields'].',';
    $client_type = $get_field_config['client_type'];
}

$today_date = '';
$patientid = '';
if(!empty($_GET['patientid'])) {
    $patientid = $_GET['patientid'];
}
$therapistsid = '';
if(!empty($_GET['therapistsid'])) {
    $therapistsid = $_GET['therapistsid'];
}
$injuryid = '';
$appoint_date = '';
if(!empty($_GET['appoint_date'])) {
    $appoint_date = $_GET['appoint_date'];
}
if(!empty($_GET['end_appoint_date'])) {
    $end_appoint_date = $_GET['end_appoint_date'];
}
$type = '';
$follow_up_call_status = '';
$notes = '';
$invoiceid = 'new';
if (!empty($bookingid)) {
    $get_bookings = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `booking` WHERE `bookingid` = '$bookingid'"));

    $today_date = $get_bookings['today_date'];
    $patientid = $get_bookings['patientid'];
    $therapistsid = $get_bookings['therapistsid'];
    $injuryid = $get_bookings['injuryid'];
	$serviceids = $get_bookings['serviceid'];
    $appoint_date = $get_bookings['appoint_date'];
    $end_appoint_date = $get_bookings['end_appoint_date'];
    $type = $get_bookings['type'];
    $follow_up_call_status = $get_bookings['follow_up_call_status'];
    $notes = $get_bookings['notes'];
	
	$invoiceid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `invoiceid` FROM `invoice` WHERE `bookingid`='$bookingid'"))['invoicid'];
	$invoiceid = ($invoiceid > 0 ? $invoiceid : 'new');
}
?>
<script>
function bookingChange(bookingid) {
    <?php
        $query = $_GET;
        unset ($query['bookingid']);
    ?>
    if ($(bookingid).val() == 'NEW') {
        location.replace("?<?= http_build_query($query) ?>&bookingid=NEW");
    } else {
        location.replace("?<?= http_build_query($query) ?>&bookingid=" + $(bookingid).val());
    }
}

$(document).on('change.select2', 'select.changeClient', function() { changeClient(this); });
function changeClient(sel) {
    if(sel.value == 'NEW_CLIENT') {
        $('#new_client').show();
    } else {
        $('#new_client').hide();
    }
    retrieveInjuries(sel);
}

function retrieveInjuries(sel) {
    var patientId = $(sel).val();
    $.ajax({
        //url: 'calendar_ajax_all.php?fill=retrieve_injuries&patientid=' + patientId+'&offline='+offline_mode,
        url: 'calendar_ajax_all.php?fill=retrieve_injuries&patientid=' + patientId,
        method: 'GET',
        dataType: 'html',
        success: function(response) {
            $('[name="appt_injuryid"]').html(response);
            $('[name="appt_injuryid"]').trigger('change.select2');
        }
    });
}
function editBooking() {
    $("#appt_edit").show();
    $("#appt_view").hide();
    $("#appt_view_btns").hide();
    $("#submit_btn").show();
    $("#back_btn").show();
    $("#edit_btn").hide();
}
function backBooking() {
    $("#appt_edit").hide();
    $("#appt_view").show();
    $("#appt_view_btns").show();
    $("#submit_btn").hide();
    $("#back_btn").hide();
    $("#edit_btn").show();
}
function payNow(url) {
    window.top.location.href = url;
}
function deleteAppt() {
    if(confirm('Are you sure you want to delete this Appointment?')) {
        var bookingid = $('[name="bookingid"]').val();
        $.ajax({
            type: 'GET',
            //url: 'calendar_ajax_all.php?fill=delete_appt&bookingid='+bookingid+'&offline='+offline_mode,
            url: 'calendar_ajax_all.php?fill=delete_appt&bookingid='+bookingid,
            dataType: 'html',
            success: function(response) {
                location.reload();
            }
        });   
    }
}

$(document).on('change', 'select.filterService', function() { filterService(this); });
$(document).on('change', 'select[name="serviceid[]"]', function() { setService(this); });
function filterService(select) {
	var service = $(select).closest('label').next();
	service.find('option').show()
	if(select.value != '') {
		service.find('option').not('[data-category="'+select.value+'"]').hide()
	}
	//service.find('select').trigger('change.select2');
}
function setService(select) {
	$(select).closest('label').prev().find('select').val($(select).find('option:selected').data('category')).trigger('change.select2');
}
function addService() {
	var block = $('[name="serviceid[]"]').last().closest('.block-group');
	destroyInputs($('.block-group'));
	var clone = block.clone();
	clone.find('input,select').val('');
	clone.find('[name^=appt_appoint_date]').val(block.find('[name^=appt_end_appoint_date]').val());
	clone.find('[name^=appt_end_appoint_date]').val(block.find('[name^=appt_end_appoint_date]').val());
	block.after(clone);
	initInputs('.block-group');
}
function remService(img) {
	if($('[name="serviceid[]"]').length <= 1) {
		addService();
	}
	$(img).closest('.block-group').remove();
}
function checkCallBeforeBooking() {
    var bookings = [];
    $('[name="appt_therapistsid[]"]').each(function() {
        var staff = this.value;
        var startdate = $(this).closest('.block-group').find('[name="appt_appoint_date[]"]').val();
        var enddate = $(this).closest('.block-group').find('[name="appt_end_appoint_date[]"]').val();
        bookings.push({ staff: staff, startdate: startdate, enddate: enddate });
    });
    bookings = JSON.stringify(bookings);
    $.ajax({
        url: '../Calendar/calendar_ajax_all.php?fill=check_call_before_booking',
        type: 'POST',
        data: { bookings: bookings },
        success: function(response) {
            if(response != '') {
                alert(response);
            } else {
                return true;
            }
        }
    });
}
</script>

<h3 style="margin-left: 0.5em;">Appointment</h3>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<input type="hidden" name="bookingid" value="<?= $bookingid ?>">

<div id="appt_view_btns" class="no-resize block-group"<?= $action == 'view' ? '' : ' style="display:none;"' ?>>
    <button type="submit" name="appt_status" value="Arrived" class="block-label"<?= $follow_up_call_status == 'Arrived' ? ' style="opacity: 0.5;" disabled' : '' ?>>Arrived</button>
    <button type="submit" name="appt_status" value="Late Cancellation / No-Show" class="block-label"<?= $follow_up_call_status == 'Arrived' || $follow_up_call_status == 'Late Cancellation / No-Show' ? ' style="opacity: 0.5;" disabled' : '' ?>>No Show</button>
    <?php if (strpos($enabled_fields, ',checkin,') !== FALSE) { ?>
    <button type="submit" name="appt_status" value="Arrived" class="block-label"<?= $follow_up_call_status == 'Arrived' ? ' style="opacity: 0.5;" disabled' : '' ?>>Check In</button>
    <?php }
    if (strpos($enabled_fields, ',pos_basic,') !== false && check_subtab_persmission($dbc, 'pos', ROLE, 'sell') === true ) {
		$pos_layout	= get_config($dbc, 'pos_layout');
		$pos_url = ( $pos_layout=='touch' ) ? '../Point of Sale/pos_touch.php?bookingid='.$bookingid : '../Point of Sale/add_point_of_sell.php'; ?>
		<a href="" onclick="payNow('<?= $pos_url; ?>'); return false;" class="block-label pull-right"<?= $follow_up_call_status != 'Arrived' ? ' style="opacity: 0.5;" disabled' : '' ?>>Pay</a><?php
    } else if ((strpos($enabled_fields, ',pos,') !== false || strpos($enabled_fields, ',pos_touch,') !== false) && check_subtab_persmission($dbc, 'posadvanced', ROLE, 'sell') === true ) {
        if (strpos($enabled_fields, ',pos,') !== false) { ?>
            <a href="" onclick="payNow('../POSAdvanced/create_invoice.php?invoiceid=<?= $invoiceid ?>&bookingid=<?= $bookingid ?>'); return false;" class="block-label pull-right"<?= $follow_up_call_status != 'Arrived' ? ' style="opacity: 0.5;" disabled' : '' ?>>Pay</a><?php
        } else if (strpos($enabled_fields, ',pos_touch,') !== false) { ?>
            <a href="" onclick="payNow('../POSAdvanced/touch_main.php?invoiceid=<?= $invoiceid ?>&bookingid=<?= $bookingid ?>'); return false;" class="block-label pull-right"<?= $follow_up_call_status != 'Arrived' ? ' style="opacity: 0.5;" disabled' : '' ?>>Pay</a><?php
        }
    } else  if (check_subtab_persmission($dbc, 'check_out', ROLE, 'sell') === true ) { ?>
		<a href="" onclick="payNow('../Invoice/index.php?invoiceid=<?= $invoiceid ?>&bookingid=<?= $bookingid ?>'); return false;" class="block-label pull-right"<?= $follow_up_call_status != 'Arrived' ? ' style="opacity: 0.5;" disabled' : '' ?>>Pay</a><?php
    } ?>
</div>


<div id="appt_edit" class="block-group" style="height: calc(100% - 10em); overflow-y: auto;<?= $action == 'edit' ? '' : ' display:none;' ?>">

    <label for="patientid" class="super-label"><?= $client_type ?>
    <select data-placeholder="Select <?= $client_type ?>" name="appt_patientid" class="chosen-select-deselect changeClient">
        <option></option>
        <option value="NEW_CLIENT">New <?= $client_type ?></option>
        <?php
            $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = '".$client_type."' AND `deleted` = 0 AND `status` = 1".$region_query),MYSQLI_ASSOC));
            foreach ($query as $id) {
                echo '<option value="'.$id.'"'.($id == $patientid ? ' selected' : '').'>'.get_contact($dbc, $id).'</option>';
            }
        ?>
    </select></label>

    <div id="new_client" style="display: none;">
        <label for="new_client_firstname" class="super-label">First Name
            <input type="text" name="new_client_firstname" class="form-control">
        </label>
        <label for="new_client_lastname" class="super-label">Last Name
            <input type="text" name="new_client_lastname" class="form-control">
        </label>
        <?php if(strpos($new_client_fields, ',home_phone,') !== FALSE) { ?>
            <label for="new_client_homephone" class="super-label">Home Phone
                <input type="text" name="new_client_homephone" class="form-control">
            </label>
        <?php } ?>
        <?php if(strpos($new_client_fields, ',cell_phone,') !== FALSE) { ?>
            <label for="new_client_cellphone" class="super-label">Cell Phone
                <input type="text" name="new_client_cellphone" class="form-control">
            </label>
        <?php } ?>
        <?php if(strpos($new_client_fields, ',business_phone,') !== FALSE) { ?>
            <label for="new_client_businessphone" class="super-label">Business Phone
                <input type="text" name="new_client_businessphone" class="form-control">
            </label>
        <?php } ?>
        <?php if(strpos($new_client_fields, ',email_address,') !== FALSE) { ?>
            <label for="new_client_email" class="super-label">Email Address
                <input type="text" name="new_client_email" class="form-control">
            </label>
        <?php } ?>
    </div>

    <?php if (strpos($enabled_fields, ',injury,') !== FALSE) { ?>
    <label for="injuryid" class="super-label">Injury
    <select data-placeholder="Select Injury" name="appt_injuryid" class="chosen-select-deselect">
        <option></option>
        <?php
            $query = "SELECT * FROM `patient_injury` WHERE `contactid` = '$patientid' AND `discharge_date` IS NULL AND `deleted` = 0";
            $result = mysqli_query($dbc, $query);
            while ($row = mysqli_fetch_array($result)) {
                echo '<option value="'.$row['injuryid'].'"'.($row['injuryid'] == $injuryid ? ' selected' : '').'>'.$row['injury_name'].' : '.$row['injury_type'].' - '.$row['injury_date'].'</option>';
            }
        ?>
    </select></label>
    <?php } ?>

	<?php foreach(explode('*#*',$serviceids) as $i => $serviceid) {
		$service_staff = explode('*#*',$therapistsid)[$i];
		$service_type = explode('*#*',$type)[$i];
		$service_start = date('Y-m-d h:i a',strtotime(explode('*#*',$appoint_date)[$i]));
		$service_end = date('Y-m-d h:i a',strtotime(explode('*#*',$end_appoint_date)[$i])); ?>
		<div class="<?= strpos($enabled_fields, ',multiservices,') !== FALSE ? 'block-group' : '' ?>">
			<label for="therapistsid" class="super-label">Staff
			<select data-placeholder="Select Staff" name="appt_therapistsid[]" class="chosen-select-deselect">
				<option></option>
				<?php
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` = 1".$region_query),MYSQLI_ASSOC));
					foreach ($query as $id) {
						echo '<option value="'.$id.'"'.($id == $service_staff ? ' selected' : '').'>'.get_contact($dbc, $id).'</option>';
					}
				?>
			</select></label>

			<?php if (strpos($enabled_fields, ',services,') !== FALSE) {
				$service_info = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `serviceid`, `category` FROM `services` WHERE `serviceid`='$serviceid'"));?>
				<label for="service_cat" class="super-label">Service Category:
				<select data-placeholder="Select Service Category" name="service_cat" class="chosen-select-deselect filterService">
					<option></option>
					<?php $service_categories = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(category) FROM `services` WHERE `category` != '' AND `deleted`=0 ORDER BY `category`"),MYSQLI_ASSOC);
					foreach ($service_categories as $service_category) {
						echo '<option '.($service_info['category'] == $service_category['category'] ? 'selected' : '').' value="'.$service_category['category'].'">'.$service_category['category'].'</option>';
					} ?>
				</select></label>

				<label for="serviceid" class="super-label">Service:
				<select data-placeholder="Select Service" name="serviceid[]" class="chosen-select-deselect">
					<option>
					<?php $services = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `services` WHERE `deleted` = 0"),MYSQLI_ASSOC);
					foreach ($services as $service) {
						echo '<option '.($serviceid == $service['serviceid'] ? 'selected' : '').' data-category="'.$service['category'].'" value="'.$service['serviceid'].'">'.$service['heading'].'</option>';
					} ?>
				</select></label>
			<?php } ?>

			<?php if (strpos($enabled_fields, ',type,') !== FALSE) { ?>
				<label for="type" class="super-label">Appointment Type:
				<select data-placeholder="Select Type" name="appt_type[]" class="chosen-select-deselect">
					<option></option>
					<?php $appointment_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `appointment_type` WHERE `deleted` = 0"),MYSQLI_ASSOC);
					foreach ($appointment_types as $appointment_type) {
						echo '<option '.($service_type == $appointment_type['id'] ? 'selected' : '').' value="'.$appointment_type['id'].'">'.$appointment_type['name'].'</option>';
					} ?>
				</select></label>
			<?php } ?>

			<label for="appoint_date" class="super-label">Start Appointment Date & Time:
			<input type="text" name="appt_appoint_date[]" class="form-control dateandtimepicker" value="<?= $service_start ?>"></label>

			<label for="end_appoint_date" class="super-label">End Appointment Date & Time:
			<input type="text" name="appt_end_appoint_date[]" class="form-control dateandtimepicker" value="<?= $service_end ?>"></label>
			<?php if (strpos($enabled_fields, ',services,') !== FALSE && strpos($enabled_fields, ',multiservices,') !== FALSE) { ?>
				<img class="inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addService();">
				<img class="inline-img pull-right" src="../img/remove.png" onclick="remService(this);">
				<div class="clearfix"></div>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if (strpos($enabled_fields, ',status,') !== FALSE) { ?>
	<label for="follow_up_call_status" class="super-label">Status:
	<select data-placeholder="Select Status" name="appt_follow_up_call_status" class="chosen-select-deselect">
		<option></option>
		<?php
			foreach ($status_types as $status) {
				echo '<option value="'.$status.'"'.($status == $follow_up_call_status ? ' selected' : '').'>'.$status.'</option>';
			}
		?>
	</select></label>
	<?php } ?>

    <?php if (strpos($enabled_fields, ',notes,') !== FALSE) { ?>
    <label for="notes" class="super-label">Notes:
    <textarea name="appt_notes" class="form-control"><?= html_entity_decode($notes) ?></textarea></label>
    <?php } ?>
</div>

<div id="appt_view" class="block-group" style="height: calc(100% - 14em); overflow-y: auto;<?= $action == 'view' ? '' : ' display:none;' ?>">
    <?php
        $patient = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$patientid'"));
        $home_phone = decryptIt($patient['home_phone']);
        $cell_phone = decryptIt($patient['cell_phone']);
        $email_address = decryptIt($patient['email_address']);
        $amount_owing = $patient['amount_owing'];
        $amount_credit = $patient['amount_credit'];
		$appoint_date_start = '';
		$appoint_start = '';
		$appoint_date_end = '';
		$appoint_end = '';
		foreach(explode('*#*', $appoint_date) as $i => $start_appoint) {
			if($appoint_date_start == '') {
				$appoint_date_start = date('l, F d', strtotime($start_appoint));
				$appoint_start = date('g:i a', strtotime($start_appoint));
			}
			$end_appoint = explode('*#*', $end_appoint_date)[$i];
			$appoint_date_end = date('l, F d', strtotime($end_appoint));
			$appoint_end = date('g:i a', strtotime($end_appoint));
		}
		
        if ($appoint_date_start != $appoint_date_end) {
            $schedule_html = $appoint_date_start.' '.$appoint_start.' - '.$appoint_date_end.' '.$appoint_end;
        } else {
            $schedule_html = $appoint_date_start.'<br />'.$appoint_start.' - '.$appoint_end;
        }
    ?>
    <h4 style="color: black"><?= get_contact($dbc, $patientid) ?></h4>
    <?= !empty($home_phone) ? 'H: '.$home_phone.' ' : '' ?>
    <?= !empty($cell_phone) ? 'C: '.$cell_phone.' ' : '' ?>
    <?= !empty($email_address) ? 'E: '.$email_address.' ' : '' ?>
    <br />
	<?php if(strpos($enabled_fields, ',past_due,') !== FALSE || strpos($enabled_fields, ',current_services,') === FALSE) { ?>
		<label class="control-label">Amount Owing</label><label class="control-label" style="float:right;">$<?= $amount_owing ?></label><br />
		<label class="control-label">Amount Credit</label><label class="control-label" style="float:right;">$<?= $amount_credit ?></label><br />
	<?php }
	if(strpos($enabled_fields, ',current_services,') !== FALSE) {
		$service_total = 0;
		foreach(explode('*#*',$serviceids) as $i => $serviceid) {
			if($serviceid > 0) {
				$rate = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `services`.`serviceid`, `category`, `heading`, `company_rate_card`.`cust_price` FROM `services` LEFT JOIN `company_rate_card` ON `services`.`serviceid`=`company_rate_card`.`item_id` AND `company_rate_card`.`tile_name` LIKE 'Services' WHERE `services`.`serviceid`='$serviceid' AND `services`.`deleted`=0 AND IFNULL(NULLIF(`include_in_pos`,''),1) > 0 AND `company_rate_card`.`deleted`=0 AND (`company_rate_card`.`end_date` >= NOW() OR `company_rate_card`.`end_date` = '0000-00-00') ORDER BY `category`"));
				$service_total += $rate['service_rate'];
			}
		} ?>
		<label class="control-label">Service Cost</label><label class="control-label" style="float:right;">$<?= number_format($service_total,2) ?></label><br />
	<?php } ?>
    
    <hr>

    <label class="control-label" style="text-align: left;"><?= $schedule_html ?></label><br />

    <hr>

    <label class="control-label" style="text-align: left;"><?= get_type_from_booking($dbc, $type) ?><br /><?= get_contact($dbc, $therapistsid) ?></label>
</div>

<div class="pull-right" style="padding-top: 1em;">
    <button id="back_btn" class="btn brand-btn" value="cancel" onclick="backBooking(); return false;"<?= $action == 'edit' && !empty($bookingid) ? '' : ' style="display:none;"' ?>>Back</button>
    <button id="edit_btn" class="btn brand-btn" onclick="editBooking(); return false;"<?= $action == 'view' ? '' : ' style="display:none;"' ?>>Edit</button>
    <button id="submit_btn" type="submit" name="submit" value="calendar_booking" class="btn brand-btn"<?= $action == 'edit' ? '' : ' style="display:none;"' ?> onclick="return checkCallBeforeBooking();">Submit</button>
    <?php
        unset($page_query['teamid']);
        unset($page_query['subtab']);
        unset($page_query['unbooked']);
        unset($page_query['equipment_assignmentid']);
        unset($page_query['shiftid']);
        unset($page_query['action']);
        unset($page_query['bookingid']);
        unset($page_query['appoint_date']);
        unset($page_query['end_appoint_date']);
        unset($page_query['therapistsid']);
        unset($page_query['equipmentid']);
        unset($page_query['add_reminder']);
    ?>
    <a href="?<?= http_build_query($page_query) ?>" class="btn brand-btn">Cancel</a>
    <?php if(!empty($bookingid)) { ?>
        <a href="#" onclick="deleteAppt(); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png"></a>
    <?php } ?>
</div>
</form>