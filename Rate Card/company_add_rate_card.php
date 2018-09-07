<?php
require_once('../include.php');
$src_card = '';
if(isset($_POST['submit'])) {
    $who_added = $_SESSION['contactid'];
    $when_added = date('Y-m-d');
    $date_of_archival = date('Y-m-d');

    $rate_card_name = filter_var($_POST['rate_card_name'],FILTER_SANITIZE_STRING);
    $ref_card = filter_var($_POST['ref_card'],FILTER_SANITIZE_STRING);
    $rate_categories = filter_var($_POST['rate_categories'],FILTER_SANITIZE_STRING);
	$result = mysqli_query($dbc, "UPDATE `company_rate_card` SET `deleted`=1 WHERE `rate_card_name`='$rate_card_name' AND `rate_categories`='$rate_categories' AND `companyrcid` NOT IN (".implode(',',$_POST['entry_id']).")");

    foreach ($_POST['entry_id'] as $key => $id) {
		$heading = isset($_POST['heading']) ? filter_var($_POST['heading'][$key],FILTER_SANITIZE_STRING) : '';
		$tile_name = isset($_POST['tile_name']) ? filter_var($_POST['tile_name'][$key],FILTER_SANITIZE_STRING) : '';
		$rate_card_types = isset($_POST['rate_card_types']) ? filter_var($_POST['rate_card_types'][$key],FILTER_SANITIZE_STRING) : '';
		$description = isset($_POST['description']) ? filter_var($_POST['description'][$key],FILTER_SANITIZE_STRING) : '';
		$item_id = isset($_POST['item_id']) ? filter_var($_POST['item_id'][$key],FILTER_SANITIZE_STRING) : '';
		if($item_id == 'Subsistence Pay') {
			$item_id = 0;
			$description = 'Subsistence Pay';
		}
		$daily = isset($_POST['daily']) ? filter_var($_POST['daily'][$key],FILTER_SANITIZE_STRING) : '';
		$hourly = isset($_POST['hourly']) ? filter_var($_POST['hourly'][$key],FILTER_SANITIZE_STRING) : '';
		$uom = isset($_POST['uom']) ? filter_var($_POST['uom'][$key],FILTER_SANITIZE_STRING) : '';
		$cost = isset($_POST['cost']) ? filter_var($_POST['cost'][$key],FILTER_SANITIZE_STRING) : '';
		$cust_price = isset($_POST['cust_price']) ? filter_var($_POST['cust_price'][$key],FILTER_SANITIZE_STRING) : '';
		$profit = isset($_POST['profit']) ? filter_var($_POST['profit'][$key],FILTER_SANITIZE_STRING) : '';
		$total = isset($_POST['total']) ? filter_var($_POST['total'][$key],FILTER_SANITIZE_STRING) : '';
		$margin = isset($_POST['total']) ? ($profit*100)/$total : 0;
		$sort_order = isset($_POST['sort_order']) ? filter_var($_POST['sort_order'][$key],FILTER_SANITIZE_STRING) : '';
		$deleted = $_POST['deleted'][$key];
		if($item_id > 0 && $cost > 0 && $tile_name == 'Services') {
			$dbc->query("UPDATE `services` SET `cost`='$cost' WHERE `serviceid`='$item_id'");
		}

		if(!empty($heading) || !empty($rate_card_types) || !empty($description) || !empty($item_id) || !empty($daily) || !empty($hourly) || !empty($uom) || !empty($cost) || !empty($cust_price) || !empty($total) || !empty($sort_order) || !empty($deleted)) {
			if(strpos($id, 'NEW_') === FALSE) {
				$query = "UPDATE `company_rate_card` SET `rate_card_name`='$rate_card_name', `ref_card`='$ref_card', `rate_categories`='$rate_categories', `tile_name`='$tile_name', `rate_card_types`='$rate_card_types', `heading`='$heading', `description`='$description', `item_id`='$item_id', `daily`='$daily', `hourly`='$hourly', `uom`='$uom', `cost`='$cost', `cust_price`='$cust_price', `profit`='$profit', `margin`='$margin', `sort_order`='$sort_order', `deleted`='$deleted' where `companyrcid`='$id'";
			} else {
				$query = "INSERT INTO `company_rate_card` (`rate_card_name`, `ref_card`, `rate_categories`, `tile_name`, `rate_card_types`, `heading`, `description`, `item_id`, `daily`, `hourly`, `uom`, `cost`, `cust_price`, `profit`, `margin`, `sort_order`, `deleted`) VALUES ('$rate_card_name', '$ref_card', '$rate_categories', '$tile_name', '$rate_card_types', '$heading' , '$description', '$item_id', '$daily', '$hourly', '$uom', '$cost', '$cust_price', '$profit', '$margin', '$sort_order', '$deleted')";
			}
			$result = mysqli_query($dbc, $query);

			if(strpos($id, 'NEW_') !== FALSE) {
				$rc_id = mysqli_insert_id($dbc);
			} else {
				$rc_id = $id;
			}
			    $date_of_archival = date('Y-m-d');

			$breakdown_result = mysqli_query($dbc, "UPDATE `rate_card_breakdown` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `rate_card_type`='".$_GET['card']."' AND `rate_card_id`='$rc_id' AND `rcbid` NOT IN (".implode(',',$_POST['bd_'.$id]).")");
			foreach($_POST['bd_'.$id] as $row => $bd_id) {
				$description = filter_var($_POST['bd_description_'.$id][$row],FILTER_SANITIZE_STRING);
				$quantity = filter_var($_POST['bd_quantity_'.$id][$row],FILTER_SANITIZE_STRING);
				$uom = filter_var($_POST['bd_uom_'.$id][$row],FILTER_SANITIZE_STRING);
				$cost = filter_var($_POST['bd_cost_'.$id][$row],FILTER_SANITIZE_STRING);
				$total = filter_var($_POST['bd_total_'.$id][$row],FILTER_SANITIZE_STRING);

				if($bd_id > 0) {
          $before_change = capture_before_change($dbc, 'rate_card_breakdown', 'description', 'rcbid', $bd_id);
          $before_change .= capture_before_change($dbc, 'rate_card_breakdown', 'quantity', 'rcbid', $bd_id);
          $before_change .= capture_before_change($dbc, 'rate_card_breakdown', 'uom', 'rcbid', $bd_id);
          $before_change .= capture_before_change($dbc, 'rate_card_breakdown', 'cost', 'rcbid', $bd_id);
          $before_change .= capture_before_change($dbc, 'rate_card_breakdown', 'total', 'rcbid', $bd_id);
					$query = "UPDATE `rate_card_breakdown` SET `description`='$description', `quantity`='$quantity', `uom`='$uom', `cost`='$cost', `total`='$total' WHERE `rcbid`='$bd_id'";
          $history = capture_after_change('description', $description);
          $history .= capture_after_change('quantity', $quantity);
          $history .= capture_after_change('uom', $uom);
          $history .= capture_after_change('cost', $cost);
          $history .= capture_after_change('total', $total);
  				add_update_history($dbc, 'ratecard_history', $history, '', $before_change);
				} else {
					$query = "INSERT INTO `rate_card_breakdown` (`rate_card_type`, `rate_card_id`, `description`, `quantity`, `uom`, `cost`, `total`) VALUES ('".$_GET['card']."', '$rc_id', '$description', '$quantity', '$uom', '$cost', '$total')";
          $before_change = '';
          $history = "Rate card entry is been added. <br />";
  				add_update_history($dbc, 'ratecard_history', $history, '', $before_change);
				}
				$result = mysqli_query($dbc, $query);

			}

      $before_change = '';
      $history = "Rate card Breakdown is been updated. <br />";
      add_update_history($dbc, 'ratecard_history', $history, '', $before_change);
			$_GET['id'] = $rc_id;
		}
    }
	$start_date = filter_var($_POST['start_date'],FILTER_SANITIZE_STRING);
	$end_date = filter_var($_POST['end_date'],FILTER_SANITIZE_STRING);
	$alert_date = filter_var($_POST['alert_date'],FILTER_SANITIZE_STRING);
	$alert_staff = filter_var(implode(',',$_POST['alert_staff']),FILTER_SANITIZE_STRING);
	mysqli_query($dbc,"UPDATE `company_rate_card` SET `start_date` = '$start_date', `end_date` = '$end_date', `alert_date` = '$alert_date', `alert_staff` = '$alert_staff', `ref_card`='$ref_card' WHERE `rate_card_name` = '$rate_card_name'");
	if($_POST['submit'] != 'ref_card') {
		echo '<script type="text/javascript"> window.location.replace("?card='.$_GET['card'].'&type='.$_GET['card'].'"); </script>';
	}
}

