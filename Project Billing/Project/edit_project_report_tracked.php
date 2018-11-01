<?php include_once('../include.php');
error_reporting(0);
if(!isset($project)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
}
$time_cards_edit = vuaed_visible_function($dbc, 'time_cards');
$project_edit = vuaed_visible_function($dbc, 'project');
$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
$table = '';
$result = mysqli_query($dbc, "SELECT * FROM (SELECT CONCAT('Time Sheet for ',`date`) time_type, 'timesheet' `src`, 0 `srcid`, 'time_cards' `table`, `time_cards`.`time_cards_id` `tableid`, 'Time Sheet' time_heading, `staff` time_staff, `date` time_date, `start_time` time_start, `end_time` time_end, SEC_TO_TIME(`total_hrs` * 3600) time_length, `time_cards_id` FROM `time_cards` LEFT JOIN `ticket_time_list` ON `ticket_time_list`.`created_date` LIKE CONCAT(`time_cards`.`date`,'%') AND `time_cards`.`ticketid`=`ticket_time_list`.`ticketid` AND `time_cards`.`staff`=`ticket_time_list`.`created_by` AND `time_cards`.`total_hrs` * 3600 = TIME_TO_SEC(`ticket_time_list`.`time_length`) AND `ticket_time_list`.`deleted`=0 WHERE `time_cards`.`deleted`=0 AND `projectid`='$projectid' AND `ticket_time_list`.`id` IS NULL UNION
	SELECT CONCAT('Checklist ',`checklist`.`checklist_name`,' Item #',`checklist_name`.`checklistnameid`) time_type, 'checklist' `src`, `checklist`.`checklistid` `srcid`, 'checklist_name' `table`, `checklist_name_time`.`checklist_time_id` `tableid`, `checklist_name`.`checklist` time_heading, `checklist_name_time`.`contactid` time_staff, `checklist_name_time`.`timer_date` time_date, '' time_start, '' time_end, `checklist_name_time`.`work_time` time_length, '' `time_cards_id` FROM `checklist` RIGHT JOIN `checklist_name` ON `checklist`.`checklistid`=`checklist_name`.`checklistid` RIGHT JOIN `checklist_name_time` ON `checklist_name_time`.`checklist_id`=`checklist_name`.`checklistnameid` WHERE `projectid`='$projectid' UNION
	SELECT CONCAT('".TICKET_NOUN." #',`tickets`.`ticketid`) time_type, 'ticket' `src`, `tickets`.`ticketid` `srcid`, 'tickets' `table`, `ticket_timer`.`tickettimerid` `tableid`, `tickets`.`heading` time_heading, `ticket_timer`.`created_by` time_staff,  `ticket_timer`.`created_date` time_date, `ticket_timer`.`start_time` time_start, `ticket_timer`.`end_time` time_end, TIMEDIFF(`ticket_timer`.`end_time`,`ticket_timer`.`start_time`) time_lengt, '' `time_cards_id` FROM `tickets` RIGHT JOIN `ticket_timer` ON `tickets`.`ticketid`=`ticket_timer`.`ticketid` WHERE `projectid`='$projectid' AND `ticket_timer`.`deleted` = 0 UNION
	SELECT CONCAT('".TICKET_NOUN." #',`ticket_attached`.`ticketid`) time_type, 'ticket' `src`, `ticket_attached`.`ticketid` `srcid`, 'ticket_attached' `table`, `ticket_attached`.`id` `tableid`, IF(`ticket_attached`.`position` > 0,`positions`.`name`,`ticket_attached`.`position`) time_heading, `ticket_attached`.`hours_tracked` time_staff,  `ticket_attached`.`date_stamp` time_date, '' time_start, '' time_end, `ticket_attached`.`hours_tracked` time_length, '' `time_cards_id` FROM `ticket_attached` LEFT JOIN `positions` ON `positions`.`position_id`=`ticket_attached`.`position` WHERE `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid' AND `deleted`=0) AND `ticket_attached`.`deleted`=0 UNION
	SELECT CONCAT('".TICKET_NOUN." #',`ticket_time_list`.`ticketid`) time_type, 'ticket' `src`, `ticket_time_list`.`ticketid` `srcid`, 'ticket_time_list' `table`, `ticket_time_list`.`id` `tableid`, `ticket_time_list`.`time_type` time_heading, `ticket_time_list`.`created_by` time_staff,  LEFT(`ticket_time_list`.`created_date`,10) time_date, MID(`ticket_time_list`.`created_date`,11) time_start, '' time_end, `ticket_time_list`.`time_length` time_length, '' `time_cards_id` FROM `ticket_time_list` WHERE `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid' AND `time_type`='Manual Time' AND `deleted`=0) AND `deleted`=0 UNION
	SELECT CONCAT('Task #',`tasklist`.`tasklistid`) time_type, 'task' `src`, `tasklist`.`tasklistid` `srcid`, 'tasklist' `table`, `tasklist`.`tasklistid` `tableid`, `tasklist`.`heading` time_heading, `tasklist`.`contactid` time_staff, `tasklist`.`task_tododate` time_date, '' time_start, '' time_end, `tasklist`.`work_time` time_length, '' `time_cards_id` FROM `tasklist` WHERE `projectid`='$projectid' UNION
	SELECT CONCAT(`tasklist`.`project_milestone`,' Task #',`tasklist`.`tasklistid`) time_type, 'task' `src`, `tasklist`.`tasklistid` `srcid`, 'tasklist_time' `table`, `tasklist_time`.`time_id` `tableid`, `tasklist`.`heading` time_heading, `tasklist_time`.`contactid` time_staff, `tasklist_time`.`timer_date` time_date, '' time_start, '' time_end, `tasklist_time`.`work_time` time_length, '' `time_cards_id` FROM `tasklist` RIGHT JOIN `tasklist_time` ON `tasklist`.`tasklistid`=`tasklist_time`.`tasklistid` WHERE `tasklist`.`projectid`='$projectid') timers ORDER BY `time_date`, `time_start`");
