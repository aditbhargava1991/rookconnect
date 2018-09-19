<?php include_once('../tcpdf/tcpdf.php');

if (isset($_POST['send_drive_log_noemail'])) {
	$poside = $_POST['send_drive_log_noemail'];
	mysqli_query($dbc, "UPDATE `purchase_orders` SET approval = 'Approved', status = 'Receiving' WHERE posid= '".$poside."'" );
    echo '<script type="text/javascript"> alert("Purchase Order #'.$poside.' approved."); </script>';
}
if (isset($_POST['send_drive_logs_approve'])) {
	$poside = $_POST['send_drive_logs_approve'];
	$email_list = $_POST['getemailsapprove'];
    if ($email_list !== '') {
			$emails_arr = explode( ',', $email_list );
			foreach( $emails_arr as $email )
			{
				if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false) {
				} else {
					 echo '<script type="text/javascript"> alert("One or more of the email addresses you have provided is not a proper email address."); </script>';
				}
			}
	$to_email = $email_list;
	$to = explode(',', $to_email);
	$subject ="Purchase Order PDF";
	$message = "Please see the attached Purchase Order below.";
	$meeting_attachment .= 'download/purchase_order_'.$poside.'.pdf';
	send_email('', $to, '', '', $subject, $message, $meeting_attachment);
	mysqli_query($dbc, "UPDATE `purchase_orders` SET approval = 'Approved', status = 'Receiving' WHERE posid= '".$poside."'" );
    echo '<script type="text/javascript"> alert("Purchase Order #'.$poside.' approved and sent to '.$email_list.'."); </script>';
	} else {
	echo '<script type="text/javascript"> alert("Please enter at least 1 email address."); </script>';
	}
}
if (isset($_POST['send_drive_logs'])) {

	$email_list = $_POST['email_list'];
    if ($email_list !== '' || $_POST['pdf_send'] !== null) {

			$emails_arr = explode( ',', $email_list );

			foreach( $emails_arr as $email )
			{
				if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false) {

				} else {
					 echo '<script type="text/javascript"> alert("One or more of the email addresses you have provided is not a proper email address."); </script>';
				}
			}
		//EMAIL
	$to_email = $email_list;

	$to = explode(',', $to_email);
	$subject ="Purchase Order PDF(s)";
	$message = "Please see the attached PDF(s) below.";

	 $meeting_attachment = '';
        foreach($_POST['pdf_send'] as $drivinglogid) {
            if($drivinglogid != '') {
                $meeting_attachment .= 'download/purchase_order_'.$drivinglogid.'.pdf*#FFM#*';
            }
        }
		send_email('', $to, '', '', $subject, $message, $meeting_attachment);


    echo '<script type="text/javascript"> alert("PDF(s) sent to '.$email_list.'."); </script>';
	} else {
	echo '<script type="text/javascript"> alert("Please enter at least 1 email address, or make sure you have selected at least one PDF to send."); </script>';
	}
}

?>
<style>.selectbutton {
	cursor: pointer;
	text-decoration: underline;
}
@media (min-width: 801px) {
	.sel2 {
		display:none;
	}
}
.approve-box {
    display: none;
    position: fixed;
    width: 500px;
	height:250px;
	top:50%;
	margin-top:-125px;
    left: 50%;
    background: lightgrey;
    color: black;
    border: 10px outset grey;
    border-radius: 15px;
    margin-left: -250px;
    text-align: center;
	z-index:99;
    padding: 20px;
}
@media (max-width:530px) {
.approve-box {
	width:100%;
	z-index:99;
	left:0px;
	margin-left:0px;
	overflow:auto;
}
}
.open-approval { cursor:pointer; text-decoration:underline; }
.open-approval:hover { cursor:pointer; text-decoration:underline; font-style: italic; }
</style>
<script type="text/javascript">
$(document).ready(function() {
	$('.selectall').click(function() {
		if($('.selectall').hasClass("deselectall")) {
			$(".selectall").removeClass('deselectall');
			$('.pdf_send').prop('checked', false);
			$(".selectall").text('Select all');
			$('.selectall').prop('title', 'This will select all rows on the current page.');
		} else {
			$(".selectall").addClass('deselectall');
			$('.pdf_send').prop('checked', true);
			$(".selectall").text('Deselect all');
			$('.selectall').prop('title', 'This will deselect all rows on the current page.');
		}
	});

	$('.view_message').click(function() {
		alert('Message: '+$(this).next().text());
	});

	$('.send_cancel').click(function() {
		var id = $(this).val();
		$('.approve-box-'+id).hide();
		$('.getemailsapprove').val('');
	});

	$('.getemailsapprove2').focusout(function() {
		$('.getemailsapprove').val($(this).val());
	});

	$('.sendemailapprovesubmit').click(function() {
		if($('.getemailsapprove').val() == '') {
			alert("Please enter at least one email.");
			return false;
		};
	});
});
$(document).on('change', 'select[name="status[]"]', function() { changePOSStatus(this); });
$(document).on('change', 'select[name="ticket"]', function() { changeTicket(this); });
$(document).on('change', 'select[name="workorder"]', function() { changeWorkOrder(this); });

