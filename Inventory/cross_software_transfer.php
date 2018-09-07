<?php include('../include.php');
error_reporting(0);
$inventoryid = $_GET['inventoryid'];
$inventory = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='$inventoryid'"));
if(!empty($_POST['submit'])) {
	$destination = $_POST['transfer_dest'];
	$qty = filter_var($_POST['transfer_qty'],FILTER_SANITIZE_STRING);
	if($qty > 0) {
		for($i = 1; $i <= $number_of_connections; $i++) {
			if(${'software_url_'.$i} == $destination) {
				$history = "$qty unit(s) of ".$inventory['category'].' '.$inventory['part_no'].': '.$inventory['product_name']." transferred to $destination.";
				$new_qty = (float)$inventory['quantity'] - $qty;

				//Remove Inventory from Local Software and Record the Change
				mysqli_query($dbc, "UPDATE `inventory` SET `quantity` = '$new_qty' WHERE `inventoryid`='$inventoryid'");
				insert_day_overview($dbc, $_SESSION['contactid'], 'Inventory', date('Y-m-d'), '', $history);
				mysqli_query($dbc, "INSERT INTO `inventory_change_log` (`inventoryid`, `contactid`, `old_inventory`, `old_cost`, `changed_quantity`, `current_cost`, `new_inventory`, `new_cost`, `date_time`, `location_of_change`, `change_comment`)
					VALUES ('$inventoryid', '".$_SESSION['contactid']."', '".$inventory['quantity']."','".$inventory['average_cost']."','".$qty."','".$inventory['average_cost']."','".$new_qty."','".$inventory['average_cost']."',CURRENT_TIMESTAMP, 'Cross Software Transfer', '$history')");

				//Add Inventory to Remote Software and Record the Change
				$dbc_cross = ${'dbc_cross_'.$i};
				$result = mysqli_query($dbc_cross, "SELECT * FROM `inventory` WHERE `category`='".$inventory['category']."' AND `part_no`='".$inventory['part_no']."' AND `product_name`='".$inventory['product_name']."' AND `name`='".$inventory['name']."'");
				if(mysqli_num_rows($result) == 0) {
					mysqli_query($dbc_cross, "INSERT INTO `inventory` (`code`, `category`, `sub_category`, `part_no`,	`description`, `comment`, `question`, `request`, `display_website`, `vendorid`, `size`, `weight`, `type`, `name`, `date_of_purchase`, `purchase_cost`, `sell_price`, `markup`, `freight_charge`, `min_bin`, `current_stock`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `minimum_billable`, `estimated_hours`, `actual_hours`, `msrp`, `quote_description`, `usd_invoice`, `shipping_rate`, `shipping_cash`, `exchange_rate`, `exchange_cash`, `cdn_cpu`, `cogs_total`, `location`, `inv_variance`, `average_cost`, `asset`, `revenue`, `buying_units`, `selling_units`, `stocking_units`, `preferred_price`, `web_price`, `id_number`, `operator`, `lsd`, `quantity`, `product_name`, `cost`, `usd_cpu`, `commission_price`, `markup_perc`, `current_inventory`, `write_offs`, `min_max`, `status`, `note`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`,`weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `bill_of_material`, `include_in_so`,`include_in_po`,`include_in_pos`, `drum_unit_cost`, `drum_unit_price`, `tote_unit_cost`, `tote_unit_price`, `include_in_product`, `wcb_price`, `spec_sheet`, `featured`, `main_image`)
						VALUES ('".$inventory['code']."', '".$inventory['category']."', '".$inventory['sub_category']."', '".$inventory['part_no']."', '".$inventory['description']."', '".$inventory['comment']."', '".$inventory['question']."', '".$inventory['request']."', '".$inventory['display_website']."', '".$inventory['vendorid']."', '".$inventory['size']."', '".$inventory['weight']."', '".$inventory['type']."', '".$inventory['name']."', '".$inventory['date_of_purchase']."', '".$inventory['purchase_cost']."', '".$inventory['sell_price']."', '".$inventory['markup']."', '".$inventory['freight_charge']."', '".$inventory['min_bin']."', '".$inventory['current_stock']."', '".$inventory['final_retail_price']."', '".$inventory['admin_price']."', '".$inventory['wholesale_price']."', '".$inventory['commercial_price']."', '".$inventory['client_price']."', '".$inventory['purchase_order_price']."', '".$inventory['sales_order_price']."', '".$inventory['minimum_billable']."', '".$inventory['estimated_hours']."', '".$inventory['actual_hours']."', '".$inventory['msrp']."', '".$inventory['quote_description']."', '".$inventory['usd_invoice']."', '".$inventory['shipping_rate']."', '".$inventory['shipping_cash']."', '".$inventory['exchange_rate']."', '".$inventory['exchange_cash']."', '".$inventory['cdn_cpu']."', '".$inventory['cogs_total']."', '".$inventory['location']."', '".$inventory['inv_variance']."', '".$inventory['average_cost']."', '".$inventory['asset']."', '".$inventory['revenue']."', '".$inventory['buying_units']."', '".$inventory['selling_units']."', '".$inventory['stocking_units']."', '".$inventory['preferred_price']."', '".$inventory['web_price']."', '".$inventory['id_number']."', '".$inventory['operator']."', '".$inventory['lsd']."', '0', '".$inventory['product_name']."', '".$inventory['cost']."', '".$inventory['usd_cpu']."', '".$inventory['commission_price']."', '".$inventory['markup_perc']."', '".$inventory['current_inventory']."', '".$inventory['write_offs']."', '".$inventory['min_max']."', '".$inventory['status']."', '".$inventory['note']."', '".$inventory['unit_price']."', '".$inventory['unit_cost']."', '".$inventory['rent_price']."', '".$inventory['rental_days']."', '".$inventory['rental_weeks']."', '".$inventory['rental_months']."', '".$inventory['rental_years']."', '".$inventory['reminder_alert']."', '".$inventory['daily']."', '".$inventory['weekly']."', '".$inventory['monthly']."', '".$inventory['annually']."', '".$inventory['total_days']."', '".$inventory['total_hours']."', '".$inventory['total_km']."', '".$inventory['total_miles']."', '".$inventory['bill_of_material']."', '".$inventory['include_in_so']."', '".$inventory['include_in_po']."', '".$inventory['include_in_pos']."', '".$inventory['drum_unit_cost']."', '".$inventory['drum_unit_price']."', '".$inventory['tote_unit_cost']."', '".$inventory['tote_unit_price']."', '".$inventory['include_in_product']."', '".$inventory['wcb_price']."', '".$inventory['spec_sheet']."', '".$inventory['featured']."', '".$inventory['main_image']."')");
					$rem_inventory = mysqli_fetch_array(mysqli_query($dbc_cross, "SELECT * FROM `inventory` WHERE `inventoryid`='".mysqli_insert_id($dbc_cross)."'"));
				} else {
					$rem_inventory = mysqli_fetch_array($result);
				}
				$before_change = '';
        $history = "New inventory Added. <br />";
		    add_update_history($dbc, 'inventory_history', $history, '', $before_change);
				$inventoryid = $rem_inventory['inventoryid'];
				$new_qty = (float)$rem_inventory['quantity'] + $qty;
				$new_cost = (($rem_inventory['average_cost'] * $rem_inventory['quantity']) + ($inventory['average_cost'] * $qty)) / $new_qty;
				mysqli_query($dbc_cross, "UPDATE `inventory` SET `quantity` = '$new_qty', `average_cost`='$new_cost' WHERE `inventoryid`='$inventoryid'");
				mysqli_query($dbc_cross, "INSERT INTO `inventory_change_log` (`inventoryid`, `contactid`, `old_inventory`, `old_cost`, `changed_quantity`, `current_cost`, `new_inventory`, `new_cost`, `date_time`, `location_of_change`, `change_comment`)
					VALUES ('$inventoryid', '".get_contact($dbc, $_SESSION['contactid'])."', '".$rem_inventory['quantity']."','".$rem_inventory['average_cost']."','".$qty."','".$rem_inventory['average_cost']."','".$new_qty."','".$rem_inventory['average_cost']."',CURRENT_TIMESTAMP, 'Cross Software Transfer', '$history')");
			}
		}
	}
	//echo "<script> window.location.replace('inventory.php'); </script>";
} ?>
</head>
<script type="text/javascript" src="inventory.js"></script>
<body>
<?php include('../navigation.php');
checkAuthorised('inventory'); ?>
<div class="container" id="inventory_div">
	<div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header">
				<?php include('../Inventory/tile_header.php'); ?>
			</div>

			<div class="tile-container" style="height: 100%;">
				<div class="standard-collapsible tile-sidebar set-section-height hide-titles-mob">
					<ul class="sidebar">
						<li class="active">Transfer Inventory</li>
					</ul>
				</div>

		        <div class="scale-to-fill has-main-screen tile-content">
					<div class="main-screen standard-body">
						<div class="standard-body-title"><h3>Transfer <?= $inventory['category'] ?> <?= $inventory['part_no'] ?>: <?= $inventory['name'] ?>Count</h3></div>
						<div class="standard-body-content pad-left pad-right pad-top">
							<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
								<h4>Transfer Inventory Across Software</h4>
								<div class="form-group">
									<label class="col-sm-4 control-label">Destination:</label>
									<div class="col-sm-8">
										<select name="transfer_dest" class="chosen-select-deselect form-control"><option></option>
											<?php for($i = 1; $i <= $number_of_connections; $i++) {
												echo "<option value='".${'software_url_'.$i}."'>".${'name_of_the_software_'.$i}."</option>";
											} ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Quantity:<br /><em>Available Quantity: <?= $inventory['quantity'] ?></em></label>
									<div class="col-sm-8">
										<input type="number" min="0" max="<?= $inventory['quantity'] > 0 ? $inventory['quantity'] : 0 ?>" step="any" name="transfer_qty" class="form-control">
									</div>
								</div>
								<div class="form-group pull-right">
									<a href="inventory.php?category=<?php echo $inventory['category']; ?>"	class="btn brand-btn">Back</a>
									<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
									<button	type="submit" name="submit"	value="Submit" class="btn brand-btn">Submit</button>
								</div>
							</form>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('../footer.php'); ?>
