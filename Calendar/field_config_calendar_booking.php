<?php
if (isset($_POST['add_booking'])) {
    $status_types = filter_var($_POST['status_types'], FILTER_SANITIZE_STRING);
    $enabled_fields = implode(',', $_POST['enabled_fields']);
    $new_client_fields = implode(',', $_POST['new_client_fields']);
    $client_type = filter_var($_POST['client_type'], FILTER_SANITIZE_STRING);

    mysqli_query($dbc, "INSERT INTO `field_config_calendar_booking` (`status_types`, `enabled_fields`, `new_client_fields`, `client_type`) SELECT 'Booking Unconfirmed,Booking Confirmed,Arrived,Invoiced,Paid,Rescheduled,Late Cancellation / No-Show,Cancelled','','','' FROM (SELECT COUNT(*) rows FROM `field_config_calendar_booking`) num WHERE num.rows=0");
    mysqli_query($dbc, "UPDATE `field_config_calendar_booking` SET `status_types` = '$status_types', `enabled_fields` = '$enabled_fields', `new_client_fields` = '$new_client_fields', `client_type` = '$client_type'");

    //Appointment Types
    foreach ($_POST['appt_type_delete'] as $key => $value) {
        $date_of_archival = date('Y-m-d');
        mysqli_query($dbc, "UPDATE `appointment_type` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `id` = '$value'");
    }
    foreach ($_POST['appt_type_existing'] as $key => $value) {
        mysqli_query($dbc, "UPDATE `appointment_type` SET `name` = '$value' WHERE `id` = '$key'");
    }
    foreach ($_POST['appt_type_new'] as $key => $value) {
        mysqli_query($dbc, "INSERT INTO `appointment_type` (`name`) SELECT '$value' FROM (SELECT COUNT(*) rows FROM `appointment_type` WHERE `name` = '$value') num WHERE num.rows = 0");
        $typeid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `appointment_type` WHERE `name` = '$value'"))['id'];
        mysqli_query($dbc, "UPDATE `appointment_type` SET `deleted` = 0 WHERE `id` = '$typeid'");
    }
}
?>
<script type="text/javascript">
function displayStatusTypes(statusCheckbox) {
    if ($(statusCheckbox).is(":checked")) {
        $('#status_types').show();
    } else {
        $('#status_types').hide();
    }
}
function addApptType() {
    var block = $('.appt_type_div').last();
    var clone = block.clone();

    clone.find('.form-control').val('');
    clone.find('.appt_type_input').prop('name', 'appt_type_new[]');

    block.after(clone);
}
function deleteApptType(btn) {
    if($('.appt_type_div').length <= 1) {
        addApptType();
    }

    var typeid = $(btn).closest('.appt_type_div').find('.appt_type_input').prop('name').split('[');
    typeid = typeid[1].slice(0,-1);
    if(typeid != '' && typeid != undefined) {
        var delete_html = '<input type="hidden" name="appt_type_delete[]" value="'+typeid+'">';
        $('.delete_appt_type').append(delete_html);
    }
    $(btn).closest('.appt_type_div').remove();
}
</script>
<?php
$status_types = '';
$enabled_fields = '';
$new_client_fields = '';
$client_type = '';

