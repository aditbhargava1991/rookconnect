<?php
/*
Add	Inventory
*/
include_once ('../include.php');
error_reporting(0);
// ADD INVENTORY
if(isset($_POST["submitty"]))
{
	$i = 0;
	$file = htmlspecialchars($_FILES['file']['tmp_name'], ENT_QUOTES);
	$handle = fopen($file, "r");
	$c = 0;
	while(($filesop = fgetcsv($handle, 1000, ",")) !== false)
	{
	    if($i == 0 || $i == 1) {
			$i++;
		} else {
			$col0	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[0],ENT_NOQUOTES));
			$col1	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[1],ENT_NOQUOTES));
			$col2	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[2],ENT_NOQUOTES));
			$col3	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[3],ENT_NOQUOTES));
			$col4	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[4],ENT_NOQUOTES));
			$col5	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[5],ENT_NOQUOTES));
			$col6	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[6],ENT_NOQUOTES));
			$col7	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[7],ENT_NOQUOTES));
			$col8	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[8],ENT_NOQUOTES));
			$col9	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[9],ENT_NOQUOTES));
			$col10	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[10],ENT_NOQUOTES));
			$col11	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[11],ENT_NOQUOTES));
			$col12	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[12],ENT_NOQUOTES));
			$col13	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[13],ENT_NOQUOTES));
			$col14	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[14],ENT_NOQUOTES));
			$col15	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[15],ENT_NOQUOTES));
			$col16	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[16],ENT_NOQUOTES));
			$col17	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[17],ENT_NOQUOTES));
			$col18	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[18],ENT_NOQUOTES));
			$col19	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[19],ENT_NOQUOTES));
			$col20	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[20],ENT_NOQUOTES));
			$col21	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[21],ENT_NOQUOTES));
			$col22	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[22],ENT_NOQUOTES));
			$col23	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[23],ENT_NOQUOTES));
			$col24	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[24],ENT_NOQUOTES));
			$col25	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[25],ENT_NOQUOTES));
			$col26	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[26],ENT_NOQUOTES));
			$col27	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[27],ENT_NOQUOTES));
			$col28	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[28],ENT_NOQUOTES));
			$col29	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[29],ENT_NOQUOTES));
			$col30	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[30],ENT_NOQUOTES));
			$col31	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[31],ENT_NOQUOTES));
			$col32	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[32],ENT_NOQUOTES));
			$col33	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[33],ENT_NOQUOTES));
			$col34	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[34],ENT_NOQUOTES));
			$col35	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[35],ENT_NOQUOTES));
			$col36	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[36],ENT_NOQUOTES));
			$col37	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[37],ENT_NOQUOTES));
			$col38	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[38],ENT_NOQUOTES));
			$col39	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[39],ENT_NOQUOTES));
			$col40	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[40],ENT_NOQUOTES));
			$col41	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[41],ENT_NOQUOTES));
			$col42	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[42],ENT_NOQUOTES));
			$col43	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[43],ENT_NOQUOTES));
			$col44	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[44],ENT_NOQUOTES));
			$col45	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[45],ENT_NOQUOTES));
			$col46	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[46],ENT_NOQUOTES));
			$col47	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[47],ENT_NOQUOTES));
			$col48	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[48],ENT_NOQUOTES));
			$col49	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[49],ENT_NOQUOTES));
			$col50	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[50],ENT_NOQUOTES));
			$col51	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[51],ENT_NOQUOTES));
			$col52	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[52],ENT_NOQUOTES));
			$col53	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[53],ENT_NOQUOTES));
			$col54	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[54],ENT_NOQUOTES));
			$col55	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[55],ENT_NOQUOTES));
			$col56	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[56],ENT_NOQUOTES));
			$col57	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[57],ENT_NOQUOTES));
			$col58	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[58],ENT_NOQUOTES));
			$col59	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[59],ENT_NOQUOTES));
			$col60	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[60],ENT_NOQUOTES));
			$col61	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[61],ENT_NOQUOTES));
			$col62	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[62],ENT_NOQUOTES));
			$col63	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[63],ENT_NOQUOTES));
			$col64	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[64],ENT_NOQUOTES));
			$col65	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[65],ENT_NOQUOTES));
			$col66	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[66],ENT_NOQUOTES));
			$col67	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[67],ENT_NOQUOTES));
			$col68	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[68],ENT_NOQUOTES));
			$col69	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[69],ENT_NOQUOTES));
			$col70	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[70],ENT_NOQUOTES));
			$col71	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[71],ENT_NOQUOTES));
			$col72	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[72],ENT_NOQUOTES));
			$col73	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[73],ENT_NOQUOTES));
			$col74	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[74],ENT_NOQUOTES));
			$col75	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[75],ENT_NOQUOTES));
			$col76	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[76],ENT_NOQUOTES));
			$col77	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[77],ENT_NOQUOTES));
			$col78	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[78],ENT_NOQUOTES));
			$col79	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[79],ENT_NOQUOTES));
			$col80	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[80],ENT_NOQUOTES));
			$col81	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[81],ENT_NOQUOTES));
			$col82	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[82],ENT_NOQUOTES));
			$col83	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[83],ENT_NOQUOTES));
			if($_POST['vendorid'] > 0) {
				$col0 = filter_var($_POST['vendorid'],FILTER_SANITIZE_STRING);
			}
			if(!empty($_POST['vpl_name'])) {
				if($_POST['vpl_name'] == 'NEW_VPL') {
					$col1 = filter_var($_POST['new_vpl_name'],FILTER_SANITIZE_STRING);
				} else {
					$col1 = filter_var($_POST['vpl_name'],FILTER_SANITIZE_STRING);
				}
			}

			$i++;
			$query_insert_inventory = "INSERT INTO `vendor_price_list` (`vendorid`, `vpl_name`, `code`, `category`, `sub_category`, `part_no`, `description`, `comment`, `question`, `request`, `display_website`, `size`, `weight`, `type`, `name`, `date_of_purchase`, `purchase_cost`, `sell_price`, `markup`, `freight_charge`, `min_bin`, `current_stock`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `minimum_billable`, `estimated_hours`, `actual_hours`, `msrp`, `quote_description`, `usd_invoice`, `shipping_rate`, `shipping_cash`, `exchange_rate`, `exchange_cash`, `cdn_cpu`, `cogs_total`, `location`, `inv_variance`, `average_cost`, `asset`, `revenue`, `buying_units`, `selling_units`, `stocking_units`, `preferred_price`, `web_price`, `id_number`, `operator`, `lsd`, `quantity`, `product_name`, `cost`, `usd_cpu`, `commission_price`, `markup_perc`, `current_inventory`, `write_offs`, `min_max`, `status`, `note`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`, `weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles`, `include_in_pos`, `include_in_so`, `include_in_po`) VALUES ('$col0', '$col1', '$col2', '$col3', '$col4', '$col5', '$col6', '$col7', '$col8', '$col9', '$col10', '$col11', '$col12', '$col13', '$col14', '$col15', '$col16', '$col17', '$col18', '$col19', '$col20', '$col21', '$col22', '$col23', '$col24', '$col25', '$col26', '$col27', '$col28', '$col29', '$col30', '$col31', '$col32', '$col33', '$col34', '$col35', '$col36', '$col37', '$col38', '$col39', '$col40', '$col41', '$col42', '$col43', '$col44', '$col45', '$col46', '$col47', '$col48', '$col49', '$col50', '$col51', '$col52', '$col53', '$col54', '$col55', '$col56', '$col57', '$col58', '$col59', '$col60', '$col61', '$col62', '$col63', '$col64', '$col65', '$col66', '$col67', '$col68', '$col69', '$col70', '$col71', '$col72', '$col73', '$col74', '$col75', '$col76', '$col77', '$col78', '$col79', '$col80', '$col81', '$col82', '$col83')";

			$result_insert_inventory = mysqli_query($dbc, $query_insert_inventory) or die(mysqli_error($dbc));
			$inventoryid = mysqli_insert_id($dbc);

			$before_change = '';
			$history = "New Vendor Price list added. <br />";
			add_update_history($dbc, 'vendorpl_history', $history, '', $before_change);

				$update_log = 'VPL Item Added (ID: '.$inventoryid.')';
				$today_date = date('Y-m-d H:i:s', time());
				$contactid = $_SESSION['contactid'];
				$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
				while($row = mysqli_fetch_assoc($result)) {
					$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
				}
				$query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Vendor Price List', 'Add', '$update_log', '$today_date', '$name')";
				$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
		}
	}
	    echo '<script type="text/javascript"> alert("Successfully imported CSV file. Please check the Vendor Price List dashboard to view your newly added items."); </script>';
}
// END ADD INVENTORY
// BEGIN EDIT INVENTORY
if(isset($_POST["submitty2"]))
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
			$col0	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[0],ENT_NOQUOTES));
			$col1	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[1],ENT_NOQUOTES));
			$col2	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[2],ENT_NOQUOTES));
			$col3	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[3],ENT_NOQUOTES));
			$col4	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[4],ENT_NOQUOTES));
			$col5	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[5],ENT_NOQUOTES));
			$col6	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[6],ENT_NOQUOTES));
			$col7	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[7],ENT_NOQUOTES));
			$col8	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[8],ENT_NOQUOTES));
			$col9	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[9],ENT_NOQUOTES));
			$col10	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[10],ENT_NOQUOTES));
			$col11	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[11],ENT_NOQUOTES));
			$col12	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[12],ENT_NOQUOTES));
			$col13	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[13],ENT_NOQUOTES));
			$col14	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[14],ENT_NOQUOTES));
			$col15	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[15],ENT_NOQUOTES));
			$col16	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[16],ENT_NOQUOTES));
			$col17	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[17],ENT_NOQUOTES));
			$col18	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[18],ENT_NOQUOTES));
			$col19	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[19],ENT_NOQUOTES));
			$col20	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[20],ENT_NOQUOTES));
			$col21	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[21],ENT_NOQUOTES));
			$col22	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[22],ENT_NOQUOTES));
			$col23	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[23],ENT_NOQUOTES));
			$col24	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[24],ENT_NOQUOTES));
			$col25	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[25],ENT_NOQUOTES));
			$col26	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[26],ENT_NOQUOTES));
			$col27	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[27],ENT_NOQUOTES));
			$col28	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[28],ENT_NOQUOTES));
			$col29	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[29],ENT_NOQUOTES));
			$col30	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[30],ENT_NOQUOTES));
			$col31	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[31],ENT_NOQUOTES));
			$col32	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[32],ENT_NOQUOTES));
			$col33	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[33],ENT_NOQUOTES));
			$col34	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[34],ENT_NOQUOTES));
			$col35	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[35],ENT_NOQUOTES));
			$col36	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[36],ENT_NOQUOTES));
			$col37	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[37],ENT_NOQUOTES));
			$col38	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[38],ENT_NOQUOTES));
			$col39	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[39],ENT_NOQUOTES));
			$col40	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[40],ENT_NOQUOTES));
			$col41	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[41],ENT_NOQUOTES));
			$col42	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[42],ENT_NOQUOTES));
			$col43	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[43],ENT_NOQUOTES));
			$col44	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[44],ENT_NOQUOTES));
			$col45	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[45],ENT_NOQUOTES));
			$col46	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[46],ENT_NOQUOTES));
			$col47	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[47],ENT_NOQUOTES));
			$col48	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[48],ENT_NOQUOTES));
			$col49	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[49],ENT_NOQUOTES));
			$col50	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[50],ENT_NOQUOTES));
			$col51	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[51],ENT_NOQUOTES));
			$col52	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[52],ENT_NOQUOTES));
			$col53	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[53],ENT_NOQUOTES));
			$col54	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[54],ENT_NOQUOTES));
			$col55	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[55],ENT_NOQUOTES));
			$col56	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[56],ENT_NOQUOTES));
			$col57	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[57],ENT_NOQUOTES));
			$col58	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[58],ENT_NOQUOTES));
			$col59	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[59],ENT_NOQUOTES));
			$col60	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[60],ENT_NOQUOTES));
			$col61	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[61],ENT_NOQUOTES));
			$col62	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[62],ENT_NOQUOTES));
			$col63	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[63],ENT_NOQUOTES));
			$col64	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[64],ENT_NOQUOTES));
			$col65	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[65],ENT_NOQUOTES));
			$col66	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[66],ENT_NOQUOTES));
			$col67	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[67],ENT_NOQUOTES));
			$col68	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[68],ENT_NOQUOTES));
			$col69	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[69],ENT_NOQUOTES));
			$col70	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[70],ENT_NOQUOTES));
			$col71	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[71],ENT_NOQUOTES));
			$col72	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[72],ENT_NOQUOTES));
			$col73	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[73],ENT_NOQUOTES));
			$col74	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[74],ENT_NOQUOTES));
			$col75	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[75],ENT_NOQUOTES));
			$col76	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[76],ENT_NOQUOTES));
			$col77	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[77],ENT_NOQUOTES));
			$col78	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[78],ENT_NOQUOTES));
			$col79	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[79],ENT_NOQUOTES));
			$col80	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[80],ENT_NOQUOTES));
			$col81	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[81],ENT_NOQUOTES));
			$col82	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[82],ENT_NOQUOTES));
			$col83	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[83],ENT_NOQUOTES));
			$col84	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[84],ENT_NOQUOTES));
			$col85	= mysqli_real_escape_string($dbc, htmlspecialchars_decode($filesop[85],ENT_NOQUOTES));
			if($_POST['vendorid'] > 0) {
				$col1 = filter_var($_POST['vendorid'],FILTER_SANITIZE_STRING);
			}
			if(!empty($_POST['vpl_name'])) {
				if($_POST['vpl_name'] == 'NEW_VPL') {
					$col2 = filter_var($_POST['new_vpl_name'],FILTER_SANITIZE_STRING);
				} else {
					$col2 = filter_var($_POST['vpl_name'],FILTER_SANITIZE_STRING);
				}
			}

			$sql = mysqli_query($dbc, 'SELECT * FROM vendor_price_list WHERE inventoryid = "'.$col0.'"');
			while($row = mysqli_fetch_assoc($sql)){
				if($row['inventoryid'] == $col0) {
					$HeadingsArray=array();
					foreach($row as $name => $value){
						$HeadingsArray[]=$value;
					}
				}
			}

			$i++;
			$query_insert_inventory = "UPDATE vendor_price_list SET vendorid = '$col1',vpl_name = '$col2',code = '$col3',category = '$col4',sub_category = '$col5,part_no = '$col6',display_website = '$col7',product_name = '$col8',cost = '$col9',usd_cpu = '$col10',commission_price = '$col11',markup_perc = '$col12',current_inventory = '$col13',write_offs = '$col14',min_max = '$col15',status = '$col16',note = '$col17',description = '$col18',comment = '$col19',question = '$col20',request = '$col21',quote_description = '$col22',size = '$col23',weight = '$col24',type = '$col25',name = '$col26',usd_invoice = '$col27',shipping_rate = '$col28',shipping_cash = '$col29',exchange_rate = '$col30',exchange_cash = '$col31',location = '$col32',id_number = '$col33',operator = '$col34',lsd = '$col35',quantity = '$col36',inv_variance = '$col37',average_cost = '$col38',asset = '$col39',revenue = '$col40',buying_units = '$col41',selling_units = '$col42',stocking_units = '$col43',preferred_price = '$col44',web_price = '$col45',cdn_cpu = '$col46',cogs_total = '$col47',date_of_purchase = '$col48',purchase_cost = '$col49',sell_price = '$col50',markup = '$col51',freight_charge = '$col52',min_bin = '$col53',current_stock = '$col54',final_retail_price = '$col55', admin_price = '$col56',wholesale_price = '$col57',commercial_price = '$col58',client_price = '$col59',minimum_billable = '$col60',estimated_hours = '$col61',actual_hours = '$col62',msrp = '$col63',unit_price = '$col64',unit_cost = '$col65',rent_price = '$col66',rental_days = '$col67',rental_weeks = '$col68',rental_months = '$col69',rental_years = '$col70',reminder_alert = '$col71',daily = '$col72',weekly = '$col73',monthly = '$col74',annually = '$col75',total_days = '$col76',total_hours = '$col77',total_km = '$col78',total_miles = '$col79',deleted = '$col80',purchase_order_price = '$col81',sales_order_price = '$col82',include_in_pos = '$col83',include_in_so = '$col84',include_in_po = '$col85' WHERE inventoryid= '$col0'";
			$result_insert_inventory = mysqli_query($dbc, $query_insert_inventory) or die(mysqli_error($dbc));


			$sql = mysqli_query($dbc, 'SELECT * FROM vendor_price_list WHERE inventoryid = "'.$col0.'"');
			while($row = mysqli_fetch_assoc($sql)){
				$x = 0;
				foreach($row as $name => $value){
					$name = $name;
					$xx = 0;
					foreach ($HeadingsArray as $original) {
						if($x == $xx) {
if($original != $value) {
	$update_log = $name.' was changed from "'.$original.'" to "'.$value.'" where inventory ID = '.$col0.'';
	$today_date = date('Y-m-d H:i:s', time());
	$contactid = $_SESSION['contactid'];
	$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
	while($row = mysqli_fetch_assoc($result)) {
		$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
	}
	$query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Vendor Price List', 'Edit', '$update_log', '$today_date', '$name')";
	$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
}
						}
						$xx++;
					}
					$x++;
				}
			}
		}
	}
	    echo '<script type="text/javascript"> alert("Successfully imported CSV file. Please check the Vendor Price List dashboard to view your freshly edited items."); </script>';
}
// END EDIT INVENTORY
// BEGIN EXPORT FROM EXPORT PAGE
/*
if(isset($_POST["exporter"]))
{
		$category = $_POST['category_export'];
		$all_type = rtrim($type_url, ", ");
		$today_date = date('Y-m-d_h-i-s-a', time());
		$FileName = "exports/inventory_export_".$today_date.".csv";
		$file = fopen($FileName,"w");
			$sql = mysqli_query($dbc, 'SELECT * FROM inventory')  or die(mysqli_error($dbc));
		// Save headings alon
		$HeadingsArray=array();
		foreach($row as $name => $value){
			$HeadingsArray[]=$name;
		}
		fputcsv($file,$HeadingsArray);

		if($category == '3456780123456971230') {
			$sql = mysqli_query($dbc, 'SELECT inventoryid,code,category,sub_category,part_no,display_website,product_name,cost,usd_cpu,commission_price,markup_perc,current_inventory,write_offs,min_max,status,note,description,comment,question,request,quote_description,vendorid,size,weight,type,name,usd_invoice,shipping_rate,shipping_cash,exchange_rate,exchange_cash,location,id_number,operator,lsd,quantity,inv_variance,average_cost,asset,revenue,buying_units,selling_units,stocking_units,preferred_price,web_price,cdn_cpu,cogs_total,date_of_purchase,purchase_cost,sell_price,markup,freight_charge,min_bin,current_stock,final_retail_price,admin_price,wholesale_price,commercial_price,client_price,minimum_billable,estimated_hours,actual_hours,msrp,unit_price,unit_cost,rent_price,rental_days,rental_weeks,rental_months,rental_years,reminder_alert,daily,weekly,monthly,annually,total_days,total_hours,total_km,total_miles,deleted,purchase_order_price,sales_order_price,include_in_pos,include_in_so,include_in_po FROM `vendor_price_list`');
		} else {
			$sql = mysqli_query($dbc, 'SELECT inventoryid,code,category,sub_category,part_no,display_website,product_name,cost,usd_cpu,commission_price,markup_perc,current_inventory,write_offs,min_max,status,note,description,comment,question,request,quote_description,vendorid,size,weight,type,name,usd_invoice,shipping_rate,shipping_cash,exchange_rate,exchange_cash,location,id_number,operator,lsd,quantity,inv_variance,average_cost,asset,revenue,buying_units,selling_units,stocking_units,preferred_price,web_price,cdn_cpu,cogs_total,date_of_purchase,purchase_cost,sell_price,markup,freight_charge,min_bin,current_stock,final_retail_price,admin_price,wholesale_price,commercial_price,client_price,minimum_billable,estimated_hours,actual_hours,msrp,unit_price,unit_cost,rent_price,rental_days,rental_weeks,rental_months,rental_years,reminder_alert,daily,weekly,monthly,annually,total_days,total_hours,total_km,total_miles,deleted,purchase_order_price,sales_order_price,include_in_pos,include_in_so,include_in_po FROM `vendor_price_list` WHERE category = "'.$category.'"');
		}

		// Save all records without headings
		while($row = mysqli_fetch_assoc($sql)){
			$valuesArray=array();
			foreach($row as $name => $value){
				$valuesArray[]=$value;
			}
			fputcsv($file,$valuesArray);
		}
		fclose($file);
		header("Location: $FileName");
		if($category == '3456780123456971230') {
			$update_log = 'All VPL items were exported.';
		} else {
			$update_log = 'All VPL items under the '.$category.' category was exported.';
		}

		$today_date = date('Y-m-d H:i:s', time());
		$contactid = $_SESSION['contactid'];
		$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
		while($row = mysqli_fetch_assoc($result)) {
			$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
		}
		$query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Vendor Price List', 'Export', '$update_log', '$today_date', '$name')";
		$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
	    echo '<script type="text/javascript"> alert("Successfully exported CSV file."); </script>';
}

// END EXPORT FROM EXPORT PAGE
*/

