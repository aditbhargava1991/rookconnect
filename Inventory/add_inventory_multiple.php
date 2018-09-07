<?php
/*
Add	Inventory
*/
include ('../include.php');
error_reporting(0);
include('field_list.php');
// ADD INVENTORY
if(isset($_POST["submitty"]))
{
	$i = 0;
	$file = htmlspecialchars($_FILES['file']['tmp_name'], ENT_QUOTES);
	$handle = fopen($file, "r");
	$headers = fgetcsv($handle, 0, ',');
	$c = 0;
	while(($row = fgetcsv($handle, 1000, ",")) !== false)
	{
		$values = [];
		foreach($headers as $i => $col) {
			$values[filter_var($col,FILTER_SANITIZE_STRING)] = filter_var(htmlentities($row[$i],FILTER_SANITIZE_STRING));
		}
		mysqli_query($dbc, "INSERT INTO `inventory` VALUES ()");
		$inventoryid = mysqli_insert_id($dbc);

		$updates = [];
		foreach($values as $field => $value) {
			if($field != '' && $field != 'inventoryid') {
				$value = filter_var($value,FILTER_SANITIZE_STRING);
				$updates[] = "`$field`='$value'";
			}
		}
		$updates = implode(',',$updates);
		$sql = "UPDATE `inventory` SET $updates WHERE `inventoryid` = '$inventoryid'";//echo '<!--'.$sql.'-->';
		mysqli_query($dbc, $sql);
		$before_change = '';
		$history = "Inventory with id $inventoryid is been Updated. <br />";
		add_update_history($dbc, 'inventory_history', $history, '', $before_change);
		$update_log = 'Inventory Added (ID: '.$inventoryid.')';
		$today_date = date('Y-m-d H:i:s', time());
		$contactid = $_SESSION['contactid'];
		$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
		while($row = mysqli_fetch_assoc($result)) {
			$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
		}
		$query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Inventory', 'Add', '$update_log', '$today_date', '$name')";
		$result_insert_customer = mysqli_query($dbc, $query_insert_customer);

		if($values['include_in_product'] == '1') {
			mysqli_query($dbc, "INSERT INTO `products` VALUES ()");
			$productid = mysqli_insert_id($dbc);
			$sql = "UPDATE `products` SET $updates WHERE `productid` = '$productid'";
			mysqli_query($dbc, $sql);
		}
	}

	echo '<script type="text/javascript"> alert("Successfully imported CSV file. Please check the Inventory dashboard to view your newly added items."); </script>';
}

// END ADD INVENTORY
// BEGIN EDIT INVENTORY
if(isset($_POST["submitty2"]))
{
	$i = 0;
	$file = htmlspecialchars($_FILES['file']['tmp_name'], ENT_QUOTES);
	$handle = fopen($file, "r");
	$headers = fgetcsv($handle, 0, ',');
	$c = 0;
	while(($row = fgetcsv($handle, 1000, ",")) !== false)
	{
		$values = [];
		foreach($headers as $i => $col) {
			$values[filter_var($col,FILTER_SANITIZE_STRING)] = filter_var(htmlentities($row[$i],FILTER_SANITIZE_STRING));
		}
		$inventoryid = $values['inventoryid'];

		if($inventoryid > 0) {
			$original = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `products` WHERE `inventoryid` = '$inventoryid'"));
			$updates = [];
			foreach($values as $field => $value) {
				if($field != '' && $field != 'inventoryid') {
					$updates[] = "`$field`='$value'";
					if($value != $original[$field] && !empty($value) && !empty($original[$field])) {
						$update_log = $field.' was changed from "'.$original[$field].'" to "'.$value.'" where inventory ID = '.$inventoryid;
						$today_date = date('Y-m-d H:i:s', time());
						$contactid = $_SESSION['contactid'];
						$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
						while($row = mysqli_fetch_assoc($result)) {
							$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
						}
						$query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Inventory', 'Edit', '$update_log', '$today_date', '$name')";
						$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
					}
				}
			}
			$updates = implode(',',$updates);
			$sql = "UPDATE `inventory` SET $updates WHERE `inventoryid` = '$inventoryid'";
			mysqli_query($dbc, $sql);
			$before_change = '';
			$history = "Inventory with id $inventoryid is been Updated. <br />";
			add_update_history($dbc, 'inventory_history', $history, '', $before_change);
		}
	}
	echo '<script type="text/javascript"> alert("Successfully imported CSV file. Please check the Inventory dashboard to view your freshly edited items."); </script>';
}
// END EDIT INVENTORY
// BEGIN EXPORT FROM EXPORT PAGE

