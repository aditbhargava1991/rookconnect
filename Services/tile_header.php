<!-- Tile Header -->
<div class="tile-header standard-header">
    <div class="col-xs-12 col-sm-4">
        <h1>
            <a href="index.php" class="default-color">Services</a>
            <!--
            <span class="pull-left" style="margin-top:-5px;"><?php
                //$staff = mysqli_query($dbc, "SELECT `initials`, `first_name`, `last_name`, `calendar_color` FROM `contacts` WHERE `contactid`='{$_SESSION['contactid']}'");
                //profile_id($dbc, $_SESSION['contactid']); ?>
            </span>
            <span class="clearfix"></span>
            -->
        </h1>
    </div>
    <div class="col-xs-12 col-sm-8 text-right gap-top pad-right"><?php
        if ( config_visible_function ( $dbc, 'services' ) == 1 ) { ?>
            <div class="pull-right gap-left top-settings">
                <a href="field_config.php" class="mobile-block pull-right "><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
                <span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div>
            <!-- Moved the Buttons to the Settings -->
            <?php if(strpos($_SERVER[REQUEST_URI], "index.php") === FALSE): ?>
              <div class="pull-right gap-left top-button-2 hide-on-mobile">
                  <a href="service_templates.php" class="btn brand-btn mobile-block pull-right">Service Templates</a>
                  <span class="popover-examples list-inline pull-right" style="margin:7px 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to configure your Service Templates."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
              </div>
              <div class="pull-right gap-left top-button-2 hide-on-mobile">
                  <a href="field_config_templates.php" class="btn brand-btn mobile-block pull-right">Export Templates</a>
                  <span class="popover-examples list-inline pull-right" style="margin:7px 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to configure your Export Templates."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
              </div>
              <div class="pull-right gap-left top-button-2 hide-on-mobile">
                  <a href="field_config_style.php" class="btn brand-btn mobile-block pull-right">PDF Styling</a>
                  <span class="popover-examples list-inline pull-right" style="margin:7px 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to configure your PDF Styling."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
              </div>
            <?php endif; ?><?php
        } ?>
        <!-- Moved the Buttons to the Settings -->
        <?php if(strpos($_SERVER[REQUEST_URI], "index.php") === FALSE): ?>
          <div class="pull-right gap-left top-button-2 hide-on-mobile">
              <a href="export_pdf.php" class="btn brand-btn mobile-block pull-right">Import/Export</a>
              <span class="popover-examples list-inline pull-right" style="margin:7px 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to Export your Services by PDF."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
          </div>
        <?php endif; ?>
        <?php
        if ( vuaed_visible_function($dbc, 'services') == 1 ) { ?>
            <div class="pull-right gap-left top-button-2">
                <a href="service.php?p=details" class="btn brand-btn mobile-block pull-right">New Service</a>
                <span class="popover-examples list-inline pull-right" style="margin:7px 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a new Service."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div><?php
        } ?>
    </div>
    <div class="clearfix"></div>
</div><!-- .tile-header -->
