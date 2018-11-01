<?php
error_reporting(0);
include ('../include.php');
ob_clean();

$from = $_GET['from'];
$name = $_GET['name'];
if($from == 'job_contact') {
	$query = mysqli_query($dbc,"SELECT CONCAT('customer*',ratecardid) id, rate_card_name FROM rate_card WHERE deleted=0 AND on_off=1 AND clientid = '$name' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION
						SELECT CONCAT('company*',MIN(`companyrcid`)) id, `rate_card_name` FROM `company_rate_card` WHERE DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `rate_card_name` HAVING MIN(`deleted`)=0 UNION
						SELECT 'position*' id, 'Rate Card by Position' rate_card_name FROM `position_rate_table` WHERE `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `deleted` UNION
						SELECT 'staff*' id, 'Rate Card by Staff' rate_card_name FROM `staff_rate_table` WHERE `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `deleted` UNION
						SELECT 'equipment*' id, 'Rate Card by Equipment' rate_card_name FROM `equipment_rate_table` WHERE `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `deleted` UNION
						SELECT 'category*' id, 'Rate Card by Equipment Category' rate_card_name FROM `category_rate_table` WHERE `deleted`=0 GROUP BY `deleted` AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
    echo '<option value=""></option>';
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='". $row['ratecardid']."'>".$row['rate_card_name'].'</option>';
    }

    echo '*FFM*';

    $query = mysqli_query($dbc,"SELECT siteid, site_name FROM field_sites WHERE clientid = '$name'");
    echo '<option value=""></option>';
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='". $row['siteid']."'>".$row['site_name'].'</option>';
    }
}
if($from == 'field_jobs_wt') {
    $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT sell_price FROM inventory WHERE inventoryid='$name'"));
    echo $result['sell_price'];
}
if($from == 'jobdate') {
    $job_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(jobid) AS total_job FROM field_jobs WHERE date(job_date) = '$name'"));
    $date_job = date("y-m-d", strtotime($name));
    $job_number = $date_job.'-'.($job_result['total_job']+1);
    echo $job_number;
}
if($from == 'field_jobs_wt') {
    $action = $_GET['action'];
    $workticketid = $_GET['id'];
    $value = $_GET['value'];

    if($action == 'actiondate') {
				$before_change = capture_before_change($dbc, 'field_work_ticket', 'date_sent', 'workticketid', $workticketid);

        $query_update_es = "UPDATE `field_work_ticket` SET `date_sent` = '$value' WHERE `workticketid` = '$workticketid'";
        $result_update_es = mysqli_query($dbc, $query_update_es);

				$history = capture_after_change('date_sent', $value);
				add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
    }
}
if($from == 'job') {
	$query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name, name FROM contacts WHERE businessid = '$name'"));
	echo "<option value=\"\"></option>\n";
	foreach($query as $row) {
		echo '<option value="'.$row['contactid'].'">'.$row['name'].' '.$row['first_name'].' '.$row['last_name']."</option>\n";
	}
}

if($from == 'site_job') {
	$query = mysqli_query($dbc,"SELECT siteid, site_name FROM field_sites WHERE clientid = '$name'");
	echo '<option value=""></option>';
	echo '<option value="NEW SITE">New Site Location</option>';
	while($row = mysqli_fetch_array($query)) {
		echo "<option value='". $row['siteid']."'>".$row['site_name'].'</option>';
	}
}

if($from == 'site_rc') {
	$query = mysqli_query($dbc,"SELECT CONCAT('customer*',ratecardid) id, rate_card_name FROM rate_card WHERE deleted=0 AND on_off=1 AND clientid = '$name' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION
						SELECT CONCAT('company*',MIN(`companyrcid`)) id, `rate_card_name` FROM `company_rate_card` WHERE DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `rate_card_name` HAVING MIN(`deleted`)=0 UNION
						SELECT 'position*' id, 'Rate Card by Position' rate_card_name FROM `position_rate_table` WHERE `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `deleted` UNION
						SELECT 'staff*' id, 'Rate Card by Staff' rate_card_name FROM `staff_rate_table` WHERE `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `deleted` UNION
						SELECT 'equipment*' id, 'Rate Card by Equipment' rate_card_name FROM `equipment_rate_table` WHERE `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `deleted` UNION
						SELECT 'category*' id, 'Rate Card by Equipment Category' rate_card_name FROM `category_rate_table` WHERE `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `deleted`");
    echo '<option value=""></option>';
    while($row = mysqli_fetch_array($query)) {
        echo "<option value='". $row['id']."'>".$row['rate_card_name'].'</option>';
    }
}

if($from == 'field_job_fs') {
	if(isset($_GET['equid'])) {
		$equid = $_GET['equid'];
		$equ_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT category, vin_number, unit_number, type FROM equipment WHERE equipmentid = '$equid'"));
		$category = $equ_result['category'];
		$type = $equ_result['type'];

		$job_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `ratecardid` FROM `field_jobs` WHERE `jobid`='$name'"));
		if($job_result['ratecardid'] == '') {
			exit('NO RATE CARD');
		}
		else if(strpos($job_result['ratecardid'],'*') === FALSE) {
			$rate_card = explode('*','customer*'.$job_result['ratecardid']);
		}
		else {
			$rate_card = explode('*',$job_result['ratecardid']);
		}
		$rate_type = $rate_card[0];
		$rate_id = $rate_card[1];
		$query = "";

		if($rate_type == 'company') {
			$query = "SELECT `daily`, `hourly` FROM `company_rate_card` WHERE (`description`='$category' OR `description`='$type' OR `description`='$equid') AND
				`rate_card_name` IN (SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='$rate_id') AND `deleted`=0";
		}
		else if($rate_type == 'customer') {
			$query = "";
		}
		else if($rate_type == 'equipment') {
			$query = "SELECT `daily`, `hourly` FROM `equipment_rate_table` WHERE `equipment_id`='$equid' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
		}
		else if($rate_type == 'category') {
			$query = "SELECT daily, hourly FROM category_rate_table WHERE category='$category' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')";
		}

		$rate_card = mysqli_fetch_assoc(mysqli_query($dbc, $query));
		echo $rate_card['daily'].'*'.$rate_card['hourly'].'*'.$rate_type.'*'.$rate_id;
	}
	else if(isset($_GET['jobid'])) {
		$jobid = $_GET['jobid'];
		echo "Result:";
		$job = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM field_jobs WHERE jobid='$jobid'"));
		echo $jobid.'*'.$job['contactid'].'*'.$job['afe_number'].'*'.$job['siteid'].'*'.$job['additional_info'];
	}
}
if($_GET['action'] == 'hand_deliver' && $_GET['workticketid'] > 0) {
	mysqli_query($dbc, "UPDATE `field_work_ticket` SET date_sent = CONCAT_WS('<br>',date_sent, '".date('Y-m-d')."|hand') WHERE `workticketid` = '".$_GET['workticketid']."'");
	$before_change = '';
  $history = "field_work_ticket entry has been updated for fsid -> ".$_GET['workticketid']." <br />";
  add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
}
?>
