<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
include_once ('../Equipment/region_location_access.php');
?>
<script type="text/javascript">
$(document).on('change', 'select[name="search_category"]', function() { changeCategory(this); });
function changeCategory(sel) {
	var value = sel.value;
	<?php if($_GET['mobile_view'] == 1) { ?>
		var panel = $(sel).closest('.panel').find('.panel-body');
		panel.html('Loading...');
		$.ajax({
			url: 'dashboard_service_record.php'+value+'&mobile_view=1',
			method: 'GET',
			response: 'html',
			success: function(response) {
				panel.html(response);
				$('.pagination_links a').click(pagination_load);
			}
		});
	<?php } else { ?>
		location = value;
	<?php } ?>
}
</script>

<?php $category = $_GET['category'];
$each_tab = explode(',', get_config($dbc, 'equipment_tabs'));
if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
	<div class="gap-left tab-container col-sm-12">
		<div class="row">
			<label class="control-label col-sm-2">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Filter equipment by Tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                Tab:
            </label>
			<div class="col-sm-4">
				<select name="search_category" class="chosen-select-deselect form-control mobile-100-pull-right category_actual">
					<option value="?tab=service_record&category=Top">Last 25 Service Records</option>
					<?php
						foreach ($each_tab as $cat_tab) {
							echo "<option ".(!empty($_GET['category']) && $_GET['category'] == $cat_tab ? 'selected' : '')." value='?tab=service_record&category=".$cat_tab."'>".$cat_tab."</option>";
						}
					?>
				</select>
			</div>
		</div>
	</div>
<?php } ?>
<div class="clearfix"></div>

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
            echo '<a href="?edit_service_record=1&category='.$category.'" class="btn brand-btn mobile-block gap-bottom pull-right double-gap-top">Add Service Record</a>';
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
		echo '<div class="pagination_links">';
	    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	    echo '</div>';

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
            echo '<th>Service Tab</th>';
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
				echo '<td data-title="Service Tab">' . $row['service_type'] . '</td>';
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
			echo '<a href=\'?edit_service_record=1&servicerecordid='.$row['servicerecordid'].'\'>Edit</a> | ';
			//echo '<a href=\'delete_restore.php?action=delete&servicerecordid='.$row['servicerecordid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a>';
			echo '</td>';

			echo "</tr>";
		}

		echo '</table>';
		echo '<div class="pagination_links">';
	    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	    echo '</div>';
		if(vuaed_visible_function($dbc, 'equipment') == 1) {
			echo '<a href="?edit_service_record=1&category='.$category.'" class="btn brand-btn mobile-block gap-bottom pull-right">Add Service Record</a>';
		}
	} else{
		echo "<h2>No Record Found.</h2>";
	}
    //echo display_filter('equipment.php');

	?>

</div>
</form>