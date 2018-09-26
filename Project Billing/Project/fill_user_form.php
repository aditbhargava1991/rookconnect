<?php include_once('../include.php');

if(isset($_POST['add_manual'])) {
	$projectid = $_POST['projectid'];
	if($projectid > 0) {
		include('user_forms.php');
	}
}

$project_form_id = $_GET['project_form_id'];
$projectid = $_GET['projectid'];
$projectform = $_GET['projectform'];
if($projectform > 0) {
	$get_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project_form` WHERE `id` = '$projectform'"));
	$projectid = $get_form['projectid'];
	$project_form_id = $get_form['project_form_id'];
}
$form_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `id` = '$project_form_id'"));
$user_form_id = $form_config['user_form_id'];
$user_form = mysqli_fetch_array(mysqli_query($dbc,"SELECT * FROM `user_forms` WHERE `form_id` = '$user_form_id'"));
$user_form_layout = $user_form['form_layout'];
?>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form" style="padding: 0; margin: 0;">
	<input type="hidden" name="projectid" value="<?= $projectid ?>">
	<input type="hidden" name="project_form_id" value="<?= $project_form_id ?>">
	<input type="hidden" name="projectform" value="<?= $projectform ?>">
	<input type="hidden" name="form_id" value="<?= $user_form_id ?>">

	<?php 
    if($user_form_layout == 'Sidebar') {
        include('user_forms_sidebar.php');
    } ?>
	<div class="scale-to-fill has-main-screen" id="user_form_mainscreen" style="padding: 0; background-color: #fff;">
	    <div class="main-screen standard-body default_screen form-horizontal" style="height: inherit;">
	    	<?php if($user_form_layout == 'Sidebar') { ?>
		        <div class="standard-body-title">
		            <h3><?= $form_config['subtab_name'] ?></h3>
		        </div>
		    <?php } ?>
	        <div class="standard-body-content double-pad-left double-pad-right pad-top">
	        	<?php if($user_form_layout != 'Sidebar') { ?>
			    	<h3><?= $form_config['subtab_name'] ?></h3>
			    	<div class="gap-top double-gap-bottom"><a href="?edit=<?= $projectid ?>&tab=user_forms&project_form_id=<?= $project_form_id ?>" class="btn config-btn">Back to Project</a></div>
	        	<?php } ?>

			    <?php include('../Project/user_forms.php'); ?>

			    <div class="form-group">
			    	<div class="col-sm-4">
			    		<p><span class="hp-red pull-right"><em>Required Fields *</em></span></p>
			    	</div>
			    	<div class="col-sm-8"></div>
			    </div>

			    <div class="form-group">
			    	<div class="col-sm-4 clearfix">
			    		<a href="?edit=<?= $projectid ?>&tab=user_forms&project_form_id=<?= $project_form_id ?>" class="btn brand-btn pull-right">Back</a>
			    	</div>
			    	<div class="col-sm-8">
			    		<button type="submit" name="add_manual" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
			    	</div>
			    </div>
            </div>
        </div>
    </div>
</form>