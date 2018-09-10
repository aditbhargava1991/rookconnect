<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment'); ?>

<?php $equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
$equipmentid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING); 
$unit_number = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$equipmentid."'"))['unit_number'];?>

<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
    <ul>
        <a href="?category=Top"><li>Back to Dashboard</li></a>
        <a href="" onclick="return false;"><li class="active blue">Submitted Inspections</li></a>
    </ul>
</div>

<div class="scale-to-fill has-main-screen" style="overflow: hidden;">
    <div class="main-screen standard-body form-horizontal">
        <div class="standard-body-title">
            <h3>Equipment Unit #<?= $unit_number ?>: Inspections</h3>
        </div>

        <div class="standard-body-content" style="padding: 0.5em;">
			<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal gap-top" role="form">
				
				<?php $search_staff = $_SESSION['contactid'];
				$search_date_from = date('Y-m-d',strtotime('-1 month'));
				$search_date_to = date('Y-m-d');
				$search_type = '';
				if(search_visible_function($dbc, 'field_job') == 1 && isset($_POST['search_staff'])) {
					$search_staff = $_POST['search_staff'];
				}
				if(isset($_POST['search_date_from'])) {
					$search_date_from = $_POST['search_date_from'];
				}
				if(isset($_POST['search_date_to'])) {
					$search_date_to = $_POST['search_date_to'];
				}
				if(isset($_POST['search_type'])) {
					$search_type = $_POST['search_type'];
				}
				if (isset($_POST['display_all_inventory'])) {
					$search_staff = $_SESSION['contactid'];
					$search_date_from = date('Y-m-d',strtotime('-1 month'));
					$search_date_to = date('Y-m-d');
					$search_type = '';
				}
				$query = "SELECT * FROM `equipment_inspections` WHERE `equipmentid`='".$_GET['edit']."' AND (`staffid`='$search_staff' OR '$search_staff'='ALL')";
				if(!empty($search_type)) {
					$query .= " AND `type`='$search_type'";
				}
				if(!empty($search_date_from)) {
					$query .= " AND `date` >= '$search_date_from'";
				}
				if(!empty($search_date_to)) {
					$query .= " AND `date` <= '$search_date_to 23:59:59'";
				}
				$result = mysqli_query($dbc, $query); ?>

				<div class="search-group">
					<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
						<?php if(search_visible_function($dbc, 'equipment') == 1) { ?>
							<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
								<div class="col-sm-4">
									<label for="site_name" class="control-label">
										<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see all staff that have created inspections."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
										Search By Staff:</label>
								</div>
								<div class="col-sm-8">
									<select data-placeholder="Select a Staff" name="search_staff" class="chosen-select-deselect form-control">
										<option value=""></option>
										  <?php
											$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
											foreach($query as $id) {
												$selected = '';
												$selected = $id == $search_staff ? 'selected = "selected"' : '';
												echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
											}
										  ?>
										<option value="ALL">Display All</option>
									</select>
								</div>
							</div>
						<?php } ?>
						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<div class="col-sm-4">
								<label for="site_name" class="control-label">
									<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to see the inspection tabs."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
									Search By Inspection Tab:</label>
							</div>
							<div class="col-sm-8">
								<select data-placeholder="Select an Inspection Tab" name="search_type" class="chosen-select-deselect form-control">
									<option value=""></option>
									<option <?= ('Pre Trip' == $search_type ? "selected" : '') ?> value="Pre Trip">Pre Trip</option>
									<option <?= ('Post Trip' == $search_type ? "selected" : '') ?> value="Post Trip">Post Trip</option>
									<option <?= ('Maintenance' == $search_type ? "selected" : '') ?> value="Maintenance">Maintenance</option>
									<option <?= ('Evaluation' == $search_type ? "selected" : '') ?> value="Evaluation">Evaluation</option>
								</select>
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<div class="col-sm-4">
								<label for="site_name" class="control-label">
									<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to select the earliest date of inspection you want to see."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
									Search From Date:</label>
							</div>
							<div class="col-sm-8">
								<input type="text" class="form-control datepicker" name="search_date_from" value="<?= $search_date_from ?>">
							</div>
						</div>
						<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
							<div class="col-sm-4">
								<label for="site_name" class="control-label">
									<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to select the latest date of inspection you want to see."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
									Search To Date:</label>
							</div>
							<div class="col-sm-8">
								<input type="text" class="form-control datepicker" name="search_date_to" value="<?= $search_date_to ?>">
							</div>
						</div>
					</div>
					<div class="form-group col-lg-3 col-md-4 col-sm-12 col-xs-12">
						<div style="display:inline-block; padding: 0 0.5em;">
							<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here after you have made your customer selection."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
							<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click to refresh the page and see all projects within this tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
							<button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
						</div>
					</div><!-- .form-group -->
					<div class="clearfix"></div>
				</div>
				
				<div id="no-more-tables">
					<a href="?edit_inspection=1&edit=<?= $_GET['edit'] ?>" class="btn brand-btn pull-right">Add Inspection</a>
					<?php if(mysqli_num_rows($result) > 0) { ?>
						<table class="table table-bordered">
							<tr class="hidden-xs hidden-sm">
								<th>Staff Name</th>
								<th>Inspection Tab</th>
								<th>Date</th>
								<th>Tab</th>
								<th>Make</th>
								<th>Model</th>
								<th>Unit Number</th>
								<th>Service Requested</th>
								<th>Inspection</th>
							</tr>
							<?php while($row = mysqli_fetch_array($result)) {
								$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$row['equipmentid']."'")); ?>
								<tr>
									<td data-title="Staff Name"><?= get_contact($dbc, $row['staffid']) ?></td>
									<td data-title="Inspection Tab"><?= $row['type'] ?></td>
									<td data-title="Date &amp; Time"><?= date('Y-m-d g:i A', strtotime($row['date'])) ?></td>
									<td data-title="Tab"><?= $equipment['category'] ?></td>
									<td data-title="Make"><?= $equipment['make'] ?></td>
									<td data-title="Model"><?= $equipment['model'] ?></td>
									<td data-title="Unit Number"><?= $equipment['unit_number'] ?></td>
									<td data-title="Service Requested?"><?= $row['immediate'] ? 'Yes' : 'No' ?></td>
									<td data-title="Inspection Report"><a href="download/inspection_report_<?= $row['inspectionid'] ?>.pdf">View Report</a></td>
								</tr>
							<?php } ?>
						</table>
					<?php } else {
						echo "<h2>No Inspections Found</h2>";
					} ?>
				</div>
			</form>
		</div>
	</div>
</div>