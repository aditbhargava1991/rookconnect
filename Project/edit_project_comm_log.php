<?php error_reporting(0);
include_once('../include.php');
include_once('../Project/config.php');
$_POST['date_end'] = empty($_POST['date_end']) ? date('Y-m-d') : date('Y-m-d',strtotime($_POST['date_end']));
if($_POST['output'] == 'PDF') {
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
            $footer_text = 'Communication Log';
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
    $report = comm_log_report($dbc, $projectid, $_POST['type'], $_POST['contact'], $_POST['date_start'], $_POST['date_end']);
    $pdf->writeHTML('<h4 style="text-align:center;">Communication '.(empty($_POST['date_start']) ? '' : 'From '.$_POST['date_start'].' ').'Until '.$_POST['date_end'].'</h4>', true, false, true, false, '');
    $pdf->writeHTML($report, true, false, true, false, '');

    $pdf->Output('communication_log_'.$projectid.'_at_'.$today_date.'.pdf', 'I');
    exit();
} ?>
<script>
function reload_comm_log(mode) {
    $.post('edit_project_comm_log.php?edit=<?= $projectid ?>&tile_name=<?= $_GET['tile_name'] ?>', {
        type: $('.comm_log_table [name=type]').val(),
        date_start: $('.comm_log_table [name=start]').val(),
        date_end: $('.comm_log_table [name=end]').val(),
        contact: $('.comm_log_table [name=contact]').val()
    }, function(response) {
        var history = $(response).filter('.comm_log_table');
        $('.comm_log_table').remove();
        destroyInputs();
        $('#head_comm_log').after(history);
        initInputs();
    });
}
</script>
<h3 id="head_comm_log">Communication History</h3>
<div id="no-more-tables" class="comm_log_table">
    <form class="form-inline" method="POST" action="">
        <div class="col-sm-5 col-md-4 col-lg-2 pull-right">
            <button class="btn brand-btn pull-right" onclick="$('.comm_log_table input,.comm_log_table select').val(''); reload_comm_log(); return false;">Display All</button>
            <button class="btn brand-btn pull-right" onclick="reload_comm_log(); return false;">Search</button>
            <input type="image" name="output" value="PDF" class="cursor-hand inline-img no-toggle pull-right" title="Download PDF" src="../img/pdf.png">
        </div>
        <div class="col-sm-7 col-md-4 col-lg-5">
            <label class="col-sm-4">Type:</label>
            <div class="col-sm-8">
                <select name="type" class="chosen-select-deselect" data-placeholder="Select Communication Type"><option />
                    <?= (in_array('Email',$tab_config) ? '<option '.($_POST['type'] == 'Email' ? 'selected' : '').' value="Email">Email</option>' : '') ?>
                    <?= (in_array('Phone',$tab_config) ? '<option '.($_POST['type'] == 'Phone' ? 'selected' : '').' value="Phone">Phone</option>' : '') ?>
                    <?= (in_array_any(['Email','Phone'],$tab_config) ? '<option '.($_POST['type'] == 'Internal' ? 'selected' : '').' value="Internal">Internal</option>' : '') ?>
                    <?= (in_array_any(['Email','Phone'],$tab_config) ? '<option '.($_POST['type'] == 'External' ? 'selected' : '').' value="External">External</option>' : '') ?>
                    <?= (in_array('Agendas',$tab_config) ? '<option '.($_POST['type'] == 'Agendas' ? 'selected' : '').' value="Agendas">Agendas</option>' : '') ?>
                    <?= (in_array('Meetings',$tab_config) ? '<option '.($_POST['type'] == 'Meetings' ? 'selected' : '').' value="Meetings">Meetings</option>' : '') ?>
                </select>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-sm-7 col-md-4 col-lg-5">
            <label class="col-sm-4"><?= CONTACTS_TILE ?>:</label>
            <div class="col-sm-8">
                <select name="contact" class="chosen-select-deselect" data-placeholder="Select <?= CONTACTS_NOUN ?>"><option />
                    <?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `category`, `name`, `first_name`, `last_name` FROM `contacts` LEFT JOIN (SELECT CONCAT(',',IFNULL(`businessid`,''),',',IFNULL(`businesscontactid`,''),',',IFNULL(`companycontactid`,''),',') `id_list` FROM `agenda_meeting` WHERE `deleted`=0 UNION SELECT CONCAT(',',IFNULL(`businessid`,''),',',IFNULL(`contactid`,''),',',IFNULL(`created_by`,''),',') `id_list` FROM `email_communication` WHERE `deleted`=0 UNION SELECT CONCAT(',',IFNULL(`businessid`,''),',',IFNULL(`contactid`,''),',',IFNULL(`created_by`,''),',') `id_list` FROM `phone_communication` WHERE `deleted`=0) `id_list` ON `id_list`.`id_list` LIKE CONCAT('%,',`contacts`.`contactid`,',%') WHERE `id_list`.`id_list` IS NOT NULL AND `contacts`.`deleted`=0 AND `contacts`.`status` > 0 GROUP BY `contacts`.`contactid`")) as $contact) { ?>
                        <option <?= $_POST['contact'] == $contact['contactid'] ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['full_name'] ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-sm-7 col-md-4 col-lg-5">
            <label class="col-sm-4">Start Date:</label>
            <div class="col-sm-8">
                <input name="start" type="text" class="form-control datepicker" value="<?= $_POST['date_start'] ?>">
            </div>
            <div class="clearfix"></div>
        </div>
        <div class="col-sm-7 col-md-4 col-lg-5">
            <label class="col-sm-4">End Date Date:</label>
            <div class="col-sm-8">
                <input name="end" type="text" class="form-control datepicker" value="<?= $_POST['date_end'] ?>">
            </div>
            <div class="clearfix"></div>
        </div>
    </form>
    <div class="clearfix"></div><br />
    <?= comm_log_report($dbc, $projectid, $_POST['type'], $_POST['contact'], $_POST['date_start'], $_POST['date_end']) ?>
