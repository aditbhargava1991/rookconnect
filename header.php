<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="robots" content="noindex">

<title>Software</title>

<!--New Function-->
<script>
function updateQueryStringParameter(uri, key, value) {
  var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
  var separator = uri.indexOf('?') !== -1 ? "&" : "?";
  if (uri.match(re)) {
    return uri.replace(re, '$1' + key + "=" + value + '$2');
  }
  else {
    return uri + separator + key + "=" + value;
  }
}

// Javascript Stored User Preferences
var time_format_style = '<?= TIME_FORMAT ?>';
var time_format_seconds = '<?= TIME_FORMAT_SEC ?>';
var time_out_notified = false;
</script>

<!-- css -->
<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,600" />
<link rel="stylesheet" type="text/css" href="<?= WEBSITE_URL;?>/css/chosen.css" />
<link rel="stylesheet" type="Text/css" href="<?= WEBSITE_URL;?>/css/select2.css" />
<link rel="stylesheet" type="text/css" href="<?= WEBSITE_URL;?>/css/jquery-ui-1.9.2.custom.css" />
<link rel="stylesheet" type="text/css" href="<?= WEBSITE_URL;?>/css/imgareaselect-default.css" />
<link rel="stylesheet" type="text/css" href="<?= WEBSITE_URL;?>/phpsign/jquery.signaturepad.css" />
<link rel="stylesheet" type="text/css" href="<?= WEBSITE_URL;?>/gantti-master/styles/css/gantti.css" /><?php
$software_config = '';
/*
 * Update the same on login_page_style.php
 */
