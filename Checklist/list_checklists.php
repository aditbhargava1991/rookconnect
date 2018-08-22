<?php if($_GET['subtabid'] > 0) {
	$subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_subtab` WHERE `subtabid` = '{$_GET['subtabid']}'"));
} else {
	$subtab = ['name'=>ucwords($_GET['subtabid'])];
	if($subtab == 'Project') {
	}
}
$tab_filter = '';
$visible_override = "";
$link = "?".http_build_query($_GET).'&view=';
$project_list = [];
if($_GET['subtabid'] == 'project') {
	$project_query = mysqli_query($dbc, "SELECT * FROM (SELECT `projectid`, `project_name` FROM `project` WHERE `projectid` IN (SELECT `projectid` FROM `checklist` WHERE `deleted`=0) UNION SELECT `projectid`, `project_name` FROM `client_project` WHERE `projectid` IN (SELECT `client_projectid` FROM `checklist` WHERE `deleted`=0)) projects ORDER BY `project_name`");
	while($project_row = mysqli_fetch_array($project_query)) {
		$project_list[] = "<option value='{$project_row['projectid']}'>{$project_row['project_name']}</option>";
	} ?>
	<script>
	$(document).on('change', 'select.filter_projects', function() { filter_projects(this.value); });
	function filter_projects(projectid) {
		if(projectid > 0) {
			$('.option-list a').hide().filter(function() { return $(this).data('projectid') == projectid; }).show();
		} else {
			$('.option-list a').hide().filter(function() { return $(this).data('visible') == 'visible'; }).show();
		}
	}
	</script>
<?php } else if($_GET['tab'] == 'checklists' && $_GET['status'] == 'project') {
	$projectid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
	$subtab['name'] = PROJECT_NOUN.' Checklists<a href="?edit='.$projectid.'&tab=checklists&status=project&checklistid=NEW" class="btn brand-btn pull-right">Add Checklist</a>';
	$tab_filter = " AND `checklist`.`projectid`='$projectid'";
	$visible_override = "visible";
	$link = "?".http_build_query($_GET)."&view=";
}
switch($_GET['subtabid']) {
	default:
		echo "<div class='standard-body-title'><h3>".$subtab['name'].($_GET['subtabid'] > 0 ?
			"<span class='pull-right double-gap-right'><a href='?archivetab=".$_GET['subtabid']."'><img src='".WEBSITE_URL."/img/icons/ROOK-trash-icon.png'/></a></span>
			<span class='pull-right double-gap-right'><a href='?edittab=".$_GET['subtabid']."'><img src='".WEBSITE_URL."/img/icons/ROOK-edit-icon.png'/></a></span>" :
			($_GET['subtabid'] == 'project' ? '<div class="pull-right col-sm-6"><select class="chosen-select-deselect form-control filter_projects"><option></option>'.implode('',$project_list).'</select></div>' : ''))."</h3></div>";
}
$subtab_list = mysqli_query($dbc, "SELECT * FROM (SELECT `checklist`.`checklistid`,`checklist`.`businessid`,`checklist`.`projectid`,`checklist`.`ticketid`,`checklist`.`client_projectid`,`checklist`.`security`,`checklist`.`assign_staff`,`checklist`.`checklist_type`,`checklist`.`reset_day`,`checklist`.`reset_time`,`checklist`.`checklist_name`,`checklist`.`flag_colour`,`checklist`.`alerts_enabled`,`checklist`.`created_by`,`checklist`.`deleted`,`checklist`.`subtabid`, `checklist_subtab`.`name`, IFNULL(`project`.`project_name`,`client_project`.`project_name`) `final_project_name`, IFNULL(`project`.`projectid`,`client_project`.`projectid`) `final_projectid` FROM `checklist` LEFT JOIN `checklist_subtab` ON `checklist`.`subtabid`=`checklist_subtab`.`subtabid` LEFT JOIN `project` ON `checklist`.`projectid`=`project`.`projectid` LEFT JOIN `client_project` ON `checklist`.`client_projectid`=`client_project`.`projectid` WHERE (`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `assign_staff`=',ALL,') AND `checklist`.`deleted`=0 $tab_filter ORDER BY `final_project_name`) `checklists` UNION
	SElECT `checklistid`,'','0','','','','ALL',`checklist_item` `checklist_type`,'','',`checklist_name`,'','','',`deleted`,'',`checklist_name` `name`,'','' FROM `item_checklist`");