function changePOSStatus(sel) {
	var status = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	if(status == 'Completed' || status == 'Approved' || status == 'Receiving') {
		$(sel).next().click();
	} else if(status == 'Archived') {
		 if (confirm('Are you sure?')) {

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Purchase Order/pos_ajax_all.php?fill=POSstatus&name="+arr[1]+'&status='+status,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
		}
	} else if(status == 'Pending') {
			$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "../Purchase Order/pos_ajax_all.php?fill=POSstatus&name="+arr[1]+'&status='+status,
				dataType: "html",   //expect html to be returned
				success: function(response){
					location.reload();
			}
		});
	}
}


function approvebutton(sel) {
	var status = sel.id;
	$(".approve-box-"+status).show();
	return false;
}


/* Change Project */
function changeProject(sel) {
	var po = sel.id;
	var id = sel.value;
	var poid = po.split('_');

	$.ajax({
		type: "GET",
		url: "../Purchase Order/pos_ajax_all.php?fill=changeProject&po="+poid[1]+"&id="+id,
		dataType: "html",
		success: function(response){
			location.reload();
		}
	});
}


/* Change Ticket */
function changeTicket(sel) {
	var po = sel.id;
	var id = sel.value;
	var poid = po.split('_');

	$.ajax({
		type: "GET",
		url: "../Purchase Order/pos_ajax_all.php?fill=changeTicket&po="+poid[1]+"&id="+id,
		dataType: "html",
		success: function(response){
			location.reload();
		}
	});
}


