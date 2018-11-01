<?php $main_tasks = explode(',',$get_ticket['task_available']);
$project_tasks = mysqli_query($dbc, "SELECT `description`, `src_table`, `src_id` FROM `project_scope` WHERE `projectid`='".$get_ticket['projectid']."' AND (`description` != '' OR (`src_table`='services' AND `src_id` > 0))");
while($project_task = mysqli_fetch_assoc($project_tasks)) {
	if($project_task['src_id'] > 0) {
		$main_tasks[] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `heading` FROM `services` WHERE `serviceid`='".$project_task['src_id']."'"))['heading'];
	} else {
		$main_tasks[] = $project_task['description'];
	}
}
$main_tasks = array_filter($main_tasks);
foreach($main_tasks as $i => $task_name) {
	$main_tasks[$i] = html_entity_decode($task_name);
} ?>
<input type="hidden" name="contactid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $get_ticket['contactid'] ?>">
<?php if($access_staff === TRUE) { ?>
	<div class="staff_tasks_add">
		<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Staff</h3>') ?>
		<div class="form-group">
			<?php foreach(get_teams($dbc, " AND IF(`end_date` = '0000-00-00','9999-12-31',`end_date`) >= '".date('Y-m-d')."'") as $team) {
				$team_staff = get_team_contactids($dbc, $team['teamid']); ?>
				<button class="btn brand-btn" onclick="<?php foreach($team_staff as $i => $staff) {
					if($staff > 0) {
						echo "$('[name=staff_task_contact][value=".$staff."]').prop('checked',true).change();";
					}
				} ?>return false;"><?= get_team_name($dbc, $team['teamid']) ?></button>
			<?php } ?>
		</div>
		<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND `deleted`=0")) as $staff) { ?>
			<label class="form-checkbox"><input type="checkbox" name="staff_task_contact" value="<?= $staff['contactid'] ?>" onchange="staff_list_add(this);"> <?= $staff['first_name'].' '.$staff['last_name'] ?></label>
		<?php } ?>
		<h3>Task</h3>
		<?php $extra_tasks = 0;
		$groups = $dbc->query("SELECT `category` FROM `task_types` WHERE `deleted`=0 GROUP BY `category` ORDER BY MIN(`sort`), MIN(`id`)");
		while($task_group_name = $groups->fetch_assoc()['category']) {
			echo "<div class='billing_group'><h4 style='".((count($main_tasks) > 0 && !in_array_any($task_group,$main_tasks)) || (strpos($value_config,',Ticket Tasks Ticket Type,') !== FALSE && config_safe_str($task_group[0]) != $ticket_type) ? 'display:none;' : '')."'>".$task_group[0]."</h4>";
			$task_list = $dbc->query("SELECT `id`, `description` FROM `task_types` WHERE `deleted`=0 AND `category`='$task_group_name' ORDER BY `sort`, `id`");
			while($task = $task_list->fetch_assoc()['description']) {
				if((count($main_tasks) == 0 || in_array($task,$main_tasks)) && ((strpos($value_config,',Ticket Tasks Ticket Type,') !== FALSE && config_safe_str($task_group_name) == $ticket_type) || strpos($value_config, ',Ticket Tasks Ticket Type,') === FALSE)) { ?>
					<label class="form-checkbox"><input type="checkbox" name="staff_assigned_task" value="<?= $task ?>" onchange="task_list_add(this, false);"> <?= $task ?></label>
				<?php } else {
					$extra_tasks++; ?>
					<label class="form-checkbox" style="display:none;"><input type="checkbox" name="staff_assigned_task" data-task-type="extra" data-task-group="<?= $task_group_name ?>" disabled value="<?= $task ?>" onchange="task_list_add(this, <?= (strpos($value_config, ',Ticket Tasks Groups,') !== FALSE || strpos($value_config, ',Ticket Tasks Ticket Type,') !== FALSE) ? 'true' : 'false' ?>);"> <?= $task ?> (Extra Billing)</label>
				<?php }
			}
			echo "</div>";
		} ?>
		<?php if(strpos($value_config,',Ticket Tasks Add Button,') !== FALSE || strpos($value_config,',Ticket Tasks Auto Check In,') === FALSE) { ?>
			<button class="btn brand-btn pull-left assign_staff_task" data-task-group="" onclick="add_staff_task(); return false;">Add Selected Staff With Task</button>
		<?php } ?>
		<?php if(strpos($value_config,',Ticket Tasks Auto Check In,') !== FALSE) { ?>
			<button class="btn brand-btn pull-left assign_staff_task" onclick="add_staff_task('<?= strpos($value_config,',Ticket Tasks Auto Load New,') !== FALSE ? 'open_ticket' : 'checkin' ?>');">Get to Work</button>
		<?php } ?>
		<?php if($extra_tasks > 0) { ?>
			<button class="btn brand-btn pull-right" data-history-label="Extra Billing Option" onclick="$('[name=staff_assigned_task][data-task-type]').removeAttr('disabled'); $('.billing_group h4,.billing_group label').show(); $(this).hide(); return false;">Extra Billing</button>
		<?php } ?>
	</div>
	<script>
	function send_billing_email(extra_billing, ticketid) {
		<?php $extra_billing_address = get_config($dbc, "ticket_extra_billing_email");
		if($extra_billing_address != '') { ?>
			if(extra_billing == undefined) {
				extra_billing = [];
			}
			var ticket_label = '';
			$.ajax({
				url: '../Ticket/ticket_ajax_all.php?action=get_ticket_label&ticketid='+ticketid+'&include_site=1',
				method: 'GET',
				dataType: 'html',
				success: function(response) {
					ticket_label = response;
					$.ajax({
						url: '../ajax_all.php?fill=send_email',
						method: 'POST',
						data: {
							send_every_email: 'true',
							send_to: '<?= $extra_billing_address ?>',
							subject: 'Extra Billing added from '+ticket_label,
							body: "<p>Additional tasks that require extra billing were added to "+ticket_label+".</p><p>The tasks were:<br />\n"+extra_billing.join('<br />\n')+"</p><p>You are receiving this message because your email address has been set to be notified when extra billing occurs.<br />To view the <?= TICKET_NOUN ?>, click <a href='<?= WEBSITE_URL ?>/Ticket/index.php?edit="+ticketid+(tile_group == '' ? '' : '&tile_group='+tile_group)+(tile_name == '' ? '' : '&tile_name='+tile_name)+"'>here</a>.</p><p>This was created by <?= get_contact($dbc, $_SESSION['contactid']) ?>.</p>"
						}, success: function(response) {
							// console.log(response);
							if(response != '') {
								alert("There was a problem sending the Extra Billing Notification:\n"+response+"\nPlease notify the appropriate individuals that extra billing has been added.");
							}
						}
					});
				}
			});
		<?php } ?>
	}
	</script>
