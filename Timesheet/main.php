<?php
/*
 * Main Container
 */
include('../include.php');
include_once('../Calendar/calendar_functions_inc.php');
include 'config.php';
?>

<script>
$(document).ready(function() {
	$('#settings_div .panel-heading').click(loadPanel);
    if($(window).width() >= 768) {
		$(window).resize(function() {
			var view_height = $(window).height() - ($('.scale-to-fill.has-main-screen').offset().top + $('#footer:visible').outerHeight());
			$('.tile-sidebar,.main-screen.standard-body').height(view_height);
		}).resize();
	}
    
    $('.search_showhide').hide();
    $('#search_showhide').click(function() {
        $('.search_showhide').toggle();
    });
});
function loadPanel() {
	body = $(this).closest('.panel').find('.panel-body');
	$.ajax({
		url: $(body).data('url'),
		response: 'html',
		success: function(response) {
			$(body).html(response);
		}
	});
}
</script>
</head>

<body>
<?php
    include_once ('../navigation.php');
    checkAuthorised('timesheet');
    $tab_config = get_config($dbc,'timesheet_tabs').',day_tracking,';
    $navtab = trim($_GET['navtab']);
    $navtab_list = explode(',', $tab_config);
?>
<div class="container" id="timesheet_div">
	<div id="dialog-pdf-options" title="Select PDF Fields" style="display: none;">
		<?php echo get_pdf_options($dbc); ?>
	</div>
	<div id="dialog-signature" title="Signature Box" style="display: none;">
		<?php $output_name = 'time_cards_signature';
		include('../phpsign/sign_multiple.php'); ?>
	</div>
	<div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="timesheet_iframe" src=""></iframe>
		</div>
	</div>
    
	<div class="row timesheet_div">
        <div class="main-screen">

            <!-- Tile Header -->
            <div class="tile-header standard-header">
                <div class="scale-to-fill">
                    <h1 class="gap-left pull-left"><a href="index.php" class="default-color">Time Sheets</a></h1><?php
                    $security = get_security($dbc, 'timesheet');
                    if ( $security['config'] > 0 ) {
                        echo '<a href="field_config.php" class="pull-right gap-right gap-top"><img src="../img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>';
                    } ?>
                    <img class="no-toggle statusIcon pull-right no-margin inline-img small" title="" src="" data-original-title="" />
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->
            
            <!-- Mobile Sidebar -->
            <div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" id="mobile_tabs">
                <?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'logo')) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_logo">
                                    Logo
                                 <span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_logo" class="panel-collapse collapse">
                            <div class="panel-body" data-url="logo.php">
                                Loading...
                            </div>
                        </div>
                    </div>
                <?php } ?>
                <?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'contact_sort')) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading mobile_load">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_displaypref">
                                Display Preferences <span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>
                        <div id="collapse_displaypref" class="panel-collapse collapse">
                            <div class="panel-body" data-url="contacts_sort_order.php">
                                Loading...
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div><!-- .panel-group -->

            <!-- Desktop Sidebar -->
            <div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
                <ul>
                    <?php echo get_tabs('', $_GET['tab'], $navtab, array('db' => $dbc, 'field' => $value['config_field'])); ?>
                </ul>
            </div>
            
            <div class="scale-to-fill has-main-screen hide-titles-mob" style="margin-bottom:-20px;">
                <div class="main-screen standard-body form-horizontal">
                    <div class="standard-body-title">
                        <h3 class="pull-left"><?php
                        if ( !empty($navtab) ) {
                            foreach ( $navtab_list as $navtab_item ) {
                                $navtab_item_lower = str_replace(' ', '_', strtolower($navtab_item));
                                if ( $navtab == $navtab_item_lower ) {
                                    echo $navtab_item;
                                }
                            }
                        } else {
                            echo $navtab_list[0];
                        } ?></h3><?php
                        if ( $navtab == 'time_sheets' ) { ?>
                            <div class="pull-right">
                                <img id="search_showhide" src="../img/icons/ROOK-3dot-icon.png" alt="Show/Hide Search" title="Show/Hide Search" class="no-toggle cursor-hand offset-top-12 gap-right" width="29" />
                            </div><?php
                        } ?>
                        <div class="clearfix"></div>
                    </div>

                    <div class="standard-body-content" style="padding:0.5em;"><?php
                        $default_tab = !empty(get_config($dbc, 'timesheet_default_tab')) ? get_config($dbc, 'timesheet_default_tab') : 'Custom';
                        switch($navtab) {
                            case 'pay_period': include('tab_pay_period.php'); break;
                            case 'holidays': include('holidays.php'); break;
                            case 'coordinator_approvals': include('time_card_approvals_coordinator.php?tab='.$default_tab); break;
                            case 'manager_approvals': include('time_card_approvals_manager.php?tab='.$default_tab); break;
                            case 'reporting': include('reporting.php?tab='.$default_tab); break;
                            case 'payroll': include('payroll.php?tab='.$default_tab); break;
                            case 'time_sheets': default: include('time_cards_content.php'); break;
                        } ?>
                    </div>
                </div>
            </div>
        </div><!-- .main-screen -->
    </div><!-- .row.timesheet_div -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>