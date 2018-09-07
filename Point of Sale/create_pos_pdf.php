<?php
function create_pos5_pdf($dbc, $posid, $d_value, $comment, $gst_total, $pst_total, $rookconnect, $edit_id) {
	//include ('../database_connection.php');
    $point_of_sell  = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM point_of_sell WHERE posid='$posid'"));
    $contactid		= $point_of_sell['contactid'];
	$couponid		= $point_of_sell['couponid'];
	$coupon_value	= $point_of_sell['coupon_value'];
	$dep_total		= $point_of_sell['deposit_total'];
	$updatedtotal	= $point_of_sell['updatedtotal'];
    $customer       = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid='$contactid'"));

	if ( $edit_id == '0' ) {
		$edited = '';
	} else {
		$edited = '_' . $edit_id;
	}

    //Tax
    $point_of_sell_product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(gst) AS total_gst, SUM(pst) AS total_pst FROM point_of_sell_product WHERE posid='$posid'"));

    $get_pos_tax    = get_config($dbc, 'pos_tax');
    $pdf_tax        = '';
	$pdf_tax_number = '';
    
    //Tax
    if ($get_pos_tax != '') {
        $pos_tax     = explode('*#*',$get_pos_tax);
        $total_count = mb_substr_count($get_pos_tax,'*#*');
        
        for ( $eq_loop=0; $eq_loop<=$total_count; $eq_loop++ ) {
            $pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

            if (strcasecmp($pos_tax_name_rate[0], 'gst') == 0) {
                $taxrate_value = $gst_total;
            }
            if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0) {
                $taxrate_value = $pst_total;
            }

            if ( $pos_tax_name_rate[3]=='Yes' && $point_of_sell['client_tax_exemption']=='Yes' ) {
            } else {
                //$pdf_tax .= $pos_tax_name_rate[0] .' : '.$pos_tax_name_rate[1].'% : $'.$taxrate_value.'<br>';
                $pdf_tax .= '
                    <tr>
                        <td style="text-align:right;" width="75.7%"><strong>'. $pos_tax_name_rate[0] . '['.$pos_tax_name_rate[1] . '%]['. $pos_tax_name_rate[2] .']</strong></td>
                        <td border="1" width="24.3%" style="text-align:right;">$'. $taxrate_value .'</td>
                    </tr>';
            }

            $pdf_tax_number .= $pos_tax_name_rate[0].' ['.$pos_tax_name_rate[2].'] <br>';

            if ( $pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes' ) {
                $client_tax_number = $pos_tax_name_rate[0].' ['.$tax_exemption_number.']';
            }
        }
    }    

    $pos_logo        = get_config($dbc, 'pos_logo');
    $invoice_footers = explode ( '*#*', get_config($dbc, 'invoice_footer') );
    foreach ( $invoice_footers as $invoice_footer ) {
        $invoice_footer_text .= $invoice_footer . '<br>';
    }

    DEFINE('POS_LOGO', $pos_logo);
    DEFINE('INVOICE_FOOTER', $invoice_footer_text);
    DEFINE('INVOICE_DATE', date('F j, Y', strtotime($point_of_sell['invoice_date'])));
    DEFINE('INVOICEID', $posid);
    DEFINE('SHIP_DATE', $point_of_sell['ship_date']);
    DEFINE('SALESPERSON', decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']));
    DEFINE('PAYMENT_TYPE', $point_of_sell['payment_type']);

    // PDF
	class MYPDF extends TCPDF {
		//Page header
		public function Header() {
            $image_file = 'download/'.POS_LOGO;
            //Image($file, $x='', $y='', $w=0, $h=0, $type='', $link='', $align='', $resize=false, $dpi=300, $palign='', $ismask=false, $imgmask=false, $border=0, $fitbox=false, $hidden=false, $fitonpage=false)
			$this->Image($image_file, 0, 10, 51, '', '', '', 'T', false, 300, 'L', false, false, 0, false, false, false);
            $this->SetFont('helvetica', '', 9);
            $header_text = '
                <table border="0">
                    <tr>
                        <td style="width:50%; padding:10px;"><br><br><br></td>
                        <td style="width:50%; text-align:right;"><h1 style="color:#999;">INVOICE</h1><br><br><br><br><p>INVOICE #: '.INVOICEID.'<br>DATE: '.INVOICE_DATE.'<br></p></td>
                    </tr>
                </table>';
            $this->writeHTMLCell(0, 0, 0 , 10, $header_text, 0, 0, false, "R", true);
		}
        
        protected $last_page_flag = false;
        
        public function Close() {
			$this->last_page_flag = true;
			parent::Close();
        }


		// Page footer
		public function Footer() {
			// Position at 35 mm from bottom /*
			$this->SetY(-35);
			$this->SetFont('helvetica', '', 9);
            if ($this->last_page_flag) {
                // ... footer for the last page ...
                $footer_text = '<br><br>' . INVOICE_FOOTER . '<br><br><center><b>Thank you for your business!</b></center>';
            }
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);
    $html = '';
    
	if ( $point_of_sell['invoice_date'] !== '' ) {
		$tdduedate = '<td>'.date('Y-m-d', strtotime($roww['invoice_date'] . "+30 days")).'</td>';
		$thduedate = '<td>Due Date</td>';
	} else {
        $tdduedate = '';
        $thduedate = '';
    }
	
    $html .= '
        <br><br><br><br><br>
        <table>
            <tr>
                <td><h3>Services provided for:</h3></td>
            </tr>
            <tr>
                <td>'. ( !empty($customer['name']) ? decryptIt($customer['name']) .' ' : '' ) . decryptIt($customer['first_name']).' '.decryptIt($customer['last_name']).'</td>
            </tr>
        </table><br><br>';

    if($client_tax_number != '') {
        $html .= '<br>Tax Exemption Number : '.$point_of_sell['tax_exemption_number'];
    }
    
	// START INVENTORY & MISC PRODUCTS
    $result         = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'inventory' AND inventoryid IS NOT NULL");
	$result2        = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND misc_product IS NOT NULL");
	$return_result  = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`returned_qty`) FROM `point_of_sell_product` WHERE `posid`='$posid'"))[0];
	$num_rows       = mysqli_num_rows($result);
	$num_rows2      = mysqli_num_rows($result2);
	
    if ( $num_rows > 0 && $num_rows2 > 0 ) {
		$title = 'Inventory & Misc Products';
	} else if ($num_rows > 0 && $num_rows2 == 0) {
		$title = 'Inventory';
	} else if($num_rows == 0 && $num_rows2 > 0) {
		$title = 'Misc Products';
	}

	if ( $num_rows > 0 || $num_rows2 > 0 ) {
		$html .= '<h4>'. $title .'</h4>
			<table border="1" cellpadding="3" style="border:1px solid #000; border-top:2px solid #000; color:#000;">
                <tr nobr="true" style="width:22%;">
                    <th width="20%"><b>Part#</b></th>
                    <th '. ( $return_result > 0 ? 'width="32%"' : 'width="44%"') .'><b>Product</b></th>
                    <th width="12%"><b>Quantity</b></th>';
                    if ( $return_result > 0 ) { $html .= '<th width="12%"><b>Returned</b></th>'; }
                    $html .= '<th width="12%"><b>Price</b></th>';
                    $html .= '<th width="12%"><b>Total</b></th>
                </tr>';

                while ( $row = mysqli_fetch_array ( $result ) ) {
                    $inventoryid	= $row['inventoryid'];
                    $price			= $row['price'];
                    $quantity		= $row['quantity'];
                    $returned		= $row['returned_qty'];

                    if ( $inventoryid != '' ) {
                        $amount = $price*($quantity-$returned);

                        $html .= '<tr>';
                            $html .= '<td>' . get_inventory ( $dbc, $inventoryid, 'part_no' ) . '</td>';
                            $html .= '<td>' . get_inventory ( $dbc, $inventoryid, 'name' ) . '</td>';
                            $html .= '<td>' . $quantity . '</td>';
                            if($return_result > 0) { $html .= '<td>'.$returned.'</td>'; }
                            $html .= '<td>$'. $price . '</td>';
                            $html .= '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
                        $html .= '</tr>';
                    }
                }

                $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND misc_product IS NOT NULL");
                while($row = mysqli_fetch_array( $result )) {
                    $misc_product = $row['misc_product'];
                    $price = $row['price'];
                    $qty = $row['quantity'];
                    $returned = $row['returned_qty'];

                    if($misc_product != '') {
                        $html .= '<tr>';
                            $html .= '<td>Not Available</td>';
                            $html .= '<td>'.$misc_product.'</td>';
                            $html .= '<td>'.$qty.'</td>';
                            if($return_result > 0) { $html .= '<td>'.$returned.'</td>'; }
                            $html .= '<td>$'.$price.'</td>';
                            $html .= '<td style="text-align:right;">$'.$price * ($qty - $returned).'</td>';
                        $html .= '</tr>';
                    }
                }
            $html .= '</table>';
    }

	// START PRODUCTS
    $result     = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'product' AND inventoryid IS NOT NULL");
	$num_rows3  = mysqli_num_rows($result);
    
    if ( $num_rows3 > 0 ) {
		if ( $num_rows > 0 || $num_rows2 > 0 ) { $html .= '<br>'; }
		$html .= '
            <h4>Product(s)</h4>
			<table border="1" cellpadding="3" style="border:1px solid #000; border-top:2px solid #000; color:#000;">
                <tr nobr="true" style="width:22%;">
                    <th width="20%"><b>Category</b></th>
                    <th '. ( $return_result > 0 ? 'width="32%"' : 'width="44%"') .'><b>Heading</b></th>
                    <th width="12%"><b>Quantity</b></th>';
                    if ( $return_result > 0 ) { $html .= '<th width="12%"><b>Returned</b></th>'; }
                    $html .= '<th width="12%"><b>Price</b></th>';
                    $html .= '<th width="12%"><b>Total</b></th>
                </tr>';
                
                while($row = mysqli_fetch_array( $result )) {
                    $inventoryid = $row['inventoryid'];
                    $price       = $row['price'];
                    $quantity    = $row['quantity'];
                    $returned    = $row['returned_qty'];

                    if($inventoryid != '') {
                        $amount = $price*($quantity-$returned);
                        $html .= '<tr>';
                            $html .= '<td>'.get_products($dbc, $inventoryid, 'category').'</td>';
                            $html .= '<td>'.get_products($dbc, $inventoryid, 'heading').'</td>';
                            $html .= '<td>'.$quantity.'</td>';
                            if($return_result > 0) { $html .= '<td>'.$returned.'</td>'; }
                            $html .= '<td>$'.$price.'</td>';
                            $html .= '<td style="text-align:right;">$'. number_format($amount,2) .'</td>';
                        $html .= '</tr>';
                    }
                }
		$html .= '</table>';
	}

	// START SERVICES
    $result     = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'service' AND inventoryid IS NOT NULL");
	$num_rows4  = mysqli_num_rows($result);
    
    if ( $num_rows4 > 0 ) {
		if ( $num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 ) { $html .= '<br>'; }
		$html .= '
            <h4>Service(s)</h4>
			<table border="1" cellpadding="3" style="border:1px solid #000; border-top:2px solid #000; color:#000;">
                <tr nobr="true">
                    <th width="20%"><b>Category</b></th>
                    <th '. ( $return_result > 0 ? 'width="32%"' : 'width="44%"') .'><b>'. ( $rookconnect=='genuine' ? 'Description' : 'Heading' ) .'</b></th>
                    <th width="12%"><b>'. ( $rookconnect=='genuine' ? 'Hours' : 'Quantity' ) .'</b></th>';
                    if ( $return_result > 0 ) { $html .= '<th width="12%"><b>Returned</b></th>'; }
                    $html .= '<th width="12%"><b>'. ( $rookconnect=='genuine' ? 'Rate' : 'Price' ) .'</b></th>';
                    $html .= '<th width="12%"><b>'. ( $rookconnect=='genuine' ? 'Amount' : 'Total' ) .'</b></th>
                </tr>';
                
                while($row = mysqli_fetch_array( $result )) {
                    $inventoryid = $row['inventoryid'];
                    $price       = $row['price'];
                    $quantity    = $row['quantity'];
                    $returned    = $row['returned_qty'];

                    if ( $inventoryid != '' ) {
                        $amount = $price*($quantity-$returned);
                        $html .= '<tr>';
                            $html .= '<td>'.get_services($dbc, $inventoryid, 'category').'</td>';
                            $html .= '<td>'.get_services($dbc, $inventoryid, 'heading').'</td>';
                            $html .= '<td>'.$quantity.'</td>';
                            if ( $return_result > 0 ) { $html .= '<td>'.$returned.'</td>'; }
                            $html .= '<td>$'.$price.'</td>';
                            $html .= '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
                        $html .= '</tr>';
                    }
                }
            $html .= '</table>';
	}

	// START VPL
    $result     = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'vpl' AND inventoryid IS NOT NULL");
	$num_rows5  = mysqli_num_rows($result);
    
    if ( $num_rows5 > 0 ) {
		if ( $num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0 ) { $html .= '<br>'; }

		$html .= '
            <h4>Vendor Price List Item(s)</h4>
			<table border="1" cellpadding="3" style="border:1px solid #000; border-top:2px solid #000; color:#000;">
                <tr nobr="true" style="width:22%;">
                    <th width="20%"><b>Part#</b></th>
                    <th '. ( $return_result > 0 ? 'width="32%"' : 'width="44%"') .'><b>Product</b></th>
                    <th width="12%"><b>Quantity</b></th>';
                    if ( $return_result > 0 ) { $html .= '<th width="12%"><b>Returned</b></th>'; }
                    $html .= '<th width="12%"><b>Price</b></th>';
                    $html .= '<th width="12%"><b>Total</b></th>
                </tr>';

                while ( $row=mysqli_fetch_array($result) ) {
                    $inventoryid = $row['inventoryid'];
                    $price       = $row['price'];
                    $quantity    = $row['quantity'];
                    $returned    = $row['returned_qty'];

                    if ( $inventoryid != '' ) {
                        $amount = $price*($quantity-$returned);
                        $html .= '<tr>';
                            $html .= '<td>'.get_vpl($dbc, $inventoryid, 'part_no').'</td>';
                            $html .= '<td>'.get_vpl($dbc, $inventoryid, 'name').'</td>';
                            $html .= '<td>'.$quantity.'</td>';
                            if ( $return_result > 0 ) { $html .= '<td>'.$returned.'</td>'; }
                            $html .= '<td>$'.$price.'</td>';
                            $html .= '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
                        $html .= '</tr>';
                    }
                }
            $html .= '</table>';
	}

    $html .= '
            <br>
            <table border="0" cellpadding="2">';
                if ( !empty($couponid) || $coupon_value!=0 ) {
                    $html .= '
                        <tr>
                            <td style="text-align:right;" width="76%"><strong>Coupon Value</strong></td>
                            <td border="1" width="24.3%" style="text-align:right;">$'. $point_of_sell['coupon_value'] .'</td>
                        </tr>';
                }
                
                if ( $point_of_sell['discount_value'] != 0 ) {
                    $html .= '
                        <tr>
                            <td style="text-align:right;" width="75.7%"><strong>Total Before Discount</strong></td>
                            <td border="1" width="24.3%" style="text-align:right;">$'. $point_of_sell['sub_total'] .'</td>
                        </tr>
                        <tr>
                            <td style="text-align:right;" width="75.7%"><strong>Discount Value</strong></td>
                            <td border="1" width="24.3%" style="text-align:right;">'. $d_value .'</td>
                        </tr>
                        <tr>
                            <td style="text-align:right;" width="75.7%"><strong>Total After Discount</strong></td>
                            <td border="1" width="24.3%" style="text-align:right;">$'. $point_of_sell['total_after_discount'] .'</td>
                        </tr>';
                } else {
                    $html .= '
                        <tr>
                            <td style="text-align:right;" width="75.7%"><strong>Sub Total</strong></td>
                            <td border="1" width="24.3%" style="text-align:right;">$'. $point_of_sell['sub_total'] .'</td>
                        </tr>';
                }
                
                if ( $point_of_sell['delivery'] != 0 ) {
                    $html .= '
                        <tr>
                            <td style="text-align:right;" width="75.7%"><strong>Delivery</strong></td>
                            <td border="1" width="24.3%" style="text-align:right;">$'. $point_of_sell['delivery'] .'</td>
                        </tr>';
                }

                if ( $point_of_sell['assembly'] != 0 ) {
                    $html .= '
                        <tr>
                            <td style="text-align:right;" width="75.7%"><strong>Assembly</strong></td>
                            <td border="1" width="24.3%" style="text-align:right;">$'. $point_of_sell['assembly'] .'</td>
                        </tr>';
                }

                if ( $pdf_tax != '' ) {
                    $html .= $pdf_tax;
                    //$html .= '<tr><td style="text-align:right;" width="75%"><strong>Tax</strong></td><td border="1" width="25%" style="text-align:right;">'.$pdf_tax.'</td></tr>';
                }
                
                if ( $point_of_sell['returned_amt'] != 0 ) {
                    $html .= '
                        <tr>
                            <td style="text-align:right;" width="75.7%"><strong>Returned Total (Including Tax)</strong></td>
                            <td border="1" width="24.3%" style="text-align:right;">$'. $point_of_sell['returned_amt'] .'</td>
                        </tr>';
                }

                $html .= '
                    <tr>
                        <td style="text-align:right;" width="75.7%"><strong>Total</strong></td>
                        <td border="1" width="24.3%" style="text-align:right;">$'. ($point_of_sell['total_price'] - $point_of_sell['returned_amt']) .'</td>
                    </tr>';
                
                if ( $point_of_sell['deposit_paid'] > 0 ) {
                    $html .='
                        <tr>
                            <td style="text-align:right;" width="75.7%"><strong>Deposit Paid</strong></td>
                            <td border="1" width="24.3%" style="text-align:right;">$'. $point_of_sell['deposit_paid'] .'</td>
                        </tr>
                        <tr>
                            <td style="text-align:right;" width="75.7%"><strong>Updated Total</strong></td>
                            <td border="1" width="24.3%" style="text-align:right;">$'. $point_of_sell['updatedtotal'] .'</td>
                        </tr>';
                }

            $html .= '</table><br><br><br>';
            
    if ( !empty($comment) ) { $html .= '<h4>Comments:</h4>' . $comment . '<br>'; }

	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}

	$pdf->writeHTML($html, true, false, true, false, '');
	$pdf->Output('../Point of Sale/download/invoice_'.$posid.$edited.'.pdf', 'F');
}