$contactid = $_SESSION['contactid'];
$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM contacts WHERE contactid='$contactid'"));
$software_config = $get_config['software_styler_choice'];
if(empty($software_config)) {
	$security_levels = explode(',',trim(ROLE));
	foreach($security_levels as $security_level) {
		$field_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_security_level_theme` WHERE `security_level` = '$security_level'"));
		if(!empty($field_config['theme'])) {
			$software_config = $field_config['theme'];
			break;
		}
	}
}
if(empty($software_config)) {
	$software_config = get_config($dbc, 'software_default_theme');
}
if($software_config == 'swr') {
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_white_red.css" type="text/css">';
} else if ($software_config == 'bwr'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_precision.css" type="text/css">';
} else if ($software_config == 'blw'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_original.css" type="text/css">';
} else if ($software_config == 'bgw'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_clinic.css" type="text/css">';
} else if ($software_config == 'silver'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_silver.css" type="text/css">';
} else if ($software_config == 'blueorange'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_blue_orange.css" type="text/css">';
} else if ($software_config == 'blackpurple'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_blackpurple.css" type="text/css">';
} else if ($software_config == 'blackred'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_blackred.css" type="text/css">';
} else if ($software_config == 'washt'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_nightshadow.css" type="text/css">';
} else if ($software_config == 'btb'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_neon.css" type="text/css">';
} else if ($software_config == 'blackneonred'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_black_neon_red.css" type="text/css">';
} else if ($software_config == 'blackneon'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_black_neon.css" type="text/css">';
} else if ($software_config == 'blackgold'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_black_gold.css" type="text/css">';
} else if ($software_config == 'blackorange'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_black_orange.css" type="text/css">';
} else if ($software_config == 'ffm'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_ffm.css" type="text/css">';
} else if ($software_config == 'garden'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_garden.css" type="text/css">';
} else if ($software_config == 'green'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_green.css" type="text/css">';
} else if ($software_config == 'navy'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_navy.css" type="text/css">';
} else if ($software_config == 'purp'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_purple.css" type="text/css">';
} else if ($software_config == 'turq'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_turquoise.css" type="text/css">';
} else if ($software_config == 'leo'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_leopard.css" type="text/css">';
} else if ($software_config == 'polka'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_polka.css" type="text/css">';
} else if ($software_config == 'happy'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_happy.css" type="text/css">';
} else if ($software_config == 'chrome'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_chrome.css" type="text/css">';
} else if ($software_config == 'cosmos'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_space.css" type="text/css">';
} else if ($software_config == 'kayla'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_kayla.css" type="text/css">';
} else if ($software_config == 'flowers'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_flowers.css" type="text/css">';
} else if ($software_config == 'realtordark'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_realtor_dark.css" type="text/css">';
} else if ($software_config == 'realtorlight'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_realtor_light.css" type="text/css">';
} else if ($software_config == 'clouds'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_clouds.css" type="text/css">';
} else if ($software_config == 'orangeblue'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_orangeblue.css" type="text/css">';
} else if ($software_config == 'dots'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_dots.css" type="text/css">';
} else if ($software_config == 'pinkdots'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_pinkdots.css" type="text/css">';
} else if ($software_config == 'intuatrack'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_intuatrack.css" type="text/css">';
} else if ($software_config == 'transport'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_transport.css" type="text/css">';
} else if ($software_config == 'redsilver'){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style_redsilver.css" type="text/css">';
} else if ($software_config == '' && @$default_style != ''){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/'.$default_style.'.css" type="text/css">';
} else if ($software_config == ''){
    echo '<link rel="stylesheet" href="'.WEBSITE_URL.'/css/style.css" type="text/css">';
} ?>
<link rel="stylesheet" type="text/css" href="<?php echo WEBSITE_URL;?>/css/software_wide_style.css" />

<!-- js -->
<script>
	if(!window.jQuery) {
		document.write('<script src="<?= WEBSITE_URL;?>/mrbs/jquery/jquery-2.1.0.min.js"><\/script>');
	}
</script>
<!-- <script src="<?= WEBSITE_URL;?>/mrbs/jquery/jquery-2.1.0.min.js"></script> -->
<script src="<?= WEBSITE_URL;?>/tinymce/tinymce.min.js"></script>
<script src="<?= WEBSITE_URL;?>/js/timer.jquery.js"></script>
<script src="<?= WEBSITE_URL;?>/js/jquery-ui-1.9.2.custom.js"></script>
<script src="<?= WEBSITE_URL;?>/js/jquery.ui.touch-punch.min.js"></script>
<script src="<?= WEBSITE_URL;?>/js/jquery-ui-timepicker-addon.js"></script>
<script src="<?= WEBSITE_URL;?>/js/jquery.imgareaselect.pack.js"></script>
<script src="<?= WEBSITE_URL;?>/js/bootstrap.min.js"></script>
<script src="<?= WEBSITE_URL;?>/js/js.cookie.js"></script>
<script src="<?= WEBSITE_URL;?>/js/chosen.jquery.js" type="text/javascript"></script>
<script src="<?= WEBSITE_URL;?>/js/select2.js" type="text/javascript"></script>
<!-- <script src="<?php //echo WEBSITE_URL;?>/Rate Card/rate_card.js"></script> -->

<!-- DL -->
<!--<script src="<?php //echo WEBSITE_URL;?>/js/jquery.canvasjs.min.js"></script>
<script src="<?php //echo WEBSITE_URL;?>/js/canvasjs.js"></script>-->

<!-- phpsign -->
<script src="<?= WEBSITE_URL;?>/phpsign/flashcanvas.js"></script>
<script src="<?= WEBSITE_URL;?>/phpsign/jquery.signaturepad.js"></script>
<script src="<?= WEBSITE_URL;?>/phpsign/json2.min.js"></script>

<!-- Gantt Chart -->
<!-- <link rel="stylesheet" href="<?php //echo WEBSITE_URL;?>/gantti-master/styles/css/screen.css" /> -->

<script src="<?= WEBSITE_URL; ?>/js/isMobile.js"></script>
<script src="<?= WEBSITE_URL; ?>/js/circlos.js"></script>
<script>
$(document).ready(function(){
    $(".radial_chart").circlos();
});
</script>
<script src="<?= WEBSITE_URL;?>/js/custom.js"></script>
<script src="<?= WEBSITE_URL;?>/js/ajax_functions.js"></script>
<script> setInterval(function() { $.ajax({ url: '<?= WEBSITE_URL ?>/refresh_session.php' }); }, 1200000); </script>

<!-- FAVICON CODE -->
<?php $url = WEBSITE_URL.$_SERVER['REQUEST_URI'];

	$checkforfavicon = mysqli_num_rows(mysqli_query($dbc,"SELECT * FROM general_configuration WHERE name='favicon_upload'"));
	$favicon_upload_check = '';
	if($checkforfavicon > 0) {
		$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM general_configuration WHERE name='favicon_upload'"));
		$favicon_upload_check = $get_config['value'];
		$get_favicon_file = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM general_configuration WHERE name='favicon_upload_ico'"));
		$favicon_actual_file = $get_favicon_file['value'];
	}
	if($favicon_upload_check !== '' && $favicon_upload_check !== NULL) {
		echo '<link href="'.WEBSITE_URL.'/Admin Settings/favicon_upload/'.$favicon_actual_file.'.ico" rel="shortcut icon">';
	} else {
      $urlobj=parse_url($url);
      $domain=$urlobj['host'];
      if (preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i', $domain, $regs)) {
        $dom = $regs['domain'];
		$arr = explode(".", $dom, 2);
		$first = $arr[0];
		if($first == 'rookconnect' || $first == 'precisionworkflow' || $first == 'freshfocussoftware') {
			$subdomain = array_shift((explode(".",$_SERVER['HTTP_HOST'])));
			if($subdomain == 'zenearthcorp' || $subdomain == 'greenearthenergysolutions' || $subdomain == 'greenlifecan' || $subdomain == 'hydrera'  || $subdomain == 'wire' || $subdomain == 'sea' || $subdomain == 'sea-alberta' || $subdomain == 'sea-vancouver' || $subdomain == 'sea-saskatoon' || $subdomain == 'sea-regina' ) { ?>
				<link rel="apple-touch-icon" sizes="57x57" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/apple-icon-57x57.png">
				<link rel="apple-touch-icon" sizes="60x60" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/apple-icon-60x60.png">
				<link rel="apple-touch-icon" sizes="72x72" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/apple-icon-72x72.png">
				<link rel="apple-touch-icon" sizes="76x76" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/apple-icon-76x76.png">
				<link rel="apple-touch-icon" sizes="114x114" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/apple-icon-114x114.png">
				<link rel="apple-touch-icon" sizes="120x120" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/apple-icon-120x120.png">
				<link rel="apple-touch-icon" sizes="144x144" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/apple-icon-144x144.png">
				<link rel="apple-touch-icon" sizes="152x152" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/apple-icon-152x152.png">
				<link rel="apple-touch-icon" sizes="180x180" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/apple-icon-180x180.png">
				<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/android-icon-192x192.png">
				<link rel="icon" type="image/png" sizes="32x32" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/favicon-32x32.png">
				<link rel="icon" type="image/png" sizes="96x96" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/favicon-96x96.png">
				<link rel="icon" type="image/png" sizes="16x16" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/favicon-16x16.png">
				<link rel="manifest" href="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/manifest.json">
				<meta name="msapplication-TileColor" content="">
				<meta name="msapplication-TileImage" content="<?php echo WEBSITE_URL;?>/img/favicon/greenfavicon/ms-icon-144x144.png">
				<meta name="theme-color" content="">
			<?php
			} else if($first == 'rookconnect') {			?>
				<link rel="apple-touch-icon" sizes="57x57" href="<?php echo WEBSITE_URL;?>/img/favicon/apple-icon-57x57.png">
				<link rel="apple-touch-icon" sizes="60x60" href="<?php echo WEBSITE_URL;?>/img/favicon/apple-icon-60x60.png">
				<link rel="apple-touch-icon" sizes="72x72" href="<?php echo WEBSITE_URL;?>/img/favicon/apple-icon-72x72.png">
				<link rel="apple-touch-icon" sizes="76x76" href="<?php echo WEBSITE_URL;?>/img/favicon/apple-icon-76x76.png">
				<link rel="apple-touch-icon" sizes="114x114" href="<?php echo WEBSITE_URL;?>/img/favicon/apple-icon-114x114.png">
				<link rel="apple-touch-icon" sizes="120x120" href="<?php echo WEBSITE_URL;?>/img/favicon/apple-icon-120x120.png">
				<link rel="apple-touch-icon" sizes="144x144" href="<?php echo WEBSITE_URL;?>/img/favicon/apple-icon-144x144.png">
				<link rel="apple-touch-icon" sizes="152x152" href="<?php echo WEBSITE_URL;?>/img/favicon/apple-icon-152x152.png">
				<link rel="apple-touch-icon" sizes="180x180" href="<?php echo WEBSITE_URL;?>/img/favicon/apple-icon-180x180.png">
				<link rel="icon" type="image/png" sizes="192x192"  href="<?php echo WEBSITE_URL;?>/img/favicon/android-icon-192x192.png">
				<link rel="icon" type="image/png" sizes="32x32" href="<?php echo WEBSITE_URL;?>/img/favicon/favicon-32x32.png">
				<link rel="icon" type="image/png" sizes="96x96" href="<?php echo WEBSITE_URL;?>/img/favicon/favicon-96x96.png">
				<link rel="icon" type="image/png" sizes="16x16" href="<?php echo WEBSITE_URL;?>/img/favicon/favicon-16x16.png">
				<link rel="manifest" href="<?php echo WEBSITE_URL;?>/img/favicon/manifest.json">
				<meta name="msapplication-TileColor" content="">
				<meta name="msapplication-TileImage" content="<?php echo WEBSITE_URL;?>/img/favicon/ms-icon-144x144.png">
				<meta name="theme-color" content="">
		<?php
			}
		} else {
			?>
			<link href="<?php echo WEBSITE_URL;?>/img/favicon.ico" rel="shortcut icon">
			<link href="<?php echo WEBSITE_URL;?>/img/apple-touch-icon.png" rel="apple-touch-icon-precomposed">
			<?php
		}
      }
	}

?>
<!-- END FAVICON CODE -->
