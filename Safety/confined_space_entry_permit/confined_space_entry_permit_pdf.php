<?php
	function confined_space_entry_permit_pdf($dbc,$safetyid, $fieldlevelriskid) {
    $form_by = $_SESSION['contactid'];
	$get_field_level = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM safety_confined_space_entry_permit WHERE fieldlevelriskid='$fieldlevelriskid'"));

	$tab = get_safety($dbc, $safetyid, 'tab');
    $form = get_safety($dbc, $safetyid, 'form');

	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_safety WHERE tab='$tab' AND form='$form'"));
    $form_config = ','.$get_field_config['fields'].',';
	$get_pdf_logo = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT pdf_logo FROM field_config_safety WHERE tab='$tab' AND form='$form'"));

    DEFINE('PDF_LOGO', $get_pdf_logo['pdf_logo']);
	DEFINE('PDF_HEADER', html_entity_decode($get_field_config['pdf_header']));
    DEFINE('PDF_FOOTER', html_entity_decode($get_field_config['pdf_footer']));

		$before_change = capture_before_change($dbc, 'safety_confined_space_entry_permit', 'status', 'fieldlevelriskid', $fieldlevelriskid);
	$result_update_employee = mysqli_query($dbc, "UPDATE `safety_confined_space_entry_permit` SET `status` = 'Done' WHERE fieldlevelriskid='$fieldlevelriskid'");
	$history = capture_after_change('status', 'Done');
	add_update_history($dbc, 'safety_history', $history, '', $before_change);


	$today_date = $get_field_level['today_date'];
    $contactid = $get_field_level['contactid'];
	$desc = $get_field_level['desc'];
	$desc1 = $get_field_level['desc1'];
	$all_task = $get_field_level['all_task'];
    $fields = explode('**FFM**', $get_field_level['fields']);

	class MYPDF extends TCPDF {

        //Page header
        public function Header() {
            if(PDF_LOGO != '') {
                $image_file = 'download/'.PDF_LOGO;
                $this->Image($image_file, 10, 10, 30, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }

            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.PDF_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);
        }

        // Page footer
        public function Footer() {
            // Position at 15 mm from bottom
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
            $this->SetY(-30);
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = PDF_FOOTER;
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "C", true);
        }
    }

    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
    $pdf->setFooterData(array(0,64,0), array(0,64,128));

    if(PDF_LOGO != '') {
        $pdf->SetMargins(PDF_MARGIN_LEFT, 55, PDF_MARGIN_RIGHT);
    } else {
        $pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
    }
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, 40);

    $pdf->AddPage();
    $pdf->setCellHeightRatio(1.6);
    $pdf->SetFont('helvetica', '', 9);

	$html_weekly = '<h2>Confined Space Entry Permit</h2>'; // Form nu heading

	$html_weekly .= '<table border="1px" style="padding:3px; border:1px solid black;">';
    $html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="34%">Entry Date</th><th width="33%">Start Time</th><th width="33%">Completion Time</th></tr>';
	$html_weekly .= '<tr nobr="true"><td>'.$today_date.'</td><td>'.$fields[1].'</td><td>'.$fields[2].'</td></tr>';

	$html_weekly .= '</table>';

	$html_weekly .= '<h4>Description of Work To Be Performed</h4>' . html_entity_decode($desc);


    $html_weekly .= '<br><table border="1px" style="padding:3px; border:1px solid black;">';
	$html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="100%">Location of Confined Space</th></tr>';
	$html_weekly .= '<tr nobr="true"><td>'.$fields[3].'</td></tr>';

    $html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="50%">Confined Space ID #</th><th width="50%">Classification</th></tr>';
	$html_weekly .= '<tr nobr="true"><td>'.$fields[4].'</td><td>'.$fields[5].'</td></tr>';

	$html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="100%">Type of Confined Space</th></tr>';
	$html_weekly .= '<tr nobr="true"><td>'.$fields[6].'</td></tr>';

    $html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="100%">Supervisor in Charge Of Entry (print name)</th></tr>';
	$html_weekly .= '<tr nobr="true"><td>'.$fields[7].'</td></tr>';

    $html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="100%">Safety Watch (print name)</th></tr>';
	$html_weekly .= '<tr nobr="true"><td>'.$fields[8].'</td></tr>';

	$html_weekly .= '</table>';

	$html_weekly .= '<h4>Pre-Entry Authorization</h4>';

    //if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Oxygen-Deficient Atmosphere';
        if ($fields[9]=='Oxygen-Deficient Atmosphere') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[10];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Oxygen-Enriched Atmosphere';
        if ($fields[11]=='Oxygen-Enriched Atmosphere') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[12];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Welding/Cutting (Hot Work)';
        if ($fields[13]=='Welding/Cutting (Hot Work)') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[14];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Engulfment';
        if ($fields[15]=='Engulfment') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[16];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Toxic Atmosphere';
        if ($fields[17]=='Toxic Atmosphere') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[18];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Flammable Atmosphere';
        if ($fields[19]=='Flammable Atmosphere') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[20];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Energized Electrical Equipment';
        if ($fields[21]=='Energized Electrical Equipment') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[22];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Entrapment';
        if ($fields[23]=='Entrapment') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[24];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Hazardous Chemical';
        if ($fields[25]=='Hazardous Chemical') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[26];
    //}

	$html_weekly .= '<h4>PPE & Safety Equipment Required for Entry</h4>';

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Self Contained Breathing Apparatus';
        if ($fields[27]=='Self Contained Breathing Apparatus') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[28];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Air-Line Respirator (SABA)';
        if ($fields[29]=='Air-Line Respirator (SABA)') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[30];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Flame Resistant Clothing';
        if ($fields[31]=='Flame Resistant Clothing') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[32];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Ventilation';
        if ($fields[33]=='Ventilation') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[34];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Two Way Communications';
        if ($fields[35]=='Two Way Communications') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[36];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Harness';
        if ($fields[37]=='Harness') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[38];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Rescue Tripod with lifeline';
        if ($fields[39]=='Rescue Tripod with lifeline') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[40];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Rescue Tripod with mechanical winch';
        if ($fields[41]=='Rescue Tripod with mechanical winch') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[42];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Chemical Suits';
        if ($fields[43]=='Chemical Suits') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[44];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Gloves';
        if ($fields[45]=='Gloves') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[46];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Hard Hat';
        if ($fields[47]=='Hard Hat') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[48];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Safety glasses / Goggles / Shields';
        if ($fields[49]=='Safety glasses / Goggles / Shields') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[50];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Hearing Protection';
        if ($fields[51]=='Hearing Protection') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[52];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Steel Toed Boots';
        if ($fields[53]=='Steel Toed Boots') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[54];
    //}

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br>Others';
        if ($fields[55]=='Others') {
            $html_weekly .= '&nbsp;&nbsp;<img src="../img/checkmark.png" width="10" height="10" border="0" alt="">';
        }
        $html_weekly .= '  '.$fields[56];
    //}

	$html_weekly .= '<h4>Comments</h4>' . html_entity_decode($desc1);


	$html_weekly .= '<h4>Air Monitoring Results Prior To Entry</h4>';

	$html_weekly .= '<table border="1px" style="padding:3px; border:1px solid black;">';
	$html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="50%">Monitor Type</th><th width="50%">Serial Number</th></tr>';
	$html_weekly .= '<tr nobr="true"><td>'.$fields[57].'</td><td>'.$fields[58].'</td></tr>';

    $html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="25%">Oxygen</th><th width="25%">LEL</th><th width="25%">CO</th><th width="25%">H2S</th></tr>';
	$html_weekly .= '<tr nobr="true"><td>'.$fields[59].'%</td><td>'.$fields[60].'%</td><td>'.$fields[61].'%</td><td>'.$fields[62].'%</td></tr>';

	$html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="25%">Calibration Performed?</th><th width="25%">Initials</th><th width="25%">Alarm Conditions?</th><th width="25%">Description</th></tr>';
	$html_weekly .= '<tr nobr="true"><td>'.$fields[63].'</td><td>'.$fields[64].'</td><td>'.$fields[65].'</td><td>'.$fields[66].'</td></tr>';

	$html_weekly .= '</table>';

    $html_weekly .= '<br><br><table border="1px" style="padding:3px; border:1px solid black;">';
    $html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
                    <th>Time</th>
                    <th>Oxygen</th>
                    <th>LEL Level</th>
                    <th>CO</th>
					<th>H2S</th></tr>';
    $all_task_each = explode('**##**',$all_task);

    $total_count = mb_substr_count($all_task,'**##**');
    for($client_loop=0; $client_loop<=$total_count; $client_loop++) {
                    $task_item = explode('**',$all_task_each[$client_loop]);
                    $task = $task_item[0];
                    $hazard = $task_item[1];
                    $level = $task_item[2];
                    $plan = $task_item[3];
					$plan1 = $task_item[4];
                    if($task != '') {
                        $html_weekly .= '<tr>';
                        $html_weekly .= '<td data-title="Email">' . $task . '</td>';
                        $html_weekly .= '<td data-title="Email">' . $hazard . '</td>';
                        $html_weekly .= '<td data-title="Email">' . $level . '</td>';
                        $html_weekly .= '<td data-title="Email">' . $plan . '</td>';
						$html_weekly .= '<td data-title="Email">' . $plan1 . '</td>';
                        $html_weekly .= '</tr>';
                    }
                }
    $html_weekly .= '</table>';

	//if (strpos(','.$form_config.',', ',fields7,') !== FALSE) {
        $html_weekly .= '<br><h4><img src="../img/checkmark.png" width="10" height="10" border="0" alt="">&nbsp;&nbsp;Entry Authorization</h4>All actions and/or conditions for safe entry have been performed';

    //}

	$sa = mysqli_query($dbc, "SELECT * FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid'");

    $html_weekly .= '<br><br><table border="1px" style="padding:3px; border:1px solid black;">';
    $html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
        <th>Name</th>
        <th>Signature</th>
        </tr>';

    while($row_sa = mysqli_fetch_array( $sa )) {
        $assign_staff_id = $row_sa['safetyattid'];
        $staffcheck = $row_sa['staffcheck'];

        $html_weekly .= '<tr nobr="true">';
        $html_weekly .= '<td data-title="Email">' . $row_sa['assign_staff'] . '</td>';

        // avs_near_miss = form name

        $html_weekly .= '<td data-title="Email"><img src="confined_space_entry_permit/download/safety_'.$assign_staff_id.'.png" width="150" height="70" border="0" alt=""></td>';
        $html_weekly .= '</tr>';
    }
    $html_weekly .= '</table>';

    $pdf->writeHTML($html_weekly, true, false, true, false, '');

    // avs_near_miss = form name
    $pdf->Output('confined_space_entry_permit/download/hazard_'.$fieldlevelriskid.'.pdf', 'F');

    $sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid'");
    while($row_sa = mysqli_fetch_array( $sa )) {
        $assign_staff_id = $row_sa['safetyattid'];

        // avs_near_miss = form name
        unlink("confined_space_entry_permit/download/safety_".$assign_staff_id.".png");
    }
    echo '';
}
?>
