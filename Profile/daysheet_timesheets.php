<!-- Daysheet My Time Sheets -->
<?php
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {

    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];
    $staffidpdf = $_POST['staffidpdf'];
    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));

	class MYPDF extends TCPDF {

		public function Header() {
			//$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
            if(REPORT_LOGO != '') {
                $image_file = '../Reports/download/'.REPORT_LOGO;
                $this->Image($image_file, 'C', 10, '', '', '', false, 'C', false, 300, 'C', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "C", true);

            $this->SetFont('helvetica', 'B', 15);
            $footer_text = 'Daysheet Report';
            $this->writeHTMLCell(0, 0, 0 , 50, $footer_text, 0, 0, false, "C", true);
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 60, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $start_date = date('Y-m-d', strtotime($starttimepdf));
    $end_date = date('Y-m-d', strtotime($endtimepdf));
    $html = '';

	if(count(array_filter(explode(',',$staffidpdf))) > 0) {
        $pdf->AddPage('L', 'LETTER');
        $pdf->SetFont('helvetica', '', 9);
        $html .= '<br><br>' . report_receivables($dbc, $start_date, $end_date, $staffidpdf, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');
        $pdf->writeHTML($html, true, false, true, false, '');
	} else {
		for($date = $start_date; $date <= $end_date; $date = date('Y-m-d', strtotime($date. ' + 1 days')))
		{
			$pdf->AddPage('L', 'LETTER');
			$pdf->SetFont('helvetica', '', 9);
			$html .= '<br><br>' . report_receivables($dbc, $date, $date, $staffidpdf, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');
			$pdf->writeHTML($html, true, false, true, false, '');
		}
	}

    $today_date = date('Y-m-d');
	//$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('../Reports/Download/daysheet_report_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'reports_daysheet_reports', 0, WEBSITE_URL.'/Reports/Download/daysheet_report_'.$today_date.'.pdf', 'Daysheet Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('../Reports/Download/daysheet_report_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $staffid = $staffidpdf;
    } ?>

<script type="text/javascript">
function handleClick(sel) {
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=daysheet_report",
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
</script>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $staffid = implode(',',$_POST['staffid']);
            }

            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-d');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }
            ?>
            <center>
                <div class="form-group">
                    <div class="form-group col-sm-4">
                        <label class="col-sm-4 control-label">From:</label>
                        <div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                    </div>
                    <div class="form-group col-sm-4">
                        <label class="col-sm-4 control-label">Until:</label>
                        <div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
                    </div>
                    <div class="form-group col-sm-2">
                        <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
                    </div>
                    <div class="form-group col-sm-2">
                        <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
                    </div>
                </div>
            </center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="staffidpdf" value="<?php echo $_SESSION['contactid']; ?>">

            <?php
                $start_date = date('Y-m-d', strtotime($starttime));
                $end_date = date('Y-m-d', strtotime($endtime));

				echo report_receivables($dbc, $start_date, $end_date, $_SESSION['contactid'], '', '', '');

            ?>

        </form>

<?php
function report_receivables($dbc, $starttime, $endtime, $staff, $table_style, $table_row_style, $grand_total_style) {
	$staff = array_filter(array_unique(explode(',',$staff)));
	if(count($staff) > 0) {
		$query = $staff;
	} else if(count($staff) == 1) {
		$query = $staff;
	} else {
		$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status=1 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""),MYSQLI_ASSOC));
	}
	if(count($staff) == 1 && $staff[0] > 0) {
		$report_data .= '<h3>'.get_contact($dbc, $staff[0]).'</h3>';
	} else {
		$report_data .= '<h3>'.$starttime.'</h3>';
	}

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'" width="100%">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="15%">'.(count($staff) == 1 && $staff[0] > 0 ? 'Date' : 'Staff').'</th>
    <th width="15%">Tile</th>
    <th width="46%">Description</th>
    <th width="8%">Total Timer Time</th>
    <th width="8%">Total Entered Time</th>
    <th width="8%">Total Time</th>
    </tr>';
    
    $final_total_timer = [];
    $final_total_entered = [];
    $final_total_time = [];
	
    for($date = $starttime; $date <= $endtime; $date = date('Y-m-d', strtotime($date. ' + 1 days')))
	{
		foreach($query as $contactid) {
			$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$contactid'"));
			$contactid = ','.$row['contactid'].',';
			$cid = $row['contactid'];

			/* $report_data .= '<tr nobr="true">';
			if(count($staff) == 1 && $staff[0] > 0) {
				$report_data .= '<td>'.$date.'</td>';
			} else {
				$report_data .= '<td>'.get_staff($dbc,$row['contactid']).'</td>';
			} */

			$ticket_list = [];
			$task_list = '';
			$checklist_list = '';
			$total_ticket_spent_time = [];
			$total_timer = [];
			$total_spent = [];
			$total_all = [];
			$staff_total_timer = [];
			$staff_total_spent = [];
			$staff_total_all = [];

			$total_tracked_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time`))) `time` FROM (SELECT `time_length` `time` FROM `ticket_time_list` WHERE `created_by`='$cid' AND `created_date` LIKE '$date%' AND `deleted`=0 AND `time_type`='Manual Time' UNION SELECT `timer` `time` FROM `ticket_timer` WHERE `created_by`='$cid' AND `created_date` LIKE '$date%' AND `deleted` = 0) `time_list`")->fetch_assoc()['time'];

			//$tickets = mysqli_query($dbc, "SELECT `tickets`.*, SEC_TO_TIME(SUM(TIME_TO_SEC(`ticket_timer`.`timer`)) + SUM(TIME_TO_SEC(`ticket_time_list`.`time_length`))) `time_spent`, SEC_TO_TIME(SUM(TIME_TO_SEC(`ticket_timer`.`timer`))) `timer_total`, SEC_TO_TIME(SUM(TIME_TO_SEC(`ticket_time_list`.`time_length`))) `manual_time` FROM `tickets` LEFT JOIN `ticket_time_list` ON `ticket_time_list`.`ticketid`=`tickets`.`ticketid` AND `ticket_time_list`.`deleted`=0 AND `ticket_time_list`.`time_type`='Manual Time' LEFT JOIN `ticket_timer` ON `tickets`.`ticketid`=`ticket_timer`.`ticketid`  WHERE `ticket_timer`.`created_date` LIKE '$date%' AND `ticket_timer`.`created_by`='$cid' GROUP BY `tickets`.`ticketid`");

            //Tickets
            $tickets = mysqli_query($dbc, "SELECT `tickets`.*, SEC_TO_TIME(SUM(TIME_TO_SEC(`timers`.`time`))) `time_spent`, SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`timers`.`type`='Tracked',`timers`.`time`,0)))) `timer_total`, SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`timers`.`type`='Manual',`timers`.`time`,0)))) `manual_time` FROM `tickets` LEFT JOIN (SELECT `ticketid`,`created_by`,`created_date`,`time_length` `time`, 'Manual' `type` FROM `ticket_time_list` WHERE `time_type`='Manual Time' AND `deleted`=0 UNION SELECT `ticketid`,`created_by`,`created_date`,`timer` `time`, 'Tracked' `type` FROM `ticket_timer`) `timers` ON `tickets`.`ticketid`=`timers`.`ticketid` WHERE `timers`.`created_date` LIKE '$date%' AND `timers`.`created_by` LIKE '$cid' AND `timers`.`deleted` = 0 GROUP BY `tickets`.`ticketid`");

			while($ticket = $tickets->fetch_assoc()) {
                $total_all[] = $ticket['timer_total'];
                $total_all[] = $ticket['manual_time'];
                
                $report_data .= '<tr nobr="true">';
                    $report_data .= (count($staff) == 1 && $staff[0] > 0) ? '<td>'.$date.'</td>' : '<td>'.get_staff($dbc,$row['contactid']).'</td>';
                    $report_data .= '<td>'. TICKET_TILE .'</td>';
                    $report_data .= '<td>'. get_ticket_label($dbc, $ticket) .'</td>';
                    $report_data .= '<td>'. $ticket['timer_total'] .'</td>';
                    $report_data .= '<td>'. $ticket['manual_time'] .'</td>';
                    $report_data .= '<td>'. AddPlayTime($total_all) .'</td>';
                $report_data .= '</tr>';

                $final_total_timer[] = $ticket['timer_total'];
                $final_total_entered[] = $ticket['manual_time'];
                $final_total_time[] = $ticket['timer_total'];
                $final_total_time[] = $ticket['manual_time'];
                
                $staff_total_timer = [];
                $staff_total_spent = [];
                $staff_total_all = [];
			}

			//Tasks
            $tasks = mysqli_query($dbc, "SELECT tasklist.*, IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`tasklist_time`.`src`='M',`tasklist_time`.`work_time`,'00:00:00')))),`tasklist`.`work_time`) `manual_time`, IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`tasklist_time`.`src`='A',`tasklist_time`.`work_time`,'00:00:00')))),'00:00:00') `timer_total`, IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(`tasklist_time`.`work_time`))),`tasklist`.`work_time`) `total_time` FROM tasklist LEFT JOIN `tasklist_time` ON `tasklist`.`tasklistid`=`tasklist_time`.`tasklistid` WHERE IFNULL(`tasklist_time`.`contactid`,`tasklist`.`contactid`) = '$cid' AND IFNULL(`tasklist_time`.`timer_date`,`tasklist`.`task_tododate`) = '".$date."' AND `tasklist`.`tasklistid` > 0 GROUP BY `tasklist`.`tasklistid`");
			while($task = mysqli_fetch_array($tasks)) {
                $report_data .= '<tr nobr="true">';
                    $report_data .= (count($staff) == 1 && $staff[0] > 0) ? '<td>'.$date.'</td>' : '<td>'.get_staff($dbc,$row['contactid']).'</td>';
                    $report_data .= '<td>Tasks</td>';
                    $report_data .= '<td>'. $task['heading'] .'</td>';
                    $report_data .= '<td>'. $task['timer_total'] .'</td>';
                    $report_data .= '<td>'. $task['manual_time'] .'</td>';
                    $report_data .= '<td>'. $task['total_time'] .'</td>';
                $report_data .= '</tr>';

                $final_total_timer[] = $task['timer_total'];
                $final_total_entered[] = $task['manual_time'];
                $final_total_time[] = $task['total_time'];
			}

			//Checklist
			$checklists = mysqli_query($dbc, "SELECT c.*, n.checklist FROM checklist_name_time c LEFT JOIN checklist_name n ON (c.checklist_id = n.checklistnameid) WHERE c.contactid = '$cid' AND c.timer_date = '".$date."'");
			while($checklist = mysqli_fetch_array($checklists)) {
                $report_data .= '<tr nobr="true">';
                    $report_data .= (count($staff) == 1 && $staff[0] > 0) ? '<td>'.$date.'</td>' : '<td>'.get_staff($dbc,$row['contactid']).'</td>';
                    $report_data .= '<td>Checklists</td>';
                    $report_data .= '<td>'. (!empty(get_checklist($dbc, $checklistid, 'checklist_name')) ? get_checklist($dbc, $checklistid, 'checklist_name').': ' : '') . $checklist['checklist'] .'</td>';
                    $report_data .= '<td>00:00:00</td>';
                    $report_data .= '<td>'. $checklist['work_time'] .'</td>';
                    $report_data .= '<td>'. $checklist['work_time'] .'</td>';
                $report_data .= '</tr>';

                $final_total_timer[] = '00:00:00';
                $final_total_entered[] = $checklist['work_time'];
                $final_total_time[] = $checklist['work_time'];
			}
		}

        if(count($staff) == 1 && $staff[0] > 0) {
        } else {
            // All staff
            $report_data .= '<tr><td><b>Total</b></td><td colspan="2"><td><b>'.AddPlayTime($final_total_timer).'</b></td><td><b>'.AddPlayTime($final_total_entered).'</b></td><td><b>'.AddPlayTime($final_total_time).'</b></td></tr>';
        }
    }
    
    if(count($staff) == 1 && $staff[0] > 0) {
        // Search by staff
        $report_data .= '<tr><td><b>Total</b></td><td colspan="2"><td><b>'.AddPlayTime($final_total_timer).'</b></td><td><b>'.AddPlayTime($final_total_entered).'</b></td><td><b>'.AddPlayTime($final_total_time).'</b></td></tr>';
    }

    $report_data .= "</table>";

    return $report_data;
}

function AddPlayTime2($times) {
    $minutes = 0;
    foreach ($times as $time) {
        $minutes += $time;
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;

    //return $hours.':'.$minutes;
    return sprintf('%02d:%02d', $hours, $minutes);
}

function AddPlayTime($times) {
    // loop throught all the times
	$minutes = 0;
    $seconds = 0;
    foreach ($times as $time) {
        list($hour, $minute, $second) = explode(':', $time);
        $minutes += explode(':',$time)[0] * 60;
        $minutes += explode(':',$time)[1];
        $seconds += (intval($second));
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;
    if ($seconds > 0) {
        $minutes += floor($seconds / 60);
        $seconds = $seconds % 60;
    }
    if ( $minutes > 0) {
        $hours += floor($minutes / 60);
        $minutes = $minutes % 60;
    }

    // returns the time already formatted
    //return $hours.':'.sprintf('%02d', $minutes);
    return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
}
?>