$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_calendar_booking`"));
if (!empty($get_field_config)) {
    $status_types = $get_field_config['status_types'];
    $enabled_fields = ','.$get_field_config['enabled_fields'].',';
    $new_client_fields = ','.$get_field_config['new_client_fields'].',';
    $client_type = $get_field_config['client_type'];
} else {
    mysqli_query($dbc, "INSERT INTO `field_config_calendar_booking` (`status_types`, `enabled_fields`, `client_type`) SELECT 'Booking Unconfirmed,Booking Confirmed,Arrived,Invoiced,Paid,Rescheduled,Late Cancellation / No-Show,Cancelled','','' FROM (SELECT COUNT(*) rows FROM `field_config_calendar_booking`) num WHERE num.rows=0");
    $get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_calendar_booking`"));
    $status_types = $get_field_config['status_types'];
}
?>
<h3>Appointments</h3>
<div class="panel-group" id="accordion2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field">Field Settings</a>
            </h4>
        </div>
        <div id="collapse_field" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="client_type" class="col-sm-4 control-label">Client Type:</label>
                    <div class="col-sm-8">
                        <select name="client_type" data-placeholder="Select Client Type" class="chosen-select-deselect form-control">
                            <option></option>
                            <?php
                            $query = "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 ORDER BY `category`";
                            $result = mysqli_query($dbc, $query);
                            while ($row = mysqli_fetch_array($result)) {
                                echo '<option value="'.$row['category'].'"'.($row['category'] == $client_type ? ' selected' : '').'>'.$row['category'].'</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="new_client_fields" class="col-sm-4 control-label">New Client Fields:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="new_client_fields[]" value="home_phone" <?= (strpos($new_client_fields, ',home_phone,') !== FALSE ? 'checked' : '') ?>> Home Phone</label>
                        <label class="form-checkbox"><input type="checkbox" name="new_client_fields[]" value="cell_phone" <?= (strpos($new_client_fields, ',cell_phone,') !== FALSE ? 'checked' : '') ?>> Cell Phone</label>
                        <label class="form-checkbox"><input type="checkbox" name="new_client_fields[]" value="business_phone" <?= (strpos($new_client_fields, ',business_phone,') !== FALSE ? 'checked' : '') ?>> Business Phone</label>
                        <label class="form-checkbox"><input type="checkbox" name="new_client_fields[]" value="email_address" <?= (strpos($new_client_fields, ',email_address,') !== FALSE ? 'checked' : '') ?>> Email Address</label>
                    </div>
                </div>
                <div class="form-group">
                    <label for="injury" class="col-sm-4 control-label">Appointment Fields:</label>
                    <div class="col-sm-8">
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="past_due" <?= (strpos($enabled_fields, ',past_due,') !== FALSE || strpos($enabled_fields, ',current_services,') === FALSE ? 'checked' : '') ?>> Show Past Amount Due</label>
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="current_services" <?= (strpos($enabled_fields, ',current_services,') !== FALSE ? 'checked' : '') ?>> Show Current Services</label>
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="injury" <?= (strpos($enabled_fields, ',injury,') !== FALSE ? 'checked' : '') ?>> Injury</label>
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="type" <?= (strpos($enabled_fields, ',type,') !== FALSE ? 'checked' : '') ?>> Appointment Type</label>
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="services" <?= (strpos($enabled_fields, ',services,') !== FALSE ? 'checked' : '') ?> onchange="if(this.checked) { $('[name^=enabled_fields][value=multiservices]').closest('label').show(); } else { $('[name^=enabled_fields][value=multiservices]').closest('label').hide(); }"> Services</label>
                        <label class="form-checkbox" style="<?= (strpos($enabled_fields, ',services,') !== FALSE ? '' : 'display:none;') ?>"><input type="checkbox" name="enabled_fields[]" value="multiservices" <?= (strpos($enabled_fields, ',multiservices,') !== FALSE ? 'checked' : '') ?>> Allow Multiple Services</label>
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="status" <?= (strpos($enabled_fields, ',status,') !== FALSE ? 'checked' : '') ?>> Status</label>
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="notes" <?= (strpos($enabled_fields, ',notes,') !== FALSE ? 'checked' : '') ?>> Notes</label>
                        <label class="form-checkbox"><input type="checkbox" name="enabled_fields[]" value="checkin" <?= (strpos($enabled_fields, ',checkin,') !== FALSE ? 'checked' : '') ?>> Check In Button</label>
                    </div>
                </div>
                <div class="form-group" id="status_types" <?php echo (strpos($enabled_fields, ',status,') !== FALSE ? '' : 'style="display: none;"') ?>>
                    <label for="status_types" class="col-sm-4 control-label">Status Types:<br />(Add the Status types separated by a comma to be displayed)</label>
                    <div class="col-sm-8">
                        <input type="text" name="status_types" value="<?= $status_types; ?>" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label for="notes" class="col-sm-4 control-label">Point of Sale:</label>
                    <div class="col-sm-8">
                        <input type="radio" name="enabled_fields[]" value="pos" <?= (strpos($enabled_fields, ',pos,') !== false ? 'checked' : ''); ?> /> <?= POS_ADVANCE_TILE ?>
                        <input type="radio" name="enabled_fields[]" value="pos_touch" <?= (strpos($enabled_fields, ',pos_touch,') !== false ? 'checked' : ''); ?> /> Point of Sale - Touch Interface
                        <input type="radio" name="enabled_fields[]" value="pos_basic" <?= (strpos($enabled_fields, ',pos_basic,') !== false ? 'checked' : ''); ?> /> Point of Sale (Basic)
                        <input type="radio" name="enabled_fields[]" value="checkout" <?= (strpos($enabled_fields, ',checkout,') !== false ? 'checked' : ''); ?> /> Check Out
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_appt_type">Appointment Types</a>
            </h4>
        </div>
        <div id="collapse_appt_type" class="panel-collapse collapse">
            <div class="panel-body">
                <?php $appointment_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `appointment_type` WHERE `deleted` = 0"),MYSQLI_ASSOC);
                foreach ($appointment_types as $appointment_type) { ?>
                    <div class="form-group appt_type_div">
                        <label for="appt_type" class="col-sm-4 control-label">Appointment Type:</label>
                        <div class="col-sm-7">
                            <input type="text" name="appt_type_existing[<?= $appointment_type['id'] ?>]" value="<?= $appointment_type['name'] ?>" class="form-control appt_type_input">
                        </div>
                        <div class="col-sm-1 pull-right">
                            <img src="../img/icons/plus.png" class="inline-img pull-right" onclick="addApptType();">
                            <img src="../img/remove.png" class="inline-img pull-right" onclick="deleteApptType(this);">
                        </div>
                    </div>
                <?php }
                if (count($appointment_types) == 0) { ?>
                    <div class="form-group appt_type_div">
                        <label for="appt_type" class="col-sm-4 control-label">Appointment Type:</label>
                        <div class="col-sm-7">
                            <input type="text" name="appt_type_new[]" value="" class="form-control appt_type_input">
                        </div>
                        <div class="col-sm-1 pull-right">
                            <img src="../img/icons/plus.png" class="inline-img pull-right" onclick="addApptType();">
                            <img src="../img/remove.png" class="inline-img pull-right" onclick="deleteApptType(this);">
                        </div>
                    </div>
                <?php } ?>
            </div>
            <div class="delete_appt_type">
            </div>
        </div>
    </div>
</div>

<div class="form-group clearfix">
	<div class="col-sm-6">
		<a href="calendars.php" class="btn brand-btn btn-lg">Back</a>
	</div>
	<div class="col-sm-6">
		<button	type="submit" name="add_booking" value="add_booking" class="btn brand-btn btn-lg pull-right">Submit</button>
	</div>
</div>