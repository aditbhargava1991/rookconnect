<div id="inv_inventory">
    <h4 class="col-sm-12"><?= INVENTORY_TILE ?>
    <span class="popover-examples list-inline">
        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Select any products from inventory here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
    </span><img src="../img/icons/ROOK-add-icon.png" class="no-toggle cursor-hand adjust_block" title="Add <?= INVENTORY_TILE ?>" width="21" onclick="add_product_row();" /></h4>
    <div class="form-group product_option" <?= (in_array('inventory',$field_config) ? '' : 'style="display:none;"') ?>>
        <label for="additional_note" class="col-sm-2 control-label">
        <?php echo (in_array('injury', $field_config) ? '<br>MVA Claim Price:' : '');
        if(!empty($_GET['invoiceid'])) {
            echo $mva_claim_price;
        } else {
            echo '<span class="mva_claim_price"></span>';
        }

        //Calculate Column Widths
        $col1 = 2;
        $col2 = 1;
        $col3 = 1;
        $col4 = 1;
        $col5 = 2;
        $col6 = 1;
        $col7 = 2;
        if(in_array('inventory_cat',$field_config) && in_array('inventory_part',$field_config) && in_array('inventory_type',$field_config) && in_array('inventory_price',$field_config)) {
            $col1 = $col2 = $col3 = $col4 = 2;
        } else if(in_array('inventory_cat',$field_config) && in_array('inventory_part',$field_config) && in_array('inventory_type',$field_config)) {
            $col1 = $col2 = $col3 = 2;
            $col4 = 1;
            $col5 = 0;
        } else if(in_array('inventory_cat',$field_config) && in_array('inventory_part',$field_config) && in_array('inventory_price',$field_config)) {
            //$col2 = $col5 = 1;
            $col1 = $col3 = 2;
            $col4 = 0;
        } else if(in_array('inventory_cat',$field_config) && in_array('inventory_type',$field_config) && in_array('inventory_price',$field_config)) {
            $col1 = $col3 = $col4 = 2;
            //$col5 = 1;
            $col2 = 0;
        } else if(in_array('inventory_part',$field_config) && in_array('inventory_type',$field_config) && in_array('inventory_price',$field_config)) {
            $col2 = $col3 = $col4 = 2;
            //$col5 = 1;
            $col1 = 0;
        } else if(in_array('inventory_cat',$field_config) && in_array('inventory_part',$field_config)) {
            $col1 = $col3 = 3;
            $col2 = 1;
            $col4 = $col5 = 0;
        } else if(in_array('inventory_cat',$field_config) && in_array('inventory_type',$field_config)) {
            $col1 = $col3 = 3;
            $col4 = 1;
            $col2 = $col5 = 0;
        } else if(in_array('inventory_cat',$field_config) && in_array('inventory_price',$field_config)) {
            //$col1 = $col3 = 3;
            $col3 = 3;
            //$col5 = 1;
            $col2 = $col4 = 0;
        } else if(in_array('inventory_part',$field_config) && in_array('inventory_type',$field_config)) {
            $col2 = $col3 = 3;
            $col4 = 1;
            $col1 = $col5 = 0;
        } else if(in_array('inventory_part',$field_config) && in_array('inventory_price',$field_config)) {
            $col2 = $col3 = 3;
            //$col5 = 1;
            $col1 = $col4 = 0;
        } else if(in_array('inventory_type',$field_config) && in_array('inventory_price',$field_config)) {
            $col3 = $col4 = 3;
            //$col5 = 1;
            $col1 = $col2 = 0;
        } else if(in_array('inventory_cat',$field_config)) {
            $col1 = 4;
            $col3 = 3;
            $col2 = $col4 = $col5 = 0;
        } else if(in_array('inventory_part',$field_config)) {
            $col2 = 3;
            $col3 = 4;
            $col1 = $col4 = $col5 = 0;
        } else if(in_array('inventory_type',$field_config)) {
            $col3 = 4;
            $col4 = 3;
            $col1 = $col2 = $col5 = 0;
        } else if(in_array('inventory_price',$field_config)) {
            $col3 = 5;
            //$col5 = 2;
            $col1 = $col2 = $col4 = 0;
        }
        mysqli_set_charset($dbc, 'utf8');
        $inventory_list = mysqli_fetch_all(mysqli_query($dbc,"SELECT `inventoryid`, `category`, `part_no`, `name` FROM inventory WHERE deleted=0 ORDER BY name"),MYSQLI_ASSOC);
        ?></label>
        <script>
        var inv_list = <?= json_encode($inventory_list) ?>;
        </script>
        <div class="col-sm-12">
            <div class="form-group clearfix hide-titles-mob product_labels" style="<?= ( empty(rtrim($inventoryid, ',')) && $_GET['inv_mode'] == 'adjust' ) ? 'display:none;' : '' ?>">
                <?php if(in_array('inventory_cat',$field_config)) { ?><label class="col-sm-<?= $col1 ?> text-center">Category</label><?php } ?>
                <?php if(in_array('inventory_part',$field_config)) { ?><label class="col-sm-<?= $col2 ?> text-center">Part #</label><?php } ?>
                <label class="col-sm-<?= $col3 ?> text-center">Name</label>
                <?php if(in_array('inventory_type',$field_config)) { ?><label class="col-sm-<?= $col4 ?> text-center">Type</label><?php } ?>
                <label class="col-sm-<?= $col6 ?> text-center">Qty</label>
                <?php if(in_array('inventory_price',$field_config)) { ?><label class="col-sm-<?= $col5 ?> text-center">Price</label><?php } ?>
                <label class="col-sm-<?= $col7 ?> text-center">Total</label>
                <?php if($_GET['inv_mode'] == 'adjust') { ?>
                    <label class="col-sm-1 text-center return_block">Return</label>
                <?php } ?>
            </div>

            <?php
            if(!empty($_GET['invoiceid'])) {
                if($inventoryid != '') {
                    $each_inventoryid = explode(',',$inventoryid);
                    $each_sell_price = explode(',',$sell_price);
                    $each_invtype = explode(',',$invtype);
                    $each_quantity = explode(',',$quantity);

                    $total_count = mb_substr_count($inventoryid,',');
                    $id_loop = 500;

                    for($client_loop=0; $client_loop<=$total_count; $client_loop++) {
                        if($each_inventoryid[$client_loop] != '') {
                            $inventoryid = $each_inventoryid[$client_loop];
                            $sell_price = $each_sell_price[$client_loop] * $discount_percent;
                            $invtype = $each_invtype[$client_loop];
                            $quantity = $each_quantity[$client_loop];
                            $inv_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `part_no`, `name`, `final_retail_price`, `wcb_price`, `client_price`, `web_price`, `purchase_order_price`, `sales_order_price`, `admin_price`, `wholesale_price`, `commercial_price`, `preferred_price`, `gst_exempt` FROM `inventory` WHERE `inventoryid`='$inventoryid'"));
                            $gst_exempt = $inv_info['gst_exempt'];
                            ?>

                            <div class="additional_product form-group clearfix <?= $_GET['inv_mode'] == 'adjust' ? 'refundable' : '' ?>">
                                <div class="col-sm-<?= $col1 ?>" <?= (in_array('inventory_cat',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Inventory Category:</label>
                                    <?php if($_GET['inv_mode'] == 'adjust') { ?>
                                        <?= $inv_info['category'] ?>
                                    <?php } else { ?>
                                        <select data-placeholder="Select Category..." id="<?php echo 'inventorycat_'.$id_loop; ?>" name="inventorycat[]" class="chosen-select-deselect form-control inventorycat" width="380">
                                            <option></option>
                                            <?php $query = mysqli_query($dbc,"SELECT `category` FROM inventory WHERE deleted=0 GROUP BY `category` ORDER BY `category`");
                                            while($row = mysqli_fetch_array($query)) {
                                                echo "<option ".($row['category'] == $inv_info['category'] ? 'selected' : '')." value='". $row['category']."'>".$row['category'].'</option>';
                                            } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-<?= $col2 ?>" <?= (in_array('inventory_part',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Inventory Part #:</label>
                                    <?php if($_GET['inv_mode'] == 'adjust') {
                                        echo $inv_info['part_no'];
                                    } else { ?>
                                        <select data-placeholder="Select Part #..." id="<?php echo 'inventorypart_'.$id_loop; ?>" name="inventorypart[]" class="chosen-select-deselect form-control inventorypart" width="380">
                                            <option value=""></option>
                                            <?php foreach($inventory_list as $row) {
                                                if(in_array($inv_info['category'],['',$row['category']])) {
                                                    echo "<option data-category='".$row['category']."' ".($row['part_no'] == $inv_info['part_no'] ? 'selected' : '')." value='". $row['part_no']."'>".$row['part_no'].'</option>';
                                                }
                                            } ?>
                                        </select>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-<?= $col3 ?>"><label class="show-on-mob">Inventory Name:</label>
                                    <?php if($_GET['inv_mode'] == 'adjust') { ?>
                                        <?= $inv_info['name'] ?><input type="hidden" name="inventoryid[]" value="<?= $inventoryid ?>"><input type="hidden" name="inventorylabel" value="<?= $inv_info['name'].(in_array('inventory_type',$field_config) ? ': '.$invtype : '') ?>">
                                    <?php } else { ?>
                                        <select data-placeholder="Select Inventory..." id="<?php echo 'inventoryid_'.$id_loop; ?>" name="inventoryid[]" class="chosen-select-deselect form-control inventoryid" width="380">
                                            <option value=""></option>
                                            <?php foreach($inventory_list as $row) {
                                                if(in_array($inv_info['category'],['',$row['category']])) {
                                                    echo "<option data-category='".$row['category']."' data-part='".$row['part_no']."' ".($row['inventoryid'] == $inventoryid ? 'selected' : '')." value='". $row['inventoryid']."'>".$row['name'].'</option>';
                                                }
                                            } ?>
                                        </select>
                                    <?php } ?>
                                </div> <!-- Quantity -->
                                <div class="col-sm-<?= $col4 ?>" <?= (in_array('inventory_type',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Type:</label>
                                    <?php if($_GET['inv_mode'] == 'adjust') { ?>
                                        <?= $invtype ?>
                                    <?php } else { ?>
                                        <select data-placeholder="Select a Type..." id="<?php echo 'invtype_'.$id_loop; ?>" name="invtype[]" class="chosen-select-deselect form-control invtype" width="480">
                                        <option <?= ($invtype == 'General' ? "selected" : '') ?> value="General">General</option>
                                        <option <?= ($invtype == 'WCB' ? "selected" : (strpos($injury_type,'WCB') === false && $injury_type != '' ? "disabled" : '')) ?> value="WCB">WCB</option>
                                        <option <?= ($invtype == 'MVA' ? "selected" : (strpos($injury_type,'MVA') === false && $injury_type != '' ? "disabled" : '')) ?> value="MVA">MVA</option>
                                        </select>
                                    <?php } ?>
                                </div>
                                <div class="col-sm-<?= $col6 ?>"><label class="show-on-mob">Quantity:</label>
                                    <?php if($_GET['inv_mode'] == 'adjust') { ?>
                                        <?= $quantity ?>
                                        <input name="init_quantity[]" id="<?php echo 'quantity_'.$id_loop; ?>" onchange="changeProduct($('#inventoryid_'+this.id.split('_')[1]).get(0));" value="<?php echo $quantity; ?>" type="hidden" class="form-control quantity" />
                                    <?php } else { ?>
                                        <input name="quantity[]" id="<?php echo 'quantity_'.$id_loop; ?>" onchange="changeProduct($('#inventoryid_'+this.id.split('_')[1]).get(0));" value="<?php echo $quantity; ?>" type="number" min="0" step="any" class="form-control quantity" />
                                    <?php } ?>
                                </div> <!-- Quantity -->
                                <div class="col-sm-<?= $col5 ?>" <?= (in_array('inventory_price',$field_config) ? '' : 'style="display:none;"') ?>><label class="show-on-mob">Unit Price:</label>
                                    <?php if($_GET['inv_mode'] == 'adjust') { ?>
                                        <input name="unit_price[]" id="<?php echo 'unitprice_'.$id_loop; ?>" value="<?php echo $sell_price / $quantity; ?>" type="hidden" readonly class="form-control invunitprice" />
                                        <?= number_format($sell_price / $quantity,2) ?>
                                    <?php } else { ?>
                                        <input name="unit_price[]" id="<?php echo 'unitprice_'.$id_loop; ?>" value="<?php echo $sell_price / $quantity; ?>" type="number" step="any" readonly class="form-control invunitprice" />
                                    <?php } ?>
                                </div> <!-- Quantity -->
                                <div class="col-sm-<?= $col7 ?>"><label class="show-on-mob">Total:</label>
                                    <input name="inventory_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                                    <input name="inventory_gst_exempt[]" type="hidden" value="<?= $gst_exempt ?>" />
                                    <?php if($_GET['inv_mode'] == 'adjust') { ?>
                                        <input name="init_price[]" id="<?php echo 'unitprice_'.$id_loop; ?>" value="<?php echo $sell_price; ?>" type="hidden" readonly class="form-control invunitprice" />
                                        <input name="sell_price[]" id="<?php echo 'sellprice_'.$id_loop; ?>" onchange="countTotalPrice()" value="0" type="number" step="any" readonly class="form-control sellprice" />
                                    <?php } else { ?>
                                        <input name="sell_price[]" id="<?php echo 'sellprice_'.$id_loop; ?>" onchange="countTotalPrice()" value="<?php echo $sell_price; ?>" type="number" step="any" readonly class="form-control sellprice" />
                                    <?php } ?>
                                </div>
                                <?php if($_GET['inv_mode'] == 'adjust') { ?>
                                    <div class="return_block col-sm-1">
                                        <input name="quantity[]" id="<?php echo 'quantity_'.$id_loop; ?>" onchange="changeProduct(this);" value="0" max="0" min="<?php echo -$quantity; ?>" type="number" step="any" class="form-control quantity" />
                                    </div>
                                <?php } ?>
                                <!-- Hidden on Returns/Adjustment
                                <div class="col-sm-2 adjust_block">
                                    <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_product_row(this);">
                                    <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_product_row();">
                                </div>
                                -->
                                <div class="col-sm-12 pay-div"></div>
                            </div>
                        <?php
                        $id_loop++;
                        }
                    }
                }
            }
            ?>

            <div class="clearfix"></div>
            <div class="additional_product form-group clearfix" style="<?= $_GET['inv_mode'] == 'adjust' ? 'display:none;' : '' ?>">
                <div class="col-sm-<?= $col1 ?>" <?= (in_array('inventory_cat',$field_config) ? '' : 'style="display:none;"') ?>>
                    <label class="show-on-mob">Inventory Category:</label>
                    <select data-placeholder="Select Category..." id="inventorycat_0" name="inventorycat[]" class="chosen-select-deselect form-control inventorycat" width="380">
                        <option value=""></option>
                        <?php $query = mysqli_query($dbc,"SELECT `category` FROM inventory WHERE deleted=0 GROUP BY `category` ORDER BY `category`");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option value='". $row['category']."'>".$row['category'].'</option>';
                        } ?>
                    </select>
                </div>
                <div class="col-sm-<?= $col2 ?>" <?= (in_array('inventory_part',$field_config) ? '' : 'style="display:none;"') ?>>
                    <label class="show-on-mob">Inventory Part #:</label>
                    <select data-placeholder="Select Part #..." id="inventorypart_0" name="inventorypart[]" class="chosen-select-deselect form-control inventorypart" width="380">
                        <option value=""></option>
                        <?php $query = mysqli_query($dbc,"SELECT `category`, `part_no` FROM inventory WHERE deleted=0 ORDER BY `part_no`");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option data-category='".$row['category']."' value='". $row['part_no']."'>".$row['part_no'].'</option>';
                        } ?>
                    </select>
                </div>
                <div class="col-sm-<?= $col3 ?>">
                    <label class="show-on-mob">Inventory Name:</label>
                    <select data-placeholder="Select Inventory..." id="inventoryid_0" name="inventoryid[]" class="chosen-select-deselect form-control inventoryid" width="380">
                        <option value=""></option>
                        <?php $query = mysqli_query($dbc,"SELECT `inventoryid`, `category`, `part_no`, `name` FROM inventory WHERE deleted=0 ORDER BY name");
                        while($row = mysqli_fetch_array($query)) {
                            echo "<option data-category='".$row['category']."' data-part='".$row['part_no']."' value='". $row['inventoryid']."'>".$row['name'].'</option>';
                        } ?>
                    </select>
                </div> <!-- Quantity -->
                <div class="col-sm-<?= $col4 ?>" <?= (in_array('inventory_type',$field_config) ? '' : 'style="display:none;"') ?>>
                    <label class="show-on-mob">Type:</label>
                    <select data-placeholder="Select a Type..." id="invtype_0" name="invtype[]" class="chosen-select-deselect form-control invtype" width="480">
                        <option value="General">General</option>
                        <option <?= (strpos($injury_type,'WCB') === false && $injury_type != '' ? "disabled" : '') ?> value="WCB">WCB</option>
                        <option <?= (strpos($injury_type,'MVA') === false && $injury_type != '' ? "disabled" : '') ?> value="MVA">MVA</option>
                    </select>
                </div>
                <div class="col-sm-<?= $col6 ?>">
                    <label class="show-on-mob">Quantity:</label>
                    <input name="quantity[]" id="quantity_0" onchange="changeProduct($('#inventoryid_'+this.id.split('_')[1]).get(0));" value=1 type="number" min="0" step="any" class="form-control quantity" />
                </div>
                <div class="col-sm-<?= $col5 ?>" <?= (in_array('inventory_price',$field_config) ? '' : 'style="display:none;"') ?>>
                    <label class="show-on-mob">Unit Price:</label>
                    <input name="unit_price[]" id="unitprice_0" value="0" type="text" step="any" readonly class="form-control invunitprice" />
                </div>
                <div class="col-sm-<?= $col7 ?>"><label class="show-on-mob">Total:</label>
                    <input name="sell_price[]" id="sellprice_0" onchange="countTotalPrice()" value="0" type="text" step="any" readonly class="form-control sellprice" />
                    <input name="inventory_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                    <input name="inventory_gst_exempt[]" type="hidden" value="0" />
                </div>
                <?php if($_GET['inv_mode'] == 'adjust') { ?>
                    <div class="return_block col-sm-2">
                    </div>
                <?php } ?>
                <div class="col-sm-2 col-pricing" <?= (in_array('pricing',$field_config) ? '' : 'style="display:none;"') ?>>
                    <div class="pricing-div" style="display:none;">
                        <select data-placeholder="Select Pricing" id="linepricing_0" name="linepricing[]" class="chosen-select-deselect form-control linepricing" onchange="changeProduct($('#inventoryid_'+this.id.split('_')[1]).get(0));">
                            <option></option>
                            <?php if(in_array('price_admin', $field_config)) { ?><option value="admin_price">Admin Price</option><?php } ?>
                            <?php if(in_array('price_client', $field_config)) { ?><option value="client_price">Client Price</option><?php } ?>
                            <?php if(in_array('price_commercial', $field_config)) { ?><option value="commercial_price">Commercial Price</option><?php } ?>
                            <?php if(in_array('price_distributor', $field_config)) { ?><option value="distributor_price">Distributor Price</option><?php } ?>
                            <?php if(in_array('price_retail', $field_config)) { ?><option value="final_retail_price">Final Retail Price</option><?php } ?>
                            <?php if(in_array('price_preferred', $field_config)) { ?><option value="preferred_price">Preferred Price</option><?php } ?>
                            <?php if(in_array('price_po', $field_config)) { ?><option value="purchase_order_price">Purchase Order Price</option><?php } ?>
                            <?php if(in_array('price_sales', $field_config)) { ?><option value="sales_order_price"><?= SALES_ORDER_NOUN ?> Price</option><?php } ?>
                            <?php if(in_array('price_web', $field_config)) { ?><option value="web_price">Web Price</option><?php } ?>
                            <?php if(in_array('price_wholesale', $field_config)) { ?><option value="wholesale_price">Wholesale Price</option><?php } ?>
                        </select>
                    </div>
                    <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_product_row(this);">
                    <img src="../img/icons/ROOK-edit-icon.png" alt="Edit Pricing" title="Edit Pricing" width="21" class="pull-right cursor-hand no-toggle price_edit" style="display:none; margin:3px 2px 0 2px;" onclick="$(this).hide(); $(this).closest('.col-pricing').find('.pricing-div').show();" />
                    <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_product_row();">
                </div>
                <div class="col-sm-12 pay-div"></div>
            </div>

            <div id="add_here_new_product"></div>
        </div>
    </div>
</div>