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
$project_security = get_security($dbc, 'project');
$_GET['status'] = empty($_GET['status']) ? 'assigned' : $_GET['status']; ?>
<script>
$(document).ready(function() {
	setActions();
});
function setActions() {
	$('.reply-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		item.find('[name=reply]').off('change').off('blur').show().focus().blur(function() {
			$(this).off('blur');
			$.ajax({
				url: 'projects_ajax.php?action=project_fields',
				method: 'POST',
				data: {
					mode: 'append',
					field: 'task',
					value: this.value,
					table: item.data('table'),
					id: item.data('id'),
					id_field: item.data('id-field')
				},
				success: function(response) {
					item.find('.task').append(response);
				}
			});
			$(this).hide().val('');
		}).keyup(function(e) {
			if(e.which == 13) {
				$(this).blur();
			} else if(e.which == 27) {
				$(this).off('blur').hide();
			}
		});
	});
	$('.archive-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		$.ajax({
			url: 'projects_ajax.php?action=project_fields',
			method: 'POST',
			data: {
				field: 'deleted',
				value: 1,
				table: item.data('table'),
				id: item.data('id'),
				id_field: item.data('id-field')
			}
		});
		item.hide();
	});
	$('.flag-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		$.ajax({
			url: 'projects_ajax.php?action=project_actions',
			method: 'POST',
			data: {
				field: 'flag_colour',
				value: item.data('colour'),
				table: item.data('table'),
				id: item.data('id'),
				id_field: item.data('id-field')
			},
			success: function(response) {
				item.data('colour',response.substr(0,6));
				item.css('background-color','#'+response.substr(0,6));
				item.find('.flag-label').html(response.substr(6));
			}
		});
	});
	$('.time-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		item.find('.time-field').timepicker('option','onClose',function() {
			if(this.value != '') {
				$.ajax({
					url: 'projects_ajax.php?action=project_actions',
					method: 'POST',
					data: {
						field: this.name,
						value: this.value,
						table: $(this).data('table'),
						ref: item.data('table'),
						ref_id: item.data('id'),
						ref_id_field: item.data('id-field')
					},
					success: function(response) {
						item.find('h4').append(response);
					}
				});
				$(this).hide().val('');
			}
		}).focus();
	});
	$('.attach-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		item.find('[type=file]').off('change').change(function() {
			var fileData = new FormData();
			fileData.append('file',$(this)[0].files[0]);
			fileData.append('field','document');
			fileData.append('table',$(this).data('table'));
			fileData.append('folder',$(this).data('folder'));
			fileData.append('id',item.data('id'));
			fileData.append('id_field',item.data('id-field'));
			$.ajax({
				contentType: false,
				processData: false,
				method: "POST",
				url: "projects_ajax.php?action=project_actions",
				data: fileData,
				success: function(response) {
					console.log(response);
				}
			});
		}).click();
	});
	$('.reminder-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		item.find('[name=reminder]').change(function() {
			var reminder = $(this).val();
			item.find('iframe').off('load').load(function() {
				var iframe = $(this);
				iframe.show().height('18em');
				iframe.contents().find('.btn').click(function() {
					if($(this).closest('body').find('select').val() != '' && confirm('Are you sure you want to schedule reminders for the selected user(s)?')) {
						var users = [];
						$(this).closest('body').find('select option:selected').each(function() {
							users.push(this.value);
						});
						$.ajax({
							method: 'POST',
							url: 'projects_ajax.php?action=project_actions',
							data: {
								id: item.data('id'),
								id_field: item.data('id-field'),
								table: item.data('table'),
								field: 'reminder',
								value: reminder,
								users: users,
								ref_id: item.data('id'),
								ref_id_field: item.data('id-field')
							},
							success: function(result) {
								item.find('h4').append(result);
							}
						});
					}
					iframe.off('load').html('').hide();
					return false;
				});
			}).attr('src','../Staff/select_staff.php?target=reminder&multiple=true');
		}).focus();
	});
	$('.alert-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		item.find('iframe').off('load').load(function() {
			var iframe = $(this);
			iframe.show().height('18em');
			iframe.contents().find('.btn').click(function() {
				if($(this).closest('body').find('select').val() != '' && confirm('Are you sure you want to enable alerts for the selected user(s)?')) {
					var users = [];
					$(this).closest('body').find('select option:selected').each(function() {
						users.push(this.value);
					});
					$.ajax({
						method: 'POST',
						url: 'projects_ajax.php?action=project_actions',
						data: {
							id: item.data('id'),
							id_field: item.data('id-field'),
							table: item.data('table'),
							field: 'alert',
							value: users
						},
						success: function(result) { console.log(result); }
					});
				}
				iframe.off('load').html('').hide();
				return false;
			});
		}).attr('src','../Staff/select_staff.php?target=alert&multiple=true');
	});
	$('.email-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		item.find('iframe').off('load').load(function() {
			var iframe = $(this);
			iframe.show().height('18em');
			iframe.contents().find('.btn').click(function() {
				if($(this).closest('body').find('select').val() != '' && confirm('Are you sure you want to send an e-mail to the selected user(s)?')) {
					var users = [];
					$(this).closest('body').find('select option:selected').each(function() {
						users.push(this.value);
					});
					$.ajax({
						method: 'POST',
						url: 'projects_ajax.php?action=project_actions',
						data: {
							id: item.data('id'),
							id_field: item.data('id-field'),
							table: item.data('table'),
							field: 'email',
							value: users
						},
						success: function(result) { console.log(result.responseText); }
					});
				}
				iframe.off('load').html('').hide();
				return false;
			});
		}).attr('src','../Staff/select_staff.php?target=email&multiple=true');
	});
}
</script>
<div class="iframe_holder" style="display:none;">
	<img src="<?= WEBSITE_URL ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
	<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
	<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
