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
            <label class="col-sm-6 text-center">Compensation for Item</label>
            <label class="col-sm-4 text-center">Compensation Rate (%)</label>
            <label class="col-sm-2 text-center"></label>
            <div class="clearfix"></div>
        </div>

        <?php $comp_rates = $dbc->query("SELECT * FROM `rate_compensation` WHERE `rate_card`='".$ratecardid."' AND `deleted`=0");
        $comp_row = $comp_rates->fetch_assoc();
        do { ?>
            <div class="form-group compensation_detail">
                <div class="col-sm-6" >
                    <label class="col-sm-6 show-on-mob">Compensation for Item:</label>
                    <select name="comp_item[]" class="chosen-select-deselect" data-placeholder="Select Compensation Item"><option />
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
                    </select>
                </div>
                <div class="col-sm-4" >
                    <label class="col-sm-4 show-on-mob">Compensation Rate (%):</label>
                    <input name="comp_percent[]" type="number" min=0 max=100 step="any" class="form-control" value="<?= $comp_row['comp_percent'] ?>" />
                </div>
                <div class="col-sm-2">
                    <input type="hidden" name="comp_id[]" value="<?= $comp_row['id'] ?>">
                    <input type="hidden" name="comp_deleted[]" value="0">
                    <img class="cursor-hand inline-img pull-right" src="../img/remove.png" onclick="remComp(this);">
                    <img class="cursor-hand inline-img pull-right" src="../img/icons/ROOK-add-icon.png" onclick="addComp();">
                </div>
                <div class="clearfix"></div>
            </div>
        <?php } while($comp_row = $comp_rates->fetch_assoc()); ?>
    </div>
</div>