<?php include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
checkAuthorised('equipment');
if(!empty($_GET['checklistid'])) {
	ob_clean();
	$checklistid = $_GET['checklistid'];
    $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM checklist WHERE checklistid='$checklistid' AND `deleted`=0"));
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_checklist"));

    $security = $get_contact['security'];
    $checklist_type = $get_contact['checklist_type'];
    $checklist_name = $get_contact['checklist_name'];

    DEFINE('CHECKLIST_NAME', $checklist_name);
    DEFINE('HEADER_TEXT', $get_config['pdf_header']);
    DEFINE('HEADER_LOGO', $get_config['pdf_logo']);
	
    class MYPDF extends TCPDF {
        public function Header() {
			
			$this->SetFont('helvetica', '', 10);
			$this->MultiCell(180, 0, html_entity_decode(HEADER_TEXT), 0, 'C', 0, 0, 15, 5, true, 0, true);
			$custom_height = $this->getStringHeight(180, HEADER_TEXT) + 10;
			
			$this->SetFont('helvetica', '', 30);
			$this->SetTextColor(255,255,255);
			$this->SetFillColor(81, 99, 113);
			$width = 180;
			$left = 15;
			if(HEADER_LOGO != '' && file_exists('download/'.HEADER_LOGO)) {
				$this->Image('download/'.HEADER_LOGO, 17, $custom_height + 2, 26, 0);
				$width -= 30;
				list($img_width, $img_height, $type, $attr) = getimagesize("download/".HEADER_LOGO);
				$img_height /= ($img_width / 30);
				$string_height = $this->getStringHeight($width, CHECKLIST_NAME);
				$this->MultiCell(30, ($img_height > $string_height ? $img_height : $string_height), '', 0, 'C', 1, 0, 15, $custom_height);
			}
			$this->MultiCell($width, ($img_height > $string_height ? $img_height : $string_height), CHECKLIST_NAME, 0, 'C', 1, 0, 180 - $width + 15, $custom_height);
        }

        // Page footer
        public function Footer() {
            // Position at 15 mm from bottom
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
            $this->Cell(90, 0, 'Date Printed: '.date('Y-m-d'), 0, 0, 'L');
            $this->Cell(90, 0, $footer_text, 0, 0, 'R');
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	
	$width = 180;
	if(HEADER_LOGO != '') {
		$width -= 30;
	}
	$pdf->SetFont('helvetica', '', 10);
	$custom_height = $pdf->getStringHeight(180, HEADER_TEXT) + 10;
	$pdf->SetFont('helvetica', '', 30);
	list($img_width, $img_height, $type, $attr) = getimagesize("download/".HEADER_LOGO);
	$img_height /= ($img_width / 30);
	$string_height = $pdf->getStringHeight((HEADER_LOGO != '' ? 150 : 180), CHECKLIST_NAME);
	$custom_height += ($img_height > $string_height ? $img_height : $string_height) + 5;

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    $pdf->SetMargins(PDF_MARGIN_LEFT, $custom_height, PDF_MARGIN_RIGHT);

    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    $pdf->AddPage();
    $pdf->SetFont('helvetica', '', 14);
    $pdf->SetTextColor(53,175,199);

    $html = '<table cellpadding="4">';

    $query_check_credentials = "SELECT * FROM checklist_name WHERE checklistid='$checklistid' AND `deleted`=0 ORDER BY checked, priority";
    $result = mysqli_query($dbc, $query_check_credentials);
    $num_rows = mysqli_num_rows($result);
    while($row = mysqli_fetch_array($result)) {
        $html .= '<tr '.($row['flag-colour'] != '' ? 'style="background-color: #'.$row['flag-colour'].';"' : '').'>
                        <td width="5%">';
        $checked = '';
        if($row['checked'] == 1) {
            $html .= '<img src="../img/checkmark.png" width="15px">&nbsp;&nbsp;';
        } else {
            $html .= '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
        }
        $html .= '</td><td width="95%">'.$row['checklist'].'</td></tr>';
    }

    $html .= '</table>';

    $pdf->writeHTML($html, true, false, true, false, '');

    $pdf->Output('download/Checklist_'.$checklistid.'.pdf', 'F');

    if($security == 'My Checklist') {
        $url = 'my_checklist.php?type='.$checklist_type.'&checklistid='.$checklistid;
    } else {
        $url = 'company_checklist.php?type='.$checklist_type.'&checklistid='.$checklistid;
    }

	echo '<script type="text/javascript">window.location.replace("download/Checklist_'.$checklistid.'.pdf");</script>';
} else {
	echo "<h2>No Checklist Selected</h2>
	<script>//setTimeout(function() { window.location.replace('".WEBSITE_URL."/home.php'); }, 3000);</script>";
}