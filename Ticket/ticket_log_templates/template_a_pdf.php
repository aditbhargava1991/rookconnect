<?php
include_once ('../../include.php');
include_once('../../tcpdf/tcpdf.php');
include('../../Ticket/ticket_log_templates/template_a_fields.php');

if($_GET['ticketid'] > 0) {
	$ticketid = $_GET['ticketid'];
	$ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	$ticket_members = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `src_table` = 'Members' AND `deleted` = 0"),MYSQLI_ASSOC);
	$ticket_staff_all = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `src_table` = 'Staff' AND `deleted` = 0"),MYSQLI_ASSOC);
	$pdf_url = TICKET_NOUN.' Log - '.$ticket['ticketid'].' - '.date('Y-m-d').'.pdf';
} else if($_GET['preview_template'] == 'true') {
	$ticket = [];
	$ticket_members = [[],[],[],[],[],[],[]];
	$ticket_staff_all = [[],[],[],[]];
	$pdf_url = 'preview_template.pdf';
} else {
	$ticket = [];
	$ticket_members = [[],[],[],[],[],[],[]];
	$ticket_staff_all = [[],[],[],[]];
	$pdf_url = 'blank_'.config_safe_str(TICKET_NOUN).'_log.pdf';
}
$label = get_ticket_label($dbc, $ticket);
$header_text = html_entity_decode(str_replace(['[NAME]','[DATE]','[DROPOFF]','[PICKUP]'],[(trim($label,'- ') != '' ? $label : '________________________________________________________'),($ticket['to_do_date'] != '' ? date('M. dS, Y', strtotime($ticket['to_do_date'])) : '______________'),($ticket['member_start_time'] != '' ? $ticket['member_start_time'] : '___________'),($ticket['member_end_time'] != '' ? $ticket['member_end_time'] : '___________')],$header));
$footer_text = html_entity_decode($footer);

DEFINE('TICKET_LOG_HEADER', $header_text);
DEFINE('TICKET_LOG_HEADER_LOGO', $header_logo);
DEFINE('TICKET_LOG_HEADER_LOGO_ALIGN', $header_logo_align);
DEFINE('TICKET_LOG_FOOTER', $footer_text);

class MYPDF extends TCPDF {
    public function Header() {
    	if(!empty(TICKET_LOG_HEADER_LOGO)) {
    		$image_file = '../../Ticket/download/'.TICKET_LOG_HEADER_LOGO;
            $this->Image($image_file, 10, 5, 0, 20, '', '', 'T', false, 300, TICKET_LOG_HEADER_LOGO_ALIGN, false, false, 0, false, false, false);
    	}
    	if(!empty(TICKET_LOG_HEADER)) {
			$this->setFont('helvetica', '', 9);
            $this->setCellHeightRatio(0.7);
            $this->writeHTMLCell(0, 0, 7.5 , 5, TICKET_LOG_HEADER, 0, 0, false, true, 'L', true);
    	}
    }
	public function footer() {
		if(!empty(TICKET_LOG_FOOTER)) {
			$this->setFont('helvetica', '', 9);
			$this->setCellHeightRatio(0.7);
            $this->writeHTMLCell(0, 0, '' , '', TICKET_LOG_FOOTER, 0, 0, false, true, 'L', true);
		}
	}
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);
$pdf->SetMargins(7.5, (!empty(TICKET_LOG_HEADER) || !empty(TICKET_LOG_HEADER_LOGO)) ? 40 : 10, 7.5);
$pdf->SetFooterMargin(25);
$pdf->SetAutoPageBreak(TRUE, 25);
$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);
$pdf->setCellHeightRatio(1);

$html = '';
$html .= '
	<style>
		th {
			background-color: #792e6c;
			color: white;
			font-weight: bold;
		}
		td {
			border: 0.5px solid black;
		}
	</style>';
$html .= '<br><br>';
if(strpos($fields, ',Program Notes,') !== FALSE) {
	$html .= '<p><b>Program Notes:</b> '.html_entity_decode($ticket['notes']).'</p><p>&nbsp;</p>';
}

