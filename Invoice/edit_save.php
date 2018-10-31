<?php if (!empty($_GET['type']) && $_GET['invoiceid'] > 0) {
    mysqli_query($dbc, "UPDATE `invoice` SET `type` = '".$invoice_type."' WHERE `invoiceid` = '".$_GET['invoiceid']."'");
}

if (isset($_POST['save_btn'])) {
	$invoice_mode = 'Saved';
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    include('add_update_invoice.php');
    echo '<script type="text/javascript"> alert("Invoice Successfully Saved"); window.location.replace("index.php"); </script>';
}

if (isset($_POST['submit_btn'])) {
    include_once('../tcpdf/tcpdf.php');
	$invoice_mode = filter_var($_POST['submit_btn'],FILTER_SANITIZE_STRING);
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    include('add_update_invoice.php');
	$type = implode(',', $_POST['payment_type']);

    if($bookingid != 0) {
        $appoint_date = get_patient_from_booking($dbc, $bookingid, 'appoint_date');
        $service_date = explode(' ', $appoint_date);
        $final_service_date = $service_date[0];
    } else {
        $final_service_date = $today_date;
    }

    $ins_pay = 0;
    for($i=0; $i<count($_POST['insurerid']); $i++) {
        $ins_pay += $_POST['insurance_payment'][$i];
    }

    $get_invoice = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));
    $patientid = $get_invoice['patientid'];
    $therapistsid = $get_invoice['therapistsid'];
    $injuryid = $get_invoice['injuryid'];

    $patients = get_contact($dbc, $patientid);
    $staff = get_contact($dbc, $therapistsid);

    include('add_update_invoice_inventory.php');

    if($_POST['next_appointment'] == 'Yes') {
        include('invoice_booking.php');
    }

	// PDF
	$invoice_design = get_config($dbc, 'invoice_design');

    if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_design_'.$get_invoice['type']))) {
        $invoice_design = get_config($dbc, 'invoice_design_'.$get_invoice['type']);
    }

	switch($invoice_design) {
		case 2:
			include('pos_invoice_2.php');
			break;
		case 3:
			include('pos_invoice_3.php');
			break;
		case 4:
			include ('patient_invoice_pdf.php');
			if($insurerid != '') {
				include ('insurer_invoice_pdf.php');
			}
			break;
		case 5:
            include('pos_invoice_small.php');
			break;
		case 'service':
            include('pos_invoice_service.php');
			break;
		case 'pink':
			include ('pos_invoice_pink.php');
			break;
		case 'cnt1':
			include ('pos_invoice_contractor_1.php');
			break;
		case 'cnt2':
			include ('pos_invoice_contractor_2.php');
			break;
		case 'cnt3':
			include ('pos_invoice_contractor_3.php');
			break;
        case 'custom_ticket':
            include ('pos_invoice_custom_ticket.php');
            break;
		case 1:
        default:
			include('pos_invoice_1.php');
			break;
	}

    if($_POST['survey'] != '') {
        include ('send_survey.php');
    }

    if($_POST['follow_up_assessment_email'] != '') {
        include ('send_follow_up_email.php');
    }

    $invoicefrom = $_POST['invoicefrom'];
    $search_user = $_POST['search_user'];
    $search_invoice = $_POST['search_invoice'];
    
    $email_msg = [];
    if($_POST['email_invoice'] == 'yes' && !empty($_POST['invoice_email_address']) && file_exists('download/invoice_'.$invoiceid.'.pdf')) {
        $subject = '';
        if(!empty($get_invoice['type'])) {
            $subject = get_config($dbc, 'invoice_email_subject_'.config_safe_str($get_invoice['type']));
        }
        if(empty($subject)) {
            $subject = get_config($dbc, 'invoice_email_subject');
        }
        $subject = str_replace(['[CUSTOMER]'],[get_contact($dbc, $get_invoice['patientid'])], $subject);
        $body = '';
        if(!empty($get_invoice['type'])) {
            $body = get_config($dbc, 'invoice_email_body_'.config_safe_str($get_invoice['type']));
        }
        if(empty($body)) {
            $body = get_config($dbc, 'invoice_email_body');
        }
        $body = str_replace(['[CUSTOMER]'],[get_contact($dbc, $get_invoice['patientid'])], $body);
        try {
            send_email('',[$_POST['invoice_email_address']=>get_contact($dbc, $get_invoice['patientid'])],'','',$subject,$body,'download/invoice_'.$invoiceid.'.pdf');
            $email_msg[] = 'Invoice successfully emailed to '.$_POST['invoice_email_address'];
        } catch (Exception $e) {
            $email_msg[] = 'Unable to Send Invoice: '.$e->getMessage();
        }
    } else if($_POST['email_invoice'] == 'yes') {
        $email_msg[] = 'No Email Address Provided: Unable to Send Invoice.';
    }

    if($invoicefrom == 'report') {
        $invoicefrom_start = $_POST['invoicefrom_start'];
        $invoicefrom_end = $_POST['invoicefrom_end'];
        echo '<script type="text/javascript"> alert("Invoice Updated."); window.location.replace("../Reports/report_unassigned_invoices.php?type=Daily&start='.$invoicefrom_start.'&end='.$invoicefrom_end.'");
        </script>';
    } else if($invoicefrom == 'calendar') {
        echo '<script type="text/javascript"> window.top.close(); window.opener.location.reload(); </script>';
    } else {
        if($search_user != '') {
            echo '<script type="text/javascript"> alert("Invoice Updated."); window.location.replace("index.php?tab=all&search_user='.$search_user.'");</script>';
        } else if($search_invoice != '') {
            echo '<script type="text/javascript"> alert("Invoice Updated."); window.location.replace("index.php?tab=all&search_invoice='.$search_invoice.'");</script>';
        } else {
            echo '<script type="text/javascript"> alert("'.implode('\n',$email_msg).(!empty($email_msg) ? '\n' : '').'Invoice Generated."); window.location.replace("index.php");
            window.open("download/invoice_'.$invoiceid.'.pdf", "fullscreen=yes");
            </script>';
        }
    }

    mysqli_close($dbc); //Close the DB Connection
}

