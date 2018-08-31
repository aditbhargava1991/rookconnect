<?php foreach ($contact_categories as $contact_category) {
    $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `businessid` = '$customerid' AND `category` = '$contact_category' AND `deleted` = 0".$classification_query),MYSQLI_ASSOC));
    foreach ($contact_list as $id) { ?>
        <div class="order_detail_contact padded" data-contactid="<?= $id ?>" data-contactname="<?= get_contact($dbc, $id) ?>" <?= $active_div == $id ? '' : 'style="display: none;"' ?>><?php 
            $heading_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `heading_name`, `mandatory_quantity`, IF(`heading_sortorder` = 0, NULL, `heading_sortorder`) `heading_sortorder` FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid' AND `contact_category` = '$contact_category' GROUP BY `heading_name` ORDER BY `item_type` <> 'inventory', `item_type` <> 'vendor', `item_type` <> 'services', `heading_sortorder` IS NOT NULL, `heading_sortorder` ASC"),MYSQLI_ASSOC);
            foreach ($heading_list as $heading) {
                $heading_name = $heading['heading_name'];
                $mandatory_quantity = $heading['mandatory_quantity']; ?>
                <div class="accordion-block-details-heading"><h4><?= $heading_name.($mandatory_quantity > 0 ? ' (Choose '.$mandatory_quantity.')' : '') ?></h4></div>
                <table class="table table-bordered table-striped" data-quantity="<?= $mandatory_quantity ?>" data-heading="<?= $heading_name ?>">
                    <thead>
                        <tr class="hidden-xs">
                            <th>Category</th>
                            <th>Product</th>
                            <th width="10%">Price</th>
                            <th width="10%">Quantity</th>
                        </tr>
                    </thead>
                    <?php $item_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT *, IF(`sortorder` = 0, NULL, `sortorder`) `sortorder` FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid' AND `contact_category` = '$contact_category' AND `heading_name` = '$heading_name' ORDER BY `sortorder` IS NOT NULL, `sortorder` ASC"),MYSQLI_ASSOC);
                    foreach ($item_list as $item) {
                        $item_details = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_product_details_temp` WHERE `parentsotid` = '".$item['sotid']."' AND `contactid` = '".$id."'")); ?>
                        <tr>
                            <input type="hidden" name="item_contactid[]" value="<?= $id ?>">
                            <input type="hidden" name="item_soptid[]" value="<?= $item['sotid'] ?>">
                            <td data-title="Category"><?= $item['item_category'] ?></td>
                            <td data-title="Product"><?= $item['item_name'].(!empty($item['time_estimate']) ? ' (Time Estimate: '.$item['time_estimate'].')' : '') ?></td>
                            <td data-title="Price"><?= $item['item_price'] ?></td>
                            <td data-title="Quantity"><input type="number" name="item_quantity[]" class="form-control" value="<?= !empty($item_details['quantity']) ? $item_details['quantity'] : '0' ?>"></td>
                        </tr>
                    <?php } ?>
                </table><hr><?php
            } ?>
        </div><?php
    }
} ?>
<?php if(empty($contact_categories)) { ?>
    <div class="order_detail_contact padded" data-contactid="<?= $customerid ?>" data-contactname="<?= !empty(get_client($dbc, $customerid)) ? get_client($dbc, $customerid) : get_contact($dbc, $customerid) ?>"><?php 
        $heading_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `heading_name`, `mandatory_quantity`, IF(`heading_sortorder` = 0, NULL, `heading_sortorder`) `heading_sortorder` FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid' AND `contact_category` = '**no_cat**' GROUP BY `heading_name` ORDER BY `item_type` <> 'inventory', `item_type` <> 'vendor', `item_type` <> 'services', `heading_sortorder` IS NOT NULL, `heading_sortorder` ASC"),MYSQLI_ASSOC);
        foreach ($heading_list as $heading) {
            $heading_name = $heading['heading_name'];
            $mandatory_quantity = $heading['mandatory_quantity']; ?>
            <div id="nocat_order_<?= config_safe_str($heading['heading_name']) ?>" class="accordion-block-details-sub">
                <div class="accordion-block-details-heading"><h4><?= $heading_name.($mandatory_quantity > 0 ? ' (Choose '.$mandatory_quantity.')' : '') ?></h4></div>
                <table class="table table-bordered table-striped" data-quantity="<?= $mandatory_quantity ?>" data-heading="<?= $heading_name ?>">
                    <thead>
                        <tr class="hidden-xs">
                            <th>Category</th>
                            <th>Product</th>
                            <th width="10%">Price</th>
                            <th width="10%">Quantity</th>
                        </tr>
                    </thead>
                    <?php $item_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT *, IF(`sortorder` = 0, NULL, `sortorder`) `sortorder` FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid' AND `contact_category` = '**no_cat**' AND `heading_name` = '$heading_name' ORDER BY `sortorder` IS NOT NULL, `sortorder` ASC"),MYSQLI_ASSOC);
                    $odd_even = 0;
                    foreach ($item_list as $item) {
                        $bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg';
                        $item_details = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_product_details_temp` WHERE `parentsotid` = '".$item['sotid']."' AND `contactid` = '".$customerid."'")); ?>
                        <tr class="<?= $bg_class ?>">
                            <input type="hidden" name="item_contactid[]" value="<?= $customerid ?>">
                            <input type="hidden" name="item_soptid[]" value="<?= $item['sotid'] ?>">
                            <td data-title="Category"><?= $item['item_category'] ?></td>
                            <td data-title="Product"><?= $item['item_name'].(!empty($item['time_estimate']) ? ' (Time Estimate: '.$item['time_estimate'].')' : '') ?></td>
                            <td data-title="Price"><?= $item['item_price'] ?></td>
                            <td data-title="Quantity"><input type="number" name="item_quantity[]" class="form-control" value="<?= !empty($item_details['quantity']) ? $item_details['quantity'] : '0' ?>"></td>
                        </tr>
                        <?php $odd_even++; ?>
                    <?php } ?>
                </table>
            </div><?php
        } ?>
    </div><?php
} ?>