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
                    <h1 class="pull-left"><a href="invoice_main.php"><?= (empty($current_tile_name) ? 'Check Out' : $current_tile_name) ?></a></h1>
                    <?php if(config_visible_function($dbc, (FOLDER_NAME == 'posadvanced' ? 'posadvanced' : 'check_out')) == 1) {
                        echo '<a href="field_config_invoice.php" class="pull-right gap-right gap-top"><img width="30" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me no-toggle"></a>';
                    } ?>
                    <span class="pull-right gap-top offset-right-5"><img src="../img/icons/eyeball.png" alt="View Tabs" title="View Tabs" class="cursor-hand no-toggle inline-img" onclick="view_tabs();" /></span>
                    <span class="pull-right gap-top offset-right-5"><img src="../img/icons/pie-chart.png" alt="Reporting" title="Reporting" class="cursor-hand no-toggle inline-img" onclick="view_summary();" /></span>
                    <div class="clearfix"></div>
                    <div class="view_tabs double-padded" style="display:none;"><?php include('tile_tabs.php'); ?></div>
                    
                    <!-- Summary Blocks --><?php
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
                    
                    if($search_from == 0000-00-00) {
                        $search_from = (in_array($rookconnect,['sea','led']) ? '' : date('Y-m-d'));
                    }

                    if($search_to == 0000-00-00) {
                        $search_to = date('Y-m-d');
                    }
                    
                    $patient_clause = !empty($search_contact) ? "AND patientid = '$search_contact'" : '';
                    
                    $today_date = date('Y-m-d');
                    $as_at_date = $search_to;
                    $last29 = date('Y-m-d', strtotime($as_at_date.' - 29 days'));
                    $last30 = date('Y-m-d', strtotime($as_at_date.' - 30 days'));
                    $last59 = date('Y-m-d', strtotime($as_at_date.' - 59 days'));
                    $last60 = date('Y-m-d', strtotime($as_at_date.' - 60 days'));
                    $last89 = date('Y-m-d', strtotime($as_at_date.' - 89 days'));
                    $last90 = date('Y-m-d', strtotime($as_at_date.' - 90 days'));
                    $last119 = date('Y-m-d', strtotime($as_at_date.' - 119 days'));
                    $last120 = date('Y-m-d', strtotime($as_at_date.' - 120 days'));
                    $ar_types = "'On Account', 'Net 30', 'Net 30 Days', 'Net 60', 'Net 60 Days', 'Net 90', 'Net 90 Days', 'Net 120', 'Net 120 Days', ''";

                    $total_30 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$search_from."' AND DATE(invoice_date) <= '".$search_to."') AND DATE(invoice_date) >= '".$last29."' $patient_clause AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                    $total_last30 = $total_30['all_payment'];

                    $total_3059 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$search_from."' AND DATE(invoice_date) <= '".$search_to."') AND (DATE(invoice_date) >= '".$last59."' AND DATE(invoice_date) < '".$last29."') $patient_clause AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                    $total_last3059 = $total_3059['all_payment'];

                    $total_6089 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$search_from."' AND DATE(invoice_date) <= '".$search_to."') AND (DATE(invoice_date) >= '".$last89."' AND DATE(invoice_date) < '".$last59."') $patient_clause AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                    $total_last6089 = $total_6089['all_payment'];

                    $total_90119 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$search_from."' AND DATE(invoice_date) <= '".$search_to."') AND (DATE(invoice_date) >= '".$last119."' AND DATE(invoice_date) < '".$last89."') $patient_clause AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                    $total_last90119 = $total_90119['all_payment'];

                    $total_120 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$search_from."' AND DATE(invoice_date) <= '".$search_to."') AND (DATE(invoice_date) < '".$last119."') $patient_clause AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                    $total_last120 = $total_120['all_payment']; ?>

                    $ar_types = "'On Account', 'Net 30', 'Net 30 Days', 'Net 60', 'Net 60 Days', 'Net 90', 'Net 90 Days', 'Net 120', 'Net 120 Days', ''";
                    
                    $query_ar = mysqli_query($dbc,"SELECT DISTINCT(patientid) FROM invoice_patient WHERE (paid_date > '$as_at_date' OR `paid` IN ($ar_types)) AND (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') ORDER BY patientid");
                    
                    $total_ar_current = 0;
                    $total_ar_30 = 0;
                    $total_ar_60 = 0;
                    $total_ar_90 = 0;
                    $total_ar_120 = 0;
                    
                    while($row = mysqli_fetch_array($query_ar)) {
                        $patientid = $row['patientid'];
                        $today_date = date('Y-m-d');
                        $as_at_date = $_GET['search_to'] != '' ? $_GET['search_to'] : $today_date;
                        $last29 = date('Y-m-d', strtotime($as_at_date.' - 29 days'));
                        $last30 = date('Y-m-d', strtotime($as_at_date.' - 30 days'));
                        $last59 = date('Y-m-d', strtotime($as_at_date.' - 59 days'));
                        $last60 = date('Y-m-d', strtotime($as_at_date.' - 60 days'));
                        $last89 = date('Y-m-d', strtotime($as_at_date.' - 89 days'));
                        $last90 = date('Y-m-d', strtotime($as_at_date.' - 90 days'));
                        $last119 = date('Y-m-d', strtotime($as_at_date.' - 119 days'));
                        $last120 = date('Y-m-d', strtotime($as_at_date.' - 120 days'));

                        $total_30 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND DATE(invoice_date) >= '".$last29."' AND patientid = '$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                        $total_last30 = $total_30['all_payment'];

                        $total_3059 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last59."' AND DATE(invoice_date) < '".$last29."') AND patientid = '$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                        $total_last3059 = $total_3059['all_payment'];

                        $total_6089 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last89."' AND DATE(invoice_date) < '".$last59."') AND patientid = '$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                        $total_last6089 = $total_6089['all_payment'];

                        $total_90119 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) >= '".$last119."' AND DATE(invoice_date) < '".$last89."') AND patientid = '$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                        $total_last90119 = $total_90119['all_payment'];

                        $total_120 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT SUM(patient_price) AS `all_payment` FROM invoice_patient WHERE (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND (DATE(invoice_date) < '".$last119."') AND patientid = '$patientid' AND (paid_date > '$as_at_date' OR IFNULL(`paid`,'') IN ($ar_types))"));
                        $total_last120 = $total_120['all_payment'];
                        
                        $total_ar_current += $total_last30;
                        $total_ar_30 += $total_last3059;
                        $total_ar_60 += $total_last6089;
                        $total_ar_90 += $total_last90119;
                        $total_ar_120 += $total_last120;
                    } ?>
                    
                    <div class="view_summary double-gap-bottom" style="display:none;">
                        <?php $total_invoices = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`final_price`) `final_price` FROM `invoice` WHERE `deleted`=0 $search_clause $search_invoice_clause")); ?>
                        <div class="col-xs-12 col-sm-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= ( $total_ar_current > 0 ) ? '<a href="../Reports/report_tiles.php?type=ar&report=A/R Aging Summary&from='.$starttime.'&to='.$endtime.'">$'.number_format($total_ar_current, 2).'</a>' : '$'. 0; ?></div>
                                <div>Current A/R</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= ( $total_ar_30 > 0 ) ? '<a href="../Reports/report_tiles.php?type=ar&report=A/R Aging Summary&from='.$starttime.'&to='.$endtime.'">$'.number_format($total_ar_30, 2).'</a>' : '$'. 0; ?></div>
                                <div>30 - 59 Days A/R</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= ( $total_ar_60 > 0 ) ? '<a href="../Reports/report_tiles.php?type=ar&report=A/R Aging Summary&from='.$starttime.'&to='.$endtime.'">$'.number_format($total_ar_60, 2).'</a>' : '$'. 0; ?></div>
                                <div>60 - 89 Dyas A/R</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= ( $total_ar_90 > 0 ) ? '<a href="../Reports/report_tiles.php?type=ar&report=A/R Aging Summary&from='.$starttime.'&to='.$endtime.'">$'.number_format($total_ar_90, 2).'</a>' : '$'. 0; ?></div>
                                <div>90 - 119 Days A/R</div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-3 gap-top">
                            <div class="summary-block">
                                <div class="text-lg"><?= ( $total_ar_120 > 0 ) ? '<a href="../Reports/report_tiles.php?type=ar&report=A/R Aging Summary&from='.$starttime.'&to='.$endtime.'">$'.number_format($total_ar_120, 2).'</a>' : '$'. 0; ?></div>
                                <div>120+ Days A/R</div>
                            </div>
                        </div>
                        <div class="clearfix"></div>
                    </div><!-- .view_summary -->
                </div>
            </div><!-- .tile-header -->

            <div class="scale-to-fill has-main-screen">
                <div class="main-screen standard-body form-horizontal">
                    <div class="standard-body-title">
                        <h3>Accounts Receivable</h3>
                    </div>
                    
                    <div class="standard-body-content">
                        <form name="invoice" method="post" action="" class="form-horizontal" role="form">
                            <?php $value_config = ','.get_config($dbc, 'invoice_dashboard').','; ?>
                            
                            <div class="form-group search-group double-gap-top">
                                <div class="col-xs-12">
                                    <div class="col-sm-6 col-xs-12">
                                        <div class="col-sm-4">
                                            <label for="site_name" class="control-label">
                                                Search By <?= $purchaser_label ?>:</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <select name="contactid" data-placeholder="Select <?= $purchaser_label ?>..." class="chosen-select-deselect form-control width-me">
                                                <option value=''></option>
                                                <?php
                                                    /*
                                                    $result = mysqli_query($dbc, "SELECT contactid, name, first_name, last_name FROM contacts WHERE `contactid` IN (SELECT `patientid` FROM `invoice`) AND `deleted`=0 AND `status`>0");
                                                    while($row = mysqli_fetch_assoc($result)) {
                                                        if ($search_contact == $row['contactid']) {
                                                            $selected = 'selected="selected"';
                                                        } else {
                                                            $selected = '';
                                                        }
                                                        $cust_name = get_contact($dbc, $row['contactid']);
                                                        //echo "<option ".$selected." value = '".$row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name'])."</option>";
                                                        if ($cust_name != '-') {
                                                            echo "<option ".$selected." value = '".$row['contactid']."'>".$cust_name."</option>";
                                                        }
                                                    }
                                                    */
                                                ?>
                                                <?php
                                                    $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE `contactid` IN (SELECT `patientid` FROM `invoice`) AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
                                                    foreach($query as $id) {
                                                        $selected = '';
                                                        $selected = $id == $search_contact ? 'selected = "selected"' : '';
                                                        echo "<option ".$selected." value='".$id."'>".get_contact($dbc, $id).'</option>';
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <?php if(strpos($value_config,',delivery,') !== FALSE) { ?>
                                        <div class="col-sm-6 col-xs-12">
                                            <div class="col-sm-4">
                                                <label for="site_name" class="control-label">Search By Invoice #:</label>
                                            </div>
                                            <div class="col-sm-8">
                                                <input name="search_invoiceid" placeholder="Invoice #" class="form-control" value="<?= $search_invoiceid ?>" />
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                                <div class="col-xs-12">
                                    <div class="col-sm-6 col-xs-12">
                                        <div class="col-sm-4">
                                            <label for="site_name" class="control-label">Search From Date:</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input name="search_from" type="text" class="datepicker form-control" value="<?= $search_from ?>">
                                        </div>
                                    </div>
                                    <div class="col-sm-6 col-xs-12">
                                        <div class="col-sm-4">
                                            <label for="site_name" class="control-label">Search To Date:</label>
                                        </div>
                                        <div class="col-sm-8">
                                            <input name="search_to" type="text" class="datepicker form-control" value="<?= $search_to ?>">
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12 text-right gap-top">
                                    <button type="submit" name="search_invoice_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
                                    <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
                                </div>
                            </div>
                            <div class="clearfix"></div>
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

                            $query_check_credentials = "SELECT invoiceid, invoice_type, patientid, invoice_date, final_price, payment_type, delivery_type, status, comment FROM invoice WHERE deleted = 0 AND `status` != 'Void' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice_patient` WHERE `paid` IN ('On Account','','Net 30 Days','Net 60 Days','Net 90 Days','Net 120 Days','No') OR `paid` IS NULL UNION SELECT `invoiceid` FROM `invoice_insurer` WHERE `paid`!='Yes') $search_clause $search_invoice_clause ORDER BY invoiceid DESC $limit";
                            $query = "SELECT count(invoiceid) as numrows FROM invoice WHERE deleted = 0 AND `status` != 'Void' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice_patient` WHERE `paid` IN ('On Account','','Net 30 Days','Net 60 Days','Net 90 Days','Net 120 Days','No') OR `paid` IS NULL UNION SELECT `invoiceid` FROM `invoice_insurer` WHERE `paid`!='Yes') $search_clause $search_invoice_clause";

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
                                        echo '<td data-title="Invoice #">' .($invoice['invoice_type'] == 'New' ? '#' : $invoice['invoice_type'].' #'). $invoice['invoiceid'].($invoice['invoiceid_src'] > 0 ? '<br />For Invoice #'.$invoice['invoiceid_src'] : '') . '</td>';
                                    }
                                    if (strpos($value_config, ','."invoice_date".',') !== FALSE) {
                                        echo '<td data-title="Invoice Date" style="white-space: nowrap; ">'.$invoice['invoice_date'].'</td>';
                                    }
                                    if (strpos($value_config, ','."customer".',') !== FALSE) {
                                        echo '<td data-title="'.$purchaser_label.'"><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/'.CONTACTS_TILE.'/contacts_inbox.php?edit='.$contactid.'\', \'auto\', false, true, $(\'#invoice_div\').outerHeight()+20); return false;">' . get_contact($dbc, $contactid) . '</a></td>';
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
                    </div><!-- .standard-body-content -->
                </div><!-- .main-screen standard-body -->
            </div><!-- .has-main-screen -->
        </div><!-- .main-screen -->
        
	</div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>