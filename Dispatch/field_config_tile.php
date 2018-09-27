<?php
/*
Dispatch Field Config - Tile Settings
*/
include ('../include.php');
checkAuthorised('dispatch'); ?>

<script type="text/javascript" src="../Dispatch/field_config.js"></script>

<div class="form-group">
    <label class="col-sm-4 control-label">Equipment Category:</label>
    <div class="col-sm-8">
        <?php 
        $equip_categories = get_config($dbc, 'equipment_tabs');
        $equip_categories = explode(',', $equip_categories);
        asort($equip_categories);
        $dispatch_tile_equipment_category = explode(',',get_config($dbc, 'dispatch_tile_equipment_category'));
        foreach($dispatch_tile_equipment_category as $equipment_category) { ?>
            <div class="equip_div">
                <div class="col-sm-10" style="padding: 0;">
                    <select name="dispatch_tile_equipment_category[]" class="chosen-select-deselect">
                        <option></option>
                        <?php foreach($equip_categories as $equip_category) {
                            echo '<option value="'.$equip_category.'"'.($equipment_category == $equip_category ? ' selected' : '').'>'.$equip_category.'</option>';
                        } ?>
                    </select>
                </div>
                <div class="col-sm-2" style="padding: 0;">
                    <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_equipment_category(this);">
                    <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_equipment_category();">
                </div>
            </div>
        <?php } ?>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label">Combine Warehouse Stops:</label>
    <div class="col-sm-8">
        <?php $dispatch_tile_combine_warehouse = get_config($dbc, 'dispatch_tile_combine_warehouse'); ?>
        <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_combine_warehouse" <?= $dispatch_tile_combine_warehouse == 1 ? 'checked' : '' ?> value="1"></label>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label">Combine Pick Up Stops:</label>
    <div class="col-sm-8">
        <?php $dispatch_tile_combine_pickup = get_config($dbc, 'dispatch_tile_combine_pickup'); ?>
        <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_combine_pickup" <?= $dispatch_tile_combine_pickup == 1 ? 'checked' : '' ?> value="1"></label>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label"><span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='This will display the Calendar to the selected Security Levels with a limited view to only things related to them.'><img src='<?= WEBSITE_URL ?>/img/info.png' width='20'></a></span> Customer View Security Levels:</label>
    <div class="col-sm-8">
        <?php $on_security = get_security_levels($dbc);
        $dispatch_tile_customer_roles = explode(',',get_config($dbc, 'dispatch_tile_customer_roles'));
        foreach($dispatch_tile_customer_roles as $customer_role) { ?>
            <div class="role_div">
                <div class="col-sm-10" style="padding: 0;">
                    <select name="dispatch_tile_customer_roles[]" class="chosen-select-deselect">
                        <option></option>
                        <?php foreach($on_security as $security_label => $security_value) {
                            echo '<option value="'.$security_value.'" '.($security_value == $customer_role ? 'selected' : '').'>'.$security_label.'</option>';
                        } ?>
                    </select>
                </div>
                <div class="col-sm-2" style="padding: 0;">
                    <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_customer_role(this);">
                    <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_customer_role();">
                </div>
            </div>
        <?php } ?>
    </div>
</div>