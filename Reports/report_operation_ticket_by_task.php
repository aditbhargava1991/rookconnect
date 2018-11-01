<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
$report_fields = explode(',', get_config($dbc, 'report_operation_fields'));

if (isset($_POST['printpdf'])) {
    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];
    $taskgrouppdf = $_POST['taskgrouppdf'];
    $taskpdf = $_POST['taskpdf'];
    $search_extra_ticketpdf = $_POST['search_extra_ticketpdf'];

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);
    DEFINE('TASK_GROUP', $taskgrouppdf);
    DEFINE('TASK', $taskpdf);
    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));

	class MYPDF extends TCPDF {

		public function Header() {
			//$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
            if(REPORT_LOGO != '') {
                $image_file = 'download/'.REPORT_LOGO;
                $this->Image($image_file, 10, 10, 80, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = TICKET_TILE.' by Task'.(!empty(TASK_GROUP) ? ' - '.TASK_GROUP : '').(!empty(TASK) ? ' - '.TASK : '');
            $this->writeHTMLCell(0, 0, 0 , 15, $footer_text, 0, 0, false, "C", true);
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 25, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html .= report_output($dbc, $starttimepdf, $endtimepdf, $taskgrouppdf, $taskpdf, $search_extra_ticketpdf, 'padding:3px; border:1px solid black;', 'print');

    $today_date = date('Y_m_d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/'.TICKET_NOUN.'_by_task_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_operation_ticket_by_task', 0, WEBSITE_URL.'/Reports/Download/'.TICKET_NOUN.'_by_task_'.$today_date.'.pdf', 'Tickets by task Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/<?= TICKET_NOUN ?>_by_task_<?= $today_date ?>.pdf', 'fullscreen=yes');
	</script><?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $task_group_search = $taskgrouppdf;
    $task_search = $taskpdf;
    $search_extra_ticket = $search_extra_ticketpdf;
} ?>

<script type="text/javascript">
$(document).ready(function() {
    filterTasks();
});
$(document).on('change', 'select[name="task_group"]', filterTasks);
function filterTasks() {
    var task_group = $('[name="task_group"]').val();
    if(task_group != undefined && task_group != '') {
        $('[name="task"] option').hide();
        $('[name="task"] option[data-task-group="'+task_group+'"]').show();
    } else {
        $('[name="task"] option').show();
    }
    $('[name="task"]').trigger('change.select2');
}
</script>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span> Displays a list of <?= TICKET_TILE ?> by Task.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">

            <?php if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
				$search_extra = $_POST['search_extra_ticket'];
                $task_search = $_POST['task'];
                $task_group_search = $_POST['task_group'];
            }
            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }
            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            } ?>

            <center>
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
									<option <?php if ($row['ticketid'] == $search_extra_ticket) { echo " selected"; } ?> value='<?= $row['ticketid'] ?>' ><?= get_ticket_label($dbc, $row) ?></option>
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
					<label class="col-sm-4">Task Group:</label>
					<div class="col-sm-8">
						<select name="task_group" class="chosen-select-deselect" data-placeholder="Select Task Group"><option />
                            <?php $task_groups = $dbc->query("SELECT `category` FROM `task_types` WHERE `deleted`=0 GROUP BY `category`");
							while($task_group = $task_groups->fetch_assoc()) {
                                echo '<option value="'.$task_group['category'].'" '.($task_group['category'] == $task_group_search ? 'selected' : '').'>'.$task_group['category'].'</option>';
                            } ?>
						</select>
					</div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Task:</label>
					<div class="col-sm-8">
						<select name="task" class="chosen-select-deselect" data-placeholder="Select Task"><option />
                            <?php $tasks = $dbc->query("SELECT `category`,`description` FROM `task_types` WHERE `deleted`=0 ORDER BY `category`,`description`");
							while($task = $tasks->fetch_assoc()) {
                                echo '<option data-task-group="'.$task['category'].'" value="'.$task['description'].'" '.($task['description'] == $task_search && (empty($task_group_search) || $task_group_search == $task['category']) ? 'selected' : '').'>'.$task['description'].'</option>';
							} ?>
						</select>
					</div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Date From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Date Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
				<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
                <div class="clearfix"></div>
			</center>

            <input type="hidden" name="taskgrouppdf" value="<?php echo $task_group_search; ?>">
            <input type="hidden" name="taskpdf" value="<?php echo $task_search; ?>">
            <input type="hidden" name="search_extra_ticketpdf" value="<?php echo $search_extra_ticket; ?>">
            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>
			<div class="clearfix"></div>

            <?php
                echo report_output($dbc, $starttime, $endtime, $task_group_search, $task_search, $search_extra_ticket);
            ?>

        </form>
        
