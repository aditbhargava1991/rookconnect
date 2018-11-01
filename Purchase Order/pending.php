<?php
/*
Payment/Invoice Listing SEA
*/

// IMPORTANT NOTE FOR CROSS SOFTWARE FUNCTIONALITY:

// **** IMPORTANT NOTE: THE $number_of_connections variable is set only in the database_connection.php file. You must put this variable in manually for this to work. Please see one of SEA's database_connection.php files in order to see how these variables are set up. If you are trying to copy this cross-software functionality, it is advised that you use the exact same format/variable names that SEA's database_connection.php file contains. *****

// DONE IMPORTANT NOTE FOR CROSS SOFTWARE FUNCTIONALITY //
include_once ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
if (isset($_POST['send_drive_log_noemail'])) {
	$poside = $_POST['send_drive_log_noemail'];
	$before_change = capture_before_change($dbc, 'purchase_orders', 'approval', 'posid', $poside);
	$before_change .= capture_before_change($dbc, 'purchase_orders', 'status', 'posid', $poside);
	mysqli_query($dbc, "UPDATE `purchase_orders` SET approval = 'Approved', status = 'Receiving' WHERE posid= '".$poside."'" );
	$history = capture_after_change('approval', 'Approved');
	$history .= capture_after_change('status', 'Receiving');
	add_update_history($dbc, 'po_history', $history, '', $before_change);
    echo '<script type="text/javascript"> alert("Purchase Order #'.$poside.' approved.");
	window.location.replace("index.php?tab=pending&subtab='.$_GET['subtab'].'&projectid='.$_GET['projectid'].'&businessid='.$_GET['businessid'].'&siteid='.$_GET['siteid'].'&vendorid='.$_GET['vendorid'].'"); </script>';
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
					 echo '<script type="text/javascript"> alert("One or more of the email addresses you have provided is not a proper email address.");
							window.location.replace("index.php?tab=pending&subtab='.$_GET['subtab'].'&projectid='.$_GET['projectid'].'&businessid='.$_GET['businessid'].'&siteid='.$_GET['siteid'].'&vendorid='.$_GET['vendorid'].'"); </script>';
							exit();
				}
			}
	$to_email = $email_list;
	$to = explode(',', $to_email);
	$message = "Please see the attached Purchase Order below.";
	$meeting_attachment .= 'download/purchase_order_'.$poside.'.pdf';
	send_email([$_POST['email_address']=>$_POST['email_name']], $to, '', '', $_POST['email_subject'], $message, $meeting_attachment);
	$before_change = capture_before_change($dbc, 'purchase_orders', 'approval', 'posid', $poside);
	$before_change .= capture_before_change($dbc, 'purchase_orders', 'status', 'posid', $poside);
	mysqli_query($dbc, "UPDATE `purchase_orders` SET approval = 'Approved', status = 'Receiving' WHERE posid= '".$poside."'" );
	$history = capture_after_change('approval', 'Approved');
	$history .= capture_after_change('status', 'Receiving');
	add_update_history($dbc, 'po_history', $history, '', $before_change);
    echo '<script type="text/javascript"> alert("Purchase Order #'.$poside.' approved and sent to '.$email_list.'.");
	window.location.replace("index.php?tab=pending&subtab='.$_GET['subtab'].'&projectid='.$_GET['projectid'].'&businessid='.$_GET['businessid'].'&siteid='.$_GET['siteid'].'&vendorid='.$_GET['vendorid'].'"); </script>';
	} else {
	echo '<script type="text/javascript"> alert("Please enter at least 1 email address.");
	window.location.replace("index.php?tab=pending&subtab='.$_GET['subtab'].'&projectid='.$_GET['projectid'].'&businessid='.$_GET['businessid'].'&siteid='.$_GET['siteid'].'&vendorid='.$_GET['vendorid'].'"); </script>';
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
					 echo '<script type="text/javascript"> alert("One or more of the email addresses you have provided is not a proper email address.");
							window.location.replace("index.php?tab=pending&subtab='.$_GET['subtab'].'&projectid='.$_GET['projectid'].'&businessid='.$_GET['businessid'].'&siteid='.$_GET['siteid'].'&vendorid='.$_GET['vendorid'].'"); </script>';
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
	window.location.replace("index.php?tab=pending&subtab='.$_GET['subtab'].'&projectid='.$_GET['projectid'].'&businessid='.$_GET['businessid'].'&siteid='.$_GET['siteid'].'&vendorid='.$_GET['vendorid'].'"); </script>';
	} else {
	echo '<script type="text/javascript"> alert("Please enter at least 1 email address, or make sure you have selected at least one PDF to send.");
	window.location.replace("index.php?tab=pending&subtab='.$_GET['subtab'].'&projectid='.$_GET['projectid'].'&businessid='.$_GET['businessid'].'&siteid='.$_GET['siteid'].'&vendorid='.$_GET['vendorid'].'"); </script>';
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
.open-approval:hover { cursor:pointer; text-decoration:underline; font-style: italic; }
	</style>
	<?php
$get_invoice =	mysqli_query($dbc,"SELECT posid FROM purchase_orders WHERE `invoice_date` + INTERVAL 30 DAY < NOW() AND status!='Completed'");
$num_rows = mysqli_num_rows($get_invoice);
if($num_rows > 0) {
    while($row = mysqli_fetch_array( $get_invoice )) {
        $posid = $row['posid'];
		//$query_update_project = "UPDATE `purchase_orders` SET status = 'Posted Past Due' WHERE `posid` = '$posid'";
	//	$result_update_project = mysqli_query($dbc, $query_update_project);
    }
}

if((!empty($_GET['type'])) && ($_GET['type'] == 'send_email')) {
    $type = $_GET['type'];
    $posid = $_GET['id'];


}
$approvals = approval_visible_function($dbc, 'purchase_order'); ?>
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
$(document).on('change', 'select[name="project"]', function() { changeProject(this); });
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
		url: "pos_ajax_all.php?fill=POSstatus&name="+arr[1]+'&status='+status,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
		}
	} else if(status == 'Pending') {
			$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "pos_ajax_all.php?fill=POSstatus&name="+arr[1]+'&status='+status,
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
		url: "pos_ajax_all.php?fill=changeProject&po="+poid[1]+"&id="+id,
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
		url: "pos_ajax_all.php?fill=changeTicket&po="+poid[1]+"&id="+id,
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
		url: "pos_ajax_all.php?fill=changeWorkOrder&po="+poid[1]+"&id="+id,
		dataType: "html",
		success: function(response){
			location.reload();
		}
	});
}
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('purchase_order');

