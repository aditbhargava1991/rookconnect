<?php
/*
Payment/Invoice Listing SEA
*/
include_once ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
if (isset($_POST['send_drive_log_noemail'])) {
	$poside = $_POST['send_drive_log_noemail'];
	$before_change = capture_before_change($dbc, 'purchase_orders', 'status', 'posid', $poside);
	mysqli_query($dbc, "UPDATE `purchase_orders` SET status = 'Paying' WHERE posid= '".$poside."'" );
    $history = capture_after_change('status', 'Paying');
	  add_update_history($dbc, 'po_history', $history, '', $before_change);
    echo '<script type="text/javascript"> alert("Purchase Order #'.$poside.' sent to Accounts Payable.");
	window.location.replace("complete.php"); </script>';
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
							window.location.replace("complete.php"); </script>';
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
	window.location.replace("complete.php"); </script>';
	} else {
	echo '<script type="text/javascript"> alert("Please enter at least 1 email address, or make sure you have selected at least one PDF to send.");
	window.location.replace("complete.php"); </script>';
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

	$('.open-approval').click(
        function() {
			var id = $(this)[0].id;
			alert(id);
			$('.send_drive_log_noemail-'+id).click();
		});
});

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
<?php if($categories != '') {
	echo "<div class='tab-container offset-left-15 mobile-100-container'>";
	foreach($cat_list as $cat_tab) {
		echo "<a href='?category=$cat_tab'  class='btn brand-btn mobile-block mobile-100 ".($current_cat == $cat_tab ? 'active_tab' : '')."'>$cat_tab</a>";
	}
	echo "</div>";
} ?>

