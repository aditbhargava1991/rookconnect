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
    $siteidpdf = $_POST['siteidpdf'];

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('SITEID', $siteidpdf);
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
            $footer_text = 'Daily Manifest Summary For <b>'.START_DATE.'</b>';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : You can see Manifests created on a given date.";
            $this->writeHTMLCell(0, 0, 10 , 45, $footer_text, 0, 0, false, "R", true);
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 65, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $html .= report_summary($dbc, $starttimepdf, $siteidpdf, 'padding:3px; border:1px solid black;', '', '');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/manifest_summary_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_daily_manifest_summary', 0, WEBSITE_URL.'/Reports/Download/manifest_summary_'.$today_date.'.pdf', 'Daily Manifest Summary Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/manifest_summary_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $siteid = $siteidpdf;
    } ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            You can see Manifests created on a given date.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">

            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
				$siteid = $_POST['siteid'];
            } else if(!empty($_GET['from'])) {
                $starttime = $_GET['from'];
            } else if($starttime == 0000-00-00) {
                $starttime = date('Y-m-d');
            }

            ?>
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">For:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="siteidpdf" value="<?php echo $siteid; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                //echo '<a href="report_referral.php?referral=printpdf&starttime='.$starttime.'&endtime='.$endtime.'" class="btn brand-btn pull-right">Print Report</a></h4><br>';

                echo report_summary($dbc, $starttime, $siteid, '', '', '');
            ?>
        </form>

<?php
function report_summary($dbc, $starttime, $siteid, $table_style, $table_row_style, $grand_total_style) {
	if($siteid > 0) {
		$manifest_filter = "AND `ticket_manifests`.`siteid`='".$siteid."'";
	}
	$manifest_list = $dbc->query("SELECT `ticket_manifests`.`id`, `ticket_manifests`.`date`, `ticket_manifests`.`revision`, `tickets`.`ticket_label`, `ticket_attached`.`po_num`, `ticket_attached`.`qty`,`ticket_attached`.`weight`,`ticket_attached`.`weight_units`, `origin`.`vendor`, `ticket_manifests`.`siteid` FROM `ticket_manifests` LEFT JOIN `ticket_attached` ON CONCAT(',',`ticket_manifests`.`line_items`,',') LIKE CONCAT('%,',`ticket_attached`.`id`,',%') LEFT JOIN `tickets` ON `tickets`.`ticketid`=`ticket_attached`.`ticketid` LEFT JOIN `ticket_schedule` `origin` ON `tickets`.`ticketid`=`origin`.`ticketid` AND `origin`.`type`='origin' WHERE `ticket_manifests`.`deleted`=0 $manifest_filter AND `ticket_manifests`.`date` = '$starttime' ORDER BY `ticket_manifests`.`id` DESC");
	if($manifest_list->num_rows > 0) {
		$report_data .= '<div id="no-more-tables">
			<table class="table table-bordered">
				<tr class="hidden-sm hidden-xs">
					<th>FILE #</th>
					<th>PO</th>
					<th>VENDOR / SHIPPER</th>
					<th>BOX/SKID/PIECE(S)</th>
					<th>WEIGHT (LBS)</th>
					<th>JF LOCATION(S)</th>
					<th>MANIFEST</th>
				</tr>';
				while($manifest = $manifest_list->fetch_assoc()) {
					$report_data .= '<tr>
						<td data-title="FILE #">'.$manifest['ticket_label'].'</td>
						<td data-title="PO">'.$manifest['po_num'].'</td>
						<td data-title="VENDOR / SHIPPER">'.get_contact($dbc, $manifest['vendor'], 'name_company').'</td>
						<td data-title="BOX/SKID/PIECE(S)">'.$manifest['qty'].'</td>
						<td data-title="WEIGHT (LBS)">'.(strtoupper($manifest['weight_units']) == 'KGS' ? $manifest['weight'] * 2.2 : (strtoupper($manifest['weight_units']) == 'LBS' ? $manifest['weight'] : $manifest['weight'].' '.$manifest['weight_units'])).'</td>
						<td data-title="JF LOCATION(S)">'.get_contact($dbc, $manifest['siteid']).'</td>
						<td data-title="MANIFEST">'.(file_exists('../Ticket/manifest/manifest_'.$manifest['id'].($manifest['revision'] > 1 ? '_'.$manifest['revision'] : '').'.pdf') ? '<a target="_blank" href="../Ticket/manifest/manifest_'.$manifest['id'].($manifest['revision'] > 1 ? '_'.$manifest['revision'] : '').'.pdf">'.date('y',strtotime($manifest['date'])).'-'.str_pad($manifest['id'],4,0,STR_PAD_LEFT).' <img class="inline-img" src="../img/pdf.png"></a>' : date('y',strtotime($manifest['date'])).'-'.str_pad($manifest['id'],4,0,STR_PAD_LEFT)).'</td>
					</tr>';
				}
			$report_data .= '</table>
		</div>';
	} else {
		$report_data = '<h3>No Manifests Found</h3>';
	}

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