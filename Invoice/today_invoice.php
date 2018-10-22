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

$purchaser_config = explode(',',get_config($dbc, 'invoice_purchase_contact'));
$purchaser_label = count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0];

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

    echo '<script type="text/javascript"> alert("Invoice Successfully Sent to Patient."); window.location.replace("index.php?tab=today"); </script>';

	//header('Location: unpaid_invoice.php');
    // Send Email to Client
}
?>
<script type="text/javascript" src="../Invoice/invoice.js"></script>
<script type="text/javascript">
$(document).ready(function() {
	$(window).resize(function() {
        var available_height = window.innerHeight - $('footer:visible').height() - $('.tile-header').height() - $('.standard-body-title').height() - 5;
        if(available_height > 200) {
            $('#invoice_div .standard-body').height(available_height);
        }
    }).resize();

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
});

$(document).on('change', 'select[name="status[]"]', function() { changeStatus(this); });

function changeStatus(sel) {
	$.ajax({
		type: "GET",
		url: "../Invoice/invoice_ajax.php?action=update_status&invoice="+$(sel).data('invoiceid')+"&status="+sel.value,
		dataType: "html",
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

<div class="standard-body-title hide-titles-mob">
    <h3 class="pull-left">Today's Invoices</h3>
    <div class="pull-right"><img src="../img/icons/pie-chart.png" class="no-toggle cursor-hand offset-top-15 double-gap-right" title="View Summary" onclick="view_summary();" /></div>
    <div class="clearfix"></div>
</div>

<div class="standard-body-content padded-desktop">
    <!-- Summary Blocks --><?php
    $search_contact = 0;
    $search_invoiceid = '';
    $search_from = date('Y-m-d');
    $search_to = date('Y-m-d');
    if (isset($_GET['search_invoice_submit'])) {
        $search_contact = $_GET['contactid'] != '' ? $_GET['contactid'] : $search_contact;
        $search_invoiceid = isset($_POST['search_invoiceid']) ? preg_replace('/[^0-9]/', '', $_POST['search_invoiceid']) : '';
    }
    $search_clause = $search_contact > 0 ? " AND `patientid`='$search_contact'" : '';
    $search_clause .= $search_from != '' ? " AND `invoice_date` >= '$search_from'" : '';
    $search_clause .= $search_to != '' ? " AND `invoice_date` <= '$search_to'" : '';
    $search_invoice_clause = !empty($search_invoiceid) ? " AND `invoiceid`='$search_invoiceid'" : '';
    ?>

    <div class="view_summary double-gap-bottom" style="display:none;">
        <div class="col-xs-12 col-sm-4 gap-top">
            <div class="summary-block">
                <?php $total_invoices = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`final_price`) `final_price` FROM `invoice` WHERE `deleted`=0 $search_clause $search_invoice_clause")); ?>

                <div class="text-lg"><?= ( $total_invoices['final_price'] > 0 ) ? '<a href="../Reports/report_tiles.php?type=sales&report=POS%20Advanced%20Sales%20Summary&landing=true&pos_submit=yes&from='.$search_from.'&to='.$search_to.'">$'.number_format($total_invoices['final_price'], 2).'</a>' : '$'. 0; ?></div>

                <div>Total Invoices</div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-4 gap-top">
            <div class="summary-block"><?php
                $ar_types = array('On Account', 'Net 30', 'Net 30 Days', 'Net 60', 'Net 60 Days', 'Net 90', 'Net 90 Days', 'Net 120', 'Net 120 Days');
                $ar_amounts = 0;
                $nonar_amounts = 0;
                $ar_invoices = mysqli_query($dbc, "SELECT `payment_type` FROM `invoice` WHERE `deleted`=0 $search_clause $search_invoice_clause");
                while ( $row = mysqli_fetch_assoc($ar_invoices) ) {
                    list($payment_types, $payment_amounts) = explode('#*#', $row['payment_type']);
                    $types = explode(',', $payment_types);
                    $amounts = explode(',', $payment_amounts);
                    $count = count($types);
                    for ( $i=0; $i <= $count; $i++ ) {
                        if ( in_array($types[$i], $ar_types) ) {
                            $ar_amounts += $amounts[$i];
                        } else {
                            $nonar_amounts += $amounts[$i];
                        }
                    }
                } ?>

                <div class="text-lg"><?= ( $ar_amounts > 0 ) ? '<a href="../Reports/report_tiles.php?type=sales&report=POS%20Advanced%20Sales%20Summary&landing=true&pos_submit=yes&from='.$search_from.'&to='.$search_to.'">$'.number_format($ar_amounts, 2).'</a>' : '$'. 0; ?></div>

                <div>Total A/R Invoices</div>
            </div>
        </div>
        <div class="col-xs-12 col-sm-4 gap-top">
            <div class="summary-block">

                <div class="text-lg"><?= ( $nonar_amounts > 0 ) ? '<a href="../Reports/report_tiles.php?type=sales&report=POS%20Advanced%20Sales%20Summary&landing=true&pos_submit=yes&from='.$search_from.'&to='.$search_to.'">$'.number_format($nonar_amounts, 2).'</a>' : '$'. 0; ?></div>
                <div>Total Paid Invoices</div>

            </div>
        </div>
        <div class="clearfix"></div>
    </div><!-- .view_summary -->

    <form name="invoice" method="post" action="" class="form-horizontal" role="form">
        <?php $value_config = ','.get_config($dbc, 'invoice_dashboard').','; ?>

        <div class="search-group double-gap-top">
            <div class="row">
                <div class="col-sm-12">
                    <div class="row">
                        <div class="col-sm-2"><label class="control-label"><?= $purchaser_label ?>:</label></div>
                        <div class="col-sm-4">
                            <select name="contactid" data-placeholder="Select <?= $purchaser_label ?>..." class="chosen-select-deselect form-control width-me">
                                <option value=""></option><?php
                                $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE `contactid` IN (SELECT `patientid` FROM `invoice`) AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
                                foreach($query as $id) {
                                    $selected = '';
                                    $selected = $id == $search_contact ? 'selected = "selected"' : '';
                                    echo "<option ".$selected." value='".$id."'>".get_contact($dbc, $id).'</option>';
                                } ?>
                            </select>
                        </div>
                        <div class="col-sm-2"><label class="control-label">Invoice #:</label></div>
                        <div class="col-sm-4"><input name="search_invoiceid" placeholder="Invoice #" class="form-control" value="<?= $search_invoiceid ?>" /></div>
                    </div>
                </div>
                <div class="col-sm-12 text-right gap-top">
                    <button type="submit" name="search_invoice_submit" value="Search" class="btn brand-btn">Search</button>
                    <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
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

        if($search_contact > 0 || $search_delivery != '') {
            $limit = '';
        } else {
            $limit = ' LIMIT '.$offset.', '.$rowsPerPage;
        }

        $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND `status` != 'Void' $search_clause $search_invoice_clause AND `invoice_date`='".date('Y-m-d')."' ORDER BY invoiceid DESC $limit";
        $query = "SELECT count(*) as numrows FROM invoice WHERE deleted = 0 AND `status` != 'Void' $search_clause $search_invoice_clause AND `invoice_date`='".date('Y-m-d')."'";

        $result = mysqli_query($dbc, $query_check_credentials);

        if(mysqli_num_rows($result) > 0) {

            // Added Pagination
            if($limit != '') {
                echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            }

            echo "<br /><div id='no-more-tables'><table class='table table-bordered table-striped'>";
                echo "<thead>";
                    echo "<tr class='hidden-xs hidden-sm'>";
                        if (strpos($value_config, ','."invoiceid".',') !== FALSE) { echo '<th>Invoice #</th>'; }
                        if (strpos($value_config, ','."invoice_date".',') !== FALSE) { echo '<th>Invoice Date</th>'; }
                        if (strpos($value_config, ','."customer".',') !== FALSE) { echo '<th>'.$purchaser_label.'</th>'; }
                        if (strpos($value_config, ','."total_price".',') !== FALSE) { echo '<th>Total Price</th>'; }
                        if (strpos($value_config, ','."payment_type".',') !== FALSE) { echo '<th>Payment Type</th>'; }
                        if (strpos($value_config, ','."delivery".',') !== FALSE) { echo '<th>Delivery/Shipping Type</th>'; }
                        if (strpos($value_config, ','."Customer Billing Status".',') !== FALSE) { echo '<th>Customer Billing Status</th>'; }
                        if (strpos($value_config, ','."invoice_pdf".',') !== FALSE) { echo '<th>Invoice PDF</th>'; }
                        if (strpos($value_config, ','."comment".',') !== FALSE) { echo '<th>Comment</th>'; }
                        if (strpos($value_config, ','."status".',') !== FALSE) { echo '<th>Status</th>'; }
                        if (strpos($value_config, ','."send") !== FALSE) {
                          ?><th>Email Invoice</th><?php
                        }
                    echo "</tr>";
                echo "</thead>";

            $src_row = false;
            $src_ids = [];
            while($src_row || $invoice = mysqli_fetch_array( $result ))
            {
                if(!$src_row && in_array($invoice['invoiceid'],$src_ids)) {
                    continue;
                }
                $src_row = false;
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
                    echo '<td data-title="Invoice #">' .($invoice['invoice_type'] == 'New' ? '#' : '<a href="create_invoice.php?invoiceid='.$invoice['invoiceid'].'&contactid='.$contactid.'">Edit '.$invoice['invoice_type'].'</a> #'). $invoice['invoiceid'].($invoice['invoiceid_src'] > 0 ? '<br />For Invoice #'.$invoice['invoiceid_src'] : '') . '</td>';
                }
                if (strpos($value_config, ','."invoice_date".',') !== FALSE) {
                    echo '<td data-title="Date" style="white-space: nowrap; ">'.$invoice['invoice_date'].'</td>';
                }
                if (strpos($value_config, ','."customer".',') !== FALSE) {
                    echo '<td data-title="'.$purchaser_label.'"><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Contacts/contacts_inbox.php?edit='.$invoice['patientid'].'\', \'auto\', false, true, $(\'#invoice_div\').outerHeight()+20); return false;">' . get_contact($dbc, $contactid, 'name_company') . '</a></td>';
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
                if (strpos($value_config, ','."Customer Billing Status".',') !== FALSE) {
                    echo '<td data-title="Customer Billing Status">' . $invoice['customer_billing_status'] . '</td>';
                }
                if (strpos($value_config, ','."invoice_pdf".',') !== FALSE) {
                    echo '<td data-title="Invoice PDF">';
                    if(file_exists($invoice_pdf)) {
                        echo '<a target="_blank" href="'.$invoice_pdf.'">Invoice #'.$invoice['invoiceid'].' <img src="'.WEBSITE_URL.'/img/icons/pdf.png" title="Invoice PDF" class="no-toggle inline-img" /></a><br />';
                    }
                    if($invoice['invoiceid_src'] > 0 && file_exists('download/invoice_'.$invoice['invoiceid_src'].'.pdf')) {
                        echo '<a target="_blank" href="download/invoice_'.$invoice['invoiceid_src'].'.pdf'.'">Primary Invoice #'.$invoice['invoiceid_src'].' <img src="'.WEBSITE_URL.'/img/icons/pdf.png" title="Primary Invoice PDF" class="no-toggle inline-img" /></a><br />';
                    } else if($invoice['invoiceid_src'] > 0 && file_exists('Download/invoice_'.$invoice['invoiceid_src'].'.pdf')) {
                        echo '<a target="_blank" href="Download/invoice_'.$invoice['invoiceid_src'].'.pdf'.'">Primary Invoice #'.$invoice['invoiceid_src'].' <img src="'.WEBSITE_URL.'/img/icons/pdf.png" title="Primary Invoice PDF" class="no-toggle inline-img" /></a><br />';
                    }
                    echo '</td>';
                }
                if (strpos($value_config, ','."comment".',') !== FALSE) {
                    echo '<td data-title="Comment">' .  html_entity_decode($invoice['comment']) . '</td>';
                }
                if (strpos($value_config, ','."status".',') !== FALSE) {
                    echo '<td data-title="Status">';
                        switch($invoice['status']) {
                            case 'Completed':
                                echo 'Paid';
                                break;
                            case 'Void':
                                echo 'Voided';
                                break;
                            case 'Saved':
                                echo 'Saved';
                                break;
                            case 'Posted':
                            default:
                                echo 'Accounts Receivable';
                                break;
                        }
                        echo ' <a class="cursor-hand" onclick="void_invoice('.$invoice['invoiceid'].');"><img src="../img/icons/void.png" class="no-toggle inline-img" title="Void Invoice" /></a>';
                        echo ' <a class="cursor-hand" href="create_invoice.php?invoiceid='.$invoice['invoiceid'].'&inv_mode=adjust"><img src="../img/icons/refund.png" class="no-toggle inline-img" title="Refund / Adjustment" /></a>';
                    echo '</td>';
                    }
                    if (strpos($value_config, ','."send") !== FALSE) {
                        echo '<td data-title="Email Invoice">';
                        if(file_exists($invoice_pdf)) {
                            ?><img src="../img/icons/ROOK-email-icon.png" class="no-toggle cursor-hand inline-img" title="Email Invoice" onclick="email_doc('<?= $invoice_pdf ?>', '<?= FOLDER_NAME ?>');" /><?php
                        }
                        //echo '<a href=\'driving_log_14days.php?email=send&drivinglogid='.$row['drivinglogid'].'\'>Email</a>';
                        echo '</td>';
                    }
                echo "</tr>";
                if($invoice['invoiceid_src'] > 0) {
                    $invoice = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='".$invoice['invoiceid_src']."'"));
                    $src_row = true;
                    $src_ids[] = $invoice['invoiceid'];
                }

            }

            echo '</table></div>';

            // Added Pagination //
            if($limit != '')
                echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            // Pagination Finish //
        } else {
            echo "<h4 class='double-gap-top double-gap-left'>No Record Found.</h4>";
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
                <label class="col-sm-4 control-label" for="customer">Send to <?= $purchaser_label ?></label>
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
</div><!-- .standard-body-content -->
