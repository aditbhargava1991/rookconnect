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
</head>
<body>
<?php include_once ('../navigation.php');
$ux_options = explode(',',get_config($dbc, FOLDER_NAME.'_ux'));
?>
<div id="invoice_div" class="container">
    <div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="edit_board" src=""></iframe>
		</div>
	</div>
    
	<div class="row">
        <div class="main-screen">
            <div class="tile-header standard-header">
                <div class="row">
                    <h1 class="pull-left"><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?></h1>
                    <?php if(config_visible_function($dbc, (FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out')) == 1) {
                        echo '<a href="field_config_invoice.php" class="pull-right gap-right gap-top"><img width="30" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me no-toggle"></a>';
                    } ?>
                    <span class="pull-right gap-top offset-right-5"><img src="../img/icons/eyeball.png" alt="View Tabs" title="View Tabs" class="cursor-hand no-toggle inline-img" onclick="view_tabs();" /></span>
                    <div class="clearfix"></div>
                    <div class="view_tabs double-padded" style="display:none;"><?php include('tile_tabs.php'); ?></div>
                </div>
            </div><!-- .tile-header -->

            <div class="scale-to-fill has-main-screen">
                <div class="main-screen standard-body form-horizontal">
                    <div class="standard-body-title">
                        <h3>All Invoices</h3>
                    </div>
                    
                    <div class="standard-body-content">
                        <form name="invoice" method="GET" action="" class="form-horizontal" role="form">
                            <?php $value_config = ','.get_config($dbc, 'invoice_dashboard').','; ?>
                            <?php $search_contact = 0;
                            $search_delivery = '';
                            $search_from = date('Y-m-01');
                            $search_to = date('Y-m-t');
                            if (isset($_GET['search_invoice_submit'])) {
                                if($_GET['contactid'] != '') {
                                   $search_contact = $_GET['contactid'];
                                }
                                if($_GET['type'] != '') {
                                   $search_delivery = $_GET['type'];
                                }
                                if($_GET['search_from'] != '') {
                                   $search_from = $_GET['search_from'];
                                }
                                if($_GET['search_to'] != '') {
                                   $search_to = $_GET['search_to'];
                                }
                            } ?>
                            <div class="search-group double-gap-top">
                                <div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <div class="col-sm-4">
                                            <label for="site_name" class="control-label">Search By <?= $purchaser_label ?>:</label>
                                        </div>
                                        <div class="col-sm-8">
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
                                    </div>
                                    <?php if(strpos($value_config,',delivery,') !== FALSE) { ?>
                                        <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                            <div class="col-sm-4">
                                                <label for="site_name" class="control-label">Search By Delivery/Shipping Type:</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <select name="type" data-placeholder="Select Delivery/Shipping Type..." class="chosen-select-deselect form-control width-me">
                                                    <option value=''></option>
                                                    <option <?= ($search_delivery == 'Pick-Up' ? 'selected' : '') ?> value="Pick-Up">Pick-Up</option>
                                                    <option <?= ($search_delivery == 'Company Delivery' ? 'selected' : '') ?> value="Company Delivery">Company Delivery</option>
                                                    <option <?= ($search_delivery == 'Drop Ship' ? 'selected' : '') ?> value="Drop Ship">Drop Ship</option>
                                                    <option <?= ($search_delivery == 'Shipping' ? 'selected' : '') ?> value="Shipping">Shipping</option>
                                                    <option <?= ($search_delivery == 'Shipping on Customer Account' ? 'selected' : '') ?> value="Shipping on Customer Account">Shipping on <?= $purchaser_label ?> Account</option>
                                                </select>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <div class="col-sm-4">
                                            <label for="site_name" class="control-label">Search From Date:</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input name="search_from" type="text" class="datepicker form-control" value="<?= $search_from ?>">
                                        </div>
                                    </div>
                                    <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
                                        <div class="col-sm-4">
                                            <label for="site_name" class="control-label">Search To Date:</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input name="search_to" type="text" class="datepicker form-control" value="<?= $search_to ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group col-lg-3 col-md-4 col-sm-12 col-xs-12">
                                    <div style="display:inline-block; padding: 0 0.5em;">
                                        <button type="submit" name="search_invoice_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
                                    </div>
                                    <div style="display:inline-block; padding: 0 0.5em;">
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
                            if($search_delivery != '') {
                                $search_clause .= " AND `delivery_type`='$search_delivery'";
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

                            $query_check_credentials = "SELECT * FROM invoice WHERE deleted = 0 AND `status` != 'Void' $search_clause ORDER BY invoiceid DESC $limit";
                            $query = "SELECT count(*) as numrows FROM invoice WHERE deleted = 0 AND `status` != 'Void' $search_clause";

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
                                        echo '<td data-title="Invoice #">' .($invoice['invoice_type'] == 'New' ? '' : $invoice['invoice_type'].' #'). $invoice['invoiceid'].($invoice['invoiceid_src'] > 0 ? '<br />For Invoice #'.$invoice['invoiceid_src'] : '') . '</td>';
                                    }


                                    if (strpos($value_config, ','."invoice_date".',') !== FALSE) {

                                        echo '<td data-title="Invoice Date" style="white-space: nowrap; ">'.$invoice['invoice_date'].'</td>';
                                    }
                                    if (strpos($value_config, ','."customer".',') !== FALSE) {
                                        echo '<td data-title="'.$purchaser_label.'"><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/'.CONTACTS_TILE.'/contacts_inbox.php?edit='.$invoice['patientid'].'\', \'auto\', false, true, $(\'#invoice_div\').outerHeight()+20); return false;">' . get_contact($dbc, $contactid, 'name_company') . '</a></td>';
                                    }
                                    if (strpos($value_config, ','."total_price".',') !== FALSE) {
                                        echo '<td data-title="Total Price" align="right">$' . number_format($invoice['final_price'],2) . '</td>';
                                    }
                                    if (strpos($value_config, ','."payment_type".',') !== FALSE) {
                                        echo '<td data-title="Payment Type">' . explode('#*#',$invoice['payment_type'])[0] . '</td>';
                                    }
                                    if (strpos($value_config, ','."delivery".',') !== FALSE) {
                                        echo '<td data-title="Delivery/Shipping Type">' . $invoice['delivery_type'] . '</td>';
                                    }
                                    if (strpos($value_config, ','."invoice_pdf".',') !== FALSE) {
                                        echo '<td data-title="Invoice PDF">';
                                        if(file_exists($invoice_pdf)) {
                                            echo '<a target="_blank" href="'.$invoice_pdf.'">Invoice #'.$invoice['invoiceid'].' <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a><br />';
                                        }
                                        if($invoice['invoiceid_src'] > 0 && file_exists('../'.FOLDER_NAME.'/Download/invoice_'.$invoice['invoiceid_src'].'.pdf')) {
                                            echo '<a target="_blank" href="'.'../'.FOLDER_NAME.'/Download/invoice_'.$invoice['invoiceid_src'].'.pdf'.'">Primary Invoice #'.$invoice['invoiceid_src'].' <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a><br />';
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

                                echo '</table></div></div>';

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
                </div><!-- .main-screen standard-body -->
            </div><!-- .has-main-screen -->
        </div><!-- .main-screen -->

    </div><!-- .row -->
</div><!-- .container -->
<?php include ('../footer.php'); ?>
