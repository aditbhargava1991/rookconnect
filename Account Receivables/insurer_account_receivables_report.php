<?php
/*
Client Listing
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
checkAuthorised('accounts_receivables');

if (isset($_POST['submit_pay'])) {
    $invoiceinsurerid = $_POST['invoiceinsurerid'];
    $deposit_number = $_POST['deposit_number'];
    $paid_date = $_POST['paid_date'];
    $date_deposit = $_POST['date_deposit'];
    $paid_type = $_POST['paid_type'];
    $insurer_price = 0;
    //$paid_date = date('Y-m-d');

    foreach ($_POST['invoiceinsurerid'] as $id => $value) {
		$query_update_in = "UPDATE `invoice_insurer` SET `paid` = 'Yes', `deposit_number` = '$deposit_number', `paid_date` = '$paid_date', `date_deposit` = '$date_deposit', `paid_type` = '$paid_type' WHERE `invoiceinsurerid` = '$value'";
		$result_update_in = mysqli_query($dbc, $query_update_in);

        $invoiceid = get_all_from_invoice_insurer($dbc, $value, 'invoiceid');

        $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(invoiceinsurerid) AS total_invoiceinsurerid FROM	invoice_insurer WHERE invoiceid='$invoiceid' AND paid='Waiting on Insurer'"));

        if($get_staff['total_invoiceinsurerid'] == 0) {
		    $query_update_in = "UPDATE `invoice` SET `paid` = 'Yes' WHERE `invoiceid` = '$invoiceid'";
		    $result_update_in = mysqli_query($dbc, $query_update_in);
        }
        $insurer_price += get_all_from_invoice_insurer($dbc, $invoiceinsurerid, 'insurer_price');
    }

    /*
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(summaryid) AS summaryid FROM report_summary WHERE DATE(today_date) = '$paid_date'"));
    if($get_config['summaryid'] == 0) {
        $query_insert_summary = "INSERT INTO `report_summary` (`today_date`) VALUES ('$paid_date')";
        $result_insert_summary = mysqli_query($dbc, $query_insert_summary);
    }

    $query_update_summary = "UPDATE `report_summary` SET `daily_payment_amount` = `daily_payment_amount` + '$insurer_price', `Direct Deposit` = `Direct Deposit` + $insurer_price WHERE DATE(today_date) = DATE(NOW())";
    $result_update_summary = mysqli_query($dbc, $query_update_summary);
    */

    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];
    $insurerpdf = $_POST['insurerpdf'];
    $invoice_nopdf = $_POST['invoice_nopdf'];
    $ui_nopdf = $_POST['ui_nopdf'];

    echo '<script type="text/javascript"> window.location.replace("insurer_account_receivables.php?p1='.$starttimepdf.'&p2='.$endtimepdf.'&p3='.$insurerpdf.'&p5='.$invoice_nopdf.'&p6='.$ui_nopdf.'"); </script>';
}

?>

<script type="text/javascript">
$(document).on('change.select2', 'select[name="new"]', function() { newStatusChange(this); });

function waiting_on_collection(sel) {
	var action = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "../ajax_all.php?fill=arcollection&invoiceinsurerid="+action,
		dataType: "html",   //expect html to be returned
		success: function(response){
			//alert("Invoice moved to Collection");
			location.reload();
		}
	});
}

function newStatusChange(sel) {
    var status = sel.value;
    var id = $(sel).attr('id');
    $.ajax({
		type: "GET",
		url: "../ajax_all.php?fill=insurerAR&invoiceinsurerid="+id+"&status="+status,
		dataType: "html",
		success: function(response){
            //console.log(response);
			location.reload();
		}
	});
}
</script>
</head>
<body>
<?php include_once ('../navigation.php');
?>

