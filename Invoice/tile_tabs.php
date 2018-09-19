<?php $tab_list = explode(',', get_config($dbc, 'invoice_tabs'));
$ux_options = explode(',',get_config($dbc, FOLDER_NAME.'_ux'));
$purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
$purchaser_label = count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0];
$payer_config = explode(',',get_config($dbc, 'invoice_payer_contact'));
$payer_label = count($payer_config) > 1 ? 'Third Party' : $payer_config[0]; ?>
<div class='gap-top tab-container mobile-100-container double-gap-bottom'>
<?php foreach($tab_list as $tab_name) {
	if(check_subtab_persmission($dbc, FOLDER_NAME == 'invoice' ? 'check_out' : 'posadvanced', ROLE, $tab_name) === TRUE) {
		switch($tab_name) {
			case 'checkin': ?>
				<a href='checkin.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/checkin.php') !== FALSE ? 'active_tab' : '' ?>">Check In</span></a>
				<?php break;
			case 'sell':
				if(in_array('touch',$ux_options)) { ?>
					<a href='create_invoice.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/create_invoice.php') !== FALSE ? 'active_tab' : '' ?>">Create Invoice (Keyboard)</span></a>
					<a href='touch_main.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/touch_main.php') !== FALSE ? 'active_tab' : '' ?>">Create Invoice (Touchscreen)</span></a>
				<?php } else { ?>
					<a href='create_invoice.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/create_invoice.php') !== FALSE ? 'active_tab' : '' ?>">Create Invoice</span></a>
				<?php }
				break;
			case 'today': ?>
				<span class="popover-examples list-inline">
					<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Invoices created today."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></span></a>
				</span>
				<a href='today_invoice.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/today_invoice.php') !== FALSE ? 'active_tab' : '' ?>">Today's Summary</span></a>
				<?php break;
			case 'all': ?>
				<span class="popover-examples list-inline">
					<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Complete history of all Invoices."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
				</span>
				<a href='invoice_list.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/invoice_list.php') !== FALSE ? 'active_tab' : '' ?>">All Invoices</span></a>
                <!-- <a href='all_invoice.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/all_invoice.php') !== FALSE ? 'active_tab' : '' ?>">All Invoices</span></a> -->
				<?php break;
			//case 'invoices': ?>
				<!-- <a href='invoice_list.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/invoice_list.php') !== FALSE ? 'active_tab' : '' ?>">Invoices</span></a> -->
				<?php //break;
			case 'unbilled_tickets': ?>
				<a href='unbilled_tickets.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/unbilled_tickets.php') !== FALSE ? 'active_tab' : '' ?>">Unbilled <?= TICKET_TILE ?></span></a>
				<?php break;
			case 'unpaid': ?>
				<a href='unpaid_invoice_list.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/unpaid_invoice_list.php') !== FALSE ? 'active_tab' : '' ?>">Accounts Receivable</span></a>
				<?php break;
			case 'contact_ar': ?>
				<a href='patient_account_receivables.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/patient_account_receivables.php') !== FALSE ? 'active_tab' : '' ?>"><?= $purchaser_label ?> A/R</span></a>
				<?php break;
			case 'third_party_ar': ?>
				<a href='insurer_account_receivables.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/insurer_account_receivables.php') !== FALSE ? 'active_tab' : '' ?>"><?= $payer_label ?> A/R</span></a>
				<?php break;
			case 'unpaid_third_party': ?>
				<a href='ui_invoice_reports.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/ui_invoice_reports.php') !== FALSE ? 'active_tab' : '' ?>">U<?= $payer_label[0] ?> Reports</span></a>
				<?php break;
			case 'paid_contact_ar': /*?>
				<a href='unpaid_invoice_list.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/unpaid_invoice_list.php') !== FALSE ? 'active_tab' : '' ?>"><?= $purchaser_label ?> Paid A/R Report</span></a>
				<?php*/ break;
			case 'paid_third_party_ar': ?>
				<a href='insurer_account_receivables_report.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/insurer_account_receivables_report.php') !== FALSE ? 'active_tab' : '' ?>"><?= $payer_label ?> Paid A/R Report</span></a>
				<?php break;
			case 'clinic_master': ?>
				<span class="popover-examples list-inline">
					<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Old data that was not transferable from Clinic Master to Clinic Ace."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
				</span>
				<a href='insurer_account_receivables_cm.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/insurer_account_receivables_cm.php') !== FALSE ? 'active_tab' : '' ?>">Clinic Master A/R Report</span></a>
				<?php break;
			case 'voided': ?>
				<a href='void_invoices.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/void_invoices.php') !== FALSE ? 'active_tab' : '' ?>">Voided / Credit Memo</span></a>
				<?php break;
			case 'refunds': ?>
				<span class="popover-examples list-inline">
					<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Find invoices in order to issue Refunds or Create Adjustment Invoices."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
				</span>
				<a href='refund_invoices.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/refund_invoices.php') !== FALSE ? 'active_tab' : '' ?>">Refund / Adjustments</span></a>
				<?php break;
			case 'ui_report': ?>
				<span class="popover-examples list-inline">
					<a href="#job_file" data-toggle="tooltip" data-placement="top" title="In this section you can create Invoices for <?= $payer_label ?>."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
				</span>
				<a href='unpaid_insurer_invoice.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/unpaid_insurer_invoice.php') !== FALSE ? 'active_tab' : '' ?>">Unpaid <?= $payer_label ?> Invoice Report</span></a>
				<?php break;
			case 'cashout': ?>
				<span class="popover-examples list-inline">
					<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Daily front desk Cashout."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
				</span>
				<a href='cashout.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/cashout.php') !== FALSE ? 'active_tab' : '' ?>">Cash Out</span></a>
				<?php break;
			case 'gf': ?>
				<a href='giftcards.php'><span class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/giftcards.php') !== FALSE ? 'active_tab' : '' ?>">Gift Card</span></a>
				<?php break;
		}
	}
} ?>
</div>