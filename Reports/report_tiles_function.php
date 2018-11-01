<?php
/*
 * List all function here.
 * Update Reports switch-case in root/tile_data.php whenever this page updates
 */
function reports_tiles($dbc) {
    include('../Reports/field_list.php'); ?>
    <script>
    $(document).ready(function() {
        $('[name=select_report]').off('change').change(function() {
            if(this.value != '') {
                window.location.replace(this.value);
            }
        });
        $('[name=printpdf]').click(function() {
            setTimeout(function (){
                $('[name=printpdf]').closest('form').removeAttr('target');
            },500);
        });
    });

    function printPDF() {
        $('[name=printpdf]').closest('form').prop('target', '_blank');
    }
    function email_doc(report){
        var url = window.location.href;
        var subject = $(report).data('title');
        var body = '<a href="'+encodeURIComponent(url)+'" target="_blank">View Report</a>'
        //overlayIFrameSlider('../Email Communication/add_email.php?type=external&subject='+subject+'&body='+body, 'auto', false, true);
        overlayIFrameSlider('../quick_action_email.php?subject='+subject+'&body='+body, 'auto', true, true);
    }
    </script>
    <div class="main-screen">
        <div class="tile-header standard-header">
            <div class="pull-right settings-block">
                <?php if(config_visible_function($dbc, 'report') == 1) {
                    echo '<a href="field_config.php" class="mobile-block pull-right "><img style="width: 30px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
                } ?>
            </div>
            <div class="scale-to-fill">
                <h1 class="gap-left"><a href="report_tiles.php">Reports</a></h1>
            </div>
            <div class="clearfix"></div>
        </div>

        <?php $value_config = ','.get_config($dbc, 'reports_dashboard').',';
        $report_tabs = !empty(get_config($dbc, 'report_tabs')) ? get_config($dbc, 'report_tabs') : 'operations,sales,ar,marketing,compensation,pnl,customer,staff';
        $report_tabs = explode(',', $report_tabs);
        if(empty($_GET['type'])) {
            $_GET['type'] = $report_tabs[0];
        } ?>

        <!-- Mobile View -->
        <div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" id="mobile_tabs">
            <?php if(in_array('operations',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_operations">
                                Operations<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_operations" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=operations&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('sales',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_sales">
                                Sales<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_sales" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=sales&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('ar',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_ar">
                                Accounts Receivable<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_ar" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=ar&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('marketing',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_marketing">
                                Marketing<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_marketing" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=marketing&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('compensation',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_compensation">
                                Compensation<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_compensation" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=compensation&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('pnl',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_pnl">
                                Profit & Loss<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_pnl" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=pnl&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('customer',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_customer">
                                Customer<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_customer" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=customer&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('staff',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_staff">
                                Staff<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_staff" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=staff&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('history',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_staff">
                                History<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_staff" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=history&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
            <?php if(in_array('estimates',$report_tabs)) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading mobile_load">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_staff1">
                                Estimates<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_staff1" class="panel-collapse collapse">
                        <div class="panel-body" data-file-name="report_tiles.php?type=estimates&mobile_view=true">
                            Loading...
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

    <?php $reports_dashboard_navigation = explode(',', get_config($dbc, 'reports_dashboard_navigation')); ?>
    <?php if(in_array('show', $reports_dashboard_navigation) || empty(array_filter($reports_dashboard_navigation))): ?>
        <!-- Desktop View -->
    	<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
            <ul>
                <li class="standard-sidebar-searchbox"><input type="text" class="search-text form-control" placeholder="Search Reports" /></li>
                <?php if(in_array('ar',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=ar"><li <?= $_GET['type']=='ar' ? 'class="active"' : '' ?>>Accounts Receivable</li></a>
                <?php } ?>
                <?php if(in_array('compensation',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=compensation"><li <?= $_GET['type']=='compensation' ? 'class="active"' : '' ?>>Compensation</li></a>
                <?php } ?>
                <?php if(in_array('customer',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=customer"><li <?= $_GET['type']=='customer' ? 'class="active"' : '' ?>>Customer</li></a>
                <?php } ?>
                <?php if(in_array('estimates',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=estimates"><li <?= $_GET['type']=='estimates' ? 'class="active"' : '' ?>>Estimates</li></a>
                <?php } ?>
                <?php if(in_array('history',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=history"><li <?= $_GET['type']=='history' ? 'class="active"' : '' ?>>History</li></a>
                <?php } ?>
                <?php if(in_array('marketing',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=marketing"><li <?= $_GET['type']=='marketing' ? 'class="active"' : '' ?>>Marketing</li></a>
                <?php } ?>
                <?php if(in_array('operations',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=operations"><li <?= $_GET['type']=='operations' || empty($_GET['type']) ? 'class="active"' : '' ?>>Operations</li></a>
                <?php } ?>
                <?php if(in_array('pnl',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=pnl"><li <?= $_GET['type']=='pnl' ? 'class="active"' : '' ?>>Profit &amp; Loss</li></a>
                <?php } ?>
                <?php if(in_array('sales',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=sales"><li <?= $_GET['type']=='sales' ? 'class="active"' : '' ?>>Sales</li></a>
                <?php } ?>
                <?php if(in_array('staff',$report_tabs)) { ?>
                    <a href="report_tiles.php?type=staff"><li <?= $_GET['type']=='staff' ? 'class="active"' : '' ?>>Staff</li></a>
                <?php } ?>

            </ul>
    	</div>
    <?php endif; ?>

        <div class="scale-to-fill has-main-screen hide-titles-mob">
            <div class="main-screen standard-body form-horizontal">
                <div class="standard-body-title">
                    <?php $title = '';
                    switch($_GET['type']) {
                        case 'sales':
                            $title = 'Sales';
                            break;
                        case 'ar':
                            $title = 'Accounts Receivables';
                            break;
                        case 'marketing':
                            $title = 'Marketing';
                            break;
                        case 'compensation':
                            $title = 'Compensation';
                            break;
                        case 'pnl':
                            $title = 'Profit & Loss';
                            break;
                        case 'customer':
                            $title = 'Customer';
                            break;
                        case 'staff':
                            $title = 'Staff';
                            break;
                        case 'history':
                            $title = 'History';
                            break;
                        case 'estimates':
                            $title = 'Estimates';
                            break;
                        case 'search':
                            $title = 'Search Results '. ( !empty($_GET['q']) ? 'for "'. $_GET['q'] .'"' : '' );
                            break;
                        case 'operations':
                        default:
                            $title = 'Operations';
                            break;
                    }
                    if(!empty($_GET['report'])) {
                        $title .= ': '.$report_list[$_GET['report']][1];
                    } ?>
                    <h3>
                        <?= $title ?>
                        <?php if ( $_GET['report'] == 'Ticket Activity Report' ) { ?>
                            <div class="pull-right">

                                <a class="cursor-hand printpdf" onclick="printPDF();"><img src="../img/icons/pdf.png" class="no-toggle" title="Print Report" width="25" /></a>
                                <img src="../img/icons/ROOK-email-icon.png" id="<?= strtolower(str_replace(' ', '_', $_GET['report'])) ?>" class="no-toggle cursor-hand offset-left-5" title="Email Report" width="25" onclick="email_doc(this);" data-title="<?= $title ?>" />
                                <a href="../quick_action_reminders.php?tile=reports" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><img class="no-toggle" title="Create Reminder" width="25" src="../img/icons/ROOK-reminder-icon.png" /></a>

                                <img src="../img/icons/ROOK-3dot-icon.png" class="show_search no-toggle cursor-hand offset-left-5" title="Show/Hide Search" width="25" />
                            </div>
                        <?php } else if ( $_GET['report'] == 'Ticket Activity Extra Report' ) { ?>
                            <div class="pull-right">

                                <a class="cursor-hand printpdf" onclick="printPDF();"><img src="../img/icons/pdf.png" class="no-toggle" title="Print Report" width="25" /></a>
                                <img src="../img/icons/ROOK-email-icon.png" id="<?= strtolower(str_replace(' ', '_', $_GET['report'])) ?>" class="no-toggle cursor-hand offset-left-5" title="Email Report" width="25" onclick="email_doc(this);" data-title="<?= $title ?>" />
                                <a href="../quick_action_reminders.php?tile=reports" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><img class="no-toggle" title="Create Reminder" width="25" src="../img/icons/ROOK-reminder-icon.png" /></a>

                                <img src="../img/icons/ROOK-3dot-icon.png" class="show_search no-toggle cursor-hand offset-left-5" title="Show/Hide Search" width="25" />
                            </div>
                        <?php } ?>
                    </h3>
                </div>

                <div class="standard-body" style="padding: 0.5em;"><?php
                    if ( $_GET['type'] == 'search' ) {
                        include('../Reports/field_list.php');
                        $value_config = ','.get_config($dbc, 'reports_dashboard').',';
                        $sorted_reports = [];
                        $q = $_GET['q']; ?>
                        <div class="standard-dashboard-body-content"><?php
                            foreach($ar_reports as $key => $report) {
                                if ( strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true && stripos($key, $q) !== false ) {
                                    $sorted_reports[$report[1]] = [$report[0],$key,'ar'];
                                }
                            }
                            foreach($compensation_reports as $key => $report) {
                                if ( strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true && stripos($key, $q) !== false ) {
                                    $sorted_reports[$report[1]] = [$report[0],$key,'compensation'];
                                }
                            }
                            foreach($customer_reports as $key => $report) {
                                if ( strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true && stripos($key, $q) !== false ) {
                                    $sorted_reports[$report[1]] = [$report[0],$key,'customer'];
                                }
                            }
                            foreach($estimates_reports as $key => $report) {
                                if ( strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true && stripos($key, $q) !== false ) {
                                    $sorted_reports[$report[1]] = [$report[0],$key,'estimates'];
                                }
                            }
                            foreach($history_reports as $key => $report) {
                                if ( strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true && stripos($key, $q) !== false ) {
                                    $sorted_reports[$report[1]] = [$report[0],$key,'history'];
                                }
                            }
                            foreach($marketing_reports as $key => $report) {
                                if ( strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true && stripos($key, $q) !== false ) {
                                    $sorted_reports[$report[1]] = [$report[0],$key,'marketing'];
                                }
                            }
                            foreach($operations_reports as $key => $report) {
                                if ( strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true && stripos($key, $q) !== false ) {
                                    $sorted_reports[$report[1]] = [$report[0],$key,'operations'];
                                }
                            }
                            foreach($pnl_reports as $key => $report) {
                                if ( strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true && stripos($key, $q) !== false ) {
                                    $sorted_reports[$report[1]] = [$report[0],$key,'pnl'];
                                }
                            }
                            foreach($sales_reports as $key => $report) {
                                if ( strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true && stripos($key, $q) !== false ) {
                                    $sorted_reports[$report[1]] = [$report[0],$key,'sales'];
                                }
                            }
                            foreach($staff_reports as $key => $report) {
                                if ( strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true && stripos($key, $q) !== false ) {
                                    $sorted_reports[$report[1]] = [$report[0],$key,'staff'];
                                }
                            }

                            if(!empty($sorted_reports)) {
                                ksort($sorted_reports);
                                foreach($sorted_reports as $key => $report) {
                                    echo '<div class="dashboard-item"><a data-file="'.$report[0].'" href="report_tiles.php?type='.$report[2].'&report='.$report[1].'">'.$key.'</a></div>';
                                }
                            } else {
                                echo '<div class="dashboard-item">No Record Found</div>';
                            } ?>
                        </div><?php

                    } else {
                        reports_tiles_content($dbc);
                    } ?>
                </div>

            </div>
        </div>
    </div>
</div>
<?php }
function reports_tiles_content($dbc) {
    include('../Reports/field_list.php');
    $value_config = ','.get_config($dbc, 'reports_dashboard').',';
    $report_tabs = !empty(get_config($dbc, 'report_tabs')) ? get_config($dbc, 'report_tabs') : 'operations,sales,ar,marketing,compensation,pnl,customer,staff,history';
    $report_tabs = explode(',', $report_tabs);
    if(empty($_GET['type'])) {
        $_GET['type'] = $report_tabs[0];
    }
    if($_GET['mobile_view'] == 'true') { ?>
        <script>
        $(document).ready(function() {
            $('[name=select_report]').off('change').change(function() {
                var panel = $(this).closest('.panel').find('.panel-body');
                if(this.value != '') {
                    var new_url = this.value;
                    $.ajax({
                        url: this.value+'&mobile_view=true',
                        method: 'POST',
                        response: 'html',
                        success: function(response) {
                            panel.html(response);
                            window.history.replaceState(null, '', new_url);
                        }
                    });
                }
            });
        });
        </script>
    <?php } ?>
    <div class="form-group form-horizontal">
        <label class="col-sm-4 control-label">Report:</label>
        <div class="col-sm-8">
            <select class="chosen-select-deselect" data-placeholder="Select Report" name="select_report">
                <option></option>
                <?php
                /* Hide Kristi from accessing Profit & Loss report on SEA (temp fix)
                 * Code also added on report_profit_loss.php */
                $contactid = $_SESSION['contactid'];
                if ( $_SERVER['SERVER_NAME'] == 'sea-alberta.rookconnect.com' || $_SERVER['SERVER_NAME'] == 'sea-regina.rookconnect.com' || $_SERVER['SERVER_NAME'] == 'sea-saskatoon.rookconnect.com' || $_SERVER['SERVER_NAME'] == 'sea-vancouver.rookconnect.com' || $_SERVER['SERVER_NAME'] == 'sea.freshfocussoftware.com' ) {
                    $results = mysqli_query ( $dbc, "SELECT `user_name` FROM `contacts` WHERE `contactid`='$contactid'");
                    while ( $row = mysqli_fetch_assoc ( $results) ) {
                        $user_name = $row[ 'user_name' ];
                        if ( $user_name == 'kristi' ) {
                            $sea_kristi = true;
                            break;
                        }
                    }
                }

                $sorted_reports = [];

                // Operations
                if($_GET['type'] == 'operations' || empty($_GET['type'])) {
                    foreach($operations_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true) {
                            $sorted_reports[$report[1]] = [$report[0],$key,'operations'];
                        }
                    }
                }
                // Sales
                else if($_GET['type'] == 'sales') {
                    foreach($sales_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true) {
                            $sorted_reports[$report[1]] = [$report[0],$key,'sales'];
                        }
                    }
                }
                // Accounts Receivables
                else if($_GET['type'] == 'ar') {
                    foreach($ar_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true) {
                            $sorted_reports[$report[1]] = [$report[0],$key,'ar'];
                        }
                    }
                }
                // Profit & Loss
                else if ( $_GET['type']=='pnl' ) {
                    foreach($pnl_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true) {
                            $sorted_reports[$report[1]] = [$report[0],$key,'pnl'];
                        }
                    }
                }
                // Marketing
                else if($_GET['type'] == 'marketing') {
                    foreach($marketing_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true) {
                            $sorted_reports[$report[1]] = [$report[0],$key,'marketing'];
                        }
                    }
                }
                // Compensation
                else if($_GET['type'] == 'compensation') {
                    foreach($compensation_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true) {
                            $sorted_reports[$report[1]] = [$report[0],$key,'compensation'];
                        }
                    }
                }
                // Customer
                else if($_GET['type'] == 'customer') {
                    foreach($customer_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true) {
                            $sorted_reports[$report[1]] = [$report[0],$key,'customer'];
                        }
                    }
                }
                // Staff
                else if($_GET['type'] == 'staff') {
                    foreach($staff_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true) {
                            $sorted_reports[$report[1]] = [$report[0],$key,'staff'];
                        }
                    }
                }
                // History
                else if($_GET['type'] == 'history') {
                    foreach($history_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true) {
                            $sorted_reports[$report[1]] = [$report[0],$key,'history'];
                        }
                    }
                }
                else if($_GET['type'] == 'estimates') {
                    foreach($estimates_reports as $key => $report) {
                        if(strpos($value_config, ','.$report[2].',') !== false && check_subtab_persmission($dbc, 'report', ROLE, $report[3]) === true) {
                            $sorted_reports[$report[1]] = [$report[0],$key,'estimates'];
                        }
                    }
                }
                else { ?>
                    <option selected value="report_tiles.php">Please Select a Tab to view the Reports</option><?php
                }
                if(!empty($sorted_reports)) {
                    ksort($sorted_reports);
                    foreach($sorted_reports as $key => $report) {
                        echo '<option data-file="'.$report[0].'" value="?type='.$report[2].'&report='.$report[1].'" '.($_GET['report'] == $report[1] ? 'selected' : '').'>'.$key.'</option>';
                    }
                }
            ?>
            </select>
        </div>
        <div class="clearfix"></div>
        <?php include('../Reports/'.$report_list[$_GET['report']][0]); ?>
    </div><?php
}
