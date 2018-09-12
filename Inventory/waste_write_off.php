<?php
/*
 * Waste / Write-Off
 * This creates an Invoice to self about the consumable waste items or write-off items from Inventory
 * Takes the average cost into account instead of different price types
 */
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(1);
$strict_view = strictview_visible_function($dbc, 'inventory');
$tile_security = get_security($dbc, 'inventory');
if($strict_view > 0) {
    $tile_security['edit'] = 0;
    $tile_security['config'] = 0;
}

// Get software name
$rookconnect = get_software_name();
switch($rookconnect) {
	case 'sea':
		$company_software_name = 'Smart Energy Alternates';
	case 'washtech':
		$company_software_name = 'Washtech';
	case 'highland':
		$company_software_name = 'Highland Projects';
	case 'breakthebarrier':
		$company_software_name = 'Break The Barrier Innovation';
	case 'beirut':
		$company_software_name = 'Beirut Street Food';
	default:
		$company_software_name = 'ROOK Connect';
}

if ( $_SERVER['REQUEST_METHOD'] == 'POST' ) {
    $invoice_date = $_POST['invoice_date'];

    if($invoice_date == '') {
        $invoice_date = date('Y-m-d');
    }

    $productpricing	= $_POST['productpricing'];
    $sub_total		= $_POST['sub_total'];
    $delivery		= filter_var($_POST['delivery'],FILTER_SANITIZE_STRING);
	$delivery		= ( !empty ($delivery) || $delivery != NULL ) ? $delivery : '0.00';
    $pdf_tax		= '';
	$gst_total		= 0;
	$pst_total		= 0;
    $total_price	= $_POST['total_price'];
	$status			= 'Completed';


    $pdf_product	= '';
    $created_by		= $_SESSION['contactid'];
    $comment		= filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);
    $ship_date		= $_POST['ship_date'];
	$due_date		= $_POST['due_date'];

	$row			= mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `businessid` FROM `contacts` WHERE `contactid`='$created_by'" ) );
	$contactid		= $row['businessid'];
	$contactid		= ( empty($contactid) || $contactid==0 ) ? $created_by : $contactid;

	$query_insert_invoice = "INSERT INTO `point_of_sell` (`invoice_date`, `contactid`, `productpricing`, `sub_total`, `delivery`, `total_price`, `payment_type`, `created_by`, `comment`, `status`, `gst`, `pst`, `updatedtotal`, `edit_id`) VALUES ('$invoice_date', '$contactid', '$productpricing', '$sub_total', '$delivery', '$total_price', 'Waste / Write-Off', '$created_by', '$comment', '$status', '$gst_total', '$pst_total', '$updatedtotal', '0')";
	$results_are_in = mysqli_query($dbc, $query_insert_invoice);

	$posid = mysqli_insert_id($dbc);

	// ADD Column in Table for PDF //
	$col	= "SELECT `type_category` FROM `point_of_sell_product`";
	$result	= mysqli_query($dbc, $col);

	if (!$result){
		$colcreate = "ALTER TABLE `point_of_sell_product` ADD COLUMN `type_category` VARCHAR(555) NULL";
		$result = mysqli_query($dbc, $colcreate);
	}

	// Add Inventory items
	for($i=0; $i<count($_POST['inventoryid']); $i++) {
		$inventoryid = $_POST['inventoryid'][$i];
		$price = $_POST['price'][$i];
		$quantity = $_POST['quantity'][$i];

		if($inventoryid != '') {
			$query_insert_invoice = "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$inventoryid', '$quantity', '$price', 'inventory')";
			$results_are_in = mysqli_query($dbc, $query_insert_invoice);

			//Update Inventory table to reduce the quantity
			$query_update_inventory = "UPDATE `inventory` SET `quantity`=(`quantity`-'$quantity') WHERE `inventoryid`='$inventoryid'";
			$results_are_in = mysqli_query ( $dbc, $query_update_inventory );
      $before_change = '';
      $history = "Inventory with id $inventoryid is been Updated. <br />";
	    add_update_history($dbc, 'inventory_history', $history, '', $before_change);
		}
	}

	// Add Products
	for($i=0; $i<count($_POST['prodinventoryid']); $i++) {
		$inventoryid = $_POST['prodinventoryid'][$i];
		$price = $_POST['prodprice'][$i];
		$quantity = $_POST['prodquantity'][$i];

		if($inventoryid != '') {
			$query_insert_invoice = "INSERT INTO `point_of_sell_product` (`posid`, `inventoryid`, `quantity`, `price`, `type_category`) VALUES ('$posid', '$inventoryid', '$quantity', '$price', 'product')";
			$results_are_in = mysqli_query($dbc, $query_insert_invoice);
		}
	}

	// Add Miscellaneous items
	for($i=0; $i<count($_POST['misc_product']); $i++) {
		$misc_product	= filter_var($_POST['misc_product'][$i],FILTER_SANITIZE_STRING);
		$misc_price		= $_POST['misc_price'][$i];
		$misc_quantity	= $_POST['misc_quantity'][$i];

		if($misc_product != '') {
			$query_insert_invoice = "INSERT INTO `point_of_sell_product` (`posid`, `misc_product`, `price`, `quantity`, `type_category`) VALUES ('$posid', '$misc_product', '$misc_price', '$misc_quantity', 'misc product')";
			$results_are_in = mysqli_query($dbc, $query_insert_invoice);
		}
	}

	$get_editid_results = mysqli_query ( $dbc, "SELECT `posid`, `edit_id` FROM `point_of_sell` WHERE `posid`='$posid'" );
	while ( $row = mysqli_fetch_assoc ( $get_editid_results ) ) {
		$edit_id = $row[ 'edit_id' ];
	}

    include ('create_pos_pdf.php');
    $pos_design = get_config($dbc, 'pos_design');

    if($pos_design == 1) {
		echo create_pos1_pdf($dbc,$posid,$d_value,$_POST['comment'], $gst_total, $pst_total, $rookconnect, $edit_id);
    }

	if($pos_design == 2) {
        echo create_pos2_pdf($dbc,$posid,$d_value,$_POST['comment'], $gst_total, $pst_total, $rookconnect, $edit_id);
    }

	if($pos_design == 3) {
        echo create_pos3_pdf($dbc,$posid,$d_value,$_POST['comment'], $gst_total, $pst_total, $rookconnect, $edit_id, $company_software_name);
    }

	if ( $edit_id == '0' ) {
		$edited = '';
	} else {
		$edited = '_' . $edit_id;
	}

	if ( $rookconnect == 'washtech') {
        $to_email = 'troy@washtech.ca';
        //$to_email = 'jaylahiru@freshfocusmedia.com';
        $attachment = 'download/invoice_' . $posid . $edited . '.pdf';
        send_email('', $to_email, '', '', 'Washtech Invoice', 'Please see Attachment for Invoice', $attachment);
    }

    if($payment_type == 'Net 30 Days' || $payment_type == 'Net 30') {
        $send_invoice = $_POST['send_invoice'];
        if($send_invoice == 1) {
            $send_email = get_config($dbc, 'invoice_outbound_email');
            $arr_email=explode(",",$send_email);
            $attachment = '../Point of Sale/download/invoice_'.$posid.'.pdf';
            //send_email('', $arr_email, '', '', 'Outbound Invoice', 'Please see Attachment for Outbound Invoice', $attachment);
        }
    }

    echo '<script type="text/javascript"> window.location.replace("waste_write_off.php");
    window.open("../Point of Sale/download/invoice_'.$posid.$edited.'.pdf", "fullscreen=yes");
    </script>';
}
?>
<script type="text/javascript" src="inventory.js"></script>
<script type="text/javascript">

