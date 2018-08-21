<?php include_once('../include.php');
checkAuthorised('services');
if($serviceid > 0 && isset($_POST['ratecardid'])) {
	$rate_cards_keep = [];
	foreach($_POST['ratecardid'] as $i => $ratecardid) {
		$row_i = filter_var($_POST['ratecard_row_i'][$i]);

		$start_date = filter_var($_POST['start_date'][$i],FILTER_SANITIZE_STRING);
		$end_date = filter_var($_POST['end_date'][$i],FILTER_SANITIZE_STRING);
		$alert_date = filter_var($_POST['alert_date'][$i],FILTER_SANITIZE_STRING);
		$alert_staff = filter_var(implode(',',$_POST['alert_staff_'.$row_i]),FILTER_SANITIZE_STRING);
		$uom = filter_var($_POST['uom'][$i],FILTER_SANITIZE_STRING);
		if($uom == 'NEW_UOM') {
			$uom = filter_var($_POST['uom_new'][$i],FILTER_SANITIZE_STRING);
		}
		$cost = filter_var($_POST['cost'][$i],FILTER_SANITIZE_STRING);
		$profit_percent = filter_var($_POST['profit_percent'][$i],FILTER_SANITIZE_STRING);
		$profit_dollar = filter_var($_POST['profit_dollar'][$i],FILTER_SANITIZE_STRING);
		$price = filter_var($_POST['price'][$i],FILTER_SANITIZE_STRING);
		if($serviceid > 0 && $cost > 0) {
			$before_change = capture_before_change($dbc, 'services', 'cost', 'serviceid', $serviceid);
			$dbc->query("UPDATE `services` SET `cost`='$cost' WHERE `serviceid`='$serviceid'");
    	$history = capture_after_change('cost', $cost);
			add_update_history($dbc, 'service_history', $history, '', $before_change);
		}

		if(!empty($start_date.$end_date.$alert_date.$alert_staff.$uom.$cost.$profit_percent.$profit_dollar.$price)) {
			$history = 'Service rate card '.($ratecardid == '' ? 'Added' : 'Edited').' by '.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' on '.date('Y-m-d h:i:s');
			$sql = '';
			if($ratecardid == '') {
				$sql = "INSERT INTO `company_rate_card` (`item_id`,`start_date`,`end_date`,`uom`,`cost`,`margin`,`profit`,`cust_price`,`history`,`created_by`,`alert_date`,`alert_staff`) VALUES
					('$serviceid','$start_date','$end_date','$uom','$cost','$profit_percent','$profit_dollar','$price','$history','".$_SESSION['contactid']."','$alert_date','$alert_staff')";
				$result = mysqli_query($dbc, $sql);
				$ratecardid = mysqli_insert_id($dbc);
			}
			else {
				$sql = "UPDATE `company_rate_card` SET `item_id`='$serviceid',`start_date`='$start_date',`end_date`='$end_date',`uom`='$uom',`cost`='$cost',`margin`='$profit_percent',`profit`='$profit_dollar',`cust_price`='$price',`history`=IFNULL(CONCAT(`history`,'<br />\n','$history'),'$history'),`alert_date`='$alert_date',`alert_staff`='$alert_staff' WHERE `companyrcid`='$ratecardid'";
				$result = mysqli_query($dbc, $sql);
			}
			$rate_cards_keep[] = $ratecardid;
		}
	}
	$rate_cards_keep = "'".implode("','", $rate_cards_keep)."'";
	        $date_of_archival = date('Y-m-d');
mysqli_query($dbc, "UPDATE `company_rate_card` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `item_id` = '$serviceid' AND `tile_name` LIKE 'Services' AND `ratecardid` NOT IN ($rate_cards_keep)");
}
