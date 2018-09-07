<?php
error_reporting(0);
/*
Payment/Invoice Listing SEA
*/
include ('../include.php');
checkAuthorised('pos');
include_once('../tcpdf/tcpdf.php');

$get_invoice =	mysqli_query($dbc,"SELECT posid FROM  point_of_sell  WHERE `invoice_date` + INTERVAL 30 DAY < NOW() AND status!='Completed'");
$num_rows = mysqli_num_rows($get_invoice);
if($num_rows > 0) {
    while($row = mysqli_fetch_array( $get_invoice )) {
        $posid = $row['posid'];
    $before_change = capture_before_change($dbc, 'point_of_sell', 'status', 'posid', $posid);

		$query_update_project = "UPDATE `point_of_sell` SET status = 'Posted Past Due' WHERE `posid` = '$posid'";

    $history = capture_after_change('status', 'Posted Past Due');
    add_update_history($dbc, 'pos_history', $history, '', $before_change);

		$result_update_project = mysqli_query($dbc, $query_update_project);
    }
}

if (isset($_POST['submit_pos'])) {

        $contactid = $_POST['contactid'];
		$productpricing = $_POST['productpricing'];
        $sub_total = $_POST['sub_total'];
		$gst = $_POST['gst'];
		$total_price = $_POST['total_price'];
		$payment_type = $_POST['payment_type'];
        $status = 'Completed';
        if($payment_type == 'Net 30 Days') {
            $status = 'Posted';
        }
		$pdf_product = '';
        $created_by = $_SESSION['contactid'];
        $comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

        // Update Inventory
		for($i=0; $i<count($_POST['inventoryid']); $i++) {
			$inventoryid = $_POST['inventoryid'][$i];
            $price = $_POST['price'][$i];
            $quantity = $_POST['quantity'][$i];

            $result_update_in = mysqli_query($dbc, "UPDATE `inventory` SET `current_stock` = current_stock - ".$quantity." WHERE `inventoryid` = '$inventoryid'");

			// START - INSERT INTO REPORT TABLES
			$amount_paid = $quantity * $price;
			$invoice_date = date('Y-m-d');
			$query_in = mysqli_query($dbc, "INSERT INTO `report_balancesheet` (`invoice_date`, `inventoryid`, `quantity`, `amount_paid`) VALUES ('$invoice_date', '$inventoryid', '$quantity', '$amount_paid')");
			$query_in = mysqli_query($dbc, "INSERT INTO `report_product_movement` (`invoice_date`, `inventoryid`, `amount_out`) VALUES ('$invoice_date', '$inventoryid', '$quantity')");
			$query_in = mysqli_query($dbc, "INSERT INTO `report_sales` (`invoice_date`, `inventoryid`, `quantity`, `amount`) VALUES ('$invoice_date', '$inventoryid', '$quantity', '$amount_paid')");
			// FINISH - INSERT INTO REPORT TABLES
		}

        // Email for sotck empty
		$count_id = count($_POST['inventoryid']);
		for($i=0; $i<$count_id; $i++) {
			$iid = $_POST['inventoryid'];
			$get_inventory =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM inventory WHERE inventoryid='$iid[$i]'"));
			if($get_inventory['min_bin'] >= $get_inventory['current_stock']) {
                $minbin_email = $get_config[''];

				$to = get_config($dbc, 'minbin_email');
				$subject = 'Stock Reminder for '.$get_inventory['name'] .'';

				$headers .= "MIME-Version: 1.0\r\n";
				$headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

				$message = '<html><body>';
				$message .= '<b>This is a reminder that this product&#39;s current inventory is less than or equal to its min bin.</b><br/><br/>';
				$message .= 'Name : '.$get_inventory['name'] .'<br/>';
				$message .= 'Category : '.$get_inventory['category'].'<br/>';
				$message .= 'Current Inventory : '.$get_inventory['current_stock'].'<br/>';
				$message .= 'Min Bin : '.$get_inventory['min_bin'].'<br/>';
				$message .= '</body></html>';

				mail($to, $subject, $message, $headers);
			}
		}
        // Email for sotck empty

        $inventoryid = implode(',',$_POST['inventoryid']);
        $price = implode(',',$_POST['price']);
	    $quantity = implode(',',$_POST['quantity']);

		$invoice_date = date('Y-m-d');

		$query_insert_invoice = "INSERT INTO `point_of_sell` (`contactid`, `inventoryid`, `quantity`, `price`, `sub_total`, `gst`, `total_price`, `payment_type`, `invoice_date`, `created_by`, `comment`, `status`) VALUES ('$contactid', '$inventoryid', '$quantity', '$price', '$sub_total', '$gst','$total_price', '$payment_type', '$invoice_date', '$created_by', '$comment', '$status')";
 		$results_are_in = mysqli_query($dbc, $query_insert_invoice);
        $posid = mysqli_insert_id($dbc);
        $before_change = '';
        $history = "Point of Sale entry Added. <br />";
        add_update_history($dbc, 'pos_history', $history, '', $before_change);

        $customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT customer, first_name, last_name, phone, email, office_street, office_city, office_state, office_country, office_zip FROM customer WHERE contactid='$contactid'"));

    	// PDF

		class MYPDF extends TCPDF {

		//Page header
		public function Header() {
			// Logo
			$image_file = WEBSITE_URL.'/img/fresh-focus-logo-dark.png';
			$this->Image($image_file, 10, 10, 51, '', 'JPG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
			// Set font
			//$this->SetFont('helvetica', 'B', 20);
			// Title
			//$this->Cell(0, 15, '<< TCPDF Example 003 >>', 0, false, 'C', 0, '', 0, false, 'M', 'M');
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-25);
			// Set font
			//$this->SetFont('helvetica', 'I', 8);
			// Page number
			$footer_text = '<b>'.COMPANY_NAME.'</b>&nbsp;&nbsp;&nbsp;Address, Calgary, Alberta, Postal C, Phone: 403-111-1111'.'<br><br>Page '.$this->getAliasNumPage().'/'.$this->getAliasNbPages();

			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
		}
	}

// create new PDF document
	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	//$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();

	$html = '<center><div style="margin-top:10px; text-align:center;"><h1>Invoice</h1></div></center>
    Invoice No : '.$posid .'<br>
    Date : '.$invoice_date.'<br>
    Ship Date : '.$invoice_date.'<br><br>';

    $html .= '<table>
				<tr><td width="50%">Sold To:</td><td width="50%">Ship To:</td></tr>
				<tr><td>'.$customer['customer'].'</td><td style="text-align: right;">'.$customer['customer'].'</td></tr>
				<tr><td>'.decryptIt($customer['first_name']).' '.decryptIt($customer['last_name']).'</td><td style="text-align: right;"style="text-align: right;">'.decryptIt($customer['first_name']).' '.decryptIt($customer['last_name']).'</td></tr>
				<tr><td>'.$customer['office_street'].'</td><td style="text-align: right;">'.$customer['office_street'].'</td></tr>
				<tr><td>'.$customer['office_city'].', '.$customer['office_state'].'</td><td style="text-align: right;">'.$customer['office_city'].', '.$customer['office_state'].'</td></tr>
				<tr><td>'.$customer['office_zip'].', '.$customer['office_country'].'</td><td style="text-align: right;">'.$customer['office_zip'].', '.$customer['office_country'].'</td></tr>
			</table>';


	$html .= '<table style="padding:3px;" border="1px" class="table table-bordered">
	<tr style="padding:3px; text-align:center; background-color:lightgrey; color:black;" >
		<td width="25%">Item No.</td>
		<td width="5%">Unit</td>
		<td width="10%">Quantity</td>
        <td width="30%">Description</td>
        <td width="5%">Tax</td>
        <td width="15%">Unit Price</td>
        <td width="10%">Amount</td>
	</tr>';

    for($i=0; $i<count($_POST['inventoryid']); $i++) {
        $inventoryid = $_POST['inventoryid'][$i];
        $price = $_POST['price'][$i];
        $quantity = $_POST['quantity'][$i];

        $get_product = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT part_no, selling_units, name FROM inventory WHERE inventoryid='$inventoryid'"));
        $amount = $price*$quantity;

        $html .= '<tr>';
        $html .=  '<td>'.$get_product['part_no'].'</td>';
        $html .=  '<td>'.$get_product['selling_units'].'</td>';
        $html .=  '<td>'.$quantity.'</td>';
        $html .=  '<td>'.$get_product['name'].'</td>';
        $html .=  '<td>G5</td>';
        $html .=  '<td>'.$price.'</td>';
        $html .= '<td>'.$amount.'</td>';
        $html .= '</tr>';
    }

    $html .= '<tr>';
    $html .=  '<td>&nbsp;</td>';
    $html .=  '<td>&nbsp;</td>';
    $html .=  '<td>&nbsp;</td>';
    $html .=  '<td>G5 - GST 5%</td>';
    $html .=  '<td>&nbsp;</td>';
    $html .=  '<td>&nbsp;</td>';
    $html .= '<td>'.$gst.'</td>';
    $html .= '</tr>';

	$html .= '</table><br><br><br>';
    $html .= 'Total Amount : '.$total_price;

    $html .= '<br><br>Comment : '.$_POST['comment'];
    $html .= '<br><br>Sold By : '.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);

	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}

	$pdf->writeHTML($html, true, false, true, false, '');
	?><?php
	$pdf->Output('download/invoice_'.$posid.'.pdf', 'F');
	// PDF
	?>

    <script type="text/javascript" language="javascript">
    window.location.replace('point_of_sell.php');
    window.open('download/invoice_<?php echo $posid;?>.pdf', 'fullscreen=yes');
    $(".myform22")[0].reset();
    </script>

		<?php
	//header('Location: payment_invoice.php');
    mysqli_close($dbc); //Close the DB Connection
}


