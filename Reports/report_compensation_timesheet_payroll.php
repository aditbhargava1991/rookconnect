<?php // Statutory Holiday Pay Breakdown
include ('../include.php');
include ('../Timesheet/config.php');

checkAuthorised('report');
include ('../tcpdf/tcpdf.php');
error_reporting(0);

if(isset($_POST['printpdf'])) {

    $starttimepdf = $_POST['startpdf'];
    $endtimepdf = $_POST['endpdf'];
    $search_staffpdf = $_POST['search_staffpdf'];

    DEFINE('START_DATE', $starttimepdf);
    DEFINE('END_DATE', $endtimepdf);
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
            //$this->setCellHeightRatio(0.7);
            //$this->SetFont('helvetica', '', 9);
            //$footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            //$this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 10, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

	$html = '<table cellspacing="10">
				<tr>
                    <td><h1>Payroll Summary - Daily Hours</h1></td>
                    <td align="right"><h2>'.get_config($dbc, 'company_name').'</h2></td>
                </tr>
                <tr>
                    <td>Date Range:    '.$starttimepdf.' to '.$endtimepdf.'</td>
                    <td></td>
                </tr>
                <tr>
                    <td>Approved for Payroll: Show All</td>
                    <td align="right"><i>Note: hours have lunch deductions pre-applied where applicable</i></td>
                </tr>
                ';

    $html .= report_statutory_breakdown($dbc, $search_staffpdf, 'padding:3px; border:1px solid black;', '', '', $starttimepdf, $endtimepdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/timesheet_payroll_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_compensation_timesheet_payroll', 0, WEBSITE_URL.'/Reports/Download/timesheet_payroll_'.$today_date.'.pdf', 'Timesheet Payroll Report');

    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/timesheet_payroll_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $search_staff = $search_staffpdf;
} ?>
        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php

            if($_GET['search_staff'] != '') {
                $search_staff = implode(',',$_GET['search_staff']);
            }
			if($_GET['start'] != '') {
				$starttime = $_GET['start'];
			}
			if($_GET['end'] != '') {
				$endtime = $_GET['end'];
			}

            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $search_staff = implode(',',$_POST['search_staff']);
            }

            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            } ?>
            <center><div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Staff:</label>
					<div class="col-sm-8">
                      <select multiple data-placeholder="Select Staff Members" name="search_staff[]" class="chosen-select-deselect form-control">
                                  <!-- <option <?= 'ALL' == $search_staff ? 'selected' : '' ?> value="ALL">All Staff</option> -->
                        <?php
                          $query = mysqli_query($dbc,"SELECT distinct(staff) FROM time_cards where staff > 0 order by staff");
                          while($row1 = mysqli_fetch_array($query)) {
                            $security_level = get_contact($dbc, $row1['staff'], 'role');
                          ?><option data-security-level='<?= $security_level ?>' <?php if (strpos(','.$search_staff.',', ','.$row1['staff'].',') !== FALSE) { echo " selected"; } ?> value='<?php echo  $row1['staff']; ?>' ><?php echo get_staff($dbc, $row1['staff']); ?></option>
                        <?php } ?>
                    </select>
					</div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
                </div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="startpdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endpdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="search_staffpdf" value="<?php echo $search_staff; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php echo report_statutory_breakdown($dbc, $search_staff, '', '', '', $starttime, $endtime); ?>

        </form>

<?php function report_statutory_breakdown($dbc, $staff, $table_style, $table_row_style, $grand_total_style, $search_start_date, $search_end_date) {

    if($staff == '') {
  		return '<h4>Please select staff.</h4>';
    } else {
        $staff_list = [];
        foreach (explode(',',$staff) as $search_staff) {
            if($search_staff > 0) {
                $staff_list[] = ['contactid'=>$search_staff,'first_name'=>'','last_name'=>get_contact($dbc, $search_staff)];
            }
        }
    }

    $report = '';
	if($report_format == 'to_array') {
		$report_output = [];
	}

	foreach($staff_list as $staff) {
        $search_staff = $staff['contactid'];

		$report .= '<h3>'.$staff['first_name'].' '.$staff['last_name'].'</h3>';

		$start_of_year = date('Y-01-01', strtotime($search_start_date));
        $total_colspan = 2;
        $report .= '<table cellpadding="3" class="table table-bordered" style="text-align:left; border:1px solid black;">
                <tr class="hidden-xs hidden-sm">
                    <th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;"><div>Date</div></th>
                    <th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;"><div>Reg. Time</div></th>
                    <th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;"><div>Over Time</div></th>
                    <th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;"><div>Double Time</div></th>
                    <th style="border-right: 1px solid grey; text-align:center; width:20%;font-weight:bold;"><div>Total</div></th>
                </tr>';


                $total = 0;
                $limits = "AND `staff`='$search_staff' AND `approv`='N'";
                if($search_site > 0) {
                    $limits .= " AND `business` LIKE '%$search_site%'";
                }

                $result = get_time_sheet($search_start_date, $search_end_date, $limits, ', `staff`, `date`, `time_cards_id`');
                $date = $search_start_date;
                $i = 0;

                while(strtotime($date) <= strtotime($search_end_date)) {
                    if($result[$i]['date'] == $date) {
                        $row = $result[$i++];
                        $total += $row['hours'];
                    } else {
                        $row = '';
                    }

                    if($row['hours'] > 0) {
                        $report .= '<tr>
                            <td  style=" border-top:1px solid grey;" data-title="Date">'.$date.'</td>
                            ';

                        $report .= (in_array('total_tracked_hrs',$value_config) ? '<td style=" border-top:1px solid grey;" data-title="Time Tracked">'.$row['timer'].'</td>' : '').'
                            <td style=" border-top:1px solid grey;" data-title="Hours">'.(empty($row['hours']) ? '' : time_decimal2time($row['hours'])).' h</td>
                            <td style=" border-top:1px solid grey;" data-title="Comments"><span></span></td>
                            <td style=" border-top:1px solid grey;" data-title="Comments"><span></span></td>
                            <td style=" border-top:1px solid grey;" data-title="Comments">'.(empty($row['hours']) ? '' : time_decimal2time($row['hours'])).' h</td>
                        </tr>';
                    }
                    if($date != $row['date']) {
                        $date = date("Y-m-d", strtotime("+1 day", strtotime($date)));
                    }

                }
                $report .= '<tr>
                    <td style=" border-top:1px solid grey;font-weight:bold;" data-title="">Totals</td>
                    <td style=" border-top:1px solid grey;font-weight:bold;" data-title="Total Hours">'.time_decimal2time($total).' h</td>
                    <td style=" border-top:1px solid grey;font-weight:bold;">0:00 h</td>
                    <td style=" border-top:1px solid grey;font-weight:bold;">0:00 h</td>
                    <td style=" border-top:1px solid grey;font-weight:bold;" data-title="Total Hours">'.time_decimal2time($total).' h</td>
                </tr>
            </table>';

        $tb_field = $value['config_field'];

		if($report_format == 'to_array') {
			$report_output[] = $report;
			$report = '';
		}
	}
	if($report_format == 'to_array') {
		return $report_output;
	}
	return $report;
} ?>