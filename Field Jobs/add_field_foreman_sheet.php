<?php
/*
Add	Sheet
*/
include ('../include.php');
checkAuthorised('field_job');
error_reporting(0);

$role = $_SESSION['role'];
$s_contactid = $_SESSION['contactid'];

if (isset($_POST['submit'])) {
	$submit = $_POST['submit'];

	$jobid = $_POST['jobid'];
    $afe_number = filter_var($_POST['afe_number'],FILTER_SANITIZE_STRING);
    $additional_info = filter_var($_POST['additional_info'],FILTER_SANITIZE_STRING);
    $siteid = $_POST['siteid'];
	$description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);

    $total_emp = count($_POST['contactid']);
    $eid = '';
    $pid = '';
    $crh = '';
    $cth = '';
    $coh = '';
    $sub = '';
    for($i=0; $i<$total_emp; $i++) {
        if($_POST['contactid'][$i] != '') {
            $eid .= $_POST['contactid'][$i].',';
            $pid .= $_POST['positionname'][$i].',';

            if($_POST['crew_reg_hour'][$i] == '') {
                $_POST['crew_reg_hour'][$i] = 0;
            }
            $crh .= $_POST['crew_reg_hour'][$i].',';
            if($_POST['crew_ot_hour'][$i] == '') {
                $_POST['crew_ot_hour'][$i] = 0;
            }
            $coh .=	$_POST['crew_ot_hour'][$i].',';

            if($_POST['crew_travel_hour'][$i] == '') {
                $_POST['crew_travel_hour'][$i] = 0;
            }
            $cth .=	$_POST['crew_travel_hour'][$i].',';

            if($_POST['sub_pay'][$i] == 1) {
                $sub .= '1,';
            } else {
                $sub .= '0,';
            }

        }
    }

    $sub_pay = rtrim($sub, ",");

    $contactid = rtrim($eid, ",");
    $positionname = rtrim($pid, ",");
    $crew_reg_hour = rtrim($crh, ",");
    $crew_travel_hour = rtrim($cth, ",");
    $crew_ot_hour = rtrim($coh, ",");

    $total_equ = count($_POST['equipmentid']);
    $eqid = '';
    $ebr = '';
    $eh = '';
    for($i=0; $i<$total_equ; $i++) {
        if($_POST['equipmentid'][$i] != '') {
            $eqid .= $_POST['equipmentid'][$i].',';
            $ebr .= $_POST['equ_billing_rate'][$i].',';
            $eh .= $_POST['equ_hours'][$i].',';
        }
    }
	$equipmentid = rtrim($eqid, ",");
	$equ_billing_rate = rtrim($ebr, ",");
	$equ_hours = rtrim($eh, ",");

    $total_sm = count($_POST['stockmat_qty']);
    $smd = '';
    $smq = '';
    $smup = '';
    $sma = '';
    for($i=0; $i<$total_sm; $i++) {
        if($_POST['stockmat_qty'][$i] != '') {
            $smd .= filter_var($_POST['stockmat_desc'][$i],FILTER_SANITIZE_STRING).'*#*';
            $smq .= $_POST['stockmat_qty'][$i].',';
            $smup .= $_POST['stockmat_up'][$i].',';
            $sma .= $_POST['stockmat_amount'][$i].',';
        }
    }
	$stockmat_desc = rtrim($smd, "*#*");
	$stockmat_qty = rtrim($smq, ",");
	$stockmat_up = rtrim($smup, ",");
    $stockmat_amount = rtrim($sma, ",");

	$today_date= $_POST['current_date'];
	if($_POST['comment'] == '') {
		$comment = '';
		$comment_by = '';
	} else {
		$comment = '##'.filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);
		$comment_by = '##'.$_SESSION['contactid'];
	}
	$supervisor_status = '';
	if($submit == 'submit_approval') {
		$supervisor_status = 'Pending';
	}

	if(empty($_POST['fsid'])) {
		$query_insert_site = "INSERT INTO `field_foreman_sheet` (`jobid`, `afe_number`, `additional_info`, `siteid`, `description`, `contactid`, `positionname`, `crew_reg_hour`, `crew_ot_hour`, `crew_travel_hour`, `equipmentid`, `equ_billing_rate`, `equ_hours`, `stockmat_desc`, `stockmat_qty`, `stockmat_up`, `stockmat_amount`, `comment`, `comment_by`, `today_date`, `sub_pay`, `supervisor_status`) VALUES	('$jobid', '$afe_number', '$additional_info', '$siteid', '$description', '$contactid', '$positionname', '$crew_reg_hour', '$crew_ot_hour', '$crew_travel_hour', '$equipmentid', '$equ_billing_rate', '$equ_hours', '$stockmat_desc', '$stockmat_qty', '$stockmat_up', '$stockmat_amount', '$comment', '$comment_by', '$today_date', '$sub_pay', '$supervisor_status')";
		$result_insert_site	= mysqli_query($dbc, $query_insert_site);
    $fsid = mysqli_insert_id($dbc);
		$before_change = '';
    $history = "field_foreman_sheet entry has been added. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
	} else {
		$fsid = $_POST['fsid'];
		$query_update_site = "UPDATE `field_foreman_sheet` SET `afe_number` = '$afe_number', `additional_info` = '$additional_info', `siteid` = '$siteid', `description` = '$description', `contactid`	= '$contactid', `positionname` = '$positionname', `crew_reg_hour` =	'$crew_reg_hour', `crew_ot_hour` = '$crew_ot_hour', `crew_travel_hour` = '$crew_travel_hour', `equipmentid` = '$equipmentid', `equ_billing_rate` = '$equ_billing_rate', `equ_hours` = '$equ_hours', `stockmat_desc` = '$stockmat_desc', `stockmat_qty` = '$stockmat_qty', `stockmat_up` = '$stockmat_up', `stockmat_amount` = '$stockmat_amount', `comment` = CONCAT(comment,'$comment'), `comment_by` = CONCAT(comment_by,'$comment_by'), `supervisor_status` = '$supervisor_status', `today_date` = '$today_date', `sub_pay` = '$sub_pay' WHERE `fsid` = '$fsid'";
		$result_update_site	= mysqli_query($dbc, $query_update_site);
		$before_change = '';
    $history = "field_invoice entry has been added for fsid -> $fsid. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

        if($jobid > 0) {
			$old_jobid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `jobid` FROM `field_foreman_sheet` WHERE `fsid`='$fsid'"))['jobid'];
			$before_change = capture_before_change($dbc, 'field_foreman_sheet', 'jobid', 'fsid', $fsid);
            mysqli_query($dbc, "UPDATE `field_foreman_sheet` SET `jobid` = '$jobid' WHERE `fsid` = '$fsid'");
	    $history = capture_after_change('jobid', $jobid);
	    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
			if($jobid != $old_jobid) {
				$before_change = capture_before_change($dbc, 'field_work_ticket', 'jobid', 'fsid', $fsid);
				mysqli_query($dbc, "UPDATE `field_work_ticket` SET `jobid`='$jobid' WHERE `fsid`='$fsid'");
				$history = capture_after_change('jobid', $jobid);
	    	add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

				mysqli_query($dbc, "UPDATE `field_po` LEFT JOIN `field_work_ticket` ON CONCAT(',',`field_work_ticket`.`fieldpoid`,',') LIKE CONCAT('%,',`field_po`.`fieldpoid`,',%') SET `field_po`.`jobid`='$jobid' WHERE `field_work_ticket`.`fsid`='$fsid'");
				$before_change = '';
			  $history = "field_invoice entry has been added for fsid -> $fsid. <br />";
			  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

			}
        }
	}

	if($submit == 'submit_approval') {
		$get_job =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT job_number, clientid, contactid FROM field_jobs WHERE jobid='$jobid'"));

        $clientid = $get_job['clientid'];
		$get_client =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT client_name FROM clients WHERE clientid='$clientid'"));

        $to = explode(',', get_config($dbc, 'fs_approval_email'));
		$subject = 'Foreman Sheet Pending Approval';
		$from = [];
		if($_POST['submit_email'] != '') {
			$from[$_POST['submit_email']] = $_POST['submit_email'];
		} else {
			$from[get_config($dbc, 'fs_from_email')] = 'Foreman Sheet Approval';
		}
		$supervisors = explode(',', get_config($dbc, 'fs_supervisor_email'));
		$send = array_merge($to, $supervisors);
		$message = "Below Foreman Sheet is submitted for your approval.<br><br>";
		if(count($send) == 0) {
			exit("<script>alert('Email addresses are missing from the configuration. Please correct this and try again.'); window.location.href='config_field_jobs.php?tab=foreman';</script>");
		}

        $url = WEBSITE_URL.'/Field Jobs';
		$message .= $get_client['client_name']. " : ".$get_job['job_number']. " : <a target='_blank' href='".$url."/add_field_foreman_sheet.php?jobid=".$jobid."&fsid=".$fsid."'>Click to Approve</a><br/>";
		if($from == '') {
			$from = [get_email($dbc, $_SESSION['contactid'])=>get_contact($dbc,$_SESSION['contactid'])];
		}

		foreach($send as $address) {
			if($address != '') {
				send_email($from, trim($address), '', '', $subject, $message, '');
			}
		}
	}

	if($submit == 'submit_approval') {
        /*
            $query_update_status = "UPDATE `field_foreman_sheet` SET office_status = 'Approved' WHERE `fsid` = '$fsid'";
            $result_update_status	= mysqli_query($dbc, $query_update_status);
            echo '<script type="text/javascript"> alert("Foreman Sheet is Submit and Approved by Office admin and redirected to Work Ticket."); window.location.replace("add_field_work_ticket.php?jobid='.$jobid.'&fsid='.$fsid.'"); </script>';
        } else {
        */
		    echo '<script type="text/javascript"> alert("This Foreman Sheet has been submitted for approval to Supervisor."); window.location.replace("field_foreman_sheet.php"); </script>';
        //}
	} else {
		echo '<script type="text/javascript"> alert("This Foreman Sheet has been Saved."); window.location.replace("field_foreman_sheet.php"); </script>';
	}
	// header('Location: field_jobs.php');

   // mysqli_close($dbc); //Close the DB Connection
}

