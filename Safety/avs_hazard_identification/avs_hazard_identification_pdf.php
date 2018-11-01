<?php
function avs_hazard_identification_pdf($dbc,$safetyid, $fieldlevelriskid) {
    $form_by = $_SESSION['contactid'];

    $get_field_level = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM safety_avs_hazard_identification WHERE fieldlevelriskid='$fieldlevelriskid'"));

    $tab = get_safety($dbc, $safetyid, 'tab');
    $form = get_safety($dbc, $safetyid, 'form');

    $get_pdf_logo = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT pdf_logo FROM field_config_safety WHERE tab='$tab' AND form='$form'"));

    DEFINE('PDF_LOGO', $get_pdf_logo['pdf_logo']);
	DEFINE('PDF_HEADER', html_entity_decode($get_field_config['pdf_header']));
    DEFINE('PDF_FOOTER', html_entity_decode($get_field_config['pdf_footer']));

    $before_change = capture_before_change($dbc, 'safety_avs_hazard_identification', 'status', 'fieldlevelriskid', $fieldlevelriskid);
    $result_update_employee = mysqli_query($dbc, "UPDATE `safety_avs_hazard_identification` SET `status` = 'Done' WHERE fieldlevelriskid='$fieldlevelriskid'");
    $history = capture_after_change('status', 'Done');
		add_update_history($dbc, 'safety_history', $history, '', $before_change);

    //$result_update_employee = mysqli_query($dbc, "UPDATE `safety_staff` SET `done` = 1 WHERE safetyid='$safetyid' AND staffid='$form_by' AND DATE(today_date) = CURDATE()");

    $today_date = $get_field_level['today_date'];
    $contactid = $get_field_level['contactid'];
    $location = $get_field_level['location'];
    $hazard_rating = $get_field_level['hazard_rating'];
    $action_timeline = $get_field_level['action_timeline'];
    $description = $get_field_level['description'];
    $action = $get_field_level['action'];
    $action_to = $get_field_level['action_to'];
    $est_comp = $get_field_level['est_comp'];
    $date_comp = $get_field_level['date_comp'];

    class MYPDF extends TCPDF {

        //Page header
        public function Header() {
            if(PDF_LOGO != '') {
                $image_file = 'download/'.PDF_LOGO;
                $this->Image($image_file, 10, 10, 30, '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->SetFont('helvetica', '', 8);
            $header_text = '';
            $this->writeHTMLCell(0, 0, '', '', $header_text, 0, 0, false, "L", "R",true);

        }

        // Page footer
        public function Footer() {
            // Position at 15 mm from bottom
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 8);
            $footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
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

    $html_weekly = '<h2>AVS Hazard Identification</h2>';

    $html_weekly .= '<table border="1px" style="padding:3px; border:1px solid black;">
            <tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">
            <th>Date</th><th>Location</th><th>Reported By</th></tr>';
    $html_weekly .= '<tr nobr="true"><td>'.$today_date.'</td><td>'.$location.'</td><td>'.$contactid.'</td></tr>';
    $html_weekly .= '</table>';

    $html_weekly .= "<h3>Hazard Rating</h3>  ".$hazard_rating;
    $html_weekly .= "<h3>Action Timeline</h3>  ".$action_timeline;

    $html_weekly .= "<h3>Description of Unsafe Acts/Conditions/Practices</h3>".html_entity_decode($description);
    $html_weekly .= "<h3>Action To Be Taken</h3>".html_entity_decode($action);

    $html_weekly .= "<h3>Action Assigned to</h3>  ".$action_to;
    $html_weekly .= "<h3>Estimated Completion Date</h3>  ".$est_comp;
    $html_weekly .= "<h3>Date Completed</h3>  ".$date_comp;

    $sa = mysqli_query($dbc, "SELECT * FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid'");

    $html_weekly .= '<br><br><table border="1px" style="padding:3px; border:1px solid black;">';
    $html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
        <th>Name</th>
        <th>Signature</th>
        <th>Review</th>
        </tr>';

    while($row_sa = mysqli_fetch_array( $sa )) {
        $assign_staff_id = $row_sa['safetyattid'];
        $staffcheck = $row_sa['staffcheck'];

        $html_weekly .= '<tr nobr="true">';
        $html_weekly .= '<td data-title="Email">' . $row_sa['assign_staff'] . '</td>';
        $html_weekly .= '<td data-title="Email"><img src="avs_hazard_identification/download/safety_'.$assign_staff_id.'.png" width="150" height="70" border="0" alt=""></td>';
        $html_weekly .= '<td data-title="Email">'.$staffcheck.'</td>';
        $html_weekly .= '</tr>';
    }
    $html_weekly .= '</table>';

    $pdf->writeHTML($html_weekly, true, false, true, false, '');
    $pdf->Output('avs_hazard_identification/download/hazard_'.$fieldlevelriskid.'.pdf', 'F');

    $sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid'");
    while($row_sa = mysqli_fetch_array( $sa )) {
        $assign_staff_id = $row_sa['safetyattid'];
        unlink("avs_hazard_identification/download/safety_".$assign_staff_id.".png");
    }
    echo '';
}
?>
