<?php // CDS Best Buy Format Macro
include_once ('include.php');
error_reporting(0);

if(isset($_POST['upload_file']) && !empty($_FILES['csv_file']['tmp_name'])) {
	$file_name = $_FILES['csv_file']['tmp_name'];
	$delimiter = $_POST['delimiter'];
	if($delimiter == 'comma') {
		$delimiter = ", ";
	} else {
		$delimiter = "\n";
	}
	$handle = fopen($file_name, 'r');
	$headers = fgetcsv($handle, 2048, ",");

	$new_values = [];

	while (($csv = fgetcsv($handle, 2048, ",")) !== FALSE) {
		$num = count($csv);
		$values = [];
		for($i = 0; $i < $num; $i++) {
			$values[$headers[$i]] = trim($csv[$i]);
		}
		$new_values[$values['Invoice_Number']]['date'] = $values['Origin_Date'];
		$new_values[$values['Invoice_Number']]['address'] = $values['Destination_Street'];
		$new_values[$values['Invoice_Number']]['city'] = $values['Destination_City'];
		$new_values[$values['Invoice_Number']]['customer_name'] = $values['Destination_Customer_First_Name'].' '.$values['Destination_Customer_Last_Name'];
		$new_values[$values['Invoice_Number']]['phone'] = $values['Destination_Phone_Number'];
		if(!empty($values['Destination_Second_Phone_Number'])) {
			$new_values[$values['Invoice_Number']]['phone'] .= ', '.$values['Destination_Second_Phone_Number'];
		}
		$new_values[$values['Invoice_Number']]['sku'] .= $delimiter.$values['SKU_Description'];
		$new_values[$values['Invoice_Number']]['comments'] = $values['Stop_Instruction'];
	}
	fclose($handle);

	if (!file_exists('cds_exports')) {
		mkdir('cds_exports', 0777, true);
	}
	$today_date = date('Y_m_d');
	$FileName = "cds_exports/".file_safe_str("cds_macro_".$today_date.".csv",'cds_exports/');
	$file = fopen($FileName, "w");
	$new_csv = ['', '', 'Client', 'Date', 'Invoice Number', 'Best Buy', 'Warehouse', 'Address', 'City/Town', 'Customer Name', 'Phone Number', 'SKU Information', 'Comments'];
	fputcsv($file, $new_csv);
	foreach ($new_values as $key => $value) {
		$new_csv = ['', '', 'Best Buy', $value['date'], $key, 'Best Buy', 'Warehouse', $value['address'], $value['city'], $value['customer_name'], $value['phone'], trim($value['sku'], $delimiter), $value['comments']];
		fputcsv($file, $new_csv);
	}
	fclose($file);
	$dbc->query("INSERT INTO `ticket_history` (`ticketid`,`userid`,`src`,`description`) VALUES (0,".$_SESSION['contactid'].",'optimizer','Macro to reformat Spreadsheet for Best Buy created $FileName')");
	header("Location: $FileName");
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename='.str_replace('cds_exports/','',$FileName));
	header('Pragma: no-cache');
}
?>

<h1>Best Buy Macro</h1>

<form class="form-horizontal" method="post" action="" enctype="multipart/form-data">
	<ol>
		<li>Upload your CSV file using the File Uploader.</li>
		<li>Specify whether to combine the SKU Descriptions using a comma or separated by new line. (NOTE: New line sometimes doesn't display properly depending on the CSV viewer you are using. If this doesn't work, use the comma separator).</li>
		<li>Press the Submit button to run the macro and generate the new CSV file.</li>
		<br>
		<p>
			New Line: <input type="radio" name="delimiter" value="newline" checked>
			Comma: <input type="radio" name="delimiter" value="comma"><br>
			<input type="file" name="csv_file">
			<input type="submit" name="upload_file" value="Submit" class="btn brand-btn">
		</p>
	</ol>
</form>