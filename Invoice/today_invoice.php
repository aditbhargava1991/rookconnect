<?php
/*
Payment/Invoice Listing
*/
include ('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

/*
if (isset($_POST['submit_pay'])) {
	$all_invoice = implode(',',$_POST['invoice']);
	header('Location: add_invoice.php?action=pay&from=patient&invoiceid='.$all_invoice);
}


if (isset($_POST['printpdf'])) {
    include_once ('print_unpaid_invoice.php');
}
*/
if (isset($_POST['send_email'])) {
	$email_list = $_POST['recipient'];
	$subject = $_POST['subject'];
	$body = $_POST['body'];
	$sender = [$_POST['sender'] => $_POST['sender_name']];
	$customers = $_POST['customer'];
	$invoices = $_POST['pdf_send'];

	foreach($invoices as $invoice) {
		$invoice = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='$invoice'"));
		$to = $email_list;
		if($customers == 'customer') {
			$to .= ','.get_email($dbc, $invoice['patientid']);
		}
		$to = filter_var_array(explode(',', $to), FILTER_VALIDATE_EMAIL);
		if(count($to) > 0) {
			$file = 'download/invoice_'.$invoice['invoiceid'].'.pdf';
			if(file_exists($file)) {
				try {
					send_email($sender, $to, '', '', $subject, $body, $file);
				} catch(Exception $e) {
					echo "<script> alert('Unable to send email for Invoice #".$invoice['posid'].". Please check your email addresses or try again later.'); </script>";
				}
			} else {
				echo "<script> alert('Unable to find invoice. Please recreate the invoice.'); </script>";
			}
		}
	}
	echo "<script> window.location.replace(''); </script>";
}
if((!empty($_GET['action'])) && ($_GET['action'] == 'delete')) {
	$invoiceid = $_GET['invoiceid'];

    $sql = mysqli_query($dbc, "DELETE FROM invoice WHERE invoiceid='$invoiceid'");
    $sql = mysqli_query($dbc, "DELETE FROM invoice_patient WHERE invoiceid='$invoiceid'");
    $sql = mysqli_query($dbc, "DELETE FROM invoice_insurer WHERE invoiceid='$invoiceid'");
}

if((!empty($_GET['action'])) && ($_GET['action'] == 'email')) {

	$invoiceid = $_GET['invoiceid'];
	$patientid = $_GET['patientid'];

	$name_of_file = 'invoice_'.$invoiceid.'.pdf';

    $to = get_email($dbc, $patientid);
    $subject = 'Physiotherapy Invoice';
	$body = 'Please find attached your invoice from Physiotherapy';
    $attachment = 'download/'.$name_of_file;

    send_email('', $to, '', '', $subject, $body, $attachment);

    echo '<script type="text/javascript"> alert("Invoice Successfully Sent to Patient."); window.location.replace("today_invoice.php"); </script>';

	//header('Location: unpaid_invoice.php');
    // Send Email to Client
}
?>
<script type="text/javascript" src="invoice.js"></script>
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

			show_hide_email();
		}
	);

    $('.all_view').click(function(event) {  //on click
		var arr = $('.patientid_for_invoice').val().split('_');
        if(this.checked) { // check select status
            $('.privileges_view_'+arr[1]).each(function() { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "checkbox1"
            });
        }else{
            $('.privileges_view_'+arr[1]).each(function() { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "checkbox1"
            });
        }
    });

	/* $('.iframe_open').click(function(){
			var id = $(this).attr('id');
			var arr = id.split('_');
		    $('#iframe_instead_of_window').attr('src', '<?php echo WEBSITE_URL; ?>/Contacts/add_contacts.php?category=Patient&contactid='+arr[0]);
		    $('.iframe_title').text('View Patient');
			$('.hide_on_iframe').hide(1000);
			$('.iframe_holder').show(1000);
	});
	$('.close_iframer').click(function(){
				$('.iframe_holder').hide(1000);
				$('.hide_on_iframe').show(1000);
				location.reload();
	}); */

});
$(document).on('change', 'select[name="status[]"]', function() { changeStatus(this); });

function changeStatus(sel) {
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../Invoice/invoice_ajax.php?action=update_status&invoice="+$(sel).data('invoiceid')+"&status="+sel.value,
		dataType: "html",   //expect html to be returned
		success: function(response){
			window.location.reload();
		}
	});
}

