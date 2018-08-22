<?php
/*
Add Vendor
*/
include ('../include.php');
error_reporting(0);

if (isset($_POST['add_service'])) {

	if($_POST['new_service'] != '') {
		$service_type = filter_var($_POST['new_service'],FILTER_SANITIZE_STRING);
	} else {
		$service_type = filter_var($_POST['service_type'],FILTER_SANITIZE_STRING);
	}

	if($_POST['new_category'] != '') {
		$category = filter_var($_POST['new_category'],FILTER_SANITIZE_STRING);
	} else {
		$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
	}
    $invoice_description = filter_var(htmlentities($_POST['invoice_description']),FILTER_SANITIZE_STRING);
    $ticket_description = filter_var(htmlentities($_POST['ticket_description']),FILTER_SANITIZE_STRING);
    $name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
    $fee = filter_var($_POST['fee'],FILTER_SANITIZE_STRING);

    $service_code = filter_var($_POST['service_code'],FILTER_SANITIZE_STRING);
    $quantity = filter_var($_POST['quantity'],FILTER_SANITIZE_STRING);
    //$category = filter_var($_POST['category'],FILTER_SANITIZE_STRING);
    $heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
    $cost = filter_var($_POST['cost'],FILTER_SANITIZE_STRING);
    //$description = filter_var($_POST['description'],FILTER_SANITIZE_STRING);

    $description = filter_var(htmlentities($_POST['description']),FILTER_SANITIZE_STRING);

    if($_POST['same_desc'] == 1) {
        $quote_description = $description;
    } else {
        $quote_description = filter_var(htmlentities($_POST['quote_description']),FILTER_SANITIZE_STRING);
    }
    $final_retail_price = filter_var($_POST['final_retail_price'],FILTER_SANITIZE_STRING);
    $admin_price = filter_var($_POST['admin_price'],FILTER_SANITIZE_STRING);
    $wholesale_price = filter_var($_POST['wholesale_price'],FILTER_SANITIZE_STRING);
    $commercial_price = filter_var($_POST['commercial_price'],FILTER_SANITIZE_STRING);
    $client_price = filter_var($_POST['client_price'],FILTER_SANITIZE_STRING);
    $minimum_billable = filter_var($_POST['minimum_billable'],FILTER_SANITIZE_STRING);
	$purchase_order_price = filter_var($_POST['purchase_order_price'],FILTER_SANITIZE_STRING);
	$sales_order_price = filter_var($_POST['sales_order_price'],FILTER_SANITIZE_STRING);
    $hourly_rate = filter_var($_POST['hourly_rate'],FILTER_SANITIZE_STRING);
    $estimated_hours = filter_var($_POST['estimated_hours'],FILTER_SANITIZE_STRING);
    $actual_hours = filter_var($_POST['actual_hours'],FILTER_SANITIZE_STRING);
    $msrp = filter_var($_POST['msrp'],FILTER_SANITIZE_STRING);

    $unit_price = filter_var($_POST['unit_price'],FILTER_SANITIZE_STRING);
    $unit_cost = filter_var($_POST['unit_cost'],FILTER_SANITIZE_STRING);
    $rent_price = filter_var($_POST['rent_price'],FILTER_SANITIZE_STRING);
    $rental_days = filter_var($_POST['rental_days'],FILTER_SANITIZE_STRING);
    $rental_weeks = filter_var($_POST['rental_weeks'],FILTER_SANITIZE_STRING);
    $rental_months = filter_var($_POST['rental_months'],FILTER_SANITIZE_STRING);
    $rental_years = filter_var($_POST['rental_years'],FILTER_SANITIZE_STRING);
    $reminder_alert = filter_var($_POST['reminder_alert'],FILTER_SANITIZE_STRING);
    $daily = filter_var($_POST['daily'],FILTER_SANITIZE_STRING);
    $weekly = filter_var($_POST['weekly'],FILTER_SANITIZE_STRING);
    $monthly = filter_var($_POST['monthly'],FILTER_SANITIZE_STRING);
    $annually = filter_var($_POST['annually'],FILTER_SANITIZE_STRING);
    $total_days = filter_var($_POST['total_days'],FILTER_SANITIZE_STRING);
    $total_hours = filter_var($_POST['total_hours'],FILTER_SANITIZE_STRING);
    $total_km = filter_var($_POST['total_km'],FILTER_SANITIZE_STRING);
    $total_miles = filter_var($_POST['total_miles'],FILTER_SANITIZE_STRING);
	$include_in_pos = filter_var($_POST['include_in_pos'],FILTER_SANITIZE_STRING);
	$include_in_po = filter_var($_POST['include_in_po'],FILTER_SANITIZE_STRING);
	$include_in_so = filter_var($_POST['include_in_so'],FILTER_SANITIZE_STRING);
	$checklist = filter_var(implode('#*#',array_filter($_POST['checklist'])),FILTER_SANITIZE_STRING);

    $gst_exempt = $_POST['gst_exempt'];
    $appointment_type = filter_var($_POST['appointment_type'],FILTER_SANITIZE_STRING);

    if(empty($_POST['serviceid'])) {
        $query_insert_vendor = "INSERT INTO `services` (`service_type`, `category`, `service_code`, `heading`, `cost`, `description`, `quote_description`, `invoice_description`, `ticket_description`, `final_retail_price`, `admin_price`, `wholesale_price`, `commercial_price`, `client_price`, `purchase_order_price`, `sales_order_price`, `minimum_billable`, `hourly_rate`, `estimated_hours`, `actual_hours`, `msrp`, `name`, `fee`, `unit_price`, `unit_cost`, `rent_price`, `rental_days`, `rental_weeks`, `rental_months`, `rental_years`, `reminder_alert`, `daily`,`weekly`, `monthly`, `annually`,  `total_days`, `total_hours`, `total_km`, `total_miles` , `include_in_so`,`include_in_po`,`include_in_pos`, `gst_exempt`, `appointment_type`, `quantity`, `checklist`) VALUES ('$service_type', '$category', '$service_code', '$heading', '$cost', '$description', '$quote_description', '$invoice_description', '$ticket_description', '$final_retail_price', '$admin_price', '$wholesale_price', '$commercial_price', '$client_price', '$purchase_order_price', '$sales_order_price', '$minimum_billable', '$hourly_rate', '$estimated_hours', '$actual_hours', '$msrp', '$name', '$fee', '$unit_price', '$unit_cost', '$rent_price', '$rental_days', '$rental_weeks', '$rental_months', '$rental_years', '$reminder_alert', '$daily', '$weekly', '$monthly', '$annually', '$total_days', '$total_hours', '$total_km', '$total_miles', '$include_in_so', '$include_in_po', '$include_in_pos', '$gst_exempt', '$appointment_type', '$quantity', '$checklist')";
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $url = 'Added';
				$before_change = "";
				$history = "Service entry has been added. <br />";
	  		add_update_history($dbc, 'service_history', $history, '', $before_change);
    } else {
        $serviceid = $_POST['serviceid'];
        $query_update_vendor = "UPDATE `services` SET `service_type` = '$service_type', `category` = '$category',`service_code` = '$service_code', `heading` = '$heading', `cost` = '$cost', `description` = '$description', `quote_description` = '$quote_description', `invoice_description` = '$invoice_description', `ticket_description` = '$ticket_description', `final_retail_price` = '$final_retail_price', `admin_price` = '$admin_price', `wholesale_price` = '$wholesale_price', `commercial_price` = '$commercial_price', `client_price` = '$client_price', `purchase_order_price` = '$purchase_order_price', `sales_order_price` = '$sales_order_price', `minimum_billable` = '$minimum_billable', `hourly_rate` = '$hourly_rate', `estimated_hours` = '$estimated_hours', `actual_hours` = '$actual_hours', `msrp` = '$msrp', `name` = '$name', `fee` = '$fee', `unit_price` = '$unit_price', `unit_cost` = '$unit_cost', `rent_price` = '$rent_price', `rental_days` = '$rental_days', `rental_weeks` = '$rental_weeks', `rental_months` = '$rental_months', `rental_years` = '$rental_years', `reminder_alert` = '$reminder_alert', `daily` = '$daily', `weekly` = '$weekly', `monthly` = '$monthly', `annually` = '$annually', `total_days` = '$total_days', `total_hours` = '$total_hours', `total_km` = '$total_km', `total_miles` = '$total_miles', `include_in_so` = '$include_in_so', `include_in_po` = '$include_in_po', `include_in_pos` = '$include_in_pos', `gst_exempt` = '$gst_exempt', `appointment_type` = '$appointment_type', `quantity` = '$quantity', `checklist`='$checklist' WHERE `serviceid` = '$serviceid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
				$before_change = "";
				$history = "Service with service id $serviceid has been updated. <br />";
	  		add_update_history($dbc, 'service_history', $history, '', $before_change);
    }


    echo '<script type="text/javascript"> window.location.replace("services.php"); </script>';

 //   mysqli_close($dbc);//Close the DB Connection
}

