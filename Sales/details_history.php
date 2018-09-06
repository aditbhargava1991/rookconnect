<!-- History -->
<?php include_once('../Sales/config.php'); ?>
<div class="accordion-block-details padded" id="history">
    <div class="accordion-block-details-heading"><h4>History</h4></div>
    
    <div class="row set-row-height">
        <div class="col-xs-12">
			<a href="history.php?id=<?= $salesid ?>" class="btn brand-btn" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;">View History</a>
        </div>
        <div class="clearfix double-gap-bottom"></div>
    </div>
    
</div><!-- .accordion-block-details -->