<?php $guest_access = true;
include('../include.php');
ob_clean();
// echo '6 Min#*#50.989#*#-114.06652';
// exit();
$driver_cookie = $_POST['driver'];
if(empty($driver_cookie)) {
	echo 'UNKNOWN#*#status_error.php?err='.urlencode('No Driver Location Found');
	exit();
}
$ch = curl_init();

curl_setopt($ch, CURLOPT_URL, "https://www.google.com/maps/timeline/kml?authuser=0&pb=!1m8!1m3!1i".date('Y')."!2i".(date('n') - 1)."!3i".date('j')."!2m3!1i".date('Y')."!2i".(date('n') - 1)."!3i".date('j')."");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");

curl_setopt($ch, CURLOPT_ENCODING, 'gzip, deflate');

$headers = array();
$headers[] = "Host: www.google.com";
$headers[] = "User-Agent: Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:40.0) Gecko/20100101 Firefox/40.0";
$headers[] = "Accept: text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8";
$headers[] = "Accept-Language: en-GB,en;q=0.5";
$headers[] = "DNT: 1";
$headers[] = "Cookie: ".$driver_cookie;
$headers[] = "Connection: keep-alive";
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
$lat = 0;
$long = 0;
if (curl_errno($ch)) {
    echo 'UNKNOWN#*#status_error.php?err='.urlencode('Unable to access Driver Location');
	exit();
} else {
	foreach(explode('<coordinates>',$result) as $location) {
		$location = explode(',',$location);
		$temp_lat = $location[1];
		$temp_long = $location[0];
		if(($temp_lat > 0 || $temp_lat < 0) && ($temp_long > 0 || $temp_long < 0)) {
			$lat = $temp_lat;
			$long = $temp_long;
		}
	}
}
if(empty($lat) && empty($long)) {
    echo 'UNKNOWN#*#status_error.php?err='.urlencode('Unable to access Driver Location');
	exit();
}
curl_close ($ch);
$data = json_decode(file_get_contents("http://maps.googleapis.com/maps/api/distancematrix/json?origins=".$lat.','.$long."&destinations=".$_POST['destination']."&language=en-EN&sensor=false"));
$time = $data->rows[0]->elements[0]->duration->text;
echo $time.'#*#'.$lat.'#*#'.$long;