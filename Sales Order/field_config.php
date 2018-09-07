<?php
/*
 * Sales Order Config
 */
error_reporting(0);
include ('../include.php');
?>

<script type="text/javascript">
$(document).ready(function() {
    $('#mobile_tabs .panel-heading').click(loadPanel);
    /* if($(window).width() > 767) {
        resizeScreen();
        $(window).resize(function() {
            resizeScreen();
        });
    } */
    $(window).resize(function() {
        var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('#sales_order_div .tile-container').offset().top - 1;
        if(available_height > 200) {
            $('#sales_order_div .tile-sidebar, #sales_order_div .scale-to-fill').height(available_height);
        }
    }).resize();
});

function resizeScreen() {
    var view_height = $(window).height() > 800 ? $(window).height() : 800;
    $('#sales_order_div .scale-to-fill,#sales_order_div .scale-to-fill .main-screen,#sales_order_div .tile-sidebar').height(view_height - $('#sales_order_div .tile-container').offset().top - $('#footer').outerHeight());
}

function loadPanel() {
    var panel = $(this).closest('.panel').find('.panel-body');
    panel.html('Loading...');
    $.ajax({
        url: panel.data('file-name'),
        method: 'POST',
        response: 'html',
        success: function(response) {
            panel.html(response);
        }
    });
}
</script>
</head>

<body>
<?php
	include_once ('../navigation.php');
checkAuthorised('sales_order');

    $tab_list = [
        'general' => 'General',
        'default_template' => 'Default Template',
        'types' => SALES_ORDER_NOUN.' Types',
        'fields' => 'Fields',
        'dashboard' => 'Dashboard Fields',
        'customer_info' => 'Customer Settings',
        'contact_categories' => 'Contact Categories',
        'pdf' => 'PDF Settings',
        'logo' => 'Default Logo',
        'statuses' => SALES_ORDER_NOUN.' Statuses',
        'actions' => SALES_ORDER_NOUN.' Next Actions',
        'payment' => 'Payment Type Options',
        'taxes' => 'Taxes & Rates',
        'staff_groups' => 'Staff Collaboration Groups',
        'security' => SALES_ORDER_NOUN.' Security Access'
    ];

    $tab = $_GET['tab'];
    if(empty($tab)) {
        $tab = 'fields';
    }
?>

<div id="sales_order_div" class="container">
    <div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
        <div class="iframe">
            <div class="iframe_loading">Loading...</div>
            <iframe name="sales_order_iframe" src=""></iframe>
        </div>
    </div>
    
    <div class="row hide_on_iframe">
		<div class="main-screen"><?php
            include('tile_header.php'); ?>
            
            <div class="tile-container" style="height: 100%;">
                <div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" style="background-color: #fff; padding: 0; margin-left: 5px; width: calc(100% - 10px);" id="mobile_tabs">
                    <?php foreach ($tab_list as $key => $value) { ?>
                        <div class="panel panel-default">
                            <div class="panel-heading mobile_load">
                                <h4 class="panel-title">
                                    <a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_<?= $key ?>">
                                        <?= $value ?><span class="glyphicon glyphicon-plus"></span>
                                    </a>
                                </h4>
                            </div>

                            <div id="collapse_<?= $key ?>" class="panel-collapse collapse">
                                <div class="panel-body" data-file-name="field_config_<?= $key ?>.php">
                                    Loading...
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>

                <!-- Sidebar -->
                <div class="standard-collapsible tile-sidebar tile-sidebar-noleftpad hide-on-mobile">
                    <ul>
                        <li><a href="index.php">Dashboard</a></li>
                        <?php foreach ($tab_list as $key => $value) {
                            echo '<a href="?tab='.$key.'"><li class="collapsed cursor-hand '.($tab == $key ? 'active' : '').'">'.$value.'</li></a>';
                        } ?>
                    </ul>
                </div><!-- .tile-sidebar -->
                
                <!-- Main Screen -->
                <div class="scale-to-fill hide-on-mobile" style="background-color: #fff;">
                    <div class="standard-body main-screen-white" style="padding-left: 0; padding-right: 0; border: none;">
                        <div class="standard-body-title"><h3><?= $tab_list[$tab] ?></h3></div>
                        <div class="standard-body-content pad-10">
                            <?php include('field_config_'.$tab.'.php'); ?>
                        </div>
                    </div>
                </div>
                
                <div class="clearfix"></div>
            </div><!-- .tile-container -->

        </div><!-- .main-screen -->
    </div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>