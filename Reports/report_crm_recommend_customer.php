<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {

    $search_contact = $_POST['search_contact'];
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
            $footer_text = 'CRM Recommendation Scale';
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

    $start_date = date('Y-m-d', strtotime($starttimepdf));
    $end_date = date('Y-m-d', strtotime($endtimepdf));
    $html = '';

	$pdf->AddPage('L', 'LETTER');
	$pdf->SetFont('helvetica', '', 9);
	$html = '';

	$html .= "<h3>".$date."</h3>";
	$html .= '<br><br>' . report_crm_recommend($dbc, $search_contact, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

	$pdf->writeHTML($html, true, false, true, false, '');


    $today_date = date('Y-m-d');
	//$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/crm_recommend_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_crm_recommend_customer', 0, WEBSITE_URL.'/Reports/Download/crm_recommend_'.$today_date.'.pdf', 'CRM Recommendation Scale Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/crm_recommend_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    } ?>
        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php
            if (isset($_POST['search_form'])) {
                $search_contact = $_POST['search_contact'];
            }
            ?>
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">For:</label>
					<div class="col-sm-8">
						<select data-placeholder="Select a contact" name="search_contact" class="chosen-select-deselect"><option></option>
							<?php $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `contactid` IN (SELECT `contactid` FROM `crm_recommend`)"),MYSQLI_ASSOC));
							foreach($contact_list as $id) {
								echo "<option ".($id == $search_contact ? 'selected' : '')." value='$id'>".get_contact($dbc, $id)."</option>";
							} ?>
						</select>
					</div>
				</div>
            <button type="submit" name="search_form" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                if($search_contact > 0) {
					echo '<div id="no-more-tables">';
					echo report_crm_recommend($dbc, $search_contact, '', '', '');
					echo "</div>";
				} else {
					echo "<h3>Please select a contact to search by.</h3>";
				}
            ?>

        </form>
<?php
function report_crm_recommend($dbc, $contactid, $table_style, $table_row_style, $grand_total_style) {
	$result = mysqli_query($dbc, "SELECT * FROM crm_recommend WHERE `contactid` = '$contactid' ORDER BY `completed_date` DESC");
	$report_data = "<h3>Recommendation Responses for ".get_contact($dbc, $contactid)."</h3>";
    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';

    $report_data .= '<tr class="hidden-sm hidden-xs" style="'.$table_row_style.'">
    <th width="40%">Name</th>
    <th width="30%">Date Completed</th>
    <th width="30%">Recommend Scale (0-10)</th>
    </tr>';

	while($row = mysqli_fetch_array( $result ))
	{
		$report_data .= "<tr>";
		$report_data .= '<td data-title="Name">' . get_contact($dbc, $row['contactid']) . '</td>';
		$report_data .= '<td data-title="Date Completed"><a href="recommend_request.php?s=' . $row['recommend_id']. '" target="_blank">' . $row['completed_date']. '</a></td>';
		$report_data .= '<td data-title="Recommend Scale (0-10)">' . $row['recommend_response'] . '</td>';
		$report_data .= "</tr>";
	}

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