if(isset($_POST["exporter"]))
{
	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	if(!file_exists('exports')) {
		mkdir('exports', 0777, true);
	}
		$category = $_POST['category_export'];
		$vendorid = $_POST['vendorid'];
		$vpl_name = $_POST['vpl_name'];
		$all_type = rtrim($type_url, ", ");
		$today_date = date('Y-m-d_h-i-s-a', time());
		$FileName = "exports/inventory_export_".$today_date.".csv";
		$file = fopen($FileName,"w");
			$sql = mysqli_query($dbc, 'SELECT inventoryid,vendorid,vpl_name,code,category,sub_category,part_no,display_website,product_name,cost,usd_cpu,commission_price,markup_perc,current_inventory,write_offs,min_max,status,note,description,comment,question,request,quote_description,size,weight,type,name,usd_invoice,shipping_rate,shipping_cash,exchange_rate,exchange_cash,location,id_number,operator,lsd,quantity,inv_variance,average_cost,asset,revenue,buying_units,selling_units,stocking_units,preferred_price,web_price,cdn_cpu,cogs_total,date_of_purchase,purchase_cost,sell_price,markup,freight_charge,min_bin,current_stock,final_retail_price,admin_price,wholesale_price,commercial_price,client_price,minimum_billable,estimated_hours,actual_hours,msrp,unit_price,unit_cost,rent_price,rental_days,rental_weeks,rental_months,rental_years,reminder_alert,daily,weekly,monthly,annually,total_days,total_hours,total_km,total_miles,deleted,purchase_order_price,sales_order_price,include_in_pos,include_in_so,include_in_po
 FROM `vendor_price_list`');
		$row = mysqli_fetch_assoc($sql);
		// Save headings alon
		$HeadingsArray=array();
		foreach($row as $name => $value){
			$HeadingsArray[]=$name;
		}
		fputcsv($file,$HeadingsArray);

		$sql = 'SELECT inventoryid,vendorid,vpl_name,code,category,sub_category,part_no,display_website,product_name,cost,usd_cpu,commission_price,markup_perc,current_inventory,write_offs,min_max,status,note,description,comment,question,request,quote_description,size,weight,type,name,usd_invoice,shipping_rate,shipping_cash,exchange_rate,exchange_cash,location,id_number,operator,lsd,quantity,inv_variance,average_cost,asset,revenue,buying_units,selling_units,stocking_units,preferred_price,web_price,cdn_cpu,cogs_total,date_of_purchase,purchase_cost,sell_price,markup,freight_charge,min_bin,current_stock,final_retail_price,admin_price,wholesale_price,commercial_price,client_price,minimum_billable,estimated_hours,actual_hours,msrp,unit_price,unit_cost,rent_price,rental_days,rental_weeks,rental_months,rental_years,reminder_alert,daily,weekly,monthly,annually,total_days,total_hours,total_km,total_miles,deleted,purchase_order_price,sales_order_price,include_in_pos,include_in_so,include_in_po FROM `vendor_price_list`';
		$filter_query = [];
		if($category != '3456780123456971230') {
			$filter_query[] = '`category` = "'.$category.'"';
		}
		if($vendorid > 0) {
			$filter_query[] = '`vendorid` = "'.$vendorid.'"';
		}
		if(!empty($vpl_name)) {
			$filter_query[] = '`vpl_name` = "'.$vpl_name.'"';
		}
		if(!empty($filter_query)) {
			$filter_query = ' WHERE '.implode(" AND ", $filter_query);
		} else {
			$filter_query = '';
		}
		$sql = mysqli_query($dbc, $sql.$filter_query);

		// Save all records without headings
		while($row = mysqli_fetch_assoc($sql)){
			$valuesArray=array();
			foreach($row as $name => $value){
				$valuesArray[]=$value;
			}
			fputcsv($file,$valuesArray);
		}
		fclose($file);
		header("Location: $FileName");
		if($category == '3456780123456971230') {
			$update_log = 'All VPL items were exported.';
		} else {
			$update_log = 'All VPL items under the '.$category.' category was exported.';
		}

		$today_date = date('Y-m-d H:i:s', time());
		$contactid = $_SESSION['contactid'];
		$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
		while($row = mysqli_fetch_assoc($result)) {
			$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
		}
		$query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Vendor Price List', 'Export', '$update_log', '$today_date', '$name')";
		$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
	    echo '<script type="text/javascript"> alert("Successfully exported CSV file."); </script>';
}


// BEGIN EXPORT ALL FROM EDITOR
if(isset($_GET['exp'])) {
	if(!file_exists('download')) {
		mkdir('download', 0777, true);
	}
	if(!file_exists('exports')) {
		mkdir('exports', 0777, true);
	}

	$all_type = rtrim($type_url, ", ");
	$today_date = date('Y-m-d_h-i-s-a', time());
	$FileName = "exports/vpl_export_".$today_date.".csv";
	$file = fopen($FileName,"w");
	$sql = mysqli_query($dbc, 'SELECT inventoryid,vendorid,vpl_name,code,category,sub_category,part_no,display_website,product_name,cost,usd_cpu,commission_price,markup_perc,current_inventory,write_offs,min_max,status,note,description,comment,question,request,quote_description,size,weight,type,name,usd_invoice,shipping_rate,shipping_cash,exchange_rate,exchange_cash,location,id_number,operator,lsd,quantity,inv_variance,average_cost,asset,revenue,buying_units,selling_units,stocking_units,preferred_price,web_price,cdn_cpu,cogs_total,date_of_purchase,purchase_cost,sell_price,markup,freight_charge,min_bin,current_stock,final_retail_price,admin_price,wholesale_price,commercial_price,client_price,minimum_billable,estimated_hours,actual_hours,msrp,unit_price,unit_cost,rent_price,rental_days,rental_weeks,rental_months,rental_years,reminder_alert,daily,weekly,monthly,annually,total_days,total_hours,total_km,total_miles,deleted,purchase_order_price,sales_order_price,include_in_pos,include_in_so,include_in_po
 FROM `vendor_price_list`');
	$row = mysqli_fetch_assoc($sql);
	// Save headings alon
	$HeadingsArray=array();
	foreach($row as $name => $value){
		$HeadingsArray[]=$name;
	}
	fputcsv($file,$HeadingsArray);

	$sql = mysqli_query($dbc, 'SELECT inventoryid,vendorid,vpl_name,code,category,sub_category,part_no,display_website,product_name,cost,usd_cpu,commission_price,markup_perc,current_inventory,write_offs,min_max,status,note,description,comment,question,request,quote_description,size,weight,type,name,usd_invoice,shipping_rate,shipping_cash,exchange_rate,exchange_cash,location,id_number,operator,lsd,quantity,inv_variance,average_cost,asset,revenue,buying_units,selling_units,stocking_units,preferred_price,web_price,cdn_cpu,cogs_total,date_of_purchase,purchase_cost,sell_price,markup,freight_charge,min_bin,current_stock,final_retail_price,admin_price,wholesale_price,commercial_price,client_price,minimum_billable,estimated_hours,actual_hours,msrp,unit_price,unit_cost,rent_price,rental_days,rental_weeks,rental_months,rental_years,reminder_alert,daily,weekly,monthly,annually,total_days,total_hours,total_km,total_miles,deleted,purchase_order_price,sales_order_price,include_in_pos,include_in_so,include_in_po FROM vendor_price_list');

	// Save all records without headings
	while($row = mysqli_fetch_assoc($sql)){
		$valuesArray=array();
		foreach($row as $name => $value){
			$valuesArray[]=$value;
		}
		fputcsv($file,$valuesArray);
	}
	fclose($file);
	header("Location: $FileName");
	$update_log = 'All VPL items were exported.';
	$today_date = date('Y-m-d H:i:s', time());
	$contactid = $_SESSION['contactid'];
	$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid' AND deleted=0 AND `status`>0");
	while($row = mysqli_fetch_assoc($result)) {
		$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
	}
	$query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Vendor Price List', 'Export', '$update_log', '$today_date', '$name')";
	$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
}
// END EXPORT
?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#category").change(function() {
            if($( "#category option:selected" ).text() == 'Other') {
                    $( "#category_name" ).show();
            } else {
                $( "#category_name" ).hide();
            }
        });
	});
	$(document).on('change', 'select[name="vendorid"]', function() { filterOrderForms(); });
	$(document).on('change', 'select[name="vpl_name"]', function() { displayNewOrderForm(); });
	function filterOrderForms() {
		var vendorid = $('[name="vendorid"]').val();
		$('[name="vpl_name"] option').hide();
		$('[name="vpl_name"] option[value=NEW_VPL]').show();
		if(vendorid != '') {
			$('.order_form').show();
			$('[name="vpl_name"] option[data-vendorid='+vendorid+']').show();
			$('[name="vpl_name"]').trigger('change.select2');
		}
	}
	function displayNewOrderForm() {
		var vpl_name = $('[name="vpl_name"]').val();
		if(vpl_name == 'NEW_VPL') {
			$('[name="new_vpl_name"]').show();
		} else {
			$('[name="new_vpl_name"]').hide();
		}
	}
