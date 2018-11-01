<?php
$fields = ',Program Notes,Members Table,Members Last Name,Members First Name,Members Contact Numbers,Members Drop Off,Members Pick Up,Members Hours,Members Notes,Members Age,Staff Table,Staff Name,Staff Duties,Staff Time In,Staff Time Out,Staff Hours,Staff Emergency Number,Staff PC Initial,Staff Notes,';
$header = '';
$header_logo_align = 'R';
$header_logo = '';
$footer = '';

$get_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_log` WHERE `template` = 'template_a'"));
if(!empty($get_config)) {
	$fields = ','.$get_config['fields'].',';
	$header = $get_config['header'];
	$header_logo_align = !empty($get_config['header_logo_align']) ? $get_config['header_logo_align'] : 'R';
	$header_logo = $get_config['header_logo'];
	$footer = $get_config['footer'];
}