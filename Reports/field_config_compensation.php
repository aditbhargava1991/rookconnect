<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('report');
error_reporting(0);

if (isset($_POST['submit'])) {
	//Report Fields - Compensation Reports
	$report_fields = implode(',', $_POST['report_compensation_fields']);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'report_compensation_fields' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='report_compensation_fields') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$report_fields' WHERE `name`='report_compensation_fields'");

    $contactid = $_POST['contactid'];

    echo '<script type="text/javascript"> window.location.replace("?tab='.$_GET['tab'].'"); </script>';
}
?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <input type="hidden" name="contactid" value="<?php echo $_GET['contactid'] ?>" />

	<?php $report_fields = explode(',', get_config($dbc, 'report_compensation_fields')); ?>
	<h4>Therapist and Adjustment Compensation Reports</h4>
    <div class="form-group">
		<label class="form-checkbox"><input type="checkbox" <?= (in_array('therapist_patient_info',$report_fields) ? 'checked' : '') ?> name="report_compensation_fields[]" value="therapist_patient_info">
		Show Customer Information</label>
    </div>

    <div class="form-group pull-right">
        <a href="report_tiles.php" class="btn brand-btn">Back</a>
        <button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
    </div>

</form>