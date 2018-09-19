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
$unassigned_sql = "SELECT 'Ticket', `ticketid` FROM tickets WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(milestone_timeline,'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '')"; ?>
<!-- <h3>Unassigned</h3> -->
<?php $ticket_status = explode(',',get_config($dbc,'ticket_status'));
$unassigned = mysqli_query($dbc, $unassigned_sql);
$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status` > 0"));
while($item = mysqli_fetch_array($unassigned)) {
	$status_icon = get_ticket_status_icon($dbc, $item['status']);
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
		<?= $icon_img ?><h3><a href="../Ticket/index.php?edit=<?= $ticket['ticketid'] ?>&from=<?= urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']) ?>"><?= $item[0] == 'Ticket' ? TICKET_NOUN : $item[0] ?> #<?= $item[1] ?> - <?= get_multiple_ticket($dbc, $item[1]) ?></a></h3>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">Milestone:</label>
			<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
				<select name="<?= $item[2] ?>" data-table="<?= $item[4] ?>" data-id-field="<?= $item[5] ?>" data-id="<?= $item[1] ?>" class="chosen-select-deselect" data-placeholder="Select a Milestone">
					<option></option>
					<?php foreach($milestones as $milestone) { ?>
						<option <?= $item[3] == $milestone ? 'selected' : '' ?> value="<?= $milestone ?>"><?= $milestone ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">To Do Date:</label>
			<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
				<input type="text" name="<?= $item[6] ?>" data-table="<?= $item[4] ?>" data-id-field="<?= $item[5] ?>" data-id="<?= $item[1] ?>" value="<?= $item[7] ?>" class="datepicker form-control" data-placeholder="Select Staff">
			</div>
		</div>
		<div class="col-sm-4 form-group">
			<label class="col-sm-4">Staff:</label>
			<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
				<select name="<?= $item[8] ?>[]" multiple data-concat=',' data-table="<?= $item[4] ?>" data-id-field="<?= $item[5] ?>" data-id="<?= $item[1] ?>" class="chosen-select-deselect"><option></option>
					<?php foreach($staff_list as $staff) { ?>
						<option <?= in_array($staff['contactid'], explode(',',$item[9])) ? 'selected' : '' ?> value="<?= $staff['contactid'] ?>"><?= $staff['first_name'].' '.$staff['last_name'] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="clearfix"></div>
	</div>
<?php } ?>
<?php include('next_buttons.php'); ?>