<?php
include_once('../tcpdf/tcpdf.php');
include_once('../include.php');

$invoiceid = $_GET['invoiceid'];

$invoice_design = get_config($dbc, 'invoice_design');
switch($invoice_design) {
	case 1:
		include('../Invoice/pos_invoice_1.php');
		header('Location: ../Point of Sale/download/invoice_'.$posid.$edited.'.pdf');
		break;
	case 2:
		include('../Invoice/pos_invoice_2.php');
		header('Location: Download/invoice_'.$invoiceid.'.pdf');
		break;
	case 3:
		include('../Invoice/pos_invoice_3.php');
		header('Location: ../Point of Sale/download/invoice_'.$posid.$edited.'.pdf');
		break;
	case 4:
		$_GET['action'] = 'build';
		include ('../Invoice/patient_invoice_pdf.php');
		header('Location: '.WEBSITE_URL.'/Invoice/download/invoice_'.$invoiceid.'.pdf');
		break;
	case 5:
        include('../Invoice/pos_invoice_small.php');
        header('Location: download/invoice_'.$invoiceid.'.pdf');
		break;
	case 'service':
        include('../Invoice/pos_invoice_service.php');
        echo "<script>window.open('download/invoice_".$invoiceid.".pdf');</script>";
		break;
} ?>
