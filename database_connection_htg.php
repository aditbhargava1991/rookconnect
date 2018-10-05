<?php
/*
 * How To Guide Database Connection
 */

$dbc_htg = @mysqli_connect('mysql.rookconnect.com', 'ffm_rook_user', 'mIghtyLion!542', 'demo_rookconnect_db');
if (!$dbc_htg) {
    trigger_error('Could not connect to How To Guides: ' . mysqli_connect_error());
}
?>
