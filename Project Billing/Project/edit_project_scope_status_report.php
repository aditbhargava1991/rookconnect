<?php include_once('../include.php');
if(!isset($project)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
    $projecttype = get_field_value('projecttype','project','projectid',$projectid);
}
if($_GET['reset'] == 'reset') {
    $_GET['starttime'] = $_GET['endtime'] = $_GET['action_items'] = $_GET['staff'] = '';
}
$start = '0000-00-00';
if(!empty($_GET['starttime'])) {
    $start = date('Y-m-d',strtotime($_GET['starttime']));
}
$end = '9999-12-31';
if(!empty($_GET['endtime'])) {
    $end = date('Y-m-d',strtotime($_GET['endtime']));
}
$actions = 'all';
if(!empty($_GET['action_items'])) {
    $actions = filter_var($_GET['action_items'],FILTER_SANITIZE_STRING);
}
$staff = 'all';
if(!empty($_GET['staff'])) {
    $staff = filter_var($_GET['staff'],FILTER_SANITIZE_STRING);
}

$project_edit = vuaed_visible_function($dbc, 'project');
$tab_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='$projecttype'"))['config_tabs']),explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project WHERE type='ALL'"))['config_tabs']))));
$result = mysqli_query($dbc, "SELECT `src`,`srcid`,`time_staff`,`time_date`,SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `time`,SUM(TIME_TO_SEC(`time_length`)) `seconds` FROM (SELECT CONCAT('Checklist ',`checklist`.`checklist_name`,' Item #',`checklist_name`.`checklistnameid`) time_type, 'checklist' `src`, `checklist`.`checklistid` `srcid`, 'checklist_name' `table`, `checklist_name_time`.`checklist_time_id` `tableid`, `checklist_name`.`checklist` time_heading, `checklist_name_time`.`contactid` time_staff, `checklist_name_time`.`timer_date` time_date, '' time_start, '' time_end, `checklist_name_time`.`work_time` time_length, '' `time_cards_id` FROM `checklist` RIGHT JOIN `checklist_name` ON `checklist`.`checklistid`=`checklist_name`.`checklistid` RIGHT JOIN `checklist_name_time` ON `checklist_name_time`.`checklist_id`=`checklist_name`.`checklistnameid` WHERE `projectid`='$projectid' UNION
	SELECT CONCAT('".TICKET_NOUN." #',`tickets`.`ticketid`) time_type, 'ticket' `src`, `tickets`.`ticketid` `srcid`, 'tickets' `table`, `ticket_timer`.`tickettimerid` `tableid`, `tickets`.`heading` time_heading, `ticket_timer`.`created_by` time_staff,  `ticket_timer`.`created_date` time_date, `ticket_timer`.`start_time` time_start, `ticket_timer`.`end_time` time_end, TIMEDIFF(`ticket_timer`.`end_time`,`ticket_timer`.`start_time`) time_length, '' `time_cards_id` FROM `tickets` RIGHT JOIN `ticket_timer` ON `tickets`.`ticketid`=`ticket_timer`.`ticketid` WHERE `projectid`='$projectid' AND `ticket_timer`.`deleted` = 0 UNION
	SELECT CONCAT('".TICKET_NOUN." #',`ticket_attached`.`ticketid`) time_type, 'ticket' `src`, `ticket_attached`.`ticketid` `srcid`, 'ticket_attached' `table`, `ticket_attached`.`id` `tableid`, IF(`ticket_attached`.`position` > 0,`positions`.`name`,`ticket_attached`.`position`) time_heading, `ticket_attached`.`hours_tracked` time_staff,  `ticket_attached`.`date_stamp` time_date, '' time_start, '' time_end, `ticket_attached`.`hours_tracked` time_length, '' `time_cards_id` FROM `ticket_attached` LEFT JOIN `positions` ON `positions`.`position_id`=`ticket_attached`.`position` WHERE `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid' AND `deleted`=0) AND `ticket_attached`.`deleted`=0 UNION
	SELECT CONCAT('".TICKET_NOUN." #',`ticket_time_list`.`ticketid`) time_type, 'ticket' `src`, `ticket_time_list`.`ticketid` `srcid`, 'ticket_time_list' `table`, `ticket_time_list`.`id` `tableid`, `ticket_time_list`.`time_type` time_heading, `ticket_time_list`.`created_by` time_staff,  LEFT(`ticket_time_list`.`created_date`,10) time_date, MID(`ticket_time_list`.`created_date`,11) time_start, '' time_end, `ticket_time_list`.`time_length` time_length, '' `time_cards_id` FROM `ticket_time_list` WHERE `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid' AND `time_type`='Manual Time' AND `deleted`=0) AND `deleted`=0 UNION
	SELECT CONCAT('Task #',`tasklist`.`tasklistid`) time_type, 'task' `src`, `tasklist`.`tasklistid` `srcid`, 'tasklist' `table`, `tasklist`.`tasklistid` `tableid`, `tasklist`.`heading` time_heading, `tasklist`.`contactid` time_staff, `tasklist`.`task_tododate` time_date, '' time_start, '' time_end, `tasklist`.`work_time` time_length, '' `time_cards_id` FROM `tasklist` WHERE `projectid`='$projectid' UNION
	SELECT CONCAT(`tasklist`.`project_milestone`,' Task #',`tasklist`.`tasklistid`) time_type, 'task' `src`, `tasklist`.`tasklistid` `srcid`, 'tasklist_time' `table`, `tasklist_time`.`time_id` `tableid`, `tasklist`.`heading` time_heading, `tasklist_time`.`contactid` time_staff, `tasklist_time`.`timer_date` time_date, '' time_start, '' time_end, `tasklist_time`.`work_time` time_length, '' `time_cards_id` FROM `tasklist` RIGHT JOIN `tasklist_time` ON `tasklist`.`tasklistid`=`tasklist_time`.`tasklistid` WHERE `tasklist`.`projectid`='$projectid') timers WHERE `time_date` BETWEEN '$start' AND '$end' AND '$actions' IN (`src`,'all') AND '$staff' IN (`time_staff`,'all') GROUP BY `src`,`srcid`,`time_date`,`time_staff` ORDER BY `time_date`, `time_start`");
