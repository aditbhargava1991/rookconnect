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
			url: 'dashboard_service_request.php'+value+'&mobile_view=1',
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
					<option value="?tab=service_request&category=Top">Last 25 Service Requests</option>
					<?php
						foreach ($each_tab as $cat_tab) {
							echo "<option ".(!empty($_GET['category']) && $_GET['category'] == $cat_tab ? 'selected' : '')." value='?tab=service_request&category=".$cat_tab."'>".$cat_tab."</option>";
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
            echo '<a href="?edit_service_request=1&category='.$category.'" class="btn brand-btn mobile-block gap-bottom pull-right double-gap-top">Add Service Request</a>';
        }
    ?>

<div class="clearfix double-gap-top"></div>

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
		$query_check_credentials = "SELECT * FROM equipment_service_request WHERE (defect LIKE '%" . $equipment . "%' OR comment LIKE '%" . $equipment . "%')";
	} else {
        if((empty($_GET['category'])) || ($_GET['category'] == 'Top')) {
            $query_check_credentials = "SELECT esr.*,equipment.* FROM equipment_service_request esr, equipment WHERE equipment.equipmentid = esr.equipmentid $access_query ORDER BY requestid DESC LIMIT 25";
			$query = "SELECT 25 numrows";
        } else {
            $category = $_GET['category'];
            $query_check_credentials = "SELECT esr.*,equipment.* FROM equipment_service_request esr, equipment WHERE equipment.equipmentid = esr.equipmentid AND equipment.category='$category' $access_query LIMIT $offset, $rowsPerPage";
            $query = "SELECT COUNT(*) numrows FROM equipment_service_request esr, equipment WHERE equipment.equipmentid = esr.equipmentid AND equipment.category='$category' $access_query";
        }
	}

	$result = mysqli_query($dbc, $query_check_credentials);

	if(mysqli_num_rows($result) > 0) {

        if(empty($_GET['category']) || $_GET['category'] == 'Top') {
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='service_request' AND equipment_dashboard IS NOT NULL"));
            $value_config = ','.$get_field_config['equipment_dashboard'].',';
        } else {
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='service_request'"));
            $value_config = ','.$get_field_config['equipment_dashboard'].',';
        }
		echo '<div class="pagination_links">';
	    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	    echo '</div>';

	    echo "<table class='table table-bordered'>";
	    echo "<tr class='hidden-xs hidden-sm'>";
        if (strpos($value_config, ','."Equipment".',') !== FALSE) {
            echo '<th>Equipment</th>';
        }

        if (strpos($value_config, ','."Defects".',') !== FALSE) {
            echo '<th>Defects</th>';
        }

        if (strpos($value_config, ','."Comment".',') !== FALSE) {
            echo '<th>Comment</th>';
        }

		echo "</tr>";
		while($row = mysqli_fetch_array( $result ))
		{
			echo '<tr>';
			if (strpos($value_config, ','."Equipment".',') !== FALSE) {
				echo '<td data-title="Description">' . $row['unit_number'].':'.$row['model'].':'.$row['type'] . '</td>';
			}

			if (strpos($value_config, ','."Defects".',') !== FALSE) {
				echo '<td data-title="Tab">' . $row['defect'] . '</td>';
			}

			if (strpos($value_config, ','."Comment".',') !== FALSE) {
				echo '<td data-title="Tab">' . html_entity_decode($row['comment']) . '</td>';
			}

			echo "</tr>";
		}

		echo '</table>';
		echo '<div class="pagination_links">';
	    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	    echo '</div>';
	} else{
		echo "<h2>No Record Found.</h2>";
	}
    if(vuaed_visible_function($dbc, 'equipment') == 1) {
	echo '<a href="?edit_service_request=1&category='.$category.'" class="btn brand-btn mobile-block gap-bottom pull-right">Add Service Request</a>';
    }
    //echo display_filter('equipment.php');

	?>

</div>
</form>