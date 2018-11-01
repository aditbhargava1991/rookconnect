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
    $staffidpdf =$_POST['staffidpdf'];

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
            $footer_text = 'Checklist Time Tracking';
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

    $html .= report_receivables($dbc, $starttimepdf, $endtimepdf , 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;', $staffidpdf);

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
    $staffid = $staffidpdf;
    } ?>
        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-inline" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php

            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $staffid = $_POST['staffid'];
            }
            if (isset($_POST['display_all_inventory'])) {
                $staffid = '';
            }
            if($starttime == 0000-00-00) {
                $starttime = '';
            }

            if($endtime == 0000-00-00) {
                $endtime = '';
            }
            ?>
            <!--
            <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">From:</label>
                <div class="col-sm-8">
                    <input name="starttime" type="text" class="datepicker" value="<?php echo $starttime; ?>">
                </div>
            </div>

            <div class="form-group until">
                <label for="site_name" class="col-sm-4 control-label">Until:</label>
                <div class="col-sm-8">
                    <input name="endtime" type="text" class="datepicker" value="<?php echo $endtime; ?>">
                </div>
            </div>
            -->

            <div class="form-group col-sm-5">
				<label class="col-sm-4 control-label">Staff:</label>
                <div class="col-sm-8">
                    <select name="staffid" data-placeholder="Select Staff..." class="chosen-select-deselect form-control" width="380">
                        <option value=''>Select Staff</option>
                        <?php
                        $result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=0 AND `status`=1"),MYSQLI_ASSOC));
                        foreach($result as $row) {
                            echo "<option ".($staffid == $row ? 'selected' : '')." value = '".$row."'>".get_staff($dbc, $row)."</option>";
                        }
                       ?>
                    </select>
                </div>
            </div>

            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
            <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>

            <br>
            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="staffidpdf" value="<?php echo $staffid; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <div id="no-more-tables"><?= report_receivables($dbc, $starttime, $endtime, '', '', '', $staffid) ?></div>

        </form>
        

<?php
function report_receivables($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $staffid) {
	$clause = '';
	if($starttime != '') {
		$clause .= " AND `time_date` >= '$starttime' AND `time_date` <= '$endtime'";
	}
	if($staffid > 0) {
		$clause .= " AND `time_staff`='$staffid'";
	}
	$result = mysqli_query($dbc, "SELECT * FROM (SELECT CONCAT('Checklist ',`checklist`.`checklist_name`,' Item #',`checklist_name`.`checklistnameid`) time_type, `checklist_name`.`checklist` time_heading, `checklist_name_time`.`contactid` time_staff, `checklist_name_time`.`timer_date` time_date, '' time_start, '' time_end, `checklist_name_time`.`work_time` time_length FROM `checklist` RIGHT JOIN `checklist_name` ON `checklist`.`checklistid`=`checklist_name`.`checklistid` RIGHT JOIN `checklist_name_time` ON `checklist_name_time`.`checklist_id`=`checklist_name`.`checklistnameid`) timers WHERE `time_length` != '' $clause ORDER BY `time_date`, `time_type`");
    $report_data = '';

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
        <th>Type</th>
        <th>Heading</th>
        <th>Staff</th>
        <th>Date</th>
        <th>Duration</th>
        </tr>';

    while($row = mysqli_fetch_array( $result ))
    {
		$report_data .= '<tr>';

		$time_length = date('G:i',strtotime(date('Y-m-d ').$row['time_length']));
		$minutes = explode(':',$time_length);
		$total_time += ($minutes[0] * 60) + $minutes[1];

		$report_data .= '<td data-title="Type">' . $row['time_type'] . '</td>';
		$report_data .= '<td data-title="Heading">' . html_entity_decode($row['time_heading']) . '</td>';
		$report_data .= '<td data-title="Staff">' . get_contact($dbc, $row['time_staff']) . '</td>';
		$report_data .= '<td data-title="Date">' . $row['time_date'] . '</td>';
		$report_data .= '<td data-title="Duration">' . $time_length . '</td>';

		$report_data .= "</tr>";
    }
    $report_data .= '<tr>
        <td colspan="4">Total Time Tracked</td>
        <td data-title="Total Time Tracked">'.floor($total_time/60).':'.sprintf("%02d", $total_time%60).'</td>
        </tr>';

    $report_data .=  '</table>';

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