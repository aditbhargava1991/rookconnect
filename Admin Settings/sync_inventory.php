<?php
/*
 * Sync Inventory Between Software
 * Set the database names in database_connection.php
 * This setting from the database is used in POS/Invoice and Inventory
 */
include_once('include.php');

if(stripos(','.$role.',',',super,') === false) {
	header('location: admin_software_config.php?software_settings');
	die();
} ?>

<div id="no-more-tables">
	<div class="notice double-gap-bottom popover-examples">
	<div class="col-sm-1 notice-icon"><img src="img/info.png" class="wiggle-me" width="25px"></div>
	<div class="col-sm-16"><span class="notice-name">NOTE:</span>
		Enable Inventory syncing between software. If a connection is not displayed below, add the connection $dbc_inventory to database_connection.php file. Inventory from this software will be synced to the software added below.</div>
		<div class="clearfix"></div>
	</div><?php
    
    if ( !$dbc_inventory ) {
        echo '<h4 class="pad-left">Other software\'s database is not added to the configuration. Please add the connection to the <em>database_connection.php</em> to enable Inventory syncing.</h4>';
    } else {
        echo '<h4 class="pad-left">Syncing inventory between this software and <em>'. DATABASE_SYNC_INVENTORY .'</em> database.</h4>';
    } ?>
</div>