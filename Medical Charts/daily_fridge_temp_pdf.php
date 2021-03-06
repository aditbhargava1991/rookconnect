<?php
/*
 * Water Temp Chart Export PDF
 * Called from edit_addition_water_temp_chart.php
 */
error_reporting(0);
include('../include.php');
include('../tcpdf/tcpdf.php');
$charts_time_format = get_config($dbc, 'charts_time_format');


$contactid = $_GET['contactid'];
$date = $_GET['date'];

$value_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `daily_fridge_temp` FROM `field_config`"));
$value_config = ','.$value_config['daily_fridge_temp'].',';

//PDF Settings
$pdf_settings = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `medical_charts_pdf_setting`"));

$pdf_logo = !empty($pdf_settings['pdf_logo']) ? $pdf_settings['pdf_logo'] : '';

$header_text = !empty($pdf_settings['header_text']) ? $pdf_settings['header_text'] : '';
$header_align = !empty($pdf_settings['header_align']) ? $pdf_settings['header_align'] : 'R';
$header_font = !empty($pdf_settings['header_font']) ? $pdf_settings['header_font'] : 'helvetica';
$header_size = !empty($pdf_settings['header_size']) ? $pdf_settings['header_size'] : 9;
$header_color = !empty($pdf_settings['header_color']) ? $pdf_settings['header_color'] : '#000000';

$footer_text = !empty($pdf_settings['footer_text']) ? $pdf_settings['footer_text'] : '';
$footer_align = !empty($pdf_settings['footer_align']) ? $pdf_settings['footer_align'] : 'C';
$footer_font = !empty($pdf_settings['footer_font']) ? $pdf_settings['footer_font'] : 'helvetica';
$footer_size = !empty($pdf_settings['footer_size']) ? $pdf_settings['footer_size'] : 9;
$footer_color = !empty($pdf_settings['footer_color']) ? $pdf_settings['footer_color'] : '#000000';

$body_font = !empty($pdf_settings['body_font']) ? $pdf_settings['body_font'] : 'helvetica';
$body_size = !empty($pdf_settings['body_size']) ? $pdf_settings['body_size'] : 9;
$body_color = !empty($pdf_settings['body_color']) ? $pdf_settings['body_color'] : '#000000';

DEFINE(MC_PDF_LOGO, $pdf_logo);
DEFINE(MC_HEADER_TEXT, html_entity_decode($header_text));
DEFINE(MC_HEADER_ALIGN, $header_align);
DEFINE(MC_HEADER_FONT, $header_font);
DEFINE(MC_HEADER_SIZE, $header_size);
DEFINE(MC_HEADER_COLOR, $header_color);
DEFINE(MC_FOOTER_TEXT, html_entity_decode($footer_text));
DEFINE(MC_FOOTER_ALIGN, $footer_align);
DEFINE(MC_FOOTER_FONT, $footer_font);
DEFINE(MC_FOOTER_SIZE, $footer_size);
DEFINE(MC_FOOTER_COLOR, $footer_color);

class MYPDF extends TCPDF {

	//Page header
	public function Header() {
        $logo_align = (MC_HEADER_ALIGN == "L" ? "R" : "L");
        $header_align = MC_HEADER_ALIGN;
        switch($header_align) {
        	case 'L':
        		$align_style = 'text-align: left;';
        		break;
        	case 'C':
        		$align_style = 'text-align: center;';
        		break;
        	case 'R':
        		$align_style = 'text-align: right;';
        }
		$font_style = 'font-family: '.MC_HEADER_FONT.'; font-size: '.MC_HEADER_SIZE.'; color: '.MC_HEADER_COLOR.'; '.$align_style;
		$this->setFont('helvetica', '', 9);
		if(MC_PDF_LOGO != '') {
			$image_file = '../Medical Charts/download/'.MC_PDF_LOGO;
            $this->Image($image_file, 10, 5, 0, 25, '', '', 'T', false, 300, $logo_align, false, false, 0, false, false, false);
		}

		if(MC_HEADER_TEXT != '') {
            $this->setCellHeightRatio(0.7);
			$header_text = '<p style="'.$font_style.'">'.MC_HEADER_TEXT.'</p>';
            $this->writeHTMLCell(0, 0, 5 , 5, $header_text, 0, 0, false, true, $header_align, true);
		}
	}

	//Page footer
	public function Footer() {
        $page_align = (MC_FOOTER_ALIGN == "R" ? "L" : "R");
        $footer_align = MC_FOOTER_ALIGN;
        switch($footer_align) {
        	case 'L':
        		$align_style = 'text-align: left;';
	            $page_align_style = 'text-align: right;';
        		break;
        	case 'C':
        		$align_style = 'text-align: center;';
	            $page_align_style = 'text-align: right;';
        		break;
        	case 'R':
        		$align_style = 'text-align: right;';
	            $page_align_style = 'text-align: left;';
        }
		$font_style = 'font-family: '.MC_FOOTER_FONT.'; font-size: '.MC_FOOTER_SIZE.'; color: '.MC_FOOTER_COLOR.'; '.$align_style;

        // Position at 15 mm from bottom
        $this->SetY(-10);
        $this->SetFont('times', '', 8);
        $footer_text = '<p style="'.$page_align_style.'">'.$this->getAliasNumPage().'</p>';
        $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, true, $page_align, true);

