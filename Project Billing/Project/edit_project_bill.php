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
$tab_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='$projecttype'"))['config_tabs']),explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='ALL'"))['config_tabs']))));
$wcb_invoice = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `patientformid` FROM `user_forms` LEFT JOIN `patientform_pdf` ON `user_forms`.`form_id`=`patientform_pdf`.`form_name` WHERE `user_forms`.`name`='WCB Invoice'"))['patientformid'];
if(count($tab_config) == 0) {
	$tab_config = explode(',','Path,Information,Details,Documents,Dates,Scope,Estimates,Tickets,Work Orders,Tasks,Checklists,Email,Phone,Reminders,Agendas,Meetings,Gantt,Profit,Report Checklist,Billing,Field Service Tickets,Purchase Orders,Invoices');
} ?>
<script>
function create_object(type) {
	if(type == 'wcb_invoice' && '<?= $wcb_invoice ?>' > 0) {
		window.top.location.href = '../Treatment/add_manual.php?patientformid=<?= $wcb_invoice ?>&action=view&projectid=<?= $_GET['projectid'] ?>&lines=<?= urlencode($_GET['lines']) ?>&from_url=<?= urlencode(WEBSITE_URL.'/Project/projects.php?edit='.$_GET['projectid'].'&tab=wcb_invoice') ?>';
	} else {
		$.ajax({
			url: 'projects_ajax.php?action=create_bill',
			method: 'POST',
			data: {
				projectid: '<?= $_GET['projectid'] ?>',
				object: type,
				bill_lines: '<?= $_GET['lines'] ?>'
			},
			success: function(response) {
				window.top.location.href = response;
			}
		});
	}
}
</script>
<div class="col-sm-12 text-center triple-padded main-screen-white">
	<?php if($security['edit'] > 0) { ?>
		<?php if(in_array('Field Service Tickets',$tab_config)) { ?>
			<a href="" onclick="create_object('field_service_ticket'); return false" class="btn brand-btn" >Field Service Ticket</a>
		<?php } ?>
		<?php if(in_array('Purchase Orders',$tab_config)) { ?>
			<a href="" onclick="create_object('purchase_order'); return false" class="btn brand-btn" >Purchase Order</a>
		<?php } ?>
		<?php if(in_array('Work Tickets',$tab_config)) { ?>
			<a href="" onclick="create_object('work_ticket'); return false" class="btn brand-btn" >Work Ticket</a>
		<?php } ?>
		<?php if(in_array('Invoices',$tab_config)) { ?>
			<a href="" onclick="create_object('invoice'); return false" class="btn brand-btn" >Invoice</a>
		<?php } ?>
		<?php if(in_array('WCB Invoices',$tab_config)) { ?>
			<a href="" onclick="create_object('wcb_invoice'); return false" class="btn brand-btn" >WCB Invoice</a>
		<?php } ?>
	<?php } ?>
	<a href="" onclick="window.location.reload(); return false" class="btn brand-btn pull-right" >Cancel</a>
</div>