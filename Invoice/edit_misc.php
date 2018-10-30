<div id="inv_misc">
    <h4 class="col-sm-12">Miscellaneous<span class="popover-examples list-inline">
        <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Add any Miscellaneous Items here."><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
    </span><img src="../img/icons/ROOK-add-icon.png" class="no-toggle cursor-hand adjust_block" title="Add Miscellaneous Item" width="21" onclick="add_misc_row();" /></h4>
    <div class="form-group misc_option" <?= (in_array('misc_items',$field_config) ? '' : 'style="display:none;"') ?>>
        <div class="col-sm-12">
            <div class="form-group clearfix hide-titles-mob misc_labels" style="<?= ( empty(rtrim($misc_items, ',')) && $_GET['inv_mode'] == 'adjust' ) ? 'display:none;' : '' ?>">
                <label class="col-sm-<?= $_GET['inv_mode'] == 'adjust' ? '3' : '4' ?> text-center">Product Name</label>
                <label class="col-sm-1 text-center">Qty</label>
                <label class="col-sm-<?= $_GET['inv_mode'] == 'adjust' ? '2' : '3' ?> text-center">Price</label>
                <label class="col-sm-2 text-center">Total</label>
                <?php if($_GET['inv_mode'] == 'adjust') { ?>
                    <label class="col-sm-1 text-center return_block">Return</label>
                <?php } ?>
            </div>

            <?php $each_misc = array_filter(explode(',', $misc_items));
            $each_misc[] = '';
            $each_misc_ticketid = explode(',', $misc_ticketid);
            $each_misc_price = explode(',', $misc_prices);
            $each_misc_qty = explode(',', $misc_qtys);
            foreach($each_misc as $loop => $misc_item) {
                if(!($each_misc_ticketid[$loop] > 0)) {
                    $misc_price = $each_misc_price[$loop] * $discount_percent;
                    $misc_qty = $each_misc_qty[$loop]; ?>
                    <div class="additional_misc form-group clearfix <?= empty($misc_item) && empty($each_misc_qty[$loop]) && $_GET['inv_mode'] == 'adjust' ? 'adjust_block2' : ($_GET['inv_mode'] == 'adjust' ? 'refundable' : '') ?>" style="<?= empty($misc_item) && empty($each_misc_qty[$loop]) && $_GET['inv_mode'] == 'adjust' ? 'display:none;' : '' ?>">
                        <input type="hidden" name="misc_ticketid[]" value="">
                        <div class="col-sm-<?= $_GET['inv_mode'] == 'adjust' ? '3' : '4' ?>"><label class="show-on-mob">Product Name:</label>
                            <input type="text" <?= !empty($misc_item) && $_GET['inv_mode'] == 'adjust' ? 'readonly' : '' ?> name="misc_item[]" value="<?= $misc_item ?>" class="form-control misc_name">
                        </div>
                        <div class="col-sm-1"><label class="show-on-mob">Quantity:</label>
                            <input type="number" <?= !empty($misc_item) && $_GET['inv_mode'] == 'adjust' ? 'readonly' : '' ?> step="any" min="0" name="misc_qty[]" value="<?= $misc_qty ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control <?= $_GET['inv_mode'] == 'adjust' && $misc_qty > 0 ? 'init_qty' : 'misc_qty' ?>">
                        </div>
                        <div class="col-sm-<?= $_GET['inv_mode'] == 'adjust' ? '2' : '3' ?>"><label class="show-on-mob">Unit Price:</label>
                            <input type="number" <?= !empty($misc_item) && $_GET['inv_mode'] == 'adjust' ? 'readonly' : '' ?> step="any" min="0" name="misc_price[]" value="<?= number_format($misc_price / $misc_qty,2) ?>" onchange="setThirdPartyMisc(this); countTotalPrice()" class="form-control misc_price">
                        </div>
                        <div class="col-sm-2"><label class="show-on-mob">Total:</label>
                            <input type="number" <?= !empty($misc_item) && $_GET['inv_mode'] == 'adjust' ? 'readonly' : '' ?> readonly name="misc_total[]" value="<?= $misc_price ?>" class="form-control misc_total">
                            <input name="misc_row_id[]" type="hidden" value="<?= $insurer_row_id++ ?>" class="insurer_row_id" />
                        </div>
                        <?php if($_GET['inv_mode'] == 'adjust') { ?>
                            <div class="col-sm-2 return_block">
                                <?php if(!empty($misc_item)) { ?>
                                    <label class="show-on-mob">Refund Qty:</label>
                                    <input type="number" name="misc_return[]" step="any" max="0" min="<?= -$misc_qty ?>" value="0" onchange="countTotalPrice()" class="form-control <?= (empty($misc_item) ? '' : 'misc_qty') ?>">
                                <?php } ?>
                            </div>
                        <?php } ?>
                        <div class="col-sm-2 adjust_block">
                            <img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="rem_misc_row(this);">
                            <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right cursor-hand" onclick="add_misc_row();">
                        </div>
                        <div class="col-sm-12 pay-div"></div>
                    </div>
                <?php }
            } ?>
            <div id="add_here_new_misc"></div>
        </div>
    </div>
</div>