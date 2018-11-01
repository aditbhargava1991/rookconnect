<?php include_once('../include.php');
include_once('../Calendar/calendar_settings_inc.php');
include_once('../Calendar/calendar_functions_inc.php');

if($_GET['reload_sidebar'] == 1) {
    $calendar_start = $_GET['date'];
    if($calendar_start == '') {
        $calendar_start = date('Y-m-d');
    } else {
        $calendar_start = date('Y-m-d', strtotime($calendar_start));
    }
}

$teams = [];
if($_GET['view'] == 'monthly') {
    $month_start_date_check = date('Y-m-01', strtotime($calendar_start));
    $month_end_date_check = date('Y-m-t', strtotime($calendar_start));

    for($curr = $month_start_date_check; strtotime($curr) <= strtotime($month_end_date_check); $curr = date('Y-m-d', strtotime($curr.' + 1 day'))) {
        $calendar_dates[] = $curr;
    }
} else if($_GET['view'] == 'weekly') {
    $weekly_start = get_config($dbc, $_GET['type'].'_weekly_start');
    if($weekly_start == 'Sunday') {
        $weekly_start = 1;
    } else {
        $weekly_start = 0;
    }
    $day = date('w', strtotime($calendar_start));
    $week_start_date_check = date('Y-m-d', strtotime($calendar_start.' -'.($day - 1 + $weekly_start).' days'));
    $week_end_date_check = date('Y-m-d', strtotime($calendar_start.' -'.($day - 7 + $weekly_start).' days'));

    for($curr = $week_start_date_check; strtotime($curr) <= strtotime($week_end_date_check); $curr = date('Y-m-d', strtotime($curr.' + 1 day'))) {
        $calendar_dates[] = $curr;
    }
} else {
    $calendar_dates = [$calendar_start];
}
foreach($calendar_dates as $calendar_date) {
    $team_list = mysqli_query($dbc, "SELECT * FROM `teams` WHERE `deleted` = 0 AND (DATE(`start_date`) <= '$calendar_date') AND (DATE(`end_date`) >= '$calendar_date' OR `end_date` IS NULL OR `end_date` = '' OR `end_date` = '0000-00-00') AND CONCAT(',',IFNULL(`hide_days`,''),',') NOT LIKE '%,$calendar_date,%'".$region_query);
    while($row = mysqli_fetch_array($team_list)) {
        $teams[$row['teamid']] = $row;
    }
}
$active_teams = array_filter(explode(',',get_user_settings()['appt_calendar_teams']));
if($_GET['reload_sidebar'] == 1 && $_GET['teamid'] > 0) {
    $active_teams = [$_GET['teamid']];
} else if($_GET['mode'] == 'ticket_summary' || $_GET['mode'] == 'staff_summary') {
    $active_teams = [];
}
foreach($teams as $row) {
    $team_contactids = [];
    $team_name = get_team_name($dbc, $row['teamid'], '<br />');
    $team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` ='".$row['teamid']."' AND `deleted` = 0"),MYSQLI_ASSOC);
    if(!empty($team_contacts)) {
        foreach ($team_contacts as $team_contact) {
            if (get_contact($dbc, $team_contact['contactid'], 'category') == 'Staff') {
                $team_contactids[] = $team_contact['contactid'];
            }
        }
        $team_contactids = implode(',', $team_contactids);
        echo "<a href='' onclick='$(\"[id^=collapse_staff] .block-item\").removeClass(\"active\"); $(\"[id^=collapse_equipment] .block-item\").removeClass(\"active\"); $(\"#collapse_teams .block-item[data-teamid!=".$row['teamid']."]\").removeClass(\"active\"); $(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); resize_calendar_view".($_GET['view'] == 'monthly' || $_GET['mode'] == 'staff_summary' || $_GET['mode'] == 'ticket_summary' ? '_monthly' : '')."(); return false;'><div class='block-item ".(in_array($row['teamid'],$active_teams) ? 'active' : '')."' data-teamid='".$row['teamid']."' data-contactids='".$team_contactids."'><span style=''>$team_name</span></div></a>";
    }
} ?>