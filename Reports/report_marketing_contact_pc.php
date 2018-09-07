<?php
/*
Client Listing
*/
include ('../include.php');
checkAuthorised('report');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['printpdf'])) {
    $starttime = $_POST['starttimepdf'];
    $endtime = $_POST['endtimepdf'];

    DEFINE('REPORT_LOGO', get_config($dbc, 'report_logo'));
    DEFINE('REPORT_HEADER', html_entity_decode(get_config($dbc, 'report_header')));
    DEFINE('REPORT_FOOTER', html_entity_decode(get_config($dbc, 'report_footer')));
    DEFINE('STARTTIME', $starttime);
    DEFINE('ENDTIME', $endtime);

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
            $footer_text = 'Postal Code Analysis';
            $this->writeHTMLCell(0, 0, 10, 35, $footer_text, 0, 0, false, "C", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : How many total Contacts come from each postal code.";
            $this->writeHTMLCell(0, 0, 10 , 45, $footer_text, 0, 0, false, "C", true);
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 55, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);

    $time = date("Y-m-d-H-m-s");
    $contact_category = $_GET['subtype'];
    if(isset($_GET['postal_code']) && $_GET['postal_code'] != '') {
      $postal_code = $_GET['postal_code'];
      $html .= report_postalcode($dbc, $contact_category, $starttime, $endtime, $postal_code, '', '', '');
    }
    else
      $html .= report_postalcode($dbc, $contact_category, $starttime, $endtime, '', 'padding:3px; border:1px solid black;', '', '', true);
    $pdf_url = 'postalcode_'.$time.'.pdf';

	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/'.$pdf_url, 'F');
    track_download($dbc, 'report_marketing_contact_pc', 0, WEBSITE_URL.'/Reports/Download/postalcode_'.$time.'.pdf', 'Postal Code Analysis Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/<?= $pdf_url ?>', 'fullscreen=yes');
	</script>
    <?php
} ?>
        <br>
        <?php
        $select_query = "select distinct(category) from contacts";
        $select_result = mysqli_query($dbc, $select_query);
        while($row = mysqli_fetch_array($select_result)) {
        ?>
          <?php if($row['category'] != ''): ?>
            <?php $contact_category = $_GET['subtype']; ?>
            <?php if($contact_category == $row['category']): ?>
                <?php $active = 'active_tab'; ?>
            <?php else: ?>
                <?php $active = ''; ?>
            <?php endif; ?>
            <div class="tab pull-left"><a href='?type=<?= $_GET['type'] ?>&report=<?= $_GET['report'] ?>&subtype=<?= $row["category"] ?>'><button type="button" class="btn brand-btn mobile-block mobile-100 <?php echo $active; ?>" ><?php echo $row['category']; ?></button></a></div>
          <?php endif; ?>
        <?php } ?>
        <br><br><br>
        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            How many total Contacts come from each postal code.</div>
            <div class="clearfix"></div>
        </div>

        <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
            <input type="hidden" name="report_type" value="<?php echo $_GET['type']; ?>">
            <input type="hidden" name="category" value="<?php echo $_GET['category']; ?>">

           <?php
            if (isset($_POST['search_email_submit'])) {
                $starttime = $_POST['starttime'];
                $endtime = $_POST['endtime'];
            }

            if(!empty($_GET['from'])) {
                $starttime = $_GET['from'];
            } else if($starttime == 0000-00-00) {
                $starttime = date('Y-m-01');
            }

            if(!empty($_GET['until'])) {
                $endtime = $_GET['until'];
            } else if($endtime == 0000-00-00) {
                $endtime = date('Y-m-d');
            }


            ?>
			<center><!--<div class="form-group">
				<div class="form-group col-sm-5">
					<label class="col-sm-4">From:</label>
					<div class="col-sm-8"><input name="starttime" type="text" class="datepicker form-control" value="<?php echo $starttime; ?>"></div>
				</div>
				<div class="form-group col-sm-5">
					<label class="col-sm-4">Until:</label>
					<div class="col-sm-8"><input name="endtime" type="text" class="datepicker form-control" value="<?php echo $endtime; ?>"></div>
				</div>
			<button type="submit" name="search_email_submit" value="Search" class="btn brand-btn mobile-block">Submit</button></div></center>

            <input type="hidden" name="starttimepdf" value="<?php echo $starttime; ?>">
            <input type="hidden" name="endtimepdf" value="<?php echo $endtime; ?>">
            <input type="hidden" name="bookingtype" value="<?= $_GET['bookingtype'] ?>">
            <input type="hidden" name="postalcode" value="<?= $_GET['postalcode'] ?>">
