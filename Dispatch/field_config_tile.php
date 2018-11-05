<?php
/*
Dispatch Field Config - Tile Settings
*/
include ('../include.php');
checkAuthorised('dispatch'); ?>

<script type="text/javascript" src="../Dispatch/field_config.js"></script>

<div class="form-group">
    <label class="col-sm-4 control-label">Enable Summary Tab:</label>
    <div class="col-sm-8">
        <?php $dispatch_tile_summary_tab = get_config($dbc, 'dispatch_tile_summary_tab'); ?>
        <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_summary_tab" <?= $dispatch_tile_summary_tab == 1 ? 'checked' : '' ?> value="1"></label>
    </div>
</div>
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
    <label class="col-sm-4 control-label">Group Equipment by Region:</label>
    <div class="col-sm-8">
        <?php $dispatch_tile_group_regions = get_config($dbc, 'dispatch_tile_group_regions'); ?>
        <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_group_regions" <?= $dispatch_tile_group_regions == 1 ? 'checked' : '' ?> value="1"></label>
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
    <label class="col-sm-4 control-label">Don't Count Warehouse As Delivery Stop:</label>
    <div class="col-sm-8">
        <?php $dispatch_tile_dont_count_warehouse = get_config($dbc, 'dispatch_tile_dont_count_warehouse'); ?>
        <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_dont_count_warehouse" <?= $dispatch_tile_dont_count_warehouse == 1 ? 'checked' : '' ?> value="1"></label>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label">Toggle All Equipment With <?= TICKET_TILE ?> When Changing Dates:</label>
    <div class="col-sm-8">
        <?php $dispatch_tile_reset_active = get_config($dbc, 'dispatch_tile_reset_active'); ?>
        <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_reset_active" <?= $dispatch_tile_reset_active == 1 ? 'checked' : '' ?> value="1"></label>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label">Hide Equipment With No <?= TICKET_TILE ?>:</label>
    <div class="col-sm-8">
        <?php $dispatch_tile_hide_empty = get_config($dbc, 'dispatch_tile_hide_empty'); ?>
        <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_hide_empty" <?= $dispatch_tile_hide_empty == 1 ? 'checked' : '' ?> value="1"></label>
    </div>
</div>
<div class="form-group">
    <label class="col-sm-4 control-label">Auto Refresh Data After Inactivity Time:</label>
    <div class="col-sm-8"><?php
        $dispatch_tile_auto_refresh = get_config($dbc, 'dispatch_tile_auto_refresh'); ?>
        <input type="text" name="dispatch_tile_auto_refresh" class="timepicker form-control" value="<?= $dispatch_tile_auto_refresh ?>">
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
<div class="form-group">
    <label class="col-sm-4 control-label">Search Fields:</label>
    <div class="col-sm-8">
        <?php $ticket_label_fields = [
            'region'=>'Region',
            'location'=>'Location',
            'classification'=>'Classification',
            'business'=>BUSINESS_CAT
        ];
        $dispatch_tile_search_fields = explode(',',get_config($dbc, 'dispatch_tile_search_fields'));
        foreach($ticket_label_fields as $label_key => $label_field) { ?>
            <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_search_fields[]" value="<?= $label_key ?>" <?= in_array($label_key, $dispatch_tile_search_fields) ? 'checked' : '' ?>> <?= $label_field ?></label>
        <?php } ?>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-4 control-label">Table View Search Fields:</label>
    <div class="col-sm-8">
        <?php $table_label_fields = [
            'region'=>'Region',
            'location'=>'Location',
            'classification'=>'Classification',
            'business'=>BUSINESS_CAT,
            'equipment'=>'Equipment',
            'status'=>'Status',
        ];
        $dispatch_tile_table_view_search_fields = explode(',',get_config($dbc, 'dispatch_tile_table_view_search_fields'));
        foreach($table_label_fields as $table_key => $table_field) { ?>
            <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_table_view_search_fields[]" value="<?= $table_key ?>" <?= in_array($table_key, $dispatch_tile_table_view_search_fields) ? 'checked' : '' ?>> <?= $table_field ?></label>
        <?php } ?>
    </div>
</div>

<div class="form-group">
    <label class="col-sm-4 control-label">Equipment Fields:</label>
    <div class="col-sm-8">
        <?php $ticket_label_fields = [
            'view_map'=>'View Map',
            'next_stop'=>'Next Stop'
        ];
        $dispatch_tile_equipment_fields = explode(',',get_config($dbc, 'dispatch_tile_equipment_fields'));
        foreach($ticket_label_fields as $label_key => $label_field) { ?>
            <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_equipment_fields[]" value="<?= $label_key ?>" <?= in_array($label_key, $dispatch_tile_equipment_fields) ? 'checked' : '' ?>> <?= $label_field ?></label>
        <?php } ?>
    </div>
</div>