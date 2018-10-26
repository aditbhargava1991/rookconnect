<?php
// Services Stat
$service_list = mysqli_query($dbc, "SELECT `item_id`, `fee`, `admin_fee`, COUNT(*) `total_appt` FROM `invoice_compensation` WHERE `contactid`='$therapistid' AND `service_date` BETWEEN '$stat_start' AND '$stat_end' AND `item_type`='services' GROUP BY `item_id`, `fee`, `admin_fee`");
$stat_base_service = 0;
$stat_performance_service = 0;
while($service_row = mysqli_fetch_array($service_list)) {
	// Base Pay
	$current_serviceid = $service_row['item_id'];
	$current_fee = $service_row['fee'];
	$current_admin = $service_row['admin_fee'];
	$current_final = $current_fee - $current_admin;
	$total_appt = $service_row['total_appt'];
	
	$current_comp = $base_pay[0]/100 * $current_final * $total_appt;
	$stat_base_service += $current_comp;
	
	// Performance Bonus
	if($current_serviceid != '42' && $current_serviceid != '43' && $current_serviceid != '45') {
		$sp1 = $sp2 = $sp3 = $sp4 = $sp5 = 0;
		if($arr_target <= $arr_actual_value) {
			$sp1 = ($final_fee*($arr_perc/100)) * $total_appt;
		}

		if($avg_hours_sch_target <= $avg_hours_sch_actual_value) {
			$sp2 = ($final_fee*($avg_hours_sch_perc/100)) * $total_appt;
		}


		if($test_actual_value != 0) {
			$sp3 = ($final_fee*($test_perc/100)) * $total_appt;
		}

		if($inter_actual_value != 0) {
			$sp4 = ($final_fee*($inter_perc/100)) * $total_appt;
		}

		if($adv_actual_value != 0) {
			$sp5 = ($final_fee*($adv_perc/100)) * $total_appt;
		}
		$stat_performance_service += $sp1 + $sp2 + $sp3 + $sp4 + $sp5;
	}
}
$grand_stat_total += $stat_base_service + $stat_performance_service;

// Inventory Stat
$comp_inv_list = mysqli_fetch_array(mysqli_query($dbc, "SELECT group_concat(`inventoryid` separator ',') as `inventoryids`, group_concat(`sell_price` separator ',') as `total_prices` FROM invoice WHERE therapistsid='$therapistid' AND (service_date >= '".$stat_start."' AND service_date <= '".$stat_end."') AND `invoice_type` IN ($invoicetype)"));
$stat_base_inventory = 0;
foreach(explode(',',$comp_inv_list['inventoryids']) as $key => $current_inventoryid) {
	$total_price = explode(',',$comp_inv_list['total_prices'])[$key];
	$stat_base_inventory += $base_pay[1] / 100 * $total_price;
}
$grand_stat_total += $stat_base_inventory;
$grand_stat_total += array_sum($invoice_summary_total);

