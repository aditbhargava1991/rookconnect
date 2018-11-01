<?php
/*
Add	Job
*/
include ('../include.php');
checkAuthorised('field_job');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
$edit_result = mysqli_fetch_array(mysqli_query($dbc, "select field_list from field_config_field_jobs where tab='invoice'"));
$edit_config = json_decode($edit_result['field_list'],true);
?>

</head>

<body>
<?php include_once ('../navigation.php');

?>
<div class="container">
  <div class="row">

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<?php
		$jobid = $_GET['jobid'];

		$job_number = get_job($dbc, $jobid, 'job_number');
		$contactid = get_job($dbc, $jobid, 'contactid');
        $description= get_job($dbc, $jobid, 'description');
		$employeeid = get_job($dbc, $jobid, 'foremanid');
		$afe_number = get_job($dbc, $jobid, 'afe_number');
        $additional_info = get_job($dbc, $jobid, 'additional_info');
        $siteid = get_job($dbc, $jobid, 'siteid');
        $location = get_site($dbc, $siteid);

		$invoice_date = date('Y-m-d');

		$in = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT invoiceid FROM field_invoice ORDER BY invoiceid DESC"));
		$in_number = $in['invoiceid']+1;

		$query_insert_po = "INSERT INTO `field_invoice` (`jobid`, `invoice_date`) VALUES ('$jobid', '$invoice_date')";
	    $result_insert_po = mysqli_query($dbc, $query_insert_po);
		$poid = mysqli_insert_id($dbc);
    $before_change = '';
    $history = "field_invoice entry has been added. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

		// PDF
        class MYPDF extends TCPDF {

		public function Header() {
			$image_file = WEBSITE_URL.'/img/fresh-focus-logo-dark.png';
			$this->SetFont('helvetica', '', 15);
			$footer_text = 'INVOICE<br>';
			$this->writeHTMLCell(0, 0, 10, 10, $footer_text, 0, 0, false, "L", true);

					   $this->Image($image_file, 0, 10, 60, '', 'PNG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
			$this->SetFont('helvetica', '', 9);
			$footer_text = '<p style="text-align:right;">Box 2052, Sundre, AB, T0M 1X0<br>Phone: 403-638-4030<br>Fax: 403-638-4001<br>Email: info@highlandprojects.com<br></p>';
			$this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-15);
			$this->SetFont('helvetica', '', 9);
			$footer_text = 'Page '.$this->getAliasNumPage().' of '.$this->getAliasNbPages();
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 50, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$pdf->SetFont('helvetica', '', 9);

	$pdf->AddPage();
	$pdf->SetFont('helvetica', '', 9);
		$html = '<table frame="box" style="border:1px solid black">
				<tr><td style="width:10%" rowspan="3"><strong>Sold To: </strong></td><td rowspan="3" style="width: 40%; border-right:1px solid black;">' . get_client($dbc, $contactid) .'<br>'.get_address($dbc, $contactid).'</td><td style="width:10%"><strong>AFE# </strong></td><td style="width:40%">'.$afe_number.'</td></tr>
				<tr><td  style="width:10%"><strong>Location:</strong></td><td style="width:40%">'.$location.'</td></tr>
				<tr><td style="width:10%"><strong>Additional Info:</strong></td><td  style="width:40%">'.$additional_info.'</td></tr>
				<tr><td style="width:10%"><strong>Contact:</strong></td><td style="border-right:1px solid black;">'. get_staff($dbc, $contactid).'</td><td style="width:10%" ><strong>Job #</strong></td><td  style="width:40%">'.$job_number.'</td></tr>
			</table>';
		$pdf->writeHTML($html, true, false, true, false, '');

		$html = '
			<table border="0" >
				<tr><td style="text-align:left; font-weight:bold;">Invoice Date: '.$invoice_date.'</td><td style="text-align: right; font-weight:bold;">Invoice #'.$in_number.'</td></tr>
			</table>
			<table border="1" cellpadding="10">

				<tr><th style="text-align:center; width:75%;"><strong>Work Ticket #</strong></th><th style="width:25%;text-align:center;"><strong>Amount</strong></th></tr>';

            $jobid = $_GET['jobid'];
			$quer_y = "SELECT * FROM field_work_ticket WHERE jobid = '$jobid' AND attach_invoice=0 AND status='Approved'";
			$result = mysqli_query($dbc, $quer_y);
			$total_wt = 0;
            $fsid = '';

			while($row = mysqli_fetch_array( $result )) {
				$html .= '<tr>
				<td style="text-align:right;" width="75%">' . $row['workticketid'] . '</td>';

				$html .= '<td style="text-align:right;" width="25%">$' . $row['total_cost'] . '</td>';
				$total_wt += $row['total_cost'];
				$html .= '</tr>';
                $fsid .= $row['fsid'].',';
			}
			$html .= '</table>';

			$gst = $total_wt * 0.05;
			$total = ($total_wt+$gst);

			$html .= '
					<br>
					<table border="0" cellpadding="10">
						<tr><td style="text-align:right;" width="75%"><strong>Sub Total</strong></td><td border="1" width="25%" style="text-align:right;">$'.$total_wt.'</td></tr>
						<tr><td style="text-align:right;" width="75%"><strong>GST</strong></td><td border="1" width="25%" style="text-align:right;">$'.round($gst).'</td></tr>
						<tr><td style="text-align:right;" width="75%"><strong>Total</strong></td><td border="1" width="25%" style="text-align:right;">$'.round($total).'</td></tr>
						<tr><td style="text-align:right;" width="75%">&nbsp;</td><td border="1" width="25%" style="text-align:right; font-weight:bold;">GST 83356 9379 RT0001</td></tr>
					</table>';

			$pdf->writeHTML($html, true, false, true, false, '');
			$pdf->Output('download/field_invoice_'.$in_number.'.pdf', 'F');
			// PDF

      $before_change = capture_before_change($dbc, 'field_work_ticket', 'attach_invoice', 'jobid', $jobid, 'status', 'Approved');

			$query_update_wt = "UPDATE `field_work_ticket` SET `attach_invoice` = '$poid' WHERE `jobid` = '$jobid' AND attach_invoice=0 AND status='Approved'";
			$result_update_wt = mysqli_query($dbc, $query_update_wt);

      $history = capture_after_change('attach_invoice', $poid);
	    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

            $f_fsid= rtrim ($fsid, ',');
            $date_of_archival = date('Y-m-d');

			$query_update_wt = "UPDATE `field_foreman_sheet` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `fsid` IN ($f_fsid)";
			$result_update_wt = mysqli_query($dbc, $query_update_wt);

      $before_change = '';
      $history = "deleted and date_of_archival entry has been updated for fsids -> $f_fsid. <br />";
      add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
            ?>

            <script type="text/javascript" language="Javascript">
            alert('Invoice Generated.');
	        window.location.replace('field_jobs.php');
	        window.open('download/field_invoice_<?php echo $in_number;?>.pdf', 'fullscreen=yes');
            </script>

            <?php
             mysqli_close($dbc); //Close the DB Connection
             ?>
		</form>
	</div>
  </div>

<?php include ('../footer.php'); ?>
