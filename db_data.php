<?php
/* Update Databases */
error_reporting(0);
include_once ('database_connection.php');
include_once ('function.php');

echo "Updating Compiled Procedures...\n";
include('db_procedures.php');
echo "\n";

echo "Updating Triggers...\n";
include('db_triggers.php');
echo "\n";

echo "Beginning Database Updates\n";

echo "\n";
include ('db_data_jay.php');
echo "\n";
include ('db_data_jonathan.php');
echo "\n";
//include ('db_data_user5.php');
echo "\n";
include ('db_data_baldwin.php');
echo "\n";
include ('db_data_dayana.php');
echo "\n";
include ('db_data_joseph.php');
echo "\n";
include ('db_data_invennico.php');
echo "\n";
include ('db_data_jenish.php');

echo "Done Updating Databases\n"; ?>
