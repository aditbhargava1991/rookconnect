<!-- Tile Header -->
<div class="tile-header standard-header">
    <div class="col-xs-12 col-sm-4">
        <h1>
            <a href="index.php"><span class="pull-left"><?= SALES_TILE ?></span></a>
            <span class="pull-left" style="margin-top:-5px;"><?php
                $staff = mysqli_query($dbc, "SELECT `initials`, `first_name`, `last_name`, `calendar_color` FROM `contacts` WHERE `contactid`='{$_SESSION['contactid']}'");
                profile_id($dbc, $_SESSION['contactid']); ?>
            </span>
            <span class="clearfix"></span>
        </h1>
    </div>
    <div class="col-xs-12 col-sm-8 text-right settings-block"><?php
        if ( config_visible_function ( $dbc, 'sales' ) == 1 ) { ?>
            <div class="pull-right gap-left top-settings">
                <a href="field_config.php" class="mobile-block pull-right "><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
                <span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div><?php
        } ?>
        <?php if ( vuaed_visible_function($dbc, 'sales') == 1 ) { ?>
            <div class="pull-right double-gap-left top-button-2">
                <a href="sale.php?p=details" class="btn brand-btn mobile-block pull-right">New <?= SALES_NOUN ?></a>
                <span class="popover-examples list-inline pull-right" style="margin:7px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Add sales lead details here."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div><?php
        }
        if (basename($_SERVER['SCRIPT_FILENAME']) == 'index.php') { ?>
            <div class="pull-right top-dashboard">
                <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-Speedometer.png" height="30" class="pull-right gap-left cursor-hand no-toggle" title="View Dashboards" onclick="$('.dashboard_select').toggle();">
                <div class="dashboard_select pull-right" style="display: none;">
                    <select class="chosen-select-deselect" onchange="window.location.replace('?dashboard='+this.value);">
                        <option value="<?= $_SESSION['contactid'] ?>">My Dashboard</option><?php
                        if ( $config_access > 0 ) {
                            echo '<option '.($_GET['dashboard'] == 'company_dashboard' ? 'selected' : '').' value="company_dashboard">Company Dashboard</option>';
							$dashboard_users = array_filter(explode(',',get_config($dbc, 'sales_dashboard_users')));
                            foreach(sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC)) as $contactid) {
                                if($contactid != $_SESSION['contactid'] && (empty($dashboard_users) || in_array($contactid,$dashboard_users))) {
                                    echo '<option '.($_GET['dashboard'] == $contactid ? 'selected' : '').' value="'.$contactid.'">'.get_contact($dbc, $contactid).'\'s Dashboard</option>';
                                }
                            }
                        } ?>
                    </select>
                </div>
            </div>
        <?php } ?>
        <?php if (basename($_SERVER['SCRIPT_FILENAME']) != 'reports.php' && basename($_SERVER['SCRIPT_FILENAME']) != 'field_config.php') { ?>
			<img class="inline-img pull-right btn-horizontal-collapse no-toggle" src="../img/icons/pie-chart.png" title="View Summary">
		<?php } ?>
        <img class="no-toggle statusIcon pull-right no-margin inline-img small" title="" src="" data-original-title="">
    </div>
    <div class="clearfix"></div>
</div><!-- .tile-header -->
