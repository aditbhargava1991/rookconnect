<?php

$projectid = $_POST['projectid'];
$ticket_category = $_POST['ticket_category'];

class MYPDF extends TCPDF {

    public function Header() {
        $this->setCellHeightRatio(0.7);
        $this->SetFont('helvetica', '', 9);
        $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);
    }

    // Page footer
    public function Footer() {
        // Position at 15 mm from bottom
        $this->SetY(-10);
        $this->SetFont('helvetica', 'I', 8);
        $footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().' printed on  '.date('m/d/y').' at '.date('g:i:s A');
        $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);

        // Position at 15 mm from bottom
        $this->SetY(-15);
        // Set font
        $this->SetFont('helvetica', '', 11);
    }
}

$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);

$report_data = '<h3>Project : '.get_project($dbc, $projectid, 'project_name').'</h3>';

if($ticket_category == 'Archive') {
    $query_check_credentials = "SELECT t.*, c.name FROM tickets t, contacts c WHERE t.businessid = c.contactid AND t.status = 'Archive' AND projectid='$projectid' ORDER BY ticketid DESC";
} else {
    $query_check_credentials = "SELECT t.*, c.name FROM tickets t, contacts c WHERE t.businessid = c.contactid AND t.status != 'Archive' AND projectid='$projectid' ORDER BY ticketid DESC";
}

$result = mysqli_query($dbc, $query_check_credentials);
$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
    $report_data .= '<table border="1px" class="table table-bordered" style="padding:3px; border:1px solid black;" >';
    $report_data .= '<tr>
        <th width="7%">'.TICKET_NOUN.'#</th>
        <th width="27%">Service</th>
        <th width="18%">'.TICKET_NOUN.' Heading</th>
        <th width="11%">TO DO</th>
        <th width="11%">Internal QA</th>
        <th width="11%">Deliverable</th>
        <th width="15%">Current Status</th>
        </tr>';
}
while($row = mysqli_fetch_array( $result )) {
    $report_data .= '<tr>';
    $clientid = $row['clientid'];
    $contactid = $row['contactid'];
    $ticketid = $row['ticketid'];

    $report_data .= '<td data-title="'.TICKET_NOUN.'#">#' . $ticketid . '</td>';

    $report_data .= '<td data-title="Service">' . $row['service_type'].'<br>'.$row['service'] .'<br>'.$row['sub_heading'] . '</td>';
    $report_data .= '<td data-title="'.TICKET_NOUN.' Heading">' . $row['heading'] . '</td>';

    $report_data .= '<td data-title="TO DO">' . $row['to_do_date'].'<br>'.$row['to_do_end_date'].'</td>';
    $report_data .= '<td data-title="Internal QA">' . $row['internal_qa_date'].'</td>';
    $report_data .= '<td data-title="Deliverable">' . $row['deliverable_date'].'</td>';

    $report_data .= '<td data-title="Current Status">' . $row['status'] . '</td>';
    $report_data .= "</tr>";
}

$report_data .= '</table>';

$pdf->writeHTML($report_data, true, 0, true, 0);

$pdf->Output('download/project_'.$projectid.'_'.date('Y-m-d').'.pdf', 'F');
$today_date = date("Y-m-d");
echo '<script type="text/javascript" language="Javascript">
    window.open("download/project_'.$projectid.'_'.$today_date.'.pdf", "fullscreen=yes");
    </script>';
