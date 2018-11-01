<?php include_once('../include.php');
error_reporting(0);
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($project)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid' AND '$projectid' > 0"));
}
$config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project_detail` WHERE `projectid`='$projectid'"));
include('../Estimate/arr_detail_types.php');
$custom_details = array_filter(explode('#*#',mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR '#*#') FROM `general_configuration` WHERE `name` IN ('project_ALL_detail_types','project_".$project['projecttype']."_detail_types')"))[0])); ?>
<script>
$(document).on('change', 'select.add_details', function() { addDetail(this); });
function addDetail(select) {
	$(select).closest('.form-group').before('<div class="form-group new_group">' +
			'<label class="col-sm-4">'+$(select).find('option:selected').text()+':<img class="inline-img" src="../img/remove.png" onclick="$(this).closest(\'.form-group\').find(\'textarea\').val(\'\').change(); $(this).closest(\'.form-group\').remove();"></label>' +
			'<div class="col-sm-8">' +
				'<textarea name="'+select.value+'" data-table="project_detail" data-id-field="detailid" data-id="<?= $details['detailid'] ?>" data-project="<?= $projectid ?>"></textarea>' +
			'</div>' +
		'</div>');
	$(select).val('').trigger('change.select2');
	var area = $('textarea:not([id])');
	initInputs('.new_group');
	area.change(saveField).keyup(syncUnsaved);
	$('.new_group').removeClass('new_group');
	setTimeout(function() { tinymce.editors[area.attr('id')].focus(); }, 10);
}
</script>
<div id="head_details" class="form-horizontal col-sm-12" data-tab-name="details">
	<h3><?= PROJECT_NOUN ?> Details</h3>
    <div class="notice double-gap-top double-gap-bottom popover-examples">
        <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11"><span class="notice-name">NOTE: </span>Here you can add any details relative to this project, including GAP, strategy and any other project details needed.</div>
        <div class="clearfix"></div>
    </div>
	<?php foreach($detail_types as $config_str => $field) {
		if((in_array($config_str, $config) || $details[$field[1]] != '') && $details[$field[1]] != '***FFFMDELETED***') { ?>
			<div class="form-group">
				<label class="col-sm-4"><?= $field[0] ?>: <?php if($security['edit'] > 0) { ?><img class="inline-img" src="../img/remove.png" onclick="$(this).closest('.form-group').find('textarea').val('***FFFMDELETED***').change(); $(this).closest('.form-group').remove();"><?php } ?></label>
				<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
					<textarea name="<?= $field[1] ?>" data-table="project_detail" data-id-field="detailid" data-id="<?= $details['detailid'] ?>" data-project="<?= $projectid ?>"><?= $details[$field[1]] ?></textarea>
				</div>
			</div>
		<?php }
	} ?>
	<?php foreach($custom_details as $custom) {
		$detail_name = config_safe_str($custom);
		$detail_value = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `projectcommid`, `comment` FROM `project_comment` WHERE `projectid`='$projectid' AND `type`='detail_$detail_name'"));
		if($detail_value['comment'] != '***FFFMDELETED***') { ?>
			<div class="form-group">
				<label class="col-sm-4"><?= $custom ?>:<?php if($security['edit'] > 0) { ?><img class="inline-img" src="../img/remove.png" onclick="$(this).closest('.form-group').find('textarea').val('***FFFMDELETED***').change(); $(this).closest('.form-group').remove();"><?php } ?></label>
				<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
					<textarea name="comment" data-table="project_comment" data-id-field="projectcommid" data-id="<?= $detail_value['projectcommid'] ?>" data-type="detail_<?= $detail_name ?>" data-project="<?= $projectid ?>"><?= $detail_value['comment'] ?></textarea>
				</div>
			</div>
		<?php }
	} ?>
	<?php if($security['edit'] > 0) { ?>
		<div class="form-group">
			<label class="col-sm-4">Add New Detail:</label>
			<div class="col-sm-8">
				<select class="chosen-select-deselect add_details">
					<option></option>
					<?php foreach($detail_types as $field) { ?>
						<option value="<?= $field[1] ?>"><?= $field[0] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php } ?>
	<div class="clearfix"></div>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_project_details.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>