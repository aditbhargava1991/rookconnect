<?php
if(isset($_POST['save_template'])) {
	$templateid = $_POST['templateid'];
	$template_name = filter_var($_POST['template_name'],FILTER_SANITIZE_STRING);
	$service_category = filter_var($_POST['service_category'],FILTER_SANITIZE_STRING);
	$serviceids = implode(',', $_POST['heading']);
	if(empty($templateid) || $templateid == 'new') {
		$query_insert = "INSERT INTO `services_service_templates` (`name`, `serviceid`, `service_category`) VALUES ('$template_name', '$serviceids', '$service_category')";
		$result_insert = mysqli_query($dbc, $query_insert);
		$templateid = mysqli_insert_id($dbc);
		$before_change = "";
		$history = "Service Template entry has been added. <br />";
		add_update_history($dbc, 'service_history', $history, '', $before_change);
	} else {
		$before_change = capture_before_change($dbc, 'services_service_templates', 'name', 'templateid', $templateid);
		$before_change .= capture_before_change($dbc, 'services_service_templates', 'serviceid', 'templateid', $templateid);
		$before_change .= capture_before_change($dbc, 'services_service_templates', 'service_category', 'templateid', $templateid);
		$query_update = "UPDATE `services_service_templates` SET `name` = '$template_name', `serviceid` = '$serviceids', `service_category` = '$service_category' WHERE `templateid` = '$templateid'";
		$result_update = mysqli_query($dbc, $query_update);
		$history = capture_after_change('name', $template_name);
		$history .= capture_after_change('serviceid', $serviceids);
		$history .= capture_after_change('service_category', $service_category);
		add_update_history($dbc, 'service_history', $history, '', $before_change);
	}

	echo '<script>window.location.href = "?templateid='.$templateid.'";</script>';
} ?>
<script type="text/javascript">
$(document).on('change', 'select[name="service_category"]', function() { changeGlobalCategory(this); });
$(document).on('change', 'select[name="category"]', function() { changeCategory(this); });
$(document).on('change', 'select[name="service_type"]', function() { changeType(this); });

