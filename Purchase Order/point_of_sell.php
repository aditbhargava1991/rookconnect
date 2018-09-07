<?php
/*
Payment/Invoice Listing SEA
*/
include_once ('../include.php');
include_once('../tcpdf/tcpdf.php');

if (isset($_POST['send_drive_logs'])) {
error_reporting(0);
	$email_list = $_POST['email_list'];
    if ($email_list !== '' || $_POST['pdf_send'] !== null) {

			$emails_arr = explode( ',', $email_list );

			foreach( $emails_arr as $email )
			{
				if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false) {

				} else {
					 echo '<script type="text/javascript"> alert("One or more of the email addresses you have provided is not a proper email address.");
							window.location.replace("point_of_sell.php"); </script>';
							exit();
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
                $meeting_attachment .= 'download/invoice_'.$drivinglogid.'.pdf*#FFM#*';
            }
        }
		send_email('', $to, '', '', $subject, $message, $meeting_attachment);


    echo '<script type="text/javascript"> alert("PDF(s) sent to '.$email_list.'.");
	window.location.replace("point_of_sell.php"); </script>';
	} else {
	echo '<script type="text/javascript"> alert("Please enter at least 1 email address, or make sure you have selected at least one PDF to send.");
	window.location.replace("point_of_sell.php"); </script>';
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
	</style>
	<?php
$get_invoice =	mysqli_query($dbc,"SELECT posid FROM purchase_orders WHERE `invoice_date` + INTERVAL 30 DAY < NOW() AND status!='Completed'");
$num_rows = mysqli_num_rows($get_invoice);
if($num_rows > 0) {
    while($row = mysqli_fetch_array( $get_invoice )) {
        $posid = $row['posid'];
		//$query_update_project = "UPDATE `purchase_orders` SET status = 'Posted Past Due' WHERE `posid` = '$posid'";
		//$result_update_project = mysqli_query($dbc, $query_update_project);
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
<form name="invoice_table" method="post" action="point_of_sell.php" class="form-inline" role="form">

	<div class="single-pad-bottom">
		<label for="search_site">Search By Any:</label>
		<?php
		$invoice_name = '';
		$searchbycustomer = '';
		$searchbyany = '';
		$search = '';
		$searchbytype = '';
		$contactid = '';
		$type = '';
		if (isset($_POST['search_invoice_submit'])) {
			if($_POST['search_invoice'] !== '') {
				$invoice_name = $_POST['search_invoice'];
				$searchbyany = "AND (inv.posid = '$invoice_name' OR c.name = '$invoice_name' OR inv.delivery_type = '$invoice_name' OR inv.total_price LIKE '%" . $invoice_name . "%' OR inv.payment_type LIKE '%" . $invoice_name . "%' OR inv.invoice_date LIKE '%" . $invoice_name . "%' OR inv.status LIKE '%" . $invoice_name . "%' OR inv.comment LIKE '%" . $invoice_name . "%') ";
			}
			if($_POST['contactid'] != '') {
			   $searchbycustomer = " AND c.name = '".$_POST['contactid']."' ";
			}
			if($_POST['type'] != '') {
			   $searchbytype = " AND inv.delivery_type = '".$_POST['type']."' ";
			}
			$search = $_POST['search_invoice'];
			$contactid = $_POST['contactid'];
			$type = $_POST['type'];
		}
		?>
			<input style="max-width:260px;" type="text" name="search_invoice" value="<?php echo $search; ?>" class="form-control">
			<div style='max-width:260px; display:inline-block;'>
			<select name="contactid" data-placeholder="Choose Customer..." style="max-width:260px !important;" class="chosen-select-deselect form-control width-me" width="380">
			<option value=''>Choose Customer</option>
			   <?php
				$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Customer' or category='Customer' or category='Business' AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
				foreach($query as $id) {
					$selected = '';
					$selected = $id == $contactid ? 'selected = "selected"' : '';
					echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
				}
			  ?>
			</select>
			</div>
			<div style='max-width:260px; display:inline-block;'>
			<select style="max-width:260px;" name="type" data-placeholder="Choose Delivery/Shipping Type..." class="chosen-select-deselect form-control width-me" width="380">
				<option value=''>Choose Delivery/Shipping Type</option>
				<option <?php if ($type == "Pick-Up") { echo " selected"; } ?>  value="Pick-Up">Pick-Up</option>
				<option <?php if ($type == "Company Delivery") { echo " selected"; } ?>  value="Company Delivery">Company Delivery</option>
				<option <?php if ($type == "Drop Ship") { echo " selected"; } ?>  value="Drop Ship">Drop Ship</option>
				<option <?php if ($type == "Shipping") { echo " selected"; } ?>  value="Shipping">Shipping</option>
			</select>
			</div>
			<?php
			$starttimesql = '';
			$starttime = '';
			$endtime = '';
		if (isset($_POST['search_invoice_submit'])) {
			$starttime = $_POST['starttime'];

			$endtime = $_POST['endtime'];
			if(($starttime !== '' && $endtime !== '')){
				$starttimesql = " AND (inv.invoice_date >= '".$starttime."' AND inv.invoice_date <= '".$endtime."') ";
			} else if(($starttime !== '' && $endtime == '')){
				$starttimesql = " AND (inv.invoice_date >= '".$starttime."') ";
			} else if(($starttime == '' && $endtime !== '')){
				$starttimesql = " AND (inv.invoice_date <= '".$endtime."') ";
			}
		}
		?>
		<div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">From:</label>
			<div class="col-sm-8">
				<input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></p>
			</div>
		</div>

		<!-- end time -->
		<div class="form-group until">
			<label for="site_name" class="col-sm-4 control-label">Until:</label>
			<div class="col-sm-8" >
				<input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></p>
			</div>
		</div>

		<button type="submit" name="search_invoice_submit" value="Search" class="btn brand-btn">Search</button>
		<button type="submit" name="display_all_invoice" value="Display All" class="btn brand-btn">Display All</button><br><br>
		<?php
		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT purchase_order_dashboard FROM field_config"));
		$value_config = ','.$get_field_config['purchase_order_dashboard'].',';
		if (strpos($value_config, ','."Send to Anyone".',') !== FALSE) { ?>
		<label for="search_vendor" class="control-label">Emails (Separated by a Comma):</label>
		<input id='roll-input' type='text' style='max-width:300px;' name='email_list' placeholder='Enter emails here...' class='form-control email_driving_logs'>
		<button onClick="return empty()" type='submit' name='send_drive_logs' class='btn brand-btn dl_send_butt'>Send PDF(s)</button>
		<div class='selectall selectbutton sel2' title='This will select all PDFs on the current page.'>Select All</div><br><br>
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

	if (isset($_POST['search_invoice_submit'])) {
		$query_check_credentialss = "SELECT inv.*, c.* FROM purchase_orders inv,  contacts c WHERE inv.contactid = c.contactid AND inv.deleted = 0  ".$searchbyany." ".$searchbycustomer." ".$searchbytype." ".$starttimesql." ORDER BY inv.posid DESC";
	} else {
		$query_check_credentialss = "SELECT * FROM purchase_orders WHERE deleted = 0 AND (status='Completed' OR status='Void') ORDER BY posid DESC LIMIT 25";
	}

	// how many rows we have in database
	$queryy = "SELECT COUNT(posid) AS numrows FROM purchase_orders";

	if($invoice_name == '') {
	   // echo '<h1 class="single-pad-bottom">'.display_pagination($dbc, $queryy, $pageNumm, $rowsPerPagee).'</h1>';
	}

	$resultt = mysqli_query($dbc, $query_check_credentialss);

	$num_rowss = mysqli_num_rows($resultt);
	if($num_rowss > 0) {


		echo "<div id='no-more-tables'><table class='table table-bordered'>";
		echo "<tr class='hidden-xs hidden-sm'>";
			if (strpos($value_config, ','."Invoice #".',') !== FALSE) {
				echo '<th>Invoice #</th>';
			}
			if (strpos($value_config, ','."Invoice Date".',') !== FALSE) {
				echo '<th>Invoice Date</th>';
			}
			if (strpos($value_config, ','."Customer".',') !== FALSE) {
				echo '<th>Customer</th>';
			}
			if (strpos($value_config, ','."Total Price".',') !== FALSE) {
				echo '<th>Total Price</th>';
			}
			if (strpos($value_config, ','."Payment Type".',') !== FALSE) {
				echo '<th>Payment Type</th>';
			}
			if (strpos($value_config, ','."Delivery/Shipping Type".',') !== FALSE) {
				echo '<th>Delivery/Shipping Type</th>';
			}
			if (strpos($value_config, ','."Invoice PDF".',') !== FALSE) {
				echo '<th>Invoice PDF</th>';
			}
			if (strpos($value_config, ','."Comment".',') !== FALSE) {
				echo '<th>Comment</th>';
			}
			if (strpos($value_config, ','."Status".',') !== FALSE) {
				echo '<th>Status</th>';
			}
			if (strpos($value_config, ','."Send to Client".',') !== FALSE) {
				echo '<th>Send to Client</th>';
			}
			if (strpos($value_config, ','."Send to Anyone".',') !== FALSE) {
			  ?><th>Email PDF<br><div class='selectall selectbutton' title='This will select all PDFs on the current page.'>Select All</div></th><?php
			}
		echo "</tr>";
	} else{
		echo "<h2>No Record Found.</h2>";
	}

	while($roww = mysqli_fetch_array( $resultt ))
	{
		$style2 = '';
		if($numodays > 0) {
			$cutoffdater = date('Y-m-d', strtotime($roww['invoice_date']. ' + '.$numodays.' days'));
			$date = date('Y/m/d', time());
			if (new DateTime($date) >= new DateTime($cutoffdater)) {
				$posid = $roww['posid'];
				$before_change = capture_before_change($dbc, 'purchase_orders', 'deleted', 'posid', $posid);
				$query_update_employee = "UPDATE `purchase_orders` SET deleted = '1' WHERE posid='$posid'";
				$history = capture_after_change('deleted', '1');
				add_update_history($dbc, 'po_history', $history, '', $before_change);
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
			echo '<td data-title="Invoice #"">' . $roww['posid'] . '</td>';
		}


		if (strpos($value_config, ','."Invoice Date".',') !== FALSE) {

			echo '<td data-title="Invoice Date">'.$roww['invoice_date'].'</td>';
		}
		if (strpos($value_config, ','."Customer".',') !== FALSE) {
			echo '<td data-title="Customer">' . get_client($dbc, $contactid) . '</td>';
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
			echo '<td data-title="Invoice PDF"><a target="_blank" href="download/invoice_'.$roww['posid'].'.pdf">PDF <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a></td>';
		}
		if (strpos($value_config, ','."Comment".',') !== FALSE) {
			echo '<td data-title="Comment">' .  html_entity_decode($roww['comment']) . '</td>';
		}
		if (strpos($value_config, ','."Status".',') !== FALSE) {
			echo '<td data-title="Status">';
			?>
			<select name="status[]" id="status_<?php echo $roww['posid']; ?>" class="chosen-select-deselect1 form-control" width="380">
				<option value=""></option>
				<option value="Posted" <?php if ($roww['status'] == "Posted") { echo " selected"; } ?> >Posted</option>
				<option value="Posted Past Due" <?php if ($roww['status'] == "Posted Past Due") { echo " selected"; } ?> >Posted Past Due</option>
				<option value="Completed" <?php if ($roww['status'] == "Completed") { echo " selected"; } ?> >Completed</option>
				<option value="Void" <?php if ($roww['status'] == "Void") { echo " selected"; } ?> >Void</option>
				<option value="Archived" <?php if ($roww['status'] == "Archived") { echo " selected"; } ?> >Archive</option>
			</select>
		<?php
			echo '</td>';
			}

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

	echo '</table></div></div>';

	if($invoice_name == '') {
		//echo display_pagination($dbc, $queryy, $pageNumm, $rowsPerPagee);
	}

	?>
</form>
