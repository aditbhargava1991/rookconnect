<script>
function view_history() {
	if($('.panel-heading:contains("View Profile")').not(':visible')) {
		$('#view_history').show();
		$('#view_profile').hide();
		$('#edit_profile').hide();
	}
	scrollScreen();
}
</script>
<button onclick="edit_profile(); return false;" class="btn brand-btn pull-right">Edit Contact</button>
<h4>Contact History</h4>
<div id="no-more-tables">
	<table class="table table-bordered">
		<tr class="hidden-sm hidden-xs">
			<th>Date / Time</th>
			<th>User</th>
			<th>History</th>
		</tr>
		<?php $history = mysqli_query($dbc, "SELECT * FROM `contacts_history` WHERE `contactid`='".$_GET['edit']."'");
		while($history_row = mysqli_fetch_array($history)) { ?>
			<tr>
				<td data-title="Date / Time"><?= convert_timestamp_mysql($dbc, date('Y-m-d g:i A', strtotime($history_row['updated_at'])), true) ?></td>
				<td data-title="User"><?= $history_row['updated_by'] ?></td>
				<td data-title="History"><?= html_entity_decode($history_row['description']) ?></td>
			</tr>
		<?php } ?>
	</table>
</div>