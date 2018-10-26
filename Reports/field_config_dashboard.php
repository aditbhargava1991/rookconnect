<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('report');
error_reporting(0);

if (isset($_POST['submit'])) {
    $reports_dashboard = implode(',',$_POST['reports_dashboard']);

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='reports_dashboard'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$reports_dashboard' WHERE name='reports_dashboard'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('reports_dashboard', '$reports_dashboard')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    echo '<script type="text/javascript"> window.location.replace("?tab='.$_GET['tab'].'"); </script>';
}
?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <input type="hidden" name="contactid" value="<?php echo $_GET['contactid'] ?>" />

	<?php $value_config = ','.get_config($dbc, 'reports_dashboard').','; ?>

    <h3>Accounts Receivable</h3>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."A/R Aging Summary".',') !== FALSE) { echo " checked"; } ?> value="A/R Aging Summary" name="reports_dashboard[]"> A/R Aging Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Balance Summary".',') !== FALSE) { echo " checked"; } ?> value="Customer Balance Summary" name="reports_dashboard[]"> Customer Balance Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Invoice List".',') !== FALSE) { echo " checked"; } ?> value="Invoice List" name="reports_dashboard[]"> Invoice List</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Patient Aging Receivable Summary".',') !== FALSE) { echo " checked"; } ?> value="Patient Aging Receivable Summary" name="reports_dashboard[]"> Customer Aging Receivable Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Balance by Invoice".',') !== FALSE) { echo " checked"; } ?> value="Customer Balance by Invoice" name="reports_dashboard[]"> Customer Balance by Invoice</label>
        </div>
        <!-- <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."POS Receivables (Basic)".',') !== FALSE) { echo " checked"; } ?> value="POS Receivables (Basic)" name="reports_dashboard[]"> POS Receivables (Basic)</label>
        </div>
        -->
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."POS Receivables".',') !== FALSE) { echo " checked"; } ?> value="POS Receivables" name="reports_dashboard[]"> POS Receivables</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Insurer Aging Receivable Summary".',') !== FALSE) { echo " checked"; } ?> value="Insurer Aging Receivable Summary" name="reports_dashboard[]"> Insurer Aging Receivable Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Collections Report by Customer".',') !== FALSE) { echo " checked"; } ?> value="Collections Report by Customer" name="reports_dashboard[]"> Collections Report by Customer</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."UI Invoice Report".',') !== FALSE) { echo " checked"; } ?> value="UI Invoice Report" name="reports_dashboard[]"> UI Invoice Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."By Invoice#".',') !== FALSE) { echo " checked"; } ?> value="By Invoice#" name="reports_dashboard[]"> By Invoice#</label>
        </div>
    <div class="clearfix"></div>


    <h3>Compensation</h3>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Adjustment Compensation".',') !== FALSE) { echo " checked"; } ?> value="Adjustment Compensation" name="reports_dashboard[]"> Adjustment Compensation</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Hourly Compensation".',') !== FALSE) { echo " checked"; } ?> value="Hourly Compensation" name="reports_dashboard[]"> Hourly Compensation</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Therapist Compensation".',') !== FALSE) { echo " checked"; } ?> value="Therapist Compensation" name="reports_dashboard[]"> Staff Compensation</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Vendor Compensation".',') !== FALSE) { echo " checked"; } ?> value="Vendor Compensation" name="reports_dashboard[]"> Vendor Compensation</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Compensation Print Appointment Reports".',') !== FALSE) { echo " checked"; } ?> value="Compensation Print Appointment Reports" name="reports_dashboard[]"> Compensation: Print Appt. Reports Button</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Statutory Holiday Pay Breakdown".',') !== FALSE) { echo " checked"; } ?> value="Statutory Holiday Pay Breakdown" name="reports_dashboard[]"> Statutory Holiday Pay Breakdown</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Timesheet Payroll".',') !== FALSE) { echo " checked"; } ?> value="Timesheet Payroll" name="reports_dashboard[]"> Time Sheet Payroll</label>
        </div>
    <div class="clearfix"></div>


    <h3>Customer</h3>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Sales by Customer Summary".',') !== FALSE) { echo " checked"; } ?> value="Customer Sales by Customer Summary" name="reports_dashboard[]"> Sales by Customer Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Sales History by Customer".',') !== FALSE) { echo " checked"; } ?> value="Customer Sales History by Customer" name="reports_dashboard[]"> Sales History by Customer</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Patient Invoices".',') !== FALSE) { echo " checked"; } ?> value="Customer Patient Invoices" name="reports_dashboard[]"> Customer Invoices</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Transaction List by Customer".',') !== FALSE) { echo " checked"; } ?> value="Customer Transaction List by Customer" name="reports_dashboard[]"> Transaction List by Customer</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Patient History".',') !== FALSE) { echo " checked"; } ?> value="Customer Patient History" name="reports_dashboard[]"> Patient History</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Customer Balance Summary".',') !== FALSE) { echo " checked"; } ?> value="Customer Customer Balance Summary" name="reports_dashboard[]"> Customer Balance Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Customer Balance by Invoice".',') !== FALSE) { echo " checked"; } ?> value="Customer Customer Balance by Invoice" name="reports_dashboard[]"> Customer Balance by Invoice</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Collections Report by Customer".',') !== FALSE) { echo " checked"; } ?> value="Customer Collections Report by Customer" name="reports_dashboard[]"> Collections Report by Customer</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Patient Aging Receivable Summary".',') !== FALSE) { echo " checked"; } ?> value="Customer Patient Aging Receivable Summary" name="reports_dashboard[]"> Patient Aging Receivable Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Customer Contact List".',') !== FALSE) { echo " checked"; } ?> value="Customer Customer Contact List" name="reports_dashboard[]"> Customer Contact List</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Customer Stats".',') !== FALSE) { echo " checked"; } ?> value="Customer Customer Stats" name="reports_dashboard[]"> Customer Stats</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer CRM Recommendations - By Customer".',') !== FALSE) { echo " checked"; } ?> value="Customer CRM Recommendations - By Customer" name="reports_dashboard[]"> CRM Recommendations - By Customer</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Contact Postal Code".',') !== FALSE) { echo " checked"; } ?> value="Customer Contact Postal Code" name="reports_dashboard[]"> Contact Postal Code</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Service Rates".',') !== FALSE) { echo " checked"; } ?> value="Customer Service Rates" name="reports_dashboard[]"> Service Rates &amp; Hours</label>
        </div>
    <div class="clearfix"></div>

    <h3>Estimates</h3>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Estimate Report".',') !== FALSE) { echo " checked"; } ?> value="Estimate Report" name="reports_dashboard[]"> Estimate Report</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Estimate Stats".',') !== FALSE) { echo " checked"; } ?> value="Estimate Stats" name="reports_dashboard[]"> Estimate Stats</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Estimate Alerts".',') !== FALSE) { echo " checked"; } ?> value="Estimate Alerts" name="reports_dashboard[]"> Estimate Alerts</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Estimate Forecasting".',') !== FALSE) { echo " checked"; } ?> value="Estimate Forecasting" name="reports_dashboard[]"> Estimate Forecasting</label>
        </div>
    <div class="clearfix"></div>

    <h3>History</h3>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Staff History".',') !== FALSE) { echo " checked"; } ?> value="History Staff History" name="reports_dashboard[]"> Staff History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Checklist History".',') !== FALSE) { echo " checked"; } ?> value="History Checklist History" name="reports_dashboard[]"> Checklist History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Sales History".',') !== FALSE) { echo " checked"; } ?> value="History Sales History" name="reports_dashboard[]"> Sales History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History HR History".',') !== FALSE) { echo " checked"; } ?> value="History HR History" name="reports_dashboard[]"> HR History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Point of Sale History".',') !== FALSE) { echo " checked"; } ?> value="History Point of Sale History" name="reports_dashboard[]"> Point of Sale History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Inventory History".',') !== FALSE) { echo " checked"; } ?> value="History Inventory History" name="reports_dashboard[]"> Inventory History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Project History".',') !== FALSE) { echo " checked"; } ?> value="History Project History" name="reports_dashboard[]"> Project History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Contact History".',') !== FALSE) { echo " checked"; } ?> value="History Contact History" name="reports_dashboard[]"> Contact History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Security History".',') !== FALSE) { echo " checked"; } ?> value="History Security History" name="reports_dashboard[]"> Security History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Services History".',') !== FALSE) { echo " checked"; } ?> value="History Services History" name="reports_dashboard[]"> Services History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Expenses History".',') !== FALSE) { echo " checked"; } ?> value="History Expenses History" name="reports_dashboard[]"> Expenses History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Products History".',') !== FALSE) { echo " checked"; } ?> value="History Products History" name="reports_dashboard[]"> Products History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History DB Backup History".',') !== FALSE) { echo " checked"; } ?> value="History DB Backup History" name="reports_dashboard[]"> DB Backup History</label>
        </div>
      <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Field Jobs History".',') !== FALSE) { echo " checked"; } ?> value="History Field Jobs History" name="reports_dashboard[]"> Field Jobs History</label>
        </div>
    <div class="clearfix"></div>

    <h3>Marketing</h3>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."CRM Recommendations - By Customer".',') !== FALSE) { echo " checked"; } ?> value="CRM Recommendations - By Customer" name="reports_dashboard[]"> CRM Recommendations - By Customer</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Cart Abandonment".',') !== FALSE) { echo " checked"; } ?> value="Cart Abandonment" name="reports_dashboard[]"> Cart Abandonment</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Referral".',') !== FALSE) { echo " checked"; } ?> value="Referral" name="reports_dashboard[]"> Referrals</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."CRM Recommendations - By Date".',') !== FALSE) { echo " checked"; } ?> value="CRM Recommendations - By Date" name="reports_dashboard[]"> CRM Recommendations - By Date</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Demographics".',') !== FALSE) { echo " checked"; } ?> value="Demographics" name="reports_dashboard[]"> Demographics</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Web Referrals Report".',') !== FALSE) { echo " checked"; } ?> value="Web Referrals Report" name="reports_dashboard[]"> Web Referrals Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Contact List".',') !== FALSE) { echo " checked"; } ?> value="Customer Contact List" name="reports_dashboard[]"> Customer Contact List</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."POS Coupons".',') !== FALSE) { echo " checked"; } ?> value="POS Coupons" name="reports_dashboard[]"> POS Coupons</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Pro Bono Report".',') !== FALSE) { echo " checked"; } ?> value="Pro Bono Report" name="reports_dashboard[]"> Pro-Bono</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Customer Stats".',') !== FALSE) { echo " checked"; } ?> value="Customer Stats" name="reports_dashboard[]"> Customer Stats</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Postal Code".',') !== FALSE) { echo " checked"; } ?> value="Postal Code" name="reports_dashboard[]"> Postal Code</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Contact Postal Code".',') !== FALSE) { echo " checked"; } ?> value="Contact Postal Code" name="reports_dashboard[]"> Contact Postal Code</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Site Visitors".',') !== FALSE) { echo " checked"; } ?> value="Site Visitors" name="reports_dashboard[]"> Website Visitors</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Net Promoter Score".',') !== FALSE) { echo " checked"; } ?> value="Net Promoter Score" name="reports_dashboard[]"> Net Promoter Score</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Driver Report".',') !== FALSE) { echo " checked"; } ?> value="Driver Report" name="reports_dashboard[]"> Driver Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Contact Report by Status".',') !== FALSE) { echo " checked"; } ?> value="Contact Report by Status" name="reports_dashboard[]"> Contact Report by Status</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Promotions and Coupons".',') !== FALSE) { echo " checked"; } ?> value="Promotions and Coupons" name="reports_dashboard[]"> Promotions &amp; Coupons Usage</label>
        </div>
    <div class="clearfix"></div>

    <h3>Operations</h3>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Daysheet".',') !== FALSE) { echo " checked"; } ?> value="Daysheet" name="reports_dashboard[]"> Therapist Day Sheet</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Appointment Summary".',') !== FALSE) { echo " checked"; } ?> value="Appointment Summary" name="reports_dashboard[]"> Appointment Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Ticket Attached".',') !== FALSE) { echo " checked"; } ?> value="Ticket Attached" name="reports_dashboard[]"> Attached to <?= TICKET_TILE ?></label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Therapist Stats".',') !== FALSE) { echo " checked"; } ?> value="Therapist Stats" name="reports_dashboard[]"> Therapist Stats</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Patient Block Booking".',') !== FALSE) { echo " checked"; } ?> value="Patient Block Booking" name="reports_dashboard[]"> Block Booking</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Drop Off Analysis".',') !== FALSE) { echo " checked"; } ?> value="Drop Off Analysis" name="reports_dashboard[]"> Drop Off Analysis</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Block Booking vs Not Block Booking".',') !== FALSE) { echo " checked"; } ?> value="Block Booking vs Not Block Booking" name="reports_dashboard[]"> Block Booking vs Not Block Booking</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Assessment Tally Board".',') !== FALSE) { echo " checked"; } ?> value="Assessment Tally Board" name="reports_dashboard[]"> Assessment Tally Board</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Discharge Report".',') !== FALSE) { echo " checked"; } ?> value="Discharge Report" name="reports_dashboard[]"> Discharge Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Injury Type".',') !== FALSE) { echo " checked"; } ?> value="Injury Type" name="reports_dashboard[]"> Injury Type</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Assessment Follow Up".',') !== FALSE) { echo " checked"; } ?> value="Assessment Follow Up" name="reports_dashboard[]"> Assessment Follow Ups</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Ticket Report".',') !== FALSE) { echo " checked"; } ?> value="Ticket Report" name="reports_dashboard[]"> <?= TICKET_NOUN ?> Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Action Item Summary".',') !== FALSE) { echo " checked"; } ?> value="Action Item Summary" name="reports_dashboard[]"> Action Item Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Treatment Report".',') !== FALSE) { echo " checked"; } ?> value="Treatment Report" name="reports_dashboard[]"> Treatment Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Field Jobs".',') !== FALSE) { echo " checked"; } ?> value="Field Jobs" name="reports_dashboard[]"> Field Jobs</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Site Work Time".',') !== FALSE) { echo " checked"; } ?> value="Site Work Time" name="reports_dashboard[]"> Time on Site</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Equipment List".',') !== FALSE) { echo " checked"; } ?> value="Equipment List" name="reports_dashboard[]"> Equipment List</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Shop Work Orders".',') !== FALSE) { echo " checked"; } ?> value="Shop Work Orders" name="reports_dashboard[]"> Shop Work Orders</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Site Work Driving".',') !== FALSE) { echo " checked"; } ?> value="Site Work Driving" name="reports_dashboard[]"> Safety Driving Logs</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Shop Work Order Task Time".',') !== FALSE) { echo " checked"; } ?> value="Shop Work Order Task Time" name="reports_dashboard[]"> Shop Work Order Task Time</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Purchase Orders".',') !== FALSE) { echo " checked"; } ?> value="Purchase Orders" name="reports_dashboard[]"> Purchase Orders</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Shop Work Order Time".',') !== FALSE) { echo " checked"; } ?> value="Shop Work Order Time" name="reports_dashboard[]"> Shop Work Order Time</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Site Work Orders".',') !== FALSE) { echo " checked"; } ?> value="Site Work Orders" name="reports_dashboard[]"> Site Work Orders</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Inventory Log".',') !== FALSE) { echo " checked"; } ?> value="Inventory Log" name="reports_dashboard[]"> Inventory Log</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Equipment Transfer".',') !== FALSE) { echo " checked"; } ?> value="Equipment Transfer" name="reports_dashboard[]"> Equipment Transfer History</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Scrum Business Productivity Summary".',') !== FALSE) { echo " checked"; } ?> value="Scrum Business Productivity Summary" name="reports_dashboard[]"> Scrum Business Productivity Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Work Order".',') !== FALSE) { echo " checked"; } ?> value="Work Order" name="reports_dashboard[]"> Work Order</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Scrum Staff Productivity Summary".',') !== FALSE) { echo " checked"; } ?> value="Scrum Staff Productivity Summary" name="reports_dashboard[]"> Scrum Staff Productivity Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."POS".',') !== FALSE) { echo " checked"; } ?> value="POS" name="reports_dashboard[]"> <?= POS_ADVANCE_TILE ?></label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Staff Tickets".',') !== FALSE) { echo " checked"; } ?> value="Staff Tickets" name="reports_dashboard[]"> Staff <?= TICKET_TILE ?></label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Scrum Status Report".',') !== FALSE) { echo " checked"; } ?> value="Scrum Status Report" name="reports_dashboard[]"> Scrum Status Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Credit Card on File".',') !== FALSE) { echo " checked"; } ?> value="Credit Card on File" name="reports_dashboard[]"> Credit Card on File</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Day Sheet Report".',') !== FALSE) { echo " checked"; } ?> value="Day Sheet Report" name="reports_dashboard[]"> Day Sheet Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Service Usage Report".',') !== FALSE) { echo " checked"; } ?> value="Service Usage Report" name="reports_dashboard[]"> % Breakdown of Services Sold</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Checklist Time".',') !== FALSE) { echo " checked"; } ?> value="Checklist Time" name="reports_dashboard[]"> Checklist Time Tracking</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Ticket Time Summary".',') !== FALSE) { echo " checked"; } ?> value="Ticket Time Summary" name="reports_dashboard[]"> <?= TICKET_NOUN ?> Time Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Download Tracker".',') !== FALSE) { echo " checked"; } ?> value="Download Tracker" name="reports_dashboard[]"> Download Tracker</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Tasklist Time".',') !== FALSE) { echo " checked"; } ?> value="Tasklist Time" name="reports_dashboard[]"> Task Time Tracking</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Ticket Deleted Notes".',') !== FALSE) { echo " checked"; } ?> value="Ticket Deleted Notes" name="reports_dashboard[]"> Archived <?= TICKET_NOUN ?> Notes</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Ticket Inventory Transport".',') !== FALSE) { echo " checked"; } ?> value="Ticket Inventory Transport" name="reports_dashboard[]"> <?= TICKET_NOUN ?> Transport of Inventory</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Dispatch Travel Time".',') !== FALSE) { echo " checked"; } ?> value="Dispatch Travel Time" name="reports_dashboard[]"> Dispatch <?= TICKET_NOUN ?> Travel Time</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Time Sheet".',') !== FALSE) { echo " checked"; } ?> value="Time Sheet" name="reports_dashboard[]"> Time Sheets Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Ticket Activity Report".',') !== FALSE) { echo " checked"; } ?> value="Ticket Activity Report" name="reports_dashboard[]"> <?= TICKET_NOUN ?> Activity Report per Customer</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Ticket Activity Extra Report".',') !== FALSE) { echo " checked"; } ?> value="Ticket Activity Extra Report" name="reports_dashboard[]"> <?= TICKET_NOUN ?> Activity Report for Extra Billing</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Ticket by Task".',') !== FALSE) { echo " checked"; } ?> value="Ticket by Task" name="reports_dashboard[]"> <?= TICKET_NOUN ?> by Task</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Rate Card Report".',') !== FALSE) { echo " checked"; } ?> value="Rate Card Report" name="reports_dashboard[]"> Rate Cards Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Import Summary".',') !== FALSE) { echo " checked"; } ?> value="Import Summary" name="reports_dashboard[]"> Import Summary Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Import Details".',') !== FALSE) { echo " checked"; } ?> value="Import Details" name="reports_dashboard[]"> Detailed Import Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Ticket Manifest Summary".',') !== FALSE) { echo " checked"; } ?> value="Ticket Manifest Summary" name="reports_dashboard[]"> Manifest Daily Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Employee Payroll Report".',') !== FALSE) { echo " checked"; } ?> value="Employee Payroll Report" name="reports_dashboard[]"> Employee Payroll Report</label>
        </div>
	<div class="clearfix"></div>


    <h3>Profit &amp; Loss</h3>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ',Revenue Receivables,') !== FALSE) { echo " checked"; } ?> value="Revenue Receivables" name="reports_dashboard[]"> Revenue &amp; Receivables</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ',Expenses,') !== FALSE) { echo " checked"; } ?> value="Expenses" name="reports_dashboard[]"> Expenses</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ',Summary,') !== FALSE) { echo " checked"; } ?> value="Summary" name="reports_dashboard[]"> Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ',Staff Compensation,') !== FALSE) { echo " checked"; } ?> value="Staff Compensation" name="reports_dashboard[]"> Staff &amp; Compensation</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ',Costs,') !== FALSE) { echo " checked"; } ?> value="Costs" name="reports_dashboard[]"> Costs</label>
        </div>

        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ',Labour Report,') !== FALSE) { echo " checked"; } ?> value="Labour Report" name="reports_dashboard[]"> Labour Report</label>
        </div>

        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ',Dollars By Service,') !== FALSE) { echo " checked"; } ?> value="Dollars By Service" name="reports_dashboard[]"> Dollars By Service</label>
        </div>
    <div class="clearfix"></div>

	<h3>Sales</h3>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Validation by Therapist".',') !== FALSE) { echo " checked"; } ?> value="Validation by Therapist" name="reports_dashboard[]"> Validation by Therapist</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Sales Summary by Injury Type".',') !== FALSE) { echo " checked"; } ?> value="Sales Summary by Injury Type" name="reports_dashboard[]"> Sales Summary by Injury Type</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Gross Revenue by Staff".',') !== FALSE) { echo " checked"; } ?> value="Gross Revenue by Staff" name="reports_dashboard[]"> Gross Revenue by Staff</label>
        </div>
        <!-- <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."POS Validation".',') !== FALSE) { echo " checked"; } ?> value="POS Validation" name="reports_dashboard[]"> POS (Basic) Validation</label>
        </div> -->
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Inventory Analysis".',') !== FALSE) { echo " checked"; } ?> value="Inventory Analysis" name="reports_dashboard[]"> Inventory Analysis</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Patient Invoices".',') !== FALSE) { echo " checked"; } ?> value="Patient Invoices" name="reports_dashboard[]"> Customer Invoices</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."POS Advanced Validation".',') !== FALSE) { echo " checked"; } ?> value="POS Advanced Validation" name="reports_dashboard[]"> POS Validation</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Unassigned/Error Invoices".',') !== FALSE) { echo " checked"; } ?> value="Unassigned/Error Invoices" name="reports_dashboard[]"> Unassigned/Error Invoices</label>
        </div>
        <!-- <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."POS Sales Summary".',') !== FALSE) { echo " checked"; } ?> value="POS Sales Summary" name="reports_dashboard[]"> POS (Basic) Sales Summary</label>
        </div> -->
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Phone Communication".',') !== FALSE) { echo " checked"; } ?> value="Phone Communication" name="reports_dashboard[]"> Phone Communication</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Staff Revenue Report".',') !== FALSE) { echo " checked"; } ?> value="Staff Revenue Report" name="reports_dashboard[]"> Staff Revenue Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."POS Advanced Sales Summary".',') !== FALSE) { echo " checked"; } ?> value="POS Advanced Sales Summary" name="reports_dashboard[]"> POS Sales Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Daily Deposit Report".',') !== FALSE) { echo " checked"; } ?> value="Daily Deposit Report" name="reports_dashboard[]"> Daily Deposit Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Expense Summary Report".',') !== FALSE) { echo " checked"; } ?> value="Expense Summary Report" name="reports_dashboard[]"> Expense Summary Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Profit-Loss".',') !== FALSE) { echo " checked"; } ?> value="Profit-Loss" name="reports_dashboard[]"> Profit-Loss</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Monthly Sales by Injury Type".',') !== FALSE) { echo " checked"; } ?> value="Monthly Sales by Injury Type" name="reports_dashboard[]"> Monthly Sales by Injury Type</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Sales by Inventory/Service Detail".',') !== FALSE) { echo " checked"; } ?> value="Sales by Inventory/Service Detail" name="reports_dashboard[]"> Sales by Inventory/Service Detail</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Profit-Loss POS Advanced".',') !== FALSE) { echo " checked"; } ?> value="Profit-Loss POS Advanced" name="reports_dashboard[]"> Profit-Loss (POS Advanced)</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Invoice Sales Summary".',') !== FALSE) { echo " checked"; } ?> value="Invoice Sales Summary" name="reports_dashboard[]"> Invoice Sales Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Payment Method List".',') !== FALSE) { echo " checked"; } ?> value="Payment Method List" name="reports_dashboard[]"> Payment Method List</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Transaction List by Customer".',') !== FALSE) { echo " checked"; } ?> value="Transaction List by Customer" name="reports_dashboard[]"> Transaction List by Customer</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Sales by Customer Summary".',') !== FALSE) { echo " checked"; } ?> value="Sales by Customer Summary" name="reports_dashboard[]"> Sales by Customer Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Patient History".',') !== FALSE) { echo " checked"; } ?> value="Patient History" name="reports_dashboard[]"> Customer History</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Unbilled Invoices".',') !== FALSE) { echo " checked"; } ?> value="Unbilled Invoices" name="reports_dashboard[]"> Unbilled Invoices</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Sales History by Customer".',') !== FALSE) { echo " checked"; } ?> value="Sales History by Customer" name="reports_dashboard[]"> Sales History by Customer</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Sales by Service Category".',') !== FALSE) { echo " checked"; } ?> value="Sales by Service Category" name="reports_dashboard[]"> Sales by Service Category</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Deposit Detail".',') !== FALSE) { echo " checked"; } ?> value="Deposit Detail" name="reports_dashboard[]"> Deposit Detail</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Sales by Service Summary".',') !== FALSE) { echo " checked"; } ?> value="Sales by Service Summary" name="reports_dashboard[]"> Sales by Service Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Sales Estimates".',') !== FALSE) { echo " checked"; } ?> value="Sales Estimates" name="reports_dashboard[]"> Sales Estimates</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Sales by Inventory Summary".',') !== FALSE) { echo " checked"; } ?> value="Sales by Inventory Summary" name="reports_dashboard[]"> Sales by Inventory Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Receipts Summary Report".',') !== FALSE) { echo " checked"; } ?> value="Receipts Summary Report" name="reports_dashboard[]"> Receipts Summary Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Project Progress Report".',') !== FALSE) { echo " checked"; } ?> value="Project Progress Report" name="reports_dashboard[]"> Project Progress Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Estimate Item Closing % By Quantity".',') !== FALSE) { echo " checked"; } ?> value="Estimate Item Closing % By Quantity" name="reports_dashboard[]"> Estimate Item Closing % By Quantity</label>
        </div>
    <div class="clearfix"></div>

    <h3>Staff</h3>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Staff Staff Tickets".',') !== FALSE) { echo " checked"; } ?> value="Staff Staff Tickets" name="reports_dashboard[]"> Staff Tickets</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Staff Scrum Staff Productivity Summary".',') !== FALSE) { echo " checked"; } ?> value="Staff Scrum Staff Productivity Summary" name="reports_dashboard[]"> Scrum Staff Productivity Summary</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Staff Daysheet".',') !== FALSE) { echo " checked"; } ?> value="Staff Daysheet" name="reports_dashboard[]"> Therapist Day Sheet</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Staff Therapist Stats".',') !== FALSE) { echo " checked"; } ?> value="Staff Therapist Stats" name="reports_dashboard[]"> Therapist Stats</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Staff Day Sheet Report".',') !== FALSE) { echo " checked"; } ?> value="Staff Day Sheet Report" name="reports_dashboard[]"> Day Sheet Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Staff Staff Revenue Report".',') !== FALSE) { echo " checked"; } ?> value="Staff Staff Revenue Report" name="reports_dashboard[]"> Staff Revenue Report</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Staff Gross Revenue by Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff Gross Revenue by Staff" name="reports_dashboard[]"> Gross Revenue by Staff</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Staff Validation by Therapist".',') !== FALSE) { echo " checked"; } ?> value="Staff Validation by Therapist" name="reports_dashboard[]"> Validation by Therapist</label>
        </div>
        <div class="col-sm-4">
            <label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."Staff Staff Compensation".',') !== FALSE) { echo " checked"; } ?> value="Staff Staff Compensation" name="reports_dashboard[]"> Staff Compensation</label>
        </div>
    <div class="clearfix"></div>

  <h3>History</h3>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Staff History".',') !== FALSE) { echo " checked"; } ?> value="History Staff History" name="reports_dashboard[]"> Staff History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Checklist History".',') !== FALSE) { echo " checked"; } ?> value="History Checklist History" name="reports_dashboard[]"> Checklist History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Sales History".',') !== FALSE) { echo " checked"; } ?> value="History Sales History" name="reports_dashboard[]"> Sales History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History HR History".',') !== FALSE) { echo " checked"; } ?> value="History HR History" name="reports_dashboard[]"> HR History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Point of Sale History".',') !== FALSE) { echo " checked"; } ?> value="History Point of Sale History" name="reports_dashboard[]"> Point of Sale History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Inventory History".',') !== FALSE) { echo " checked"; } ?> value="History Inventory History" name="reports_dashboard[]"> Inventory History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Project History".',') !== FALSE) { echo " checked"; } ?> value="History Project History" name="reports_dashboard[]"> Project History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Contact History".',') !== FALSE) { echo " checked"; } ?> value="History Contact History" name="reports_dashboard[]"> Contact History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Security History".',') !== FALSE) { echo " checked"; } ?> value="History Security History" name="reports_dashboard[]"> Security History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Equipment History".',') !== FALSE) { echo " checked"; } ?> value="History Equipment History" name="reports_dashboard[]"> Equipment History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Vendor Price List History".',') !== FALSE) { echo " checked"; } ?> value="History Vendor Price List History" name="reports_dashboard[]"> Vendor Price List History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Rate Card History".',') !== FALSE) { echo " checked"; } ?> value="History Rate Card History" name="reports_dashboard[]"> Rate Card History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Purchase Order History".',') !== FALSE) { echo " checked"; } ?> value="History Purchase Order History" name="reports_dashboard[]"> Purchase Order History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Intake History".',') !== FALSE) { echo " checked"; } ?> value="History Intake History" name="reports_dashboard[]"> Intake History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Services History".',') !== FALSE) { echo " checked"; } ?> value="History Services History" name="reports_dashboard[]"> Services History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Estimates History".',') !== FALSE) { echo " checked"; } ?> value="History Estimates History" name="reports_dashboard[]"> Estimates History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Expenses History".',') !== FALSE) { echo " checked"; } ?> value="History Expenses History" name="reports_dashboard[]"> Expenses History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Products History".',') !== FALSE) { echo " checked"; } ?> value="History Products History" name="reports_dashboard[]"> Products History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Safety History".',') !== FALSE) { echo " checked"; } ?> value="History Safety History" name="reports_dashboard[]"> Safety History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History DB Backup History".',') !== FALSE) { echo " checked"; } ?> value="History DB Backup History" name="reports_dashboard[]"> DB Backup History</label>
	</div>
  <div class="col-sm-4">
		<label class="form-checkbox-any"><input type="checkbox" <?php if (strpos($value_config, ','."History Field Jobs History".',') !== FALSE) { echo " checked"; } ?> value="History Field Jobs History" name="reports_dashboard[]"> Field Jobs History</label>
	</div>

  <div class="clearfix"></div>

    <div class="form-group pull-right">
        <a href="report_tiles.php" class="btn brand-btn">Back</a>
        <button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
    </div>

</form>