?>
<script src="<?php echo WEBSITE_URL;?>/js/jquery.cookie.js"></script>
<script type="text/javascript">

$(document).ready(function() {

    $("#form1").submit(function( event ) {
        var sub_total = $("input[name=sub_total]").val();
        var gst = $("input[name=gst]").val();
        var total_price = $("input[name=total_price]").val();
        var customer = $("#customer").val();
        var productpricing = $("#productpricing").val();
        var payment_type = $("#payment_type").val();

        if (sub_total == '0' || total_price == '0' || customer == '' || productpricing == '' || payment_type == '') {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });


    $('.all_part_no').hide();
    $('.all_category').hide();

    $('.price').attr('readonly', true);

    var count = 1;

    $('.hide_additional_position').hide();
    $('#add_position_button').on( 'click', function () {
     if ($('.hide_additional_position').is(":hidden")) {
        $('.hide_additional_position').show();
     } else {
        //$('.equipment').show();
        var clone = $('.additional_position').clone();
        clone.find('.form-control').val('');
        clone.find('.price').val('0');
        clone.find('.quantity').val('0');
        //clone.find('.product').attr('name', 'inventoryid_'+count+'[]');

        var all_cat1 = $(".all_category").html();
        clone.find(".category").html(all_cat1);
        clone.find(".category").trigger("change.select2");

        //clone.find(".category").val('');
        //clone.find(".category").trigger("change.select2");
        clone.find(".product").html('');
        clone.find(".product").trigger("change.select2");

        var all_part_no1 = $(".all_part_no").html();
        clone.find(".part").html(all_part_no1);
        clone.find(".part").trigger("change.select2");

        clone.find('.product').attr('id', 'product_dd_'+count);
        clone.find('.price').attr('id', 'price_dd_'+count);
        clone.find('.part').attr('id', 'part_dd_'+count);
        clone.find('.quantity').attr('id', 'qty_dd_'+count);
        clone.find('.category').attr('id', 'category_dd_'+count);
        clone.find('.form-control').trigger("change.select2");
        clone.removeClass("additional_position");
        $('#add_here_new_position').append(clone);
		resetChosen($("#category_dd_"+count));
		resetChosen($("#product_dd_"+count));
		resetChosen($("#part_dd_"+count));

        count++;
    }
        return false;
    });

});
$(document).on('change', 'select[name="status[]"]', function() { changePOSStatus(this); });
$(document).on('change', 'select[name="contactid"]', function() { changeClient(this); });
$(document).on('change', 'select[name="category[]"]', function() { selectCategory(this); });
$(document).on('change', 'select[name="part_no[]"]', function() { selectPart(this); });
$(document).on('change', 'select[name="inventoryid[]"]', function() { selectProduct(this); });
$(document).on('change', 'select[name="productpricing"]', function() { selectProductPricing(this); });

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

function changeClient(sel) {
	var clientid = sel.value;

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "pos_ajax_all.php?fill=POSclient&clientid="+clientid,
		dataType: "html",   //expect html to be returned
		success: function(response){
            if (response.indexOf("Preferred Price") >= 0) {
                var price_val = "preferred_price";
            } else {
                var price_val = "final_retail_price";
            }
			$("#productpricing").val(price_val);

            //$("#productpricing").val(response).attr("selected", "selected");
            $("#productpricing").trigger("change.select2");
		}
	});
}

