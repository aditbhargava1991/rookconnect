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
$wcb_invoice = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `patientformid` FROM `user_forms` LEFT JOIN `patientform_pdf` ON `user_forms`.`form_id`=`patientform_pdf`.`form_name` WHERE `user_forms`.`name`='WCB Invoice'"))['patientformid'];
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
<script>
$(document).ready(function() {
	$('.toggle-switch').click(function() {
		$(this).find('img').toggle();
		$(this).find('input').val($(this).find('input').val() == 'No' ? 'Yes' : 'No').change();
	});
});
</script>
<h3>WCB Invoices</h3>
<?php if($wcb_invoice > 0 && $security['edit'] > 0) { ?>
	<a class="btn brand-btn pull-right" href="../Treatment/add_manual.php?patientformid=<?= $wcb_invoice ?>&action=view&projectid=<?= $projectid ?>&from_url=<?= urlencode(WEBSITE_URL.'/Project/projects.php?edit='.$projectid.'&tab=wcb_invoice') ?>">Add Invoice</a>
	<div class="clearfix"></div>
<?php } ?>
<?php $invoice_list = mysqli_query($dbc, "SELECT * FROM `user_forms` LEFT JOIN `patientform_pdf` ON `user_forms`.`form_id`=`patientform_pdf`.`form_name` LEFT JOIN `patient_injury` ON `patientform_pdf`.`injuryid`=`patient_injury`.`injuryid` WHERE `user_forms`.`name`='WCB Invoice' AND ',".$project['clientid'].",' LIKE CONCAT('%,',`patient_injury`.`contactid`,',%')");
if(mysqli_num_rows($invoice_list) > 0) {
	while($invoice = mysqli_fetch_array($invoice_list)) { ?>
		<div class="dashboard-item">
			<h4><a href="../Treatment/<?= $invoice['pdf_path'] ?>">Invoice #<?= $invoice['fieldlevelriskid'] ?> - <?= $invoice['today_date'] ?></a></h4>
			<div class="clearfix"></div>
		</div>
	<?php }
} else {
	echo "<h2>No Invoices Found</h2>";
} ?>
<?php if($wcb_invoice > 0 && $security['edit'] > 0) { ?>
	<a class="btn brand-btn pull-right" href="../Treatment/add_manual.php?patientformid=<?= $wcb_invoice ?>&action=view&&projectid=<?= $projectid ?>from_url=<?= urlencode(WEBSITE_URL.'/Project/projects.php?edit='.$projectid.'&tab=wcb_invoice') ?>">Add Invoice</a>
	<div class="clearfix"></div>
<?php } ?>
<?php include('next_buttons.php'); ?>