function show_hide_email() {
	var status = $('[name="pdf_send[]"]:checked').length;
	if(status > 0) {
		$('[name=send_email_div]').show();
	} else {
		$('[name=send_email_div]').hide();
	}
}
</script>
</head>
<body>
<?php include_once ('../navigation.php');
$ux_options = explode(',',get_config($dbc, FOLDER_NAME.'_ux'));
?>
<div id="invoice_div" class="container triple-pad-bottom">
    <div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="edit_board" src=""></iframe>
		</div>
	</div>
    <!--
    <div class='iframe_holder' style='display:none;'>
		<img src='<?php //echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
		<span class='iframe_title' style='color:white; font-weight:bold; position: relative; left: 20px; font-size: 30px;'></span>
		<iframe id="iframe_instead_of_window" style='width: 100%;' height="1000px; border:0;" src=""></iframe>
    </div>
    -->
	<div class="row hide_on_iframe">
        <h1 class="pull-left"><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?>: Invoices</h1>
        <?php
            echo '<a href="field_config_invoice.php" class="btn mobile-block pull-right gap-top"><img style="width:30px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
        ?>
        <div class="clearfix"></div>
		<?php $tab_list = explode(',', get_config($dbc, 'invoice_tabs'));
		?><div class='gap-top mobile-100-container'><?php
		foreach($tab_list as $tab_name) {
			if(check_subtab_persmission($dbc, FOLDER_NAME == 'invoice' ? 'check_out' : 'posadvanced', ROLE, $tab_name) === TRUE) {
				switch($tab_name) {
					case 'sell':
						if(in_array('touch',$ux_options)) { ?>
							<a href='add_invoice.php' class="btn brand-btn mobile-block mobile-100">Create Invoice (Keyboard)</a>
							<a href='touch_main.php' class="btn brand-btn mobile-block mobile-100">Create Invoice (Touchscreen)</a>
						<?php } else { ?>
							<a href='add_invoice.php' class="btn brand-btn mobile-block mobile-100">Create Invoice</a>
						<?php }
						break;
					case 'today': ?>
						<span class="popover-examples list-inline">
							<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Invoices created today."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span>
						<a href='today_invoice.php' class="btn brand-btn mobile-block mobile-100 active_tab">Today's Invoices</a>
						<?php break;
					case 'all': ?>
						<span class="popover-examples list-inline">
							<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Complete history of all Invoices."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span>
						<a href='all_invoice.php' class="btn brand-btn mobile-block mobile-100">All Invoices</a>
						<?php break;
					case 'invoices': ?>
						<a href='invoice_list.php' class="btn brand-btn mobile-block mobile-100">Invoices</a>
						<?php break;
					case 'unpaid': ?>
						<a href='unpaid_invoice_list.php' class="btn brand-btn mobile-block mobile-100">Accounts Receivable</a>
						<?php break;
					case 'voided': ?>
						<a href='void_invoices.php' class="btn brand-btn mobile-block mobile-100">Voided Invoices</a>
						<?php break;
					case 'refunds': ?>
						<span class="popover-examples list-inline">
							<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Find invoices in order to issue Refunds or Create Adjustment Invoices."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span>
						<a href='refund_invoices.php' class="btn brand-btn mobile-block mobile-100">Refund / Adjustments</a>
						<?php break;
					case 'ui_report': ?>
						<span class="popover-examples list-inline">
							<a href="#job_file" data-toggle="tooltip" data-placement="top" title="In this section you can create Invoices for insurers."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span>
						<a href='unpaid_insurer_invoice.php' class="btn brand-btn mobile-block mobile-100">Unpaid Insurer Invoice Report</a>
						<?php break;
					case 'cashout': ?>
						<span class="popover-examples list-inline">
							<a href="#job_file" data-toggle="tooltip" data-placement="top" title="Daily front desk Cashout."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
						</span>
						<a href='cashout.php' class="btn brand-btn mobile-block mobile-100">Cash Out</a>
						<?php break;
                    case 'gf': ?>
                        <a href='giftcards.php' class="btn brand-btn mobile-block mobile-100">Gift Card</a>
                        <?php break;
				}
			}
		}
		?></div>
		
        <form name="invoice" method="post" action="" class="form-horizontal" role="form">
			<?php $value_config = ','.get_config($dbc, 'invoice_dashboard').','; ?>
			<?php $search_contact = 0;
			$search_delivery = '';
			$search_from = date('Y-m-01');
			$search_to = date('Y-m-t');
			if (isset($_POST['search_invoice_submit'])) {
				if($_POST['contactid'] != '') {
				   $search_contact = $_POST['contactid'];
				}
			} ?>
			<div class="search-group double-gap-top">
				<div class="row">
                    <div class="col-sm-7">
                        <div class="row">
                            <div class="col-sm-5 text-right"><label class="control-label">Search By Customer:</label></div>
                            <div class="col-sm-7">
                                <select name="contactid" data-placeholder="Select Customer..." class="chosen-select-deselect form-control width-me">
                                    <option value=""></option><?php
                                    $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE `contactid` IN (SELECT `patientid` FROM `invoice`) AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
                                    foreach($query as $id) {
                                        $selected = '';
                                        $selected = $id == $search_contact ? 'selected = "selected"' : '';
                                        echo "<option ".$selected." value='".$id."'>".get_contact($dbc, $id).'</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <button type="submit" name="search_invoice_submit" value="Search" class="btn brand-btn">Search</button>
                        <a href="" type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</a>
                    </div>
                </div>
            </div>
        </form>
		<div class="clearfix"></div>
		<form method="POST" action="" name="send_email" class="form-horizontal">
            <?php
            // Display Pager

            $rowsPerPagee = ITEMS_PER_PAGE;
            $pageNumm  = 1;

            if(isset($_GET['pagee'])) {
                $pageNumm = $_GET['pagee'];
            }

            $offsett = ($pageNumm - 1) * $rowsPerPagee;

            /* Pagination Counting */
            $rowsPerPage = 25;
            $pageNum = 1;

            if(isset($_GET['page'])) {
                $pageNum = $_GET['page'];
            }

            $offset = ($pageNum - 1) * $rowsPerPage;

			$search_clause = '';
			if($search_contact > 0) {
				$search_clause .= " AND `patientid`='$search_contact'";
			}
			
			if($search_contact > 0 || $search_delivery != '') {
				$limit = '';
			} else {
				$limit = ' LIMIT '.$offset.', '.$rowsPerPage;
			}
			
			$query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND `status` != 'Void' $search_clause AND `invoice_date`='".date('Y-m-d')."' ORDER BY invoiceid DESC $limit";
			$query = "SELECT count(*) as numrows FROM invoice WHERE deleted = 0 AND `status` != 'Void' $search_clause AND `invoice_date`='".date('Y-m-d')."'";

            $result = mysqli_query($dbc, $query_check_credentials);

            if(mysqli_num_rows($result) > 0) {

                // Added Pagination //
                if($limit != '')
                    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                // Pagination Finish //

                echo "<br /><div id='no-more-tables'><table class='table table-bordered'>";
                echo "<tr class='hidden-xs hidden-sm'>";
                    if (strpos($value_config, ','."invoiceid".',') !== FALSE) {
                        echo '<th>Invoice #</th>';
                    }
                    if (strpos($value_config, ','."invoice_date".',') !== FALSE) {
                        echo '<th>Invoice Date</th>';
                    }
                    if (strpos($value_config, ','."customer".',') !== FALSE) {
                        echo '<th>Customer</th>';
                    }
                    if (strpos($value_config, ','."total_price".',') !== FALSE) {
                        echo '<th>Total Price</th>';
                    }
                    if (strpos($value_config, ','."payment_type".',') !== FALSE) {
                        echo '<th>Payment Type</th>';
                    }
                    if (strpos($value_config, ','."delivery".',') !== FALSE) {
                        echo '<th>Delivery/Shipping Type</th>';
                    }
                    if (strpos($value_config, ','."invoice_pdf".',') !== FALSE) {
                        echo '<th>Invoice PDF</th>';
                    }
                    if (strpos($value_config, ','."comment".',') !== FALSE) {
                        echo '<th>Comment</th>';
                    }
                    if (strpos($value_config, ','."status".',') !== FALSE) {
                        echo '<th>Status</th>';
                    }
					if (strpos($value_config, ','."send") !== FALSE) {
                      ?><th>Email PDF<br><div class='selectall btn brand-btn' title='This will select all PDFs on the current page.'>Select All</div></th><?php
                    }
                echo "</tr>";
				
				while($invoice = mysqli_fetch_array( $result ))
				{
					$invoice_pdf = 'download/invoice_'.$invoice['invoiceid'].'.pdf';
					$style = '';
					if($invoice['status'] == 'Posted Past Due') {
						$style = 'color:green;';
					}
					if($invoice['status'] == 'Void') {
						$style = 'color:red;';
					}
					$contactid = $invoice['patientid'];
					echo "<tr>";

					if (strpos($value_config, ','."invoiceid".',') !== FALSE) {
						echo '<td data-title="Invoice #">' .($invoice['invoice_type'] == 'New' ? '#' : '<a href="add_invoice.php?invoiceid='.$invoice['invoiceid'].'&contactid='.$contactid.'">Edit '.$invoice['invoice_type'].'</a> #'). $invoice['invoiceid'] . '</td>';
					}
					if (strpos($value_config, ','."invoice_date".',') !== FALSE) {
						echo '<td data-title="Date" style="white-space: nowrap; ">'.$invoice['invoice_date'].'</td>';
					}
					if (strpos($value_config, ','."customer".',') !== FALSE) {
						echo '<td data-title="Customer"><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/'.CONTACTS_TILE.'/contacts_inbox.php?edit='.$invoice['patientid'].'\', \'auto\', false, true, $(\'#invoice_div\').outerHeight()+20); return false;">' . get_contact($dbc, $contactid, 'name_company') . '</a></td>';
					}
					if (strpos($value_config, ','."total_price".',') !== FALSE) {
						echo '<td data-title="Total" align="right">$' . number_format($invoice['final_price'],2) . '</td>';
					}
					if (strpos($value_config, ','."payment_type".',') !== FALSE) {
						echo '<td data-title="Payment">' . explode('#*#',$invoice['payment_type'])[0] . '</td>';
					}
					if (strpos($value_config, ','."delivery".',') !== FALSE) {
						echo '<td data-title="Delivery">' . $invoice['delivery_type'] . '</td>';
					}
					if (strpos($value_config, ','."invoice_pdf".',') !== FALSE) {
						echo '<td data-title="Invoice PDF">';
						if(file_exists($invoice_pdf)) {
							echo '<a target="_blank" href="'.$invoice_pdf.'">Invoice #'.$invoice['invoiceid'].' <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a>';
						}
						echo '</td>';
					}
					if (strpos($value_config, ','."comment".',') !== FALSE) {
						echo '<td data-title="Comment">' .  html_entity_decode($invoice['comment']) . '</td>';
					}
					if (strpos($value_config, ','."status".',') !== FALSE) {
						echo '<td data-title="Status">';
						?>
						<select name="status[]" data-invoiceid="<?= $invoice['invoiceid'] ?>" class="chosen-select-deselect form-control">
							<option value=""></option>
							<option value="Posted" <?php if ($invoice['status'] == "Posted") { echo " selected"; } ?> >Posted</option>
							<option value="Posted Past Due" <?php if ($invoice['status'] == "Posted Past Due") { echo " selected"; } ?> >Posted Past Due</option>
							<option value="Completed" <?php if ($invoice['status'] == "Completed") { echo " selected"; } ?> >Completed</option>
							<option value="Void" <?php if ($invoice['status'] == "Void") { echo " selected"; } ?> >Void</option>
							<option value="Archived" <?php if ($invoice['status'] == "Archived") { echo " selected"; } ?> >Archive</option>
						</select>
					<?php
						echo '</td>';
						}
						if (strpos($value_config, ','."send") !== FALSE) {
							echo '<td data-title="Email PDF">';
							if(file_exists($invoice_pdf)) {
								?><input style="height: 25px; width: 25px;" type='checkbox' name='pdf_send[]' class='pdf_send' value='<?php echo $invoice['invoiceid']; ?>' onchange="show_hide_email();"><?php
							}
							//echo '<a href=\'driving_log_14days.php?email=send&drivinglogid='.$row['drivinglogid'].'\'>Email</a>';
							echo '</td>';
						}
					echo "</tr>";

				}

				echo '</table></div></div>';

				// Added Pagination //
                if($limit != '')
                    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
				// Pagination Finish //
            } else {
                echo "<h2>No Record Found.</h2>";
            } ?>
			
			<div name="send_email_div" class="form-horizontal" style="display:none;">
				<div class="form-group">
					<label class="col-sm-4 control-label">Sending Email Name</label>
					<div class="col-sm-8"><input type="text" class="form-control" name="sender_name" value="<?php echo get_contact($dbc, $_SESSION['contactid']); ?>"></div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Sending Email Address</label>
					<div class="col-sm-8"><input type="text" class="form-control" name="sender" value="<?php echo get_email($dbc, $_SESSION['contactid']); ?>"></div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label" for="customer">Send to Customer</label>
					<div class="col-sm-8"><input type="checkbox" checked class="" id="customer" name="customer" value="customer" style="height:1.5em;width:1.5em;"></div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Additional Recipient Email Addresses<br /><em>(separate multiple emails using a comma and no spaces)</em></label>
					<div class="col-sm-8"><input type="text" class="form-control" name="recipient" value=""></div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Email Subject</label>
					<div class="col-sm-8"><input type="text" class="form-control" name="subject" value="See the attached Invoice"></div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Email Body</label>
					<div class="col-sm-8"><textarea name="body">Please see the attached PDF(s) below.</textarea></div>
				</div>
				<button class="btn brand-btn pull-right" type="submit" name="send_email" value="send">Send Email</button>
			</div>

        </form>

        <!-- <a href="<?php //echo WEBSITE_URL;?>/home.php" class="btn brand-btn">Back</a> -->

	</div>

</div>
</div>
<?php include ('../footer.php'); ?>
