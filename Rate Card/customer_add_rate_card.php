<?php
//Rate Card Tiles

if (isset($_POST['submit'])) {
	require_once('../include.php');
    $who_added = $_SESSION['contactid'];
    $when_added = date('Y-m-d');

    $clientid = filter_var($_POST['ratecardclientid'],FILTER_SANITIZE_STRING);
    $rate_card_name = filter_var($_POST['rate_card_name'],FILTER_SANITIZE_STRING);
    $ref_card = filter_var($_POST['ref_card'],FILTER_SANITIZE_STRING);
    $start_date = filter_var($_POST['start_date'],FILTER_SANITIZE_STRING);
    $end_date = filter_var($_POST['end_date'],FILTER_SANITIZE_STRING);
    $alert_date = filter_var($_POST['alert_date'],FILTER_SANITIZE_STRING);
    $alert_staff = filter_var(implode(',',$_POST['alert_staff']),FILTER_SANITIZE_STRING);
    $frequency_type = filter_var($_POST['frequency_type'],FILTER_SANITIZE_STRING);
    $frequency_interval = filter_var($_POST['frequency_interval'],FILTER_SANITIZE_STRING);
    $total_price = 0;

    $package = '';
    $j=0;
    if(!empty($_POST['packageid'])) {
        foreach ($_POST['packageid'] as $packageid_all) {
            $package .= $packageid_all.'#'.$_POST['packagefinalprice'][$j].'**';
            $total_price += $_POST['packagefinalprice'][$j];
            $j++;
        }
    }

    $promotion = '';
    $j=0;
    if(!empty($_POST['promotionid'])) {
        foreach ($_POST['promotionid'] as $promotionid_all) {
            $promotion .= $promotionid_all.'#'.$_POST['promotionfinalprice'][$j].'**';
            $total_price += $_POST['promotionfinalprice'][$j];
            $j++;
        }
    }

    $custom = '';
    $j=0;
    if(!empty($_POST['customid'])) {
        foreach ($_POST['customid'] as $customid_all) {
            $custom .= $customid_all.'#'.$_POST['customfinalprice'][$j].'**';
            $total_price += $_POST['customfinalprice'][$j];
            $j++;
        }
    }

    $material = '';
    $j=0;
    if(!empty($_POST['materialid'])) {
        foreach ($_POST['materialid'] as $materialid_all) {
            $material .= $materialid_all.'#'.$_POST['mfinalprice'][$j].'**';
            $total_price += $_POST['mfinalprice'][$j];
            $j++;
        }
    }

    $services = '';
    $service_comments = '';
    $j=0;
    if(!empty($_POST['serviceid'])) {
        foreach ($_POST['serviceid'] as $serviceid_all) {
            $services .= $serviceid_all.'#'.$_POST['sfinalprice'][$j].'#'.$_POST['sunitmeasure'][$j].'**';
            $total_price += $_POST['sfinalprice'][$j];
            $j++;
        }
        foreach ($_POST['service_comments'] as $service_comment) {
            $service_comments .= $service_comment.'#*#';
        }
    }

    $products = '';
    $j=0;
    if(!empty($_POST['productid'])) {
        foreach ($_POST['productid'] as $productid_all) {
            $products .= $productid_all.'#'.$_POST['pfinalprice'][$j].'**';
            $total_price += $_POST['pfinalprice'][$j];
            $j++;
        }
    }

    $sred = '';
    $j=0;
    if(!empty($_POST['sredid'])) {
        foreach ($_POST['sredid'] as $sredid_all) {
            $sred .= $sredid_all.'#'.$_POST['sredfinalprice'][$j].'**';
            $total_price += $_POST['sredfinalprice'][$j];
            $j++;
        }
    }

    $staff = '';
    $j=0;
    if(!empty($_POST['contactid'])) {
        foreach ($_POST['contactid'] as $contactid_all) {
            $staff .= $contactid_all.'#'.$_POST['stfinalprice'][$j].'**';
            $total_price += $_POST['stfinalprice'][$j];
            $j++;
        }
    }

    $staff_position = '';
    $j=0;
    if(!empty($_POST['staff_pos'])) {
        foreach ($_POST['staff_pos'] as $j => $staff_pos_all) {
			if($staff_pos_all > 0) {
				$staff_position .= $staff_pos_all.'#'.($_POST['staff_pos_unit'] == 'Daily' ? 0 : $_POST['staff_pos_rate'][$j]).'#'.($_POST['staff_pos_unit'] == 'Daily' ? $_POST['staff_pos_rate'][$j] : 0).'**';
				$total_price += $_POST['staff_pos_rate'][$j];
			}
        }
    }

    $contractor = '';
    $j=0;
    if(!empty($_POST['contractorid'])) {
        foreach ($_POST['contractorid'] as $contractorid_all) {
            $contractor .= $contractorid_all.'#'.$_POST['cntfinalprice'][$j].'**';
            $total_price += $_POST['cntfinalprice'][$j];
            $j++;
        }
    }

    $client = '';
    $j=0;
    if(!empty($_POST['clientid'])) {
        foreach ($_POST['clientid'] as $clientid_all) {
            $client .= $clientid_all.'#'.$_POST['clfinalprice'][$j].'**';
            $total_price += $_POST['clfinalprice'][$j];
            $j++;
        }
    }

    $vendor = '';
    $j=0;
    if(!empty($_POST['vendorperson'])) {
        foreach ($_POST['vendorperson'] as $vendorperson_all) {
            $vendor .= $vendorperson_all.'#'.$_POST['vfinalprice'][$j].'**';
            $total_price += $_POST['vfinalprice'][$j];
            $j++;
        }
    }

    $customer = '';
    $j=0;
    if(!empty($_POST['customerid'])) {
        foreach ($_POST['customerid'] as $customerid_all) {
            $customer .= $customerid_all.'#'.$_POST['custfinalprice'][$j].'**';
            $total_price += $_POST['custfinalprice'][$j];
            $j++;
        }
    }

    $inventory = '';
    $j=0;
    if(!empty($_POST['inventoryid'])) {
        foreach ($_POST['inventoryid'] as $inventoryid_all) {
            $inventory .= $inventoryid_all.'#'.$_POST['infinalprice'][$j].'**';
            $total_price += $_POST['infinalprice'][$j];
            $j++;
        }
    }

    $equipment = '';
    $j=0;
    if(!empty($_POST['equipmentid'])) {
        foreach ($_POST['equipmentid'] as $equipmentid_all) {
            $equipment .= $equipmentid_all.'#'.$_POST['eqfinalprice'][$j].'**';
            $total_price += $_POST['eqfinalprice'][$j];
            $j++;
        }
    }

    $equipment_category = '';
    $j=0;
    if(!empty($_POST['equipmentcategory'])) {
        foreach ($_POST['equipmentcategory'] as $equipmentcategory_all) {
            $equipment_category .= $equipmentcategory_all.'#'.$_POST['eq_cat_hourly_rate'][$j].'#'.$_POST['eq_cat_daily_rate'][$j].'**';
            $total_price += $_POST['eq_cat_hourly_rate'][$j];
            $total_price += $_POST['eq_cat_daily_rate'][$j];
            $j++;
        }
    }

    $labour = '';
    $j=0;
    if(!empty($_POST['labourid'])) {
        foreach ($_POST['labourid'] as $labourid_all) {
            $labour .= $labourid_all.'#'.$_POST['lfinalprice'][$j].'**';
            $total_price += $_POST['lfinalprice'][$j];
            $j++;
        }
    }

    $expense = '';
    $j=0;
    if(!empty($_POST['expensetype'])) {
        foreach ($_POST['expensetype'] as $expensetype_all) {
            $expense .= $expensetype_all.'#'.$_POST['expensecategory'][$j].'#'.$_POST['expfinalprice'][$j].'**';
            $total_price += $_POST['expfinalprice'][$j];
            $j++;
        }
    }

    $other = '';
    $j=0;
    if(!empty($_POST['other_detail'])) {
        foreach ($_POST['other_detail'] as $other_detail_all) {
            $other .= $other_detail_all.'#'.$_POST['other_detail'][$j].'#'.$_POST['otherfinalprice'][$j].'**';
            $total_price += $_POST['otherfinalprice'][$j];
            $j++;
        }
    }

    $mileage = filter_var($_POST['mileageprice'],FILTER_SANITIZE_STRING);

    if(empty($_POST['ratecardid'])) {
        $query_insert_customer = "INSERT INTO `rate_card` (`clientid`, `rate_card_name`, `ref_card`, `frequency_type`, `frequency_interval`, `package`, `promotion`, `services`, `products`, `sred`, `client`, `customer`, `inventory`, `equipment`, `equipment_category`, `staff`, `staff_position`, `contractor`, `expense`, `vendor`, `custom`, `material`, `labour`, `other`, `mileage`, `total_price`, `who_added`, `when_added`, `start_date`, `end_date`, `alert_date`, `alert_staff`, `created_by`) VALUES ('$clientid', '$rate_card_name', '$ref_card', '$frequency_type', '$frequency_interval', '$package' , '$promotion', '$services', '$products', '$sred', '$client', '$customer', '$inventory', '$equipment', '$equipment_category', '$staff', '$staff_position', '$contractor', '$expense', '$vendor', '$custom', '$material', '$labour', '$other', '$mileage', '$total_price', '$who_added', '$when_added', '$start_date', '$end_date', '$alert_date', '$alert_staff', '".$_SESSION['contactid']."')";
        $result_insert_customer = mysqli_query($dbc, $query_insert_customer);
        $url = 'Added';
				$before_change = '';
        $history = "Rate card entry is been added. <br />";
				add_update_history($dbc, 'ratecard_history', $history, '', $before_change);
    } else {
        $ratecardid = $_POST['ratecardid'];
        $query_update_vendor = "UPDATE `rate_card` SET `rate_card_name` = '$rate_card_name', `ref_card` = '$ref_card', `frequency_type` = '$frequency_type', `frequency_interval` = '$frequency_interval', `package` = '$package', `promotion` = '$promotion', `services` = '$services', `products` = '$products', `sred` = '$sred', `client` = '$client', `customer` = '$customer', `inventory` = '$inventory', `equipment` = '$equipment', `equipment_category` = '$equipment_category', `staff` = '$staff', `staff_position` = '$staff_position', `contractor` = '$contractor', `expense` = '$expense', `vendor` = '$vendor', `custom` = '$custom', `material` = '$material', `labour` = '$labour', `other` = '$other', `mileage` = '$mileage', `total_price` = '$total_price', `who_added` = '$who_added', `start_date` = '$start_date', `end_date` = '$end_date', `alert_date` = '$alert_date', `alert_staff` = '$alert_staff' WHERE `ratecardid` = '$ratecardid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
				$before_change = '';
        $history = "Rate card entry is been updated with Rate Card Name - $rate_card_name. <br />";
				add_update_history($dbc, 'ratecard_history', $history, '', $before_change);
    }
    
    foreach($_POST['comp_item'] as $i => $comp_item) {
        $comp_item = filter_var($comp_item,FILTER_SANITIZE_STRING);
        $comp_rate = filter_var($_POST['comp_percent'][$i],FILTER_SANITIZE_STRING);
        $comp_fee = filter_var($_POST['comp_fee'][$i],FILTER_SANITIZE_STRING);
        $comp_id = filter_var($_POST['comp_id'][$i],FILTER_SANITIZE_STRING);
        $deleted = filter_var($_POST['comp_deleted'][$i],FILTER_SANITIZE_STRING);
        if($comp_id > 0) {
            $dbc->query("UPDATE `rate_compensation` SET `rate_card`='$ratecardid', `item_type`='$comp_item', `comp_percent`='$comp_rate', `comp_fee`='$comp_fee', `deleted`='$deleted' WHERE `id`='$comp_id'");
        } else if(!empty($comp_item) && !empty($comp_rate)) {
            $dbc->query("INSERT INTO `rate_compensation` (`rate_card`, `item_type`, `comp_percent`, comp_fee`, `deleted`) VALUES ('$ratecardid', '$comp_item', '$comp_rate', '$comp_fee', '$deleted')");
        }
    }

    echo '<script type="text/javascript"> window.location.replace("?card=customer&type=customer&category='.config_safe_str(get_contact($dbc, $clientid, 'category')).'"); </script>';
}
?>
<script>
$(document).ready(function() {

});
$(document).on('change', 'select#ratecardcontactcategory', function() { filterCustomerContacts(); });
function filterCustomerContacts() {
    var option = $('#ratecardcontactcategory option:selected');
    $('[name="ratecardclientid"] option').hide().filter('[data-category='+option.val()+']').show();
    $('[name="ratecardclientid"]').trigger('change.select2');
}
function deleteRatecard(sel, hide, blank) {
	var typeId = sel.id;
	var arr = typeId.split('_');

    $("#"+hide+arr[1]).hide();
    $("#"+blank+arr[1]).val('');
    return false;
}
</script>
    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_fields` FROM `field_config_ratecard`"));
        $base_field_config = ','.$get_field_config['config_fields'].',';
        $customer_contact_categories = get_config($dbc, 'customer_rate_card_contact_categories');
        if(empty($customer_contact_categories)) {
            $customer_contact_categories = 'Business';
        }
        $customer_contact_categories = explode(',',$customer_contact_categories);

        $clientid = $_GET['clientid'];
        $rate_card_name = '';
        $ref_card = '';
        $start_date = '';
        $end_date = '';
        $alert_date = '';
        $alert_staff = '';
        $frequency_type = '';
        $frequency_interval = '';
        $package = '';
        $promotion = '';
        $services = '';
        $service_comments = '';
        $products = '';
        $sred = '';
        $client = '';
        $material = '';
        $inventory = '';
        $equipment = '';
        $equipment_category = '';
        $staff = '';
        $staff_position = '';
        $contractor = '';
        $customer = '';
        $expense = '';
        $vendor = '';
        $custom = '';
        $labour = '';
        $other = '';
        $disabled = '';
        $selected_contact_cat = '';
        $mileage = '';

        if(!empty($_GET['ratecardid'])) {
            $ratecardid = $_GET['ratecardid'];
            $ratecard = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `rate_card` WHERE `ratecardid`='$ratecardid'"));
            $clientid = $ratecard['clientid'];
            $rate_card_name = empty($ratecard['rate_card_name']) ? get_contact($dbc, $clientid, 'name_company') : $ratecard['rate_card_name'];
            $ref_card = $ratecard['ref_card'];
            $start_date = $ratecard['start_date'];
            $end_date = $ratecard['end_date'];
            $alert_date = $ratecard['alert_date'];
            $alert_staff =$ratecard['alert_staff'];
            $frequency_type = $ratecard['frequency_type'];
            $frequency_interval = $ratecard['frequency_interval'];
            $package = $ratecard['package'];
            $promotion = $ratecard['promotion'];
            $services = $ratecard['services'];
            $service_comments = $ratecard['service_comments'];
            $products = $ratecard['products'];
            $sred = $ratecard['sred'];
            $client = $ratecard['client'];
            $material = $ratecard['material'];
            $inventory = $ratecard['inventory'];
            $equipment = $ratecard['equipment'];
            $equipment_category = $ratecard['equipment_category'];
            $staff = $ratecard['staff'];
            $staff_position = $ratecard['staff_position'];
            $contractor = $ratecard['contractor'];
            $customer = $ratecard['customer'];
            $expense = $ratecard['expense'];
            $vendor = $ratecard['vendor'];
            $custom = $ratecard['custom'];
            $labour = $ratecard['labour'];
            $other = $ratecard['other'];
            $disabled = 'disabled';
            $selected_contact_cat = get_contact($dbc, $clientid, 'category');
            $mileage = $ratecard['mileage'];
            ?>
        <input type="hidden" id="ratecardid" name="ratecardid" value="<?php echo $ratecardid ?>" />
        <input type="hidden" name="ratecardclientid" value="<?php echo $clientid ?>" />
        <?php
        }
    ?>

    <?php
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `field_config_ratecard`"));
    $value_config = ','.$get_field_config['config_fields'].',';
    ?>
    <div class="panel-group" id="accordion2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_abi" >Rate Card Info<span class="glyphicon glyphicon-minus"></span></a>
                </h4>
            </div>

            <div id="collapse_abi" class="panel-collapse collapse in">
                <div class="panel-body">

                    <?php if(count($customer_contact_categories) > 1) { ?>
                        <div class="form-group clearfix completion_date">
                            <label for="first_name" class="col-sm-4 control-label text-right">Contact Category:</label>
                            <div class="col-sm-8">
                                <select id="ratecardcontactcategory" <?php echo $disabled; ?> data-placeholder="Select Category..." class="chosen-select-deselect form-control" width="380">
                                    <option value=''></option>
                                    <?php
                                    foreach ($customer_contact_categories as $contact_cat) {
                                        echo '<option value="'.$contact_cat.'" '.($selected_contact_cat == $contact_cat ? 'selected' : '').'>'.$contact_cat.'</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="form-group clearfix completion_date">
                        <label for="first_name" class="col-sm-4 control-label text-right">Generate For:</label>
                        <div class="col-sm-8">
                            <select name="ratecardclientid" <?php echo $disabled; ?> data-placeholder="Select Contact..." onchange="if($('[name=rate_card_name]').val() == '') { $('[name=rate_card_name]').val($(this).find('option:selected').text()) }" class="chosen-select-deselect form-control" width="380">
                                <option value=''></option>
                                <?php
                                $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT * FROM `contacts` WHERE `category` IN ('".implode("','", $customer_contact_categories)."') AND `deleted`=0"),MYSQLI_ASSOC));
                                foreach ($query as $row) {
                                    echo '<option value="'.$row.'" '.($row == $clientid ? 'selected' : '').' data-category="'.get_contact($dbc, $row, 'category').'">'.(!empty(get_client($dbc, $row)) ? get_client($dbc, $row) : get_contact($dbc, $row)).'</option>';
                                }
                                ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-group clearfix completion_date">
                        <label for="first_name" class="col-sm-4 control-label text-right">Rate Card Name:</label>
                        <div class="col-sm-8">
                            <input name="rate_card_name" value="<?php echo $rate_card_name; ?>" type="text" class="form-control"></p>
                        </div>
                    </div>

                    <?php if(strpos($value_config, ',ref_card,') !== FALSE) { ?>
                        <div class="form-group clearfix completion_date">
                            <label for="first_name" class="col-sm-4 control-label text-right">Reference Rate Card:</label>
                            <div class="col-sm-8">
                                <select name="ref_card" class="chosen-select-deselect" data-placeholder="Select Company Rate Card"><option />
									<?php $rate_list = $dbc->query("SELECT `rate_card_name` FROM `company_rate_card` WHERE IFNULL(`rate_card_name`,'') != '' AND `deleted`=0 GROUP BY `rate_card_name`");
									while($ref_rate_name = $rate_list->fetch_assoc()) { ?>
										<option <?= $ref_rate_name['rate_card_name'] == $ref_card ? 'selected' : '' ?> value="<?= $ref_rate_name['rate_card_name'] ?>"><?= $ref_rate_name['rate_card_name'] ?></option>
									<?php } ?>
								</select>
                            </div>
                        </div>
                    <?php } ?>

                    <?php if(strpos($value_config, ',start_end_dates,') !== FALSE) { ?>
                        <div class="form-group clearfix completion_date">
                            <label for="first_name" class="col-sm-4 control-label text-right">Start Date:</label>
                            <div class="col-sm-8">
                                <input name="start_date" value="<?php echo $start_date; ?>" type="text" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="form-group clearfix completion_date">
                            <label for="first_name" class="col-sm-4 control-label text-right">End Date:</label>
                            <div class="col-sm-8">
                                <input name="end_date" value="<?php echo $end_date; ?>" type="text" class="form-control datepicker">
                            </div>
                        </div>
                    <?php } ?>

                    <?php if(strpos($value_config, ',reminder_alerts,') !== FALSE) { ?>
                        <div class="form-group clearfix completion_date">
                            <label for="first_name" class="col-sm-4 control-label text-right">Alert Date:</label>
                            <div class="col-sm-8">
                                <input name="alert_date" value="<?php echo $alert_date; ?>" type="text" class="form-control datepicker">
                            </div>
                        </div>
                        <div class="form-group clearfix completion_date">
                            <label for="first_name" class="col-sm-4 control-label text-right">Alert Staff:</label>
                            <div class="col-sm-8">
                                <select name="alert_staff[]" multiple data-placeholder="Select Staff..." class="form-control chosen-select-deselect">
                                    <?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
                                    foreach($staff_list as $staffid) {
                                        echo '<option value="'.$staffid.'" '.(strpos(','.$alert_staff.',',','.$staffid.',') !== FALSE ? 'selected' : '').'>'.get_contact($dbc, $staffid).'</option>';
                                    } ?>
                                </select>
                            </div>
                        </div>
                    <?php } ?>

                </div>
            </div>
        </div>

        <?php if (strpos($value_config, ','."Customer Fields Freqeuncy".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_frequency" >Frequency<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_frequency" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_frequency.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Package".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_package" >Packages<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_package" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_package.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Promotion".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_promotion" >Promotion<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_promotion" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_promotion.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Custom".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_cus" >Custom<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_cus" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_custom.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Material".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Material" >Material<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_Material" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_material.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Services".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_service" >Services<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_service" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_services.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Products".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Products" >Products<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_Products" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_products.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."SRED".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_sred" >SR&ED<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_sred" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_sred.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff" >Staff<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_staff" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_staff.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Position".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staffpos" >Position<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_staffpos" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_position.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Contractor".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_contractor" >Contractor<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_contractor" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_contractor.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Clients".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_clients" >Clients<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_clients" class="panel-collapse collapse">
                <div class="panel-body">
                   <?php
                    include ('add_rate_card_clients.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Vendor Pricelist".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_vendor" >Vendor Pricelist<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_vendor" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_vendor.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Customer".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_customer" >Customer<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_customer" class="panel-collapse collapse">
                <div class="panel-body">
                   <?php
                    include ('add_rate_card_customer.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Inventory".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_inv" >Inventory<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_inv" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_inventory.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_equipment" >Equipment<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_equipment" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_equipment.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Labour".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Labour" >Labour<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_Labour" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_labour.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Equipment by Category".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_eqcat" >Equipment by Category<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_eqcat" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_equipment_category.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Mileage".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_mileage" >Mileage<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_mileage" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_mileage.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Expenses".',') !== FALSE) { /*
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_expenses" >Expenses<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_expenses" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_expenses.php');
                    ?>
                </div>
            </div>
        </div>
        <?php */} ?>

        <?php if (strpos($value_config, ','."Other".',') !== FALSE) { /*
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Other" >Other<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_Other" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_rate_card_other.php');
                    ?>
                </div>
            </div>
        </div>
        <?php */} ?>

        <?php if (strpos($value_config, ','."Compensation".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_comp" >Compensation<span class="glyphicon glyphicon-plus"></span></a>
                    </h4>
                </div>

                <div id="collapse_comp" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php include ('add_rate_card_comp.php'); ?>
                    </div>
                </div>
            </div>
        <?php } ?>

    </div>


    <div class="form-group">
        <div class="col-sm-12">
            <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
        </div>
    </div>

    </form>
