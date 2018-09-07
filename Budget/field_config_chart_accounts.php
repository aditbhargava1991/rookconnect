<?php /* Field Configuration for Budget */
include_once ('../include.php');
checkAuthorised('budget');
$_GET['type'] = (empty($_GET['type']) ? $tab_config[0] : $_GET['type']);
$include = explode('#*#', get_config($dbc, 'chart_accts_'.$_GET['type']));
$disable = explode('#*#', get_config($dbc, 'chart_accts_expense').'#*#'.get_config($dbc, 'chart_accts_'.($_GET['type'] == 'assets' ? 'liabilities' : 'assets'))); ?>
<script>
$(document).ready(function() {
	$('[name=chart_items]:not([disabled])').change(function() {
		var chart_items = [];
		$('[name=chart_items]:checked').each(function() {
			chart_items.push(this.value);
		});
		$.post('budget_ajax_all.php?action=general_config', {
			name: 'chart_accts_<?= $_GET['type'] ?>',
			value: chart_items.join('#*#')
		});
	});
});
</script>

<h3>Products</h3>
	<label class="form-checkbox"><input type="checkbox" <?= in_array("all_products",$include) ? "checked" : (in_array("all_products", $disable) || in_array_starts('product_',$include) || in_array_starts('product_',$disable) ? 'disabled' : '') ?> value="all_products" onclick="if(this.checked) { $('[value^=product_]').prop('disabled',true); } else { $('[value^=product_]').removeAttr('disabled'); }" name="chart_items">All Products</label>
	<?php $product_cats = $dbc->query("SELECT `category` FROM `products` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `category`");
	while($cat = $product_cats->fetch_assoc()) { ?>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("product_".$cat['category'],$include) ? "checked" : (in_array("product_".$cat['category'], $disable) || in_array("all_products", $disable) || in_array("all_products", $include) ? 'disabled' : '') ?> value="product_<?= $cat['category'] ?>" name="chart_items"><?= $cat['category'] ?></label>
	<?php } ?>
<h3>Materials</h3>
	<label class="form-checkbox"><input type="checkbox" <?= in_array("all_materials",$include) ? "checked" : (in_array("all_materials", $disable) || in_array_starts('material_',$include) || in_array_starts('material_',$disable) ? 'disabled' : '') ?> value="all_materials" onclick="if(this.checked) { $('[value^=material_]').prop('disabled',true); } else { $('[value^=material_]').removeAttr('disabled'); }" name="chart_items">All Materials</label>
	<?php $material_cats = $dbc->query("SELECT `category` FROM `material` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `category`");
	while($cat = $material_cats->fetch_assoc()) { ?>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("material_".$cat['category'],$include) ? "checked" : (in_array("material_".$cat['category'], $disable) || in_array("all_materials", $disable) || in_array("all_materials", $include) ? 'disabled' : '') ?> value="material_<?= $cat['category'] ?>" name="chart_items"><?= $cat['category'] ?></label>
	<?php } ?>
<h3><?= INVENTORY_TILE ?></h3>
	<label class="form-checkbox"><input type="checkbox" <?= in_array("all_invs",$include) ? "checked" : (in_array("all_invs", $disable) || in_array_starts('inventory_',$include) || in_array_starts('inventory_',$disable) ? 'disabled' : '') ?> value="all_invs" onclick="if(this.checked) { $('[value^=inventory_]').prop('disabled',true); } else { $('[value^=inventory_]').removeAttr('disabled'); }" name="chart_items">All <?= INVENTORY_TILE ?></label>
	<?php foreach(explode('#*#', get_config($dbc, 'inventory_tabs')) as $cat) { ?>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("inventory_".$cat,$include) ? "checked" : (in_array("inventory_".$cat, $disable) || in_array("all_invs", $disable) || in_array("all_invs", $include) ? 'disabled' : '') ?> value="inventory_<?= $cat ?>" name="chart_items"><?= $cat ?></label>
	<?php } ?>
