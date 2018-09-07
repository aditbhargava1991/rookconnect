<?php include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
include_once('../Ticket/field_list.php');
if(!file_exists('download')) {
	mkdir('download', 0777, true);
}
$hide_blank_fields = false;
if(get_config($dbc, 'ticket_pdf_hide_blank') == 1 && $_GET['ticketid'] > 0) {
	$hide_blank_fields = true;
}
$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
$filename = "download/output_".($ticketid > 0 ? $ticketid : 'new_'.config_safe_str(TICKET_NOUN))."_".date('Y_m_d').".pdf";
$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));
if(!empty($get_ticket) && $get_ticket['ticketid'] >0) {	
	foreach($get_ticket as $field_id => $value) {
		if($value == '0000-00-00' || $value == '0') {
			$get_ticket[$field_id] = '';
		}
	}

	$ticket_type = $get_ticket['ticket_type'];
	$businessid = $get_ticket['businessid'] ?: $businessid;
	$equipmentid = $get_ticket['equipmentid'];

	$clientid = $get_ticket['clientid'] ?: $clientid;
	if($businessid == '') {
		$businessid = get_contact($dbc, $clientid, 'businessid');
	}

	$projectid = $get_ticket['projectid'];
	$client_projectid = $get_ticket['client_projectid'];
	$piece_work = $get_ticket['piece_work'];
	//$projecttype = get_project($dbc, $projectid, 'projecttype');
	$service_type = $get_ticket['service_type'];
	$service = $get_ticket['service'];
	$sub_heading = $get_ticket['sub_heading'];
	$heading = $get_ticket['heading'];
	$heading_auto = $get_ticket['heading_auto'];
	$category = $get_ticket['category'];
	$assign_work = $get_ticket['assign_work'];
    $details_where = $get_ticket['details_where'];
    $details_who = $get_ticket['details_who'];
    $details_why = $get_ticket['details_why'];
    $details_what = $get_ticket['details_what'];
    $details_position = $get_ticket['details_position'];
	$project_path = '';
	if(!empty($projectid)) {
		$project_path = get_project($dbc, $projectid, 'project_path');
	} else if(!empty($client_projectid)) {
		$project_path = get_client_project($dbc, $client_projectid, 'project_path');
	}

	$projecttype = get_project($dbc, $projectid, 'projecttype');
	$milestone_timeline = html_entity_decode($get_ticket['milestone_timeline']);

	$created_date = date('Y-m-d');
	$login_id = $_SESSION['contactid'];
	// AND timer_type='Break' AND end_time IS NULL

	$get_ticket_timer = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT start_timer_time, timer_type FROM ticket_timer WHERE tickettimerid IN (SELECT MAX(`tickettimerid`) FROM `ticket_timer` WHERE `ticketid`='$ticketid' AND created_by='$login_id' AND `deleted` = 0)"));

	$created_date = $get_ticket['created_date'];
	$created_by = $get_ticket['created_by'];

	$start_time = $get_ticket_timer['start_timer_time'];
	$timer_type = $get_ticket_timer['timer_type'];

	if($start_time == '0' || $start_time == '') {
		$time_seconds = 0;
	} else {
		$time_seconds = (time()-$start_time);
	}

	$to_do_date = $get_ticket['to_do_date'];
	$internal_qa_date = $get_ticket['internal_qa_date'];
	$deliverable_date = $get_ticket['deliverable_date'];

	$to_do_end_date = $get_ticket['to_do_end_date'];
	$internal_qa_contactid = $get_ticket['internal_qa_contactid'];
	$deliverable_contactid = $get_ticket['deliverable_contactid'];

	$to_do_start_time = $get_ticket['to_do_start_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['to_do_start_time']));
	$to_do_end_time = $get_ticket['to_do_end_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['to_do_end_time']));
	$internal_qa_start_time = $get_ticket['internal_qa_start_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['internal_qa_start_time']));
	$internal_qa_end_time = $get_ticket['internal_qa_end_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['internal_qa_end_time']));
	$deliverable_start_time = $get_ticket['deliverable_start_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['deliverable_start_time']));
	$deliverable_end_time = $get_ticket['deliverable_end_time'] == '' ? '' : date('h:i a', strtotime($get_ticket['deliverable_end_time']));

	$status = $get_ticket['status'];
	$max_time = explode(':', $get_ticket['max_time']);
	$max_qa_time = explode(':', $get_ticket['max_qa_time']);
	$spent_time = $get_ticket['spent_time'];
	$total_days = $get_ticket['total_days'];
	$contactid = $get_ticket['contactid'];
}
$access_view_project_info = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_project_info');
$access_view_project_details = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_project_details');
$access_view_staff = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_staff');
$access_view_summary = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_summary');
$access_view_complete = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_complete');
$access_view_notifications = check_subtab_persmission($dbc, 'ticket', ROLE, 'view_notifications');
$get_project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='{$get_ticket['projectid']}'"));
$ticket_types = [];
$value_config = ','.get_field_config($dbc, 'tickets').',';
$sort_order = explode(',',get_config($dbc, 'ticket_sortorder'));
foreach(explode(',',get_config($dbc, 'ticket_tabs')) as $ticket_type) {
	$ticket_types[config_safe_str($ticket_type)] = $ticket_type;
}
$ticket_type = $_GET['ticket_type'];
if(!empty($get_ticket['ticket_type'])) {
	$ticket_type = $get_ticket['ticket_type'] ?: get_config($dbc, 'default_ticket_type');
}
if($ticket_type == '') {
	// $value_config .= get_config($dbc, 'ticket_fields_%', true).',';
	foreach($ticket_types as $type_i => $type_label) {
		$value_config .= get_config($dbc, 'ticket_fields_'.$type_i).',';
	}
} else {
	$value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
	$sort_order = explode(',',get_config($dbc, 'ticket_sortorder_'.$ticket_type));
}

//Accordion Sort Order
foreach ($accordion_list as $accordion_field => $accordion_field_fields) {
	if(!in_array($accordion_field, $sort_order)) {
		$sort_order[] = $accordion_field;
	}
}

DEFINE('HEADER_LEFT', html_entity_decode(str_replace(['[TICKET_TYPE]','[TICKETID]','[TO_DO_DATE]'],[$ticket_types[$get_ticket['ticket_type']],$ticketid,$get_ticket['to_do_date']],get_config($dbc, 'ticket_pdf_header_left'))));
DEFINE('HEADER_CENTER', html_entity_decode(str_replace(['[TICKET_TYPE]','[TICKETID]','[TO_DO_DATE]'],[$ticket_types[$get_ticket['ticket_type']],$ticketid,$get_ticket['to_do_date']],get_config($dbc, 'ticket_pdf_header_center'))));
DEFINE('HEADER_RIGHT', html_entity_decode(str_replace(['[TICKET_TYPE]','[TICKETID]','[TO_DO_DATE]'],[$ticket_types[$get_ticket['ticket_type']],$ticketid,$get_ticket['to_do_date']],get_config($dbc, 'ticket_pdf_header_right'))));
DEFINE('FOOTER_TEXT', html_entity_decode(str_replace(['[TICKET_TYPE]','[TICKETID]','[TO_DO_DATE]'],[$ticket_types[$get_ticket['ticket_type']],$ticketid,$get_ticket['to_do_date']],get_config($dbc, 'ticket_pdf_footer'))));
DEFINE('PDF_LOGO', pdf_to_image('download/'.get_config($dbc, 'ticket_pdf_logo')));
DEFINE('PDF_LOGO_ALIGN', !empty(get_config($dbc, 'ticket_pdf_logo_align')) ? get_config($dbc, 'ticket_pdf_logo_align') : 'C');
DEFINE ('TICKET_PDF_ORIENTATION', !empty(get_config($dbc, 'ticket_pdf_orientation')) ? get_config($dbc, 'ticket_pdf_orientation') : 'P');
if(strpos($value_config,',TEMPLATE Work Ticket') !== FALSE) {
	class MYPDF extends TCPDF {

		public function Header() {
			$this->SetY(10);
			$this->SetFont('helvetica', '', 9);
            $this->setCellHeightRatio(0.6);
			$this->writeHTMLCell(0, 0, 10, 20, HEADER_LEFT, 0, 0, false, "L", true);

			$this->SetY(10);
            $this->setCellHeightRatio(0.6);
			$footer_text = '<p style="text-align:center;">'.HEADER_CENTER.'</p>';
			$this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);

			$this->SetY(10);
            $this->setCellHeightRatio(0.6);
			$footer_text = '<p style="text-align:right;">'.HEADER_RIGHT.'</p>';
			$this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);

			if(PDF_LOGO != '') {
				$this->Image(PDF_LOGO, 0, 10, '', 20, 'PNG', '', 'T', false, 300, PDF_LOGO_ALIGN, false, false, 0, false, false, false);
			}
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica', '', 9);
			$footer_text = '<p style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().'</p>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
			$this->SetY(-15);
			$this->SetFont('helvetica', '', 9);
			$this->writeHTMLCell(0, 0, '', '', FOOTER_TEXT, 0, 0, false, "L", true);
		}
	}

	$pdf = new MYPDF(TICKET_PDF_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();
	$pdf->SetFont('helvetica', '', 9);
	$html = '<table frame="box" style="border:1px solid black">

				<tr><td style="width:10%" rowspan="3"><strong>Sold To: </strong></td><td rowspan="3" style="width: 40%; border-right:1px solid black;">' . get_client($dbc, $get_ticket['businessid']).'<br>'.get_address($dbc, $get_ticket['businessid']).'</td>

                <td style="width:10%"><strong>AFE#:</strong></td><td style="width:40%">'.$get_ticket['afe_number'].'</td></tr>

				<tr><td  style="width:10%"><strong>Location:</strong></td><td style="width:40%">'.get_contact($dbc, $get_ticket['siteid'],'site_name').'</td></tr>
				<tr><td style="width:10%"><strong>Additional Info:</strong></td><td  style="width:40%">'.$get_ticket['piece_work'].'</td></tr>
				<tr><td style="width:10%"><strong>Contact:</strong></td><td style="border-right:1px solid black;">'. get_staff($dbc, $get_ticket['clientid']).'</td><td style="width:10%" ><strong>Job#:</strong></td><td  style="width:40%">'.$get_project['project_name'].'</td></tr>

			</table>
			<table frame="box" style="width:100%; border:1px solid black">
				<tr><td rowspan="1" style="width:20%"><strong>Job Description:</strong></td><td  rowspan="1" style="width:80%">'.html_entity_decode($get_ticket['assign_work']).'</td></tr>
				<tr><td>&nbsp;</td><td>&nbsp;</td></tr>
			</table>';


	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->SetFont('helvetica', '', 8);

	$emp = '';
	$reg_hour_rate = 0;
	$ot_hour_rate = 0;
	$crew_total = 0;

	$add_page = 0;
	// Create Cloned PDF to use for calculating line heights
	$page_height_pdf = clone($pdf);

	// Calculate the number of rows used by the description
	$rows_needed = 0;
	$page_height_pdf->AddPage();
	$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table><tr><td>DEFAULT HEIGHT</td></tr></table>", 0, 1, 1, false);
	$height = $page_height_pdf->getY();
	$page_height_pdf->deletePage($page_height_pdf->getPage());
	$page_height_pdf->AddPage();
	$page_height_pdf->SetFont('helvetica','',9);
	$page_height_pdf->writeHTMLCell('', '', 0, 0, '<table><tr><td width="20%"></td><td width="80%">'.html_entity_decode($get_ticket['assign_work'])."</td></tr></table>", 0, 1, 1, false);
	$current_row_height = $page_height_pdf->getY();
	$page_height_pdf->deletePage($page_height_pdf->getPage());
	$rows_needed += floor($current_row_height / $height) - 1;

	$labour_html = '';
	$labor2_html = '';
	$equip_html = '';
	$equip2_html = '';
	$material_html = '';
	$material2_html = '';
	$other_html = '';
	$other2_html = '';
	$count_subpay_pdf = 0;

	$emp_per_page = 8;
	if($rows_needed > 18) {
		$rows_needed -= 18;
		$emp_per_page -= 6;
	} else {
		$emp_per_page -= ($rows_needed / 3);
		$rows_needed = 0;
	}
	$fill_crew_rows = $emp_per_page;
	$fill_crew2_rows = 0;

	/**LOOP THROUGH EMPLOYEES FROM THE FOREMAN SHEET*/
	/** ADD INDIVIDUAL HOURS AND RATES HERE*/
	$attached = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table`='Staff' AND `ticketid`='$ticketid' AND `item_id` > 0 AND `deleted`=0");
	$emp_loop = 0;
	$html_target = 'labour_html';
	while($staff = mysqli_fetch_assoc($attached)) {
		/**CHECK FOR INDEX VS. NUM ROWS PER PAGE*/
		/**IF INDEX GREATER THAN NUM ROWS SET NEW PAGE FLAG AND CREATE SECOND HTML STRING*/
		if($emp_loop >= $emp_per_page){
			if($add_page != 1){
				$add_page = 1;
			}
			$html_target = 'labor2_html';
		}
		$position = $staff['position'];
		$position_id = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `position_id` FROM `positions` WHERE `name`='$position'"))['position_id'];
		$hourly = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT hourly FROM position_rate_table WHERE position_id = '".$position_id."' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')"))['hourly'];
		if(!($hourly > 0)) {
			$hourly = 0;
		}
		$reg_rate_total = ($staff['hours_estimated'][$emp_loop]*$hourly);
		$ot_rate_total = ($staff['hours_ot'][$emp_loop]*$hourly*1.5);
		$travel_rate_total = ($staff['hours_travel'][$emp_loop]*$hourly);
		$count_subpay_pdf += $staff['hours_subsist'];

		$$html_target .= '<tr><td rowspan="3" style="border-right: 1px solid grey; border-top:1px solid grey;">' . get_staff($dbc, $staff['item_id']).'</td><td style="border-right: 1px solid grey; border-top:1px solid grey;" rowspan="3">' . $position .'</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">REG</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">' . $staff['hours_estimated'][$emp_loop].'</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format((float)$hourly, 2, '.', '').'</td><td style="border-top:1px solid grey; text-align:center;">$'.number_format((float)$reg_rate_total, 2, '.', '').'</td></tr>
		<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">O.T.</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'. $staff['hours_ot'][$emp_loop].'</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format((float)$hourly * 1.5, 2, '.', '').'</td><td style=" border-top:1px solid grey; text-align:center;">$'.number_format((float)$ot_rate_total, 2, '.', '').'</td></tr>
		<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">TRV</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'. $staff['hours_travel'][$emp_loop].'</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format((float)$hourly, 2, '.', '').'</td><td style=" border-top:1px solid grey; text-align:center;">$'.number_format((float)$travel_rate_total, 2, '.', '').'</td></tr>';
		$crew_total += $reg_rate_total+$ot_rate_total+$travel_rate_total;
		if(($emp_loop == mysqli_num_rows($attached)) && ($emp_loop < $emp_per_page)){
			$fill_crew_rows = ($emp_per_page - 2 - mysqli_num_rows($attached));
		}
		else if(($emp_loop == mysqli_num_rows($attached)) && ($emp_loop > $emp_per_page)){
			$fill_crew2_rows = ($emp_per_page + 8 - mysqli_num_rows($attached));
		}
		$emp_loop++;
	}

	if($emp_loop < $emp_per_page) {
		$fill_crew_rows = ($emp_per_page - 2 - mysqli_num_rows($attached));
	}
	else if($emp_loop > $emp_per_page){
		$fill_crew2_rows = ($emp_per_page + 8 - mysqli_num_rows($attached));
	}
	for ($blank_rows = $fill_crew_rows; $blank_rows > 0; $blank_rows--){
		$labour_html .='<tr><td rowspan="3" style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey;" rowspan="3">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">REG</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>
			<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">O.T.</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style=" border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>
			<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">TRV</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style=" border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>';
	}
	for ($blank_rows = $fill_crew2_rows; $blank_rows > 0; $blank_rows--){
		$labour2_html .='<tr><td rowspan="3" style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey;" rowspan="3">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">REG</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>
			<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">O.T.</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style=" border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>
			<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">TRV</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style=" border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>';
	}

	/**LOOP THROUGH EQUIPMENT FROM THE FOREMAN SHEET*/
	/** ADD INDIVIDUAL HOURS AND RATES HERE*/
    $equip = '';
    $equip_total = 0;
	$equip_per_page = 8;
	$equip_per_page2 = 8;
	if($rows_needed > 5) {
		$rows_needed -= 5;
		$equip_per_page -= 5;
	} else if($rows_needed > 0) {
		$equip_per_page -= $rows_needed;
		$rows_needed = 0;
	}
	$fill_equip_rows = $equip_per_page;
	$fill_equip2_rows = 0;
	$html_target = 'equip_html';
	$count_target = 'equip_per_page';
	$equip_loop = 0;
	$attached = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table`='equipment' AND `ticketid`='$ticketid' AND `item_id` > 0 AND `deleted`=0");
	while($equipment = mysqli_fetch_assoc($attached)) {
		$rate = $equipment['rate'];
		$hours = $equipment['hours_estimated'];
		$equip = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `equipmentid`='{$equipment['item_id']}'"));
		if($equip_loop >= $equip_per_page){
			if($add_page != 1){
				$add_page = 1;
			}
			$html_target = 'equip2_html';
			$count_target = 'equip_per_page2';
		}
		$row_html = '<tr><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'.$equip['unit_number'] .'</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">'.$equip['type'].'</td>';
		$row_html .= '<td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">'.$hours.'</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">Hours</td>';
		$eq_amount = $rate * $hours;
		$row_html .= '<td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format((float)$rate, 2, '.', '').'</td><td style="border-top:1px solid grey; text-align:center;">$'.number_format((float)$eq_amount, 2, '.', '').'</td></tr>';
		$$html_target .= $row_html;

		$page_height_pdf->AddPage();
		$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table><tr><td>DEFAULT HEIGHT</td></tr></table>", 0, 1, 1, false);
		$height = $page_height_pdf->getY();
		$page_height_pdf->deletePage($page_height_pdf->getPage());
		$page_height_pdf->AddPage();
		$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table>".$row_html."</table>", 0, 1, 1, false);
		$current_row_height = $page_height_pdf->getY();
		$page_height_pdf->deletePage($page_height_pdf->getPage());
		if($current_row_height > $height) {
			$$count_target -= floor($current_row_height / $height) - 1;
		}

		$equip_total += $eq_amount;
		$equip_loop++;
	}

	if($equip_loop < $equip_per_page){
		$fill_equip_rows = ($equip_per_page - 2 - $equip_loop);
	}
	else if($equip_loop > $equip_per_page) {
		$fill_equip2_rows = ($equip_per_page - 2 + $equip_per_page - $equip_loop);
	}

	for ($blank_rows = $fill_equip_rows; $blank_rows > 0; $blank_rows--){
		$equip_html .='<tr>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
		</tr>';
	}
	for ($blank_rows = $fill_equip2_rows; $blank_rows > 0; $blank_rows--){
		$equip2_html .='<tr>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
		</tr>';
	}

    /** LOOP THROUGH MATERIALS ADDED TO THE FOREMAN SHEET*/
	$attached = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table`='material' AND `ticketid`='$ticketid' AND (`item_id` > 0 OR `description` != '') AND `deleted`=0");
	$material_subtotal = 0;
	$other_subtotal = 0;
	$other_subtotal_without_markup = 0;

	$fill_mat_rows = 0;
	$fill_mat2_rows = 0;
	$mat_per_page = 6;
	$mat_per_page2 = 6;
	if($rows_needed > 0) {
		$mat_per_page -= $rows_needed;
		$rows_needed = 0;
	}
	$sm_loop = 0;
	$target_html = 'material_html';
	$target_count = 'mat_per_page';
	while($material = mysqli_fetch_assoc($attached)) {
		if($sm_loop >= $mat_per_page){
			if($add_page !=1){
				$add_page = 1;
			}
			$target_html = 'material2_html';
			$target_count = 'mat_per_page2';
		}
		$mat_description = ($material['description'] == '' ? mysqli_fetch_assoc()[''] : $material['description']);			$row_html = '<tr>';
		$row_html .='
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;">'.$mat_description.'</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">'.$material['qty'].'</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">$'.number_format((float)$material['rate'], 2, '.', '').'</td>
		<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">$'.number_format((float)$material['rate'] * $material['qty'], 2, '.', '').'</td>';
		$row_html .= '</tr>';
		$$target_html .= $row_html;
		$material_subtotal += $material['rate'] * $material['qty'];

		$page_height_pdf->AddPage();
		$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table><tr><td>DEFAULT HEIGHT</td></tr></table>", 0, 1, 1, false);
		$height = $page_height_pdf->getY();
		$page_height_pdf->deletePage($page_height_pdf->getPage());
		$page_height_pdf->AddPage();
		$page_height_pdf->writeHTMLCell(100, '', 0, 0, "<table>".$row_html."</table>", 0, 1, 1, false);
		$current_row_height = $page_height_pdf->getY();
		$page_height_pdf->deletePage($page_height_pdf->getPage());
		if($current_row_height > $height) {
			$$target_count -= floor($current_row_height / $height) - 1;
		}
		$sm_loop++;
	}

	if($sm_loop < $mat_per_page) {
		$fill_mat_rows = ($mat_per_page - 2 - $sm_loop);
	}
	else if($sm_loop > $mat_per_page) {
		$fill_mat2_rows = ($mat_per_page2 - 2 + $mat_per_page - $sm_loop);
	}

	if($fill_mat_rows > 0){
		for ($blank_rows = $fill_mat_rows; $blank_rows > 0; $blank_rows--){
			$material_html .='<tr>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>';
		}
	}
	else if($fill_mat2_rows > 0){
		for ($blank_rows = $fill_mat2_rows; $blank_rows > 0; $blank_rows--){
			$material2_html .='<tr>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>';
		}

	}

    /**LOOP THROUGH FIELD PO's ADDED TO THE WORK TICKET*/
    $po_image = [];

    if($count_subpay_pdf >= 1){
        $i = 1;
        $po_index = $i - 1;
    }
    else{
        $i = 0;
        $po_index = $i;
    }

    $fill_other_rows = 0;
	$fill_other2_rows = 0;

	foreach(array_filter(explode(',',$get_ticket['po_id'])) as $i => $ticket_po) {
		$po = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_purchase_orders` WHERE `id`='$ticket_po'"));
		$vendor = get_client($dbc, $po['vendorid']);

		$wt_total = $po['final_total'] * (1 + $po['mark_up'] / 100);
		$other_subtotal += $wt_total;
		$other_subtotal_without_markup += $po['final_total'];
		$invoice_image = pdf_to_image('download/'.$po['invoice']);
		if($invoice_image != '') {
			$po_image[] = '<img src="'.$invoice_image.'" style="-webkit-transform:rotate(270deg);"><br>';
		}
		if($i >= 8){/**NEED TO FINISH HOW TO ADDRESS THE FIELD PO ID ISSUE WITH SUB PAY*/
			if($add_page !=1){
				$add_page = 1;
			}
			$other2_html .= '<tr>';
			$other2_html .='<td  style="border-right: 1px solid grey; border-top:1px solid grey; text-align: left;">'.$vendor.': INVOICE '.$po['invoice_number'].'</td>
			<td  style=" border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format($wt_total, 2, '.', '').'</td>';
			$other2_html .= '</tr>';
		}
		else{
			$other_html .= '<tr>';
			$other_html .='<td  style="border-right: 1px solid grey; border-top:1px solid grey; text-align: left;">'.$vendor.': INVOICE '.$po['invoice_number'].'</td>
			<td  style=" border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">$'.number_format($wt_total, 2, '.', '').'</td>';
			$other_html .= '</tr>';
		}
    }
	if($i < 6) {
		$fill_other_rows = 6 - $i;
	} else if($i > 6) {
		$fill_other2_rows = 10 - $i;
	}

	if($fill_other_rows > 0){
		for ($blank_rows = 0; $blank_rows <= $fill_other_rows; $blank_rows++){
			$other_html .='<tr>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			</tr>';
		}
	}
	else if($fill_other2_rows > 0){
		for ($blank_rows = 0; $blank_rows <= $fill_other2_rows; $blank_rows++){
			$other2_html .='<tr>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td>
			<td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td>
			</tr>';
		}

	}

	$sub_pay_rate_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT daily FROM `company_rate_card` WHERE `description`='Subsistence Pay' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION SELECT daily FROM  position_rate_table  WHERE position_id IN(SELECT position_id FROM positions WHERE name='Subsistence Pay') AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION SELECT 0"))['daily'];
	$sub_total = ($crew_total + $equip_total + $material_subtotal + $other_subtotal+$sub_pay_rate_card);

    $sub_total_other_no_markup = ($crew_total + $equip_total + $material_subtotal + $other_subtotal_without_markup+$sub_pay_rate_card);

	$gst = ($sub_total * 0.05);
	$grand_total = $sub_total + $gst;

	$cost_summary ='
					<p>&nbsp;</p>
					<table >
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">LABOUR &nbsp;</td><td style="border:1px solid black; width:60%; font-size: 12px; text-align:right;"> &nbsp;$'.number_format((float)$crew_total, 2, '.', '').'</td></tr>
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">VEHICLE & EQUIP. &nbsp;</td><td style="border:1px solid black; width:60%; font-size: 12px; text-align:right;"> &nbsp;$'.number_format((float)$equip_total, 2, '.', '').'</td></tr>
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">MATERIAL &nbsp;</td><td style="border:1px solid black; width:60%; font-size: 12px; text-align:right;"> &nbsp;$'.number_format((float)$material_subtotal, 2, '.', '').'</td></tr>
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">OTHER ITEMS &nbsp;</td><td style="border:1px solid black; width:60%; font-size: 12px; text-align:right;"> &nbsp;$'.number_format((float)($sub_pay_rate_card+$other_subtotal), 2, '.', '').'</td></tr>
					</table>
					<p>&nbsp;</p>
					<table >
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">SUB-TOTAL &nbsp;</td><td style="border:1px solid black; padding-left:10px; width:60%; font-size: 12px; text-align:right;"> &nbsp;$'.number_format((float)$sub_total, 2, '.', '').'</td></tr>
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">GST &nbsp;</td><td style="border:1px solid black; width:60%; font-size: 12px; text-align:right;"> &nbsp;$'.number_format((float)$gst, 2, '.', '').'</td></tr>
						<tr><td style="text-align:right; width:40%; font-weight:bold; font-size: 9px;">TOTAL &nbsp;</td><td style="border:1px solid black; width:60%; font-size: 12px; text-align:right;"><strong> &nbsp;$'.number_format((float)$grand_total, 2, '.', '').'</strong></td></tr>
					</table>
					<p>&nbsp;</p>
					<table>
						<tr style="text-align:center; font-weight:bold;"><td style="text-align:center; font-weight:bold; border:1px solid black;">Customer Signature</td></tr>

						<tr><td style="text-align:center; font-weight:bold; border:1px solid black;"  rowspan="2">&nbsp;</td></tr>
						<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>


					</table>
					<table>
						<tr style="text-align:center; font-weight:bold;"><td style="text-align:center; font-weight:bold; border:1px solid black;">Customer Name</td></tr>

						<tr><td style="text-align:center; font-weight:bold; border:1px solid black;"  rowspan="2">&nbsp;</td></tr>
						<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>


					</table>
					<table>
						<tr style="text-align:center; font-weight:bold;"><td style="text-align:center; font-weight:bold; border:1px solid black;">Company Stamp</td></tr>

						<tr><td style="text-align:center; font-weight:bold; border:1px solid black;"  rowspan="3">&nbsp;</td></tr>
						<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>
						<tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td></tr>


					</table>';

	$left_column = '<table cellpadding="0">
						<tr>
							<td>
								<table style="border: 1px solid black" cellpadding="1">
									<tr><th style="border-right: 1px solid grey; text-align:center; width:22%;font-weight:bold;">Name</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:22%;">Trade</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:8%;">&nbsp;</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:16%;">Hours</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:16%;">Rate</th><th style="font-weight:bold;text-align:center; width:16%;">Amount</th></tr>';
									$left_column .= $labour_html;
									$left_column .='<tr><td colspan="5" style=" border-top:1px solid grey;font-weight:bold;">Labour Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align:center;">$'.number_format((float)$crew_total, 2, '.', '').'</td></tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table style="border: 1px solid black" cellpadding="1">
									<tr><th style="border-right: 1px solid grey; text-align:center; width:8%;font-weight:bold;">#</th><th style="border-right: 1px solid grey; text-align:center; width:36%;font-weight:bold;">Vehicle/Equipment Charges</th><th style="border-right: 1px solid grey; text-align:center; width:14%;font-weight:bold;">Qty</th><th style="border-right: 1px solid grey; text-align:center; width:10%;font-weight:bold;">Unit</th><th style="border-right: 1px solid grey; text-align:center; width:16%;font-weight:bold;">Rate</th><th style=" text-align:center; width:16%;font-weight:bold;">Amount</th></tr>';
									$left_column .= $equip_html;
									$left_column .='<tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="5">Vehicle & Equipment Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align:center;">$'.number_format((float)$equip_total, 2, '.', '').'</td></tr>
								</table>
							</td>
						</tr>
						<tr>
							<td>
								<table style="border: 1px solid black" cellpadding="1">
									<tr><th style="border-right: 1px solid grey; text-align:center; width:40%;font-weight:bold;">Materials Charges</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Qty</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Unit Price</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Amount</th></tr>';
									$left_column .= $material_html;
									$left_column .= '
									<tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="3">Materials Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align:center;">$'.number_format((float)$material_subtotal, 2, '.', '').'</td></tr>
								</table>
							</td>
						</tr>
					</table>';
	$right_column = '<table border="1" frame="box" cellpadding="1">
						<tr><th style="border-right: 1px solid grey; text-align:center; width:80%;font-weight:bold;">Other Items</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Amount</th></tr>';
						$right_column .= $other_html;
						if($count_subpay_pdf != 0){
							$right_column .= '<tr><td  style=" border-top:1px solid grey;border-right:1px solid grey;font-weight:bold;text-align:left;">Subsistence Pay x '.$count_subpay_pdf.' Crew @  $'.$sub_pay_rate_card.' ea.</td><td style="border-top:1px solid grey;font-weight:bold;text-align:center; width: 20%;">$'.number_format((float)$sub_pay_rate_card, 2, '.', '').'</td></tr>';
						}
						$right_column .= '<tr><td  style="border-top:1px solid grey;font-weight:bold; text-align: left;">Other Items Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align: center;">$'.number_format((float)($other_subtotal+$sub_pay_rate_card), 2, '.', '').'</td></tr>
					</table>';
					if($add_page != 1){
						$right_column .= $cost_summary;

					}

    // get current vertical position
    $y = $pdf->getY();

    // set color for background
    $pdf->SetFillColor(255, 255, 255);

    // set color for text
    $pdf->SetTextColor(0, 0, 0);

    // write the first column
    $pdf->writeHTMLCell(100, '', '', $y, $left_column, 0, 0, 1, true, 'J', true);

    // set color for background
    $pdf->SetFillColor(255, 255, 255);

    // set color for text
    $pdf->SetTextColor(0, 0, 0);

    // write the second column
    $pdf->writeHTMLCell(80, '', '', '', $right_column, 0, 1, 1, true, 'J', true);

	if($add_page == 1){
		$pdf->AddPage();
		/**START WRITING SECOND PAGE HERE*/
		$pdf->SetFont('helvetica', '', 8);

		$left_column = '<table cellpadding="0">
							<tr>
								<td>
									<table style="border: 1px solid black" cellpadding="1">
										<tr><th style="border-right: 1px solid grey; text-align:center; width:22%;font-weight:bold;">Name</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:22%;">Trade</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:8%;">&nbsp;</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:16%;">Hours</th><th style="border-right: 1px solid grey;font-weight:bold;text-align:center; width:16%;">Rate</th><th style="font-weight:bold;text-align:center; width:16%;">Amount</th></tr>';
										if($labour2_html != ''){
											$left_column .= $labour2_html;
										}
										else{
											for($i = 0; $i <= 10; $i++ ){
												$left_column .= '<tr><td rowspan="2" style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey;" rowspan="2">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey;">REG</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>
															<tr><td style="border-right: 1px solid grey; border-top:1px solid grey;">O.T.</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style="border-right: 1px solid grey; border-top:1px solid grey; text-align:center;">&nbsp;</td><td style=" border-top:1px solid grey; text-align:center;">&nbsp;</td></tr>
															';
											}
										}
										$left_column .='<tr><td colspan="5" style=" border-top:1px solid grey;font-weight:bold;">Labour Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align:center;">$'.number_format((float)$crew_total, 2, '.', '').'</td></tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table style="border: 1px solid black" cellpadding="1">
										<tr><th style="border-right: 1px solid grey; text-align:center; width:8%;font-weight:bold;">#</th><th style="border-right: 1px solid grey; text-align:center; width:36%;font-weight:bold;">Vehicle/Equipment Charges</th><th style="border-right: 1px solid grey; text-align:center; width:16%;font-weight:bold;">Qty</th><th style="border-right: 1px solid grey; text-align:center; width:8%;font-weight:bold;">Unit</th><th style="border-right: 1px solid grey; text-align:center; width:16%;font-weight:bold;">Rate</th><th style=" text-align:center; width:16%;font-weight:bold;">Amount</th></tr>';
										if($equip2_html != ''){
											$left_column .= $equip2_html;
										}
										else{
											$left_column .= '<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>';
										}
										$left_column .='
										<tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="5">Vehicle & Equipment Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align:center;">$'.number_format((float)$equip_total, 2, '.', '').'</td></tr>
									</table>
								</td>
							</tr>
							<tr>
								<td>
									<table style="border: 1px solid black" cellpadding="1">
										<tr><th style="border-right: 1px solid grey; text-align:center; width:40%;font-weight:bold;">Materials Charges</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Qty</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Unit Price</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Amount</th></tr>';
										if($material2_html != ''){
											$left_column .= $material2_html;
										}
										else{
											$left_column .= '<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															 <tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															 <tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															 <tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															 <tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
															 <tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>';
										}
										$left_column .= '
										<tr><td  style=" border-top:1px solid grey;font-weight:bold;" colspan="3">Materials Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align:center;">$'.number_format((float)$material_subtotal, 2, '.', '').'</td></tr>
									</table>
								</td>
							</tr>
						</table>';
						if($other2_html != ''){
							$right_column = '<table border="1" frame="box" cellpadding="1">
							<tr><th style="border-right: 1px solid grey; text-align:center; width:80%;font-weight:bold;">Other Items</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Amount</th></tr>';

							$right_column .= $other2_html;
							$right_column .= '<tr><td  style="border-top:1px solid grey;font-weight:bold; text-align: left;">Other Items Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align: center;">$'.number_format((float)($other_subtotal+$sub_pay_rate_card), 2, '.', '').'</td></tr>
							</table>';
						}
						else{
							$right_column = '<table border="1" frame="box" cellpadding="1">
												<tr><th style="border-right: 1px solid grey; text-align:center; width:80%;font-weight:bold;">Other Items</th><th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;">Amount</th></tr>
												<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
												<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
												<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
												<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
												<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
												<tr><td  style="border-right: 1px solid grey; border-top:1px solid grey;">&nbsp;</td><td  style="border-right: 1px solid grey; border-top:1px solid grey;text-align:center;">&nbsp;</td></tr>
												<tr><td  style="border-top:1px solid grey;font-weight:bold; text-align: left;">Other Items Sub-Total</td><td style="border-top:1px solid grey;font-weight:bold; text-align: center;">$'.number_format((float)($other_subtotal+$sub_pay_rate_card), 2, '.', '').'</td></tr>
											 </table>';
						}

							$right_column .= $cost_summary;


	// writeHTMLCell($w, $h, $x, $y, $html='', $border=0, $ln=0, $fill=0, $reseth=true, $align='', $autopadding=true)

	// get current vertical position
	$y = $pdf->getY();

	// set color for background
	$pdf->SetFillColor(255, 255, 255);

	// set color for text
	$pdf->SetTextColor(0, 0, 0);

	// write the first column
	$pdf->writeHTMLCell(100, '', '', $y, $left_column, 0, 0, 1, true, 'J', true);

	// set color for background
	$pdf->SetFillColor(255, 255, 255);

	// set color for text
	$pdf->SetTextColor(0, 0, 0);

	// write the second column
	$pdf->writeHTMLCell(80, '', '', '', $right_column, 0, 1, 1, true, 'J', true);

	}

    foreach($po_image as $po_item) {
        $pdf->AddPage();
		if(strpos($po_item,'.pdf') === FALSE) {
			$pdf->Rotate(270, 70, 110);
		}
        $pdf->writeHTML($po_item, true, false, true, false, 'C');
    }

	$pdf->Output($filename, 'F');
} else {
	class MYPDF extends TCPDF {

		public function Header() {
			$this->SetY(10);
			$this->SetFont('helvetica', '', 9);
            $this->setCellHeightRatio(0.6);
			$this->writeHTMLCell(0, 0, 10, 20, HEADER_LEFT, 0, 0, false, "L", true);

			$this->SetY(10);
            $this->setCellHeightRatio(0.6);
			$footer_text = '<p style="text-align:center;">'.HEADER_CENTER.'</p>';
			$this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);

			$this->SetY(10);
            $this->setCellHeightRatio(0.6);
			$footer_text = '<p style="text-align:right;">'.HEADER_RIGHT.'</p>';
			$this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);

			if(PDF_LOGO != '') {
				$this->Image(PDF_LOGO, 0, 10, '', 20, 'PNG', '', 'T', false, 300, PDF_LOGO_ALIGN, false, false, 0, false, false, false);
			}
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica', '', 9);
			$footer_text = '<p style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().'</p>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
			$this->SetY(-15);
			$this->SetFont('helvetica', '', 9);
			$this->writeHTMLCell(0, 0, '', '', FOOTER_TEXT, 0, 0, false, "L", true);
		}
	}

	$pdf = new MYPDF(TICKET_PDF_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 35, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();
	$pdf->SetFont('helvetica', '', 9);

	ob_clean();
	echo '<h1>'.($ticketid > 0 ? get_ticket_label($dbc, $get_ticket) : 'New '.TICKET_NOUN).'</h1>';

	$_GET['edit'] = $ticketid;
	unset($ticketid);
	$force_readonly = true;

	$pdf_header_color = get_calendar_today_color($dbc);

	$global_value_config = $value_config;

	$generate_pdf = true;
	$pdf_contents = [];
	foreach($sort_order as $sort_field) {
		$value_config = $global_value_config;

		//Custom accordions
		if(strpos($value_config, ','.$sort_field.',') !== FALSE && substr($sort_field, 0, strlen('FFMCUST_')) === 'FFMCUST_') {
			$_GET['tab'] = str_replace(' ','_',$sort_field);
			$acc_label = explode('FFMCUST_',$sort_field)[1];
			include('edit_ticket_tab.php');
	 	}
		if (strpos($value_config, ','."Information".',') !== FALSE && $sort_field == 'Information') {
			$_GET['tab'] = 'project_info';
			$acc_label = PROJECT_NOUN.' Information';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Details".',') !== FALSE && $sort_field == 'Details') {
			$_GET['tab'] = 'project_details';
			$acc_label = PROJECT_NOUN.' Details';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Path & Milestone".',') !== FALSE && $sort_field == 'Path & Milestone') {
			$_GET['tab'] = 'ticket_path_milestone';
			$acc_label = PROJECT_NOUN.' Path & Milestone';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Individuals".',') !== FALSE && $sort_field == 'Individuals') {
			$_GET['tab'] = 'ticket_individuals';
			$acc_label = 'Individuals Present';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Fees".',') !== FALSE && $sort_field == 'Fees') {
			$_GET['tab'] = 'ticket_fees';
			$acc_label = 'Fees';
			include('edit_ticket_tab.php');
		}
		if ((strpos($value_config, ','."Location".',') !== FALSE || strpos($value_config, ','."Emergency".',') !== FALSE) && $sort_field == 'Location') {
			$_GET['tab'] = 'ticket_location';
			$acc_label = 'Site';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Members ID".',') !== FALSE && $sort_field == 'Members ID') {
			$_GET['tab'] = 'ticket_members_id_card';
			$acc_label = 'Members ID Card';
			include('edit_ticket_tab.php');
		}
		if ((strpos($value_config, ','."Mileage".',') !== FALSE || strpos($value_config, ','."Drive Time".',') !== FALSE) && $sort_field == 'Mileage') {
			$_GET['tab'] = 'ticket_mileage';
			$acc_label = strpos($value_config, ','."Mileage".',') !== FALSE ? 'Mileage' : 'Drive Time';
			include('edit_ticket_tab.php');
		}
		if(strpos($value_config, ',Staff,') !== FALSE && $sort_field == 'Staff') {
			$_GET['tab'] = 'ticket_staff_list';
			$acc_label = 'Staff';
			include('edit_ticket_tab.php');
		}
		if(strpos($value_config, ',Staff Tasks,') !== FALSE && $sort_field == 'Staff Tasks') {
			if($ticketid > 0 && $_GET['new_ticket'] != 'true') {
				$_GET['tab'] = 'ticket_staff_tasks';
				$acc_label = 'Staff Tasks';
				include('edit_ticket_tab.php');
				$collapse_i++;
			}
		}
		if(strpos($value_config, ',Members,') !== FALSE && $sort_field == 'Members') {
			$_GET['tab'] = 'ticket_members';
			$acc_label = 'Members';
			include('edit_ticket_tab.php');
		}
		if(strpos($value_config, ',Clients,') !== FALSE && $sort_field == 'Clients') {
			$_GET['tab'] = 'ticket_clients';
			$acc_label = 'Clients';
			include('edit_ticket_tab.php');
		}
		if(strpos($value_config, ',Wait List,') !== FALSE && $sort_field == 'Wait List') {
			$_GET['tab'] = 'ticket_wait_list';
			$acc_label = 'Wait List';
			include('edit_ticket_tab.php');
		}
		if ((strpos($value_config, ','."Check In".',') !== FALSE || strpos($value_config, ','."Check In Member Drop Off".',') !== FALSE) && $sort_field == 'Check In') {
			$_GET['tab'] = 'ticket_checkin';
			$acc_label = strpos($value_config, ','."Check In Member Drop Off".',') !== FALSE ? 'Member Drop Off' : 'Check In';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Medication".',') !== FALSE && $access_medication === TRUE && $sort_field == 'Medication') {
			$_GET['tab'] = 'ticket_medications';
			$acc_label = 'Medication Administration';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Ticket Details".',') !== FALSE && $sort_field == 'Ticket Details') {
			$_GET['tab'] = 'ticket_info';
			$acc_label = TICKET_NOUN.' Details';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Services".',') !== FALSE && $sort_field == 'Ticket Details') {
			$_GET['tab'] = 'ticket_info';
			$acc_label = 'Services';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Equipment".',') !== FALSE && $sort_field == 'Equipment') {
			$_GET['tab'] = 'ticket_equipment';
			$acc_label = 'Equipment';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Checklist".',') !== FALSE && $access_all > 0 && $sort_field == 'Checklist') {
			$_GET['tab'] = 'ticket_checklist';
			$acc_label = 'Checklist';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Checklist Items".',') !== FALSE && $access_all > 0 && $sort_field == 'Checklist Items') {
			$_GET['tab'] = 'ticket_view_checklist';
			$acc_label = 'Checklist Items';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Charts".',') !== FALSE && $access_all > 0 && $sort_field == 'Charts') {
			$_GET['tab'] = 'ticket_view_charts';
			$acc_label = 'Charts';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Safety".',') !== FALSE && $access_all > 0 && $sort_field == 'Safety') {
			$_GET['tab'] = 'ticket_safety';
			$acc_label = 'Safety Checklist';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Materials".',') !== FALSE && $sort_field == 'Materials') {
			$_GET['tab'] = 'ticket_materials';
			$acc_label = 'Materials';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ',Miscellaneous') !== FALSE && $sort_field == 'Miscellaneous') {
			$_GET['tab'] = 'ticket_miscellaneous';
			$acc_label = 'Miscellaneous';
			include('edit_ticket_tab.php');
			$collapse_i++;
		}
		if (strpos($value_config, ',Inventory Basic') !== FALSE && $sort_field == 'Inventory') {
			$_GET['tab'] = 'ticket_inventory';
			$acc_label = 'Inventory';
			include('edit_ticket_tab.php');
			$collapse_i++;
		}
		if (strpos($value_config, ',Inventory General,') !== FALSE && $sort_field == 'Inventory General') {
			$_GET['tab'] = 'ticket_inventory_general';
			$acc_label = 'General Cargo / Inventory Information';
			include('edit_ticket_tab.php');
			$collapse_i++;
		}
		if (strpos($value_config, ',Inventory Detail,') !== FALSE && $sort_field == 'Inventory Detail') {
			$_GET['tab'] = 'ticket_inventory_detailed';
			$acc_label = 'Detailed Cargo / Inventory Information';
			include('edit_ticket_tab.php');
			$collapse_i++;
		}
		if (strpos($value_config, ',Inventory Return,') !== FALSE && $sort_field == 'Inventory Return') {
			$_GET['tab'] = 'ticket_inventory_return';
			$acc_label = 'Return Information';
			include('edit_ticket_tab.php');
			$collapse_i++;
		}
		if (strpos($value_config, ','."Purchase Orders".',') !== FALSE && $access_all > 0 && $sort_field == 'Purchase Orders') {
			$_GET['tab'] = 'ticket_purchase_orders';
			$acc_label = 'Purchase Orders';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Attached Purchase Orders".',') !== FALSE && $access_all > 0 && $sort_field == 'Attached Purchase Orders') {
			$_GET['tab'] = 'ticket_attach_purchase_orders';
			$acc_label = 'Purchase Orders';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Delivery".',') !== FALSE && $sort_field == 'Delivery') {
			$_GET['tab'] = 'ticket_delivery';
			$acc_label = 'Delivery Details';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ',Transport Origin') !== FALSE && $sort_field == 'Transport') {
			$_GET['tab'] = 'ticket_transport_origin';
			$acc_label = 'Transport Log - Origin';
			include('edit_ticket_tab.php');
			$collapse_i++;
		}
		if (strpos($value_config, ',Transport Destination') !== FALSE && $sort_field == 'Transport') {
			$_GET['tab'] = 'ticket_transport_destination';
			$acc_label = 'Transport Log - Destination';
			include('edit_ticket_tab.php');
			$collapse_i++;
		}
		if (strpos(str_replace(['Transport Origin','Transport Destination'],'',$value_config), ',Transport ') !== FALSE && $sort_field == 'Transport') {
			$_GET['tab'] = 'ticket_transport_details';
			$acc_label = 'Carrier Details';
			include('edit_ticket_tab.php');
			$collapse_i++;
		}
		if (strpos($value_config, ','."Documents".',') !== FALSE && $sort_field == 'Documents') {
			$_GET['tab'] = 'view_ticket_documents';
			$acc_label = 'Documents';
			include('edit_ticket_tab.php');
		}
		if ((strpos($value_config, ','."Check Out".',') !== FALSE || strpos($value_config, ','."Check Out Member Pick Up".',') !== FALSE) && $sort_field == 'Check Out') {
			$_GET['tab'] = 'ticket_checkout';
			$acc_label = strpos($value_config, ','."Check In Member Pick Up".',') !== FALSE ? 'Member Pick Up' : 'Check Out';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Staff Check Out".',') !== FALSE && $sort_field == 'Staff Check Out') {
			$_GET['tab'] = 'ticket_checkout_staff';
			$acc_label = 'Staff Check Out';
			include('edit_ticket_tab.php');
		}
		if ((strpos($value_config, ','."Deliverables".',') !== FALSE || strpos($value_config, ','."Deliverable To Do".',') !== FALSE || strpos($value_config, ','."Deliverable Internal".',') !== FALSE || strpos($value_config, ','."Deliverable Customer".',') !== FALSE) && $sort_field == 'Deliverables') {
			$_GET['tab'] = 'view_ticket_deliverables';
			$acc_label = 'Deliverables';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Timer".',') !== FALSE && $sort_field == 'Timer') {
			$_GET['tab'] = 'view_ticket_timer';
			$acc_label = 'Time Tracking';
			include('edit_ticket_tab.php');
			$collapse_i++;
		}
		if (strpos($value_config, ','."Timer".',') !== FALSE && $access_all > 0 && $sort_field == 'Timer') {
			$_GET['tab'] = 'view_day_tracking';
			$acc_label = 'Day Tracking';
			include('edit_ticket_tab.php');
			$collapse_i++;
		}
		if (strpos($value_config, ','."Addendum".',') !== FALSE && $sort_field == 'Addendum') {
			$_GET['tab'] = 'addendum_view_ticket_comment';
			$acc_label = 'Addendum Notes';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Client Log".',') !== FALSE && $sort_field == 'Client Log') {
			$_GET['tab'] = 'ticket_log_notes';
			$acc_label = 'Staff Log Notes';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Debrief".',') !== FALSE && $sort_field == 'Debrief') {
			$_GET['tab'] = 'debrief_view_ticket_comment';
			$acc_label = 'Debrief Notes';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Member Log Notes".',') !== FALSE && $sort_field == 'Member Log Notes') {
			$category = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category` FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.",'Business','Sites') AND `deleted`=0 AND `status`>0 GROUP BY `category` ORDER BY COUNT(*) DESC"))['category'];
			$_GET['tab'] = 'member_view_ticket_comment';
			$acc_label = $category.' Daily Log Notes';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Cancellation".',') !== FALSE && $sort_field == 'Cancellation') {
			$_GET['tab'] = 'ticket_cancellation';
			$acc_label = 'Cancellation';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Custom Notes".',') !== FALSE && $sort_field == 'Custom Notes') {
			$_GET['tab'] = 'custom_view_ticket_comment';
			$custom_note_labels = get_config($dbc, 'ticket_custom_notes_type');
			foreach(explode('#*#',$custom_note_labels) as $custom_comment_types) {
				$acc_label = $custom_comment_types;
				$custom_comment_types = [$custom_comment_types];
				include('edit_ticket_tab.php');
			}
		}
		if (strpos($value_config, ','."Internal Communication".',') !== FALSE && $sort_field == 'Internal Communication') {
			$_GET['tab'] = 'internal_communication';
			$acc_label = 'Internal Communication';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."External Communication".',') !== FALSE && $sort_field == 'External Communication') {
			$_GET['tab'] = 'external_communication';
			$acc_label = 'External Communication';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Notes".',') !== FALSE && $sort_field == 'Notes') {
			$_GET['tab'] = 'notes_view_ticket_comment';
			$acc_label = TICKET_NOUN.' Notes';
			include('edit_ticket_tab.php');
		}
		if ((strpos($value_config, ','."Summary".',') !== FALSE || strpos($value_config, ','."Staff Summary".',') !== FALSE) && $sort_field == 'Summary') {
			$_GET['tab'] = 'ticket_summary';
			$acc_label = strpos($value_config, ','."Staff Summary".',') !== FALSE ? 'Staff Summary' : 'Summary';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Multi-Disciplinary Summary Report".',') !== FALSE && $sort_field == 'Multi-Disciplinary Summary Report') {
			$_GET['tab'] = 'view_multi_disciplinary_summary_report';
			$acc_label = 'Multi Disciplinary Summary Notes';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Complete".',') !== FALSE && $sort_field == 'Complete') {
			$_GET['tab'] = 'ticket_complete';
			$acc_label = 'Complete '.TICKET_NOUN;
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Notifications".',') !== FALSE && $sort_field == 'Notifications') {
			$_GET['tab'] = 'view_ticket_notifications';
			$acc_label = 'Notifications';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Region Location Classification".',') !== FALSE && $sort_field == 'Region Location Classification') {
			$_GET['tab'] = 'ticket_reg_loc_class';
			$acc_label = 'Region/Location/Classification';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Incident Reports".',') !== FALSE && $sort_field == 'Incident Reports') {
			$_GET['tab'] = 'view_ticket_incident_reports';
			$acc_label = INC_REP_TILE;
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Billing".',') !== FALSE && $sort_field == 'Billing') {
			$_GET['tab'] = 'ticket_billing';
			$acc_label = 'Billing';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Customer Notes".',') !== FALSE && $sort_field == 'Customer Notes') {
			$_GET['tab'] = 'ticket_customer_notes';
			$acc_label = 'Customer Notes';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Residue".',') !== FALSE && $sort_field == 'Residue') {
			$_GET['tab'] = 'ticket_residues';
			$acc_label = 'Residue';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Reading".',') !== FALSE && $sort_field == 'Reading') {
			$_GET['tab'] = 'ticket_readings';
			$acc_label = 'Reading';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Tank Reading".',') !== FALSE && $sort_field == 'Tank Reading') {	
			$_GET['tab'] = 'ticket_tank_readings';
			$acc_label = 'Tank Reading';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Shipping List".',') !== FALSE && $sort_field == 'Shipping List') {
			$_GET['tab'] = 'ticket_shipping_list';
			$acc_label = 'Shipping List';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Other List".',') !== FALSE && $sort_field == 'Other List') {
			$_GET['tab'] = 'ticket_other_list';
			$acc_label = 'Other List';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Pressure".',') !== FALSE && $sort_field == 'Pressure') {
			$_GET['tab'] = 'ticket_pressure';
			$acc_label = 'Pressure';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Chemicals".',') !== FALSE && $sort_field == 'Chemicals') {
			$_GET['tab'] = 'ticket_chemicals';
			$acc_label = 'Chemicals';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Intake".',') !== FALSE && $sort_field == 'Intake') {
			$_GET['tab'] = 'ticket_intake';
			$acc_label = 'Intake';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."History".',') !== FALSE && $sort_field == 'History') {
			$_GET['tab'] = 'ticket_history';
			$acc_label = 'History';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Work History".',') !== FALSE && $sort_field == 'Work History') {
			$_GET['tab'] = 'ticket_work_history';
			$acc_label = 'Work History';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Service Staff Checklist".',') !== FALSE && $sort_field == 'Service Staff Checklist') {
			$_GET['tab'] = 'ticket_service_checklist';
			$acc_label = 'Service Checklist';
			include('edit_ticket_tab.php');
		}
		if (strpos($value_config, ','."Service Extra Billing".',') !== FALSE && $sort_field == 'Service Extra Billing') {
			$_GET['tab'] = 'ticket_service_extra_billing';
			$acc_label = 'Service Extra Billing';
			include('edit_ticket_tab.php');
		}
	}
	ob_clean();
	$html = '';
	$html .= '<h1>'.get_ticket_label($dbc, mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"))).'</h1>';
	$html .= '<table border="1" cellpadding="2">';
	$htmls = [];
	foreach($pdf_contents as $key => $line) {
		$line_html = '';
		$img = $line[2];
		$header = $line[0];
		$line = $line[1];
		if($header == '**HEADING**') {
			end($htmls);
			if($hide_blank_fields && explode('#*#',key($htmls))[0] == '**HEADING**') {
				array_pop($htmls);
			}
			$htmls[$header.'#*#'.$key] = '<tr><td class="pdf_header" style="text-align: center; background-color: #'.$pdf_header_color.'; color: #fff; font-weight: bold; width: 100%; border: 1px solid black;">'.$line.'</td></tr>';
		} else if (!empty(trim(strip_tags($line[1]))) || !$hide_blank_fields) {
			if($img != 'img') {
				$line = preg_replace('/<img((?!>).)*>/','',$line);
			}
			$line = preg_replace('/<button((?!\/button>).)*\/button>/','',$line);
			if(strpos($line,'form-group')) {
				$line = str_replace('class="form-group','style="margin:0;padding:0;display:block;width:900px;" class="',$line);
			} else {
				$line = str_replace('div','span',$line);
			}
			$line = str_replace('class="form-control"','style="width:100%;"',$line);
			$line = str_replace('class="form-control datepicker"','style="width:100%;"',$line);
			$line = str_replace('select ','select style="width:100%;"',$line);
			$line = str_replace('textarea','div',$line);
			$line = str_replace('class="col-sm-4','style="width:25%;" class="',$line);
			$line = str_replace('class="control-label col-sm-4','style="width:25%;display:inline-block;" class="',$line);
			$line = str_replace('class="col-sm-8','style="width:74%;" class="',$line);

			$width = 100;
			$line_html .= '<tr>';
			if(!empty($header)) {
				$width = 70;
				$line_html .= '<td class="pdf_label" style="width: 30%; border: 1px solid black; display: inline;">'.$header.'</td>';
			}
			$line_html .= '<td class="pdf_content" style="width: '.$width.'%; border: 1px solid black; display: inline;">'.($ticketid>0 ? $line : '').'</td>';
			$line_html .= '</tr>';
			if(!empty(trim(strip_tags($line))) || !$hide_blank_fields) {
				$htmls[$header.'#*#'.$key] = $line_html;
			}
		}
	}
	end($htmls);
	if($hide_blank_fields && explode('#*#',key($htmls))[0] == '**HEADING**') {
		array_pop($htmls);
	}
	$html .= implode('',$htmls);
	$html .= '</table>';

	$pdf->writeHTML($html);
	$pdf->Output($filename, 'F');
	echo "<script> window.location.replace('$filename'); </script>";
    /*
    if(empty($_GET['action'])) {
	    echo "<script> window.location.replace('$filename'); </script>";
    } else {
        //$referer = $_SERVER['HTTP_REFERER'];
        //header("Location: $referer");
    }
    */
}

function pdf_to_image($file) {
	if($file == '' || $file == 'download/' || !file_exists($file)) {
		return '';
	}
	$file_type = strtolower(pathinfo($file, PATHINFO_EXTENSION));

	if($file_type == 'pdf') {
		try {
			exec('gs -sDEVICE=png16m -r600 -dDownScaleFactor=3 -o "download/field_invoice/'.$file.'.png" "download/field_invoice/'.$file.'"');
			$file .= '.png';
			$file_type = 'png';
		} catch(Exception $e) { return ''; }
	}
	if($file_type == 'jpg' || $file_type == 'jpeg' || $file_type == 'bmp' || $file_type == 'gif' || $file_type == 'png') {
		return $file;
	}
	return '';
}
