<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project'); ?>
<h3 id="head_agendas">Agendas</h3>
<div class="notice double-gap-top double-gap-bottom popover-examples">
    <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
    <div class="col-sm-11"><span class="notice-name">NOTE: </span>View, add and edit any past, present or future agendas created for this project.</div>
    <div class="clearfix"></div>
</div><?php
if(vuaed_visible_function($dbc, 'agenda_meeting') == 1) { ?>
    <div class="pull-right gap-bottom">
        <a class="cursor-hand" onclick="overlayIFrameSlider('../Agenda Meetings/add_agenda.php?projectid=<?= $projectid ?>', 'auto', false, false);"><img src="../img/icons/ROOK-add-icon.png" class="no-toggle" title="Add Agenda" /></a>
    </div>
    <div class="clearfix"></div><?php
}

$pending_active_tab = '';
$approved_active_tab = '';
$done_active_tab = '';
$tab_all = '';
$status = '';
if($_GET['category'] == 'Pending') {
    $pending_active_tab = 'active_tab';
	$status = 'Pending';
}
else if($_GET['category'] == 'Approved') {
    $approved_active_tab = 'active_tab';
	$status = 'Approved';
}
else if($_GET['category'] == 'Done') {
    $done_active_tab = 'active_tab';
	$status = 'Done';
}
else {
	$tab_all = 'active_tab';
}

/*
echo '<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="View, add and edit past, present and future agendas and meetings for this project."><img src="../img/info.png" width="20"></a></span>';
echo "<a href='?edit=".$projectid."&tab=agendas'><button type='button' class='btn brand-btn mobile-block ".$tab_all."'>All Statuses</button></a>&nbsp;&nbsp;</div>";


echo '<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="View and edit all pending agendas or meetings for this project."><img src="../img/info.png" width="20"></a></span>';
echo "<a href='?edit=".$projectid."&tab=agendas&category=Pending'><button type='button' class='btn brand-btn mobile-block ".$pending_active_tab."'>Pending</button></a>&nbsp;&nbsp;</div>";

echo '<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Review all approved agendas or meetings for this project."><img src="../img/info.png" width="20"></a></span>';
echo "<a href='?edit=".$projectid."&tab=agendas&category=Approved'><button type='button' class='btn brand-btn mobile-block ".$approved_active_tab."'>Approved</button></a>&nbsp;&nbsp;</div>";

echo '<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="View and edit all completed agendas or meetings for this project."><img src="../img/info.png" width="20"></a></span>';
echo "<a href='?edit=".$projectid."&tab=agendas&category=Done'><button type='button' class='btn brand-btn mobile-block ".$done_active_tab."'>Done</button></a>&nbsp;&nbsp;</div>";
echo "<br /><br />";
*/

$query_agenda = "SELECT * FROM `agenda_meeting` WHERE (`status`='$status' OR '$status' = '') AND `type`='Agenda' AND CONCAT(',',projectid,',') LIKE '%,$projectid,%' ORDER BY `date_of_meeting` DESC";

$result = mysqli_query($dbc, $query_agenda);
$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
    echo '<div id="no-more-tables"><table class="table table-bordered">';
    echo '<tr class="hidden-xs hidden-sm">
        <th>Agenda Date</th>
        <th>Agenda Topic</th>
        <th>Agenda Objective</th>
        <th>Agenda Status</th>
        <th>Details</th>
        </tr>';
		
	while($row = mysqli_fetch_array( $result )) {
		echo '<tr>';
		$agenda = $row['agendameetingid'];
		$date = $row['date_of_meeting'];
		$topic = $row['agenda_topic'];
		$objective = $row['meeting_objective'];
		$status = $row['status'];

		echo '<td data-title="Date">' .$date. '</td>';
		echo '<td data-title="Topic">' .$topic. '</td>';
		echo '<td data-title="Objective">' .$objective. '</td>';
		echo '<td data-title="Status">' .$status. '</td>';
		echo '<td data-title="Details">';
		if(vuaed_visible_function($dbc, 'agenda_meeting') == 1) {
			//echo '<a href="'.WEBSITE_URL.'/Agenda Meetings/add_agenda.php?agendameetingid='.$agenda.'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'"><img src="../img/icons/ROOK-edit-icon.png" class="no-toggle inline-img" title="View or Edit Details" /></a>';
			echo '<a class="cursor-hand" onclick="overlayIFrameSlider(\'../Agenda Meetings/add_agenda.php?agendameetingid='.$agenda.'&projectid='.$projectid.'\', \'auto\', false, false);"><img src="../img/icons/ROOK-edit-icon.png" class="no-toggle inline-img" title="View or Edit Details" /></a>';
		}
		echo '</td>';

		echo "</tr>";
	}

	echo '</table></div>';
} else {
    echo "<h4>No Agendas Found.</h4>";
} ?>

<div class="clearfix"></div>
<hr />