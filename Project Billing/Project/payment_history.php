<?php include_once('../include.php');
if($_GET['id'] > 0) {
	$id = $_GET['id'];
	$history = $dbc->query("SELECT * FROM `project_payments` WHERE `id`='$id'")->fetch_assoc();
	echo '<h3>History for '.$history['heading'].'</h3>';
	echo html_entity_decode($history['history']);
} else {
	echo '<h3>No History to Display</h3>';
}