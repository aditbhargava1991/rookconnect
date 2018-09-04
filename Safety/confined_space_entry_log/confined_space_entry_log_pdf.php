<?php
function confined_space_entry_log_pdf($dbc,$safetyid, $fieldlevelriskid) {
    $form_by = $_SESSION['contactid'];

    $get_weekly = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM safety_confined_space_entry_log WHERE fieldlevelriskid='$fieldlevelriskid'"));

    $tab = get_safety($dbc, $safetyid, 'tab');
    $form = get_safety($dbc, $safetyid, 'form');

    $get_pdf_logo = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT pdf_logo FROM field_config_safety WHERE tab='$tab' AND form='$form'"));

    DEFINE('PDF_LOGO', $get_pdf_logo['pdf_logo']);
	DEFINE('PDF_HEADER', html_entity_decode($get_field_config['pdf_header']));
    DEFINE('PDF_FOOTER', html_entity_decode($get_field_config['pdf_footer']));

    $before_change = capture_before_change($dbc, 'safety_confined_space_entry_log', 'status', 'fieldlevelriskid', $fieldlevelriskid);
    $result_update_employee = mysqli_query($dbc, "UPDATE `safety_confined_space_entry_log` SET `status` = 'Done' WHERE fieldlevelriskid='$fieldlevelriskid'");
    $history = capture_after_change('status', 'Done');
		add_update_history($dbc, 'safety_history', $history, '', $before_change);
    //$result_update_employee = mysqli_query($dbc, "UPDATE `safety_staff` SET `done` = 1 WHERE safetyid='$safetyid' AND staffid='$form_by' AND DATE(today_date) = CURDATE()");

    $today_date = $get_weekly['today_date'];
    $fields = explode('**FFM**', $get_weekly['fields']);

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

    $html_weekly = '<h2>Confined Space Entry Log</h2>';

    $html_weekly .= '<table border="1px" style="padding:3px; border:1px solid black;">
            <tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="20%">Date</th><th width="40%">Job Number</th><th width="40%">Safety Watch</th></tr>';
    $html_weekly .= '<tr nobr="true"><td>'.$today_date.'</td><td>'.$fields[0].'</td><td>'.$fields[1].'</td></tr>
            <tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="50%">Client</th><th width="50%">Client Rep.</th></tr>';
    $html_weekly .= '<tr nobr="true"><td>'.$fields[2].'</td><td>'.$fields[3].'</td></tr>
        <tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="50%">Location</th><th width="50%">Supervisor</th></tr>';
    $html_weekly .= '<tr nobr="true"><td>'.$fields[4].'</td><td>'.$fields[5].'</td></tr>
        <tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="100%">Confined Space Description</th></tr>';
    $html_weekly .= '<tr nobr="true"><td>'.$fields[6].'</td></tr>
        <tr nobr="true" style="background-color:lightgrey; color:black;">
            <th width="50%">Start Date & Time</th><th width="50%">Finish Date & Time</th></tr>';
    $html_weekly .= '<tr nobr="true"><td>'.$fields[7].'</td><td>'.$fields[8].'</td></tr>';

    $html_weekly .= '</table>';

    $sa = mysqli_query($dbc, "SELECT * FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid'");

    $html_weekly .= '<br><br><table border="1px" style="padding:3px; border:1px solid black;">';
    $html_weekly .= '<tr nobr="true" style="background-color:lightgrey; color:black;">
        <th>Name</th>
        <th>Signature</th>
        <th>Time In</th>
        <th>Time Out</th>
        <th>Running Total</th>
        </tr>';

    while($row_sa = mysqli_fetch_array( $sa )) {
        $assign_staff_id = $row_sa['safetyattid'];

        $html_weekly .= '<tr nobr="true">';
        $html_weekly .= '<td data-title="Email">' . $row_sa['assign_staff'] . '</td>';
        $html_weekly .= '<td data-title="Email"><img src="confined_space_entry_log/download/safety_'.$assign_staff_id.'.png" width="150" height="70" border="0" alt=""></td>';
        $staffcheck = explode('*#*', $row_sa['staffcheck']);
        $html_weekly .= '<td data-title="Email">'.$staffcheck[0].'</td>';
        $html_weekly .= '<td data-title="Email">'.$staffcheck[1].'</td>';
        $html_weekly .= '<td data-title="Email">'.$staffcheck[2].'</td>';
        $html_weekly .= '</tr>';
    }
    $html_weekly .= '</table>';

    $pdf->writeHTML($html_weekly, true, false, true, false, '');
    $pdf->Output('confined_space_entry_log/download/hazard_'.$fieldlevelriskid.'.pdf', 'F');

    $sa = mysqli_query($dbc, "SELECT safetyattid FROM safety_attendance WHERE fieldlevelriskid = '$fieldlevelriskid' AND safetyid='$safetyid'");
    while($row_sa = mysqli_fetch_array( $sa )) {
        $assign_staff_id = $row_sa['safetyattid'];
        unlink("confined_space_entry_log/download/safety_".$assign_staff_id.".png");
    }
    echo '';
}
?>
