<?php
/*
Payment/Invoice Listing SEA
*/
include_once ('../include.php');
include_once('../tcpdf/tcpdf.php');
$software_url = $_SERVER['SERVER_NAME'];
if(($software_url == 'sea-alberta.rookconnect.com' || $software_url == 'sea-vancouver.rookconnect.com' || $software_url == 'sea-saskatoon.rookconnect.com' || $software_url == 'sea.freshfocussoftware.com' || $software_url == 'sea-regina.rookconnect.com') && !isset($_GET['order_list'])) {
	header('Location: add_point_of_sell.php?order_list=1');
}

//set_time_limit(0);
$get_invoice =	mysqli_query($dbc,"SELECT posid FROM purchase_orders  WHERE `invoice_date` + INTERVAL 30 DAY < NOW() AND status!='Completed'");
$num_rows = mysqli_num_rows($get_invoice);
if($num_rows > 0) {
    while($row = mysqli_fetch_array( $get_invoice )) {
        $posid = $row['posid'];
		//$query_update_project = "UPDATE `purchase_orders` SET status = 'Posted Past Due' WHERE `posid` = '$posid'";
		//$result_update_project = mysqli_query($dbc, $query_update_project);
    }
}

if (isset($_POST['submit_pos'])) {
	$order_title = $_POST['order_list_nom'];
    $invoice_date = $_POST['invoice_date'];
    $name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$upload = '';
	if($_FILES['upload']['name'] != '') {
		$upload = file_safe_str($_FILES['upload']['name']);
		move_uploaded_file($_FILES['upload']['tmp_name'],'download/'.$upload);
	}
	$po_category = $_POST['po_category'];

    if($invoice_date == '') {
        $invoice_date = date('Y-m-d');
    }
    $contactid = implode(',',$_POST['contactid']);
    $equipmentid = $_POST['equipmentid'];
    $productpricing = $_POST['productpricing'];

    $sub_total = $_POST['sub_total'];

    $discount_value = '0';
    if($_POST['discount_value'] != 0) {
        $discount_type = $_POST['discount_type'];
        $discount_value = $_POST['discount_value'];
        if($discount_type == '%') {
            $d_value =  $discount_value.'%';
        }
        if($discount_type == '$') {
            $d_value =  '$'.$discount_value;
        }
    }

    $total_after_discount = $_POST['total_after_discount'];
    $delivery = filter_var($_POST['delivery'],FILTER_SANITIZE_STRING);
    $assembly = filter_var($_POST['assembly'],FILTER_SANITIZE_STRING);

    $delivery_type = filter_var($_POST['delivery_type'],FILTER_SANITIZE_STRING);
    $delivery_address = filter_var($_POST['delivery_address'],FILTER_SANITIZE_STRING);
    $contractorid = filter_var($_POST['contractorid'],FILTER_SANITIZE_STRING);

    $total_before_tax = $_POST['total_before_tax'];

    $tax_exemption_number = $_POST['tax_exemption_number'];
    $client_tax_exemption = $_POST['client_tax_exemption'];
    $total_tax = $_POST['tax_rate'];

    // GST PST
    $get_pos_tax = get_config($dbc, 'purchase_order_tax');
    $pdf_tax = '';
    $gst_total = 0;
    $pst_total = 0;
	$tax = $_POST[ 'select_tax' ]; /* Get selected tax type - configurator rate or manually entered rate */
	$entered_tax_rate = ( $tax == '0' ) ? intval ( trim ( $_POST['tax_rate'] ) ) : intval ( trim ( $_POST['tax_rate2'] ) );

    if($get_pos_tax != '') {
        $pos_tax = explode('*#*',$get_pos_tax);

        $total_count = mb_substr_count($get_pos_tax,'*#*');
        for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
            $pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

            if ( $tax == '0' ) {
				if (strcasecmp($pos_tax_name_rate[0], 'gst') == 0) {
					$gst_total = number_format((($total_before_tax*$pos_tax_name_rate[1])/100), 2);
				}
			} else { /* Tax rate entered manually */
				$gst_total = number_format((($total_before_tax*$entered_tax_rate)/100), 2);
			}

            if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0) {
                if($pos_tax_name_rate[3] == 'Yes' && $client_tax_exemption == 'Yes') {
                    $pst_total = 0;
                } else {
                    $pst_total = number_format((($total_before_tax*$pos_tax_name_rate[1])/100), 2);
                }
            }
        }
    }
    // GST PST

    $total_price = $_POST['total_price'];
	$dep_paid = round($_POST['deposit_paid'],2);
	$updatedtotal = round($total_price - $dep_paid,2);
    $payment_type = $_POST['payment_type'];
    $status = 'Pending';
	$software_author = '';
	$software_url = $_POST['software_url'];
	$software_seller = $_POST['software_seller'];
	if($software_url == 'sea-alberta.rookconnect.com') {
		$software_author = 'SEA Alberta';
	} else if($software_url == 'sea-vancouver.rookconnect.com') {
		$software_author = 'SEA Vancouver';
	} else if($software_url == 'sea-saskatoon.rookconnect.com') {
		$software_author = 'SEA Saskatoon';
	} else if($software_url == 'sea-regina.rookconnect.com') {
		$software_author = 'SEA Regina';
	}
    $pdf_product = '';
    $created_by = $_SESSION['contactid'];
    $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);
    $ship_date = $_POST['ship_date'];
	$due_date = $_POST['due_date'];
	$cross_software = $_POST['cross_software'];
	$projectid = $_POST['project'];
	$clientprojectid = '';
	if(substr($projectid,0,1) == 'C') {
		$clientprojectid = substr($projectid,1);
		$projectid = '';
	}
	$workorderid = $_POST['work_order'];
	$businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
	$siteid = filter_var($_POST['siteid'],FILTER_SANITIZE_STRING);
    $query_insert_invoice = "INSERT INTO `purchase_orders` (`invoice_date`, `name`, `upload`, `po_category`, `contactid`, `equipmentid`, `productpricing`, `sub_total`, `discount_type`, `discount_value`, `total_after_discount`, `delivery`, `assembly`, `total_before_tax`, `client_tax_exemption`, `tax_exemption_number`, `total_price`, `payment_type`, `created_by`, `comment`, `ship_date`, `due_date`, `status`, `gst`, `pst`, `delivery_type`, `delivery_address`, `contractorid`, `deposit_paid`, `updatedtotal`, `cross_software`,`software_author`, `software_seller`, `projectid`, `client_projectid`, `ticketid`, `businessid`, `siteid`, `workorderid`) VALUES ('$invoice_date', '$name', '$upload', '$po_category', '$contactid', '$equipmentid', '$productpricing', '$sub_total', '$discount_type', '$discount_value', '$total_after_discount', '$delivery', '$assembly', '$total_before_tax', '$client_tax_exemption', '$tax_exemption_number', '$total_price', '$payment_type', '$created_by', '$comment', '$ship_date', '$due_date', '$status', '$gst_total', '$pst_total', '$delivery_type', '$delivery_address', '$contractorid', '$dep_paid', '$updatedtotal', '$cross_software', '$software_author', '$software_seller', '$projectid', '$client_projectid', '$ticketid', '$businessid', '$siteid', '$workorderid')";

    $results_are_in = mysqli_query($dbc, $query_insert_invoice) or die(mysqli_error($dbc));

		$before_change = '';
    $history = "Purchase Order entry has been added. <br />";
    add_update_history($dbc, 'po_history', $history, '', $before_change);

    $posid = mysqli_insert_id($dbc);
			// ADD Column in Table for PDF //
			$col = "SELECT `type_category` FROM purchase_orders_product";
			$result = mysqli_query($dbc, $col);
		if (!$result){
			$colcreate = "ALTER TABLE `purchase_orders_product` ADD COLUMN `type_category` VARCHAR(555) NULL";
			$result = mysqli_query($dbc, $colcreate);
		}
    if($_POST['order_id'] == '' || $_POST['order_id'] == NULL) {
		for($i=0; $i<count($_POST['inventoryid']); $i++) {
			$inventoryid = $_POST['inventoryid'][$i];
			$price = $_POST['price'][$i];
			$quantity = $_POST['quantity'][$i];

			if($inventoryid != '') {
				$query_insert_invoice = "INSERT INTO `purchase_orders_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$inventoryid', '$quantity', '$price', 'inventory')";
				$results_are_in = mysqli_query($dbc, $query_insert_invoice);
			}
		}

		$before_change = '';
    $history = "Purchase Order products have been added. <br />";
    add_update_history($dbc, 'po_history', $history, '', $before_change);

		for($i=0; $i<count($_POST['vplinventoryid']); $i++) {
			$inventoryid = $_POST['vplinventoryid'][$i];
			$price = $_POST['vplprice'][$i];
			$quantity = $_POST['vplquantity'][$i];

			if($inventoryid != '') {
				$query_insert_invoice = "INSERT INTO `purchase_orders_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$inventoryid', '$quantity', '$price', 'vpl')";
				$results_are_in = mysqli_query($dbc, $query_insert_invoice);
			}
		}

		$before_change = '';
    $history = "Purchase Order products have been added. <br />";
    add_update_history($dbc, 'po_history', $history, '', $before_change);

		for($i=0; $i<count($_POST['prodinventoryid']); $i++) {
			$inventoryid = $_POST['prodinventoryid'][$i];
			$lineprice = $_POST['productlinepricing'][$i];
			$price = $_POST['prodprice'][$i];
			$quantity = $_POST['prodquantity'][$i];

			if($inventoryid != '') {
				$query_insert_invoice = "INSERT INTO `purchase_orders_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`, `lineprice`) VALUES ('$posid', '$inventoryid', '$quantity', '$price', 'product', '$lineprice')";
				$results_are_in = mysqli_query($dbc, $query_insert_invoice);
			}
		}

		$before_change = '';
    $history = "Purchase Order products have been added. <br />";
    add_update_history($dbc, 'po_history', $history, '', $before_change);

		for($i=0; $i<count($_POST['servinventoryid']); $i++) {
			$inventoryid = $_POST['servinventoryid'][$i];
			$price = $_POST['servprice'][$i];
			$quantity = $_POST['servquantity'][$i];

			if($inventoryid != '') {
				$query_insert_invoice = "INSERT INTO `purchase_orders_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$inventoryid', '$quantity', '$price', 'service')";
				$results_are_in = mysqli_query($dbc, $query_insert_invoice);
			}
		}

		$before_change = '';
    $history = "Purchase Order products have been added. <br />";
    add_update_history($dbc, 'po_history', $history, '', $before_change);

		for($i=0; $i<count($_POST['misc_product']); $i++) {
			$misc_product = filter_var($_POST['misc_product'][$i],FILTER_SANITIZE_STRING);
			$misc_tag = filter_var($_POST['misc_tag'][$i],FILTER_SANITIZE_STRING);
			$misc_grade = filter_var($_POST['misc_grade'][$i],FILTER_SANITIZE_STRING);
			$misc_detail = filter_var($_POST['misc_detail'][$i],FILTER_SANITIZE_STRING);
			$misc_qty = filter_var($_POST['misc_qty'][$i],FILTER_SANITIZE_STRING);
			$misc_price = $_POST['misc_price'][$i];

			if($misc_product != '') {
				$query_insert_invoice = "INSERT INTO `purchase_orders_product` (`posid`, `misc_product`, `tag`, `grade`, `detail`, `price`, `quantity`, `type_category`) VALUES ('$posid', '$misc_product', '$misc_tag', '$misc_grade', '$misc_detail', '$misc_price', '$misc_qty', 'misc product')";
				$results_are_in = mysqli_query($dbc, $query_insert_invoice);
			}
		}

		$before_change = '';
    $history = "Purchase Order products have been added. <br />";
    add_update_history($dbc, 'po_history', $history, '', $before_change);
	} else {
		$order_id = $_POST['order_id'];
		if($_POST['spreadsheet'] == 'Yes') {
					include('edit_vpl_spreadsheet.php');
					if(isset($file_name_save_db)) {
						$before_change = capture_before_change($dbc, 'purchase_orders', 'spreadsheet_name', 'posid', $posid);
						$query_update_vendor = "UPDATE `purchase_orders` SET `spreadsheet_name` = '$file_name_save_db' WHERE `posid` = '$posid'";
						$result_update_vendor = mysqli_query($dbc, $query_update_vendor);
				    $history = capture_after_change('spreadsheet_name', $file_name_save_db);
				    add_update_history($dbc, 'po_history', $history, '', $before_change);
			}
		}
		for($i=0; $i<count($_POST['inventoryid_list']); $i++) {
			$inventoryid = $_POST['inventoryid_list'][$i];
			$price = $_POST['price_list'][$i];
			$quantity = $_POST['quantity_list'][$i];
			$get_driver = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM order_lists WHERE order_id='$order_id'"));
			$tile = $get_driver['tile']; if($tile == 'VPL') { $tile = 'vpl'; } else { $tile = 'inventory'; }
			if($quantity > 0) {
				if($inventoryid != '') {
					$query_insert_invoice = "INSERT INTO `purchase_orders_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$inventoryid', '$quantity', '$price', '$tile')";
					$results_are_in = mysqli_query($dbc, $query_insert_invoice);
				}
			}
		}

		$before_change = '';
    $history = "Purchase Order products have been added. <br />";
    add_update_history($dbc, 'po_history', $history, '', $before_change);
	}

    include ('create_pos_pdf.php');
    $pos_design = get_config($dbc, 'purchase_order_design');
    if($pos_design == 1) {
        echo create_pos1_pdf($dbc, $posid, $d_value, $_POST['comment'], $gst_total, $pst_total, $entered_tax_rate);
    }
    if($pos_design == 2) {
        echo create_pos2_pdf($dbc, $posid, $d_value, $_POST['comment'], $gst_total, $pst_total, $entered_tax_rate);
    }
	if($pos_design == 3) {
        echo create_pos3_pdf($dbc, $posid, $d_value, $_POST['comment'], $gst_total, $pst_total, $_POST['company_software_name'], $entered_tax_rate);
    }

    $software_url = $_SERVER['SERVER_NAME'];

    if($software_url == 'www.washtechsoftware.com') {
        $to_email = 'troy@washtech.ca';
        //$to_email = 'dayanapatel@freshfocusmedia.com';
        $attachment = 'download/purchase_order_'.$posid.'.pdf';
        send_email('', $to_email, '', '', 'Washtech Invoice', 'Please see Attachment for Invoice', $attachment);
    }

    if($payment_type == 'Net 30 Days' || $payment_type == 'Net 30') {
        $send_invoice = $_POST['send_invoice'];
        if($send_invoice == 1) {
            $send_email = get_config($dbc, 'purchase_order_invoice_outbound_email');
            $arr_email=explode(",",$send_email);
            $attachment = 'download/purchase_order_'.$posid.'.pdf';
            //send_email('', $arr_email, '', '', 'Outbound Invoice', 'Please see Attachment for Outbound Invoice', $attachment);
        }
    }
    echo '<script> if(window.self !== window.top) {
		try {
			window.top.new_po_added("'.$posid.'", "'.$total_price.'");
		} catch (error) { }
	}
	window.location.replace("index.php");
	</script>';
