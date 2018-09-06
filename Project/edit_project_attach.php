<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
} ?>
<script>
var submitted = false;
$(document).ready(function() {
	if($('.btn.brand-btn').length <= 2) {
		$('.btn.brand-btn').first().click();
	}
});
function create_object(type) {
	if(!submitted) {
		submitted = true;
		$.ajax({
			url: 'projects_ajax.php?action=create_from_scope',
			method: 'POST',
			data: {
				projectid: '<?= $_GET['projectid'] ?>',
				milestone: '<?= $_GET['milestone'] ?>',
				object: type,
				scope_lines: <?= $_GET['lines'] ?>
			},
			success: function(response) {
				window.top.location.href = response;
			}
		});
	}
}
</script>
<?php $tab_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='$projecttype'"))['config_tabs']),explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='ALL'"))['config_tabs']))));
if(count($tab_config) == 0) {
	$tab_config = explode(',','Path,Information,Details,Documents,Dates,Scope,Estimates,Tickets,Work Orders,Tasks,Checklists,Email,Phone,Reminders,Agendas,Meetings,Gantt,Profit,Report Checklist,Billing,Field Service Tickets,Purchase Orders,Invoices');
}
if($security['edit'] > 0) {
	if(in_array('Tickets',$tab_config)) { ?>
		<a href="" onclick="create_object('ticket'); return false" class="btn brand-btn center" ><?= TICKET_NOUN ?></a>
	<?php }
	if(in_array('Work Orders',$tab_config)) { ?>
		<a href="" onclick="create_object('workorder'); return false" class="btn brand-btn center" >Work Order</a>
	<?php }
	if(in_array('Tasks',$tab_config)) { ?>
		<a href="" onclick="create_object('task'); return false" class="btn brand-btn center" ><?= TASK_TILE ?></a>
	<?php }
	if(in_array('Checklists',$tab_config)) { ?>
		<a href="" onclick="create_object('checklist'); return false" class="btn brand-btn center" >Checklist</a>
	<?php }
} ?>
<a href="" onclick="window.location.reload(); return false" class="btn brand-btn center" >Cancel</a>