<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('report');
error_reporting(0);

if (isset($_POST['submit'])) {
    //Logo
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    $logo = htmlspecialchars($_FILES["logo"]["name"], ENT_QUOTES);

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='report_logo'"));
    if($get_config['configid'] > 0) {
		if($logo == '') {
			$logo_update = $_POST['logo_file'];
		} else {
			$logo_update = $logo;
		}
		move_uploaded_file($_FILES["logo"]["tmp_name"],"download/" . $logo_update);
        $query_update_employee = "UPDATE `general_configuration` SET value = '$logo_update' WHERE name='report_logo'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
		move_uploaded_file($_FILES["logo"]["tmp_name"], "download/" . $_FILES["logo"]["name"]) ;
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('report_logo', '$logo')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Logo

    echo '<script type="text/javascript"> window.location.replace("?tab='.$_GET['tab'].'"); </script>';
}
?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <input type="hidden" name="contactid" value="<?php echo $_GET['contactid'] ?>" />

    <?php
    $logo = get_config($dbc, 'report_logo');
    ?>

    <div class="form-group">
        <label for="file[]" class="col-sm-4 control-label">
            <span class="popover-examples list-inline"><a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas."><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            Upload Logo
        :</label>
        <div class="col-sm-8">
            <?php if($logo != '') {
            echo '<a href="download/'.$logo.'" target="_blank">View</a>';
            ?>
            <input type="hidden" name="logo_file" value="<?php echo $logo; ?>" />
            <input name="logo" type="file" data-filename-placement="inside" class="form-control" />
            <?php } else { ?>
            <input name="logo" type="file" data-filename-placement="inside" class="form-control" />
            <?php } ?>
        </div>
    </div>

    <div class="form-group pull-right">
        <a href="report_tiles.php" class="btn brand-btn">Back</a>
        <button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
    </div>

</form>