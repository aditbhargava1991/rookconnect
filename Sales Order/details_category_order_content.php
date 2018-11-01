<?php include_once('../include.php');
include_once('../Sales Order/details_category_functions.php');
// error_reporting(E_ALL);
if($_GET['from_type'] == 'iframe') {
    $sotid = $_GET['sotid'];
    $so_type = $_GET['so_type'];
    $field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_so`"));
    $customer_cat = explode(',', $field_config['customer_category']);
    $customer_fields = ','.$field_config['customer_fields'].',';
    $value_config = ','.$field_config['fields'].',';
    if(!empty($so_type)) {
        $customer_cat = explode(',', get_config($dbc, 'so_'.config_safe_str($so_type).'_customer_category'));
        $customer_fields = ','.get_config($dbc, 'so_'.config_safe_str($so_type).'_customer_fields').',';
        $value_config = ','.get_config($dbc, 'so_'.config_safe_str($so_type).'_fields').',';
    }
    if(empty($customer_cat)) {
        $customer_cat = ['Business'];
    }
    if($customer_fields == ',,') {
        $customer_fields = ',Business Name,Region,Location,Classification,Phone Number,Email Address,';
    }
    $cat_config = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_contacts` WHERE `sales_order_type` = '$so_type'"),MYSQLI_ASSOC);
    if(empty($cat_config)) {
        $no_cat = true;
    }
}
$contact_category = $contact_cat['contact_category'];
$item_from_types = explode(',',$field_config['product_fields']);
if(!empty($so_type)) {
    $item_from_types = explode(',',get_config($dbc,'so_'.config_safe_str($so_type).'_product_fields'));
} ?>
<div class="accordion-block-details padded contact_type_div" id="<?= !$no_cat ? $contact_category : 'nocat' ?>_order" data-contact-category="<?= !$no_cat ? $contact_category : '**no_cat**' ?>">
    <div class="accordion-block-details-heading"><h4><?= !$no_cat ? $contact_category : 'Sales' ?> Order Details</h4></div>
    <?php foreach ($item_from_types as $item_from) {
        $include_hours = false;
        if($item_from == 'Services') {
            $service_fields = ','.get_field_config($dbc,'services').',';
            if(strpos($service_fields, ',Estimated Hours,') !== false) {
                $include_hours = true;
            }
        } ?>
        <div class="row item_div_block" data-item-type="<?= $item_from ?>">
            <div class="row double-gap-top"><div class="col-sm-12 default-color"><h4><?= $item_from ?></h4></div></div><?php
            $headings = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `heading_name`, `mandatory_quantity`, IF(`heading_sortorder` = 0, NULL, `heading_sortorder`) `heading_sortorder` FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid' AND `item_type` = '$item_from' AND `contact_category` = '".(!$no_cat ? $contact_category : '**no_cat**')."' GROUP BY `heading_name` ORDER BY `heading_sortorder` IS NOT NULL, `heading_sortorder` ASC"),MYSQLI_ASSOC);

            foreach ($headings as $heading) {
                $heading_name = $heading['heading_name'];
                $mandatory_quantity = $heading['mandatory_quantity'];
                $query  = "SELECT *, IF(`sortorder` = 0, NULL, `sortorder`) `sortorder` FROM `sales_order_product_temp` WHERE `parentsotid`='$sotid' AND `item_type`='$item_from' AND `contact_category` = '".(!$no_cat ? $contact_category: '**no_cat**')."' AND `heading_name` = '$heading_name' ORDER BY `sortorder` IS NOT NULL, `sortorder` ASC";
                $result = mysqli_query($dbc, $query);

                if ( $result->num_rows > 0 ) { ?>
                    <div class="sortable_heading block-group">
                        <img src="<?= WEBSITE_URL ?>/img/icons/drag_handle.png" class="inline-img heading_handle pull-right no-toggle" title="Drag">
                        <div class="heading_row inline">
                            <div class="row heading_row_text"><div class="col-sm-12 default-color"><b><?= $heading_name.($mandatory_quantity > 0 ? ' (Mandatory Quantity: '.$mandatory_quantity.')' : '') ?></b> <a href="" onclick="editHeading(this); return false;"><span style="font-size: x-small; color: #888;">EDIT HEADING</span></a></div></div>
                            <div class="heading_row_edit" style="display: none;">
                                <div class="row">
                                    <div class="col-sm-3"><b>Heading Name</b></div>
                                    <div class="col-sm-2" style="text-align: center;"><b>Mandatory?</b></div>
                                    <div class="col-sm-3 mandatory_quantity" <?= ($mandatory_quantity == 0 ? 'style="display: none;"' : '') ?>><b>Mandatory Quantity</b></div>
                                    <div class="col-sm-2"></div>
                                </div>
                                <div class="row heading_details">
                                    <div class="col-sm-3"><input type="text" name="heading_name" value="<?= $heading_name ?>" class="form-control"></div>
                                    <div class="col-sm-2" style="text-align: center;"><input type="checkbox" name="mandatory_checkbox" value="1" <?= ($mandatory_quantity > 0 ? 'checked="checked"' : '') ?> style="transform: scale(1.5);" onclick="displayMandatoryQuantity(this);"></div>
                                    <div class="col-sm-3 mandatory_quantity" <?= ($mandatory_quantity == 0 ? 'style="display: none;"' : '') ?>><input type="number" name="mandatory_quantity" value="<?= $mandatory_quantity ?>" class="form-control"></div>
                                    <div class="col-sm-2"><button onclick="saveHeading(this); return false;" class="btn brand-btn" data-category="<?= $item_from ?>" data-contact-category="<?= !$no_cat ? $contact_category : '**no_cat**' ?>" data-heading-name="<?= $heading_name ?>">Save</button></div>
                                </div>
                            </div>
                        </div>
                        <div class="row hidden-xs">
                            <div class="col-sm-<?= $include_hours ? '3' : '4' ?>"><b><?= $item_from == 'Labour' ? 'Labour Type' : 'Category' ?></b></div>
                            <div class="col-sm-<?= $include_hours ? '4' : '5' ?>"><b><?= $item_from == 'Services' ? 'Service' : ($item_from == 'Unit #' ? 'Equipment' : 'Product') ?></b></div>
                            <?php if($include_hours) { ?>
                                <div class="col-sm-2"><b>Time Estimate</b></div>
                            <?php } ?>
                            <div class="col-sm-2"><b>Price</b></div>
                            <!-- <div class="col-sm-2"><b>Quantity</b></div> -->
                            <div class="col-sm-1"></div>
                        </div><?php

                        $odd_even = 0;
                        while ( $row=mysqli_fetch_assoc($result) ) {
                            $bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
                            <div class="row pad-top-5 sortable_row <?= $bg_class ?>" data-id="<?= $row['sotid'] ?>" id="row_<?= $row["sotid"]; ?>">
                                <div class="visible-xs-block col-xs-4"><b><?= $item_from == 'Labour' ? 'Labour Type' : 'Category' ?>: </b></div><div class="col-xs-9 col-sm-<?= $include_hours ? '3' : '4' ?>"><?= $row['item_category']; ?></div>
                                <div class="visible-xs-block col-xs-4"><b>Product: </b></div><div class="col-sm-<?= $include_hours ? '4' : '5' ?>"><?= $row['item_name']; ?></div>
                                <?php if($include_hours) { ?>
                                    <div class="visible-xs-block col-xs-4"><b>Time Estimate:</b></div><div class="col-sm-2"><input type="text" name="time_estimate" value="<?= $row['time_estimate'] ?>" data-initial="<?= $row['time_estimate'] ?>" class="form-control timepicker-5" onchange="updateTime(this);"></div>
                                <?php } ?>
                                <div class="visible-xs-block col-xs-4"><b>Price: </b></div><div class="col-sm-2"><div class="price_text"><span class="price_text_number"><?= $row['item_price'] ?></span> <a href="" onclick="editPrice(this); return false;"><span style="font-size: x-small; color: #888;">EDIT</span></a></div><div class="price_input" style="display:none;"><input type="number" name="item_price_input" value="<?= $row['item_price'] ?>" class="form-control" onfocusout="updatePrice(this);" step="0.01"></div></div>
                                <!-- <div class="visible-xs-block col-xs-3"><b>Quantity: </b></div><div class="col-sm-2"><?= $row['quantity']; ?></div> -->
                                <div class="pull-right col-sm-1"><a href="javascript:void(0);" onclick="deleteRow(this, 'row_'); return false;" id="delete_<?= $row['sotid']; ?>"><img src="<?= WEBSITE_URL; ?>/img/remove.png" height="20" /></a><img src="<?= WEBSITE_URL ?>/img/icons/drag_handle.png" class="inline-img sortable_handle gap-left no-toggle" title="Drag"></div>
                            </div><?php
                            $odd_even++;
                        } ?>
                    </div>
                <?php }
            } ?>
        </div>
        <div class="row set-row-height">
            <div class="col-sm-12"><a href="javascript:void(0);" class="iframe_open" data-category="<?= $item_from ?>" data-title="Select items from <?= $item_from ?>" data-contact-category="<?= !$no_cat ? $contact_category : '**no_cat**' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" height="20" class="pull-right gap-right"></a></div>
        </div>
        <hr>
    <?php } ?>
</div>

<?php if($_GET['from_type'] == 'iframe') { ?>
    <div class="form-group">
        <a href="" onclick="window.parent.$('#save_order').click();" class="btn brand-btn pull-right">Finish</a>
    </div>
<?php } ?>