<div class="container triple-pad-bottom">
    <div class="row">
        <div class="col-md-12">
        <h2>Insurer Accounts Receivable</h2>
		
        <div class="tab-container"><?php
            if ( check_subtab_persmission( $dbc, 'accounts_receivables', ROLE, 'insurer_ar' ) === true ) { ?>
                <a href="insurer_account_receivables.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Insurer A/R</button></a><?php
            } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Insurer A/R</button><?php
            }
            
            if ( check_subtab_persmission( $dbc, 'accounts_receivables', ROLE, 'patient_ar' ) === true ) { ?>
                <a href="patient_account_receivables.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Patient A/R</button></a><?php
            } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Patient A/R</button><?php
            }
            
            if ( check_subtab_persmission( $dbc, 'accounts_receivables', ROLE, 'ui_invoice_report' ) === true ) { ?>
                <a href="ui_invoice_reports.php"><button type="button" class="btn brand-btn mobile-block mobile-100">UI Reports</button></a><?php
            } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">UI Reports</button><?php
            }
            
            if ( check_subtab_persmission( $dbc, 'accounts_receivables', ROLE, 'insurer_ar_report' ) === true ) { ?>
                <a href="insurer_account_receivables_report.php"><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Insurer Paid A/R Report</button></a><?php
            } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Insurer Paid A/R Report</button><?php
            }
            
            if ( check_subtab_persmission( $dbc, 'accounts_receivables', ROLE, 'insurer_ar_cm' ) === true ) { ?>
                <a href="insurer_account_receivables_cm.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Insurer A/R Clinic Master</button></a><?php
            } else { ?>
                <button type="button" class="btn disabled-btn mobile-block mobile-100">Insurer A/R Clinic Master</button><?php
            } ?>
		</div>
        
        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            The Insurer Paid A/R Report displays payments made by the insurer on behalf of the customer or UI.</div>
            <div class="clearfix"></div>
            </div>

            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if(!empty($_GET['p1'])) {
                $starttime = $_GET['p1'];
                $endtime = $_GET['p2'];
                $insurer = $_GET['p3'];
                $invoice_no = $_GET['p5'];
                $ui_no = $_GET['p6'];
                $payment_type = $_GET['p7'];
            }
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $insurer = $_POST['insurer'];
                $invoice_no = $_POST['invoice_no'];
                $ui_no = $_POST['ui_no'];
                $payment_type = $_POST['payment_type'];
            }
            if (isset($_POST['search_email_all'])) {
                $starttime = date('Y-m-d');
                $endtime = date('Y-m-d');
                $insurer = '';
                $invoice_no = '';
                $ui_no = '';
                $payment_type = '';
            }
            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-d');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }
            ?>
            <br />

			<div class="form-group">
				<div style="margin-left:100px">
					<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Here is where you select the date range of the invoice. The date range must be large enough so that the invoice will populate."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					From Paid Date:
					<input name="starttime" type="text" style="width:100px" class="datepicker" value="<?php echo $starttime; ?>">

					Until Paid Date:
						<input name="endtime" type="text" style="width:100px" class="datepicker" value="<?php echo $endtime; ?>">

					<label for="site_name" class="col-sm-1 control-label">
						<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search for invoice(s) by insurer."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						Insurer:
					</label>
					<div class="col-sm-8" style="width:auto;">
						<select data-placeholder="Choose a Insurer..." name="insurer" class="chosen-select-deselect form-control" width="380">
							<option value="">Display All</option>
							<?php
								$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Insurer' AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
								foreach($query as $id) {
									$selected = '';
									$selected = $id == $insurer ? 'selected = "selected"' : '';
									echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
								}
							?>
						</select>
					</div>
					
					<label for="site_name" class="col-sm-1 control-label">
						<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search for invoice(s) by insurer."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						Paid Type:
					</label>
					<div class="col-sm-8" style="width:auto;">
						<select data-placeholder="Choose a Type..." name="payment_type" style="width:100px !important" class="chosen-select-deselect form-control">
							<option value="">Display All</option>
							<option <?php if ($payment_type=='Transfer') echo 'selected="selected"';?> value="Transfer">Transfer</option>
							<option <?php if ($payment_type=='EFT') echo 'selected="selected"';?> value="EFT">EFT</option>
							<option <?php if ($payment_type=='Cheque') echo 'selected="selected"';?> value="Cheque">Cheque</option>
						</select>
					</div>
				</div>
				<br><br>
			<div style="margin-left:250px">
				<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search by invoice # directly. You must enter a complete value."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				Invoice #:
				<input name="invoice_no" type="text" class="form-control1" value="<?php echo $invoice_no; ?>">

				<span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Search by the generated UI #."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				UI #:
				<input name="ui_no" type="text" class="form-control1" value="<?php echo $ui_no; ?>">
			</div>

            <br><br>
            <center><button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
			<span class="popover-examples list-inline" style="margin:0 0 0 10px;"><a data-toggle="tooltip" data-placement="top" title="Select this to remove all of the search filters you've applied. It will revert back to today's invoices."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <button type="submit" name="search_email_all" value="Search" class="btn brand-btn mobile-block">Display Default</button></center>

            </div>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="insurerpdf" value="<?php echo $insurer; ?>">
            <input type="hidden" name="invoice_nopdf" value="<?php echo $invoice_no; ?>">
            <input type="hidden" name="ui_nopdf" value="<?php echo $ui_no; ?>">
            <input type="hidden" name="payment_typepdf" value="<?php echo $payment_type; ?>">

            <!-- <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button> -->
            <br><br>

            </form>
            <form id="form2" name="form2" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <?php
                echo report_receivables($dbc, $starttime, $endtime, '', '', '', $insurer, $invoice_no, $ui_no, $payment_type);

                if((!empty($_GET['p1'])) && (empty($_GET['p3']))) {
                    echo '<a href="'.WEBSITE_URL.'/Reports/report_daily_sales_summary.php?from='.$_GET['p1'].'&to='.$_GET['p2'].'" class="btn brand-btn">Back</a>';
                }
            ?>

        </form>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>

