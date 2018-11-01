<?php include('../include.php');
checkAuthorised('optimize'); ?>
<script>
var scaleFunction = function() {}
$(document).ready(function() {
	$(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.main-screen .main-screen').not('.show-on-mob .main-screen').is(':visible')) {
			var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
			if(available_height > 200) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
				if($('.assign_list_box').is(':visible')) {
					var available_height = $('.main-screen .main-screen').innerHeight() - $('.assign_list_box').get(0).offsetTop;
					if(available_height > 150) {
						$('.assign_list_box').outerHeight(available_height);
						$('.main-screen .main-screen').css('overflow-y','hidden');
					}
				}
			}
		}
        scaleFunction();
	}).resize();
});
</script>
<?php include('../navigation.php');
$security = get_security($dbc, 'optimize');
$tab_list = [];
if(check_subtab_persmission($dbc, 'optimize', ROLE, 'upload')) {
	$tab_list[] = 'upload';
}
if(check_subtab_persmission($dbc, 'optimize', ROLE, 'macros')) {
	$tab_list[] = 'macros';
}
if(check_subtab_persmission($dbc, 'optimize', ROLE, 'assign')) {
	$tab_list[] = 'assign';
}
if(check_subtab_persmission($dbc, 'optimize', ROLE, 'history')) {
	$tab_list[] = 'history';
}
$tab_list[] = 'assign_past_imports';
if(empty($_GET['tab']) || !in_array($_GET['tab'],$tab_list)) {
	$_GET['tab'] = $tab_list[0];
}
if(!file_exists('macros')) {
	mkdir('macros',0777);
}
$macro_list = [];
$bus_list = [];
$bus_config = explode('#*#',get_config($dbc, 'upload_macro_businesses'));
$cur_macro = [];
$cur_bus = [];
foreach(explode('#*#',get_config($dbc, 'upload_macros')) as $i => $macro) {
	$macro = explode('|',$macro);
	if(!empty($macro[1]) && file_exists('macros/'.$macro[1])) {
		$macro_list[$macro[0]] = [config_safe_str($macro[1]),$macro[2]];
        $bus_list[$macro[0]] = explode('|',$bus_config[$i]);
        if($_GET['macro'] == config_safe_str($macro[1])) {
            $cur_macro = [config_safe_str($macro[1]),$macro[2]];
            $cur_bus = explode('|',$bus_config[$i]);
            foreach(explode(',',get_config($dbc, '%_classification', true, ',')) as $bus_class_list) {
                foreach($cur_bus as &$bus_opt) {
                    if(config_safe_str($bus_class_list) == $bus_opt) {
                        $bus_opt = $bus_class_list;
                    }
                }
            }
        }
	}
} ?>
<div class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe src="../blank_loading_page.php"></iframe>
		</div>
	</div>
	<div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header" style="<?= IFRAME_PAGE ? 'display:none;' : '' ?>">
                <div class="pull-right settings-block">
					<?php if($security['config'] > 0) { ?>
                        <div class='pull-right gap-left'><a href='?settings=macros'><img src='<?= WEBSITE_URL ?>/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30' /></a></div>
                    <?php } ?>
                </div>
                <div class="scale-to-fill">
					<h1 class="gap-left"><a href="?">Trip Optimizer</a><img class="no-toggle statusIcon pull-right no-margin inline-img small" title="" src="" data-original-title=""></h1>
				</div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->
            
			<div class="clearfix"></div>
			<?php include_once('sidebar.php'); ?>
			<div class="main-content-screen scale-to-fill has-main-screen hide-titles-mob">
				<div class="loading_overlay" style="display:none;"><div class="loading_wheel"></div></div>
				<?php if(!empty($_GET['settings']) && $security['config'] > 0) {
					include('field_config.php');
				} else if($_GET['tab'] == 'macros') {
					include('macro.php');
				} else if($_GET['tab'] == 'history') {
					include('history.php');
				} else if($_GET['tab'] == 'assign_past_imports') {
					include('past_work_orders.php');
				} else if($_GET['tab'] == 'assign') {
					include('assign.php');
				} else {
					include('upload.php');
				} ?>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
	</div>
</div>
<?php include('../footer.php');