/* Change Work Order */
function changeWorkOrder(sel) {
	var po = sel.id;
	var id = sel.value;
	var poid = po.split('_');

	$.ajax({
		type: "GET",
		url: "../Purchase Order/pos_ajax_all.php?fill=changeWorkOrder&po="+poid[1]+"&id="+id,
		dataType: "html",
		success: function(response){
			location.reload();
		}
	});
}
</script>
<form name="invoice_table" method="post" action="" class="form-inline offset-top-20" role="form">
	<input type='hidden' class='getemailsapprove' value='' name='getemailsapprove'>
	<div class="form-group">
		<?php // Search Fields
		$search_type = '';
		$search_from = '';
		$search_until = '';
		$search = '';
		if(!empty($_POST['search_type'])) {
			$search_type = $_POST['search_type'];
			$search .= " AND inv.delivery_type='$search_type'";
		}
		if(!empty($_POST['search_from'])) {
			$search_from = $_POST['search_from'];
			$search .= " AND inv.invoice_date >= '$search_from'";
		}
		if(!empty($_POST['search_until'])) {
			$search_until = $_POST['search_until'];
			$search .= " AND inv.invoice_date <= '$search_until'";
		}
		if(!empty($current_cat)) {
			$search .= " AND inv.po_category='$current_cat'";
		}
		?>

		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
			<label for="search_vendor" class="control-label">Search By Shipping Type:</label>
		</div>
		<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
			<select data-placeholder="Choose Delivery/Shipping Type..." name="search_type" class="chosen-select-deselect form-control">
				<option value=""></option>
				<option <?php if ($search_type == "Pick-Up") { echo " selected"; } ?>  value="Pick-Up">Pick-Up</option>
				<option <?php if ($search_type == "Company Delivery") { echo " selected"; } ?>  value="Company Delivery">Company Delivery</option>
				<option <?php if ($search_type == "Drop Ship") { echo " selected"; } ?>  value="Drop Ship">Drop Ship</option>
				<option <?php if ($search_type == "Shipping") { echo " selected"; } ?>  value="Shipping">Shipping</option>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
			<label for="search_from" class="control-label">Search From Date:</label>
		</div>
		<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
			<input placeholder="Search From Date..." name="search_from" value="<?php echo $search_from; ?>" class="datepicker form-control">
		</div>

		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
			<label for="search_until" class="control-label">Search Until Date:</label>
		</div>
		<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
			<input placeholder="Search Until Date..." name="search_until" value="<?php echo $search_until; ?>" class="datepicker form-control">
		</div>

		<div class="clearfix"></div>
		<div class="form-group pull-right">
			<span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Remember to fill in one of the above boxes to search properly."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><button type="submit" name="search_invoice_submit" value="Search" class="btn brand-btn">Search</button>
			<span class="popover-examples list-inline hide-on-mobile" style="margin:0 5px 0 12px"><a data-toggle="tooltip" data-placement="top" title="Refreshes the page to display all order information under the specific tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><a href="" class="btn brand-btn hide-on-mobile">Display All</a>
		</div>
	</div>
	<div class="clearfix"></div>

		<?php
		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT purchase_order_dashboard FROM field_config"));
		$value_config = ','.$get_field_config['purchase_order_dashboard'].',';
		if (strpos($value_config, ','."Send to Anyone".',') !== FALSE) { ?>
		<!--<div class="clearfix" style='margin:10px;'></div>-->
			<div class="row pad-10 offset-top-20">
				<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin:5px 0 10px 0; padding:0px 15px;">
					<label for="search_vendor" class="control-label" style='width:100%;'><span class="popover-examples list-inline" style="margin:0 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Remember to check the boxes of the PDF’s that you would like to be emailed."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Emails (Separated by a Comma):</label>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style='margin-bottom:10px; padding:0px 10px;'>
					<input id='roll-input' type='text'  name='email_list' placeholder='Enter emails here...' class='form-control email_driving_logs'>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 pull-sm-right pull-xs-right" style='margin-bottom:10px; padding-right:10px;'>
					<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here after you have entered the emails you wish to send PDF(s) of the purchase orders to."><img src="../img/info.png" width="20"></a></span>
					<button onClick="return empty()" type='submit' name='send_drive_logs' class='btn brand-btn dl_send_butt'>Send PDF(s)</button>
				</div>
				<div class="col-lg-1 col-md-3 col-sm-12 col-xs-12 pull-sm-right pull-xs-right" style="padding-right:12px;">
					<div class='selectall selectbutton sel2' title='This will select all PDFs on the current page.'>Select All</div>
				</div>
			</div>
		<?php } ?>
		</div>
	<?php
	// Display Pager

	$rowsPerPagee = ITEMS_PER_PAGE;
	$pageNumm  = 1;

	if(isset($_GET['pagee'])) {
		$pageNumm = $_GET['pagee'];
	}

	$offsett = ($pageNumm - 1) * $rowsPerPagee;

	if (isset($_POST['display_all_invoice'])) {
		$invoice_name = '';
	}

	$query_check_credentialss = "SELECT inv.*, c.`contactid` FROM purchase_orders inv LEFT JOIN contacts c ON inv.contactid = c.contactid WHERE inv.`projectid`='$projectid' AND inv.deleted = 0 ".$search." ORDER BY inv.posid DESC";

	// how many rows we have in database
	$queryy = "SELECT COUNT(posid) AS numrows FROM purchase_orders";

	$resultt = mysqli_query($dbc, $query_check_credentialss);

	$num_rowss = mysqli_num_rows($resultt);
	if($num_rowss > 0) {


		echo "<br clear='all' /><div id='no-more-tables'><table class='table table-bordered'>";
		echo "<tr class='hidden-xs hidden-sm'>";
			if (strpos($value_config, ','."Invoice #".',') !== FALSE) {
				echo '<th width="6%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order Number as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20" /></a></div>P.O. #</th>';
			}
			if (strpos($value_config, ','."Invoice Date".',') !== FALSE) {
				echo '<th width="6%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order Date as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>P.O. Date</th>';
			}
			if (strpos($value_config, ','."Customer".',') !== FALSE) {
				echo '<th width="12%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Vendor name as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Vendor</th>';
			}
			if (strpos($value_config, ','."Total Price".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Total Price as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Total Price</th>';
			}
			if (strpos($value_config, ','."Payment Type".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Payment Type as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Payment Type</th>';
			}
			if (strpos($value_config, ','."Delivery/Shipping Type".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Delivery/Shipping Type as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Delivery/Shipping Type</th>';
			}
			if (strpos($value_config, ','."Invoice PDF".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order created into a PDF document. This opens in a new tab on your computer."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>P.O. PDF</th>';
			}
			if (strpos($value_config, ','."View Spreadsheet".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order created into a Spreadsheet. This opens in a new tab on your computer."></a></div>P.O. Spreadsheet</th>';
			}
			if (strpos($value_config, ','."Comment".',') !== FALSE) {
				echo '<th width="12%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Comment from the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Comment</th>';
			}
			if (strpos($value_config, ','."Status".',') !== FALSE) {
				echo '<th width="10%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Use the drop down menu to change the status of the Purchase Order. When you change the status, the order will move into the selected tab."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Status</th>';
			}
			if (strpos($value_config, ','."Send to Client".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Clicking this will send a PDF to the tagged client."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Send to Client</th>';
			}
			if (strpos($value_config, ','."Send to Anyone".',') !== FALSE) {
			  ?><th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Check this box to send one or several Purchase Orders in a PDF document, then enter the desired email in the Emails box."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a></div>Email PDF<br><div class='selectall selectbutton' title='This will select all PDFs on the current page.'>Select All</div></th><?php
			}
			if (strpos($value_config, ','."Ticket".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Change the Ticket attached to the Purchase Order."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Ticket</th>';
			}
			if (strpos($value_config, ','."Work Order".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Change the Work Order attached to the Purchase Order."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Work Order</th>';
			}
		echo "</tr>";
	} else{
		echo "<h2>No Record Found.</h2>";
	}

	while($roww = mysqli_fetch_array( $resultt ))
	{
		$style2 = '';
		$style = '';
		if($roww['status'] == 'Posted Past Due') {
			$style = 'color:green;';
		}
		if($roww['status'] == 'Void') {
			$style = 'color:red;';
		}
		$contactid = $roww['contactid'];
		echo "<tr style='".$style.$style2."'>";

		if (strpos($value_config, ','."Invoice #".',') !== FALSE) {
			echo '<td data-title="P.O. #"">';
			if($roww['software_seller'] == 'main') {
				if ($roww['cross_software_approval'] !== "" && $roww['cross_software_approval'] !== NULL && $roww['cross_software_approval'] !== 'disapproved') {
					echo $roww['posid'];
				} else {
					echo "P.O. must be approved before P.O. # can be seen.";
				}
			} else {
				echo $roww['posid'];
			}
			echo '</td>';
		}
		if (strpos($value_config, ','."Invoice Date".',') !== FALSE) {
			echo '<td data-title="P.O. Date">'.$roww['invoice_date'].'</td>';
		}
		if (strpos($value_config, ','."Customer".',') !== FALSE) {
			echo '<td data-title="Vendor">' . get_client($dbc, $contactid) . '</td>';
		}
		if (strpos($value_config, ','."Total Price".',') !== FALSE) {
			echo '<td data-title="Total Price">' . $roww['total_price'] . '</td>';
		}
		if (strpos($value_config, ','."Payment Type".',') !== FALSE) {
			//Code was not working, so I had to manually pull from DB below ---v
			$get_pay_type = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM purchase_orders WHERE posid='".$roww['posid']."'"));
			echo '<td data-title="Payment Type">' . $get_pay_type['payment_type'] . '</td>';
		}
		if (strpos($value_config, ','."Delivery/Shipping Type".',') !== FALSE) {
			echo '<td data-title="Delivery/Shipping">' . $roww['delivery_type'] . '</td>';
		}
		if (strpos($value_config, ','."Invoice PDF".',') !== FALSE) {
			echo '<td data-title="P.O. PDF">';
			if($roww['software_seller'] == 'main') {
				if ($roww['cross_software_approval'] !== "" && $roww['cross_software_approval'] !== NULL && $roww['cross_software_approval'] !== 'disapproved') {
					echo '<a target="_blank" href="download/purchase_order_'.$roww['posid'].'.pdf">PDF <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
				} else {
					echo "P.O. must be approved before Invoice/PDF can be seen.";
				}
			} else {
				echo '<a target="_blank" href="download/purchase_order_'.$roww['posid'].'.pdf">PDF <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
			}
			echo '</td>';
		}
		if (strpos($value_config, ','."View Spreadsheet".',') !== FALSE) {
			echo '<td data-title="View Spreadsheet">';
			if($roww['spreadsheet_name'] !== NULL && $roww['spreadsheet_name'] !== '' ) {
				echo '<a target="_blank" href="download/'.$roww['spreadsheet_name'].'">Spreadsheet <img style="width:15px;" src="'.WEBSITE_URL.'/img/icons/file.png" title="Spreadsheet"></a></td>';
			} else { echo '-'; }
		}
		if (strpos($value_config, ','."Comment".',') !== FALSE) {
			echo '<td data-title="Comment">' .  html_entity_decode($roww['comment']) . '</td>';
		}
		if (strpos($value_config, ','."Status".',') !== FALSE) {
			echo '<td data-title="Status">';
			if($roww['software_seller'] == 'main') {
				if ($roww['cross_software_approval'] !== "" && $roww['cross_software_approval'] !== NULL && $roww['cross_software_approval'] !== 'disapproved') { ?>
					<select name="status[]" id="status_<?php echo $roww['posid']; ?>" class="chosen-select-deselect1 form-control" width="380">
						<option value=""></option>
						<option value="Pending" <?php if ($roww['status'] == "Pending") { echo " selected"; } ?> >Pending</option>
						<option value="Receiving" <?php if ($roww['status'] == "Receiving") { echo " selected"; } ?> >Approve</option>
						<option value="Archived" <?php if ($roww['status'] == "Archived") { echo " selected"; } ?> >Archive</option>
					</select>
				<?php echo '<span style="display:none;" class="open-approval" onclick="approvebutton(this)" id="'.$roww['posid'].'">Approve</span>'; ?>
					<div class="approve-box-<?php echo $roww['posid']; ?> approve-box">Please enter the email(s) (separated by a comma) you would like to send this Order to.<br>(If you prefer not to send the P.O., hit skip.)<br><br>
					<input type='text' style='max-width:300px;' name='' placeholder='email1@example.com,email2@example.com' class='form-control getemailsapprove2'><br><br>
					<button type='submit' name='send_drive_logs_approve' class='btn brand-btn sendemailapprovesubmit' value='<?php echo $roww['posid']; ?>'>Approve and Send</button>
					<button type='submit' name='send_drive_log_noemail' class='btn brand-btn ' value='<?php echo $roww['posid']; ?>'>Skip</button>
					<button onClick="hide-box" value="<?php echo $roww['posid']; ?>" type='button' name='send_drive_logs' class='btn brand-btn send_cancel'>Cancel</button>
					</div> <?php
				} else if($roww['cross_software_approval'] == "disapproved") {
					echo 'Disapproved <img src="../img/icons/forbidden.png" width="25px" class="wiggle-me">';
					if($roww['cross_software_disapproval'] !== '' && $roww['cross_software_disapproval'] !== NULL) {
						echo '<br><span class="view_message" id="view_message_'.$roww['posid'].'" style="text-decoration:underline;cursor:pointer;">View Message</span>
						<span class="the_message" style="display:none;">'.$roww['cross_software_disapproval'].'</span>';
					}
				} else {
					echo 'Awaiting approval <img src="../img/icons/locked-2.png" width="25px" class="wiggle-me">';
				}
				echo '</td>';
			} else {
				?>
					<select name="status[]" id="status_<?php echo $roww['posid']; ?>" class="chosen-select-deselect1 form-control" width="380">
						<option value=""></option>
						<option value="Pending" <?php if ($roww['status'] == "Pending") { echo " selected"; } ?> >Pending</option>
						<option value="Receiving" <?php if ($roww['status'] == "Receiving") { echo " selected"; } ?> >Approve</option>
						<option value="Archived" <?php if ($roww['status'] == "Archived") { echo " selected"; } ?> >Archive</option>
					</select>
				<?php echo '<span style="display:none;" class="open-approval" onclick="approvebutton(this)" id="'.$roww['posid'].'">Approve</span>';
						  ?>
							<div class="approve-box-<?php echo $roww['posid']; ?> approve-box">Please enter the email(s) (separated by a comma) you would like to send this Order to.<br>(If you prefer not to send the P.O., hit skip.)<br><br>
							<input type='text' style='max-width:300px;' name='' placeholder='email1@example.com,email2@example.com' class='form-control getemailsapprove2'><br><br>
							<button type='submit' name='send_drive_logs_approve' class='btn brand-btn sendemailapprovesubmit' value='<?php echo $roww['posid']; ?>'>Approve and Send</button>
							<button type='submit' name='send_drive_log_noemail' class='btn brand-btn ' value='<?php echo $roww['posid']; ?>'>Skip</button>
							<button onClick="hide-box" value="<?php echo $roww['posid']; ?>" type='button' name='send_drive_logs' class='btn brand-btn send_cancel'>Cancel</button>
							</div> <?php
			}
		}

		if (strpos($value_config, ','."Send to Client".',') !== FALSE) {
			if($roww['software_seller'] == 'main') {
				if ($roww['cross_software_approval'] !== "" && $roww['cross_software_approval'] !== NULL && $roww['cross_software_approval'] !== 'disapproved') {
					if($roww['status'] == "Void") {
						echo '<td data-title="Send to Client">'.$roww['status_history'].'</td>';
					} else {
					   echo '<td data-title="Send to Client"><a href="send_point_of_sell.php?posid='.$roww['posid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Send</a></td>';
					}
				}
			} else {
				if($roww['status'] == "Void") {
					echo '<td data-title="Send to Client">'.$roww['status_history'].'</td>';
				} else {
				   echo '<td data-title="Send to Client"><a href="send_point_of_sell.php?posid='.$roww['posid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Send</a></td>';
				}
			}
		}
		 if (strpos($value_config, ','."Send to Anyone".',') !== FALSE) {
		echo '<td data-title="Email PDF">';
			if ($roww['cross_software_approval'] !== "" && $roww['cross_software_approval'] !== NULL && $roww['cross_software_approval'] !== 'disapproved') {
				?><input style="height: 25px; width: 25px;" type='checkbox' name='pdf_send[]' class='pdf_send' value='<?php echo $roww['posid']; ?>'>
				<?php
			}
			echo '</td>';
		}

	   if (strpos($value_config, ','."Ticket".',') !== FALSE) {
			echo '<td data-title="Ticket">'; ?>
			<select id="ticket_<?= $roww['posid']; ?>" name="ticket" data-placeholder="Choose Ticket..." class="chosen-select-deselect form-control" width="380">
				<option value=""></option><?php
				$result = mysqli_query($dbc, "SELECT `ticketid`, `heading` FROM `tickets` WHERE `status`!='Archived' ORDER BY `created_date` DESC");
				while($row = mysqli_fetch_assoc($result)) {
					$selected = "";
					if($row['ticketid'] == $roww['ticketid']) {
						$selected = 'selected="selected"';
					}
					echo '<option ' . $selected . ' value="' . $row['ticketid'] . '">' . $row['heading'] . '</option>';
				} ?>
			</select><?php
			echo '</td>';
		}
		if (strpos($value_config, ','."Work Order".',') !== FALSE) {
			echo '<td data-title="Work Order">'; ?>
			<select id="workorder_<?= $roww['posid']; ?>" name="workorder" data-placeholder="Choose Work Order..." class="chosen-select-deselect form-control" width="380">
				<option value=""></option><?php
				$result = mysqli_query($dbc, "SELECT `workorderid`, `heading` FROM `workorder` ORDER BY `created_date` DESC");
				while($row = mysqli_fetch_assoc($result)) {
					$selected = "";
					if($row['workorderid'] == $roww['workorderid']) {
						$selected = 'selected="selected"';
					}
					echo '<option ' . $selected . ' value="' . $row['workorderid'] . '">' . $row['heading'] . '</option>';
				} ?>
			</select><?php
			echo '</td>';
		}
		echo "</tr>";
	}

	echo '</table></div></div>'; ?>
</form>
