<?php
/*
 * Copy Live DB (Data + Settings) to Demo DB
 * Set the database names in database_connection.php
 */
include_once('include.php');

if(stripos(','.$role.',',',super,') === false) {
	header('location: admin_software_config.php?software_settings');
	die();
}

function reset_demo_to_live($db_all, $database_1, $database_2) {
    $conn = $db_all;
    $live_db = $database_1;
    $demo_db = $database_2;
    $html = '<table class="table table-bordered"><tr><th>Table</th><th>Status</th></tr>';

    mysqli_select_db($live_db, $conn);
    set_time_limit(0);

    $tables = mysqli_query($conn, "SHOW TABLES FROM `$live_db`");
    
    while ($tab = mysqli_fetch_row($tables)) {
        $table = $tab[0];
        mysqli_query($conn, "DROP TABLE IF EXISTS `$demo_db`.`$table`");
        mysqli_query($conn, "CREATE TABLE `$demo_db`.`$table` LIKE `$live_db`.`$table`") or die(mysqli_error($conn));
        mysqli_query($conn, "INSERT INTO `$demo_db`.`$table` SELECT * FROM `$live_db`.`$table`");
        $html .= '<tr><td>'. $table .'</td><td>Copied</td></tr>';
    }
    
    $html .= '</table>';
    
    return $html;
} ?>

<div id="no-more-tables">
	<div class="notice double-gap-bottom popover-examples">
	<div class="col-sm-1 notice-icon"><img src="img/info.png" class="wiggle-me" width="25px"></div>
	<div class="col-sm-16"><span class="notice-name">NOTE:</span>
		Reset your Demo software's data and configuration to a copy of your Live software. This process will take some time and <u>CANNOT</u> be undone.</div>
		<div class="clearfix"></div>
	</div><?php
    
    if ( !DATABASE_COPY2 ) {
        echo '<p>Demo database is not added to the configuration. Please add the demo database to use this function.</p>';
    } else {
        if ( isset($_POST['submit']) ) {
            $db_all = mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD);
            $database_1 = DATABASE_NAME; //Live
            $database_2 = DATABASE_COPY2; //Demo
            echo reset_demo_to_live($db_all, $database_1, $database_2);
        } else { ?>
            <form name="reset_demo" method="post" action="">
                <div class="text-center"><input type="submit" name="submit" value="Reset Demo Software" class="btn config-btn" /></div>
            </form><?php
        }
    } ?>
</div>