<?php
function report_receivables($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $insurer, $invoice_no, $ui_no, $payment_type) {

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th>Invoice#</th>
    <th>UI#</th>
    <th>Service Date</th>
    <th>Invoice Date</th>
    <th>Insurer</th>
    <th>Price</th>
    <th>Paid Type</th>
    <th>Number</th>
    <th>Date Deposited</th>
    <th>Paid Date</th>
    <th>Status</th>
    </tr>';

    if($insurer != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (DATE(ii.paid_date) >= '".$starttime."' AND DATE(ii.paid_date) <= '".$endtime."') AND ii.insurerid='$insurer' AND ii.invoiceid = i.invoiceid AND ii.paid='Yes' ORDER BY ii.invoiceid");
    } else if($invoice_no != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (DATE(ii.paid_date) >= '".$starttime."' AND DATE(ii.paid_date) <= '".$endtime."') AND ii.paid='Yes' AND ii.invoiceid = i.invoiceid AND i.invoiceid='$invoice_no' ORDER BY ii.invoiceid");
    } else if($ui_no != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (DATE(ii.paid_date) >= '".$starttime."' AND DATE(ii.paid_date) <= '".$endtime."') AND ii.paid='Yes' AND ii.invoiceid = i.invoiceid AND ii.ui_invoiceid='$ui_no' ORDER BY ii.invoiceid");
    } else if($payment_type != '') {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (DATE(ii.paid_date) >= '".$starttime."' AND DATE(ii.paid_date) <= '".$endtime."') AND ii.paid='Yes' AND ii.invoiceid = i.invoiceid AND ii.paid_type='$payment_type' ORDER BY ii.invoiceid");
    } else {
        $report_service = mysqli_query($dbc,"SELECT ii.*, i.service_date FROM invoice_insurer ii, invoice i WHERE (DATE(ii.paid_date) >= '".$starttime."' AND DATE(ii.paid_date) <= '".$endtime."') AND ii.invoiceid = i.invoiceid AND ii.paid='Yes' ORDER BY ii.invoiceid");
    }

    $amt_to_bill = 0;
    $total = 0;
    while($row_report = mysqli_fetch_array($report_service)) {
        $insurer_price = $row_report['insurer_price'];
        $invoiceid = $row_report['invoiceid'];
        $patientid = get_all_from_invoice($dbc, $invoiceid, 'patientid');
        $insurerid = rtrim($row_report['insurerid'],',');
        
        $row_color = ( $row_report['new']=='1' ) ? 'style="background-color:#C5FFB8"' : '';

        $each_insurance_payment = explode('#*#', $insurance_payment);
        $report_data .= '<tr nobr="true" '. $row_color .'>';
        $report_data .= '<td>#'.$invoiceid.' : '.get_contact($dbc, $patientid).'</td>';
        $report_data .= '<td>#'.$row_report['ui_invoiceid'].'</td>';
        $report_data .= '<td>'.$row_report['service_date'].'</td>';
        $report_data .= '<td>'.$row_report['invoice_date'].'</td>';
        $report_data .= '<td>'.get_all_form_contact($dbc, $insurerid, 'name').'</td>';
        $report_data .= '<td>'.$insurer_price.'</td>';
        $report_data .= '<td>'.$row_report['paid_type'].'</td>';
        $report_data .= '<td>'.$row_report['deposit_number'].'</td>';
        $report_data .= '<td>'.$row_report['date_deposit'].'</td>';
        $report_data .= '<td>'.$row_report['paid_date'].'</td>';
        
        $selected = ( $row_report['new']=='1' ) ? 'selected="selected"' : '';
        
        if ( $row_report['new']=='1' ) {
            $report_data .= '
                <td>
                    <select name="new" id="'. $row_report['invoiceinsurerid'] .'" class="chosen-select-deselect" '. $disabled .'>
                        <option value="1" '. $selected .'>New</option>
                        <option value="0">Notes Sent</option>
                    </select>
                </td>';
        } else {
            $report_data .= '<td>Notes Sent</td>';
        }
        
        $report_data .= '</tr>';
        $total += $insurer_price;
    }

    $report_data .= '<tr nobr="true"><td>Total</td><td></td><td></td><td></td><td></td><td>'.$total.'</td><td></td><td></td><td></td><td></td></tr>';
    $report_data .= '</table><br>';

    return $report_data;
}
?>