if($cross_software !== 'zen') {
   echo '    window.open("download/purchase_order_'.$posid.'.pdf", "fullscreen=yes");';
}
echo '    </script>';
}
?>
<script type="text/javascript">

 var vplcount = 1;
$(document).ready(function() {

	$("#default_tax").show();
	$("#enter_tax").hide();

	$("input[name='select_tax']").change(function(){
		if ($(this).val() == '0') {
			$("#enter_tax").hide();
			$("#default_tax").show();
		} else {
			$("#default_tax").hide();
			$("#enter_tax").show();
		}
	});

	$("#form1").submit(function( event ) {
        var sub_total = $("input[name=sub_total]").val();
        var gst = $("input[name=gst]").val();
        var total_price = $("input[name=total_price]").val();
        var customerid = $("#customerid").val();
        var productpricing = $("#productpricing").val();
        //var payment_type = $("#payment_type").val();

        //if (sub_total == '0' || total_price == '0' || customerid == '' || productpricing == '' || payment_type == '') {
        if (sub_total == '0' || total_price == '0' || customerid == '' || payment_type == '') {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });

	var domain = document.location.hostname;
	/*if ( ! (domain == 'washtechsoftware.com' || domain == 'washtech.freshfocuscrm.com' || domain == 'localhost') ) {
		$('.price').attr('readonly', true);
	}*/
	//$('.price').attr('readonly', false);
	$('.servprice').attr('readonly', true);
	$('.prodprice').attr('readonly', true);
	$('.vplprice').attr('readonly', true);


    var count = 1;
    $('#deleteservices_0').hide();
	 var servcount = 1;
    $('#servdeleteservices_0').hide();
	 var prodcount = 1;
    $('#proddeleteservices_0').hide();
    $('#vpldeleteservices_0').hide();

	//BEGIN INVENTORY CODE
    $('#add_position_button').on( 'click', function () {
        $('#deleteservices_0').show();

        var clone = $('.additional_position').clone();
        clone.find('.form-control').val('');
        clone.find('.price').val('0');
        clone.find('.quantity').val('0');
        //clone.find(".product").html('');
        //clone.find(".product").trigger("change.select2");

        clone.find('.product').attr('id', 'product_dd_'+count);
        clone.find('.price').attr('id', 'price_dd_'+count);
        clone.find('.part').attr('id', 'part_dd_'+count);
        clone.find('.quantity').attr('id', 'qty_dd_'+count);
        clone.find('.category').attr('id', 'category_dd_'+count);

        clone.find('#services_0').attr('id', 'services_'+count);
        clone.find('#deleteservices_0').attr('id', 'deleteservices_'+count);
        $('#deleteservices_0').hide();

        clone.find('.form-control').trigger("change.select2");
        clone.removeClass("additional_position");
        $('#add_here_new_position').append(clone);
		resetChosen($("#category_dd_"+count));
		resetChosen($("#product_dd_"+count));
		resetChosen($("#part_dd_"+count));

        count++;
        return false;
    });

	// END INVENTORY and BEGIN VPL

	  $('#add_position_buttonvpl').on( 'click', function () {
        $('#vpldeleteservices_0').show();

        var clone = $('.additional_positionvpl').clone();
        clone.find('.form-control').val('');
        clone.find('.vplprice').val('0');
        clone.find('.vplquantity').val('0');
        //clone.find(".product").html('');
        //clone.find(".product").trigger("change.select2");

        clone.find('.vplproduct').attr('id', 'vplproduct_dd_'+vplcount);
        clone.find('.vplprice').attr('id', 'vplprice_dd_'+vplcount);
        clone.find('.vplpart').attr('id', 'vplpart_dd_'+vplcount);
        clone.find('.vplquantity').attr('id', 'vplqty_dd_'+vplcount);
        clone.find('.vplcategory').attr('id', 'vplcategory_dd_'+vplcount);

        clone.find('#vplservices_0').attr('id', 'vplservices_'+vplcount);
        clone.find('#vpldeleteservices_0').attr('id', 'vpldeleteservices_'+vplcount);
        $('#vpldeleteservices_0').hide();

        clone.find('.form-control').trigger("change.select2");
        clone.removeClass("additional_positionvpl");
        $('#add_here_new_positionvpl').append(clone);
		resetChosen($("#vplcategory_dd_"+vplcount));
		resetChosen($("#vplproduct_dd_"+vplcount));
		resetChosen($("#vplpart_dd_"+vplcount));

        vplcount++;
        return false;
    });

	  $('#order_forms').change(function() {
		var vpl_name = $(this).find('option:selected').val();
		var vendorid = $(this).find('option:selected').data('vendorid');
		if(vpl_name != undefined && vpl_name != '') {
			overlayIFrameSlider('<?= WEBSITE_URL ?>/Vendor Price List/order_form.php?from_tile=purchase_orders&vendorid='+vendorid+'&vpl_name='+vpl_name+'&vplcount='+vplcount, 'auto', true, true);
		}
	  });

	// END Vendor Price List and BEGIN Product

	$('#add_position_buttonprod').on( 'click', function () {
        $('#proddeleteservices_0').show();

        var clone = $('.additional_positionprod').clone();
        clone.find('.form-control').val('');
        clone.find('.prodprice').val('0');
        clone.find('.prodquantity').val('0');
        //clone.find(".product").html('');
        //clone.find(".product").trigger("change.select2");

        clone.find('.prodproduct').attr('id', 'prodproduct_dd_'+prodcount);
        clone.find('.prodlineprice').attr('id', 'prodlineprice_dd_'+prodcount);

        clone.find('.prodprice').attr('id', 'prodprice_dd_'+prodcount);
        clone.find('.prodpart').attr('id', 'prodpart_dd_'+prodcount);
        clone.find('.prodquantity').attr('id', 'prodqty_dd_'+prodcount);
        clone.find('.prodcategory').attr('id', 'prodcategory_dd_'+prodcount);

        clone.find('#prodservices_0').attr('id', 'prodservices_'+prodcount);
        clone.find('#proddeleteservices_0').attr('id', 'proddeleteservices_'+prodcount);
        $('#proddeleteservices_0').hide();

        clone.find('.form-control').trigger("change.select2");
        clone.removeClass("additional_positionprod");
        $('#add_here_new_positionprod').append(clone);
		resetChosen($("#prodcategory_dd_"+prodcount));
		resetChosen($("#prodproduct_dd_"+prodcount));

		resetChosen($("#prodlineprice_dd_"+prodcount));

		resetChosen($("#prodpart_dd_"+prodcount));

        prodcount++;
        return false;
    });

	// END PRODUCT BEGIN Service

	$('#add_position_buttonserv').on( 'click', function () {
        $('#servdeleteservices_0').show();

        var clone = $('.additional_positionserv').clone();
        clone.find('.form-control').val('');
        clone.find('.servprice').val('0');
        clone.find('.servquantity').val('0');
        //clone.find(".product").html('');
        //clone.find(".product").trigger("change.select2");

        clone.find('.servproduct').attr('id', 'servproduct_dd_'+servcount);
        clone.find('.servprice').attr('id', 'servprice_dd_'+servcount);
        clone.find('.servpart').attr('id', 'servpart_dd_'+servcount);
        clone.find('.servquantity').attr('id', 'servqty_dd_'+servcount);
        clone.find('.servcategory').attr('id', 'servcategory_dd_'+servcount);

        clone.find('#servservices_0').attr('id', 'servservices_'+servcount);
        clone.find('#servdeleteservices_0').attr('id', 'servdeleteservices_'+servcount);
        $('#servdeleteservices_0').hide();

        clone.find('.form-control').trigger("change.select2");
        clone.removeClass("additional_positionserv");
        $('#add_here_new_positionserv').append(clone);
		resetChosen($("#servcategory_dd_"+servcount));
		resetChosen($("#servproduct_dd_"+servcount));
		resetChosen($("#servpart_dd_"+servcount));
        servcount++;

        return false;
    });

	// END SERVICE

    var count = 1;
    $('#deletemisc_0').hide();
    $('#add_misc_button').on( 'click', function () {
        $('#deletemisc_0').show();

        var clone = $('.additional_misc').clone();
        clone.find('.form-control').val('');
        clone.find('.misc_price').val('0');

        clone.find('.misc_product').attr('id', 'misc_product_'+count);
        clone.find('.misc_price').attr('id', 'misc_price_dd_'+count);

        clone.find('#misc_0').attr('id', 'misc_'+count);
        clone.find('#deletemisc_0').attr('id', 'deletemisc_'+count);
        $('#deletemisc_0').hide();

        clone.find('.form-control').trigger("change.select2");
        clone.removeClass("additional_misc");
        $('#add_here_new_misc').append(clone);
        count++;
        return false;
    });

	var selected = $('[name=contactid]').val();
	var options = $('[name=contactid]').find('option');
	options.sort(function(a,b) {
		if(b.text == '')
			return 0;
		if(a.text.toUpperCase() > b.text.toUpperCase())
			return 1;
		if(a.text.toUpperCase() < b.text.toUpperCase())
			return -1;
		return 0;
	});
	$('[name=contactid]').empty().append(options);
	$('[name=contactid]').val(selected).trigger('change.select2');
});
$(document).on('change', 'select[name="contactid"]', function() { changeClient(this); });
$(document).on('change', 'select[name="productpricing"]', function() { selectProductPricing(this); });
$(document).on('change', 'select[name="category[]"]', function() { selectCategory(this); });
$(document).on('change', 'select[name="part_no[]"]', function() { selectProduct(this); });
$(document).on('change', 'select[name="inventoryid[]"]', function() { selectProduct(this); });
$(document).on('change', 'select[name="vplcategory[]"]', function() { vplselectCategory(this); });
$(document).on('change', 'select[name="vplpart_no[]"]', function() { vplselectProduct(this); });
$(document).on('change', 'select[name="vplinventoryid[]"]', function() { vplselectProduct(this); });
$(document).on('change', 'select[name="prodcategory[]"]', function() { prodselectCategory(this); });
$(document).on('change', 'select[name="prodpart_no[]"]', function() { prodselectProduct(this); });
$(document).on('change', 'select[name="prodinventoryid[]"]', function() { prodselectProduct(this); });
$(document).on('change', 'select[name="productlinepricing[]"]', function() { selectProductLinePricing(this); });
$(document).on('change', 'select[name="servcategory[]"]', function() { servselectCategory(this); });
$(document).on('change', 'select[name="servinventoryid[]"]', function() { servselectProduct(this); });
$(document).on('change', 'select[name="delivery_type"]', function() { selectShippingtype(); });

// FOR INVENTORY VV
function selectCategory(sel) {
    var hiddenpricing = $("#hiddenpricing").val();
    var productPrice = $("#productpricing").val();
    if(hiddenpricing == 1 && productPrice == '') {
        //alert('Error : Please select Pricing First.');
		//return false;
    }

    var end = sel.value;
    var typeId = sel.id;

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posFromCategory&name="+end,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('*#*');

            var arr = typeId.split('_');
            $("#part_dd_"+arr[2]).html(result[0]);
            $("#part_dd_"+arr[2]).trigger("change.select2");

            $("#product_dd_"+arr[2]).html(result[1]);
            $("#product_dd_"+arr[2]).trigger("change.select2");

            $("#price_dd_"+arr[2]).val('0');
            $("#qty_dd_"+arr[2]).val('0');
        }
    });
}

