<?php
if(isset($_POST['save_template'])) {
	$templateid = $_POST['templateid'];
	$sales_order_type = $_POST['so_type'];
	$template_name = filter_var($_POST['template_name'],FILTER_SANITIZE_STRING);
	$region = filter_var($_POST['region'],FILTER_SANITIZE_STRING);
	$location = filter_var($_POST['location'],FILTER_SANITIZE_STRING);
	$classification = filter_var($_POST['classification'],FILTER_SANITIZE_STRING);
	if(empty($templateid) || $templateid == 'new') {
		$query_insert = "INSERT INTO `sales_order_template` (`template_name`, `sales_order_type`, `region`, `location`, `classification`) VALUES ('$template_name', '$sales_order_type', '$region', '$location', '$classification')";
		$result_insert = mysqli_query($dbc, $query_insert);
		$templateid = mysqli_insert_id($dbc);
	} else {
		$query_update = "UPDATE `sales_order_template` SET `template_name` = '$template_name', `sales_order_type` = '$sales_order_type', `region` = '$region', `classification` = '$classification' WHERE `id` = '$templateid'";
		$result_update = mysqli_query($dbc, $query_update);
	}

	echo '<script>window.location.href = "?templateid='.$templateid.'";</script>';
}
$regions = array_filter(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0]));
$locations = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
$classifications = array_filter(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_classification'"))[0]));
?>
<script>
$(document).ready(function() {
	sortableItems();
});
$(document).on('change', 'select[name="so_type"]', function() { changeSOType(this); });
function changeSOType(sel) {
	var templateid = $('#templateid').val();
    window.location.href = "?templateid="+templateid+"&so_type="+sel.value;
}

//Delete
function deleteRow(sel, hide) {
    var typeId = sel.id;
    var arr    = typeId.split('_');
    var id  = arr[1];
    $('#'+hide+arr[1]).hide();
    $.ajax({
        type: "GET",
        url: "ajax.php?from_type=template&fill=removeItem&id="+id,
        dataType: "html",
        success: function(response) {
        }
    });
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
	var templateid = $('#templateid').val();
	var item_type = $(sel).data('category');
	var contact_category = $(sel).data('contact-category');
	var old_heading_name = $(sel).data('heading-name');
	var row = $(sel).closest('.heading_details');
	var heading_name = row.find('[name="heading_name"]').val();
	var mandatory_checkbox = 0;
	if (row.find('[name="mandatory_checkbox"]').is(':checked')) {
		mandatory_checkbox = 1;
	}
	var mandatory_quantity = row.find('[name="mandatory_quantity"]').val();
	$.ajax({
		type: "POST",
		url: "ajax.php?from_type=template&fill=updateHeading&templateid="+templateid+"&item_type="+item_type+"&contact_category="+contact_category+"&old_heading_name="+old_heading_name+"&mandatory_checkbox="+mandatory_checkbox+"&mandatory_quantity="+mandatory_quantity+"&heading_name="+heading_name,
		dataType: "html",
		success: function(response) {
			$(sel).attr('data-heading-name', heading_name);
			$(sel).closest('.heading_row').find('.heading_row_text').show().find('b').text(response);
			$(sel).closest('.heading_row').find('.heading_row_edit').hide();
		}
	})
}

//Edit Price
function editPrice(sel) {
    var row = $(sel).closest('.row');
    row.find('.price_text').hide();
    row.find('.price_input').show().find('input').focus();
}

//Update Price
function updatePrice(sel) {
    var row = $(sel).closest('.row');
    var id = row.attr('id').split('_')[1];
    var price = $(sel).val();
    $.ajax({
    	async: false,
        url: 'ajax.php?from_type=template&fill=updateProductPrice&id='+id+'&price='+price,
        type: "GET",
        dataType: "html",
        success: function(response) {
            row.find('.price_text_number').text(response);
            $(sel).val(response);
            row.find('.price_text').show();
            row.find('.price_input').hide();
        }
    });
}

//Update Price
function updateTime(sel) {
    var row = $(sel).closest('.row');
    var id = row.attr('id').split('_')[1];
    var time = $(sel).val();
    var previous_time = $(sel).data('initial');
    $.ajax({
    	async: false,
        url: 'ajax.php?from_type=template&fill=updateProductTime&id='+id+'&time_estimate='+time,
        type: "GET",
        dataType: "html",
        success: function(response) {
        	$(sel).data('initial', $(sel).val())

            var price = row.find('[name="item_price_input"]').val();
			var minutes = time.split(':');
			minutes = (parseInt(minutes[0])*60) + parseInt(minutes[1]);
         	var previous_minutes = previous_time.split(':');
			previous_minutes = (parseInt(previous_minutes[0])*60) + parseInt(previous_minutes[1]);

			if(previous_minutes > 0 && minutes > 0 && previous_minutes != minutes) {
				price = minutes / previous_minutes * price;
				row.find('[name="item_price_input"]').val(price);
				updatePrice(row.find('[name="item_price_input"]'));
			}
        }
    });
}

//Delete Sales Order Template
function deleteSalesOrderTemplate() {
    if (confirm('Are you sure you want to delete this <?= SALES_ORDER_NOUN ?> Template?')) {
        var templateid = $('#templateid').val();
        $.ajax({
            type: 'GET',
            url: 'ajax.php?fill=deleteSalesOrderTemplate&templateid='+templateid,
            dataType: 'html',
            success: function(response) {
                window.location.href = 'templates.php?templateid=new';
            }
        });
    }
}

//Sortable items
function sortableItems() {
    $('.sortable_heading').sortable({
        items: '.sortable_row',
        handle: '.sortable_handle',
        stop: function(event, ui) {
            reorderItems();
        }
    });
    $('.item_div_block').sortable({
        items: '.sortable_heading',
        handle: '.heading_handle',
        stop: function(event, ui) {
            reorderItems();
        }
    });
}

//Sort order
function reorderItems() {
    var templateid = $('#templateid').val();
    $('.contact_type_div').each(function() {
        var contact_category = $(this).data('contact-category');
        $(this).find('.item_div_block').each(function() {
            var item_type = $(this).data('item-type');
            var heading_sortorder = 1;
            $(this).find('.sortable_heading').each(function() {
                var heading_name = $(this).find('[name="heading_name"]').val();
                var items = [];
                $(this).find('.sortable_row').each(function() {
                    items.push($(this).data('id'));
                });
                $.ajax({
                    url: '../Sales Order/ajax.php?fill=reoderItems',
                    method: 'POST',
                    data: { templateid: templateid, from_type: 'template', contact_category: contact_category, item_type: item_type, items: items, heading_sortorder: heading_sortorder },
                    success: function(response) {
                    }
                });
                heading_sortorder++;
            });
        });
    });
}
</script>
<form class="form-horizontal" action="" method="post" style="height: calc(100% - 2.5em);">
	<div class="standard-body main-screen-white" style="padding-left: 0; padding-right: 0; border: none;">
	    <div class="standard-body-title">
			<?php $templateid = filter_var($_GET['templateid'],FILTER_SANITIZE_STRING);
			if($templateid > 0) {
				$template = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `sales_order_template` WHERE `id`='$templateid'"));
				$so_type = $template['sales_order_type'];
			} else {
				$template = [];
				$so_type = $_GET['so_type'];
			} ?>
			<h3><?= ($templateid > 0 ? $template['template_name'].' Template' : 'Create New Template') ?></h3>
		</div>
		<div class="standard-body-content pad-10">
		    <?php $sales_order_types = get_config($dbc, 'sales_order_types');
		    if(!empty($sales_order_types)) { ?>
		        <div class="form-group">
		            <label class="col-sm-4 control-label"><?= SALES_ORDER_NOUN ?> Type:</label>
		            <div class="col-sm-8">
		                <select name="so_type" data-placeholder="Select a Type" class="chosen-select-deselect form-control">
		                    <?php foreach(explode(',', $sales_order_types) as $sales_order_type) {
		                        if(empty($so_type)) {
		                            $so_type = $sales_order_type;
		                        } ?>
		                        <option value="<?= $sales_order_type ?>" <?= $sales_order_type == $so_type ? 'selected' : '' ?>><?= $sales_order_type ?></option>
		                    <?php } ?>
		                </select>
		            </div>
		        </div>
		    <?php }
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
				$item_from_types = explode(',', get_config($dbc, 'so_'.config_safe_str($so_type).'_product_fields'));
			} ?>
			<input type="hidden" id="templateid" name="templateid" value="<?= $templateid ?>">
			<?php if($templateid == 'new' || $templateid > 0) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Template Name:</label>
					<div class="col-sm-8">
						<input type="text" name="template_name" value="<?= $template['template_name'] ?>" class="form-control" data-table="estimate_templates">
						<input type="hidden" name="id" value="<?= $template['id'] ?>">
					</div>
				</div>
				<?php if(count($regions) > 0) { ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Region:</label>
						<div class="col-sm-8">
							<select name="region" class="chosen-select-deselect form-control">
								<option></option>
								<?php foreach($regions as $region) { ?>
									<option <?= $template['region'] == $region ? 'selected' : '' ?> value="<?= $region ?>"><?= $region ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php }
				if(count($locations) > 0) { ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Location:</label>
						<div class="col-sm-8">
							<select name="location" class="chosen-select-deselect form-control">
								<option></option>
								<?php foreach($locations as $location) { ?>
									<option <?= $template['location'] == $location ? 'selected' : '' ?> value="<?= $location ?>"><?= $location ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php }
				if(count($classifications) > 0) { ?>
					<div class="form-group">
						<label class="col-sm-4 control-label">Classification:</label>
						<div class="col-sm-8">
							<select name="classification" class="chosen-select-deselect form-control">
								<option></option>
								<?php foreach($classifications as $classification) { ?>
									<option <?= $template['classification'] == $classification ? 'selected' : '' ?> value="<?= $classification ?>"><?= $classification ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
					<hr>
				<?php } ?>
				<?php foreach ($contact_categories as $contact_category) { ?>
					<div class="accordion-block-details padded contact_type_div" data-contact-category="<?= $contact_category ?>">
					    <div class="accordion-block-details-heading"><h4><?= $contact_category == '**no_cat**' ? 'Sales' : $contact_category ?> Order Details</h4></div>
					<?php foreach ($item_from_types as $item_from) {
						$include_hours = false;
						if($item_from == 'Services') {
							$service_fields = ','.get_field_config($dbc,'services').',';
							if(strpos($service_fields, ',Estimated Hours,') !== false) {
								$include_hours = true;
							}
						} ?>
						    <div class="row item_div_block" data-item-type="<?= $item_from ?>">
						    	<div class="row double-gap-top"><div class="col-sm-12 default-color"><h4><?= $item_from ?></h4></div></div><?php
						    	$headings = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `heading_name`, `mandatory_quantity`, IF(`heading_sortorder` = 0, NULL, `heading_sortorder`) `heading_sortorder` FROM `sales_order_template_product` WHERE `template_id` = '$templateid' AND `item_type` = '$item_from' AND `contact_category` = '$contact_category' GROUP BY `heading_name` ORDER BY `heading_sortorder` IS NOT NULL, `heading_sortorder` ASC"),MYSQLI_ASSOC);

						    	foreach ($headings as $heading) {
						    		$heading_name = $heading['heading_name'];
						    		$mandatory_quantity = $heading['mandatory_quantity'];
							        $query  = "SELECT *, IF(`sortorder` = 0, NULL, `sortorder`) `sortorder` FROM `sales_order_template_product` WHERE `template_id`='$templateid' AND `item_type`='$item_from' AND `contact_category` = '$contact_category' AND `heading_name` = '$heading_name' ORDER BY `sortorder` IS NOT NULL, `sortorder` ASC";
							        $result = mysqli_query($dbc, $query);

							        if ( $result->num_rows > 0 ) { ?>
					                    <div class="sortable_heading block-group">
					                        <img src="<?= WEBSITE_URL ?>/img/icons/drag_handle.png" class="inline-img heading_handle pull-right no-toggle" title="Drag">
								        	<div class="heading_row inline">
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
								            <div class="row hidden-xs">
								                <div class="col-sm-<?= $include_hours ? '3' : '4' ?>"><b>Category</b></div>
								                <div class="col-sm-<?= $include_hours ? '4' : '5' ?>"><b>Product</b></div>
								                <?php if($include_hours) { ?>
								                	<div class="col-sm-2"><b>Time Estimate</b></div>
								                <?php } ?>
								                <div class="col-sm-2"><b>Price</b></div>
								                <!-- <div class="col-sm-2"><b>Quantity</b></div> -->
								                <div class="col-sm-1"></div>
								            </div><?php
								            
								            while ( $row=mysqli_fetch_assoc($result) ) { ?>
								                <div class="row pad-top-5 sortable_row" data-id="<?= $row['id'] ?>" id="row_<?= $row["id"]; ?>">
								                    <div class="visible-xs-block col-xs-4"><b>Category: </b></div><div class="col-xs-9 col-sm-<?= $include_hours ? '3' : '4' ?>"><?= $row['item_category']; ?></div>
								                    <div class="visible-xs-block col-xs-4"><b>Product: </b></div><div class="col-sm-<?= $include_hours ? '3' : '4' ?>"><?= $row['item_name']; ?></div>
								                    <?php if($include_hours) { ?>
								                    	<div class="visible-xs-block col-xs-4"><b>Time Estimate:</b></div><div class="col-sm-2"><input type="text" name="time_estimate" value="<?= $row['time_estimate'] ?>" data-initial="<?= $row['time_estimate'] ?>" class="form-control timepicker-5" onchange="updateTime(this);"></div>
								                    <?php } ?>
								                    <div class="visible-xs-block col-xs-4"><b>Price: </b></div><div class="col-sm-2"><div class="price_text"><span class="price_text_number"><?= $row['item_price']; ?></span> <a href="" onclick="editPrice(this); return false;"><span style="font-size: x-small; color: #888;">EDIT</span></a></div><div class="price_input" style="display:none;"><input type="number" name="item_price_input" value="<?= $row['item_price']; ?>" class="form-control" onfocusout="updatePrice(this);" step="0.01"></div></div>
								                    <!-- <div class="visible-xs-block col-xs-3"><b>Quantity: </b></div><div class="col-sm-2"><?= $row['quantity']; ?></div> -->
								                    <div class="pull-right col-sm-1">
								                    <a href="javascript:void(0);" onclick="deleteRow(this, 'row_'); return false;" id="delete_<?= $row['id']; ?>"><img src="<?= WEBSITE_URL; ?>/img/remove.png" height="20" /></a><img src="<?= WEBSITE_URL ?>/img/icons/drag_handle.png" class="inline-img sortable_handle gap-left no-toggle" title="Drag"></div>
								                </div><?php
								            } ?>
								        </div>
							        <?php }
						        } ?>
						    </div>
					    <div class="row set-row-height">
				            <div class="col-sm-12"><a href="javascript:void(0);" class="iframe_open" data-category="<?= $item_from ?>" data-title="Select items from <?= $item_from ?>" data-contact-category="<?= $contact_category ?>"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" height="20" class="pull-right gap-right"></a></div>
					    </div>
						<hr>
					<?php } ?>
					</div>
				<?php } ?>
			<?php } ?>
		</div>
		<div class="clearfix"></div>
		<div class="pull-right gap-top gap-right">
		    <a href="index.php" class="btn brand-btn">Back</a>
		    <button type="submit" id="save_template" name="save_template" value="Submit" class="btn brand-btn">Save</button>
	        <a href="#" onclick="deleteSalesOrderTemplate(); return false;"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png" class="no-toggle" title="Archive"></a>
		</div>
	</div>
</form>