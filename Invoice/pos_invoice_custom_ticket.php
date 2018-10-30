<?php include_once ('../include.php');
$point_of_sell = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM invoice WHERE invoiceid='$invoiceid'"));
if(empty($posid)) {
	$posid = $invoiceid;
}
$contactid		= $point_of_sell['patientid'];
$couponid		= $point_of_sell['couponid'];
$coupon_value	= $point_of_sell['coupon_value'];
$customer = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid='$contactid'"));

$custom_ticket_fields = get_config($dbc, 'invoice_custom_ticket_fields');
if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_custom_ticket_fields_'.$point_of_sell['type']))) {
	$custom_ticket_fields = get_config($dbc, 'invoice_custom_ticket_fields_'.$point_of_sell['type']);
}
$custom_ticket_fields = explode(',', $custom_ticket_fields);

// if ( $edit_id == '0' ) {
	// $edited = '';
// } else {
	// $edited = '_' . $edit_id;
// }

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
$point_of_sell_product = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(gst) AS total_gst, SUM(pst) AS total_pst FROM invoice_lines WHERE invoiceid='$invoiceid'"));

$get_pos_tax = get_config($dbc, 'pos_tax');
$pdf_tax = '';
if($get_pos_tax != '') {
	$pos_tax = explode('*#*',$get_pos_tax);

	$total_count = mb_substr_count($get_pos_tax,'*#*');
	for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
		$pos_tax_name_rate = explode('**',$pos_tax[$eq_loop]);

		if (strcasecmp($pos_tax_name_rate[0], 'gst') == 0) {
			$taxrate_value = $point_of_sell['gst_amt'];
		}
		if (strcasecmp($pos_tax_name_rate[0], 'pst') == 0) {
			$taxrate_value = $point_of_sell['pst_amt'];
		}

		if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {

		} else {
			$pdf_tax .= $pos_tax_name_rate[0] .' : '.$pos_tax_name_rate[1].'% : $'.number_format($taxrate_value, 2).'<br>';
		}

		$pdf_tax_number .= $pos_tax_name_rate[0].'# '.$pos_tax_name_rate[2].' <br>';

		if($pos_tax_name_rate[3] == 'Yes' && $point_of_sell['client_tax_exemption'] == 'Yes') {
			$client_tax_number = $pos_tax_name_rate[0].' ['.$tax_exemption_number.']';
		}
	}
}
//Tax

$invoice_footer = get_config($dbc, 'invoice_footer');
if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_footer_'.$point_of_sell['type']))) {
    $invoice_footer = get_config($dbc, 'invoice_footer_'.$point_of_sell['type']);
}


$logo = get_config($dbc, 'invoice_logo');
if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_logo_'.$point_of_sell['type']))) {
    $logo = get_config($dbc, 'invoice_logo_'.$point_of_sell['type']);
}


$logo = 'download/'.$logo;
if(!file_exists($logo)) {
    $logo = '../POSAdvanced/'.$logo;
    if(!file_exists($logo)) {
    	$logo = '../Invoice/'.$logo;
	    if(!file_exists($logo)) {
	        $logo = '';
	    }
    }
}
DEFINE('POS_LOGO', $logo);
$logo_height = 0;
$logo_width = 0;
if(file_exists(POS_LOGO)) {
	list($image_width, $image_height) = getimagesize(POS_LOGO);
	$logo_height = $image_height;
	$logo_width = $image_width;
	if($image_height > 180) {
		$logo_width = (180 / $logo_height) * 100;
		$logo_height = 180;
	}
	if($logo_width > 360) {
		$logo_height = (360 / $logo_width) * 100;
		$logo_width = 360;
	}
	$logo_height = $logo_height / 7.2;
	$logo_width = $logo_width / 7.2;
}
DEFINE('LOGO_HEIGHT', $logo_height);
DEFINE('LOGO_WIDTH', $logo_width);
DEFINE('INVOICE_FOOTER', $invoice_footer);
DEFINE('INVOICE_DATE', $point_of_sell['invoice_date']);
DEFINE('INVOICEID', $point_of_sell['invoiceid']);

	// PDF

class MYPDF extends TCPDF {

