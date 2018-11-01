<?php
/*
EIS
*/
include ('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}

?>

<div class="standard-body-title hide-titles-mob">
    <h3 class="pull-left">Check In</h3>
    <div class="pull-right">
        <img src="../img/icons/ROOK-3dot-icon.png" class="no-toggle cursor-hand offset-top-15 double-gap-right" title="" width="25" data-original-title="Show/Hide Check In" onclick="view_checkin()"> </div>
    <div class="clearfix"></div>
</div>
 <form name="form_sites" method="post" action="" class="form-inline view_checkin hidden" role="form">
        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Check In displays all appointments for the current day (in the Today sub tab) as well as by Staff (in staff name sub tabs), and gives the user the ability to check a customer in (by clicking Check In in the <?= $purchaser_label ?> row). This will change the symbol on the calendar to Checked In. If an appointment is highlighted red, the customer is a Late Cancellation or a No-Show. If an appointment is highlighted yellow, the customer is Late.</div>
            <div class="clearfix"></div>
        </div>
        
        <div class="row ">
            <label class="col-sm-3 control-label">Search By Any:</label>
            <div class="col-sm-6">
                <?php if(isset($_POST['search_email_submit'])) { ?>
                    <input type="text" name="search_email" value="<?php echo $_POST['search_email']?>" class="form-control">
                <?php } else { ?>
                    <input type="text" name="search_email" class="form-control">
                <?php } ?>
            </div>
            <div class="col-sm-3 text-right">
                <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn">Search</button>
                <button type="submit" name="display_all_email" value="Display All" class="btn brand-btn">Display All</button>
            </div>
        </div>
    </form>

    <div class="pull-right double-gap-top view_checkin hidden"><img src="../img/red.png" width="32" height="32" border="0" alt=""> Cancelled/No-Show &nbsp;&nbsp;&nbsp;<img src="../img/yellow.png" width="32" height="32" border="0" alt="">&nbsp;&nbsp;Late</div>
    <div class="clearfix"></div>

    <div class="table-responsive"><?php
        $contactid = $_GET['contactid'];
        $class = '';
        if('0' == $_GET['contactid']) {
            $class= 'active_tab';
        }
        echo '<span class="popover-examples list-inline view_checkin hidden">
            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Click here to see all appointments for the current day."><img src="'.WEBSITE_URL.'/img/info.png" width="20"></a>
        </span>';
        echo '<a href="checkin.php?contactid=0" class="view_checkin hidden"><button type="button" class="btn brand-btn mobile-block '.$class.'" >Today</button></a>&nbsp;&nbsp;';
        echo "</div>";
        $tabs = mysqli_query($dbc, "SELECT distinct(therapistsid) FROM booking WHERE deleted=0 AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) = DATE(NOW()) AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND appoint_time IS NULL ORDER BY therapistsid");
        while($row_tab = mysqli_fetch_array( $tabs )) {
            $class='';
            $therapistsid = $row_tab['therapistsid'];
            if($therapistsid == $_GET['contactid']) {
                $class= 'active_tab';
            }
            echo '<a href="checkin.php?contactid='.$therapistsid.'"><button type="button" class="btn brand-btn mobile-block '.$class.'" >'.get_contact($dbc, $therapistsid).'</button></a>&nbsp;&nbsp;';
        }
        
        $email = '';
        if (isset($_POST['search_email_submit'])) {
            $email = $_POST['search_email'];
        }
        if (isset($_POST['display_all_email'])) {
            $email = '';
        }

        if($email != '') {
            $query_check_credentials = "SELECT * FROM booking WHERE therapistsid='$contactid' AND (p.first_name LIKE '%" . $email . "%' OR  p.last_name LIKE '%" . $email . "%' OR s.first_name LIKE '%" . $email . "%' OR  s.last_name LIKE '%" . $email . "%' OR  b.today_date LIKE '%" . $email . "%' OR  b.treatment_type LIKE '%" . $email . "%' OR  b.appoint_date LIKE '%" . $email . "%' OR b.follow_up_call_date  LIKE '%" . $email . "%') AND b.appoint_time IS NULL ORDER BY bookingid DESC";
        } else {
            if($contactid == 0) {
                $query_check_credentials = "SELECT * FROM booking WHERE (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) = DATE(NOW()) AND type != 'I' AND type != 'E' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND appoint_time IS NULL ORDER BY therapistsid,appoint_date";
            } else {
                //echo $query_check_credentials = "SELECT * FROM booking WHERE therapistsid='$contactid' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) = DATE(NOW()) AND patientid != 0 AND follow_up_call_status != 'Cancelled' AND follow_up_call_status != 'Rescheduled' ORDER BY (str_to_date(substr(appoint_date,12,19),'%G-%i-%s'))";

                //$query_check_credentials = "SELECT * FROM booking WHERE therapistsid='$contactid' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) = DATE(NOW()) AND patientid != 0 AND follow_up_call_status != 'Cancelled' AND follow_up_call_status != 'Rescheduled' AND appoint_time IS NULL ORDER BY appoint_date";

                $query_check_credentials = "SELECT * FROM booking WHERE therapistsid='$contactid' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) = DATE(NOW()) AND type != 'I' AND type != 'P' AND type != 'Q' AND type != 'R' AND type != '' AND appoint_time IS NULL ORDER BY appoint_date";
            }

            //$query_check_credentials = "SELECT * FROM booking  WHERE therapistsid='$contactid' AND (str_to_date(substr(appoint_date,1,10),'%Y-%m-%d')) = DATE(NOW()) AND appoint_time IS NULL ORDER BY appoint_date";
        }

        $result = mysqli_query($dbc, $query_check_credentials);

        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
        } else{
            echo "<h3>No Record Found.</h3>";
        }
        $status_loop = '';
        while($row = mysqli_fetch_array( $result ))
        {
            if($row['therapistsid'] != $status_loop) {
                echo "<table border='2' cellpadding='10' class='table'>";
                echo '<tr>
                <th>Booking ID</th>
                <th>Booking Date</th>
                <th>'.$purchaser_label.'</th>
                <th>Injury</th>
                <th>Document</th>
                <th>Therapist</th>
                <th>Appointment Date & Time</th>
                <th>Status</th>
                <th>Check In</th>
                ';
                echo "</tr>";

                //echo '<h3>' . $users['first_name']. ' '. $users['last_name'] . '</h3>';
                echo '<h3>' . get_contact($dbc, $row['therapistsid']) . '</h3>';
                $status_loop = $row['therapistsid'];
            }

            $back = '';
            if ($row['appoint_date'] < date('Y-m-d H:i:s')) {
                $back = 'style="background-color: #FCE47D;"';
            }
            if ($row['follow_up_call_status'] == 'Late Cancellation / No-Show' || $row['follow_up_call_status'] == 'Cancelled') {
                $back = 'style="background-color: rgba(255,0,0,0.4);"';
            }

            echo '<tr '.$back.'>';
            echo '<td>' . $row['bookingid'] . '</td>';
            echo '<td>' . $row['today_date'] . '</td>';

            echo '<td><a href="../Contacts/add_contacts.php?category=Patient&contactid='.$row['patientid'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">'.get_contact($dbc, $row['patientid']). '</a></td>';

            //<a href="#"  onclick=" window.open(\''.WEBSITE_URL.'/Contact/add_contact.php?contactid='.$row['patientid'].'\', \'newwindow\', \'width=900, height=900\'); return false;">'.get_contact($dbc, $row['patientid']). '</a>
            echo '<td>' . get_all_from_injury($dbc, $row['injuryid'], 'injury_name').' : '.get_all_from_injury($dbc, $row['injuryid'], 'injury_type') . '</td>';
            echo '<td>';
            if($row['upload_document'] != '') {
                $file_names = explode('#$#', $row['upload_document']);
                $file_names_md5 = explode('#*FFM*#', $row['upload_document_md5']);
                echo '<ul>';
                $i=0;
                foreach($file_names as $file_name) {
                    if($file_name != '') {
                        $md5 = md5_file("Download/".$file_name);
                        if($md5 == $file_names_md5[$i]) {
                            echo '<li><a href="Download/'.$file_name.'" target="_blank">'.$file_name.'</a></li>';
                        } else {
                            echo '<li>'.$file_name.' (Error : File Change)</li>';
                        }
                    }
                    $i++;
                }
                echo '</ul>';
            } else {
                echo '-';
            }
            echo '</td>';

            echo '<td>' . get_contact($dbc, $row['therapistsid']) . '</td>';
            echo '<td>' . $row['appoint_date'] . '</td>';

            $bookingid = $row['bookingid'];
            $result_comment = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT count(commentid) AS total_comment FROM comment WHERE fromid='$bookingid' AND from_page='booking'"));

            $comment = $result_comment['total_comment'];

            //echo '<td data-title="Comments"><a href="comment.php?from=booking&fromid='.$bookingid.'">'.$comment.'</a></td>';

            if($row['type'] != 'I' && $row['type'] != 'E' && $row['type'] != 'P' && $row['type'] != 'Q' && $row['type'] != 'R' && $row['type'] != '') {
                echo '<td>' . $row['follow_up_call_status'] . '</td>';
            } else if($row['type'] == 'I') {
                echo '<td>On Holiday</td>';
            } else if($row['type'] == 'E') {
                echo '<td>On Break</td>';
            } else if($row['type'] == 'P') {
                echo '<td></td>';
            } else if($row['type'] == 'Q') {
                echo '<td>No Book Days</td>';
            } else if($row['type'] == 'R') {
                echo '<td>On Vacation</td>';
            } else {
                echo '<td></td>';
            }

            echo '<td>';
            echo '<a href=\'add_checkin_patient.php?bookingid='.$row['bookingid'].'\'>Check In</a>';
            echo '</td>';

            echo "</tr>";
        }

        echo '</table>'; ?>

</div><!-- .standard-body-content -->
<script type="text/javascript">
    function view_checkin() {
        $('.view_checkin').toggleClass('hidden');
    }
</script>