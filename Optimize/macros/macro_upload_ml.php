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
        $values = [ 'ORDER' => trim($csv[0]), 'DATE' => trim($csv[1]), 'QTY' => trim($csv[2]), 'SKU' => trim($csv[3]), 'MODEL' => trim($csv[4]), 'APPLIANCE' => trim($csv[5]),
            'APP_PART' => trim($csv[6]), 'WEIGHT' => trim($csv[7]), 'VOLUME' => trim($csv[8]), 'CUST_NO' => trim($csv[9]), 'NAME' => trim($csv[10]), 'STREET' => trim($csv[11]),
            'UNIT' => trim($csv[12]), 'CITY' => trim($csv[13]), 'PROV' => trim($csv[14]), 'POSTAL' => trim($csv[15]), 'PHONE1' => trim($csv[16]), 'PHONE2' => trim($csv[17]),
            'PHONE3' => trim($csv[18]), 'EMAIL' => trim($csv[19]), 'DAYNOTE' => trim($csv[20]), 'STATUS' => trim($csv[21]) ];
		$new_values[$values['ORDER']]['invoice_number'] = $values['ORDER'];
		$new_values[$values['ORDER']]['date'] = date('Y-m-d',strtotime($values['DATE']));
		$new_values[$values['ORDER']]['description'] = ($new_values[$values['ORDER']]['description'] == '' ? implode(' ',array_filter([$values['QTY'],$values['APPLIANCE'],$values['APP_PART']])) : implode(', ',array_merge(explode(', ',$new_values[$values['ORDER']]['description']),[implode(' ',array_filter([$values['QTY'],$values['APPLIANCE'],$values['APP_PART'],$values['WEIGHT']]))])));
		$new_values[$values['ORDER']]['volume'] = ($new_values[$values['ORDER']]['volume'] == '' ? $values['VOLUME'] : implode(', ',array_merge(explode(', ',$new_values[$values['ORDER']]['volume']),[$values['VOLUME']])));
		$new_values[$values['ORDER']]['customer_name'] = $values['NAME'];
		$new_values[$values['ORDER']]['street_address'] = $values['STREET'];
		$new_values[$values['ORDER']]['unit_number'] = $values['UNIT'];
		$new_values[$values['ORDER']]['city'] = $values['CITY'];
		$new_values[$values['ORDER']]['province'] = $values['PROV'];
		$new_values[$values['ORDER']]['postal_code'] = $values['POSTAL'];
		$new_values[$values['ORDER']]['phone1'] = $values['PHONE1'];
		$new_values[$values['ORDER']]['phone2'] = $values['PHONE2'];
		$new_values[$values['ORDER']]['phone3'] = $values['PHONE3'];
		$new_values[$values['ORDER']]['email'] = $values['EMAIL'];
		$new_values[$values['ORDER']]['info'] = $values['DAYNOTE'];
		$new_values[$values['ORDER']]['status'] = $values['STATUS'];
	}
	fclose($handle);

	if (!file_exists('output_ml')) {
		mkdir('output_ml', 0777, true);
	}
	$today_date = date('Y_m_d');
	$FileName = "output_ml/".file_safe_str("macro_miele_".$today_date.".csv",'output_ml/');
	$file = fopen($FileName, "w");
	$new_csv = ['Invoice Number', 'Date', 'Description', 'Gross Volume', 'Customer Name', 'Street Address', 'Unit Number', 'City', 'Province', 'Postal Code','Phone 1','Phone 2','Phone 3','Email','00022','Shipment Status'];
	fputcsv($file, $new_csv);
	foreach ($new_values as $key => $value) {
		$new_csv = [$value['invoice_number'],$value['date'],$value['description'],$value['volume'],$value['customer_name'],$value['street_address'],$value['unit_number'],$value['city'],$value['province'],$value['postal_code'],$value['phone1'],$value['phone2'],$value['phone3'],$value['email'],$value['info'],$value['status']];
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
