<?php
/*
Client Listing
*/
include ('../include.php');
$report_fields = explode(',', get_config($dbc, 'report_operation_fields'));
checkAuthorised('report');
include_once('../Timesheet/reporting_functions.php');
include_once('../Timesheet/config.php');
$value = $config['settings']['Choose Fields for Time Sheets Dashboard']; ?>


<div id="timesheet_div">
    <?php
	$time_tabs = $config['tabs']['Reporting'];
	$html = '';
	if(is_array($time_tabs)) {
		foreach($time_tabs as $subtitle => $content) {
			$subactive = '';
			if($subtitle == $_GET['tab']) {
				$subactive = 'active_tab';
			}

			$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
			$value_config = ','.$get_field_config[$value['config_field']].',';

			if (strpos($value_config, ','.$content.',') !== FALSE || strpos($value_config, ','.$subtitle.',') !== FALSE) {
				$content = "?type=".$_GET['type']."&report=".$_GET['report']."&".explode('?', $content)[1];
				$html .= "<a href='".$content."'><button type='button' class='btn brand-btn mobile-block ".$subactive."' >".$subtitle."</button></a>";
			}
		}
		if(!empty($html)) {
			echo '<br><div>'.$html.'</div><br>';
		}
	}
    include('../Timesheet/reporting_content.php'); ?>
</div>
<script>
$('document').ready(function() {
    var tables = $('table');

    tables.map(function(idx, table) {
        var rows = $(table).find('tbody > tr');
        rows.map(function(idx, row){
            if(idx%2 == 0) {
                $(row).css('background-color', '#e6e6e6');
            }
        })
    })
})
</script>