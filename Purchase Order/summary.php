<?php $blocks = [];
$total_height = 0;
if(in_array('summary amts',$po_tabs)) {
	$block_height = 38;
	$block = '<h4>Outstanding Amounts</h4>';
	foreach(array_unique(explode(',',$po_types.',')) as $i => $po_type) {
		$amt = $dbc->query("SELECT SUM(`total_price`) `total` FROM `purchase_orders` WHERE `deleted`=0 AND `status` != 'Completed' AND `po_category`='$po_type'")->fetch_assoc();
		$block .= '<div>Outstanding'.($po_type == '' && $i == 0 ? '' : ($po_type == '' ? ' for Other' : ' for '.$po_type)).': $'.number_format($amt['total'],2).'</div>';
		$block_height += 18;
	}
	$blocks[] = [ '<div class="overview-block">'.$block.'</div>', $block_height ];
	$total_height += $block_height;
}
if(in_array('summary outstanding',$po_tabs)) {
	$block_height = 38;
	$block = '<h4>Outstanding Purchase Orders</h4>';
	foreach(array_unique(explode(',',$po_types.',')) as $i => $po_type) {
		$amt = $dbc->query("SELECT COUNT(*) `total` FROM `purchase_orders` WHERE `deleted`=0 AND `status` != 'Completed' AND `po_category`='$po_type'")->fetch_assoc();
		$block .= '<div>Outstanding'.($po_type == '' && $i == 0 ? '' : ($po_type == '' ? ' for Other' : ' for '.$po_type)).': '.$amt['total'].'</div>';
		$block_height += 18;
	}
	$blocks[] = [ '<div class="overview-block">'.$block.'</div>', $block_height ];
	$total_height += $block_height;
}
if(in_array('summary date',$po_tabs)) {
	$block_height = 74;
	$block = '<h4>Date Sent</h4>';
	$sent_dates = $dbc->query("SELECT * FROM `purchase_orders` WHERE `deleted`=0 AND `status` != 'Completed' AND `date_sent` IS NOT NULL");
	while($sent = $sent_dates->fetch_assoc()) {
		foreach(explode('#*#',$sent['date_sent']) as $i => $date) {
			if($date != '') {
				$block .= '<div>PO #'.$sent['posid'].' '.$sent['name'].' Sent by '.explode('#*#',$sent['sent_by'])[$i].' on '.$date.'</div>';
				$block_height += 18;
			}
		}
	}
	$blocks[] = [ '<div class="overview-block">'.$block.'</div>', $block_height ];
	$total_height += $block_height;
}
if(in_array('summary details',$po_tabs)) {
	$block_height = 38;
	$block = '<h4>Details</h4>';
	$po_list = $dbc->query("SELECT * FROM `purchase_orders` WHERE `deleted`=0 AND `status` != 'Completed'");
	while($po = $po_list->fetch_assoc()) {
		$block .= '<div><a href="../Ticket/line_item_views.php?po='.$po['name'].'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;">'.$po['status'].' PO #'.$po['posid'].': '.$po['name'].' Details <img src="../img/icons/eyeball.png" class="inline-img"></a></div>';
		$block_height += 19;
	}
	$blocks[] = [ '<div class="overview-block">'.$block.'</div>', $block_height ];
	$total_height += $block_height;
}
echo '<div class="col-sm-6">';
$output_height = 0;
foreach($blocks as $i => $block) {
	if($output_height >= $total_height / 2 || $i == count($blocks) - 1) {
		echo '</div><div class="col-sm-6">';
	}
	echo $block[0];
	$output_height += $block[1];
} ?>
</div>