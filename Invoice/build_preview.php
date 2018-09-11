<?php include_once('config.php'); ?>
<?php if($_GET['inv_mode'] === 'adjust') { ?>
    <div class="col-sm-12" style="text-align:right;"><h3>
        <label style="color: blue;">Adjustment <input type="checkbox" onchange="if(this.checked) { $('.adjust_block').show(); $('[name=paid]').first().change(); } else { $('.adjust_block').hide(); }"></label>
        <label style="color: red;">Refund <input type="checkbox" onchange="if(this.checked) { $('.return_block').show(); } else { $('.return_block').hide(); }"></label>
    </h3></div>
<?php } ?>
<script>
<?php if($_GET['inv_mode'] === 'adjust') { ?>
    $(document).ready(function() {
        $('.adjust_block').hide();
        $('.return_block').hide();
    });
<?php } ?>
</script>
<div class="col-sm-12 preview_div">
    <h3><?= $_GET['inv_mode'] === 'adjust' ? 'Adjustment' : 'Invoice' ?> Details</h3>
    <h4 <?= (in_array('invoice_date',$field_config) ? '' : 'style="display:none;"') ?>>Invoice Date: <label class="detail_invoice_date pull-right"><?= date('Y-m-d') ?></label></h4>
    <h4 <?= (in_array('customer',$field_config) ? '' : 'style="display:none;"') ?>><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?>: <label class="detail_patient_name pull-right"><?= (empty($_GET['invoiceid']) ? get_contact($dbc, $_GET['contactid']) : $patient) ?></label></h4>
    <h4 <?= (in_array('injury',$field_config) ? '' : 'style="display:none;"') ?>>Injury: <label class="detail_patient_injury pull-right"><?= (empty($_GET['invoiceid']) ? '' : $injury) ?></label></h4>
    <h4 <?= (in_array('treatment',$field_config) ? '' : 'style="display:none;"') ?>>Treatment Plan: <label class="detail_patient_treatment pull-right"><?= (empty($_GET['invoiceid']) ? '' : $treatment_plan) ?></label></h4>
    <h4 <?= (in_array('staff',$field_config) ? '' : 'style="display:none;"') ?>>Staff: <label class="detail_staff_name pull-right"><?= (empty($_GET['invoiceid']) ? '' : $staff) ?></label></h4>
    <h4 <?= (in_array('services',$field_config) || in_array('unbilled_tickets',$field_config) ? '' : 'style="display:none;"') ?>>Services</h4>
    <div class="detail_service_list" <?= (in_array('services',$field_config) || in_array('unbilled_tickets',$field_config) ? '' : 'style="display:none;"') ?>></div>
    <h4 <?= (in_array('inventory',$field_config) ? '' : 'style="display:none;"') ?>><?= INVENTORY_TILE ?></h4>
    <div class="detail_inventory_list" <?= (in_array('inventory',$field_config) ? '' : 'style="display:none;"') ?>></div>
    <h4 <?= (in_array('products',$field_config) ? '' : 'style="display:none;"') ?>>Products</h4>
    <div class="detail_products_list" <?= (in_array('products',$field_config) ? '' : 'style="display:none;"') ?>></div>
    <h4 <?= (in_array('packages',$field_config) ? '' : 'style="display:none;"') ?>>Packages</h4>
    <div class="detail_package_list" <?= (in_array('packages',$field_config) ? '' : 'style="display:none;"') ?>></div>
    <h4 <?= (in_array('misc_items',$field_config) || in_array('unbilled_tickets',$field_config) ? '' : 'style="display:none;"') ?>>Miscellaneous Items</h4>
    <div class="detail_misc_list" <?= (in_array('misc_items',$field_config) || in_array('unbilled_tickets',$field_config) ? '' : 'style="display:none;"') ?>></div>
    <h4>Sub-Total: <label class="detail_sub_total_amt pull-right">$0.00</label></h4>
    <h4 <?= (in_array('promo',$field_config) ? '' : 'style="display:none;"') ?>>Promotion: <label class="detail_promo_amt pull-right"><?= $promotionid > 0 ? '' : 'N/A' ?></label></h4>
    <h4 <?= (in_array('discount',$field_config) ? '' : 'style="display:none;"') ?>>Discount: <label class="detail_discount_amt pull-right">$0.00</label></h4>
    <h4>Sub-Total after Discount: <label class="detail_sub_total_after_discount pull-right">$0.00</label></h4>
    <h4 <?= (in_array('delivery',$field_config) ? '' : 'style="display:none;"') ?>>Delivery: <label class="detail_shipping_amt pull-right">$0.00</label></h4>
    <h4 <?= (in_array('assembly',$field_config) ? '' : 'style="display:none;"') ?>>Assembly: <label class="detail_assembly_amt pull-right">$0.00</label></h4>
    <h4>Total before Tax: <label class="detail_mid_total_amt pull-right">$0.00</label></h4>
    <h4>GST: <label class="detail_gst_amt pull-right">$0.00</label></h4>
    <h4 <?= (in_array('tips',$field_config) ? '' : 'style="display:none;"') ?>>Gratuity: <label class="detail_gratuity_amt pull-right">$0.00</label></h4>
    <h4 <?= (in_array('gf',$field_config) ? '' : 'style="display:none;"') ?>>Gift Card Value: <label class="detail_gf_amt pull-right"><span id="detail_gift_amount">N/A</span></label></h4>
    <h4 style="display:none;">Credit to Account: <label class="detail_credit_balance pull-right">$0.00</label></h4>
    <h4>Total: <label class="detail_total_amt pull-right">$0.00</label></h4>
    <h4 style="display:none;">Refund Amount: <label class="detail_refund_amt pull-right">$0.00</label></h4>
    <h4 style="display:none;">Adjustment Amount: <label class="detail_adjust_amt pull-right">$0.00</label></h4>
    <h4 style="display:none;"><?= count($payer_config) > 1 ? 'Third Party' : $payer_config[0] ?> Portion: <label class="detail_insurer_amt pull-right">$0.00</label></h4>
    <h4 style="display:none;"><?= count($purchaser_config) > 1 ? 'Customer' : $purchaser_config[0] ?> Portion: <label class="detail_patient_amt pull-right">$0.00</label></h4>
</div>