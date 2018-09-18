<?php include_once('../include.php');
include_once('../Sales/config.php');
if(empty($salesid)) {
	$salesid = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
} ?>
<script>

</script>
<!-- Time Tracking -->
<div class="accordion-block-details padded" id="deli">
    <div class="accordion-block-details-heading"><h4>Deliverable</h4></div>

        <?php
         if(!empty($_GET['id'])) {
            $sid = $_GET['id'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT number_of_days, number_of_days_start_date FROM sales WHERE salesid='$sid'"));

            $number_of_days = $get_contact ['number_of_days'];
            $number_of_days_start_date = $get_contact ['number_of_days_start_date'];
        }
        ?>

        <div class="row set-row-height">
            <div class="col-xs-12 col-sm-4 gap-md-left-15">Total Number of Days:</div>
            <div class="col-xs-12 col-sm-5"><input data-table="sales" name="number_of_days" value="<?= $number_of_days; ?>" type="text" class="form-control" /></div>
            <div class="clearfix"></div>
        </div>

        <div class="clearfix"></div>

        <?php
        if($number_of_days > 0) { ?>
        <div class="row set-row-height">
            <div class="col-xs-12 col-sm-4 gap-md-left-15">Time Line Countdown:</div>
            <div class="col-xs-12 col-sm-5">
                <?php
                    $now = time(); // or your date as well
                    $your_date = strtotime($number_of_days_start_date);
                    $datediff = $now - $your_date;

                    echo round($datediff / (60 * 60 * 24)).'/'.$number_of_days.' Days';
                ?>
            </div>
            <div class="clearfix"></div>
        </div>
        <?php } ?>

</div><!-- .accordion-block-details -->