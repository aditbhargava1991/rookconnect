<?php
/*
Dispatch Field Config - Tile Settings
*/
include ('../include.php');
checkAuthorised('dispatch'); ?>

<script type="text/javascript" src="../Dispatch/field_config.js"></script>

<div class="form-group">
    <label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Truck View Fields:</label>
    <div class="col-sm-8">
        <?php $ticket_label_fields = [
            'project'=>PROJECT_NOUN,
            'customer'=>'Customer',
            'client'=>'Client',
            'site_address'=>'Site Address',
            'service_template'=>'Service Template',
            'assigned'=>'Assigned Staff',
            'preferred'=>'Preferred Staff',
            'time'=>'Time',
            'eta'=>'ETA',
            'available'=>'Time Frame',
            'address'=>'Address',
            'start_date'=>'Date',
            'ticket_notes'=>TICKET_NOUN.' Notes',
            'delivery_notes'=>'Delivery Notes',
            'status'=>'Status',
            'camera'=>'Camera Upload Hover',
            'signature'=>'Signature Hover',
            'star_rating'=>'Star Rating Hover',
            'customer_notes_hover'=>'Customer Notes Hover'
        ];
        $dispatch_tile_ticket_card_fields = explode(',',get_config($dbc, 'dispatch_tile_ticket_card_fields'));
        foreach($ticket_label_fields as $label_key => $label_field) { ?>
            <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_ticket_card_fields[]" value="<?= $label_key ?>" <?= in_array($label_key, $dispatch_tile_ticket_card_fields) ? 'checked' : '' ?>> <?= $label_field ?></label>
        <?php } ?>
    </div>
</div>


<div class="form-group">
    <label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Table View Fields:</label>
    <div class="col-sm-8">
        <?php $table_label_fields = [
            'project'=>PROJECT_NOUN,
            'customer'=>'Customer',
            'client'=>'Client',
            'site_address'=>'Site Address',
            'service_template'=>'Service Template',
            'assigned'=>'Assigned Staff',
            'preferred'=>'Preferred Staff',
            'time'=>'Time',
            'eta'=>'ETA',
            'available'=>'Time Frame',
            'address'=>'Address',
            'start_date'=>'Date',
            'ticket_notes'=>TICKET_NOUN.' Notes',
            'delivery_notes'=>'Delivery Notes',
            'status'=>'Status',
            'camera'=>'Camera Upload Hover',
            'signature'=>'Signature Hover',
            'star_rating'=>'Star Rating Hover',
            'customer_notes_hover'=>'Customer Notes Hover'
        ];
        $dispatch_tile_table_view_fields = explode(',',get_config($dbc, 'dispatch_tile_table_view_fields'));
        foreach($table_label_fields as $table_key => $table_field) { ?>
            <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_table_view_fields[]" value="<?= $table_key ?>" <?= in_array($table_key, $dispatch_tile_table_view_fields) ? 'checked' : '' ?>> <?= $table_field ?></label>
        <?php } ?>
    </div>
</div>