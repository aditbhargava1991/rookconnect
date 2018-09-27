<?php include_once('../include.php');
$security = get_security($dbc, $tile);
$strict_view = strictview_visible_function($dbc, 'project');
if($strict_view > 0) {
	$security['edit'] = 0;
	$security['config'] = 0;
}

if($_GET['add_new_detail'] == 'true') {
	$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid' AND '$projectid' > 0"));
	$projecttype = $project['projecttype'];
	$custom_tab = $_POST['tab'];
	$custom_heading = $_POST['heading'];
	$custom_field = $_POST['field'];
	$custom_fieldtype = $_POST['fieldtype'];

	mysqli_query($dbc, "INSERT INTO `project_custom_details` (`projectid`, `tab`, `heading`, `field`, `field_type`) VALUES ('$projectid', '$custom_tab', '$custom_heading', '$custom_field', '$custom_fieldtype')");
} ?>

<div id="custom_<?= config_safe_str($custom_heading) ?>" class="custom_detail_div form-horizontal col-sm-12" data-tab-name="custom_<?= config_safe_str($custom_tab) ?>_<?= config_safe_str($custom_heading) ?>">
	<h3><?= $custom_heading ?></h3>
	<?php $custom_fields = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `project_custom_details` WHERE `projectid` = '$projectid' AND '$projectid' > 0 AND `tab` = '$custom_tab' AND `heading` = '$custom_heading' AND `deleted` = 0"),MYSQLI_ASSOC);
	foreach($custom_fields as $custom_field) { ?>
		<div class="form-group" data-id="<?= $custom_field['id'] ?>">
			<label class="col-sm-4"><?= $custom_field['field'] ?>:<?php if($security['edit'] > 0) { ?><img data-id="<?= $custom_field['id'] ?>" class="inline-img" src="../img/remove.png" onclick="removeCustomDetail(this);"><?php } ?></label>
			<div class="col-sm-8">
				<?php if($custom_field['field_type'] == 'uploader') { ?>
					<div class="uploader_file">
						<?php if(!empty($custom_field['value'])) { ?>
							<a href="download/<?= $custom_field['value'] ?>" target="_blank">View</a><?php if($security['edit'] > 0) { ?> | <a href="" onclick="removeCustomDetailUpload(this); return false;">Delete</a><?php } ?>
						<?php } ?>
					</div>
					<?php if($security['edit'] > 0) { ?>
						<input type="file" data-id="<?= $custom_field['id'] ?>" onchange="addCustomDetailUpload(this);" data-filename-placement="inside" class="form-control">
					<?php } ?>
				<?php } else { ?>
					<?php if(!($security['edit'] > 0)) {
						echo '<div class="readonly-block">';
					} ?>
					<textarea name="value" data-table="project_custom_details" data-id-field="id" data-id="<?= $custom_field['id'] ?>" data-project="<?= $projectid ?>" class="form-control"><?= html_entity_decode($custom_field['value']) ?></textarea>
					<?php if(!($security['edit'] > 0)) {
						echo '</div>';
					} ?>
				<?php } ?>
			</div>
		</div>
	<?php } ?>
	<?php if($security['edit'] > 0) { ?>
		<div class="form-group">
			<label class="col-sm-4">Add New Detail:</label>
			<div class="col-sm-8">
				<select class="chosen-select-deselect" onchange="addCustomDetail(this);">
					<option></option>
					<?php foreach($custom_details[$custom_heading] as $custom_field) { ?>
						<option data-project="<?= $projectid ?>" data-tab="<?= $custom_tab ?>" data-heading="<?= $custom_heading ?>" data-fieldtype="<?= $custom_field['type'] ?>" value="<?= $custom_field['label'] ?>"><?= $custom_field['label'] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php } ?>
	<div class="clearfix"></div>
</div>