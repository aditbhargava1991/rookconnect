<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0); ?>


		<?php $search_equip = (!empty($_GET['type']) ? filter_var($_GET['type'],FILTER_SANITIZE_STRING) : 'active');
		$search_equip = '';
		
		if (isset($_POST['search_equip'])) {
			$search_equip = $_POST['search_equip'];
		} ?>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <center><div class="form-group">
		<div class="col-sm-5">
			<label class="col-sm-4">Equipment:</label>
			<div class="col-sm-8"><?php $equip_list = mysqli_query($dbc, "SELECT `equipmentid`, `category`, `unit_number` FROM `equipment` WHERE `equipmentid` IN (SELECT `equipmentid` FROM `equipment_history`)"); ?>
			<select name="search_equip" class="chosen-select-deselect form-control"><option></option>
				<?php while($row = mysqli_fetch_array($equip_list)) {
					echo "<option ".($row['equipmentid'] == $search_equip ? 'selected' : '')." value='".$row['equipmentid']."'>".$row['category'].": Unit #".$row['unit_number']."</option>";
				} ?>
			</select></div>
		</div>
        <div class="col-sm-2"><button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
        <button type="button" onclick="window.location=''" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button></div>
        </div></center>
        <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>

            <input type="hidden" name="report_status" value="<?php echo $search_status; ?>">
            <input type="hidden" name="report_from" value="<?php echo $search_from; ?>">
            <input type="hidden" name="report_until" value="<?php echo $search_until; ?>">
            <br><br>

            <?php
                echo work_orders($dbc, $search_equip);
            ?>

        </form>

<?php
function work_orders($dbc, $equip = '', $table_style = '', $table_row_style = '', $grand_total_style = '') {
    $report_data = '';
	$result = mysqli_query($dbc, "SELECT *, `equipment_history`.`notes` as `history_notes` FROM `equipment_history` LEFT JOIN `equipment` ON `equipment_history`.`equipmentid`=`equipment`.`equipmentid` WHERE `equipment`.`equipmentid`='$equip' OR '$equip'='' ORDER BY `log_time`");
	
	if(mysqli_num_rows($result) == 0) {
		return "<h3>No Equipment History Found</h3>";
	}

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th>Unit #</th>
			<th>Date</th>
			<th>History</th>';
    $report_data .=  "</tr>";

    while($history = mysqli_fetch_array( $result ))
    {
		$report_data .= "<tr>";
		$report_data .= "<td data-title='Unit #'>".$history['category'].": Unit #".$history['unit_number']."</td>";
		$report_data .= "<td data-title='Date'>".date('Y-m-d g:i A', strtotime($history['log_time']))."</td>";
		$report_data .= "<td data-title='History'>".$history['history_notes']."</td>";
		$report_data .= "</tr>";
    }

    $report_data .= '</table>';

    return $report_data;
}
?>
<script>
$('document').ready(function() {
    var tables = $('table');

    tables.map(function(idx, table) {
        var rows = $(table).find('tbody > tr');
        rows.map(function(idx, row){
            if(idx%2 == 0) {
                $(row).css('background-color', '#e6e6e6');
            }
        })
    })
})
</script>