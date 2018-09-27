<?php
include_once('../include.php');
ob_clean();

if($_GET['action'] == 'save_config') {
	$field = $_POST['field'];
	$value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	set_config($dbc, $field, $value);
}