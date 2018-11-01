<?php
/*
 * Day Sheet Report
 * Any updates here should be updated on /Profile/daysheet_timesheets.php
 */
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
$report_fields = explode(',', get_config($dbc, 'report_operation_fields'));

if (isset($_POST['printpdf'])) {

    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];
    $staffidpdf = $_POST['staffidpdf'];
    $search_ticket = $_POST['ticketidpdf'];
    $extraticketpdf = $_POST['extraticketpdf'];
    $search_expenses = $_POST['ticket_expensespdf'];
    $search_notes = $_POST['ticket_notespdf'];
    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));

	class MYPDF extends TCPDF {

		public function Header() {
			//$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
            if(REPORT_LOGO != '') {
                $image_file = 'download/'.REPORT_LOGO;
                $this->Image($image_file, 'C', 10, '', '', '', false, 'C', false, 300, 'C', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:ceter;">'.REPORT_HEADER.'</p>';
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

    if (strpos($staffidpdf, ',') !== false) {
        $format = 'multi_staff';
    } elseif ( (strpos($staffidpdf, ',') === false) && $staffidpdf != '') {
        $format = 'single_staff';
    } else {
        $format = 'date_only';
    }

    if( $format == 'single_staff' ) {
        $pdf->AddPage('L', 'LETTER');
        $pdf->SetFont('helvetica', '', 9);
        $html = '';
        $html .= '<br><br>' . report_receivables($dbc, $start_date, $end_date, $staffidpdf, $format, $search_ticket, $extraticketpdf, $search_expenses, $search_notes, 'padding:3px; border:1px solid black;', 'background-color:lightgrey; color:black;', 'background-color:lightgrey; color:black;');

        $pdf->writeHTML($html, true, false, true, false, '');

    } elseif ( $format == 'multi_staff' || $format == 'date_only' ) {
        for($date = $start_date; $date <= $end_date; $date = date('Y-m-d', strtotime($date. ' + 1 days')))
        {
            $pdf->AddPage('L', 'LETTER');
			$pdf->SetFont('helvetica', '', 9);
			      $html = '<br><br>' . report_receivables($dbc, $date, $date, $staffidpdf, $format, $search_ticket, $extraticketpdf, $search_expenses, $search_notes, 'padding:3px; border:1px solid black;', 'background-color:lightgrey; color:black;', 'background-color:lightgrey; color:black;');
            $pdf->writeHTML($html, true, false, true, false, '');
        }
    }

    $pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);
    $html = '<br /><br />'. report_summary($dbc, $start_date, $end_date, $staffidpdf, $format, 'padding:3px; border:1px solid black;', 'background-color:lightgrey; color:black;', 'background-color:lightgrey; color:black;');
    $pdf->writeHTML($html, true, false, true, false, '');

    $today_date = date('Y-m-d');
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
    $extraticket = $extraticketpdf;
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
                $search_ticket = $_POST['search_ticket'];
                $extraticket = $_POST['search_extra_ticket'];
                $search_expenses = $_POST['search_expenses'];
                $search_notes = $_POST['search_notes'];
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
				<div class="col-sm-5">
					<label for="search_ticket" class="col-sm-4 control-label">Search By <?= TICKET_NOUN ?>:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a <?= TICKET_NOUN ?> #" name="search_ticket" class="chosen-select-deselect form-control">
							<option value=""></option>
							<?php
							$query = mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted`=0 ORDER BY `ticketid`");
							while($row = mysqli_fetch_array($query)) { ?>
								<option <?php if ($row['ticketid'] == $search_ticket) { echo " selected"; } ?> value='<?= $row['ticketid'] ?>' ><?= get_ticket_label($dbc, $row) ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
				<?php if(in_array('filter_extra_billing',$report_fields)) { ?>
					<div class="col-sm-5">
						<label for="search_extra_ticket" class="col-sm-4 control-label">Search By Extra Billing <?= TICKET_NOUN ?>:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select a <?= TICKET_NOUN ?> #" name="search_extra_ticket" class="chosen-select-deselect form-control">
								<option value=""></option>
								<?php
								$query = mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted`=0 AND `ticketid` IN (SELECT `ticketid` FROM `ticket_comment` WHERE `type`='service_extra_billing' AND `deleted`=0) ORDER BY `ticketid`");
								while($row = mysqli_fetch_array($query)) { ?>
									<option <?php if ($row['ticketid'] == $extraticket) { echo " selected"; } ?> value='<?= $row['ticketid'] ?>' ><?= get_ticket_label($dbc, $row) ?></option>
								<?php } ?>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if(in_array('filter_staff_expenses',$report_fields)) { ?>
					<div class="col-sm-5">
						<label for="search_expenses" class="col-sm-4 control-label">Only <?= TICKET_TILE ?> with Expenses:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select Option" name="search_material" class="chosen-select-deselect form-control">
								<option <?= $search_expenses == 'No' ? 'selected' : '' ?> value="No">Display All</option>
								<option <?= $search_expenses == 'Yes' ? 'selected' : '' ?> value="Yes">Only with Expenses</option>
							</select>
						</div>
					</div>
				<?php } ?>
				<?php if(in_array('filter_ticket_notes',$report_fields)) { ?>
					<div class="col-sm-5">
						<label for="search_notes" class="col-sm-4 control-label">Only <?= TICKET_TILE ?> with Notes:</label>
						<div class="col-sm-8">
							<select data-placeholder="Select Option" name="search_material" class="chosen-select-deselect form-control">
								<option <?= $search_notes == 'No' ? 'selected' : '' ?> value="No">Display All</option>
								<option <?= $search_notes == 'Yes' ? 'selected' : '' ?> value="Yes">Only with Notes</option>
							</select>
						</div>
					</div>
				<?php } ?>
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
            <input type="hidden" name="ticketidpdf" value="<?php echo $search_ticket; ?>">
            <input type="hidden" name="extraticketpdf" value="<?php echo $extraticket; ?>">
            <input type="hidden" name="ticket_expensespdf" value="<?php echo $search_expenses; ?>">
            <input type="hidden" name="ticket_notespdf" value="<?php echo $search_notes; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                $start_date = date('Y-m-d', strtotime($starttime));
                $end_date = date('Y-m-d', strtotime($endtime));

                if (strpos($staffid, ',') !== false) {
                    $format = 'multi_staff';
                } elseif ( (strpos($staffid, ',') === false) && $staffid != '') {
                    $format = 'single_staff';
                } else {
                    $format = 'date_only';
                }

                if( $format == 'single_staff' ) {
                    echo report_receivables($dbc, $start_date, $end_date, $staffid, $format, '', '', '');
                    echo "<br>";
				} elseif ( $format == 'multi_staff' || $format == 'date_only' ) {
                    for($date = $start_date; $date <= $end_date; $date = date('Y-m-d', strtotime($date. ' + 1 days')))
					{
						echo report_receivables($dbc, $date, $date, $staffid, $format, $search_ticket, $extraticket, $search_expenses, $search_notes, '', '', '');
						echo "<br>";
					}
				}

				echo report_summary($dbc, $start_date, $end_date, $staffid, $format, '', '', '');
            ?>

        </form>

<?php
function report_summary($dbc, $starttime, $endtime, $staff, $format, $table_style, $table_row_style, $grand_total_style) {
    if ($format == 'multi_staff') {
        $staff = explode(',', $staff);
        $staff_list = "'" . implode("','", $staff) . "'";
        $query_mod_tickets = "`timers`.`created_by` IN ($staff_list) AND";
        $query_mod_tasks = "IFNULL(`tasklist_time`.`contactid`,`tasklist`.`contactid`) IN ($staff_list) AND";
        $query_mod_checklists = "`checklist_name_time`.`contactid` IN ($staff_list) AND";
    } elseif ($format == 'single_staff') {
        $query_mod_tickets = "`timers`.`created_by` LIKE '$staff' AND";
        $query_mod_tasks = "IFNULL(`tasklist_time`.`contactid`,`tasklist`.`contactid`) = '$staff' AND";
        $query_mod_checklists = "`checklist_name_time`.`contactid` = '$staff' AND";
    } else {
        $query_mod_tickets = "";
        $query_mod_tasks = "";
        $query_mod_checklists = "";
    }

    $total_summary = array();
    $report_data_summary = '<h3>Summary</h3><table border="1px" class="table table-bordered" style="'.$table_style.'" width="100%"><tr style="'.$table_row_style.'"><th width="74%">Business</th><th width="26%">Total Time</th></tr>';

    $summary = sort_contacts_query(mysqli_query($dbc, "SELECT `businessid` `contactid`, IFNULL(NULLIF(`name`,''),'".encryptIt('ZZZZZZZZZZZZ')."') `name`, SEC_TO_TIME(SUM(TIME_TO_SEC(`time_spent`))) `time_spent` FROM (SELECT `tickets`.`businessid`, `contacts`.`name`, SEC_TO_TIME(SUM(TIME_TO_SEC(`timers`.`time`))) `time_spent` FROM `tickets` LEFT JOIN (SELECT `ticketid`,`item_id`,`date_stamp`,SEC_TO_TIME(`hours_tracked` * 3600)` `time`, 'Staff Time' `type` FROM `ticket_attached` WHERE `src_table` IN ('Staff_Tasks','Staff') AND `ticket_attached`.`deleted`=0 UNION SELECT `ticketid`,`created_by`,`created_date`,`time_length` `time`, 'Manual' `type` FROM `ticket_time_list` WHERE `time_type`='Manual Time' AND `ticket_time_list`.`deleted`=0 UNION SELECT `ticketid`,`created_by`,`created_date`,`timer` `time`, 'Tracked' `type` FROM `ticket_timer` WHERE `ticket_timer`.`deleted` = 0) `timers` ON `tickets`.`ticketid`=`timers`.`ticketid` LEFT JOIN `contacts` ON (`contacts`.`contactid`=`tickets`.`businessid`) WHERE $query_mod_tickets (`timers`.`created_date` BETWEEN '$starttime 00:00:00' AND '$endtime 23:59:59') GROUP BY  `tickets`.`businessid`
    UNION ALL
    SELECT `tasklist`.`businessid`, `contacts`.`name`, IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(`tasklist_time`.`work_time`))),`tasklist`.`work_time`) `time_spent` FROM `tasklist` LEFT JOIN `tasklist_time` ON `tasklist`.`tasklistid`=`tasklist_time`.`tasklistid` LEFT JOIN `contacts` ON (`tasklist`.`businessid` IS NOT NULL AND `tasklist`.`businessid`=`contacts`.`contactid`) WHERE $query_mod_tasks ( IFNULL(`tasklist_time`.`timer_date`,`tasklist`.`task_tododate`) BETWEEN '$starttime' AND '$endtime') AND `tasklist`.`tasklistid` > 0 GROUP BY `tasklist`.`businessid`
    UNION ALL
    SELECT `checklist`.`businessid`, `contacts`.`name`, `checklist_name_time`.`work_time` `time_spent` FROM `checklist_name_time` LEFT JOIN `checklist_name` ON (`checklist_name`.`checklistnameid`=`checklist_name_time`.`checklist_id`) LEFT JOIN `checklist` ON (`checklist`.`checklistid`=`checklist_name`.`checklistid`) LEFT JOIN `contacts` ON (`contacts`.`contactid`=`checklist`.`businessid`) WHERE $query_mod_checklists (`timer_date` BETWEEN '$starttime' AND '$endtime') GROUP BY `checklist`.`businessid`) `time` GROUP BY IFNULL(`businessid`,0)"));

    foreach($summary as $row_summary) {
        $total_summary[] = $row_summary['time_spent'];
        $report_data_summary .= '<tr><td>'. ( $row_summary['name'] === 'ZZZZZZZZZZZZ' ? 'Uncategorized' : $row_summary['name'] ) .'</td><td>'. $row_summary['time_spent'] .'</td></tr>';
    }

    $report_data_summary .= '<tr><td><b>Total</b></td><td><b>'. AddPlayTime($total_summary) .'</b></td></tr>';
    $report_data_summary .= '</table><br />';

    return $report_data_summary;
}

function report_receivables($dbc, $starttime, $endtime, $staff, $format, $ticketid, $extraticket, $expenses, $notes, $table_style, $table_row_style, $grand_total_style) {
	$report_fields = explode(',', get_config($dbc, 'report_operation_fields'));
	if(!in_array('daysheet_tickets',$report_fields) && !in_array('daysheet_tasks',$report_fields) && !in_array('daysheet_checklists',$report_fields)) {
		$report_fields = ['daysheet_tickets','daysheet_tasks','daysheet_checklists'];
	}
      $report_data = '';
    $date_staff = '';

    if ($format == 'multi_staff') {
        $query = array_filter(array_unique(explode(',',$staff)));
        $report_data .= '<h3>'.$starttime.'</h3>';
        $date_staff = 'staff';
    } elseif ($format == 'single_staff') {
        $query = array($staff);
        $report_data .= '<h3>'.get_contact($dbc, $staff).'</h3>';
        $date_staff = 'date';
    } else {
        $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status=1 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""),MYSQLI_ASSOC));
        $report_data .= '<h3>'.$starttime.'</h3>';
        $date_staff = 'staff';
    }

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'" width="100%">';
    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="15%">'.($date_staff=='date' ? 'Date' : 'Staff').'</th>
    <th width="15%">Tile</th>
    <th width="44%">Description</th>
    <th width="7%">Total Timer Time</th>
    <th width="7%">Total Entered Time</th>
    <th width="7%">Total Time</th>
    <th width="5%">Sign Off</th>
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

			$total_tracked_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time`))) `time` FROM (SELECT SEC_TO_TIME(`hours_tracked` * 3600) `time` FROM `ticket_attached` WHERE `src_table` IN ('Staff_Tasks','Staff') AND `item_id`='$cid' AND `date_stamp` LIKE '$date%' AND `deleted`=0 UNION SELECT `time_length` `time` FROM `ticket_time_list` WHERE `created_by`='$cid' AND `created_date` LIKE '$date%' AND `ticket_time_list`.`deleted`=0 AND `time_type`='Manual Time' UNION SELECT `timer` `time` FROM `ticket_timer` WHERE `created_by`='$cid' AND `created_date` LIKE '$date%' AND `ticket_timer`.`deleted`=0) `time_list`")->fetch_assoc()['time'];

			if(in_array('daysheet_tickets',$report_fields)) {
            //Tickets
            $tickets = mysqli_query($dbc, "SELECT `tickets`.*, SEC_TO_TIME(SUM(TIME_TO_SEC(`timers`.`time`))) `time_spent`, SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`timers`.`type`='Tracked',`timers`.`time`,0)))) `timer_total`, SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`timers`.`type`='Manual',`timers`.`time`,0)))) `manual_time` FROM `tickets` LEFT JOIN (SELECT `ticketid`,`created_by`,`created_date`,`time_length` `time`, 'Manual' `type` FROM `ticket_time_list` WHERE `time_type`='Manual Time' AND `ticket_time_list`.`deleted`=0 UNION SELECT `ticketid`,`created_by`,`created_date`,`timer` `time`, 'Tracked' `type` FROM `ticket_timer` WHERE `ticket_timer`.`deleted`=0) `timers` ON `tickets`.`ticketid`=`timers`.`ticketid` WHERE `timers`.`created_date` LIKE '$date%' AND `timers`.`created_by` LIKE '$cid' AND '$ticketid' IN (`tickets`.`ticketid`,'') AND '$extraticket' IN (`tickets`.`ticketid`,'') ".($expenses == 'Yes' ? " AND `tickets`.`ticketid` IN (SELECT `work_order` FROM `expenses` WHERE `work_order`.`deleted`=0) " : '').($notes == 'Yes' ? " AND `tickets`.`ticketid` iN (SELECT `ticketid` FROM `ticket_comment` WHERE `ticket_comment`.`deleted`=0 AND `type` NOT IN ('day','service_extra_billing')) " : '')." GROUP BY `tickets`.`ticketid`");

        while($ticket = $tickets->fetch_assoc()) {
                  $total_all[] = $ticket['timer_total'];
                  $total_all[] = $ticket['manual_time'];

                  $report_data .= '<tr nobr="true">';
                      $report_data .= '<td>'. ( $date_staff=='date' ? $date : get_staff($dbc,$row['contactid']) ) .'</td>';
                      $report_data .= '<td>'. TICKET_TILE .'</td>';
                      $report_data .= '<td>'. get_ticket_label($dbc, $ticket) .'</td>';
                      $report_data .= '<td>'. $ticket['timer_total'] .'</td>';
                      $report_data .= '<td>'. $ticket['manual_time'] .'</td>';
                      $report_data .= '<td>'. AddPlayTime($total_all) .'</td>';
                      $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(daysheetreportid) AS daysheetreportid FROM daysheet_report WHERE contactid='$cid' AND today_date='$date'"));
                      $report_data .= '<td>'. ( ($get_config['daysheetreportid'] >= 1) ? '<img src="../img/checkmark.png" width="11" height="11" border="0" alt="">' : '<input type="checkbox" onclick="handleClick(this);" name="contactid" value="1">' ) .'</td>';
                  $report_data .= '</tr>';

                  $final_total_timer[] = $ticket['timer_total'];
                  $final_total_entered[] = $ticket['manual_time'];
                  $final_total_time[] = $ticket['timer_total'];
                  $final_total_time[] = $ticket['manual_time'];

                  $staff_total_timer = [];
                  $staff_total_spent = [];
                  $staff_total_all = [];
        }
      }

			if(in_array('daysheet_tasks',$report_fields)) {
        //Tasks
              $tasks = mysqli_query($dbc, "SELECT tasklist.*, IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`tasklist_time`.`src`='M',`tasklist_time`.`work_time`,'00:00:00')))),`tasklist`.`work_time`) `manual_time`, IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(IF(`tasklist_time`.`src`='A',`tasklist_time`.`work_time`,'00:00:00')))),'00:00:00') `timer_total`, IFNULL(SEC_TO_TIME(SUM(TIME_TO_SEC(`tasklist_time`.`work_time`))),`tasklist`.`work_time`) `total_time` FROM tasklist LEFT JOIN `tasklist_time` ON `tasklist`.`tasklistid`=`tasklist_time`.`tasklistid` WHERE IFNULL(`tasklist_time`.`contactid`,`tasklist`.`contactid`) = '$cid' AND IFNULL(`tasklist_time`.`timer_date`,`tasklist`.`task_tododate`) = '".$date."' AND `tasklist`.`tasklistid` > 0 GROUP BY `tasklist`.`tasklistid`");
        while($task = mysqli_fetch_array($tasks)) {
                  $report_data .= '<tr nobr="true">';
                      $report_data .= '<td>'. ( $date_staff=='date' ? $date : get_staff($dbc,$row['contactid']) ) .'</td>';
                      $report_data .= '<td>'.TASK_TILE.'</td>';
                      $report_data .= '<td>'. $task['heading'] .'</td>';
                      $report_data .= '<td>'. $task['timer_total'] .'</td>';
                      $report_data .= '<td>'. $task['manual_time'] .'</td>';
                      $report_data .= '<td>'. $task['total_time'] .'</td>';
                      $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(daysheetreportid) AS daysheetreportid FROM daysheet_report WHERE contactid='$cid' AND today_date='$date'"));
                      $report_data .= '<td>'. ( ($get_config['daysheetreportid'] >= 1) ? '<img src="../img/checkmark.png" width="11" height="11" border="0" alt="">' : '<input type="checkbox" onclick="handleClick(this);" name="contactid" value="1">' ) .'</td>';
                  $report_data .= '</tr>';

                  $final_total_timer[] = $task['timer_total'];
                  $final_total_entered[] = $task['manual_time'];
                  $final_total_time[] = $task['total_time'];
        }
      }

			if(in_array('daysheet_checklists',$report_fields)) {
        //Checklist
        $checklists = mysqli_query($dbc, "SELECT c.*, n.checklist FROM checklist_name_time c LEFT JOIN checklist_name n ON (c.checklist_id = n.checklistnameid) WHERE c.contactid = '$cid' AND c.timer_date = '".$date."'");
        while($checklist = mysqli_fetch_array($checklists)) {
                  $report_data .= '<tr nobr="true">';
                      $report_data .= '<td>'. ( $date_staff=='date' ? $date : get_staff($dbc,$row['contactid']) ).'</td>';
                      $report_data .= '<td>Checklists</td>';
                      $report_data .= '<td>'. (!empty(get_checklist($dbc, $checklistid, 'checklist_name')) ? get_checklist($dbc, $checklistid, 'checklist_name').': ' : '') . $checklist['checklist'] .'</td>';
                      $report_data .= '<td>00:00:00</td>';
                      $report_data .= '<td>'. $checklist['work_time'] .'</td>';
                      $report_data .= '<td>'. $checklist['work_time'] .'</td>';
                      $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(daysheetreportid) AS daysheetreportid FROM daysheet_report WHERE contactid='$cid' AND today_date='$date'"));
                      $report_data .= '<td>'. ( ($get_config['daysheetreportid'] >= 1) ? '<img src="../img/checkmark.png" width="11" height="11" border="0" alt="">' : '<input type="checkbox" onclick="handleClick(this);" name="contactid" value="1">' ) .'</td>';
                  $report_data .= '</tr>';

                  $final_total_timer[] = '00:00:00';
                  $final_total_entered[] = $checklist['work_time'];
                  $final_total_time[] = $checklist['work_time'];
        }
      }
		}

        if($date_staff=='date') {
        } else {
            // All staff
            $report_data .= '<tr><td colspan="3"><b>Total</b></td><td><b>'.AddPlayTime($final_total_timer).'</b></td><td><b>'.AddPlayTime($final_total_entered).'</b></td><td><b>'.AddPlayTime($final_total_time).'</b></td><td></td></tr>';
        }
    }

    if($date_staff=='date') {
        // Search by staff
        $report_data .= '<tr><td colspan="3"><b>Total</b></td><td><b>'.AddPlayTime($final_total_timer).'</b></td><td><b>'.AddPlayTime($final_total_entered).'</b></td><td><b>'.AddPlayTime($final_total_time).'</b></td><td></td></tr>';

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