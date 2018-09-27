<?php include_once('../include.php');
if($_GET['projectid'] > 0) {
	$projectid = $_GET['projectid'];
	$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
	$projecttype = $project['projecttype'];
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
	$base_config = array_filter(array_unique(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype' UNION
		SELECT `config_fields`  FROM `field_config_project` WHERE `fieldconfigprojectid` IN (SELECT MAX(`fieldconfigprojectid`) FROM `field_config_project` WHERE `type` IN ('".preg_replace('/[^a-z_,\']/','',str_replace(' ','_',str_replace(',',"','",strtolower(get_config($dbc,'project_tabs')))))."'))"))[0])));
	$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
	if(count($value_config) == 0) {
		$value_config = explode(',','Information Contact Region,Information Contact Location,Information Contact Classification,Information Business,Information Contact,Information Rate Card,Information Project Type,Information Project Short Name,Details Detail,Dates Project Created Date,Dates Project Start Date,Dates Estimate Completion Date,Dates Effective Date,Dates Time Clock Start Date');
	}
	$tab_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='$projecttype'"))['config_tabs']),explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='ALL'"))['config_tabs']))));
	if(count($tab_config) == 0) {
		$tab_config = explode(',','Path,Information,Details,Documents,Dates,Scope,Estimates,Tickets,Work Orders,Tasks,Checklists,Email,Phone,Reminders,Agendas,Meetings,Gantt,Profit,Report Checklist,Billing,Field Service Tickets,Purchase Orders,Invoices');
	}
} ?>
<?php $blocks = [];
$total_length = 0;
if(in_array('Summary Estimated',$tab_config)) {
	$total_estimated_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `time` FROM `ticket_time_list` WHERE `deleted`=0 AND `time_type` IN ('Completion Estimate','QA Estimate') AND `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `deleted`=0 AND `projectid`='$projectid')")->fetch_assoc()['time'];
	$blocks[] = [68, '<div class="overview-block">
		<h4>Total Estimated Time: '.$total_estimated_time.'</h4>
	</div>'];
	$total_length += 68;
}
if(in_array('Summary Tracked',$tab_config)) {
	$total_tracked_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time`))) `time` FROM (SELECT `time_length` `time`, `ticketid` FROM `ticket_time_list` WHERE `deleted`=0 AND `time_type`='Manual Time' UNION SELECT `timer` `time`, `ticketid` FROM `ticket_timer` WHERE `deleted` = 0) `time_list` WHERE `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid' AND `deleted`=0)")->fetch_assoc()['time'];
	$blocks[] = [68, '<div class="overview-block">
		<h4>Total Tracked Time: '.$total_tracked_time.'</h4>
	</div>'];
	$total_length += 68;
}
if(in_array('Summary Contact',$tab_config)) {
	$block_length = 68;
	$project_lead = [];
	if($project['project_lead'] > 0) {
		foreach(sort_contacts_query($dbc->query("SELECT `first_name`, `last_name`, `email_address`, `office_phone` FROM `contacts` WHERE `contactid`='{$project['project_lead']}'")) as $project_lead) { }
	}
	$block = '<div class="overview-block">
		<h4>Point of Contact</h4>
		<img class="inline-img" src="../img/business.PNG"> '.get_config($dbc, 'company_name').'
		'.($project['project_lead'] > 0 ? '<br /><img class="inline-img" src="../img/person.PNG"> '.$project_lead['name'].' '.$project_lead['first_name'].' '.$project_lead['last_name'].'
		'.($project_lead['email_address'] != '' ? '<br /><a href="mailto:'.$project_lead['email_address'].'"><img class="inline-img" src="../img/email.PNG"> '.$project_lead['email_address'].'</a>' : '')
		.($project_lead['office_phone'] != '' ? '<br /><a href="tel:'.$project_lead['office_phone'].'"><img class="inline-img" src="../img/office_phone.PNG"> '.$project_lead['office_phone'].'</a>' : '') : '').'
	</div>';
	$block_length += 23;
	$block_length += $project['project_lead'] > 0 ? 23 : 0;
	$block_length += $project_lead['email_address'] != '' ? 23 : 0;
	$block_length += $project_lead['office_phone'] != '' ? 23 : 0;
	$blocks[] = [$block_length, $block];
	$total_length += $block_length;
}
if(in_array('Summary Details',$tab_config)) {
	$block_length = 68;
	$block = '<div class="overview-block">
		<h4>'.PROJECT_NOUN.' Details</h4>';
		if($project['project_lead'] > 0) {
			$block .= '<label class="control-label">'.PROJECT_NOUN.' Lead:</label> '.get_contact($dbc, $project['project_lead']).'<br />';
			$block_length += 23;
		}
		if($project['project_colead'] > 0) {
			$block .= '<label class="control-label">'.PROJECT_NOUN.' Co-Lead:</label> '.get_contact($dbc, $project['project_colead']).'<br />';
			$block_length += 23;
		}
		$block .= '<label class="control-label">'.PROJECT_NOUN.' Type:</label> '.$project_tabs[$project['projecttype']].'<br />';
		$block_length += 23;
		foreach(array_filter(explode(',',$project['project_path'])) as $pathid) {
			$block .= '<label class="control-label">'.PROJECT_NOUN.' Path:</label> '.get_field_value('project_path', 'project_path_milestone', 'project_path_milestone', $pathid).'<br />';
			$block_length += 23;
		}
		$block .= '<label class="control-label">'.PROJECT_NOUN.' Status:</label> '.$project['status'].'<br />';
		$block_length += 23;
	$block .= '</div>';
	$blocks[] = [$block_length, $block];
	$total_length += $block_length;
}
if(in_array('Summary Tickets',$tab_config)) {
	$block_length = 68;
	$block = '<div class="overview-block">
		<h4>'.TICKET_NOUN.' Summary</h4>';
		foreach(explode(',',get_config($dbc, 'ticket_status')) as $status) {
			$count = $dbc->query("SELECT COUNT(*) `count`, GROUP_CONCAT(CONCAT(`contactid`,',',`internal_qa_contactid`,',',`deliverable_contactid`)) FROM `tickets` WHERE `deleted`=0 AND `status`='$status' AND `projectid`='$projectid'")->fetch_assoc();
			if($count['count'] > 0) {
				$icon = get_ticket_status_icon($dbc, $status);
				$block .= '<label class="control-label">'.($icon != '' ? ($icon == 'initials' ? '<span class="id-circle-small" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($status).'</span> ' : '<img class="inline-img" src="'.$icon.'"> ') : '').$status.':</label> '.$count['count'];
				foreach(array_unique(array_filter(explode(',',$count['contacts']))) as $staff) {
					$block .= profile_id($dbc, $staff, false);
				}
				$block .= '<br />';
				$block_length += 23;
			}
		}
	$block .= '</div>';
	$blocks[] = [$block_length, $block];
	$total_length += $block_length;
}
if(in_array('Summary Tasks',$tab_config)) {
	$block_length = 68;
	$block = '<div class="overview-block">
		<h4>Task Summary</h4>';
		foreach(explode(',',get_config($dbc, 'task_status')) as $status) {
			$count = $dbc->query("SELECT COUNT(*) `count` FROM `tasklist` WHERE `deleted`=0 AND `status`='$status' AND `projectid`='$projectid'")->fetch_assoc();
			if($count['count'] > 0) {
				$block .= '<label class="control-label">'.$status.':</label> '.$count['count'].'<br />';
				$block_length += 23;
			}
		}
		if($block_length == 68) {
			$block .= '<h5>No Tasks Found</h5>';
			$block_length += 23;
		}
	$block .= '</div>';
	$blocks[] = [$block_length, $block];
	$total_length += $block_length;
}
if(in_array('Summary Checklist',$tab_config)) {
	$block_length = 68;
	$block = '<div class="overview-block">
		<h4>Checklists</h4>';
		$lists = $dbc->query("SELECT `checklistid`, `checklist_name` FROM `checklist` WHERE `projectid`='$projectid' AND `deleted`=0");
		while($list = $lists->fetch_assoc()) {
				$block .= '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Checklist/checklist.php?view='.$list['checklistid'].'\',\'auto\',true,true); return false;">'.$list['checklist_name'].'</a><br />';
				$block_length += 23;
		}
	$block .= '</div>';
	$blocks[] = [$block_length, $block];
	$total_length += $block_length;
}
if(in_array('Summary Communications',$tab_config)) {
	$block_length = 68;
	$block = '<div class="overview-block">
		<h4>Communication</h4>';
		$comms = $dbc->query("SELECT 'Email' `type`, COUNT(*) `count` FROM `email_communication` WHERE `deleted`=0 AND `projectid`='$projectid' UNION SELECT 'Phone' `type`, COUNT(*) `count` FROM `phone_communication` WHERE `deleted`=0 AND `projectid`='$projectid' UNION SELECT `type`, COUNT(*) `count` FROM `agenda_meeting` WHERE `projectid`='$projectid' GROUP BY `type`");
		while($count = $comms->fetch_assoc()) {
				$block .= '<label class="control-label">'.$count['type'].':</label> '.$count['count'].'<br />';
				$block_length += 23;
		}
	$block .= '</div>';
	$blocks[] = [$block_length, $block];
	$total_length += $block_length;
}
if(in_array('Summary Notes',$tab_config)) {
	$block_length = 68;
	$block = '<div class="overview-block">
		<h4>Notes</h4>';
		$notes = $dbc->query("SELECT `created_date`, `comment` FROM `project_comment` WHERE `type`='project_note' AND `projectid`='$projectid'");
		while($note = $notes->fetch_assoc()) {
				$block .= '<label class="control-label">'.$note['created_date'].':</label> '.html_entity_decode($note['comment']);
				$block_length += 46;
		}
	$block .= '</div>';
	$blocks[] = [$block_length, $block];
	$total_length += $block_length;
}
if(in_array('Summary Reporting',$tab_config)) {
	$block_length = 68;
	$block = '<div class="overview-block">
		<h4>Reporting</h4>';
		if(in_array('Deliverables',$tab_config)) {
			$block .= '<a href="?edit=6&amp;tab=deliverables"><label class="cursor-hand control-label">Deliverables</label></a><br />';
			$block_length += 23;
		}
		if(in_array('Gantt',$tab_config)) {
			$block .= '<a href="?edit=6&amp;tab=gantt"><label class="cursor-hand control-label">Gantt Chart</label></a><br />';
			$block_length += 23;
		}
		$total_tracked_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time`))) `time` FROM (SELECT `time_length` `time`, `ticketid` FROM `ticket_time_list` WHERE `deleted`=0 AND `time_type`='Manual Time' UNION SELECT `timer` `time`, `ticketid` FROM `ticket_timer` WHERE `deleted` = 0) `time_list` WHERE `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid' AND `deleted`=0)")->fetch_assoc()['time'];
		$block .= '<label class="control-label">Total Time Spent:</label> '.$total_tracked_time;
		$block_length += 23;

	$block .= '</div>';
	$blocks[] = [$block_length, $block];
	$total_length += $block_length;
}
if(in_array('Summary Billing',$tab_config)) {
	$block_length = 68;
	$block = '<div class="overview-block">
		<h4>Billing</h4>';
		$lists = $dbc->query("SELECT * FROM `invoice` WHERE `projectid`='$projectid' AND `deleted`=0 AND `status` NOT IN ('Void','Archived')");
		while($list = $lists->fetch_assoc()) {
				$block .= '<a href="../Invoice/Download/invoice_'.$list['invoiceid'].'">Invoice #'.$list['invoiceid'].'</a><br />';
				$block_length += 23;
		}
	$block .= '</div>';
	$blocks[] = [$block_length, $block];
	$total_length += $block_length;
}
if(in_array('Summary Payments',$tab_config)) {
	$block_length = 68;
	$block = '<div class="overview-block">
		<h4>Payment Schedule</h4>';
		$lists = $dbc->query("SELECT CONCAT(`invoice`.`tile_name`,' #',`invoice`.`invoiceid`) `heading`, `invoice`.`total_price`, `invoice`.`due_date`, `invoice_payment`.`date_paid`, `invoice_payment`.`paid` FROM `invoice` LEFT JOIN `invoice_payment` ON `invoice`.`invoiceid`=`invoice_payment`.`invoiceid` WHERE `invoice`.`projectid`='$projectid' AND `invoice`.`status` NOT IN ('Void','Archived') UNION SELECT `heading`, `amount` `total_price`, `due_date`, `date_paid`, IF(`status`='Paid','Yes','No') `paid` FROM `project_payments` WHERE `deleted`=0 AND `status` NOT IN ('Void') AND `projectid`='$projectid'");
		while($list = $lists->fetch_assoc()) {
				$block .= '<a href="../Invoice/Download/invoice_'.$list['invoiceid'].'">'.ucwords($list['heading']).' Due on '.$list['due_date'].($list['paid'] > 0 ? ', Paid on '.$list['date_paid'] : '').'</a><br />';
				$block_length += 23;
		}
	$block .= '</div>';
	$blocks[] = [$block_length, $block];
	$total_length += $block_length;
}
$display_column = 0;
$displayed_length = 0;
if($_GET['edit'] > 0) {
?>
<div class="col-sm-6">
	<?php
		foreach($blocks as $block) {
		if($block[0] == $displayed_length && $display_column == 0) {
			$displayed_length = 0;
			$total_length -= $block[0] + $displayed_length;
			echo '</div><div class="col-sm-6">'.$block[1].'</div><div class="col-sm-6">';
		} else if($displayed_length > $total_length / 2) {
			$displayed_length = 0;
			$display_column = 1;
			echo '</div><div class="col-sm-6">'.$block[1];
		} else {
			$displayed_length += $block[0];
			echo $block[1];
		}
	}
	?>
</div>
<?php } else {
	echo '<h2>Please add Project Details in order to see a Summary of the Project.</h2>';
} ?>
