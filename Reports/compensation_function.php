
<?php
function report_compensation($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $therapist, $stat_holidays, $invoicetype = "'New','Refund','Adjustment'", $equipment) {
	$report_fields = explode(',', get_config($dbc, 'report_compensation_fields'));
    $report_data = '';

    if($equipment > 0) {
        $equipment = [$equipment];
        $result = [];
    } else if($therapist == '') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`=1 AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else {
		$result = [ $therapist ];
        $business = get_contact($dbc, $therapist, 'businessid');
        $equipment = [];
        $equip_list = $dbc->query("SELECT `equipmentid` FROM `equipment` WHERE `deleted`=0 AND `ownership_status` IN ('".implode("','",array_filter([$therapist,$business]))."')");
        while($equip_row = $equip_list->fetch_assoc()) {
            $equipment[] = $equip_row['equipmentid'];
        }
    }

    $all_booking = 0;
    $grand_total = 0;
    $grand_stat_total = 0;
    $avg_per_day_stat = 0;
	foreach($result as $rowid) {
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts`.contactid, `contacts`.scheduled_hours, `contacts`.`schedule_days`, `contacts`.category_contact, IFNULL(`base_pay`,'0*#*0') base_pay FROM contacts LEFT JOIN `compensation` ON `contacts`.`contactid`=`compensation`.`contactid` AND '$starttime' BETWEEN `compensation`.`start_date` AND `compensation`.`end_date` WHERE `contacts`.contactid='$rowid'"));
        $therapistid = $row['contactid'];
        $category_contact = $row['category_contact'];
		$schedule = $row['schedule_days'];
        $base_pay = explode('*#*',$row['base_pay']);

        $comp_entered = false;
        $invoice_summary_total = [];
        include_once('report_compensation_invoice.php');
        
        if(!$comp_entered) {
            include_once ('report_compensation_services.php');
            include_once ('report_compensation_preformance_logic.php');
            include_once ('report_compensation_inventory.php');
        }

		foreach(explode(',',$stat_holidays) as $stat_day) {
			if($stat_day >= $starttime && $stat_day <= $endtime) {
				$stat_day = strtotime($stat_day);
				$weekday = date('w',$stat_day);
				if($schedule == '' || in_array(date('w',$stat_day), explode(',',$schedule))) {
					$stat_start = date('Y-m-d',strtotime('-63 day',$stat_day));
					$stat_end = date('Y-m-d',strtotime('-1 day',$stat_day));
					include('report_compensation_stat_holiday.php');
				}
			}
		}
        //if($total_stat_holiday != 0) {
        //    include_once ('report_compensation_stat_holiday.php');
        //} else {
        //    $avg_per_day_stat = '0.00';
        //}

        include_once ('report_compensation_summary.php');

        //include_once ('report_compensation_preformance.php');
    }
    
    // Get Equipment Compensation
    foreach($equipment as $equipmentid) {
        $equip = $dbc->query("SELECT * FROM `equipment` WHERE `equipmentid`='$equipmentid'")->fetch_assoc();
        // error_reporting(E_ALL);
        include('report_compensation_equipment.php');
    }

    return $report_data;
}

function report_appt_compensation($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $therapist, $stat_holidays, $invoicetype = "'New','Refund','Adjustment'") {
    $report_data = '';

    if($therapist == '') {
		$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, `first_name`, `last_name` FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`=1 AND deleted=0 AND status=1"),MYSQLI_ASSOC));
    } else {
		$result = [ $therapist ];
    }

    $all_booking = 0;
    $grand_total = 0;
    $grand_stat_total = 0;
    $avg_per_day_stat = 0;
	foreach($result as $rowid) {
		$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts`.contactid, `contacts`.scheduled_hours, `contacts`.`schedule_days`, `contacts`.category_contact, IFNULL(`base_pay`,'0*#*0') base_pay FROM contacts LEFT JOIN `compensation` ON `contacts`.`contactid`=`compensation`.`contactid` AND '$starttime' BETWEEN `compensation`.`start_date` AND `compensation`.`end_date` WHERE `contacts`.contactid='$rowid'"));
        $therapistid = $row['contactid'];
        $category_contact = $row['category_contact'];
		$schedule = $row['schedule_days'];
        $base_pay = explode('*#*',$row['base_pay']);

        $comp_entered = false;
        $invoice_summary_total = [];
        include_once('report_compensation_invoice.php');
        
        if(!$comp_entered) {
            include_once ('report_compensation_services.php');
            include_once ('report_compensation_preformance_logic.php');
            include_once ('report_compensation_inventory.php');
        }

		foreach(explode(',',$stat_holidays) as $stat_day) {$report_data .= "Stat Dates: $stat_day";
			if($stat_day >= $starttime && $stat_day <= $endtime) {
				$stat_day = strtotime($stat_day);
				$weekday = date('w',$stat_day);
				if($schedule == '' || in_array(date('w',$stat_day), explode(',',$schedule))) {
					$stat_start = date('Y-m-d',strtotime('-63 day',$stat_day));
					$stat_end = date('Y-m-d',strtotime('-1 day',$stat_day));
					include('report_compensation_stat_holiday.php');
				}
			}
		}
        //if($total_stat_holiday != 0) {
        //    include_once ('report_compensation_stat_holiday.php');
        //} else {
        //    $avg_per_day_stat = '0.00';
        //}

        include_once ('report_compensation_summary.php');

        //include_once ('report_compensation_preformance.php');
    }

    return $report_data;
}

function combineStringArrayWithDuplicates ($keys, $values) {
    $total_array = sizeof($keys);
    $iter = 0;
    $key_old = 0;
    $fee = 0;
    $m = 0;
    foreach ($keys as $key) {
        if($iter == 0) {
            $fee += $values[$iter];
            $key_old = $key;

        } else if($key != $key_old && $iter != 0) {
            $combined[$key_old.':'.$m] = $fee;
            $m = 0;
            $fee = 0;
            $key_old = $key;
            $fee += $values[$iter];
        } else {
            $fee += $values[$iter];
        }
        $m++;
        $iter++;
    }
    if($iter == $total_array) {
        $combined[$key_old.':'.$m] = $fee;
    }
    return $combined;
}

function combineArrayDuplicates ($ids, $prices, $patients, $qtys) {
    $combined = [];
    foreach ($ids as $iter => $key) {
        $combined[$key]['sell_price'] += $prices[$iter];
		$combined[$key]['qty'] += $qtys[$iter];
		$combined[$key]['patientids'][] = $patients[$iter];
    }
    return $combined;
}

function array_combine_($keys, $values)
{
    $result = array();
    foreach ($keys as $i => $k) {
        $result[$k][] = $values[$i];
    }
    array_walk($result, create_function('&$v', '$v = (count($v) == 1)? array_pop($v): $v;'));
    return    $result;
}
?>