<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('field_job');
error_reporting(0);

if (isset($_POST['submit'])) {

    $attachment = '';
    $today_date = date('Y-m-d');
    $all_insurerid = explode(',', $_POST['workticketid']);
    foreach($all_insurerid as $workticketid){
        if($workticketid != '') {
            $name_of_file = 'download/field_work_ticket_'.$workticketid.'.pdf';
            $attachment .= $name_of_file.'*#FFM#*';

    	    $query_update_site = "UPDATE `field_work_ticket` SET date_sent = CONCAT_WS('<br>',date_sent, '$today_date') WHERE `workticketid` = '$workticketid'";
		    $result_update_site	= mysqli_query($dbc, $query_update_site);
        }
    }

    $manualtypeid = $_POST['manualtypeid'];

    $email_to = explode(',', str_replace(' ', '', $_POST['email_to']));
    $cc_email_to = explode(',', str_replace(' ', '', $_POST['cc_email_to']));
    $to_email = array_filter(array_merge((array)$email_to,(array)$_POST['email_staff']));
	$email_from = $_POST['email_from'];
	$email_name = $_POST['email_name'];

    $email_subject =$_POST['email_subject'];
    $email_body = $_POST['email_body'];

    $bcc = get_config($dbc, 'wt_bcc_email');

	$failed = [];

    if($_POST['email_to'] != '' || $_POST['cc_email_to'] != '' || $_POST['email_staff'] != '') {
		try {
			send_email([$email_from => $email_name], $to_email, $cc_email_to, $bcc, $email_subject, $email_body, $attachment);
		} catch (Exception $e) {
			$failed[] = $address;
		}
    }

	if(count($failed) > 0) {
		echo "<script> alert('Unable to send the work ticket to the following address(es):\n";
		foreach($failed as $email) {
			echo $email."\n";
		}
		echo "This may be due to a problem with the email addresses, or with the SMTP\n";
		echo "server being temporarily unavailable. Please try again later.'); </script>";
	}
    echo '<script type="text/javascript"> window.location.replace("field_work_ticket.php"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
});
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php $from = get_config($dbc, 'wt_email_from');
	$from = (empty($from) ? get_email($dbc, $_SESSION['contactid']) : $from);
	$subject = get_config($dbc, 'wt_email_subject');
	$subject = (empty($subject) ? 'Please review the attached Work Ticket' : $subject);
	$body = html_entity_decode(get_config($dbc, 'wt_email_body'));
	$body = (empty($body) ? 'Attached to this email is a Work Ticket. Please review it, and let us know if you have any concerns.' : $body); ?>

    <div class="form-group">
		<label for="company_name" class="col-sm-4 control-label">Sender's Email Address:</label>
		<div class="col-sm-8">
			<input name="email_from" type="text" class="form-control" value="<?= $from ?>"></p>
		</div>
    </div>
    <div class="form-group">
		<label for="company_name" class="col-sm-4 control-label">Sender's Name:</label>
		<div class="col-sm-8">
			<input name="email_name" type="text" class="form-control" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>"></p>
		</div>
    </div>

    <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Confirm Work Ticket(s):</label>
    <div class="col-sm-8">
        <?php
        $all_insurerid = explode(',', $_GET['workticketid']);
        foreach($all_insurerid as $workticketid){
            if($workticketid != '') {
                $name_of_file = 'download/field_work_ticket_'.$workticketid.'.pdf';
                echo '<a href="'.$name_of_file.'" target="_blank">#'.$workticketid.'</a>&nbsp;&nbsp;';
            }
        }
        echo '<input type="hidden" name="workticketid" value="'.$_GET['workticketid'].'" />';
        ?>
    </div>
    </div>

    <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Email To:<br><em>(separate multiple emails using a comma and no spaces)</em></label>
    <div class="col-sm-8">
        <input name="email_to" type="text" value="<?php echo get_email($dbc, $_GET['cid']);?>" class="form-control">
    </div>
    </div>

    <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">CC Email To:<br><em>(separate multiple emails using a comma and no spaces)</em></label>
    <div class="col-sm-8">
        <input name="cc_email_to" type="text" value="<?php echo get_email($dbc, $_GET['ccid']);?>" class="form-control">
    </div>
    </div>

    <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Email Staff:</label>
    <div class="col-sm-8">
        <select name="email_staff[]" multiple data-placeholder="Select Staff" value="<?php echo $get_contact['email_address'];?>" class="chosen-select-deselect form-control"><option></option>
			<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `category`, `last_name`, `first_name`, `name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`=1 AND `show_hide_user`=1 AND `deleted`=0"), MYSQLI_ASSOC));
			foreach($staff_list as $id) {
				$name = get_contact($dbc, $id);
				$email = get_email($dbc, $id);
				if($email != '') {
					echo "<option value='$email'>$name : $email</option>";
				}
			} ?>
		</select>
    </div>
    </div>

    <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Email Subject:</label>
    <div class="col-sm-8">
        <input name="email_subject" type="text" class="form-control" value="<?= $subject ?>"></p>
    </div>
    </div>

    <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Email Body:</label>
    <div class="col-sm-8">
        <textarea name="email_body" rows="5" cols="50" class="form-control"><?= $body ?></textarea>
    </div>
    </div>

    <div class="form-group">
        <div class="col-sm-4 clearfix">
            <a href="field_work_ticket.php" class="btn brand-btn pull-right">Back</a>
        </div>
        <div class="col-sm-8">
            <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Send</button>
        </div>
    </div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>