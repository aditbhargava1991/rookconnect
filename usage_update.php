<?php include_once('include.php');
$limit = 5 * 1024 * 1024 * 1024; // 5 GiB
$increment = 500 * 1024 * 1024; // 500 MiB
$warning = 100 * 1024 * 1024; // 100 MiB
$inc_count = 0;
$database = 0;
$filesystem = get_dir_size('..');

$result = mysqli_query($dbc, "SHOW TABLE STATUS");
while($row = mysqli_fetch_assoc($result)) {
	$database += $row['Data_length'] + $row['Index_length'];
}
$total_usage = $database + $filesystem;
$inc_count = $total_usage > $limit ? ceil(($total_usage - $limit) / $increment) : 0;
$total_paid = $limit + ($increment * $inc_count);

$last_notify = 0;
$append = '';
for($i = 0; $total_usage > $limit + $i * $increment - $warning; $i++) {
	$last_notify = $limit + $i * $increment - ($warning * ($total_usage < $limit + $i * $increment ? 1 : 0));
	$append = ($total_usage < $limit + $i * $increment ?
		'<p>You are within 100 MiB of the next data usage tier.<br />You are currently using '.roundByteSize($total_usage).'.</p>' :
		'<p>You have reached the next usage tier.<br />You are currently using '.roundByteSize($total_usage).'.</p>');
}

$dbc_support = mysqli_connect('localhost', 'ffm_rook_user', 'mIghtyLion!542', 'ffm_rook_db');
//$dbc_support = mysqli_connect('localhost', 'root', '', 'local_rookconnect_db');
mysqli_query($dbc_support, "INSERT INTO `software_usage` (`software_id`) SELECT '".WEBSITE_URL."' FROM (SELECT COUNT(*) rows FROM `software_usage` WHERE `software_id`='".WEBSITE_URL."') num WHERE num.rows=0");
mysqli_query($dbc_support, "UPDATE `software_usage` SET `database_mb`='".round($database / (1024 * 1024),2)."', `filesystem_mb`='".round($filesystem / (1024 * 1024),2)."', `total_mb`='".round($total_usage / (1024 * 1024),2)."' WHERE `software_id`='".WEBSITE_URL."'");

$usage_config = mysqli_fetch_assoc(mysqli_query($dbc_support, "SELECT * FROM `software_usage` WHERE `software_id`='".WEBSITE_URL."'"));
if(floor($last_notify / (1024 * 1024)) > $usage_config['notification_mb']) {
	mysqli_query($dbc_support, "UPDATE `software_usage` SET `notification_mb`='".floor($last_notify / (1024 * 1024))."' WHERE `software_id`='".WEBSITE_URL."'");
	$body = html_entity_decode($usage_config['email_body']).$append;
	send_email('', [$usage_config['recipient_address']=>$usage_config['recipient_name']], '', '', $usage_config['email_subject'], $body);
}

function get_dir_size($folder) {
	$size = 0;
	foreach(glob($folder.'/*') as $path) {
		//echo $path.'<br />';
		if(is_file($path)) {
			$size += filesize($path);
		} else if(is_dir($path) && !strpos_any(['/..','Database Backups'],$path)) {
			$size += get_dir_size($path);
		}
	}
	return $size;
}

if(isset($_POST['field'])) {
	$field = filter_var($_POST['field'], FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'], FILTER_SANITIZE_STRING);
	mysqli_query($dbc_support, "UPDATE `software_usage` SET `$field`='$value' WHERE `software_id`='".WEBSITE_URL."'");
}
