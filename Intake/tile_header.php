<div class="col-xs-12 col-sm-4">
    <h1>
        <span class="pull-left" style="margin-top: -5px;"><a href="intake.php" class="default-color">Intake Forms</a></span>
        <span class="clearfix"></span>
    </h1>
</div>
<div class="col-xs-12 col-sm-8 text-right settings-block">
    <div class="pull-right top-settings"><?php
    	if(config_visible_function($dbc, 'intake') == 1) { ?>
            <a href="field_config.php" class="mobile-block pull-right "><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
            <span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span><?php
        } ?><?php
        if(vuaed_visible_function($dbc, 'intake') == 1) {
            echo '<a href="" onclick="addIntakeForm(); return false;"><button type="button" class="btn brand-btn mobile-block gap-bottom mobile-100-pull-right pull-right gap-right hide-titles-mob" style="margin-bottom:10px !important;">Add Intake Form</button><img src="'.WEBSITE_URL.'/img/icons/ROOK-add-icon.png" class="show-on-mob add-icon-lg gap-right"></a>';
        } ?>
        <?php if(isset($_GET['edit']) && $_GET['edit']!=''){ ?>
        <div class="icons_div pull-right" data-id="<?= $_GET['edit'] ?>">
            <a href='Add Reminder' onclick='return false;'><img src='<?= WEBSITE_URL?>/img/icons/ROOK-reminder-icon.png' class='no-toggle reminder-icon' title='Schedule Reminder' style='width: 2.5em;margin-right: 1em;'></a>
        </div>
        <?php }?>
    </div>
</div>
<div class="clearfix"></div>
<script>
$(document).ready(function() {
    $('.icons_div .reminder-icon').off('click').click(function() {
        var item = $(this).closest('.icons_div');
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile=add_intake&id='+item.data('id'), 'auto', false, true);
    });
})
</script>