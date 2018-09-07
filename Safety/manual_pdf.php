<?php
$get_manual = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM safety WHERE safetyid='$safetyid'"));

$heading_number = $get_manual['heading_number'];
$sub_heading_number = $get_manual['sub_heading_number'];
$category = $get_manual['category'];
$heading = $get_manual['heading'];
$sub_heading = $get_manual['sub_heading'];
$description = $get_manual['description'];
$third_heading_number = $get_manual['third_heading_number'];
$third_heading = $get_manual['third_heading'];
$manual_type = $get_manual['category'];

define('HEADER_TEXT', html_entity_decode(get_config($dbc, "manual_header")));
define('FOOTER_TEXT', html_entity_decode(get_config($dbc, "manual_footer")));

$pdf_html = '<form action="" method="POST" enctype="multipart/form-data"><h1>'.$manual_type.'</h1>';
$pdf_name = '';
if ($third_heading != '') {
	$pdf_html .= "<h3>$third_heading_number - $third_heading</h3>";
	$pdf_name = config_safe_str($third_heading_number.'-'.$third_heading);
} else if ($sub_heading != '') {
	$pdf_html .= "<h3>$sub_heading_number - $sub_heading</h3>";
	$pdf_name = config_safe_str($sub_heading_number.'-'.$sub_heading);
} else if ($heading != '') {
	$pdf_html .= "<h3>$heading_number - $heading</h3>";
	$pdf_name = config_safe_str($heading_number.'-'.$heading);
}
$pdf_name .= '_'.$_SESSION['contactid'];

$pdf_html .= html_entity_decode($description);

$links = mysqli_query($dbc, "SELECT upload, uploadid FROM manuals_upload WHERE type='document' AND manualtypeid='$manualtypeid' AND `upload` != ''");
if(mysqli_num_rows($links) > 0) {
	$pdf_html .= "<h3>Documents</h3>\n<ul>";
	while($doc_link = mysqli_fetch_array($links)) {
		$pdf_html .= '<li><a href="'.WEBSITE_URL.'/HR/download/'.$doc_link['upload'].'" target="_blank">'.$doc_link['upload'].'</a></li>';
	}
	$pdf_html .= "</ul>";
}
$links = mysqli_query($dbc, "SELECT upload, uploadid FROM manuals_upload WHERE type='link' AND manualtypeid='$manualtypeid' AND `upload` != ''");
if(mysqli_num_rows($links) > 0) {
	$pdf_html .= "<h3>Links</h3>\n<ul>";
	while($url_link = mysqli_fetch_array($links)) {
		$pdf_html .= '<li><a href="'.$url_link['upload'].'" target="_blank">'.$url_link['upload'].'</a></li>';
	}
	$pdf_html .= "</ul>";
}
$links = mysqli_query($dbc, "SELECT upload, uploadid FROM manuals_upload WHERE type='video' AND manualtypeid='$manualtypeid' AND `upload` != ''");
if(mysqli_num_rows($links) > 0) {
	$pdf_html .= "<h3>Videos</h3>\n<ul>";
	while($video_link = mysqli_fetch_array($links)) {
		$pdf_html .= '<li><a href="'.WEBSITE_URL.'/HR/download/'.$video_link['upload'].'" target="_blank">'.$video_link['upload'].'</a></li>';
	}
	$pdf_html .= "</ul>";
}

if (!empty($comment)) {
	$pdf_html .= "<h3>Comments</h3>";
	$pdf_html .= '<table width="100%"><tr><td>'.html_entity_decode($comment).'</td></tr><tr><td>/td></tr></table><div style="clear:both;"></div><br />&nbsp;';
}
$pdf_html .= "<h3>Signed</h3>";
if(!empty($safetyid) && file_exists('download/sign_'.$safetyid.'_'.$_SESSION['contactid'].'.png')) {
	$pdf_html .= '<table width="100%" cellspacing="5"><tr><td width="40%" style="border-bottom: 1px solid black">';
	$pdf_html .= (file_exists('download/sign_'.$safetyid.'_'.$_SESSION['contactid'].'.png') ? '<img src="download/sign_'.$safetyid.'_'.$_SESSION['contactid'].'.png" height="30" border="0" alt="">' : '').'</td>';
	$pdf_html .= '<td width="40%" style="border-bottom: 1px solid black"><br /><br />'.get_contact($dbc, $_SESSION['contactid']).'</td><td width="20%" style="border-bottom: 1px solid black"><br /><br />'.date('Y-m-d').'</td></tr>';
	$pdf_html .= '<tr><td>(Sign Here)</td><td>Print Name</td><td>Today\'s Date</td></tr></table>';
} else {
	$pdf_html .= '<table width="100%" cellspacing="5"><tr><td width="40%" style="border-bottom: 1px solid black"><br /><br /><br /></td>';
	$pdf_html .= '<td width="40%" style="border-bottom: 1px solid black"></td><td width="20%" style="border-bottom: 1px solid black"></td></tr>';
	$pdf_html .= '<tr><td>(Sign Here)</td><td>Print Name</td><td>Today\'s Date</td></tr></table>';
}
$pdf_html .= "</form>";

include_once('../tcpdf/tcpdf.php');
class MYPDF extends TCPDF {
	public function Header() {
		$this->SetFont('helvetica', '', 8);
		$this->writeHTMLCell(0, 0, '', '', HEADER_TEXT, 0, 0, false, "L", "R",true);
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		$this->SetFont('helvetica', 'I', 8);
		$footer_text = FOOTER_TEXT.'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
		$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
	}
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
$pdf->setFooterData(array(0,64,0), array(0,64,128));
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);
$pdf->setY(10);
$pdf->writeHTML(HEADER_TEXT);
$margin_top = $pdf->GetY();
$pdf->DeletePage(1);
$pdf->SetMargins(PDF_MARGIN_LEFT, $margin_top, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf_path = 'download/'.$pdf_name.'_'.date('Y-m-d').'.pdf';
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 10);
$pdf->writeHTML($pdf_html, true, false, true, false, '');
$pdf->Output($pdf_path, 'F');
//track_download($dbc, 'manuals', $manualtypeid, WEBSITE_URL.'/HR/'.$pdf_path, 'PDF of Manual generated by Rook Connect.');
if(empty($manual_id)) { ?>
	<script> window.location.replace('<?= $pdf_path ?>'); </script>
<?php } ?>