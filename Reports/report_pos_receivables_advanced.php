<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

/*
if(isset($_POST['printcsv'])) {
  $starttime = $_POST['starttime'];
  $endtime = $_POST['endtime'];
  $contactid = $_POST['contactid'];
  $file_name = "report_pos_" . date("Y-m-d_m") . '.csv';

  ob_end_clean();
  $fp = fopen('php://output','w');
  header('Content-Type: text/csv; charset=utf-8');
  header('Content-Disposition: attachment; filename='.$file_name);

  $total = 0;
  $data[] = 'Invoice#,Invoice Date,Customer,Sub Total,Discount,Delivery,Assembly,Total Before Tax,GST,PST,Total,Status';

   if($contactid == '') {
       $report_validation = mysqli_query($dbc,"SELECT * FROM point_of_sell WHERE deleted = 0 AND status != 'Completed' AND (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."')");
   } else {
       $report_validation = mysqli_query($dbc,"SELECT inv.*, c.* FROM point_of_sell inv,  contacts c WHERE inv.contactid = c.contactid AND status != 'Completed' AND inv.deleted = 0 AND c.name = '$contactid' AND (DATE(invoice_date) >= '".$starttime."' AND DATE(invoice_date) <= '".$endtime."') AND c.deleted=0 AND c.status=1");
   }
   $num_rows = mysqli_num_rows($report_validation);
   while($row_report = mysqli_fetch_array($report_validation)) {
      $row = '';
      if($row_report['status'] == 'Posted Past Due') {
          $style = 'style = color:red;';
      }
      if($row_report['status'] == 'Posted') {
          $style = 'style = color:Green;';
      }

      $report_data .= '<tr nobr="true" '.$style.'>';

      $row .= $row_report['posid'] . ',';
      $row .= $row_report['invoice_date'] . ',';
      $row .= get_client($dbc, $row_report['contactid']) . ',';
      $row .= $row_report['sub_total'] . ',';
      $row .= $row_report['discount_value'] . ',';
      $row .= $row_report['delivery'] . ',';
      $row .= $row_report['assembly'] . ',';
      $row .= $row_report['total_before_tax'] . ',';
      $row .= $row_report['gst'] . ',';
      $row .= $row_report['pst'] . ',';
      $row .= $row_report['total_price'] . ',';
      $row .= $row_report['status'] . ',';
      $sub_total += $row_report['sub_total'];
      $discount_value += $row_report['discount_value'];
      $delivery += $row_report['delivery'];
      $assembly += $row_report['assembly'];
      $total_before_tax += $row_report['total_before_tax'];
      $gst += $row_report['gst'];
      $pst += $row_report['pst'];
      $total_price += $row_report['total_price'];
      $total++;
      $data[] = $row;
  }

  $data[] = ',,Total Invoice - '.$total.','.number_format($sub_total, 2).','.number_format($discount_value, 2).','.number_format($delivery, 2).
            ','.number_format($assembly, 2).','.number_format($total_before_tax, 2).','.number_format($gst, 2).','.number_format($pst, 2).','.number_format($total_price, 2);

  foreach ($data as $line) {
      $val = explode(",", $line);
      fputcsv($fp, $val);
  }
  exit();
}
*/

