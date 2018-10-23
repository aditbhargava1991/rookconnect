<?php
/*
Add	Job
*/
include ('../include.php');
checkAuthorised('field_job');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
$role = $_SESSION['role'];

if (isset($_POST['submit'])) {
	$jobid = $_POST['jobid'];
	$fsid = $_POST['fsid'];

	$job_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM  field_jobs  WHERE jobid = '$jobid'"));
	$rate_card = explode('*',$job_result['ratecardid']);
	$rate_type = $rate_card[0];
	$rate_id = $rate_card[1];

    $unique_wt = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(workticketid) AS wti FROM  field_work_ticket  WHERE fsid = '$fsid' AND fsid !=0"));
    $total_wt_avail = $unique_wt['wti'];

	$description = filter_var($_POST['description'],FILTER_SANITIZE_STRING);
	//$fieldpoid = implode(',',$_POST['fieldpoid']);
    $fpoid_db = '';
    foreach($_POST['fieldpoid'] as $fpoid_id_db){
        $sep_po_db = explode('_', $fpoid_id_db);
        $fpid_db = $sep_po_db[0];
        $fpoid_db .= $fpid_db.',';
    }
    $fieldpoid = rtrim($fpoid_db,',');
    $wt_date = $_POST['wt_date'];

    // Un attach PO
    if(!empty($_POST['workticketid'])) {
        $workticketid = $_POST['workticketid'];
        $attached_fpoid_db = '';
        foreach($_POST['attached_fieldpoid'] as $attached_fpoid_id_db){
            $attached_sep_po_db = explode('_', $attached_fpoid_id_db);
            $attached_fpid_db = $attached_sep_po_db[0];
            $attached_fpoid_db .= $attached_fpid_db.',';
        }
        $attached_fieldpoid = rtrim($attached_fpoid_db,',');
        $final_po = $attached_fieldpoid.','.$fieldpoid;
        $fieldpoid = trim($final_po, ',');
		$wt_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT fieldpoid FROM  field_work_ticket  WHERE workticketid = '$workticketid'"));

				$before_change = capture_before_change($dbc, 'field_work_ticket', 'fieldpoid', 'workticketid', $workticketid);

        $query_update_site = "UPDATE `field_work_ticket` SET `fieldpoid` = '$fieldpoid' WHERE `workticketid` = '$workticketid'";
        $result_update_site	= mysqli_query($dbc, $query_update_site);

				$history = capture_after_change('fieldpoid', $fieldpoid);
				add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

        if($attached_fieldpoid != $wt_result['fieldpoid']) {
            $ex_po_db = explode(',', $wt_result['fieldpoid']);
            foreach($ex_po_db as $ex_poid){
                if (strpos($attached_fieldpoid, $ex_poid) !== false) {

                } else {
										$before_change = capture_before_change($dbc, 'field_po', 'status', 'fieldpoid', $ex_poid);
										$before_change .= capture_before_change($dbc, 'field_po', 'attach_workticket', 'fieldpoid', $ex_poid);

                    $query_update_po = "UPDATE `field_po` SET `status` = 'To be Billed', `attach_workticket` = 0 WHERE `fieldpoid` = '$ex_poid'";
                    $result_update_po = mysqli_query($dbc, $query_update_po);

										$history = capture_after_change('status', 'To be Billed');
										$history .= capture_after_change('attach_workticket', 0);

										add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
                }
            }
        }
    }
    // Un attach PO

	$equ_hours = implode(',',$_POST['equ_hours']);
    $equ_billing_rate = implode(',',$_POST['equ_billing_rate']);
	$billable_reg_hour = implode(',',$_POST['billable_reg_hour']);
	$billable_ot_hour = implode(',',$_POST['billable_ot_hour']);
    $billable_travel_hour = implode(',',$_POST['billable_travel_hour']);
    $sub_pay = implode(',',$_POST['sub_pay']);
    $count_subpay_pdf = substr_count($sub_pay, '1');

    /*
    $sub = '';
    $count_pay = count($_POST['billable_ot_hour']);
    $count_subpay_pdf = 0;
    for($cpay = 0;$cpay<$count_pay; $cpay++) {
        if($_POST['sub_pay_'.$cpay] == 1) {
            $count_subpay_pdf++;
            $sub .= '1,';
        } else {
            $sub .= '0,';
        }
    }
    $sub_pay = rtrim($sub, ",");
    */

	$query = "SELECT daily FROM  position_rate_table  WHERE position_id IN(SELECT position_id FROM positions WHERE name='Subsistence Pay') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
	if($rate_type == 'company') {
		$query = "SELECT daily FROM `company_rate_card` WHERE `description`='Subsistence Pay' AND `rate_card_name` in (SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='$rate_id') AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
	}
	else if($rate_type == 'customer') {
		$query = "";
	}
	else if($rate_type == 'position') {
		$query = "SELECT daily FROM  position_rate_table  WHERE position_id IN(SELECT position_id FROM positions WHERE name='Subsistence Pay') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
	}
	else if($rate_type == 'staff') {
		$query = "";
	}
    $mp_result = mysqli_fetch_assoc(mysqli_query($dbc, $query));
    $sub_pay_rate_card = ($mp_result['daily']*$count_subpay_pdf);

	//$crew_position = implode(',',$_POST['crew_position']);

    $wt_desc = $_POST['wt_desc'];
    foreach($_POST['fieldpoid'] as $fpoid_id){
        $sep_po = explode('_', $fpoid_id);
        $fpid = $sep_po[0];
        $i = $sep_po[1];
        $wt_desc1 = filter_var($wt_desc[$i],FILTER_SANITIZE_STRING);
        $wt_cost1 = $_POST['wt_cost'][$i];
        $wt_per1 = $_POST['wt_per'][$i];
        $wt_total1 = round($wt_cost1 * (1 + ($wt_per1 / 100)), 2);

        $query_update_po = "UPDATE `field_po` SET `wt_desc` = '$wt_desc1', `wt_cost` = '$wt_cost1', `wt_per` = '$wt_per1', `wt_total` = '$wt_total1', `status` = 'Billed', `attach_workticket` = 1 WHERE `fieldpoid` = '$fpid'";
        $result_update_po = mysqli_query($dbc, $query_update_po);

				$before_change = '';
		    $history = "field_po entry has been updated for fieldpoid -> $fpid. <br />";
		    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
    }

	if((empty($_POST['workticketid'])) && $total_wt_avail == 0) {
		$query_insert_wt = "INSERT INTO `field_work_ticket` (`jobid`, `fsid`, `description`, `fieldpoid`, `billable_reg_hour`, `billable_ot_hour`, `billable_travel_hour`, `wt_date`, `sub_pay`) VALUES ('$jobid', '$fsid', '$description', '$fieldpoid', '$billable_reg_hour', '$billable_ot_hour', '$billable_travel_hour', '$wt_date', '$sub_pay')";

		$result_insert_wt	= mysqli_query($dbc, $query_insert_wt);
		$in_number = mysqli_insert_id($dbc);
		$before_change = '';
    $history = "field_work_ticket entry has been added. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

		// FOR PAYROLL
		$fs_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM  field_foreman_sheet  WHERE fsid = '$fsid'"));
		$employeeid = $fs_result['contactid'];
		$total_emp = substr_count($employeeid, ',');
		$sep_emp = explode(',', $employeeid);
		$sep_pos = explode(',', $fs_result['positionname']);
		//$created_date = date('Y-m-d');
        $created_date = $fs_result['today_date'];

		for($i=0; $i<=$total_emp; $i++) {
			if($sep_emp[$i] != '') {
				$payroll_emp = $sep_emp[$i];
				$payroll_pos = $sep_pos[$i];
				$payroll_reg = $_POST['billable_reg_hour'][$i];
				$payroll_ot = $_POST['billable_ot_hour'][$i];
                $payroll_travel = $_POST['billable_travel_hour'][$i];
                if($_POST['sub_pay_'.$i] == 1) {
                    $sub = $mp_result['daily_rate'];
                } else {
                    $sub = 0;
                }
				$query_update_payroll = "UPDATE `field_payroll` SET `workticketid`='$in_number', `reg`='$payroll_reg', `ot`='$payroll_ot', `travel`='$payroll_travel', `sub`='$sub' WHERE `fsid`='$fsid' AND `contactid`='$payroll_emp' AND `created_date`='$created_date' AND `positionid`='$payroll_pos'";
				mysqli_query($dbc, $query_update_payroll);

				$before_change = '';
		    $history = "field_payroll entry has been updated for positionid -> $payroll_pos. <br />";
		    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

				//$query_insert_payroll = "INSERT INTO `payroll` (`workticketid`, `employeeid`, `positionid`,`reg`, `ot`, `travel`, `sub`,`created_date`) VALUES ('$in_number', '$payroll_emp', '$payroll_pos', '$payroll_reg', '$payroll_ot', '$payroll_travel', '$sub', '$created_date')";
				//$result_insert_payroll	= mysqli_query($dbc, $query_insert_payroll);
			}
		}
		// fOR PAYROLL
	} else {
        if($total_wt_avail == 1) {
            $unique_wt = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT workticketid FROM  field_work_ticket  WHERE fsid = '$fsid'"));
            $workticketid = $unique_wt['workticketid'];
        } else {
		    $workticketid = $_POST['workticketid'];
        }

		$query_update_site = "UPDATE `field_work_ticket` SET `description` = '$description', `billable_reg_hour` = '$billable_reg_hour', `billable_ot_hour` = '$billable_ot_hour', `billable_travel_hour` = '$billable_travel_hour', `wt_date` = '$wt_date', `sub_pay` = '$sub_pay' WHERE `workticketid` = '$workticketid'";
		$result_update_site	= mysqli_query($dbc, $query_update_site);
		$in_number = $workticketid;

		$before_change = '';
    $history = "field_work_ticket entry has been updated for workticketid -> $workticketid. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

		$query_update_fs = "UPDATE `field_foreman_sheet` SET `equ_hours` = '$equ_hours' WHERE `fsid` = '$fsid'";
		$result_update_fs	= mysqli_query($dbc, $query_update_fs);

		$before_change = '';
    $history = "field_foreman_sheet entry has been updated for fsid -> $fsid. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

		// fOR PAYROLL
		$payroll_wtid = $workticketid;
        //$query_restore_payroll = "DELETE FROM `payroll` WHERE `workticketid` = '$payroll_wtid'";
        //$result_restore_payroll = mysqli_query($dbc, $query_restore_payroll);

		$fs_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM  field_foreman_sheet  WHERE fsid = '$fsid'"));
		$employeeid = $fs_result['contactid'];
		$total_emp = substr_count($employeeid, ',');
		$sep_emp = explode(',', $employeeid);
		$sep_pos = explode(',', $fs_result['positionname']);
		//$created_date = date('Y-m-d');
        $created_date = $fs_result['today_date'];

		for($i=0; $i<=$total_emp; $i++) {
			if($sep_emp[$i] != '') {
				$payroll_emp = $sep_emp[$i];
				$payroll_pos = $sep_pos[$i];
				$payroll_reg = $_POST['billable_reg_hour'][$i];
				$payroll_ot = $_POST['billable_ot_hour'][$i];
                $payroll_travel = $_POST['billable_travel_hour'][$i];
                if($_POST['sub_pay_'.$i] == 1) {
                    $sub = $mp_result['daily_rate'];
                } else {
                    $sub = 0;
                }
				$query_update_payroll = "UPDATE `field_payroll` SET `workticketid`='$in_number', `reg`='$payroll_reg', `ot`='$payroll_ot', `travel`='$payroll_travel', `sub`='$sub' WHERE `fsid`='$fsid' AND `contactid`='$payroll_emp'";
				mysqli_query($dbc, $query_update_payroll);

				$before_change = '';
		    $history = "field_payroll entry has been updated for contactid -> $payroll_emp. <br />";
		    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
				//$query_insert_payroll = "INSERT INTO `payroll` (`workticketid`, `employeeid`, `positionid`,`reg`, `ot`, `travel`, `sub`, `created_date`) VALUES ('$in_number', '$payroll_emp', '$payroll_pos', '$payroll_reg', '$payroll_ot', '$payroll_travel', '$sub', '$created_date')";
				//$result_insert_payroll	= mysqli_query($dbc, $query_insert_payroll);
			}
		}
		// fOR PAYROLL

        // report_revenue remove
        $result_revenue	= mysqli_query($dbc, "DELETE FROM report_revenue WHERE workticketid='$in_number'");
	}

    $ratecard_clientid = $job_result['ratecard_clientid'];
	$clientid = $job_result['clientid'];
	$client = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name, mail_street, mail_country, mail_city, mail_state, mail_zip FROM contacts WHERE contactid='$clientid'"));

    if($fsid == 0) {
        $afe = $job_result['afe_number'];
        $locationid =$job_result['siteid'];
        $additional_info = $job_result['additional_info'];
    } else {
        $fs_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM  field_foreman_sheet  WHERE fsid = '$fsid'"));
        $locationid = $fs_result['siteid'];
        $afe = $fs_result['afe_number'];
        $additional_info = $fs_result['additional_info'];
    }

    $location = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT site_name from field_sites WHERE siteid = '$locationid'"));

	$contactid = $job_result['contactid'];
	$contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name, email_address FROM contacts WHERE contactid='$contactid'"));
	$customeremail = get_email($dbc, $contactid);

    if($fs_result['contactid'] != '') {
	    $total_count = mb_substr_count($fs_result['contactid'],',');
        if($total_count == 0) {
            $total_count = 1;
        }
    }
	$no_table_labourcharges = $total_count;
	$employeeid = explode(',',$fs_result['contactid']);
	$crew_reg_hour = explode(',',$fs_result['crew_reg_hour']);
	$crew_ot_hour = explode(',',$fs_result['crew_ot_hour']);
    $crew_travel_hour = explode(',',$fs_result['crew_travel_hour']);
	$equiphours = explode(',',$fs_result['equ_hours']);
	$equiprate = explode(',',$fs_result['equ_billing_rate']);
	$equipid = explode(',',$fs_result['equipmentid']);
	$crew_position = explode(',',$fs_result['positionname']);

    $equip_count = 0;
    if($fs_result['equipmentid'] != '') {
	    $equip_count = mb_substr_count($fs_result['equipmentid'],',');
        //if($equip_count == 0) {
        //    $equip_count = 1;
        //}
    }

	$equip_count_table = $equip_count;

	// PDF
	$wt_logo = get_config($dbc, 'field_jobs_wt_logo');
	DEFINE('WT_LOGO', $wt_logo);
	DEFINE('WT_HEADER_TEXT', html_entity_decode(get_config($dbc, 'field_jobs_wt_address')));
    DEFINE('WT_DATE', $wt_date);
    DEFINE('WORK_TICKET', $in_number);

	class MYPDF extends TCPDF {

		public function Header() {
			$image_file = 'download/'.WT_LOGO;
			$this->SetFont('helvetica', '', 15);
			$footer_text = 'WORK TICKET';
			$this->writeHTMLCell(0, 0, -140, 10, $footer_text, 0, 0, false, "L", true);
			$this->SetFont('helvetica', '', 9);
			$footer_text = '<br><strong>Do not pay from this ticket<br>Date Work Performed:</strong> ' .WT_DATE.'<br>';
			$this->writeHTMLCell(0, 0, 10, 20, $footer_text, 0, 0, false, "L", true);
			if(WT_LOGO != '') {
				$this->Image($image_file, 0, 10, 60, '', 'PNG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
			}
            $this->setCellHeightRatio(0.6);
			$footer_text = '<p style="text-align:right;">'.WT_HEADER_TEXT.'<br>Work Ticket# : '.WORK_TICKET.'</p>';
			$this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica', '', 9);
			$footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();
	$pdf->SetFont('helvetica', '', 9);
	$html = '<table frame="box" style="border:1px solid black">

				<tr><td style="width:10%" rowspan="3"><strong>Sold To: </strong></td><td rowspan="3" style="width: 40%; border-right:1px solid black;">' . get_client($dbc, $clientid).'<br>'.get_address($dbc, $clientid).'</td>

                <td style="width:10%"><strong>AFE#:</strong></td><td style="width:40%">'.$afe.'</td></tr>

				<tr><td  style="width:10%"><strong>Location:</strong></td><td style="width:40%">'.$location['site_name'].'</td></tr>
				<tr><td style="width:10%"><strong>Additional Info:</strong></td><td  style="width:40%">'.$additional_info.'</td></tr>
				<tr><td style="width:10%"><strong>Contact:</strong></td><td style="border-right:1px solid black;">'. get_staff($dbc, $contactid).'</td><td style="width:10%" ><strong>Job#:</strong></td><td  style="width:40%">'.$job_result['job_number'].'</td></tr>

			</table>
			<table frame="box" style="width:100%; border:1px solid black">
				<tr><td rowspan="1" style="width:20%"><strong>Job Description:</strong></td><td  rowspan="1" style="width:80%">'.$description.'</td></tr>
				<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			</table>';


	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->SetFont('helvetica', '', 8);

	$emp = '';
	$reg_hour_rate = 0;
	$ot_hour_rate = 0;
	$crew_total = 0;

	$add_page = 0;
	// Create Cloned PDF to use for calculating line heights
	$page_height_pdf = clone($pdf);

	// Calculate the number of rows used by the description
	$rows_needed = 0;
	$page_height_pdf->AddPage();
	$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table><tr><td>DEFAULT HEIGHT</td></tr></table>", 0, 1, 1, false);
	$height = $page_height_pdf->getY();
	$page_height_pdf->deletePage($page_height_pdf->getPage());
	$page_height_pdf->AddPage();
	$page_height_pdf->SetFont('helvetica','',9);
	$page_height_pdf->writeHTMLCell('', '', 0, 0, '<table><tr><td width="20%"></td><td width="80%">'.$description."</td></tr></table>", 0, 1, 1, false);
	$current_row_height = $page_height_pdf->getY();
	$page_height_pdf->deletePage($page_height_pdf->getPage());
	$rows_needed += floor($current_row_height / $height) - 1;

	$labour_html = '';
	$labor2_html = '';
	$equip_html = '';
	$equip2_html = '';
	$material_html = '';
	$material2_html = '';
	$other_html = '';
	$other2_html = '';

	$emp_per_page = 8;
	if($rows_needed > 18) {
		$rows_needed -= 18;
		$emp_per_page -= 6;
	} else {
		$emp_per_page -= ($rows_needed / 3);
		$rows_needed = 0;
	}

	/**LOOP THROUGH EMPLOYEES FROM THE FOREMAN SHEET*/
	/** ADD INDIVIDUAL HOURS AND RATES HERE*/
	if($total_count !=0){
		for($emp_loop=0; $emp_loop<=$total_count; $emp_loop++) {

			/**CHECK FOR INDEX VS. NUM ROWS PER PAGE*/
			/**IF INDEX GREATER THAN NUM ROWS SET NEW PAGE FLAG AND CREATE SECOND HTML STRING*/
			if($emp_loop >= $emp_per_page){
				if($add_page != 1){
					$add_page = 1;
				}
				if($employeeid[$emp_loop] != '') {
					$pos = get_positions($dbc, $crew_position[$emp_loop], 'name');

					$query = "SELECT hourly FROM position_rate_table WHERE position_id = '".$crew_position[$emp_loop]."' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
					if($rate_type == 'company') {
						$query = "SELECT `hourly` FROM `company_rate_card` WHERE (`description` = '$pos' OR `description` = '{$employeeid[$emp_loop]}') AND `rate_card_name` IN
							(SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='$rate_id') AND `deleted`=0 ORDER BY (CASE WHEN `tile_name`='Staff' THEN 1 ELSE 2 END)";
					}
					else if($rate_type == 'customer') {
						$query = "";
					}
					else if($rate_type == 'position') {
						$query = "SELECT hourly FROM position_rate_table WHERE position_id = '".$crew_position[$emp_loop]."' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
					}
					else if($rate_type == 'staff') {
						$query = "SELECT `hourly` FROM `staff_rate_table` WHERE CONCAT(',',`staff_id`,',') LIKE '%,{$employeeid[$emp_loop]},%' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
					}
					$rate_card = mysqli_fetch_assoc(mysqli_query($dbc,$query));
					$reg_emp_rate = $rate_card['hourly'];
					$ot_emp_rate = ($rate_card['hourly']*1.5);

					/*
                    $query = mysqli_query($dbc,"SELECT name FROM positions WHERE deleted=0 AND position_id = '".$crew_position[$emp_loop]."'");

					while($row = mysqli_fetch_array($query)) {
						$pos = $row['name'];
						$name = $row['name'];
					}
                    */

					$reg_rate_total = ($_POST['crew_reg_hour'][$emp_loop]*$reg_emp_rate);
					$ot_rate_total = ($_POST['crew_ot_hour'][$emp_loop]*$ot_emp_rate);
                    $travel_rate_total = ($_POST['crew_travel_hour'][$emp_loop]*$reg_emp_rate);

					$labour2_html .= '<tr><td rowspan="3" style="border-right: 1px solid grey; border-top:1px solid grey;">' . get_staff($dbc, $employeeid[$emp_loop]).'</td><td style="border-right: 1px solid grey; border-top:1px solid grey;" rowspan="3">' . $pos .'</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">REG</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">' . $_POST['crew_reg_hour'][$emp_loop].'</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format((float)$reg_emp_rate, 2, '.', '').'</td><td style="border-top:1px solid grey; text-align:center;">$'.number_format((float)$reg_rate_total, 2, '.', '').'</td></tr>
					<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">O.T.</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'. $_POST['crew_ot_hour'][$emp_loop].'</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format((float)$ot_emp_rate, 2, '.', '').'</td><td style=" border-top:1px solid grey; text-align:center;">$'.number_format((float)$ot_rate_total, 2, '.', '').'</td></tr>
					<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">TRV</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'. $_POST['crew_travel_hour'][$emp_loop].'</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format((float)$reg_emp_rate, 2, '.', '').'</td><td style=" border-top:1px solid grey; text-align:center;">$'.number_format((float)$travel_rate_total, 2, '.', '').'</td></tr>';
					$crew_total += $reg_rate_total+$ot_rate_total+$travel_rate_total;
				}
			}
			else{
				if($employeeid[$emp_loop] != '') {
					$pos = get_positions($dbc, $crew_position[$emp_loop], 'name');

					$query = "SELECT hourly FROM position_rate_table WHERE position_id = '".$crew_position[$emp_loop]."' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
					if($rate_type == 'company') {
						$query = "SELECT `hourly` FROM `company_rate_card` WHERE (`description` = '$pos' OR `description` = '{$employeeid[$emp_loop]}') AND `rate_card_name` IN
							(SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='$rate_id') ORDER BY (CASE WHEN `tile_name`='Staff' THEN 1 ELSE 2 END)";
					}
					else if($rate_type == 'customer') {
						$query = "";
					}
					else if($rate_type == 'position') {
						$query = "SELECT hourly FROM position_rate_table WHERE position_id = '".$crew_position[$emp_loop]."' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
					}
					else if($rate_type == 'staff') {
						$query = "SELECT `hourly` FROM `staff_rate_table` WHERE CONCAT(',',`staff_id`,',') LIKE '%,{$employeeid[$emp_loop]},%' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
					}
					$rate_card = mysqli_fetch_assoc(mysqli_query($dbc,$query));
					$reg_emp_rate = $rate_card['hourly'];
					$ot_emp_rate = ($rate_card['hourly']*1.5);

					$reg_rate_total = ($_POST['crew_reg_hour'][$emp_loop]*$reg_emp_rate);
					$ot_rate_total = ($_POST['crew_ot_hour'][$emp_loop]*$ot_emp_rate);
                    $travel_rate_total = ($_POST['crew_travel_hour'][$emp_loop]*$reg_emp_rate);

					$labour_html .= '<tr><td rowspan="3" style="border-right: 1px solid grey; border-top:1px solid grey;">' . get_staff($dbc, $employeeid[$emp_loop]).'</td><td style="border-right: 1px solid grey; border-top:1px solid grey;" rowspan="3">' . $pos .'</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">REG</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">' . $_POST['crew_reg_hour'][$emp_loop].'</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format((float)$reg_emp_rate, 2, '.', '').'</td><td style="border-top:1px solid grey; text-align:center;">$'.number_format((float)$reg_rate_total, 2, '.', '').'</td></tr>
					<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">O.T.</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'. $_POST['crew_ot_hour'][$emp_loop].'</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format((float)$ot_emp_rate, 2, '.', '').'</td><td style=" border-top:1px solid grey; text-align:center;">$'.number_format((float)$ot_rate_total, 2, '.', '').'</td></tr>
					<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">TRV</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'. $_POST['crew_travel_hour'][$emp_loop].'</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format((float)$reg_emp_rate, 2, '.', '').'</td><td style=" border-top:1px solid grey; text-align:center;">$'.number_format((float)$travel_rate_total, 2, '.', '').'</td></tr>';
					$crew_total += $reg_rate_total+$ot_rate_total+$travel_rate_total;
				}

			}
			if(($emp_loop == $total_count) && ($emp_loop < $emp_per_page)){
				$fill_crew_rows = ($emp_per_page - 2 - $total_count);
			}
			else if(($emp_loop == $total_count) && ($emp_loop > $emp_per_page)){
				$fill_crew2_rows = ($emp_per_page + 8 - $emp_loop);
			}

		}
	}
	else{
		$fill_crew_rows = $emp_per_page;
	}

	if($fill_crew_rows > 0){
		for ($blank_rows = 0; $blank_rows < $fill_crew_rows; $blank_rows++){
			$labour_html .='<tr><td rowspan="3" style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey;" rowspan="3">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">REG</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>
				<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">O.T.</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style=" border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>
				<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">TRV</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style=" border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>';
		}
	}
	else if($fill_crew2_rows > 0){
		for ($blank_rows = 0; $blank_rows < $fill_crew2_rows; $blank_rows++){
			$labour2_html .='<tr><td rowspan="3" style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey;" rowspan="3">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">REG</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>
				<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">O.T.</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style=" border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>
				<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">TRV</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style=" border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>';
		}

	}


	/**LOOP THROUGH EQUIPMENT FROM THE FOREMAN SHEET*/
	/** ADD INDIVIDUAL HOURS AND RATES HERE*/

    $equip = '';
    $equip_total = 0;
	$equip_per_page = 8;
	$equip_per_page2 = 8;
	if($rows_needed > 5) {
		$rows_needed -= 5;
		$equip_per_page -= 5;
	} else if($rows_needed > 0) {
		$equip_per_page -= $rows_needed;
		$rows_needed = 0;
	}

    for($equip_loop=0; $equip_loop <= $equip_count; $equip_loop++) {
		$equipmentid = $equipid[$equip_loop];

		if($equipmentid !== '') {

			$equip_rate_type = $equiprate[$equip_loop];
			//$eq_rate = $equiprate[$equip_loop];
			$eq_name = get_equipment_field($dbc, $equipmentid, 'type');

			$query = "SELECT hourly, daily FROM category_rate_table WHERE category = '$eq_name' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
			if($rate_type == 'company') {
				$query = "SELECT `daily`, `hourly` FROM `company_rate_card` WHERE (`description`='$eq_name' OR `description`='$equipmentid') AND `rate_card_name` IN
					(SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='$rate_id') AND `deleted`=0";
			}
			else if($rate_type == 'customer') {
				$query = "";
			}
			else if($rate_type == 'equipment') {
				$query = "SELECT `daily`, `hourly` FROM `equipment_rate_table` WHERE `equipment_id`='$equipmentid' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
			}
			else if($rate_type == 'category') {
				$query = "SELECT hourly, daily FROM category_rate_table WHERE category = '$eq_name' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
			}
			$rate_card = mysqli_fetch_assoc(mysqli_query($dbc,$query));
			if($equiprate[$equip_loop] == 'Hourly') {
				$eq_rate = $rate_card['hourly'];
			}
			if($equiprate[$equip_loop] == 'Daily') {
				$eq_rate = $rate_card['daily'];
			}

			/**CHECK FOR INDEX VS. NUM ROWS PER PAGE*/
			/**IF INDEX GREATER THAN NUM ROWS SET NEW PAGE FLAG AND CREATE SECOND HTML STRING*/
			if($equip_loop >= $equip_per_page){
				if($add_page != 1){
					$add_page = 1;
				}

				$row_html = '<tr>
					<td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'.get_equipment_field($dbc, $equipmentid, 'unit_number') .'</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">'.get_equipment_field($dbc, $equipmentid, 'type').'</td>';
					if($equip_rate_type == 'Hourly'){
						$row_html .= '<td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'.$equiphours[$equip_loop].'</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">Hours</td>';
						$eq_amount = $eq_rate * $equiphours[$equip_loop];
					} else {
						$row_html .= '<td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">1</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">Day(s)</td>';
						$eq_amount = $eq_rate;
					}
				$row_html .= '<td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format((float)$eq_rate, 2, '.', '').'</td><td style="border-top:1px solid grey; text-align:center;">$'.number_format((float)$eq_amount, 2, '.', '').'</td></tr>';
				$equip2_html .= $row_html;

				$page_height_pdf->AddPage();
				$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table><tr><td>DEFAULT HEIGHT</td></tr></table>", 0, 1, 1, false);
				$height = $page_height_pdf->getY();
				$page_height_pdf->deletePage($page_height_pdf->getPage());
				$page_height_pdf->AddPage();
				$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table>".$row_html."</table>", 0, 1, 1, false);
				$current_row_height = $page_height_pdf->getY();
				$page_height_pdf->deletePage($page_height_pdf->getPage());
				if($current_row_height > $height) {
					$equip_per_page2 -= floor($current_row_height / $height) - 1;
				}

				$equip_total += $eq_amount;

			} else{/**ELSE ADD TO FIRST PAGE*/
				$row_html = '<tr>
					<td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'.get_equipment_field($dbc, $equipmentid, 'unit_number').'</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">'.get_equipment_field($dbc, $equipmentid, 'type').'</td>';
					if($equip_rate_type == 'Hourly'){
						$row_html .= '<td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'.$equiphours[$equip_loop].'</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">Hours</td>';
						$eq_amount = $eq_rate * $equiphours[$equip_loop];
					} else {
						$row_html .= '<td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">1</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">Day</td>';
						$eq_amount = $eq_rate;
					}
				$row_html .= '<td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format((float)$eq_rate, 2, '.', '').'</td><td style="border-top:1px solid grey; text-align:center;">$'.number_format((float)$eq_amount, 2, '.', '').'</td></tr>';
				$equip_html .= $row_html;

				$page_height_pdf->AddPage();
				$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table><tr><td>DEFAULT HEIGHT</td></tr></table>", 0, 1, 1, false);
				$height = $page_height_pdf->getY();
				$page_height_pdf->deletePage($page_height_pdf->getPage());
				$page_height_pdf->AddPage();
				$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table>".$row_html."</table>", 0, 1, 1, false);
				$current_row_height = $page_height_pdf->getY();
				$page_height_pdf->deletePage($page_height_pdf->getPage());
				if($current_row_height > $height) {
					$equip_per_page -= floor($current_row_height / $height) - 1;
				}

				$equip_total += $eq_amount;
			}
		}
		// Revenue Report
		$revenue = number_format((float)$eq_amount, 2, '.', '');
		$query_insert_payroll = "INSERT INTO `report_revenue` (`workticketid`, `equipmentid`, `revenue`, `created_date`) VALUES ('$in_number', '$equipmentid', '$revenue', '$wt_date')";
		$result_insert_payroll	= mysqli_query($dbc, $query_insert_payroll);
		// Revenue Report


		if(($equip_loop == $equip_count) && ($equip_loop < $equip_per_page)){
			$fill_equip_rows = ($equip_per_page - 2 - $equip_loop);
		}
		else if(($equip_loop == $equip_count) && ($equip_loop > $equip_per_page)){
			$fill_equip2_rows = ($equip_per_page - 2 + $equip_per_page - $equip_loop);
		}
    }

	if($fill_equip_rows > 0){
		for ($blank_rows = 0; $blank_rows <= $fill_equip_rows; $blank_rows++){
			$equip_html .='<tr>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			</tr>';
		}
	}
	else if($fill_equip2_rows > 0){
		for ($blank_rows = 0; $blank_rows <= $fill_equip2_rows; $blank_rows++){
			$equip2_html .='<tr>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			</tr>';
		}

	}

    /** LOOP THROUGH MATERIALS ADDED TO THE FOREMAN SHEET*/
	$fwt = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_work_ticket WHERE workticketid='$in_number'"));
    $fieldpoid_pdf = explode(',',$fwt['fieldpoid']);
    $total_fieldpoid = mb_substr_count($fwt['fieldpoid'], ',');

	$material_subtotal = 0;
	$other_subtotal = 0;

    $stockmat_qty = explode(',',$fs_result['stockmat_qty']);
    $stockmat_desc = explode('*#*',$fs_result['stockmat_desc']);
    $stockmat_up = explode(',',$fs_result['stockmat_up']);
    $stockmat_amount = explode(',',$fs_result['stockmat_amount']);

    $total_count = mb_substr_count($fs_result['stockmat_qty'],',');

	$fill_mat_rows = 0;
	$fill_mat2_rows = 0;
	$mat_per_page = 6;
	$mat_per_page2 = 6;
	if($rows_needed > 0) {
		$mat_per_page -= $rows_needed;
		$rows_needed = 0;
	}

    for($sm_loop=0; $sm_loop<=$total_count; $sm_loop++) {

		if($sm_loop >= $mat_per_page){
			if($add_page !=1){
				$add_page = 1;
			}
			$row_html = '<tr>';
			$row_html .='
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;">'.$stockmat_desc[$sm_loop].'</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">'.$stockmat_qty[$sm_loop].'</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">$'.number_format((float)$stockmat_up[$sm_loop], 2, '.', '').'</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">$'.number_format((float)$stockmat_amount[$sm_loop], 2, '.', '').'</td>';
			$row_html .= '</tr>';
			$material2_html .= $row_html;

			$page_height_pdf->AddPage();
			$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table><tr><td>DEFAULT HEIGHT</td></tr></table>", 0, 1, 1, false);
			$height = $page_height_pdf->getY();
			$page_height_pdf->deletePage($page_height_pdf->getPage());
			$page_height_pdf->AddPage();
			$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table>".$row_html."</table>", 0, 1, 1, false);
			$current_row_height = $page_height_pdf->getY();
			$page_height_pdf->deletePage($page_height_pdf->getPage());
			if($current_row_height > $height) {
				$mat_per_page2 -= floor($current_row_height / $height) - 1;
			}
		}
		else{
			$row_html = '<tr>';
			$row_html .='
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;">'.$stockmat_desc[$sm_loop].'</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">'.$stockmat_qty[$sm_loop].'</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">$'.number_format((float)$stockmat_up[$sm_loop], 2, '.', '').'</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">$'.number_format((float)$stockmat_amount[$sm_loop], 2, '.', '').'</td>';
			$row_html .= '</tr>';
			$material_html .= $row_html;

			$page_height_pdf->AddPage();
			$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table><tr><td>DEFAULT HEIGHT</td></tr></table>", 0, 1, 1, false);
			$height = $page_height_pdf->getY();
			$page_height_pdf->deletePage($page_height_pdf->getPage());
			$page_height_pdf->AddPage();
			$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table>".$row_html."</table>", 0, 1, 1, false);
			$current_row_height = $page_height_pdf->getY();
			$page_height_pdf->deletePage($page_height_pdf->getPage());
			if($current_row_height > $height) {
				$mat_per_page -= floor($current_row_height / $height) - 1;
			}
		}

        $material_subtotal += $stockmat_amount[$sm_loop];

		if(($sm_loop == $total_count) && ($sm_loop < $mat_per_page)){
			$fill_mat_rows = ($mat_per_page - 2 - $sm_loop);
		}
		else if(($sm_loop == $total_count) && ($sm_loop > $mat_per_page)){
			$fill_mat2_rows = ($mat_per_page2 - 2 + $mat_per_page - $sm_loop);
		}
    }

	if($fill_mat_rows > 0){
		for ($blank_rows = 0; $blank_rows <= $fill_mat_rows; $blank_rows++){
			$material_html .='<tr>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>';
		}
	}
	else if($fill_mat2_rows > 0){
		for ($blank_rows = 0; $blank_rows <= $fill_mat2_rows; $blank_rows++){
			$material2_html .='<tr>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>';
		}

	}

    /**LOOP THROUGH FIELD PO's ADDED TO THE WORK TICKET*/
    $po_image = [];

    if($count_subpay_pdf >= 1){
        $i = 1;
        $po_index = $i - 1;
    }
    else{
        $i = 0;
        $po_index = $i;
    }

    $fill_other_rows = 0;
	$fill_other2_rows = 0;

    for(; $po_index<=$total_fieldpoid; $po_index++) {
		$fieldpoid = $fieldpoid_pdf[$po_index];
		$get_po =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	field_po WHERE fieldpoid='$fieldpoid'"));
        $vendorid = $get_po['vendorid'];
		$get_vendor =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name FROM contacts WHERE contactid='$vendorid'"));

        $vendor_invoice = $get_po['vendor_invoice'];

        if($fieldpoid != '') {

            $vin = explode('##FFM##', $vendor_invoice);
            $vinc = 0;

            foreach($vin as $venin) {
                if($venin != '') {
                    $file_type = strtolower(pathinfo($venin, PATHINFO_EXTENSION));
					if($file_type == 'pdf') {
						try {
							exec('gs -sDEVICE=png16m -r600 -dDownScaleFactor=3 -o "download/field_invoice/'.$venin.'.png" "download/field_invoice/'.$venin.'"');
							$venin .= '.png';
							$file_type = 'png';
						} catch(Exception $e) { }
					}
                    if($file_type == 'jpg' || $file_type == 'jpeg' || $file_type == 'bmp' || $file_type == 'gif' || $file_type == 'png') {
                        $po_image[] = '<img src="download/field_invoice/'.$venin.'" style="-webkit-transform:rotate(270deg);"><br>';
                    }
                }
                $vinc++;
            }
			$wt_total = (float)$get_po['wt_total'];
			$other_subtotal += $wt_total;
			$other_subtotal_without_markup += $get_po['cost'];
			if($i >= 8){/**NEED TO FINISH HOW TO ADDRESS THE FIELD PO ID ISSUE WITH SUB PAY*/
				 //if(($get_po['type'] == '3rd Party') || ($get_po['type'] == 'Other')) {
				if($add_page !=1){
					$add_page = 1;
				}
				$other2_html .= '<tr>';
				$other2_html .='<td  style="border-right: 1px solid grey; border-top:1px solid grey; text-align: left;">'.decryptIt($get_vendor['name']).': INVOICE '.$get_po['third_invoice_no'].'</td>
				<td  style=" border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format($wt_total, 2, '.', '').'</td>';
				$other2_html .= '</tr>';
			//}
			}
			else{
					 //if(($get_po['type'] == '3rd Party') || ($get_po['type'] == 'Other')) {
				$other_html .= '<tr>';
				$other_html .='<td  style="border-right: 1px solid grey; border-top:1px solid grey; text-align: left;">'.decryptIt($get_vendor['name']).': INVOICE '.$get_po['third_invoice_no'].'</td>
				<td  style=" border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format($wt_total, 2, '.', '').'</td>';
				$other_html .= '</tr>';
			//}
			}
        }
		$i++;
		if(($po_index == $total_fieldpoid) && ($po_index < 6)){
			$fill_other_rows = (6 - $i);
		}
		else if(($po_index == $total_count) && ($po_index >= 6)){
			$fill_other2_rows = (10 - $i);
		}
    }

	if($fill_other_rows > 0){
		for ($blank_rows = 0; $blank_rows <= $fill_other_rows; $blank_rows++){
			$other_html .='<tr>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			</tr>';
		}
	}
	else if($fill_other2_rows > 0){
		for ($blank_rows = 0; $blank_rows <= $fill_other2_rows; $blank_rows++){
			$other2_html .='<tr>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			</tr>';
		}

	}

	/*
    for($i=0; $i<count($_POST['fieldpoid']); $i++) {
		$fieldpoid = $_POST['fieldpoid'][$i];
		$get_po =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	field_po WHERE fieldpoid='$fieldpoid'"));
        $mark_up = (($get_po['mark_up']/100)+1);
        $set_qty = $get_po['qty'].'#*#';
        $mname = explode('#*#',$get_po['desc']);
        $mqty = explode('#*#',$set_qty);
        $mup = explode('#*#',$get_po['price_per_unit']);

        $mat_count = mb_substr_count($set_qty,'#*#');
        $other_count = mb_substr_count($set_qty,'#*#');

        if(($get_po['type'] == 'Stock') || ($get_po['type'] == 'Materials')) {
            for($mat_loop=0; $mat_loop<$mat_count; $mat_loop++) {
                if($mname[$mat_loop] !== '') {
					$material_html .= '<tr>';
					$material_html .='<td  style="border-right: 1px solid grey; border-top:1px solid grey;">'.$mname[$mat_loop].'</td><td  style=" border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'.$mqty[$mat_loop].'</td><td  style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.$mup[$mat_loop].'</td><td  style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.($mqty[$mat_loop]*$mup[$mat_loop]*$mark_up).'</td>';
					$material_html .= '</tr>';
                }
            }
            $material_subtotal = $get_po['total_cost'];
        }
		else if(($get_po['type'] == '3rd Party') || ($get_po['type'] == 'Other')) {
            for($other_loop=0; $other_loop<$other_count; $other_loop++) {
                if($mname[$other_loop] !== '') {
					$other_html .= '<tr>';
					$other_html .='<td  style="border-right: 1px solid grey; border-top:1px solid grey;">'.$mname[$other_loop].'</td><td  style=" border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'.$mqty[$other_loop].'</td><td  style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.$mup[$other_loop].'</td><td  style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.($mqty[$other_loop]*$mup[$other_loop]*$mark_up).'</td>';
					$other_html .= '</tr>';
                }
            }
            $other_subtotal = $get_po['total_cost'];
		}
	}
    */
	$sub_total = ($crew_total + $equip_total + $material_subtotal + $other_subtotal+$sub_pay_rate_card);

    $sub_total_other_no_markup = ($crew_total + $equip_total + $material_subtotal + $other_subtotal_without_markup+$sub_pay_rate_card);

	$gst = ($sub_total * 0.05);
	$grand_total = $sub_total + $gst;

	$cost_summary ='
					<p>&nbsp;</p>
					<table >
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">LABOUR &nbsp;</td><td style="border:1px solid black; width:60%; font-size: 12px; text-align:right;"> &nbsp;$'.number_format((float)$crew_total, 2, '.', '').'</td></tr>
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">VEHICLE & EQUIP. &nbsp;</td><td style="border:1px solid black; width:60%; font-size: 12px; text-align:right;"> &nbsp;$'.number_format((float)$equip_total, 2, '.', '').'</td></tr>
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">MATERIAL &nbsp;</td><td style="border:1px solid black; width:60%; font-size: 12px; text-align:right;"> &nbsp;$'.number_format((float)$material_subtotal, 2, '.', '').'</td></tr>
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">OTHER ITEMS &nbsp;</td><td style="border:1px solid black; width:60%; font-size: 12px; text-align:right;"> &nbsp;$'.number_format((float)($sub_pay_rate_card+$other_subtotal), 2, '.', '').'</td></tr>
					</table>
					<p>&nbsp;</p>
					<table >
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">SUB-TOTAL &nbsp;</td><td style="border:1px solid black; padding-left:10px; width:60%; font-size: 12px; text-align:right;"> &nbsp;$'.number_format((float)$sub_total, 2, '.', '').'</td></tr>
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">GST &nbsp;</td><td style="border:1px solid black; width:60%; font-size: 12px; text-align:right;"> &nbsp;$'.number_format((float)$gst, 2, '.', '').'</td></tr>
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">TOTAL &nbsp;</td><td style="border:1px solid black; width:60%; font-size: 12px; text-align:right;"><strong> &nbsp;$'.number_format((float)$grand_total, 2, '.', '').'</strong></td></tr>
					</table>
					<p>&nbsp;</p>
					<table>
						<tr style="text-align:center; font-weight:bold;"><td style="text-align:center; font-weight:bold; border:1px solid black;">Customer Signature</td></tr>

						<tr><td style="text-align:center; font-weight:bold; border:1px solid black;"  rowspan="2">&nbsp;</td></tr>
						<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>


					</table>
					<table>
						<tr style="text-align:center; font-weight:bold;"><td style="text-align:center; font-weight:bold; border:1px solid black;">Customer Name</td></tr>

						<tr><td style="text-align:center; font-weight:bold; border:1px solid black;"  rowspan="2">&nbsp;</td></tr>
						<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>


					</table>
					<table>
						<tr style="text-align:center; font-weight:bold;"><td style="text-align:center; font-weight:bold; border:1px solid black;">Company Stamp</td></tr>

						<tr><td style="text-align:center; font-weight:bold; border:1px solid black;"  rowspan="3">&nbsp;</td></tr>
						<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
						<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>


					</table>';

	$left_column = '<table cellpadding="0">
						<tr>
							<td>
								<table style="border: 1px solid black" cellpadding="1">
									<tr><th style="border-right: 1px solid grey; text-align:center; width:22%;font-weight:bold;">Name</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:22%;">Trade</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:8%;">&nbsp;</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:16%;">Hours</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:16%;">Rate</th><th style="font-weight:bold;text-align:center; width:16%;">Amount</th></tr>';
									$left_column .= $labour_html;
									$left_column .='<tr><td colspan="5" style=" border-top:1px solid grey;font-weight:bold;">Labour Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align:center;">$'.number_format((float)$crew_total, 2, '.', '').'</td></tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table style="border: 1px solid black" cellpadding="1">
									<tr><th style="border-right: 1px solid grey; text-align:center; width:8%;font-weight:bold;">#</th><th style="border-right: 1px solid grey; text-align:center; width:36%;font-weight:bold;">Vehicle/Equipment Charges</th><th style="border-right: 1px solid grey; text-align:center; width:14%;font-weight:bold;">Qty</th><th style="border-right: 1px solid grey; text-align:center; width:10%;font-weight:bold;">Unit</th><th style="border-right: 1px solid grey; text-align:center; width:16%;font-weight:bold;">Rate</th><th style=" text-align:center; width:16%;font-weight:bold;">Amount</th></tr>';
									$left_column .= $equip_html;
									$left_column .='<tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="5">Vehicle & Equipment Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align:center;">$'.number_format((float)$equip_total, 2, '.', '').'</td></tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table style="border: 1px solid black" cellpadding="1">
									<tr><th style="border-right: 1px solid grey; text-align:center; width:40%;font-weight:bold;">Materials Charges</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Qty</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Unit Price</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Amount</th></tr>';
									$left_column .= $material_html;
									$left_column .= '
									<tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="3">Materials Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align:center;">$'.number_format((float)$material_subtotal, 2, '.', '').'</td></tr>
								</table>
							</td>
						</tr>
					</table>';
	$right_column = '<table border="1" frame="box" cellpadding="1">
						<tr><th style="border-right: 1px solid grey; text-align:center; width:80%;font-weight:bold;">Other Items</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Amount</th></tr>';
						$right_column .= $other_html;
						if($count_subpay_pdf != 0){
							$right_column .= '<tr><td  style=" border-top:1px solid grey;border-right:1px solid grey;font-weight:bold;text-align:left;">Subsistence Pay x '.$count_subpay_pdf.' Crew @  $'.$mp_result['daily_rate'].' ea.</td><td style="border-top:1px solid grey;font-weight:bold;text-align:center; width: 20%;">$'.number_format((float)$sub_pay_rate_card, 2, '.', '').'</td></tr>';
							//$other_subtotal += $sub_pay_rate_card;
						}
						$right_column .= '<tr><td  style="border-top:1px solid grey;font-weight:bold; text-align: left;">Other Items Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align: center;">$'.number_format((float)($other_subtotal+$sub_pay_rate_card), 2, '.', '').'</td></tr>
					</table>';
					if($add_page != 1){
						$right_column .= $cost_summary;

					}

    // writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

    // get current vertical position
    $y = $pdf->getY();

    // set color for background
    $pdf->SetFillColor(255, 255, 255);

    // set color for text
    $pdf->SetTextColor(0, 0, 0);

    // write the first column
    $pdf->writeHTMLCell(100, '', '', $y, $left_column, 0, 0, 1, true, 'J', true);

    // set color for background
    $pdf->SetFillColor(255, 255, 255);

    // set color for text
    $pdf->SetTextColor(0, 0, 0);

    // write the second column
    $pdf->writeHTMLCell(80, '', '', '', $right_column, 0, 1, 1, true, 'J', true);



	//$pdf->writeHTML($html, true, false, true, false, '');

	if($add_page == 1){
		$pdf->AddPage();
		/*$pdf->SetFont('helvetica', '', 9);
		$html = '<table frame="box" style="border:1px solid black">

					<tr><td style="width:10%" rowspan="3"><strong>Sold To: </strong></td><td rowspan="3" style="width: 40%; border-right:1px solid black;">' . $client['name'].'<br>'.$client['mail_street'].'<br>'.$client['mail_city'].', '.$client['mail_state']. '<br>'.$client['mail_country'].', '.$client['mail_zip'].'</td>

					<td style="width:10%"><strong>AFE#:</strong></td><td style="width:40%">'.$afe.'</td></tr>

					<tr><td  style="width:10%"><strong>Location:</strong></td><td style="width:40%">'.$location['site_name'].'</td></tr>
					<tr><td style="width:10%"><strong>Additional Info:</strong></td><td  style="width:40%">'.$additional_info.'</td></tr>
					<tr><td style="width:10%"><strong>Contact:</strong></td><td style="border-right:1px solid black;">'. $contact['first_name'].' '.$contact['last_name'].'</td><td style="width:10%" ><strong>Job#:</strong></td><td  style="width:40%">'.$job_result['job_number'].'</td></tr>

				</table>
				<table frame="box" style="width:100%; border:1px solid black">
					<tr><td rowspan="1" style="width:20%"><strong>Job Description:</strong></td><td  rowspan="1" style="width:80%">'.$description.'</td></tr>
					<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
				</table>';

		$pdf->writeHTML($html, true, false, true, false, '');*/
		/**START WRITING SECOND PAGE HERE*/
		$pdf->SetFont('helvetica', '', 8);

		$left_column = '<table cellpadding="0">
							<tr>
								<td>
									<table style="border: 1px solid black" cellpadding="1">
										<tr><th style="border-right: 1px solid grey; text-align:center; width:22%;font-weight:bold;">Name</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:22%;">Trade</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:8%;">&nbsp;</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:16%;">Hours</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:16%;">Rate</th><th style="font-weight:bold;text-align:center; width:16%;">Amount</th></tr>';
										if($labour2_html != ''){
											$left_column .= $labour2_html;
										}
										else{
											for($i = 0; $i <= 10; $i++ ){
												$left_column .= '<tr><td rowspan="2" style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey;" rowspan="2">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">REG</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>
															<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">O.T.</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style=" border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>
															';
											}
										}
										$left_column .='<tr><td colspan="5" style=" border-top:1px solid grey;font-weight:bold;">Labour Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align:center;">$'.number_format((float)$crew_total, 2, '.', '').'</td></tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table style="border: 1px solid black" cellpadding="1">
										<tr><th style="border-right: 1px solid grey; text-align:center; width:8%;font-weight:bold;">#</th><th style="border-right: 1px solid grey; text-align:center; width:36%;font-weight:bold;">Vehicle/Equipment Charges</th><th style="border-right: 1px solid grey; text-align:center; width:16%;font-weight:bold;">Qty</th><th style="border-right: 1px solid grey; text-align:center; width:8%;font-weight:bold;">Unit</th><th style="border-right: 1px solid grey; text-align:center; width:16%;font-weight:bold;">Rate</th><th style=" text-align:center; width:16%;font-weight:bold;">Amount</th></tr>';
										if($equip2_html != ''){
											$left_column .= $equip2_html;
										}
										else{
											$left_column .= '<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>';
										}
										$left_column .='
										<tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="5">Vehicle & Equipment Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align:center;">$'.number_format((float)$equip_total, 2, '.', '').'</td></tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table style="border: 1px solid black" cellpadding="1">
										<tr><th style="border-right: 1px solid grey; text-align:center; width:40%;font-weight:bold;">Materials Charges</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Qty</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Unit Price</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Amount</th></tr>';
										if($material2_html != ''){
											$left_column .= $material2_html;
										}
										else{
											$left_column .= '<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															 <tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															 <tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															 <tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															 <tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															 <tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>';
										}
										$left_column .= '
										<tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="3">Materials Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align:center;">$'.number_format((float)$material_subtotal, 2, '.', '').'</td></tr>
									</table>
								</td>
							</tr>
						</table>';
						if($other2_html != ''){
							$right_column = '<table border="1" frame="box" cellpadding="1">
							<tr><th style="border-right: 1px solid grey; text-align:center; width:80%;font-weight:bold;">Other Items</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Amount</th></tr>';

							$right_column .= $other2_html;
							$right_column .= '<tr><td  style="border-top:1px solid grey;font-weight:bold; text-align: left;">Other Items Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align: center;">$'.number_format((float)($other_subtotal+$sub_pay_rate_card), 2, '.', '').'</td></tr>
							</table>';
						}
						else{
							$right_column = '<table border="1" frame="box" cellpadding="1">
												<tr><th style="border-right: 1px solid grey; text-align:center; width:80%;font-weight:bold;">Other Items</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Amount</th></tr>
												<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
												<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
												<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
												<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
												<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
												<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
												<tr><td  style="border-top:1px solid grey;font-weight:bold; text-align: left;">Other Items Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align: center;">$'.number_format((float)($other_subtotal+$sub_pay_rate_card), 2, '.', '').'</td></tr>
											 </table>';
						}

							$right_column .= $cost_summary;


	// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

	// get current vertical position
	$y = $pdf->getY();

	// set color for background
	$pdf->SetFillColor(255, 255, 255);

	// set color for text
	$pdf->SetTextColor(0, 0, 0);

	// write the first column
	$pdf->writeHTMLCell(100, '', '', $y, $left_column, 0, 0, 1, true, 'J', true);

	// set color for background
	$pdf->SetFillColor(255, 255, 255);

	// set color for text
	$pdf->SetTextColor(0, 0, 0);

	// write the second column
	$pdf->writeHTMLCell(80, '', '', '', $right_column, 0, 1, 1, true, 'J', true);

	}

    foreach($po_image as $po_item) {
        $pdf->AddPage();
		if(strpos($po_item,'.pdf') === FALSE) {
			$pdf->Rotate(270, 70, 110);
		}
        $pdf->writeHTML($po_item, true, false, true, false, 'C');
    }

    $filename = $basename = "field_work_ticket_".$in_number.".pdf";
    $j = 0;
    while(file_exists('download/'.$filename)) {
        $filename = preg_replace('/(\.[a-z0-9]*)/', ' ('.++$j.')$1', $basename);
    }
    if(file_exists('download/'.$basename)) {
        rename('download/'.$basename, 'download/'.$filename);
    }

	$pdf->Output('download/field_work_ticket_'.$in_number.'.pdf', 'F');
	// PDF
    if($_POST['submit'] == 'Submit1') {

        // Send PDF in email
        $to = $customeremail;
		if($rookconnect == 'highland') {
			$subject ="Highland Projects Work Ticket";
		} else {
			$subject ="Field Work Ticket";
		}
		$message = decryptIt($contact['first_name']).", your Work Ticket is attached to this email in PDF format.";
		$ticket_file = 'download/field_work_ticket_'.$in_number.'.pdf';

		send_email('', $to, '', '', $subject, $message, $ticket_file);

		/* Old Send Mail System
		$headers = "From: $from";

        // boundary
        $semi_rand = md5(time());
        $mime_boundary = "==Multipart_Boundary_x{$semi_rand}x";

        // headers for attachment
        $headers .= "\nMIME-Version: 1.0\n" . "Content-Type: multipart/mixed;\n" . " boundary=\"{$mime_boundary}\"";

        // multipart boundary
        $message = "This is a multi-part message in MIME format.\n\n" . "--{$mime_boundary}\n" . "Content-Type: text/plain; charset=\"iso-8859-1\"\n" . "Content-Transfer-Encoding: 7bit\n\n" . $message . "\n\n";
        $message .= "--{$mime_boundary}\n";
		// Send ticket in email
        // Send PDF in email

        $filename = basename($ticket_file);
        $file = fopen($ticket_file,"rb");
        $data = fread($file,filesize($ticket_file));
        fclose($file);
        $data = chunk_split(base64_encode($data));
        $message .= "Content-Type: {\"application/octet-stream\"};\n" . " name=\"$ticket_file\"\n" .
        "Content-Disposition: attachment;\n" . " filename=\"$filename\"\n" .
        "Content-Transfer-Encoding: base64\n\n" . $data . "\n\n";
        $message .= "--{$mime_boundary}\n";*/
    }

	$total_cost = round($grand_total, 2);
    $sub_total_wt = round($sub_total, 2);
    $gst_wt = round($gst, 2);
    $subextra_total = round(number_format((float)($other_subtotal+$sub_pay_rate_card), 2, '.', ''), 2);

	$query_update_wt = "UPDATE `field_work_ticket` SET `crew_total` = '$crew_total', `equip_total` = '$equip_total', `material_total` = '$material_subtotal', `subextra_total` = '$subextra_total',    `sub_total` = '$sub_total_wt', `gst` = '$gst_wt', `total_cost` = '$total_cost' WHERE `workticketid` = '$in_number'";
	$result_update_wt	= mysqli_query($dbc, $query_update_wt);

	$before_change = '';
  $history = "field_work_ticket entry has been updated for workticketid -> $in_number. <br />";
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

	echo '<script type="text/javascript"> window.location.replace("field_work_ticket.php"); </script>';

   // mysqli_close($dbc); //Close the DB Connection
}

$edit_result = mysqli_fetch_array(mysqli_query($dbc, "select field_list from field_config_field_jobs where tab='work'"));
$edit_config = $edit_result['field_list'];
if(str_replace(',','',$edit_config) == '') {
	$edit_config = ',work_ticket,date,job,customer,invoice,sent,approved,';
}
?>
<script type="text/javascript">
    $(document).ready(function() {

    });
	$(document).on('change', 'select[name="jobid"]', function() { changeJob(this); });
	$(document).on('change', 'select[name="equ_billing_rate[]"]', function() { selectHours(this); });
	function numericFilter(txb) {
	   txb.value = txb.value.replace(/[^\0-9]/ig, "");
	}

    function multiplyPOCost(txb) {
        var get_id = txb.id;
        var split_id = get_id.split('_');
		var cost = parseFloat($('#pocost_'+split_id[1]).val());
		var mark_up = parseFloat($('#poper_'+split_id[1]).val());
		var gst = parseFloat($('#pogst_'+split_id[1]).val());
        var total = cost * (1 + mark_up / 100) * (1 + gst / 100);
		$('#pototal_'+split_id[1]).val(total.toFixed(2));
    }

	function multiplyCost(txb) {
        var get_id = txb.id;
        var split_id = get_id.split('_');

		var cost = parseFloat($('#pocost_'+split_id[1]).val());
		var mark_up = parseFloat($('#poper_'+split_id[1]).val());
		var tax = parseFloat(cost*0.05);
        var gst = parseFloat(cost*mark_up)/100;

        var cost_gst_tax = parseFloat(cost+gst+tax);
		document.getElementById('pototal_'+split_id[1]).value = cost_gst_tax.toFixed(2);
	}

    function changeJob(txb) {
        window.location = 'add_field_work_ticket.php?jobid='+txb.value+'&fsid=0&from=blank';
    }

</script>
</head>

<body>
<?php include_once ('../navigation.php');

?>
<div class="container">
  <div class="row">

		<h1	class="triple-pad-bottom">Work Ticket</h1>
		<?php if(empty($_GET['from_url'])) { ?>
			<div class="pad-left double-gap-bottom"><a href="field_work_ticket.php" class="btn config-btn">Back to Dashboard</a></div>
		<?php } else {
			echo '<div class="pad-left double-gap-bottom"><a href="'.urldecode($_GET['from_url']).'" class="btn config-btn">Back to Dashboard</a></div>';
		} ?>

		<form id="form1" name="form1" method="post"	action="add_field_work_ticket.php" enctype="multipart/form-data" class="form-horizontal" role="form">

		<?php
		$jobid = $_GET['jobid'];
		$job_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM field_jobs WHERE jobid = '$jobid'"));
		$job_num = $job_result['job_number'];

		$rate_card = explode('*',$job_result['ratecardid']);
		$rate_type = $rate_card[0];
		$rate_id = $rate_card[1];

		$fsid = $_GET['fsid'];
		$get_fs =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	field_foreman_sheet WHERE fsid='$fsid'"));

		$get_wt =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT workticketid FROM field_work_ticket ORDER BY workticketid DESC LIMIT 1"));

        $workticketid = $get_wt['workticketid']+1;

		$description = $get_fs['description'];

		$contactid = $get_fs['contactid'];
		$positionname = $get_fs['positionname'];
		$crew_reg_hour = $get_fs['crew_reg_hour'];
		$crew_ot_hour = $get_fs['crew_ot_hour'];
        $crew_travel_hour = $get_fs['crew_travel_hour'];
        $sub_pay = $get_fs['sub_pay'];

		$equipmentid = $get_fs['equipmentid'];
		$ebillrate = $get_fs['equ_billing_rate'];
		$ehours = $get_fs['equ_hours'];

        $stockmat_desc = $get_fs['stockmat_desc'];
        $stockmat_qty = $get_fs['stockmat_qty'];
        $stockmat_up = $get_fs['stockmat_up'];
        $stockmat_amount = $get_fs['stockmat_amount'];

		$fieldpoid = '';
		$billable_reg_hour = '';
		$billable_ot_hour = '';
        $wt_date = $get_fs['today_date'];

		if(!empty($_GET['workticketid'])) {
			$workticketid = $_GET['workticketid'];
			$get_wt =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	field_work_ticket WHERE workticketid='$workticketid'"));

			$fieldpoid = $get_wt['fieldpoid'];
			$description = $get_wt['description'];
			$billable_reg_hour = $get_wt['billable_reg_hour'];
			$billable_ot_hour =  $get_wt['billable_ot_hour'];
			$billable_travel_hour =  $get_wt['billable_travel_hour'];
            $wt_date = $get_wt['wt_date'];
            $sub_pay = $get_wt['sub_pay'];

		?>
		<input type="hidden" name="workticketid" value="<?php echo $workticketid ?>" />
		<?php	}  ?>

			<input type="hidden" id="jobid"	name="jobid" value="<?php echo $jobid ?>" />
			<input type="hidden" id="fsid"	name="fsid" value="<?php echo $fsid ?>" />
			<input type="hidden" name="description" value="<?php echo $description; ?>" />

			<?php if(strpos($edit_config, ',ticket,') !== false): ?>
				<div class="form-group">
					<label for="office_country" class="col-sm-4 control-label">WT#:</label>
					<div class="col-sm-8">
					   <input name="workticketid" disabled type="text" value="<?php echo $workticketid; ?>"  class="form-control"></p>
					</div>
				</div>
			<?php endif; ?>

				<?php if((!empty($_GET['from'])) && (empty($_GET['workticketid']))) { ?>
				<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label">Job#:</label>
				  <div class="col-sm-8">
					<select required id="jobid" data-placeholder="Choose a Job..." name="jobid" class="chosen-select-deselect form-control job_check" width="380">
					  <option value=""></option>
					  <?php
							$query = mysqli_query($dbc,"SELECT jobid, job_number FROM field_jobs WHERE deleted = 0");
						while($row = mysqli_fetch_array($query)) {
							if ($jobid == $row['jobid']) {
								$selected = 'selected="selected"';
							} else {
								$selected = '';
							}
							echo "<option ".$selected." value='". $row['jobid']."'>".$row['job_number'].'</option>';
						}
					  ?>
					</select>
				  </div>
				</div>
            <?php } else { ?>
            <div class="form-group">
                <label for="office_country" class="col-sm-4 control-label">Job#:</label>
                <div class="col-sm-8">
                   <?php echo $job_num; ?>
                </div>
            </div>
            <?php } ?>

			<?php if(strpos($edit_config, ',date,') !== false): ?>
				<div class="form-group">
					<label for="office_country" class="col-sm-4 control-label">Date:</label>
					<div class="col-sm-8">
					   <input name="wt_date" type="text" value="<?php echo $wt_date; ?>"  class="datepicker"></p>
					</div>
				</div>
			<?php endif; ?>

			<?php if(strpos($edit_config, ',description,') !== false): ?>
				<div class="form-group">
					<label for="additional_note" class="col-sm-4 control-label">Description:</label>
					<div class="col-sm-8">
						<textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
					</div>
				</div>
			<?php endif; ?>

			<div class="form-group">
				<label for="additional_note" class="col-sm-4 control-label">Attach New PO:</label>
				<div class="col-sm-8">

                <table class='table table-bordered'>
                        <?php
                            $mp_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT daily_rate FROM  client_rate_card  WHERE type='Mark Up' AND clientid IN(SELECT ratecard_clientid FROM field_jobs WHERE jobid='$jobid')"));
                            $mark_up = $mp_result['daily_rate'];
                            if($mark_up == '') {
                                $mark_up = 0.00;
                            }
                        ?>
					  <?php
					    //if(empty($_GET['workticketid'])) {
							//$query = mysqli_query($dbc,"SELECT fieldpoid, po_number FROM field_po WHERE jobid = '$jobid' AND attach_workticket=0 AND status='Complete'");
							//$query = mysqli_query($dbc,"SELECT fieldpoid, po_number, status, vendorid, cost, attach_workticket, vendor_invoice FROM field_po WHERE jobid = '$jobid' AND attach_workticket=0 AND status = 'To be Billed'");

							$query = mysqli_query($dbc,"SELECT *  FROM field_po WHERE jobid = '$jobid' AND attach_workticket=0 AND (status = 'To be Billed' OR status = 'Pending') AND `deleted`=0");

						//} else {
							//$query = mysqli_query($dbc,"SELECT fieldpoid, po_number, status, vendorid, cost, attach_workticket FROM field_po WHERE jobid = '$jobid'");
						//}
                        echo '<tr><th></th><th>PO</th><th>3rd Party Invoice(#)</th><th>Invoice</th><th>Desc</th><th>Sub Total</th><th>Mark up%</th><th>GST%</th><th>Total</th></tr>';
                        $i = 0;
						while($row = mysqli_fetch_array($query)) {

                            $style = '';
                            if($row['status'] == 'Pending') {
                                $style = "style= 'background-color: #bda501;'";
                            }
                            echo '<tr '.$style.'>';
							$vendid = $row['vendorid'];

							$vendname = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT vendor FROM vendors WHERE vendorid='$vendid'"));
							$vend_name = $vendname['vendor'];
                            $vendor_invoice = $row['vendor_invoice'];

                            $cost = $row['cost'];
                            //$tax = $cost*0.05;
                            $gst = $row['mark_up'];
                            $ftotal = $row['total_cost'];

                            echo '<td>';
                            if($row['status'] == 'To be Billed') { ?>
                            <input value='<?php echo  $row['fieldpoid'].'_'.$i; ?>' type="checkbox" name="fieldpoid[]" class="select_item">
                            <?php } else echo '-'; ?>
                            </td>
                            <td><?php echo $row['po_number'] ." : ".$vend_name; ?></td>
                            <td><?php echo $row['third_invoice_no']; ?></td>

                            <td>
                            <?php
                            if($vendor_invoice != '') {
                                $vin = explode('##FFM##', $vendor_invoice);
                                $vinc = 0;
                                foreach($vin as $venin) {
                                    if($venin != '') {
                                        echo ' - <a href="download/field_invoice/'.$venin.'" target="_blank">'.$venin.'</a><br>';
                                    }
                                    $vinc++;
                                }
                            }
                            ?>
                            </td>

                            <?php if($row['status'] == 'To be Billed') { ?>
						    <td><input name="wt_desc[]" value="<?php echo strip_tags(html_entity_decode($row['description'])); ?>" type="text" class="form-control office_zip" /></td>
						    <td><input name="wt_cost[]" value="<?php echo $row['cost']; ?>" id="pocost_<?php echo $i; ?>" type="text" class="form-control office_zip" onChange="numericFilter(this); multiplyPOCost(this);" /></td>
						    <td><input name="wt_per[]" id="poper_<?php echo $i; ?>" type="number" class="form-control office_zip" value="<?php echo $mark_up; ?>" onChange="numericFilter(this); multiplyPOCost(this);" /></td>
						    <td><input name="wt_gst[]" id="pogst_<?php echo $i; ?>" type="number" class="form-control office_zip" value="<?php echo $gst; ?>" onChange="numericFilter(this); multiplyPOCost(this);" /></td>
						    <td><input name="wt_total[]" value="<?php echo number_format((float)$ftotal, 2, '.', ''); ?>" id="pototal_<?php echo $i; ?>" type="text" class="form-control office_zip" /></td>
                            <?php } else { echo '<td>-</td><td>-</td><td>-</td><td>-</td>'; } ?>

							<?php
                            echo '</tr>';
                            if($row['status'] != 'Pending') {
                                $i++;
                            }
                        }
					    ?>
                    </table>
    			</div>
			</div>

            <?php if((!empty($_GET['workticketid'])) && ($fieldpoid != '')) { ?>
			<div class="form-group">
				<label for="additional_note" class="col-sm-4 control-label">Attached PO:</label>
				<div class="col-sm-8">

                <table class='table table-bordered'>

					  <?php
                        $ex_po_db = explode(',', $fieldpoid);
                        echo '<tr><th></th><th>PO</th><th>Desc</th><th>Cost</th><th>%</th><th>Total</th></tr>';
                        $i = 0;

                        foreach($ex_po_db as $ex_poid) {
                            $po_fwt = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT fwt.fieldpoid, fp.* FROM field_po fp, field_work_ticket fwt WHERE fwt.workticketid = '$workticketid' AND fp.fieldpoid = '$ex_poid'"));
                            echo '<tr>';
							$vend_name = get_client($dbc, $po_fwt['vendorid']);
							?>
                            <td>
                            <input value='<?php echo  $po_fwt['fieldpoid'].'_'.$i; ?>' type="checkbox" name="attached_fieldpoid[]" checked class="select_item">
                            </td>
                            <td><?php echo $po_fwt['po_number'] ." : ".$vend_name ." : $". $po_fwt['cost']; ?></td>
						    <td><input disabled name="wt_desc[]" value='<?php echo  $po_fwt['wt_desc']; ?>' type="text" class="form-control office_zip" /></td>
						    <td><input disabled name="wt_cost[]" value='<?php echo  $po_fwt['wt_cost']; ?>' id="pocost_<?php echo $i; ?>" type="text" class="form-control office_zip" onKeyUp="numericFilter(this); multiplyCost(this);" /></td>
						    <td><input disabled name="wt_per[]" value='<?php echo  $po_fwt['wt_per']; ?>' id="poper_<?php echo $i; ?>" type="text" class="form-control office_zip" onKeyUp="numericFilter(this); multiplyCost(this);" /></td>
						    <td><input disabled name="wt_total[]" value='<?php echo  $po_fwt['wt_total']; ?>' id="pototal_<?php echo $i; ?>" type="text" class="form-control office_zip" /></td>

							<?php
                            echo '</tr>';
                            $i++;
                        }
						?>
                    </table>
    			</div>
			</div>
            <?php } ?>



            <?php if(empty($_GET['from'])) { ?>
		    <h3>Crew</h3>
            <table class="wttable" border="0"><tr align="center">
                <td>Name</td>
                <td>Position</td>
                <td>Billable Reg</td>
                <td>Billable OT</td>
                <td>Billable Travel</td>
				<?php if(strpos($edit_config, ',mod_reg,') !== false): ?>
					<td>Modified Reg</td>
				<?php endif; ?>
				<?php if(strpos($edit_config, ',mod_ot,') !== false): ?>
					<td>Modified OT</td>
				<?php endif; ?>
                    <td>Modified Travel</td>
                    <td>Subsistence Pay</td>

			    <?php
				$total_count = mb_substr_count($contactid,',');

                $crew_contactid = explode(',',$contactid);
				$crew_position = explode(',',$positionname);
				$crew_reg_hour = explode(',',$crew_reg_hour);
				$crew_ot_hour = explode(',',$crew_ot_hour);
                $crew_travel_hour = explode(',',$crew_travel_hour);
				$billable_reg_hour = explode(',',$billable_reg_hour);
				$billable_ot_hour = explode(',',$billable_ot_hour);
                $billable_travel_hour = explode(',',$billable_travel_hour);
                $sub_pay = explode(',',$sub_pay);

				$tags_emp = explode(',',$employeeid);

				for($emp_loop=0; $emp_loop<=$total_count; $emp_loop++) {
                    $ccid = '';
					$cp = '';
					$crh = '';
					$coh = '';
					$ct = '';
                    $sp = 0;
                    $cth = '';

					$b_rh = $crew_reg_hour[$emp_loop];
					$b_oh = $crew_ot_hour[$emp_loop];
                    $b_th = $crew_travel_hour[$emp_loop];

					if(isset($crew_contactid[$emp_loop])) {
						$ccid = $crew_contactid[$emp_loop];
					}
					if(isset($crew_position[$emp_loop])) {
						$cp = $crew_position[$emp_loop];
					}
					if(isset($crew_reg_hour[$emp_loop])) {
						$crh = $crew_reg_hour[$emp_loop];
					}
					if(isset($crew_ot_hour[$emp_loop])) {
						$coh = $crew_ot_hour[$emp_loop];
					}
					if(isset($crew_travel_hour[$emp_loop])) {
						$cth = $crew_travel_hour[$emp_loop];
					}
					if(isset($billable_reg_hour[$emp_loop])) {
						$b_rh = $billable_reg_hour[$emp_loop];
					}
					if(isset($billable_ot_hour[$emp_loop])) {
						$b_oh = $billable_ot_hour[$emp_loop];
					}
					if(isset($billable_travel_hour[$emp_loop])) {
						$b_th = $billable_travel_hour[$emp_loop];
					}
                    if($sub_pay[$emp_loop] == 1) {
                        $sp = 1;
                    }
                    if(empty($_GET['workticketid'])) {
                        $b_rh = $crh;
                        $b_oh = $coh;
                        $b_th = $cth;
                    }
				?>

                <tr>
                    <td>
                        <input name="contactid[]" style="background-color: #989898;" readonly type="text" value="<?php echo get_staff($dbc, $ccid);	?>" class="form-control office_zip" />
					</td>
                    <td>
                        <input name="crew_position[]" style="background-color: #989898;" readonly type="text" value="<?php echo get_positions($dbc, $cp, 'name');	?>" class="form-control office_zip" />
					</td>
					<td class="sml-inp">
						<input name="crew_reg_hour[]" style="background-color: #989898;" readonly type="text" value="<?php echo $crh;	?>" class="form-control office_zip" />
					</td>
					<td class="sml-inp">
						<input name="crew_ot_hour[]" style="background-color: #989898;" readonly type="text" value="<?php echo $coh; ?>" class="form-control office_zip" />
					</td>
                    <td class="sml-inp">
						<input name="crew_travel_hour[]" style="background-color: #878687;" readonly type="text" value="<?php echo $cth; ?>" class="form-control office_zip" />
					</td>
					<?php if(strpos($edit_config, ',mod_reg,') !== false): ?>
						<td class="sml-inp">
							<input name="billable_reg_hour[]" type="text" value="<?php echo $b_rh; ?>" class="form-control office_zip" />
						</td>
					<?php endif; ?>
					<?php if(strpos($edit_config, ',mod_ot,') !== false): ?>
						<td class="sml-inp"><!-- Quantity -->
							<input name="billable_ot_hour[]" type="text" value="<?php echo $b_oh; ?>" class="form-control office_zip" />
						 </td>
					<?php endif; ?>
                    <td class="sml-inp"><!-- Quantity -->
						<input name="billable_travel_hour[]" type="text" value="<?php echo $b_th; ?>" class="form-control office_zip" />
					</td>
                    <td>
                        <select data-placeholder="Choose a Sub Pay..." name="sub_pay[]" class="chosen-select-deselect1 form-control" width="380">
                          <option value=""></option>
                          <option <?php if ($sp == '1') { echo  'selected="selected"'; } ?>  value="1">Yes</option>
                          <option <?php if ($sp == '0') { echo  'selected="selected"'; } ?> value="0">No</option>
                        </select>
                    </td>
                </tr>
			<?php } ?>

            </table>

            <h3>Equipment</h3>

                <table class="wttable" border="0"><tr align="center">
                    <tr>
                    <td>Equipment</td>
                    <td>Billing Rate</td>
                    <?php
                        if(approval_visible_function($dbc, 'field_job') == 1) { ?>
                    <td>Rates</td>
                    <?php } ?>
                    <td>Hours</td>
                </tr>

                <?php
				$ebillrate = explode(',',$ebillrate);
				$ehours = explode(',',$ehours);
				$eqid = explode(',',$equipmentid);
                $total_count_eq = mb_substr_count($equipmentid,',');

                for($eq_loop=0; $eq_loop<=$total_count_eq; $eq_loop++) {
                    $eq = '';
					$ebr = '';
					$eh = '';
					if(isset($eqid[$eq_loop])) {
						$eq = $eqid[$eq_loop];
					}
					if(isset($ebillrate[$eq_loop])) {
						$ebr = $ebillrate[$eq_loop];
					}
					if(isset($ehours[$eq_loop])) {
						$eh = $ehours[$eq_loop];
					}

                    $equ_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `type` FROM `equipment` WHERE `equipmentid` = '$eq'"));
                    $type = $equ_result['type'];
					$query = "SELECT hourly, daily FROM category_rate_table WHERE category = '$type' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
					if($rate_type == 'company') {
						$query = "SELECT `daily`, `hourly` FROM `company_rate_card` WHERE (`description`='$type' OR `description`='$eq') AND `rate_card_name` IN
							(SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='$rate_id') AND `deleted`=0";
					}
					else if($rate_type == 'customer') {
						$query = "";
					}
					else if($rate_type == 'equipment') {
						$query = "SELECT `daily`, `hourly` FROM `equipment_rate_table` WHERE `equipment_id`='$eq' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
					}
					else if($rate_type == 'category') {
						$query = "SELECT hourly, daily FROM category_rate_table WHERE category = '$type' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
					}
                    $rate_card = mysqli_fetch_assoc(mysqli_query($dbc, $query));

                    if (strpos($ebr, 'Daily') !== FALSE) {
                        $rate_eq_h = $rate_card['daily'];
                    }
                    if (strpos($ebr, 'Hourly') !== FALSE) {
                        $rate_eq_h = $rate_card['hourly'];
                    }

                    ?>

                    <tr>
                        <td>
                            <input name="equipmentid_<?php echo $eq_loop;?>[]" style="background-color: #989898;" readonly type="text" value="<?php echo get_equipment($dbc, $eq);	?>" class="form-control office_zip" />
				        </td>
                        <td class="sml-inp">
                            <select style="background-color: #989898;" disabled data-placeholder="Choose a Crew Member..." name="equ_billing_rate[]" id="<?php echo 'equrate_'.$eq_loop; ?>" class="chosen-select-deselect1 form-control office_zip" width="380">
                              <option value=""></option>
                              <option <?php if (strpos($ebr, 'Daily') !== FALSE) { echo " selected='selected'"; } ?> value="Daily">Daily</option>
                              <option <?php if (strpos($ebr, 'Hourly') !== FALSE) { echo " selected='selected'"; } ?> value="Hourly">Hourly</option>
                            </select>
						</td>
                        <?php
                            if(approval_visible_function($dbc, 'field_job') == 1) { ?>
                        <td>
                            <input disabled value="<?php echo $rate_eq_h; ?>"  type="text" class="form-control" />
                        </td>
                        <?php } ?>
                        <td class="sml-inp">
								<input name="equ_hours[]" value="<?php echo $eh; ?>" <?php if (strpos($ebr, 'Hourly') !== FALSE) { } else { echo " style='display:none;'"; } ?> type="text" class="form-control office_zip" />
						</td>
                    </tr>

                    <?php
                    } ?>
                </table>

                <h3>Stock/Material</h3>

				<div class="form-group clearfix">
					<label class="col-sm-3 text-center">Desc</label>
					<label class="col-sm-2 text-center">Qty</label>
					<label class="col-sm-2 text-center">Unit Price</label>
                    <label class="col-sm-2 text-center">Amount<br>(Mark Up 15%)</label>
				</div>
                <?php
						$tags_qty = explode(',',$stockmat_qty);

						//$equipmentid = explode(',',$equipmentid);
                        $stockmat_desc = explode('*#*',$stockmat_desc);
						$stockmat_up = explode(',',$stockmat_up);
						$stockmat_amount = explode(',',$stockmat_amount);

						$total_count = mb_substr_count($stockmat_qty,',');
                        echo '<input type="hidden" id="total_count_edit" value="'.$total_count.'">';
						$no_rate_position = '';
						for($sm_loop=0; $sm_loop<=$total_count; $sm_loop++) {
							$ct = '';
							$smd = '';
							$smq = '';
							$smup = '';
                            $sma = '';

							if(isset($tags_qty[$sm_loop])) {
								$smq = $tags_qty[$sm_loop];
							}
							if(isset($stockmat_desc[$sm_loop])) {
								$smd = $stockmat_desc[$sm_loop];
							}
							if(isset($stockmat_up[$sm_loop])) {
								$smup = $stockmat_up[$sm_loop];
							}
							if(isset($stockmat_amount[$sm_loop])) {
								$sma = $stockmat_amount[$sm_loop];
							}
						    ?>

							<div class="form-group clearfix">
							  <div class="col-sm-3">
                                <input name="stockmat_desc[]" style="background-color: #989898;" disabled type="text" id="<?php echo 'stockmatdesc_'.$sm_loop; ?>" value='<?php echo  $smd; ?>' class="form-control office_zip stockmat_desc" />
							  </div>
							<div class="col-sm-2">
								<input name="stockmat_qty[]" style="background-color: #989898;" disabled type="text" id="<?php echo 'stockmatqty_'.$sm_loop; ?>" onKeyUp="numericFilter(this); multiplyCost(this);" value='<?php echo  $smq; ?>' class="form-control office_zip stockmat_qty" />
							</div>
							<div class="col-sm-2">
								<input name="stockmat_up[]" style="background-color: #989898;" disabled type="text" id="<?php echo 'stockmatup_'.$sm_loop; ?>" onKeyUp="numericFilter(this); multiplyCost(this);" value='<?php echo  $smup; ?>' class="form-control office_zip " />
							</div>

							<div class="col-sm-2">
								<input name="stockmat_amount[]" style="background-color: #989898;" disabled type="text" id="<?php echo 'stockmatamount_'.$sm_loop; ?>" value='<?php echo  $sma; ?>' class="form-control office_zip " />
							</div>
						</div>
                    <?php } ?>
        <?php } ?>

		<div class="form-group">
			<div class="col-sm-4">
				<p><span class="text-red pull-right"><em>Required	Fields *</em></span></p>
			</div>
			<div class="col-sm-8"></div>
		</div>

		  <div class="form-group">
			<div class="col-sm-4 clearfix">
			<?php if(empty($_GET['from_url'])) { ?>
				<div class="pad-left double-gap-bottom"><a href="field_work_ticket.php" class="btn config-btn">Back</a></div>
			<?php } else {
				echo '<div class="pad-left double-gap-bottom"><a href="'.urldecode($_GET['from_url']).'" class="btn config-btn">Back</a></div>';
			} ?>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<?php if(empty($_GET['mode']) || $_GET['mode'] != 'view') { ?>
				<div class="col-sm-8"><br>
					<!-- <button	type="submit" name="submit"	value="Submit1" class="btn brand-btn btn-lg	pull-right" style="margin-right:10px; margin-left:10px;">Send &amp; Submit</button> -->
					<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Save Work Ticket</button>
				</div>
			<?php } ?>
		  </div>

		</form>
	</div>
  </div>

<?php include ('../footer.php'); ?>