// END FOR INVENTORY & BEGIN FOR VPL

function vplselectCategory(sel) {
    var hiddenpricing = $("#hiddenpricing").val();
    var productPrice = $("#productpricing").val();
    if(hiddenpricing == 1 && productPrice == '') {
        //alert('Error : Please select Pricing First.');
		//return false;
    }
    var end = sel.value;
    var typeId = sel.id;

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posFromCategoryvpl&name="+end,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('*#*');

            var arr = typeId.split('_');
            $("#vplpart_dd_"+arr[2]).html(result[0]);
            $("#vplpart_dd_"+arr[2]).trigger("change.select2");

            $("#vplproduct_dd_"+arr[2]).html(result[1]);
            $("#vplproduct_dd_"+arr[2]).trigger("change.select2");

            $("#vplprice_dd_"+arr[2]).val('0');
            $("#vplqty_dd_"+arr[2]).val('0');
        }
    });
}

// END FOR VPL & BEGIN FOR Product

function prodselectCategory(sel) {
    var hiddenpricing = $("#hiddenpricing").val();
    var productPrice = $("#productpricing").val();
    if(hiddenpricing == 1 && productPrice == '') {
        //alert('Error : Please select Pricing First.');
		//return false;
    }
    var end = sel.value;
    var typeId = sel.id;

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posFromCategoryprod&name="+end,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('*#*');

            var arr = typeId.split('_');
            $("#prodpart_dd_"+arr[2]).html(result[0]);
            $("#prodpart_dd_"+arr[2]).trigger("change.select2");

            $("#prodproduct_dd_"+arr[2]).html(result[1]);
            $("#prodproduct_dd_"+arr[2]).trigger("change.select2");

            $("#prodprice_dd_"+arr[2]).val('0');
            $("#prodqty_dd_"+arr[2]).val('0');
        }
    });
}
// END FOR Product & BEGIN FOR Services

function servselectCategory(sel) {
    var hiddenpricing = $("#hiddenpricing").val();
    var productPrice = $("#productpricing").val();
    if(hiddenpricing == 1 && (productPrice == '' || productPrice == null)) {
        //alert('Error : Please select Pricing First.');
		//return false;
    }

    var end = sel.value;
    var typeId = sel.id;

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posFromCategoryserv&name="+end,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('*#*');

            var arr = typeId.split('_');
            $("#servpart_dd_"+arr[2]).html(result[0]);
            $("#servpart_dd_"+arr[2]).trigger("change.select2");

            $("#servproduct_dd_"+arr[2]).html(result[1]);
            $("#servproduct_dd_"+arr[2]).trigger("change.select2");

            $("#servprice_dd_"+arr[2]).val('0');
            $("#servqty_dd_"+arr[2]).val('0');
        }
    });
}

// END SERVICES & BEGIN FOR INVENTORY

function selectProduct(sel) {
    var end = sel.value;
    var typeId = sel.id;
    var arr = typeId.split('_');
    var hiddenpricing = $("#hiddenpricing").val();
    var productPrice = $("#productpricing").val();
    if(hiddenpricing == 1 && (productPrice == '' || productPrice == null)) {
        //alert('Error : Please select Pricing First.');
		//return false;
    }
    var category = $("#category_dd_"+arr[2]).val();

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posUpProductFromProduct&inventoryid="+end+"&productPrice="+productPrice+"&category="+category,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('**##**');

            $("#category_dd_"+arr[2]).html(result[0]);
            $("#part_dd_"+arr[2]).html(result[1]);
            $("#product_dd_"+arr[2]).html(result[2]);
            $("#category_dd_"+arr[2]).trigger("change.select2");
            $("#part_dd_"+arr[2]).trigger("change.select2");
            $("#product_dd_"+arr[2]).trigger("change.select2");

            if(hiddenpricing == 1) {
                $("#price_dd_"+arr[2]).val(result[3]);
            } else {
				$("#price_dd_"+arr[2]).val(result[3]);
			}
            $("#qty_dd_"+arr[2]).val('0');
        }
    });
}

// END INVENTORY & BEGIN FOR VPL

function vplselectProduct(sel) {
    var end = sel.value;
    var typeId = sel.id;
    var arr = typeId.split('_');
    var hiddenpricing = $("#hiddenpricing").val();
    var productPrice = $("#productpricing").val();
    if(hiddenpricing == 1 && (productPrice == '' || productPrice == null)) {
        //alert('Error : Please select Pricing First.');
		//return false;
    }
    var category = $("#vplcategory_dd_"+arr[2]).val();

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posUpProductFromProductvpl&inventoryid="+end+"&productPrice="+productPrice+"&category="+category,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('**##**');
            $("#vplcategory_dd_"+arr[2]).html(result[0]);
            $("#vplpart_dd_"+arr[2]).html(result[1]);
            $("#vplproduct_dd_"+arr[2]).html(result[2]);
            $("#vplcategory_dd_"+arr[2]).trigger("change.select2");
            $("#vplpart_dd_"+arr[2]).trigger("change.select2");
            $("#vplproduct_dd_"+arr[2]).trigger("change.select2");

            if(hiddenpricing == 1) {
                $("#vplprice_dd_"+arr[2]).val(result[3]);
            } else {
				$("#price_dd_"+arr[2]).val(result[3]);
			}

            $("#vplqty_dd_"+arr[2]).val('0');
        }
    });
}

// END VPL & BEGIN FOR PROD

function prodselectProduct(sel) {
    var end = sel.value;
    var typeId = sel.id;
    var arr = typeId.split('_');
    var hiddenpricing = $("#hiddenpricing").val();
    var productPrice = $("#productpricing").val();
    if(hiddenpricing == 1 && (productPrice == '' || productPrice == null)) {
        //alert('Error : Please select Pricing First.');
		//return false;
    }
    var category = $("#prodcategory_dd_"+arr[2]).val();

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posUpProductFromProductprod&inventoryid="+end+"&productPrice="+productPrice+"&category="+category,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('**##**');

            $("#prodcategory_dd_"+arr[2]).html(result[0]);
            $("#prodpart_dd_"+arr[2]).html(result[1]);
            $("#prodproduct_dd_"+arr[2]).html(result[2]);
            $("#prodcategory_dd_"+arr[2]).trigger("change.select2");
            $("#prodpart_dd_"+arr[2]).trigger("change.select2");
            $("#prodproduct_dd_"+arr[2]).trigger("change.select2");
            if(hiddenpricing == 1) {
                $("#prodprice_dd_"+arr[2]).val(result[3]);
            } else {
				$("#price_dd_"+arr[2]).val(result[3]);
			}
            $("#prodqty_dd_"+arr[2]).val('0');
        }
    });
}

// END PROD & BEGIN FOR SERVICES

function servselectProduct(sel) {
    var end = sel.value;
    var typeId = sel.id;
    var arr = typeId.split('_');
    var hiddenpricing = $("#hiddenpricing").val();
    var productPrice = $("#productpricing").val();
    if(hiddenpricing == 1 && (productPrice == '' || productPrice == null)) {
        //alert('Error : Please select Pricing First.');
		//return false;
    }
    var category = $("#servcategory_dd_"+arr[2]).val();

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posUpProductFromProductserv&inventoryid="+end+"&productPrice="+productPrice+"&category="+category,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('**##**');

            $("#servcategory_dd_"+arr[2]).html(result[0]);
            $("#servpart_dd_"+arr[2]).html(result[1]);
            $("#servproduct_dd_"+arr[2]).html(result[2]);
            $("#servcategory_dd_"+arr[2]).trigger("change.select2");
            $("#servpart_dd_"+arr[2]).trigger("change.select2");
            $("#servproduct_dd_"+arr[2]).trigger("change.select2");
            if(hiddenpricing == 1) {
                $("#servprice_dd_"+arr[2]).val(result[3]);
            } else {
				$("#price_dd_"+arr[2]).val(result[3]);
			}
            $("#servqty_dd_"+arr[2]).val('0');
        }
    });
}

// END SERVICES

function changeClient(sel) {
	var clientid = sel.value;
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "pos_ajax_all.php?fill=POSclient&clientid="+clientid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            var result = response.split('**');
            if (result[0].indexOf("Client Price") >= 0) {
                var price_val = "client_price";
            }
            if (result[0].indexOf("Admin Price") >= 0) {
                var price_val = "admin_price";
                $('.price').attr('readonly', false);
            }
            if (result[0].indexOf("Commercial Price") >= 0) {
                var price_val = "commercial_price";
            }
            if (result[0].indexOf("Wholesale Price") >= 0) {
                var price_val = "wholesale_price";
            }
            if (result[0].indexOf("Final Retail Price") >= 0) {
                var price_val = "final_retail_price";
            }
            if (result[0].indexOf("Preferred Price") >= 0) {
                var price_val = "preferred_price";
            }
			if (result[0].indexOf("Purchase Order Price") >= 0) {
                var price_val = "purchase_order_price";
            }
			if (result[0].indexOf("Sales Order Price") >= 0) {
                var price_val = "sales_order_price";
            }
            if (result[0].indexOf("Web Price") >= 0) {
                var price_val = "web_price";
            }
            if (result[0].indexOf("Average Cost") >= 0) {
                var price_val = "average_cost";
            }

            $("#client_tax_exemption").val(result[1]);
            var sum = 0;
            $('.pos_tax').each(function () {
                sum += +$(this).val() || 0;
            });
            var yes_tax_exemption = $("#yes_tax_exemption").val();
            if(result[1] == 'Yes' && yes_tax_exemption == 1) {
                $("#tax_exemption_fillup").html("Exemption Number : "+result[2]);
                var not_count_pos_tax = $("#not_count_pos_tax").val();
                var final_tax = parseFloat(sum - not_count_pos_tax);
                $("#tax_rate").val(final_tax);
                $("#tax_exemption_number").val(result[2]);
            } else {
                $("#tax_exemption_fillup").html('');
                $("#tax_exemption_number").val('');
                $("#tax_rate").val(sum);
            }
			$("#productpricing").val(price_val);
            $("#productpricing").trigger("change.select2");
            $("#delivery_address_fillup").val(result[3]);

			if (result[4] != '') {
				$('#project').html(result[4]).trigger("change.select2");
			}

			if (result[5] != '') {
				$('#ticket').html(result[5]).trigger("change.select2");
			}

			/* Query not working
			if (result[6] != '') {
				$('#work_order').html(result[6]).trigger("change.select2");
			}
			*/
		}
	});
}

function selectProductPricing(sel) {
    $(".category").val('');
    $(".category").trigger("change.select2");
    $(".product").val('');
    $(".product").trigger("change.select2");
    $(".part").val('');
    $(".part").trigger("change.select2");

	$(".vplcategory").val('');
    $(".vplcategory").trigger("change.select2");
    $(".vplproduct").val('');
    $(".vplproduct").trigger("change.select2");
    $(".vplpart").val('');
    $(".vplpart").trigger("change.select2");

	$(".servcategory").val('');
    $(".servcategory").trigger("change.select2");
    $(".servproduct").val('');
    $(".servproduct").trigger("change.select2");
    $(".servpart").val('');
    $(".servpart").trigger("change.select2");

	$(".prodcategory").val('');
    $(".prodcategory").trigger("change.select2");
    $(".prodproduct").val('');
    $(".prodproduct").trigger("change.select2");
    $(".prodpart").val('');
    $(".prodpart").trigger("change.select2");

    $('.price').val('0');
    $('.quantity').val('0');

	$('.vplprice').val('0');
    $('.vplquantity').val('0');

	$('.servprice').val('0');
    $('.servquantity').val('0');

	$('.prodprice').val('0');
    $('.prodquantity').val('0');

    $('#sub_total').val('0');
    $('#gst').val('0.00');
    $('#total_price').val('0');

    var productPrice = $("#productpricing").val();
    if(productPrice == 'admin_price') {
        $('.price').attr('readonly', false);
		$('.vplprice').attr('readonly', false);
		$('.servprice').attr('readonly', false);
		$('.prodprice').attr('readonly', false);
    } else {
        //$('.price').attr('readonly', true);
		 $('.vplprice').attr('readonly', true);
		 $('.servprice').attr('readonly', true);
		  $('.prodprice').attr('readonly', true);
    }
    countPOSTotal();
}

function selectProductLinePricing(sel) {
    var end = sel.value;
    var typeId = sel.id;
    var arr = typeId.split('_');

    var productid = $("#prodproduct_dd_"+arr[2]).val();

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posUpProductPriceFromProductprod&productid="+productid+"&productPrice="+end,
        dataType: "html",   //expect html to be returned
        success: function(response){
            $("#prodprice_dd_"+arr[2]).val(response);
            $("#prodqty_dd_"+arr[2]).val('0');
        }
    });
}

function updateTax() {
	var newTax = $("input[name='pos_tax2']").val();
	$("input[name='pos_tax2']").val(newTax);
	$("input[name='tax_rate2']").val(newTax);
}

