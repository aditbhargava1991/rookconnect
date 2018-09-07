<?php
/*
Add Driving Log
*/
include ('../include.php');
checkAuthorised('driving_log');
require_once('../phpsign/signature-to-image.php');
error_reporting(0);

$view_only_mode = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `driving_log_view_only_mode` WHERE `contactid` = '".$_SESSION['contactid']."'"))['view_only_mode'];
include_once('view_only_mode.php');

if (isset($_POST['submit_new'])) {
    $driverid = $_POST['driverid'];
    $codriverid = $_POST['codriverid'];
    $clientid = $_POST['clientid'];
    $cycle = $_POST['cycle'];
    $main_office_addy = $_POST['main_office_addy'];
    $home_terminal_addy = $_POST['home_terminal_addy'];
    $notes = htmlentities($_POST['notes']);
    $sign = $_POST['output'];

    // CREATE DRIVING LOG WITH OFF-DUTY HOURS SINCE LAST DRIVING LOG
    // Want more info? Here is an example: User creates a driving log and finishes it 11:59:59 PM on June 20th, 2016 (in other words, June 21st, at 12:00 AM). User takes vacation for a week, which is 7 days (June 21 + 7 = June 28, 2016). User creates a new driving log on June 28th. He has been off-duty for 7 days, so this code below creates a driving log with an off-duty timer of 168 hours (7 days * 24 hours).
    $get_time_left = "SELECT * FROM `driving_log` WHERE `driverid` = '$driverid' AND `cycle` = '$cycle' AND end_date IS NOT NULL ORDER BY `drivinglogid` DESC";

    $result1 = mysqli_query($dbc, $get_time_left);
    $num_rows1 = mysqli_num_rows($result1);

    if($num_rows1 > 0) {
        while($row1 = mysqli_fetch_array($result1)) {
            $lastdlogid = $row1['drivinglogid'];
            $get_last_dlog_enddate = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM driving_log WHERE drivinglogid='$lastdlogid'"));
            $lastenddate = $get_last_dlog_enddate['start_date']; /* This is supposed to be start_date - it is not a mistake. */
            $query_insert_graph = 'INSERT INTO `driving_log` (`driverid`, `off_duty_since_last_login`) VALUES ("'.$driverid.'", "1")';
            $result_insert_graph = mysqli_query($dbc, $query_insert_graph);
            $dlog_off_duty_id = mysqli_insert_id($dbc);

            $now = strtotime(date('Y/m/d'));
            $your_date = strtotime($lastenddate);
            $datediff = $now - $your_date;
            if($datediff > 0) {
              $datediff = $datediff - 1; /* This is to account for the fact that the start_date ends at 11:59 PM (essentially losing a day) */
            }
            $hours_from_last_dlog = floor($datediff/(60*60*24))*24;

            $time_in_12_hour_format = date("H:i");
            $reverse_explode = array_reverse(explode(':',$time_in_12_hour_format));
            $i = 0;
            $len = count($reverse_explode);
            foreach( $reverse_explode as $time ) {
                if ($i == 1) {
                    $hours = $time;
                } else {
                    $minutes = $time;
                }
                $i++;
            }
            $hours_from_last_dlog = $hours_from_last_dlog;
            $time_from_last_dlog = $hours_from_last_dlog.':'.'00'.':'.'00';
            $query_insert_graph = 'INSERT INTO `driving_log_timer` (`drivinglogid`, `level`, `off_duty_timer`) VALUES ("'.$dlog_off_duty_id.'", "1", "'.$time_from_last_dlog.'")';
            $result_insert_graph = mysqli_query($dbc, $query_insert_graph);
            break;
        }
    }
    //  END OF CREATING A DRIVING LOG WITH OFF-DUTY HOURS SINCE PREVIOUS DRIVING LOG

    if(empty($_POST['drivinglogid'])) {
        $start_date = date('Y-m-d');
        $last_timer_value = 'on_duty_timer*#*'.time();
        $start_log = date('H.i');
        $query_insert_log = "INSERT INTO `driving_log` (`main_office_address`, `off_duty_since_last_login`, `home_terminal_address`, `driverid`, `codriverid`, `clientid`, `vehicleid`, `trailerid`,  `start_date`, `last_timer_value`, `start_log`, `cycle`, `notes`) VALUES ('$main_office_addy', '', '$home_terminal_addy', '$driverid', '$codriverid', '$clientid', '$vehicleid', '$trailerid', '$start_date', '$last_timer_value', '$start_log', '$cycle', '$notes')";
        $result_insert_log = mysqli_query($dbc, $query_insert_log);
        $drivinglogid = mysqli_insert_id($dbc)+0;
        $real_drivinglogid = $drivinglogid;

        // SET OFF-DUTY TIMER
        $timer_time = date('G:i:s');
        $curr_time = date('g:i A');

        // GET TOTAL OFF-DUTY TIME
        $get_driver = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM driving_log WHERE drivinglogid='$drivinglogid'"));
        $drive_id = $get_driver['driverid'];
        $cycler = $get_driver['cycle'];

        $get_time_left = "SELECT * FROM `driving_log` WHERE `driverid` = '$drive_id' AND `cycle` = '$cycler' ORDER BY `drivinglogid` DESC";

        $result1 = mysqli_query($dbc, $get_time_left);
        $num_rows1 = mysqli_num_rows($result1);

        if($num_rows1 > 0) {
            $on_duty_time = '';
            $seconds = 0;
            $minutes = 0;
            $hours = 0;
            $reverse_explode = array_reverse(explode(':',$timer_time));

            $i = 0;
            $len = count($reverse_explode);
            foreach( $reverse_explode as $time ) {
                if ($i == 0) {
                    $seconds += $time;
                } else if ($i == $len - 1) {
                    $hours += $time;
                } else {
                    $minutes += $time;
                }
                // …
                $i++;
            }

            while($row1 = mysqli_fetch_array($result1)) {
                $drivinglogidd = $row1['drivinglogid'];

                $select_timers = "SELECT * FROM driving_log_timer WHERE drivinglogid = '$drivinglogidd' ORDER BY timerid DESC";

                $result2 = mysqli_query($dbc, $select_timers);
                $num_rows2 = mysqli_num_rows($result2);
                $is_reset = '';
                if($num_rows2 > 0) {
                    while($row2 = mysqli_fetch_array($result2)) {
                        if($row2['reset_cycle'] == 1) {
                            $is_reset .='1';
                            break;
                        }
                        if($row2['off_duty_timer'] !== '' && $row2['off_duty_timer'] !== NULL) {
                            $reverse_explode = array_reverse(explode(':',$row2['off_duty_timer']));

                            $i = 0;
                            $len = count($reverse_explode);
                            foreach( $reverse_explode as $time ) {
                                if ($i == 0) {
                                    $seconds += $time;
                                } else if ($i == $len - 1) {
                                    $hours += $time;
                                } else {
                                    $minutes += $time;
                                }
                                // …
                                $i++;
                            }
                        }
                    }
                }
            }
        }

        // SUM UP OFF DUTY TIME
        $minute_from_seconds = $seconds/60;
        $minute_add = floor($minute_from_seconds);
        $seconds_left = $minute_from_seconds - $minute_add;
        $seconds = $seconds_left*60;

        $minutes = $minutes + $minute_add;

        $hours_from_minutes = $minutes/60;
        $hour_add = floor($hours_from_minutes);
        $minutes_left = $hours_from_minutes - $hour_add;
        $minutes = $minutes_left*60;

        $hours = $hours+$hour_add;

        $hours_left = sprintf("%02d", $hours);
        $minutes_left = sprintf("%02d", $minutes);
        $seconds_left = sprintf("%02d", $seconds);
        //if statement

        if($cycler == 'Cycle 1(7 days)') {
            if($hours_left >= 36) {
                $resetter = 1;
            } else {
                $resetter = NULL;
            }
        } else {
            if($hours_left >= 72) {
                $resetter = 1;
            } else {
                $resetter = NULL;
            }
        }

        $query_insert_report = "INSERT INTO `driving_log_timer` (`drivinglogid`, `level`, `off_duty_timer`, `off_duty_time`, `end_off_duty_time`, `final_off_duty_timer`, `dl_comment`, `amendments`, `amendments_comment`, `reset_cycle`) VALUES ('$real_drivinglogid', '1', '$timer_time', '12:00 AM', '$curr_time', '$timer_time', 'Off-duty time', '', '' , '$resetter')";
        $result_insert_report = mysqli_query($dbc, $query_insert_report);

        $sign = $_POST['dl_start_sig'];
        if($sign != '') {
            $img = sigJsonToImage($sign);
            imagepng($img, 'download/dl_start_'.$real_drivinglogid.'.png');
        }

        $url = 'add_driving_log.php?timer=on&drivinglogid='.$real_drivinglogid.'#on_duty_timer';
    }

    echo '<script type="text/javascript"> window.location.replace("'.$url.'"); </script>';
}

