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
	$projecttype = filter_var($_GET['projecttype'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
	$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid' AND '$projectid' > 0")); ?>
<script>
var paths = <?= json_encode(explode(',',$project['project_path'])) ?>;
var ex_paths = <?= json_encode(explode(',',$project['external_path'])) ?>;
function savePath(path, path_list, new_path, tickets, tasks, items, intakes) {
    $.ajax({
        url: 'projects_ajax.php?action=update_path',
        method: 'POST',
        data: {
            projectid: '<?= $projectid ?>',
            path_list: path_list.join(','),
            path: path,
            new_path: new_path,
            tickets: tickets,
            tasks: tasks,
            items: items,
            intakes: intakes
        },
        success: function(response) {
            console.log(response);
        }
    });
}
function removeInternal(path) {
    path = $(path).closest('.form-group[data-id]');
    var pathid = $(path).data('id');
    if(pathid >= 0) {
        paths.splice(pathid, 1);
    }
    path.hide();
    savePath('project_path', paths);
}
function removeExternal(path) {
    path = $(path).closest('.form-group[data-id]');
    var pathid = $(path).data('id');
    if(pathid >= 0) {
        ex_paths.splice(pathid, 1);
    }
    path.hide();
    savePath('external_path', ex_paths);
}
function openPathSlider() {
    window.history.replaceState('','Software',window.location.href.replace(/&tab=[a-zA-Z_]+/g,'')+'&tab=details_path');
    overlayIFrameSlider('../Project/edit_project_path_select.php?projectid='+$('[name=projectid]').val(),'75%',true);
}
</script>
<input type="hidden" name="projectid" value="<?= $projectid ?>">
<div id="head_details_path">
	<h3><?= PROJECT_NOUN ?> Path Templates<img class="inline-img cursor-hand no-toggle black-color small" src="../img/project-path.png" title="Add / Remove Path" onclick="openPathSlider();"></h3>
	<?php if (in_array('Path',$tab_config)) {
        foreach(explode(',',$project['project_path']) as $i => $project_path_id) {
            if($project_path_id > 0) { ?>
                <div class="form-group" data-id="<?= $project_path_id ?>">
                    <label class="col-sm-4"><?= (empty(explode('#*#',$project['project_path_name'])[$i]) ? get_field_value('project_path','project_path_milestone','project_path_milestone',$project_path_id) : explode('#*#',$project['project_path_name'])[$i]) ?>
                        <img class="inline-img cursor-hand pull-right" src="../img/remove.png" onclick="removeInternal(this);">
                        <img class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png" onclick="openPathSlider();"></label>
                    <div class="col-sm-8">
                    <?php $paths = mysqli_query($dbc, "SELECT * FROM `project_path_custom_milestones` WHERE `projectid`='$projectid' AND `deleted`=0 AND `path_type`='I' AND `pathid`='$project_path_id' ORDER BY `sort`, `id`");
                    while($path_milestone = $paths->fetch_assoc()) {
                        echo empty($path_milestone['label']) ? $path_milestone['milestone'] : $path_milestone['label'].'<br />';
                    } ?>
                    </div>
                </div>
            <?php }
        }
        if(empty(array_filter(explode(',',$project['project_path'])))) { ?>
            <a href="" onclick="openPathSlider(); return false" class="btn brand-btn">Add <?= PROJECT_NOUN ?> Path <img class="inline-img" src="../img/project-path.png"></a>
        <?php }
    } ?>

	<?php if (in_array('External Path',$tab_config)) { ?>
        <?php foreach(explode(',',$project['external_path']) as $i => $project_path_id) {
            if($project_path_id > 0) { ?>
                <div class="form-group" data-id="<?= $project_path_id ?>">
                    <label class="col-sm-4"><?= (empty(explode('#*#',$project['external_path_name'])[$i]) ? get_field_value('project_path','project_path_milestone','project_path_milestone',$project_path_id) : explode('#*#',$project['external_path_name'])[$i]) ?>
                        <img class="inline-img cursor-hand pull-right" src="../img/remove.png" onclick="removeExternal(this);">
                        <img class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png" onclick="openPathSlider();"></label>
                    <div class="col-sm-8">
                    <?php $paths = mysqli_query($dbc, "SELECT * FROM `project_path_custom_milestones` WHERE `projectid`='$projectid' AND `deleted`=0 AND `path_type`='E' AND `pathid`='$project_path_id' ORDER BY `sort`, `id`");
                    while($path_milestone = $paths->fetch_assoc()) {
                        echo empty($path_milestone['label']) ? $path_milestone['milestone'] : $path_milestone['label'].'<br />';
                    } ?>
                    </div>
                </div>
            <?php }
        }
        if(empty(array_filter(explode(',',$project['external_path'])))) { ?>
            <a href="" onclick="openPathSlider(); return false" class="btn brand-btn">Add External Path <img class="inline-img" src="../img/project-path.png"></a>
        <?php }
    } ?>
	<div class="clearfix"></div>
</div>