function countPOSTotal(sel) {

	var productPrice = $("#productpricing").val();

    var current_id = sel.id;
    var result = current_id.split('_');

    var qty = $("#qty_dd_"+result[2]).val();
    var pro = $("#product_dd_"+result[2]).val();

	var servqty = $("#servqty_dd_"+result[2]).val();
    var servpro = $("#servproduct_dd_"+result[2]).val();

	var prodqty = $("#prodqty_dd_"+result[2]).val();
    var prodpro = $("#prodproduct_dd_"+result[2]).val();

	var vplqty = $("#vplqty_dd_"+result[2]).val();
    var vplpro = $("#vplproduct_dd_"+result[2]).val();

    var numQty = $('.quantity').length;
	var vplnumQty = $('.vplquantity').length;
	var servnumQty = $('.servquantity').length;
	var prodnumQty = $('.prodquantity').length;
    var c = 0;
    var i;
    var price = 0;

	var tax_type = $("input[name='select_tax']:checked").val();
	var tax_rate;

	if (tax_type == "1") {
		tax_rate = $("#tax_rate2").val();
		// console.log(tax_type+" + tax_rate: " + tax_rate);
	} else {
		tax_rate = $("#tax_rate").val();
		// console.log(tax_type+" + else tax_rate: " + tax_rate);
	}
	//tax_rate = 15;


    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posPromotion&pro="+pro+"&qty="+qty+"&productPrice="+productPrice,
        dataType: "html",   //expect html to be returned
        success: function(response){
            if(current_id == "qty_dd_"+result[2]) {
                if(response != '') {
                    $("#price_dd_"+result[2]).val(response);
                }
            }
        },
        complete: function(){
            for(i=0; i<numQty; i++) {
                var qty = $("#qty_dd_"+i).val();
                var price = $("#price_dd_"+i).val();
				if(price !== '' && price !== null && price > 0) {
					c += parseFloat(price*qty);
				}
            }

            var nummisc = $('.misc_price').length;
            var m;
            for(m=0; m<nummisc; m++) {
                var price = $("#misc_price_dd_"+m).val();
                if(price !== '' && price !== null && price > 0) {
					c += parseFloat(price);
				}
            }

			var ggxy;
            for(ggxy=0; ggxy<servnumQty; ggxy++) {
                var qty = $("#servqty_dd_"+ggxy).val();
                var price = $("#servprice_dd_"+ggxy).val();
                if(price !== '' && price !== null && price > 0) {
					c += parseFloat(price*qty);
				}
            }

			var ggxx;
            for(ggxx=0; ggxx<prodnumQty; ggxx++) {
				var qty = $("#prodqty_dd_"+ggxx).val();
                var price = $("#prodprice_dd_"+ggxx).val();
                if(price !== '' && price !== null && price > 0) {
					c += parseFloat(price*qty);
				}
            }

			var ggxz;
            for(ggxz=0; ggxz<vplnumQty; ggxz++) {
				var qty = $("#vplqty_dd_"+ggxz).val();
                var price = $("#vplprice_dd_"+ggxz).val();
                if(price !== '' && price !== null && price > 0) {
					c += parseFloat(price*qty);
				}
            }


            $("#sub_total").val((c).toFixed(2));
            $("#total_before_tax").val((c).toFixed(2));

            var discount_type = $('input:radio[name=discount_type]:checked').val();
            var discount_value = $("#discount_value").val();

            var total_after_dis = c;

            if(discount_type == '%') {
                var discount = (c*(discount_value/100));
                var total_after_dis = parseFloat(c) - parseFloat(discount);
            }
            if(discount_type == '$') {
                var total_after_dis = parseFloat(c) - parseFloat(discount_value);
            }

            $('#total_after_discount').val((total_after_dis).toFixed(2));

            var delivery = $("#delivery").val();
            var assembly = $("#assembly").val();
            if(delivery == '' || typeof delivery === "undefined") {
                delivery = 0;
            }
            if(assembly == '' || typeof assembly === "undefined") {
                assembly = 0;
            }

            var shipping_total = parseFloat(total_after_dis) + parseFloat(delivery) + parseFloat(assembly);

            var final_total = shipping_total.toFixed(2);

            $('#total_before_tax').val(final_total);

            //var tax_rate = taxCalc();
			//console.log("tax_rate: "+tax_rate);

            if (typeof tax_rate === "undefined") {
                $('#total_price').val(final_total);
				$('#updatedtotal').val(final_total-$('#deposit_paid').val());
            } else {
                var tax_rate_value = (final_total*tax_rate)/100;
                $('#tax_price').val(tax_rate_value.toFixed(2));
                var total_after_gst = parseFloat(final_total) + parseFloat(tax_rate_value);
                $('#total_price').val(total_after_gst.toFixed(2));
				$('#updatedtotal').val(final_total-$('#deposit_paid').val());
            }

        }
    });

}

function selectShippingtype() {
    var delivery_type = $("#delivery_type").val();
    if(delivery_type == 'Drop Ship' || delivery_type == 'Shipping') {
        $("#contractorid").show();
        $("#delivery_address").show();
        $("#delivery_div").show();
    } else if(delivery_type == 'Company Delivery') {
        $("#contractorid").hide();
        $("#delivery_div").hide();
        $("#delivery_address").show();
    } else {
        $("#contractorid").hide();
        $("#delivery_address").hide();
        $("#delivery_div").show();
    }
}
function seleteService(sel, hide, blank) {
	var typeId = sel.id;
	var arr = typeId.split('_');

    $("#"+hide+arr[1]).hide();
    $("#"+blank+arr[1]).val('');
}

function numericFilter(txb) {
   txb.value = txb.value.replace(/[^\0-9]/ig, "");
}

jQuery(document).ready(function($){
	$('.live-search-list2 tr').each(function(){
	$(this).attr('data-search-term', $(this).text().toLowerCase());
	});

	$('.live-search-box2').on('keyup', function(){

	var searchTerm = $(this).val().toLowerCase();

		$('.live-search-list2 tr').each(function(){

			if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
				$(this).show();
			} else {
				if($(this).hasClass('dont-hide')) {
				} else { $(this).hide(); }
			}

		});

	});

	$('[name=project],[name=siteid],[name=businessid],[name=work_order]').change(function() {
		if(this.name == 'work_order') {
			var ticket = $(this).find('option:selected');
			var project = ticket.data('project');
			$('[name=project]').val(project).trigger('change.select2');
			var business = ticket.data('business');
			if(project > 0 && !(business > 0)) {
				business = $('[name=project] option:selected').data('business');
			}
			if(business > 0) {
				$('[name=businessid]').val(business).trigger('change.select2');
			}
			var site = ticket.data('site');
			if(project > 0 && !(site > 0)) {
				site = $('[name=project] option:selected').data('site');
			}
			if(site > 0) {
				$('[name=siteid]').val(site).trigger('change.select2');
			}
		} else if(this.name == 'project') {
			var project = $(this).find('option:selected');
			$('[name=work_order] option').each(function() {
				if($(this).data('project') > 0 && $(this).data('project') != project.val() && project.val() > 0) {
					$(this).hide();
				} else {
					$(this).show();
				}
			});
			$('[name=work_order]').trigger('change.select2');
			var business = project.data('business');
            if(business > 0) {
                $('[name=businessid]').val(business).trigger('change.select2');
            }
			var site = project.data('site');
            if(site > 0) {
                $('[name=siteid]').val(site).trigger('change.select2');
            } else if(business > 0) {
                $('[name=siteid] option').each(function() {
                    if($(this).data('business') > 0 && $(this).data('business') != business && business > 0) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                });
                $('[name=siteid]').trigger('change.select2');
            }
		} else if(this.name == 'businessid') {
			var business = this.value;
			$('[name=project] option').each(function() {
				if($(this).data('business') > 0 && $(this).data('business') != business && business > 0) {
					$(this).hide();
				} else {
					$(this).show();
				}
			});
			$('[name=work_order] option').each(function() {
				if($(this).data('business') > 0 && $(this).data('business') != business && business > 0) {
					$(this).hide();
				} else {
					$(this).show();
				}
			});
			$('[name=work_order]').trigger('change.select2');
			$('[name=siteid] option').each(function() {
				if($(this).data('business') > 0 && $(this).data('business') != business && business > 0) {
					$(this).hide();
				} else {
					$(this).show();
				}
			});
			$('[name=siteid]').trigger('change.select2');
		} else if(this.name == 'siteid') {
			var business = $(this).find('option:selected').data('business');
			if(business > 0) {
				$('[name=businessid]').val(business).trigger('change.select2');
			}
		}
	});
});

