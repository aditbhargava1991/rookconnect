<?php // Edit Equipment Rate Card
if (isset($_POST['submit'])) {
	require_once('../include.php');
	$id = $_POST['submit'];
	$item_id = filter_var($_POST['equip_id'],FILTER_SANITIZE_STRING);
	$start_date = filter_var($_POST['start_date'],FILTER_SANITIZE_STRING);
	$end_date = filter_var($_POST['end_date'],FILTER_SANITIZE_STRING);
	$alert_date = filter_var($_POST['alert_date'],FILTER_SANITIZE_STRING);
	$alert_staff = filter_var(implode(',',$_POST['alert_staff']),FILTER_SANITIZE_STRING);
	$annual = filter_var($_POST['annual'],FILTER_SANITIZE_STRING);
	$monthly = filter_var($_POST['monthly'],FILTER_SANITIZE_STRING);
	$semi_month = filter_var($_POST['semi_month'],FILTER_SANITIZE_STRING);
	$weekly = filter_var($_POST['weekly'],FILTER_SANITIZE_STRING);
	$daily = filter_var($_POST['daily'],FILTER_SANITIZE_STRING);
	$hourly = filter_var($_POST['hourly'],FILTER_SANITIZE_STRING);
	$hourly_work = filter_var($_POST['hourly_work'],FILTER_SANITIZE_STRING);
	$hourly_travel = filter_var($_POST['hourly_travel'],FILTER_SANITIZE_STRING);
	$field_day_actual = filter_var($_POST['field_day_actual'],FILTER_SANITIZE_STRING);
	$field_day_bill = filter_var($_POST['field_day_bill'],FILTER_SANITIZE_STRING);
	$cost = filter_var($_POST['cost'],FILTER_SANITIZE_STRING);
	$price_admin = filter_var($_POST['price_admin'],FILTER_SANITIZE_STRING);
	$price_wholesale = filter_var($_POST['price_wholesale'],FILTER_SANITIZE_STRING);
	$price_commercial = filter_var($_POST['price_commercial'],FILTER_SANITIZE_STRING);
	$price_client = filter_var($_POST['price_client'],FILTER_SANITIZE_STRING);
	$minimum = filter_var($_POST['minimum'],FILTER_SANITIZE_STRING);
	$unit_price = filter_var($_POST['unit_price'],FILTER_SANITIZE_STRING);
	$unit_cost = filter_var($_POST['unit_cost'],FILTER_SANITIZE_STRING);
	$rent_price = filter_var($_POST['rent_price'],FILTER_SANITIZE_STRING);
	$rent_days = filter_var($_POST['rent_days'],FILTER_SANITIZE_STRING);
	$rent_weeks = filter_var($_POST['rent_weeks'],FILTER_SANITIZE_STRING);
	$rent_months = filter_var($_POST['rent_months'],FILTER_SANITIZE_STRING);
	$rent_years = filter_var($_POST['rent_years'],FILTER_SANITIZE_STRING);
	$num_days = filter_var($_POST['num_days'],FILTER_SANITIZE_STRING);
	$num_hours = filter_var($_POST['num_hours'],FILTER_SANITIZE_STRING);
	$num_kms = filter_var($_POST['num_kms'],FILTER_SANITIZE_STRING);
	$num_miles = filter_var($_POST['num_miles'],FILTER_SANITIZE_STRING);
	$fee = filter_var($_POST['fee'],FILTER_SANITIZE_STRING);
	$hours_estimated = filter_var($_POST['hours_estimated'],FILTER_SANITIZE_STRING);
	$hours_actual = filter_var($_POST['hours_actual'],FILTER_SANITIZE_STRING);
	$service_code = filter_var($_POST['service_code'],FILTER_SANITIZE_STRING);
	$description = filter_var($_POST['description'],FILTER_SANITIZE_STRING);
    $tile_name = 'Equipment';
	$history = 'Equipment rate card '.($id == '' ? 'Added' : 'Edited').' by '.get_contact($dbc, $_SESSION['contactid']).' on '.date('Y-m-d h:i:s');
	$sql = '';
	if($id == '') {
		$sql = "INSERT INTO `company_rate_card` (`rate_card_name`,`start_date`,`end_date`,`alert_date`,`alert_staff`,`tile_name`,`description`,`item_id`,`uom`,`cost`,`editable`,`cust_price`,`profit`,`margin`,`daily`,`hourly`,`created_by`,`history`) VALUES
			('$rate_card_name','$start_date','$end_date','$alert_date','$alert_staff','$tile_name','$description','$item_id','$uom','$cost','$editable','$cust_price','$profit','$margin','$daily','$hourly','".$_SESSION['contactid']."','$history')";
	}
	else {
		$sql = "UPDATE `company_rate_card` SET `rate_card_name`='$rate_card_name',`start_date`='$start_date',`end_date`='$end_date',`alert_date`='$alert_date',`alert_staff`='$alert_staff',`tile_name`='$tile_name',`description`='$description',`item_id`='$item_id',`uom`='$uom',`cost`='$cost',`editable`='$editable',`cust_price`='$cust_price',`profit`='$profit',`margin`='$margin',`daily`='$daily',`hourly`='$hourly',`history`=CONCAT(IFNULL(CONCAT(`history`,'<br />'),''),'$history') WHERE `companyrcid`='$id'";
	}
	$result = mysqli_query($dbc, $sql);
	echo "<!--".mysqli_error($dbc)."-->";
    echo '<script type="text/javascript"> window.location.replace("?card=equipment&type=equipment"); </script>';
} ?>
<div class='col-sm-12 main_frame' id='no-more-tables'><form id="equipment_rate" name="equipment_rate" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php $id = $_GET['id'];
	$equipid = $_GET['equipment'];
	if($id > 0):
		$sql = "SELECT * FROM `company_rate_card` WHERE `companyrcid`='$id'";
		$result = mysqli_query($dbc, $sql);
	elseif($equipid > 0):
		$result = mysqli_query($dbc, "SELECT '$equipid' `equipment_id`");
	else:
		$result = mysqli_query($dbc, "SELECT 0 `equipment_id`");
	endif;
	$equip_sql = "SELECT CONCAT('Unit: ',`unit_number`,', VIN:',`vin_number`,', ',`make`,' ',`model`) name, CONCAT('Unit: ',`unit_number`,', VIN:',`vin_number`,', ',`make`,' ',`model`) description, `equipmentid` id FROM `equipment`";
	$equip_results = mysqli_query($dbc, $equip_sql);
	$rates_sql = "SELECT `rate_card_name` FROM `company_rate_card` WHERE `deleted`=0 GROUP BY `rate_card_name` ORDER BY `rate_card_name`";
	$rate_results = mysqli_query($dbc, $rates_sql);
	$row = mysqli_fetch_assoc($result); ?>
    <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rate Card:</label>
    <div class='col-sm-8'><select name='rate_card' data-placeholder='Select Rate Card' class='chosen-select-deselect form-control'><option></option>
    <?php while($rate_name = mysqli_fetch_array($rate_results)) {
        echo "<option".($rate_name['rate_card_name'] == $ratecard['rate_card_name'] ? ' selected' : '')." value='{$rate_name['rate_card_name']}' title='{$rate_name['rate_card_name']}'>{$rate_name['rate_card_name']}</option>";
    } ?>
    </select></div></div>
    <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Equipment:</label>
    <div class='col-sm-8'><select name='equip_id' data-placeholder='Choose Equipment' class='chosen-select-deselect form-control'><option></option>
    <?php while($equip_row = mysqli_fetch_array($equip_results)) {
        echo "<option".($row['equipment_id'] == $equip_row['id'] ? ' selected' : '')." value='{$equip_row['id']}'>{$equip_row['name']}</option>";
    } ?>
    </select></div></div>
    <?php
    $field_config = get_config($dbc, 'equipment_rate_fields');
    if(str_replace(',','',$field_config) == '') {
        $field_config = ",annual,monthly,hourly,";
    }
    if(strpos($field_config, ',start_end_dates,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Start Date</label>
        <div class='col-sm-8'><input class='form-control datepicker' type='text' name='start_date' value='<?php echo $row['start_date']; ?>'></div></div>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>End Date</label>
        <div class='col-sm-8'><input class='form-control datepicker' type='text' name='end_date' value='<?php echo $row['end_date']; ?>'></div></div>
    <?php }
    if(strpos($field_config, ',reminder_alerts,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Alert Date</label>
        <div class='col-sm-8'><input class='form-control datepicker' type='text' name='alert_date' value='<?php echo $row['alert_date']; ?>'></div></div>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Alert Staff</label>
        <div class='col-sm-8'>
            <select name="alert_staff[]" multiple data-placeholder="Select Staff..." class="form-control chosen-select-deselect">
                <?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
                foreach($staff_list as $staffid) {
                    echo '<option value="'.$staffid.'" '.(strpos(','.$row['alert_staff'].',',','.$staffid.',') !== FALSE ? 'selected' : '').'>'.get_contact($dbc, $staffid).'</option>';
                } ?>
            </select>
        </div></div>
    <?php }
    if(strpos($field_config, ',annual,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Annual Rate</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='annual' value='<?php echo $row['annual']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',monthly,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Monthly Rate</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='monthly' value='<?php echo $row['monthly']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',semi_month,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Semi-Monthly Rate</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='semi_month' value='<?php echo $row['semi_month']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',weekly,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Weekly Rate</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='weekly' value='<?php echo $row['weekly']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',daily,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Daily Rate</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='daily' value='<?php echo $row['daily']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',hourly,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Hourly Rate</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='hourly' value='<?php echo $row['hourly']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',hourly_work,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Hourly Rate (Work)</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='hourly_work' value='<?php echo $row['hourly_work']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',hourly_travel,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Hourly Rate (Travel)</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='hourly_travel' value='<?php echo $row['hourly_travel']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',field_day_actual,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Field Day Rate (Actual Cost)</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='field_day_actual' value='<?php echo $row['field_day_actual']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',field_day_bill,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Field Day Rate (Billable Rate)</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='field_day_billable' value='<?php echo $row['field_day_bill']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',cost,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Cost</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='cost' value='<?php echo $row['cost']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',price_admin,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Admin Price</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='price_admin' value='<?php echo $row['price_admin']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',price_wholesale,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Wholesale Price</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='price_wholesale' value='<?php echo $row['price_wholesale']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',price_commercial,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Commercial Price</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='price_commercial' value='<?php echo $row['price_commercial']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',price_client,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Client Price</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='price_client' value='<?php echo $row['price_client']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',minimum,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Minimum Billable</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='minimum' value='<?php echo $row[minimum]; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',unit_price,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Unit Price</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='unit_price' value='<?php echo $row['unit_price']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',unit_cost,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Unit Cost</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='unit_cost' value='<?php echo $row['unit_cost']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',rent_price,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rent Price</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='rent_price' value='<?php echo $row['rent_price']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',rent_days,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rental Days</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='rent_days' value='<?php echo $row['rent_days']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',rent_weeks,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rental Weeks</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='rent_weeks' value='<?php echo $row['rent_weeks']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',rent_months,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rental Months</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='rent_months' value='<?php echo $row['rent_months']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',rent_years,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rental Years</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='rent_years' value='<?php echo $row['rent_years']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',num_days,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Number of Days</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='num_days' value='<?php echo $row['num_days']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',num_hours,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Number of Hours</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='num_hours' value='<?php echo $row['num_hours']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',num_kms,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Number of Kilometres</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='num_kms' value='<?php echo $row['num_kms']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',num_miles,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Number of Miles</label>
        <div class='col-sm-8'><input class='form-control' type='number' name='num_miles' value='<?php echo $row['num_miles']; ?>' min='0' step='any'></div></div>
    <?php }
    if(strpos($field_config, ',fee,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Fee</label>
        <div class='col-sm-8'><input class='form-control' type='text' name='fee' value='<?php echo $row['fee']; ?>'></div></div>
    <?php }
    if(strpos($field_config, ',hours_estimated,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Estimated Hours</label>
        <div class='col-sm-8'><input class='form-control' type='text' name='hours_estimated' value='<?php echo $row['hours_estimated']; ?>'></div></div>
    <?php }
    if(strpos($field_config, ',hours_actual,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Actual Hours</label>
        <div class='col-sm-8'><input class='form-control' type='text' name='hours_actual' value='<?php echo $row['hours_actual']; ?>'></div></div>
    <?php }
    if(strpos($field_config, ',service_code,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Service Code</label>
        <div class='col-sm-8'><input class='form-control' type='text' name='service_code' value='<?php echo $row['service_code']; ?>'></div></div>
    <?php }
    if(strpos($field_config, ',description,') !== false) { ?>
        <div class='form-group clearfix completion_date'><label class='col-sm-4 control-label text-right'>Rate Description</label>
        <div class='col-sm-8'><input class='form-control' type='text' name='description' value='<?php echo $row['description']; ?>'></div></div>
    <?php } ?>
	<button type='submit' name='submit' value='<?php echo $id; ?>' class="btn brand-btn btn-lg pull-right">Submit</button>
</div>