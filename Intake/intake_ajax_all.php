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
		mysqli_query($dbc, "UPDATE `intake_forms` SET `access_code` = '$access_code' WHERE `intakeformid` = '$intakeformid'");
		echo '<a href="'.WEBSITE_URL.'/Intake/add_form.php?formid='.$intakeformid.'&access_code='.$access_code.'" target="_blank">'.WEBSITE_URL.'/Intake/add_form.php?formid='.$intakeformid.'&access_code='.$access_code.'</a>';
	} else if( $_GET['fill'] == 'removeLink' ) {
		$intakeformid = $_GET['intakeformid'];
		mysqli_query($dbc, "UPDATE `intake_forms` SET `access_code` = '' WHERE `intakeformid` = '$intakeformid'");
	}
?>