<?php
function report_output($dbc, $starttime, $endtime, $task_group_search, $task_search, $search_extra_ticket, $pdf_style, $report_type) {
	$report_data = '';
    $starttime = filter_var($starttime,FILTER_SANITIZE_STRING);
    $endtime = filter_var($endtime,FILTER_SANITIZE_STRING);
    $task_group_search = filter_var($task_group_search,FILTER_SANITIZE_STRING);
	$task_search = filter_var($task_search,FILTER_SANITIZE_STRING);
	$search_extra_ticket = filter_var($search_extra_ticket,FILTER_SANITIZE_STRING);

	$query = "SELECT * FROM `tickets` WHERE `deleted` = 0 AND `created_date` BETWEEN '$starttime' AND '$endtime' AND IFNULL(`task_available`,'') != ''";
    if(!empty($task_group_search)) {
        $available_tasks = [];
        foreach(explode('#*#', get_config($dbc, 'ticket_ALL_staff_tasks')) as $task_group) {
            $task_group = array_filter(array_unique(explode('*#*',$task_group)));
            $task_group_name = $task_group[0];
            if($task_group_name == $task_group_search) {
                unset($task_group[0]);
                foreach($task_group as $task) {
                    $available_tasks[] = $task;
                }
            }
        }
        $available_tasks = array_unique(array_filter($available_tasks));
        $query .= " AND (CONCAT(',',`task_available`,',') LIKE '%,".implode(",%' OR CONCAT(',',`task_available`,',') LIKE '%,", $available_tasks).",%')";
    }
	if(!empty($task_search)) {
        $available_tasks = [$task_search];
        $query .= " AND CONCAT(',',`task_available`,',') LIKE '%,$task_search,%'";
	}
    $query .= " ORDER BY `created_date`";

    $result = mysqli_query($dbc, $query);

	if(mysqli_num_rows($result) > 0) {
        $report_data .= '<table width="100%" border="1" class="table table-bordered" style="'.$pdf_style.'">';
        $report_data .= '<tr class="hidden-xs">
            <th>Date</th>
            <th>Task</th>
            <th>'.TICKET_NOUN.'</th>
            <!--<th>Materials on '.TICKET_NOUN.'</th>-->
        </tr>';
        while($row = mysqli_fetch_assoc($result)) {
            // $material_list = [];
            // $materials = $dbc->query("SELECT * FROM `ticket_attached` WHERE `ticketid`='".$row['ticketid']."' AND `deleted`=0 AND `src_table`='material' AND `date_stamp`='".$row['created_date']."'");
            foreach(explode(',',$row['task_available']) as $ticket_task) {
                if(in_array($ticket_task, $available_tasks) || empty($available_tasks)) {
                    $report_data .= '<tr>
                        <td data-title="Date">'.date('Y-m-d', strtotime($row['created_date'])).'</td>
                        <td data-title="Task">'.$ticket_task.'</td>
                        <td data-title="'.TICKET_NOUN.'">'.($report_type != 'print' ? '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'" onclick="overlayIFrameSlider(this.href+\'&date='.$ow['created_date'].'&calendar_view=true\'); return false;">' : '').get_ticket_label($dbc, $row).($report_type != 'print' ? '</a>' : '').'</td>
                        <!--<td data-title="Materials on '.TICKET_NOUN.'"></td>-->
                    </tr>';
                }
            }
        }
        $report_data .= '</table>';
	} else {
		$report_data .= '<h3>No '.TICKET_TILE.' Found</h3>';
	}
    return $report_data;
} ?>
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