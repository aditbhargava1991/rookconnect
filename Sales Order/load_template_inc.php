<?php include_once('../include.php');
checkAuthorised('sales_order');
$so_type = $_GET['so_type'];
$cat_config = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_so_contacts` WHERE `sales_order_type` = '$so_type'"),MYSQLI_ASSOC);
$contact_categories = [];
foreach ($cat_config as $contact_cat) {
	$contact_categories[] = $contact_cat['contact_category'];
}
if(empty($contact_categories)) {
	$contact_categories[] = '**no_cat**';
}
$item_from_types = explode(',', mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `product_fields` FROM `field_config_so`"))['product_fields']);
if(!empty($so_type)) {
    $item_from_types = explode(',',get_config($dbc,'so_'.config_safe_str($so_type).'_product_fields'));
}

$templateid = $_GET['templateid'];
$load_type = $_GET['load_type'];
if($load_type == 'sales_order') {
	$template = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_temp` WHERE `sotid` = '$templateid'"));
	$load_table = 'sales_order_product_temp';
	$load_table_id = 'parentsotid';
	$load_table_row_id = 'sotid';
} else {
	$template = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_order_template` WHERE `id` = '$templateid'"));
	$load_table = 'sales_order_template_product';
	$load_table_id = 'template_id';
	$load_table_row_id = 'id';
}

?>
<script type="text/javascript">
$(document).ready(function() {
    //iFrame
    $('.iframe_open').click(function(){
    	var templateid = $('#templateid').val();
        var category = $(this).data('category');
        var title    = $(this).data('title');
        var pricing  = $(this).data('pricing');
        var contact_category = $(this).data('contact-category');
        $('#iframe_instead_of_window').attr('src', 'get_products.php?<?= $load_type == 'sales_order' ? 'sotid' : 'templateid' ?>='+templateid+'&category='+category+'&pricing='+pricing+'&contact_category='+contact_category+'&from_type=load_template&load_type=<?= $load_type ?>');
        $('.iframe_title').text(title);
        $('.iframe_holder2 iframe').outerHeight($('.iframe_holder2').closest('html').outerHeight());
        $('.iframe_holder2').show();
        $('.hide_on_iframe2').hide();
    });

    $('.close_iframer').click(function(){
        $('.iframe_holder2').hide();
        $('.hide_on_iframe2').show();
    });

    $('iframe').load(function() {
        this.contentWindow.document.body.style.overflow = 'scroll';
        this.contentWindow.document.body.style.minHeight = '0';
        this.contentWindow.document.body.style.paddingBottom = '15em';
        this.style.height = (this.contentWindow.document.body.offsetHeight + 10) + 'px';
    });
});
window.onpopstate = function() {
    $('.iframe_holder2').hide();
    $('.hide_on_iframe2').show();
}
//Edit Heading
function editHeading(sel) {
	var row = $(sel).closest('.heading_row');
	row.find('.heading_row_text').hide();
	row.find('.heading_row_edit').show();
}

//Edit Heading Display Quantity
function displayMandatoryQuantity(sel) {
	if ($(sel).is(':checked')) {
		$(sel).closest('.heading_row_edit').find('.mandatory_quantity').show();
	} else {
		$(sel).closest('.heading_row_edit').find('.mandatory_quantity').hide();
	}
}

//Save Heading
function saveHeading(sel) {
	var row = $(sel).closest('.heading_details');
	var heading_name = row.find('[name="heading_name"]').val();
	var mandatory_checkbox = 0;
	var mandatory_quantity = 0;
	if (row.find('[name="mandatory_checkbox"]').is(':checked')) {
		mandatory_checkbox = 1;
		mandatory_quantity = row.find('[name="mandatory_quantity"]').val();
	}
	var heading_text = heading_name+(mandatory_quantity > 0 ? ' (Mandatory Quantity: '+mandatory_quantity+')' : '');
	$(sel).closest('.heading_row').find('.heading_row_text').show().find('b').text(heading_text);
	$(sel).closest('.heading_row').find('.heading_row_edit').hide();
	$(sel).closest('.heading_block').find('.product_headingname').val(heading_name);
	$(sel).closest('.heading_block').find('.product_mandatory').val(mandatory_quantity);
}

//Remove item
function removeItem(a) {
	$(a).closest('tr').remove();
}
</script>

<?php foreach($contact_categories as $contact_category) { ?>
	<div class="accordion-block-details padded" data-contact-category="<?= $contact_category ?>">
	    <div class="accordion-block-details-heading"><h4><?= $contact_category == '**no_cat**' ? 'Sales' : $contact_category ?> Order Details</h4></div>
		<?php foreach ($item_from_types as $item_from) {
			$include_hours = false;
			if($item_from == 'Services') {
				$service_fields = ','.get_field_config($dbc,'services').',';
				if(strpos($service_fields, ',Estimated Hours,') !== false) {
					$include_hours = true;
				}
			} ?>
			<div class="row item_type_div" data-item-type="<?= $item_from ?>">
		    	<div class="row double-gap-top"><div class="col-sm-12 default-color"><h4><?= $item_from ?></h4></div></div><?php
				$headings = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `heading_name`, `mandatory_quantity`, IF(`heading_sortorder` = 0, NULL, `heading_sortorder`) `heading_sortorder` FROM `$load_table` WHERE `$load_table_id` = '$templateid' AND `item_type` = '$item_from' AND `contact_category` = '$contact_category' GROUP BY `heading_name` ORDER BY `heading_sortorder` IS NOT NULL, `heading_sortorder` ASC"),MYSQLI_ASSOC);
				if(!empty($headings)) {
					foreach ($headings as $heading) {
						$heading_name = $heading['heading_name'];
						$mandatory_quantity = $heading['mandatory_quantity'];
				        $query  = "SELECT *, IF(`sortorder` = 0, NULL, `sortorder`) `sortorder` FROM `$load_table` WHERE `$load_table_id`='$templateid' AND `item_type`='$item_from' AND `contact_category` = '$contact_category' AND `heading_name` = '$heading_name' ORDER BY `sortorder` IS NOT NULL, `sortorder` ASC";
				        $result = mysqli_query($dbc, $query);

				        if ( $result->num_rows > 0 ) { ?>
				        	<div class="heading_block" data-heading="<?= $heading_name ?>">
				        		<img src="<?= WEBSITE_URL ?>/img/icons/drag_handle.png" class="inline-img heading_handle pull-right no-toggle" title="Drag">
					        	<div class="heading_row">
						            <div class="row heading_row_text"><div class="col-sm-12 default-color"><b><?= $heading_name.($mandatory_quantity > 0 ? ' (Mandatory Quantity: '.$mandatory_quantity.')' : '') ?></b> <a href="" onclick="editHeading(this); return false;"><span style="font-size: x-small; color: #888;">EDIT HEADING</span></a></div></div>
						            <div class="heading_row_edit" style="display: none;">
							            <div class="row">
							            	<div class="col-sm-3"><b>Heading Name</b></div>
							            	<div class="col-sm-2" style="text-align: center;"><b>Mandatory?</b></div>
							            	<div class="col-sm-3 mandatory_quantity" <?= ($mandatory_quantity == 0 ? 'style="display: none;"' : '') ?>><b>Mandatory Quantity</b></div>
							            	<div class="col-sm-2"></div>
							            </div>
							            <div class="row heading_details">
							            	<div class="col-sm-3"><input type="text" name="heading_name" value="<?= $heading_name ?>" class="form-control"></div>
							            	<div class="col-sm-2" style="text-align: center;"><input type="checkbox" name="mandatory_checkbox" value="1" <?= ($mandatory_quantity > 0 ? 'checked="checked"' : '') ?> style="transform: scale(1.5);" onclick="displayMandatoryQuantity(this);"></div>
							            	<div class="col-sm-3 mandatory_quantity" <?= ($mandatory_quantity == 0 ? 'style="display: none;"' : '') ?>><input type="number" name="mandatory_quantity" value="<?= $mandatory_quantity ?>" class="form-control"></div>
							            	<div class="col-sm-2"><button onclick="saveHeading(this); return false;" class="btn brand-btn" data-category="<?= $item_from ?>" data-contact-category="<?= $contact_category ?>" data-heading-name="<?= $heading_name ?>">Save</button></div>
							            </div>
							        </div>
							    </div>
			                	<table id="no-more-tables" class="table table-bordered table-striped product_table">
			                		<thead>
				                		<tr class="hidden-xs">
				                			<th width="20%">Category</th>
				                			<th width="<?= $include_hours ? '30' : '40' ?>%">Product</th>
				                			<?php if($include_hours) { ?>
					                			<th width="10%">Time Estimate</th>
				                			<?php } ?>
				                			<th width="10%">Price</th>
				                			<th width="5%"></th>
				                		</tr>
				                	</thead>

				                	<tbody><?php
							            while ( $row=mysqli_fetch_assoc($result) ) {
											$rate = $row['item_price'];
											if($load_table == 'sales_order_template_product' && $_GET['sotid'] > 0 && $item_from == 'Services') {
												$customer = $dbc->query("SELECT `customerid` FROM `sales_order_temp` WHERE `sotid`='{$_GET['sotid']}'")->fetch_assoc()['customerid'];
												$rate_info = $dbc->query("SELECT `{$row['item_type']}` `price` FROM `rate_card` WHERE `clientid`='$customer' AND `clientid` > 0 AND CONCAT('**',`{$row['item_type']}`,'#') LIKE '%**{$row['item_type_id']}#%' AND `deleted`=0 AND `on_off`=1 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION
													SELECT `service_rate` `price` FROM `service_rate_card` WHERE `deleted`=0 AND `serviceid`='{$row['item_type_id']}' AND '{$row['item_type']}'='services' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION
													SELECT `cust_price` `price` FROM `company_rate_card` WHERE LOWER(`tile_name`)='{$row['item_type']}' AND `item_id`='{$row['item_type_id']}' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')")->fetch_assoc()['price'];
												$rate = 0;
												if(strpos($rate_info,'#') !== FALSE) {
													foreach(explode('**',$rate_info) as $rate_line) {
														$rate_line = explode('#',$rate_line);
														if($rate_line[0] == $row['item_type_id']) {
															$rate = $rate_line[1];
														}
													}
												} else {
													$rate = $rate_info;
												}
											} ?>
							            	<tr>
							                	<input type="hidden" class="product_headingname" name="product_headingname[]" value="<?= $row['heading_name'] ?>">
							                	<input type="hidden" class="product_mandatory" name="product_mandatory[]" value="<?= $row['mandatory_quantity'] ?>">
							                	<input type="hidden" name="product_id[]" value="<?=$row[$load_table_row_id] ?>">
							                	<input type="hidden" name="product_item_type[]" value="<?=$row['item_type'] ?>">
							                	<input type="hidden" name="product_item_type_id[]" value="<?=$row['item_type_id'] ?>">
							                	<input type="hidden" name="product_item_category[]" value="<?=$row['item_category'] ?>">
							                	<input type="hidden" name="product_item_name[]" value="<?=$row['item_name'] ?>">
							                	<input type="hidden" name="product_contact_category[]" value="<?=$row['contact_category'] ?>">
							                	<td data-title="Category"><?= $row['item_category'] ?></td>
							                	<td data-title="Product"><?= $row['item_name'] ?></td>
					                			<?php if($include_hours) { ?>
					                				<td data-title="Time Estimate"><input type="text" name="time_estimate[]" class="form-control timepicker" value="<?= $row['time_estimate'] ?>" onchange="updatePrice(this);"></td>
					                			<?php } else { ?>
					                				<input type="hidden" name="time_estimate[]" value="">
				                				<?php } ?>
							                	<td data-title="Price"><input type="number" name="product_price[]" value="<?= $rate ?>" class="form-control" step="0.01"></td>
							                	<!-- <td align="center" data-title="Include"><input type="checkbox" name="product_id[]" value="<?= $row['id'] ?>" checked="checked" style="width: 20px; height: 20px;"></td> -->
							                	<td align="center"><img src="<?= WEBSITE_URL; ?>/img/remove.png" height="20" onclick="removeItem(this);" /><img src="<?= WEBSITE_URL ?>/img/icons/drag_handle.png" class="inline-img sortable_handle pull-right no-toggle" title="Drag"></td>
							                </tr><?php
							            } ?>
							        </tbody>
						        </table>
					        </div>
				        <?php }
					}
				} else {
					echo 'No Items Found';
				} ?>
			</div>
            <!-- <div class="col-sm-3 gap-md-left-15 pad-top-5">Add Item From:</div>
            <div class="col-sm-7"><a href="javascript:void(0);" class="btn brand-btn iframe_open" data-category="<?= $item_from ?>" data-title="Select items from <?= $item_from ?>" data-contact-category="<?= $contact_category ?>"><?= $item_from ?></a></div> -->
			<div class="clearfix"></div>
            <div class="col-sm-12"><a href="javascript:void(0);" class="iframe_open" data-category="<?= $item_from ?>" data-title="Select items from <?= $item_from ?>" data-contact-category="<?= $contact_category ?>"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" height="20" class="pull-right"></a></div>
			<div class="clearfix"></div>
			<hr>
		<?php } ?>
	</div>
<?php } ?>