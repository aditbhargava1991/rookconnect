<?php include ('../include.php');
if(isset($_POST['email_intake'])) {
	$receiver = filter_var($_POST['email_receiver'],FILTER_SANITIZE_STRING);
	$sender_name = filter_var($_POST['email_sender_name'],FILTER_SANITIZE_STRING);
	$sender = filter_var($_POST['email_sender'],FILTER_SANITIZE_STRING);
	$subject = filter_var($_POST['email_subject'],FILTER_SANITIZE_STRING);
	$body = filter_var(html_entity_decode($_POST['email_body']),FILTER_SANITIZE_STRING);

	$receiver = explode(',', $receiver);
	$log = '';
	foreach ($receiver as $email) {
		try {
			send_email([$sender=>$sender_name], $email, '', '', $subject, $body, '');
		} catch(Exception $e) {
			$log .= 'Unable to send email to '.$email.': '.$e->getMessage();
		}
	}
	if(empty($log)) {
		$log = 'Successfully emailed.';
	}

	echo '<script type="text/javascript"> alert("'.$log.'"); window.location.href = "../Intake/intake.php?tab=softwareforms"; </script>';
}
?>
</head>

<body><?php
include_once ('../navigation.php');
checkAuthorised('intake');

$intakeformid = '';
$user_form_id = '';
$form_name = '';
$expiry_date = '';
$access_code = '';
if(!empty($_GET['intakeformid'])) {
	$intakeformid = $_GET['intakeformid'];
	$intake = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `intakeformid` = '$intakeformid'"));
	$user_form_id = $intake['user_form_id'];
	$form_name = $intake['form_name'];
	$expiry_date = $intake['expiry_date'];
	$access_code = $intake['access_code'];
}
$subject = $form_name;
$body = 'The following form has been sent to you to fill out:<br><br>
	Form: '.$form_name.'<br>
	URL: <a href="'.WEBSITE_URL.'/Intake/add_form.php?formid='.$intakeformid.'&access_code='.$access_code.'">'.WEBSITE_URL.'/Intake/add_form.php?formid='.$intakeformid.'&access_code='.$access_code.'</a> ';
if(!empty($expiry_date)) {
	$body .= '<br><br>You have until '.$expiry_date.' to fill this form out.';
}
?>

<div class="container" style="background-color: #fff;">
	<div class="row hide_on_iframe">
		<div class="main-screen">
			<div class="tile-header standard-header">
				<?php include('../Intake/tile_header.php'); ?>
			</div>


            <div class="standard-collapsible tile-sidebar hide-on-mobile">
            	<ul>
            		<a href="../Intake/intake.php?tab=softwareforms"><li>Back to Dashboard</li></a>
            		<a href="" onclick="return false;"><li class="active">Email Intake Form</li></a>
            	</ul>
            </div>

            <div class="scale-to-fill has-main-screen">
            	<div class="main-screen standard-body">
	        		<div class="standard-body-title">
	        			<h3>Email <?= $form_name ?></h3>
	        		</div>
            		<div class="standard-body-content" style="padding: 0 0.5em;">
		                <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
							<input type="hidden" name="intakeformid" value="<?= $intakeformid ?>">
				        	<!-- Notice -->
				            <div class="notice gap-bottom gap-top popover-examples">
				                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
				                <div class="col-sm-11"><span class="notice-name">NOTE:</span>
								Email intake form here.</div>
				                <div class="clearfix"></div>
				            </div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Receiver's Email:<br><em>Enter emails separated by commas</em></label>
								<div class="col-sm-8">
									<input type="text" name="email_receiver" class="form-control">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Email Sender's Name:</label>
								<div class="col-sm-8">
									<input type="text" name="email_sender_name" class="form-control" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Email Address:</label>
								<div class="col-sm-8">
									<input type="text" name="email_sender" class="form-control" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Email Subject:</label>
								<div class="col-sm-8">
									<input type="text" name="email_subject" class="form-control" value="<?= $subject ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Email Body:</label>
								<div class="col-sm-8">
									<textarea name="email_body" class="form-control"><?php echo $body; ?></textarea>
								</div>
							</div>
			                <div class="pull-right gap-top gap-right gap-bottom">
			                    <a href="intake.php?tab=softwareforms" class="btn brand-btn">Cancel</a>
			                    <button type="submit" id="email_intake" name="email_intake" value="Submit" class="btn brand-btn">Submit</button>
			                </div>
			            </form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include ('../footer.php'); ?>