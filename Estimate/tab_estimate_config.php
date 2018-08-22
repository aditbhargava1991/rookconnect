<?php
/*
Dashboard
*/

if (isset($_POST['submit'])) {
    $config_fields = implode(',',$_POST['config_fields']);
	$tab_id = $_POST['tab_id'];

	$query = "UPDATE `estimate_tab` SET estimate_tab_config = ',$config_fields,' WHERE `estimate_tab_id` = '$tab_id'";
	$result = mysqli_query($dbc, $query);

  $before_change = '';
	$history = "Estimates tab config has been updated. <br />";
	add_update_history($dbc, 'estimates_history', $history, '', $before_change);

    echo '<script type="text/javascript"> </script>';
}
?>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<input type="hidden" name="referer" value="<?php echo $main_page; ?>" />
	<input type="hidden" name="tab_id" value="<?php echo $_GET['tab']; ?>">
<?php
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_estimate"));
$value_config = ','.$get_field_config['config_fields'].',';
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM estimate_tab WHERE `estimate_tab_id`='{$_GET['tab']}'"));
$estimate_config = ','.$get_field_config['estimate_tab_config'].',';
?>
<div class="panel-group" id="accordion2">
	<?php if(strpos($value_config,',Package,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Package" >
						Package<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Package" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Package".',') !== FALSE) { echo " checked"; } ?> value="Package" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Package
					<br><br>

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Package Service Type".',') !== FALSE) { echo " checked"; } ?> value="Package Service Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
					<input type="checkbox" <?php if (strpos($estimate_config, ','."Package Category".',') !== FALSE) { echo " checked"; } ?> value="Package Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Package Heading".',') !== FALSE) { echo " checked"; } ?> value="Package Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

				</div>
			</div>
		</div>
	<?php endif; ?>

	<?php if(strpos($value_config,',Promotion,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Promotion" >
						Promotion<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Promotion" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Promotion".',') !== FALSE) { echo " checked"; } ?> value="Promotion" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Promotion
					<br><br>

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Promotion Service Type".',') !== FALSE) { echo " checked"; } ?> value="Promotion Service Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
					<input type="checkbox" <?php if (strpos($estimate_config, ','."Promotion Category".',') !== FALSE) { echo " checked"; } ?> value="Promotion Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Promotion Heading".',') !== FALSE) { echo " checked"; } ?> value="Promotion Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Custom,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Custom" >
						Custom<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Custom" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Custom".',') !== FALSE) { echo " checked"; } ?> value="Custom" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Custom
					<br><br>

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Custom Service Type".',') !== FALSE) { echo " checked"; } ?> value="Custom Service Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
					<input type="checkbox" <?php if (strpos($estimate_config, ','."Custom Category".',') !== FALSE) { echo " checked"; } ?> value="Custom Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Custom Heading".',') !== FALSE) { echo " checked"; } ?> value="Custom Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Material,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Material" >
						Material<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Material" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Material".',') !== FALSE) { echo " checked"; } ?> value="Material" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Material
					<br><br>

					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Material Code".',') !== FALSE) { echo " checked"; } ?> value="Material Code" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Code&nbsp;&nbsp;

					<?php $type_result = mysqli_query($dbc, "SELECT DISTINCT(`rate_card_types`) type FROM `company_rate_card` WHERE `tile_name`='Material' AND `deleted` = 0 AND `rate_card_types` != '' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($type_result) > 0) { ?>
						<br><br>
						Rate Card Types:
						<br>
					<?php }
					while($row = mysqli_fetch_array($type_result)) { ?>
						<input type="checkbox" <?php if (strpos($estimate_config, ',Material'.$row['type'].',') !== FALSE) { echo " checked"; } ?> value="Material<?php echo $row['type']; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $row['type']; ?>&nbsp;&nbsp;
					<?php } ?>

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Services,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Services" >
						Services<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Services" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Services".',') !== FALSE) { echo " checked"; } ?> value="Services" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Services
					<br><br>

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Services Service Type".',') !== FALSE) { echo " checked"; } ?> value="Services Service Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Service Type&nbsp;&nbsp;
					<input type="checkbox" <?php if (strpos($estimate_config, ','."Services Category".',') !== FALSE) { echo " checked"; } ?> value="Services Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Services Heading".',') !== FALSE) { echo " checked"; } ?> value="Services Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

					<?php
					$estimate_service_price_or_hours = get_config($dbc, 'estimate_service_price_or_hours');
					if($estimate_service_price_or_hours == '') {
						$estimate_service_price_or_hours = 'Estimated Hours';
					}
					?>

				  <div class="form-group">
					<label for="fax_number"	class="col-sm-4	control-label">Change Name Price/Hours:</label>
					<div class="col-sm-8">
						<input name="estimate_service_price_or_hours" type="text" value = "<?php echo $estimate_service_price_or_hours; ?>" class="form-control">
					</div>
				  </div>

					<?php
					$estimate_service_qty_cost = get_config($dbc, 'estimate_service_qty_cost');
					if($estimate_service_qty_cost == '') {
						$estimate_service_qty_cost = 'Cost Per Hours';
					}
					?>

				  <div class="form-group">
					<label for="fax_number"	class="col-sm-4	control-label">Change Name Quantity/Cost:</label>
					<div class="col-sm-8">
						<input name="estimate_service_qty_cost" type="text" value = "<?php echo $estimate_service_qty_cost; ?>" class="form-control">
					</div>
				  </div>

					<?php $type_result = mysqli_query($dbc, "SELECT DISTINCT(`rate_card_types`) type FROM `company_rate_card` WHERE `tile_name`='Services' AND `deleted` = 0 AND `rate_card_types` != '' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($type_result) > 0) { ?>
						<br><br>
						Rate Card Types:
						<br>
					<?php }
					while($row = mysqli_fetch_array($type_result)) { ?>
						<input type="checkbox" <?php if (strpos($estimate_config, ',Services'.$row['type'].',') !== FALSE) { echo " checked"; } ?> value="Services<?php echo $row['type']; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $row['type']; ?>&nbsp;&nbsp;
					<?php } ?>

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Products,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Products" >
						Products<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Products" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Products".',') !== FALSE) { echo " checked"; } ?> value="Products" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Products
					<br><br>

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Products Product Type".',') !== FALSE) { echo " checked"; } ?> value="Products Product Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Product Type&nbsp;&nbsp;
					<input type="checkbox" <?php if (strpos($estimate_config, ','."Products Category".',') !== FALSE) { echo " checked"; } ?> value="Products Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Products Heading".',') !== FALSE) { echo " checked"; } ?> value="Products Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

					<?php $type_result = mysqli_query($dbc, "SELECT DISTINCT(`rate_card_types`) type FROM `company_rate_card` WHERE `tile_name`='Products' AND `deleted` = 0 AND `rate_card_types` != '' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($type_result) > 0) { ?>
						<br><br>
						Rate Card Types:
						<br>
					<?php }
					while($row = mysqli_fetch_array($type_result)) { ?>
						<input type="checkbox" <?php if (strpos($estimate_config, ',Product'.$row['type'].',') !== FALSE) { echo " checked"; } ?> value="Product<?php echo $row['type']; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $row['type']; ?>&nbsp;&nbsp;
					<?php } ?>

				 </div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',SRED,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_sred" >
						SR&ED<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_sred" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."SRED".',') !== FALSE) { echo " checked"; } ?> value="SRED" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;SR&ED
					<br><br>

					<input type="checkbox" <?php if (strpos($estimate_config, ','."SRED SRED Type".',') !== FALSE) { echo " checked"; } ?> value="SRED SRED Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;SR&ED Type&nbsp;&nbsp;
					<input type="checkbox" <?php if (strpos($estimate_config, ','."SRED Category".',') !== FALSE) { echo " checked"; } ?> value="SRED Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."SRED Heading".',') !== FALSE) { echo " checked"; } ?> value="SRED Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Staff,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Staff" >
						Staff<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Staff" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Staff
					<br><br>

					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Staff Contact Person".',') !== FALSE) { echo " checked"; } ?> value="Staff Contact Person" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Person&nbsp;&nbsp;

					<?php $type_result = mysqli_query($dbc, "SELECT DISTINCT(`rate_card_types`) type FROM `company_rate_card` WHERE `tile_name`='Staff' AND `deleted` = 0 AND `rate_card_types` != '' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($type_result) > 0) { ?>
						<br><br>
						Rate Card Types:
						<br>
					<?php }
					while($row = mysqli_fetch_array($type_result)) { ?>
						<input type="checkbox" <?php if (strpos($estimate_config, ',Staff'.$row['type'].',') !== FALSE) { echo " checked"; } ?> value="Staff<?php echo $row['type']; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $row['type']; ?>&nbsp;&nbsp;
					<?php } ?>

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Contractor,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Contractor" >
						Contractor<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Contractor" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Contractor".',') !== FALSE) { echo " checked"; } ?> value="Contractor" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contractor
					<br><br>

					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Contractor Contact Person".',') !== FALSE) { echo " checked"; } ?> value="Contractor Contact Person" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Person&nbsp;&nbsp;

					<?php $type_result = mysqli_query($dbc, "SELECT DISTINCT(`rate_card_types`) type FROM `company_rate_card` WHERE `tile_name`='Contractor' AND `deleted` = 0 AND `rate_card_types` != '' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($type_result) > 0) { ?>
						<br><br>
						Rate Card Types:
						<br>
					<?php }
					while($row = mysqli_fetch_array($type_result)) { ?>
						<input type="checkbox" <?php if (strpos($estimate_config, ',Contractor'.$row['type'].',') !== FALSE) { echo " checked"; } ?> value="Contractor<?php echo $row['type']; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $row['type']; ?>&nbsp;&nbsp;
					<?php } ?>

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Clients,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Clients" >
						Clients<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Clients" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Clients".',') !== FALSE) { echo " checked"; } ?> value="Clients" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Clients
					<br><br>

					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Clients Client Name".',') !== FALSE) { echo " checked"; } ?> value="Clients Client Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Client Name&nbsp;&nbsp;

					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Clients Contact Person".',') !== FALSE) { echo " checked"; } ?> value="Clients Contact Person" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Person&nbsp;&nbsp;

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Vendor Pricelist,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pl" >
						Vendor Pricelist<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_pl" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Vendor Pricelist".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Vendor Pricelist
					<br><br>

					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Vendor Pricelist Vendor".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist Vendor" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Vendor&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Vendor Pricelist Price List".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist Price List" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Price List&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Vendor Pricelist Category".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Vendor Pricelist Product".',') !== FALSE) { echo " checked"; } ?> value="Vendor Pricelist Product" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Product&nbsp;&nbsp;

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Customer,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Customer" >
						Customer<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Customer" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Customer".',') !== FALSE) { echo " checked"; } ?> value="Customer" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Customer
					<br><br>

					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Customer Customer Name".',') !== FALSE) { echo " checked"; } ?> value="Customer Customer Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Customer Name&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Customer Contact Person".',') !== FALSE) { echo " checked"; } ?> value="Customer Contact Person" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Contact Person&nbsp;&nbsp;

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Inventory,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Inventory" >
						Inventory<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Inventory" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Inventory".',') !== FALSE) { echo " checked"; } ?> value="Inventory" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Inventory
					<br><br>

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Inventory Category".',') !== FALSE) { echo " checked"; } ?> value="Inventory Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;

				   <?php /* <input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Inventory Product Name".',') !== FALSE) { echo " checked"; } ?> value="Inventory Product Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Product Name&nbsp;&nbsp; */ ?>

					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Inventory Product Name".',') !== FALSE) { echo " checked"; } ?> value="Inventory Product Name" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Product Name&nbsp;&nbsp;

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Inventory Part No".',') !== FALSE) { echo " checked"; } ?> value="Inventory Part No" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Part Number&nbsp;&nbsp;

					<?php $type_result = mysqli_query($dbc, "SELECT DISTINCT(`rate_card_types`) type FROM `company_rate_card` WHERE `tile_name`='Inventory' AND `deleted` = 0 AND `rate_card_types` != '' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($type_result) > 0) { ?>
						<br><br>
						Rate Card Types:
						<br>
					<?php }
					while($row = mysqli_fetch_array($type_result)) { ?>
						<input type="checkbox" <?php if (strpos($estimate_config, ',Inventory'.$row['type'].',') !== FALSE) { echo " checked"; } ?> value="Inventory<?php echo $row['type']; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $row['type']; ?>&nbsp;&nbsp;
					<?php } ?>

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Equipment,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Equipment" >
						Equipment<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Equipment" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Equipment".',') !== FALSE) { echo " checked"; } ?> value="Equipment" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Equipment
					<br><br>

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Equipment Category".',') !== FALSE) { echo " checked"; } ?> value="Equipment Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Equipment Unit/Serial Number".',') !== FALSE) { echo " checked"; } ?> value="Equipment Unit/Serial Number" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Unit/Serial Number&nbsp;&nbsp;

					<?php $type_result = mysqli_query($dbc, "SELECT DISTINCT(`rate_card_types`) type FROM `company_rate_card` WHERE `tile_name`='Equipment' AND `deleted` = 0 AND `rate_card_types` != '' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($type_result) > 0) { ?>
						<br><br>
						Rate Card Types:
						<br>
					<?php }
					while($row = mysqli_fetch_array($type_result)) { ?>
						<input type="checkbox" <?php if (strpos($estimate_config, ',Equipment'.$row['type'].',') !== FALSE) { echo " checked"; } ?> value="Equipment<?php echo $row['type']; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $row['type']; ?>&nbsp;&nbsp;
					<?php } ?>
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Labour,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Labour" >
						Labour<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Labour" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Labour".',') !== FALSE) { echo " checked"; } ?> value="Labour" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Labour
					<br><br>

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Labour Type".',') !== FALSE) { echo " checked"; } ?> value="Labour Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Type&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Labour Heading".',') !== FALSE) { echo " checked"; } ?> value="Labour Heading" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Heading&nbsp;&nbsp;

					<?php $type_result = mysqli_query($dbc, "SELECT DISTINCT(`rate_card_types`) type FROM `company_rate_card` WHERE `tile_name`='Labour' AND `deleted` = 0 AND `rate_card_types` != '' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($type_result) > 0) { ?>
						<br><br>
						Rate Card Types:
						<br>
					<?php }
					while($row = mysqli_fetch_array($type_result)) { ?>
						<input type="checkbox" <?php if (strpos($estimate_config, ',Labour'.$row['type'].',') !== FALSE) { echo " checked"; } ?> value="Labour<?php echo $row['type']; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $row['type']; ?>&nbsp;&nbsp;
					<?php } ?>

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Expenses,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Expenses" >
						Expenses<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Expenses" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Expenses".',') !== FALSE) { echo " checked"; } ?> value="Expenses" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Expenses
					<br><br>

					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Expenses Type".',') !== FALSE) { echo " checked"; } ?> value="Expenses Type" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Type&nbsp;&nbsp;
					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Expenses Category".',') !== FALSE) { echo " checked"; } ?> value="Expenses Category" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Category&nbsp;&nbsp;
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Setup,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Setup" >
						Set Up<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Setup" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Setup".',') !== FALSE) { echo " checked"; } ?> value="Setup" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Set Up

					<?php $type_result = mysqli_query($dbc, "SELECT DISTINCT(`rate_card_types`) type FROM `company_rate_card` WHERE `tile_name`='Setup' AND `deleted` = 0 AND `rate_card_types` != '' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($type_result) > 0) { ?>
						<br><br>
						Rate Card Types:
						<br>
					<?php }
					while($row = mysqli_fetch_array($type_result)) { ?>
						<input type="checkbox" <?php if (strpos($estimate_config, ',Setup'.$row['type'].',') !== FALSE) { echo " checked"; } ?> value="Setup<?php echo $row['type']; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $row['type']; ?>&nbsp;&nbsp;
					<?php } ?>

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Teardown,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Teardown" >
						Tear Down<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Teardown" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Teardown".',') !== FALSE) { echo " checked"; } ?> value="Teardown" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Tear Down

					<?php $type_result = mysqli_query($dbc, "SELECT DISTINCT(`rate_card_types`) type FROM `company_rate_card` WHERE `tile_name`='Teardown' AND `deleted` = 0 AND `rate_card_types` != '' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($type_result) > 0) { ?>
						<br><br>
						Rate Card Types:
						<br>
					<?php }
					while($row = mysqli_fetch_array($type_result)) { ?>
						<input type="checkbox" <?php if (strpos($estimate_config, ',Teardown'.$row['type'].',') !== FALSE) { echo " checked"; } ?> value="Teardown<?php echo $row['type']; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $row['type']; ?>&nbsp;&nbsp;
					<?php } ?>

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Rental,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Rental" >
						Rental<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Rental" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Rental".',') !== FALSE) { echo " checked"; } ?> value="Rental" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Rental

					<?php $type_result = mysqli_query($dbc, "SELECT DISTINCT(`rate_card_types`) type FROM `company_rate_card` WHERE `tile_name`='Rental' AND `deleted` = 0 AND `rate_card_types` != '' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($type_result) > 0) { ?>
						<br><br>
						Rate Card Types:
						<br>
					<?php }
					while($row = mysqli_fetch_array($type_result)) { ?>
						<input type="checkbox" <?php if (strpos($estimate_config, ',Rental'.$row['type'].',') !== FALSE) { echo " checked"; } ?> value="Rental<?php echo $row['type']; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $row['type']; ?>&nbsp;&nbsp;
					<?php } ?>

				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php if(strpos($value_config,',Other,') !== false): ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Other" >
						Other<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_Other" class="panel-collapse collapse">
				<div class="panel-body">

					<input type="checkbox" <?php if (strpos($estimate_config, ','."Other".',') !== FALSE) { echo " checked"; } ?> value="Other" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Other
					<br><br>

					<input disabled type="checkbox" <?php if (strpos($estimate_config, ','."Other Detail".',') !== FALSE) { echo " checked"; } ?> value="Other Detail" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;Detail&nbsp;&nbsp;
				</div>
			</div>
		</div>
	<?php endif; ?>
	<?php $accordions = explode('#*#',mysqli_fetch_array(mysqli_query($dbc,"SELECT `custom_accordions` FROM `field_config_estimate`"))['custom_accordions']);
	foreach($accordions as $accordion):
		$config_arr = explode(',',$accordion);
		$name = $config_arr[0];
		$id = str_replace(' ','',strtolower($name));
		unset($config_arr[0]); ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_<?php echo $id; ?>" ><?php echo $name; ?><span class="glyphicon glyphicon-plus"></span></a>
				</h4>
			</div>

			<div id="collapse_<?php echo $id; ?>" class="panel-collapse collapse">
				<div class="panel-body">
					<input type="checkbox" <?php if (strpos($estimate_config, ','.$id.',') !== FALSE) { echo " checked"; } ?> value="<?php echo $id; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $name; ?>

					<?php $type_result = mysqli_query($dbc, "SELECT DISTINCT(`rate_card_types`) type FROM `company_rate_card` WHERE `tile_name`='$id' AND `deleted` = 0 AND `rate_card_types` != '' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
					if(mysqli_num_rows($type_result) > 0) { ?>
						<br><br>
						Rate Card Types:
						<br>
					<?php }
					while($row = mysqli_fetch_array($type_result)) { ?>
						<input type="checkbox" <?php if (strpos($estimate_config, ','.$id.$row['type'].',') !== FALSE) { echo " checked"; } ?> value="<?php echo $id.$row['type']; ?>" style="height: 20px; width: 20px;" name="config_fields[]">&nbsp;&nbsp;<?php echo $row['type']; ?>&nbsp;&nbsp;
					<?php } ?>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div>

<div class="form-group">
        <a href="<?php echo $main_page; ?>" class="btn config-btn btn-lg pull-left">Back</a>
		<!--<a href="#" class="btn config-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->

        <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
</div>

</form>
