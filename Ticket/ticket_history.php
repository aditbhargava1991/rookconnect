<?php
/*
Dashboard
*/
include_once('../include.php');
error_reporting();
$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid' AND '$ticketid' > 0"));
$ticket_type = $get_ticket['ticket_type'];
$businessid = $get_ticket['businessid'];
$clientid = $get_ticket['clientid'];
$projectid = $get_ticket['projectid'];
$projecttype = get_project($dbc, $get_ticket['projectid'], 'projecttype');

$ticket_type_label = '';
$ticket_tabs = array_filter(explode(',',get_config($dbc, 'ticket_tabs')));
foreach($ticket_tabs as $ticket_tab) {
	if(config_safe_str($ticket_tab) == $ticket_type) {
		$ticket_type_label = $ticket_tab;
	}
}

$projecttype_label = '';
$project_tabs = array_filter(explode(',',get_config($dbc, 'project_tabs')));
foreach($project_tabs as $project_tab) {
	if(config_safe_str($project_tab) == $projecttype) {
		$projecttype_label = $project_tab;
	}
}

$titles = [
	'Ticket History'=>TICKET_NOUN.' History',
	'Customer History Business Ticket Type'=>'Business - Last 5 by '.TICKET_NOUN.' Tab'.(!empty($ticket_type_label) ? ' ('.$ticket_type_label.')' : ''),
	'Customer History Business Project Type'=>'Business - Last 5 by '.PROJECT_NOUN.' Tab'.(!empty($projecttype_label) ? ' ('.$projecttype_label.')' : ''),
	'Customer History Business Ticket Project Type'=>'Business - Last 5 by '.TICKET_NOUN.' Tab'.(!empty($ticket_type_label) ? ' ('.$ticket_type_label.')' : '').' & '.PROJECT_NOUN.' Tab'.(!empty($projecttype_label) ? ' ('.$projecttype_label.')' : ''),
	'Customer History Customer Ticket Type'=>'Client - Last 5 by '.TICKET_NOUN.' Tab'.(!empty($ticket_type_label) ? ' ('.$ticket_type_label.')' : ''),
	'Customer History Customer Project Type'=>'Client - Last 5 by '.PROJECT_NOUN.' Tab'.(!empty($projecttype_label) ? ' ('.$projecttype_label.')' : ''),
	'Customer History Customer Ticket Project Type'=>'Client - Last 5 by '.TICKET_NOUN.' Tab'.(!empty($ticket_type_label) ? ' ('.$ticket_type_label.')' : '').' & '.PROJECT_NOUN.' Tab'.(!empty($projecttype_label) ? ' ('.$projecttype_label.')' : '')
];

