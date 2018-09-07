<?php
include ('../include.php');
include 'config.php';

$value = $config['settings']['Choose Fields for Time Sheets Approvals'];
$back_url = 'time_card_approvals_coordinator.php';
if(strpos($_SERVER['HTTP_REFERER'],'time_card_approvals_coordinator.php') !== FALSE) {
	$back_url = 'time_card_approvals_coordinator.php';
}
if(strpos($_SERVER['HTTP_REFERER'],'time_card_approvals_manager.php') !== FALSE) {
	$back_url = 'time_card_approvals_manager.php';
}
if(strpos($_SERVER['HTTP_REFERER'],'payroll.php') !== FALSE) {
	$back_url = 'payroll.php';
}

if(isset($_GET['action']) && $_GET['action'] == 'delete') {
    mysqli_query($dbc, "DELETE FROM time_cards WHERE time_cards_id=".$_GET['time_cards_id']);

    echo '<script type="text/javascript"> window.location.replace("'.$back_url.'"); </script>';
}

if (isset($_POST['submit'])) {

    $sign = $_POST['output'];
    $img = sigJsonToImage($sign);

    $sign2 = $_POST['sign2'];
    $img2 = sigJsonToImage($sign2);

    foreach($_GET['element'] as $element) {
        imagepng($img, 'download/manager_'.$element.'.png');
        imagepng($img2, 'download/staff_'.$element.'.png');
    }

    $inputs = get_post_inputs($value['data']);

    $query_update_vendor = prepare_update($inputs, 'time_cards', 'time_cards_id', $_GET['element']);

    $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
    $url = 'Updated';

    echo '<script type="text/javascript"> window.location.replace("'.$back_url.'"); </script>';
} else if(isset($_POST['approv_db'])) {
	$staff = $_POST['staff_id'];
	/*$session_user = get_contact($dbc, $_SESSION['contactid']);
	$layout = get_config($dbc, "timesheet_layout");
	$value_config = explode(',',get_field_config($dbc, 'time_cards'));
	$timesheet_record_history = get_config($dbc, 'timesheet_record_history');
	$time_cards_id = 0;
	$site = $_POST['site_id'];
	$supervisor = $_POST['supervisor_id'];
	$super_type = $_POST['supervisor'];
	if(isset($_POST['time_cards_id'])) {
		foreach($_POST['time_cards_id'] as $i => $id) {
			$date = filter_var($_POST['date'][$i],FILTER_SANITIZE_STRING);
			$date_editable = filter_var($_POST['date_editable'][$i],FILTER_SANITIZE_STRING);
			$staff = filter_var($_POST['staff'][$i],FILTER_SANITIZE_STRING);
			$type_of_time = filter_var($_POST['type_of_time'][$i],FILTER_SANITIZE_STRING);
			$start_time = filter_var($_POST['start_time'][$i],FILTER_SANITIZE_STRING);
			$end_time = filter_var($_POST['end_time'][$i],FILTER_SANITIZE_STRING);
			if(strpos($_POST['total_hrs'][$i],':') !== FALSE) {
				$_POST['total_hrs'][$i] = explode(':',$_POST['total_hrs'][$i]);
				$_POST['total_hrs'][$i] = $_POST['total_hrs'][$i][0] + ($_POST['total_hrs'][$i][1] / 60);
			}
			if(strpos($_POST['total_hrs_vac'][$i],':') !== FALSE) {
				$_POST['total_hrs_vac'][$i] = explode(':',$_POST['total_hrs_vac'][$i]);
				$_POST['total_hrs_vac'][$i] = $_POST['total_hrs_vac'][$i][0] + ($_POST['total_hrs_vac'][$i][1] / 60);
			}
			$total_hrs = filter_var($_POST['total_hrs'][$i],FILTER_SANITIZE_STRING);
			$total_hrs_vac = filter_var($_POST['total_hrs_vac'][$i],FILTER_SANITIZE_STRING);
			$comment_box = filter_var($_POST['comment_box'][$i],FILTER_SANITIZE_STRING);
			$business = filter_var($_POST['site_id'],FILTER_SANITIZE_STRING);
			$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
			$deleted = filter_var($_POST['deleted'][$i],FILTER_SANITIZE_STRING);
			$ids = [];
			$comment_history = [];
			if($id > 0) {
				$ids[] = $id;
				$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$id'"));
				if($time_card['type_of_time'] == 'Vac Hrs.') {
					if(number_format($total_hrs_vac,1) != number_format($time_card['total_hrs'],1)) {
						$comment_history[$id] .= $session_user.' updated Vacation Hours from '.$time_card['total_hrs'].' to '.$total_hrs_vac.'.<br>';
					}
					$_SERVER['DBC']->query("UPDATE `time_cards` SET `total_hrs`='$total_hrs_vac', `type_of_time`='Vac Hrs.', `comment_box`='$comment_box', `deleted` = '$deleted' WHERE `time_cards_id`='$id'");
					if($total_hrs > 0 && $deleted != 1) {
						$_SERVER['DBC']->query("INSERT INTO `time_cards` (`date`, `staff`, `type_of_time`, `start_time`, `end_time`, `total_hrs`, `comment_box`) VALUES ('$date', '$staff', '$type_of_time', '$start_time', '$end_time', '$total_hrs', '$comment_box')");
						$ids[] = mysqli_insert_id($dbc);
						$comment_history[$id] .= $session_user.' inserted new row with Hours'.(!empty($type_of_time) ? ' for '.$type_of_time : '').'.<br>';
					}
				} else if($total_hrs > 0 || $deleted == 1) {
					$_SERVER['DBC']->query("UPDATE `time_cards` SET `total_hrs`='$total_hrs', `type_of_time`='$type_of_time', `start_time`='$start_time', `end_time`='$end_time', `comment_box`='$comment_box', `deleted` = '$deleted' WHERE `time_cards_id`='$id'");
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
					if($total_hrs_vac > 0 && $deleted != 1) {
						$_SERVER['DBC']->query("INSERT INTO `time_cards` (`date`, `staff`, `type_of_time`, `total_hrs`, `comment_box`) VALUES ('$date', '$staff', 'Vaca Hrs', '$total_hrs_vac', '$comment_box')");
						$ids[] = mysqli_insert_id($dbc);
						$comment_history[$id] .= $session_user.' inserted new row with Vacation Hours.<br>';
					}
				}
			} else if(!empty($type_of_time.$start_time.$end_time.$total_hrs.$total_hrs_vac.$comment_box.$business.$projectid)) {
				if($total_hrs > 0) {
					$_SERVER['DBC']->query("INSERT INTO `time_cards` (`date`, `staff`, `type_of_time`, `start_time`, `end_time`, `total_hrs`, `comment_box`) VALUES ('$date', '$staff', '$type_of_time', '$start_time', '$end_time', '$total_hrs', '$comment_box')");
					$ids[] = mysqli_insert_id($dbc);
					$comment_history[$id] .= $session_user.' inserted new row with Hours'.(!empty($type_of_time) ? ' for '.$type_of_time : '').'.<br>';
				}
				if($total_hrs_vac > 0) {
					$_SERVER['DBC']->query("INSERT INTO `time_cards` (`date`, `staff`, `type_of_time`, `total_hrs`, `comment_box`) VALUES ('$date', '$staff', 'Vac Hrs.','$total_hrs_vac', '$comment_box')");
					$ids[] = mysqli_insert_id($dbc);
					$comment_history[$id] .= $session_user.' inserted new row with Vacation Hours.<br>';
				}
				if($_POST['approve_date_id'][$i] != 'UNCHECKED_PLACEHOLDER' && isset($_POST['approve_date_id'][$i])) {
					$_POST['approve_date_id'][$i] = $id;
				}
			}
			foreach($ids as $id) {
				if($id > 0 && !empty($date_editable) && $date != $date_editable) {
					mysqli_query($dbc, "UPDATE `time_cards` SET `date` = '$date_editable' WHERE `time_cards_id` = '$id'");
				}
			}
			if($layout == 'ticket_task') {
				$ticketid = filter_var($_POST['ticketid'][$i],FILTER_SANITIZE_STRING);
				foreach($ids as $id) {
					$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$id'"));
					if((!empty($ticketid) ? $ticketid : 0) != $time_card['ticketid']) {
						$comment_history[$id] .= $session_user.' updated '.TICKET_NOUN.' from '.get_ticket_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$time_card['ticketid']."'"))).' to '.get_ticket_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"))).'.<br>';
					}
					$_SERVER['DBC']->query("UPDATE `time_cards` SET `ticketid` = '$ticketid' WHERE `time_cards_id`='$id'");
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
				}
			}
		}
		$super_type = $_POST['supervisor'];
		$supervisor = $_SESSION['contactid'];
		foreach(array_unique($_POST['staff']) as $staff) {
			if($_POST['approve_db'] == 'approv_btn') {
				$sign = $_POST['output'];
				if($_POST['use_profile_sig'] == 1) {
					$sign = html_entity_decode(mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_description` WHERE `contactid` = '".$_SESSION['contactid']."'"))['stored_signature']);
				}
				$img = sigJsonToImage($sign);
				$sign_file = 'download/'.$super_type.'_'.date('Y_m_d');
				$file_name = $sign_file;
				$i = 0;
				while(file_exists($file_name.'.png')) {
					$file_name = $sign_file.++$i;
				}
				$file_name .= '.png';
				imagepng($img, $file_name);

				foreach($_POST['approve_date'] as $date) {
					if($super_type == 'Manager') {
						$sql_approval = "UPDATE `time_cards` SET `manager_name`='".get_contact($dbc,$supervisor)."', `date_manager`='".date('Y-m-d')."', `manager_signature`='".$file_name."', `manager_approvals` = IF(CONCAT(',',IFNULL(`manager_approvals`,''),',') LIKE '%,$supervisor,%', `manager_approvals`, CONCAT(`manager_approvals`,',$supervisor')) WHERE `date`='$date' AND `staff`='$staff' AND `business`='$site' AND `deleted`=0";
					} else if($super_type == 'Coordinator') {
						$sql_approval = "UPDATE `time_cards` SET `coordinator_name`='".get_contact($dbc,$supervisor)."', `date_coordinator`='".date('Y-m-d')."', `coordinator_signature`='".$file_name."', `coord_approvals` = IF(CONCAT(',',IFNULL(`coord_approvals`,''),',') LIKE '%,$supervisor,%', `coord_approvals`, CONCAT(`coord_approvals`,',$supervisor')) WHERE `date`='$date' AND `staff`='$staff' AND `business`='$site' AND `deleted`=0";
					}
					$result_approval = mysqli_query($dbc, $sql_approval);
				}
				foreach($_POST['approve_date_id'] as $time_cards_id) {
					if($time_cards_id != 'UNCHECKED_PLACEHOLDER') {
						if($super_type == 'Manager') {
							$sql_approval = "UPDATE `time_cards` SET `manager_name`='".get_contact($dbc,$supervisor)."', `date_manager`='".date('Y-m-d')."', `manager_signature`='".$file_name."', `manager_approvals` = IF(CONCAT(',',IFNULL(`manager_approvals`,''),',') LIKE '%,$supervisor,%', `manager_approvals`, CONCAT(`manager_approvals`,',$supervisor')) WHERE `time_cards_id` = '$time_cards_id'";
						} else if($super_type == 'Coordinator') {
							$sql_approval = "UPDATE `time_cards` SET `coordinator_name`='".get_contact($dbc,$supervisor)."', `date_coordinator`='".date('Y-m-d')."', `coordinator_signature`='".$file_name."', `coord_approvals` = IF(CONCAT(',',IFNULL(`coord_approvals`,''),',') LIKE '%,$supervisor,%', `coord_approvals`, CONCAT(`coord_approvals`,',$supervisor')) WHERE `time_cards_id` = '$time_cards_id'";
						}
					}
					$result_approval = mysqli_query($dbc, $sql_approval);
				}

				$tab_config = get_config($dbc, 'timesheet_tabs');
				if(strpos(','.$tab_config.',',',Coordinator Approvals,') !== FALSE && strpos(','.$tab_config.',',',Manager Approvals,') !== FALSE) {
					$sql_final_approval = "UPDATE `time_cards` SET `approv`='Y' WHERE `manager_name` IS NOT NULL AND `date_manager` IS NOT NULL AND `coordinator_name` IS NOT NULL AND `date_coordinator` IS NOT NULL AND `deleted`=0";
				} else {
					$sql_final_approval = "UPDATE `time_cards` SET `approv`='Y' WHERE (`manager_name` IS NOT NULL AND `date_manager` IS NOT NULL) OR (`coordinator_name` IS NOT NULL AND `date_coordinator` IS NOT NULL) AND `deleted`=0";
				}
				$result_final_approval = mysqli_query($dbc, $sql_final_approval);
			} else if($_POST['approve_db'] == 'paid_btn') {
				foreach($_POST['approve_date'] as $date) {
					$sql_approval = "UPDATE `time_cards` SET `approv`='P' WHERE `date`='$date' AND `staff`='$staff' AND `business`='$site' AND `deleted`=0";
					$result_approval = mysqli_query($dbc, $sql_approval);
				}
			}
		}
	} else {
		foreach($_POST as $name => $value) {
			if($name == 'delete_time_cards') {
				foreach($value as $delete_id) {
					if($delete_id > 0) {
								$date_of_archival = date('Y-m-d');
							mysqli_query($dbc, "UPDATE `time_cards` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `time_cards_id` = '$delete_id'");
					}
				}
				continue;
			} else if($layout == 'multi_line' && explode('_',$name)[0] == 'timecardid') {
				$time_cards_id = filter_var($value,FILTER_SANITIZE_STRING);
				continue;
			} else if($name == 'staff_id') {
				$staff = filter_var($value,FILTER_SANITIZE_STRING);
				continue;
			} else if($name == 'site_id') {
				$site = filter_var($value,FILTER_SANITIZE_STRING);
				continue;
			} else if($value != '') {
				$name = explode('_',$name);
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
					case 'vaca': $type = 'Vac Hrs.Taken'; break;
					case 'comments': $type = 'Comment'; break;
					case 'approvedateid': $type = 'Approve Date ID'; break;
					default: continue;
				}
				if($type != '') {
					$date = $name[1].'-'.$name[2].'-'.$name[3];
					if($layout == 'multi_line' && $time_cards_id > 0) {
						$time_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '$time_cards_id'"));
					}
					if($type == 'Approve Date ID') {
						if($value != 'UNCHECKED_PLACEHOLDER') {
							$_POST['approve_date_id'][] = $value;
						}
					} else if($layout == 'multi_line' && $time_cards_id > 0) {
						if($type == 'Comment') {
							$sql = "UPDATE `time_cards` SET `comment_box`='$value' WHERE `time_cards_id` = '$time_cards_id'";
							$result = mysqli_query($dbc, $sql);
						} else {
							if(strpos($value,':') !== FALSE) {
								$value = explode(':',$value);
								$value = $value[0] + ($value[1] / 60);
							}
							$value = number_format($value, 2);
							$sql = "UPDATE `time_cards` SET `total_hrs`='$value' WHERE `time_cards_id` = '$time_cards_id'";
							$result = mysqli_query($dbc, $sql);

							if(in_array('update_ticket_payable',$value_config) && !empty($_POST['ticketattachedid_'.explode('_',implode('_',$name),2)[1]])) {
								mysqli_query($dbc, "UPDATE `ticket_attached` SET `hours_set` = '$value' WHERE `id` = '".$_POST['ticketattachedid_'.explode('_',implode('_',$name),2)[1]]."'");
							}
						}
					} else if($type == 'Comment') {
						$value = filter_var($value,FILTER_SANITIZE_STRING);
						$sql = "UPDATE `time_cards` SET `comment_box`='$value' WHERE `date`='$date' AND `staff`='$staff' AND '$site' IN (IFNULL(`business`,''),'') AND `deleted`=0";
						$result = mysqli_query($dbc, $sql);
					} else {
						if(strpos($value,':') !== FALSE) {
							$value = explode(':',$value);
							$value = $value[0] + ($value[1] / 60);
						}
						$value = number_format($value, 2);
						if($layout != 'multi_line') {
							$total_hrs = $dbc->query("SELECT SUM(`total_hrs`) `hrs` FROM `time_cards` WHERE `date`='$date' AND `staff`='$staff' AND (`type_of_time`='$type' OR ('$type' = 'Regular Hrs.' AND `type_of_time` NOT IN ('Extra Hrs.','Relief Hrs.','Sleep Hrs.','Sick Time Adj.','Sick Hrs.Taken','Stat Hrs.','Stat Hrs.Taken','Vac Hrs.','Vac Hrs.Taken','Break'))) AND '$site' IN (IFNULL(`business`,''),'') AND `deleted`=0")->fetch_assoc();
							$value -= $total_hrs['hrs'];
						}
						if($value > 0 || $value < 0) {
							$sql = "INSERT INTO `time_cards` (`date`, `type_of_time`, `business`, `staff`, `total_hrs`) VALUES ('$date','$type','$site','$staff','$value')";
							$result = mysqli_query($dbc, $sql);
							$time_cards_id = mysqli_insert_id($dbc);

							$approve_field_name = $name;
							unset($approve_field_name[0]);
							$approve_field_name = 'approvedateid_'.implode('_', $approve_field_name);
							if($_POST[$approve_field_name] != 'UNCHECKED_PLACEHOLDER' && isset($_POST[$approve_field_name])) {
								$_POST['approve_date_id'][] = $time_cards_id;
							}
						} else {
							$sql = "SELECT 'unchanged'";
							$result = mysqli_query($dbc, $sql);
						}
					}
				}
			}
		}
		if($_POST['approv_db'] == 'approv_btn') {
			$sign = $_POST['output'];
			if($_POST['use_profile_sig'] == 1) {
				$sign = html_entity_decode(mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_description` WHERE `contactid` = '".$_SESSION['contactid']."'"))['stored_signature']);
			}
			$img = sigJsonToImage($sign);
			$sign_file = 'download/'.$super_type.'_'.date('Y_m_d');
			$file_name = $sign_file;
			$i = 0;
			while(file_exists($file_name.'.png')) {
				$file_name = $sign_file.++$i;
			}
			$file_name .= '.png';
			imagepng($img, $file_name);

			foreach($_POST['approve_date'] as $date) {
				if($super_type == 'Manager') {
					$sql_approval = "UPDATE `time_cards` SET `manager_name`='".get_contact($dbc,$supervisor)."', `date_manager`='".date('Y-m-d')."', `manager_signature`='".$file_name."', `manager_approvals` = IF(CONCAT(',',IFNULL(`manager_approvals`,''),',') LIKE '%,$supervisor,%', `manager_approvals`, CONCAT(`manager_approvals`,',$supervisor')) WHERE `date`='$date' AND `staff`='$staff' AND `business`='$site' AND `deleted`=0";
				} else if($super_type == 'Coordinator') {
					$sql_approval = "UPDATE `time_cards` SET `coordinator_name`='".get_contact($dbc,$supervisor)."', `date_coordinator`='".date('Y-m-d')."', `coordinator_signature`='".$file_name."', `coord_approvals` = IF(CONCAT(',',IFNULL(`coord_approvals`,''),',') LIKE '%,$supervisor,%', `coord_approvals`, CONCAT(`coord_approvals`,',$supervisor')) WHERE `date`='$date' AND `staff`='$staff' AND `business`='$site' AND `deleted`=0";
				}
				$result_approval = mysqli_query($dbc, $sql_approval);
			}
			foreach($_POST['approve_date_id'] as $time_cards_id) {
				if($super_type == 'Manager') {
					$sql_approval = "UPDATE `time_cards` SET `manager_name`='".get_contact($dbc,$supervisor)."', `date_manager`='".date('Y-m-d')."', `manager_signature`='".$file_name."', `manager_approvals` = IF(CONCAT(',',IFNULL(`manager_approvals`,''),',') LIKE '%,$supervisor,%', `manager_approvals`, CONCAT(`manager_approvals`,',$supervisor')) WHERE `time_cards_id` = '$time_cards_id'";
				} else if($super_type == 'Coordinator') {
					$sql_approval = "UPDATE `time_cards` SET `coordinator_name`='".get_contact($dbc,$supervisor)."', `date_coordinator`='".date('Y-m-d')."', `coordinator_signature`='".$file_name."', `coord_approvals` = IF(CONCAT(',',IFNULL(`coord_approvals`,''),',') LIKE '%,$supervisor,%', `coord_approvals`, CONCAT(`coord_approvals`,',$supervisor')) WHERE `time_cards_id` = '$time_cards_id'";
				}
				$result_approval = mysqli_query($dbc, $sql_approval);
			}

			$tab_config = get_config($dbc, 'timesheet_tabs');
			if(strpos(','.$tab_config.',',',Coordinator Approvals,') !== FALSE && strpos(','.$tab_config.',',',Manager Approvals,') !== FALSE) {
				$sql_final_approval = "UPDATE `time_cards` SET `approv`='Y' WHERE `manager_name` IS NOT NULL AND `date_manager` IS NOT NULL AND `coordinator_name` IS NOT NULL AND `date_coordinator` IS NOT NULL AND `deleted`=0 AND `approv`!='P'";
			} else {
				$sql_final_approval = "UPDATE `time_cards` SET `approv`='Y' WHERE ((`manager_name` IS NOT NULL AND `date_manager` IS NOT NULL) OR (`coordinator_name` IS NOT NULL AND `date_coordinator` IS NOT NULL)) AND `deleted`=0 AND `approv`!='P'";
			}
			$result_final_approval = mysqli_query($dbc, $sql_final_approval);
		} else if($_POST['approv_db'] == 'paid_btn') {
			foreach($_POST['approve_date'] as $date) {
				$sql_approval = "UPDATE `time_cards` SET `approv`='P' WHERE `date`='$date' AND `staff`='$staff' AND `business`='$site' AND `deleted`=0";
				$result_approval = mysqli_query($dbc, $sql_approval);
			}
			foreach($_POST['approvedateid'] as $time_cards_id) {
				$sql_approval = "UPDATE `time_cards` SET `approv`='P' WHERE `time_cards_id` = '$time_cards_id'";
				$result_approval = mysqli_query($dbc, $sql_approval);
			}
			$back_url = 'payroll.php';
		}
	}*/
	echo "<script>window.location.replace('".$back_url."?tab=".$_GET['tab']."&subtab=".$_GET['subtab']."&search_staff%5B%5D=".$staff."&pay_period=".$_GET['pay_period']."&search_start_date=".$_GET['search_start_date']."&search_end_date=".$_GET['search_end_date']."&search_site=".$_GET['search_site']."');</script>";
}
?>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('timesheet');