<h3>Assets</h3>
	<label class="form-checkbox"><input type="checkbox" <?= in_array("all_assets",$include) ? "checked" : (in_array("all_assets", $disable) || in_array_starts('asset_',$include) || in_array_starts('asset_',$disable) ? 'disabled' : '') ?> value="all_assets" onclick="if(this.checked) { $('[value^=asset_]').prop('disabled',true); } else { $('[value^=asset_]').removeAttr('disabled'); }" name="chart_items">All Assets</label>
	<?php foreach(explode(',', get_config($dbc, 'asset_tabs')) as $cat) { ?>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("asset_".$cat,$include) ? "checked" : (in_array("asset_".$cat, $disable) || in_array("all_assets", $disable) || in_array("all_assets", $include) ? 'disabled' : '') ?> value="asset_<?= $cat ?>" name="chart_items"><?= $cat ?></label>
	<?php } ?>
<h3>Equipment</h3>
	<label class="form-checkbox"><input type="checkbox" <?= in_array("all_equips",$include) ? "checked" : (in_array("all_equips", $disable) || in_array_starts('equip_',$include) || in_array_starts('equip_',$disable) ? 'disabled' : '') ?> value="all_equips" onclick="if(this.checked) { $('[value^=equip_]').prop('disabled',true); } else { $('[value^=equip_]').removeAttr('disabled'); }" name="chart_items">All Equipments</label>
	<?php foreach(explode(',', get_config($dbc, 'equipment_tabs')) as $cat) { ?>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("equip_".$cat,$include) ? "checked" : (in_array("equip_".$cat, $disable) || in_array("all_equips", $disable) || in_array("all_equips", $include) ? 'disabled' : '') ?> value="equip_<?= $cat ?>" name="chart_items"><?= $cat ?></label>
	<?php } ?>
<h3>Custom</h3>
	<label class="form-checkbox"><input type="checkbox" <?= in_array("all_customs",$include) ? "checked" : (in_array("all_customs", $disable) || in_array_starts('custom_',$include) || in_array_starts('custom_',$disable) ? 'disabled' : '') ?> value="all_customs" onclick="if(this.checked) { $('[value^=custom_]').prop('disabled',true); } else { $('[value^=custom_]').removeAttr('disabled'); }" name="chart_items">All Custom</label>
	<?php $custom_cats = $dbc->query("SELECT `category` FROM `custom` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `category`");
	while($cat = $custom_cats->fetch_assoc()) { ?>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("custom_".$cat['category'],$include) ? "checked" : (in_array("custom_".$cat['category'], $disable) || in_array("all_customs", $disable) || in_array("all_customs", $include) ? 'disabled' : '') ?> value="custom_<?= $cat['category'] ?>" name="chart_items"><?= $cat['category'] ?></label>
	<?php } ?>
<h3>Expenses</h3>
	<label class="form-checkbox"><input type="checkbox" <?= in_array("all_expenses",$include) ? "checked" : (in_array("all_expenses", $disable) || in_array_starts('expense_',$include) || in_array_starts('expense_',$disable) ? 'disabled' : '') ?> value="all_expenses" onclick="if(this.checked) { $('[value^=expense_]').prop('disabled',true); } else { $('[value^=expense_]').removeAttr('disabled'); }" name="chart_items">All Expenses</label>
	<?php $expense_cats = $dbc->query("SELECT `category`, `ec` FROM `expense_categories` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `ec`,`category`");
	while($cat = $expense_cats->fetch_assoc()) { ?>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("expense_".$cat['category'],$include) ? "checked" : (in_array("expense_".$cat['category'], $disable) || in_array("all_expenses", $disable) || in_array("all_expenses", $include) ? 'disabled' : '') ?> value="expense_<?= $cat['category'] ?>" name="chart_items"><?= (!empty($cat['ec']) ? $cat['ec'].': ' : '').$cat['category'] ?></label>
	<?php } ?>