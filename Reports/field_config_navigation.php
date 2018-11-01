<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('report');
error_reporting(0);

if (isset($_POST['submit'])) {
	//Report Fields - Compensation Reports
	$report_fields = implode(',', $_POST['reports_dashboard_navigation']);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'reports_dashboard_navigation' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='reports_dashboard_navigation') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$report_fields' WHERE `name`='reports_dashboard_navigation'");

    $contactid = $_POST['contactid'];

    echo '<script type="text/javascript"> window.location.replace("?tab='.$_GET['tab'].'"); </script>';
}


?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <input type="hidden" name="contactid" value="<?php echo $_GET['contactid'] ?>" />

	<?php $report_fields = explode(',', get_config($dbc, 'reports_dashboard_navigation')); ?>
	<h4>Dashboard Left Navigations Setting</h4>
    <div class="form-group">
		<label class="form-checkbox">
			<input type="radio" <?= (in_array('show',$report_fields) || empty(array_filter($report_fields)) ? 'checked' : '') ?> name="reports_dashboard_navigation[]" value="show">
			Show Dashboard Navigations
			<input type="radio" <?= (in_array('hide',$report_fields) ? 'checked' : '') ?> name="reports_dashboard_navigation[]" value="hide">
			Hide Dashboard Navigations
		</label>
    </div>

    <div class="form-group pull-right">
        <a href="report_tiles.php" class="btn brand-btn">Back</a>
        <button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
    </div>

</form>
