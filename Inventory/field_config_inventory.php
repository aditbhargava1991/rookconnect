<?php
/*
Dashboard
*/
include ('../include.php');

error_reporting(0);

if(isset($_POST['dashboard_tab'])) {
	$cx_data = $_POST['dashboard_tab_cx'];
	$inv_setting = implode(",", $cx_data);
	$query_insert_setting = "UPDATE `inventory_setting` SET `value`='$inv_setting' WHERE `inventorysettingid`=1";
    $result_insert_setting = mysqli_query($dbc, $query_insert_setting);
	echo '<script type="text/javascript"> window.location.replace("field_config_inventory.php?type=dashboard_tab"); </script>';
}

if (isset($_POST['add_tab'])) {
    $inventory_tabs = filter_var(implode('#*#',array_filter($_POST['inventory_tabs'])),FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='inventory_tabs'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$inventory_tabs' WHERE name='inventory_tabs'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('inventory_tabs', '$inventory_tabs')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    echo '<script type="text/javascript"> window.location.replace("field_config_inventory.php?type=tab"); </script>';
}

if (isset($_POST['inv_dashboard'])) {
    $inventory_dashboard = implode(',',$_POST['inventory_dashboard']);
    $tab_dashboard       = filter_var($_POST['tab_dashboard'],FILTER_SANITIZE_STRING);
    //if (strpos(','.$inventory_dashboard.',',','.'Category'.',') === false) {
    //    $inventory_dashboard = 'Category,'.$inventory_dashboard;
    //}
    
    if ( empty($tab_dashboard) ) {
        // A Category was not selected from the Tabs dropdown.
        // So we take it as the default for Display All and Last 25 Added dashboards
        $get_field_config = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `inventory_dashboard` FROM `field_config_inventory` WHERE `tab`='Top'" ) );
        
        if ( $get_field_config['configinvid'] > 0 ) {
            $query_update_config  = "UPDATE `field_config_inventory` SET `inventory_dashboard`='$inventory_dashboard' WHERE `tab`='Top'";
            $result_update_config = mysqli_query($dbc, $query_update_employee);
        } else {
            $query_insert_config = "INSERT INTO `field_config_inventory` (`tab`, `inventory_dashboard`) VALUES ('Top', '$inventory_dashboard')";
            $result_insert_config = mysqli_query($dbc, $query_insert_config);
        }
    
    } else {
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_inventory WHERE tab='$tab_dashboard' AND accordion IS NULL"));
        
        if($get_field_config['configinvid'] > 0) {
            $query_update_employee = "UPDATE `field_config_inventory` SET inventory_dashboard = '$inventory_dashboard' WHERE tab='$tab_dashboard'";
            $result_update_employee = mysqli_query($dbc, $query_update_employee);
        } else {
            $query_insert_config = "INSERT INTO `field_config_inventory` (`tab`, `inventory_dashboard`) VALUES ('$tab_dashboard', '$inventory_dashboard')";
            $result_insert_config = mysqli_query($dbc, $query_insert_config);
        }
    }
    
    echo '<script type="text/javascript"> window.location.replace("field_config_inventory.php?type=dashboard&tab='.$tab_dashboard.'"); </script>';
}

if (isset($_POST['inv_field'])) {
    $tab_field = filter_var($_POST['tab_field'],FILTER_SANITIZE_STRING);
    $accordion = filter_var($_POST['accordion'],FILTER_SANITIZE_STRING);
    $order     = filter_var($_POST['order'],FILTER_SANITIZE_STRING);
    if (!empty ($order) ) {
        $order_update_query = ", `order`='$order'";
    } else {
        $order_update_query = '';
    }

    $inventory = implode(',',$_POST['inventory']);

    //if (strpos(','.$inventory.',',','.'Category'.',') === false) {
    //    $inventory = 'Category,'.$inventory;
    //}

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configinvid) AS configinvid FROM field_config_inventory WHERE tab='$tab_field' AND accordion='$accordion'"));
    if($get_field_config['configinvid'] > 0) {
        $query_update_employee = "UPDATE `field_config_inventory` SET inventory = '$inventory' " . $order_update_query . " WHERE tab='$tab_field' AND accordion='$accordion'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `field_config_inventory` (`tab`, `accordion`, `inventory`, `order`) VALUES ('$tab_field', '$accordion', '$inventory', '$order')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    echo '<script type="text/javascript"> window.location.replace("field_config_inventory.php?type=field&tab='.$tab_field.'&accr='.$accordion.'"); </script>';
}

if (isset($_POST['general'])) {
    $inventory_minbin_email     = $_POST['inventory_minbin_email'];
    $inventory_minbin_subject   = $_POST['inventory_minbin_subject'];
    $inventory_minbin_body      = $_POST['inventory_minbin_body'];

	if (!filter_var(trim($inventory_minbin_email), FILTER_VALIDATE_EMAIL) === false) {

    } else {
        echo '
            <script type="text/javascript">
                alert("The email address you have provided appears to be not valid. Please add a valid email address.");
                window.location.replace("field_config_inventory.php?type=general");
            </script>';
        exit();
    }

    $get_config = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE `name`='inventory_minbin_email'" ) );
    if ( $get_config['configid'] > 0 ) {
        $query_update_employee = "UPDATE `general_configuration` SET `value`='$inventory_minbin_email' WHERE `name`='inventory_minbin_email'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('inventory_minbin_email', '$inventory_minbin_email')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $get_config = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE `name`='inventory_minbin_subject'" ) );
    if ( $get_config['configid'] > 0 ) {
        $query_update_employee = "UPDATE `general_configuration` SET `value`='$inventory_minbin_subject' WHERE `name`='inventory_minbin_subject'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('inventory_minbin_subject', '$inventory_minbin_subject')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $get_config = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT COUNT(`configid`) AS `configid` FROM `general_configuration` WHERE `name`='inventory_minbin_body'" ) );
    if ( $get_config['configid'] > 0 ) {
        $query_update_employee = "UPDATE `general_configuration` SET `value`='$inventory_minbin_body' WHERE `name`='inventory_minbin_body'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('inventory_minbin_body', '$inventory_minbin_body')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    echo '<script type="text/javascript">window.location.replace("field_config_inventory.php?type=general");</script>';
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
	$('input.inventory_default_select_all').on('change', function() {
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "inventory_ajax_all.php?fill=inventory_default_select_all&value="+value,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});
	$('input.show_digi_count').on('change', function() {
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "inventory_ajax_all.php?fill=show_digi_count&value="+value,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
		});
	});
	$('input.show_impexp_inv').on('change', function() {
		if($(this).prop('checked') == true){
			var value = $(this).attr('value');
		} else { var value = ''; }
		$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "inventory_ajax_all.php?fill=show_impexp_inv&value="+value,
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
<h1>Inventory Settings</h1>
<?php if(get_config($dbc, 'inventory_default_select_all') == 1) { ?>
<div class="pad-left gap-top double-gap-bottom"><a href="inventory.php?category=dispall_31V2irt2u3e5S3s1f2ADe3_31" class="btn config-btn">Back to Dashboard</a></div>
<?php } else { ?>
<div class="pad-left double-gap-bottom"><a href="inventory.php?category=Top" class="btn config-btn">Back to Dashboard</a></div>
<?php } ?>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<?php if($_GET['type'] != 'pdfstyling' && $_GET['type'] != 'templates') { ?>
    <form id="form1" name="form1" method="post"	action="field_config_inventory.php" enctype="multipart/form-data" class="form-horizontal" role="form">
<?php } ?>

<!-- <div class="panel-group" id="accordion2"> -->

    <?php
    $invtype = $_GET['tab'];
    $accr = $_GET['accr'];
    $type = $_GET['type'];

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventory FROM field_config_inventory WHERE tab='$invtype' AND accordion='$accr'"));
    $inventory_config = ','.$get_field_config['inventory'].',';

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT inventory_dashboard FROM field_config_inventory WHERE tab='$invtype' AND accordion IS NULL"));
    $inventory_dashboard_config = ','.$get_field_config['inventory_dashboard'].',';
    $get_field_order = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(`order` SEPARATOR ',') AS all_order FROM field_config_inventory WHERE tab='$invtype'"));

    $active_tab = '';
    $active_field = '';
    $active_dashboard_tab = '';
    $active_dashboard = '';
    $active_general = '';
    $active_rs = '';
	$digi_count = '';
	$impexp = '';
    $pdfstyling = '';
    $templates = '';

    if($_GET['type'] == 'tab') {
        $active_tab = 'active_tab';
    }
    if($_GET['type'] == 'field') {
        $active_field = 'active_tab';
    }
	if($_GET['type'] == 'dashboard_tab') {
        $active_dashboard_tab = 'active_tab';
    }
    if($_GET['type'] == 'dashboard') {
        $active_dashboard = 'active_tab';
    }
    if($_GET['type'] == 'general') {
        $active_general = 'active_tab';
    }
    if($_GET['type'] == 'rs') {
        $active_rs = 'active_tab';
    }
	if($_GET['type'] == 'digi_count') {
        $digi_count = 'active_tab';
    }
	if($_GET['type'] == 'impexp') {
        $impexp = 'active_tab';
    }
    if($_GET['type'] == 'pdfstyling') {
        $pdfstyling = 'active_tab';
    }
    if($_GET['type'] == 'templates') {
        $templates = 'active_tab';
    }

    echo "<a href='field_config_inventory.php?type=tab'><button type='button' class='btn brand-btn mobile-block ".$active_tab."' >Tabs</button></a>&nbsp;&nbsp;";
    echo "<a href='field_config_inventory.php?type=field'><button type='button' class='btn brand-btn mobile-block ".$active_field."' >Fields</button></a>&nbsp;&nbsp;";
	echo "<a href='field_config_inventory.php?type=dashboard_tab'><button type='button' class='btn brand-btn mobile-block ".$active_dashboard_tab."' >Dashboard Sub Tabs</button></a>&nbsp;&nbsp;";
    echo "<a href='field_config_inventory.php?type=dashboard'><button type='button' class='btn brand-btn mobile-block ".$active_dashboard."' >Dashboard</button></a>&nbsp;&nbsp;";
    echo "<a href='field_config_inventory.php?type=general'><button type='button' class='btn brand-btn mobile-block ".$active_general."' >General</button></a>&nbsp;&nbsp;";
    echo "<a href='field_config_receive_shipment.php?type=rs'><button type='button' class='btn brand-btn mobile-block ".$active_rs."' >Receive Shipment</button></a>&nbsp;&nbsp;";
	echo "<a href='field_config_order_lists.php'><button type='button' class='btn brand-btn mobile-block' >Order Lists</button></a>&nbsp;&nbsp;";
	echo "<a href='field_config_inventory.php?type=digi_count'><button type='button' class='btn brand-btn mobile-block ".$digi_count."' >Digital Inventory Count</button></a>&nbsp;&nbsp;";
	echo "<a href='field_config_inventory.php?type=impexp'><button type='button' class='btn brand-btn mobile-block ".$impexp."' >Import/Export</button></a>&nbsp;&nbsp;";
    echo "<a href='field_config_inventory.php?type=pdfstyling'><button type='button' class='btn brand-btn mobile-block ".$pdfstyling."' >PDF Styling</button></a>&nbsp;&nbsp;";
    echo "<a href='field_config_inventory.php?type=templates'><button type='button' class='btn brand-btn mobile-block ".$templates."' >Templates</button></a>";

    echo '<br><br>';

    if($_GET['type'] == 'tab') {
		$inventory_tab_list = explode('#*#', get_config($dbc, 'inventory_tabs')); ?>
		<script>
		function add_inventory_tab() {
			var clone = $('.inv-tabs .form-group').last().clone();
			clone.find('input').val('');
			$('.inv-tabs').append(clone);
			$('.inv-tabs input').last().focus();
		}
		function rem_inventory_tab(link) {
			$(link).closest('.form-group').remove();
		}
		</script>
        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="These tabs sort your inventory by Category, so please make sure the tab names match your inventory's category names."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Add Tabs:</label>
            <div class="col-sm-8 inv-tabs">
				<?php foreach($inventory_tab_list as $inventory_tab) { ?>
					<div class="form-group">
						<div class="col-sm-10"><input name="inventory_tabs[]" type="text" value="<?= $inventory_tab ?>" class="form-control"/></div>
						<div class="col-sm-2"><img src="<?= WEBSITE_URL ?>/img/plus.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right" onclick="add_inventory_tab();">
							<img src="<?= WEBSITE_URL ?>/img/remove.png" style="height: 1.5em; margin: 0.25em; width: 1.5em;" class="pull-right" onclick="rem_inventory_tab(this);"></div>
					</div>
				<?php } ?>
            </div>
        </div>
		<div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Instead of tabs, have a drop down menu that will sort your inventory by their respective tabs."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Or Use a Drop Down Menu:</label>
            <div class="col-sm-8">
			<?php
			$checked = '';
			$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_category_dropdown'"));
			if($get_config['configid'] > 0) {
				$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_category_dropdown'"));
				if($get_config['value'] == '1') {
					$checked = 'checked';
				}
			}
			?>
              <input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?>  name='' class='show_category_dropdown' value='1'>
            </div>
        </div>
        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Check this box to make Default as Select All."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Check this box to make Default as Select All:</label>
            <div class="col-sm-8">
                <?php
                $checked = '';
                $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='inventory_default_select_all'"));
                if($get_config['configid'] > 0) {
                    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='inventory_default_select_all'"));
                    if($get_config['value'] == '1') {
                        $checked = 'checked';
                    }
                }
                ?>
              <input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?>  name='' class='inventory_default_select_all' value='1'>
            </div>
        </div>

		<div class="clearfix"></div>

        <div class="form-group double-gap-top">
            <div class="col-sm-6">
                <?php if(get_config($dbc, 'inventory_default_select_all') == 1) { ?>
					<div class="double-gap-bottom"><a href="inventory.php?category=dispall_31V2irt2u3e5S3s1f2ADe3_31" class="btn brand-btn btn-lg">Back</a></div>
                <?php } else { ?>
					<div class="double-gap-bottom"><a href="inventory.php?category=Top" class="btn brand-btn btn-lg">Back</a></div>
                <?php } ?>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
            </div>
            <div class="col-sm-6">
                <button	type="submit" name="add_tab" value="add_tab" class="btn brand-btn btn-lg pull-right">Submit</button>
            </div>
        </div>

		<div class="clearfix"></div>
    <?php }

    if($_GET['type'] == 'field') {
        ?>
        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label">
				<span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose your desired tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				Tabs:
			</label>
            <div class="col-sm-8">
				<select data-placeholder="Choose a Tab..." id="tab_field" name="tab_field" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                    $tabs = get_config($dbc, 'inventory_tabs');
                    $each_tab = explode('#*#', $tabs);
                    foreach ($each_tab as $cat_tab) {
						$url_tab = preg_replace('/[^a-z]/','',strtolower($cat_tab));
                        echo "<option ".($invtype == $url_tab ? 'selected' : '')." value='". $url_tab."'>".$cat_tab.'</option>';
                    }
					
					//$cat_list = explode('#*#',get_config($dbc, 'inventory_tabs'));
					//foreach($cat_list as $each_cat) {
					//	if($invtype == preg_replace('/[^a-z]/','',strtolower($each_cat))) {
					//		$invtype = filter_var($each_cat,FILTER_SANITIZE_STRING);
					//	}
					//}
                  ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label">
				<span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here first to choose your desired fields that will be in the selected tab. Make sure to choose the order in which you would like these to be viewed in the second drop down."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				Accordion:
			</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose an Accordion..." id="acc" name="accordion" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <option <?php if ($accr == "Description") { echo " selected"; } ?> value="Description"><?= get_field_config_inventory($dbc, 'Description', 'order', $invtype) ?> : Description</option>
                  <option <?php if ($accr == "Unique Identifier") { echo " selected"; } ?> value="Unique Identifier"><?php echo get_field_config_inventory($dbc, 'Unique Identifier', 'order', $invtype); ?> : Unique Identifier</option>
                  <option <?php if ($accr == "Product Cost") { echo " selected"; } ?> value="Product Cost"><?php echo get_field_config_inventory($dbc, 'Product Cost', 'order', $invtype); ?> : Product Cost</option>
                  <option <?php if ($accr == "Purchase Info") { echo " selected"; } ?> value="Purchase Info"><?php echo get_field_config_inventory($dbc, 'Purchase Info', 'order', $invtype); ?> : Purchase Info</option>
                  <option <?php if ($accr == "Shipping Receiving") { echo " selected"; } ?> value="Shipping Receiving"><?php echo get_field_config_inventory($dbc, 'Shipping Receiving', 'order', $invtype); ?> : Shipping Receiving</option>
                  <option <?php if ($accr == "Pricing") { echo " selected"; } ?> value="Pricing"><?php echo get_field_config_inventory($dbc, 'Pricing', 'order', $invtype); ?> : Pricing</option>
                  <option <?php if ($accr == "Markup") { echo " selected"; } ?> value="Markup"><?php echo get_field_config_inventory($dbc, 'Markup', 'order', $invtype); ?> : Markup</option>
                  <option <?php if ($accr == "Stock") { echo " selected"; } ?> value="Stock"><?php echo get_field_config_inventory($dbc, 'Stock', 'order', $invtype); ?> : Stock</option>
                  <option <?php if ($accr == "Location") { echo " selected"; } ?> value="Location"><?php echo get_field_config_inventory($dbc, 'Location', 'order', $invtype); ?> : Location</option>
                  <option <?php if ($accr == "Dimensions") { echo " selected"; } ?> value="Dimensions"><?php echo get_field_config_inventory($dbc, 'Dimensions', 'order', $invtype); ?> : Dimensions</option>
                  <option <?php if ($accr == "Alerts") { echo " selected"; } ?> value="Alerts"><?php echo get_field_config_inventory($dbc, 'Alerts', 'order', $invtype); ?> : Alerts</option>
                  <option <?php if ($accr == "Time Allocation") { echo " selected"; } ?> value="Time Allocation"><?php echo get_field_config_inventory($dbc, 'Time Allocation', 'order', $invtype); ?> : Time Allocation</option>
                  <option <?php if ($accr == "Admin Fees") { echo " selected"; } ?> value="Admin Fees"><?php echo get_field_config_inventory($dbc, 'Admin Fees', 'order', $invtype); ?> : Admin Fees</option>
                  <option <?php if ($accr == "Quote") { echo " selected"; } ?> value="Quote"><?php echo get_field_config_inventory($dbc, 'Quote', 'order', $invtype); ?> : Quote</option>
                  <option <?php if ($accr == "Status") { echo " selected"; } ?> value="Status"><?php echo get_field_config_inventory($dbc, 'Status', 'order', $invtype); ?> : Status</option>
                  <option <?php if ($accr == "Display On Website") { echo " selected"; } ?> value="Display On Website"><?php echo get_field_config_inventory($dbc, 'Display On Website', 'order', $invtype); ?> : Display On Website</option>
                  <option <?php if ($accr == "General") { echo " selected"; } ?> value="General"><?php echo get_field_config_inventory($dbc, 'General', 'order', $invtype); ?> : General</option>
                  <option <?php if ($accr == "Rental") { echo " selected"; } ?> value="Rental"><?php echo get_field_config_inventory($dbc, 'Rental', 'order', $invtype); ?> : Rental</option>
                  <option <?php if ($accr == "Day/Week/Month/Year") { echo " selected"; } ?> value="Day/Week/Month/Year"><?php echo get_field_config_inventory($dbc, 'Day/Week/Month/Year', 'order', $invtype); ?> : Day/Week/Month/Year</option>
                  <option <?php if ($accr == "Vehicle") { echo " selected"; } ?> value="Vehicle"><?php echo get_field_config_inventory($dbc, 'Vehicle', 'order', $invtype); ?> : Vehicle</option>
                  <option <?php if ($accr == "Bill of Material") { echo " selected"; } ?> value="Bill of Material"><?php echo get_field_config_inventory($dbc, 'Bill of Material', 'order', $invtype); ?> : Bill of Material</option>
                  <option <?php if ($accr == "Supplimentary Products") { echo " selected"; } ?> value="Supplimentary Products"><?php echo get_field_config_inventory($dbc, 'Supplimentary Products', 'order', $invtype); ?> : Supplimentary Products</option>
                  <option <?php if ($accr == "Inventory History") { echo " selected"; } ?> value="Inventory History"><?php echo get_field_config_inventory($dbc, 'Inventory History', 'order', $invtype); ?> : Inventory History</option>
                </select>
                <select data-placeholder="Choose an Order..." name="order" class="chosen-select-deselect form-control" width="380">
                    <option value=""></option>
                    <?php
                    for($m=1;$m<=30;$m++) { ?>
                        <option <?php if (get_field_config_inventory($dbc, $accr, 'order', $invtype) == $m) { echo  'selected="selected"'; } else if (strpos(','.$get_field_order['all_order'].',', ','.$m.',') !== false) { echo " disabled"; } ?> value="<?php echo $m;?>"><?php echo $m;?></option>
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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Name".',') !== false) { echo " checked"; } ?> value="Name" name="inventory[]">&nbsp;&nbsp;Name&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Product Name".',') !== false) { echo " checked"; } ?> value="Product Name" name="inventory[]">&nbsp;&nbsp;Product Name&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Brand".',') !== false) { echo " checked"; } ?> value="Brand" name="inventory[]">&nbsp;&nbsp;Brand&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Category".',') !== false) { echo " checked"; } ?> value="Category" name="inventory[]">&nbsp;&nbsp;Tab&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Subcategory".',') !== false) { echo " checked"; } ?> value="Subcategory" name="inventory[]">&nbsp;&nbsp;Subtab&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Type".',') !== false) { echo " checked"; } ?> value="Type" name="inventory[]">&nbsp;&nbsp;Type&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Color".',') !== false) { echo " checked"; } ?> value="Color" name="inventory[]">&nbsp;&nbsp;Color&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Description".',') !== false) { echo " checked"; } ?> value="Description" name="inventory[]">&nbsp;&nbsp;Description&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Application".',') !== false) { echo " checked"; } ?> value="Application" name="inventory[]">&nbsp;&nbsp;Application
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."GST Exempt".',') !== false) { echo " checked"; } ?> value="GST Exempt" style="height: 20px; width: 20px;" name="inventory[]">&nbsp;&nbsp;GST Exempt

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Code".',') !== false) { echo " checked"; } ?> value="Code" name="inventory[]">&nbsp;&nbsp;Code
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."ID #".',') !== false) { echo " checked"; } ?> value="ID #" name="inventory[]">&nbsp;&nbsp;ID #
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Item SKU".',') !== false) { echo " checked"; } ?> value="Item SKU" name="inventory[]">&nbsp;&nbsp;Item SKU
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Part #".',') !== false) { echo " checked"; } ?> value="Part #" name="inventory[]">&nbsp;&nbsp;Part #
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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Cost".',') !== false) { echo " checked"; } ?> value="Cost" name="inventory[]">&nbsp;&nbsp;Cost
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."CDN Cost Per Unit".',') !== false) { echo " checked"; } ?> value="CDN Cost Per Unit" name="inventory[]">&nbsp;&nbsp;CDN Cost Per Unit
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."USD Cost Per Unit".',') !== false) { echo " checked"; } ?> value="USD Cost Per Unit" name="inventory[]">&nbsp;&nbsp;USD Cost Per Unit
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."COGS".',') !== false) { echo " checked"; } ?> value="COGS" name="inventory[]">&nbsp;&nbsp;COGS GL Code
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Average Cost".',') !== false) { echo " checked"; } ?> value="Average Cost" name="inventory[]">&nbsp;&nbsp;Average Cost
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."USD Invoice".',') !== false) { echo " checked"; } ?> value="USD Invoice" name="inventory[]">&nbsp;&nbsp;USD Invoice

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Vendor".',') !== false) { echo " checked"; } ?> value="Vendor" name="inventory[]">&nbsp;&nbsp;Vendor
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Purchase Cost".',') !== false) { echo " checked"; } ?> value="Purchase Cost" name="inventory[]">&nbsp;&nbsp;Purchase Cost
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Date Of Purchase".',') !== false) { echo " checked"; } ?> value="Date Of Purchase" name="inventory[]">&nbsp;&nbsp;Date Of Purchase

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Shipping Rate".',') !== false) { echo " checked"; } ?> value="Shipping Rate" name="inventory[]">&nbsp;&nbsp;Shipping Rate
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Shipping Cash".',') !== false) { echo " checked"; } ?> value="Shipping Cash" name="inventory[]">&nbsp;&nbsp;Shipping Cash
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Freight Charge".',') !== false) { echo " checked"; } ?> value="Freight Charge" name="inventory[]">&nbsp;&nbsp;Freight Charge
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Exchange Rate".',') !== false) { echo " checked"; } ?> value="Exchange Rate" name="inventory[]">&nbsp;&nbsp;Exchange Rate
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Exchange $".',') !== false) { echo " checked"; } ?> value="Exchange $" name="inventory[]">&nbsp;&nbsp;Exchange $

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Sell Price".',') !== false) { echo " checked"; } ?> value="Sell Price" name="inventory[]">&nbsp;&nbsp;Sell Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Final Retail Price".',') !== false) { echo " checked"; } ?> value="Final Retail Price" name="inventory[]">&nbsp;&nbsp;Final Retail Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Wholesale Price".',') !== false) { echo " checked"; } ?> value="Wholesale Price" name="inventory[]">&nbsp;&nbsp;Wholesale Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Commercial Price".',') !== false) { echo " checked"; } ?> value="Commercial Price" name="inventory[]">&nbsp;&nbsp;Commercial Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Client Price".',') !== false) { echo " checked"; } ?> value="Client Price" name="inventory[]">&nbsp;&nbsp;Client Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Preferred Price".',') !== false) { echo " checked"; } ?> value="Preferred Price" name="inventory[]">&nbsp;&nbsp;Preferred Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Admin Price".',') !== false) { echo " checked"; } ?> value="Admin Price" name="inventory[]">&nbsp;&nbsp;Admin Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Web Price".',') !== false) { echo " checked"; } ?> value="Web Price" name="inventory[]">&nbsp;&nbsp;Web Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Clearance Price".',') !== false) { echo " checked"; } ?> value="Clearance Price" name="inventory[]">&nbsp;&nbsp;Clearance Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Commission Price".',') !== false) { echo " checked"; } ?> value="Commission Price" name="inventory[]">&nbsp;&nbsp;Commission Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."MSRP".',') !== false) { echo " checked"; } ?> value="MSRP" name="inventory[]">&nbsp;&nbsp;MSRP
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Unit Price".',') !== false) { echo " checked"; } ?> value="Unit Price" name="inventory[]">&nbsp;&nbsp;Unit Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Unit Cost".',') !== false) { echo " checked"; } ?> value="Unit Cost" name="inventory[]">&nbsp;&nbsp;Unit Cost
						<input type="checkbox" <?php if (strpos($inventory_config, ','."Purchase Order Price".',') !== false) { echo " checked"; } ?> value="Purchase Order Price" name="inventory[]">&nbsp;&nbsp;Purchase Order Price
						<input type="checkbox" <?php if (strpos($inventory_config, ','."Sales Order Price".',') !== false) { echo " checked"; } ?> value="Sales Order Price" name="inventory[]">&nbsp;&nbsp;<?= SALES_ORDER_NOUN ?> Price
                        <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Cost".',') !== false) { echo " checked"; } ?> value="Drum Unit Cost" name="inventory[]">&nbsp;&nbsp;Drum Unit Cost
                        <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Price".',') !== false) { echo " checked"; } ?> value="Drum Unit Price" name="inventory[]">&nbsp;&nbsp;Drum Unit Price
                        <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Cost".',') !== false) { echo " checked"; } ?> value="Tote Unit Cost" name="inventory[]">&nbsp;&nbsp;Tote Unit Cost
                        <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Price".',') !== false) { echo " checked"; } ?> value="Tote Unit Price" name="inventory[]">&nbsp;&nbsp;Tote Unit Price
                        <input type="checkbox" <?php if (strpos($value_config, ','."WCB Price".',') !== false) { echo " checked"; } ?> value="WCB Price" name="inventory[]">&nbsp;&nbsp;WCB Price
                        <input type="checkbox" <?php if (strpos($value_config, ','."Suggested Retail Price".',') !== false) { echo " checked"; } ?> value="Suggested Retail Price" name="inventory[]">&nbsp;&nbsp;Suggested Retail Price
                        <input type="checkbox" <?php if (strpos($value_config, ','."Rush Price".',') !== false) { echo " checked"; } ?> value="Rush Price" name="inventory[]">&nbsp;&nbsp;Rush Price

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Markup By $".',') !== false) { echo " checked"; } ?> value="Markup By $" name="inventory[]">&nbsp;&nbsp;Markup By $
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Markup By %".',') !== false) { echo " checked"; } ?> value="Markup By %" name="inventory[]">&nbsp;&nbsp;Markup By %

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Current Stock".',') !== false) { echo " checked"; } ?> value="Current Stock" name="inventory[]">&nbsp;&nbsp;Current Stock
                        <!-- Taken out to remove confusion between quantity and current inventory <input type="checkbox" <?php // if (strpos($inventory_config, ','."Current Inventory".',') !== false) { echo " checked"; } ?> value="Current Inventory" name="inventory[]">&nbsp;&nbsp;Current Inventory-->
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Quantity".',') !== false) { echo " checked"; } ?> value="Quantity" name="inventory[]">&nbsp;&nbsp;Quantity
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Variance".',') !== false) { echo " checked"; } ?> value="Variance" name="inventory[]">&nbsp;&nbsp;GL Code
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Write-offs".',') !== false) { echo " checked"; } ?> value="Write-offs" name="inventory[]">&nbsp;&nbsp;Write-offs

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Buying Units".',') !== false) { echo " checked"; } ?> value="Buying Units" name="inventory[]">&nbsp;&nbsp;Buying Units
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Selling Units".',') !== false) { echo " checked"; } ?> value="Selling Units" name="inventory[]">&nbsp;&nbsp;Selling Units
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Stocking Units".',') !== false) { echo " checked"; } ?> value="Stocking Units" name="inventory[]">&nbsp;&nbsp;Stocking Units
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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Location".',') !== false) { echo " checked"; } ?> value="Location" name="inventory[]">&nbsp;&nbsp;Location
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."LSD".',') !== false) { echo " checked"; } ?> value="LSD" name="inventory[]">&nbsp;&nbsp;LSD

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Size".',') !== false) { echo " checked"; } ?> value="Size" name="inventory[]">&nbsp;&nbsp;Size&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Weight".',') !== false) { echo " checked"; } ?> value="Weight" name="inventory[]">&nbsp;&nbsp;Weight&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Length".',') !== false) { echo " checked"; } ?> value="Length" name="inventory[]">&nbsp;&nbsp;Length&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Gauge".',') !== false) { echo " checked"; } ?> value="Gauge" name="inventory[]">&nbsp;&nbsp;Gauge&nbsp;&nbsp;&nbsp;
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Pressure".',') !== false) { echo " checked"; } ?> value="Pressure" name="inventory[]">&nbsp;&nbsp;Pressure

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Min Max".',') !== false) { echo " checked"; } ?> value="Min Max" name="inventory[]">&nbsp;&nbsp;Min Max
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Min Bin".',') !== false) { echo " checked"; } ?> value="Min Bin" name="inventory[]">&nbsp;&nbsp;Min Bin

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Estimated Hours".',') !== false) { echo " checked"; } ?> value="Estimated Hours" name="inventory[]">&nbsp;&nbsp;Estimated Hours
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Actual Hours".',') !== false) { echo " checked"; } ?> value="Actual Hours" name="inventory[]">&nbsp;&nbsp;Actual Hours

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Minimum Billable".',') !== false) { echo " checked"; } ?> value="Minimum Billable" name="inventory[]">&nbsp;&nbsp;Minimum Billable
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."GL Revenue".',') !== false) { echo " checked"; } ?> value="GL Revenue" name="inventory[]">&nbsp;&nbsp;GL Revenue

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."GL Assets".',') !== false) { echo " checked"; } ?> value="GL Assets" name="inventory[]">&nbsp;&nbsp;GL Assets

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Quote Description".',') !== false) { echo " checked"; } ?> value="Quote Description" name="inventory[]">&nbsp;&nbsp;Quote Description

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Status".',') !== false) { echo " checked"; } ?> value="Status" name="inventory[]">&nbsp;&nbsp;Status

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
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Display On Website".',') !== false) { echo " checked"; } ?> value="Display On Website" name="inventory[]">&nbsp;&nbsp;Display On Website
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Featured On Website".',') !== false) { echo " checked"; } ?> value="Featured On Website" name="inventory[]">&nbsp;&nbsp;Featured On Website
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."New Item".',') !== false) { echo " checked"; } ?> value="New Item" name="inventory[]">&nbsp;&nbsp;Display Item As New On Website
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Item On Sale".',') !== false) { echo " checked"; } ?> value="Item On Sale" name="inventory[]">&nbsp;&nbsp;Display Item As On Sale On Website
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Item On Clearance".',') !== false) { echo " checked"; } ?> value="Item On Clearance" name="inventory[]">&nbsp;&nbsp;Display Item As Clearance On Website
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
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Main Image".',') !== false) { echo " checked"; } ?> value="Main Image" name="inventory[]">&nbsp;&nbsp;Main Image
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Additional Images".',') !== false) { echo " checked"; } ?> value="Additional Images" name="inventory[]">&nbsp;&nbsp;Additional Images
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Spec Sheet".',') !== false) { echo " checked"; } ?> value="Spec Sheet" name="inventory[]">&nbsp;&nbsp;Spec Sheet
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Notes".',') !== false) { echo " checked"; } ?> value="Notes" name="inventory[]">&nbsp;&nbsp;Notes
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Comments".',') !== false) { echo " checked"; } ?> value="Comments" name="inventory[]">&nbsp;&nbsp;Comments
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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Rent Price".',') !== false) { echo " checked"; } ?> value="Rent Price" name="inventory[]">&nbsp;&nbsp;Rent Price
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Rental Days".',') !== false) { echo " checked"; } ?> value="Rental Days" name="inventory[]">&nbsp;&nbsp;Rental Days
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Rental Weeks".',') !== false) { echo " checked"; } ?> value="Rental Weeks" name="inventory[]">&nbsp;&nbsp;Rental Weeks
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Rental Months".',') !== false) { echo " checked"; } ?> value="Rental Months" name="inventory[]">&nbsp;&nbsp;Rental Months
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Rental Years".',') !== false) { echo " checked"; } ?> value="Rental Years" name="inventory[]">&nbsp;&nbsp;Rental Years
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Reminder/Alert".',') !== false) { echo " checked"; } ?> value="Reminder/Alert" name="inventory[]">&nbsp;&nbsp;Reminder/Alert

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Daily".',') !== false) { echo " checked"; } ?> value="Daily" name="inventory[]">&nbsp;&nbsp;Daily
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Weekly".',') !== false) { echo " checked"; } ?> value="Weekly" name="inventory[]">&nbsp;&nbsp;Weekly
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Monthly".',') !== false) { echo " checked"; } ?> value="Monthly" name="inventory[]">&nbsp;&nbsp;Monthly
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Annually".',') !== false) { echo " checked"; } ?> value="Annually" name="inventory[]">&nbsp;&nbsp;Annually
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."#Of Days".',') !== false) { echo " checked"; } ?> value="#Of Days" name="inventory[]">&nbsp;&nbsp;#Of Days
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."#Of Hours".',') !== false) { echo " checked"; } ?> value="#Of Hours" name="inventory[]">&nbsp;&nbsp;#Of Hours

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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."#Of Kilometers".',') !== false) { echo " checked"; } ?> value="#Of Kilometers" name="inventory[]">&nbsp;&nbsp;#Of Kilometers
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."#Of Miles".',') !== false) { echo " checked"; } ?> value="#Of Miles" name="inventory[]">&nbsp;&nbsp;#Of Miles

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_bom" >
                            Bill of Material<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_bom" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Bill of Material".',') !== false) { echo " checked"; } ?> value="Bill of Material" name="inventory[]">&nbsp;&nbsp;Bill of Material

                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_supplimentary" >
                            Supplimentary Products<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_supplimentary" class="panel-collapse collapse">
                    <div class="panel-body">

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Supplimentary Products".',') !== false) { echo " checked"; } ?> value="Supplimentary Products" name="inventory[]">&nbsp;&nbsp;Supplimentary Products

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
						<input type="checkbox" <?php if (strpos($inventory_config, ','."Include in P.O.S.".',') !== false) {
                            echo " checked"; } ?> value="Include in P.O.S." name="inventory[]">&nbsp;&nbsp;Include in Point of Sale
                            <input type="checkbox" <?php if (strpos($inventory_config, ','."Include in Purchase Orders".',') !== false) {
                            echo " checked"; } ?> value="Include in Purchase Orders" name="inventory[]">&nbsp;&nbsp;Include in Purchase Orders
                            <input type="checkbox" <?php if (strpos($inventory_config, ','."Include in Sales Orders".',') !== false) {
                            echo " checked"; } ?> value="Include in Sales Orders" name="inventory[]">&nbsp;&nbsp;Include in <?= SALES_ORDER_TILE ?>
                            <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in Product".',') !== false) {
                            echo " checked"; } ?> value="Include in Product" name="inventory[]">&nbsp;&nbsp;Include in Product
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

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Min Amount".',') !== false) { echo " checked"; } ?> value="Min Amount" name="inventory[]">&nbsp;&nbsp;Min Amount
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Max Amount".',') !== false) { echo " checked"; } ?> value="Max Amount" name="inventory[]">&nbsp;&nbsp;Max Amount

                    </div>
                </div>
            </div>

			<div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_history" >
                            Inventory History<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_history" class="panel-collapse collapse">
                    <div class="panel-body">
						<input type="checkbox" <?php echo (strpos($inventory_config, ','."Change Log".',') !== false ? " checked" : ''); ?> value="Change Log" name="inventory[]">&nbsp;&nbsp;Inventory Change Log
					</div>
                </div>
            </div>
        </div>


        </div>

        <div class="form-group">
            <div class="col-sm-6">
				<?php if(get_config($dbc, 'inventory_default_select_all') == 1) { ?>
					<div class="double-gap-bottom"><a href="inventory.php?category=dispall_31V2irt2u3e5S3s1f2ADe3_31" class="btn brand-btn btn-lg">Back</a></div>
				<?php } else { ?>
					<div class="double-gap-bottom"><a href="inventory.php?category=Top" class="btn brand-btn btn-lg">Back</a></div>
				<?php } ?>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
            </div>
            <div class="col-sm-6">
                <button	type="submit" name="inv_field" value="inv_field" class="btn brand-btn btn-lg pull-right">Submit</button>
            </div>
        </div>

    <?php }
    ?>

	<?php if($_GET['type'] == 'dashboard_tab'): ?>
		<div class="form-group">
			<div class="col-sm-12"><?php
                $inventory_setting = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `value` FROM `inventory_setting` WHERE `inventorysettingid` = 1"));
                $set_check_value = $inventory_setting['value'];

                $rs_checked			= '';
                $bom_checked		= '';
                $bomc_checked		= '';
                $writeoff_checked	= '';
                $checklists_checked	= '';
                $orderlists_checked	= '';
                $checklist_orders	= '';

                if (strpos($set_check_value, "rs") !== false) {
                    $rs_checked = 'checked';
                }

                if (strpos($set_check_value, "bom") !== false) {
                    $bom_checked = 'checked';
                }

                if (strpos($set_check_value, "bomc") !== false) {
                    $bomc_checked = 'checked';
                }

                if (strpos($set_check_value, "writeoff") !== false) {
                    $writeoff_checked = 'checked';
                }

                if (strpos($set_check_value, "checklists") !== false) {
                    $checklists_checked = 'checked';
                }

                if (strpos($set_check_value, "orderlists") !== false) {
                    $orderlists_checked = 'checked';
                }

                if (strpos($set_check_value, "checklist_orders") !== false) {
                    $checklist_orders = 'checked';
                } ?>

                <div class="panel-group" id="accordion2">
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dashboard_tabs" >
                                    Show / Hide Dashboard Sub Tabs<span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>

                        <div id="collapse_dashboard_tabs" class="panel-collapse collapse">
                            <div id="no-more-tables" class="panel-body">
                                <table class="table" cellpadding="10" border="2">
                                    <tr>
                                        <td width="25%">
                                            <input type="checkbox" <?php echo $rs_checked; ?> value="rs" name="dashboard_tab_cx[]">&nbsp;&nbsp;Recieve Shipment
                                        </td>
                                        <td width="25%">
                                            <input type="checkbox" <?php echo $bom_checked; ?> value="bom" name="dashboard_tab_cx[]">&nbsp;&nbsp;Bill of Material
                                        </td>
                                        <td width="25%">
                                            <input type="checkbox" <?php echo $bomc_checked; ?> value="bomc" name="dashboard_tab_cx[]">&nbsp;&nbsp;Bill of Material (Consumables)
                                        </td>
                                        <td width="25%">
                                            <input type="checkbox" <?php echo $writeoff_checked; ?> value="writeoff" name="dashboard_tab_cx[]">&nbsp;&nbsp;Waste / Write-Off
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <input type="checkbox" <?php echo $checklists_checked; ?> value="checklists" name="dashboard_tab_cx[]">&nbsp;&nbsp;Checklists
                                        </td>
                                        <td>
                                            <input type="checkbox" <?php echo $orderlists_checked; ?> value="orderlists" name="dashboard_tab_cx[]">&nbsp;&nbsp;Order Lists
                                        </td>
                                        <td>
                                            <input type="checkbox" <?php echo $checklist_orders; ?> value="checklist_orders" name="dashboard_tab_cx[]">&nbsp;&nbsp;Order Checklists
                                        </td>
                                        <td colspan="1">&nbsp;</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-6">
                        <?php if(get_config($dbc, 'inventory_default_select_all') == 1) { ?>
                            <div class="double-gap-bottom"><a href="inventory.php?category=dispall_31V2irt2u3e5S3s1f2ADe3_31" class="btn brand-btn btn-lg">Back</a></div>
                        <?php } else { ?>
                            <div class="double-gap-bottom"><a href="inventory.php?category=Top" class="btn brand-btn btn-lg">Back</a></div>
                        <?php } ?>
                    </div>
                    <div class="col-sm-6">
                        <button	type="submit" name="dashboard_tab" value="dashboard_tab" class="btn brand-btn btn-lg pull-right">Submit</button>
                    </div>
                </div>
			</div>
		</div>
	<?php endif; ?>

    <?php if($_GET['type'] == 'dashboard') { ?>
        <div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label">
				<span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose your desired tab. These will be visible to you on the Inventory Dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				Tabs:
			</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Tab..." id="tab_dashboard" name="tab_dashboard" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php
                    $tabs = get_config($dbc, 'inventory_tabs');
                    $each_tab = explode('#*#', $tabs);
                    foreach ($each_tab as $cat_tab) {
						$url_tab = preg_replace('/[^a-z]/','',strtolower($cat_tab));
                        if ($invtype == $url_tab) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        echo "<option ".$selected." value='". $url_tab."'>".$cat_tab.'</option>';
                    }
					
					$cat_list = explode('#*#',get_config($dbc, 'inventory_tabs'));
					foreach($cat_list as $each_cat) {
						if($invtype == preg_replace('/[^a-z]/','',strtolower($each_cat))) {
							$invtype = filter_var($each_cat,FILTER_SANITIZE_STRING);
						}
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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Description".',') !== false) { echo " checked"; } ?> value="Description" name="inventory_dashboard[]">&nbsp;&nbsp;Description
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Category".',') !== false) { echo " checked"; } ?> value="Category" name="inventory_dashboard[]">&nbsp;&nbsp;Tab
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Subcategory".',') !== false) { echo " checked"; } ?> value="Subcategory" name="inventory_dashboard[]">&nbsp;&nbsp;Subtab
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Name".',') !== false) { echo " checked"; } ?> value="Name" name="inventory_dashboard[]">&nbsp;&nbsp;Name
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Product Name".',') !== false) { echo " checked"; } ?> value="Product Name" name="inventory_dashboard[]">&nbsp;&nbsp;Product Name
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Type".',') !== false) { echo " checked"; } ?> value="Type" name="inventory_dashboard[]">&nbsp;&nbsp;Type
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Color".',') !== false) { echo " checked"; } ?> value="Color" name="inventory_dashboard[]">&nbsp;&nbsp;Color

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Code".',') !== false) { echo " checked"; } ?> value="Code" name="inventory_dashboard[]">&nbsp;&nbsp;Code
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."ID #".',') !== false) { echo " checked"; } ?> value="ID #" name="inventory_dashboard[]">&nbsp;&nbsp;ID #
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Item SKU".',') !== false) { echo " checked"; } ?> value="Item SKU" name="inventory_dashboard[]">&nbsp;&nbsp;Item SKU
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Part #".',') !== false) { echo " checked"; } ?> value="Part #" name="inventory_dashboard[]">&nbsp;&nbsp;Part #
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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Average Cost".',') !== false) { echo " checked"; } ?> value="Average Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Average Cost
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Unit Cost".',') !== false) { echo " checked"; } ?> value="Unit Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Unit Cost
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Cost".',') !== false) { echo " checked"; } ?> value="Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Cost
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."CDN Cost Per Unit".',') !== false) { echo " checked"; } ?> value="CDN Cost Per Unit" name="inventory_dashboard[]">&nbsp;&nbsp;CDN Cost Per Unit
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."USD Cost Per Unit".',') !== false) { echo " checked"; } ?> value="USD Cost Per Unit" name="inventory_dashboard[]">&nbsp;&nbsp;USD Cost Per Unit
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Drum Unit Cost".',') !== false) { echo " checked"; } ?> value="Drum Unit Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Drum Unit Cost
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Tote Unit Cost".',') !== false) { echo " checked"; } ?> value="Tote Unit Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Tote Unit Cost
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."COGS".',') !== false) { echo " checked"; } ?> value="COGS" name="inventory_dashboard[]">&nbsp;&nbsp;COGS GL Code
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."USD Invoice".',') !== false) { echo " checked"; } ?> value="USD Invoice" name="inventory_dashboard[]">&nbsp;&nbsp;USD Invoice

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Vendor".',') !== false) { echo " checked"; } ?> value="Vendor" name="inventory_dashboard[]">&nbsp;&nbsp;Vendor
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Purchase Cost".',') !== false) { echo " checked"; } ?> value="Purchase Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Purchase Cost
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Date Of Purchase".',') !== false) { echo " checked"; } ?> value="Date Of Purchase" name="inventory_dashboard[]">&nbsp;&nbsp;Date Of Purchase

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Shipping Rate".',') !== false) { echo " checked"; } ?> value="Shipping Rate" name="inventory_dashboard[]">&nbsp;&nbsp;Shipping Rate
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."Shipping Cash".',') !== false) { echo " checked"; } ?> value="Shipping Cash" name="inventory_dashboard[]">&nbsp;&nbsp;Shipping Cash
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Freight Charge".',') !== false) { echo " checked"; } ?> value="Freight Charge" name="inventory_dashboard[]">&nbsp;&nbsp;Freight Charge
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Exchange Rate".',') !== false) { echo " checked"; } ?> value="Exchange Rate" name="inventory_dashboard[]">&nbsp;&nbsp;Exchange Rate
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Exchange $".',') !== false) { echo " checked"; } ?> value="Exchange $" name="inventory_dashboard[]">&nbsp;&nbsp;Exchange $

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Sell Price".',') !== false) { echo " checked"; } ?> value="Sell Price" name="inventory_dashboard[]">&nbsp;&nbsp;Sell Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Final Retail Price".',') !== false) { echo " checked"; } ?> value="Final Retail Price" name="inventory_dashboard[]">&nbsp;&nbsp;Final Retail Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Wholesale Price".',') !== false) { echo " checked"; } ?> value="Wholesale Price" name="inventory_dashboard[]">&nbsp;&nbsp;Wholesale Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Commercial Price".',') !== false) { echo " checked"; } ?> value="Commercial Price" name="inventory_dashboard[]">&nbsp;&nbsp;Commercial Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Client Price".',') !== false) { echo " checked"; } ?> value="Client Price" name="inventory_dashboard[]">&nbsp;&nbsp;Client Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Preferred Price".',') !== false) { echo " checked"; } ?> value="Preferred Price" name="inventory_dashboard[]">&nbsp;&nbsp;Preferred Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Admin Price".',') !== false) { echo " checked"; } ?> value="Admin Price" name="inventory_dashboard[]">&nbsp;&nbsp;Admin Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Web Price".',') !== false) { echo " checked"; } ?> value="Web Price" name="inventory_dashboard[]">&nbsp;&nbsp;Web Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Clearance Price".',') !== false) { echo " checked"; } ?> value="Clearance Price" name="inventory_dashboard[]">&nbsp;&nbsp;Clearance Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Commission Price".',') !== false) { echo " checked"; } ?> value="Commission Price" name="inventory_dashboard[]">&nbsp;&nbsp;Commission Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."MSRP".',') !== false) { echo " checked"; } ?> value="MSRP" name="inventory_dashboard[]">&nbsp;&nbsp;MSRP
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Unit Price".',') !== false) { echo " checked"; } ?> value="Unit Price" name="inventory_dashboard[]">&nbsp;&nbsp;Unit Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Unit Cost".',') !== false) { echo " checked"; } ?> value="Unit Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Unit Cost
						<input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Purchase Order Price".',') !== false) { echo " checked"; } ?> value="Purchase Order Price" name="inventory_dashboard[]">&nbsp;&nbsp;Purchase Order Price
						<input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Sales Order Price".',') !== false) { echo " checked"; } ?> value="Sales Order Price" name="inventory_dashboard[]">&nbsp;&nbsp;<?= SALES_ORDER_NOUN ?> Price
                        <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Cost".',') !== false) { echo " checked"; } ?> value="Drum Unit Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Drum Unit Cost
                        <input type="checkbox" <?php if (strpos($value_config, ','."Drum Unit Price".',') !== false) { echo " checked"; } ?> value="Drum Unit Price" name="inventory_dashboard[]">&nbsp;&nbsp;Drum Unit Price
                        <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Cost".',') !== false) { echo " checked"; } ?> value="Tote Unit Cost" name="inventory_dashboard[]">&nbsp;&nbsp;Tote Unit Cost
                        <input type="checkbox" <?php if (strpos($value_config, ','."Tote Unit Price".',') !== false) { echo " checked"; } ?> value="Tote Unit Price" name="inventory_dashboard[]">&nbsp;&nbsp;Tote Unit Price
                        <input type="checkbox" <?php if (strpos($value_config, ','."WCB Price".',') !== false) { echo " checked"; } ?> value="WCB Price" name="inventory_dashboard[]">&nbsp;&nbsp;WCB Price
                        <input type="checkbox" <?php if (strpos($value_config, ','."Suggested Retail Price".',') !== false) { echo " checked"; } ?> value="Suggested Retail Price" name="inventory_dashboard[]">&nbsp;&nbsp;Suggested Retail Price
                        <input type="checkbox" <?php if (strpos($value_config, ','."Rush Price".',') !== false) { echo " checked"; } ?> value="Rush Price" name="inventory_dashboard[]">&nbsp;&nbsp;Rush Price

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Markup By $".',') !== false) { echo " checked"; } ?> value="Markup By $" name="inventory_dashboard[]">&nbsp;&nbsp;Markup By $
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Markup By %".',') !== false) { echo " checked"; } ?> value="Markup By %" name="inventory_dashboard[]">&nbsp;&nbsp;Markup By %

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Current Stock".',') !== false) { echo " checked"; } ?> value="Current Stock" name="inventory_dashboard[]">&nbsp;&nbsp;Current Stock
                        <!-- Taken out to remove confusion between quantity and current inventory <input type="checkbox" <?php //if (strpos($inventory_dashboard_config, ','."Current Inventory".',') !== false) { echo " checked"; } ?> value="Current Inventory" name="inventory_dashboard[]">&nbsp;&nbsp;Current Inventory-->
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Quantity".',') !== false) { echo " checked"; } ?> value="Quantity" name="inventory_dashboard[]">&nbsp;&nbsp;Quantity
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Variance".',') !== false) { echo " checked"; } ?> value="Variance" name="inventory_dashboard[]">&nbsp;&nbsp;GL Code
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Write-offs".',') !== false) { echo " checked"; } ?> value="Write-offs" name="inventory_dashboard[]">&nbsp;&nbsp;Write-offs

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Buying Units".',') !== false) { echo " checked"; } ?> value="Buying Units" name="inventory_dashboard[]">&nbsp;&nbsp;Buying Units
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Selling Units".',') !== false) { echo " checked"; } ?> value="Selling Units" name="inventory_dashboard[]">&nbsp;&nbsp;Selling Units
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Stocking Units".',') !== false) { echo " checked"; } ?> value="Stocking Units" name="inventory_dashboard[]">&nbsp;&nbsp;Stocking Units

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Location".',') !== false) { echo " checked"; } ?> value="Location" name="inventory_dashboard[]">&nbsp;&nbsp;Location
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."LSD".',') !== false) { echo " checked"; } ?> value="LSD" name="inventory_dashboard[]">&nbsp;&nbsp;LSD

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Size".',') !== false) { echo " checked"; } ?> value="Size" name="inventory_dashboard[]">&nbsp;&nbsp;Size
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Weight".',') !== false) { echo " checked"; } ?> value="Weight" name="inventory_dashboard[]">&nbsp;&nbsp;Weight

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Min Max".',') !== false) { echo " checked"; } ?> value="Min Max" name="inventory_dashboard[]">&nbsp;&nbsp;Min Max
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Min Bin".',') !== false) { echo " checked"; } ?> value="Min Bin" name="inventory_dashboard[]">&nbsp;&nbsp;Min Bin

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Estimated Hours".',') !== false) { echo " checked"; } ?> value="Estimated Hours" name="inventory_dashboard[]">&nbsp;&nbsp;Estimated Hours
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Actual Hours".',') !== false) { echo " checked"; } ?> value="Actual Hours" name="inventory_dashboard[]">&nbsp;&nbsp;Actual Hours

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Minimum Billable".',') !== false) { echo " checked"; } ?> value="Minimum Billable" name="inventory_dashboard[]">&nbsp;&nbsp;Minimum Billable
                        <input type="checkbox" <?php if (strpos($inventory_config, ','."GL Revenue".',') !== false) { echo " checked"; } ?> value="GL Revenue" name="inventory_dashboard[]">&nbsp;&nbsp;GL Revenue

                        <input type="checkbox" <?php if (strpos($inventory_config, ','."GL Assets".',') !== false) { echo " checked"; } ?> value="GL Assets" name="inventory_dashboard[]">&nbsp;&nbsp;GL Assets

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Quote Description".',') !== false) { echo " checked"; } ?> value="Quote Description" name="inventory_dashboard[]">&nbsp;&nbsp;Quote Description

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Status".',') !== false) { echo " checked"; } ?> value="Status" name="inventory_dashboard[]">&nbsp;&nbsp;Status

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
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Display On Website".',') !== false) { echo " checked"; } ?> value="Display On Website" name="inventory_dashboard[]">&nbsp;&nbsp;Display On Website
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Featured On Website".',') !== false) { echo " checked"; } ?> value="Featured On Website" name="inventory_dashboard[]">&nbsp;&nbsp;Featured On Website
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."New Item".',') !== false) { echo " checked"; } ?> value="New Item" name="inventory_dashboard[]">&nbsp;&nbsp;Display Item As New On Website
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Item On Sale".',') !== false) { echo " checked"; } ?> value="Item On Sale" name="inventory_dashboard[]">&nbsp;&nbsp;Display Item As On Sale On Website
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Item On Clearance".',') !== false) { echo " checked"; } ?> value="Item On Clearance" name="inventory_dashboard[]">&nbsp;&nbsp;Display Item As Clearance On Website
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
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Main Image".',') !== false) { echo " checked"; } ?> value="Main Image" name="inventory_dashboard[]">&nbsp;&nbsp;Main Image
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Spec Sheet".',') !== false) { echo " checked"; } ?> value="Spec Sheet" name="inventory_dashboard[]">&nbsp;&nbsp;Spec Sheet
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Notes".',') !== false) { echo " checked"; } ?> value="Notes" name="inventory_dashboard[]">&nbsp;&nbsp;Notes
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Comments".',') !== false) { echo " checked"; } ?> value="Comments" name="inventory_dashboard[]">&nbsp;&nbsp;Comments

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rent Price".',') !== false) { echo " checked"; } ?> value="Rent Price" name="inventory_dashboard[]">&nbsp;&nbsp;Rent Price
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rental Days".',') !== false) { echo " checked"; } ?> value="Rental Days" name="inventory_dashboard[]">&nbsp;&nbsp;Rental Days
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rental Weeks".',') !== false) { echo " checked"; } ?> value="Rental Weeks" name="inventory_dashboard[]">&nbsp;&nbsp;Rental Weeks
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rental Months".',') !== false) { echo " checked"; } ?> value="Rental Months" name="inventory_dashboard[]">&nbsp;&nbsp;Rental Months
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Rental Years".',') !== false) { echo " checked"; } ?> value="Rental Years" name="inventory_dashboard[]">&nbsp;&nbsp;Rental Years
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Reminder/Alert".',') !== false) { echo " checked"; } ?> value="Reminder/Alert" name="inventory_dashboard[]">&nbsp;&nbsp;Reminder/Alert

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Daily".',') !== false) { echo " checked"; } ?> value="Daily" name="inventory_dashboard[]">&nbsp;&nbsp;Daily
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Weekly".',') !== false) { echo " checked"; } ?> value="Weekly" name="inventory_dashboard[]">&nbsp;&nbsp;Weekly
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Monthly".',') !== false) { echo " checked"; } ?> value="Monthly" name="inventory_dashboard[]">&nbsp;&nbsp;Monthly
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Annually".',') !== false) { echo " checked"; } ?> value="Annually" name="inventory_dashboard[]">&nbsp;&nbsp;Annually
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."#Of Days".',') !== false) { echo " checked"; } ?> value="#Of Days" name="inventory_dashboard[]">&nbsp;&nbsp;#Of Days
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."#Of Hours".',') !== false) { echo " checked"; } ?> value="#Of Hours" name="inventory_dashboard[]">&nbsp;&nbsp;#Of Hours

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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."#Of Kilometers".',') !== false) { echo " checked"; } ?> value="#Of Kilometers" name="inventory_dashboard[]">&nbsp;&nbsp;#Of Kilometers
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."#Of Miles".',') !== false) { echo " checked"; } ?> value="#Of Miles" name="inventory_dashboard[]">&nbsp;&nbsp;#Of Miles

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
						<input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in P.O.S.".',') !== false) {
                            echo " checked"; } ?> value="Include in P.O.S." name="inventory_dashboard[]">&nbsp;&nbsp;Include in Point of Sale
                            <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in Purchase Orders".',') !== false) {
                            echo " checked"; } ?> value="Include in Purchase Orders" name="inventory_dashboard[]">&nbsp;&nbsp;Include in Purchase Orders
                            <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in Sales Orders".',') !== false) {
                            echo " checked"; } ?> value="Include in Sales Orders" name="inventory_dashboard[]">&nbsp;&nbsp;Include in <?= SALES_ORDER_TILE ?>
                            <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Include in Product".',') !== false) {
                            echo " checked"; } ?> value="Include in Product" name="inventory_dashboard[]">&nbsp;&nbsp;Include in Product
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

                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Min Amount".',') !== false) { echo " checked"; } ?> value="Min Amount" name="inventory_dashboard[]">&nbsp;&nbsp;Min Amount
                        <input type="checkbox" <?php if (strpos($inventory_dashboard_config, ','."Max Amount".',') !== false) { echo " checked"; } ?> value="Max Amount" name="inventory_dashboard[]">&nbsp;&nbsp;Max Amount

                    </div>
                </div>
            </div>

			<div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_history" >
                            History<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_history" class="panel-collapse collapse">
                    <div class="panel-body">
						<input type="checkbox" <?php echo (strpos($inventory_dashboard_config, ','."History".',') !== false ? " checked" : ''); ?> value="Change Log" name="inventory_dashboard[]">&nbsp;&nbsp;Inventory Change Log
					</div>
                </div>
            </div>
        </div>

        <br>

        <div class="form-group">
            <div class="col-sm-6">
				<?php if(get_config($dbc, 'inventory_default_select_all') == 1) { ?>
					<div class="double-gap-bottom"><a href="inventory.php?category=dispall_31V2irt2u3e5S3s1f2ADe3_31" class="btn brand-btn btn-lg">Back</a></div>
				<?php } else { ?>
					<div class="double-gap-bottom"><a href="inventory.php?category=Top" class="btn brand-btn btn-lg">Back</a></div>
				<?php } ?>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
            </div>
            <div class="col-sm-6">
                <button	type="submit" name="inv_dashboard"	value="inv_dashboard" class="btn brand-btn btn-lg pull-right">Submit</button>
            </div>
        </div>

		<div class="clearfix"></div>

    <?php }

    if($_GET['type'] == 'general') { ?>

        <h3 class="gap-left">Min Bin Emails</h3>

        <div class="notice popover-examples double-gap-top triple-gap-bottom">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span> Adding and configuring the email below will tell the software who to alert when an inventory item hits the minimum quantity you want.</div>
            <div class="clearfix"></div>
        </div>

        <div class="panel-group" id="accordion2">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_minbin_email">
                            Add Min Bin Email Address<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_minbin_email" class="panel-collapse collapse">
                    <div id="no-more-tables" class="panel-body">
                        <div class="form-group">
                            <label class="col-sm-4 control-label"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Add a verified email here. Once the inventory has reached the Min Bin, a notification will be sent."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Email Address:</label>
                            <div class="col-sm-8">
                                <input name="inventory_minbin_email" value="<?php echo get_config($dbc, 'inventory_minbin_email'); ?>" placeholder="Add an email address" type="text" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_minbin_email_config">
                            Configure Min Bin Email<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_minbin_email_config" class="panel-collapse collapse">
                    <div id="no-more-tables" class="panel-body">
                        <div class="form-group">
							<label class="col-sm-4 control-label">Min Bin Email Subject:</label>
							<div class="col-sm-8"><?php
                                $inventory_minbin_subject = get_config($dbc, 'inventory_minbin_subject');
                                if ( empty ($inventory_minbin_subject) ) {
                                    $inventory_minbin_subject = 'Inventory Min Bin Alert';
                                } ?>
								<input type="text" name="inventory_minbin_subject" class="form-control" value="<?php echo $inventory_minbin_subject; ?>" />
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Min Bin Email Body:</label>
							<div class="col-sm-8"><?php
                                $inventory_minbin_body = get_config($dbc, 'inventory_minbin_body');
                                if ( empty ($inventory_minbin_body) ) {
                                    $inventory_minbin_body = '
                                        <h1>Inventory Min Bin Alert</h1>
                                        <p>Below inventory item(s) have reached the minimum quantity. Please re-order as necessary.</p>';
                                } ?>
								<textarea name="inventory_minbin_body" class="form-control"><?php echo $inventory_minbin_body; ?></textarea>
							</div>
						</div>
                    </div>
                </div>
            </div>
        </div><!-- .panel-group -->

		<div class="form-group">
			<div class="col-sm-6">
				<?php if(get_config($dbc, 'inventory_default_select_all') == 1) { ?>
					<div class="double-gap-bottom"><a href="inventory.php?category=dispall_31V2irt2u3e5S3s1f2ADe3_31" class="btn brand-btn btn-lg">Back</a></div>
				<?php } else { ?>
					<div class="double-gap-bottom"><a href="inventory.php?category=Top" class="btn brand-btn btn-lg">Back</a></div>
				<?php } ?>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button	type="submit" name="general" value="general" class="btn brand-btn btn-lg pull-right">Submit</button>
			</div>
		</div><?php
	} ?>

