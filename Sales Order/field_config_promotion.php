<?php include_once('../include.php');
checkAuthorised('sales_order');
/*
Dashboard
*/
include ('../include.php');
error_reporting(0);

if (isset($_POST['submit'])) {

    //Tax
    $pos_promotion = '';
    for($i = 0; $i < count($_POST['pos_promotion_inventoryid']); $i++) {
        if($_POST['pos_promotion_inventoryid'][$i] != '') {
            $pos_promotion .= filter_var($_POST['pos_promotion_inventoryid'][$i],FILTER_SANITIZE_STRING).'**'.$_POST['pos_promotion_qty'][$i].'**'.$_POST['pos_promotion_pricing'][$i].'*#*';
        }
    }

    $pos_promotion = rtrim($pos_promotion, "*#*'");
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='sales_order_promotion'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$pos_promotion' WHERE name='sales_order_promotion'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('sales_order_promotion', '$pos_promotion')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Tax
    echo '<script type="text/javascript"> window.location.replace("field_config_promotion.php"); </script>';

}
?>
<script type="text/javascript">
$(document).ready(function() {
    var add_new_v = 1;
    $('#add_tax_button').on( 'click', function () {
        var clone = $('.additional_tax').clone();

        clone.find('.form-control').val('');
        clone.find('#pos_pricing_0').attr('id', 'pos_pricing_'+add_new_v);
        clone.find('#pos_product_0').attr('id', 'pos_product_'+add_new_v);

        clone.removeClass("additional_tax");
        $('#add_here_new_tax').append(clone);

        resetChosen($("#pos_pricing_"+add_new_v));
        resetChosen($("#pos_product_"+add_new_v));

        add_new_v++;
        return false;
    });
});
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1><?= SALES_ORDER_TILE ?> (Promotions)</h1>
<div class="pad-left gap-top double-gap-bottom"><a href="index.php" class="btn config-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<div class="tab-container mobile-100-container double-gap-top double-gap-bottom">
	<div class="pull-left tab">
		<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to create your desired fields for <?= SALES_ORDER_NOUN ?>."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<a href="field_config_pos.php"><button type="button" class="btn brand-btn mobile-block">General</button></a>
	</div>
	<div class="pull-left tab">
		<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to create Promotional items."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<a href="field_config_promotion.php"><button type="button" class="btn brand-btn mobile-block active_tab">Promotion</button></a>
	</div>
    <div class="pull-left tab">
        <span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to create Staff Collaboration Groups."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        <a href="field_config_staff_groups.php"><button type="button" class="btn brand-btn mobile-block">Staff Collaboration Groups</button></a>
    </div>
	
	<div class="clearfix double-gap-bottom"></div>
</div>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal double-gap-top" role="form">