$(document).ready(function() {
    $("#form1").submit(function( event ) {
        var sub_total		= $("input[name=sub_total]").val();
        var total_price		= $("input[name=total_price]").val();
        var productpricing	= $("#productpricing").val();
        var payment_type	= $("#payment_type").val();

        if (sub_total == '0' || total_price == '0' || productpricing == '') {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });

    var count = 1;
    $('#deleteservices_0').hide();
	var prodcount = 1;
    $('#proddeleteservices_0').hide();

	/* Inventory */
	$('#add_position_button').on('click', function () {
        $('#deleteservices_0').show();

        var clone = $('.additional_position').clone();
        clone.find('.form-control').val('');
        clone.find('.price').val('0');
        clone.find('.quantity').val('0');

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

	/* Products */
	$('#add_position_buttonprod').on( 'click', function () {
        $('#proddeleteservices_0').show();

        var clone = $('.additional_positionprod').clone();
        clone.find('.form-control').val('');
        clone.find('.prodprice').val('0');
        clone.find('.prodquantity').val('0');

        clone.find('.prodproduct').attr('id', 'prodproduct_dd_'+prodcount);
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
		resetChosen($("#prodpart_dd_"+prodcount));

        prodcount++;
        return false;
    });

	/* Miscellaneous Products */
    var misccount = 1;
    $('#deletemisc_0').hide();
    $('#add_misc_button').on( 'click', function () {
        $('#deletemisc_0').show();

        var clone = $('.additional_misc').clone();
        clone.find('.form-control').val('');
        clone.find('.misc_price').val('0');
        clone.find('.misc_quantity').val('0');

        clone.find('.misc_product').attr('id', 'misc_product_'+misccount);
        clone.find('.misc_price').attr('id', 'misc_price_dd_'+misccount);
        clone.find('.misc_quantity').attr('id', 'misc_quantity_dd_'+misccount);

        clone.find('#misc_0').attr('id', 'misc_'+misccount);
        clone.find('#deletemisc_0').attr('id', 'deletemisc_'+misccount);
        $('#deletemisc_0').hide();

        clone.find('.form-control').trigger("change.select2");
        clone.removeClass("additional_misc");
        $('#add_here_new_misc').append(clone);
        misccount++;
        return false;
    });

});
$(document).on('change', 'select[name="productpricing"]', function() { selectProductPricing(this); });
$(document).on('change', 'select[name="category[]"]', function() { selectCategory(this); });
$(document).on('change', 'select[name="part_no[]"]', function() { selectProduct(this); });
$(document).on('change', 'select[name="inventoryid[]"]', function() { selectProduct(this); });
$(document).on('change', 'select[name="prodcategory[]"]', function() { prodselectCategory(this); });
$(document).on('change', 'select[name="prodpart_no[]"]', function() { prodselectProduct(this); });
$(document).on('change', 'select[name="prodinventoryid[]"]', function() { prodselectProduct(this); });


/* Select Product Cost */
function selectProductPricing(sel) {
    $(".category").val('');
    $(".category").trigger("change.select2");
    $(".product").val('');
    $(".product").trigger("change.select2");
    $(".part").val('');
    $(".part").trigger("change.select2");

	$(".prodcategory").val('');
    $(".prodcategory").trigger("change.select2");
    $(".prodproduct").val('');
    $(".prodproduct").trigger("change.select2");
    $(".prodpart").val('');
    $(".prodpart").trigger("change.select2");

    $('.price').val('0');
    $('.quantity').val('0');

	$('.prodprice').val('0');
    $('.prodquantity').val('0');

    $('#sub_total').val('0');
    $('#total_price').val('0');

    countPOSTotal(sel);
}


/* Count POS Total */
function countPOSTotal(sel) {
    var productPrice = $("#productpricing").val();

    var current_id = sel.id;
    var result = current_id.split('_');

    var qty = $("#qty_dd_"+result[2]).val();
    var pro = $("#product_dd_"+result[2]).val();

	var prodqty = $("#prodqty_dd_"+result[2]).val();
    var prodpro = $("#prodproduct_dd_"+result[2]).val();

    var numQty = $('.quantity').length;
	var prodnumQty = $('.prodquantity').length;
    var c = 0;
    var i;
    var price = 0;

    $.ajax({
        type: "GET",
        url: "inventory_ajax_all.php?fill=posGetCost&pro="+pro+"&qty="+qty+"&productPrice="+productPrice,
        dataType: "html",
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
				var qty = $("#misc_quantity_dd_"+m).val();
                if(price !== '' && price !== null) {
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

            $("#sub_total").val((c).toFixed(2));

			var delivery = $("#delivery").val();
			if(delivery == '' || typeof delivery === "undefined") {
				delivery = 0.00;
			}
            var shipping_total = parseFloat(delivery);

            var final_total = parseFloat( c + shipping_total );
			$('#total_price').val((final_total).toFixed(2));
        }
    });
}


/* Category Drop Down */

// Inventory Category
function selectCategory(sel) {
    var productPrice = $("#productpricing").val();
    if(productPrice == '') {
        alert('Error: Please select Product Cost First.');
		return false;
    }
    var end = sel.value;
    var typeId = sel.id;

    $.ajax({
        type: "GET",
        url: "../Point of Sale/pos_ajax_all.php?fill=posFromCategory&name="+end,
        dataType: "html",
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

// Products Category
function prodselectCategory(sel) {
    var productPrice = $("#prodproductpricing").val();
    if(productPrice == '') {
        alert('Error: Please select Product Cost First.');
		return false;
    }
    var end = sel.value;
    var typeId = sel.id;

    $.ajax({
        type: "GET",
        url: "../Point of Sale/pos_ajax_all.php?fill=posFromCategoryprod&name="+end,
        dataType: "html",
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


/* Product Drop Down */
// Inventory
function selectProduct(sel) {
    var end = sel.value;
    var typeId = sel.id;
    var arr = typeId.split('_');
    var productPrice = $("#productpricing").val();
    if(productPrice == '' || productPrice == null) {
        alert('Error: Please select Product Cost First.');
		return false;
	}
    var category = $("#category_dd_"+arr[2]).val();

    $.ajax({
        type: "GET",
        url: "../Point of Sale/pos_ajax_all.php?fill=posUpProductFromProduct&inventoryid="+end+"&productPrice="+productPrice+"&category="+category,
        dataType: "html",
        success: function(response){
            var result = response.split('**##**');

            $("#category_dd_"+arr[2]).html(result[0]);
            $("#part_dd_"+arr[2]).html(result[1]);
            $("#product_dd_"+arr[2]).html(result[2]);
            $("#category_dd_"+arr[2]).trigger("change.select2");
            $("#part_dd_"+arr[2]).trigger("change.select2");
            $("#product_dd_"+arr[2]).trigger("change.select2");

            $("#price_dd_"+arr[2]).val(result[3]);
            $("#qty_dd_"+arr[2]).val('0');
        }
    });
}

//Products
function prodselectProduct(sel) {
    var end = sel.value;
    var typeId = sel.id;
    var arr = typeId.split('_');
    var productPrice = $("#productpricing").val();
	if(productPrice == '' || productPrice == null) {
        alert('Error : Please select Pricing First.');
		return false;
	}
    var category = $("#prodcategory_dd_"+arr[2]).val();

    $.ajax({
        type: "GET",
        url: "../Point of Sale/pos_ajax_all.php?fill=posUpProductFromProductprod&inventoryid="+end+"&productPrice="+productPrice+"&category="+category,
        dataType: "html",
        success: function(response){
            var result = response.split('**##**');

            $("#prodcategory_dd_"+arr[2]).html(result[0]);
            $("#prodpart_dd_"+arr[2]).html(result[1]);
            $("#prodproduct_dd_"+arr[2]).html(result[2]);
            $("#prodcategory_dd_"+arr[2]).trigger("change.select2");
            $("#prodpart_dd_"+arr[2]).trigger("change.select2");
            $("#prodproduct_dd_"+arr[2]).trigger("change.select2");

            $("#prodprice_dd_"+arr[2]).val(result[3]);
            $("#prodqty_dd_"+arr[2]).val('0');
        }
    });
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
}
</style>
</head>

<body>
<?php
	include_once ('../navigation.php');
    checkAuthorised('inventory');
    $inventory_navigation_position = get_config($dbc, 'inventory_navigation_position');
?>
<div class="container" id="inventory_div">
    <div class="row">
        <div class="main-screen">
            <div class="tile-header standard-header">
                <?php include('../Inventory/tile_header.php'); ?>
            </div>

            <div class="tile-container" style="height: 100%;">
                <?php if($inventory_navigation_position == 'top') {
                    include('../Inventory/tile_nav_top.php');
                } ?>

                <?php if($inventory_navigation_position != 'top') { ?>
                    <div class="standard-collapsible tile-sidebar set-section-height">
                        <?php include('../Inventory/tile_sidebar.php'); ?>
                    </div>
                <?php } ?>

                <div class="scale-to-fill has-main-screen tile-content hide-titles-mob">
                    <div class="main-screen standard-body">
                        <div class="standard-body-title"><h3>Waste Write Off</h3></div>
                        <div class="standard-body-content pad-left pad-right">
                        <!--
                            History not developed
                            <div class="col-sm-12 gap-top"><a href='bill_of_material_history.php?type=log'><button type='button' class='btn brand-btn mobile-block' style='max-width:100px;'>History</button></a></div> -->
                            <?php
                    			$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(inventory_dashboard SEPARATOR ',') AS all_inventory FROM field_config_inventory WHERE accordion IS NULL AND inventory IS NULL"));
                    			$value_config = ','.$get_field_config['all_inventory'].',';
                    		?>

                            <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal myform22 double-gap-top" role="form"><?php
                    			$get_field_config	= mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `pos` FROM `field_config`" ) );
                    			$value_config		= ',' . $get_field_config['pos'] . ',';

                    			if ( strpos ( $value_config, ',Send Outbound Invoice,' ) !== FALSE ) { ?>
                    				<input name="send_invoice" value='1' type="hidden" class="form-control" /><?php
                    			} else { ?>
                    				<input name="send_invoice" value='0' type="hidden" class="form-control" /><?php
                    			} ?>

                    			<div class="form-group">
                    				<label for="first_name" class="col-sm-3 control-label text-right">Date:</label>
                    				<div class="col-sm-2">
                    					<input name="invoice_date" readonly value="<?php echo date('Y-m-d'); ?>" type="text" class="form-control"></p>
                    				</div>
                    				<div class="clearfix"></div>
                    			</div>

                    			<div class="form-group">
                    				<label for="site_name" class="col-sm-3 control-label"><span class="brand-color">*</span> Product Cost:</label>
                    				<div class="col-sm-9 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
                    					<select data-placeholder="Select Product Cost..." id="productpricing" name="productpricing" class="chosen-select-deselect form-control" width="380">
                    						<option value=""></option>
                    						<option value="average_cost">Average Cost</option>
                    						<option value="unit_cost">Unit Cost</option>
                    						<option value="cost">Cost</option>
                    						<option value="cdn_cpu">CDN Cost Per Unit</option>
                    						<option value="usd_cpu">USD Cost Per Unit</option>
                    						<option value="drum_unit_cost">Drum Unit Cost</option>
                    						<option value="tote_unit_cost">Tote Unit Cost</option>
                    					</select>
                    				</div>
                    			</div>

                    			<!-- Inventory -->
                    			<div class="inventory-margin"><?php
                    				if ( strpos ( $value_config, ',Products,' ) !== FALSE ) { ?>
                    					<div class="form-group clearfix"><label class="col-sm-12"><h4>Inventory</h4></label></div>
                    					<div class="form-group clearfix hide-titles-mob"><?php
                    						if ( strpos ( $value_config, ',Category,' ) !== FALSE ) { ?>
                    							<label class="col-sm-3 text-center">Category</label><?php
                    						}
                    						if ( strpos ( $value_config, ',Part#,' ) !== FALSE ) { ?>
                    							<label class="col-sm-2 text-center">Part#</label><?php
                    						}
                    						if ( strpos ( $value_config, ',Name,' ) !== FALSE ) { ?>
                    							<label class="col-sm-3 text-center">Product</label><?php
                    						} ?>
                    						<label class="col-sm-2 text-center">Cost</label><?php
                    						if ( strpos ( $value_config, ',Quantity,' ) !== FALSE ) { ?>
                    							<label class="col-sm-1 text-center">Quantity</label><?php
                    						} ?>
                    					</div>

                    					<div class="additional_position">
                    						<div class="clearfix"></div><?php

                    						if ( $count != 0 || $count != '' ) {
                    							for ( $i=0; $i<=$count; $i++ ) { ?>
                    								<div class="form-group clearfix" id="services_<?= $i; ?>"><?php
                    									if ( strpos ( $value_config, ',Category,' ) !== FALSE ) { ?>
                    										<label class="show-on-mob control-label">Tab:</label>
                    										<div class="col-sm-3 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile type" id="category_<?= $i; ?>">
                    											<select data-placeholder="Select a Category..." id="category_dd_<?= $i; ?>" name="category[]" class="chosen-select-deselect form-control category">
                    												<option value="<?= $category[$i]; ?>"><?= $category[$i]; ?></option><?php
                    												$query = mysqli_query ( $dbc, "SELECT DISTINCT `category` FROM `inventory` ORDER BY `category`" );
                    												while ( $row = mysqli_fetch_assoc ( $query ) ) { ?>
                    													<option id="<?= $row['category']; ?>" value="<?= $row['category']; ?>"><?= $row['category']; ?></option><?php
                    												} ?>
                    											</select>
                    										</div><?php
                    									}

                    									if ( strpos ( $value_config, ',Part#,' ) !== FALSE ) { ?>
                    										<label class="show-on-mob control-label">Part #:</label>
                    										<div class="expand-mobile <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> col-sm-2" id="part_<?= $i; ?>">
                    											<select data-placeholder="Select a Part#..." id="part_dd_<?= $i; ?>" name="part_no[]" class="chosen-select-deselect form-control part">
                    												<option value="<?= $inventoryid[$i]; ?>"><?= $part_no[$i]; ?></option><?php
                    												$query = mysqli_query ( $dbc, "SELECT `inventoryid`, `part_no` FROM `inventory` WHERE `deleted`=0 ORDER BY `part_no`" );
                    												while ( $row = mysqli_fetch_array ( $query ) ) { ?>
                    													<option value="<?= $row['inventoryid']; ?>"><?= $row['part_no']; ?></option><?php
                    												} ?>
                    											</select>
                    										</div><?php
                    									}

                    									if ( strpos ( $value_config, ',Name,' ) !== FALSE ) { ?>
                    										<label class="show-on-mob control-label">Product:</label>
                    										<div class="col-sm-3 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="product_<?= $i; ?>">
                    											<select data-placeholder="Select a Product..." name="inventoryid[]" id="product_dd_<?= $i; ?>" class="chosen-select-deselect  form-control product">
                    												<option value="<?= $inventoryid[$i]; ?>"><?= $name[$i]; ?></option><?php
                    												$query = mysqli_query($dbc,"SELECT `inventoryid`, `name` FROM `inventory` WHERE `deleted`=0 ORDER BY `name`");
                    												while ( $row = mysqli_fetch_array ( $query ) ) { ?>
                    													<option value="<?= $row['inventoryid']; ?>"><?= $row['name']; ?></option><?php
                    												} ?>
                    											</select>
                    										</div><?php
                    									} ?>


                    									<label class="show-on-mob control-label">Cost:</label>
                    									<div class="col-sm-2 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="price_<?= $i; ?>">
                    										<input data-placeholder="Select Cost..." name="price[]" id="price_dd_<?= $i; ?>" value="<?= ($price[$i]) ? $price[$i] : 0; ?>" onkeyup="countPOSTotal(this);" type="text" class="expand-mobile form-control price" />
                    									</div><?php

                    									if ( strpos ( $value_config, ',Quantity,' ) !== FALSE ) { ?>
                    										<label class="show-on-mob control-label">Quantity:</label>
                    										<div class="col-sm-1 qt expand-mobile" id="qty_<?= $i; ?>">
                    											<input data-placeholder="Choose a Product..." name="quantity[]" id="qty_dd_<?= $i; ?>" onkeyup="numericFilter(this); countPOSTotal(this);" value="<?= ($quantity[$i]) ? $quantity[$i] : 0; ?>" type="text" class="expand-mobile form-control quantity" />
                    										</div><?php
                    									} ?>

                    									<div class="col-sm-2 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> m-top-mbl" >
                    										<a href="#" onclick="seleteService(this,'services_','product_dd_'); return false;" id="deleteservices_<?= $i; ?>" class="btn brand-btn">Delete</a>
                    									</div>
                    								</div><!-- #services_0 --><?php
                    							} //for loop

                    						} else { ?>
                    							<div class="form-group clearfix" id="services_0" width="100%"><?php
                    								if ( strpos ( $value_config, ',Category,' ) !== FALSE ) { ?>
                    									<label class="show-on-mob control-label">Tab:</label>
                    									<div class="col-sm-3 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile type" id="category_0">
                    										<select data-placeholder="Select a Tab..." id="category_dd_0" name="category[]" class="chosen-select-deselect form-control category">
                    											<option value=""></option><?php
                    											$query = mysqli_query ( $dbc, "SELECT DISTINCT `category` FROM `inventory` ORDER BY `category`" );
                    											while ( $row = mysqli_fetch_array ( $query ) ) { ?>
                    												<option id="<?php echo $row['category']; ?>" value="<?= $row['category']; ?>"><?= $row['category']; ?></option><?php
                    											} ?>
                    										</select>
                    									</div><?php
                    								}

                    								if ( strpos ( $value_config, ',Part#,' ) !== FALSE ) { ?>
                    									<label class="show-on-mob control-label">Part #:</label>
                    									<div class="expand-mobile col-sm-2 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>" id="part_0">
                    										<select data-placeholder="Select a Part#..." id="part_dd_0" name="part_no[]" class="chosen-select-deselect form-control part">
                    											<option value=""></option><?php
                    											$query = mysqli_query ( $dbc, "SELECT `inventoryid`, `part_no` FROM `inventory` WHERE `deleted`=0 ORDER BY `part_no`" );
                    											while ( $row = mysqli_fetch_array ( $query ) ) { ?>
                    												<option value="<?= $row['inventoryid']; ?>"><?= $row['part_no']; ?></option><?php
                    											} ?>
                    										</select>
                    									</div><?php
                    								}

                    								if ( strpos ( $value_config, ',Name,' ) !== FALSE ) { ?>
                    									<label class="show-on-mob control-label">Product:</label>
                    									<div class="col-sm-3 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="product_0">
                    										<select data-placeholder="Select a Product..." name="inventoryid[]" id="product_dd_0" class="chosen-select-deselect form-control product">
                    											<option value=""></option><?php
                    											$query = mysqli_query($dbc,"SELECT `inventoryid`, `name` FROM `inventory` WHERE `deleted`=0 ORDER BY `name`");
                    											while ( $row = mysqli_fetch_array ( $query ) ) { ?>
                    												<option value="<?= $row['inventoryid']; ?>"><?= $row['name']; ?></option><?php
                    											} ?>
                    										</select>
                    									</div><?php
                    								} ?>

                    								<label for="company_name" class="show-on-mob control-label">Cost:</label>
                    								<div class="col-sm-2 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="price_0">
                    									<input data-placeholder="Select a Product..." name="price[]" id="price_dd_0" value="0" onkeyup="countPOSTotal(this);" type="text" class="expand-mobile form-control price" />
                    								</div><?php

                    								if ( strpos ( $value_config, ',Quantity,' ) !== FALSE ) { ?>
                    									<label class="show-on-mob control-label">Quantity:</label>
                    									<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> qt expand-mobile" id="qty_0">
                    										<input data-placeholder="Select a Product..." name="quantity[]" id="qty_dd_0" onkeyup="numericFilter(this); countPOSTotal(this);" value="0" type="text" class="expand-mobile form-control quantity" />
                    									</div><?php
                    								} ?>

                    								<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> m-top-mbl" >
                    									<a href="#" onclick="seleteService(this,'services_','product_dd_'); return false;" id="deleteservices_0" class="btn brand-btn">Delete</a>
                    								</div>
                    							</div><!-- else --><?php
                    						} ?>

                    					</div><!-- .additional_position -->

                    					<div id="add_here_new_position"></div>

                                        <?php if($tile_security['edit'] > 0) { ?>
                        					<div class="col-sm-12  triple-gap-bottom"><button id="add_position_button" class="btn brand-btn mobile-block">Add</button></div>
                                        <?php }
                    				} ?>
                    				<!-- .Inventory -->

                    				<!-- Products --><?php
                    				if ( strpos ( $value_config, ',prodProducts,' ) !== FALSE ) { ?>
                    					<div class="form-group clearfix"><label class="col-sm-1 text-center"><h4>Product(s)</h4></label></div>
                    					<div class="form-group clearfix hide-titles-mob"><?php
                    						if ( strpos ( $value_config, ',prodCategory,' ) !== FALSE ) { ?>
                    							<label class="col-sm-3 text-center">Tab</label><?php
                    						}
                    						if ( strpos ( $value_config, ',prodProduct Type,' ) !== FALSE ) { ?>
                    							<label class="col-sm-3 text-center">Product Type</label><?php
                    						}
                    						if ( strpos ( $value_config, ',prodHeading,' ) !== FALSE ) { ?>
                    							<label class="col-sm-2 text-center">Heading</label><?php
                    						}
                    						if ( strpos ( $value_config, ',prodPrice,' ) !== FALSE ) { ?>
                    							<label class="col-sm-2 text-center">Cost</label><?php
                    						}
                    						if ( strpos ( $value_config, ',prodQuantity,' ) !== FALSE ) { ?>
                    							<label class="col-sm-1 text-center">Quantity</label><?php
                    						} ?>
                    					</div>

                    					<div class="additional_positionprod">
                    						<div class="clearfix"></div>
                    						<div class="form-group clearfix" id="prodservices_0"><?php
                    							if ( strpos ( $value_config, ',prodCategory,' ) !== FALSE ) { ?>
                    								<label class="show-on-mob control-label">Tab:</label>
                    								<div class="col-sm-3 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile prodtype" id="prodcategory_0">
                    									<select data-placeholder="Select a Category..." id="prodcategory_dd_0" name="prodcategory[]" class="chosen-select-deselect form-control prodcategory">
                    										<option value=""></option><?php
                    										$query = mysqli_query ( $dbc, "SELECT DISTINCT `category` FROM `products` ORDER BY `category`" );
                    										while($row = mysqli_fetch_array($query)) {
                    											?><option id='<?php echo $row['category'];?>' value='<?php echo $row['category'];?>'><?php echo $row['category'];?></option><?php
                    										} ?>
                    									</select>
                    								</div><?php
                    							}

                    							if ( strpos ( $value_config, ',prodProduct Type,' ) !== FALSE ) { ?>
                    								<label class="show-on-mob control-label">Product Tab:</label>
                    								<div class="col-sm-3 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="prodpart_0">
                    									<select data-placeholder="Select a Tab..." id="prodpart_dd_0" name="prodpart_no[]" class="chosen-select-deselect form-control prodpart">
                    										<option value=""></option><?php
                    										$query = mysqli_query ( $dbc, "SELECT `productid`, `product_type` FROM `products` WHERE `deleted`=0 ORDER BY `product_type`" );
                    										while($row = mysqli_fetch_array($query)) {
                    											?><option value='<?php echo $row['productid'];?>'><?php echo $row['product_type'];?></option><?php
                    										}
                    										?>
                    									</select>
                    								</div><?php
                    							}

                    							if ( strpos ( $value_config, ',prodHeading,' ) !== FALSE ) { ?>
                    								<label class="show-on-mob control-label">Heading:</label>
                    								<div class="col-sm-2 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="prodproduct_0">
                    									<select data-placeholder="Select a Heading..." name="prodinventoryid[]" id="prodproduct_dd_0" class="chosen-select-deselect form-control prodproduct">
                    										<option value=""></option><?php
                    										$query = mysqli_query ( $dbc, "SELECT `productid`, `heading` FROM `products` WHERE `deleted`=0 ORDER BY `heading`" );
                    										while($row = mysqli_fetch_array($query)) {
                    											?><option value='<?php echo $row['productid'];?>'><?php echo $row['heading'];?></option><?php
                    										} ?>
                    									</select>
                    								</div><?php
                    							}

                    							if ( strpos ( $value_config, ',prodPrice,' ) !== FALSE ) { ?>
                    								<label class="col-sm-4 show-on-mob control-label">Cost:</label>
                    								<div class="col-sm-2 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="prodprice_0">
                    									<input data-placeholder="Select a Product..." name="prodprice[]" id="prodprice_dd_0" value="0" onkeyup="countPOSTotal(this);" type="text" class="form-control prodprice" />
                    								</div><?php
                    							}

                    							if ( strpos ( $value_config, ',prodQuantity,' ) !== FALSE ) { ?>
                    								<label class="show-on-mob control-label">Quantity:</label>
                    								<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile prodqt" id="prodqty_0">
                    									<input data-placeholder="Select a Product..." name="prodquantity[]" id="prodqty_dd_0" onkeyup="numericFilter(this); countPOSTotal(this);" value="0" type="text" class="form-control prodquantity" />
                    								</div><?php
                    							} ?>

                    							<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> m-top-mbl" >
                    								<a href="#" onclick="seleteService(this,'prodservices_','prodproduct_dd_'); return false;" id="proddeleteservices_0" class="btn brand-btn">Delete</a>
                    							</div>

                    						</div><!-- #prodservices_0 -->
                    					</div><!-- .additional_positionprod -->

                    					<div id="add_here_new_positionprod"></div>

                                        <?php if($tile_security['edit'] > 0) { ?>
                        					<div class="col-sm-12 triple-gap-bottom"><button id="add_position_buttonprod" class="btn brand-btn mobile-block">Add</button></div>
                                        <?php }
                    				} ?>
                    				<!-- .Products -->

                    				<!-- Miscellaneous Items --><?php
                    				if ( strpos ( $value_config, ',Misc Item,' ) !== FALSE ) { ?>
                    					<div class="form-group clearfix"><label class="text-center"><h4>Miscellaneous Products</h4></label></div>
                    					<div class="form-group clearfix hide-titles-mob">
                    						<label class="col-sm-3 text-center">Misc Product</label>
                    						<label class="col-sm-2 text-center">Cost</label>
                    						<label class="col-sm-1 text-center">Quantity</label>
                    					</div><?php

                    					if ( $count_misc != 0 || $count_misc != '' ) {
                    						for ( $i=0; $i<=$count_misc; $i++ ) { ?>
                    							<div class="additional_misc">
                    								<div class="clearfix"></div>
                    								<div class="form-group clearfix" id="misc_<?= $i; ?>" width="100%">
                    									<label class="show-on-mob control-label">Misc Product:</label>
                    									<div class="col-sm-3 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="miscproduct_<?= $i; ?>">
                    										<input data-placeholder="Select a Product..." id="misc_product_<?= $i; ?>" name="misc_product[]" type="text" class="form-control misc_product" value="<?= ( $misc_desc[$i] ) ? $misc_desc[$i] : 0; ?>" />
                    									</div>

                    									<label class="show-on-mob control-label">Cost:</label>
                    									<div class="col-sm-2 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="price_<?= $i; ?>">
                    										<input data-placeholder="Select a Product..." name="misc_price[]" id="misc_price_dd_<?= $i; ?>" value="<?= ( $misc_price[$i] ) ? $misc_price[$i] : 0; ?>" onkeyup="countPOSTotal(this);" type="text" class="form-control misc_price" />
                    									</div>

                    									<label for="company_name" class="show-on-mob control-label">Quantity:</label>
                    									<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="quantity_<?= $i; ?>">
                    										<input data-placeholder="Select Quantity..." name="misc_quantity[]" id="misc_quantity_dd_<?= $i; ?>" value="<?= ( $misc_quantity[$i] ) ? $misc_quantity[$i] : 0; ?>" onkeyup="numericFilter(this); countPOSTotal(this);" type="text" class="form-control misc_quantity" />
                    									</div>

                    									<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> m-top-mbl" >
                    										<a href="#" onclick="seleteService(this,'misc_','misc_product_'); return false;" id="deletemisc_<?= $i; ?>" class="btn brand-btn">Delete</a>
                    									</div>
                    								</div><!-- #misc_0 -->
                    							</div><!-- .additional_misc --><?php
                    						}

                    					} else { ?>
                    						<div class="additional_misc">
                    							<div class="clearfix"></div>
                    							<div class="form-group clearfix" id="misc_0">
                    								<label for="company_name" class="show-on-mob control-label">Misc Product:</label>
                    								<div class="col-sm-3 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="miscproduct_0">
                    									<input data-placeholder="Select a Product..." id="misc_product_0" name="misc_product[]" type="text" class="form-control misc_product" />
                    								</div>

                    								<label for="company_name" class="show-on-mob control-label">Price:</label>
                    								<div class="col-sm-2 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="price_0">
                    									<input data-placeholder="Select a Product..." name="misc_price[]" id="misc_price_dd_0" value="0" onkeyup="countPOSTotal(this);" type="text" class="form-control misc_price" />
                    								</div>

                    								<label for="company_name" class="show-on-mob control-label">Quantity:</label>
                    								<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> expand-mobile" id="quantity_0">
                    									<input data-placeholder="Select Quantity..." name="misc_quantity[]" id="misc_quantity_dd_0" value="0" onkeyup="numericFilter(this); countPOSTotal(this);" type="text" class="form-control misc_quantity" />
                    								</div>

                    								<div class="col-sm-1 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?> m-top-mbl" >
                    									<a href="#" onclick="seleteService(this,'misc_','misc_product_'); return false;" id="deletemisc_0" class="btn brand-btn">Delete</a>
                    								</div>
                    							</div>

                    						</div><!-- .additional_misc --><?php
                    					} ?>

                    					<div id="add_here_new_misc"></div>

                                        <?php if($tile_security['edit'] > 0) { ?>
                        					<div class="col-sm-12 triple-gap-bottom"><button id="add_misc_button" class="btn brand-btn mobile-block">Add</button></div>
                                        <?php }
                    				} ?>
                    			</div><!-- .inventory-margin -->
                    			<!-- .Inventory -->

                    			<div class="form-group">
                    				<label class="col-sm-3 control-label"><span class="brand-color">*</span> Sub-Total:</label>
                                    <div class="col-sm-9 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
                    					<input name="sub_total" id="sub_total" value="<?= ($sub_total) ? $sub_total : 0; ?>" type="text" class="form-control" />
                                    </div>
                    			</div>

                    			<div class="form-group" id="delivery_div">
                                    <label class="col-sm-3 control-label">Shipping Cost:</label>
                    				<div class="col-sm-9 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
                    					<input name="delivery" onkeyup="countPOSTotal(this);" id="delivery" value="<?= ($delivery) ? $delivery : 0; ?>" type="text" class="form-control" />
                    				</div>
                    			</div>

                    			<div class="form-group">
                    				<label class="col-sm-3 control-label"><span class="brand-color">*</span> Total Price:</label>
                    				<div class="col-sm-9 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
                    					<input name="total_price" id="total_price" type="text" class="form-control" />
                    				</div>
                    			</div>

                    			<div class="form-group">
                                    <label class="col-sm-3 control-label">Comment:</label>
                    				<div class="col-sm-9 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
                    					<textarea name="comment" rows="4" cols="50" class="form-control"><?= ($comment) ? $comment : ''; ?></textarea>
                    				</div>
                    			</div>

                    			<div class="form-group">
                    				<label class="col-sm-3 control-label">Created By:</label>
                    				<div class="col-sm-9 <?= !($tile_security['edit'] > 0) ? 'readonly-block' : '' ?>">
                    					<input name="created_by" readonly value="<?= ($created_by) ? $created_by : decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?>" type="text" value=0 class="form-control" />
                    				</div>
                    			</div>

                    			<input type='hidden' name='company_software_name' value='<?PHP echo COMPANY_SOFTWARE_NAME; ?>'>


                    			<div class="clearfix double-gap-top"></div>

                                <?php if($tile_security['edit'] > 0) { ?>
                        			<div class="form-group">
                        				<div class="col-sm-3"><span class="empire-red pull-right"><em>Required Fields *</em></span></div>
                                        <div class="col-sm-9"></div>
                                    </div>

                        			<div class="form-group">
                        				<div class="col-sm-6"></div>
                        				<div class="col-sm-6"><button type="submit" name="submit_pos" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button></div>
                                      </div>
                                <?php } ?>

                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
	</div><!-- .row -->
</div><!-- .container -->

<?php include ('../footer.php'); ?>
