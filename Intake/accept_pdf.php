<?php
	/*
	 * This file accepts the PDF Intake Form submissions from websites and
	 * insert the data into `intake` table
	 */

	include('../database_connection.php');

	/* Headers for mail */
	$headers  = "MIME-Version: 1.0\r\n";
	$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

	$uploaddir	= realpath('./download') . '/';
	$uploadfile	= $uploaddir . basename($_FILES['file_contents']['name']);

	if ( 0 < $_FILES['file_contents']['error'] ) {
		/*
		$file_error  = 'Page: ' . $_SERVER['SERVER_NAME'] . '/Intake/accept_pdf.php' . '<br />';
		$file_error .= 'PDF Submitted From: ' . '<br>';
		$file_error .= 'Error: ' . $_FILES['file_contents']['error'] . '<br />';
		mail('jaylahiru@freshfocusmedia.com', 'Intake Tile - PDF Transfer Error', $file_error, $headers);
		echo $file_error;
		*/
	} else {
		if (move_uploaded_file($_FILES['file_contents']['tmp_name'], $uploadfile)) {
			//echo "File is valid, and was successfully uploaded.\n";
			$category		= $_POST['category'];
			$name			= $_POST['name'];
			$email			= $_POST['email'];
			$phone			= $_POST['phone'];
			$intake_file	= 'download/' . htmlspecialchars(basename($_FILES['file_contents']['name']), ENT_QUOTES);
			$received_date	= date('Y-m-d');

			$insert_query = "INSERT INTO `intake` (`category`, `name`, `email`, `phone`, `intake_file`, `received_date`) VALUES ('{$category}', '{$name}', '{$email}', '{$phone}', '{$intake_file}', '{$received_date}')";
			$results = mysqli_query($dbc, $insert_query);
      $before_change = "";
      $history = "Intake entry has been added. <br />";
	    add_update_history($dbc, 'intake_history', $history, '', $before_change);
			//echo ( mysqli_error($dbc) ) ? mysqli_error($dbc) : 'Inserted!';
		} else {
			//echo "Possible file upload attack!\n";
		}
	}

	/*
	echo '<br /><br />Here is some more debugging info:<br />';
	print_r($_FILES);
	echo "\n<hr />\n";
	print_r($_POST);
	*/
?>
