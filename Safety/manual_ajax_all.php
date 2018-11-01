<?php
include ('../include.php');

if($_GET['fill'] == 'section') {
    $heading_number = $_GET['heading_number'];
    $category = $_GET['category'];

	$query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT heading FROM safety WHERE heading_number = '$heading_number' AND category = '$category'"));
    echo $query['heading'];
}

if($_GET['fill'] == 'sub_heading_number') {
    $heading_number = $_POST['heading_number'];
    $category = $_POST['category'];
    $sub_heading = $_POST['sub_heading'];
	$max_subsection = $_POST['max_section'];

	$sub_heading_numbers = [];
	$sub_headings = [];
	$disabled = [];
	$sub_heading_result = mysqli_query($dbc, "SELECT DISTINCT `sub_heading_number`, `sub_heading`, IFNULL(`third_heading_number`,'') third_heading_number FROM `safety` WHERE `tab`='$category' AND `heading_number`='$heading_number' AND `deleted`=0");
	while($sub_heading_used = mysqli_fetch_array($sub_heading_result)) {
		$sub_heading_numbers[] = $sub_heading_used['sub_heading_number'];
		$sub_headings[] = $sub_heading_used['sub_heading'];
		if($sub_heading_used['third_heading_number'] == '') {
			$disabled[] = $sub_heading_used['sub_heading_number'];
		}
	}
	echo "<option></option>";
	for($j=1;$j<=$max_subsection;$j++) {
		echo "<option ".($sub_heading === "$heading_number.$j" ? 'selected' : (in_array("$heading_number.$j",$disabled,true) ? 'disabled' : ''));
		echo " value='". $heading_number.'.'.$j."'>".$heading_number.'.'.$j.(in_array("$heading_number.$j",$sub_heading_numbers,true) ? ' : '.$sub_headings[array_search("$heading_number.$j",$sub_heading_numbers)] : '').'</option>';
	}
}

if($_GET['fill'] == 'third_heading_number') {
    $heading_number = $_POST['sub_heading_number'];
    $category = $_POST['category'];
    $sub_heading = $_POST['third_heading'];
	$max_subsection = $_POST['max_section'];

	$sub_heading_numbers = [];
	$sub_headings = [];
	$sub_heading_result = mysqli_query($dbc, "SELECT DISTINCT `third_heading_number`, `third_heading` FROM `safety` WHERE `tab`='$category' AND `sub_heading_number`='$heading_number' AND `deleted`=0");
	while($sub_heading_used = mysqli_fetch_array($sub_heading_result)) {
		$sub_heading_numbers[] = $sub_heading_used['third_heading_number'];
		$sub_headings[] = $sub_heading_used['third_heading'];
	}
	echo "<option></option>";
	for($j=1;$j<=$max_subsection;$j++) {
		echo "<option ".($sub_heading === "$heading_number.$j" ? 'selected' : (in_array("$heading_number.$j",$sub_heading_numbers,true) ? 'disabled' : ''));
		echo " value='". $heading_number.'.'.$j."'>".$heading_number.'.'.$j.(in_array("$heading_number.$j",$sub_heading_numbers,true) ? ' : '.$sub_headings[array_search("$heading_number.$j",$sub_heading_numbers)] : '').'</option>';
	}
}

if($_GET['fill'] == 'subsection') {
    $sub_heading_number = $_GET['sub_heading_number'];
    $category = $_GET['category'];

	$query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT sub_heading FROM safety WHERE sub_heading_number = '$sub_heading_number' AND category = '$category'"));
    echo $query['sub_heading'];
}

if($_GET['fill'] == 'accordionview') {
    $contactid = $_GET['contactid'];
	$value = $_GET['value'];
	if($value !== 'on') {
		$value = NULL;
	}
    $query_update_project = "UPDATE `contacts` SET safety_manual_view='$value' WHERE `contactid` = '$contactid'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
}

if ( $_GET['fill'] == 'managerApproval' ) {
    $action     = $_GET['action'];
	$safetyid   = $_GET['safetyid'];

    if ( $action=='approve' ) {
		$query_update  = "UPDATE `safety_attendance` SET manager_approval='1' WHERE `safetyattid`='$safetyid'";
	} elseif ( $action=='reject' ) {
		$query_update  = "UPDATE `safety_attendance` SET manager_approval='2' WHERE `safetyattid`='$safetyid'";
    }

	$result_update_project = mysqli_query($dbc, $query_update);
}

$before_change = '';
$history = "safety_attendance entry has been updated for safetyattid $assign_staff_id <br />";
add_update_history($dbc, 'safety_history', $history, '', $before_change);
?>
