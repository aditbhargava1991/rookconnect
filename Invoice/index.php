<?php
    error_reporting(0);
    include_once('../include.php');
?>
<script>
    $(document).ready(function() {
        $(window).resize(function() {
            $('.main-screen').css('padding-bottom',0);
            if($('.main-screen .main-screen').is(':visible')) {
                var available_height = window.innerHeight - $(footer).outerHeight() - $('.sidebar:visible').offset().top;
                if(available_height > 200) {
                    $('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
                    $('.sidebar').outerHeight(available_height).css('overflow-y','auto');
                    $('.search-results').outerHeight(available_height).css('overflow-y','auto');
                }
                var sidebar_height = $('.tile-sidebar').outerHeight(true);
            }
        }).resize();
        
        $('.panel-heading.mobile_load').click(loadPanel);
        
        $('.search-text').keypress(function(e) {
            if (e.which==13) {
                var search = this.value;
                window.location.replace('index.php?q='+search);
            }
        });
    });
    
    function loadPanel() {
        var panel = $(this).closest('.panel').find('.panel-body');
        var tab = panel.data('url');
        panel.load(tab);
    }
</script>
</head>

<body>
<?php 
    if(FOLDER_NAME == 'accountreceivables') {
        checkAuthorised('accounts_receivables');
        $security = get_security($dbc, 'accounts_receivables');
        $tab_list = ['insurer_ar','patient_ar','ui_invoice_report','insurer_ar_report','insurer_ar_cm'];
    } else if(FOLDER_NAME == 'posadvanced') {
        checkAuthorised('posadvanced');
        $security = get_security($dbc, 'posadvanced');
        $tab_list = explode(',', get_config($dbc, 'invoice_tabs'));
    } else {
        checkAuthorised('check_out');
        $security = get_security($dbc, 'check_out');
        $tab_list = explode(',', get_config($dbc, 'invoice_tabs'));
    }
    
    $edit_access = vuaed_visible_function($dbc, 'check_out');
    $config_access = config_visible_function($dbc, 'check_out');
    $ux_options = explode(',',get_config($dbc, FOLDER_NAME.'_ux'));
    $purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
    $purchaser_label = count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0];
    $payer_config = explode(',',get_config($dbc, 'invoice_payer_contact'));
    $payer_label = count($payer_config) > 1 ? 'Third Party' : $payer_config[0];
    
    include_once ('../navigation.php');
?>

<div id="invoice_div" class="container">

    <div class="iframe_overlay" style="display:none;">
        <div class="iframe">
            <div class="iframe_loading">Loading...</div>
            <iframe src=""></iframe>
        </div>
    </div>
    
    <div class="row">
		<div class="main-screen">
            <!-- Tile Header -->
			<div class="tile-header standard-header" style="<?= IFRAME_PAGE ? 'display:none;' : '' ?>">
                <div class="row">
                    <div class="pull-left"><h1><a href="invoice_main.php"><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?></a></h1></div>
                    <div class="pull-right gap-top"><?php
                        if ( config_visible_function($dbc, (FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out')) == 1 ) { ?>
                            <a href="field_config_invoice.php" class="mobile-block pull-right gap-right offset-left-5"><img style="width:30px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me no-toggle"></a><?php
                        }
                        if ( in_array('sell', $tab_list) && vuaed_visible_function($dbc, (FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out')) == 1 ) {
                            if ( in_array('touch',$ux_options) ) { ?>
                                <a href='create_invoice.php'><span class="hide-titles-mob btn brand-btn <?= strpos($_SERVER['PHP_SELF'],'/create_invoice.php') !== FALSE ? 'active_tab' : '' ?>">New Invoice (Keyboard)</span><img src="../img/icons/ROOK-add-icon.png" class="show-on-mob no-toggle header-icon" style="margin-top:-3px;" title="New Invoice (Keyboard)" /></a>
                                <a href='touch_main.php'><span class="hide-titles-mob btn brand-btn <?= strpos($_SERVER['PHP_SELF'],'/touch_main.php') !== FALSE ? 'active_tab' : '' ?>">New Invoice (Touchscreen)</span><img src="../img/icons/ROOK-add-icon.png" class="show-on-mob no-toggle header-icon" style="margin-top:-3px;" title="New Invoice (Touchscreen)" /></a><?php
                            } else { ?>
                                <a href='create_invoice.php'><span class="hide-titles-mob btn brand-btn <?= strpos($_SERVER['PHP_SELF'],'/create_invoice.php') !== FALSE ? 'active_tab' : '' ?>">New Invoice</span><img src="../img/icons/ROOK-add-icon.png" class="show-on-mob no-toggle header-icon" style="margin-top:-3px;" title="New Invoice" /></a><?php
                            }
                        } ?>
                    </div>
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->
            
            <?php include('sidebar.php'); ?>
            
            <!-- Content -->
            <div class="scale-to-fill has-main-screen hide-titles-mob" style="margin-bottom:-20px;">
                <div class="main-screen standard-body form-horizontal"><?php
                    switch($_GET['tab']) {
                        case 'checkin':
                            include('checkin.php');
                            break;
                        case 'sell':
                            ob_clean();
                            header('Location: create_invoice.php?invoiceid='.$_GET['invoiceid']);
                            exit();
                            break;
                        case 'touch':
                            include('touch_main.php');
                            break;
                        case 'today':
                            include('today_invoice.php');
                            break;
                        case 'all':
                            include('invoice_list.php');
                            break;
                        case 'unbilled_tickets':
                            include('unbilled_tickets.php');
                            break;
                        case 'unpaid':
                            include('unpaid_invoice_list.php');
                            break;
                        case 'contact_ar':
                            include('patient_account_receivables.php');
                            break;
                        case 'third_party_ar':
                            include('insurer_account_receivables.php');
                            break;
                        case 'unpaid_third_party':
                            include('unpaid_insurer_invoice.php');
                            break;
                        case 'paid_third_party_ar':
                            include('insurer_account_receivables_report.php');
                            break;
                        case 'clinic_master':
                            include('insurer_account_receivables_cm.php');
                            break;
                        case 'voided':
                            include('void_invoices.php');
                            break;
                        case 'refunds':
                            include('refund_invoices.php');
                            break;
                        case 'ui_report':
                            include('ui_invoice_reports.php');
                            break;
                        case 'cashout':
                            include('cashout.php');
                            break;
                        case 'gf':
                            include('giftcards.php');
                            break;
                        case 'search':
                            include('search.php');
                            break;
                        default:
                            include('today_invoice.php');
                            break;
                    } ?>
                </div>
            </div><!-- .has-main-screen -->
            
		</div><!-- .main-screen -->
	</div><!-- .row -->
</div><!-- .container -->

<div class="clearfix"></div>

<?php include('../footer.php'); ?>