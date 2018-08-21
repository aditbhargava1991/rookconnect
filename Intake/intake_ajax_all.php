<?php
	/*
	 * Intake Tile AJAX Calls
	 */

	include('../include.php');
	ob_clean();

	if ( $_GET['fill'] == 'getContactsList' ) {
		$category	= $_GET['category'];
		$action 	= $_GET['action'];
		$results	= mysqli_query ( $dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category`='{$category}' AND `deleted`='0'" );

		echo '<option value=""></option>';
		if($action == 'project' || $action == 'sales' || $action == 'ticket') {
			echo '<option value="NEW_CONTACT">Create New Contact</option>';
		}

		while ( $row = mysqli_fetch_assoc($results) ) {
			echo '<option value="' . $row['contactid'] . '">' . decryptIt($row['first_name']) . " " . decryptIt($row['last_name']) . '</option>';
		}
	} else if( $_GET['fill'] == 'generateLink' ) {
		$intakeformid = $_GET['intakeformid'];
		$today_date = date('Y-m-d h:i:s');
		$access_code = preg_replace('/[^\p{L}\p{N}\s]/u', '', encryptIt($intakeformid.'_'.$today_date));
		$before_change = capture_before_change($dbc, 'intake_forms', 'access_code', 'intakeformid', $intakeformid);

		mysqli_query($dbc, "UPDATE `intake_forms` SET `access_code` = '$access_code' WHERE `intakeformid` = '$intakeformid'");

		$history = capture_after_change('access_code', $access_code);
	  add_update_history($dbc, 'intake_history', $history, '', $before_change);
		echo '<a href="'.WEBSITE_URL.'/Intake/add_form.php?formid='.$intakeformid.'&access_code='.$access_code.'" target="_blank">'.WEBSITE_URL.'/Intake/add_form.php?formid='.$intakeformid.'&access_code='.$access_code.'</a>';
	} else if( $_GET['fill'] == 'removeLink' ) {
		$intakeformid = $_GET['intakeformid'];
		$before_change = capture_before_change($dbc, 'intake_forms', 'access_code', 'intakeformid', $intakeformid);

		mysqli_query($dbc, "UPDATE `intake_forms` SET `access_code` = '' WHERE `intakeformid` = '$intakeformid'");

		$history = capture_after_change('access_code', '');
	  add_update_history($dbc, 'intake_history', $history, '', $before_change);
	}
if($_GET['fill'] == 'intakeflagmanual') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	$label = filter_var($_POST['label'],FILTER_SANITIZE_STRING);
	$start = filter_var($_POST['start'],FILTER_SANITIZE_STRING);
	$end = filter_var($_POST['end'],FILTER_SANITIZE_STRING);
	$before_change = capture_before_change($dbc, 'intake', 'flag_colour', 'intakeid', $id);
	$before_change .= capture_before_change($dbc, 'intake', 'flag_label', 'intakeid', $id);
	$before_change .= capture_before_change($dbc, 'intake', 'flag_start', 'intakeid', $id);
	$before_change .= capture_before_change($dbc, 'intake', 'flag_end', 'intakeid', $id);

	mysqli_query($dbc, "UPDATE `intake` SET `flag_colour`='$value',`flag_label`='$label',`flag_start`='$start',`flag_end`='$end' WHERE `intakeid`='$id'");

	$history = capture_after_change('flag_colour', $value);
	$history .= capture_after_change('flag_label', $label);
	$history .= capture_after_change('flag_start', $start);
	$history .= capture_after_change('flag_end', $end);
	add_update_history($dbc, 'intake_history', $history, '', $before_change);
}
?>
