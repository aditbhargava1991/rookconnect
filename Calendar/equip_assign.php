<?php include_once('../include.php');
include_once('../Calendar/calendar_functions_inc.php');
checkAuthorised('calendar_rook');

if (isset($_POST['submit'])) {
    $equipmentid = filter_var($_POST['equip_assign_equipmentid'],FILTER_SANITIZE_STRING);
    $clientid = filter_Var($_POST['equip_assign_clientid'],FILTER_SANITIZE_STRING);
    $teamid = filter_var($_POST['equip_assign_teamid'],FILTER_SANITIZE_STRING);
    $region = filter_var($_POST['equip_assign_region'],FILTER_SANITIZE_STRING);
    $location = filter_var($_POST['equip_assign_location'],FILTER_SANITIZE_STRING);
    $classification = filter_var($_POST['equip_assign_classification'],FILTER_SANITIZE_STRING);
    $start_date = filter_var($_POST['equip_assign_start_date'],FILTER_SANITIZE_STRING);
    $end_date = filter_var($_POST['equip_assign_end_date'],FILTER_SANITIZE_STRING);
    $notes = filter_var(htmlentities($_POST['equip_assign_notes']),FILTER_SANITIZE_STRING);

    $updated_fields = ['equipmentid'=>$equipmentid, 'teamid'=>(empty($teamid) ? 0 : $teamid), 'region'=>$region, 'con_location'=>$location, 'classification'=>$classification];

    mysqli_query($dbc, "UPDATE `equipment` SET `region` = '$region', `location` = '$location', `classification` = '$classification' WHERE `equipmentid` = '$equipmentid'");

    if (empty($_POST['equipment_assignmentid']) ||$_POST['equipment_assignmentid'] == 'NEW') {
        $query = "INSERT INTO `equipment_assignment` (`equipmentid`, `clientid`, `teamid`, `region`, `location`, `classification`, `start_date`, `end_date`, `notes`) VALUES ('$equipmentid', '$clientid', '$teamid', '$region', '$location', '$classification', '$start_date', '$end_date', '$notes')";
        $result = mysqli_query($dbc, $query);
        $equipment_assignmentid = mysqli_insert_id($dbc);

        //Retrieve existing Tickets with this equipmentid and lands between the start and end dates
        $all_tickets_sql = "SELECT * FROM `tickets` WHERE `equipmentid` = '$equipmentid' AND DATE(`to_do_date`) >= '$start_date' AND (DATE(`to_do_date`) <= '$end_date' OR '$end_date' = '0000-00-00' OR '$end_date' = '') AND `deleted` = 0";
        $tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);
        $all_tickets_schedule_sql = "SELECT * FROM `ticket_schedule` WHERE `equipmentid` = '$equipmentid' AND DATE(`to_do_date`) >= '$start_date' AND (DATE(`to_do_date`) <= '$end_date' OR '$end_date' = '0000-00-00' OR '$end_date' = '') AND `deleted` = 0";
        $ticket_schedule = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_schedule_sql),MYSQLI_ASSOC);
    } else {
        $equipment_assignmentid = $_POST['equipment_assignmentid'];
        $query = "UPDATE `equipment_assignment` SET `equipmentid` = '$equipmentid', `clientid` = '$clientid', `teamid` = '$teamid', `region` = '$region', `location` = '$location', `classification` = '$classification', `start_date` = '$start_date', `end_date` = '$end_date', `notes` = '$notes' WHERE `equipment_assignmentid` = '$equipment_assignmentid'";
        $result = mysqli_query($dbc, $query);

        //Retrieve existing Tickets with this equipment_assignmentid and lands between the start and end dates
        $all_tickets_sql = "SELECT * FROM `tickets` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND DATE(`to_do_date`) >= '$start_date' AND (DATE(`to_do_date`) <= '$end_date' OR '$end_date' = '0000-00-00' OR '$end_date' = '') AND `deleted` = 0";
        $tickets = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_sql),MYSQLI_ASSOC);
        $all_tickets_schedule_sql = "SELECT * FROM `ticket_schedule` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND DATE(`to_do_date`) >= '$start_date' AND (DATE(`to_do_date`) <= '$end_date' OR '$end_date' = '0000-00-00' OR '$end_date' = '') AND `deleted` = 0";
        $ticket_schedule = mysqli_fetch_all(mysqli_query($dbc, $all_tickets_schedule_sql),MYSQLI_ASSOC);
    }

    mysqli_query($dbc, "DELETE FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid'");
	$restrict_assign = get_config($dbc, 'equip_multi_assign_staff_disallow');
    for ($i = 0; $i < count($_POST['equip_assign_contactid']); $i++) {
        $contact_position = $_POST['equip_assign_contact_position'][$i];
        $contactid = $_POST['equip_assign_contactid'][$i];
		if($restrict_assign > 0) {
			mysqli_query($dbc, "UPDATE `equipment_assignment_staff` LEFT JOIN `equipment_assignment` ON `equipment_assignment`.`equipment_assignmentid`=`equipment_assignment_staff`.`equipment_assignmentid` SET `equipment_assignment_staff`.`deleted`=1, `equipment_assignment_staff`.`date_of_archival`=DATE(NOW()) WHERE `equipment_assignment`.`start_date`='$start_date' AND `equipment_assignment`.`end_date`='$end_date' AND `equipment_assignment_staff`.`contactid`='$contactid' AND `equipment_assignment_staff`.`deleted`=0");
		}
        mysqli_query($dbc, "INSERT INTO `equipment_assignment_staff` (`equipment_assignmentid`, `contactid`, `contact_position`) VALUES ('$equipment_assignmentid', '$contactid', '$contact_position')");
    }
    for ($i = 0; $i < count($_POST['equip_assign_contractorid']); $i++) {
        $contractor_position = $_POST['equip_assign_contractor_position'][$i];
        $contractorid = $_POST['equip_assign_contractorid'][$i];
		if($restrict_assign > 0) {
			mysqli_query($dbc, "UPDATE `equipment_assignment_staff` LEFT JOIN `equipment_assignment` ON `equipment_assignment`.`equipment_assignmentid`=`equipment_assignment_staff`.`equipment_assignmentid` SET `equipment_assignment_staff`.`deleted`=1, `equipment_assignment_staff`.`date_of_archival`=DATE(NOW()) WHERE `equipment_assignment`.`start_date`='$start_date' AND `equipment_assignment`.`end_date`='$end_date' AND `equipment_assignment_staff`.`contactid`='$contractorid' AND `equipment_assignment_staff`.`deleted`=0");
		}
        mysqli_query($dbc, "INSERT INTO `equipment_assignment_staff` (`equipment_assignmentid`, `contactid`, `contact_position`, `contractor`) VALUES ('$equipment_assignmentid', '$contractorid', '$contractor_position', '1')");
    }

    //Update ticket list retrieved from above to the new data
    $equipassign_staff = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '$equipment_assignmentid' AND `deleted` = 0"),MYSQLI_ASSOC);
    $contact = [];
    foreach ($equipassign_staff as $staffid) {
        if(!in_array($staffid['contactid'], $contact)) {
            $contact[] = $staffid['contactid'];
        }
    }
    $team_staff = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
    foreach ($team_staff as $staffid) {
        if(!in_array($staffid['contactid'], $contact)) {
            $contact[] = $staffid['contactid'];
        }
    }
    $contact = implode(',',$contact);

    foreach ($tickets as $ticket) {
        mysqli_query($dbc, "UPDATE `tickets` SET `equipment_assignmentid` = '$equipment_assignmentid', `equipmentid` = '$equipmentid', `teamid` = '$teamid', `contactid` = ',$contact,', `region` = '$region', `con_location` = '$location', `classification` = '$classification' WHERE `ticketid` = '".$ticket['ticketid']."'");

        //Record history
        $ticket_histories = [];
        foreach($updated_fields as $key => $updated_field) {
            if($ticket[$key] != $updated_field) {
                $ticket_histories[$key] = "$key updated to $updated_field";
            }
        }
        $ea_contacts = [];
        foreach(explode(',', $contact) as $ea_contact) {
            if($ea_contact > 0) {
                $ea_contacts[] = get_contact($dbc, $ea_contact);
            }
        }
        $ticket_histories['equipment_assignmentid'] = "equipment_assignmentid updated to $equipment_assignmentid (".implode(', ',$ea_contacts).")";
        if(!empty($ticket_histories)) {
            mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `src`, `description`) VALUES ('{$ticket['ticketid']}','{$_SESSION['contactid']}','calendar','Row #{$ticket['ticketid']} of tickets updated: ".implode(', ',$ticket_histories)."')");
        }
    }
    foreach ($ticket_schedule as $ticket) {
        mysqli_query($dbc, "UPDATE `ticket_schedule` SET `equipment_assignmentid` = '$equipment_assignmentid', `equipmentid` = '$equipmentid', `teamid` = '$teamid', `contactid` = ',$contact,', `region` = '$region', `con_location` = '$location', `classification` = '$classification' WHERE `id` = '".$ticket['id']."'");

        //Record history
        $ticket_histories = [];
        foreach($updated_fields as $key => $updated_field) {
            if($ticket[$key] != $updated_field) {
                $ticket_histories[$key] = "$key updated to $updated_field";
            }
        }
        $ea_contacts = [];
        foreach(explode(',', $contact) as $ea_contact) {
            if($ea_contact > 0) {
                $ea_contacts[] = get_contact($dbc, $ea_contact);
            }
        }
        $ticket_histories['equipment_assignmentid'] = "equipment_assignmentid updated to $equipment_assignmentid (".implode(', ',$ea_contacts).")";
        if(!empty($ticket_histories)) {
            mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `userid`, `src`, `description`) VALUES ('{$ticket['ticketid']}','{$_SESSION['contactid']}','calendar','Row #{$ticket['id']} of ticket_schedule updated: ".implode(', ',$ticket_histories)."')");
        }
    }

    $query = $_GET;
    unset ($query['equipment_assignmentid']);
    echo '<script>window.location.replace("?'.http_build_query($query).'&equipment_assignmentid='.$equipment_assignmentid.'");</script>';
}

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
    $position_enabled = $get_field_config['position_enabled'];
    $enabled_fields = ','.$get_field_config['enabled_fields'].',';
}
?>
<script type="text/javascript">
$(document).on('change', 'select[name="equip_assign_equipmentid"]', function() { equipmentChange(); });
function equipmentAssignmentChange(equipment_assignmentid) {
    <?php
        $query = $_GET;
        unset ($query['equipment_assignmentid']);
    ?>
    if ($(equipment_assignmentid).val() == 'NEW') {
        location.replace("?<?= http_build_query($query) ?>&equipment_assignmentid=NEW");
    } else {
        location.replace("?<?= http_build_query($query) ?>&equipment_assignmentid=" + $(equipment_assignmentid).val());
    }
}
function updateEndDate(start_date) {
    if ($('[name="equip_assign_end_date"]').val() == '') {
        $('[name="equip_assign_end_date"]').val($(start_date).val());
        $('[name="equip_assign_end_date"]').trigger("change.select2");
    }
}
function addContact() {
    var block = $('div.contact-block').last();
    destroyInputs('.contact-block');
    clone = block.clone();

    clone.find('.form-control').val('');

    block.after(clone);
    initInputs('.contact-block');
}
function deleteContact(button) {
    if($('div.contact-block').length <= 1) {
        addContact();
    }
    $(button).closest('div.contact-block').remove();
}
function newEquipmentAssignment() {
    $.ajax({
        url: '../Calendar/equip_assign_inc.php?equipment_assignmentid=NEW&region=<?= $_GET['region'] ?>',
        method: 'POST',
        response: 'html',
        success: function(response) {
            $('.equip_assign_block').html(response);
            $('#equip_assign_header').text('New <?= $equipment_category ?> Assignment');
        }
    });
}
function equipmentChange() {
    var equipmentid = $('[name="equip_assign_equipmentid"]').val();
    $.ajax({
        url: '../Calendar/calendar_ajax_all.php?fill=retrieve_equipment_info&equipmentid='+equipmentid,
        type: 'GET',
        dataType: 'html',
        success: function(response) {
            var arr = response.split('*#*');
            console.log(arr);
            if($('[name="equip_assign_region"]').val() == '') {
                $('[name="equip_assign_region"]').val(arr[0]).trigger('change.select2');
            }
            if($('[name="equip_assign_location"]').val() == '') {
                $('[name="equip_assign_location"]').val(arr[1]).trigger('change.select2');
            }
            if($('[name="equip_assign_classification"]').val() == '') {
                $('[name="equip_assign_classification"]').val(arr[2]).trigger('change.select2');
            }
        }
    })
}
</script>

<h3>
    <span id="equip_assign_header"><?= $_GET['equipment_assignmentid'] > 0 ? 'Edit' : 'New' ?> <?= $equipment_category ?> Assignment</span>
    <a href="" onclick="newEquipmentAssignment(); return false;" class="btn brand-btn pull-right">New Assignment</a>
    <div class="clearfix"></div>
</h3>

<div class="block-group equip_assign_block" style="height: calc(100% - 4.5em); overflow-y: auto;">
<?php include('../Calendar/equip_assign_inc.php'); ?>
</div>