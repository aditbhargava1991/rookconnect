<?php
/*
Dispatch Field Config - Tile Settings
*/
include ('../include.php');
checkAuthorised('dispatch'); ?>

<script type="text/javascript" src="../Dispatch/field_config.js"></script>

<div class="form-group">
    <label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Fields:</label>
    <div class="col-sm-8">
        <?php $ticket_label_fields = [
            'project'=>PROJECT_NOUN,
            'customer'=>'Customer',
            'client'=>'Client',
            'site_address'=>'Site Address',
            'service_template'=>'Service Template',
            'assigned'=>'Assigned Staff',
            'preferred'=>'Preferred Staff',
            'address'=>'Address',
            'start_date'=>'Date',
            'ticket_notes'=>TICKET_NOUN.' Notes',
            'delivery_notes'=>'Delivery Notes',
            'status'=>'Status'
        ];
        $dispatch_tile_ticket_card_fields = explode(',',get_config($dbc, 'dispatch_tile_ticket_card_fields'));
        foreach($ticket_label_fields as $label_key => $label_field) { ?>
            <label class="form-checkbox"><input type="checkbox" name="dispatch_tile_ticket_card_fields[]" value="<?= $label_key ?>" <?= in_array($label_key, $dispatch_tile_ticket_card_fields) ? 'checked' : '' ?>> <?= $label_field ?></label>
        <?php } ?>
    </div>
</div>