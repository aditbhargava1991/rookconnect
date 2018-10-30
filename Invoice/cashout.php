<?php
/*
Payment/Invoice Listing
*/
include ('../include.php');
if(FOLDER_NAME == 'posadvanced') {
    checkAuthorised('posadvanced');
} else {
    checkAuthorised('check_out');
}
include_once('../tcpdf/tcpdf.php');
require_once('../phpsign/signature-to-image.php');

if (isset($_POST['print_cashout'])) {
    $sign = $_POST['output'];
    $img = sigJsonToImage($sign);
    $today_date = date('Y-m-d');
    // Save to file
    imagepng($img, 'Download/cashoutsign_'.$today_date.'.png');

	class MYPDF extends TCPDF {

		public function Header() {
			$image_file = WEBSITE_URL.'/img/Clinic-Ace-Logo-Final-250px.png';
			$this->SetFont('helvetica', '', 13);
            $this->Image($image_file, 0, 10, 60, '', 'PNG', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
            $footer_text = 'Daily cash balance sheet - '.date('Y-m-d');
            $this->writeHTMLCell(0, 0, 0 , 35, $footer_text, 0, 0, false, "R", true);
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

	$pdf->SetMargins(PDF_MARGIN_LEFT, 40, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage('L', 'LETTER');
    $pdf->SetFont('helvetica', '', 9);
    $html = '';

    $html .= '<h3>Invoice</h3>';
    $query_check_credentials = "SELECT invoiceid, final_price FROM invoice WHERE deleted = 0 AND DATE(invoice_date) = DATE(NOW()) AND paid = 'Yes' ORDER BY invoiceid";
    $result = mysqli_query($dbc, $query_check_credentials);

    $html .= '<table border="1px" style="padding:3px; border:1px solid black;">';
    $html .= '<tr><th>Invoice#</th>
    <th>Total</th>
    </tr>';
    $total = 0;
    while($row = mysqli_fetch_array( $result )) {
        $html .= '<tr>';
        $html .= '<td>' . $row['invoiceid'] . '</td>';
        $html .= '<td>' . $row['final_price'] . '</td>';
        $html .= '</tr>';
        $total += $row['final_price'];
    }
    $html .= '<tr style="background-color:light grey; color:black;"><td>Total</td><td>'.$total.'</td></tr>';
    $html .= '</table>';

    $html .= '<br><h3>Coins/Bills</h3>';
    $html .= '<table border="1px" style="padding:3px; border:1px solid black;">';
    $html .= '<tr>
    <th></th><th>$100</th><th>$50</th><th>$20</th><th>$10</th><th>$5</th><th>$2</th><th>$1</th><th>$0.25</th><th>$0.10</th><th>$0.05</th>
    </tr>';

    $html .= '<tr>';
    $html .= '<td>Qty</td>';
    $html .= '<td>' . $_POST['qty_coin_100'] . '</td>';
    $html .= '<td>' . $_POST['qty_coin_50'] . '</td>';
    $html .= '<td>' . $_POST['qty_coin_20'] . '</td>';
    $html .= '<td>' . $_POST['qty_coin_10'] . '</td>';
    $html .= '<td>' . $_POST['qty_coin_5'] . '</td>';
    $html .= '<td>' . $_POST['qty_coin_2'] . '</td>';
    $html .= '<td>' . $_POST['qty_coin_1'] . '</td>';
    $html .= '<td>' . $_POST['qty_coin_025'] . '</td>';
    $html .= '<td>' . $_POST['qty_coin_010'] . '</td>';
    $html .= '<td>' . $_POST['qty_coin_005'] . '</td>';
    $html .= '</tr>';
    $html .= '<tr>';
    $html .= '<td>Total</td>';
    $html .= '<td>' . $_POST['coin_100'] . '</td>';
    $html .= '<td>' . $_POST['coin_50'] . '</td>';
    $html .= '<td>' . $_POST['coin_20'] . '</td>';
    $html .= '<td>' . $_POST['coin_10'] . '</td>';
    $html .= '<td>' . $_POST['coin_5'] . '</td>';
    $html .= '<td>' . $_POST['coin_2'] . '</td>';
    $html .= '<td>' . $_POST['coin_1'] . '</td>';
    $html .= '<td>' . $_POST['coin_025'] . '</td>';
    $html .= '<td>' . $_POST['coin_010'] . '</td>';
    $html .= '<td>' . $_POST['coin_005'] . '</td>';
    $html .= '</tr>';

    $html .= '</table><br><br>';
    $html .= 'Total : '.$_POST['coin_total'];
    $html .= '<br>Float Amount : '.$_POST['float_amount'];
    $html .= '<br><br>Final Total : '.$_POST['final_price'];

    $html .= '<br><br><br><br>Verified By, <br><img src="Download/cashoutsign_'.$today_date.'.png" width="190" height="80" border="0" alt="">';

    $today_date = date('Y-m-d');
	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('Download/daily_cash_balance_sheet_'.$today_date.'.pdf', 'F');

    ?>

	<script type="text/javascript" language="Javascript">
	window.location.replace('cashout.php');
	window.open('Download/daily_cash_balance_sheet_<?php echo $today_date;?>.pdf', 'fullscreen=yes');
	</script>
<?php } ?>

<script src="<?php echo WEBSITE_URL;?>/js/jquery.cookie.js"></script>
<script type="text/javascript">
$(document).ready(function() {
});
function countTotalPrice(sel) {
    var coin_id = sel.id;
    var proValue = sel.value;

    if(coin_id == '025') {
        var coin_value = parseFloat(0.25);
    } else if(coin_id == '010') {
        var coin_value = parseFloat(0.10);
    } else if(coin_id == '005') {
        var coin_value = parseFloat(0.05);
    } else {
        var coin_value = parseFloat(coin_id);
    }

    var multiprice = parseFloat(coin_value*proValue);

    $('.'+coin_id).text(round2Fixed(multiprice));
    $('.'+coin_id).val(round2Fixed(multiprice));

    var sum_price = 0;
    $('.coinprice').each(function () {
        sum_price += +$(this).text() || 0;
    });

    $('.price').text(round2Fixed(parseFloat(sum_price)));
    $('.price').val(round2Fixed(parseFloat(sum_price)));

    var float_amount = $("#float_amount").val();

    $('.final_price').text(round2Fixed(parseFloat(sum_price-float_amount)));
    $('.final_price').val(round2Fixed(parseFloat(sum_price-float_amount)));
}
function view_cashout()
{
    $('.view_cashout').toggleClass('hidden');
}
</script>
<style>
form .form-control { width:10% !important; }
</style>

<div class="standard-body-title hide-titles-mob">
    <h3 class="pull-left">Cashout</h3>
    <div class="pull-right"><!--<img src="../img/icons/ROOK-3dot-icon.png" class="no-toggle cursor-hand offset-top-15 double-gap-right" title="" width="25" data-original-title="Show/Hide U<?/*= substr(PAYER_LABEL,0,1) */?> Invoice Report" onclick="view_cashout()">--></div>
    <div class="clearfix"></div>
</div>

<div class="standard-body-content padded-desktop ">
    <form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal padded" role="form">
        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11"><span class="notice-name">NOTE:</span>
            Cashout is used at the end of the day to account for all transactions including cash, credit/debit cards and refunds.</div>
            <div class="clearfix"></div>
        </div>
        <?php
        $today_date = date('Y-m-d');
        $filename = 'Download/daily_cash_balance_sheet_'.$today_date.'.pdf';

        if (file_exists($filename)) {
            echo '<a class="btn brand-btn pull-right" href="'.$filename.'" target="_blank">View Report</a>';
        }
        ?>

        <button type="submit" name="print_cashout" value="Print Report" class="btn brand-btn pull-right">Print Report</button>

        <div class="table-responsive">
        <h3>Bills</h3>

          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">$100</label>
            <div class="col-sm-8">
              <input onchange="countTotalPrice(this)" name="qty_coin_100" id="100" type="text" class="form-control" />
              <span class="100 coinprice"></span>
              <input type="hidden" class="100" name="coin_100">
            </div>
          </div>
          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">$50</label>
            <div class="col-sm-8">
              <input onchange="countTotalPrice(this)" name="qty_coin_50" id="50" type="text" class="form-control" />
              <span class="50 coinprice"></span>
              <input type="hidden" class="50" name="coin_50">
            </div>
          </div>

          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">$20</label>
            <div class="col-sm-8">
              <input onchange="countTotalPrice(this)" name="qty_coin_20" id="20" type="text" class="form-control" />
              <span class="20 coinprice"></span>
              <input type="hidden" class="20" name="coin_20">
            </div>
          </div>

          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">$10</label>
            <div class="col-sm-8">
              <input onchange="countTotalPrice(this)" name="qty_coin_10" id="10" type="text" class="form-control" />
              <span class="10 coinprice"></span>
              <input type="hidden" class="10" name="coin_10">
            </div>
          </div>

          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">$5</label>
            <div class="col-sm-8">
              <input onchange="countTotalPrice(this)" name="qty_coin_5" id="5" type="text" class="form-control" />
              <span class="5 coinprice"></span>
              <input type="hidden" class="5" name="coin_5">
            </div>
          </div>

          <h3>Coins</h3>
          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">$2</label>
            <div class="col-sm-8">
              <input onchange="countTotalPrice(this)" name="qty_coin_2" id="2" type="text" class="form-control" />
              <span class="2 coinprice"></span>
              <input type="hidden" class="2" name="coin_2">
            </div>
          </div>

          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">$1</label>
            <div class="col-sm-8">
              <input onchange="countTotalPrice(this)" name="qty_coin_1" id="1" type="text" class="form-control" />
              <span class="1 coinprice"></span>
              <input type="hidden" class="1" name="coin_1">
            </div>
          </div>

          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">$0.25</label>
            <div class="col-sm-8">
              <input onchange="countTotalPrice(this)" name="qty_coin_025" id="025" type="text" class="form-control" />
              <span class="025 coinprice"></span>
              <input type="hidden" class="025" name="coin_025">
            </div>
          </div>

          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">$0.10</label>
            <div class="col-sm-8">
              <input onchange="countTotalPrice(this)" name="qty_coin_010" id="010" type="text" class="form-control" />
              <span class="010 coinprice"></span>
              <input type="hidden" class="010" name="coin_010">
            </div>
          </div>

          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">$0.05</label>
            <div class="col-sm-8">
              <input onchange="countTotalPrice(this)" name="qty_coin_005" id="005" type="text" class="form-control" />
              <span class="005 coinprice"></span>
              <input type="hidden" class="005" name="coin_005">
            </div>
          </div>

          <hr style="border: 1px solid black; width: 20%; float: left;"><br><br><br>

          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">Total</label>
            <div class="col-sm-8">
              <span class="price"></span>
              <input type="hidden" class="price" name="coin_total">
            </div>
          </div>

          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">Float Amount</label>
            <div class="col-sm-8">
              <input onchange="countTotalPrice(this)" id="float_amount" name="float_amount" type="text" value="45" class="form-control" />
            </div>
          </div>

          <div class="form-group">
            <label for="site_name" class="col-sm-1 control-label">Final Total</label>
            <div class="col-sm-8">
              <span class="final_price"></span>
              <input type="hidden" class="final_price" name="final_price">
            </div>
          </div>

        <h3>Cash Invoices</h3>
        <?php
        //$query_check_credentials = "SELECT invoiceid, final_price FROM invoice WHERE deleted = 0 AND DATE(invoice_date) = DATE(NOW()) AND paid = 'Yes' ORDER BY invoiceid";
        $query_check_credentials = "SELECT invoiceid, payment_type, final_price FROM invoice WHERE deleted = 0 AND DATE(invoice_date) = DATE(NOW()) AND payment_type NOT LIKE '#*#%' AND payment_type LIKE '%CASH%' ORDER BY invoiceid";

        $result = mysqli_query($dbc, $query_check_credentials);

        echo "<table border='2' cellpadding='10' class='table'>";
        echo "<tr><th width='50%'>Invoice#</th>
        <th width='25%'>Cash Total</th>
        <th width='25%'>Invoice Total</th>
        </tr>";
        $cash_total = 0;
        $total = 0;
        while($row = mysqli_fetch_array( $result )) {
            $payments_array = explode('#*#', $row['payment_type']);
            $payment_types = reset($payments_array);
            $payment_amounts = end($payments_array);
            $payment_types_array = explode(',', $payment_types);
            $payment_amounts_array = explode(',', $payment_amounts);
            $payment_type_key = array_search('Cash', $payment_types_array);                
            echo '<tr>';
            echo '<td><a href="/'.FOLDER_NAME.'/download/invoice_'.$row['invoiceid'].'.pdf" target="_blank">' . $row['invoiceid'] . '</a></td>';
            echo '<td>' . number_format($payment_amounts_array[$payment_type_key],2) . '</td>';
            echo '<td>' . number_format($row['final_price'],2) . '</td>';
            echo '</tr>';
            $cash_total += $payment_amounts_array[$payment_type_key];
            $total += $row['final_price'];
        }
        echo '<tr><td><b>Total</b></td><td><b>'.number_format($cash_total,2).'</b></td><td><b>'.number_format($total,2).'</b></td></tr>';
        echo '</table>';
        ?>
        <h3>Credit Invoices</h3>
        <?php
        //$query_check_credentials = "SELECT invoiceid, final_price FROM invoice WHERE deleted = 0 AND DATE(invoice_date) = DATE(NOW()) AND paid = 'Yes' ORDER BY invoiceid";
        //$query_check_credentials = "SELECT invoiceid, payment_type, final_price FROM invoice WHERE deleted = 0 AND DATE(invoice_date) = DATE(NOW()) AND payment_type NOT LIKE '#*#%' AND payment_type NOT LIKE 'CASH%' AND `payment_type` NOT LIKE 'Gift Card%' ORDER BY invoiceid";
        $query_check_credentials = "SELECT invoiceid, payment_type, final_price FROM invoice WHERE deleted = 0 AND DATE(invoice_date) = DATE(NOW()) AND payment_type NOT LIKE '#*#%' ORDER BY invoiceid";

        $result = mysqli_query($dbc, $query_check_credentials);
        
        echo "<table border='2' cellpadding='10' class='table'>";
        echo "<tr><th width='50%'>Invoice#</th>
        <th width='25%'>Credit Total</th>
        <th width='25%'>Invoice Total</th>
        </tr>";
        $credit_total = 0;
        $total = 0;
        while($row = mysqli_fetch_array( $result )) {
            $payments_array = explode('#*#', $row['payment_type']);
            $payment_types = reset($payments_array);
            $payment_amounts = end($payments_array);
            $payment_types_array = explode(',', $payment_types);
            $payment_amounts_array = explode(',', $payment_amounts);
            
            //Find and remvoe Cash & Gift Card values
            if ( ($cash_key = array_search('Cash', $payment_types_array)) !== false ) {
                unset($payment_amounts_array[$cash_key]);
            }
            if ( ($gc_key = array_search('Gift Card', $payment_types_array)) !== false ) {
                unset($payment_amounts_array[$gc_key]);
            }
            
            //Remove empty values (cash or gift card values)
            $payment_amounts_array = array_filter($payment_amounts_array);
        
            foreach ($payment_amounts_array as $amount) {
                echo '<tr>';
                echo '<td><a href="/'.FOLDER_NAME.'/download/invoice_'.$row['invoiceid'].'.pdf" target="_blank">' . $row['invoiceid'] . '</a></td>';
                echo '<td>';
                    echo number_format($amount, 2) .'<br />';
                    $credit_total += $amount;
                echo '</td>';
                echo '<td>' . number_format($row['final_price'],2) . '</td>';
                echo '</tr>';
                $total += $row['final_price'];
            }
        }
        echo '<tr><td><b>Total</b></td><td><b>'.number_format($credit_total,2).'</b></td><td><b>'.number_format($total,2).'</b></td></tr>';
        echo '</table>';
        ?>

        <h3>Gift Card Invoices</h3>
        <?php
        //$query_check_credentials = "SELECT invoiceid, final_price FROM invoice WHERE deleted = 0 AND DATE(invoice_date) = DATE(NOW()) AND paid = 'Yes' ORDER BY invoiceid";
        $query_check_credentials = "SELECT invoiceid, payment_type, final_price FROM invoice WHERE deleted = 0 AND DATE(invoice_date) = DATE(NOW()) AND payment_type NOT LIKE '#*#%' AND payment_type LIKE '%Gift Card%' ORDER BY invoiceid";

        $result = mysqli_query($dbc, $query_check_credentials);

        echo "<table border='2' cellpadding='10' class='table'>";
        echo "<tr><th width='50%'>Invoice#</th>
        <th width='25%'>Gift Card Total</th>
        <th width='25%'>Invoice Total</th>
        </tr>";
        $gc_total = 0;
        $total = 0;
        while($row = mysqli_fetch_array( $result )) {
            $payments_array = explode('#*#', $row['payment_type']);
            $payment_types = reset($payments_array);
            $payment_amounts = end($payments_array);
            $payment_types_array = explode(',', $payment_types);
            $payment_amounts_array = explode(',', $payment_amounts);
            $payment_type_key = array_search('Gift Card', $payment_types_array);
            echo '<tr>';
            echo '<td><a href="/'.FOLDER_NAME.'/download/invoice_'.$row['invoiceid'].'.pdf" target="_blank">' . $row['invoiceid'] . '</a></td>';
            echo '<td>' . number_format($payment_amounts_array[$payment_type_key],2) . '</td>';
            echo '<td>' . number_format($row['final_price'],2) . '</td>';
            echo '</tr>';
            $gc_total += $payment_amounts_array[$payment_type_key];
            $total += $row['final_price'];
        }
        echo '<tr><td><b>Total</b></td><td><b>'.number_format($gc_total,2).'</b></td><td><b>'.number_format($total,2).'</b></td></tr>';
        echo '</table>';
        ?>

        <h3>Refund Invoices</h3>
        <?php
        //$query_check_credentials = "SELECT invoiceid, final_price FROM invoice WHERE deleted = 0 AND DATE(invoice_date) = DATE(NOW()) AND paid = 'Yes' ORDER BY invoiceid";
        $query_check_credentials = "SELECT invoiceid, final_price FROM invoice WHERE deleted = 0 AND DATE(refund_date) = DATE(NOW()) ORDER BY invoiceid";

        $result = mysqli_query($dbc, $query_check_credentials);

        echo "<table border='2' cellpadding='10' class='table'>";
        echo "<tr><th>Invoice#</th>
        <th>Total</th>
        </tr>";

        $total = 0;
        while($row = mysqli_fetch_array( $result )) {
            echo '<tr>';
            echo '<td><a href="/'.FOLDER_NAME.'/download/invoice_'.$row['invoiceid'].'.pdf" target="_blank">' . $row['invoiceid'] . '</a></td>';
            echo '<td>' . number_format($row['final_price'],2) . '</td>';
            echo '</tr>';
            $total += $row['final_price'];
        }
        echo '<tr><td><b>Total</td></b><td><b>'.number_format($total,2).'</b></td></tr>';
        echo '</table>';
        include ('../phpsign/sign.php');
        ?>
        </div>

    </form>
</div>