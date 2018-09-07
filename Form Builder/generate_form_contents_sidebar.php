<?php
$form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id`='$form_id'"));
$form_layout = !empty($form['form_layout']) ? $form['form_layout'] : 'Accordions';

$form_accordions = [];
$div_i = 0;

if(FOLDER_NAME == 'safety') {
	$form_accordions['safety_0'] = 'General Information';
	if(empty($_GET['formid'])) {
		$form_accordions['safety_1'] = 'Attendance';
	}
	$form_accordions['safety_2'] = 'Information';
} else if($incident_report_form == 1) {
	if(!empty($report_info)) {
		$form_accordions['increp_info'] = 'Information';
	}
	$form_accordions['increp_details'] = (strpos($value_config, ','."Type_DetailsLabel".',') !== FALSE ? 'Details of Staff/Member(s) Involved' : 'Type & Individuals');
}

$field_list = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id`='".$form_id."' AND (`type` = 'ACCORDION' OR `sort_order` = 0) AND `type` NOT IN ('REFERENCE','OPTION') AND `deleted`=0 ORDER BY `sort_order`");
while($field = mysqli_fetch_array($field_list)) {
	if($field['sort_order'] == 0 && $field['type'] != 'ACCORDION') {
		$form_accordions[$div_i++] = $form['name'];
	} else {
		$form_accordions[$div_i++] = $field['label'];
	}
}
if(FOLDER_NAME == 'safety' && !empty($_GET['formid'])) {
	$form_accordions['safety_sigs'] = 'Signatures';
} else if($incident_report_form == 1 && $get_field_config['pdf_notes'] != '') {
	$form_accordions['increp_notes'] = 'Description';
}
?>
<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible" id="user_form_sidebar">
	<ul>
		<?php foreach ($form_accordions as $div_counter => $div_label) { ?>
			<a href="" data-tab-target="user_form_div_<?= $div_counter ?>"><li><?= $div_label ?></li></a>
		<?php } ?>
	</ul>
</div>