<?php
if (isset($_POST['submit'])) {
    set_config($dbc, 'inventory_markup', filter_var($_POST['inventory_markup'],FILTER_SANITIZE_STRING));
}
?>

<div class="gap-top">

    <div class="notice popover-examples double-gap-top triple-gap-bottom">
        <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11"><span class="notice-name">NOTE:</span> Setting a default markup will affect all inventory in all tabs.</div>
        <div class="clearfix"></div>
    </div>

    <div class="form-group">
        <label class="col-sm-4 control-label"><span class="popover-examples list-inline" style="margin:0 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Enter a number to use as a percentage markup, such as 15."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span> Standard Markup:</label>
        <div class="col-sm-8">
            <input name="inventory_markup" value="<?php echo get_config($dbc, 'inventory_markup'); ?>" placeholder="Enter a Markup percentage" type="number" step="any" min="0" class="form-control">
        </div>
    </div>
</div>