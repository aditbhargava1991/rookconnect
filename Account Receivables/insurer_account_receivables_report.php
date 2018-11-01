<?php
/*
Client Listing
*/
include ('../include.php');
$download_folder = '../'.(tile_enabled($dbc, 'posadvanced')['user_enabled'] ? 'POSAdvanced/' : 'Invoice/');
$folder = '../Account Receivables/';
$current_tile_name = 'Accounts Receivable';
checkAuthorised('accounts_receivables');
include('../Invoice/insurer_account_receivables_report.php');