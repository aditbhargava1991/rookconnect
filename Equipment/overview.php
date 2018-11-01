<?php include_once('../include.php');
$equipmentid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
if($equipmentid > 0) {
    $result = $dbc->query("SELECT * FROM `equipment` WHERE `equipmentid`='$equipmentid'");
    while($row = mysqli_fetch_array( $result )) {
        $value_config = get_config($dbc, 'equipment_overview_'.config_safe_str($row['category']));

	    echo '<div class="standard-body-title"><h3 style="margin-top: 0.5em;">'.(vuaed_visible_function($dbc, 'equipment') == 1 ? '<a href="?edit='.$row['equipmentid'].'">' : '').get_equipment_label($dbc, $row).(vuaed_visible_function($dbc, 'equipment') == 1 ? '</a>' : '').'</h3></div>';

        echo '<div class="standard-body-content">';
	    echo '</div>';
	}
} ?>