if($_GET['id'] > 0) {
	$rate_info = "SELECT `rate_tiles`.`rate_card_name`, `rate_tiles`.`ref_card`, `rate_tiles`.`start_date`, `rate_tiles`.`end_date`, `rate_tiles`.`alert_date`, `rate_tiles`.`alert_staff` FROM `company_rate_card` `rate_tiles` LEFT JOIN `company_rate_card` `rate_name` ON `rate_tiles`.`rate_card_name`=`rate_name`.`rate_card_name` WHERE (`rate_name`.`companyrcid`='{$_GET['id']}') AND `rate_tiles`.`deleted`=0 GROUP BY `rate_tiles`.`rate_card_name`, `rate_tiles`.`tile_name` ORDER BY `rate_tiles`.`tile_name`";
	$rate_info = $dbc->query($rate_info);
	$row = $rate_info->fetch_assoc();
	$rate_id = $_GET['id'];
	$rate_name = $row['rate_card_name'];
	$ref_card = $row['ref_card'];
	$start_date = $row['start_date'];
	$end_date = $row['end_date'];
	$alert_date = $row['alert_date'];
	$alert_staff = $row['alert_staff'];
} else {
	$dbc->query("INSERT INTO `company_rate_card` (`rate_card_name`) VALUES ('New Rate Card')");
	$rate_id = $dbc->insert_id;
	$rate_name = "New Rate Card";
}
$company_sections = explode(',',get_config($dbc,'company_rate_card_sections'));
$tile_list = [];
if(in_array('tasks',$company_sections) && tile_enabled($dbc, 'tasks')['user_enabled'] || tile_enabled($dbc,'tickets')['user_enabled']) {
	$tile_list[] = 'Tasks';
}
if(in_array('material',$company_sections) && tile_enabled($dbc, 'material')['user_enabled']) {
	$tile_list[] = 'Material';
	$material_categories = [];
	$material_cat_list = $dbc->query("SELECT `category` FROM `material` GROUP BY `category` ORDER BY `category`");
	while($material = $material_cat_list->fetch_assoc()) {
		$material_categories[] = $material['category'];
	}
}
if(in_array('services',$company_sections) && tile_enabled($dbc, 'services')['user_enabled']) {
	$tile_list[] = 'Services';
	$service_categories = [];
	$service_cat_list = $dbc->query("SELECT `category` FROM `services` GROUP BY `category` ORDER BY `category`");
	while($service = $service_cat_list->fetch_assoc()) {
		$service_categories[] = $service['category'];
	}
}
if(in_array('products',$company_sections) && tile_enabled($dbc, 'products')['user_enabled']) {
	$tile_list[] = 'Products';
	$product_categories = [];
	$product_cat_list = $dbc->query("SELECT `product_type` FROM `products` GROUP BY `category` ORDER BY `product_type`");
	while($product = $product_cat_list->fetch_assoc()) {
		$product_categories[] = $vpl['product_type'];
	}
}
if(in_array('staff',$company_sections)) {
	$tile_list[] = 'Staff';
}
if(in_array('position',$company_sections)) {
	$tile_list[] = 'Position';
}
if(in_array('contractor',$company_sections)) {
	$tile_list[] = 'Contractor';
}
if(in_array('clients',$company_sections)) {
	$tile_list[] = 'Clients';
}
if(in_array('customer',$company_sections)) {
	$tile_list[] = 'Customer';
}
if(in_array('vpl',$company_sections) && tile_enabled($dbc, 'vpl')['user_enabled']) {
	$tile_list[] = 'Vendor Pricelist';
	$vpl_categories = [];
	$vpl_cat_list = $dbc->query("SELECT `category` FROM `vendor_price_list` GROUP BY `category` ORDER BY `category`");
	while($vpl = $vpl_cat_list->fetch_assoc()) {
		$vpl_categories[] = $vpl['category'];
	}
}
if(in_array('inventory',$company_sections) && tile_enabled($dbc, 'inventory')['user_enabled']) {
	$tile_list[] = 'Inventory';
	$inv_categories = explode('#*#',get_config($dbc, 'inventory_tabs'));
}
if(in_array('equipment',$company_sections) && tile_enabled($dbc, 'equipment')['user_enabled']) {
	$tile_list[] = 'Equipment';
	$equip_categories = explode(',',get_config($dbc, 'equipment_tabs'));
}
if(in_array('labour',$company_sections) && tile_enabled($dbc, 'labour')['user_enabled']) {
	$tile_list[] = 'Labour';
	$labour_categories = [];
	$labour_cat_list = $dbc->query("SELECT `labour_type` FROM `labour` GROUP BY `labour_type` ORDER BY `labour_type`");
	while($labour = $labour_cat_list->fetch_assoc()) {
		$labour_categories[] = $labour['labour_type'];
	}
}
if(in_array('timesheets',$company_sections) && tile_enabled($dbc, 'timesheet')['user_enabled']) {
	$tile_list[] = 'Time Sheet';
}
if(in_array('driving_log',$company_sections) && tile_enabled($dbc, 'driving_log')['user_enabled']) {
	$tile_list[] = 'Mileage';
}
if(in_array('expense',$company_sections) && tile_enabled($dbc, 'expense')['user_enabled']) {
	$tile_list[] = 'Expenses';
}
if(in_array('other',$company_sections)) {
	$tile_list[] = 'Other';
}
foreach(array_unique(array_merge(explode('#*#',mysqli_fetch_array(mysqli_query($dbc,"SELECT `custom_accordions` FROM `field_config_estimate`"))['custom_accordions']),explode('#*#',mysqli_fetch_array(mysqli_query($dbc,"SELECT `custom_accordions` FROM `field_config_cost_estimate`"))['custom_accordions']))) as $accordion) {
	if($accordion != '') {
		$tile_list[] = $accordion;
	}
}
$card_tab = filter_var($_GET['card'],FILTER_SANITIZE_STRING);
$field_config = get_config($dbc,$card_tab.'_rate_fields');
$field_order = get_config($dbc, 'estimate_field_order');
if($field_order == '') {
	$field_order = 'Type#*#Heading#*#Description#*#Item Type#*#Daily#*#Hourly#*#Customer Price#*#Dollarsaving#*#Percentsaving#*#UOM#*#Quantity#*#Cost#*#Margin#*#Profit#*#Estimate Price#*#Total';
}
$field_order = explode('#*#',$field_order);
foreach(explode('#*#','Type#*#Heading#*#Description#*#Item Type#*#Daily#*#Hourly#*#Customer Price#*#Dollarsaving#*#Percentsaving#*#UOM#*#Quantity#*#Cost#*#Margin#*#Profit#*#Estimate Price#*#Total') as $field) {
	if(!in_array_starts($field, $field_order)) {
		$field_order[] = $field;
	}
}
if(str_replace(',','',$field_config) == '') {
	$field_config = ',tile,type,heading,description,uom,cost,estimate,customer,quantity,total,profit,margin,';
}
?>
<script>
var rate_name = "";
var added_id = 0;
$(document).ready(function() {
	setFunctions();
	// get_types_tiles();
	highlightSync();
	$('[name^=tile]').each(function() {
		changeTile(this);
	});
});
function setFunctions() {
	destroyInputs();
	$('.add_rate').off('click').on( 'click', function () {
		var block = $(this).closest('.tile_group');
		var clone = block.find('tr:visible.additional_positionprod').last().clone();
        clone.find('.form-control').val('');
		clone.find('[name^=deleted]').val(0);
		clone.find('[name^=entry_id]').val("NEW_"+added_id++);
		clone.find('[name^=itemtype]').empty().append("<option></option><option value='Subsistence Pay'>Subsistence Pay</option>");

        clone.find('.form-control').trigger("change.select2");
		clone.css('display','');
        block.find('tr.additional_positionprod').last().after(clone);
		changeTile(block.find('tr.additional_positionprod').last().find('[name^=tile]').get(0));
		block.find('tr.additional_positionprod').last().find('input[type=text],input[type=number]').first().focus();
		setFunctions();
        return false;
    });
	$('[name^=quantity]').each(function() {
		changePriceTotal(this);
	});
	$('[name=ref_card]').change(function() {
		if(this.value != $(this).data('value')) {
			$('button[name=submit]').val('ref_card');
			$('button[name=submit]').click()
		}
	});
	$('table').sortable({
		handle: '.handle',
		items: 'tr:not(:first)',
		stop: function () {
			var i = 0;
			$('[name="sort_order[]"]').each(function() {
				$(this).val(i++);
			});
		}
	});
	initInputs();
}
function calcBreakdowns() {
	$('[name^=bd_cost]').each(function() {
		var row = $(this).closest('tr');
		var cost = this.value;
		var qty = row.find('[name^=bd_quantity]').val();
		if(cost > 0 && qty > 0) {
			row.find('[name^=bd_total]').val(qty * cost);
		}
	});
	var totals = [];
	$('[name^=bd_total]').each(function() {
		if(isNaN(totals[$(this).data('id')])) {
			totals[$(this).data('id')] = 0;
		}
		totals[$(this).data('id')] += +this.value;
	});

	for(var id in totals) {
		if(typeof(totals[id]) !== 'function' && totals[id] != 0) {
			$('[name="entry_id[]"][value='+id+']').closest('tr').find('[name="cost[]"]').val(totals[id]);
		}
	}
}