-->
            <?php if(isset($_GET['postal_code'])) { ?>
                <a href="?type=<?= $_GET['type'] ?>&subtype=<?= $_GET['subtype'] ?>" class="btn brand-btn pull-left">Back</a>
            <?php } ?>
            <button type="submit" name="printpdf" value="<?= (!empty($_GET['bookingtype']) ? 'Print Report Clients' : 'Print Report') ?>" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php
                $contact_category = $_GET['subtype'];
                if(isset($_GET['postal_code'])) {
                  $postal_code = filter_var($_GET['postal_code'],FILTER_SANITIZE_STRING);
                  echo report_postalcode($dbc, $contact_category, $starttime, $endtime, $postal_code, '', '', '');
                }
                else
                  echo report_postalcode($dbc, $contact_category, $starttime, $endtime, false, '', '');
            ?>

        </form>


<?php
function report_postalcode($dbc, $contact_category, $starttime, $endtime, $postal_code = false, $table_style, $table_row_style, $grand_total_style, $pdf_print=false) {
    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    if($postal_code !== false) {
      $query = "select contactid, first_name, last_name, category, `postal_code` from contacts where category = '$contact_category' and ((IFNULL(`postal_code`,'') LIKE '$postal_code%' AND '$postal_code' != '') OR IFNULL(`postal_code`,'')='$postal_code') AND `deleted`=0";
      $all_contacts = mysqli_query($dbc, $query);
      $report_data .= '<tr style="'.$table_row_style.'">
          <th>Contacts for Postal Code '.$postal_code.'</th>
          <th>Postal Code</th>
      </tr>';
	  foreach(sort_contacts_query($all_contacts) as $contact) {
        $report_data .= '<tr nobr="true">';
        $report_data .= '<td><a href="../Contacts/contacts_inbox.php?category='.$contact['category'].'&edit='.$contact['contactid'].'">'.$contact['first_name'].' '.$contact['last_name'].'</a></td>';
        $report_data .= '<td>'.$contact['postal_code'].'</td>';
        $report_data .= '</tr>';
      }
    }
    else {
        $report_data .= '<tr style="'.$table_row_style.'">
            <th>Postal Code</th>
            <th>Total Contact(s)</th>
        </tr>';

        $query = "select LTRIM(IFNULL(postal_code,'')) postal_code, count(distinct(contactid)) as contact_count from contacts where category = '$contact_category' AND `deleted`=0 group by LTRIM(IFNULL(postal_code,'')) ORDER BY LTRIM(IFNULL(postal_code,''))";
        $all_post_codes = mysqli_query($dbc, $query);

        while($all_post_code = mysqli_fetch_array($all_post_codes)) {
            $report_data .= '<tr nobr="true">';
            $report_data .= '<td>'.($all_post_code['postal_code'] == '' ? 'No Postal Code' : substr($all_post_code['postal_code'], 0, 3)).'</td>';
            //$report_data .= '<td><a href="?type='.$_GET['type'].'&subtype='.$_GET['subtype'].'&postal_code='.substr($all_post_code['postal_code'], 0, 3).'">'. substr($all_post_code['contact_count'], 0, 3).'</a></td>';
            $report_data .= '<td>'. substr($all_post_code['contact_count'], 0, 3).'</td>';
            $report_data .= "</tr>";
        }
    }

    $report_data .= '</table>';

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
