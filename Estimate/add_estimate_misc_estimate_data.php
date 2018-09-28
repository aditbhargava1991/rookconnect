<?php
    $miscestimteid = $_GET['estimateid'];

    //Products
    $k=0;
    foreach($_POST['ptype_misc'] as $pidmisc) {
        if($_POST['ptotalmisc'][$k] != 0) {
            $miscestimteid = $_GET['estimateid'];
            $miscacco = 'Product';
            $misctype = $_POST['ptype_misc'][$k];
            $mischead = $_POST['pheadmisc'][$k];
            $miscdisc = $_POST['pdisc_misc'][$k];
            $miscuom = $_POST['puom_misc'][$k];
            $misccost = $_POST['ptotalmisc'][$k];
            $miscesprice = $_POST['pestimatepricemisc'][$k];
            $misceqty = $_POST['pqtymisc'][$k];
            $misctotal = $_POST['ptotalmisc'][$k];
            $miscprofit = $_POST['pprofitmisc'][$k];
            $miscmargin = $_POST['pmarginmisc'][$k];

			$estiamtetabid = '';
			if($_GET['estimatetabid']) {
				$estiamtetabid = $_GET['estimatetabid'];
			}

            $query_insert_ticket = "INSERT INTO `estimate_misc` (`estimateid`, `accordion`, `type`, `heading`, `description`, `uom`, `cost`, `estimate_price`, `qty`, `total`, `profit`, `margin`, `estimate_tab_id`)
            VALUES ('$miscestimteid', '$miscacco', '$misctype' , '$mischead', '$miscdisc', '$miscuom', '$misccost', '$miscesprice', '$misceqty', '$misctotal', '$miscprofit', '$miscmargin', '$estiamtetabid');";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);

            $before_change = '';
            $history = "Estimates misc entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $k++;
        }
    }
	$sql = "SELECT * FROM estimate_misc WHERE accordion='Product' AND estimateid=" . $estimateid;
	$query_misc_rc = mysqli_query($dbc,$sql);
	$misc_rc = 0;
	while($misc_row_rc = mysqli_fetch_array($query_misc_rc)) {
		$p_html .= '<tr nobr="true">';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $p_html .= '<td>Product</td>'; break;
				case 'Description': $p_html .= '<td>'.$misc_row_rc['type'].'<br>'.$misc_row_rc['head'].'</td>'; break;
				case 'UOM': $p_html .= '<td>'.$misc_row_rc['uom'].'</td>'; break;
				case 'Quantity': $p_html .= '<td>'.$misc_row_rc['qty'].'</td>'; break;
				case 'Price': $p_html .= '<td>$'.number_format((float)$misc_row_rc['estimate_price'], 2, '.', '').'</td>'; break;
				case 'Total': $p_html .= '<td>$'.number_format((float)$misc_row_rc['total'], 2, '.', '').'</td>'; break;
				case 'Total X 4': $p_html .= '<td>$'.number_format((float)$misc_row_rc['total'] * 4, 2, '.', '').'</td>'; $total4 = true; break;
			}
		}
		$p_html .= '</tr>';

		$total_price += $misc_row_rc['total'];
		$total_product += $misc_row_rc['total'];
		$products_total += $misc_row_rc['qty'];
		$products_price_total +=$misc_row_rc['estimate_price'];

		$color_off = '';
		$plus_minus = $misc_row_rc['estimate_price'] - $misc_row_rc['cost'];
		$financial_plus_minus += $plus_minus;
		if($plus_minus < 0) {
			$color_off = 'style = "color:red; "';
		} else {
			$color_off = 'style = "color:green; "';
		}
		$plus_minus = abs($plus_minus);
		$review_profit_loss .= '<tr><td>Product</td>
			<td>'.$misc_row_rc['heading'].'</td>
			<td>'.$misc_row_rc['description'].'</td>
			<td>'.$misc_row_rc['uom'].'</td>
			<td>'.$misc_row_rc['qty'].'</td>
			<td>$'.$misc_row_rc['cost'].'</td>
			<td>'.$misc_row_rc['margin'].'%</td>
			<td>$'.number_format((float)$misc_row_rc['estimate_price'], 2, '.', '').'</td>
			<td '.$color_off.'>$'.$plus_minus.'</td>
			<td>$'.$misc_row_rc['total'].'</td></tr>';
	}

    // Inventory
    $k=0;
    foreach($_POST['intype_misc'] as $inidmisc) {
        if($_POST['intotalmisc'][$k] != 0) {
            $miscestimteid = $_GET['estimateid'];
            $miscacco = 'Inventory';
            $misctype = $_POST['intype_misc'][$k];
            $mischead = $_POST['inheadmisc'][$k];
            $miscdisc = $_POST['indisc_misc'][$k];
            $miscuom = $_POST['inuom_misc'][$k];
            $misccost = $_POST['incostmisc'][$k];
            $miscesprice = $_POST['inestimatepricemisc'][$k];
            $misceqty = $_POST['inqtymisc'][$k];
            $misctotal = $_POST['intotalmisc'][$k];
            $miscprofit = $_POST['inprofit_misc'][$k];
            $miscmargin = $_POST['inmarginmisc'][$k];

            $estiamtetabid = '';
			if($_GET['estimatetabid']) {
				$estiamtetabid = $_GET['estimatetabid'];
			}

            $query_insert_ticket = "INSERT INTO `estimate_misc` (`estimateid`, `accordion`, `type`, `heading`, `description`, `uom`, `cost`, `estimate_price`, `qty`, `total`, `profit`, `margin`, `estimate_tab_id`)
            VALUES ('$miscestimteid', '$miscacco', '$misctype' , '$mischead', '$miscdisc', '$miscuom', '$misccost', '$miscesprice', '$misceqty', '$misctotal', '$miscprofit', '$miscmargin', '$estiamtetabid');";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);

            $before_change = '';
            $history = "Estimates misc entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $k++;
        }
    }
	$sql = "SELECT * FROM estimate_misc WHERE accordion='Inventory' AND estimateid=" . $estimateid;
	$query_misc_rc = mysqli_query($dbc,$sql);
	$misc_rc = 0;
	while($misc_row_rc = mysqli_fetch_array($query_misc_rc)) {
		$in_html .= '<tr nobr="true">';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $in_html .= '<td>Inventory</td>'; break;
				case 'Description': $in_html .= '<td>'.$misc_row_rc['type'].'<br>'.$misc_row_rc['head'].'</td>'; break;
				case 'UOM': $in_html .= '<td>'.$misc_row_rc['uom'].'</td>'; break;
				case 'Quantity': $in_html .= '<td>'.$misc_row_rc['qty'].'</td>'; break;
				case 'Price': $in_html .= '<td>$'.number_format((float)$misc_row_rc['estimate_price'], 2, '.', '').'</td>'; break;
				case 'Total': $in_html .= '<td>$'.number_format((float)$misc_row_rc['total'], 2, '.', '').'</td>'; break;
				case 'Total X 4': $in_html .= '<td>$'.number_format((float)$misc_row_rc['total'] * 4, 2, '.', '').'</td>'; $total4 = true; break;
			}
		}
		$in_html .= '</tr>';

		$total_price += $misc_row_rc['total'];
		$total_inventory += $misc_row_rc['total'];
		$inventory_total += $misc_row_rc['qty'];
		$inventory_price_total +=$misc_row_rc['estimate_price'];

		$color_off = '';
		$plus_minus = $misc_row_rc['estimate_price'] - $misc_row_rc['cost'];
		$financial_plus_minus += $plus_minus;
		if($plus_minus < 0) {
			$color_off = 'style = "color:red; "';
		} else {
			$color_off = 'style = "color:green; "';
		}
		$plus_minus = abs($plus_minus);
		$review_profit_loss .= '<tr><td>Inventory</td>
			<td>'.$misc_row_rc['heading'].'</td>
			<td>'.$misc_row_rc['description'].'</td>
			<td>'.$misc_row_rc['uom'].'</td>
			<td>'.$misc_row_rc['qty'].'</td>
			<td>$'.$misc_row_rc['cost'].'</td>
			<td>'.$misc_row_rc['margin'].'%</td>
			<td>$'.number_format((float)$misc_row_rc['estimate_price'], 2, '.', '').'</td>
			<td '.$color_off.'>$'.$plus_minus.'</td>
			<td>$'.$misc_row_rc['total'].'</td></tr>';
	}

    //Equipemt
    $k=0;
    foreach($_POST['eqtype_misc'] as $eqidmisc) {
        if($_POST['eqtotalmisc'][$k] != 0) {
            $miscacco = 'Equipment';
            $misctype = $_POST['eqtype_misc'][$k];
            $mischead = $_POST['eqheadmisc'][$k];
            $miscdisc = $_POST['eqdisc_misc'][$k];
            $miscuom = $_POST['equom_misc'][$k];
            $misccost = $_POST['eqcostmisc'][$k];
            $miscesprice = $_POST['eqestimatepricemisc'][$k];
            $misceqty = $_POST['eqqtymisc'][$k];
            $misctotal = $_POST['eqtotalmisc'][$k];
            $miscprofit = $_POST['eqprofitmisc'][$k];
            $miscmargin = $_POST['eqmarginmisc'][$k];

            $estiamtetabid = '';
			if($_GET['estimatetabid']) {
				$estiamtetabid = $_GET['estimatetabid'];
			}

            $query_insert_ticket = "INSERT INTO `estimate_misc` (`estimateid`, `accordion`, `type`, `heading`, `description`, `uom`, `cost`, `estimate_price`, `qty`, `total`, `profit`, `margin`, `estimate_tab_id`)
            VALUES ('$miscestimteid', '$miscacco', '$misctype' , '$mischead', '$miscdisc', '$miscuom', '$misccost', '$miscesprice', '$misceqty', '$misctotal', '$miscprofit', '$miscmargin', '$estiamtetabid');";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);

            $before_change = '';
            $history = "Estimates misc entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $k++;
        }
    }
	$sql = "SELECT * FROM estimate_misc WHERE accordion='Equipment' AND estimateid=" . $estimateid;
	$query_misc_rc = mysqli_query($dbc,$sql);
	$misc_rc = 0;
	while($misc_row_rc = mysqli_fetch_array($query_misc_rc)) {
		$eq_html .= '<tr nobr="true">';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $eq_html .= '<td>Equipment</td>'; break;
				case 'Description': $eq_html .= '<td>'.$misc_row_rc['type'].'<br>'.$misc_row_rc['head'].'</td>'; break;
				case 'UOM': $eq_html .= '<td>'.$misc_row_rc['uom'].'</td>'; break;
				case 'Quantity': $eq_html .= '<td>'.$misc_row_rc['qty'].'</td>'; break;
				case 'Price': $eq_html .= '<td>$'.number_format((float)$misc_row_rc['estimate_price'], 2, '.', '').'</td>'; break;
				case 'Total': $eq_html .= '<td>$'.number_format((float)$misc_row_rc['total'], 2, '.', '').'</td>'; break;
				case 'Total X 4': $eq_html .= '<td>$'.number_format((float)$misc_row_rc['total'] * 4, 2, '.', '').'</td>'; $total4 = true; break;
			}
		}
		$eq_html .= '</tr>';

		$total_price += $misc_row_rc['total'];
		$total_equipment += $misc_row_rc['total'];
		$equipment_total += $misc_row_rc['qty'];
		$equipment_price_total +=$misc_row_rc['estimate_price'];

		$color_off = '';
		$plus_minus = $misc_row_rc['estimate_price'] - $misc_row_rc['cost'];
		$financial_plus_minus += $plus_minus;
		if($plus_minus < 0) {
			$color_off = 'style = "color:red; "';
		} else {
			$color_off = 'style = "color:green; "';
		}
		$plus_minus = abs($plus_minus);
		$review_profit_loss .= '<tr><td>Equipment</td>
			<td>'.$misc_row_rc['heading'].'</td>
			<td>'.$misc_row_rc['description'].'</td>
			<td>'.$misc_row_rc['uom'].'</td>
			<td>'.$misc_row_rc['qty'].'</td>
			<td>$'.$misc_row_rc['cost'].'</td>
			<td>'.$misc_row_rc['margin'].'%</td>
			<td>$'.number_format((float)$misc_row_rc['estimate_price'], 2, '.', '').'</td>
			<td '.$color_off.'>$'.$plus_minus.'</td>
			<td>$'.$misc_row_rc['total'].'</td></tr>';
	}

    //Labour
    $k=0;
    foreach($_POST['ltype_misc'] as $lidmisc) {
        if($_POST['ltotalmisc'][$k] != 0) {
			if($k == 0) {
			}

            $miscacco = 'Labour';
            $misctype = $_POST['ltype_misc'][$k];
            $mischead = $_POST['lheadmisc'][$k];
            $miscdisc = $_POST['ldisc_misc'][$k];
            $miscuom = $_POST['luom_misc'][$k];
            $misccost = $_POST['lcostmisc'][$k];
            $miscesprice = $_POST['lestimatepricemisc'][$k];
            $misceqty = $_POST['lqtymisc'][$k];
            $misctotal = $_POST['ltotalmisc'][$k];
            $miscprofit = $_POST['lprofitmisc'][$k];
            $miscmargin = $_POST['lmarginmisc'][$k];

            $estiamtetabid = '';
			if($_GET['estimatetabid']) {
				$estiamtetabid = $_GET['estimatetabid'];
			}

            $query_insert_ticket = "INSERT INTO `estimate_misc` (`estimateid`, `accordion`, `type`, `heading`, `description`, `uom`, `cost`, `estimate_price`, `qty`, `total`, `profit`, `margin`, `estimate_tab_id`)
            VALUES ('$miscestimteid', '$miscacco', '$misctype' , '$mischead', '$miscdisc', '$miscuom', '$misccost', '$miscesprice', '$misceqty', '$misctotal', '$miscprofit', '$miscmargin', '$estiamtetabid');";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);

            $before_change = '';
            $history = "Estimate misc has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);

            $k++;
        }
    }
	$sql = "SELECT * FROM estimate_misc WHERE accordion='Labour' AND estimateid=" . $estimateid;
	$query_misc_rc = mysqli_query($dbc,$sql);
	$misc_rc = 0;
	while($misc_row_rc = mysqli_fetch_array($query_misc_rc)) {
		$l_html .= '<tr nobr="true">';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $l_html .= '<td>Labour</td>'; break;
				case 'Description': $l_html .= '<td>'.$misc_row_rc['type'].'<br>'.$misc_row_rc['head'].'</td>'; break;
				case 'UOM': $l_html .= '<td>'.$misc_row_rc['uom'].'</td>'; break;
				case 'Quantity': $l_html .= '<td>'.$misc_row_rc['qty'].'</td>'; break;
				case 'Price': $l_html .= '<td>$'.number_format((float)$misc_row_rc['estimate_price'], 2, '.', '').'</td>'; break;
				case 'Total': $l_html .= '<td>$'.number_format((float)$misc_row_rc['total'], 2, '.', '').'</td>'; break;
				case 'Total X 4': $l_html .= '<td>$'.number_format((float)$misc_row_rc['total'] * 4, 2, '.', '').'</td>'; $total4 = true; break;
			}
		}
		$l_html .= '</tr>';

		$total_price += $misc_row_rc['total'];
		$total_labour += $misc_row_rc['total'];
		$labour_total += $misc_row_rc['qty'];
		$labour_price_total +=$misc_row_rc['estimate_price'];

		$color_off = '';
		$plus_minus = $misc_row_rc['estimate_price'] - $misc_row_rc['cost'];
		$financial_plus_minus += $plus_minus;
		if($plus_minus < 0) {
			$color_off = 'style = "color:red; "';
		} else {
			$color_off = 'style = "color:green; "';
		}
		$plus_minus = abs($plus_minus);
		$review_profit_loss .= '<tr><td>Labour</td>
			<td>'.$misc_row_rc['heading'].'</td>
			<td>'.$misc_row_rc['description'].'</td>
			<td>'.$misc_row_rc['uom'].'</td>
			<td>'.$misc_row_rc['qty'].'</td>
			<td>$'.$misc_row_rc['cost'].'</td>
			<td>'.$misc_row_rc['margin'].'%</td>
			<td>$'.number_format((float)$misc_row_rc['estimate_price'], 2, '.', '').'</td>
			<td '.$color_off.'>$'.$plus_minus.'</td>
			<td>$'.$misc_row_rc['total'].'</td></tr>';
	}

    //Custom Accordions
	$accordions = explode('#*#',mysqli_fetch_array(mysqli_query($dbc,"SELECT `custom_accordions` FROM `field_config_estimate`"))['custom_accordions']);
	foreach($accordions as $num => $accordion):
		$current_html = '';
		$config_arr = explode(',',$accordion);
		$config_labels = explode('*#*',$acc_labels[$num]);
		$name = $config_arr[0];
		$id = str_replace(' ','',strtolower($name));
		$k=0;
		foreach($_POST[$id.'type_misc'] as $lidmisc) {
			if($_POST[$id.'totalmisc'][$k] != 0) {
				if($k == 0) {
				}

				$miscacco = $name;
				$misctype = $_POST[$id.'type_misc'][$k];
				$mischead = $_POST[$id.'headmisc'][$k];
				$miscdisc = $_POST[$id.'disc_misc'][$k];
				$miscuom = $_POST[$id.'uom_misc'][$k];
				$misccost = $_POST[$id.'costmisc'][$k];
				$miscesprice = $_POST[$id.'estimatepricemisc'][$k];
				$misceqty = $_POST[$id.'qtymisc'][$k];
				$misctotal = $_POST[$id.'totalmisc'][$k];
				$miscprofit = $_POST[$id.'profitmisc'][$k];
				$miscmargin = $_POST[$id.'marginmisc'][$k];

				$estiamtetabid = '';
				if($_GET['estimatetabid']) {
					$estiamtetabid = $_GET['estimatetabid'];
				}

				$query_insert_ticket = "INSERT INTO `estimate_misc` (`estimateid`, `accordion`, `type`, `heading`, `description`, `uom`, `cost`, `estimate_price`, `qty`, `total`, `profit`, `margin`, `estimate_tab_id`)
				VALUES ('$miscestimteid', '$miscacco', '$misctype' , '$mischead', '$miscdisc', '$miscuom', '$misccost', '$miscesprice', '$misceqty', '$misctotal', '$miscprofit', '$miscmargin', '$estiamtetabid');";
				$result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);

        $before_change = '';
        $history = "Estimates misc entry has been added. <br />";
        add_update_history($dbc, 'estimates_history', $history, '', $before_change);

				$k++;
			}
		}
		$sql = "SELECT * FROM estimate_misc WHERE accordion='".$name."' AND estimateid=" . $estimateid;
		$query_misc_rc = mysqli_query($dbc,$sql);
		$misc_rc = 0;
		while($misc_row_rc = mysqli_fetch_array($query_misc_rc)) {
			$arr_description = implode(' - ',array_filter([ $misc_row_rc['head'], $misc_row_rc['description'] ]));
			$current_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type': $current_html .= '<td>'.$misc_row_rc['type'].'</td>'; break;
					case 'Description': $current_html .= '<td>'.$arr_description.'</td>'; break;
					case 'UOM': $current_html .= '<td>'.$misc_row_rc['uom'].'</td>'; break;
					case 'Quantity': $current_html .= '<td>'.$misc_row_rc['qty'].'</td>'; break;
					case 'Price': $current_html .= '<td>'.number_format((float)$misc_row_rc['estimate_price'], 2, '.', '').'</td>'; break;
					case 'Total': $current_html .= '<td>'.number_format((float)$misc_row_rc['total'], 2, '.', '').'</td>'; break;
					case 'Total X 4': $current_html .= '<td>'.number_format((float)$misc_row_rc['total'] * 4, 2, '.', '').'</td>'; $total4 = true; break;
				}
			}
			$current_html .= '</tr>';
			$accordion_total += round((float)$misc_row_rc['total'],2);

			$total_price += $misc_row_rc['total'];
			$total_labour += $misc_row_rc['total'];
			$labour_total += $misc_row_rc['qty'];
			$labour_price_total += $misc_row_rc['estimate_price'];

			$color_off = '';
			$plus_minus = $misc_row_rc['estimate_price'] - $misc_row_rc['cost'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
			$plus_minus = abs($plus_minus);
			$review_profit_loss .= '<tr><td>'.$name.'</td>
				<td>'.$misc_row_rc['heading'].'</td>
				<td>'.$misc_row_rc['description'].'</td>
				<td>'.$misc_row_rc['uom'].'</td>
				<td>'.$misc_row_rc['qty'].'</td>
				<td>$'.$misc_row_rc['cost'].'</td>
				<td>'.$misc_row_rc['margin'].'%</td>
				<td>$'.number_format((float)$misc_row_rc['estimate_price'], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.$misc_row_rc['total'].'</td></tr>';
		}
		$accordion_content[$num] .= $current_html;
		foreach($accordion_types as $type_key => $accordion_type_html) {
			if(!isset($accordion_type_totals[$type_key])) {
				$accordion_type_totals[$type_key] = 0;
			}
			$accordion_type_totals[$type_key] += round((float)$misc_row_rc['total'],2);
			$accordion_types[$type_key] .= $current_html;
		}
	endforeach;
	$accordion_foot = '<tr><td colspan="'.($acc_fields - 1 - ($total4 ? 1 : 0)).'" border="0px" style="border-left:0px white hidden; border-bottom:0px white hidden;"><p style="text-align:right;">Sub Total</p></td><td>$'.number_format((float)$accordion_total, 2, '.', '').'</td>'.($total4 ? '<td>$'.number_format((float)$accordion_total * 4, 2, '.', '').'</td>' : '').'</tr></table>';
	foreach($accordion_content as $key => $content) {
		$accordion_html .= $accordion_title[$key].$content;
	}
	if($accordion_html != '') {
		$accordion_html = $accordion_head.$accordion_html.$accordion_foot;
	}
	foreach($accordion_types as $type_key => $accordion_type_html) {
		$number = $accordion_type_totals[$type_key];
		$accordion_types[$type_key] = $accordion_head.$accordion_type_html.'<tr><td colspan="'.($acc_fields - 1 - ($total4 ? 1 : 0)).'" border="0px" style="border-left:0px white hidden; border-bottom:0px white hidden;"><p style="text-align:right;">Sub Total</p></td><td>$'.number_format((float)$number,2,'.','').'</td>'.($total4 ? '<td>$'.number_format((float)$number * 4, 2, '.', '').'</td>' : '').'</tr></table>';
	}
