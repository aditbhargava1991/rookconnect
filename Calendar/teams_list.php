<?php include_once('../include.php'); ?>
<script type="text/javascript">
function archiveTeam(a, teamid) {
	$.ajax({
		url: '../Calendar/calendar_ajax_all.php?fill=archive_team&teamid='+teamid,
		method: 'GET',
		success: function(response) {
			$(a).closest('tr').remove();
		}
	});
}
</script>
<a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Calendar/teams.php?teamid=NEW'); return false;" class="btn brand-btn pull-right gap-bottom">Add Team</a><div class="clearfix"></div>
<?php
$teams = get_teams($dbc);
$team_fields = ',start_date,end_date,';
$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_teams`"));
if (!empty($get_field_config)) {
    $team_fields = ','.$get_field_config['team_fields'].',';
}
if(!empty($teams)) { ?>
	<div id="no-more-tables">
		<table class="table table-bordered">
			<tr class="hidden-xs">
				<th>Team</th>
				<th>Staff</th>
				<?php if(strpos($team_fields, ',start_date,') !== FALSE) { ?>
					<th>Start Date</th>
				<?php } ?>
				<?php if(strpos($team_fields, ',end_date,') !== FALSE) { ?>
					<th>End Date</th>
				<?php } ?>
				<?php if(strpos($team_fields, ',region,') !== FALSE) { ?>
					<th>Region</th>
				<?php } ?>
				<?php if(strpos($team_fields, ',location,') !== FALSE) { ?>
					<th>Location</th>
				<?php } ?>
				<?php if(strpos($team_fields, ',classification,') !== FALSE) { ?>
					<th>Classification</th>
				<?php } ?>
				<?php if(strpos($team_fields, ',notes,') !== FALSE) { ?>
					<th>Notes</th>
				<?php } ?>
				<th>Function</th>
			</tr>
			<?php foreach($teams as $team) { ?>
				<tr>
					<td data-title="Team"><?= get_team_name($dbc, $team['teamid']) ?></td>
					<td data-title="Staff"><?= get_team_name($dbc, $team['teamid'], '<br />', 1) ?></td>
					<?php if(strpos($team_fields, ',start_date,') !== FALSE) { ?>
						<td data-title="Start Date"><?= in_array($team['start_date'], ['','0000-00-00']) ? 'Ongoing' : $team['start_date'] ?></td>
					<?php } ?>
					<?php if(strpos($team_fields, ',end_date,') !== FALSE) { ?>
						<td data-title="End Date"><?= in_array($team['end_date'], ['','0000-00-00']) ? 'Ongoing' : $team['end_date'] ?></td>
					<?php } ?>
					<?php if(strpos($team_fields, ',region,') !== FALSE) { ?>
						<td data-title="Region"><?= $team['region'] ?></td>
					<?php } ?>
					<?php if(strpos($team_fields, ',location,') !== FALSE) { ?>
						<td data-title="Location"><?= $team['location'] ?></td>
					<?php } ?>
					<?php if(strpos($team_fields, ',classification,') !== FALSE) { ?>
						<td data-title="Classification"><?= $team['classification'] ?></td>
					<?php } ?>
					<?php if(strpos($team_fields, ',notes,') !== FALSE) { ?>
						<td data-title="Notes"><?= html_entity_decode($team['notes']) ?></td>
					<?php } ?>
					<td data-title="Function"><a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Calendar/teams.php?teamid=<?= $team['teamid'] ?>'); return false;">Edit</a> | <a href="" onclick="archiveTeam(this, '<?= $team['teamid'] ?>'); return false;">Delete</a></td>
				</tr>
			<?php } ?>
		</table>
	</div>
<?php } else {
	echo '<h4>No Teams Found.</h4>';
} ?>
<a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Calendar/teams.php?teamid=NEW'); return false;" class="btn brand-btn pull-right">Add Team</a>