<?php
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status` > 0"));
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'")); ?>
<script>
$(document).ready(function() {
	setActions();
    resizeProjectPath();
	$(window).resize(function() {
		resizeProjectPath();
	}).resize();
	$('select.path_select_onchange').change(function() {
		window.location.replace('?edit=<?= $_GET['edit'] ?>&tab=path&pathid='+this.value);
	});
});
function initDragging() {
	// Dragging to other Statuses
	$('.dashboard-list').sortable({
		connectWith: '.dashboard-list',
		sort: function(event) {
			var end_distance = window.innerWidth - event.clientX;
			var start_distance = event.clientX - $('.dashboard-container').offset().left;
			clearInterval(keep_scrolling);
			if(end_distance < 20) {
				keep_scrolling = setInterval(function() { $('.dashboard-container').scrollLeft($('.dashboard-container').scrollLeft() + 10); }, 10);
			} else if(start_distance < 20) {
				keep_scrolling = setInterval(function() { $('.dashboard-container').scrollLeft($('.dashboard-container').scrollLeft() - 10); }, 10);
			}
		},
		handle: '.milestone-handle',
		items: '.dashboard-item',
		update: function(event, element) {
			$.ajax({
				url: 'projects_ajax.php?action=project_fields',
				method: 'POST',
				data: {
					field: 'status',
					value: element.item.closest('.dashboard-list').data('status'),
					table: element.item.data('table'),
					id: element.item.data('id'),
					id_field: element.item.data('id-field')
				},
				success: function(response) {
					$('.empty-list').remove();
					$('.dashboard-item:first-of-type [name=task],.dashboard-item:first-of-type .btn.brand-btn').closest('.dashboard-item.add_block').prepend('<div class="empty-list text-center">Nothing to do.</div>');
				}
			});
		}
	});
}
</script>
<h3 class="pad-horizontal action-icons"><span class="pull-left"><?= PROJECT_NOUN ?> Scrum Board</span>
<?php if(in_array($_GET['tab'],['path','path_external_path','scrum_board']) && $security['edit'] > 0 && $pathid != 'AllSB' && $pathid[0] != 'scrum') { ?>
	<div class="col-sm-4 pull-right path_select smaller" style="display:none;"><select class="chosen-select-deselect path_select_onchange" data-placeholder="Select <?= PROJECT_NOUN ?> Path">
		<option></option>
		<?php if(in_array('Scrum Board',$tab_config)) { ?><option <?= $_GET['tab'] == 'scrum_board' ? 'selected' : '' ?> value="SB">Scrum Board</option><?php } ?>
		<?php $paths = mysqli_query($dbc, "SELECT `project_path`, `project_path_milestone` FROM `project_path_milestone` WHERE `project_path` != '' AND `project_path_milestone` IN (".$project['project_path'].") ORDER BY `project_path`");
		while($path = mysqli_fetch_array($paths)) { ?>
			<option <?= $path['project_path_milestone'] == $pathid && $_GET['tab'] == 'path' ? 'selected' : '' ?> value="I|<?= $path['project_path_milestone'] ?>"><?= $path['project_path'] ?></option>
		<?php }
		$external_paths = mysqli_query($dbc, "SELECT `project_path`, `project_path_milestone` FROM `project_path_milestone` WHERE `project_path` != '' AND `project_path_milestone` IN (".$project['external_path'].") ORDER BY `project_path`");
		while($path = mysqli_fetch_array($external_paths)) { ?>
			<option <?= $path['project_path_milestone'] == $pathid && $_GET['tab'] == 'path_external_path' ? 'selected' : '' ?> value="E|<?= $path['project_path_milestone'] ?>">External: <?= $path['project_path'] ?></option>
		<?php } ?>
	</select></div>
	<img class="inline-img pull-right no-toggle black-color small" src="../img/project-path.png" title="Select the <?= PROJECT_NOUN ?> Path" onclick="$('.path_select').show(); $(this).hide();">
	<img class="inline-img pull-right no-toggle black-color small" src="../img/icons/ROOK-add-icon.png" title="Add / Remove <?= ($_GET['tab'] == 'path_external_path' ? 'External ' : '').PROJECT_NOUN ?> Path" onclick="overlayIFrameSlider('edit_project_path_select.php?projectid=<?= $projectid ?>&path=<?= $_GET['tab'] == 'path' ? 'project_path' : 'external_path' ?>','75%',true)">
<?php } ?></h3>
<div class="clearfix"></div>
<div class="double-scroller"><div></div></div>
<div class="has-dashboard form-horizontal dashboard-container" style="<?= $pathid == 'AllSB' || $pathid[0] == 'scrum' ? 'overflow-y:hidden;' : '' ?>">
	<?php $ticket_status_list = explode(',',get_config($dbc, 'ticket_status'));
	$task_statuses = explode(',',get_config($dbc, 'task_status'));
	$add_action = '';
	$action_title = '';
	if(in_array('Tickets',$tab_config)) {
		$add_action = "overlayIFrameSlider('../Ticket/index.php?calendar_view=true&edit=0&projectid=".$projectid."&milestone_timeline=".urlencode($milestone)."&from=".urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI'])."'); return false;";
		$action_title = 'Add '.TICKET_NOUN;
	} else if(in_array('Tasks',$tab_config)) {
		$add_action = "overlayIFrameSlider('../Tasks_Updated/add_task.php?projectid=$projectid','50%',false);";
		$action_title = 'Add Task';
	}
	foreach(array_unique(array_merge($ticket_status_list,$task_statuses)) as $i => $status) {
        if($pathid[0] != 'scrum' || $pathid[1] == config_safe_str($status)) {
            $sql = "SELECT 'Ticket', `ticketid` FROM tickets WHERE projectid='$projectid' AND `deleted`=0 AND `status` != 'Archive' AND `status`='$status'
                UNION SELECT 'Task', `tasklistid` FROM `tasklist` WHERE `projectid`='$projectid' AND `deleted`=0 AND `status` != 'Archive' AND `status`='$status'";
            $milestone_items = mysqli_query($dbc, $sql); ?>
            <div class="<?= $pathid == 'AllSB' || $pathid[0] == 'scrum' ? 'item-list' : 'dashboard-list' ?>" style="margin-bottom: -10px;">
                <a href="?edit=<?= $projectid ?>&tab=path&pathid=scrum|<?= config_safe_str($status) ?>" <?= $pathid == 'AllSB' || $pathid[0] == 'scrum' ? 'onclick="return false;"' : '' ?>><div class="info-block-header"><h4><?= $status ?>
                    <?= $add_action != '' && $security['edit'] > 0 && $pathid != 'AllSB' && $pathid[0] != 'scrum' ? '<a href=""><img class="no-margin black-color inline-img pull-right" src="../img/icons/ROOK-add-icon.png" title="'.$action_title.'" onclick="'.$add_action.'return false;"></a>' : '' ?></h4>
                <div class="small"><?= mysqli_num_rows($milestone_items) ?></div></div></a>
                <ul class="<?= $pathid == 'AllSB' || $pathid[0] == 'scrum' ? 'connectedChecklist full-width' : 'dashboard-list' ?>" data-status="<?= $status ?>">
                    <?php while($item = mysqli_fetch_array($milestone_items)) {
                        include('scrum_card_load.php');
                    } ?>
                </ul>
            </div>
        <?php }
    } ?>
</div>
<div class="clearfix"></div>
