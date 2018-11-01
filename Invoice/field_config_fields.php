<?php include_once('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
error_reporting(0);
$invoice_fields = FOLDER_NAME.'_fields'; ?>
<script>
$(document).ready(function() {
	$('input').change(saveOptions);
});
function saveOptions() {
	var search = [];
	$('[name="<?= $invoice_fields ?>[]"]:checked').each(function() {
		search.push(this.value);
	});
	$.ajax({
		url: '../Invoice/invoice_ajax.php?action=general_config',
		method: 'POST',
		data: {
			name: '<?= $invoice_fields ?>',
			value: search
		}
	});
}
</script>
<div class="block-group form-horizontal">
	<h3>Activate Fields</h3>
	<?php $invoice_field_list = explode(',',get_config($dbc, $invoice_fields)); ?>
	<label class="form-checkbox"><input <?= (in_array('invoice_type',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="invoice_type"> Invoice Type</label>
	<label class="form-checkbox"><input <?= (in_array('customer',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="customer"> Customer</label>
	<label class="form-checkbox"><input <?= (in_array('injury',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="injury"> Injury</label>
	<label class="form-checkbox"><input <?= (in_array('staff',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="staff"> Staff (Providing Service)</label>
	<label class="form-checkbox"><input <?= (in_array('appt_type',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="appt_type"> Appointment Type</label>
	<label class="form-checkbox"><input <?= (in_array('treatment',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="treatment"> Treatment Plan</label>
	<label class="form-checkbox"><input <?= (in_array('service_date',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="service_date"> Service Date</label>
	<label class="form-checkbox"><input <?= (in_array('invoice_date',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="invoice_date"> Invoice Date</label>
	<label class="form-checkbox"><input <?= (in_array('pay_mode',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="pay_mode"> Payment Method</label>
	<label class="form-checkbox"><input <?= (in_array('pricing',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="pricing"> Pricing</label>
	<label class="form-checkbox"><input <?= (in_array('price_client',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="price_client"> Pricing - Client Price</label>
	<label class="form-checkbox"><input <?= (in_array('price_admin',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="price_admin"> Pricing - Admin Price</label>
	<label class="form-checkbox"><input <?= (in_array('price_commercial',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="price_commercial"> Pricing - Commercial Price</label>
	<label class="form-checkbox"><input <?= (in_array('price_wholesale',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="price_wholesale"> Pricing - Wholesale Price</label>
	<label class="form-checkbox"><input <?= (in_array('price_retail',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="price_retail"> Pricing - Final Retail Price</label>
	<label class="form-checkbox"><input <?= (in_array('price_preferred',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="price_preferred"> Pricing - Preferred Price</label>
	<label class="form-checkbox"><input <?= (in_array('price_po',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="price_po"> Pricing - Purchase Order Price</label>
	<label class="form-checkbox"><input <?= (in_array('price_sales',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="price_sales"> Pricing - <?= SALES_ORDER_NOUN ?> Price</label>
	<label class="form-checkbox"><input <?= (in_array('price_web',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="price_web"> Pricing - Web Price</label>
	<label class="form-checkbox"><input <?= (in_array('send_invoice',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="send_invoice"> Send Outbound Invoice</label>
	<label class="form-checkbox"><input <?= (in_array('discount',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="discount"> Discount</label>
	<label class="form-checkbox"><input <?= (in_array('coupon',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="coupon"> Coupon</label>
	<label class="form-checkbox"><input <?= (in_array('delivery',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="delivery"> Delivery / Pickup</label>
	<label class="form-checkbox"><input <?= (in_array('assembly',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="assembly"> Assembly</label>
	<label class="form-checkbox"><input <?= (in_array('comment',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="comment"> Comment</label>
	<label class="form-checkbox"><input <?= (in_array('tax_exempt',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="tax_exempt"> Tax Exemption</label>
	<label class="form-checkbox"><input <?= (in_array('created_by',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="created_by"> Created / Sold By</label>
	<label class="form-checkbox"><input <?= (in_array('ship_date',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="ship_date"> Ship Date</label>
	<label class="form-checkbox"><input <?= (in_array('services',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="services"> Services</label>
	<label class="form-checkbox"><input <?= (in_array('service_cat',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="service_cat"> Service - Category</label>
	<label class="form-checkbox"><input <?= (in_array('service_head',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="service_head"> Service - Heading</label>
	<label class="form-checkbox"><input <?= (in_array('service_price',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="service_price"> Service - Price</label>
	<label class="form-checkbox"><input <?= (in_array('service_qty',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="service_qty"> Service - Quantity</label>
	<label class="form-checkbox"><input <?= (in_array('products',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="products"> Products</label>
	<label class="form-checkbox"><input <?= (in_array('product_cat',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="product_cat"> Product - Category</label>
	<label class="form-checkbox"><input <?= (in_array('product_head',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="product_head"> Product - Heading</label>
	<label class="form-checkbox"><input <?= (in_array('product_price',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="product_price"> Product - Price</label>
	<label class="form-checkbox"><input <?= (in_array('product_qty',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="product_qty"> Product - Quantity</label>
	<label class="form-checkbox"><input <?= (in_array('inventory',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="inventory"> Inventory</label>
	<label class="form-checkbox"><input <?= (in_array('inventory_cat',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="inventory_cat"> Inventory - Category</label>
	<label class="form-checkbox"><input <?= (in_array('inventory_part',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="inventory_part"> Inventory - Part #</label>
	<label class="form-checkbox"><input <?= (in_array('inventory_type',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="inventory_type"> Inventory - Type</label>
	<label class="form-checkbox"><input <?= (in_array('inventory_price',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="inventory_price"> Inventory - Price</label>
	<label class="form-checkbox"><input <?= (in_array('packages',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="packages"> Packages</label>
	<label class="form-checkbox"><input <?= (in_array('packages_cat',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="packages_cat"> Packages - Category</label>
	<label class="form-checkbox"><input <?= (in_array('packages_name',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="packages_name"> Packages - Name</label>
	<label class="form-checkbox"><input <?= (in_array('packages_fee',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="packages_fee"> Packages - Fee</label>
	<label class="form-checkbox"><input <?= (in_array('misc_items',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="misc_items"> Misc Items</label>
	<label class="form-checkbox"><input <?= (in_array('deposit_paid',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="deposit_paid"> Deposit Paid</label>
	<label class="form-checkbox"><input <?= (in_array('due_date',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="due_date"> Due Date</label>
	<label class="form-checkbox"><input <?= (in_array('service_queue',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="service_queue"> Service Queue</label>
	<label class="form-checkbox"><input <?= (in_array('promo',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="promo"> Promotion</label>
	<label class="form-checkbox"><input <?= (in_array('tips',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="tips"> Gratuity</label>
	<label class="form-checkbox"><input <?= (in_array('next_appt',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="next_appt"> Next Appointment</label>
	<label class="form-checkbox"><input <?= (in_array('survey',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="survey"> Send Survey</label>
	<label class="form-checkbox"><input <?= (in_array('request_recommend',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="request_recommend"> Request Recommendation Report</label>
	<label class="form-checkbox"><input <?= (in_array('followup',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="followup"> Send Follow Up Email</label>
	<label class="form-checkbox"><input <?= (in_array('Customer Billing Status',$invoice_field_list) ? 'checked' : '') ?> type="checkbox" name="<?= $invoice_fields ?>[]" value="Customer Billing Status"> Customer Billing Status</label>

</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_fields.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>