if (isset($_POST['submit_status'])) {

	$submit_status = explode("-",$_POST['submit_status']);
	$who = $submit_status[0];
	$status = $submit_status[1];
	if($status == 'Flat_Rate') {
		$status = 'Approved';
	}
	$jobid = $_POST['jobid'];

	$fsid = $_POST['fsid'];
	$description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);
    $afe_number = filter_var($_POST['afe_number'],FILTER_SANITIZE_STRING);
    $additional_info = filter_var($_POST['additional_info'],FILTER_SANITIZE_STRING);
    $siteid = $_POST['siteid'];

    $total_emp = count($_POST['contactid']);
    $eid = '';
    $pid = '';
    $crh = '';
    $cth = '';
    $coh = '';
    for($i=0; $i<$total_emp; $i++) {
        if($_POST['contactid'][$i] != '') {
            $eid .= $_POST['contactid'][$i].',';
            $pid .= $_POST['positionname'][$i].',';
            $crh .= $_POST['crew_reg_hour'][$i].',';
            $cth .= $_POST['crew_travel_hour'][$i].',';
            $coh .=	$_POST['crew_ot_hour'][$i].',';
        }
    }
    $contactid = rtrim($eid, ",");
    $positionname = rtrim($pid, ",");
    $crew_reg_hour = rtrim($crh, ",");
    $crew_travel_hour = rtrim($cth, ",");
    $crew_ot_hour = rtrim($coh, ",");

    $total_equ = count($_POST['equipmentid']);
    $eqid = '';
    $ebr = '';
    $eh = '';
    for($i=0; $i<$total_equ; $i++) {
        if($_POST['equipmentid'][$i] != '') {
            $eqid .= $_POST['equipmentid'][$i].',';
            $ebr .= $_POST['equ_billing_rate'][$i].',';
            $eh .= $_POST['equ_hours'][$i].',';
        }
    }
	$equipmentid = rtrim($eqid, ",");
	$equ_billing_rate = rtrim($ebr, ",");
	$equ_hours = rtrim($eh, ",");

    $total_sm = count($_POST['stockmat_qty']);
    $smd = '';
    $smq = '';
    $smup = '';
    $sma = '';
    for($i=0; $i<$total_sm; $i++) {
        if($_POST['stockmat_qty'][$i] != '') {
            $smd .= $_POST['stockmat_desc'][$i].'*#*';
            $smq .= $_POST['stockmat_qty'][$i].',';
            $smup .= $_POST['stockmat_up'][$i].',';
            $sma .= $_POST['stockmat_amount'][$i].',';
        }
    }
	$stockmat_desc = rtrim($smd, "*#*");
	$stockmat_qty = rtrim($smq, ",");
	$stockmat_up = rtrim($smup, ",");
    $stockmat_amount = rtrim($sma, ",");

	if($_POST['comment'] == '') {
		$comment = '';
		$comment_by = '';
	} else {
		$comment = '##'.filter_var($_POST['comment'],FILTER_SANITIZE_STRING);
		$comment_by = '##'.$_SESSION['contactid'];
	}
    $today_date= $_POST['current_date'];
	$query_update_site = "UPDATE `field_foreman_sheet` SET `afe_number` = '$afe_number', `additional_info` = '$additional_info', `siteid` = '$siteid', `description` = '$description', `contactid`	= '$contactid', `positionname` = '$positionname', `crew_reg_hour` =	'$crew_reg_hour', `crew_travel_hour` =	'$crew_travel_hour', `crew_ot_hour` = '$crew_ot_hour', `equipmentid` = '$equipmentid', `equ_billing_rate` = '$equ_billing_rate', `equ_hours` = '$equ_hours', `stockmat_desc` = '$stockmat_desc', `stockmat_qty` = '$stockmat_qty', `stockmat_up` = '$stockmat_up', `stockmat_amount` = '$stockmat_amount', `comment` = CONCAT(comment,'$comment'), `comment_by` = CONCAT(comment_by,'$comment_by'), $who = '$status', `today_date` = '$today_date' WHERE `fsid` = '$fsid'";
	$result_update_site	= mysqli_query($dbc, $query_update_site);

	$before_change = '';
  $history = "field_foreman_sheet entry has been added for fsid -> $fsid. <br />";
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

    if($jobid > 0) {
				$before_change = capture_before_change($dbc, 'field_foreman_sheet', 'jobid', 'fsid', $fsid);
        mysqli_query($dbc, "UPDATE `field_foreman_sheet` SET `jobid` = '$jobid' WHERE `fsid` = '$fsid'");
				$history = capture_after_change('jobid', $jobid);
	    	add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
    }

	if($who == 'supervisor_status' && $status == 'Approved') {

		$fs_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT today_date FROM  field_foreman_sheet  WHERE fsid = '$fsid'"));
        $created_date = $fs_result['today_date'];

        $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(payrollid) AS total_payrollid FROM field_payroll WHERE fsid = '$fsid'"));
        $get_payroll = $get_config['total_payrollid'];
        if($get_payroll >= 1) {
            $result_restore_payroll = mysqli_query($dbc, "DELETE FROM `field_payroll` WHERE `fsid` = '$fsid'");
        }

        $total_emp = count($_POST['contactid']);
        for($i=0; $i<$total_emp; $i++) {
            if($_POST['contactid'][$i] != '') {
                $eid = $_POST['contactid'][$i];
                $pid = $_POST['positionname'][$i];

                if($_POST['crew_reg_hour'][$i] == '') {
                    $crh = 0;
                } else {
                    $crh = $_POST['crew_reg_hour'][$i];
                }
                if($_POST['crew_ot_hour'][$i] == '') {
                    $coh = 0;
                } else {
                    $coh =	$_POST['crew_ot_hour'][$i];
                }

                if($_POST['crew_travel_hour'][$i] == '') {
                    $cth = 0;
                } else {
                    $cth =	$_POST['crew_travel_hour'][$i];
                }

				$rate_card_id = explode('*',$dbc->query("SELECT `ratecardid` FROM `field_jobs` WHERE `jobid`='$jobid'")->fetch_assoc());
				if($rate_card_id[0] == 'company') {
					$mp_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE `rate_card_name` IN (SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='{$rate_card_id[1]}') AND 'Subsistence Pay' IN (`heading`,`description`) AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')"));
				} else if($rate_card_id[0] == 'position') {
					$mp_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT daily FROM  position_rate_table  WHERE position_id IN(SELECT position_id FROM positions WHERE name='Subsistence Pay') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')"));
				}
                $sub_pay_rate_card = ($mp_result['daily']*$count_subpay_pdf);

                if($_POST['sub_pay'][$i] == 1) {
                    $sub = $mp_result['daily'];
                } else {
                    $sub = 0;
                }

                $query_insert_payroll = "INSERT INTO `field_payroll` (`fsid`, `contactid`, `positionid`,`reg`, `ot`, `travel`, `sub`,`created_date`) VALUES ('$fsid', '$eid', '$pid', '$crh', '$coh', '$cth', '$sub', '$created_date')";
                $result_insert_payroll	= mysqli_query($dbc, $query_insert_payroll);
								$before_change = '';
						    $history = "field_payroll entry has been added. <br />";
						    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
            }
        }

		$before_change = capture_before_change($dbc, 'field_foreman_sheet', 'office_status', 'fsid', $fsid);
		$query_update_status = "UPDATE `field_foreman_sheet` SET office_status = 'Pending' WHERE `fsid` = '$fsid'";
		$result_update_status	= mysqli_query($dbc, $query_update_status);
		$history = capture_after_change('office_status', 'Pending');
	  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

		$get_job =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT job_number, clientid, contactid FROM field_jobs WHERE jobid='$jobid'"));

		$to = get_config($dbc, 'fs_supervisor_email');
		$subject = 'Field job Foreman Sheet';
		$from = get_config($dbc, 'fs_from_email');

		$message .= "Thank you, your request for Office Approval has been successfully submitted. ";
		$message .= "Job Number <b>".$get_job['job_number'] ."</b> is submitted for your approval.<br/>";

		$send = explode(',', $to);
		foreach($send as $address) {
			if($address != '') {
				send_email($from, trim($address), '', '', $subject, $message, '');
			}
		}

		echo '<script type="text/javascript"> alert("Foreman Sheet is Approved and Submit to Office for Approval."); window.location.replace("field_foreman_sheet.php"); </script>';
	}

	if($who == 'supervisor_status' && $status == 'Rejected') {
		$before_change = capture_before_change($dbc, 'field_foreman_sheet', 'supervisor_status', 'fsid', $fsid);

		$query_update_status = "UPDATE `field_foreman_sheet` SET supervisor_status = '' WHERE `fsid` = '$fsid'";
		$result_update_status	= mysqli_query($dbc, $query_update_status);

		$history = capture_after_change('supervisor_status', '');
	  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
		echo '<script type="text/javascript"> alert("Foreman Sheet is Rejected by Supervisor."); window.location.replace("add_field_foreman_sheet.php?jobid='.$jobid.'&fsid='.$fsid.'"); </script>';
	}

	if($who == 'office_status' && $status == 'Rejected') {
		$before_change = capture_before_change($dbc, 'field_foreman_sheet', 'supervisor_status', 'fsid', $fsid);
		$before_change .= capture_before_change($dbc, 'field_foreman_sheet', 'office_status', 'fsid', $fsid);

		$query_update_status = "UPDATE `field_foreman_sheet` SET supervisor_status = 'Pending', office_status = '' WHERE `fsid` = '$fsid'";
		$result_update_status	= mysqli_query($dbc, $query_update_status);

		$history = capture_after_change('supervisor_status', 'Pending');
		$history .= capture_after_change('office_status', '');
	  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

		echo '<script type="text/javascript"> alert("Foreman Sheet is Rejected by Office."); window.location.replace("add_field_foreman_sheet.php?jobid='.$jobid.'&fsid='.$fsid.'"); </script>';
	}

	if($who == 'office_status' && $submit_status[1] == 'Approved') {
		echo '<script type="text/javascript"> alert("Foreman Sheet is Approved by Office and redirected to Work Ticket."); window.location.replace("add_field_work_ticket.php?jobid='.$jobid.'&fsid='.$fsid.'"); </script>';
	} else if($who == 'office_status' && $submit_status[1] == 'Flat_Rate') {
		echo '<script type="text/javascript"> alert("Foreman Sheet has been Approved by Office."); window.location.replace("field_foreman_sheet.php"); </script>';
	}

}

