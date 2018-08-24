<?php include_once('../include.php');
checkAuthorised('estimate');
ob_clean();
error_reporting(0);
$approvals = approval_visible_function($dbc, 'estimate');
$dashboard = filter_var($_GET['dashboard'], FILTER_SANITIZE_STRING);
if(empty($dashboard)) {
	$dashboard = $_SESSION['contactid'];
}
$search_query = '';
if($dashboard > 0) {
	$search_query .= " AND CONCAT(',',IFNULL(`assign_staffid`,''),',',IFNULL(`created_by`,''),',') LIKE '%,$dashboard,%'";
}
$status = filter_var($_GET['status'], FILTER_SANITIZE_STRING);
$start = filter_var($_GET['start'], FILTER_SANITIZE_STRING);
$end = filter_var($_GET['end'], FILTER_SANITIZE_STRING);
if(!empty($_GET['startdate'])) {
	$search_query .= " AND IFNULL(`status_date`,`created_date`) >= '".$_GET['startdate']."'";
}
if(!empty($_GET['enddate'])) {
	$search_query .= " AND IFNULL(`status_date`,`created_date`) <= '".$_GET['enddate']."'";
}
if(!empty($_GET['staffid'])) {
	$search_query .= " AND CONCAT(',',`assign_staffid`,',') LIKE '%,".$_GET['staffid'].",%'";
}
$today_date = date('Y-m-d');
$action_type = '';
if(!empty($_GET['action_type'])) {
	$action_type = $_GET['action_type'];
}
$all_status_list = [];
foreach(explode('#*#',get_config($dbc, 'estimate_status')) as $status_name) {
	$all_status_list[] = "'".preg_replace('/[^a-z]/','',strtolower($status_name))."'";
}
$closed_status = preg_replace('/[^a-z]/','',strtolower(get_config($dbc, 'estimate_project_status')));
$closed_date = get_user_settings()['estimate_closed'];
$closed_date = strtotime($closed_date) > date('Y-m-01') ? $closed_date : date('Y-m-01');
$estimates_list = mysqli_query($dbc, "SELECT `estimateid`, `estimate_name`, `businessid`, `clientid`, `status`, `total_price`, `expiry_date`, `add_to_project` FROM `estimate` WHERE ('$status' IN (`status`,'all') OR ('$status'='misc' AND `status` NOT IN (".implode(',',$all_status_list)."))) AND (`status` != '$closed_status' OR `status_date` >= '$closed_date') AND `estimate`.`deleted`=0".$search_query." LIMIT $start,$end");
$status_list = explode('#*#',get_config($dbc, 'estimate_status'));
if(mysqli_num_rows($estimates_list) > 0) {
	while($estimate = mysqli_fetch_array($estimates_list)) {
		$next_action = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate_actions` WHERE `estimateid`='{$estimate['estimateid']}' AND `deleted`=0 ORDER BY `due_date` ASC"));
		if($action_type == 'upcoming' && strtotime($next_action['due_date']) < strtotime($today_date)) {
			continue;
		} else if($action_type == 'pastdue' && strtotime($next_action['due_date']) >= strtotime($today_date)) {
			continue;
		}
		?>
		<div class="dashboard-item override-dashboard-item">
			<h4><a href="?view=<?= $estimate['estimateid'] ?>"><?= ($estimate['estimate_name'] != '' ? $estimate['estimate_name'] : '[UNTITLED '.$estimate['estimatetype'].']') ?><img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit"></a></h4>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="col-sm-4">Customer:</label>
					<div class="col-sm-8"><?= get_client($dbc, $estimate['businessid']) ?></div>
				</div>
				<div class="form-group">
					<label class="col-sm-4">Contact:</label>
					<div class="col-sm-8"><?= get_contact($dbc, $estimate['clientid']) ?></div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="col-sm-4"><?= ESTIMATE_TILE ?> Status:</label>
					<div class="col-sm-8">
						<?php if($approvals > 0 || ($estimate['status'] != 'Saved' && $estimate['status'] != 'Pending')) { ?>
							<select class="chosen-select-deselect" name="status" data-table="estimate" data-identifier="estimateid" data-id="<?= $estimate['estimateid'] ?>"><option></option>
								<?php $selected = false;
								foreach($status_list as $status_name) {
									$status_id = preg_replace('/[^a-z]/','',strtolower($status_name));
									if($status_id == $estimate['status']) {
										$selected = true;
									} ?>
									<option <?= $status_id == $estimate['status'] ? 'selected' : '' ?> value="<?= $status_id ?>"><?= $status_name ?></option>
								<?php }
								if(!$selected) { ?>
									<option selected value="<?= $estimate['status'] ?>"><?= $estimate['status'] ?></option>
								<?php } ?>
								<option value="archived">Archive</option>
							</select>
						<?php } else {
							echo $estimate['status'];
						} ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4">Next Action:</label>
					<div class="col-sm-8">
						<select class="chosen-select-deselect" name="action" data-table="estimate_actions" data-identifier="id" data-id="<?= $next_action['id'] ?>" data-estimate="<?= $estimate['estimateid'] ?>"><option></option>
							<option <?= $next_action['action'] == 'phone' ? 'selected' : '' ?> value="phone">Phone Call</option>
							<option <?= $next_action['action'] == 'email' ? 'selected' : '' ?> value="email">Email</option>
						</select>
					</div>
				</div>
			</div>
			<div class="col-sm-4">
				<div class="form-group">
					<label class="col-sm-4">Total Value:</label>
					<div class="col-sm-8">$<?= number_format($estimate['total_price'],2) ?></div>
				</div>
				<div class="form-group">
					<label class="col-sm-4">Expiry Date:</label>
					<div class="col-sm-8"><input type="text" class="form-control datepicker" value="<?= $estimate['expiry_date'] ?>" name="expiry_date" data-table="estimate" data-identifier="estimateid" data-id="<?= $estimate['estimateid'] ?>"></div>
				</div>
			</div>
			<div class="clearfix"></div>
			<input type="text" class="form-control" name="notes" value="" style="display:none;" data-table="estimate_notes" data-identifier="id" data-id="" data-estimate="<?= $estimate['estimateid'] ?>" onblur="$(this).val('').hide();">
			<div class="action-icons">
				<?php if($estimate['projectid'] > 0) { ?>
					<a href="../Project/projects.php?edit=<?= $estimate['projectid'] ?>"><img src="../img/icons/create_project.png" class="inline-img black-color" title="View <?= PROJECT_NOUN.' #'.$estimate['projectid'] ?>"></a>
				<?php } else { ?>
					<a href="convert_to_project.php?estimate=<?= $estimate['estimateid'] ?>" onclick="overlayIFrame('convert_select_project.php?estimateid=<?= $estimate['estimateid'] ?>');return false;"><img src="../img/icons/create_project.png" class="inline-img black-color" title="<?= $estimate['add_to_project'] > 0 ? 'Attach to '.PROJECT_NOUN.' #'.$estimate['add_to_project'] : 'Create '.PROJECT_NOUN.' from '.ESTIMATE_TILE.'.' ?>"></a>
				<?php } ?>
				<a href="?financials=<?= $estimate['estimateid'] ?>"><img src="../img/icons/financials.png" class="inline-img" title="View Estimate Financial Summary."></a>
				<a href="Add Note" onclick="$(this).closest('.dashboard-item').find('[name=notes]').show().focus(); return false;"><img src="../img/notepad-icon-blue.png" class="inline-img black-color" title="Add Note to Estimate."></a>
				<a href="Archive" onclick="$(this).closest('.dashboard-item').find('[name=status]').val('archived').trigger('change.select2').change(); return false;"><img src="../img/icons/ROOK-trash-icon.png" class="inline-img" title="Archive the Estimate."></a>
			</div>
		</div>
	<?php }
} else {
	echo "<h4 class='gap-left'>No estimates to display.</h4>";
} ?>
<div style="display:none;"><?php include_once('../footer.php'); ?></div>