<?php error_reporting(0);
include_once('../include.php');

if (isset($_POST['submit'])) {
	$estimate_tile = $_POST['estimate_tile'];
    set_config($dbc, 'estimate_tile_name', $estimate_tile);
    
    //Auto Archive
    $estimate_auto_archive = isset($_POST['estimate_auto_archive']) ? 1 : 0;
    $estimate_auto_archive_days = filter_var($_POST['estimate_auto_archive_days'],FILTER_VALIDATE_INT);
    set_config($dbc, 'estimate_auto_archive', $estimate_auto_archive);
    set_config($dbc, 'estimate_auto_archive_days', $estimate_auto_archive_days);
	set_config($dbc, 'disable_us_auto_convert', $_POST['us_pricing_convert'] == 'false' ? 'false' : 'true');
}
?>
<script>
</script>

<form class="form-horizontal margin-vertical margin-horizontal" action="" method="POST" enctype="multipart/form-data">

    <h3>Estimate Tile Name</h3>
    <div class="form-group type-option">
        <label class="col-sm-4">Tile Name:<br /><em>Enter the name you would like the Estimate tile to be labelled as.</em></label>
        <div class="col-sm-8">
            <input type="text" name="estimate_tile" class="form-control" value="<?= ESTIMATE_TILE ?>">
        </div>
    </div>
    
    <h4 class="double-gap-top">Auto Archive</h4>
    <?php
        $estimate_auto_archive = get_config($dbc, 'estimate_auto_archive');
        $estimate_auto_archive_days = get_config($dbc, 'estimate_auto_archive_days');
    ?>
    <div class="form-group">
        <div class="col-sm-4">
            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Update the Closed &amp; Abandoned statuses under Estimates Status tab for this to work."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            Auto archive Closed &amp; Abandoned Estimates:
        </div>
        <div class="col-sm-8">
            <input type="checkbox" name="estimate_auto_archive" value="<?= $estimate_auto_archive==1 ? 1 : 0 ?>" <?= $estimate_auto_archive==1 ? 'checked' : '' ?> /> Enable
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-4">Auto Archive Closed &amp; Abandoned Estimates After # of Days:</div>
        <div class="col-sm-8">
            <input type="number" name="estimate_auto_archive_days" class="form-control" value="<?= !empty($estimate_auto_archive_days) ? $estimate_auto_archive_days : '30' ?>" min="1" step="1" />
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-4">Auto Convert USD Pricing</div>
        <div class="col-sm-8">
            <label class="form-checkbox"><input type="checkbox" name="us_pricing_convert" <?= get_config($dbc, 'disable_us_auto_convert') == 'true' ? '' : 'checked' ?> value="false" />Enable</label>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-4"></div>
        <div class="col-sm-8"><button type="submit" name="submit" value="submit" class="btn brand-btn pull-right">Submit</button></div>
    </div>

</form>

<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_tile.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>