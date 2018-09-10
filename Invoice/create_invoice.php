<?php include_once('config.php');
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
				<div class="pull-right text-lg">
                    <a data-toggle="collapse" data-parent="#header_divs" href="#header_summary" onclick="setWindowSize();"><img src="<?= WEBSITE_URL ?>/img/icons/pie-chart.png" height="32" width="32" title="Show <?= CONTACTS_NOUN ?> Summary" class="override-theme-color-icon no-toggle pad-5"></a>
                    <a data-toggle="collapse" data-parent="#header_divs" href="#header_tabs" onclick="setWindowSize();"><img src="<?= WEBSITE_URL ?>/img/icons/eyeball.png" height="32" width="32" title="Show <?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?> Tabs" class="override-theme-color-icon no-toggle pad-5"></a>
					<?php if($security['edit'] > 0) { ?>
                        <a href="" onclick="$('#save').click(); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/save.png" height="32" width="32" title="Save Invoice" class="override-theme-color-icon no-toggle pad-5"></a>
					<?php } ?>
					<?php if($security['config'] > 0) { ?>
                        <a href="field_config_invoice.php"><img title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me inline-img no-toggle pad-5"></a>
					<?php } ?>
				</div>
				<div class="scale-to-fill">
					<h1 class="gap-left"><a href="invoice_main.php"><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?></a></h1>
				</div>
                <div id="header_tabs" class="double-gap-bottom pad-horizontal panel-collapse collapse">
                    <?php include('tile_tabs.php'); ?>
                </div>
                <div id="header_summary" class="double-gap-bottom pad-horizontal panel-collapse collapse">
                    <?php $summary_only = true;
                    $invoice_config = $field_config;
                    include('../Contacts/contact_profile.php');
                    $field_config = $invoice_config; ?>
                    <div class="clearfix"></div>
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
                        <h3>Create <?= 'Invoice' ?></h3>
                    </div>
                    <div class="standard-body-content col-sm-12">
                        <form action="" method="POST" enctype="multipart/form-data" class="form-horizontal" role="form">
                        <?php if(in_array('touch',$ux_options) && (!in_array('standard',$ux_options) || $_GET['ux'] == 'touch')) { ?>
                            <script> window.location.replace('touch_main.php'); </script>
                        <?php } ?>
                        <?php include('edit_details.php');
                        if(in_array('services',$field_config) || in_array('unbilled_tickets',$field_config)) {
                            include('edit_services.php');
                        }
                        if(in_array('inventory',$field_config)) {
                            include('edit_inventory.php');
                        }
                        if(in_array('products',$field_config)) {
                            include('edit_products.php');
                        }
                        if(in_array('packages',$field_config)) {
                            include('edit_packages.php');
                        }
                        if(in_array('misc_items',$field_config) || in_array('unbilled_tickets',$field_config)) {
                            include('edit_misc.php');
                        }
                        if(in_array('unbilled_tickets',$field_config)) {
                            include('edit_tickets.php');
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