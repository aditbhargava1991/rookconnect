<?php include('../include.php');
require_once('../phpsign/signature-to-image.php');
error_reporting(0);
checkAuthorised();
ob_clean();

if($_GET['action'] == 'add_signature') {
	$contactid = $_POST['contactid'];
	$date = $_POST['date'];
	$signature = $_POST['signature'];

    if (!file_exists('../Timesheet/download')) {
        mkdir('../Timesheet/download', 0777, true);
    }

    $img = sigJsonToImage($signature);
    $file_name = $contactid.'_'.$date.'_sig.png';
    imagepng($img, '../Timesheet/download/'.$file_name);

    mysqli_query($dbc, "INSERT INTO `time_cards_signature` (`contactid`, `date`, `signature`) VALUES ('$contactid', '$date', '$file_name')");
    echo $file_name;
}
else if($_GET['action'] == 'type_time') {
	$name = explode('_',filter_var($_POST['field'],FILTER_SANITIZE_STRING));
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	$site = filter_var($_POST['site_id'],FILTER_SANITIZE_STRING);
	$staff = filter_var($_POST['staff_id'],FILTER_SANITIZE_STRING);
	$type = '';
	switch($name[0]) {
		case 'regular': $type = 'Regular Hrs.'; break;
		case 'training': $type = 'Regular Hrs.'; break;
		case 'drive': $type = 'Regular Hrs.'; break;
		case 'direct': $type = 'Direct Hrs.'; break;
		case 'indirect': $type = 'Indirect Hrs.'; break;
		case 'extra': $type = 'Extra Hrs.'; break;
		case 'relief': $type = 'Relief Hrs.'; break;
		case 'sleep': $type = 'Sleep Hrs.'; break;
		case 'sickadj': $type = 'Sick Time Adj.'; break;
		case 'sick': $type = 'Sick Hrs.Taken'; break;
		case 'statavail': $type = 'Stat Hrs.'; break;
		case 'stat': $type = 'Stat Hrs.Taken'; break;
		case 'vacavail': $type = 'Vac Hrs.'; break;
		case 'vaca': $type = 'Vac Hrs.Taken'; break;
		default: continue;
	}
	$comment = filter_var($_POST['comment'],FILTER_SANITIZE_STRING);
	$deleted = $_POST['deleted'] > 0 ? 1 : 0;
	if($type != '') {
		$layout = get_config($dbc, 'timesheet_layout');
		$id_search = '';
		$id = $_POST['id'];
		if ($id > 0) {
			$id_search = "AND `time_cards_id`='$id'";
		}
		$value = number_format ( time_time2decimal($value), 3 );
		$date = str_replace(['[',']'],'',$name[1].'-'.$name[2].'-'.$name[3]);
		if($id > 0 && $layout == 'multi_line') {
			$sql = "UPDATE `time_cards` SET `total_hrs`='$value', `comment_box`=CONCAT(IFNULL(CONCAT(`comment_box`,IF('$comment'!='' AND `comment_box`!='','&lt;br /&gt;','')),''),'$comment'), `deleted`='$deleted' WHERE `time_cards_id` = '$id'";
			$result = mysqli_query($dbc, $sql);

			$value_config = explode(',',get_field_config($dbc, 'time_cards'));
			if(in_array('update_ticket_payable',$value_config) && !empty($_POST['ticket_attached_id'])) {
				$total_hrs = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$id'"))['total_hrs'];
				mysqli_query($dbc, "UPDATE `ticket_attached` SET `hours_set` = '$total_hrs' WHERE `id` = '{$_POST['ticket_attached_id']}'");
			}
		} else {
			$sql = "INSERT INTO `time_cards` (`date`, `type_of_time`, `business`, `staff`, `projectid`, `highlight`) SELECT '$date','$type','$site','$staff','$projectid',1 FROM
				(SELECT COUNT(*) num_rows FROM `time_cards` WHERE ".($id > 0 ? "`time_cards_id` = '$id'" : "'`date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0").") rows WHERE rows.num_rows = 0";
			$result = mysqli_query($dbc, $sql);
			$insert_id = mysqli_insert_id($dbc);
			if($insert_id > 0) {
				$id_search = "AND `time_cards_id`='$insert_id'";
			}
			$prev_hrs = $dbc->query("SELECT SUM(`total_hrs`) `hrs`, MAX(`time_cards_id`) `id` FROM `time_cards` WHERE `deleted`=0 AND `staff`='$staff' AND `type_of_time`='$type' AND `date`='$date' AND IFNULL(`business`,'')='$site'".($id_search != '' ? " AND `time_cards_id` NOT IN (SELECT `time_cards_id` FROM `time_cards` WHERE `deleted`=0 $id_search)" : ''))->fetch_assoc();
			$value -= $prev_hrs['hrs'];
			if($prev_hrs['id'] > 0) {
				$id_search = "AND `time_cards_id`='".$prev_hrs['id']."'";
			}
			$sql = "UPDATE `time_cards` SET `total_hrs`=IFNULL(`total_hrs`,0)+($value), `comment_box`=CONCAT(IFNULL(CONCAT(`comment_box`,IF('$comment'!='' AND `comment_box`!='','&lt;br /&gt;','')),''),'$comment'), `deleted`='$deleted' WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0 $id_search";
			$result = mysqli_query($dbc, $sql);
		}
		$sql = "SELECT `time_cards_id` FROM `time_cards` WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0 $id_search";
		$ids[] = mysqli_fetch_array(mysqli_query($dbc, $sql))['time_cards_id'];
	}
}
else if($_GET['action'] == 'task_time') {
	$session_user = get_contact($dbc, $_SESSION['contactid']);
	$timesheet_record_history = get_config($dbc, 'timesheet_record_history');

	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$id_vac = filter_var($_POST['id_vac'],FILTER_SANITIZE_STRING);
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$date_editable = filter_var($_POST['date_editable'],FILTER_SANITIZE_STRING);
	$start_time = filter_var($_POST['start_time'],FILTER_SANITIZE_STRING);
	$end_time = filter_var($_POST['end_time'],FILTER_SANITIZE_STRING);
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$type_of_time = filter_var($_POST['type_of_time'],FILTER_SANITIZE_STRING);
	if(strpos($_POST['total_hrs'],':') !== FALSE) {
		$_POST['total_hrs'] = explode(':',$_POST['total_hrs']);
		$_POST['total_hrs'] = $_POST['total_hrs'][0] + ($_POST['total_hrs'][1] / 60);
	}
	if(strpos($_POST['total_hrs_vac'],':') !== FALSE) {
		$_POST['total_hrs_vac'] = explode(':',$_POST['total_hrs_vac']);
		$_POST['total_hrs_vac'] = $_POST['total_hrs_vac'][0] + ($_POST['total_hrs_vac'][1] / 60);
	}
	$total_hrs = filter_var($_POST['total_hrs'],FILTER_SANITIZE_STRING);
	$total_hrs_vac = filter_var($_POST['total_hrs_vac'],FILTER_SANITIZE_STRING);
	$comment_box = filter_var($_POST['comment_box'],FILTER_SANITIZE_STRING);

	$business = filter_var($_POST['site_id'],FILTER_SANITIZE_STRING);
	$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
	$ids = '';
	$comment_history = '';
	if($id > 0) {
		$ids .= $id;
		$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$id'"));
		mysqli_query($dbc,"UPDATE `time_cards` SET `total_hrs`='$total_hrs', `type_of_time`='$type_of_time', `comment_box`='$comment_box', `start_time` = '$start_time', `end_time` = '$end_time' WHERE `time_cards_id`='$id'");
		if(number_format($total_hrs,1) != number_format($time_card['total_hrs'],1)) {
			$comment_history[$id] .= $session_user.' updated Hours from '.$time_card['total_hrs'].' to '.$total_hrs.'.<br>';
		}
		if($type_of_time != $time_card['type_of_time']) {
			$comment_history[$id] .= $session_user.' updated Type of Time from '.$time_card['type_of_time'].' to '.$type_of_time.'.<br>';
		}
		if(strtotime($start_time) != strtotime($time_card['start_time'])) {
			$comment_history[$id] .= $session_user.' updated Start Time from '.$time_card['start_time'].' to '.$start_time.'.<br>';
		}
		if(strtotime($end_time) != strtotime($time_card['end_time'])) {
			$comment_history[$id] .= $session_user.' updated End Time from '.$time_card['end_time'].' to '.$end_time.'.<br>';
		}
		if(!empty($date_editable) && $date != $date_editable) {
			mysqli_query($dbc, "UPDATE `time_cards` SET `date` = '$date_editable' WHERE `time_cards_id` = '$id'");
		}
	} else if($total_hrs > 0) {
		mysqli_query($dbc,"INSERT INTO `time_cards` (`date`, `staff`, `type_of_time`, `total_hrs`, `comment_box`, `projectid`, `business`, `start_time`, `end_time`) VALUES('$date', '$staff', '$type_of_time', '$total_hrs', '$comment_box', '$business', '$projectid', '$start_time', '$end_time')");
		$ids .= mysqli_insert_id($dbc);
		$comment_history[mysqli_insert_id($dbc)] .= $session_user.' inserted new row with Hours'.(!empty($type_of_time) ? ' for '.$type_of_time : '').'.<br>';
	}
	$ids .= ',';
	if($id_vac > 0) {
		$ids .= $id_vac;
		$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$id_vac'"));
		mysqli_query($dbc,"UPDATE `time_cards` SET `total_hrs`='$total_hrs_vac', `type_of_time`='Vac Hrs.', `comment_box`='$comment_box' WHERE `time_cards_id`='$id_vac'");
		if(number_format($total_hrs_vac,1) != number_format($time_card['total_hrs'],1)) {
			$comment_history[$id_vac] .= $session_user.' updated Vacation Hours from '.$time_card['total_hrs'].' to '.$total_hrs_vac.'.<br>';
		}
		if(!empty($date_editable) && $date != $date_editable) {
			mysqli_query($dbc, "UPDATE `time_cards` SET `date` = '$date_editable' WHERE `time_cards_id` = '$id_vac'");
		}
	} else if($total_hrs_vac > 0) {
		mysqli_query($dbc,"INSERT INTO `time_cards` (`date`, `staff`, `type_of_time`, `total_hrs`, `comment_box`) VALUES('$date', '$staff', 'Vac Hrs.', '$total_hrs_vac', '$comment_box')");
		$ids .= mysqli_insert_id($dbc);
		$comment_history[mysqli_insert_id($dbc)] .= $session_user.' inserted new row with Vacation Hours.<br>';
	}
	echo $ids;
	foreach(explode(',',$ids) as $id) {
		if($id > 0 && !empty($date_editable) && $date != $date_editable) {
			mysqli_query($dbc, "UPDATE `time_cards` SET `date` = '$date_editable' WHERE `time_cards_id` = '$id'");
		}
	}
	if($_POST['ticketid'] > 0) {
		$ticketid = $_POST['ticketid'];
		foreach(explode(',', $ids) as $id) {
			if($id > 0) {
				$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$id'"));
				if((!empty($ticketid) ? $ticketid : 0) != $time_card['ticketid']) {
					$comment_history[$id] .= $session_user.' updated '.TICKET_NOUN.' from '.get_ticket_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$time_card['ticketid']."'"))).' to '.get_ticket_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"))).'.<br>';
				}
				mysqli_query($dbc, "UPDATE `time_cards` SET `ticketid` = '$ticketid' WHERE `time_cards_id` = '$id'");
			}
		}
		$ticket_attached = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `src_table` = 'Staff_Tasks' AND `position` = '$type_of_time' AND `src_id` = '$staff' AND `deleted` = 0"));
		if(empty($ticket_attached) && !empty($type_of_time)) {
			mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `src_table`, `src_id`, `position`, `date_stamp`, `hours_set`) VALUES ('$ticketid', 'Staff_tasks', '$type_of_time', '$date', '$total_hrs')");
		}
	}
	if($timesheet_record_history == 1) {
		foreach($comment_history as $id => $comment_hist) {
			$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$id'"));
			$comment_hist = rtrim($comment_hist,'<br>');
			if(!empty($time_card['comment_box'])) {
				$comment_hist = '<br>'.$comment_hist;
			}
			mysqli_query($dbc, "UPDATE `time_cards` SET `comment_box` = '".$time_card['comment_box'].htmlentities($comment_hist)."' WHERE `time_cards_id` = '$id'");
			echo "UPDATE `time_cards` SET `comment_box` = '".$time_card['comment_box'].htmlentities($comment_hist)."' WHERE `time_cards_id` = '$id'";
		}
	}
}
else if($_GET['action'] == 'rate_time') {
	$staff = $_POST['staff_id'];
	$site = $_POST['site_id'];
	$projectid = $_POST['projectid'];
	$hours = $_POST['hours'];
	$type = $_POST['hours_type'];
	$date = $_POST['hours_date'];
	$day_type = $_POST['day_type'];
	$day_date = $_POST['day_date'];
	$days = $_POST['day_checkbox'] > 0 ? 1 : 0;
	$location = filter_var($_POST['location'],FILTER_SANITIZE_STRING);
	$customer = filter_var($_POST['customer'],FILTER_SANITIZE_STRING);
	$comment = filter_var($_POST['cat_comment'],FILTER_SANITIZE_STRING);
	if($type != '' && $hours > 0) {
		$sql = "INSERT INTO `time_cards` (`date`, `type_of_time`, `business`, `staff`, `location`, `customer`, `projectid`, `highlight`) SELECT '$date','$type','$site','$staff','$location','$customer','$projectid',1 FROM
			(SELECT COUNT(*) num_rows FROM `time_cards` WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0) rows WHERE rows.num_rows = 0";
		$result = mysqli_query($dbc, $sql);
		$sql = "UPDATE `time_cards` SET `total_hrs`='$hours', `location`='$location', `customer`='$customer', `day`='', `projectid`='$projectid' WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0";
		$result = mysqli_query($dbc, $sql);
		$sql = "UPDATE `time_cards` SET `comment_box`='$comment' WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND '$comment'!='' AND `deleted`=0";
		$result = mysqli_query($dbc, $sql);
		$sql = "SELECT `time_cards_id` FROM `time_cards` WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0";
	} else if($day_type != '' && $days > 0) {
		$sql = "INSERT INTO `time_cards` (`date`, `type_of_time`, `business`, `staff`, `location`, `customer`, `projectid`, `highlight`) SELECT '$day_date','$day_type','$site','$staff','$location','$customer','$projectid',1 FROM
			(SELECT COUNT(*) num_rows FROM `time_cards` WHERE `date`='$day_date' AND `staff`='$staff' AND `type_of_time`='$day_type' AND `business`='$site' AND `deleted`=0) rows WHERE rows.num_rows = 0";
		$result = mysqli_query($dbc, $sql);
		$sql = "UPDATE `time_cards` SET `total_hrs`='', `location`='$location', `customer`='$customer', `day`='$days', `projectid`='$projectid' WHERE `date`='$day_date' AND `staff`='$staff' AND `type_of_time`='$day_type' AND `business`='$site' AND `deleted`=0";
		$result = mysqli_query($dbc, $sql);
		$sql = "UPDATE `time_cards` SET `comment_box`='$comment' WHERE `date`='$day_date' AND `staff`='$staff' AND `type_of_time`='$day_type' AND `business`='$site' AND '$comment'!='' AND `deleted`=0";
		$result = mysqli_query($dbc, $sql);
		$sql = "SELECT `time_cards_id` FROM `time_cards` WHERE `date`='$day_date' AND `staff`='$staff' AND `type_of_time`='$day_type' AND `business`='$site' AND `deleted`=0";
	}
}
else if($_GET['action'] == 'position_time') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$id_vac = filter_var($_POST['id_vac'],FILTER_SANITIZE_STRING);
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$type_of_time = filter_var($_POST['type_of_time'],FILTER_SANITIZE_STRING);
	if(strpos($_POST['total_hrs'],':') !== FALSE) {
		$_POST['total_hrs'] = explode(':',$_POST['total_hrs']);
		$_POST['total_hrs'] = $_POST['total_hrs'][0] + ($_POST['total_hrs'][1] / 60);
	}
	if(strpos($_POST['total_hrs_vac'],':') !== FALSE) {
		$_POST['total_hrs_vac'] = explode(':',$_POST['total_hrs_vac']);
		$_POST['total_hrs_vac'] = $_POST['total_hrs_vac'][0] + ($_POST['total_hrs_vac'][1] / 60);
	}
	$total_hrs = filter_var($_POST['total_hrs'],FILTER_SANITIZE_STRING);
	$total_hrs_vac = filter_var($_POST['total_hrs_vac'],FILTER_SANITIZE_STRING);
	$comment_box = filter_var($_POST['comment_box'],FILTER_SANITIZE_STRING);

	$business = filter_var($_POST['site_id'],FILTER_SANITIZE_STRING);
	$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);

	$ids = '';
	if($id > 0) {
		mysqli_query($dbc,"UPDATE `time_cards` SET `total_hrs`='$total_hrs', `type_of_time`='$type_of_time', `comment_box`='$comment_box' WHERE `time_cards_id`='$id'");
	} else if($total_hrs > 0) {
		mysqli_query($dbc,"INSERT INTO `time_cards` (`date`, `staff`, `type_of_time`, `total_hrs`, `comment_box`, `projectid`, `business`) VALUES('$date', '$staff', '$type_of_time', '$total_hrs', '$comment_box', '$business', '$projectid')");
		$ids .= mysqli_insert_id($dbc);
	}
	$ids .= ',';
	if($id_vac > 0) {
		mysqli_query($dbc,"UPDATE `time_cards` SET `total_hrs`='$total_hrs_vac', `type_of_time`='Vac Hrs.', `comment_box`='$comment_box' WHERE `time_cards_id`='$id_vac'");
	} else if($total_hrs_vac > 0) {
		mysqli_query($dbc,"INSERT INTO `time_cards` (`date`, `staff`, `type_of_time`, `total_hrs`, `comment_box`, `projectid`, `business`) VALUES('$date', '$staff', 'Vac Hrs.', '$total_hrs_vac', '$comment_box', '$business', '$projectid')");
		$ids .= mysqli_insert_id($dbc);
	}
	echo $ids;
}
else if($_GET['action'] == 'unapprove_time') {
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
	$type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);

	if($type == 'day') {
		mysqli_query($dbc, "UPDATE `time_cards` SET `approv` = 'N', `manager_name` = NULL, `date_manager` = NULL, `coordinator_name` = NULL, `date_coordinator` = NULL, `manager_approvals` = '', `coord_approvals` = '' WHERE `staff` = '$staff' AND `deleted` = 0 ANd `date` = '$date' AND `approv` = 'Y'");
		echo "UPDATE `time_cards` SET `approv` = 'N', `manager_name` = NULL, `date_manager` = NULL, `coordinator_name` = NULL, `date_coordinator` = NULL WHERE `staff` = '$staff' AND `deleted` = 0 ANd `date` = '$date' AND `approv` = 'Y'";
	} else if($type == 'id') {
		mysqli_query($dbc, "UPDATE `time_cards` SET `approv` = 'N', `manager_name` = NULL, `date_manager` = NULL, `coordinator_name` = NULL, `date_coordinator` = NULL, `manager_approvals` = '', `coord_approvals` = '' WHERE `time_cards_id` = '$id'");
	}
}
else if($_GET['action'] == 'stop_holiday_update_noti') {
	set_config($dbc, 'holiday_update_stopdate', date('Y-m-d'));
} else if($_GET['action'] == 'update_time') {
	$timesheet_record_history = get_config($dbc, 'timesheet_record_history');
	$layout = get_config($dbc, 'timesheet_layout');
    $type_of_time = filter_var($_POST['type_of_time'],FILTER_SANITIZE_STRING);
    $field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
    $value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
    $history_hours = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
    if($field == 'total_hrs') {
        $value = explode(':',$value);
        $value = $value[0] + ($value[1] / 60);
    }
    $id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
    $date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
    $staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
    $siteid = filter_var($_POST['siteid'],FILTER_SANITIZE_STRING);
    $projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
    $clientid = filter_var($_POST['clientid'],FILTER_SANITIZE_STRING);
    $ticketid = filter_var($_POST['ticketid'],FILTER_SANITIZE_STRING);
    $attach_id = filter_var($_POST['ticketattachedid'],FILTER_SANITIZE_STRING);
    $type = filter_var($_POST['type_of_time'],FILTER_SANITIZE_STRING);
    $page = filter_var($_POST['page'],FILTER_SANITIZE_STRING);
	$comment_history = '';
	$session_user = get_contact($dbc, $_SESSION['contactid']);
    $approve_by = $_SESSION['contactid'];
    $approve_date = date('Y-m-d');
    if($_POST['save_type'] == 'multi' && $field == 'approv') {
        $prior = filter_var($_POST['prior_approv'],FILTER_SANITIZE_STRING);

        if($value == 'N') {
            $approval_status = 'Pending';
        } else if($value == 'Y') {
            $approval_status = 'Approved';
        } else if($value == 'P') {
            $approval_status = 'Paid';
        }

        //$comment_history .= $session_user.' updated status to '.$approval_status.'.<br>';
        $dbc->query("UPDATE `time_cards` SET `approv`='$value', `approve_by` = '$approve_by', `approve_date` = '$approve_date', `comment_box` = '".$time_card['comment_box'].htmlentities($comment_history)."' WHERE `staff`='$staff' AND `date`='$date' AND IFNULL(`business`,'') LIKE '%$siteid%' AND `approv`='$prior' AND `deleted`=0");
        return;
    }
    if(!($id > 0) && $layout == '') {
        $id = $dbc->query("SELECT MAX(`time_cards_id`) `time_cards_id` FROM `time_cards` WHERE `staff`='$staff' AND `date`='$date' AND '$siteid' IN (`business`,'') AND '$projectid' IN (`projectid`,'') AND '$ticketid' IN (`ticketid`,'') AND '$clientid' IN (`clientid`,'') AND '$attach_id' IN (`ticket_attached_id`,'') AND '$type_of_time' IN (`type_of_time`,'')")->fetch_assoc()['time_cards_id'];
        $total_hours = $dbc->query("SELECT SUM(`total_hrs`) `total`, IF(`time_cards_id`='$id',`total_hrs`,0) `row_hours` FROM `time_cards` WHERE `staff`='$staff' AND `date`='$date' AND '$siteid' IN (`business`,'') AND '$projectid' IN (`projectid`,'') AND '$ticketid' IN (`ticketid`,'') AND '$clientid' IN (`clientid`,'') AND '$attach_id' IN (`ticket_attached_id`,'') AND '$type_of_time' IN (`type_of_time`,'')")->fetch_assoc();
        if($total_hours['total'] != $total_hours['row_hours'] && $field == 'total_hours') {
            $value -= $total_hours['total'] - $total_hours['row_hours'];
        }
    }
    if($id > 0 && $field == 'approv') {
		$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$id'"));
        if($value == 'N') {
            $approval_status = 'Pending';
        } else if($value == 'Y') {
            $approval_status = 'Approved';
        } else if($value == 'P') {
            $approval_status = 'Paid';
        }

        $comment_history1 = $session_user.' updated status to '.$approval_status.'.<br>';
        $dbc->query("UPDATE `time_cards` SET `approv`='$value', `approve_by` = '$approve_by', `approve_date` = '$approve_date', `comment_box` = '".$time_card['comment_box'].htmlentities($comment_history1)."' WHERE `time_cards_id`='$id'");

        //$dbc->query("UPDATE `time_cards` SET `$field`='$value' WHERE `time_cards_id`='$id'");
    } else if($id > 0) {
		$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$id'"));
		if($field == 'total_hrs' && number_format($value,1) != number_format($time_card['total_hrs'],1)) {
			//$comment_history .= $session_user.' updated '.$field.' from '.$time_card['total_hrs'].' to '.$total_hrs.'.<br>';
            $comment_history .= $session_user.' updated total hours to '.$history_hours.'.<br>';
		}
		if($type_of_time != $time_card['type_of_time']) {
			//$comment_history .= $session_user.' updated Type of Time from '.$time_card['type_of_time'].' to '.$type_of_time.'.<br>';
		}
        $dbc->query("UPDATE `time_cards` SET `$field`='$value', `".($page == 'time_cards.php' ? '' : 'manager_')."highlight`=1 WHERE `time_cards_id`='$id'");
    } else {
        $comment_history = $session_user.' added Time.<br>';
        $dbc->query("INSERT INTO `time_cards` (`business`,`projectid`,`ticketid`,`date`,`clientid`,`ticket_attached_id`,`staff`,`type_of_time`,`$field`) VALUES ('$siteid','$projectid','$ticketid','$date','$clientid','$attach_id','$staff','$type','$value')");
        echo $id = $dbc->insert_id;
    }
    if($attach_id > 0 && $field == 'total_hrs') {
        $dbc->query("UPDATE `ticket_attached` SET `time_set`='$value' WHERE `id`='$attach_id'");
    }
	if($timesheet_record_history == 1 && $comment_history != '') {
        $time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$id'"));
        $comment_history = rtrim($comment_history,'<br>');
        if(!empty($time_card['comment_box'])) {
            $comment_history = '<br>'.$comment_history;
        }
        mysqli_query($dbc, "UPDATE `time_cards` SET `comment_box` = '".$time_card['comment_box'].htmlentities($comment_history)."' WHERE `time_cards_id` = '$id'");
	}

	$insert_dayoff = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_time_cards_dayoff` WHERE `hours_type` = '".$type_of_time."'"));
    $time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$id'"));
    if(!($time_card['shiftid'] > 0) && $insert_dayoff['enabled'] == 1) {
    	$dayoff_type = $insert_dayoff['dayoff_type'];
    	if(empty($dayoff_type)) {
    		$dayoff_type = 'Day Off';
    	}
    	mysqli_query($dbc, "INSERT INTO `contacts_shifts` (`contactid`,`startdate`,`enddate`,`dayoff_type`) VALUES ('$staff','$date','$date','$dayoff_type')");
    	$shiftid = mysqli_insert_id($dbc);
    	mysqli_query($dbc, "UPDATE `time_cards` SET `shiftid` = '$shiftid' WHERE `time_cards_id` = '$id'");
    }
}
else if($_GET['action'] == 'set_stat_pay') {
	$staffid = filter_var($_POST['staffid'],FILTER_SANITIZE_STRING);
	$stat_pay = filter_var($_POST['stat_pay'],FILTER_SANITIZE_STRING);
	$vaca_pay = filter_var($_POST['vaca_pay'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "UPDATE `contacts` SET `stat_pay` = '$stat_pay', `vaca_pay` = '$vaca_pay' WHERE `contactid` = '$staffid'");
}