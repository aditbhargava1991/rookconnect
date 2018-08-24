<?php
/*
Add	Inventory
*/
include ('../include.php');
error_reporting(0);

if(isset($_POST["submitty"]))
{
	$i = 0;
	$file = htmlspecialchars($_FILES['file']['tmp_name'], ENT_QUOTES);
	$handle = fopen($file, "r");
	$c = 0;
	while(($filesop = fgetcsv($handle, 1000, ",")) !== false)
	{
	    if($i == 0) {
			$i++;
		} else {
			$col0	= mysqli_real_escape_string($dbc, htmlentities($filesop[0]));
			$col1	= mysqli_real_escape_string($dbc, htmlentities($filesop[1]));
			$col2	= mysqli_real_escape_string($dbc, htmlentities($filesop[2]));
			$col3	= mysqli_real_escape_string($dbc, htmlentities($filesop[3]));
			$col4	= mysqli_real_escape_string($dbc, htmlentities($filesop[4]));
			$col5	= mysqli_real_escape_string($dbc, htmlentities($filesop[5]));
			$col6	= mysqli_real_escape_string($dbc, htmlentities($filesop[6]));
			$col7	= mysqli_real_escape_string($dbc, htmlentities($filesop[7]));
			$col8	= mysqli_real_escape_string($dbc, htmlentities($filesop[8]));
			$col9	= mysqli_real_escape_string($dbc, htmlentities($filesop[9]));
			$col10	= mysqli_real_escape_string($dbc, htmlentities($filesop[10]));
			$col11	= mysqli_real_escape_string($dbc, htmlentities($filesop[11]));
			$col12	= mysqli_real_escape_string($dbc, htmlentities($filesop[12]));
			$col13	= mysqli_real_escape_string($dbc, htmlentities($filesop[13]));
			$col14	= mysqli_real_escape_string($dbc, htmlentities($filesop[14]));
			$col15	= mysqli_real_escape_string($dbc, htmlentities($filesop[15]));
			$col16	= mysqli_real_escape_string($dbc, htmlentities($filesop[16]));
			$col17	= mysqli_real_escape_string($dbc, htmlentities($filesop[17]));
			$col18	= mysqli_real_escape_string($dbc, htmlentities($filesop[18]));
			$col19	= mysqli_real_escape_string($dbc, htmlentities($filesop[19]));
			$col20	= mysqli_real_escape_string($dbc, htmlentities($filesop[20]));
			$col21	= mysqli_real_escape_string($dbc, htmlentities($filesop[21]));
			$col22	= mysqli_real_escape_string($dbc, htmlentities($filesop[22]));
			$col23	= mysqli_real_escape_string($dbc, htmlentities($filesop[23]));
			$col24	= mysqli_real_escape_string($dbc, htmlentities($filesop[24]));
			$col25	= mysqli_real_escape_string($dbc, htmlentities($filesop[25]));
			$col26	= mysqli_real_escape_string($dbc, htmlentities($filesop[26]));
			$col27	= mysqli_real_escape_string($dbc, htmlentities($filesop[27]));
			$col28	= mysqli_real_escape_string($dbc, htmlentities($filesop[28]));
			$col29	= mysqli_real_escape_string($dbc, htmlentities($filesop[29]));
			$col30	= mysqli_real_escape_string($dbc, htmlentities($filesop[30]));
			$col31	= mysqli_real_escape_string($dbc, htmlentities($filesop[31]));
			$col32	= mysqli_real_escape_string($dbc, htmlentities($filesop[32]));
			$col33	= mysqli_real_escape_string($dbc, htmlentities($filesop[33]));
			$col34	= mysqli_real_escape_string($dbc, htmlentities($filesop[34]));
			$col35	= mysqli_real_escape_string($dbc, htmlentities($filesop[35]));
			$col36	= mysqli_real_escape_string($dbc, htmlentities($filesop[36]));
			$col37	= mysqli_real_escape_string($dbc, htmlentities($filesop[37]));
			$col38	= mysqli_real_escape_string($dbc, htmlentities($filesop[38]));
			$col39	= mysqli_real_escape_string($dbc, htmlentities($filesop[39]));
			$col40	= mysqli_real_escape_string($dbc, htmlentities($filesop[40]));
			$col41	= mysqli_real_escape_string($dbc, htmlentities($filesop[41]));
			$col42	= mysqli_real_escape_string($dbc, htmlentities($filesop[42]));
			$col43	= mysqli_real_escape_string($dbc, htmlentities($filesop[43]));
			$col44	= mysqli_real_escape_string($dbc, htmlentities($filesop[44]));
			$col45	= mysqli_real_escape_string($dbc, htmlentities($filesop[45]));
			$col46	= mysqli_real_escape_string($dbc, htmlentities($filesop[46]));
			$col47	= mysqli_real_escape_string($dbc, htmlentities($filesop[47]));
			$col48	= mysqli_real_escape_string($dbc, htmlentities($filesop[48]));
			$col49	= mysqli_real_escape_string($dbc, htmlentities($filesop[49]));
			$col50	= mysqli_real_escape_string($dbc, htmlentities($filesop[50]));
			$col51	= mysqli_real_escape_string($dbc, htmlentities($filesop[51]));
			$col52	= mysqli_real_escape_string($dbc, htmlentities($filesop[52]));
			$col53	= mysqli_real_escape_string($dbc, htmlentities($filesop[53]));
			$col54	= mysqli_real_escape_string($dbc, htmlentities($filesop[54]));
			$col55	= mysqli_real_escape_string($dbc, htmlentities($filesop[55]));
			$col56	= mysqli_real_escape_string($dbc, htmlentities($filesop[56]));
			$col57	= mysqli_real_escape_string($dbc, htmlentities($filesop[57]));
			$col58	= mysqli_real_escape_string($dbc, htmlentities($filesop[58]));
			$col59	= mysqli_real_escape_string($dbc, htmlentities($filesop[59]));
			$col60	= mysqli_real_escape_string($dbc, htmlentities($filesop[60]));
			$col61	= mysqli_real_escape_string($dbc, htmlentities($filesop[61]));
			$col62	= mysqli_real_escape_string($dbc, htmlentities($filesop[62]));
			$col63	= mysqli_real_escape_string($dbc, htmlentities($filesop[63]));
			$col64	= mysqli_real_escape_string($dbc, htmlentities($filesop[64]));
			$col65	= mysqli_real_escape_string($dbc, htmlentities($filesop[65]));
			$col66	= mysqli_real_escape_string($dbc, htmlentities($filesop[66]));
			$col67	= mysqli_real_escape_string($dbc, htmlentities($filesop[67]));
			$col68	= mysqli_real_escape_string($dbc, htmlentities($filesop[68]));
			$col69	= mysqli_real_escape_string($dbc, htmlentities($filesop[69]));
			$col70	= mysqli_real_escape_string($dbc, htmlentities($filesop[70]));
			$col71	= mysqli_real_escape_string($dbc, htmlentities($filesop[71]));
			$col72	= mysqli_real_escape_string($dbc, htmlentities($filesop[72]));
			$col73	= mysqli_real_escape_string($dbc, htmlentities($filesop[73]));
			$col74	= mysqli_real_escape_string($dbc, htmlentities($filesop[74]));
			$col75	= mysqli_real_escape_string($dbc, htmlentities($filesop[75]));
			$col76	= mysqli_real_escape_string($dbc, htmlentities($filesop[76]));
			$col77	= mysqli_real_escape_string($dbc, htmlentities($filesop[77]));
			$col78	= mysqli_real_escape_string($dbc, htmlentities($filesop[78]));
			$col79	= mysqli_real_escape_string($dbc, htmlentities($filesop[79]));
			$col80	= mysqli_real_escape_string($dbc, htmlentities($filesop[80]));
			$col81	= mysqli_real_escape_string($dbc, htmlentities($filesop[81]));
			$col82	= mysqli_real_escape_string($dbc, htmlentities($filesop[82]));
			$col83	= mysqli_real_escape_string($dbc, htmlentities($filesop[83]));
			$col84	= mysqli_real_escape_string($dbc, htmlentities($filesop[84]));
			$col85	= mysqli_real_escape_string($dbc, htmlentities($filesop[85]));
			$col86	= mysqli_real_escape_string($dbc, htmlentities($filesop[86]));
			$col87	= mysqli_real_escape_string($dbc, htmlentities($filesop[87]));
			$col88	= mysqli_real_escape_string($dbc, htmlentities($filesop[88]));
			$col89	= mysqli_real_escape_string($dbc, htmlentities($filesop[89]));
			$i++;
			$query_insert_inventory = "INSERT INTO `products` (`code`, `category`, `sub_category`, `part_no`, `description`, `comment`, `question`, `request`, `display_website`, `vendorid`, `size`, `weight`, `type`, `name`, `date_of_purchase`, `purchase_cost`, `sell_price`, `markup`, `freight_charge`, `min_bin`, `current_stock`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `minimum_billable`, `estimated_hours`, `actual_hours`, `msrp`, `quote_description`, `usd_invoice`, `shipping_rate`, `shipping_cash`, `exchange_rate`, `exchange_cash`, `cdn_cpu`, `cogs_total`, `location`, `inv_variance`, `average_cost`, `asset`, `revenue`, `buying_units`, `selling_units`, `stocking_units`, `preferred_price`, `web_price`, `id_number`, `operator`, `lsd`, `quantity`, `product_name`, `cost`, `usd_cpu`, `commission_price`, `markup_perc`, `current_inventory`, `write_offs`, `min_max`, `status`, `note`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`, `weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `include_in_pos`, `include_in_so`, `include_in_po`,`include_in_inventory`,`hourly_rate`,`product_code`,`ticket_description`,`invoice_description`,`heading`,`product_type`) VALUES ('$col0', '$col1', '$col2', '$col3', '$col4', '$col5', '$col6', '$col7', '$col8', '$col9', '$col10', '$col11', '$col12', '$col13', '$col14', '$col15', '$col16', '$col17', '$col18', '$col19', '$col20', '$col21', '$col22', '$col23', '$col24', '$col25', '$col26', '$col27', '$col28', '$col29', '$col30', '$col31', '$col32', '$col33', '$col34', '$col35', '$col36', '$col37', '$col38', '$col39', '$col40', '$col41', '$col42', '$col43', '$col44', '$col45', '$col46', '$col47', '$col48', '$col49', '$col50', '$col51', '$col52', '$col53', '$col54', '$col55', '$col56', '$col57', '$col58', '$col59', '$col60', '$col61', '$col62', '$col63', '$col64', '$col65', '$col66', '$col67', '$col68', '$col69', '$col70', '$col71', '$col72', '$col73', '$col74', '$col75', '$col76', '$col77', '$col78', '$col79', '$col80', '$col81', '$col82', '$col83', '$col84', '$col85', '$col86', '$col87', '$col88', '$col89')";
			$result_insert_inventory = mysqli_query($dbc, $query_insert_inventory) or die(mysqli_error($dbc));
			$productid = mysql_insert_id($dbc);

			$before_change = '';
			$history = "Products entry has been added. <br />";
			add_update_history($dbc, 'products_history', $history, '', $before_change);

			if($col83 == '1') {
			$query_insert_inventory = "INSERT INTO `inventory` (`code`, `category`, `sub_category`, `part_no`, `description`, `comment`, `question`, `request`, `display_website`, `vendorid`, `size`, `weight`, `type`, `name`, `date_of_purchase`, `purchase_cost`, `sell_price`, `markup`, `freight_charge`, `min_bin`, `current_stock`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `minimum_billable`, `estimated_hours`, `actual_hours`, `msrp`, `quote_description`, `usd_invoice`, `shipping_rate`, `shipping_cash`, `exchange_rate`, `exchange_cash`, `cdn_cpu`, `cogs_total`, `location`, `inv_variance`, `average_cost`, `asset`, `revenue`, `buying_units`, `selling_units`, `stocking_units`, `preferred_price`, `web_price`, `id_number`, `operator`, `lsd`, `quantity`, `product_name`, `cost`, `usd_cpu`, `commission_price`, `markup_perc`, `current_inventory`, `write_offs`, `min_max`, `status`, `note`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`, `weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `include_in_pos`, `include_in_so`, `include_in_po`,`productid`,`hourly_rate`,`product_code`,`ticket_description`,`invoice_description`,`heading`,`product_type`) VALUES ('$col0', '$col1', '$col2', '$col3', '$col4', '$col5', '$col6', '$col7', '$col8', '$col9', '$col10', '$col11', '$col12', '$col13', '$col14', '$col15', '$col16', '$col17', '$col18', '$col19', '$col20', '$col21', '$col22', '$col23', '$col24', '$col25', '$col26', '$col27', '$col28', '$col29', '$col30', '$col31', '$col32', '$col33', '$col34', '$col35', '$col36', '$col37', '$col38', '$col39', '$col40', '$col41', '$col42', '$col43', '$col44', '$col45', '$col46', '$col47', '$col48', '$col49', '$col50', '$col51', '$col52', '$col53', '$col54', '$col55', '$col56', '$col57', '$col58', '$col59', '$col60', '$col61', '$col62', '$col63', '$col64', '$col65', '$col66', '$col67', '$col68', '$col69', '$col70', '$col71', '$col72', '$col73', '$col74', '$col75', '$col76', '$col77', '$col78', '$col79', '$col80', '$col81', '$col82', '$productid', '$col84', '$col85', '$col86', '$col87', '$col88', '$col89')";
			$result_insert_inventory = mysqli_query($dbc, $query_insert_inventory) or die(mysqli_error($dbc));

			$before_change = '';
			$history = "Inventory entry has been added. <br />";
			add_update_history($dbc, 'products_history', $history, '', $before_change);
			}
		}
	}
	    echo '<script type="text/javascript"> alert("Successfully imported CSV file. Please check the Products dashboard to view your newly added items."); </script>';
}