if (isset($_POST['submit_pay'])) {

		$all_invoiceid = $_POST['invoiceid'];
		$from = $_POST['from'];

        $type = implode(',', $_POST['payment_type']);
        $payment_price = implode(',', $_POST['payment_price']);
        $payment_type = $type.'#*#'.$payment_price;

        $payment_type = !empty($payment_type) ? "'$payment_type'" : "NULL";

		$var=explode(',',$all_invoiceid);
	    foreach($var as $invoiceid) {

            $bookingid = get_all_from_invoice($dbc, $invoiceid, 'bookingid');
            $follow_up_call_status = 'Paid';
            $query_update_booking = "UPDATE `booking` SET `follow_up_call_status` = '$follow_up_call_status' WHERE `bookingid` = '$bookingid'";
            $result_update_booking = mysqli_query($dbc, $query_update_booking);

            $calid = get_calid_from_bookingid($dbc, $bookingid);
            $query_update_cal = "UPDATE `mrbs_entry` SET `patientstatus` = '$follow_up_call_status' WHERE `id` = '$calid'";
            $result_update_cal = mysqli_query($dbc, $query_update_cal);

			$query_invoice = "UPDATE `invoice` SET `payment_type` = $payment_type, `paid` = 'Yes' WHERE `invoiceid` = '$invoiceid'";
			$result_invoice = mysqli_query($dbc, $query_invoice);

			$invoice = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT patientid, fee, sell_price,total_price FROM invoice WHERE invoiceid='$invoiceid'"));
			$patientid = $invoice['patientid'];
			$total_price = $invoice['total_price'];

			$query_update_patient = "UPDATE `patients` SET `account_balance` = account_balance - '$total_price' WHERE `patientid` = '$patientid'";
			$result_update_patient = mysqli_query($dbc, $query_update_patient);
		}
		if ($from == 'patient') {
            echo '<script type="text/javascript"> alert("Invoice Successfully Paid."); window.location.replace("index.php?tab=today&patientid='.$patientid.'"); </script>';
		} else {
            echo '<script type="text/javascript"> alert("Invoice Successfully Paid."); window.location.replace("index.php"); </script>';
		}
}