echo "<ul class='option-list'>";
while($checklist = mysqli_fetch_array($subtab_list)) {
	$users = '';
	foreach(explode(',',$checklist['assign_staff']) as $id) {
		if($id > 0) {
			$users .= get_contact($dbc, $id).' ';
		}
	}
	$visibility = "hidden' style='display:none;";
	switch($_GET['subtabid']) {
	case 'favourites':
		if(in_array($checklist['checklistid'],explode(',',$user_settings['checklist_fav']))) {
			$visibility = "visible";
		}
		break;
	case 'private':
		if($checklist['assign_staff'] == ",{$_SESSION['contactid']},") {
			$visibility = "visible";
		}
		break;
	case 'shared':
		if($checklist['assign_staff'] != ",{$_SESSION['contactid']},") {
			$visibility = "visible";
		}
		break;
	case 'project':
		if($checklist['client_projectid'] > 0 || $checklist['projectid'] > 0) {
			$visibility = "visible";
		}
		break;
	case 'company':
		if($checklist['assign_staff'] == ",ALL,") {
			$visibility = "visible";
		}
		break;
	case 'ongoing':
		if($checklist['checklist_type'] == "ongoing") {
			$visibility = "visible";
		}
		break;
	case 'daily':
		if($checklist['checklist_type'] == "daily") {
			$visibility = "visible";
		}
		break;
	case 'weekly':
		if($checklist['checklist_type'] == "weekly") {
			$visibility = "visible";
		}
		break;
	case 'monthly':
		if($checklist['checklist_type'] == "monthly") {
			$visibility = "visible";
		}
		break;
	case 'equipment':
		if($checklist['checklist_type'] == "equipment") {
			$visibility = "visible";
		}
		break;
	case 'inventory':
		if($checklist['checklist_type'] == "inventory") {
			$visibility = "visible";
		}
		break;
	case $checklist['subtabid']:
		$visibility = "visible";
		break;
	}
	$visibility = ($visible_override == '' ? $visibility : $visible_override);
	echo "<a href='".(in_array($checklist['checklist_type'],['equipment','inventory']) ? str_replace('view=','item_view=',$link) : $link).$checklist['checklistid']."' class='col-sm-6' data-projectid='{$checklist['final_projectid']}' data-project='{$checklist['final_project_name']}' data-subtab='{$checklist['name']}' data-users='$users' data-name='{$checklist['checklist_name']}' data-visible='$visibility'><li style='width:calc(100% - 3em);'>";
	profile_id($dbc, $checklist['created_by']);
	$additional = array_values(array_unique(array_filter(explode(',',str_replace(",{$checklist['created_by']},",',',','.$checklist['assign_staff'].',')))));
	echo '<div style="display:inline; width:calc(100% - 3em);">'.(count($additional) > 0 ? ($additional[0] == 'ALL' ? '+All Staff: ' : '+'.count($additional).' ') : '').' '.($_GET['subtabid'] == 'project' ? 'Project #'.$checklist['final_projectid'].': '.$checklist['final_project_name'].': ' : '').$checklist['checklist_name']."</div><div class='clearfix'></div></li></a>";
}
echo "</ul>"; ?>
<script>
$(document).ready(function() {
	var height = 0;
	$('.option-list li').each(function() {
		if(this.offsetHeight > height) {
			height = this.offsetHeight;
		}
	});
	$('.option-list li').outerHeight(height);
    // $('.option-list li').css('line-height', height+'px');
});
</script>