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
$ticket_status = explode(',',get_config($dbc,'ticket_status')); ?>
<script type="text/javascript">
$(document).on('change', 'select[name="status_searcher"]', function() { statusSearch(this); });
function statusSearch(sel) {
	var status = sel.value;
	window.location.href = "?edit=<?= $_GET['edit'] ?>&tab=tickets&status_searcher="+status;
}
function displayAllTickets() {
	window.location.href = "?edit=<?= $_GET['edit'] ?>&tab=tickets&display_all_tickets=true";
}
function setTotalBudgetTime(input) {
	$.ajax({
		type: "POST",
		url: "../Ticket/ticket_ajax_all.php?action=update_ticket_total_budget_time",
		data: { ticketid: $(input).data('id'), time: $(input).val() },
		dataType: "html",
		success: function(response){
			if(response != '') {
				$(input).closest('.dashboard-item').find('.total_budget_time_icon').attr('title', response).show();
			} else {
				$(input).closest('.dashboard-item').find('.total_budget_time_icon').attr('title', response).hide();
			}
		}
	});
}
</script>
<h3><!-- <?= $_GET['ticket_type'] != '' ? $ticket_type[$_GET['ticket_type']] : TICKET_TILE ?> -->&nbsp;
	<?php if(in_array('Export',$field_config) && check_subtab_persmission($dbc, 'ticket', ROLE, 'export') === TRUE && !($strict_view > 0)) { ?>
		<a href="../Ticket/index.php?tab=export&tile_name=&projectid=<?= $_GET['edit'] ?>&from=<?= urlencode(WEBSITE_URL.'/Project/projects.php?edit='.$_GET['edit'].'&tab=tickets') ?>" class="btn brand-btn pull-right">Import <?= TICKET_TILE ?></a>
		<form style="display: inline;" action="../Ticket/index.php?tab=export&tile_name=" method="POST" enctype="multipart/form-data">
			<input type="hidden" name="export_projectid" value="<?= $_GET['edit'] ?>">
			<button name="export" value="ALL" type="submit" class="btn brand-btn pull-right">Export <?= PROJECT_NOUN ?> <?= TICKET_TILE ?></button>
		</form>
	<?php } ?>
	<?php if($ticket_security['edit'] > 0) {
        $url_ticket_type = filter_var($_GET['ticket_type'], FILTER_SANITIZE_STRING); ?>
		<a href="../Ticket/index.php?edit=0&type=<?= $url_ticket_type ?>&projectid=<?= $projectid ?>&from=<?= urlencode(WEBSITE_URL.'/Project/projects.php?edit='.$_GET['edit']) ?>" class="btn brand-btn pull-right">New <?= TICKET_NOUN ?></a>
	<?php } ?>
	<?php if($ticket_security['search'] > 0) { ?>
		<a href="?edit=<?= $projectid ?>&tab=tickets&ticket_type=<?= $_GET['ticket_type'] ?>&status=archive" class="hide-titles-mob <?= $_GET['status'] == 'archive' ? 'active_tab' : '' ?> btn brand-btn pull-right">Archived</a>
		<?php if(in_array('Unassigned Hide',$tab_config)) { ?>
			<a href="?edit=<?= $projectid ?>&tab=tickets&ticket_type=<?= $_GET['ticket_type'] ?>&status=active" class="hide-titles-mob <?= $_GET['status'] == 'active' ? 'active_tab' : '' ?> btn brand-btn pull-right">Active</a>
		<?php } else { ?>
			<a href="?edit=<?= $projectid ?>&tab=tickets&ticket_type=<?= $_GET['ticket_type'] ?>&status=assigned" class="hide-titles-mob <?= $_GET['status'] == 'assigned' ? 'active_tab' : '' ?> btn brand-btn pull-right">Scheduled</a>
			<a href="?edit=<?= $projectid ?>&tab=tickets&ticket_type=<?= $_GET['ticket_type'] ?>&status=unassigned" class="hide-titles-mob <?= $_GET['status'] == 'unassigned' ? 'active_tab' : '' ?> btn brand-btn pull-right">Unassigned</a>
		<?php } ?>
	<?php } ?>
	<?php if($ticket_security['search'] > 0 && get_config($dbc, 'ticket_status_search') == 'dropdown') { ?>
		<div class="col-sm-3 pull-right smaller">
			<select data-placeholder="Select a <?= TICKET_NOUN ?> Status" name="status_searcher" id="" class="chosen-select-deselect form-control input-sm">
			  <option value=""></option>
			  <?php foreach ($ticket_status as $cat_tab) {
					$count_query = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`tickets`.`status`) as cat_count FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `tickets`.`projectid`='$projectid' AND `tickets`.`deleted`=0 AND '{$_GET['ticket_type']}' IN ('', `tickets`.`ticket_type`) AND `tickets`.`status` = '$cat_tab'"));
					echo "<option ".($_GET['status_searcher'] == $cat_tab ? 'selected' : '')." value='".$cat_tab."'>".$cat_tab.' ('.$count_query['cat_count'].')</option>';
				} ?>
			</select>
		</div>
	<?php } ?>
	</h3>
    <div class="notice double-gap-top double-gap-bottom popover-examples">
        <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11"><span class="notice-name">NOTE: </span>View and edit all <?= $_GET['status'] == 'archive' ? 'Archived ' : ($_GET['status'] == 'active' ? 'Active ' : ($_GET['status'] == 'unassigned' ? 'Unassigned ' : ($_GET['status'] == 'assigned' ? 'Scheduled ' : $_GET['status_searcher'].' '))) ?><?= $_GET['ticket_type'] != '' ? $ticket_type[$_GET['ticket_type']] : strtolower(TICKET_TILE) ?> that have been created for this project from here.</div>
        <div class="clearfix"></div>
    </div>