function syncDetails(input) {
	if(input.name.substr(0,14) == 'bd_description') {
		var detail = input.value;
		var cost = 0;
		$('[name^=bd_description]').each(function() {
			if(this.value == detail && this.name != input.name) {
				cost = $(this).closest('tr').find('[name^=bd_cost]').val();
			}
		});
		$(input).closest('tr').find('[name^=bd_cost]').val(cost);
	} else {
		var cost = input.value;
		var detail = $(input).closest('tr').find('[name^=bd_description]').val();
		$('[name^=bd_description]').each(function() {
			if(this.value == detail) {
				$(this).closest('tr').find('[name^=bd_cost]').val(cost);
			}
		});
	}

	highlightSync();
	calcBreakdowns();
}

function highlightSync() {
	$('[name^=bd_description]').each(function() {
		var descript = this.value;
		if($('[name^=bd_description]').filter(function() { return this.value == descript; }).length > 1) {
			$(this).css('border-color','green').css('border-width','2');
			$(this).closest('tr').find('[name^=bd_cost]').css('border-color','green').css('border-width','2');
		}
	});
}

function addBreakdownRow(button) {
	var row = $(button).closest('tr');
	var id = row.find('[name="entry_id[]"]').val();
	var details = row.next('tr');
	details.find('td').show();
	details.find('.panel').show();
	if(details.find('.collapse.in').length == 0) {
		details.find('div a[data-toggle=collapse]').click();
	}

	var breakdown = '<tr><input type="hidden" name="bd_'+id+'[]" value="">';
	breakdown += '<td data-title="Description"><input type="text" name="bd_description_'+id+'[]" value="" onchange="syncDetails(this);" placeholder="Description" class="form-control"></td>';
	breakdown += '<td data-title="Quantity"><input type="number" onchange="calcBreakdowns();" name="bd_quantity_'+id+'[]" value="" placeholder="Quantity" class="form-control" min="0" step="any"></td>';
	breakdown += '<td data-title="UoM"><input type="text" name="bd_uom_'+id+'[]" value="" placeholder="UoM" class="form-control"></td>';
	breakdown += '<td data-title="Unit Cost"><input type="number" onchange="syncDetails(this);" name="bd_cost_'+id+'[]" value="" placeholder="Unit Cost" class="form-control" min="0" step="any"></td>';
	breakdown += '<td data-title="Subtotal"><input type="number" onchange="calcBreakdowns();" name="bd_total_'+id+'[]" data-id="'+id+'" value="" placeholder="Subtotal" class="form-control" min="0" step="any"></td>';
	breakdown += '<td style="text-align:center;"><a href="" onclick="removeDetail(); return false;"><img src="../img/remove.png" class="inline-img"></a></td>';
	breakdown += '</tr>';
	details.find('table').append(breakdown);
	calcBreakdowns();
}

