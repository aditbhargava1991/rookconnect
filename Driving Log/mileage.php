<?php include('../include.php');
checkAuthorised('driving_log');
include('../navigation.php');
if(isset($_POST['reset'])) {
	unset($_POST['search_category']);
	unset($_POST['search_contact']);
	unset($_POST['search_project']);
}
$security = get_security($dbc, 'driving_log');
$config = explode(',',get_config($dbc, 'mileage_fields'));
$search_staff = $_POST['search_staff'] > 0 ? $_POST['search_staff'] : $_SESSION['contactid'];
$search_start = isset($_POST['search_start']) ? $_POST['search_start'] : date('Y-m-01');
$search_end = isset($_POST['search_end']) ? $_POST['search_end'] : date('Y-m-d');
$search_cat = isset($_POST['search_category']) ? $_POST['search_category'] : '';
$search_contact = isset($_POST['search_contact']) ? $_POST['search_contact'] : 0;
$search_project = isset($_POST['search_project']) ? $_POST['search_project'] : 0;
$search_start = date('Y-m-d 00:00:00', strtotime($search_start));
$search_end = date('Y-m-d 23:59:59', strtotime($search_end));
?>

<?php if(isset($_POST['export_mileage'])) {
	include('../Driving Log/mileage_pdf.php');
} ?>

