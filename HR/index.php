<?php include_once('../include.php'); ?>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.main-screen .main-screen').is(':visible') && $('.sidebar').is(':visible')) {
			var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
			if(available_height > 300) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
			}
            var sidebar_height = $('.tile-sidebar').outerHeight(true);
            $('.has-main-screen .main-screen').css('min-height', sidebar_height);
		} else {
			$('.main-screen .main-screen').css('height','auto');
		}
	}).resize();
});
</script>
<?php include_once('../navigation.php');
$tile = empty($_GET['tile_name']) ? 'hr' : filter_var($_GET['tile_name'],FILTER_SANITIZE_STRING);
$security = get_security($dbc, $tile);
$hr_summary = explode(',',get_config($dbc,'hr_summary'));
if($security['config'] < 1) {
	$hr_summary = array_filter($hr_summary,function($str) { return strpos($str,'admin_') === FALSE; });
}
if(empty($_GET['tab']) && count($hr_summary > 0)) {
	$_GET['tab'] = 'summary';
}
$categories = [];
$pin_levels = implode(",%' OR `pinned` LIKE '%,",array_filter(explode(',',ROLE)));
$pincount = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `rows` FROM (SELECT `hrid` FROM `hr` WHERE `deleted`=0 AND (CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%') UNION SELECT `manualtypeid` FROM `manuals` WHERE `deleted`=0 AND (CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%')) `num`"))['rows'];
if($pincount > 0 && !in_array('individual_pin',$hr_summary)) {
	$categories['pinned'] = 'Pinned';
}
if(!in_array('individual_fave',$hr_summary)) {
	$categories['favourites'] = 'Favourites';
}
foreach(explode(',',get_config($dbc, 'hr_tabs')) as $cat) {
	$categories[config_safe_str($cat)] = $cat;
}
$tab = $_GET['tab'] == '' ? ($tile == 'hr' ? (in_array('Pinned',$categories) ? 'pinned' : 'favourites') : $tile) : filter_var($_GET['tab'],FILTER_SANITIZE_STRING);
$label = $tile == 'hr' ? 'HR' : $categories[$tile];
if($_GET['request_update'] == 1) {
	$tab = 'request_update';
} else if($_GET['reports'] == 'view') {
	$tab = 'reporting';
} else if(isset($_GET['hr_edit']) || isset($_GET['manual_edit'])) {
	$tab = 'editforms';
} else if(isset($_GET['hr']) || isset($_GET['manual'])) {
	$tab = 'viewforms';
} else if(isset($_GET['settings'])) {
	$tab = 'configure';
} else if(isset($_GET['performance_review'])) {
	$tab = 'performance_review';
} else if($_GET['tab'] == 'summary') {
	$tab = 'summary';
}
checkAuthorised('hr');

