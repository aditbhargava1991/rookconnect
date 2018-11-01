<?php include_once('../include.php');
error_reporting(0);
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($project)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
}
$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
$result = mysqli_query($dbc, "SELECT `times`.`time`, `times`.`type`, `times`.`label`, `tickets`.* FROM (SELECT '' `label`, `tickets`.`ticketid`, 'Completion Estimate' `type`, IFNULL(`total_time`,`max_time`) `time` FROM `tickets` LEFT JOIN (SELECT `ticketid`, SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `total_time` FROM `ticket_time_list` WHERE `deleted`=0 AND `time_type` IN ('Completion Estimate') GROUP BY `ticketid`, `time_type`) `time` ON `tickets`.`ticketid`=`time`.`ticketid` WHERE `deleted`=0 AND IFNULL(`max_time`,'00:00:00') != '00:00:00' AND `projectid`='$projectid' UNION
	SELECT '' `label`, `tickets`.`ticketid`, 'QA Estimate' `type`, IFNULL(`total_time`,`max_qa_time`) `time` FROM `tickets` LEFT JOIN (SELECT `ticketid`, SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `total_time` FROM `ticket_time_list` WHERE `deleted`=0 AND `time_type` IN ('QA Estimate') GROUP BY `ticketid`, `time_type`) `time` ON `tickets`.`ticketid`=`time`.`ticketid` WHERE `deleted`=0 AND IFNULL(`max_qa_time`,'00:00:00') != '00:00:00' AND `projectid`='$projectid' UNION
	SELECT `date_of_meeting` `label`, 0 `ticketid`, `type`, SEC_TO_TIME(TIME_TO_SEC(STR_TO_DATE(`end_time_of_meeting`,'%h:%i %p')) - TIME_TO_SEC(STR_TO_DATE(`time_of_meeting`,'%h:%i %p'))) `time` FROM `agenda_meeting` WHERE `projectid`='6') `times` LEFT JOIN `tickets` ON `tickets`.`ticketid`=`times`.`ticketid` ORDER BY `times`.`ticketid`, `times`.`type`");
echo '<h3>Estimated Time<a href="?edit='.$projectid.'&tab=estimate_time&output=PDF" class="pull-right"><img src="../img/pdf.png" class="inline-img"></a></h3>';
$table = '';
if(mysqli_num_rows($result) > 0) {
    $table .= '<div id="no-more-tables"><table class="table table-bordered" style="width:100%;" cellpadding="3" cellspacing="0" border="1">';
    $table .= '<tr class="hidden-xs hidden-sm">
        <th>Item</th>
        <th>Type</th>
        <th>Time</th>
        </tr>';
	$total_time = 0;
	while($row = mysqli_fetch_array( $result )) {
		$table .= '<tr>';
		
		$time_length = date('G:i',strtotime(date('Y-m-d ').$row['time']));
		$seconds = explode(':',$row['time']);
		$total_time += ($seconds[0] * 3600) + ($seconds[1] * 60) + $seconds[2];

		$table .= '<td data-title="'.($row['label'] != '' ? 'Date' : TICKET_NOUN).'">' . ($row['label'] != '' ? $row['label'] : '<a href="" onclick="overlayIFrameSlider(\'../Ticket/index.php?edit='.$row['srcid'].'\'); return false;">'.get_ticket_label($dbc,$row).'</a>') . '</td>';
		$table .= '<td data-title="Type">' . $row['type'] . '</td>';
		$table .= '<td data-title="Time">' . $row['time'] . '</td>';

		$table .= "</tr>";
	}
    $table .= '<tr>
        <td colspan="2">Total Estimated Time</td>
        <td data-title="Total Estimated Time">'.floor($total_time/3600).':'.sprintf("%02d", floor($total_time/60)%60).':'.sprintf("%02d", $total_time%60).'</td>
        </tr>';

	$table .= '</table></div>';
} else {
    $table .= "<h2>No Estimated Time Found.</h2>";
}
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
            $footer_text = PROJECT_NOUN.' Estimated Time';
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
    $pdf->Output(config_safe_str(PROJECT_NOUN).'_'.$projectid.'_time_estimate_'.$today_date.'.pdf', 'I');
    exit();
} else {
    echo $table;
}
include('next_buttons.php'); ?>