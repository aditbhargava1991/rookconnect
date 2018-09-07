<?php
/*
Add	Shipment
*/
include ('../include.php');
checkAuthorised('inventory');
error_reporting(0);

if (isset($_POST['submit'])) {

    $date_added = date('Y-m-d');
    $inventoryid =	$_POST['inventoryid'];
    $current_inventory =	$_POST['quantity'];

    $sell_price =	$_POST['sell_price'];
    $final_retail_price =	$_POST['final_retail_price'];
    $wholesale_price =	$_POST['wholesale_price'];
    $commercial_price =	$_POST['commercial_price'];
    $client_price =	$_POST['client_price'];
    $preferred_price =	$_POST['preferred_price'];
    $admin_price =	$_POST['admin_price'];
    $web_price =	$_POST['web_price'];
    $commission_price =	$_POST['commission_price'];
    $msrp =	$_POST['msrp'];
    $unit_price =	$_POST['unit_price'];
    $unit_cost =	$_POST['unit_cost'];
    $purchase_order_price =	$_POST['purchase_order_price'];
    $sales_order_price =	$_POST['sales_order_price'];

    $who_added = $_SESSION['contactid'];

    for($i=0; $i<count($_POST['inventoryid']); $i++) {
        $query_insert_inventory = "INSERT INTO `receive_shipment` (`inventoryid`, `quantity`, `sell_price`, `final_retail_price`, `wholesale_price`, `commercial_price`, `client_price`, `preferred_price`, `admin_price`, `web_price`, `commission_price`, `msrp`, `unit_price`, `unit_cost`, `purchase_order_price`, `sales_order_price`, `date_added`, `who_added`) VALUES	('$inventoryid[$i]', '$current_inventory[$i]', '$sell_price[$i]', '$final_retail_price[$i]', '$wholesale_price[$i]', '$commercial_price[$i]', '$client_price[$i]', '$preferred_price[$i]', '$admin_price[$i]', '$web_price[$i]', '$commission_price[$i]', '$msrp[$i]', '$unit_price[$i]', '$unit_cost[$i]', '$purchase_order_price[$i]', '$sales_order_price[$i]', '$date_added', '$who_added')";
        $result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);

        $get_inv = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM inventory WHERE inventoryid='$inventoryid[$i]'"));
        $final_ur = $get_inv['quantity']+$current_inventory[$i];

        $final_1 = max($sell_price[$i], $get_inv['sell_price']);
        $final_2 = max($final_retail_price[$i], $get_inv['final_retail_price']);
        $final_3 = max($wholesale_price[$i], $get_inv['wholesale_price']);
        $final_4 = max($commercial_price[$i], $get_inv['commercial_price']);
        $final_5 = max($client_price[$i], $get_inv['client_price']);
        $final_6 = max($preferred_price[$i], $get_inv['preferred_price']);
        $final_7 = max($admin_price[$i], $get_inv['admin_price']);
        $final_8 = max($web_price[$i], $get_inv['web_price']);
        $final_9 = max($commission_price[$i], $get_inv['commission_price']);
        $final_10 = max($msrp[$i], $get_inv['msrp']);
        $final_11 = max($unit_price[$i], $get_inv['unit_price']);
        $final_12 = max($unit_cost[$i], $get_inv['unit_cost']);
        $final_13 = max($purchase_order_price[$i], $get_inv['purchase_order_price']);
        $final_14 = max($sales_order_price[$i], $get_inv['sales_order_price']);
		$average_cost = (($get_inv['average_cost'] * $get_inv['quantity']) + ($unit_cost[$i] * $current_inventory[$i])) / $final_ur;

        $query_update_inventory = "UPDATE `inventory` SET `quantity` = '$final_ur', `sell_price` = '$final_1', `final_retail_price` = '$final_2', `wholesale_price` = '$final_3', `commercial_price` = '$final_4', `client_price` = '$final_5', `preferred_price` = '$final_6', `admin_price` = '$final_7', `web_price` = '$final_8', `commission_price` = '$final_9', `msrp` = '$final_10', `unit_price` = '$final_11', `unit_cost` = '$final_12', `purchase_order_price` = '$final_13', `sales_order_price` = '$final_14', `average_cost` = '$average_cost' WHERE `inventoryid` = '{$inventoryid[$i]}'";
		$query_log_inventory = "INSERT INTO `inventory_change_log` (`inventoryid`, `contactid`, `location_of_change`, `old_inventory`, `old_cost`, `changed_quantity`, `current_cost`, `new_inventory`, `new_cost`, `date_time`, `deleted`) VALUES ('{$inventoryid[$i]}', '$who_added', 'Inventory Shipment', '{$get_inv['quantity']}', '{$get_inv['average_cost']}', '$current_inventory[$i]', '$unit_cost[$i]', '$final_ur', '$average_cost', '$datetime', '0')";
        $result_update_inventory	= mysqli_query($dbc, $query_update_inventory) or die(mysqli_error($dbc));
		$result_log = mysqli_query($dbc, $query_log_inventory);
        $before_change = '';
        $history = "Inventory with id $inventoryid is been Updated. <br />";
		    add_update_history($dbc, 'inventory_history', $history, '', $before_change);

    }

    echo '<script type="text/javascript"> window.location.replace("receive_shipment.php"); </script>';

   // mysqli_close($dbc); //Close the DB Connection
}

