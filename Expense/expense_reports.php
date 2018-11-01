<?php if($_GET['output'] == 'pdf') {
	include_once('../tcpdf/tcpdf.php');
	$get_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT `pdf_logo`, `pdf_header` FROM `field_config_expense`"));
	$head_logo = get_config($dbc, 'expense_logo');
	$pdf_header = get_config($dbc, 'expense_header');
	$pdf_footer = get_config($dbc, 'expense_footer');
	$staff_name = get_contact($dbc, $staff);
	$display_month = date('F Y', strtotime($search_month));
	DEFINE('HEADER_LOGO', $head_logo);
	DEFINE('HEADER_TEXT', html_entity_decode($pdf_header));
	DEFINE('FOOTER_TEXT', $pdf_footer == '' ? "<em>Expense Report".($_GET['min_date'] != '' ? ' From '.$_GET['min_date'] : '').($_GET['max_date'] != '' ? ' To '.$_GET['max_date'] : '')."</em>" : html_entity_decode($pdf_footer));

	class MYPDF extends TCPDF {

		//Page header
		public function Header() {
			if($front_client_info != '') {
				if ($this->PageNo() > 1) {
					if(HEADER_LOGO != '') {
						$image_file = 'download/'.HEADER_LOGO;
						$this->Image($image_file, 10, 5, 25, '', '', '', 'T', false, 300, 'L', false, false, 0, false, false, false);
					}

					if(HEADER_TEXT != '') {
						$this->setCellHeightRatio(0.7);
						$this->SetFont('helvetica', '', 10);
						$header_text = '<p style="text-align:right;">'.HEADER_TEXT.'</p>';
						$this->writeHTMLCell(0, 0, 0 , 5, $header_text, 0, 0, false, (HEADER_LOGO == '' ? 'C' : 'R'), true);
					}
				}
			} else {
				if(HEADER_LOGO != '') {
					$image_file = 'download/'.HEADER_LOGO;
					$this->Image($image_file, 10, 5, 25, '', '', '', 'T', false, 300, 'L', false, false, 0, false, false, false);
				}

				if(HEADER_TEXT != '') {
					$this->setCellHeightRatio(0.7);
					$this->SetFont('helvetica', '', 10);
					$header_text = '<p style="text-align:right;">'.HEADER_TEXT.'</p>';
					$this->writeHTMLCell(0, 0, 0 , 5, $header_text, 0, 0, false, (HEADER_LOGO == '' ? 'C' : 'R'), true);
				}
			}
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica', 'I', 8);
			$footer_text = '<p style="text-align:right;">'.$this->getAliasNumPage().'</p>';
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "R", true);

			$this->SetY(-15);
			$this->setCellHeightRatio(0.7);
			$this->SetFont('helvetica', '', 8);
			$footer_text = FOOTER_TEXT;
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "C", true);
		}
	}

	$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$margin_height = ($head_logo == '' && $pdf_header == '' ? 15 : 30);
	$pdf->SetMargins(PDF_MARGIN_LEFT, $margin_height, PDF_MARGIN_RIGHT);
	$pdf->AddPage();

	$pdf->SetFont('helvetica', '', 14);
	$pdf->Write(0, "Expense Report".($_GET['min_date'] != '' ? ' From '.$_GET['min_date'] : '').($_GET['max_date'] != '' ? ' To '.$_GET['max_date'] : ''), '', 0, 'C', true, 0, false, false, 0);
	$pdf->Ln();

	$pdf->SetFont('helvetica', '', 8);
	$pdf->setCellHeightRatio(1.75);
	$pdf->writeHTML(report_display($dbc), true, false, true, false, '');

	if (!file_exists('download/reports')) {
		mkdir('download/reports', 0777, true);
	}
	$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);
	$pdf_name = 'download/reports/expense_report_'.date('Y_m_d_h_i').'.pdf';
	$pdf->Output($pdf_name, 'F');
	echo '<script type="text/javascript"> window.location.replace("'.$pdf_name.'"); </script>';
}
$config_row = mysqli_fetch_array(mysqli_query($dbc, "SELECT expense, expense_dashboard, exchange_buffer, gst_name, gst_amt, pst_name, pst_amt, hst_name, hst_amt, expense_types, expense_rows FROM field_config_expense UNION SELECT
	'Flight,Hotel,Breakfast,Lunch,Dinner,Beverages,Transportation,Entertainment,Gas,Misc',
	'Description,Date,Receipt,Type,Day Expense,Amount,Tax,Total', '0', 'GST', '5', 'PST', '0', 'HST', '0', 'Meals,Tip', 1"));
$field_config = explode(',',$config_row['expense_dashboard']); ?>
<div style="padding: 0.5em 0;">
	<script>
	toggle_filter = function() {
		if($('.toggle-filter').text() == 'Filter Expenses') {
			$('.toggle-filter').text('Close Filter Options');
			$('.report-display').addClass('col-sm-6 col-xs-12');
			$('.filter-div').show().addClass('col-sm-6 col-xs-12');
			$('.toggle_all').hide();
		} else {
			$('.toggle-filter').text('Filter Expenses');
			$('.report-display').removeClass('col-sm-6 col-xs-12');
			$('.filter-div').hide().removeClass('col-sm-6 col-xs-12');
			$('.toggle_all').show();
		}
	}
	</script>
	<form class="horizontal" action="" method="POST">
		<div class="filter-div pull-right panel-group block-panels" id="filter_accordions" style="display:none;">
			<?php $date_start = date('Y-01-01');
			$date_end = date('Y-m-t');
			$filter_status = '';
			$filter_staff = 0;
			$filter_amt_min = 0;
			$filter_amt_max = 0;
			$filter_vendors = '';
			$filter_projects = '';
			$filter_category = '';
			$filter_receipt = '';
			$filter_warnings = '';
			if($_POST['submit'] == 'search') {
				$date_start = $_POST['start_date'];
				$date_end = $_POST['end_date'];
				$filter_status = $_POST['filter_status'];
				$filter_staff = implode(',',$_POST['filter_staff']);
				$filter_amt_min = $_POST['filter_amt_min'];
				$filter_amt_max = $_POST['filter_amt_max'];
				$filter_vendors = filter_var(implode(',',array_filter($_POST['filter_vendors'])),FILTER_SANITIZE_STRING);
				$filter_projects = filter_var(implode(',',array_filter($_POST['filter_projects'])),FILTER_SANITIZE_STRING);
				$filter_category = filter_var(implode(',',$_POST['filter_category']),FILTER_SANITIZE_STRING);
				$filter_receipt = $_POST['filter_receipt'];
				$filter_warnings = implode(',',$_POST['filter_warnings']);
			} ?>
			<div class="panel panel-name double-gap-top">
				Filter Expenses
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_dates" >
							Date<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_dates" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label class="col-sm-4 control-label">Start Date:</label>
							<div class="col-sm-8"><input type="text" name="start_date" class="form-control datepicker" value="<?= $date_start ?>"></div>
						</div><div class="clearfix"></div>
						<div class="form-group">
							<label class="col-sm-4 control-label">End Date:</label>
							<div class="col-sm-8"><input type="text" name="end_date" class="form-control datepicker" value="<?= $date_end ?>"></div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_status" >
							Status<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_status" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label for="first_name" class="col-sm-4 control-label text-right">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								Expense Status:
							</label>
							<div class="col-sm-8">
								<select data-placeholder="Select a status..." name="filter_status" class="chosen-select-deselect form-control">
									<option value=""></option>
									<option <?= $filter_status == 'Submitted' ? 'selected' : '' ?> value="Submitted">Pending</option>
									<option <?= $filter_status == 'Approved' ? 'selected' : '' ?> value="Approved">Approved</option>
									<option <?= $filter_status == 'Paid' ? 'selected' : '' ?> value="Paid">Paid</option>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_staff" >
							Staff<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_staff" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label class="col-sm-4 control-label">Staff:</label>
							<div class="col-sm-8">
								<select data-placeholder="Select a Staff..." name="filter_staff[]" multiple class="chosen-select-deselect form-control">
									<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
									foreach($query as $query_contactid) {
										echo "<option ".(in_array($query_contactid,explode(',',$filter_staff)) ? 'selected' : '')." value='".$query_contactid."'>".get_contact($dbc, $query_contactid)."</option>";
									} ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_amt" >
							Amount<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_amt" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label for="first_name" class="col-sm-4 control-label text-right">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set an amount range for the expenses. Leaving the minimum amount blank will allow you to select all expenses below a certain amount."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								Minimum Amount:
							</label>
							<div class="col-sm-8">
								<input type="number" name="filter_amt_min" class="form-control" step="0.01" min="0" value="<?= $filter_amt_min ?>" />
							</div><div class="clearfix"></div>
							<label for="first_name" class="col-sm-4 control-label text-right">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set an amount range for the expenses. Leaving either the maximum amount blank will allow you to select all above a certain amount."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								Maximum Amount:
							</label>
							<div class="col-sm-8">
								<input type="number" name="filter_amt_max" class="form-control" step="0.01" min="0" value="<?= $filter_amt_max ?>" />
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_vendor" >
							Vendor<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_vendor" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label class="col-sm-4 control-label">Vendor Name:</label>
							<div class="col-sm-8">
								<select data-placeholder="Select a Vendor..." name="filter_vendors[]" multiple class="chosen-select-deselect form-control">
									<?php $query = mysqli_query($dbc, "SELECT IFNULL(`vendor`,'') `vendor` FROM `expense` WHERE `deleted`=0 AND `vendor` != '' GROUP BY `vendor` ORDER BY `vendor`");
									while($row = mysqli_fetch_array($query)) {
										echo "<option ".(in_array($row['vendor'],explode(',',$filter_vendors)) ? 'selected' : '')." value='".$row['vendor']."'>".$row['vendor']."</option>";
									} ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_project" >
							<?= PROJECT_TILE ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_project" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label class="col-sm-4 control-label"><?= PROJECT_NOUN ?>:</label>
							<div class="col-sm-8">
								<select data-placeholder="Select <?= PROJECT_NOUN ?>..." name="filter_projects[]" multiple class="chosen-select-deselect form-control">
									<?php $query = mysqli_query($dbc, "SELECT * FROM `project` WHERE `deleted`=0 AND `projectid` IN (SELECT `projectid` FROM `expense` WHERE `deleted`=0)");
									while($row = mysqli_fetch_array($query)) {
										echo "<option ".(in_array($row['projectid'],explode(',',$filter_projects)) ? 'selected' : '')." value='".$row['projectid']."'>".get_project_label($dbc, $row)."</option>";
									} ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_category" >
							Category<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_category" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label for="first_name" class="col-sm-4 control-label text-right">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								Category:
							</label>
							<div class="col-sm-8">
								<select data-placeholder="Select a Category..." name="filter_category[]" multiple class="chosen-select-deselect form-control">
									<?php $query = mysqli_query($dbc, "SELECT DISTINCT(CONCAT(EC,': ',`category`)) cat, `category` FROM `expense_categories` ORDER BY cat");
									while($query_cat = mysqli_fetch_array($query)) {
										echo "<option ".($filter_category == $query_cat['cat'] ? 'selected' : '')." value='".$query_cat['category']."'>".$query_cat['cat']."</option>";
									} ?>
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_receipt" >
							Receipt<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_receipt" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label for="first_name" class="col-sm-4 control-label text-right">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								Receipt Attached:
							</label>
							<div class="col-sm-8">
								<label class="form-checkbox"><input type="radio" <?= $filter_receipt != 'yes' && $filter_receipt != 'no' ? 'checked' : '' ?> name="filter_receipt" value="any"> N/A</label>
								<label class="form-checkbox"><input type="radio" <?= $filter_receipt == 'yes' ? 'checked' : '' ?> name="filter_receipt" value="yes"> Yes</label>
								<label class="form-checkbox"><input type="radio" <?= $filter_receipt == 'no' ? 'checked' : '' ?> name="filter_receipt" value="no"> No</label>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#filter_accordions" href="#collapse_warnings" >
							Warnings<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_warnings" class="panel-collapse collapse">
					<div class="panel-body">
						<div class="form-group">
							<label for="first_name" class="col-sm-4 control-label text-right">
								<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
								Warnings / Errors:
							</label>
							<div class="col-sm-8">
								<select data-placeholder="Select Warnings..." name="filter_warnings[]" multiple class="chosen-select-deselect form-control">
								</select>
							</div>
						</div>
					</div>
				</div>
			</div>
			<div class="form-group gap-top">
				<button class="btn brand-btn pull-right gap-left" name="submit" value="search">Search</button>
				<a href="" class="btn brand-btn pull-right">Display All Current</a>
			</div>
		</div>
	</form>
	<div class="report-display">
		<?= report_display($dbc) ?>
	</div>
</div>
<?php function report_display($dbc) {
	$html = '';
	$config_row = mysqli_fetch_array(mysqli_query($dbc, "SELECT expense, expense_dashboard, exchange_buffer, gst_name, gst_amt, pst_name, pst_amt, hst_name, hst_amt, expense_types, expense_rows FROM field_config_expense UNION SELECT
		'Flight,Hotel,Breakfast,Lunch,Dinner,Beverages,Transportation,Entertainment,Gas,Misc',
		'Description,Date,Receipt,Type,Day Expense,Amount,Tax,Total', '0', 'GST', '5', 'PST', '0', 'HST', '0', 'Meals,Tip', 1"));
	$field_config = explode(',',$config_row['expense_dashboard']);
	$date_start = date('Y-m-01');
	$date_end = date('Y-m-t');
	$filter_status = '';
	$filter_staff = 0;
	$filter_amt_min = 0;
	$filter_amt_max = 0;
	$filter_vendors = '';
	$filter_projects = '';
	$filter_category = '';
	$filter_receipt = '';
	$filter_warnings = '';
	if($_POST['submit'] == 'search') {
		$date_start = $_POST['start_date'];
		$date_end = $_POST['end_date'];
		$filter_status = $_POST['filter_status'];
		$filter_staff = implode(',',$_POST['filter_staff']);
		$filter_amt_min = $_POST['filter_amt_min'];
		$filter_amt_max = $_POST['filter_amt_max'];
		$filter_vendors = filter_var(implode(',',array_filter($_POST['filter_vendors'])),FILTER_SANITIZE_STRING);
		$filter_projects = filter_var(implode(',',array_filter($_POST['filter_projects'])),FILTER_SANITIZE_STRING);
		$filter_category = filter_var(implode(',',$_POST['filter_category']),FILTER_SANITIZE_STRING);
		$filter_receipt = $_POST['filter_receipt'];
		$filter_warnings = implode(',',$_POST['filter_warnings']);
	}
	$filter_query = '';
	if($_POST['filter_report_submit']) {
		$date_start = $_POST['report_min_date'];
		$date_end = $_POST['report_max_date'];
		$search_ByProject = $_POST['search_ByProject'];
		if($search_ByProject!=''){
			$filter_query .= (!empty($filter_query) ? 'AND' : '')." `projectid` = '$search_ByProject'";
		}
		$search_ByTicket = $_POST['search_ByTicket'];
		if($search_ByTicket!=''){
			$filter_query .= (!empty($filter_query) ? 'AND' : '')." `ticketid` = '$search_ByTicket'";
		}
		$search_ByStaff = $_POST['search_ByStaff'];
		if($search_ByStaff!=''){
			$filter_query .= (!empty($filter_query) ? 'AND' : '')." `staff` = '$search_ByStaff'";
		}
		$search_ByCategory = $_POST['search_ByCategory'];
		if($search_ByCategory!=''){
			$filter_query .= (!empty($filter_query) ? 'AND' : '')." `category` = '$search_ByCategory'";
		}
	}

	if($date_start == '' || $date_start == '0000-00-00') {
		$date_start = '0000-00-00';
	} else {
		//$html .= "<span class='block-label'>Filter: Expense Start Date: $date_start</span>";
	}
	if($date_end == '' || $date_end == '0000-00-00') {
		$date_end = '9999-99-99';
	} else {
		//$html .= "<span class='block-label'>Filter: Expense End Date: $date_end</span>";
	}
	$filter_query .= (!empty($filter_query) ? 'AND' : '')."`ex_date` BETWEEN '$date_start' AND '$date_end' AND `deleted`=0";
	if($filter_status != '') {
		$html .= "<span class='block-label'>Filter: Status: $filter_status</span>";
		$filter_query .= " AND `status`='$filter_status'";
	}
	if($filter_staff != '') {
		$html .= "<span class='block-label'>Filter: Staff: ";
		$staff_list = [];
		foreach(explode(',',$filter_staff) as $staff) {
			if($staff > 0) {
				$staff_list[] = get_contact($dbc, $staff);
			} else {
				$staff_list[] = $staff;
			}
		}
		$html .= implode(', ', $staff_list)."</span>";
		$filter_query .= " AND `staff` IN (".$filter_staff.")";
	}
	if($filter_amt_min > 0) {
		$html .= "<span class='block-label'>Filter: Minimum Amount: $".number_format($filter_amt_min,2)."</span>";
		$filter_query .= " AND `amount` >= $filter_amt_min";
	}
	if($filter_amt_max > 0) {
		$html .= "<span class='block-label'>Filter: Maximum Amount: $".number_format($filter_amt_max,2)."</span>";
		$filter_query .= " AND `amount` <= '$filter_amt_max'";
	}
	if($filter_vendors != '') {
		$html .= "<span class='block-label'>Filter: Vendors: $filter_vendors</span>";
		$filter_query .= " AND `vendor` IN ('".implode("','",explode(',',$filter_vendors))."')";
	}
	if($filter_projects != '') {
		$html .= "<span class='block-label'>Filter: ".PROJECT_TILE.": ";
		foreach(explode(',',$filter_projects) as $projectid) {
			$html .= get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'")));
		}
		$html .= "</span>";
		$filter_query .= " AND `projectid` IN (".$filter_projects.")";
	}
	if($filter_category != '') {
		$html .= "<span class='block-label'>Filter: Categories: $filter_category</span>";
		$filter_query .= " AND `category` IN ('".implode("','",explode(',',$filter_category))."')";
	}
	if($filter_receipt == 'yes') {
		$html .= "<span class='block-label'>Filter: Receipt Attached</span>";
		$filter_query .= " AND IFNULL(`ex_file`,'')!=''";
	} else if($filter_receipt == 'no') {
		$html .= "<span class='block-label'>Filter: No Receipt Attached</span>";
		$filter_query .= " AND IFNULL(`ex_file`,'')=''";
	}
	$html .= '<form name="filter_report_form" action="" method="POST">
	<div class="col-sm-6">
		<label for="first_name" class="col-sm-4 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set a date range for the expenses. Leaving the minimum date blank will allow you to select all expenses before a date."><img src="'.WEBSITE_URL.'/img/info.png" width="20"></a></span>
			From:
		</label>
		<div class="col-sm-8">
			<input type="text" name="report_min_date" class="datepicker form-control" value="'.$date_start.'" />
		</div>
	</div>
	<div class="col-sm-6">
		<label for="first_name" class="col-sm-4 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to set a date range for the expenses. Leaving either the maximum date blank will allow you to select all expenses after a date."><img src="'.WEBSITE_URL.'/img/info.png" width="20"></a></span>
			Until:
		</label>
		<div class="col-sm-8">
			<input type="text" name="report_max_date" class="datepicker form-control" value="'.$date_end.'" />
		</div>
	</div>
        <div class="clearfix"></div>';

        $html .= '<div class="col-sm-6">
			<label for="search_ticket" class="col-sm-4 control-label"><span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select Ticket"><img src="'.WEBSITE_URL.'/img/info.png" width="20"></a></span> Search By '. TICKET_NOUN .':</label>
			<div class="col-sm-8">
				<select data-placeholder="Select a '. TICKET_NOUN .' #" name="search_ByTicket" class="chosen-select-deselect form-control">
					<option value="">Select Ticket</option>';?>
					<?php
					$query = mysqli_query($dbc,"SELECT `ticketid`, `heading` FROM `tickets` WHERE `deleted`=0 ORDER BY `ticketid`");
					while($row = mysqli_fetch_array($query)) {
						$html .= '<option '.(($row['ticketid'] == $_POST['search_ByTicket']) ? "selected" : "").' value="'.$row['ticketid'].'">'.TICKET_NOUN.' #'.$row['ticketid'].' '.$row['heading'].'</option>';
					 }
				$html .= '</select>
			</div>
		</div>';

		$html .= '<div class="col-sm-6">
			<label for="search_ticket" class="col-sm-4 control-label"><span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select Category"><img src="'.WEBSITE_URL.'/img/info.png" width="20"></a></span> Search By Category:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select a Category#" name="search_ByCategory" class="chosen-select-deselect form-control">
					<option value="">Select Category</option>';?>
					<?php 
					$query = mysqli_query($dbc, "SELECT DISTINCT(CONCAT(EC,': ',`category`)) cat, `category` FROM `expense_categories` ORDER BY cat");
					while($query_cat = mysqli_fetch_array($query)) {
						$html .= "<option ".($query_cat['category'] == $_POST['search_ByCategory'] ? 'selected' : '')." value='".$query_cat['category']."'>".$query_cat['cat']."</option>";
					}
				$html .= '</select>
			</div>
		</div><div class="clearfix"></div>';

		$html .= '<div class="col-sm-6">
			<label for="search_ticket" class="col-sm-4 control-label"><span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select Project"><img src="'.WEBSITE_URL.'/img/info.png" width="20"></a></span> Search By '. PROJECT_NOUN .':</label>
			<div class="col-sm-8">
				<select data-placeholder="Select a '. PROJECT_NOUN .' #" name="search_ByProject" class="chosen-select-deselect form-control">
					<option value="">Select Project</option>';?>
					<?php 
					$query = mysqli_query($dbc, "SELECT * FROM `project` WHERE `deleted`=0 AND `projectid` IN (SELECT `projectid` FROM `expense` WHERE `deleted`=0)");
					while($row = mysqli_fetch_array($query)) {
						$html .= "<option ".(($row['projectid'] == $_POST['search_ByProject']) ? 'selected' : '')." value='".$row['projectid']."'>".get_project_label($dbc, $row)."</option>";
					}
				$html .= '</select>
			</div>
		</div>';

		$html .= '<div class="col-sm-6">
			<label for="search_ticket" class="col-sm-4 control-label"><span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Select Staff"><img src="'.WEBSITE_URL.'/img/info.png" width="20"></a></span> Search By Staff:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select a Staff#" name="search_ByStaff" class="chosen-select-deselect form-control">
					<option value="">Select Staff</option>';?>
					<?php 
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"),MYSQLI_ASSOC));
						foreach($query as $query_contactid) {
							$html .= "<option ".(($query_contactid == $_POST['search_ByStaff']) ? 'selected' : '')." value='".$query_contactid."'>".get_contact($dbc, $query_contactid)."</option>";
						}
				$html .= '</select>
			</div>
		</div><div class="clearfix"></div>';

		$html .= '<div class="col-sm-12" style="padding: 5px 0 5px 0;">
			<input type="submit" name="filter_report_submit" class="btn brand-btn mobile-block pull-right" value="Search">
		</div>
		<br>
	</form>';

	//$html .= '<div style="text-align: center;"><a href="?tab=reports&view=staff" '.(!isset($_GET['view']) || $_GET['view'] == 'staff' ? 'class="active"' : '').'>Staff</a> | <a href="?tab=reports&view=category" '.($_GET['view'] == 'category' ? 'class="active"' : '').'>Category</a>' .((in_array('Vendor',$field_config)) ? ' | <a href="?tab=reports&view=vendor"'.($_GET['view'] == 'vendor' ? 'class="active"' : '').'>Vendor</a>' : '') .((in_array('Project',$field_config)) ? ' | <a href="?tab=reports&view=project"'.($_GET['view'] == 'project' ? 'class="active"' : '').'>'.PROJECT_TILE.'</a>' : '').'</div>';
	
	$html .= '<table class="table table-bordered new-table">
			<tr class="hidden-xm hidden-xs">
				<th style="max-width: 25%; width: 15em;">Staff</th>
				<th>Expense Amount</th>
			</tr>';

			$expense_report = mysqli_query($dbc, "SELECT `staff`, SUM(`total`) expense_sum FROM `expense` WHERE $filter_query GROUP BY `staff` ORDER BY expense_sum DESC");
			$max_expenses = 0;
			while($report = mysqli_fetch_array($expense_report)) {
				if($report['expense_sum'] > $max_expenses) {
					$report_level = floor($report['expense_sum'] / 10);
					$max_expenses = ceil($report['expense_sum'] / $report_level) * $report_level;
				}
				$html .= '<tr>
					<td data-title="Staff" style="max-width: 25%; width: 200px;">'.($report['staff'] > 0 ? '<a href="?filter_id=all&staff_id='.$report['staff'].'">'.get_contact($dbc, $report['staff']).'</a>' : ($report['staff'] != '' ? $report['staff'] : 'Unknown')).'</td>
					<td data-title="Expense Amount" style="background-color: #AAA; padding: 0 0 0 0;">
						<div style="background-color: #6DCFF6; line-height: 2.5em; width:'.($report['expense_sum'] / $max_expenses * 100).'%;">&nbsp;</div>
						<div style="margin: -1.75em 1em 0;"><b>$'.number_format($report['expense_sum'],2).'</b></div>
					</td>
				</tr>';
			}
			
			$expense_report = mysqli_query($dbc, "SELECT CONCAT(IFNULL(CONCAT(`categories`.`EC`,': '),''),IFNULL(`expense`.`category`,'')) `category`, SUM(`total`) expense_sum, CONCAT(LPAD(0,100,IFNULL(`categories`.`EC`,'')),IFNULL(`expense`.`category`,'')) expense_sort FROM `expense` LEFT JOIN (SELECT `EC`, `category` FROM `expense_categories` GROUP BY `category`) `categories` ON `expense`.`category`=`categories`.`category` WHERE $filter_query GROUP BY IFNULL(`expense`.`category`,'') ORDER BY expense_sum DESC");
			$max_expenses = 0;
			$lines = [];
			while($report = mysqli_fetch_array($expense_report)) {
				if($report['expense_sum'] > $max_expenses) {
					$report_level = floor($report['expense_sum'] / 10);
					$max_expenses = ceil($report['expense_sum'] / $report_level) * $report_level;
				}
				$lines[$report['expense_sort']] = '<tr>
					<td data-title="Category" style="max-width: 25%; width: 15em;">'.($report['category'] != '' ? $report['category'] : 'Uncategorized').'</td>
					<td data-title="Expense Amount" style="background-color: #AAA; padding: 0 0 0 0;">
						<div style="background-color: #6DCFF6; line-height: 2.5em; width:'.($report['expense_sum'] / $max_expenses * 100).'%;">&nbsp;</div>
						<div style="margin: -1.75em 1em 0;"><b>$'.number_format($report['expense_sum'],2).'</b></div>
					</td>
				</tr>';
			}
			ksort($lines);
			foreach($lines as $line) {
				$html .= $line;
			}
			
			$expense_report = mysqli_query($dbc, "SELECT `projectid`, SUM(`total`) `expense_sum` FROM `expense` WHERE $filter_query GROUP BY `projectid` ORDER BY expense_sum DESC");
			$max_expenses = 0;
			while($report = mysqli_fetch_array($expense_report)) {
				if($report['expense_sum'] > $max_expenses) {
					$report_level = floor($report['expense_sum'] / 10);
					$max_expenses = ceil($report['expense_sum'] / $report_level) * $report_level;
				}
				$html .= '<tr>
					<td data-title="'.PROJECT_NOUN.'" style="max-width: 25%; width: 20em;">'.($report['projectid'] > 0 ? get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='".$report['projectid']."'"))) : 'N/A').'</td>
					<td data-title="Expense Amount" style="background-color: #AAA; padding: 0 0 0 0;">
						<div style="background-color: #6DCFF6; line-height: 2.5em; width:'.($report['expense_sum'] / $max_expenses * 100).'%;">&nbsp;</div>
						<div style="margin: -1.75em 1em 0;"><b>$'.number_format($report['expense_sum'],2).'</b></div>
					</td>
				</tr>';
			}

			$html .= '</table>';

	if($_GET['view'] == 'vendor') {
		$html .= '<table class="table table-bordered new-table">
			<tr class="hidden-xm hidden-xs">
				<th style="max-width: 25%; width: 15em;">Vendor</th>
				<th>Expense Amount</th>
			</tr>';
			$expense_report = mysqli_query($dbc, "SELECT IFNULL(`vendor`,'') `vendor`, SUM(`total`) `expense_sum` FROM `expense` WHERE $filter_query GROUP BY IFNULL(`vendor`,'') ORDER BY expense_sum DESC");
			$max_expenses = 0;
			while($report = mysqli_fetch_array($expense_report)) {
				if($report['expense_sum'] > $max_expenses) {
					$report_level = floor($report['expense_sum'] / 10);
					$max_expenses = ceil($report['expense_sum'] / $report_level) * $report_level;
				}
				$html .= '<tr>
					<td data-title="Vendor" style="max-width: 25%; width: 15em;">'.($report['vendor'] != '' ? $report['vendor'] : 'Unspecified').'</td>
					<td data-title="Expense Amount" style="background-color: #AAA; padding: 0 0 0 0;">
						<div style="background-color: #6DCFF6; line-height: 2.5em; width:'.($report['expense_sum'] / $max_expenses * 100).'%;">&nbsp;</div>
						<div style="margin: -1.75em 1em 0;"><b>$'.number_format($report['expense_sum'],2).'</b></div>
					</td>
				</tr>';
			}
		$html .= '</table>';
	}
	return $html;
} ?>