<script>
$(document).ready(function() {
	$('table input,table select').off('change',saveField).change(saveField);
});
function saveField() {
	var field = this;
	if($(field).data('locked') != '') {
		setTimeout(function() { $(field).change(); }, 250);
	}
	var row = $(field).closest('tr');
	var name = this.name;
	var id = $(this).data('id');
	if(!(id > 0)) {
		row.find('[data-locked]').data('locked','true');
	}
	var value = this.value;
	if(name == 'mileage') {
		row.find('[name=double_mileage]').val(value * 2).change();
	}
	if(value == 'MANUAL') {
		$(this).hide().next('span').hide();
		$(this).closest('td').find('input').show().focus();
	} else {
		$.ajax({
			url: 'driving_log_ajax_all.php?action=mileage_fields',
			method: 'POST',
			data: {
				id: id,
				name: name,
				value: value
			},
			success: function(response) {
				if(response > 0) {
					row.find('[data-id]').data('id',response);
				}
				row.find('[data-locked]').data('locked','');
			}
		});
	}
}
function addMileage() {
	destroyInputs('.mileage_row');
	var block = $('.mileage_row').last();
	var clone = $(block).clone();

	$(clone).find('[data-id]').data('id','');
	$(clone).find('input').val('');
	$(clone).find('select').val('').trigger('change.select2');

	$(block).after(clone);
	initInputs('.mileage_row');

	$('table input,table select').off('change',saveField).change(saveField);
}
function removeMilage(img) {
	if($('.mileage_row').length <= 1) {
		addMileage();
	}

	var block = $(img).closest('.mileage_row');
	$(block).find('[name="deleted"]').val(1).change();
	$(block).remove();
}
</script>
<div class="container">
	<div class="row">
		<h1>Mileage<?= $security['config'] > 0 ? '<a href="field_config_dl.php"><img title="Tile Settings" src="../img/icons/settings-4.png" class="inline-img small pull-right settings-classic wiggle-me"></a>' : '' ?></h1>
		<form class="form-horizontal" method="POST" action="" role="form" enctype="multipart/form-data">
			<?php if(in_array('staff',$config)) { ?>
				<div class="col-sm-5">
					<label class="col-sm-4 control-label">Search By Staff:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select Staff..." class="chosen-select-deselect" name="search_staff">
							<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND `deleted`=0")) as $contact) { ?>
								<option <?= $contact['contactid'] == $search_staff ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['first_name'].' '.$contact['last_name'] ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('contact',$config)) { ?>
				<div class="col-sm-5">
					<label class="col-sm-4 control-label">Search By Client:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select Client..." class="chosen-select-deselect" name="search_contact"><option></option>
							<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category`!='Staff' AND `status`>0 AND `deleted`=0 AND `contactid` IN (SELECT `contactid` FROM `mileage` WHERE `deleted`=0)")) as $contact) { ?>
								<option <?= $contact['contactid'] == $search_contact ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['first_name'].' '.$contact['last_name'] ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('projects',$config)) { ?>
				<div class="col-sm-5">
					<label class="col-sm-4 control-label">Search By <?= PROJECT_NOUN ?>:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select <?= PROJECT_NOUN ?>..." class="chosen-select-deselect" name="search_project"><option></option>
							<?php $projects = mysqli_query($dbc, "SELECT `projectid`, `project_name`, `projecttype`, `status`, `businessid`, `clientid` FROM `project` WHERE `deleted`=0 AND `projectid` IN (SELECT `projectid` FROM `mileage` WHERE `deleted`=0)");
							while($project = mysqli_fetch_assoc($projects)) { ?>
								<option <?= $project['projectid'] == $search_project ? 'selected' : '' ?> value="<?= $project['projectid'] ?>"><?= get_project_label($dbc, $project) ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('category',$config)) { ?>
				<div class="col-sm-5">
					<label class="col-sm-4 control-label">Search By Category:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select Category..." class="chosen-select-deselect" name="search_category"><option></option>
							<?php $cats = mysqli_query($dbc, "SELECT `category` FROM `mileage` WHERE `deleted`=0 GROUP BY `category`");
							while($cat = mysqli_fetch_assoc($cats)) { ?>
								<option <?= $cat['category'] == $search_cat ? 'selected' : '' ?> value="<?= $cat['category'] ?>"><?= $cat['category'] ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('startdate',$config)) { ?>
				<div class="col-sm-5">
					<label class="col-sm-4 control-label">Search From Date:</label>
					<div class="col-sm-8">
						<input placeholder="Start Date..." class="form-control datepicker" name="search_start" value="<?= $search_start ?>">
					</div>
				</div>
				<div class="col-sm-5">
					<label class="col-sm-4 control-label">Search To Date:</label>
					<div class="col-sm-8">
						<input placeholder="End Date..." class="form-control datepicker" name="search_end" value="<?= $search_end ?>">
					</div>
				</div>
			<?php } ?>
			<div class="col-sm-2 pull-right">
				<button class="btn brand-btn pull-right" name="submit" value="submit" type="submit">Search</button>
				<button class="btn brand-btn pull-right" name="reset" value="reset" type="submit">Display All</button>
			</div>
			<div class="clearfix"></div>
			<a href="driving_log_tiles.php" class="btn brand-btn pull-left">Back to Dashboard</a>
			<div class="col-sm-12" id="no-more-tables">
				<div class="clearfix"></div>
				<button type="submit" name="export_mileage" class="double-gap-top gap-bottom btn brand-btn pull-right">Export PDF</button>
				<?php if(in_array('staff',$config)) { ?><h2>Mileage for <?= get_contact($dbc, $search_staff) ?></h2><?php }
				$categories = [];
				$cat_list = mysqli_query($dbc, "SELECT `category` FROM `mileage` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
				while($cat = mysqli_fetch_assoc($cat_list)) {
					$categories[] = $cat['category'];
				}
				$tickets = mysqli_fetch_all(mysqli_query($dbc, "SELECT `ticketid`, `heading`, `projectid`, `main_ticketid`, `sub_ticket`, `to_do_date`, `status`, `businessid`, `clientid`, `ticket_label`, `ticket_label_date`, `last_updated_time` FROM `tickets` WHERE `deleted`=0"),MYSQLI_ASSOC);
				$projects = mysqli_fetch_all(mysqli_query($dbc, "SELECT `projectid`, `project_name`, `projecttype`, `status`, `businessid`, `clientid` FROM `project` WHERE `deleted`=0"),MYSQLI_ASSOC);
				$tasks = mysqli_fetch_all(mysqli_query($dbc, "SELECT `tasklistid`, `heading` FROM `tasklist` WHERE `deleted`=0"),MYSQLI_ASSOC);
				$equipment = mysqli_fetch_all(mysqli_query($dbc, "SELECT `equipmentid`, CONCAT(IFNULL(`category`,''),': ',IFNULL(`make`,''),' ',IFNULL(`model`,''),' ',IFNULL(`label`,''),' ',IFNULL(`unit_number`,'')) label FROM `equipment` WHERE `deleted`=0"),MYSQLI_ASSOC);
				$checklists = mysqli_fetch_all(mysqli_query($dbc, "SELECT `checklistid`, `checklist_name` FROM `checklist` WHERE `deleted`=0"),MYSQLI_ASSOC);
				$expenses = mysqli_fetch_all(mysqli_query($dbc, "SELECT `expenseid`, CONCAT(`title`,' ',`ex_date`) label FROM `expense` WHERE `deleted`=0"),MYSQLI_ASSOC);
				$meetings = mysqli_fetch_all(mysqli_query($dbc, "SELECT `agendameetingid`, CONCAT(`meeting_topic`,' - ',`date_of_meeting`) label FROM `agenda_meeting` WHERE `deleted`=0 AND `type`='Meeting'"),MYSQLI_ASSOC); ?>
				<table class="table table-bordered">
					<tr class="hidden-sm hidden-xs">
						<?php if(in_array('staff',$config)) { ?><th>Driver</th><?php } ?>
						<?php if(in_array('startdate',$config)) { ?><th>Start</th><?php } ?>
						<?php if(in_array('enddate',$config)) { ?><th>End</th><?php } ?>
						<th>Mileage</th>
						<?php if(in_array('category',$config)) { ?><th>Category</th><?php } ?>
						<?php if(in_array('details',$config)) { ?><th>Details</th><?php } ?>
						<?php if(in_array('rate',$config)) { ?><th>Cost</th><?php } ?>
						<?php if(in_array('contact',$config)) { ?><th>Client</th><?php } ?>
						<?php if(in_array('double_mileage',$config)) { ?><th>KMx2</th><?php } ?>
						<?php if(in_array('tickets',$config)) { ?><th><?= TICKET_TILE ?></th><?php } ?>
						<?php if(in_array('projects',$config)) { ?><th><?= PROJECT_TILE ?></th><?php } ?>
						<?php if(in_array('tasks',$config)) { ?><th><?= TASK_TILE ?></th><?php } ?>
						<?php if(in_array('equipment',$config)) { ?><th>Equipment</th><?php } ?>
						<?php if(in_array('checklist',$config)) { ?><th>Checklists</th><?php } ?>
						<?php if(in_array('expense',$config)) { ?><th>Expense</th><?php } ?>
						<?php if(in_array('meetings',$config)) { ?><th>Meeting</th><?php } ?>
						<th>Function</th>
					</tr>
					<?php $mile_log = mysqli_query($dbc, "SELECT * FROM `mileage` WHERE `deleted`=0 AND `staffid`='$search_staff' AND '$search_contact' IN (`contactid`,'','0') AND '$search_project' IN (`projectid`,'','0') AND `category` LIKE '%$search_cat%' AND (`start` BETWEEN '$search_start' AND '$search_end' OR `end` BETWEEN '$search_start' AND '$search_end' OR IFNULL(`start`,'0000-00-00 00:00:00')='0000-00-00 00:00:00' AND IFNULL(`end`,'0000-00-00 00:00:00')='0000-00-00 00:00:00') ORDER BY IFNULL(`start`,'9999-99-99 23:59:59'), IFNULL(`end`,'9999-99-99 23:59:59')");
					$mileage = mysqli_fetch_assoc($mile_log);
					do {
						$rate['cust_price'] = 0;
						if($mileage['projectid'] > 0) {
							$projectid = $mileage['projectid'];
						} else if($mileage['ticketid'] > 0) {
							$projectid = get_field_value('projectid','tickets','ticketid',$mileage['ticketid']);
						}
						if($projectid > 0) {
							$rate = explode('*',get_field_value('ratecardid','project','projectid',$projectid));
							if($rate[0] > 0) {
								$rate['cust_price'] = 0;
							} else if($rate[0] == 'company') {
								$rate = $dbc->query("SELECT `cust_price` FROM `company_rate_card` WHERE `deleted`=0 AND `tile_name`='Mileage' AND `rate_card_name` IN (SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='{$rate[1]}') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')")->fetch_assoc();
							}
						} else {
							$rate = $dbc->query("SELECT `cust_price` FROM `company_rate_card` WHERE `deleted`=0 AND `tile_name`='Mileage' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')")->fetch_assoc();
						} ?>
						<tr class="mileage_row">
							<?php if(in_array('staff',$config)) { ?><td data-title="Driver">
								<select class="chosen-select-deselect" data-placeholder="Select a Driver..." name="staffid" data-id="<?= $mileage['id'] ?>" data-locked=""><option></option>
									<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND `deleted`=0")) as $contact) { ?>
										<option <?= $contact['contactid'] == $mileage['staffid'] ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['first_name'].' '.$contact['last_name'] ?></option>
									<?php } ?>
								</select>
								</td><?php } ?>
							<?php if(in_array('startdate',$config)) { ?><td data-title="Start">
								<input type="text" class="form-control dateandtimepicker" name="start" data-id="<?= $mileage['id'] ?>" data-locked="" value="<?= empty($mileage['start']) ? date('Y-m-d h:i a') : $mileage['start'] ?>">
								</td><?php } ?>
							<?php if(in_array('enddate',$config)) { ?><td data-title="End">
								<input type="text" class="form-control dateandtimepicker" name="end" data-id="<?= $mileage['id'] ?>" data-locked="" value="<?= empty($mileage['start']) && empty($mileage['end']) ? date('Y-m-d h:i a') : $mileage['end'] ?>">
								</td><?php } ?>
							<td data-title="Mileage"><input type="number" min="0" max="86400" step="0.05" class="form-control" name="mileage" data-id="<?= $mileage['id'] ?>" data-locked="" value="<?= $mileage['mileage'] ?>"></td>
							<?php if(in_array('category',$config)) { ?><td data-title="Category">
								<select class="chosen-select-deselect" data-placeholder="Select a Category..." name="category" data-id="<?= $mileage['id'] ?>" data-locked=""><option></option>
									<?php foreach($categories as $category) { ?>
										<option <?= $category == $mileage['category'] ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
									<?php } ?>
									<option value="MANUAL">Add Category</option>
								</select>
								<input type="text" class="form-control" name="category" data-id="<?= $mileage['id'] ?>" data-locked="" value="" style="display:none;">
								</td><?php } ?>
							<?php if(in_array('details',$config)) { ?><td data-title="Details">
								<input type="text" class="form-control" name="details" data-id="<?= $mileage['id'] ?>" data-locked="" value="<?= $mileage['details'] ?>">
								</td><?php } ?>
							<?php if(in_array('rate',$config)) { ?><td data-title="Cost">
								<input type="text" class="form-control" name="cost" readonly value="<?= $mileage['mileage']*$rate['cust_price'] ?>">
								</td><?php } ?>
							<?php if(in_array('contact',$config)) { ?><td data-title="Client">
								<select class="chosen-select-deselect" data-placeholder="Select a Client..." name="contactid" data-id="<?= $mileage['id'] ?>" data-locked=""><option></option>
									<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `name`, `contactid` FROM `contacts` WHERE `category`!='Staff' AND `status`>0 AND `deleted`=0 AND CONCAT(`name`,`last_name`,`first_name`) != ''")) as $contact) { ?>
										<option <?= $contact['contactid'] == $mileage['contactid'] ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['name'] != '' ? $contact['name'] : $contact['first_name'].' '.$contact['last_name'] ?></option>
									<?php } ?>
								</select>
								</td><?php } ?>
							<?php if(in_array('double_mileage',$config)) { ?><td data-title="KMx2">
								<input type="number" readonly class="form-control" name="double_mileage" data-id="<?= $mileage['id'] ?>" data-locked="" value="<?= $mileage['double_mileage'] ?>">
								</td><?php } ?>
							<?php if(in_array('tickets',$config)) { ?><td data-title="<?= TICKET_TILE ?>">
								<select class="chosen-select-deselect" data-placeholder="Select <?= TICKET_NOUN ?>..." name="ticketid" data-id="<?= $mileage['id'] ?>" data-locked=""><option></option>
									<?php foreach($tickets as $ticket) { ?>
										<option <?= $ticket['ticketid'] == $mileage['ticketid'] ? 'selected' : '' ?> value="<?= $ticket['ticketid'] ?>"><?= get_ticket_label($dbc, $ticket) ?></option>
									<?php } ?>
								</select>
								</td><?php } ?>
							<?php if(in_array('projects',$config)) { ?><td data-title="<?= PROJECT_TILE ?>">
								<select class="chosen-select-deselect" data-placeholder="Select <?= PROJECT_NOUN ?>..." name="projectid" data-id="<?= $mileage['id'] ?>" data-locked=""><option></option>
									<?php foreach($projects as $project) { ?>
										<option <?= $project['projectid'] == $mileage['projectid'] ? 'selected' : '' ?> value="<?= $project['projectid'] ?>"><?= get_project_label($dbc, $project) ?></option>
									<?php } ?>
								</select>
								</td><?php } ?>
							<?php if(in_array('tasks',$config)) { ?><td data-title="Tasks">
								<select class="chosen-select-deselect" data-placeholder="Select a Driver..." name="taskid" data-id="<?= $mileage['id'] ?>" data-locked=""><option></option>
									<?php foreach($tasks as $task) { ?>
										<option <?= $task['tasklistid'] == $mileage['taskid'] ? 'selected' : '' ?> value="<?= $task['tasklistid'] ?>"><?= $task['heading'] ?></option>
									<?php } ?>
								</select>
								</td><?php } ?>
							<?php if(in_array('equipment',$config)) { ?><td data-title="Equipment">
								<select class="chosen-select-deselect" data-placeholder="Select Equipment..." name="equipmentid" data-id="<?= $mileage['id'] ?>" data-locked=""><option></option>
									<?php foreach($equipment as $equip) { ?>
										<option <?= $equip['equipmentid'] == $mileage['equipmentid'] ? 'selected' : '' ?> value="<?= $equip['equipmentid'] ?>"><?= $equip['label'] ?></option>
									<?php } ?>
								</select>
								</td><?php } ?>
							<?php if(in_array('checklist',$config)) { ?><td data-title="Checklists">
								<select class="chosen-select-deselect" data-placeholder="Select a Checklist..." name="checklistid" data-id="<?= $mileage['id'] ?>" data-locked=""><option></option>
									<?php foreach($checklists as $checklist) { ?>
										<option <?= $checklist['checklistid'] == $mileage['checklistid'] ? 'selected' : '' ?> value="<?= $checklist['checklistid'] ?>"><?= $checklist['checklist_name'] ?></option>
									<?php } ?>
								</select>
								</td><?php } ?>
							<?php if(in_array('expense',$config)) { ?><td data-title="Expense">
								<select class="chosen-select-deselect" data-placeholder="Select an Expense..." name="expenseid" data-id="<?= $mileage['id'] ?>" data-locked=""><option></option>
									<?php foreach($expenses as $expense) { ?>
										<option <?= $expense['expenseid'] == $mileage['expenseid'] ? 'selected' : '' ?> value="<?= $expense['expenseid'] ?>"><?= $expense['label'] ?></option>
									<?php } ?>
								</select>
								</td><?php } ?>
							<?php if(in_array('meetings',$config)) { ?><td data-title="Meeting">
								<select class="chosen-select-deselect" data-placeholder="Select a Meeting..." name="meetingid" data-id="<?= $mileage['id'] ?>" data-locked=""><option></option>
									<?php foreach($meetings as $meeting) { ?>
										<option <?= $meeting['agendameetingid'] == $mileage['meetingid'] ? 'selected' : '' ?> value="<?= $meeting['agendameetingid'] ?>"><?= $meeting['label'] ?></option>
									<?php } ?>
								</select>
								</td><?php } ?>
							<td data-title="Function">
								<input type="hidden" name="deleted" data-id="<?= $mileage['id'] ?>" data-locked="" value="0">
		                        <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMileage();">
		                        <img src="../img/remove.png" class="inline-img pull-right" onclick="removeMilage(this);">
							</td>
						</tr>
					<?php } while($mileage = mysqli_fetch_assoc($mile_log)); ?>
				</table>
			</div>
		</form>
	</div>
</div>
<?php include('../footer.php'); ?>