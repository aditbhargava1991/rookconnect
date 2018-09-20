<script type="text/javascript">
$(document).ready(function() {
	reloadCustomFunctions();
});
function reloadCustomFunctions() {
	$('#custom_divs').find('input,select').change(saveCustomTabs);
}
function saveCustomTabs() {
	var projecttype = $('[name="project_type"]').val();
	var custom_details = [];
	$('[name="custom_detail_heading[]"]').each(function() {
		var tab = $(this).closest('.custom_detail_block').find('[name="custom_detail_tab[]"]').val();
		var heading = $(this).val();
		var fields = [];
		$(this).closest('.custom_heading_block').find('[name="custom_detail_field[]"]:enabled').each(function() {
			fields.push($(this).val()+'####'+$(this).closest('.custom_field_block').find('[name="custom_detail_fieldtype[]"]').val());
		});
		var custom_detail = { tab: tab, heading: heading, fields: fields };
		custom_details.push(custom_detail);
	});
	custom_details = JSON.stringify(custom_details);

	$.ajax({
		url: '../Project/projects_ajax.php?action=set_custom_fields',
		method: 'POST',
		data: { type: projecttype, custom_details: custom_details },
		success: function(response) {
			// console.log(response);
		}
	});
}
function addCustomTab() {
	$.ajax({
		url: '../Project/field_config_fields_custom_details.php?new_detail=1',
		success: function(response) {
			$('#custom_divs').append(response);
			reloadCustomFunctions();
		}
	});
}
function removeCustomTab(img) {
	$(img).closest('.custom_detail_block').remove();
	saveCustomTabs();
}
function addCustomHeading(img) {
	destroyInputs('#custom_divs');
	var tab_block = $(img).closest('.custom_detail_block');
	var block = $(tab_block).find('.custom_heading_block').last();
	var clone = $(block).clone();

	while($(clone).find('.custom_field_block').length > 1) {
		removeCustomField($(clone).find('.custom_field_block').first().find('img').first());
	}

	clone.find('input,select').val('').removeAttr('disabled');
	clone.find('.field-disabled').removeClass('field-disabled');
	clone.find('select[name="custom_detail_fieldtype[]"]').val('textarea');
	block.after(clone);

	initInputs('#custom_divs');
	reloadCustomFunctions();
}
function removeCustomHeading(img) {
	var tab_block = $(img).closest('.custom_detail_block');
	if($(tab_block).find('.custom_heading_block').length <= 1) {
		addCustomHeading(img);
	}
	$(img).closest('.custom_heading_block').remove();
	saveCustomTabs();
}
function addCustomField(img) {
	destroyInputs('#custom_divs');
	var heading_block = $(img).closest('.custom_heading_block');
	var block = $(heading_block).find('.custom_field_block').last();
	var clone = $(block).clone();

	clone.find('input,select').val('').removeAttr('disabled');
	clone.find('.field-disabled').removeClass('field-disabled');
	clone.find('select[name="custom_detail_fieldtype[]"]').val('textarea');
	block.after(clone);

	initInputs('#custom_divs');
	reloadCustomFunctions();
}
function removeCustomField(img) {
	var heading_block = $(img).closest('.custom_heading_block');
	if($(heading_block).find('.custom_field_block').length <= 1) {
		addCustomField(img);
	}
	$(img).closest('.custom_field_block').remove();
	saveCustomTabs();
}
</script>
<?php
$all_custom_details = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_custom_details` WHERE `type` = 'ALL' AND '$projecttype' != 'ALL' ORDER BY `fieldconfigid` ASC"),MYSQLI_ASSOC);
$field_custom_details = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_custom_details` WHERE `type` = '$projecttype' ORDER BY `fieldconfigid` ASC"),MYSQLI_ASSOC);
$custom_details = [];
foreach($all_custom_details as $custom_detail) {
	$custom_details[$custom_detail['tab']]['disabled'] = true;
	$custom_details[$custom_detail['tab']]['headings'][$custom_detail['heading']]['disabled'] = true;
	foreach(explode('****', $custom_detail['fields']) as $custom_field) {
		$custom_field = explode('####', $custom_field);
		$custom_details[$custom_detail['tab']]['headings'][$custom_detail['heading']]['fields'][] = ['label'=>$custom_field[0],'type'=>$custom_field[1],'disabled'=>true];
	}
}
foreach($field_custom_details as $custom_detail) {
	foreach(explode('****', $custom_detail['fields']) as $custom_field) {
		$custom_field = explode('####', $custom_field);
		$custom_details[$custom_detail['tab']]['headings'][$custom_detail['heading']]['fields'][] = ['label'=>$custom_field[0],'type'=>$custom_field[1]];
	}
} ?>
<div id="custom_divs">
	<?php foreach($custom_details as $tab_name => $custom_tab) {
		include('../Project/field_config_fields_custom_details.php');
	} ?>
</div>
<a href="" onclick="addCustomTab(); return false;" class="btn brand-btn pull-right gap-top gap-bottom">Add Custom Tab</a>