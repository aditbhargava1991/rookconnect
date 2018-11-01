<?php include('../include.php');
$download_folder = '../'.(tile_enabled($dbc, 'posadvanced')['user_enabled'] ? 'POSAdvanced/' : 'Invoice/');
$folder = '../Account Receivables/';
include('../Invoice/pay_receivables.php');