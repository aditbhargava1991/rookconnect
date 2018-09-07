<?php //Form Field Details
include_once('../include.php');
checkAuthorised('form_builder');
include_once('../Form Builder/field_values.php');

$dropdown_source_tables = [];
$dropdown_source_categories = [];
foreach(array_filter(array_unique(explode(',',get_config($dbc,'contacts_tabs').','.get_config($dbc,'contacts3_tabs').','.get_config($dbc,'contactsrolodex_tabs').','.get_config($dbc,'clientinfo_tabs').',Staff'))) as $category) {
	if($category != '') {
		$dropdown_source_tables[] = 'contacts';
		$dropdown_source_categories[] = $category;
	}
}
sort($dropdown_source_categories);
$dropdown_field_categories = ['name', 'contact_name', 'full_address', 'street', 'city', 'province', 'postal', 'country', 'home_phone', 'office_phone', 'cell_phone', 'email_address', 'birth_date'];

$field_id = $_GET['field_id'];
$field_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `field_id` = '$field_id'"));
$form_id = $field_info['form_id'];
?>

<script type="text/javascript">
$(document).ready(function() {
	sortableServices();
	$('.form_content input,select,textarea').on('change', function() { saveFields(this); });

	var field_type = '<?= $field_info['type'] ?>';
	var group = $('.form_content');
	displayDefaultOptions(field_type, group);
	group.find('.dropdown_fields').hide();
	group.find('.option_fields').hide();
	group.find('.reference_fields').hide();
	group.find('.table_fields').hide();
	group.find('.text_content').hide();
	group.find('.tableadv_fields').hide();
	group.find('.date_format').hide();
	group.find('.contactinfo_fields').hide();
	group.find('.textblock_format').hide();
	group.find('.checkboxradio_format').hide();
	group.find('.slider_fields').hide();
	group.find('.slider_total_fields').hide();
	group.find('.pdf_styling_options').hide();
	group.find('.services_fields').hide();
	group.find('.pdf_styling_checkbox').hide();
	group.find('.select_fields').hide();
	switch(field_type) {
		case 'SERVICES':
			group.find('.default_value').hide();
			group.find('.services_fields').show();
			break;
		case 'SLIDER_TOTAL':
			group.find('.default_value').hide();
			group.find('.slider_total_fields').show();
			break;
		case 'SLIDER':
			group.find('.default_value').hide();
			group.find('.slider_fields').show();
			break;
		case 'CONTACTINFO':
			group.find('.default_value').hide();
			group.find('.contactinfo_fields').show();
			break;
		case 'DATE':
			group.find('.pdf_styling_options').show();
			group.find('.date_format').show();
			group.find('.reference_fields [name="field_references"]').trigger('change.select2');
			group.find('.reference_fields').show();
			break;
		case 'TABLEADV':
			group.find('.default_value').hide();
			group.find('.option_fields').hide();
			group.find('.text_content').hide();
			group.find('.tableadv_fields').show();
			break;
		case 'SELECT_CUS':
			group.find('.pdf_styling_options').show();
			group.find('.default_value').hide();
			group.find('.dropdown_fields').show();
			group.find('.option_fields').show();
			group.find('.text_content').hide();
			group.find('.tableadv_fields').hide();
			group.find('.select_fields').show();
			break;
		case 'RADIO':
		case 'CHECKBOX':
			group.find('.pdf_styling_options').show();
			group.find('.default_value').hide();
			group.find('.option_fields').show();
			group.find('.text_content').hide();
			group.find('.tableadv_fields').hide();
			group.find('.checkboxradio_format').show();
			group.find('.pdf_styling_checkbox').show();
			break;
		case 'SELECT':
			group.find('.pdf_styling_options').show();
			group.find('.default_value').show();
			group.find('.dropdown_fields').show();
			group.find('.text_content').hide();
			group.find('.tableadv_fields').hide();
			group.find('.select_fields').show();
			break;
		case 'TABLE':
			group.find('.default_value').hide();
			group.find('.table_fields').show();
			group.find('.text_content').hide();
			group.find('.tableadv_fields').hide();
			break;
		case 'TEXTBOXREF':
		case 'REFERENCE':
			group.find('.pdf_styling_options').show();
			group.find('.reference_fields [name="field_references"]').trigger('change.select2');
			group.find('.default_value').hide();
			group.find('.reference_fields').show();
			group.find('.text_content').hide();
			group.find('.tableadv_fields').hide();
			break;
		case 'TEXT':
			group.find('.pdf_styling_options').show();
			group.find('.pdf_styling_label').hide();
			group.find('.default_value').hide();
			group.find('.text_content').hide();
			group.find('.tableadv_fields').hide();
			break;
		case 'CHECKINFO':
		case 'TIME':
		case 'MULTISIGN':
			group.find('.pdf_styling_options').show();
			group.find('.default_value').hide();
			group.find('.text_content').hide();
			group.find('.tableadv_fields').hide();
			break;
		case 'ACCORDION':
			group.find('.pdf_styling_options').show();
			group.find('.pdf_styling_label').hide();
			group.find('.default_value').hide();
			group.find('.text_content').hide();
			group.find('.tableadv_fields').hide();
			break;
		case 'TEXTBLOCK':
			group.find('.pdf_styling_options').show();
			group.find('.default_value').hide();
			group.find('.reference_fields').hide();
			group.find('.text_content').show();
			group.find('.tableadv_fields').hide();
			group.find('.textblock_format').show();
			break;
		case 'TEXTBOX':
			group.find('.reference_fields [name="field_references"]').trigger('change.select2');
			group.find('.reference_fields').show();
			group.find('.pdf_styling_options').show();
			group.find('.default_value').show();
			group.find('.text_content').hide();
			group.find('.tableadv_fields').hide();
		break;
		case 'TEXTAREA':
		default:
			group.find('.pdf_styling_options').show();
			group.find('.default_value').show();
			group.find('.text_content').hide();
			group.find('.tableadv_fields').hide();
			
	}
});
$(document).on('change', 'select[name="field_default"]', function() { setDefault(this); });
$(document).on('change', 'select[name="set_dropdown_fields"]', function() { setDropdownFields($(this).find('option:selected')); });
$(document).on('change', 'select[name="field_field"]', function() { setDropdownFields($(this).find('option:selected')); });
$(document).on('change', 'select[name="contactinfo_category"]', function() { changeContactCategory(this); });
$(document).on('change', 'select[name="service_cat"]', function() { changeServiceCategory(this); });
function saveFields(field) {
	var formid = $('#formid').val();
	var field_id = $('#field_id').val();
	var field_type = $('#field_type').val();
	var label = $('[name=field_label]').val();
	var sublabel = $('[name=field_sublabel]').val();
	var name = $('[name=field_name]').val();
	var field_default = $('[name=field_default]').val();
	if(field_default == 'TEXT') {
		field_default = $('[name=field_default_text]').val();
	}
	var field_source_table = $('[name=field_source_table]').val();
	var field_source_conditions = $('[name=field_source_conditions]').val();
	var date_format = $('[name=date_format]').val();
	var textblock_format = $('[name="textblock_format"]').val();
	var checkboxradio_format = $('[name="checkboxradio_format"]').val();

	var option_fields = [];
	$('.option-block').each(function() {
		var option_id = $(this).find('[name="option_id[]"]').val();
		var option_totaled = 0;
		var option_input = '';

		if($(this).find('[name="option_totaled[]"]').is(':checked')) {
			option_totaled = 1;
		}
		if($(this).find('[name="option_input[]"]').is(':checked')) {
			option_input = $(this).find('[name="option_input[]"]').val();
		}
		var option_label = $(this).find('[name="option_label[]"]').val();
		if(option_label != '') {
			var option_field = { option_id: option_id, option_totaled: option_totaled, option_label: option_label, option_input: option_input };
			option_fields.push(option_field);
		}
	});
	option_fields = JSON.stringify(option_fields);

	var ref_source_table = $('[name=field_references]').val();
	var ref_source_conditions = $('[name=field_field]').val();
	if(ref_source_conditions == 'CUSTOM_VALUE') {
		ref_source_conditions = $('[name="custom_ref_value"]').val();
	}
	var ref_custom_val = $('[name=custom_ref_value]').val();
	var table_styling = $('[name=field_styling]').val();
	var content = $('[name=field_content]').val();
	var mandatory = 0;
	if($('[name=field_mandatory]').is(':checked')) {
		var mandatory = $('[name=field_mandatory]').val();
	}
	if($('[name=field_multiple]').is(':checked')) {
		content += $('[name=field_multiple]').val();
	}

	var option_row_fields = [];
	if(field_type == 'TABLEADV') {
		$('.tableadv_table tr').each(function() {
			var option_row_id = $(this).find('[name="option_row_id[]"]').val();
			var option_row = [];
			$(this).find('[name="option_row[]"]').each(function() {
				option_row.push($(this).val());
			});
			option_row_field = { option_row_id: option_row_id, option_row: option_row };
			option_row_fields.push(option_row_field);
		});
	}
	option_row_fields = JSON.stringify(option_row_fields);

	var contact_tile_name = $('[name="contactinfo_category"]').find('option:selected').data('tile');
	var contact_category = $('[name="contactinfo_category"]').val();
	var contact_fields = [];
	if(field_type == 'CONTACTINFO') {
		$('[name="contactinfo_fields[]"]').each(function() {
			if($(this).is(':checked')) {
				var field_name = $(this).val();
				var field_label = $(this).data('label');
				var contact_field = { field_name: field_name, field_label: field_label };
				contact_fields.push(contact_field);
			}
		});
	}
	contact_fields = JSON.stringify(contact_fields);

	var slider_min = $('[name="slider_min"]').val();
	var slider_max = $('[name="slider_max"]').val();
	var slider_increment = $('[name="slider_increment"]').val();
	var slider_fields = [];
	if(field_type == 'SLIDER_TOTAL') {
		$('[name="slider_fields[]"]').each(function() {
			if($(this).is(':checked')) {
				slider_fields.push($(this).val());
			}
		});
	}
	slider_fields = JSON.stringify(slider_fields);

	var services = [];
	if(field_type == 'SERVICES') {
		$('#form_services tr').each(function() {
			if($(this).data('id') != undefined) {
				services.push($(this).data('id'));
			}
		});
	}
	var services_hide_external = '';
	if($('[name="hide_from_external"]').is(':checked')) {
		services_hide_external = 'hide_from_external';
	}

	var pdf_align = $('[name="pdf_align"]').val();
	var pdf_label = $('[name="pdf_label"]:checked').val();
	var pdf_checkbox = $('[name="pdf_checkbox_style"]:checked').val();
	var pdf_checkbox_size = $('[name="pdf_checkbox_size"]:checked').val();

	var field_data = { formid: formid, field_id: field_id, field_type: field_type, label: label, sublabel: sublabel, name: name, field_default: field_default, field_source_table: field_source_table, field_source_conditions: field_source_conditions, date_format: date_format, textblock_format: textblock_format, checkboxradio_format: checkboxradio_format, option_fields: option_fields, ref_source_table: ref_source_table, ref_source_conditions: ref_source_conditions, ref_custom_val: ref_custom_val, table_styling: table_styling, content: content, mandatory: mandatory, option_row_fields: option_row_fields, contact_tile_name: contact_tile_name, contact_category: contact_category, contact_fields: contact_fields, slider_min: slider_min, slider_max: slider_max, slider_increment: slider_increment, slider_fields: slider_fields, services: services, services_hide_external: services_hide_external, pdf_align: pdf_align, pdf_label: pdf_label, pdf_checkbox: pdf_checkbox, pdf_checkbox_size: pdf_checkbox_size };
	$.ajax({
		url: '../Form Builder/form_ajax.php?fill=update_field',
		type: 'POST',
		data: field_data,
		success: function(response) {
			var response_arr = response.split('*#*');
			if(response_arr[0] == 'name_exists') {
				alert(response_arr[1]);
				$('[name=field_name]').val(response_arr[2]);
			} else if(field != undefined && field.name == 'option_label[]' && response != '') {
				$(field).closest('.option-block').find('[name="option_id[]"]').val(response);
			} else if(response_arr[0] == 'tableadv_ids') {
				var counter = 1;
				$('.tableadv_table tr').each(function() {
					$(this).find('[name="option_row_id[]"]').val(response_arr[counter]);
					counter++;
				});
			}
			var parent_label = window.parent.$('.field_sortable[data-fieldid='+field_id+'] a');
			parent_label_text = parent_label.html().split(':')[0]+': '+label;
			parent_label.html(parent_label_text);
		}
	});

}
function addOption(link) {
	var option = $(link).closest('.option_fields,.table_fields').find('div.option-block').last().clone();
	option.find('input[type=text]').val('');
	option.find('input[type=hidden]').val('');
	option.find('input[type=checkbox]').attr('checked', false);
	$(link).closest('.option_fields,.table_fields').append(option).find('input[type=text]').last().focus();
	$('.form_content input,select,textarea').on('change', function() { saveFields(this); });
	return false;
}
function remOption(link) {
	if($(link).closest('.option_fields,.table_fields').find('div.option-block').length == 1) {
		addOption(link);
	}
	var delete_id = $(link).parent('div.col-sm-8').find('input[name="option_id[]"]').val();
	var delete_html = '<input type="hidden" name="delete_id[]" value="' + delete_id + '">';
	$('#collapse_fields .panel-body').append(delete_html);
	$(link).closest('div.option-block').remove();
	saveFields();
	return false;
}
function setDefault(select) {
	if(select.value != 'SESSION_CONTACT' && select.value != 'TIMESTAMP' && select.value != '') {
		$(select).closest('.form-group').find('[name=field_default_text]').show();
	} else {
		$(select).closest('.form-group').find('[name=field_default_text]').hide();
	}
}
function setDropdownFields(option) {
	if ($(option).val() == 'CUSTOM_VALUE') {
		$(option).closest('.reference_fields').find('div.custom_ref_value').show();
		$(option).closest('.form-horizontal').find('div.option_fields').hide();
		// $(option).closest('.reference_fields').find('div.custom_ref_value').find('input[name="custom_ref_value[]"]').val('');
	} else if($(option).val() == 'SELECT_CUS') {
		$(option).closest('.reference_fields').find('div.custom_ref_value').hide();
		$(option).closest('.form-horizontal').find('div.option_fields').show();
		$(option).closest('.form-horizontal').find('.default_value').hide();
		$(option).closest('.dropdown_fields').find('[name="field_source_table"]').val($(option).data('table'));
		$(option).closest('.dropdown_fields').find('[name="field_source_conditions"]').val($(option).data('condition'));
	} else {
		$(option).closest('.reference_fields').find('div.custom_ref_value').hide();
		$(option).closest('.form-horizontal').find('div.option_fields').hide();
		if($(option).closest('.form-group').hasClass('dropdown_fields')) {
			$(option).closest('.form-horizontal').find('.default_value').show();
		}
		// $(option).closest('.reference_fields').find('div.custom_ref_value').find('input[name="custom_ref_value[]"]').val('not_custom');
		$(option).closest('.dropdown_fields').find('[name="field_source_table"]').val($(option).data('table'));
		$(option).closest('.dropdown_fields').find('[name="field_source_conditions"]').val($(option).data('condition'));
	}
	saveFields();
}
function addContentInput(button) {
	var text_content = $(button).closest('.text_content').find('textarea');
	tinyMCE.get(text_content.attr('id')).execCommand('mceInsertContent', false, '[[input]]');
}
function tableAddRow(link) {
	var row = $(link).closest('div.col-sm-12').find('table tr').last().clone();
	row.find('input.form-control').val('');
	row.find('input.option_row_id').val('')
	$(link).closest('div.col-sm-12').find('table').append(row);
	$('.form_content input,select,textarea').on('change', function() { saveFields(this); });
	saveFields();
	return false;
}
function tableRemRow(link) {
	var delete_id = $(link).closest('div.col-sm-12').find('table tr').last().find('input.option_row_id').val();
	var delete_html = '<input type="hidden" name="delete_id[]" value="' + delete_id + '">';
	$('#collapse_fields .panel-body').append(delete_html);

	if ($(link).closest('div.col-sm-12').find('table tr').length == 2) {
		tableAddRow(link);
		$(link).closest('div.col-sm-12').find('table tr').last().prev().remove();
	} else {
		$(link).closest('div.col-sm-12').find('table tr').last().remove();
	}
	saveFields();
	return false;
}
function tableAddCol(link) {
	$(link).closest('div.col-sm-12').find('table tr').each(function() {
		var table_cell = $(this).find('td').last().clone();
		table_cell.find('input').val('');
		$(this).append(table_cell);
		var table_cell = $(this).find('th').last().clone();
		table_cell.find('input').val('');
		$(this).append(table_cell);
	});
	$('.form_content input,select,textarea').on('change', function() { saveFields(this); });
	saveFields();
	return false;
}
function tableRemCol(link) {
	$(link).closest('div.col-sm-12').find('table tr').each(function() {
		if ($(this).find('th').length == 1 || $(this).find('td').length == 1) {
			$(this).find('th').first().find('input').val('');
			$(this).find('td').first().find('input').val('');
		} else {
			$(this).find('th').last().remove();
			$(this).find('td').last().remove();
		}
	});
	saveFields();
	return false;
}
function displayDefaultOptions(type, group) {
	var default_sel = $(group).find('[name="field_default"]');
	default_sel.find('option').hide();
	default_sel.find('option[value=""]').show();
	switch(type) {
		case 'DATE':
		case 'DATETIME':
			default_sel.find('option[value="TIMESTAMP"]').show();
			break;
		case 'SIGNONLY':
		case 'SIGN':
		case 'SELECT':
			default_sel.find('option[value="SESSION_CONTACT"]').show();
			break;
		default:
			default_sel.find('option').show();
	}
	default_sel.trigger('change.select2');
}
function closeIFrame() {
	window.parent.$('.iframe_overlay').hide();
	window.parent.$('.iframe_overlay .iframe iframe').off('load').attr('src', '');
}
function changeContactCategory(sel) {
	var tile_name = $(sel).find('option:selected').data('tile');
	var category = sel.value;
	$.ajax({
		url: '../Form Builder/edit_form_field_details_contact.php?tile_name='+tile_name+'&category='+category,
		type: 'GET',
		dataType: 'html',
		success: function(response) {
			destroyInputs('.contactinfo_fields');
			$('.contactinfo_fields_table').html(response);
			initInputs('.contactinfo_fields');
			$('select[name="contactinfo_category"]').on('change', function() { changeContactCategory(this); });
			$('.form_content input,select,textarea').on('change', function() { saveFields(this); });
		}
	});
}
function changeServiceCategory(sel) {
	var category = sel.value;
	$.ajax({
		url: '../Form Builder/edit_form_field_details_services.php?category='+category,
		type: 'GET',
		dataType: 'html',
		success: function(response) {
			$('#form_add_services').html(response);
		}
	});
}
function addService(img) {
	var tr = $(img).closest('tr');
	var clone = $(tr).clone();
	clone.find('.add_img').remove();
	clone.find('.added_img').show();
	$('#form_services').append(clone);
	saveFields();
	sortableServices();
}
function removeService(img) {
	$(img).closest('tr').remove();
	saveFields();
}
function sortableServices() {
	$('#form_services').sortable({
		items: '.sortable-service',
		handle: '.drag-handle',
		stop: function(e, block) {
			saveFields();
		}
	});
}
</script>

