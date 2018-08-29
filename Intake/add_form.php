<?php include('../include.php'); ?>
</head>

<body><?php
if(!empty($_SESSION['contactid'])) {
	include_once('../navigation.php');
}
checkAuthorised('intake');
if(!empty($_GET['formid'])) {
    $user_form_id = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM intake_forms WHERE intakeformid='".$_GET['formid']."'"))['user_form_id'];
    if($user_form_id > 0) {
        $user_form_layout = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"))['form_layout'];
        $user_form_layout = !empty($user_form_layout) ? $user_form_layout : 'Accordions';   
    }
}

$intakeformid = $_GET['formid'];
$intakeid = $_GET['intakeid'];
if(!empty($intakeid)) {
    $get_intake = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid` = '$intakeid'"));
    $intakeformid = $get_intake['intakeformid'];
}
$intake_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `intakeformid` = '$intakeformid'"));
$user_form_id = $intake_form['user_form_id'];
$access_code = $intake_form['access_code'];
$expiry_date = $intake_form['expiry_date'];
$value_config = get_field_config($dbc, 'intake_software_dashboard'); ?>

<div class="container" style="<?= $user_form_layout == 'Sidebar' ? 'padding: 0; margin: 0;' : 'min-height: calc(100vh - 60px);' ?>">
	<div class="dialog_attachcontact" title="Attach to Contact?" style="display: none;">
		<h4>Would you like to attach this Intake Form to a Contact?</h4>
	</div>

	<div class="iframe_holder" style="display:none;">
		<img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
		<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
		<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
	</div>

	<div class="row">
		<?php
		if(vuaed_visible_function($dbc, 'intake') == 1) {
			include('../Intake/user_forms.php');
		} else {
			if(empty($intake_form)) {
				echo '<h1>This form does not exist.</h1>';
			} else if($_GET['access_code'] != $access_code) {
				echo '<h1>You do not have access to this form.</h1>';
			} else if(strtotime($expiry_date) < strtotime(date('Y-m-d'))) {
				echo '<h1>This form has expired.</h1>';
			} else if($_GET['complete'] == 'true') {
				echo '<h1>Your form has been submitted.</h1>';
			} else {
				include('../Intake/user_forms.php');
			}	
		}
		?>
	</div>
</div>

<?php include ('../footer.php'); ?>