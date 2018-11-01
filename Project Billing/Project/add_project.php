<?php
//Rate CArd Tiles

include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

$contactid_intake = ( isset($_GET['clientid']) ) ? trim($_GET['clientid']) : '';
$intakeid = ( isset($_GET['intakeid']) ) ? trim($_GET['intakeid']) : '';
$from_url = 'project.php';
if(!empty($_GET['from_url'])) {
	$from_url = urldecode($_GET['from_url']);
}

if (isset($_POST['save'])) {
    $who_added = $_SESSION['contactid'];
    $when_added = date('Y-m-d');

    $budget_price = '';
    for($i=0; $i<=16; $i++) {
        $budget_price .= $_POST['budget_price_'.$i].'*#*';
    }
    $budget_price .= $_POST['total_budget'];

    $clientid = filter_var(implode(',',$_POST['projectclientid']),FILTER_SANITIZE_STRING);
    $businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
    $ratecardid = filter_var($_POST['ratecardid'],FILTER_SANITIZE_STRING);

    $project_path = filter_var($_POST['project_path'],FILTER_SANITIZE_STRING);
    if($project_path == 'ADD_NEW_TEMPLATE') {
        $new_project_path = filter_var($_POST['new_project_path'],FILTER_SANITIZE_STRING);

        $milestone_arr = [];
        $timeline_arr = [];
        $temp_checklist_arr = [];
        $temp_ticket_arr = [];
        $temp_workorder_arr = [];
        $checklist_arr = [];
        $ticket_arr = [];
        $workorder_arr = [];

        foreach($_POST as $field => $value) {
            if(strpos($field,'milestone_') !== false) {
                $key = explode('_',$field)[1];
                $milestone_arr[$key] = $value;
            }
            else if(strpos($field,'timeline_') !== false) {
                $key = explode('_',$field)[1];
                $timeline_arr[$key] = $value;
            }
            else if(strpos($field,'checklist_item_') !== false) {
                $key = explode('_',$field)[2];
                $temp_checklist_arr[$key] = implode('*#*',array_filter($value));
            }
            else if(strpos($field,'ticket_item_') !== false) {
                $key = explode('_',$field)[2];
                $tickets_i = [];
                $services_i = $_POST['ticket_service_'.$key];
                foreach($value as $i => $heading) {
                    $tickets_i[] = $heading.'FFMSPLIT'.$services_i[$i];
                }
                $temp_ticket_arr[$key] = implode('*#*',array_filter($tickets_i));
            }
            else if(strpos($field,'work_order_item_') !== false) {
                $key = explode('_',$field)[3];
                $temp_workorder_arr[$key] = implode('*#*',array_filter($value));
            }
        }
        foreach($milestone_arr as $key => $value) {
            if($value != '') {
                $checklist_arr[$key] = $temp_checklist_arr[$key];
                $ticket_arr[$key] = $temp_ticket_arr[$key];
                $workorder_arr[$key] = $temp_workorder_arr[$key];
            } else {
                unset($milestone_arr[$key]);
            }
        }

        $milestone = filter_var(implode('#*#',$milestone_arr),FILTER_SANITIZE_STRING);
        $timeline = filter_var(implode('#*#',$timeline_arr),FILTER_SANITIZE_STRING);
        $checklist = filter_var(implode('#*#',$checklist_arr),FILTER_SANITIZE_STRING);
        $ticket = filter_var(implode('#*#',$ticket_arr),FILTER_SANITIZE_STRING);
        $workorder = filter_var(implode('#*#',$workorder_arr),FILTER_SANITIZE_STRING);

        $query_insert_config = "INSERT INTO `project_path_milestone` (`project_path`, `milestone`, `timeline`, `checklist`, `ticket`, `workorder`)
            VALUES ('$new_project_path', '$milestone', '$timeline', '$checklist', '$ticket', '$workorder')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
        $project_path = mysqli_insert_id($dbc);
    }
    $projecttype = filter_var($_POST['projecttype'],FILTER_SANITIZE_STRING);
    $created_date = $_POST['created_date'];
    $start_date = $_POST['start_date'];
    $estimated_completed_date = $_POST['estimated_completed_date'];
    $completion_date = $_POST['completion_date'];
    $effective_date = $_POST['effective_date'];
    $time_clock_start_date = $_POST['time_clock_start_date'];

    $project_name = filter_var($_POST['project_name'],FILTER_SANITIZE_STRING);

    echo $query_insert_customer = "INSERT INTO `project` (`businessid`, `clientid`, `ratecardid`, `projecttype`, `project_name`, `created_date`, `created_by`, `start_date`, `estimated_completed_date`, `completion_date`, `budget_price`, `project_path`, `effective_date`, `time_clock_start_date`) VALUES ('$businessid', '$clientid', '$ratecardid', '$projecttype', '$project_name', '$created_date', '".$_SESSION['contactid']."', '$start_date', '$estimated_completed_date', '$completion_date', '$budget_price', '$project_path', '$effective_date', '$time_clock_start_date')";
    $result_insert_customer = mysqli_query($dbc, $query_insert_customer);
    $projectid = mysqli_insert_id($dbc);
    $user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
	mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', 'Created ".PROJECT_NOUN."', '$projectid')");

    echo insert_day_overview($dbc, $who_added, 'Project', $when_added, '', 'Added Project '.$project_name);

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

    $detail2_issue = filter_var(htmlentities($_POST['detail2_issue']),FILTER_SANITIZE_STRING);
    $detail2_plan = filter_var(htmlentities($_POST['detail2_plan']),FILTER_SANITIZE_STRING);
    $detail2_do = filter_var(htmlentities($_POST['detail2_do']),FILTER_SANITIZE_STRING);
    $detail2_check = filter_var(htmlentities($_POST['detail2_check']),FILTER_SANITIZE_STRING);
    $detail2_adjust = filter_var(htmlentities($_POST['detail2_adjust']),FILTER_SANITIZE_STRING);

    $detail_procedureid = filter_var($_POST['procedureid'],FILTER_SANITIZE_STRING);
    $detail_quote = filter_var($_POST['quote'],FILTER_SANITIZE_STRING);
    $detail_dwg = filter_var($_POST['dwg'],FILTER_SANITIZE_STRING);
    $detail_quantity = filter_var($_POST['quantity'],FILTER_SANITIZE_STRING);
    $detail_sn = filter_var($_POST['sn'],FILTER_SANITIZE_STRING);
    $detail_totalprojectbudget = filter_var($_POST['totalprojectbudget'],FILTER_SANITIZE_STRING);

    $query_insert_detail = "INSERT INTO `project_detail` (`projectid`, `detail_issue`, `detail_problem`, `detail_gap`, `detail_technical_uncertainty`, `detail_base_knowledge`, `detail_do`, `detail_already_known`, `detail_sources`, `detail_current_designs`, `detail_known_techniques`, `detail_review_needed`, `detail_looking_to_achieve`, `detail_plan`, `detail_next_steps`, `detail_learnt`,  `detail_discovered`,  `detail_tech_advancements`, `detail_work`, `detail_adjustments_needed`, `detail_future_designs`, `detail_check`, `detail_objective`, `detail_targets`, `detail_audience`, `detail_strategy`, `detail_desired_outcome`, `detail_actual_outcome`, `detail2_issue`, `detail2_plan`, `detail2_do`, `detail2_check`, `detail2_adjust`, `detail_procedure_id`, `detail_quote`, `detail_dwg`, `detail_quantity`, `detail_sn`, `detail_total_project_budget`) VALUES ('$projectid', '$detail_issue', '$detail_problem', '$detail_gap', '$detail_technical_uncertainty', '$detail_base_knowledge', '$detail_do', '$detail_already_known', '$detail_sources', '$detail_current_designs', '$detail_known_techniques', '$detail_review_needed', '$detail_looking_to_achieve', '$detail_plan', '$detail_next_steps', '$detail_learnt',  '$detail_discovered',  '$detail_tech_advancements', '$detail_work',  '$detail_adjustments_needed', '$detail_future_designs', '$detail_check', '$detail_objective', '$detail_targets', '$detail_audience', '$detail_strategy', '$detail_desired_outcome', '$detail_actual_outcome', '$detail2_issue', '$detail2_plan', '$detail2_do', '$detail2_check', '$detail2_adjust', '$detail_procedureid', '$detail_quote', '$detail_dwg', '$detail_quantity', '$detail_sn', '$detail_totalprojectbudget', '$detail_procedureid', '$detail_quote', '$detail_dwg', '$detail_quantity', '$detail_sn', '$detail_totalprojectbudget')";

    $result_insert_detail = mysqli_query($dbc, $query_insert_detail);

    $document = htmlspecialchars($_FILES["document"]["name"], ENT_QUOTES);
    for($i = 0; $i < count($_FILES['document']['name']); $i++) {
        if($document[$i] != '') {
			$label = filter_var($_POST['document_label'][$i], FILTER_SANITIZE_STRING);
            move_uploaded_file($_FILES["document"]["tmp_name"][$i], "download/" . $_FILES["document"]["name"][$i]) ;
            $query_insert_upload = "INSERT INTO `project_document` (`projectid`, `upload`, `label`) VALUES ('$projectid', '$document[$i]', '$label')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }
    }

    for($i = 0; $i < count($_POST['support_link']); $i++) {
        $support_link = $_POST['support_link'][$i];
		$label = filter_var($_POST['support_link_label'][$i], FILTER_SANITIZE_STRING);

        if($support_link != '') {
            $query_insert_client_doc = "INSERT INTO `project_document` (`projectid`, `link`, `label`) VALUES ('$projectid', '$support_link', '$label')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
        }
    }

	$contactid_intake = $_POST['contactid_intake'];
	$intakeid = $_POST['intakeid'];
	if ( !empty($intakeid) ) {
		$row			= mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `intakeid`, `intake_file` FROM `intake` WHERE `intakeid`='$intakeid'" ) );
		$support_link	= WEBSITE_URL . '/Intake/' . $row['intake_file'];
		$query_insert_client_doc	= "INSERT INTO `project_document` (`projectid`, `link`) VALUES ('$projectid', '$support_link')";
		$result_insert_client_doc	= mysqli_query($dbc, $query_insert_client_doc);

		$assigned_date = date('Y-m-d');
		$update_intake = mysqli_query ( $dbc, "UPDATE `intake` SET `assigned_date`='$assigned_date' WHERE `intakeid`='$intakeid'" );
	}
	$intake_url_var = '&intakeid=' . $intakeid . '&clientid=' . $contactid_intake;

	// Create the checklists, work orders, and tickets from the default checklist for each milestone
    $milestone_list = explode('#*#', get_project_path_milestone($dbc, $project_path, 'milestone'));
    $checklist = explode('#*#', get_project_path_milestone($dbc, $project_path, 'checklist'));
    $tickets = explode('#*#', get_project_path_milestone($dbc, $project_path, 'ticket'));
    $workorders = explode('#*#', get_project_path_milestone($dbc, $project_path, 'workorder'));
	foreach($milestone_list as $key => $milestone) {
		$checklist_items = explode('*#*', $checklist[$key]);
		foreach($checklist_items as $sort => $item) {
			if($item != '') {
				$query = "INSERT INTO `tasklist` (`projectid`, `task_milestone_timeline`, `heading`, `task`, `sort`, `updated_date`) VALUES ('$projectid', '$milestone', '$item', '$item', '$sort', '".date('Y-m-d')."')";
				$result = mysqli_query($dbc, $query);
			}
		}
		$ticket_list = explode('*#*', $tickets[$key]);
		foreach($ticket_list as $ticket) {
			if($ticket != '') {
				$ticket = explode('FFMSPLIT',$ticket);
				$heading = $ticket[0];
				$serviceid = $ticket[1];
				$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT `service_type`, `category`, `heading` FROM `services` WHERE `serviceid`='$serviceid'"));
				$query = "INSERT INTO `tickets` (`businessid`, `projectid`, `service_type`, `service`, `sub_heading`, `heading`, `project_path`, `milestone_timeline`)
					VALUES ('$businessid', '$projectid', '".$service['service_type']."', '".$service['category']."', '".$service['heading']."', '$heading', '$project_path', '$milestone')";
				$result = mysqli_query($dbc, $query);
			}
		}
		$workorder_list = explode('*#*', $workorders[$key]);
		foreach($workorder_list as $workorder) {
			if($workorder != '') {
				$query = "INSERT INTO `workorders` (`businessid`, `projectid`, `heading`)
					VALUES ('$businessid', '$projectid', '$workorder')";
			}
		}
	}

    echo '<script type="text/javascript">  window.location.replace("add_project.php?projectid='.$projectid.$intake_url_var.'&from_url='.$from_url.'"); </script>';
}

if (isset($_POST['submit'])) {
    $projectid = $_POST['projectid'];
    $clientid = get_project($dbc, $projectid, 'clientid');
    $businessid = get_estimate($dbc, $projectid, 'businessid');
    $who_added = $_SESSION['contactid'];
    $when_added = date('Y-m-d');

    $budget_price = '';
    for($i=0; $i<=16; $i++) {
        $budget_price .= $_POST['budget_price_'.$i].'*#*';
    }
    $budget_price .= $_POST['total_budget'];

    $ratecardid = filter_var($_POST['ratecardid'],FILTER_SANITIZE_STRING);
    $project_name = filter_var($_POST['project_name'],FILTER_SANITIZE_STRING);
    $total_price = 0;
    $desc = '';

    echo insert_day_overview($dbc, $who_added, 'Project', $when_added, '', 'Edited Project '.$project_name);

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT config_fields_quote FROM field_config_project"));
    $config_fields_quote = ','.$get_field_config['config_fields_quote'].',';

    $temp_ticket = mysqli_query($dbc, "DELETE FROM temp_ticket WHERE quoteid='$projectid'");

    //Packages
    $package = '';
    $package_html = '';
    $review_profit_loss = '';
    $review_budget = '';

    $financial_cost = 0;
    $financial_price = 0;
    $financial_plus_minus = 0;

    $total_package = 0;
    $j=0;
    foreach ($_POST['packageid'] as $packageid_all) {
        if($packageid_all != '') {
            $package .= $packageid_all.'#'.$_POST['packageprojectprice'][$j].'**';
            $total_price += $_POST['packageprojectprice'][$j];
            $total_package += $_POST['packageprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM package WHERE packageid='$packageid_all'"));

            $package_html .= '<tr nobr="true">';
            $package_html .= '<td>Package</td>';

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

            $package_html .= '<td>-</td>';
            $package_html .= '<td>-</td>';
            $package_html .= '<td>$'.$_POST['packageprojectprice'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['packageprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['packageprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['packageprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Packages</td><td>'.$query['service_type'].' : '.$query['category'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['packageprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['packageprojectprice'][$j];

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
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Package', '$packageid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_package != 0) {
        $review_budget .= '<tr><td>Packages</td><td>$'.$_POST['budget_price_0'].'</td> <td>$'.$total_package.'</td></tr>';
    }

    //Promotion
    $promotion = '';
    $promotion_html = '';
    $total_promotion = 0;
    $j=0;
    foreach ($_POST['promotionid'] as $promotionid_all) {
        if($promotionid_all != '') {
            $promotion .= $promotionid_all.'#'.$_POST['promotionprojectprice'][$j].'**';
            $total_price += $_POST['promotionprojectprice'][$j];
            $total_promotion += $_POST['promotionprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM promotion WHERE promotionid='$promotionid_all'"));

            $promotion_html .= '<tr nobr="true">';
            $promotion_html .= '<td>Promotion</td>';

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

            $promotion_html .= '<td>-</td>';
            $promotion_html .= '<td>-</td>';
            $promotion_html .= '<td>$'.$_POST['promotionprojectprice'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['promotionprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['promotionprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['promotionprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Promotions</td><td>'.$query['service_type'].' : '.$query['category'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['promotionprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['promotionprojectprice'][$j];

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
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Promotion', '$promotionid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_promotion != 0) {
        $review_budget .= '<tr><td>Promotions</td><td>$'.$_POST['budget_price_1'].'</td> <td>$'.$total_promotion.'</td></tr>';
    }

    //Custom
    $custom = '';
    $custom_html = '';
    $total_custom = 0;
    $j=0;
    foreach ($_POST['customid'] as $customid_all) {
        if($customid_all != '') {
            $custom .= $customid_all.'#'.$_POST['customprojectprice'][$j].'**';
            $total_price += $_POST['customprojectprice'][$j];
            $total_custom += $_POST['customprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM custom WHERE customid='$customid_all'"));

            $custom_html .= '<tr nobr="true">';
            $custom_html .= '<td>Custom</td>';

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

            $custom_html .= '<td>-</td>';
            $custom_html .= '<td>-</td>';
            $custom_html .= '<td>$'.$_POST['customprojectprice'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['customprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['customprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['customprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Custom</td><td>'.$query['service_type'].' : '.$query['category'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['customprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['customprojectprice'][$j];

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
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Custom', '$customid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

    if($total_custom != 0) {
        $review_budget .= '<tr><td>Custom</td><td>$'.$_POST['budget_price_2'].'</td> <td>$'.$total_custom.'</td></tr>';
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
            $material .= $materialid_all.'#'.$_POST['mprojectprice'][$j].'#'.$_POST['mprojectqty'][$j].'**';
            $total_price += $_POST['mprojectprice'][$j]*$_POST['mprojectqty'][$j];
            $total_material += $_POST['mprojectprice'][$j]*$_POST['mprojectqty'][$j];

            $material_total += $_POST['mprojectqty'][$j];
            $material_price_total += $_POST['mprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM material WHERE materialid='$materialid_all'"));

            $m_html .= '<tr nobr="true">';
            $m_html .= '<td>Material</td>';

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
            if (strpos($config_fields_quote, ','."Material Weight Per Feet".',') !== FALSE) {
                $m_html .= 'Weight Per Foot : '.$query['weight_per_feet'].'<br>';
            }
            $m_html .= '</td>';

            $m_html .= '<td>$'.$_POST['mprojectprice'][$j].'</td>';
            $m_html .= '<td>'.$_POST['mprojectqty'][$j].'</td>';
            $m_html .= '<td>$'.$_POST['mprojecttotal'][$j].'</td></tr>';

            $color_off = '';
            if($query['price'] > $_POST['mprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['price']-$_POST['mprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['mprojectprice'][$j]-$query['price'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Material</td><td>'.$query['code'].' - '.decryptIt($query['name']).'</td> <td>$'.$query['price'].'</td><td>$'.$_POST['mprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['price'];
            $financial_price += $_POST['mprojectprice'][$j];

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
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Material', '$materialid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }

    if($total_material != 0) {
        $review_budget .= '<tr><td>Material</td><td>$'.$_POST['budget_price_14'].'</td> <td>$'.$total_material.'</td></tr>';
    }

    //Services
    $services = '';
    $s_html = '';
    $total_service = 0;
    $j=0;
    $service_total = 0;
    $service_price_total = 0;
    foreach ($_POST['serviceid'] as $serviceid_all) {
        if($serviceid_all != '') {
            $services .= $serviceid_all.'#'.$_POST['sprojectprice'][$j].'#'.$_POST['sprojectqty'][$j].'**';
            $total_price += $_POST['sprojectprice'][$j]*$_POST['sprojectqty'][$j];
            $total_service += $_POST['sprojectprice'][$j]*$_POST['sprojectqty'][$j];

            $service_total += $_POST['sprojectqty'][$j];
            $service_price_total += $_POST['sprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM services WHERE serviceid='$serviceid_all'"));

            $s_html .= '<tr nobr="true">';
            $s_html .= '<td>Service</td>';

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

            $s_html .= '<td>$'.$_POST['sprojectprice'][$j].'</td>';
            $s_html .= '<td>'.$_POST['sprojectqty'][$j].'</td>';
            $s_html .= '<td>$'.$_POST['sprojecttotal'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['sprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['sprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['sprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Services</td><td>'.$query['service_type'].' : '.$query['category'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['sprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['sprojectprice'][$j];

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
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Service', '$serviceid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_service != 0) {
        $review_budget .= '<tr><td>Services</td><td>$'.$_POST['budget_price_3'].'</td> <td>$'.$total_service.'</td></tr>';
    }

    //Products
    $products = '';
    $p_html = '';
    $total_product = 0;
    $j=0;
    $product_total = 0;
    $product_price_total = 0;
    foreach ($_POST['productid'] as $productid_all) {
        if($productid_all != '') {
            $products .= $productid_all.'#'.$_POST['pprojectprice'][$j].'#'.$_POST['pprojectqty'][$j].'**';
            $total_price += $_POST['pprojectprice'][$j]*$_POST['pprojectqty'][$j];
            $total_product += $_POST['pprojectprice'][$j]*$_POST['pprojectqty'][$j];

            $product_total += $_POST['pprojectqty'][$j];
            $product_price_total += $_POST['pprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM products WHERE productid='$productid_all'"));

            $p_html .= '<tr nobr="true">';
            $p_html .= '<td>Product</td>';

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

            $p_html .= '<td>$'.$_POST['pprojectprice'][$j].'</td>';
            $p_html .= '<td>'.$_POST['pprojectqty'][$j].'</td>';
            $p_html .= '<td>$'.$_POST['pprojecttotal'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['pprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['pprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['pprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Products</td><td>'.$query['product_type'].' : '.$query['category'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['pprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['pprojectprice'][$j];

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
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `product_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Product', '$productid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_product != 0) {
        $review_budget .= '<tr><td>Products</td><td>$'.$_POST['budget_price_16'].'</td> <td>$'.$total_product.'</td></tr>';
    }

    //SR & ED
    $sred = '';
    $sred_html = '';
    $total_sred = 0;
    $j=0;
    $sred_total = 0;
    $sred_price_total = 0;
    foreach ($_POST['sredid'] as $sredid_all) {
        if($sredid_all != '') {
            $sred .= $sredid_all.'#'.$_POST['sredprojectprice'][$j].'#'.$_POST['sredprojectqty'][$j].'**';
            $total_price += $_POST['sredprojectprice'][$j]*$_POST['sredprojectqty'][$j];
            $total_sred += $_POST['sredprojectprice'][$j]*$_POST['sredprojectqty'][$j];

            $sred_total += $_POST['sredprojectqty'][$j];
            $sred_price_total += $_POST['sredprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM sred WHERE sredid='$sredid_all'"));

            $sred_html .= '<tr nobr="true">';
            $sred_html .= '<td>SRED</td>';

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

            $sred_html .= '<td>$'.$_POST['sredprojectprice'][$j].'</td>';
            $sred_html .= '<td>'.$_POST['sredprojectqty'][$j].'</td>';
            $sred_html .= '<td>$'.$_POST['sredprojecttotal'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['sredprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['sredprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['sredprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>SR&ED</td><td>'.$query['sred_type'].' : '.$query['category'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['sredprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['sredprojectprice'][$j];

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
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'SRED', '$sredid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_sred != 0) {
        $review_budget .= '<tr><td>SR&ED</td><td>$'.$_POST['budget_price_15'].'</td> <td>$'.$total_sred.'</td></tr>';
    }

    //Staff
    $staff = '';
    $staff_html = '';
    $total_staff = 0;
    $j=0;
    $staff_total = 0;
    $staff_price_total = 0;
    foreach ($_POST['contactid'] as $contactid_all) {
        if($contactid_all != '') {
            $staff .= $contactid_all.'#'.$_POST['stprojectprice'][$j].'#'.$_POST['stprojectqty'][$j].'**';
            $total_price += $_POST['stprojectprice'][$j]*$_POST['stprojectqty'][$j];
            $total_staff += $_POST['stprojectprice'][$j]*$_POST['stprojectqty'][$j];

            $staff_total += $_POST['stprojectqty'][$j];
            $staff_price_total += $_POST['stprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name, cost, description, quote_description  FROM contacts WHERE contactid='$contactid_all'"));

            $staff_html .= '<tr nobr="true">';
            $staff_html .= '<td>Staff</td>';

            $staff_html .= '<td>';
            if (strpos($config_fields_quote, ','."Staff Contact Person".',') !== FALSE) {
                $staff_html .= 'Contact Person : '.$query['first_name'].' '.$query['last_name'].'<br>';
            }
            if (strpos($config_fields_quote, ','."Staff Description".',') !== FALSE) {
                $staff_html .= 'Description : '.$query['description'].'<br>';
            }
            if (strpos($config_fields_quote, ','."Staff Quote Description".',') !== FALSE) {
                $staff_html .= 'Description : '.$query['quote_description'].'<br>';
            }
            $staff_html .= '</td>';

            $staff_html .= '<td>$'.$_POST['stprojectprice'][$j].'</td>';
            $staff_html .= '<td>'.$_POST['stprojectqty'][$j].'</td>';
            $staff_html .= '<td>$'.$_POST['stprojecttotal'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['stprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['stprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green;"';
                $plus_minus = $_POST['stprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Staff</td><td>'.$query['first_name'].' '.$query['last_name'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['stprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['stprojectprice'][$j];

            $temp_ticket_desc = '';
            $temp_ticket_desc .= 'Contact Person : '.$query['first_name'].' '.$query['last_name'].'<br>';
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }
            $st = $query['first_name'].' '.$query['last_name'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Staff', '$contactid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_staff != 0) {
        $review_budget .= '<tr><td>Staff</td><td>$'.$_POST['budget_price_4'].'</td> <td>$'.$total_staff.'</td></tr>';
    }

    //Contractor
    $contractor = '';
    $cont_html = '';
    $total_contractor = 0;
    $j=0;
    $contractor_total = 0;
    $contractor_price_total = 0;
    foreach ($_POST['contractorid'] as $contractorid_all) {
        if($contractorid_all != '') {
            $contractor .= $contractorid_all.'#'.$_POST['cntprojectprice'][$j].'#'.$_POST['cntprojectqty'][$j].'**';
            $total_price += $_POST['cntprojectprice'][$j]*$_POST['cntprojectqty'][$j];
            $total_contractor += $_POST['cntprojectprice'][$j]*$_POST['cntprojectqty'][$j];

            $contractor_total += $_POST['cntprojectqty'][$j];
            $contractor_price_total += $_POST['cntprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name, description, quote_description, cost  FROM contacts WHERE contactid='$contractorid_all'"));

            $cont_html .= '<tr nobr="true">';
            $cont_html .= '<td>Contractor</td>';

            $cont_html .= '<td>';
            if (strpos($config_fields_quote, ','."Contractor Contact Person".',') !== FALSE) {
                $cont_html .= 'Contact Person : '.$query['first_name'].' '.$query['last_name'].'<br>';
            }
            if (strpos($config_fields_quote, ','."Contractor Description".',') !== FALSE) {
                $cont_html .= 'Description : '.$query['description'].'<br>';
            }
            if (strpos($config_fields_quote, ','."Contractor Quote Description".',') !== FALSE) {
                $cont_html .= 'Description : '.$query['quote_description'].'<br>';
            }
            $cont_html .= '</td>';

            $cont_html .= '<td>$'.$_POST['cntprojectprice'][$j].'</td>';
            $cont_html .= '<td>'.$_POST['cntprojectqty'][$j].'</td>';
            $cont_html .= '<td>$'.$_POST['cntprojecttotal'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['cntprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['cntprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['cntprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Contractor</td><td>'.$query['first_name'].' '.$query['last_name'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['cntprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['cntprojectprice'][$j];

            $temp_ticket_desc = '';
            $temp_ticket_desc .= 'Contact Person : '.$query['first_name'].' '.$query['last_name'].'<br>';
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }
            $st = $query['first_name'].' '.$query['last_name'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Contractor', '$contractorid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_contractor != 0) {
        $review_budget .= '<tr><td>Contractor</td><td>$'.$_POST['budget_price_5'].'</td> <td>$'.$total_contractor.'</td></tr>';
    }

    //Client
    $client = '';
    $c_html = '';
    $total_client = 0;
    $j=0;
    foreach ($_POST['clientid'] as $clientid_all) {
        if($clientid_all != '') {
            $client .= $clientid_all.'#'.$_POST['clprojectprice'][$j].'**';
            $total_price += $_POST['clprojectprice'][$j];
            $total_client += $_POST['clprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name, first_name, last_name, description, quote_description, cost FROM contacts WHERE contactid='$clientid_all'"));

            $c_html .= '<tr nobr="true">';
            $c_html .= '<td>Client</td>';

            $c_html .= '<td>';
            if (strpos($config_fields_quote, ','."Clients Client Name".',') !== FALSE) {
                $c_html .= 'Client : '.decryptIt($query['name']).'<br>';
            }
            if (strpos($config_fields_quote, ','."Clients Contact Person".',') !== FALSE) {
                $c_html .= 'Contact Person : '.$query['first_name'].' '.$query['last_name'].'<br>';
            }
            if (strpos($config_fields_quote, ','."Clients Description".',') !== FALSE) {
                $c_html .= 'Description : '.$query['description'].'<br>';
            }
            if (strpos($config_fields_quote, ','."Clients Quote Description".',') !== FALSE) {
                $c_html .= 'Description : '.$query['quote_description'].'<br>';
            }
            $c_html .= '</td>';

            $c_html .= '<td>-</td>';
            $c_html .= '<td>-</td>';
            $c_html .= '<td>$'.$_POST['clprojectprice'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['clprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['clprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['clprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Clients</td><td>'.decryptIt($query['name']).'-'.$query['first_name'].' '.$query['last_name'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['clprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['clprojectprice'][$j];

            $temp_ticket_desc = '';
            $temp_ticket_desc .= 'Client : '.decryptIt($query['name']).'<br>';
            $temp_ticket_desc .= 'Contact Person : '.$query['first_name'].' '.$query['last_name'].'<br>';
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }
            $st = $query['first_name'].' '.$query['last_name'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Client', '$clientid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_client != 0) {
        $review_budget .= '<tr><td>Clients</td><td>$'.$_POST['budget_price_6'].'</td> <td>$'.$total_client.'</td></tr>';
    }

    //Vendor
    $vendor = '';
    $v_html = '';
    $total_vendor = 0;
    $j=0;
    $vendor_total = 0;
    $vendor_price_total = 0;
    foreach ($_POST['vendorperson'] as $vendorperson_all) {
        if($vendorperson_all != '') {
            $vendor .= $vendorperson_all.'#'.$_POST['vprojectprice'][$j].'#'.$_POST['vprojectqty'][$j].'**';
            $total_price += $_POST['vprojectprice'][$j]*$_POST['vprojectqty'][$j];
            $total_vendor += $_POST['vprojectprice'][$j]*$_POST['vprojectqty'][$j];

            $vendor_total += $_POST['vprojectqty'][$j];
            $vendor_price_total += $_POST['vprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT vp.*, c.name AS vendor_name FROM vendor_pricelist vp, contacts c WHERE c.contactid = vp.vendorid AND vp.pricelistid='$vendorperson_all'"));

            $v_html .= '<tr nobr="true">';
            $v_html .= '<td>Pricelist</td>';

            $v_html .= '<td>';

            if (strpos($config_fields_quote, ','."Vendor Pricelist Vendor".',') !== FALSE) {
                $v_html .= 'Vendor : '.$query['vendor_name'].'<br>';
            }
            if (strpos($config_fields_quote, ','."Vendor Pricelist Price List".',') !== FALSE) {
                $v_html .= 'Price List : '.$query['pricelist_name'].'<br>';
            }
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
                $v_html .= 'Description : '.$query['description'].'<br>';
            }
            if (strpos($config_fields_quote, ','."Vendor Pricelist Quote Description".',') !== FALSE) {
                $v_html .= 'Description : '.$query['quote_description'].'<br>';
            }
            $v_html .= '</td>';

            $v_html .= '<td>$'.$_POST['vprojectprice'][$j].'</td>';
            $v_html .= '<td>'.$_POST['vprojectqty'][$j].'</td>';
            $v_html .= '<td>$'.$_POST['vprojecttotal'][$j].'</td></tr>';

            $color_off = '';
            if($query['cdn_cpu'] > $_POST['vprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cdn_cpu']-$_POST['vprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['vprojectprice'][$j]-$query['cdn_cpu'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Vendor Price List</td><td>'.$query['pricelist_name'].' : '.$query['category'].' : '.decryptIt($query['name']).'</td> <td>$'.$query['cdn_cpu'].'</td><td>$'.$_POST['vprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cdn_cpu'];
            $financial_price += $_POST['vprojectprice'][$j];

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
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }
            $st = $query['pricelist_name'];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Vendor Price List', '$vendorperson_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_vendor != 0) {
        $review_budget .= '<tr><td>Vendor Price List</td><td>$'.$_POST['budget_price_7'].'</td> <td>$'.$total_vendor.'</td></tr>';
    }

    //customer
    $customer = '';
    $cust_html = '';
    $total_customer = 0;
    $j=0;
    foreach ($_POST['customerid'] as $customerid_all) {
        if($customerid_all != '') {
            $customer .= $customerid_all.'#'.$_POST['custprojectprice'][$j].'**';
            $total_price += $_POST['custprojectprice'][$j];
            $total_customer += $_POST['custprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name, first_name, last_name, description, quote_description, cost FROM contacts WHERE contactid='$customerid_all'"));

            $cust_html .= '<tr nobr="true">';
            $cust_html .= '<td>Customer</td>';

            $cust_html .= '<td>';
            if (strpos($config_fields_quote, ','."Customer Client Name".',') !== FALSE) {
                $cust_html .= 'Customer : '.decryptIt($query['name']).'<br>';
            }
            if (strpos($config_fields_quote, ','."Customer Contact Person".',') !== FALSE) {
                $cust_html .= 'Contact Person : '.$query['first_name'].' '.$query['last_name'].'<br>';
            }
            if (strpos($config_fields_quote, ','."Customer Description".',') !== FALSE) {
                $cust_html .= 'Description : '.$query['description'].'<br>';
            }
            if (strpos($config_fields_quote, ','."Customer Quote Description".',') !== FALSE) {
                $cust_html .= 'Description : '.$query['quote_description'].'<br>';
            }
            $cust_html .= '</td>';

            $cust_html .= '<td>-</td>';
            $cust_html .= '<td>-</td>';
            $cust_html .= '<td>$'.$_POST['custprojectprice'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['custprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['custprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['custprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Customer</td><td>'.decryptIt($query['name']).'-'.$query['first_name'].' '.$query['last_name'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['custprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['custprojectprice'][$j];

            $temp_ticket_desc = '';
            $temp_ticket_desc .= 'Customer : '.decryptIt($query['name']).'<br>';
            $temp_ticket_desc .= 'Contact Person : '.$query['first_name'].' '.$query['last_name'].'<br>';
            if($query['description'] != '') {
                $temp_ticket_desc .= 'Description : '.$query['description'].'<br>';
            }

            $st = decryptIt($query['name']);
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Customer', '$customerid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_customer != 0) {
        $review_budget .= '<tr><td>Customer</td><td>$'.$_POST['budget_price_8'].'</td> <td>$'.$total_customer.'</td></tr>';
    }

    // Inventory
    $inventory = '';
    $in_html = '';
    $total_inventory = 0;
    $j=0;
    $inventory_total = 0;
    $inventory_price_total = 0;
    foreach ($_POST['inventoryid'] as $inventoryid_all) {
        if($inventoryid_all != '') {
            $inventory .= $inventoryid_all.'#'.$_POST['inprojectprice'][$j].'#'.$_POST['inprojectqty'][$j].'**';
            $total_price += $_POST['inprojectprice'][$j]*$_POST['inprojectqty'][$j];
            $total_inventory += $_POST['inprojectprice'][$j]*$_POST['inprojectqty'][$j];

            $inventory_total += $_POST['inprojectqty'][$j];
            $inventory_price_total += $_POST['inprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM inventory WHERE inventoryid='$inventoryid_all'"));

            $in_html .= '<tr nobr="true">';
            $in_html .= '<td>Inventory</td>';

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

            $in_html .= '<td>$'.$_POST['inprojectprice'][$j].'</td>';
            $in_html .= '<td>'.$_POST['inprojectqty'][$j].'</td>';
            $in_html .= '<td>$'.$_POST['inprojecttotal'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['inprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['inprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['inprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Inventory</td><td>'.$query['category'].' - '.$query['code'].' : '.$query['part_no'].' - '.decryptIt($query['name']).'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['inprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['inprojectprice'][$j];

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
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Inventory', '$inventoryid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_inventory != 0) {
        $review_budget .= '<tr><td>Inventory</td><td>$'.$_POST['budget_price_9'].'</td> <td>$'.$total_inventory.'</td></tr>';
    }

    //Equipemt
    $equipment = '';
    $eq_html = '';
    $total_equipment = 0;
    $j=0;
    $equipment_total = 0;
    $equipment_price_total = 0;

    foreach ($_POST['equipmentid'] as $equipmentid_all) {
        if($equipmentid_all != '') {
            $equipment .= $equipmentid_all.'#'.$_POST['eqprojectprice'][$j].'#'.$_POST['eqprojectqty'][$j].'**';
            $total_price += $_POST['eqprojectprice'][$j]*$_POST['eqprojectqty'][$j];
            $total_equipment += $_POST['eqprojectprice'][$j]*$_POST['eqprojectqty'][$j];

            $equipment_total += $_POST['eqprojectqty'][$j];
            $equipment_price_total += $_POST['eqprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM equipment WHERE equipmentid='$equipmentid_all'"));

            $eq_html .= '<tr nobr="true">';
            $eq_html .= '<td>Equipment</td>';

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

            $eq_html .= '<td>$'.$_POST['eqprojectprice'][$j].'</td>';
            $eq_html .= '<td>'.$_POST['eqprojectqty'][$j].'</td>';
            $eq_html .= '<td>$'.$_POST['eqprojecttotal'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['eqprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['eqprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['eqprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Equipment</td><td>'.$query['category'].' - '.$query['unit_number'].' : '.$query['serial_number'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['eqprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['eqprojectprice'][$j];

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
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Equipment', '$equipmentid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_equipment != 0) {
        $review_budget .= '<tr><td>Equipment</td><td>$'.$_POST['budget_price_10'].'</td> <td>$'.$total_equipment.'</td></tr>';
    }

    //Labour
    $labour = '';
    $l_html = '';
    $total_labour = 0;
    $j=0;
    $labour_total = 0;
    $labour_price_total = 0;
    foreach ($_POST['labourid'] as $labourid_all) {
        if($labourid_all != '') {
            $labour .= $labourid_all.'#'.$_POST['lprojectprice'][$j].'#'.$_POST['lprojectqty'][$j].'**';
            $total_price += $_POST['lprojectprice'][$j]*$_POST['lprojectqty'][$j];
            $total_labour += $_POST['lprojectprice'][$j]*$_POST['lprojectqty'][$j];

            $labour_total += $_POST['lprojectqty'][$j];
            $labour_price_total += $_POST['lprojectprice'][$j];

            $query = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM labour WHERE labourid='$labourid_all'"));

            $l_html .= '<tr nobr="true">';
            $l_html .= '<td>Labour</td>';

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

            $l_html .= '<td>$'.$_POST['lprojectprice'][$j].'</td>';
            $l_html .= '<td>'.$_POST['lprojectqty'][$j].'</td>';
            $l_html .= '<td>$'.$_POST['lprojecttotal'][$j].'</td></tr>';

            $color_off = '';
            if($query['cost'] > $_POST['lprojectprice'][$j]) {
                $color_off = 'style = "color:red; "';
                $plus_minus = $query['cost']-$_POST['lprojectprice'][$j];
                $financial_plus_minus -= $plus_minus;
            } else {
                $color_off = 'style = "color:green; "';
                $plus_minus = $_POST['lprojectprice'][$j]-$query['cost'];
                $financial_plus_minus += $plus_minus;
            }
            $review_profit_loss .= '<tr><td>Labour</td><td>'.$query['labour_type'].' : '.$query['heading'].'</td> <td>$'.$query['cost'].'</td><td>$'.$_POST['lprojectprice'][$j].'</td><td '.$color_off.'>$'.$plus_minus.'</td></tr>';

            $financial_cost += $query['cost'];
            $financial_price += $_POST['lprojectprice'][$j];

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
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `itemid`, `service_type`,`desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Labour', '$labourid_all', '$st', '$temp_ticket_desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_labour != 0) {
        $review_budget .= '<tr><td>Labour</td><td>$'.$_POST['budget_price_13'].'</td> <td>$'.$total_labour.'</td></tr>';
    }

    //Expense
    $expense = '';
    $ex_html = '';
    $total_expense = 0;
    $j=0;
    foreach ($_POST['expensetype'] as $expensetype_all) {
        if($expensetype_all != '') {
            $expense .=     $expensetype_all.'#'.$_POST['expensecategory'][$j].'#'.$_POST['expprojectprice'][$j].'**';
            $total_price += $_POST['expprojectprice'][$j];
            $total_expense += $_POST['expprojectprice'][$j];

            $ex_html .= '<tr nobr="true"><td>'.$expensetype_all.'</td> <td>'.$_POST['expensecategory'][$j].'</td> <td>$'.$_POST['expprojectprice'][$j].'</td></tr>';

            $review_profit_loss .= '<tr><td>Expense</td><td>'.$expensetype_all.' : '.$_POST['expensecategory'][$j].'</td> <td>-</td><td>$'.$_POST['expprojectprice'][$j].'</td><td >-</td></tr>';

            $financial_cost += 0;
            $financial_price += $_POST['expprojectprice'][$j];
            $financial_plus_minus += 0;

            $desc = $expensetype_all.' : '.$_POST['expensecategory'][$j];
            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Expenses', '$desc')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);

        }
        $j++;
    }
    if($total_expense != 0) {
        $review_budget .= '<tr><td>Expense</td><td>$'.$_POST['budget_price_11'].'</td> <td>$'.$total_expense.'</td></tr>';
    }

    //Other
    $other = '';
    $other_html = '';
    $total_other = 0;
    $j=0;
    foreach ($_POST['other_detail'] as $other_detail_all) {
        if($other_detail_all != '') {
            $other .=     $other_detail_all.'#'.$_POST['otherprojectprice'][$j].'**';
            $total_price += $_POST['otherprojectprice'][$j];
            $total_other += $_POST['otherprojectprice'][$j];

            $other_html .= '<tr nobr="true"><td>'.$other_detail_all.'</td><td>$'.$_POST['otherprojectprice'][$j].'</td></tr>';

            $review_profit_loss .= '<tr><td>Other Items</td><td>'.$other_detail_all.'</td> <td>-</td><td>$'.$_POST['otherprojectprice'][$j].'</td><td >-</td></tr>';

            $financial_cost += 0;
            $financial_price += $_POST['otherprojectprice'][$j];
            $financial_plus_minus += 0;

            $query_insert_ticket = "INSERT INTO `temp_ticket` (`projectid`,  `businessid`, `clientid`, `category`, `desc`) VALUES ('$projectid', '$businessid', '$clientid', 'Other', '$other_detail_all')";
            $result_insert_ticket = mysqli_query($dbc, $query_insert_ticket);
        }
        $j++;
    }
    if($total_other != 0) {
        $review_budget .= '<tr><td>Other Items</td><td>$'.$_POST['budget_price_12'].'</td> <td>$'.$total_other.'</td></tr>';
    }

    $html = '';
    $html .= '<span class="pull-right">Date : '.date('Y-m-d').'<br>
              Invoice # '.$projectid.'</span>';

	$html .= '<p style="text-align:right;">'.get_client($dbc, $clientid);
    if(get_staff($dbc, $clientid) != '') {
        $html .= '<br>'.get_staff($dbc, $clientid);
    }

    if(get_contact($dbc, $contactid, 'business_street') != '') {
        $html .= '<br>'.get_contact($dbc, $contactid, 'business_street');
        $html .= '<br>'.get_contact($dbc, $contactid, 'business_city');
        $html .= ', '.get_contact($dbc, $contactid, 'business_state');
        $html .= '<br>'.get_contact($dbc, $contactid, 'business_country');
        $html .= ', '.get_contact($dbc, $contactid, 'business_zip');
    }

    if(get_contact($dbc, $contactid, 'office_phone') != '') {
        $html .= '<br>'.get_contact($dbc, $contactid, 'office_phone');
    }
    if(get_contact($dbc, $contactid, 'cell_phone') != '') {
        $html .= '<br>'.get_contact($dbc, $contactid, 'cell_phone');
    }
    if(get_contact($dbc, $contactid, 'email_address') != '') {
        $html .= '<br>'.get_contact($dbc, $contactid, 'email_address');
    }

    $html .= '</p>';

    $html .= '<table border="1px" style="padding:3px; border:1px solid black;">
            <tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">
            <th>Sales Person</th><th>Job</th><th>Payment Terms</th><th>Due Period</th></tr>';
    $html .= '<tr nobr="true">
            <td>'.decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).'</td><td>'.$project_name.'</td><td>'.get_config($dbc, 'quote_payment_term').'</td><td>'.get_config($dbc, 'quote_due_period').'</td></tr>';
    $html .= '</table><br>';

    $html .= '<table border="1px" style="padding:3px; border:1px solid black;">
            <tr nobr="true" style="background-color:lightgrey; color:black;  width:22%;">
            <th>Type</th><th>Description</th><th>Rates</th><th>Hours/Qty</th><th style=" width:15%;">Price</th></tr>';

    if($package_html != '') {
        $html .= $package_html;
    }
    if($promotion_html != '') {
        $html .= $promotion_html;
    }
    if($custom_html != '') {
        $html .= $custom_html;
    }
    if($m_html != '') {
        $html .= $m_html;
    }
    if($s_html != '') {
        $html .= $s_html;
    }
    if($p_html != '') {
        $html .= $p_html;
    }
    if($sred_html != '') {
        $html .= $sred_html;
    }
    if($l_html != '') {
        $html .= $l_html;
    }
    if($staff_html != '') {
        $html .= $staff_html;
    }
    if($cont_html != '') {
        $html .= $cont_html;
    }
    if($c_html != '') {
        $html .= $c_html;
    }
    if($v_html != '') {
        $html .= $v_html;
    }
    if($cust_html != '') {
        $html .= $cust_html;
    }
    if($in_html != '') {
        $html .= $in_html;
    }
    if($eq_html != '') {
        $html .= $eq_html;
    }
    if($ex_html != '') {
        $html .= $ex_html;
    }
    if($other_html != '') {
        $html .= $other_html;
    }
    $html .= '<tr><td colspan="4"><p style="text-align:right;">Sub Total</p></td><td>$'.$total_price.'</td></tr>';

    $value_config = get_config($dbc, 'quote_tax');

    $quote_tax = explode('*#*',$value_config);

    $total_count = mb_substr_count($value_config,'*#*');
    $tax_rate = 0;
    for($eq_loop=0; $eq_loop<=$total_count; $eq_loop++) {
        $quote_tax_name_rate = explode('**',$quote_tax[$eq_loop]);
        $tax_rate += $quote_tax_name_rate[1];
        $html .= '<tr><td colspan="4"><p   style="text-align:right;">'.$quote_tax_name_rate[0].'<br><em>['.$quote_tax_name_rate[2].']</em></p></td><td>'.$quote_tax_name_rate[1].'%</td></tr>';
    }

    $final = ($total_price*$tax_rate)/100;
    $final_total = ($total_price+$final);

    $html .= '<tr><td colspan="4"><p style="text-align:right;">Total</p></td><td>$'.$final_total.'</td></tr>';

    $html .= '</table>';

    $html_1 = addslashes($html);
    $review_profit_loss_1 = mysqli_real_escape_string($dbc, $review_profit_loss);

    $review_budget_1 = mysqli_real_escape_string($dbc, $review_budget);

    $history = '';
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(projectid) AS total_id FROM project WHERE `projectid` = '$projectid' AND status = 'Submitted'"));

    if($get_config['total_id'] == 1) {
        $get_project = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM project WHERE `projectid` = '$projectid'"));
        if($get_project['package'] != $package) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Package on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['promotion'] != $promotion) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Promotion on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['material'] != $material) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Material on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['services'] != $services) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Services on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['products'] != $products) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Products on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['sred'] != $sred) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed SR&ED on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['labour'] != $labour) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Labour on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['client'] != $client) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Client on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['customer'] != $customer) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Customer on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['inventory'] != $inventory) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Inventory on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['equipment'] != $equipment) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Equipment on '.date('Y-m-d H:i:s').'<br>';
        }

        if($get_project['staff'] != $staff) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Staff on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['contractor'] != $contractor) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Contractor on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['vendor'] != $vendor) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Vendor on '.date('Y-m-d H:i:s').'<br>';
        }
        if($get_project['custom'] != $custom) {
            $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Custom on '.date('Y-m-d H:i:s').'<br>';
        }
    }

    $created_date = $_POST['created_date'];
    $start_date = $_POST['start_date'];
    $estimated_completed_date = $_POST['estimated_completed_date'];
    $completion_date = $_POST['completion_date'];
    $project_path = filter_var($_POST['project_path'],FILTER_SANITIZE_STRING);
    if($project_path == 'ADD_NEW_TEMPLATE') {
        $new_project_path = filter_var($_POST['new_project_path'],FILTER_SANITIZE_STRING);

        $milestone_arr = [];
        $timeline_arr = [];
        $temp_checklist_arr = [];
        $temp_ticket_arr = [];
        $temp_workorder_arr = [];
        $checklist_arr = [];
        $ticket_arr = [];
        $workorder_arr = [];

        foreach($_POST as $field => $value) {
            if(strpos($field,'milestone_') !== false) {
                $key = explode('_',$field)[1];
                $milestone_arr[$key] = $value;
            }
            else if(strpos($field,'timeline_') !== false) {
                $key = explode('_',$field)[1];
                $timeline_arr[$key] = $value;
            }
            else if(strpos($field,'checklist_item_') !== false) {
                $key = explode('_',$field)[2];
                $temp_checklist_arr[$key] = implode('*#*',array_filter($value));
            }
            else if(strpos($field,'ticket_item_') !== false) {
                $key = explode('_',$field)[2];
                $tickets_i = [];
                $services_i = $_POST['ticket_service_'.$key];
                foreach($value as $i => $heading) {
                    $tickets_i[] = $heading.'FFMSPLIT'.$services_i[$i];
                }
                $temp_ticket_arr[$key] = implode('*#*',array_filter($tickets_i));
            }
            else if(strpos($field,'work_order_item_') !== false) {
                $key = explode('_',$field)[3];
                $temp_workorder_arr[$key] = implode('*#*',array_filter($value));
            }
        }
        foreach($milestone_arr as $key => $value) {
            if($value != '') {
                $checklist_arr[$key] = $temp_checklist_arr[$key];
                $ticket_arr[$key] = $temp_ticket_arr[$key];
                $workorder_arr[$key] = $temp_workorder_arr[$key];
            } else {
                unset($milestone_arr[$key]);
            }
        }

        $milestone = filter_var(implode('#*#',$milestone_arr),FILTER_SANITIZE_STRING);
        $timeline = filter_var(implode('#*#',$timeline_arr),FILTER_SANITIZE_STRING);
        $checklist = filter_var(implode('#*#',$checklist_arr),FILTER_SANITIZE_STRING);
        $ticket = filter_var(implode('#*#',$ticket_arr),FILTER_SANITIZE_STRING);
        $workorder = filter_var(implode('#*#',$workorder_arr),FILTER_SANITIZE_STRING);

        $query_insert_config = "INSERT INTO `project_path_milestone` (`project_path`, `milestone`, `timeline`, `checklist`, `ticket`, `workorder`)
            VALUES ('$new_project_path', '$milestone', '$timeline', '$checklist', '$ticket', '$workorder')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
        $project_path = mysqli_insert_id($dbc);
    }
    $milestone_timeline = filter_var($_POST['milestone_timeline'],FILTER_SANITIZE_STRING);
	$projecttype = $_POST['projecttype'];
    $effective_date = $_POST['effective_date'];
    $time_clock_start_date = $_POST['time_clock_start_date'];

    $query_update_report = "UPDATE `project` SET `created_date` = '$created_date', `start_date` = '$start_date', `estimated_completed_date` = '$estimated_completed_date', `completion_date` = '$completion_date', `project_name` = '$project_name', `projecttype`='$projecttype', `package` = '$package', `promotion` = '$promotion', `material` = '$material', `services` = '$services', `products` = '$products', `sred` = '$sred', `labour` = '$labour', `client` = '$client', `customer` = '$customer', `inventory` = '$inventory', `equipment` = '$equipment', `staff` = '$staff', `contractor` = '$contractor', `expense` = '$expense', `vendor` = '$vendor', `custom` = '$custom', `other_detail` = '$other', `total_price` = '$total_price', `project_data` = '$html_1',  `review_profit_loss` = '$review_profit_loss_1',  `review_budget` = '$review_budget_1', `budget_price` = '$budget_price', `financial_cost` = '$financial_cost', `financial_price` = '$financial_price', `financial_plus_minus` = '$financial_plus_minus', `project_path` = '$project_path', `milestone_timeline` = '$milestone_timeline', `effective_date` = '$effective_date', `time_clock_start_date` = '$time_clock_start_date' WHERE `projectid` = '$projectid'";
    $user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
	mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', '".htmlentities($history)."', '$projectid')");
    $result_update_report = mysqli_query($dbc, $query_update_report);

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

    $detail2_issue = filter_var(htmlentities($_POST['detail2_issue']),FILTER_SANITIZE_STRING);
    $detail2_plan = filter_var(htmlentities($_POST['detail2_plan']),FILTER_SANITIZE_STRING);
    $detail2_do = filter_var(htmlentities($_POST['detail2_do']),FILTER_SANITIZE_STRING);
    $detail2_check = filter_var(htmlentities($_POST['detail2_check']),FILTER_SANITIZE_STRING);
    $detail2_adjust = filter_var(htmlentities($_POST['detail2_adjust']),FILTER_SANITIZE_STRING);

    $detail_procedureid = filter_var($_POST['procedureid'],FILTER_SANITIZE_STRING);
    $detail_quote = filter_var($_POST['quote'],FILTER_SANITIZE_STRING);
    $detail_dwg = filter_var($_POST['dwg'],FILTER_SANITIZE_STRING);
    $detail_quantity = filter_var($_POST['quantity'],FILTER_SANITIZE_STRING);
    $detail_sn = filter_var($_POST['sn'],FILTER_SANITIZE_STRING);
    $detail_totalprojectbudget = filter_var($_POST['totalprojectbudget'],FILTER_SANITIZE_STRING);

    $detailid = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(detailid) AS total_detail FROM project_detail WHERE `projectid` = '$projectid'"));

    if($detailid['total_detail'] > 0) {
        $query_update_report = "UPDATE `project_detail` SET `detail_issue` = '$detail_issue', `detail_problem` = '$detail_problem', `detail_technical_uncertainty` = '$detail_technical_uncertainty', `detail_base_knowledge` = '$detail_base_knowledge', `detail_do` = '$detail_do', `detail_already_known` = '$detail_already_known', `detail_sources` = '$detail_sources', `detail_current_designs` = '$detail_current_designs', `detail_known_techniques` = '$detail_known_techniques', `detail_review_needed` = '$detail_review_needed', `detail_looking_to_achieve` = '$detail_looking_to_achieve', `detail_plan` = '$detail_plan', `detail_next_steps` = '$detail_next_steps', `detail_learnt` = '$detail_learnt', `detail_discovered` = '$detail_discovered', `detail_tech_advancements` = '$detail_tech_advancements', `detail_work` = '$detail_work', `detail_adjustments_needed` = '$detail_adjustments_needed', `detail_future_designs` = '$detail_future_designs', `detail_check` = '$detail_check', `detail_objective` = '$detail_objective', `detail_gap` = '$detail_gap', `detail_targets` = '$detail_targets', `detail_audience` = '$detail_audience', `detail_strategy` = '$detail_strategy', `detail_desired_outcome` = '$detail_desired_outcome', `detail_actual_outcome` = '$detail_actual_outcome', `detail2_issue` = '$detail2_issue', `detail2_plan` = '$detail2_plan', `detail2_do` = '$detail2_do', `detail2_check` = '$detail2_check', `detail2_adjust` = '$detail2_adjust', `detail_procedure_id` = '$detail_procedureid', `detail_quote` = '$detail_quote', `detail_dwg` = '$detail_dwg', `detail_quantity` = '$detail_quantity', `detail_sn` = '$detail_sn', `detail_total_project_budget` = '$detail_totalprojectbudget' WHERE `projectid` = '$projectid'";
        $result_update_report = mysqli_query($dbc, $query_update_report);
    } else {
        $query_insert_detail = "INSERT INTO `project_detail` (`projectid`, `detail_issue`, `detail_problem`, `detail_gap`, `detail_technical_uncertainty`, `detail_base_knowledge`, `detail_do`, `detail_already_known`, `detail_sources`, `detail_current_designs`, `detail_known_techniques`, `detail_review_needed`, `detail_looking_to_achieve`, `detail_plan`, `detail_next_steps`, `detail_learnt`,  `detail_discovered`,  `detail_tech_advancements`, `detail_work`, `detail_adjustments_needed`, `detail_future_designs`, `detail_check`, `detail_objective`, `detail_targets`, `detail_audience`, `detail_strategy`, `detail_desired_outcome`, `detail_actual_outcome`, `detail2_issue`, `detail2_plan`, `detail2_do`, `detail2_check`, `detail2_adjust`, `detail_procedure_id`, `detail_quote`, `detail_dwg`, `detail_quantity`, `detail_sn`, `detail_total_project_budget`) VALUES ('$projectid', '$detail_issue', '$detail_problem', '$detail_gap', '$detail_technical_uncertainty', '$detail_base_knowledge', '$detail_do', '$detail_already_known', '$detail_sources', '$detail_current_designs', '$detail_known_techniques', '$detail_review_needed', '$detail_looking_to_achieve', '$detail_plan', '$detail_next_steps', '$detail_learnt',  '$detail_discovered',  '$detail_tech_advancements', '$detail_work',  '$detail_adjustments_needed', '$detail_future_designs', '$detail_check', '$detail_objective', '$detail_targets', '$detail_audience', '$detail_strategy', '$detail_desired_outcome', '$detail_actual_outcome', '$detail2_issue', '$detail2_plan', '$detail2_do', '$detail2_check', '$detail2_adjust', '$detail_procedureid', '$detail_quote', '$detail_dwg', '$detail_quantity', '$detail_sn', '$detail_totalprojectbudget')";
        $result_insert_detail = mysqli_query($dbc, $query_insert_detail);
    }

    //Comment
    $type = '';
    $note_heading = filter_var($_POST['note_heading'],FILTER_SANITIZE_STRING);

    if($note_heading == 'General') {
        $type = 'note';
    }
    $project_comment = htmlentities($_POST['project_comment']);
    $t_comment = filter_var($project_comment,FILTER_SANITIZE_STRING);
    if($t_comment != '') {
        $email_comment = $_POST['email_comment'];

        if($type != '') {
            $query_insert_ca = "INSERT INTO `project_comment` (`projectid`, `comment`, `email_comment`, `created_date`, `created_by`, `type`, `note_heading`) VALUES ('$projectid', '$t_comment', '$email_comment', '$created_date', '$who_added', '$type', '$note_heading')";
            $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
        } else {
            $query_update_report = "UPDATE `project_detail` SET `$note_heading` = CONCAT($note_heading,'$t_comment') WHERE `projectid` = '$projectid'";
            $result_update_report = mysqli_query($dbc, $query_update_report);
        }

        if ($_POST['send_email_on_comment'] == 'Yes') {
            //Code for Send Email
            $email = get_email($dbc, $email_comment);
			$sender = (!empty($_POST['email_sender']) ? $_POST['email_sender'] : '');
			$subject = $_POST['email_subject'];
			$email_body = str_replace(['[NOTE]','[PROJECTID]'], [$_POST['project_comment'],$projectid], $_POST['email_body']);

			if($email != '') {
				try {
					send_email($sender, $email, '', '', $subject, $email_body, '');
				} catch(Exception $e) {
					echo "<script>alert('Unable to send email. Please try again later.');</script>";
				}
			}
        }
    }

    $document = htmlspecialchars($_FILES["document"]["name"], ENT_QUOTES);
    for($i = 0; $i < count($_FILES['document']['name']); $i++) {
        if($document[$i] != '') {
            move_uploaded_file($_FILES["document"]["tmp_name"][$i], "download/" . $_FILES["document"]["name"][$i]) ;
            $query_insert_upload = "INSERT INTO `project_document` (`projectid`, `upload`) VALUES ('$projectid', '$document[$i]')";
            $result_insert_upload = mysqli_query($dbc, $query_insert_upload);
        }
    }

    for($i = 0; $i < count($_POST['support_link']); $i++) {
        $support_link = $_POST['support_link'][$i];

        if($support_link != '') {
            $query_insert_client_doc = "INSERT INTO `project_document` (`projectid`, `link`) VALUES ('$projectid', '$support_link')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
        }
    }

    $status = get_project($dbc, $projectid, 'status');

    if($status == 'Pending') {
        $ptype = 'Pending';
    } else {
        $ptype = $_POST['ptype'];
    }

    if(!empty($_POST['from'])) {
        $url = urldecode($_POST['from']);
    } else {
        $url = 'project.php?type='.$ptype;
    }

	if ( !empty($intakeid) ) {
		$url = '../Intake/intake.php';
	}

    echo '<script type="text/javascript"> window.location.replace("'.$from_url.'"); </script>';
}
?>
<script type="text/javascript">
$(document).ready(function() {
    $("#form1").submit(function( event ) {
        if ($("#businessid").val() == '') {
            alert("Please make sure you have filled in the business.");
            return false;
        }

        if ($("#projecttype").val() == '') {
            alert("Please make sure you have filled in the project type.");
            return false;
        }

        if ($("input[name=project_name]").val() == '') {
            alert("Please make sure you have filled in project name.");
            return false;
        }
    });

    $("#businessid").change(function() {
        var ptype = $("#ptype").val();
		var intakeid = $("#intakeid").val();
		var contactid_intake = $("#contactid_intake").val();
		window.location = 'add_project.php?type='+ptype+'&bid='+this.value+'&intakeid='+intakeid+'&clientid='+contactid_intake;
	});

	$("#projectclientid").change(function() {
        var businessid = $("#businessid").val();
        var ptype = $("#ptype").val();
		var intakeid = $("#intakeid").val();
		var contactid_intake = $("#contactid_intake").val();
		var clientid = $(this).val();

		if ( contactid_intake != '' ) {
			clientid = contactid_intake;
		}

        window.location = 'add_project.php?type='+ptype+'&bid='+businessid+'&clientid='+clientid+"&intakeid="+intakeid;
	});

    $("#ratecardid").change(function() {
        var businessid = $("#businessid").val();
        var clientid = $("#projectclientid").val();
        var ptype = $("#ptype").val();
		var intakeid = $("#intakeid").val();
		var contactid_intake = $("#contactid_intake").val();

		if ( contactid_intake != '' ) {
			clientid = contactid_intake;
		}

        window.location = 'add_project.php?type='+ptype+'&bid='+businessid+'&clientid='+clientid+'&ratecardid='+this.value+"&intakeid="+intakeid;
	});

	$("#project_path").change(function() {
		var project_path = $("#project_path").val();
		$.ajax({
			type: "GET",
			url: "project_ajax_all.php?fill=project_path_milestone&project_path="+project_path,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('.template-target').html(response);
			}
		});
	}).trigger('change');

});
function deleteProject(sel, hide, blank) {
	var typeId = sel.id;
	var arr = typeId.split('_');

    var projectid = $("#projectid").val();

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
        $("#mprojectprice_"+arr[1]).val('0');
        $("#mprojectqty_"+arr[1]).val('0');
        $("#mprojecttotal_"+arr[1]).val('0');
        countMaterial('delete');
    }

    if(arr[0] == 'deleteservices') {
        $("#sprojectprice_"+arr[1]).val('0');
        $("#sprojectqty_"+arr[1]).val('0');
        $("#sprojecttotal_"+arr[1]).val('0');
        countService('delete');
    }
    if(arr[0] == 'deleteproducts') {
        $("#pprojectprice_"+arr[1]).val('0');
        $("#pprojectqty_"+arr[1]).val('0');
        $("#pprojecttotal_"+arr[1]).val('0');
        countService('delete');
    }
    if(arr[0] == 'deletesred') {
        $("#sredprojectprice_"+arr[1]).val('0');
        $("#sredprojectqty_"+arr[1]).val('0');
        $("#sredprojecttotal_"+arr[1]).val('0');
        countSrEd('delete');
    }
    if(arr[0] == 'deletestaff') {
        $("#stprojectprice_"+arr[1]).val('0');
        $("#stprojectqty_"+arr[1]).val('0');
        $("#stprojecttotal_"+arr[1]).val('0');
        countStaff('delete');
    }
    if(arr[0] == 'deletecontractor') {
        $("#cntprojectprice_"+arr[1]).val('0');
        $("#cntprojectqty_"+arr[1]).val('0');
        $("#cntprojecttotal_"+arr[1]).val('0');
        countContractor('delete');
    }

    if(arr[0] == 'deleteclients') {
        $("#clientest_"+arr[1]).val('0');
        countClient();
    }

    if(arr[0] == 'deletevendor') {
        $("#vprojectprice_"+arr[1]).val('0');
        $("#vprojectqty_"+arr[1]).val('0');
        $("#vprojecttotal_"+arr[1]).val('0');
        countVendor('delete');
    }
    if(arr[0] == 'deletecustomer') {
        $("#customerest_"+arr[1]).val('0');
        countCustomer();
    }
    if(arr[0] == 'deleteinventory') {
        $("#inprojectprice_"+arr[1]).val('0');
        $("#inprojectqty_"+arr[1]).val('0');
        $("#inprojecttotal_"+arr[1]).val('0');
        countInventory('delete');
    }
    if(arr[0] == 'deleteequipment') {
        $("#eqprojectprice_"+arr[1]).val('0');
        $("#eqprojectqty_"+arr[1]).val('0');
        $("#eqprojecttotal_"+arr[1]).val('0');
        countEquipment('delete');
    }
    if(arr[0] == 'deletelabour') {
        $("#lprojectprice_"+arr[1]).val('0');
        $("#lprojectqty_"+arr[1]).val('0');
        $("#lprojecttotal_"+arr[1]).val('0');
        countLabour('delete');
    }

    if(projectid == 0) {
        if(arr[0] == 'deletepackage') {
            alert('If you Delete any Package then all data Related to this Package will gone.');
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
            window.location = 'add_project.php?projectid='+projectid+'&pid='+package_id+'&promoid='+promotion_id+'&cid='+custom_id;
        }

        if(arr[0] == 'deletepromotion') {
            alert('If you Delete any Promotion then all data Related to this Promotion will gone.');
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
            window.location = 'add_project.php?projectid='+projectid+'&pid='+package_id+'&promoid='+promotion_id+'&cid='+custom_id;
        }

        if(arr[0] == 'deletecustom') {
            alert('If you Delete any Custom then all data Related to this Custom will gone.');
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

            window.location = 'add_project.php?projectid='+projectid+'&pid='+package_id+'&promoid='+promotion_id+'&cid='+custom_id;
        }
    }

    $("#"+hide+arr[1]).hide();
    $("#"+blank+arr[1]).val('');


    return false;
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised();
?>
<div class="container">
  <div class="row">
    <?php
    $type = $_GET['type'];
    if($type == 'SRED') {
        $type = 'SR&ED';
    }
    if($type == 'RD') {
        $type = 'R&D';
    }
    ?>
    <h1><?php echo (empty($_GET['projectid']) ? 'Add ' : 'Edit ').PROJECT_TILE; ?></h1>
    <?php if ( !empty ($intakeid) ) { ?>
		<div class="pad-left gap-top double-gap-bottom"><a href="../Intake/intake.php" class="btn brand-btn">Back to Dashboard</a></div>
	<?php } else { ?>
		<?php if(!empty($_GET['detail'])) { ?>
			<div class="pad-left gap-top double-gap-bottom"><a href="<?= $_GET['from']; ?>" class="btn brand-btn">Back to Dashboard</a></div>
		<?php } else if(!empty($_GET['from_url'])) { ?>
			<a href="<?php echo urldecode($_GET['from_url']); ?>" class="btn brand-btn">Back to Dashboard</a>
		<?php } else { ?>
			<a href="project.php?type=<?= $_GET['type']; ?>&from=<?= $_GET['from']; ?>" class="btn brand-btn">Back to Dashboard</a>
		<?php } ?>
	<?php } ?>

	<?php if ( !empty ($intakeid) ) { ?>
		<div class="notice">
			<img src="<?= WEBSITE_URL; ?>/img/info.png" width="30">&nbsp;&nbsp;Add a new Project to attach the Intake Form to.
		</div><?php
	} ?>

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
		<input type="hidden" name="intakeid" id="intakeid" value="<?php echo $intakeid; ?>" />
		<input type="hidden" name="contactid_intake" id="contactid_intake" value="<?php echo $contactid_intake; ?>" />

    <!--
    <button	type="submit" name="submit"	value="Reject" class="btn brand-btn	pull-right">Reject</button>    &nbsp;&nbsp;&nbsp;
    <button	type="submit" name="submit"	value="Approve" class="btn brand-btn	pull-right">Approve</button>
    <br><br>
    -->
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
        $ratecardid = '';
        if(!empty($_GET['ratecardid'])) {
            $ratecardid = $_GET['ratecardid'];
        }

        $back_url = 'project.php';
        if(!empty($_GET['from'])) {
            echo '<input type="hidden" name="from" value="'.$_GET['from'].'">';
            $back_url = urldecode($_GET['from']);
        }

        $project_name = '';
        $budget_price = '';
        $disable_business = '';
        $disable_client = '';
        $disable_rc = '';
        $disable_type = '';
        $projecttype = $_GET['type'];
        $created_date = date('Y-m-d');
        $start_date = date('Y-m-d');
        $estimated_completed_date = '';
        $completion_date = '';
        $afe_number = '';

        if(!empty($_GET['projectid'])) {
            $projectid = $_GET['projectid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM project WHERE projectid='$projectid'"));
            $clientid = $get_contact['clientid'];
            $businessid = $get_contact['businessid'];
            if($businessid ==  '' || $businessid ==  0) {
                $businessid = get_contact($dbc, $clientid, 'businessid');
            }
            $ratecardid = $get_contact['ratecardid'];
            $project_name = $get_contact['project_name'];
            $budget_price = explode('*#*', $get_contact['budget_price']);
            $disable_business = 'disabled';
            $disable_client = 'disabled';
            $disable_rc = 'disabled';
            $disable_type = 'disabled';

            $projecttype = $get_contact['projecttype'];
            $created_date = $get_contact['created_date'];
            $start_date = $get_contact['start_date'];
            $estimated_completed_date = $get_contact['estimated_completed_date'];
            $completion_date = $get_contact['completion_date'];
            $project_path = $get_contact['project_path'];
            $milestone_timeline = $get_contact['milestone_timeline'];

            $afe_number = $get_contact['afe_number'];

            $get_rc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM rate_card WHERE ratecardid='$ratecardid'"));
        ?>
        <input type="hidden" id="projectid" name="projectid" value="<?php echo $projectid ?>" />
        <?php   } else { ?>
                <input type="hidden" id="projectid" name="projectid" value="0" />
        <?php }
		$config_sql = "SELECT `config_fields` FROM field_config_project WHERE type='$projecttype' UNION
			SELECT `config_fields`  FROM `field_config_project` WHERE `fieldconfigprojectid` IN (SELECT MAX(`fieldconfigprojectid`) FROM `field_config_project` WHERE `type` IN ('".preg_replace('/[^a-z_,\']/','',str_replace(' ','_',str_replace(',',"','",strtolower(get_config($dbc,'project_tabs')))))."'))";
		$base_field_config_all = mysqli_fetch_assoc(mysqli_query($dbc,$config_sql));
        $base_field_config = ','.$base_field_config_all['config_fields'].',';
		$config_sql = "SELECT `config_fields` FROM field_config_project WHERE type='$projecttype' UNION
			SELECT `config_fields`  FROM `field_config_project` WHERE `fieldconfigprojectid` IN (SELECT MAX(`fieldconfigprojectid`) FROM `field_config_project` WHERE `type` IN ('".preg_replace('/[^a-z_,\']/','',str_replace(' ','_',str_replace(',',"','",strtolower(get_config($dbc,'project_tabs')))))."'))";
		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,$config_sql));
        $value_config = ','.$get_field_config['config_fields'].',';

        // TEMPORARY DISPLAY FIELDS
        $base_field_config .= ',Information Business,Information Contact,Information Project Type,Information Project Short Name,Path Path,Path Milestone Timeline,Dates Project Created Date,Dates Project Start Date,Dates Estimate Completion Date,Documents Documents,Documents Links,';
        $value_config .= ',Information Business,Information Contact,Information Project Type,Information Rate Card,Information Project Short Name,Path Path,Path Milestone Timeline,Dates Project Created Date,Dates Project Start Date,Dates Estimate Completion Date,Documents Documents,Documents Links,';
    ?>
    <input type="hidden" name="hidden_clientid" id="hidden_clientid" value="<?php echo $clientid; ?>">
    <input type="hidden" name="hidden_ratecardid" id="hidden_ratecardid" value="<?php echo $ratecardid; ?>">
    <input type="hidden" id="ptype" name="ptype" value="<?php echo $projecttype ?>" />

    <div class="panel-group" id="accordion2">

        <?php
        $note_add_view = '';
        $detail_add_view = '';
        $info_view = '';
        if(!empty($_GET['note'])) {
            $note_add_view = 'in';
        } else if(!empty($_GET['detail'])) {
            $detail_add_view = 'in';
        } else {
            $info_view = 'in';
        }
        ?>
        <?php if (strpos($value_config, ','."Information ") !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_abi" ><?php echo (PROJECT_TILE == 'Projects' ? 'Project' : (PROJECT_TILE == 'Jobs' ? 'Job' : PROJECT_TILE)); ?> Information<span class="glyphicon glyphicon-minus"></span></a>
                </h4>
            </div>

            <div id="collapse_abi" class="panel-collapse collapse <?php echo $info_view; ?>">
                <div class="panel-body">
                    <?php
                    include ('add_project_basic_info.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Path ") !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pm" >Project Path<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_pm" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_path_milestone.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Dates ") !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_date" >Dates<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_date" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_dates.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_detail" >Details<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_detail" class="panel-collapse collapse <?php echo $detail_add_view; ?>">
                <div class="panel-body">
                    <?php
                    include ('add_project_detail.php');
                    ?>
                </div>
            </div>
        </div>
        <?php if(!empty($_GET['projectid'])) {
        ?>
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
                 <?php include ('add_view_project_comment.php'); ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Documents ") !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_doc" >Documents<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_doc" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    if ( !empty ($intakeid) ) { ?>
						<div class="notice">
							<img src="<?php echo WEBSITE_URL; ?>/img/info.png" width="30">&nbsp;&nbsp;Intake Form will be attached to this Project when you click on Save &amp; Continue button.
						</div><?php
					}
					include ('add_project_documents.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_basic" >Budget Info<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_basic" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    include ('add_project_budget.php');
                    ?>

                </div>
            </div>
        </div>

        <?php if(!empty($_GET['projectid'])) {
        ?>
        <?php if (strpos($value_config, ','."Package".',') !== FALSE) {
        ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pp" >Package<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_pp" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_package.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Promotion".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_promo" >Promotion<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_promo" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_promotion.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Custom".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_cus" >Custom<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_cus" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_custom.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Material".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_material" >Material<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_material" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_material.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Services".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_service" >Services<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_service" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_services.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Products".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_Products" >Products<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_Products" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_products.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."SRED".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_sred" >SR&ED<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_sred" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_sred.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff" >Staff<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_staff" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_staff.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Contractor".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_contractor" >Contractor<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_contractor" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_contractor.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Clients".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_clients" >Clients<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_clients" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_clients.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Vendor Pricelist".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_vendor" >Vendor Pricelist<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_vendor" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_vendor.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Customer".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_customer" >Customer<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_customer" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_customer.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Inventory".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_inv" >Inventory<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_inv" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_inventory.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_equipment" >Equipment<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_equipment" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_equipment.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Labour".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_labour" >Labour<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_labour" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_labour.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Expenses".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_expenses" >Expenses<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_expenses" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_expenses.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Other".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_other" >Other<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_other" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_other.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_summary" >Summary<span class="glyphicon glyphicon-plus"></span></a>
                </h4>
            </div>

            <div id="collapse_summary" class="panel-collapse collapse">
                <div class="panel-body">
                    <?php
                    include ('add_project_summary.php');
                    ?>
                </div>
            </div>
        </div>
        <?php } ?>

    </div>

    <?php if(empty($_GET['projectid'])) { ?>
        <div class="form-group">
            <div class="col-sm-6 clearfix">
                <span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your Project."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <?php if ( !empty ($intakeid) ) { ?>
					<a href="../Intake/intake.php" class="btn brand-btn btn-lg">Back</a>
				<?php } else if(!empty($_GET['from_url'])) { ?>
					<a href="<?php echo urldecode($_GET['from_url']); ?>" class="btn brand-btn btn-lg">Back</a>
				<?php } else { ?>
					<a href="project.php?type=<?= $_GET['type']; ?>&from=<?= $_GET['from']; ?>" class="btn brand-btn btn-lg">Back</a>
				<?php } ?>
            </div>
            <div class="col-sm-6">
                <button	type="submit" name="save" value="save" class="btn brand-btn btn-lg pull-right">Save &amp; Continue</button>
				<span class="popover-examples list-inline pull-right" style="margin:12px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click this to finalize your Project."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div>
        </div>
    <?php } else { ?>
        <div class="form-group">
            <div class="col-sm-6 clearfix">
                <span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Clicking this will discard your Project."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>

				<?php if ( !empty ($intakeid) ) { ?>
					<a href="../Intake/intake.php" class="btn brand-btn btn-lg">Back</a>
				<?php } else { ?>
					<?php if(!empty($_GET['from'])) { ?>
						<a href="<?= $_GET['from']; ?>" class="btn brand-btn btn-lg">Back</a>
					<?php } else if(!empty($_GET['from_url'])) { ?>
						<a href="<?php echo urldecode($_GET['from_url']); ?>" class="btn brand-btn btn-lg">Back</a>
					<?php } else { ?>
						<a href="project.php?type=<?= $_GET['type']; ?>&from=<?= $_GET['from']; ?>" class="btn brand-btn btn-lg">Back</a>
					<?php } ?>
				<?php } ?>

				<!--<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
            </div>
            <div class="col-sm-6">
                <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
				<span class="popover-examples list-inline pull-right" style="margin:12px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click this to finalize your Project."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div>
        </div>
    <?php } ?>

    </form>

  </div>
</div>
<?php include_once('../footer.php'); ?>
