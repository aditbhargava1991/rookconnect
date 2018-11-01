<?php include_once('../include.php');
checkAuthorised('sales_order');
/*
/* Sales Order Config - Fields
*/

if(isset($_POST['save_config'])) {
    $sales_order_type = $_POST['so_type'];

    $field_config = filter_var(implode(',',$_POST['so_fields']),FILTER_SANITIZE_STRING);
    if(!empty($sales_order_type)) {
        set_config($dbc, 'so_'.config_safe_str($sales_order_type).'_fields', $field_config);
    } else {
        mysqli_query($dbc, "INSERT INTO `field_config_so` (`fields`) SELECT '$field_config' FROM (SELECT COUNT(*) rows FROM `field_config_so`) num WHERE num.rows = 0");
        mysqli_query($dbc, "UPDATE `field_config_so` SET `fields` = '$field_config'");
    }

    $field_config = filter_var(implode(',',$_POST['so_product_fields']),FILTER_SANITIZE_STRING);
    if(!empty($sales_order_type)) {
        set_config($dbc, 'so_'.config_safe_str($sales_order_type).'_product_fields', $field_config);
    } else {
        mysqli_query($dbc, "INSERT INTO `field_config_so` (`product_fields`) SELECT '$field_config' FROM (SELECT COUNT(*) rows FROM `field_config_so`) num WHERE num.rows = 0");
        mysqli_query($dbc, "UPDATE `field_config_so` SET `product_fields` = '$field_config'");
    }
}

$field_list = [
	'Sales Order Template',
    'Copy Sales Order',
	'Sales Order Name',
    'Primary Staff',
	'Assign Staff',
	'Staff Collaboration Groups',
	'Business Contact',
	'Classification',
	'Next Action',
	'Next Action Follow Up Date',
	'Logo',
	'Custom Designs',
	'Discount',
	'Delivery',
	'Assembly',
	'Payment Type',
	'Deposit Paid',
	'Comment',
	'Ship Date',
	'Due Date',
    'Frequency',
	'Notes'
];

$product_types = [
	'Inventory',
	'Vendor',
	'Services',
    'Labour',
    'Equipment'
];
?>

<script type="text/javascript">
$(document).on('change', 'select[name="so_type"]', function() { changeSOType(this); });
function changeSOType(sel) {
    window.location.href = "?tab=fields&so_type="+sel.value;
}
function checkFields() {
    if($('[name="so_fields[]"][value="Sales Order Name"]:checked').length > 0) {
        $('#sales_order_name_block').show();
    } else {
        $('#sales_order_name_block').hide();
    }
}
</script>

<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <div class="gap-top">
        <?php $sales_order_types = get_config($dbc, 'sales_order_types');
        $so_type = $_GET['so_type'];
        if(!empty($sales_order_types)) { ?>
            <div class="form-group">
                <label class="col-sm-4"><?= SALES_ORDER_NOUN ?> Type:</label>
                <div class="col-sm-8">
                    <select name="so_type" data-placeholder="Select a Type" class="chosen-select-deselect form-control">
                        <?php foreach(explode(',', $sales_order_types) as $sales_order_type) {
                            if(empty($so_type)) {
                                $so_type = $sales_order_type;
                            } ?>
                            <option value="<?= $sales_order_type ?>" <?= $sales_order_type == $so_type ? 'selected' : '' ?>><?= $sales_order_type ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
        <?php }
        $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_so`"));
        $value_config = ','.$field_config['fields'].',';
        $value_config_products = ','.$field_config['product_fields'].',';
        if(!empty($so_type)) {
            $value_config = ','.get_config($dbc, 'so_'.config_safe_str($so_type).'_fields').',';
            $value_config_products = ','.get_config($dbc, 'so_'.config_safe_str($so_type).'_product_fields').',';
        }
        ?>
    	<h4>Fields</h4>
        <table cellpadding='10' class='table table-bordered'>
        	<?php $i = 0;
        	foreach ($field_list as $field) {
        		if ($i == 0) {
        			echo '<tr>';
        		}
        		echo '<td><input type="checkbox" '.(strpos($value_config, ','.$field.',') !== FALSE ? 'checked' : '').' value="'.$field.'" style="height: 20px; width: 20px;" name="so_fields[]" onchange="checkFields();">&nbsp;&nbsp;'.$field.'</td>';
        		$i++;
        		if($i == 5) {
        			echo '</tr>';
        			$i = 0;
        		}
        	}
        	if($i != 0) {
        		echo '</tr>';
        	} ?>
        </table>

        <div id="sales_order_name_block" <?= strpos($value_config, ',Sales Order Name,') !== FALSE ? '' : 'style="display: none;"' ?>>
            <h4>Auto-Generate <?= SALES_ORDER_NOUN ?> Name</h4>
            <table cellpadding='10' class='table table-bordered'>
                <tr>
                    <td><input type="radio" name="so_fields[]" value="NO GENERATE" <?= strpos($value_config,',NO GENERATE,') !== FALSE ? 'checked' : '' ?>> No Generated Name</td>
                    <td><input type="radio" name="so_fields[]" value="Generate Name Customer Sotid" <?= strpos($value_config,',Generate Name Customer Sotid,') !== FALSE ? 'checked' : '' ?>> Use Customer Name and <?= SALES_ORDER_NOUN ?> Number</td>
                </tr>
            </table>
        </div>

        <h4>Product Types</h4>
        <table cellpadding='10' class='table table-bordered'>
        	<?php $i = 0;
        	foreach ($product_types as $field) {
        		if ($i == 0) {
        			echo '<tr>';
        		}
        		echo '<td><input type="checkbox" '.(strpos($value_config_products, ','.$field.',') !== FALSE ? 'checked' : '').' value="'.$field.'" style="height: 20px; width: 20px;" name="so_product_fields[]">&nbsp;&nbsp;'.$field.'</td>';
        		$i++;
        		if($i == 5) {
        			echo '</tr>';
        			$i = 0;
        		}
        	}
        	if($i != 0) {
        		echo '</tr>';
        	} ?>
        </table>
    </div>
    <div class="pull-right gap-top gap-right gap-bottom">
        <a href="index.php" class="btn brand-btn">Cancel</a>
        <button type="submit" id="save_config" name="save_config" value="Submit" class="btn brand-btn">Submit</button>
    </div>
</form>