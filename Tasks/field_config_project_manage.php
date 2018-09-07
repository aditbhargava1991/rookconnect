<?php
/*
Task Dashboard Setting
*/
include ('../include.php');
checkAuthorised('tasks');
error_reporting(0);
?>
<?php
if(isset($_POST['task_include_checklists'])) {
    $task_include_checklists = filter_var($_POST['task_include_checklists'],FILTER_SANITIZE_STRING);
    set_config($dbc, 'task_include_checklists', $task_include_checklists);
}
if(isset($_POST['tasks_slider_layout'])) {
    $tasks_slider_layout = filter_var($_POST['tasks_slider_layout'],FILTER_SANITIZE_STRING);
    set_config($dbc, 'tasks_slider_layout', $tasks_slider_layout);
} else if($_POST['auto_archive_config'] == 1) {
    $tasklist_auto_archive = filter_var($_POST['tasklist_auto_archive'],FILTER_SANITIZE_STRING);
    $tasklist_auto_archive_days = filter_var($_POST['tasklist_auto_archive_days'],FILTER_SANITIZE_STRING);
    set_config($dbc, 'tasklist_auto_archive', $tasklist_auto_archive);
    set_config($dbc, 'tasklist_auto_archive_days', $tasklist_auto_archive_days);
} else if($data = $_POST['project_manage_dashboard']) {
    $dashboard = implode(',', $data);
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT count(task_id) as task_count FROM task_dashboard"));
    if($get_field_config['task_count'] == 1) {
        $query_insert_dashboard = "UPDATE `task_dashboard` set task_dashboard_tile = '" . $dashboard . "' WHERE task_id = 1";
    }
    else {
        $query_insert_dashboard = "INSERT INTO `task_dashboard` (`task_id`,`task_dashboard_tile`) VALUES (1, '$dashboard')";
    }

    mysqli_query($dbc, $query_insert_dashboard);
} else if(!empty($_POST['submit'])) {
	$colours = filter_var(implode(',',$_POST['flag_colours']),FILTER_SANITIZE_STRING);
	$flag_names = filter_var(implode('#*#',$_POST['flag_name']),FILTER_SANITIZE_STRING);
	
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT count(task_id) as task_count FROM task_dashboard"));
    if($get_field_config['task_count'] > 0) {
        $query_insert_dashboard = "UPDATE `task_dashboard` set `flag_colours` = '$colours', `flag_names` = '$flag_names'";
    }
    else {
        $query_insert_dashboard = "INSERT INTO `task_dashboard` (`flag_colours`, `flag_names`) VALUES ('$colours', '$flag_names)";
    }
	set_config($dbc, 'quick_action_icons', filter_var(implode(',',$_POST['quick_action_icons']),FILTER_SANITIZE_STRING));

    mysqli_query($dbc, $query_insert_dashboard);
} ?>
<?php include ('../navigation.php'); ?>
<div class="container">
    <div class='row'>
        <?php
        $how_to = '';
        $tile_settings = '';
        $add_tab = '';
        $quick_actions = '';
        $auto_archive = '';
        if($_GET['category'] == 'how_to') {
            $how_to = 'active_tab';
        } else if($_GET['category'] == 'tile_settings') {
            $tile_settings = 'active_tab';
        } else if($_GET['category'] == 'quick_actions') {
            $quick_actions = 'active_tab';
        } else if($_GET['category'] == 'auto_archive') {
            $auto_archive = 'active_tab';
        } else {
            $add_tab = 'active_tab';
        }
        echo "<a href='field_config_project_manage.php?category=how_to'><button type='button' class='btn brand-btn mobile-block ".$how_to."'>Task Tabs</button></a>";
        echo "<a href='field_config_project_manage.php?category=tile_settings'><button type='button' class='btn brand-btn mobile-block ".$tile_settings."'>Tile Settings</button></a>";
        echo "<a href='field_config_project_manage.php?category=add_tab'><button type='button' class='btn brand-btn mobile-block ".$add_tab."'>Edit Task Tabs</button></a>";
        echo "<a href='field_config_project_manage.php?category=quick_actions'><button type='button' class='btn brand-btn mobile-block ".$quick_actions."'>Quick Actions</button></a>";
        echo "<a href='field_config_project_manage.php?category=auto_archive'><button type='button' class='btn brand-btn mobile-block ".$auto_archive."'>Auto Archive Settings</button></a>";
        ?>

        <form role="form" class="form-horizontal" enctype="multipart/form-data" action="" method="post" name="form1" id="form1">
            <div class="panel-group" id="accordion2">

                <?php if($_GET['category'] == 'how_to') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_doc">
                               How To<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_doc" class="panel-collapse collapse in">
                        <div class="panel-body">
                        <b>Step 1:</b><br>
                        Think of the categories you would like to organize your Tasks Dashboard into. We have provided you with 5 to start; here is how to add your own.<br><br>

                        <b>Step 2:</b><br>
                        Click to the next Add Tabs section to fill out which Tabs will be displayed on the Tasks Dashboard.<br><br>

                        <b>Step 3:</b><br>
                        Click Submit to finalize your changes. If you click Back, it will not save your changes.<br><br>

                        <b>Reminder:</b><br>
                        Separate these Tabs with a comma, with no spaces between the comma and the next entry. The order in which you enter them into the Add Classifications bar will be the order they appear in your Tasks Dashboard.

                        </div>
                    </div>
                </div>
                <?php } else if($_GET['category'] == 'tile_settings') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_slider">
                               Slider Settings<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_slider" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-sm-4">Slider Default Layout:</label>
                                <div class="col-sm-8">
                                    <?php $tasks_slider_layout = get_config($dbc, 'tasks_slider_layout'); ?>
                                    <label><input name="tasks_slider_layout" type="radio" value="full" <?= $tasks_slider_layout == 'full' ? 'checked' : '' ?>>Full View</label>
                                    <label><input name="tasks_slider_layout" type="radio" value="accordion" <?= $tasks_slider_layout != 'full' ? 'checked' : '' ?>>Accordion View</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_fields">
                               Task Settings<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_fields" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="form-group">
                                <label class="col-sm-4">Include Checklists:</label>
                                <div class="col-sm-8">
                                    <?php $task_include_checklists = get_config($dbc, 'task_include_checklists'); ?>
                                    <label class="form-checkbox"><input type="checkbox" name="task_include_checklists" value="1" <?= $task_include_checklists == 1 ? 'checked' : '' ?>> Enable</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
				<div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tasks">
                               Task Types<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_tasks" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="form-group" id="no-more-tables">
								<?php $groups = $dbc->query("SELECT `category` FROM `task_types` WHERE `deleted`=0 GROUP BY `category` ORDER BY MIN(`sort`), MIN(`id`)");
								while($group = $groups->fetch_array()) {
									$group_names[] = $group[0];
								}
								$rate_security = get_security($dbc, 'rate_cards');
								foreach($group_names as $group) {
									$tasks = $dbc->query("SELECT `task_types`.`id`, `task_types`.`description`, `task_types`.`details`, `rate`.`companyrcid`, `rate`.`cust_price`, `rate`.`uom` FROM `task_types` LEFT JOIN `company_rate_card` `rate` ON `rate`.`tile_name`='Tasks' AND (`task_types`.`id`=`rate`.`item_id` OR (`rate`.`item_id`=0 AND `task_types`.`description`=`rate`.`description` AND `task_types`.`category`=`rate`.`heading`)) AND `rate`.`deleted`=0 WHERE `task_types`.`deleted`=0 AND `task_types`.`category`='$group' ORDER BY `task_types`.`sort`,`task_types`.`id`"); ?>
									<div class='col-sm-12 task-group'>
										<div class='form-group'>
											<label class='col-sm-3 control-label'>Category:</label>
											<div class='col-sm-8'>
												<input type='text' <?= ((in_array('Ticket Tasks Projects',array_merge($all_config,$value_config)) || in_array('Ticket Tasks Ticket Type',array_merge($all_config,$value_config))) ? 'readonly' : '') ?> name='category' value='<?= $group ?>' class='form-control' onchange='set_task_data();'>
											</div>
											<div class="col-sm-1"><img class="inline-img group_handle cursor-hand" src="../img/icons/drag_handle.png"></div>
										</div>
										<table class="table table-bordered">
											<tr class="hidden-sm hidden-xs">
												<th>Heading</th>
												<th>Description</th>
												<th>Rate</th>
												<th></th>
											</tr>
										<?php $task = $tasks->fetch_assoc();
										do { ?>
											<tr>
												<td data-title="Heading"><input type='text' name='task' data-id="<?= $task['id'] ?>" value='<?= $task['description'] ?>' class='form-control' onchange='set_task_data();'></td>
												<td data-title="Description"><input type='text' name='details' data-id="<?= $task['id'] ?>" value='<?= $task['details'] ?>' class='form-control' onchange='set_task_data();'></td>
												<td data-title="Rate">$<?= number_format($task['cust_price'],2).' '.$task['uom'] ?> <?= $rate_security['edit'] > 0 ? '<a href="../Rate Card/ratecards.php?card=tasks&type=tasks&'.($task['companyrcid'] > 0 ? 'id='.$task['companyrcid'] : 'task='.$task['id']).'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;">Edit</a>' : '' ?></td>
												<td data-title="">
													<input type="hidden" name="deleted" value="0">
													<img src="../img/remove.png" class="inline-img cursor-hand" onclick="$(this).closest('tr').hide().find('[name=deleted]').val(1); set_task_data();">
													<img src="../img/icons/ROOK-add-icon.png" class="inline-img cursor-hand" onclick="add_task(this);">
													<img src="../img/icons/drag_handle.png" class="inline-img cursor-hand handle">
												</td>
											</tr>
										<?php } while($task = $tasks->fetch_assoc()); ?>
										</table>
									</div>
								<?php } ?>
								<button onclick="add_task_group(); return false;" class="btn brand-btn pull-right">Add Category</button>
								<div class="clearfix"></div>
								<script>
								function add_task(btn) {
									var row = $(btn).closest('tr');
									var clone = row.clone();
									clone.find('input').val('').data('id','');
									row.after(clone);
									clone.find('input').first().focus();
								}
								function add_task_group() {
									var group = $('.task-group').last();
									var clone = group.clone();
									clone.find('.form-group').not(':last').not(':first').remove();
									clone.find('input').val('').data('id','');
									group.after(clone);
									set_task_data();
								}
								function set_task_data() {
									var data = [];
									$('.task-group').each(function() {
										var cat = $(this).find('[name=category]').val();
										if(cat != '') {
											$(this).find('tr').each(function() {
												if($(this).find('[name=task]').length > 0) {
													var id = $(this).find('[name=task]').data('id');
													var heading = $(this).find('[name=task]').val();
													var details = $(this).find('[name=details]').val();
													data.push({'id':id,'category':cat,'task':heading,'details':details});
												}
											});
										}
									});
									$.post('../Ticket/ticket_ajax_all.php?action=task_types', { tasks: data }, function(response) {
										if(response > 0) {
											$('[name=task]').filter(function() { return !($(this).data('id') > 0); }).first().data('id',response);
										}
									});
								}
								$(document).ready(function() {
									$('#collapse_tasks').sortable({
										handle: '.group_handle',
										items: '.task-group',
										update: set_task_data
									});
									$('#collapse_tasks .form-group').sortable({
										handle: '.handle',
										items: 'tr',
										update: set_task_data
									});
								});
								</script>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } else if($_GET['category'] == 'quick_actions') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_doc">
                               Quick Action Icons<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_doc" class="panel-collapse collapse in">
                        <div class="panel-body">
							<div class="form-group">
								<label class="col-sm-4 control-label">Enable Quick Action Icons</label>
								<div class="col-sm-8">
									<?php $quick_action_icons = explode(',',get_config($dbc, 'quick_action_icons')); ?>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('edit',$quick_action_icons) ? 'checked' : '' ?> value="edit"> <img class="inline-img" src="../img/icons/ROOK-edit-icon.png"> Edit</label>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('sync',$quick_action_icons) ? 'checked' : '' ?> value="sync"> <img class="inline-img" src="../img/icons/ROOK-sync-icon.png"> Sync External Path</label>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= !in_array('flag_manual',$quick_action_icons) && in_array('flag',$quick_action_icons) ? 'checked' : '' ?> value="flag" onclick="$('[name^=quick_action_icons][value=flag_manual]').removeAttr('checked');"> <img class="inline-img" src="../img/icons/ROOK-flag-icon.png"> Flag</label>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('flag_manual',$quick_action_icons) ? 'checked' : '' ?> value="flag_manual" onclick="$('[name^=quick_action_icons][value=flag]').removeAttr('checked');"> <img class="inline-img" src="../img/icons/ROOK-flag-icon.png"> Manually Flag with Label</label>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('reply',$quick_action_icons) ? 'checked' : '' ?> value="reply"> <img class="inline-img" src="../img/icons/ROOK-reply-icon.png"> Reply</label>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('attach',$quick_action_icons) ? 'checked' : '' ?> value="attach"> <img class="inline-img" src="../img/icons/ROOK-attachment-icon.png"> Attach</label>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('alert',$quick_action_icons) ? 'checked' : '' ?> value="alert"> <img class="inline-img" src="../img/icons/ROOK-alert-icon.png"> Alerts</label>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('email',$quick_action_icons) ? 'checked' : '' ?> value="email"> <img class="inline-img" src="../img/icons/ROOK-email-icon.png"> Email</label>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('reminder',$quick_action_icons) ? 'checked' : '' ?> value="reminder"> <img class="inline-img" src="../img/icons/ROOK-reminder-icon.png"> Reminders</label>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('time',$quick_action_icons) ? 'checked' : '' ?> value="time"> <img class="inline-img" src="../img/icons/ROOK-timer-icon.png"> Add Time</label>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('timer',$quick_action_icons) ? 'checked' : '' ?> value="timer"> <img class="inline-img" src="../img/icons/ROOK-timer2-icon.png"> Track Time</label>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('archive',$quick_action_icons) ? 'checked' : '' ?> value="archive"> <img class="inline-img" src="../img/icons/ROOK-trash-icon.png"> Archive</label>
										<label class="form-checkbox"><input type="checkbox" name="quick_action_icons[]" <?= in_array('hide_all',$quick_action_icons) ? 'checked' : '' ?> value="hide_all" onclick="$('[name^=quick_action_icons]').not('[value=hide_all]').removeAttr('checked');"> Disable All</label>
								</div>
							</div>
                        </div>
                    </div>
                </div>
				<?php $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM task_dashboard"));
				$flag_colours = $get_config['flag_colours'];
				$flag_names = explode('#*#', $get_config['flag_names']); ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_flags">
                               Flag Colours<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_flags" class="panel-collapse collapse">
                        <div class="panel-body">
							<div class="form-group">
								<label for="file[]" class="col-sm-4 control-label">Flag Colours to Use<span class="popover-examples list-inline">&nbsp;
								<a  data-toggle="tooltip" data-placement="top" title="The selected colours will be cycled through when you flag a task."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
								</span>:</label>
								<div class="col-sm-8">
									<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'FF6060') !== FALSE ? 'checked' : ''); ?> value="FF6060" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
									<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FF6060; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
									<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[0]; ?>" class="form-control"></div>
									<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'DEBAA6') !== FALSE ? 'checked' : ''); ?> value="DEBAA6" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
									<div style="border: 1px solid black; border-radius: 0.25em; background-color: #DEBAA6; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
									<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[1]; ?>" class="form-control"></div>
									<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'FFAEC9') !== FALSE ? 'checked' : ''); ?> value="FFAEC9" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
									<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FFAEC9; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
									<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[2]; ?>" class="form-control"></div>
									<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'FFC90E') !== FALSE ? 'checked' : ''); ?> value="FFC90E" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
									<div style="border: 1px solid black; border-radius: 0.25em; background-color: #FFC90E; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
									<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[3]; ?>" class="form-control"></div>
									<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'EFE4B0') !== FALSE ? 'checked' : ''); ?> value="EFE4B0" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
									<div style="border: 1px solid black; border-radius: 0.25em; background-color: #EFE4B0; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
									<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[4]; ?>" class="form-control"></div>
									<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'B5E61D') !== FALSE ? 'checked' : ''); ?> value="B5E61D" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
									<div style="border: 1px solid black; border-radius: 0.25em; background-color: #B5E61D; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
									<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[5]; ?>" class="form-control"></div>
									<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, '99D9EA') !== FALSE ? 'checked' : ''); ?> value="99D9EA" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
									<div style="border: 1px solid black; border-radius: 0.25em; background-color: #99D9EA; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
									<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[6]; ?>" class="form-control"></div>
									<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'D0E1F7') !== FALSE ? 'checked' : ''); ?> value="D0E1F7" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
									<div style="border: 1px solid black; border-radius: 0.25em; background-color: #D0E1F7; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
									<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[7]; ?>" class="form-control"></div>
									<label class="col-sm-4"><input type="checkbox" <?php echo (strpos($flag_colours, 'C8BFE7') !== FALSE ? 'checked' : ''); ?> value="C8BFE7" name="flag_colours[]" style="height:1.5em; width: 1.5em;">
									<div style="border: 1px solid black; border-radius: 0.25em; background-color: #C8BFE7; display: inline-block; height: 1.5em; margin: 0 0.25em; min-width: 4em; width: calc(100% - 3em);"></div></label>
									<div class="col-sm-8"><input type="text" name="flag_name[]" value="<?php echo $flag_names[8]; ?>" class="form-control"></div>
								</div>
							</div>
                        </div>
                    </div>
                </div>
                <?php } else if($_GET['category'] == 'add_tab') { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                            <h4 class="panel-title">
                            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to add or remove your Tabs."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                            <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_00">
                                Task Tabs<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div class="panel-collapse collapse in" id="collapse_00" style="height: auto;">
                        <div class="panel-body">
                            <?php $get_field_config_tiles = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT task_dashboard_tile FROM task_dashboard")); ?>
                            <?php $get_field_config_tile = ','.$get_field_config_tiles['task_dashboard_tile'] . ','; ?>
                            <?php if (strpos($get_field_config_tile, ','."Company Tasks".',') !== FALSE): ?>
                                <?php $company_check_box = 'checked'; ?>
                            <?php else: ?>
                                <?php $company_check_box = ''; ?>
                            <?php endif; ?>

                            <?php if (strpos($get_field_config_tile, ','."Community Tasks".',') !== FALSE): ?>
                                <?php $community_check_box = 'checked'; ?>
                            <?php else: ?>
                                <?php $community_check_box = ''; ?>
                            <?php endif; ?>

                            <?php if (strpos($get_field_config_tile, ','."Project Tasks".',') !== FALSE): ?>
                                <?php $project_check_box = 'checked'; ?>
                            <?php else: ?>
                                <?php $project_check_box = ''; ?>
                            <?php endif; ?>

                            <?php if (strpos($get_field_config_tile, ','."Business Tasks".',') !== FALSE): ?>
                                <?php $business_check_box = 'checked'; ?>
                            <?php else: ?>
                                <?php $business_check_box = ''; ?>
                            <?php endif; ?>

                            <?php if (strpos($get_field_config_tile, ','."Client Tasks".',') !== FALSE): ?>
                                <?php $client_check_box = 'checked'; ?>
                            <?php else: ?>
                                <?php $client_check_box = ''; ?>
                            <?php endif; ?>

                            <?php if (strpos($get_field_config_tile, ','."Reporting".',') !== FALSE): ?>
                                <?php $reporting_check_box = 'checked'; ?>
                            <?php else: ?>
                                <?php $reporting_check_box = ''; ?>
                            <?php endif; ?>
                            <input type="checkbox" name="project_manage_dashboard[]" <?php echo $company_check_box; ?> style="height: 20px; width: 20px;" value="Company Tasks">&nbsp;&nbsp;Company Tasks
                            <!--
                            <input type="checkbox" name="project_manage_dashboard[]" <?php echo $community_check_box; ?> style="height: 20px; width: 20px;" value="Community Tasks">&nbsp;&nbsp;Community Tasks
                            -->
                            <input type="checkbox" name="project_manage_dashboard[]" <?php echo $project_check_box; ?> style="height: 20px; width: 20px;" value="Project Tasks">&nbsp;&nbsp;<?= PROJECT_NOUN ?> Tasks
                            <input type="checkbox" name="project_manage_dashboard[]" <?php echo $business_check_box; ?> style="height: 20px; width: 20px;" value="Business Tasks">&nbsp;&nbsp;<?= BUSINESS_CAT ?> Tasks
                            <input type="checkbox" name="project_manage_dashboard[]" <?php echo $client_check_box; ?> style="height: 20px; width: 20px;" value="Client Tasks">&nbsp;&nbsp;<?= CONTACTS_TILE ?> Tasks
                            <input type="checkbox" name="project_manage_dashboard[]" <?= strpos($get_field_config_tile, ',Sales Tasks,') !== FALSE ? 'checked' : '' ?> style="height: 20px; width: 20px;" value="Sales Tasks">&nbsp;&nbsp;<?= SALES_NOUN ?> Tasks
                            <input type="checkbox" name="project_manage_dashboard[]" <?php echo $reporting_check_box; ?> style="height: 20px; width: 20px;" value="Reporting">&nbsp;&nbsp;Reporting
                        </div>
                    </div>
                </div>
                <?php } else if($_GET['category'] == 'auto_archive') { ?>
                <input type="hidden" name="auto_archive_config" value="1">
                <div class="panel panel-default">
                    <div class="panel-heading">
                            <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion_tabs" href="#collapse_00">
                                Auto-Archive Completed Tasks<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div class="panel-collapse collapse in" id="collapse_00" style="height: auto;">
                        <div class="panel-body">
                            <?php $tasklist_auto_archive = get_config($dbc, 'tasklist_auto_archive');
                            $tasklist_auto_archive_days = get_config($dbc, 'tasklist_auto_archive_days'); ?>
                            <div class="form-group">
                                <label class="control-label col-sm-4">Auto-Archive Completed Tasks:</label>
                                <div class="col-sm-8">
                                    <label class="form-checkbox"><input type="checkbox" name="tasklist_auto_archive" value="1" <?= $tasklist_auto_archive == 1 ? 'checked' : '' ?>> Enable</label>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="control-label col-sm-4">Auto-Archive Completed Tasks After # of Days:</label>
                                <div class="col-sm-8">
                                    <input type="number" name="tasklist_auto_archive_days" class="form-control" value="<?= !empty($tasklist_auto_archive_days) ? $tasklist_auto_archive_days : '30' ?>" min="1">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </div>
            <div class="form-group">
                <div class="col-sm-6">
                    <a class="btn config-btn btn-lg pull-left" href="index.php?category=All&tab=Private">Back</a>
                    <!--<a href="#" class="btn config-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
                    <button class="btn config-btn btn-lg pull-right" value="Submit" name="submit" type="submit">Submit</button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php include ('../footer.php'); ?>