// Total Stat
$total_active = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(DISTINCT((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')))) AS total_active FROM booking WHERE therapistsid = '$therapistid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$stat_start."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$stat_end."')"));
$days_worked = $total_active['total_active'];

$avg_per_day_stat += ($grand_stat_total/$days_worked);

//$report_validation1 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT group_concat(`serviceid` separator ',') as `all_serviceid`, group_concat(`inventoryid` separator ',') as `all_inventoryid`, group_concat(`sell_price` separator ',') as `all_sell_price` FROM invoice WHERE therapistsid='$therapistid' AND serviceid IS NOT NULL AND (service_date >= '".$stat_start."' AND service_date <= '".$stat_end."') AND `invoice_type` IN ($invoicetype)"));
//
//$serviceid1 = explode(',', $report_validation1['all_serviceid']);
//$serviceid1 = array_filter($serviceid1);
//if($report_validation1['all_serviceid'] != '') {
//    asort($serviceid1);
//    $occurences1 = array_count_values($serviceid1);
//
//    $total_base_service1 = 0;
//    foreach ($occurences1 as $key1 => $total_appt1) {
//        $final_serviceid1 = rtrim($key1,',');
//
//		$fee_level = mysqli_fetch_array(mysqli_query($dbc, ""));
//        $fee1 = get_all_from_service($dbc, $final_serviceid1, 'fee');
//        $admin_price1 = get_all_from_service($dbc, $final_serviceid1, 'admin_price');
//        $final_fee1 = ($fee1-$admin_price1);
//        $service_fee1 = ($final_fee1*$total_appt1);
//        $base_pay_perc1 = $base_pay[0];
//        $comp_pay1 = ($base_pay_perc1*0.01*$service_fee1);
//
//        $total_base_service1 += $comp_pay1;
//    }
//    $grand_stat_total += $total_base_service1;
//}


/// Metrix Stat

//$report_validation2 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT group_concat(`serviceid` separator ',') as `all_serviceid`, group_concat(`inventoryid` separator ',') as `all_inventoryid`, group_concat(`sell_price` separator ',') as `all_sell_price` FROM invoice WHERE therapistsid='$therapistid' AND serviceid IS NOT NULL AND (service_date >= '".$stat_start."' AND service_date <= '".$stat_end."') AND `invoice_type` IN ($invoicetype)"));
//
//$serviceid2 = explode(',', $report_validation2['all_serviceid']);
//$serviceid2 = array_filter($serviceid2);
//
//if($report_validation2['all_serviceid'] != '') {
//    asort($serviceid2);
//    $occurences = array_count_values($serviceid2);
//
//    $final_metrix1 = 0;
//    foreach ($occurences as $key => $total_appt) {
//        $final_serviceid = rtrim($key,',');
//
//        $fee = get_all_from_service($dbc, $final_serviceid, 'fee');
//        $admin_price = get_all_from_service($dbc, $final_serviceid, 'admin_price');
//        $final_fee = ($fee-$admin_price);
//
//        if($final_serviceid != '42' && $final_serviceid != '43' && $final_serviceid != '45') {
//            $after1 = 0;
//            $after2 = 0;
//            $after3 = 0;
//            $after4 = 0;
//            $after5 = 0;
//
//            $service_fee = ($final_fee*$total_appt);
//
//            $base_pay_perc = $base_pay[0];
//            $comp_pay = ($base_pay_perc*0.01*$service_fee);
//
//            $total_header = '';
//            if($arr_target <= $arr_actual_value) {
//                $after1 = ($final_fee*($arr_perc/100)) * $total_appt;
//            }
//
//            if($avg_hours_sch_target <= $avg_hours_sch_actual_value) {
//                $after2 = ($final_fee*($avg_hours_sch_perc/100)) * $total_appt;
//            }
//
//
//            if($test_actual_value != 0) {
//                $after3 = ($final_fee*($test_perc/100)) * $total_appt;
//            }
//
//            if($inter_actual_value != 0) {
//                $after4 = ($final_fee*($inter_perc/100)) * $total_appt;
//            }
//
//            if($adv_actual_value != 0) {
//                $after5 = ($final_fee*($adv_perc/100)) * $total_appt;
//            }
//
//            $metrix1 = ($after1+$after2+$after3+$after4+$after5);
//            $final_metrix1 += $metrix1;
//        }
//    }
//
//    $grand_stat_total += $final_metrix1;
//}

// Inventory
//if(str_replace(',', '', $comp_inv_list['inventoryids']) != '') {
//	$inventoryid2 = array_filter(explode(',',$comp_inv_list['inventoryids']));
//	$sell_price2 = array_filter(explode(',',$comp_inv_list['total_prices']));
//    asort($inventoryid2);
//    $sorted_arr2 = [];
//    foreach($inventoryid2 as $key2=>$val2) {
//      array_push($sorted_arr2, $sell_price2[$key2]);
//    }
//    $combined2 = combineStringArrayWithDuplicates($inventoryid2, $sorted_arr2);
//
//    $total_base_inv2 = 0;
//    foreach ($combined2 as $key2 => $sell_price2) {
//        $inv_pay2 = ($base_pay[1]/100)*$sell_price2;
//        $total_base_inv2 += $inv_pay2;
//    }
//    $grand_stat_total += $total_base_inv2;
//}
//
//$total_active = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(DISTINCT((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')))) AS total_active FROM booking WHERE therapistsid = '$therapistid' AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND ((str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) >= '".$stat_start."' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) <= '".$stat_end."')"));
//$days_worked = $total_active['total_active'];
//
//$avg_per_day_stat += ($grand_stat_total/$days_worked);
//