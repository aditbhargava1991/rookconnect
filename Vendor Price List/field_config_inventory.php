<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('vpl');

error_reporting(0);

if (isset($_POST['add_tab'])) {
    $inventory_tabs = filter_var($_POST['inventory_tabs'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='vpl_tabs'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$inventory_tabs' WHERE name='vpl_tabs'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('vpl_tabs', '$inventory_tabs')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    echo '<script type="text/javascript"> window.location.replace("field_config_inventory.php?type=tab"); </script>';
}

if (isset($_POST['inv_dashboard'])) {
    $tab_dashboard = filter_var($_POST['tab_dashboard'],FILTER_SANITIZE_STRING);
    $inventory_dashboard = implode(',',$_POST['inventory_dashboard']);
    //if (strpos(','.$inventory_dashboard.',',','.'Category'.',') === false) {
    //    $inventory_dashboard = 'Category,'.$inventory_dashboard;
    //}

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_vpl WHERE tab='$tab_dashboard' AND accordion IS NULL"));
    if($get_field_config['configinvid'] > 0) {
        $query_update_employee = "UPDATE `field_config_vpl` SET inventory_dashboard = '$inventory_dashboard' WHERE tab='$tab_dashboard'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config_vpl` (`tab`, `inventory_dashboard`) VALUES ('$tab_dashboard', '$inventory_dashboard')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    echo '<script type="text/javascript"> window.location.replace("field_config_inventory.php?type=dashboard&tab='.$tab_dashboard.'"); </script>';
}

if (isset($_POST['inv_field'])) {
    $tab_field = filter_var($_POST['tab_field'],FILTER_SANITIZE_STRING);
    $accordion = filter_var($_POST['accordion'],FILTER_SANITIZE_STRING);
    $inventory = implode(',',$_POST['inventory']);
    $order = filter_var($_POST['order'],FILTER_SANITIZE_STRING);

    //if (strpos(','.$inventory.',',','.'Category'.',') === false) {
    //    $inventory = 'Category,'.$inventory;
    //}

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_vpl WHERE tab='$tab_field' AND accordion='$accordion'"));
    if($get_field_config['configinvid'] > 0) {
        $query_update_employee = "UPDATE `field_config_vpl` SET inventory = '$inventory', `order` = '$order' WHERE tab='$tab_field' AND accordion='$accordion'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config_vpl` (`tab`, `accordion`, `inventory`, `order`) VALUES ('$tab_field', '$accordion', '$inventory', '$order')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    echo '<script type="text/javascript"> window.location.replace("field_config_inventory.php?type=field&tab='.$tab_field.'&accr='.$accordion.'"); </script>';
}

if (isset($_POST['general'])) {
    $inventory_minbin_email = filter_var($_POST['inventory_minbin_email'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='vpl_minbin_email'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$inventory_minbin_email' WHERE name='vpl_minbin_email'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('vpl_minbin_email', '$inventory_minbin_email')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
	//  Tile Title
    $pos_tile_titler = filter_var($_POST['pos_tile_titler'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='vpl_tile_titler'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$pos_tile_titler' WHERE name='vpl_tile_titler'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('vpl_tile_titler', '$pos_tile_titler')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //  Tile Title
 echo '<script type="text/javascript"> window.location.replace("field_config_inventory.php?type=general"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
	$("#tab_dashboard").change(function() {
        window.location = 'field_config_inventory.php?type=dashboard&tab='+this.value;
	});
	$("#tab_field").change(function() {
        window.location = 'field_config_inventory.php?type=field&tab='+this.value;
	});

	$("#acc").change(function() {
        var tabs = $("#tab_field").val();
        window.location = 'field_config_inventory.php?type=field&tab='+tabs+'&accr='+this.value;
	});
	$('input.show_category_dropdown').on('change', function() {
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "inventory_ajax_all.php?fill=show_category_dropdown&value="+value,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});
	$('input.show_impexp_vpl').on('change', function() {
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "inventory_ajax_all.php?fill=show_impexp_vpl&value="+value,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});

});
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1><?php if(get_tile_title_vpl($dbc) == '' || get_tile_title_vpl($dbc) == NULL ) { $poser = "Vendor Price List"; } else { $poser = get_tile_title_vpl($dbc); } ; echo $poser; ?></h1>
<div class="gap-top double-gap-bottom"><a href="inventory.php?category=All" class="btn config-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<form id="form1" name="form1" method="post"	action="field_config_inventory.php" enctype="multipart/form-data" class="form-horizontal" role="form">

<div class="panel-group" id="accordion2">

    <?php
    $invtype = $_GET['tab'];
    $accr = $_GET['accr'];
    $type = $_GET['type'];

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventory FROM field_config_vpl WHERE tab='$invtype' AND accordion='$accr'"));
    $inventory_config = ','.$get_field_config['inventory'].',';

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventory_dashboard FROM field_config_vpl WHERE tab='$invtype' AND accordion IS NULL"));
    $inventory_dashboard_config = ','.$get_field_config['inventory_dashboard'].',';

    $get_field_order = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(`order` SEPARATOR ',') AS all_order FROM field_config_vpl WHERE tab='$invtype'"));

    $active_tab = '';
    $active_field = '';
    $active_dashboard = '';
    $active_general = '';
	$impexp = '';
    if($_GET['type'] == 'tab') {
        $active_tab = 'active_tab';
    }
    if($_GET['type'] == 'field') {
        $active_field = 'active_tab';
    }
    if($_GET['type'] == 'dashboard') {
        $active_dashboard = 'active_tab';
    }
    if($_GET['type'] == 'general') {
        $active_general = 'active_tab';
    }
	if($_GET['type'] == 'impexp') {
        $impexp = 'active_tab';
    }
	?>

    <div class="tab-container mobile-100-container">
		<div class="pull-left tab">
			<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click to add your own Tabs."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="field_config_inventory.php?type=tab"><button type="button" class="btn brand-btn mobile-block <?= $active_tab; ?>">Tabs</button></a>
		</div>
		<div class="pull-left tab">
			<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click to add desired fields."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="field_config_inventory.php?type=field"><button type="button" class="btn brand-btn mobile-block <?= $active_field; ?>">Fields</button></a>
		</div>
		<div class="pull-left tab">
			<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click to add desired fields you would like to appear on you VPL dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="field_config_inventory.php?type=dashboard"><button type="button" class="btn brand-btn mobile-block <?= $active_dashboard; ?>">Dashboard</button></a>
		</div>
		<div class="pull-left tab">
			<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="More options here."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="field_config_inventory.php?type=general"><button type="button" class="btn brand-btn mobile-block <?= $active_general; ?>">General</button></a>
		</div>
		<div class="pull-left tab">
			<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Create new order lists here."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="field_config_order_lists.php"><button type="button" class="btn brand-btn mobile-block">Order Lists</button></a>
		</div>
		<div class="pull-left tab">
			<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here to enable/disable the Import/Export functionality."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="field_config_inventory.php?type=impexp"><button type="button" class="btn brand-btn mobile-block <?= $impexp; ?>">Import/Export</button></a>
		</div>
		<div class="clearfix"></div>
    </div>

	<?php
	if($_GET['type'] == 'tab') {
        ?>
        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="These tabs sort your VPL items by Category, so please make sure the tab names match your VPL items's category names. Also, please make sure you do not place any spaces beside the commas."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Add Tabs Separated By a Comma:</label>
            <div class="col-sm-8">
              <input name="inventory_tabs" type="text" value="<?php echo get_config($dbc, 'vpl_tabs'); ?>" class="form-control"/>
            </div>
        </div>
		<div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Instead of tabs, have a drop-down menu that will sort your VPL items by their respective categories."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Or Use a Drop-Down Menu:</label>
            <div class="col-sm-8">
			<?php
			$checked = '';
			$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_category_dropdown_vpl'"));
			if($get_config['configid'] > 0) {
				$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_category_dropdown_vpl'"));
				if($get_config['value'] == '1') {
					$checked = 'checked';
				}
			}
			?>
              <input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?>  name='' class='show_category_dropdown' value='1'>
            </div>
        </div>

        <div class="form-group double-gap-top">
            <div class="col-sm-6">
				<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="If you click this, your settings will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="inventory.php?category=All" class="btn config-btn btn-lg">Back</a>
				<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
            </div>
            <div class="col-sm-6">
                <button	type="submit" name="add_tab" value="add_tab" class="btn config-btn btn-lg pull-right">Submit</button>
				<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save your settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div>
        </div>

		<div class="clearfix"></div>
    <?php }

    if($_GET['type'] == 'field') {
        ?>
        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label">Tabs:</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Vendor..." id="tab_field" name="tab_field" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                    $tabs = get_config($dbc, 'vpl_tabs');
                    $each_tab = explode(',', $tabs);
                    foreach ($each_tab as $cat_tab) {
                        if ($invtype == $cat_tab) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
                    }
                  ?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label">Accordion:</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Vendor..." id="acc" name="accordion" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <option <?php if ($accr == "Description") { echo " selected"; } ?> value="Description"><?php echo get_field_config_vpl($dbc, 'Description', 'order', $invtype); ?> : Description</option>
                  <option <?php if ($accr == "Unique Identifier") { echo " selected"; } ?> value="Unique Identifier"><?php echo get_field_config_vpl($dbc, 'Unique Identifier', 'order', $invtype); ?> : Unique Identifier</option>
                  <option <?php if ($accr == "Product Cost") { echo " selected"; } ?> value="Product Cost"><?php echo get_field_config_vpl($dbc, 'Product Cost', 'order', $invtype); ?> : Product Cost</option>
                  <option <?php if ($accr == "Purchase Info") { echo " selected"; } ?> value="Purchase Info"><?php echo get_field_config_vpl($dbc, 'Purchase Info', 'order', $invtype); ?> : Purchase Info</option>
                  <option <?php if ($accr == "Shipping Receiving") { echo " selected"; } ?> value="Shipping Receiving"><?php echo get_field_config_vpl($dbc, 'Shipping Receiving', 'order', $invtype); ?> : Shipping Receiving</option>
                  <option <?php if ($accr == "Pricing") { echo " selected"; } ?> value="Pricing"><?php echo get_field_config_vpl($dbc, 'Pricing', 'order', $invtype); ?> : Pricing</option>
                  <option <?php if ($accr == "Markup") { echo " selected"; } ?> value="Markup"><?php echo get_field_config_vpl($dbc, 'Markup', 'order', $invtype); ?> : Markup</option>
                  <option <?php if ($accr == "Stock") { echo " selected"; } ?> value="Stock"><?php echo get_field_config_vpl($dbc, 'Stock', 'order', $invtype); ?> : Stock</option>
                  <option <?php if ($accr == "Location") { echo " selected"; } ?> value="Location"><?php echo get_field_config_vpl($dbc, 'Location', 'order', $invtype); ?> : Location</option>
                  <option <?php if ($accr == "Dimensions") { echo " selected"; } ?> value="Dimensions"><?php echo get_field_config_vpl($dbc, 'Dimensions', 'order', $invtype); ?> : Dimensions</option>
                  <option <?php if ($accr == "Alerts") { echo " selected"; } ?> value="Alerts"><?php echo get_field_config_vpl($dbc, 'Alerts', 'order', $invtype); ?> : Alerts</option>
                  <option <?php if ($accr == "Time Allocation") { echo " selected"; } ?> value="Time Allocation"><?php echo get_field_config_vpl($dbc, 'Time Allocation', 'order', $invtype); ?> : Time Allocation</option>
                  <option <?php if ($accr == "Admin Fees") { echo " selected"; } ?> value="Admin Fees"><?php echo get_field_config_vpl($dbc, 'Admin Fees', 'order', $invtype); ?> : Admin Fees</option>
                  <option <?php if ($accr == "Quote") { echo " selected"; } ?> value="Quote"><?php echo get_field_config_vpl($dbc, 'Quote', 'order', $invtype); ?> : Quote</option>
                  <option <?php if ($accr == "Status") { echo " selected"; } ?> value="Status"><?php echo get_field_config_vpl($dbc, 'Status', 'order', $invtype); ?> : Status</option>
                  <option <?php if ($accr == "Display On Website") { echo " selected"; } ?> value="Display On Website"><?php echo get_field_config_vpl($dbc, 'Display On Website', 'order', $invtype); ?> : Display On Website</option>
                  <option <?php if ($accr == "General") { echo " selected"; } ?> value="General"><?php echo get_field_config_vpl($dbc, 'General', 'order', $invtype); ?> : General</option>
                  <option <?php if ($accr == "Rental") { echo " selected"; } ?> value="Rental"><?php echo get_field_config_vpl($dbc, 'Rental', 'order', $invtype); ?> : Rental</option>
                  <option <?php if ($accr == "Day/Week/Month/Year") { echo " selected"; } ?> value="Day/Week/Month/Year"><?php echo get_field_config_vpl($dbc, 'Day/Week/Month/Year', 'order', $invtype); ?> : Day/Week/Month/Year</option>
                  <option <?php if ($accr == "Vehicle") { echo " selected"; } ?> value="Vehicle"><?php echo get_field_config_vpl($dbc, 'Vehicle', 'order', $invtype); ?> : Vehicle</option>
                  <option <?php if ($accr == "Bill of Material") { echo " selected"; } ?> value="Bill of Material"><?php echo get_field_config_vpl($dbc, 'Bill of Material', 'order', $invtype); ?> : Bill of Material</option>
                </select>
                <select data-placeholder="Choose a order..." name="order" class="chosen-select-deselect form-control" width="380">
                    <option value=""></option>
                    <?php
                    for($m=1;$m<=30;$m++) { ?>
                        <option <?php if (get_field_config_vpl($dbc, $accr, 'order', $invtype) == $m) { echo  'selected="selected"'; } ?>  <?php if (strpos(','.$get_field_order['all_order'].',', ','.$m.',') !== FALSE) { echo " disabled"; } ?> value="<?php echo $m;?>"><?php echo $m;?></option>
                    <?php }
                    ?>
                </select>
            </div>
        </div>

        <h3>Fields</h3>
        <div class="panel-group" id="accordion2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_1" >
                            Description<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_1" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Description
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Category
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Subcategory".',') !== FALSE) { echo " checked"; } ?> value="Subcategory" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Subcategory
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Name".',') !== FALSE) { echo " checked"; } ?> value="Name" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Name
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Product Name".',') !== FALSE) { echo " checked"; } ?> value="Product Name" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Product Name
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Type
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Color".',') !== FALSE) { echo " checked"; } ?> value="Color" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Color

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_2" >
                            Unique Identifier<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_2" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Code".',') !== FALSE) { echo " checked"; } ?> value="Code" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Code
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."ID #".',') !== FALSE) { echo " checked"; } ?> value="ID #" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;ID #
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Item SKU".',') !== FALSE) { echo " checked"; } ?> value="Item SKU" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Item SKU
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Part #".',') !== FALSE) { echo " checked"; } ?> value="Part #" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Part #
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_3" >
                            Product Cost<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_3" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Cost
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."CDN Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="CDN Cost Per Unit" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;CDN Cost Per Unit
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."USD Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="USD Cost Per Unit" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;USD Cost Per Unit
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."COGS".',') !== FALSE) { echo " checked"; } ?> value="COGS" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;COGS GL Code
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Average Cost".',') !== FALSE) { echo " checked"; } ?> value="Average Cost" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Average Cost
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."USD Invoice".',') !== FALSE) { echo " checked"; } ?> value="USD Invoice" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;USD Invoice

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_4" >
                            Purchase Info<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_4" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Vendor".',') !== FALSE) { echo " checked"; } ?> value="Vendor" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Vendor
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Purchase Cost".',') !== FALSE) { echo " checked"; } ?> value="Purchase Cost" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Purchase Cost
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Date Of Purchase".',') !== FALSE) { echo " checked"; } ?> value="Date Of Purchase" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Date Of Purchase

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_5" >
                            Shipping & Receiving<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_5" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Shipping Rate".',') !== FALSE) { echo " checked"; } ?> value="Shipping Rate" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Shipping Rate
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Shipping Cash".',') !== FALSE) { echo " checked"; } ?> value="Shipping Cash" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Shipping Cash
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Freight Charge".',') !== FALSE) { echo " checked"; } ?> value="Freight Charge" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Freight Charge
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Exchange Rate".',') !== FALSE) { echo " checked"; } ?> value="Exchange Rate" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Exchange Rate
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Exchange $".',') !== FALSE) { echo " checked"; } ?> value="Exchange $" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Exchange $

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_6" >
                            Pricing<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_6" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Sell Price".',') !== FALSE) { echo " checked"; } ?> value="Sell Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Sell Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Final Retail Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Wholesale Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Commercial Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Client Price".',') !== FALSE) { echo " checked"; } ?> value="Client Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Client Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Preferred Price".',') !== FALSE) { echo " checked"; } ?> value="Preferred Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Preferred Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Admin Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Web Price".',') !== FALSE) { echo " checked"; } ?> value="Web Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Web Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Commission Price".',') !== FALSE) { echo " checked"; } ?> value="Commission Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Commission Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."MSRP".',') !== FALSE) { echo " checked"; } ?> value="MSRP" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;MSRP
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Unit Price".',') !== FALSE) { echo " checked"; } ?> value="Unit Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Unit Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Unit Cost".',') !== FALSE) { echo " checked"; } ?> value="Unit Cost" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Unit Cost
						<input type="checkbox" <?php if (strpos($inventory_config, ','."Purchase Order Price".',') !== FALSE) { echo " checked"; } ?> value="Purchase Order Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Purchase Order Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Sales Order Price".',') !== FALSE) { echo " checked"; } ?> value="Sales Order Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;<?= SALES_ORDER_NOUN ?> Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Suggested Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Suggested Retail Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Suggested Retail Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Rush Price".',') !== FALSE) { echo " checked"; } ?> value="Rush Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Rush Price

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_7" >
                            Markup<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_7" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Markup By $".',') !== FALSE) { echo " checked"; } ?> value="Markup By $" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Markup By $
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Markup By %".',') !== FALSE) { echo " checked"; } ?> value="Markup By %" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Markup By %

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_8" >
                            Stock<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_8" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Current Stock".',') !== FALSE) { echo " checked"; } ?> value="Current Stock" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Current Stock
                        <!-- Taken out to remove confusion between quantity and current inventory <input type="checkbox" <?php //if (strpos($inventory_config, ','."Current Inventory".',') !== FALSE) { echo " checked"; } ?> value="Current Inventory" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Current Inventory-->
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Quantity".',') !== FALSE) { echo " checked"; } ?> value="Quantity" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Quantity
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Variance".',') !== FALSE) { echo " checked"; } ?> value="Variance" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;GL Code
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Write-offs".',') !== FALSE) { echo " checked"; } ?> value="Write-offs" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Write-offs

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Buying Units".',') !== FALSE) { echo " checked"; } ?> value="Buying Units" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Buying Units
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Selling Units".',') !== FALSE) { echo " checked"; } ?> value="Selling Units" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Selling Units
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Stocking Units".',') !== FALSE) { echo " checked"; } ?> value="Stocking Units" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Stocking Units
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_9" >
                            Location<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_9" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Location".',') !== FALSE) { echo " checked"; } ?> value="Location" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Location
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."LSD".',') !== FALSE) { echo " checked"; } ?> value="LSD" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;LSD

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_10" >
                            Dimensions<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_10" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Size".',') !== FALSE) { echo " checked"; } ?> value="Size" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Size
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Weight".',') !== FALSE) { echo " checked"; } ?> value="Weight" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Weight

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_11" >
                            Alerts<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_11" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Min Max".',') !== FALSE) { echo " checked"; } ?> value="Min Max" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Min Max
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Min Bin".',') !== FALSE) { echo " checked"; } ?> value="Min Bin" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Min Bin

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_12" >
                            Time Allocation<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_12" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Estimated Hours".',') !== FALSE) { echo " checked"; } ?> value="Estimated Hours" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Estimated Hours
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Actual Hours".',') !== FALSE) { echo " checked"; } ?> value="Actual Hours" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Actual Hours

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_13" >
                            Admin Fees<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_13" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Minimum Billable".',') !== FALSE) { echo " checked"; } ?> value="Minimum Billable" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Minimum Billable
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."GL Revenue".',') !== FALSE) { echo " checked"; } ?> value="GL Revenue" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;GL Revenue

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."GL Assets".',') !== FALSE) { echo " checked"; } ?> value="GL Assets" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;GL Assets

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_14" >
                            Quote<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_14" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Quote Description

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_15" >
                            Status<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_15" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Status

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_16" >
                            Display On Website<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_16" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Display On Website".',') !== FALSE) { echo " checked"; } ?> value="Display On Website" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Display On Website

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_17" >
                            General<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_17" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Notes".',') !== FALSE) { echo " checked"; } ?> value="Notes" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Notes
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Comments".',') !== FALSE) { echo " checked"; } ?> value="Comments" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Comments

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_18" >
                            Rental<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_18" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Rent Price".',') !== FALSE) { echo " checked"; } ?> value="Rent Price" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Rent Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Rental Days".',') !== FALSE) { echo " checked"; } ?> value="Rental Days" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Rental Days
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Rental Weeks".',') !== FALSE) { echo " checked"; } ?> value="Rental Weeks" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Rental Weeks
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Rental Months".',') !== FALSE) { echo " checked"; } ?> value="Rental Months" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Rental Months
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Rental Years".',') !== FALSE) { echo " checked"; } ?> value="Rental Years" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Rental Years
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Reminder/Alert".',') !== FALSE) { echo " checked"; } ?> value="Reminder/Alert" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Reminder/Alert

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_51" >
                            Day/Week/Month/Year<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_51" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Daily".',') !== FALSE) { echo " checked"; } ?> value="Daily" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Daily
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Weekly".',') !== FALSE) { echo " checked"; } ?> value="Weekly" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Weekly
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Monthly".',') !== FALSE) { echo " checked"; } ?> value="Monthly" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Monthly
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Annually".',') !== FALSE) { echo " checked"; } ?> value="Annually" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Annually
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."#Of Days".',') !== FALSE) { echo " checked"; } ?> value="#Of Days" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;#Of Days
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."#Of Hours".',') !== FALSE) { echo " checked"; } ?> value="#Of Hours" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;#Of Hours

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_53" >
                            Vehicle<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_53" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."#Of Kilometers".',') !== FALSE) { echo " checked"; } ?> value="#Of Kilometers" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;#Of Kilometers
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."#Of Miles".',') !== FALSE) { echo " checked"; } ?> value="#Of Miles" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;#Of Miles

                    </div>
                </div>
            </div>

			<div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ordersandpos" >
                            Inclusion<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_ordersandpos" class="panel-collapse collapse">
                    <div class="panel-body">
						<input type="checkbox" <?php if (strpos($inventory_config, ','."Include in P.O.S.".',') !== FALSE) {
                            echo " checked"; } ?> value="Include in P.O.S." style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Include in <?= POS_ADVANCE_TILE ?>
                            <input type="checkbox" <?php if (strpos($inventory_config, ','."Include in Purchase Orders".',') !== FALSE) {
                            echo " checked"; } ?> value="Include in Purchase Orders" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Include in Purchase Orders
                            <input type="checkbox" <?php if (strpos($inventory_config, ','."Include in Sales Orders".',') !== FALSE) {
                            echo " checked"; } ?> value="Include in Sales Orders" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;Include in <?= SALES_ORDER_TILE ?>
					</div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_amount" >
                            Amount<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_amount" class="panel-collapse collapse">
                    <div class="panel-body">
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Min Amount".',') !== FALSE) { echo " checked"; } ?> value="Min Amount" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Min Amount
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Max Amount".',') !== FALSE) { echo " checked"; } ?> value="Max Amount" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Max Amount
                    </div>
                </div>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-6">
				<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="If you click this, your settings will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="inventory.php?filter=Top" class="btn config-btn btn-lg">Back</a>
				<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
            </div>
            <div class="col-sm-6">
                <button	type="submit" name="inv_field"	value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save your settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div>
        </div>

		<div class="clearfix"></div>

    <?php }
    ?>

    <?php if($_GET['type'] == 'dashboard') { ?>
        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label">Tabs:</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Vendor..." id="tab_dashboard" name="tab_dashboard" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                    $tabs = get_config($dbc, 'vpl_tabs');
                    $each_tab = explode(',', $tabs);
                    foreach ($each_tab as $cat_tab) {
                        if ($invtype == $cat_tab) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
                    }
                  ?>
                </select>
            </div>
        </div>

        <h3>Dashboard</h3>
        <div class="panel-group" id="accordion2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_1" >
                            Description<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_1" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Description
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Category
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Subcategory".',') !== FALSE) { echo " checked"; } ?> value="Subcategory" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Subcategory
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Name".',') !== FALSE) { echo " checked"; } ?> value="Name" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Name
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Product Name".',') !== FALSE) { echo " checked"; } ?> value="Product Name" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Product Name
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Type
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Color".',') !== FALSE) { echo " checked"; } ?> value="Color" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Color

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_2" >
                            Unique Identifier<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_2" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Code".',') !== FALSE) { echo " checked"; } ?> value="Code" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Code
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."ID #".',') !== FALSE) { echo " checked"; } ?> value="ID #" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;ID #
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Item SKU".',') !== FALSE) { echo " checked"; } ?> value="Item SKU" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Item SKU
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Part #".',') !== FALSE) { echo " checked"; } ?> value="Part #" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Part #
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_3" >
                            Product Cost<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_3" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Cost".',') !== FALSE) { echo " checked"; } ?> value="Cost" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Cost
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."CDN Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="CDN Cost Per Unit" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;CDN Cost Per Unit
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."USD Cost Per Unit".',') !== FALSE) { echo " checked"; } ?> value="USD Cost Per Unit" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;USD Cost Per Unit
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."COGS".',') !== FALSE) { echo " checked"; } ?> value="COGS" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;COGS GL Code
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Average Cost".',') !== FALSE) { echo " checked"; } ?> value="Average Cost" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Average Cost
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."USD Invoice".',') !== FALSE) { echo " checked"; } ?> value="USD Invoice" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;USD Invoice

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_4" >
                            Purchase Info<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_4" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Vendor".',') !== FALSE) { echo " checked"; } ?> value="Vendor" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Vendor
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Purchase Cost".',') !== FALSE) { echo " checked"; } ?> value="Purchase Cost" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Purchase Cost
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Date Of Purchase".',') !== FALSE) { echo " checked"; } ?> value="Date Of Purchase" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Date Of Purchase

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_5" >
                            Shipping & Receiving<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_5" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Shipping Rate".',') !== FALSE) { echo " checked"; } ?> value="Shipping Rate" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Shipping Rate
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Shipping Cash".',') !== FALSE) { echo " checked"; } ?> value="Shipping Cash" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Shipping Cash
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Freight Charge".',') !== FALSE) { echo " checked"; } ?> value="Freight Charge" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Freight Charge
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Exchange Rate".',') !== FALSE) { echo " checked"; } ?> value="Exchange Rate" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Exchange Rate
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Exchange $".',') !== FALSE) { echo " checked"; } ?> value="Exchange $" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Exchange $

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_6" >
                            Pricing<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_6" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Sell Price".',') !== FALSE) { echo " checked"; } ?> value="Sell Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Sell Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Final Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Final Retail Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Final Retail Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Wholesale Price".',') !== FALSE) { echo " checked"; } ?> value="Wholesale Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Wholesale Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Commercial Price".',') !== FALSE) { echo " checked"; } ?> value="Commercial Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Commercial Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Client Price".',') !== FALSE) { echo " checked"; } ?> value="Client Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Client Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Preferred Price".',') !== FALSE) { echo " checked"; } ?> value="Preferred Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Preferred Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Admin Price".',') !== FALSE) { echo " checked"; } ?> value="Admin Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Admin Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Web Price".',') !== FALSE) { echo " checked"; } ?> value="Web Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Web Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Commission Price".',') !== FALSE) { echo " checked"; } ?> value="Commission Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Commission Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."MSRP".',') !== FALSE) { echo " checked"; } ?> value="MSRP" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;MSRP
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Unit Price".',') !== FALSE) { echo " checked"; } ?> value="Unit Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Unit Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Unit Cost".',') !== FALSE) { echo " checked"; } ?> value="Unit Cost" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Unit Cost
						<input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Purchase Order Price".',') !== FALSE) { echo " checked"; } ?> value="Purchase Order Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Purchase Order Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Sales Order Price".',') !== FALSE) { echo " checked"; } ?> value="Sales Order Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;<?= SALES_ORDER_NOUN ?> Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Suggested Retail Price".',') !== FALSE) { echo " checked"; } ?> value="Suggested Retail Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Suggested Retail Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rush Price".',') !== FALSE) { echo " checked"; } ?> value="Rush Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Rush Price

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_7" >
                            Markup<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_7" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Markup By $".',') !== FALSE) { echo " checked"; } ?> value="Markup By $" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Markup By $
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Markup By %".',') !== FALSE) { echo " checked"; } ?> value="Markup By %" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Markup By %

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_8" >
                            Stock<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_8" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Current Stock".',') !== FALSE) { echo " checked"; } ?> value="Current Stock" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Current Stock
                        <!-- Taken out to remove confusion between quantity and current inventory <input type="checkbox" <?php //if (strpos($inventory_dashboard_config, ','."Current Inventory".',') !== FALSE) { echo " checked"; } ?> value="Current Inventory" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Current Inventory-->
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Quantity".',') !== FALSE) { echo " checked"; } ?> value="Quantity" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Quantity
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Variance".',') !== FALSE) { echo " checked"; } ?> value="Variance" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;GL Code
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Write-offs".',') !== FALSE) { echo " checked"; } ?> value="Write-offs" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Write-offs

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Buying Units".',') !== FALSE) { echo " checked"; } ?> value="Buying Units" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Buying Units
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Selling Units".',') !== FALSE) { echo " checked"; } ?> value="Selling Units" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Selling Units
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Stocking Units".',') !== FALSE) { echo " checked"; } ?> value="Stocking Units" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Stocking Units

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_9" >
                            Location<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_9" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Location".',') !== FALSE) { echo " checked"; } ?> value="Location" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Location
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."LSD".',') !== FALSE) { echo " checked"; } ?> value="LSD" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;LSD

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_10" >
                            Dimensions<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_10" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Size".',') !== FALSE) { echo " checked"; } ?> value="Size" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Size
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Weight".',') !== FALSE) { echo " checked"; } ?> value="Weight" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Weight

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_11" >
                            Alerts<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_11" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Min Max".',') !== FALSE) { echo " checked"; } ?> value="Min Max" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Min Max
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Min Bin".',') !== FALSE) { echo " checked"; } ?> value="Min Bin" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Min Bin

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_12" >
                            Time Allocation<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_12" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Estimated Hours".',') !== FALSE) { echo " checked"; } ?> value="Estimated Hours" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Estimated Hours
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Actual Hours".',') !== FALSE) { echo " checked"; } ?> value="Actual Hours" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Actual Hours

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_13" >
                            Admin Fees<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_13" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Minimum Billable".',') !== FALSE) { echo " checked"; } ?> value="Minimum Billable" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Minimum Billable
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."GL Revenue".',') !== FALSE) { echo " checked"; } ?> value="GL Revenue" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;GL Revenue

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."GL Assets".',') !== FALSE) { echo " checked"; } ?> value="GL Assets" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;GL Assets

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_14" >
                            Quote<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_14" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Quote Description".',') !== FALSE) { echo " checked"; } ?> value="Quote Description" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Quote Description

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_15" >
                            Status<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_15" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Status".',') !== FALSE) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Status

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_16" >
                            Display On Website<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_16" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Display On Website".',') !== FALSE) { echo " checked"; } ?> value="Display On Website" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Display On Website

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_17" >
                            General<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_17" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Notes".',') !== FALSE) { echo " checked"; } ?> value="Notes" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Notes
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Comments".',') !== FALSE) { echo " checked"; } ?> value="Comments" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Comments

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_18" >
                            Rental<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_18" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rent Price".',') !== FALSE) { echo " checked"; } ?> value="Rent Price" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Rent Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rental Days".',') !== FALSE) { echo " checked"; } ?> value="Rental Days" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Rental Days
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rental Weeks".',') !== FALSE) { echo " checked"; } ?> value="Rental Weeks" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Rental Weeks
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rental Months".',') !== FALSE) { echo " checked"; } ?> value="Rental Months" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Rental Months
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rental Years".',') !== FALSE) { echo " checked"; } ?> value="Rental Years" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Rental Years
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Reminder/Alert".',') !== FALSE) { echo " checked"; } ?> value="Reminder/Alert" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Reminder/Alert

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_51" >
                            Day/Week/Month/Year<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_51" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Daily".',') !== FALSE) { echo " checked"; } ?> value="Daily" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Daily
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Weekly".',') !== FALSE) { echo " checked"; } ?> value="Weekly" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Weekly
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Monthly".',') !== FALSE) { echo " checked"; } ?> value="Monthly" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Monthly
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Annually".',') !== FALSE) { echo " checked"; } ?> value="Annually" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Annually
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."#Of Days".',') !== FALSE) { echo " checked"; } ?> value="#Of Days" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;#Of Days
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."#Of Hours".',') !== FALSE) { echo " checked"; } ?> value="#Of Hours" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;#Of Hours

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_53" >
                            Vehicle<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_53" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."#Of Kilometers".',') !== FALSE) { echo " checked"; } ?> value="#Of Kilometers" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;#Of Kilometers
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."#Of Miles".',') !== FALSE) { echo " checked"; } ?> value="#Of Miles" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;#Of Miles

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ordersandpos" >
                            Inclusion<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_ordersandpos" class="panel-collapse collapse">
                    <div class="panel-body">
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in P.O.S.".',') !== FALSE) {
                            echo " checked"; } ?> value="Include in P.O.S." style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Include in <?= POS_ADVANCE_TILE ?>
                            <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in Purchase Orders".',') !== FALSE) {
                            echo " checked"; } ?> value="Include in Purchase Orders" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Include in Purchase Orders
                            <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in Sales Orders".',') !== FALSE) {
                            echo " checked"; } ?> value="Include in Sales Orders" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Include in <?= SALES_ORDER_TILE ?>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_amount" >
                            Amount<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_amount" class="panel-collapse collapse">
                    <div class="panel-body">
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Min Amount".',') !== FALSE) { echo " checked"; } ?> value="Min Amount" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Min Amount
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Max Amount".',') !== FALSE) { echo " checked"; } ?> value="Max Amount" style="height: 20px; width: 20px;" name="inventory_dashboard[]">&nbsp;&nbsp;Max Amount
                    </div>
                </div>
            </div>
        </div>

        <br>

        <div class="form-group">
            <div class="col-sm-6">
				<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="If you click this, your settings will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="inventory.php?filter=Top" class="btn config-btn btn-lg">Back</a>
				<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
            </div>
            <div class="col-sm-6">
                <button	type="submit" name="inv_dashboard"	value="inv_dashboard" class="btn config-btn btn-lg	pull-right">Submit</button>
				<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save your settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div>
        </div>

    <?php }

    if($_GET['type'] == 'general') { ?>

        <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Send Email for Min Bin:</label>
        <div class="col-sm-8">
          <input name="inventory_minbin_email" value="<?php echo get_config($dbc, 'vpl_minbin_email'); ?>" type="text" class="form-control">
        </div>
        </div>

		<div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Title of V.P.L. Tile on Home Screen:</label>
        <div class="col-sm-8">
          <input name="pos_tile_titler" value="<?php if(get_config($dbc, 'vpl_tile_titler') == '' || get_config($dbc, 'vpl_tile_titler') == NULL ) { echo "Vendor Price List"; } else { echo get_config($dbc, 'vpl_tile_titler'); } ?>" type="text" class="form-control">
        </div>
        </div>
    <div class="form-group">
        <div class="col-sm-6">
			<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="If you click this, your settings will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a href="inventory.php?filter=Top" class="btn config-btn btn-lg">Back</a>
			<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
        </div>
        <div class="col-sm-6">
            <button	type="submit" name="general"	value="general" class="btn config-btn btn-lg	pull-right">Submit</button>
			<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save your settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        </div>
    </div>

		<div class="clearfix"></div>


    <?php } ?>
	<?php if($_GET['type'] == 'impexp') { ?>
		<div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="The Import/Export functionality allows users to export a full spreadsheet of the tile's data, as well as add or edit multiple row items at once."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Enable Import/Export:</label>
            <div class="col-sm-8">
			<?php
			$checked = '';
			$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_impexp_vpl'"));
			if($get_config['configid'] > 0) {
				$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_impexp_vpl'"));
				if($get_config['value'] == '1') {
					$checked = 'checked';
				}
			}
			?>
              <input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?>  name='' class='show_impexp_vpl' value='1'>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-6">
				<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="If you click this, your settings will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="inventory.php?category=All" class="btn config-btn btn-lg">Back</a>
				<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
            </div>
            <div class="col-sm-6">
                <button	type="submit" name="add_tab" value="add_tab" class="btn config-btn btn-lg pull-right">Submit</button>
				<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save your settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div>
        </div>

<?php } ?>

<!--
<div class="form-group">
    <div class="col-sm-4 clearfix">
        <a href="inventory.php?filter=Top" class="btn config-btn pull-right">Back</a>
    </div>
    <div class="col-sm-8">
        <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
    </div>
</div>
-->
</div>

</form>
</div>
</div>

<?php include ('../footer.php'); ?>