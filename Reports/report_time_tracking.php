<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {
    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];
    $jobpdf =$_POST['jobpdf'];
    $afe_numberpdf =$_POST['afe_numberpdf'];
    $locationpdf =$_POST['locationpdf'];
    $staffidpdf =$_POST['staffidpdf'];

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
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'Time Tracking';
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "C", true);
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

    $html .= report_receivables($dbc, $starttimepdf, $endtimepdf, $jobpdf , 'padding:3px; border:1px solid black;', 'background-color:grey; color:black;', 'background-color:lightgrey; color:black;', $afe_numberpdf, $locationpdf, $staffidpdf);

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/timetracking_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_time_tracking', 0, WEBSITE_URL.'/Reports/Download/timetracking_'.$today_date.'.pdf', 'Time Tracking Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/timetracking_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
    $job_number = $jobpdf;
    $afe_number = $afe_numberpdf;
    $location = $locationpdf;
    $staffid = $staffidpdf;
    } ?>

<script type="text/javascript">

</script>
</head>
<body>
<?php include_once ('../navigation.php');
?>

<div class="container triple-pad-bottom">
    <div class="row">
        <div class="col-md-12">

        <?php echo reports_tiles($dbc);  ?>

        <br><br>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-inline" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

            <?php

            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
                $job_number = $_POST['job_number'];
                $afe_number = $_POST['afe_number'];
                $location = $_POST['location'];
                $staffid = $_POST['staffid'];
            }
            if (isset($_POST['display_all_inventory'])) {
                $job_number = '';
                $afe_number = '';
                $location = '';
                $staffid = '';
            }
            if($starttime == 0000-00-00) {
                $starttime = date('Y-m-d');
            }

            if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }
            ?>
            <!--
            <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">From:</label>
                <div class="col-sm-8">
                    <input name="starttime" type="text" class="datepicker" value="<?php echo $starttime; ?>">
                </div>
            </div>

            <div class="form-group until">
                <label for="site_name" class="col-sm-4 control-label">Until:</label>
                <div class="col-sm-8">
                    <input name="endtime" type="text" class="datepicker" value="<?php echo $endtime; ?>">
                </div>
            </div>
            -->

            <div class="form-group">
                <div class="col-sm-8">
                    <select name="job_number" data-placeholder="Choose Job..." class="chosen-select-deselect form-control" width="380">
                        <option value=''>Choose Job</option>
                        <?php
                        $result = mysqli_query($dbc, "SELECT distinct(job_number) FROM time_tracking WHERE deleted=0 AND job_number != ''");
                        while($row = mysqli_fetch_assoc($result)) {
                            if ($job_number == $row['job_number']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value = '".$row['job_number']."'>".$row['job_number']."</option>";
                        }
                       ?>
                    </select>
                    <select name="afe_number" data-placeholder="Choose Job..." class="chosen-select-deselect form-control" width="380">
                        <option value=''>Choose AFE</option>
                        <?php
                        $result = mysqli_query($dbc, "SELECT distinct(afe_number) FROM time_tracking WHERE deleted=0 AND afe_number != ''");
                        while($row = mysqli_fetch_assoc($result)) {
                            if ($afe_number == $row['afe_number']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value = '".$row['afe_number']."'>".$row['afe_number']."</option>";
                        }
                       ?>
                    </select>
                    <select name="location" data-placeholder="Choose Location..." class="chosen-select-deselect form-control" width="380">
                        <option value=''>Choose Location</option>
                        <?php
                        $result = mysqli_query($dbc, "SELECT distinct(location) FROM time_tracking WHERE location != ''");
                        while($row = mysqli_fetch_assoc($result)) {
                            if ($location == $row['location']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value = '".$row['location']."'>".$row['location']."</option>";
                        }
                       ?>
                    </select>
                    <select name="staffid" data-placeholder="Choose Person..." class="chosen-select-deselect form-control" width="380">
                        <option value=''>Choose Person</option>
                        <?php
                        $result = mysqli_query($dbc, "SELECT distinct(staffid) FROM time_tracking_labour WHERE staffid != ''");
                        while($row = mysqli_fetch_assoc($result)) {
                            if ($staffid == $row['staffid']) {
                                $selected = 'selected="selected"';
                            } else {
                                $selected = '';
                            }
                            echo "<option ".$selected." value = '".$row['staffid']."'>".get_staff($dbc, $row['staffid'])."</option>";
                        }
                       ?>
                    </select>
                </div>
            </div>

            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
            <button type="submit" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>

            <br>
            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="jobpdf" value="<?php echo $job_number; ?>">
            <input type="hidden" name="afe_numberpdf" value="<?php echo $afe_number; ?>">
            <input type="hidden" name="locationpdf" value="<?php echo $location; ?>">
            <input type="hidden" name="staffidpdf" value="<?php echo $staffid; ?>">

            <button type="submit" name="printpdf" value="Print Report" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                echo report_receivables($dbc, $starttime, $endtime, $job_number, '', '', '', $afe_number, $location, $staffid);
            ?>

        </form>

        </div>
    </div>
</div>
<?php include ('../footer.php'); ?>

<?php
function report_receivables($dbc, $starttime, $endtime, $job_number, $table_style, $table_row_style, $grand_total_style, $afe_number, $location, $staffid) {
    $report_data = '';

    if($job_number != '') {
        $result = mysqli_query($dbc,"SELECT * FROM time_tracking WHERE deleted = 0 AND job_number='$job_number'");
    } else if($afe_number != '') {
        $result = mysqli_query($dbc,"SELECT * FROM time_tracking WHERE deleted = 0 AND afe_number='$afe_number'");
    } else if($location != '') {
        $result = mysqli_query($dbc,"SELECT * FROM time_tracking WHERE deleted = 0 AND location='$location'");
    } else if($staffid != '') {
        $result = mysqli_query($dbc,"SELECT DISTINCT(tt.timetrackingid), tt.* FROM time_tracking tt, time_tracking_labour ttl WHERE tt.deleted = 0 AND tt.timetrackingid = ttl.timetrackingid AND ttl.staffid='$staffid'");
    } else {
        $result = mysqli_query($dbc,"SELECT * FROM time_tracking WHERE deleted = 0 AND DATE(work_preformed) = DATE(NOW())");
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT time_tracking_dashboard FROM field_config"));
    $value_config = ','.$get_field_config['time_tracking_dashboard'].',';

    $report_data .= '<table border="1px" class="table table-bordered" width="100%" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">';
    if (strpos($value_config, ','."Business".',') !== FALSE) {
        $report_data .=  '<th width="15%">Business</th>';
    }
    if (strpos($value_config, ','."Contact".',') !== FALSE) {
        $report_data .=  '<th width="15%">Contact</th>';
    }
    if (strpos($value_config, ','."Location".',') !== FALSE) {
        $report_data .=  '<th width="10%">Location</th>';
    }
    if (strpos($value_config, ','."Job number".',') !== FALSE) {
        $report_data .=  '<th width="10%">Job number</th>';
    }
    if (strpos($value_config, ','."AFE number".',') !== FALSE) {
        $report_data .=  '<th width="10%">AFE number</th>';
    }
    if (strpos($value_config, ','."Work performed".',') !== FALSE) {
        $report_data .=  '<th width="10%">Work performed</th>';
    }
    if (strpos($value_config, ','."Short description".',') !== FALSE) {
        $report_data .=  '<th width="10%">Short description</th>';
    }
    if (strpos($value_config, ','."Job description".',') !== FALSE) {
        $report_data .=  '<th width="10%">Job description</th>';
    }
    if (strpos($value_config, ','."Labour".',') !== FALSE) {
        $report_data .=  '<th width="30%">Labour - Position - Reg Hours - Reg Rate - OT Hours - OT Rate</th>';
    }
    $report_data .=  "</tr>";

    while($row = mysqli_fetch_array( $result ))
    {
        $report_data .= '<tr nobr="true">';
        $timetrackingid = $row['timetrackingid'];
        if (strpos($value_config, ','."Business".',') !== FALSE) {
            $report_data .=  '<td data-title="Code">' . get_client($dbc, $row['businessid']) . '</td>';
        }
        if (strpos($value_config, ','."Contact".',') !== FALSE) {
            $report_data .=  '<td data-title="Code">' . get_staff($dbc, $row['contactid']) . '</td>';
        }
        if (strpos($value_config, ','."Location".',') !== FALSE) {
            $report_data .=  '<td data-title="Code">' . $row['location'] . '</td>';
        }
        if (strpos($value_config, ','."Job number".',') !== FALSE) {
            $report_data .=  '<td data-title="Code">' . $row['job_number'] . '</td>';
        }
        if (strpos($value_config, ','."AFE number".',') !== FALSE) {
            $report_data .=  '<td data-title="Code">' . $row['afe_number'] . '</td>';
        }
        if (strpos($value_config, ','."Work performed".',') !== FALSE) {
            $report_data .=  '<td data-title="Code">' . $row['work_preformed'] . '</td>';
        }
        if (strpos($value_config, ','."Short description".',') !== FALSE) {
            $report_data .=  '<td data-title="Code">' . $row['short_desc'] . '</td>';
        }
        if (strpos($value_config, ','."Job description".',') !== FALSE) {
            $report_data .=  '<td data-title="Code">' . $row['job_desc'] . '</td>';
        }

        if (strpos($value_config, ','."Labour".',') !== FALSE) {
            $emp = '';
            $time_tracking_labour = mysqli_query($dbc, "SELECT * FROM time_tracking_labour WHERE timetrackingid='$timetrackingid'");

            while($row_labour = mysqli_fetch_array( $time_tracking_labour )) {
                $emp .= get_staff($dbc, $row_labour['staffid']);
                $emp .= ' - '.$row_labour['position'];
                $emp .= ' - ' . $row_labour['reg_hours'];
                $emp .= ' - ' . $row_labour['reg_rate'];
                $emp .= ' - ' . $row_labour['ot_hours'];
                $emp .= ' - ' . $row_labour['ot_rate'];
                $emp .= '<br>';
            }
            $report_data .=  '<td data-title="Labour">' . $emp . '</td>';
        }

        $report_data .=  "</tr>";
    }

    $report_data .=  '</table>';

    return $report_data;
}

?>