if(isset($_POST["exporter"]))
{
	$category = $_POST['category_export'];
	$template = $_POST['template'];
	$today_date = date('Y-m-d_h-i-s-a', time());
	$FileName = "exports/inventory_export_".$today_date.".csv";
	$file = fopen($FileName, "w");
	$HeadingsArray = ['inventoryid'];
	$query_fields = '`inventoryid`,';

	if(!empty($template)) {
	    $query_template = "SELECT GROUP_CONCAT(`heading_name`) as field_list FROM `inventory_templates_headings` WHERE `template_id` = '$template' AND `deleted` = 0 ORDER BY `sort_order` ASC";
	    $result_template = mysqli_fetch_assoc(mysqli_query($dbc, $query_template));
	    $fields_config = $result_template['field_list'];
	} else {
		$fields_config = '';
	}

	foreach($field_list as $key => $field) {
		if(strpos($key, '**NOCSV**') === FALSE) {
			$key = trim($key,"#");
			if(strpos(','.$fields_config.',', ','.$key.',') || empty($fields_config)) {
				$HeadingsArray[] = $key;
				$query_fields .= '`'.$key.'`,';
			}
		}
	}
	$query_fields = rtrim($query_fields, ',');
	fputcsv($file,$HeadingsArray);

	if($category == '3456780123456971230') {
		$sql = mysqli_query($dbc, 'SELECT '.$query_fields.' FROM `inventory` WHERE `deleted` = 0 ORDER BY `category`');
	} else {
		$sql = mysqli_query($dbc, 'SELECT '.$query_fields.' FROM `inventory` WHERE `deleted` = 0 AND category = "'.$category.'"');
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
		$update_log = 'All inventory items were exported.';
	} else {
		$update_log = 'All inventory under the '.$category.' category was exported.';
	}

	$today_date = date('Y-m-d H:i:s', time());
	$contactid = $_SESSION['contactid'];
	$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
	while($row = mysqli_fetch_assoc($result)) {
		$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
	}
	$query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Inventory', 'Export', '$update_log', '$today_date', '$name')";
	$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
    echo '<script type="text/javascript"> alert("Successfully exported CSV file."); </script>';
}

// END EXPORT FROM EXPORT PAGE

// BEGIN EXPORT PDF
if(isset($_POST['exportpdf'])) {
	include('inventory_pdf.php');
}
// END EXPORT PDF