</div>
<?php function comm_log_report($dbc, $projectid, $type, $contact, $start, $end) {
    $type = filter_var($type,FILTER_SANITIZE_STRING);
    $contact = empty($contact) ? '%' : filter_var($contact,FILTER_SANITIZE_STRING);
    $start = empty($start) ? '0000-00-00' : filter_var($start,FILTER_SANITIZE_STRING);
    $end = filter_var($end,FILTER_SANITIZE_STRING);
    $report_meetings = $dbc->query("SELECT * FROM `agenda_meeting` WHERE `projectid`='$projectid' AND `projectid` > 0 AND '$type' IN (`type`,'') AND `date_of_meeting` BETWEEN '$start' AND '$end' AND CONCAT(',',IFNULL(`businessid`,''),',',IFNULL(`businesscontactid`,''),',',IFNULL(`companycontactid`,''),',') LIKE '%,$contact,%' AND `deleted`=0");
    $report_email = $dbc->query("SELECT * FROM `email_communication` WHERE `projectid`='$projectid' AND `projectid` > 0 AND '$type' IN (`communication_type`,'') AND `today_date` BETWEEN '$start' AND '$end' AND CONCAT(',',IFNULL(`businessid`,''),',',IFNULL(`contactid`,''),',',IFNULL(`created_by`,''),',') LIKE '%,$contact,%' AND `deleted`=0");
    $report_phone = $dbc->query("SELECT * FROM `phone_communication` WHERE `projectid`='$projectid' AND `projectid` > 0 AND '$type' IN (`communication_type`,'') AND CONCAT(',',IFNULL(`businessid`,''),',',IFNULL(`contactid`,''),',',IFNULL(`created_by`,''),',') LIKE '%,$contact,%' AND `deleted`=0");
    if($report_meetings->num_rows+$report_email->num_rows+$report_phone->num_rows > 0) {
        $row_colour1 = get_config($dbc, 'report_row_colour_1');
        $row_colour2 = get_config($dbc, 'report_row_colour_2');
        $report = '<table class="table table-bordered" border="1" cellpadding="3" cellspacing="0">';
        $report .= '<tr class="hidden-xs hidden-sm">
            <th>'.CONTACTS_NOUN.' ID</th>
            <th>'.CONTACTS_NOUN.' Name</th>
            <th>Communication Type</th>
            <th>Date</th>
            <th>Notes</th>
            <th>Created</th>
            <th>Created By</th>
        </tr>';
        $i = 0;
        while($row = $report_meetings->fetch_assoc()) {
            $row_contacts = [];
            $row_ids = [];
            foreach(explode(',',$row['businessid'].','.$row['businesscontactid'].','.$row['companycontactid']) as $contactid) {
                if($contactid > 0) {
                    $row_contacts[] = get_contact($dbc, $contactid, 'name_company');
                    $row_ids[] = $contactid;
                }
            }
            $report .= '<tr style="background-color: '.($i++ % 2 == 1 ? $row_colour1 : $row_colour2).'">
                <td data-title="'.CONTACTS_NOUN.' ID">'.implode(', ',$row_ids).'</td>
                <td data-title="'.CONTACTS_NOUN.' Name">'.implode(', ',$row_contacts).'</td>
                <td data-title="Communication Type">'.$row['type'].'</td>
                <td data-title="Date">'.$row['date_of_meeting'].'</td>
                <td data-title="Notes">'.strip_tags(str_replace(['<br>','<br />'],["\n","\n"],html_entity_decode($row['meeting_note']))).'</td>
                <td data-title="Created"></td>
                <td data-title="Created By"></td>
            </tr>';
        }
        while($row = $report_email->fetch_assoc()) {
            $row_contacts = [];
            $row_ids = [];
            foreach(explode(',',$row['businessid'].','.$row['contactid'].','.$row['created_by']) as $contactid) {
                if($contactid > 0) {
                    $row_contacts[] = get_contact($dbc, $contactid, 'name_company');
                    $row_ids[] = $contactid;
                }
            }
            $report .= '<tr style="background-color: '.($i++ % 2 == 1 ? $row_colour1 : $row_colour2).'">
                <td data-title="'.CONTACTS_NOUN.' ID">'.implode(', ',$row_ids).'</td>
                <td data-title="'.CONTACTS_NOUN.' Name">'.implode(', ',$row_contacts).'</td>
                <td data-title="Communication Type">'.$row['communication_type'].' Email</td>
                <td data-title="Date">'.$row['today_date'].'</td>
                <td data-title="Notes"><b>'.$row['subject'].'</b><br />'.strip_tags(str_replace(['<br>','<br />'],["\n","\n"],html_entity_decode($row['email_body']))).'</td>
                <td data-title="Created">'.$row['today_date'].'</td>
                <td data-title="Created By">'.get_contact($dbc, $row['created_by'],'name_company').'</td>
            </tr>';
        }
        while($row = $report_phone->fetch_assoc()) {
            $row_contacts = [];
            $row_ids = [];
            foreach(explode(',',$row['businessid'].','.$row['contactid'].','.$row['created_by']) as $contactid) {
                if($contactid > 0) {
                    $row_contacts[] = get_contact($dbc, $contactid, 'name_company');
                    $row_ids[] = $contactid;
                }
            }
            $report .= '<tr style="background-color: '.($i++ % 2 == 1 ? $row_colour1 : $row_colour2).'">
                <td data-title="'.CONTACTS_NOUN.' ID">'.implode(', ',$row_ids).'</td>
                <td data-title="'.CONTACTS_NOUN.' Name">'.implode(', ',$row_contacts).'</td>
                <td data-title="Communication Type">'.$row['communication_type'].' Phone Call</td>
                <td data-title="Date"></td>
                <td data-title="Notes"><b>'.strip_tags(str_replace(['<br>','<br />'],["\n","\n"],html_entity_decode($row['comment']))).'</td>
                <td data-title="Created"></td>
                <td data-title="Created By">'.get_contact($dbc, $row['created_by'],'name_company').'</td>
            </tr>';
        }
        $report .= '</table>';
    } else {
        $report = '<h3>No Communication Found</h3>';
    }
    return $report;
}