</div>
<div class="hide_on_iframe">
	<h3><?php if($_GET['status'] == 'project') { ?>
			<!-- Checklists -->
		<?php } else { ?>
			<!-- Tasks -->&nbsp;
			<a href="?edit=<?= $projectid ?>&tab=tasks&status=complete" class="hide-titles-mob <?= $_GET['status'] == 'complete' ? 'active_tab' : '' ?> btn brand-btn pull-right">Completed</a>
			<a href="?edit=<?= $projectid ?>&tab=tasks&status=assigned" class="hide-titles-mob <?= $_GET['status'] == 'assigned' ? 'active_tab' : '' ?> btn brand-btn pull-right">Incomplete</a></h3>
		<?php } ?>
	<?php if($_GET['status'] == 'project') {
		if(!empty($_GET['checklistid'])) {
			include('../Checklist/edit_checklist.php');
		} else if(!empty($_GET['view'])) {
			include('../Checklist/view_checklist.php');
		} else if(!empty($_GET['item_view'])) {
			include('../Checklist/item_checklist_view.php');
		} else {
			include('../Checklist/list_checklists.php');
		}
	} else {
		$milestones = explode('#*#',html_entity_decode(mysqli_fetch_array(mysqli_query($dbc, "SELECT `milestone` FROM `project_path_milestone` WHERE `project_path_milestone`='".$project['project_path']."'"))['milestone']));
		$tab_filter = '';
		if($_GET['status'] == 'complete') {
			$tab_filter = "AND `status`='Done'";
		} else if($_GET['status'] == 'assigned') {
			$tab_filter = "AND `status`!='Done'";
		}
		$sql = "SELECT * FROM `tasklist` WHERE `projectid`='$projectid' AND `deleted`=0 $tab_filter ORDER BY ";
		foreach($milestones as $milestone) {
			$sql .= "`project_milestone`='$milestone', ";
		}
		$sql .= "`sort`";
		$tasks = mysqli_query($dbc, $sql);
		$ticket_flag_names = [''=>''];
		$flag_names = explode('#*#', get_config($dbc, 'ticket_colour_flag_names'));
		foreach(explode(',',get_config($dbc, 'ticket_colour_flags')) as $i => $colour) {
			$ticket_flag_names[$colour] = $flag_names[$i];
		}
		while($task = mysqli_fetch_array($tasks)) { ?>
			<div class="dashboard-item form-horizontal" data-id="<?= $task['tasklistid'] ?>" data-id-field="tasklistid" data-table="tasklist" data-colour="<?= $task['flag_colour'] ?>" style="background-color:#<?= $task['flag_colour'] != '' ? $task['flag_colour'] : 'F2F2F2' ?>">
				<span class="flag-label"><?= $ticket_flag_names[$task['flag_colour']] ?></span>
				<h3><div class="pull-left"><input type="checkbox" data-id="<?= $task['tasklistid'] ?>" data-id-field="tasklistid" data-table="tasklist" value="Done" name="status" <?= $task['status'] == 'Done' ? 'checked' : '' ?> <?= !($security['edit'] > 0) ? 'readonly disabled' : '' ?>> #<?= $task['tasklistid'] ?></div>
					<div class="scale-to-fill action-icons small pad-horizontal">
						<?php if($security['edit'] > 0) { ?>
							<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-flag-icon.png" class="inline-img flag-icon no-toggle" title="Flag This!">
							<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-alert-icon.png" class="inline-img alert-icon no-toggle" title="Enable Alerts">
							<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-email-icon.png" class="inline-img email-icon no-toggle" title="Send Email">
							<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-reminder-icon.png" class="inline-img reminder-icon no-toggle" title="Schedule Reminder">
							<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-attachment-icon.png" class="inline-img attach-icon no-toggle" title="Attach File">
							<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-reply-icon.png" class="inline-img reply-icon no-toggle" title="Add Note">
							<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-timer-icon.png" class="inline-img time-icon no-toggle" title="Add Time">
							<img src="<?= WEBSITE_URL ?>/img/icons/trash-icon-red.png" class="inline-img archive-icon no-toggle" title="Archive">
						<?php } ?>
					</div>
					<?php foreach(explode(',',$task['alerts_enabled']) as $assignid) {
						if($assignid > 0) {
							echo '<span class="pull-left col-sm-12 small">'.profile_id($dbc, $assignid,false).' Assigned to '.get_contact($dbc, $assignid).'</span>';
						}
					} ?>
					<div class="clearfix"></div></h3>
				<div class="col-sm-6 form-group">
					<label class="col-sm-4">Milestone:</label>
					<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
						<select name="project_milestone" data-table="tasklist" data-id-field="tasklistid" data-id="<?= $task['tasklistid'] ?>" class="chosen-select-deselect">
							<option></option>
							<?php foreach($milestones as $milestone) { ?>
								<option <?= $task['project_milestone'] == $milestone ? 'selected' : '' ?> value="<?= $milestone ?>"><?= $milestone ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<div class="col-sm-6 form-group">
					<label class="col-sm-4">Task:</label>
					<div class="col-sm-8 small task"><?= preg_replace_callback('/\[PROFILE ([0-9]+)\]/',profile_callback,html_entity_decode($task['task'])) ?></div>
				</div>
				<div class="col-sm-12">
					<input type='text' name='reply' value='' data-name='task' class="form-control" style="display:none;">
					<input type='text' name='work_time' value='' data-table='tasklist' class="form-control timepicker time-field" style="border:0;height:0;margin:0;padding:0;width:0;">
					<input type='text' name='reminder' value='' class="form-control datepicker" style="border:0;height:0;margin:0;padding:0;width:0;">
					<iframe style="display:none; width:100%;" src=""></iframe>
					<input type='file' name='document' value='' data-table="<?= $doc_table ?>" data-folder="<?= $doc_folder ?>" style="display:none;">
				</div>
				<div class="clearfix"></div>
			</div>
		<?php }
	} ?>
	<?php include('next_buttons.php'); ?>
</div>