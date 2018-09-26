<!-- Daysheet Main Screen -->
<?php
include_once('../include.php');
include_once('../Profile/daysheet_functions.php');
//Configs
$user_settings = get_user_settings();
//Item Layout
$daysheet_styling = $user_settings['daysheet_styling'];
if(empty($daysheet_styling)) {
    $daysheet_styling = get_config($dbc, 'daysheet_styling');
}
if(empty($daysheet_styling)) {
    $daysheet_styling = 'card';
}
$daysheet_fields_config = $user_settings['daysheet_fields_config'];
if(empty($daysheet_fields_config)) {
    $daysheet_fields_config = explode(',', get_config($dbc, 'daysheet_fields_config'));
} else {
    $daysheet_fields_config = explode(',', $daysheet_fields_config);
}
$daysheet_weekly_config = $user_settings['daysheet_weekly_config'];
if(empty($daysheet_weekly_config)) {
    $daysheet_weekly_config = explode(',', get_config($dbc, 'daysheet_weekly_config'));
} else {
    $daysheet_weekly_config = explode(',', $daysheet_weekly_config);
}
$daysheet_button_config = $user_settings['daysheet_button_config'];
if(empty($daysheet_button_config)) {
    $daysheet_button_config = explode(',', get_config($dbc, 'daysheet_button_config'));
} else {
    $daysheet_button_config = explode(',', $daysheet_button_config);
}
$daysheet_rightside_views = $user_settings['daysheet_rightside_views'];
if(empty($daysheet_rightside_views)) {
    $daysheet_rightside_views = get_config($dbc, 'daysheet_rightside_views');
}
if(empty($daysheet_rightside_views)) {
    $daysheet_rightside_views = ['Journal','Weekly Overview','Monthly Overview'];
} else if($daysheet_rightside_views != '**ALL_OFF**') {
    $daysheet_rightside_views = explode(',', $daysheet_rightside_views);
}
$daysheet_ticket_fields = explode(',',get_config($dbc, 'daysheet_ticket_fields'));
$completed_ticket_status = get_config($dbc, 'auto_archive_complete_tickets');
$side_content = '';
if (!empty($_GET['side_content'])) {
    $side_content = $_GET['side_content'];
} else {
    if(in_array('Journal', $daysheet_rightside_views)) {
        $side_content = 'journal';
    } else if(in_array('Weekly Overview', $daysheet_rightside_views)) {
        $side_content = 'weekly';
    } else if(in_array('Monthly Overview', $daysheet_rightside_views)) {
        $side_content = 'monthly';
    }
}
$daily_date = date('Y-m-d');
if (!empty($_GET['daily_date'])) {
    $daily_date = $_GET['daily_date'];
} else {
    $_GET['daily_date'] = date('Y-m-d');
}
$contactid = $_SESSION['contactid'];
if (isset($_GET['daily_date'])) {
    $daily_date = $_GET['daily_date'];
}
$ticket_mode = get_config($dbc, 'daysheet_ticket_default_mode');
$ticket_action_mode = 0;
if($ticket_mode == 'action') {
    $ticket_action_mode = 1;
}

