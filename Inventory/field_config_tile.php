<?php
$inventory_tile = INVENTORY_TILE;
$inventory_noun = INVENTORY_NOUN;
if (isset($_POST['submit'])) {
    set_config($dbc, 'inventory_tile_name', filter_var($_POST['inventory_tile'].'#*#'.$_POST['inventory_noun'],FILTER_SANITIZE_STRING));
    set_config($dbc, 'inventory_default', filter_var($_POST['inventory_default'],FILTER_SANITIZE_STRING));
    set_config($dbc, 'inventory_cost', filter_var($_POST['inventory_cost'],FILTER_SANITIZE_STRING));
    set_config($dbc, 'inventory_sort', filter_var($_POST['inventory_sort'],FILTER_SANITIZE_STRING));
	$inventory_tile = $_POST['inventory_tile'];
	$inventory_noun = $_POST['inventory_noun'];
}
?>

<div class="gap-top">
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label">Tile Name:<br /><em>Enter the name you would like the Inventory tile to be labelled as.</em></label>
        <div class="col-sm-8">
			<input name="inventory_tile" type="text" value="<?= $inventory_tile ?>" class="form-control"/>
        </div>
    </div>
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label">Tile Noun:<br /><em>Enter the name you would like individual Inventory to be labelled as.</em></label>
        <div class="col-sm-8">
			<input name="inventory_noun" type="text" value="<?= $inventory_noun ?>" class="form-control"/>
        </div>
    </div>
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label">Default Tab:<br /><em>Inventory without a Tab will be set to this tab.</em></label>
        <div class="col-sm-8">
			<select name="inventory_default" class="chosen-select-deselect"><option></option>
				<?php $inventory_default = get_config($dbc, 'inventory_default');
				foreach(explode('#*#', get_config($dbc, 'inventory_tabs')) as $inventory_tab) { ?>
					<option <?= $inventory_tab == $inventory_default ? 'selected' : '' ?> value="<?= $inventory_tab ?>"><?= $inventory_tab ?></option>
				<?php } ?>
			</select>
        </div>
    </div>
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label">Designated Cost Field:<br /><em>The field that will be used for reporting as the Inventory Cost.</em></label>
        <div class="col-sm-8">
			<select name="inventory_cost" class="chosen-select-deselect"><option></option>
				<?php $inventory_cost = get_config($dbc, 'inventory_cost');
				foreach(['Cost'=>'cost','Average Cost'=>'average_cost','CDN Cost Per Unit'=>'cdn_cpu','USD Cost Per Unit'=>'usd_cpu','Drum Unit Cost'=>'drum_unit_cost','Tote Unit Cost'=>'tote_unit_cost','Purchase Cost'=>'purchase_cost','Unit Cost'=>'unit_cost'] as $label => $cost_field) { ?>
					<option <?= $cost_field == $inventory_cost ? 'selected' : '' ?> value="<?= $cost_field ?>"><?= $label ?></option>
				<?php } ?>
			</select>
        </div>
    </div>
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label">Inventory Sort Order:<br /><em>The field that will be used for sorting the Inventory.</em></label>
        <div class="col-sm-8">
			<select name="inventory_sort" class="chosen-select-deselect"><option></option>
				<?php $inventory_sort = get_config($dbc, 'inventory_sort');
				foreach(['Name'=>'default','Purchase Order'=>'po_line'] as $label => $sort_option) { ?>
					<option <?= $sort_option == $inventory_sort ? 'selected' : '' ?> value="<?= $sort_option ?>"><?= $label ?></option>
				<?php } ?>
			</select>
        </div>
    </div>

    <div class="clearfix"></div>
</div>