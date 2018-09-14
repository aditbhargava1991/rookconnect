<?php
/*
Payment/Invoice Listing
*/
include ('../include.php');
include_once('../Ticket/config.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
?>
<script type="text/javascript" src="../Invoice/invoice.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $(window).resize(function() {
        var available_height = window.innerHeight - $('footer:visible').height() - $('.tile-header').height() - $('.standard-body-title').height() - 5;
        if(available_height > 200) {
            $('#invoice_div .standard-body').height(available_height);
        }
    }).resize();
});
</script>
</head>
<body>
<?php include_once ('../navigation.php');
?>
<div id="invoice_div" class="container">
    <div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="edit_board" src=""></iframe>
		</div>
	</div>
    
	<div class="row">
        <div class="main-screen">
            <div class="tile-header standard-header">
                <div class="row">
                    <h1 class="pull-left"><a href="invoice_main.php"><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?></a></h1>
                    <?php if(config_visible_function($dbc, (FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out')) == 1) {
                        echo '<a href="field_config_invoice.php" class="pull-right gap-right gap-top"><img width="30" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me no-toggle"></a>';
                    } ?>
                    <span class="pull-right gap-top offset-right-5"><img src="../img/icons/eyeball.png" alt="View Tabs" title="View Tabs" class="cursor-hand no-toggle inline-img" onclick="view_tabs();" /></span>
                    <div class="clearfix"></div>
                    <div class="view_tabs double-padded" style="display:none;"><?php include('tile_tabs.php'); ?></div>
                </div>
            </div><!-- .tile-header -->

            <div class="scale-to-fill has-main-screen">
                <div class="main-screen standard-body form-horizontal">
                    <div class="standard-body-title">
                        <h3>Unbilled <?= TICKET_TILE ?></h3>
                    </div>
                    
                    <div class="standard-body-content">
                        <div class="pad-10">
                            <?php $_GET['status'] = 'unbilled';
                            include('../Ticket/ticket_invoice.php'); ?>
                        </div>
                    </div><!-- .standard-body-content -->
                </div><!-- .main-screen standard-body -->
            </div><!-- .has-main-screen -->
        </div><!-- .main-screen -->
        
	</div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>