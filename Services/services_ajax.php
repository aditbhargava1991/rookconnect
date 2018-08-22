<?php include('../include.php');
error_reporting(0);
checkAuthorised();
ob_clean();

if($_GET['action'] == 'save_template_field') {
	$table = filter_var($_POST['table_name'],FILTER_SANITIZE_STRING);
	$heading_id = filter_var($_POST['heading_id'],FILTER_SANITIZE_STRING);
	$template_id = filter_var($_POST['template_id'],FILTER_SANITIZE_STRING);
	$field_name = filter_var($_POST['field_name'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);

	$sql = "";
	if($table == 'services_templates') {
		if(!is_numeric($template_id)) {
			mysqli_query($dbc, "INSERT INTO `services_templates` () VALUES ()");
			$template_id = mysqli_insert_id($dbc);
			echo $template_id;
		}
		$sql = "UPDATE `$table` SET `$field_name`='$value' WHERE `id`='$template_id'";
	} else if($table == 'services_templates_headings') {
		if(!is_numeric($heading_id)) {
			mysqli_query($dbc, "INSERT INTO `services_templates_headings` () VALUES ()");
			$heading_id = mysqli_insert_id($dbc);
			echo $heading_id;
			$before_change = "";
			$history = "Service template headings entry has been added. <br />";
			add_update_history($dbc, 'service_history', $history, '', $before_change);
		}
		$sql = "UPDATE `$table` SET `$field_name`='$value', `template_id`='$template_id' WHERE `id`='$heading_id'";
	}
	mysqli_query($dbc, $sql);
} else if($_GET['action'] == 'set_sort_order') {
	$table = filter_var($_POST['table_name'],FILTER_SANITIZE_STRING);
	$i = 0;
	foreach($_POST['sort_ids'] as $id) {
		mysqli_query($dbc, "UPDATE `$table` SET `sort_order`='$i' WHERE `id`='$id'");
		$i++;
	}
} else if($_GET['action'] == 'generate_import_csv') {
	include('field_list.php');
	$FileName = 'download/Add_multiple_services.csv';
	$file = fopen($FileName, "w");
	$HeadingsArray = [];
	foreach($field_list as $key => $field) {
		$HeadingsArray[] = $key;
	}
	fputcsv($file, $HeadingsArray);
	fclose($file);
	echo $FileName;
}
