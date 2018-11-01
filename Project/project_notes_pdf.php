<?php include_once('../include.php');
include_once('../tcpdf/tcpdf.php');

$html = '';
$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
$note_start_date = (empty($_GET['start_date']) ? '0000-00-00' : date('Y-m-d',strtotime($_GET['start_date'])));
$note_end_date = (empty($_GET['end_date']) ? '9999-12-31' : date('Y-m-d',strtotime($_GET['end_date'])));
$project_notes = mysqli_query($dbc, "SELECT * FROM `project_comment` WHERE `projectid`='$projectid' AND '$projectid' > 0 AND `type` NOT LIKE 'detail_%' AND `created_date` BETWEEN '".$note_start_date."' AND '".$note_end_date."'");
if(mysqli_num_rows($project_notes) > 0) {
    $odd_even = 0;
    $html .= '<table class="table table-bordered">
        <tr class="hidden-xs hidden-sm">
            <th>Date Created</th>
            <th>Tagged</th>
            <th>Sent To</th>
            <th>Created By</th>
            <th>Note</th>
        </tr>';
        while($note = mysqli_fetch_assoc($project_notes)) {
            $bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg';
            $html .= '<tr class="'.$bg_class.'">
                <td data-title="Date Created">'.$note['created_date'].'</td>
                <td data-title="Tagged">'.get_contact($dbc, $note['email_comment']).'</td>
                <td data-title="Sent To">'.get_contact($dbc, $note['email_comment']).'</td>
                <td data-title="Created By">'.get_contact($dbc, $note['created_by']).'</td>
                <td data-title="Note">'.html_entity_decode($note['comment']).'</td>
            </tr>';
            $odd_even++;
        }
    $html .= '</table>';
} else {
    $html .= '<h3>No Notes Found</h3>';
}

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'LETTER', true, 'UTF-8', false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetFooterMargin(10);
$pdf->SetAutoPageBreak(FALSE, 0);
$pdf->SetFont('helvetica', '', 8);
$pdf->setPrintFooter(false);
$pdf->AddPage();

$pdf->writeHTML($html);
// if(!file_exists('download')) {
    // mkdir('download', 0777, true);
// }
// $file_name = 'download/project_'.$projectid.'_notes'.date('Y_m_d_h_i').'.pdf';
ob_clean();
$pdf->Output($file_name, 'I');