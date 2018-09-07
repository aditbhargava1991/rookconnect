<?php include_once('../include.php');
include('../Safety/field_list.php'); ?>
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
$categories = [];
$pin_levels = implode(",%' OR `pinned` LIKE '%,",array_filter(explode(',',ROLE)));
$pincount = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `rows` FROM (SELECT `hrid` FROM `hr` WHERE `deleted`=0 AND (CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%') UNION SELECT `manualtypeid` FROM `manuals` WHERE `deleted`=0 AND (CONCAT(',',`pinned`,',') LIKE '%,ALL,%' OR CONCAT(',',`pinned`,',') LIKE '%,".$pin_levels.",%' OR CONCAT(',',`pinned`,',') LIKE '%,".$_SESSION['contactid'].",%')) `num`"))['rows'];
if($pincount > 0) {
	$categories['pinned'] = 'Pinned';
}
$categories['favourites'] = 'Favourites';
foreach(explode(',',get_config($dbc, 'safety_dashboard')) as $cat) {
	$categories[config_safe_str($cat)] = $cat;
}
$bypass_cat = explode(',',get_config($dbc, 'safety_bypass_list'));
// $site_list = array_filter(explode(',',get_config($dbc, 'safety_main_site_tabs')));
$site_list = [];
foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `site_name`, `display_name` FROM `contacts` WHERE `category`='".SITES_CAT."' AND `deleted`=0 AND `status` > 0")) as $site) {
	$site_list[$site['contactid']] = $site['display_name'] != '' ? $site['display_name'] : $site['site_name'];
}
$tab = $_GET['tab'] == '' ? (in_array('Pinned',$categories) ? 'pinned' : 'favourites') : filter_var($_GET['tab'],FILTER_SANITIZE_STRING);
$tab_name = $categories[$tab];
$tab_cat_name = filter_var($_GET['cat_name'],FILTER_SANITIZE_STRING);
$site = filter_var($_GET['site'],FILTER_SANITIZE_STRING);
$security = get_security($dbc, 'safety');
$label = 'Safety: '.$categories[$tab];
if($_GET['reports'] == 'view') {
	$label = 'Safety: Reports';
	$tab = 'reporting';
} else if(isset($_GET['safetyid']) && $_GET['action'] == 'view') {
	$label = 'Safety: Review';
	$tab = 'viewforms';
} else if(isset($_GET['safetyid'])) {
	$label = 'Safety: Configure';
	$tab = 'editforms';
} else if(isset($_GET['settings'])) {
	$label = 'Safety: Settings';
	$tab = 'configure';
} else if($_GET['tab'] == 'driving_log') {
	$label = 'Safety: Driving Log';
	$tab = 'driving_log';
} else if($_GET['tab'] == 'incident_reports') {
	$label = 'Safety: '.INC_REP_TILE;
	$tab = 'incident_reports';
}
checkAuthorised('safety');

if(!empty($_GET['safetyid']) && $_GET['action'] != 'edit') {
    $user_form_id = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM safety WHERE safetyid='".$_GET['safetyid']."'"))['user_form_id'];
    if($user_form_id > 0) {
        $user_form_layout = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['form_layout'];
        $user_form_layout = !empty($user_form_layout) ? $user_form_layout : 'Accordions';
    }
} ?>

<?php if(!IFRAME_PAGE) { ?>
<div class="container" <?= $user_form_layout == 'Sidebar' ? 'style="padding: 0; margin: 0;"' : '' ?>>
	<div class="iframe_overlay" style="margin-top: -20px; margin-left: -15px; display:none;">
		<div class="iframe" style="position: relative; float: right; width: 50%; min-width: 25em; max-width: 100%; left: 0px;">
			<div class="iframe_loading">Loading...</div>
			<iframe name="calendar_iframe" src="<?= WEBSITE_URL ?>/blank_loading_page.php"></iframe>
		</div>
	</div>
	<div class="row">
		<div <?= $user_form_layout != 'Sidebar' ? 'class="main-screen"' : '' ?>>
			<div class="tile-header" <?= $user_form_layout == 'Sidebar' ? 'style="border-bottom: 1px solid #E1E1E1;"' : '' ?>>
                <div class="pull-right settings-block"><?php if($security['config'] > 0) {
                        echo "<div class='pull-right gap-left'><a href='?settings=tabs'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30' /></a></div>";
                    }
					if(check_subtab_persmission($dbc, 'safety', ROLE, 'reporting')) {
                        echo "<div class='pull-right gap-left'><a href='?reports=view'><button class='btn brand-btn ".($_GET['reports'] == 'view' ? "active_tab" : "")." icon-pie-chart'>Reporting</button></a></div>";
                    }
					if($security['edit'] > 0) {
                        echo "<div class='pull-right gap-left'><a href='?safetyid=0&action=edit&form=Manual'><img src='".WEBSITE_URL."/img/icons/ROOK-add-icon.png' class='inline-img show-on-mob' /><button class='btn brand-btn hide-on-mobile'>New Manual</button></a></div>";
                        echo "<div class='pull-right gap-left'><a href='?safetyid=0&action=edit'><img src='".WEBSITE_URL."/img/icons/ROOK-add-icon.png' class='inline-img show-on-mob' /><button class='btn brand-btn hide-on-mobile'>New Form</button></a></div>";
                    } ?>
                    <img class="no-toggle statusIcon pull-right no-margin inline-img" title="" src="" />
                </div>
                <div class="scale-to-fill"><h1 class="gap-left"><a href="?"><span class="hide-on-mobile"><?= $label ?></span><span class="show-on-mob">Safety</span></a></h1></div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->
            
			<div class="clearfix"></div>
<?php } ?>
			<?php $device = new Mobile_Detect;
			if($device->isMobile) {
				include('safety_mobile.php');
			} else if(!empty($_GET['settings']) && $security['config'] > 0) {
				include('field_config.php');
			} else if(isset($_GET['safetyid'])) {
				if(!IFRAME_PAGE) {
				    if($user_form_layout == 'Sidebar') {
				    	include('safety_user_forms_sidebar.php');
				    } else {
						include('safety_sidebar.php');
				    }
				}
				include('safety_form.php');
			} else if(isset($_GET['safety_pdf'])) {
				include('safety_pdf.php');
			} else if(isset($_GET['reports'])) {
				checkAuthorised('safety', 'reporting');
				include('safety_sidebar.php');
				include('safety_reporting.php');
			} else if($_GET['tab'] == 'incident_reports') {
				include('safety_sidebar.php');
				include('safety_incident_reports.php');
			} else if($_GET['tab'] == 'driving_log') {
				include('safety_sidebar.php');
				include('safety_driving_log.php');
			} else {
				checkAuthorised('safety', $tab_cat);
				$tab_cat = $tab;
				include('safety_sidebar.php');
				include('safety_list.php');
			} ?>
<?php if(!IFRAME_PAGE) { ?>
		</div>
	</div>
</div>
<?php include('../footer.php'); ?>
<?php } ?>