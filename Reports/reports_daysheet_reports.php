<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
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
                $image_file = 'download/'.REPORT_LOGO;
                $this->Image($image_file, 10, 10, '', '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'Daysheet Report';
            $this->writeHTMLCell(0, 0, 0 , 40, $footer_text, 0, 0, false, "R", true);
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

	//$pdf->AddPage('L', 'LETTER');
    //$pdf->SetFont('helvetica', '', 9);

    //$html .= report_receivables($dbc, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $start_date = date('Y-m-d', strtotime($starttimepdf));
    $end_date = date('Y-m-d', strtotime($endtimepdf));
    $html = '';

	if(count(array_filter(explode(',',$staffidpdf))) > 0) {
        $pdf->AddPage('L', 'LETTER');
        $pdf->SetFont('helvetica', '', 9);
        $html = '';

        $html .= '<br><br>' . report_receivables($dbc, $start_date, $end_date, $staffidpdf, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

        $pdf->writeHTML($html, true, false, true, false, '');
	} else {
		for($date = $start_date; $date <= $end_date; $date = date('Y-m-d', strtotime($date. ' + 1 days')))
		{
			$pdf->AddPage('L', 'LETTER');
			$pdf->SetFont('helvetica', '', 9);
			$html = '';

			$html .= '<br><br>' . report_receivables($dbc, $date, $date, $staffidpdf, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

			$pdf->writeHTML($html, true, false, true, false, '');
		}
	}

    $today_date = date('Y-m-d');
	//$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/daysheet_report_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'reports_daysheet_reports', 0, WEBSITE_URL.'/Reports/Download/daysheet_report_'.$today_date.'.pdf', 'Daysheet Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/daysheet_report_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
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
</head>
<body>
<?php include_once ('../navigation.php');
?>

<div class="container triple-pad-bottom">
    <div class="row">
        <div class="col-md-12">

        <?php echo reports_tiles($dbc);  ?>

        <br><br>

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
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Staff:</label>
					<div class="col-sm-8">
						<?php $query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status=1 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY."")); ?>
						<select name="staffid[]" multiple data-placeholder="Select Staff" class="chosen-select-deselect"><option />
							<?php foreach($query as $staff) { ?>
								<option <?= in_array($staff['contactid'],explode(',',$staffid)) ? 'selected' : '' ?> value="<?= $staff['contactid'] ?>"><?= $staff['first_name'].' '.$staff['last_name'] ?></option>
							<?php } ?>
						</select>
					</div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="staffidpdf" value="<?php echo $staffid; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                $start_date = date('Y-m-d', strtotime($starttime));
                $end_date = date('Y-m-d', strtotime($endtime));

				if(count(array_filter(explode(',',$staffid))) > 0) {
					echo report_receivables($dbc, $start_date, $end_date, $staffid, '', '', '');
				} else {
					for($date = $start_date; $date <= $end_date; $date = date('Y-m-d', strtotime($date. ' + 1 days')))
					{
						echo report_receivables($dbc, $date, $date, $staffid, '', '', '');
						echo "<br>";
					}
				}

            ?>

        </form>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>

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
    <th width="10%">'.(count($staff) == 1 && $staff[0] > 0 ? 'Date' : 'Staff').'</th>
    <th width="30%">'.TICKET_TILE.'</th>
    <th width="24%">Tasks</th>
    <th width="24%">Checklists</th>
    <th width="3%">Total Timer Time</th>
    <th width="3%">Total Entered Time</th>
    <th width="3%">Total Time</th>
    <th width="3%">Sign Off</th>
    </tr>';

	for($date = $starttime; $date <= $endtime; $date = date('Y-m-d', strtotime($date. ' + 1 days')))
	{
        $final_total_timer = [];
        $final_total_entered = [];
        $final_total_time = [];
		foreach($query as $contactid) {
			$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$contactid'"));
			$contactid = ','.$row['contactid'].',';
			$cid = $row['contactid'];

			$report_data .= '<tr nobr="true">';
			if(count($staff) == 1 && $staff[0] > 0) {
				$report_data .= '<td>'.$date.'</td>';
			} else {
				$report_data .= '<td>'.get_staff($dbc,$row['contactid']).'</td>';
			}

			$ticket_list = [];
			$task_list = '';
			$checklist_list = '';
			$total_ticket_spent_time = [];
			$total_timer = [];
			$total_spent = [];
			$total_all = [];

			$total_tracked_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time`))) `time` FROM (SELECT `time_length` `time` FROM `ticket_time_list` WHERE `created_by`='$cid' AND `created_date` LIKE '$date%' AND `deleted`=0 AND `time_type`='Manual Time' UNION SELECT `timer` `time` FROM `ticket_timer` WHERE `created_by`='$cid' AND `created_date` LIKE '$date%') `time_list`")->fetch_assoc()['time'];

			//$tickets = mysqli_query($dbc, "SELECT `tickets`.*, SEC_TO_TIME(SUM(TIME_TO_SEC(`ticket_timer`.`timer`)) + SUM(TIME_TO_SEC(`ticket_time_list`.`time_length`))) `time_spent`, SEC_TO_TIME(SUM(TIME_TO_SEC(`ticket_timer`.`timer`))) `timer_total`, SEC_TO_TIME(SUM(TIME_TO_SEC(`ticket_time_list`.`time_length`))) `manual_time` FROM `tickets` LEFT JOIN `ticket_time_list` ON `ticket_time_list`.`ticketid`=`tickets`.`ticketid` AND `ticket_time_list`.`deleted`=0 AND `ticket_time_list`.`time_type`='Manual Time' LEFT JOIN `ticket_timer` ON `tickets`.`ticketid`=`ticket_timer`.`ticketid`  WHERE `ticket_timer`.`created_date` LIKE '$date%' AND `ticket_timer`.`created_by`='$cid' GROUP BY `tickets`.`ticketid`");

            $tickets = mysqli_query($dbc, "SELECT `tickets`.*, SEC_TO_TIME(SUM(TIME_TO_SEC(`timers`.`time`))) `time_spent`, SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`timers`.`type`='Tracked',`timers`.`time`,0)))) `timer_total`, SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`timers`.`type`='Manual',`timers`.`time`,0)))) `manual_time` FROM `tickets` LEFT JOIN (SELECT `ticketid`,`created_by`,`created_date`,`time_length` `time`, 'Manual' `type` FROM `ticket_time_list` WHERE `time_type`='Manual Time' AND `deleted`=0 UNION SELECT `ticketid`,`created_by`,`created_date`,`timer` `time`, 'Tracked' `type` FROM `ticket_timer`) `timers` ON `tickets`.`ticketid`=`timers`.`ticketid` WHERE `timers`.`created_date` LIKE '$date%' AND `timers`.`created_by` LIKE '$cid' GROUP BY `tickets`.`ticketid`");

			while($ticket = $tickets->fetch_assoc()) {
				//$ticket_list[] = '<p>'.get_ticket_label($dbc, $ticket).' - '.substr($ticket['time_spent'],0,-3).'</p>';
                $ticket_list[] = '<p>'.get_ticket_label($dbc, $ticket).'</p>';
				$total_ticket_spent_time[] = $ticket['time_spent'];
				$total_timer[] = $ticket['timer_total'];
				$total_spent[] = $ticket['manual_time'];

                $final_total_timer[] = $ticket['timer_total'];
                $final_total_entered[] = $ticket['manual_time'];

                $total_all[] = $ticket['timer_total'];
                $total_all[] = $ticket['manual_time'];

                $final_total_time[] = $ticket['timer_total'];
                $final_total_time[] = $ticket['manual_time'];

			}

			$tasks = mysqli_query($dbc, "SELECT tasklist.*, IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`tasklist_time`.`src`='M',`tasklist_time`.`work_time`,'00:00:00')))),`tasklist`.`work_time`) `manual_time`, IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`tasklist_time`.`src`='A',`tasklist_time`.`work_time`,'00:00:00')))),'00:00:00') `timer_total`, IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(`tasklist_time`.`work_time`))),`tasklist`.`work_time`) `total_time` FROM tasklist LEFT JOIN `tasklist_time` ON `tasklist`.`tasklistid`=`tasklist_time`.`tasklistid` WHERE IFNULL(`tasklist_time`.`contactid`,`tasklist`.`contactid`) = '$cid' AND IFNULL(`tasklist_time`.`timer_date`,`tasklist`.`task_tododate`) = '".$date."' AND `tasklist`.`tasklistid` > 0 GROUP BY `tasklist`.`tasklistid`");
			while($task = mysqli_fetch_array($tasks)) {
				$tasklistid = $task['tasklistid'];
				$task_list .= "<p><a target= '_blank' href='../Tasks/add_task.php?tasklistid=".$tasklistid."'>".$task['heading'].'</a> - '.substr($task['total_time'],0,-3).'</p>';
				$total_timer[] = $task['timer_total'];
				$total_spent[] = $task['manual_time'];
				$total_all[] = $task['total_time'];

                $final_total_timer[] = $task['timer_total'];
                $final_total_entered[] = $task['manual_time'];
                $final_total_time[] = $task['total_time'];
			}

			//Checklist
			$checklists = mysqli_query($dbc, "SELECT * FROM checklist_name_time WHERE contactid = '$cid' AND timer_date = '".$date."'");
			while($checklist = mysqli_fetch_array($checklists)) {
				$checklistnameid = $checklist['checklist_id'];
				$checklistid = get_checklist_name($dbc, $checklistnameid, 'checklistid');
				$checklist_name = get_checklist($dbc, $checklistid, 'checklist_name');
				$checklist_list .= "<p><a target= '_blank' href='../Checklist/checklist.php?view=".$checklistid."'>".$checklist_name.'</a> - '.substr($checklist['work_time'],0,-3).'</p>';
				$total_timer[] = '00:00:00';
				$total_spent[] = $checklist['work_time'];
				$total_all[] = $checklist['work_time'];

                $final_total_timer[] = '00:00:00';
                $final_total_entered[] = $checklist['work_time'];
                $final_total_time[] = $checklist['work_time'];
			}


			$report_data .= '<td><p>'.implode('</p><p>',$ticket_list).'</p></td>';
			$report_data .= '<td>'.$task_list.'</td>';
			$report_data .= '<td>'.$checklist_list.'</td>';
			$report_data .= '<td>'.AddPlayTime($total_timer).'</td>';
			$report_data .= '<td>'.AddPlayTime($total_spent).'</td>';
			$report_data .= '<td>'.AddPlayTime($total_all).'</td>';

			$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(daysheetreportid) AS daysheetreportid FROM daysheet_report WHERE contactid='$cid' AND today_date='$date'"));

			if($get_config['daysheetreportid'] >= 1) {
				$report_data .= '<td><img src="../img/checkmark.png" width="11" height="11" border="0" alt=""></td>';
			} else {
				$report_data .= '<td><input type="checkbox" onclick="handleClick(this);" name="contactid" value="1"></td>';
			}

            /*
			$report_data .= '<td>';
			$pdf_url = 'Day_'.$starttime.'-'.trim($contactid,',').'.pdf';
			if(file_exists($_SERVER["DOCUMENT_ROOT"].'/Profile/download/'.$pdf_url)) {
				$report_data .= '<a target="_blank" href="'.WEBSITE_URL.'/Profile/download/'.$pdf_url.'"><img src="'.WEBSITE_URL.'/img/pdf.png"></a>';
			} else if(file_exists($_SERVER["DOCUMENT_ROOT"].'/Daysheet/download/'.$pdf_url)) {
				$report_data .= '<a target="_blank" href="'.WEBSITE_URL.'/Daysheet/download/'.$pdf_url.'"><img src="'.WEBSITE_URL.'/img/pdf.png"></a>';
			}
			$report_data .= '</td>';
            */

			$report_data .= "</tr>";
		}

        $report_data .= '<tr><td><b>Total</b></td><td></td><td></td><td></td><td><b>'.AddPlayTime($final_total_timer).'</b></td><td><b>'.AddPlayTime($final_total_entered).'</b></td><td><b>'.AddPlayTime($final_total_time).'</b></td><td></td></tr>';
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
    foreach ($times as $time) {
        list($hour, $minute) = explode(':', $time);
        $minutes += explode(':',$time)[0] * 60;
        $minutes += explode(':',$time)[1];
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;

    // returns the time already formatted
    return $hours.':'.sprintf('%02d', $minutes);
}
?>