function changeTile(sel) {
	var tile = sel.value;
    var cat = $(sel).closest('.form-group').find('[name=category]').first().val();
	if(tile == '') {
		return;
	}
	$.ajax({
		type: "GET",
		url: "ratecard_ajax_all.php?fill=rate_card_desc&type="+tile+"&cat="+cat,
		dataType: "html",   //expect html to be returned
		success: function(response){
			if(response == '') {
				return;
			}
            var result = response.split('*#*');
			var current = $(sel).parents('tr').find('[name^=descript]');
			var value = current.find('option:selected').val();
			current.empty();
			selected = false;
			current.append("<option/>");
            result.forEach(function(option) {
				var info = option.split('|');
				var id = info[1];
				var descript = info[0];
				var select = "";
				if(id == value) {
					select = " selected";
					selected = true;
				}
				current.append("<option"+select+" value='"+id+"'>"+descript+"</option>");
			});
			select = '';
			if(value == 'Subsistence Pay') {
				select = 'selected';
				selected = true;
			}
			if(tile == 'Position') {
				current.append("<option "+select+" value='Subsistence Pay'>Subsistence Pay</option>");
			}
			if(!selected && value != undefined) {
				current.append("<option selected value='"+value+"'>"+value+"</option>");
			}
			current.trigger("change.select2");
		}
	});
}

