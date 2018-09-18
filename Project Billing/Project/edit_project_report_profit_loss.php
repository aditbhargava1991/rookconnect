<?php error_reporting(0);
include_once('../include.php');
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project'); ?>
<!-- <h3>Profit &amp; Loss</h3> -->
<div class="notice double-gap-top double-gap-bottom popover-examples">
    <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
    <div class="col-sm-11"><span class="notice-name">NOTE: </span>View all profit &amp; loss details of the work put towards this project.</div>
    <div class="clearfix"></div>
</div>
<?php $projectid = $_GET['edit'];
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));

$items = [];
$packages = array_filter(explode('**',$project['package']));
foreach($packages as $package) {
	$items[] = array_merge(['Package'],explode('#',$package));
}
$promotions = array_filter(explode('**',$project['promotion']));
foreach($promotions as $promotion) {
	$items[] = array_merge(['Promotion'],explode('#',$promotion));
}
$materials = array_filter(explode('**',$project['material']));
foreach($materials as $material) {
	$items[] = array_merge(['Material'],explode('#',$material));
}
$services = array_filter(explode('**',$project['services']));
foreach($services as $service) {
	$items[] = array_merge(['Service'],explode('#',$service));
}
$products = array_filter(explode('**',$project['products']));
foreach($products as $product) {
	$items[] = array_merge(['Product'],explode('#',$product));
}
$sreds = array_filter(explode('**',$project['sred']));
foreach($sreds as $sred) {
	$items[] = array_merge(['SR&ED'],explode('#',$sred));
}
$labours = array_filter(explode('**',$project['labour']));
foreach($labours as $labour) {
	$items[] = array_merge(['Labour'],explode('#',$labour));
}
$clients = array_filter(explode('**',$project['client']));
foreach($clients as $client) {
	$items[] = array_merge(['Client'],explode('#',$client));
}
$customers = array_filter(explode('**',$project['customer']));
foreach($customers as $customer) {
	$items[] = array_merge(['Customer'],explode('#',$customer));
}
$inventories = array_filter(explode('**',$project['inventory']));
foreach($inventories as $inventory) {
	$items[] = array_merge(['Inventory'],explode('#',$inventory));
}
$equipments = array_filter(explode('**',$project['equipment']));
foreach($equipments as $equipment) {
	$items[] = array_merge(['Equipment'],explode('#',$equipment));
}
$staffs = array_filter(explode('**',$project['staff']));
foreach($staffs as $staff) {
	$items[] = array_merge(['Staff'],explode('#',$staff));
}
$contractors = array_filter(explode('**',$project['contractor']));
foreach($contractors as $contractor) {
	$items[] = array_merge(['Contractor'],explode('#',$contractor));
}
$expenses = array_filter(explode('**',$project['expense']));
foreach($expenses as $expense) {
	$items[] = array_merge(['Expense'],explode('#',$expense));
}
$vendors = array_filter(explode('**',$project['vendor']));
foreach($vendors as $vendor) {
	$items[] = array_merge(['Vendor'],explode('#',$vendor));
}
$customs = array_filter(explode('**',$project['custom']));
foreach($customs as $custom) {
	$items[] = array_merge(['Custom'],explode('#',$custom));
}
$others = array_filter(explode('**',$project['other_detail']));
foreach($others as $other) {
	$items[] = array_merge(['Other'],explode('#',$other));
}
$purchase_orders = mysqli_query($dbc, "SELECT `posid`, `po_category`, `status`, `total_price` FROM `purchase_orders` WHERE `projectid`='$projectid' AND `deleted`=0");
while($po = mysqli_fetch_array($purchase_orders)) {
	$items[] = ['Purchase Order','PO #'.$po['posid'], implode(': ',array_filter([$po['po_category'],$po['status']])), $po['total_price']];
}
$tickets = mysqli_query($dbc, "SELECT `tickets`.*, `ticket_timer`.`start_time`, `ticket_timer`.`end_time`, `hourly` FROM `ticket_timer` LEFT JOIN `tickets` ON `ticket_timer`.`ticketid`=`tickets`.`ticketid` AND `ticket_timer`.`deleted` = 0 LEFT JOIN `staff_rate_table` ON CONCAT(',',`staff_rate_table`.`staff_id`,',') LIKE CONCAT('%,',`ticket_timer`.`created_by`,',%') AND `staff_rate_table`.`deleted`=0 WHERE `tickets`.`projectid`='$projectid' GROUP BY `ticket_timer`.`tickettimerid` ORDER BY `tickettimerid`");
while($ticket = mysqli_fetch_array($tickets)) {
	$end = $ticket['end_time'];
	if(empty($end)) {
		$end = date('h:i A');
	}
	$hours = (strtotime($end) - strtotime($ticket['start_time'])) / 3600;
	$items[] = ['Ticket', '<a href="../Ticket/index.php?edit='.$ticket['ticketid'].'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\',\'auto\',true,true); return false;">'.get_ticket_label($dbc,$ticket).'</a>',$ticket['heading']." (".round($hours,2)." hours)",round($hours * $ticket['hourly'],2)];
}
$tasks = mysqli_query($dbc, "SELECT `time_id`, tasks.`project_milestone`, tasks.`heading`, timer`work_time`, `hourly` FROM `tasklist_time` timer LEFT JOIN `tasklist` tasks ON timer.`tasklistid`=tasks.`tasklistid` LEFT JOIN `staff_rate_table` ON CONCAT(',',`staff_rate_table`.`staff_id`,',') LIKE CONCAT('%,',timer.`contactid`,',%') AND `staff_rate_table`.`deleted`=0 AND DATE(NOW()) BETWEEN `staff_rate_table`.`start_date` AND IFNULL(NULLIF(`staff_rate_table`.`end_date`,'0000-00-00'),'9999-12-31') WHERE tasks.`projectid`='$projectid' GROUP BY timer.`time_id` ORDER BY `time_id`");
while($task = mysqli_fetch_array($taskss)) {
	$hours = explode(':',$task['work_time']);
	$hours = $hours[0] + ($hours[1] / 60) + ($hours[2] / 3600);
	$items[] = ['Task', $task['project_milestone'].' Milestone',html_entity_decode($task['heading'])." (".round($hours,2)." hours)",round($hours * $task['hourly'],2)];
}
echo '<a href="?edit='.$projectid.'&tab=profitloss&output=PDF" class="pull-right"><img src="../img/pdf.png" class="inline-img"></a>';
$table = '';
$table .= '<div id="no-more-tables"><table class="table table-bordered" style="width:100%;" cellpadding="3" cellspacing="0" border="1">';
$table .= '<tr class="hidden-xs hidden-sm">
	<th>Type</th>
	<th>Heading</th>
	<th>Description</th>
	<th>Cost</th>
	</tr>';

