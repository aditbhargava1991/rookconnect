<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project'); ?>
<h3 id="head_phone">Phone</h3>
<div class="notice double-gap-top double-gap-bottom popover-examples">
    <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
    <div class="col-sm-11"><span class="notice-name">NOTE: </span>View all phone communication sent through the software for this project.</div>
    <div class="clearfix"></div>
</div>
<!--
<div class="tab-container mobile-100-container">
	<?php if ( check_subtab_persmission($dbc, 'phone_communication', ROLE, 'internal') === TRUE ) { ?>
		<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Contains all internal phone communication, including follow up by/date and status, which can be edited from here."><img src="../img/info.png" width="20"></a></span>
		<a href="?edit=<?php echo $projectid; ?>&tab=phone&category=Internal"><button type="button" class="mobile-100 btn brand-btn mobile-block <?php echo ((empty($_GET['category']) || $_GET['category'] == 'Internal') ? 'active_tab' : ''); ?>">Internal</button></a>&nbsp;&nbsp;
		</div>
	<?php } ?>
	<?php if ( check_subtab_persmission($dbc, 'phone_communication', ROLE, 'external') === TRUE ) { ?>
		<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Contains all external phone communication, including follow up by/date and status, which can be edited from here."><img src="../img/info.png" width="20"></a></span>
		<a href="?edit=<?php echo $projectid; ?>&tab=phone&category=External"><button type="button" class="mobile-100 btn brand-btn mobile-block <?php echo ((!empty($_GET['category']) && $_GET['category'] == 'External') ? 'active_tab' : ''); ?>">External</button></a>&nbsp;&nbsp;
		</div>
	<?php } ?>
	<?php if ( check_subtab_persmission($dbc, 'phone_communication', ROLE, 'log') === TRUE ) { ?>
		<div class="pull-left"><span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="All phone communication tied to this project is logged here."><img src="../img/info.png" width="20"></a></span>
		<a href="?edit=<?php echo $projectid; ?>&tab=phone&category=Log"><button type="button" class="btn mobile-100 brand-btn mobile-block <?php echo ((!empty($_GET['category']) && $_GET['category'] == 'Log') ? 'active_tab' : ''); ?>">Log</button></a>&nbsp;&nbsp;
		</div><br /><br />
	<?php } ?>
</div>
-->

<?php if($_GET['category'] == 'Log') {
	include('../Phone Communication/log_display.php');
} else {
	$_GET['type'] = (empty($_GET['category']) ? 'Internal' : $_GET['category']);
	include('../Phone Communication/phone_list.php');
} ?>