<?php
/*
 * Add New Internal/External Email
 * Called From: index.php, dashboard_email.php
 */
include ('../include.php');
error_reporting(0);
$communication_type = empty($_POST['comm_type']) ? (empty($_GET['type']) ? 'Internal' : ucfirst(filter_var($_GET['type'], FILTER_SANITIZE_STRING))) : $_POST['comm_type'];
$back_url = '../blank_loading_page.php';

if (isset($_POST['submit'])) {
    $businessid = $_POST['businessid'];
	$contactid = $_POST['contactid'];
 	$projectid = $_POST['projectid'];
 	$ticketid = $_POST['ticketid'];
	$client_projectid = '';
	if(substr($projectid,0,1) == 'C') {
		$client_projectid = substr($projectid,1);
		$projectid = '';
	}
    $salesid = filter_var($_POST['salesid'],FILTER_SANITIZE_STRING);

    $email_body = htmlentities($_POST['email_body']);
    $subject = htmlentities($_POST['subject']);
    $from_name = filter_var(empty($_POST['from_name']) ? get_config($dbc, 'company_name') : $_POST['from_name'],FILTER_SANITIZE_STRING);
    $from_email = filter_var(empty($_POST['from_email']) ? get_config($_SERVER['DBC'], 'main_email_user') : $_POST['from_email'],FILTER_SANITIZE_STRING);
    $to_contact = implode(',',$_POST['businesscontact_to_emailid']);
    $cc_contact = implode(',',$_POST['businesscontact_cc_emailid']);
    $to_staff = implode(',',$_POST['companycontact_to_emailid']);
    $cc_staff = implode(',',$_POST['companycontact_cc_emailid']);
    $new_emailid = filter_var($_POST['new_emailid'],FILTER_SANITIZE_STRING);
	$follow_up_date = $_POST['followup_date'];
	$follow_up_by = $_POST['followup_by'];
	$today_date = date('Y-m-d h:i:s');
    $created_by = $_SESSION['contactid'];

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

    $meeting_attachment = '';
    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
        $document = htmlspecialchars($_FILES["upload_document"]["name"][$i], ENT_QUOTES);

        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]) ;

        if($document != '') {
            $query_insert_client_doc = "INSERT INTO `email_communicationid_upload` (`email_communicationid`, `document`, `created_by`, `created_date`) VALUES ('0', '$document', '$created_by', '$today_date')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);

            $file_support = 'download/'.$document;
            $meeting_attachment .= $file_support.'*#FFM#*';
        }
    }
    foreach(array_filter($_POST['email_file']) as $email_file) {
        $query_insert_client_doc = "INSERT INTO `email_communicationid_upload` (`email_communicationid`, `document`, `created_by`, `created_date`) VALUES ('0', '../$email_file', '$created_by', '$today_date')";
        $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
        $meeting_attachment .= $email_file.'*#FFM#*';
    }

    $meeting_email_send = '';
    // Meeting Note Email
    if($to_contact != '' || $to_staff != '') {
        $meeting_email_send = $to_contact.','.$to_staff;
    }

    $meeting_cc_email_send = '';
    if($cc_contact != '' || $cc_staff != '' || $new_emailid != '') {
        $meeting_cc_email_send = $cc_contact.','.$cc_staff.','.$new_emailid;
    }

    if($meeting_email_send != '') {
        $meeting_arr_email = array_filter(explode(",",$meeting_email_send));
    }

    if($meeting_cc_email_send != '') {
        $meeting_cc_arr_email = array_filter(explode(",",$meeting_cc_email_send));
    }

    $send_body = '';
    if($meeting_email_send != '' || $meeting_cc_email_send != '') {
        if($businessid != '') {
            $send_body .= '<b>Business : </b>'.get_client($dbc, $businessid).'<br>';
        }
        if($contactid != '') {
            $send_body .= '<b>Contact : </b>'.get_staff($dbc, $contactid).'<br>';
        }
        if($projectid != '') {
			$project_tabs = get_config($dbc, 'project_tabs');
			if($project_tabs == '') {
				$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly,';
			}
			$project_tabs = explode(',',$project_tabs);
			foreach($project_tabs as $item) {
				if(preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item))) == get_project($dbc, $projectid, 'projecttype')) {
					$send_body .= '<b>Project : </b>'.$item.' : '.get_project($dbc, $projectid, 'project_name').'<br><br>';
				}
			}
        }
        if($email_body != '') {
            $send_body .= $_POST['email_body'];
        }
		if($ticketid > 0 && $communication_type == 'External') {
			$ticket_options = $dbc->query("SELECT GROUP_CONCAT(`fields` SEPARATOR ',') `field_config` FROM (SELECT `value` `fields` FROM `general_configuration` LEFT JOIN `tickets` ON `general_configuration`.`name` LIKE CONCAT('ticket_fields_',`tickets`.`ticket_type`) WHERE `ticketid`='$ticketid' UNION SELECT `tickets` `fields` FROM `field_config`) `options`")->fetch_assoc();
			if(strpos(','.$ticket_options['field_config'].',',',External Response,') !== FALSE) {
				$send_body .= '<p><a href="'.WEBSITE_URL.'/external_response.php?r='.urlencode(encryptIt(json_encode(['ticketid'=>$ticketid]))).'" target="_blank">You can reply to this message by clicking here.</a></p>';
				// $email_body .= htmlentities('<p><a href="'.WEBSITE_URL.'/external_response.php?r='.urlencode(encryptIt(json_encode(['ticketid'=>$ticketid]))).'" target="_blank">You can reply to this message by clicking here.</a></p>');
			}
		} else if($ticketid > 0 && $communication_type == 'Internal') {
            $send_body .= '<p><a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$ticketid.'" target="_blank">Click here</a> to view the '.TICKET_NOUN.'</p>';
            // $email_body .= htmlentities('<p><a href="'.WEBSITE_URL.'/external_response.php?r='.encryptIt(json_encode(['ticketid'=>$ticketid])).'" target="_blank">Click here</a> to view the '.TICKET_NOUN.'</p>');
		}

    if($_POST['submit'] == 'draft') {
      /*$email_subject = $_POST['subject'];
      $meeting_email = implode(",", $meeting_arr_email);
      $meeting_cc_email = implode(",", $meeting_cc_arr_email);
      $query_insert_email_comm = "INSERT INTO `email_communication_draft` (`from_email`, `from_name`, `to_emails`, `cc_emails`, `subject`, `send_body`,`meeting_attachment`)
      VALUES ('$from_email', '$from_name', '$meeting_email', '$meeting_cc_email', '$email_subject', '$send_body',  '$send_body')";
      $result_insert_email_comm = mysqli_query($dbc, $query_insert_email_comm);*/
    }
    else {
      if (empty($from_email)) {
          $from_email = '';
      }
      try {
        send_email([$from_email => $from_name], $meeting_arr_email, $meeting_cc_arr_email , '', $_POST['subject'], $send_body, $meeting_attachment);
        if($salesid > 0) {
            add_update_history($dbc, 'sales', "Sent Email to Lead: ".$_POST['subject'], '', '', $salesid);
        }
      } catch(Exception $e) {
        echo "<script> alert('Unable to send the email: ".$e->getMessage()."'); </script>";
      }
    }
    }
    // Meeting Note Email

    if(empty($_POST['email_communicationid']) || count($meeting_arr_email) > 0 || count($meeting_cc_arr_email) > 0) {
        if($_POST['submit'] == 'draft') {
          $draft = 1;
        }
        else {
          $draft = 0;
        }

        $query_insert_ca = 'INSERT INTO `email_communication` (`communication_type`, `businessid`, `contactid`, `salesid`, `projectid`, `ticketid`, `client_projectid`, `subject`, `email_body`, `to_contact`, `cc_contact`, `to_staff`, `cc_staff`, `new_emailid`, `today_date`, `created_by`, `follow_up_by`, `follow_up_date`, `from_email`, `from_name`,`draft`) VALUES ("'.$communication_type.'", "'.$businessid.'", "'.$contactid.'",  "'.$salesid.'", "'. $projectid.'", "'. $ticketid.'", "'. $client_projectid.'",  "'.$subject.'",  "'.$email_body.'", "'.$to_contact.'", "'.$cc_contact.'", "'.$to_staff.'", "'.$cc_staff.'", "'.$new_emailid.'", "'.$today_date.'", "'.$created_by.'", "'.$follow_up_by.'", "'.$follow_up_date.'", "'.$from_email.'", "'.$from_name.'", "'.$draft.'")';

        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
        $email_communicationid = mysqli_insert_id($dbc);

		$overview = 'Added Email Communication #'.$email_communicationid;
		if(!empty($_POST['timer']) && $_POST['timer'] != '') {
			$overview .= ' - Added Time : '.$_POST['timer'];
		}
		if($projectid > 0) {
			$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
			mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', 'Sent Email #".$email_communicationid."', '$projectid')");
		}
    } else {
        $email_communicationid = $_POST['email_communicationid'];
        $query_update_ticket = 'UPDATE `email_communication` SET `communication_type` = "'.$communication_type.'", `businessid` = "'.$businessid.'", `contactid` = "'.$contactid.'", `salesid` = "'.$salesid.'", `projectid` = "'.$projectid.'", `ticketid` = "'.$ticketid.'", `client_projectid` = "'.$client_projectid.'", `subject` = "'.$subject.'", `email_body` = "'.$email_body.'", `to_contact` = "'.$to_contact.'", `cc_contact` = "'.$cc_contact.'", `to_staff` = "'.$to_staff.'", `cc_staff` = "'.$cc_staff.'", `new_emailid` = "'.$new_emailid.'", `follow_up_by` = "'.$follow_up_by.'", `follow_up_date` = "'.$follow_up_date.'", `from_email` = "'.$from_email.'", `from_name` = "'.$from_name.'" WHERE `email_communicationid` = "'.$email_communicationid.'"';
        $result_update_ticket = mysqli_query($dbc, $query_update_ticket);

		$overview = 'Updated Email Communication #'.$email_communicationid;
		if(!empty($_POST['timer']) && $_POST['timer'] != '') {
			$overview .= ' - Added Time : '.$_POST['timer'];
		}
    }

    if ( !empty($follow_up_by) && !empty($follow_up_date) ) {
        mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `subject`, `src_table`, `src_tableid` ) VALUES ('$follow_up_by', '$follow_up_date', '$subject', 'email_communication', '$email_communicationid')");
    }

	//Connect the attachments to the current communication
	mysqli_query($dbc, "UPDATE `email_communicationid_upload` SET `email_communicationid`='$email_communicationid' WHERE `email_communicationid`=0 AND `created_by`='$created_by' AND `created_date`='$today_date'");
	echo insert_day_overview($dbc, $created_by, 'Communication', date('Y-m-d'), '', $overview);

	$timer = $_POST['timer'];
	$end_time = date('g:i A');

	$start_time = 0;
	if($timer != '0' && $timer != '00:00:00' && $timer != '') {
		$query_update_ticket = "UPDATE `email_communication_timer` SET `end_time` = '$end_time', `start_timer_time` = '$start_time', `timer`='$timer', `communication_id` = '$email_communicationid' WHERE `communication_id` = '$email_communicationid' AND created_by='$created_by' AND end_time IS NULL";
		$result_update_ticket = mysqli_query($dbc, $query_update_ticket);

		$query_update_ticket = "UPDATE `email_communication_timer` SET `start_timer_time` = '0', `communication_id`='$email_communicationid' WHERE (`communication_id` = '$email_communicationid' OR `communication_id` = 0 OR `communication_id` IS NULL) AND `created_by`='$created_by'";
		$result_update_ticket = mysqli_query($dbc, $query_update_ticket);
	} ?>

	<script>
        try {
            $(window.top.document).find('iframe[src*=Ticket]').get(0).contentWindow.reloadCommunications();
        } catch(e) { }
        try {
            window.parent.reloadCommunications();
        } catch(e) { }
        window.location.replace("<?= $back_url ?>");
	</script><?php
} ?>
<script type="text/javascript">
    $(document).ready(function() {
        /* $("#businessid").change(function() {
            var comm_type = $("#comm_type").val();
            window.location = 'add_communication.php?type='+comm_type+'&bid='+this.value;
        }); */
        $("#form1").submit(function( event ) {
            var email_communicationid = $("#email_communicationid").val();
            if(email_communicationid == undefined) {
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

    function addStaff(button) {
        var block_class = $(button).parent().parent().parent().attr('class');
        var block = $('div.'+block_class).last();
        destroyInputs('.'+block_class);
        clone = block.clone();
        clone.find('.form-control').val('');
        block.after(clone);
        initInputs('.'+block_class);
    }
    function removeStaff(button) {
        var block_class = $(button).parent().parent().parent().attr('class');
        if($('div.'+block_class).length <= 1) {
            addStaff(button);
        }
        $(button).closest('div.'+block_class).remove();
        $('div.'+block_class).first().find('[name="companycontact_to_emailid[]"]').change();
    }
</script>
</head>

<body>
<?php
    include_once ('../navigation.php');
    checkAuthorised('email_communication');
?>
<div class="container">
    <div class="row">
        <h3 class="gap-left pull-left">Email</h3>
        <div class="pull-right offset-top-15"><?php
            if (!empty($_GET['email_communicationid'])) {
                $id = preg_replace('/[^0-9]/', '', $_GET['email_communicationid']);
            } else {
                $id = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT MAX(`email_communicationid`) + 1 `id` FROM `email_communication`"))['id'];
            } ?>
            <img src="../img/icons/ROOK-reminder-icon.png" alt="Add Reminder" title="Add Reminder" class="no-toggle cursor-hand" data-placement="bottom" width="25" onclick="overlayIFrameSlider('../quick_action_reminders.php?tile=email&id=<?= $id ?>', 'auto', false, true);" />
            <img src="../img/icons/ROOK-timer2-icon.png" alt="Track Time" title="Track Time" class="no-toggle cursor-hand offset-left-5 offset-right-5" data-placement="bottom" width="25" onclick="overlayIFrameSlider('../quick_action_timer.php?tile=email&id=<?= $id ?>', 'auto', false, true);" />
            <a href="../blank_loading_page.php"><img src="../img/icons/cancel.png" alt="Close" title="Close" class="no-toggle" data-placement="bottom" width="25" /></a>
        </div>
        <div class="clearfix"></div>

        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
        <?php
            if(!empty($_GET['category'])) {
                $comm_type = $_GET['category'];
                echo '<input type="hidden" id="comm_type" name="comm_type" value="'.$comm_type.'" />';
            } else if(!empty($_GET['type'])) {
                $comm_type = ucfirst(filter_var($_GET['type'], FILTER_SANITIZE_STRING));
                echo '<input type="hidden" id="comm_type" name="comm_type" value="'.$comm_type.'" />';
            }

            if ( $comm_type=='Internal' ) {
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

            if(!empty($_GET['cid'])) {
                $clientid = $_GET['cid'];
            }

            if(!empty($_GET['salesid'])) { ?>
                <input type="hidden" name="salesid" value="<?= $_GET['salesid'] ?>">
            <?php }

            if(!empty($_GET['projectid'])) {
                $projectid = $_GET['projectid'];
                $businessid = get_project($dbc, $projectid, 'businessid');
                $clientid = get_project($dbc, $projectid, 'clientid');
            }
            if(!empty($_GET['subject'])) {
                $subject = urldecode($_GET['subject']);
            }
            if(!empty($_GET['body'])) {
                $email_body = urldecode($_GET['body']);
            }

            $followup_by = '';
            $followup_date = '';
            $contactid = $_SESSION['contactid'];

            if (!empty($_GET['email_communicationid'])) {
                $email_communicationid = $_GET['email_communicationid'];
                $get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM email_communication WHERE email_communicationid='$email_communicationid'"));
                $businessid = $get_ticket['businessid'];
                $contactid = $get_ticket['contactid'];
                if($businessid == '') {
                    $businessid = get_contact($dbc, $contactid, 'businessid');
                }
                $to_contact = $get_ticket['to_contact'];
                $cc_contact = $get_ticket['cc_contact'];
                $to_staff = $get_ticket['to_staff'];
                $cc_staff = $get_ticket['cc_staff'];
                $new_emailid = $get_ticket['new_emailid'];
                $ticketid = $get_ticket['ticketid'];
                $projectid = $get_ticket['projectid'];
                $followup_by = $get_ticket['follow_up_by'];
                $followup_date = $get_ticket['follow_up_date'];
                $subject = $get_ticket['subject'];
                $email_body = $get_ticket['email_body']; ?>
                <input type="hidden" id="email_communicationid" name="email_communicationid" value="<?php echo $email_communicationid ?>" /><?php
            } else if (!empty($_GET['ticketid'])) {
                $ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
                $ticket_details = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
                $businessid = $ticket_details['businessid'];
                $contactid = explode(',',$ticket_details['clientid'])[0];
                if($contactid > 0 && !($businessid > 0)) {
                    $businessid = get_contact($dbc, $contactid, 'businessid');
                }
                $comm_tags = explode(',',$ticket_details['communication_tags']);
                $projectid = $ticket_details['projectid'];
                if(empty($subject)) {
                    $subject = get_ticket_label($dbc, $ticket_details);
                }
            }
            $local_page = $_GET;
            unset($local_page['type']);
            unset($local_page['category']);
            $local_query = '';
            foreach($local_page as $name => $value) {
                $local_query .= "&$name=$value";
            } ?>
            <input type="hidden" name="ticketid" value="<?= $ticketid ?>">

            <div class="panel-group block-panels main-screen" id="accordion2" style="background-color: #fff; padding: 0; margin-left: 0.5em; width: calc(100% - 1em);">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_type" >
                                Email Communication Type<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_type" class="panel-collapse collapse">
                        <div class="panel-body">
                            <select name="comm_type" data-placeholder="Choose an Option..." class="chosen-select-deselect form-control" onchange="window.location.replace('?type='+this.value+'<?= $local_query ?>');">
                                <option value="Internal" <?= $comm_type == 'Internal' ? 'selected' : '' ?>>Internal</option>
                                <option value="External" <?= $comm_type == 'External' ? 'selected' : '' ?>>External</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info" >
                                Information<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_info" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php include ('add_email_business_info.php'); ?>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_td2" >
                                Email Communication<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_td2" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php include ('add_email_people.php'); ?>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_td" >
                                Email Communication Details<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_td" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php include ('add_email_details.php'); ?>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_td1" >
                                Email Attachment<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>
                    <div id="collapse_td1" class="panel-collapse collapse">
                        <div class="panel-body">
                            <?php include ('add_email_attachments.php'); ?>
                        </div>
                    </div>
                </div>

                <!--
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
                            <?php //include ('add_email_followup.php'); ?>
                        </div>
                    </div>
                </div>
                -->

                <?php //if (strpos($value_config, ','."Communication Timer".',') !== FALSE) { ?>
                    <!--
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_timer" >
                                    Time Tracking<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>

                        <div id="collapse_timer" class="panel-collapse collapse">
                            <div class="panel-body">
                                <?php //include ('add_email_communication_timer.php'); ?>
                            </div>
                        </div>
                    </div>
                    -->
                <?php //} ?>

            </div><!-- .panel-group .block-panels .main-screen -->

            <div class="form-group">
                <button type="submit" name="submit" value="draft" class="btn brand-btn pull-right">Save as Draft</button>
                <button type="submit" name="submit" value="submit" class="btn brand-btn pull-right">Submit</button>
                <a href="../blank_loading_page.php" class="btn brand-btn pull-right">Cancel</a>
            </div>
        </form>
  </div>
</div>
<?php include ('../footer.php'); ?>
