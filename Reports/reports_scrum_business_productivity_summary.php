<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {

    $search_businesspdf = $_POST['search_businesspdf'];
    $search_staffpdf = $_POST['search_staffpdf'];

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
            $footer_text = 'Business Productivity Summary';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);
		}

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

    $html .= report_receivables($dbc, $search_businesspdf, $search_staffpdf, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/reports_scrum_business_productivity_summary'.$today_date.'.pdf', 'F');
    track_download($dbc, 'reports_scrum_business_productivity_summary', 0, WEBSITE_URL.'/Reports/Download/reports_scrum_business_productivity_summary'.$today_date.'.pdf', 'Business Productivity Summary Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/reports_scrum_business_productivity_summary<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $search_business = $search_businesspdf;
    $search_staff = $search_staffpdf;
    } ?>


        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if($search_business == '') {
                $search_business = '';
            }
            if($search_staff == '') {
                $search_staff = $_SESSION['contactid'];
            }

            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $search_business = $_POST['search_business'];
                $search_staff = $_POST['search_staff'];
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
					<label class="col-sm-4">Search By Business:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a Business" name="search_business" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
							<option value=""></option>
							<?php $query = mysqli_query($dbc,"SELECT distinct(businessid) FROM tickets");
							while($row1 = mysqli_fetch_array($query)) {
								?><option <?php if ($row1['businessid'] == $search_business) { echo " selected"; } ?> value='<?php echo  $row1['businessid']; ?>' ><?php echo get_client($dbc, $row1['businessid']); ?></option>
							<?php } ?>
						</select>
					</div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">OR Search By Staff:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a Staff" name="search_staff" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
							<option value="">Display All</option>
							<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT distinct(contactid), first_name, last_name FROM contacts WHERE category='staff' AND deleted=0 AND status=1"),MYSQLI_ASSOC));
							foreach($query as $row1) {
								?><option <?php if ($row1 == $search_staff) { echo " selected"; } ?> value='<?php echo  $row1; ?>' ><?php echo get_staff($dbc, $row1); ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="search_businesspdf" value="<?php echo $search_business; ?>">
            <input type="hidden" name="search_staffpdf" value="<?php echo $search_staff; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                echo report_receivables($dbc, $search_business, $search_staff, '', '', '');
            ?>

        </form>

<?php
function report_receivables($dbc, $search_business, $search_staff, $table_style, $table_row_style, $grand_total_style) {

    if($search_business != '') {
        $query = mysqli_query($dbc,"SELECT ticketid, businessid, contactid, heading, max_time, spent_time, status FROM tickets WHERE businessid IS NOT NULL AND businessid='$search_business' ORDER BY businessid");
    } elseif($search_staff != '') {
        $query = mysqli_query($dbc,"SELECT ticketid, businessid, contactid, heading, max_time, spent_time, status FROM tickets WHERE businessid IS NOT NULL AND contactid LIKE '%," . $search_staff . ",%' ORDER BY businessid");
    } else {
        $query = mysqli_query($dbc,"SELECT ticketid, businessid, contactid, heading, max_time, spent_time, status FROM tickets WHERE businessid IS NOT NULL ORDER BY businessid");
    }

    $bid = '';
    $report_data = '';
    $total_estimated = array();
    $total_tracked = array();
    while($row = mysqli_fetch_array($query)) {
        $contactid = $row['businessid'];

        if($bid != $row['businessid']) {

            if($bid != '') {

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

                $report_data .= '<td>Summary</td><td></td><td style="'.$style1.'">'.$te12.'</td><td style="'.$style1.'">'.$tt12.'</td><td></td>';
                $report_data .= "</tr>";


                $report_data .= "</table><br>";

                $total_estimated = array();
                $total_tracked = array();

            }

            $report_data .= '<h3>'.get_client($dbc, $row['businessid']).'</h3>';

            if($bid != '') {
                $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
                $report_data .= '<tr style="'.$table_row_style.'">
                <th width="10%">'.TICKET_NOUN.'#</th>
                <th width="30%">Staff</th>
                <th width="20%">Estimated Hrs for Completion</th>
                <th width="20%">Time Tracked</th>
                <th width="20%">'.TICKET_NOUN.' Status</th>
                </tr>';

            }

            if($bid == '') {
                $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
                $report_data .= '<tr style="'.$table_row_style.'">
                <th width="10%">'.TICKET_NOUN.'#</th>
                <th width="30%">Staff</th>
                <th width="20%">Estimate Hrs for Completion</th>
                <th width="20%">Time Tracked</th>
                <th width="20%">'.TICKET_NOUN.' Status</th>
                </tr>';
            }

            $bid = $row['businessid'];
        }

        $report_data .= '<tr nobr="true">';

        $report_data .= '<td>'.$row['ticketid'].'</td>';

        $report_data .= '<td>'.get_multiple_contact($dbc, $row['contactid']).'</td>';

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

    $report_data .= '<td><b>Summary</b> </td><td></td><td style="'.$style2.'"><b>'.$te1.' </b></td><td style="'.$style2.'"><b>'.$tt1.'</b></td><td></td>';
    $report_data .= "</tr>";
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