if(strpos($fields, ',Members Table,') !== FALSE) {
	$td_width = 4;
	$html .= '<table cellpadding="3">';
	$html .= '<tr>';
	$html .= '<th width="4%">&nbsp;<p style="font-size: 2px;">&nbsp;</p></th>';
	if(strpos($fields, ',Members Last Name,') !== FALSE) {
		$td_width += 12;
		$html .= '<th width="12%">Last Name</th>';
	}
	if(strpos($fields, ',Members First Name,') !== FALSE) {
		$td_width += 12;
		$html .= '<th width="12%">First Name</th>';
	}
	if(strpos($fields, ',Members Contact Numbers,') !== FALSE) {
		$td_width += 25;
		$html .= '<th width="25%" colspan="2" style="text-align: center;">Contact Numbers</th>';
	}
	if(strpos($fields, ',Members Drop Off,') !== FALSE) {
		$td_width += 8;
		$html .= '<th width="8%">D/O</th>';
	}
	if(strpos($fields, ',Members Pick Up,') !== FALSE) {
		$td_width += 8;
		$html .= '<th width="8%">P/U</th>';
	}
	if(strpos($fields, ',Members Hours,') !== FALSE) {
		$td_width += 8;
		$html .= '<th width="8%">Hours</th>';
	}
	if(strpos($fields, ',Members Notes,') !== FALSE) {
		if(strpos($fields, ',Members Age,') !== FALSE) {
			$td_width += 5;
		}
		$html .= '<th width="'.(100 - $td_width).'%">Participant Notes</th>';
	}
	if(strpos($fields, ',Members Age,') !== FALSE) {
		$html .= '<th width="5%">Age</th>';
	}
	$html .= '</tr>';

	$members_i = 1;
	foreach($ticket_members as $ticket_member) {
		$member = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$ticket_member['item_id']."'"));
		$html .= '<tr>';
		$html .= '<td>'.$members_i.'<p style="font-size: 2px;">&nbsp;</p></td>';
		if(strpos($fields, ',Members Last Name,') !== FALSE) {
			$html .= '<td>'.decryptIt($member['last_name']).'</td>';
		}
		if(strpos($fields, ',Members First Name,') !== FALSE) {
			$html .= '<td>'.decryptIt($member['first_name']).'</td>';
		}
		if(strpos($fields, ',Members Contact Numbers,') !== FALSE) {
			$contact_numbers = [];
			if(!empty($member['home_phone'])) {
				$contact_numbers[] = decryptIt($member['home_phone']);
			}
			if(!empty($member['cell_phone'])) {
				$contact_numbers[] = decryptIt($member['cell_phone']);
			}
			$html .= '<td>'.$contact_numbers[0].'</td>';
			$html .= '<td>'.$contact_numbers[1].'</td>';
		}
		if(strpos($fields, ',Members Drop Off,') !== FALSE) {
			$html .= '<td>'.(!empty($ticket_member['checked_in']) ? $ticket_member['checked_in'] : '').'</td>';
		}
		if(strpos($fields, ',Members Pick Up,') !== FALSE) {
			$html .= '<td>'.(!empty($ticket_member['checked_out']) ? $ticket_member['checked_out'] : '').'</td>';
		}
		if(strpos($fields, ',Members Hours,') !== FALSE) {
			$html .= '<td>'.(!empty($ticket_member['hours_set']) ? $ticket_member['hours_set'] : '').'</td>';
		}
		if(strpos($fields, ',Members Notes,') !== FALSE) {
			$html .= '<td>'.$ticket_member['notes'].'</td>';
		}
		if(strpos($fields, ',Members Age,') !== FALSE) {
			$age = '';
			if(!empty($member['birth_date'])) {
				$age = date_diff(date_create($member['birth_date']), date_create('today'))->y;
			}
			$html .= '<td>'.$age.'</td>';
		}
		$html .= '</tr>';
		$members_i++;
	}
	$html .= '</table><br><br><br>';
}

