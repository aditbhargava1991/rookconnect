<?php $ticket_list = explode(',',$_POST['ticketid']);
$total_price = 0;
$inv_services = [];
$inv_service_ticketid = [];
$inv_service_stopid = [];
$inv_service_qty = [];
$inv_service_fee = [];
$services_price = 0;
$misc_item = [];
$misc_ticketid = [];
$misc_stopid = [];
$misc_price = [];
$misc_qty = [];
$misc_total = [];
$price_final = 0;
$ticket_type = '';
$total_service_price = 0;
$po_num_list = [];
$value_config = ','.get_config($dbc, 'project_admin_fields').',';
foreach($ticket_list as $ticketid) {
	if($ticketid > 0) {
		$ticket = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
        $ticket_type = empty($ticket_type) ? $ticket['ticket_type'] : $ticket_type;
        foreach(explode('#*#',$ticket['purchase_order']) as $po_number) {
            $po_num_list[] = $po_number;
        }
		foreach(explode(',',$ticket['serviceid']) as $i => $service) {
			$qty = explode(',',$ticket['service_qty'])[$i];
			$fuel = explode(',',$ticket['service_fuel_charge'])[$i];
			$discount = explode(',',$ticket['service_discount'])[$i];
			$dis_type = explode(',',$ticket['service_discount_type'])[$i];
			$price = 0;
			$customer_rate = $dbc->query("SELECT `services` FROM `rate_card` WHERE `clientid`='".$ticket['businessid']."' AND `deleted`=0 AND `on_off`=1")->fetch_assoc();
			foreach(explode('**',$customer_rate['services']) as $service_rate) {
				$service_rate = explode('#',$service_rate);
				if($service == $service_rate[0] && $service_rate[1] > 0) {
					$price = $service_rate[1];
				}
			}
			if(!($price > 0)) {
				$service_rate = $dbc->query("SELECT `cust_price`, `admin_fee` FROM `company_rate_card` WHERE `deleted`=0 AND `item_id`='$service' AND `tile_name` LIKE 'Services' AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) AND `cust_price` > 0")->fetch_assoc();
				$price = $service_rate['cust_price'];
			}
			$inv_services[] = $service;
			$inv_service_ticketid[] = $ticketid;
			$inv_service_stopid[] = 0;
			$inv_service_qty[] = $qty;
			$price_total = ($price * $qty * ($fuel / 100 + 100));
			$price_total -= ($dis_type == '%' ? $discount / 100 * $price_total : $discount);
			$inv_service_fee[] = $price_total;
			$total_price += $price_total;
            $total_service_price += $price_total;
		}
        $srv_i = count(explode(',',$ticket['serviceid']));
		$tickets = $dbc->query("SELECT * FROM `ticket_schedule` WHERE `ticketid`='$ticketid' AND `deleted`=0 ORDER BY `sort`");
        while($ticket = $tickets->fetch_assoc()) {
            foreach(explode(',',$ticket['serviceid']) as $i => $service) {
                $fuel = explode(',',$ticket['surcharge'])[$i];
                $discount = explode(',',$ticket['service_discount'])[$i];
                $dis_type = explode(',',$ticket['service_discount_type'])[$i];
                $price = 0;
                $customer_rate = $dbc->query("SELECT `services` FROM `rate_card` WHERE `clientid`='".$ticket['businessid']."' AND `deleted`=0 AND `on_off`=1")->fetch_assoc();
                foreach(explode('**',$customer_rate['services']) as $service_rate) {
                    $service_rate = explode('#',$service_rate);
                    if($service == $service_rate[0] && $service_rate[1] > 0) {
                        $price = $service_rate[1];
                    }
                }
                if(!($price > 0)) {
                    $service_rate = $dbc->query("SELECT `cust_price`, `admin_fee` FROM `company_rate_card` WHERE `deleted`=0 AND `item_id`='$service' AND `tile_name` LIKE 'Services' AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) AND `cust_price` > 0")->fetch_assoc();
                    $price = $service_rate['cust_price'];
                }
                $inv_services[] = $service;
                $inv_service_ticketid[] = $ticketid;


                $inv_service_stopid[] = $ticket['id'];
                $inv_service_qty[] = 1;
                $price_total = ($price + ($fuel / 100 + 100));
                $price_total -= ($dis_type == '%' ? $discount / 100 * $price_total : $discount);
                $inv_service_fee[] = $price_total;
                $total_price += $price_total;
                $total_service_price += $price_total;
            }
            $srv_i += count(explode(',',$ticket['serviceid']));
        }
		$ticket_lines = $dbc->query("SELECT * FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `src_table` LIKE 'Staff%'");
		while($line = $ticket_lines->fetch_assoc()) {
			$description = get_contact($dbc, $line['item_id']).' - '.$line['position'];
			$qty = !empty($line['hours_set']) ? $line['hours_set'] : $line['hours_tracked'];
			$price = $dbc->query("SELECT * FROM `company_rate_card` WHERE `deleted`=0 AND (`cust_price` > 0 OR `hourly` > 0) AND ((`tile_name`='Staff' AND (`item_id`='".$line['item_id']."' OR `description`='all_staff')) OR (`tile_name`='Position' AND (`description`='".$line['position']."' OR `item_id`='".get_field_value('position_id','positions','name',$line['position'])."')))")->fetch_assoc();
			$price = $price['cust_price'] > 0 ? $price['cust_price'] : $price['hourly'];
			$misc_item[] = $description;
			$misc_ticketid[] = $ticketid;
			$misc_stopid[] = 0;
			$misc_qty[] = $qty;
			$misc_price[] = $price;
			$misc_total[] = $price * $qty;
			$total_price += $price * $qty;
		}
        if(strpos($value_config, ',Additional KM Charge,') !== FALSE) {
            $travel_km = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`hours_travel`) `travel_km` FROM `ticket_attached` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0"))['travel_km'];
            $total_travel_km = $total_service_price * $travel_km;
            if($total_travel_km > 0) {
                $description = 'Additional KM Charge';
                $qty = 1;
                $price = $total_travel_km;
                $misc_item[] = $description;
                $misc_stopid[] = 0;
                $misc_qty[] = $qty;
                $misc_price = $price;
                $mist_total = $price * $qty;
                $total_price += $price * $qty;
            }
        }
		$ticket_lines = $dbc->query("SELECT * FROM `ticket_attached` WHERE `ticketid`='$ticketid' AND `deleted`=0 AND `src_table` LIKE 'misc_item'");
		while($line = $ticket_lines->fetch_assoc()) {
			$description = get_contact($dbc, $line['description']);
			$qty = $line['qty'];
			$price = $line['rate'];
			$misc_item[] = $description;
			$misc_ticketid[] = $ticketid;
			$misc_stopid[] = 0;
			$misc_price[] = $price;
			$misc_qty[] = $qty;
			$misc_total[] = $price * $qty;
			$total_price += $price * $qty;
		}
		$billing_discount = $ticket['billing_discount'];
		$billing_dis_type = $ticket['billing_discount_type'];
		$billing_discount_total = ($billing_dis_type == '%' ? $total_price * $billing_discount / 100 : $billing_discount);
		$price_final += $total_price - $billing_discount_total;
	}
}
$invoice_type = !empty($ticket_type) ? get_config($dbc, 'ticket_invoice_type_'.$ticket_type) : '';
$invoice_type = empty($invoice_type) ? get_config($dbc, 'ticket_invoice_type') : $invoice_type;
$po_num_list = implode(', ',array_filter(array_unique($po_num_list)));
mysqli_query($dbc, "INSERT INTO `invoice` (`tile_name`,`type`,`po_num`,`projectid`,`ticketid`,`businessid`,`patientid`,`invoice_date`,`total_price`,`discount`,`final_price`,`serviceid`,`fee`,`misc_item`,`misc_price`,`misc_qty`,`misc_total`,`service_ticketid`,`misc_ticketid`) SELECT 'invoice','$invoice_type','$po_num_list',MAX(`projectid`),GROUP_CONCAT(`ticketid` SEPARATOR ','),MAX(`businessid`),MAX(`businessid`),DATE(NOW()),'$total_price','$billing_discount_total','$price_final','".implode(',',$inv_services)."','".implode(',',$inv_service_fee)."','".implode(',',$misc_item)."','".implode(',',$misc_price)."','".implode(',',$misc_qty)."','".implode(',',$misc_total)."','".implode(',',$inv_service_ticketid)."','".implode(',',$misc_ticketid)."' FROM `tickets` WHERE `ticketid` IN (".implode(',',$ticket_list).")");
$invoiceid = $dbc->insert_id;
foreach($inv_services as $i => $service) {
	$service = $dbc->query("SELECT * FROM `services` WHERE `serviceid`='$service'")->fetch_assoc();
	mysqli_query($dbc, "INSERT INTO `invoice_lines` (`invoiceid`, `item_id`, `category`, `heading`, `description`, `quantity`, `unit_price`, `uom`, `sub_total`, `ticketid`) VALUES ('$invoiceid', '$service', 'services', '".TICKET_TILE."', '{$service['heading']}', '{$inv_service_qty[$i]}', '".($inv_service_fee[$i] / $inv_service_qty[$i])."', 'each', '".$inv_service_fee[$i]."', '".$inv_service_ticketid[$i]."')");
}
foreach($misc_item as $i => $misc) {
	mysqli_query($dbc, "INSERT INTO `invoice_lines` (`invoiceid`, `category`, `heading`, `description`, `quantity`, `unit_price`, `uom`, `sub_total`, `ticketid`) VALUES ('$invoiceid', 'misc_product', '".TICKET_TILE."', '$misc', '{$misc_qty[$i]}', '".($misc_price[$i])."', 'each', '".$misc_total[$i]."', '".$misc_ticketid[$i]."')");
}
$tile_target = 'Invoice';
if(!tile_visible($dbc, 'check_out')) {
	if(tile_visible($dbc, 'posadvanced')) {
		$tile_target = 'POSAdvanced';
	}
}
$get_invoice = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='$invoiceid'"));
// PDF
$invoice_design = get_config($dbc, 'invoice_design');
if(!empty($get_invoice['type']) && !empty(get_config($dbc, 'invoice_design_'.$get_invoice['type']))) {
    $invoice_design = get_config($dbc, 'invoice_design_'.$get_invoice['type']);
}
switch($invoice_design) {
    case 2:
        include('pos_invoice_2.php');
        break;
    case 3:
        include('pos_invoice_3.php');
        break;
    case 4:
        include ('patient_invoice_pdf.php');
        if($insurerid != '') {
            include ('insurer_invoice_pdf.php');
        }
        break;
    case 5:
        include('pos_invoice_small.php');
        break;
    case 'service':
        include('pos_invoice_service.php');
        break;
    case 'pink':
        include ('pos_invoice_pink.php');
        break;
    case 'cnt1':
        include ('pos_invoice_contractor_1.php');
        break;
    case 'cnt2':
        include ('pos_invoice_contractor_2.php');
        break;
    case 'cnt3':
        include ('pos_invoice_contractor_3.php');
        break;
    case 'custom_ticket':
        include ('pos_invoice_custom_ticket.php');
        break;
    case 1:
    default:
        include('pos_invoice_1.php');
        break;
}
$invoiceid .= empty($invoice_type) ? '' : '&type='.$invoice_type;echo WEBSITE_URL.'/'.$tile_target.'/create_invoice.php?invoiceid='.$invoiceid; ?>