function changeGlobalCategory(sel) {
	var global_category = $(sel).val();
	if(global_category != undefined && global_category != '') {
		$('select[name="category"]').val(global_category).prop('readonly',true).prop('disabled',true).change().closest('td').addClass('readonly-block');
	} else {
		$('select[name="category"]').prop('readonly',false).prop('disabled',false).closest('td').removeClass('readonly-block');
	}
}
function changeCategory(sel) {
	var block = $(sel).closest('tr');
	if(sel.value != '') {
		$(block).find('[name="service_type"] option').hide();
		$(block).find('[name="service_type"] option[data-category="'+sel.value+'"]').show();
		$(block).find('[name="service_type"]').trigger('change.select2');
		$(block).find('[name="heading[]"] option').hide();
		$(block).find('[name="heading[]"] option[data-category="'+sel.value+'"]').show();
		$(block).find('[name="heading[]"]').trigger('change.select2');
	}
}
function changeType(sel) {
	var block = $(sel).closest('tr');
	if(sel.value != '') {
		var filter = '[data-service-type="'+sel.value+'"]';
		var category = $(block).find('[name="category"]').val();
		if(category != '') {
			filter += '[data-category="'+category+'"]';
		}
		$(block).find('[name="heading[]"] option').hide();
		$(block).find('[name="heading[]"] option'+filter).show();
		$(block).find('[name="heading[]"]').trigger('change.select2');
	}
}
function addService() {
	destroyInputs('.service_table');
	var block = $('tr.service_row').last();
	var clone = $(block).clone();
	clone.find('select').val('');
	clone.find('select option').show();
	block.after(clone);
	initInputs('.service_table');

	var global_category = $('select[name="service_category"]').val();
	if(global_category != undefined && global_category != '') {
		clone.find('select[name="category"]').val(global_category).prop('readonly',true).prop('disabled',true).change().closest('td').addClass('readonly-block');
	}
}
function removeService(btn) {
	if($('tr.service_row').length <= 1) {
		addService();
	}
	$(btn).closest('tr').remove();
}
</script>
<?php
$service_fields = explode(',',get_field_config($dbc, 'services'));
$template = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services_service_templates` WHERE `templateid` = '$templateid' AND '$templateid' > 0"));
?>
<form class="form-horizontal" action="" method="post" style="height: calc(100% - 2.5em);">
	<div class="standard-body main-screen-white" style="padding-left: 0; padding-right: 0; border: none;">
	    <div class="standard-body-title">
			<h3><?= ($templateid > 0 ? $template['name'] : 'Create New Template') ?></h3>
		</div>
		<div class="standard-body-content pad-10">
			<input type="hidden" id="templateid" name="templateid" value="<?= $templateid ?>">
			<div class="form-group">
				<label class="col-sm-4 control-label">Template Name:</label>
				<div class="col-sm-8">
					<input type="text" name="template_name" value="<?= $template['name'] ?>" class="form-control">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Limit Service Category:</label>
				<div class="col-sm-8">
					<select name="service_category" class="chosen-select-deselect form-control">
						<option></option>
						<?php $service_categories = mysqli_query($dbc, "SELECT DISTINCT `category` FROM `services` WHERE `deleted` = 0 ORDER BY `category`");
						while($service_cat = mysqli_fetch_assoc($service_categories)) {
							echo '<option value="'.$service_cat['category'].'" '.($service_cat['category'] == $template['service_category'] ? 'selected' : '' ).'>'.$service_cat['category'].'</option>';
						} ?>
					</select>
				</div>
			</div>
			<div id="no-more-tables" class="service_table">
				<table class="table table-bordered">
					<tr class="hidden-xs">
						<?php if(in_array('Category',$service_fields)) { ?>
							<th>Category</th>
						<?php } ?>
						<?php if(in_array('Service Type',$service_fields)) { ?>
							<th>Service Type</th>
						<?php } ?>
						<th>Heading</th>
						<th>Function</th>
					</tr>
					<?php $serviceids = explode(',', $template['serviceid']);
					foreach($serviceids as $serviceid) {
						$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$serviceid'")); ?>
						<tr class="service_row">
							<?php if(in_array('Category',$service_fields)) { ?>
								<td data-title="Category" <?= !empty($template['service_category']) ? 'class="readonly-block"' : '' ?>>
									<select name="category" class="chosen-select-deselect" <?= !empty($template['service_category']) ? 'readonly disabled' : '' ?>>
										<option></option>
										<?php $service_categories = $dbc->query("SELECT `category` FROM `services` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
										while($row_cat = $service_categories->fetch_assoc()) { ?>
											<option value="<?= $row_cat['category'] ?>" <?= $row_cat['category'] == $service['category'] ? 'selected' : '' ?>><?= $row_cat['category'] ?></option>
										<?php } ?>
									</select>
								</td>
							<?php } ?>
							<?php if(in_array('Service Type',$service_fields)) { ?>
								<td data-title="Service Type">
									<select name="service_type" class="chosen-select-deselect">
										<option></option>
										<?php $service_categories = $dbc->query("SELECT `category`,`service_type` FROM `services` WHERE `deleted`=0 AND IFNULL(`service_type`,'') != '' GROUP BY CONCAT(`category`,`service_type`) ORDER BY `service_type`");
										while($service_row = $service_categories->fetch_assoc()) { ?>
											<option data-category="<?= $service_row['category'] ?>" value="<?= $service_row['service_type'] ?>" <?= $service['service_type'] == $service_row['service_type'] ? 'selected' : '' ?>><?= $service_row['service_type'] ?></option>
										<?php } ?>
									</select>
								</td>
							<?php } ?>
							<td data-title="Heading">
								<select name="heading[]" class="chosen-select-deselect">
									<option></option>
									<?php $service_categories = $dbc->query("SELECT `serviceid`,`heading`,`category`,`service_type` FROM `services` WHERE `deleted`=0 AND IFNULL(`heading`,'') != '' ORDER BY `heading`");
									while($service_row = $service_categories->fetch_assoc()) { ?>
										<option data-category="<?= $service_row['category'] ?>" data-service-type="<?= $service_row['service_type'] ?>" value="<?= $service_row['serviceid'] ?>" <?= $service_row['serviceid'] == $service['serviceid'] ? 'selected' : '' ?>><?= $service_row['heading'] ?></option>
									<?php } ?>
								</select>
							</td>
							<td data-title="Function">
		                        <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addService();">
		                        <img src="../img/remove.png" class="inline-img pull-right remove_btn" onclick="removeService(this);">
							</td>
						</tr>
					<?php } ?>
				</table>
			</div>
			<div class="clearfix"></div>
			<div class="pull-right gap-top gap-right">
			    <a href="index.php" class="btn brand-btn">Back</a>
			    <button type="submit" id="save_template" name="save_template" value="Submit" class="btn brand-btn">Save</button>
		        <a href="#" onclick="deleteServiceTemplate(); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png"></a>
			</div>
		</div>
	</div>
</form>
