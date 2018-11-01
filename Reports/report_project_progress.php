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
    $projectidpdf =$_POST['projectidpdf'];

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);
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
            $footer_text = 'Project Progress Report';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "C", true);
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

    $html .= report_receivables($dbc, $starttimepdf, $endtimepdf , 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;', $projectidpdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/timetracking_'.$today_date.'.pdf', 'F');

    track_download($dbc, 'report_checklist_time', 0, WEBSITE_URL.'/Reports/Download/timetracking_'.$today_date.'.pdf', 'Checklist Time Tracking Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/timetracking_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $projectid = $projectidpdf;
} ?>
<style>
* {box-sizing: border-box}

.skills {
  text-align: right;
  padding-right: 20px;
  color: white;
}

.html {width: 90%; background-color: #4CAF50;}

</style>
        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-inline" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php

            if (isset($_POST['search_email_submit'])) {
                $projectid = $_POST['projectid'];
            }
            if (isset($_POST['display_all_inventory'])) {
                $projectid = '';
            }

            ?>

            <div class="form-group col-sm-8">
				<label class="col-sm-4 control-label">Project:</label>
                <div class="col-sm-8">
				<select data-placeholder="Select..." name="projectid" class="chosen-select-deselect form-control" width="380">
				  <option value=""></option>
				  <?php $query = mysqli_query($dbc,"SELECT projectid, projecttype, project_name, businessid, clientid, status FROM project WHERE deleted=0 AND status NOT IN ('Archive') order by `projectid` DESC");
					while($row = mysqli_fetch_array($query)) {
                        echo "<option ".($projectid == $row['projectid'] ? 'selected' : '')." value = '".$row['projectid']."'>".get_project_label($dbc,$row)."</option>";
					}
				  ?>
				</select>
                </div>
            </div>

            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
            <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>

            <br>
            <input type="hidden" name="projectidpdf" value="<?php echo $projectid; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
            if($projectid != '') {
                echo report_receivables($dbc, $starttime, $endtime, '', '', '', $projectid);
            } else {
              echo '<h1>Please choose project to view progress report.</h1>';
            } ?>

        </form>


<?php
function report_receivables($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $projectid) {

	$total_estimated_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `time` FROM `ticket_time_list` WHERE `deleted`=0 AND `time_type` IN ('Completion Estimate','QA Estimate') AND `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `deleted`=0 AND `projectid`='$projectid')")->fetch_assoc()['time'];

	$total_tracked_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time`))) `time` FROM (SELECT `time_length` `time`, `ticketid` FROM `ticket_time_list` WHERE `deleted`=0 AND `time_type`='Manual Time' UNION SELECT `timer` `time`, `ticketid` FROM `ticket_timer`) `time_list` WHERE `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid' AND `deleted`=0)")->fetch_assoc()['time'];

    sscanf($total_estimated_time, "%d:%d:%d", $hours, $minutes, $seconds);
    $second_est = isset($seconds) ? $hours * 3600 + $minutes * 60 + $seconds : $hours * 60 + $minutes;

    sscanf($total_tracked_time, "%d:%d:%d", $hours1, $minutes1, $seconds1);
    $second_tra = isset($seconds1) ? $hours1 * 3600 + $minutes1 * 60 + $seconds1 : $hours1 * 60 + $minutes1;

    $main_timeline = round(($second_tra * 100)/$second_est);

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr nobr="true" style="'.$table_row_style.'">
    <td width="33.33%"> <h3>Estimated Hours</h3><h1>'.substr($total_estimated_time, 0, 5).'</h1></td>
    <td width="33.33%"><h3>Actual Hours</h3><h1>'.substr($total_tracked_time, 0, 5).'</h1></td>
    <td width="33.33%"><h3>Progress</h3><h1>'.$main_timeline.'%</h1></td>';
    $report_data .= "</tr>";
    $report_data .= '</table><br><br><br>';

    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr nobr="true" style="'.$table_row_style.'">
    <th width="40%">Service</th>
    <th width="10%">Estimated Hrs</th>
    <th width="10%">Actual Hrs</th>
    <th width="40%">Timeline</th>';
    $report_data .= "</tr>";

    $member_list = mysqli_query($dbc, "SELECT GROUP_CONCAT(ticketid SEPARATOR ', ') AS all_ticket, serviceid FROM `tickets` WHERE `projectid`='$projectid' AND serviceid IS NOT NULL AND serviceid != '0' GROUP BY serviceid");
    while($member = mysqli_fetch_assoc($member_list)) {
        $serviceid = $member['serviceid'];
        $all_ticket = $member['all_ticket'];

	    $total_service_estimated_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time_length`))) `time` FROM `ticket_time_list` WHERE `deleted`=0 AND `time_type` IN ('Completion Estimate','QA Estimate') AND `ticketid` IN ($all_ticket)")->fetch_assoc()['time'];

	    $total_service_tracked_time = $dbc->query("SELECT SEC_TO_TIME(SUM(TIME_TO_SEC(`time`))) `time` FROM (SELECT `time_length` `time`, `ticketid` FROM `ticket_time_list` WHERE `deleted`=0 AND `time_type`='Manual Time' UNION SELECT `timer` `time`, `ticketid` FROM `ticket_timer`) `time_list` WHERE `ticketid` IN ($all_ticket)")->fetch_assoc()['time'];

        sscanf($total_service_estimated_time, "%d:%d:%d", $hours2, $minutes2, $seconds2);
        $seconds_est = isset($seconds2) ? $hours2 * 3600 + $minutes2 * 60 + $seconds2 : $hours2 * 60 + $minutes2;

        sscanf($total_service_tracked_time, "%d:%d:%d", $hours3, $minutes3, $seconds3);
        $seconds_tra = isset($seconds3) ? $hours3 * 3600 + $minutes3 * 60 + $seconds3 : $hours3 * 60 + $minutes3;

        $timeline = round(($seconds_tra * 100)/$seconds_est);

        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.get_all_from_service($dbc, $serviceid, 'heading').'</td>';
        $report_data .= '<td>'.$total_service_estimated_time.'</td>';
        $report_data .= '<td>'.$total_service_tracked_time.'</td>';

        if(($total_service_estimated_time > $total_service_tracked_time) && ($timeline > 0)) {
            $report_data .= '<td>
            <div style="background-color: #4CAF50;height:20px;width:'.$timeline.'%">'.$timeline.'%</div>
            </td>';
        } else {
            $report_data .= '<td>
            <div style="height:20px;width:0%">-</div>
            </td>';
        }

        $report_data .= '</tr>';


    }
    $report_data .= '</table><br>';

    return $report_data;
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