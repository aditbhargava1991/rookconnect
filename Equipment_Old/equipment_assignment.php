<?php
/*
Add	Inventory
*/
include ('../include.php'); ?>
<script type="text/javascript">
$(document).ready(function() {
	if($('#equip_assign_table tr.equip_assign_row').length <= 0) {
		$('#equip_assign_table').replaceWith('<h3>No Equipment Assignment found.</h3>');
	}
});
function archiveEquipAssign(a, id) {
	if(confirm('Are you sure you want to archive this Equipment Assignment?')) {
		$.ajax({
			url: '../Equipment/equipment_ajax.php?fill=archive_equipment_assignment&equipment_assignmentid='+id,
			method: 'GET',
			success: function(response) {
				$(a).closest('tr').remove();
			}
		});
	}
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('equipment');
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$equip_assign_fields = ','.get_config($dbc,'equipment_equip_assign_fields').',';
$equipmentid = filter_var($_GET['equipmentid'],FILTER_SANITIZE_STRING);
$client_type = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"))['client_type'];
$client_type = empty($client_type) ? 'Customer' : $client_type;
$search_start_date = !empty($_POST['search_start_date']) ? $_POST['search_start_date'] : date('Y-m-d');
$search_end_date = !empty($_POST['search_end_date']) ? $_POST['search_end_date'] : date('Y-m-d', strtotime($search_start_date.' + 1 month'));

$get_equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$equipmentid."'"));

