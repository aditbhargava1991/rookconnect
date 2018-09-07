<?php
/*
Support
*/
include ('../include.php');
error_reporting(0);

if (isset($_POST['support'])) {
    $name =  filter_var($_POST['name'],FILTER_SANITIZE_STRING);
    $email =  filter_var($_POST['email'],FILTER_SANITIZE_STRING);
    $cc_email =  filter_var($_POST['cc_email'],FILTER_SANITIZE_STRING);
    $contact_number =  filter_var($_POST['contact_number'],FILTER_SANITIZE_STRING);
    $message_support = filter_var(htmlentities($_POST['message_support']),FILTER_SANITIZE_STRING);
    $support_type = filter_var($_POST['support_type'],FILTER_SANITIZE_STRING);
    $priority = filter_var($_POST['priority'],FILTER_SANITIZE_STRING);
    $heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
    $current_date =  $_POST['current_date'];
    $company_name = get_config($dbc, 'company_name');
    $current_date = date('Y-m-d');
    $document = implode('*#*',$_FILES["file"]["name"]);

    //$file_path = '/home/ffm_software_ftp/ffm.rookconnect.com/Ticket/';

	//if (!file_exists($file_path.'download')) {
	//	mkdir($file_path.'download', 0777, true);
	//}

	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}

    for($i = 0; $i < count($_FILES['file']['name']); $i++) {
        move_uploaded_file($_FILES["file"]["tmp_name"][$i], "download/" . $_FILES["file"]["name"][$i]) ;
    }

    $dbc_software = @mysqli_connect('localhost', 'ffm_rook_user', 'mIghtyLion!542', 'ffm_rook_db');

    $query_insert_support = "INSERT INTO `support` (`name`, `email`, `cc_email`, `contact_number`, `message`, `document`, `current_date`, `support_type`, `priority`, `company_name`, `heading`) VALUES ('$name', '$email', '$cc_email', '$contact_number', '$message_support', '$document', '$current_date', '$support_type', '$priority', '$company_name', '$heading')";

    $result_insert_support = mysqli_query($dbc_software, $query_insert_support);
    //$result_insert_support = mysqli_query($dbc, $query_insert_support);

    $send_document = htmlspecialchars($_FILES["file"]["name"], ENT_QUOTES);

    if (mysqli_affected_rows($dbc_software) == 1) {

		// Email to FFM staff.

        $mail_message = 'Name : '.$name .'<br/>';
        $mail_message .= 'Email : '.$email .'<br/>';
        $mail_message .= 'CC Email : '.$cc_email .'<br/>';
        $mail_message .= 'Contact Number : '.$contact_number .'<br/>';
        $mail_message .= 'Heading : '.$_POST['heading'] .'<br/>';
        $mail_message .= 'Message : <br>'.$_POST['message_support'] .'<br/>';
        $mail_message .= 'Date : '.$current_date;
        $message = html_entity_decode($mail_message);

		$to = 'dayanapatel@freshfocusmedia.com,kennethbond@freshfocusmedia.com,jenniferhardy@freshfocusmedia.com,jaylahiru@freshfocusmedia.com,jonathanhurdman@freshfocusmedia.com,baldwinyu@freshfocusmedia.com,josephma@freshfocusmedia.com';
        if($cc_email != '') {
            $to .= ','.$cc_email;
        }

        $to_email = explode(',', $to);
        $subject ="Support from ".get_config($dbc, 'company_name');

        $meeting_attachment = '';
        for($i = 0; $i < count($_FILES['file']['name']); $i++) {
            if($_FILES["file"]["name"][$i] != '') {
                move_uploaded_file($_FILES["file"]["tmp_name"][$i], "download/" . $_FILES["file"]["name"][$i]) ;
                $meeting_attachment .= 'download/'.$_FILES["file"]["name"][$i].'*#FFM#*';
            }
        }

        send_email('', $to_email, '', '', $subject, $message, $meeting_attachment);

		// Thank you Email to sender and CC email.
        $to_sender = $email;
        $subject_sender = 'Confirmation of Your Support Request';

        $message_sender = 'Hello '.$name.',<br>'.'Your support request has been sucessfully received. Your request is currently under review by our support team, and someone will be contacting you shortly. For your records please find a copy of your original request below.<br><br>Thank you,<br>Fresh Focus Media Support<br><br>
		----------------------BEGIN ORIGINAL MESSAGE-----------------------------<br><br>';
		$message_sender .= html_entity_decode($mail_message);

        if($email != '') {
            send_email('', $to_sender, '', '', $subject_sender, $message_sender, $meeting_attachment);
        }
    }

    echo '<script type="text/javascript"> window.location.replace("../home.php"); </script>';
}

