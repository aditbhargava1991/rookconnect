<?php if(!empty($_GET['q'])) {
	$query = filter_var($_GET['q'],FILTER_SANITIZE_STRING);
	$scrum_list = $dbc->query("SELECT * FROM `daysheet_notepad` WHERE `contactid`=0 AND `date` != '' AND `notes` LIKE '%$query%' ORDER BY `date` DESC");
	if($scrum_list->num_rows > 0) { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Entry</th>
				<th>Staff</th>
				<th>Date Created</th>
			</tr>
			<?php while($notes = $scrum_list->fetch_assoc()) {
				$note = strip_tags(html_entity_decode($notes['notes']));
				$offset = strrpos($note,' ',150); ?>
				<tr>
					<td data-title="Entry"><a href="?tab=notes&date=<?= $notes['date'] ?>"><?= substr($note, 0, $offset ?: 150) ?></a></td>
					<td data-title="Staff"><?php $staff_list = [];
					foreach(array_filter(explode(',',$notes['assigned'])) as $staff) {
						$staff_list[] = get_contact($dbc, $staff);
					}
					echo implode(', ',$staff_list); ?></td>
					<td data-title="Date Created"><a href="?tab=notes&date=<?= $notes['date'] ?>"><?= $notes['date'].' '.$notes['start_time'].(!empty($notes['start_time']) && !empty($notes['end_time']) ? ' - ' : '').$notes['end_time'] ?></a></td>
				</tr>
			<?php } ?>
		</table>
	<?php } else {
		echo '<h3>No Notes Found</h3>';
	}
} else {
	include('scrum_notes.php');
}
