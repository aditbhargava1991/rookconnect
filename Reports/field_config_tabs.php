<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('report');
error_reporting(0);

if (isset($_POST['submit'])) {
    //Tabs
    $report_tabs = filter_var(implode(',', $_POST['report_tabs']),FILTER_SANITIZE_STRING);
    set_config($dbc, 'report_tabs', $report_tabs);
    //Tabs

    echo '<script type="text/javascript"> window.location.replace("?tab='.$_GET['tab'].'"); </script>';
}
?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <input type="hidden" name="contactid" value="<?php echo $_GET['contactid'] ?>" />
    <?php $report_tabs = !empty(get_config($dbc, 'report_tabs')) ? get_config($dbc, 'report_tabs') : 'operations,sales,ar,marketing,compensation,pnl,customer,staff';
    $report_tabs = explode(',', $report_tabs); ?>
    <div class="form-group">
        <label class="form-checkbox"><input type="checkbox" name="report_tabs[]" value="ar" <?= in_array('ar', $report_tabs) ? 'checked' : '' ?>> Accounts Receivable</label>
        <label class="form-checkbox"><input type="checkbox" name="report_tabs[]" value="compensation" <?= in_array('compensation', $report_tabs) ? 'checked' : '' ?>> Compensation</label>
        <label class="form-checkbox"><input type="checkbox" name="report_tabs[]" value="customer" <?= in_array('customer', $report_tabs) ? 'checked' : '' ?>> Customer</label>
        <label class="form-checkbox"><input type="checkbox" name="report_tabs[]" value="estimates" <?= in_array('estimates', $report_tabs) ? 'checked' : '' ?>> Estimates</label>
        <label class="form-checkbox"><input type="checkbox" name="report_tabs[]" value="history" <?= in_array('history', $report_tabs) ? 'checked' : '' ?>> History</label>
        <label class="form-checkbox"><input type="checkbox" name="report_tabs[]" value="marketing" <?= in_array('marketing', $report_tabs) ? 'checked' : '' ?>> Marketing</label>
        <label class="form-checkbox"><input type="checkbox" name="report_tabs[]" value="operations" <?= in_array('operations', $report_tabs) ? 'checked' : '' ?>> Operations</label>
        <label class="form-checkbox"><input type="checkbox" name="report_tabs[]" value="pnl" <?= in_array('pnl', $report_tabs) ? 'checked' : '' ?>> Profit &amp; Loss</label>
        <label class="form-checkbox"><input type="checkbox" name="report_tabs[]" value="sales" <?= in_array('sales', $report_tabs) ? 'checked' : '' ?>> Sales</label>
        <label class="form-checkbox"><input type="checkbox" name="report_tabs[]" value="staff" <?= in_array('staff', $report_tabs) ? 'checked' : '' ?>> Staff</label>
    </div>

    <div class="form-group pull-right">
		<a href="report_tiles.php" class="btn brand-btn">Back</a>
		<button	type="submit" name="submit"	value="Submit" class="btn brand-btn">Submit</button>
	</div>
</form>
