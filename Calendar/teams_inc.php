<?php include_once('../include.php');
checkAuthorised('calendar_rook');
include_once('../Calendar/calendar_functions_inc.php');

$teamid = '';
if ($_GET['teamid'] == 'NEW') {
    $teamid = '';
} else {
    $teamid = $_GET['teamid'];
}
$region = '';
if (!empty($_GET['region'])) {
    $region = $_GET['region'];
}
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

$contact_category = '';
$position_enabled = '';
$team_fields = '';
$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_teams`"));
if (!empty($get_field_config)) {
    $contact_category = explode(',', $get_field_config['contact_category']);
    $position_enabled = $get_field_config['position_enabled'];
    $team_fields = ','.$get_field_config['team_fields'].',';
}

// $contact_position = '';
// $contactid = '';
$region = '';
$location = '';
$classification = '';
$start_date = '';
$end_date = '';
$notes = '';
if (!empty($teamid)) {
    $get_team = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '$teamid'"));

    // $contact_position = explode(',',$get_team['contact_position']);
    // $contactid = explode(',',$get_team['contactid']);
    $region = $get_team['region'];
    $location = $get_team['location'];
    $classification = $get_team['classification'];
    $start_date = $get_team['start_date'];
    $end_date = $get_team['end_date'];
    $notes = $get_team['notes'];
}

$active_team = '';
$active_schedule = '';

if($_GET['subtab'] == 'team' || empty($_GET['subtab'])) {
    $active_team = ' active';
}
if($_GET['subtab'] == 'schedule') {
    $active_schedule = ' active';
}
?>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
<label for="team_select" class="super-label">Select Team:
<select data-placeholder="Select Team" name="teamid" class="chosen-select-deselect" onchange="teamChange(this);">
    <option></option>
    <option <?= ($teamid == '' ? 'selected' : '') ?> value="NEW">New Team</option>
    <?php
        $query = "SELECT * FROM `teams` WHERE `deleted` = 0".$region_query;
        $result = mysqli_query($dbc, $query);
        while ($row = mysqli_fetch_array($result)) {
            $team_name = getTeamName($dbc, $row['teamid']);
            echo '<option value="'.$row['teamid'].'"'.($row['teamid'] == $teamid ? ' selected' : '').'>'.$team_name.'</option>';
        }
    ?>
</select></label>

<hr>

<?php
$assign_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
for ($team_i = 0; $team_i < count($assign_contacts) || $team_i < 1; $team_i++) { ?>
    <div class="contact-block">
        <?php if($position_enabled == 1) { ?>
            <label for="contact_position" class="super-label">Contact Position
                <select data-placeholder="Select Position" name="team_contact_position[]" class="chosen-select-deselect form-control">
                    <option></option>
                    <?php $query = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `positions` WHERE `deleted` = 0 ORDER BY `name`"),MYSQLI_ASSOC);
                    foreach ($query as $row) {
                        echo '<option value="'.$row['name'].'" '.($row['name'] == $assign_contacts[$team_i]['contact_position'] ? 'selected' : '').'>'.$row['name'].'</option>';
                    } ?>
                </select>
            </label>
        <?php } ?>
        <label for="contact" class="super-label">Contact
            <select data-placeholder="Select Contact" name="team_contactid[]" class="chosen-select-deselect form-control">
                <option></option>
                <?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".("'".implode("','",$contact_category)."'").") AND `deleted` = 0 AND `status` = 1".$region_query),MYSQLI_ASSOC));
                foreach ($query as $id) {
                    echo '<option value="'.$id.'"'.($id == $assign_contacts[$team_i]['contactid'] ? ' selected' : '').'>'.get_contact($dbc, $id).'</option>';
                }
                if(!in_array($assign_contacts[$team_i]['contactid'], $query) && !empty($assign_contacts[$team_i]['contact'])) {
                    echo '<option value="'.$assign_contacts[$team_i]['contactid'].'" selected>'.get_contact($dbc, $assign_contacts[$team_i]['contactid']).'</option>';
                } ?>
            </select>
        </label>
        <div class="pull-right">
            <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addContact();">
            <img src="../img/remove.png" class="inline-img pull-right" onclick="deleteContact(this);">
        </div>
    </div>
<?php } ?>

<input type="hidden" name="team_contact_count" value="<?= count($contact_category) ?>">

<?php if (strpos($team_fields, ',region,') !== FALSE) { ?>
<label for="region" class="super-label">Region:
<select data-placeholder="Select Region" name="team_region" class="chosen-select-deselect form-control">
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

<?php if (strpos($team_fields, ',location,') !== FALSE) { ?>
<label for="location" class="super-label">Location:
<select data-placeholder="Select Location" name="team_location" class="chosen-select-deselect form-control">
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

<?php if (strpos($team_fields, ',classification,') !== FALSE) { ?>
<label for="classification" class="super-label">Classification:
<select data-placeholder="Select Classification" name="team_classification" class="chosen-select-deselect form-control">
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

<?php if (strpos($team_fields, ',start_date,') !== FALSE) { ?>
<label for="start_date" class="super-label">Start Date:
<input type="text" name="team_start_date" class="form-control datepicker" value="<?= $start_date == '0000-00-00' ? '' : $start_date ?>"></label>
<?php } ?>

<?php if (strpos($team_fields, ',end_date,') !== FALSE) { ?>
<label for="start_date" class="super-label">End Date:
<input type="text" name="team_end_date" class="form-control datepicker" value="<?= $end_date == '0000-00-00' ? '' : $end_date ?>"></label>
<?php } ?>

<?php if (strpos($team_fields, ',notes,') !== FALSE) { ?>
<label for="notes" class="super-label">Notes:
<textarea name="team_notes" class="form-control"><?= html_entity_decode($notes) ?></textarea></label>
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
        unset($page_query['equipmentid']);
        unset($page_query['add_reminder']);
    ?>
    <a href="?<?= http_build_query($page_query); ?>" class="btn brand-btn">Cancel</a>
</div>
</form>