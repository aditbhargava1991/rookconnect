<!-- Sales Order Details Preview -->
<?php 
$cat_config = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_contacts` WHERE `sales_order_type` = '$so_type'"),MYSQLI_ASSOC);
$contact_categories = [];
foreach ($cat_config as $contact_cat) {
    $contact_categories[] = $contact_cat['contact_category'];
}
?>
<script>
	$(document).ready(function() {
        updateItemDetails();
        updateItemOrderDetails();
        $('[name="item_quantity[]"]').off('change', updateItemDetails).change(updateItemDetails);
    });
</script>
<?php $preview_type = 'order_details';
include('details_preview_functions.php'); ?>

<?php
    // GST PST
    $get_pos_tax = get_config($dbc, 'sales_order_tax');
    $gst_total = 0;
    $pst_total = 0;
    if($get_pos_tax != '') {
        $pos_tax = explode('*#*',$get_pos_tax);

        $total_count = mb_substr_count($get_pos_tax,'*#*');
        for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
            $pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

            if (strcasecmp($pos_tax_name_rate[0], 'gst') == 0 && $pos_tax_name_rate[3] != 'Yes') {
                $gst_total = $gst_total + $pos_tax_name_rate[1];
            }

            if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0 && $pos_tax_name_rate[3] != 'Yes') {
                $pst_total = $pst_total + $pos_tax_name_rate[1];
            }
        }
    }
    $subtotal = 0;
    // Products
    $product_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `sales_order_product_temp` WHERE `parentsotid` = '$sotid'"),MYSQLI_ASSOC);
    foreach ($product_list as $product) {
        $product_price = number_format($product['item_price'], 2);
        $product_details_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `sales_order_product_details_temp` WHERE `parentsotid` ='".$product['sotid']."'"),MYSQLI_ASSOC);
        foreach ($product_details_list as $product_details) {
            $subtotal = $subtotal + ($product_price * $product_details['quantity']);
        }
    }
    $subtotal = number_format($subtotal, 2);
?>

<input type="hidden" id="subtotal" name="subtotal" value="<?= $subtotal ?>">
<input type="hidden" id="gst_rate" name="gst_rate" value="<?= $gst_total ?>">
<input type="hidden" id="pst_rate" name="pst_rate" value="<?= $pst_total ?>">
<input type="hidden" id="discount_type" name="discount_type" value="<?= $discount_type ?>">
<input type="hidden" id="discount_value" name="discount_value" value="<?= $discount_value ?>">
<input type="hidden" id="delivery_type" name="delivery_type" value="<?= $delivery_type ?>">
<input type="hidden" id="delivery_amount" name="delivery_amount" value="<?= $delivery_amount ?>">
<input type="hidden" id="assembly_amount" name="assembly_amount" value="<?= $assembly_amount ?>">
<input type="hidden" id="deposit_paid" name="deposit_paid" value="<?= $deposit_paid ?>">

<div class="main-screen-white double-gap-top" style="border: 1px solid #ACA9A9">
    <div class="preview-block-container">
        <div class="preview-block">
            <div class="preview-block-header">
                <h4><?= SALES_ORDER_NOUN ?></h4>
            </div>
            
            <div class="padded">
                <?php foreach ($contact_categories as $contact_category) { ?>
                    <div class="preview_<?= $contact_category ?>_details"></div><br>
                <?php } ?>
                <?php if(empty($contact_categories)) { ?>
                    <div class="preview_nocat_details"></div><br>
                <?php } ?>
                <div class="preview_order_details"></div>
				
				<?php $notes = mysqli_query($dbc, "SELECT * FROM `sales_order_notes` WHERE `sales_order_id`='$sotid' AND `deleted`=0");
				if(mysqli_num_rows($notes) > 0) { ?>
					<div id="no-more-tables">
						<h5>ORDER NOTES</h5>
						<table class="table table-bordered table-striped">
							<thead>
                                <tr class="hidden-sm hidden-xs">
                                    <th>Note</th>
                                    <th>Created</th>
                                </tr>
                            </thead>
							<?php while($note = mysqli_fetch_assoc($notes)) { ?>
								<tr>
									<td data-title="Note"><?= html_entity_decode($note['note']) ?></td>
									<td data-title="Created"><?= get_contact($dbc, $note['created_by']).($note['created_by'] > 0 ? '<br />' : '') ?><?= date('Y-m-d',strtotime($note['created_date'])) ?></td>
								</tr>
							<?php } ?>
						</table>
					</div>
				<?php } ?>
            </div>

        </div><!-- .preview-block -->
    </div><!-- .preview-block-container -->
</div><!-- .main-screen-white -->

<div class="clearfix"></div>