if (isset($_POST['printpdf'])) {
    $starttimepdf = $_POST['starttimepdf'];
    $endtimepdf = $_POST['endtimepdf'];

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
                $this->Image($image_file, 10, 10, '', 20, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
            }
            $this->setCellHeightRatio(0.7);
            $this->SetFont('helvetica', '', 9);
            $footer_text = '<p style="text-align:right;">'.REPORT_HEADER.'</p>';
            $this->writeHTMLCell(0, 0, 0 , 5, $footer_text, 0, 0, false, "R", true);

            $this->SetFont('helvetica', '', 13);
            $footer_text = 'POS Receivables From <b>'.START_DATE.'</b> To <b>'.END_DATE.'</b>';
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

    $html .= report_receivables($dbc, $starttimepdf, $endtimepdf, 'padding:3px; border:1px solid black;', '', 'background-color:lightgrey; color:black;');

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/receivables_'.$today_date.'.pdf', 'F');
    track_download($dbc, 'report_pos_receivables', 0, WEBSITE_URL.'/Reports/Download/receivables_'.$today_date.'.pdf', 'POS Receivables Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/receivables_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
    <?php
    $starttime = $starttimepdf;
    $endtime = $endtimepdf;
} ?>

<div id="invoice_div">
<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal triple-gap-top" role="form">
    <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
    <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

    <?php
    $contactid = '';
    if (isset($_POST['search_email_submit'])) {
        $starttime = $_POST['starttime'];
        $endtime = $_POST['endtime'];
        $contactid = $_POST['contactid'];
    }

    if($starttime == 0000-00-00) {
        $starttime = date('Y-m-01');
    }

    if($endtime == 0000-00-00) {
        $endtime = date('Y-m-d');
    }
    ?>
    <center>
        <div class="form-group">
            <div class="form-group col-sm-5">
                <label class="col-sm-4">From:</label>
                <div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
            </div>
            <div class="form-group col-sm-5">
                <label class="col-sm-4">Until:</label>
                <div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
            </div>
            <div class="form-group col-sm-5">
                <label class="col-sm-4">Customer:</label>
                <div class="col-sm-8">
                    <select name="contactid" data-placeholder="Select a Customer..." class="chosen-select-deselect form-control1" width="380">
                        <option value=''>Customer</option>
                        <?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, name FROM contacts WHERE (category='Customer' OR category='Customers') AND deleted=0 AND status=1"),MYSQLI_ASSOC));
                        foreach($query as $rowid) {
                            echo "<option ".($contactid == $rowid ? 'selected' : '')." value='$rowid'>".get_contact($dbc, $rowid, 'name_company')."</option>";
                        } ?>
                    </select>
                </div>
            </div>
            <button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button>
        </div>
    </center>

    <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
    <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
    <input type="hidden" name="contactidpdf" value="<?php echo $contactid; ?>">

    <!--
    <button type="submit" name="printcsv" value="Print CSV Report" title="Print CSV Report" class="pull-right"><img title="Print CSV Report" width="15px" src="../img/csv.png"></button>
    -->
    <button type="submit" name="printpdf" style="margin-right:15px" value="Print PDF Report" title="Print PDF Report" title="Print PDF Report" class="pull-right"><img src="../img/pdf.png"></button>
    <br><br>

    <?php echo report_receivables($dbc, $starttime, $endtime, $contactid, '', '', ''); ?>

</form>
</div>
<?php
function report_receivables($dbc, $starttime, $endtime, $contactid, $table_style, $table_row_style, $grand_total_style) {

    $search_clause = '';
    if($contactid > 0) {
        $search_clause .= " AND `patientid`='$contactid'";
    }
    if($starttime != '') {
        $search_clause .= " AND `invoice_date` >= '$starttime'";
    }
    if($endtime != '') {
        $search_clause .= " AND `invoice_date` <= '$endtime'";
    }

    $report_validation = mysqli_query($dbc,"SELECT invoiceid, invoice_type, patientid, invoice_date, final_price, payment_type, delivery_type, status, comment FROM invoice WHERE deleted = 0 AND `status` != 'Void' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice_patient` WHERE `paid` = 'Net 30' OR `paid`='On Account' OR `paid`='' OR `paid` IS NULL UNION SELECT `invoiceid` FROM `invoice_insurer` WHERE `paid`!='Yes') $search_clause ORDER BY invoiceid DESC");

    $num_rows = mysqli_num_rows($report_validation);

    if($num_rows > 0) {
        $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">
        <tr style="'.$table_row_style.'">
                        <th>Invoice #</th>
                        <th>Invoice Date</th>
                        <th>Customer</th>
                        <th>Total Price</th>
                        <th>Payment Type</th>
                        <th>Delivery/Shipping Type</th>
                        <th>Status</th>
        </tr>';

        $odd_even = 0;

        while($invoice = mysqli_fetch_array($report_validation)) {
            $bg_class = $odd_even % 2 == 0 ? '' : 'background-color:#e6e6e6;';

            $report_data .= '<tr nobr="true" style="'.$bg_class.'">';
                $report_data .= '<td data-title="Invoice #">' .($invoice['invoice_type'] == 'New' ? '#' : $invoice['invoice_type'].' #'). $invoice['invoiceid'] . ' <a href="../POSAdvanced/download/invoice_'.$invoice['invoiceid'].'.pdf" target="_blank"><img src="../img/pdf.png" alt="Invoice PDF" /></a></td>';
                $report_data .= '<td data-title="Invoice Date" style="white-space: nowrap; ">'.$invoice['invoice_date'].'</td>';
                $report_data .= '<td data-title="Customer"><a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/'.CONTACTS_TILE.'/contacts_inbox.php?edit='.$invoice['patientid'].'\', \'auto\', false, true, $(\'#invoice_div\').outerHeight()+20); return false;">' . get_contact($dbc, $invoice['patientid']) . '</a></td>';
                $report_data .= '<td data-title="Total Price" align="right">$' . number_format($invoice['final_price'],2) . '</td>';
                $report_data .= '<td data-title="Payment Type">' . explode('#*#',$invoice['payment_type'])[0] . '</td>';
                $report_data .= '<td data-title="Delivery">' . $invoice['delivery_type'] . '</td>';
                $report_data .= '<td data-title="Comment">' .  $invoice['status'] . '</td>';
            $report_data .= "</tr>";

            $odd_even++;
        }

        $report_data .= '</table>';
    }

    return $report_data;
}

?>
