<?php
/*
Payment/Invoice Listing SEA
*/

// IMPORTANT NOTE FOR CROSS SOFTWARE FUNCTIONALITY:

// **** IMPORTANT NOTE: $number_of_connections variable is set only in the database_connection.php file. You must put this variable in manually for this to work. Please see one of SEA's database_connection.php files in order to see how these variables are set up. If you are trying to copy this cross-software functionality, it is advised that you use the exact same format/variable names that SEA's database_connection.php file contains. *****

// DONE IMPORTANT NOTE FOR CROSS SOFTWARE FUNCTIONALITY //
include_once ('../include.php');
include_once('../tcpdf/tcpdf.php');

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
.open-approval { cursor:pointer; }
.open-approval:hover { cursor:pointer; text-decoration:underline; }
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
?>
<script type="text/javascript">
$(document).ready(function() {
	$('.send_cancel').click(
        function() {
			var id = $(this).val();
			$('.approve-box-'+id).hide();
			$('.getemailsapprove').val('');

		});

	$('.getemailsapprove2').focusout(
        function() {
			$('.getemailsapprove').val($(this).val());
		});

	$('.sendemailapprovesubmit').click(
        function() {
			if($('.getemailsapprove').val() == '') {
				alert("Please enter at least one email.");
				return false;
			};
		});

	$('.iframe_open').click(function(){
		var posid = $(this).data('posid');
		$('#iframe_instead_of_window').attr('src', 'cross_software_order.php?posid='+posid);
		$('.iframe_title').text('Tile Status History');
		$('.iframe_holder').show();
		$('.hide_on_iframe').hide();
	});

	$('.close_iframer').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});

	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		this.style.height = (this.contentWindow.document.body.offsetHeight + 80) + 'px';
	});
});

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

function disapprove_button(sel) {
	if (confirm('Are you sure you want to disapprove this P.O.?')) {
		var status = sel.id;
		var arr = status.split('_');
		var id = arr[0];
		var dbc = arr[1];
		var message = prompt("Optional: Leave a message explaining why you have disapproved this P.O.:", "");

		if (message != null) {
			message = message.replace(/[^\w\s]/gi, '');
		} else {
			message = '';
		}
		$.ajax({    //create an ajax request to load_page.php
					type: "GET",
					url: "pos_ajax_all.php?fill=cross_software_approval&dbc="+dbc+"&disapprove=true&name="+message+'&status='+id,
					dataType: "html",   //expect html to be returned
					success: function(response){
						alert("You have successfully disapproved this P.O.");
						//console.log(response);
						location.reload();
				}
		});
	}
}

function approvebutton(sel) {
	if (confirm('Are you sure you want to approve this P.O.?')) {
		var status = sel.id;
		var arr = status.split('_');
		var id = arr[0];
		var dbc = arr[1];
		$.ajax({    //create an ajax request to load_page.php
					type: "GET",
					url: "pos_ajax_all.php?fill=cross_software_approval&dbc="+dbc+"&status="+id,
					dataType: "html",   //expect html to be returned
					success: function(response){
						alert("You have successfully approved this P.O.");
						//console.log(response);
						location.reload();
				}
		});
	}
}


</script>
<?php
$numodays = '';
$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='po_archive_after_num_days'"));
			if($get_config['configid'] > 0) {
				$get_num_of_days = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM	general_configuration WHERE	name='po_archive_after_num_days'"));
				$numodays = $get_num_of_days['value'];

			}

