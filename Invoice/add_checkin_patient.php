<?php
/*
Add Service Code
*/

include ('../include.php');
error_reporting(0);
checkAuthorised('check_in');

if (isset($_POST['submit'])) {
    $bookingid = $_POST['bookingid'];

    $today_date = date('Y-m-d');
    $current_time = date('H:i:s');

    $notes = filter_var($_POST['notes'],FILTER_SANITIZE_STRING);

    $next_appointment = 'No';

    if($_FILES["upload_document"]["name"] != '') {
        $upload_document = implode('#$#', $_FILES["upload_document"]["name"]);
    } else {
        $upload_document = '';
    }

    $upload_document_md5 = '';
    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "Download/".$_FILES["upload_document"]["name"][$i]) ;
        $upload_document_md5 .= md5_file("Download/".$_FILES["upload_document"]["name"][$i]).'#*FFM*#';
    }

    if($upload_document != '') {
        $ud = '#$#'.$upload_document;
        $ud_md5 = '#*FFM*#'.$upload_document_md5;
    }

    $appoint_date = get_patient_from_booking($dbc, $bookingid, 'appoint_date');
    $service_date = explode(' ', $appoint_date);
    $final_service_date = $service_date[0];

    $follow_up_call_status = 'Arrived';
	$query_update_es = "UPDATE `booking` SET `follow_up_call_status` = '$follow_up_call_status', `appoint_time` = '$current_time', `notes` = '$notes', `upload_document` = concat(upload_document, '$ud'), `upload_document_md5` = concat(upload_document_md5, '$ud_md5') WHERE `bookingid` = '$bookingid'";
	$result_update_es = mysqli_query($dbc, $query_update_es);

    $calid = get_calid_from_bookingid($dbc, $bookingid);
    $query_update_cal = "UPDATE `mrbs_entry` SET `patientstatus` = '$follow_up_call_status' WHERE `id` = '$calid'";
    $result_update_cal = mysqli_query($dbc, $query_update_cal);

    $book_inid = 0;

    $query_insert_invoice = "INSERT INTO invoice (`invoice_type`, `bookingid`, `injuryid`, `service_date`, `invoice_date`, `patientid`, `therapistsid`)
    SELECT  'Saved',
			$bookingid,
            injuryid,
            '$final_service_date',
            '$today_date',
            patientid,
            therapistsid
    from booking WHERE bookingid = '$bookingid'";
    $result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);

    /*
    $get_patient = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(followupcallid) AS total_patient FROM follow_up_calls WHERE patientid IN(SELECT patientid FROM booking WHERE bookingid='$bookingid')"));
    if($get_patient['total_patient'] == 0) {
        $query_insert_booking = "INSERT INTO follow_up_calls (`patientid`, `therapistsid`, `follow_up_date`)
        SELECT  patientid,
                therapistsid,
                '$today_date'
        from booking WHERE bookingid = '$bookingid'";
        $result_insert_booking = mysqli_query($dbc, $query_insert_booking);
    } else {
	    $query_update_es = "UPDATE `follow_up_calls` SET `follow_up_date` = '$today_date' WHERE `patientid` IN(SELECT patientid FROM booking WHERE bookingid='$bookingid')";
	    $result_update_es = mysqli_query($dbc, $query_update_es);
    }
    */

    $patientid = get_patient_from_booking($dbc, $bookingid, 'patientid');
    $therapistsid = get_patient_from_booking($dbc, $bookingid, 'therapistsid');

    $new_patient = get_all_form_contact($dbc, $patientid, 'new_patient');

    if($new_patient == 1) {
        $query_insert_cal = "INSERT INTO `therapist_stat` (`today_date`, `therapistid`) VALUES ('$today_date', '$therapistsid')";
        $result_insert_cal = mysqli_query($dbc, $query_insert_cal);

	    $query_update_es = "UPDATE `contacts` SET `new_patient` = 0 WHERE `contactid`='$patientid'";
	    $result_update_es = mysqli_query($dbc, $query_update_es);
    }

    // Notify therapist on patient arrival/check-in
    $communication_check_in_way = get_config($dbc, 'communication_check_in_way');
    $appoint_date = explode(' ', get_patient_from_booking($dbc, $bookingid, 'appoint_date'));
    $end_appoint_date = explode(' ', get_patient_from_booking($dbc, $bookingid, 'end_appoint_date'));
    $patient_name = get_contact($dbc, $patientid);
    if($communication_check_in_way == 'Email') {
        $email = get_email($dbc, $therapistsid);
        if($email == '') {
            $email = 'cparil@calgaryphysicaltherapy.com';
        }
        $subject = $patient_name.' Arrived';
        $message = 'Please see below Patient information.<br><br>
                    Patient Name : '.$patient_name.'<br>
                    Appointment Date Time : '.$appoint_date[1].' - '.$end_appoint_date[1].'<br>
                    Notes : '.$_POST['notes'];
        //send_email('', $email, '', '', $subject, $message, '');
    }

    $from_patient = $_POST['from_patient'];
    if($from_patient == 0) {
        echo '<script type="text/javascript"> window.location.replace("checkin.php?contactid='.$therapistsid.'"); </script>';
    } else {
        echo '<script type="text/javascript"> window.top.close(); window.opener.location.reload(); </script>';
    }

    mysqli_close($dbc); //Close the DB Connection
}