	//Page header
	public function Header() {
		// Logo
		if(!empty(POS_LOGO)) {
			$image_file = POS_LOGO;
			if(file_get_contents($image_file)) {
				$image_file = $image_file;
			} else {
				$image_file = '../Point of Sale/'.$image_file;
			}
			if(file_get_contents($image_file)) {
				$this->Image($image_file, 15, 10, LOGO_WIDTH, LOGO_HEIGHT, '', '', 'T', false, 300, 'L', false, false, 0, false, false, false);
			}
		}
		$this->SetFont('helvetica', '', 12);
		$header_text = INVOICE_DATE.'<br />Invoice #'.INVOICEID;
		$this->writeHTMLCell(0, 0, '', 5, $header_text, 0, 0, false, "L", "R",true);
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

$pdf = new MYPDF('L', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, false, false);
$pdf->setFooterData(array(0,64,0), array(0,64,128));

$pdf->SetMargins(PDF_MARGIN_LEFT, 30, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(40);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

$pdf->AddPage();
$pdf->SetFont('helvetica', '', 9);

$html = '<table style="padding:3px;">
	<tr style="padding:3px; font-weight: bold;">
		<td style="border-bottom: 1px solid black;">Bill To:</td>
	</tr>
	<tr style="padding:3px;">
		<td>'.
			(!empty($customer['name']) ? decryptIt($customer['name']).'<br />' : '').
			(!empty($customer['mailing_address']) ? $customer['mailing_address'].'<br />' : '').
			(!empty($customer['city']) ? $customer['city'].', ' : '').(!empty($customer['province']) ? $customer['province'].' ' : '').(!empty($customer['postal_code']) ? $customer['postal_code'] : '').'<br />'.
			(!empty($customer['email_address']) ? decryptIt($customer['email_address']).'<br />' : '').
		'</td>
	</tr>
	<tr style="padding:3px; font-weight: bold;">
		<td style="border-bottom: 1px solid black;">Payable by cheque or electronic funds transfer to:</td>
	</tr>
	<tr style="padding:3px;">
		<td>'.
			(!empty(get_config($dbc, 'company_name')) ? get_config($dbc, 'company_name').'<br />' : '').
			(!empty(get_config($dbc, 'company_address')) ? get_config($dbc, 'company_address').'<br />' : '').
			(!empty(get_config($dbc, 'company_phone_number')) ? get_config($dbc, 'company_phone_number').'<br />' : '').
		'</td>
	</tr>';
if($pdf_tax != '') {
	$html .= '<tr style="padding:3px;">
		<td>'.$pdf_tax_number.'</td>
	</tr>';
}

if($client_tax_number != '') {
	$html .= '<tr style="padding:3px;" >
		<td>Tax Exemption : '.$point_of_sell['tax_exemption_number'].'</td>
	</tr>';
}
$html .= '
</table>
<br>
';

// START INVENTORY & MISC PRODUCTS
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'inventory' AND item_id IS NOT NULL");
$result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product' AND `ticketid` = 0");
$return_result = mysqli_fetch_array(mysqli_query($dbc, "SELECT MAX(`returned_qty`) FROM `invoice_lines` WHERE `invoiceid`='$invoiceid'"))[0];
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
		$inventoryid = $row['item_id'];
		$price = $row['unit_price'];
		$quantity = $row['quantity'];
		$returned = $row['returned_qty'];

		if($inventoryid != '') {
			$amount = $price*($quantity-$returned);

			$html .= '<tr>';

				// Don't display Part# for SEA
				if ( $rookconnect !== 'sea' ) {
					$html .= '<td>' . get_inventory ( $dbc, $inventoryid, 'part_no' ) . '</td>';
				}

				$html .= '<td>'.($quantity < 0 ? 'Return: ' : '').get_inventory($dbc, $inventoryid, 'name').'</td>';
				$html .= '<td>'.number_format($quantity,0).'</td>';
				if($return_result > 0) {
					$html .= '<td>'.$returned.'</td>';
				}
				$html .= '<td>$'.$price.'</td>';
				$html .= '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
			$html .= '</tr>';
		}
	}

	while($row = mysqli_fetch_array( $result2 )) {
		$misc_product = $row['description'];
		$price = $row['unit_price'];

		if($misc_product != '') {
			$html .= '<tr>';
			$html .=  '<td>Not Available</td>';
			$html .=  '<td>'.($quantity < 0 ? 'Return: ' : '').$misc_product.'</td>';
			$html .=  '<td>'.number_format($row['quantity'],0).'</td>';
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
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'product' AND item_id IS NOT NULL");
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
			$html .=  '<td>'.($quantity < 0 ? 'Return: ' : '').get_products($dbc, $inventoryid, 'heading').'</td>';
			$html .=  '<td>'.number_format($quantity,0).'</td>';
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
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'service' AND item_id IS NOT NULL AND `ticketid` = 0");
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
		$inventoryid = $row['item_id'];
		$price = $row['unit_price'];
		$quantity = $row['quantity'];
		$returned		= $row['returned_qty'];

		if($inventoryid != '') {
			$amount = $price*($quantity-$returned);
			$html .= '<tr>';
			$html .=  '<td>'.get_services($dbc, $inventoryid, 'category').'</td>';
			$html .=  '<td>'.($quantity < 0 ? 'Refund: ' : '').get_services($dbc, $inventoryid, 'heading').'</td>';
			$html .=  '<td>'.number_format($quantity,0).'</td>';
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
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'vpl' AND item_id IS NOT NULL");
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
			$html .=  '<td>'.($quantity < 0 ? 'Return: ' : '').get_vpl($dbc, $inventoryid, 'name').'</td>';
			$html .=  '<td>'.number_format($quantity,0).'</td>';
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

// START TIME SHEET
$result = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'time_cards' AND item_id IS NOT NULL");
$num_rows6 = mysqli_num_rows($result);
if($num_rows6 > 0) {
	if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0 || $num_rows5 > 0) { $html .= '<br>'; }

	$html .= '<h2>Time Sheets</h2>
		<table border="1px" style="padding:3px; border:1px solid grey;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">';

	$html .= '<th>Heading</th><th>Quantity</th><th>Price</th><th>Total</th></tr>';

	while($row = mysqli_fetch_array( $result )) {
		$amount = $row['sub_total'];

		$html .= '<tr>';
		$html .=  '<td>'.$row['heading'].'</td>';
		$html .=  '<td>'.($amount < 0 ? 'Refund: ' : '').number_format($row['quantity'],0).'</td>';
		$html .=  '<td>$'.$row['unit_price'].'</td>';
		$html .=  '<td style="text-align:right;">$'.number_format($amount,2).'</td>';
		$html .= '</tr>';
	}
	$html .= '</table>';
}
// START TIME SHEET

// START TICKETS
$result = mysqli_query($dbc, "SELECT `ticketid`, `stop_id` FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'service' AND item_id IS NOT NULL AND `ticketid` > 0 GROUP BY `ticketid`".(in_array('delivery_group',$invoice_custom_ticket_fields) ? ", `stop_id`" : ''));
$num_rows7 = mysqli_num_rows($result);
if($num_rows7 > 0) {
	if($num_rows > 0 || $num_rows2 > 0 || $num_rows3 > 0 || $num_rows4 > 0 || $num_rows5 > 0|| $num_rows6 > 0) { $html .= '<br>'; }

	$invoice_custom_ticket = get_config($dbc, 'invoice_custom_ticket');
	if(!empty($point_of_sell['type']) && !empty(get_config($dbc, 'invoice_custom_ticket_'.$point_of_sell['type']))) {
	    $invoice_custom_ticket = get_config($dbc, 'invoice_custom_ticket_'.$point_of_sell['type']);
	}
	$invoice_custom_ticket = array_filter(explode(',',$invoice_custom_ticket));

	$html .= '<h2>'.TICKET_NOUN.'(s)</h2>
		<table border="1px" style="padding:3px; border:1px solid black;">
		<tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">
		<th width="10%">Date</th><th width="10%">'.TICKET_NOUN.'</th>';
		$service_width_diff = 0;
		if(in_array('num_stops',$custom_ticket_fields)) {
			$service_width_diff += 10;
			$html .= '<th width="10%">Stops</th>';
		}

        /*
		if(in_array('customer_code',$custom_ticket_fields)) {
			$service_width_diff += 10;
			$html .= '<th width="10%">Customer Code</th>';
		}
         */
		if(in_array('pro_number',$custom_ticket_fields)) {
			$service_width_diff += 10;
			$html .= '<th width="10%">Pro Number</th>';
		}
		if(in_array('volume',$custom_ticket_fields)) {
			$service_width_diff += 10;
			$html .= '<th width="10%">Volume</th>';
		}
		if(in_array('location',$custom_ticket_fields)) {
			$service_width_diff += 10;
			$html .= '<th width="10%">Location</th>';
		}
		if(in_array('departure_location',$custom_ticket_fields)) {
			$service_width_diff += 10;
			$html .= '<th width="10%">Departure Location</th>';
		}
		if(in_array('destination',$custom_ticket_fields)) {
			$service_width_diff += 10;
			$html .= '<th width="10%">Destination</th>';
		}

		$service_widths = count($invoice_custom_ticket) + 1;
		$service_widths = (70 - $service_width_diff) / $service_widths;
		foreach($invoice_custom_ticket as $service_id) {
            if($service_id > 0) {
                $service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$service_id'"));
                $html .= '<th width="'.$service_widths.'%">'.$service['heading'].'</th>';
            } else if(explode('|',$service_id)[0] == 'multi_srv_group') {
                $html .= '<th width="'.$service_widths.'%">'.explode('|',$service_id)[1].'</th>';
            }
		}
		$html .= '<th width="'.$service_widths.'%">All Other Items</th><th width="10%">Total</th></tr>';
	while($ticketid = mysqli_fetch_array( $result )) {
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$ticketid['ticketid']."'"));

		$line_total = 0;

		$html .= '<tr>';
		$html .= '<td>'.$ticket['to_do_date'].'</td>';
		$html .= '<td>'.get_ticket_label($dbc, $ticket).'</td>';
        $num_stops = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`id`) num_rows FROM `ticket_schedule` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0 AND `type` != 'origin' AND `type` != 'destination'"))['num_rows'];
		if(in_array('num_stops',$custom_ticket_fields)) {
			$html .= '<td>'.$num_stops.'</td>';
		}

        /*
		if(in_array('customer_code',$custom_ticket_fields)) {
			$customer_code = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`id`) num_rows FROM `ticket_schedule` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0 AND `type` != 'origin' AND `type` != 'destination'"))['num_rows'];
			$html .= '<td>'.$customer_code.'</td>';
		}
        */
        if(in_array('pro_number',$custom_ticket_fields)) {
			$pro_numbers = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT `order_number` FROM `ticket_schedule` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0 AND `type` != 'origin' AND `type` != 'destination'".(in_array('delivery_group',$invoice_custom_ticket_fields) ? " AND `id`='".$ticketid['stop_id']."'" : '')),MYSQLI_ASSOC),'order_number');
			$html .= '<td>';
                foreach ( $pro_numbers as $pro_number ) {
                	if(!empty($pro_number)) {
	                    $html .= $pro_number .'<br />';
	                }
                }
            $html .= '</td>';
        }
        if(in_array('volume',$custom_ticket_fields)) {
			$volumes = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT `volume` FROM `ticket_schedule` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0 AND `type` != 'origin' AND `type` != 'destination'".(in_array('delivery_group',$invoice_custom_ticket_fields) ? " AND `id`='".$ticketid['stop_id']."'" : '')),MYSQLI_ASSOC),'order_number');
			$html .= '<td>';
                foreach ( $volumes as $volume ) {
                	if(!empty($volume)) {
	                    $html .= $volume .'<br />';
	                }
                }
            $html .= '</td>';
        }
		if(in_array('location',$custom_ticket_fields)) {
			$locations = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT CONCAT(IF(`address`='', '', `address`), IF(`city`='', '', CONCAT(', ', `city`)), IF(`postal_code`='', '', CONCAT(', ', `postal_code`))) locations FROM `ticket_schedule` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0 AND `type` != 'origin' AND `type` != 'destination'".(in_array('delivery_group',$invoice_custom_ticket_fields) ? " AND `id`='".$ticketid['stop_id']."'" : '')),MYSQLI_ASSOC),'locations');
			$html .= '<td>';
                foreach ( $locations as $location ) {
                	if(!empty($location)) {
	                    $html .= $location .'<br />';
	                }
                }
            $html .= '</td>';
		}
		if(in_array('departure_location',$custom_ticket_fields)) {
			$departure_locations = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT CONCAT(IF(`address`='', '', `address`), IF(`city`='', '', CONCAT(', ', `city`)), IF(`postal_code`='', '', CONCAT(', ', `postal_code`))) locations FROM `ticket_schedule` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0 AND `type` = 'Pick Up'".(in_array('delivery_group',$invoice_custom_ticket_fields) ? " AND `id`='".$ticketid['stop_id']."'" : '')),MYSQLI_ASSOC),'locations');
			$html .= '<td>';
                foreach ( $departure_locations as $departure_location ) {
                	if(!empty($departure_location)) {
	                    $html .= $departure_location .'<br />';
	                }
                }
            $html .= '</td>';
		}
		if(in_array('destination',$custom_ticket_fields)) {
			$destinations = array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT CONCAT(IF(`address`='', '', `address`), IF(`city`='', '', CONCAT(', ', `city`)), IF(`postal_code`='', '', CONCAT(', ', `postal_code`))) locations FROM `ticket_schedule` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0 AND `type` = 'Drop Off'".(in_array('delivery_group',$invoice_custom_ticket_fields) ? " AND `id`='".$ticketid['stop_id']."'" : '')),MYSQLI_ASSOC),'locations');
			$html .= '<td>';
                foreach ( $destinations as $destination ) {
                	if(!empty($destination)) {
	                    $html .= $destination .'<br />';
	                }
                }
            $html .= '</td>';
		}


		$item_id_query = '';
		foreach($invoice_custom_ticket as $service_id) {
            if($service_id > 0) {
                $html .= '<td>';
                $service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$service_id'"));
                $result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'service' AND item_id = '$service_id' AND `ticketid` = '".$ticket['ticketid']."'");
                while($row = mysqli_fetch_assoc($result2)) {
                    $inventoryid = $row['item_id'];
                    $price = $row['unit_price'];
                    $quantity = $row['quantity'];
                    $returned		= $row['returned_qty'];

                    if($inventoryid != '') {
                        $amount = $price*($quantity-$returned);
                        $line_total += $amount;

                        $html .= '<table border="0"><tr>';
                        $html .= '<td width="75%">'.$service['heading'].'<br />$'.number_format($price,2).' x '.$quantity.'</td>';
                        $html .= '<td width="25%" style="text-align:right;">$'.number_format($amount,2).'</td>';
                        $html .= '</tr></table><p style="font-size:1px;"></p>';
                    }
                }
                $html .= '</td>';
                $item_id_query .= " AND item_id NOT IN ('".$service_id."')";

            } else if(explode('|',$service_id)[0] == 'multi_srv_group') {
                $html .= '<td>';
                $service_id = explode('|',$service_id);
                unset($service_id[1]);
                unset($service_id[0]);
                if(count($service_id) > 0) {
                    $service_id = implode(',',$service_id);
                    $result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'service' AND item_id IN ($service_id) AND `ticketid` = '".$ticket['ticketid']."'".(in_array('delivery_group',$invoice_custom_ticket_fields) ? " AND `stop_id`='".$ticketid['stop_id']."'" : ''));
                    while($row = mysqli_fetch_assoc($result2)) {
                        $service = $row['item_id'];
                        $service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid`='$service'"));
                        $inventoryid = $row['item_id'];
                        $price = $row['unit_price'];
                        $quantity = $row['quantity'];
                        $returned		= $row['returned_qty'];

                        if($inventoryid != '') {
                            $amount = $price*($quantity-$returned);
                            $line_total += $amount;

                            $html .= '<table border="0"><tr>';
                            $html .= '<td width="75%">'.$service['heading'].'<br />$'.number_format($price,2).' x '.$quantity.'</td>';
                            $html .= '<td width="25%" style="text-align:right;">$'.number_format($amount,2).'</td>';
                            $html .= '</tr></table><p style="font-size:1px;"></p>';
                        }
                    }
                    $item_id_query .= " AND item_id NOT IN (".$service_id.")";
                }
                $html .= '</td>';
            }
		}
		$html .= '<td>';
		$result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'service' AND `ticketid` = '".$ticket['ticketid']."' AND `item_id` IS NOT NULL ".$item_id_query.(in_array('delivery_group',$invoice_custom_ticket_fields) ? " AND `stop_id`='".$ticketid['stop_id']."'" : ''));
		while($row = mysqli_fetch_assoc($result2)) {
			$inventoryid = $row['item_id'];
			$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$inventoryid'"));
			$price = $row['unit_price'];
			$quantity = $row['quantity'];
			$returned		= $row['returned_qty'];

			if($inventoryid != '') {
				$amount = $price*($quantity-$returned);
				$line_total += $amount;

				$html .= '<table border="0"><tr>';
				$html .= '<td width="75%">'.$service['heading'].'<br />$'.number_format($price,2).' x '.$quantity.'</td>';
				$html .= '<td width="25%" style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr></table><p style="font-size:1px;"></p>';
			}
		}
		$result2 = mysqli_query($dbc, "SELECT * FROM invoice_lines WHERE invoiceid='$invoiceid' AND category = 'misc product' AND `ticketid` = '".$ticket['ticketid']."' AND `item_id` IS NOT NULL");
		while($row = mysqli_fetch_assoc($result2)) {
			$description = $row['description'];
			$price = $row['unit_price'];
			$quantity = $row['quantity'];
			$returned		= $row['returned_qty'];

			if($description != '' && $quantity > 0) {
				$amount = $price*($quantity-$returned);
				$line_total += $amount;

				$html .= '<table border="0"><tr>';
				$html .= '<td width="75%">'.$description.'<br />$'.number_format($price,2).' x '.$quantity.'</td>';
				$html .= '<td width="25%" style="text-align:right;">$'.number_format($amount,2).'</td>';
				$html .= '</tr></table><p style="font-size:1px;"></p>';
			}
		}
		$html .= '</td>';
		$html .= '<td style="text-align:right;">$'.number_format($line_total,2).'</td>';
		$html .= '</tr>';
	}
	$html .= '</table>';
}
// END TICKETS

$col_span = 4;

if ( !empty($couponid) || $coupon_value!=0 ) {
	$col_span += 1;
}

if($point_of_sell['discount'] != 0) {
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
if($point_of_sell['returned_amt'] != '') {
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
	if($point_of_sell['discount'] != 0) {
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
	<td>'.explode('#*#',$point_of_sell['payment_type'])[0].'</td>';
	if ( !empty($couponid) || $coupon_value!=0 ) {
		$html .= '<td>$'.number_format($point_of_sell['coupon_value'], 2).'</td>';
	}
	if($point_of_sell['discount'] != 0) {
		$html .= '<td>$'.number_format($point_of_sell['total_price'], 2).'</td>
				  <td>$'.number_format($point_of_sell['discount'], 2).'</td>
				  <td>$'.number_format($point_of_sell['total_price']-$point_of_sell['discount'], 2).'</td>';
	} else {
		if ( !empty($couponid) || $coupon_value!=0 ) {
			$sub_total_coupon = $point_of_sell['total_price'] - $coupon_value;
			$html .= '<td>$'.number_format($sub_total_coupon, 2).'</td>';
		} else {
			$html .= '<td>$'.number_format($point_of_sell['total_price'], 2).'</td>';
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
	if($point_of_sell['returned_amt'] != '') {
		$html .= '<td>$'.$point_of_sell['returned_amt'].'</td>';
	}
	//$html .= '<td>$'.number_format($point_of_sell['total_price'] - $point_of_sell['returned_amt'], 2).'</td>';
    $html .= '<td>$'.number_format($point_of_sell['final_price'], 2).'</td>';

	if($point_of_sell['deposit_paid'] != 0 && $point_of_sell['deposit_paid'] != '') {
			$html .='<td>$'.$point_of_sell['deposit_paid'].'</td>';
			$html .='<td>$'.$point_of_sell['updatedtotal'].'</td>';
		}


	$html .='
</tr>
</table><br /><br />';

if($point_of_sell['invoice_date'] !== '') {
				$tdduedate = '</tr><tr><td width="25%">Due Date : '.date('Y-m-d', strtotime($roww['invoice_date'] . "+30 days")).'</td>';
			} else { $tdduedate = '';  }
$html .= 'Comments: '.html_entity_decode($comment).'<br><br><br>
<table> <tr><td width="25%">Date: '.$point_of_sell['invoice_date'].'</td><td width="25%"></td>'.$tdduedate.'<td width="25%"></td><td width="25%"></td></tr><tr><td width="25%">Created By: '.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).'</td><td width="25%"></td><td width="25%"></td><td width="25%"></td></tr></table>
<br><br><br>
<table width="400px" style="border-bottom:1px solid black;"><tr><td>
Signature</td></tr></table></div>
';

if (!file_exists('download')) {
	mkdir('download', 0777, true);
}

$pdf->writeHTML($html, true, false, true, false, '');
$invoice_type = $point_of_sell['type'];
include('../Invoice/pos_invoice_append_ticket.php');
$pdf->Output('download/invoice_'.$posid.$edited.'.pdf', 'F');