<?php } ?>
<div class="clearfix"></div>
<?php if($project_lead > 0 && $ticketid == 0) { ?>
	<script>
	$(document).ready(function() {
		var select = $('#collapse_staff,#tab_section_ticket_staff_list').find('[name=item_id]').first();
		if(!(select.val() > 0)) {
			select.val(<?= $project_lead ?>).change().trigger('change.select2');
		}
	});
	</script>
<?php }
echo '<hr>';
$query = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `src_table`='Staff_Tasks' AND `deleted`=0 AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `tile_name`='".FOLDER_NAME."'".$query_daily);
while($staff = mysqli_fetch_assoc($query)) { ?>
	<div class="staff_task">
		<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id">
		<label class="col-sm-6"><img class="inline-img pull-left small" src="../img/remove.png" onclick="$(this).closest('.staff_task').hide().find('input').val(1).change(); reload_checkin(); reload_summary();">
			Staff: <?= get_contact($dbc, $staff['item_id']) ?></label>
		<label class="col-sm-6">Task: <?= $staff['position'] ?></label>
		<div class="clearfix"></div>
	</div>
	<?php $pdf_contents[] = ['Task', 'Staff: '.get_contact($dbc, $staff['item_id']).'<br>'.'Task: '.$staff['position']]; ?>
<?php } ?>