?>
<script type="text/javascript">
$(document).ready(function() {
    $("#form1").submit(function( event ) {
        var service_type = $("#service_type").val();
        var category = $("input[name=category]").val();
        var heading = $("input[name=heading]").val();
        if (category == '' || heading == '' ) {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });

    $("#service_type").change(function() {
        if($("#service_type option:selected").text() == 'New Service') {
                $( "#new_service" ).show();
        } else {
            $( "#new_service" ).hide();
        }
    });

    $("#category").change(function() {
        if($("#category option:selected").text() == 'New Category') {
                $( "#new_category" ).show();
        } else {
            $( "#new_category" ).hide();
        }
    });

} );

</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('services');
?>
<div class="container">
  <div class="row">

    <h1>Services</h1>
	<div class="pad-left gap-top double-gap-bottom"><a href="services.php" class="btn config-btn">Back to Dashboard</a></div>

    <form id="form1" name="form1" method="post"	action="add_services.php" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT services FROM field_config"));
        $value_config = ','.$get_field_config['services'].',';

        $service_type = '';
        $category = '';
        $service_code = '';
        $quantity = '';
        $heading = '';
        $cost = '';
        $description = '';
        $quote_description = '';
        $invoice_description = '';
        $ticket_description = '';

        $final_retail_price = '';
        $name = '';
        $fee = '';
        $admin_price = '';
        $wholesale_price = '';
        $commercial_price = '';
        $client_price = '';
		$purchase_order_price = '';
		$sales_order_price = '';
        $minimum_billable = '';
        $hourly_rate = '';
        $estimated_hours = '';
        $actual_hours = '';
        $msrp = '';

        $unit_price = '';
        $unit_cost = '';
        $rent_price = '';
        $rental_days = '';
        $rental_weeks = '';
        $rental_months = '';
        $rental_years = '';
        $reminder_alert = '';
        $daily = '';
        $weekly = '';
        $monthly = '';
        $annually = '';
        $total_days = '';
        $total_hours = '';
        $total_km = '';
        $total_miles = '';
		$include_in_po = '';
		$include_in_so = '';
		$include_in_pos = '';
        $gst_exempt = '';
        $appointment_type = '';
		$checklist = [''];

        if(!empty($_GET['serviceid'])) {

            $serviceid = $_GET['serviceid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM services WHERE serviceid='$serviceid'"));

            $service_type = $get_contact['service_type'];
            $category = $get_contact['category'];
            $service_code = $get_contact['service_code'];
            $quantity = $get_contact['quantity'];
            $heading = $get_contact['heading'];
            $cost = $get_contact['cost'];
            $description = $get_contact['description'];
            $quote_description = $get_contact['quote_description'];
            $invoice_description = $get_contact['invoice_description'];
            $ticket_description = $get_contact['ticket_description'];
            $name = $get_contact['name'];
            $fee = $get_contact['fee'];

            $final_retail_price = $get_contact['final_retail_price'];
            $admin_price = $get_contact['admin_price'];
            $wholesale_price = $get_contact['wholesale_price'];
            $commercial_price = $get_contact['commercial_price'];
            $client_price = $get_contact['client_price'];
			$purchase_order_price = $get_contact['purchase_order_price'];
			$sales_order_price = $get_contact['sales_order_price'];
            $minimum_billable = $get_contact['minimum_billable'];
            $hourly_rate = $get_contact['hourly_rate'];
            $estimated_hours = $get_contact['estimated_hours'];
            $actual_hours = $get_contact['actual_hours'];
            $msrp = $get_contact['msrp'];

            $unit_price = $get_contact['unit_price'];
            $unit_cost = $get_contact['unit_cost'];
            $rent_price = $get_contact['rent_price'];
            $rental_days = $get_contact['rental_days'];
            $rental_weeks = $get_contact['rental_weeks'];
            $rental_months = $get_contact['rental_months'];
            $rental_years = $get_contact['rental_years'];
            $reminder_alert = $get_contact['reminder_alert'];
            $daily = $get_contact['daily'];
            $weekly = $get_contact['weekly'];
            $monthly = $get_contact['monthly'];
            $annually = $get_contact['annually'];
            $total_days = $get_contact['total_days'];
            $total_hours = $get_contact['total_hours'];
            $total_km = $get_contact['total_km'];
            $total_miles = $get_contact['total_miles'];
			$include_in_po = $get_contact['include_in_po'];
			$include_in_so = $get_contact['include_in_so'];
			$include_in_pos = $get_contact['include_in_pos'];
            $gst_exempt = $get_contact['gst_exempt'];
            $appointment_type = $get_contact['appointment_type'];
			$checklist = explode('#*#', $get_contact['checklist']);
        ?>
        <input type="hidden" id="serviceid" name="serviceid" value="<?php echo $serviceid ?>" />
        <?php   }      ?>

        <?php if (strpos($value_config, ','."Service Type".',') !== FALSE) { ?>
       <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">
			<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Select a service that you have created in the settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Service Type<span class="hp-red">*</span>:
		</label>
        <div class="col-sm-8">
            <select id="service_type" name="service_type" class="chosen-select-deselect form-control" width="380">
                <option value=''></option>
                <?php
                $query = mysqli_query($dbc,"SELECT distinct(service_type) FROM services order by service_type");
                while($row = mysqli_fetch_array($query)) {
                    if ($service_type == $row['service_type']) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $row['service_type']."'>".$row['service_type'].'</option>';

                }
                echo "<option value = 'Other'>New Service</option>";
                ?>
            </select>
        </div>
      </div>

       <div class="form-group" id="new_service" style="display: none;">
        <label for="travel_task" class="col-sm-4 control-label">New Service Name:
        </label>
        <div class="col-sm-8">
            <input name="new_service" type="text" class="form-control" />
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Category".',') !== FALSE) { ?>

      <!-- <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Category<span class="hp-red">*</span>:</label>
        <div class="col-sm-8">
          <input name="category" value="<?php echo $category; ?>" type="text" id="name" class="form-control">
        </div>
      </div>
      -->

       <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">
			<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Select from a saved category, or choose New Category on the bottom."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Category<span class="hp-red">*</span>:
		</label>
        <div class="col-sm-8">
            <select id="category" name="category" class="chosen-select-deselect form-control" width="380">
                <option value=''></option>
                <?php
                $query = mysqli_query($dbc,"SELECT distinct(category) FROM services order by category");
                while($row = mysqli_fetch_array($query)) {
                    if ($category == $row['category']) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $row['category']."'>".$row['category'].'</option>';

                }
                echo "<option value = 'Other'>New Category</option>";
                ?>
            </select>
        </div>
      </div>

       <div class="form-group" id="new_category" style="display: none;">
        <label for="travel_task" class="col-sm-4 control-label">New Category Name:
        </label>
        <div class="col-sm-8">
            <input name="new_category" type="text" class="form-control" />
        </div>
      </div>

      <?php } ?>

       <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">
			<span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Name your entry."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Heading<span class="hp-red">*</span>:
		</label>
        <div class="col-sm-8">
          <input name="heading" value="<?php echo $heading; ?>" type="text" id="name" class="form-control">
        </div>
      </div>
      <?php } ?>

       <?php if (strpos($value_config, ','."Name".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Name<span class="hp-red">*</span>:</label>
        <div class="col-sm-8">
          <input name="name" value="<?php echo $name; ?>" type="text" id="name" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Service Code".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Service Code:</label>
        <div class="col-sm-8">
          <input name="service_code" value="<?php echo $service_code; ?>" type="text" id="name" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Quantity".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Quantity:</label>
        <div class="col-sm-8">
          <input name="quantity" value="<?php echo $quantity; ?>" type="text" id="name" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Description".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="first_name[]" class="col-sm-4 control-label">Description:</label>
        <div class="col-sm-8">
          <textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Quote Description".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="first_name[]" class="col-sm-4 control-label"></label>
        <div class="col-sm-8">
          <input type="checkbox" value="1" name="same_desc">Check this if Quote Description is same as Description.
        </div>
      </div>

      <div class="form-group">
        <label for="first_name[]" class="col-sm-4 control-label">Quote Description:</label>
        <div class="col-sm-8">
          <textarea name="quote_description" rows="5" cols="50" class="form-control"><?php echo $quote_description; ?></textarea>
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Invoice Description".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Invoice Description:</label>
        <div class="col-sm-8">
          <textarea name="invoice_description" rows="5" cols="50" class="form-control"><?php echo $invoice_description; ?></textarea>
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Ticket Description".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label"><?= TICKET_NOUN ?> Description:</label>
        <div class="col-sm-8">
          <textarea name="ticket_description" rows="5" cols="50" class="form-control"><?php echo $ticket_description; ?></textarea>
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Checklist".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Checklist:</label>
        <div class="col-sm-8">
			<?php foreach($checklist as $item) { ?>
				<div name="checklist_row">
					<div class="col-sm-10"><input type="text" class="form-control" value="<?= $item ?>" name="checklist[]"></div>
					<div class="col-sm-2"><button class="btn brand-btn pull-right" onclick="$(this).closest('[name=checklist_row]').remove(); return false;" style="width:100%;" tabindex="-1">Delete</button></div>
				</div>
			<?php } ?>
			<button class="btn brand-btn pull-right" onclick="$(this).before($('[name=checklist_row]').last().clone()); $('[name^=checklist]').last().val('').focus(); return false;">Add</button>
        </div>
      </div>
      <?php } ?>

       <?php if (strpos($value_config, ','."Fee".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Fee<span class="hp-red">*</span>:</label>
        <div class="col-sm-8">
          <input name="fee" value="<?php echo $fee; ?>" type="text" id="name" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Cost".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Cost:</label>
        <div class="col-sm-8">
          <input name="cost" value="<?php echo $cost; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Final Retail Price:</label>
        <div class="col-sm-8">
          <input name="final_retail_price" value="<?php echo $final_retail_price; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

        <?php if (strpos($value_config, ','."Admin Price".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Admin Price:</label>
        <div class="col-sm-8">
          <input name="admin_price" value="<?php echo $admin_price; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Wholesale Price:</label>
        <div class="col-sm-8">
          <input name="wholesale_price" value="<?php echo $wholesale_price; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Commercial Price".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Commercial Price:</label>
        <div class="col-sm-8">
          <input name="commercial_price" value="<?php echo $commercial_price; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Client Price".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Client Price:</label>
        <div class="col-sm-8">
          <input name="client_price" value="<?php echo $client_price; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

	  <?php if (strpos($value_config, ','."Purchase Order Price".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Purchase Order Price:</label>
        <div class="col-sm-8">
          <input name="purchase_order_price" value="<?php echo $purchase_order_price; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

	  <?php if (strpos($value_config, ','."Sales Order Price".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label"><?= SALES_ORDER_NOUN ?> Price:</label>
        <div class="col-sm-8">
          <input name="sales_order_price" value="<?php echo $sales_order_price; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

	  <?php if (strpos($value_config, ','."Include in Sales Orders".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Include in <?= SALES_ORDER_TILE ?>:</label>
        <div class="col-sm-8">
          <input type='checkbox' style='width:20px; height:20px;' <?php if($include_in_so !== '' && $include_in_so !== NULL) { echo "checked"; } ?> name='include_in_so' class='' value='1'>
        </div>
      </div>
      <?php } ?>

	  <?php if (strpos($value_config, ','."Include in Purchase Orders".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Include in Purchase Orders:</label>
        <div class="col-sm-8">
          <input type='checkbox' style='width:20px; height:20px;' <?php if($include_in_po !== '' && $include_in_po !== NULL) { echo "checked"; } ?> name='include_in_po' class='' value='1'>
        </div>
      </div>
      <?php } ?>

	  <?php if (strpos($value_config, ','."Include in P.O.S.".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Include in <?= POS_ADVANCE_TILE ?>:</label>
        <div class="col-sm-8">
          <input type='checkbox' style='width:20px; height:20px;' <?php if($include_in_pos !== '' && $include_in_pos !== NULL) { echo "checked"; } ?> name='include_in_pos' class='' value='1'>
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Minimum Billable Hours:</label>
        <div class="col-sm-8">
          <input name="minimum_billable" value="<?php echo $minimum_billable; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Hourly Rate:</label>
        <div class="col-sm-8">
          <input name="hourly_rate" value="<?php echo $hourly_rate; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Estimated Hours:</label>
        <div class="col-sm-8">
          <input name="estimated_hours" value="<?php echo $estimated_hours; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."Actual Hours".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Actual Hours:</label>
        <div class="col-sm-8">
          <input name="actual_hours" value="<?php echo $actual_hours; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

      <?php if (strpos($value_config, ','."MSRP".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">MSRP:</label>
        <div class="col-sm-8">
          <input name="msrp" value="<?php echo $msrp; ?>" type="text" class="form-control">
        </div>
      </div>
      <?php } ?>

    <?php if (strpos($value_config, ','."Unit Price".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Unit Price:</label>
    <div class="col-sm-8">
      <input name="unit_price" value="<?php echo $unit_price; ?>" type="text" class="form-control">
    </div>
    </div>
    <?php } ?>

    <?php if (strpos($value_config, ','."Unit Cost".',') !== FALSE) { ?>
    <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Unit Cost:</label>
    <div class="col-sm-8">
      <input name="unit_cost" value="<?php echo $unit_cost; ?>" type="text" class="form-control">
    </div>
    </div>
    <?php } ?>

  <?php if (strpos($value_config, ','."Rent Price".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Rent Price:</label>
    <div class="col-sm-8">
      <input name="rent_price" value="<?php echo $rent_price; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>


  <?php if (strpos($value_config, ','."Rental Days".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Rental Days:</label>
    <div class="col-sm-8">
      <input name="rental_days" value="<?php echo $rental_days; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Rental Weeks".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Rental Weeks:</label>
    <div class="col-sm-8">
      <input name="rental_weeks" value="<?php echo $rental_weeks; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."Rental Months".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Rental Months:</label>
    <div class="col-sm-8">
      <input name="rental_months" value="<?php echo $rental_months; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."Rental Years".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Rental Years:</label>
    <div class="col-sm-8">
      <input name="rental_years" value="<?php echo $rental_years; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."Reminder/Alert".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Reminder/Alert:</label>
    <div class="col-sm-8">
      <input name="reminder_alert" value="<?php echo $reminder_alert; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>


  <?php if (strpos($value_config, ','."Daily".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Daily:</label>
    <div class="col-sm-8">
      <input name="daily" value="<?php echo $daily; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Weekly".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Weekly:</label>
    <div class="col-sm-8">
      <input name="weekly" value="<?php echo $weekly; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."Monthly".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Monthly:</label>
    <div class="col-sm-8">
      <input name="monthly" value="<?php echo $monthly; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."Annually".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">Annually:</label>
    <div class="col-sm-8">
      <input name="annually" value="<?php echo $annually; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."#Of Days".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">#Of Days:</label>
    <div class="col-sm-8">
      <input name="total_days" value="<?php echo $total_days; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."#Of Hours".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">#Of Hours:</label>
    <div class="col-sm-8">
      <input name="total_hours" value="<?php echo $total_hours; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."#Of Kilometers".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">#Of Kilometers:</label>
    <div class="col-sm-8">
      <input name="total_km" value="<?php echo $total_km; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>
  <?php if (strpos($value_config, ','."#Of Miles".',') !== FALSE) { ?>
  <div class="form-group">
    <label for="company_name" class="col-sm-4 control-label">#Of Miles:</label>
    <div class="col-sm-8">
      <input name="total_miles" value="<?php echo $total_miles; ?>" type="text" class="form-control">
    </div>
  </div>
  <?php } ?>

        <?php if (strpos($value_config, ','."GST exempt".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">GST exempt:</label>
        <div class="col-sm-8">
            <input type="checkbox" <?php if ($gst_exempt == '1') { echo " checked"; } ?> value="1" style="height: 20px; width: 20px;" name="gst_exempt">
        </div>
      </div>
      <?php } ?>

       <?php if (strpos($value_config, ','."Appointment Type".',') !== FALSE) { ?>
      <div class="form-group">
        <label for="company_name" class="col-sm-4 control-label">Appointment Type<span class="hp-red">*</span>:</label>
        <div class="col-sm-8">
            <select name="appointment_type" class="chosen-select-deselect form-control" width="380">
                <option value=''></option>
                <?php $appointment_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `appointment_type` WHERE `deleted` = 0"),MYSQLI_ASSOC);
                foreach ($appointment_types as $this_appointment_type) {
                    echo '<option '.($appointment_type == $this_appointment_type['id'] ? 'selected' : '').' value="'.$this_appointment_type['id'].'">'.$this_appointment_type['name'].'</option>';
                } ?>
            </select>
        </div>
      </div>
      <?php } ?>

		<div class="form-group double-gap-top">
			<p><span class="hp-red"><em>Required Fields *</em></span></p>
        </div>

        <div class="form-group">
            <div class="col-sm-6">
                <span class="popover-examples"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your entry."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a href="services.php" class="btn brand-btn btn-lg">Back</a>
				<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
			</div>
			<div class="col-sm-6">
				<button type="submit" name="add_service" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
				<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click this to finalize your entry."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			</div>
        </div>

    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
