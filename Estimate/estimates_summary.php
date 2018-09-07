<?php include_once('../include.php');
echo '<h3>Summary</h3>';
$summary_view = explode(',',get_config($dbc,'estimate_summary_view'));
$summary_blocks = [];
$summary_height = [];
$total_height = 0;
$summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) total, SUM(`total_price`) value FROM `estimate` WHERE `deleted`=0"));
$summary_total = $summary['total'];
$closed_status = preg_replace('/[^a-z]/','',strtolower(get_config($dbc, 'estimate_project_status')));
$closed_date = get_user_settings()['estimate_closed'];
$closed_date = strtotime($closed_date) > date('Y-m-01') ? $closed_date : date('Y-m-01');
$estimate_status = explode('#*#',get_config($dbc, 'estimate_status'));
$estimate_types = explode(',',get_config($dbc,'project_tabs'));
if(in_array('Total Estimates',$summary_view)) {
	$summary_blocks[] = '<div class="overview-block"><h4>Total '.ESTIMATE_TILE.': '.$summary['total'].'</h4></div>';
	$summary_height[] = 38;
	$total_height += 38;
}
if(in_array('Total Value',$summary_view)) {
	$summary_blocks[] = '<div class="overview-block"><h4>Total Value of '.ESTIMATE_TILE.': $'.number_format($summary['value'],2).'</h4></div>';
	$summary_height[] = 38;
	$total_height += 38;
}
if(in_array('6 Month Value',$summary_view)) {
	$block_details = '<div class="overview-block"><h4>'.ESTIMATE_TILE.' (Last 6 Months)</h4>';
	for($i = 0; $i < 6; $i++) {
		$date = strtotime("- $i months");
		$summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(IF(`status`='$closed_status' AND `status_date` BETWEEN '".date('Y-m-01',$date)."' AND '".date('Y-m-t',$date)."',`total_price`,0) `closed` FROM `estimate` WHERE `deleted`=0"));
		$block_details .= date('Y-M',$date).' - $'.number_format($summary['closed'],2).'<br />';
	}
	$block_details .= '</div>';
	$summary_blocks[] = $block_details;
	$summary_height[] = 145;
	$total_height += 145;
}
if(in_array('Current Year Value',$summary_view)) {
	$block_details = '<div class="overview-block"><h4>'.ESTIMATE_TILE.' This Year</h4>';
	$block_height = 38;
	foreach($estimate_status as $status) {
		$summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(IF((`status`='$status' AND `status`!='$completed_status') OR (`status`='$completed_status' AND `status_date` > '".date('Y-01-01')."'),1,0) `count` FROM `estimate` WHERE `deleted`=0"));
		$block_details .= $status.': '.round($summary['count'],2).'<br />';
		$block_height += 18;
	}
	$block_details .= '</div>';
	$summary_blocks[] = $block_details;
	$summary_height[] = $block_height;
	$total_height += $block_height;
}
if(in_array('Estimate Type $',$summary_view)) {
	$block_details = '<div class="overview-block"><h4>'.ESTIMATE_TILE.' Value by Type</h4>';
	$block_height = 38;
	foreach($estimate_types as $type) {
		$summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(IF((`estimatetype`='$type',`total_price`,0) `value` FROM `estimate` WHERE `deleted`=0"));
		$block_details .= $type.': $'.number_format($summary['value'],2).'<br />';
		$block_height += 18;
	}
	$block_details .= '</div>';
	$summary_blocks[] = $block_details;
	$summary_height[] = $block_height;
	$total_height += $block_height;
}
if(in_array('Estimate Type Count',$summary_view)) {
	$block_details = '<div class="overview-block"><h4>'.rtrim(ESTIMATE_TILE, 's').' by Type</h4>';
	$block_height = 38;
	foreach($estimate_types as $type) {
		$summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `estimate` WHERE `estimatetype`='$type' AND `deleted`=0"));
		$block_details .= $type.': '.round($summary['count'],2).'<br />';
		$block_height += 18;
	}
	$block_details .= '</div>';
	$summary_blocks[] = $block_details;
	$summary_height[] = $block_height;
	$total_height += $block_height;
}
if(in_array('Revenue Won',$summary_view)) {
	$summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(IF((`status`='$closed_status',`total_price`,0) `won`, SUM(IF((`status`='Archive',`total_price`,0) `lost` FROM `estimate` WHERE `deleted`=0"));
	$summary_blocks[] = '<div class="overview-block"><h4>Revenue: $'.number_format($summary['won'],2).' Won / $'.number_format($summary['lost'],2).' Lost</h4></div>';
	$summary_height[] = 38;
	$total_height += $block_height;
}
if(in_array('Revenue Won by Type',$summary_view)) {
	$block_details = '<div class="overview-block"><h4>'.ESTIMATE_TILE.' Value by Type</h4>';
	$block_height = 38;
	foreach($estimate_types as $type) {
		$summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(IF((`status`='$closed_status',`total_price`,0) `won`, SUM(IF((`status`='Archive',`total_price`,0) `lost` FROM `estimate` WHERE `estimatetype`='$type' AND `deleted`=0"));
		$block_details .= $type.': $'.number_format($summary['won'],2).' Won / $'.number_format($summary['lost'],2).' Lost<br />';
		$block_height += 18;
	}
	$block_details .= '</div>';
	$summary_blocks[] = $block_details;
	$summary_height[] = $block_height;
	$total_height += $block_height;
}
if(in_array('Closing Rate',$summary_view)) {
	$summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(IF(`status`='$closed_status',1,0))/COUNT(*) `closed` FROM `estimate` WHERE `deleted`=0"));
	$summary_blocks[] = '<div class="overview-block"><h4>Closing Rate: '.number_format($summary['closed'] * 100,1).'%</h4></div>';
	$summary_height[] = 38;
	$total_height += $block_height;
}
if(in_array('Average Complete',$summary_view)) {
	$summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT SUM(DATEDIFF(`status_date`,`created_date`))/COUNT(*) `days` FROM `estimate` WHERE `status`='$closed_status' AND `deleted`=0"));
	$summary_blocks[] = '<div class="overview-block"><h4>Average Time to Complete: '.round($summary['days'],2).' days</h4></div>';
	$summary_height[] = 38;
	$total_height += $block_height;
}
foreach($estimate_status as $status) {
	if(in_array('Report Status '.$status,$summary_view)) {
		$summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) total, SUM(`total_price`) value FROM `estimate` WHERE `deleted`=0 AND `status`='".preg_replace('/[^a-z]/','',strtolower($status))."'"));
		$summary_blocks[] = '<div class="overview-block"><h4>Total '.$status.': '.number_format($summary['total'],0).'</h4></div>';
		$summary_height[] = 38;
		$total_height += 38;
		$summary_blocks[] = '<div class="overview-block"><h4>Value of '.$status.': $'.number_format($summary['value'],2).'</h4></div>';
		$summary_height[] = 38;
		$total_height += 38;
		$summary_blocks[] = '<div class="overview-block"><h4>% of '.$status.': '.number_format($summary['total'] / $summary_total * 100,1).'%</h4></div>';
		$summary_height[] = 38;
		$total_height += 38;
	}
}
echo '<div class="col-sm-6">';
$output_height = 0;
$split_col = false;
foreach($summary_blocks as $i => $block) {
	if(!$split_col && $i > 0 && ($output_height > ($total_height / 2) || $i == count($summary_blocks) - 1)) {
		$split_col = true;
		echo '</div><div class="col-sm-6">';
	}
	echo $block;
	$output_height += $summary_height[$i];
}
echo '</div>'; ?>