function create_pos3_pdf($dbc,$posid,$d_value,$comment, $gst_total, $pst_total, $rookconnect, $edit_id, $company_software_name) {
	include ('../database_connection.php');
    $point_of_sell = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM point_of_sell WHERE posid='$posid'"));
    $contactid		= $point_of_sell['contactid'];
	$couponid		= $point_of_sell['couponid'];
	$coupon_value	= $point_of_sell['coupon_value'];
	$dep_total		= $point_of_sell['deposit_total'];
	$updatedtotal	= $point_of_sell['updatedtotal'];
    $customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid='$contactid'"));

	if ( $edit_id == '0' ) {
		$edited = '';
	} else {
		$edited = '_' . $edit_id;
	}

    //Tax
    $point_of_sell_product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(gst) AS total_gst, SUM(pst) AS total_pst FROM point_of_sell_product WHERE posid='$posid'"));

    $get_pos_tax = get_config($dbc, 'pos_tax');
    $pdf_tax = '';
	$pdf_tax_number = '';
    if($get_pos_tax != '') {
        $pos_tax = explode('*#*',$get_pos_tax);

        $total_count = mb_substr_count($get_pos_tax,'*#*');
        for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
            $pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

            if (strcasecmp($pos_tax_name_rate[0], 'gst') == 0) {
                $taxrate_value = $gst_total;
            }
            if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0) {
                $taxrate_value = $pst_total;
            }

            if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {

            } else {
                //$pdf_tax .= $pos_tax_name_rate[0] .' : '.$pos_tax_name_rate[1].'% : $'.$taxrate_value.'<br>';
                $pdf_tax .= '<tr><td style="text-align:right;" width="75%"><strong>'.$pos_tax_name_rate[0] .'['.$pos_tax_name_rate[1].'%]['.$pos_tax_name_rate[2].']</strong></td><td border="1" width="25%" style="text-align:right;background-color: rgb(232,238,238);">$'.$taxrate_value.'</td></tr>';
            }

            $pdf_tax_number .= $pos_tax_name_rate[0].' ['.$pos_tax_name_rate[2].'] <br>';

            if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {
                $client_tax_number = $pos_tax_name_rate[0].' ['.$tax_exemption_number.']';
            }
        }
    }
    //Tax

    $pos_logo = get_config($dbc, 'pos_logo');
    $invoice_footer = get_config($dbc, 'invoice_footer');

    DEFINE('POS_LOGO', $pos_logo);
    DEFINE('INVOICE_FOOTER', $invoice_footer);
    DEFINE('INVOICE_DATE', $point_of_sell['invoice_date']);
    DEFINE('INVOICEID', $posid);
	DEFINE('COMPANY_SOFTWARE_NAME', $company_software_name);
    DEFINE('SHIP_DATE', $point_of_sell['ship_date']);
    DEFINE('SALESPERSON', decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']));
    DEFINE('PAYMENT_TYPE', $point_of_sell['payment_type']);

    // PDF
	class MYPDF extends TCPDF {
		//Page header
		public function Header() {
            $image_file = 'download/'.POS_LOGO;
			$this->Image($image_file, 0, 3, 51, '', '', '', 'T', false, 300, 'L', false, false, 0, false, false, false);

            $this->SetFont('helvetica', '', 9);

				//$footer_text = '<p style="text-align:right;">Date : ' .INVOICE_DATE.'<br>Invoice# : '.INVOICEID.'<br>Ship Date : ' .SHIP_DATE.'<br>Sales Person : ' .SALESPERSON.'<br>Payment Type : ' .PAYMENT_TYPE.'<br>Shipping Method : '.$point_of_sell['delivery_type'].'</p>';
				$footer_text = '<table border="0"><tr><td style="width:50%;padding:10px;"><br><br><br><br><br></td><td  style="width:50%;"><p style="text-align:right;"><h1><em>Invoice</em></h1>Date: '.INVOICE_DATE.'<br>Invoice #: '.INVOICEID.'<br></p></td></tr></table>';

            $this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);
		}


		  protected $last_page_flag = false;

		  public function Close() {
			$this->last_page_flag = true;
			parent::Close();
		  }


		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom /* CHANGED (SetY used to be -25) */
			$this->SetY(-27);
			// Set font
			$this->SetFont('helvetica', 'I', 8);
			// Page number
			    if ($this->last_page_flag) {
				  // ... footer for the last page ...
				  //<table width="400px" style="border-bottom:1px solid black;text-align:left;font-style: normal !important;font-size:9"><tr><td style="text-align:left;font-style: normal !important;font-size:9">
	    //Signature</td></tr></table>
				  $footer_text = '<br><br><center><p style="text-align:center;">Transfer Funds to '.COMPANY_SOFTWARE_NAME.'<br>Thank you for your business!</p></center><br>'.INVOICE_FOOTER;
				} else {
				  // ... footer for the normal page ...
				  $footer_text = INVOICE_FOOTER;
				}

			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);
	//$pdf->AddPage();
    $html = '';

	//$html .= '<p style="text-align:left;">Box 2052, Sundre, AB, T0M 1X0<br>Phone: 403-638-4030<br>Fax: 403-638-4001<br>Email: info@highlandprojects.com<br>Work Ticket# : </p>';
	if($point_of_sell['invoice_date'] !== '') {
		$tdduedate = '<td>'.date('Y-m-d', strtotime($roww['invoice_date'] . "+30 days")).'</td>';
		$thduedate = '<td>Due Date</td>';
	} else { $tdduedate = ''; $thduedate = ''; }
	$html .= '<p style="text-align:center"><h2>'.COMPANY_SOFTWARE_NAME.'</h2></p><br><br><br><br>
	<table><tr><td style="width:20%">To:</td><td style="width:30%">'.decryptIt($customer['name']).' '.decryptIt($customer['first_name']).' '.decryptIt($customer['last_name']).'<br>'.(!empty($customer['mailing_address']) ? $customer['mailing_address'] : $customer['address']).'<br>'.$customer['city'].', '.$customer['state'].' '.$customer['zip_code'].'<br>'.decryptIt($customer['cell_phone']).'<br>'.decryptIt($customer['email_address']).'</td><td style="width:20%">Ship to:</td><td style="width:30%">'.decryptIt($customer['name']).' '.decryptIt($customer['first_name']).' '.decryptIt($customer['last_name']).'<br>'.(!empty($customer['mailing_address']) ? $customer['mailing_address'] : $customer['address']).'<br>'.$customer['city'].', '.$customer['state'].' '.$customer['zip_code'].'<br>'.decryptIt($customer['cell_phone']).'<br>'.decryptIt($customer['email_address']).'</td></tr></table><br><br>';
    
    if ( !empty($customer['referred_by']) ) {
        $html .= '<table><tr><td style="width:20%">Reference:</td><td style="width:80%">'.$customer['referred_by'].'</td></tr></table><br><br>';
    }
    
    $html .= '<table border="1px" style="padding:3px; border:1px solid grey;">
            <tr nobr="true" style="background-color:rgb(140,173,174); color:black; "><td>Salesperson</td><td>Delivery Option</td><td>Ship Date</td><td>Payment Type</td>'.$thduedate.'</tr>
	<tr><td>'.SALESPERSON.'</td><td>'.$point_of_sell['delivery_type'].'</td><td>'.SHIP_DATE.'</td><td>'.PAYMENT_TYPE.'</td>'.$tdduedate.'</tr>
	</table><br><br>
	';

    if($client_tax_number != '') {
        $html .= '<br>Tax Exemption Number : '.$point_of_sell['tax_exemption_number'];
    }
    $html .= '</p>';
	// START INVENTORY & MISC PRODUCTS
    $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'inventory' AND inventoryid IS NOT NULL");
	$result2 = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND misc_product IS NOT NULL");
	$return_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`returned_qty`) FROM `point_of_sell_product` WHERE `posid`='$posid'"))[0];
	$num_rows = mysqli_num_rows($result);
	$num_rows2 = mysqli_num_rows($result2);
	if($num_rows > 0 && $num_rows2 > 0) {
		$titler = 'Inventory & Misc Products';
	} else if ($num_rows > 0 && $num_rows2 == 0) {
		$titler = 'Inventory';
	} else if($num_rows == 0 && $num_rows2 > 0) {
		$titler = 'Misc Products';
	}

	if($num_rows > 0 || $num_rows2 > 0) {
		$html .= '<h2>'.$titler.'</h2>
			<table border="1px" style="padding:3px; border:1px solid grey;">
            <tr nobr="true" style="background-color:rgb(140,173,174); color:black;  width:22%;">';
            
        $html .= '<th>Part#</th>';

		$html .= '<th>Product</th><th>Quantity</th>';
		if($return_result > 0) {
			$html .= '<th>Returned</th>';
		}
		$html .= '<th>Price</th><th>Total</th></tr>';

		while ( $row = mysqli_fetch_array ( $result ) ) {
			$inventoryid	= $row['inventoryid'];
			$price			= $row['price'];
			$quantity		= $row['quantity'];
			$returned		= $row['returned_qty'];

			if ( $inventoryid != '' ) {
				$amount = $price*($quantity-$returned);

				$html .= '<tr>';
					$html .= '<td>' . get_inventory ( $dbc, $inventoryid, 'part_no' ) . '</td>';
					$html .= '<td>' . get_inventory ( $dbc, $inventoryid, 'name' ) . '</td>';
					$html .= '<td>' . $quantity . '</td>';
					if($return_result > 0) {
						$html .= '<td>'.$returned.'</td>';
					}
					$html .= '<td>$'. $price . '</td>';
					$html .= '<td style="text-align:right; background-color:rgb(232,238,238);">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}

		$result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND misc_product IS NOT NULL");
		while($row = mysqli_fetch_array( $result )) {
			$misc_product = $row['misc_product'];
			$price = $row['price'];
			$qty = $row['quantity'];
			$returned = $row['returned_qty'];

			if($misc_product != '') {
				$html .= '<tr>';
				$html .=  '<td>Not Available</td>';
				$html .=  '<td>'.$misc_product.'</td>';
				$html .=  '<td>'.$qty.'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .=  '<td>$'.$price.'</td>';
				$html .=  '<td style="text-align:right; background-color:rgb(232,238,238);">$'.$price * ($qty - $returned).'</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
	}
	// END INVENTORY AND MISC PRODUCTS

	// START PRODUCTS
    $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'product' AND inventoryid IS NOT NULL");
	$num_rows3 = mysqli_num_rows($result);
    if($num_rows3 > 0) {
		if($num_rows > 0 || $num_rows2 > 0) { $html .= '<br>'; }
		$html .= '<h2>Product(s)</h2>
			<table border="1px" style="padding:3px; border:1px solid grey;">
            <tr nobr="true" style="background-color:rgb(140,173,174); color:black;  width:22%;">
            <th>Category</th><th>Heading</th><th>Quantity</th>';
			if($return_result > 0) {
				$html .= '<th>Returned</th>';
			}
			$html .= '<th>Price</th><th>Total</th></tr>';
		while($row = mysqli_fetch_array( $result )) {
			$inventoryid = $row['inventoryid'];
			$price = $row['price'];
			$quantity = $row['quantity'];
			$returned = $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);
				$html .= '<tr>';
				$html .=  '<td>'.get_products($dbc, $inventoryid, 'category').'</td>';
				$html .=  '<td>'.get_products($dbc, $inventoryid, 'heading').'</td>';
				$html .=  '<td>'.$quantity.'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .=  '<td>$'.$price.'</td>';
				$html .=  '<td style="text-align:right; background-color:rgb(232,238,238);">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
	}
	// END PRODUCTS

	// START SERVICES
    $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'service' AND inventoryid IS NOT NULL");
	$num_rows4 = mysqli_num_rows($result);
    if($num_rows4 > 0) {
		if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0) { $html .= '<br>'; }
		$html .= '<h2>Service(s)</h2>
			<table border="1px" style="padding:3px; border:1px solid grey;">
            <tr nobr="true" style="background-color:rgb(140,173,174); color:black;  width:22%;">
            <th>Category</th><th>Heading</th><th>Quantity</th>';
			if($return_result > 0) {
				$html .= '<th>Returned</th>';
			}
			$html .= '<th>Price</th><th>Total</th></tr>';
		while($row = mysqli_fetch_array( $result )) {
			$inventoryid = $row['inventoryid'];
			$price = $row['price'];
			$quantity = $row['quantity'];
			$returned = $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);
				$html .= '<tr>';
				$html .=  '<td>'.get_services($dbc, $inventoryid, 'category').'</td>';
				$html .=  '<td>'.get_services($dbc, $inventoryid, 'heading').'</td>';
				$html .=  '<td>'.$quantity.'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .=  '<td>$'.$price.'</td>';
				$html .=  '<td style="text-align:right; background-color:rgb(232,238,238);">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
	}
	// END SERVICES

	// START VPL
    $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'vpl' AND inventoryid IS NOT NULL");
	$num_rows5 = mysqli_num_rows($result);
    if($num_rows5 > 0) {
		if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0) { $html .= '<br>'; }

		$html .= '<h2>Vendor Price List Item(s)</h2>
			<table border="1px" style="padding:3px; border:1px solid grey;">
            <tr nobr="true" style="background-color:rgb(140,173,174); color:black;  width:22%;">';

		$html .= '<th>Part#</th>';

		$html .= '<th>Product</th><th>Quantity</th>';
		if($return_result > 0) {
			$html .= '<th>Returned</th>';
		}
		$html .= '<th>Price</th><th>Total</th></tr>';

		while($row = mysqli_fetch_array( $result )) {
			$inventoryid = $row['inventoryid'];
			$price = $row['price'];
			$quantity = $row['quantity'];
			$returned = $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);

				$html .= '<tr>';
				$html .=  '<td>'.get_vpl($dbc, $inventoryid, 'part_no').'</td>';
				$html .=  '<td>'.get_vpl($dbc, $inventoryid, 'name').'</td>';
				$html .=  '<td>'.$quantity.'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .=  '<td>$'.$price.'</td>';
				$html .=  '<td style="text-align:right; background-color:rgb(232,238,238);">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
	}
	// END VPL

    $html .= '
            <br>
            <table border="0" cellpadding="2" >';
			if ( !empty($couponid) || $coupon_value!=0 ) {
				$html .= '<tr><td style="text-align:right;" width="75%"><strong>Coupon Value</strong></td><td border="1" width="25%" style="text-align:right;background-color: rgb(232,238,238);">$'.$point_of_sell['coupon_value'].'</td></tr>';
			}
            if($point_of_sell['discount_value'] != 0) {
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Total Before Discount</strong></td><td border="1" width="25%" style="text-align:right;background-color: rgb(232,238,238);">$'.$point_of_sell['sub_total'].'</td></tr>';
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Discount Value</strong></td><td border="1" width="25%" style="text-align:right;background-color: rgb(232,238,238);">'.$d_value.'</td></tr>';
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Total After Discount</strong></td><td border="1" width="25%" style="text-align:right;background-color: rgb(232,238,238);">$'.$point_of_sell['total_after_discount'].'</td></tr>';
            } else {
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Sub Total</strong></td><td border="1" width="25%" style="text-align:right;background-color: rgb(232,238,238);">$'.$point_of_sell['sub_total'].'</td></tr>';

            }
            if($point_of_sell['delivery'] != 0) {
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Delivery</strong></td><td border="1" width="25%" style="text-align:right;background-color: rgb(232,238,238);">$'.$point_of_sell['delivery'].'</td></tr>';
            }

            if($point_of_sell['assembly'] != 0) {
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Assembly</strong></td><td border="1" width="25%" style="text-align:right;background-color: rgb(232,238,238);">$'.$point_of_sell['assembly'].'</td></tr>';
            }

            if($pdf_tax != '') {
                $html .= $pdf_tax;
                //$html .= '<tr><td style="text-align:right;" width="75%"><strong>Tax</strong></td><td border="1" width="25%" style="text-align:right;">'.$pdf_tax.'</td></tr>';
            }
            if($point_of_sell['returned_amt'] != 0) {
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Returned Total (Including Tax)</strong></td><td border="1" width="25%" style="text-align:right;background-color: rgb(232,238,238);">$'.$point_of_sell['returned_amt'].'</td></tr>';
            }

            $html .= '<tr><td style="text-align:right;" width="75%"><strong>Total</strong></td><td border="1" width="25%" style="text-align:right;background-color: rgb(232,238,238);">$'.number_format(($point_of_sell['total_price'] - $point_of_sell['returned_amt']),2).'</td></tr>';
			if($point_of_sell['deposit_paid'] > 0) {
				$html .='<tr><td style="text-align:right;" width="75%"><strong>Deposit Paid</strong></td><td border="1" width="25%" style="text-align:right;background-color: rgb(232,238,238);">$'.$point_of_sell['deposit_paid'].'</td></tr>';
				$html .='<tr><td style="text-align:right;" width="75%"><strong>Updated Total</strong></td><td border="1" width="25%" style="text-align:right;background-color: rgb(232,238,238);">$'.$point_of_sell['updatedtotal'].'</td></tr>';
			}

            $html .= '</table><br><br>';


    $html .= '<br />';

    $html .= $comment.'<br>';

	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}

	$pdf->writeHTML($html, true, false, true, false, '');
	?><?php
	$pdf->Output('../Point of Sale/download/invoice_'.$posid.$edited.'.pdf', 'F');

}


