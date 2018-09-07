<?php
/*
Payment/Invoice Listing SEA
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['send_email'])) {
	$email_list = $_POST['recipient'];
	$subject = $_POST['subject'];
	$body = $_POST['body'];
	$sender = $_POST['sender'];
	$sender = [$sender => get_contact($dbc, $_SESSION['contactid'])];
	$customers = $_POST['customer'];
	$invoices = $_POST['pdf_send'];

	foreach($invoices as $invoice) {
		$invoice = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `point_of_sell` WHERE `posid`='$invoice'"));
		$to = $email_list;
		if($customers == 'customer') {
			$to .= ','.get_email($dbc, $invoice['contactid']);
		}
		$to = filter_var_array(explode(',', $to), FILTER_VALIDATE_EMAIL);
		if(count($to) > 0) {
			$file = 'download/invoice_'.$invoice['posid'].($invoice['edit_id'] > 0 ? '_'.$invoice['edit_id'] : '').'.pdf';
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
$get_invoice =	mysqli_query($dbc,"SELECT `posid` FROM `point_of_sell` WHERE `invoice_date` + INTERVAL 30 DAY < NOW() AND `status` NOT IN ('Completed', 'Void', 'Archived'");
$num_rows = mysqli_num_rows($get_invoice);
if($num_rows > 0) {
    while($row = mysqli_fetch_array( $get_invoice )) {
    $posid = $row['posid'];
		$before_change = capture_before_change($dbc, 'point_of_sell', 'status', 'posid', $posid);

		$query_update_project = "UPDATE `point_of_sell` SET status = 'Posted Past Due' WHERE `posid` = '$posid'";
		$result_update_project = mysqli_query($dbc, $query_update_project);

		$history = capture_after_change('status', 'Posted Past Due');
		add_update_history($dbc, 'pos_history', $history, '', $before_change);
    }
}

if((!empty($_GET['type'])) && ($_GET['type'] == 'send_email')) {
    $type = $_GET['type'];
    $posid = $_GET['id'];


}
?>
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
checkAuthorised('pos');
if(session_status() == PHP_SESSION_NONE) {
	session_start(['cookie_lifetime' => 518400]);
	$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
}
if ( isset ( $_POST['search_invoice_submit'] ) ) {
	$starttime	= $_POST['starttime'];
	$endtime	= $_POST['endtime'];
	$_SESSION['starttime'] = $starttime;
	$_SESSION['endtime'] = $endtime;
}
?>
<div class="container triple-pad-bottom">
    <div class='iframe_holder' style='display:none;'>

		<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframer' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
		<span class='iframe_title' style='color:white; font-weight:bold; position: relative; left: 20px; font-size: 30px;'></span>
		<iframe id="iframe_instead_of_window" style='width: 100%;' height="1000px; border:0;" src=""></iframe>
    </div>
	<div class="row hide_on_iframe">
		<?php if(isset($_GET['contact_view_invoice'])) {
				echo '<a href="#" class="btn config-btn btn-lg " onclick="history.go(-1);return false;">Back</a>';
		} ?>
		<div class="col-sm-10">
			<h1><?= POS_ADVANCE_TILE ?> Dashboard</h1>
		</div>
		<div class="col-sm-2 double-gap-top">
			<?php
				if(config_visible_function($dbc, 'pos') == 1) {
					echo '<a href="field_config_pos.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a>';
				}
			?>
        </div>

		<div class="clearfix double-gap-bottom"></div>

		<?php
		$numodays = '';
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='archive_after_num_days'"));
					if($get_config['configid'] > 0) {
						$get_num_of_days = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM	general_configuration WHERE	name='archive_after_num_days'"));
						$numodays = $get_num_of_days['value'];

					}

		?>

        <div class="gap-left tab-container mobile-100-container">
			<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'sell') === TRUE ) { ?>
				<?php
					$pos_layout	= get_config($dbc, 'pos_layout');

					if ( $pos_layout=='keyboard' ) { ?>
						<a href="add_point_of_sell.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Sell</button></a><?php
					} elseif  ( $pos_layout=='touch' ) { ?>
						<a href="pos_touch.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Sell</button></a><?php
					} else { ?>
						<a href="add_point_of_sell.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Sell - Keyboard Input</button></a>
						<a href="pos_touch.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Sell - Touch Input</button></a><?php
					}
				?>
			<?php } else { ?>
				<button type="button" class="btn disabled-btn mobile-block mobile-100">Sell</button>
			<?php } ?>

			<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'invoices') === TRUE ) { ?>
				<a href="point_of_sell.php"><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Invoices</button></a>
			<?php } else { ?>
				<button type="button" class="btn disabled-btn mobile-block mobile-100">Invoices</button>
			<?php } ?>

			<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'returns') === TRUE ) { ?>
				<a href="returns.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Returns</button></a>
			<?php } else { ?>
				<button type="button" class="btn disabled-btn mobile-block mobile-100">Returns</button>
			<?php } ?>

			<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'unpaid') === TRUE ) { ?>
				<a href="unpaid_invoice.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Accounts Receivable</button></a>
			<?php } else { ?>
				<button type="button" class="btn disabled-btn mobile-block mobile-100">Accounts Receivable</button>
			<?php } ?>

			<?php if ( check_subtab_persmission($dbc, 'pos', ROLE, 'voided') === TRUE ) { ?>
				<a href="voided.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Voided Invoices</button></a>
			<?php } else { ?>
				<button type="button" class="btn disabled-btn mobile-block mobile-100">Voided Invoices</button>
			<?php } ?>

			<?php if ( vuaed_visible_function ( $dbc, 'pos' ) == 1 ) { ?>
				<a href="giftcards.php"><button type="button" class="btn brand-btn mobile-block mobile-100">Gift Cards</button></a>
			<?php } else {
				echo '<script>
						alert("You do not have access to this page, please consult your software administrator (or settings) to gain access to this page.");
						window.location.replace("point_of_sell.php");
					</script>';
			} ?>
		</div><!-- .mobile-100-container -->

    <form name="invoice_table" method="post" action="point_of_sell.php" class="form-inline" role="form">
			<?php $invoice_name = '';
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
				   $searchbycustomer = " AND (c.name = '".$_POST['contactid']."' OR inv.`software_author`='".$_POST['contactid']."') ";
				}
				if($_POST['type'] != '') {
				   $searchbytype = " AND inv.delivery_type = '".$_POST['type']."' ";
				}
				$search = $_POST['search_invoice'];
				$contactid = $_POST['contactid'];
				$type = $_POST['type'];
			} ?>
			<div class="search-group">
				<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="col-sm-4">
							<label for="site_name" class="control-label">
								Search By Any:</label>
						</div>
						<div class="col-sm-8">
							<input  type="text" name="search_invoice" value="<?php echo $search; ?>" class="form-control">
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="col-sm-4">
							<label for="site_name" class="control-label">
								Search By Customer:</label>
						</div>
						<div class="col-sm-8">
							<select name="contactid" data-placeholder="Select Customer..." class="chosen-select-deselect form-control width-me">
								<option value=''></option>
								<?php
								$result = mysqli_query($dbc, "SELECT contactid, name FROM contacts WHERE category NOT IN (".STAFF_CATS.") AND category != 'Employee' order by name");
								while($row = mysqli_fetch_assoc($result)) {
									if ($contactid == $row['name']) {
										$selected = 'selected="selected"';
									} else {
										$selected = '';
									}
									echo "<option ".$selected." value = '".$row['name']."'>".decryptIt($row['name'])."</option>";
								}
								$result = mysqli_query($dbc, "SELECT DISTINCT `software_author` name FROM `point_of_sell` WHERE `software_author`!='' ORDER BY `software_author`");
								while($row = mysqli_fetch_assoc($result)) {
									echo "<option ".($contactid == $row['name'])." value='".$row['name']."'>".$row['name']."</option>";
								}
							   ?>
							</select>
						</div>
					</div>
				</div>
				<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="col-sm-4">
							<label for="site_name" class="control-label">
								Search By Delivery/Shipping Type:</label>
						</div>
						<div class="col-sm-8">
							<select name="type" data-placeholder="Select Delivery/Shipping Type..." class="chosen-select-deselect form-control width-me">
								<option value=''></option>
								<option <?php if ($type == "Pick-Up") { echo " selected"; } ?>  value="Pick-Up">Pick-Up</option>
								<option <?php if ($type == "Company Delivery") { echo " selected"; } ?>  value="Company Delivery">Company Delivery</option>
								<option <?php if ($type == "Drop Ship") { echo " selected"; } ?>  value="Drop Ship">Drop Ship</option>
								<option <?php if ($type == "Shipping") { echo " selected"; } ?>  value="Shipping">Shipping</option>
							</select>
						</div>
					</div>
				</div>
				<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
					<?php $starttimesql = '';
					if ( isset ( $_SESSION['starttime'] ) || isset ( $_SESSION['endtime'] ) ) {
						if ( ( $_SESSION['starttime'] !== '' && $_SESSION['endtime'] !== '' ) ) {
							$starttimesql = " AND (inv.invoice_date >= '" . $_SESSION['starttime'] . "' AND inv.invoice_date <= '" . $_SESSION['endtime'] . "') ";
						} else if ( ( $_SESSION['starttime'] !== '' && $_SESSION['endtime'] == '' ) ) {
							$starttimesql = " AND (inv.invoice_date >= '" . $_SESSION['starttime'] . "') ";
						} else if ( ( $_SESSION['starttime'] == '' && $_SESSION['endtime'] !== '' ) ) {
							$starttimesql = " AND (inv.invoice_date <= '" . $_SESSION['endtime'] . "') ";
						}
					} ?>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="col-sm-4">
							<label for="site_name" class="control-label">
								Search From Date:</label>
						</div>
						<div class="col-sm-8">
							<input name="starttime" type="text" class="datepicker form-control" value="<?php echo ( isset ( $_SESSION['starttime'] ) ) ? $_SESSION['starttime'] : ''; ?>">
						</div>
					</div>
					<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
						<div class="col-sm-4">
							<label for="site_name" class="control-label">
								Search To Date:</label>
						</div>
						<div class="col-sm-8">
							<input name="endtime" type="text" class="datepicker form-control" value="<?php echo ( isset ( $_SESSION['endtime'] ) ) ? $_SESSION['endtime'] : ''; ?>">
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
			</div><!-- .form-group -->
			<div class="clearfix"></div>
			<?php $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT pos_dashboard FROM field_config"));
			$value_config = ','.$get_field_config['pos_dashboard'].','; ?>

        </form>
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

            if (isset($_POST['display_all_invoice'])) {
                $invoice_name = '';
				$query = "SELECT *, COUNT(inv.posid) AS numrows FROM point_of_sell inv WHERE inv.deleted = 0 AND inv.status='Completed' ORDER BY inv.posid DESC";
            }
			if(isset($_GET['contact_view_invoice'])) {
				$view_invoice = $_GET['contact_view_invoice'];
				//query manipulation allows users to view all invoices given a specific contact id. This is done by going to the contacts tile > business category > any business that has been assigned to an invoice will have a link to view all invoices attached to that business. Click the link, it will send you to this page, which will display only invoices assigned to that specific business.
				$query_manipulation = " AND inv.contactid = '".$view_invoice."'";
			} else {
				$query_manipulation = "AND inv.status='Completed'";
			}

			if ( isset ( $_POST['search_invoice_submit'] )) {
                $query_check_credentialss = "SELECT inv.*, c.contactid, c.name, c.first_name, c.last_name FROM point_of_sell inv,  contacts c WHERE inv.contactid = c.contactid AND inv.deleted = 0 ".$query_manipulation." ".$searchbyany." ".$searchbycustomer." ".$searchbytype." ".$starttimesql." ORDER BY inv.posid DESC LIMIT $offset, $rowsPerPage";
                $query = "SELECT count(inv.posid) as numrows FROM point_of_sell inv,  contacts c WHERE inv.contactid = c.contactid AND inv.deleted = 0 ".$query_manipulation." ".$searchbyany." ".$searchbycustomer." ".$searchbytype." ".$starttimesql." ORDER BY inv.posid DESC";
            } else {
                $query_check_credentialss = "SELECT * FROM point_of_sell inv WHERE inv.deleted = 0 ".$query_manipulation." ORDER BY inv.posid DESC LIMIT $offset, $rowsPerPage";
				 $query = "SELECT COUNT(posid) as numrows FROM point_of_sell inv WHERE inv.deleted = 0 ".$query_manipulation." ORDER BY inv.posid DESC";
            }

            // how many rows we have in database
            $queryy = "SELECT COUNT(posid) AS numrows FROM point_of_sell";

            if($invoice_name == '') {
               // echo '<h1 class="single-pad-bottom">'.display_pagination($dbc, $queryy, $pageNumm, $rowsPerPagee).'</h1>';
            }

            $resultt = mysqli_query($dbc, $query_check_credentialss);

            $num_rowss = mysqli_num_rows($resultt);
            if($num_rowss > 0) {

                // Added Pagination //
                if(isset($query))
                    echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
                // Pagination Finish //

                echo "<br /><div id='no-more-tables'><table class='table table-bordered'>";
                echo "<tr class='hidden-xs hidden-sm'>";
                    if (strpos($value_config, ','."Invoice #".',') !== FALSE) {
                        echo '<th>Invoice #</th>';
                    }
                    if (strpos($value_config, ','."Invoice Date".',') !== FALSE) {
                        echo '<th>Invoice Date</th>';
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
                        echo '<th>Invoice PDF</th>';
                    }
                    if (strpos($value_config, ','."Comment".',') !== FALSE) {
                        echo '<th>Comment</th>';
                    }
                    if (strpos($value_config, ','."Status".',') !== FALSE) {
                        echo '<th>Status</th>';
                    }
					if (strpos($value_config, ','."Send") !== FALSE) {
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
						$before_change = capture_before_change($dbc, 'point_of_sell', 'status', 'posid', $posid);

						$query_update_employee = "UPDATE `point_of_sell` SET status = 'Archived' WHERE posid='$posid'";
						$result_update_employee = mysqli_query($dbc, $query_update_employee);

						$history = capture_after_change('status', 'Archived');
						add_update_history($dbc, 'pos_history', $history, '', $before_change);

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
                    echo '<td data-title="Invoice #">' . $roww['posid'] . '</td>';
                }


                if (strpos($value_config, ','."Invoice Date".',') !== FALSE) {

                    echo '<td data-title="Invoice Date" style="white-space: nowrap; ">'.$roww['invoice_date'].'</td>';
                }
                if (strpos($value_config, ','."Customer".',') !== FALSE) {
                    echo '<td data-title="Customer">' . ($contactid > 0 ? get_client($dbc, $contactid) : $roww['software_author']) . '</td>';
                }
                if (strpos($value_config, ','."Total Price".',') !== FALSE) {
                    echo '<td data-title="Total Price" align="right">$' . number_format($roww['total_price'],2) . '</td>';
                }
                if (strpos($value_config, ','."Payment Type".',') !== FALSE) {
					//Code was not working, so I had to manually pull from DB below ---v
					$get_pay_type = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM point_of_sell WHERE posid='".$roww['posid']."'"));
                    echo '<td data-title="Payment Type">' . $get_pay_type['payment_type'] . '</td>';
                }
                if (strpos($value_config, ','."Delivery/Shipping Type".',') !== FALSE) {
                    echo '<td data-title="Delivery/Shipping Type">' . $roww['delivery_type'] . '</td>';
                }
                if (strpos($value_config, ','."Invoice PDF".',') !== FALSE) {
                    echo '<td data-title="Invoice PDF">';
					$version = $roww['edit_id'];
					for($i = $version; $i > 0; $i--) {
						echo '<a target="_blank" href="download/invoice_'.$roww['posid'].'_'.$i.'.pdf">PDF '.($i == $version ? '' : 'V'.$i).' <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF '.($i == $version ? '' : 'V'.$i).'"></a><br />';
					}
					echo '<a target="_blank" href="download/invoice_'.$roww['posid'].'.pdf">'.($version > 0 ? 'Original' : 'PDF').' <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a></td>';
                }
                if (strpos($value_config, ','."Comment".',') !== FALSE) {
                    echo '<td data-title="Comment">' .  html_entity_decode($roww['comment']) . '</td>';
                }
                if (strpos($value_config, ','."Status".',') !== FALSE) {
                    echo '<td data-title="Status">';
                    ?>
                    <select name="status[]" id="status_<?php echo $roww['posid']; ?>" class="chosen-select-deselect1 form-control" width="380">
                        <option value=""></option>
                        <option value="Posted" <?php if ($roww['status'] == "Posted") { echo " selected"; } ?> >Posted</option>
                        <option value="Posted Past Due" <?php if ($roww['status'] == "Posted Past Due") { echo " selected"; } ?> >Posted Past Due</option>
                        <option value="Completed" <?php if ($roww['status'] == "Completed") { echo " selected"; } ?> >Completed</option>
                        <option value="Void" <?php if ($roww['status'] == "Void") { echo " selected"; } ?> >Void</option>
						<option value="Archived" <?php if ($roww['status'] == "Archived") { echo " selected"; } ?> >Archive</option>
                    </select>
                <?php
                    echo '</td>';
                    }
					if (strpos($value_config, ','."Send") !== FALSE) {
						echo '<td data-title="Email PDF">';
						?><input style="height: 25px; width: 25px;" type='checkbox' name='pdf_send[]' class='pdf_send' value='<?php echo $roww['posid']; ?>' onchange="show_hide_email();">
						<?php
						//echo '<a href=\'driving_log_14days.php?email=send&drivinglogid='.$row['drivinglogid'].'\'>Email</a>';
						echo '</td>';
					}
                echo "</tr>";

            }

            echo '</table></div></div>';

            // Added Pagination //
            if(isset($query))
                echo '<br clear="all" />' . display_pagination($dbc, $query, $pageNum, $rowsPerPage);
            // Pagination Finish //

            if($invoice_name == '') {
                //echo display_pagination($dbc, $queryy, $pageNumm, $rowsPerPagee);
            }

            ?>
			<div name="send_email_div" class="form-horizontal" style="display:none;">
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
					<div class="col-sm-8"><input type="text" class="form-control" name="subject" value="Point of Sale PDF(s)"></div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Email Body</label>
					<div class="col-sm-8"><textarea name="body">Please see the attached PDF(s) below.</textarea></div>
				</div>
				<button class="btn brand-btn pull-right" type="submit" name="send_email" value="send">Send Email</button>
			</div>
		</form>

	</div>
</div>
<?php include ('../footer.php'); ?>
