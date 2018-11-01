<?php
if(vuaed_visible_function($dbc, 'agenda_meeting') == 1) {
        echo '<div class="pull-right"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to add an Agenda."><img src="../img/info.png" width="20"></a></span>';
		echo '<a id="'.$projectid.'" href="'.WEBSITE_URL.'/Agenda Meetings/add_agenda.php?projectid='.$projectid.'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="btn brand-btn mobile-block">Add Agenda</a></div>';
        
        echo '<div class="pull-right"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Meeting."><img src="../img/info.png" width="20"></a></span>';
		echo '<a id="'.$projectid.'" href="'.WEBSITE_URL.'/Agenda Meetings/add_meeting.php?projectid='.$projectid.'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" class="btn brand-btn mobile-block">Add Meeting</a></div>';
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

echo '<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="View, add and edit past, present and future agendas and meetings for this project."><img src="../img/info.png" width="20"></a></span>';
echo "<a href='review_project.php?type=meeting&projectid=".$projectid."&from_url=".urlencode($_GET['from_url'])."'><button type='button' class='btn brand-btn mobile-block ".$tab_all."'>All Statuses</button></a>&nbsp;&nbsp;</div>";


echo '<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="View and edit all pending agendas or meetings for this project."><img src="../img/info.png" width="20"></a></span>';
echo "<a href='review_project.php?type=meeting&projectid=".$projectid."&category=Pending&from_url=".urlencode($_GET['from_url'])."'><button type='button' class='btn brand-btn mobile-block ".$pending_active_tab."'>Pending</button></a>&nbsp;&nbsp;</div>";

echo '<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Review all approved agendas or meetings for this project."><img src="../img/info.png" width="20"></a></span>';
echo "<a href='review_project.php?type=meeting&projectid=".$projectid."&category=Approved&from_url=".urlencode($_GET['from_url'])."'><button type='button' class='btn brand-btn mobile-block ".$approved_active_tab."'>Approved</button></a>&nbsp;&nbsp;</div>";

echo '<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="View and edit all completed agendas or meetings for this project."><img src="../img/info.png" width="20"></a></span>';
echo "<a href='review_project.php?type=meeting&projectid=".$projectid."&category=Done&from_url=".urlencode($_GET['from_url'])."'><button type='button' class='btn brand-btn mobile-block ".$done_active_tab."'>Done</button></a>&nbsp;&nbsp;</div>";
echo "<br /><br />";

$query_agenda = "SELECT * FROM `agenda_meeting` WHERE (`status`='$status' OR '$status' = '') AND `type`='Agenda' AND CONCAT(',',projectid,',') LIKE '%,$projectid,%' ORDER BY `date_of_meeting` DESC";
$query_meeting = "SELECT * FROM `agenda_meeting` WHERE (`status`='$status' OR '$status' = '') AND `type`='Meeting' AND CONCAT(',',projectid,',') LIKE '%,$projectid,%' ORDER BY `date_of_meeting` DESC";

$result = mysqli_query($dbc, $query_agenda);
$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
    echo '<div id="no-more-tables"><table class="table table-bordered">';
    echo '<tr class="hidden-xs hidden-sm">
        <th>Agenda Date</th>
        <th>Agenda Topic</th>
        <th>Agenda Objective</th>
        <th>Agenda Status</th>
        <th>Agenda Function</th>
        </tr>';
		
	while($row = mysqli_fetch_array( $result )) {
		echo '<tr>';
		$agenda = $row['agendameetingid'];
		$date = $row['date_of_meeting'];
		$topic = $row['agenda_topic'];
		$objective = $row['meeting_objective'];
		$status = $row['status'];

		echo '<td data-title="Agenda Date">' .$date. '</td>';
		echo '<td data-title="Agenda Topic">' .$topic. '</td>';
		echo '<td data-title="Agenda Objective">' .$objective. '</td>';
		echo '<td data-title="Agenda Status">' .$status. '</td>';
		echo '<td data-title="Function">';
		if(vuaed_visible_function($dbc, 'agenda_meeting') == 1) {
			 echo '<a href="'.WEBSITE_URL.'/Agenda Meetings/add_agenda.php?agendameetingid='.$agenda.'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Edit</a>';
		}
		echo '</td>';

		echo "</tr>";
	}

	echo '</table>';
} else {
    echo "<h2>No Agendas Found.</h2>";
}

$result = mysqli_query($dbc, $query_meeting);
$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
    echo '<div id="no-more-tables"><table class="table table-bordered">';
    echo '<tr class="hidden-xs hidden-sm">
        <th>Meeting Date</th>
        <th>Meeting Topic</th>
        <th>Meeting Objective</th>
        <th>Meeting Status</th>
        <th>Function</th>
        </tr>';
		
	while($row = mysqli_fetch_array( $result )) {
		echo '<tr>';
		$meeting = $row['agendameetingid'];
		$date = $row['date_of_meeting'];
		$topic = $row['agenda_topic'];
		$objective = $row['meeting_objective'];
		$status = $row['status'];

		echo '<td data-title="Meeting Date">' .$date. '</td>';
		echo '<td data-title="Meeting Topic">' .$topic. '</td>';
		echo '<td data-title="Meeting Objective">' .$objective. '</td>';
		echo '<td data-title="Meeting Status">' .$status. '</td>';
		echo '<td data-title="Function">';
		if(vuaed_visible_function($dbc, 'agenda_meeting') == 1) {
			 echo '<a href="'.WEBSITE_URL.'/Agenda Meetings/add_meeting.php?agendameetingid='.$meeting.'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Edit</a>';
		}
		echo '</td>';

		echo "</tr>";
	}

	echo '</table>';
} else {
    echo "<h2>No Meetings Found.</h2>";
}
