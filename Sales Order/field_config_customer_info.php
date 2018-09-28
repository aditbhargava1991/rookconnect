<?php include_once('../include.php');
checkAuthorised('sales_order');
/*
/* Sales Order Config - Fields
*/

if(isset($_POST['save_config'])) {
    $sales_order_type = $_POST['so_type'];

    $customer_cat = filter_var(implode(',',$_POST['customer_category']),FILTER_SANITIZE_STRING);
	$field_config = filter_var(implode(',',$_POST['so_fields']),FILTER_SANITIZE_STRING);

    if(!empty($sales_order_type)) {
        set_config($dbc, 'so_'.config_safe_str($sales_order_type).'_customer_category', $customer_cat);
        set_config($dbc, 'so_'.config_safe_str($sales_order_type).'_customer_fields', $field_config);
    } else {
    	mysqli_query($dbc, "INSERT INTO `field_config_so` (`customer_category`,`customer_fields`) SELECT '$customer_cat','$field_config' FROM (SELECT COUNT(*) rows FROM `field_config_so`) num WHERE num.rows = 0");
    	mysqli_query($dbc, "UPDATE `field_config_so` SET `customer_category` = '$customer_cat', `customer_fields` = '$field_config'");
    }
}

$field_list = [
    'Business Name',
    'First Name',
    'Last Name',
    'Region',
    'Location',
    'Classification',
    'Address',
    'Phone Number',
    'Email Address',
    'Payment Type',
    'Budget',
    'Preferred Booking Time',
    'Square Footage',
    'Number of Bathrooms',
    'Alarm System Information',
    'Pets',
    'Notification Type',
    'Extra Information'
];
?>

<script type="text/javascript">
$(document).on('change', 'select[name="so_type"]', function() { changeSOType(this); });
function changeSOType(sel) {
    window.location.href = "?tab=customer_info&so_type="+sel.value;
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
        $customer_cat = ','.$field_config['customer_category'].',';
        $value_config = ','.$field_config['customer_fields'].',';
        if(!empty($so_type)) {
            $customer_cat = ','.get_config($dbc, 'so_'.config_safe_str($so_type).'_customer_category').',';
            $value_config = ','.get_config($dbc, 'so_'.config_safe_str($so_type).'_customer_fields').',';
        }
        if($customer_cat == ',,') {
            $customer_cat = ',Business,';
        }
        if($value_config == ',,') {
            $value_config = ',Business Name,Region,Location,Classification,Phone Number,Email Address,';
        } ?>
        <div class="form-group">
            <label class="col-sm-4">Customer Category:</label>
            <div class="col-sm-8">
                <select multiple name="customer_category[]" class="chosen-select-deselect form-control">
                    <?php $all_cats = get_config($dbc, 'contacts_tabs').','.get_config($dbc, 'contactsrolodex_tabs').','.get_config($dbc, 'contacts3_tabs').','.get_config($dbc, 'clientinfo_tabs').','.get_config($dbc, 'members_tabs').','.get_config($dbc, 'vendors_tabs');
                    $all_cats = array_unique(array_filter(explode(',', $all_cats)));
                    asort($all_cats);
                    foreach ($all_cats as $contact_cat) {
                        echo '<option value="'.$contact_cat.'" '.(strpos($customer_cat, ','.$contact_cat.',') !== FALSE ? 'selected' : '').'>'.$contact_cat.'</option>';
                    } ?>
                </select>
            </div>
        </div>
        <table cellpadding='10' class='table table-bordered'>
        	<?php $i = 0;
        	foreach ($field_list as $field) {
        		if ($i == 0) {
        			echo '<tr>';
        		}
        		echo '<td><input type="checkbox" '.(strpos($value_config, ','.$field.',') !== FALSE ? 'checked' : '').' value="'.$field.'" style="height: 20px; width: 20px;" name="so_fields[]">&nbsp;&nbsp;'.$field.'</td>';
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