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
            $footer_text = 'Staff Productivity Summary';
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

	//$pdf->AddPage('L', 'LETTER');
    //$pdf->SetFont('helvetica', '', 9);

    //$html .= report_receivables($dbc, $search_businesspdf, $search_staffpdf, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $start_date = date('Y-m-d', strtotime($starttimepdf));
    $end_date = date('Y-m-d', strtotime($endtimepdf));
    $html = '';

    for($date = $start_date; $date <= $end_date; $date = date('Y-m-d', strtotime($date. ' + 1 days')))
    {
        $html = '';

        //$html .= "<h3>".$date."</h3>";
        $html .= report_receivables($dbc, $date, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

        if($html != '') {
            $pdf->AddPage('L', 'LETTER');
            $pdf->SetFont('helvetica', '', 9);

            $pdf->writeHTML($html, true, false, true, false, '');
        }
    }

    $today_date = date('Y-m-d');
	//$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/reports_scrum_staff_productivity_summary'.$today_date.'.pdf', 'F');
    track_download($dbc, 'reports_scrum_staff_productivity_summary', 0, WEBSITE_URL.'/Reports/Download/reports_scrum_staff_productivity_summary'.$today_date.'.pdf', 'Staff Productivity Summary Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/reports_scrum_staff_productivity_summary<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>


        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
            }

            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }
            ?>
            <center><div class="form-group">
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

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
			<div class="clearfix"></div>

            <?php
                $start_date = date('Y-m-d', strtotime($starttime));
                $end_date = date('Y-m-d', strtotime($endtime));

                for($date = $start_date; $date <= $end_date; $date = date('Y-m-d', strtotime($date. ' + 1 days')))
                {
                    echo report_receivables($dbc, $date, '', '', '');
                    echo "<br>";
                }

            ?>

        </form>

<?php
function report_receivables($dbc, $starttime, $table_style, $table_row_style, $grand_total_style) {

    $query = mysqli_query($dbc,"SELECT ticketid, businessid, contactid, heading, max_time, spent_time, status FROM tickets WHERE businessid IS NOT NULL AND to_do_date <= '".$starttime."' AND to_do_end_date >= '".$starttime."' ORDER BY contactid");
    $num_rows = mysqli_num_rows($query);
    $report_data = '<h3>'.$starttime.'</h3>';

    if($num_rows > 0) {
        $bid = 'N/A';
        $total_estimated = array();
        $total_tracked = array();
        while($row = mysqli_fetch_array($query)) {
            $contactid = $row['contactid'];

            if($bid != $row['contactid']) {

                if($bid != 'N/A') {

                    $report_data .= "<tr>";

                    $te12 = AddPlayTime($total_estimated);
                    $tt12 = AddPlayTime($total_tracked);

                    $time11    =   strtotime($te12);
                    $time21   =   strtotime($tt12);
                    $style1 = '';
                    if($time11 > $time21) {
                        $style1 = 'color:blue;';
                    } else {
                        $style1 = 'color:red;';
                    }

                    $report_data .= '<td>Summary </td><td></td><td style="'.$style1.'">'.$te12.' </td><td style="'.$style1.'">'.$tt12.'</td><td></td>';
                    $report_data .= "</tr>";


                    $report_data .= "</table><br>";

                    $total_estimated = array();
                    $total_tracked = array();

                }

                $report_data .= '<h4>'.get_multiple_contact($dbc, $row['contactid']).'</h4>';

                if($bid != '') {
                    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
                    $report_data .= '<tr style="'.$table_row_style.'">
                    <th width="10%">'.TICKET_NOUN.'#</th>
                    <th width="30%">Heading</th>
                    <th width="20%">Estimated Hrs for Completion</th>
                    <th width="20%">Time Tracked</th>
                    <th width="20%">'.TICKET_NOUN.' Status</th>
                    </tr>';
                } else {
                    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
                    $report_data .= '<tr style="'.$table_row_style.'">
                    <th width="10%">'.TICKET_NOUN.'#</th>
                    <th width="30%">Heading</th>
                    <th width="20%">Estimate Hrs for Completion</th>
                    <th width="20%">Time Tracked</th>
                    <th width="20%">'.TICKET_NOUN.' Status</th>
                    </tr>';
                }

                $bid = $row['contactid'];
            }

            $report_data .= '<tr nobr="true">';

            $report_data .= '<td>'.$row['ticketid'].'</td>';
            $report_data .= '<td>'.$row['heading'].'</td>';

            //$report_data .= '<td>'.get_multiple_contact($dbc, $row['contactid']).'</td>';

            $ticketid = $row['ticketid'];

            $result = mysqli_query($dbc, "SELECT * FROM ticket_timer WHERE ticketid = '$ticketid' AND `deleted` = 0");
            $num_rows = mysqli_num_rows($result);
            if($num_rows > 0) {
                $times = array();
                while($row2 = mysqli_fetch_array($result)) {
                    if($row2['end_time'] != '' && $row2['timer_type'] == 'Work') {
                        $to_time = strtotime($row2['start_time']);
                        $from_time = strtotime($row2['end_time']);
                        $times[] = round(abs($to_time - $from_time) / 60,2);
                    }
                }

                $st = AddPlayTime2($times);
            } else {
                $st = $row['spent_time'];
            }

            $time1    =   strtotime($row['max_time']);
            $time2   =   strtotime($st);
            if($time1 > $time2) {
                $style = 'color:blue;';
            } else {
                $style = 'color:red;';
            }
            $report_data .=  '<td data-title="Code" style="'.$style.'">' . $row['max_time'] . '</td>';

            $report_data .=  '<td data-title="Code" style="'.$style.'">' . $st . '</td>';

            $total_estimated[] = $row['max_time'];
            $total_tracked[] = $st;

            $report_data .= '<td>'.$row['status'].'</td>';
            $report_data .= "</tr>";
        }

        $report_data .= "<tr>";

        $te1 = AddPlayTime($total_estimated);
        $tt1 = AddPlayTime($total_tracked);

        $time12    =   strtotime($te1);
        $time22   =   strtotime($tt1);
        $style2 = '';
        if($time12 > $time22) {
            $style2 = 'color:blue;';
        } else {
            $style2 = 'color:red;';
        }

		$report_data .= '<td>Summary</td><td></td><td style="'.$style2.'">'.$te1.'</td><td style="'.$style2.'">'.$tt1.'</td><td></td>';
        $report_data .= "</tr>";
        $report_data .= "</table>";

    } else {
		$report_data .= '<p>No Results Found.</p>';
	}

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
    return sprintf('%02d:%02d:00', $hours, $minutes);
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
    return sprintf('%02d:%02d:00', $hours, $minutes);
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