?>
<script type="text/javascript">
$(document).ready(function() {
    $('#add_row_doc').on( 'click', function () {
        var clone = $('.additional_doc').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_doc");
        $('#add_here_new_doc').append(clone);
        return false;
    });
});
</script>

</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
	<div class="row">
        <div class="col-md-12">
            <center><a href="http://www.freshfocusmedia.com" target="_blank"><img width="240px" src="<?php echo WEBSITE_URL; ?>/img/ffm-logo-support.png"></a>
            <h1 class="double-pad-bottom">Contact Info</h1>
			<h4><a href="http://www.freshfocusmedia.com" target="_blank">Visit Our Website</a><br><br>
			Phone : <a href="tel:1.888.380.9439">1.888.380.9439</a><br><br>
                Suite 200, 7220 Fairmount Dr SE Calgary, AB T2H 0X7<br></h4>
            </center>
            <h1 class="triple-pad-bottom">Support</h1>

            <form id="form1" name="form1" method="post" action="support.php" enctype="multipart/form-data" class="form-horizontal" role="form">

                <div class="form-group">
                    <label for="site_name" class="col-sm-4 control-label">Name<span class="text-red">*</span>:</label>
                    <div class="col-sm-8">
                      <input name="name" type="text" required class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="site_name" class="col-sm-4 control-label">Email:</label>
                    <div class="col-sm-8">
                      <input name="email" type="text" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="site_name" class="col-sm-4 control-label">CC Email(s):<br><em>(separate multiple emails with a comma)</em></label>
                    <div class="col-sm-8">
                      <input name="cc_email" type="text" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="site_name" class="col-sm-4 control-label">Phone Number:</label>
                    <div class="col-sm-8">
                      <input name="contact_number" type="text" class="form-control" />
                    </div>
                </div>

                <!--
                <div class="form-group">
                    <label for="site_name" class="col-sm-4 control-label">Type:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Choose a Type..." name="support_type" class="chosen-select-deselect form-control" width="380">
                          <option value=""></option>
                          <?php
                            $tabs = get_config($dbc, 'helpdesk_type');
                            $each_tab = explode(',', $tabs);
                            foreach ($each_tab as $cat_tab) {
                                if ($invtype == $cat_tab) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
                            }
                          ?>
                        </select>

                    </div>
                </div>
                -->

                <div class="form-group">
                    <label for="site_name" class="col-sm-4 control-label">Priority:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Choose a Therapist..." name="priority" class="chosen-select-deselect form-control" width="380">
                            <option value="Low" style="background-color: brown;">Low</option>
                            <option value="Medium" style="background-color: green;">Medium</option>
                            <option value="High" style="background-color: blue;">High</option>
                            <option value="Urgent" style="background-color: yellow;">Urgent</option>
                            <option value="Critical" style="background-color: red;">Critical</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="site_name" class="col-sm-4 control-label">Heading:</label>
                    <div class="col-sm-8">
                      <input name="heading" type="text" class="form-control" />
                    </div>
                </div>

                <div class="form-group">
                    <label for="additional_note" class="col-sm-4 control-label">Message:</label>
                    <div class="col-sm-8">
                        <textarea name="message_support" rows="5" cols="50" class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
                            <span class="popover-examples list-inline">&nbsp;
                            <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
                            </span>
                    </label>
                    <div class="col-sm-8">

                        <div class="enter_cost additional_doc clearfix">
                            <div class="clearfix"></div>

                            <div class="form-group clearfix">
                                <div class="col-sm-5">
                                    <input name="file[]" multiple type="file" data-filename-placement="inside" class="form-control" />
                                </div>
                            </div>

                        </div>

                        <div id="add_here_new_doc"></div>

                        <div class="form-group triple-gapped clearfix">
                            <div class="col-sm-offset-4 col-sm-8">
                                <button id="add_row_doc" class="btn brand-btn pull-left">Add Another Document</button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Date:</label>
                    <div class="col-sm-8">
                      <input name="current_date" type="text" value="<?php echo date('Y-m-d');; ?>" readonly class="form-control"/>
                    </div>
                </div>

                  <div class="form-group">
                    <div class="col-sm-4 clearfix">
                        <!-- <a href="<?php //echo WEBSITE_URL; ?>/home.php" class="btn brand-btn mobile-block pull-right">Back</a> -->
                    </div>
                    <div class="col-sm-8">
                        <button type="submit" name="support" value="Submit" class="btn brand-btn mobile-block btn-lg pull-right">Submit</button>
                    </div>
                  </div>

            </form>
        </div>
	</div>
</div>

<?php include ('../footer.php'); ?>
