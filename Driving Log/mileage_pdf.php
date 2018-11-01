<?php include_once('../include.php');
checkAuthorised('driving_log');
include('../tcpdf/tcpdf.php');
//PDF Settings
$pdf_settings = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `driving_log_mileage_pdf_setting`"));

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

DEFINE(MILEAGE_PDF_LOGO, $pdf_logo);
DEFINE(MILEAGE_HEADER_TEXT, html_entity_decode($header_text));
DEFINE(MILEAGE_HEADER_ALIGN, $header_align);
DEFINE(MILEAGE_HEADER_FONT, $header_font);
DEFINE(MILEAGE_HEADER_SIZE, $header_size);
DEFINE(MILEAGE_HEADER_COLOR, $header_color);
DEFINE(MILEAGE_FOOTER_TEXT, html_entity_decode($footer_text));
DEFINE(MILEAGE_FOOTER_ALIGN, $footer_align);
DEFINE(MILEAGE_FOOTER_FONT, $footer_font);
DEFINE(MILEAGE_FOOTER_SIZE, $footer_size);
DEFINE(MILEAGE_FOOTER_COLOR, $footer_color);

class MYPDF extends TCPDF {

	//Page header
	public function Header() {
        $logo_align = (MILEAGE_HEADER_ALIGN == "L" ? "R" : "L");
        $header_align = MILEAGE_HEADER_ALIGN;
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
		$font_style = 'font-family: '.MILEAGE_HEADER_FONT.'; font-size: '.MILEAGE_HEADER_SIZE.'; color: '.MILEAGE_HEADER_COLOR.'; '.$align_style;
		$this->setFont('helvetica', '', 9);
		if(MILEAGE_PDF_LOGO != '') {
			$image_file = '../Driving Log/download/'.MILEAGE_PDF_LOGO;
            $this->Image($image_file, 10, 5, 0, 25, '', '', 'T', false, 300, $logo_align, false, false, 0, false, false, false);
		}

		if(MILEAGE_HEADER_TEXT != '') {
            $this->setCellHeightRatio(0.7);
			$header_text = '<p style="'.$font_style.'">'.MILEAGE_HEADER_TEXT.'</p>';
            $this->writeHTMLCell(0, 0, 5 , 5, $header_text, 0, 0, false, true, $header_align, true);
		}
	}

	//Page footer
	public function Footer() {
        $page_align = (MILEAGE_FOOTER_ALIGN == "R" ? "L" : "R");
        $footer_align = MILEAGE_FOOTER_ALIGN;
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
		$font_style = 'font-family: '.MILEAGE_FOOTER_FONT.'; font-size: '.MILEAGE_FOOTER_SIZE.'; color: '.MILEAGE_FOOTER_COLOR.'; '.$align_style;

        // Position at 15 mm from bottom
        $this->SetY(-10);
        $this->SetFont('times', '', 8);
        $footer_text = '<p style="'.$page_align_style.'">'.$this->getAliasNumPage().'</p>';
        $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, true, $page_align, true);

		if(MILEAGE_FOOTER_TEXT != '') {
            $this->SetY(-20);
            $this->setCellHeightRatio(0.7);
			$footer_text = '<p style="'.$font_style.'">'.MILEAGE_FOOTER_TEXT.'</p>';
            $this->writeHTMLCell(0, 0, '' , '', $footer_text, 0, 0, false, true, $footer_align, true);
		}
	}
}

$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
$pdf->SetMargins(PDF_MARGIN_LEFT, (!empty(MILEAGE_PDF_LOGO) ? 30 : 5), PDF_MARGIN_RIGHT);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 8);
$pdf->setCellHeightRatio(1);

$today_date = date('Y-m-d');

$html = '';
$html .= '<div style="font-family: '.$body_font.'; font-size: '.$body_size.'; color: '.$body_color.';">';
$html .= '<p style="text-align: center">';
$html .= '<h1>Mileage for '.get_contact($dbc, $search_staff).' - '.$today_date.'</h1>';
$html .= '</p>';

