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
	$new_values = [];
	$headers = [];
    while(count(array_filter($headers)) < 10) {
        $headers = fgetcsv($handle, 2048, ",");
    }

	while (($csv = fgetcsv($handle, 2048, ",")) !== FALSE) {
		$num = count($csv);
		$values = [];
		for($i = 0; $i < $num; $i++) {
			$values[$headers[$i]] = trim($csv[$i]);
		}
		$new_values[$values['Order-No.']]['invoice_number'] = $values['Order-No.'];
		$new_values[$values['Order-No.']]['date'] = date('Y-m-d',strtotime($values['Delivery Date']));
		$new_values[$values['Order-No.']]['description'] = ($new_values[$values['Order-No.']]['description'] == '' ? implode(' ',array_filter([$values['Qty'],$values['Appliance'],$values['Appliance /Part']])) : implode(', ',array_merge(explode(', ',$new_values[$values['Order-No.']]['description']),[implode(' ',array_filter([$values['Qty'],$values['Appliance'],$values['Appliance /Part']]))])));
		$new_values[$values['Order-No.']]['weight'] = ($new_values[$values['Order-No.']]['weight'] == '' ? $values['Gross Weight (KG)'] : implode(', ',array_merge(explode(', ',$new_values[$values['Order-No.']]['weight']),[$values['Gross Weight (KG)']])));
		$new_values[$values['Order-No.']]['volume'] = ($new_values[$values['Order-No.']]['volume'] == '' ? $values['Gross Volume (LTR)'] : implode(', ',array_merge(explode(', ',$new_values[$values['Order-No.']]['volume']),[$values['Gross Volume (LTR)']])));
		$new_values[$values['Order-No.']]['customer_name'] = $values['Name'];
		$new_values[$values['Order-No.']]['street_address'] = $values['Street'];
		$new_values[$values['Order-No.']]['unit_number'] = $values['Additional Address Info'];
		$new_values[$values['Order-No.']]['city'] = $values['City'];
		$new_values[$values['Order-No.']]['province'] = $values['Prov'];
		$new_values[$values['Order-No.']]['postal_code'] = $values['Postalcode'];
		$new_values[$values['Order-No.']]['phone1'] = $values['Phone 1'];
		$new_values[$values['Order-No.']]['phone2'] = $values['Phone 2'];
		$new_values[$values['Order-No.']]['phone3'] = $values['Phone 3'];
		$new_values[$values['Order-No.']]['email'] = $values['eMail'];
		$new_values[$values['Order-No.']]['info'] = $values['00022'];
		$new_values[$values['Order-No.']]['status'] = $values['Shipment Status'];
	}
	fclose($handle);

	if (!file_exists('output_ml')) {
		mkdir('output_ml', 0777, true);
	}
	$today_date = date('Y_m_d');
	$FileName = "output_ml/".file_safe_str("macro_miele_".$today_date.".csv",'output_ml/');
	$file = fopen($FileName, "w");
	$new_csv = ['Invoice Number', 'Date', 'Description', 'Gross Weight', 'Gross Volume', 'Customer Name', 'Street Address', 'Unit Number', 'City', 'Province', 'Postal Code','Phone 1','Phone 2','Phone 3','Email','00022','Shipment Status'];
	fputcsv($file, $new_csv);
	foreach ($new_values as $key => $value) {
		$new_csv = [$value['invoice_number'],$value['date'],$value['description'],$value['weight'],$value['volume'],$value['customer_name'],$value['street_address'],$value['unit_number'],$value['city'],$value['province'],$value['postal_code'],$value['phone1'],$value['phone2'],$value['phone3'],$value['email'],$value['info'],$value['status']];
		fputcsv($file, $new_csv);
	}
	fclose($file);
	$dbc->query("INSERT INTO `ticket_history` (`ticketid`,`userid`,`src`,`description`) VALUES (0,".$_SESSION['contactid'].",'optimizer','Macro to reformat Spreadsheet for Miele created $FileName')");
	header("Location: $FileName");
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename='.str_replace('output_ml/','',$FileName));
	header('Pragma: no-cache');
}
?>

<h1>Miele Macro</h1>

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