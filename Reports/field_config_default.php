<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('report');
error_reporting(0);

if (isset($_POST['submit'])) {
    //Default Sub Tab
    $mobile_landing_subtab = ( !empty($_POST['mobile_landing_subtab']) ) ? filter_var($_POST['mobile_landing_subtab'], FILTER_SANITIZE_STRING) : '';
    $desktop_landing_subtab = ( !empty($_POST['desktop_landing_subtab']) ) ? filter_var($_POST['desktop_landing_subtab'], FILTER_SANITIZE_STRING) : '';

    $mobile_landing_subtab_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE `name`='reports_mobile_landing_subtab'"));
    if($mobile_landing_subtab_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET `value`='$mobile_landing_subtab' WHERE `name`='reports_mobile_landing_subtab'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('reports_mobile_landing_subtab', '$mobile_landing_subtab')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $desktop_landing_subtab_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE `name`='reports_desktop_landing_subtab'"));
    if($desktop_landing_subtab_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET `value`='$desktop_landing_subtab' WHERE `name`='reports_desktop_landing_subtab'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('reports_desktop_landing_subtab', '$desktop_landing_subtab')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    echo '<script type="text/javascript"> window.location.replace("?tab='.$_GET['tab'].'"); </script>';
}
?>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <input type="hidden" name="contactid" value="<?php echo $_GET['contactid'] ?>" />

    <div class="form-group">
        <label class="col-sm-4 control-label">Mobile Default Report</label>
        <div class="col-sm-8"><?php
            $mobile_landing_subtab_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE `name`='reports_mobile_landing_subtab'"));
            $desktop_landing_subtab_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE `name`='reports_desktop_landing_subtab'")); ?>
            <select name="mobile_landing_subtab" class="form-control chosen-select-deselect" data-placeholder="Select Sub Tab...">
                <option value=""></option>
                <optgroup label="Operations">
                    <option value="Daysheet" <?= $mobile_landing_subtab_config['value']=='Daysheet' ? 'selected="selected"' : '' ?>>Therapist Day Sheet</option>
                    <option value="Therapist Stats" <?= $mobile_landing_subtab_config['value']=='Therapist Stats' ? 'selected="selected"' : '' ?>>Therapist Stats</option>
                    <option value="Block Booking vs Not Block Booking" <?= $mobile_landing_subtab_config['value']=='Block Booking vs Not Block Booking' ? 'selected="selected"' : '' ?>>Block Booking vs Not Block Booking</option>
                    <option value="Injury Type" <?= $mobile_landing_subtab_config['value']=='Injury Type' ? 'selected="selected"' : '' ?>>Injury Type</option>
                    <option value="Treatment Report" <?= $mobile_landing_subtab_config['value']=='Treatment Report' ? 'selected="selected"' : '' ?>>Treatment Report</option>
                    <option value="Equipment List" <?= $mobile_landing_subtab_config['value']=='Equipment List' ? 'selected="selected"' : '' ?>>Equipment List</option>
                    <option value="Shop Work Order Task Time" <?= $mobile_landing_subtab_config['value']=='Shop Work Order Task Time' ? 'selected="selected"' : '' ?>>Shop Work Order Task Time</option>
                    <option value="Site Work Orders" <?= $mobile_landing_subtab_config['value']=='Site Work Orders' ? 'selected="selected"' : '' ?>>Site Work Orders</option>
                    <option value="Scrum Business Productivity Summary" <?= $mobile_landing_subtab_config['value']=='Scrum Business Productivity Summary' ? 'selected="selected"' : '' ?>>Scrum Business Productivity Summary</option>
                    <option value="Scrum Staff Productivity Summary" <?= $mobile_landing_subtab_config['value']=='Scrum Staff Productivity Summary' ? 'selected="selected"' : '' ?>>Scrum Staff Productivity Summary</option>
                    <option value="Scrum Status Report" <?= $mobile_landing_subtab_config['value']=='Scrum Status Report' ? 'selected="selected"' : '' ?>>Scrum Status Report</option>
                    <option value="Service Usage Report" <?= $mobile_landing_subtab_config['value']=='Service Usage Report' ? 'selected="selected"' : '' ?>>% Breakdown of Services Sold</option>
                    <option value="Download Tracker" <?= $mobile_landing_subtab_config['value']=='Download Tracker' ? 'selected="selected"' : '' ?>>Download Tracker</option>
                    <option value="Appointment Summary" <?= $mobile_landing_subtab_config['value']=='Appointment Summary' ? 'selected="selected"' : '' ?>>Appointment Summary</option>
                    <option value="Patient Block Booking" <?= $mobile_landing_subtab_config['value']=='Patient Block Booking' ? 'selected="selected"' : '' ?>>Block Booking</option>
                    <option value="Assessment Tally Board" <?= $mobile_landing_subtab_config['value']=='Assessment Tally Board' ? 'selected="selected"' : '' ?>>Assessment Tally Board</option>
                    <option value="Assessment Follow Up" <?= $mobile_landing_subtab_config['value']=='Assessment Follow Up' ? 'selected="selected"' : '' ?>>Assessment Follow Ups</option>
                    <option value="Field Jobs" <?= $mobile_landing_subtab_config['value']=='Field Jobs' ? 'selected="selected"' : '' ?>>Field Jobs</option>
                    <option value="Shop Work Orders" <?= $mobile_landing_subtab_config['value']=='Shop Work Orders' ? 'selected="selected"' : '' ?>>Shop Work Orders</option>
                    <option value="Purchase Orders" <?= $mobile_landing_subtab_config['value']=='Purchase Orders' ? 'selected="selected"' : '' ?>>Purchase Orders</option>
                    <option value="Inventory Log" <?= $mobile_landing_subtab_config['value']=='Inventory Log' ? 'selected="selected"' : '' ?>>Inventory Log</option>
                    <option value="POS" <?= $mobile_landing_subtab_config['value']=='POS' ? 'selected="selected"' : '' ?>><?= POS_ADVANCE_TILE ?></option>
                    <option value="Credit Card on File" <?= $mobile_landing_subtab_config['value']=='Credit Card on File' ? 'selected="selected"' : '' ?>>Credit Card on File</option>
                    <option value="Checklist Time" <?= $mobile_landing_subtab_config['value']=='Checklist Time' ? 'selected="selected"' : '' ?>>Checklist Time Tracking</option>
                    <option value="Tasklist Time" <?= $mobile_landing_subtab_config['value']=='Tasklist Time' ? 'selected="selected"' : '' ?>>Task Time Tracking</option>
                    <option value="Ticket Attached" <?= $mobile_landing_subtab_config['value']=='Ticket Attached' ? 'selected="selected"' : '' ?>>Attached to <?= TICKET_TILE ?></option>
                    <option value="Drop Off Analysis" <?= $mobile_landing_subtab_config['value']=='Drop Off Analysis' ? 'selected="selected"' : '' ?>>Drop Off Analysis</option>
                    <option value="Discharge Report" <?= $mobile_landing_subtab_config['value']=='Discharge Report' ? 'selected="selected"' : '' ?>>Discharge Report</option>
                    <option value="Ticket Report" <?= $mobile_landing_subtab_config['value']=='Ticket Report' ? 'selected="selected"' : '' ?>><?= TICKET_NOUN ?> Report</option>
                    <option value="Action Item Summary" <?= $mobile_landing_subtab_config['value']=='Action Item Summary' ? 'selected="selected"' : '' ?>>Action Item Summary</option>
                    <option value="Site Work Time" <?= $mobile_landing_subtab_config['value']=='Site Work Time' ? 'selected="selected"' : '' ?>>Site Work Order Time on Site</option>
                    <option value="Site Work Driving" <?= $mobile_landing_subtab_config['value']=='Site Work Driving' ? 'selected="selected"' : '' ?>>Site Work Order Driving Logs</option>
                    <option value="Shop Work Order Time" <?= $mobile_landing_subtab_config['value']=='Shop Work Order Time' ? 'selected="selected"' : '' ?>>Shop Work Order Time</option>
                    <option value="Equipment Transfer" <?= $mobile_landing_subtab_config['value']=='Equipment Transfer' ? 'selected="selected"' : '' ?>>Equipment Transfer History</option>
                    <option value="Work Order" <?= $mobile_landing_subtab_config['value']=='Work Order' ? 'selected="selected"' : '' ?>>Work Order</option>
                    <option value="Staff Tickets" <?= $mobile_landing_subtab_config['value']=='Staff Tickets' ? 'selected="selected"' : '' ?>>Staff <?= TICKET_TILE ?></option>
                    <option value="Day Sheet Report" <?= $mobile_landing_subtab_config['value']=='Day Sheet Report' ? 'selected="selected"' : '' ?>>Day Sheet Report</option>
                    <option value="Ticket Time Summary" <?= $mobile_landing_subtab_config['value']=='Ticket Time Summary' ? 'selected="selected"' : '' ?>><?= TICKET_NOUN ?> Time Summary</option>
                    <option value="Ticket Deleted Notes" <?= $mobile_landing_subtab_config['value']=='Ticket Deleted Notes' ? 'selected="selected"' : '' ?>>Archived <?= TICKET_NOUN ?> Notes</option>
                    <option value="Ticket Activity Report" <?= $mobile_landing_subtab_config['value']=='Ticket Activity Report' ? 'selected="selected"' : '' ?>><?= TICKET_NOUN ?> Activity Report per Customer</option>
                    <option value="Ticket Activity Extra Report" <?= $mobile_landing_subtab_config['value']=='Ticket Activity Extra Report' ? 'selected="selected"' : '' ?>><?= TICKET_NOUN ?> Activity Report for Extra Billing</option>
                    <option value="Rate Card Report" <?= $mobile_landing_subtab_config['value']=='Rate Card Report' ? 'selected="selected"' : '' ?>>Rate Cards Report</option>
                    <option value="Import Summary" <?= $mobile_landing_subtab_config['value']=='Import Summary' ? 'selected="selected"' : '' ?>>Import Summary Report</option>
                    <option value="Import Details" <?= $mobile_landing_subtab_config['value']=='Import Details' ? 'selected="selected"' : '' ?>>Detailed Import Report</option>
                    <option value="Ticket Manifest Summary" <?= $mobile_landing_subtab_config['value']=='Ticket Manifest Summary' ? 'selected="selected"' : '' ?>>Manifest Daily Summary</option>
                    <option value="Employee Payroll Report" <?= $mobile_landing_subtab_config['value']=='Employee Payroll Report' ? 'selected="selected"' : '' ?>>Employee Payroll Report</option>
                </optgroup>
                <optgroup label="Sales">
                    <option value="Validation by Therapist" <?= $mobile_landing_subtab_config['value']=='Validation by Therapist' ? 'selected="selected"' : '' ?>>Validation by Therapist</option>
                    <!-- <option value="POS Validation" <?= $mobile_landing_subtab_config['value']=='POS Validation' ? 'selected="selected"' : '' ?>>POS (Basic) Validation</option> -->
                    <option value="POS Advanced Validation" <?= $mobile_landing_subtab_config['value']=='POS Advanced Validation' ? 'selected="selected"' : '' ?>>POS Validation</option>
                    <option value="Phone Communication" <?= $mobile_landing_subtab_config['value']=='Phone Communication' ? 'selected="selected"' : '' ?>>Phone Communication</option>
                    <option value="Daily Deposit Report" <?= $mobile_landing_subtab_config['value']=='Daily Deposit Report' ? 'selected="selected"' : '' ?>>Daily Deposit Report</option>
                    <option value="Monthly Sales by Injury Type" <?= $mobile_landing_subtab_config['value']=='Monthly Sales by Injury Type' ? 'selected="selected"' : '' ?>>Monthly Sales by Injury Type</option>
                    <option value="Invoice Sales Summary" <?= $mobile_landing_subtab_config['value']=='Invoice Sales Summary' ? 'selected="selected"' : '' ?>>Invoice Sales Summary</option>
                    <option value="Sales by Customer Summary" <?= $mobile_landing_subtab_config['value']=='Sales by Customer Summary' ? 'selected="selected"' : '' ?>>Sales by Customer Summary</option>
                    <option value="Sales History by Customer" <?= $mobile_landing_subtab_config['value']=='Sales History by Customer' ? 'selected="selected"' : '' ?>>Sales History by Customer</option>
                    <option value="Sales by Service Summary" <?= $mobile_landing_subtab_config['value']=='Sales by Service Summary' ? 'selected="selected"' : '' ?>>Sales by Service Summary</option>
                    <option value="Sales by Inventory Summary" <?= $mobile_landing_subtab_config['value']=='Sales by Inventory Summary' ? 'selected="selected"' : '' ?>>Sales by Inventory Summary</option>
                    <option value="Sales Summary by Injury Type" <?= $mobile_landing_subtab_config['value']=='Sales Summary by Injury Type' ? 'selected="selected"' : '' ?>>Sales Summary by Injury Type</option>
                    <option value="Inventory Analysis" <?= $mobile_landing_subtab_config['value']=='Inventory Analysis' ? 'selected="selected"' : '' ?>>Inventory Analysis</option>
                    <option value="Unassigned/Error Invoices" <?= $mobile_landing_subtab_config['value']=='Unassigned/Error Invoices' ? 'selected="selected"' : '' ?>>Unassigned/Error Invoices</option>
                    <option value="Staff Revenue Report" <?= $mobile_landing_subtab_config['value']=='Staff Revenue Report' ? 'selected="selected"' : '' ?>>Staff Revenue Report</option>
                    <option value="Expense Summary Report" <?= $mobile_landing_subtab_config['value']=='Expense Summary Report' ? 'selected="selected"' : '' ?>>Expense Summary Report</option>
                    <option value="Sales by Inventory/Service Detail" <?= $mobile_landing_subtab_config['value']=='Sales by Inventory/Service Detail' ? 'selected="selected"' : '' ?>>Sales by Inventory/Service Detail</option>
                    <option value="Payment Method List" <?= $mobile_landing_subtab_config['value']=='Payment Method List' ? 'selected="selected"' : '' ?>>Payment Method List</option>
                    <option value="Patient History" <?= $mobile_landing_subtab_config['value']=='Patient History' ? 'selected="selected"' : '' ?>>Customer History</option>
                    <option value="Sales by Service Category" <?= $mobile_landing_subtab_config['value']=='Sales by Service Category' ? 'selected="selected"' : '' ?>>Sales by Service Category</option>
                    <option value="Sales Estimates" <?= $mobile_landing_subtab_config['value']=='Sales Estimates' ? 'selected="selected"' : '' ?>>Sales Estimates</option>
                    <option value="Receipts Summary Report" <?= $mobile_landing_subtab_config['value']=='Receipts Summary Report' ? 'selected="selected"' : '' ?>>Receipts Summary Report</option>
                    <option value="Project Progress Report" <?= $mobile_landing_subtab_config['value']=='Project Progress Report' ? 'selected="selected"' : '' ?>>Project Progress Report</option>
                    <option value="Estimate Item Closing % By Quantity" <?= $mobile_landing_subtab_config['value']=='Estimate Item Closing % By Quantity' ? 'selected="selected"' : '' ?>>Estimate Item Closing % By Quantity</option>
                    <option value="Gross Revenue by Staff" <?= $mobile_landing_subtab_config['value']=='Gross Revenue by Staff' ? 'selected="selected"' : '' ?>>Gross Revenue by Staff</option>
                    <option value="Patient Invoices" <?= $mobile_landing_subtab_config['value']=='Patient Invoices' ? 'selected="selected"' : '' ?>>Customer Invoices</option>
                    <!-- <option value="POS Sales Summary" <?= $mobile_landing_subtab_config['value']=='POS Sales Summary' ? 'selected="selected"' : '' ?>>POS (Basic) Sales Summary</option> -->
                    <option value="POS Advanced Sales Summary" <?= $mobile_landing_subtab_config['value']=='POS Advanced Sales Summary' ? 'selected="selected"' : '' ?>>POS Sales Summary</option>
                    <option value="Profit-Loss" <?= $mobile_landing_subtab_config['value']=='Profit-Loss' ? 'selected="selected"' : '' ?>>Profit-Loss</option>
                    <option value="Profit-Loss POS Advanced" <?= $mobile_landing_subtab_config['value']=='Profit-Loss POS Advanced' ? 'selected="selected"' : '' ?>>Profit-Loss (POS Advanced)</option>
                    <option value="Transaction List by Customer" <?= $mobile_landing_subtab_config['value']=='Transaction List by Customer' ? 'selected="selected"' : '' ?>>Transaction List by Customer</option>
                    <option value="Unbilled Invoices" <?= $mobile_landing_subtab_config['value']=='Unbilled Invoices' ? 'selected="selected"' : '' ?>>Unbilled Invoices</option>
                    <option value="Deposit Detail" <?= $mobile_landing_subtab_config['value']=='Deposit Detail' ? 'selected="selected"' : '' ?>>Deposit Detail</option>
                </optgroup>
                <optgroup label="Accounts Receivable">
                    <option value="A/R Aging Summary" <?= $mobile_landing_subtab_config['value']=='A/R Aging Summary' ? 'selected="selected"' : '' ?>>A/R Aging Summary</option>
                    <option value="Patient Aging Receivable Summary" <?= $mobile_landing_subtab_config['value']=='Patient Aging Receivable Summary' ? 'selected="selected"' : '' ?>>Customer Aging Receivable Summary</option>
                    <option value="Insurer Aging Receivable Summary" <?= $mobile_landing_subtab_config['value']=='Insurer Aging Receivable Summary' ? 'selected="selected"' : '' ?>>Insurer Aging Receivable Summary</option>
                    <option value="By Invoice#" <?= $mobile_landing_subtab_config['value']=='By Invoice#' ? 'selected="selected"' : '' ?>>By Invoice#</option>
                    <option value="Customer Balance Summary" <?= $mobile_landing_subtab_config['value']=='Customer Balance Summary' ? 'selected="selected"' : '' ?>>Customer Balance Summary</option>
                    <option value="Customer Balance by Invoice" <?= $mobile_landing_subtab_config['value']=='Customer Balance by Invoice' ? 'selected="selected"' : '' ?>>Customer Balance by Invoice</option>
                    <option value="Collections Report by Customer" <?= $mobile_landing_subtab_config['value']=='Collections Report by Customer' ? 'selected="selected"' : '' ?>>Collections Report by Customer</option>
                    <option value="Invoice List" <?= $mobile_landing_subtab_config['value']=='Invoice List' ? 'selected="selected"' : '' ?>>Invoice List</option>
                    <!-- <option value="POS Receivables (Basic)" <?= $mobile_landing_subtab_config['value']=='POS Receivables (Basic)' ? 'selected="selected"' : '' ?>>POS Receivables (Basic)</option> -->
                    <option value="POS Receivables" <?= $mobile_landing_subtab_config['value']=='POS Receivables' ? 'selected="selected"' : '' ?>>POS Receivables </option>
                    <option value="UI Invoice Report" <?= $mobile_landing_subtab_config['value']=='UI Invoice Report' ? 'selected="selected"' : '' ?>>UI Invoice Report</option>
                </optgroup>
                <optgroup label="Profit & Loss">
                    <option value="Revenue Receivables" <?= $mobile_landing_subtab_config['value']=='Revenue Receivables' ? 'selected="selected"' : '' ?>>Revenue &amp; Receivables</option>
                    <option value="Staff Compensation" <?= $mobile_landing_subtab_config['value']=='Staff Compensation' ? 'selected="selected"' : '' ?>>Staff &amp; Compensation</option>
                    <option value="Expenses" <?= $mobile_landing_subtab_config['value']=='Expenses' ? 'selected="selected"' : '' ?>>Expenses</option>
                    <option value="Costs" <?= $mobile_landing_subtab_config['value']=='Costs' ? 'selected="selected"' : '' ?>>Costs</option>
                    <option value="Summary" <?= $mobile_landing_subtab_config['value']=='Summary' ? 'selected="selected"' : '' ?>>Summary</option>
                    <option value="Labour Report" <?= $mobile_landing_subtab_config['value']=='Labour Report' ? 'selected="selected"' : '' ?>>Labour Report</option>
                    <option value="Dollars By Service" <?= $mobile_landing_subtab_config['value']=='Dollars By Service' ? 'selected="selected"' : '' ?>>Dollars By Service</option>

                </optgroup>
                <optgroup label="Marketing">
                    <option value="CRM Recommendations - By Customer" <?= $mobile_landing_subtab_config['value']=='CRM Recommendations - By Customer' ? 'selected="selected"' : '' ?>>CRM Recommendations - By Customer</option>
                    <option value="CRM Recommendations - By Date" <?= $mobile_landing_subtab_config['value']=='CRM Recommendations - By Date' ? 'selected="selected"' : '' ?>>CRM Recommendations - By Date</option>
                    <option value="Customer Contact List" <?= $mobile_landing_subtab_config['value']=='Customer Contact List' ? 'selected="selected"' : '' ?>>Customer Contact List</option>
                    <option value="Customer Stats" <?= $mobile_landing_subtab_config['value']=='Customer Stats' ? 'selected="selected"' : '' ?>>Customer Stats</option>
                    <option value="Demographics" <?= $mobile_landing_subtab_config['value']=='Demographics' ? 'selected="selected"' : '' ?>>Demographics</option>
                    <option value="POS Coupons" <?= $mobile_landing_subtab_config['value']=='POS Coupons' ? 'selected="selected"' : '' ?>>POS Coupons</option>
                    <option value="Postal Code" <?= $mobile_landing_subtab_config['value']=='Postal Code' ? 'selected="selected"' : '' ?>>Postal Code</option>
                    <option value="Net Promoter Score" <?= $mobile_landing_subtab_config['value']=='Net Promoter Score' ? 'selected="selected"' : '' ?>>Net Promoter Score</option>
                    <option value="Driver Report" <?= $mobile_landing_subtab_config['value']=='Driver Report' ? 'selected="selected"' : '' ?>>Driver Report</option>
                    <option value="Contact Report by Status" <?= $mobile_landing_subtab_config['value']=='Contact Report by Status' ? 'selected="selected"' : '' ?>>Contact Report by Status</option>
                    <option value="Referral" <?= $mobile_landing_subtab_config['value']=='Referral' ? 'selected="selected"' : '' ?>>Referrals</option>
                    <option value="Web Referrals Report" <?= $mobile_landing_subtab_config['value']=='Web Referrals Report' ? 'selected="selected"' : '' ?>>Web Referrals Report</option>
                    <option value="Pro Bono Report" <?= $mobile_landing_subtab_config['value']=='Pro Bono Report' ? 'selected="selected"' : '' ?>>Pro-Bono</option>
                    <option value="Contact Postal Code" <?= $mobile_landing_subtab_config['value']=='Contact Postal Code' ? 'selected="selected"' : '' ?>>Contact Postal Code</option>
                </optgroup>
                <optgroup label="Compensation">
                    <option value="Adjustment Compensation" <?= $mobile_landing_subtab_config['value']=='Adjustment Compensation' ? 'selected="selected"' : '' ?>>Adjustment Compensation</option>
                    <option value="Compensation Print Appointment Reports" <?= $mobile_landing_subtab_config['value']=='Compensation Print Appointment Reports' ? 'selected="selected"' : '' ?>>Compensation: Print Appt. Reports Button</option>
                    <option value="Hourly Compensation" <?= $mobile_landing_subtab_config['value']=='Hourly Compensation' ? 'selected="selected"' : '' ?>>Hourly Compensation</option>
                    <option value="Therapist Compensation" <?= $mobile_landing_subtab_config['value']=='Therapist Compensation' ? 'selected="selected"' : '' ?>>Therapist Compensation</option>
                    <option value="Statutory Holiday Pay Breakdown" <?= $mobile_landing_subtab_config['value']=='Statutory Holiday Pay Breakdown' ? 'selected="selected"' : '' ?>>Statutory Holiday Pay Breakdown</option>
                    <option value="Timesheet Payroll" <?= $mobile_landing_subtab_config['value']=='Timesheet Payroll' ? 'selected="selected"' : '' ?>>Time Sheet Payroll</option>
                </optgroup>
                <optgroup label="Customer">
                    <option value="Customer Sales by Customer Summary" <?= $mobile_landing_subtab_config['value']=='Customer Sales by Customer Summary' ? 'selected="selected"' : '' ?>>Sales by Customer Summary</option>
                    <option value="Customer Sales History by Customer" <?= $mobile_landing_subtab_config['value']=='Customer Sales History by Customer' ? 'selected="selected"' : '' ?>>Sales History by Customer</option>
                    <option value="Customer Patient Invoices" <?= $mobile_landing_subtab_config['value']=='Customer Patient Invoices' ? 'selected="selected"' : '' ?>>Customer Invoices</option>
                    <option value="Customer Transaction List by Customer" <?= $mobile_landing_subtab_config['value']=='Customer Transaction List by Customer' ? 'selected="selected"' : '' ?>>Transaction List by Customer</option>
                    <option value="Customer Patient History" <?= $mobile_landing_subtab_config['value']=='Customer Patient History' ? 'selected="selected"' : '' ?>>Patient History</option>
                    <option value="Customer Customer Balance Summary" <?= $mobile_landing_subtab_config['value']=='Customer Customer Balance Summary' ? 'selected="selected"' : '' ?>>Customer Balance Summary</option>
                    <option value="Customer Customer Balance by Invoice" <?= $mobile_landing_subtab_config['value']=='Customer Customer Balance by Invoice' ? 'selected="selected"' : '' ?>>Customer Balance by Invoice</option>
                    <option value="Customer Collections Report by Customer" <?= $mobile_landing_subtab_config['value']=='Customer Collections Report by Customer' ? 'selected="selected"' : '' ?>>Collections Report by Customer</option>
                    <option value="Customer Patient Aging Receivable Summary" <?= $mobile_landing_subtab_config['value']=='Customer Patient Aging Receivable Summary' ? 'selected="selected"' : '' ?>>Patient Aging Receivable Summary</option>
                    <option value="Customer Customer Contact List" <?= $mobile_landing_subtab_config['value']=='Customer Customer Contact List' ? 'selected="selected"' : '' ?>>Customer Contact List</option>
                    <option value="Customer Customer Stats" <?= $mobile_landing_subtab_config['value']=='Customer Customer Stats' ? 'selected="selected"' : '' ?>>Customer Stats</option>
                    <option value="Customer CRM Recommendations - By Customer" <?= $mobile_landing_subtab_config['value']=='Customer CRM Recommendations - By Customer' ? 'selected="selected"' : '' ?>>CRM Recommendations - By Customer</option>
                    <option value="Customer Contact Postal Code" <?= $mobile_landing_subtab_config['value']=='Customer Contact Postal Code' ? 'selected="selected"' : '' ?>>Contact Postal Code</option>
                </optgroup>
                <optgroup label="Staff">
                    <option value="Staff Staff Tickets" <?= $mobile_landing_subtab_config['value']=='Staff Staff Tickets' ? 'selected="selected"' : '' ?>>Staff Tickets</option>
                    <option value="Staff Scrum Staff Productivity Summary" <?= $mobile_landing_subtab_config['value']=='Staff Scrum Staff Productivity Summary' ? 'selected="selected"' : '' ?>>Scrum Staff Productivity Summary</option>
                    <option value="Staff Daysheet" <?= $mobile_landing_subtab_config['value']=='Staff Daysheet' ? 'selected="selected"' : '' ?>>Therapist Day Sheet</option>
                    <option value="Staff Therapist Stats" <?= $mobile_landing_subtab_config['value']=='Staff Therapist Stats' ? 'selected="selected"' : '' ?>>Therapist Stats</option>
                    <option value="Staff Day Sheet Report" <?= $mobile_landing_subtab_config['value']=='Staff Day Sheet Report' ? 'selected="selected"' : '' ?>>Day Sheet Report</option>
                    <option value="Staff Staff Revenue Report" <?= $mobile_landing_subtab_config['value']=='Staff Staff Revenue Report' ? 'selected="selected"' : '' ?>>Staff Revenue Report</option>
                    <option value="Staff Gross Revenue by Staff" <?= $mobile_landing_subtab_config['value']=='Staff Gross Revenue by Staff' ? 'selected="selected"' : '' ?>>Gross Revenue by Staff</option>
                    <option value="Staff Validation by Therapist" <?= $mobile_landing_subtab_config['value']=='Staff Validation by Therapist' ? 'selected="selected"' : '' ?>>Validation by Therapist</option>
                    <option value="Staff Staff Compensation" <?= $mobile_landing_subtab_config['value']=='Staff Staff Compensation' ? 'selected="selected"' : '' ?>>Staff Compensation</option>
                </optgroup>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-4 control-label">Desktop Default Report</label>
        <div class="col-sm-8">
            <select name="desktop_landing_subtab" class="form-control chosen-select-deselect" data-placeholder="Select Sub Tab...">
                <option value=""></option>
                <optgroup label="Operations">
                    <option value="Daysheet" <?= $desktop_landing_subtab_config['value']=='Daysheet' ? 'selected="selected"' : '' ?>>Therapist Day Sheet</option>
                    <option value="Therapist Stats" <?= $desktop_landing_subtab_config['value']=='Therapist Stats' ? 'selected="selected"' : '' ?>>Therapist Stats</option>
                    <option value="Block Booking vs Not Block Booking" <?= $desktop_landing_subtab_config['value']=='Block Booking vs Not Block Booking' ? 'selected="selected"' : '' ?>>Block Booking vs Not Block Booking</option>
                    <option value="Injury Type" <?= $desktop_landing_subtab_config['value']=='Injury Type' ? 'selected="selected"' : '' ?>>Injury Type</option>
                    <option value="Treatment Report" <?= $desktop_landing_subtab_config['value']=='Treatment Report' ? 'selected="selected"' : '' ?>>Treatment Report</option>
                    <option value="Equipment List" <?= $desktop_landing_subtab_config['value']=='Equipment List' ? 'selected="selected"' : '' ?>>Equipment List</option>
                    <option value="Shop Work Order Task Time" <?= $desktop_landing_subtab_config['value']=='Shop Work Order Task Time' ? 'selected="selected"' : '' ?>>Shop Work Order Task Time</option>
                    <option value="Site Work Orders" <?= $desktop_landing_subtab_config['value']=='Site Work Orders' ? 'selected="selected"' : '' ?>>Site Work Orders</option>
                    <option value="Scrum Business Productivity Summary" <?= $desktop_landing_subtab_config['value']=='Scrum Business Productivity Summary' ? 'selected="selected"' : '' ?>>Scrum Business Productivity Summary</option>
                    <option value="Scrum Staff Productivity Summary" <?= $desktop_landing_subtab_config['value']=='Scrum Staff Productivity Summary' ? 'selected="selected"' : '' ?>>Scrum Staff Productivity Summary</option>
                    <option value="Scrum Status Report" <?= $desktop_landing_subtab_config['value']=='Scrum Status Report' ? 'selected="selected"' : '' ?>>Scrum Status Report</option>
                    <option value="Service Usage Report" <?= $desktop_landing_subtab_config['value']=='Service Usage Report' ? 'selected="selected"' : '' ?>>% Breakdown of Services Sold</option>
                    <option value="Download Tracker" <?= $desktop_landing_subtab_config['value']=='Download Tracker' ? 'selected="selected"' : '' ?>>Download Tracker</option>
                    <option value="Appointment Summary" <?= $desktop_landing_subtab_config['value']=='Appointment Summary' ? 'selected="selected"' : '' ?>>Appointment Summary</option>
                    <option value="Patient Block Booking" <?= $desktop_landing_subtab_config['value']=='Patient Block Booking' ? 'selected="selected"' : '' ?>>Block Booking</option>
                    <option value="Assessment Tally Board" <?= $desktop_landing_subtab_config['value']=='Assessment Tally Board' ? 'selected="selected"' : '' ?>>Assessment Tally Board</option>
                    <option value="Assessment Follow Up" <?= $desktop_landing_subtab_config['value']=='Assessment Follow Up' ? 'selected="selected"' : '' ?>>Assessment Follow Ups</option>
                    <option value="Field Jobs" <?= $desktop_landing_subtab_config['value']=='Field Jobs' ? 'selected="selected"' : '' ?>>Field Jobs</option>
                    <option value="Shop Work Orders" <?= $desktop_landing_subtab_config['value']=='Shop Work Orders' ? 'selected="selected"' : '' ?>>Shop Work Orders</option>
                    <option value="Purchase Orders" <?= $desktop_landing_subtab_config['value']=='Purchase Orders' ? 'selected="selected"' : '' ?>>Purchase Orders</option>
                    <option value="Inventory Log" <?= $desktop_landing_subtab_config['value']=='Inventory Log' ? 'selected="selected"' : '' ?>>Inventory Log</option>
                    <option value="POS" <?= $desktop_landing_subtab_config['value']=='POS' ? 'selected="selected"' : '' ?>><?= POS_ADVANCE_TILE ?></option>
                    <option value="Credit Card on File" <?= $desktop_landing_subtab_config['value']=='Credit Card on File' ? 'selected="selected"' : '' ?>>Credit Card on File</option>
                    <option value="Checklist Time" <?= $desktop_landing_subtab_config['value']=='Checklist Time' ? 'selected="selected"' : '' ?>>Checklist Time Tracking</option>
                    <option value="Tasklist Time" <?= $desktop_landing_subtab_config['value']=='Tasklist Time' ? 'selected="selected"' : '' ?>>Task Time Tracking</option>
                    <option value="Ticket Attached" <?= $desktop_landing_subtab_config['value']=='Ticket Attached' ? 'selected="selected"' : '' ?>>Attached to <?= TICKET_TILE ?></option>
                    <option value="Drop Off Analysis" <?= $desktop_landing_subtab_config['value']=='Drop Off Analysis' ? 'selected="selected"' : '' ?>>Drop Off Analysis</option>
                    <option value="Discharge Report" <?= $desktop_landing_subtab_config['value']=='Discharge Report' ? 'selected="selected"' : '' ?>>Discharge Report</option>
                    <option value="Ticket Report" <?= $desktop_landing_subtab_config['value']=='Ticket Report' ? 'selected="selected"' : '' ?>><?= TICKET_NOUN ?> Report</option>
                    <option value="Action Item Summary" <?= $desktop_landing_subtab_config['value']=='Action Item Summary' ? 'selected="selected"' : '' ?>>Action Item Summary</option>
                   <option value="Site Work Time" <?= $desktop_landing_subtab_config['value']=='Site Work Time' ? 'selected="selected"' : '' ?>>Site Work Order Time on Site</option>
                    <option value="Site Work Driving" <?= $desktop_landing_subtab_config['value']=='Site Work Driving' ? 'selected="selected"' : '' ?>>Site Work Order Driving Logs</option>
                    <option value="Shop Work Order Time" <?= $desktop_landing_subtab_config['value']=='Shop Work Order Time' ? 'selected="selected"' : '' ?>>Shop Work Order Time</option>
                    <option value="Equipment Transfer" <?= $desktop_landing_subtab_config['value']=='Equipment Transfer' ? 'selected="selected"' : '' ?>>Equipment Transfer History</option>
                    <option value="Work Order" <?= $desktop_landing_subtab_config['value']=='Work Order' ? 'selected="selected"' : '' ?>>Work Order</option>
                    <option value="Staff Tickets" <?= $desktop_landing_subtab_config['value']=='Staff Tickets' ? 'selected="selected"' : '' ?>>Staff <?= TICKET_TILE ?></option>
                    <option value="Day Sheet Report" <?= $desktop_landing_subtab_config['value']=='Day Sheet Report' ? 'selected="selected"' : '' ?>>Day Sheet Report</option>
                    <option value="Ticket Time Summary" <?= $desktop_landing_subtab_config['value']=='Ticket Time Summary' ? 'selected="selected"' : '' ?>><?= TICKET_NOUN ?> Time Summary</option>
                    <option value="Ticket Deleted Notes" <?= $desktop_landing_subtab_config['value']=='Ticket Deleted Notes' ? 'selected="selected"' : '' ?>>Archived <?= TICKET_NOUN ?> Notes</option>
                    <option value="Ticket Activity Report" <?= $desktop_landing_subtab_config['value']=='Ticket Activity Report' ? 'selected="selected"' : '' ?>><?= TICKET_NOUN ?> Activity Report per Customer</option>
                    <option value="Ticket Activity Extra Report" <?= $desktop_landing_subtab_config['value']=='Ticket Activity Extra Report' ? 'selected="selected"' : '' ?>><?= TICKET_NOUN ?> Activity Report for Extra Billing</option>
                    <option value="Rate Card Report" <?= $desktop_landing_subtab_config['value']=='Rate Card Report' ? 'selected="selected"' : '' ?>>Rate Cards Report</option>
                    <option value="Import Summary" <?= $desktop_landing_subtab_config['value']=='Import Summary' ? 'selected="selected"' : '' ?>>Import Summary Report</option>
                    <option value="Import Details" <?= $desktop_landing_subtab_config['value']=='Import Details' ? 'selected="selected"' : '' ?>>Detailed Import Report</option>
                    <option value="Ticket Manifest Summary" <?= $desktop_landing_subtab_config['value']=='Ticket Manifest Summary' ? 'selected="selected"' : '' ?>>Manifest Daily Summary</option>
                    <option value="Employee Payroll Report" <?= $desktop_landing_subtab_config['value']=='Employee Payroll Report' ? 'selected="selected"' : '' ?>>Employee Payroll Report</option>
                </optgroup>
                <optgroup label="Sales">
                    <option value="Validation by Therapist" <?= $desktop_landing_subtab_config['value']=='Validation by Therapist' ? 'selected="selected"' : '' ?>>Validation by Therapist</option>
                    <!-- <option value="POS Validation" <?= $desktop_landing_subtab_config['value']=='POS Validation' ? 'selected="selected"' : '' ?>>POS (Basic) Validation</option> -->
                    <option value="POS Advanced Validation" <?= $desktop_landing_subtab_config['value']=='POS Advanced Validation' ? 'selected="selected"' : '' ?>>POS Validation</option>
                    <option value="Phone Communication" <?= $desktop_landing_subtab_config['value']=='Phone Communication' ? 'selected="selected"' : '' ?>>Phone Communication</option>
                    <option value="Daily Deposit Report" <?= $desktop_landing_subtab_config['value']=='Daily Deposit Report' ? 'selected="selected"' : '' ?>>Daily Deposit Report</option>
                    <option value="Monthly Sales by Injury Type" <?= $desktop_landing_subtab_config['value']=='Monthly Sales by Injury Type' ? 'selected="selected"' : '' ?>>Monthly Sales by Injury Type</option>
                    <option value="Invoice Sales Summary" <?= $desktop_landing_subtab_config['value']=='Invoice Sales Summary' ? 'selected="selected"' : '' ?>>Invoice Sales Summary</option>
                    <option value="Sales by Customer Summary" <?= $desktop_landing_subtab_config['value']=='Sales by Customer Summary' ? 'selected="selected"' : '' ?>>Sales by Customer Summary</option>
                    <option value="Sales History by Customer" <?= $desktop_landing_subtab_config['value']=='Sales History by Customer' ? 'selected="selected"' : '' ?>>Sales History by Customer</option>
                    <option value="Sales by Service Summary" <?= $desktop_landing_subtab_config['value']=='Sales by Service Summary' ? 'selected="selected"' : '' ?>>Sales by Service Summary</option>
                    <option value="Sales by Inventory Summary" <?= $desktop_landing_subtab_config['value']=='Sales by Inventory Summary' ? 'selected="selected"' : '' ?>>Sales by Inventory Summary</option>
                    <option value="Sales Summary by Injury Type" <?= $desktop_landing_subtab_config['value']=='Sales Summary by Injury Type' ? 'selected="selected"' : '' ?>>Sales Summary by Injury Type</option>
                    <option value="Inventory Analysis" <?= $desktop_landing_subtab_config['value']=='Inventory Analysis' ? 'selected="selected"' : '' ?>>Inventory Analysis</option>
                    <option value="Unassigned/Error Invoices" <?= $desktop_landing_subtab_config['value']=='Unassigned/Error Invoices' ? 'selected="selected"' : '' ?>>Unassigned/Error Invoices</option>
                    <option value="Staff Revenue Report" <?= $desktop_landing_subtab_config['value']=='Staff Revenue Report' ? 'selected="selected"' : '' ?>>Staff Revenue Report</option>
                    <option value="Expense Summary Report" <?= $desktop_landing_subtab_config['value']=='Expense Summary Report' ? 'selected="selected"' : '' ?>>Expense Summary Report</option>
                    <option value="Sales by Inventory/Service Detail" <?= $desktop_landing_subtab_config['value']=='Sales by Inventory/Service Detail' ? 'selected="selected"' : '' ?>>Sales by Inventory/Service Detail</option>
                    <option value="Payment Method List" <?= $desktop_landing_subtab_config['value']=='Payment Method List' ? 'selected="selected"' : '' ?>>Payment Method List</option>
                    <option value="Patient History" <?= $desktop_landing_subtab_config['value']=='Patient History' ? 'selected="selected"' : '' ?>>Customer History</option>
                    <option value="Sales by Service Category" <?= $desktop_landing_subtab_config['value']=='Sales by Service Category' ? 'selected="selected"' : '' ?>>Sales by Service Category</option>
                    <option value="Sales Estimates" <?= $desktop_landing_subtab_config['value']=='Sales Estimates' ? 'selected="selected"' : '' ?>>Sales Estimates</option>
                    <option value="Receipts Summary Report" <?= $desktop_landing_subtab_config['value']=='Receipts Summary Report' ? 'selected="selected"' : '' ?>>Receipts Summary Report</option>
                    <option value="Project Progress Report" <?= $desktop_landing_subtab_config['value']=='Project Progress Report' ? 'selected="selected"' : '' ?>>Project Progress Report</option>
                    <option value="Estimate Item Closing % By Quantity" <?= $desktop_landing_subtab_config['value']=='Estimate Item Closing % By Quantity' ? 'selected="selected"' : '' ?>>Estimate Item Closing % By Quantity</option>
                    <option value="Gross Revenue by Staff" <?= $desktop_landing_subtab_config['value']=='Gross Revenue by Staff' ? 'selected="selected"' : '' ?>>Gross Revenue by Staff</option>
                    <option value="Patient Invoices" <?= $desktop_landing_subtab_config['value']=='Patient Invoices' ? 'selected="selected"' : '' ?>>Customer Invoices</option>
                    <!-- <option value="POS Sales Summary" <?= $desktop_landing_subtab_config['value']=='POS Sales Summary' ? 'selected="selected"' : '' ?>>POS (Basic) Sales Summary</option> -->
                    <option value="POS Advanced Sales Summary" <?= $desktop_landing_subtab_config['value']=='POS Advanced Sales Summary' ? 'selected="selected"' : '' ?>>POS Sales Summary</option>
                    <option value="Profit-Loss" <?= $desktop_landing_subtab_config['value']=='Profit-Loss' ? 'selected="selected"' : '' ?>>Profit-Loss</option>
                    <option value="Profit-Loss POS Advanced" <?= $desktop_landing_subtab_config['value']=='Profit-Loss POS Advanced' ? 'selected="selected"' : '' ?>>Profit-Loss (POS Advanced)</option>
                    <option value="Transaction List by Customer" <?= $desktop_landing_subtab_config['value']=='Transaction List by Customer' ? 'selected="selected"' : '' ?>>Transaction List by Customer</option>
                    <option value="Unbilled Invoices" <?= $desktop_landing_subtab_config['value']=='Unbilled Invoices' ? 'selected="selected"' : '' ?>>Unbilled Invoices</option>
                    <option value="Deposit Detail" <?= $desktop_landing_subtab_config['value']=='Deposit Detail' ? 'selected="selected"' : '' ?>>Deposit Detail</option>
                </optgroup>
                <optgroup label="Accounts Receivable">
                    <option value="A/R Aging Summary" <?= $desktop_landing_subtab_config['value']=='A/R Aging Summary' ? 'selected="selected"' : '' ?>>A/R Aging Summary</option>
                    <option value="Patient Aging Receivable Summary" <?= $desktop_landing_subtab_config['value']=='Patient Aging Receivable Summary' ? 'selected="selected"' : '' ?>>Customer Aging Receivable Summary</option>
                    <option value="Insurer Aging Receivable Summary" <?= $desktop_landing_subtab_config['value']=='Insurer Aging Receivable Summary' ? 'selected="selected"' : '' ?>>Insurer Aging Receivable Summary</option>
                    <option value="By Invoice#" <?= $desktop_landing_subtab_config['value']=='By Invoice#' ? 'selected="selected"' : '' ?>>By Invoice#</option>
                    <option value="Customer Balance Summary" <?= $desktop_landing_subtab_config['value']=='Customer Balance Summary' ? 'selected="selected"' : '' ?>>Customer Balance Summary</option>
                    <option value="Customer Balance by Invoice" <?= $desktop_landing_subtab_config['value']=='Customer Balance by Invoice' ? 'selected="selected"' : '' ?>>Customer Balance by Invoice</option>
                    <option value="Collections Report by Customer" <?= $desktop_landing_subtab_config['value']=='Collections Report by Customer' ? 'selected="selected"' : '' ?>>Collections Report by Customer</option>
                    <option value="Invoice List" <?= $desktop_landing_subtab_config['value']=='Invoice List' ? 'selected="selected"' : '' ?>>Invoice List</option>
                    <!-- <option value="POS Receivables (Basic)" <?= $desktop_landing_subtab_config['value']=='POS Receivables (Basic)' ? 'selected="selected"' : '' ?>>POS Receivables (Basic)</option> -->
                    <option value="UI Invoice Report" <?= $desktop_landing_subtab_config['value']=='UI Invoice Report' ? 'selected="selected"' : '' ?>>UI Invoice Report</option>
                </optgroup>
                <optgroup label="Profit & Loss">
                    <option value="Revenue Receivables" <?= $desktop_landing_subtab_config['value']=='Revenue Receivables' ? 'selected="selected"' : '' ?>>Revenue &amp; Receivables</option>
                    <option value="Staff Compensation" <?= $desktop_landing_subtab_config['value']=='Staff Compensation' ? 'selected="selected"' : '' ?>>Staff &amp; Compensation</option>
                    <option value="Expenses" <?= $desktop_landing_subtab_config['value']=='Expenses' ? 'selected="selected"' : '' ?>>Expenses</option>
                    <option value="Costs" <?= $desktop_landing_subtab_config['value']=='Costs' ? 'selected="selected"' : '' ?>>Costs</option>
                    <option value="Summary" <?= $desktop_landing_subtab_config['value']=='Summary' ? 'selected="selected"' : '' ?>>Summary</option>
                    <option value="Labour Report" <?= $desktop_landing_subtab_config['value']=='Labour Report' ? 'selected="selected"' : '' ?>>Labour Report</option>
                    <option value="Dollars By Service" <?= $desktop_landing_subtab_config['value']=='Dollars By Service' ? 'selected="selected"' : '' ?>>Dollars By Service</option>
                </optgroup>
                <optgroup label="Marketing">
                    <option value="CRM Recommendations - By Customer" <?= $desktop_landing_subtab_config['value']=='CRM Recommendations - By Customer' ? 'selected="selected"' : '' ?>>CRM Recommendations - By Customer</option>
                    <option value="CRM Recommendations - By Date" <?= $desktop_landing_subtab_config['value']=='CRM Recommendations - By Date' ? 'selected="selected"' : '' ?>>CRM Recommendations - By Date</option>
                    <option value="Customer Contact List" <?= $desktop_landing_subtab_config['value']=='Customer Contact List' ? 'selected="selected"' : '' ?>>Customer Contact List</option>
                    <option value="Customer Stats" <?= $desktop_landing_subtab_config['value']=='Customer Stats' ? 'selected="selected"' : '' ?>>Customer Stats</option>
                    <option value="Demographics" <?= $desktop_landing_subtab_config['value']=='Demographics' ? 'selected="selected"' : '' ?>>Demographics</option>
                    <option value="POS Coupons" <?= $desktop_landing_subtab_config['value']=='POS Coupons' ? 'selected="selected"' : '' ?>>POS Coupons</option>
                    <option value="Postal Code" <?= $desktop_landing_subtab_config['value']=='Postal Code' ? 'selected="selected"' : '' ?>>Postal Code</option>
                    <option value="Net Promoter Score" <?= $desktop_landing_subtab_config['value']=='Net Promoter Score' ? 'selected="selected"' : '' ?>>Net Promoter Score</option>
                    <option value="Driver Report" <?= $desktop_landing_subtab_config['value']=='Driver Report' ? 'selected="selected"' : '' ?>>Driver Report</option>
                    <option value="Contact Report by Status" <?= $desktop_landing_subtab_config['value']=='Contact Report by Status' ? 'selected="selected"' : '' ?>>Contact Report by Status</option>
                    <option value="Referral" <?= $desktop_landing_subtab_config['value']=='Referral' ? 'selected="selected"' : '' ?>>Referrals</option>
                    <option value="Web Referrals Report" <?= $desktop_landing_subtab_config['value']=='Web Referrals Report' ? 'selected="selected"' : '' ?>>Web Referrals Report</option>
                    <option value="Pro Bono Report" <?= $desktop_landing_subtab_config['value']=='Pro Bono Report' ? 'selected="selected"' : '' ?>>Pro-Bono</option>
                    <option value="Contact Postal Code" <?= $desktop_landing_subtab_config['value']=='Contact Postal Code' ? 'selected="selected"' : '' ?>>Contact Postal Code</option>
                </optgroup>
                <optgroup label="Compensation">
                    <option value="Adjustment Compensation" <?= $desktop_landing_subtab_config['value']=='Adjustment Compensation' ? 'selected="selected"' : '' ?>>Adjustment Compensation</option>
                    <option value="Compensation Print Appointment Reports" <?= $desktop_landing_subtab_config['value']=='Compensation Print Appointment Reports' ? 'selected="selected"' : '' ?>>Compensation: Print Appt. Reports Button</option>
                    <option value="Hourly Compensation" <?= $desktop_landing_subtab_config['value']=='Hourly Compensation' ? 'selected="selected"' : '' ?>>Hourly Compensation</option>
                    <option value="Therapist Compensation" <?= $desktop_landing_subtab_config['value']=='Therapist Compensation' ? 'selected="selected"' : '' ?>>Therapist Compensation</option>
                    <option value="Statutory Holiday Pay Breakdown" <?= $desktop_landing_subtab_config['value']=='Statutory Holiday Pay Breakdown' ? 'selected="selected"' : '' ?>>Statutory Holiday Pay Breakdown</option>
                    <option value="Timesheet Payroll" <?= $desktop_landing_subtab_config['value']=='Timesheet Payroll' ? 'selected="selected"' : '' ?>>Time Sheet Payroll</option>
                </optgroup>
                <optgroup label="Customer">
                    <option value="Customer Sales by Customer Summary" <?= $desktop_landing_subtab_config['value']=='Customer Sales by Customer Summary' ? 'selected="selected"' : '' ?>>Sales by Customer Summary</option>
                    <option value="Customer Sales History by Customer" <?= $desktop_landing_subtab_config['value']=='Customer Sales History by Customer' ? 'selected="selected"' : '' ?>>Sales History by Customer</option>
                    <option value="Customer Patient Invoices" <?= $desktop_landing_subtab_config['value']=='Customer Patient Invoices' ? 'selected="selected"' : '' ?>>Customer Invoices</option>
                    <option value="Customer Transaction List by Customer" <?= $desktop_landing_subtab_config['value']=='Customer Transaction List by Customer' ? 'selected="selected"' : '' ?>>Transaction List by Customer</option>
                    <option value="Customer Patient History" <?= $desktop_landing_subtab_config['value']=='Customer Patient History' ? 'selected="selected"' : '' ?>>Patient History</option>
                    <option value="Customer Customer Balance Summary" <?= $desktop_landing_subtab_config['value']=='Customer Customer Balance Summary' ? 'selected="selected"' : '' ?>>Customer Balance Summary</option>
                    <option value="Customer Customer Balance by Invoice" <?= $desktop_landing_subtab_config['value']=='Customer Customer Balance by Invoice' ? 'selected="selected"' : '' ?>>Customer Balance by Invoice</option>
                    <option value="Customer Collections Report by Customer" <?= $desktop_landing_subtab_config['value']=='Customer Collections Report by Customer' ? 'selected="selected"' : '' ?>>Collections Report by Customer</option>
                    <option value="Customer Patient Aging Receivable Summary" <?= $desktop_landing_subtab_config['value']=='Customer Patient Aging Receivable Summary' ? 'selected="selected"' : '' ?>>Patient Aging Receivable Summary</option>
                    <option value="Customer Customer Contact List" <?= $desktop_landing_subtab_config['value']=='Customer Customer Contact List' ? 'selected="selected"' : '' ?>>Customer Contact List</option>
                    <option value="Customer Customer Stats" <?= $desktop_landing_subtab_config['value']=='Customer Customer Stats' ? 'selected="selected"' : '' ?>>Customer Stats</option>
                    <option value="Customer CRM Recommendations - By Customer" <?= $desktop_landing_subtab_config['value']=='Customer CRM Recommendations - By Customer' ? 'selected="selected"' : '' ?>>CRM Recommendations - By Customer</option>
                    <option value="Customer Contact Postal Code" <?= $desktop_landing_subtab_config['value']=='Customer Contact Postal Code' ? 'selected="selected"' : '' ?>>Contact Postal Code</option>
                </optgroup>
                <optgroup label="Staff">
                    <option value="Staff Staff Tickets" <?= $desktop_landing_subtab_config['value']=='Staff Staff Tickets' ? 'selected="selected"' : '' ?>>Staff Tickets</option>
                    <option value="Staff Scrum Staff Productivity Summary" <?= $desktop_landing_subtab_config['value']=='Staff Scrum Staff Productivity Summary' ? 'selected="selected"' : '' ?>>Scrum Staff Productivity Summary</option>
                    <option value="Staff Daysheet" <?= $desktop_landing_subtab_config['value']=='Staff Daysheet' ? 'selected="selected"' : '' ?>>Therapist Day Sheet</option>
                    <option value="Staff Therapist Stats" <?= $desktop_landing_subtab_config['value']=='Staff Therapist Stats' ? 'selected="selected"' : '' ?>>Therapist Stats</option>
                    <option value="Staff Day Sheet Report" <?= $desktop_landing_subtab_config['value']=='Staff Day Sheet Report' ? 'selected="selected"' : '' ?>>Day Sheet Report</option>
                    <option value="Staff Staff Revenue Report" <?= $desktop_landing_subtab_config['value']=='Staff Staff Revenue Report' ? 'selected="selected"' : '' ?>>Staff Revenue Report</option>
                    <option value="Staff Gross Revenue by Staff" <?= $desktop_landing_subtab_config['value']=='Staff Gross Revenue by Staff' ? 'selected="selected"' : '' ?>>Gross Revenue by Staff</option>
                    <option value="Staff Validation by Therapist" <?= $desktop_landing_subtab_config['value']=='Staff Validation by Therapist' ? 'selected="selected"' : '' ?>>Validation by Therapist</option>
                    <option value="Staff Staff Compensation" <?= $desktop_landing_subtab_config['value']=='Staff Staff Compensation' ? 'selected="selected"' : '' ?>>Staff Compensation</option>
                </optgroup>
              </optgroup>
              <optgroup label="History">
                  <option value="History Staff History" <?= $desktop_landing_subtab_config['value']=='History Staff History' ? 'selected="selected"' : '' ?>>Staff History</option>
                  <option value="History Checklist History" <?= $desktop_landing_subtab_config['value']=='History Checklist History' ? 'selected="selected"' : '' ?>>Checklist History</option>
                  <option value="History Sales History" <?= $desktop_landing_subtab_config['value']=='History Sales History' ? 'selected="selected"' : '' ?>>Sales History</option>
                  <option value="History HR History" <?= $desktop_landing_subtab_config['value']=='History HR History' ? 'selected="selected"' : '' ?>>HR History</option>
                  <option value="History Point of Sale History" <?= $desktop_landing_subtab_config['value']=='History Point of Sale History' ? 'selected="selected"' : '' ?>>Point of Sale History</option>
                  <option value="History Inventory History" <?= $desktop_landing_subtab_config['value']=='History Inventory History' ? 'selected="selected"' : '' ?>>Inventory History</option>
                  <option value="History Project History" <?= $desktop_landing_subtab_config['value']=='History Project History' ? 'selected="selected"' : '' ?>>Project History</option>
                  <option value="History Contact History" <?= $desktop_landing_subtab_config['value']=='History Contact History' ? 'selected="selected"' : '' ?>>Contact History</option>
                  <option value="History Security History" <?= $desktop_landing_subtab_config['value']=='History Security History' ? 'selected="selected"' : '' ?>>Security History</option>
                  <option value="History Equipment History" <?= $desktop_landing_subtab_config['value']=='History Equipment History' ? 'selected="selected"' : '' ?>>Equipment History</option>
                  <option value="History Vendor Price List History" <?= $desktop_landing_subtab_config['value']=='History Vendor Price List History' ? 'selected="selected"' : '' ?>>Vendor Price List History</option>
                  <option value="History Rate Card History" <?= $desktop_landing_subtab_config['value']=='History Rate Card History' ? 'selected="selected"' : '' ?>>Rate Card History</option>
                  <option value="History Purchase Order History" <?= $desktop_landing_subtab_config['value']=='History Purchase Order History' ? 'selected="selected"' : '' ?>>Purchase Order History</option>
                  <option value="History Intake History" <?= $desktop_landing_subtab_config['value']=='History Intake History' ? 'selected="selected"' : '' ?>>Intake History</option>
                  <option value="History Services History" <?= $desktop_landing_subtab_config['value']=='History Services History' ? 'selected="selected"' : '' ?>>Services History</option>
                  <option value="History Expenses History" <?= $desktop_landing_subtab_config['value']=='History Expenses History' ? 'selected="selected"' : '' ?>>Expenses History</option>
                  <option value="History Estimates History" <?= $desktop_landing_subtab_config['value']=='History Estimates History' ? 'selected="selected"' : '' ?>>Estimates History</option>
                  <option value="History Products History" <?= $desktop_landing_subtab_config['value']=='History Products History' ? 'selected="selected"' : '' ?>>Products History</option>
                  <option value="History Safety History" <?= $desktop_landing_subtab_config['value']=='History Safety History' ? 'selected="selected"' : '' ?>>Safety History</option>
                  <option value="History DB Backup History" <?= $desktop_landing_subtab_config['value']=='History DB Backup History' ? 'selected="selected"' : '' ?>>DB Backup History</option>
                  <option value="History Field Jobs History" <?= $desktop_landing_subtab_config['value']=='History Field Jobs History' ? 'selected="selected"' : '' ?>>Field Jobs History</option>
              </optgroup>
              <optgroup label="Estimates">
                  <option value="Estimate Report" <?= $desktop_landing_subtab_config['value']=='Estimate Report' ? 'selected="selected"' : '' ?>>Estimate Report</option>
                  <option value="Estimate Stats" <?= $desktop_landing_subtab_config['value']=='Estimate Stats' ? 'selected="selected"' : '' ?>>Estimate Stats</option>
                  <option value="Estimate Alerts" <?= $desktop_landing_subtab_config['value']=='Estimate Alerts' ? 'selected="selected"' : '' ?>>Estimate Alerts</option>
                  <option value="Estimate Forecasting" <?= $desktop_landing_subtab_config['value']=='Estimate Forecasting' ? 'selected="selected"' : '' ?>>Estimate Forecasting</option>
              </optgroup>

            </select>
        </div>
    </div>

    <div class="form-group pull-right">
        <a href="report_tiles.php" class="btn brand-btn">Back</a>
        <button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
    </div>

</form>
