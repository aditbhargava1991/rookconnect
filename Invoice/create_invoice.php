<?php include_once('config.php');
include_once('edit_save.php');
include_once ('../navigation.php'); ?>
<div class="container">
	<div class="iframe_overlay" style="display:none; margin-top:-20px; padding-bottom:20px;">
		<div class="iframe">
			<iframe name="edit_board" src="../blank_loading_page.php"></iframe>
		</div>
	</div>
    <div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header" id="header_divs">
				<div class="pull-right pad-5">
                    <a data-toggle="collapse" data-parent="#header_divs" href="#header_tabs" onclick="setWindowSize();"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-3dot-icon.png" title="Show <?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?> Tabs" class="inline-img override-theme-color-icon no-toggle"></a>
					<?php if($security['config'] > 0) { ?>
                        <a href="field_config_invoice.php"><img title="Tile Settings" src="../img/icons/settings-4.png" width="30" class="settings-classic wiggle-me no-toggle"></a>
					<?php } ?>
				</div>
				<div class="scale-to-fill">
					<h1 class="gap-left"><a href="invoice_main.php"><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?></a></h1>
				</div>
                <div id="header_summary" class="double-gap-bottom pad-horizontal panel-collapse collapse">
                    <?php $summary_only = true;
                    $invoice_patient = $patient;
                    $invoice_config = $field_config;
                    include('../Contacts/contact_profile.php');
                    $field_config = $invoice_config;
                    $patient = $invoice_patient; ?>
                    <hr />
                </div>
                <div id="header_tabs" class="double-gap-bottom pad-horizontal panel-collapse collapse">
                    <?php include('tile_tabs.php'); ?>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible">
                <ul><?php include('sidebar_edit.php'); ?></ul>
            </div>
            <div class="scalable">
                <?php include('build_preview.php'); ?>
            </div>
            <div class="scale-to-fill has-main-screen">
                <div class="main-screen standard-body default_screen form-horizontal">
                    <div class="standard-body-title">
                        <h3><?= $_GET['inv_mode'] === 'adjust' ? 'Adjust Invoice #'.$invoiceid : 'Create Invoice' ?>
                            <div class="pull-right small">
                                <a data-toggle="collapse" data-parent="#header_divs" href="#header_summary" onclick="setWindowSize();"><img src="<?= WEBSITE_URL ?>/img/icons/pie-chart.png" title="Show <?= CONTACTS_NOUN ?> Summary" class="inline-img override-theme-color-icon no-toggle"></a>
                                <a href="invoice_main.php"><img class="inline-img no-toggle" title="Cancel Invoice" src="../img/icons/ROOK-trash-icon.png"></a>
                                <a href="../quick_action_reminders.php?tile=invoice" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><img class="inline-img no-toggle" title="Create Reminder" src="../img/icons/ROOK-reminder-icon.png"></a>
                                <?php if($security['edit'] > 0 && $_GET['inv_mode'] != 'adjust') { ?>
                                    <a href="" onclick="$('#save').click(); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/save.png" title="Save Invoice" class="inline-img override-theme-color-icon no-toggle"></a>
                                <?php } ?>
                            </div>
                        </h3>
                    </div>
                    <div class="standard-body-content col-sm-12">
                        <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal" role="form">
                            <?php if(in_array('touch',$ux_options) && (!in_array('standard',$ux_options) || $_GET['ux'] == 'touch')) { ?>
                                <script> window.location.replace('touch_main.php'); </script>
                            <?php } ?>
                            <?php include('edit_details.php');
                            echo '<hr />';
                            if(in_array('services',$field_config) || in_array('unbilled_tickets',$field_config)) {
                                include('edit_services.php');
                                echo '<hr />';
                            }
                            if(in_array('inventory',$field_config)) {
                                include('edit_inventory.php');
                                echo '<hr />';
                            }
                            if(in_array('products',$field_config)) {
                                include('edit_products.php');
                                echo '<hr />';
                            }
                            if(in_array('packages',$field_config)) {
                                include('edit_packages.php');
                                echo '<hr />';
                            }
                            if(in_array('misc_items',$field_config) || in_array('unbilled_tickets',$field_config)) {
                                include('edit_misc.php');
                                echo '<hr />';
                            }
                            if(in_array_starts('unbilled_tickets',$field_config)) {
                                include('edit_tickets.php');
                                echo '<hr />';
                            }
                            include('edit_summary.php'); ?>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include('../footer.php');