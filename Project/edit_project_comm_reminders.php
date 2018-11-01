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
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project'); ?>
<!-- <h3>Reminders</h3> -->
<div class="notice double-gap-top double-gap-bottom popover-examples">
    <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
    <div class="col-sm-11"><span class="notice-name">NOTE: </span>Set reminders for staff for specific dates and times, and view past and current reminders for this project. Reminders will display based on the From - To Dates selected, or you can view all by clicking Display All.</div>
    <div class="clearfix"></div>
</div>
<?php
$search_from = '';
$search_to = '';
$clause = '';
if(!empty($_POST['search_from'])) {
	$search_from = $_POST['search_from'];
	$clause .= " AND reminder_date >= '$search_from'";
}
if(!empty($_POST['search_to'])) {
	$search_to = $_POST['search_to'];
	$clause .= " AND reminder_date <= '$search_to'";
}
?>
<form method="post" action="">
	<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
		<label for="search_from" class="control-label">Search From Date:</label>
	</div>
	<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
		<input type="text" name="search_from" class="form-control datepicker" value="<?php echo $search_from; ?>">
	</div>
	<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
		<label for="search_from" class="control-label">Search To Date:</label>
	</div>
	<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
		<input type="text" name="search_to" class="form-control datepicker" value="<?php echo $search_to; ?>">
	</div>
	<div class="col-sm-8 col-xs-12 col-lg-6 pad-top pull-xs-right">
		<span class="popover-examples no-gap-pad"><a data-toggle="tooltip" data-placement="top" title="Click here after you have entered From - To Dates."><img src="../img/info.png" width="20"></a></span>
		<button type="submit" name="search_contacts_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
		<span class="popover-examples list-inline"><a style="margin:5px 0 0 15px;" data-toggle="tooltip" data-placement="top" title="Refreshes the page to display all Project Reminders."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<a href="" class="btn brand-btn mobile-block">Display All</a>
	</div>
</form>
<?php if(vuaed_visible_function($dbc, 'project') == 1) {
	echo '<a href="edit_reminder.php?project_id='.$projectid.'" class="btn brand-btn mobile-block pull-right">Add Reminder</a>';
	echo '<span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Reminder."><img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
}
echo '<a href="?edit='.$projectid.'&tab=reminders&output=PDF" class="pull-right"><img src="../img/pdf.png" class="inline-img"></a>'; ?>
<div class="clearfix"></div>
<?php $rowsPerPage = 25;
$pageNum = (empty($_GET['page']) ? 1 : $_GET['page']);
$offset = $rowsPerPage * ($pageNum - 1);
$reminders = mysqli_query($dbc, "SELECT * FROM `reminders` WHERE `reminder_type` = 'PROJECT".$projectid."' AND `deleted`=0 $clause LIMIT $offset, $rowsPerPage");
$sql_num = "SELECT COUNT(*) numrows FROM `reminders` WHERE `reminder_type` = 'PROJECT".$projectid."' AND `deleted`=0 $clause";
$table = '';
if(mysqli_num_rows($reminders) == 0)
{
	$table .= "<h1>No Reminders Found</h1>";
}
else {
	display_pagination($dbc, $sql_num, $pageNum, $rowsPerPage);
    $table .= '<table class="table table-bordered" style="width:100%;" cellpadding="3" cellspacing="0" border="1">
		<tr>
			<th>Staff Members</th>
			<th>Reminder Date</th>
			<th>Subject</th>
			<th>Sent</th>
			<th>Function</th>
		</tr>';
		while($reminder = mysqli_fetch_array($reminders)) {
			$table .= '<tr>
				<td data-title="Staff">';
                $staff = explode(',',$reminder['contactid']);
					foreach($staff as $person) {
							$table .= get_staff($dbc, $person)."<br />\n";
					}
                    $table .= '</td>
				<td data-title="Date">'.$reminder['reminder_date'].' ('.$reminder['reminder_time'].')</td>
				<td data-title="Subject">'.$reminder['subject'].'</td>
				<td data-title="Sent">'.($reminder['sent'] == 1 ? 'Sent' : 'Not Sent').'</td>
				<td data-title="">'.(vuaed_visible_function($dbc, 'Staff') ? '<a href="edit_reminder.php?reminderid='.$reminder['reminderid'].'&project_id='.$projectid.'">Edit</a>' : '').'</td>
			</tr>';
		}
	$table .= '</table>';
display_pagination($dbc, $sql_num, $pageNum, $rowsPerPage); 
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
            $footer_text = PROJECT_NOUN.' Reminders';
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
    $pdf->Output(config_safe_str(PROJECT_NOUN).'_'.$projectid.'_reminders_'.$today_date.'.pdf', 'I');
    exit();
} else {
    echo $table;
}
include('next_buttons.php'); ?>