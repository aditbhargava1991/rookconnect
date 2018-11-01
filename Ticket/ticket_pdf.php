<?php include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
include_once('../Ticket/field_list.php');
include_once('../Ticket/config.php');
if(!file_exists('download')) {
	mkdir('download', 0777, true);
}
$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
$hide_blank_fields = false;
if(get_config($dbc, 'ticket_pdf_hide_blank') == 1 && $ticketid > 0) {
	$hide_blank_fields = true;
}
if(!($ticketid > 0) && get_config($dbc, 'ticket_pdf_blank_new_id') == 1) {
    $ticket_type = filter_var(!empty($_GET['ticket_type']) ? $_GET['ticket_type'] : get_config($dbc, 'default_ticket_type'),FILTER_SANITIZE_STRING);
    $dbc->query("INSERT INTO `tickets` (`ticket_type`) VALUES ('$ticket_type')");
    $ticketid = $dbc->insert_id;
}
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

	$html = '';
    include('../Ticket/ticket_pdf_content.php');
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
