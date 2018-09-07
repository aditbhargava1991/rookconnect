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
    $jobpdf =$_POST['jobpdf'];
    $afe_numberpdf =$_POST['afe_numberpdf'];
    $locationpdf =$_POST['locationpdf'];

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
            $footer_text = 'Work Orders';
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

    $html .= report_receivables($dbc, $starttimepdf, $endtimepdf, $jobpdf , 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/timetracking_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_work_order', 0, WEBSITE_URL.'/Reports/Download/timetracking_'.$today_date.'.pdf', 'Work Orders Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/timetracking_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $projectid = $jobpdf;
    } ?>


        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-inline" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php

            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $projectid = $_POST['projectid'];
            }
            if (isset($_POST['display_all_inventory'])) {
                $projectid = '';
                $afe_number = '';
                $location = '';
            }
            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-d');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
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

            <div class="form-group">
                <div class="col-sm-8">
                    <!--
                    <select name="projectid" data-placeholder="Choose Job..." class="chosen-select-deselect form-control" width="380">
                        <option value=''>Choose Job</option>
                        <?php
                        $result = mysqli_query($dbc, "SELECT distinct(projectid) FROM workorder");
                        while($row = mysqli_fetch_assoc($result)) {
                            if ($projectid == $row['projectid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value = '".$row['projectid']."'>#".$row['projectid']."</option>";
                        }
                       ?>
                    </select>
                    -->
                    <!--
                    <select name="staffid" data-placeholder="Choose Person..." class="chosen-select-deselect form-control" width="380">
                        <option value=''>Choose Person</option>
                        <?php
                        $result = mysqli_query($dbc, "SELECT distinct(created_by) FROM workorder_timer");
                        while($row = mysqli_fetch_assoc($result)) {
                            if ($staffid == $row['created_by']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value = '".$row['created_by']."'>".get_staff($dbc, $row['created_by'])."</option>";
                        }
                       ?>
                    </select>
                    -->
                </div>
            </div>

            <!--
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
            <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
            -->

            <br>
            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="jobpdf" value="<?php echo $projectid; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                echo report_receivables($dbc, $starttime, $endtime, $projectid, '', '', '');
            ?>

        </form>


<?php
function report_receivables($dbc, $starttime, $endtime, $projectid, $table_style, $table_row_style, $grand_total_style) {
    $report_data = '';

    if($projectid != '') {
        $result = mysqli_query($dbc,"SELECT w.*, wt.* FROM workorder w, workorder_timer wt WHERE w.workorderid = wt.workorderid AND w.projectid='$projectid'");
    } else {
        $result = mysqli_query($dbc,"SELECT * FROM workorder");
    }

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th>Work Order#</th>';
    $report_data .= '<th>Job#</th>';
    $report_data .= '<th>Staff : Hours</th>';
    $report_data .=  "</tr>";

    while($row = mysqli_fetch_array( $result ))
    {
        $report_data .= '<tr nobr="true">';
        $timetrackingid = $row['timetrackingid'];
        $report_data .=  '<td data-title="Code">' . $row['workorderid'] . '</td>';
        $report_data .=  '<td data-title="Code">' . $row['projectid'] . '</td>';

        $contactid = $row['contactid'];

        $report_data .=  '<td data-title="Code">';
        $to = explode(',', $row['contactid']);
        $staff = '';
        foreach($to as $category => $value)  {
            if($value != '') {
                $report_data .= get_staff($dbc, $value).' : ';
                $workorderid = $row['workorderid'];
                $workorder_timer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(`timer` SEPARATOR ',') AS all_timer FROM workorder_timer WHERE workorderid='$workorderid' AND created_by = '$value' AND timer_type = 'Work'"));
                $all_timer = explode(',', $workorder_timer['all_timer']);

                $report_data .= AddPlayTime($all_timer);
                $report_data .= '<br>';
            }
        }
        $report_data .=  '</td>';

        $report_data .=  "</tr>";
    }

    $report_data .=  '</table>';

    $report_data .=  '<br><br>';

    $result = mysqli_query($dbc,"SELECT GROUP_CONCAT(`timer` SEPARATOR ',') AS all_timer, timer_task, workorderid FROM workorder_timer WHERE timer_type = 'Work' GROUP BY workorderid, timer_task");

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th>Work Order#</th>';
    $report_data .= '<th>Task</th>';
    $report_data .= '<th>Hours</th>';
    $report_data .=  "</tr>";

    while($row = mysqli_fetch_array( $result ))
    {
        $report_data .= '<tr nobr="true">';
        $timetrackingid = $row['timetrackingid'];
        $report_data .=  '<td data-title="Code">' . $row['workorderid'] . '</td>';
        //$report_data .=  '<td data-title="Code">' . $row['projectid'] . '</td>';
        $report_data .=  '<td data-title="Code">' . $row['timer_task'] . '</td>';
        $report_data .=  '<td data-title="Code">' . AddPlayTime(explode(',', $row['all_timer'])) . '</td>';

        $report_data .=  "</tr>";
    }

    $report_data .=  '</table>';

    return $report_data;
}

function AddPlayTime($times) {
    // loop throught all the times
    foreach ($times as $time) {
        list($hour, $minute) = explode(':', $time);
        $minutes += $hour * 60;
        $minutes += $minute;
    }

    $hours = floor($minutes / 60);
    $minutes -= $hours * 60;

    // returns the time already formatted
    return sprintf('%02d H %02d M', $hours, $minutes);
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