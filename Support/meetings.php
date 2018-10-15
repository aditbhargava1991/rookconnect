<?php include_once('config.php'); ?>
<div class="notice double-gap-bottom popover-examples">
	<img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" style="width:3em;">
	<div style="float:right; width:calc(100% - 4em);"><span class="notice-name">Note:</span>
	To ensure transparency, all Agendas and Meetings logged with your business will be stored here for future reference.</div>
	<div class="clearfix"></div>
</div>
<?php $meeting_list = mysqli_query($dbc_support, "SELECT * FROM `agenda_meeting` WHERE `businessid`='$user'");
if(mysqli_num_rows($meeting_list) > 0) { ?>
	<div id="no-more-tables">
		<table class="table table-bordered">
			<tr class="hidden-xs hidden-sm">
				<th>Type</th>
				<th>Topic</th>
				<th>Time</th>
				<th>Items to Bring</th>
				<th>Objective</th>
				<th>Attendees</th>
			</tr>
			<?php while($row = mysqli_fetch_array($meeting_list)) {
				echo "<tr>";
					echo "<td data-title='Type'>".$row['type']."</td>";
					if($row['type'] == 'Agenda') {
						echo "<td data-title='Agenda Topic'>".$row['agenda_topic']."</td>";
						echo "<td data-title='Date &amp; Time'>".$row['date_of_meeting']."<br />".$row['time_of_meeting'].(!empty($row['end_time_of_meeting']) ? ' - '.$row['end_time_of_meeting'] : '')."</td>";
						echo "<td data-title='Items to Bring'>".$row['agenda_topic']."</td>";
					} else {
						echo "<td data-title='Meeting Topic'>".$row['meeting_topic']."</td>";
						echo "<td data-title='Date &amp; Time'>".$row['date_of_meeting']."<br />".$row['time_of_meeting'].(!empty($row['end_time_of_meeting']) ? ' - '.$row['end_time_of_meeting'] : '')."</td>";
						echo "<td data-title='Items to Bring'>".$row['agenda_topic']."</td>";
					}
					echo "<td data-title='Objectives'>".$row['meeting_objective']."</td>";
					echo "<td data-title='Attendees'>";
					$attendees = array_filter(array_merge(explode(',',$row['businesscontactid']),explode(',',$row['companycontactid'])));
					$attend_list = [];
					foreach($attendees as $id) {
						$attend_list[] = get_contact($dbc_support, $id);
					}
					echo implode('<br />',$attend_list)."</td>";
				echo "</tr>";
			} ?>
		</table>
	</div>
<?php } else {
	echo "<h3>No Meetings or Agendas Found</h3>";
}