echo '<h3>&nbsp;';
	if(in_array('Reporting Track Time',$value_config)) {
		$tracking = $dbc->query("SELECT * FROM `time_cards` WHERE (`projectid`='$projectid' OR `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid')) AND `staff`='".$_SESSION['contactid']."' AND `deleted`=0 AND `timer_start` > 0");
		echo '<button class="btn brand-btn pull-right cursor-hand time_tracking" onclick="toggleProjectTracking()">'.($tracking->num_rows > 0 ? 'Stop Tracking Time' : 'Get to Work').'</button>';
	}
	echo ($time_cards_edit ? '<button class="btn brand-btn pull-right cursor-hand" onclick="overlayIFrameSlider(\'../Timesheet/add_time_cards.php?projectid='.$projectid.'\',\'auto\',true); return false;">Add Time</button>' : '');
echo '<a href="?edit='.$projectid.'&tab=track_time&output=PDF" class="pull-right"><img src="../img/pdf.png" class="inline-img"></a></h3>';
if(mysqli_num_rows($result) > 0) {
    $table .= '<div id="no-more-tables"><table class="table table-bordered" style="width:100%;" cellpadding="3" cellspacing="0" border="1">';
    $table .= '<tr class="hidden-xs hidden-sm">
        <th>Type</th>
        <th>Heading</th>
        <th>Staff</th>
        <th>Date</th>
        <th>Start Time</th>
        <th>End Time</th>
        <th>Duration</th>
        '.($time_cards_edit || $project_edit ? '<th>Function</th>' : '').'
        </tr>';
	$total_time = 0;
	while($row = mysqli_fetch_array( $result )) {
		$table .= '<tr>';

		$time_length = date('G:i',strtotime(date('Y-m-d ').$row['time_length']));
		$minutes = explode(':',$time_length);
		$total_time += ($minutes[0] * 60) + $minutes[1];

		$table .= '<td data-title="Type">' .($row['src'] == 'ticket' ? '<a href="" onclick="overlayIFrameSlider(\'../Ticket/index.php?edit='.$row['srcid'].'&calendar_view=true\'); return false;">'.get_ticket_label($dbc, $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='".$row['srcid']."'")->fetch_assoc()).'</a>' : ($row['src'] == 'task' ? '<a href="" onclick="overlayIFrameSlider(\'../Tasks_Updated/add_task.php?tasklistid='.$row['srcid'].'\'); return false;">'.$row['time_type'].'</a>' : $row['time_type'])) . '</td>';
		$table .= '<td data-title="Heading">' . html_entity_decode($row['time_heading']) . '</td>';
		$table .= '<td data-title="Staff">' . get_contact($dbc, $row['time_staff']) . '</td>';
		$table .= '<td data-title="Date">' . $row['time_date'] . '</td>';
		$table .= '<td data-title="Start Time">' . $row['time_start'] . '</td>';
		$table .= '<td data-title="End Time">' . $row['time_end'] . '</td>';
		$table .= '<td data-title="Duration">' . $time_length . '</td>';
		if($time_cards_edit || $project_edit) {
			$table .= '<td data-title="Function">';
			if($row['time_cards_id'] > 0 && $time_cards_edit) {
				$table .= '<a href="" onclick="overlayIFrameSlider(\'../Timesheet/add_time_cards.php?projectid='.$projectid.'&time_cards_id='.$row['time_cards_id'].'\',\'auto\',true); return false;">Edit</a>';
			} else if(empty($row['time_cards_id']) && $project_edit) {
				$table .= '<a href="" onclick="overlayIFrameSlider(\'project_edit_time.php?projectid='.$projectid.'&src='.$row['table'].'&id='.$row['tableid'].'\',\'auto\',true); return false;">Edit</a>';
			}
			$table .= '</td>';
		}

		$table .= "</tr>";
	}
    $table .= '<tr>
        <td colspan="6">Total Time Tracked</td>
        <td data-title="Total Time Tracked">'.floor($total_time/60).':'.sprintf("%02d", $total_time%60).'</td>
        '.(vuaed_visible_function($dbc, 'time_cards') ? '<td></td>' : '').'
        </tr>';

	$table .= '</table></div>';
} else {
    $table .= "<h2>No Time Found.</h2>";
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
            $footer_text = PROJECT_NOUN.' Tracked Time';
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
    $pdf->Output(config_safe_str(PROJECT_NOUN).'_'.$projectid.'_tracked_time_'.$today_date.'.pdf', 'I');
    exit();
} else {
    echo $table;
}
include('next_buttons.php'); ?>