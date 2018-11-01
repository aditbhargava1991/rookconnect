<?php
/*
 * Sidebar
 * Included From: index.php
 * JS on index.php
 */
?>

<?php
    // Move Today's Invoices to the top
    $tab_list = array_diff($tab_list, array('today'));
    array_unshift($tab_list, 'today');
?>

<!-- Mobile -->
<div id="mobile_accordions" class="sidebar show-on-mob panel-group block-panels col-xs-12 form-horizontal"><?php
    foreach($tab_list as $tab_name) {
        if ( check_subtab_persmission($dbc, FOLDER_NAME == 'invoice' ? 'check_out' : 'posadvanced', ROLE, $tab_name) === true ) {
            switch($tab_name) {
                case 'checkin': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_checkin">
                                    Check In<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_checkin" class="panel-collapse collapse">
                            <div class="panel-body" data-url="checkin.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'today': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_today">
                                    Today's Summary<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_today" class="panel-collapse collapse">
                            <div class="panel-body" data-url="index.php?tab=today">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'all': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_all">
                                    All Invoices<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_all" class="panel-collapse collapse">
                            <div class="panel-body" data-url="invoice_list.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'unbilled_tickets': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_unbilled_tickets">
                                    Unbilled <?= TICKET_TILE ?><span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_unbilled_tickets" class="panel-collapse collapse">
                            <div class="panel-body" data-url="unbilled_tickets.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'unpaid': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_unpaid">
                                    Accounts Receivable<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_unpaid" class="panel-collapse collapse">
                            <div class="panel-body" data-url="unpaid_invoice_list.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'contact_ar': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_contact_ar">
                                    <?= $purchaser_label ?> A/R<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_contact_ar" class="panel-collapse collapse">
                            <div class="panel-body" data-url="patient_account_receivables.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'third_party_ar': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_third_party_ar">
                                    <?= $payer_label ?> A/R<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_third_party_ar" class="panel-collapse collapse">
                            <div class="panel-body" data-url="insurer_account_receivables.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'unpaid_third_party': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_unpaid_third_party">
                                    Unpaid <?= $payer_label ?> Invoice Report<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_unpaid_third_party" class="panel-collapse collapse">
                            <div class="panel-body" data-url="unpaid_insurer_invoice.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'paid_third_party_ar': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_paid_third_party_ar">
                                    <?= $payer_label ?> Paid A/R Report<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_paid_third_party_ar" class="panel-collapse collapse">
                            <div class="panel-body" data-url="insurer_account_receivables_report.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'clinic_master': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_clinic_master">
                                    Clinic Master A/R Report<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_clinic_master" class="panel-collapse collapse">
                            <div class="panel-body" data-url="insurer_account_receivables_cm.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'voided': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_voided">
                                    Voided / Credit Memo<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_voided" class="panel-collapse collapse">
                            <div class="panel-body" data-url="void_invoices.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'refunds': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_refunds">
                                    Refund / Adjustments<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_refunds" class="panel-collapse collapse">
                            <div class="panel-body" data-url="refund_invoices.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'ui_report': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_ui_report">
                                    U<?= $payer_label[0] ?> Reports<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_ui_report" class="panel-collapse collapse">
                            <div class="panel-body" data-url="ui_invoice_reports.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'cashout': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_cashout">
                                    Cashout<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_cashout" class="panel-collapse collapse">
                            <div class="panel-body" data-url="cashout.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;

                case 'gf': ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_accordions" href="#collapse_gf">
                                    Gift Cards<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_gf" class="panel-collapse collapse">
                            <div class="panel-body" data-url="giftcards.php">
                                Loading...
                            </div>
                        </div>
                    </div><?php
                    break;
            }
        }
    } ?>
</div><!-- #mobile_accordions -->

<!-- Desktop -->
<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
    <ul id="newsboard_desktop" class="panel-group">
        <!-- <li class="standard-sidebar-searchbox"><input type="text" name="search" class="search-text form-control" placeholder="Search <?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?>" /></li> --><?php
        foreach($tab_list as $tab_name) {
            if ( check_subtab_persmission($dbc, FOLDER_NAME == 'invoice' ? 'check_out' : 'posadvanced', ROLE, $tab_name) === true ) {
                switch($tab_name) {
                    case 'checkin': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'checkin' ? 'active' : '' ?>"><a href="index.php?tab=checkin">Check In</a></li><?php
                        break;
                    /*

                    */

                    case 'today': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'today' || $_GET['tab'] == '' ? 'active' : '' ?>"><a href="index.php?tab=today">Today's Summary</a></li><?php
                        break;
                    case 'all': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'all' ? 'active' : '' ?>"><a href="index.php?tab=all">All Invoices</a></li><?php
                        break;
                    case 'unbilled_tickets': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'unbilled_tickets' ? 'active' : '' ?>"><a href="index.php?tab=unbilled_tickets">Unbilled <?= TICKET_TILE ?></a></li><?php
                        break;
                    case 'unpaid': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'unpaid' ? 'active' : '' ?>"><a href="index.php?tab=unpaid">Accounts Receivable</a></li><?php
                        break;
                    case 'contact_ar': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'contact_ar' ? 'active' : '' ?>"><a href="index.php?tab=contact_ar"><?= $purchaser_label ?> A/R</a></li><?php
                        break;
                    case 'third_party_ar': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'third_party_ar' ? 'active' : '' ?>"><a href="index.php?tab=third_party_ar"><?= $payer_label ?> A/R</a></li><?php
                        break;
                    case 'unpaid_third_party': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'unpaid_third_party' ? 'active' : '' ?>"><a href="index.php?tab=unpaid_third_party">U<?= $payer_label[0] ?> Reports</a></li><?php
                        break;
                    case 'paid_third_party_ar': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'paid_third_party_ar' ? 'active' : '' ?>"><a href="index.php?tab=paid_third_party_ar"><?= $payer_label ?> Paid A/R Report</a></li><?php
                        break;
                    case 'clinic_master': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'clinic_master' ? 'active' : '' ?>"><a href="index.php?tab=clinic_master">Clinic Master A/R Report</a></li><?php
                        break;
                    case 'voided': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'voided' ? 'active' : '' ?>"><a href="index.php?tab=voided">Voided / Credit Memo</a></li><?php
                        break;
                    case 'refunds': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'refunds' ? 'active' : '' ?>"><a href="index.php?tab=refunds">Refund / Adjustments</a></li><?php
                        break;
                    case 'ui_report': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'ui_report' ? 'active' : '' ?>"><a href="index.php?tab=ui_report">Unpaid <?= $payer_label ?> Invoice Report</a></li><?php
                        break;
                    case 'cashout': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'cashout' ? 'active' : '' ?>"><a href="index.php?tab=cashout">Cash Out</a></li><?php
                        break;
                    case 'gf': ?>
                        <li class="sidebar-higher-level <?= $_GET['tab'] == 'gf' ? 'active' : '' ?>"><a href="index.php?tab=gf">Gift Card</a></li><?php
                        break;
                }
            }
        } ?>
    </ul>
</div><!-- .sidebar -->