function create_pos2_pdf($dbc,$posid,$d_value,$comment, $gst_total, $pst_total, $rookconnect, $edit_id) {
	include ('../database_connection.php');
    $point_of_sell = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM point_of_sell WHERE posid='$posid'"));
    $contactid		= $point_of_sell['contactid'];
	$couponid		= $point_of_sell['couponid'];
	$coupon_value	= $point_of_sell['coupon_value'];

	if ( $edit_id == '0' ) {
		$edited = '';
	} else {
		$edited = '_' . $edit_id;
	}

    $customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT name, first_name, last_name, home_phone, cell_phone, email_address, business_address, city, state, country, zip_code FROM contacts WHERE contactid='$contactid'"));

    //Tax
    $point_of_sell_product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(gst) AS total_gst, SUM(pst) AS total_pst FROM point_of_sell_product WHERE posid='$posid'"));

    $get_pos_tax = get_config($dbc, 'pos_tax');
    $pdf_tax = '';
    if($get_pos_tax != '') {
        $pos_tax = explode('*#*',$get_pos_tax);

        $total_count = mb_substr_count($get_pos_tax,'*#*');
        for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
            $pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

            if (strcasecmp($pos_tax_name_rate[0], 'gst') == 0) {
                $taxrate_value = $gst_total;
            }
            if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0) {
                $taxrate_value = $pst_total;
            }

            if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {

            } else {
                //$pdf_tax .= $pos_tax_name_rate[0] .' : '.$pos_tax_name_rate[1].'% : $'.$taxrate_value.'<br>';
                $pdf_tax .= '<tr><td style="text-align:right;" width="75%"><strong>'.$pos_tax_name_rate[0] .'['.$pos_tax_name_rate[1].'%]['.$pos_tax_name_rate[2].']</strong></td><td border="1" width="25%" style="text-align:right;">$'.$taxrate_value.'</td></tr>';
            }

            $pdf_tax_number .= $pos_tax_name_rate[0].' ['.$pos_tax_name_rate[2].'] <br>';

            if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {
                $client_tax_number = $pos_tax_name_rate[0].' ['.$tax_exemption_number.']';
            }
        }
    }
    //Tax

    $pos_logo = get_config($dbc, 'pos_logo');
    $invoice_footer = get_config($dbc, 'invoice_footer');

    DEFINE('POS_LOGO', $pos_logo);
    DEFINE('INVOICE_FOOTER', $invoice_footer);
    DEFINE('INVOICE_DATE', $point_of_sell['invoice_date']);
    DEFINE('INVOICEID', $posid);
	DEFINE('DUEDATE', date('Y-m-d', strtotime($roww['invoice_date'] . "+30 days")));
    DEFINE('SHIP_DATE', $point_of_sell['ship_date']);
    DEFINE('SALESPERSON', decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']));
    DEFINE('PAYMENT_TYPE', $point_of_sell['payment_type']);

	// Hide Sales Person from Washtech
	if ( $rookconnect !== 'washtech' ) {
		$sales_person = '<br>Sales Person : ' . SALESPERSON;
	} else {
		$sales_person = '';
	}

    // PDF
	class MYPDF extends TCPDF {
		//Page header
		public function Header() {
            $image_file = 'download/'.POS_LOGO;
			$this->Image($image_file, 0, 3, 51, '', '', '', 'T', false, 300, 'L', false, false, 0, false, false, false);

            $this->SetFont('helvetica', '', 9);
				if(DUEDATE !== '') {
					$tdduedate = '<br>Due Date : '.DUEDATE.'<br>';
				} else { $tdduedate = '';  }
			if($point_of_sell['delivery_type'] !== '' && $point_of_sell['delivery_type'] !== NULL) {
				$footer_text = '<p style="text-align:right;">Date : ' .INVOICE_DATE.'<br>Invoice# : '.INVOICEID.'<br>Ship Date : ' . SHIP_DATE . $sales_person . '<br>Payment Type : ' .PAYMENT_TYPE.'<br>Shipping Method : '.$point_of_sell['delivery_type'].$tdduedate.'</p>';
			} else {
				$footer_text = '<p style="text-align:right;">Date : ' .INVOICE_DATE.'<br>Invoice# : '.INVOICEID.'<br>Ship Date : ' .SHIP_DATE . $sales_person . '<br>Payment Type : ' .PAYMENT_TYPE.$tdduedate.'</p>';
			}
            $this->writeHTMLCell(0, 0, 0 , 10, $footer_text, 0, 0, false, "R", true);
		}


		  protected $last_page_flag = false;

		  public function Close() {
			$this->last_page_flag = true;
			parent::Close();
		  }


		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom /* CHANGED (SetY used to be -25) */
			$this->SetY(-25);
			// Set font
			$this->SetFont('helvetica', 'I', 8);
			// Page number
			    if ($this->last_page_flag) {
				  // ... footer for the last page ...
				  $footer_text = '<table width="400px" style="border-bottom:1px solid black;text-align:left;font-style: normal !important;font-size:9"><tr><td style="text-align:left;font-style: normal !important;font-size:9">
	    Signature</td></tr></table><br><br><br>'.INVOICE_FOOTER;
				} else {
				  // ... footer for the normal page ...
				  $footer_text = INVOICE_FOOTER;
				}

			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
	$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);
	//$pdf->AddPage();
    $html = '';

	//$html .= '<p style="text-align:left;">Box 2052, Sundre, AB, T0M 1X0<br>Phone: 403-638-4030<br>Fax: 403-638-4001<br>Email: info@highlandprojects.com<br>Work Ticket# : </p>';

	$html .= '<br><br><br><br><br><p style="text-align:left;">'.decryptIt($customer['name']).' '.decryptIt($customer['first_name']).' '.decryptIt($customer['last_name']).'<br>'.$customer['business_address'].'<br>'.$customer['city'].', '.$customer['state'].' '.$customer['zip_code'].'<br>'.decryptIt($customer['cell_phone']).'<br>'.decryptIt($customer['email_address']).'<br>';

    if($client_tax_number != '') {
        $html .= '<br>Tax Exemption Number : '.$point_of_sell['tax_exemption_number'];
    }
    $html .= '</p>';
	// START INVENTORY & MISC PRODUCTS
    $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'inventory' AND inventoryid IS NOT NULL");
	$result2 = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'misc product'");
	$return_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`returned_qty`) FROM `point_of_sell_product` WHERE `posid`='$posid'"))[0];
	$num_rows = mysqli_num_rows($result);
	$num_rows2 = mysqli_num_rows($result2);
	if($num_rows > 0 && $num_rows2 > 0) {
		$titler = 'Inventory & Misc Products';
	} else if ($num_rows > 0 && $num_rows2 == 0) {
		$titler = 'Inventory';
	} else if($num_rows == 0 && $num_rows2 > 0) {
		$titler = 'Misc Products';
	}
    if($num_rows > 0 || $num_rows2 > 0) {
		$html .= '<h2>'.$titler.'</h2>
			<table border="1px" style="padding:3px; border:1px solid black;">
            <tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">';

		// Don't display Part# for SEA
		if ( $rookconnect !== 'sea' ) {
			$html .= '<th>Part#</th>';
		}

		$html .= '<th>Product</th><th>Quantity</th>';
		if($return_result > 0) {
			$html .= '<th>Returned</th>';
		}
		$html .= '<th>Price</th><th>Total</th></tr>';

		while($row = mysqli_fetch_array( $result )) {
			$inventoryid = $row['inventoryid'];
			$price = $row['price'];
			$quantity = $row['quantity'];
			$returned		= $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);

				$html .= '<tr>';

					// Don't display Part# for SEA
					if ( $rookconnect !== 'sea' ) {
						$html .= '<td>' . get_inventory ( $dbc, $inventoryid, 'part_no' ) . '</td>';
					}

					$html .= '<td>'.get_inventory($dbc, $inventoryid, 'name').'</td>';
					$html .= '<td>'.$quantity.'</td>';
					if($return_result > 0) {
						$html .= '<td>'.$returned.'</td>';
					}
					$html .= '<td>$'.$price.'</td>';
					$html .= '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}

		$result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'misc product'");
		while($row = mysqli_fetch_array( $result )) {
			$misc_product	= $row['misc_product'];
			$price			= $row['price'];
			$quantity		= $row['quantity'];
			$returned		= $row['returned_qty'];
			$amount			= $price*($quantity-$returned);

			if($misc_product != '') {
				$html .= '<tr>';
				$html .=  '<td>Not Available</td>';
				$html .=  '<td>'.$misc_product.'</td>';
				$html .=  '<td>' . $quantity . '</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .=  '<td>$'.$price.'</td>';
				$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
	}
	// END INVENTORY AND MISC PRODUCTS

	// START PRODUCTS
    $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'product' AND inventoryid IS NOT NULL");
	$num_rows3 = mysqli_num_rows($result);
    if($num_rows3 > 0) {
		if($num_rows > 0 || $num_rows2 > 0) { $html .= '<br>'; }
		$html .= '<h2>Product(s)</h2>
			<table border="1px" style="padding:3px; border:1px solid black;">
            <tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">
            <th>Category</th><th>Heading</th><th>Quantity</th>';
			if($return_result > 0) {
				$html .= '<th>Returned</th>';
			}
			$html .= '<th>Price</th><th>Total</th></tr>';
		while($row = mysqli_fetch_array( $result )) {
			$inventoryid = $row['inventoryid'];
			$price = $row['price'];
			$quantity = $row['quantity'];
			$returned		= $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);
				$html .= '<tr>';
				$html .=  '<td>'.get_products($dbc, $inventoryid, 'category').'</td>';
				$html .=  '<td>'.get_products($dbc, $inventoryid, 'heading').'</td>';
				$html .=  '<td>'.$quantity.'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .=  '<td>$'.$price.'</td>';
				$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
	}
	// END PRODUCTS

	// START SERVICES
    $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'service' AND inventoryid IS NOT NULL");
	$num_rows4 = mysqli_num_rows($result);
    if($num_rows4 > 0) {
		if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0) { $html .= '<br>'; }
		$html .= '<h2>Service(s)</h2>
			<table border="1px" style="padding:3px; border:1px solid black;">
            <tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">
            <th>Category</th><th>Heading</th><th>Quantity</th>';
			if($return_result > 0) {
				$html .= '<th>Returned</th>';
			}
			$html .= '<th>Price</th><th>Total</th></tr>';
		while($row = mysqli_fetch_array( $result )) {
			$inventoryid = $row['inventoryid'];
			$price = $row['price'];
			$quantity = $row['quantity'];
			$returned		= $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);
				$html .= '<tr>';
				$html .=  '<td>'.get_services($dbc, $inventoryid, 'category').'</td>';
				$html .=  '<td>'.get_services($dbc, $inventoryid, 'heading').'</td>';
				$html .=  '<td>'.$quantity.'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .=  '<td>$'.$price.'</td>';
				$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
	}
	// END SERVICES
	// START VPL
    $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'vpl' AND inventoryid IS NOT NULL");
	$num_rows5 = mysqli_num_rows($result);
    if($num_rows5 > 0) {
		if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0) { $html .= '<br>'; }

		$html .= '<h2>Vendor Price List Item(s)</h2>
			<table border="1px" style="padding:3px; border:1px solid grey;">
            <tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">';

		// Don't display Part# for SEA
		if ( $rookconnect !== 'sea' ) {
			$html .= '<th>Part#</th>';
		}

		$html .= '<th>Product</th><th>Quantity</th>';
		if($return_result > 0) {
			$html .= '<th>Returned</th>';
		}
		$html .= '<th>Price</th><th>Total</th></tr>';

		while($row = mysqli_fetch_array( $result )) {
			$inventoryid = $row['inventoryid'];
			$price = $row['price'];
			$quantity = $row['quantity'];
			$returned		= $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);

				$html .= '<tr>';
				$html .=  '<td>'.get_vpl($dbc, $inventoryid, 'part_no').'</td>';
				$html .=  '<td>'.get_vpl($dbc, $inventoryid, 'name').'</td>';
				$html .=  '<td>'.$quantity.'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .=  '<td>$'.$price.'</td>';
				$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
	}
	// END VPL

    $html .= '
            <br>
            <table border="0" cellpadding="2">';
			if ( !empty($couponid) || $coupon_value!=0 ) {
				$html .= '<tr><td style="text-align:right;" width="75%"><strong>Coupon Value</strong></td><td border="1" width="25%" style="text-align:right;">$'.$point_of_sell['coupon_value'].'</td></tr>';
			}
            if($point_of_sell['discount_value'] != 0) {
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Total Before Discount</strong></td><td border="1" width="25%" style="text-align:right;">$'.$point_of_sell['sub_total'].'</td></tr>';
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Discount Value</strong></td><td border="1" width="25%" style="text-align:right;">'.$d_value.'</td></tr>';
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Total After Discount</strong></td><td border="1" width="25%" style="text-align:right;">$'.$point_of_sell['total_after_discount'].'</td></tr>';
            } else {
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Sub Total</strong></td><td border="1" width="25%" style="text-align:right;">$'.$point_of_sell['sub_total'].'</td></tr>';

            }
            if($point_of_sell['delivery'] != 0) {
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Delivery</strong></td><td border="1" width="25%" style="text-align:right;">$'.$point_of_sell['delivery'].'</td></tr>';
            }

            if($point_of_sell['assembly'] != 0) {
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Assembly</strong></td><td border="1" width="25%" style="text-align:right;">$'.$point_of_sell['assembly'].'</td></tr>';
            }

            if($pdf_tax != '') {
                $html .= $pdf_tax;
                //$html .= '<tr><td style="text-align:right;" width="75%"><strong>Tax</strong></td><td border="1" width="25%" style="text-align:right;">'.$pdf_tax.'</td></tr>';
            }

            if($point_of_sell['returned_amt'] != 0) {
                $html .= '<tr><td style="text-align:right;" width="75%"><strong>Returned Total (Including Tax)</strong></td><td border="1" width="25%" style="text-align:right;">$'.$point_of_sell['returned_amt'].'</td></tr>';
            }

            $html .= '<tr><td style="text-align:right;" width="75%"><strong>Total</strong></td><td border="1" width="25%" style="text-align:right;">$'.$point_of_sell['total_price'] - $point_of_sell['returned_amt'].'</td></tr>';
			if($point_of_sell['deposit_paid'] > 0) {
				$html .='<tr><td style="text-align:right;" width="75%"><strong>Deposit Paid</strong></td><td border="1" width="25%" style="text-align:right;">$'.$point_of_sell['deposit_paid'].'</td></tr>';
				$html .='<tr><td style="text-align:right;" width="75%"><strong>Updated Total</strong></td><td border="1" width="25%" style="text-align:right;">$'.$point_of_sell['updatedtotal'].'</td></tr>';
			}


            $html .= '</table><br><br>';


    $html .= '<br />';

    $html .= $comment.'<br>';

    //$html .= 'Payment Method : '.$point_of_sell['payment_type'].'<br>';


    if($point_of_sell['delivery_type'] == 'Pick-Up' || $point_of_sell['delivery_type'] == 'Company Delivery') {
        //$html .= '<br><br><br><br><br><br><br><br><br><br><br><br><br>';
	    //$html .= '<table width="400px" style="border-bottom:1px solid black;"><tr><td>
	    //Signature</td></tr></table></div>
	    //';
    }

	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}

	$pdf->writeHTML($html, true, false, true, false, '');
	?><?php
	$pdf->Output('../Point of Sale/download/invoice_'.$posid.$edited.'.pdf', 'F');

}


