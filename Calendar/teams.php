<?php include_once('../include.php');
checkAuthorised();
include_once('../Calendar/calendar_functions_inc.php');
if (isset($_POST['submit'])) {
    $team_team_name = filter_var($_POST['team_name'],FILTER_SANITIZE_STRING);
    $region = filter_var($_POST['team_region'],FILTER_SANITIZE_STRING);
    $location = filter_var($_POST['team_location'],FILTER_SANITIZE_STRING);
    $classification = filter_var($_POST['team_classification'],FILTER_SANITIZE_STRING);
    $start_date = filter_var($_POST['team_start_date'],FILTER_SANITIZE_STRING);
    $end_date = filter_var($_POST['team_end_date'],FILTER_SANITIZE_STRING);
    $notes = filter_var(htmlentities($_POST['team_notes']),FILTER_SANITIZE_STRING);

    if (empty($_POST['teamid']) || $_POST['teamid'] == 'NEW') {
        $query = "INSERT INTO `teams` (`team_name`, `region`, `location`, `classification`, `start_date`, `end_date`, `notes`) VALUES ('$team_team_name', '$region', '$location', '$classification', '$start_date', '$end_date', '$notes')";
        $result = mysqli_query($dbc, $query);
        $teamid = mysqli_insert_id($dbc);
    } else {
        $teamid = $_POST['teamid'];
        $query = "UPDATE `teams` SET `team_name` = '$team_team_name', `region` = '$region', `location` = '$location', `classification` = '$classification', `start_date` = '$start_date', `end_date` = '$end_date', `notes` = '$notes' WHERE `teamid` = '$teamid'";
        $result = mysqli_query($dbc, $query);
    }

    mysqli_query($dbc, "DELETE FROM `teams_staff` WHERE `teamid` = '$teamid'");
    for ($i = 0; $i < count($_POST['team_contactid']); $i++) {
        $contact_position = $_POST['team_contact_position'][$i];
        $contactid = $_POST['team_contactid'][$i];
        if(!empty($contactid)) {
            mysqli_query($dbc, "INSERT INTO `teams_staff` (`teamid`, `contactid`, `contact_position`) VALUES ('$teamid', '$contactid', '$contact_position')");
        }
    }

    $query = $_GET;
    $query['subtab'] = 'team';
    unset ($query['teamid']);
    // echo '<script>window.location.replace("?'.http_build_query($query).'&teamid='.$teamid.'");</script>';
}
?>
<script type="text/javascript">
function teamChange(teamid) {
    var teamid = $(teamid).val();
    if ($(teamid).val() == 'NEW') {
        loadTeam('NEW');
    } else {
        loadTeam(teamid);
    }
}
function addContact() {
    var block = $('div.contact-block').last();
    clone = block.clone();

    clone.find('.form-control').val('');
    resetChosen(clone.find('select'));

    block.after(clone);
}
function deleteContact(button) {
    if($('div.contact-block').length <= 1) {
        addContact();
    }
    $(button).closest('div.contact-block').remove();
}
function loadTeam(teamid) {
    $.ajax({
        url: '../Calendar/teams_inc.php?teamid='+teamid+'&region=<?= $_GET['region'] ?>',
        method: 'POST',
        response: 'html',
        success: function(response) {
            $('.team_block').html(response);
            if(teamid == 'NEW') {
                $('#team_header').text('New Team');
            } else {
                $('#team_header').text('Edit Team');
            }
        }
    });
}
</script>

<h3 class="gap-left gap-right">
    <span id="team_header"><?= $_GET['teamid'] > 0 ? 'Edit' : 'New' ?> Team</span>
    <a href="" onclick="loadTeam('NEW'); return false;" class="btn brand-btn pull-right">New Team</a>
    <div class="clearfix"></div>
</h3>
<!-- <div align="right"><a href="#" class="block-label-sml<?= $active_schedule ?>">Schedule</a>
<a href="#" class="block-label-sml<?= $active_team ?>">Team</a></div> -->

<div class="team_block" style="height: calc(100% - 8em); overflow-y: auto;">
    <?php include('../Calendar/teams_inc.php'); ?>
</div>  