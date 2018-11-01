<div class="pull-right settings-block">
	<div class="hide-titles-mob">
		<?php if(config_visible_function($dbc, 'inventory') == 1) { ?>
			<a href="field_config.php?type=tab" class="mobile-block pull-right "><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
			<span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="pdf_styling.php"><button type="button" class="btn brand-btn mobile-block gap-bottom mobile-100-pull-right pull-right">PDF Styling</button></a>
			<a href="templates.php"><button type="button" class="btn brand-btn mobile-block gap-bottom mobile-100-pull-right pull-right">Templates</button></a><?php
			$digi_count_or_not ='';
			$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_digi_count'"));
			if($get_config['configid'] > 0) {
				$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_digi_count'"));
				if($get_config['value'] == '1') {
					$digi_count_or_not = 'true';
				}
			}
			$impexp_or_not ='';
			$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_impexp_inv'"));
			if($get_config['configid'] > 0) {
				$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_impexp_inv'"));
				if($get_config['value'] == '1') {
					$impexp_or_not = 'true';
				}
			}
		}
		if(vuaed_visible_function($dbc, 'inventory') == 1) {
			echo '<a href="add_inventory.php?category='.$category.$order_list.'"><button type="button"  class="btn brand-btn mobile-block gap-bottom mobile-100-pull-right pull-right" style="margin-bottom:10px !important;">Add '.INVENTORY_NOUN.'</button></a>';
			if($impexp_or_not == 'true') {
				echo '<a  href="add_inventory_multiple.php?category='.$category.'"><button type="button"   class="btn brand-btn mobile-block gap-bottom mobile-100-pull-right  pull-right" style="margin-bottom:10px !important;">Import/Export</button></a>';
			}
			if($digi_count_or_not == 'true') {
				echo '<a  href="digital_inventory_count.php"><button type="button"  class="btn brand-btn mobile-block gap-bottom pull-right mobile-100-pull-right " style="margin-bottom:10px !important;">Digital Count</button></a>';
			}
		} ?>
	</div>
	<div class="show-on-mob">
		<?php if(vuaed_visible_function($dbc, 'inventory') == 1) {
			echo '<a href="add_inventory.php?category='.$category.$order_list.'"><img src="'.WEBSITE_URL.'/img/icons/ROOK-add-icon.png" class="add-icon-lg"></a>';
		}
		if(config_visible_function($dbc, 'inventory') == 1) { ?>
			<a href="field_config.php?type=tab" class="mobile-block pull-right gap-left"><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
		<?php } ?>
	</div>
</div>
<div style="overflow-x: auto;">
	<h1 class="gap-left"><a href="inventory.php" class="default-color"><?= INVENTORY_TILE ?></a></h1>
</div>

<div class="clearfix"></div>
<div class="notice gap-bottom gap-top popover-examples">
    <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
    <div class="col-sm-11"><span class="notice-name">NOTE:</span>
    This tile monitors all physical inventory for resale, displaying the main overview of your inventory. These items are managed by the software with auto Min Bin reminders for when each inventory item reaches a designated level.</div>
    <div class="clearfix"></div>
</div>