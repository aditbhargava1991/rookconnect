<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {
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
            $footer_text = 'Scrum Status Report';
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

    $html .= report_receivables($dbc, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/scrum_status_report_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'reports_scrum_status_report', 0, WEBSITE_URL.'/Reports/Download/scrum_status_report_'.$today_date.'.pdf', 'Scrum Status Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/scrum_status_report_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    } ?>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <br>

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                echo report_receivables($dbc, '', '', '');
            ?>

        </form>


<?php
function report_receivables($dbc, $table_style, $table_row_style, $grand_total_style) {

    $each_tab = explode(',', get_config($dbc, 'ticket_status'));

    $report_data = '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    $report_data .= '<th width="20%">Status</th><th width="80%">Total '.TICKET_TILE.'</th>';
    $report_data .= "</tr>";

    $report_data = '<table border="1px" class="table table-bordered" style="'.$table_style.'">';

    $report_data .= '<tr style="'.$table_row_style.'">
    <th width="20%">Status</th>
    <th width="80%">Total '.TICKET_TILE.'</th>
    </tr>';
    $total_ticket = 0;
    foreach ($each_tab as $cat_tab) {
        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.$cat_tab.'</td>';

        $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(ticketid) AS total_ticket FROM tickets WHERE status='$cat_tab'"));

        $report_data .= '<td>'.$get_config['total_ticket'].'</td>';

        $report_data .= "</tr>";
        $total_ticket += $get_config['total_ticket'];
    }

    $get_config1 = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(ticketid) AS `total_ticket1`, GROUP_CONCAT(`ticketid` SEPARATOR ',') AS `all_ticket`, GROUP_CONCAT(`heading` SEPARATOR '#*#') AS `all_heading` FROM tickets WHERE status != 'Archived' AND (contactid IS NULL OR contactid = '0' OR contactid = ',,')"));
    $d_ticket = '';
    $each_tab = explode(',', $get_config1['all_ticket']);
    $each_heading = explode('#*#', $get_config1['all_heading']);
    $m = 0;
    foreach ($each_tab as $cat_tab) {
        $d_ticket .= "<a target= '_blank' href='../Ticket/index.php?edit=".$cat_tab."'>".$cat_tab.' : '.$each_heading[$m].'</a><br>';
        $m++;
    }

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td><b>Total</b></td><td><b>'.$total_ticket.'</b></td>';
    $report_data .= "</tr>";

    $report_data .= '<tr nobr="true">';
    $report_data .= '<td>Unassigned</td><td>'.$get_config1['total_ticket1'].'<br>'.$d_ticket.'</td>';
    $report_data .= "</tr>";

    $report_data .= "</table>";
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