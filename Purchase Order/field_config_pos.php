<?php
/*
Dashboard
*/
include_once ('../include.php');

if (isset($_POST['submit'])) {
    //Logo
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    $pos_logo = htmlspecialchars($_FILES["pos_logo"]["name"], ENT_QUOTES);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='purchase_order_logo'"));
    if($get_config['configid'] > 0) {
		if($pos_logo == '') {
			$logo_update = htmlspecialchars($_POST['logo_file'], ENT_QUOTES);
		} else {
			$logo_update = $pos_logo;
		}
		move_uploaded_file($_FILES["pos_logo"]["tmp_name"],"download/" . $logo_update);
        $query_update_employee = "UPDATE `general_configuration` SET value = '$logo_update' WHERE name='purchase_order_logo'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        move_uploaded_file($_FILES["pos_logo"]["tmp_name"], "download/" . $_FILES["pos_logo"]["name"]) ;
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('purchase_order_logo', '$pos_logo')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Logo

    //Design

    $pos_design = $_POST['pos_design'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='purchase_order_design'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$pos_design' WHERE name='purchase_order_design'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('purchase_order_design', '$pos_design')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Design

    $pos = implode(',',$_POST['pos']);
    $pos_dashboard = implode(',',$_POST['pos_dashboard']);

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_employee = "UPDATE `field_config` SET purchase_order = '$pos', purchase_order_dashboard = '$pos_dashboard' WHERE `fieldconfigid` = 1";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config` (`purchase_order`, `purchase_order_dashboard`) VALUES ('$pos', '$pos_dashboard')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    //Tax
    $pos_tax = '';
    for($i = 0; $i < count($_POST['pos_tax_name']); $i++) {
        if($_POST['pos_tax_name'][$i] != '') {
            //$column = $_POST['pos_tax_name'][$i];
            //$result_column = mysqli_query($dbc, "SHOW COLUMNS FROM `report_sales` LIKE '$column'");
            //$exists_column = (mysqli_num_rows($result_column))?TRUE:FALSE;

            //if($exists_column == '') {
            //    mysqli_query($dbc, "ALTER TABLE `report_sales` ADD `$column` VARCHAR(50) NULL");
            //}

            $pos_tax .= filter_var($_POST['pos_tax_name'][$i],FILTER_SANITIZE_STRING).'**'.$_POST['pos_tax_rate'][$i].'**'.$_POST['pos_tax_number'][$i].'**'.$_POST['pos_tax_exemption_'.$i].'*#*';
        }
    }

    $pos_tax = rtrim($pos_tax, "*#*'");
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='purchase_order_tax'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$pos_tax' WHERE name='purchase_order_tax'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('purchase_order_tax', '$pos_tax')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Tax

    // invoice_outbound_email
    $invoice_outbound_email = filter_var($_POST['invoice_outbound_email'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='purchase_order_invoice_outbound_email'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_outbound_email' WHERE name='purchase_order_invoice_outbound_email'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('purchase_order_invoice_outbound_email', '$invoice_outbound_email')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // invoice_outbound_email

	// POS Tile Title
    $pos_tile_titler = filter_var($_POST['pos_tile_titler'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='purchaseorder_tile_titler'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$pos_tile_titler' WHERE name='purchaseorder_tile_titler'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('purchaseorder_tile_titler', '$pos_tile_titler')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // POS Tile Title

		// POS Archive After x Days
    $archive_after_num_days = filter_var($_POST['archive_after_num_days'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='po_archive_after_num_days'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$archive_after_num_days' WHERE name='po_archive_after_num_days'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('po_archive_after_num_days', '$archive_after_num_days')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // POS Archive After x Days

    // Categories
    $purchase_order_categories = filter_var(htmlentities($_POST['purchase_order_categories']),FILTER_SANITIZE_STRING);

    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='purchase_order_categories'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$purchase_order_categories' WHERE name='purchase_order_categories'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('purchase_order_categories', '$purchase_order_categories')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // Categories

    // payment_type
    $invoice_payment_types = filter_var($_POST['invoice_payment_types'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='po_invoice_payment_types'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = 'Pay Now,Net 30,Net 60,Net 90,Net 120,$invoice_payment_types' WHERE name='po_invoice_payment_types'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('po_invoice_payment_types', 'Pay Now,Net 30,Net 60,Net 90,Net 120,$invoice_payment_types')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // payment_type

    // Footer
    $invoice_footer = filter_var($_POST['invoice_footer'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='po_invoice_footer'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_footer' WHERE name='po_invoice_footer'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('po_invoice_footer', '$invoice_footer')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // Footer

    echo '<script type="text/javascript"> window.location.replace(""); </script>';

}
?>
<script type="text/javascript">
$(document).ready(function() {
    $('#add_tax_button').on( 'click', function () {
        var clone = $('.additional_tax').clone();

        var numItems = $('.tax_exemption_div').length;
        clone.find('.tax_exemption').attr("name", "pos_tax_exemption_"+numItems);
        clone.find('.form-control').val('');
        clone.find('.rate').val('0');
        clone.removeClass("additional_tax");
        $('#add_here_new_tax').append(clone);
        return false;
    });
});
</script>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<div class="panel-group" id="accordion2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_categories" >
                   Purchase Order Categories<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_categories" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Categories (separated by commas):</label>
                    <div class="col-sm-8">
                        <input type="text" name="purchase_order_categories" rows="3" cols="50" class="form-control" value="<?php echo get_config($dbc, 'purchase_order_categories'); ?>" />
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_logo" >
                    Logo for Purchase Order PDF<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_logo" class="panel-collapse collapse">
            <div class="panel-body">
                <?php
                $pos_logo = get_config($dbc, 'purchase_order_logo');
                ?>

                <div class="form-group">
                <label for="file[]" class="col-sm-4 control-label">Upload Logo
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="20"></a>
                </span>
                :</label>
                <div class="col-sm-8">
                <?php if($pos_logo != '') {
                    echo '<a href="download/'.$pos_logo.'" target="_blank">View</a>';
                    ?>
                    <input type="hidden" name="logo_file" value="<?php echo $pos_logo; ?>" />
                    <input name="pos_logo" type="file" data-filename-placement="inside" class="form-control" />
                  <?php } else { ?>
                  <input name="pos_logo" type="file" data-filename-placement="inside" class="form-control" />
                  <?php } ?>
                </div>
                </div>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_design" >
                    Design of Purchase Order PDF<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_design" class="panel-collapse collapse">
            <div class="panel-body">
                <?php
                $pos_design = get_config($dbc, 'purchase_order_design');
                ?>
                <label><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($pos_design == '1') { echo 'checked'; } ?> type="radio" name="pos_design" value="1"></label>
                <a target="_blank" href="../img/invoice_design1.png"><img src="../img/invoice_design1.png" width="100" height="100" border="0" alt=""></a>

                &nbsp;&nbsp;&nbsp;

                <label><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($pos_design == '2') { echo 'checked'; } ?> type="radio" name="pos_design" value="2"></label>
                <a target="_blank" href="../img/invoice_design2.png"><img src="../img/invoice_design2.png" width="100" height="100" border="0" alt=""></a>

				&nbsp;&nbsp;&nbsp;

                <label><input style="height: 30px; width: 30px;" class="tax_exemption" <?php if ($pos_design == '3') { echo 'checked'; } ?> type="radio" name="pos_design" value="3"></label>
                <a target="_blank" href="../img/invoice_design3.png"><img src="../img/invoice_design3.png" width="100" height="100" border="0" alt=""></a>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
                    Choose Fields for Creating a Purchase Order<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_field" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <?php $value_config = get_field_config($dbc, 'purchase_order'); ?>

                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Send Outbound Invoice".',') !== FALSE) { echo " checked"; } ?> value="Send Outbound Invoice" name="pos[]">&nbsp;&nbsp;Send Outbound Purchase Order</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Invoice Date".',') !== FALSE) { echo " checked"; } ?> value="Invoice Date" name="pos[]">&nbsp;&nbsp;Order Date</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."PO Name".',') !== FALSE) { echo " checked"; } ?> value="PO Name" name="pos[]">&nbsp;&nbsp;PO Name</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" name="pos[]">&nbsp;&nbsp;<?= PROJECT_NOUN ?></label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Ticket".',') !== FALSE) { echo " checked"; } ?> value="Ticket" name="pos[]">&nbsp;&nbsp;<?= TICKET_NOUN ?></label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Business".',') !== FALSE) { echo " checked"; } ?> value="Business" name="pos[]">&nbsp;&nbsp;<?= BUSINESS_CAT ?></label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Site".',') !== FALSE) { echo " checked"; } ?> value="Site" name="pos[]">&nbsp;&nbsp;<?= SITES_CAT ?></label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" name="pos[]">&nbsp;&nbsp;Equipment</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Customer".',') !== FALSE) { echo " checked"; } ?> value="Customer" name="pos[]">&nbsp;&nbsp;Vendor</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Order Forms".',') !== FALSE) { echo " checked"; } ?> value="Order Forms" name="pos[]">&nbsp;&nbsp;Order Forms</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Product Pricing".',') !== FALSE) { echo " checked"; } ?> value="Product Pricing" name="pos[]">&nbsp;&nbsp;Pricing</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Tax".',') !== FALSE) { echo " checked"; } ?> value="Tax" name="pos[]">&nbsp;&nbsp;Tax</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Products".',') !== FALSE) { echo " checked"; } ?> value="Products" name="pos[]">&nbsp;&nbsp;Inventory</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."vplProducts".',') !== FALSE) { echo " checked"; } ?> value="vplProducts" name="pos[]">&nbsp;&nbsp;Vendor Price List</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."prodProducts".',') !== FALSE) { echo " checked"; } ?> value="prodProducts" name="pos[]">&nbsp;&nbsp;Products</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Pricing by Line Item".',') !== FALSE) { echo " checked"; } ?> value="Pricing by Line Item" name="pos[]">&nbsp;&nbsp;Pricing by Line Item</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."servServices".',') !== FALSE) { echo " checked"; } ?> value="servServices" name="pos[]">&nbsp;&nbsp;Services</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Misc Item".',') !== FALSE) { echo " checked"; } ?> value="Misc Item" name="pos[]">&nbsp;&nbsp;Misc Item</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Total Price".',') !== FALSE) { echo " checked"; } ?> value="Total Price" name="pos[]">&nbsp;&nbsp;Total Price</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Discount".',') !== FALSE) { echo " checked"; } ?> value="Discount" name="pos[]">&nbsp;&nbsp;Discount</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Delivery".',') !== FALSE) { echo " checked"; } ?> value="Delivery" name="pos[]">&nbsp;&nbsp;Delivery/Pickup</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Assembly".',') !== FALSE) { echo " checked"; } ?> value="Assembly" name="pos[]">&nbsp;&nbsp;Assembly</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Tax 2".',') !== FALSE) { echo " checked"; } ?> value="Tax 2" name="pos[]">&nbsp;&nbsp;Tax at End of Order</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Payment Type".',') !== FALSE) { echo " checked"; } ?> value="Payment Type" name="pos[]">&nbsp;&nbsp;Payment Type</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Deposit Paid".',') !== FALSE) { echo " checked"; } ?> value="Deposit Paid" name="pos[]">&nbsp;&nbsp;Deposit Paid</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Comment".',') !== FALSE) { echo " checked"; } ?> value="Comment" name="pos[]">&nbsp;&nbsp;Comment</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Created/Sold By".',') !== FALSE) { echo " checked"; } ?> value="Created/Sold By" name="pos[]">&nbsp;&nbsp;Created/Sold By</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Ship Date".',') !== FALSE) { echo " checked"; } ?> value="Ship Date" name="pos[]">&nbsp;&nbsp;Ship Date</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Upload".',') !== FALSE) { echo " checked"; } ?> value="Upload" name="pos[]">&nbsp;&nbsp;Supporting Document (Invoice / Receipt)</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Due Date".',') !== FALSE) { echo " checked"; } ?> value="Due Date" name="pos[]">&nbsp;&nbsp;Due Date</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Tax Exemption".',') !== FALSE) { echo " checked"; } ?> value="Tax Exemption" name="pos[]">&nbsp;&nbsp;Tax Exemption</label>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_product" >
                    Choose Fields for Inventory<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_product" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" name="pos[]">&nbsp;&nbsp;Category
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Part#".',') !== FALSE) { echo " checked"; } ?> value="Part#" name="pos[]">&nbsp;&nbsp;Part#
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Name".',') !== FALSE) { echo " checked"; } ?> value="Name" name="pos[]">&nbsp;&nbsp;Name
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Price".',') !== FALSE) { echo " checked"; } ?> value="Price" name="pos[]">&nbsp;&nbsp;Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Quantity".',') !== FALSE) { echo " checked"; } ?> value="Quantity" name="pos[]">&nbsp;&nbsp;Quantity
                        </td>
                    </tr>

                </table>
            </div>
        </div>
    </div>
	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_service" >
                    Choose Fields for Services<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_service" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."servCategory".',') !== FALSE) { echo " checked"; } ?> value="servCategory" name="pos[]">&nbsp;&nbsp;Category
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."servHeading".',') !== FALSE) { echo " checked"; } ?> value="servHeading" name="pos[]">&nbsp;&nbsp;Heading
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."servPrice".',') !== FALSE) { echo " checked"; } ?> value="servPrice" name="pos[]">&nbsp;&nbsp;Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."servQuantity".',') !== FALSE) { echo " checked"; } ?> value="servQuantity" name="pos[]">&nbsp;&nbsp;Quantity
                        </td>
                    </tr>

                </table>
            </div>
        </div>
    </div>
	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_productprod" >
                    Choose Fields for Products<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_productprod" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."prodCategory".',') !== FALSE) { echo " checked"; } ?> value="prodCategory" name="pos[]">&nbsp;&nbsp;Category
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."prodHeading".',') !== FALSE) { echo " checked"; } ?> value="prodHeading" name="pos[]">&nbsp;&nbsp;Heading
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."prodPrice".',') !== FALSE) { echo " checked"; } ?> value="prodPrice" name="pos[]">&nbsp;&nbsp;Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."prodQuantity".',') !== FALSE) { echo " checked"; } ?> value="prodQuantity" name="pos[]">&nbsp;&nbsp;Quantity
                        </td>
                    </tr>

                </table>
            </div>
        </div>
    </div>

	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_productvpl" >
                    Choose Fields for Vendor Price List<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_productvpl" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."vplCategory".',') !== FALSE) { echo " checked"; } ?> value="vplCategory" name="pos[]">&nbsp;&nbsp;Category
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."vplPart#".',') !== FALSE) { echo " checked"; } ?> value="vplPart#" name="pos[]">&nbsp;&nbsp;Part#
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."vplName".',') !== FALSE) { echo " checked"; } ?> value="vplName" name="pos[]">&nbsp;&nbsp;Name
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."vplPrice".',') !== FALSE) { echo " checked"; } ?> value="vplPrice" name="pos[]">&nbsp;&nbsp;Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."vplQuantity".',') !== FALSE) { echo " checked"; } ?> value="vplQuantity" name="pos[]">&nbsp;&nbsp;Quantity
                        </td>
                    </tr>

                </table>
            </div>
        </div>
    </div>

	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_misc" >
                    Choose Fields for Misc Items<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_misc" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."miscQty".',') !== FALSE) { echo " checked"; } ?> value="miscQty" name="pos[]">&nbsp;&nbsp;Qty
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."miscGrade".',') !== FALSE) { echo " checked"; } ?> value="miscGrade" name="pos[]">&nbsp;&nbsp;Grade
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."miscTag".',') !== FALSE) { echo " checked"; } ?> value="miscTag" name="pos[]">&nbsp;&nbsp;Tag
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."miscDetail".',') !== FALSE) { echo " checked"; } ?> value="miscDetail" name="pos[]">&nbsp;&nbsp;Detail
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."miscUnitPrice".',') !== FALSE) { echo " checked"; } ?> value="miscUnitPrice" name="pos[]">&nbsp;&nbsp;Unit Price
                        </td>
                    </tr>

                </table>
            </div>
        </div>
    </div>

    <?php if (strpos($value_config, ','."Product Pricing".',') !== FALSE) { ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pp" >
                    Choose Fields for Pricing<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_pp" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { echo " checked"; } ?> value="Client Price" name="pos[]">&nbsp;&nbsp;Client Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" name="pos[]">&nbsp;&nbsp;Admin Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" name="pos[]">&nbsp;&nbsp;Commercial Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" name="pos[]">&nbsp;&nbsp;Wholesale Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" name="pos[]">&nbsp;&nbsp;Final Retail Price
                        </td>

                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Preferred Price".',') !== FALSE) { echo " checked"; } ?> value="Preferred Price" name="pos[]">&nbsp;&nbsp;Preferred Price
                        </td>
                    </tr>
                    <tr>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) { echo " checked"; } ?> value="Purchase Order Price" name="pos[]">&nbsp;&nbsp;Purchase Order Price
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) { echo " checked"; } ?> value="Sales Order Price" name="pos[]">&nbsp;&nbsp;<?= SALES_ORDER_NOUN ?> Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Web Price".',') !== FALSE) { echo " checked"; } ?> value="Web Price" name="pos[]">&nbsp;&nbsp;Web Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Cost".',') !== FALSE) { echo " checked"; } ?> value="Drum Unit Cost" name="pos[]">&nbsp;&nbsp;Drum Unit Cost
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Price".',') !== FALSE) { echo " checked"; } ?> value="Drum Unit Price" name="pos[]">&nbsp;&nbsp;Drum Unit Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Cost".',') !== FALSE) { echo " checked"; } ?> value="Tote Unit Cost" name="pos[]">&nbsp;&nbsp;Tote Unit Cost
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Price".',') !== FALSE) { echo " checked"; } ?> value="Tote Unit Price" name="pos[]">&nbsp;&nbsp;Tote Unit Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Average Cost".',') !== FALSE) { echo " checked"; } ?> value="Average Cost" name="pos[]">&nbsp;&nbsp;Average Cost
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."USD Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="USD Cost Per Unit" name="pos[]">&nbsp;&nbsp;USD Cost Per Unit
                        </td>
                    </tr>

                </table>
            </div>
        </div>
    </div>
    <?php } ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dashboard" >
                    Choose Fields for Purchase Order Dashboard<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_dashboard" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT purchase_order_dashboard FROM field_config"));
                $value_config = ','.$get_field_config['purchase_order_dashboard'].',';
                ?>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Invoice #".',') !== FALSE) { echo " checked"; } ?> value="Invoice #" name="pos_dashboard[]">&nbsp;&nbsp;Purchase Order #</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Invoice Date".',') !== FALSE) { echo " checked"; } ?> value="Invoice Date" name="pos_dashboard[]">&nbsp;&nbsp;Order Date</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Customer".',') !== FALSE) { echo " checked"; } ?> value="Customer" name="pos_dashboard[]">&nbsp;&nbsp;Vendor</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" name="pos_dashboard[]">&nbsp;&nbsp;Equipment</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Total Price".',') !== FALSE) { echo " checked"; } ?> value="Total Price" name="pos_dashboard[]">&nbsp;&nbsp;Total Price</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Payment Type".',') !== FALSE) { echo " checked"; } ?> value="Payment Type" name="pos_dashboard[]">&nbsp;&nbsp;Payment Type</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Invoice PDF".',') !== FALSE) { echo " checked"; } ?> value="Invoice PDF" name="pos_dashboard[]">&nbsp;&nbsp;Purchase Order PDF</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Comment".',') !== FALSE) { echo " checked"; } ?> value="Comment" name="pos_dashboard[]">&nbsp;&nbsp;Comment</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" name="pos_dashboard[]">&nbsp;&nbsp;Status</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Send to Client".',') !== FALSE) { echo " checked"; } ?> value="Send to Client" name="pos_dashboard[]">&nbsp;&nbsp;Send to Client</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Delivery/Shipping Type".',') !== FALSE) { echo " checked"; } ?> value="Delivery/Shipping Type" name="pos_dashboard[]">&nbsp;&nbsp;Delivery/Shipping Type</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Send to Anyone".',') !== FALSE) { echo " checked"; } ?> value="Send to Anyone" name="pos_dashboard[]">&nbsp;&nbsp;Send to Anyone</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."View Spreadsheet".',') !== FALSE) { echo " checked"; } ?> value="View Spreadsheet" name="pos_dashboard[]">&nbsp;&nbsp;View Spreadsheet</label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Project".',') !== FALSE) { echo " checked"; } ?> value="Project" name="pos_dashboard[]">&nbsp;&nbsp;<?= PROJECT_NOUN ?></label>
                <label class="form-checkbox"><input type="checkbox" <?php if (strpos($value_config, ','."Ticket".',') !== FALSE) { echo " checked"; } ?> value="Ticket" name="pos_dashboard[]">&nbsp;&nbsp;<?= TICKET_NOUN ?></label>

            </div>
        </div>
    </div>

	 <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_titler" >
                   Tile Title<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_titler" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Title of Purchase Order Tile on the Home Screen:</label>
                    <div class="col-sm-8">
                      <input name="pos_tile_titler" value="<?php if(get_config($dbc, 'purchaseorder_tile_titler') == '' || get_config($dbc, 'purchaseorder_tile_titler') == NULL ) { echo "Purchase Order"; } else { echo get_config($dbc, 'purchaseorder_tile_titler'); } ?>" type="text" class="form-control"/>
                    </div>
                </div>

            </div>
        </div>
    </div>

	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_archiveinvoicer" >
                   Archiving Purchase Orders<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_archiveinvoicer" class="panel-collapse collapse">
            <div class="panel-body">
				<?php include_once ('javascript_include.php'); ?>
                <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Auto-Archive Purchase Orders After X Days:</label>
                    <div class="col-sm-8">
					  Enabled:  <input onclick="handleClick(this);" <?php if(get_config($dbc, 'po_archive_after_num_days') !== '' && get_config($dbc, 'po_archive_after_num_days') !== NULL) { echo "checked"; } ?> type='radio' name='yesornoarchiveinvoice' class='yesornoarchiveinvoice' value='yes'>
					  Disabled: <input onclick="handleClick(this);" <?php if(get_config($dbc, 'po_archive_after_num_days') == '' || get_config($dbc, 'po_archive_after_num_days') == NULL) { echo "checked"; } ?> type='radio' name='yesornoarchiveinvoice' class='yesornoarchiveinvoice' value='no'>
                    </div>

                </div>

				<div class="form-group hide_numofdays" <?php if(get_config($dbc, 'po_archive_after_num_days') == '' || get_config($dbc, 'po_archive_after_num_days') == NULL) { echo "style='display:none;'"; } ?>>
                    <label for="office_country" class="col-sm-4 control-label hide_numofdays" style="margin-top:5px;"># Of Days After Creation To Archive Purchase Orders:</label>
                    <div class="col-sm-8 hide_numofdays">
					  <input name="archive_after_num_days" value="<?php echo get_config($dbc, 'po_archive_after_num_days'); ?>" type="number" class="form-control hide_numofdays2"/>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pay" >
                   Payment Type Options<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_pay" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Payment Type Options:<br><em>(separate by a comma)</em></label>
                    <div class="col-sm-8">
					<?php
						$invoice_pay_typers = str_replace('Pay Now,Net 30,Net 60,Net 90,Net 120,', '', get_config($dbc, 'po_invoice_payment_types'));
					?>
                      <input name="invoice_payment_types" value="<?php echo $invoice_pay_typers; ?>" type="text" class="form-control"/>
                    </div>
                </div>
				<div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Preset Payment Type Options:</label>
                    <div class="col-sm-8">
                      <ul><li>Pay Now</li>
					  <li>Net 30</li>
					  <li>Net 60</li>
					  <li>Net 90</li>
					  <li>Net 120</li>
					  </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_out" >
                   Outbound Purchase Order Email<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_out" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">

                <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Outbound Purchase Order Email:<br><em>(separate multiple emails with a comma)</em></label>
                    <div class="col-sm-8">
                      <input name="invoice_outbound_email" value="<?php echo get_config($dbc, 'purchase_order_invoice_outbound_email'); ?>" type="text" class="form-control"/>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tax" >
                    Set Tax Names & Rates<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_tax" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="form-group clearfix">
                    <label class="col-sm-2 text-center">Name</label>
                    <label class="col-sm-2 text-center">Rate(%)<br><em>(add number without % sign)</em></label>
                    <label class="col-sm-2 text-center">Tax Number</label>
                    <label class="col-sm-2">Tax Exempt</label>
                </div>

                <?php
                $value_config = get_config($dbc, 'purchase_order_tax');

                $pos_tax = explode('*#*',$value_config);

                $total_count = mb_substr_count($value_config,'*#*');
                for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
                    $pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);
                ?>
                    <div class="clearfix"></div>
                    <div class="form-group clearfix">
                      <div class="col-sm-2">
                            <input name="pos_tax_name[]" value="<?php echo $pos_tax_name_rate[0];?>" type="text" class="form-control quantity" />
                        </div>
                        <div class="col-sm-2">
                            <input name="pos_tax_rate[]" value="<?php echo $pos_tax_name_rate[1]; ?>" type="text" class="form-control category" />
                        </div>
                        <div class="col-sm-2">
                            <input name="pos_tax_number[]" value="<?php echo $pos_tax_name_rate[2]; ?>" type="text" class="form-control category" />
                        </div>
                        <div class="col-sm-2">
                          <div class="radio tax_exemption_div">
                            <label><input class="tax_exemption" type="radio" <?php if ($pos_tax_name_rate[3] == 'Yes') { echo 'checked'; } ?> name="pos_tax_exemption_<?php echo $eq_loop;?>" value="Yes">Yes</label>
                            &nbsp; &nbsp;
                            <label><input class="tax_exemption" type="radio" <?php if ($pos_tax_name_rate[3] == 'No' || $pos_tax_name_rate[3] == '') { echo 'checked'; } ?> name="pos_tax_exemption_<?php echo $eq_loop;?>" value="No">No</label>
                          </div>
                        </div>

                    </div>
                <?php } ?>

                <div class="additional_tax">
                <div class="clearfix"></div>
                <div class="form-group clearfix" width="100%">
                    <div class="col-sm-2">
                        <input name="pos_tax_name[]" type="text" class="form-control price" />
                    </div>
                    <div class="col-sm-2">
                        <input name="pos_tax_rate[]" value="0" type="text" class="form-control rate" />
                    </div>
                    <div class="col-sm-2">
                        <input name="pos_tax_number[]" type="text" class="form-control" />
                    </div>
                    <div class="col-sm-2">
                      <div class="radio tax_exemption_div">
                        <label><input class="tax_exemption" type="radio" name="pos_tax_exemption_<?php echo $eq_loop;?>" value="Yes">Yes</label>
                        &nbsp; &nbsp;
                        <label><input class="tax_exemption" type="radio" name="pos_tax_exemption_<?php echo $eq_loop;?>" value="No">No</label>
                      </div>
                    </div>
                </div>

                </div>

                <div id="add_here_new_tax"></div>

                <div class="col-sm-8 col-sm-offset-4 triple-gap-bottom">
                    <button id="add_tax_button" class="btn brand-btn mobile-block">Add</button>
                </div>


            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_footer" >
                    Footer Text<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_footer" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Purchase Order PDF Footer Text:<br><em>(e.g. - company name, address, phone, etc.)</em></label>
                    <div class="col-sm-8">
                      <input name="invoice_footer" value="<?php echo get_config($dbc, 'po_invoice_footer'); ?>" type="text" class="form-control"/>
                    </div>
                </div>

            </div>
        </div>
    </div>


</div>

<div class="form-group">
	<button	type="submit" name="submit"	value="Submit" class="btn brand-btn pull-right">Submit</button>
</div>

</form>