$total = 0;
foreach($items as $item) {
	$type = $item[0];
	$heading = '';
	$description = '';
	$price = 0;
	if($type == 'Other') {
		$description = $item[1];
		$price = $item[2];
	} else if($type == 'Expense') {
		$heading = $item[1];
		$description = $item[2];
		$price = $item[3];
	} else if($type == 'Labour') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `labour_type`, `heading` FROM `labour` WHERE `labourid`=".$item[1]));
		$heading = $result['labour_type'];
		$description = $result['heading'];
		$price = $item[2] * $item[3];
	} else if($type == 'Equipment') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, CONCAT(`unit_number`,' : ',`serial_number`) `description` FROM `equipment` WHERE `equipmentid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['description'];
		$price = $item[2] * $item[3];
	} else if($type == 'Inventory') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, CONCAT(`unit_number`,' : ',`serial_number`) `description` FROM `inventory` WHERE `inventoryid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['description'];
		$price = $item[2] * $item[3];
	} else if($type == 'Customer') {
		$description = get_contact($dbc, $item[1]);
		$price = $item[2];
	} else if($type == 'Vendor') {
		$description = get_contact($dbc, $item[1], 'name');
		$price = $item[2] * $item[3];
	} else if($type == 'Client') {
		$description = get_contact($dbc, $item[1], 'name');
		$price = $item[2];
	} else if($type == 'Contractor') {
		$description = get_contact($dbc, $item[1]);
		$price = $item[2] * $item[3];
	} else if($type == 'Staff') {
		$description = get_contact($dbc, $item[1]);
		$price = $item[2] * $item[3];
	} else if($type == 'SR&ED') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `sred` WHERE `sredid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['heading'];
		$price = $item[2] * $item[3];
	} else if($type == 'Product') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `products` WHERE `productid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['heading'];
		$price = $item[2] * $item[3];
	} else if($type == 'Service') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `services` WHERE `serviceid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['heading'];
		$price = $item[2] * $item[3];
	} else if($type == 'Material') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `code`, `name` FROM `material` WHERE `materialid`=".$item[1]));
		$heading = $result['code'];
		$description = $result['name'];
		$price = $item[2] * $item[3];
	} else if($type == 'Custom') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `custom` WHERE `customid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['heading'];
		$price = $item[2];
	} else if($type == 'Promotion') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `promotion` WHERE `promotionid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['heading'];
		$price = $item[2];
	} else if($type == 'Package') {
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `heading` FROM `package` WHERE `packageid`=".$item[1]));
		$heading = $result['category'];
		$description = $result['heading'];
		$price = $item[2];
	} else {
		$heading = $item[1];
		$description = $item[2];
		$price = $item[3];
	}
	$total += $price;
	$table .= '<tr><td data-title="Type">'.$type.'</td>';
	$table .= '<td data-title="Heading">'.$heading.'</td>';
	$table .= '<td data-title="Description">'.$description.'</td>';
	$table .= '<td data-title="Cost">'.$price.'</td>';
	$table .= "</tr>\n";
}

$table .= '<tr><td colspan="3">Total</td>
    <td data-title="Estimate Price">'.$total.'</td>
    </tr></table></div>';
if($_GET['output'] == 'PDF') {
    include('../tcpdf/tcpdf.php');
    ob_clean();
    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));
    class MYPDF extends TCPDF {
        public function Header() {
            //$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
            if(REPORT_LOGO != '') {
                $image_file = 'download/'.REPORT_LOGO;
                $this->Image($image_file, 10, 10, '', '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);
            $this->SetFont('helvetica', '', 13);
            $footer_text = PROJECT_NOUN.' Profit &amp; Loss';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);
        }
        // Page footer
        public function Footer() {
            $this->SetY(-24);
            $this->SetFont('helvetica', 'I', 9);
            $footer_text = '<span style="text-align:left;">'.REPORT_FOOTER.'</span>';
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
            // Position at 15 mm from bottom
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 9);
            $footer_text = '<span style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().' printed on '.date('Y-m-d H:i:s').'</span>';
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
        }
    }
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    $pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);
    $pdf->writeHTML($table, true, false, true, false, '');
    $pdf->Output(config_safe_str(PROJECT_NOUN).'_'.$projectid.'_profitloss_'.$today_date.'.pdf', 'I');
    exit();
} else {
    echo $table;
}
include('next_buttons.php'); ?>