if(!empty($_GET['hr'])) {
    $user_form_id = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM hr WHERE hrid='".$_GET['hr']."'"))['user_form_id'];
    if($user_form_id > 0) {
        $user_form_layout = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['form_layout'];
        $user_form_layout = !empty($user_form_layout) ? $user_form_layout : 'Accordions';
    }
}
if($_GET['performance_review'] == 'add' && !empty($_GET['form_id'])) {
	$user_form_id = $_GET['form_id'];
    if($user_form_id > 0) {
        $user_form_layout = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['form_layout'];
        $user_form_layout = !empty($user_form_layout) ? $user_form_layout : 'Accordions';
    }
} ?>
<div class="container" <?= $user_form_layout == 'Sidebar' ? 'style="padding: 0; margin: 0;"' : '' ?>>
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="ticket_iframe" src=""></iframe>
		</div>
	</div>
	<div class="row">
		<div <?= $user_form_layout != 'Sidebar' ? 'class="main-screen"' : '' ?>>
			<?php if(!IFRAME_PAGE) { ?>
				<div class="tile-header" <?= $user_form_layout == 'Sidebar' ? 'style="border-bottom: 1px solid #E1E1E1;"' : '' ?>>
					<div class="pull-right settings-block"><?php if($security['config'] > 0) {
							echo "<div class='pull-right gap-left'><a href='?settings=tabs&tile_name=$tile'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30' /></a></div>";
						}
						if($security['edit'] > 0) {
							echo "<div class='pull-right gap-left'><a href='?hr_edit=0&tile_name=$tile'><img src='".WEBSITE_URL."/img/icons/ROOK-add-icon.png' class='inline-img show-on-mob' /><button class='btn brand-btn hide-on-mobile'>Add Form</button></a></div>";
                            $back_url = $_GET['tab'];
							echo "<div class='pull-right gap-left'><a href='?manual_edit=0&tile_name=$tile&tab=$back_url'><img src='".WEBSITE_URL."/img/icons/ROOK-add-icon.png' class='inline-img show-on-mob' /><button class='btn brand-btn hide-on-mobile'>Add Manual</button></a></div>";
						} ?>
                        <?php if(vuaed_visible_function($dbc, 'preformance_review')) { ?>
                            <a href="?performance_review=add" class="btn brand-btn pull-right">Add Performance Review</a>
                        <?php } ?>
					</div>
					<div class="scale-to-fill"><h1 class="gap-left"><a href="?tile_name=<?= $tile ?>"><span class="hide-on-mobile"><?= $label ?></span><span class="show-on-mob">HR</span></a></h1></div>
					<div class="clearfix"></div>
				</div><!-- .tile-header -->
			<?php } ?>

			<div class="clearfix"></div>
			<?php $device = new Mobile_Detect;
			if($device->isMobile) {
				include('mobile_hr.php');
			} else if($_GET['request_update'] == 1) {
				include('sidebar.php');
				include('request_update.php');
			} else if(!empty($_GET['settings']) && $security['config'] > 0) {
				include('field_config.php');
			} else if(isset($_GET['hr'])) {
			    if($user_form_layout == 'Sidebar') {
			    	include('user_forms_sidebar.php');
			    } else {
					include('sidebar.php');
			    }
				include('fill_hr.php');
			} else if(isset($_GET['hr_edit'])) {
				checkAuthorised($tile);
                $tab = config_safe_str(get_field_value('category','hr','hrid',$_GET['hr_edit']));
				include('sidebar.php');
				include('edit_hr.php');
			} else if(isset($_GET['manual'])) {
				include('sidebar.php');
				include('fill_manual.php');
			} else if(isset($_GET['manual_edit'])) {
				checkAuthorised($tile);
                $tab = config_safe_str(get_field_value('category','manuals','manualtypeid',$_GET['manual_edit']));
				include('sidebar.php');
				include('edit_manual.php');
			} else if(isset($_GET['manualid_pdf'])) {
				include('manual_pdf.php');
			} else if(isset($_GET['hrid_pdf'])) {
				include('hr_pdf.php');
			} else if(isset($_GET['reports'])) {
				checkAuthorised($tile, 'reporting');
				include('sidebar.php');
				include('report_hr.php');
			} else if($_GET['performance_review'] == 'list') {
				checkAuthorised('preformance_review');
				include('sidebar.php');
				include('list_pr.php');
			} else if($_GET['performance_review'] == 'add') {
				checkAuthorised('preformance_review');
			    if($user_form_layout == 'Sidebar') {
			    	include('user_forms_sidebar.php');
			    } else {
					include('sidebar.php');
			    }
				include('add_pr.php');
			} else if($_GET['tab'] == 'summary') {
				include('sidebar.php');
				include('summary.php');
			} else if($_GET['tab'] == 'search') {
				include('sidebar.php');
				include('search.php');
			} else {
				checkAuthorised($tile, $tab_cat);
				$tab_cat = $tab;
				include('sidebar.php');
				include('list_hr.php');
			} ?>
		</div>
	</div>
</div>
<?php include('../footer.php'); ?>