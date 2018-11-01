<?php include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
ob_clean();
$output = '<p>'.$_POST['deliver_list'].'</p><p>'.$_POST['deliver_comment'].'</p>';

class MYPDF extends TCPDF {
	public function Header() {
		$this->setCellHeightRatio(0.7);
		$this->SetFont('helvetica', '', 9);
		$footer_text = '<p style="text-align:right;">Deliverable Report as of '.date('Y-m-d').'</p>';
		$this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);
	}

	// Page footer
	public function Footer() {
		// Position at 15 mm from bottom
		$this->SetY(-15);
		$this->SetFont('helvetica', 'I', 9);
		$footer_text = '<span style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().'</span>';
		$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
	}
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

$pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->AddPage('P', 'LETTER');
$pdf->SetFont('helvetica', '', 9);

$filename = 'download/deliverables_'.$_POST['businessid'].'_'.date('Y-m-d').'.pdf';
$pdf->writeHTML($output, true, false, true, false, '');
$pdf->Output($filename, 'F'); ?>

<script>
window.location.replace('<?= $filename ?>');
</script>