<?php
include_once('../include.php');
ob_clean();

if($_GET['fill'] == 'contact_category') {
	$selected_contacts = ','.$_POST['selected_contacts'].',';
    $category = implode("','",explode(',',$_GET['category']));
	$query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE category IN ('$category') AND `deleted`=0 AND `status`=1"));
	echo '<option value=""></option>';
	foreach($query as $row) {
		echo "<option value='".$row['contactid']."' ".(strpos($selected_contacts, ','.$row['contactid'].',') !== FALSE ? 'selected' : '').">".$row['full_name'].'</option>';
	}
}
if($_GET['fill'] == 'update_match_status') {
	$matchid = $_GET['matchid'];
	$status = $_GET['status'];
	mysqli_query($dbc, "UPDATE `match_contact` SET `status` = '$status' WHERE `matchid` = '$matchid'");
}
if($_GET['fill'] == 'delete_match') {
	$matchid = $_GET['matchid'];
	mysqli_query($dbc, "UPDATE `match_contact` SET `deleted` = 1 WHERE `matchid` = '$matchid'");
}
?>