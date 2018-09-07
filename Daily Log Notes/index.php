<?php include_once('../include.php'); ?>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.main-screen .main-screen').is(':visible')) {
			var available_height = window.innerHeight - $(footer).outerHeight() - $('.tile-sidebar:visible').offset().top;
			if(available_height > 200) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.tile-sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
			}
            var sidebar_height = $('.tile-sidebar').outerHeight(true);
            $('.has-main-screen .main-screen').css('min-height', sidebar_height);
		}
	}).resize();
});
</script>
<?php include_once('../navigation.php');
checkAuthorised('daily_log_notes');
$security = get_security($dbc, 'daily_log_notes'); ?>
<div class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="log_iframe" src=""></iframe>
		</div>
	</div>
	<div class="row">
		<div class="main-screen">
			<div class="tile-header">
                <div class="pull-right settings-block"><?php if($security['config'] > 0) {
                        echo "<div class='pull-right gap-left'><a href='?settings=tabs'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30' /></a></div>";
                    }
                    echo "<div class='pull-right gap-left'><a href='?reports=view'><button class='btn brand-btn ".($_GET['reports'] == 'view' ? "active_tab" : "")." icon-pie-chart'>Reporting</button></a></div>"; ?>
                </div>
                <div class="scale-to-fill"><h1 class="gap-left"><a href="?">Daily Log Notes</a></h1></div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->
            
			<div class="clearfix"></div>
			<?php if(!empty($_GET['settings']) && $security['config'] > 0) {
				include('field_config_log.php');
			} else if(isset($_GET['reports'])) {
				include('sidebar.php'); ?>
				<div class='scale-to-fill has-main-screen hide-titles-mob'>
					<div class='main-screen form-horizontal'>
						<?php include('reports.php'); ?>
					</div>
				</div>
			<?php } else {
				include('sidebar.php'); ?>
				<div class='scale-to-fill has-main-screen hide-titles-mob'>
					<div class='main-screen form-horizontal'>
						<?php include('log_note_list.php'); ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<?php include('../footer.php'); ?>