if(strpos($fields, ',Staff Table,') !== FALSE) {
	$td_width = 4;
	$html .= '<table cellpadding="3">';
	$html .= '<tr>';
	$html .= '<th width="4%">&nbsp;<p style="font-size: 2px;">&nbsp;</p></th>';
	if(strpos($fields, ',Staff Name,') !== FALSE) {
		$td_width += 12;
		$html .= '<th width="12%">Staff Name</th>';
	}
	if(strpos($fields, ',Staff Duties,') !== FALSE) {
		$td_width += 12;
		$html .= '<th width="12%">Duties</th>';
	}
	if(strpos($fields, ',Staff Time In,') !== FALSE) {
		$td_width += 8;
		$html .= '<th width="8%">Time In</th>';
	}
	if(strpos($fields, ',Staff Time Out,') !== FALSE) {
		$td_width += 8;
		$html .= '<th width="8%">Time Out</th>';
	}
	if(strpos($fields, ',Staff Hours,') !== FALSE) {
		$td_width += 8;
		$html .= '<th width="8%">Hours</th>';
	}
	if(strpos($fields, ',Staff Emergency Number,') !== FALSE) {
		$td_width += 12.5;
		$html .= '<th width="12.5%">Emergency #</th>';
	}
	if(strpos($fields, ',Staff PC Initial,') !== FALSE) {
		$td_width += 10;
		$html .= '<th width="10%">PC Initial</th>';
	}
	if(strpos($fields, ',Staff Notes,') !== FALSE) {
		$html .= '<th width="'.(100 - $td_width).'%">Medical Information/<br>Special Comments</th>';
	}
	$html .= '</tr>';

	$staff_i = 1;
	foreach($ticket_staff_all as $ticket_staff) {
		$staff = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$ticket_staff['item_id']."'"));
		$html .= '<tr>';
		$html .= '<td>'.$staff_i.'<p style="font-size: 2px;">&nbsp;</p></td>';
		if(strpos($fields, ',Staff Name,') !== FALSE) {
			$html .= '<td>'.decryptIt($staff['first_name']).' '.decryptIt($staff['last_name']).'</td>';
		}
		if(strpos($fields, ',Staff Duties,') !== FALSE) {
			$html .= '<td>'.$ticket_staff['position'].'</td>';
		}
		if(strpos($fields, ',Staff Time In,') !== FALSE) {
			$html .= '<td>'.(!empty($ticket_staff['checked_in']) ? $ticket_staff['checked_in'] : '').'</td>';
		}
		if(strpos($fields, ',Staff Time Out,') !== FALSE) {
			$html .= '<td>'.(!empty($ticket_staff['checked_out']) ? $ticket_staff['checked_out'] : '').'</td>';
		}
		if(strpos($fields, ',Staff Hours,') !== FALSE) {
			$html .= '<td>'.(!empty($ticket_staff['hours_set']) ? $ticket_staff['hours_set'] : '').'</td>';
		}
		if(strpos($fields, ',Staff Emergency Number,') !== FALSE) {
			$emergency_number = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_medical` WHERE `contactid` = '".$ticket_staff['item_id']."'"));
			$emergency_numbers = [];
			$emergency_numbers[] = $emergency_number['pri_emergency_cell_phone'];
			$emergency_numbers[] = $emergency_number['pri_emergency_home_phone'];
			$emergency_numbers = implode('<br>',$emergency_numbers);

			$html .= '<td>'.$emergency_numbers.'</td>';
		}
		if(strpos($fields, ',Staff PC Initial,') !== FALSE) {
			$html .= '<td></td>';
		}
		if(strpos($fields, ',Staff Notes,') !== FALSE) {
			$html .= '<td>'.$ticket_staff['notes'].'</td>';
		}
		$html .= '</tr>';
		$staff_i++;
	}
	$html .= '</table>';
}

$pdf->writeHTML(utf8_encode($html), true, false, true, false, '');

if(!file_exists('download')) {
    mkdir('download', 0777, true);
}
$pdf->Output('download/'.$pdf_url, 'F');

echo '<script type="text/javascript"> window.location.href = "download/'.$pdf_url.'"; </script>';
?>
