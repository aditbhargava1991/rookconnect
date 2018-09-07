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
			<a href="?edit=<?= $projectid ?>&tab=tasks&status=assigned" class="hide-titles-mob <?= $_GET['status'] == 'assigned' ? 'active_tab' : '' ?> btn brand-btn pull-right">Incomplete</a><br />&nbsp;
            <div class="clearfix"></div>
		<?php } ?></h3>
    <div class="connectedChecklist">
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
        $task_statuses = explode(',',get_config($dbc, 'task_status'));
        $status_complete = $task_statuses[count($task_statuses) - 1];
		if($_GET['status'] == 'complete') {
			$tab_filter = "AND `status`='$status_complete'";
		} else if($_GET['status'] == 'assigned') {
			$tab_filter = "AND `status`!='$status_complete'";
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
        if($tasks->num_rows > 0) {
            while($task = mysqli_fetch_array($tasks)) {
                $fromTasks = 1;
                $_GET['taskid'] = $task['tasklistid'];
                include('scrum_card_load.php');
            }
        } else { ?>
            <li class="dashboard-item new-items " data-id="324" data-table="tasklist" data-name="project_milestone" data-id-field="tasklistid" data-colour="F2F2F2" style="background-color: #F2F2F2;"><span class="flag-label"></span>
                <h4>No Tasks Found</h4>
                <div class="clearfix"></div>
            </li>
        <?php }
	} ?>
    </div>
    <br />&nbsp;
	<?php include('next_buttons.php'); ?>
</div>