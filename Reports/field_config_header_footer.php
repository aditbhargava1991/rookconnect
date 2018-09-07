<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('report');
error_reporting(0);

if (isset($_POST['submit'])) {
    //Header & Footer
    $report_header = filter_var(htmlentities($_POST['report_header']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='report_header'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$report_header' WHERE name='report_header'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('report_header', '$report_header')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $report_footer = filter_var(htmlentities($_POST['report_footer']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='report_footer'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$report_footer' WHERE name='report_footer'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('report_footer', '$report_footer')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $invoice_unpaid_footer = filter_var(htmlentities($_POST['invoice_unpaid_footer']),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_unpaid_footer'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_unpaid_footer' WHERE name='invoice_unpaid_footer'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_unpaid_footer', '$invoice_unpaid_footer')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Header & Footer

    echo '<script type="text/javascript"> window.location.replace("?tab='.$_GET['tab'].'"); </script>';
}
?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <input type="hidden" name="contactid" value="<?php echo $_GET['contactid'] ?>" />

    <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label"><h4>Header</h4></label>
    <div class="col-sm-8">
        <textarea name="report_header" rows="5" cols="50" class="form-control"><?php echo get_config($dbc, 'report_header'); ?></textarea>
    </div>
    </div>

    <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label"><h4>Footer</h4></label>
    <div class="col-sm-8">
        <textarea name="report_footer" rows="5" cols="50" class="form-control"><?php echo get_config($dbc, 'report_footer'); ?></textarea>
    </div>
    </div>
    
    <div class="form-group pull-right">
        <a href="report_tiles.php" class="btn brand-btn">Back</a>
        <button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
    </div>
</form>