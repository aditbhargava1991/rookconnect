<?php
    $query = mysqli_query($dbc,"DELETE FROM estimate_company_rate_card WHERE estimateid='$estimateid'");

    // Material
    $j = 0;
    foreach ($_POST['crc_material_qty'] as $materialid_all) {
        if($materialid_all != '' && $materialid_all != '0') {

            $tile_name = 'Material';
            $companyrcid = filter_var($_POST['crc_material_companyrcid'][$j],FILTER_SANITIZE_STRING);
            $type = filter_var($_POST['crc_material_type_'.$j],FILTER_SANITIZE_STRING);
            $heading = filter_var($_POST['crc_material_heading'][$j],FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['crc_material_description'][$j],FILTER_SANITIZE_STRING);
            $uom = filter_var($_POST['crc_material_uom'][$j],FILTER_SANITIZE_STRING);
            $cost = filter_var($_POST['crc_material_cost'][$j],FILTER_SANITIZE_STRING);
            $cust_price = filter_var($_POST['crc_material_cust_price'][$j],FILTER_SANITIZE_STRING);
            $qty = filter_var($_POST['crc_material_qty'][$j],FILTER_SANITIZE_STRING);
            $rc_total = filter_var($_POST['crc_material_total'][$j],FILTER_SANITIZE_STRING);

            $query_insert_customer = "INSERT INTO `estimate_company_rate_card` (`estimateid`, `companyrcid`, `tile_name`, `rc_type`, `heading`, `description`, `uom`, `cost`, `cust_price`, `qty`, `rc_total`) VALUES ('$estimateid', '$companyrcid', '$tile_name','$type', '$heading' , '$description', '$uom', '$cost', '$cust_price', '$qty', '$rc_total')";
            $result_insert_customer = mysqli_query($dbc, $query_insert_customer);

            $before_change = '';
            $history = "Estimates company rate card entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $total_price += $rc_total;
            $total_products += $rc_total;
            $products_total += $qty;
            $products_price_total +=$cust_price;

            //$total_price += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];
            //$total_material += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];

            //$material_total += $_POST['mestimateqty'][$j];
            //$material_price_total += $_POST['mestimateprice'][$j];

            $m_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type': $m_html .= '<td>Material</td>'; break;
					case 'Description': $m_html .= '<td>'.$type.'<br>'.$heading.'</td>'; break;
					case 'UOM': $m_html .= '<td>'.$uom.'</td>'; break;
					case 'Quantity': $m_html .= '<td>'.$qty.'</td>'; break;
					case 'Price': $m_html .= '<td>'.number_format((float)$cust_price, 2, '.', '').'</td>'; break;
					case 'Total': $m_html .= '<td>'.number_format((float)$rc_total, 2, '.', '').'</td>'; break;
					case 'Total X 4': $m_html .= '<td>'.number_format((float)$rc_total * 4, 2, '.', '').'</td>'; $total4 = true; break;
				}
			}
			$m_html .= '</tr>';

            $color_off = '';
			$plus_minus = $cust_price - $cost;
            $financial_plus_minus += $plus_minus;
            if($plus_minus < 0) {
                $color_off = 'style = "color:red; "';
            } else {
                $color_off = 'style = "color:green; "';
            }
			$plus_minus = abs($plus_minus);
			$review_profit_loss .= '<tr><td>Material</td>
				<td>'.$type.' : '.$heading.'</td>
				<td>'.$description.'</td>
				<td>'.$uom.'</td>
				<td>'.$qty.'</td>
				<td>$'.$cost.'</td>
				<td>'.number_format((1-(float)$cost/(float)$cust_price)*100, 2, '.', '').'%</td>
				<td>$'.number_format((float)$cust_price, 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.$rc_total.'</td></tr>';

        }
        $j++;
    }

    //Services
    $j = 0;
    foreach ($_POST['crc_service_qty'] as $serviceid_all) {
        if($serviceid_all != '' && $serviceid_all != '0') {

            $tile_name = 'Services';
            $companyrcid = filter_var($_POST['crc_service_companyrcid'][$j],FILTER_SANITIZE_STRING);
            $type = filter_var($_POST['crc_service_type_'.$j],FILTER_SANITIZE_STRING);
            $heading = filter_var($_POST['crc_service_heading'][$j],FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['crc_service_description'][$j],FILTER_SANITIZE_STRING);
            $uom = filter_var($_POST['crc_service_uom'][$j],FILTER_SANITIZE_STRING);
            $cost = filter_var($_POST['crc_service_cost'][$j],FILTER_SANITIZE_STRING);
            $cust_price = filter_var($_POST['crc_service_cust_price'][$j],FILTER_SANITIZE_STRING);
            $qty = filter_var($_POST['crc_service_qty'][$j],FILTER_SANITIZE_STRING);
            $rc_total = filter_var($_POST['crc_service_total'][$j],FILTER_SANITIZE_STRING);

            $query_insert_customer = "INSERT INTO `estimate_company_rate_card` (`estimateid`, `companyrcid`, `tile_name`, `rc_type`, `heading`, `description`, `uom`, `cost`, `cust_price`, `qty`, `rc_total`) VALUES ('$estimateid', '$companyrcid', '$tile_name','$type', '$heading' , '$description', '$uom', '$cost', '$cust_price', '$qty', '$rc_total')";
            $result_insert_customer = mysqli_query($dbc, $query_insert_customer);

            $before_change = '';
            $history = "Estimates company rate card entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $total_price += $rc_total;
            $total_service += $rc_total;
            $service_total += $qty;
            $service_price_total +=$cust_price;

            //$total_price += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];
            //$total_service += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];

            //$service_total += $_POST['mestimateqty'][$j];
            //$service_price_total += $_POST['mestimateprice'][$j];

            $s_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type': $s_html .= '<td>Services</td>'; break;
					case 'Description': $s_html .= '<td>'.$type.'<br>'.$heading.'</td>'; break;
					case 'UOM': $s_html .= '<td>'.$uom.'</td>'; break;
					case 'Quantity': $s_html .= '<td>'.$qty.'</td>'; break;
					case 'Price': $s_html .= '<td>'.number_format((float)$cust_price, 2, '.', '').'</td>'; break;
					case 'Total': $s_html .= '<td>'.number_format((float)$rc_total, 2, '.', '').'</td>'; break;
					case 'Total X 4': $s_html .= '<td>'.number_format((float)$rc_total * 4, 2, '.', '').'</td>'; $total4 = true; break;
				}
			}
            $s_html .= '</tr>';

            $color_off = '';
			$plus_minus = $cust_price - $cost;
            $financial_plus_minus += $plus_minus;
            if($plus_minus < 0) {
                $color_off = 'style = "color:red; "';
            } else {
                $color_off = 'style = "color:green; "';
            }
			$plus_minus = abs($plus_minus);
			$review_profit_loss .= '<tr><td>Services</td>
				<td>'.$type.' : '.$heading.'</td>
				<td>'.$description.'</td>
				<td>'.$uom.'</td>
				<td>'.$qty.'</td>
				<td>$'.$cost.'</td>
				<td>'.number_format((1-(float)$cost/(float)$cust_price)*100, 2, '.', '').'%</td>
				<td>$'.number_format((float)$cust_price, 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.$rc_total.'</td></tr>';

        }
        $j++;
    }

    //Products
    //$j = 0;

    $total_rc_products = $_POST['total_rc_products'];
    for($j=0;$j<=$total_rc_products;$j++) {
        if($_POST['crc_products_qty_'.$j] != '' && $_POST['crc_products_qty_'.$j] != '0') {
            $tile_name = 'Product';
            $companyrcid = filter_var($_POST['crc_products_companyrcid_'.$j],FILTER_SANITIZE_STRING);
            $type = filter_var($_POST['crc_products_type_'.$j],FILTER_SANITIZE_STRING);
            $heading = filter_var($_POST['crc_products_heading_'.$j],FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['crc_products_description_'.$j],FILTER_SANITIZE_STRING);
            $uom = filter_var($_POST['crc_products_uom_'.$j],FILTER_SANITIZE_STRING);
            $cost = filter_var($_POST['crc_products_cost_'.$j],FILTER_SANITIZE_STRING);
            $cust_price = filter_var($_POST['crc_products_cust_price_'.$j],FILTER_SANITIZE_STRING);
            $qty = filter_var($_POST['crc_products_qty_'.$j],FILTER_SANITIZE_STRING);
            $rc_total = filter_var($_POST['crc_products_total_'.$j],FILTER_SANITIZE_STRING);

            $profit = filter_var($_POST['crc_products_profit_'.$j],FILTER_SANITIZE_STRING);
            $margin = filter_var($_POST['crc_products_margin_'.$j],FILTER_SANITIZE_STRING);

            $query_insert_products = "INSERT INTO `estimate_company_rate_card` (`estimateid`, `companyrcid`, `tile_name`, `rc_type`, `heading`, `description`, `uom`, `cost`, `cust_price`, `qty`, `rc_total`,`profit`,`margin`) VALUES ('$estimateid', '$companyrcid', '$tile_name','$type', '$heading' , '$description', '$uom', '$cost', '$cust_price', '$qty', '$rc_total','$profit','$margin')";
            $result_insert_customer = mysqli_query($dbc, $query_insert_products);

            $before_change = '';
            $history = "Estimates company rate card entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $total_price += $rc_total;
            $total_products += $rc_total;
            $products_total += $qty;
            $products_price_total +=$cust_price;

            //$total_price += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];
            //$total_products += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];

            //$products_total += $_POST['mestimateqty'][$j];
            //$products_price_total += $_POST['mestimateprice'][$j];

            $p_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type': $p_html .= '<td>Product</td>'; break;
					case 'Description': $p_html .= '<td>'.$type.'<br>'.$heading.'</td>'; break;
					case 'UOM': $p_html .= '<td>'.$uom.'</td>'; break;
					case 'Quantity': $p_html .= '<td>'.$qty.'</td>'; break;
					case 'Price': $p_html .= '<td>'.number_format((float)$cust_price, 2, '.', '').'</td>'; break;
					case 'Total': $p_html .= '<td>'.number_format((float)$rc_total, 2, '.', '').'</td>'; break;
					case 'Total X 4': $p_html .= '<td>'.number_format((float)$rc_total * 4, 2, '.', '').'</td>'; $total4 = true; break;
				}
			}
            $p_html .= '</tr>';

            $color_off = '';
			$plus_minus = $cust_price - $cost;
            $financial_plus_minus += $plus_minus;
            if($plus_minus < 0) {
                $color_off = 'style = "color:red; "';
            } else {
                $color_off = 'style = "color:green; "';
            }
			$plus_minus = abs($plus_minus);
			$review_profit_loss .= '<tr><td>Product</td>
				<td>'.$type.' : '.$heading.'</td>
				<td>'.$description.'</td>
				<td>'.$uom.'</td>
				<td>'.$qty.'</td>
				<td>$'.$cost.'</td>
				<td>'.number_format((1-(float)$cost/(float)$cust_price)*100, 2, '.', '').'%</td>
				<td>$'.number_format((float)$cust_price, 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.$rc_total.'</td></tr>';

        }
        //$j++;
    }

    //Staff
    $j = 0;
    foreach ($_POST['crc_staff_qty'] as $staffid_all) {
        if($staffid_all != '' && $staffid_all != '0') {

            $tile_name = 'Staff';
            $companyrcid = filter_var($_POST['crc_staff_companyrcid'][$j],FILTER_SANITIZE_STRING);
            $type = filter_var($_POST['crc_staff_type_'.$j],FILTER_SANITIZE_STRING);
            $heading = filter_var($_POST['crc_staff_heading'][$j],FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['crc_staff_description'][$j],FILTER_SANITIZE_STRING);
            $uom = filter_var($_POST['crc_staff_uom'][$j],FILTER_SANITIZE_STRING);
            $cost = filter_var($_POST['crc_staff_cost'][$j],FILTER_SANITIZE_STRING);
            $cust_price = filter_var($_POST['crc_staff_cust_price'][$j],FILTER_SANITIZE_STRING);
            $qty = filter_var($_POST['crc_staff_qty'][$j],FILTER_SANITIZE_STRING);
            $rc_total = filter_var($_POST['crc_staff_total'][$j],FILTER_SANITIZE_STRING);

            $query_insert_customer = "INSERT INTO `estimate_company_rate_card` (`estimateid`, `companyrcid`, `tile_name`, `rc_type`, `heading`, `description`, `uom`, `cost`, `cust_price`, `qty`, `rc_total`) VALUES ('$estimateid', '$companyrcid', '$tile_name','$type', '$heading' , '$description', '$uom', '$cost', '$cust_price', '$qty', '$rc_total')";
            $result_insert_customer = mysqli_query($dbc, $query_insert_customer);

            $before_change = '';
            $history = "Estimates company rate card entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $total_price += $rc_total;
            $total_products += $rc_total;
			$cost_staff += $cost * $qty;
            $products_total += $qty;
            $products_price_total +=$cust_price;
            //$total_price += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];
            //$total_staff += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];

            //$staff_total += $_POST['mestimateqty'][$j];
            //$staff_price_total += $_POST['mestimateprice'][$j];

            $staff_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type': $staff_html .= '<td>Staff</td>'; break;
					case 'Description': $staff_html .= '<td>'.$type.'<br>'.$heading.'</td>'; break;
					case 'UOM': $staff_html .= '<td>'.$uom.'</td>'; break;
					case 'Quantity': $staff_html .= '<td>'.$qty.'</td>'; break;
					case 'Price': $staff_html .= '<td>'.number_format((float)$cust_price, 2, '.', '').'</td>'; break;
					case 'Total': $staff_html .= '<td>'.number_format((float)$rc_total, 2, '.', '').'</td>'; break;
					case 'Total X 4': $staff_html .= '<td>'.number_format((float)$rc_total * 4, 2, '.', '').'</td>'; $total4 = true; break;
				}
			}
            $staff_html .= '</tr>';

            $color_off = '';
			$plus_minus = $cust_price - $cost;
            $financial_plus_minus += $plus_minus;
            if($plus_minus < 0) {
                $color_off = 'style = "color:red; "';
            } else {
                $color_off = 'style = "color:green; "';
            }
			$plus_minus = abs($plus_minus);
			$review_profit_loss .= '<tr><td>Staff</td>
				<td>'.$type.' : '.$heading.'</td>
				<td>'.$description.'</td>
				<td>'.$uom.'</td>
				<td>'.$qty.'</td>
				<td>$'.$cost.'</td>
				<td>'.number_format((1-(float)$cost/(float)$cust_price)*100, 2, '.', '').'%</td>
				<td>$'.number_format((float)$cust_price, 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.$rc_total.'</td></tr>';

        }
        $j++;
    }

    //Contractor
    $j = 0;
    foreach ($_POST['crc_contractor_qty'] as $contractorid_all) {
        if($contractorid_all != '' && $contractorid_all != '0') {

            $tile_name = 'Contractor';
            $companyrcid = filter_var($_POST['crc_contractor_companyrcid'][$j],FILTER_SANITIZE_STRING);
            $type = filter_var($_POST['crc_contractor_type_'.$j],FILTER_SANITIZE_STRING);
            $heading = filter_var($_POST['crc_contractor_heading'][$j],FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['crc_contractor_description'][$j],FILTER_SANITIZE_STRING);
            $uom = filter_var($_POST['crc_contractor_uom'][$j],FILTER_SANITIZE_STRING);
            $cost = filter_var($_POST['crc_contractor_cost'][$j],FILTER_SANITIZE_STRING);
            $cust_price = filter_var($_POST['crc_contractor_cust_price'][$j],FILTER_SANITIZE_STRING);
            $qty = filter_var($_POST['crc_contractor_qty'][$j],FILTER_SANITIZE_STRING);
            $rc_total = filter_var($_POST['crc_contractor_total'][$j],FILTER_SANITIZE_STRING);

            $query_insert_customer = "INSERT INTO `estimate_company_rate_card` (`estimateid`, `companyrcid`, `tile_name`, `rc_type`, `heading`, `description`, `uom`, `cost`, `cust_price`, `qty`, `rc_total`) VALUES ('$estimateid', '$companyrcid', '$tile_name','$type', '$heading' , '$description', '$uom', '$cost', '$cust_price', '$qty', '$rc_total')";
            $result_insert_customer = mysqli_query($dbc, $query_insert_customer);

            $before_change = '';
            $history = "Estimates company rate card entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $total_price += $rc_total;
            $total_products += $rc_total;
			$cost_contractor += $cost * $qty;
            $products_total += $qty;
            $products_price_total +=$cust_price;
            //$total_price += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];
            //$total_contractor += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];

            //$contractor_total += $_POST['mestimateqty'][$j];
            //$contractor_price_total += $_POST['mestimateprice'][$j];

            $cont_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type': $cont_html .= '<td>Contractor</td>'; break;
					case 'Description': $cont_html .= '<td>'.$type.'<br>'.$heading.'</td>'; break;
					case 'UOM': $cont_html .= '<td>'.$uom.'</td>'; break;
					case 'Quantity': $cont_html .= '<td>'.$qty.'</td>'; break;
					case 'Price': $cont_html .= '<td>'.number_format((float)$cust_price, 2, '.', '').'</td>'; break;
					case 'Total': $cont_html .= '<td>'.number_format((float)$rc_total, 2, '.', '').'</td>'; break;
					case 'Total X 4': $cont_html .= '<td>'.number_format((float)$rc_total * 4, 2, '.', '').'</td>'; $total4 = true; break;
				}
			}
            $cont_html .= '</tr>';

            $color_off = '';
			$plus_minus = $cust_price - $cost;
            $financial_plus_minus += $plus_minus;
            if($plus_minus < 0) {
                $color_off = 'style = "color:red; "';
            } else {
                $color_off = 'style = "color:green; "';
            }
			$plus_minus = abs($plus_minus);
			$review_profit_loss .= '<tr><td>Contractor</td>
				<td>'.$type.' : '.$heading.'</td>
				<td>'.$description.'</td>
				<td>'.$uom.'</td>
				<td>'.$qty.'</td>
				<td>$'.$cost.'</td>
				<td>'.number_format((1-(float)$cost/(float)$cust_price)*100, 2, '.', '').'%</td>
				<td>$'.number_format((float)$cust_price, 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.$rc_total.'</td></tr>';
        }
        $j++;
    }

    //Client
    $j = 0;
    foreach ($_POST['crc_clients_qty'] as $clientsid_all) {
        if($clientsid_all != '' && $clientsid_all != '0') {

            $tile_name = 'Client';
            $companyrcid = filter_var($_POST['crc_clients_companyrcid'][$j],FILTER_SANITIZE_STRING);
            $type = filter_var($_POST['crc_clients_type_'.$j],FILTER_SANITIZE_STRING);
            $heading = filter_var($_POST['crc_clients_heading'][$j],FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['crc_clients_description'][$j],FILTER_SANITIZE_STRING);
            $uom = filter_var($_POST['crc_clients_uom'][$j],FILTER_SANITIZE_STRING);
            $cost = filter_var($_POST['crc_clients_cost'][$j],FILTER_SANITIZE_STRING);
            $cust_price = filter_var($_POST['crc_clients_cust_price'][$j],FILTER_SANITIZE_STRING);
            $qty = filter_var($_POST['crc_clients_qty'][$j],FILTER_SANITIZE_STRING);
            $rc_total = filter_var($_POST['crc_clients_total'][$j],FILTER_SANITIZE_STRING);

            $query_insert_customer = "INSERT INTO `estimate_company_rate_card` (`estimateid`, `companyrcid`, `tile_name`, `rc_type`, `heading`, `description`, `uom`, `cost`, `cust_price`, `qty`, `rc_total`) VALUES ('$estimateid', '$companyrcid', '$tile_name','$type', '$heading' , '$description', '$uom', '$cost', '$cust_price', '$qty', '$rc_total')";
            $result_insert_customer = mysqli_query($dbc, $query_insert_customer);

            $before_change = '';
            $history = "Estimates company rate card entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $total_price += $rc_total;
            $total_products += $rc_total;
			$cost_client += $cost * $qty;
            $products_total += $qty;
            $products_price_total +=$cust_price;
            //$total_price += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];
            //$total_clients += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];

            //$clients_total += $_POST['mestimateqty'][$j];
            //$clients_price_total += $_POST['mestimateprice'][$j];

            $c_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type': $c_html .= '<td>Client</td>'; break;
					case 'Description': $c_html .= '<td>'.$type.'<br>'.$heading.'</td>'; break;
					case 'UOM': $c_html .= '<td>'.$uom.'</td>'; break;
					case 'Quantity': $c_html .= '<td>'.$qty.'</td>'; break;
					case 'Price': $c_html .= '<td>'.number_format((float)$cust_price, 2, '.', '').'</td>'; break;
					case 'Total': $c_html .= '<td>'.number_format((float)$rc_total, 2, '.', '').'</td>'; break;
					case 'Total X 4': $c_html .= '<td>'.number_format((float)$rc_total * 4, 2, '.', '').'</td>'; $total4 = true; break;
				}
			}
            $c_html .= '</tr>';

            $color_off = '';
			$plus_minus = $cust_price - $cost;
            $financial_plus_minus += $plus_minus;
            if($plus_minus < 0) {
                $color_off = 'style = "color:red; "';
            } else {
                $color_off = 'style = "color:green; "';
            }
			$plus_minus = abs($plus_minus);
			$review_profit_loss .= '<tr><td>Client</td>
				<td>'.$type.' : '.$heading.'</td>
				<td>'.$description.'</td>
				<td>'.$uom.'</td>
				<td>'.$qty.'</td>
				<td>$'.$cost.'</td>
				<td>'.number_format((1-(float)$cost/(float)$cust_price)*100, 2, '.', '').'%</td>
				<td>$'.number_format((float)$cust_price, 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.$rc_total.'</td></tr>';

        }
        $j++;
    }

    //Vendor
    /*
    $j = 0;
    foreach ($_POST['crc_clients_qty'] as $clientsid_all) {
        if($clientsid_all != '' && $productsid_all != '0') {

            $tile_name = 'Client';
            $companyrcid = $_POST['crc_clients_companyrcid'][$j];
            $heading = $_POST['crc_clients_heading'][$j];
            $description = $_POST['crc_clients_description'][$j];
            $uom = $_POST['crc_clients_uom'][$j];
            $cost = $_POST['crc_clients_cost'][$j];
            $cust_price = $_POST['crc_clients_cust_price'][$j];
            $qty = $_POST['crc_products_qty'][$j];
            $rc_total = $_POST['crc_products_total'][$j];

            $query_insert_customer = "INSERT INTO `estimate_company_rate_card` (`estimateid`, `companyrcid`, `tile_name`, `rc_type`, `heading`, `description`, `uom`, `cost`, `cust_price`, `qty`, `rc_total`) VALUES ('$estimateid', '$companyrcid', '$tile_name','$type', '$heading' , '$description', '$uom', '$cost', '$cust_price', '$qty', '$rc_total')";
            $result_insert_customer = mysqli_query($dbc, $query_insert_customer);

            //$total_price += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];
            //$total_clients += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];

            //$clients_total += $_POST['mestimateqty'][$j];
            //$clients_price_total += $_POST['mestimateprice'][$j];

            $c_html .= '<tr nobr="true">';
            $c_html .= '<td>Client</td>';

            $c_html .= '<td>';
            $c_html .= $type.'<br>'.$heading;
            $c_html .= '</td>';
            $c_html .= '<td>'.$qty.'</td>';
            $c_html .= '<td>'.$uom.'</td>';
            $c_html .= '<td>$'.number_format((float)$cust_price, 2, '.', '').'</td>';
            $c_html .= '<td>$'.number_format((float)$rc_total, 2, '.', '').'</td></tr>';
        }
        $j++;
    }
    */

    //customer
    $j = 0;
    foreach ($_POST['crc_customer_qty'] as $customerid_all) {
        if($customerid_all != '' && $customerid_all != '0') {

            $tile_name = 'Customer';
            $companyrcid = filter_var($_POST['crc_customer_companyrcid'][$j],FILTER_SANITIZE_STRING);
            $type = filter_var($_POST['crc_customer_type_'.$j],FILTER_SANITIZE_STRING);
            $heading = filter_var($_POST['crc_customer_heading'][$j],FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['crc_customer_description'][$j],FILTER_SANITIZE_STRING);
            $uom = filter_var($_POST['crc_customer_uom'][$j],FILTER_SANITIZE_STRING);
            $cost = filter_var($_POST['crc_customer_cost'][$j],FILTER_SANITIZE_STRING);
            $cust_price = filter_var($_POST['crc_customer_cust_price'][$j],FILTER_SANITIZE_STRING);
            $qty = filter_var($_POST['crc_customer_qty'][$j],FILTER_SANITIZE_STRING);
            $rc_total = filter_var($_POST['crc_customer_total'][$j],FILTER_SANITIZE_STRING);

            $query_insert_customer = "INSERT INTO `estimate_company_rate_card` (`estimateid`, `companyrcid`, `tile_name`, `rc_type`, `heading`, `description`, `uom`, `cost`, `cust_price`, `qty`, `rc_total`) VALUES ('$estimateid', '$companyrcid', '$tile_name','$type', '$heading' , '$description', '$uom', '$cost', '$cust_price', '$qty', '$rc_total')";
            $result_insert_customer = mysqli_query($dbc, $query_insert_customer);

            $before_change = '';
            $history = "Estimates company rate card entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $total_price += $rc_total;
            $total_products += $rc_total;
			$cost_customer += $cost * $qty;
            $products_total += $qty;
            $products_price_total +=$cust_price;
            //$total_price += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];
            //$total_customer += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];

            //$customer_total += $_POST['mestimateqty'][$j];
            //$customer_price_total += $_POST['mestimateprice'][$j];

            $cust_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type': $cust_html .= '<td>Customer</td>'; break;
					case 'Description': $cust_html .= '<td>'.$type.'<br>'.$heading.'</td>'; break;
					case 'UOM': $cust_html .= '<td>'.$uom.'</td>'; break;
					case 'Quantity': $cust_html .= '<td>'.$qty.'</td>'; break;
					case 'Price': $cust_html .= '<td>'.number_format((float)$cust_price, 2, '.', '').'</td>'; break;
					case 'Total': $cust_html .= '<td>'.number_format((float)$rc_total, 2, '.', '').'</td>'; break;
					case 'Total X 4': $cust_html .= '<td>'.number_format((float)$rc_total * 4, 2, '.', '').'</td>'; $total4 = true; break;
				}
			}
            $cust_html .= '</tr>';

            $color_off = '';
			$plus_minus = $cust_price - $cost;
            $financial_plus_minus += $plus_minus;
            if($plus_minus < 0) {
                $color_off = 'style = "color:red; "';
            } else {
                $color_off = 'style = "color:green; "';
            }
			$plus_minus = abs($plus_minus);
			$review_profit_loss .= '<tr><td>Customer</td>
				<td>'.$type.' : '.$heading.'</td>
				<td>'.$description.'</td>
				<td>'.$uom.'</td>
				<td>'.$qty.'</td>
				<td>$'.$cost.'</td>
				<td>'.number_format((1-(float)$cost/(float)$cust_price)*100, 2, '.', '').'%</td>
				<td>$'.number_format((float)$cust_price, 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.$rc_total.'</td></tr>';

        }
        $j++;
    }

    // Inventory
    //$j = 0;
    $total_rc_inventory = $_POST['total_rc_inventory'];
    for($j=0;$j<=$total_rc_inventory;$j++) {
        if($_POST['crc_inventory_qty_'.$j] != '' && $_POST['crc_inventory_qty_'.$j] != '0') {
            $tile_name = 'Inventory';
            $companyrcid = filter_var($_POST['crc_inventory_companyrcid_'.$j],FILTER_SANITIZE_STRING);
            $type = filter_var($_POST['crc_inventory_type_'.$j],FILTER_SANITIZE_STRING);
            $heading = filter_var($_POST['crc_inventory_heading_'.$j],FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['crc_inventory_description_'.$j],FILTER_SANITIZE_STRING);
            $uom = filter_var($_POST['crc_inventory_uom_'.$j],FILTER_SANITIZE_STRING);
            $cost = filter_var($_POST['crc_inventory_cost_'.$j],FILTER_SANITIZE_STRING);
            $cust_price = filter_var($_POST['crc_inventory_cust_price_'.$j],FILTER_SANITIZE_STRING);
            $qty = filter_var($_POST['crc_inventory_qty_'.$j],FILTER_SANITIZE_STRING);
            $rc_total = filter_var($_POST['crc_inventory_total_'.$j],FILTER_SANITIZE_STRING);

            $profit = filter_var($_POST['crc_inventory_profit_'.$j],FILTER_SANITIZE_STRING);
            $margin = filter_var($_POST['crc_inventory_margin_'.$j],FILTER_SANITIZE_STRING);

            $query_insert_inventory = "INSERT INTO `estimate_company_rate_card` (`estimateid`, `companyrcid`, `tile_name`, `rc_type`, `heading`, `description`, `uom`, `cost`, `cust_price`, `qty`, `rc_total`,`profit`,`margin`) VALUES ('$estimateid', '$companyrcid', '$tile_name','$type', '$heading' , '$description', '$uom', '$cost', '$cust_price', '$qty', '$rc_total','$profit','$margin')";
            $result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);

            $before_change = '';
            $history = "Estimates company rate card entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $total_price += $rc_total;
            $total_products += $rc_total;
            $products_total += $qty;
            $products_price_total +=$cust_price;
            //$total_price += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];
            //$total_inventory += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];

            //$inventory_total += $_POST['mestimateqty'][$j];
            //$inventory_price_total += $_POST['mestimateprice'][$j];

            $in_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type': $in_html .= '<td>Inventory</td>'; break;
					case 'Description': $in_html .= '<td>'.$type.'<br>'.$heading.'</td>'; break;
					case 'UOM': $in_html .= '<td>'.$uom.'</td>'; break;
					case 'Quantity': $in_html .= '<td>'.$qty.'</td>'; break;
					case 'Price': $in_html .= '<td>'.number_format((float)$cust_price, 2, '.', '').'</td>'; break;
					case 'Total': $in_html .= '<td>'.number_format((float)$rc_total, 2, '.', '').'</td>'; break;
					case 'Total X 4': $in_html .= '<td>'.number_format((float)$rc_total * 4, 2, '.', '').'</td>'; $total4 = true; break;
				}
			}
            $in_html .= '</tr>';

            $color_off = '';
			$plus_minus = $cust_price - $cost;
            $financial_plus_minus += $plus_minus;
            if($plus_minus < 0) {
                $color_off = 'style = "color:red; "';
            } else {
                $color_off = 'style = "color:green; "';
            }
			$plus_minus = abs($plus_minus);
			$review_profit_loss .= '<tr><td>Inventory</td>
				<td>'.$type.' : '.$heading.'</td>
				<td>'.$description.'</td>
				<td>'.$uom.'</td>
				<td>'.$qty.'</td>
				<td>$'.$cost.'</td>
				<td>'.number_format((1-(float)$cost/(float)$cust_price)*100, 2, '.', '').'%</td>
				<td>$'.number_format((float)$cust_price, 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.$rc_total.'</td></tr>';

        }
        //$j++;
    }

    //Equipemt
    $j = 0;
    $total_rc_equipment = $_POST['total_rc_equipment'];
    for($j=0;$j<=$total_rc_equipment;$j++) {
        if($_POST['crc_equipment_qty_'.$j] != '' && $_POST['crc_equipment_qty_'.$j] != '0') {
            $tile_name = 'Equipment';
            $companyrcid = filter_var($_POST['crc_equipment_companyrcid_'.$j],FILTER_SANITIZE_STRING);
            $type = filter_var($_POST['crc_equipment_type_'.$j],FILTER_SANITIZE_STRING);
            $heading = filter_var($_POST['crc_equipment_heading_'.$j],FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['crc_equipment_description_'.$j],FILTER_SANITIZE_STRING);
            $uom = filter_var($_POST['crc_equipment_uom_'.$j],FILTER_SANITIZE_STRING);
            $cost = filter_var($_POST['crc_equipment_cost_'.$j],FILTER_SANITIZE_STRING);
            $cust_price = filter_var($_POST['crc_equipment_cust_price_'.$j],FILTER_SANITIZE_STRING);
            $qty = filter_var($_POST['crc_equipment_qty_'.$j],FILTER_SANITIZE_STRING);
            $rc_total = filter_var($_POST['crc_equipment_total_'.$j],FILTER_SANITIZE_STRING);
            $profit = filter_var($_POST['crc_equipment_profit_'.$j],FILTER_SANITIZE_STRING);
            $margin = filter_var($_POST['crc_equipment_profit_'.$j],FILTER_SANITIZE_STRING);

            $query_insert_equipment = "INSERT INTO `estimate_company_rate_card` (`estimateid`, `companyrcid`, `tile_name`, `rc_type`, `heading`, `description`, `uom`, `cost`, `cust_price`, `qty`, `rc_total`,`profit`,`margin`) VALUES ('$estimateid', '$companyrcid', '$tile_name','$type', '$heading' , '$description', '$uom', '$cost', '$cust_price', '$qty', '$rc_total','$profit','$margin')";
            $result_insert_equipment = mysqli_query($dbc, $query_insert_equipment);

            $before_change = '';
            $history = "Estimates company rate card entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $total_price += $rc_total;
            $total_products += $rc_total;
            $products_total += $qty;
            $products_price_total +=$cust_price;
            //$total_price += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];
            //$total_equipment += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];

            //$equipment_total += $_POST['mestimateqty'][$j];
            //$equipment_price_total += $_POST['mestimateprice'][$j];

            $eq_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type': $eq_html .= '<td>Equipment</td>'; break;
					case 'Description': $eq_html .= '<td>'.$type.'<br>'.$heading.'</td>'; break;
					case 'UOM': $eq_html .= '<td>'.$uom.'</td>'; break;
					case 'Quantity': $eq_html .= '<td>'.$qty.'</td>'; break;
					case 'Price': $eq_html .= '<td>'.number_format((float)$cust_price, 2, '.', '').'</td>'; break;
					case 'Total': $eq_html .= '<td>'.number_format((float)$rc_total, 2, '.', '').'</td>'; break;
					case 'Total X 4': $eq_html .= '<td>'.number_format((float)$rc_total * 4, 2, '.', '').'</td>'; $total4 = true; break;
				}
			}
            $eq_html .= '</tr>';

            $color_off = '';
			$plus_minus = $cust_price - $cost;
            $financial_plus_minus += $plus_minus;
            if($plus_minus < 0) {
                $color_off = 'style = "color:red; "';
            } else {
                $color_off = 'style = "color:green; "';
            }
			$plus_minus = abs($plus_minus);
			$review_profit_loss .= '<tr><td>Equipment</td>
				<td>'.$type.' : '.$heading.'</td>
				<td>'.$description.'</td>
				<td>'.$uom.'</td>
				<td>'.$qty.'</td>
				<td>$'.$cost.'</td>
				<td>'.number_format((1-(float)$cost/(float)$cust_price)*100, 2, '.', '').'%</td>
				<td>$'.number_format((float)$cust_price, 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.$rc_total.'</td></tr>';
        }
        //$j++;
    }

    //Labour
    $j = 0;

    $total_rc_labour = $_POST['total_rc_labour'];
    for($j=0;$j<=$total_rc_labour;$j++) {
        if($_POST['crc_labour_qty_'.$j] != '' && $_POST['crc_labour_qty_'.$j] != '0') {
            $tile_name = 'Labour';
            $companyrcid = filter_var($_POST['crc_labour_companyrcid_'.$j],FILTER_SANITIZE_STRING);
            $type = filter_var($_POST['crc_labour_type_'.$j],FILTER_SANITIZE_STRING);
            $heading = filter_var($_POST['crc_labour_heading_'.$j],FILTER_SANITIZE_STRING);
            $description = filter_var($_POST['crc_labour_description_'.$j],FILTER_SANITIZE_STRING);
            $uom = filter_var($_POST['crc_labour_uom_'.$j],FILTER_SANITIZE_STRING);
            $cost = filter_var($_POST['crc_labour_cost_'.$j],FILTER_SANITIZE_STRING);
            $cust_price = filter_var($_POST['crc_labour_cust_price_'.$j],FILTER_SANITIZE_STRING);
            $qty = filter_var($_POST['crc_labour_qty_'.$j],FILTER_SANITIZE_STRING);
            $rc_total = filter_var($_POST['crc_labour_total_'.$j],FILTER_SANITIZE_STRING);

            $profit = filter_var($_POST['crc_labour_profit_'.$j],FILTER_SANITIZE_STRING);
            $margin = filter_var($_POST['crc_labour_margin_'.$j],FILTER_SANITIZE_STRING);

            $query_insert_labour = "INSERT INTO `estimate_company_rate_card` (`estimateid`, `companyrcid`, `tile_name`, `rc_type`, `heading`, `description`, `uom`, `cost`, `cust_price`, `qty`, `rc_total`,`profit`,`margin`) VALUES ('$estimateid', '$companyrcid', '$tile_name','$type', '$heading' , '$description', '$uom', '$cost', '$cust_price', '$qty', '$rc_total','$profit','$margin')";
            $result_insert_labour = mysqli_query($dbc, $query_insert_labour);

            $before_change = '';
            $history = "Estimates company rate card entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $total_price += $rc_total;
            $total_products += $rc_total;
            $products_total += $qty;
            $products_price_total +=$cust_price;
            //$total_price += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];
            //$total_labour += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];

            //$labour_total += $_POST['mestimateqty'][$j];
            //$labour_price_total += $_POST['mestimateprice'][$j];

            $l_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type': $l_html .= '<td>Labour</td>'; break;
					case 'Description': $l_html .= '<td>'.$type.'<br>'.$heading.'</td>'; break;
					case 'UOM': $l_html .= '<td>'.$uom.'</td>'; break;
					case 'Quantity': $l_html .= '<td>'.$qty.'</td>'; break;
					case 'Price': $l_html .= '<td>'.number_format((float)$cust_price, 2, '.', '').'</td>'; break;
					case 'Total': $l_html .= '<td>'.number_format((float)$rc_total, 2, '.', '').'</td>'; break;
					case 'Total X 4': $l_html .= '<td>'.number_format((float)$rc_total * 4, 2, '.', '').'</td>'; $total4 = true; break;
				}
			}
            $l_html .= '</tr>';

            $color_off = '';
			$plus_minus = $cust_price - $cost;
            $financial_plus_minus += $plus_minus;
            if($plus_minus < 0) {
                $color_off = 'style = "color:red; "';
            } else {
                $color_off = 'style = "color:green; "';
            }
			$plus_minus = abs($plus_minus);
			$review_profit_loss .= '<tr><td>Labour</td>
				<td>'.$type.' : '.$heading.'</td>
				<td>'.$description.'</td>
				<td>'.$uom.'</td>
				<td>'.$qty.'</td>
				<td>$'.$cost.'</td>
				<td>'.number_format((1-(float)$cost/(float)$cust_price)*100, 2, '.', '').'%</td>
				<td>$'.number_format((float)$cust_price, 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.$rc_total.'</td></tr>';

        }
        //$j++;
    }

    //Custom Accordions
	$acc_config = mysqli_fetch_array(mysqli_query($dbc,"SELECT `custom_accordions` FROM `field_config_estimate`"))['custom_accordions'];
	$accordions = explode('#*#',$acc_config);
	$acc_total = 0;
	$acc_fields = 0;
	$width_factor = 0;
	$first_acc = explode(',',$accordions[0]);

	$field_order = get_config($dbc, 'estimate_field_order');
	if($field_order == '') {
		$field_order = 'Type***#*#Heading***#*#Description***#*#UOM***Unit of Measure#*#Quantity***Qty#*#Cost***#*#Margin***% Margin#*#Profit***$ Profit#*#Price***Unit Price#*#Total***Line Total';
	}
	$field_order = explode('#*#',$field_order);

    $accordion_head = '<table border="1px" style="padding:3px; border:1px solid black; width:100%;">
            <tr nobr="true" style="background-color:lightgrey; color:black;">';
	foreach($field_order as $field_data) {
		switch(explode('***',$field_data)[0]) {
			case 'Description':
				$width_factor += 4;
			case 'Type':
			case 'UOM':
			case 'Quantity':
			case 'Price':
			case 'Total':
			case 'Total X 4':
				$width_factor += 1;
				$acc_fields++;
				break;
		}
	}
	$width_factor = round(100 / $width_factor,2);
	foreach($field_order as $field_data) {
		$data = explode('***',$field_data);
		if($data[1] == '') {
			$data[1] = $data[0];
		}
		switch($data[0]) {
			case 'Type':
				$accordion_head .= '<th style="width:'.$width_factor.'%;">'.$data[1].'</th>';
				break;
			case 'Description':
				$accordion_head .= '<th style="width:'.($width_factor*5).'%;">'.$data[1].'</th>';
				break;
			case 'UOM':
				$accordion_head .= '<th style="width:'.$width_factor.'%;">'.$data[1].'</th>';
				break;
			case 'Quantity':
				$accordion_head .= '<th style="width:'.$width_factor.'%;">'.$data[1].'</th>';
				break;
			case 'Price':
				$accordion_head .= '<th style="width:'.$width_factor.'%;">'.$data[1].'</th>';
				break;
			case 'Total':
				$accordion_head .= '<th style="width:'.$width_factor.'%;">'.$data[1].'</th>';
				break;
			case 'Total X 4':
				$accordion_head .= '<th style="width:'.$width_factor.'%;">'.$data[1].'</th>';
				$total4 = true;
				break;
		}
	}
	$accordion_head .= '</tr>';
	$accordion_title = [];
	$accordion_content = [];
	foreach($accordions as $num => $accordion):
		$current_total = 0;
		$config_arr = explode(',',$accordion);
		$name = $config_arr[0];
		$accordion_title[$num] .= '<tr><th colspan="'.$acc_fields.'" style="text-align:center;">'.$name.'</th></tr>';
		$id = str_replace(' ','',strtolower($name));
		$j = 0;

		$total_rc_accordion = $_POST['total_rc_'.$id];
		for($j=0;$j<=$total_rc_accordion;$j++) {
			if($_POST['crc_'.$id.'_qty_'.$j] != '' && $_POST['crc_'.$id.'_qty_'.$j] != '0') {
				$tile_name = $name;
				$companyrcid = filter_var($_POST['crc_'.$id.'_companyrcid_'.$j],FILTER_SANITIZE_STRING);
				$type = filter_var($_POST['crc_'.$id.'_type_'.$j],FILTER_SANITIZE_STRING);
				$type_key = $companyrcid.'_'.$type;
				$current_html = '';
				$heading = filter_var($_POST['crc_'.$id.'_heading_'.$j],FILTER_SANITIZE_STRING);
				$description = filter_var($_POST['crc_'.$id.'_description_'.$j],FILTER_SANITIZE_STRING);
				$uom = filter_var($_POST['crc_'.$id.'_uom_'.$j],FILTER_SANITIZE_STRING);
				$cost = filter_var($_POST['crc_'.$id.'_cost_'.$j],FILTER_SANITIZE_STRING);
				$cust_price = filter_var($_POST['crc_'.$id.'_cust_price_'.$j],FILTER_SANITIZE_STRING);
				$qty = filter_var($_POST['crc_'.$id.'_qty_'.$j],FILTER_SANITIZE_STRING);
				$rc_total = filter_var($_POST['crc_'.$id.'_total_'.$j],FILTER_SANITIZE_STRING);
				$crc_description = [];
				$arr_description = implode(' - ',array_filter([ $heading, $description ]));

				$profit = filter_var($_POST['crc_'.$id.'_profit_'.$j],FILTER_SANITIZE_STRING);
				$margin = filter_var($_POST['crc_'.$id.'_margin_'.$j],FILTER_SANITIZE_STRING);

				$query_insert_accordion = "INSERT INTO `estimate_company_rate_card` (`estimateid`, `companyrcid`, `tile_name`, `rc_type`, `heading`, `description`, `uom`, `cost`, `cust_price`, `qty`, `rc_total`,`profit`,`margin`) VALUES ('$estimateid', '$companyrcid', '$tile_name','$type', '$heading' , '$description', '$uom', '$cost', '$cust_price', '$qty', '$rc_total','$profit','$margin')";
				$result_insert_accordion = mysqli_query($dbc, $query_insert_accordion);

        $before_change = '';
        $history = "Estimates company rate card entry has been added. <br />";
        add_update_history($dbc, 'estimates_history', $history, '', $before_change);
        
				$total_price += $rc_total;

				if(empty($accordion_type_totals[$type_key])) {
					$accordion_type_totals[$type_key] = 0;
				}
				$accordion_type_totals[$type_key] += round((float)$rc_total,2);

				$current_html .= '<tr nobr="true">';
				foreach($field_order as $field_data) {
					$data = explode('***',$field_data);
					if($data[1] == '') {
						$data[1] = $data[0];
					}
					switch($data[0]) {
						case 'Type': $current_html .= '<td style="width:'.$width_factor.'%;">'.$type.'</td>'; break;
						case 'Description': $current_html .= '<td style="width:'.($width_factor * 5).'%;">'.$arr_description.'</td>'; break;
						case 'UOM': $current_html .= '<td style="width:'.$width_factor.'%;">'.$uom.'</td>'; break;
						case 'Quantity': $current_html .= '<td style="width:'.$width_factor.'%;">'.$qty.'</td>'; break;
						case 'Price': $current_html .= '<td style="width:'.$width_factor.'%;">'.number_format((float)$cust_price, 2, '.', '').'</td>'; break;
						case 'Total': $current_html .= '<td style="width:'.$width_factor.'%;">'.number_format((float)$rc_total, 2, '.', '').'</td>'; break;
						case 'Total X 4': $current_html .= '<td style="width:'.$width_factor.'%;">'.number_format((float)$rc_total * 4, 2, '.', '').'</td>'; $total4 = true; break;
					}
				}
				$current_html .= '</tr>';
				$accordion_total += round((float)$rc_total,2);
				$current_total += round((float)$rc_total,2);

				$color_off = '';
				$plus_minus = $cust_price - $cost;
				$financial_plus_minus += $plus_minus;
				if($plus_minus < 0) {
					$color_off = 'style = "color:red; "';
				} else {
					$color_off = 'style = "color:green; "';
				}
				$plus_minus = abs($plus_minus);
				$review_profit_loss .= '<tr><td>'.$name.'</td>
					<td>'.$type.' : '.$heading.'</td>
					<td>'.$description.'</td>
					<td>'.$uom.'</td>
					<td>'.$qty.'</td>
					<td>$'.$cost.'</td>
					<td>'.number_format((1-(float)$cost/(float)$cust_price)*100, 2, '.', '').'%</td>
					<td>$'.number_format((float)$cust_price, 2, '.', '').'</td>
					<td '.$color_off.'>$'.$plus_minus.'</td>
					<td>$'.$rc_total.'</td></tr>';

			}
			$accordion_content[$num] .= $current_html;
			$accordion_types[$type_key] .= $current_html;
		}
		foreach($accordion_types as $type_key => $accordion_type_html) {
			$rcid = explode('_',$type_key)[0];
			$rate_card = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `company_rate_card` WHERE `companyrcid`='$rcid'"));
			$accordion_types[$type_key] = str_replace('</th>', ': '.$rate_card['rate_card_name'].' ('.$rate_card['rate_card_types'].')</th>', $accordion_title[$num]).$accordion_type_html;
		}
		$category_totals .= '<tr nobr="true">';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for '.$name.'</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
				case 'Total X 4': $category_totals .= '<td>'.number_format((float)$current_total * 4, 2, '.', '').'</td>'; $total4 = true; break;
			}
		}
		$category_totals .= '</tr>';
	endforeach;