// BEGIN EXPORT ALL FROM EDITOR
if(isset($_GET['exp'])) {
	$all_type = rtrim($type_url, ", ");
	$today_date = date('Y-m-d_h-i-s-a', time());
	$FileName = "exports/inventory_export_".$today_date.".csv";
	$file = fopen($FileName,"w");
	$sql = mysqli_query($dbc, 'SELECT inventoryid,code,category,sub_category,part_no,display_website,product_name,cost,usd_cpu,commission_price,markup_perc,current_inventory,write_offs,min_max,status,note,description,comment,question,request,quote_description,vendorid,size,weight,type,name,usd_invoice,shipping_rate,shipping_cash,exchange_rate,exchange_cash,location,id_number,operator,lsd,quantity,inv_variance,average_cost,asset,revenue,buying_units,selling_units,stocking_units,preferred_price,web_price,drum_unit_cost,drum_unit_price,tote_unit_cost,tote_unit_price,cdn_cpu,cogs_total,date_of_purchase,purchase_cost,sell_price,markup,freight_charge,min_bin,current_stock,final_retail_price,admin_price,wholesale_price,commercial_price,client_price,minimum_billable,estimated_hours,actual_hours,msrp,unit_price,unit_cost,rent_price,rental_days,rental_weeks,rental_months,rental_years,reminder_alert,daily,weekly,monthly,annually,total_days,total_hours,total_km,total_miles,bill_of_material,deleted,purchase_order_price,sales_order_price,include_in_pos,include_in_so,include_in_po,fee,productid,hourly_rate,include_in_product,product_code,ticket_description,invoice_description,heading,product_type FROM inventory');
	$row = mysqli_fetch_assoc($sql);
	// Save headings alon
	$HeadingsArray=array();
	foreach($row as $name => $value){
		$HeadingsArray[]=$name;
	}
	fputcsv($file,$HeadingsArray);
	$sql = mysqli_query($dbc, 'SELECT inventoryid,code,category,sub_category,part_no,display_website,product_name,cost,usd_cpu,commission_price,markup_perc,current_inventory,write_offs,min_max,status,note,description,comment,question,request,quote_description,vendorid,size,weight,type,name,usd_invoice,shipping_rate,shipping_cash,exchange_rate,exchange_cash,location,id_number,operator,lsd,quantity,inv_variance,average_cost,asset,revenue,buying_units,selling_units,stocking_units,preferred_price,web_price,drum_unit_cost,drum_unit_price,tote_unit_cost,tote_unit_price,cdn_cpu,cogs_total,date_of_purchase,purchase_cost,sell_price,markup,freight_charge,min_bin,current_stock,final_retail_price,admin_price,wholesale_price,commercial_price,client_price,minimum_billable,estimated_hours,actual_hours,msrp,unit_price,unit_cost,rent_price,rental_days,rental_weeks,rental_months,rental_years,reminder_alert,daily,weekly,monthly,annually,total_days,total_hours,total_km,total_miles,bill_of_material,deleted,purchase_order_price,sales_order_price,include_in_pos,include_in_so,include_in_po,fee,productid,hourly_rate,include_in_product,product_code,ticket_description,invoice_description,heading,product_type FROM inventory');



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
	$update_log = 'All inventory items were exported.';
	$today_date = date('Y-m-d H:i:s', time());
	$contactid = $_SESSION['contactid'];
	$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
	while($row = mysqli_fetch_assoc($result)) {
		$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
	}
	$query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Inventory', 'Export', '$update_log', '$today_date', '$name')";
	$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
}
// END EXPORT
?>
<script type="text/javascript" src="inventory.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $('#mobile_tabs .panel-heading').click(loadPanel);
});
function loadPanel() {
    $('#mobile_tabs .panel-heading:not(.higher_level_heading)').closest('.panel').find('.panel-body').html('Loading...');
    if(!$(this).hasClass('higher_level_heading')) {
	    var panel = $(this).closest('.panel').find('.panel-body');
	    panel.html('Loading...');
	    $.ajax({
	        url: panel.data('file'),
	        method: 'POST',
	        response: 'html',
	        success: function(response) {
	            panel.html(response);
	        }
	    });
	}
}
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('inventory');
?>
<div class="container" id="inventory_div">
<?php
	$active_add = 'active';
	$active_edit = '';
	$active_export = '';
	$active_log = '';
	$type_get = '';
	$title = '';

	if(isset($_GET['type'])) {
		$type_get = $_GET['type'];
		if($type_get == 'add' || $type_get == '') {
			$active_add = 'active';
			$title = 'Add Multiple Products';
		} else if($type_get == 'edit') {
			$active_edit = 'active';
			$active_add = '';
			$title = 'Edit Multiple Products';
		} else if($type_get == 'export') {
			$active_export = 'active';
			$active_add = '';
			$title = 'Export Inventory';
		} else if($type_get == 'exportpdf') {
			$active_exportpdf = 'active';
			$active_add = '';
			$title = 'Export PDF';
		} else if($type_get == 'log') {
			$active_log = 'active';
			$active_add = '';
			$title = 'History';
		}
	} else {
		$title = 'Add Multiple Products';
	} ?>

	<div class="row hide_on_iframe">
		<div class="main-screen">
			<div class="tile-header standard-header">
				<?php include('../Inventory/tile_header.php'); ?>
			</div>

			<div class="tile-container" style="height: 100%;">

				<div class="show-on-mob panel-group block-panels col-xs-12 form-horizontal" id="mobile_tabs">
					<div class="panel panel-default" style="background: white;">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_add">
									Add Multiple<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_add" class="panel-collapse collapse">
							<div class="panel-body" data-file="add_inventory_multiple_inc.php?type=add">
								Loading...
							</div>
						</div>
					</div>
					<div class="panel panel-default" style="background: white;">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_edit">
									Edit Multiple<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_edit" class="panel-collapse collapse">
							<div class="panel-body" data-file="add_inventory_multiple_inc.php?type=edit">
								Loading...
							</div>
						</div>
					</div>
					<div class="panel panel-default" style="background: white;">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_export">
									Export<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_export" class="panel-collapse collapse">
							<div class="panel-body" data-file="add_inventory_multiple_inc.php?type=export">
								Loading...
							</div>
						</div>
					</div>
					<div class="panel panel-default" style="background: white;">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_exportpdf">
									Export PDF<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_exportpdf" class="panel-collapse collapse">
							<div class="panel-body" data-file="add_inventory_multiple_inc.php?type=exportpdf">
								Loading...
							</div>
						</div>
					</div>
					<div class="panel panel-default" style="background: white;">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#mobile_tabs" href="#collapse_log">
									History<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_log" class="panel-collapse collapse">
							<div class="panel-body" data-file="add_inventory_multiple_inc.php?type=log">
								Loading...
							</div>
						</div>
					</div>
				</div>

	            <div class="standard-collapsible tile-sidebar set-section-height hide-titles-mob">
	            	<ul class="sidebar">
	            		<a href="add_inventory_multiple.php?type=add"><li class="<?= $active_add ?>">Add Multiple</li></a>
	            		<a href="add_inventory_multiple.php?type=edit"><li class="<?= $active_edit ?>">Edit Multiple</li></a>
	            		<a href="add_inventory_multiple.php?type=export"><li class="<?= $active_export ?>">Export</li></a>
	            		<a href="add_inventory_multiple.php?type=exportpdf"><li class="<?= $active_exportpdf ?>">Export PDF</li></a>
	            		<a href="add_inventory_multiple.php?type=log"><li class="<?= $active_log ?>">History</li></a>
	            	</ul>
	            </div>

	            <div class="scale-to-fill has-main-screen tile-content hide-titles-mob">
					<div class="main-screen standard-body">
						<div class="standard-body-title"><h3><?= $title ?></h3></div>
						<div class="standard-body-content pad-left pad-right">
							<?php include('../Inventory/add_inventory_multiple_inc.php'); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include ('../footer.php'); ?>