?>
<script type="text/javascript">
    $(document).ready(function(){
        $(".next_appointment_fields").hide();

        $(".next_appointment").change(function(){
                if($(this).val() == 'Yes') {
                    $(".next_appointment_fields").show();
                } else {
                    $(".next_appointment_fields").hide();
                }
        });
    });
</script>
</head>

<body>
<?php include_once ('../navigation.php');

?>
<div class="container">
  <div class="row">

        <h1 class="triple-pad-bottom">Check in Patient</h1>

		<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        <?php
        $from = 0;
        if(!empty($_GET['from'])) {
            $from = 1;
        }

        echo '<input type="hidden" name="from_patient" value="'.$from.'" />';

        $today_date = date('Y-m-d');
        $current_time = date('H:i:s');

        $bookingid = $_GET['bookingid'];
        $get_site = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM booking WHERE bookingid='$bookingid'"));

        $patientid = $get_site['patientid'];
        $treatment_type = $get_site['treatment_type'];
        $upload_document = $get_site['upload_document'];
        $upload_document_md5 = $get_site['upload_document_md5'];
        $therapistsid = $get_site['therapistsid'];
        $appoint_date = explode(' ', $get_site['appoint_date']);
        $end_appoint_date = explode(' ', $get_site['end_appoint_date']);
        $notes = $get_site['notes'];
        $follow_up_call_date = $get_site['follow_up_call_date'];
        ?>

        <input type="hidden" id="bookingid" name="bookingid" value="<?php echo $bookingid ?>" />

          <div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Date Time:</label>
            <div class="col-sm-8">
                <?php echo $today_date.' '.$current_time; ?>
            </div>
          </div>

          <div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Appointment Date Time:</label>
            <div class="col-sm-8">
                <?php echo $appoint_date[1].' - '.$end_appoint_date[1]; ?>
            </div>
          </div>

          <div class="form-group patient">
            <label for="site_name" class="col-sm-4 control-label">Patient:</label>
            <div class="col-sm-8">
                <?php
                echo get_contact($dbc, $patientid);
                ?>
            </div>
          </div>

          <div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Therapist:</label>
            <div class="col-sm-8">
                <?php
                $therapists = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE contactid='$therapistsid'"));
                echo decryptIt($therapists['first_name']).' '.decryptIt($therapists['last_name']);
                ?>
            </div>
          </div>

          <div class="form-group">
            <label for="file[]" class="col-sm-4 control-label">Upload Document:</label>
            <div class="col-sm-8">
            <?php if((!empty($_GET['bookingid'])) && ($upload_document != '')) {
                    $file_names = explode('#$#', $upload_document);
                    $file_names_md5 = explode('#*FFM*#', $upload_document_md5);
                    echo '<ul>';
                    $i=0;
                    foreach($file_names as $file_name) {
                        $md5 = md5_file("Download/".$file_name);
                        if(($file_name != '') && ($md5 == $file_names_md5[$i])) {
                            echo '<li><a href="Download/'.$file_name.'" target="_blank">'.$file_name.'</a></li>';
                        } else if($md5 != $file_names_md5[$i]) {
                            echo '<li>'.$file_name.' (Error : File Change)</li>';
                        }
                        $i++;
                    }
                    echo '</ul>';
                ?>
                <input type="hidden" name="upload_document" value="<?php echo $upload_document; ?>" />
                <input multiple name="upload_document[]" type="file" id="file" data-filename-placement="inside" class="form-control" />
              <?php } else { ?>
              <input multiple name="upload_document[]" type="file" id="file" data-filename-placement="inside" class="form-control" />
              <?php } ?>

            </div>
          </div>


            <!-- <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">Next Appointment<span class="hp-red">*</span>:</label>
                <div class="col-sm-8">
                  <input required name="next_appointment" type="radio" value="Yes" class="form next_appointment" /> Yes
                  <input required name="next_appointment" checked type="radio" value="No" class="form next_appointment" /> No
                </div>
            </div>
            -->

          <div class="form-group">
            <label for="first_name[]" class="col-sm-4 control-label">Notes:</label>
            <div class="col-sm-8">
              <textarea name="notes" rows="5" cols="50" class="form-control"><?php echo $notes; ?></textarea>
            </div>
          </div>

             <div class="form-group">
                <div class="col-sm-4">
                    <p><span class="empire-red pull-right"><em>Required Fields *</em></span></p>
                </div>
                <div class="col-sm-8"></div>
            </div>

          <div class="form-group">
            <div class="col-sm-4 clearfix">
                <a href="checkin.php?contactid=<?php echo $therapistsid;?>" class="btn brand-btn pull-right">Back</a>
            </div>
            <div class="col-sm-8">
                <button type="submit" name="submit" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
            </div>
          </div>

        </form>
    </div>
  </div>

<?php include ('../footer.php'); ?>