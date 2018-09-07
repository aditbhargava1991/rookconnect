<?php
/*
Equipment Listing
*/
include ('../include.php');

?>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('equipment');
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
include_once ('../Equipment/region_location_access.php');
?>
<script type="text/javascript">
$(document).on('change', 'select[name="search_category"]', function() { location = this.value; });
</script>
<div class="container">
	<div class="row">

        <div class="col-sm-10"><h1 class="single-pad-bottom">Equipment: Service Records</h1></div>
		<div class="col-sm-2 double-gap-top">
			<?php
				if(config_visible_function($dbc, 'equipment') == 1) {
					echo '<a href="field_config_equipment.php?type=tab" class="mobile-block pull-right "><img style="width:45px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
                    echo '<span class="popover-examples pull-right" style="margin:10px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the Settings within this tile. Any changes will appear on your dashboard."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
				}
			?>
		</div>
		<div class="clearfix double-gap-bottom"></div>

		<div class="tab-container">
			<?php if ( in_array('Equipment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'equipment') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit all Active Equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="equipment.php?category=Top&status=Active"><button type="button" class="btn brand-btn mobile-block">Active Equipment</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Equipment',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'equipment') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit Inactive Equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="equipment.php?category=Top&status=Inactive"><button type="button" class="btn brand-btn mobile-block">Inactive Equipment</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Inspection',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'inspection') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View all past and scheduled equipment inspections."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="inspections.php"><button type="button" class="btn brand-btn mobile-block">Inspections</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Assign',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'assign') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit all Assigned equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="assign_equipment.php"><button type="button" class="btn brand-btn mobile-block">Assigned Equipment</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Work Order',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'work_orders') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View the status of all Work Orders."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="work_orders.php"><button type="button" class="btn brand-btn mobile-block">Work Orders</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View all Equipment Expenses."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="expenses.php"><button type="button" class="btn brand-btn mobile-block">Expenses</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Expenses',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'expenses') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and edit all Balance Sheets relating to a specific item of equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="balance.php"><button type="button" class="btn brand-btn mobile-block">Balance Sheets</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Schedules',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'schedules') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View the scheduled service dates for equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="service_schedules.php"><button type="button" class="btn brand-btn mobile-block">Service Schedules</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Requests',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'requests') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and add Services Requests for equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="service_request.php?category=Top"><button type="button" class="btn brand-btn mobile-block">Service Requests</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Records',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'records') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View and add Service Records for equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="service_record.php?category=Top"><button type="button" class="btn brand-btn mobile-block active_tab">Service Records</button></a>
                </div>
			<?php } ?>
			<?php if ( in_array('Checklists',$equipment_main_tabs) && check_subtab_persmission($dbc, 'equipment', ROLE, 'checklist') === TRUE ) { ?>
				<div class="tab pull-left">
                    <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="View, add and edit Equipment Checklists."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                    <a href="equipment_checklist.php"><button type="button" class="btn brand-btn mobile-block">Checklists</button></a>
                </div>
			<?php } ?>
            <div class="clearfix"></div>
		</div>
	
		<div class="gap-left tab-container col-sm-12">
			<?php $category = $_GET['category'];
			$each_tab = explode(',', get_config($dbc, 'equipment_tabs'));

			if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
				<div class="row">
					<label class="control-label col-sm-2">
                        <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Filter equipment by Category."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                        Category:
                    </label>
					<div class="col-sm-4">
						<select name="search_category" class="chosen-select-deselect form-control mobile-100-pull-right category_actual">
							<option value="?category=Top">Last 25 Service Records</option>
							<?php
								foreach ($each_tab as $cat_tab) {
									echo "<option ".(!empty($_GET['category']) && $_GET['category'] == $cat_tab ? 'selected' : '')." value='?category=".$cat_tab."'>".$cat_tab."</option>";
								}
							?>
						</select>
					</div>
				</div>
			<?php } else {
				echo "<a href='?category=Top'><button type='button' class='btn brand-btn mobile-block ".(empty($_GET['category']) || $_GET['category'] == 'Top' ? 'active_tab' : '')."' >Last 25 Service Records</button></a>&nbsp;&nbsp;";
				foreach ($each_tab as $cat_tab) {
					echo "<a href='?category=".$cat_tab."'><button type='button' class='btn brand-btn mobile-block ".(!empty($_GET['category']) && $_GET['category'] == $cat_tab ? 'active_tab' : '')."' >".$cat_tab."</button></a>&nbsp;&nbsp;";
				}
			} ?>
		</div>
		<div class="clearfix"></div>

		<div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
			<div class="col-sm-11"><span class="notice-name">NOTE:</span>
			Through this section, full services records and tracking can be done on all equipment. These records are ideal for reporting, sales, year end and tracking the profit and losses on all equipment.</div>
			<div class="clearfix"></div>
		</div>

		<form name="form_sites" method="post" action="" class="form-inline" role="form">

            <center>
            <div class="form-group">
                <label for="site_name" class="col-sm-5 control-label">Search By Any:</label>
                <div class="col-sm-6">
					<?php if(isset($_POST['search_equipment_submit'])) { ?>
						<input type="text" name="search_equipment" value="<?php echo $_POST['search_equipment']?>" class="form-control">
					<?php } else { ?>
						<input type="text" name="search_equipment" class="form-control">
					<?php } ?>
                </div>
            </div>
            &nbsp;
				<button type="submit" name="search_equipment_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
				<button type="submit" name="display_all_equipment" value="Display All" class="btn brand-btn mobile-block">Display All</button>
            </center>

            <?php
                if(vuaed_visible_function($dbc, 'equipment') == 1) {
                    echo '<a href="add_equipment_service_record.php?category='.$category.'" class="btn brand-btn mobile-block gap-bottom pull-right double-gap-top">Add Service Record</a>';
                }
            ?>

		<div id="no-more-tables">
			<?php
			$rowsPerPage = 25;
			$pageNum = 1;

			if(isset($_GET['page'])) {
				$pageNum = $_GET['page'];
			}

			$offset = ($pageNum - 1) * $rowsPerPage;
			
			$equipment = '';
			if (isset($_POST['search_equipment_submit'])) {
				$equipment = $_POST['search_equipment'];
                if (isset($_POST['search_equipment'])) {
                    $equipment = $_POST['search_equipment'];
                }
                if ($_POST['search_category'] != '') {
                    $equipment = $_POST['search_category'];
                }
			}
			if (isset($_POST['display_all_equipment'])) {
				$equipment = '';
			}

			if($equipment != '') {
				$query_check_credentials = "SELECT * FROM equipment_service_record WHERE (service_date LIKE '%" . $equipment . "%' OR advised_service_date LIKE '%" . $equipment . "%')";
			} else {
                if((empty($_GET['category'])) || ($_GET['category'] == 'Top')) {
                    $query_check_credentials = "SELECT esr.*,equipment.* FROM equipment_service_record esr, equipment WHERE equipment.equipmentid = esr.equipmentid $access_query ORDER BY servicerecordid DESC LIMIT 25";
                    $query = "SELECT 25 numrows";
                } else {
                    $category = $_GET['category'];
                    $query_check_credentials = "SELECT esr.*,equipment.* FROM equipment_service_record esr, equipment WHERE equipment.equipmentid = esr.equipmentid AND equipment.category='$category' $access_query LIMIT $offset, $rowsPerPage";
                    $query = "SELECT COUNT(*) numrows FROM equipment_service_record esr, equipment WHERE equipment.equipmentid = esr.equipmentid AND equipment.category='$category' $access_query";
                }
			}

			$result = mysqli_query($dbc, $query_check_credentials);

			if(mysqli_num_rows($result) > 0) {

                if(empty($_GET['category']) || $_GET['category'] == 'Top') {
                    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='service_record' AND equipment_dashboard IS NOT NULL"));
                    $value_config = ','.$get_field_config['equipment_dashboard'].',';
                } else {
                    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='service_record'"));
                    $value_config = ','.$get_field_config['equipment_dashboard'].',';
                }
				echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);

			    echo "<table class='table table-bordered'>";
			    echo "<tr class='hidden-xs hidden-sm'>";
                if (strpos($value_config, ','."Service Date".',') !== FALSE) {
                    echo '<th>Service Date</th>';
                }
                if (strpos($value_config, ','."Advised Service Date".',') !== FALSE) {
                    echo '<th>Advised Service Date</th>';
                }
                if (strpos($value_config, ','."Equipment".',') !== FALSE) {
                    echo '<th>Equipment</th>';
                }
                if (strpos($value_config, ','."Service Type".',') !== FALSE) {
                    echo '<th>Service Type</th>';
                }
                if (strpos($value_config, ','."Inventory".',') !== FALSE) {
                    echo '<th>Inventory</th>';
                }
                if (strpos($value_config, ','."Description of Job".',') !== FALSE) {
                    echo '<th>Description of Job</th>';
                }
                if (strpos($value_config, ','."Service Record Mileage".',') !== FALSE) {
                    echo '<th>Service Record Mileage</th>';
                }
                if (strpos($value_config, ','."Hours".',') !== FALSE) {
                    echo '<th>Hours</th>';
                }
                if (strpos($value_config, ','."Completed".',') !== FALSE) {
                    echo '<th>Completed</th>';
                }
                if (strpos($value_config, ','."Staff".',') !== FALSE) {
                    echo '<th>Staff</th>';
                }
                if (strpos($value_config, ','."Vendor".',') !== FALSE) {
                    echo '<th>Vendor</th>';
                }
                if (strpos($value_config, ','."Service Record Cost".',') !== FALSE) {
                    echo '<th>Service Record Cost</th>';
                }
				echo "<th>Function</th>";
                echo "</tr>";
				while($row = mysqli_fetch_array( $result ))
				{
					echo '<tr>';
					$equipment = $row['unit_number'];
					$equipment .= ' : '.$row['serial_number'];
					$equipment .= ' : '.$row['type'];
					$equipment .= ' : '.$row['category'];
					$equipment .= ' : '.$row['make'];
					$equipment .= ' : '.$row['model'];
					$equipment .= ' : '.$row['year_purchased'];
					$equipment .= ' : '.$row['mileage'];

					if (strpos($value_config, ','."Service Date".',') !== FALSE) {
						echo '<td data-title="Srv. Date">' . $row['service_date'] . '</td>';
					}
					if (strpos($value_config, ','."Advised Service Date".',') !== FALSE) {
						echo '<td data-title="Advised Srv. Date">' . $row['advised_service_date'] . '</td>';
					}
					if (strpos($value_config, ','."Equipment".',') !== FALSE) {
						echo '<td data-title="Serial Number">' . $equipment . '</td>';
					}
					if (strpos($value_config, ','."Service Type".',') !== FALSE) {
						echo '<td data-title="Service Type">' . $row['service_type'] . '</td>';
					}

					if (strpos($value_config, ','."Inventory".',') !== FALSE) {
						$inventoryid = $row['inventoryid'];
						$inventory = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventoryid, code, category, sub_category FROM inventory WHERE inventoryid='$inventoryid'"));
						if($inventoryid != '') {
							echo '<td data-title="Inventory Item">' . $inventory['code'].' : '.$inventory['category']. ' : '.$inventory['sub_category'] . '</td>';
						} else {
							echo '<td>-</td>';
						}
					}

					if (strpos($value_config, ','."Description of Job".',') !== FALSE) {
						echo '<td data-title="Job Desc">' . $row['description_of_job'] . '</td>';
					}
					if (strpos($value_config, ','."Service Record Mileage".',') !== FALSE) {
						echo '<td data-title="Srv Rec. Mileage">' . $row['service_record_mileage'] . '</td>';
					}
					if (strpos($value_config, ','."Hours".',') !== FALSE) {
						echo '<td data-title="Hours">' . $row['service_record_hours'] . '</td>';
					}
					if (strpos($value_config, ','."Completed".',') !== FALSE) {
						echo '<td data-title="Completed">' . $row['completed'] . '</td>';
					}

					if (strpos($value_config, ','."Staff".',') !== FALSE) {
						$contactid = $row['contactid'];
						$staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name FROM contacts WHERE contactid='$contactid'"));
						if($contactid != '') {
							echo '<td data-title="Staff">' . decryptIt($staff['first_name']).' '.decryptIt($staff['last_name']) . '</td>';
						} else {
							echo '<td>-</td>';
						}
					}
					if (strpos($value_config, ','."Vendor".',') !== FALSE) {
						$vendorid = $row['vendorid'];
						$vendor = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name FROM contacts WHERE contactid='$vendorid'"));
						if($vendorid != '') {
							echo '<td data-title="Vendor">' . decryptIt($vendor['name']) . '</td>';
						} else {
							echo '<td>-</td>';
						}
					}
					if (strpos($value_config, ','."Service Record Cost".',') !== FALSE) {
						echo '<td data-title="Srv. Cost">' . $row['cost'] . '</td>';
					}

					echo '<td data-title="Function">';
					echo '<a href=\'add_equipment_service_record.php?servicerecordid='.$row['servicerecordid'].'\'>Edit</a> | ';
					//echo '<a href=\'delete_restore.php?action=delete&servicerecordid='.$row['servicerecordid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a>';
					echo '</td>';

					echo "</tr>";
				}

				echo '</table>';
				echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
				if(vuaed_visible_function($dbc, 'equipment') == 1) {
					echo '<a href="add_equipment_service_record.php?category='.$category.'" class="btn brand-btn mobile-block gap-bottom pull-right">Add Service Record</a>';
				}
			} else{
				echo "<h2>No Record Found.</h2>";
			}
            //echo display_filter('equipment.php');

			?>

		</div>
	</div>
</div>

<?php include ('../footer.php'); ?>