function changePriceTotal(sel){
	var row = sel.closest('tr');
	var arr = sel.id.split('_');
	var estimatePrice= $(row).find('[name^=estimateprice]').val();
	var cost= $(row).find('[name^=cost]').val();
	var quantity= $(row).find('[name^=quantity]').val();
	if(quantity=='' && quantity==0){ quantity=1; }
	var total=quantity*estimatePrice;
	$(row).find('[name^=total]').val(total);

	var profit=total-(cost*quantity);
	$(row).find('[name^=profit]').val(profit);

	var margin = +((profit*100)/total).toFixed(2) || 0;
	$(row).find('[name^=margin]').val(margin);

	var saving_field = $(row).find('[name^=dollarsaving],[name^=percentsaving]').first();
	var main_rate = saving_field.data('main-rate');
	if(main_rate > 0) {
		var main_field = saving_field.data('rate-field');
		var rate = $(row).find('[name^='+main_field+']').val();
		var dollars = +(main_rate - rate).toFixed(2) || 0;
		var percent = +(100 - (rate / main_rate * 100)).toFixed(2) || 0;
		if(dollars != 0) {
			$(row).find('[name^=dollarsaving]').val(percent);
			$(row).find('[name^=percentsaving]').val(percent);
		}
	}
}

function remove_row(span) {
	$(span).parents('tr').hide();
	$(span).parents('tr').children('[name^=deleted]').val(1);
}

function removeDetail(span) {
	var panel = $(span).closest('.panel-body');
	$(span).closest('tr').remove();

	if(panel.find('td').length == 0) {
		var details = panel.closest('tr');
		details.find('td').hide();
		details.find('.panel').hide();
	}

	calcBreakdowns();
}

function get_types_tiles() {
	var selectedTile = $('[name=filter_tile]').val();
	var tiles = new Array();
	$('[name="tile_name[]"]').each(function() {
		var tile = $(this).find('option:selected').text();
		if(tiles.find(function(option) {
			return option == tile;
		}) == undefined) {
			tiles.push(tile);
		}
	});
	tiles.sort();
	$('[name=filter_tile]').empty();
	$('[name=filter_tile]').append("<option value=''>Display All</option>");
	for(var tile in tiles) {
		var selected = '';
		if(tiles[tile] == selectedTile) {
			selected = ' selected';
		}
		$('[name=filter_tile]').append("<option"+selected+" value='"+tiles[tile]+"'>"+tiles[tile]+"</option>");
	}
	$('[name=filter_tile]').trigger('change.select2');

	var selectedType = $('[name=filter_type]').val();
	var types = new Array();
	$('[name="rate_card_types[]"]').each(function() {
		var type = $(this).find('option:selected').text();
		if(types.find(function(option) {
			return option == type;
		}) == undefined) {
			types.push(type);
		}
	});
	types.sort();
	$('[name=filter_type]').empty();
	$('[name=filter_type]').append("<option value=''>Display All</option>");
	for(var type in types) {
		var selected = '';
		if(types[type] == selectedType) {
			selected = ' selected';
		}
		$('[name=filter_type]').append("<option"+selected+" value='"+types[type]+"'>"+types[type]+"</option>");
	}
	$('[name=filter_type]').trigger('change.select2');
}

