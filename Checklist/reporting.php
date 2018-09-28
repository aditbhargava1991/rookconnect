<?php if (isset($_POST['printpdf'])) {
	include_once('../tcpdf/tcpdf.php');
    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];
    $checklistpdf = $_POST['checklistpdf'];

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);
    DEFINE('CHECKLIST', $checklistpdf);
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
            $footer_text = 'Checklist Report From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
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

    $html .= report_checklist($dbc, $starttimepdf, $endtimepdf, $checklistpdf, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $today_date = date('Y-m-d');
    $pdf->writeHTML($html, true, false, true, false, '');
    $pdf->Output('download/checklist_report_'.$today_date.'.pdf', 'F');
    ?><script>
		window.open('download/checklist_report_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
    </script><?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
	$checklist = $checklistpdf;
} ?>

<script>
    $(document).ready(function() {
        $('#print_report').click(function() {
            $('[name=printpdf]').click();
        });
    });
</script>

<div class="standard-body-title">
    <h3 class="pull-left">Reporting</h3>
    <div class="pull-right"><img id="print_report" src="../img/icons/pdf.png" alt="Print Report" title="Print Report" class="no-toggle cursor-hand offset-top-15 offset-right-15" width="30" /></div>
    <div class="clearfix"></div>
</div>

<form name="form_sites" method="post" action="" class="form-horizontal" role="form">
	<?php if (isset($_POST['search_checklist_report'])) {
		$starttime = $_POST['starttime'];
		$endtime = $_POST['endtime'];
		$checklist = $_POST['checklist'];
	}
	if($starttime == 0000-00-00) {
		$starttime = date('Y-m-d', strtotime('-1month'));
	}
	if($endtime == 0000-00-00) {
		$endtime = date('Y-m-d');
	}
	if($checklist == 'ALL') {
		$checklist == '';
	} ?>

	<div class="form-group padded">
		<div class="row">
			<label class="col-sm-2 control-label">From:</label>
            <div class="col-sm-4"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
			<label class="col-sm-2 control-label">Until:</label>
            <div class="col-sm-4"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
			<label class="col-sm-2 control-label">Checklist:</label>
            <div class="col-sm-4">
                <select data-placeholder="Select Checklist" name="checklist" class="chosen-select-deselect">
                    <option <?= empty($checklist) ? 'selected' : '' ?> value="ALL">All Checklists</option>
                    <?php $checklists = mysqli_query($dbc, "SELECT `checklist_name` FROM `checklist` WHERE `deleted`=0");
                    while($list = htmlentities(mysqli_fetch_assoc($checklists)['checklist_name'])) { ?>
                        <option <?= $checklist == $list ? 'selected' : '' ?> value="<?= $list ?>"><?= $list ?></option>
                    <?php } ?>
                </select>
            </div>
            <label class="col-sm-2 control-label"></label>
            <div class="col-sm-4">
                <button type="submit" name="search_checklist_report" value="Search" class="btn brand-btn mobile-block pull-right">Submit</button>
                <a href="" class="btn brand-btn mobile-block pull-right">Current</a>
            </div>
		</div>
        
		<input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
		<input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
	</div>
    
    <button type="submit" name="printpdf" value="Print Report" class="pull-right gap-right gap-bottom image-btn no-toggle" title="Print Report" style="visibility:hidden;"><img src="../img/icons/pdf.png" alt="Print Report" width="30" /></button>
    
    <div class="clearfix"></div>

    <div id="no-more-tables">
        <?php echo report_checklist($dbc, $starttime, $endtime, $checklist, '', '', ''); ?>
    </div>
</form><?php

function report_checklist($dbc, $starttime, $endtime, $checklist, $table_style, $table_row_style, $grand_total_style) {
    $query_filters = '';

    if (!empty($_GET['subtabid'])) {
        $subtabid = $_GET['subtabid'];
        $query_filters .= " AND `subtabid` = '$subtabid'";
    }
    if (!empty($_GET['checklist_type'])) {
        $type = $_GET['type'];
        $query_filters .= " AND `checklist_type` = '$type'";
    }
    if ($checklist != '') {
        $query_filters .= " AND `checklist_name` = '".filter_var(html_entity_decode($checklist),FILTER_SANITIZE_STRING)."'";
    }

    $query_check_credentials = "SELECT * FROM `checklist_report` WHERE `date` >= '$starttime' AND `date` <= '$endtime' $query_filters ORDER BY `checklistreportid` DESC";

    $result = mysqli_query($dbc, $query_check_credentials) or die(mysqli_error($dbc));

    $num_rows = mysqli_num_rows($result);
    if($num_rows > 0) {
        $report_data .= '<table border="1px" class="table table-bordered table-striped" style="'.$table_style.'">';
        $report_data .= '<thead><tr style="'.$table_row_style.'"><th>Date</th>
            <th>User</th>
            <th>Sub Tab</th>
            <th>Checklist Type</th>
            <th>Checklist Name</th>
            <th>Report</th>
            </tr></thead>';
    
		while($row = mysqli_fetch_array( $result ))
		{
			$user = $row['user'];
			$date = $row['date'];
			$subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_subtab` WHERE `subtabid` = '" . $row['subtabid'] . "'"))['name'];
			$query_checklist = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid` = '" . $row['checklistid'] . "'"));
			$checklist_type = $query_checklist['checklist_type'];
			$checklist_name = $query_checklist['checklist_name'];

			$pos1 = strlen($user) + 1;
			$pos2 = strrpos($row['report'], "on") - $pos1 - 1;
			$report = substr($row['report'], $pos1, $pos2);

			$report_data .= '<tr nobr="true">';
			$report_data .= '<td data-title="Date">' . $date . '</td>';
			$report_data .= '<td data-title="User">' . $user . '</td>';
			$report_data .= '<td data-title="Subtab">' . $subtab . '</td>';
			$report_data .= '<td data-title="Checklist Type">' . $checklist_type . '</td>';
			$report_data .= '<td data-title="Checklist Name">' . $checklist_name . '</td>';
			$report_data .= '<td data-title="Report">' . $report . '</td>';
			$report_data .= "</tr>";
		}

		$report_data .= '</table>';
    } else {
        $report_data = "<h2>No Record Found.</h2>";
    }
    return $report_data;
}