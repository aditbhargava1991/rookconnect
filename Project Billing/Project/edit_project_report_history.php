<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
    $security = get_security($dbc, $tile);
    $strict_view = strictview_visible_function($dbc, 'project');
    if($strict_view > 0) {
        $security['edit'] = 0;
        $security['config'] = 0;
    }
}
if(!isset($projectid)) {
    $projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
    foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
        if($tile == 'project' || $tile == config_safe_str($type_name)) {
            $project_tabs[config_safe_str($type_name)] = $type_name;
        }
    }
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'")); ?>
<h1><?= get_project_label($dbc, $project) ?> History</h1>
<a href="?edit=<?= $projectid ?>&tab=history&output=PDF" class="pull-right"><img src="../img/pdf.png" class="inline-img"></a>
<?php $table = '';
$history = mysqli_query($dbc, "SELECT * FROM `project_history` WHERE `projectid`='$projectid' ORDER BY `updated_at` ASC");
if($history->num_rows > 0) {
    $table .= '<div id="no-more-tables"><table class="table table-bordered" style="width:100%;" cellpadding="3" cellspacing="0" border="1">
        <tr class="hidden-sm hidden-xs">
            <th>User</th>
            <th>Date of Change</th>
            <th>History</th>
        </tr>';
    while($row = $history->fetch_assoc()) {
        $table .= '<tr>
            <td data-title="User">'.$row['updated_by'].'</td>
            <td data-title="Date">'.date('F j, Y H:i',strtotime($row['updated_at'])).'</td>
            <td data-title="History">'.html_entity_decode($row['description']).'</td>
        </tr>';
    }
    $table .= '</table></div>';
} else {
    $table .= "<h3>No History Found.</h3>";
}
if($_GET['output'] == 'PDF') {
    include('../tcpdf/tcpdf.php');
    ob_clean();
    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));
    class MYPDF extends TCPDF {
        public function Header() {
            //$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
            if(REPORT_LOGO != '') {
                $image_file = 'download/'.REPORT_LOGO;
                $this->Image($image_file, 10, 10, '', '', '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);
            $this->SetFont('helvetica', '', 13);
            $footer_text = PROJECT_NOUN.' History';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);
        }
        // Page footer
        public function Footer() {
            $this->SetY(-24);
            $this->SetFont('helvetica', 'I', 9);
            $footer_text = '<span style="text-align:left;">'.REPORT_FOOTER.'</span>';
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
            // Position at 15 mm from bottom
            $this->SetY(-15);
            $this->SetFont('helvetica', 'I', 9);
            $footer_text = '<span style="text-align:right;">Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages().' printed on '.date('Y-m-d H:i:s').'</span>';
            $this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);
        }
    }
    $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
    $pdf->setFooterData(array(0,64,0), array(0,64,128));
    $pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    $pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);
    $pdf->writeHTML($table, true, false, true, false, '');
    $pdf->Output(config_safe_str(PROJECT_NOUN).'_'.$projectid.'_history_'.$today_date.'.pdf', 'I');
    exit();
} else {
    echo $table;
} ?>
<?php include('next_buttons.php'); ?>