$current_cat = (empty($_GET['category']) ? $cat_list[0] : $_GET['category']);

?>
<div class="clearfix"></div>
<?php if($categories != '') {
	echo "<div class='tab-container offset-left-15 mobile-100-container'>";
	foreach($cat_list as $cat_tab) {
		echo "<a href='?category=$cat_tab'  class='btn brand-btn mobile-block mobile-100 ".($current_cat == $cat_tab ? 'active_tab' : '')."'>$cat_tab</a>";
	}
	echo "</div>";
} ?>

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
			<select data-placeholder="Choose Vendor..." name="search_vendor" class="chosen-select-deselect form-control">
				<option value=""></option>
				<?php
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Vendor' AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
					foreach($query as $id) {
						$selected = '';
						$selected = $id == $search_vendor ? 'selected = "selected"' : '';
						echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
					}
				  ?>
			</select>
		</div>

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
					<label for="search_vendor" class="control-label" style='width:100%;'><span class="popover-examples list-inline" style="margin:0 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Remember to check the boxes of the PDF’s that you would like to be emailed."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Email Recipients (separated by a comma):</label>
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

	$query_check_credentialss = "SELECT inv.*, c.* FROM purchase_orders inv LEFT JOIN contacts c ON inv.contactid=c.contactid LEFT JOIN tickets t ON inv.ticketid=t.ticketid WHERE inv.deleted = 0 AND (inv.status='Pending') ".$search." ORDER BY inv.posid DESC";

	// how many rows we have in database
	$queryy = "SELECT COUNT(posid) AS numrows FROM purchase_orders";

	$resultt = mysqli_query($dbc, $query_check_credentialss);

	$num_rowss = mysqli_num_rows($resultt);
	if($num_rowss > 0) {


		echo "<br clear='all' /><div id='no-more-tables'><table class='table table-bordered table-striped'>";
		echo "<thead><tr class='hidden-xs hidden-sm'>";
			if (strpos($value_config, ','."Invoice #".',') !== FALSE) {
				echo '<th width="6%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order Number as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20" /></a></div>P.O. #</th>';
			}
			if (strpos($value_config, ','."Invoice Date".',') !== FALSE) {
				echo '<th width="6%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order Date as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>P.O. Date</th>';
			}
			if (strpos($value_config, ','."Customer".',') !== FALSE) {
				echo '<th width="12%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Vendor name as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Vendor</th>';
			}
			if (strpos($value_config, ','."Equipment".',') !== FALSE) {
				echo '<th width="12%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Equipment as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Equipment</th>';
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
			//    echo '<th>Approve & Send</th>';
			if (strpos($value_config, ','."Send to Client".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Clicking this will send a PDF to the tagged client."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Send to Client</th>';
			}
			if (strpos($value_config, ','."Send to Anyone".',') !== FALSE) {
			  ?><th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Check this box to send one or several Purchase Orders in a PDF document, then enter the desired email in the Emails box."><img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20"></a></div>Email PDF<br><div class='selectall selectbutton' title='This will select all PDFs on the current page.'>Select All</div></th><?php
			}
			if (strpos($value_config, ','."Project".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Change the Project attached to the Purchase Order."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Project</th>';
			}
			if (strpos($value_config, ','."Ticket".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Change the '.TICKET_NOUN.' attached to the Purchase Order."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>'.TICKET_NOUN.'</th>';
			}
			if (strpos($value_config, ','."Work Order".',') !== FALSE) {
				echo '<th width="8%"><div class="popover-examples list-inline" style="margin:2px 5px 5px 0"><a data-toggle="tooltip" data-placement="top" title="Change the Work Order attached to the Purchase Order."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></div>Work Order</th>';
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
		if (strpos($value_config, ','."Equipment".',') !== FALSE) {
			echo '<td data-title="Equipment">' . $dbc->query("SELECT CONCAT(`category`,': ',`make`,' ',`model`,' ',`unit_number`) `label` FROM `equipment` WHERE `equipmentid`='".$roww['equipmentid']."'")->fetch_assoc()['label'] . '</td>';
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
			if($roww['software_seller'] == 'main' && $approvals > 0) {
				if ($roww['cross_software_approval'] !== "" && $roww['cross_software_approval'] !== NULL && $roww['cross_software_approval'] !== 'disapproved') { ?>
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
			} else if($approvals > 0) {
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
			} else {
				echo $roww['status'];
			}
		}
			/* echo '<td data-title="Approve"><span class="open-approval" onclick="approvebutton(this)" id="'.$roww['posid'].'">Approve</span>';
				  ?>
					<div class="approve-box-<?php echo $roww['posid']; ?> approve-box">Please enter the email(s) (separated by a comma) you would like to send this Order to.<br>(If you prefer not to send the P.O., hit skip.)<br><br>
					<input type='text' style='max-width:300px;' name='' placeholder='email1@example.com,email2@example.com' class='form-control getemailsapprove2'><br><br>
					<button type='submit' name='send_drive_logs_approve' class='btn brand-btn sendemailapprovesubmit' value='<?php echo $roww['posid']; ?>'>Approve and Send</button>
					<button type='submit' name='send_drive_log_noemail' class='btn brand-btn ' value='<?php echo $roww['posid']; ?>'>Skip</button>
					<button onClick="hide-box" value="<?php echo $roww['posid']; ?>" type='button' name='send_drive_logs' class='btn brand-btn send_cancel'>Cancel</button>
					</div>
				  <?php
			echo '</td>'; */

		if (strpos($value_config, ','."Send to Client".',') !== FALSE) {
			if($roww['software_seller'] == 'main') {
				if ($roww['cross_software_approval'] !== "" && $roww['cross_software_approval'] !== NULL && $roww['cross_software_approval'] !== 'disapproved') {
					if($roww['status'] == "Void") {
						echo '<td data-title="Send to Client">'.$roww['status_history'].'</td>';
					} else {
					   echo '<td data-title="Send to Client"><a href="?tab=send_pos&posid='.$roww['posid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Send</a></td>';
					}
				}
			} else {
				if($roww['status'] == "Void") {
					echo '<td data-title="Send to Client">'.$roww['status_history'].'</td>';
				} else {
				   echo '<td data-title="Send to Client"><a href="?tab=send_pos&posid='.$roww['posid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Send</a></td>';
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

		if (strpos($value_config, ','."Project".',') !== FALSE) {
			echo '<td data-title="Project">'; ?>
			<select id="project_<?= $roww['posid']; ?>" name="project" data-placeholder="Choose Project..." class="chosen-select-deselect form-control" width="380">
				<option value=""></option><?php
				$result = mysqli_query($dbc, "SELECT * FROM (SELECT `projectid`, `project_name` FROM `project` WHERE `deleted`=0 UNION SELECT CONCAT('C',`projectid`), `project_name` FROM `client_project` WHERE `deleted`=0) PROJECTS ORDER BY `project_name`");
				while($row = mysqli_fetch_assoc($result)) {
					$selected = "";
					if($row['projectid'] == $roww['projectid']) {
						$selected = 'selected="selected"';
					}
					echo '<option ' . $selected . ' value="' . $row['projectid'] . '">' . $row['project_name'] . '</option>';
				} ?>
			</select><?php
			echo '</td>';
		}

	   if (strpos($value_config, ','."Ticket".',') !== FALSE) {
			echo '<td data-title="'.TICKET_NOUN.'">'; ?>
			<select id="ticket_<?= $roww['posid']; ?>" name="ticket" data-placeholder="Choose <?= TICKET_NOUN ?>..." class="chosen-select-deselect form-control" width="380">
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

	echo '</table></div></div>';

	?>
</form>
