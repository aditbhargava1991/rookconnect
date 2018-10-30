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

/*
if (isset($_POST['submit_pay'])) {
	$all_invoice = implode(',',$_POST['invoice']);
	header('Location: create_invoice.php?action=pay&from=patient&invoiceid='.$all_invoice);
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
			$file = '../Invoice/Download/invoice_'.$invoice['invoiceid'].'.pdf';
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
    $attachment = 'Download/'.$name_of_file;

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
function view_summary()
{
    $('.view_summary').toggle();
}
function view_voided()
{
    $('.view_voided').toggleClass('hidden');
}
</script>

<div class="standard-body-title hide-titles-mob">
    <h3 class="pull-left">Voided / Credit Memo</h3>
    <div class="pull-right"><img src="../img/icons/pie-chart.png" class="no-toggle cursor-hand offset-top-15 double-gap-right" title="View Summary" onclick="view_summary();" />
        <img src="../img/icons/ROOK-3dot-icon.png" class="no-toggle cursor-hand offset-top-15 double-gap-right" title="" width="25" data-original-title="Show/Hide Voided / Credit Memo" onclick="view_voided()"></div>
    <div class="clearfix"></div>
</div>

<div class="standard-body-content padded-desktop ">
    <!-- Summary Blocks -->
    <div class="view_summary double-gap-bottom" style="display:none;">
        <div class="col-xs-12 col-sm-4 gap-top">
            <div class="summary-block"><?php
                $search_contact = 0;
                $search_invoiceid = '';
                $search_from = date('Y-m-01');
                $search_to = date('Y-m-t');
                if (isset($_POST['search_invoice_submit'])) {
                    if($_POST['contactid'] != '') {
                       $search_contact = $_POST['contactid'];
                    }
                    if($_POST['type'] != '') {
                       $search_delivery = $_POST['type'];
                    }
                    if($_POST['search_from'] != '') {
                       $search_from = $_POST['search_from'];
                    }
                    if($_POST['search_to'] != '') {
                       $search_to = $_POST['search_to'];
                    }
                    $search_invoiceid = isset($_POST['search_invoiceid']) ? preg_replace('/[^0-9]/', '', $_POST['search_invoiceid']) : '';
                }

                $search_clause = $search_contact > 0 ? " AND `patientid`='$search_contact'" : '';
                $search_clause .= $search_from != '' ? " AND `invoice_date` >= '$search_from'" : '';
                $search_clause .= $search_to != '' ? " AND `invoice_date` <= '$search_to'" : '';
                $search_invoice_clause = !empty($search_invoiceid) ? " AND `invoiceid`='$search_invoiceid'" : '';

                $total_invoices = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`final_price`) `final_price` FROM `invoice` WHERE `deleted`=0 $search_clause $search_invoice_clause AND `status`='Void'")); ?>
                <div class="text-lg"><?= ( $total_invoices['final_price'] > 0 ) ? '$'.number_format($total_invoices['final_price'], 2) : '$'. 0; ?></div>
                <div>Total Invoices</div>
            </div>
        </div>
        <div class="clearfix"></div>
    </div><!-- .view_summary -->
    <div class="">

    <form name="invoice" method="post" action="" class="form-horizontal view_voided hidden" role="form">
        <?php $value_config = ','.get_config($dbc, 'invoice_dashboard').','; ?>

        <div class="form-group search-group double-gap-top">
            <div class="col-xs-12">
                <div class="col-sm-6 col-xs-12">
                    <div class="col-sm-4"><label class="control-label"><?= $purchaser_label ?>:</label></div>
                    <div class="col-sm-8">
                        <select name="contactid" data-placeholder="Select <?= $purchaser_label ?>..." class="chosen-select-deselect form-control width-me">
                            <option value=''></option>
                            <?php
                            $result = mysqli_query($dbc, "SELECT contactid, first_name, last_name FROM contacts WHERE `contactid` IN (SELECT `patientid` FROM `invoice`) AND `deleted`=0 AND `status`>0");
                            while($row = mysqli_fetch_assoc($result)) {
                                if ($search_contact == $row['contactid']) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".$selected." value = '".$row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name'])."</option>";
                            }
                           ?>
                        </select>
                    </div>
                </div>
                <?php if(strpos($value_config,',invoiceid,') !== FALSE) { ?>
                    <div class="col-sm-6 col-xs-12">
                        <div class="col-sm-4"><label class="control-label">Invoice #:</label></div>
                        <div class="col-sm-8"><input name="search_invoiceid" placeholder="Invoice #" class="form-control" value="<?= $search_invoiceid ?>" /></div>
                    </div>
                <?php } ?>
            </div>
            <div class="col-xs-12">
                <div class="col-sm-6 col-xs-12">
                    <div class="col-sm-4"><label class="control-label">From:</label></div>
                    <div class="col-sm-8"><input name="search_from" type="text" class="datepicker form-control" value="<?= $search_from ?>" /></div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="col-sm-4"><label class="control-label">To:</label></div>
                    <div class="col-sm-8"><input name="search_to" type="text" class="datepicker form-control" value="<?= $search_to ?>" /></div>
                </div>
            </div>
            <div class="col-xs-12 text-right gap-top">
                <button type="submit" name="search_invoice_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
                <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
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
        $search_invoice_clause = '';
        if ( !empty($search_invoiceid) ) {
            $search_invoice_clause = " AND `invoiceid`='$search_invoiceid'";
        }
        if($search_from != '') {
            $search_clause .= " AND `invoice_date` >= '$search_from'";
        }
        if($search_to != '') {
            $search_clause .= " AND `invoice_date` <= '$search_to'";
        }

        if($search_contact > 0 || $search_delivery != '') {
            $limit = '';
        } else {
            $limit = ' LIMIT '.$offset.', '.$rowsPerPage;
        }

        $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND `status` = 'Void' $search_clause $search_invoice_clause ORDER BY invoiceid DESC $limit";
        $query = "SELECT count(*) as numrows FROM invoice WHERE deleted = 0 AND `status` = 'Void' $search_clause $search_invoice_clause";

        $result = mysqli_query($dbc, $query_check_credentials);

        if(mysqli_num_rows($result) > 0) {

            // Added Pagination //
            if($limit != '')
                echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            // Pagination Finish //

            echo "<br /><div id='no-more-tables'><table class='table table-bordered table-striped'>";
                echo "<thead>";
                    echo "<tr class='hidden-xs hidden-sm'>";
                        if (strpos($value_config, ','."invoiceid".',') !== FALSE) {
                            echo '<th>Invoice #</th>';
                        }
                        if (strpos($value_config, ','."invoice_date".',') !== FALSE) {
                            echo '<th>Invoice Date</th>';
                        }
                        if (strpos($value_config, ','."customer".',') !== FALSE) {
                            echo '<th>'.$purchaser_label.'</th>';
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
                echo "</thead>";

            $src_row = false;
            $src_ids = [];
            while($src_row || $invoice = mysqli_fetch_array( $result ))
            {
                if(!$src_row && in_array($invoice['invoiceid'],$src_ids)) {
                    continue;
                }
                $src_row = false;
                $invoice_pdf = '../'.FOLDER_NAME.'/Download/invoice_'.$invoice['invoiceid'].'.pdf';
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
                    echo '<td data-title="Invoice #">' .($invoice['invoice_type'] == 'New' ? '#' : $invoice['invoice_type'].' #') .'<a href="'.$invoice_pdf.'" target="_blank">'. $invoice['invoiceid'] .'</a>'.($invoice['invoiceid_src'] > 0 ? '<br />For Invoice #'.$invoice['invoiceid_src'] : '') . '</td>';
                }


                if (strpos($value_config, ','."invoice_date".',') !== FALSE) {

                    echo '<td data-title="Invoice Date" style="white-space: nowrap; ">'.$invoice['invoice_date'].'</td>';
                }
                if (strpos($value_config, ','."customer".',') !== FALSE) {
                    echo '<td data-title="'.$purchaser_label.'"><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Contacts/contacts_inbox.php?edit='.$contactid.'\', \'auto\', false, true, $(\'#invoice_div\').outerHeight()+20); return false;">' . get_contact($dbc, $contactid) . '</a></td>';
                }
                if (strpos($value_config, ','."total_price".',') !== FALSE) {
                    echo '<td data-title="Total Price" align="right">$' . number_format($invoice['final_price'],2) . '</td>';
                }
                if (strpos($value_config, ','."payment_type".',') !== FALSE) {
                    echo '<td data-title="Payment Type">' . explode('#*#',$invoice['payment_type'])[0] . '</td>';
                }
                if (strpos($value_config, ','."delivery".',') !== FALSE) {
                    echo '<td data-title="Delivery">' . $invoice['delivery_type'] . '</td>';
                }
                if (strpos($value_config, ','."invoice_pdf".',') !== FALSE) {
                    echo '<td data-title="Invoice PDF">';
                    if(file_exists($invoice_pdf)) {
                        echo '<a target="_blank" href="'.$invoice_pdf.'">Invoice #'.$invoice['invoiceid'].' <img src="'.WEBSITE_URL.'/img/icons/pdf.png" title="Invoice PDF" class="no-toggle inline-img" /></a><br />';
                    }
                    if($invoice['invoiceid_src'] > 0 && file_exists('../'.FOLDER_NAME.'/Download/invoice_'.$invoice['invoiceid_src'].'.pdf')) {
                        echo '<a target="_blank" href="'.'../'.FOLDER_NAME.'/Download/invoice_'.$invoice['invoiceid_src'].'.pdf'.'">Primary Invoice #'.$invoice['invoiceid_src'].' <img src="'.WEBSITE_URL.'/img/icons/pdf.png" title="Primary Invoice PDF" class="no-toggle inline-img" /></a><br />';
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
                if($invoice['invoiceid_src'] > 0) {
                    $invoice = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='".$invoice['invoiceid_src']."'"));
                    $src_row = true;
                    $src_ids[] = $invoice['invoiceid'];
                }

            }

            echo '</table></div>';

            // Added Pagination
            if($limit != '') {
                echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            }
        } else {
            echo "<h4 class='gap-left'>No Record Found.</h4>";
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

    </div>
</div><!-- .standard-body-content -->