<form name="invoice_table" method="post" action="" class="form-inline offset-top-20" role="form">
	<input type='hidden' class='getemailsapprove' value='' name='getemailsapprove'>
	<div class="row"><?php
		// Search Fields
		$search_any = '';
		$search_vendor = '';
		$search_type = '';
		$search_from = '';
		$search_until = '';
		$search = '';

		if(!empty($_POST['search_any'])) {
			$search_any = $_POST['search_any'];
			$search .= "AND (inv.poid='$search_any' OR c.descript='$search_any' OR inv.grade LIKE '%". $search_any ."%' OR inv.tag LIKE '%" . $search_any . "%' OR inv.detail LIKE '%". $search_any ."%' OR inv.issue_date LIKE '%". $search_any ."%' OR inv.final_total LIKE '%". $search_any ."%') ";
		}
		if(!empty($_POST['search_vendor'])) {
			$search_vendor = $_POST['search_vendor'];
			$search .= " AND c.contactid='$search_vendor'";
		}
		if(!empty($_POST['search_from'])) {
			$search_from = $_POST['search_from'];
			$search .= " AND inv.issue_date >= '$search_from'";
		}
		if(!empty($_POST['search_until'])) {
			$search_until = $_POST['search_until'];
			$search .= " AND inv.issue_date <= '$search_until'";
		} ?>

		<div class="col-sm-6">
			<div class="col-sm-3"><label for="search_any" class="control-label">Search Within Tab:</label></div>
			<div class="col-sm-9"><input placeholder="Search Within Tab..." name="search_any" value="<?php echo $search_any; ?>" class="form-control"></div>
		</div>
		<div class="col-sm-6">
			<div class="col-sm-3"><label for="search_vendor" class="control-label">Search By Vendor:</label></div>
			<div class="col-sm-9">
				<select data-placeholder="Select a Vendor..." name="search_vendor" class="chosen-select-deselect form-control">
					<option value=""></option><?php
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Vendor' or category='Vendors' AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
					foreach($query as $id) {
						$selected = '';
						$selected = $id == $search_vendor ? 'selected = "selected"' : '';
						echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
					} ?>
				</select>
			</div>
		</div>

		<div class="clearfix"></div>

		<div class="col-sm-6">
			<div class="col-sm-3"><label for="search_from" class="control-label">Search From Date:</label></div>
			<div class="col-sm-9"><input placeholder="Search From Date..." name="search_from" value="<?php echo $search_from; ?>" class="datepicker form-control"></div>
		</div>
		<div class="col-sm-6">
			<div class="col-sm-3"><label for="search_until" class="control-label">Search Until Date:</label></div>
			<div class="col-sm-9"><input placeholder="Search Until Date..." name="search_until" value="<?php echo $search_until; ?>" class="datepicker form-control"></div>
		</div>

		<div class="clearfix"></div>

		<div class="pull-right gap-right gap-top">
			<span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Remember to fill in one of the above boxes to search properly."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><button type="submit" name="search_invoice_submit" value="Search" class="btn brand-btn">Search</button>
			<span class="popover-examples list-inline hide-on-mobile" style="margin:0 5px 0 12px"><a data-toggle="tooltip" data-placement="top" title="Refreshes the page to display all order information under the specific tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><a href="" class="btn brand-btn hide-on-mobile">Display All</a>
		</div>
	</div>

	<div class="clearfix"></div><?php

		if (strpos($value_config, ','."Send to Anyone".',') !== FALSE) { ?>
			<!--<div class="clearfix" style='margin:10px;'></div>-->
			<div class="row pad-10 offset-top-20">
				<div class="col-lg-3 col-md-3 col-sm-12 col-xs-12" style="margin:5px 0 10px 0; padding:0px 15px;">
					<label for="search_vendor" class="control-label" style='width:100%;'><span class="popover-examples list-inline" style="margin:0 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Remember to check the boxes of the PDF’s that you would like to be emailed."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Emails (Separated by a Comma):</label>
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

			<!--<div class="clearfix" style='margin:10px;'></div>--><?php
		} ?>

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

	/* Pagination Counting */
	$rowsPerPage = 25;
	$pageNum = 1;

	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}

	$offset = ($pageNum - 1) * $rowsPerPage;

	// Loop through all of the databases and get all of the completed orders, then combine the results into a single array
	$result_set = [];
	for($i = 0; $i < (!empty($number_of_connections) ? $number_of_connections : 0); $i++) {
		$dbc_cross = ${'dbc_cross_'.($i+1)};
		$url_cross = ${'software_url_'.($i+1)};
		$query_check_credentials = "SELECT inv.*, c.*, '$url_cross' URL FROM site_work_po inv, contacts c WHERE inv.vendorid=c.contactid AND inv.deleted=0 ". $search ." ORDER BY inv.poid DESC";
		if($result = mysqli_query($dbc_cross, $query_check_credentials)) {
			$result_set = array_merge($result_set, mysqli_fetch_all($result, MYSQLI_ASSOC));
		}
	}

	$query_check_credentials = "SELECT inv.*, c.*, '".WEBSITE_URL."' URL FROM site_work_po inv, contacts c WHERE inv.vendorid=c.contactid AND inv.deleted=0 ". $search ." ORDER BY inv.poid DESC";
	if($result = mysqli_query($dbc, $query_check_credentials)) {
		$result_set = array_merge($result_set, mysqli_fetch_all($result, MYSQLI_ASSOC));
	}

	$num_rows = count($result_set);
	if($num_rows > 0) {
		// Added Pagination //
		if($offset > $num_rows) {
			$pageNum = 1;
			$offset = 0;
		}
		echo display_pagination($dbc, "SELECT $num_rows numrows", $pageNum, $rowsPerPage);
		// Pagination Finish //
		?>

		<br clear='all' />
		<div id='no-more-tables'>
			<table class='table table-bordered table-striped'>
                <thead>
                    <tr class='hidden-xs hidden-sm'>
                        <th width="6%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order Number as selected on the Order Form."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a></div>P.O. #</th>
                        <th width="6%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order Date as selected on the Order Form."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a></div>P.O. Date</th>
                        <th width="12%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Vendor name as selected on the Order Form."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a></div>Vendor</th>
                        <th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Total Price as selected on the Order Form."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a></div>Total Price</th>
                        <th width="6%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order created into a PDF document. This opens in a new tab on your computer."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a></div>P.O. PDF</th>
                        <th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Check this box to send one or several Purchase Orders in a PDF document, then enter the desired email in the Emails box."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a></div>Email PDF<br><div class='selectall selectbutton' title='This will select all PDFs on the current page.'>Select All</div></th>
                    </tr>
                </thead><?php

		for($i = $offset; $i < $num_rows && $i < $rowsPerPage * $pageNum; $i++)
		{
			$roww = $result_set[$i];
			$contactid = $roww['vendorid'];
			$software_url_get = $roww['URL']; ?>

			<tr>
				<td data-title="P.O. #"><input type="text" value="<?= $roww['poid']; ?>" class="form-control" style="max-width:130px;"></td>
				<td data-title="P.O. Date"><?= $roww['issue_date']; ?></td>
				<td data-title="Vendor"><?= get_client($dbc, $contactid); ?></td>
				<td data-title="Total Price"><?= $roww['total_price']; ?></td>
				<td data-title="P.O. PDF"><a target="_blank" href="<?= $software_url_get.'/Site Work Orders/download/work_order_po_'.$roww['poid'].'.pdf'; ?>">PDF <img src="<?= WEBSITE_URL; ?>/img/pdf.png" title="PDF"></a></td>
				<td data-title="Email PDF"><input style="height: 25px; width: 25px;" type='checkbox' name='pdf_send[]' class='pdf_send' value='<?= $roww['poid']; ?>'></td>
			</tr><?php
		}

		echo '</table>';
		// Added Pagination //
		echo display_pagination($dbc, "SELECT $num_rows numrows", $pageNum, $rowsPerPage);
		// Pagination Finish //
		echo '</div></div>';

	} else{
		echo "<h2>No Record Found.</h2>";
	}
	?>
</form>
