<?php include_once('../include.php');
include_once('../tcpdf/tcpdf.php');

if($_GET['import_csv'] == 1 && !empty($_FILES['import_csv_file']['tmp_name'])) {
	$file_name = $_FILES['import_csv_file']['tmp_name'];
	$handle = fopen($file_name, "r");
	$headers = fgetcsv($handle, 2048, ",");
	$error = false;
	$errors = '';

	while (($csv = fgetcsv($handle, 2048, ",")) !== FALSE) {
		$num = count($csv);
		$values = [];
		for($i = 0; $i < $num; $i++) {
			$values[$headers[$i]] = htmlentities(trim($csv[$i]));
		}
		$fields = [];
		foreach($values as $key => $value) {
			if($key != 'Staff Name' && $key != 'time_cards_id') {
				$fields[] = "`$key`='$value'";
			}
		}
		$time_cards_id = $values['time_cards_id'];
		if(!($time_cards_id > 0)) {
			mysqli_query($dbc, "INSERT INTO `time_cards` VALUES ()");
			$time_cards_id = mysqli_insert_id($dbc);
		}
		$sql = "UPDATE `time_cards` SET ".implode(', ', $fields)." WHERE `time_cards_id` = '$time_cards_id'";
		if(!mysqli_query($dbc,$sql)) {
			$errors .= "Error updating Time Card ID ".$time_cards_id."\n";
			$error = true;
		}
	}

	fclose($handle);
	echo '<script type="text/javascript"> alert("'.($error ? $errors : 'Successfully imported CSV file.').'"); window.location.href= "'.urldecode($_GET['back_url']).'" </script>';
} else if($_GET['export_csv'] == 1) {
	ob_clean();

	$search_site = '';
	$search_staff_list = '';
	$search_start_date = '';
	$search_end_date = '';
	$position = '';
	$approv = '';
	$approv_type = '';

	if(!empty($_GET['search_site'])) {
	    $search_site = $_GET['search_site'];
	}
	if(!empty($_GET['search_staff'])) {
	    $search_staff_list = $_GET['search_staff'];
	}
	if(!empty($_GET['search_start_date'])) {
		$search_start_date = $_GET['search_start_date'];
	}
	if(!empty($_GET['search_end_date'])) {
		$search_end_date = $_GET['search_end_date'];
	}
	if(!empty($_GET['approv'])) {
		$approv = $_GET['approv'];
	}
	if(!empty($_GET['approv_type'])) {
		$approv_type = $_GET['approv_type'];
	}

	$current_period = isset($_GET['pay_period']) ? $_GET['pay_period'] : -1;
	$_GET['pay_period'] = $current_period;
	include('pay_period_dates.php');

	$query = mysqli_query($dbc,"SELECT `supervisor`, `position`, `staff_list`, `security_level_list` FROM `field_config_supervisor` WHERE `supervisor`='".$_SESSION['contactid']."' OR (SELECT CONCAT(',',`staff_list`,',') FROM `field_config_supervisor` WHERE `supervisor`='".$_SESSION['contactid']."' AND `position` = '".$approv_type."') LIKE CONCAT('%,',`supervisor`,',%')");
	$staff_members = [];
	if(mysqli_num_rows($query) > 0) {
		while($row1 = mysqli_fetch_array($query)) {
			if($row1['supervisor'] == $_SESSION['contactid']) {
				$position = $row1['position'];
			}
			$staff_members = array_unique(array_merge($staff_members, array_filter(explode(',',$row1['staff_list']))));
			$security_levels = array_filter(explode(',',$row1['security_level_list']));
			if(!empty($security_levels)) {
				foreach($security_levels as $security_level) {
					if(!empty($security_level)) {
						$staff_with_security = array_column(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted` = 0 AND `status` > 0 AND `contactid` IN (SELECT `staff` FROM `time_cards`) AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND CONCAT(',',`role`,',') LIKE '%,".$security_level.",%'")),'contactid');
						$staff_members = array_unique(array_merge($staff_members, array_filter($staff_with_security)));
					}
				}
			}
		}
		$staff_members_ids = $staff_members;
		$staff_members = [];
		foreach($staff_members_ids as $staff_members_id) {
			$staff_members[] = ['contactid' => $staff_members_id, 'full_name' => get_contact($dbc,$staff_members_id)];
		}
	} else {
		$staff_members = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted` = 0 AND `status` > 0 AND `contactid` IN (SELECT `staff` FROM `time_cards`) AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.$security_query));
	}
	foreach($staff_members as $staff_id) {
		if(in_array('ALL_STAFF',$search_staff_list)) {
			$search_staff_list[] = $staff_id['contactid'];
		}
	}

	$columns = mysqli_fetch_all(mysqli_query($dbc, "SHOW COLUMNS from `time_cards`"),MYSQLI_ASSOC);
	$filename = 'download/timesheet_'.date('Y-m-d_H-i-s').'.csv';
	$file = fopen($filename,"w");

	$line = ['Staff Name'];
	foreach($columns as $column) {
		$line[] = $column['Field'];
	}
	fputcsv($file, $line);

	foreach(array_filter(array_unique($search_staff_list), function($id) { return $id != 'ALL_STAFF'; }) as $search_staff) {
		$staff_name = get_contact($dbc, $search_staff);
		$filter = '';
		if($search_site != '') {
			$filter .= " AND IFNULL(`business`,'') LIKE '%$search_site%'";
		}
		if($approv != '') {
			$filter .= " AND `approv` = '".$approv."'";
		}

		$sql = mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `staff`='$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date' AND `deleted`=0 $filter ORDER BY `date`, `start_time`, `end_time` ASC");
		while($row = mysqli_fetch_assoc($sql)) {
			$line = [$staff_name];
			foreach($columns as $column) {
				$line[] = $row[$column['Field']];
			}
			fputcsv($file, $line);
		}
	}

	fclose($file);
	header("Location: $filename");
	exit;
} else if($_GET['export_pdf'] == 1) {

    if(!file_exists('Download')) {
        mkdir('Download',0777,true);
    }

	$search_site = '';
	$search_staff_list = '';
	$search_start_date = '';
	$search_end_date = '';
	$position = '';
	$approv = '';
	$approv_type = '';

	if(!empty($_GET['search_site'])) {
	    $search_site = $_GET['search_site'];
	}
	if(!empty($_GET['search_staff'])) {
	    $search_staff_list = $_GET['search_staff'];
	}
	if(!empty($_GET['search_start_date'])) {
		$search_start_date = $_GET['search_start_date'];
	}
	if(!empty($_GET['search_end_date'])) {
		$search_end_date = $_GET['search_end_date'];
	}
	if(!empty($_GET['approv'])) {
		$approv = $_GET['approv'];
	}
	if(!empty($_GET['approv_type'])) {
		$approv_type = $_GET['approv_type'];
	}

	$current_period = isset($_GET['pay_period']) ? $_GET['pay_period'] : -1;
	$_GET['pay_period'] = $current_period;
	include('pay_period_dates.php');

	$query = mysqli_query($dbc,"SELECT `supervisor`, `position`, `staff_list`, `security_level_list` FROM `field_config_supervisor` WHERE `supervisor`='".$_SESSION['contactid']."' OR (SELECT CONCAT(',',`staff_list`,',') FROM `field_config_supervisor` WHERE `supervisor`='".$_SESSION['contactid']."' AND `position` = '".$approv_type."') LIKE CONCAT('%,',`supervisor`,',%')");
	$staff_members = [];
	if(mysqli_num_rows($query) > 0) {
		while($row1 = mysqli_fetch_array($query)) {
			if($row1['supervisor'] == $_SESSION['contactid']) {
				$position = $row1['position'];
			}
			$staff_members = array_unique(array_merge($staff_members, array_filter(explode(',',$row1['staff_list']))));
			$security_levels = array_filter(explode(',',$row1['security_level_list']));
			if(!empty($security_levels)) {
				foreach($security_levels as $security_level) {
					if(!empty($security_level)) {
						$staff_with_security = array_column(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted` = 0 AND `status` > 0 AND `contactid` IN (SELECT `staff` FROM `time_cards`) AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND CONCAT(',',`role`,',') LIKE '%,".$security_level.",%'")),'contactid');
						$staff_members = array_unique(array_merge($staff_members, array_filter($staff_with_security)));
					}
				}
			}
		}
		$staff_members_ids = $staff_members;
		$staff_members = [];
		foreach($staff_members_ids as $staff_members_id) {
			$staff_members[] = ['contactid' => $staff_members_id, 'full_name' => get_contact($dbc,$staff_members_id)];
		}
	} else {
		$staff_members = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted` = 0 AND `status` > 0 AND `contactid` IN (SELECT `staff` FROM `time_cards`) AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.$security_query));
	}
	foreach($staff_members as $staff_id) {
		if(in_array('ALL_STAFF',$search_staff_list)) {
			$search_staff_list[] = $staff_id['contactid'];
		}
	}

    $report_data =  '<table border="1px" class="table table-bordered" style="padding:3px; border:1px solid black;">';

    $report_data .=  '<tr>';
	$report_data .=  '<th width="15%">Staff Name</th>';
	$report_data .=  '<th width="10%">Date</th>';
	$report_data .=  '<th width="7%">Start Time</th>';
	$report_data .=  '<th width="8%">End Time</th>';
	$report_data .=  '<th width="10%">Total Hours</th>';
	$report_data .=  '<th width="35%">Comment</th>';
	$report_data .=  '<th width="8%">Approve</th>';
    $report_data .=  '</tr>';

	foreach(array_filter(array_unique($search_staff_list), function($id) { return $id != 'ALL_STAFF'; }) as $search_staff) {
		$staff_name = get_contact($dbc, $search_staff);
		$filter = '';
		if($search_site != '') {
			$filter .= " AND IFNULL(`business`,'') LIKE '%$search_site%'";
		}
		if($approv != '') {
			$filter .= " AND `approv` = '".$approv."'";
		}

		$sql = mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `staff`='$search_staff' AND `date` >= '$search_start_date' AND `date` <= '$search_end_date' AND `deleted`=0 $filter ORDER BY `date`, `start_time`, `end_time` ASC");

        $report_data .=  '<tr>';
		while($row = mysqli_fetch_assoc($sql)) {
            $report_data .=  '<td>'.$staff_name.'</td>';
			$report_data .=  '<td>'.$row['date'].'</td>';
			$report_data .=  '<td>'.$row['start_time'].'</td>';
			$report_data .=  '<td>'.$row['end_time'].'</td>';
			$report_data .=  '<td>'.number_format($row['total_hrs'],1).'</td>';
			$report_data .=  '<td>'.$row['comment_box'].'</td>';
			$report_data .=  '<td>'.$row['approv'].'</td>';
		}
        $report_data .=  '</tr>';
	}

    $report_data .=  '</table>';


	class MYPDF extends TCPDF {

		public function Header() {

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'Manager Approvals Timesheet Summary';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-15);
            $this->SetFont('helvetica', 'I', 9);
			$footer_text = '<span style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().' printed on '.date('Y-m-d H:i:s').'</span>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
    	}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html = $report_data;

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/timesheet_manager_approval_summary_'.$today_date.'.pdf', 'F');

    $redirect_url = str_replace("time_cards_csv.php","time_card_approvals_manager.php",$_SERVER['REQUEST_URI']);
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/timesheet_manager_approval_summary_<?php echo  $today_date;?>.pdf', 'fullscreen=yes');
    window.location.replace('<?php echo $redirect_url;?>');
	</script>
    <?php
}