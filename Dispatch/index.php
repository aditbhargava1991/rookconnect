<?php
/*
Dispatch Tile
*/
include_once('../include.php');
?>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.main-screen .main-screen').not('.show-on-mob .main-screen').is(':visible')) {
			var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.has-main-screen:visible').offset().top;
			if(available_height > 200) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
			}
		}
	}).resize();
});
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('dispatch');
?>

<div class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="dispatch_iframe" src=""></iframe>
		</div>
	</div>
	<div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header">
				<div class="pull-right settings-block">
			        <div class="gap-left pull-right">
					    <?php if(config_visible_function($dbc, 'dispatch') == 1) { ?>
				            <a href="?settings=tile" class="mobile-block pull-right "><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
				            <span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					    <?php } ?>
					    <?php if(!isset($_GET['settings'])) { ?>
						    <a href="" onclick="retrieve_summary(this, 'ALL'); return false;"><img class="dispatch-summary-icon inline-img pull-right btn-horizontal-collapse no-toggle gap-right" src="../img/icons/pie-chart.png" title="" data-original-title="View Summary"></a>
						<?php } ?>
			        </div>
				</div>
				<div class="scale-to-fill">
				    <h1 class="gap-left"><a href="?" class="default-color">Dispatch</a></h1>
				</div>
				<div class="clearfix"></div>
			</div>

			<div class="clearfix"></div>
			<?php if(isset($_GET['settings']) && config_visible_function($dbc, 'dispatch') == 1) {
				include('field_config.php');
			} else {
				include('dashboard.php');
			} ?>
		</div>
	</div>
</div>

<?php include_once('../footer.php'); ?>