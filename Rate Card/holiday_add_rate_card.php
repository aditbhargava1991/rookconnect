<?php
//Rate Card Tiles

if (isset($_POST['submit'])) {
	require_once('../include.php');
    $who_added = $_SESSION['contactid'];
    $when_added = date('Y-m-d');

    $rate_type = filter_var($_POST['rate_type'],FILTER_SANITIZE_STRING);
    $positionid = filter_var($_POST['positionid'],FILTER_SANITIZE_STRING);
    $staffid = filter_var($_POST['staffid'],FILTER_SANITIZE_STRING);
    $no_of_hours_paid = filter_var($_POST['no_of_hours_paid'],FILTER_SANITIZE_STRING);

    if(empty($_POST['ratecardholidayid'])) {
        $query_insert_customer = "INSERT INTO `rate_card_holiday_pay` (`rate_type`, `positionid`, `staffid`, `no_of_hours_paid`, `who_added`, `when_added`) VALUES ('$rate_type', '$positionid', '$staffid', '$no_of_hours_paid', '$who_added', '$when_added')";
        $result_insert_customer = mysqli_query($dbc, $query_insert_customer);
        $url = 'Added';
				$before_change = '';
        $history = "Rate card holiday entry is been added. <br />";
				add_update_history($dbc, 'ratecard_history', $history, '', $before_change);
    } else {
        $ratecardholidayid = $_POST['ratecardholidayid'];
        $query_update_vendor = "UPDATE `rate_card_holiday_pay` SET `rate_type` = '$rate_type', `positionid` = '$positionid', `staffid` = '$staffid', `no_of_hours_paid` = '$no_of_hours_paid' WHERE `ratecardholidayid` = '$ratecardholidayid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
				$before_change = '';
        $history = "Rate card holiday pay entry is been updated. <br />";
				add_update_history($dbc, 'ratecard_history', $history, '', $before_change);
    }

    echo '<script type="text/javascript"> window.location.replace("?card=holiday&type=holiday"); </script>';
}
?>
    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
        $rate_type = '';
        $positionid = '';
        $staffid = '';
        $no_of_hours_paid = '';

        if(!empty($_GET['ratecardid'])) {
            $ratecardholidayid = $_GET['ratecardid'];
            $ratecard = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM `rate_card_holiday_pay` WHERE `ratecardholidayid`='$ratecardholidayid'"));
            $rate_type = $ratecard['rate_type'];
            $positionid = $ratecard['positionid'];
            $no_of_hours_paid = $ratecard['no_of_hours_paid'];
            $staffid = $ratecard['staffid'];
            ?>
        <input type="hidden" id="ratecardholidayid" name="ratecardholidayid" value="<?php echo $ratecardholidayid ?>" />
        <?php
        }
    ?>

    <?php
    $value_config = ','.get_config($dbc, 'holiday_db_rate_fields').',';
    ?>
    <div class="panel-group" id="accordion2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_abi" >Rate Card Info<span class="glyphicon glyphicon-minus"></span></a>
                </h4>
            </div>

            <div id="collapse_abi" class="panel-collapse collapse in">
                <div class="panel-body">

	<?php if (strpos($value_config, ','."holiday_rate_type".',') !== FALSE) { ?>
                       <div class="form-group clearfix completion_date">
                            <label for="first_name" class="col-sm-4 control-label text-right">Rate Type:</label>
                            <div class="col-sm-8">
                                <select name = "rate_type" data-placeholder="Select Category..." class="chosen-select-deselect form-control" width="380">
                                    <option value=''></option>
                                    <option value='Paid' <?php if($rate_type == 'Paid') { echo 'selected';} ?> >Paid</option>
                                    <option value='Flat Rate' <?php if($rate_type == 'Flat Rate') { echo 'selected';} ?>>Flat Rate</option>
                                    <option value='Hourly' <?php if($rate_type == 'Hourly') { echo 'selected';} ?>>Hourly</option>
                                    <option value='Time + 1/2' <?php if($rate_type == 'Time + 1/2') { echo 'selected';} ?>>Time + 1/2</option>
                                    <option value='Double Time' <?php if($rate_type == 'Double Time') { echo 'selected';} ?>>Double Time</option>
                                    <option value='None' <?php if($rate_type == 'None') { echo 'selected';} ?>>None</option>
                                </select>
                            </div>
                        </div>
	<?php } ?>

	<?php if (strpos($value_config, ','."holiday_rate_position".',') !== FALSE) { ?>
                        <div class="form-group clearfix completion_date">
                            <label for="first_name" class="col-sm-4 control-label text-right">Position:</label>
                            <div class="col-sm-8">
                                <select data-placeholder="Choose a Position..." name="positionid" class="chosen-select-deselect form-control" width="380">
                                <option value=""></option>
                                <?php
                                        $query = mysqli_query($dbc,"SELECT position_id, name FROM positions WHERE `deleted`=0 ORDER BY `name`");
                                        while($row = mysqli_fetch_array($query)) { ?>
                                            <option value='<?php echo  $row['position_id']; ?>' <?php if($positionid == $row['position_id']) { echo 'selected';} ?> ><?php echo $row['name']; ?></option>
                                        <?php  } ?>
                                        </select>
                            </div>
                        </div>
	<?php } ?>

	<?php if (strpos($value_config, ','."holiday_rate_staff".',') !== FALSE) { ?>
                        <div class="form-group clearfix completion_date">
                            <label for="first_name" class="col-sm-4 control-label text-right">Staff:</label>
                            <div class="col-sm-8">
                                <select data-placeholder="Choose a Staff..." name="staffid" class="chosen-select-deselect form-control" width="380">
                                <option value=""></option>
                                <?php
                                        $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = 'Staff' AND `deleted` = 0 AND `status` = 1"),MYSQLI_ASSOC));
                                        foreach ($query as $id) {
                                            echo '<option value="'.$id.'" '.(strpos(','.$staffid.',', ','.$id.',') !== FALSE ? ' selected' : '').'>'.get_contact($dbc, $id).'</option>';
                                        }
                                        ?>
                                        </select>
                            </div>
                        </div>
	<?php } ?>

	<?php if (strpos($value_config, ','."hoilday_rate_hours".',') !== FALSE) { ?>
                        <div class="form-group clearfix completion_date">
                            <label for="first_name" class="col-sm-4 control-label text-right">Number of Hours paid:</label>
                            <div class="col-sm-8">
                                <input name="no_of_hours_paid" value="<?= $no_of_hours_paid ?>" type="text" class="form-control">
                            </div>
                        </div>
	<?php } ?>


                </div>
            </div>
        </div>

    </div>

    <div class="form-group">
        <div class="col-sm-12">
            <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
        </div>
    </div>

    </form>