function selectPart(sel) {
    var end = sel.value;
    var typeId = sel.id;

    var productPrice = $("#productpricing").val();
    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posUpProductFromPart&name="+end+"&productPrice="+productPrice,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var arr = typeId.split('_');
            var result = response.split('*#*');
            $("#product_dd_"+arr[2]).html(result[0]);
            $("#product_dd_"+arr[2]).trigger("change.select2");

            $("#category_dd_"+arr[2]).html(result[1]);
            $("#category_dd_"+arr[2]).trigger("change.select2");

            $("#price_dd_"+arr[2]).val(result[2]);
            $("#qty_dd_"+arr[2]).val('0');
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

    $('.price').val('0');
    $('.quantity').val('0');

    $('#sub_total').val('0');
    $('#gst').val('0.00');
    $('#total_price').val('0');

    var productPrice = $("#productpricing").val();
    if(productPrice == 'admin_price') {
        $('.price').attr('readonly', false);
    } else {
        $('.price').attr('readonly', true);
    }
}

function selectCategory(sel) {
    var end = sel.value;
    var typeId = sel.id;

    var productPrice = $("#productpricing").val();

    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=posUpProduct&name="+end+"&productPrice="+productPrice,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('**##**');

            var arr = typeId.split('_');
            $("#product_dd_"+arr[2]).html(result[0]);
            $("#product_dd_"+arr[2]).trigger("change.select2");
            $("#part_dd_"+arr[2]).html(result[1]);
            $("#part_dd_"+arr[2]).trigger("change.select2");

            $("#price_dd_"+arr[2]).val('0');
            $("#qty_dd_"+arr[2]).val('0');
        }
    });
}