?>
<a href="../img/help_po_cross_software.png" target="_BLANK"><img style="margin:10px;" src="../img/icons/info.png" width="30px" class="wiggle-me"></a>
<!--<div class='mobile-100-container' >-->
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
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Vendor' or category='Vendors' AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
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
		<?php } ?>

		</div>
	<?php

	if (isset($_POST['display_all_invoice'])) {
		$invoice_name = '';
	}

	$result_set = [];
	if(isset($number_of_connections) && $number_of_connections > 0) {
		foreach (range(1, $number_of_connections) as $i) {
			$dbc_cross = ${'dbc_cross_'.$i};
			$url_cross = ${'software_url_'.$i};
			$query_check_credentials = "SELECT inv.*, c.*, '$url_cross' URL FROM purchase_orders inv,  contacts c
				WHERE inv.contactid = c.contactid AND inv.deleted = 0 AND (cross_software != '' AND cross_software IS NOT NULL)
				AND inv.status != 'Completed' ".$search." ORDER BY inv.invoice_date, inv.posid DESC";
			if($result = mysqli_query($dbc_cross, $query_check_credentials)) {
				$result_set = array_merge($result_set, mysqli_fetch_all($result, MYSQLI_ASSOC));
			}
		}
		$num_of_rows = count($result_set);

		if($num_of_rows > 0) {
			// Added Pagination //
			$rowsPerPage = 25;
			$pageNum = 1;

			if(isset($_GET['page'])) {
				$pageNum = $_GET['page'];
			}

			$offset = ($pageNum - 1) * $rowsPerPage;
			if($offset > $num_rows) {
				$pageNum = 1;
				$offset = 0;
			}
			echo display_pagination($dbc, "SELECT $num_of_rows numrows", $pageNum, $rowsPerPage);
			// Pagination Finish //
			echo "<div id='no-more-tables'><table class='table table-bordered table-striped'>";
			echo "<thead><tr class='hidden-xs hidden-sm'>";
					echo '<th><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order Number shown on approval."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span>P.O. #</th>';
					echo '<th><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order Date as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span>Date of Order</th>';
					echo '<th><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Vendor name as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span>Ordered By</th>';
					echo '<th><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Total price as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span>Total Price</th>';
					echo '<th><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Payment type as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span>Payment Type</th>';
					echo '<th><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Inventory items that were ordered."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span>Order</th>';
					echo '<th><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Delivery/Shipping Type as selected on the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span>Delivery/Shipping Type</th>';
					echo '<th><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Purchase Order created into a PDF document. This opens in a new tab on your computer."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span>P.O. Invoice/PDF</th>';
					echo '<th><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Comment from the Order Form."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span>Comment</th>';
					echo '<th><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="See the current status of a P.O."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span>Current Status</th>';
					echo '<th><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="Allows you to approve or disapprove a P.O."><img src="'. WEBSITE_URL .'/img/info-w.png" width="20"></a></span>Approve/Disapprove</th>';

			echo "</tr></thead>";

			for($i = $offset; $i < $num_of_rows && $i < $rowsPerPage * $pageNum; $i++) {
				$roww = $result_set[$i];
				$style2 = '';
				$style = '';
				$contactid = $roww['contactid'];
				$software_url_get = $roww['URL'];

				/*
				 * Get the correct DB connection.
				 * Names are taken from Global software database_connection.php
				 */
				$sentby = $roww['software_author'];

				// Previously the Approval/Disapproval ID was using $roww['posid'].'_'.$i We honour this, in case any other software is using it.
				$dbc_hash = $i;

				$rookconnect = get_software_name();
				if ( $rookconnect=='sea' || $rookconnect=='localhost' ) {
					if ( strpos(strtolower($sentby), 'vancouver') ) {
						$dbc_hash = 1;
					}
					if ( strpos(strtolower($sentby), 'alberta') ) {
						$dbc_hash = 2;
					}
					if ( strpos(strtolower($sentby), 'saskatoon') ) {
						$dbc_hash = 3;
					}
					if ( strpos(strtolower($sentby), 'regina') ) {
						$dbc_hash = 4;
					}
				}

				echo "<tr style='".$style.$style2."'>";
					echo '<td data-title="P.O. #"">';
					if ($roww['cross_software_approval'] !== "" && $roww['cross_software_approval'] !== NULL && $roww['cross_software_approval'] !== 'disapproved') {
						echo $roww['posid'];
					} else {
						echo "P.O. must be approved before P.O. # can be seen.";
					}
					echo '</td>';
					echo '<td data-title="Date of Order">'.$roww['invoice_date'].'</td>';
					echo '<td data-title="Ordered By">' . $roww['software_author'] . '</td>';
					echo '<td data-title="Total Price">' . $roww['total_price'] . '</td>';
					//Code was not working, so I had to manually pull from DB below ---v
					$get_pay_type = mysqli_fetch_assoc(mysqli_query($dbc_cross,"SELECT * FROM purchase_orders WHERE posid='".$roww['posid']."'"));
					echo '<td data-title="Payment Type">' . $get_pay_type['payment_type'] . '</td>';
					echo '<td data-title="Inventory Ordered"><a href="" onclick="return false;" data-posid="'.$roww['posid'].'" class="iframe_open">View Order</a></td>';
					echo '<td data-title="Delivery/Shipping Type">' . $roww['delivery_type'] . '</td>';
					echo '<td data-title="P.O. Invoice/PDF">';
					if ($roww['cross_software_approval'] !== "" && $roww['cross_software_approval'] !== NULL && $roww['cross_software_approval'] !== 'disapproved') {
						echo '<a target="_blank" href="'.$software_url_get.'/Purchase Order/download/purchase_order_'.$roww['posid'].'.pdf">PDF <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
					} else {
						echo "P.O. must be approved before Invoice/PDF can be seen.";
					}
					echo '</td>';
					echo '<td data-title="Comment">' .  html_entity_decode($roww['comment']) . '</td>';
					echo '<td data-title="Current Status">';
					if($roww['status'] == 'Complete' || $roww['status'] == 'Completed') {
						echo '<img class="wiggle-me" src="../img/icons/success.png" width="25px">';
					}
					$status_name = $roww['status'];
					if ($roww['cross_software_approval'] == "" && $roww['cross_software_approval'] == NULL) {
						$status_name = '<span style="font-weight:bold; color:red;"><img class="wiggle-me" src="../img/icons/alarm-1.png" width="25px"> Awaiting Your Approval</span>';
					}
					echo $status_name;
					echo '</td>';
					echo '<td data-title="Approval">';
					if ($roww['cross_software_approval'] !== "" && $roww['cross_software_approval'] !== NULL && $roww['cross_software_approval'] !== 'disapproved') {
						$approve = '<span style="color:red; font-weight:bold;">Approved</span>';
						$disapprove = 'Disapprove';
					} else if($roww['cross_software_approval'] == 'disapproved') {
						$approve = 'Approve';
						$disapprove = '<span style="color:red; font-weight:bold;">Disapproved</span>';
					} else {
						$approve = 'Approve';
						$disapprove = 'Disapprove';
					}
						echo '<span class="open-approval" onclick="approvebutton(this)" id="'.$roww['posid'].'_'.$dbc_hash.'"><img class="wiggle-me" src="../img/icons/like.png" width="25px"> '.$approve.'</span><br>';
						echo '<span class="open-approval" onclick="disapprove_button(this)" id="'.$roww['posid'].'_'.$dbc_hash.'"><img class="wiggle-me" src="../img/icons/dislike.png" width="25px"> '.$disapprove.'</span>';
					echo '</td>';
				echo "</tr>";
			}

			echo '</table></div>';
			// Added Pagination //
			echo display_pagination($dbc, "SELECT $num_of_rows numrows", $pageNum, $rowsPerPage);
			// Pagination Finish //
		}
		else {
			echo "<h2>No Record Found.</h2>";
		}
	} else {
		echo "You currently don't have any connections set up to any other software, please talk to your software administrator if you would like to set this functionality up.";
		$number_of_connections = 0;
	}

	?>
</form>