<div class="form-horizontal form_content" style="padding: 0.5em;">
	<input type="hidden" id="formid" value="<?= $form_id ?>">
	<input type="hidden" id="field_id" value="<?= $field_id ?>">
	<input type="hidden" id="field_type" value="<?= $field_info['type'] ?>">
	<h3 style="margin-top: 0px;"><?= $field_types[$field_info['type']] ?></h3>
	<div class="notice double-gap-bottom popover-examples">
		<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
		<div class="col-sm-11"><span class="notice-name">NOTE:</span> <?= ($field_info['type'] == 'SELECT_CUS' ? $field_note['SELECT'] : $field_note[$field_info['type']]) ?></div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group">
		<label class="col-sm-12">Field Name:<br /><em>This must be a unique name for the field for this form.</em></label>
		<div class="col-sm-12">
			<input type="text" name="field_name" value="<?= $field_info['name'] ?>" class="form-control">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-12">Field Label:<br /><em>This is what will appear on the screen when completing the form.</em></label>
		<div class="col-sm-12">
			<input type="text" name="field_label" value="<?= $field_info['label'] ?>" class="form-control">
		</div>
	</div>

	<div class="form-group">
		<label class="col-sm-12">Field Sub-Label:<br /><em>This is what will appear underneath the label in a smaller font.</em></label>
		<div class="col-sm-12">
			<input type="text" name="field_sublabel" value="<?= $field_info['sublabel'] ?>" class="form-control">
		</div>
	</div>

	<div class="form-group default_value">
		<label class="col-sm-12 default_value">Default Value:</label>
		<div class="col-sm-12 default_value">
			<span class="selectSpan"><select name="field_default" value="<?= $field_info['default'] ?>" class="chosen-select-deselect"><option></option>
				<option <?= $field_info['default'] == 'SESSION_CONTACT' ? 'selected' : '' ?> value="SESSION_CONTACT">Current User</option>
				<option <?= $field_info['default'] == 'TIMESTAMP' ? 'selected' : '' ?> value="TIMESTAMP">Current Date/Time</option>
				<option <?= $field_info['default'] != 'TIMESTAMP' && $field_info['default'] != 'SESSION_CONTACT' && $field_info['default'] != '' ? 'selected' : '' ?> value="TEXT">Text Value</option></select></span>
		</div>
		<div class="col-sm-12 default_value">
			<input type="text" name="field_default_text" value="<?= $field_info['default'] != 'TIMESTAMP' && $field_info['default'] != 'SESSION_CONTACT' ? $field_info['default'] : '' ?>" class="form-control" <?= $field_info['default'] != 'TIMESTAMP' && $field_info['default'] != 'SESSION_CONTACT' && $field_info['default'] != '' ? '' : 'style="display:none;"' ?>>
		</div>
	</div>

	<div class="form-group dropdown_fields" style="display:none;">
		<input type="hidden" name="field_source_table" value="<?= $field_info['source_table'] ?>">
		<input type="hidden" name="field_source_conditions" value="<?= $field_info['source_conditions'] ?>">
		<label class="col-sm-12">Dropdown Source:</label>
		<div class="col-sm-12">
			<select class="chosen-select-deselect form-control" name="set_dropdown_fields" ><option></option>
				<option <?= $field_info['type'] == 'SELECT_CUS' ? 'selected' : '' ?> data-table="SELECT_CUS" data-condition="SELECT_CUS" value="SELECT_CUS">Custom Values</option>
				<?php foreach($dropdown_source_categories as $i => $category) {
					echo "<option ".($category == $field_info['source_conditions'] && $dropdown_source_tables[$i] == $field_info['source_table'] ? 'selected' : '')." data-table='".$dropdown_source_tables[$i]."' data-condition='$category'>$category Contacts</option>";
				} ?>
			</select>
		</div>
	</div>

	<div class="form-group date_format" style="display:none;">
		<label class="col-sm-12">Date Format:</label>
		<div class="col-sm-12">
			<select class="chosen-select-deselect form-control" name="date_format">
				<option <?= ($field_info['styling'] != '/' ? 'selected' : '') ?>>YYYY-MM-DD</option>
				<option <?= ($field_info['styling'] == '/' ? 'selected' : '') ?> value="/">YYYY/MM/DD</option>
			</select>
		</div>
	</div>

	<div class="form-group option_fields" style="display:none;">
		<label class="col-sm-12">Option Values:</label>
		<?php $option_list = mysqli_query($dbc, "SELECT `field_id`, `label`, `source_conditions`, `sort_order`, 1 as rowOrder FROM `user_form_fields` WHERE `form_id`='$form_id' AND `type`='OPTION' AND `name`='".$field_info['name']."' AND '".$field_info['type']."' IN ('RADIO','CHECKBOX','SELECT_CUS') AND `deleted`=0 UNION SELECT 0, '', '', 0, 2 as rowOrder ORDER BY `rowOrder`, `sort_order`");
		while($option_info = mysqli_fetch_array($option_list)) { ?>
			<div class="option-block">
				<input type="hidden" name="option_id[]" value="<?= $option_info['field_id'] ?>">
				<input type="hidden" name="option_totaled[]" value="0">
				<?php if($field_info['type'] == 'CHECKBOX') { ?>
					<div class="col-sm-7"><input type="text" class="form-control" name="option_label[]" onchange="" value="<?= $option_info['label'] ?>"></div>
					<label class="col-sm-2 form-checkbox"><input type="checkbox" name="option_input[]" onchange="" value="input" <?= $option_info['source_conditions'] == 'input' ? 'checked' : '' ?>> Add Input Box</label>
				<?php } else { ?>
					<div class="col-sm-10"><input type="text" class="form-control" name="option_label[]" onchange="" value="<?= $option_info['label'] ?>"></div>
				<?php } ?>
				<a href="" onclick="return addOption(this);" class="pull-right"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img"></a>
				<a href="" onclick="return remOption(this);" class="pull-right" style="position: relative; left: -1em;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img"></a>
			</div>
		<?php } ?>
	</div>

	<div class="form-group reference_fields" style="display:none;">
		<?php
			$is_custom_value = (!in_array($field_info['source_conditions'], $dropdown_field_categories) && $field_info['source_conditions'] != '') ? true : false;
			$ref_sources = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$form_id' AND `deleted` = 0 AND `type` = 'SELECT'"),MYSQLI_ASSOC);
		?>
		<label class="col-sm-12">Reference Source:<br /><em>This field will pull a chosen value based on the contact selected in the Reference Source field.</em></label>
		<div class="col-sm-12">
			<select class="chosen-select-deselect form-control" name="field_references" data-id="<?= $field_info['references'] ?>">
				<option></option>
				<?php if(!empty($ref_sources)) {
					foreach($ref_sources as $ref_source) { ?>
						<option <?= $field_info['references'] == $ref_source['field_id'] ? 'selected' : '' ?> value="<?= $ref_source['field_id'] ?>"><?= $ref_source['name'].(!empty($ref_source['label']) ? ' ('.$ref_source['label'].')' : '') ?></option>
					<?php }
				} else { ?>
					<option value=0>N/A</option>
				<?php } ?>
			</select>
		</div>
		<div class="clearfix"></div>
	</div>

	<div class="form-group reference_fields" style="display:none;">
		<label class="col-sm-12">Reference Field Name:</label>
		<div class="col-sm-12">
			<select class="chosen-select-deselect form-control" name="field_field"><option></option>
				<option <?= $field_info['source_conditions'] == 'name' ? 'selected' : '' ?> value="name">Business Name</option>
				<option <?= $field_info['source_conditions'] == 'contact_name' ? 'selected' : '' ?> value="contact_name">Contact Name</option>
				<option <?= $field_info['source_conditions'] == 'full_address' ? 'selected' : '' ?> value="full_address">Full Address</option>
				<option <?= $field_info['source_conditions'] == 'street' ? 'selected' : '' ?> value="street">Street Address</option>
				<option <?= $field_info['source_conditions'] == 'city' ? 'selected' : '' ?> value="city">City</option>
				<option <?= $field_info['source_conditions'] == 'province' ? 'selected' : '' ?> value="province">Province</option>
				<option <?= $field_info['source_conditions'] == 'postal_code' ? 'selected' : '' ?> value="postal">Postal Code</option>
				<option <?= $field_info['source_conditions'] == 'country' ? 'selected' : '' ?> value="country">Country</option>
				<option <?= $field_info['source_conditions'] == 'home_phone' ? 'selected' : '' ?> value="home_phone">Home Phone</option>
				<option <?= $field_info['source_conditions'] == 'office_phone' ? 'selected' : '' ?> value="business_phone">Business Phone</option>
				<option <?= $field_info['source_conditions'] == 'cell_phone' ? 'selected' : '' ?> value="cell_phone">Cell Phone</option>
				<option <?= $field_info['source_conditions'] == 'email_address' ? 'selected' : '' ?> value="email_address">Email Address</option>
				<option <?= $field_info['source_conditions'] == 'birth_date' ? 'selected' : '' ?> value="birth_date">Birth Date</option>
				<option value="CUSTOM_VALUE" <?= $is_custom_value ? 'selected' : '' ?>>Custom Value</option>
			</select>
		</div>
		<div class="clearfix"></div>
		<div class="custom_ref_value" <?= $is_custom_value ? '' : 'style="display:none;"' ?>>
			<br>
			<label class="col-sm-12">Custom Value:<br /><em>Enter the field name from contacts database to search.</em></label>
			<div class="col-sm-12">
				<input name="custom_ref_value" class="form-control" value="<?= $is_custom_value ? $field_info['source_conditions'] : '' ?>">
			</div>
		</div>
	</div>

	<div class="form-group table_fields" style="display:none;">
		<label class="col-sm-12">Column Headings:</label>
		<?php $option_list = mysqli_query($dbc, "SELECT `field_id`, `label`, `totaled`, `sort_order`, 1 as rowOrder FROM `user_form_fields` WHERE `form_id`='$form_id' AND `type`='OPTION' AND `name`='".$field_info['name']."' AND '".$field_info['type']."' IN ('TABLE') AND `deleted`=0 UNION SELECT 0, '', 0, 0, 2 as rowOrder ORDER BY `rowOrder`, `sort_order`");
		while($option_info = mysqli_fetch_array($option_list)) { ?>
			<div class="option-block">
				<input type="hidden" name="option_id[]" value="<?= $option_info['field_id'] ?>">
				<div class="col-sm-9"><input type="text" class="form-control" name="option_label[]" onchange="" value="<?= $option_info['label'] ?>"></div>
				<div class="col-sm-2">
					<label><input type="checkbox" class="form-control" name="option_totaled[]" <?= $option_info['totaled'] == 1 ? 'checked' : '' ?> value="1"> Total<label></div>
				<a href="" onclick="return addOption(this);" class="pull-right"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img"></a>
				<a href="" onclick="return remOption(this);" class="pull-right" style="position: relative; left: -1em;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img"></a>
			</div>
		<?php } ?>
	</div>

	<div class="form-group tableadv_fields" style="display: none;">
		<div>
			<label class="col-sm-12">Table:<br /><em>Cells with text will display the text and empty cells will create an input box.<br />To style rows enter the styling in [[ and ]] after text (eg. [[colspan="2" style="border: 0px;"]]).<br />To disable a row enter [[disable]].<br />To use a checkbox enter [[checkbox]].<br>For simple calculations, start the cell with = and format like Excel (eg. =C2*D2, =E2+E3+E4). To get cells from different tables, use the field_name followed by a period and ending with the cell (eg. =table_a.E5+table_b.E5).<br />Use [[bullet]] to add a bullet point before text.<br>Use [[checkbox="x"]] to mark checkboxes as x.<br>Use [[checkbox="large_chk"]] for large checkmark image.</em></label>
			<div class="col-sm-12">
				<table class="table table-bordered tableadv_table">
				<?php $option_list = mysqli_query($dbc, "SELECT `field_id`, `label`, `totaled` FROM `user_form_fields` WHERE `form_id` = '$form_id' AND `type` = 'OPTION' AND `name` = '".$field_info['name']."' AND '".$field_info['type']."' IN ('TABLEADV') AND `deleted` = 0 ORDER BY `sort_order`");
				$option_list = mysqli_fetch_all($option_list, MYSQLI_ASSOC); 
				if (count($option_list) < 2) {
					echo '<tr>';
					echo '<input type="hidden" name="option_row_id[]" class="option_row_id">';
					echo '<th><input type="text" name="option_row[]" class="form-control"></th>';
					echo '</tr>';
					echo '<tr>';
					echo '<input type="hidden" name="option_row_id[]" class="option_row_id">';
					echo '<td><input type="text" name="option_row[]" class="form-control"></td>';
					echo '</tr>';
				} else {
					$table_headers = explode('*#*', $option_list[0]['label']);
					echo '<tr>';
					echo '<input type="hidden" name="option_row_id[]" value="'.$option_list[0]['field_id'].'" class="option_row_id">';
					foreach ($table_headers as $table_header) { ?>
						<th><input type="text" name="option_row[]" value="<?= $table_header ?>" class="form-control"></th>
					<?php }
					echo '</tr>';
					for ($i = 1; $i < count($option_list); $i++) {
						$table_row = explode('*#*', $option_list[$i]['label']);
						echo '<tr>';
						echo '<input type="hidden" name="option_row_id[]" value="'.$option_list[$i]['field_id'].'" class="option_row_id">';
						foreach ($table_row as $single_cell) { ?>
							<td><input type="text" name="option_row[]" value="<?= $single_cell ?>" class="form-control"></td>
						<?php }
						echo '</tr>';
					}
				}
				?>
				</table>
				<div class="pull-right">
					<label class="control-label" style="position:relative; left: -1.5em;">Rows:</label>
						<a href="" onclick="return tableRemRow(this);" style="position: relative; left: -1em;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img"></a>
						<a href="" onclick="return tableAddRow(this);"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img"></a>
				</div>
				<div class="clearfix"></div>
				<div class="pull-right">
					<label class="control-label" style="position: relative; left: -1.5em;">Columns:</label>
						<a href="" onclick="return tableRemCol(this);" style="position: relative; left: -1em;"><img src="<?= WEBSITE_URL ?>/img/remove.png" class="inline-img"></a>
						<a href="" onclick="return tableAddCol(this);"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="inline-img"></a>
				</div>
			</div>
		</div>
	</div>
	<div class="form-group tableadv_fields" style="display: none;">
		<label class="col-sm-12">Table Styling:<br><em>Use [[checkbox="x"]] to mark checkboxes as x.<br>Use [[checkbox="large_chk"]] for large checkmark image.</em></label>
		<div class="col-sm-12">
			<input type="text" name="field_styling" value="<?= $field_info['styling'] ?>" class="form-control">
		</div>
	</div>

	<div class="form-group text_content" style="display:none;">
		<label class="col-sm-12">Content:</label>
		<div class="col-sm-12">
			<textarea name="field_content" class="form-control"><?= $field_info['type'] != 'SELECT' && $field_info['type'] != 'SELECT_CUS' ? html_entity_decode($field_info['content']) : '' ?></textarea>
		</div>
		<button id="add_content_input" class="btn brand-btn pull-right" onclick="addContentInput(this); return false;">Add Input</button>
	</div>

	<div class="form-group textblock_format" style="display:none;">
		<label class="col-sm-12">Text Format:<br><em>How user inputs will be formatted. Either with an underline or no underline.</em></label>
		<div class="col-sm-12">
			<select class="chosen-select-deselect form-control" name="textblock_format">
				<option <?= ($field_info['styling'] != 'nounderline' ? 'selected' : '') ?>>Inputs Underlined</option>
				<option <?= ($field_info['styling'] == 'nounderline' ? 'selected' : '') ?> value="nounderline">Inputs Not Underlined</option>
			</select>
		</div>
	</div>

	<div class="form-group checkboxradio_format" style="display:none;">
		<label class="col-sm-12">Checked Format:<br><em>How a checked Checkbox or Radio button will be filled. Either with a solid color or an X.</em></label>
		<div class="col-sm-12">
			<select class="chosen-select-deselect form-control" name="checkboxradio_format">
				<option <?= (strpos(','.$field_info['styling'].',', ',x,') === FALSE ? 'selected' : '') ?>>Solid Fill</option>
				<option <?= (strpos(','.$field_info['styling'].',', ',x,') !== FALSE ? 'selected' : '') ?> value="x">Fill with X</option>
			</select>
		</div>
	</div>

	<div class="form-group contactinfo_fields" style="display:none;">
		<label class="col-sm-12">Contact Category:</label>
		<div class="col-sm-12">
			<select name="contactinfo_category" class="chosen-select-deselect form-control">
				<option></option>
				<?php $all_tiles = [];
					$all_tiles['contacts'] = array_unique(array_filter(explode(',', get_config($dbc, 'contacts_tabs'))));
					$all_tiles['contactsrolodex'] = array_unique(array_filter(explode(',', get_config($dbc, 'contactsrolodex_tabs'))));
					$all_tiles['contacts3'] = array_unique(array_filter(explode(',', get_config($dbc, 'contacts3_tabs'))));
					$all_tiles['clientinfo'] = array_unique(array_filter(explode(',', get_config($dbc, 'clientinfo_tabs'))));
					$all_tiles['members'] = array_unique(array_filter(explode(',', get_config($dbc, 'members_tabs'))));
					$all_tiles['vendors'] = array_unique(array_filter(explode(',', get_config($dbc, 'vendors_tabs'))));
					$all_cats = [];
					foreach($all_tiles as $tile_name => $tile_cats) {
						foreach ($tile_cats as $cat_name) {
							$selected_cat = '';
							if($field_info['source_table'] == $tile_name && $field_info['source_conditions'] == $cat_name) {
								$selected_cat = 'selected';
							}
							echo '<option data-tile="'.$tile_name.'" value="'.$cat_name.'" '.$selected_cat.'>'.$cat_name.'</option>';
						}
					}
				?>
			</select>
		</div>
	</div>

	<div class="form-group contactinfo_fields" style="display:none;">
		<label class="col-sm-12">Contact Fields:</label>
		<div class="col-sm-12 contactinfo_fields_table">
			<?php include('../Form Builder/edit_form_field_details_contact.php'); ?>
		</div>
	</div>

	<div class="form-group slider_fields" style="display:none;">
		<label class="col-sm-12">Min Value:</label>
		<div class="col-sm-12">
			<input type="number" name="slider_min" class="form-control" value="<?= (explode(',',$field_info['content'])[0]) > 0 ? explode(',',$field_info['content'])[0] : '0' ?>">
		</div>
	</div>

	<div class="form-group slider_fields" style="display:none;">
		<label class="col-sm-12">Max Value:</label>
		<div class="col-sm-12">
			<input type="number" name="slider_max" class="form-control" value="<?= !empty(explode(',',$field_info['content'])[1]) ? explode(',',$field_info['content'])[1] : '100' ?>">
		</div>
	</div>

	<div class="form-group slider_fields" style="display:none;">
		<label class="col-sm-12">Increment:</label>
		<div class="col-sm-12">
			<input type="number" name="slider_increment" class="form-control" value="<?= !empty(explode(',',$field_info['content'])[2]) ? explode(',',$field_info['content'])[2] : '1' ?>" min="1">
		</div>
	</div>

	<div class="form-group slider_total_fields" style="display:none;">
		<label class="col-sm-12">Sliders:</label>
		<div class="col-sm-12">
			<div class="block-group">
				<?php $sliders = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `type` = 'SLIDER' AND `form_id` = '$form_id' AND `deleted` = 0"),MYSQLI_ASSOC);
				foreach ($sliders as $slider) { ?>
					<label class="form-checkbox"><input type="checkbox" name="slider_fields[]" value="<?= $slider['field_id'] ?>" <?= strpos(','.$field_info['content'].',', ','.$slider['field_id'].',') !== FALSE ? 'checked' : '' ?>> <?= $slider['label'] ?></label>
				<?php } ?>
			</div>
		</div>
	</div>

	<div class="form-group services_fields" style="display: none;">
		<label class="col-sm-12">Services:</label>
		<div class="col-sm-12">
			<table class="table table-bordered" id="form_services">
				<tr>
					<th>Service</th>
				</tr>
				<?php $form_services = mysqli_fetch_all(mysqli_query($dbc, "SELECT `source_conditions` FROM `user_form_fields` WHERE `form_id`='$form_id' AND `type`='OPTION' AND `name`='".$field_info['name']."' AND '".$field_info['type']."' IN ('SERVICES') AND `deleted`=0 ORDER BY `sort_order`"),MYSQLI_ASSOC);
				foreach($form_services as $form_service) {
					if($form_service['source_conditions'] > 0) {
						$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '".$form_service['source_conditions']."'")); ?>
						<tr class="sortable-service" data-id="<?= $service['serviceid'] ?>">
							<td data-title="Service">
								<?= $service['heading'] ?>
								<div class="pull-right">
									<img src="../img/remove.png" class="inline-img added_img" onclick="removeService(this);">
									<img src="../img/icons/drag_handle.png" class="inline-img added_img drag-handle">
								</div>
							</td>
						</tr>
					<?php }
				} ?>
			</table>
		</div>
	</div>

	<div class="form-group services_fields" style="display: none;">
		<label class="col-sm-12">Add More Services:</label>
		<div class="col-sm-12">
			<select name="service_cat" data-placeholder="Select a Category" class="chosen-select-deselect form-control">
				<option></option>
				<?php $query = mysqli_query($dbc, "SELECT DISTINCT `category` FROM `services` WHERE IFNULL(`category`,'') != '' AND `deleted` = 0 ORDER BY `category`");
				while($row = mysqli_fetch_array($query)) { ?>
					<option value="<?= $row['category'] ?>"><?= $row['category'] ?></option>
				<?php } ?>
			</select>
		</div>
		<div class="col-sm-12" id="form_add_services">
		</div>
	</div>

	<div class="form-group services_fields" style="display: none;">
		<label class="col-sm-12">
			Hide Prices From External Users:&nbsp;
			<input type="checkbox" name="hide_from_external" value="hide_from_external" <?= strpos(','.$field_info['source_conditions'].',', ',hide_from_external,') !== FALSE ? 'checked' : '' ?> style="position: relative; top: 0.5em;">
	</div>

	<div class="form-group select_fields" style="display: none;">
		<label class="col-sm-12">
			Allow Multiple Selections:&nbsp;
			<input type="checkbox" name="field_multiple" value="multiple" <?= $field_info['content'] == 'multiple' ? 'checked' : '' ?> style="position: relative; top: 0.5em;">
		</label>
	</div>

	<div class="mandatory_field">
		<label class="col-sm-12">
			Mandatory:&nbsp;
			<input type="checkbox" name="field_mandatory" value="1" <?= $field_info['mandatory'] == 1 ? 'checked' : '' ?> style="position: relative; top: 0.5em;">
		</label>
	</div>

	<div class="clearfix"></div><hr>

	<div class="pdf_styling_options">
		<h4>PDF Styling Options</h4>
		<div class="form-group pdf_styling_align">
			<label class="col-sm-12">Text Align:</label>
			<div class="col-sm-12">
				<select name="pdf_align" class="chosen-select-deselect form-control">
					<option value="left" <?= empty($field_info['pdf_align']) || $field_info['pdf_align'] == 'left' ? 'selected' : '' ?>>Left</option>
					<option value="center" <?= $field_info['pdf_align'] == 'center' ? 'selected' : '' ?>>Center</option>
					<option value="right" <?= $field_info['pdf_align'] == 'right' ? 'selected' : '' ?>>Right</option>
				</select>
			</div>
		</div>
		<div class="form-group pdf_styling_label">
			<label class="col-sm-12">Label Style:</label>
			<div class="col-sm-12">
				<label class="form-checkbox" style="vertical-align: top;"><input type="radio" name="pdf_label" value="0" <?= $field_info['pdf_label'] == 0 ? 'checked' : '' ?>><br>Label<br>Field</label>
				<label class="form-checkbox" style="vertical-align: top;"><input type="radio" name="pdf_label" value="1" <?= $field_info['pdf_label'] == 1 ? 'checked' : '' ?>><br>Label: Field</label>
				<label class="form-checkbox" style="vertical-align: top;"><input type="radio" name="pdf_label" value="2" <?= $field_info['pdf_label'] == 2 ? 'checked' : '' ?>><br>Label<br><li>Field</li></label>

			</div>
		</div>
		<div class="form-group pdf_styling_checkbox" style="display: none;">
			<label class="col-sm-12"><?= $field_info['type'] == 'RADIO' ? 'Radio' : 'Checkbox' ?> Options Style:</label>
			<div class="col-sm-12">
				<label class="form-checkbox"><input type="radio" name="pdf_checkbox_style" value="" <?= strpos(','.$field_info['styling'].',', ',newline,') === FALSE ? 'checked' : '' ?>> Same Line (Same line all options)</label>
				<label class="form-checkbox"><input type="radio" name="pdf_checkbox_style" value="newline" <?= strpos(','.$field_info['styling'].',', ',newline,') !== FALSE ? 'checked' : '' ?>> Multiple Lines (New line per option)</label>
			</div>
		</div>
		<div class="form-group pdf_styling_checkbox" style="display: none;">
			<label class="col-sm-12"><?= $field_info['type'] == 'RADIO' ? 'Radio' : 'Checkbox' ?> Size:</label>
			<div class="col-sm-12">
				<label class="form-checkbox"><input type="radio" name="pdf_checkbox_size" value="" <?= strpos(','.$field_info['styling'].',', ',chk_med,') === FALSE && strpos(','.$field_info['styling'].',', ',chk_lrg,') === FALSE ? 'checked' : '' ?>> Small</label>
				<label class="form-checkbox"><input type="radio" name="pdf_checkbox_size" value="chk_med" <?= strpos(','.$field_info['styling'].',', ',chk_med,') !== FALSE ? 'checked' : '' ?>> Medium</label>
				<label class="form-checkbox"><input type="radio" name="pdf_checkbox_size" value="chk_lrg" <?= strpos(','.$field_info['styling'].',', ',chk_lrg,') !== FALSE ? 'checked' : '' ?>> Large</label>
			</div>
		</div>
	</div>

	<div class="form-group pull-right">
		<button type="button" onclick="closeIFrame();" class="btn brand-btn">Finish</button>
	</div>

</div>