</script>
<style>
@media(max-width:991px) {
.hide-titles-mob {
	display:none;
}
.show-on-mob {
	text-align:left !important;
	display:inline-block;
}
.expand-mobile {
	width:90% !important;
	display:block !important;
}
.m-top-mbl {
	margin-top:20px;
}
}
@media(min-width:992px) {
.show-on-mob {
	display:none;
}
.form-inline .form-group {

    vertical-align: top;
}

.order-list {
	width:60%;
	margin-left:26%;
	padding:2px;
}
}
</style>
<?php $current_cat = (empty($_GET['category']) ? $cat_list[0] : $_GET['category']);
if ( isset($_GET['contactid']) && $_GET['contactid'] ) {
    $contactid = $_GET['contactid'];
}
?>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal myform22" role="form">
	<input type='hidden' name='whoareyou' value='<?php echo $_SESSION['contactid']; ?>'>
	<input name="order_id" id="" type="hidden" value="<?php if(isset($_GET['order_list'])) { echo $_GET['order_list']; } ?>" class="form-control" />
	<input name="tax_exemption_number" id="tax_exemption_number" type="hidden" class="form-control" />
	<input name="client_tax_exemption" id="client_tax_exemption" type="hidden" class="form-control" />
	<?php $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT purchase_order FROM field_config"));
	$value_config = ','.$get_field_config['purchase_order'].',';

	if (strpos($value_config, ','."Send Outbound Invoice".',') !== FALSE) { ?>
		<input name="send_invoice" value='1' type="hidden" class="form-control" /><?php
	} else { ?>
		<input name="send_invoice" value='0' type="hidden" class="form-control" /><?php
	}

	if (strpos($value_config, ','."Invoice Date".',') !== FALSE) { ?>
		<div class="form-group offset-top-20">
			<label for="first_name" class="col-sm-3 control-label text-right">Order Date:</label>
			<div class="col-sm-9">
				<input name="invoice_date" value="<?php echo date('Y-m-d'); ?>" type="text" class="datepicker form-control" style='width:162px;'></p>
			</div>
		</div><?php
	}

	if (strpos($value_config, ','."PO Name".',') !== FALSE) { ?>
		<div class="form-group offset-top-20">
			<label for="first_name" class="col-sm-3 control-label text-right">Name:</label>
			<div class="col-sm-9">
				<input name="name" value="" type="text" class="form-control"></p>
			</div>
		</div><?php
	} ?>

	<?php if (strpos($value_config, ','."Project".',') !== FALSE) { ?>
		<div class="form-group">
			<label for="site_name" class="col-sm-3 control-label"><?= PROJECT_NOUN ?>:</label>
			<div class="col-sm-9">
				<select id="project" name="project" data-placeholder="Choose <?= PROJECT_NOUN ?>..." class="chosen-select-deselect form-control" width="380">
					<option value=""></option><?php
					$result = mysqli_query($dbc, "SELECT * FROM `project` WHERE `deleted`=0 ORDER BY `project_name`");
					while($row = mysqli_fetch_assoc($result)) {
						echo "<option data-business='".$row['businessid']."' data-site='".$row['siteid']."' value='" . $row['projectid'] . "'>" . get_project_label($dbc,$row) . "</option>";
					} ?>
				</select>
			</div>
		</div>
	<?php } ?>

	<?php if (strpos($value_config, ','."Ticket".',') !== FALSE) { ?>
		<div class="form-group">
			<label for="site_name" class="col-sm-3 control-label"><?= TICKET_NOUN ?>:</label>
			<div class="col-sm-9">
				<select id="work_order" name="work_order" data-placeholder="Choose <?= TICKET_NOUN ?>..." class="chosen-select-deselect form-control" width="380">
					<option value=""></option><?php
					$result = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `deleted`=0 AND `status`!='Archive' ORDER BY `created_date` DESC");
					while($row = mysqli_fetch_assoc($result)) {
						echo "<option data-business='".$row['businessid']."' data-site='".$row['siteid']."' data-project='".$row['projectid']."' value='" . $row['ticketid'] . "'>" .get_ticket_label($dbc,$row) . "</option>";
					} ?>
				</select>
			</div>
		</div>
	<?php } ?>

	<?php if (strpos($value_config, ','."Business".',') !== FALSE) { ?>
		<div class="form-group">
			<label for="site_name" class="col-sm-3 control-label"><?= BUSINESS_CAT ?>:</label>
			<div class="col-sm-9">
				<select name="businessid" data-placeholder="Choose <?= BUSINESS_CAT ?>..." class="chosen-select-deselect form-control" width="380">
					<option value=""></option><?php
					foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `name`, `last_name`, `first_name` FROM `contacts` WHERE `category`='".BUSINESS_CAT."' AND `deleted`=0 AND `status` > 0")) as $row) {
						echo "<option value='" . $row['contactid'] . "'>".$row['full_name']."</option>";
					} ?>
				</select>
			</div>
		</div>
	<?php } ?>

	<?php if (strpos($value_config, ','."Site".',') !== FALSE) { ?>
		<div class="form-group">
			<label for="site_name" class="col-sm-3 control-label"><?= SITES_CAT ?>:</label>
			<div class="col-sm-9">
				<select name="siteid" data-placeholder="Choose <?= SITES_CAT ?>..." class="chosen-select-deselect form-control" width="380">
					<option value=""></option><?php
					foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `site_name`,`display_name`,`name`,`businessid` FROM `contacts` WHERE `category`='".SITES_CAT."' AND `deleted`=0 AND `status` > 0")) as $row) {
						echo "<option data-business='".$row['businessid']."' value='" . $row['contactid'] . "'>".$row['full_name']."</option>";
					} ?>
				</select>
			</div>
		</div>
	<?php } ?>

	<?php if (strpos($value_config, ','."Equipment".',') !== FALSE) {
        $equip_cats = explode(',',get_config($dbc, 'equipment_tabs'));
        if(count($equip_cats) > 1) { ?>
            <script>
            function filterEquipment(category) {
                $('[name=equipmentid] option').each(function() {
                    if(category == '' || $(this).data('category') == category) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
                $('[name=equipmentid]').trigger('change.select2');
            }
            </script>
            <div class="form-group">
                <label for="travel_task" class="col-sm-3 control-label"><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="This drop down menu displays the equipment categories, and filters the Equipment drop down to find the equipment."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Equipment Category:</label>
                <div class="col-sm-9">
                    <select onchange="filterEquipment(this.value);" data-placeholder="Select Equipment Category..." class="chosen-select-deselect form-control"><option />
                        <?php foreach($equip_cats as $equip_cat) { ?>
                            <option value='<?= $equip_cat ?>'><?= $equip_cat ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <?php } ?>
		<div class="form-group">
			<label for="travel_task" class="col-sm-3 control-label">Equipment:</label>
			<div class="col-sm-9">
				<select name="equipmentid" data-placeholder="Select Equipment..." class="chosen-select-deselect form-control"><option />
                    <?php $result = mysqli_query($dbc, "SELECT `equipmentid`, `category`, `make`, `model`, `unit_number` FROM `equipment` WHERE `deleted`=0");
					while($equip_row = $result->fetch_assoc()) { ?>
                        <option data-category="<?= $equip_row['category'] ?>" value="<?= $equip_row['equipmentid'] ?>"><?= (count($equip_cats) > 1 && $equip_row['category'] != '' ? $equip_row['category'].': ' : '').implode(' ',array_filter([$equip_row['make'],$equip_row['model'],$equip_row['unit_number']])) ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php } ?>

	<?php if (strpos($value_config, ','."Customer".',') !== FALSE) { ?>
		<script>
		function addVendor() {
			destroyInputs();
			var vendor = $('[name="contactid[]"]').last().closest('.form-group');
			var clone = vendor.clone();
			clone.find('select').val('');
			clone.find('img').show();
			vendor.after(clone);
			initInputs();
		}
		</script>
		<div class="form-group">
			<label for="travel_task" class="col-sm-3 control-label"><span class="popover-examples list-inline" style="margin:-5px 5px 0 0"><a data-toggle="tooltip" data-placement="top" title="This drop down menu displays all vendors assigned in the vendor sub tab of the contact tile."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?= VENDOR_TILE ?><span class="brand-color">*</span>:<br><em><span id="tax_exemption_fillup"></em></span></label>
			<div class="col-sm-8">
				<select id="customerid" name="contactid[]" data-placeholder="Select a Vendor..." class="chosen-select-deselect form-control" width="380">
					<option value=''></option><?php
					$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, name FROM contacts WHERE (category NOT IN (".STAFF_CATS.") AND category != 'Employee' AND IFNULL(`name`,'') != '') AND deleted=0"),MYSQLI_ASSOC));
					foreach($result as $vendor_contactid) {
						echo "<option ".($vendor_contactid == ($contactid ?: $vendor_orderlist) ? 'selected' : '')." value='".$vendor_contactid."'>".get_client($dbc, $vendor_contactid)."</option>";
					} ?>
				</select>
			</div>
			<div class="col-sm-1">
				<img class="inline-img cursor-hand" src="../img/remove.png" onclick="$(this).closest('.form-group').remove();" style="display:none;">
				<img class="inline-img cursor-hand" src="../img/icons/ROOK-add-icon.png" onclick="addVendor();">
			</div>
		</div>
	<?php } ?>

	<?php if (strpos($value_config, ','."Order Forms".',') !== FALSE) { ?>
		<script type="text/javascript">
		$(document).on('change','select[name="contactid[]"]', function() { filterOrderForms(); });
		function filterOrderForms() {
			$('#order_forms option').hide();
			$('select[name="contactid[]"]').each(function() {
				var vendorid = $(this).val();
				$('#order_forms option[data-vendorid='+vendorid+']').show();
			});
			$('#order_forms').trigger('change.select2');
		}
		</script>
		<div class="form-group">
			<label class="col-sm-3 control-label">Load Order Form:</label>
			<div class="col-sm-9">
				<select id="order_forms" name="order_forms" data-placeholder="Select an Order Form..." class="chosen-select-deselect form-control">
					<option></option>
					<?php $order_forms = mysqli_query($dbc, "SELECT `vpl_name`, `vendorid` FROM `vendor_price_list` WHERE `deleted` = 0 AND IFNULL(`vpl_name`,'') != '' AND `vendorid` > 0 GROUP BY CONCAT(`vendorid`,`vpl_name`) ORDER BY `vpl_name`");
					while($order_form = mysqli_fetch_assoc($order_forms)) {
						echo '<option data-vendorid="'.$order_form['vendorid'].'" value="'.$order_form['vpl_name'].'">'.$order_form['vpl_name'].'</option>';
					} ?>
				</select>
			</div>
		</div>
	<?php } ?>

	<input type="hidden" id="hiddenpricing" value="0" />
<?php if(!isset($_GET['order_list'])) { ?>
  <?php if (strpos($value_config, ','."Product Pricing".',') !== FALSE) { ?>
	<input type="hidden" id="hiddenpricing" value="1" />

  <div class="form-group">
	<label for="site_name" class="col-sm-3 control-label">Product Pricing<span class="brand-color">*</span>:</label>
	<div class="col-sm-9">
		<select data-placeholder="Choose Pricing..." id="productpricing" name="productpricing" class="chosen-select-deselect form-control" width="380">
			<option value="">Please Select</option><?php
			if (strpos($value_config, ','."Client Price".',') !== FALSE) { ?>
				<option value="client_price">Client Price</option><?php
			}
			if (strpos($value_config, ','."Admin Price".',') !== FALSE) { ?>
				<option value="admin_price">Admin Price</option><?php
			}
			if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { ?>
				<option value="commercial_price">Commercial Price</option><?php
			}
			if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { ?>
				<option value="wholesale_price">Wholesale Price</option><?php
			}
			if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { ?>
				<option value="final_retail_price">Final Retail Price</option><?php
			}
			if (strpos($value_config, ','."Preferred Price".',') !== FALSE) { ?>
				<option value="preferred_price">Preferred Price</option><?php
			}
			if (strpos($value_config, ','."Web Price".',') !== FALSE) { ?>
				<option value="web_price">Web Price</option><?php
			}
			if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) { ?>
				<option value="purchase_order_price">Purchase Order Price</option><?php
			}
			if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) { ?>
				<option value="sales_order_price"><?= SALES_ORDER_NOUN ?> Price</option><?php
			}
			if (strpos($value_config, ','."Drum Unit Cost".',') !== FALSE) { ?>
				<option value="drum_unit_cost">Drum Unit Cost</option><?php
			}
			if (strpos($value_config, ','."Drum Unit Price".',') !== FALSE) { ?>
				<option value="drum_unit_price">Drum Unit Price</option><?php
			}
			if (strpos($value_config, ','."Tote Unit Cost".',') !== FALSE) { ?>
				<option value="tote_unit_cost">Tote Unit Cost</option><?php
			}
			if (strpos($value_config, ','."Tote Unit Price".',') !== FALSE) { ?>
				<option value="tote_unit_price">Tote Unit Price</option><?php
			}
			if (strpos($value_config, ','."Average Cost".',') !== FALSE) { ?>
				<option value="average_cost" selected>Average Cost</option><?php
			}
			if (strpos($value_config, ','."USD Cost Per Price".',') !== FALSE) { ?>
				<option value="usd_cpu" selected>USD Cost Per Price</option><?php
			} ?>
		</select>
	</div>
  </div>
  <?php } } 		  ?>
   <!-- TAX -->
   <?php if(strpos($value_config, ',Tax 2,') === FALSE) { ?>
		<div class="form-group">
			<label for="site_name" class="col-sm-3 control-label">Select Tax:</label>
			<div class="col-sm-9">
				<div class="col-sm-6">
					<input name="select_tax" value="0" type="radio" class="" checked="checked" style="float:left; margin-right:10px; width:auto;" />
					<div>Standard Tax</div>
				</div>
				<div class="col-sm-6">
					<input name="select_tax" value="1" type="radio" class="" style="float:left; margin-right:10px; width:auto;" />
					<div>Enter Tax</div>
				</div>
			</div>
		</div>

		<?php if (strpos($value_config, ','."Tax".',') !== FALSE) { ?>
			<div id="default_tax">
				<?php $pos_tax_value	= get_config($dbc, 'purchase_order_tax');
				$pos_tax		= explode('*#*',$pos_tax_value);

				$total_count	= mb_substr_count($pos_tax_value,'*#*');
				$tax_rate		= 0;
				$tax_exe		= 0;

				for ($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
					$pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);
					$tax_rate += $pos_tax_name_rate[1]; ?>

					<div class="clearfix"></div>

					<div class="form-group">
						<label for="site_name" class="col-sm-3 control-label"><?php echo $pos_tax_name_rate[0];?> (%):<br><em>[<?php echo $pos_tax_name_rate[2];?>]</em></label>
						<div class="col-sm-9">
							<input name="pos_tax" value='<?php echo $pos_tax_name_rate[1];?>' type="text" class="form-control pos_tax" readonly />
						</div>
					</div><?php

					if ($pos_tax_name_rate[3] == 'Yes') {
						DEFINE('TAX_EXEMPTION', $pos_tax_name_rate[0]); ?>
						<input id="not_count_pos_tax" value='<?php echo $pos_tax_name_rate[1];?>' type="hidden" />
						<input id="not_count_pos_tax_number" value='<?php echo $pos_tax_name_rate[2];?>' type="hidden" /><?php
						$tax_exe = 1;
					}
				}

				if ($tax_exe == 0) { ?>
					<input id="yes_tax_exemption" value='0' type="hidden" /><?php
				} else { ?>
					<input id="yes_tax_exemption" value='1' type="hidden" /><?php
				}

				echo '<input type="hidden" name="tax_rate" id="tax_rate" value="'.$tax_rate.'" />'; ?>
			</div>
		<?php } ?>

		<div id="enter_tax">
			<label for="pos_tax2" class="col-sm-3 control-label">Tax (%)</label>
			<div class="col-sm-9">
				<input name="pos_tax2" type="text" class="form-control pos_tax" onkeyup="updateTax();"  /><br />
				<input type="hidden" name="tax_rate2" id="tax_rate2" />
			</div>
		</div>
   <?php } ?>
	<!-- /TAX -->
  <!-- **$$**$$**$$**$$**$$ **$$**$$**$$**$$**$$ **$$**$$**$$**$$**$$ ORDER LIST TABLE **$$**$$**$$**$$**$$ **$$**$$**$$**$$**$$ **$$**$$**$$**$$**$$  -->
  <?php
  $cross_software = '';
  $software_seller = '';
		if(isset($_GET['order_list'])) {
			if(isset($_GET['zen'])) {
				$software_seller = $_GET['software'];
				$dbcorzen = $dbczen;
				$cross_software = 'zen';
			} else {
				$dbcorzen = $dbc;
			}
			?>
		<input type='hidden' name='software_seller' value='<?php echo $software_seller; ?>'>
		<input type='hidden' name='software_url' value='<?php echo $software_url; ?>'>
		<div class='live-search-list2'>
		<div class="col-sm-9 col-sm-offset-3"><input type='text' name='x' class=' form-control live-search-box2' placeholder="Search Order List..." style='max-width:300px; margin-bottom:20px;'></div>
	  <div id="no-more-tables" class='list_dashboard'  <?php if(isset($_GET['order_id'])) { ?>style='display:none;'<?php } ?>>

	<?php
	$order_id = $_GET['order_list'];
	$get_driver = mysqli_fetch_assoc(mysqli_query($dbcorzen,"SELECT * FROM order_lists WHERE order_id='$order_id'"));
	$inventoryidorder = $get_driver['inventoryid'];
	//$custom_pricer = $get_driver['custom_pricing'];
	$tile = $get_driver['tile']; if($tile == 'VPL') { $tile = 'vendor_price_list'; } else { $tile = 'inventory'; }
	if($inventoryidorder !== '') {
	//$query_check_credentials = "SELECT * FROM ".$tile." WHERE inventoryid IN (" . $inventoryidorder . ") ORDER BY category, name, product_name";
	$query_check_credentials = "SELECT * FROM ".$tile." WHERE inventoryid IN (" . $inventoryidorder . ") ORDER BY IF(category RLIKE '^[a-z]', 1, 2), category, IF(name RLIKE '^[a-z]', 1, 2), name";
	$result = mysqli_query($dbcorzen, $query_check_credentials);
	$num_rows = mysqli_num_rows($result);
	if($num_rows > 0) {
		echo "<table class='table table-bordered order-list' >";
		echo "<tr class='hidden-xs hidden-sm dont-hide'>";
				if (strpos($value_config, ','."Category".',') !== FALSE) {
					echo '<th>Category</th>';
				}
				if (strpos($value_config, ','."Part#".',') !== FALSE) {
					echo '<th>Part #</th>';
				}
				if (strpos($value_config, ','."Name".',') !== FALSE) {
					echo '<th>Product</th>';
				}
				if (strpos($value_config, ','."Price".',') !== FALSE) {
					echo '<th>Price</th>';
				}
				if (strpos($value_config, ','."Quantity".',') !== FALSE) {
					echo '<th>Quantity</th>';
				}
			echo "</tr>";
	} else {
		if($tile =='Vendor_price_List') {
			echo "<h3 class ='list_dashboard'>No items have been added to this list. Click <a href='../Vendor Price List/inventory.php?order_list=".$_GET['order_list']."&category=Top' style='text-decoration:underline;'>here</a> to add items.</h3>";
		} else { echo "<h3 class ='list_dashboard'>No items have been added to this list. Click <a href='../Inventory/inventory.php?order_list=".$_GET['order_list']."&category=Top' style='text-decoration:underline;'>here</a> to add items.</h3>"; }
	}
	$ix = 0;
	while($row = mysqli_fetch_array( $result ))
	{
		echo "<tr>";
				echo '<input type="hidden" value="'.$row['inventoryid'].'" name="inventoryid_list[]">';
		   if (strpos($value_config, ','."Category".',') !== FALSE) {
					echo '<td data-title="Category">'.$row['category'].'</td>';
				}
				if (strpos($value_config, ','."Part#".',') !== FALSE) {
					echo '<td data-title="Part #">'.$row['part_no'].'</td>';
				}
				if (strpos($value_config, ','."Name".',') !== FALSE) {
					echo '<td data-title="Product">'.$row['name'].'</td>';
				}
				if (strpos($value_config, ','."Price".',') !== FALSE) {
					echo '<td data-title="Price">';
					?><input data-placeholder="Choose a Product..." name="price_list[]" id="price_dd_<?php echo $ix; ?>" value="<?php
					$custom_pricer = $get_driver['custom_pricing'];
					if($custom_pricer !== NULL && $custom_pricer !== '' && $custom_pricer !== 'po_price' && $custom_pricer !== 'preferred_price') {
						$custom_pricer = ($custom_pricer+100)/100;
						$custom_pricer = ($custom_pricer*$row['cdn_cpu']);
						echo number_format($custom_pricer,2);
					} elseif ( $custom_pricer === 'preferred_price' ) {
						echo number_format($row['preferred_price'], 2);
					} else {
						if($row['purchase_order_price'] !== '' && $row['purchase_order_price'] !== NULL) {
							echo number_format($row['purchase_order_price'],2); } else { echo "0"; }
					}
					?>" style="width:100% !important; min-width: 100px;" onkeyup="countPOSTotal(this);" type="text" class="expand-mobile form-control price" readonly="readonly" /><?php
					echo '</td>';
				}
				if (strpos($value_config, ','."Quantity".',') !== FALSE) {
					echo '<td data-title="Quantity">';
					?><input data-placeholder="Choose a Product..." name="quantity_list[]" id="qty_dd_<?php echo $ix; ?>" onkeyup="numericFilter(this); countPOSTotal(this);" value="0" style="width:100% !important;    min-width: 100px;" type="text" class="expand-mobile form-control quantity" /><?php
					echo '</td>';
				}
		echo "</tr>";
		$ix++;
	}

	echo '</table></div>';

  } else { echo "<h3 class ='list_dashboard'>No items have been added to this list. Click <a href='../Inventory/inventory.php?order_list=".$_GET['order_list']."&category=Top'>here</a> to add items.</h3>"; } echo ' </div>'; } ?>
 <input type='hidden' value='<?php echo $cross_software;?>' name='cross_software'>
<!-- **$$**$$**$$**$$**$$ **$$**$$**$$**$$**$$ **$$**$$**$$**$$**$$  END ORDER LIST TABLE **$$**$$**$$**$$**$$ **$$**$$**$$**$$**$$ **$$**$$**$$**$$**$$  -->
<div class="inventory-margin" style="<?php if ( isset ( $_GET['order_list'] ) ) { echo 'display:none;'; } ?>">
	<!-- INVENTORY -->
	<?php if (strpos($value_config, ','."Products".',') !== FALSE) { ?>
	<div class="form-group clearfix col-xs-12 col-sm-12">
		<h4>Inventory</h4>
	</div>
	<div class="form-group clearfix hide-titles-mob">
		<?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>
			<label class="col-sm-3  text-center" style="width:20%;">Category</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."Part#".',') !== FALSE) { ?>
			<label class="col-sm-3 text-center" style="width:20%;">Part#</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
			<label class="col-sm-3 text-center" style="position:relative;width:20%">Product</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."Price".',') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="position:relative;width:10%">Price</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."Quantity".',') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="position:relative;width:10%">Quantity</label>
		<?php } ?>
	</div>


  <div class="additional_position">
	<div class="clearfix"></div>
	<div class="form-group clearfix" id="services_0" width="100%">

		<?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
		<div class="col-sm-3 expand-mobile type"  style="width: 20%; display:inline-block; position:relative;" id="category_0">
			<select data-placeholder="Choose a Category..." id="category_dd_0" name="category[]" class="chosen-select-deselect  form-control category">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT DISTINCT category FROM inventory WHERE include_in_po != ''");
				while($row = mysqli_fetch_array($query)) {
					?><option id='<?php echo $row['category'];?>' value='<?php echo $row['category'];?>'><?php echo $row['category'];?></option><?php
				}
				?>
			</select>
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."Part#".',') !== FALSE) { ?>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Part #:</label>
		<div class="expand-mobile col-sm-3"  style="width: 20%;  display:inline-block; position:relative;" id="part_0">
		<select data-placeholder="Choose a Part#..." id="part_dd_0" name="part_no[]" class="chosen-select-deselect form-control part">
			<option value=""></option>
			<?php
			$query = mysqli_query($dbc,"SELECT inventoryid, part_no FROM inventory WHERE include_in_po != '' AND deleted=0");
			while($row = mysqli_fetch_array($query)) {
				?><option value='<?php echo $row['inventoryid'];?>'><?php echo $row['part_no'];?></option><?php
			}
			?>
		</select>
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Product:</label>
		<div class="col-sm-3 expand-mobile" id="product_0" style="width:20%; position:relative; display:inline-block;">
			<select data-placeholder="Choose a Product..." name="inventoryid[]" id="product_dd_0" class="chosen-select-deselect  form-control product" style="position:relative;">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT inventoryid, name FROM inventory WHERE deleted=0 AND include_in_po != ''");
				while($row = mysqli_fetch_array($query)) {
					?><option value='<?php echo $row['inventoryid'];?>'><?php echo $row['name'];?></option><?php
				}
				?>
			</select>
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."Price".',') !== FALSE) { ?>

		<div class="col-sm-1 expand-mobile" id="price_0" style="width:10%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Price:</label>
			<input data-placeholder="Choose a Product..." name="price[]" id="price_dd_0" value="0" style="width:100% !important;" onkeyup="countPOSTotal(this);" type="text" class="expand-mobile form-control price" />
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."Quantity".',') !== FALSE) { ?>

		<div class="col-sm-3 qt expand-mobile" id="qty_0" style="width:10%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
			<input data-placeholder="Choose a Product..." name="quantity[]" id="qty_dd_0" onkeyup="numericFilter(this); countPOSTotal(this);" value="0" style="width:100% !important;" type="text" class="expand-mobile form-control quantity" />
		</div>
		<?php } ?>
		<div class="col-sm-1 m-top-mbl" >
			<a href="#" onclick="seleteService(this,'services_','product_dd_'); return false;" id="deleteservices_0" class="btn brand-btn">Delete</a>
		</div>
	</div>

	</div>

	<div id="add_here_new_position"></div>

	<div class="col-sm-12  triple-gap-bottom">
		<button id="add_position_button" class="btn brand-btn mobile-block">Add</button>
	</div>
	<?php } ?>
		<!-- END INVENTORY -->

		<!-- Vendor Price List -->
	<?php if (strpos($value_config, ','."vplProducts".',') !== FALSE) { ?>
	<div class="form-group clearfix">
	<label class="col-sm-12"><h4>Vendor Price List</h4></label>
	</div>
	<div class="form-group clearfix hide-titles-mob">
		<?php if (strpos($value_config, ','."vplCategory".',') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="width:20%;">Category</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."vplPart#".',') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="width:20%;">Part#</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."vplName".',') !== FALSE) { ?>
			<label class="col-sm-3 text-center" style="position:relative;width:20%">Product</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."vplPrice".',') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="position:relative;width:10%">Price</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."vplQuantity".',') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="position:relative;width:10%">Quantity</label>
		<?php } ?>
	</div>


  <div class="additional_positionvpl">
	<div class="clearfix"></div>
	<div class="form-group clearfix" id="vplservices_0" width="100%">

		<?php if (strpos($value_config, ','."vplCategory".',') !== FALSE) { ?>
		<div class="col-sm-1 expand-mobile vpltype"  style="width:20%; display:inline-block; position:relative;" id="vplcategory_0">
			<label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
			<select data-placeholder="Choose a Category..." id="vplcategory_dd_0" name="vplcategory[]" class="chosen-select-deselect form-control vplcategory">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT DISTINCT category FROM vendor_price_list WHERE include_in_po != ''");
				while($row = mysqli_fetch_array($query)) {
					?><option id='<?php echo $row['category'];?>' value='<?php echo $row['category'];?>'><?php echo $row['category'];?></option><?php
				}
				?>
			</select>
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."vplPart#".',') !== FALSE) { ?>
		<div class="col-sm-1 expand-mobile"  style="width:20%; display:inline-block; position:relative;" id="vplpart_0">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Part #:</label>
		<select data-placeholder="Choose a Part#..." id="vplpart_dd_0" name="vplpart_no[]" class="chosen-select-deselect form-control vplpart">
			<option value=""></option>
			<?php
			$query = mysqli_query($dbc,"SELECT inventoryid, part_no FROM vendor_price_list WHERE deleted=0 AND include_in_po != ''");
			while($row = mysqli_fetch_array($query)) {
				?><option value='<?php echo $row['inventoryid'];?>'><?php echo $row['part_no'];?></option><?php
			}
			?>
		</select>
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."vplName".',') !== FALSE) { ?>
		<div class="col-sm-3 expand-mobile" id="vplproduct_0" style="width:20%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Product:</label>
			<select data-placeholder="Choose a Product..." name="vplinventoryid[]" id="vplproduct_dd_0" class="chosen-select-deselect form-control vplproduct" style="position:relative;">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT inventoryid, name FROM vendor_price_list WHERE deleted=0 AND include_in_po != ''");
				while($row = mysqli_fetch_array($query)) {
					?><option value='<?php echo $row['inventoryid'];?>'><?php echo $row['name'];?></option><?php
				}
				?>
			</select>
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."vplPrice".',') !== FALSE) { ?>
		<div class="col-sm-1 expand-mobile" id="vplprice_0" style="width:10%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Price:</label>
			<input data-placeholder="Choose a Product..." name="vplprice[]" id="vplprice_dd_0" value="0" style="" onkeyup="countPOSTotal(this);" type="text" class="form-control vplprice" />
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."vplQuantity".',') !== FALSE) { ?>
		<div class="col-sm-3 expand-mobile vplqt" id="vplqty_0" style="width:10%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
			<input data-placeholder="Choose a Product..." name="vplquantity[]" id="vplqty_dd_0" onkeyup="numericFilter(this); countPOSTotal(this);" value="0" style="" type="text" class="form-control vplquantity" />
		</div>
		<?php } ?>
		<div class="col-sm-1 m-top-mbl" >
			<a href="#" onclick="seleteService(this,'vplservices_','vplproduct_dd_'); return false;" id="vpldeleteservices_0" class="btn brand-btn">Delete</a>
		</div>
	</div>

	</div>

	<div id="add_here_new_positionvpl"></div>

	<div class="col-sm-12 triple-gap-bottom">
		<button id="add_position_buttonvpl" class="btn brand-btn mobile-block">Add</button>
	</div>
	<?php } ?>
		<!-- END VPL  -->

		<!-- Products -->
	<?php if (strpos($value_config, ','."prodProducts".',') !== FALSE) { ?>

	<div class="form-group clearfix">
	<label class="col-sm-12"><h4>Product(s)</h4></label>
	</div>
	  <div class="form-group clearfix hide-titles-mob">
		<?php if (strpos($value_config, ','."prodCategory".',') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="width:20%;">Category</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."prodProduct Type".',') !== FALSE) { ?><!--
			<label class="col-sm-1 text-center" style="width:15%;">Product Type</label>-->
		<?php } ?>
		<?php if (strpos($value_config, ','."prodHeading".',') !== FALSE) { ?>
			<label class="col-sm-3 text-center" style="position:relative;width:20%">Heading</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."Pricing by Line Item".',') !== FALSE) { ?>
			<label class="col-sm-2 text-center" style="position:relative;width:15%">Pricing</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."prodPrice".',') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="position:relative;width:10%">Price</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."prodQuantity".',') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="position:relative;width:10%">Quantity</label>
		<?php } ?>
	</div>

  <div class="additional_positionprod">
	<div class="clearfix"></div>
	<div class="form-group clearfix" id="prodservices_0" width="100%">

		<?php if (strpos($value_config, ','."prodCategory".',') !== FALSE) { ?>
		<div class="col-sm-1 expand-mobile prodtype"  style="width:20%; display:inline-block; position:relative;" id="prodcategory_0">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
			<select data-placeholder="Choose a Category..."  id="prodcategory_dd_0" name="prodcategory[]" class="chosen-select-deselect form-control prodcategory">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT DISTINCT category FROM products WHERE include_in_po != ''");
				while($row = mysqli_fetch_array($query)) {
					?><option id='<?php echo $row['category'];?>' value='<?php echo $row['category'];?>'><?php echo $row['category'];?></option><?php
				}
				?>
			</select>
		</div>
		<?php } ?>
		<?php if (strpos($value_config, ','."prodProduct Type".',') !== FALSE) { ?>
		<div class="col-sm-1 expand-mobile"  style="width:15%; display:inline-block; position:relative;" id="prodpart_0">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Product Type:</label>
		<select data-placeholder="Choose a Type..." id="prodpart_dd_0" name="prodpart_no[]" class="chosen-select-deselect form-control prodpart">
			<option value=""></option>
			<?php
			$query = mysqli_query($dbc,"SELECT productid, product_type FROM products WHERE deleted=0");
			while($row = mysqli_fetch_array($query)) {
				?><option value='<?php echo $row['productid'];?>'><?php echo $row['product_type'];?></option><?php
			}
			?>
		</select>
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."prodHeading".',') !== FALSE) { ?>
		<div class="col-sm-3 expand-mobile" id="prodproduct_0" style="width:20%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
			<select data-placeholder="Choose a Heading..." name="prodinventoryid[]" id="prodproduct_dd_0" class="chosen-select-deselect form-control prodproduct" style="position:relative;">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT productid, heading FROM products WHERE deleted=0 AND include_in_po != ''");
				while($row = mysqli_fetch_array($query)) {
					?><option value='<?php echo $row['productid'];?>'><?php echo $row['heading'];?></option><?php
				}
				?>
			</select>
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."Pricing by Line Item".',') !== FALSE) { ?>
		<div class="col-sm-3 expand-mobile" id="prodlineprice_0" style="width:15%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Pricing:</label>
			<select data-placeholder="Choose Pricing..." id="prodlineprice_dd_0" name="productlinepricing[]" class="chosen-select-deselect form-control prodlineprice" width="380">
				<option value="">Please Select</option>
				<?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { ?>
				<option value="client_price">Client Price</option>
				<?php } ?>
				<?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { ?>
				<option value="admin_price">Admin Price</option>
				<?php } ?>
				<?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { ?>
				<option value="commercial_price">Commercial Price</option>
				<?php } ?>
				<?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { ?>
				<option value="wholesale_price">Wholesale Price</option>
				<?php } ?>
				<?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { ?>
				<option value="final_retail_price">Final Retail Price</option>
				<?php } ?>
				<?php if (strpos($value_config, ','."Preferred Price".',') !== FALSE) { ?>
				<option value="preferred_price">Preferred Price</option>
				<?php } ?>
				<?php if (strpos($value_config, ','."Web Price".',') !== FALSE) { ?>
				<option value="web_price">Web Price</option>
				<?php } ?>
				<?php if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) { ?>
				<option value="purchase_order_price">Purchase Order Price</option>
				<?php } ?>
				<?php if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) { ?>
				<option value="sales_order_price"><?= SALES_ORDER_NOUN ?> Price</option>
				<?php } ?>
				<?php if (strpos($value_config, ','."Drum Unit Cost".',') !== FALSE) { ?>
				<option value="drum_unit_cost">Drum Unit Cost</option>
				<?php } ?>
				<?php if (strpos($value_config, ','."Drum Unit Price".',') !== FALSE) { ?>
				<option value="drum_unit_price">Drum Unit Price</option>
				<?php } ?>
				<?php if (strpos($value_config, ','."Tote Unit Cost".',') !== FALSE) { ?>
				<option value="tote_unit_cost">Tote Unit Cost</option>
				<?php } ?>
				<?php if (strpos($value_config, ','."Tote Unit Price".',') !== FALSE) { ?>
				<option value="tote_unit_price">Tote Unit Price</option>
				<?php } ?>
			</select>
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."prodPrice".',') !== FALSE) { ?>
		<div class="col-sm-1 expand-mobile" id="prodprice_0" style="width:10%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Price:</label>
			<input data-placeholder="Choose a Product..." name="prodprice[]" id="prodprice_dd_0" value="0" style="" onkeyup="countPOSTotal(this);" type="text" class="form-control prodprice" />
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."prodQuantity".',') !== FALSE) { ?>
		<div class="col-sm-3 expand-mobile prodqt" id="prodqty_0" style="width:10%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Quantity:</label>
			<input data-placeholder="Choose a Product..." name="prodquantity[]" id="prodqty_dd_0" onkeyup="numericFilter(this); countPOSTotal(this);" value="0" style="" type="text" class="form-control prodquantity" />
		</div>
		<?php } ?>

		<div class="col-sm-1 m-top-mbl" >
			<a href="#" onclick="seleteService(this,'prodservices_','prodproduct_dd_'); return false;" id="proddeleteservices_0" class="btn brand-btn">Delete</a>
		</div>
	</div>

	</div>

	<div id="add_here_new_positionprod"></div>

	<div class="col-sm-12  triple-gap-bottom">
		<button id="add_position_buttonprod" class="btn brand-btn mobile-block">Add</button>
	</div>
	<?php } ?>
		<!-- END Products -->

		<!-- Services -->
	<?php if (strpos($value_config, ','."servServices".',') !== FALSE) { ?>

	<div class="form-group clearfix">
	<label class="col-sm-12"><h4>Service(s)</h4></label>
	</div>
	  <div class="form-group clearfix hide-titles-mob">
		<?php if (strpos($value_config, ','."servCategory".',') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="width:20%;">Category</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."servService Type".',') !== FALSE) { ?>
			<!--<label class="col-sm-1 text-center" style="width:15%;">Service Type</label>-->
		<?php } ?>
		<?php if (strpos($value_config, ','."servHeading".',') !== FALSE) { ?>
			<label class="col-sm-3 text-center" style="position:relative;width:20%">Heading</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."servPrice".',') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="position:relative;width:10%">Price</label>
		<?php } ?>
		<?php if (strpos($value_config, ','."servQuantity".',') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="position:relative;width:10%">Quantity</label>
		<?php } ?>
	</div>


  <div class="additional_positionserv">
	<div class="clearfix"></div>
	<div class="form-group clearfix" id="servservices_0" width="100%">

		<?php if (strpos($value_config, ','."servCategory".',') !== FALSE) { ?>
		<div class="col-sm-1 servtype expand-mobile"  style="width:20%; display:inline-block; position:relative;" id="servcategory_0">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Category:</label>
			<select data-placeholder="Choose a Category..."  id="servcategory_dd_0" name="servcategory[]" class="chosen-select-deselect form-control servcategory">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT DISTINCT category FROM services WHERE include_in_po != ''");
				while($row = mysqli_fetch_array($query)) {
					?><option id='<?php echo $row['category'];?>' value='<?php echo $row['category'];?>'><?php echo $row['category'];?></option><?php
				}
				?>
			</select>
		</div>
		<?php } /* ?>

		<?php if (strpos($value_config, ','."servService Type".',') !== FALSE) { ?>
		<div class="col-sm-1"  style="width:15%; display:inline-block; position:relative;" id="servpart_0">
		<select data-placeholder="Choose a Type..." onchange="servselectProduct(this)"  id="servpart_dd_0" name="servpart_no[]" class="chosen-select-deselect form-control servpart">
			<option value=""></option>
			<?php
			$query = mysqli_query($dbc,"SELECT serviceid, service_type FROM services WHERE deleted=0");
			while($row = mysqli_fetch_array($query)) {
				?><option value='<?php echo $row['serviceid'];?>'><?php echo $row['service_type'];?></option><?php
			}
			?>
		</select>
		</div>
		<?php }  */?>

		<?php if (strpos($value_config, ','."servHeading".',') !== FALSE) { ?>
		<div class="col-sm-3 expand-mobile" id="servproduct_0" style="width:20%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Heading:</label>
			<select data-placeholder="Choose a Heading..." name="servinventoryid[]" id="servproduct_dd_0" class="chosen-select-deselect form-control servproduct" style="position:relative;">
				<option value=""></option>
				<?php
				$query = mysqli_query($dbc,"SELECT serviceid, heading FROM services WHERE deleted=0 AND include_in_po != ''");
				while($row = mysqli_fetch_array($query)) {
					?><option value='<?php echo $row['serviceid'];?>'><?php echo $row['heading'];?></option><?php
				}
				?>
			</select>
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."servPrice".',') !== FALSE) { ?>
		<div class="col-sm-1 expand-mobile" id="servprice_0" style="width:10%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Price:</label>
			<input data-placeholder="Choose a Product..." name="servprice[]" id="servprice_dd_0" value="0" style="" onkeyup="countPOSTotal(this);" type="text" class="form-control servprice" />
		</div>
		<?php } ?>

		<?php if (strpos($value_config, ','."servQuantity".',') !== FALSE) { ?>
		<label for="company_name expand-mobile" class="col-sm-4 show-on-mob control-label">Quantity:</label>
		<div class="col-sm-3 servqt expand-mobile" id="servqty_0" style="width:10%; position:relative; display:inline-block;">
			<input data-placeholder="Choose a Product..." name="servquantity[]" id="servqty_dd_0" onkeyup="numericFilter(this); countPOSTotal(this);" value="0" style="" type="text" class="form-control servquantity" />
		</div>
		<?php } ?>
		<div class="col-sm-1 m-top-mbl" >
			<a href="#" onclick="seleteService(this,'servservices_','servproduct_dd_'); return false;" id="servdeleteservices_0" class="btn brand-btn">Delete</a>
		</div>
	</div>

	</div>

	<div id="add_here_new_positionserv"></div>

	<div class="col-sm-12 triple-gap-bottom">
		<button id="add_position_buttonserv" class="btn brand-btn mobile-block">Add</button>
	</div>
	<?php } ?>
		<!-- END Services -->

	<?php if (strpos($value_config, ','."Misc Item".',') !== FALSE) { ?>
	<div class="form-group clearfix hide-titles-mob">
		<?php if(strpos($value_config,',miscQty,') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="position:relative;width:10%">Qty</label>
		<?php } ?>
		<label class="col-sm-3 text-center " style="position:relative;width:20%">Description</label>
		<?php if(strpos($value_config,',miscGrade,') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="position:relative;width:10%">Grade</label>
		<?php } ?>
		<?php if(strpos($value_config,',miscTag,') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="position:relative;width:10%">Tag</label>
		<?php } ?>
		<?php if(strpos($value_config,',miscDetail,') !== FALSE) { ?>
			<label class="col-sm-3 text-center" style="position:relative;width:20%">Detail</label>
		<?php } ?>
		<?php if(strpos($value_config,',miscUnitPrice,') !== FALSE) { ?>
			<label class="col-sm-1 text-center" style="position:relative;width:10%">Unit Price</label>
		<?php } ?>
		<label class="col-sm-1 text-center" style="position:relative;width:10%">Price</label>
	</div>

  <div class="additional_misc">
	<div class="clearfix"></div>
	<div class="form-group clearfix" id="misc_0" width="100%">
		<?php if(strpos($value_config,',miscQty,') !== FALSE) { ?>
			<div class="col-sm-1 expand-mobile" id="price_0" style="width:10%; position:relative; display:inline-block;">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Qty:</label>
				<input name="misc_qty[]" id="misc_qty_dd_0" value="0" style="" onchange="$(this).closest('.form-group').find('[name^=misc_price]').val($(this).closest('.form-group').find('[name^=misc_unit_price]').val() * this.value).keyup();" type="text" class="form-control " />
			</div>
		<?php } ?>

		<div class="col-sm-1 expand-mobile" id="miscproduct_0" style="width:20%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Description:</label>
			<input data-placeholder="Choose a Product..." id="misc_product_0" name="misc_product[]" type="text" class="form-control misc_product" />
		</div>

		<?php if(strpos($value_config,',miscGrade,') !== FALSE) { ?>
			<div class="col-sm-1 expand-mobile" id="price_0" style="width:10%; position:relative; display:inline-block;">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Grade:</label>
				<input name="misc_grade[]" id="misc_grade_dd_0" value="" style="" type="text" class="form-control " />
			</div>
		<?php } ?>

		<?php if(strpos($value_config,',miscTag,') !== FALSE) { ?>
			<div class="col-sm-1 expand-mobile" id="price_0" style="width:10%; position:relative; display:inline-block;">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Tag:</label>
				<input name="misc_tag[]" id="misc_tag_dd_0" value="" style="" type="text" class="form-control " />
			</div>
		<?php } ?>

		<?php if(strpos($value_config,',miscDetail,') !== FALSE) { ?>
			<div class="col-sm-3 expand-mobile" id="price_0" style="width:20%; position:relative; display:inline-block;">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Detail:</label>
				<input name="misc_detail[]" id="misc_detail_dd_0" value="" style="" type="text" class="form-control " />
			</div>
		<?php } ?>

		<?php if(strpos($value_config,',miscUnitPrice,') !== FALSE) { ?>
			<div class="col-sm-1 expand-mobile" id="price_0" style="width:10%; position:relative; display:inline-block;">
				<label for="company_name" class="col-sm-4 show-on-mob control-label">Unit Price:</label>
				<input name="misc_unit_price[]" id="misc_unit_price_dd_0" value="0" style="" onchange="$(this).closest('.form-group').find('[name^=misc_price]').val($(this).closest('.form-group').find('[name^=misc_qty]').val() * this.value).keyup();" type="text" class="form-control " />
			</div>
		<?php } ?>

		<div class="col-sm-1 expand-mobile" id="price_0" style="width:10%; position:relative; display:inline-block;">
		<label for="company_name" class="col-sm-4 show-on-mob control-label">Price:</label>
			<input data-placeholder="Choose a Product..." name="misc_price[]" id="misc_price_dd_0" value="0" style="" onkeyup="countPOSTotal(this);" type="text" class="form-control misc_price" />
		</div>

		<div class="col-sm-1 m-top-mbl" >
			<a href="#" onclick="seleteService(this,'misc_','misc_product_'); return false;" id="deletemisc_0" class="btn brand-btn">Delete</a>
		</div>
	</div>

	</div>

	<div id="add_here_new_misc"></div>

	<div class="col-sm-12 triple-gap-bottom">
		<button id="add_misc_button" class="btn brand-btn mobile-block">Add</button>
	</div>
	 <?php } ?>
	</div>

	<?php if (strpos($value_config, ','."Total Price".',') !== FALSE) { ?>
	  <div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Sub-Total<span class="brand-color">*</span>:</label>
		<div class="col-sm-9">
		  <input name="sub_total" id="sub_total" value=0 type="text" class="form-control" />
		</div>
	  </div>
	  <?php } ?>

	<?php if (strpos($value_config, ','."Discount".',') !== FALSE) { ?>
		<div class="form-group">
			<label for="site_name" class="col-sm-3 control-label">Discount Type:</label>
			<div class="col-sm-9">
			  <div class="radio">
				<label class="double-pad-right"><input type="radio" style="height:20px;width:20px;  margin-right:20px;" name="discount_type" value="%">%</label>
				<label class="pad-right"><input type="radio" style="height:20px;width:20px; margin-right:20px;" name="discount_type" value="$">$</label>
			  </div>
			</div>
		</div>
		<div class="form-group">
			<label for="site_name" class="col-sm-3 control-label">Discount Value:</label>
			<div class="col-sm-9">
			  <input name="discount_value" onkeyup="countPOSTotal(this);" id="discount_value" value=0 type="text" class="form-control" />
			</div>
		</div>

		<div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Total After Discount:</label>
			<div class="col-sm-9">
				<input name="total_after_discount" id="total_after_discount" value=0 type="text" class="form-control" />
			</div>
		</div>
	<?php } ?>

	<?php if (strpos($value_config, ','."Delivery".',') !== FALSE) { ?>
		<div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Delivery Option:</label>
			<div class="col-sm-9">
				<select data-placeholder="Choose a Type..." name="delivery_type" id="delivery_type" class="chosen-select-deselect form-control product" style="position:relative;">
					<option value=""></option>
					<option value="Pick-Up">Pick-Up</option>
					<option value="Company Delivery">Company Delivery</option>
					<option value="Drop Ship">Drop Ship</option>
					<option value="Shipping">Shipping</option>
				</select>
			</div>
		</div>

		<div class="form-group" style="display: none;" id="delivery_address">
		<label for="site_name" class="col-sm-3 control-label">Confirm delivery Address:</label>
			<div class="col-sm-9">
				<input name="delivery_address" id="delivery_address_fillup" type="text" class="form-control" />
			</div>
		</div>

	  <div class="form-group" style="display: none;" id="contractorid">
		<label for="travel_task" class="col-sm-3 control-label">Contractor:</label>
		<div class="col-sm-9">
		  <select name="contractorid" data-placeholder="Choose Contractor..." class="chosen-select-deselect form-control" width="380">
		  <option value=''></option>
			   <?php
				$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Contractor' AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
				foreach($query as $id) {
					$selected = '';
					//$selected = $id == $search_user ? 'selected = "selected"' : '';
					echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
				}
			  ?>
		  </select>
		</div>
	  </div>

		<div class="form-group" id="delivery_div">
		<label for="site_name" class="col-sm-3 control-label">Delivery/Shipping Amount:</label>
			<div class="col-sm-9">
				<input name="delivery" onkeyup="countPOSTotal(this);" id="delivery" value=0 type="text" class="form-control" />
			</div>
		</div>

	<?php } ?>

	<?php if (strpos($value_config, ','."Assembly".',') !== FALSE) { ?>
		<div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Assembly Amount:</label>
			<div class="col-sm-9">
				<input name="assembly" onkeyup="countPOSTotal(this);" id="assembly" value=0 type="text" class="form-control" />
			</div>
		</div>
	<?php } ?>

	<div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Total Before Tax:</label>
		<div class="col-sm-9">
			<input name="total_before_tax" id="total_before_tax" value=0 type="text" class="form-control" />
		</div>
	</div>
   <?php if(strpos($value_config, ',Tax 2,') !== FALSE) { ?>
		<div class="form-group">
			<label for="site_name" class="col-sm-3 control-label">Select Tax:</label>
			<div class="col-sm-9">
				<div class="col-sm-6">
					<input name="select_tax" value="0" type="radio" class="" checked="checked" style="float:left; margin-right:10px; width:auto;" />
					<div>Standard Tax</div>
				</div>
				<div class="col-sm-6">
					<input name="select_tax" value="1" type="radio" class="" style="float:left; margin-right:10px; width:auto;" />
					<div>Enter Tax</div>
				</div>
			</div>
		</div>

		<div id="default_tax">
			<?php $pos_tax_value	= get_config($dbc, 'purchase_order_tax');
			$pos_tax		= explode('*#*',$pos_tax_value);

			$total_count	= mb_substr_count($pos_tax_value,'*#*');
			$tax_rate		= 0;
			$tax_exe		= 0;

			for ($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
				$pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);
				$tax_rate += $pos_tax_name_rate[1]; ?>

				<div class="clearfix"></div>

				<div class="form-group">
					<label for="site_name" class="col-sm-3 control-label"><?php echo $pos_tax_name_rate[0];?> (%):<br><em>[<?php echo $pos_tax_name_rate[2];?>]</em></label>
					<div class="col-sm-9">
						<input name="pos_tax" value='<?php echo $pos_tax_name_rate[1];?>' type="text" class="form-control pos_tax" readonly />
					</div>
				</div><?php

				if ($pos_tax_name_rate[3] == 'Yes') {
					DEFINE('TAX_EXEMPTION', $pos_tax_name_rate[0]); ?>
					<input id="not_count_pos_tax" value='<?php echo $pos_tax_name_rate[1];?>' type="hidden" />
					<input id="not_count_pos_tax_number" value='<?php echo $pos_tax_name_rate[2];?>' type="hidden" /><?php
					$tax_exe = 1;
				}
			}

			if ($tax_exe == 0) { ?>
				<input id="yes_tax_exemption" value='0' type="hidden" /><?php
			} else { ?>
				<input id="yes_tax_exemption" value='1' type="hidden" /><?php
			}

			echo '<input type="hidden" name="tax_rate" id="tax_rate" value="'.$tax_rate.'" />'; ?>
		</div>

		<div id="enter_tax">
			<label for="pos_tax2" class="col-sm-3 control-label">Tax (%)</label>
			<div class="col-sm-9">
				<input name="pos_tax2" type="text" class="form-control pos_tax" onkeyup="updateTax();"  /><br />
				<input type="hidden" name="tax_rate2" id="tax_rate2" />
			</div>
		</div>
   <?php } ?>

	<div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Tax Price ($):</label>
		<div class="col-sm-9">
			<input name="tax_price" id="tax_price" readonly type="text" class="form-control" />
		</div>
	</div>

	  <div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Total Price<span class="brand-color">*</span>:</label>
		<div class="col-sm-9">
		  <input name="total_price" id="total_price" type="text" value=0 class="form-control" />
		</div>
	  </div>

	  <?php if (strpos($value_config, ','."Payment Type".',') !== FALSE) { ?>
	  <div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Payment Type<span class="brand-color">*</span>:</label>
		<div class="col-sm-9">
		  <select id="payment_type" name="payment_type" data-placeholder="Choose a Type..." class="chosen-select-deselect form-control" width="380">
			  <option value=""></option>
			  <?php
				//$tabs = get_config($dbc, 'po_invoice_payment_types');
				$tabs = get_config($dbc, 'invoice_payment_types');
				$each_tab = explode(',', $tabs);
				//if (is_array($each_tab) && count($each_tab) > 0) {
				foreach ($each_tab as $cat_tab) {
					if ($invtype == $cat_tab) {
						$selected = 'selected="selected"';
					} else {
						$selected = '';
					}
					echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
				}
				// } else {
				//	 echo "<option value='Pay Now'>Pay Now</option>";
				//	 echo "<option value='Net 30'>Net 30</option>";
				//	 echo "<option value='Net 60'>Net 60</option>";
				//	 echo "<option value='Net 90'>Net 90</option>";
				//	 echo "<option value='Net 120'>Net 120</option>";
				// }
			  ?>
		  </select>
		</div>
	  </div>
	  <?php } ?>

	  <div class="form-group" <?php if (strpos($value_config, ','."Deposit Paid".',') !== FALSE) { } else { echo 'style="display:none;"'; } ?>>
		<label for="site_name" class="col-sm-3 control-label">Deposit Paid ($)<span class="brand-color"></span>:</label>
		<div class="col-sm-9">
		  <input name="deposit_paid" onkeyup="countPOSTotal(this);" id="deposit_paid" value=0 type="text" class="form-control" />
		  <input type='hidden' name='updatedtotal' id='updatedtotal' class='form-control'>
		</div>
	  </div>

	  <?php if (strpos($value_config, ','."Comment".',') !== FALSE) { ?>
	  <div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Comment:</label>
		<div class="col-sm-9">
		  <textarea name="comment" rows="4" cols="50" class="form-control" ></textarea>
		</div>
	  </div>
	  <?php } ?>

	  <?php if (strpos($value_config, ','."Created/Sold By".',') !== FALSE) { ?>
	  <div class="form-group">
		<label for="site_name" class="col-sm-3 control-label">Created/Sold By:</label>
		<div class="col-sm-9">
		  <input name="created_by" readonly value="<?php echo decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?>" type="text" value=0 class="form-control" />
		</div>
	  </div>
	  <?php } ?>

	<?php if (strpos($value_config, ','."Ship Date".',') !== FALSE) { ?>
	<div class="form-group">
		<label for="first_name" class="col-sm-3 control-label text-right">Ship Date:</label>
		<div class="col-sm-9">
			<input name="ship_date" value="<?php echo date('Y-m-d'); ?>" type="text" class="datepicker form-control" style='width:162px;'></p>
		</div>
	</div>
	<?php } ?>

	<?php if (strpos($value_config, ','."Upload".',') !== FALSE) { ?>
	<div class="form-group">
		<label for="first_name" class="col-sm-3 control-label text-right">Supporting Document:<br><em>Receipts, Invoices, etc.</em></label>
		<div class="col-sm-9">
			<input name="upload" type="file" class="form-control">
		</div>
	</div>
	<?php } ?>

	<?php if (strpos($value_config, ','."Due Date".',') !== FALSE) { ?>
	<div class="form-group">
		<label for="first_name" class="col-sm-3 control-label text-right">Due Date:</label>
		<div class="col-sm-9">
			<input name="due_date" value="<?php echo date('Y-m-d'); ?>" type="text" class="datepicker form-control" style='width:162px;'></p>
		</div>
	</div>
	<?php }

	$software_url = $_SERVER['SERVER_NAME'];
	if(($software_url == 'www.washtechsoftware.com' || $software_url == 'washtech.freshfocuscrm.com' || $software_url == 'localhost') && isset($_GET['order_list'])) { ?>
	<div class="form-group">
		<label for="first_name" class="col-sm-3 control-label text-right">Convert to Spreadsheet:</label>
		<div class="col-sm-9">
			<input name="spreadsheet" value="Yes" type="checkbox" checked class="" style="width:20px; height:20px;">
		</div>
	</div>
	<?php } ?>
	<input type='hidden' name='company_software_name' value='<?PHP echo COMPANY_SOFTWARE_NAME; ?>'>
	<div class="form-group">
		<p><span class="empire-red"><em>Required Fields *</em></span></p>
	</div>

	  <div class="form-group">
		<button type="submit" name="submit_pos" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
	  </div>

</form>
