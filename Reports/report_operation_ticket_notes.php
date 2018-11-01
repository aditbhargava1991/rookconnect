<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
if (isset($_POST['printpdf'])) {
	$ticketid = $_POST['report_ticket'];
	$projectid = $_POST['report_project'];
	$from_date = $_POST['report_from'];
	$until_date = $_POST['report_until'];
    $today_date = date('Y-m-d');
	$pdf_name = "Download/".config_safe_str(TICKET_NOUN)."_archive_notes_$today_date.pdf";

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
            $footer_text = 'Archived '.TICKET_NOUN.' Notes';
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

	$html = '<h3>Report Date: '.$from_date.($until_date == $from_date ? '' : ' to '.$until_date).'</h3>';
    $html .= shop_work_orders($dbc, $from_date, $until_date, $ticketid, $projectid, true, 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output($pdf_name, 'F');
    track_download($dbc, 'report_operation_ticket_notes', 0, WEBSITE_URL.'/Reports/Download/'.config_safe_str(TICKET_NOUN).'_archive_notes_'.$today_date.'.pdf', 'Archived Notes Report');
    ?>

	<script>
		window.location.replace('<?php echo $pdf_name; ?>');
	</script>
<?php } ?>


        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-inline" role="form">

            <?php $search_ticket = '';
			$search_project = '';
			$search_task = '';
			$search_from = date('Y-m-01');
			$search_until = date('Y-m-d');

            if (isset($_POST['search_ticket'])) {
                $search_ticket = $_POST['search_ticket'];
            }
            if (isset($_POST['search_project'])) {
                $search_project = $_POST['search_project'];
            }
            if (isset($_POST['search_task'])) {
                $search_task = $_POST['search_task'];
            }
            if (isset($_POST['search_from'])) {
                $search_from = $_POST['search_from'];
            }
            if (isset($_POST['search_until'])) {
                $search_until = $_POST['search_until'];
            } ?>

			<div class="col-sm-5">
				<label for="search_ticket" class="col-sm-4 control-label">Search By <?= TICKET_NOUN ?>:</label>
				<div class="col-sm-8">
					<select data-placeholder="Select a <?= TICKET_NOUN ?> #" name="search_ticket" class="chosen-select-deselect form-control">
						<option value=""></option>
						<?php
						$query = mysqli_query($dbc,"SELECT `ticketid`, `heading` FROM `tickets` WHERE `deleted`=0 ORDER BY `ticketid`");
						while($row = mysqli_fetch_array($query)) { ?>
							<option <?php if ($row['ticketid'] == $search_ticket) { echo " selected"; } ?> value='<?php echo  $row['ticketd']; ?>' ><?php echo TICKET_NOUN.' #'.$row['ticketid'].' '.$row['heading']; ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="col-sm-5">
				<label for="search_project" class="col-sm-4 control-label">Search By <?= PROJECT_NOUN ?>:</label>
				<div class="col-sm-8">
					<select data-placeholder="Select a <?= PROJECT_NOUN ?>" name="search_project" class="chosen-select-deselect form-control">
						<option value=""></option>
						<?php $query = mysqli_query($dbc, "SELECT `projectid`, `project_name` FROM `project` WHERE `deleted`=0 AND `status` NOT IN ('Pending')");
						while($row = mysqli_fetch_assoc($query)) { ?>
							<option <?php if ($row['projectid'] == $search_project) { echo " selected"; } ?> value='<?php echo  $row['projectid']; ?>' ><?= PROJECT_NOUN.' #'.$row['projectid'].' '.$row['project_name'] ?></option>
						<?php } ?>
					</select>
				</div>
			</div>

			<div class="col-sm-5">
				<label for="search_from" class="col-sm-4 control-label">Search From Date:</label>
				<div class="col-sm-8 col-xs-8">
					<input type="text" class="form-control datepicker" name="search_from" value="<?php echo $search_from; ?>" style="width:100;">
				</div>
			</div>
			<div class="col-sm-5">
				<label for="search_until" class="col-sm-4 control-label">Search Until Date:</label>
				<div class="col-sm-8 col-xs-8">
					<input type="text" class="form-control datepicker" name="search_until" value="<?php echo $search_until; ?>" style="width:100;">
				</div>
			</div>

			<div class="col-sm-2">
				<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
				<button type="button" onclick="window.location=''" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display Current</button>
				<button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
			</div>

            <input type="hidden" name="report_ticket" value="<?php echo $search_ticket; ?>">
            <input type="hidden" name="report_project" value="<?php echo $search_project; ?>">
            <input type="hidden" name="report_from" value="<?php echo $search_from; ?>">
            <input type="hidden" name="report_until" value="<?php echo $search_until; ?>">
            <div class="clearfix"></div>

            <?= shop_work_orders($dbc, $search_from, $search_until, $search_ticket, $search_project, $search_task) ?>

        </form>

<?php
function shop_work_orders($dbc, $search_from, $search_until, $search_ticket, $search_project, $no_page = false, $table_style = '', $table_row_style = '', $grand_total_style = '') {
    $report_data = '';

	$rowsPerPage = 25;
	$pageNum = 1;
	$limit = '';
	if(isset($_GET['page'])) {
		$pageNum = $_GET['page'];
	}
	$offset = ($pageNum - 1) * $rowsPerPage;
	if($no_page === false) {
		$limit = " LIMIT $offset, $rowsPerPage";
	}

	$from = '';
	if(!$no_page) {
		$from = '&from='.urlencode(WEBSITE_URL.'/Reports/report_operation_ticket_notes.php?type=operations');
	}

	$sql = "SELECT * FROM `ticket_comment` LEFT JOIN `tickets` ON `ticket_comment`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_comment`.`deleted`=1 AND `tickets`.`deleted`=0 AND '$search_ticket' IN ('',`tickets`.`ticketid`) AND '$search_project' IN ('',`tickets`.`projectid`) AND ('$search_until' = '' OR `ticket_comment`.`created_date` <= '$search_until') AND ('$search_from' = '' OR `ticket_comment`.`created_date` >= '$search_from') $limit";
	$query = "SELECT COUNT(*) numrows FROM `ticket_comment` LEFT JOIN `tickets` ON `ticket_comment`.`ticketid`=`tickets`.`ticketid` WHERE `ticket_comment`.`deleted`=1 AND `tickets`.`deleted`=0 AND '$search_ticket' IN ('',`tickets`.`ticketid`) AND '$search_project' IN ('',`tickets`.`projectid`) AND ('$search_until' = '' OR `ticket_comment`.`created_date` <= '$search_until') AND ('$search_from' = '' OR `ticket_comment`.`created_date` >= '$search_from')";
    $result = mysqli_query($dbc,$sql);
	if($no_page === false) {
		echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	}

	if(mysqli_num_rows($result) > 0) {
		$report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
		$report_data .= '<tr style="'.$table_row_style.'">';
		$report_data .= '<th>'.TICKET_NOUN.' #</th>';
		$report_data .= '<th>Notes / Comment</th>';
		$report_data .= '<th>Assigned To / References</th>';
		$report_data .= '<th>Date</th>';
		$report_data .= '<th>User</th>';
		$report_data .=  "</tr>";

		while($row = mysqli_fetch_array( $result )) {
			$report_data .= '<tr nobr="true">';
			$report_data .=  '<td data-title="'.TICKET_NOUN.' #">'.($edit_ticket > 0 ? '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].$from.'">' : '').get_ticket_label($dbc, $row).($edit_ticket > 0 ? '</a>' : '').'</td>';
			$report_data .=  '<td data-title="Description">' . html_entity_decode($row['comment']). '</td>';
			$report_data .=  '<td data-title="Assigned To / References">';
			$contacts = [];
			foreach(explode(',',$row['email_comment']) as $userid) {
				if($userid > 0) {
					$contacts[] = get_contact($dbc, $userid);
				}
			}
			$report_data .= implode('<br />',$contacts).'</td>';
			$report_data .=  '<td data-title="Date">' . $row['created_date'] . '</td>';
			$report_data .=  '<td data-title="User">' . get_contact($dbc, $row['created_by']) . '</td>';
			$report_data .=  "</tr>";
		}

		$report_data .=  '</table>';
	} else {
		$report_data .=  '<h3>No Comments Found</h3>';
	}

    return $report_data;
}
?>
<script>
$('document').ready(function() {
    var tables = $('table');

    tables.map(function(idx, table) {
        var rows = $(table).find('tbody > tr');
        rows.map(function(idx, row){
            if(idx%2 == 0) {
                $(row).css('background-color', '#e6e6e6');
            }
        })
    })
})
</script>