<?php if($_GET['type'] == 'digi_count') { ?>
		<div class="form-group">
            <label for="fax_number"	class="col-sm-4	control-label"><span class="popover-examples list-inline"><a class="" style="margin:7px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Digital inventory count functionality is ideal for users wanting to confirm that their actual quantity of inventory matches the quantity of inventory in their software."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Enable Digital Inventory Count:</label>
            <div class="col-sm-8">
			<?php
			$checked = '';
			$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_digi_count'"));
			if($get_config['configid'] > 0) {
				$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_digi_count'"));
				if($get_config['value'] == '1') {
					$checked = 'checked';
				}
			}
			?>
              <input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?>  name='' class='show_digi_count' value='1'>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-6">
                <?php if(get_config($dbc, 'inventory_default_select_all') == 1) { ?>
					<div class="double-gap-bottom"><a href="inventory.php?category=dispall_31V2irt2u3e5S3s1f2ADe3_31" class="btn brand-btn btn-lg">Back</a></div>
                <?php } else { ?>
					<div class="double-gap-bottom"><a href="inventory.php?category=Top" class="btn brand-btn btn-lg">Back</a></div>
                <?php } ?>
                <!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
            </div>
            <div class="col-sm-6">
                <button	type="submit" name="add_tab" value="add_tab" class="btn brand-btn btn-lg pull-right">Submit</button>
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
			$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='show_impexp_inv'"));
			if($get_config['configid'] > 0) {
				$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='show_impexp_inv'"));
				if($get_config['value'] == '1') {
					$checked = 'checked';
				}
			}
			?>
              <input type='checkbox' style='width:20px; height:20px;' <?php echo $checked; ?>  name='' class='show_impexp_inv' value='1'>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-6">
				<?php if(get_config($dbc, 'inventory_default_select_all') == 1) { ?>
					<div class="double-gap-bottom"><a href="inventory.php?category=dispall_31V2irt2u3e5S3s1f2ADe3_31" class="btn brand-btn btn-lg">Back</a></div>
				<?php } else { ?>
					<div class="double-gap-bottom"><a href="inventory.php?category=Top" class="btn brand-btn btn-lg">Back</a></div>
				<?php } ?>
				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
            </div>
            <div class="col-sm-6">
                <button	type="submit" name="add_tab" value="add_tab" class="btn brand-btn btn-lg pull-right">Submit</button>
            </div>
        </div>

		<div class="clearfix"></div>

<?php } ?>
<?php if($_GET['type'] == 'pdfstyling') {
    include('field_config_style.php');
} ?>
<?php if($_GET['type'] == 'templates') {
    include('field_config_templates.php');
} ?>

<!--
<div class="form-group">
    <div class="col-sm-4 clearfix">
        <a href="inventory.php?category=Top" class="btn config-btn pull-right">Back</a>
    </div>
    <div class="col-sm-8">
        <button	type="submit" name="submit"	value="Submit" class="btn config-btn btn-lg	pull-right">Submit</button>
    </div>
</div>
-->
        

<?php if($_GET['type'] != 'pdfstyling' && $_GET['type'] != 'templates') { ?>
    </form>
<?php } ?>
</div>
</div>

<?php include ('../footer.php'); ?>