?>
<div class="container">
	<div class="row">
    	<h3 class="inline">History - <?= get_ticket_label($dbc, $get_ticket) ?></h3>
        <div class="pull-right gap-top"><a href=""><img src="../img/icons/cancel.png" alt="Close" title="Close" class="inline-img" /></a></div>
        <div class="clearfix"></div>
        <hr />

        <?php $history_fields = array_filter(explode(',',get_config($dbc, 'ticket_history_fields')));
        if(empty($history_fields)) {
        	$history_fields = ['Ticket History'];
        }
        $field_count = 0;
        foreach($history_fields as $history_field) {
        	if(array_key_exists($history_field, $titles)) {
        		$field_count++;
        	}
        }
    	if($field_count > 1) { ?>
	        <div class="main-screen" style="border: 0;">
				<div class="panel-group block-panels col-xs-12 form-horizontal" style="background-color: #fff; padding: 0; margin-left: 5px; width: calc(100% - 10px); height: 100%;" id="mobile_tabs">
    	<?php }
        foreach($history_fields as $history_field) {
        	if(array_key_exists($history_field, $titles)) {
	        	if($field_count > 1) { ?>
	        		<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_<?= config_safe_str($history_field) ?>">
									<?= $titles[$history_field] ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_<?= config_safe_str($history_field) ?>" class="panel-collapse collapse">
							<div class="panel-body">
	        	<?php } else { ?>
	        		<h4><?= $titles[$history_field] ?></h4>
	        	<?php }
	        	if($history_field == 'Ticket History') { ?>
					<table class="table table-bordered">
						<tr>
							<th>Date</th>
							<th>User</th>
							<th>Description</th>
						</tr>
						<tr>
							<?php $ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `created_by`, `created_date` FROM `tickets` WHERE `ticketid`='$ticketid' AND `ticketid` > 0"));
							$name = get_contact($dbc, $ticket['created_by']);
							if($name == '' || $name == '-') {
								$name = 'Admin';
							} ?>
							<td data-title="Date"><?= convert_timestamp_mysql($dbc, $ticket['created_date'], true) ?></td>
							<td data-title="User"><?= $name ?></td>
							<td data-title="Description"><?= TICKET_NOUN ?> Created</td>
						</tr>
						<?php $result_tickets = mysqli_query($dbc, "SELECT * FROM ticket_history WHERE ticketid ='$ticketid' AND `ticketid` > 0 ORDER BY `date` ASC");
						while($history = mysqli_fetch_assoc($result_tickets)) {
							$name = get_contact($dbc, $history['userid']);
							if($name == '' || $name == '-') {
								$name = 'Admin';
							}
							if ( strpos($history['description'], 'sign_off_signature updated to')!==false ) {
								$description = substr($history['description'], 0, strpos($history['description'], 'sign_off_signature updated to')) . 'sign_off_signature updated';
							} else {
								$description = $history['description'];
							} ?>
							<tr>
								<td data-title="Date"><?= convert_timestamp_mysql($dbc, $history['date'], true) ?></td>
								<td data-title="User"><?= $name ?></td>
								<td data-title="Description"><?= html_entity_decode($description); ?></td>
							</tr>
						<?php } ?>
						<tr>
							<?php $ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `created_by`, `created_date` FROM `tickets` WHERE `ticketid`='$ticketid' AND `ticketid` > 0"));
							$name = get_contact($dbc, $ticket['created_by']);
							if($name == '' || $name == '-') {
								$name = 'Admin';
							} ?>
							<td data-title="Date"><?= $ticket['created_date'] ?></td>
							<td data-title="User"><?= $name ?></td>
							<td data-title="Description"><?= TICKET_NOUN ?> Created</td>
						</tr>
					</table>
	        	<?php } else {
	        		echo '<div class="customer_div">';
	        		include('../Ticket/ticket_customer_history.php');
	        		echo '</div>';
	        	}
	        	if($field_count > 1) { ?>
		        			</div>
		        		</div>
		        	</div>
	        	<?php } else { ?>
	        		<hr />
	        	<?php }
	        }
	    }
    	if($field_count > 1) { ?>
				</div>
			</div>
    	<?php } ?>
	</div>
</div>
<?php
function get_ticket_block($dbc, $ticket, $history_fields) {
	$html = '';
	$field_exists = false;
	if(in_array('Customer History Field Service Template', $history_fields)) {
		$field_exists = true;
		$html .= '<div class="clearfix"></div>
			<div class="col-sm-12">Service Template: '.mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `services_service_templates` WHERE `templateid` = '".$ticket['service_templateid']."' AND '".$ticket['service_templateid']."' > 0"))['name'].'</div>';
	}
	if(in_array('Customer History Field Display Notes', $history_fields)) {
		$field_exists = true;
		$notes_html = '';
		$ticket_notes = mysqli_query($dbc, "SELECT `ticket_comment`.*, `tickets`.`ticket_type` FROM ticket_comment LEFT JOIN `tickets` ON `ticket_comment`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_comment`.ticketid='".$ticket['ticketid']."' AND `ticket_comment`.type='note' AND `ticket_comment`.`deleted`=0 ORDER BY ticketcommid DESC");
		while($row = mysqli_fetch_assoc($ticket_notes)) {
			$notes_html .= profile_id($dbc, $row['created_by'], false);
			$notes_html .= '<div class="pull-right" style="width: calc(100% - 3.5em);">'.html_entity_decode($row['comment'].$row['note']);
			$notes_html .= "<em>Added by ".get_contact($dbc, $row['created_by'])." at ".$row['note_date'].$row['created_date'];
			$notes_html .= "</em>";
			$notes_html .= '</div><div class="clearfix"></div><hr>';
		}
		$html .= '<div class="clearfix"></div>
			<div class="col-sm-12">Notes: '.$notes_html.'</div>';
	}

	$html = '<div class="block-group"><a style="color:black !important;" href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].(empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']).'"><b style="margin-top: 0;">'.get_ticket_label($dbc, $ticket).'</a></b>'.$html.'<div class="clearfix"></div></div>';
	return $html;
}