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
            $footer_text = 'Postal Code Analysis ('.STARTTIME.' to '.ENDTIME.')';
            $this->writeHTMLCell(0, 0, 10, 35, $footer_text, 0, 0, false, "C", true);

            $this->setCellHeightRatio(1.30);
            $this->SetFont('helvetica', '', 10);
            $footer_text = "NOTE : How many total Patients come from each postal code.";
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

    if(!empty($_POST['bookingtype'])) {
        $bookingtype = $_POST['bookingtype'];
        $postalcode = $_POST['postalcode'];
        $html .= report_postalcode_clients($dbc, $starttime, $endtime, $bookingtype, $postalcode, 'padding:3px; border:1px solid black;', '', '', true);
        $pdf_url = 'postalcode_'.$bookingtype.'_'.$postalcode.'_'.$starttime.'_'.$endtime.'.pdf';
    } else {
        $html .= report_postalcode($dbc, $starttime, $endtime, 'padding:3px; border:1px solid black;', '', '', true);
        $pdf_url = 'postalcode_'.$starttime.'_'.$endtime.'.pdf';
    }

	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/'.$pdf_url, 'F');
    track_download($dbc, 'report_postalcode', 0, WEBSITE_URL.'/Reports/Download/'.$pdf_url, 'Postal Code Analysis Report');
    ?>

	<script type="text/javascript" language="Javascript">
	window.open('Download/<?= $pdf_url ?>', 'fullscreen=yes');
	</script>
    <?php
} ?>

        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            How many total Patients come from each postal code within selected date range.</div>
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
			<center><div class="form-group">
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

            <?php if(!empty($_GET['bookingtype'])) { ?>
                <a href="?type=marketing&from=<?= $starttime ?>&until=<?= $endtime ?>" class="btn brand-btn pull-left">Back</a>
            <?php } ?>
            <button type="submit" name="printpdf" value="<?= (!empty($_GET['bookingtype']) ? 'Print Report Clients' : 'Print Report') ?>" class="btn brand-btn pull-right">Print Report</button>
            <br><br>

            <?php if(!empty($_GET['bookingtype'])) {
                echo report_postalcode_clients($dbc, $starttime, $endtime, $_GET['bookingtype'], $_GET['postalcode'] , '', '', '');
            } else {
                echo report_postalcode($dbc, $starttime, $endtime, '', '', '');
            } ?>

        </form>

<?php
function report_postalcode($dbc, $starttime, $endtime, $table_style, $table_row_style, $grand_total_style, $pdf_print=false) {
    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
        <th>Postal Code</th>
        <th>Total Unique Patient(s)</th>
        <th>Total Unique MVC Patient(s)</th>
        <th>Total Unique Private Patient(s)</th>
        <th>Total Patient(s)</th>
    </tr>';

    $total_contact = mysqli_query($dbc,"SELECT c.contactid, c.business_zip, c.ship_zip, c.zip_code, c.postal_code, b.type FROM contacts c,invoice i,booking b WHERE  c.contactid=i.patientid AND i.bookingid = b.bookingid AND (DATE(service_date) >= '".$starttime."' AND DATE(service_date) <= '".$endtime."') AND c.deleted=0 AND c.status=1");
    $zip_three = array();
    $zip_three_unique = array();
    $zip_three_unique_mvc = array();
    $zip_three_unique_private = array();
    $mvc_types = ['C','D','F','G','L','M'];
    $private_types = ['A','B','K','U'];
    while($row_report = mysqli_fetch_array($total_contact)) {
        $this_zip = '';
        $type = $row_report['type'];
        if($row_report['business_zip'] != '') {
            $zip_three[] = strtolower(substr(decryptIt($row_report['business_zip']), 0, 3));
            $this_zip = strtolower(substr(decryptIt($row_report['business_zip']), 0, 3));
        } else if($row_report['postal_code'] != '') {
            $zip_three[] = strtolower(substr(decryptIt($row_report['postal_code']), 0, 3));
            $this_zip = strtolower(substr(decryptIt($row_report['postal_code']), 0, 3));
		} else if($row_report['zip_code'] != '') {
            $zip_three[] = strtolower(substr(decryptIt($row_report['zip_code']), 0, 3));
            $this_zip = strtolower(substr(decryptIt($row_report['zip_code']), 0, 3));
		} else if($row_report['ship_zip'] != '') {
            $zip_three[] = strtolower(substr(decryptIt($row_report['ship_zip']), 0, 3));
            $this_zip = strtolower(substr(decryptIt($row_report['ship_zip']), 0, 3));
		}
        if(!empty($this_zip)) {
            if(!in_array($row_report['contactid'], $zip_three_unique[$this_zip])) {
                $zip_three_unique[$this_zip][] = $row_report['contactid'];
            }
            if(!in_array($row_report['contactid'], $zip_three_unique_mvc[$this_zip]) && in_array($type, $mvc_types)) {
                $zip_three_unique_mvc[$this_zip][] = $row_report['contactid'];
            }
            if(!in_array($row_report['contactid'], $zip_three_unique_private[$this_zip]) && in_array($type, $private_types)) {
                $zip_three_unique_private[$this_zip][] = $row_report['contactid'];
            }
        }
    }
    asort($zip_three);
    $occurences = array_count_values($zip_three);

    foreach ($occurences as $key => $value) {
        $report_data .= '<tr nobr="true">';
        $report_data .= '<td>'.$key.'</td>';
        $report_data .= '<td>';
        if(count($zip_three_unique[$key]) > 0 && !$pdf_print) {
            $report_data .= '<a href="?type=marketing&from='.$starttime.'&until='.$endtime.'&bookingtype=all&postalcode='.$key.'">'.count($zip_three_unique[$key]).'</a>';
        } else {
            $report_data .= count($zip_three_unique[$key]);
        }
        $report_data .= '</td>';
        $report_data .= '<td>';
        if(count($zip_three_unique_mvc[$key]) > 0 && !$pdf_print) {
            $report_data .= '<a href="?type=marketing&from='.$starttime.'&until='.$endtime.'&bookingtype=mvc&postalcode='.$key.'">'.count($zip_three_unique_mvc[$key]).'</a>';
        } else {
            $report_data .= count($zip_three_unique_mvc[$key]);
        }
        $report_data .= '</td>';
        $report_data .= '<td>';
        if(count($zip_three_unique_private[$key]) > 0 && !$pdf_print) {
            $report_data .= '<a href="?type=marketing&from='.$starttime.'&until='.$endtime.'&bookingtype=private&postalcode='.$key.'">'.count($zip_three_unique_private[$key]).'</a>';
        } else {
            $report_data .= count($zip_three_unique_private[$key]);
        }
        $report_data .= '</td>';
        $report_data .= '<td>'.$value.'</td>';
        $report_data .= "</tr>";
    }
    $report_data .= '</table>';

    return $report_data;
}

