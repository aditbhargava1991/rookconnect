<script type="text/javascript">
$(document).ready(function() {
	filterRegLocClass(1);
});
function filterRegLocClass(keep_hidden = '') {
	var region = $('[name="region"]').val();
	var location = $('[name="con_location"]').val();
	var classification = $('[name="classification"]').val();

	<?php if (strpos($value_config, ','."RegLocClass Filters Project".',') !== FALSE) { ?>
		if(keep_hidden != 1) {
			$('[name="projectid"] option').show();
		}
		$('[name="projectid"] option').filter(function() {
			if(
				(region != undefined && region != '' && $(this).data('region') != undefined && $(this).data('region') != '' && $(this).data('region').indexOf(region) == -1) ||
				(location != undefined && location != '' && $(this).data('location') !=undefined && $(this).data('location') != '' && $(this).data('location').indexOf(location) == -1) ||
				(classification != undefined && classification != '' && $(this).data('classification') != undefined && $(this).data('classification') != '' && $(this).data('classification').indexOf(classification) == -1)
			) {
				return true;
			}
		}).hide();
		$('[name="projectid"]').trigger('change.select2');
	<?php } ?>

	<?php if (strpos($value_config, ','."RegLocClass Filters Business".',') !== FALSE) { ?>
		if(keep_hidden != 1) {
			$('[name="businessid"] option').show();
		}
		$('[name="businessid"] option').filter(function() {
			if(
				(region != undefined && region != '' && $(this).data('region') != undefined && $(this).data('region') != '' && $(this).data('region').indexOf(region) == -1) ||
				(location != undefined && location != '' && $(this).data('location') !=undefined && $(this).data('location') != '' && $(this).data('location').indexOf(location) == -1) ||
				(classification != undefined && classification != '' && $(this).data('classification') != undefined && $(this).data('classification') != '' && $(this).data('classification').indexOf(classification) == -1)
			) {
				return true;
			}
		}).hide();
		$('[name="businessid"]').trigger('change.select2');
	<?php } ?>
}
</script>
<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Region/Location/Classification</h3>') : '' ?>
<?php include('../Equipment/region_location_access.php'); ?>
<?php if($access_any > 0) { ?>
    <?php foreach ($field_sort_order as $field_sort_field) { ?>
		<?php if (strpos($value_config, ','."Con Region".',') !== FALSE && $field_sort_field == 'Con Region') { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Region:</label>
				<div class="col-sm-8">
				  <select name="region" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['region'] ?>" data-placeholder="Select Region" class="chosen-select-deselect form-control">
					<option></option>
					<?php $region_list = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
					foreach ($region_list as $con_region) {
					  if(in_array($con_region, $allowed_regions)) {
						echo "<option ".($get_ticket['region'] == $con_region ? 'selected' : '')." value='$con_region'>$con_region</option>";
					  }
					} ?>
				  </select>
				</div>
			</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."Con Location".',') !== FALSE && $field_sort_field == 'Con Location') { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Location:</label>
				<div class="col-sm-8">
				  <select name="con_location" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['con_location'] ?>" data-placeholder="Select Location" class="chosen-select-deselect form-control">
					<option></option>
					<?php $location_list = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
					foreach ($location_list as $con_location) {
					  if(in_array($con_location, $allowed_locations)) {
						echo "<option ".($get_ticket['con_location'] == $con_location ? 'selected' : '')." value='$con_location'>$con_location</option>";
					  }
					} ?>
				  </select>
				</div>
			</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."Con Classification".',') !== FALSE && $field_sort_field == 'Con Classification') { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Classification:</label>
				<div class="col-sm-8">
				  <select name="classification" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['classification'] ?>" data-placeholder="Select Classification" class="chosen-select-deselect form-control">
					<option data-regions="[]"></option>
					<?php $class_regions = explode(',',get_config($dbc, '%_class_regions', true, ','));
					$contact_classifications = [];
					$classification_regions = [];
					foreach(explode(',',get_config($dbc, '%_classification', true, ',')) as $i => $contact_classification) {
					  $row = array_search($contact_classification, $contact_classifications);
					  if($class_regions[$i] == 'ALL') {
						$class_regions[$i] = '';
					  }
					  if($row !== FALSE && $class_regions[$i] != '') {
						$classification_regions[$row][] = $class_regions[$i];
					  } else {
						$contact_classifications[] = $contact_classification;
						$classification_regions[] = array_filter([$class_regions[$i]]);
					  }
					}
					foreach ($contact_classifications as $i => $con_classification) {
						$hidden_classification = '';
						if(!empty($get_ticket['region']) && !in_array($get_ticket['region'], $classification_regions[$i]) && !empty($classification_regions[$i])) {
							$hidden_classification = 'style="display:none;"';
						}
						echo "<option ".($get_ticket['classification'] == $con_classification ? 'selected' : '')." data-regions='".json_encode($classification_regions[$i])."' value='$con_classification' $hidden_classification>$con_classification</option>";
					} ?>
				  </select>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
<?php } else { ?>
    <?php foreach ($field_sort_order as $field_sort_field) { ?>
		<?php if (strpos($value_config, ','."Con Region".',') !== FALSE && $field_sort_field == 'Con Region') { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Region:</label>
				<div class="col-sm-8">
				  <?= $get_ticket['region'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Region', $get_ticket['region']]; ?>
		<?php } ?>

		<?php if (strpos($value_config, ','."Con Location".',') !== FALSE && $field_sort_field == 'Con Location') { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Location:</label>
				<div class="col-sm-8">
				  <?= $get_ticket['con_location'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Location', $get_ticket['con_location']]; ?>
		<?php } ?>

		<?php if (strpos($value_config, ','."Con Classification".',') !== FALSE && $field_sort_field == 'Con Classification') { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Classification:</label>
				<div class="col-sm-8">
				  <?= $get_ticket['classification'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Classification', $get_ticket['classification']]; ?>
		<?php } ?>
	<?php } ?>
<?php } ?>