?>
<script>
$(document).ready(function() {
    $("#form1").submit(function( event ) {
        var inventoryid = $("#inventoryid").val();
        var unitsrec = $("#unitsrec").val();

        if (inventoryid == '' || unitsrec == '') {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });

    var count = 1;

    $('.hide_additional_position').hide();
    $('#add_position_button').on( 'click', function () {
     if ($('.hide_additional_position').is(":hidden")) {
        $('.hide_additional_position').show();
     } else {
        //$('.equipment').show();
        var clone = $('.additional_position').clone();
        clone.find('.form-control').val('');
        clone.find('.final_retail_price').val('0');
        clone.find('.wholesale_price').val('0');
        clone.find('.commercial_price').val('0');
        clone.find('.invid').attr('id', 'inventoryid_'+count);

        clone.removeClass("additional_position");
        $('#add_here_new_position').append(clone);

		resetChosen($("#inventoryid_"+count));

        count++;
    }
        return false;
    });

});
</script>
</head>

<body>
<?php include_once ('../navigation.php');
?>
<div class="container">
  <div class="row">

        <h1>Add a New Shipment</h1>

		<div class="gap-top double-gap-bottom"><a href="receive_shipment.php" class="btn config-btn">Back to Dashboard</a></div>

		<form id="form1" name="form1" method="post"	action="add_receive_shipment.php" enctype="multipart/form-data" class="form-horizontal" role="form">
            <?php
            $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT receive_shipment FROM field_config_inventory WHERE tab='receive_shipment' AND accordion='receive_shipment'"));
            $inventory_config = ','.$get_field_config['receive_shipment'].',';
            ?>
            <div class="form-group clearfix">
                <?php if (strpos($inventory_config, ','."Inventory".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="width:30%;">Inventory</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Quantity".',') !== FALSE) { ?>
                <label class="col-sm-3 text-center" style="position:relative;width:10%">Add Quantity to Current Inventory</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Sell Price".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Sell Price</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Final Retail Price".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Final Retail Price</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Wholesale Price".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Wholesale Price</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Commercial Price".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Commercial Price</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Client Price".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Client Price</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Preferred Price".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Preferred Price</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Admin Price".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Admin Price</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Web Price".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Web Price</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Commission Price".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Commission Price</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."MSRP".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">MSRP</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Unit Price".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Unit Price</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Unit Cost".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Unit Cost</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Purchase Order Price".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%">Purchase Order Price</label>
                <?php } ?>
                <?php if (strpos($inventory_config, ','."Sales Order Price".',') !== FALSE) { ?>
                <label class="col-sm-1 text-center" style="position:relative;width:10%"><?= SALES_ORDER_NOUN ?> Price</label>
                <?php } ?>
            </div>

            <div class="additional_position">
                <div class="clearfix"></div>
                <div class="form-group clearfix" width="100%">

                    <?php if (strpos($inventory_config, ','."Inventory".',') !== FALSE) { ?>
                    <div class="col-sm-1 type"  style="width:30%; display:inline-block; position:relative;" id="category_0">
                      <select id="inventoryid_0" name="inventoryid[]" class="chosen-select-deselect form-control invid" width="380">
                      <option value=''></option>
                          <?php
                            $result = mysqli_query($dbc, "SELECT inventoryid, category, name FROM inventory WHERE deleted=0 order by category, name");
                            while($row = mysqli_fetch_assoc($result)) {
                                echo "<option value = '".$row['inventoryid']."'>".$row['category'].' : '.$row['name']."</option>";
                            }
                          ?>
                      </select>
                    </div>
                    <?php } ?>

                    <?php if (strpos($inventory_config, ','."Quantity".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="quantity[]" id="unitsrec" type="text" class="form-control" />
                    </div>
                    <?php } ?>


                    <?php if (strpos($inventory_config, ','."Sell Price".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="sell_price[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."Final Retail Price".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="final_retail_price[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."Wholesale Price".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="wholesale_price[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."Commercial Price".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="commercial_price[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."Client Price".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="client_price[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."Preferred Price".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="preferred_price[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."Admin Price".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="admin_price[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."Web Price".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="web_price[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."Commission Price".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="commission_price[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."MSRP".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="msrp[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."Unit Price".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="unit_price[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."Unit Cost".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="unit_cost[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."Purchase Order Price".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="purchase_order_price[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>
                    <?php if (strpos($inventory_config, ','."Sales Order Price".',') !== FALSE) { ?>
                    <div class="col-sm-1" style="width:10%; position:relative; display:inline-block;">
                        <input name="sales_order_price[]" type="text" value="0" class="form-control" />
                    </div>
                    <?php } ?>

            </div>

            </div>

            <div id="add_here_new_position"></div>

            <div class="col-sm-8 col-sm-offset-4 triple-gap-bottom">
                <button id="add_position_button" class="btn brand-btn mobile-block">Add</button>
            </div>

    	<div class="form-group">
			<p><span class="brand-color"><em>Required Fields *</em></span></p>
		</div>

		  <div class="form-group">
			<div class="col-sm-6">
				<a href="receive_shipment.php" class="btn brand-btn btn-lg">Back</a>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
			</div>
		  </div>



		</form>

	</div>
  </div>

<?php include ('../footer.php'); ?>
