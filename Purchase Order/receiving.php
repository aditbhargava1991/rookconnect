<?php
/*
Payment/Invoice Listing SEA
*/
include_once ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
if (isset($_POST['send_drive_log_noemail'])) {
	$poside = $_POST['send_drive_log_noemail'];
	mysqli_query($dbc, "UPDATE `purchase_orders` SET status = 'Paying' WHERE posid= '".$poside."'" );
    echo '<script type="text/javascript"> alert("Purchase Order #'.$poside.' sent to Accounts Payable.");
	window.location.replace("receiving.php"); </script>';
}
if (isset($_POST['send_drive_logs'])) {
	$email_list = $_POST['email_list'];
    if ($email_list !== '' || $_POST['pdf_send'] !== null) {

			$emails_arr = explode( ',', $email_list );

			foreach( $emails_arr as $email )
			{
				if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false) {

				} else {
					 echo '<script type="text/javascript"> alert("One or more of the email addresses you have provided is not a proper email address.");
							window.location.replace("receiving.php"); </script>';
							exit();
				}
			}
		//EMAIL
	$to_email = $email_list;

	$to = explode(',', $to_email);
	$message = "Please see the attached PDF(s) below.";

	 $meeting_attachment = '';
        foreach($_POST['pdf_send'] as $drivinglogid) {
            if($drivinglogid != '') {
                $meeting_attachment .= 'download/purchase_order_'.$drivinglogid.'.pdf*#FFM#*';
            }
        }
		send_email([$_POST['email_address']=>$_POST['email_name']], $to, '', '', $_POST['email_subject'], $message, $meeting_attachment);


    echo '<script type="text/javascript"> alert("PDF(s) sent to '.$email_list.'.");
	window.location.replace("receiving.php"); </script>';
	} else {
	echo '<script type="text/javascript"> alert("Please enter at least 1 email address, or make sure you have selected at least one PDF to send.");
	window.location.replace("receiving.php"); </script>';
	}
}

