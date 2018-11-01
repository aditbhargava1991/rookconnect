<!-- Estimate -->
<?php include_once('../Sales/config.php'); ?>
<div class="accordion-block-details padded" id="quote">
    <div class="accordion-block-details-heading"><h4>Quote</h4></div>
    <div class="row">
        <div class="col-sm-12 gap-md-left-15 set-row-height"><?php
            $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(`estimateid`) AS `total_id`, `estimateid` FROM `estimate` WHERE `businessid`='$businessid' AND (`status`!='Saved' AND `status`!='Submitted' AND `status`!='Approved Quote')"));
            
            if ( $get_config['total_id'] > 0 ) {
                echo '<a href="'.WEBSITE_URL.'/Quote/quotes.php?businessid='.$businessid.'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Click to View Quote</a>';
                echo '<br><a href="'.WEBSITE_URL.'/Estimate/download/quote_'.$get_config['estimateid'].'.pdf" target="_blank" ><img src="'.WEBSITE_URL.'/img/pdf.png" width="16" height="16" border="0" alt="View">View PDF</a>';
            } else {
                $get_estimate = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`estimateid`) AS `total_estimate` FROM `estimate` WHERE `businessid`='$businessid' AND (`status`='Saved' OR `status`='Submitted')"));

                if($get_estimate['total_estimate'] > 0) {
                    echo 'No Quote<br />';
                    echo '<a href="'.WEBSITE_URL.'/Estimate/estimate.php?businessid='.$businessid.'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Click to View/Approve Estimate</a>';
                } else {
                    echo 'No Estimate or Quote';
                }
            } ?>
        </div>
        <div class="clearfix"></div>
    </div>
</div>