function report_postalcode_clients($dbc, $starttime, $endtime, $bookingtype, $postalcode, $table_style, $table_row_style, $grand_total_style, $pdf_print=false) {
    if($bookingtype == 'mvc') {
        $header_text .= 'Unique MVC Patient(s) for Postal Code '.$_GET['postalcode'];
        $type_query = " AND b.type IN ('C','D','F','G','L','M') ";
    } else if($bookingtype == 'private') {
        $header_text .= 'Unique MVC Patient(s) for Postal Code '.$_GET['postalcode'];
        $type_query = " AND b.type IN ('A','B','K','U') ";
    } else {
        $header_text .= 'Unique Patient(s) for Postal Code '.$_GET['postalcode'];
        $type_query = "";
    }
    $report_data .= '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
    $report_data .= '<tr style="'.$table_row_style.'">
        <th colspan="5" style="text-align: center;">'.$header_text.'</th>
    </tr>';

    $total_contact = mysqli_query($dbc,"SELECT c.contactid, c.first_name, c.last_name, c.name, c.business_zip, c.ship_zip, c.zip_code, c.postal_code, b.type FROM contacts c,invoice i,booking b WHERE  c.contactid=i.patientid AND i.bookingid = b.bookingid AND (DATE(service_date) >= '".$starttime."' AND DATE(service_date) <= '".$endtime."') AND c.deleted=0 AND c.status=1 $type_query");
    $contacts = array();
    while($row_report = mysqli_fetch_array($total_contact)) {
        $this_zip = '';
        if($row_report['business_zip'] != '') {
            $this_zip = strtolower(substr(decryptIt($row_report['business_zip']), 0, 3));
        } else if($row_report['postal_code'] != '') {
            $this_zip = strtolower(substr(decryptIt($row_report['postal_code']), 0, 3));
        } else if($row_report['zip_code'] != '') {
            $this_zip = strtolower(substr(decryptIt($row_report['zip_code']), 0, 3));
        } else if($row_report['ship_zip'] != '') {
            $this_zip = strtolower(substr(decryptIt($row_report['ship_zip']), 0, 3));
        }
        if($this_zip == $postalcode) {
            if(!in_array($row_report['contactid'], $contacts)) {
                $contacts[] = $row_report;
            }
        }
    }
    $contacts = sort_contacts_array($contacts);
    $i = 0;
    foreach($contacts as $id) {
        $contact = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$id'"));
        if($i == 0) {
            $report_data .= '<tr nobr="true">';
        }
        $report_data .= '<td width="20%">';
        if(!$pdf_print) {
            $report_data .= '<a href="../'.ucfirst($contact['tile_name']).'/contacts_inbox.php?category='.$contact['category'].'&edit='.$id.'">'.get_contact($dbc, $id).'</a>';
        } else {
            $report_data .= get_contact($dbc, $id);
        }
        $report_data .= '</td>';
        $i++;
        if($i == 5) {
            $report_data .= '</tr>';
            $i = 0;
        }
    }
    for($i; $i < 5 && $i != 0; $i++) {
        $report_data .= '<td width="20%"></td>';
        if($i == 4) {
            $report_data .= '</tr>';
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