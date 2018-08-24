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
<input type="hidden" name="projectid" value="<?= $projectid ?>">
<div id="head_details_path">
	<h3><?= PROJECT_NOUN ?> Path Templates<img class="inline-img cursor-hand no-toggle black-color small" src="../img/icons/ROOK-add-icon.png" title="Add / Remove Path" onclick="overlayIFrameSlider('../Project/edit_project_path_select.php?projectid=<?= $projectid ?>','75%',true)"></h3>
	<?php if (in_array('Path',$tab_config)) { ?>
        <?php foreach(explode(',',$project['project_path']) as $i => $project_path_id) {
            if($project_path_id > 0) {
                echo '<label class="col-sm-4">'.(empty(explode('#*#',$project['project_path_name'])[$i]) ? get_field_value('project_path','project_path_milestone','project_path_milestone',$project_path_id) : explode('#*#',$project['project_path_name'])[$i]).'</label>';
                echo '<div class="col-sm-8">';
                $paths = mysqli_query($dbc, "SELECT * FROM `project_path_custom_milestones` WHERE `projectid`='$projectid' AND `deleted`=0 AND `path_type`='I' AND `pathid`='$project_path_id' ORDER BY `sort`, `id`");
                while($path_milestone = $paths->fetch_assoc()) {
                    echo empty($path_milestone['label']) ? $path_milestone['milestone'] : $path_milestone['label'].'<br />';
                }
                echo '</div>';
            }
        } ?>
	<?php } ?>

	<?php if (in_array('External Path',$tab_config)) { ?>
        <?php foreach(explode(',',$project['external_path']) as $i => $project_path_id) {
            if($project_path_id > 0) {
                echo '<label class="col-sm-4">'.(empty(explode('#*#',$project['external_path_name'])[$i]) ? get_field_value('project_path','project_path_milestone','project_path_milestone',$project_path_id) : explode('#*#',$project['external_path_name'])[$i]).'</label>';
                echo '<div class="col-sm-8">';
                $paths = mysqli_query($dbc, "SELECT * FROM `project_path_custom_milestones` WHERE `projectid`='$projectid' AND `deleted`=0 AND `path_type`='E' AND `pathid`='$project_path_id' ORDER BY `sort`, `id`");
                while($path_milestone = $paths->fetch_assoc()) {
                    echo empty($path_milestone['label']) ? $path_milestone['milestone'] : $path_milestone['label'].'<br />';
                }
                echo '</div>';
            }
        } ?>
	<?php } ?>
	<div class="clearfix"></div>
</div>