<div class="panel-group" id="accordion2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tax" >
                    Promotion<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_tax" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="form-group clearfix">
                    <label class="col-sm-2 text-center">Inventory</label>
                    <label class="col-sm-2 text-center">Qty(This Or More)</label>
                    <label class="col-sm-2 text-center">Pricing</label>
                </div>

                <?php
                $value_config = get_config($dbc, 'sales_order_promotion');

                $pos_promotion = explode('*#*',$value_config);
                $id_loop = 500;
                $total_count = mb_substr_count($value_config,'*#*');
                for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
                    $pos_promotion_data = explode('**',$pos_promotion[$eq_loop]);
                    ?>

                    <div class="clearfix"></div>
                    <div class="form-group clearfix">
                      <div class="col-sm-2">
                            <select data-placeholder="Choose a Product..." name="pos_promotion_inventoryid[]" id="<?php echo 'pos_product_'.$id_loop; ?>" class="chosen-select-deselect form-control product" style="position:relative;">
                                <option value=""></option>
                                <?php
                                $query = mysqli_query($dbc,"SELECT inventoryid, part_no, name FROM inventory WHERE deleted=0 order by part_no");
                                while($row = mysqli_fetch_array($query)) {
                                    if($pos_promotion_data[0] == $row['inventoryid']) {
                                        $selected = ' selected';
                                    } else {
                                        $selected = '';
                                    }
                                    ?><option <?php echo $selected; ?> value='<?php echo $row['inventoryid'];?>'><?php echo $row['part_no'].' : '.decryptIt($row['name']);?></option><?php
                                }
                                ?>
                            </select>

                        </div>
                        <div class="col-sm-2">
                            <input name="pos_promotion_qty[]" value="<?php echo $pos_promotion_data[1]; ?>" type="text" class="form-control category" />
                        </div>
                        <div class="col-sm-2">
                            <select data-placeholder="Choose a Pricing..." id="<?php echo 'pos_pricing_'.$id_loop; ?>" name="pos_promotion_pricing[]" class="chosen-select-deselect form-control" width="380">
                                <option value=""></option>
                                <option <?php if ($pos_promotion_data[2] == "client_price") { echo " selected"; } ?> value="client_price">Client Price</option>
                                <option <?php if ($pos_promotion_data[2] == "admin_price") { echo " selected"; } ?> value="admin_price">Admin Price</option>
                                <option <?php if ($pos_promotion_data[2] == "commercial_price") { echo " selected"; } ?> value="commercial_price">Commercial Price</option>
                                <option <?php if ($pos_promotion_data[2] == "wholesale_price") { echo " selected"; } ?> value="wholesale_price">Wholesale Price</option>
                                <option <?php if ($pos_promotion_data[2] == "final_retail_price") { echo " selected"; } ?> value="final_retail_price">Final Retail Price</option>
                                <option <?php if ($pos_promotion_data[2] == "preferred_price") { echo " selected"; } ?> value="preferred_price">Preferred Price</option>
                                <option <?php if ($pos_promotion_data[2] == "web_price") { echo " selected"; } ?> value="web_price">Web Price</option>
                            </select>
                        </div>
                    </div>
                <?php } ?>

                <div class="additional_tax">
                <div class="clearfix"></div>
                <div class="form-group clearfix" width="100%">
                    <div class="col-sm-2">
                        <select data-placeholder="Choose a Product..." name="pos_promotion_inventoryid[]" class="chosen-select-deselect form-control" id="pos_product_0" style="position:relative;">
                            <option value=""></option>
                            <?php
                            $query = mysqli_query($dbc,"SELECT inventoryid, part_no, name FROM inventory WHERE deleted=0 order by name");
                            while($row = mysqli_fetch_array($query)) {
                                ?><option value='<?php echo $row['inventoryid'];?>'><?php echo $row['part_no'].' : '.decryptIt($row['name']);?></option><?php
                            }
                            ?>
                        </select>
                    </div>
                    <div class="col-sm-2">
                        <input name="pos_promotion_qty[]" value="0" type="text" class="form-control category" />
                    </div>
                    <div class="col-sm-2">
                        <select data-placeholder="Choose a Pricing..." name="pos_promotion_pricing[]" class="chosen-select-deselect form-control" id="pos_pricing_0" width="380">
                            <option value=""></option>
                            <option value="client_price">Client Price</option>
                            <option value="admin_price">Admin Price</option>
                            <option value="commercial_price">Commercial Price</option>
                            <option value="wholesale_price">Wholesale Price</option>
                            <option value="final_retail_price">Final Retail Price</option>
                            <option value="preferred_price">Preferred Price</option>
                            <option value="web_price">Web Price</option>
                        </select>
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

</div>

<div class="form-group">
    <div class="col-sm-6">
        <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your <?= SALES_ORDER_NOUN ?> Promotional settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        <a href="index.php" class="btn config-btn btn-lg">Back</a>
		<!--<a href="#" class="btn config-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
	</div>
	<div class="col-sm-6">
        <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
		<span class="popover-examples list-inline pull-right" style="margin:15px 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click this to finalize <?= SALES_ORDER_NOUN ?> Promotional settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
    </div>
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>

<div class="pull-right gap-top gap-right gap-bottom">
    <a href="index.php" class="btn brand-btn">Cancel</a>
    <button type="submit" id="save_config" name="save_config" value="Submit" class="btn brand-btn">Submit</button>
</div>