if (isset($_POST['submit_checklist'])) {
    //Safety Inpect
    $drivinglogid = $_POST['drivinglogid'];
    $safetyinspectid = $_POST['safetyinspectid'];
    $vehicleid = $_POST['vehicleid'];
    $trailerid = $_POST['trailerid'];
    $safety_inspect_driverid = $_POST['safety_inspect_driverid'];
    $inspect_date = $_POST['inspect_date'];
    $begin_odo_kms = $_POST['begin_odo_kms'];
    $begin_hours = $_POST['begin_hours'];
    $location_of_presafety = $_POST['location_of_presafety'];
    $location_of_postsafety = $_POST['location_of_postsafety'];
    $final_odo_kms = $_POST['final_odo_kms'];
    $final_hours = $_POST['final_hours'];
    $safety1 = $_POST['safety1'];
    $safety2 = $_POST['safety2'];
    $safety3 = $_POST['safety3'];
    $safety4 = $_POST['safety4'];
    $safety5 = $_POST['safety5'];
    $safety6 = $_POST['safety6'];
    $safety7 = $_POST['safety7'];
    $safety8 = $_POST['safety8'];
    $safety9 = $_POST['safety9'];
    $safety10 = $_POST['safety10'];
    $safety11 = $_POST['safety11'];
    $safety12 = $_POST['safety12'];
    $safety13 = $_POST['safety13'];
    $safety14 = $_POST['safety14'];
    $safety15 = $_POST['safety15'];
    $safety16 = $_POST['safety16'];
    $safety17 = $_POST['safety17'];
    $safety18 = $_POST['safety18'];
    $safety19 = $_POST['safety19'];
    $safety20 = $_POST['safety20'];
    $safety21 = $_POST['safety21'];
    $safety22 = $_POST['safety22'];
    $safety23 = $_POST['safety23'];
    $safety24 = $_POST['safety24'];
    $safety25 = $_POST['safety25'];
    $safety26 = $_POST['safety26'];
    $safety27 = $_POST['safety27'];
    $safety28 = $_POST['safety28'];
    $safety29 = $_POST['safety29'];
    $safety30 = $_POST['safety30'];
    $safety31 = $_POST['safety31'];
    $safety32 = $_POST['safety32'];
    $safety33 = $_POST['safety33'];
    $safety34 = $_POST['safety34'];
    $safety35 = $_POST['safety35'];
    $safety36 = $_POST['safety36'];
    $safety37 = $_POST['safety37'];
    $safety38 = $_POST['safety38'];
    $repair_note = filter_var($_POST['repair_note'],FILTER_SANITIZE_STRING);
    if($vehicleid > 0 && $final_hours > 0 && $final_odo_kms > 0) {
        mysqli_query("UPDATE `equipment` SET `mileage`='$final_odo_kms', `hours_operated`='$final_hours' WHERE `equipmentid`='$vehicleid'");
    }

    if (empty($safetyinspectid)) {
        $query_update_log = "UPDATE `driving_log` SET `vehicleid` = '$vehicleid', `trailerid` = '$trailerid' WHERE `drivinglogid` = '$drivinglogid'";
        $result_update_log = mysqli_query($dbc, $query_update_log);

        $query_insert_sa = "INSERT INTO `driving_log_safety_inspect` (`drivinglogid`, `safety_inspect_driverid`, `safety_inspect_vehicleid`, `safety_inspect_trailerid`, `inspect_date`, `begin_odo_kms`, `final_odo_kms`, `begin_hours`, `final_hours`, `safety1`, `safety2`, `safety3`, `safety4`, `safety5`, `safety6`, `safety7`, `safety8`, `safety9`, `safety10`, `safety11`, `safety12`, `safety13`, `safety14`, `safety15`, `safety16`, `safety17`, `safety18`, `safety19`, `safety20`, `safety21`, `safety22`, `safety23`, `safety24`, `safety25`, `safety26`, `safety27`, `safety28`, `safety29`, `safety30`, `safety31`, `safety32`, `safety33`, `safety34`, `safety35`, `safety36`, `safety37`, `safety38`, `repair_note`, `location_of_presafety`) VALUES ('$drivinglogid', '$safety_inspect_driverid', '$vehicleid', '$trailerid', '$inspect_date', '$begin_odo_kms', '$final_odo_kms', '$begin_hours', '$final_hours', '$safety1', '$safety2', '$safety3', '$safety4', '$safety5', '$safety6', '$safety7', '$safety8', '$safety9', '$safety10', '$safety11', '$safety12', '$safety13', '$safety14', '$safety15', '$safety16', '$safety17', '$safety18', '$safety19', '$safety20', '$safety21', '$safety22', '$safety23', '$safety24', '$safety25', '$safety26', '$safety27', '$safety28', '$safety29', '$safety30', '$safety31', '$safety32', '$safety33', '$safety34', '$safety35', '$safety36', '$safety37', '$safety38', '$repair_note', '$location_of_presafety')";
        $result_insert_sa = mysqli_query($dbc, $query_insert_sa);
        $safetyinspectid = mysqli_insert_id($dbc);

        $sign = $_POST['dl_checklist_sig'];
        if($sign != '') {
            $img = sigJsonToImage($sign);
            imagepng($img, 'download/presafety_'.$drivinglogid.'_'.$safetyinspectid.'.png');
        }

        $url = 'add_driving_log.php?timer=on&drivinglogid='.$drivinglogid;
    } else {
        $query_update_sa = "UPDATE `driving_log_safety_inspect` SET `final_odo_kms` = '$final_odo_kms', `safety2` = '$safety2', `safety4` = '$safety4', `safety6` = '$safety6', `safety8` = '$safety8', `safety10` = '$safety10', `safety12` = '$safety12', `safety14` = '$safety14', `safety16` = '$safety16', `safety18` = '$safety18', `safety20` = '$safety20', `safety22` = '$safety22', `safety24` = '$safety24', `safety26` = '$safety26', `safety28` = '$safety28', `safety30` = '$safety30', `safety32` = '$safety32', `safety34` = '$safety34', `safety36` = '$safety36', `safety38` = '$safety38', `repair_note` = '$repair_note', `location_of_postsafety` = '$location_of_postsafety' WHERE `safetyinspectid` = '$safetyinspectid'";
        $result_update_sa = mysqli_query($dbc, $query_update_sa);

        $sign = $_POST['dl_checklist_sig'];
        if($sign != '') {
            $img = sigJsonToImage($sign);
            imagepng($img, 'download/postsafety_'.$drivinglogid.'_'.$safetyinspectid.'.png');
        }

        $url = 'add_driving_log.php?timer=on&drivinglogid='.$drivinglogid;
    }
    //Safety Inpect

    echo '<script type="text/javascript">window.location.replace("'.$url.'");</script>';
}
?>
</head>
<script type="text/javascript">
$(document).ready(function() {
    <?php if ($view_only_mode == 1) { ?>
        $('div.container form').find('input,select,button,a,textarea,.select2,.chosen-container,ul div').not('.allow_view_only').each(function() {
            $(this).css('pointer-events', 'none');
            if ($(this)[0].tagName == 'TEXTAREA') {
                $(this).parent('div').css('pointer-events', 'none');
            }
        });
    <?php } ?>
});
</script>
<body>
<?php include_once ('../navigation.php');
?>
<div class="container">
    <div class="row">

        <h3 style="margin-top: 0; padding: 0;" class="pull-left">Driving Log</h3>
        
        <div class="pull-right">View Only: <a href="" class="view_only_button"><img src="../img/icons/switch-<?= $view_only_mode == 1 ? '7' : '6' ?>.png" style="height: 2em;"></a></div>
        
        <div class="clearfix"></div>
        
        <a href="driving_log_tiles.php" class="btn config-btn">Back to Dashboard</a>
        
        <div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25px"></div>
			<div class="col-sm-16"><span class="notice-name">NOTE:</span> This section allows you to save the information of a Driver and Co-Driver. After filling in the required fields, click Submit at the bottom left side of the page to store the information in your software - this will store a log of your Drivers and Co-Drivers.</div>
		</div>
        
        <div class="clearfix"></div>

        <form id="form1" name="form1" method="post" action="add_driving_log.php" enctype="multipart/form-data" role="form" style="padding: 0; margin: 0;">
        <?php
        	$driverid = $_SESSION['contactid'];
            $codriverid = '';
            $clientid = '';
            $notes = '';
            $vehicleid = '';
            $trailerid = '';
            $distance = '';
            $safety_inspect_driverid = '';
            $safety_inspect_vehicleid = '';
            $inspect_date = '';
            $begin_odo_kms = '';
            $final_odo_kms = '';
        	$location_of_presafety = '';
        	$location_of_postsafety = '';
            $safety1 = '';
            $safety2 = '';
            $safety3 = '';
            $safety4 = '';
            $safety5 = '';
            $safety6 = '';
            $safety7 = '';
            $safety8 = '';
            $safety9 = '';
            $safety10 = '';
            $safety11 = '';
            $safety12 = '';
            $safety13 = '';
            $safety14 = '';
            $safety15 = '';
            $safety16 = '';
            $safety17 = '';
            $safety18 = '';
            $safety19 = '';
            $safety20 = '';
            $safety21 = '';
            $safety22 = '';
            $safety23 = '';
            $safety24 = '';
            $safety25 = '';
            $safety26 = '';
            $safety27 = '';
            $safety28 = '';
            $safety29 = '';
            $safety30 = '';
            $safety31 = '';
            $safety32 = '';
            $safety33 = '';
            $safety34 = '';
            $safety35 = '';
            $safety36 = '';
            $safety37 = '';
            $safety38 = '';
            $repair_note = '';
            $last_timer_value = 0;

            if(!empty($_GET['endofday']))   {
                echo '<input type="hidden" id="endofday" name="endofday" value="1" />';
            } else {
                echo '<input type="hidden" id="endofday" name="endofday" value="0" />';
            }
            if(!empty($_GET['safety'])) {
                echo '<input type="hidden" id="safety" name="safety" value="1" />';
            } else {
                echo '<input type="hidden" id="safety" name="safety" value="0" />';
            }
            if(!empty($_GET['drivinglogid']))	{
        		$drivinglogid = $_GET['drivinglogid'];
        		$get_log = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM driving_log WHERE	drivinglogid='$drivinglogid'"));

                $driverid = $get_log['driverid'];
                $codriverid = $get_log['codriverid'];
                $clientid = $get_log['clientid'];
                $notes = $get_log['notes'];
                $vehicleid = $get_log['vehicleid'];
                $trailerid = $get_log['trailerid'];
                $distance = $get_log['distance'];
                $last_timer_value = $get_log['last_timer_value'];

                if($last_timer_value == '0') {
                    $time_seconds = 0;
                } else {
                    $timer_name = explode('*#*',$last_timer_value);
                    $time_seconds = (time()-$timer_name[1]);
                }

                $vehicle_status = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `driving_log_safety_inspect` WHERE `drivinglogid` = '$drivinglogid' ORDER BY `safetyinspectid` DESC"));
                if ((!empty($vehicle_status['begin_odo_kms']) && !empty($vehicle_status['final_odo_kms'])) || empty($vehicle_status)) {
                    $vehicle_status = 'Done';
                } else {
                    $vehicle_status = 'Not Done';
                }
        	?>
        		<input type="hidden" id="drivinglogid"	name="drivinglogid" value="<?php echo $drivinglogid ?>" />
        		<input type="hidden" id="validate_start" name="validate_start" value="1" />
        		<input type="hidden" id="last_timer_value" value="<?php echo $time_seconds ?>">
                <input type="hidden" id="active_status" value="<?php echo $timer_name[0] ?>">
                <input type="hidden" id="vehicle_status" value="<?php echo $vehicle_status ?>">
        	<?php } else { ?>
        		<input type="hidden" id="validate_start" name="validate_start" value="0" />
        	<?php } ?>

            <?php if(empty($_GET['drivinglogid'])) {
                include ('add_driving_log_new.php');
            } else {
                include ('add_driving_log_current.php');
            } ?>

        </form>
    </div>
</div>

<?php include ('../footer.php'); ?>