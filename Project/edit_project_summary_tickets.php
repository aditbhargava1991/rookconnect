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

$display_column = 0;
$displayed_length = 0;
if($_GET['edit'] > 0) {
?>
<div class="col-sm-6">
	<?php $block_i = 0;
    foreach($blocks as $block) {
		if($block[0] == $displayed_length && $display_column == 0) {
            $block_i = 0;
			$displayed_length = 0;
			$total_length -= $block[0] + $displayed_length;
			echo '</div><div class="col-sm-6">'.$block[1].'</div><div class="col-sm-6">';
		} else if($block_i++ > 0 && $displayed_length + $block[0] - 25 > $total_length / 2) {
			$displayed_length = 0;
			$display_column = 1;
			echo '</div><div class="col-sm-6">'.$block[1];
		} else {
			$displayed_length += $block[0];
			echo $block[1];
		}
	} ?>
</div>
<?php } else {
	echo '<h2>Please add Project Details in order to see a Summary of the Project.</h2>';
} ?>
