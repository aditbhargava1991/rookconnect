<?php
/*
Add	Job
*/
include ('../include.php');
checkAuthorised('field_job');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['submit'])) {
    $jobid = $_POST['jobid'];
    $pdf_sub_total = $_POST['pdf_sub_total'];
    $pdf_gst = $_POST['pdf_gst'];
    $pdf_total = $_POST['pdf_total'];
    $dis_perc = $_POST['dis_perc'];
    $dis_dollor = $_POST['dis_dollor'];
    $invoice_comments = filter_var($_POST['invoice_comments'], FILTER_SANITIZE_STRING);

    $invoice_date = date('Y-m-d');

	$invoice_id = 0;
	$attach_invoice = 0;
	if($_POST['invoiceid'] > 0) {
		$invoice_id = filter_var($_POST['invoiceid'],FILTER_SANITIZE_STRING);
		$attach_invoice = $invoice_id;
	} else {
		$query_insert_po = "INSERT INTO `field_invoice` (`jobid`, `invoice_date`, `comments`) VALUES ('$jobid', '$invoice_date', '$invoice_comments')";
		$result_insert_po = mysqli_query($dbc, $query_insert_po);
		$invoice_id = mysqli_insert_id($dbc);
    $before_change = '';
    $history = "field_invoice entry has been added. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
	}

    DEFINE('IN_DATE', $invoice_date);
    DEFINE('INVOICE_NUMBER', $invoice_id);
    DEFINE('INVOICE_LOGO', get_config($dbc, 'field_jobs_invoice_logo'));
	DEFINE('INVOICE_HEADER_TEXT', html_entity_decode(get_config($dbc, 'field_jobs_invoice_address')));

		// PDF
    class MYPDF extends TCPDF {

		public function Header() {
			$image_file = 'download/'.INVOICE_LOGO;
			$this->SetFont('helvetica', '', 15);
			$footer_text = 'INVOICE #'.INVOICE_NUMBER.'<br>';
			$this->writeHTMLCell(0, 0, 10, 10, $footer_text, 0, 0, false, "L", true);

			$this->Image($image_file, 0, 10, 60, '', 'PNG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
			$this->SetFont('helvetica', '', 9);
            $this->setCellHeightRatio(0.6);
			$footer_text = '<p style="text-align:right;">'.INVOICE_HEADER_TEXT.'<br>Invoice Date :  '.IN_DATE.'</p>';
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 39, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
	$pdf->SetFont('helvetica', '', 9);

	$pdf->AddPage();
	$pdf->SetFont('helvetica', '', 9);

	$html = display_pdf_header($dbc, $jobid);

	$pdf->writeHTML($html, true, false, true, false, '');
	if($_POST['invoice_mode'] == 'flat_rate') {
		$get_job = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_jobs` WHERE `jobid`='$jobid'"));
    $before_change = capture_before_change($dbc, 'field_jobs', 'invoice', 'jobid', $jobid);

		mysqli_query($dbc, "UPDATE `field_jobs` SET `invoice`='Flat Rate' WHERE `jobid`='$jobid'");

    $history = capture_after_change('invoice', 'Flat Rate');
  	add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

		$html = '
            <table style="border:1px solid black" cellpadding="2">
                <tr>
                    <th style="border:1px solid black; text-align:center; width:75%;"><strong>Description</strong></th>
                    <th style="border:1px solid black; text-align:center; width:25%;"><strong>Amount</strong></th>
                </tr>
                <tr>
                    <td style="text-align:right;">Job #'.$get_job['job_number'].'</td>
                    <td style="text-align:right; border-left:1px solid black;">$'.number_format((float)$_POST['job_sub_total'],2).'</td>
                </tr>
            </table>';
	} else {
		for ( $i=0; $i<count($_POST['workticketid']); $i++ ) {
            $update_comments = mysqli_query($dbc, "UPDATE `field_work_ticket` SET `comments`='{$_POST['comments'][$i]}' WHERE `workticketid`='{$_POST['workticketid'][$i]}'");
            $before_change = '';
            $history = "field_work_ticket entry has been updated for workticketid -> {$_POST['workticketid'][$i]}. <br />";
            add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
        }

        $html = '
            <table style="border:1px solid black" cellpadding="2">
                <tr>
                    <th style="border:1px solid black; text-align:center; width:75%;"><strong>Work Ticket #</strong></th>
                    <th style="border:1px solid black; text-align:center; width:25%;"><strong>Amount</strong></th>
                </tr>';

		$quer_y = "SELECT * FROM field_work_ticket WHERE jobid='$jobid' AND attach_invoice='$attach_invoice' AND status='Approved'";
		$result = mysqli_query($dbc, $quer_y);
		$fsid = '';

		while($row = mysqli_fetch_array( $result )) {
			$html .= '
                <tr style="border-right:1px solid black;">
                    <td style="text-align:right;" width="75%">' . $row['workticketid'] .($row['comments'] != '' ? ' - '.$row['comments'] : ''). '</td>
                    <td style="text-align:right; border-left:1px solid black" width="25%">$'.number_format((float)$row['sub_total'], 2, '.', '') . '</td>
                </tr>';
			$fsid .= $row['fsid'].',';
		}
		$html .= '</table>';
	}

	$discount = '';
	if($dis_perc != '') {
		$discount = $dis_perc.'%';
	}
	if($dis_dollor != '') {
		$discount = '$'.$dis_dollor;
	}
	$html .= '<table border="0" cellpadding="2">';
			if($discount != '') {
				$html .= '<tr><td style="text-align:right;" width="75%"><strong>Discount</strong></td><td border="1" width="25%" style="text-align:right;">'.$discount.'</td></tr>';
			}
				$html .= '<tr><td style="text-align:right;" width="75%"><strong>Sub Total</strong></td><td border="1" width="25%" style="text-align:right;">$'.number_format((float)$pdf_sub_total, 2, '.', '').'</td></tr>
				<tr><td style="text-align:right;" width="75%"><strong>GST</strong></td><td border="1" width="25%" style="text-align:right;">$'.number_format((float)$pdf_gst, 2, '.', '').'</td></tr>
				<tr><td style="text-align:right;" width="75%"><strong>Total</strong></td><td border="1" width="25%" style="text-align:right;">$'.number_format((float)$pdf_total, 2, '.', '').'</td></tr>
				<tr><td style="text-align:right;" width="75%">&nbsp;</td><td border="1" width="25%" style="text-align:right; font-weight:bold;">GST 83356 9379 RT0001</td></tr>
			</table>';

    if ( !empty($invoice_comments) ) {
        $html .= '<br><br><br>Comments:<br>'. $invoice_comments;
    }


		$pdf->writeHTML($html, true, false, true, false, '');
		$pdf->Output('download/field_invoice_'.$invoice_id.'.pdf', 'F');
		// PDF

    $before_change = capture_before_change($dbc, 'field_work_ticket', 'attach_invoice', 'attach_invoice', 0, 'status', 'Approved');

		$query_update_wt = "UPDATE `field_work_ticket` SET `attach_invoice`='$invoice_id' WHERE `jobid`='$jobid' AND attach_invoice=0 AND status='Approved'";
		$result_update_wt = mysqli_query($dbc, $query_update_wt);

    $history = capture_after_change('attach_invoice', $invoice_id);
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);

		$f_fsid= rtrim ($fsid, ',');
        $date_of_archival = date('Y-m-d');


		$query_update_wt = "UPDATE `field_foreman_sheet` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `fsid` IN ($f_fsid)";
		$result_update_wt = mysqli_query($dbc, $query_update_wt);echo $html;

    $before_change = '';
    $history = "field_foreman_sheet entry has been updated for fsid -> $f_fsid. <br />";
    add_update_history($dbc, 'field_jobs_history', $history, '', $before_change);
		?>

	<script type="text/javascript" language="Javascript">
	alert('Invoice Generated.');
	window.location.replace('field_invoice.php?paytype=Unpaid');
	window.open('download/field_invoice_<?php echo $invoice_id;?>.pdf', 'fullscreen=yes');
	</script>

	<?php
	 mysqli_close($dbc); //Close the DB Connection
}
?>
<script type="text/javascript">
function numericFilter(txb) {
   txb.value = txb.value.replace(/[^\0-9]/ig, "");
}
function countTotal(txb) {
    var get_id = txb.id;
    var sub_total = $('#sub_total').val();
    var dis_dollor = 0;
	var total_after_gst = sub_total;
    if(get_id == 'from_perc' && txb.value != '') {
		dis_dollor = txb.value;
        var total_after = parseFloat((sub_total*dis_dollor)/100);
        total_after_gst = parseFloat(sub_total) - parseFloat(total_after);
		$('[name=dis_dollor]').val('');
    } else if(get_id == 'from_dollor' && txb.value != '') {
		dis_dollor = txb.value;
        total_after_gst = parseFloat(sub_total)-parseFloat(dis_dollor);
		$('[name=dis_perc]').val('');
    } else if (get_id == 'flat_rate_subtotal') {
		$('#sub_total').val(txb.value);
		sub_total = txb.value;
		total_after_gst = sub_total;
		if($('[name=dis_dollor]').val() != '') {
			var dis_dollor = $('[name=dis_dollor]').val();
			total_after_gst = parseFloat(sub_total)-parseFloat(dis_dollor);
		} else if($('[name=dis_perc]').val() != '') {
			var dis_dollor = $('[name=dis_perc]').val();
			var total_after = parseFloat((sub_total*dis_dollor)/100);
			total_after_gst = parseFloat(sub_total) - parseFloat(total_after);
		}
	}

    var total = round2Fixed(total_after_gst);
    $(".sub_total_change").text(round2Fixed(total));

    var gst_change = round2Fixed(total*0.05);
    $(".gst_change").text(round2Fixed(gst_change));

    var final_total = parseFloat(total)+parseFloat(gst_change);
    $(".total_change").text(round2Fixed(final_total));

    $("#pdf_sub_total").val(round2Fixed(total));
    $("#pdf_gst").val(round2Fixed(gst_change));
    $("#pdf_total").val(round2Fixed(final_total));
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');

?>
<div class="container">
  <div class="row">

		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<?php
		$jobid = $_GET['jobid'];
		$invoiceid = $_GET['invoiceid'] > 0 ? $_GET['invoiceid'] : 0;

        echo '<input type="hidden" name="jobid" value="'.$jobid.'">';
        echo '<input type="hidden" name="invoiceid" value="'.$invoiceid.'">';

        echo display_pdf_header($dbc, $jobid);

		if($_GET['mode'] != 'flat_rate') {
			echo '
                <table style="border:1px solid black; width:61%;" cellpadding="2">
                    <tr>
                        <th style="border:1px solid black; text-align:center; width:75%;"><strong>Work Ticket #</strong></th>
                        <th style="border:1px solid black; text-align:center; width:25%;"><strong>Amount</strong></th>
                    </tr>';

			$jobid = $_GET['jobid'];
			$quer_y = "SELECT * FROM field_work_ticket WHERE jobid = '$jobid' AND attach_invoice='$invoiceid' AND status='Approved'";
			$result = mysqli_query($dbc, $quer_y);
			$total_wt = 0;
			$fsid = '';

			while($row = mysqli_fetch_array( $result )) {
				echo '
                    <tr style="border-right:1px solid black;">
                        <td style="padding-right:5px; text-align:right;" width="75%">'. $row['workticketid'] .'
							<div style="display: inline-block; width: calc(100% - 8em);"><input placeholder="Add comment to invoice (optional)" type="text" class="form-control" name="comments[]" value="'. ( !empty($row['comments']) ? $row['comments'] : '' ) .'" /></div></td>
                        <td style="text-align:right; border-left:1px solid black" width="25%">$'.number_format((float)$row['sub_total'], 2, '.', '') . '</td>';
                        $total_wt += $row['sub_total'];
                    echo '</tr>';
                echo '<input type="hidden" name="workticketid[]" value="'. $row['workticketid'] .'" />';
				$fsid .= $row['fsid'].',';
			}
			echo '</table><br>';
		}

			$gst = $total_wt * 0.05;
			$total = ($total_wt+$gst);

          echo '<div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Sub Total:</label>
            <div class="col-sm-8 pad-5">
              <div id="wt_subtotal" style="'.($_GET['mode'] == 'flat_rate' ? 'display:none;' : '').'">$'.number_format((float)$total_wt, 2, '.', '').'</div>
			  <div id="flat_subtotal" style="'.($_GET['mode'] == 'flat_rate' ? '' : 'display:none;').'">
				<input type="number" class="form-control" id="flat_rate_subtotal" value="'.number_format((float)$total_wt,2).'" step="0.01" min=0 onchange="countTotal(this);" style="width: 17%;">
			  </div><br />
			  <label><input name="invoice_mode" value="flat_rate" type="checkbox" '.($_GET['mode'] == 'flat_rate' ? 'checked' : '').' onchange="$(\'#wt_subtotal,#flat_subtotal\').toggle();">Enter Flat Rate</label>
            </div>
          </div>';

          echo '<input type="hidden" id="sub_total" name="job_sub_total" value="'.number_format((float)$total_wt, 2, '.', '').'">';

          echo '<div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Discount(%):</label>
            <div class="col-sm-8">
              <input name="dis_perc" onKeyUp="numericFilter(this); countTotal(this);" style="width: 17%;" type="text" id="from_perc" class="form-control" /> OR
            </div>
          </div>';

          echo '<div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Discount($):</label>
            <div class="col-sm-8">
              <input name="dis_dollor" onKeyUp="numericFilter(this); countTotal(this);" style="width: 17%;" type="text" id="from_dollor" class="form-control" />
            </div>
          </div>';

          echo '<div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Sub Total:</label>
            <div class="col-sm-8 sub_total_change pad-5">
              $'.number_format((float)$total_wt, 2, '.', '').'
            </div>
          </div>';

          echo '<div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">GST:</label>
            <div class="col-sm-8 gst_change pad-5">
              $'.number_format((float)$gst, 2, '.', '').'
            </div>
          </div>';

          echo '<div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">Total:</label>
            <div class="col-sm-8 total_change pad-5">
              $'.number_format((float)$total, 2, '.', '').'
            </div>
          </div>';

          echo '<div class="form-group">
            <label for="site_name" class="col-sm-4 control-label">&nbsp;</label>
            <div class="col-sm-8">GST 83356 9379 RT0001</div>
          </div>';

          echo '<div class="form-group">
            <label for="site_name" class="col-sm-4 control-label"><b>Comments</b></label>
            <div class="col-sm-8"><textarea name="invoice_comments" class="noMceEditor form-control" style="height:150px; max-width:380px;"></textarea></div>
          </div>';

            echo '<input type="hidden" id="pdf_sub_total" name="pdf_sub_total"  value="'.number_format((float)$total_wt, 2, '.', '').'">';
            echo '<input type="hidden" id="pdf_gst" name="pdf_gst" value="'.number_format((float)$gst, 2, '.', '').'">';
            echo '<input type="hidden" id="pdf_total" name="pdf_total" value="'.number_format((float)$total, 2, '.', '').'">';

            echo '<div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">&nbsp;</label>
                <div class="col-sm-8"><button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button></div>
            </div>';

            ?>
		</form>
	</div>
  </div>

<?php include ('../footer.php'); ?>
<?php
function display_pdf_header($dbc, $jobid) {
		$job_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM  field_jobs  WHERE jobid = '$jobid'"));

		$job_number = $job_result['job_number'];
        $clientid = $job_result['clientid'];
		$contactid = $job_result['contactid'];
        $description= $job_result['description'];
		$employeeid = $job_result['employeeid'];
		$crew_reg_rate = $job_result['crew_reg_rate'];
		$crew_ot_rate = $job_result['crew_ot_rate'];
		$afe_num = $job_result['afe_number'];
        $additional_info = $job_result['additional_info'];
        $locationid = $job_result['siteid'];

        $loc = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT site_name from field_sites WHERE siteid = '$locationid'"));
        $location = $loc['site_name'];

		$invoice_date = date('Y-m-d');

		//$client = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT client_name, mail_street, mail_country, mail_city, mail_state, mail_zip FROM clients WHERE clientid='$clientid'"));

		$contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name FROM contacts WHERE contactid='$contactid'"));

		$html = '<table frame="box" style="border:1px solid black;">
				<tr><td style="width:10%" rowspan="3"><strong>Sold To: </strong></td><td rowspan="3" style="width: 40%; border-right:1px solid black;">' . get_client($dbc, $clientid).'<br>'.get_address($dbc, $clientid).'</td><td style="width:10%"><strong>AFE# </strong></td><td style="width:40%">'.$job_result['afe_number'].'</td></tr>
				<tr><td  style="width:10%"><strong>Location:</strong></td><td style="width:40%">'.$location.'</td></tr>
				<tr><td style="width:10%"><strong>Additional Info:</strong></td><td  style="width:40%">'.$additional_info.'</td></tr>
				<tr><td style="width:10%"><strong>Contact:</strong></td><td style="border-right:1px solid black;">'. get_staff($dbc, $contactid).'</td><td style="width:10%" ><strong>Job #</strong></td><td  style="width:40%">'.$job_result['job_number'].'</td></tr>
			</table><br><br>';
        return $html;
}
