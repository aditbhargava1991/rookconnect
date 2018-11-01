<?php include('../include.php');
$redirected = false;
foreach(['insurer_ar','patient_ar','ui_invoice_report','insurer_ar_report','insurer_ar_cm'] as $tab_name) {
	if(!$redirected && check_subtab_persmission($dbc, 'accounts_receivables', $_SESSION['role'], $tab_name)) {
		switch($tab_name) {
            case 'insurer_ar': header('Location: insurer_account_receivables.php');
				$redirected = true;
				break;
			case 'patient_ar': header('Location: patient_account_receivables.php');
				$redirected = true;
				break;
			case 'ui_invoice_report': header('Location: ui_invoice_reports.php');
				$redirected = true;
				break;
			case 'insurer_ar_report': header('Location: insurer_account_receivables_report.php');
				$redirected = true;
				break;
			case 'insurer_ar_cm': header('Location: insurer_account_receivables_cm.php');
				$redirected = true;
				break;
		}
	}
}