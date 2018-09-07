<?php
if (isset($_POST['add_unbooked'])) {
    $ticket_filters = implode(',', $_POST['ticket_filters']);
    mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'unbooked_ticket_filters' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='unbooked_ticket_filters') num WHERE num.rows=0");
    mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$ticket_filters."' WHERE `name`='unbooked_ticket_filters'");

    $appt_filters = implode(',', $_POST['appt_filters']);
    mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'unbooked_appt_filters' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='unbooked_appt_filters') num WHERE num.rows=0");
    mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='".$appt_filters."' WHERE `name`='unbooked_appt_filters'");
}
?>
<h3>Unbooked List</h3>
<div class="panel-group" id="accordion2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field">Filter Settings</a>
            </h4>
        </div>
        <div id="collapse_field" class="panel-collapse collapse">
            <div class="panel-body">
                <?php $enabled_fields = ','.get_config($dbc, 'unbooked_ticket_filters').','; ?>
                <h4><?= TICKET_NOUN ?> Filters</h4>
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',project_type,') !== FALSE) { echo " checked"; } ?> value="project_type" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;<?= PROJECT_NOUN ?> Type
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',project,') !== FALSE) { echo " checked"; } ?> value="project" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;<?= PROJECT_NOUN ?>
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',region,') !== FALSE) { echo " checked"; } ?> value="region" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;Region
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',location,') !== FALSE) { echo " checked"; } ?> value="location" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;Location
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',classification,') !== FALSE) { echo " checked"; } ?> value="classification" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;Classification
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',customer,') !== FALSE) { echo " checked"; } ?> value="customer" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;Customer
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',business,') !== FALSE) { echo " checked"; } ?> value="business" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;Business
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',client,') !== FALSE) { echo " checked"; } ?> value="client" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;Client
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',corporation,') !== FALSE) { echo " checked"; } ?> value="corporation" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;Corporation
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',staff,') !== FALSE) { echo " checked"; } ?> value="staff" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;Staff
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',status,') !== FALSE) { echo " checked"; } ?> value="status" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;Status
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',date_range,') !== FALSE) { echo " checked"; } ?> value="date_range" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;Date Range
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',searchbox,') !== FALSE) { echo " checked"; } ?> value="searchbox" style="height: 20px; width: 20px;" name="ticket_filters[]">&nbsp;&nbsp;Search Box
                        </td>
                    </tr>
                </table>

                <?php $enabled_fields = ','.get_config($dbc, 'unbooked_appt_filters').','; ?>
                <h4>Appointment Filters</h4>
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',patient,') !== FALSE) { echo " checked"; } ?> value="patient" style="height: 20px; width: 20px;" name="appt_filters[]">&nbsp;&nbsp;Patient
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',injurytype,') !== FALSE) { echo " checked"; } ?> value="injurytype" style="height: 20px; width: 20px;" name="appt_filters[]">&nbsp;&nbsp;Injury Type
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',appttype,') !== FALSE) { echo " checked"; } ?> value="appttype" style="height: 20px; width: 20px;" name="appt_filters[]">&nbsp;&nbsp;Appointment Type
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($enabled_fields, ',searchbox,') !== FALSE) { echo " checked"; } ?> value="searchbox" style="height: 20px; width: 20px;" name="appt_filters[]">&nbsp;&nbsp;Search Box
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="form-group clearfix">
	<div class="col-sm-6">
		<a href="calendars.php" class="btn brand-btn btn-lg">Back</a>
	</div>
	<div class="col-sm-6">
		<button	type="submit" name="add_unbooked" value="active_unbooked" class="btn brand-btn btn-lg pull-right">Submit</button>
	</div>
</div>