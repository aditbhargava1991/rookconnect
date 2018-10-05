<?php
/*
 * How To Guide Database Connection
 */

$dbc_htg = @mysqli_connect('mysql.rookconnect.com', 'rook_htg_usr', 'M3se9*.nM#v4@R7', 'rook_howtoguide_db');
if (!$dbc_htg) {
    trigger_error('Could not connect to How To Guides: ' . mysqli_connect_error());
}
?>
