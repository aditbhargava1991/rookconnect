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
$field_config = array_filter(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `tickets_dashboard` FROM field_config"))['tickets_dashboard']));
$ticket_type = [];
$_GET['ticket_type'] = filter_var($_GET['ticket_type'],FILTER_SANITIZE_STRING);
foreach(array_filter(explode(',',get_config($dbc, 'ticket_tabs'))) as $type_name) {
	$ticket_type[config_safe_str($type_name)] = $type_name;
}
$ticket_security = get_security($dbc, 'ticket');
$_GET['status'] = empty($_GET['status']) && $ticket_security['search'] > 0 ? (in_array('Unassigned Hide',$tab_config) ? 'active' : 'unassigned') : $_GET['status'];
if(!empty($_GET['status_searcher']) || $_GET['display_all_tickets'] == 'true') {
	$_GET['status'] = '';
}
$ticket_status = explode(',',get_config($dbc,'ticket_status'));
$form = $dbc->query("SELECT * FROM `ticket_pdf` WHERE `id`='".filter_var($_GET['id'],FILTER_SANITIZE_STRING)."'")->fetch_assoc();
$form['file_name'] = config_safe_str($form['pdf_name']); ?>
<script type="text/javascript">
$(document).on('change', 'select[name="status_searcher"]', function() { statusSearch(this); });
function statusSearch(sel) {
	var status = sel.value;
	window.location.href = "?edit=<?= $_GET['edit'] ?>&tab=tickets&status_searcher="+status;
}
function displayAllTickets() {
	window.location.href = "?edit=<?= $_GET['edit'] ?>&tab=tickets&display_all_tickets=true";
}
</script>
<h3><?= $_GET['ticket_type'] != '' ? $ticket_type[$_GET['ticket_type']] : TICKET_TILE ?>: <?= $form['pdf_name'] ?><?php if($ticket_security['edit'] > 0) { ?><a href="../Ticket/index.php?projectid=<?= $projectid ?>&custom_form=<?= $_GET['id'] ?>" class="btn brand-btn pull-right">Create New</a><?php } ?></h3>
<?php $ticket_sort = ' ORDER BY `ticketid` DESC';
switch(get_config($dbc, 'ticket_sorting')) {
	case 'oldest': $ticket_sort = ' ORDER BY `ticketid` ASC'; break;
	case 'project': $ticket_sort = ' ORDER BY `project_name` ASC'; break;
}
$tickets = mysqli_query($dbc, "SELECT `project`.*, `tickets`.*, `tickets`.`status` as ticket_status FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `tickets`.`projectid`='$projectid' AND `tickets`.`deleted`=0 AND `tickets`.`status` != 'Archive' AND '{$_GET['ticket_type']}' IN ('', `tickets`.`ticket_type`) AND `ticketid` IN (SELECT `ticketid` FROM `ticket_pdf_field_values` WHERE `pdf_type`='".filter_var($_GET['id'],FILTER_SANITIZE_STRING)."' AND `deleted`=0) ".$tab_filter.$ticket_sort);
if($ticket_bypass && mysqli_num_rows($tickets) == 1 && $ticket_security['edit'] == 0 && get_config($dbc, 'project_ticket_bypass') == 'bypass' && !($strict_view > 0)) {
	$ticket = mysqli_fetch_array($tickets);
	echo "<script> window.location.replace('../Ticket/ticket_pdf_custom.php?form=trucking_bol&ticketid=".$ticket['ticketid']."'); </script>";
} else {
	while($ticket = mysqli_fetch_array($tickets)) { ?>
		<div class="dashboard-item form-horizontal">
			<h3><a href="../Ticket/download/<?= $form['file_name'] ?>_<?= $ticket['ticketid'] ?>.pdf"><?= get_ticket_label($dbc, $ticket) ?></a>
			<div class="clearfix"></div>
		</div>
	<?php }
} ?>
<div class="clearfix"></div>
<?php include('next_buttons.php'); ?>