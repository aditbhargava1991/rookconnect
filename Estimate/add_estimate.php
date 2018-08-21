<?php
//Rate CArd Tiles

include ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);
$main_page = isset($_POST['referer']) ? $_POST['referer'] : $_SERVER['HTTP_REFERER'];
if(!empty($_GET['from'])) {
	$main_page = $_GET['from'];
}
else if(strpos($main_page,'/estimate.php') === false) {
	$main_page = 'estimate.php';
}
$field_order = get_config($dbc, 'estimate_field_order');
if($field_order == '') {
	$field_order = 'Type***#*#Heading***#*#Description***#*#UOM***Unit of Measure#*#Quantity***Qty#*#Cost***#*#Margin***% Margin#*#Profit***$ Profit#*#Price***Unit Price#*#Total***Line Total';
}
$field_order = explode('#*#',$field_order);

$software_url = $_SERVER['SERVER_NAME'];
if($software_url == 'www.washtechsoftware.com' || $software_url == 'washtech.freshfocuscrm.com') {
	$washtech_software_checker = 'true';
}

if (isset($_POST['save'])) {
    $who_added = $_SESSION['contactid'];
    $when_added = date('Y-m-d');

    $budget_price = '';
    for($i=0; $i<=16; $i++) {
        $budget_price .= $_POST['budget_price_'.$i].'*#*';
    }
    $budget_price .= $_POST['total_budget'];

    $assign_staffid = ','.implode(',',$_POST['assign_staffid']).',';
    $businessid = $_POST['businessid'];
	if($businessid == 'ADD NEW') {
		mysqli_query($dbc, "INSERT INTO `contacts` (`category`, `name`) VALUES ('Business', '".encryptIt(filter_var($_POST['new_business'],FILTER_SANITIZE_STRING))."')");
		$businessid = mysqli_insert_id($dbc);
	}
    $clientid = filter_var($_POST['estimateclientid'],FILTER_SANITIZE_STRING);
	if($clientid == 'ADD NEW') {
		$new_name = explode(' ', filter_var($_POST['new_contact_name'],FILTER_SANITIZE_STRING));
		$new_last = $new_name[count($new_name)];
		unset($new_name[count($new_name)]);
		$new_first = implode(' ', $new_name);
		mysqli_query($dbc, "INSERT INTO `contacts` (`category`, `first_name`, `last_name`, `businessid`) VALUES ('Customers', '".encryptIt($new_first)."', '".encryptIt($dbc, $new_last)."', '$businessid')");
		$clientid = mysqli_insert_id($dbc);
	}
    $siteid = filter_var($_POST['siteid'],FILTER_SANITIZE_STRING);
	if($siteid == 'ADD NEW') {
		mysqli_query($dbc, "INSERT INTO `field_sites` (`clientid`, `site_name`) VALUES ('$businessid', '".filter_var($_POST['new_site_location'],FILTER_SANITIZE_STRING)."')");
		$siteid = mysqli_insert_id($dbc);
	}
    $afe_number = filter_var($_POST['afe_number'],FILTER_SANITIZE_STRING);
    $payment_terms = filter_var($_POST['payment_terms'],FILTER_SANITIZE_STRING);
    $payment_due = filter_var($_POST['payment_due'],FILTER_SANITIZE_STRING);

    $ratecardid = filter_var($_POST['ratecardid'],FILTER_SANITIZE_STRING);
    $maincompanyrcid = filter_var(trim(implode(',',$_POST['companyrcid']),','),FILTER_SANITIZE_STRING);
    $ratecardtype = filter_var(trim(implode(',',$_POST['ratecardtype']),','),FILTER_SANITIZE_STRING);
    $estimatetype = implode(',', $_POST['estimatetype']);

    $created_date = $_POST['created_date'];
    $start_date = $_POST['start_date'];
    $expiry_date = $_POST['expiry_date'];
    $estimated_completed_date = $_POST['estimated_completed_date'];
    $completion_date = $_POST['completion_date'];

    $estimate_name = filter_var($_POST['estimate_name'],FILTER_SANITIZE_STRING);

    $history = decryptIt(decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Added on '.date('Y-m-d H:i:s').'<br>';

    $query_insert_customer = "INSERT INTO `estimate` (`businessid`, `clientid`, `siteid`, `afe_number`, `payment_terms`, `payment_due`, `ratecardid`, `companyrcid`, `rate_card_type`, `estimatetype`, `estimate_name`, `created_date`, `start_date`, `expiry_date`, `estimated_completed_date`, `completion_date`, `budget_price`, `history`, `assign_staffid`) VALUES ('$businessid', '$clientid', '$siteid', '$afe_number', '$payment_terms', '$payment_due', '$ratecardid', '$maincompanyrcid', '$ratecardtype', '$estimatetype', '$estimate_name', '$created_date', '$start_date', '$expiry_date', '$estimated_completed_date', '$completion_date', '$budget_price', '$history', '$assign_staffid')";
    $result_insert_customer = mysqli_query($dbc, $query_insert_customer);
    $estimateid = mysqli_insert_id($dbc);

		$before_change = '';
		$history = "Estimates entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);

    insert_day_overview($dbc, $who_added, 'Estimate', $when_added, '', 'Added Estimate '.$estimate_name);

	//Details
    $detail_detail = filter_var(htmlentities($_POST['detail_detail']),FILTER_SANITIZE_STRING);
    $detail_issue = filter_var(htmlentities($_POST['detail_issue']),FILTER_SANITIZE_STRING);
    $detail_problem = filter_var(htmlentities($_POST['detail_problem']),FILTER_SANITIZE_STRING);
    $detail_technical_uncertainty = filter_var(htmlentities($_POST['detail_technical_uncertainty']),FILTER_SANITIZE_STRING);
    $detail_base_knowledge = filter_var(htmlentities($_POST['detail_base_knowledge']),FILTER_SANITIZE_STRING);
    $detail_do = filter_var(htmlentities($_POST['detail_do']),FILTER_SANITIZE_STRING);
    $detail_already_known = filter_var(htmlentities($_POST['detail_already_known']),FILTER_SANITIZE_STRING);
    $detail_sources = filter_var(htmlentities($_POST['detail_sources']),FILTER_SANITIZE_STRING);
    $detail_current_designs = filter_var(htmlentities($_POST['detail_current_designs']),FILTER_SANITIZE_STRING);
    $detail_known_techniques = filter_var(htmlentities($_POST['detail_known_techniques']),FILTER_SANITIZE_STRING);
    $detail_review_needed = filter_var(htmlentities($_POST['detail_review_needed']),FILTER_SANITIZE_STRING);
    $detail_looking_to_achieve = filter_var(htmlentities($_POST['detail_looking_to_achieve']),FILTER_SANITIZE_STRING);
    $detail_plan = filter_var(htmlentities($_POST['detail_plan']),FILTER_SANITIZE_STRING);
    $detail_next_steps = filter_var(htmlentities($_POST['detail_next_steps']),FILTER_SANITIZE_STRING);
    $detail_learnt = filter_var(htmlentities($_POST['detail_learnt']),FILTER_SANITIZE_STRING);
    $detail_discovered = filter_var(htmlentities($_POST['detail_discovered']),FILTER_SANITIZE_STRING);
    $detail_tech_advancements = filter_var(htmlentities($_POST['detail_tech_advancements']),FILTER_SANITIZE_STRING);
    $detail_work = filter_var(htmlentities($_POST['detail_work']),FILTER_SANITIZE_STRING);
    $detail_adjustments_needed = filter_var(htmlentities($_POST['detail_adjustments_needed']),FILTER_SANITIZE_STRING);
    $detail_future_designs = filter_var(htmlentities($_POST['detail_future_designs']),FILTER_SANITIZE_STRING);
    $detail_check = filter_var(htmlentities($_POST['detail_check']),FILTER_SANITIZE_STRING);
    $detail_objective = filter_var(htmlentities($_POST['detail_objective']),FILTER_SANITIZE_STRING);
    $detail_gap = filter_var(htmlentities($_POST['detail_gap']),FILTER_SANITIZE_STRING);
    $detail_targets = filter_var(htmlentities($_POST['detail_targets']),FILTER_SANITIZE_STRING);
    $detail_audience = filter_var(htmlentities($_POST['detail_audience']),FILTER_SANITIZE_STRING);
    $detail_strategy = filter_var(htmlentities($_POST['detail_strategy']),FILTER_SANITIZE_STRING);
    $detail_desired_outcome = filter_var(htmlentities($_POST['detail_desired_outcome']),FILTER_SANITIZE_STRING);
    $detail_actual_outcome = filter_var(htmlentities($_POST['detail_actual_outcome']),FILTER_SANITIZE_STRING);
    $detail_note = filter_var(htmlentities($_POST['detail_note']),FILTER_SANITIZE_STRING);

    $query_insert_detail = "INSERT INTO `estimate_detail` (`estimateid`, `detail_detail`, `detail_issue`, `detail_problem`, `detail_gap`, `detail_technical_uncertainty`, `detail_base_knowledge`, `detail_do`, `detail_already_known`, `detail_sources`, `detail_current_designs`, `detail_known_techniques`, `detail_review_needed`, `detail_looking_to_achieve`, `detail_plan`, `detail_next_steps`, `detail_learnt`,  `detail_discovered`,  `detail_tech_advancements`, `detail_work`, `detail_adjustments_needed`, `detail_future_designs`, `detail_check`, `detail_objective`, `detail_targets`, `detail_audience`, `detail_strategy`, `detail_desired_outcome`, `detail_actual_outcome`, `detail_quote_note`) VALUES ('$estimateid', '$detail_detail', '$detail_issue', '$detail_problem', '$detail_gap', '$detail_technical_uncertainty', '$detail_base_knowledge', '$detail_do', '$detail_already_known', '$detail_sources', '$detail_current_designs', '$detail_known_techniques', '$detail_review_needed', '$detail_looking_to_achieve', '$detail_plan', '$detail_next_steps', '$detail_learnt',  '$detail_discovered',  '$detail_tech_advancements', '$detail_work',  '$detail_adjustments_needed', '$detail_future_designs', '$detail_check', '$detail_objective', '$detail_targets', '$detail_audience', '$detail_strategy', '$detail_desired_outcome', '$detail_actual_outcome', '$detail_note')";

    $result_insert_detail = mysqli_query($dbc, $query_insert_detail);

		$before_change = '';
		$history = "Estimates details entry has been added. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);

    $document = $_FILES["document"]["name"];
    for($i = 0; $i < count($_FILES['document']['name']); $i++) {
        if($document[$i] != '') {
            move_uploaded_file($_FILES["document"]["tmp_name"][$i], "download/" . $_FILES["document"]["name"][$i]) ;
            $query_insert_upload = "INSERT INTO `estimate_document` (`estimateid`, `upload`) VALUES ('$estimateid', '$document[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);

						$before_change = '';
            $history = "Estimates documents entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);
        }
    }

    if (!empty($_GET['from'])) {
        echo '<script type="text/javascript"> location.replace("' . $_GET['from'] . '?businessid=' . $businessid . '"); </script>';
    } else {
        echo '<script type="text/javascript">location.replace("add_estimate.php?estimateid='.$estimateid.'"); </script>';
    }

}

if (isset($_POST['submit'])) {
    $estimateid = $_POST['estimateid'];
    $businessid = get_estimate($dbc, $estimateid, 'businessid');
    $clientid = get_estimate($dbc, $estimateid, 'clientid');
    $siteid = $_POST['siteid'];
    $afe_number = filter_var($_POST['afe_number'],FILTER_SANITIZE_STRING);
    $payment_terms = filter_var($_POST['payment_terms'],FILTER_SANITIZE_STRING);
    $payment_due = filter_var($_POST['payment_due'],FILTER_SANITIZE_STRING);
    $who_added = $_SESSION['contactid'];
    $when_added = date('Y-m-d');
	$quote_mode = implode(',',$_POST['quote_mode']);
	$quote_multiple = $_POST['quote_multiple'];
	$category_totals = '';

    $budget_price = '';
    for($i=0; $i<=16; $i++) {
        $budget_price .= $_POST['budget_price_'.$i].'*#*';
    }
    $budget_price .= $_POST['total_budget'];

    $ratecardid = filter_var($_POST['ratecardid'],FILTER_SANITIZE_STRING);
    $maincompanyrcid = filter_var(trim(implode(',',$_POST['companyrcid']),','),FILTER_SANITIZE_STRING);
    $ratecardtype = filter_var(trim(implode(',',$_POST['ratecardtype']),','),FILTER_SANITIZE_STRING);
    $estimate_name = filter_var($_POST['estimate_name'],FILTER_SANITIZE_STRING);
    //$budget_price = filter_var($_POST['budget_price'],FILTER_SANITIZE_STRING);
    $total_price = 0;
	$total_usd = filter_var($_POST['summary_total_usd'],FILTER_SANITIZE_STRING);
	$total_cad = filter_var($_POST['summary_total_cad'],FILTER_SANITIZE_STRING);
    $desc = '';

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT config_fields_quote FROM field_config_estimate"));
    $config_fields_quote = ','.$get_field_config['config_fields_quote'].',';

    $temp_ticket = mysqli_query($dbc, "DELETE FROM temp_ticket WHERE quoteid='$estimateid'");

    //Packages
    $package = '';
    $package_html = '';
    $review_profit_loss = '';
    $review_budget = '';

    $financial_cost = 0;
    $financial_price = 0;
    $financial_profit = 0;
    $financial_margin = 0;
    $financial_plus_minus = 0;

    $total_package = 0;
	$cost_package = 0;
    $j=0;
    foreach ($_POST['packageid'] as $packageid_all) {
        if($packageid_all != '') {
            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM package WHERE packageid='$packageid_all'"));

            $package .= $packageid_all.'#'.$_POST['packageestimateprice'][$j].'**';
            $total_price += $_POST['packageestimateprice'][$j];
            $total_package += $_POST['packageestimateprice'][$j];
			$cost_package += $query['cost'];

            $package_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$package_html .= '<td>Package</td>';
						break;
					case 'Description':
						$package_html .= '<td>';
						if (strpos($config_fields_quote, ','."Package Service Type".',') !== FALSE) {
							$package_html .= 'Service Type : '.$query['service_type'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Package Category".',') !== FALSE) {
							$package_html .= 'Category : '.$query['category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Package Heading".',') !== FALSE) {
							$package_html .= 'Heading : '.$query['heading'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Package Description".',') !== FALSE) {
							$package_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Package Quote Description".',') !== FALSE) {
							$package_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$package_html .= '</td>';
						break;
					case 'UOM':
						$package_html .= '<td>-</td>';
						break;
					case 'Quantity':
						$package_html .= '<td>-</td>';
						break;
					case 'Price':
						$package_html .= '<td>-</td>';
						break;
					case 'Total':
						$package_html .= '<td>$'.number_format((float)$_POST['packageestimateprice'][$j], 2, '.', '').'</td>';
						break;
				}
			}
			$package_html .= '</tr>';

            $color_off = '';
            if($query['cost'] > $_POST['packageestimateprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['packageestimateprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['packageestimateprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Package</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td></td>
				<td>1</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['packageestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['packageestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['packageestimateprice'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['packageestimateprice'][$j];

            $temp_ticket_desc = '';
            if($query['service_type'] != '') {
                $temp_ticket_desc .= 'Service Type : '.$query['service_type'].'<br>';
            }
            if($query['category'] != '') {
                $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
            }
            if($query['heading'] != '') {
                $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
            }
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }

            $st = $query['service_type'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Package', '$packageid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

    //Promotion
    $promotion = '';
    $promotion_html = '';
    $total_promotion = 0;
	$cost_promotion = 0;
    $j=0;
    foreach ($_POST['promotionid'] as $promotionid_all) {
        if($promotionid_all != '') {
            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM promotion WHERE promotionid='$promotionid_all'"));
            $promotion .= $promotionid_all.'#'.$_POST['promotionestimateprice'][$j].'**';
            $total_price += $_POST['promotionestimateprice'][$j];
            $total_promotion += $_POST['promotionestimateprice'][$j];
			$cost_promotion += $query['cost'];

            $promotion_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$promotion_html .= '<td>Promotion</td>';
						break;
					case 'Description':
						$promotion_html .= '<td>';
						if (strpos($config_fields_quote, ','."Promotion Service Type".',') !== FALSE) {
							$promotion_html .= 'Service Type : '.$query['service_type'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Promotion Category".',') !== FALSE) {
							$promotion_html .= 'Category : '.$query['category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Promotion Heading".',') !== FALSE) {
							$promotion_html .= 'Heading : '.$query['heading'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Promotion Description".',') !== FALSE) {
							$promotion_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Promotion Quote Description".',') !== FALSE) {
							$promotion_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$promotion_html .= '</td>';
						break;
					case 'UOM':
						$promotion_html .= '<td>-</td>';
						break;
					case 'Quantity':
						$promotion_html .= '<td>-</td>';
						break;
					case 'Price':
						$promotion_html .= '<td>-</td>';
						break;
					case 'Total':
						$promotion_html .= '<td>$'.number_format((float)$_POST['promotionestimateprice'][$j], 2, '.', '').'</td>';
						break;
				}
			}
			$promotion_html .= '</tr>';

            $color_off = '';
            if($query['cost'] > $_POST['promotionestimateprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['promotionestimateprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['promotionestimateprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Promotion</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td></td>
				<td>1</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['promotionestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['promotionestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['promotionestimateprice'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['promotionestimateprice'][$j];

            $temp_ticket_desc = '';
            if($query['service_type'] != '') {
                $temp_ticket_desc .= 'Service Type : '.$query['service_type'].'<br>';
            }
            if($query['category'] != '') {
                $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
            }
            if($query['heading'] != '') {
                $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
            }
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }
            $st = $query['service_type'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Promotion', '$promotionid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

    //Custom
    $custom = '';
    $custom_html = '';
    $total_custom = 0;
	$cost_custom = 0;
    $j=0;
    foreach ($_POST['customid'] as $customid_all) {
        if($customid_all != '') {
            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM custom WHERE customid='$customid_all'"));
            $custom .= $customid_all.'#'.$_POST['customestimateprice'][$j].'**';
            $total_price += $_POST['customestimateprice'][$j];
            $total_custom += $_POST['customestimateprice'][$j];
			$cost_custom += $query['cost'];

            $custom_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$custom_html .= '<td>Custom</td>';
						break;
					case 'Description':
						$custom_html .= '<td>';
						if (strpos($config_fields_quote, ','."Custom Service Type".',') !== FALSE) {
							$custom_html .= 'Service Type : '.$query['service_type'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Custom Category".',') !== FALSE) {
							$custom_html .= 'Category : '.$query['category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Custom Heading".',') !== FALSE) {
							$custom_html .= 'Heading : '.$query['heading'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Custom Description".',') !== FALSE) {
							$custom_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Custom Quote Description".',') !== FALSE) {
							$custom_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$custom_html .= '</td>';
						break;
					case 'UOM':
						$custom_html .= '<td>-</td>';
						break;
					case 'Quantity':
						$custom_html .= '<td>-</td>';
						break;
					case 'Price':
						$custom_html .= '<td>-</td>';
						break;
					case 'Total':
						$custom_html .= '<td>$'.number_format((float)$_POST['customestimateprice'][$j], 2, '.', '').'</td>';
						break;
				}
			}
			$custom_html .= '</tr>';

            $color_off = '';
            if($query['cost'] > $_POST['customestimateprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['customestimateprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['customestimateprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Custom</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td></td>
				<td>1</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['customestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['customestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['customestimateprice'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['customestimateprice'][$j];

            $temp_ticket_desc = '';
            if($query['service_type'] != '') {
                $temp_ticket_desc .= 'Service Type : '.$query['service_type'].'<br>';
            }
            if($query['category'] != '') {
                $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
            }
            if($query['heading'] != '') {
                $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
            }
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }
            $st = $query['service_type'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Custom', '$customid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

    // Material
    $material = '';
    $m_html = '';
    $total_material = 0;
    $j=0;
    $material_total = 0;
    $material_price_total = 0;
    foreach ($_POST['materialid'] as $materialid_all) {
        if($materialid_all != '') {
            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM material WHERE materialid='$materialid_all'"));
            $material .= $materialid_all.'#'.$_POST['mestimateprice'][$j].'#'.$_POST['mestimateqty'][$j].'#'.$_POST['mestimateunit'][$j].'**';
            $total_price += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];
            $total_material += $_POST['mestimateprice'][$j]*$_POST['mestimateqty'][$j];

            $material_total += $_POST['mestimateqty'][$j];
            $material_price_total += $_POST['mestimateprice'][$j];

            $m_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$m_html .= '<td>Material</td>';
						break;
					case 'Description':
						$m_html .= '<td>';
						if (strpos($config_fields_quote, ','."Material Code".',') !== FALSE) {
							$m_html .= 'Code : '.$query['code'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Material Category".',') !== FALSE) {
							$m_html .= 'Category : '.$query['category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Material Sub-Category".',') !== FALSE) {
							$m_html .= 'Sub-Category : '.$query['sub_category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Material Material Name".',') !== FALSE) {
							$m_html .= 'Name : '.decryptIt($query['name']).'<br>';
						}
						if (strpos($config_fields_quote, ','."Material Description".',') !== FALSE) {
							$m_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Material Quote Description".',') !== FALSE) {
							$m_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Material Width".',') !== FALSE) {
							$m_html .= 'Width : '.$query['width'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Material Length".',') !== FALSE) {
							$m_html .= 'Length : '.$query['length'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Material Units".',') !== FALSE) {
							$m_html .= 'Units : '.$query['units'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Material Unit Weight".',') !== FALSE) {
							$m_html .= 'Unit Weight : '.$query['unit_weight'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Material Weight Per Foot".',') !== FALSE) {
							$m_html .= 'Weight Per Foot : '.$query['weight_per_feet'].'<br>';
						}
						$m_html .= '</td>';
						break;
					case 'UOM':
						$m_html .= '<td>'.$_POST['mestimateunit'][$j].'</td>';
						break;
					case 'Quantity':
						$m_html .= '<td>'.$_POST['mestimateqty'][$j].'</td>';
						break;
					case 'Price':
						$m_html .= '<td>$'.number_format((float)$_POST['mestimateprice'][$j], 2, '.', '').'</td>';
						break;
					case 'Total':
						$m_html .= '<td>$'.number_format((float)$_POST['mestimatetotal'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $m_html .= '</tr>';

            $color_off = '';
			$plus_minus = $_POST['mestimateprice'][$j] - $query['price'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
			$plus_minus = abs($plus_minus);
            $review_profit_loss .= '<tr><td>Material</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td>'.(float)$_POST['mestimateunit'][$j].'</td>
				<td>'.(float)$_POST['mestimateqty'][$j].'</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['mestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['mestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['mestimateprice'][$j]*(float)$_POST['mestimateqty'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['price'];
            $financial_price += $_POST['mestimateprice'][$j];

            $temp_ticket_desc = '';
            if ($query['code'] != '') {
                $temp_ticket_desc .= 'Code : '.$query['code'].'<br>';
            }
            if ($query['category'] != '') {
                $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
            }
            if ($query['sub_category'] != '') {
                $temp_ticket_desc .= 'Sub-Category : '.$query['sub_category'].'<br>';
            }
            if (decryptIt($query['name']) != '') {
                $temp_ticket_desc .= 'Name : '.decryptIt($query['name']).'<br>';
            }
            if ($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }
            if ($query['width'] != '') {
                $temp_ticket_desc .= 'Width : '.$query['width'].'<br>';
            }
            if ($query['length'] != '') {
                $temp_ticket_desc .= 'Length : '.$query['length'].'<br>';
            }
            if ($query['units'] != '') {
                $temp_ticket_desc .= 'Units : '.$query['units'].'<br>';
            }
            if ($query['unit_weight'] != '') {
                $temp_ticket_desc .= 'Unit Weight : '.$query['unit_weight'].'<br>';
            }
            if ($query['weight_per_feet'] != '') {
                $temp_ticket_desc .= 'Weight Per Foot : '.$query['weight_per_feet'].'<br>';
            }
            $st = $query['category'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Material', '$materialid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
	$cost_material = $_POST['material_total'] - $_POST['material_profit'];

    //Services
    $services = '';
    $s_html = '';
    $total_service = 0;
    $j=0;
    $service_total = 0;
    $service_price_total = 0;
    foreach ($_POST['serviceid'] as $serviceid_all) {
        if($serviceid_all != '') {
            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM services WHERE serviceid='$serviceid_all'"));
            $services .= $serviceid_all.'#'.$_POST['sestimateprice'][$j].'#'.$_POST['sestimateqty'][$j].'#'.$_POST['sestimateunit'][$j].'**';
            $total_price += $_POST['sestimateprice'][$j]*$_POST['sestimateqty'][$j];
            $total_service += $_POST['sestimateprice'][$j]*$_POST['sestimateqty'][$j];

            $service_total += $_POST['sestimateqty'][$j];
            $service_price_total += $_POST['sestimateprice'][$j];

            $s_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$s_html .= '<td>Service</td>';
						break;
					case 'Description':
						$s_html .= '<td>';
						if (strpos($config_fields_quote, ','."Services Service Type".',') !== FALSE) {
							$s_html .= 'Service Type : '.$query['service_type'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Services Category".',') !== FALSE) {
							$s_html .= 'Category : '.$query['category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Services Heading".',') !== FALSE) {
							$s_html .= 'Heading : '.$query['heading'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Services Description".',') !== FALSE) {
							$s_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Services Quote Description".',') !== FALSE) {
							$s_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$s_html .= '</td>';
						break;
					case 'UOM':
						$s_html .= '<td>'.$_POST['sestimateunit'][$j].'</td>';
						break;
					case 'Quantity':
						$s_html .= '<td>'.$_POST['sestimateqty'][$j].'</td>';
						break;
					case 'Price':
						$s_html .= '<td>$'.number_format((float)$_POST['sestimateprice'][$j], 2, '.', '').'</td>';
						break;
					case 'Total':
						$s_html .= '<td>$'.number_format((float)$_POST['sestimatetotal'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $s_html .= '</tr>';

            $color_off = '';
			$plus_minus = $_POST['sestimateprice'][$j] - $query['price'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
			$plus_minus = abs($plus_minus);
            $review_profit_loss .= '<tr><td>Services</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td>'.(float)$_POST['sestimateunit'][$j].'</td>
				<td>'.(float)$_POST['sestimateqty'][$j].'</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['sestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['sestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['sestimateprice'][$j]*(float)$_POST['sestimateqty'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['sestimateprice'][$j];

            $temp_ticket_desc = '';
            if($query['service_type'] != '') {
                $temp_ticket_desc .= 'Service Type : '.$query['service_type'].'<br>';
            }
            if($query['category'] != '') {
                $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
            }
            if($query['heading'] != '') {
                $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
            }
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }
            $st = $query['service_type'].' : '.$query['category'].' : '.$query['heading'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Service', '$serviceid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
	$cost_service = $_POST['service_total'] - $_POST['services_profit'];

    //Products
    $products = '';

    $p_html = '';
    $total_product = 0;
    $j=0;
    $product_total = 0;
    $product_price_total = 0;
    foreach ($_POST['productid'] as $productid_all) {
        if($productid_all != '') {
			/*if($_GET['estimatetabid']) {
				$products .= $_GET['estimatetabid'] . '$';
			}*/
            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM products WHERE productid='$productid_all'"));

            $products .= $productid_all.'#'.$_POST['pestimateprice'][$j].'#'.$_POST['pestimateqty'][$j].'#'.$_POST['pestimateunit'][$j].'#'.$_POST['peprofit'][$j].'#'.$_POST['peprofitmargin'][$j].'**';
            $total_price += $_POST['pestimateprice'][$j]*$_POST['pestimateqty'][$j];
            $total_product += $_POST['pestimateprice'][$j]*$_POST['pestimateqty'][$j];

            $product_total += $_POST['pestimateqty'][$j];
            $product_price_total += $_POST['pestimateprice'][$j];

            $p_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$p_html .= '<td>Product</td>';
						break;
					case 'Description':
						$p_html .= '<td>';
						if (strpos($config_fields_quote, ','."Products Product Type".',') !== FALSE) {
							$p_html .= 'Product Type : '.$query['product_type'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Products Category".',') !== FALSE) {
							$p_html .= 'Category : '.$query['category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Products Heading".',') !== FALSE) {
							$p_html .= 'Heading : '.$query['heading'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Products Description".',') !== FALSE) {
							$p_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Products Quote Description".',') !== FALSE) {
							$p_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$p_html .= '</td>';
						break;
					case 'UOM':
						$p_html .= '<td>'.$_POST['pestimateunit'][$j].'</td>';
						break;
					case 'Quantity':
						$p_html .= '<td>'.$_POST['pestimateqty'][$j].'</td>';
						break;
					case 'Price':
						$p_html .= '<td>$'.number_format((float)$_POST['pestimateprice'][$j], 2, '.', '').'</td>';
						break;
					case 'Total':
						$p_html .= '<td>$'.number_format((float)$_POST['pestimatetotal'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $p_html .= '</tr>';

            $color_off = '';
			$plus_minus = $_POST['pestimateprice'][$j] - $query['cost'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
			$plus_minus = abs($plus_minus);
            $review_profit_loss .= '<tr><td>Product</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td>'.(float)$_POST['pestimateunit'][$j].'</td>
				<td>'.(float)$_POST['pestimateqty'][$j].'</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['pestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['pestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['pestimateprice'][$j]*(float)$_POST['pestimateqty'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_profit += $_POST['peprofit'][$j];
            $financial_margin += $_POST['peprofitmargin'][$j];
            $financial_price += $_POST['pestimateprice'][$j];

            $temp_ticket_desc = '';
            if($query['product_type'] != '') {
                $temp_ticket_desc .= 'Product Type : '.$query['product_type'].'<br>';
            }
            if($query['category'] != '') {
                $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
            }
            if($query['heading'] != '') {
                $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
            }
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }
            $st = $query['product_type'].' : '.$query['category'].' : '.$query['heading'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `product_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Product', '$productid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
	$cost_product = $_POST['product_total'] - $_POST['product_profit'];

    //SR & ED
    $sred = '';
    $sred_html = '';
    $total_sred = 0;
    $j=0;
    $sred_total = 0;
	$cost_sred = 0;
    $sred_price_total = 0;
    foreach ($_POST['sredid'] as $sredid_all) {
        if($sredid_all != '') {
            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM sred WHERE sredid='$sredid_all'"));
            $sred .= $sredid_all.'#'.$_POST['sredestimateprice'][$j].'#'.$_POST['sredestimateqty'][$j].'**';
            $total_price += $_POST['sredestimateprice'][$j]*$_POST['sredestimateqty'][$j];
            $total_sred += $_POST['sredestimateprice'][$j]*$_POST['sredestimateqty'][$j];

            $sred_total += $_POST['sredestimateqty'][$j];
            $sred_price_total += $_POST['sredestimateprice'][$j];
			$cost_sred += $query['cost'];

            $sred_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$sred_html .= '<td>SR&ED</td>';
						break;
					case 'Description':
						$sred_html .= '<td>';
						if (strpos($config_fields_quote, ','."SRED SRED Type".',') !== FALSE) {
							$sred_html .= 'SR&ED Type : '.$query['sred_type'].'<br>';
						}
						if (strpos($config_fields_quote, ','."SRED Category".',') !== FALSE) {
							$sred_html .= 'Category : '.$query['category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."SRED Heading".',') !== FALSE) {
							$sred_html .= 'Heading : '.$query['heading'].'<br>';
						}
						if (strpos($config_fields_quote, ','."SRED Description".',') !== FALSE) {
							$sred_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."SRED Quote Description".',') !== FALSE) {
							$sred_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$sred_html .= '</td>';
						break;
					case 'UOM':
						$sred_html .= '<td>-</td>';
						break;
					case 'Quantity':
						$sred_html .= '<td>'.$_POST['sredestimateqty'][$j].'</td>';
						break;
					case 'Price':
						$sred_html .= '<td>$'.number_format((float)$_POST['sredestimateprice'][$j], 2, '.', '').'</td>';
						break;
					case 'Total':
						$sred_html .= '<td>$'.number_format((float)$_POST['sredestimatetotal'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $sred_html .= '</tr>';

            $color_off = '';
			$plus_minus = $_POST['sredestimateprice'][$j] - $query['price'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
            $review_profit_loss .= '<tr><td>SR&ED</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td></td>
				<td>1</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['sredestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['sredestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['sredestimateprice'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['sredestimateprice'][$j];

            $temp_ticket_desc = '';
            if($query['sred_type'] != '') {
                $temp_ticket_desc .= 'SR&ED Type : '.$query['sred_type'].'<br>';
            }
            if($query['category'] != '') {
                $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
            }
            if($query['heading'] != '') {
                $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
            }
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }
            $st = $query['sred_type'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'SRED', '$sredid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

    //Staff
    $staff = '';
    $staff_html = '';
    $total_staff = 0;
    $j=0;
    $staff_total = 0;
	$cost_staff = 0;
    $staff_price_total = 0;
    foreach ($_POST['contactid'] as $contactid_all) {
        if($contactid_all != '') {
            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name, cost, description, quote_description  FROM contacts WHERE contactid='$contactid_all'"));
            $staff .= $contactid_all.'#'.$_POST['stestimateprice'][$j].'#'.$_POST['stestimateqty'][$j].'#'.$_POST['stestimateunit'][$j].'**';
            $total_price += $_POST['stestimateprice'][$j]*$_POST['stestimateqty'][$j];
            $total_staff += $_POST['stestimateprice'][$j]*$_POST['stestimateqty'][$j];

            $staff_total += $_POST['stestimateqty'][$j];
            $staff_price_total += $_POST['stestimateprice'][$j];
			$cost_staff += $query['cost'];

            $staff_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$staff_html .= '<td>Staff</td>';
						break;
					case 'Description':
						$staff_html .= '<td>';
						if (strpos($config_fields_quote, ','."Staff Contact Person".',') !== FALSE) {
							$staff_html .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
						}
						if (strpos($config_fields_quote, ','."Staff Description".',') !== FALSE) {
							$staff_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Staff Quote Description".',') !== FALSE) {
							$staff_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$staff_html .= '</td>';
						break;
					case 'UOM':
						$staff_html .= '<td>'.$_POST['stestimateunit'][$j].'</td>';
						break;
					case 'Quantity':
						$staff_html .= '<td>'.$_POST['stestimateqty'][$j].'</td>';
						break;
					case 'Price':
						$staff_html .= '<td>$'.number_format((float)$_POST['stestimateprice'][$j], 2, '.', '').'</td>';
						break;
					case 'Total':
						$staff_html .= '<td>$'.number_format((float)$_POST['stestimatetotal'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $staff_html .= '</tr>';

            $color_off = '';
			$plus_minus = $_POST['stestimateprice'][$j] - $query['price'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
            $review_profit_loss .= '<tr><td>Staff</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td>'.(float)$_POST['stestimateunit'][$j].'</td>
				<td>'.(float)$_POST['stestimateqty'][$j].'</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['stestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['stestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['stestimateprice'][$j]*(float)$_POST['stestimateqty'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['stestimateprice'][$j];

            $temp_ticket_desc = '';
            $temp_ticket_desc .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }
            $st = decryptIt($query['first_name']).' '.decryptIt($query['last_name']);
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Staff', '$contactid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

    //Contractor
    $contractor = '';
    $cont_html = '';
    $total_contractor = 0;
    $j=0;
    $contractor_total = 0;
	$cost_contractor = 0;
    $contractor_price_total = 0;
    foreach ($_POST['contractorid'] as $contractorid_all) {
        if($contractorid_all != '') {
            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name, description, quote_description, cost  FROM contacts WHERE contactid='$contractorid_all'"));
            $contractor .= $contractorid_all.'#'.$_POST['cntestimateprice'][$j].'#'.$_POST['cntestimateqty'][$j].'#'.$_POST['cntestimateunit'][$j].'**';
            $total_price += $_POST['cntestimateprice'][$j]*$_POST['cntestimateqty'][$j];
            $total_contractor += $_POST['cntestimateprice'][$j]*$_POST['cntestimateqty'][$j];

            $contractor_total += $_POST['cntestimateqty'][$j];
            $contractor_price_total += $_POST['cntestimateprice'][$j];
			$cost_contractor += $query['price'];

            $cont_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$cont_html .= '<td>Contractor</td>';
						break;
					case 'Description':
						$cont_html .= '<td>';
						if (strpos($config_fields_quote, ','."Contractor Contact Person".',') !== FALSE) {
							$cont_html .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
						}
						if (strpos($config_fields_quote, ','."Contractor Description".',') !== FALSE) {
							$cont_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Contractor Quote Description".',') !== FALSE) {
							$cont_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$cont_html .= '</td>';
						break;
					case 'UOM':
						$cont_html .= '<td>'.$_POST['cntestimateunit'][$j].'</td>';
						break;
					case 'Quantity':
						$cont_html .= '<td>'.$_POST['cntestimateqty'][$j].'</td>';
						break;
					case 'Price':
						$cont_html .= '<td>$'.number_format((float)$_POST['cntestimateprice'][$j], 2, '.', '').'</td>';
						break;
					case 'Total':
						$cont_html .= '<td>$'.number_format((float)$_POST['cntestimatetotal'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $cont_html .= '</tr>';

            $color_off = '';
			$plus_minus = $_POST['cntestimateprice'][$j] - $query['price'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
            $review_profit_loss .= '<tr><td>Contractor</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td>'.(float)$_POST['cnestimateunit'][$j].'</td>
				<td>'.(float)$_POST['cnestimateqty'][$j].'</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['cnestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['cnestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['cnestimateprice'][$j]*(float)$_POST['cnestimateqty'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['cntestimateprice'][$j];

            $temp_ticket_desc = '';
            $temp_ticket_desc .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }
            $st = decryptIt($query['first_name']).' '.decryptIt($query['last_name']);
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Contractor', '$contractorid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

    //Client
    $client = '';
    $c_html = '';
    $total_client = 0;
	$cost_client = 0;
    $j=0;
    foreach ($_POST['clientid'] as $clientid_all) {
        if($clientid_all != '') {
            $client .= $clientid_all.'#'.$_POST['clestimateprice'][$j].'**';
            $total_price += $_POST['clestimateprice'][$j];
            $total_client += $_POST['clestimateprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name, first_name, last_name, description, quote_description, cost FROM contacts WHERE contactid='$clientid_all'"));

            $c_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$c_html .= '<td>Client</td>';
						break;
					case 'Description':
						$c_html .= '<td>';
						if (strpos($config_fields_quote, ','."Clients Client Name".',') !== FALSE) {
							$c_html .= 'Client : '.decryptIt($query['name']).'<br>';
						}
						if (strpos($config_fields_quote, ','."Clients Contact Person".',') !== FALSE) {
							$c_html .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
						}
						if (strpos($config_fields_quote, ','."Clients Description".',') !== FALSE) {
							$c_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Clients Quote Description".',') !== FALSE) {
							$c_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$c_html .= '</td>';
						break;
					case 'UOM':
						$c_html .= '<td>-</td>';
						break;
					case 'Quantity':
						$c_html .= '<td>-</td>';
						break;
					case 'Price':
						$c_html .= '<td>-</td>';
						break;
					case 'Total':
						$c_html .= '<td>$'.number_format((float)$_POST['clestimateprice'][$j], 2, '.', '').'</td>';
						break;
				}
			}
			$cost_client += $query['price'];

            $c_html .= '</tr>';

            $color_off = '';
			$plus_minus = $_POST['clestimateprice'][$j] - $query['price'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
            $review_profit_loss .= '<tr><td>Clients</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td></td>
				<td>1</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['clestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['clestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['clestimateprice'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['clestimateprice'][$j];

            $temp_ticket_desc = '';
            $temp_ticket_desc .= 'Client : '.decryptIt($query['name']).'<br>';
            $temp_ticket_desc .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }

            $st = decryptIt($query['first_name']).' '.decryptIt($query['last_name']);
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Client', '$clientid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

    //Vendor
    $vendor = '';
    $v_html = '';
    $total_vendor = 0;
    $j=0;
    $vendor_total = 0;
	$cost_vendor = 0;
    $vendor_price_total = 0;
    foreach ($_POST['vestimateqty'] as $vendorperson_all) {
        if($vendorperson_all != '' && $vendorperson_all > 0  && $vendorperson_all !== NULL) {
            $vendor .= $_POST['vestimatevendorid'][$j].'#'.$_POST['vestimateprice'][$j].'#'.$_POST['vestimateqty'][$j].'#**';
			//$vendor .= $vendorperson_all.'#'.$_POST['vestimateprice'][$j].'#'.$_POST['vestimateqty'][$j].'#'./*No longer exists $_POST['vestimateunit'][$j]*/.'**';
            $total_price += $_POST['vestimateprice'][$j]*$_POST['vestimateqty'][$j];
            $total_vendor += $_POST['vestimateprice'][$j]*$_POST['vestimateqty'][$j];

            $vendor_total += $_POST['vestimateqty'][$j];
            $vendor_price_total += $_POST['vestimateprice'][$j];
			$vendor_identification = $_POST['vestimatevendorid'][$j];
            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM vendor_price_list WHERE inventoryid = '$vendor_identification'"));

			$cost_vendor += $query['cost'];
            $v_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$v_html .= '<td>Pricelist</td>';
						break;
					case 'Description':
						$v_html .= '<td>';
						if (strpos($config_fields_quote, ','."Vendor Pricelist Category".',') !== FALSE) {
							$v_html .= 'Category : '.$query['category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Vendor Pricelist Product".',') !== FALSE) {
							$v_html .= 'Product : '.decryptIt($query['name']).'<br>';
						}
						if (strpos($config_fields_quote, ','."Vendor Pricelist Code".',') !== FALSE) {
							$v_html .= 'Code : '.$query['code'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Vendor Pricelist Sub-Category".',') !== FALSE) {
							$v_html .= 'Sub-Category : '.$query['sub_category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Vendor Pricelist Size".',') !== FALSE) {
							$v_html .= 'Size : '.$query['size'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Vendor Pricelist Type".',') !== FALSE) {
							$v_html .= 'Type : '.$query['type'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Vendor Pricelist Part No".',') !== FALSE) {
							$v_html .= 'Part No : '.$query['part_no'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Vendor Pricelist Variance".',') !== FALSE) {
							$v_html .= 'Variance : '.$query['inv_variance'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Vendor Pricelist Description".',') !== FALSE) {
							if($query['description'] !== NULL && $query['description'] !== '') {
								$v_html .= 'Description : '.$query['description'].'<br>';
							}
						}
						if (strpos($config_fields_quote, ','."Vendor Pricelist Quote Description".',') !== FALSE) {
							$v_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$v_html .= '</td>';
						break;
					case 'UOM':
						$v_html .= '<td>-</td>';
						break;
					case 'Quantity':
						$v_html .= '<td>'.$_POST['vestimateqty'][$j].'</td>';
						break;
					case 'Price':
						$v_html .= '<td>$'.number_format((float)$_POST['vestimateprice'][$j], 2, '.', '').'</td>';
						break;
					case 'Total':
						$v_html .= '<td>$'.number_format((float)$_POST['total'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $v_html .= '</tr>';

            $color_off = '';
			$plus_minus = $_POST['vestimateprice'][$j] - $query['cdn_cpu'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
            $review_profit_loss .= '<tr><td>Vendor Price List</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td></td>
				<td>1</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['vestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['vestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['vestimateprice'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cdn_cpu'];
            $financial_price += $_POST['vestimateprice'][$j];

            $temp_ticket_desc = '';
            if ($query['vendor_name'] != '') {
                $temp_ticket_desc .= 'Vendor : '.$query['vendor_name'].'<br>';
            }
            if ($query['pricelist_name'] != '') {
                $temp_ticket_desc .= 'Price List : '.$query['pricelist_name'].'<br>';
            }
            if ($query['category'] != '') {
                $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
            }
            if (decryptIt($query['name']) != '') {
                $temp_ticket_desc .= 'Product : '.decryptIt($query['name']).'<br>';
            }
            if ($query['code'] != '') {
                $temp_ticket_desc .= 'Code : '.$query['code'].'<br>';
            }
            if ($query['sub_category'] != '') {
                $temp_ticket_desc .= 'Sub-Category : '.$query['sub_category'].'<br>';
            }
            if ($query['size'] != '') {
                $temp_ticket_desc .= 'Size : '.$query['size'].'<br>';
            }
            if ($query['type'] != '') {
                $temp_ticket_desc .= 'Type : '.$query['type'].'<br>';
            }
            if ($query['part_no'] != '') {
                $temp_ticket_desc .= 'Part No : '.$query['part_no'].'<br>';
            }
            if ($query['inv_variance'] != '') {
                $temp_ticket_desc .= 'Variance : '.$query['inv_variance'].'<br>';
            }
            if($query['description'] != '' && $query['description'] != NULL) {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }

            $st = $query['pricelist_name'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Vendor Price List', '$vendor_identification', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

    //customer
    $customer = '';
    $cust_html = '';
    $total_customer = 0;
	$cost_customer = 0;
    $j=0;
    foreach ($_POST['customerid'] as $customerid_all) {
        if($customerid_all != '') {
            $customer .= $customerid_all.'#'.$_POST['custestimateprice'][$j].'**';
            $total_price += $_POST['custestimateprice'][$j];
            $total_customer += $_POST['custestimateprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name, first_name, last_name, description, quote_description, cost FROM contacts WHERE contactid='$customerid_all'"));

			$cost_customer += $query['cost'];
            $cust_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$cust_html .= '<td>Customer</td>';
						break;
					case 'Description':
						$cust_html .= '<td>';
						if (strpos($config_fields_quote, ','."Customer Client Name".',') !== FALSE) {
							$cust_html .= 'Customer : '.decryptIt($query['name']).'<br>';
						}
						if (strpos($config_fields_quote, ','."Customer Contact Person".',') !== FALSE) {
							$cust_html .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
						}
						if (strpos($config_fields_quote, ','."Customer Description".',') !== FALSE) {
							$cust_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Customer Quote Description".',') !== FALSE) {
							$cust_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$cust_html .= '</td>';
						break;
					case 'UOM':
						$cust_html .= '<td>-</td>';
						break;
					case 'Quantity':
						$cust_html .= '<td>-</td>';
						break;
					case 'Price':
						$cust_html .= '<td>-</td>';
						break;
					case 'Total':
						$cust_html .= '<td>$'.number_format((float)$_POST['custestimateprice'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $cust_html .= '</tr>';

            $color_off = '';
			$plus_minus = $_POST['custestimateprice'][$j] - $query['price'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
            $review_profit_loss .= '<tr><td>Customer</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td></td>
				<td>1</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['custestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['custestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['custestimateprice'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['custestimateprice'][$j];

            $temp_ticket_desc = '';
            $temp_ticket_desc .= 'Customer : '.decryptIt($query['name']).'<br>';
            $temp_ticket_desc .= 'Contact Person : '.decryptIt($query['first_name']).' '.decryptIt($query['last_name']).'<br>';
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }

            $st = decryptIt($query['name']);
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Customer', '$customerid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

    // Inventory
    $inventory = '';
    $in_html = '';
    $total_inventory = 0;
    $j=0;
    $inventory_total = 0;
    $inventory_price_total = 0;
    foreach ($_POST['inventoryid'] as $inventoryid_all) {
        if($inventoryid_all != '' && $_POST['inestimateqty'][$j] > 0) {
            $inventory .= $inventoryid_all.'#'.$_POST['inestimateprice'][$j].'#'.$_POST['inestimateqty'][$j].'#'.$_POST['inestimateunit'][$j].'#'.$_POST['inprofit'][$j].'#'.$_POST['inprofitmargin'][$j].'**';
            $total_price += $_POST['inestimateprice'][$j]*$_POST['inestimateqty'][$j];
            $total_inventory += $_POST['inestimateprice'][$j]*$_POST['inestimateqty'][$j];

            $inventory_total += $_POST['inestimateqty'][$j];
            $inventory_price_total += $_POST['inestimateprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM inventory WHERE inventoryid='$inventoryid_all'"));

            $in_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$in_html .= '<td>Inventory</td>';
						break;
					case 'Description':
						$in_html .= '<td>';
						if (strpos($config_fields_quote, ','."Inventory Category".',') !== FALSE) {
							$in_html .= 'Category : '.$query['category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Product Name".',') !== FALSE) {
							$in_html .= 'Name : '.decryptIt($query['name']).'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Code".',') !== FALSE) {
							$in_html .= 'Code : '.$query['code'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Sub-Category".',') !== FALSE) {
							$in_html .= 'Sub-Category : '.$query['sub_category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Size".',') !== FALSE) {
							$in_html .= 'Size : '.$query['size'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Type".',') !== FALSE) {
							$in_html .= 'Type : '.$query['type'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Part No".',') !== FALSE) {
							$in_html .= 'Part No : '.$query['part_no'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Location".',') !== FALSE) {
							$in_html .= 'Location : '.$query['location'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Variance".',') !== FALSE) {
							$in_html .= 'Variance : '.$query['inv_variance'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Weight".',') !== FALSE) {
							$in_html .= 'Weight : '.$query['weight'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory ID Number".',') !== FALSE) {
							$in_html .= 'ID Number : '.$query['id_number'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Operator".',') !== FALSE) {
							$in_html .= 'Operator : '.$query['operator'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory LSD".',') !== FALSE) {
							$in_html .= 'LSD : '.$query['lsd'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Comments".',') !== FALSE) {
							$in_html .= 'Comments : '.$query['comment'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Questions".',') !== FALSE) {
							$in_html .= 'Questions : '.$query['question'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Requests".',') !== FALSE) {
							$in_html .= 'Requests : '.$query['request'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Description".',') !== FALSE) {
							$in_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Inventory Quote Description".',') !== FALSE) {
							$in_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$in_html .= '</td>';
						break;
					case 'UOM':
						$in_html .= '<td>'.$_POST['inestimateunit'][$j].'</td>';
						break;
					case 'Quantity':
						$in_html .= '<td>'.$_POST['inestimateqty'][$j].'</td>';
						break;
					case 'Price':
						$in_html .= '<td>$'.number_format((float)$_POST['inestimateprice'][$j], 2, '.', '').'</td>';
						break;
					case 'Total':
						$in_html .= '<td>$'.number_format((float)$_POST['inestimatetotal'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $in_html .= '</tr>';

            $color_off = '';
			$plus_minus = $_POST['inestimateprice'][$j] - $query['price'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
            $review_profit_loss .= '<tr><td>Inventory</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td>'.(float)$_POST['inestimateunit'][$j].'</td>
				<td>'.(float)$_POST['inestimateqty'][$j].'</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['inestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['inestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['inestimateprice'][$j]*(float)$_POST['inestimateqty'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_profit += $_POST['inprofit'][$j];
            $financial_margin += $_POST['inprofitmargin'][$j];
            $financial_price += $_POST['inestimateprice'][$j];

            $temp_ticket_desc = '';
            if ($query['category'] != '') {
                $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
            }
            if (decryptIt($query['name']) != '') {
                $temp_ticket_desc .= 'Name : '.decryptIt($query['name']).'<br>';
            }
            if ($query['code'] != '') {
                $temp_ticket_desc .= 'Code : '.$query['code'].'<br>';
            }
            if ($query['sub_category'] != '') {
                $temp_ticket_desc .= 'Sub-Category : '.$query['sub_category'].'<br>';
            }
            if ($query['size'] != '') {
                $temp_ticket_desc .= 'Size : '.$query['size'].'<br>';
            }
            if ($query['type'] != '') {
                $temp_ticket_desc .= 'Type : '.$query['type'].'<br>';
            }
            if ($query['part_no'] != '') {
                $temp_ticket_desc .= 'Part No : '.$query['part_no'].'<br>';
            }
            if ($query['location'] != '') {
                $temp_ticket_desc .= 'Location : '.$query['location'].'<br>';
            }
            if ($query['inv_variance'] != '') {
                $temp_ticket_desc .= 'Variance : '.$query['inv_variance'].'<br>';
            }
            if ($query['weight'] != '') {
                $temp_ticket_desc .= 'Weight : '.$query['weight'].'<br>';
            }
            if ($query['id_number'] != '') {
                $temp_ticket_desc .= 'ID Number : '.$query['id_number'].'<br>';
            }
            if ($query['operator'] != '') {
                $temp_ticket_desc .= 'Operator : '.$query['operator'].'<br>';
            }
            if ($query['lsd'] != '') {
                $temp_ticket_desc .= 'LSD : '.$query['lsd'].'<br>';
            }
            if ($query['comment'] != '') {
                $temp_ticket_desc .= 'Comments : '.$query['comment'].'<br>';
            }
            if ($query['question'] != '') {
                $temp_ticket_desc .= 'Questions : '.$query['question'].'<br>';
            }
            if ($query['request'] != '') {
                $temp_ticket_desc .= 'Requests : '.$query['request'].'<br>';
            }
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }

            $st = $query['category'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Inventory', '$inventoryid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
	$cost_inventory = $_POST['inventory_total'] - $_POST['inventory_profit'];

    //Equipemt
    $equipment = '';
    $eq_html = '';
    $total_equipment = 0;
    $j=0;
    $equipment_total = 0;
    $equipment_price_total = 0;

    foreach ($_POST['equipmentid'] as $equipmentid_all) {
        if($equipmentid_all != '') {
            $equipment .= $equipmentid_all.'#'.$_POST['eqestimateprice'][$j].'#'.$_POST['eqestimateqty'][$j].'#'.$_POST['eqestimateunit'][$j].'#'.$_POST['eqprofit'][$j].'#'.$_POST['eqprofitmargin'][$j].'**';
            $total_price += $_POST['eqestimateprice'][$j]*$_POST['eqestimateqty'][$j];
            $total_equipment += $_POST['eqestimateprice'][$j]*$_POST['eqestimateqty'][$j];

            $equipment_total += $_POST['eqestimateqty'][$j];
            $equipment_price_total += $_POST['eqestimateprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM equipment WHERE equipmentid='$equipmentid_all'"));

            $eq_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$eq_html .= '<td>Equipment</td>';
						break;
					case 'Description':
						$eq_html .= '<td>';
						if (strpos($config_fields_quote, ','."Equipment Category".',') !== FALSE) {
							$eq_html .= 'Category : '.$query['category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Unit Number".',') !== FALSE) {
							$eq_html .= 'Unit Number : '.$query['unit_number'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Serial Number".',') !== FALSE) {
							$eq_html .= 'Serial Number : '.$query['serial_number'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Type".',') !== FALSE) {
							$eq_html .= 'Type : '.$query['type'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Make".',') !== FALSE) {
							$eq_html .= 'Make : '.$query['make'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Model".',') !== FALSE) {
							$eq_html .= 'Model : '.$query['model'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Model Year".',') !== FALSE) {
							$eq_html .= 'Model Year : '.$query['model_year'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Year Purchased".',') !== FALSE) {
							$eq_html .= 'Year Purchased : '.$query['year_purchased'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Mileage".',') !== FALSE) {
							$eq_html .= 'Mileage : '.$query['mileage'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Hours Operated".',') !== FALSE) {
							$eq_html .= 'Hours Operated : '.$query['hours_operated'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Notes".',') !== FALSE) {
							$eq_html .= 'Notes : '.$query['notes'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Nickname".',') !== FALSE) {
							$eq_html .= 'Nickname : '.$query['nickname'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment VIN Number".',') !== FALSE) {
							$eq_html .= 'VIN Number : '.$query['vin_number'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Color".',') !== FALSE) {
							$eq_html .= 'Color : '.$query['color'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Licence Plate".',') !== FALSE) {
							$eq_html .= 'Licence Plate : '.$query['licence_plate'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Ownership Status".',') !== FALSE) {
							$eq_html .= 'Ownership Status : '.$query['ownership_status'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Description".',') !== FALSE) {
							$eq_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Equipment Quote Description".',') !== FALSE) {
							$eq_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$eq_html .= '</td>';
						break;
					case 'UOM':
						$eq_html .= '<td>'.$_POST['eqestimateunit'][$j].'</td>';
						break;
					case 'Quantity':
						$eq_html .= '<td>'.$_POST['eqestimateqty'][$j].'</td>';
						break;
					case 'Price':
						$eq_html .= '<td>$'.number_format((float)$_POST['eqestimateprice'][$j], 2, '.', '').'</td>';
						break;
					case 'Total':
						$eq_html .= '<td>$'.number_format((float)$_POST['eqestimatetotal'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $eq_html .= '</tr>';

            $color_off = '';
			$plus_minus = $_POST['eqestimateprice'][$j] - $query['price'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
            $review_profit_loss .= '<tr><td>Equipment</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td>'.(float)$_POST['eqestimateunit'][$j].'</td>
				<td>'.(float)$_POST['eqestimateqty'][$j].'</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['eqestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['eqestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['eqestimateprice'][$j]*(float)$_POST['eqestimateqty'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_profit += $_POST['eqprofit'][$j];
            $financial_margin += $_POST['eqprofitmargin'][$j];
            $financial_price += $_POST['eqestimateprice'][$j];

            $temp_ticket_desc = '';
            if($query['category'] != '') {
                $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
            }
            if($query['unit_number'] != '') {
                $temp_ticket_desc .= 'Unit Number : '.$query['unit_number'].'<br>';
            }
            if($query['serial_number'] != '') {
                $temp_ticket_desc .= 'Serial Number : '.$query['serial_number'].'<br>';
            }
            if($query['type'] != '') {
                $temp_ticket_desc .= 'Type : '.$query['type'].'<br>';
            }
            if($query['make'] != '') {
                $temp_ticket_desc .= 'Make : '.$query['make'].'<br>';
            }
            if($query['model'] != '') {
                $temp_ticket_desc .= 'Model : '.$query['model'].'<br>';
            }
            if($query['model_year'] != '') {
                $temp_ticket_desc .= 'Model Year : '.$query['model_year'].'<br>';
            }
            if($query['year_purchased'] != '') {
                $temp_ticket_desc .= 'Year Purchased : '.$query['year_purchased'].'<br>';
            }
            if($query['mileage'] != '') {
                $temp_ticket_desc .= 'Mileage : '.$query['mileage'].'<br>';
            }
            if($query['hours_operated'] != '') {
                $temp_ticket_desc .= 'Hours Operated : '.$query['hours_operated'].'<br>';
            }
            if($query['notes'] != '') {
                $temp_ticket_desc .= 'Notes : '.$query['notes'].'<br>';
            }
            if($query['nickname'] != '') {
                $temp_ticket_desc .= 'Nickname : '.$query['nickname'].'<br>';
            }
            if($query['vin_number'] != '') {
                $temp_ticket_desc .= 'VIN Number : '.$query['vin_number'].'<br>';
            }
            if($query['color'] != '') {
                $temp_ticket_desc .= 'Color : '.$query['color'].'<br>';
            }
            if($query['licence_plate'] != '') {
                $temp_ticket_desc .= 'Licence Plate : '.$query['licence_plate'].'<br>';
            }
            if($query['ownership_status'] != '') {
                $temp_ticket_desc .= 'Ownership Status : '.$query['ownership_status'].'<br>';
            }
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }

            $st = $query['category'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Equipment', '$equipmentid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
	$cost_equipment = $_POST['equ_total'] - $_POST['equipment_profit'];

    //Labour
    $labour = '';
    $l_html = '';
    $total_labour = 0;
    $j=0;
    $labour_total = 0;
    $labour_price_total = 0;
    foreach ($_POST['labourid'] as $labourid_all) {
        if($labourid_all != '') {
            $labour .= $labourid_all.'#'.$_POST['lestimateprice'][$j].'#'.$_POST['lestimateqty'][$j].'#'.$_POST['lestimateunit'][$j].'#'.$_POST['lprofit'][$j].'#'.$_POST['lprofitmargin'][$j].'**';
            $total_price += $_POST['lestimateprice'][$j]*$_POST['lestimateqty'][$j];
            $total_labour += $_POST['lestimateprice'][$j]*$_POST['lestimateqty'][$j];

            $labour_total += $_POST['lestimateqty'][$j];
            $labour_price_total += $_POST['lestimateprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM labour WHERE labourid='$labourid_all'"));

            $l_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$l_html .= '<td>Labour</td>';
						break;
					case 'Description':
						$l_html .= '<td>';
						if (strpos($config_fields_quote, ','."Labour Type".',') !== FALSE) {
							$l_html .= 'Type : '.$query['labour_type'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Labour Heading".',') !== FALSE) {
							$l_html .= 'Heading : '.$query['heading'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Labour Category".',') !== FALSE) {
							$l_html .= 'Category : '.$query['category'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Labour Labour Code".',') !== FALSE) {
							$l_html .= 'Labour Code : '.$query['labour_code'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Labour Name".',') !== FALSE) {
							$l_html .= 'Name : '.decryptIt($query['name']).'<br>';
						}
						if (strpos($config_fields_quote, ','."Labour Description".',') !== FALSE) {
							$l_html .= 'Description : '.$query['description'].'<br>';
						}
						if (strpos($config_fields_quote, ','."Labour Quote Description".',') !== FALSE) {
							$l_html .= 'Description : '.$query['quote_description'].'<br>';
						}
						$l_html .= '</td>';
						break;
					case 'UOM':
						$l_html .= '<td>'.$_POST['lestimateunit'][$j].'</td>';
						break;
					case 'Quantity':
						$l_html .= '<td>'.$_POST['lestimateqty'][$j].'</td>';
						break;
					case 'Price':
						$l_html .= '<td>$'.number_format((float)$_POST['lestimateprice'][$j], 2, '.', '').'</td>';
						break;
					case 'Total':
						$l_html .= '<td>$'.number_format((float)$_POST['lestimatetotal'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $l_html .= '</tr>';

            $color_off = '';
			$plus_minus = $_POST['lestimateprice'][$j] - $query['price'];
			$financial_plus_minus += $plus_minus;
			if($plus_minus < 0) {
				$color_off = 'style = "color:red; "';
			} else {
				$color_off = 'style = "color:green; "';
			}
            $review_profit_loss .= '<tr><td>Labour</td>
				<td>'.$query['heading'].'</td>
				<td>'.html_entity_decode($query['description']).'</td>
				<td>'.(float)$_POST['lestimateunit'][$j].'</td>
				<td>'.(float)$_POST['lestimateqty'][$j].'</td>
				<td>$'.$query['cost'].'</td>
				<td>'.number_format((1-$query['cost']/(float)$_POST['lestimateprice'][$j])*100,2,'.','').'%</td>
				<td>$'.number_format((float)$_POST['lestimateprice'][$j], 2, '.', '').'</td>
				<td '.$color_off.'>$'.$plus_minus.'</td>
				<td>$'.number_format((float)$_POST['lestimateprice'][$j]*(float)$_POST['lestimateqty'][$j], 2, '.', '').'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_profit += $_POST['lprofit'][$j];
            $financial_margin += $_POST['lprofitmargin'][$j];
            $financial_price += $_POST['lestimateprice'][$j];

            $temp_ticket_desc = '';
            if($query['labour_type'] != '') {
                $temp_ticket_desc .= 'Type : '.$query['labour_type'].'<br>';
            }
            if($query['heading'] != '') {
                $temp_ticket_desc .= 'Heading : '.$query['heading'].'<br>';
            }
            if($query['category'] != '') {
                $temp_ticket_desc .= 'Category : '.$query['category'].'<br>';
            }
            if($query['labour_code'] != '') {
                $temp_ticket_desc .= 'Labour Code : '.$query['labour_code'].'<br>';
            }
            if(decryptIt($query['name']) != '') {
                $temp_ticket_desc .= 'Name : '.decryptIt($query['name']).'<br>';
            }
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }

            $st = $query['labour_type'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Labour', '$labourid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
	$cost_labour = $_POST['labour_total'] - $_POST['labour_profit'];

    //Expense
    $expense = '';
    $ex_html = '';
    $total_expense = 0;
    $j=0;
    foreach ($_POST['expensetype'] as $expensetype_all) {
        if($expensetype_all != '') {
            $expense .=     $expensetype_all.'#'.$_POST['expensecategory'][$j].'#'.$_POST['expestimateprice'][$j].'**';
            $total_price += $_POST['expestimateprice'][$j];
            $total_expense += $_POST['expestimateprice'][$j];

            $ex_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$ex_html .= '<td>'.$expensetype_all.'</td>';
						break;
					case 'Description':
						$ex_html .= '<td>'.$_POST['expensecategory'][$j].'</td>';
						break;
					case 'UOM':
						$ex_html .= '<td></td>';
						break;
					case 'Quantity':
						$ex_html .= '<td></td>';
						break;
					case 'Price':
						$ex_html .= '<td></td>';
						break;
					case 'Total':
						$ex_html .= '<td>$'.number_format((float)$_POST['expestimateprice'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $ex_html .= '</tr>';

            $review_profit_loss .= '<tr><td>Expense</td>
				<td>'.$query['heading'].'</td>
				<td>'.$_POST['expensecategory'][$j].'</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td>$'.number_format((float)$_POST['expestimateprice'][$j], 2, '.', '').'</td>
				<td></td>
				<td>$'.number_format((float)$_POST['expestimateprice'][$j], 2, '.', '').'</td></tr>';

            $desc = $expensetype_all.' : '.$_POST['expensecategory'][$j];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`,`desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Expenses', '$desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

    //Other
    $other = '';
    $other_html = '';
    $total_other = 0;
    $j=0;
    foreach ($_POST['other_detail'] as $other_detail_all) {
        if($other_detail_all != '') {
            $other .=     $other_detail_all.'#'.$_POST['otherestimateprice'][$j].'**';
            $total_price += $_POST['otherestimateprice'][$j];
            $total_other += $_POST['otherestimateprice'][$j];

			$other_html .= '<tr nobr="true">';
			foreach($field_order as $field_data) {
				$data = explode('***',$field_data);
				if($data[1] == '') {
					$data[1] = $data[0];
				}
				switch($data[0]) {
					case 'Type':
						$other_html .= '<td>'.$other_detail_all.'</td>';
						break;
					case 'Description':
						$other_html .= '<td></td>';
						break;
					case 'UOM':
						$other_html .= '<td></td>';
						break;
					case 'Quantity':
						$other_html .= '<td></td>';
						break;
					case 'Price':
						$other_html .= '<td></td>';
						break;
					case 'Total':
						$other_html .= '<td>$'.number_format((float)$_POST['otherestimateprice'][$j], 2, '.', '').'</td>';
						break;
				}
			}
            $other_html .= '</tr>';

            $review_profit_loss .= '<tr><td>Other Items</td>
				<td></td>
				<td>'.$other_detail_all.'</td>
				<td></td>
				<td></td>
				<td></td>
				<td></td>
				<td>$'.number_format((float)$_POST['otherestimateprice'][$j], 2, '.', '').'</td>
				<td></td>
				<td>$'.number_format((float)$_POST['otherestimateprice'][$j], 2, '.', '').'</td></tr>';

            $query_insert_ticket = "INSERT INTO `temp_ticket` (`quoteid`, `businessid`, `clientid`, `category`, `desc`) VALUES ('$estimateid', '$businessid', '$clientid', 'Other', '$other_detail_all')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

	$accordion_html = '';
	$accordion_total = 0;
	$accordion_types = [];
	$accordion_type_totals = [];
    include ('add_estimate_company_rate_card_estimate_data.php');
    include ('add_estimate_misc_estimate_data.php');
	$accordion_type_count = count($accordion_types);

    if($total_package != 0) {
        $review_budget .= '<tr><td>Packages</td>
			<td>'.number_format((1-$cost_package/$total_package)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_package - $cost_package,2).'</td>
			<td>$'.$total_package.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Packages</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_promotion != 0) {
        //$review_budget .= '<tr><td>Promotions</td><td>$'.number_format((float)$_POST['budget_price_1'], 2, '.', '').'</td> <td>$'.$total_promotion.'</td></tr>';
        $review_budget .= '<tr><td>Promotions</td>
			<td>'.number_format((1-$cost_promotion/$total_promotion)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_promotion - $cost_promotion,2).'</td>
			<td>$'.$total_promotion.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Promotions</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_custom != 0) {
        //$review_budget .= '<tr><td>Custom</td><td>$'.number_format((float)$_POST['budget_price_2'], 2, '.', '').'</td> <td>$'.$total_custom.'</td></tr>';
        $review_budget .= '<tr><td>Custom</td>
			<td>'.number_format((1-$cost_custom/$total_custom)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_custom - $cost_custom,2).'</td>
			<td>$'.$total_custom.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Custom</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_material != 0) {
        //$review_budget .= '<tr><td>Material</td><td>$'.number_format((float)$_POST['budget_price_14'], 2, '.', '').'</td> <td>$'.$total_material.'</td></tr>';
        $review_budget .= '<tr><td>Material</td>
			<td>'.number_format((1-$cost_material/$total_material)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_material - $cost_material,2).'</td>
			<td>$'.$total_material.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Materials</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_service != 0) {
        //$review_budget .= '<tr><td>Services</td><td>$'.number_format((float)$_POST['budget_price_3'], 2, '.', '').'</td> <td>$'.$total_service.'</td></tr>';
        $review_budget .= '<tr><td>Services</td>
			<td>'.number_format((1-$cost_service/$total_service)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_service - $cost_service,2).'</td>
			<td>$'.$total_service.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Services</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_product != 0) {
        //$review_budget .= '<tr><td>Product</td><td>$'.number_format((float)$_POST['budget_price_16'], 2, '.', '').'</td> <td>$'.$total_product.'</td></tr>';
        $review_budget .= '<tr><td>Product</td>
			<td>'.number_format((1-$cost_product/$total_product)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_product - $cost_product,2).'</td>
			<td>$'.$total_product.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Products</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_sred != 0) {
        //$review_budget .= '<tr><td>SR&ED</td><td>$'.number_format((float)$_POST['budget_price_15'], 2, '.', '').'</td> <td>$'.$total_sred.'</td></tr>';
        $review_budget .= '<tr><td>SR&ED</td>
			<td>'.number_format((1-$cost_sred/$total_sred)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_sred - $cost_sred,2).'</td>
			<td>$'.$total_sred.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for SR&ED</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_staff != 0) {
        //$review_budget .= '<tr><td>Staff</td><td>$'.number_format((float)$_POST['budget_price_4'], 2, '.', '').'</td> <td>$'.$total_staff.'</td></tr>';
        $review_budget .= '<tr><td>Staff</td>
			<td>'.number_format((1-$cost_staff/$total_staff)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_staff - $cost_staff,2).'</td>
			<td>$'.$total_staff.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Staff</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_contractor != 0) {
        //$review_budget .= '<tr><td>Contractor</td><td>$'.number_format((float)$_POST['budget_price_5'], 2, '.', '').'</td> <td>$'.$total_contractor.'</td></tr>';
        $review_budget .= '<tr><td>Contractor</td>
			<td>'.number_format((1-$cost_contractor/$total_contractor)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_contractor - $cost_contractor,2).'</td>
			<td>$'.$total_contractor.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Contractors</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_client != 0) {
        //$review_budget .= '<tr><td>Clients</td><td>$'.number_format((float)$_POST['budget_price_6'], 2, '.', '').'</td> <td>$'.$total_client.'</td></tr>';
        $review_budget .= '<tr><td>Clients</td>
			<td>'.number_format((1-$cost_client/$total_client)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_client - $cost_client,2).'</td>
			<td>$'.$total_client.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Clients</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_vendor != 0) {
        //$review_budget .= '<tr><td>Vendor Price List</td><td>$'.number_format((float)$_POST['budget_price_7'], 2, '.', '').'</td> <td>$'.$total_vendor.'</td></tr>';
        $review_budget .= '<tr><td>Vendor Price List</td>
			<td>'.number_format((1-$cost_vendor/$total_vendor)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_vendor - $cost_vendor,2).'</td>
			<td>$'.$total_vendor.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Vendors</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_customer != 0) {
        //$review_budget .= '<tr><td>Customer</td><td>$'.number_format((float)$_POST['budget_price_8'], 2, '.', '').'</td> <td>$'.$total_customer.'</td></tr>';
        $review_budget .= '<tr><td>Customer</td>
			<td>'.number_format((1-$cost_customer/$total_customer)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_customer - $cost_customer,2).'</td>
			<td>$'.$total_customer.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Customers</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_inventory != 0) {
        //$review_budget .= '<tr><td>Inventory</td><td>$'.number_format((float)$_POST['budget_price_9'], 2, '.', '').'</td> <td>$'.$total_inventory.'</td></tr>';
        $review_budget .= '<tr><td>Inventory</td>
			<td>'.number_format((1-$cost_inventory/$total_inventory)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_inventory - $cost_inventory,2).'</td>
			<td>$'.$total_inventory.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Packages</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_equipment != 0) {
        //$review_budget .= '<tr><td>Equipment</td><td>$'.number_format((float)$_POST['budget_price_10'], 2, '.', '').'</td> <td>$'.$total_equipment.'</td></tr>';
        $review_budget .= '<tr><td>Equipment</td>
			<td>'.number_format((1-$cost_equipment/$total_equipment)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_equipment - $cost_equipment,2).'</td>
			<td>$'.$total_equipment.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Equipment</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_labour != 0) {
        //$review_budget .= '<tr><td>Labour</td><td>$'.number_format((float)$_POST['budget_price_13'], 2, '.', '').'</td> <td>$'.$total_labour.'</td></tr>';
        $review_budget .= '<tr><td>Labour</td>
			<td>'.number_format((1-$cost_labour/$total_labour)*100, 2, '.', '').'%</td>
			<td>$'.number_format($total_labour - $cost_labour,2).'</td>
			<td>$'.$total_labour.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Labour</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_expense != 0) {
        //$review_budget .= '<tr><td>Expense</td><td>$'.number_format((float)$_POST['budget_price_11'], 2, '.', '').'</td> <td>$'.$total_expense.'</td></tr>';
        $review_budget .= '<tr><td>Expense</td>
			<td></td>
			<td></td>
			<td>$'.$total_expense.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Expenses</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }
    if($total_other != 0) {
        //$review_budget .= '<tr><td>Other Items</td><td>$'.number_format((float)$_POST['budget_price_12'], 2, '.', '').'</td> <td>$'.$total_other.'</td></tr>';
        $review_budget .= '<tr><td>Other Items</td>
			<td></td>
			<td></td>
			<td>$'.$total_other.'</td></tr>';
		foreach($field_order as $field_data) {
			$data = explode('***',$field_data);
			if($data[1] == '') {
				$data[1] = $data[0];
			}
			switch($data[0]) {
				case 'Type': $category_totals .= '<td>Total for Other</td>'; break;
				case 'Description': $category_totals .= '<td></td>'; break;
				case 'UOM': $category_totals .= '<td></td>'; break;
				case 'Quantity': $category_totals .= '<td></td>'; break;
				case 'Price': $category_totals .= '<td></td>'; break;
				case 'Total': $category_totals .= '<td>'.number_format((float)$current_total, 2, '.', '').'</td>'; break;
			}
		}
		$category_totals .= '</tr>';
    }

	// Details
	$detail_config = mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields_quote` FROM `field_config_estimate`"));
    $detail_detail = filter_var(htmlentities($_POST['detail_detail']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Detail,') !== false && $detail_detail != '') {
		$detail_html .= '<tr><td>Detail</td><td>'.html_entity_decode($detail_detail).'</td></tr>';
	}
    $detail_issue = filter_var(htmlentities($_POST['detail_issue']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Issue,') !== false && $detail_issue != '') {
		$detail_html .= '<tr><td>Issue</td><td>'.html_entity_decode($detail_issue).'</td></tr>';
	}
    $detail_problem = filter_var(htmlentities($_POST['detail_problem']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Problem,') !== false && $detail_problem != '') {
		$detail_html .= '<tr><td>Problem</td><td>'.html_entity_decode($detail_problem).'</td></tr>';
	}
    $detail_technical_uncertainty = filter_var(htmlentities($_POST['detail_technical_uncertainty']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Technical Uncertainty,') !== false && $detail_technical_uncertainty != '') {
		$detail_html .= '<tr><td>Technical Uncertainty</td><td>'.html_entity_decode($detail_technical_uncertainty).'</td></tr>';
	}
    $detail_base_knowledge = filter_var(htmlentities($_POST['detail_base_knowledge']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Base Knowledge,') !== false && $detail_base_knowledge != '') {
		$detail_html .= '<tr><td>Base Knowledge</td><td>'.html_entity_decode($detail_base_knowledge).'</td></tr>';
	}
    $detail_do = filter_var(htmlentities($_POST['detail_do']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Do,') !== false && $detail_do != '') {
		$detail_html .= '<tr><td>Do</td><td>'.html_entity_decode($detail_do).'</td></tr>';
	}
    $detail_already_known = filter_var(htmlentities($_POST['detail_already_known']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Already Known,') !== false && $detail_already_known != '') {
		$detail_html .= '<tr><td>Already Known</td><td>'.html_entity_decode($detail_already_known).'</td></tr>';
	}
    $detail_sources = filter_var(htmlentities($_POST['detail_sources']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Sources,') !== false && $detail_sources != '') {
		$detail_html .= '<tr><td>Sources</td><td>'.html_entity_decode($detail_sources).'</td></tr>';
	}
    $detail_current_designs = filter_var(htmlentities($_POST['detail_current_designs']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Current Designs,') !== false && $detail_current_designs != '') {
		$detail_html .= '<tr><td>Current Designs</td><td>'.html_entity_decode($detail_current_designs).'</td></tr>';
	}
    $detail_known_techniques = filter_var(htmlentities($_POST['detail_known_techniques']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Known Techniques,') !== false && $detail_known_techniques != '') {
		$detail_html .= '<tr><td>Known Techniques</td><td>'.html_entity_decode($detail_known_techniques).'</td></tr>';
	}
    $detail_review_needed = filter_var(htmlentities($_POST['detail_review_needed']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Review Needed,') !== false && $detail_review_needed != '') {
		$detail_html .= '<tr><td>Review Needed</td><td>'.html_entity_decode($detail_review_needed).'</td></tr>';
	}
    $detail_looking_to_achieve = filter_var(htmlentities($_POST['detail_looking_to_achieve']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Looking to Achieve,') !== false && $detail_looking_to_achieve != '') {
		$detail_html .= '<tr><td>Looking to Achieve</td><td>'.html_entity_decode($detail_looking_to_achieve).'</td></tr>';
	}
    $detail_plan = filter_var(htmlentities($_POST['detail_plan']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Plan,') !== false && $detail_plan != '') {
		$detail_html .= '<tr><td>Plan</td><td>'.html_entity_decode($detail_plan).'</td></tr>';
	}
    $detail_next_steps = filter_var(htmlentities($_POST['detail_next_steps']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Next Steps,') !== false && $detail_next_steps != '') {
		$detail_html .= '<tr><td>Next Steps</td><td>'.html_entity_decode($detail_next_steps).'</td></tr>';
	}
    $detail_learnt = filter_var(htmlentities($_POST['detail_learnt']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Learnt,') !== false && $detail_learnt != '') {
		$detail_html .= '<tr><td>Learnt</td><td>'.html_entity_decode($detail_learnt).'</td></tr>';
	}
    $detail_discovered = filter_var(htmlentities($_POST['detail_discovered']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Discovered,') !== false && $detail_discovered != '') {
		$detail_html .= '<tr><td>Discovered</td><td>'.html_entity_decode($detail_discovered).'</td></tr>';
	}
    $detail_tech_advancements = filter_var(htmlentities($_POST['detail_tech_advancements']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Tech Advancements,') !== false && $detail_tech_advancements != '') {
		$detail_html .= '<tr><td>Tech Advancements</td><td>'.html_entity_decode($detail_tech_advancements).'</td></tr>';
	}
    $detail_work = filter_var(htmlentities($_POST['detail_work']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Work,') !== false && $detail_work != '') {
		$detail_html .= '<tr><td>Work</td><td>'.html_entity_decode($detail_work).'</td></tr>';
	}
    $detail_adjustments_needed = filter_var(htmlentities($_POST['detail_adjustments_needed']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Adjustments Needed,') !== false && $detail_adjustments_needed != '') {
		$detail_html .= '<tr><td>Adjustments Needed</td><td>'.html_entity_decode($detail_adjustments_needed).'</td></tr>';
	}
    $detail_future_designs = filter_var(htmlentities($_POST['detail_future_designs']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Future Designs,') !== false && $detail_future_designs != '') {
		$detail_html .= '<tr><td>Future Designs</td><td>'.html_entity_decode($detail_future_designs).'</td></tr>';
	}
    $detail_gap = filter_var(htmlentities($_POST['detail_gap']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details GAP,') !== false && $detail_gap != '') {
		$detail_html .= '<tr><td>GAP</td><td>'.html_entity_decode($detail_gap).'</td></tr>';
	}
    $detail_targets = filter_var(htmlentities($_POST['detail_targets']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Targets,') !== false && $detail_targets != '') {
		$detail_html .= '<tr><td>Targets</td><td>'.html_entity_decode($detail_targets).'</td></tr>';
	}
    $detail_audience = filter_var(htmlentities($_POST['detail_audience']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Audience,') !== false && $detail_audience != '') {
		$detail_html .= '<tr><td>Audience</td><td>'.html_entity_decode($detail_audience).'</td></tr>';
	}
    $detail_strategy = filter_var(htmlentities($_POST['detail_strategy']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Strategy,') !== false && $detail_strategy != '') {
		$detail_html .= '<tr><td>Strategy</td><td>'.html_entity_decode($detail_strategy).'</td></tr>';
	}
    $detail_desired_outcome = filter_var(htmlentities($_POST['detail_desired_outcome']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Desired Outcome,') !== false && $detail_desired_outcome != '') {
		$detail_html .= '<tr><td>Desired Outcome</td><td>'.html_entity_decode($detail_desired_outcome).'</td></tr>';
	}
    $detail_actual_outcome = filter_var(htmlentities($_POST['detail_actual_outcome']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Actual Outcome,') !== false && $detail_actual_outcome != '') {
		$detail_html .= '<tr><td>Actual Outcome</td><td>'.html_entity_decode($detail_actual_outcome).'</td></tr>';
	}
    $detail_check = filter_var(htmlentities($_POST['detail_check']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Check,') !== false && $detail_check != '') {
		$detail_html .= '<tr><td>Check</td><td>'.html_entity_decode($detail_check).'</td></tr>';
	}
    $detail_objective = filter_var(htmlentities($_POST['detail_objective']),FILTER_SANITIZE_STRING);
	if(strpos(','.$detail_config['config_fields_quote'].',','Details Objective,') !== false && $detail_objective != '') {
		$detail_html .= '<tr><td>Objective</td><td>'.html_entity_decode($detail_objective).'</td></tr>';
	}
    $detail_note = filter_var(htmlentities($_POST['detail_note']),FILTER_SANITIZE_STRING);

	if($detail_html != '') {
		$detail_html = '<table border="1px" style="padding:3px; border:1px solid black; width:100%;">
			<tr nobr="true" style="background-color:lightgrey; color:black;">
			<th width="15%">Detail Type</th><th width="85%">Information</th></tr>
			'.$detail_html.'</table><br><br>';
	}

    $query_update_report = "UPDATE `estimate_detail` SET `detail_detail` = '$detail_detail', `detail_issue` = '$detail_issue', `detail_problem` = '$detail_problem', `detail_technical_uncertainty` = '$detail_technical_uncertainty', `detail_base_knowledge` = '$detail_base_knowledge', `detail_do` = '$detail_do', `detail_already_known` = '$detail_already_known', `detail_sources` = '$detail_sources', `detail_current_designs` = '$detail_current_designs', `detail_known_techniques` = '$detail_known_techniques', `detail_review_needed` = '$detail_review_needed', `detail_looking_to_achieve` = '$detail_looking_to_achieve', `detail_plan` = '$detail_plan', `detail_next_steps` = '$detail_next_steps', `detail_learnt` = '$detail_learnt', `detail_discovered` = '$detail_discovered', `detail_tech_advancements` = '$detail_tech_advancements', `detail_work` = '$detail_work', `detail_adjustments_needed` = '$detail_adjustments_needed', `detail_future_designs` = '$detail_future_designs', `detail_check` = '$detail_check', `detail_objective` = '$detail_objective', `detail_gap` = '$detail_gap', `detail_targets` = '$detail_targets', `detail_audience` = '$detail_audience', `detail_strategy` = '$detail_strategy', `detail_desired_outcome` = '$detail_desired_outcome', `detail_actual_outcome` = '$detail_actual_outcome', `detail_quote_note`='$detail_note' WHERE `estimateid` = '$estimateid'";
    $result_update_report = mysqli_query($dbc, $query_update_report);

		$before_change = '';
		$history = "Estimates detail entry has been updated for estimate id $estimateid. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);

    // Put the html portions together
	$base_field_config = ','.mysqli_fetch_assoc(mysqli_query($dbc,"SELECT config_fields FROM field_config_estimate WHERE `fieldconfigestimateid` = 1"))['config_fields'].',';
	$quote_html = '';
	$html = '';

    $html .= '<table border="0px" style="width:100%;">';
    $html .= '<tr nobr="true">
            <td style="width:70%;">';
    $html .= 'TO : '.get_client($dbc, $businessid);
    //if(get_staff($dbc, $clientid) != '') {
    //    $html .= '<br>'.get_staff($dbc, $clientid);
    //}
    if(get_contact($dbc, $businessid, 'business_address') != '') {
        $html .= '<br>'.get_contact($dbc, $businessid, 'business_address');
        $html .= '<br>'.get_contact($dbc, $businessid, 'city');
        $html .= ', '.get_contact($dbc, $businessid, 'province');
        $html .= '<br>'.get_contact($dbc, $businessid, 'country');
        $html .= ', '.get_contact($dbc, $businessid, 'zip_code');
    }

    if(get_contact($dbc, $businessid, 'office_phone') != '') {
        $html .= '<br>'.get_contact($dbc, $businessid, 'office_phone');
    }
    if(get_contact($dbc, $businessid, 'cell_phone') != '') {
        $html .= '<br>'.get_contact($dbc, $businessid, 'cell_phone');
    }
    if(get_contact($dbc, $businessid, 'email_address') != '') {
        $html .= '<br>'.get_contact($dbc, $businessid, 'email_address');
    }
    $html .= '</td><td style="width:30%; text-align:right;">';
    $html .= 'Cost Estimate No. '.$estimateid.'<br>Date : '.date('Y-m-d').'<br>Expiration Date : '.$_POST['expiry_date'];
	if(strpos($base_field_config, ',AFE Number,') !== FALSE) {
		$html .= 'AFE # <u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;'.$afe_number.'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</u>';
	}
    $html .= '</td></tr>';
    $html .= '</table><br><br>';

    $html .= '<table border="1px" style="padding:3px; border:1px solid black; width:100%;">
            <tr nobr="true" style="background-color:lightgrey; color:black;">
            <th>Sales Person</th><th>Job</th><th>Payment Terms</th><th>Due Period</th></tr>';
    $html .= '<tr nobr="true">
            <td>'.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).'</td><td>'.$estimate_name.'</td><td>'.$payment_terms.'</td><td>'.$payment_due.'</td></tr>';
    $html .= '</table><br><br>';
	$html .= $detail_html;
	$quote_html = $html;

	$html_arr = [];
	if($accordion_type_count > 0) {
		foreach($accordion_types as $accordion_type_html) {
			$html_arr[] = $html.$accordion_type_html;
		}
	}
	$html .= $accordion_html;

	$html_close = '';

    $table_head = '<table border="1px" style="padding:3px; border:1px solid black; width:100%;">
            <tr nobr="true" style="background-color:lightgrey; color:black;">';
	foreach($field_order as $field_data) {
		$data = explode('***',$field_data);
		if($data[1] == '') {
			$data[1] = $data[0];
		}
		switch($data[0]) {
			case 'Type':
				$table_head .= '<th style="width:10%;">'.$data[1].'</th>';
				break;
			case 'Description':
				$table_head .= '<th style="width:50%;">'.$data[1].'</th>';
				break;
			case 'UOM':
				$table_head .= '<th style="width:10%;">'.$data[1].'</th>';
				break;
			case 'Quantity':
				$table_head .= '<th style="width:10%;">'.$data[1].'</th>';
				break;
			case 'Price':
				$table_head .= '<th style="width:10%;">'.$data[1].'</th>';
				break;
			case 'Total':
				$table_head .= '<th style="width:10%;">'.$data[1].'</th>';
				break;
		}
	}
	$table_head .= '</tr>';
    $table_html .= $package_html;
	$table_html .= $promotion_html;
	$table_html .= $custom_html;
	$table_html .= $m_html;
	$table_html .= $s_html;
	$table_html .= $p_html;
	$table_html .= $sred_html;
	$table_html .= $l_html;
	$table_html .= $staff_html;
	$table_html .= $cont_html;
	$table_html .= $c_html;
	$table_html .= $v_html;
	$table_html .= $cust_html;
	$table_html .= $in_html;
	$table_html .= $eq_html;
	$table_html .= $ex_html;
	$table_html .= $other_html;
	$table_foot = '<tr><td colspan="5" border="0px" style="border-left:0px white hidden; border-bottom:0px white hidden;"><p style="text-align:right;">Sub Total</p></td><td>$'.number_format((float)$total_price - $accordion_total, 2, '.', '').'</td></tr></table>';

	if($table_html != '') {
		$acc_field_width = 10;
		$html_close .= $table_head.$table_html.$table_foot;
	}
	$html_close .= '<table border="1px" style="padding:3px; border:1px solid black; width:100%;">';
    $value_config = get_config($dbc, 'quote_tax');

    $quote_tax = explode('*#*',$value_config);

    $total_count = mb_substr_count($value_config,'*#*');
    $tax_rate = 0;
    for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
        $quote_tax_name_rate = explode('**',$quote_tax[$eq_loop]);
        $tax_rate += $quote_tax_name_rate[1];
        if($quote_tax_name_rate[1] != '0') {
            $html_close .= '<tr><td style="width:90%;"><p style="text-align:right;">'.$quote_tax_name_rate[0].'<br>';
            if($quote_tax_name_rate[2] != '') {
                $html_close .= '<em>['.$quote_tax_name_rate[2].']</em>';
            }
            $html_close .= '</p></td><td style="width:10%">'.$quote_tax_name_rate[1].'%</td></tr>';
        }
    }

	foreach($html_arr as $key => $html_types) {
		$type_total = $total_price - $accordion_total;
		$type_total += $accordion_type_totals[$key];
		$html_arr[$key] .= $html_close;
		$html_arr[$key] .= '<tr><td border="0px" style="border-left:0px white hidden; border-top:0px white hidden; border-bottom:0px white hidden; width:90%;"><p style="text-align:right;">Total</p></td><td style="width:10%">$'.number_format((float)$type_total, 2, '.', '').'</td></tr></table><br />'.html_entity_decode(get_config($dbc, 'quote_sign_notes')).html_entity_decode($detail_note);
	}

    $final = ($total_price*$tax_rate)/100;
    $final_total = ($total_price+$final);

    $html_close .= '<tr><td border="0px" style="border-left:0px white hidden; border-top:0px white hidden; border-bottom:0px white hidden; width:90%;"><p style="text-align:right;">Total</p></td><td style="width:10%">$'.number_format((float)$final_total, 2, '.', '').'</td></tr>';

    $html_close .= '</table><br />'.html_entity_decode(get_config($dbc, 'quote_sign_notes')).html_entity_decode($detail_note);

    $html_1 = addslashes($html.$html_close);
	$html_arr_1 = [];
	foreach($html_arr as $html_types) {
		$html_arr_1[] = addslashes($html_types);
	}
	$html_split = implode('**#**', $html_arr_1);
    $review_profit_loss_1 = mysqli_real_escape_string($dbc, $review_profit_loss);

    $review_budget_1 = mysqli_real_escape_string($dbc, $review_budget);

    $history = '';
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(estimateid) AS total_id FROM estimate WHERE `estimateid` = '$estimateid' AND status = 'Submitted'"));

    if($get_config['total_id'] == 1) {
        $get_estimate = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM estimate WHERE `estimateid` = '$estimateid'"));
        if($get_estimate['package'] != $package) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Package on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['promotion'] != $promotion) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Promotion on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['material'] != $material) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Material on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['services'] != $services) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Services on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['products'] != $products) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Products on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['sred'] != $sred) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed SR&ED on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['labour'] != $labour) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Labour on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['client'] != $client) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Client on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['customer'] != $customer) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Customer on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['inventory'] != $inventory) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Inventory on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['equipment'] != $equipment) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Equipment on '.date('Y-m-d H:i:s').'<br>';
        }

        if($get_estimate['staff'] != $staff) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Staff on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['contractor'] != $contractor) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Contractor on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['vendor'] != $vendor) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Vendor on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_estimate['custom'] != $custom) {
            $history .= (decryptIt($_SESSION['first_name'])).' '.decryptIt($_SESSION['last_name']).' Changed Custom on '.date('Y-m-d H:i:s').'<br>';
        }
    }

    $created_date = $_POST['created_date'];
    $start_date = $_POST['start_date'];
    $expiry_date = $_POST['expiry_date'];
    $estimated_completed_date = $_POST['estimated_completed_date'];
    $completion_date = $_POST['completion_date'];
    $assign_staffid = ','.implode(',',$_POST['assign_staffid']).',';
	$quote_arr = [];
	if(in_array('Total',explode(',',$quote_mode))) {
		$quote_arr[] = $quote_html.$html_close;
	}
	if(in_array('Category',explode(',',$quote_mode))) {
		$quote_arr[] = $quote_html. $category_totals.$html_close;
	}
	if($quote_mode == '' || in_array('All',explode(',',$quote_mode))) {
		$quote_arr[] = $html.$html_close;
	}
	$quote_html = htmlentities(implode('**#**', $quote_arr));

    $query_update_report = "UPDATE `estimate` SET `siteid` = '$siteid', `afe_number` = '$afe_number', `payment_terms` = '$payment_terms', `payment_due` = '$payment_due', `companyrcid` = '$maincompanyrcid', `rate_card_type` = '$ratecardtype', `created_date` = '$created_date', `start_date` = '$start_date', `expiry_date` = '$expiry_date', `estimated_completed_date` = '$estimated_completed_date', `completion_date` = '$completion_date', `estimate_name` = '$estimate_name', `package` = '$package', `promotion` = '$promotion', `material` = '$material', `services` = '$services', `products` = '$products', `sred` = '$sred', `labour` = '$labour', `client` = '$client', `customer` = '$customer', `inventory` = '$inventory', `equipment` = '$equipment', `staff` = '$staff', `contractor` = '$contractor', `expense` = '$expense', `vendor` = '$vendor', `custom` = '$custom', `other_detail` = '$other', `total_price` = '$total_price', `total_price_cad` = '$total_cad', `total_price_usd` = '$total_usd', `estimate_data` = '$html_1', `estimate_data_split` = '$html_split', `quote_mode`='$quote_mode', `quote_multiple`='$quote_multiple', `quote_html`='$quote_html', `review_profit_loss` = '$review_profit_loss_1',  `review_budget` = '$review_budget_1', `status` = 'Submitted', `history` = CONCAT(history,'$history'), `budget_price` = '$budget_price', `financial_cost` = '$financial_cost', `financial_price` = '$financial_price',`financial_profit` = '$financial_profit',`financial_margin` = '$financial_margin', `financial_plus_minus` = '$financial_plus_minus', `assign_staffid` = '$assign_staffid' WHERE `estimateid` = '$estimateid'";
    $result_update_report = mysqli_query($dbc, $query_update_report) or die(mysqli_error($dbc));

		$before_change = '';
		$history = "Estimates entry has been updated for estimate id $estimateid. <br />";
		add_update_history($dbc, 'estimates_history', $history, '', $before_change);

    //Comment
    $type = '';
    $note_heading = filter_var($_POST['note_heading'],FILTER_SANITIZE_STRING);

    if($note_heading == 'General') {
        $type = 'note';
    }
    $estimate_comment = htmlentities($_POST['estimate_comment']);
    $t_comment = filter_var($estimate_comment,FILTER_SANITIZE_STRING);
    if($t_comment != '') {
        $email_comment = $_POST['email_comment'];

        if($type != '') {
            $query_insert_ca = "INSERT INTO `estimate_comment` (`estimateid`, `comment`, `email_comment`, `created_date`, `created_by`, `type`, `note_heading`) VALUES ('$estimateid', '$t_comment', '$email_comment', '$created_date', '$created_by', '$type', '$note_heading')";
            $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

						$before_change = '';
            $history = "Estimates comment entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);
        } else {
            $query_update_report = "UPDATE `estimate_detail` SET `$note_heading` = CONCAT($note_heading,'$t_comment') WHERE `estimateid` = '$estimateid'";
            $result_update_report = mysqli_query($dbc, $query_update_report);
						$before_change = '';
						$history = "Estimates detail entry has been updated for estimate id $estimateid. <br />";
						add_update_history($dbc, 'estimates_history', $history, '', $before_change);
        }

        if ($_POST['send_email_on_comment'] == 'Yes') {
            //Code for Send Email
			$sender = (!empty($_POST['email_sender']) ? $_POST['email_sender'] : '');
            $email = get_email($dbc, $email_comment);
            $subject = $_POST['email_subject'];

            $email_body = str_replace(['[NOTE]','[ESTIMATEID]'], [$_POST['estimate_comment'],$estimateid], $_POST['email_body']);

            if($email != '') {
				try {
					send_email($sender, $email, '', '', $subject, $email_body, '');
				} catch(Exception $e) {
					echo "<script>alert('Unable to send email. Please try again later.');</script>";
				}
            }
        }
    }
    //Comment

    $document = $_FILES["document"]["name"];
    for($i = 0; $i < count($_FILES['document']['name']); $i++) {
        if($document[$i] != '') {
            move_uploaded_file($_FILES["document"]["tmp_name"][$i], "download/" . $_FILES["document"]["name"][$i]) ;
            $query_insert_upload = "INSERT INTO `estimate_document` (`estimateid`, `upload`) VALUES ('$estimateid', '$document[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);

						$before_change = '';
            $history = "Estimates document entry has been added. <br />";
            add_update_history($dbc, 'estimates_history', $history, '', $before_change);
        }
    }

    insert_day_overview($dbc, $who_added, ESTIMATE_TILE, $when_added, '', 'Edited '.ESTIMATE_TILE.' '.$estimate_name);

    //include ('add_estimate_company_rate_card.php');

	if((isset($_POST['load_tab']) ? $_POST['load_tab'] : 'Master') == '') {
		echo '<script type="text/javascript"> window.location.replace("'.$main_page.'"); </script>';
	}
}
?>
<script type="text/javascript">
$(document).ready(function() {
    $("#form1").submit(function( event ) {
        var estimateclientid = $("#estimateclientid").val();
        var estimate_name = $("input[name=estimate_name]").val();
        var estimatetype = $("#estimatetype").val();
        var businessid = $("#businessid").val();

        if (businessid == '' || estimate_name == '' || estimatetype == '') {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });

    $("#businessid").change(function() {
		if($(this).val() == 'ADD NEW') {
			$('#new_business_div').show();
		} else if($(this).val() != '') {
            var fromlocation = $("#fromlocation").val();
			window.location = 'add_estimate.php?bid='+this.value+fromlocation;
		}
	});

	$("#estimateclientid").change(function() {
		if($(this).val() == 'ADD NEW') {
			$('#new_contact_div').show();
		} else if($(this).val() != '') {
			var businessid = $("#businessid").val();
            var fromlocation = $("#fromlocation").val();
			window.location = 'add_estimate.php?bid='+businessid+'&clientid='+this.value+fromlocation;
		}
	});

	$("#siteselect").change(function() {
		if($(this).val() == 'ADD NEW') {
			$('#new_site_div').show();
		}
	});

});
function deleteEstimate(sel, hide, blank) {
	var typeId = sel.id;
	var arr = typeId.split('_');

    var estimateid = $("#estimateid").val();

    if(arr[0] == 'deletepackage') {
        $("#packageest_"+arr[1]).val('0');
        countPackage();
    }
    if(arr[0] == 'deletepromotion') {
        $("#promotionest_"+arr[1]).val('0');
        countPromotion();
    }
    if(arr[0] == 'deletecustom') {
        $("#customest_"+arr[1]).val('0');
        countCustom();
    }

    if(arr[0] == 'deletematerial') {
        $("#mestimateprice_"+arr[1]).val('0');
        $("#mestimateqty_"+arr[1]).val('0');
        $("#mestimatetotal_"+arr[1]).val('0');
        countMaterial('delete');
    }

    if(arr[0] == 'deleteservices') {
        $("#sestimateprice_"+arr[1]).val('0');
        $("#sestimateqty_"+arr[1]).val('0');
        $("#sestimatetotal_"+arr[1]).val('0');
        countService('delete');
    }

    if(arr[0] == 'deleteproducts') {
        $("#pestimateprice_"+arr[1]).val('0');
        $("#pestimateqty_"+arr[1]).val('0');
        $("#pestimatetotal_"+arr[1]).val('0');
        countProduct('delete');
    }

	if(arr[0] == 'deleteproductsmisc') {
        $("#ptotalmisc_"+arr[1]).val('0');
        $("#pqtymisc_"+arr[1]).val('0');
        countProduct('delete');
    }

	if(arr[0] == 'deleteinventorymisc') {
        $("#intotalmisc_"+arr[1]).val('0');
        $("#inqtymisc_"+arr[1]).val('0');
        countInventory('delete');
    }

	if(arr[0] == 'deleteequipmentmisc') {
        $("#eqtotalmisc_"+arr[1]).val('0');
        $("#eqqtymisc_"+arr[1]).val('0');
        countEquipment('delete');
    }

	if(arr[0] == 'deletelabourmisc') {
        $("#ltotalmisc_"+arr[1]).val('0');
        $("#lqtymisc_"+arr[1]).val('0');
        countLabour('delete');
    }

    if(arr[0] == 'deletesred') {
        $("#sredestimateprice_"+arr[1]).val('0');
        $("#sredestimateqty_"+arr[1]).val('0');
        $("#sredestimatetotal_"+arr[1]).val('0');
        countSrEd('delete');
    }
    if(arr[0] == 'deletestaff') {
        $("#stestimateprice_"+arr[1]).val('0');
        $("#stestimateqty_"+arr[1]).val('0');
        $("#stestimatetotal_"+arr[1]).val('0');
        countStaff('delete');
    }
    if(arr[0] == 'deletecontractor') {
        $("#cntestimateprice_"+arr[1]).val('0');
        $("#cntestimateqty_"+arr[1]).val('0');
        $("#cntestimatetotal_"+arr[1]).val('0');
        countContractor('delete');
    }

    if(arr[0] == 'deleteclients') {
        $("#clientest_"+arr[1]).val('0');
        countClient();
    }

    if(arr[0] == 'deletevendor') {
        $("#vestimateprice_"+arr[1]).val('0');
        $("#vestimateqty_"+arr[1]).val('0');
        $("#vestimatetotal_"+arr[1]).val('0');
        countVendor('delete');
    }
    if(arr[0] == 'deletecustomer') {
        $("#customerest_"+arr[1]).val('0');
        countCustomer();
    }
    if(arr[0] == 'deleteinventory') {
        $("#inestimateprice_"+arr[1]).val('0');
        $("#inestimateqty_"+arr[1]).val('0');
        $("#inestimatetotal_"+arr[1]).val('0');
        countInventory('delete');
    }
    if(arr[0] == 'deleteequipment') {
        $("#eqestimateprice_"+arr[1]).val('0');
        $("#eqestimateqty_"+arr[1]).val('0');
        $("#eqestimatetotal_"+arr[1]).val('0');
        countEquipment('delete');
    }
    if(arr[0] == 'deletelabour') {
        $("#lestimateprice_"+arr[1]).val('0');
        $("#lestimateqty_"+arr[1]).val('0');
        $("#lestimatetotal_"+arr[1]).val('0');
        countLabour('delete');
    }

    if(estimateid == 0) {
        if(arr[0] == 'deletepackage') {
            alert('If you Delete any Package, then all data Related to this Package will be gone.');
            var packageval = $("#"+blank+arr[1]).val();
            var param = getParameterByName('pid');

            var package_id = param.replace(packageval+",", "");
            var package_id = package_id.replace(",,", ",");

            var promotion_id='';
            $('.promotion_head').each(function () {
                promotion_id += $(this).val()+',';
            });

            var custom_id='';
            $('.custom_head').each(function () {
                custom_id += $(this).val()+',';
            });
            window.location = 'add_estimate.php?estimateid='+estimateid+'&pid='+package_id+'&promoid='+promotion_id+'&cid='+custom_id;
        }

        if(arr[0] == 'deletepromotion') {
            alert('If you Delete any Promotion, then all data Related to this Promotion will be gone.');
            var promoval = $("#"+blank+arr[1]).val();
            var param = getParameterByName('promoid');

            var promotion_id = param.replace(promoval+",", "");
            var promotion_id = promotion_id.replace(",,", ",");

            var package_id='';
            $('.package_head').each(function () {
                package_id += $(this).val()+',';
            });

            var custom_id='';
            $('.custom_head').each(function () {
                custom_id += $(this).val()+',';
            });
            window.location = 'add_estimate.php?estimateid='+estimateid+'&pid='+package_id+'&promoid='+promotion_id+'&cid='+custom_id;
        }

        if(arr[0] == 'deletecustom') {
            alert('If you Delete any Custom item, then all data Related to this Custom item will be gone.');
            var cusval = $("#"+blank+arr[1]).val();
            var param = getParameterByName('cid');

            var custom_id = param.replace(cusval+",", "");
            var custom_id = custom_id.replace(",,", ",");

            var package_id='';
            $('.package_head').each(function () {
                package_id += $(this).val()+',';
            });

            var promotion_id='';
            $('.promotion_head').each(function () {
                promotion_id += $(this).val()+',';
            });

            window.location = 'add_estimate.php?estimateid='+estimateid+'&pid='+package_id+'&promoid='+promotion_id+'&cid='+custom_id;
        }
    }

    $("#"+hide+arr[1]).hide();
    $("#"+blank+arr[1]).val('');

    return false;
}

function toggleMultiples(count, no_update) {
	if(count > 1) {
		$('label:contains(" X ")').each(function() {
			$(this).text(this.textContent.substring(0,this.textContent.search(' X ')) + ' X ' + count);
		});
	} else {
		$('label:contains(" X ")').each(function() {
			$(this).text(this.textContent.substring(0,this.textContent.search(' X ')) + ' X ' + count);
		});
	}
	if(no_update !== true) {
		$('[name*=multi]').not('[name=quote_multiple]').not('readonly').each(function() {
			$(this).val(round2Fixed($('#'+this.id.replace('_multiple','').replace('multi','')).val()*count));
		});
	}
}
</script>
<style>
@media (min-width: 750px) {
	label[class^=col-sm-][data-width],
	div[class^=col-sm-][data-width] {
		width: calc(100% / var(--columns) * var(--width))
	}
}
[data-width="1"] { --width:1 }
[data-width="2"] { --width:2 }
[data-columns="1"] { --columns:1 }
[data-columns="2"] { --columns:2 }
[data-columns="3"] { --columns:3 }
[data-columns="4"] { --columns:4 }
[data-columns="5"] { --columns:5 }
[data-columns="6"] { --columns:6 }
[data-columns="7"] { --columns:7 }
[data-columns="8"] { --columns:8 }
[data-columns="9"] { --columns:9 }
[data-columns="10"] { --columns:10 }
[data-columns="11"] { --columns:11 }
[data-columns="12"] { --columns:12 }
[data-columns="13"] { --columns:13 }
[data-columns="14"] { --columns:14 }
[data-columns="15"] { --columns:15 }
[data-columns="16"] { --columns:16 }
[data-columns="17"] { --columns:17 }
[data-columns="18"] { --columns:18 }
[data-columns="19"] { --columns:19 }
[data-columns="20"] { --columns:20}
[data-columns="21"] { --columns:21 }
[data-columns="22"] { --columns:22}
[data-columns="23"] { --columns:23}
[data-columns="24"] { --columns:24}
[data-columns="25"] { --columns:25}
</style>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('estimate');
$info_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT `field_info` FROM `field_config_estimate`"));
$info_config = explode(',',$info_config['field_info']);
$short_name = 'Short Name/LSD';
$expiry_length = '';
foreach($info_config as $str) {
	$info_arr = explode('#*#',$str);
	if($info_arr[0] == 'short_name') {
		$short_name = $info_arr[1];
	} else if($info_arr[0] == 'expiry_length') {
		$expiry_length = $info_arr[1];
	}
}
?>
<div class="container">
  <div class="row">

    <h1><?= ESTIMATE_TILE ?></h1>
	<div class="gap-top double-gap-bottom"><a href="<?php echo $main_page; ?>" class="btn brand-btn">Back to Dashboard</a></div>

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        <input type="hidden" id="fromlocation" name="fromlocation" value="<?php if (!empty($_GET['from'])) {
            echo '&from=' . $_GET['from'];
        } ?>">

		<div class="pad-left double-gap-bottom">

			<?php $base_field_config_all = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT config_fields FROM field_config_estimate WHERE `fieldconfigestimateid` = 1"));
			$base_field_config = ','.$base_field_config_all['config_fields'].',';
			if($_GET['estimateid']):
				$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_estimate"));
				$value_config = ','.$get_field_config['config_fields'].',';
				$get_tabs = mysqli_query($dbc,"SELECT * FROM `estimate_tab` WHERE `estimate_tab` != ''");
				$estimateConfigValue = $value_config;
				$estimateTab = '';
				$estimateConfigValueCount = mysqli_num_rows($get_tabs);
				$tab_set = false;
				$load_tab = isset($_POST['load_tab']) ? $_POST['load_tab'] : 'Master';
				while($get_tab = mysqli_fetch_assoc($get_tabs)):
					if(!$tab_set) {
						$load_tab = isset($_POST['load_tab']) ? $_POST['load_tab'] : $get_tab['estimate_tab_id'];
						$tab_set = true;
					}
					$active_tab = '';
					$tab_id = $get_tab['estimate_tab_id'];
					if($tab_id == $load_tab) {
						$active_tab = ' active_tab';
						$estimateTab = $get_tab['estimate_tab'];
						$estimateConfigValue = ','.$get_tab['estimate_tab_config'].',';
					}
					?>
					<button type="submit" name="submit" onclick="$('#load_tab').val('<?php echo $tab_id; ?>');" class="btn btn brand-btn btn-lg<?php echo $active_tab; ?>"><?php echo $get_tab['estimate_tab']; ?></button>
				<?php endwhile;
				if($estimateConfigValueCount > 0): ?>
					<button type="submit" name="submit" onclick="$('#load_tab').val('summary');" class="btn btn brand-btn btn-lg<?php echo $load_tab == 'summary' ? ' active_tab' : ''; ?>">Summary</button>
				<?php endif;
			endif; ?>
		</div>
		<input type="hidden" name="load_tab" id="load_tab" value="">

		<?php
        $clientid = '';
        $businessid = '';
        if(!empty($_GET['bid'])) {
            $businessid = $_GET['bid'];
        }
        if(!empty($_GET['clientid'])) {
            $clientid = $_GET['clientid'];
            $businessid = get_contact($dbc, $clientid, 'businessid');
        }
        $siteid = '';
        $afe_number = '';
        $ratecardid = '';
        if(!empty($_GET['ratecardid'])) {
            $ratecardid = $_GET['ratecardid'];
        }
        $estimate_name = '';
        $budget_price = '';
        $disable_business = '';
        $disable_client = '';
        $disable_rc = '';
        $disable_type = '';
        $estimatetype = [];
        $created_date = date('Y-m-d');
        $start_date = '';
        $expiry_date = '';
        $estimated_completed_date = '';
        $completion_date = '';
		$total_cost = 0;
		$total_price = 0;
		$total_price_usd = 0;
		$quote_mode = ['All'];
		$quote_multiple = 1;
		$universal_rc_search = "(`rate_card_name`='')";

        if(!empty($_GET['estimateid'])) {
            $estimateid = $_GET['estimateid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM estimate WHERE estimateid='$estimateid'"));
            $businessid = $get_contact['businessid'];
            $clientid = $get_contact['clientid'];
            if($businessid ==  '' || $businessid ==  0) {
                $businessid = get_contact($dbc, $clientid, 'businessid');
            }
            $siteid = $get_contact['siteid'];
            $afe_number = $get_contact['afe_number'];
            $payment_terms = $get_contact['payment_terms'];
            $payment_due = $get_contact['payment_due'];
            $ratecardid = $get_contact['ratecardid'];
            $companyrcid = $get_contact['companyrcid'];
            $ratecardtypes = $get_contact['rate_card_type'];
            $estimate_name = $get_contact['estimate_name'];
            $budget_price = explode('*#*', $get_contact['budget_price']);
            $disable_business = 'disabled';
            $disable_client = 'disabled';
            $disable_rc = 'disabled';
            $disable_type = 'disabled';

            $estimatetype = explode(',',$get_contact['estimatetype']);
            $created_date = $get_contact['created_date'];
            $start_date = $get_contact['start_date'];
            $expiry_date = $get_contact['expiry_date'];
            $estimated_completed_date = $get_contact['estimated_completed_date'];
            $completion_date = $get_contact['completion_date'];
            $assign_staffid = $get_contact['assign_staffid'];
			$quote_mode = explode(',',$get_contact['quote_mode']);
			$quote_multiple = $get_contact['quote_multiple'];

            $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM rate_card WHERE ratecardid='$ratecardid'"));
            $get_company_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT rate_card_name, rate_categories FROM company_rate_card WHERE companyrcid='$companyrcid'"));
            $company_rate_card_name = $get_company_rc['rate_card_name'];
			$company_rate_categories = $get_company_rc['rate_categories'];
			if(strpos(get_config($dbc, 'universal_rate_fields'),',category,') !== FALSE) {
				$universal_rc_search = "(`rate_card_name`='' AND (IFNULL(`rate_categories`,'')='$company_rate_categories' OR IFNULL(`rate_categories`,'')=''))";
			}

        ?>
        <input type="hidden" id="estimateid" name="estimateid" value="<?php echo $estimateid ?>" />
        <?php   } else { ?>
                <input type="hidden" id="estimateid" name="estimateid" value="0" />
        <?php }
    ?>
    <input type="hidden" name="hidden_clientid" id="hidden_clientid" value="<?php echo $clientid; ?>">
    <input type="hidden" name="hidden_ratecardid" id="hidden_ratecardid" value="<?php echo $ratecardid; ?>">
     <input type="hidden" name="hidden_companyrcid" id="hidden_rcompanyrcid" value="<?php echo $companyrcid; ?>">
   <div class="panel-group" id="accordion2">
        <?php
        $note_add_view = '';
        $info_view = '';
        if(!empty($_GET['note'])) {
            $note_add_view = 'in';
        } else {
            $info_view = 'in';
        } ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_abi" ><?= rtrim(ESTIMATE_TILE, 's') ?> Information<span class="glyphicon glyphicon-minus"></span></a>
				</h4>
			</div>

			<div id="collapse_abi" class="panel-collapse collapse <?php echo $info_view; ?>">
				<div class="panel-body">
					<?php
					include ('add_estimate_basic_info.php');
					?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_rc" >Rate Cards<span class="glyphicon glyphicon-minus"></span></a>
				</h4>
			</div>

			<div id="collapse_rc" class="panel-collapse collapse">
				<div class="panel-body">
					<?php
					include ('add_estimate_rate_cards.php');
					?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff2" >Staff Collaboration<span class="glyphicon glyphicon-plus"></span></a>
				</h4>
			</div>

			<div id="collapse_staff2" class="panel-collapse collapse">
				<div class="panel-body">
					<?php
					include ('add_estimate_assign_staff.php');
					?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_date" >Dates<span class="glyphicon glyphicon-plus"></span></a>
				</h4>
			</div>

			<div id="collapse_date" class="panel-collapse collapse">
				<div class="panel-body">
					<?php
					include ('add_estimate_dates.php');
					?>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_detail" >Details<span class="glyphicon glyphicon-plus"></span></a>
				</h4>
			</div>

			<div id="collapse_detail" class="panel-collapse collapse">
				<div class="panel-body">
					<?php
					include ('add_estimate_detail.php');
					?>
				</div>
			</div>
		</div>

		<?php if(!empty($_GET['estimateid'])) { ?>
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_notes" >
					   Notes<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_notes" class="panel-collapse collapse <?php echo $note_add_view; ?>">
				<div class="panel-body">
				 <?php include ('add_view_estimate_comment.php'); ?>
				</div>
			</div>
		</div>
		<?php } ?>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_doc" >Documents<span class="glyphicon glyphicon-plus"></span></a>
				</h4>
			</div>

			<div id="collapse_doc" class="panel-collapse collapse">
				<div class="panel-body">
					<?php
					include ('add_estimate_documents.php');
					?>
				</div>
			</div>
		</div>

		<!-- Hide this if WASHTECH is using ESTIMATES -->
		<?php if(!isset($washtech_software_checker)) { ?>
		<?php if (strpos($value_config, ','."Budget".',') !== FALSE): ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_basic" >Budget Information<span class="glyphicon glyphicon-plus"></span></a>
					</h4>
				</div>

				<div id="collapse_basic" class="panel-collapse collapse">
					<div class="panel-body">

						<?php
						include ('add_estimate_budget.php');
						?>

					</div>
				</div>
			</div>
		<?php endif; ?>
		<?php } ?>

        <?php if(!empty($_GET['estimateid'])) { ?>
		<?php /*if($estimateConfigValueCount == 0): ?>
			<?php require('add_estimate_default.php'); ?>
		<?php elseif($estimateConfigValueCount > 0 && !isset($_GET['estimatetabid'])): ?>
			<?php include('add_estimate_default_setting.php'); ?>
		<?php else:*/ ?>
			<?php if (strpos($value_config, ','."Package".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Package".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pp" ><?php echo $estimateTab.' '; ?>Package<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_pp" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_package.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Promotion".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Promotion".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_promo" ><?php echo $estimateTab.' '; ?>Promotion<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_promo" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_promotion.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Custom".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Custom".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_cus" ><?php echo $estimateTab.' '; ?>Custom<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_cus" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_custom.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Material".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Material".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_material" ><?php echo $estimateTab.' '; ?>Material<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_material" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_material.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Services".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Services".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_service" ><?php echo $estimateTab.' '; ?>Services<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_service" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_services.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Products".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Products".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Products" ><?php echo $estimateTab.' '; ?>Products<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_Products" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_products.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."SRED".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."SRED".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_sred" ><?php echo $estimateTab.' '; ?>SR&ED<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_sred" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_sred.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Staff".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Staff".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff" ><?php echo $estimateTab.' '; ?>Staff<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_staff" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_staff.php');
							?>
						</div>
					</div>
				</div>
			<?php endif ;?>

			<?php if (strpos($value_config, ','."Contractor".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Contractor".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_contractor" ><?php echo $estimateTab.' '; ?>Contractor<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_contractor" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_contractor.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Clients".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Clients".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_clients" ><?php echo $estimateTab.' '; ?>Clients<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_clients" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_clients.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Vendor Pricelist".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Vendor Pricelist".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_vendor" ><?php echo $estimateTab.' '; ?>Vendor Price List<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_vendor" class="panel-collapse collapse">
						<div class="panel-body">
							<!-- Hide this if WASHTECH is using ESTIMATES -->
							<?php if(!isset($washtech_software_checker)) {
								//include ('add_estimate_vendor.php');
								include ('add_estimate_vendor_order_list.php');
							} else {
								include ('add_estimate_vendor_order_list.php');
							}
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Customer".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Customer".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_customer" ><?php echo $estimateTab.' '; ?>Customer<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_customer" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_customer.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Inventory".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Inventory".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_inv" ><?php echo $estimateTab.' '; ?>Inventory<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_inv" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_inventory.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Equipment".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Equipment".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_equipment" ><?php echo $estimateTab.' '; ?>Equipment<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_equipment" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_equipment.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Labour".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Labour".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_labour" ><?php echo $estimateTab.' '; ?>Labour<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_labour" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_labour.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Expenses".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Expenses".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_expenses" ><?php echo $estimateTab.' '; ?>Expenses<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_expenses" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_expenses.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php if (strpos($value_config, ','."Other".',') !== FALSE): ?>
				<div class="panel panel-default" style="<?php echo (strpos($estimateConfigValue, ','."Other".',') === FALSE) ? "display:none;" : ""; ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_other" ><?php echo $estimateTab.' '; ?>Other<span class="glyphicon glyphicon-plus"></span></a>
						</h4>
					</div>

					<div id="collapse_other" class="panel-collapse collapse">
						<div class="panel-body">
							<?php
							include ('add_estimate_other.php');
							?>
						</div>
					</div>
				</div>
			<?php endif; ?>


			<?php include ('add_estimate_accordions.php'); ?>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_summary" >Summary<span class="glyphicon glyphicon-plus"></span></a>
					</h4>
				</div>

				<div id="collapse_summary" class="panel-collapse collapse">
					<div class="panel-body">
						<?php
						include ('add_estimate_summary.php');
						?>
					</div>
				</div>
			</div>

			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_quote_display" >Quote Display<span class="glyphicon glyphicon-plus"></span></a>
					</h4>
				</div>

				<div id="collapse_quote_display" class="panel-collapse collapse">
					<div class="panel-body">
						<?php
						include ('add_estimate_quote_display.php');
						?>
					</div>
				</div>
			</div>
		<?php } ?>
    </div>

    <?php if(empty($_GET['estimateid'])) { ?>
        <div class="form-group">
            <div class="col-sm-6">
				<span class="popover-examples list-inline" style="margin:15px 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="If you click this, your current estimate will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a href="<?php echo $main_page; ?>" class="btn brand-btn btn-lg">Back</a>
			</div>
            <div class="col-sm-6">
				<button type="submit" name="save" value="save" class="btn brand-btn btn-lg pull-right">Save &amp; Continue</button>
				<span class="popover-examples list-inline pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="After adding in the above headings, click here to save. Stay on this page in order to fill out the rest of the estimate."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			</div>
        </div>
    <?php } else { ?>
        <div class="form-group">
            <div class="col-sm-6">
				<span class="popover-examples list-inline" style="margin:15px 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="If you click this, your current estimate will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a href="<?php echo $main_page; ?>" class="btn brand-btn btn-lg">Back</a>
			</div>
			<div class="col-sm-6">
				<button type="button" name="" value="Submit" class="btn brand-btn btn-lg js_submitter pull-right">Submit</button>
                <button	type="submit" name="submit" style='display:none;' value="Submit" class="btn brand-btn js_submitter_actual btn-lg pull-right">Submit</button>
				<span class="popover-examples list-inline pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save your estimate."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div>
        </div>
    <?php } ?>

    <script type="text/javascript">
	  $('.js_submitter').on( 'click', function () {
		$('.vndr_qty_changer:not([name])').each(function() {
			var act_inv = this.id;
			var arr = act_inv.split('_');
			$('.vndrowhide_'+arr[2]).remove();
			this.remove();
		});
		$('.crc_equipment_qty').each(function() {
			var act_inv = this.id;
			var arr = act_inv.split('_');

			var act_inv = $(this).val();
			if(act_inv > 0) {

            } else {
			    $('.rc_est_equ_'+arr[3]).remove();
			    this.remove();
            }
		});

		$('.crc_products_qty').each(function() {
			var act_inv = this.id;
			var arr = act_inv.split('_');

			var act_inv = $(this).val();
			if(act_inv > 0) {

            } else {
			    $('.rc_est_products_'+arr[3]).remove();
			    this.remove();
            }
		});
		$('.crc_inventory_qty').each(function() {
			var act_inv = this.id;
			var arr = act_inv.split('_');

			var act_inv = $(this).val();
			if(act_inv > 0) {

            } else {
			    $('.rc_est_inventory_'+arr[3]).remove();
			    this.remove();
            }
		});
		$('.crc_labour_qty').each(function() {
			var act_inv = this.id;
			var arr = act_inv.split('_');

			var act_inv = $(this).val();
			if(act_inv > 0) {

            } else {
			    $('.rc_est_labour_'+arr[3]).remove();
			    this.remove();
            }
		});

		setTimeout(
		function() {
			$('.js_submitter_actual').click();
		}, 500);

		toggleMultiples(<?= $quote_multiple ?>, true);
    });
    </script>

    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