</script>
<?php checkAuthorised('vpl');
	$active_add = 'active_tab';
	$active_edit = '';
	$active_export = '';
	$active_log = '';
	$type_get = '';
	$title = '';
	if(isset($_GET['type'])) {
		$type_get = $_GET['type'];
		if($type_get == 'add' || $type_get == '') {
			$active_add = 'active_tab';
			$title = 'Add Multiple Products';
		} else if($type_get == 'edit') {
			$active_edit = 'active_tab';
			$active_add = '';
			$title = 'Edit Multiple Products';
		} else if($type_get == 'export') {
			$active_export = 'active_tab';
			$active_add = '';
			$title = 'Export Vendor Price List (VPL)';
		} else if($type_get == 'log') {
			$active_log = 'active_tab';
			$active_add = '';
			$title = 'History';
		}
	} else {
		$title = 'Add Multiple Products';
	} ?>


<div class="standard-body-title">
	<h3><?= $title ?></h3>
</div>
<div class="standard-body-content pad-10">
	<div class="pad-left gap-top double-gap-bottom"><a href="?vpl=1" class="btn config-btn">Back to Dashboard</a></div><?php

	echo "<div class='gap-left tab-container mobile-100-container'>";
		echo "<a href='?vpl=1&impexp=1&type=add'><button type='button' class='btn brand-btn mobile-100 mobile-block ".$active_add."' >Add Multiple</button></a>&nbsp;&nbsp;";
		echo "<a href='?vpl=1&impexp=1&type=edit'><button type='button' class='btn brand-btn mobile-block mobile-100 ".$active_edit."' >Edit Multiple</button></a>&nbsp;&nbsp;";
		echo "<a href='?vpl=1&impexp=1&type=export'><button type='button' class='btn brand-btn mobile-block mobile-100 ".$active_export."' >Export</button></a>&nbsp;&nbsp;";
		echo "<a href='?vpl=1&impexp=1&type=log'><button type='button' class='btn brand-btn mobile-block mobile-100 ".$active_log."' >History</button></a>&nbsp;&nbsp;";
	echo '</div>'; ?>

	<?php if($type_get == '' || $type_get == 'add') { ?>
	  <div class="row add">

	<form name="import" method="post" enctype="multipart/form-data">
					<div class="notice">Steps to Upload Multiple Items into the Vendor Price List (VPL) tile:<br><Br>
						<b>1.</b> Please download the following Excel(CSV) file to use as a template: <a href='Vendor_price_list.csv' style='color:white !important; text-decoration:underline !important;'>Vendor_price_list.csv</a>.<br><br>
						<b>2.</b> Fill in the rows (starting from row 2). Please note that each row you fill out will become a separate VPL item in the VPL tile.<br>
						<span style='color:pink;'><img src='../img/warning.png' style='width:25px;'> NOTE</span>: Do not change/move/delete any of the column titles in the first row.<br> <span style='color:lightgreen'><b>Hint</b>:</span> press CTRL+F on your keyboard to find the fields you would like to populate; this will help you locate them faster. <br><br>
						<b>3.</b> After you are done filling out your data, save the Excel (CSV) file, upload the CSV file below, and hit submit.<br><br>
						<b>4.</b> Please look for your newly added items in the VPL dashboard!<br><br>
						<div class="form-group">
							<label class="col-sm-4 control-label"><span class="popover-examples pad-5"><a data-toggle="tooltip" data-placement="top" title="Choose a Vendor to attach all imported items to. Leave blank to use vendorid from CSV."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Vendor:</label>
							<div class="col-sm-8">
								<select name="vendorid" class="chosen-select-deselect form-control">
									<option></option>
									<?php $vendors = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = 'Vendor' AND `deleted` = 0 AND `status` > 0"));
									foreach($vendors as $vendor) {
										if(!empty($vendor['full_name']) && $vendor['full_name'] != '-') {
											echo '<option value="'.$vendor['contactid'].'">'.$vendor['full_name'].'</option>';
										}
									} ?>
								</select>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label"><span class="popover-examples pad-5"><a data-toggle="tooltip" data-placement="top" title="Choose an Order Form to attach all imported items to. Leave blank to use vpl_name from CSV."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Order Form:</label>
							<div class="col-sm-8">
								<select name="vpl_name" class="chosen-select-deselect form-control">
									<option></option>
									<option value="NEW_VPL">New Order Form</option>
									<?php $order_forms = mysqli_query($dbc, "SELECT * FROM `vendor_price_list` WHERE IFNULL(`vpl_name`,'') != '' AND `vendorid` > 0 AND `deleted` = 0 GROUP BY CONCAT(`vendorid`,`vpl_name`)");
									while($row = mysqli_fetch_assoc($order_forms)) {
										echo '<option value="'.$row['vpl_name'].'" data-vendorid="'.$row['vendorid'].'" style="display:none;">'.$row['vpl_name'].'</option>';
									} ?>
								</select>
								<input type="text" name="new_vpl_name" class="form-control" value="" style="display:none;">
							</div>
						</div>
						<input class="form-control" type="file" name="file" /><br />
						<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
					</div>
					<div class="gap-right pull-right">
						<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="If you click this, your products will not be imported."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						<a href="?vpl=1" class="btn brand-btn">Back</a>
						<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Click this to import your products list."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						<input class="btn brand-btn" type="submit" name="submitty" value="Submit" />
						<div class="clearfix"></div>
					</div>
				</form>
		</div>
	<?php } else if ($type_get == 'edit') { ?>
		<div class="row edit">
			<form name="import" method="post" enctype="multipart/form-data">
				<div class="notice">Steps to Edit Multiple Items in the Vendor Price List (VPL) tile:<br><Br>
					<b>1.</b> Please download the following Excel (CSV) file, which will be the current list of all of your VPL items: <a href='?vpl=1&impexp=1&type=edit&exp=true' style='color:white; text-decoration:underline;'>Export VPL</a><br>
					<span style='color:lightgreen'><b>Hint:</b></span> if you would like to edit items from a specific category, export the Excel (CSV) file from this page: <a href='?vpl=1&impexp=1&type=export' target="_BLANK" style='color:white; text-decoration:underline;'>Export Specific VPL items</a>.<br><br>
					<b>2.</b> Make your desired changes inside of the Excel file.<br>
					<span style='color:pink;'><img src='../img/warning.png' style='width:25px;'> NOTE</span>: Do not change/move/delete any of the column titles in the first row. Also, do not change the data in the first column (<em>inventoryid</em>), or else the edits may not go through properly. <br><span style='color:lightgreen'><b>Hint:</b></span> press CTRL+F on your keyboard to find the fields you would like to populate; this will help you locate them faster.<br><br>
					<b>3.</b> After you are done editing the data, save your Excel (CSV) file, upload the CSV file below, and hit submit.<br><br>
					<b>4.</b> Please look for your edited items in the VPL dashboard!<br><br>
					<div class="form-group">
						<label class="col-sm-4 control-label"><span class="popover-examples pad-5"><a data-toggle="tooltip" data-placement="top" title="Choose a Vendor to attach all imported items to. Leave blank to use vendorid from CSV."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Vendor:</label>
						<div class="col-sm-8">
							<select name="vendorid" class="chosen-select-deselect form-control">
								<option></option>
								<?php $vendors = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = 'Vendor' AND `deleted` = 0 AND `status` > 0"));
								foreach($vendors as $vendor) {
									if(!empty($vendor['full_name']) && $vendor['full_name'] != '-') {
										echo '<option value="'.$vendor['contactid'].'">'.$vendor['full_name'].'</option>';
									}
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><span class="popover-examples pad-5"><a data-toggle="tooltip" data-placement="top" title="Choose an Order Form to attach all imported items to. Leave blank to use vpl_name from CSV."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Order Form:</label>
						<div class="col-sm-8">
							<select name="vpl_name" class="chosen-select-deselect form-control">
								<option></option>
								<option value="NEW_VPL">New Order Form</option>
								<?php $order_forms = mysqli_query($dbc, "SELECT * FROM `vendor_price_list` WHERE IFNULL(`vpl_name`,'') != '' AND `vendorid` > 0 AND `deleted` = 0 GROUP BY CONCAT(`vendorid`,`vpl_name`)");
								while($row = mysqli_fetch_assoc($order_forms)) {
									echo '<option value="'.$row['vpl_name'].'" data-vendorid="'.$row['vendorid'].'" style="display:none;">'.$row['vpl_name'].'</option>';
								} ?>
							</select>
							<input type="text" name="new_vpl_name" class="form-control" value="" style="display:none;">
						</div>
					</div>
					<input class="form-control" type="file" name="file" /><br />
					<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="gap-right pull-right">
					<a href="?vpl=1" class="btn brand-btn">Back</a>
					<input class="btn brand-btn pull-right" type="submit" name="submitty2" value="Submit" />
					<div class="clearfix"></div>
				</div>
			</form>
		</div>

	<?php }  else if ($type_get == 'export') { ?>
		<div class="row export">
			<form name="import" method="post" enctype="multipart/form-data">
				<div class="notice">
					<div class="form-group">
						<label class="col-sm-4 control-label"><span class="popover-examples pad-5"><a data-toggle="tooltip" data-placement="top" title="Choose a Vendor to export. Leave blank to export all."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Vendor:</label>
						<div class="col-sm-8">
							<select name="vendorid" class="chosen-select-deselect form-control">
								<option></option>
								<?php $vendors = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = 'Vendor' AND `deleted` = 0 AND `status` > 0"));
								foreach($vendors as $vendor) {
									if(!empty($vendor['full_name']) && $vendor['full_name'] != '-') {
										echo '<option value="'.$vendor['contactid'].'">'.$vendor['full_name'].'</option>';
									}
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><span class="popover-examples pad-5"><a data-toggle="tooltip" data-placement="top" title="Choose an Order Form to export. Leave blank to export all."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Order Form:</label>
						<div class="col-sm-8">
							<select name="vpl_name" class="chosen-select-deselect form-control">
								<option></option>
								<?php $order_forms = mysqli_query($dbc, "SELECT * FROM `vendor_price_list` WHERE IFNULL(`vpl_name`,'') != '' AND `vendorid` > 0 AND `deleted` = 0 GROUP BY CONCAT(`vendorid`,`vpl_name`)");
								while($row = mysqli_fetch_assoc($order_forms)) {
									echo '<option value="'.$row['vpl_name'].'" data-vendorid="'.$row['vendorid'].'" style="display:none;">'.$row['vpl_name'].'</option>';
								} ?>
							</select>
						</div>
					</div>
					<div class="form-group"><?php
						$sql = mysqli_query($dbc, 'SELECT * FROM vendor_price_list WHERE deleted = 0 GROUP BY category ORDER BY `category`');  ?>
						<label for="travel_task" class="col-sm-4 control-label">
							<span class="popover-examples pad-5" style='display:inline-block;'><a data-toggle="tooltip" data-placement="top" title="Select which category you would like to export, or select All Categories to export every VPL item that you have."><img src="../img/info.png" width="20"></a></span>Category:
						</label>
						<div class="col-sm-8">
							<select name="category_export" class="chosen-select-deselect form-control" width="380">
								<option value="3456780123456971230">All Categories</option><?php
								while($row = mysqli_fetch_assoc($sql)){
									echo '<option value="'.$row['category'].'">'.$row['category'].'</option>';
								} ?>
							</select>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>

				<div class="gap-right pull-right">
					<a href="?vpl=1" class="btn brand-btn">Back</a>
					<button class="btn brand-btn" type="submit" name="exporter" value="Export" />Submit</button>
				</div>

			</form>
		</div>
	<?php }  else if ($type_get == 'log') {
	    $query_check_credentials = "SELECT * FROM import_export_log WHERE deleted = 0 AND table_name = 'Vendor Price List' ORDER BY date_time DESC LIMIT 10000";
		 $gettotalrows = "SELECT * FROM import_export_log WHERE deleted = 0 AND table_name = 'Vendor Price List'";
	            $result = mysqli_query($dbc, $query_check_credentials);
				$xxres = mysqli_query($dbc, $gettotalrows);
	            $num_rows = mysqli_num_rows($result);
				  $num_rowst = mysqli_num_rows($xxres);
	            if($num_rows > 0) {
					echo "<br>Currently displaying the last $num_rows rows (out of a total of $num_rowst rows).<br><br>";
	                echo "<table class='table table-bordered '>";
	                echo "<tr class='hidden-xs hidden-sm'>";
	                        echo '<th>Type</th>';
	                        echo '<th>Description</th>';
	                        echo '<th>Date/Time</th>';
	                        echo '<th>Author</th>';
	                    echo "</tr>";
	            } else {
	                echo "<h2 class ='list_dashboard'>No Record Found.</h2>";
	            }

	            while($row = mysqli_fetch_array( $result ))
	            {
	                echo "<tr>";
							echo '<td data-title="Type">' . $row['type'] . '</td>';
	                        echo '<td data-title="Description">' . $row['description'] . '</td>';
							$time = substr($row['date_time'], strpos($row['date_time'], ' '));
							$time = date("g:i a", strtotime($time));
							$arr = explode(' ',trim($row['date_time']));
							echo '<td data-title="Date & Time">'.$arr[0].' at '.$time. '</td>';
							echo '<td data-title="Author">' . $row['contact'] . '</td>';
	                echo "</tr>";
	            }

	            echo '</table>'; ?>

				<a href="?vpl=1" class="btn brand-btn btn-lg ">Back</a>
				<!--<a href="#" class="btn brand-btn btn-lg" onclick="history.go(-1);return false;">Back</a>-->
			</div>
	<?php } ?>

</div>
