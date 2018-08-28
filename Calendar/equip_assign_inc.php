<?php include_once('../include.php');
include_once('../Calendar/calendar_functions_inc.php');
checkAuthorised('calendar_rook');
$contact_regions = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
$contact_security = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_security` WHERE `contactid`='$contactid'"));
$allowed_regions = array_filter(explode('#*#',$contact_security['region_access']));
if(count($allowed_regions) == 0) {
    $allowed_regions = $contact_regions;
}
$class_regions = explode(',',get_config($dbc, '%_class_regions', true, ','));
$contact_classifications = [];
$classification_regions = [];
foreach(explode(',',get_config($dbc, '%_classification', true, ',')) as $i => $contact_classification) {
	$row = array_search($contact_classification, $contact_classifications);
	if($row !== FALSE && $class_regions[$i] != '') {
		$classification_regions[$row][] = $class_regions[$i];
	} else {
		$contact_classifications[] = $contact_classification;
		$classification_regions[] = array_filter([$class_regions[$i]]);
	}
}
$contact_locations = array_filter(explode(',', mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `con_locations` FROM `field_config_contacts` WHERE `con_locations` IS NOT NULL"))['con_locations']));
$allowed_locations = array_filter(explode('#*#',$contact_security['location_access']));
if(count($allowed_locations) == 0) {
    $allowed_locations = $contact_locations;
}
if(empty($_GET['region'])) {
    $_GET['region'] = $allowed_regions[0];
}
$region_query = '';
if($_GET['region'] == 'Display All' && $allowed_regions != $contact_regions) {
    $all_allowed = "'".trim(implode("','", $allowed_regions), "','")."'";
    $region_query = " AND IFNULL(`region`,'') IN (".$all_allowed.",'')";
} else if($_GET['region'] != 'Display All' && count($contact_regions) > 0) {
    $region_query = " AND IFNULL(`region`,'') IN ('".$_GET['region']."','')";
}

$equipment_assignmentid = '';
if ($_GET['equipment_assignmentid'] == 'NEW') {
    $equipment_assignmentid = '';
} else {
    $equipment_assignmentid = $_GET['equipment_assignmentid'];
}
$equipment_category = '';
$client_type = '';
$contact_category = '';
$contractor_category = '';
$position_enabled = '';
$enabled_fields = '';
$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"));
if (!empty($get_field_config)) {
    $equipment_category = $get_field_config['equipment_category'];
    $equipment_categories = array_filter(explode(',', $equipment_category));
    if(empty($equipment_categories) || count($equipment_categories) > 1) {
        $equipment_category = 'Equipment';
    }
    $equip_cat_query = '';
    if(count($equipment_categories) > 0) {
        $equip_cat_query = " AND `equipment`.`category` IN ('".implode("','", $equipment_categories)."')";
    }
    $client_type = $get_field_config['client_type'];
    $contact_category = explode(',', $get_field_config['contact_category']);
    $contractor_category = !empty($get_field_config['contractor_category']) ? explode(',', $get_field_config['contractor_category']) : '';
    $position_enabled = $get_field_config['position_enabled'];
    $enabled_fields = ','.$get_field_config['enabled_fields'].',';
}

$equipmentid = '';
$clientid = '';
// $contact_position = '';
// $contactid = '';
$teamid = '';
$region = '';
$location = '';
$classification = '';
$start_date = '';
$end_date = '';
$notes = '';
if($_GET['equipmentid']) {
    $equipmentid = $_GET['equipmentid'];
    $equipment_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid` = '$equipmentid'"));
    $region = $equipment_info['region'];
    $location = $equipment_info['location'];
    $classification = $equipment_info['classification'];
}
if(!empty($_GET['start_date'])) {
    $start_date = $_GET['start_date'];
}
if(!empty($_GET['end_date'])) {
    $end_date = $_GET['end_date'];
}
if (!empty($equipment_assignmentid)) {
    $get_equip_assign = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipment_assignmentid` = '$equipment_assignmentid'"));

    $equipmentid = $get_equip_assign['equipmentid'];
    $clientid = $get_equip_assign['clientid'];
    // $contact_position = explode(',',$get_equip_assign['contact_position']);
    // $contactid = explode(',',$get_equip_assign['contactid']);
    $teamid = $get_equip_assign['teamid'];
    $region = $get_equip_assign['region'];
    $location = $get_equip_assign['location'];
    $classification = $get_equip_assign['classification'];
    $start_date = $get_equip_assign['start_date'];
    $end_date = $get_equip_assign['end_date'];
    $notes = $get_equip_assign['notes'];
} ?>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
<!-- <label for="equipment_assignment" class="super-label">Select <?= $equipment_category ?> Assignment:
<select data-placeholder="Select <?= $equipment_category ?> Assignment" name="equipment_assignmentid" class="chosen-select-deselect" onchange="equipmentAssignmentChange(this)">
    <option></option>
    <option <?= ($teamid == '' ? 'selected' : '') ?> value="NEW">New <?= $equipment_category ?> Assignment</option>
    <?php
        $query = "SELECT ea.*, e.* FROM `equipment_assignment` ea LEFT JOIN `equipment` e ON ea.`equipmentid` = e.`equipmentid` WHERE ea.`deleted` = 0 AND DATE(ea.`end_date`) >= DATE(CURDATE())".$region_query." ORDER BY e.`unit_number`";
        $result = mysqli_query($dbc, $query);
        while ($row = mysqli_fetch_array($result)) {
            $equipment_name = $equipment_category . ' #' . $row['unit_number'];
            echo '<option value="'.$row['equipment_assignmentid'].'"'.($row['equipment_assignmentid'] == $equipment_assignmentid ? ' selected' : '').'>'.$equipment_name.'</option>';
        }
    ?>
</select></label> -->

<input type="hidden" name="equipment_assignmentid" value="<?= $equipment_assignmentid ?>">

<label for="equipment_category" class="super-label"><?= $equipment_category ?>:
<select data-placeholder="Select <?= $equipment_category ?>" name="equip_assign_equipmentid" class="chosen-select-deselect">
    <option></option>
    <?php
        $query = "SELECT * FROM `equipment` WHERE `deleted` = 0 ".$equip_cat_query." ORDER BY `category`, `unit_number`";
        $result = mysqli_query($dbc, $query);
        while ($row = mysqli_fetch_array($result)) {
            $equipment_name = $row['category'] . ' #' . $row['unit_number'];
            echo '<option value="'.$row['equipmentid'].'"'.($row['equipmentid'] == $equipmentid ? ' selected' : '').'>'.$equipment_name.'</option>';
        }
    ?>
</select></label>

<label for="clientid" class="super-label"><?= $client_type ?>:
<select data-placeholder="Select <?= $client_type ?>" name="equip_assign_clientid" class="chosen-select-deselect">
    <option></option>
    <?php
        $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 AND `category` = '".$client_type."'".$region_query),MYSQLI_ASSOC));
        foreach ($query as $id) {
            echo '<option value="'.$id.'"'.($id == $clientid ? ' selected' : '').'>'.(!empty(get_client($dbc, $id)) ? get_client($dbc, $id) : get_contact($dbc, $id)).'</option>';
        }
    ?>
</select></label>

<hr>

<label class="form-label">Contacts</label>
<?php $assign_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0 AND `contractor` = 0"),MYSQLI_ASSOC);
for ($equip_i = 0; $equip_i < count($assign_contacts) || $equip_i < 2; $equip_i++) { ?>
    <div class="contact-block">
        <?php if($position_enabled == 1) { ?>
            <label for="contact_position" class="super-label">Assigned Contact Position
                <select data-placeholder="Select Position" name="equip_assign_contact_position[]" class="chosen-select-deselect form-control">
                    <option></option>
                    <?php $query = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `positions` WHERE `deleted` = 0 ORDER BY `name`"),MYSQLI_ASSOC);
                    foreach ($query as $row) {
                        echo '<option value="'.$row['name'].'" '.($row['name'] == $assign_contacts[$equip_i]['contact_position'] ? 'selected' : '').'>'.$row['name'].'</option>';
                    } ?>
                </select>
            </label>
        <?php } ?>
        <label for="contact" class="super-label">Assigned Contact
            <select data-placeholder="Select Contact" name="equip_assign_contactid[]" class="chosen-select-deselect form-control">
                <option></option>
                <?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".("'".implode("','",$contact_category)."'").") AND `deleted` = 0 AND `status` = 1".$region_query),MYSQLI_ASSOC));
                foreach ($query as $id) {
                    echo '<option value="'.$id.'"'.($id == $assign_contacts[$equip_i]['contactid'] ? ' selected' : '').'>'.get_contact($dbc, $id).'</option>';
                }
                if(!in_array($assign_contacts[$equip_i]['contactid'], $query) && !empty($assign_contacts[$equip_i]['contactid'])) {
                    echo '<option value="'.$assign_contacts[$equip_i]['contactid'].'" selected>'.get_contact($dbc, $assign_contacts[$equip_i]['contactid']).'</option>';
                } ?>
            </select>
        </label>
        <div class="pull-right">
            <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addContact();">
            <img src="../img/remove.png" class="inline-img pull-right" onclick="deleteContact(this);">
        </div>
    </div>
<?php } ?>
<hr>

<?php if(!empty($contractor_category)) { ?>
    <label class="form-label">Contractors</label>
    <?php $assign_contractors = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0 AND `contractor` = 1"),MYSQLI_ASSOC);
    for ($equip_i = 0; $equip_i < count($assign_contractors) || $equip_i < 1; $equip_i++) { ?>
        <div class="contractor-block">
            <?php if($position_enabled == 1) { ?>
                <label for="contractor_position" class="super-label">Assigned Contractor Position
                    <select data-placeholder="Select Position" name="equip_assign_contractor_position[]" class="chosen-select-deselect form-control">
                        <option></option>
                        <?php $query = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `positions` WHERE `deleted` = 0 ORDER BY `name`"),MYSQLI_ASSOC);
                        foreach ($query as $row) {
                            echo '<option value="'.$row['name'].'" '.($row['name'] == $assign_contractors[$equip_i]['contact_position'] ? 'selected' : '').'>'.$row['name'].'</option>';
                        } ?>
                    </select>
                </label>
            <?php } ?>
            <label for="contractor" class="super-label">Assigned Contractor
                <select data-placeholder="Select Contractor" name="equip_assign_contractorid[]" class="chosen-select-deselect form-control">
                    <option></option>
                    <?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".("'".implode("','",$contractor_category)."'").") AND `deleted` = 0 AND `status` = 1".$region_query),MYSQLI_ASSOC));
                    foreach ($query as $id) {
                        echo '<option value="'.$id.'"'.($id == $assign_contractors[$equip_i]['contactid'] ? ' selected' : '').'>'.get_contact($dbc, $id).'</option>';
                    }
                    if(!in_array($assign_contractors[$equip_i]['contactid'], $query) && !empty($assign_contractors[$equip_i]['contactid'])) {
                        echo '<option value="'.$assign_contractors[$equip_i]['contactid'].'" selected>'.get_contact($dbc, $assign_contractors[$equip_i]['contactid']).'</option>';
                    } ?>
                </select>
            </label>
            <div class="pull-right">
                <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addContractor();">
                <img src="../img/remove.png" class="inline-img pull-right" onclick="deleteContractor(this);">
            </div>
        </div>
    <?php } ?>
    <hr>
<?php } ?>

<input type="hidden" name="equip_assign_contact_count" value="<?= count($contact_category) ?>">

<?php if (strpos($enabled_fields, ',team,') !== FALSE) { ?>
<label for="equip_assign_team" class="super-label">Team:
<select data-placeholder="Select Team" name="equip_assign_teamid" class="chosen-select-deselect">
    <option></option>
    <?php
        $query = "SELECT * FROM `teams` WHERE `deleted` = 0 AND (DATE(`start_date`) <= DATE(CURDATE()) OR `start_date` IS NULL OR `start_date` = '' OR `start_date` = '0000-00-00') AND (DATE(`end_date`) >= DATE(CURDATE()) OR `end_date` IS NULL OR `end_date` = '' OR `end_date` = '0000-00-00')".$region_query;
        $result = mysqli_query($dbc, $query);
        while ($row = mysqli_fetch_array($result)) {
            $team_name = get_team_name($dbc, $row['teamid']);
            echo '<option value="'.$row['teamid'].'"'.($row['teamid'] == $teamid ? ' selected' : '').'>'.$team_name.'</option>';
        }
    ?>
</select></label>
<?php } ?>

<hr>

<?php if (strpos($enabled_fields, ',region,') !== FALSE) { ?>
<label for="region" class="super-label">Region:
<select data-placeholder="Select Region" name="equip_assign_region" class="chosen-select-deselect form-control">
    <option></option>
    <?php
        $query = "SELECT * FROM `general_configuration` WHERE `name` LIKE '%_region'";
        $result = mysqli_query($dbc, $query);
        $region_list = '';
        while ($row = mysqli_fetch_array($result)) {
            $region_list .= $row['value'] . ',';
        }
        $region_list = rtrim($region_list, ',');
        $region_list = explode(',', $region_list);
        asort($region_list);
        foreach ($region_list as $single_region) {
            if ($region == $single_region) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            if (in_array($single_region, $allowed_regions) || $region == $single_region) {
                echo "<option ".$selected." value='". $single_region."'>".$single_region.'</option>';
            }
        }
    ?>
</select></label>
<?php } ?>

<?php if (strpos($enabled_fields, ',location,') !== FALSE) { ?>
<label for="location" class="super-label">Location:
<select data-placeholder="Select Location" name="equip_assign_location" class="chosen-select-deselect form-control">
    <option></option>
    <?php
        foreach ($contact_locations as $single_location) {
            if ($location == $single_location) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            if (in_array($single_location, $allowed_locations) || $location == $single_location) {
                echo "<option ".$selected." value='". $single_location."'>".$single_location.'</option>';
            }
        }
    ?>
</select></label>
<?php } ?>

<?php if (strpos($enabled_fields, ',classification,') !== FALSE) { ?>
<label for="classification" class="super-label">Classification:
<select data-placeholder="Select Classification" name="equip_assign_classification" class="chosen-select-deselect form-control">
    <option></option>
    <?php
        foreach ($contact_classifications as $single_classification) {
            if ($classification == $single_classification) {
                $selected = 'selected="selected"';
            } else {
                $selected = '';
            }
            if (in_array($single_classification, $contact_classifications) || $classification == $single_classification) {
                echo "<option ".$selected." value='". $single_classification."'>".$single_classification.'</option>';
            }
        }
    ?>
</select></label>
<?php } ?>

<?php if (strpos($enabled_fields, ',start_date,') !== FALSE) { ?>
<label for="start_date" class="super-label">Start Date:
<input type="text" name="equip_assign_start_date" class="form-control datepicker" value="<?= $start_date == '0000-00-00' ? '' : $start_date ?>" onchange="updateEndDate(this)"></label>
<?php } ?>

<?php if (strpos($enabled_fields, ',end_date,') !== FALSE) { ?>
<label for="start_date" class="super-label">End Date:
<input type="text" name="equip_assign_end_date" class="form-control datepicker" value="<?= $end_date == '0000-00-00' ? '' : $end_date ?>"></label>
<?php } ?>

<?php if (strpos($enabled_fields, ',notes,') !== FALSE) { ?>
<label for="notes" class="super-label">Notes:
<textarea name="equip_assign_notes" class="form-control"><?= html_entity_decode($notes) ?></textarea></label>
<?php } ?>

<div class="pull-right" style="padding-top: 1em;">
    <button type="submit" name="submit" value="calendar_team" class="btn brand-btn">Submit</button>
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
    ?>
    <a href="?<?= http_build_query($page_query) ?>" class="btn brand-btn">Cancel</a>
</div>
</form>