$html .= '<table border="1" cellpadding="2">';
$html .= '<tr style="font-weight: bold;">';
if(in_array('staff',$config)) {
	$html .= '<th>Driver</th>';
}
if(in_array('startdate',$config)) {
	$html .= '<th>Start</th>';
}
if(in_array('enddate',$config)) {
	$html .= '<th>End</th>';
}
$html .= '<th>Mileage</th>';
if(in_array('category',$config)) {
	$html .= '<th>Category</th>';
}
if(in_array('details',$config)) {
	$html .= '<th>Details</th>';
}
if(in_array('contact',$config)) {
	$html .= '<th>Client</th>';
}
if(in_array('double_mileage',$config)) {
	$html .= '<th>KMx2</th>';
}
if(in_array('tickets',$config)) {
	$html .= '<th>'.TICKET_TILE.'</th>';
}
if(in_array('projects',$config)) {
	$html .= '<th>'.PROJECT_TILE.'</th>';
}
if(in_array('tasks',$config)) {
	$html .= '<th>'.TASK_TILE.'</th>';
}
if(in_array('equipment',$config)) {
	$html .= '<th>Equipment</th>';
}
if(in_array('checklist',$config)) {
	$html .= '<th>Checklists</th>';
}
if(in_array('expense',$config)) {
	$html .= '<th>Expense</th>';
}
if(in_array('meetings',$config)) {
	$html .= '<th>Meeting</th>';
}
$html .= '</tr>';

$mile_log = mysqli_query($dbc, "SELECT * FROM `mileage` WHERE `deleted`=0 AND `staffid`='$search_staff' AND '$search_contact' IN (`contactid`,'') AND '$search_project' IN (`projectid`,'') AND `category` LIKE '%$search_cat%' AND (`start` BETWEEN '$search_start' AND '$search_end' OR `end` BETWEEN '$search_start' AND '$search_end' OR IFNULL(`start`,'0000-00-00 00:00:00')='0000-00-00 00:00:00' AND IFNULL(`end`,'0000-00-00 00:00:00')='0000-00-00 00:00:00')");
while($mileage = mysqli_fetch_assoc($mile_log)) {
	$html .= '<tr>';
	if(in_array('staff',$config)) {
		$html .= '<td>'.get_contact($dbc, $mileage['staffid']).'</td>';
	}
	if(in_array('startdate',$config)) {
		$html .= '<td>'.$mileage['start'].'</td>';
	}
	if(in_array('enddate',$config)) {
		$html .= '<td>'.$mileage['end'].'</td>';
	}
	$html .= '<td>'.$mileage['mileage'].'</td>';
	if(in_array('category',$config)) {
		$html .= '<td>'.$mileage['category'].'</td>';
	}
	if(in_array('details',$config)) {
		$html .= '<td>'.$mileage['details'].'</td>';
	}
	if(in_array('contact',$config)) {
		$html .= '<td>'.(!empty(get_client($dbc, $mileage['contactid'])) ? get_client($dbc, $mileage['contactid']) : get_contact($dbc, $mileage['contactid'])).'</td>';
	}
	if(in_array('double_mileage',$config)) {
		$html .= '<td>'.$mileage['double_mileage'].'</td>';
	}
	if(in_array('tickets',$config)) {
		$ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$mileage['ticketid']."'"));
		$html .= '<td>'.get_ticket_label($dbc, $ticket).'</td>';
	}
	if(in_array('projects',$config)) {
		$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$mileage['projectid']."'"));
		$html .= '<td>'.get_project_label($dbc, $project).'</td>';
	}
	if(in_array('tasks',$config)) {
		$task = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `tasklistid` = '".$mileage['taskid']."'"));
		$html .= '<td>'.$task['heading'].'</td>';
	}
	if(in_array('equipment',$config)) {
		$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`make`,''),' ',IFNULL(`model`,''),' ',IFNULL(`label`,''),' ',IFNULL(`unit_number`,'')) label FROM `equipment` WHERE `equipmentid` = '".$mileage['equipmentid']."'"));
		$html .= '<td>'.$equipment['label'].'</td>';
	}
	if(in_array('checklist',$config)) {
		$checklist = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '".$mileage['checklistid']."'"));
		$html .= '<td>'.$checklist['checklist_name'].'</td>';
	}
	if(in_array('expense',$config)) {
		$expense = mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(`title`,' ',`ex_date`) label FROM `expense` WHERE `expenseid` = '".$mileage['expenseid']."'"));
		$html .= '<td>'.$expense['label'].'</td>';
	}
	if(in_array('meetings',$config)) {
		$meeting = mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(`meeting_topic`,' - ',`date_of_meeting`) label FROM `agenda_meeting` WHERE `agendameetingid` = '".$mileage['meetingid']."'"));
		$html .= '<td>'.$meeting['label'].'</td>';
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
$file_name = 'mileage_pdf_'.$today_date.'.pdf';

$pdf->Output('download/'.$file_name, 'F');
echo '<script type="text/javascript">window.open("download/'.$file_name.'", "_blank");</script>';
?>