?>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('products');
?>
<div class="container">
	<div class="row">

		<h1>Add Multiple Products</h1>
		<div class="gap-top double-gap-bottom"><a href="products.php" class="btn config-btn">Back to Dashboard</a></div>

		<form name="import" method="post" enctype="multipart/form-data">
			<div class="notice double-gap-bottom">Steps to Upload Multiple Products at Once:<br /><br />
				<b>1.</b> Please download the following CSV file: <a href='Add_multiple_products.csv' style='text-decoration:underline;'>Add_multiple_products.csv</a>.<br>
				<b>2.</b> Use CTRL+F to find which fields you would like to populate (<span style='color:red;'>NOTE</span>: Do not change/move/delete any of the column titles in the first row).<br>
				<b>3.</b> Fill in the rows (starting from row 2). Please note that each row you fill out will become a separate item in the Products tile.<br>
				<b>4.</b> Upload the CSV file below, and hit submit.<br>
				<b>5.</b> Please look for your newly added items in the Products dashboard!<br><br>
				<input class="form-control" type="file" name="file" />
			</div>

			<div class="col-sm-6">
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your entry."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a href="products.php?category=<?php echo $category; ?>" class="btn brand-btn btn-lg">Back</a>
				<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<input class="btn brand-btn btn-lg pull-right" type="submit" name="submitty" value="Submit" />
				<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click this to finalize your entry."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			</div>
		</form>



	</div>
</div>

<?php include ('../footer.php'); ?>
