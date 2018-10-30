<?php /* Pagination Counting */
$rowsPerPage = 25;
$pageNum = 1;

if(isset($_GET['page'])) {
    $pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

$status_search = '';
if($status == 'Active') {
	$status_search = " AND IFNULL(`status`,'') NOT IN ('Inactive')";
} else {
	$status_search = " AND IFNULL(`status`,'') IN ('Inactive')";
}
if(!empty($equipment)) {
	if(!empty($_GET['category']) && $_GET['category'] != 'Top') {
		$category_query = " AND category='$category'";
	}
    $query_check_credentials = "SELECT * FROM equipment WHERE deleted=0 $status_search AND (unit_number LIKE '%" . $equipment . "%' OR type LIKE '%" . $equipment . "%' OR category LIKE '%" . $equipment . "%' OR ownership_status LIKE '%" . $equipment . "%' OR make LIKE '%" . $equipment . "%' OR model LIKE '%" . $equipment . "%' OR model_year LIKE '%" . $equipment . "%' OR cost LIKE '%" . $equipment . "%' OR region LIKE '%" . $equipment . "%' OR location LIKE '%" . $equipment . "%' OR classification LIKE '%" . $equipment . "%') $category_query $access_query ORDER BY ABS(unit_number) LIMIT $offset, $rowsPerPage";
    $query = "SELECT count(*) as numrows FROM equipment WHERE deleted=0 $status_search AND (unit_number LIKE '%" . $equipment . "%' OR type LIKE '%" . $equipment . "%' OR category LIKE '%" . $equipment . "%' OR ownership_status LIKE '%" . $equipment . "%' OR make LIKE '%" . $equipment . "%' OR model LIKE '%" . $equipment . "%' OR model_year LIKE '%" . $equipment . "%' OR cost LIKE '%" . $equipment . "%' OR region LIKE '%" . $equipment . "%' OR location LIKE '%" . $equipment . "%' OR classification LIKE '%" . $equipment . "%') $category_query $access_query ORDER BY ABS(unit_number)";
} else {
    if((empty($_GET['category'])) || ($_GET['category'] == 'Top')) {
        $query_check_credentials = "SELECT * FROM equipment WHERE deleted = 0 $status_search $access_query ORDER BY ABS(unit_number) DESC LIMIT 25";
        $query = "SELECT 25 as numrows";
    } else {
        $category = $_GET['category'];
        $query_check_credentials = "SELECT * FROM equipment WHERE deleted = 0 $status_search AND category='$category' $access_query ORDER BY ABS(unit_number) LIMIT $offset, $rowsPerPage";
        $query = "SELECT count(*) as numrows FROM equipment WHERE deleted = 0 $status_search AND category='$category'".$access_query;
    }
}
$result = mysqli_query($dbc, $query_check_credentials);

$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
    if(empty($_GET['category']) || $_GET['category'] == 'Top') {
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE `tab` NOT IN ('service_record','service_request') AND equipment_dashboard IS NOT NULL"));
        $value_config = ',Category,'.$get_field_config['equipment_dashboard'].',';
    } else {
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT equipment_dashboard FROM field_config_equipment WHERE tab='$category' AND accordion IS NULL UNION SELECT equipment_dashboard FROM field_config_equipment WHERE `tab` NOT IN ('service_record','service_request') AND equipment_dashboard IS NOT NULL"));
		$value_config = ','.$get_field_config['equipment_dashboard'].',';
    }

    // Added Pagination //
    if(isset($query)) {
    	echo '<div class="pagination_links">';
        echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
        echo '</div>';
    }
    // Pagination Finish //

    while($row = mysqli_fetch_array( $result )) {

	    $color = '';
	    if($row['status'] == 'In equipment') {
	        $color = 'style="color: white;"';
	    }
	    if($row['status'] == 'In transit from vendor') {
	        $color = 'style="color: red;"';
	    }
	    if($row['status'] == 'In transit between yards') {
	        $color = 'style="color: blue;"';
	    }
	    if($row['status'] == 'Not confirmed in yard by equipment check') {
	        $color = 'style="color: yellow;"';
	    }
	    if($row['status'] == 'Assigned to job') {
	        $color = 'style="color: green;"';
	    }
	    if($row['status'] == 'In transit and assigned') {
	        $color = 'style="color: purple;"';
	    }

	    echo '<div class="dashboard-item">';
	    echo '<h3 style="margin-top: 0.5em;">'.(vuaed_visible_function($dbc, 'equipment') == 1 ? '<a href="?edit='.$row['equipmentid'].'">' : '').get_equipment_label($dbc, $row).(vuaed_visible_function($dbc, 'equipment') == 1 ? '</a>' : '').'</h3>';

		$quick_actions = explode(',',get_config($dbc, 'equipment_quick_action_icons'));
        echo '<span class="pull-right action-icons double-gap-bottom gap-top" style="width: 100%;">';

        if($row['favorite'] == 0) {
            $fav_image = 'ROOK-star-empty-icon.png';
        } else {
            $fav_image = 'ROOK-star-icon.png';
        }
        echo in_array('favorite',$quick_actions) ? '<span class="no-toggle" title="Favorite" onclick="favorite_item(\''.$row['equipmentid'].'\',this); return false;"><img src="'.WEBSITE_URL.'/img/icons/'.$fav_image.'" style="height:100%;" onclick="return false;" class="inline-img favorite"></span>' : '';

        echo in_array('preview', $quick_actions) ? '<span title="View Equipment" onclick="overlayIFrameSlider(\'edit_equipment.php?edit='.$row['equipmentid'].'&view=readonly&iframe_slider=1\',\'auto\',true,true); return false;"><img title="View Equipment" src="../img/icons/eyeball.png" class="inline-img no-toggle" onclick="return false;"></span>' : '';
        echo in_array('flag_manual',$quick_actions) ? '<span class="no-toggle" title="Flag This!" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/quick_action_flags.php?tile=equipment&id='.$row['equipmentid'].'\',\'auto\',true,true);  return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:100%;" onclick="return false;" class="inline-img"></span>' : '';
        echo !in_array('flag_manual',$quick_actions) && in_array('flag',$quick_actions) ? '<span class="no-toggle" title="Flag This!" onclick="flag_item(\''.$row['equipmentid'].'\',this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:100%;" onclick="return false;" class="inline-img"></span>' : '';
        if (in_array('edit', $quick_actions)) { ?>
            <span  onclick="overlayIFrameSlider('<?=WEBSITE_URL?>/Equipment/edit_equipment.php?edit=<?=$row['equipmentid']?>&iframe_slider=1'); return false;"><img src="<?=WEBSITE_URL?>/img/icons/ROOK-edit-icon.png" title="Edit Equipment" class="inline-img no-toggle" onclick="return false;"></span><?php
        }
        echo in_array('reminder', $quick_actions) ? '<span title="Schedule Reminder" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/quick_action_reminders.php?tile=equipment&id='.$row['equipmentid'].'\'); return false;"><img title="Schedule Reminder" src="../img/icons/ROOK-reminder-icon.png" class="inline-img no-toggle" onclick="return false;"></span>' : '';
	    echo in_array('archive', $quick_actions) && vuaed_visible_function($dbc, 'equipment') == 1 ? '<span title="Archive Equipment"><a href="'.WEBSITE_URL.'/delete_restore.php?action=delete&equipmentid='.$row['equipmentid'].'" onclick="return confirm(\'Are you sure?\');"><img src="../img/icons/trash-icon-red.png" title="Archive Equipment" class="inline-img no-toggle"></a></span>' : '';
	    echo '</span>';

	    if (!empty($row['equipment_image']) && file_exists('download/'.$row['equipment_image'])) {
	    	echo '<div class="col-sm-6"><img src="download/'.$row['equipment_image'].'" class="pull-left thumbnail-small" style="margin: 0.5em; max-width: 150px; height: auto;"></div>';
	    }
	    if (strpos($value_config, ','."Unit #".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Unit #:</label>
			<div class="col-sm-8">' . $row['unit_number'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."VIN #".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">VIN #:</label>
			<div class="col-sm-8">' . $row['vin_number'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Serial #".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Serial #:</label>
			<div class="col-sm-8">' . $row['serial_number'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Description".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Description:</label>
			<div class="col-sm-8">' . $row['equ_description'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Category".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Tab:</label>
			<div class="col-sm-8">' . $row['category'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Type".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Tab:</label>
			<div class="col-sm-8">' . $row['type'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Make".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Make:</label>
			<div class="col-sm-8">' . $row['make'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Model".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Model:</label>
			<div class="col-sm-8">' . $row['model'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Unit of Measure".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Unit of Measure:</label>
			<div class="col-sm-8">' . $row['submodel'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Model Year".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Model Year:</label>
			<div class="col-sm-8">' . $row['model_year'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Label".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Equipment Label:</label>
			<div class="col-sm-8">' . $row['label'] . '</div>
		</div>';
	    }
		if (strpos($value_config, ','."Total Kilometres".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Total Kilometres:</label>
			<div class="col-sm-8">' . $row['total_kilometres'] . '</div>
		</div>';
	    }
		if (strpos($value_config, ','."Total Kilometres".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Leased:</label>
			<div class="col-sm-8">' . ($row['leased'] > 0 ? 'Yes' : 'No') . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Style".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Style:</label>
			<div class="col-sm-8">' . $row['style'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Vehicle Size".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Vehicle Size:</label>
			<div class="col-sm-8">' . $row['vehicle_size'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Color".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Color:</label>
			<div class="col-sm-8">' . $row['color'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Trim".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Trim:</label>
			<div class="col-sm-8">' . $row['trim'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Staff".',') !== FALSE) {
	    $staff_list = [];
	    foreach(explode(',', $row['staffid']) as $staff_id) {
	    	if($staff_id > 0) {
	    		$staff_list[] = get_contact($dbc, $staff_id);
	    	}
	    }
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Staff:</label>
			<div class="col-sm-8">' . implode(', ', $staff_list) . '</div>
		</div>';
	    }

	    if (strpos($value_config, ','."Fuel Type".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Fuel Tab:</label>
			<div class="col-sm-8">' . $row['fuel_type'] . '</div>
		</div>';
	    }

	    if (strpos($value_config, ','."Tire Type".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Tire Tab:</label>
			<div class="col-sm-8">' . $row['tire_type'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Drive Train".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Drive Train:</label>
			<div class="col-sm-8">' . $row['drive_train'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Licence Plate".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Licence Plate:</label>
			<div class="col-sm-8">' . $row['licence_plate'] . '</div>
		</div>';
	    }

	    if (strpos($value_config, ','."Nickname".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Nickname:</label>
			<div class="col-sm-8">' . $row['nickname'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Year Purchased".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Year Purchased:</label>
			<div class="col-sm-8">' . $row['year_purchased'] . '</div>
		</div>';

	    }

	    if (strpos($value_config, ','."Mileage".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Mileage:</label>
			<div class="col-sm-8">' . $row['mileage'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Hours Operated".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Hours Operated:</label>
			<div class="col-sm-8">' . $row['hours_operated'] . '</div>
		</div>';
	    }

	    if (strpos($value_config, ','."Cost".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Cost:</label>
			<div class="col-sm-8">' . $row['cost'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."CDN Cost Per Unit".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">CDN Cost Per Unit:</label>
			<div class="col-sm-8">' . $row['cnd_cost_per_unit'] . '</div>
		</div>';
	    }

	    if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">USD Cost Per Unit:</label>
			<div class="col-sm-8">' . $row['usd_cost_per_unit'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Finance".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Finance:</label>
			<div class="col-sm-8">' . $row['finance'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Lease".',') !== FALSE) {

	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Lease:</label>
			<div class="col-sm-8">' . $row['lease'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Insurance".',') !== FALSE) {

	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Insurance:</label>
			<div class="col-sm-8">' . $row['insurance'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Hourly Rate:</label>
			<div class="col-sm-8">' . $row['hourly_rate'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Daily Rate".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Daily Rate:</label>
			<div class="col-sm-8">' . $row['daily_rate'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Semi-monthly Rate".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Semi-monthly Rate:</label>
			<div class="col-sm-8">' . $row['semi_monthly_rate'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Monthly Rate".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Monthly Rate:</label>
			<div class="col-sm-8">' . $row['monthly_rate'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Field Day Cost".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Field Day Cost:</label>
			<div class="col-sm-8">' . $row['field_day_cost'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Field Day Billable".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Field Day Billable:</label>
			<div class="col-sm-8">' . $row['field_day_billable'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."HR Rate Work".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">HR Rate Work:</label>
			<div class="col-sm-8">' . $row['hr_rate_work'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."HR Rate Travel".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">HR Rate Travel:</label>
			<div class="col-sm-8">' . $row['hr_rate_travel'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Billing Rate".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Billing Rate:</label>
			<div class="col-sm-8">$' . number_format($row['invoiced_amt'] / $row['invoiced_hours'],2) . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Billed Hours".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Total Billed Hours:</label>
			<div class="col-sm-8">' . round($row['invoiced_hours'],3) . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Billed Total".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Total Billed Amount:</label>
			<div class="col-sm-8">$' . number_format($row['invoiced_amt'],2) . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Expense Total".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Total Expenses:</label>
			<div class="col-sm-8">$' . number_format($row['expense_total'],2) . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Profit Total".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Total Profit:</label>
			<div class="col-sm-8">$' . number_format($row['invoiced_amt'] - $row['expense_total'],2) . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Next Service Date".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Next Service Date:</label>
			<div class="col-sm-8">' . $row['next_service_date'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Next Service Hours".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Next Service Hours:</label>
			<div class="col-sm-8">' . $row['next_service'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Next Service Description".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Next Service Description:</label>
			<div class="col-sm-8">' . $row['next_serv_desc'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Service Location".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Service Location:</label>
			<div class="col-sm-8">' . $row['service_location'] . '</div>
		</div>';

	    }

	    if (strpos($value_config, ','."Last Oil Filter Change (date)".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Last Oil Filter Change (date):</label>
			<div class="col-sm-8">' . $row['last_oil_filter_change_date'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Last Oil Filter Change (km)".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Last Oil Filter Change (km):</label>
			<div class="col-sm-8">' . $row['last_oil_filter_change'] . '</div>
		</div>';
	    }

	    if (strpos($value_config, ','."Next Oil Filter Change (date)".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Next Oil Filter Change (date):</label>
			<div class="col-sm-8">' . $row['next_oil_filter_change_date'] . '</div>
		</div>';
	    }

	    if (strpos($value_config, ','."Next Oil Filter Change (km)".',') !== FALSE) {

	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Next Oil Filter Change (km):</label>
			<div class="col-sm-8">' . $row['next_oil_filter_change'] . '</div>
		</div>';

	    }
	    if (strpos($value_config, ','."Last Inspection & Tune Up (date)".',') !== FALSE) {
	                echo '<div class="col-sm-6">
			<label class="col-sm-4">Last Inspection & Tune Up (date):</label>
			<div class="col-sm-8">' . $row['last_insp_tune_up_date'] . '</div>
		</div>';
	    }

	    if (strpos($value_config, ','."Last Inspection & Tune Up (km)".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Last Inspection & Tune Up (km):</label>
			<div class="col-sm-8">' . $row['last_insp_tune_up'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Next Inspection & Tune Up (date)".',') !== FALSE) {
	                echo '<div class="col-sm-6">
			<label class="col-sm-4">Next Inspection & Tune Up (date):</label>
			<div class="col-sm-8">' . $row['next_insp_tune_up_date'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Next Inspection & Tune Up (km)".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Next Inspection & Tune Up (km):</label>
			<div class="col-sm-8">' . $row['next_insp_tune_up'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Tire Condition".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Tire Condition:</label>
			<div class="col-sm-8">' . $row['tire_condition'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Last Tire Rotation (date)".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Last Tire Rotation (date):</label>
			<div class="col-sm-8">' . $row['last_tire_rotation_date'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Last Tire Rotation (km)".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Last Tire Rotation (km):</label>
			<div class="col-sm-8">' . $row['last_tire_rotation'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Next Tire Rotation (date)".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Next Tire Rotation (date):</label>
			<div class="col-sm-8">' . $row['next_tire_rotation_date'] . '</div>
		</div>';
		}
	    if (strpos($value_config, ','."Next Tire Rotation (km)".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Next Tire Rotation (km):</label>
			<div class="col-sm-8">' . $row['next_tire_rotation'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Registration Renewal date".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Registration Renewal Date:</label>
			<div class="col-sm-8"><a href="?edit='.$row['equipmentid'].'&target_field=reg_renewal_date">' . $row['reg_renewal_date'] . '</a></div>
		</div>';
	    }
	    if (strpos($value_config, ','."Insurance Renewal Date".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Insurance Renewal Date:</label>
			<div class="col-sm-8"><a href="?edit='.$row['equipmentid'].'&target_field=insurance_renewal">' . $row['insurance_renewal'] . '</a></div>
		</div>';
	    }
		if (strpos($value_config, ','."CVIP Ticket Renewal Date".',') !== FALSE) {
	         echo '<div class="col-sm-6">
			<label class="col-sm-4">CVIP Ticket Renewal Date:</label>
			<div class="col-sm-8">' . $row['cvip_renewal_date'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Region Dropdown".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Region:</label>
			<div class="col-sm-8">' . implode(', ',explode('*#*',$row['region'])) . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Location Dropdown".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Location:</label>
			<div class="col-sm-8">' . implode(', ',explode('*#*',$row['location'])) . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Classification Dropdown".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Classification:</label>
			<div class="col-sm-8">' . implode(', ',explode('*#*',$row['classification'])) . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Location".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Location:</label>
			<div class="col-sm-8">' . $row['location'] . '</div>
		</div>';
	    }

	    if (strpos($value_config, ','."LSD".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">LSD:</label>
			<div class="col-sm-8">' . $row['lsd'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Service History Link".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Service History:</label>
			<div class="col-sm-8"><a href="?edit='.$row['equipmentid'].'&subtab=service_schedules">View</a></div>
		</div>';
	    }
	    if (strpos($value_config, ','."Status".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Status:</label>
			<div class="col-sm-8">' . $row['status'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Ownership Status".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Ownership Status:</label>
			<div class="col-sm-8">' . $row['ownership_status'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Assigned Status".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Assigned Status:</label>
			<div class="col-sm-8">' . $row['assigned_status'] . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Quote Description:</label>
			<div class="col-sm-8">' . html_entity_decode($row['quote_description']) . '</div>
		</div>';
	    }
	    if (strpos($value_config, ','."Notes".',') !== FALSE) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Notes:</label>
			<div class="col-sm-8">' . $row['notes'] . '</div>
		</div>';
	    }

	    if(vuaed_visible_function($dbc, 'equipment') == 1) {
	    echo '<div class="col-sm-6">
			<label class="col-sm-4">Function:</label>
	        <div class="col-sm-8"><a href=\'?edit='.$row['equipmentid'].'\'>Edit</a> | '; echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&equipmentid='.$row['equipmentid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a></div>
	    </div>';
	    }
	    echo '<div class="clearfix"></div>';
	    echo '</div>';
	}
} else {
	echo '<h2>No Equipment Found.</h2>';
}

// Added Pagination //
if(isset($query)) {
	echo '<div class="pagination_links">';
    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    echo '</div>';
}
// Pagination Finish //
?>
<script>
function setManualFlag(tasklistid, colour, label) {
    window.location.reload();
}
function flag_item(checklist,eve) {
	checklist_id = checklist;
	$.ajax({
		method: "POST",
		url: "../Equipment/equipment_ajax.php?fill=equipmentflag",
		data: { id: checklist_id },
		complete: function(result) {
			console.log(result.responseText);
			$(eve).closest('div.dashboard-item').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
		}
	});
}

function favorite_item(checklist,eve) {
	checklist_id = checklist;
	$.ajax({
		method: "POST",
		url: "../Equipment/equipment_ajax.php?fill=equipmentfavorite",
		data: { id: checklist_id },
		complete: function(result) {
            window.location.reload();
		}
	});
}
</script>