if(!empty($_GET['manager_approval'])) {
	$date_manager = date('Y-m-d');
}
if(!empty($_GET['coordinator_approval'])) {
	$date_coordinator = date('Y-m-d');
}
?>
<div class="container">
  <div class="row">

    <h1 class="triple-pad-bottom">Time Sheet Approvals</h1>

    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">




<div class="panel-group" id="accordion2">
<?php

$k=0;
if(isset($value['config_field'])) {
    $get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
    $value_config = ','.$get_field_config[$value['config_field']].',';
    foreach($value['data'] as $tab_name => $tabs) {
    ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field<?php echo $k; ?>" >
                    <?php echo $tab_name; ?><span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>
        <div id="collapse_field<?php echo $k; ?>" class="panel-collapse collapse">
            <div class="panel-body">
                    <?php
                        foreach($tabs as $field) {
                            if (strpos($value_config, ','.$field[2].',') !== FALSE) {
                                echo get_field($field, @$$field[2], $dbc);
                            }
                        }
                    ?>
                </ul>
            </div>
        </div>
    </div>
    <?php
       $k++;
    }

}


?>
</div>

        <div class="form-group">
          <div class="col-sm-4">
              <p><span class="hp-red pull-right"><em>Required Fields *</em></span></p>
          </div>
          <div class="col-sm-8"></div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 clearfix">
                <a href="<?php echo $back_url; ?>" class="btn brand-btn pull-right">Back</a>
            </div>
          <div class="col-sm-8">
            <button type="submit" name="submit" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
          </div>
        </div>



    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
