<?php include_once('../include.php');
include_once('../Sales/config.php');
if(empty($salesid)) {
	$salesid = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
} ?>
<script>

</script>
<!-- Time Tracking -->
<div class="accordion-block-details padded" id="deli">
    <div class="accordion-block-details-heading"><h4>Time Line Countdown</h4></div>

        <div class="row set-row-height">
            <div class="col-xs-12 col-sm-4 gap-md-left-15">Total Number of Days:</div>
            <div class="col-xs-12 col-sm-5"><input data-table="sales" name="number_of_days" value="<?= $number_of_days; ?>" type="text" class="form-control" /></div>
            <div class="clearfix"></div>
        </div>

        <?php
            $now = time(); // or your date as well
            $your_date = strtotime($number_of_days_start_date);
            $datediff = $now - $your_date;

            echo round($datediff / (60 * 60 * 24)).'/'.$number_of_days.' Days';
        ?>

</div><!-- .accordion-block-details -->