<?php // Rate Cards View
error_reporting(0);
include_once('../include.php');
$rookconnect = get_software_name();
if(!IFRAME_PAGE) { ?>
	<script>
	$(document).ready(function() {
		$(window).resize(function() {
			$('.main-screen').css('padding-bottom',0);
			if($('.main-screen .main-screen').not('.show-on-mob .main-screen').is(':visible')) {
				var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
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
<?php
checkAuthorised('rate_card');
$edit_access = vuaed_visible_function($dbc, 'rate_card');
$config_access = config_visible_function($dbc, 'rate_card');
if(!IFRAME_PAGE) {
	include_once ('../navigation.php');
}
$tab_list = array_filter(explode(',',get_config($dbc, 'rate_card_tabs')));
if(empty($_GET['type'])) {
	$_GET['type'] = $tab_list[0];
} ?>
<div class="container" <?= IFRAME_PAGE ? 'style="height:auto;"' : '' ?>>
	<div class="row">
		<div class="main-screen">
			<?php if(!IFRAME_PAGE) { ?>
				<div class="tile-header">
					<h1 style="margin-top: 0; padding: 0.25em;"><a href="?">Rate Cards</a><?php if($config_access > 0) {
						echo "<div class='pull-right' style='height: 1.35em; width: 1.35em;'><a href='?settings=fields'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' style='height: 100%;'></a></div>";
						echo "<div class='popover-examples pull-right' style='margin:3px 4px 0 5px;'><a data-toggle='tooltip' data-placement='top' title='Click here to access the settings within this tile. Any changes made will appear on your dashboard.'><img src='". WEBSITE_URL ."/img/info.png' width='20'></a></div>";
					}
					if($edit_access > 0) { ?>
						<a href="?type=<?= $_GET['type'] ?>&card=<?= $_GET['type'] ?>&status=import" class="<?= $_GET['status'] == 'import' ? 'active_tab' : '' ?> btn brand-btn pull-right">Import Rates</a>
						<?php echo "
							<div class='pull-right' style='height: 1em; padding: 0 0.25em;'>
								<div class='popover-examples pull-left' style='margin:4px 2px 0 0;'><a data-toggle='tooltip' data-placement='top' title='Create a new rate card.'><img src='". WEBSITE_URL ."/img/info.png' width='20'></a></div>
								<a href='?type=".$_GET['type']."&card=".$_GET['type']."&id=new&category=".$_GET['category']."&t=".$_GET['t']."' style='font-size: 0.5em;'><button class='btn brand-btn hide-titles-mob'>New Rate Card</button></a>
							</div>";
					} ?></h1><div class="clearfix"></div>
				</div>
			<?php } else { ?>
				<h1>Rate Cards<a href="../blank_loading_page.php" class="pull-right"><img src="../img/icons/cancel.png" class="small inline-img"></a></h1>
			<?php } ?>
			<?php if(!empty($_GET['settings']) && $config_access > 0) {
				include('field_config.php');
			} else {
				if(!IFRAME_PAGE) {
					include('rate_mobile.php');
					include('rate_sidebar.php');
				} ?>
				<div class='main-content-screen scale-to-fill has-main-screen <?= IFRAME_PAGE ? '' : 'hide-titles-mob' ?>' style="overflow-y:hidden;">
					<div class='main-screen standard-body override-main-screen form-horizontal'  <?= IFRAME_PAGE ? 'style="height:auto;"' : '' ?>>
						<div class="standard-body-title"><h3><?= $_GET['id'] > 0 ? 'Edit Rate Card' : 'Rate Cards' ?></h3></div>
						<div class="standard-body-content pad-left pad-right"><div class="clearfix"></div>
							<?php if($_GET['status'] == 'import') {
								include_once('import_rates.php');
							} else if($_GET['type'] == 'universal') {
								include('company_add_rate_card.php');
							} else if($_GET['type'] == 'company') {
								if($_GET['id'] > 0 && $_GET['status'] == 'show') {
									include('company_show_rate_card.php');
								} else if(!empty($_GET['id'])) {
									include('company_add_rate_card.php');
								} else {
									include('company_current_rate_card.php');
								}
							} else if($_GET['type'] == 'customer') {
								if($_GET['ratecardid'] > 0 && $_GET['status'] == 'show') {
									include('customer_show_rate_card.php');
								} else if(!empty($_GET['id']) || !empty($_GET['ratecardid'])) {
									include('customer_add_rate_card.php');
								} else {
									include('customer_active_rate_card.php');
								}
							} else if($_GET['type'] == 'holiday') {
								if($_GET['ratecardid'] > 0 && $_GET['status'] == 'show') {
									include('holiday_show_rate_card.php');
								} else if(!empty($_GET['id']) || !empty($_GET['ratecardid'])) {
									include('holiday_add_rate_card.php');
								} else {
									include('holiday_active_rate_card.php');
								}
							}
							else if($_GET['type'] == 'estimate') {
								include('estimate_scope.php');
							} else if($_GET['type'] == 'position') {
								if($_GET['id'] > 0 && $_GET['status'] == 'show') {
									include('position_show_rate_card.php');
								} else if(!empty($_GET['id'])) {
									include('position_add_rate_card.php');
								} else {
									include('position_current_rate_card.php');
								}
							} else if($_GET['type'] == 'staff') {
								if($_GET['id'] > 0 && $_GET['status'] == 'show') {
									include('staff_show_rate_card.php');
								} else if(!empty($_GET['id'])) {
									include('staff_add_rate_card.php');
								} else {
									include('staff_current_rate_card.php');
								}
							} else if($_GET['type'] == 'equipment') {
								if($_GET['id'] > 0 && $_GET['status'] == 'show') {
									include('equipment_show_rate_card.php');
								} else if($_GET['status'] == 'add' || !empty($_GET['id'])) {
									include('equipment_add_rate_card.php');
								} else {
									include('equipment_current_rate_card.php');
								}
							} else if($_GET['type'] == 'category') {
								if($_GET['id'] > 0 && $_GET['status'] == 'show') {
									include('category_show_rate_card.php');
								} else if(!empty($_GET['id'])) {
									include('category_add_rate_card.php');
								} else {
									include('category_current_rate_card.php');
								}
							} else if($_GET['type'] == 'services') {
								if($_GET['id'] > 0 && $_GET['status'] == 'show') {
									include('services_show_rate_card.php');
								} else if(!empty($_GET['id']) || !empty($_GET['service'])) {
									include('services_add_rate_card.php');
								} else {
									include('services_current_rate_card.php');
								}
							} else if($_GET['type'] == 'labour') {
								if($_GET['id'] > 0 && $_GET['status'] == 'show') {
									include('labour_show_rate_card.php');
								} else if(!empty($_GET['id']) || !empty($_GET['labourid'])) {
									include('labour_add_rate_card.php');
								} else {
									include('labour_current_rate_card.php');
								}
							} else if($_GET['type'] == 'tasks') {
								if(!empty($_GET['id']) || !empty($_GET['task'])) {
									include('tasks_add_rate_card.php');
								} else {
									include('tasks_current_rate_card.php');
								}
							} else { ?>
								<h4 class="pad-10">Please select a type of Rate Card from the left</h4>
							<?php } ?>
							<div class="clearfix"></div>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		if($('ul.sidebar').is(':visible')) {
			var available_height = window.innerHeight - $(footer).outerHeight() - $('.main-screen .main-screen').offset().top;
			if(available_height > 200) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('ul.sidebar').outerHeight(available_height).css('overflow-y','auto');
			}
		}
	}).resize();
});
</script>
<?php include('../footer.php'); ?>