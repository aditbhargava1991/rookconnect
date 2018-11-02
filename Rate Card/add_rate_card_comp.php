<script>
function addComp(img) {
    var src = $(img).closest('.col-sm-12').find('.compensation_detail:visible').last();
    destroyInputs();
    var clone = src.clone();
    clone.find('input,select').val('');
    src.after(clone);
    initInputs();
}
function remComp(img) {
    if($(img).closest('.col-sm-12').find('.compensation_detail:visible').length <= 1) {
        addComp(img);
    }
    $(img).closest('.compensation_detail').hide().find('[name^=comp_deleted]').val(1);
}
</script>
<div class="form-group">
    <div class="col-sm-12">
        <?php if (strpos($value_config, ','."Compensation".',') !== FALSE) { ?>
            <h4>Individual Compensation</h4>
            <div class="form-group hide-titles-mob">
                <div class="pull-right" style="width:5em;">&nbsp;</div>
                <div class="scale-to-fill">
                    <label class="col-sm-6 text-center">Compensation for Item</label>
                    <label class="col-sm-3 text-center">Compensation Rate (%)</label>
                    <label class="col-sm-3 text-center">Compensation Amt ($)</label>
                </div>
                <div class="clearfix"></div>
            </div>

            <?php $comp_rates = $dbc->query("SELECT * FROM `rate_compensation` WHERE `rate_card`='".$ratecardid."' AND `item_type` NOT LIKE 'equip_%' AND `deleted`=0");
            $comp_row = $comp_rates->fetch_assoc();
            do { ?>
                <div class="form-group compensation_detail">
                    <div class="pull-right" style="width:5em;">
                        <input type="hidden" name="comp_id[]" value="<?= $comp_row['id'] ?>">
                        <input type="hidden" name="comp_deleted[]" value="0">
                        <img class="cursor-hand inline-img pull-right" src="../img/remove.png" onclick="remComp(this);">
                        <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addComp(this);">
                    </div>
                    <div class="scale-to-fill">
                        <div class="col-sm-6">
                            <label class="show-on-mob">Compensation for Item:</label>
                            <select name="comp_item[]" class="chosen-select-deselect" data-placeholder="Select Compensation Item" onchange="if(['ticket','day','hour','km'].indexOf(this.value) >= 0) { $(this).closest('.form-group').find('[name^=comp_percent]').prop('readonly',true); $(this).closest('.form-group').find('[name^=comp_fee]').prop('readonly',false); } else { $(this).closest('.form-group').find('[name^=comp_percent]').prop('readonly',false); $(this).closest('.form-group').find('[name^=comp_fee]').prop('readonly',true); }"><option />
                                <option value="services" <?= $comp_row['item_type'] == 'services' ? 'selected' : '' ?>>Services</option>
                                <option value="inventory" <?= $comp_row['item_type'] == 'inventory' ? 'selected' : '' ?>><?= INVENTORY_TILE ?></option>
                                <option value="product" <?= $comp_row['item_type'] == 'product' ? 'selected' : '' ?>>Product</option>
                                <option value="equipment" <?= $comp_row['item_type'] == 'equipment' ? 'selected' : '' ?>>Equipment</option>
                                <option value="labour" <?= $comp_row['item_type'] == 'labour' ? 'selected' : '' ?>>Labour</option>
                                <option value="material" <?= $comp_row['item_type'] == 'material' ? 'selected' : '' ?>>Material</option>
                                <option value="vpl" <?= $comp_row['item_type'] == 'vpl' ? 'selected' : '' ?>>Vendor Pricelist</option>
                                <option value="position" <?= $comp_row['item_type'] == 'position' ? 'selected' : '' ?>>Position</option>
                                <option value="staff" <?= $comp_row['item_type'] == 'staff' ? 'selected' : '' ?>>Staff</option>
                                <option value="misc" <?= $comp_row['item_type'] == 'misc' ? 'selected' : '' ?>>Miscellaneous Item</option>
                                <option value="ticket" <?= $comp_row['item_type'] == 'ticket' ? 'selected' : '' ?>><?= TICKET_NOUN ?></option>
                                <option value="day" <?= $comp_row['item_type'] == 'day' ? 'selected' : '' ?>>Daily</option>
                                <option value="hour" <?= $comp_row['item_type'] == 'hour' ? 'selected' : '' ?>>Hourly</option>
                                <option value="km" <?= $comp_row['item_type'] == 'km' ? 'selected' : '' ?>>Per KM</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="show-on-mob">Compensation Rate (%):</label>
                            <input name="comp_percent[]" <?= in_array($comp_row['item_type'],['ticket','day','hour','km']) ? 'readonly' : '' ?> type="number" min=0 max=100 step="any" class="form-control" value="<?= $comp_row['comp_percent'] ?>" />
                        </div>
                        <div class="col-sm-3">
                            <label class="show-on-mob">Compensation Amount ($):</label>
                            <input name="comp_fee[]" <?= in_array($comp_row['item_type'],['ticket','day','hour','km']) ? '' : 'readonly' ?> type="number" min=0 max=100 step="any" class="form-control" value="<?= $comp_row['comp_fee'] ?>" />
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php } while($comp_row = $comp_rates->fetch_assoc()); ?>
        <?php } ?>
    </div>
    <div class="col-sm-12">
        <?php if (strpos($value_config, ','."Equip Compensation".',') !== FALSE) { ?>
            <h4>Equipment Compensation</h4>
            <div class="form-group hide-titles-mob">
                <div class="pull-right" style="width:5em;">&nbsp;</div>
                <div class="scale-to-fill">
                    <label class="col-sm-6 text-center">Compensation for Item</label>
                    <label class="col-sm-3 text-center">Compensation Rate (%)</label>
                    <label class="col-sm-3 text-center">Compensation Amt ($)</label>
                </div>
                <div class="clearfix"></div>
            </div>

            <?php $comp_rates = $dbc->query("SELECT * FROM `rate_compensation` WHERE `rate_card`='".$ratecardid."' AND `item_type` LIKE 'equip_%' AND `deleted`=0");
            $comp_row = $comp_rates->fetch_assoc();
            do { ?>
                <div class="form-group compensation_detail">
                    <div class="pull-right" style="width:5em;">
                        <input type="hidden" name="comp_id[]" value="<?= $comp_row['id'] ?>">
                        <input type="hidden" name="comp_deleted[]" value="0">
                        <img class="cursor-hand inline-img pull-right" src="../img/remove.png" onclick="remComp(this);">
                        <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addComp(this);">
                    </div>
                    <div class="scale-to-fill">
                        <div class="col-sm-6">
                            <label class="show-on-mob">Compensation for Item:</label>
                            <select name="comp_item[]" class="chosen-select-deselect" data-placeholder="Select Compensation Item" onchange="if(['equip_ticket','equip_day','equip_hour','equip_km'].indexOf(this.value) >= 0) { $(this).closest('.form-group').find('[name^=comp_percent]').prop('readonly',true); $(this).closest('.form-group').find('[name^=comp_fee]').prop('readonly',false); } else { $(this).closest('.form-group').find('[name^=comp_percent]').prop('readonly',false); $(this).closest('.form-group').find('[name^=comp_fee]').prop('readonly',true); }"><option />
                                <option value="equip_ticket" <?= $comp_row['item_type'] == 'equip_ticket' ? 'selected' : '' ?>><?= TICKET_NOUN ?></option>
                                <!--<option value="equip_day" <?= $comp_row['item_type'] == 'equip_day' ? 'selected' : '' ?>>Daily</option>
                                <option value="equip_hour" <?= $comp_row['item_type'] == 'equip_hour' ? 'selected' : '' ?>>Hourly</option>-->
                                <option value="equip_km" <?= $comp_row['item_type'] == 'equip_km' ? 'selected' : '' ?>>Per KM</option>
                                <option value="equip_services" <?= $comp_row['item_type'] == 'equip_services' ? 'selected' : '' ?>>Services</option>
                                <option value="equip_inventory" <?= $comp_row['item_type'] == 'equip_inventory' ? 'selected' : '' ?>><?= INVENTORY_TILE ?></option>
                                <option value="equip_product" <?= $comp_row['item_type'] == 'equip_product' ? 'selected' : '' ?>>Product</option>
                                <option value="equip_equipment" <?= $comp_row['item_type'] == 'equip_equipment' ? 'selected' : '' ?>>Equipment</option>
                                <option value="equip_labour" <?= $comp_row['item_type'] == 'equip_labour' ? 'selected' : '' ?>>Labour</option>
                                <option value="equip_material" <?= $comp_row['item_type'] == 'equip_material' ? 'selected' : '' ?>>Material</option>
                                <option value="equip_vpl" <?= $comp_row['item_type'] == 'equip_vpl' ? 'selected' : '' ?>>Vendor Pricelist</option>
                                <option value="equip_position" <?= $comp_row['item_type'] == 'equip_position' ? 'selected' : '' ?>>Position</option>
                                <option value="equip_staff" <?= $comp_row['item_type'] == 'equip_staff' ? 'selected' : '' ?>>Staff</option>
                                <option value="equip_misc" <?= $comp_row['item_type'] == 'equip_misc' ? 'selected' : '' ?>>Miscellaneous Item</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label class="show-on-mob">Compensation Rate (%):</label>
                            <input name="comp_percent[]" <?= in_array($comp_row['item_type'],['equip_ticket','equip_day','equip_hour','equip_km']) ? 'readonly' : '' ?> type="number" min=0 max=100 step="any" class="form-control" value="<?= $comp_row['comp_percent'] ?>" />
                        </div>
                        <div class="col-sm-3">
                            <label class="show-on-mob">Compensation Amount ($):</label>
                            <input name="comp_fee[]" <?= in_array($comp_row['item_type'],['equip_ticket','equip_day','equip_hour','equip_km']) ? '' : 'readonly' ?> type="number" min=0 max=100 step="any" class="form-control" value="<?= $comp_row['comp_fee'] ?>" />
                        </div>
                    </div>
                    <div class="clearfix"></div>
                </div>
            <?php } while($comp_row = $comp_rates->fetch_assoc()); ?>
        <?php } ?>
    </div>
</div>