if($result->num_rows > 0) {
    $table = '<table class="table table-bordered" style="width:100%;" border="1" cellpadding="3" cellspacing="0">
        <tr class="hidden-xs hidden-sm">
            <th>Date</th>
            <th>'.PROJECT_NOUN.'</th>
            <th>Action Item</th>
            <th>Staff</th>
            <th>Status</th>
            <th>Total Time</th>
        </tr>';
        $total_time = 0;
        while($report_row = $result->fetch_assoc()) {
            $total_time += $report_row['seconds'];
            $status = '';
            $label = '';
            if($report_row['src'] == 'ticket') {
                $item = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='".$report_row['srcid']."'")->fetch_assoc();
                $label = '<a href="../Ticket/index.php?edit='.$report_row['srcid'].'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\',\'auto\',true,true); return false;">'.get_ticket_label($dbc, $item).'</a>';
                $status = $item['status'];
            } else if($report_row['src'] == 'task') {
                $item = $dbc->query("SELECT * FROM `tasklist` WHERE `tasklistid`='".$report_row['srcid']."'")->fetch_assoc();
                $label = '<a href="../Tasks/add_task.php?tasklistid='.$report_row['srcid'].'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;">Task #'.$item['tasklistid'].'</a>';
                $status = $item['status'];
            } else if($report_row['src'] == 'checklist') {
                $item = $dbc->query("SELECT * FROM `checklist` WHERE `tasklistid`='".$report_row['srcid']."'")->fetch_assoc();
                $label = '<a href="../Checklist/checklist.php?view='.$report_row['srcid'].'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;">Checklist '.$item['checklist_name'].'</a>';
            }
            $table .= '<tr>
                <td data-title="Date">'.$report_row['time_date'].'</td>
                <td data-title="'.PROJECT_NOUN.'">'.PROJECT_NOUN.'# '.$projectid.'</td>
                <td data-title="Action Item">'.$label.'</td>
                <td data-title="Staff">'.get_contact($dbc, $report_row['time_staff']).'</td>
                <td data-title="Status">'.$status.'</td>
                <td data-title="Total Time">'.$report_row['time'].'</td>
            </tr>';
        }
        $table .= '<tr>
            <td data-title="" colspan="5">Total</td>
            <td data-title="Total Time">'.time_decimal2time($total_time / 3600, true).':00</td>
        </tr>
    </table>';
} else {
    $table = 'No Action Items Found';
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
            $footer_text = 'Status Report';
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
    // echo '<h4 style="text-align:center;">As on: '.date('d/m/Y').'</h4>'.$table;
    $pdf->writeHTML('<h4 style="text-align:center;">As on: '.date('d/m/Y').'</h4>'.$table, true, false, true, false, '');

	$pdf->Output('status_report_'.$projectid.'_at_'.$today_date.'.pdf', 'I');
    exit();
} else { ?>
    <form class="" method="GET" action="">
        <input type="hidden" name="edit" value="<?= $_GET['edit'] ?>">
        <input type="hidden" name="tab" value="action_item_report">
        <div class="col-sm-2 pull-right">
            <a target="_blank" href="?edit=<?= $projectid ?>&tab=action_item_report&starttime=<?= $_GET['starttime'] ?>&endtime=<?= $_GET['endtime'] ?>&staff=<?= $_GET['staff'] ?>&actions=<?= $_GET['actions'] ?>&output=PDF" class="pull-right"><img src="../img/pdf.png" class="inline-img"></a>
            <button class="btn brand-btn" name="submit" type="submit" value="search">Search</button>
            <button class="btn brand-btn" name="reset" type="submit" value="reset">Display All</button>
        </div>
        <div class="col-sm-10">
            <label class="col-sm-2">Start Date:</label>
            <div class="col-sm-4"><input type="text" class="form-control datepicker" name="starttime" placeholder="Start Date" value="<?= $start == 0000-00-00 ? '' : $start ?>"></div>
            <label class="col-sm-2">End Date:</label>
            <div class="col-sm-4"><input type="text" class="form-control datepicker" name="endtime" placeholder="End Date" value="<?= $end == 0000-00-00 || $end == '9999-12-31' ? '' : $end ?>"></div>
        </div>
        <div class="col-sm-10">
            <label class="col-sm-2">Staff:</label>
            <div class="col-sm-4">
                <select class="chosen-select-deselect" name="staff" data-placeholder="Select Action Item"><option />
                    <option <?= $actions == 'all' ? 'selected' : '' ?> value="all">All Staff</option>
                    <?php foreach(sort_contacts_query($dbc->query("SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status` > 0")) as $staff) { ?>
                        <option <?= $actions == $staff['contactid'] ? 'selected' : '' ?> value="<?= $staff['contactid'] ?>"><?= $staff['full_name'] ?></option>
                    <?php } ?>
                </select></div>
            <label class="col-sm-2">Action Item:</label>
            <div class="col-sm-4">
                <select class="chosen-select-deselect" name="action_items" data-placeholder="Select Action Item"><option />
                    <option <?= $actions == 'all' ? 'selected' : '' ?> value="all">All Items</option>
                    <?php if(in_array('Tickets',$tab_config)) { ?><option <?= $actions == 'ticket' ? 'selected' : '' ?> value="ticket"><?= TICKET_TILE ?></option><?php } ?>
                    <?php if(in_array('Tasks',$tab_config)) { ?><option <?= $actions == 'task' ? 'selected' : '' ?> value="task">Tasks</option><?php } ?>
                    <?php if(in_array('Checklists',$tab_config)) { ?><option <?= $actions == 'checklist' ? 'selected' : '' ?> value="checklist">Checklist</option><?php } ?>
                </select></div>
        </div>
        <div class="clearfix"></div>
    </form>
    <?php echo $table; ?>
    <div class="clearfix"></div>
<?php }
include('next_buttons.php'); ?>