function addCategory(img) {
	var block = $(img).closest('.form-group');
	destroyInputs();
	var clone = block.clone();
	clone.find('.rate_details').empty();
	clone.find('input,select').not('[name=tile]').val('');
	block.after(clone);
	initInputs();
}
function loadRates(select) {
	var block = $(select).closest('.form-group');
	var target = block.find('.rate_details');
	target.empty().load('company_add_tile_rates.php?rate_id=<?= $rate_id ?>&tile='+encodeURI(block.find('[name=tile]').val())+'&category='+encodeURI(block.find('[name=category]').val())+'&type='+encodeURI(block.find('[name=type]').val()),function() { setFunctions(); });
}
</script>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php if(strpos($field_config,',breakdown,') !== FALSE) { ?>
		<div class="notice double-gap-bottom popover-examples">
			<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
			<div class="col-sm-11"><span class="notice-name">NOTE:</span>
			When entering breakdown details, the cost for the detail will be synchronized for all details within the rate card that have the same description, and multiplied by the cost, will define the total cost for the rate card item. The cost and description for synced details are shown with a green border.</div>
			<div class="clearfix"></div>
		</div>
	<?php } ?>
	<input type="hidden" name="submit" value="Submit">
	<h3>Rate Card Details</h3>

	<?php if(strpos($field_config,',category,') !== FALSE && $card_tab != 'universal') { ?>
		<div class="form-group clearfix completion_date">
			<label for="rate_categories" class="col-sm-4 control-label text-right">Rate Card Category:</label>
			<div class="col-sm-8">
				<select name="rate_categories" class="chosen-select-deselect form-control"><option></option><?php
					$rate_category_list = mysqli_query($dbc, "SELECT DISTINCT IFNULL(`rate_categories`,'') rate_categories FROM `company_rate_card` WHERE IFNULL(`rate_categories`,'') != '' ORDER BY IFNULL(`rate_categories`,'')");
					while($row = mysqli_fetch_array($rate_category_list)) {
						echo "<option ".($row['rate_categories'] == $rate_categories ? 'selected' : '')." value='".$row['rate_categories']."'>".$row['rate_categories']."</option>";
					} ?>
					<option value="NEW_CAT">New Rate Card Category</option>
				</select>
				<input name="rate_categories" value="<?php echo $rate_categories; ?>" type="text" class="form-control" style="display:none;">
			</div>
		</div>
	<?php } else if(strpos($field_config,',category,') !== FALSE && $card_tab == 'universal') { ?>
		<input type="hidden" name="rate_categories" value="<?= $_GET['category'] ?>">
	<?php } ?>
	<?php if($card_tab == 'company') { ?>
		<div class="form-group clearfix completion_date">
			<label for="first_name" class="col-sm-4 control-label text-right">Rate Card Name:</label>
			<div class="col-sm-8">
				<input name="rate_card_name" value="<?php echo $rate_name; ?>" type="text" class="form-control">
			</div>
		</div>
		<?php if(strpos($field_config, ',ref_card,') !== FALSE) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><span class="popover-examples list-inline tooltip-navigation"><a style="top:0;" class="info_i_sm" data-toggle="tooltip" data-placement="top" title="" data-original-title="This is the Rate Card that will be used as a base rate to calculate savings within the current Rate Card."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
					Primary Rate Card:</label>
				<div class="col-sm-8">
					<select name="ref_card" data-placeholder="Select a Primary Rate Card" class="chosen-select-deselect" data-value="<?= $ref_card ?>"><option />
						<?php $rate_cards = $dbc->query("SELECT `rate_card_name` FROM `company_rate_card` WHERE `deleted`=0 AND `rate_card_name` != '$rate_name' GROUP BY `rate_card_name` ORDER BY `rate_card_name`");
						while($rate_card_row = $rate_cards->fetch_assoc()) { ?>
							<option <?= $ref_card == $rate_card_row['rate_card_name'] ? 'selected' : '' ?> value="<?= $rate_card_row['rate_card_name'] ?>"><?= $rate_card_row['rate_card_name'] ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
		<?php } ?>
		<?php if(strpos($field_config, ',start_end_dates,') !== FALSE) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Start and End Dates:</label>
				<div class="col-sm-4">
					<input name="start_date" placeholder="Start Date" value="<?= $start_date != '0000-00-00' ? $start_date : '' ?>" type="text" class="form-control datepicker">
				</div>
				<div class="col-sm-4">
					<input name="end_date" placeholder="End Date" value="<?= $end_date != '0000-00-00' ? $end_date : '' ?>" type="text" class="form-control datepicker">
				</div>
			</div>
		<?php } ?>
		<?php if(strpos($field_config, ',reminder_alerts,') !== FALSE) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Alert Date:</label>
				<div class="col-sm-8">
					<input name="alert_date" value="<?php echo $alert_date; ?>" type="text" class="form-control datepicker">
				</div>
			</div>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Alert Staff:</label>
				<div class="col-sm-8">
					<select name="alert_staff[]" multiple data-placeholder="Select Staff..." class="form-control chosen-select-deselect">
						<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
						foreach($staff_list as $staffid) {
							echo '<option value="'.$staffid.'" '.(strpos(','.$alert_staff.',',','.$staffid.',') !== FALSE ? 'selected' : '').'>'.get_contact($dbc, $staffid).'</option>';
						} ?>
					</select>
				</div>
			</div>
		<?php } ?>
	<?php } ?>

	<?php foreach($tile_list as $tile_name) {
		$cat_name = '';
		$type_name = ''; ?>
		<label class="col-sm-4"><h4>Rate Card: <?= $tile_name ?></h4></label>
		<?php if($tile_name == 'Material') {
			$company_cats = $dbc->query("SELECT `material`.`category` FROM `company_rate_card` LEFT JOIN `material` ON `company_rate_card`.`description`=`material`.`name` WHERE `tile_name` LIKE 'Material' AND `item_id` > 0 AND `company_rate_card`.`deleted`=0 AND `rate_card_name`='$rate_name' GROUP BY `material`.`category`");
			$cat = $company_cats->fetch_assoc();
			do { ?>
				<div class="form-group">
					<input type="hidden" name="tile" value="<?= $tile_name ?>">
					<div class="col-sm-1 pull-right">
						<img class="inline-img no-toggle cursor-hand" onclick="addCategory(this);" src="../img/icons/ROOK-add-icon.png">
					</div>
					<div class="col-sm-5 pull-right">
						<select name="category" class="chosen-select-deselect" data-placeholder="Select Category" onchange="loadRates(this);"><option />
							<?php foreach($material_categories as $cat_name) { ?>
								<option <?= $cat['category'] == $cat_name ? 'selected' : '' ?> value="<?= $cat_name ?>"><?= $cat_name ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="col-sm-2 control-label pull-right">Material Category:</label>
					<div class="col-sm-12 rate_details">
						<?php if($cat['category'] != '') {
							$cat_name = $cat['material'];
							$type_name = '';
							include('company_add_tile_rates.php');
						} else {
							// echo '<h4>Please select a category</hr>';
						} ?>
					</div>
				</div>
			<?php } while($cat = $company_cats->fetch_assoc());
		} else if($tile_name == 'Services') {
			$company_cats = $dbc->query("SELECT `services`.`category` FROM `company_rate_card` LEFT JOIN `services` ON `company_rate_card`.`description`=`services`.`heading` OR `company_rate_card`.`item_id`=`services`.`serviceid` WHERE `tile_name` LIKE 'Services' AND `item_id` > 0 AND `company_rate_card`.`deleted`=0 AND `rate_card_name`='$rate_name' GROUP BY `services`.`category`");
			$cat = $company_cats->fetch_assoc();
			do { ?>
				<div class="form-group">
					<input type="hidden" name="tile" value="<?= $tile_name ?>">
					<div class="col-sm-1 pull-right">
						<img class="inline-img no-toggle cursor-hand" onclick="addCategory(this);" src="../img/icons/ROOK-add-icon.png">
					</div>
					<div class="col-sm-5 pull-right">
						<select name="category" class="chosen-select-deselect" data-placeholder="Select Category" onchange="loadRates(this);"><option />
							<?php foreach($service_categories as $cat_name) { ?>
								<option <?= $cat['category'] == $cat_name ? 'selected' : '' ?> value="<?= $cat_name ?>"><?= $cat_name ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="col-sm-2 control-label pull-right">Service Category:</label>
					<div class="col-sm-12 rate_details">
						<?php if($cat['category'] != '') {
							$cat_name = $cat['category'];
							$type_name = '';
							include('company_add_tile_rates.php');
						} ?>
					</div>
				</div>
			<?php } while($cat = $company_cats->fetch_assoc());
		} else if($tile_name == 'Products') {
			$company_cats = $dbc->query("SELECT `products`.`product_type` FROM `company_rate_card` LEFT JOIN `products` ON `company_rate_card`.`description`=`products`.`heading` WHERE `tile_name` LIKE 'Product' AND `item_id` > 0 AND `company_rate_card`.`deleted`=0 AND `rate_card_name`='$rate_name' GROUP BY `products`.`product_type`");
			$cat = $company_cats->fetch_assoc();
			do { ?>
				<div class="form-group">
					<input type="hidden" name="tile" value="<?= $tile_name ?>">
					<div class="col-sm-1 pull-right">
						<img class="inline-img no-toggle cursor-hand" onclick="addCategory(this);" src="../img/icons/ROOK-add-icon.png">
					</div>
					<div class="col-sm-5 pull-right">
						<select name="category" class="chosen-select-deselect" data-placeholder="Select Category" onchange="loadRates(this);"><option />
							<?php foreach($product_categories as $cat_name) { ?>
								<option <?= $cat['product_types'] == $cat_name ? 'selected' : '' ?> value="<?= $cat_name ?>"><?= $cat_name ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="col-sm-2 control-label pull-right">Product Category:</label>
					<div class="col-sm-12 rate_details">
						<?php if($cat['product_type'] != '') {
							$cat_name = $cat['product_type'];
							$type_name = '';
							include('company_add_tile_rates.php');
						} else {
							// echo '<h4>Please select a category</hr>';
						} ?>
					</div>
				</div>
			<?php } while($cat = $company_cats->fetch_assoc());
		} else if($tile_name == 'Vendor Pricelist') {
			$company_cats = $dbc->query("SELECT `vendor_price_list`.`category` FROM `company_rate_card` LEFT JOIN `vendor_price_list` ON `company_rate_card`.`description`=`vendor_price_list`.`name` WHERE `tile_name` LIKE 'VPL' AND `item_id` > 0 AND `company_rate_card`.`deleted`=0 AND `rate_card_name`='$rate_name' GROUP BY `vendor_price_list`.`category`");
			$cat = $company_cats->fetch_assoc();
			do { ?>
				<div class="form-group">
					<input type="hidden" name="tile" value="<?= $tile_name ?>">
					<div class="col-sm-1 pull-right">
						<img class="inline-img no-toggle cursor-hand" onclick="addCategory(this);" src="../img/icons/ROOK-add-icon.png">
					</div>
					<div class="col-sm-5 pull-right">
						<select name="category" class="chosen-select-deselect" data-placeholder="Select Category" onchange="loadRates(this);"><option />
							<?php foreach($vpl_categories as $cat_name) { ?>
								<option <?= $cat['category'] == $cat_name ? 'selected' : '' ?> value="<?= $cat_name ?>"><?= $cat_name ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="col-sm-2 control-label pull-right">Vendor Price List Category:</label>
					<div class="col-sm-12 rate_details">
						<?php if($cat['category'] != '') {
							$cat_name = $cat['category'];
							$type_name = '';
							include('company_add_tile_rates.php');
						} else {
							// echo '<h4>Please select a category</hr>';
						} ?>
					</div>
				</div>
			<?php } while($cat = $company_cats->fetch_assoc());
		} else if($tile_name == 'Inventory') {
			$company_cats = $dbc->query("SELECT `inventory`.`category` FROM `company_rate_card` LEFT JOIN `inventory` ON `company_rate_card`.`description`=`inventory`.`name` WHERE `tile_name` LIKE 'Inventory' AND `item_id` > 0 AND `company_rate_card`.`deleted`=0 AND `rate_card_name`='$rate_name' GROUP BY `inventory`.`category`");
			$cat = $company_cats->fetch_assoc();
			do { ?>
				<div class="form-group">
					<input type="hidden" name="tile" value="<?= $tile_name ?>">
					<div class="col-sm-1 pull-right">
						<img class="inline-img no-toggle cursor-hand" onclick="addCategory(this);" src="../img/icons/ROOK-add-icon.png">
					</div>
					<div class="col-sm-5 pull-right">
						<select name="category" class="chosen-select-deselect" data-placeholder="Select Category" onchange="loadRates(this);"><option />
							<?php foreach($inv_categories as $cat_name) { ?>
								<option <?= $cat['category'] == $cat_name ? 'selected' : '' ?> value="<?= $cat_name ?>"><?= $cat_name ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="col-sm-2 control-label pull-right">Inventory Category:</label>
					<div class="col-sm-12 rate_details">
						<?php if($cat['category'] != '') {
							$cat_name = $cat['category'];
							$type_name = '';
							include('company_add_tile_rates.php');
						} else {
							// echo '<h4>Please select a category</hr>';
						} ?>
					</div>
				</div>
			<?php } while($cat = $company_cats->fetch_assoc());
		} else if($tile_name == 'Equipment') {
			// $company_cats = $dbc->query("SELECT `equipment`.`category` FROM `company_rate_card` LEFT JOIN `equipment` ON `company_rate_card`.`item_id`=`equipment`.`equipmentid` WHERE `tile_name` LIKE 'Equipment' AND `item_id` > 0 AND `company_rate_card`.`deleted`=0 AND `rate_card_name`='$rate_name' GROUP BY `equipment`.`category` UNION SELECT 'type_rates' `category` FROM `company_rate_card` WHERE `item_id`=0 AND `description` IN (SELECT `type` FROM `equipment` WHERE `deleted`=0) AND `tile_name` LIKE 'Equipment' AND `deleted`=0 AND `rate_card_name`='$rate_name' UNION SELECT 'category_rates' `category` FROM `company_rate_card` WHERE `item_id`=0 AND `description` IN (SELECT `category` FROM `equipment` WHERE `deleted`=0) AND `tile_name` LIKE 'Equipment' AND `deleted`=0 AND `rate_card_name`='$rate_name'");
			$company_cats = $dbc->query("SELECT 'type_rates' `category` FROM `company_rate_card` WHERE `item_id`=0 AND `description` IN (SELECT `type` FROM `equipment` WHERE `deleted`=0) AND `tile_name` LIKE 'Equipment' AND `deleted`=0 AND (`rate_card_name`='$rate_name' OR `rate_card_name`='$ref_card') AND `rate_card_name` != '' UNION SELECT 'category_rates' `category` FROM `company_rate_card` WHERE `item_id`=0 AND `description` IN (SELECT `category` FROM `equipment` WHERE `deleted`=0) AND `tile_name` LIKE 'Equipment' AND `deleted`=0 AND (`rate_card_name`='$rate_name' OR `rate_card_name`='$ref_card') AND `rate_card_name` != ''");
			$cat = $company_cats->fetch_assoc();
			do { ?>
				<div class="form-group">
					<input type="hidden" name="tile" value="<?= $tile_name ?>">
					<div class="col-sm-1 pull-right">
						<img class="inline-img no-toggle cursor-hand" onclick="addCategory(this);" src="../img/icons/ROOK-add-icon.png">
					</div>
					<div class="col-sm-5 pull-right">
						<select name="category" class="chosen-select-deselect" data-placeholder="Select Category" onchange="loadRates(this);"><option />
							<?php /*foreach($equip_categories as $cat_name) { ?>
								<option <?= $cat['category'] == $cat_name ? 'selected' : '' ?> value="<?= $cat_name ?>"><?= $cat_name ?></option>
							<?php }*/ ?>
							<option <?= $cat['category'] == 'category_rates' ? 'selected' : '' ?> value="category_rates">Rates per Category</option>
							<option <?= $cat['category'] == 'type_rates' ? 'selected' : '' ?> value="type_rates">Rates per Type</option>
						</select>
					</div>
					<label class="col-sm-2 control-label pull-right">Equipment Category:</label>
					<div class="col-sm-12 rate_details">
						<?php if($cat['category'] != '') {
							$cat_name = $cat['category'];
							$type_name = '';
							include('company_add_tile_rates.php');
						} else {
							// echo '<h4>Please select a category</hr>';
						} ?>
					</div>
				</div>
			<?php } while($cat = $company_cats->fetch_assoc());
		} else if($tile_name == 'Labour') {
			$company_cats = $dbc->query("SELECT `labour`.`labour_type` FROM `company_rate_card` LEFT JOIN `labour` ON `company_rate_card`.`description`=`labour`.`heading` OR `company_rate_card`.`item_id`=`labour`.`labourid` WHERE `tile_name` LIKE 'Labour' AND `item_id` > 0 AND `company_rate_card`.`deleted`=0 AND `rate_card_name`='$rate_name' GROUP BY `labour`.`labour_type`");
			$cat = $company_cats->fetch_assoc();
			do { ?>
				<div class="form-group">
					<input type="hidden" name="tile" value="<?= $tile_name ?>">
					<div class="col-sm-1 pull-right">
						<img class="inline-img no-toggle cursor-hand" onclick="addCategory(this);" src="../img/icons/ROOK-add-icon.png">
					</div>
					<div class="col-sm-5 pull-right">
						<select name="category" class="chosen-select-deselect" data-placeholder="Select Category" onchange="loadRates(this);"><option />
							<?php foreach($labour_categories as $cat_name) { ?>
								<option <?= $cat['labour_type'] == $cat_name ? 'selected' : '' ?> value="<?= $cat_name ?>"><?= $cat_name ?></option>
							<?php } ?>
						</select>
					</div>
					<label class="col-sm-2 control-label pull-right">Labour Category:</label>
					<div class="col-sm-12 rate_details">
						<?php if($cat['labour_type'] != '') {
							$cat_name = $cat['labour_type'];
							$type_name = '';
							include('company_add_tile_rates.php');
						} else {
							// echo '<h4>Please select a category</hr>';
						} ?>
					</div>
				</div>
			<?php } while($cat = $company_cats->fetch_assoc());
		} else {
			include('company_add_tile_rates.php');
		} ?>
	<?php } ?>
	<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
	<div class="clearfix"></div>
</form>
