<?php
include ('../database_connection.php');

if($_GET['fill'] == 'heading_number') {
    $category = $_POST['category'];
    $type = $_POST['type'];
	$max_section = $_POST['max_section'];
	$manualid = $_POST['manualid'];
	$heading_number = '';
	if($manual = mysqli_fetch_array(mysqli_query($dbc, "SELECT `heading_number` FROM `manuals` WHERE `manualtypeid`='$manualid' AND `category`='$category' AND `manual_type`='$type'"))) {
		$heading_number = $manual['heading_number'];
	}

	$heading_numbers = [];
	$headings = [];
	$heading_result = mysqli_query($dbc, "SELECT DISTINCT `heading_number`, `heading` FROM `manuals` WHERE `manual_type`='$type' AND `category`='$category' AND `deleted`=0");
	while($heading_used = mysqli_fetch_array($heading_result)) {
		$heading_numbers[] = $heading_used['heading_number'];
		$headings[] = $heading_used['heading'];
	}
	echo "<option></option>";
	for($j=1;$j<=$max_section;$j++) {
		echo "<option ".("$j" === $heading_number ? 'selected' : '')." value='". $j."'>".$j.(in_array("$j",$heading_numbers,true) ? ' : '.$headings[array_search($j,$heading_numbers)] : '').'</option>';
	}
}

if($_GET['fill'] == 'section') {
    $heading_number = $_GET['heading_number'];
    $category = $_GET['category'];
    $type = $_GET['type'];

	$query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT heading FROM manuals WHERE heading_number = '$heading_number' AND category = '$category' AND manual_type = '$type'"));
    echo $query['heading'];
}

if($_GET['fill'] == 'sub_heading_number') {
    $heading_number = $_POST['heading_number'];
    $category = $_POST['category'];
    $type = $_POST['type'];
    $sub_heading = $_POST['sub_heading'];
	$max_subsection = $_POST['max_section'];

	$sub_heading_numbers = [];
	$sub_headings = [];
	$disabled = [];
	$sub_heading_result = mysqli_query($dbc, "SELECT DISTINCT `sub_heading_number`, `sub_heading`, IFNULL(`third_heading_number`,'') third_heading_number FROM `manuals` WHERE `manual_type`='$type' AND `category`='$category' AND `heading_number`='$heading_number' AND `deleted`=0");
	while($sub_heading_used = mysqli_fetch_array($sub_heading_result)) {
		$sub_heading_numbers[] = $sub_heading_used['sub_heading_number'];
		$sub_headings[] = $sub_heading_used['sub_heading'];
		if($sub_heading_used['third_heading_number'] == '') {
			$disabled[] = "'".$sub_heading_used['sub_heading_number']."'";
		}
	}
	echo "<option></option>";
	for($j=1;$j<=$max_subsection;$j++) {
		echo "<option ".($sub_heading === "$heading_number.$j" ? 'selected' : (in_array("'$heading_number.$j'",$disabled,true) ? 'disabled' : ''));
		echo " value='". $heading_number.'.'.$j."'>".$heading_number.'.'.$j.(in_array("'$heading_number.$j'",$disabled,true) ? ' : '.$sub_headings[array_search("'$heading_number.$j'",$disabled)] : '').'</option>';
	}
}

if($_GET['fill'] == 'subsection') {
    $sub_heading_number = $_GET['sub_heading_number'];
    $category = $_GET['category'];
    $type = $_GET['type'];

	$query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT sub_heading FROM manuals WHERE sub_heading_number = '$sub_heading_number' AND category = '$category' AND manual_type = '$type'"));
    echo $query['sub_heading'];
}

if($_GET['fill'] == 'third_heading_number') {
    $heading_number = $_POST['sub_heading_number'];
    $category = $_POST['category'];
    $type = $_POST['type'];
    $sub_heading = $_POST['third_heading'];
	$max_subsection = $_POST['max_section'];

	$sub_heading_numbers = [];
	$sub_headings = [];
	$sub_heading_result = mysqli_query($dbc, "SELECT DISTINCT `third_heading_number`, `third_heading` FROM `manuals` WHERE `manual_type`='$type' AND `category`='$category' AND `sub_heading_number`='$heading_number' AND `deleted`=0");
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
?>