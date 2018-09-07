<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('pos');
error_reporting(0);

if (isset($_POST['submit'])) {
    //Logo
	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}
    $pos_logo = htmlspecialchars($_FILES["pos_logo"]["name"], ENT_QUOTES);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='pos_logo'"));
    if($get_config['configid'] > 0) {
		if($pos_logo == '') {
			$logo_update = $_POST['logo_file'];
		} else {
			$logo_update = $pos_logo;
		}
		move_uploaded_file($_FILES["pos_logo"]["tmp_name"],"download/" . $logo_update);
        $query_update_employee = "UPDATE `general_configuration` SET value = '$logo_update' WHERE name='pos_logo'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        move_uploaded_file($_FILES["pos_logo"]["tmp_name"], "download/" . $_FILES["pos_logo"]["name"]) ;
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('pos_logo', '$pos_logo')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Logo

    //Design

    $pos_design = $_POST['pos_design'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='pos_design'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$pos_design' WHERE name='pos_design'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('pos_design', '$pos_design')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Design

    //Layout
    $pos_layout = ( !empty ( $_POST['pos_layout'] ) ) ? $_POST['pos_layout'] : 'keyboard';
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE `name`='pos_layout'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET `value`='$pos_layout' WHERE `name`='pos_layout'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('pos_layout', '$pos_layout')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Layout

    //Default Sub Tab
    $mobile_landing_subtab = ( !empty($_POST['mobile_landing_subtab']) ) ? filter_var($_POST['mobile_landing_subtab'], FILTER_SANITIZE_STRING) : '';
    $desktop_landing_subtab = ( !empty($_POST['desktop_landing_subtab']) ) ? filter_var($_POST['desktop_landing_subtab'], FILTER_SANITIZE_STRING) : '';

    $mobile_landing_subtab_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE `name`='pos_mobile_landing_subtab'"));
    if($mobile_landing_subtab_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET `value`='$mobile_landing_subtab' WHERE `name`='pos_mobile_landing_subtab'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('pos_mobile_landing_subtab', '$mobile_landing_subtab')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $desktop_landing_subtab_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE `name`='pos_desktop_landing_subtab'"));
    if($desktop_landing_subtab_config['configid'] > 0) {
        $query_update_config = "UPDATE `general_configuration` SET `value`='$desktop_landing_subtab' WHERE `name`='pos_desktop_landing_subtab'";
        $result_update_config = mysqli_query($dbc, $query_update_config);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('pos_desktop_landing_subtab', '$desktop_landing_subtab')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Default Sub Tab

    //New Customer Category Selection
    $pos_new_customer = ( !empty ( $_POST['pos_new_customer'] ) ) ? $_POST['pos_new_customer'] : 'Customer';
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE `name`='pos_new_customer'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET `value`='$pos_new_customer' WHERE `name`='pos_new_customer'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('pos_new_customer', '$pos_new_customer')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //New Customer Category Selection

    $pos = implode(',',$_POST['pos']);
    $pos_dashboard = implode(',',$_POST['pos_dashboard']);

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(fieldconfigid) AS fieldconfigid FROM field_config"));
    if($get_field_config['fieldconfigid'] > 0) {
        $query_update_employee = "UPDATE `field_config` SET pos = '$pos', pos_dashboard = '$pos_dashboard' WHERE `fieldconfigid` = 1";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config` (`pos`, `pos_dashboard`) VALUES ('$pos', '$pos_dashboard')";
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
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='pos_tax'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$pos_tax' WHERE name='pos_tax'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('pos_tax', '$pos_tax')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Tax

    // invoice_outbound_email
    $invoice_outbound_email = filter_var($_POST['invoice_outbound_email'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_outbound_email'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_outbound_email' WHERE name='invoice_outbound_email'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_outbound_email', '$invoice_outbound_email')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // invoice_outbound_email

	// POS Tile Title
    $pos_tile_titler = filter_var($_POST['pos_tile_titler'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='pos_tile_titler'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$pos_tile_titler' WHERE name='pos_tile_titler'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('pos_tile_titler', '$pos_tile_titler')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // POS Tile Title

    // POS Archive After x Days
    $archive_after_num_days = filter_var($_POST['archive_after_num_days'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='archive_after_num_days'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$archive_after_num_days' WHERE name='archive_after_num_days'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('archive_after_num_days', '$archive_after_num_days')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // POS Archive After x Days

    //Pay multiple invoices

    $pay_multiple_ar_invoices = filter_var($_POST['pay_multiple_ar_invoices'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='pay_multiple_ar_invoices'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$pay_multiple_ar_invoices' WHERE name='pay_multiple_ar_invoices'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('pay_multiple_ar_invoices', '$pay_multiple_ar_invoices')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Pay multiple invoices

    // payment_type
    $invoice_payment_types = filter_var($_POST['invoice_payment_types'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_payment_types'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_payment_types' WHERE name='invoice_payment_types'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_payment_types', '$invoice_payment_types')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // payment_type

    // Footer
    $invoice_footer = filter_var($_POST['invoice_footer'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='invoice_footer'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$invoice_footer' WHERE name='invoice_footer'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('invoice_footer', '$invoice_footer')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    // Footer

    echo '<script type="text/javascript"> window.location.replace("field_config_pos.php"); </script>';

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
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1><?= POS_ADVANCE_TILE ?></h1>
<div class="pad-left gap-top double-gap-bottom"><a href="point_of_sell.php" class="btn config-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<div class="pad-left gap-top double-gap-bottom">
	<a href='field_config_pos.php'><button type="button" class="btn brand-btn mobile-block active_tab" >General</button></a>
	<a href='field_config_promotion.php'><button type="button" class="btn brand-btn mobile-block" >Promotion</button></a>
</div>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<div class="panel-group" id="accordion2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_logo" >
                    Logo for Invoice PDF<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_logo" class="panel-collapse collapse">
            <div class="panel-body">
                <?php
                $pos_logo = get_config($dbc, 'pos_logo');
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
                    Design Invoice PDF<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_design" class="panel-collapse collapse">
            <div class="panel-body">
                <?php $pos_design = get_config($dbc, 'pos_design'); ?>
                <label><input style="height:30px; width:30px;" class="tax_exemption" <?= ($pos_design == '1') ? 'checked' : ''; ?> type="radio" name="pos_design" value="1"></label>
                <a target="_blank" href="../img/invoice_design1.png"><img src="../img/invoice_design1.png" width="100" height="100" border="0" alt=""></a>

                &nbsp;&nbsp;&nbsp;

                <label><input style="height:30px; width:30px;" class="tax_exemption" <?= ($pos_design == '2') ? 'checked' : ''; ?> type="radio" name="pos_design" value="2"></label>
                <a target="_blank" href="../img/invoice_design2.png"><img src="../img/invoice_design2.png" width="100" height="100" border="0" alt=""></a>

				&nbsp;&nbsp;&nbsp;

                <label><input style="height:30px; width:30px;" class="tax_exemption" <?= ($pos_design == '3') ? 'checked' : ''; ?> type="radio" name="pos_design" value="3"></label>
                <a target="_blank" href="../img/invoice_design3.png"><img src="../img/invoice_design3.png" width="100" height="100" border="0" alt=""></a>

				&nbsp;&nbsp;&nbsp;

                <label><input style="height:30px; width:30px;" class="tax_exemption" <?= ($pos_design == '5') ? 'checked' : ''; ?> type="radio" name="pos_design" value="5"></label>
                <a target="_blank" href="../img/invoice_design5.png"><img src="../img/invoice_design5.png" width="100" height="100" border="0" alt=""></a>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_layout" >
                    Choose POS Layout<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_layout" class="panel-collapse collapse">
            <div class="panel-body"><?php
                $pos_layout = get_config($dbc, 'pos_layout'); ?>

				<div class="col-sm-3">
					<label class="offset-right-5"><input class="" <?php if ($pos_layout == 'keyboard') { echo 'checked'; } ?> type="radio" name="pos_layout" value="keyboard"></label>
					Keyboard Input Layout
				</div>
				<div class="col-sm-3">
					<label class="offset-right-5"><input class="" <?php if ($pos_layout == 'touch') { echo 'checked'; } ?> type="radio" name="pos_layout" value="touch"></label>
					Touch Screen Layout
				</div>
				<div class="col-sm-3">
					<label class="offset-right-5"><input class="" <?php if ($pos_layout == 'both') { echo 'checked'; } ?> type="radio" name="pos_layout" value="both"></label>
					Keyboard &amp; Touch Screen Layout
				</div>
				<div class="clearfix"></div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_default_subtab" >
                    Choose Default Landing Sub Tab<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_default_subtab" class="panel-collapse collapse">
            <div class="panel-body">
				<div class="form-group">
					<label class="col-sm-4 control-label">Mobile Default Landing Sub Tab</label>
                    <div class="col-sm-8">
                        <select name="mobile_landing_subtab" class="form-control chosen-select-deselect" data-placeholder="Select Sub Tab...">
                            <option value=""></option><?php
                            $mobile_landing_subtab_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE `name`='pos_mobile_landing_subtab'"));
                            $pos_layout = get_config($dbc, 'pos_layout');
                            if ($pos_layout == 'keyboard') {
                                echo '<option value="sell" '. ($mobile_landing_subtab_config['value']=='sell' ? 'selected="selected"' : '') .'>Sell</option>';
                            } elseif ($pos_layout == 'touch') {
                                echo '<option value="sell_touch" '. ($mobile_landing_subtab_config['value']=='sell_touch' ? 'selected="selected"' : '') .'>Sell</option>';
                            } elseif ($pos_layout == 'both') {
                                echo '<option value="sell" '. ($mobile_landing_subtab_config['value']=='sell' ? 'selected="selected"' : '') .'>Sell - Keyboard Input</option>';
                                echo '<option value="sell_touch" '. ($mobile_landing_subtab_config['value']=='sell_touch' ? 'selected="selected"' : '') .'>Sell - Touch Input</option>';
                            } else {
                                echo '<option value="sell" '. ($mobile_landing_subtab_config['value']=='sell' ? 'selected="selected"' : '') .'>Sell</option>';
                            } ?>
                            <option value="invoices" <?= $mobile_landing_subtab_config['value']=='invoices' ? 'selected="selected"' : '' ?>>Invoices</option>
                            <option value="returns" <?= $mobile_landing_subtab_config['value']=='returns' ? 'selected="selected"' : '' ?>>Returns</option>
                            <option value="accounts_receivable" <?= $mobile_landing_subtab_config['value']=='accounts_receivable' ? 'selected="selected"' : '' ?>>Accounts Receivable</option>
                            <option value="voided_invoices" <?= $mobile_landing_subtab_config['value']=='voided_invoices' ? 'selected="selected"' : '' ?>>Voided Invoices</option>
                            <option value="gift_cards" <?= $mobile_landing_subtab_config['value']=='gift_cards' ? 'selected="selected"' : '' ?>>Gift Cards</option>
                        </select>
                    </div>
                </div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Desktop Default Landing Sub Tab</label>
                    <div class="col-sm-8">
                        <select name="desktop_landing_subtab" class="form-control chosen-select-deselect" data-placeholder="Select Sub Tab...">
                            <option value=""></option><?php
                            $desktop_landing_subtab_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE `name`='pos_desktop_landing_subtab'"));
                            $pos_layout = get_config($dbc, 'pos_layout');
                            if ($pos_layout == 'keyboard') {
                                echo '<option value="sell" '. ($desktop_landing_subtab_config['value']=='sell' ? 'selected="selected"' : '') .'>Sell</option>';
                            } elseif ($pos_layout == 'touch') {
                                echo '<option value="sell_touch" '. ($desktop_landing_subtab_config['value']=='sell_touch' ? 'selected="selected"' : '') .'>Sell</option>';
                            } elseif ($pos_layout == 'both') {
                                echo '<option value="sell" '. ($desktop_landing_subtab_config['value']=='sell' ? 'selected="selected"' : '') .'>Sell - Keyboard Input</option>';
                                echo '<option value="sell_touch" '. ($desktop_landing_subtab_config['value']=='sell_touch' ? 'selected="selected"' : '') .'>Sell - Touch Input</option>';
                            } else {
                                echo '<option value="sell" '. ($desktop_landing_subtab_config['value']=='sell' ? 'selected="selected"' : '') .'>Sell</option>';
                            } ?>
                            <option value="invoices" <?= $desktop_landing_subtab_config['value']=='invoices' ? 'selected="selected"' : '' ?>>Invoices</option>
                            <option value="returns" <?= $desktop_landing_subtab_config['value']=='returns' ? 'selected="selected"' : '' ?>>Returns</option>
                            <option value="accounts_receivable" <?= $desktop_landing_subtab_config['value']=='accounts_receivable' ? 'selected="selected"' : '' ?>>Accounts Receivable</option>
                            <option value="voided_invoices" <?= $desktop_landing_subtab_config['value']=='voided_invoices' ? 'selected="selected"' : '' ?>>Voided Invoices</option>
                            <option value="gift_cards" <?= $desktop_landing_subtab_config['value']=='gift_cards' ? 'selected="selected"' : '' ?>>Gift Cards</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
                    Choose Fields for POS<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_field" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT pos FROM field_config"));
                $value_config = ','.$get_field_config['pos'].',';
                ?>

                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Invoice Date".',') !== FALSE) { echo " checked"; } ?> value="Invoice Date" name="pos[]">&nbsp;&nbsp;Invoice Date
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Customer".',') !== FALSE) { echo " checked"; } ?> value="Customer" name="pos[]">&nbsp;&nbsp;Customer
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Product Pricing".',') !== FALSE) { echo " checked"; } ?> value="Product Pricing" name="pos[]">&nbsp;&nbsp;Pricing
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Send Outbound Invoice".',') !== FALSE) { echo " checked"; } ?> value="Send Outbound Invoice" name="pos[]">&nbsp;&nbsp;Send Outbound Invoice
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Discount".',') !== FALSE) { echo " checked"; } ?> value="Discount" name="pos[]">&nbsp;&nbsp;Discount
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Coupon".',') !== FALSE) { echo " checked"; } ?> value="Coupon" name="pos[]">&nbsp;&nbsp;Coupon
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Delivery".',') !== FALSE) { echo " checked"; } ?> value="Delivery" name="pos[]">&nbsp;&nbsp;Delivery/Pickup
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Assembly".',') !== FALSE) { echo " checked"; } ?> value="Assembly" name="pos[]">&nbsp;&nbsp;Assembly
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Tax".',') !== FALSE) { echo " checked"; } ?> value="Tax" name="pos[]">&nbsp;&nbsp;Tax
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Total Price".',') !== FALSE) { echo " checked"; } ?> value="Total Price" name="pos[]">&nbsp;&nbsp;Total Price
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Payment Type".',') !== FALSE) { echo " checked"; } ?> value="Payment Type" name="pos[]">&nbsp;&nbsp;Payment Type
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Comment".',') !== FALSE) { echo " checked"; } ?> value="Comment" name="pos[]">&nbsp;&nbsp;Comment
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Tax Exemption".',') !== FALSE) { echo " checked"; } ?> value="Tax Exemption" name="pos[]">&nbsp;&nbsp;Tax Exemption
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Created/Sold By".',') !== FALSE) { echo " checked"; } ?> value="Created/Sold By" name="pos[]">&nbsp;&nbsp;Created/Sold By
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Ship Date".',') !== FALSE) { echo " checked"; } ?> value="Ship Date" name="pos[]">&nbsp;&nbsp;Ship Date
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Products".',') !== FALSE) { echo " checked"; } ?> value="Products" name="pos[]">&nbsp;&nbsp;Inventory
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Misc Item".',') !== FALSE) { echo " checked"; } ?> value="Misc Item" name="pos[]">&nbsp;&nbsp;Misc Item
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."servServices".',') !== FALSE) { echo " checked"; } ?> value="servServices" name="pos[]">&nbsp;&nbsp;Services
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."prodProducts".',') !== FALSE) { echo " checked"; } ?> value="prodProducts" name="pos[]">&nbsp;&nbsp;Products
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Deposit Paid".',') !== FALSE) { echo " checked"; } ?> value="Deposit Paid" name="pos[]">&nbsp;&nbsp;Deposit Paid
                        </td>
                    </tr>
					<tr>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Due Date".',') !== FALSE) { echo " checked"; } ?> value="Due Date" name="pos[]">&nbsp;&nbsp;Due Date
                        </td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Service Queue".',') !== FALSE) { echo " checked"; } ?> value="Service Queue" name="pos[]">&nbsp;&nbsp;Service Queue
						</td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Hold Orders".',') !== FALSE) { echo " checked"; } ?> value="Hold Orders" name="pos[]">&nbsp;&nbsp;Hold Orders
                        </td>
						<td>&nbsp;&nbsp;</td>
						<td>&nbsp;&nbsp;</td>
					</tr>
                </table>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_buttons" >
                    Choose Buttons for POS Touch Input<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_buttons" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td width="25%">
                            <input type="checkbox" <?php if (strpos($value_config, ',Add Item,') !== FALSE) { echo " checked"; } ?> value="Add Item" name="pos[]">&nbsp;&nbsp;Add Item
                        </td>
                        <td width="25%">
                            <input type="checkbox" <?php if (strpos($value_config, ',Touch Discount,') !== FALSE) { echo " checked"; } ?> value="Touch Discount" name="pos[]">&nbsp;&nbsp;Discount
                        </td>
                        <td width="25%">
                            <input type="checkbox" <?php if (strpos($value_config, ',Coupon,') !== FALSE) { echo " checked"; } ?> value="Coupon" name="pos[]">&nbsp;&nbsp;Coupon
                        </td>
                        <td width="25%">
                            <input type="checkbox" <?php if (strpos($value_config, ',Email Receipt,') !== FALSE) { echo " checked"; } ?> value="Email Receipt" name="pos[]">&nbsp;&nbsp;Email Receipt
                        </td>
					</tr>
					<tr>
                        <td width="25%">
                            <input type="checkbox" <?php if (strpos($value_config, ',Write-Off,') !== FALSE) { echo " checked"; } ?> value="Write-Off" name="pos[]">&nbsp;&nbsp;Write-Off
                        </td>
                        <td width="25%">
                            <input type="checkbox" <?php if (strpos($value_config, ',Bill of Materials,') !== FALSE) { echo " checked"; } ?> value="Bill of Materials" name="pos[]">&nbsp;&nbsp;Bill of Materials (Consumables)
                        </td>
						<td width="25%"><input type="checkbox" <?php if (strpos($value_config, ',Gift Card,') !== FALSE) { echo " checked"; } ?> value="Gift Card" name="pos[]">&nbsp;&nbsp;Gift Card</td>
						<td width="25%">&nbsp;&nbsp;</td>
                    </tr>
                </table>
				<div class="col-sm-12">
					<div class="pad-5">Choose New Customer Category</div><?php
					$pos_new_customer = get_config($dbc, 'pos_new_customer');
					$contact_categories	 = str_replace('Staff', '', get_config($dbc, 'contacts_tabs'));
					$contact_categories	.= str_replace('Staff', '', get_config($dbc, 'contacts3_tabs'));
					$each_category		= array_unique ( explode(',', $contact_categories) );

					for ( $i=0; $i<count($each_category); $i++ ) {
						$checked = ( $pos_new_customer == $each_category[$i] ) ? ' checked="checked"' : '';
						echo '<div class="col-sm-3"><label class="offset-right-5"><input type="radio"' . $checked . ' name="pos_new_customer" value="' . $each_category[$i] . '" /></label>' . $each_category[$i] . '</div>';
					} ?>
				</div>
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
                        <td width="20%">
                            <input type="checkbox" <?php if (strpos($value_config, ','."servCategory".',') !== FALSE) { echo " checked"; } ?> value="servCategory" name="pos[]">&nbsp;&nbsp;Category
                        </td>
                        <td width="20%">
                            <input type="checkbox" <?php if (strpos($value_config, ','."servHeading".',') !== FALSE) { echo " checked"; } ?> value="servHeading" name="pos[]">&nbsp;&nbsp;Heading
                        </td>
                        <td width="20%">
                            <input type="checkbox" <?php if (strpos($value_config, ','."servPrice".',') !== FALSE) { echo " checked"; } ?> value="servPrice" name="pos[]">&nbsp;&nbsp;Price
                        </td>
                        <td width="20%">
                            <input type="checkbox" <?php if (strpos($value_config, ','."servPriceEdit".',') !== FALSE) { echo " checked"; } ?> value="servPriceEdit" name="pos[]">&nbsp;&nbsp;Enable Price Editing
                        </td>
                        <td width="20%">
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
                            <input type="checkbox" <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" name="pos[]">&nbsp;&nbsp;Admin Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { echo " checked"; } ?> value="Client Price" name="pos[]">&nbsp;&nbsp;Client Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" name="pos[]">&nbsp;&nbsp;Commercial Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Distributor Price".',') !== FALSE) { echo " checked"; } ?> value="Distributor Price" name="pos[]">&nbsp;&nbsp;Distributor Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" name="pos[]">&nbsp;&nbsp;Final Retail Price
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" name="pos[]">&nbsp;&nbsp;Wholesale Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Preferred Price".',') !== FALSE) { echo " checked"; } ?> value="Preferred Price" name="pos[]">&nbsp;&nbsp;Preferred Price
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) { echo " checked"; } ?> value="Purchase Order Price" name="pos[]">&nbsp;&nbsp;Purchase Order Price
                        </td>
						<td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) { echo " checked"; } ?> value="Sales Order Price" name="pos[]">&nbsp;&nbsp;<?= SALES_ORDER_NOUN ?> Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Web Price".',') !== FALSE) { echo " checked"; } ?> value="Web Price" name="pos[]">&nbsp;&nbsp;Web Price
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
                    Choose Fields for POS Dashboard<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_dashboard" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT pos_dashboard FROM field_config"));
                $value_config = ','.$get_field_config['pos_dashboard'].',';
                ?>

                <table border='2' cellpadding='10' class='table'>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Invoice #".',') !== FALSE) { echo " checked"; } ?> value="Invoice #" name="pos_dashboard[]">&nbsp;&nbsp;Invoice #
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Invoice Date".',') !== FALSE) { echo " checked"; } ?> value="Invoice Date" name="pos_dashboard[]">&nbsp;&nbsp;Invoice Date
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Customer".',') !== FALSE) { echo " checked"; } ?> value="Customer" name="pos_dashboard[]">&nbsp;&nbsp;Customer
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Total Price".',') !== FALSE) { echo " checked"; } ?> value="Total Price" name="pos_dashboard[]">&nbsp;&nbsp;Total Price
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Payment Type".',') !== FALSE) { echo " checked"; } ?> value="Payment Type" name="pos_dashboard[]">&nbsp;&nbsp;Payment Type
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Invoice PDF".',') !== FALSE) { echo " checked"; } ?> value="Invoice PDF" name="pos_dashboard[]">&nbsp;&nbsp;Invoice PDF
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Comment".',') !== FALSE) { echo " checked"; } ?> value="Comment" name="pos_dashboard[]">&nbsp;&nbsp;Comment
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" name="pos_dashboard[]">&nbsp;&nbsp;Status
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Send") !== FALSE) { echo " checked"; } ?> value="Send" name="pos_dashboard[]">&nbsp;&nbsp;Send
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ','."Delivery/Shipping Type".',') !== FALSE) { echo " checked"; } ?> value="Delivery/Shipping Type" name="pos_dashboard[]">&nbsp;&nbsp;Delivery/Shipping Type
                        </td>

                    </tr>
                </table>
            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_coupons" >
                    Choose Fields for Coupons Dashboard<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_coupons" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">
                <?php
                $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `pos_dashboard` FROM `field_config`"));
                $value_config = ','.$get_field_config['pos_dashboard'].',';
                ?>

                <table border="2" cellpadding="10" class="table">
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ',Coupon ID,') !== FALSE) { echo " checked"; } ?> value="Coupon ID" name="pos_dashboard[]">&nbsp;&nbsp;Coupon ID
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ',Coupon Title,') !== FALSE) { echo " checked"; } ?> value="Coupon Title" name="pos_dashboard[]">&nbsp;&nbsp;Title
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ',Coupon Description,') !== FALSE) { echo " checked"; } ?> value="Coupon Description" name="pos_dashboard[]">&nbsp;&nbsp;Description
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ',Discount Type,') !== FALSE) { echo " checked"; } ?> value="Discount Type" name="pos_dashboard[]">&nbsp;&nbsp;Discount Type
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ',Discount,') !== FALSE) { echo " checked"; } ?> value="Discount" name="pos_dashboard[]">&nbsp;&nbsp;Coupon Value
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ',Start Date,') !== FALSE) { echo " checked"; } ?> value="Start Date" name="pos_dashboard[]">&nbsp;&nbsp;Start Date
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ',Expiry Date,') !== FALSE) { echo " checked"; } ?> value="Expiry Date" name="pos_dashboard[]">&nbsp;&nbsp;Expiry Date
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ',# of Times Used,') !== FALSE) { echo " checked"; } ?> value="# of Times Used" name="pos_dashboard[]">&nbsp;&nbsp;# of Times Used
                        </td>
                        <td>
                            <input type="checkbox" <?php if (strpos($value_config, ',Function,') !== FALSE) { echo " checked"; } ?> value="Function" name="pos_dashboard[]">&nbsp;&nbsp;Function
                        </td>
                        <td>&nbsp;&nbsp;</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

		<div class="panel panel-default">
	        <div class="panel-heading">
	            <h4 class="panel-title">
	                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_gf" >
	                    Choose Fields for Gift Card Dashboard<span class="glyphicon glyphicon-plus"></span>
	                </a>
	            </h4>
	        </div>

	        <div id="collapse_gf" class="panel-collapse collapse">
	            <div class="panel-body" id="no-more-tables">
	                <table border='2' cellpadding='10' class='table'>
	                    <tr>
													<td>
	                            <input type="checkbox" <?php if (strpos($value_config, ','."Gift Card ID".',') !== FALSE) { echo " checked"; } ?> value="Gift Card ID" name="pos_dashboard[]">&nbsp;&nbsp;Gift Card ID
	                        </td>
													<td>
	                            <input type="checkbox" <?php if (strpos($value_config, ','."Gift Card Number".',') !== FALSE) { echo " checked"; } ?> value="Gift Card Number" name="pos_dashboard[]">&nbsp;&nbsp;Gift Card Number
	                        </td>
	                        <td>
	                            <input type="checkbox" <?php if (strpos($value_config, ','."Description_GF".',') !== FALSE) { echo " checked"; } ?> value="Description_GF" name="pos_dashboard[]">&nbsp;&nbsp;Description
	                        </td>
													<td>
	                            <input type="checkbox" <?php if (strpos($value_config, ','."Created By".',') !== FALSE) { echo " checked"; } ?> value="Created By" name="pos_dashboard[]">&nbsp;&nbsp;Created By
	                        </td>
													<td>
	                            <input type="checkbox" <?php if (strpos($value_config, ','."Status_GF".',') !== FALSE) { echo " checked"; } ?> value="Status_GF" name="pos_dashboard[]">&nbsp;&nbsp;Used Value
	                        </td>
	                        <td>
	                            <input type="checkbox" <?php if (strpos($value_config, ','."Gift Card Value".',') !== FALSE) { echo " checked"; } ?> value="Gift Card Value" name="pos_dashboard[]">&nbsp;&nbsp;Gift Card Value
	                        </td>
	                        <td>
	                            <input type="checkbox" <?php if (strpos($value_config, ','."Issue Date GF".',') !== FALSE) { echo " checked"; } ?> value="Issue Date GF" name="pos_dashboard[]">&nbsp;&nbsp;Issue Date
	                        </td>
													<td>
														 <input type="checkbox" <?php if (strpos($value_config, ','."Expiry Date GF".',') !== FALSE) { echo " checked"; } ?> value="Expiry Date GF" name="pos_dashboard[]">&nbsp;&nbsp;Expiry Date
												 </td>
	                    </tr>
	                </table>
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
                    <label for="office_country" class="col-sm-4 control-label">Title of P.O.S. Tile on the Home Screen:</label>
                    <div class="col-sm-8">
                      <input name="pos_tile_titler" value="<?php if(get_config($dbc, 'pos_tile_titler') == '' || get_config($dbc, 'pos_tile_titler') == NULL ) { echo POS_ADVANCE_TILE; } else { echo get_config($dbc, 'pos_tile_titler'); } ?>" type="text" class="form-control"/>
                    </div>
                </div>

            </div>
        </div>
    </div>

	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_archiveinvoicer" >
                   Archiving Invoices<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_archiveinvoicer" class="panel-collapse collapse">
            <div class="panel-body">
				<?php include_once ('javascript_include.php'); ?>
                <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Auto-Archive Invoices After X Days:</label>
                    <div class="col-sm-8">
					  Enabled:  <input onclick="handleClick(this);" <?php if(get_config($dbc, 'archive_after_num_days') !== '' && get_config($dbc, 'archive_after_num_days') !== NULL) { echo "checked"; } ?> type='radio' name='yesornoarchiveinvoice' class='yesornoarchiveinvoice' value='yes'>
					  Disabled: <input onclick="handleClick(this);" <?php if(get_config($dbc, 'archive_after_num_days') == '' || get_config($dbc, 'archive_after_num_days') == NULL) { echo "checked"; } ?> type='radio' name='yesornoarchiveinvoice' class='yesornoarchiveinvoice' value='no'>
                    </div>

                </div>

				<div class="form-group hide_numofdays" <?php if(get_config($dbc, 'archive_after_num_days') == '' || get_config($dbc, 'archive_after_num_days') == NULL) { echo "style='display:none;'"; } ?>>
                    <label for="office_country" class="col-sm-4 control-label hide_numofdays">Number of Days Until Invoices get Archived After Creation Date:</label>
                    <div class="col-sm-8 hide_numofdays">
					  <input name="archive_after_num_days" value="<?php echo get_config($dbc, 'archive_after_num_days'); ?>" type="number" class="form-control hide_numofdays2"/>
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
						$invoice_pay_typers = str_replace('Pay Now,Net 30,Net 60,Net 90,Net 120,', '', get_config($dbc, 'invoice_payment_types'));
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
                   Outbound Invoice Email<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_out" class="panel-collapse collapse">
            <div class="panel-body" id="no-more-tables">

                <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Outbound Invoice Email:<br><em>(separate by a comma)</em></label>
                    <div class="col-sm-8">
                      <input name="invoice_outbound_email" value="<?php echo get_config($dbc, 'invoice_outbound_email'); ?>" type="text" class="form-control"/>
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
                $value_config = get_config($dbc, 'pos_tax');

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
                    <label for="office_country" class="col-sm-4 control-label">Invoice PDF Footer Text:<br><em>(e.g. - company name, address, phone, etc.)</em></label>
                    <div class="col-sm-8">
                      <input name="invoice_footer" value="<?php echo get_config($dbc, 'invoice_footer'); ?>" type="text" class="form-control"/>
                    </div>
                </div>

            </div>
        </div>
    </div>


	<div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_payar" >
                   Pay Multiple A/R Invoices<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_payar" class="panel-collapse collapse">
            <div class="panel-body">
                <div class="form-group">
                    <label for="office_country" class="col-sm-4 control-label">Pay Multiple A/R Invoices:</label>
                    <div class="col-sm-8">
					  Enabled:  <input <?php if(get_config($dbc, 'pay_multiple_ar_invoices') !== '' && get_config($dbc, 'pay_multiple_ar_invoices') !== NULL) { echo "checked"; } ?> type='radio' name='pay_multiple_ar_invoices' class='yesornopaymultipleinvoice' value='yes'>
					  Disabled: <input <?php if(get_config($dbc, 'pay_multiple_ar_invoices') == '' || get_config($dbc, 'pay_multiple_ar_invoices') == NULL) { echo "checked"; } ?> type='radio' name='pay_multiple_ar_invoices' class='yesornopaymultipleinvoice' value='no'>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<div class="form-group">
    <div class="col-sm-6">
        <a href="point_of_sell.php" class="btn brand-btn btn-lg">Back</a>
		<!--<a href="#" class="btn config-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
	</div>
	<div class="col-sm-6">
        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
    </div>
</div>

</form>
</div>
</div>
<?php include ('../footer.php'); ?>
