<?php include ('../include.php');
if(isset($_POST['save_intake'])) {
	$intakeformid = filter_var($_POST['intakeformid'],FILTER_SANITIZE_STRING);
	$user_form_id = filter_var($_POST['user_form_id'],FILTER_SANITIZE_STRING);
	$form_name = filter_var($_POST['form_name'],FILTER_SANITIZE_STRING);
	$expiry_date = filter_var($_POST['expiry_date'],FILTER_SANITIZE_STRING);

	if(!empty($intakeformid)) {
		mysqli_query($dbc, "UPDATE `intake_forms` SET `user_form_id` = '$user_form_id', `form_name` = '$form_name', `expiry_date` = '$expiry_date' WHERE `intakeformid` = '$intakeformid'");
		$before_change = "";
		$history = "Intake entry has been updated for User form name - $form_name. <br />";
		add_update_history($dbc, 'intake_history', $history, '', $before_change);
	} else {
		mysqli_query($dbc, "INSERT INTO `intake_forms` (`user_form_id`, `form_name`, `expiry_date`) VALUES ('$user_form_id', '$form_name', '$expiry_date')");
		$intakeformid = mysqli_insert_id($dbc);
		$before_change = "";
		$history = "Intake Form entry has been added. <br />";
		add_update_history($dbc, 'intake_history', $history, '', $before_change);
	}

	echo '<script type="text/javascript"> window.location.href = "../Intake/intake.php?tab=softwareforms"; </script>';
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
if(!empty($_GET['edit'])) {
	$intakeformid = $_GET['edit'];
	$intake = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `intakeformid` = '$intakeformid'"));
	$user_form_id = $intake['user_form_id'];
	$form_name = $intake['form_name'];
	$expiry_date = $intake['expiry_date'];
}
?>

<div class="container" style="background-color: #fff;">
	<div class="iframe_overlay" style="display:none;">
        <div class="iframe">
            <div class="iframe_loading">Loading...</div>
            <iframe src=""></iframe>
        </div>
    </div>
	<div class="row hide_on_iframe">
		<div class="main-screen">
			<div class="tile-header standard-header">
				<?php include('../Intake/tile_header.php'); ?>
			</div>

            <div class="standard-collapsible tile-sidebar hide-on-mobile">
            	<ul>
            		<a href="../Intake/intake.php?tab=softwareforms"><li>Back to Dashboard</li></a>
            		<a href="" onclick="return false;"><li class="active">Intake Form Information</li></a>
            	</ul>
            </div>

            <div class="scale-to-fill has-main-screen">
            	<div class="main-screen standard-body">
	        		<div class="standard-body-title">
	        			<h3><?= !empty($_GET['edit']) ? 'Edit' : 'Add' ?> Intake Form</h3>
	        		</div>
            		<div class="standard-body-content" style="padding: 0 0.5em;">
			            <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
							<input type="hidden" name="intakeformid" value="<?= $intakeformid ?>">
				        	<!-- Notice -->
				            <div class="notice gap-bottom gap-top popover-examples">
				                <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
				                <div class="col-sm-11"><span class="notice-name">NOTE:</span>
								Add an Intake Form here.</div>
				                <div class="clearfix"></div>
				            </div>

							<div class="form-group">
								<label class="col-sm-4 control-label">Form:</label>
								<div class="col-sm-8">
									<select name="user_form_id" data-placeholder="Select a Form..." class="chosen-select-deselect">
										<optioN></optioN>
										<?php $forms_list = mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE CONCAT(',', `assigned_tile`, ',') LIKE '%,intake,%' AND `deleted` = 0");
										while ($row = mysqli_fetch_array($forms_list)) {
											echo '<option value="'.$row['form_id'].'" '.($row['form_id'] == $user_form_id ? 'selected' : '').'>'.$row['name'].'</option>';
										} ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Form Name:</label>
								<div class="col-sm-8">
									<input type="text" name="form_name" class="form-control" value="<?= $form_name ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Expiry Date:</label>
								<div class="col-sm-8">
									<input type="text" name="expiry_date" class="form-control datepicker" value="<?= $expiry_date ?>">
								</div>
							</div>
			                <div class="pull-right gap-top gap-right gap-bottom">
			                    <a href="intake.php?tab=softwareforms" class="btn brand-btn">Cancel</a>
			                    <button type="submit" id="save_intake" name="save_intake" value="Submit" class="btn brand-btn">Submit</button>
			                </div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include ('../footer.php'); ?>