<?php $milestones = explode('#*#',mysqli_fetch_array(mysqli_query($dbc, "SELECT `milestone` FROM `project_path_milestone` WHERE `project_path`='".$project['project_path']."'"))['milestone']);
$tab_filter = '';
if($_GET['status'] == 'archive') {
	$tab_filter = "AND `tickets`.`status`='Archive'";
} else if($_GET['status'] == 'active') {
	$tab_filter = "AND `tickets`.`status`!='Archive'";
} else if($_GET['status'] == 'assigned') {
	$tab_filter = "AND `tickets`.`status`!='Archive' AND (`tickets`.`status` != '' AND IFNULL(`tickets`.milestone_timeline,'') IN ('".implode("','",$milestones)."') AND IFNULL(`tickets`.to_do_date,'0000-00-00') != '0000-00-00' AND REPLACE(IFNULL(`tickets`.contactid,''),',','') != '')";
} else if($_GET['status'] == 'unassigned') {
	$tab_filter = "AND `tickets`.`status`!='Archive' AND (`tickets`.`status` = '' OR IFNULL(`tickets`.milestone_timeline,'') NOT IN ('".implode("','",$milestones)."') OR IFNULL(`tickets`.to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(`tickets`.contactid,''),',','') = '')";
} else if(!empty($_GET['status_searcher'])) {
	$tab_filter = "AND `tickets`.`status` = '".$_GET['status_searcher']."'";
} else if($_GET['display_all_tickets'] == 'true') {
	$tab_filter = '';
}
$ticket_sort = ' ORDER BY `ticketid` DESC';
switch(get_config($dbc, 'ticket_sorting')) {
	case 'oldest': $ticket_sort = ' ORDER BY `ticketid` ASC'; break;
	case 'project': $ticket_sort = ' ORDER BY `project_name` ASC'; break;
}
$tickets = mysqli_query($dbc, "SELECT `project`.*, `tickets`.*, `tickets`.`status` as ticket_status FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid`=`project`.`projectid` WHERE `tickets`.`projectid`='$projectid' AND `tickets`.`deleted`=0 AND '{$_GET['ticket_type']}' IN ('', `tickets`.`ticket_type`) ".$tab_filter.$ticket_sort);
if($ticket_bypass && mysqli_num_rows($tickets) == 1 && $ticket_security['edit'] == 0 && get_config($dbc, 'project_ticket_bypass') == 'bypass') {
	$ticket = mysqli_fetch_array($tickets);
	echo "<script> window.location.replace('../Ticket/index.php?edit=".$ticket['ticketid']."'); </script>";
} else {
	if($tickets->num_rows > 0) {
		while($ticket = mysqli_fetch_array($tickets)) {
			$status_icon = get_ticket_status_icon($dbc, $ticket['status']);
			if(!empty($status_icon)) {
                if($status_icon == 'initials') {
                    $icon_img = '<span class="id-circle-large pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($ticket['status']).'</span>';
                } else {
                    $icon_img = '<img src="'.$status_icon.'" class="pull-right" style="max-height: 30px;">';
                }
			} else {
				$icon_img = '';
			} ?>
			<div class="dashboard-item form-horizontal">
				<?php if(in_array('Extra Billing',$field_config)) {
					$extra_billing = $dbc->query("SELECT COUNT(*) `num` FROM `ticket_comment` WHERE `ticketid` = '{$ticket['ticketid']}' AND '{$ticket['ticketid']}' > 0 AND `type` = 'service_extra_billing' AND `deleted` = 0 ORDER BY `ticketcommid` DESC")->fetch_assoc();
				} else {
					$extra_billing['num'] = 0;
				}
				if(in_array('Total Budget Time',$field_config) && $ticket['total_budget_time'] != '00:00:00' && !empty($ticket['total_budget_time'])) {
					$total_budget_time = time_time2decimal($ticket['total_budget_time']);
					$total_staff_time = 0;
					$staff_hours = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table` LIKE '%Staff%' AND `deleted` = 0 AND `ticketid` = '".$ticket['ticketid']."'");
					while($staff_hour = mysqli_fetch_assoc($staff_hours)) {
						if($staff_hour['hours_tracked'] > 0) {
							$total_staff_time += $staff_hour['hours_tracked'];
						} else if(!empty($staff_hour['checked_out']) && !empty($staff_hour['checked_in'])) {
							$total_staff_time += (time_time2decimal($staff_hour['checked_out']) - time_time2decimal($staff_hour['checked_in']));
						}
					}
					$total_budget_time_exceeded = number_format($total_staff_time - $total_budget_time,2);
				} else {
					$total_budget_time_exceeded = 0;
				} ?>
				<?= $icon_img ?><h3 <?= $extra_billing['num'] > 0 ? 'style="color:red;"' : '' ?>><a href="../Ticket/index.php?edit=<?= $ticket['ticketid'] ?>&from=<?= urlencode(WEBSITE_URL.'/Project/projects.php?edit='.$_GET['edit']) ?>"><?= get_ticket_label($dbc, $ticket) ?> <img class="inline-img small no-toggle total_budget_time_icon" title="Total Budget Time exceeded by <?= $total_budget_time_exceeded ?> hours." src="../img/icons/ROOK-status-paid.png" style="filter: invert(30%) sepia(94%) saturate(50000%) hue-rotate(356deg) brightness(103%) contrast(117%); <?= $total_budget_time_exceeded > 0 ? '' : 'display:none;' ?>"></a><?= !in_array('Hide Slider',$field_config) ? ' <a href="../Ticket/index.php?edit='.$ticket['ticketid'].'&action_mode=1&from='.urlencode($_GET['from']).'" '.(!in_array('Action Mode Button Eyeball',$field_config) ? 'class="btn brand-btn"' : '').' onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\',\'auto\',false,true); return false;">'.(in_array('Action Mode Button Eyeball',$field_config) ? '<img src="../img/icons/eyeball.png" class="inline-img">' : (!empty(get_config($dbc, 'ticket_slider_button')) ? get_config($dbc, 'ticket_slider_button') : 'Sign In')).'</a>' : '' ?>
				<span class="pull-right small">
				<?php if(!in_array('Staff',$field_config) && count($field_config) > 0) {
					foreach(array_unique(explode(',',$ticket['contactid'].','.$ticket['internal_qa_contactid'].','.$ticket['deliverable_contactid'])) as $assignid) {
						if($assignid > 0) {
							profile_id($dbc, $assignid);
						}
					}
				} ?>
				</span><div class="clearfix"></div></h3>
				<?php if(in_array('Business',$field_config) || count($field_config) == 0) { ?>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4"><?= BUSINESS_CAT ?>:</label>
						<div class="col-sm-8"><?= get_client($dbc, $ticket['businessid']) ?></div>
					</div>
				<?php } ?>
				<?php if(in_array('Contact',$field_config) || count($field_config) == 0) { ?>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4">Contact:</label>
						<div class="col-sm-8"><?php 
							foreach(array_filter(explode(',',$ticket['clientid'])) as $clientid) {
								echo get_contact($dbc, $clientid).'<br />';
							}
						?></div>
					</div>
				<?php } ?>
				<?php if(in_array('Services',$field_config) || count($field_config) == 0) { ?>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4">Services:</label>
						<div class="col-sm-8"><?php 
							foreach(array_filter(explode(',',$ticket['serviceid'])) as $service) {
								$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category`, `heading` FROM `services` WHERE `serviceid`='$service'"));
								echo ($service['category'] == '' ? '' : $service['category'].': ').$service['heading'].'<br />';
							}
						?></div>
					</div>
				<?php } ?>
				<?php if(in_array('Heading',$field_config) || count($field_config) == 0) { ?>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4"><?= TICKET_NOUN ?> Heading:</label>
						<div class="col-sm-8"><?= $ticket['heading'] ?></div>
					</div>
				<?php } ?>
				<?php if(in_array('Status',$field_config) || count($field_config) == 0) { ?>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4">Status:</label>
						<div class="col-sm-8">
							<?php if($ticket_security['edit'] > 0) { ?>
								<select name="status" data-table="tickets" data-id-field="ticketid" data-id="<?= $ticket['ticketid'] ?>" class="chosen-select-deselect">
									<option></option>
									<?php foreach($ticket_status as $status) { ?>
										<option <?= $ticket['ticket_status'] == $status ? 'selected' : '' ?> value="<?= $status ?>"><?= $status ?></option>
									<?php } ?>
								</select>
							<?php } else {
								echo $ticket['status'];
							} ?>
						</div>
					</div>
				<?php } ?>
				<?php if(in_array('Staff',$field_config) || count($field_config) == 0) { ?>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4">Staff:</label>
						<div class="col-sm-8"><?php $staff_list = [];
						foreach(array_filter(array_unique(explode(',',$ticket['contactid'].','.$ticket['internal_qa_contactid'].','.$ticket['deliverable_contactid']))) as $staffid) {
							if($staffid > 0) {
								$staff_list[] = profile_id($dbc, $staffid, true).get_contact($dbc, $staffid);
							}
						}
						echo implode(', ',$staff_list); ?></div>
					</div>
				<?php } ?>
				<?php if(in_array('Members',$field_config) || count($field_config) == 0) { ?>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4">Members:</label>
						<div class="col-sm-8"><?php 
							$member_list = mysqli_query($dbc, "SELECT `item_id` FROM `ticket_attached` WHERE `src_table`='members' AND `ticketid`='{$ticket['ticketid']}' AND `deleted`=0");
							while($member = mysqli_fetch_assoc($member_list)['item_id']) {
								echo '<a href="../Members/contact_inbox.php?edit='.$member.'">'.get_contact($dbc, $member).'</a><br />';
							}
						?></div>
					</div>
				<?php } ?>
				<?php if(in_array('Clients',$field_config) || count($field_config) == 0) { ?>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4">Clients:</label>
						<div class="col-sm-8"><?php 
							$member_list = mysqli_query($dbc, "SELECT `item_id` FROM `ticket_attached` WHERE `src_table`='clients' AND `ticketid`='{$ticket['ticketid']}' AND `deleted`=0");
							while($member = mysqli_fetch_assoc($member_list)['item_id']) {
								echo '<a href="../Members/contact_inbox.php?edit='.$member.'">'.get_contact($dbc, $member).'</a><br />';
							}
						?></div>
					</div>
				<?php } ?>
				<?php if(in_array('Create Date',$field_config) || count($field_config) == 0) { ?>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4">Date Created:</label>
						<div class="col-sm-8"><?= $ticket['heading'] ?></div>
					</div>
				<?php } ?>
				<?php if(in_array('Ticket Date',$field_config) || count($field_config) == 0) { ?>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4"><?= TICKET_NOUN ?> Date:</label>
						<div class="col-sm-8"><?php
							$dates = mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE IFNULL(`to_do_date`,'0000-00-00')!='0000-00-00' AND `ticketid`='".$ticket['ticketid']."'");
							if($dates->num_rows > 0) {
								while($date_row = $dates->fetch_assoc()) {
									switch($date_row['type']) {
										case 'origin': echo 'Shipment Date: '; break;
										case 'destination': echo 'Delivery Date: '; break;
										case '': break;
										default: echo $date_row['type'].': '; break;
									}
									echo $date_row['to_do_date']."<br />\n";
								}
							} else {
								echo $ticket['to_do_date'];
							}
						?></div>
					</div>
				<?php } ?>
				<?php if(in_array('Deliverable Date',$field_config) || count($field_config) == 0) { ?>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4">To Do Date:</label>
						<div class="col-sm-8"><?php
							echo ($ticket['to_do_date'] == '' ? '' : $ticket['to_do_date'].'<br />');
							foreach(array_filter(explode(',', $ticket['contactid'])) as $staff) {
								echo get_contact($dbc, $staff).'<br />';
							}
							echo '('.$ticket['max_time'].')';
						?></div>
					</div>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4">Internal QA Date:</label>
						<div class="col-sm-8"><?php
							echo ($ticket['internal_qa_date'] == '' ? '' : $ticket['internal_qa_date'].'<br />');
							foreach(array_filter(explode(',', $ticket['internal_qa_contactid'])) as $staff) {
								echo get_contact($dbc, $staff).'<br />';
							}
							echo '('.$ticket['max_qa_time'].')';
						?></div>
					</div>
					<div class="col-sm-6 form-group">
						<label class="col-sm-4">Deliverable Date:</label>
						<div class="col-sm-8"><?php
							echo ($ticket['deliverable_date'] == '' ? '' : $ticket['deliverable_date'].'<br />');
							foreach(array_filter(explode(',', $ticket['deliverable_contactid'])) as $staff) {
								echo get_contact($dbc, $staff).'<br />';
							}
						?></div>
					</div>
				<?php } ?>
				<?php if(in_array('Documents',$field_config)) { ?>
					<div class="col-sm-6">
						<label class="col-sm-4">Documents:</label>
						<div class="col-sm-8"><?php
							$documents = mysqli_query($dbc, "SELECT CONCAT('".TICKET_NOUN.": ',IFNULL(CONCAT(NULLIF(NULLIF(`type`,'Link'),''),': '),''),IFNULL(NULLIF(`label`,''),`document`)) `label`, CONCAT('download/',`document`) `link` FROM `ticket_document` WHERE `ticketid`='".$ticket['ticketid']."' AND `deleted`=0 AND IFNULL(`document`,'') != '' UNION
								SELECT CONCAT('".PROJECT_NOUN.": ',IFNULL(CONCAT(NULLIF(NULLIF(`category`,''),'undefined'),': '),''),IFNULL(NULLIF(`label`,''),`upload`)) `label`, CONCAT('../Project/download/',`upload`) `link` FROM `project_document` WHERE `projectid`='".$ticket['projectid']."' AND `deleted`=0 AND IFNULL(`upload`,'') != ''");
							while($document = $documents->fetch_assoc()) {
								echo '<a href="'.$document['link'].'">'.$document['label']."</a><br />\n";
							}
						?></div>
					</div>
				<?php } ?>
				<?php if(in_array('Invoiced',$field_config)) { ?>
					<div class="col-sm-6">
						<label class="col-sm-4">Invoiced:</label>
						<div class="col-sm-8">
							<?= $ticket['invoiced'] > 0 ? 'Yes' : 'No' ?>
						</div>
					</div>
				<?php } ?>
				<?php if(in_array('Total Budget Time',$field_config)) { ?>
					<div class="col-sm-6">
						<label class="col-sm-4">Total Budget Time:</label>
						<div class="col-sm-8">
							<?php if($ticket_security['edit'] > 0) {
								echo '<input type="text" name="total_budget_time" data-id="'.$ticket['ticketid'].'" onchange="setTotalBudgetTime(this);" class="timepicker-15 form-control" value="'.$ticket['total_budget_time'].'">';
							} else {
								echo $ticket['total_budget_time'];
							} ?>
						</div>
					</div>
				<?php } ?>
				<div class="clearfix"></div>
			</div>
		<?php }
	} else {
		echo "<h3>No ".TICKET_TILE." attached to this ".PROJECT_NOUN."</h3>";
	}
} ?>
<?php include('next_buttons.php'); ?>