if(isset($_GET['pdf_foreman_sheet'])) {
    ob_clean();
    include_once('../tcpdf/tcpdf.php');
	DEFINE('WT_LOGO', $wt_logo);

	class MYPDF extends TCPDF {

		public function Header() {
			$image_file = 'download/'.WT_LOGO;
			$this->SetFont('helvetica', '', 15);
			$footer_text = 'FOREMAN SHEET';
			$this->writeHTMLCell(0, 0, -140, 10, $footer_text, 0, 0, false, "L", true);
			$this->SetFont('helvetica', '', 9);
			$footer_text = '<strong>Please complete this document and submit to the office</strong><br>For additional details, please use an additional sheet';
			$this->writeHTMLCell(0, 0, 10, 20, $footer_text, 0, 0, false, "L", true);
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
    $html = '<h1>Foreman Sheet</h1>
    <table style="width:100%;" border="1" cellspacing="0" cellpadding="3">
        <tr>
            <td style="font-weight:bold;width:22%;">Job #:</td>
            <td colspan="6" style="width:78%;"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Date:</td>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">AFE #:</td>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Additional Info:</td>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Customer Site Location:</td>
            <td colspan="6"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;height:45px;">Description:</td>
            <td colspan="6" style="height:45px;"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;width:22%;"></td>
            <td style="font-weight:bold;width:24%;">Name</td>
            <td style="font-weight:bold;width:16%;">Position</td>
            <td style="font-weight:bold;width:9%;">Regular<br />Hrs</td>
            <td style="font-weight:bold;width:9%;">Overtime</td>
            <td style="font-weight:bold;width:8%;">Travel</td>
            <td style="font-weight:bold;width:12%;">Subsistence<br />Pay</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Crew:</td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Crew:</td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Crew:</td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Crew:</td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Crew:</td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Crew:</td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Crew:</td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;" colspan="2">Equipment</td>
            <td style="font-weight:bold;" colspan="2">Billing Rate<br />(Daily / Hourly)</td>
            <td style="font-weight:bold;" colspan="2">Hours</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Equipment:</td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;" colspan="2"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Equipment:</td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;" colspan="2"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Equipment:</td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;" colspan="2"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Equipment:</td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;" colspan="2"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Equipment:</td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;" colspan="2"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Equipment:</td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;" colspan="2"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;" colspan="2">Description</td>
            <td style="font-weight:bold;">Quantity</td>
            <td style="font-weight:bold;">Unit Price</td>
            <td style="font-weight:bold;" colspan="2">Amount<br />(Mark Up 15%)</td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Stock / Material:</td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;" colspan="2"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Stock / Material:</td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;" colspan="2"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Stock / Material:</td>
            <td style="font-weight:bold;" colspan="2"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;"></td>
            <td style="font-weight:bold;" colspan="2"></td>
        </tr>
        <tr>
            <td colspan="7"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;height:45px;">Comments:</td>
            <td style="font-weight:bold;height:45px;" colspan="6"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Submitter\'s Name:</td>
            <td style="font-weight:bold;" colspan="6"></td>
        </tr>
        <tr>
            <td style="font-weight:bold;">Submitter\'s Email:</td>
            <td style="font-weight:bold;" colspan="6"></td>
        </tr>
    </table>';
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('field_foreman_sheet_blank.pdf', 'I');
}

$edit_result = mysqli_fetch_array(mysqli_query($dbc, "select field_list from field_config_field_jobs where tab='foreman'"));
$edit_config = $edit_result['field_list'];
if(str_replace(',','',$edit_config) == '') {
	$edit_config = ',job,date,afe,additional,site,description,crew_name,crew_pos,crew_reg,crew_ot,crew_travel,crew_sub,equipment,stock_desc,stock_qty,stock_price,stock_amount,comments,';
}
$approvals = approval_visible_function($dbc, 'field_job');
?>

</head>
<script type="text/javascript">

$(document).ready(function() {

    $(".hide_show_crew").hide();
    $(".hide_show_equ").hide();
    $(".hide_show_equ_edit").hide();

    $(".alert-txt").hide();
	var name_eq = $('.equipmentid').length;
	//$('.equhours').hide();
	$('.dailyorhour').each(function(i, obj) {
		if($(this).val() == "Hourly") {
		   $(this).parent().next().show();
		}
	});

	$(".save_but").click(function() {
		var jc = $(".job_check").val();

		if (jc == '') {
			document.body.scrollTop = document.documentElement.scrollTop = 0;
			$(".alert-txt").show();
			return false;
			} else {
			$(".alert-txt").hide();
			}
	});

	$("[name=jobid]").change(function() {
        var fsid = $('#fsid').val();
        if(fsid > 0) {
            window.location = 'add_field_foreman_sheet.php?change_job=true&jobid='+this.value+'&fsid='+$('#fsid').val();
        } else {
            window.location = 'add_field_foreman_sheet.php?change_job=true&jobid='+this.value;
        }
	});

    $('#add_row_crew').on( 'click', function () {
            $(".hide_show_crew").show();
            var clone = $('.additional_crew').clone();
            clone.find('.form-control').val('');
            resetChosen(clone.find("select[class^=chosen]"));
            clone.removeClass("additional_crew");
            $('#add_here_new_crew').append(clone);
			$('[name="contactid[]"]').off('change',updateCrewPosition).change(updateCrewPosition);
            return false;
    });

    $('#add_row_equ').on( 'click', function () {
            //$(".hide_show_equ").show();
            var name_eq = $('.equipmentid').length;
            var add_new_id = name_eq;
            var clone = $('.additional_equ').clone();
            clone.find('.form-control').val('');
            clone.find('.equhours').hide();

            var add_id;
            for(add_id=0; add_id<name_eq; add_id++) {
                clone.find('#equid_'+add_id).attr('id', 'equid_'+add_new_id);
                clone.find('#equrate_'+add_id).attr('id', 'equrate_'+add_new_id);
                clone.find('.equ_hours_val'+add_id).attr('class', 'form-control  equ_hours_val'+add_new_id);
                clone.find('.equhours_'+add_id).attr('class', 'col-sm-2 equhours_'+add_new_id);
                add_new_id++;
            }

            clone.find('.form-control').trigger("change.select2");

            clone.removeClass("additional_equ");
            $('#add_here_new_equ').append(clone);
            var add_chosen;
            var co_mul = (name_eq+4);

            for(add_chosen=name_eq; add_chosen<co_mul; add_chosen++) {
                resetChosen($("#equid_"+add_chosen));
                resetChosen($("#equrate_"+add_chosen));
            }

            name_eq = co_mul;
            return false;
    });

    $('#add_row_equ_edit').on( 'click', function () {
        $(".hide_show_equ_edit").show();

        var name_eq1= parseFloat($('#total_count_edit').val());
        var name_eq = parseFloat(name_eq1+1);
        var add_new_id = $('.equipmentid').length;
        var v2 = add_new_id;

        var clone = $('.additional_equ_edit').clone();
        clone.find('.form-control').val('');
        clone.find('.equhours').hide();

        for(add_id=0; add_id<2; add_id++) {
            clone.find('#equid_'+name_eq).attr('id', 'equid_'+add_new_id);
            clone.find('#equrate_'+name_eq).attr('id', 'equrate_'+add_new_id);
            clone.find('.equ_hours_val'+name_eq).attr('class', 'form-control  equ_hours_val'+add_new_id);
            clone.find('.equhours_'+name_eq).attr('class', 'col-sm-2 equhours_'+add_new_id);
            add_new_id++;
        }

        //clone.find('.form-control').trigger("change.select2");

        clone.removeClass("additional_equ_edit");
        $('#add_here_new_equ_edit').append(clone);

        var add_chosen;
        var co_mul = (v2+2);
        for(add_chosen=v2; add_chosen<co_mul; add_chosen++) {
            resetChosen($("#equid_"+add_chosen));
            resetChosen($("#equrate_"+add_chosen));
        }

        v2 = co_mul;
        return false;
    });

    //var sm = 1;
    $('#add_row_stockmat').on( 'click', function () {
            //$(".hide_show_equ").show();
            var clone = $('.additional_stockmat').clone();
            clone.find('.form-control').val('');

            var sm = $('.stockmat_qty').length;
            clone.find('#stockmatqty_0').attr('id', 'stockmatqty_'+sm);
            clone.find('#stockmatup_0').attr('id', 'stockmatup_'+sm);
            clone.find('#stockmatamount_0').attr('id', 'stockmatamount_'+sm);
            //sm++;

            clone.removeClass("additional_stockmat");
            $('#add_here_new_stockmat').append(clone);
            return false;
    });

    //$(".hide_show_stockmat").hide();
    $('#edit_row_stockmat').on( 'click', function () {
            //$(".hide_show_stockmat").show();
            var clone = $('.edit_stockmat').clone();
            clone.find('.form-control').val('');

            var sm = $('.stockmat_qty').length;
            clone.find('.stockmatqty').attr('id', 'stockmatqty_'+sm);
            clone.find('.stockmatup').attr('id', 'stockmatup_'+sm);
            clone.find('.stockmatamount').attr('id', 'stockmatamount_'+sm);
            //sm++;

            clone.removeClass("edit_stockmat");
            $('#edit_here_new_stockmat').append(clone);
            return false;
    });

	$('[name="contactid[]"]').off('change',updateCrewPosition).change(updateCrewPosition);

	$('[id^=equrate]').change();
});
// $(document).on('change', 'select[name="jobid"]', function() { job_select(this); });
$(document).on('change', 'select[name="equ_billing_rate[]"]', function() { selectHours(this); });

function updateCrewPosition() {
	var pos = $(this).find('option:selected').data('position');
	if(pos == '' || pos == undefined) {
		return;
	}
	var select = $(this).closest('.form-group').find('[name="positionname[]"]');
	if(pos != select.value && pos != $(select).find('option:selected').text()) {
		if($(select).find('option[value="'+pos+'"]').length == 0) {
			pos = $(select).find('option[data-label="'+pos+'"]').val();
		}
		if($(select).find('option[value="'+pos+'"]').length > 0) {
			$(select).val(pos).change();
		}
	}
}
function updateCrewRate(select) {
	var line = $(select).closest('.form-group');
	var rate = $(select).find('option:selected').data('rate');
	line.find('[name=crew_rate]').val(rate);
}

function multiplyCost(txb) {
    var get_id = txb.id;
    var split_id = get_id.split('_');
    var amount = parseFloat($('#stockmatqty_'+split_id[1]).val() * $('#stockmatup_'+split_id[1]).val());
    document.getElementById('stockmatamount_'+split_id[1]).value = amount.toFixed(2);
    //materialStock();
}

function numericFilter(txb) {
   txb.value = txb.value.replace(/[^\0-9]/ig, "");
}
function selectHours(sel) {
	var rate = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	if(rate == 'Hourly') {
		$('.equhours_'+arr[1]).show();
	} else {
		$('.equ_hours_val'+arr[1]).val("");
		$('.equhours_'+arr[1]).hide();
	}

    var equid = $("#equid_"+arr[1]).val();
    var jobid = $("[name=jobid]").val();

	if(jobid == '') {
		return;
	}
    $.ajax({
		type: "GET",
		url: "field_job_ajax_all.php?from=field_job_fs&name="+jobid+"&equid="+equid,
		dataType: "html",   //expect html to be returned
		success: function(response){
			if(response == 'NO RATE CARD') {
				alert("No Rate Card selected for the current job. Please select a rate card.")
				return;
			}
            var result = response.split('*');
            if(rate == 'Daily') {
                $('#fillequrate_'+arr[1]).val(result[0]);
            } else {
                $('#fillequrate_'+arr[1]).val(result[1]);
            }
		}
	});
}
function job_select(sel) {
	var job = sel.value;
	$('[name=jobid]').val(job);
	$.ajax({
		type: "GET",
		url: "field_job_ajax_all.php?from=field_job_fs&name=none&jobid="+job,
		dataType: "html",
		success: function(response) {
			var data = response.split('*');
			var contactid = data[1];
			$('[name=afe_number]').val(data[2]);
			$('[name=siteid]').val(data[3]);
			$('[name=siteid]').trigger('change.select2');
			$('[name=additional_info]').val(data[4]);
		}
	});
}
</script>

<body>
<?php include_once ('../navigation.php');

?>
<div class="container">
	<div class="row">
		<form id="form1" name="form1" method="post"	action="add_field_foreman_sheet.php" enctype="multipart/form-data" class="form-horizontal" role="form">
		<?php
        $afe_number = '';
        $siteid = '';
        $additional_info = '';
		$job_num = '';

		if(!empty($_GET['jobid'])) {
			$jobid = $_GET['jobid'];
			$get_job =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	field_jobs WHERE jobid='$jobid'"));
            $contactid = $get_job['contactid'];
            $afe_number = $get_job['afe_number'];
            $siteid = $get_job['siteid'];
            $additional_info = $get_job['additional_info'];
			$job_num = $get_job['job_number'];
            $job_description = html_entity_decode($get_job['description']);
			$job_ratecardid = $get_job['ratecardid'];
		}
        $description = '';
		$contactid =	'';
		$positionname	= '';
		$crew_reg_hour = '';
		$crew_ot_hour = '';
		$equipmentid = '';
		$equ_billing_rate = '';
		$equ_hours = '';

        $stockmat_desc = '';
        $stockmat_qty = '';
        $stockmat_up = '';
        $stockmat_amount = '';

        $current_date = date('Y-m-d'); ?>
		<div class="col-md-12">

		<h1	class="triple-pad-bottom">Foreman Sheet<?php echo ($job_num != '' ? " for Job# ".$job_num : ""); ?></h1>
		<div class="pad-left double-gap-bottom"><a href="field_foreman_sheet.php" class="btn config-btn">Back to Dashboard</a></div>
		<?php if(!empty($_GET['fsid'])) {

			$fsid = $_GET['fsid'];
			$get_fs =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	field_foreman_sheet WHERE fsid='$fsid'"));

            $afe_number = $get_fs['afe_number'];
            $siteid = $get_fs['siteid'];
            $additional_info = $get_fs['additional_info'];

            $description = $get_fs['description'];
			$contactid =	$get_fs['contactid'];
			$positionname	= $get_fs['positionname'];
			$crew_reg_hour =	$get_fs['crew_reg_hour'];
			$crew_ot_hour = $get_fs['crew_ot_hour'];
            $crew_travel_hour = $get_fs['crew_travel_hour'];
			$equipmentid = $get_fs['equipmentid'];
			$equ_billing_rate = $get_fs['equ_billing_rate'];
			$equ_hours = $get_fs['equ_hours'];
			$supervisor_status = $get_fs['supervisor_status'];
			$office_status = $get_fs['office_status'];
            $current_date = $get_fs['today_date'];
			$stockmat_desc = $get_fs['stockmat_desc'];
			$stockmat_qty = $get_fs['stockmat_qty'];
			$stockmat_up = $get_fs['stockmat_up'];
            $stockmat_amount = $get_fs['stockmat_amount'];
            $sub_pay = $get_fs['sub_pay'];
		?>
		<input type="hidden" id="fsid"	name="fsid" value="<?php echo $fsid ?>" />
        <input type="hidden" id="add_update"	name="add_update" value="1" />
		<?php	} else {	   ?>
        <input type="hidden" id="add_update"	name="add_update" value="0" />
        <?php }

		if(!empty($_GET['jobid']) && !empty($_GET['change_job'])) {
			$jobid = $_GET['jobid'];
			$get_job =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	field_jobs WHERE jobid='$jobid'"));
            $contactid = $get_job['contactid'];
            $afe_number = $get_job['afe_number'];
            $siteid = $get_job['siteid'];
            $additional_info = $get_job['additional_info'];
			$job_num = $get_job['job_number'];
            $job_description = html_entity_decode($get_job['description']);
			$job_ratecardid = $get_job['ratecardid'];
		} ?>
		<input type="hidden" name="jobid" value="<?php echo $jobid ?>" />

		<?php if(strpos($edit_config,',job,') !== false && (empty($_GET['fsid']) || approval_visible_function($dbc, 'field_job') == 1)): ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><img src='<?php echo WEBSITE_URL; ?>/img/warning-txt.png' class="alert-txt"><span class="alert-txt" style="display:none; background-color:red; border-radius:5px; padding:5px;"><b> Please select a Job Number.</b></span> Job#:</label>
			  <div class="col-sm-8">
				<select required id="jobid" data-placeholder="Choose a Job..." name="jobid" class="chosen-select-deselect form-control job_check" width="380">
				  <option value=""></option>
				  <?php
					if(vuaed_visible_function($dbc, 'field_job') == 1) {
						$query = mysqli_query($dbc,"SELECT jobid, job_number FROM field_jobs WHERE deleted = 0");
					}
					else {
						$query = mysqli_query($dbc,"SELECT jobid, job_number FROM field_jobs WHERE deleted = 0 AND foremanid LIKE '%,".$s_contactid.",%'");
					}
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
		<?php else: ?>
            <div class="form-group">
                <label for="office_country" class="col-sm-4 control-label">Job#:</label>
                <div class="col-sm-8">
                   <?php echo $job_num.' - '.$job_description; ?>
                </div>
            </div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',date,') !== false): ?>
			<div class="form-group">
				<label for="office_country" class="col-sm-4 control-label">Date:</label>
				<div class="col-sm-8">
				   <input name="current_date" type="text" value="<?php echo $current_date; ?>"  class="datepicker"></p>
				</div>
			</div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',afe,') !== false): ?>
		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">AFE#<span class="text-red">*</span>:</label>
			<div class="col-sm-8">
			  <input name="afe_number" type="text" value="<?php echo $afe_number; ?>" class="form-control" />
			</div>
		  </div>
		<?php endif; ?>

	  <?php if(strpos($edit_config,',additional,') !== false): ?>
		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Additional Info<span class="text-red">*</span>:</label>
			<div class="col-sm-8">
			  <input name="additional_info" type="text" value="<?php echo $additional_info; ?>" class="form-control" />
			</div>
		  </div>
	  <?php endif; ?>

		<?php if(strpos($edit_config,',site,') !== false): ?>
			<div class="form-group location_db">
			  <label for="site_name" class="col-sm-4 control-label">Customer Site Location:</label>
			  <div class="col-sm-8">
				<select data-placeholder="Choose a Location..." name="siteid" class="chosen-select-deselect form-control" width="380">
				  <option value=""></option>
				  <?php
					$query = mysqli_query($dbc,"SELECT siteid, site_name FROM field_sites WHERE deleted=0");
					while($row = mysqli_fetch_array($query)) {
						if ($siteid == $row['siteid']) {
							$selected = 'selected="selected"';
						} else {
							$selected = '';
						}
						echo "<option ".$selected." value='". $row['siteid']."'>".$row['site_name'].'</option>';
					}
				  ?>
				</select>
			  </div>
			</div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',description,') !== false): ?>
			<div class="form-group">
				<label for="additional_note" class="col-sm-4 control-label">Description:</label>
				<div class="col-sm-8">
					<textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
				</div>
			</div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',crew_') !== false): ?>
			<!-- Foreman sheet crew -->
			<?php $id=1; $no_ratecard = 0;
			$sorted_list = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, category, name, first_name, last_name, position FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND status>0")); ?>

			<div class="form-group">
				<label for="additional_note" class="col-sm-4 control-label"><h3>Crew</h3></label>
				<div class="col-sm-8">
					<div class="form-group clearfix hide-titles-mob">
						<?php if(strpos($edit_config,',crew_name') !== false): ?>
							<label class="col-sm-3 text-center">Name</label>
						<?php endif; ?>
						<?php if(strpos($edit_config,',crew_pos') !== false): ?>
							<label class="col-sm-3 text-center">Position</label>
						<?php endif; ?>
						<?php if(strpos($edit_config,',crew_rate') !== false && $approvals == 1): ?>
							<label class="col-sm-1 text-center">Hourly Rate</label>
						<?php endif; ?>
						<?php if(strpos($edit_config,',crew_reg') !== false): ?>
							<label class="col-sm-1 text-center">Billable Reg</label>
						<?php endif; ?>
						<?php if(strpos($edit_config,',crew_ot') !== false): ?>
							<label class="col-sm-1 text-center">Billable OT</label>
						<?php endif; ?>
						<?php if(strpos($edit_config,',crew_travel') !== false): ?>
							<label class="col-sm-1 text-center">Billable Travel</label>
						<?php endif; ?>
						<?php if(strpos($edit_config,',crew_sub') !== false): ?>
							<label class="col-sm-1 text-center">Subsistence Pay</label>
						<?php endif; ?>
					</div>

					<?php
					if(empty($_GET['fsid'])) {
						?>

						<div class="additional_crew clearfix">
							<div class="clearfix"></div>

						<?php for($total_line=0; $total_line<=3; $total_line++) {
							$crew_rate = 0; ?>
							<div class="form-group clearfix">
								<?php if(strpos($edit_config,',crew_name') !== false): ?>
									<div class="col-sm-3"><label for="company_name" class="col-sm-4 show-on-mob control-label">Name:</label>
										<select data-placeholder="Choose a Crew Member..." name="contactid[]" class="chosen-select-deselect form-control office_zip" width="380">
										  <option value=""></option>
										  <?php foreach($sorted_list as $staff) { ?>
												<option data-position="<?= $staff['position'] ?>" value='<?php echo  $staff['contactid']; ?>' ><?= $staff['full_name'] ?></option>
											<?php  } ?>
										</select>
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config,',crew_pos') !== false): ?>
									<div class="col-sm-3"><label for="company_name" class="col-sm-4 show-on-mob control-label">Position:</label>
										<select data-placeholder="Choose a Position..." name="positionname[]" class="chosen-select-deselect form-control office_zip" onchange="updateCrewRate(this);" width="380">
											<option value=""></option>
											<?php $rate_sql = "LEFT JOIN `position_rate_table` `rates` ON `positions`.`position_id`=`rates`.`position_id` AND `rates`.`deleted`=0 AND DATE(NOW()) BETWEEN `rates`.`start_date` AND IFNULL(NULLIF(`rates`.`end_date`,'0000-00-00'),'9999-12-31')";
											$rate_card_id = (strpos($job_ratecardid,'*') === FALSE ? explode('*','customer*'.$job_ratecardid) : $rate_card_id = explode('*',$job_ratecardid));
											if($rate_card_id[0] == 'company') {
												$rate_name = $dbc->query("SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='{$rate_card_id[1]}'")->fetch_assoc()['rate_card_name'];
												$rate_sql = "LEFT JOIN `company_rate_card` `rates` ON `positions`.`name`=`rates`.`description` AND `rates`.`deleted`=0 AND `rates`.`rate_card_name`='$rate_name' AND DATE(NOW()) BETWEEN `rates`.`start_date` AND IFNULL(NULLIF(`rates`.`end_date`,'0000-00-00'),'9999-12-31')";
											}
											$query = mysqli_query($dbc,"SELECT `positions`.position_id, `positions`.name, `rates`.`hourly` FROM positions $rate_sql WHERE `positions`.`deleted`=0 AND `rates`.`hourly` > 0 ORDER BY `name`");
											while($row = mysqli_fetch_array($query)) { ?>
												<option data-rate="<?= $row['hourly'] ?>" data-label="<?= $row['name'] ?>" value='<?= $row['position_id'] ?>' ><?= $row['name'] ?></option>
											<?php } ?>
										</select>
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config,',crew_rate') !== false && $approvals == 1): ?>
									<div class="col-sm-1">
										<input disabled type="text" class="form-control" name="crew_rate" value="" />
									</div>
								<?php endif; ?>
								<?php if(strpos($edit_config,',crew_reg') !== false): ?>
									<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Billable Reg:</label>
										<input name="crew_reg_hour[]" type="text" class="form-control office_zip" />
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config,',crew_ot') !== false): ?>
									<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Billable OT:</label>
										<input name="crew_ot_hour[]" type="text" class="form-control office_zip" />
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config,',crew_travel') !== false): ?>
									<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Billable Travel:</label>
										<input name="crew_travel_hour[]" type="text" class="form-control office_zip" />
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config,',crew_sub') !== false): ?>
									<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Subsistence Pay:</label>
										<select data-placeholder="Choose a Sub Pay..." name="sub_pay[]" class="chosen-select-deselect form-control" width="380">
                                          <option value=""></option>
                                          <option value="1">Yes</option>
                                          <option selected value="0">No</option>
                                        </select>
									</div> <!-- Quantity -->
								<?php endif; ?>
							</div>
						<?php } ?>

						</div>

						<div id="add_here_new_crew"></div>

						<div class="form-group triple-gapped clearfix">
							<div class="col-sm-offset-4 col-sm-8">
								<button id="add_row_crew" class="btn brand-btn pull-left">Add More</button>
							</div>
						</div>

						<?php
							$id++;
					} else {
							$tags_emp = explode(',',$contactid);
							$positionname = explode(',',$positionname);
							$crew_reg_hour = explode(',',$crew_reg_hour);
							$crew_ot_hour = explode(',',$crew_ot_hour);
                            $crew_travel_hour = explode(',',$crew_travel_hour);
                            $sub_pay = explode(',',$sub_pay);

							$total_count = mb_substr_count($contactid,',');
							$no_ratecard = 0;
							$no_rate_position = '';
							for($emp_loop=0; $emp_loop<=$total_count; $emp_loop++) {
								$empid = '';
								$cp = '';
								$crh = '';
								$coh = '';
								$ct = '';
								$style = '';
                                $cth = '';
                                $sp = 0;

								if(isset($tags_emp[$emp_loop])) {
									$empid = $tags_emp[$emp_loop];
								}
								if(isset($positionname[$emp_loop])) {
									$cp = $positionname[$emp_loop];
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
                                if($sub_pay[$emp_loop] == 1) {
                                    $sp = 1;
                                }

								$crew_rate = 0; ?>

								<div class="form-group clearfix">
									<?php if(strpos($edit_config,',crew_name') !== false): ?>
									  <div class="col-sm-3"><label for="company_name" class="col-sm-4 show-on-mob control-label">Name:</label>
										<select data-placeholder="Choose a Crew Member..." name="contactid[]" class="chosen-select-deselect form-control office_zip" width="380">
										  <option value=""></option>
										  <?php foreach($sorted_list as $staff) { ?>
												<option <?= ($staff['contactid'] == $empid ? 'selected' : '') ?> data-position="<?= $staff['position'] ?>" value='<?php echo  $staff['contactid']; ?>' ><?= $staff['full_name'] ?></option>
											<?php  } ?>
										</select>
									  </div> <!-- Quantity -->
								  <?php endif; ?>
								  <?php if(strpos($edit_config,',crew_pos') !== false): ?>
									  <div class="col-sm-3"><label for="company_name" class="col-sm-4 show-on-mob control-label">Position:</label>
										<select <?php echo $style;?> data-placeholder="Choose a Position..." name="positionname[]" class="chosen-select-deselect form-control office_zip" onchange="updateCrewRate(this);" width="380">
										  <option value=""></option>
										  <?php $rate_sql = "LEFT JOIN `position_rate_table` `rates` ON `positions`.`position_id`=`rates`.`position_id` AND `rates`.`deleted`=0 AND DATE(NOW()) BETWEEN `rates`.`start_date` AND IFNULL(NULLIF(`rates`.`end_date`,'0000-00-00'),'9999-12-31')";
											$rate_card_id = (strpos($job_ratecardid,'*') === FALSE ? explode('*','customer*'.$job_ratecardid) : $rate_card_id = explode('*',$job_ratecardid));
											if($rate_card_id[0] == 'company') {
												$rate_name = $dbc->query("SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='{$rate_card_id[1]}'")->fetch_assoc()['rate_card_name'];
												$rate_sql = "LEFT JOIN `company_rate_card` `rates` ON `positions`.`name`=`rates`.`description` AND `rates`.`deleted`=0 AND `rates`.`rate_card_name`='$rate_name' AND DATE(NOW()) BETWEEN `rates`.`start_date` AND IFNULL(NULLIF(`rates`.`end_date`,'0000-00-00'),'9999-12-31')";
											}
											$query = mysqli_query($dbc,"SELECT `positions`.position_id, `positions`.name, `rates`.`hourly` FROM positions $rate_sql WHERE `positions`.`deleted`=0 AND `rates`.`hourly` > 0 ORDER BY `name`");
											while($row = mysqli_fetch_array($query)) { ?>
												<option data-rate="<?= $row['hourly'] ?>" <?php if ($cp == $row['position_id']) {
													$crew_rate = $row['hourly'];
													echo " selected='selected'";
												} ?> data-label="<?= $row['name'] ?>" value='<?php echo  $row['position_id']; ?>' ><?php echo $row['name']; ?></option>
											<?php } ?>
										</select>
									  </div> <!-- Quantity -->
								  <?php endif; ?>
								<?php if(strpos($edit_config,',crew_rate') !== false && $approvals == 1): ?>
									<div class="col-sm-1">
										<input disabled type="text" class="form-control" name="crew_rate" value="<?= $crew_rate ?>" />
									</div>
								<?php endif; ?>
								  <?php if(strpos($edit_config,',crew_reg') !== false): ?>
									  <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Billable Reg:</label>
										<input name="crew_reg_hour[]" type="text" value="<?php echo $crh;	?>" class="form-control office_zip" />
									  </div> <!-- Quantity -->
								  <?php endif; ?>
								  <?php if(strpos($edit_config,',crew_ot') !== false): ?>
									  <div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Billable OT:</label>
										<input name="crew_ot_hour[]" type="text" value="<?php echo $coh; ?>" class="form-control office_zip" />
									  </div> <!-- Quantity -->
								  <?php endif; ?>

								<?php if(strpos($edit_config,',crew_travel') !== false): ?>
									<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Billable Travel:</label>
										<input name="crew_travel_hour[]" type="text" value="<?php echo $cth; ?>" class="form-control office_zip" />
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config,',crew_sub') !== false): ?>
									<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Subsistence Pay:</label>
										<select data-placeholder="Choose a Sub Pay..." name="sub_pay[]" class="chosen-select-deselect form-control" width="380">
                                          <option value=""></option>
                                          <option <?php if ($sp == '1') { echo  'selected="selected"'; } ?> value="1">Yes</option>
                                          <option <?php if ($sp == '0') { echo  'selected="selected"'; } ?> value="0">No</option>
                                        </select>
									</div> <!-- Quantity -->
								<?php endif; ?>

							</div>

						<?php } ?>

						<div class="enter_cost additional_crew hide_show_crew">
							<div class="clearfix"></div>

							<div class="form-group clearfix">
								<?php $crew_rate = 0;
								if(strpos($edit_config,',crew_name') !== false): ?>
									<div class="col-sm-3"><label for="company_name" class="col-sm-4 show-on-mob control-label">Name:</label>
										<select data-placeholder="Choose a Crew Member..." name="contactid[]" class="chosen-select-deselect form-control office_zip" width="380">
										  <option value=""></option>
										  <?php foreach($sorted_list as $staff) { ?>
												<option data-position="<?= $staff['position'] ?>" value='<?php echo  $staff['contactid']; ?>' ><?= $staff['full_name'] ?></option>
											<?php  } ?>
										</select>
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config,',crew_pos') !== false): ?>
									<div class="col-sm-3"><label for="company_name" class="col-sm-4 show-on-mob control-label">Position:</label>
										<select data-placeholder="Choose a Position..." name="positionname[]" class="chosen-select-deselect form-control office_zip" onchange="updateCrewRate(this);" width="380">
										  <option value=""></option>
										  <?php $rate_sql = "LEFT JOIN `position_rate_table` `rates` ON `positions`.`position_id`=`rates`.`position_id` AND `rates`.`deleted`=0 AND DATE(NOW()) BETWEEN `rates`.`start_date` AND IFNULL(NULLIF(`rates`.`end_date`,'0000-00-00'),'9999-12-31')";
											$rate_card_id = (strpos($job_ratecardid,'*') === FALSE ? explode('*','customer*'.$job_ratecardid) : $rate_card_id = explode('*',$job_ratecardid));
											if($rate_card_id[0] == 'company') {
												$rate_name = $dbc->query("SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='{$rate_card_id[1]}'")->fetch_assoc()['rate_card_name'];
												$rate_sql = "LEFT JOIN `company_rate_card` `rates` ON `positions`.`name`=`rates`.`description` AND `rates`.`deleted`=0 AND `rates`.`rate_card_name`='$rate_name' AND DATE(NOW()) BETWEEN `rates`.`start_date` AND IFNULL(NULLIF(`rates`.`end_date`,'0000-00-00'),'9999-12-31')";
											}
											$query = mysqli_query($dbc,"SELECT `positions`.position_id, `positions`.name, `rates`.`hourly` FROM positions $rate_sql WHERE `positions`.`deleted`=0 AND `rates`.`hourly` > 0 ORDER BY `name`");
											while($row = mysqli_fetch_array($query)) { ?>
												<option data-rate="<?= $row['hourly'] ?>" data-label="<?= $row['name'] ?>" value='<?php echo  $row['position_id']; ?>' ><?php echo $row['name']; ?></option>
											<?php  }
											?>
										</select>
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config,',crew_rate') !== false && $approvals == 1): ?>
									<div class="col-sm-1">
										<input disabled type="text" class="form-control" name="crew_rate" value="<?= $crew_rate ?>" />
									</div>
								<?php endif; ?>
								<?php if(strpos($edit_config,',crew_reg') !== false): ?>
									<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Billable Reg:</label>
										<input name="crew_reg_hour[]" type="text" class="form-control office_zip" />
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config,',crew_ot') !== false): ?>
									<div class="col-sm-1"><label for="company_name" class="col-sm-4 show-on-mob control-label">Billable OT:</label>
										<input name="crew_ot_hour[]" type="text" class="form-control office_zip" />
									</div> <!-- Quantity -->
								<?php endif; ?>

                                <?php if(strpos($edit_config,',crew_travel') !== false): ?>
                                <div class="col-sm-1">
                                    <input name="crew_travel_hour[]" type="text" class="form-control office_zip" />
                                </div> <!-- Quantity -->
                                <?php endif; ?>
                                <?php if(strpos($edit_config,',crew_sub') !== false): ?>
                                <div class="col-sm-1">
                                    <select data-placeholder="Choose a Sub Pay..." name="sub_pay[]" class="chosen-select-deselect form-control" width="380">
                                      <option value=""></option>
                                      <option value="1">Yes</option>
                                      <option selected value="0">No</option>
                                    </select>
                                </div>
                                <?php endif; ?>

							</div>

						</div>

						<div id="add_here_new_crew"></div>

						<div class="form-group triple-gapped clearfix">
							<div class="col-sm-offset-4 col-sm-8">
								<button id="add_row_crew" class="btn brand-btn pull-left">Add More</button>
							</div>
						</div>

						<?php }

						?>
				</div>
			</div>
		<?php endif; ?>

		<?php if(strpos($edit_config,',equipment,') !== false): ?>
			<!-- Foreman sheet equipment -->

			<?php $id=1; ?>

			<div class="form-group">
				<label for="additional_note" class="col-sm-4 control-label"><h3>Equipment</h3></label>
				<div class="col-sm-8">
					<div class="form-group clearfix hide-titles-mob">
						<label class="col-sm-3 text-center">Equipment</label>
						<label class="col-sm-2 text-center">Billing Rate</label>
                        <?php
                        if($approvals == 1) { ?>
                        <label class="col-sm-2 text-center">Rates</label>
                        <?php } ?>
						<label class="col-sm-2 text-center">Hours</label>
					</div>

					<?php
					if(empty($_GET['fsid'])) {
						?>

						<div class="enter_cost additional_equ clearfix">
							<div class="clearfix"></div>

							<?php for($total_line=0; $total_line<=3; $total_line++) {
							?>
							<div class="form-group clearfix">
								<div class="col-sm-3"><label for="company_name" class="col-sm-4 show-on-mob control-label">Equipment:</label>
									<select data-placeholder="Choose Equipment..." id="<?php echo 'equid_'.$total_line; ?>" name="equipmentid[]" class="chosen-select-deselect form-control equipmentid" width="380">
										<option value=""></option>
										<?php
											$query = mysqli_query($dbc,"SELECT * FROM equipment WHERE deleted=0 AND `status` != 'Inactive' ORDER BY `unit_number`, `type`");

											while($row = mysqli_fetch_array($query)) {
												?>
												<option value='<?php echo  $row['equipmentid']; ?>' ><?php echo $row['unit_number'].': '.$row['type']; ?></option>
										  <?php  }
										?>
									</select>
								</div>
								<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Billing Rate:</label>
									<select data-placeholder="Choose a Billing Rate..." name="equ_billing_rate[]" id="<?php echo 'equrate_'.$total_line; ?>" class="chosen-select-deselect form-control office_zip" width="380">
									  <option value=""></option>
									  <option value="Daily">Daily</option>
									  <option value="Hourly">Hourly</option>
									</select>
								</div> <!-- Quantity -->
                                <?php
								if(vuaed_visible_function($dbc, 'field_job') == 1) { ?>
									<div class="<?= $approvals == 1 ? 'col-sm-2' : 'hidden' ?>">
										<input disabled id="<?php echo 'fillequrate_'.$total_line; ?>"  type="text" class="form-control" />
									</div>
                                <?php } ?>

								<div class="col-sm-2 equhours <?php echo 'equhours_'.$total_line; ?>" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Hours:</label>
									<input name="equ_hours[]" type="text" class="form-control office_zip equ_hours_val<?php echo $total_line; ?>" />
								</div> <!-- Quantity -->
							</div>
						<?php } ?>

						</div>

						<div id="add_here_new_equ"></div>

						<div class="form-group triple-gapped clearfix">
							<div class="col-sm-offset-4 col-sm-8">
								<button id="add_row_equ" class="btn brand-btn pull-left">Add More</button>
							</div>
						</div>

						<?php
							$id++;
					} else {

							$tags_equ = explode(',',$equipmentid);

							//$equipmentid = explode(',',$equipmentid);
							$equ_billing_rate = explode(',',$equ_billing_rate);
							$equ_hours = explode(',',$equ_hours);

							$total_count = mb_substr_count($equipmentid,',');

							$equipmentid = explode(',',$equipmentid);
							echo '<input type="hidden" id="total_count_edit" value="'.$total_count.'">';
							$no_rate_position = '';
							for($emp_loop=0; $emp_loop<=$total_count; $emp_loop++) {
								$ct = '';
								$eq = '';
								$ebr = '';
								$eh = '';
								$style = '';
                                $rate_eq_h = 0;

								if(isset($equipmentid[$emp_loop])) {
									$eq = $equipmentid[$emp_loop];
								}
								if(isset($equ_billing_rate[$emp_loop])) {
									$ebr = $equ_billing_rate[$emp_loop];
								}
								if(isset($equ_hours[$emp_loop])) {
									$eh = $equ_hours[$emp_loop];
								}

								$rate_card = ['daily'=>0,'hourly'=>0];
								if(strpos($job_ratecardid,'*') === FALSE) {
									$rate_card_id = explode('*','customer*'.$job_ratecardid);
								} else {
									$rate_card_id = explode('*',$job_ratecardid);
								}

								$query = "";
								if($rate_card_id[0] == 'company') {
									$equ_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT category, type FROM equipment WHERE equipmentid = '$eq'"));
									$query = "SELECT `daily`, `hourly` FROM `company_rate_card` WHERE (`description`='{$equ_result['category']}' OR `description`='{$equ_result['type']}' OR `description`='$eq') AND `rate_card_name` IN (SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='{$rate_card_id[1]}') AND `deleted`=0";
								} else if($rate_card_id[0] == 'equipment') {
									$query = "SELECT `daily`, `hourly` FROM `equipment_rate_table` WHERE `equipment_id`='$eq' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
								} else if($rate_card_id[0] == 'category') {
									$equ_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT category FROM equipment WHERE equipmentid = '$eq'"));
									$query = "SELECT daily, hourly FROM category_rate_table WHERE category='{$equ_result['category']}' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
								}
								$rate_card = mysqli_fetch_assoc(mysqli_query($dbc, $query));

                                if (strpos($ebr, 'Daily') !== FALSE) {
                                    $rate_eq_h = $rate_card['daily'];
                                }
                                if (strpos($ebr, 'Hourly') !== FALSE) {
                                    $rate_eq_h = $rate_card['hourly'];
                                } ?>

								<div class="form-group clearfix">
								  <div class="col-sm-3"><label for="company_name" class="col-sm-4 show-on-mob control-label">Equipment:</label>
									<select id="<?php echo 'equid_'.$emp_loop; ?>" data-placeholder="Choose Equipment..."  name="equipmentid[]" class="chosen-select-deselect form-control equipmentid" width="380">
										<option value=""></option>
										<?php
											$query = mysqli_query($dbc,"SELECT * FROM equipment WHERE deleted=0 ORDER BY `unit_number`, `type`");
											while($row = mysqli_fetch_array($query)) {
												?>
												<option <?php if ($eq == $row['equipmentid']) {
											echo " selected='selected'"; } ?> value='<?php echo  $row['equipmentid']; ?>' ><?php echo $row['unit_number'].': '.$row['type']; ?></option>
										  <?php  }
										?>
									</select>
								  </div> <!-- Quantity -->
								<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Billing Rate:</label>
									<select data-placeholder="Choose a Billing Rate..." name="equ_billing_rate[]" id="<?php echo 'equrate_'.$emp_loop; ?>" class="chosen-select-deselect form-control office_zip dailyorhour" width="380">
									  <option value=""></option>
									  <option <?php if (strpos($ebr, 'Daily') !== FALSE) { echo " selected='selected'"; } ?> value="Daily">Daily</option>
									  <option <?php if (strpos($ebr, 'Hourly') !== FALSE) { echo " selected='selected'"; } ?> value="Hourly">Hourly</option>
									</select>
								</div> <!-- Quantity -->

                                <?php
								if(vuaed_visible_function($dbc, 'field_job') == 1) { ?>
									<div class="<?= $approvals == 1 ? 'col-sm-2' : 'hidden' ?>">
										<input disabled value="<?php echo $rate_eq_h; ?>"  id="<?php echo 'fillequrate_'.$emp_loop; ?>"  type="text" class="form-control" />
									</div>
                                <?php } ?>

								<div class="col-sm-2 equhours <?php echo 'equhours_'.$emp_loop; ?>"><label for="company_name" class="col-sm-4 show-on-mob control-label">Hours:</label>
									<input name="equ_hours[]" value="<?php echo $eh; ?>" type="text" class="equ_hours_val<?php echo $emp_loop; ?> form-control office_zip" />
								</div> <!-- Quantity -->
								<?php if($style == 1 ) { ?>
									<img src="img/cross.png" width="32" height="32" border="0" alt="">
								<?php } ?>

							</div>
						<?php } ?>

						<div class="additional_equ_edit clearfix hide_show_equ_edit">
							<div class="form-group">
								<div class="col-sm-3"><label for="company_name" class="col-sm-4 show-on-mob control-label">Equipment:</label>
									<select data-placeholder="Choose Equipment..."  name="equipmentid[]" id="<?php echo 'equid_'.($total_count+1); ?>"   class="chosen-select-deselect form-control equipmentid" width="380">
										<option value=""></option>
										<?php
											$query = mysqli_query($dbc,"SELECT * FROM equipment WHERE deleted=0 ORDER BY `unit_number`, `type`");
											while($row = mysqli_fetch_array($query)) {
												?>
												<option value='<?php echo  $row['equipmentid']; ?>' ><?php echo $row['unit_number'].': '.$row['type']; ?></option>
										  <?php  }
										?>
									</select>
								</div>
								<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Billing Rate:</label>
									<select data-placeholder="Choose a Billing Rate..." name="equ_billing_rate[]" id="<?php echo 'equrate_'.($total_count+1); ?>" class="chosen-select-deselect form-control office_zip" width="380">
									  <option value=""></option>
									  <option value="Daily">Daily</option>
									  <option value="Hourly">Hourly</option>
									</select>
								</div> <!-- Quantity -->
                                <?php
								if(vuaed_visible_function($dbc, 'field_job') == 1) { ?>
									<div class="<?= $approvals == 1 ? 'col-sm-2' : 'hidden' ?>">
										<input disabled id="<?php echo 'fillequrate_'.$total_line; ?>"  type="text" class="form-control" />
									</div>
                                <?php } ?>

								<div class="col-sm-2 equhours <?php $eq_hours_tl = 'equhours_'.($total_count+1); echo $eq_hours_tl; ?>" ><label for="company_name" class="col-sm-4 show-on-mob control-label">Hours:</label>
									<input name="equ_hours[]" type="text" class="form-control office_zip equ_hours_val<?php echo $total_line; ?>" />
								</div> <!-- Quantity -->
							</div>

						</div>

						<div id="add_here_new_equ_edit"></div>

						<div class="form-group triple-gapped clearfix">
							<div class="col-sm-offset-4 col-sm-8">
								<button id="add_row_equ_edit" class="btn brand-btn pull-left">Add More</button>
							</div>
						</div>

						<?php }
						?>
				</div>
			</div>
		<?php endif; ?>

		<?php if(strpos($edit_config, ',stock_') !== false): ?>
			<!-- Stock/Material -->
			<?php $id=1; ?>

			<div class="form-group">
				<label for="additional_note" class="col-sm-4 control-label"><h3>Stock/Material</h3></label>
				<div class="col-sm-8">
					<div class="form-group clearfix hide-titles-mob">
						<?php if(strpos($edit_config, ',stock_desc') !== false): ?>
							<label class="col-sm-3 text-center">Description</label>
						<?php endif; ?>
						<?php if(strpos($edit_config, ',stock_qty') !== false): ?>
							<label class="col-sm-2 text-center">Quantity</label>
						<?php endif; ?>
						<?php if(strpos($edit_config, ',stock_price') !== false): ?>
							<label class="col-sm-2 text-center">Unit Price</label>
						<?php endif; ?>
						<?php if(strpos($edit_config, ',stock_amount') !== false): ?>
							<label class="col-sm-2 text-center">Amount<br>(Mark Up 15%)</label>
						<?php endif; ?>
					</div>

					<?php
					if(empty($_GET['fsid'])) {
						?>

						<div class="enter_cost additional_stockmat clearfix">
							<div class="clearfix"></div>

							<div class="form-group clearfix">
								<?php if(strpos($edit_config, ',stock_desc') !== false): ?>
									<div class="col-sm-3"><label for="company_name" class="col-sm-4 show-on-mob control-label">Description:</label>
										<input name="stockmat_desc[]" type="text" id="stockmatdesc_0" class="form-control office_zip " />
									</div>
								<?php endif; ?>
								<?php if(strpos($edit_config, ',stock_qty') !== false): ?>
									<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
										<input name="stockmat_qty[]" type="text" onKeyUp="numericFilter(this); multiplyCost(this);" id="stockmatqty_0" class="form-control office_zip stockmat_qty" />
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config, ',stock_price') !== false): ?>
									<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Unit Price:</label>
										<input name="stockmat_up[]" type="text" onKeyUp="numericFilter(this); multiplyCost(this);" id="stockmatup_0" class="form-control office_zip " />
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config, ',stock_amount') !== false): ?>
									<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Amount<br>(Mark Up 15%):</label>
										<input name="stockmat_amount[]" type="text" id="stockmatamount_0" class="form-control office_zip " />
									</div> <!-- Quantity -->
								<?php endif; ?>
							</div>

						</div>

						<div id="add_here_new_stockmat"></div>

						<div class="form-group triple-gapped clearfix">
							<div class="col-sm-offset-4 col-sm-8">
								<button id="add_row_stockmat" class="btn brand-btn pull-left">Add More</button>
							</div>
						</div>

						<?php
							$id++;
					} else {

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
								<?php if(strpos($edit_config, ',stock_desc') !== false): ?>
									<div class="col-sm-3"><label for="company_name" class="col-sm-4 show-on-mob control-label">Description:</label>
										<input name="stockmat_desc[]" type="text" id="<?php echo 'stockmatdesc_'.$sm_loop; ?>" value='<?php echo  $smd; ?>' class="form-control office_zip stockmat_desc" />
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config, ',stock_qty') !== false): ?>
									<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
										<input name="stockmat_qty[]" type="text" id="<?php echo 'stockmatqty_'.$sm_loop; ?>" onKeyUp="numericFilter(this); multiplyCost(this);" value='<?php echo  $smq; ?>' class="form-control office_zip stockmat_qty" />
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config, ',stock_price') !== false): ?>
									<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Unit Price:</label>
										<input name="stockmat_up[]" type="text" id="<?php echo 'stockmatup_'.$sm_loop; ?>" onKeyUp="numericFilter(this); multiplyCost(this);" value='<?php echo  $smup; ?>' class="form-control office_zip " />
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config, ',stock_amount') !== false): ?>
									<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Amount<br>(Mark Up 15%):</label>
										<input name="stockmat_amount[]" type="text" id="<?php echo 'stockmatamount_'.$sm_loop; ?>" value='<?php echo  $sma; ?>' class="form-control office_zip " />
									</div> <!-- Quantity -->
								<?php endif; ?>
							</div>
						<?php } ?>

						<div class="enter_cost edit_stockmat hide_show_stockmat">
							<div class="clearfix"></div>

							<div class="form-group clearfix">
								<?php if(strpos($edit_config, ',stock_desc') !== false): ?>
									<div class="col-sm-3"><label for="company_name" class="col-sm-4 show-on-mob control-label">Description:</label>
										<input name="stockmat_desc[]" type="text" id="<?php echo 'stockmatdesc_'.($sm_loop); ?>" class="form-control office_zip stockmatdesc" />
									</div>
								<?php endif; ?>
								<?php if(strpos($edit_config, ',stock_qty') !== false): ?>
									<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
										<input name="stockmat_qty[]" onKeyUp="numericFilter(this); multiplyCost(this);" type="text" id="<?php echo 'stockmatqty_'.($sm_loop); ?>" class="form-control office_zip stockmat_qty stockmatqty" />
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config, ',stock_price') !== false): ?>
									<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Unit Price:</label>
										<input name="stockmat_up[]" onKeyUp="numericFilter(this); multiplyCost(this);" type="text" id="<?php echo 'stockmatup_'.($sm_loop); ?>" class="form-control office_zip stockmatup" />
									</div> <!-- Quantity -->
								<?php endif; ?>
								<?php if(strpos($edit_config, ',stock_amount') !== false): ?>
									<div class="col-sm-2"><label for="company_name" class="col-sm-4 show-on-mob control-label">Amount<br>(Mark Up 15%):</label>
										<input name="stockmat_amount[]" type="text" id="<?php echo 'stockmatamount_'.($sm_loop); ?>" class="form-control office_zip stockmatamount" />
									</div> <!-- Quantity -->
								<?php endif; ?>
							</div>

						</div>

						<div id="edit_here_new_stockmat"></div>

						<div class="form-group triple-gapped clearfix">
							<div class="col-sm-offset-4 col-sm-8">
								<button id="edit_row_stockmat" class="btn brand-btn pull-left">Add More</button>
							</div>
						</div>

						<?php }
						?>
				</div>
			</div>
		<?php endif; ?>


		<?php
			if($no_ratecard == 1) {
				echo "<h3>Below position(s) don't have a Regular Rate. Please update the rate card and then you will be able to approve/reject this Foreman sheet.<br>";
				echo str_replace(',', '<br />', $no_rate_position);

				echo '</h3>';
			}
		?>

		<?php if(strpos($edit_config,',comments,') !== false): ?>
			<h2>Comments</h2>
			<div class="form-group">
				<label for="additional_note" class="col-sm-4 control-label"></label>
				<div class="col-sm-8">
					<textarea name="comment" rows="5" cols="50" class="form-control"></textarea>
				</div>
			</div>
			  <div class="form-group">
				<div class="col-sm-4 clearfix">
					<a href="field_foreman_sheet.php" class="btn brand-btn pull-right">Back</a>
					<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-8">
					<?php
					if($approvals == 0) {
						if(empty($_GET['fsid'])) { ?>
							<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	save_but pull-right">Save</button>
						<?php } else { ?>
							<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg save_but">Save</button>
							<button	type="submit" name="submit"	value="submit_approval" class="btn brand-btn btn-lg	save_but pull-right">Submit for Approval</button>
							<div class="form-group">
								<label class="col-sm-4">Submitter's Name</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="submit_name" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4">Submitter's Email</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="submit_name" value="<?= get_contact($dbc, $_SESSION['contactid'],'email_address') ?>">
								</div>
							</div>
						<?php }
					} else if(vuaed_visible_function($dbc, 'field_job') == 1) { ?>
						<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg save_but">Save</button>
						<button	type="submit" name="submit"	value="submit_approval" class="btn brand-btn btn-lg	save_but pull-right">Submit for Approval</button>
						<div class="form-group">
							<label class="col-sm-4">Submitter's Name</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="submit_name" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4">Submitter's Email</label>
							<div class="col-sm-8">
								<input type="text" class="form-control" name="submit_name" value="<?= get_contact($dbc, $_SESSION['contactid'],'email_address') ?>">
							</div>
						</div>
					<?php } ?>
				</div>
			  </div>

			<?php

			if(!empty($_GET['fsid'])) {

				$fsid = $_GET['fsid'];
				$result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT comment, comment_by FROM field_foreman_sheet WHERE fsid = '$fsid'"));
				$comment = explode('##',$result['comment']);
				$comment_by = explode('##',$result['comment_by']);
				$total_count = mb_substr_count($result['comment'],'##');
				for($emp_loop=$total_count; $emp_loop>0; $emp_loop--) {
					if($comment[$emp_loop] != '') {
						$contactid = $comment_by[$emp_loop];
						echo '<br>By : '.get_staff($dbc, $contactid).'<br>
						Comment : '.html_entity_decode($comment[$emp_loop]).'<br><br>';
					}
				}

				echo '<input type="hidden" name="fsid" value="'.$_GET['fsid'].'">';

				if(!empty($_GET['who'])) {
					echo '<input type="hidden" name="who" value="'.$_GET['who'].'">';
					echo '<input type="hidden" name="status" value="'.$_GET['status'].'">';
				}

			}
			?>
		<?php endif; ?>

		<?php
		if((!empty($_GET['fsid'])) && ($no_ratecard == 0)) {

			if($approvals == 1) {
				if($supervisor_status != 'Approved') {
					echo '<h3>Supervisor Process</h3>

					<button	type="submit" name="submit_status"	value="supervisor_status-Approved" class="btn brand-btn">Approve</button>';

					//echo '<a href=\'add_field_foreman_sheet.php?who=supervisor_status&status=Approved&fsid='.$fsid.'&jobid='.$jobid.'\'>Approve</a>';

					if($supervisor_status != 'Rejected') {
						//echo ' | <a href=\'add_field_foreman_sheet.php?who=supervisor_status&status=Rejected&fsid='.$fsid.'&jobid='.$jobid.'\'>Reject</a>';

						echo ' || <button	type="submit" name="submit_status"	value="supervisor_status-Rejected" class="btn brand-btn">Decline</button>';
					}
				}
			}
			if($approvals == 1) {
				//if(($office_status != 'Approved') && ($supervisor_status == 'Approved')) {
                if(($office_status != 'Approved')) {
					echo '<h3>Office Admin Process</h3>';

					//echo '<a class="btn brand-btn" href=\'add_field_foreman_sheet.php?who=office_status&status=Approved&fsid='.$fsid.'&jobid='.$jobid.'\'>Approve</a>';

					if($get_job['invoice'] != 'Flat Rate') {
						echo '<button	type="submit" name="submit_status"	value="office_status-Approved" class="btn brand-btn">Approve</button> || ';
					}
					echo '<button	type="submit" name="submit_status"	value="office_status-Flat_Rate" class="btn brand-btn">Approve Without Work Ticket</button>';

					if($office_status != 'Rejected') {
						echo ' || <button	type="submit" name="submit_status"	value="office_status-Rejected" class="btn brand-btn">Decline</button>';

						//echo ' | <a class="btn brand-btn" href=\'add_field_foreman_sheet.php?who=office_status&status=Rejected&fsid='.$fsid.'&jobid='.$jobid.'\'>Reject</a>';
					}
				}
			}
		}
		?>

		</form>

	</div>
  </div>
</div>

<?php include ('../footer.php'); ?>
