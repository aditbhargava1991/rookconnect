<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
$report_fields = explode(',', get_config($dbc, 'report_operation_fields')); ?>

            <?php
                echo driving_logs($dbc);
            ?>

<?php
function driving_logs($dbc) {
    $report_data = '';

    $sql = "SELECT * FROM `site_work_driving_log`";
	$result = mysqli_query($dbc, $sql.' ORDER BY `drive_date` DESC, `drive_time` DESC');
	
	if(mysqli_num_rows($result) == 0) {
		return "<h3>No Driving Logs Found</h3>";
	}

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th>Staff Name</th>
			<th>Equipment Category</th>
			<th>Equipment</th>
			<th>Log Date</th>
			<th>Driving Time</th>
			<th>PDF Driving Safety Checklist</th>
			<th>Repair Requested</th>
			<th>End Driving Time</th>';
    $report_data .=  "</tr>";

    while($log = mysqli_fetch_array( $result ))
    {
		$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='".$log['equipment']."'"));
		$checklist = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `site_work_driving_inspect` WHERE `drivinglogid`='".$log['log_id']."'"));
		$repair = 'No';
		foreach($checklist as $response) {
			if($response == 'Yes') {
				$repair = 'Yes';
			}
		}
        $report_data .= '<tr nobr="true">
			<td data-title="Staff Name:">'.get_contact($dbc, $log['staff']).'</td>
			<td data-title="Equipment Category:">'.$equipment['category'].'</td>
			<td data-title="Equipment:">'.$equipment['unit_number'].(empty($equipment['model']) ? '' : ': '.$equipment['model']).(empty($equipment['label']) ? '' : ': '.$equipment['label']).'</td>
			<td data-title="Log Date:">'.$log['drive_date'].'</td>
			<td data-title="Start Driving Time:">'.$log['drive_time'].'</td>
			<td data-title="PDF Driving Safety Checklist:"><a href="../Site Work Orders/download/driving_log_'.$log['log_id'].'.pdf">View</a></td>
			<td data-title="Repair Requested:">'.$repair.'</td>
			<td data-title="End Driving Time:">'.$log['end_drive_time'].'</td></tr>';
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