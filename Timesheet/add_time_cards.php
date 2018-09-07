<?php
include ('../include.php');
include 'config.php';

$value = $config['settings']['Choose Fields for Time Sheets'];

if(isset($_GET['action']) && $_GET['action'] == 'delete') {
    mysqli_query($dbc, "DELETE FROM time_cards WHERE time_cards_id=".$_GET['time_cards_id']);

    echo '<script type="text/javascript"> window.location.replace("time_cards.php"); </script>';
} else if (isset($_POST['submit']) && $_POST['submit'] == 'Submit') {
	/*$_POST['total_hrs'] = explode(':',$_POST['total_hrs']);
	$_POST['total_hrs'] = $_POST['total_hrs'][0] + ($_POST['total_hrs'][1] / 60);
    $inputs = get_post_inputs($value['data']);
    $files = get_post_uploads($value['data']);
	if($_POST['ticketid'] > 0) {
		$ticket_date = date('Y-m-d h:i:s',strtotime($_POST['date'].' '.$_POST['start_time']));
		$dbc->query("INSERT INTO `ticket_time_list` (`ticketid`, `time_type`, `time_length`, `created_by`, `created_date`) VALUES ('{$_POST['ticketid']}', 'Manual Time', SEC_TO_TIME('".($_POST['total_hrs'] * 3600)."'), '".filter_var($_POST['staff'],FILTER_SANITIZE_STRING)."', '".$ticket_date."')");
	}
    move_files($files);

    if(empty($_POST['time_cards_id'])) {
        $query_insert_vendor = prepare_insert($inputs, 'time_cards');
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $bowel_movement_id = mysqli_insert_id($dbc);
        $url = 'Added';
    } else {
        $time_cards_id = $_POST['time_cards_id'];
        $query_update_vendor = prepare_update($inputs, 'time_cards', 'time_cards_id', $time_cards_id);
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
    }

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }*/

    echo '<script type="text/javascript"> window.location.replace("time_cards.php"); </script>';

} else if(isset($_POST['submit']) && ($_POST['submit'] == 'rate_timesheet' || $_POST['submit'] == 'rate_approval')) {
	$staff = $_POST['staff_id'];
	$site = $_POST['site_id'];
	$projectid = $_POST['projectid'];
	$cat_comments = [];
	$ids = [ 0 ];
	// foreach($_POST['cat_comment'] as $row => $comment) {
		// $cat_comments[] = [ filter_var($comment,FILTER_SANITIZE_STRING), $_POST['comment_cat'][$row], $_POST['comment_date'][$row] ];
	// }
    // foreach($_POST['location'] as $row => $location) {
        // $locations[] = [ filter_var($location,FILTER_SANITIZE_STRING), $_POST['location_date'][$row] ];
    // }
    // foreach($_POST['customer'] as $row => $customer) {
        // $customers[] = [ filter_var($customer,FILTER_SANITIZE_STRING), $_POST['customer_date'][$row] ];
    // }
    // foreach($_POST['hours'] as $row => $hours) {
        // $category = $_POST['hours_cat'][$row];
        // $type = $_POST['hours_type'][$row];
        // $date = $_POST['hours_date'][$row];
        // $location = '';
        // foreach($locations as $location_arr) {
            // $location = ($date == $location_arr[1] ? $location_arr[0] : $location);
        // }
        // $customer = '';
        // foreach($customers as $customer_arr) {
            // $customer = ($date == $customer_arr[1] ? $customer_arr[0] : $customer);
        // }
        // $comment = '';
        // foreach($cat_comments as $comment_arr) {
            // $comment = ($category == $comment_arr[1] && $date == $comment_arr[2] ? $comment_arr[0] : $comment);
        // }
        // if($type != '' && $hours > 0) {
            // $sql = "INSERT INTO `time_cards` (`date`, `type_of_time`, `business`, `staff`, `location`, `customer`, `projectid`, `highlight`) SELECT '$date','$type','$site','$staff','$location','$customer','$projectid',1 FROM
                // (SELECT COUNT(*) num_rows FROM `time_cards` WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0) rows WHERE rows.num_rows = 0";
            // $result = mysqli_query($dbc, $sql);
            // $sql = "UPDATE `time_cards` SET `total_hrs`='$hours', `location`='$location', `customer`='$customer', `day`='', `projectid`='$projectid' WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0";
            // $result = mysqli_query($dbc, $sql);
            // $sql = "UPDATE `time_cards` SET `comment_box`='$comment' WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND '$comment'!='' AND `deleted`=0";
            // $result = mysqli_query($dbc, $sql);
            // $sql = "SELECT `time_cards_id` FROM `time_cards` WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0";
            // $ids[] = mysqli_fetch_array(mysqli_query($dbc, $sql))['time_cards_id'];
        // }
    // }
    foreach($_POST['day'] as $row => $hours) {
        // $category = $_POST['day_cat'][$row];
        $type = $_POST['day_type'][$row];
        $date = $_POST['day_date'][$row];
        // $location = '';
        // foreach($locations as $location_arr) {
            // $location = ($date == $location_arr[1] ? $location_arr[0] : $location);
        // }
        // $customer = '';
        // foreach($customers as $customer_arr) {
            // $customer = ($date == $customer_arr[1] ? $customer_arr[0] : $customer);
        // }
        // $comment = '';
        // foreach($cat_comments as $comment_arr) {
            // $comment = ($category == $comment_arr[1] && $date == $comment_arr[2] ? $comment_arr[0] : $comment);
        // }
        // if($type != '') {
            // $day = '';
            // if (isset($_POST['day_checkbox'][$row])) {
                // $day = '1';
                // $sql = "INSERT INTO `time_cards` (`date`, `type_of_time`, `business`, `staff`, `location`, `customer`, `projectid`, `highlight`) SELECT '$date','$type','$site','$staff','$location','$customer','$projectid',1 FROM
                    // (SELECT COUNT(*) num_rows FROM `time_cards` WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0) rows WHERE rows.num_rows = 0";
                // $result = mysqli_query($dbc, $sql);
            // }
            // $sql = "UPDATE `time_cards` SET `total_hrs`='', `location`='$location', `customer`='$customer', `day`='$day', `projectid`='$projectid' WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0";
            // $result = mysqli_query($dbc, $sql);
            // $sql = "UPDATE `time_cards` SET `comment_box`='$comment' WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND '$comment'!='' AND `deleted`=0";
            // $result = mysqli_query($dbc, $sql);
            $sql = "SELECT `time_cards_id` FROM `time_cards` WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0";
            $ids[] = mysqli_fetch_array(mysqli_query($dbc, $sql))['time_cards_id'];
        // }
    }
	if($_POST['submit'] == 'rate_approval') {
		$result = mysqli_query($dbc, "SELECT * FROM `field_config_supervisor` WHERE `staff_list` like '%,$staff,%'");
		if($result = mysqli_fetch_array($result)) {
			$staff_name = get_contact($dbc,$staff);
			$manager_type = $result['position'];
			$manager = $result['supervisor'];
			$manager_name = get_contact($dbc,$manager);
			$manager_email = get_email($dbc,$manager);
			$subject = $staff_name.' has submitted a Time Sheet for Approval';
			$body = "$staff_name has submitted a Time Sheet for Approval.<br />
				<a href='".WEBSITE_URL."/Timesheet/time_card_approvals_".strtolower($manager_type).".php?search_staff=".$staff."'>Click Here</a> to approve the Time Sheet";
			send_email('', $manager_email, '', '', $subject, $body, '');
		} else {
            array_filter($ids);
			$ids = implode(',',$ids);
			$sql_update = "UPDATE `time_cards` SET `approv`='Y' WHERE `time_cards_id` IN ($ids)";
			mysqli_query($dbc, $sql_update);
		}
	}
	echo "<script>window.location.replace('time_cards.php');</script>";
} else if(isset($_POST['submit']) && ($_POST['submit'] == 'positions' || $_POST['submit'] == 'position_approval') || $_POST['submit'] == 'ticket_task') {
	/*$page_staff = filter_var($_POST['staff_id'],FILTER_SANITIZE_STRING);
	// foreach($_POST['time_cards_id'] as $i => $id) {
		// $date = filter_var($_POST['date'][$i],FILTER_SANITIZE_STRING);
		// $staff = filter_var($_POST['staff'][$i],FILTER_SANITIZE_STRING);
		// $type_of_time = filter_var($_POST['type_of_time'][$i],FILTER_SANITIZE_STRING);
		// if(strpos($_POST['total_hrs'][$i],':') !== FALSE) {
			// $_POST['total_hrs'][$i] = explode(':',$_POST['total_hrs'][$i]);
			// $_POST['total_hrs'][$i] = $_POST['total_hrs'][$i][0] + ($_POST['total_hrs'][$i][1] / 60);
		// }
		// $total_hrs = filter_var($_POST['total_hrs'][$i],FILTER_SANITIZE_STRING);
		// $comment_box = filter_var($_POST['comment_box'][$i],FILTER_SANITIZE_STRING);
		// $business = filter_var($_POST['site_id'],FILTER_SANITIZE_STRING);
		// $projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
		// if(!empty($total_hrs)) {
			// if($id > 0) {
				// mysqli_query($dbc,"UPDATE `time_cards` SET `total_hrs`='$total_hrs', `type_of_time`='$type_of_time', `comment_box`='$comment_box' WHERE `time_cards_id`='$id'");
			// } else {
				// mysqli_query($dbc,"INSERT INTO `time_cards` (`date`, `staff`, `type_of_time`, `total_hrs`, `comment_box`, `projectid`, `business`) VALUES('$date', '$staff', '$type_of_time', '$total_hrs', '$comment_box', '$business', '$projectid')");
				// $id = mysqli_insert_id($dbc);
			// }
			// if($_POST['submit'] == 'ticket_task') {
				// $ticketid = $_POST['ticketid'][$i];
				// mysqli_query($dbc, "UPDATE `time_cards` SET `ticketid` = '$ticketid' WHERE `time_cards_id` = '$id'");
				// $ticket_attached = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `src_table` = 'Staff_Tasks' AND `position` = '$type_of_time' AND `src_id` = '$staff' AND `deleted` = 0"));
				// if(empty($ticket_attached) && !empty($type_of_time)) {
					// mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `src_table`, `src_id`, `position`, `date_stamp`, `hours_set`) VALUES ('$ticketid', 'Staff_tasks', '$type_of_time', '$date', '$total_hrs')");
				// }
			// }
		// }
	// }
	if($_POST['submit'] == 'position_approval') {
		$result = mysqli_query($dbc, "SELECT * FROM `field_config_supervisor` WHERE `staff_list` like '%,$page_staff,%'");
		if($result = mysqli_fetch_array($result)) {
			$staff_name = get_contact($dbc,$page_staff);
			$manager_type = $result['position'];
			$manager = $result['supervisor'];
			$manager_name = get_contact($dbc,$manager);
			$manager_email = get_email($dbc,$manager);
			$subject = $staff_name.' has submitted a Time Sheet for Approval';
			$body = "$staff_name has submitted a Time Sheet for Approval.<br />
				<a href='".WEBSITE_URL."/Timesheet/time_card_approvals_".strtolower($manager_type).".php?search_staff=".$page_staff."'>Click Here</a> to approve the Time Sheet";
			send_email('', $manager_email, '', '', $subject, $body, '');
		} else {
			$ids = implode(',',$ids);
			$sql_update = "UPDATE `time_cards` SET `approv`='Y' WHERE `time_cards_id` IN ($ids) AND `deleted`=0";
			mysqli_query($dbc, $sql_update);
		}
	}*/
	echo "<script>window.location.replace('time_cards.php');</script>";
} else if(isset($_POST['submit']) && ($_POST['submit'] == 'timesheet' || $_POST['submit'] == 'approval')) {
	/*// if(strpos($_POST['total_hrs'],':') !== FALSE) {
		// $_POST['total_hrs'] = explode(':',$_POST['total_hrs']);
		// $_POST['total_hrs'] = $_POST['total_hrs'][0] + ($_POST['total_hrs'][1] / 60);
	// }
	$staff = $_POST['staff_id'];
	$site = $_POST['site_id'];
	// $ids = [ 0 ];
	foreach($_POST as $field => $row) {
		foreach($row as $i => $value) {
			if($field == 'staff_id' || $field == 'site_id' || $field == 'time_cards_id') {
				continue;
			} else if($value != '') {
				$name = explode('_',$field);
				$type = '';
				switch($name[0]) {
					case 'regular': $type = 'Regular Hrs.'; break;
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
				// $id = filter_var($_POST['time_cards_id_'.$name[1].'_'.$name[2].'_'.$name[3]][$i],FILTER_SANITIZE_STRING);
				// $comment = filter_var($_POST['add_comment_'.$name[1].'_'.$name[2].'_'.$name[3]][$i],FILTER_SANITIZE_STRING);
				// $deleted = $_POST['deleted_'.$name[1].'_'.$name[2].'_'.$name[3]][$i] > 0 ? 1 : 0;
				// if($type != '') {
					// $id_search = '';
					// if ($id > 0) {
						// $id_search = "AND `time_cards_id`='$id'";
					// }
					// $value = number_format ( time_time2decimal($value), 3 );
					$date = $name[1].'-'.$name[2].'-'.$name[3];
					// $sql = "INSERT INTO `time_cards` (`date`, `type_of_time`, `business`, `staff`, `projectid`, `highlight`) SELECT '$date','$type','$site','$staff','$projectid',1 FROM
						// (SELECT COUNT(*) num_rows FROM `time_cards` WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0 $id_search) rows WHERE rows.num_rows = 0";
					// $result = mysqli_query($dbc, $sql);
					// if(mysqli_insert_id($dbc) > 0) {
						// $id_search = "AND `time_cards_id`='".mysqli_insert_id($dbc)."'";
					// }
					// $prev_hrs = $dbc->query("SELECT SUM(`total_hrs`) `hrs`, MAX(`time_cards_id`) `id` FROM `time_cards` WHERE `deleted`=0 AND `staff`='$staff' AND `date`='$date' AND IFNULL(`business`,'')='$site'".($id_search != '' ? " AND `time_cards_id` NOT IN (SELECT `time_cards_id` FROM `time_cards` WHERE `deleted`=0 $id_search)" : ''))->fetch_assoc();
					// $value -= $prev_hrs['hrs'];
					// $id_search = "AND `time_cards_id`='".$prev_hrs['id']."'";
					// $sql = "UPDATE `time_cards` SET `total_hrs`=IFNULL(`total_hrs`,0)+($value), `comment_box`=CONCAT(IFNULL(CONCAT(`comment_box`,IF('$comment'!='' AND `comment_box`!='','&lt;br /&gt;','')),''),'$comment'), `deleted`='$deleted' WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0 $id_search";
					// $result = mysqli_query($dbc, $sql);
					$sql = "SELECT `time_cards_id` FROM `time_cards` WHERE `date`='$date' AND `staff`='$staff' AND `type_of_time`='$type' AND `business`='$site' AND `deleted`=0 $id_search";
					$ids[] = mysqli_fetch_array(mysqli_query($dbc, $sql))['time_cards_id'];
				// }
			}
		}
	}
	if($_POST['submit'] == 'approval') {
		$result = mysqli_query($dbc, "SELECT * FROM `field_config_supervisor` WHERE `staff_list` like '%,$staff,%'");
		if($result = mysqli_fetch_array($result)) {
			$staff_name = get_contact($dbc,$staff);
			$manager_type = $result['position'];
			$manager = $result['supervisor'];
			$manager_name = get_contact($dbc,$manager);
			$manager_email = get_email($dbc,$manager);
			$subject = $staff_name.' has submitted a Time Sheet for Approval';
			$body = "$staff_name has submitted a Time Sheet for Approval.<br />
				<a href='".WEBSITE_URL."/Timesheet/time_card_approvals_".strtolower($manager_type).".php?search_staff=".$staff."'>Click Here</a> to approve the Time Sheet";
			send_email('', $manager_email, '', '', $subject, $body, '');
		} else {
			$ids = implode(',',$ids);
			$sql_update = "UPDATE `time_cards` SET `approv`='Y' WHERE `time_cards_id` IN ($ids) AND `deleted`=0";
			mysqli_query($dbc, $sql_update);
		}
	}*/
	echo "<script>window.location.replace('time_cards.php');</script>";
}
?>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('timesheet');
?>
<div class="container">
  <div class="row">

    <h1>Time Sheets</h1>
	<div class="gap-top double-gap-bottom"><a href="time_cards.php" class="btn config-btn">Back to Dashboard</a></div>

    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php

        $inputs = get_all_inputs($value['data']);

        foreach($inputs as $input) {
            $$input = '';
        }

        if(!empty($_GET['time_cards_id'])) {

            $time_cards_id = $_GET['time_cards_id'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM time_cards WHERE time_cards_id='$time_cards_id'"));

            foreach($inputs as $input) {
                $$input = $get_contact[$input];
            }

        ?>
        <input type="hidden" id="time_cards_id" name="time_cards_id" value="<?php echo $time_cards_id ?>" />
        <?php } else if($_GET['projectid'] > 0) {
			$project_details = $dbc->query("SELECT `projectid`, `businessid`, `clientid`, `siteid` FROM `project` WHERE `projectid`='".filter_var($_GET['projectid'],FILTER_SANITIZE_STRING)."' AND `deleted`=0")->fetch_assoc();
			$business = $project_details['businessid'] > 0 ? $project_details['businessid'] : $project_details['siteid'];
			$customer = array_filter(explode(',',$project_details['clientid']))[0];
			$project = $project_details['projectid'];
		} ?>



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
        <div id="collapse_field<?php echo $k; ?>" class="panel-collapse collapse <?= IFRAME_PAGE ? 'in' : '' ?>">
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
			<p><span class="hp-red"><em>Required Fields *</em></span></p>
        </div>

        <div class="form-group">
            <div class="col-sm-6"><a href="time_cards.php" class="btn brand-btn btn-lg">Back</a></div>
			<div class="col-sm-6"><button type="submit" name="submit" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button></div>
			<div class="clearfix"></div>
        </div>

        

    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
