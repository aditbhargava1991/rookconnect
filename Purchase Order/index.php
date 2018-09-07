<?php // Projects View
include_once('../include.php');
$tile_title = get_tile_title_po($dbc); ?>
<?php if(!IFRAME_PAGE) { ?>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.sidebar').is(':visible')) {
			<?php if(isset($_GET['edit']) && $ticket_layout == 'Accordions') { ?>
				var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.standard-body').offset().top;
			<?php } else { ?>
				var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
			<?php } ?>
			if(available_height > 200) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
			}
		}
	}).resize();
});
</script>
<?php } ?>
</head>
<body>
<?php checkAuthorised('purchase_order');
include_once ('../navigation.php');
$po_types = get_config($dbc, 'purchase_order_categories');
$security = get_security($dbc, 'purchase_order'); ?>
<div class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="ticket_iframe" src=""></iframe>
		</div>
	</div>
	<div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header" style="<?= IFRAME_PAGE ? 'display:none;' : '' ?>">
                <div class="pull-right settings-block"><?php
                    if($security['config'] > 0) {
                        echo "<div class='pull-right gap-left'><a href='?settings=fields'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30' /></a></div>";
                    } ?>
                </div>
                <div class="scale-to-fill">
					<h1 class="gap-left"><a href="?"><?= $tile_title ?></a>
						<img class="no-toggle statusIcon pull-right no-margin inline-img small" title="" src="" data-original-title=""></h1>
				</div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

			<div class="clearfix"></div>
			
			<?php if(!IFRAME_PAGE) { ?>
				<div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible">
					<ul>
						<?php include_once('sidebar.php'); ?>
					</ul>
				</div>
			<?php } ?>
			
			<div class='scale-to-fill has-main-screen'>
				<div class='main-screen standard-body form-horizontal' <?= IFRAME_PAGE ? 'style="height:auto;"' : '' ?>>
					<div class="standard-body-title">
						<h3><?= $page_title ?></h3>
					</div>
					<div class="standard-body-content pad-top" style="padding: 5px;">
						<?php if($_GET['settings'] == 'tabs') {
							include_once('field_config_tabs.php');
						} else if($_GET['settings'] == 'promo') {
							include_once('field_config_promotion.php');
						} else if(!empty($_GET['settings'])) {
							include_once('field_config_pos.php');
						} else if($_GET['tab'] == 'summary') {
							include_once('summary.php');
						} else if($_GET['tab'] == 'create') {
							include_once('add_point_of_sell.php');
						} else if($_GET['tab'] == 'pending') {
							include_once('pending.php');
						} else if($_GET['tab'] == 'receiving') {
							include_once('receiving.php');
						} else if($_GET['tab'] == 'payable') {
							include_once('unpaid_invoice.php');
						} else if($_GET['tab'] == 'remote') {
							include_once('cross_software_pending.php');
						} else if($_GET['tab'] == 'site_po') {
							include_once('site_po.php');
						} else if($_GET['tab'] == 'cust_po') {
							include_once('customer_po.php');
						} else if($_GET['tab'] == 'send_pos') {
							include_once('send_point_of_sell.php');
						} else {
							include_once('complete.php');
						} ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<?php include_once('../footer.php'); ?>
