<?php
/*
Payment/Invoice Listing SEA
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
if (isset($_POST['send_drive_log_noemail'])) {
	error_reporting(0);
	$poside = $_POST['send_drive_log_noemail'];
	mysqli_query($dbc, "UPDATE `sales_order` SET approval = 'Approved', status = 'Completed' WHERE posid= '".$poside."'" );
    echo '<script type="text/javascript"> alert("'.SALES_ORDER_NOUN.' #'.$poside.' approved.");
	window.location.replace("pending.php"); </script>';
}
if (isset($_POST['send_drive_logs_approve'])) {
error_reporting(0);
	$poside = $_POST['send_drive_logs_approve'];
	$purchaseorderconv = 0;
	$zenearthconv = 0;
	$pointofsaleconv = 0;
	$purchaseorderconv = $_POST['pocheckbox'];
	$pointofsaleconv = $_POST['poscheckbox'];
	$zenearthconv = $_POST['zenearthcheckbox'];

	if ( $purchaseorderconv == 1 || $pointofsaleconv == 1 ) {
		$convo = " and converted.";
	} else {
		$convo = ".";
	}
	$email_list = $_POST['getemailsapprove'];
	include ('converttopoandpos.php');
    if ($email_list !== '') {
				$emails_arr = explode( ',', $email_list );
				foreach( $emails_arr as $email )
				{
					if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false) {
					} else {
						 echo '<script type="text/javascript"> alert("One or more of the email addresses you have provided is not a proper email address.");
								window.location.replace("pending.php"); </script>';
								exit();
					}
				}
		$to_email = $email_list;
		$to = explode(',', $to_email);
		$subject =SALES_ORDER_NOUN." PDF";
		$message = "Please see the attached ".SALES_ORDER_NOUN." below.";
		$meeting_attachment .= 'download/invoice_'.$poside.'.pdf';
		send_email('', $to, '', '', $subject, $message, $meeting_attachment);
		mysqli_query($dbc, "UPDATE `sales_order` SET approval = 'Approved', status = 'Completed' WHERE posid= '".$poside."'" );
    echo '<script type="text/javascript"> alert("'.SALES_ORDER_NOUN.' #'.$poside.' approved and sent to '.$email_list.'.");
	window.location.replace("pending.php"); </script>';
	}
	mysqli_query($dbc, "UPDATE `sales_order` SET approval = 'Approved', status = 'Completed' WHERE posid= '".$poside."'" );
    echo '<script type="text/javascript"> alert("'.SALES_ORDER_NOUN.' #'.$poside.' approved'.$convo.'");
	window.location.replace("pending.php"); </script>';
}
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
							window.location.replace("pending.php"); </script>';
							exit();
				}
			}
		//EMAIL
	$to_email = $email_list;

	$to = explode(',', $to_email);
	$subject =SALES_ORDER_NOUN." PDF(s)";
	$message = "Please see the attached PDF(s) below.";

	 $meeting_attachment = '';
        foreach($_POST['pdf_send'] as $drivinglogid) {
            if($drivinglogid != '') {
                $meeting_attachment .= 'download/invoice_'.$drivinglogid.'.pdf*#FFM#*';
            }
        }
		send_email('', $to, '', '', $subject, $message, $meeting_attachment);


    echo '<script type="text/javascript"> alert("PDF(s) sent to '.$email_list.'.");
	window.location.replace("pending.php"); </script>';
	} else {
	echo '<script type="text/javascript"> alert("Please enter at least 1 email address, or make sure you have selected at least one PDF to send.");
	window.location.replace("pending.php"); </script>';
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
	height:400px;
	top:50%;
	margin-top:-200px;
    left: 50%;
    background: lightgrey;
    color: black;
    border: 10px outset grey;
    border-radius: 15px;
    margin-left: -250px;
    text-align: center;
	z-index:999999;
    padding: 20px;
}
@media (max-width:530px) {
.approve-box {
	width:100%;
	z-index:9999999;
	left:0px;
	margin-left:0px;
	overflow:auto;
}
}
.open-approval { cursor:pointer; text-decoration:underline; }
.open-approval:hover { cursor:pointer; text-decoration:underline; font-style: italic; }
	</style>
	<?php
$get_invoice =	mysqli_query($dbc,"SELECT posid FROM sales_order WHERE `invoice_date` + INTERVAL 30 DAY < NOW() AND status!='Completed'");
$num_rows = mysqli_num_rows($get_invoice);
if($num_rows > 0) {
    while($row = mysqli_fetch_array( $get_invoice )) {
        $posid = $row['posid'];
		//$query_update_project = "UPDATE `sales_order` SET status = 'Posted Past Due' WHERE `posid` = '$posid'";
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
	$('input.unchecker').on('change', function() {
		$('input.unchecker').not(this).prop('checked', false);
		$('.pocheckbox').val('');
		$('.poscheckbox').val('');
			if($(this).hasClass("pocheckings")) {
				$('.pocheckbox').val($(this).val());
			}
			if($(this).hasClass("poscheckings")) {
				$('.poscheckbox').val($(this).val());
			}
	});
	$('input.uncheck_on_cancel').on('change', function() {
			if($(this).hasClass("zenearthcheckings")) {
				$('.zenearthcheckbox').val($(this).val());
			}
	});
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

		$('.iframe_open').click(function(){
		var id = $(this).attr('id');
	   $('#iframe_instead_of_window').attr('src', 'send_point_of_sell.php?posid='+id);
	   $('.iframe_title').text('Currently Sending #'+id+' to Client.');
	   $('.iframe_holder').show();
	   $('.hide_on_iframe').hide();
});

$('.close_iframer').click(function(){
	var result = confirm("If you have not hit the submit button, your changes will not go through. Are you sure you want to close this window?");
	if (result) {
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	}
});


	$('.send_cancel').click(
        function() {
			var id = $(this).val();
			$('.approve-box-'+id).hide();
			$('.getemailsapprove').val('');
			$('.zenearthcheckbox').val('');
			$('.pocheckbox').val('');
			$('.poscheckbox').val('');
			$('.uncheck_on_cancel').prop('checked', false);

		});

	$('.getemailsapprove2').focusout(
        function() {
			$('.getemailsapprove').val($(this).val());
		});

	$('.sendemailapprovesubmit').click(
        function() {
			if($('.getemailsapprove').val() == '' && $('.zenearthcheckbox').val() == '' && $('.pocheckbox').val() == '' && $('.poscheckbox').val() == '' ) {
					alert("Please enter at least one email or select a checkbox to continue.");
					return false;
			};
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


function approvebutton(sel) {
	var status = sel.id;
	$(".approve-box-"+status).show();
	return false;
}


</script>
</head>
<body>
<?php $software_url = $_SERVER['SERVER_NAME']; ?>
<?php include_once ('../navigation.php');
checkAuthorised('sales_order');


?>
<div class="container triple-pad-bottom">
     <div class='iframe_holder' style='display:none;'>

		<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
		<span class='iframe_title' style='color:white; font-weight:bold; position: relative; left: 20px; font-size: 30px;'></span>
		<iframe id="iframe_instead_of_window" style='width: 100%;' height="1000px; border:0;" src=""></iframe>
    </div>
	<div class="row hide_on_iframe">
		<div class="col-sm-10">
			<h1>Pending Orders</h1>
		</div>
		<div class="col-sm-2 double-gap-top">
			<?php
				if(config_visible_function($dbc, 'sales_order') == 1) {
					echo '<a href="field_config_pos.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
					echo '<span class="popover-examples list-inline pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
				}
			?>
        </div>

		<div class="clearfix double-gap-bottom"></div>

		<?php
		$numodays = '';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='sales_order_archive_after_num_days'"));
					if($get_config['configid'] > 0) {
						$get_num_of_days = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM	general_configuration WHERE	name='sales_order_archive_after_num_days'"));
						$numodays = $get_num_of_days['value'];

					}

		?>

		<div class="tab-container mobile-100-container"><?php
			if(vuaed_visible_function($dbc, 'sales_order') == 1) { ?>
				<div class="pull-left tab">
					<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to create a <?= SALES_ORDER_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
					if ( check_subtab_persmission($dbc, 'sales_order', ROLE, 'create') === TRUE ) { ?>
						<a href='add_point_of_sell.php'><button type="button" class="btn brand-btn mobile-block mobile-100">Create an Order</button></a><?php
					} else { ?>
						<button type="button" class="btn disabled-btn mobile-block mobile-100">Create an Order</button><?php
					} ?>
				</div>
			<?php } ?>
			<div class="pull-left tab">
				<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to view your Pending Orders."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
				if ( check_subtab_persmission($dbc, 'sales_order', ROLE, 'pending') === TRUE ) { ?>
					<a href="pending.php"><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Pending Orders</button></a><?php
				} else { ?>
					<button type="button" class="btn disabled-btn mobile-block mobile-100 active_tab">Pending Orders</button><?php
				} ?>
			</div>
			<!--<a href='receiving.php'><button type="button" class="btn brand-btn mobile-block mobile-100">Receiving</button></a>
			<a href='unpaid_invoice.php'><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Accounts Payable</button></a>-->
			<div class="pull-left tab">
				<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to view Completed <?= SALES_ORDER_TILE ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
				if ( check_subtab_persmission($dbc, 'sales_order', ROLE, 'completed') === TRUE ) { ?>
					<a href="complete.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Completed <?= SALES_ORDER_TILE ?></button></a><?php
				} else { ?>
					<button type="button" class="btn disabled-btn mobile-block mobile-100">Completed <?= SALES_ORDER_TILE ?></button><?php
				} ?>
			</div>

			<div class="clearfix double-gap-bottom"></div>
		</div>

        <form name="invoice_table" method="post" action="pending.php" class="form-inline" role="form">
			<input type='hidden' class='getemailsapprove' value='' name='getemailsapprove'>
			<input type='hidden' class='zenearthcheckbox' value='' name='zenearthcheckbox'>
			<input type='hidden' class='pocheckbox' value='' name='pocheckbox'>
			<input type='hidden' class='poscheckbox' value='' name='poscheckbox'>
            <div class="single-pad-bottom">
                <div class="col-lg-2 col-md-2 col-sm-3 col-xs-3">
                <label for="search_site" style='width:100%; text-align:right;'>Search By Any:</label>
				</div>
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
                ?><div class="col-lg-6 col-md-6 col-sm-9 col-xs-9" style='margin-bottom:10px;'>
				<div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <input  type="text" name="search_invoice" value="<?php echo $search; ?>" class="form-control">
					</div>
				</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12" style='margin-bottom:10px;'>
                    <select name="contactid" data-placeholder="Choose Customer..." class="chosen-select-deselect form-control width-me" width="380">
                    <option value=''>Choose a Customer</option>
                        <?php
                        $result = mysqli_query($dbc, "SELECT contactid, name FROM contacts WHERE category='Customer' or category='Business' or category = 'Client' order by name");
                        while($row = mysqli_fetch_assoc($result)) {
                            if ($contactid == decryptIt($row['name'])) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value = '".decryptIt($row['name'])."'>".decryptIt($row['name'])."</option>";
                        }
                       ?>
                    </select>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
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
				<div class="clearfix" style='margin:10px;'>
				</div>
				<div class="col-lg-1 col-md-1 col-sm-1 col-xs-3">
                    <label for="site_name" class="col-sm-4 control-label" style='text-align:right;width:100%;'>From:</label>
				</div>
                    <div class="col-lg-2 col-md-2 col-sm-2 col-xs-9">
                        <input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></p>
                    </div>

                <!-- end time -->
                <div class="col-lg-1 col-md-1 col-sm-1 col-xs-3">
                    <label for="site_name" class="col-sm-4 control-label" style='text-align:right;width:100%;'>Until:</label>
                    </div>
					<div class="col-lg-2 col-md-2 col-sm-2 col-xs-9">
                        <input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></p>
                    </div>
					<div class="col-xs-3"></div>
				<div class="col-lg-6 col-md-6 col-sm-6 col-xs-9">
					<span class="popover-examples" style="margin-left:5px;"><a data-toggle="tooltip" data-placement="top" title="Click here after you have made your selections."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<button type="submit" name="search_invoice_submit" value="Search" class="btn brand-btn">Search</button>

					<span class="popover-examples gap-left"><a data-toggle="tooltip" data-placement="top" title="Click to refresh the page and see all Pending <?= SALES_ORDER_TILE ?> within this tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<button type="submit" name="display_all_invoice" value="Display All" class="btn brand-btn">Display All</button>
				</div>
				<?php
				$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT sales_order_dashboard FROM field_config"));
                $value_config = ','.$get_field_config['sales_order_dashboard'].',';
				if (strpos($value_config, ','."Send to Anyone".',') !== FALSE) { ?>
				<div class="clearfix" style='margin:10px;'>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-4" style='margin-bottom:10px;'>
				<label for="search_vendor" class="control-label" style='width:100%; text-align:right;'>Emails (Separated by a Comma):</label>
				</div>
				<div class="col-lg-3 col-md-3 col-sm-4 col-xs-8" style='margin-bottom:10px;'>
				<input id='roll-input' type='text'  name='email_list' placeholder='Enter emails here...' class='form-control email_driving_logs'>
				</div>
				<div class="gap-left col-lg-3 col-md-3 col-sm-2 col-xs-4" style='margin-bottom:10px;'>
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="After entering the email to send to, click here to send the PDF versions of the <?= SALES_ORDER_TILE ?> displayed on this page."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					<button onClick="return empty()" type='submit' name='send_drive_logs' class='btn brand-btn dl_send_butt'>Send PDF(s)</button>
				</div>
				<div class="col-lg-1 col-md-3 col-sm-4 col-xs-4">
				<div class='selectall selectbutton sel2' title='This will select all PDFs on the current page.'>Select All</div>
				</div>
				<div class="clearfix" style='margin:10px;'>
				</div>
				<?php } ?>
                <br><br>

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
                $query_check_credentialss = "SELECT inv.*, c.* FROM sales_order inv,  contacts c WHERE inv.contactid = c.contactid AND inv.deleted = 0 AND (status='Pending') ".$searchbyany." ".$searchbycustomer." ".$searchbytype." ".$starttimesql." ORDER BY inv.posid DESC";
            } else {
                $query_check_credentialss = "SELECT * FROM sales_order WHERE deleted = 0 AND (status='Pending') ORDER BY posid DESC LIMIT 25";
            }

            // how many rows we have in database
            $queryy = "SELECT COUNT(posid) AS numrows FROM sales_order";

            if($invoice_name == '') {
               // echo '<h1 class="single-pad-bottom">'.display_pagination($dbc, $queryy, $pageNumm, $rowsPerPagee).'</h1>';
            }

            $resultt = mysqli_query($dbc, $query_check_credentialss);

            $num_rowss = mysqli_num_rows($resultt);
            if($num_rowss > 0) {


                echo "<div id='no-more-tables'><table class='table table-bordered'>";
                echo "<tr class='hidden-xs hidden-sm'>";
					$software_url = $_SERVER['SERVER_NAME'];

                    if (strpos($value_config, ','."Invoice #".',') !== FALSE) {
                        echo '<th>S.O. #</th>';
                    }
					$software_url = $_SERVER['SERVER_NAME'];
					if($software_url == 'zenearthcorp.rookconnect.com' || $software_url == 'zendemo.rookconnect.com' ) {
						echo '<th>Author</th>';
					}
                    if (strpos($value_config, ','."Invoice Date".',') !== FALSE) {
                        echo '<th>S.O. Date</th>';
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
                        echo '<th>S.O. PDF</th>';
                    }
                    if (strpos($value_config, ','."Comment".',') !== FALSE) {
                        echo '<th>Comment</th>';
                    }
                    if (strpos($value_config, ','."Status".',') !== FALSE) {
                        echo '<th>Status</th>';
                    }
                     //   echo '<th>Approve & Send</th>';
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
						$query_update_employee = "UPDATE `sales_order` SET deleted = '1' WHERE posid='$posid'";
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
                    echo '<td data-title="S.O. #"">' . $roww['posid'] . '';
					$posid = $roww['posid'];
					echo '</td>';
                }
				if($software_url == 'zenearthcorp.rookconnect.com' || $software_url == 'zendemo.rookconnect.com' ) {
						if($roww['software_author'] == NULL || $roww['software_author'] == '') {
							$software_author = 'Zen Earth Corp';
						} else { $software_author = $roww['software_author']; }
						echo '<td data-title="Author">'.$software_author.'</td>';
				}
                if (strpos($value_config, ','."Invoice Date".',') !== FALSE) {

                    echo '<td data-title="S.O. Date">'.$roww['invoice_date'].'</td>';
                }
                if (strpos($value_config, ','."Customer".',') !== FALSE) {
                    echo '<td data-title="Customer">' . get_client($dbc, $contactid) . '</td>';
                }
                if (strpos($value_config, ','."Total Price".',') !== FALSE) {
                    echo '<td data-title="Total Price">' . $roww['total_price'] . '</td>';
                }
                if (strpos($value_config, ','."Payment Type".',') !== FALSE) {
					//Code was not working, so I had to manually pull from DB below ---v
					$get_pay_type = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM sales_order WHERE posid='".$roww['posid']."'"));
                    echo '<td data-title="Payment Type">' . $get_pay_type['payment_type'] . '</td>';
                }
                if (strpos($value_config, ','."Delivery/Shipping Type".',') !== FALSE) {
                    echo '<td data-title="Delivery/Shipping Type">' . $roww['delivery_type'] . '</td>';
                }
                if (strpos($value_config, ','."Invoice PDF".',') !== FALSE) {
                    echo '<td data-title="S.O. PDF">';
					if($roww['software_author'] == NULL || $roww['software_author'] == '' || ($software_url !== 'zenearthcorp.rookconnect.com' && $software_url !== 'zendemo.rookconnect.com')) {
					echo '<a target="_blank" href="download/invoice_'.$roww['posid'].'.pdf">PDF <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
					} else if($roww['software_author'] == 'Green Earth Energy Solutions') { echo '<a target="_blank" href="'.WEBSITE_URL.'/Sales Order/download/invoice_'.$roww['cross_software_posid'].'.pdf">PDF <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>'; } else if($roww['software_author'] == 'Green Life Can LLC') { echo '<a target="_blank" href="'.WEBSITE_URL.'/Sales Order/download/invoice_'.$roww['cross_software_posid'].'.pdf">PDF <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>'; }
					echo '</td>';
                }
                if (strpos($value_config, ','."Comment".',') !== FALSE) {
                    echo '<td data-title="Comment">' .  html_entity_decode($roww['comment']) . '</td>';
                }
                if (strpos($value_config, ','."Status".',') !== FALSE) {
                    echo '<td data-title="Status">'; ?>
                    <select name="status[]" onchange="changePOSStatus(this)" id="status_<?php echo $roww['posid']; ?>" class="chosen-select-deselect1 form-control" width="380">
                        <option value=""></option>
                        <option value="Pending" <?php if ($roww['status'] == "Pending") { echo " selected"; } ?> >Pending</option>
						<option value="Completed" <?php if ($roww['status'] == "Completed") { echo " selected"; } ?> >Approve</option>
                        <option value="Archived" <?php if ($roww['status'] == "Archived") { echo " selected"; } ?> >Archive</option>
                    </select>
					<?php echo '<span class="open-approval" onclick="approvebutton(this)" id="'.$roww['posid'].'" style="display:none;">Approve</span>';
						  ?>
							<div class="approve-box-<?php echo $roww['posid']; ?> approve-box"><div style='text-align:left;'>Please enter the email(s) (separated by a comma) you would like to send this Order to. Also, if you would like to convert this Order to a Purchase Order or a <?= POS_ADVANCE_TILE ?>, please select one of the checkboxes below.<br>(If you prefer not to send and/or convert the S.O., please hit skip.)</div><br><br>
							<input type='text' style='max-width:300px;' name='' placeholder='email1@example.com,email2@example.com' class='form-control getemailsapprove2'><br><br>
							<div style='width:170px; text-align:left; margin:auto;'>
							<span style='<?php if(($software_url == 'greenearthenergysolutions.rookconnect.com' || $software_url == 'greenlifecan.rookconnect.com' || $software_url == 'localhost') && $roww['cross_software'] == 'zen') { } else { echo 'display:none;'; } ?>'>
							<input type='checkbox' style='width:20px; height:20px;' id='zenearthcheckings'  name='zenearthconv' class='uncheck_on_cancel zenearthcheckings' value='1'> Send to Zen Earth</span><br>
							<input type='checkbox' style='width:20px; height:20px;' id='pocheckings'  name='purchaseorderconv' class='unchecker uncheck_on_cancel pocheckings' value='1'> Purchase Order<br>
							<input type='checkbox' style='width:20px; height:20px;' id='poscheckings'  name='pointofsaleconv' class='unchecker uncheck_on_cancel poscheckings' value='1'> <?= POS_ADVANCE_TILE ?><br><br>
							</div>
							<button type='submit' name='send_drive_logs_approve' class='btn brand-btn sendemailapprovesubmit' value='<?php echo $roww['posid']; ?>'>Approve and Send</button>
							<button type='submit' name='send_drive_log_noemail' class='btn brand-btn ' value='<?php echo $roww['posid']; ?>'>Skip</button>
							<button onClick="hide-box" value="<?php echo $roww['posid']; ?>" type='button' name='send_drive_logs' class='btn brand-btn send_cancel'>Cancel</button>
							</div>
                <?php
                    echo '</td>';
                    }
                   /* echo '<td data-title="Approve"><span class="open-approval" onclick="approvebutton(this)" id="'.$roww['posid'].'">Approve</span>';
						  ?>
							<div class="approve-box-<?php echo $roww['posid']; ?> approve-box"><div style='text-align:left;'>Please enter the email(s) (separated by a comma) you would like to send this Order to. Also, if you would like to convert this Order to a Purchase Order or a Point of Sale, please select one of the checkboxes below.<br>(If you prefer not to send and/or convert the S.O., please hit skip.)</div><br><br>
							<input type='text' style='max-width:300px;' name='' placeholder='email1@example.com,email2@example.com' class='form-control getemailsapprove2'><br><br>
							<div style='width:170px; text-align:left; margin:auto;'>
							<input type='checkbox' style='width:20px; height:20px;' id='pocheckings'  name='purchaseorderconv' class='unchecker' value='1'> Purchase Order<br>
							<input type='checkbox' style='width:20px; height:20px;' id='poscheckings'  name='pointofsaleconv' class='unchecker' value='1'> Point of Sale<br><br>
							</div>
							<button type='submit' name='send_drive_logs_approve' class='btn brand-btn sendemailapprovesubmit' value='<?php echo $roww['posid']; ?>'>Approve and Send</button>
							<button type='submit' name='send_drive_log_noemail' class='btn brand-btn ' value='<?php echo $roww['posid']; ?>'>Skip</button>
							<button onClick="hide-box" value="<?php echo $roww['posid']; ?>" type='button' name='send_drive_logs' class='btn brand-btn send_cancel'>Cancel</button>
							</div>
						  <?php
                    echo '</td>'; */

                if (strpos($value_config, ','."Send to Client".',') !== FALSE) {
                    if($roww['status'] == "Void") {
                        echo '<td data-title="Send to Client">'.$roww['status_history'].'</td>';
                    } else {
                        echo '<td data-title="Send to Client"><a href="send_point_of_sell.php?posid='.$roww['posid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Send</a></td>';
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
			<input type='hidden' value='<?php echo COMPANY_SOFTWARE_NAME; ?>' name='company_software_name'>
        </form>

	</div>
</div>
<?php include ('../footer.php'); ?>
