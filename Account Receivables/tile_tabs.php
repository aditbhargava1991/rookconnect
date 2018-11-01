<?php $tab_list = explode(',', get_config($dbc, 'invoice_tabs'));
$ux_options = explode(',',get_config($dbc, FOLDER_NAME.'_ux'));
$purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
$purchaser_label = count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0];
$payer_config = explode(',',get_config($dbc, 'invoice_payer_contact'));
$payer_label = count($payer_config) > 1 ? 'Third Party' : $payer_config[0]; ?>
<div class='gap-top tab-container mobile-100-container double-gap-bottom'>
    <div class="tab-container"><?php
        if ( check_subtab_persmission( $dbc, 'accounts_receivables', ROLE, 'insurer_ar' ) === true ) { ?>
            <a href="insurer_account_receivables.php"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/insurer_account_receivables.php') !== FALSE ? 'active_tab' : '' ?>">Insurer A/R</button></a><?php
        } else { ?>
            <button type="button" class="btn disabled-btn mobile-block mobile-100">Insurer A/R</button><?php
        }
        
        if ( check_subtab_persmission( $dbc, 'accounts_receivables', ROLE, 'patient_ar' ) === true ) { ?>
            <a href="patient_account_receivables.php"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/patient_account_receivables.php') !== FALSE ? 'active_tab' : '' ?>">Patient A/R</button></a><?php
        } else { ?>
            <button type="button" class="btn disabled-btn mobile-block mobile-100">Patient A/R</button><?php
        }
        
        if ( check_subtab_persmission( $dbc, 'accounts_receivables', ROLE, 'ui_invoice_report' ) === true ) { ?>
            <a href="ui_invoice_reports.php"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/ui_invoice_reports.php') !== FALSE ? 'active_tab' : '' ?>">UI Reports</button></a><?php
        } else { ?>
            <button type="button" class="btn disabled-btn mobile-block mobile-100">UI Reports</button><?php
        }
        
        if ( check_subtab_persmission( $dbc, 'accounts_receivables', ROLE, 'insurer_ar_report' ) === true ) { ?>
            <a href="insurer_account_receivables_report.php"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/insurer_account_receivables_report.php') !== FALSE ? 'active_tab' : '' ?>">Insurer Paid A/R Report</button></a><?php
        } else { ?>
            <button type="button" class="btn disabled-btn mobile-block mobile-100">Insurer Paid A/R Report</button><?php
        }
        
        if ( check_subtab_persmission( $dbc, 'accounts_receivables', ROLE, 'insurer_ar_cm' ) === true ) { ?>
            <a href="insurer_account_receivables_cm.php"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= strpos($_SERVER['PHP_SELF'],'/insurer_account_receivables_cm.php') !== FALSE ? 'active_tab' : '' ?>">Insurer A/R Clinic Master</button></a><?php
        } else { ?>
            <button type="button" class="btn disabled-btn mobile-block mobile-100">Insurer A/R Clinic Master</button><?php
        } ?>
    </div>
</div>