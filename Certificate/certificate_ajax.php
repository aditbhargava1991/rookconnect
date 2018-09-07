<?php include_once('../include.php');
ob_clean();

if($_GET['action'] == 'update_field') {
	$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$id_field = filter_var($_POST['id_field'],FILTER_SANITIZE_STRING);
	$attach = filter_var($_POST['attached'],FILTER_SANITIZE_STRING);
	$attach_field = filter_var($_POST['attach_field'],FILTER_SANITIZE_STRING);
	if($id > 0) {
		mysqli_query($dbc, "UPDATE `$table` SET `$field`='$value' WHERE `$id_field`='$id'");
	} else {
		mysqli_query($dbc, "INSERT INTO `$table` (`$field`".($attach > 0 ? ",`$attach_field`" : '').($table == 'certificate_uploads' ? ",`type`" : '').") VALUES ('$value'".($attach > 0 ? ",'$attach'" : '').($table == 'certificate_uploads' ? ",'Link'" : '').")");
		echo mysqli_insert_id($dbc);
	}
	$new_value = filter_var($_POST['new_value'],FILTER_SANITIZE_STRING);
	if($new_value == 1) {
		if($field == 'certificate_type') {
			$certificate_types = array_filter(explode('#*#',get_config($dbc, 'certificate_types')));
			if(!in_array($value,$certificate_types)) {
				$certificate_types[] = $value;
				set_config($dbc, 'certificate_types', implode('#*#',$certificate_types));
			}
		} else if($field == 'category') {
			$certificate_categories = array_filter(explode('#*#',get_config($dbc, 'certificate_categories')));
			if(!in_array($value,$certificate_categories)) {
				$certificate_categories[] = $value;
				set_config($dbc, 'certificate_categories', implode('#*#',$certificate_categories));
			}
		}
	}
} else if($_GET['action'] == 'add_file') {
	$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$attach = filter_var($_POST['attached'],FILTER_SANITIZE_STRING);
	$attach_field = filter_var($_POST['attach_field'],FILTER_SANITIZE_STRING);
	$basename = preg_replace('/[^\.A-Za-z0-9]/','',$_FILES['file']['name']);
	$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', $basename);
	for($i = 1; file_exists('download/'.$filename); $i++) {
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.$i.')$1', $basename);
	}
	move_uploaded_file($_FILES['file']['tmp_name'],'download/'.$filename);
	mysqli_query($dbc, "INSERT INTO `$table` (`$attach_field`,`type`,`document_link`) VALUES ('$attach','Document','$filename')");
} else if($_GET['action'] == 'update_config') {
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT '$name' FROM (SELECT COUNT(*) FROM `general_configuration` WHERE `name`='$name') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$value' WHERE `name`='$name'");
}