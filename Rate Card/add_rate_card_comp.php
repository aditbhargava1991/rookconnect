<script>
function addComp() {
    var src = $('.compensation_detail:visible').last();
    destroyInputs();
    var clone = src.clone();
    clone.find('input,select').val('');
    src.after(clone);
    initInputs();
}
function remComp(img) {
    if($('.compensation_detail:visible').length <= 1) {
        addComp();
    }
    $(img).closest('.compensation_detail').hide().find('[name^=comp_deleted]').val(1);
}
</script>
<div class="form-group">
    <div class="col-sm-12">
        <div class="form-group hide-titles-mob">
            <div class="pull-right" style="width:5em;">&nbsp;</div>
            <div class="scale-to-fill">
                <label class="col-sm-6 text-center">Compensation for Item</label>
                <label class="col-sm-3 text-center">Compensation Rate (%)</label>
                <label class="col-sm-3 text-center">Compensation Amt ($)</label>
            </div>
            <div class="clearfix"></div>
        </div>

        <?php $comp_rates = $dbc->query("SELECT * FROM `rate_compensation` WHERE `rate_card`='".$ratecardid."' AND `deleted`=0");
        $comp_row = $comp_rates->fetch_assoc();
        do { ?>
            <div class="form-group compensation_detail">
                <div class="pull-right" style="width:5em;">
                    <input type="hidden" name="comp_id[]" value="<?= $comp_row['id'] ?>">
                    <input type="hidden" name="comp_deleted[]" value="0">
                    <img class="cursor-hand inline-img pull-right" src="../img/remove.png" onclick="remComp(this);">
                    <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addComp();">
                </div>
                <div class="scale-to-fill">
                    <div class="col-sm-6">
                        <label class="show-on-mob">Compensation for Item:</label>
                        <select name="comp_item[]" class="chosen-select-deselect" data-placeholder="Select Compensation Item" onchange="if(this.value == 'ticket') { $(this).closest('.form-group').find('[name^=comp_percent]').prop('readonly',true); $(this).closest('.form-group').find('[name^=comp_fee]').prop('readonly',false); } else { $(this).closest('.form-group').find('[name^=comp_percent]').prop('readonly',false); $(this).closest('.form-group').find('[name^=comp_fee]').prop('readonly',true); }"><option />
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
                        <input name="comp_percent[]" <?= $comp_row['item_type'] == 'ticket' ? 'readonly' : '' ?> type="number" min=0 max=100 step="any" class="form-control" value="<?= $comp_row['comp_percent'] ?>" />
                    </div>
                    <div class="col-sm-3">
                        <label class="show-on-mob">Compensation Amount ($):</label>
                        <input name="comp_fee[]" <?= $comp_row['item_type'] == 'ticket' ? '' : 'readonly' ?> type="number" min=0 max=100 step="any" class="form-control" value="<?= $comp_row['comp_fee'] ?>" />
                    </div>
                </div>
                <div class="clearfix"></div>
            </div>
        <?php } while($comp_row = $comp_rates->fetch_assoc()); ?>
    </div>
</div>