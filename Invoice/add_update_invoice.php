<?php
if($invoice_mode != 'Saved' && $_POST['request_recommendation'] == 'send') {
	include('send_crm_recommend.php');
}
$receipt_payments = [];
$inv_status = 'Completed';

if($invoice_mode != 'Adjustment') {
	$type_type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);

	$today_date = date('Y-m-d');
	$service_date = $_POST['service_date'];
	$paid = $_POST['paid'];
	$service_pdf = '';
	$invoice_type = $_POST['invoice_type'];
	$gratuity = $_POST['gratuity'];
	$giftcardid = filter_var($_POST['gf_number'],FILTER_SANITIZE_STRING);
	$fee_total_price = 0;
	$fee_patient_price = 0;
	$fee_insurer_price = [];
	$inv_total_price = 0;
	$inv_patient_price = 0;
	$inv_insurer_price = [];
	$package_total_price = 0;
	$package_patient_price = 0;
	$package_insurer_price = [];
	$misc_total_price = 0;
	$misc_patient_price = 0;
	$misc_insurer_price = [];

    $reference = isset($_POST['reference']) ? filter_var(htmlentities($_POST['reference']),FILTER_SANITIZE_STRING) : '';
	$comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

	$pricing = $_POST['pricing'];
	$delivery_type =$_POST['delivery_type'];
	$contractorid = $_POST['contractorid'];
	$delivery_address =  filter_var($_POST['delivery_address'],FILTER_SANITIZE_STRING);
	$delivery = filter_var($_POST['delivery'],FILTER_SANITIZE_STRING);
    $assembly = filter_var($_POST['assembly'],FILTER_SANITIZE_STRING);
	$ship_date = filter_var($_POST['ship_date'],FILTER_SANITIZE_STRING);
	$created_by = $_SESSION['contactid'];

	$promotionid = $_POST['promotionid'];
	$promotion = '';
	if($promotionid != '') {
		$promotion .= 'Promotion : '.get_promotion($dbc, $promotionid, 'heading').' : $'.get_promotion($dbc, $promotionid, 'cost');
	}
	$promo_total = $promo_value = get_promotion($dbc, $promotionid, 'cost');

	$service_promo_lines = $_POST['service_promotion_line'];
	$product_promo_lines = $_POST['product_promotion_line'];
	$promo_name = '';

	if($promotionid > 0) {
		$promo_details = mysqli_fetch_array(mysqli_query($dbc,"SELECT promotionid, heading, cost FROM promotion WHERE `promotionid`='$promotionid'"));
		$promo_name = $promo_details['heading'];
	}

	//To be used to track the portion of each service and product that is paid by each source
	$invoice_patient = [];
	$invoice_insurer = [];
	$service_patient_pays = [];
	$product_patient_pays = [];
	$package_patient_pays = [];
	$misc_patient_pays = [];
	$service_ins_pays = [];
	$product_ins_pays = [];
	$package_ins_pays = [];
	$misc_ins_pays = [];
	$service_pro_bono = '';
	$product_pro_bono = '';
	$package_pro_bono = '';
	$misc_pro_bono = '';
	$service_promo = '';
	$product_promo = '';
	$package_promo = '';
	$misc_promo = '';
	$pro_bono = 0;
    $credit_balance = 0;

    $invoice_lines_delete = [];
	$invoice_lines = [];

	$payment_types = [];
	$payment_amts = [];
	$payment_used = [];
	foreach($_POST['payment_type'] as $i => $type) {
		if($type == 'Pro-Bono') {
			$payment_types = array_merge([$type], $payment_types);
			$payment_amts = array_merge([$_POST['payment_price'][$i]], $payment_amts);
			$payment_used = array_merge([$_POST['payment_price'][$i]], $payment_used);
			$pro_bono += $_POST['payment_price'][$i];
		} else {
			$payment_types[] = $type;
			$payment_amts[] = $_POST['payment_price'][$i];
			$payment_used[] = $_POST['payment_price'][$i];
		}

        if ( $type=='On Account' ) {
            $credit_balance += $_POST['payment_price'][$i];
            $inv_status = 'Posted';
        }
	}

	if($giftcardid != '') {
		$payment_types[] = 'Gift Card';
		$payment_amts[] = filter_var($_POST['set_gf'],FILTER_SANITIZE_STRING);
		$payment_used[] = filter_var($_POST['set_gf'],FILTER_SANITIZE_STRING);
		$_POST['final_price'] += $_POST['set_gf'];
	}

	$insurers = [];
	foreach($_POST['insurer_payment_amt'] as $i => $ins_amt) {
		$insurers[$_POST['insurerid'][$i]] += $ins_amt;
	}

	$services = [];
	$service_cats = [];
	$service_names = [];
	$service_fees = [];
	$service_gst = [];
	$service_admin_fees = [];
	$service_totals = [];
	$service_patient_type = [];
	$service_insurer_id = [];
	$service_patient = [];
	$service_insurer = [];
    //Delete previous records before saving the updated ones
    $invoice_lines_delete[] = "DELETE FROM `invoice_lines` WHERE `invoiceid`='INVOICEID' AND `category`='service'";
	foreach($_POST['serviceid'] as $i => $sid) {
		if($sid > 0) {
			$service_insurer[$i] = '';
			$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT s.category, s.heading, r.cust_price service_rate, r.admin_fee, r.editable FROM services s, company_rate_card r WHERE s.serviceid='$sid' AND s.serviceid = r.item_id AND r.tile_name LIKE 'Services' AND '$today_date' >= r.start_date AND ('$today_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00') AND r.deleted=0"));
			$services[] = $sid;
			$service_pdf .= $service['category'].' : '.$service['heading'].'<br>';
			$service_cats[] = $service['category'];
			$service_names[] = $service['heading'];
			$service_fee = ($service['editable'] > 0 ? $_POST['fee'][$i] : $service['service_rate']);
			$service_fees[] = $service_fee;
			$gst = $_POST['gst_exempt'][$i] == "1" ? 0 : $service_fee * $_POST['tax_rate'] / 100;
			$service_gst[] = $gst;
			$service_admin_fees[] = $service['admin_fee'];
			$fee = $service_fee + $gst;
			$fee_total_price += $fee;
			$service_totals[] = $fee;
			$row = $_POST['service_row_id'][$i];
            $invoice_lines[] = "INSERT INTO `invoice_lines` (`invoiceid`, `item_id`, `description`, `category`, `quantity`, `unit_price`, `sub_total`, `gst`, `total`)
				VALUES ('INVOICEID', '$sid', '".$service['category'].': '.$service['heading']."', 'service', '1', '".$service_fee."', '".$service_fee."', '$gst', '$fee')";
			foreach($_POST['insurer_row_applied'] as $j => $match_row) {
				$applied = 0;
				if($row == $match_row && $_POST['insurer_payment_amt'][$j] != 0) {
					$applied = $_POST['insurer_payment_amt'][$j];

					//Invoice Insurer Portion
					$gst_insurer = ($fee == $applied ? $gst : 0);//round($applied / $service['service_rate'] * $gst,2);
					//$applied += $gst_insurer;
					$invoice_insurer[] = [ 'insurer' => $_POST['insurerid'][$j],
						'sub_total' => $applied - $gst_insurer,
						'gst_amt' => $gst_insurer,
						'price' => $applied,
						'service_cat' => $service['category'],
						'service_name' => $service['heading'],
						'product_name' => '',
						'paid' => $paid ];

					$gst -= $gst_insurer;
					$fee -= $applied;
					$list_service_insurer[$_POST['insurerid'][$j]] .= $service['category'].' : '.$service['heading'].'<br>';
					$fee_insurer_price[$_POST['insurerid'][$j]] += $applied;
					//$insurers[$_POST['insurerid'][$i]] += $gst_insurer;
					$service_insurer[$i] .= $_POST['insurerid'][$j].':'.$applied.'#*#';
				}
			}
			$promo_applied = 0;
			if($promo_value > 0 && $fee > 0) {
				if($promo_value > $fee) {
					$promo_applied = $fee;
				} else {
					$promo_applied = $promo_value;
				}
			}
			$promo_value -= $promo_applied;
			$fee -= $promo_applied;
			$service_promo .= $promo_applied.',';
			foreach($payment_used as $j => $amt_unused) {
				$applied = 0;
				if($fee > 0 && $amt_unused > 0) {
					if($fee > $amt_unused) {
						$applied = $amt_unused;
					} else {
						$applied = $fee;
					}
					$payment_used[$j] -= $applied;

					//Invoice Patient Portion
					$gst_patient = round($applied / $fee * $gst,2);
					if($gst < $gst_patient) {
						$gst_patient = $gst;
					}
					$invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
						'gst_amt' => $gst_patient,
						'gratuity' => 0,
						'price' => $applied,
						'service_cat' => $service['category'],
						'service_name' => $service['heading'],
						'product_name' => '',
						'paid' => $payment_types[$j] ];

					$fee -= $applied;
					$gst -= $gst_patient;
					$fee_patient_price += $applied;
				}

				if($payment_types[$j] == 'Pro-Bono') {
					$service_pro_bono .= $applied.',';
				} else {
					$service_patient[$i] += $applied;
				}
			}
		}
	}

	$packages = [];
	$package_name = [];
	$package_fees = [];
	$package_gst = [];
	$package_totals = [];
	$package_patient_type = [];
	$package_insurer_id = [];
	$package_patient = [];
	$package_insurer = [];
    //Delete previous records before saving the updated ones
    $invoice_lines_delete[] = "DELETE FROM `invoice_lines` WHERE `invoiceid`='INVOICEID' AND `category`='package'";
	foreach($_POST['packageid'] as $i => $package) {
		if($package > 0) {
			$package_insurer[$i] = '';
			$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `package` WHERE `packageid`='$package'"));
			$list_package_patient .= $details['category'].' : '.$details['heading'].'<br>';
			$packages[] = $package;
			$package_name[] = $details['heading'];
			$package_fees[] = $_POST['package_cost'][$i];
			$gst = $_POST['package_gst_exempt'][$i] == "1" ? 0 : $_POST['package_cost'][$i] * $_POST['tax_rate'] / 100;
			$package_gst[] = $gst;
			$total = $_POST['package_cost'][$i] + $gst;
			$package_totals[] = $total;
			$package_total_price += $total;
			$row = $_POST['package_row_id'][$i];
            $invoice_lines[] = "INSERT INTO `invoice_lines` (`invoiceid`, `item_id`, `description`, `category`, `quantity`, `unit_price`, `sub_total`, `gst`, `total`)
				VALUES ('INVOICEID', '$package', '".$details['category'].': '.$details['heading']."', 'package', '1', '".$_POST['package_cost'][$i]."', '".$_POST['package_cost'][$i]."', '$gst', '$total')";
			foreach($_POST['insurer_row_applied'] as $j => $match_row) {
				$applied = 0;
				if($row == $match_row && $_POST['insurer_payment_amt'][$j] != 0) {
					$applied = $_POST['insurer_payment_amt'][$j];

					//Invoice Insurer Portion
					$gst_insurer = ($applied == $total ? $gst : 0);//round($applied / $_POST['package_cost'][$i] * $gst,2);
					//$applied += $gst_insurer;
					$invoice_insurer[] = [ 'insurer' => $_POST['insurerid'][$j],
						'sub_total' => $applied - $gst_insurer,
						'gst_amt' => $gst_insurer,
						'price' => $applied,
						'service_cat' => 'Package',
						'service_name' => $details['heading'],
						'product_name' => '',
						'paid' => $paid ];

					$gst -= $gst_insurer;
					$total -= $applied;
					$list_package_insures[$_POST['insurerid'][$j]] .= $details['category'].' : '.$details['heading'].'<br>';
					$package_insurer_price[$_POST['insurerid'][$j]] += $applied;
					//$insurers[$_POST['insurerid'][$i]] += $gst_insurer;
					$package_insurer[$i] .= $_POST['insurerid'][$j].':'.$applied.'#*#';
				}
			}
			$promo_applied = 0;
			if($promo_value > 0 && $total > 0) {
				if($promo_value > $total) {
					$promo_applied = $total;
				} else {
					$promo_applied = $promo_value;
				}
			}
			$promo_value -= $promo_applied;
			$total -= $promo_applied;
			$package_promo .= $promo_applied.',';
			foreach($payment_used as $j => $amt_unused) {
				$applied = 0;
				if($total > 0 && $amt_unused > 0) {
					if($total > $amt_unused) {
						$applied = $amt_unused;
					} else {
						$applied = $total;
					}
					$payment_used[$j] -= $applied;

					//Invoice Patient Portion
					$gst_patient = round($applied / $total * $gst,2);
					if($gst < $gst_patient) {
						$gst_patient = $gst;
					}
					$invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
						'gst_amt' => $gst_patient,
						'gratuity' => 0,
						'price' => $applied,
						'service_cat' => 'Package',
						'service_name' => $details['heading'],
						'product_name' => '',
						'paid' => $payment_types[$j] ];

					$gst -= $gst_patient;
					$total -= $applied;
					$package_patient_price += $applied;
				}

				if($payment_types[$j] == 'Pro-Bono') {
					$package_pro_bono .= $applied.',';
				} else {
					$package_patient[$i] += $applied;
				}
			}
		}
	}

	$inv_ids = [];
	$inv_types = [];
	$inv_names = [];
	$inv_prices = [];
	$inv_gst = [];
	$inv_totals = [];
	$inv_qtys = [];
	$inv_patient_type = [];
	$inv_insurer_id = [];
	$inv_patient = [];
	$inv_insurer = [];
    //Delete previous records before saving the updated ones
    $invoice_lines_delete[] = "DELETE FROM `invoice_lines` WHERE `invoiceid`='INVOICEID' AND `category`='inventory'";
	foreach($_POST['inventoryid'] as $i => $inv) {
		if($inv > 0) {
			$inv_insurer[$i] = '';
			$inventory = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='$inv'"));
			$list_inventory_patient .= $inventory['name'].'<br>';
			$inv_ids[] = $inv;
			$inv_types[] = $_POST['invtype'][$i];
			$inv_names[] = $inventory['name'][$i];
			$inv_qtys[] = $_POST['quantity'][$i];
			$inv_prices[] = $_POST['sell_price'][$i];
			$gst = ($_POST['inventory_gst_exempt'][$i] == "1" ? 0 : $_POST['sell_price'][$i] * $_POST['tax_rate'] / 100);
			$inv_gst[] = $gst;
			$total = $_POST['sell_price'][$i] + $gst;
			$inv_totals[] = $total;
			$inv_total_price += $total;
			$row = $_POST['inventory_row_id'][$i];
			$invoice_lines[] = "INSERT INTO `invoice_lines` (`invoiceid`, `item_id`, `description`, `category`, `quantity`, `unit_price`, `sub_total`, `gst`, `total`)
				VALUES ('INVOICEID', '$inv', '".$inventory['name']."', 'inventory', '".$_POST['quantity'][$i]."', '".$_POST['unit_price'][$i]."', '".$_POST['sell_price'][$i]."', '$gst', '$total')";
			foreach($_POST['insurer_row_applied'] as $j => $match_row) {
				$applied = 0;
				if($row == $match_row && $_POST['insurer_payment_amt'][$j] != 0) {
					$applied = $_POST['insurer_payment_amt'][$j];

					//Invoice Insurer Portion
					$gst_insurer = ($applied == $total ? $gst : 0);//round($applied / $_POST['sell_price'][$i] * $gst,2);
					//$applied += $gst_insurer;
					$invoice_insurer[] = [ 'insurer' => $_POST['insurerid'][$j],
						'sub_total' => $applied - $gst_insurer,
						'gst_amt' => $gst_insurer,
						'price' => $applied,
						'service_cat' => '',
						'service_name' => '',
						'product_name' => $inventory['name'],
						'paid' => $paid ];

					$gst -= $gst_insurer;
					$total -= $applied;
					$list_inventory_insures[$_POST['insurerid'][$j]] .= $inventory['name'].'<br>';
					$inv_insurer_price[$_POST['insurerid'][$j]] += $applied;
					//$insurers[$_POST['insurerid'][$i]] += $gst_insurer;
					$inv_insurer[$i] .= $_POST['insurerid'][$j].':'.$applied.'#*#';
				}
			}

            //Update Inventory quantity
            if ( $invoice_mode != 'Saved' ) {
                $qty = filter_var($_POST['quantity'][$i], FILTER_SANITIZE_STRING);
                mysqli_query($dbc, "UPDATE `inventory` SET `quantity`=`quantity`-'$qty' WHERE `inventoryid`='$inv'");
            }

			$promo_applied = 0;
			if($promo_value > 0 && $total > 0) {
				if($promo_value > $total) {
					$promo_applied = $total;
				} else {
					$promo_applied = $promo_value;
				}
			}
			$promo_value -= $promo_applied;
			$total -= $promo_applied;
			$product_promo .= $promo_applied.',';
			foreach($payment_used as $j => $amt_unused) {
				$applied = 0;
				if($total > 0 && $amt_unused > 0) {
					if($total > $amt_unused) {
						$applied = $amt_unused;
					} else {
						$applied = $total;
					}
					$payment_used[$j] -= $applied;

					//Invoice Patient Portion
					$gst_patient = round($applied / $total * $gst,2);
					if($gst < $gst_patient) {
						$gst_patient = $gst;
					}
					$invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
						'gst_amt' => $gst_patient,
						'gratuity' => 0,
						'price' => $applied,
						'service_cat' => '',
						'service_name' => '',
						'product_name' => $inventory['name'],
						'paid' => $payment_types[$j] ];

					$gst -= $gst_patient;
					$total -= $applied;
					$inv_patient_price += $applied;
				}

				if($payment_types[$j] == 'Pro-Bono') {
					$product_pro_bono .= $applied.',';
				} else {
					$inv_patient[$i] += $applied;
				}
			}
		}
	}

	$misc_names = [];
	$misc_unit_prices = [];
	$misc_qtys = [];
	$misc_prices = [];
	$misc_gst = [];
	$misc_totals = [];
	$misc_insurer_id = [];
	$misc_patient = [];
	$misc_insurer = [];
    //Delete previous records before saving the updated ones
    $invoice_lines_delete[] = "DELETE FROM `invoice_lines` WHERE `invoiceid`='INVOICEID' AND `category`='misc product'";
	foreach($_POST['misc_item'] as $i => $misc) {
		if($misc != '') {
			$misc_insurer[$i] = '';
			$list_misc_patient .= $misc.'<br>';
			$misc_names[] = $misc;
			$misc_unit_prices[] = $_POST['misc_price'][$i];
			$misc_qtys[] = $_POST['misc_qty'][$i];
			$misc_prices[] = $_POST['misc_total'][$i];
			$gst = $_POST['misc_total'][$i] * $_POST['tax_rate'] / 100;
			$misc_gst[] = $gst;
			$total = $_POST['misc_total'][$i] + $gst;
			$misc_totals[] = $total;
			$misc_total_price += $total;
			$row = $_POST['misc_row_id'][$i];
			$invoice_lines[] = "INSERT INTO `invoice_lines` (`invoiceid`, `description`, `category`, `quantity`, `unit_price`, `sub_total`, `gst`, `total`)
				VALUES ('INVOICEID', '".$misc."', 'misc product', '".$_POST['misc_qty'][$i]."', '".$_POST['misc_price'][$i]."', '".$_POST['misc_total'][$i]."', '$gst', '$total')";
			foreach($_POST['insurer_row_applied'] as $j => $match_row) {
				$applied = 0;
				if($row == $match_row && $_POST['insurer_payment_amt'][$j] != 0) {
					$applied = $_POST['insurer_payment_amt'][$j];

					//Invoice Insurer Portion
					$gst_insurer = ($applied == $total ? $gst : 0);//round($applied / $_POST['sell_price'][$i] * $gst,2);
					//$applied += $gst_insurer;
					$invoice_insurer[] = [ 'insurer' => $_POST['insurerid'][$j],
						'sub_total' => $applied - $gst_insurer,
						'gst_amt' => $gst_insurer,
						'price' => $applied,
						'service_cat' => '',
						'service_name' => '',
						'product_name' => 'Miscellaneous: '.$misc,
						'paid' => $paid ];

					$gst -= $gst_insurer;
					$total -= $applied;
					$list_misc_insures[$_POST['insurerid'][$j]] .= 'Miscellaneous: '.$misc.'<br>';
					$misc_insurer_price[$_POST['insurerid'][$j]] += $applied;
					//$insurers[$_POST['insurerid'][$i]] += $gst_insurer;
					$misc_insurer[$i] .= $_POST['insurerid'][$j].':'.$applied.'#*#';
				}
			}
			$promo_applied = 0;
			if($promo_value > 0 && $total > 0) {
				if($promo_value > $total) {
					$promo_applied = $total;
				} else {
					$promo_applied = $promo_value;
				}
			}
			$promo_value -= $promo_applied;
			$total -= $promo_applied;
			$misc_promo .= $promo_applied.',';
			foreach($payment_used as $j => $amt_unused) {
				$applied = 0;
				if($total > 0 && $amt_unused > 0) {
					if($total > $amt_unused) {
						$applied = $amt_unused;
					} else {
						$applied = $total;
					}
					$payment_used[$j] -= $applied;

					//Invoice Patient Portion
					$gst_patient = round($applied / $total * $gst,2);
					if($gst < $gst_patient) {
						$gst_patient = $gst;
					}
					$invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
						'gst_amt' => $gst_patient,
						'gratuity' => 0,
						'price' => $applied,
						'service_cat' => '',
						'service_name' => '',
						'product_name' => 'Miscellaneous: '.$misc,
						'paid' => $payment_types[$j] ];

					$gst -= $gst_patient;
					$total -= $applied;
					$misc_patient_price += $applied;
				}

				if($payment_types[$j] == 'Pro-Bono') {
					$misc_pro_bono .= $applied.',';
				} else {
					$misc_patient[$i] += $applied;
				}
			}
		}
	}

	if($gratuity > 0) {
		$invoice_patient[] = [ 'sub_total' => 0,
			'gst_amt' => 0,
			'gratuity' => $gratuity,
			'price' => $gratuity,
			'service_cat' => '',
			'service_name' => '',
			'product_name' => 'Gratuity',
			'paid' => $payment_types[$j] ];
	}

	if($delivery > 0) {
		$invoice_patient[] = [ 'sub_total' => $delivery,
			'gst_amt' => $delivery * $_POST['tax_rate'] / 100,
			'gratuity' => 0,
			'price' => $delivery + $delivery * $_POST['tax_rate'] / 100,
			'service_cat' => '',
			'service_name' => '',
			'product_name' => 'Delivery: '.$delivery_type,
			'paid' => $payment_types[$j] ];
	}

	if($assembly > 0) {
		$invoice_patient[] = [ 'sub_total' => $assembly,
			'gst_amt' => $assembly * $_POST['tax_rate'] / 100,
			'gratuity' => 0,
			'price' => $assembly + $assembly * $_POST['tax_rate'] / 100,
			'service_cat' => '',
			'service_name' => '',
			'product_name' => 'Assembly',
			'paid' => $payment_types[$j] ];
	}

	$serviceid = implode(',', $services).',';
	$fee = implode(',', $service_fees).',';
	$fee_total = array_sum($service_fees);
	foreach($inv_qtys as $i => $cur_qty) {
		if(!$inv_ids[$i] > 0) {
			unset($inv_ids[$i]);
			unset($inv_prices[$i]);
			unset($inv_types[$i]);
			unset($inv_qtys[$i]);
		}
	}
	$inventoryid = implode(',', $inv_ids).',';
	$sell_price = implode(',', $inv_prices).',';
	$invtype = implode(',', $inv_types).',';
	$quantity = implode(',', $inv_qtys).',';
	$packageid = implode(',', $packages).',';
	$package_cost = implode(',', $package_fees).',';
	$misc_item = filter_var(implode(',', $misc_names).',',FILTER_SANITIZE_STRING);
	$misc_price = implode(',', $misc_prices).',';
	$misc_qty = implode(',', $misc_qtys).',';
	$misc_total = implode(',', $misc_totals).',';
	$payment_type = implode(',', $payment_types).'#*#'.implode(',',$payment_amts);
	$insurerid = '';
	$insurance_payment = '';
	foreach($insurers as $ins_id => $ins_amt) {
		$insurerid .= $ins_id.',';
		$insurance_payment .= $ins_amt.',';
	}

	$total_price = $_POST['total_price'];
	$credit_balance = $_POST['credit_balance'];
	$final_price = $_POST['final_price'];

    $discount_amt = 0;
    $discount_type = filter_var($_POST['discount_type'],FILTER_SANITIZE_STRING);
    $discount_value = filter_var($_POST['discount_value'],FILTER_SANITIZE_STRING);
    if ($discount_value > 0) {
        if ($discount_type=='%') {
            $discount_amt = $total_price * ($discount_value/100);
        } else {
            $discount_amt = $discount_value;
        }
    }
    $discount = number_format($discount_amt, 2);

	if($credit_balance > 0) {
		$invoice_patient[] = [ 'sub_total' => 0,
			'gst_amt' => 0,
			'gratuity' => 0,
			'price' => $credit_balance,
			'service_cat' => '',
			'service_name' => '',
			'product_name' => 'Credit on Account',
			'paid' => $payment_types[$j] ];
	}

	$bookingid = 0;
	if(empty($_POST['invoiceid'])) {
		if($_POST['invoice_type'] == 'Non Patient') {
			$patientid = 0;
			$therapistsid = 0;
			$injuryid = 0;
			$first_name = $_POST['first_name'];
			$last_name = $_POST['last_name'];
			$email = $_POST['email'];
		} else {
			$patientid = $_POST['patientid'];
			$therapistsid = $_POST['therapistsid'];
			$injuryid = $_POST['injuryid'];
		}
		$mva_claim_price = get_all_from_injury($dbc, $injuryid, 'mva_claim_price');

		$all_af = implode(',', $service_admin_fees);

		$service_patient = implode(',',$service_patient).',';
		$product_patient = implode(',',$inv_patient).',';
		$package_patient = implode(',',$package_patient).',';
		$misc_patient = implode(',',$misc_patient).',';
		$service_ins = implode(',',$service_insurer).',';
		$product_ins = implode(',',$inv_insurer).',';
		$package_ins = implode(',',$package_insurer).',';
		$misc_ins = implode(',',$misc_insurer).',';
		//$gst_amt = $final_price + $promo_total - $gratuity - $credit_balance - $delivery - $total_price;
        $gst_amt = $final_price + $promo_total + $discount - $gratuity - $delivery - $assembly - $total_price;
		$query_insert_invoice = "INSERT INTO `invoice` (`type`, `invoice_type`, `injuryid`, `patientid`, `therapistsid`, `serviceid`, `fee`, `admin_fee`, `service_patient`, `service_insurer`, `service_pro_bono`, `service_promo`, `inventoryid`, `sell_price`, `invtype`, `quantity`, `inventory_patient`, `inventory_insurer`, `inventory_pro_bono`, `inventory_promo`, `packageid`, `package_cost`, `package_patient`, `package_insurer`, `package_pro_bono`, `package_promo`, `misc_item`, `misc_price`, `misc_qty`, `misc_total`, `misc_patient`, `misc_insurer`, `misc_promo`, `misc_pro_bono`, `total_price`, `gst_amt`, `gratuity`, `credit_balance`, `delivery`, `delivery_type`, `delivery_address`, `contractorid`, `assembly`, `created_by`, `discount`, `final_price`, `pro_bono`, `insurerid`, `insurance_payment`, `paid`, `payment_type`, `pricing`, `service_date`, `invoice_date`, `ship_date`, `survey`, `request_recommend`, `follow_up_email`, `promotionid`, `giftcardid`, `comment`)
			VALUES ('$type_type', '$invoice_mode', '$injuryid', '$patientid', '$therapistsid', '$serviceid', '$fee', '$all_af', '$service_patient', '$service_ins', '$service_pro_bono', '$service_promo', '$inventoryid', '$sell_price', '$invtype', '$quantity', '$product_patient', '$product_ins', '$product_pro_bono', '$product_promo', '$packageid', '$package_cost', '$package_patient', '$package_ins', '$package_pro_bono', '$package_promo', '$misc_item', '$misc_price', '$misc_qty', '$misc_total', '$misc_patient', '$misc_ins', '$misc_promo', '$misc_pro_bono', '$total_price', '$gst_amt', '$gratuity', '$credit_balance', '$delivery', '$delivery_type', '$delivery_address', '$contractorid', '$assembly', '$created_by', '$discount', '$final_price', '$pro_bono', '$insurerid', '$insurance_payment', '$paid', '$payment_type', '$pricing', '$service_date', '$today_date', '$ship_date', '".$_POST['survey']."', '".$_POST['request_recommendation']."', '".$_POST['follow_up_assessment_email']."', '$promotionid', '$giftcardid', '$comment')";
        $result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
		$invoiceid = mysqli_insert_id($dbc);

		if($_POST['invoice_type'] == 'Non Patient') {
			$query_insert_invoice = "INSERT INTO `invoice_nonpatient` (`invoiceid`, `first_name`, `last_name`, `email`) VALUES ('$invoiceid', '$first_name', '$last_name', '$email')";
			$result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
		}

		$service = '';
		$assessment = '';
		$service_all = '';
		for($i=0; $i<count($_POST['serviceid']); $i++) {
			$serviceid = $_POST['serviceid'][$i];
			$fee = $_POST['fee'][$i];

			if($serviceid != '') {

				$service = get_all_from_service($dbc, $serviceid, 'category');
				$service_type = get_all_from_service($dbc, $serviceid, 'service_type');

				$service_all .= get_all_from_service($dbc, $serviceid, 'service_code').' : '.get_all_from_service($dbc, $serviceid, 'service_type').'<br>';

			}
		}
	} else {
		$invoiceid = $_POST['invoiceid'];
		$mva_claim_price = $_POST['mva_claim_price'];
		$bookingid = get_all_from_invoice($dbc, $invoiceid, 'bookingid');
		$injuryid = get_all_from_invoice($dbc, $invoiceid, 'injuryid');
		$invoice_date = get_all_from_invoice($dbc, $invoiceid, 'invoice_date');
		$service_date = get_all_from_invoice($dbc, $invoiceid, 'service_date');
		$therapistsid = $_POST['therapistsid'];
		if($paid == 'Yes') {
			$follow_up_call_status = 'Paid';
		} else {
			$follow_up_call_status = 'Invoiced';
		}

		$query_update_booking = "UPDATE `booking` SET `follow_up_call_status` = '$follow_up_call_status' WHERE `bookingid` = '$bookingid'";
		$result_update_booking = mysqli_query($dbc, $query_update_booking);

		$calid = get_calid_from_bookingid($dbc, $bookingid);
		$query_update_cal = "UPDATE `mrbs_entry` SET `patientstatus` = '$follow_up_call_status' WHERE `id` = '$calid'";
		$result_update_cal = mysqli_query($dbc, $query_update_cal);

		$all_af = '';
		foreach($_POST['serviceid'] as $sid) {
			$result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT r.admin_fee FROM services s, company_rate_card r WHERE s.serviceid='$sid' AND s.serviceid = r.item_id AND r.`tile_name` LIKE 'Services' AND '$service_date' >= r.start_date AND ('$service_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')"));
			$all_af .= $result['admin_fee'].',';
		}
		foreach($_POST['payment_type'] as $type) {
			$label = preg_replace('/[^a-z0-9\[\]]/', '_', strtolower($type));
			if($label == 'pro_bono') {
				$pro_bono += array_sum($_POST['service_pro_bono_payment_type']) + array_sum($_POST['product_pro_bono_payment_type']);
			}
			$service_patient_pays[] = implode(',',$_POST['service_'.$label.'_payment_type']);
			$product_patient_pays[] = implode(',',$_POST['product_'.$label.'_payment_type']);
		}
		foreach($_POST['insurerid'] as $ins_id) {
			$service_ins_pays[] = implode(',',$_POST['service_'.$ins_id.'_insurerid']);
			$product_ins_pays[] = implode(',',$_POST['product_'.$ins_id.'_insurerid']);
		}

		$service_pro_bono = implode(',',$_POST['service_pro_bono_payment_type']);
		$product_pro_bono = implode(',',$_POST['product_pro_bono_payment_type']);
		$service_promo = implode(',',$_POST['service_promotion_line']);
		$product_promo = implode(',',$_POST['product_promotion_line']);
		//$service_patient = implode('#*#',$service_patient_pays);
		//$product_patient = implode('#*#',$product_patient_pays);
		//$service_ins = implode('#*#',$service_ins_pays);
		//$product_ins = implode('#*#',$product_ins_pays);
		$n = 0;

		$calid = get_calid_from_bookingid($dbc, $bookingid);

		foreach($_POST['payment_type'] as $type) {
			$label = preg_replace('/[^a-z0-9\[\]]/', '_', strtolower($type));
			if($label == 'pro_bono') {
				$pro_bono += array_sum($_POST['service_pro_bono_payment_type']) + array_sum($_POST['product_pro_bono_payment_type']);
			}
			$service_patient_pays[] = implode(',',$_POST['service_'.$label.'_payment_type']);
			$product_patient_pays[] = implode(',',$_POST['product_'.$label.'_payment_type']);
		}
		foreach($_POST['insurerid'] as $ins_id) {
			$service_ins_pays[] = implode(',',$_POST['service_'.$ins_id.'_insurerid']);
			$product_ins_pays[] = implode(',',$_POST['product_'.$ins_id.'_insurerid']);
		}

		$service_pro_bono = implode(',',$_POST['service_pro_bono_payment_type']);
		$product_pro_bono = implode(',',$_POST['product_pro_bono_payment_type']);
		$service_promo = implode(',',$_POST['service_promotion_line']);
		$product_promo = implode(',',$_POST['product_promotion_line']);
		//$service_patient = implode('#*#',$service_patient_pays);
		//$product_patient = implode('#*#',$product_patient_pays);
		//$service_ins = implode('#*#',$service_ins_pays);
		//$product_ins = implode('#*#',$product_ins_pays);
		$service_patient = implode(',',$service_patient).',';
		$product_patient = implode(',',$inv_patient).',';
		$package_patient = implode(',',$package_patient).',';
		$misc_patient = implode(',',$misc_patient).',';
		$service_ins = implode(',',$service_insurer).',';
		$product_ins = implode(',',$inv_insurer).',';
		$package_ins = implode(',',$package_insurer).',';
		$misc_ins = implode(',',$misc_insurer).',';
		$gst_amt = $final_price + $promo_total + $discount - $gratuity - $delivery - $assembly - $total_price;
		$query_update_invoice = "UPDATE `invoice` SET `type`='$type_type', `invoice_type`='$invoice_mode', `service_date`='$service_date', `therapistsid`='$therapistsid', `serviceid` = '$serviceid', `fee` = '$fee', `admin_fee` = '$all_af', `service_patient`='$service_patient', `service_insurer`='$service_ins', `service_pro_bono`='$service_pro_bono', `service_promo`='$service_promo', `inventoryid` = '$inventoryid', `invtype` = '$invtype', `sell_price` = '$sell_price', `quantity`='$quantity', `inventory_patient`='$product_patient', `inventory_insurer`='$product_ins', `inventory_pro_bono`='$product_pro_bono', `packageid` = '$packageid', `package_cost` = '$package_cost', `package_patient`='$package_patient', `package_insurer`='$package_ins', `misc_item`='$misc_item', `misc_price`='$misc_price', `misc_qty`='$misc_qty', `misc_total`='$misc_total', `misc_patient`='$misc_patient', `misc_insurer`='$misc_ins', `misc_promo`='$misc_promo', `misc_pro_bono`='$misc_pro_bono', `total_price` = '$total_price', `gst_amt` = '$gst_amt', `delivery`='$delivery', `assembly`='$assembly', `gratuity` = '$gratuity', `discount`='$discount', `final_price` = '$final_price', `pro_bono`='$pro_bono', `insurerid` = '$insurerid', `insurance_payment` = '$insurance_payment', `paid` = '$paid', `ship_date`='$ship_date', `invoice_date`='$today_date', `payment_type` = '$payment_type', `survey`='".$_POST['survey']."', `request_recommend`='".$_POST['request_recommendation']."', `follow_up_email`='".$_POST['follow_up_assessment_email']."', `promotionid` = '$promotionid', `giftcardid` = '$giftcardid' WHERE `invoiceid` = '$invoiceid'";

		$result_update_invoice = mysqli_query($dbc, $query_update_invoice);
		$patientid = $_POST['patientid'];
	}

    //Update Reference (similar to POS Basic. SEA Alberta is using this)
    if ( !empty($reference) ) {
        mysqli_query($dbc, "UPDATE `contacts` SET `referred_by`='$reference' WHERE `contactid`='$patientid'");
    }
        
    //Update promotion times_used
    if ( !empty($promotionid) ) {
        mysqli_query($dbc, "UPDATE `promotion` SET `times_used` = IF(ISNULL(`times_used`), 1, `times_used` + 1) WHERE `promotionid`='$promotionid'");
    }

	$query = mysqli_query($dbc,"DELETE FROM invoice_compensation WHERE `invoiceid`='$invoiceid'");
	$n = 0;

	//Delete any records before proceeding to insertion
    foreach ( $invoice_lines_delete as $query ) {
        mysqli_query($dbc, str_replace('INVOICEID',$invoiceid,$query));
    }

    if($invoice_type != 'Saved') {
		foreach($invoice_lines as $query) {
			mysqli_query($dbc, str_replace('INVOICEID',$invoiceid,$query));
		}
		$service_all = explode(',',$serviceid);
		$service_all_fee = explode(',',$fee);
		$service_all_pro_bono = explode(',',$service_pro_bono);
		foreach($service_all as $s_row => $sid) {
			if($sid != '') {
				$f = $service_all_fee[$s_row];
				$result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT r.admin_fee, s.gst_exempt FROM services s, company_rate_card r WHERE s.serviceid='$sid' AND s.serviceid = r.serviceid AND '$today_date' >= r.start_date AND ('$today_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')"));
				$af = $result['admin_fee'];
				$total_pro_bono_amt = $service_all_pro_bono[$s_row] - ($result['gst_exempt'] == 1 ? 0 : $f * $_POST['tax_rate'] / 100);
				if($f != $total_pro_bono_amt) {
					$f -= $total_pro_bono_amt;
					if($af > $f) {
						$af = $f;
					}
					$query_insert_invoice = "INSERT INTO `invoice_compensation` (`invoiceid`, `therapistsid`, `serviceid`, `fee`, `admin_fee`, `service_date`) VALUES ('$invoiceid', '$therapistsid', '$sid', '$f', '$af', '$today_date')";
					$result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
				}
				$n++;
			}
		}

		if($treatment_plan != '') {
			$query_update_invoice = "UPDATE `patient_injury` SET `treatment_plan` = '$treatment_plan' WHERE `injuryid` = '$injuryid'";
			$result_update_invoice = mysqli_query($dbc, $query_update_invoice);
		}

		$injury_type = get_all_from_injury($dbc, $injuryid, 'injury_type');
		$invoice_date = $invoice_report_date = get_all_from_invoice($dbc, $invoiceid, 'invoice_date');

		$result_delete_client = mysqli_query($dbc, "DELETE FROM `invoice_patient` WHERE `invoiceid` = '$invoiceid'");

		$on_account = 0;
		$patient_account = 0;
		$credit = 0;
		$all_payment_type = '';
		foreach($invoice_patient as $payment) {
			$all_payment_type .= $payment['paid'].',';
			mysqli_query($dbc, "INSERT INTO `invoice_patient` (`invoiceid`, `injury_type`, `invoice_date`, `patientid`, `sub_total`, `gst_amt`, `gratuity_portion`, `patient_price`, `service_category`, `service_name`, `product_name`, `paid`, `paid_date`) VALUES ('$invoiceid', '$injury_type', '$invoice_date', '$patientid', '".$payment['sub_total']."', '".$payment['gst_amt']."', '".$payment['gratuity']."', '".$payment['price']."', '".$payment['service_cat']."', '".$payment['service_name']."', '".$payment['product_name']."', '".$payment['paid']."', '$invoice_report_date')");

			if($payment['paid'] == 'On Account') {
				$on_account += $payment['price'];
                $inv_status = 'Posted';
			} else if($payment['paid'] == 'Patient Account') {
				$patient_account += $payment['price'];
			} else {
				$credit += $payment['price'];
			}
		}
		$receipt_payments = array_merge($receipt_payments, $invoice_patient);

		//Gift Cards
		$giftcard = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `pos_giftcards` WHERE `giftcard_number` = '".$_POST['gf_number']."'"));
		$giftcardid = $giftcard['posgiftcardsid'];
		$giftcard_used_value = $giftcard['value'] - $giftcard['used_value'];
		if($sub_total > $giftcard_used_value) {
			$giftcard_paid = $giftcard_used_value;
			$giftcard_used_value = $giftcard['value'];
		} else {
			$giftcard_paid = $sub_total - $giftcard_used_value;
			$giftcard_used_value += $sub_total;
		}
		mysqli_query($dbc, "UPDATE `pos_giftcards` SET `used_value` = '$giftcard_used_value' WHERE `posgiftcardsid` = '$giftcardid'");
		if ( $giftcard_paid>0 ) {
            mysqli_query($dbc, "INSERT INTO `invoice_patient` (`invoiceid`, `injury_type`, `invoice_date`, `patientid`, `sub_total`, `gst_amt`, `gratuity_portion`, `patient_price`, `service_category`, `service_name`, `product_name`, `paid`, `paid_date`) VALUES ('$invoiceid', '$injury_type', '$invoice_date', '$patientid', '$giftcard_paid', '0', '', '$giftcard_paid', '', '', '', 'Gift Card', '$invoice_report_date')");
        }
		//Gift Cards

		$result_delete_client = mysqli_query($dbc, "DELETE FROM `invoice_insurer` WHERE `invoiceid` = '$invoiceid'");
		foreach($invoice_insurer as $payment) {
			mysqli_query($dbc, "INSERT INTO `invoice_insurer` (`invoiceid`, `injury_type`, `invoice_date`, `insurerid`, `sub_total`, `gst_amt`, `insurer_price`, `service_category`, `service_name`, `product_name`, `paid`) VALUES ('$invoiceid', '$injury_type', '$invoice_date', '".$payment['insurer']."', '".$payment['sub_total']."', '".$payment['gst_amt']."', '".$payment['price']."', '".$payment['service_cat']."', '".$payment['service_name']."', '".$payment['product_name']."', '$paid')");
		}

		if($paid != 'Saved') {
			$result_delete_packages = mysqli_query($dbc, "DELETE FROM `contact_package_sold` WHERE `invoiceid` = '$invoiceid'");
			foreach($packages as $packageid) {
				$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `package` WHERE `packageid`='$packageid'"));
				foreach(explode('**',$details['assign_services']) as $package_detail) {
					$package_detail = explode('#', $package_detail);
					$service_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid`='".$package_detail[0]."'"));
					for($i = 0; $i < $package_detail[1]; $i++) {
						mysqli_query($dbc, "INSERT INTO `contact_package_sold` (`contactid`, `invoiceid`, `package_item_type`, `item_id`, `item_description`) VALUES ('$patientid', '$invoiceid', 'Service', '".$package_detail[0]."', '".$service_info['category'].': '.$service_info['heading']."')");
					}
				}
				foreach(explode('**',$details['assign_inventory']) as $package_detail) {
					$package_detail = explode('#', $package_detail);
					$inv_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='".$package_detail[0]."'"));
					for($i = 0; $i < $package_detail[1]; $i++) {
						mysqli_query($dbc, "INSERT INTO `contact_package_sold` (`contactid`, `invoiceid`, `package_item_type`, `item_id`, `item_description`) VALUES ('$patientid', '$invoiceid', 'Inventory', '".$package_detail[0]."', '".$inv_info['name']."')");
					}
				}
			}
		}

		if(strpos($all_payment_type, 'On Account') === FALSE) {
			$result_update_in = mysqli_query($dbc, "UPDATE `invoice` SET `patient_payment_receipt` = 1 WHERE `invoiceid`='$invoiceid'");

			$logo = get_config($dbc, 'invoice_logo');
			DEFINE('INVOICE_LOGO', $logo);

			include ('patient_payment_receipt_pdf.php');
		}

		if($credit_balance > 0) {
			$query_update_invoice = "UPDATE `contacts` SET `amount_credit` = amount_credit + '$credit_balance' WHERE `contactid` = '$patientid'";
			$result_update_invoice = mysqli_query($dbc, $query_update_invoice);
		}

		if($paid == 'Credit On Account') {
			$query_update_invoice = "UPDATE `contacts` SET `amount_credit` = amount_credit + '$credit' WHERE `contactid` = '$patientid'";
			$result_update_invoice = mysqli_query($dbc, $query_update_invoice);
		}

		if($on_account != 0) {
			$query_update_invoice = "UPDATE `contacts` SET `amount_credit` = amount_credit - '$on_account' WHERE `contactid` = '$patientid'";
			$result_update_invoice = mysqli_query($dbc, $query_update_invoice);
		}

		if($patient_account != 0) {
			$query_update_invoice = "UPDATE `contacts` SET `amount_credit` = amount_credit - '$patient_account' WHERE `contactid` = '$patientid'";
			$result_update_invoice = mysqli_query($dbc, $query_update_invoice);
		}
	}

} else {
	// Adjustment
    $refund_categories = [];
    $src_invoiceid = $_POST['invoiceid'];
	$src_invoice = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='$src_invoiceid'"));
    $src_invoice_date = $src_invoice['invoice_date'];
    $mva_claim_price = $_POST['mva_claim_price'];
    $injuryid = $src_invoice['injuryid'];
    $invoice_date = date('Y-m-d');
	$service_date = $src_invoice['service_date'];
    $therapistsid = $src_invoice['therapistsid'];
    $patientid = $src_invoice['patientid'];
	$today_date = date('Y-m-d');
	$invoice_report_date = $today_date;
	$paid = $_POST['paid'];
	$service_pdf = '';
	$invoice_type = $_POST['invoice_type'];
	$on_account = 0;
	$comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

	$invoice_lines = [];

	if($paid == 'Yes') {
		$follow_up_call_status = 'Paid';
	} else {
		$follow_up_call_status = 'Invoiced';
	}
	$appt_type = $_POST['app_type'];
	$bookingid = get_all_from_invoice($dbc, $src_invoiceid, 'bookingid');

	//Adjust the booking`
	$booking = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `booking` WHERE `bookingid`='$bookingid'"));
	if($appt_type != $booking['type']) {
		mysqli_query($dbc, "UPDATE `booking` SET `follow_up_call_status`='Cancelled' WHERE `bookingid`='$bookingid'");
		mysqli_query($dbc, "INSERT INTO `booking` (`today_date`, `patientid`, `injuryid`, `treatment_type`, `upload_document`, `upload_document_md5`, `therapistsid`, `appoint_date`, `end_appoint_date`, `notes`, `type`, `follow_up_call_date`, `follow_up_call_status`, `call_today`, `appoint_time`, `calid`, `block_booking`, `confirmation_email_date`, `confirmation_email_reply_date`, `reminder_email_date`, `reminder_email_reply_date`, `reactive_email_date`, `create_by`, `modified_by`)
			VALUES ('".date('Y-m-d')."', '".$booking['patientid']."', '".$booking['injuryid']."', '".$booking['treatment_type']."', '".$booking['upload_document']."', '".$booking['upload_document_md5']."', '".$booking['therapistsid']."', '".$booking['appoint_date']."', '".$booking['end_appoint_date']."', '".$booking['notes']."', '".$appt_type."', '".$booking['follow_up_call_date']."', '".$booking['follow_up_call_status']."', '".$booking['call_today']."', '".$booking['appoint_time']."', '".$booking['calid']."', '".$booking['block_booking']."', '".$booking['confirmation_email_date']."', '".$booking['confirmation_email_reply_date']."', '".$booking['reminder_email_date']."', '".$booking['reminder_email_reply_date']."', '".$booking['reactive_email_date']."', '".$booking['create_by']."', '".$_SESSION['contactid']."')");
	}

	//Refund Information
	$final_amount = 0;
	$total_amount = 0;
	$fee_total_price = 0;
	$fee_patient_price = 0;
	$fee_insurer_price = [];
	$inv_total_price = 0;
	$inv_patient_price = 0;
	$inv_insurer_price = [];
	$package_total_price = 0;
	$package_patient_price = 0;
	$package_insurer_price = [];

	$payment_types = [];
	$payment_amts = [];
	$payment_used = [];
	foreach($_POST['refund_type_amount'] as $i => $amt) {
		if($amt > 0) {
			$type = $_POST['refund_to_type'][$i];
			if($type == 'Pro-Bono') {
				$payment_types = array_merge([$type], $payment_types);
				$payment_amts = array_merge([$amt], $payment_amts);
				$payment_used = array_merge([$amt], $payment_used);
				$pro_bono -= $amt;
			} else {
				$payment_types[] = $type;
				$payment_amts[] = $amt;
				$payment_used[] = $amt;
			}
		}
	}

	$promotionid = $_POST['promotionid'];
	$promotion = '';
	if($promotionid != '') {
		$promotion .= 'Promotion : '.get_promotion($dbc, $promotionid, 'heading').' : $'.get_promotion($dbc, $promotionid, 'cost');
	}
	$promo_value = get_promotion($dbc, $promotionid, 'cost');

	$service_promo_lines = $_POST['service_promotion_line'];
	$product_promo_lines = $_POST['product_promotion_line'];
	$promo_name = '';

	if($promotionid > 0) {
		$promo_details = mysqli_fetch_array(mysqli_query($dbc,"SELECT promotionid, heading, cost FROM promotion WHERE `promotionid`='$promotionid'"));
		$promo_name = $promo_details['heading'];
	}

	//To be used to track the portion of each service and product that is paid by each source for refunds
	$invoice_patient = [];
	$invoice_insurer = [];
	$service_patient_pays = [];
	$product_patient_pays = [];
	$package_patient_pays = [];
	$service_ins_pays = [];
	$product_ins_pays = [];
	$package_ins_pays = [];
	$service_pro_bono = '';
	$product_pro_bono = '';
	$package_pro_bono = '';
	$service_promo = '';
	$product_promo = '';
	$package_promo = '';
	$pro_bono = 0;

	$insurers = [];
    if ( isset($_POST['insurer_payment_amt']) ) {
        foreach($_POST['insurer_payment_amt'] as $i => $ins_amt) {
            if($ins_amt < 0) {
                $insurers[$_POST['insurerid'][$i]] += $ins_amt;
            }
        }
    }

	$services = [];
	$service_cats = [];
	$service_names = [];
	$service_fees = [];
	$service_gst = [];
	$service_admin_fees = [];
	$service_totals = [];
	$service_patient_type = [];
	$service_insurer_id = [];
	$service_patient = [];
	$service_insurer = [];
	if ( isset($_POST['init_serviceid']) ) {
        foreach($_POST['init_serviceid'] as $i => $sid) {
            $row = $_POST['init_service_row_id'][$i];
            if($sid > 0 && in_array($row, $_POST['servicerow_refund'])) {
                $service_insurer[$i] = '';
                $service = mysqli_fetch_array(mysqli_query($dbc, "SELECT s.category, s.heading, r.cust_price service_rate, r.admin_fee FROM services s, company_rate_card r WHERE s.serviceid='$sid' AND s.serviceid = r.item_id AND r.tile_name LIKE 'Services' AND '$today_date' >= r.start_date AND ('$today_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')"));
                $services[] = $sid;
                $service_pdf .= $service['category'].' : '.$service['heading'].'<br>';
                $service_cats[] = $service['category'];
                $service_names[] = $service['heading'];
                $service_fees[] = (-$service['service_rate']);
                $gst = $_POST['init_gst_exempt'][$i] == "1" ? 0 : (-$service['service_rate']) * $_POST['tax_rate'] / 100;
                $service_gst[] = $gst;
                $service_admin_fees[] = (-$service['admin_fee']);
                $fee = (-$service['service_rate']) + $gst;
                $fee_total_price += $fee;
                $service_totals[] = $fee;
                $total_amount += $fee - $gst;
                $final_amount += $fee;
                foreach($_POST['insurer_row_applied'] as $j => $match_row) {
                    $applied = 0;
                    if($row == $match_row && $_POST['insurer_payment_amt'][$j] != 0) {
                        $applied = $_POST['insurer_payment_amt'][$j];

                        //Invoice Insurer Portion
                        $gst_insurer = ($applied == $fee ? $gst : 0);//round($applied / (-$service['service_rate']) * $gst,2);
                        //$applied += $gst_insurer;
                        $invoice_insurer[] = [ 'insurer' => $_POST['insurerid'][$j],
                            'sub_total' => $applied - $gst_insurer,
                            'gst_amt' => $gst_insurer,
                            'price' => $applied,
                            'service_cat' => $service['category'],
                            'service_name' => $service['heading'],
                            'product_name' => '',
                            'paid' => $paid ];

                        $gst -= $gst_insurer;
                        $fee -= $applied;
                        $list_service_insurer[$_POST['insurerid'][$j]] .= $service['category'].' : '.$service['heading'].'<br>';
                        $fee_insurer_price[$_POST['insurerid'][$j]] += $applied;
                        //$insurers[$_POST['insurerid'][$i]] += $gst_insurer;
                        $service_insurer[$i] .= $_POST['insurerid'][$j].':'.$applied.'#*#';
                    }
                }
                foreach($payment_used as $j => $amt_unused) {
                    $applied = 0;
                    if($fee < 0 && $amt_unused > 0) {
                        if($fee * -1 > $amt_unused) {
                            $applied = -$amt_unused;
                        } else {
                            $applied = $fee;
                        }
                        $payment_used[$j] += $applied;

                        //Invoice Patient Portion
                        $gst_patient = round($applied / $fee * $gst,2);
                        if($gst > $gst_patient) {
                            $gst_patient = $gst;
                        }
                        $invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
                            'gst_amt' => $gst_patient,
                            'gratuity' => 0,
                            'price' => $applied,
                            'service_cat' => $service['category'],
                            'service_name' => $service['heading'],
                            'product_name' => '',
                            'paid' => $payment_types[$j] ];

                        $fee -= $applied;
                        $gst -= $gst_patient;
                        $fee_patient_price += $applied;
                    }

                    if($payment_types[$j] == 'Pro-Bono') {
                        $service_pro_bono .= $applied.',';
                    } else if($payment_types[$j] == 'On Account') {
                        $on_account += $applied;
                        $inv_status = 'Posted';
                    } else {
                        $service_patient[$i] += $applied;
                    }
                }
                if($fee < 0) {
                    $applied = 0;
                    $applied = $fee;
                    //$on_account -= $applied;

                    //Invoice Patient Portion
                    $gst_patient = round($applied / $fee * $gst,2);
                    if($gst < $gst_patient) {
                        $gst_patient = $gst;
                    }
                    $invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
                        'gst_amt' => $gst_patient,
                        'gratuity' => 0,
                        'price' => $applied,
                        'service_cat' => $service['category'],
                        'service_name' => $service['heading'],
                        'product_name' => '',
                        'paid' => 'On Account' ];
                    $service_patient[$i] += $applied;
                }
            } else if($sid > 0) {
                $service = mysqli_fetch_array(mysqli_query($dbc, "SELECT s.category, s.heading, r.cust_price service_rate, r.admin_fee FROM services s, company_rate_card r WHERE s.serviceid='$sid' AND s.serviceid = r.item_id AND r.tile_name LIKE 'Services' AND '$today_date' >= r.start_date AND ('$today_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')"));
                $fee = -$service['service_rate'];
                $gst = $_POST['init_gst_exempt'][$i] == "1" ? 0 : $fee * $_POST['tax_rate'] / 100;
                $refund_categories[] = [ 'sub_total' => $fee,
                    'gst_amt' => $gst,
                    'price' => $fee + $gst,
                    'refund' => 0,
                    'service_cat' => $service['category'],
                    'service_name' => $service['heading'],
                    'product_name' => '' ];
            }
        }
    }

	$packages = [];
	$package_name = [];
	$package_fees = [];
	$package_gst = [];
	$package_totals = [];
	$package_patient_type = [];
	$package_insurer_id = [];
	$package_patient = [];
	$package_insurer = [];
	if ( isset($_POST['init_packageid']) ) {
        foreach($_POST['init_packageid'] as $i => $package) {
            $row = $_POST['init_package_row_id'][$i];
            if($package > 0 && in_array($row, $_POST['packagerow_refund'])) {
                $package_insurer[$i] = '';
                $details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `package` WHERE `packageid`='$package'"));
                $list_package_patient .= $details['category'].' : '.$details['heading'].'<br>';
                $packages[] = $package;
                $package_name[] = $details['heading'];
                $package_fees[] = (-$_POST['package_cost'][$i]);
                $gst = $_POST['package_gst_exempt'][$i] == "1" ? 0 : (-$_POST['package_cost'][$i]) * $_POST['tax_rate'] / 100;
                $package_gst[] = $gst;
                $total = (-$_POST['package_cost'][$i]) + $gst;
                $package_totals[] = $total;
                $package_total_price += $total;
                $total_amount += $total - $gst;
                $final_amount += $total;
                foreach($_POST['insurer_row_applied'] as $j => $match_row) {
                    $applied = 0;
                    if($row == $match_row && $_POST['insurer_payment_amt'][$j] != 0) {
                        $applied = $_POST['insurer_payment_amt'][$j];

                        //Invoice Insurer Portion
                        $gst_insurer = ($applied == $total ? $gst : 0);//round($applied / (-$_POST['package_cost'][$i]) * $gst,2);
                        //$applied += $gst_insurer;
                        $invoice_insurer[] = [ 'insurer' => $_POST['insurerid'][$j],
                            'sub_total' => $applied - $gst_insurer,
                            'gst_amt' => $gst_insurer,
                            'price' => $applied,
                            'service_cat' => 'Package',
                            'service_name' => $details['heading'],
                            'product_name' => '',
                            'paid' => $paid ];

                        $gst -= $gst_insurer;
                        $total -= $applied;
                        $list_package_insures[$_POST['insurerid'][$j]] .= $details['category'].' : '.$details['heading'].'<br>';
                        $package_insurer_price[$_POST['insurerid'][$j]] += $applied;
                        //$insurers[$_POST['insurerid'][$i]] += $gst_insurer;
                        $package_insurer[$i] .= $_POST['insurerid'][$j].':'.$applied.'#*#';
                    }
                }
                foreach($payment_used as $j => $amt_unused) {
                    $applied = 0;
                    if($total < 0 && $amt_unused > 0) {
                        if($total * -1 > $amt_unused) {
                            $applied = -$amt_unused;
                        } else {
                            $applied = $total;
                        }
                        $payment_used[$j] += $applied;

                        //Invoice Patient Portion
                        $gst_patient = round($applied / $total * $gst,2);
                        if($gst > $gst_patient) {
                            $gst_patient = $gst;
                        }
                        $invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
                            'gst_amt' => $gst_patient,
                            'gratuity' => 0,
                            'price' => $applied,
                            'service_cat' => 'Package',
                            'service_name' => $details['heading'],
                            'product_name' => '',
                            'paid' => $payment_types[$j] ];

                        $total -= $applied;
                        $gst -= $gst_patient;
                        $package_total_price += $applied;
                    }

                    if($payment_types[$j] == 'Pro-Bono') {
                        $service_pro_bono .= $applied.',';
                    } else if($payment_types[$j] == 'On Account') {
                        $on_account += $applied;
                        $inv_status = 'Posted';
                    } else {
                        $package_patient[$i] += $applied;
                    }
                }
                if($total < 0) {
                    $applied = 0;
                    $applied = $total;
                    //$on_account -= $total;

                    //Invoice Patient Portion
                    $gst_patient = round($applied / $total * $gst,2);
                    if($gst < $gst_patient) {
                        $gst_patient = $gst;
                    }
                    $invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
                        'gst_amt' => $gst_patient,
                        'gratuity' => 0,
                        'price' => $applied,
                        'service_cat' => 'Package',
                        'service_name' => $details['heading'],
                        'product_name' => '',
                        'paid' => 'On Account' ];
                    $package_patient[$i] += $applied;
                }
            } else if($package > 0) {
                $details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `package` WHERE `packageid`='$package'"));
                $fee = -$_POST['package_cost'][$i];
                $gst = $_POST['package_gst_exempt'][$i] == "1" ? 0 : $fee * $_POST['tax_rate'] / 100;
                $refund_categories[] = [ 'sub_total' => $fee,
                    'gst_amt' => $gst,
                    'price' => $fee + $gst,
                    'refund' => 0,
                    'service_cat' => 'Package',
                    'service_name' => $details['heading'],
                    'product_name' => '' ];
            }
        }
    }

	$inv_ids = [];
	$inv_types = [];
	$inv_names = [];
	$inv_prices = [];
	$inv_gst = [];
	$inv_totals = [];
	$inv_qtys = [];
	$inv_patient_type = [];
	$inv_insurer_id = [];
	$inv_patient = [];
	$inv_insurer = [];
	foreach($_POST['inventoryid'] as $i => $inv) {
		if($inv > 0 && $_POST['sell_price'][$i] < 0) {
			$inv_insurer[$i] = '';
			$inventory = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='$inv'"));
			$list_inventory_patient .= $inventory['name'].'<br>';
			$inv_ids[] = $inv;
			$inv_types[] = $_POST['invtype'][$i];
			$inv_names[] = $inventory['name'][$i];
			$inv_qtys[] = $_POST['quantity'][$i];
			$inv_prices[] = $_POST['sell_price'][$i];
			$gst = ($_POST['inventory_gst_exempt'][$i] == "1" ? 0 : $_POST['sell_price'][$i] * $_POST['tax_rate'] / 100);
			$inv_gst[] = $gst;
			$total = $_POST['sell_price'][$i] + $gst;
			$inv_totals[] = $total;
			$inv_total_price += $total;
			$total_amount += $total - $gst;
			$final_amount += $total;
			$row = $_POST['inventory_row_id'][$i];
            $invoice_lines[] = "INSERT INTO `invoice_lines` (`invoiceid`, `description`, `category`, `quantity`, `unit_price`, `sub_total`, `gst`, `total`)
				VALUES ('INVOICEID', '".$inventory['name'][$i]."', 'inventory', '".$_POST['quantity'][$i]."', '".$_POST['unit_price'][$i]."', '".$_POST['sell_price'][$i]."', '$gst', '$total')";
			if ( isset($_POST['insurer_row_applied']) ) {
                foreach($_POST['insurer_row_applied'] as $j => $match_row) {
                    $applied = 0;
                    if($row == $match_row && $_POST['insurer_payment_amt'][$j] != 0) {
                        $applied = $_POST['insurer_payment_amt'][$j];

                        //Invoice Insurer Portion
                        $gst_insurer = ($applied == $total ? $gst : 0);//round($applied / $_POST['sell_price'][$i] * $gst,2);
                        //$applied += $gst_insurer;
                        $invoice_insurer[] = [ 'insurer' => $_POST['insurerid'][$j],
                            'sub_total' => $applied - $gst_insurer,
                            'gst_amt' => $gst_insurer,
                            'price' => $applied,
                            'service_cat' => '',
                            'service_name' => '',
                            'product_name' => $inventory['name'],
                            'paid' => $paid ];

                        $gst -= $gst_insurer;
                        $total -= $applied;
                        $list_inventory_insures[$_POST['insurerid'][$j]] .= $inventory['name'].'<br>';
                        $inv_insurer_price[$_POST['insurerid'][$j]] += $applied;
                        //$insurers[$_POST['insurerid'][$i]] += $gst_insurer;
                        $inv_insurer[$i] .= $_POST['insurerid'][$j].':'.$applied.'#*#';
                    }
                }
            }
			foreach($payment_used as $j => $amt_unused) {
				$applied = 0;
				if($total < 0 && $amt_unused > 0) {
					if($total * -1 > $amt_unused) {
						$applied = -$amt_unused;
					} else {
						$applied = $total;
					}
					$payment_used[$j] += $applied;

					//Invoice Patient Portion
					$gst_patient = round($applied / $total * $gst,2);
					if($gst > $gst_patient) {
						$gst_patient = $gst;
					}
					$invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
						'gst_amt' => $gst_patient,
						'gratuity' => 0,
						'price' => $applied,
						'service_cat' => '',
						'service_name' => '',
						'product_name' => $inventory['name'],
						'paid' => $payment_types[$j] ];

					$total -= $applied;
					$gst -= $gst_patient;
					$inv_total_price += $applied;
				}

				if($payment_types[$j] == 'Pro-Bono') {
					$service_pro_bono .= $applied.',';
				} else if($payment_types[$j] == 'On Account') {
					$on_account += $applied;
                    $inv_status = 'Posted';
				} else {
					$inv_patient[$i] += $applied;
				}
			}
			if($total < 0) {
				$applied = 0;
				$applied = $total;
				//$on_account -= $applied;

				//Invoice Patient Portion
				$gst_patient = round($applied / $total * $gst,2);
				if($gst < $gst_patient) {
					$gst_patient = $gst;
				}
				$invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
					'gst_amt' => $gst_patient,
					'gratuity' => 0,
					'price' => $applied,
					'service_cat' => '',
					'service_name' => '',
					'product_name' => $inventory['name'],
					'paid' => 'On Account' ];
				$inv_patient[$i] += $applied;
			}
		} else if($inv > 0) {
			$inventory = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='$inv'"));
			$fee = -$_POST['init_price'][$i];
			$gst = $_POST['inventory_gst_exempt'][$i] == "1" ? 0 : $fee * $_POST['tax_rate'] / 100;
			$refund_categories[] = [ 'sub_total' => $fee,
				'gst_amt' => $gst,
				'price' => $fee + $gst,
				'refund' => 0,
				'service_cat' => '',
				'service_name' => '',
				'product_name' => $inventory['name'] ];
		}

        //Update Inventory quantity
        if ( $invoice_mode!='Saved' && $inv > 0 ) {
            $qty = filter_var($_POST['quantity'][$i], FILTER_SANITIZE_STRING);
            mysqli_query($dbc, "UPDATE `inventory` SET `quantity`=`quantity`-'$qty' WHERE `inventoryid`='$inv'");
        }
	}

	$misc_names = [];
	$misc_unit_prices = [];
	$misc_qtys = [];
	$misc_prices = [];
	$misc_gst = [];
	$misc_totals = [];
	$misc_insurer_id = [];
	$misc_patient = [];
	$misc_insurer = [];
	foreach($_POST['misc_item'] as $i => $misc) {
		if($misc != '' && $_POST['misc_total'][$i] < 0) {
			$misc_insurer[$i] = '';
			$list_misc_patient .= $misc.'<br>';
			$misc_names[] = $misc;
			$misc_unit_prices[] = $_POST['misc_price'][$i];
			$misc_qtys[] = $_POST['misc_return'][$i];
			$misc_prices[] = $_POST['misc_total'][$i];
			$gst = $_POST['misc_total'][$i] * $_POST['tax_rate'] / 100;
			$misc_gst[] = $gst;
			$total = $_POST['misc_total'][$i] + $gst;
			$final_amount += $total;
			$misc_totals[] = $total;
			$misc_total_price += $total;
			$row = $_POST['misc_row_id'][$i];
			$invoice_lines[] = "INSERT INTO `invoice_lines` (`invoiceid`, `description`, `category`, `quantity`, `unit_price`, `sub_total`, `gst`, `total`)
				VALUES ('INVOICEID', '".$misc."', 'misc product', '".$_POST['misc_return'][$i]."', '".$_POST['misc_price'][$i]."', '".$_POST['misc_total'][$i]."', '$gst', '$total')";
			foreach($_POST['insurer_row_applied'] as $j => $match_row) {
				$applied = 0;
				if($row == $match_row && $_POST['insurer_payment_amt'][$j] != 0) {
					$applied = $_POST['insurer_payment_amt'][$j];

					//Invoice Insurer Portion
					$gst_insurer = ($applied == $total ? $gst : 0);//round($applied / $_POST['sell_price'][$i] * $gst,2);
					//$applied += $gst_insurer;
					$invoice_insurer[] = [ 'insurer' => $_POST['insurerid'][$j],
						'sub_total' => $applied - $gst_insurer,
						'gst_amt' => $gst_insurer,
						'price' => $applied,
						'service_cat' => '',
						'service_name' => '',
						'product_name' => 'Miscellaneous: '.$misc,
						'paid' => $paid ];

					$gst -= $gst_insurer;
					$total -= $applied;
					$list_misc_insures[$_POST['insurerid'][$j]] .= 'Miscellaneous: '.$misc.'<br>';
					$misc_insurer_price[$_POST['insurerid'][$j]] += $applied;
					//$insurers[$_POST['insurerid'][$i]] += $gst_insurer;
					$misc_insurer[$i] .= $_POST['insurerid'][$j].':'.$applied.'#*#';
				}
			}
			$promo_applied = 0;
			if($promo_value > 0 && $total > 0) {
				if($promo_value > $total) {
					$promo_applied = $total;
				} else {
					$promo_applied = $promo_value;
				}
			}
			$promo_value -= $promo_applied;
			$total -= $promo_applied;
			$misc_promo .= $promo_applied.',';
			foreach($payment_used as $j => $amt_unused) {
				$applied = 0;
				if($total > 0 && $amt_unused > 0) {
					if($total > $amt_unused) {
						$applied = $amt_unused;
					} else {
						$applied = $total;
					}
					$payment_used[$j] -= $applied;

					//Invoice Patient Portion
					$gst_patient = round($applied / $total * $gst,2);
					if($gst < $gst_patient) {
						$gst_patient = $gst;
					}
					$invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
						'gst_amt' => $gst_patient,
						'gratuity' => 0,
						'price' => $applied,
						'service_cat' => '',
						'service_name' => '',
						'product_name' => 'Miscellaneous: '.$misc,
						'paid' => $payment_types[$j] ];

					$gst -= $gst_patient;
					$total -= $applied;
					$misc_patient_price += $applied;
				}

				if($payment_types[$j] == 'Pro-Bono') {
					$misc_pro_bono .= $applied.',';
				} else {
					$misc_patient[$i] += $applied;
				}
			}
			if($total < 0) {
				$applied = 0;
				$applied = $total;
				//$on_account -= $applied;

				//Invoice Patient Portion
				$gst_patient = round($applied / $total * $gst,2);
				if($gst < $gst_patient) {
					$gst_patient = $gst;
				}
				$invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
					'gst_amt' => $gst_patient,
					'gratuity' => 0,
					'price' => $applied,
					'service_cat' => '',
					'service_name' => '',
					'product_name' => 'Miscellaneous: '.$misc,
					'paid' => 'On Account' ];
				$misc_patient[$i] += $applied;
			}
		} else if($misc != '' && isset($_POST['misc_return'])) {
			$fee = -$_POST['misc_price'][$i] * $_POST['misc_qty'][$i];
			$gst = $fee * $_POST['tax_rate'] / 100;
			$refund_categories[] = [ 'sub_total' => $fee,
				'gst_amt' => $gst,
				'price' => $fee + $gst,
				'refund' => 0,
				'service_cat' => '',
				'service_name' => '',
				'product_name' => 'Miscellaneous: '.$misc ];
		}
	}

	//Apply further refunds to the non-refunded items
	foreach($payment_used as $j => $amt_unused) {
		$applied = 0;
		if($amt_unused > 0) {
			foreach($refund_categories as $i => $refund_category) {
				$refund = $refund_category['price'] + $refund_category['refund'];
				$applied = ($refund < -$amt_unused ? -$amt_unused : $refund);
				//Invoice Patient Portion
				if($applied != 0) {
					$payment_used[$j] += $applied;
					$amt_unused += $applied;
					$refund_categories[$i]['refund'] -= $applied;
					$gst = round($refund_category['gst_amt'] * $applied / $refund_category['price'], 2);
					$total_amount += $applied - $gst;
					$final_amount += $applied;
					$invoice_patient[] = [ 'sub_total' => $applied - $gst,
						'gst_amt' => $gst,
						'gratuity' => 0,
						'price' => $applied,
						'service_cat' => $refund_category['service_cat'],
						'service_name' => $refund_category['service_name'],
						'product_name' => $refund_category['product_name'],
						'paid' => $payment_types[$j] ];
				}
			}
		}

		if($payment_types[$j] == 'On Account') {
			$on_account += $applied;
            $inv_status = 'Posted';
		}
	}

	//Record further payments as Refunds
	foreach($payment_used as $j => $amt_unused) {
		$applied = 0;
		if($amt_unused > 0) {
			$applied = -$amt_unused;
			$payment_used[$j] += $applied;
			$total_amount += $applied;
			$final_amount += $applied;
			//Invoice Patient Portion
			$invoice_patient[] = [ 'sub_total' => $applied,
				'gst_amt' => 0,
				'gratuity' => 0,
				'price' => $applied,
				'service_cat' => 'Refund',
				'service_name' => 'Refund',
				'product_name' => '',
				'paid' => $payment_types[$j] ];
		}

		if($payment_types[$j] == 'On Account') {
			$on_account += $applied;
            $inv_status = 'Posted';
		}
	}

	$serviceid = implode(',', $services).',';
	$fee = implode(',', $service_fees).',';
	$all_af = implode(',', $service_admin_fees).',';
	$fee_total = array_sum($service_fees);
	foreach($inv_qtys as $i => $cur_qty) {
		if(!$inv_ids[$i] > 0) {
			unset($inv_ids[$i]);
			unset($inv_prices[$i]);
			unset($inv_types[$i]);
			unset($inv_qtys[$i]);
		}
	}
	$inventoryid = implode(',', $inv_ids).',';
	$sell_price = implode(',', $inv_prices).',';
	$invtype = implode(',', $inv_types).',';
	$quantity = implode(',', $inv_qtys).',';
	$packageid = implode(',', $packages).',';
	$package_cost = implode(',', $package_fees).',';
	$misc_item = implode(',', $misc_names).',';
	$misc_price = implode(',', $misc_prices).',';
	$misc_qty = implode(',', $misc_qtys).',';
	$misc_total = implode(',', $misc_totals).',';
	$final_refunded = 0;
	$payment_type_names = '';
	$payment_type_amts = '';
	foreach($payment_amts as $j => $amt_applied) {
		$final_refunded += $amt_applied;
		$payment_type_names .= $payment_types[$j].',';
		$payment_type_amts .= -$amt_applied.',';
	}
	$insurerid = '';
	$insurance_payment = '';
	foreach($insurers as $ins_id => $ins_amt) {
		$final_refunded -= $ins_amt;
		$insurerid .= $ins_id.',';
		$insurance_payment .= $ins_amt.',';
	}
	if($final_refunded != -$final_amount) {
		$payment_type_names .= 'On Account,';
		$payment_type_amts .= $final_refunded + $final_amount;
        $inv_status = 'Posted';
	}
	$payment_type = trim($payment_type_names,',').'#*#'.trim($payment_type_amts,',');

	//Save Refund Information
	$refund_amount = $final_amount;
	if($refund_amount != 0) {
		$service_patient = implode(',',$service_patient).',';
		$product_patient = implode(',',$inv_patient).',';
		$package_patient = implode(',',$package_patient).',';
		$misc_patient = implode(',',$misc_patient).',';
		$service_ins = implode(',',$service_insurer).',';
		$product_ins = implode(',',$inv_insurer).',';
		$package_ins = implode(',',$package_insurer).',';
		$misc_ins = implode(',',$misc_insurer).',';
		$gst_amt = $final_amount - $total_amount;
		$refund_gst = $gst_amt;
		$created_by = $_SESSION['contactid'];

		$query_insert_invoice = "INSERT INTO `invoice` (`invoice_type`, `invoiceid_src`, `injuryid`, `patientid`, `therapistsid`, `serviceid`, `fee`, `admin_fee`, `service_patient`, `service_insurer`, `service_pro_bono`, `service_promo`, `inventoryid`, `sell_price`, `invtype`, `quantity`, `inventory_patient`, `inventory_insurer`, `inventory_pro_bono`, `inventory_promo`, `packageid`, `package_cost`, `package_patient`, `package_insurer`, `package_pro_bono`, `package_promo`, `misc_item`, `misc_price`, `misc_qty`, `misc_total`, `misc_patient`, `misc_insurer`, `misc_promo`, `misc_pro_bono`, `total_price`, `gst_amt`, `gratuity`, `created_by`, `final_price`, `pro_bono`, `insurerid`, `insurance_payment`, `paid`, `payment_type`, `service_date`, `invoice_date`, `promotionid`, `giftcardid`, `comment`)
			VALUES ('Refund', '$src_invoiceid', '$injuryid', '$patientid', '$therapistsid', '$serviceid', '$fee', '$all_af', '$service_patient', '$service_ins', '$service_pro_bono', '$service_promo', '$inventoryid', '$sell_price', '$invtype', '$quantity', '$product_patient', '$product_ins', '$product_pro_bono', '$product_promo', '$packageid', '$package_cost', '$package_patient', '$package_ins', '$package_pro_bono', '$package_promo', '$misc_item', '$misc_price', '$misc_qty', '$misc_total', '$misc_patient', '$misc_ins', '$misc_promo', '$misc_pro_bono', '$total_amount', '$gst_amt', '$gratuity', '$created_by', '$final_amount', '$pro_bono', '$insurerid', '$insurance_payment', '$paid', '$payment_type', '$service_date', '$today_date', '$promotionid', '$giftcardid', '$comment')";
		$result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
		$invoiceid = mysqli_insert_id($dbc);
		foreach($invoice_lines as $query) {
			mysqli_query($dbc, str_replace('INVOICEID',$invoiceid,$query));
		}
		//Refund Compensation
		foreach($services as $i => $sid) {
			$service_all_pro_bono = explode(',',$service_pro_bono);
			if($sid != '') {
				$f = $service_fees[$i];
				$result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT r.admin_fee, s.gst_exempt FROM services s, company_rate_card r WHERE s.serviceid='$sid' AND s.serviceid = r.item_id AND r.tile_name LIKE 'Services' AND '$today_date' >= r.start_date AND ('$today_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')"));
				$af = -$result['admin_fee'];
				$total_pro_bono_amt = $service_all_pro_bono[$i] - ($result['gst_exempt'] == 1 ? 0 : $f * $_POST['tax_rate'] / 100);
				if($f != $total_pro_bono_amt) {
					$f -= $total_pro_bono_amt;
					if($af < $f) {
						$af = $f;
					}
					$query_insert_invoice = "INSERT INTO `invoice_compensation` (`invoiceid`, `therapistsid`, `serviceid`, `fee`, `admin_fee`, `service_date`) VALUES ('$invoiceid', '$therapistsid', '$sid', '$f', '$af', '$today_date')";
					$result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
				}
			}
		}
		//Refund Packages
		foreach($packages as $packageid) {
			$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `package` WHERE `packageid`='$packageid'"));
			foreach(explode('**',$details['assign_services']) as $package_detail) {
				$package_detail = explode('#', $package_detail);
				for($i = 0; $i < $package_detail[1]; $i++) {
					$sold_package = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contact_package_sold` WHERE `invoiceid`='$src_invoiceid' AND `package_item_type`='Service' AND `item_id`='".$package_detail[0]."' AND `deleted`=0"))['package_sold_id'];
                        $date_of_archival = date('Y-m-d');

					mysqli_query($dbc, "UPDATE `contact_package_sold` SET `deleted`=1 WHERE `package_sold_id`='$sold_package'");
				}
			}
			foreach(explode('**',$details['assign_inventory']) as $package_detail) {
				$package_detail = explode('#', $package_detail);
				for($i = 0; $i < $package_detail[1]; $i++) {
					$sold_package = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contact_package_sold` WHERE `invoiceid`='$src_invoiceid' AND `package_item_type`='Inventory' AND `item_id`='".$package_detail[0]."' AND `deleted`=0"))['package_sold_id'];
                    $date_of_archival = date('Y-m-d');
					mysqli_query($dbc, "UPDATE `contact_package_sold` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `package_sold_id`='$sold_package'");
				}
			}
		}
		//Patient and Insurer Portions
		foreach($invoice_patient as $payment) {
			mysqli_query($dbc, "INSERT INTO `invoice_patient` (`invoiceid`, `injury_type`, `invoice_date`, `patientid`, `sub_total`, `gst_amt`, `gratuity_portion`, `patient_price`, `service_category`, `service_name`, `product_name`, `paid`, `paid_date`) VALUES ('$invoiceid', '$injury_type', '$invoice_date', '$patientid', '".$payment['sub_total']."', '".$payment['gst_amt']."', '".$payment['gratuity']."', '".$payment['price']."', '".$payment['service_cat']."', '".$payment['service_name']."', '".$payment['product_name']."', '".$payment['paid']."', '$invoice_report_date')");
		}
		$receipt_payments = array_merge($receipt_payments, $invoice_patient);
		foreach($invoice_insurer as $payment) {
			mysqli_query($dbc, "INSERT INTO `invoice_insurer` (`invoiceid`, `injury_type`, `invoice_date`, `insurerid`, `sub_total`, `gst_amt`, `insurer_price`, `service_category`, `service_name`, `product_name`, `paid`) VALUES ('$invoiceid', '$injury_type', '$invoice_date', '".$payment['insurer']."', '".$payment['sub_total']."', '".$payment['gst_amt']."', '".$payment['price']."', '".$payment['service_cat']."', '".$payment['service_name']."', '".$payment['product_name']."', '$paid')");
		}
	}
	//Save non-patient details
	if($patientid == 0) {
		mysqli_query($dbc, "INSERT INTO `invoice_nonpatient` (`invoiceid`, `first_name`, `last_name`, `email`) SELECT '$invoiceid', `first_name`, `last_name`, `email` FROM `invoice_nonpatient` WHERE `invoiceid`='$src_invoiceid'");
	}
	//Generate Receipt and Invoice for Refund
	if(strpos($all_payment_type, 'On Account') === FALSE) {
		$result_update_in = mysqli_query($dbc, "UPDATE `invoice` SET `patient_payment_receipt` = 1 WHERE `invoiceid`='$invoiceid'");

		$logo = get_config($dbc, 'invoice_logo');
		DEFINE('INVOICE_LOGO', $logo);

		//include ('patient_payment_receipt_pdf.php');
	}

	$get_invoice = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='$invoiceid'"));
	// PDF
	$invoice_design = get_config($dbc, 'invoice_design');
	switch($invoice_design) {
		case 1:
			include('pos_invoice_1.php');
			break;
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
			include ('pos_invoice_small.php');
			break;
		case 'service':
			include ('pos_invoice_service.php');
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
        default:
			include('pos_invoice_1.php');
			break;
	}

	//Adjustment Information
	$receipt_payments = [];
	$adjust_amount = 0;
	$final_amount = 0;
	$total_amount = 0;
	$gratuity = $_POST['gratuity'];
	$therapistsid = $_POST['therapistsid'];
	if(empty($service_date)) {
		$service_date = date('Y-m-d');
	}
	$comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);

	$promotionid = $_POST['promotionid'];
	$promotion = '';

	$invoice_lines = [];

	if($promotionid != '') {
		$promotion .= 'Promotion : '.get_promotion($dbc, $promotionid, 'heading').' : $'.get_promotion($dbc, $promotionid, 'cost');
	}
	$promo_total = $promo_value = get_promotion($dbc, $promotionid, 'cost');

	$service_promo_lines = $_POST['service_promotion_line'];
	$product_promo_lines = $_POST['product_promotion_line'];
	$promo_name = '';

	if($promotionid > 0) {
		$promo_details = mysqli_fetch_array(mysqli_query($dbc,"SELECT promotionid, heading, cost FROM promotion WHERE `promotionid`='$promotionid'"));
		$promo_name = $promo_details['heading'];
	}

	//To be used to track the portion of each service and product that is paid by each source for adjustments
	$invoice_patient = [];
	$invoice_insurer = [];
	$service_patient_pays = [];
	$product_patient_pays = [];
	$package_patient_pays = [];
	$service_ins_pays = [];
	$product_ins_pays = [];
	$package_ins_pays = [];
	$service_pro_bono = '';
	$product_pro_bono = '';
	$package_pro_bono = '';
	$service_promo = '';
	$product_promo = '';
	$package_promo = '';
	$pro_bono = 0;

	$payment_types = [];
	$payment_amts = [];
	$payment_used = [];
	if(-$refund_amount > $final_refunded) {
		$payment_types[] = 'On Account';
		$payment_amts[] = -$refund_amount - $final_refunded;
		$payment_used[] = -$refund_amount - $final_refunded;
        $inv_status = 'Posted';
	}
	foreach($_POST['payment_type'] as $i => $type) {
		if($type == 'Pro-Bono') {
			$payment_types = array_merge([$type], $payment_types);
			$payment_amts = array_merge([$_POST['payment_price'][$i]], $payment_amts);
			$payment_used = array_merge([$_POST['payment_price'][$i]], $payment_used);
			$pro_bono += $_POST['payment_price'][$i];
		} else {
			$payment_types[] = $type;
			$payment_amts[] = $_POST['payment_price'][$i];
			$payment_used[] = $_POST['payment_price'][$i];
		}
	}

	$insurers = [];
	foreach($_POST['insurer_payment_amt'] as $i => $ins_amt) {
		if($ins_amt > 0) {
			$insurers[$_POST['insurerid'][$i]] += $ins_amt;
		}
	}

	$services = [];
	$service_cats = [];
	$service_names = [];
	$service_fees = [];
	$service_gst = [];
	$service_admin_fees = [];
	$service_totals = [];
	$service_patient_type = [];
	$service_insurer_id = [];
	$service_patient = [];
	$service_insurer = [];
	foreach($_POST['serviceid'] as $i => $sid) {
		$row = $_POST['service_row_id'][$i];
		if($sid > 0) {
			$service_insurer[$i] = '';
			$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT s.category, s.heading, r.cust_price service_rate, r.admin_fee FROM services s, company_rate_card r WHERE s.serviceid='$sid' AND s.serviceid = r.item_id AND r.tile_name LIKE 'Services' AND '$today_date' >= r.start_date AND ('$today_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')"));
			$services[] = $sid;
			$service_pdf .= $service['category'].' : '.$service['heading'].'<br>';
			$service_cats[] = $service['category'];
			$service_names[] = $service['heading'];
			$service_fees[] = $service['service_rate'];
			$gst = $_POST['gst_exempt'][$i] == "1" ? 0 : $service['service_rate'] * $_POST['tax_rate'] / 100;
			$service_gst[] = $gst;
			$service_admin_fees[] = $service['admin_fee'];
			$fee = $service['service_rate'] + $gst;
			$fee_total_price += $fee;
			$service_totals[] = $fee;
			$adjust_amount += $fee;
			$total_amount += $fee - $gst;
			$final_amount += $fee;
			foreach($_POST['insurer_row_applied'] as $j => $match_row) {
				$applied = 0;
				if($row == $match_row && $_POST['insurer_payment_amt'][$j] != 0) {
					$applied = $_POST['insurer_payment_amt'][$j];

					//Invoice Insurer Portion
					$gst_insurer = ($applied == $total ? $gst : 0);//round($applied / $service['service_rate'] * $gst,2);
					//$applied += $gst_insurer;
					$invoice_insurer[] = [ 'insurer' => $_POST['insurerid'][$j],
						'sub_total' => $applied - $gst_insurer,
						'gst_amt' => $gst_insurer,
						'price' => $applied,
						'service_cat' => $service['category'],
						'service_name' => $service['heading'],
						'product_name' => '',
						'paid' => $paid ];

					$gst -= $gst_insurer;
					$fee -= $applied;
					$list_service_insurer[$_POST['insurerid'][$j]] .= $service['category'].' : '.$service['heading'].'<br>';
					$fee_insurer_price[$_POST['insurerid'][$j]] += $applied;
					//$insurers[$_POST['insurerid'][$i]] += $gst_insurer;
					$service_insurer[$i] .= $_POST['insurerid'][$j].':'.$applied.'#*#';
				}
			}
			if($fee > $on_account) {
				$fee -= $on_account;
				$on_account = 0;
			} else if($on_account > 0) {
				$fee = 0;
				$on_account -= $fee;
			}
			foreach($payment_used as $j => $amt_unused) {
				$applied = 0;
				if($fee > 0 && $amt_unused > 0) {
					if($fee > $amt_unused) {
						$applied = $amt_unused;
					} else {
						$applied = $fee;
					}
					$payment_used[$j] -= $applied;

					//Invoice Patient Portion
					$gst_patient = round($applied / $fee * $gst,2);
					if($gst < $gst_patient) {
						$gst_patient = $gst;
					}
					$invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
						'gst_amt' => $gst_patient,
						'gratuity' => 0,
						'price' => $applied,
						'service_cat' => $service['category'],
						'service_name' => $service['heading'],
						'product_name' => '',
						'paid' => $payment_types[$j] ];

					$fee -= $applied;
					$gst -= $gst_patient;
					$fee_patient_price += $applied;
				}

				if($payment_types[$j] == 'Pro-Bono') {
					$service_pro_bono .= $applied.',';
				} else if($payment_types[$j] == 'On Account') {
					$on_account -= $applied;
				} else {
					$service_patient[$i] += $applied;
				}
			}
		}
	}

	$packages = [];
	$package_name = [];
	$package_fees = [];
	$package_gst = [];
	$package_totals = [];
	$package_patient_type = [];
	$package_insurer_id = [];
	$package_patient = [];
	$package_insurer = [];
	foreach($_POST['packageid'] as $i => $package) {
		$row = $_POST['package_row_id'][$i];
		if($package > 0) {
			$package_insurer[$i] = '';
			$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `package` WHERE `packageid`='$package'"));
			$list_package_patient .= $details['category'].' : '.$details['heading'].'<br>';
			$packages[] = $package;
			$package_name[] = $details['heading'];
			$package_fees[] = $_POST['package_cost'][$i];
			$gst = $_POST['package_gst_exempt'][$i] == "1" ? 0 : $_POST['package_cost'][$i] * $_POST['tax_rate'] / 100;
			$package_gst[] = $gst;
			$total = $_POST['package_cost'][$i] + $gst;
			$package_totals[] = $total;
			$package_total_price += $total;
			$adjust_amount += $total;
			$total_amount += $total - $gst;
			$final_amount += $total;
			foreach($_POST['insurer_row_applied'] as $j => $match_row) {
				$applied = 0;
				if($row == $match_row && $_POST['insurer_payment_amt'][$j] != 0) {
					$applied = $_POST['insurer_payment_amt'][$j];

					//Invoice Insurer Portion
					$gst_insurer = ($applied == $total ? $gst : 0);//round($applied / $_POST['package_cost'][$i] * $gst,2);
					//$applied += $gst_insurer;
					$invoice_insurer[] = [ 'insurer' => $_POST['insurerid'][$j],
						'sub_total' => $applied - $gst_insurer,
						'gst_amt' => $gst_insurer,
						'price' => $applied,
						'service_cat' => 'Package',
						'service_name' => $details['heading'],
						'product_name' => '',
						'paid' => $paid ];

					$gst -= $gst_insurer;
					$total -= $applied;
					$list_package_insures[$_POST['insurerid'][$j]] .= $details['category'].' : '.$details['heading'].'<br>';
					$package_insurer_price[$_POST['insurerid'][$j]] += $applied;
					//$insurers[$_POST['insurerid'][$i]] += $gst_insurer;
					$package_insurer[$i] .= $_POST['insurerid'][$j].':'.$applied.'#*#';
				}
			}
			if($total > $on_account) {
				$total -= $on_account;
				$on_account = 0;
			} else if($on_account > 0) {
				$total = 0;
				$on_account -= $total;
			}
			foreach($payment_used as $j => $amt_unused) {
				$applied = 0;
				if($total > 0 && $amt_unused > 0) {
					if($total > $amt_unused) {
						$applied = $amt_unused;
					} else {
						$applied = $total;
					}
					$payment_used[$j] -= $applied;

					//Invoice Patient Portion
					$gst_patient = round($applied / $total * $gst,2);
					if($gst < $gst_patient) {
						$gst_patient = $gst;
					}
					$invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
						'gst_amt' => $gst_patient,
						'gratuity' => 0,
						'price' => $applied,
						'service_cat' => 'Package',
						'service_name' => $details['heading'],
						'product_name' => '',
						'paid' => $payment_types[$j] ];

					$gst -= $gst_patient;
					$total -= $applied;
					$package_patient_price += $applied;
				}

				if($payment_types[$j] == 'Pro-Bono') {
					$package_pro_bono .= $applied.',';
				} else if($payment_types[$j] == 'On Account') {
					$on_account -= $applied;
				} else {
					$package_patient[$i] += $applied;
				}
			}
		}
	}

	$inv_ids = [];
	$inv_types = [];
	$inv_names = [];
	$inv_prices = [];
	$inv_gst = [];
	$inv_totals = [];
	$inv_qtys = [];
	$inv_patient_type = [];
	$inv_insurer_id = [];
	$inv_patient = [];
	$inv_insurer = [];
	foreach($_POST['inventoryid'] as $i => $inv) {
		if($inv > 0 && $_POST['sell_price'][$i] > 0) {
			$inv_insurer[$i] = '';
			$inventory = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='$inv'"));
			$list_inventory_patient .= $inventory['name'].'<br>';
			$inv_ids[] = $inv;
			$inv_types[] = $_POST['invtype'][$i];
			$inv_names[] = $inventory['name'][$i];
			$inv_qtys[] = $_POST['quantity'][$i];
			$inv_prices[] = $_POST['sell_price'][$i];
			$gst = ($_POST['inventory_gst_exempt'][$i] == "1" ? 0 : $_POST['sell_price'][$i] * $_POST['tax_rate'] / 100);
			$inv_gst[] = $gst;
			$total = $_POST['sell_price'][$i] + $gst;
			$inv_totals[] = $total;
			$inv_total_price += $total;
			$adjust_amount += $total;
			$total_amount += $total - $gst;
			$final_amount += $total;
			$row = $_POST['inventory_row_id'][$i];
			foreach($_POST['insurer_row_applied'] as $j => $match_row) {
				$applied = 0;
				if($row == $match_row && $_POST['insurer_payment_amt'][$j] != 0) {
					$applied = $_POST['insurer_payment_amt'][$j];

					//Invoice Insurer Portion
					$gst_insurer = ($applied == $total ? $gst : 0);//round($applied / $_POST['sell_price'][$i] * $gst,2);
					//$applied += $gst_insurer;
					$invoice_insurer[] = [ 'insurer' => $_POST['insurerid'][$j],
						'sub_total' => $applied - $gst_insurer,
						'gst_amt' => $gst_insurer,
						'price' => $applied,
						'service_cat' => '',
						'service_name' => '',
						'product_name' => $inventory['name'],
						'paid' => $paid ];

					$gst -= $gst_insurer;
					$total -= $applied;
					$list_inventory_insures[$_POST['insurerid'][$j]] .= $inventory['name'].'<br>';
					$inv_insurer_price[$_POST['insurerid'][$j]] += $applied;
					//$insurers[$_POST['insurerid'][$i]] += $gst_insurer;
					$inv_insurer[$i] .= $_POST['insurerid'][$j].':'.$applied.'#*#';
				}
			}
			if($total > $on_account) {
				$total -= $on_account;
				$on_account = 0;
			} else if($on_account > 0) {
				$total = 0;
				$on_account -= $total;
			}
			foreach($payment_used as $j => $amt_unused) {
				$applied = 0;
				if($total > 0 && $amt_unused > 0) {
					if($total > $amt_unused) {
						$applied = $amt_unused;
					} else {
						$applied = $total;
					}
					$payment_used[$j] -= $applied;

					//Invoice Patient Portion
					$gst_patient = round($applied / $total * $gst,2);
					if($gst < $gst_patient) {
						$gst_patient = $gst;
					}
					$invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
						'gst_amt' => $gst_patient,
						'gratuity' => 0,
						'price' => $applied,
						'service_cat' => '',
						'service_name' => '',
						'product_name' => $inventory['name'],
						'paid' => $payment_types[$j] ];

					$gst -= $gst_patient;
					$total -= $applied;
					$inv_patient_price += $applied;
				}

				if($payment_types[$j] == 'Pro-Bono') {
					$product_pro_bono .= $applied.',';
				} else if($payment_types[$j] == 'On Account') {
					$on_account -= $applied;
				} else {
					$inv_patient[$i] += $applied;
				}
			}
		}
	}

	$misc_names = [];
	$misc_unit_prices = [];
	$misc_qtys = [];
	$misc_prices = [];
	$misc_gst = [];
	$misc_totals = [];
	$misc_insurer_id = [];
	$misc_patient = [];
	$misc_insurer = [];
	foreach($_POST['misc_item'] as $i => $misc) {
		if($misc != '' && $_POST['misc_total'][$i] > 0) {
			$misc_insurer[$i] = '';
			$list_misc_patient .= $misc.'<br>';
			$misc_names[] = $misc;
			$misc_unit_prices[] = $_POST['misc_price'][$i];
			$misc_qtys[] = $_POST['misc_qty'][$i];
			$misc_prices[] = $_POST['misc_total'][$i];
			$gst = $_POST['misc_total'][$i] * $_POST['tax_rate'] / 100;
			$misc_gst[] = $gst;
			$total = $_POST['misc_total'][$i] + $gst;
			$adjust_amount += $total;
			$final_amount += $total;
			$misc_totals[] = $total;
			$misc_total_price += $total;
			$row = $_POST['misc_row_id'][$i];
			$invoice_lines[] = "INSERT INTO `invoice_lines` (`invoiceid`, `description`, `category`, `quantity`, `unit_price`, `sub_total`, `gst`, `total`)
				VALUES ('INVOICEID', '".$misc."', 'misc product', '".$_POST['misc_qty'][$i]."', '".$_POST['misc_price'][$i]."', '".$_POST['misc_total'][$i]."', '$gst', '$total')";
			foreach($_POST['insurer_row_applied'] as $j => $match_row) {
				$applied = 0;
				if($row == $match_row && $_POST['insurer_payment_amt'][$j] != 0) {
					$applied = $_POST['insurer_payment_amt'][$j];

					//Invoice Insurer Portion
					$gst_insurer = ($applied == $total ? $gst : 0);//round($applied / $_POST['sell_price'][$i] * $gst,2);
					//$applied += $gst_insurer;
					$invoice_insurer[] = [ 'insurer' => $_POST['insurerid'][$j],
						'sub_total' => $applied - $gst_insurer,
						'gst_amt' => $gst_insurer,
						'price' => $applied,
						'service_cat' => '',
						'service_name' => '',
						'product_name' => 'Miscellaneous: '.$misc,
						'paid' => $paid ];

					$gst -= $gst_insurer;
					$total -= $applied;
					$list_misc_insures[$_POST['insurerid'][$j]] .= 'Miscellaneous: '.$misc.'<br>';
					$misc_insurer_price[$_POST['insurerid'][$j]] += $applied;
					//$insurers[$_POST['insurerid'][$i]] += $gst_insurer;
					$misc_insurer[$i] .= $_POST['insurerid'][$j].':'.$applied.'#*#';
				}
			}
			$promo_applied = 0;
			if($promo_value > 0 && $total > 0) {
				if($promo_value > $total) {
					$promo_applied = $total;
				} else {
					$promo_applied = $promo_value;
				}
			}
			$promo_value -= $promo_applied;
			$total -= $promo_applied;
			$misc_promo .= $promo_applied.',';
			foreach($payment_used as $j => $amt_unused) {
				$applied = 0;
				if($total > 0 && $amt_unused > 0) {
					if($total > $amt_unused) {
						$applied = $amt_unused;
					} else {
						$applied = $total;
					}
					$payment_used[$j] -= $applied;

					//Invoice Patient Portion
					$gst_patient = round($applied / $total * $gst,2);
					if($gst < $gst_patient) {
						$gst_patient = $gst;
					}
					$invoice_patient[] = [ 'sub_total' => $applied - $gst_patient,
						'gst_amt' => $gst_patient,
						'gratuity' => 0,
						'price' => $applied,
						'service_cat' => '',
						'service_name' => '',
						'product_name' => 'Miscellaneous: '.$misc,
						'paid' => $payment_types[$j] ];

					$gst -= $gst_patient;
					$total -= $applied;
					$misc_patient_price += $applied;
				}

				if($payment_types[$j] == 'Pro-Bono') {
					$misc_pro_bono .= $applied.',';
				} else {
					$misc_patient[$i] += $applied;
				}
			}
		}
	}

	if($gratuity > 0) {
		$invoice_patient[] = [ 'sub_total' => 0,
			'gst_amt' => 0,
			'gratuity' => $gratuity,
			'price' => $gratuity,
			'service_cat' => '',
			'service_name' => '',
			'product_name' => 'Gratuity',
			'paid' => $payment_types[$j] ];
	}

	if($delivery > 0) {
		$invoice_patient[] = [ 'sub_total' => $delivery,
			'gst_amt' => $delivery * $_POST['tax_rate'] / 100,
			'gratuity' => 0,
			'price' => $delivery + $delivery * $_POST['tax_rate'] / 100,
			'service_cat' => '',
			'service_name' => '',
			'product_name' => 'Delivery: '.$delivery_type,
			'paid' => $payment_types[$j] ];
	}

	//Apply further refunds to the the items used for excess refunds
	foreach($payment_used as $j => $amt_unused) {
		$applied = 0;
		if($amt_unused > 0) {
			foreach($refund_categories as $i => $refund_category) {
				$refund = $refund_category['refund'];
				$applied = ($refund > $amt_unused ? $amt_unused : $refund);
				//Invoice Patient Portion
				if($applied != 0) {
					$payment_used[$j] -= $applied;
					$amt_unused -= $applied;
					$refund_categories['refund'][$i] -= $applied;
					$gst = round($refund_category['gst_amt'] * $applied / $refund_category['price'], 2);
					$adjust_amount += $applied;
					$total_amount += $applied - $gst;
					$final_amount += $applied;
					$invoice_patient[] = [ 'sub_total' => $applied - $gst,
						'gst_amt' => $gst,
						'gratuity' => 0,
						'price' => $applied,
						'service_cat' => $refund_category['service_cat'],
						'service_name' => $refund_category['service_name'],
						'product_name' => $refund_category['product_name'],
						'paid' => $payment_types[$j] ];
				}
			}
		}

		if($payment_types[$j] == 'On Account') {
			$on_account += $applied;
		}
	}

	//Record further payments as Adjustments
	foreach($payment_used as $j => $amt_unused) {
		$applied = 0;
		if($amt_unused > 0) {
			$applied = $amt_unused;
			$payment_used[$j] -= $applied;
			$adjust_amount += $applied;
			$total_amount += $applied;
			$final_amount += $applied;
			//Invoice Patient Portion
			$invoice_patient[] = [ 'sub_total' => $applied,
				'gst_amt' => 0,
				'gratuity' => 0,
				'price' => $applied,
				'service_cat' => 'Adjustment',
				'service_name' => 'Adjustment',
				'product_name' => '',
				'paid' => $payment_types[$j] ];
		}

		if($payment_types[$j] == 'On Account') {
			$on_account += $applied;
		}
	}

	$serviceid = implode(',', $services).',';
	$fee = implode(',', $service_fees).',';
	$all_af = implode(',', $service_admin_fees).',';
	$fee_total = array_sum($service_fees);
	foreach($inv_qtys as $i => $cur_qty) {
		if(!$inv_ids[$i] > 0) {
			unset($inv_ids[$i]);
			unset($inv_prices[$i]);
			unset($inv_types[$i]);
			unset($inv_qtys[$i]);
		}
	}
	$inventoryid = implode(',', $inv_ids).',';
	$sell_price = implode(',', $inv_prices).',';
	$invtype = implode(',', $inv_types).',';
	$quantity = implode(',', $inv_qtys).',';
	$packageid = implode(',', $packages).',';
	$package_cost = implode(',', $package_fees).',';
	$misc_items = implode(',', $misc_names).',';
	$misc_price = implode(',', $misc_prices).',';
	$misc_qty = implode(',', $misc_qtys).',';
	$misc_total = implode(',', $misc_totals).',';
	$payment_type = implode(',', $payment_types).'#*#'.implode(',',$payment_amts);
	$insurerid = '';
	$insurance_payment = '';
	foreach($insurers as $ins_id => $ins_amt) {
		$insurerid .= $ins_id.',';
		$insurance_payment .= $ins_amt.',';
	}

	//Save Adjustment Information
	if($adjust_amount != 0) {
		$patientid = $src_invoice['patientid'];
		$service_patient = implode(',',$service_patient).',';
		$product_patient = implode(',',$inv_patient).',';
		$package_patient = implode(',',$package_patient).',';
		$misc_patient = implode(',',$misc_patient).',';
		$service_ins = implode(',',$service_insurer).',';
		$product_ins = implode(',',$inv_insurer).',';
		$package_ins = implode(',',$package_insurer).',';
		$misc_ins = implode(',',$misc_insurer).',';
		$gst_amt = $final_amount - $total_amount;
		$adjust_gst = $gst_amt;
		$delivery = $_POST['delivery'];
		$delivery_type = $_POST['delivery_type'];
		$delivery_address = $_POST['delivery_address'];
		$ship_date = $_POST['ship_date'];
		$contractorid = $_POST['contractorid'];
		$created_by = $_SESSION['contactid'];
		$pricing = $_POST['pricing'];

		$query_insert_invoice = "INSERT INTO `invoice` (`invoice_type`, `invoiceid_src`, `injuryid`, `patientid`, `therapistsid`, `serviceid`, `fee`, `admin_fee`, `service_patient`, `service_insurer`, `service_pro_bono`, `service_promo`, `inventoryid`, `sell_price`, `invtype`, `quantity`, `inventory_patient`, `inventory_insurer`, `inventory_pro_bono`, `inventory_promo`, `packageid`, `package_cost`, `package_patient`, `package_insurer`, `package_pro_bono`, `package_promo`, `misc_item`, `misc_price`, `misc_qty`, `misc_total`, `misc_patient`, `misc_insurer`, `misc_promo`, `misc_pro_bono`, `total_price`, `gst_amt`, `gratuity`, `delivery`, `delivery_type`, `delivery_address`, `contractorid`, `created_by`, `final_price`, `pro_bono`, `insurerid`, `insurance_payment`, `paid`, `payment_type`, `pricing`, `service_date`, `invoice_date`, `ship_date`, `promotionid`, `giftcardid`, `comment`)
			VALUES ('Adjustment', '$src_invoiceid', '$injuryid', '$patientid', '$therapistsid', '$serviceid', '$fee', '$all_af', '$service_patient', '$service_ins', '$service_pro_bono', '$service_promo', '$inventoryid', '$sell_price', '$invtype', '$quantity', '$product_patient', '$product_ins', '$product_pro_bono', '$product_promo', '$packageid', '$package_cost', '$package_patient', '$package_ins', '$package_pro_bono', '$package_promo', '$misc_item', '$misc_price', '$misc_qty', '$misc_total', '$misc_patient', '$misc_ins', '$misc_promo', '$misc_pro_bono', '$total_amount', '$gst_amt', '$gratuity', '$delivery', '$delivery_type', '$delivery_address', '$contractorid', '$created_by', '$final_amount', '$pro_bono', '$insurerid', '$insurance_payment', '$paid', '$payment_type', '$pricing', '$service_date', '$today_date', '$ship_date', '$promotionid', '$giftcardid', '$comment')";
		$result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
		$invoiceid = mysqli_insert_id($dbc);
		foreach($invoice_lines as $query) {
			mysqli_query($dbc, str_replace('INVOICEID',$invoiceid,$query));
		}
		//Adjustment Compensation
		foreach($services as $i => $sid) {
			$service_all_pro_bono = explode(',',$service_pro_bono);
			if($sid != '') {
				$f = $service_fees[$i];
				$result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT r.admin_fee, s.gst_exempt FROM services s, company_rate_card r WHERE s.serviceid='$sid' AND s.serviceid = r.item_id AND r.tile_name LIKE 'Services' AND '$today_date' >= r.start_date AND ('$today_date' <= r.end_date OR IFNULL(r.end_date,'0000-00-00') = '0000-00-00')"));
				$admin = $result['admin_fee'];
				$total_pro_bono_amt = $service_all_pro_bono[$i] - ($result['gst_exempt'] == 1 ? 0 : $f * $_POST['tax_rate'] / 100);
				if($f != $total_pro_bono_amt) {
					$f -= $total_pro_bono_amt;
					if($af > $f) {
						$af = $f;
					}
					$query_insert_invoice = "INSERT INTO `invoice_compensation` (`invoiceid`, `therapistsid`, `serviceid`, `fee`, `admin_fee`, `service_date`) VALUES ('$invoiceid', '$therapistsid', '$sid', '$f', '$af', '$today_date')";
					$result_insert_invoice = mysqli_query($dbc, $query_insert_invoice);
				}
			}
		}
		//Adjustment Package
		foreach($packages as $packageid) {
			$details = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `package` WHERE `packageid`='$packageid'"));
			foreach(explode('**',$details['assign_services']) as $package_detail) {
				$package_detail = explode('#', $package_detail);
				$service_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid`='".$package_detail[0]."'"));
				for($i = 0; $i < $package_detail[1]; $i++) {
					mysqli_query($dbc, "INSERT INTO `contact_package_sold` (`contactid`, `invoiceid`, `package_item_type`, `item_id`, `item_description`) VALUES ('$patientid', '$invoiceid', 'Service', '".$package_detail[0]."', '".$service_info['category'].': '.$service_info['heading']."')");
				}
			}
			foreach(explode('**',$details['assign_inventory']) as $package_detail) {
				$package_detail = explode('#', $package_detail);
				$inv_info = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `inventory` WHERE `inventoryid`='".$package_detail[0]."'"));
				for($i = 0; $i < $package_detail[1]; $i++) {
					mysqli_query($dbc, "INSERT INTO `contact_package_sold` (`contactid`, `invoiceid`, `package_item_type`, `item_id`, `item_description`) VALUES ('$patientid', '$invoiceid', 'Inventory', '".$package_detail[0]."', '".$inv_info['name']."')");
				}
			}
		}
		//Patient and Insurer Portions
		foreach($invoice_patient as $payment) {
			mysqli_query($dbc, "INSERT INTO `invoice_patient` (`invoiceid`, `injury_type`, `invoice_date`, `patientid`, `sub_total`, `gst_amt`, `gratuity_portion`, `patient_price`, `service_category`, `service_name`, `product_name`, `paid`, `paid_date`) VALUES ('$invoiceid', '$injury_type', '$invoice_date', '$patientid', '".$payment['sub_total']."', '".$payment['gst_amt']."', '".$payment['gratuity']."', '".$payment['price']."', '".$payment['service_cat']."', '".$payment['service_name']."', '".$payment['product_name']."', '".$payment['paid']."', '$invoice_report_date')");
		}
		$receipt_payments = array_merge($receipt_payments, $invoice_patient);
		foreach($invoice_insurer as $payment) {
			mysqli_query($dbc, "INSERT INTO `invoice_insurer` (`invoiceid`, `injury_type`, `invoice_date`, `insurerid`, `sub_total`, `gst_amt`, `insurer_price`, `service_category`, `service_name`, `product_name`, `paid`) VALUES ('$invoiceid', '$injury_type', '$invoice_date', '".$payment['insurer']."', '".$payment['sub_total']."', '".$payment['gst_amt']."', '".$payment['price']."', '".$payment['service_cat']."', '".$payment['service_name']."', '".$payment['product_name']."', '$paid')");
		}
	}
	//Generate Receipt and Invoice for Adjustment
	if(strpos($all_payment_type, 'On Account') === FALSE) {
		$result_update_in = mysqli_query($dbc, "UPDATE `invoice` SET `patient_payment_receipt` = 1 WHERE `invoiceid`='$invoiceid'");

		$logo = get_config($dbc, 'invoice_logo');
		DEFINE('INVOICE_LOGO', $logo);

		include ('patient_payment_receipt_pdf.php');
	}

	$get_invoice = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `invoice` WHERE `invoiceid`='$invoiceid'"));
	// PDF
	$invoice_design = get_config($dbc, 'invoice_design');
	switch($invoice_design) {
		case 1:
			include('pos_invoice_1.php');
			break;
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
			include ('pos_invoice_small.php');
			break;
		case 'service':
			include ('pos_invoice_service.php');
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
		default:
			include('pos_invoice_1.php');
			break;
	}

	$final_amount = $adjust_amount + $refund_amount;
	$gst_amt = $adjust_gst + $refund_gst;
	$total_amount = $final_amount - $gst_amt;
	if($on_account > 0) {
		$query_update_invoice = "UPDATE `contacts` SET `amount_credit` = amount_credit + '$on_account' WHERE `contactid` = '$patientid'";
		$result_update_invoice = mysqli_query($dbc, $query_update_invoice);
	} else if($on_account < 0) {
		$amount_credit = mysqli_fetch_array(mysqli_query($dbc, "SELECT `amount_credit` FROM `contacts` WHERE `contactid`='$patientid'"))['amount_credit'];
		if($amount_credit + $on_account < 0) {
			$amount_credit = 0;
			$on_account += $amount_credit;
			$query_update_invoice = "UPDATE `contacts` SET `amount_credit` = '$amount_credit', `amount_owing` = '$on_account' WHERE `contactid` = '$patientid'";
			$result_update_invoice = mysqli_query($dbc, $query_update_invoice);
		} else {
			$amount_credit -= $on_account;
			$query_update_invoice = "UPDATE `contacts` SET `amount_credit` = '$amount_credit' WHERE `contactid` = '$patientid'";
			$result_update_invoice = mysqli_query($dbc, $query_update_invoice);
		}
	}
}
// Set Invoice Status
$dbc->query("UPDATE `invoice` SET `status`='$inv_status' WHERE `invoiceid`='$invoiceid'");
// Update the Invoice Ticket List
$ticketid = filter_var(implode(',',$_POST['ticketid']),FILTER_SANITIZE_STRING);
$dbc->query("UPDATE `invoice` SET `ticketid`='$ticketid' WHERE `invoiceid`='$invoiceid'");