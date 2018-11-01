<?php include_once('../include.php');
$security = get_security($dbc, $tile);
$strict_view = strictview_visible_function($dbc, 'project');
if($strict_view > 0) {
	$security['edit'] = 0;
	$security['config'] = 0;
}
if(!isset($project)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid' AND '$projectid' > 0"));
	$projecttype = $project['projecttype'];
}
$custom_tab = $_GET['custom_tab']; ?>

<script type="text/javascript">
function addCustomDetail(select) {
	var option = $(select).find('option:selected');
	if($(option).val() != '' && $(option).val() != undefined) {
		$.ajax({
			url: '../Project/edit_project_custom_details_field.php?add_new_detail=true',
			method: 'POST',
			data: {
				projectid: $(option).data('project'),
				tab: $(option).data('tab'),
				heading: $(option).data('heading'),
				fieldtype: $(option).data('fieldtype'),
				field: $(option).val()
			},
			dataType: 'html',
			success: function(response) {
				destroyInputs($(select).closest('.custom_detail_div'));
				$(select).closest('.custom_detail_div').html(response);
				$('input,select,textarea').filter('[data-table]').off('change',saveField).change(saveField).keyup(statusUnsaved);
			}
		});
	}
}
function removeCustomDetail(img) {
	var id = $(img).data('id');
	$.ajax({
		url: '../Project/projects_ajax.php?action=remove_custom_field',
		method: 'POST',
		data: { id: id },
		success: function(response) {
			console.log(response);
			$(img).closest('.form-group').remove();	
		}
	});
}
function addCustomDetailUpload(input) {
	var field_data = new FormData();
	field_data.append('id', $(input).data('id'));
	field_data.append('value', $(input)[0].files[0]);

	$.ajax({
		processData: false,
		contentType: false,
		url: '../Project/projects_ajax.php?action=add_custom_field_upload',
		type: 'POST',
		data: field_data,
		success: function(response) {
			$(input).val('');
			$(input).closest('.form-group').find('.uploader_file').html('<a href="download/'+response+'" target="_blank">View</a> | <a href="" onclick="removeCustomDetailUpload(this); return false;">Delete</a>');
		}
	});
}
function removeCustomDetailUpload(img) {
	var id = $(img).closest('.form-group').data('id');
	$.ajax({
		url: '../Project/projects_ajax.php?action=remove_custom_field_upload',
		method: 'POST',
		data: { id: id },
		success: function(response) {
			$(img).closest('.uploader_file').html('');
		}
	});
}
</script>

<?php $details_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`id`) num_rows FROM `project_custom_details` WHERE `projectid` = '$projectid' AND `tab` = '$custom_tab'"))['num_rows'];
$all_custom_details = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_custom_details` WHERE (`type` = 'ALL' OR `type` = '$projecttype') AND `tab` = '$custom_tab' ORDER BY `fieldconfigid` ASC"),MYSQLI_ASSOC);
$custom_details = [];
foreach($all_custom_details as $custom_detail) {
	foreach(explode('****', $custom_detail['fields']) as $custom_field) {
		$custom_field = explode('####', $custom_field);
		$custom_details[$custom_detail['heading']][] = ['label'=>$custom_field[0],'type'=>$custom_field[1]];
		if($details_count == 0) {
			mysqli_query($dbc, "INSERT INTO `project_custom_details` (`projectid`, `tab`, `heading`, `field`, `field_type`) VALUES ('$projectid', '$custom_tab', '{$custom_detail['heading']}', '$custom_field[0]', '$custom_field[1]')");
		}
	}
}

$custom_headings = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`heading`) FROM `field_config_project_custom_details` WHERE (`type` = 'ALL' OR `type` = '$projecttype') AND `tab` = '$custom_tab' ORDER BY `fieldconfigid` ASC"),MYSQLI_ASSOC),'heading');
foreach($custom_headings as $custom_heading) {
	include('../Project/edit_project_custom_details_field.php');
} ?>