?><style>.selectbutton {
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
.open-approval:hover { cursor:pointer; text-decoration:none; }
	</style>
	<?php
$get_invoice =	mysqli_query($dbc,"SELECT posid FROM purchase_orders WHERE `invoice_date` + INTERVAL 30 DAY < NOW() AND status!='Completed'");
$num_rows = mysqli_num_rows($get_invoice);
if($num_rows > 0) {
    while($row = mysqli_fetch_array( $get_invoice )) {
        $posid = $row['posid'];
	//	$query_update_project = "UPDATE `purchase_orders` SET status = 'Posted Past Due' WHERE `posid` = '$posid'";
	//	$result_update_project = mysqli_query($dbc, $query_update_project);
    }
}

if((!empty($_GET['type'])) && ($_GET['type'] == 'send_email')) {
    $type = $_GET['type'];
    $posid = $_GET['id'];


}
?>
<script type="text/javascript">
$(document).ready(function() {
	$('.selectall').click(
        function() {
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

	$('.open-approval').click(
        function() {
			var id = $(this)[0].id;
			$('.send_drive_log_noemail-'+id).click();
		});


});
$(document).on('change', 'select[name="status[]"]', function() { changePOSStatus(this); });

function changePOSStatus(sel) {
	var status = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "pos_ajax_all.php?fill=POSstatus&name="+arr[1]+'&status='+status,
		dataType: "html",   //expect html to be returned
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
		$search_any = '';
		$search_vendor = '';
		$search_type = '';
		$search_from = '';
		$search_until = '';
		$search = '';
		if(!empty($_POST['search_any'])) {
			$search_any = $_POST['search_any'];
			$search .= "AND (inv.posid = '$search_any' OR c.name = '$search_any' OR inv.delivery_type = '$search_any' OR inv.total_price LIKE '%" . $search_any . "%' OR inv.payment_type LIKE '%" . $search_any . "%' OR inv.invoice_date LIKE '%" . $search_any . "%' OR inv.status LIKE '%" . $search_any . "%' OR inv.comment LIKE '%" . $search_any . "%') ";
		}
		if(!empty($_POST['search_vendor'])) {
			$search_vendor = $_POST['search_vendor'];
			$search .= " AND c.contactid='$search_vendor'";
		}
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
			<label for="search_any" class="control-label">Search Within Tab:</label>
		</div>
		<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
			<input placeholder="Search Within Tab..." name="search_any" value="<?php echo $search_any; ?>" class="form-control">
		</div>

		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
			<label for="search_vendor" class="control-label">Search By Vendor:</label>
		</div>
		<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
			<select data-placeholder="Select a Vendor..." name="search_vendor" class="chosen-select-deselect form-control">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Vendor' or category='Vendors' order by name");
				while($row = mysqli_fetch_array($query)) {
					?><option <?php if ($row['contactid'] == $search_vendor) { echo " selected"; } ?> value='<?php echo  $row['contactid']; ?>' ><?php echo decryptIt($row['name']); ?></option>
				<?php	}
				?>
			</select>
		</div>

		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
			<label for="search_vendor" class="control-label">Search By Shipping Type:</label>
		</div>
		<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
			<select data-placeholder="Select Delivery/Shipping Type..." name="search_type" class="chosen-select-deselect form-control">
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
			<input placeholder="Search From Date..." name="search_from" value="<?php echo $search_from; ?>" class="datepicker" style="width:100%;">
		</div>

		<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
			<label for="search_until" class="control-label">Search Until Date:</label>
		</div>
		<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
			<input placeholder="Search Until Date..." name="search_until" value="<?php echo $search_until; ?>" class="datepicker" style="width:100%;">
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
				<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Email From Name:</label>
				<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style='margin-bottom:10px; padding:0px 10px;'>
					<input type='text'  name='email_name' placeholder='Enter Your Name...' class='form-control' value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
				</div>
				<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Email From Address:</label>
				<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style='margin-bottom:10px; padding:0px 10px;'>
					<input type='text'  name='email_address' placeholder='Enter Your Address...' class='form-control' value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
				</div>
				<label class="control-label col-lg-3 col-md-3 col-sm-12 col-xs-12">Email Subject:</label>
				<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style='margin-bottom:10px; padding:0px 10px;'>
					<input type='text'  name='email_subject' placeholder='Enter an Email Subject...' class='form-control' value='Purchase Order PDF(s)'>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin:5px 0 10px 0; padding:0px 15px;">
					<label for="search_vendor" class="control-label" style='width:100%;'><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Remember to check the boxes of the PDF’s that you would like to be emailed."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Emails (Separated by a Comma):</label>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style='margin-bottom:10px; padding:0px 10px;'>
					<input id='roll-input' type='text'  name='email_list' placeholder='Enter emails here...' class='form-control email_driving_logs'>
				</div>
				<div class="col-lg-1 col-md-3 col-sm-12 col-xs-12 pull-sm-right pull-xs-right" style='margin-bottom:10px; padding-right:10px;'>
					<button onClick="return empty()" type='submit' name='send_drive_logs' class='btn brand-btn dl_send_butt'>Send PDF(s)</button>
				</div>
				<div class="col-lg-1 col-md-3 col-sm-12 col-xs-12 pull-sm-right pull-xs-right" style="padding-right:12px;">
					<div class='selectall selectbutton sel2' title='This will select all PDFs on the current page.'>Select All</div>
				</div>
			</div>
		<?php } ?>

		<?php
			//if (strpos(CUSTOMER_PRIVILEGES,'AE') !== false) {
			//	echo '<a href="add_inventory.php" class="btn brand-btn pull-right">Add Product</a>';
			//}
		?>
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

	$query_check_credentialss = "SELECT inv.*, c.* FROM purchase_orders inv,  contacts c WHERE inv.contactid = c.contactid AND inv.deleted = 0 AND (inv.status='Receiving')  ".$search." ORDER BY inv.posid DESC";

	// how many rows we have in database
	$queryy = "SELECT COUNT(posid) AS numrows FROM purchase_orders";

	if ( $resultt = mysqli_query($dbc, $query_check_credentialss) ) {
		$num_rowss = mysqli_num_rows($resultt);
	} else {
		$num_rowss = 0;
	}

	if($num_rowss > 0) {
		echo "<br clear='all' /><div id='no-more-tables'><table class='table table-bordered'>";
		echo "<tr class='hidden-xs hidden-sm'>";
			if (strpos($value_config, ','."Invoice #".',') !== FALSE) {
				echo '<th width="6%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order Number as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>P.O. #</th>';
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
				echo '<th width="12%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Use the drop down menu to change the status of the Purchase Order. When you change the status, the order will move into the selected tab."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Status</th>';
			}
			echo '<th width="">Receive Items</th>';
			echo '<th width="">Send to A/P</th>';
			if (strpos($value_config, ','."Send to Client".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Clicking this will send a PDF to the tagged client."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Send to Client</th>';
			}
			if (strpos($value_config, ','."Send to Anyone".',') !== FALSE) {
			  ?><th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Check this box to send one or several Purchase Orders in a PDF document, then enter the desired email in the Emails box."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a></div>Email PDF<br><div class='selectall selectbutton' title='This will select all PDFs on the current page.'>Select All</div></th><?php
			}
		echo "</tr>";

		while($roww = mysqli_fetch_array( $resultt )) {
			$style2 = '';
			if($numodays > 0) {
				$cutoffdater = date('Y-m-d', strtotime($roww['invoice_date']. ' + '.$numodays.' days'));
				$date = date('Y/m/d', time());
				if (new DateTime($date) >= new DateTime($cutoffdater)) {
					$posid = $roww['posid'];
					$query_update_employee = "UPDATE `purchase_orders` SET deleted = '1' WHERE posid='$posid'";
					$result_update_employee = mysqli_query($dbc, $query_update_employee);
					$style2 = 'display:none;';
				}
			}
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
				echo '<td data-title="P.O. #"">' . $roww['posid'] . '</td>';
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
				echo '<td data-title="Delivery/Shipping Type">' . $roww['delivery_type'] . '</td>';
			}
			if (strpos($value_config, ','."Invoice PDF".',') !== FALSE) {
				echo '<td data-title="P.O. PDF"><a target="_blank" href="download/purchase_order_'.$roww['posid'].'.pdf">PDF <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a></td>';
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
				echo '<td data-title="Status">'; ?>
			   <select name="status[]" id="status_<?php echo $roww['posid']; ?>" class="chosen-select-deselect1 form-control" width="380">
					<option value=""></option>
					<option value="Pending" <?php if ($roww['status'] == "Pending") { echo " selected"; } ?> >Pending</option>
					<option value="Receiving" <?php if ($roww['status'] == "Receiving") { echo " selected"; } ?> >Receiving</option>
					<option value="Paying" <?php if ($roww['status'] == "Paying") { echo " selected"; } ?> >Paying</option>
					<option value="Completed" <?php if ($roww['status'] == "Completed") { echo " selected"; } ?> >Complete</option>
					<option value="Archived" <?php if ($roww['status'] == "Archived") { echo " selected"; } ?> >Archive</option>
				</select>
			<?php
				echo '</td>';
				}
				 echo '<td data-title="Receive Items"><a href="receive_pay.php?posid='.$roww['posid'].'&type=receive">Receive Items</a>';
				echo '</td>';
				echo '<td data-title="Send to A/P"><span class="open-approval" id="'.$roww['posid'].'">Send to Accounts Payable</span>';
					  ?><button type='submit' name='send_drive_log_noemail' class='btn brand-btn send_drive_log_noemail-<?php echo $roww['posid']; ?>' value='<?php echo $roww['posid']; ?>' style='display:none;'>Skip</button>
					  <?php
				echo '</td>';

			if (strpos($value_config, ','."Send to Client".',') !== FALSE) {
				if($roww['status'] == "Void") {
					echo '<td data-title="Send to Client">'.$roww['status_history'].'</td>';
				} else {
					echo '<td data-title="Send to Client"><a href="?tab=send_pos&posid='.$roww['posid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Send</a></td>';
				}
			}
			 if (strpos($value_config, ','."Send to Anyone".',') !== FALSE) {
			echo '<td data-title="Email PDF">';
				?><input style="height: 25px; width: 25px;" type='checkbox' name='pdf_send[]' class='pdf_send' value='<?php echo $roww['posid']; ?>'>
				<?php
				//echo '<a href=\'driving_log_14days.php?email=send&drivinglogid='.$row['drivinglogid'].'\'>Email</a>';
				echo '</td>';
			}
			echo "</tr>";

		}

	} else{
		echo "<h2>No Record Found.</h2>";
	}



	echo '</table></div></div>';

	?>
</form>