function gstmultiply(subtotal) {
    var gst = subtotal*0.05;
    var total = parseFloat(subtotal) + parseFloat(gst);
    document.getElementById('total_price').value = total.toFixed(2);
    document.getElementById('gst').value = gst.toFixed(2);
}

function selectProduct(pro) {
    var proValue = pro.value;
    var proId = pro.id;
    var arr = proId.split('_');
    var productPrice = $("#productpricing").val();
    $.ajax({    //create an ajax request to load_page.php
        type: "GET",
        url: "pos_ajax_all.php?fill=updatePrice&name="+proValue+"&productPrice="+productPrice,
        dataType: "html",   //expect html to be returned
        success: function(response){
            var result = response.split('*#*');
            $("#part_dd_"+arr[2]).html(result[1]);
            $("#part_dd_"+arr[2]).trigger("change.select2");

            $("#price_dd_"+arr[2]).val(result[0]);
            $("#qty_dd_"+arr[2]).val('0');
            selectQuantity();
        }
    });
}

function selectQuantity() {
    var productPrice = $("#productpricing").val();

    var numQty = $('.quantity').length;
    var c = 0;
    var i;
    for(i=0; i<numQty; i++) {
        var price = $("#price_dd_"+i).val();
        var qty = $("#qty_dd_"+i).val();
        c += parseFloat(price*qty);
    }
    $("#sub_total").val((c).toFixed(2));
    gstmultiply($("#sub_total").val());
}
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised();
?>
<div class="container triple-pad-bottom">
    <div class="row">
		<h1 class="double-pad-bottom"><?= POS_ADVANCE_TILE ?> Dashboard
        <?php
        if(config_visible_function($dbc, 'pos') == 1) {
            echo '<a href="field_config_pos.php" class="mobile-block pull-right "><img style="width: 50px;" title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me"></a><br><br>';
        }
        ?>
        </h1>

        <a href='point_of_sell.php'><button type="button" class="btn brand-btn mobile-block active_tab" >Invoice</button></a>
        <?php if(vuaed_visible_function($dbc, 'contacts') == 1) { ?>
        <a href='add_point_of_sell.php'><button type="button" class="btn brand-btn mobile-block" >Sell</button></a>
        <?php } ?>

		<div class="tabs">
		    <ul id="myTab" class="tab-links nav nav-pills">
		        <li class="active"><a href="#tab1">Invoices</a></li>
		        <li><a href="#tab3">Sell</a></li>
			</ul>
		</div>

        <div class="tab-content">

            <div id="tab1" class="tab-pane active triple-gap-top">

				<h1 class="double-pad-bottom">Invoices</h1>
			    <div class="table-responsive">

		        <form name="invoice_table" method="post" action="point_of_sell.php" class="form-inline" role="form">

                    <center>
                    <div class="form-group">
                        <label for="site_name" class="col-sm-5 control-label">Search By Any:</label>
                        <div class="col-sm-6">
                                <?php if(isset($_POST['search_invoice_submit'])) { ?>
                                    <input type="text" name="search_invoice" value="<?php echo $_POST['search_invoice']?>" class="form-control">
                                <?php } else { ?>
                                    <input type="text" name="search_invoice" class="form-control">
                                <?php } ?>
                        </div>
                    </div>
                    &nbsp;
                        <button type="submit" name="search_invoice_submit" value="Search" class="btn brand-btn">Search</button>
                        <button type="submit" name="display_all_invoice" value="Display All" class="btn brand-btn">Display All</button>
                    </center>

                        <?php
                            if (strpos(CUSTOMER_PRIVILEGES,'AE') !== false) {
                            //	echo '<a href="add_inventory.php" class="btn brand-btn pull-right">Add Product</a>';
                            }
                        ?>
                    <?php
                    // Display Pager

                    $rowsPerPagee = ITEMS_PER_PAGE;
                    $pageNumm  = 1;

                    if(isset($_GET['pagee'])) {
                        $pageNumm = $_GET['pagee'];
                    }

                    $offsett = ($pageNumm - 1) * $rowsPerPagee;

                    $invoice_name = '';
                    if (isset($_POST['search_invoice_submit'])) {
                        $invoice_name = $_POST['search_invoice'];
                    }
                    if (isset($_POST['display_all_invoice'])) {
                        $invoice_name = '';
                    }

                    if($invoice_name != '') {
                        $query_check_credentialss = "SELECT inv.*, c.* FROM point_of_sell inv,  contacts c WHERE inv.contactid = c.contactid AND inv.deleted = 0 AND (c.name LIKE '%" . $invoice_name . "%' OR inv.total_price LIKE '%" . $invoice_name . "%' OR inv.payment_type LIKE '%" . $invoice_name . "%' OR inv.invoice_date LIKE '%" . $invoice_name . "%' OR inv.status LIKE '%" . $invoice_name . "%' OR inv.comment LIKE '%" . $invoice_name . "%') ORDER BY inv.posid DESC";
                    } else {
                        $query_check_credentialss = "SELECT * FROM point_of_sell WHERE deleted = 0 ORDER BY posid DESC";
                    }

                    // how many rows we have in database
                    $queryy = "SELECT COUNT(posid) AS numrows FROM point_of_sell";

                    if($invoice_name == '') {
                       // echo '<h1 class="single-pad-bottom">'.display_pagination($dbc, $queryy, $pageNumm, $rowsPerPagee).'</h1>';
                    }

                    $resultt = mysqli_query($dbc, $query_check_credentialss);

                    $num_rowss = mysqli_num_rows($resultt);
                    if($num_rowss > 0) {
                        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT pos_dashboard FROM field_config"));
                        $value_config = ','.$get_field_config['pos_dashboard'].',';

                        echo "<table class='table table-bordered'>";
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
                            if (strpos($value_config, ','."Invoice PDF".',') !== FALSE) {
                                echo '<th>Invoice PDF</th>';
                            }
                            if (strpos($value_config, ','."Comment".',') !== FALSE) {
                                echo '<th>Comment</th>';
                            }
                            if (strpos($value_config, ','."Status".',') !== FALSE) {
                                echo '<th>Status</th>';
                            }
                        echo "</tr>";
                    } else{
                        echo "<h2>No Record Found.</h2>";
                    }

                    while($roww = mysqli_fetch_array( $resultt ))
                    {
                        $style = '';
                        if($roww['status'] == 'Posted Past Due') {
                            $style = 'style = color:red;';
                        }

                        $contactid = $roww['contactid'];
                        echo "<tr ".$style.">";
                        $customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT name, first_name, last_name FROM contacts WHERE contactid='$contactid'"));

                        if (strpos($value_config, ','."Invoice #".',') !== FALSE) {
                            echo '<td>' . $roww['posid'] . '</td>';
                        }
                        if (strpos($value_config, ','."Invoice Date".',') !== FALSE) {
                            echo '<td>' . $roww['invoice_date'] . '</td>';
                        }
                        if (strpos($value_config, ','."Customer".',') !== FALSE) {
                            echo '<td>' . decryptIt($customer['name']) . '</td>';
                        }
                        if (strpos($value_config, ','."Total Price".',') !== FALSE) {
                            echo '<td>' . $roww['total_price'] . '</td>';
                        }
                        if (strpos($value_config, ','."Payment Type".',') !== FALSE) {
                            echo '<td>' . $roww['payment_type'] . '</td>';
                        }
                        if (strpos($value_config, ','."Invoice PDF".',') !== FALSE) {
                            echo '<td><a target="_blank" href="download/invoice_'.$roww['posid'].'.pdf">PDF <img src="'.WEBSITE_URL.'/img/pdf.png" title="PDF"></a></td>';
                        }
                        if (strpos($value_config, ','."Comment".',') !== FALSE) {
                            echo '<td>' . $roww['comment'] . '</td>';
                        }
                        if (strpos($value_config, ','."Status".',') !== FALSE) {
                            echo '<td>';
                            ?>
                            <select name="status[]" id="status_<?php echo $roww['posid']; ?>" class="chosen-select-deselect1 form-control" width="380">
                                <option value=""></option>
                                <option value="Posted" <?php if ($roww['status'] == "Posted") { echo " selected"; } ?> >Posted</option>
                                <option value="Posted Past Due" <?php if ($roww['status'] == "Posted Past Due") { echo " selected"; } ?> >Posted Past Due</option>
                                <option value="Completed" <?php if ($roww['status'] == "Completed") { echo " selected"; } ?> >Completed</option>
                            </select>
                        <?php
                            echo '</td>';
                            }
                        echo "</tr>";
                    }

                    echo '</table></div>';

                    if($invoice_name == '') {
                        //echo display_pagination($dbc, $queryy, $pageNumm, $rowsPerPagee);
                    }

                    ?>
                </form>

			</div>

			<div id="tab3" class="tab-pane triple-gap-top">
                <?php if(empty($_GET['action'])) { ?>
                <h1 class="triple-pad-bottom">Sell</h1>

                <form id="form1" name="form1" method="post" action="point_of_sell.php" enctype="multipart/form-data" class="form-horizontal myform22" role="form">
                <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT pos FROM field_config"));
                $value_config = ','.$get_field_config['pos'].',';
                ?>

                <?php if (strpos($value_config, ','."Customer".',') !== FALSE) { ?>
                  <div class="form-group" id="customerg">
                    <label for="travel_task" class="col-sm-4 control-label" id="customer_12">Customer<span class="brand-color">*</span>:</label>
                    <div class="col-sm-8">
                      <select id="customer" name="contactid" data-placeholder="Choose Customer..." class="chosen-select-deselect form-control" width="380">
                      <option value=''>Choose Customer...</option>
						  <?php
							$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Customer' AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
							foreach($query as $id) {
								$selected = '';
								//$selected = strpos($assign_staff, ','.$id.',') !== false ? 'selected = "selected"' : '';
								echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id,'name').'</option>';
							}
						  ?>

                      </select>
                    </div>
                  </div>
                  <?php } ?>

                  <?php if (strpos($value_config, ','."Product Pricing".',') !== FALSE) { ?>
                  <div class="form-group">
                    <label for="site_name" class="col-sm-4 control-label">Product Pricing<span class="brand-color">*</span>:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Choose Pricing..." id="productpricing" name="productpricing" class="chosen-select-deselect form-control" width="380">
                            <option value="final_retail_price">Final Retail Price</option>
                            <option value="preferred_price">Preferred Price</option>
                            <option value="web_price">Web Price</option>
                        </select>
                    </div>
                  </div>
                  <?php } ?>

                  <div class="form-group all_part_no">
                        <?php
                        $query = mysqli_query($dbc,"SELECT inventoryid, part_no FROM inventory WHERE deleted=0");
                        echo '<option value="">Please Select</option>';
                        while($row = mysqli_fetch_array($query)) {
                            ?><option value='<?php echo $row['inventoryid'];?>'><?php echo $row['part_no'];?></option><?php
                        }
                        ?>
                  </div>

                  <div class="form-group all_category">
                        <?php
                        $query = mysqli_query($dbc,"SELECT DISTINCT category FROM inventory");
                        echo '<option value="">Please Select</option>';
                        while($row = mysqli_fetch_array($query)) {
                            ?><option value='<?php echo $row['category'];?>'><?php echo $row['category'];?></option><?php
                        }
                        ?>
                  </div>

                  <?php if (strpos($value_config, ','."Products".',') !== FALSE) { ?>
                    <div class="form-group clearfix">
                                <label class="col-sm-1 col-sm-offset-4 text-center" style="width:10%;">Category</label>
                                <label class="col-sm-1 text-center" style="width:15%;">Part#</label>
                                <label class="col-sm-3 text-center" style="position:relative;width:20%">Product</label>
                                <label class="col-sm-1 text-center" style="position:relative;width:7%">Price</label>
                                <label class="col-sm-1 text-center" style="position:relative;width:5%">Quantity</label>
                    </div>

                  <div class="additional_position">
                    <div class="clearfix"></div>
                    <div class="form-group clearfix" width="100%">
                        <div class="col-sm-1 col-sm-offset-4 type"  style="width:10%; display:inline-block; position:relative;" id="category_0">
                         <select data-placeholder="Choose a Category..."  id="category_dd_0" name="category[]" class="chosen-select-deselect form-control category">
                             <?php
                            $query = mysqli_query($dbc,"SELECT DISTINCT category FROM inventory order by category");
                            echo '<option value="">Please Select</option>';
                            while($row = mysqli_fetch_array($query)) {
                                ?><option id='<?php echo $row['category'];?>' value='<?php echo $row['category'];?>'><?php echo $row['category'];?></option><?php
                            }
                            ?>
                        </select>
                        </div>

                        <div class="col-sm-1"  style="width:15%; display:inline-block; position:relative;" id="part_0">
                         <select data-placeholder="Choose a Part#..." id="part_dd_0" name="part_no[]" class="chosen-select-deselect form-control part">
                             <?php
                            $query = mysqli_query($dbc,"SELECT inventoryid, part_no FROM inventory WHERE deleted=0 order by part_no");
                            echo '<option value="">Please Select</option>';
                            while($row = mysqli_fetch_array($query)) {
                                ?><option value='<?php echo $row['inventoryid'];?>'><?php echo $row['part_no'];?></option><?php
                            }
                            ?>
                        </select>
                        </div>

                        <div class="col-sm-3 eq" id="product_0" style="width:20%; position:relative; display:inline-block;">
                            <select data-placeholder="Choose a Product..." name="inventoryid[]" id="product_dd_0" class="chosen-select-deselect form-control product" style="position:relative;">
                                <option value=""></option>

                            </select>
                        </div>

                            <div class="col-sm-1" id="price_0" style="width:7%; position:relative; display:inline-block;">
                                <input data-placeholder="Choose a Product..." name="price[]" id="price_dd_0" value="0" style="" type="text" class="form-control price" />
                            </div>

                            <div class="col-sm-3 qt" id="qty_0" style="width:5%; position:relative; display:inline-block;">
                                <input data-placeholder="Choose a Product..." name="quantity[]" id="qty_dd_0" onkeyup="selectQuantity(this);" value="0" style="" type="text" class="form-control quantity" />
                            </div>

                    </div>

                    </div>

                    <div id="add_here_new_position"></div>

                    <div class="col-sm-8 col-sm-offset-4 triple-gap-bottom">
                        <button id="add_position_button" class="btn brand-btn mobile-block">Add</button>
                    </div>
                    <?php } ?>

                    <?php if (strpos($value_config, ','."Total Price".',') !== FALSE) { ?>
                      <div class="form-group">
                        <label for="site_name" class="col-sm-4 control-label">Sub-Total<span class="brand-color">*</span>:</label>
                        <div class="col-sm-8">
                          <input name="sub_total" id="sub_total" value=0 type="text" class="form-control" />
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="site_name" class="col-sm-4 control-label">GST<span class="brand-color">*</span>:</label>
                        <div class="col-sm-8">
                          <input name="gst" id="gst" value='0.00' type="text" class="form-control" />
                        </div>
                      </div>

                      <div class="form-group">
                        <label for="site_name" class="col-sm-4 control-label">Total Price<span class="brand-color">*</span>:</label>
                        <div class="col-sm-8">
                          <input name="total_price" id="total_price" type="text" value=0 class="form-control" />
                        </div>
                      </div>
                      <?php } ?>

                      <?php if (strpos($value_config, ','."Payment Type".',') !== FALSE) { ?>
                      <div class="form-group">
                        <label for="site_name" class="col-sm-4 control-label">Payment Type<span class="brand-color">*</span>:</label>
                        <div class="col-sm-8">
                          <select id="payment_type" name="payment_type" data-placeholder="Choose a Type..." class="chosen-select-deselect form-control" width="380">
                                <option value=''></option>
                                <option value = 'Mastercard'>Mastercard</option>
                                <option value = 'Visa'>Visa</option>
                                <option value = 'Debit'>Debit</option>
                                <option value = 'Cash'>Cash</option>
                                <option value = 'Net 30 Days'>Net 30 Days</option>
                          </select>
                        </div>
                      </div>
                      <?php } ?>

                      <?php if (strpos($value_config, ','."Comment".',') !== FALSE) { ?>
                      <div class="form-group">
                        <label for="site_name" class="col-sm-4 control-label">Comment:</label>
                        <div class="col-sm-8">
                          <textarea name="comment" rows="4" cols="50" class="form-control" ></textarea>
                        </div>
                      </div>
                      <?php } ?>

                         <div class="form-group">
                            <div class="col-sm-4">
                                <p><span class="empire-red pull-right"><em>Required Fields *</em></span></p>
                            </div>
                            <div class="col-sm-8"></div>
                        </div>

                      <div class="form-group">
                        <div class="col-sm-4 clearfix">
                            <!--<a href="point_of_sell.php" class="btn brand-btn btn-lg pull-right">Back</a>-->
							<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>

                            <button type="submit" name="submit_pos" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
                        </div>
                      </div>

                </form>

		        <?php } ?>

   			</div>
        </div>

	</div>
</div>

<script>
	$('#myTab a').click(function (e) {
	  e.preventDefault()
	  $(this).tab('show')
	})

    $('#myTab a').on('shown.bs.tab', function(e){
      //save the latest tab using a cookie:
      $.cookie('last_tab', $(e.target).attr('href'));
    });

    //activate latest tab, if it exists:
    var lastTab = $.cookie('last_tab');
    if (lastTab) {
        $('ul.nav-pills').children().removeClass('active');
        $('a[href='+ lastTab +']').parents('li:first').addClass('active');
        $('div.tab-content').children().removeClass('active');
        $(lastTab).addClass('active');
    }
</script>
<?php include ('../footer.php'); ?>
