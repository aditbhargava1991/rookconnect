<?php
error_reporting(0);
/*
Payment/Invoice Listing SEA
*/
include_once ('../include.php');
include_once('../tcpdf/tcpdf.php');

if (isset($_POST['send_drive_logs'])) {
	$email_list = $_POST['email_list'];
    if ($email_list !== '' || $_POST['pdf_send'] !== null) {

			$emails_arr = explode( ',', $email_list );

			foreach( $emails_arr as $email )
			{
				if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false) {

				} else {
					 echo '<script type="text/javascript"> alert("One or more of the email addresses you have provided is not a proper email address.");
							window.location.replace("index.php?tab=payable&subtab='.$_GET['subtab'].'&projectid='.$_GET['projectid'].'&businessid='.$_GET['businessid'].'&siteid='.$_GET['siteid'].'&vendorid='.$_GET['vendorid'].'"); </script>';
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
                $meeting_attachment .= 'download/invoice_'.$drivinglogid.'.pdf*#FFM#*';
            }
        }
		send_email([$_POST['email_address']=>$_POST['email_name']], $to, '', '', $_POST['email_subject'], $message, $meeting_attachment);


    echo '<script type="text/javascript"> alert("PDF(s) sent to '.$email_list.'.");
	window.location.replace("index.php?tab=payable&subtab='.$_GET['subtab'].'&projectid='.$_GET['projectid'].'&businessid='.$_GET['businessid'].'&siteid='.$_GET['siteid'].'&vendorid='.$_GET['vendorid'].'"); </script>';
	} else {
	echo '<script type="text/javascript"> alert("Please enter at least 1 email address, or make sure you have selected at least one PDF to send.");
	window.location.replace("index.php?tab=payable&subtab='.$_GET['subtab'].'&projectid='.$_GET['projectid'].'&businessid='.$_GET['businessid'].'&siteid='.$_GET['siteid'].'&vendorid='.$_GET['vendorid'].'"); </script>';
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

$get_invoice =	mysqli_query($dbc,"SELECT posid FROM  purchase_orders  WHERE `invoice_date` + INTERVAL 30 DAY < NOW() AND status!='Completed'");
$num_rows = mysqli_num_rows($get_invoice);
if($num_rows > 0) {
    while($row = mysqli_fetch_array( $get_invoice )) {
        $posid = $row['posid'];
		//$query_update_project = "UPDATE `purchase_orders` SET status = 'Posted Past Due' WHERE `posid` = '$posid'";
		//$result_update_project = mysqli_query($dbc, $query_update_project);
    }
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

		$('.change_id').focusout(function() {
			var val = $(this).val();
			var id = $(this)[0].id;
			$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "pos_ajax_all.php?fill=change_id&val="+val+'&id='+id,
				dataType: "html",   //expect html to be returned
				success: function(response){
				}
			});
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
<?php $current_cat = (empty($_GET['category']) ? $cat_list[0] : $_GET['category']); ?>

<?php
$numodays = '';
$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='po_archive_after_num_days'"));
			if($get_config['configid'] > 0) {
				$get_num_of_days = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM	general_configuration WHERE	name='po_archive_after_num_days'"));
				$numodays = $get_num_of_days['value'];

			}

?>

<?php if($categories != '') {
	echo "<div class='tab-container offset-left-15 mobile-100-container'>";
	foreach($cat_list as $cat_tab) {
		echo "<a href='?tab=payable&category=$cat_tab'  class='btn brand-btn mobile-block mobile-100 ".($current_cat == $cat_tab ? 'active_tab' : '')."'>$cat_tab</a>";
	}
	echo "</div>";
} ?>

<form name="invoice_table" method="post" action="" class="form-inline offset-top-20" role="form">

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
        if(!empty($_GET['vendorid']) && !isset($_POST['search_vendor'])) {
            $_POST['search_vendor'] = $_GET['vendorid'];
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
		if(!empty($_GET['ticket'])) {
			$search .= " AND t.ticket_type='".filter_var($_GET['ticket'],FILTER_SANITIZE_STRING)."'";
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
		<div id='no-more-tables'>
	<?php
	if (isset($_POST['display_all_invoice'])) {
		$invoice_name = '';
	}
	$query_check_credentialss = "SELECT inv.*, c.* FROM purchase_orders inv LEFT JOIN contacts c ON inv.contactid=c.contactid LEFT JOIN tickets t ON inv.ticketid=t.ticketid WHERE inv.deleted = 0 AND inv.status = 'Paying' ".$search." ORDER BY inv.posid DESC";

	// how many rows we have in database
	$queryy = "SELECT COUNT(posid) AS numrows FROM purchase_orders";

	if($invoice_name == '') {
	   // echo '<h1 class="single-pad-bottom">'.display_pagination($dbc, $queryy, $pageNumm, $rowsPerPagee).'</h1>';
	}

	$resultt = mysqli_query($dbc, $query_check_credentialss);

	$num_rowss = mysqli_num_rows($resultt);
	if($num_rowss > 0) {


		echo "<br clear='all' /><table class='table table-bordered table-striped'>";
		echo "<thead><tr class='hidden-xs hidden-sm'>";
			if (strpos($value_config, ','."Invoice #".',') !== FALSE) {
				echo '<th><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="This will automatically fill in as you fill out each Purchase Order."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Invoice #</th>';
			}
			if (strpos($value_config, ','."Invoice Date".',') !== FALSE) {
				echo '<th><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="The day the Purchase Order became an invoice."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Invoice Date</th>';
			}
			if (strpos($value_config, ','."Customer".',') !== FALSE) {
				echo '<th><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Vendor name as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Vendor</th>';
			}
			if (strpos($value_config, ','."Equipment".',') !== FALSE) {
				echo '<th><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Equipment as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Equipment</th>';
			}
			if (strpos($value_config, ','."Total Price".',') !== FALSE) {
				echo '<th><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Total Price as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Total Price</th>';
			}
			echo '<th><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="When payment to the vendor is due by."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Due Date</th>';
			if (strpos($value_config, ','."Invoice PDF".',') !== FALSE) {
				echo '<th><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="The Purchase Order turned into an invoice. This opens in a new tab on your computer."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Invoice PDF</th>';
			}
			if (strpos($value_config, ','."View Spreadsheet".',') !== FALSE) {
				echo '<th><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order created into a Spreadsheet. This opens in a new tab on your computer."></a></div>P.O. Spreadsheet</th>';
			}
			if (strpos($value_config, ','."Comment".',') !== FALSE) {
				echo '<th><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Comment from the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Comment</th>';
			}
			if (strpos($value_config, ','."Status".',') !== FALSE) {
				echo '<th><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Use the drop down menu to change the status of the Purchase Order. When you change the status, the order will move into the selected tab."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Status</th>';
			}
			echo '<th><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="This is where you will keep track of the payment made on this Purchase Order."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Pay For Items</th>';
			if (strpos($value_config, ','."Send to Anyone".',') !== FALSE) {
			  ?><th><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Check this box to send one or several Purchase Orders in a PDF document, then enter the desired email in the Emails box."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a></div>Email PDF<br><div class='selectall selectbutton' title='This will select all PDFs on the current page.'>Select All</div></th><?php
			}
		echo "</tr></thead>";
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
				$before_change = capture_before_change($dbc, 'point_of_sell', 'deleted', 'posid', $posid);
				$query_update_employee = "UPDATE `point_of_sell` SET deleted = '1' WHERE posid='$posid'";
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
		$customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT name, first_name, last_name FROM contacts WHERE contactid='$contactid'"));

		if (strpos($value_config, ','."Invoice #".',') !== FALSE) {
			$new_id = $roww['new_id_number'];
			if($new_id == '' || $new_id == NULL) {
				$new_id = $roww['posid'];
			}

			echo '<td data-title="P.O. #"><input type="text" id="'.$roww['posid'].'" value="'.$new_id.'" class="change_id form-control" style="max-width:130px;"</td>';
		}
		if (strpos($value_config, ','."Invoice Date".',') !== FALSE) {
			echo '<td data-title="Invoice Date">' . $roww['invoice_date'] . '</td>';
		}
		if (strpos($value_config, ','."Customer".',') !== FALSE) {
			echo '<td data-title="Vendor">' . decryptIt($customer['name']) . '</td>';
		}
		if (strpos($value_config, ','."Equipment".',') !== FALSE) {
			echo '<td data-title="Equipment">' . $dbc->query("SELECT CONCAT(`category`,': ',`make`,' ',`model`,' ',`unit_number`) `label` FROM `equipment` WHERE `equipmentid`='".$roww['equipmentid']."'")->fetch_assoc()['label'] . '</td>';
		}
		if (strpos($value_config, ','."Total Price".',') !== FALSE) {
			echo '<td data-title="Total Price">' . $roww['total_price'] . '</td>';
		}
		echo '<td data-title="Due Date">' . date('Y-m-d', strtotime($roww['invoice_date'] . "+30 days")) . '</td>';
		if (strpos($value_config, ','."Invoice PDF".',') !== FALSE) {
			echo '<td data-title="Invoice PDF"><a target="_blank" href="download/invoice_'.$roww['posid'].'.pdf">PDF <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a></td>';
		}
		if (strpos($value_config, ','."View Spreadsheet".',') !== FALSE) {
			echo '<td data-title="View Spreadsheet">';
			if($roww['spreadsheet_name'] !== NULL && $roww['spreadsheet_name'] !== '' ) {
				echo '<a target="_blank" href="download/'.$roww['spreadsheet_name'].'">Spreadsheet <img style="width:15px;" src="'.WEBSITE_URL.'/img/icons/file.png" title="Spreadsheet"></a></td>';
			} else { echo '-'; }
		}
		if (strpos($value_config, ','."Comment".',') !== FALSE) {
			echo '<td data-title="Comment">' . html_entity_decode ( $roww['comment'] ) . '</td>';
		}
		if (strpos($value_config, ','."Status".',') !== FALSE) {
			echo '<td data-title="Status">';
			?>
			<select name="status[]" id="status_<?php echo $roww['posid']; ?>" class="chosen-select-deselect1 form-control" width="380">
				<option value=""></option>
				<option value="Pending" <?php if ($roww['status'] == "Pending") { echo " selected"; } ?> >Pending</option>
				<option value="Receiving" <?php if ($roww['status'] == "Receiving") { echo " selected"; } ?> >Receiving</option>
				<option value="Paying" <?php if ($roww['status'] == "Paying") { echo " selected"; } ?> >Paying</option>
				<option value="Completed" <?php if ($roww['status'] == "Completed") { echo " selected"; } ?> >Complete</option>
			</select>
		<?php
			echo '</td>';
			}
		 echo '<td data-title="Pay For Items"><a href="receive_pay.php?posid='.$roww['posid'].'&type=pay">Pay For Items</a>';
		 echo '</td>';
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
