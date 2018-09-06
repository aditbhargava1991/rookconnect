<?php
if (isset($_POST['add_security'])) {
    foreach($_POST['security_level'] as $i => $security_level) {
        $level_arr = explode('#*#', $security_level);
        $calendar_type = $level_arr[0];
        $security_level = $level_arr[1];
        $allowed_roles = filter_var(implode(',',$_POST['allowed_roles_'.$i]),FILTER_SANITIZE_STRING);
        $allowed_ticket_types = filter_var(implode(',',$_POST['allowed_ticket_types_'.$i]),FILTER_SANITIZE_STRING);
        mysqli_query($dbc, "INSERT INTO `field_config_calendar_security` (`calendar_type`, `role`) SELECT '$calendar_type', '$security_level' FROM (SELECT COUNT(*) rows FROM `field_config_calendar_security` WHERE `calendar_type` = '$calendar_type' AND `role` = '$security_level') num WHERE num.rows=0");
        mysqli_query($dbc, "UPDATE `field_config_calendar_security` SET `allowed_roles` = '$allowed_roles', `allowed_ticket_types` = '$allowed_ticket_types' WHERE `role` = '$security_level' AND `calendar_type` = '$calendar_type'");
    }
}
$sec_i = 0;
?>
<h3>Security Settings</h3>
<div class="panel-group" id="accordion2">
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_uni">Universal Calendar - Allowed Security Levels/<?= TICKET_NOUN ?> Types<span class="glyphicon glyphicon-plus"></span></a>
            </h4>
        </div>
        <div id="collapse_uni" class="panel-collapse collapse">
            <div class="panel-body">
                <div id="no-more-tables">
                    <table class="table table-bordered">
                        <tr class="hidden-xs">
                            <th>Security Level</th>
                            <th>Allowed Security Levels</th>
                            <th>Allowed <?= TICKET_NOUN ?> Types</th>
                        </tr>
                        <?php foreach(get_security_levels($dbc) as $security_label => $security_level) {
                            $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_calendar_security` WHERE `role` = '".$security_level."' AND `calendar_type` = 'uni'")); ?>
                            <tr>
                                <input type="hidden" name="security_level[<?= $sec_i ?>]" value="uni#*#<?= $security_level ?>">
                                <td data-title="Security Level"><?= $security_label ?></td>
                                <td data-title="Allowed Security Levels">
                                    <select name="allowed_roles_<?= $sec_i ?>[]" multiple class="chosen-select-deselect">
                                        <option></option>
                                        <?php foreach(get_security_levels($dbc) as $allowed_security_label => $allowed_security_level) {
                                            echo '<option value="'.$allowed_security_level.'" '.(strpos(','.$field_config['allowed_roles'].',', ','.$allowed_security_level.',') !== FALSE ? 'selected' : '').'>'.$allowed_security_label.'</option>';
                                        } ?>
                                    </select>
                                </td>
                                <td data-title="Allowed <?= TICKET_NOUN ?> Types">
                                    <select name="allowed_ticket_types_<?= $sec_i ?>[]" multiple class="chosen-select-deselect">
                                        <option></option>
                                        <?php foreach(array_filter(explode(',',get_config($dbc, 'ticket_tabs'))) as $ticket_type) {
                                            $ticket_type_value = config_safe_str($ticket_type);
                                            echo '<option value="'.$ticket_type_value.'" '.(strpos(','.$field_config['allowed_ticket_types'].',', ','.$ticket_type_value.',') !== FALSE ? 'selected' : '').'>'.$ticket_type.'</option>';
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <?php $sec_i++;
                        } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dispatch">Dispatch Calendar - Allowed Security Levels/<?= TICKET_NOUN ?> Types<span class="glyphicon glyphicon-plus"></span></a>
            </h4>
        </div>
        <div id="collapse_dispatch" class="panel-collapse collapse">
            <div class="panel-body">
                <div id="no-more-tables">
                    <table class="table table-bordered">
                        <tr class="hidden-xs">
                            <th>Security Level</th>
                            <th>Allowed Security Levels</th>
                            <th>Allowed <?= TICKET_NOUN ?> Types</th>
                        </tr>
                        <?php foreach(get_security_levels($dbc) as $security_label => $security_level) {
                            $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_calendar_security` WHERE `role` = '".$security_level."' AND `calendar_type` = 'schedule'")); ?>
                            <tr>
                                <input type="hidden" name="security_level[<?= $sec_i ?>]" value="schedule#*#<?= $security_level ?>">
                                <td data-title="Security Level"><?= $security_label ?></td>
                                <td data-title="Allowed Security Levels">
                                    <select name="allowed_roles_<?= $sec_i ?>[]" multiple class="chosen-select-deselect">
                                        <?php foreach(get_security_levels($dbc) as $allowed_security_label => $allowed_security_level) {
                                            echo '<option value="'.$allowed_security_level.'" '.(strpos(','.$field_config['allowed_roles'].',', ','.$allowed_security_level.',') !== FALSE ? 'selected' : '').'>'.$allowed_security_label.'</option>';
                                        } ?>
                                    </select>
                                </td>
                                <td data-title="Allowed <?= TICKET_NOUN ?> Types">
                                    <select name="allowed_ticket_types_<?= $sec_i ?>[]" multiple class="chosen-select-deselect">
                                        <?php foreach(array_filter(explode(',',get_config($dbc, 'ticket_tabs'))) as $ticket_type) {
                                            $ticket_type_value = config_safe_str($ticket_type);
                                            echo '<option value="'.$ticket_type_value.'" '.(strpos(','.$field_config['allowed_ticket_types'].',', ','.$ticket_type_value.',') !== FALSE ? 'selected' : '').'>'.$ticket_type.'</option>';
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <?php $sec_i++;
                        } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ticket"><?= TICKET_NOUN ?> Calendar - Allowed Security Levels/<?= TICKET_NOUN ?> Types<span class="glyphicon glyphicon-plus"></span></a>
            </h4>
        </div>
        <div id="collapse_ticket" class="panel-collapse collapse">
            <div class="panel-body">
                <div id="no-more-tables">
                    <table class="table table-bordered">
                        <tr class="hidden-xs">
                            <th>Security Level</th>
                            <th>Allowed Security Levels</th>
                            <th>Allowed <?= TICKET_NOUN ?> Types</th>
                        </tr>
                        <?php foreach(get_security_levels($dbc) as $security_label => $security_level) {
                            $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_calendar_security` WHERE `role` = '".$security_level."' AND `calendar_type` = 'ticket'")); ?>
                            <tr>
                                <input type="hidden" name="security_level[<?= $sec_i ?>]" value="ticket#*#<?= $security_level ?>">
                                <td data-title="Security Level"><?= $security_label ?></td>
                                <td data-title="Allowed Security Levels">
                                    <select name="allowed_roles_<?= $sec_i ?>[]" multiple class="chosen-select-deselect">
                                        <?php foreach(get_security_levels($dbc) as $allowed_security_label => $allowed_security_level) {
                                            echo '<option value="'.$allowed_security_level.'" '.(strpos(','.$field_config['allowed_roles'].',', ','.$allowed_security_level.',') !== FALSE ? 'selected' : '').'>'.$allowed_security_label.'</option>';
                                        } ?>
                                    </select>
                                </td>
                                <td data-title="Allowed <?= TICKET_NOUN ?> Types">
                                    <select name="allowed_ticket_types_<?= $sec_i ?>[]" multiple class="chosen-select-deselect">
                                        <?php foreach(array_filter(explode(',',get_config($dbc, 'ticket_tabs'))) as $ticket_type) {
                                            $ticket_type_value = config_safe_str($ticket_type);
                                            echo '<option value="'.$ticket_type_value.'" '.(strpos(','.$field_config['allowed_ticket_types'].',', ','.$ticket_type_value.',') !== FALSE ? 'selected' : '').'>'.$ticket_type.'</option>';
                                        } ?>
                                    </select>
                                </td>
                            </tr>
                            <?php $sec_i++;
                        } ?>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="form-group clearfix">
	<div class="col-sm-6">
		<a href="calendars.php" class="btn brand-btn btn-lg">Back</a>
	</div>
	<div class="col-sm-6">
		<button	type="submit" name="add_security" value="active_security" class="btn brand-btn btn-lg pull-right">Submit</button>
	</div>
</div>