$unit_number = $get_equipment['unit_number'];
?>
<div class="container">
	<div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="equipment_iframe" src=""></iframe>
		</div>
	</div>
	<div class="row">

		<h1>Equipment Unit #<?= $unit_number ?>: Equipment Assignment</h1>

		<div class="pad-left gap-top double-gap-bottom"><a href="equipment.php?category=<?php echo $category; ?>" class="btn brand-btn">Back to Dashboard</a></div>

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

			<div class="gap-left tab-container">
				<a href="add_equipment.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Equipment</a>
				<?php if ( in_array('Inspection',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'inspection') === TRUE ) { ?>
					<a href="equipment_inspections.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Inspections</a>
				<?php } ?>
				<?php if ( in_array('Work Order',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'work_order') === TRUE ) { ?>
					<a href="equipment_work_order.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Work Orders</a>
				<?php } ?>
				<?php if ( in_array('Schedules',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'schedules') === TRUE ) { ?>
					<a href="equipment_service.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Service Schedule</a>
				<?php } ?>
				<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
					<a href="equipment_expenses.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Expenses</a>
				<?php } ?>
				<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
					<a href="equipment_balance.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn">Balance Sheet</a>
				<?php } ?>
	            <?php if ( in_array('Equipment Assignment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'eqipment', ROLE, 'equip_assign') === TRUE ) { ?>
	                <a href="equipment_assignment.php?equipmentid=<?= $_GET['equipmentid'] ?>" class="btn brand-btn active_tab">Equipment Assignment</a>
	            <?php } ?>
			</div>

			<div class="search-group">
				<div class="form-group col-lg-4 col-md-4 col-sm-12 col-sm-12">
					<label for="site_name" class="control-label col-sm-4">Start Date:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control datepicker" name="search_start_date" value="<?= $search_start_date ?>">
					</div>
				</div>
				<div class="form-group col-lg-4 col-md-4 col-sm-12 col-sm-12">
					<label for="site_name" class="control-label col-sm-4">End Date:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control datepicker" name="search_end_date" value="<?= $search_end_date ?>">
					</div>
				</div>
				<div class="form-group col-lg-4 col-md-4 col-sm-12 col-sm-12">
					<div style="display:inline-block; padding: 0 0.5em;">
						<button type="submit" name="search_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
					</div>
				</div><!-- .form-group -->
				<div class="clearfix"></div>
			</div>

			<div id="no-more-tables">
				<?php if(vuaed_visible_function($dbc, 'equipment') == 1) { ?>
					<div class="pull-right gap-bottom">
						<a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Calendar/equip_assign.php?equipmentid=<?= $_GET['equipmentid'] ?>&start_date=<?= date('Y-m-d') ?>&end_date=<?= date('Y-m-d') ?>'); return false;" class="btn brand-btn">Add Equipment Assignment</a>
					</div>
				<?php } ?>
				<table id="equip_assign_table" class="table table-bordered">
					<tr class="hidden-xs">
						<th>Date</th>
						<?php if(strpos($equip_assign_fields, ',equipment_assignmentid,') !== FALSE) { ?>
							<th>Equipment Assignment #</th>
						<?php } ?>
						<?php if(strpos($equip_assign_fields, ',client,') !== FALSE) { ?>
							<th><?= $client_type ?></th>
						<?php } ?>
						<?php if(strpos($equip_assign_fields, ',staff,') !== FALSE) { ?>
							<th>Staff</th>
						<?php } ?>
						<?php if(strpos($equip_assign_fields, ',contractors,') !== FALSE) { ?>
							<th>Contractors</th>
						<?php } ?>
						<?php if(strpos($equip_assign_fields, ',region,') !== FALSE) { ?>
							<th>Region</th>
						<?php } ?>
						<?php if(strpos($equip_assign_fields, ',location,') !== FALSE) { ?>
							<th>Location</th>
						<?php } ?>
						<?php if(strpos($equip_assign_fields, ',classification,') !== FALSE) { ?>
							<th>Classification</th>
						<?php } ?>
						<?php if(strpos($equip_assign_fields, ',start_date,') !== FALSE) { ?>
							<th>Start Date</th>
						<?php } ?>
						<?php if(strpos($equip_assign_fields, ',end_date,') !== FALSE) { ?>
							<th>End Date</th>
						<?php } ?>
						<?php if(strpos($equip_assign_fields, ',notes,') !== FALSE) { ?>
							<th>Notes</th>
						<?php } ?>
						<?php if(vuaed_visible_function($dbc, 'equipment') == 1) { ?>
							<th>Function</th>
						<?php } ?>
					</tr>
					<?php for($current_date = $search_start_date; strtotime($current_date) <= strtotime($search_end_date); $current_date = date('Y-m-d', strtotime($current_date.' + 1 day'))) {
						$equip_assigns = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment` WHERE `equipmentid` = '".$equipmentid."' AND `deleted` = 0 AND DATE(`start_date`) <= '".$current_date."' AND DATE(`end_date`) >= '".$current_date."' AND CONCAT(',',`hide_days`,',') NOT LIKE ('%,".$current_date.",%') ORDER BY `start_date` DESC, `end_date` ASC"),MYSQLI_ASSOC);
						foreach($equip_assigns as $equip_assign) {
							$staff_contactids = [];
							$contractor_contactids = [];
							$equip_assign_team = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '".$equip_assign['teamid']."'"));

					        $team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` ='".$equip_assign_team['teamid']."' AND `deleted` = 0"),MYSQLI_ASSOC);
					        foreach ($team_contacts as $team_contact) {
					        	if(!empty($team_contact['contactid']) && !in_array($team_contact['contactid'], $hide_staff)) {
						    		$staff_contactids[$team_contact['contactid']] = [get_contact($dbc, $team_contact['contactid'], 'category'), get_contact($dbc, $team_contact['contactid']), $team_contact['contact_position']];
					        	}
					        }

					        $equip_assign_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `equipment_assignment_staff` WHERE `equipment_assignmentid` = '".$equip_assign['equipment_assignmentid']."' AND `deleted` = 0"),MYSQLI_ASSOC);
					        foreach ($equip_assign_contacts as $equip_assign_contact) {
					        	if(!empty($equip_assign_contact['contactid'])) {
					        		if($equip_assign_contact['contractor'] == 1) {
							    		$contractor_contactids[$equip_assign_contact['contactid']] = [get_contact($dbc, $equip_assign_contact['contactid'], 'category'), get_contact($dbc, $equip_assign_contact['contactid']), $equip_assign_contact['contact_position']];
							    	} else {
							    		$staff_contactids[$equip_assign_contact['contactid']] = [get_contact($dbc, $equip_assign_contact['contactid'], 'category'), get_contact($dbc, $equip_assign_contact['contactid']), $equip_assign_contact['contact_position']];
							    	}
						    	}
					        } ?>
							<tr class="equip_assign_row">
								<td data-title="Date"><?= $current_date ?></td>
								<?php if(strpos($equip_assign_fields, ',equipment_assignmentid,') !== FALSE) { ?>
									<td data-title="Equipment Assignment #"><?= $equip_assign['equipment_assignmentid'] ?></td>
								<?php } ?>
								<?php if(strpos($equip_assign_fields, ',client,') !== FALSE) { ?>
									<td data-title="<?= $client_type ?>"><?= !empty(get_client($dbc, $equip_assign['clientid'])) ? get_client($dbc, $equip_assign['clientid']) : get_contact($dbc, $equip_assign['clientid']) ?></td>
								<?php } ?>
								<?php if(strpos($equip_assign_fields, ',staff,') !== FALSE) { ?>
									<td data-title="Staff">
										<?php foreach($staff_contactids as $value) {
											echo $value[0].': '.(!empty($value[2]) ? $value[2].': ' : '').$value[1].'<br />';
										} ?>
									</td>
								<?php } ?>
								<?php if(strpos($equip_assign_fields, ',contractors,') !== FALSE) { ?>
									<td data-title="Contractors">
										<?php foreach($contractor_contactids as $value) {
											echo $value[0].': '.(!empty($value[2]) ? $value[2].': ' : '').$value[1].'<br />';
										} ?>
									</td>
								<?php } ?>
								<?php if(strpos($equip_assign_fields, ',region,') !== FALSE) { ?>
									<td data-title="Region"><?= $equip_assign['region'] ?></td>
								<?php } ?>
								<?php if(strpos($equip_assign_fields, ',location,') !== FALSE) { ?>
									<td data-title="Location"><?= $equip_assign['location'] ?></td>
								<?php } ?>
								<?php if(strpos($equip_assign_fields, ',classification,') !== FALSE) { ?>
									<td data-title="Classification"><?= $equip_assign['classification'] ?></td>
								<?php } ?>
								<?php if(strpos($equip_assign_fields, ',start_date,') !== FALSE) { ?>
									<td data-title="Start Date"><?= $equip_assign['start_date'] ?></td>
								<?php } ?>
								<?php if(strpos($equip_assign_fields, ',end_date,') !== FALSE) { ?>
									<td data-title="End Date"><?= $equip_assign['end_date'] ?></td>
								<?php } ?>
								<?php if(strpos($equip_assign_fields, ',notes,') !== FALSE) { ?>
									<td data-title="Notes"><?= html_entity_decode($equip_assign['notes']) ?></td>
								<?php } ?>
								<?php if(vuaed_visible_function($dbc, 'equipment') == 1) { ?>
									<td data-title="Function"><a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Calendar/equip_assign.php?equipment_assignmentid=<?= $equip_assign['equipment_assignmentid'] ?>'); return false;">Edit</a> | <a href="" onclick="archiveEquipAssign(this, '<?= $equip_assign['equipment_assignmentid'] ?>'); return false;">Archive</a></td>
								<?php } ?>
							</tr>
						<?php }
					 ?>
					<?php } ?>
				</table>
				<div class="clearfix"></div>
				<?php if(vuaed_visible_function($dbc, 'equipment') == 1) { ?>
					<div class="pull-right">
						<a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Calendar/equip_assign.php?equipmentid=<?= $_GET['equipmentid'] ?>&start_date=<?= date('Y-m-d') ?>&end_date=<?= date('Y-m-d') ?>'); return false;" class="btn brand-btn">Add Equipment Assignment</a>
					</div>
				<?php } ?>
			</div>
		</form>

	</div>
</div>

<?php include('../footer.php'); ?>