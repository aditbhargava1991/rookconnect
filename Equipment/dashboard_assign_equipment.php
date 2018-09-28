<?php include_once('../include.php');
checkAuthorised('equipment');
$security = get_security($dbc, 'equipment');

if(!empty($_FILES['upload']['name'])) {
	include('upload_csv.php');
}
?>
<script>
$(document).on('change', 'select[name="search_category"]', function() { changeCategory(this); });
function changeCategory(sel) {
	var value = sel.value;
	<?php if($_GET['mobile_view'] == 1) { ?>
		var panel = $(sel).closest('.panel').find('.panel-body');
		panel.html('Loading...');
		$.ajax({
			url: 'dashboard_assign_equipment.php'+value+'&mobile_view=1',
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
function send_csv() {
	$('[name=upload]').change(function() {
		$('form').submit();
	});
	$('[name=upload]').click();
}
</script>
<?php $status = (empty($_GET['status']) ? 'Active' : $_GET['status']);
$equipment_main_tabs = explode(',',get_config($dbc, 'equipment_main_tabs'));
include_once('../Equipment/region_location_access.php'); ?>

<?php
$category = $_GET['category'];
$each_tab = explode(',', get_config($dbc, 'equipment_tabs'));

if (get_config($dbc, 'show_category_dropdown_equipment') == '1') { ?>
    <div class="gap-left tab-container col-sm-10">
        <div class="row">
			<label class="control-label col-sm-2">
                <span class="popover-examples" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Filter equipment by Tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                Tab:
            </label>
			<div class="col-sm-4">
				<select name="search_category" class="chosen-select-deselect form-control mobile-100-pull-right category_actual">
					<option value="?tab=assign_equipment&category=Top">Top 25</option>
					<?php
						foreach ($each_tab as $cat_tab) {
							echo "<option ".(!empty($_GET['category']) && $_GET['category'] == $cat_tab ? 'selected' : '')." value='?tab=assign_equipment&category=".$cat_tab."'>".$cat_tab."</option>";
						}
					?>
				</select>
			</div>
        </div>
	</div>
<?php } ?>

<?php if(vuaed_visible_function($dbc, 'equipment') == 1) {
	echo '<div class="gap-bottom pull-right">';
        echo '<span class="popover-examples" style="margin:0 2px;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a new equipment Assignment."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
        echo '<a href="?edit_assigned_equipment=1" class="btn brand-btn mobile-block">Add Assignment</a>';
    echo '</div>';
} ?>

<div class="clearfix double-gap-top"></div>

<div id="no-more-tables"><?php
$rowsPerPage = 25;
$pageNum = 1;

if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;
$query = "SELECT COUNT(*) numrows FROM equipment WHERE deleted = 0 AND (`equipmentid` IN (SELECT `assign_to_equip` FROM `equipment`) OR `assigned_staff` > 0) AND (`category` = '".$_GET['category']."' OR '".$_GET['category']."' IN ('Top','')) $access_query";
$query_check_credentials = "SELECT * FROM equipment WHERE deleted = 0 AND (`equipmentid` IN (SELECT `assign_to_equip` FROM `equipment`) OR `assigned_staff` > 0) AND (`category` = '".$_GET['category']."' OR '".$_GET['category']."' IN ('Top','')) $access_query LIMIT $offset, $rowsPerPage";
$result = mysqli_query($dbc, $query_check_credentials);
$num_rows = mysqli_num_rows($result);

if($num_rows > 0) {
	// Added Pagination //
	echo '<div class="pagination_links">';
    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    echo '</div>';
	// Pagination Finish //
	
	echo "<table class='table table-bordered'><tr class='hidden-xs'>";
	echo "<th>Equipment</th>";
	echo "<th>Staff</th>";
	echo "<th>Assigned</th>";
	echo "<th>Function</th>";
	echo "</tr>";
    while($row = mysqli_fetch_array( $result )) {
		echo "<tr>";
		echo "<td data-title='Equipment'>".$row['category']." ".$row['make']." ".$row['model']." ".$row['unit_number']."</td>";
		echo "<td data-title='Staff'>".get_contact($dbc, $row['assigned_staff'])."</td>";
		echo "<td data-title='Assigned'>";
		$equipment_list = mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `assign_to_equip`='".$row['equipmentid']."'");
		while($equip_row = mysqli_fetch_array($equipment_list)) {
			echo $equip_row['category']." ".$equip_row['make']." ".$equip_row['model']." ".$equip_row['unit_number']."<br />";
		}
		echo "</td>";
		echo "<td data-title='Function'><a href='?edit_assigned_equipment=1&equipmentid=".$row['equipmentid']."'>Edit</a></td>";
		echo "</tr>";
	}
	echo '</table>';

	// Added Pagination //
	echo '<div class="pagination_links">';
    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    echo '</div>';
	// Pagination Finish //
} else {
	echo "<h2>No Assignments Found</h2>";
}

if(vuaed_visible_function($dbc, 'equipment') == 1) {
    echo '<div class="gap-bottom pull-right">';
        echo '<span class="popover-examples" style="margin:0 2px;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a new equipment Assignment."><img src="'. WEBSITE_URL .'/img/info.png" width="20"></a></span>';
        echo '<a href="?edit_assigned_equipment=1" class="btn brand-btn mobile-block">Add Assignment</a>';
    echo '</div>';
} ?>

</div>