// $past_due_list = mysqli_query($dbc, "SELECT * FROM `daysheet_reminders` WHERE `date` < '".date('Y-m-d')."' AND `contactid` = '".$contactid."' AND `deleted` = 0 AND `done` = 0");
?>
<script type="text/javascript">
$(document).ready(function() {
    $('.status_opacity').find('a:first').css('opacity', '0.5');
});
function changeDailyDate(input) {
    var url = '?daily_date='+$(input).val()+'&side_content=<?= $_GET['side_content'] ?>&weekly_date=<?= $_GET['weekly_date'] ?>';
    window.location.href = url;
}
</script>
<div class="col-sm-12 gap-top main-screen-header">
    <input type="hidden" id="daily_date" value="<?= $_GET['daily_date'] ?>">
    <input type="hidden" id="side_content" value="<?= $_GET['side_content'] ?>">
    <input type="hidden" id="weekly_date" value="<?= $_GET['weekly_date'] ?>">
    <div class="col-sm-6">
    <?php if($_GET['tab'] == 'daysheet') { ?>

        <h1 style="display: inline; padding-left: 0;">Daily Overview&nbsp;&nbsp;</h1><div style="display: inline;"><input onchange="changeDailyDate(this);" style="width: 8em;" type="text" name="daily_date" class="datepicker" value="<?= date('Y-m-d', strtotime($daily_date)) ?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="?daily_date=<?= date('Y-m-d', strtotime($daily_date.' - 1 day')) ?>&side_content=<?= $side_content ?>&weekly_date=<?= $_GET['weekly_date'] ?>" class="mobile-anchor"><img style="height: 0.7em;" src="<?= WEBSITE_URL ?>/img/icons/back-arrow.png"></a> | <a href="?daily_date=<?= date('Y-m-d', strtotime($daily_date.' + 1 day')) ?>&side_content=<?= $side_content ?>&weekly_date=<?= $_GET['weekly_date'] ?>" class="mobile-anchor"><img style="height: 0.7em;" src="<?= WEBSITE_URL ?>/img/icons/next-arrow.png"></a></div>
        <h4><?= date('F d, Y', strtotime($daily_date)) ?></h4>

    <?php } ?>
    </div>
    <div class="col-sm-6 gap-top">
        <?php
            if (strpos($_SERVER['PHP_SELF'], 'Daysheet') === FALSE) {
        ?>
            <?php if (in_array('My Tasks', $daysheet_button_config)) { ?><a href="?daily_date=<?= $daily_date ?>&side_content=my_tasks" class="btn brand-btn pull-right mobile-anchor"><?= TASK_TILE ?></a><?php } ?>
            <?php if (in_array('My Checklists', $daysheet_button_config) && mysqli_num_rows(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` IN ('".implode("','", array_filter(explode(',',$user_settings['checklist_fav'])))."') AND (`assign_staff` LIKE '%,$contactid,%' OR `assign_staff`=',ALL,')")) > 0) { ?><a href="?daily_date=<?= $daily_date ?>&side_content=my_checklists" class="btn brand-btn pull-right mobile-anchor">CHECKLISTS</a><?php } ?>
            <?php if (in_array('My Tickets', $daysheet_button_config)) { ?><a href="?daily_date=<?= $daily_date ?>&side_content=my_tickets" class="btn brand-btn pull-right mobile-anchor"><?= strtoupper(TICKET_TILE) ?></a><?php } ?>
            <?php if (in_array('My Communications', $daysheet_button_config)) { ?><a href="?daily_date=<?= $daily_date ?>&side_content=my_communications" class="btn brand-btn pull-right mobile-anchor">COMMUNICATIONS</a><?php } ?>
            <?php if (in_array('My Sales', $daysheet_button_config)) { ?><a href="?daily_date=<?= $daily_date ?>&side_content=my_sales" class="btn brand-btn pull-right mobile-anchor"><?= strtoupper(SALES_TILE) ?></a><?php } ?>
            <?php if (in_array('My Projects', $daysheet_button_config)) { ?><a href="?daily_date=<?= $daily_date ?>&side_content=my_projects" class="btn brand-btn pull-right mobile-anchor"><?= strtoupper(PROJECT_TILE) ?></a><?php } ?>
        <?php } ?>

        <?php if ($_GET['tab'] == 'daysheet') { ?><a href="?daily_date=<?= $daily_date ?>&side_content=notifications" class="btn brand-btn pull-right mobile-anchor">Notifications <?= $noti_count > 0 ? '<span style="font-weight: bold; color: red;">('.$noti_count.')</span>' : '('.$noti_count.')' ?></a><?php } ?>
        <?php if ($num_rows_past > 0) { ?><a href="?daily_date=<?= $daily_date ?>&side_content=past_due" class="btn brand-btn pull-right mobile-anchor" style="background: red; background-color: red;">PAST DUE ALERTS</a><?php } ?>
    </div>
</div>
<div class="clearfix"></div><?php

$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='projects_planner'"));
$note = $notes['note'];
if ( !empty($note) ) { ?>
    <div class="notice popover-examples">
        <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11"><span class="notice-name">NOTE:</span>
        <?= $note ?></div>
        <div class="clearfix"></div>
    </div><?php
} ?>

<div class="col-sm-12 main-screen-details">
    <?php if($_GET['tab'] == 'daysheet') { ?>
    <div class="col-sm-<?= $daysheet_rightside_views == '**ALL_OFF**' && empty($side_content) ? '12' : '6' ?>">
        <div class="sidebar" style="padding: 1em; margin: 0 auto; overflow-y: auto;">
            <?php include('daysheet_daily.php'); ?>
        </div>
    </div>
    <?php } ?>
    <?php if($daysheet_rightside_views != '**ALL_OFF**' || !empty($side_content)) { ?>
        <?php if($_GET['tab'] == 'daysheet') { ?>
        <div class="col-sm-6">
        <?php } else { ?>
        <div class="col-sm-12">
        <?php }?>

            <div class="col-sm-12">
                <div class="pull-right">
                    <?php
                    if ($_GET['tab'] == 'daysheet' || $_GET['side_content'] == 'weekly' || $_GET['side_content'] == 'monthly') { ?>
                    <a href="?daily_date=<?= $daily_date ?>&side_content=monthly" class="mobile-anchor"><img src="<?= WEBSITE_URL ?>/img/month-overview-blue.png" class="pull-right inline-img no-toggle <?= $side_content == 'monthly' ? '' : 'black-color' ?>" title="Monthly Overview"></a>
                    <a href="?daily_date=<?= $daily_date ?>&side_content=weekly" class="mobile-anchor"><img src="<?= WEBSITE_URL ?>/img/weekly-overview-blue.png" class="pull-right inline-img no-toggle <?= !in_array($side_content,['monthly','journal','my_projects','my_tickets','my_tasks','my_checklists','my_communications','my_sales','past_due','notifications']) ? '' : 'black-color' ?>" title="Weekly Overview"></a>
                    <a href="?daily_date=<?= $daily_date ?>&side_content=journal" class="mobile-anchor"><img src="<?= WEBSITE_URL ?>/img/notepad-icon-blue.png" class="pull-right inline-img no-toggle <?= $side_content == 'journal' ? '' : 'black-color' ?>" title="Journal"></a>
                    <?php } ?>


                    <?php
                    if ($_GET['tab'] == 'projects') { ?>
                            <button class='btn brand-btn'><a href="../Project/projects.php?tile_name=project">Projects
                            </a></button>
                    <?php } ?>

                    <?php
                    if ($_GET['tab'] == 'checklists') { ?>
                    <a href="?tab=checklists&daily_date=<?= $daily_date ?>&side_content=my_checklists&date_display=monthly" class="mobile-anchor"><img src="<?= WEBSITE_URL ?>/img/month-overview-blue.png" class="pull-right inline-img no-toggle <?= $side_content == 'monthly' ? '' : 'black-color' ?>" title="Monthly Overview"></a>

                    <a href="?tab=checklists&daily_date=<?= $daily_date ?>&side_content=my_checklists&date_display=weekly" class="mobile-anchor"><img src="<?= WEBSITE_URL ?>/img/weekly-overview-blue.png" class="pull-right inline-img no-toggle <?= !in_array($side_content,['weekly','journal','my_projects','my_tickets','my_tasks','my_checklists','my_communications','my_sales','past_due','notifications']) ? '' : 'black-color' ?>" title="Weekly Overview"></a>
                    <?php } ?>

                    <?php
                    if ($_GET['tab'] == 'communications') { ?>
                    <a href="?tab=communications&daily_date=<?= $daily_date ?>&side_content=my_communications&date_display=monthly" class="mobile-anchor"><img src="<?= WEBSITE_URL ?>/img/month-overview-blue.png" class="pull-right inline-img no-toggle <?= $side_content == 'monthly' ? '' : 'black-color' ?>" title="Monthly Overview"></a>

                    <a href="?tab=communications&daily_date=<?= $daily_date ?>&side_content=my_communications&date_display=weekly" class="mobile-anchor"><img src="<?= WEBSITE_URL ?>/img/weekly-overview-blue.png" class="pull-right inline-img no-toggle <?= !in_array($side_content,['weekly','journal','my_projects','my_tickets','my_tasks','my_checklists','my_communications','past_due','notifications']) ? '' : 'black-color' ?>" title="Weekly Overview"></a>
                    <?php } ?>

                    <?php if ($_GET['tab'] == 'sales') { ?>
                    <a href="?tab=saless&daily_date=<?= $daily_date ?>&side_content=my_saless&date_display=monthly" class="mobile-anchor"><img src="<?= WEBSITE_URL ?>/img/month-overview-blue.png" class="pull-right inline-img no-toggle <?= $side_content == 'monthly' ? '' : 'black-color' ?>" title="Monthly Overview"></a>

                    <a href="?tab=saless&daily_date=<?= $daily_date ?>&side_content=my_saless&date_display=weekly" class="mobile-anchor"><img src="<?= WEBSITE_URL ?>/img/weekly-overview-blue.png" class="pull-right inline-img no-toggle <?= !in_array($side_content,['weekly','journal','my_projects','my_tickets','my_tasks','my_checklists','my_communications','my_sales','past_due','notifications']) ? '' : 'black-color' ?>" title="Weekly Overview"></a>
                    <?php } ?>

                </div>
                <div class="scale-to-fill weekly-overview-header">
                    <?php if ($side_content == 'my_tags') { ?>
                        <h1 class="no-margin">Tags</h1>
                    <?php } else if ($side_content == 'contact_form') {
                        $form_id = $_GET['form_id'];
                        $contact_form = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id` = '$form_id'"));
                        $attached_contact = $_GET['attached_contactid']; ?>
                        <h1 class="no-margin"><?= $contact_form['name'] ?> - <?= !empty(get_client($dbc, $match_contact)) ? get_client($dbc, $match_contact) : get_contact($dbc, $match_contact) ?></h1>
                    <?php } else if ($side_content == 'my_shifts') { ?>
                        <h1 class="no-margin">Shifts</h1>
                    <?php } else if ($side_content == 'notifications') { ?>
                        <h1 class="no-margin">Notifications</h1>
                    <?php } else if ($side_content == 'journal') { ?>
                        <h1 class="no-margin">Journal for <?= date('F jS', strtotime($_GET['daily_date'])) ?></h1>
                    <?php } else if ($side_content == 'my_projects') { ?>
                        <h1 class="no-margin"><?= PROJECT_TILE ?></h1>
                    <?php } else if ($side_content == 'my_tickets') { ?>
                        <h1 class="no-margin"><?= TICKET_TILE ?></h1>
                    <?php } else if ($side_content == 'my_checklists') { ?>
                        <h1 class="no-margin">Checklists</h1>
                    <?php } else if ($side_content == 'my_tasks') { ?>
                        <h1 class="no-margin"><?= TASK_TILE ?></h1>
                    <?php } else if ($side_content == 'my_communications') { ?>
                        <h1 class="no-margin">Communications</h1>
                    <?php } else if ($side_content == 'my_sales') { ?>
                        <h1 class="no-margin"><?= SALES_TILE ?></h1>
                    <?php } else if ($side_content == 'my_timesheets') { ?>
                        <h1 class="no-margin">Time Sheets</h1>
                    <?php } else if ($side_content == 'past_due' && $_GET['tab'] == 'alerts') { ?>
                        <h1 class="no-margin">Alerts</h1>
                    <?php } else if ($side_content == 'past_due') { ?>
                        <h1 class="no-margin">Past Due Alerts</h1>
                    <?php } else if($side_content == 'monthly') {
                        $weekly_date = date('Y-m-d',strtotime($daily_date));
                        if (!empty($_GET['weekly_date'])) {
                            $weekly_date = $_GET['weekly_date'];
                        } ?>
                        <h1 class="no-margin">Monthly Overview</h1>
                        <h4><?= date('F 1', strtotime($weekly_date)) ?> - <?= date('F t, Y', strtotime($weekly_date)) ?>
    					<a href="?daily_date=<?= $daily_date ?>&side_content=monthly&weekly_date=<?= date('Y-m-d', strtotime($weekly_date.' - 1 month')) ?>" class="mobile-anchor"><img style="height: 0.7em;" src="<?= WEBSITE_URL ?>/img/icons/back-arrow.png"></a> |
    					<a href="?daily_date=<?= $daily_date ?>&side_content=monthly&weekly_date=<?= date('Y-m-d', strtotime($weekly_date.' + 1 month')) ?>" class="mobile-anchor"><img style="height: 0.7em;" src="<?= WEBSITE_URL ?>/img/icons/next-arrow.png"></a></h4>
                    <?php } else if($side_content == 'my_support') {
						echo '<h1>Support Requests</h1>';
					} else {
                        $weekly_date = date('Y-m-d',strtotime($daily_date));
                        if (!empty($_GET['weekly_date'])) {
                            $weekly_date = $_GET['weekly_date'];
                        }
                        $day = date('w');
                        $week_start = date('F j', strtotime($weekly_date.' -'.($day - 1).' days'));
                        $week_end = date('F j, Y', strtotime($weekly_date.' -'.($day - 7).' days'));
                        ?>
                        <h1 class="no-margin">Weekly Overview</h1>
                        <h4><?= $week_start ?> - <?= $week_end ?>
    					<a href="?daily_date=<?= $daily_date ?>&side_content=weekly&weekly_date=<?= date('Y-m-d', strtotime($weekly_date.' - 7 days')) ?>" class="mobile-anchor"><img style="height: 0.7em;" src="<?= WEBSITE_URL ?>/img/icons/back-arrow.png"></a> |
    					<a href="?daily_date=<?= $daily_date ?>&side_content=weekly&weekly_date=<?= date('Y-m-d', strtotime($weekly_date.' + 7 days')) ?>" class="mobile-anchor"><img style="height: 0.7em;" src="<?= WEBSITE_URL ?>/img/icons/next-arrow.png"></a></h4>
                    <?php } ?>
                </div>
            </div>
            <div class="clearfix"></div>
            <div class="sidebar weekly" style="padding: 1em; margin: 0 auto; overflow-y: auto; <?= $_GET['tab'] == 'tickets' ? ' background-color: transparent;' : '' ?>">
                <?php if($side_content == 'my_tags') {
                    include('daysheet_tags.php');
                } else if($side_content == 'contact_form') {
                    include('daysheet_contact_form.php');
                } else if($side_content == 'my_shifts') {
                    include('daysheet_shifts.php');
                } else if($side_content == 'notifications') {
                    include('daysheet_notifications.php');
                } else if ($side_content == 'journal') {
                    include('daysheet_notepad.php');
                } else if ($side_content == 'my_projects') {
                    include('daysheet_projects.php');
                } else if ($side_content == 'my_tickets') {
                    include('daysheet_tickets.php');
                } else if ($side_content == 'my_tasks') {
                    include('daysheet_tasks.php');
                } else if ($side_content == 'my_checklists') {
                    include('daysheet_checklists.php');
                } else if ($side_content == 'my_timesheets') {
                    include('daysheet_timesheets.php');
                } else if ($side_content == 'my_support') {
                    include('daysheet_support.php');
                } else if ($side_content == 'my_communications') {
                    include('daysheet_communications.php');
                } else if ($side_content == 'my_sales') {
                    include('daysheet_sales.php');
                } else if ($side_content == 'past_due') {
                    include('daysheet_pastdue.php');
                } else {
                    include('daysheet_weekly.php');
                } ?>
            </div>
        </div>
    <?php } ?>
</div>

<input type="hidden" name="daysheet_contactid" value="<?= $contactid ?>">