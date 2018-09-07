<?php session_start();
include ('include.php');
ob_clean();

if($_GET['action'] == "next_occurrence") {
	$name = $_POST['name'];
	$month = $_POST['month'];
	$weekday = $_POST['weekday'];
	$week = $_POST['week'];
	$day = $_POST['day'];
	$year = date("Y");
	
	$current = new DateTime();
	do {
		if($name == 'Easter Monday') {
			exit('');
		} else if($name == 'Easter Sunday') {
			exit('');
		} else if($name == 'Good Friday') {
			exit('');
		} else if($day > 0) {
			$description = "$day $month $year";
		} else if ($week != '' && $weekday != '') {
			$description = '';
			switch($week) {
				case 1: $description = "first $weekday of $month $year";
					break;
				case 2: $description = "second $weekday of $month $year";
					break;
				case 3: $description = "third $weekday of $month $year";
					break;
				case 4: $description = "fourth $weekday of $month $year";
					break;
				case 5: $description = "fifth $weekday of $month $year";
					break;
				case -1: $description = "last $weekday of $month $year";
					break;
				case -2: $description = "last $weekday of $month $year previous $weekday";
					break;
				case -3: $description = "last $weekday of $month $year previous $weekday previous $weekday";
					break;
				case -4: $description = "last $weekday of $month $year previous $weekday previous $weekday previous $weekday";
					break;
				default: $description = "January 1, $year";
					break;
			}
		}
		$holiday = new DateTime($description);
		$year++;
	} while($current >= $holiday);
	echo $holiday->format('Y-m-d');
}