function create_pos1_pdf($dbc,$posid,$d_value,$comment, $gst_total, $pst_total, $rookconnect, $edit_id) {
	include ('../database_connection.php');
    $point_of_sell = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM point_of_sell WHERE posid='$posid'"));    
	$contactid		= $point_of_sell['contactid'];
	$couponid		= $point_of_sell['couponid'];
	$coupon_value	= $point_of_sell['coupon_value'];

	if ( $edit_id == '0' ) {
		$edited = '';
	} else {
		$edited = '_' . $edit_id;
	}

    $customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT name, first_name, last_name, office_phone, home_phone, cell_phone, email_address, business_address, city, state, country, zip_code, referred_by FROM contacts WHERE contactid='$contactid'"));

	$customer_phone = '';

	if ( decryptIt($customer['office_phone']) != '' || decryptIt($customer['office_phone']) != NULL ) {
		$customer_phone = decryptIt($customer['office_phone']);
	} else {
		if ( decryptIt($customer['cell_phone']) != '' || decryptIt($customer['cell_phone']) != NULL ) {
			$customer_phone = decryptIt($customer['cell_phone']);
		}
	}

    //Tax
    $point_of_sell_product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(gst) AS total_gst, SUM(pst) AS total_pst FROM point_of_sell_product WHERE posid='$posid'"));

    $get_pos_tax = get_config($dbc, 'pos_tax');
    $pdf_tax = '';
    if($get_pos_tax != '') {
        $pos_tax = explode('*#*',$get_pos_tax);

        $total_count = mb_substr_count($get_pos_tax,'*#*');
        for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
            $pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

            if (strcasecmp($pos_tax_name_rate[0], 'gst') == 0) {
                $taxrate_value = $gst_total;
            }
            if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0) {
                $taxrate_value = $pst_total;
            }

            if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {

            } else {
                $pdf_tax .= $pos_tax_name_rate[0] .' : '.$pos_tax_name_rate[1].'% : $'.number_format($taxrate_value, 2).'<br>';
            }

            $pdf_tax_number .= $pos_tax_name_rate[0].' ['.$pos_tax_name_rate[2].'] <br>';

            if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {
                $client_tax_number = $pos_tax_name_rate[0].' ['.$tax_exemption_number.']';
            }
        }
    }
    //Tax

    $pos_logo = get_config($dbc, 'pos_logo');
    $invoice_footer = get_config($dbc, 'invoice_footer');

    DEFINE('POS_LOGO', $pos_logo);
    DEFINE('INVOICE_FOOTER', $invoice_footer);

    	// PDF

		class MYPDF extends TCPDF {

		//Page header
		public function Header() {
			// Logo
			$image_file = 'download/'.POS_LOGO;
			$this->Image($image_file, 10, 10, 51, '', '', '', 'T', false, 300, 'C', false, false, 0, false, false, false);
            $this->SetFont('helvetica', '', 8);
            $header_text = '';
            $this->writeHTMLCell(0, 0, '', '', $header_text, 0, 0, false, "L", true);
		}

		// Page footer
		public function Footer() {
			// Position at 15 mm from bottom
			$this->SetY(-25);
			// Set font
			$this->SetFont('helvetica', 'I', 8);
			// Page number
			$footer_text = INVOICE_FOOTER;
			$this->writeHTMLCell(0, 0, '', '', $footer_text, 0, 0, false, "L", true);
		}
	}

	$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

	$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
	$pdf->setFooterData(array(0,64,0), array(0,64,128));

	$pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

	$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

	$pdf->AddPage();
    $pdf->SetFont('helvetica', '', 9);

    $html = '';
    $html .= ( $rookconnect=='led' ) ? '<div style="text-align:center;">' . INVOICE_FOOTER . '</div>' : '<br /><br /><br /><br />';
	$html .= '<center><div style="margin-top:10px; text-align:center;"><h1>Invoice  #'.$posid.'</h1></div></center>
    <div style="font-size:10px;">
		<table style="padding:3px; text-align:center;" border="1px" class="table table-bordered">
	<tr style="padding:3px;  text-align:center" >
		<th colspan="4" style="background-color:grey; color:black;">Customer Information</th>
	</tr>
	<tr style="padding:3px;  text-align:center; background-color:white; color:black;" >
		<td>Customer Name</td>
		<td>Customer Phone</td>
		<td>Email</td>
		<td>Reference</td>
	</tr>
	<tr style="background-color:lightgrey; color:black;">
		<td>'.decryptIt($customer['name']).'</td>
		<td>'.$customer_phone.'</td>
		<td>'.decryptIt($customer['email_address']).'</td>
		<td>'.$customer['referred_by'].'</td>
	</tr>';

    if($client_tax_number != '') {
        $html .= '<tr style="padding:3px;  text-align:center" >
            <th colspan="4" style="background-color:white; color:black;">Tax Exemption : '.$point_of_sell['tax_exemption_number'].'</th>
        </tr>';
    }
    $html .= '
	</table>
	<br><br><br>
	';

	// START INVENTORY & MISC PRODUCTS
    $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'inventory' AND inventoryid IS NOT NULL");
	$result2 = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND misc_product IS NOT NULL");
	$return_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`returned_qty`) FROM `point_of_sell_product` WHERE `posid`='$posid'"))[0];
	$num_rows = mysqli_num_rows($result);
	$num_rows2 = mysqli_num_rows($result2);
	if($num_rows > 0 && $num_rows2 > 0) {
		$titler = 'Inventory & Misc Products';
	} else if ($num_rows > 0 && $num_rows2 == 0) {
		$titler = 'Inventory';
	} else if($num_rows == 0 && $num_rows2 > 0) {
		$titler = 'Misc Products';
	}
    if($num_rows > 0 || $num_rows2 > 0) {
		$html .= '<h2>'.$titler.'</h2>
			<table border="1px" style="padding:3px; border:1px solid black;">
            <tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">';
        $html .= '<th>Part#</th>';
		$html .= '<th>Product</th>';
        $html .= '<th>Quantity</th>';
		if($return_result > 0) {
			$html .= '<th>Returned</th>';
		}
		$html .= '<th>Price</th>';
        $html .= '<th>Total</th></tr>';

		while($row = mysqli_fetch_array( $result )) {
			$inventoryid = $row['inventoryid'];
			$price = $row['price'];
			$quantity = $row['quantity'];
			$returned		= $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);

				$html .= '<tr>';
                    $html .= '<td>' . get_inventory ( $dbc, $inventoryid, 'part_no' ) . '</td>';
					$html .= '<td>' . get_inventory($dbc, $inventoryid, 'name') . '</td>';
					$html .= '<td>' . $quantity . '</td>';
					if($return_result > 0) {
						$html .= '<td>'.$returned.'</td>';
					}
					$html .= '<td>$' . $price . '</td>';
					$html .= '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}

		$result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND misc_product IS NOT NULL");
		while($row = mysqli_fetch_array( $result )) {
			$misc_product = $row['misc_product'];
			$price = $row['price'];

			if($misc_product != '') {
				$html .= '<tr>';
				$html .=  '<td>Not Available</td>';
				$html .=  '<td>'.$misc_product.'</td>';
				$html .=  '<td>'.$row['quantity'].'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$row['returned_qty'].'</td>';
				}
				$html .=  '<td>$'.$price.'</td>';
				$html .=  '<td style="text-align:right;">$'.$price * ($row['quantity'] - $row['returned_qty']).'</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
	}
	// END INVENTORY AND MISC PRODUCTS

	// START PRODUCTS
    $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'product' AND inventoryid IS NOT NULL");
	$num_rows3 = mysqli_num_rows($result);
    if($num_rows3 > 0) {
		if($num_rows > 0 || $num_rows2 > 0) { $html .= '<br>'; }
		$html .= '<h2>Product(s)</h2>
			<table border="1px" style="padding:3px; border:1px solid black;">
            <tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">
            <th>Category</th><th>Heading</th><th>Quantity</th>';
			if($return_result > 0) {
				$html .= '<th>Returned</th>';
			}
			$html .= '<th>Price</th><th>Total</th></tr>';
		while($row = mysqli_fetch_array( $result )) {
			$inventoryid = $row['inventoryid'];
			$price = $row['price'];
			$quantity = $row['quantity'];
			$returned		= $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);
				$html .= '<tr>';
				$html .=  '<td>'.get_products($dbc, $inventoryid, 'category').'</td>';
				$html .=  '<td>'.get_products($dbc, $inventoryid, 'heading').'</td>';
				$html .=  '<td>'.$quantity.'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .=  '<td>$'.$price.'</td>';
				$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
	}
	// END PRODUCTS

	// START SERVICES
    $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'service' AND inventoryid IS NOT NULL");
	$num_rows4 = mysqli_num_rows($result);
    if($num_rows4 > 0) {
		if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0) { $html .= '<br>'; }
		$html .= '<h2>Service(s)</h2>
			<table border="1px" style="padding:3px; border:1px solid black;">
            <tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">
            <th>Category</th><th>Heading</th><th>Quantity</th>';
			if($return_result > 0) {
				$html .= '<th>Returned</th>';
			}
			$html .= '<th>Price</th><th>Total</th></tr>';
		while($row = mysqli_fetch_array( $result )) {
			$inventoryid = $row['inventoryid'];
			$price = $row['price'];
			$quantity = $row['quantity'];
			$returned		= $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);
				$html .= '<tr>';
				$html .=  '<td>'.get_services($dbc, $inventoryid, 'category').'</td>';
				$html .=  '<td>'.get_services($dbc, $inventoryid, 'heading').'</td>';
				$html .=  '<td>'.$quantity.'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .=  '<td>$'.$price.'</td>';
				$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
	}
	// END SERVICES
	// START VPL
    $result = mysqli_query($dbc, "SELECT * FROM point_of_sell_product WHERE posid='$posid' AND type_category = 'vpl' AND inventoryid IS NOT NULL");
	$num_rows5 = mysqli_num_rows($result);
    if($num_rows5 > 0) {
		if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0) { $html .= '<br>'; }

		$html .= '<h2>Vendor Price List Item(s)</h2>
			<table border="1px" style="padding:3px; border:1px solid grey;">
            <tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">';

		// Don't display Part# for SEA
		if ( $rookconnect !== 'sea' ) {
			$html .= '<th>Part#</th>';
		}

        $html .= '<th>Product</th><th>Quantity</th>';
		if($return_result > 0) {
			$html .= '<th>Returned</th>';
		}
		$html .= '<th>Price</th><th>Total</th></tr>';

		while($row = mysqli_fetch_array( $result )) {
			$inventoryid = $row['inventoryid'];
			$price = $row['price'];
			$quantity = $row['quantity'];
			$returned		= $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);

				$html .= '<tr>';
				$html .=  '<td>'.get_vpl($dbc, $inventoryid, 'part_no').'</td>';
				$html .=  '<td>'.get_vpl($dbc, $inventoryid, 'name').'</td>';
				$html .=  '<td>'.$quantity.'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .=  '<td>$'.$price.'</td>';
				$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr>';
			}
		}
		$html .= '</table>';
	}
	// END VPL

    $col_span = 4;
	
	if ( !empty($couponid) || $coupon_value!=0 ) {
        $col_span += 1;
    }
	
    if($point_of_sell['discount_value'] != 0) {
        $col_span += 2;
    }

    if($point_of_sell['delivery'] != 0) {
        $col_span += 1;
    }

    if($point_of_sell['assembly'] != 0) {
        $col_span += 1;
    }
	if($point_of_sell['deposit_paid'] != 0 && $point_of_sell['deposit_paid'] != '') {
		$col_span += 2;
	}

    if($point_of_sell['delivery'] != 0 || $point_of_sell['assembly'] != 0) {
    //    $col_span += 1;
    }
    if($pdf_tax == '') {
        $col_span -= 1;
    }
    if($point_of_sell['returned_amt'] != 0) {
        $col_span += 1;
    }
	$html .= '
	<br><br><br>
	<table style="padding:3px;" border="1px" class="table table-bordered">
	<tr style="padding:3px;  text-align:center" >
		<th colspan="'.$col_span.'" style="background-color:grey; color:black;">Payment Information</th>
	</tr>
	<tr style="padding:3px; text-align:center; background-color:white; color:black;" >
		<td>Payment Type</td>';
		if ( !empty($couponid) || $coupon_value!=0 ) {
            $html .= '<td>Coupon Value</td>';
		}
        if($point_of_sell['discount_value'] != 0) {
            $html .= '<td>Total Before Discount</td>
                      <td>Discount Value</td>
                      <td>Total After Discount</td>
                    ';
        } else {
            $html .= '<td>Sub Total</td>';
        }
        if($point_of_sell['delivery'] != 0) {
            $html .= '<td>Delivery</td>';
        }
        if($point_of_sell['assembly'] != 0) {
            $html .= '<td>Assembly</td>';
        }
        if($point_of_sell['delivery'] != 0 || $point_of_sell['assembly'] != 0) {
            //$html .= '<td>Final Total</td>';
        }
        if($pdf_tax != '') {
            $html .= '<td>Tax</td>';
        }
        if($point_of_sell['returned_amt'] != 0) {
            $html .= '<td>Returned Total (Including Tax)</td>';
        }

        $html .= '<td>Total</td>';
		if($point_of_sell['deposit_paid'] != 0 && $point_of_sell['deposit_paid'] != '') {
				$html .='<td>Deposit Paid</td>';
				$html .='<td>Updated Total</td>';
			}
		$html .= '
		</tr>
	<tr style="background-color:lightgrey; color:black;">
		<td>'.$point_of_sell['payment_type'].'</td>';
		if ( !empty($couponid) || $coupon_value!=0 ) {
			$html .= '<td>$'.number_format($point_of_sell['coupon_value'], 2).'</td>';
		}
        if($point_of_sell['discount_value'] != 0) {
            if ($point_of_sell['discount_type']=='$') {
                $discount_total = '$'.number_format($point_of_sell['discount_value'], 2);
            } else {
                $discount_total = $point_of_sell['discount_value'].'%';
            }
            $html .= '<td>$'.number_format($point_of_sell['sub_total'], 2).'</td>
                      <td>'.$discount_total.'</td>
                      <td>$'.number_format($point_of_sell['total_after_discount'], 2).'</td>';
        } else {
            if ( !empty($couponid) || $coupon_value!=0 ) {
				$sub_total_coupon = $point_of_sell['sub_total'] - $coupon_value;
				$html .= '<td>$'.number_format($sub_total_coupon, 2).'</td>';
			} else {
				$html .= '<td>$'.number_format($point_of_sell['sub_total'], 2).'</td>';
			}
        }
        if($point_of_sell['delivery'] != 0) {
            $html .= '<td>$'.$point_of_sell['delivery'].'</td>';
        }
        if($point_of_sell['assembly'] != 0) {
            $html .= '<td>$'.$point_of_sell['assembly'].'</td>';
        }
        if($point_of_sell['delivery'] != 0 || $point_of_sell['assembly'] != 0) {
            //$html .= '<td>$'.$final_total.'</td>';
        }
        if($pdf_tax != '') {
            $html .= '<td>'.$pdf_tax.'</td>';
        }
        if($point_of_sell['returned_amt'] != 0) {
            $html .= '<td>$'.$point_of_sell['returned_amt'].'</td>';
        }
		$html .= '<td>$'.number_format($point_of_sell['total_price'] - $point_of_sell['returned_amt'], 2).'</td>';

		if($point_of_sell['deposit_paid'] != 0 && $point_of_sell['deposit_paid'] != '') {
				$html .='<td>$'.$point_of_sell['deposit_paid'].'</td>';
				$html .='<td>$'.$point_of_sell['updatedtotal'].'</td>';
			}


		$html .='
	</tr>
	</table><br /><br />';

    if($pdf_tax != '') {
        $html .= $pdf_tax_number.'<br /><br />';
    }
	if($point_of_sell['invoice_date'] !== '') {
					$tdduedate = '</tr><tr><td width="25%">Due Date : '.date('Y-m-d', strtotime($roww['invoice_date'] . "+30 days")).'</td>';
				} else { $tdduedate = '';  }
    $html .= 'Comments: '.$comment.'<br><br><br>
	<table> <tr><td width="25%">Date: '.$point_of_sell['invoice_date'].'</td><td width="25%"></td>'.$tdduedate.'<td width="25%"></td><td width="25%"></td></tr><tr><td width="25%">Created By: '.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).'</td><td width="25%"></td><td width="25%"></td><td width="25%"></td></tr></table>
	<br><br><br>
	<table width="400px" style="border-bottom:1px solid black;"><tr><td>
	Signature</td></tr></table></div>
	';

	if (!file_exists('download')) {
		mkdir('download', 0777, true);
	}

	$pdf->writeHTML($html, true, false, true, false, '');
	?><?php
	$pdf->Output('../Point of Sale/download/invoice_'.$posid.$edited.'.pdf', 'F');

}



?>