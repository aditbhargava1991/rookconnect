<?php
if (isset($_POST['submit'])) {
    $cx_data = $_POST['dashboard_tab_cx'];
    $inv_setting = implode(",", $cx_data);
	$dbc->query("INSERT INTO `inventory_setting` (`value`) SELECT '$inv_setting' FROM (SELECT COUNT(*) `rows` FROM `inventory_setting` WHERE `inventorysettingid`=1) `num` WHERE `num`.`rows`=0");
    $query_insert_setting = "UPDATE `inventory_setting` SET `value`='$inv_setting' WHERE `inventorysettingid`=1";
    $result_insert_setting = mysqli_query($dbc, $query_insert_setting);
}
?>

<div class="gap-top">
    <div class="form-group">
        <div class="col-sm-12"><?php
            $inventory_setting = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `value` FROM `inventory_setting` WHERE `inventorysettingid` = 1"));
            $set_check_value = $inventory_setting['value']; ?>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",summary") !== false ? 'checked' : '' ?> value="summary" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Summary</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",warehouse,") !== false ? 'checked' : '' ?> value="warehouse" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Warehousing</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",purchaseorders,") !== false ? 'checked' : '' ?> value="purchaseorders" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Purchase Orders</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",customerorders,") !== false ? 'checked' : '' ?> value="customerorders" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Customer Orders</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",pallet,") !== false ? 'checked' : '' ?> value="pallet" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Pallet #</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",pick_lists,") !== false ? 'checked' : '' ?> value="pick_lists" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Pick Lists</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",no_cost,") !== false ? 'checked' : '' ?> value="no_cost" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;<?= INVENTORY_NOUN ?> Without a Cost</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",rs,") !== false ? 'checked' : '' ?> value="rs" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Recieve Shipment</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",bom,") !== false ? 'checked' : '' ?> value="bom" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Bill of Material</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",bomc,") !== false ? 'checked' : '' ?> value="bomc" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Bill of Material (Consumables)</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",writeoff,") !== false ? 'checked' : '' ?> value="writeoff" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Waste / Write-Off</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",checklists,") !== false ? 'checked' : '' ?> value="checklists" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Checklists</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",orderlists,") !== false ? 'checked' : '' ?> value="orderlists" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Order Lists</label>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",checklist_orders,") !== false ? 'checked' : '' ?> value="checklist_orders" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Order Checklists</label>
        </div>
        <div class="col-sm-12">
			<h3>Summary Options</h3>
			<label class="form-checkbox"><input type="checkbox" <?= strpos(','.$set_check_value.',', ",summary category,") !== false ? 'checked' : '' ?> value="summary category" name="dashboard_tab_cx[]" style="height: 20px; width: 20px;" >&nbsp;&nbsp;Summary of Tabs</label>
        </div>
    </div>
</div>