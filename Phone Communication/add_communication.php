<?php
/*
Add Vendor
*/
include ('../include.php');
error_reporting(0);
$back_url = 'phone_communication.php?type=Internal';
if(!empty($_GET['from_url'])) {
	$back_url = urldecode($_GET['from_url']);
}
else if($communication_type == 'Internal') {
	$back_url = 'phone_communication.php?type=Internal';
}
else if($communication_type == 'External') {
	$back_url = 'phone_communication.php?type=External';
}

$fax_method = get_config($dbc, 'fax_service');
$communication_type = $_GET['type'];
$from = isset($_GET['projectid']) ? 'project' : '';
$projectid = isset($_GET['projectid']) ? filter_var($_GET['projectid'], FILTER_SANITIZE_STRING) : '';

if (isset($_POST['submit'])) {
	$communication_type = $_POST['comm_type'];
    $businessid = $_POST['businessid'];
	$contactid = $_POST['contactid'];
 	$projectid = $_POST['projectid'];
	$status = $_POST['status'];
	$doc = $_POST['doc'];
	$comments = $_POST['comments'];
	$client_projectid = '';
	if(substr($projectid,0,1) == 'C') {
		$client_projectid = substr($projectid,1);
		$projectid = '';
	}

    $comment = filter_var($_POST['comment'],FILTER_SANITIZE_STRING);
    

	$follow_up_date = $_POST['followup_date'];
	$follow_up_by = $_POST['followup_by'];

	$today_date = date('Y-m-d');
    $created_by = $_SESSION['contactid'];

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
	if($communication_type == 'Fax') {
		if($fax_method == '') {
			echo "<script>alert('No Fax Service has been Configured');</script>";
		} else {
			$file = file_safe_str($_FILES['fax_file']['file_name']);
			move_uploaded_file($_FILES['fax_file']['tmp_name'],'download/'.$file);
			$manual = $_POST['manual_fax'];
			$recipient = $manual;
			if(empty($recipient)) {
				$recipient = get_contact($dbc,$contactid,'fax');
			}
			if(empty($recipient)) {
				$recipient = get_contact($dbc,$businessid,'fax');
			}
			if(!empty($recipient)) {
				$dbc->query("INSERT INTO `phone_communication` (`businessid`,`contactid`,`manual`,`projectid`,`doc`,`comment`,`file`,`created_by`,`follow_up_date`,`follow_up_by`,`communication_type`) VALUES ('$businessid','$contactid','$manual','$projectid','$today_date','$comments','download/$file','$created_by','$follow_up_date','$follow_up_by','Fax')");
				send_email('',$recipient.$fax_method,'','',$_POST['subject'],$comments,'download/'.$file);
			} else {
				echo "<script>alert('No Fax Number Provided');</script>";
			}
		}
	} else {
		$meeting_phone_send = '';

		$send_body = '';
		if($meeting_phone_send != '' || $meeting_cc_phone_send != '') {
			if($businessid != '') {
				$send_body .= '<b>Business : </b>'.get_client($dbc, $businessid).'<br>';
			}
			if($contactid != '') {
				$send_body .= '<b>Contact : </b>'.get_staff($dbc, $contactid).'<br>';
			}
			if($projectid != '') {
				$project_tabs = get_config($dbc, 'project_tabs');
				if($project_tabs == '') {
					$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
				}
				$project_tabs = explode(',',$project_tabs);
				foreach($project_tabs as $item) {
					if(preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item))) == get_project($dbc, $projectid, 'projecttype')) {
						$send_body .= '<b>Project : </b>'.$item.' : '.get_project($dbc, $projectid, 'project_name').'<br><br>';
					}
				}
			}
			if($phone_body != '') {
				$send_body .= $_POST['phone_body'];
			}

			$send_body .=  '<br><br><a target="_blank" href="'.$_SERVER['SERVER_NAME'].'/Phone Communication/add_communication.php?type='.$communication_type.'&phone_communicationid='.$phone_communicationid.'">Click Here</a><br>';

			if($meeting_attachment == '') {
				send_phone('', $meeting_arr_phone, $meeting_cc_arr_phone , '', $subject, $send_body, '');
			} else {
				send_phone('', $meeting_arr_phone, $meeting_cc_arr_phone , '', $subject, $send_body, $meeting_attachment);
			}
		}
		// Meeting Note Phone
		if(empty($_POST['phone_communicationid']) || count($meeting_arr_phone) > 0 || count($meeting_cc_arr_phone) > 0) {
			$query_insert_ca = "INSERT INTO `phone_communication` 
			(`communication_type`, `businessid`, `contactid`, `projectid`, `client_projectid`, `comment`, `doc`,`created_by`, `follow_up_by`, `follow_up_date`) 
			VALUES ('$communication_type', '$businessid', '$contactid', '$projectid', '$client_projectid', '$comments',  '$doc', '$created_by', '$follow_up_by', '$follow_up_date')";
			$result_insert_ca = mysqli_query($dbc, $query_insert_ca);
			$phone_communicationid = mysqli_insert_id($dbc);

			$overview = 'Added Phone Communication #'.$phone_communicationid;
			if(!empty($_POST['timer']) && $_POST['timer'] != '') {
				$overview .= ' - Added Time : '.$_POST['timer'];
			}
			
			if($projectid > 0) {
				$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
				mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', 'Made Phone Call #$phone_communicationid', '$projectid')");
			}
		} else {
			$phone_communicationid = $_POST['phone_communicationid'];
			$query_update_ticket = "UPDATE `phone_communication` SET `communication_type` = '$communication_type', `businessid` = '$businessid', `contactid` = '$contactid', `projectid` = '$projectid', `client_projectid` = '$client_projectid', `comment` = '$comments', `doc`='$doc', `to_contact` = '$to_contact', `follow_up_by` = '$follow_up_by', `follow_up_date` = '$follow_up_date' WHERE `phone_communicationid` = '$phone_communicationid'";
			$result_update_ticket = mysqli_query($dbc, $query_update_ticket);

			$overview = 'Updated Phone Communication #'.$phone_communicationid;
			if(!empty($_POST['timer']) && $_POST['timer'] != '') {
				$overview .= ' - Added Time : '.$_POST['timer'];
			}
		}
		echo insert_day_overview($dbc, $created_by, 'Communication', date('Y-m-d'), '', $overview);

		$timer = $_POST['timer'];
		$end_time = date('g:i A');

		$start_time = 0;
		if($timer != '0' && $timer != '00:00:00' && $timer != '') {	
			$query_update_ticket = "UPDATE `phone_communication_timer` SET `end_time` = '$end_time', `start_timer_time` = '$start_time', `timer`='$timer', `communication_id` = '$phone_communicationid' WHERE `communication_id` = '$phone_communicationid' AND created_by='$created_by' AND end_time IS NULL";
			$result_update_ticket = mysqli_query($dbc, $query_update_ticket);

			$query_update_ticket = "UPDATE `phone_communication_timer` SET `start_timer_time` = '0', `communication_id`='$phone_communicationid' WHERE (`communication_id` = '$phone_communicationid' OR `communication_id` = 0 OR `communication_id` IS NULL) AND `created_by`='$created_by'";
			$result_update_ticket = mysqli_query($dbc, $query_update_ticket);
		}
	}

    echo '<script type="text/javascript"> window.location.replace("'.$back_url.'"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {

    $("#businessid").change(function() {
        var comm_type = $("#comm_type").val();
		window.location = 'add_communication.php?type='+comm_type+'&bid='+this.value;
	});
    $("#form1").submit(function( event ) {
        var phone_communicationid = $("#phone_communicationid").val();
        if(phone_communicationid == undefined) {
            var businessid = $("#businessid").val();
            var serviceid = $("#serviceid").val();
            var service_type = $("#service_type").val();
            var service_category = $("#service_category").val();

            var heading = $("input[name=heading]").val();

            var status = $("#status").val();
            var contactid = $("#contactid").val();
            var category = $("#category").val();

            if (businessid == '' || serviceid == '' || service_type == '' || service_category == '' || heading == '' || status == '' || category == '' || contactid == null) {
                alert("Please make sure you have filled in all of the required fields.");
                return false;
            }
        }
    });

});
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('phone_communication'); ?>
<div class="container">
  <div class="row">
    <h3 class="gap-left pull-left">Phone</h3>
    <?php if ( $from == 'project' ) { ?>
        <div class="pull-right offset-top-15"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img" /></a></div>
    <?php } ?>
    <div class="clearfix"></div>
	<?php if ( $from != 'project' ) { ?>
        <div class="pad-left gap-top double-gap-bottom"><a href="<?php echo $back_url; ?>" class="btn config-btn">Back to Dashboard</a></div>
    <?php } ?>

	<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
		<?php if($communication_type == 'Fax') {
			$value_config = ','.get_config($dbc,'fax_communication').',';
			include('fax_add.php');
		} else { ?>
			<h1>Phone Communication</h1>
			<div class="pad-left gap-top double-gap-bottom"><a href="<?php echo $back_url; ?>" class="btn config-btn">Back to Dashboard</a></div>

			<?php if(!empty($_GET['type'])) {
				echo '<input type="hidden" id="comm_type" name="comm_type" value="'.$communication_type.'" />';
			}
			
			if ( $communication_type=='Internal' ) {
				$get_field_config	= mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `internal_communication` FROM `field_config`"));
				$value_config		= ',' . $get_field_config['internal_communication'] . ',';
			} else {
				$get_field_config	= mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `external_communication` FROM `field_config`"));
				$value_config		= ',' . $get_field_config['external_communication'] . ',';
			}

			$clientid = '';
			$businessid = '';

			if(!empty($_GET['bid'])) {
				$businessid = $_GET['bid'];
			}

			/*
			if(!empty($_GET['clientid'])) {
				$clientid = $_GET['clientid'];
				$businessid = get_contact($dbc, $clientid, 'businessid');
			}
			*/
			if(!empty($_GET['projectid'])) {
				$projectid = $_GET['projectid'];
				$businessid = get_project($dbc, $projectid, 'businessid');
				$clientid = get_project($dbc, $projectid, 'clientid');
			}

			$followup_by = '';
			$followup_date = '';
			$doc = '';
			$contactid = $_SESSION['contactid'];
			if(!empty($_GET['phone_communicationid'])) {

				$phone_communicationid = $_GET['phone_communicationid'];
				$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM phone_communication WHERE phone_communicationid='$phone_communicationid'"));

				$businessid = $get_ticket['businessid'];
                $projectid = $get_ticket['projectid'];
                $followup_by = $get_ticket['follow_up_by'];
                $doc = $get_ticket['doc'];
                $followup_date = $get_ticket['follow_up_date'];

                $comments = $get_ticket['comment'];
            ?>
            <input type="hidden" id="phone_communicationid" name="phone_communicationid" value="<?php echo $phone_communicationid ?>" />
            <?php   }      ?>

            <div class="panel-group <?= ($from == 'project' ? 'block-panels main-screen' : '') ?>" id="accordion2" <?= $from == 'project' ? 'style="background-color: #fff; padding: 0; margin-left: 0.5em; width: calc(100% - 1em);"' : '' ?>>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info" >
                                Information<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_info" class="panel-collapse collapse in">
                        <div class="panel-body">
                            <?php
                                include ('add_business_info.php');
                            ?>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_td" >
                                Phone Communication Details<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_td" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php
                                include ('add_phone_communication_info.php');
                            ?>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_followup" >
                                Reminder<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_followup" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php
                                include ('add_phone_communication_follow_up.php');
                            ?>
                        </div>
                    </div>
                </div>

            </div>

            <?php if ( $from == 'project' ) { ?>
                <div class="form-group">
                    <button type="submit" name="submit" value="submit" class="btn brand-btn pull-right">Submit</button>
                    <a href="" class="btn brand-btn pull-right">Cancel</a>
                </div>
            <?php } else { ?>
                <div class="form-group">
                    <div class="col-sm-6">
                        <a href="<?php echo $back_url; ?>" class="btn brand-btn btn-lg">Back</a>
                    </div>
                    <div class="col-sm-6">
                        <button type="submit" name="submit" value="submit" class="btn brand-btn btn-lg pull-right">Submit</button>
                    </div>
                </div>
            <?php } ?>

            <style>
                .chosen-container {
                    width:100%;
                }
            </style>
		<?php } ?>
	</form>
  </div>
</div>
<?php include ('../footer.php'); ?>