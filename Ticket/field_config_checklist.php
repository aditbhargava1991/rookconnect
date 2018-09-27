<?php error_reporting(0);
include_once('../include.php');


?>
<script>
function handleClick(cb) {
	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=checklist_tile&checklistid='+cb.value+'&checked='+cb.checked,
		method: 'GET',
		dataType: 'html',
		success: function(response) {
		}
	});
}
</script>
<div class="standard-dashboard-body-title">
    <h3>Settings - Checklist:</h3>
</div>

<div class="standard-dashboard-body-content full-height">
    <div class="dashboard-item dashboard-item2 full-height">
        <form class="form-horizontal">

        <?php
            /* Pagination Counting */
            $rowsPerPage = 25;
            $pageNum = 1;

            if(isset($_GET['page'])) {
                $pageNum = $_GET['page'];
            }

            $offset = ($pageNum - 1) * $rowsPerPage;

            $query_check_credentials = "SELECT * FROM checklist WHERE deleted = 0 ORDER BY checklistid DESC LIMIT $offset, $rowsPerPage";
            $query = "SELECT count(*) as numrows FROM checklist WHERE deleted = 0 ORDER BY checklistid DESC";

            $result = mysqli_query($dbc, $query_check_credentials);

            $num_rows = mysqli_num_rows($result);

            if($num_rows > 0) {

                // Added Pagination //
                echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                // Pagination Finish //

                echo "<table class='table table-bordered'>";
			    echo "<tr class='hidden-sm hidden-xs'>";
                        echo '<th>Checklist Name</th>';
                        echo '<th>Tab</th>';
                        echo '<th>Assigned Staff</th>';
                       echo '<th>Create Tile</th>';
                    echo "</tr>";
            } else {
                echo "<h2>No Record Found.</h2>";
            }
            while($row = mysqli_fetch_array( $result ))
            {
                echo "<tr>";

       				echo '<td data-title="Type">' . $row['checklist_name'] . '</td>';
       				echo '<td data-title="Type">' . $row['security'] .' : '.$row['checklist_type'] . '</td>';
       				echo '<td data-title="Type">' .  get_multiple_contact($dbc, $row['assign_staff']) . '</td>';
                    $checked = '';
                    if($row['checklist_tile'] == 1) {
                        $checked = 'checked';
                    }
       				echo '<td data-title="Type"><input type="checkbox" onclick="handleClick(this);" '. $checked.' class = "checklist_tile" id = '.$row['checklistid'].' name="checklist_tile[]" value="'.$row['checklistid'].'" /></td>';
                echo "</tr>";
            }

            echo "</table>";
            ?>
        </form>
    </div><!-- .dashboard-item -->
</div><!-- .standard-dashboard-body-content -->


<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_additions.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>