		if(MC_FOOTER_TEXT != '') {
            $this->SetY(-20);
            $this->setCellHeightRatio(0.7);
			$footer_text = '<p style="'.$font_style.'">'.MC_FOOTER_TEXT.'</p>';
            $this->writeHTMLCell(0, 0, '' , '', $footer_text, 0, 0, false, true, $footer_align, true);
		}
	}
}

$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetMargins(PDF_MARGIN_LEFT, (!empty(MC_PDF_LOGO) ? 30 : 5), PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);
$pdf->setCellHeightRatio(1);

$html = '';
$html .= '<div style="font-family: '.$body_font.'; font-size: '.$body_size.'; color: '.$body_color.';">';
$html .= '<p style="text-align: center">';
$html .= '<h1>Daily Fridge Temp Chart - '.get_client($dbc, $contactid).' - '.date('F Y', strtotime($date)).'</h1>';
$html .= '</p>';

$date_start = date('Y-m-01', strtotime($date));
$date_end = date('Y-m-t', strtotime($date));
$fridge_record_query = "SELECT * FROM `daily_fridge_temp` WHERE `business`='$contactid' AND `date` BETWEEN '$date_start' AND '$date_end' AND `deleted`=0 ORDER BY `date`, IFNULL(STR_TO_DATE(`time`, '%l:%i %p'),STR_TO_DATE(`time`, '%H:%i')) ASC";
$fridge_record_result = mysqli_fetch_all(mysqli_query($dbc, $fridge_record_query),MYSQLI_ASSOC);

$html .= '<table border="1" cellpadding="2">';
$html .= '<tr>';
$html .= '<th><b>Date</b></th>';
if (strpos($value_config, ','."time".',') !== FALSE) {
	$html .= '<th><b>Time</b></th>';
}
if (strpos($value_config, ','."fridge".',') !== FALSE) {
	$html .= '<th><b>Fridge</b></th>';
}
if (strpos($value_config, ','."temp".',') !== FALSE) {
	$html .= '<th><b>Temperature</b></th>';
}
if (strpos($value_config, ','."note".',') !== FALSE) {
	$html .= '<th><b>Note</b></th>';
}
if (strpos($value_config, ','."staff".',') !== FALSE) {
	$html .= '<th><b>Staff</b></th>';
}
if (strpos($value_config, ','."history".',') !== FALSE) {
	$html .= '<th><b>History</b></th>';
}
$html .= '</tr>';

foreach ($fridge_record_result as $chart) {
	$daily_fridge_temp_id = $chart['daily_fridge_temp_id'];
	$fridge_date = $chart['date'];
	$time = $chart['time'];
	if($charts_time_format == '24h') {
		$time = date('H:i', strtotime(date('Y-m-d').' '.$time));
	} else {
		$time = date('h:i a', strtotime(date('Y-m-d').' '.$time));
	}
	$fridge = $chart['fridge'];
	$temp = $chart['temp'];
	$note = $chart['note'];
	$staff = $chart['staff'];
	$history = $chart['history'];

	$html .= '<tr>';
	$html .= '<td>'.$fridge_date.'</td>';
	if (strpos($value_config, ','."time".',') !== FALSE) {
		$html .= '<td>'.$time.'</td>';
	}
	if (strpos($value_config, ','."fridge".',') !== FALSE) {
		$html .= '<td>'.$fridge.'</td>';
	}
	if (strpos($value_config, ','."temp".',') !== FALSE) {
		$html .= '<td>'.$temp.'</td>';
	}
	if (strpos($value_config, ','."note".',') !== FALSE) {
		$html .= '<td>'.html_entity_decode($note).'</td>';
	}
	if (strpos($value_config, ','."staff".',') !== FALSE) {
		$html .= '<td>'.get_contact($dbc, $staff).'</td>';
	}
	if (strpos($value_config, ','."history".',') !== FALSE) {
		$html .= '<td>'.html_entity_decode($history).'</td>';
	}
	$html .= '</tr>';
}
$html .= '</table>';
$html .= '</div>';

$pdf->writeHTML(utf8_encode($html), true, false, true, false, '');

if(!file_exists('download')) {
	mkdir('download', 0777, true);
}

$today_date = date('Y-m-d_H-i-a', time());
$file_name = 'daily_fridge_temp_'.$contactid.'_'.$today_date.'.pdf';

$pdf->Output('download/'.$file_name, 'F');
echo '<script type="text/javascript">window.location.replace("download/'.$file_name.'", "_blank");</script>';
?>