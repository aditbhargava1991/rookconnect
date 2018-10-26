<?php
/*
Delete and Restore FFM
*/
include ('include.php');
include ('database_connection_htg.php');

error_reporting(0);
?>

</head>
<body>
<?php include_once ('navigation.php'); ?>
<h3>Archiving record...</h3>

<?php
$date_of_archival = date('Y-m-d');
    if($_GET['action'] == 'delete') {
        $deleted = 1;
        $date_of_archival = date('Y-m-d');
    }
    if($_GET['action'] == 'restore') {
        $deleted = 0;
    }
	if($_GET['action'] == 'delete_2') {
        $deleted = 2;
    }
    $date_of_archival = date('Y-m-d');
	//Contract Archived
	if(!empty($_GET['contractid'])) {
		$id = $_GET['contractid'];
		$date = date('Y-m-d h:i:s');
		$category = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category` FROM `contracts` WHERE `contractid`='$id'"))['category'];
		$sql = "UPDATE `contracts` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `contractid`='$id'";
		$result = mysqli_query($dbc, $sql);
		echo '<script type="text/javascript"> window.location.replace("Contracts/contracts.php?tab='.$category.'"); </script>';
	}

	//Rate Table Archived
	if(!empty($_GET['category_rate_id'])) {
		$id = $_GET['category_rate_id'];
		$date = date('Y-m-d h:i:s');
		$sql = "UPDATE `category_rate_table` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival', HISTORY=CONCAT(IFNULL(CONCAT(`history`,'<br />'),''),'','Rate card deleted by ".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])." on ','$date') WHERE `rate_id`='$id'";
		$result = mysqli_query($dbc, $sql);
		echo '<script type="text/javascript"> window.location.replace("Rate Card/rate_card.php?card=category"); </script>';
	}
	else if(!empty($_GET['staff_rate_id'])) {
		$id = $_GET['staff_rate_id'];
		$date = date('Y-m-d h:i:s');
		$sql = "UPDATE `staff_rate_table` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival', `history`=CONCAT(IFNULL(CONCAT(`history`,'<br />'),''),'','Rate card deleted by ".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])." on ','$date') WHERE `rate_id`='$id'";
		$result = mysqli_query($dbc, $sql);
		echo '<script type="text/javascript"> window.location.replace("Rate Card/rate_card.php?card=staff"); </script>';
	}
	else if(!empty($_GET['equipment_rate_id'])) {
		$id = $_GET['equipment_rate_id'];
		$date = date('Y-m-d h:i:s');
		$sql = "UPDATE `equipment_rate_table` SET `deleted`=1, `date_of_archival` = '$date_of_archival', HISTORY=CONCAT(IFNULL(CONCAT(`history`,'<br />'),''),'','Rate card deleted by ".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])." on ','$date') WHERE `rate_id`='$id'";
		$result = mysqli_query($dbc, $sql);
		echo '<script type="text/javascript"> window.location.replace("Rate Card/rate_card.php?card=equipment"); </script>';
	}
	else if(!empty($_GET['position_rate_id'])) {
		$id = $_GET['position_rate_id'];
		$date = date('Y-m-d h:i:s');
		$sql = "UPDATE `position_rate_table` SET `deleted`=1, `date_of_archival` = '$date_of_archival', `history`=CONCAT(IFNULL(CONCAT(`history`,'<br />'),''),'','Rate card deleted by ".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])." on ','$date') WHERE `rate_id`='$id'";
		$result = mysqli_query($dbc, $sql);
		echo '<script type="text/javascript"> window.location.replace("Rate Card/rate_card.php?card=position"); </script>';
	}
	if(!empty($_GET['projectmanageid'])) {
		$id = $_GET['projectmanageid'];
		$current = mysqli_fetch_array(mysqli_query($dbc,"SELECT `status`, `tile`, `tab` FROM `project_manage` WHERE `projectmanageid`='$id'"));
		$new_status = 'Deleted';
		if($deleted == 0 && $current['status'] == 'Deleted') {
			$new_status = 'Approved';
		}
		else if($deleted == 0 && $current['status'] == 'Rejected') {
			$new_status = '';
		}
		else if($deleted == 1 && $current['status'] == 'Approved') {
			$new_status = 'Deleted';
		}
		else if($deleted == 1 && $current['status'] == '') {
			$new_status = 'Rejected';
		}
		else if($deleted == 2) {
			$new_status = 'Hidden';
		}
		$sql = "UPDATE `project_manage` SET `status`='$new_status' WHERE `projectmanageid`='$id'";
		$result = mysqli_query($dbc, $sql);
		if ( isset($_GET['category']) && ($_GET['category']=='shop_work_order') ) {
            header('Location: Archived/archived_data.php?archive_type=shop_work_order');
            die();
        } elseif($current['tile'] == 'Shop Work Orders' && ($current['status'] == 'Approved' || $current['status'] == 'Deleted')) {
			header('Location: Project Workflow/project_workflow_dashboard.php?tile=Shop Work Orders&tab=Shop Work Order');
		}
		else if($current['tile'] == 'Shop Work Orders' && ($current['status'] == '' || $current['status'] == 'Rejected')) {
			header('Location: Project Workflow/project_workflow_dashboard.php?tile=Shop Work Orders&tab=Pending Work Order');
		}
		else {
			header('Location: Project Workflow/project_workflow_dashboard.php');
		}
	}
	if(!empty($_GET['site_poid'])) {
		$poid = $_GET['site_poid'];
		if(!empty($_GET['vinc'])) {
			$invoices = explode('##FFM##', mysqli_fetch_array(mysqli_query($dbc, "SELECT `invoice` FROM `site_work_po` WHERE `poid`='$poid'"))['invoice']);
			unset($invoices[$_GET['vinc']]);
			$invoices = implode('##FFM##', $invoices);
			mysqli_query($dbc, "UPDATE `site_work_po` SET `invoice`='$invoices' WHERE `poid`='$poid'");
		} else {
			mysqli_query($dbc, "UPDATE `site_work_po` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `poid`='$poid'");
		}
		header('Location: Site Work Orders/site_work_orders.php?tab=po');
	}
    if(!empty($_GET['fpoid']) && !empty($_GET['vinc'])) {
        $fpoid = $_GET['fpoid'];
        echo $vinc = $_GET['vinc'];
        echo '<br>';
        $query_check_credentials = "SELECT vendor_invoice FROM field_po WHERE fieldpoid = '$fpoid'";
        $result = mysqli_query($dbc, $query_check_credentials);
        while($row = mysqli_fetch_array( $result )) {
            $vendor_invoice = $row['vendor_invoice'];
            $vin = explode('##FFM##', $vendor_invoice);
            $vinc1 = 0;
            foreach($vin as $venin) {
                if($vinc == $vinc1) {
                    echo $vinc1;
                    $final_vendor_invoice = str_replace("##FFM##".$venin,"",$vendor_invoice);
                }
                $vinc1++;
            }
        }
        echo '<br>';
        echo $final_vendor_invoice;

        $query_update = "UPDATE `field_po` SET vendor_invoice='$final_vendor_invoice', status='Pending'  WHERE fieldpoid='$fpoid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Field Jobs/field_po.php');
        }
    }
	if(!empty($_GET['siteid'])) {
		$siteid = $_GET['siteid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `field_sites` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `siteid`='$siteid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Field Jobs/field_sites.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['agendameetingid'])) {
		$agendameetingid = $_GET['agendameetingid'];
        $query_update = "UPDATE `agenda_meeting` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `agendameetingid`='$agendameetingid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Agenda Meetings/'.$_GET['page'].'.php');
		}
	}
	if(!empty($_GET['fieldpoid'])) {
		$id = $_GET['fieldpoid'];
		$query_update = "UPDATE `field_po` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `fieldpoid`='$id'";
		mysqli_query($dbc, $query_update);
		header('Location: Field Jobs/field_po.php');
	}

	if(!empty($_GET['propertyid'])) {
		$id = $_GET['propertyid'];
		$query_update = "UPDATE `properties` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `propertyid`='$id'";
		mysqli_query($dbc, $query_update);
		header('Location: Properties/properties.php');
	}

	if(!empty($_GET['jobid'])) {
		$jobid = $_GET['jobid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `field_po` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `fieldpoid`='$fieldpoid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Field Jobs/field_jobs.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['seizure_record_id'])) {
		$seizure_record_id = $_GET['seizure_record_id'];
        $category = $_GET['category'];
        $query_update = "UPDATE seizure_record SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE seizure_record_id='$seizure_record_id'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Medical Charts/seizure_record.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['blood_glucose'])) {
		$blood_glucose_id = $_GET['blood_glucose_id'];
        $category = $_GET['category'];
        $query_update = "UPDATE blood_glucose SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE blood_glucose_id='$blood_glucose_id'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Medical Charts/blood_glucose.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['bowel_movement_id'])) {
		$bowel_movement_id = $_GET['bowel_movement_id'];
        $category = $_GET['category'];
        $query_update = "UPDATE bowel_movement SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE bowel_movement_id='$bowel_movement_id'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Medical Charts/bowel_movement.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['daily_water_temp_id'])) {
		$daily_water_temp_id = $_GET['daily_water_temp_id'];
        $category = $_GET['category'];
        $query_update = "UPDATE daily_water_temp SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE daily_water_temp_id='$daily_water_temp_id'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Medical Charts/daily_water_temp.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['daily_water_temp_bus_id'])) {
		$daily_water_temp_bus_id = $_GET['daily_water_temp_bus_id'];
        $category = $_GET['category'];
        $query_update = "UPDATE daily_water_temp_bus SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE daily_water_temp_bus_id='$daily_water_temp_bus_id'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Medical Charts/daily_water_temp_bus.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['daily_fridge_temp_id'])) {
		$daily_fridge_temp_id = $_GET['daily_fridge_temp_id'];
        $category = $_GET['category'];
        $query_update = "UPDATE daily_fridge_temp SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE daily_fridge_temp_id='$daily_fridge_temp_id'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Medical Charts/daily_fridge_temp.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['daily_freezer_temp_id'])) {
		$daily_freezer_temp_id = $_GET['daily_freezer_temp_id'];
        $category = $_GET['category'];
        $query_update = "UPDATE daily_freezer_temp SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE daily_freezer_temp_id='$daily_freezer_temp_id'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Medical Charts/daily_freezer_temp.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['daily_dishwasher_temp_id'])) {
		$daily_dishwasher_temp_id = $_GET['daily_dishwasher_temp_id'];
        $category = $_GET['category'];
        $query_update = "UPDATE daily_dishwasher_temp SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE daily_dishwasher_temp_id='$daily_dishwasher_temp_id'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Medical Charts/daily_dishwasher_temp.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['patientformid'])) {
		$patientformid = $_GET['patientformid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `patientform` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `patientformid`='$patientformid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Treatment/index.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['fsid'])) {
		$fsid = $_GET['fsid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `field_foreman_sheet` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `fsid`='$fsid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Field Jobs/field_foreman_sheet.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['reminderid'])) {
		$reminderid = $_GET['reminderid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `reminders` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `reminderid`='$reminderid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Staff/staff.php?tab=reminders&category='.$category.'&filter=Top');
		}
	}




	if(!empty($_GET['infogatheringid'])) {
		$infogatheringid = $_GET['infogatheringid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `infogathering` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `infogatheringid`='$infogatheringid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Information Gathering/infogathering.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['workticketid'])) {
		$workticketid = $_GET['workticketid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `field_work_ticket` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `workticketid`='$workticketid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Field Jobs/field_work_ticket.php?category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['goalid'])) {
		$goalid = $_GET['goalid'];
        $query_update = "UPDATE `goal` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `goalid`='$goalid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Compensation/goals.php');
		}
	}
	if(!empty($_GET['compensationid'])) {
		$compensationid = $_GET['compensationid'];
        $query_update = "UPDATE `compensation` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `compensationid`='$compensationid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Compensation/compensation.php');
		}
	}
	if(!empty($_GET['hourlypayid'])) {
		$hourlypayid = $_GET['hourlypayid'];
        $query_update = "UPDATE `hourly_pay` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `hourlypayid`='$hourlypayid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Compensation/goals.php');
		}
	}

	if(!empty($_GET['invoiceid'])) {
		$invoiceid = $_GET['invoiceid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `field_invoice` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `invoiceid`='$invoiceid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			$subtab = $_GET['subtab'];
			if($subtab == 'Unpaid')
				header('Location: Field Jobs/field_invoice.php?paytype=Unpaid&category='.$category.'&filter=Top');
			if($subtab == 'Paid')
				header('Location: Field Jobs/field_invoice.php?paytype=Paid&category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['payrollid'])) {
		$payrollid = $_GET['payrollid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `field_payroll` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `payrollid`='$payrollid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Field Jobs/field_payroll.php?paytype=Unpaid&category='.$category.'&filter=Top');
		}
	}

	if(!empty($_GET['fieldsiteid'])) {
		$fieldsiteid = $_GET['fieldsiteid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `field_sites` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `siteid`='$fieldsiteid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
			header('Location: Field Jobs/field_sites.php?category='.$category.'&filter=Top');
        }
	}

	if(!empty($_GET['contact_fieldsiteid'])) {
		$id = $_GET['contact_fieldsiteid'];
		$query_update = "UPDATE `field_sites` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `siteid`='$id'";
		mysqli_query($dbc, $query_update);
		header('Location: Contacts/contacts.php?category=Sites');
	}

	if(!empty($_GET['fieldjobid'])) {
		$id = $_GET['fieldjobid'];
		$query_update = "UPDATE `field_jobs` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `jobid`='$id'";
		mysqli_query($dbc, $query_update);
		header('Location: Field Jobs/field_jobs.php');
	}
	if(!empty($_GET['fieldforemanid'])) {
		$id = $_GET['fieldforemanid'];
		$query_update = "UPDATE `field_foreman_sheet` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `fsid`='$id'";
		mysqli_query($dbc, $query_update);
		header('Location: Field Jobs/field_foreman_sheet.php');
	}
	if(!empty($_GET['fieldwtid'])) {
		$id = $_GET['fieldwtid'];
		$query_update = "UPDATE `field_work_ticket` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `workticketid`='$id'";
		mysqli_query($dbc, $query_update);
		header('Location: Field Jobs/field_work_ticket.php');
	}
	if(!empty($_GET['fieldinvoiceid'])) {
		$id = $_GET['fieldinvoiceid'];
		$query_update = "UPDATE `field_invoice` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `invoiceid`='$id'";
		mysqli_query($dbc, $query_update);
		header('Location: Field Jobs/field_invoice.php?paytype=Unpaid');
	}
	if(!empty($_GET['field_job'])) {
		$id = $_GET['field_job'];
		$category = $_GET['category'];
        $archived_tile = false;
		switch($_GET['job_tab']) {
			case 'Sites': $table = 'field_sites'; $id_field = 'siteid'; break;
			case 'Jobs': $table = 'field_jobs'; $id_field = 'jobid'; break;
			case 'Foreman Sheet': $table = 'field_foreman_sheet'; $id_field = 'fsid'; break;
			case 'PO': $table = 'field_po'; $id_field = 'fieldpoid'; break;
			case 'Work Ticket': $table = 'field_work_ticket'; $id_field = 'workticketid'; break;
            case 'Attached to Work Ticket': $table = 'field_po'; $id_field = 'fieldpoid'; $archived_tile = true; break;
			case 'Invoice': $table = 'field_invoice'; $id_field = 'invoiceid'; break;
			default: exit("<script>alert('Something went wrong.');window.location.replace('/Field Jobs/field_sites.php');"); break;
		}

		$sql_update = "UPDATE `$table` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `$id_field`='$id'";
		$result = mysqli_query($dbc, $sql_update);
		if ( $archived_tile ) {
            header('Location: Archived/archived_data.php?archive_type='.$category);
            die();
        } else {
            header('Location: Archived/archived_data.php?archive_type=field_jobs&category='.$category);
        }
	}

    if(!empty($_GET['contactid'])) {
		if(!empty($_GET['from_url'])) {
			$url_return = $_GET['from_url'].'&filter=Top';
		} else if ($_GET['category'] == 'Members') {
            $url_return = 'Members/contacts.php?category='.$_GET['category'];
        } else if ($_GET['category'] == 'Clients') {
            $url_return = 'ClientInfo/contacts.php?category='.$_GET['category'];
        } else {
			$url_return = "Contacts/contacts.php?category='.$category.'&filter=Top";
		}
        $contactid = $_GET['contactid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `contacts` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE contactid='$contactid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: '.$url_return);
        }
        if($_GET['action'] == 'restore' && !empty($_GET['from'])) {
            header('Location: '.$_GET['from']);
        }
    }

	if(!empty($_GET['serviceratecardid'])) {
		$serviceratecardid = $_GET['serviceratecardid'];
        $query_update = "UPDATE `service_rate_card` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `serviceratecardid`='$serviceratecardid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['action'] == 'delete') {
			header('Location: Rate Card/rate_card.php?card=services');
		}
	}

	if(!empty($_GET['supportid'])) {
        $supportid = $_GET['supportid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `support` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE supportid='$supportid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Helpdesk/helpdesk.php?category='.$category.'&filter=Top');
        }
    }

    if(!empty($_GET['checklistnameid'])) {
        $checklistnameid = $_GET['checklistnameid'];
        $checklistid = $_GET['checklistid'];

        echo delete_content($dbc, 'checklist_name', 'checklistnameid', $checklistnameid, 'Checklist/checklist.php?view='.$checklistid);
    }

	if(!empty($_GET['sales_lead_number'])) {
        $sales_lead_number = $_GET['sales_lead_number'];
        $category = $_GET['category'];
        $query_update = "UPDATE `sales_lead` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE sales_lead_number='$sales_lead_number'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
           //DELETE HAS NOT BEEN BUILT FOR SALES YET // header('Location: Contacts/contacts.php?category='.$category.'&filter=Top');
        }
    }

    if(!empty($_GET['timerid'])) {
        $drivinglogid = $_GET['drivinglogid'];
        echo delete_content($dbc, 'driving_log_timer', 'timerid', $_GET['timerid'], 'Driving Log/amendments.php?graph=off&drivinglogid='.$drivinglogid);
    }

    if(!empty($_GET['ticketdocid'])) {
        $ticketdocid = $_GET['ticketdocid'];
        $ticketid = get_ticket_document($dbc, $ticketdocid, 'ticketid');
        echo delete_content($dbc, 'ticket_document', 'ticketdocid', $ticketdocid, 'Ticket/index.php?edit='.$ticketid);
    }

    if(!empty($_GET['remove_project_checklist'])) {
        $checklistid = $_GET['checklistid'];
        $projectid = $_GET['projectid'];
        echo delete_content($dbc, 'checklist', 'checklistid', $_GET['checklistid'], 'Project/review_project.php?type=checklist&projectid='.$projectid.'&category=ongoing');
    }

    if(!empty($_GET['remove_client_project_checklist'])) {
        $checklistid = $_GET['checklistid'];
        $projectid = $_GET['projectid'];
        echo delete_content($dbc, 'checklist', 'checklistid', $_GET['checklistid'], 'Client Project/review_project.php?type=checklist&projectid='.$projectid.'&category=ongoing');
    }

    if(!empty($_GET['materialid'])) {
		$materialid = $_GET['materialid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `material` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE materialid='$materialid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Material/material.php?category='.$category.'&filter=Top');
        }
    }

	if(!empty($_GET['safetyid'])) {
		$safetyid = $_GET['safetyid'];
        $category = $_GET['category'];
		$tab = $_GET['tab'];
        $query_update = "UPDATE `safety` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE safetyid='$safetyid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Safety/safety.php?tab='.$tab.'&category='.$category.'&filter=Top');
        }
    }

    if(!empty($_GET['inventoryid'])) {
		if($_GET['action'] == 'delete') {
            if(get_config($dbc, 'inventory_default_select_all') == 1) {
    			echo archive_content($dbc, 'inventory', 'inventoryid', $_GET['inventoryid'], 'Inventory/inventory.php?category=dispall_31V2irt2u3e5S3s1f2ADe3_31');
            } else {
			    echo archive_content($dbc, 'inventory', 'inventoryid', $_GET['inventoryid'], 'Inventory/inventory.php?category=Top');
            }
			//echo archive_content($dbc, 'inventory', 'inventoryid', $_GET['inventoryid'], 'Inventory/inventory.php?category=Top');
		} else {
			$contactid = $_GET['inventoryid'];
			$category = $_GET['category'];
			$query_update = "UPDATE `inventory` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE inventoryid='$contactid'";
			$result_update = mysqli_query($dbc, $query_update);
		}
    }

	if(!empty($_GET['vplid'])) {
		$vplid = $_GET['vplid'];
        $category = $_GET['category'];
        $contactid = $_GET['contactid'];
        $query_update = "UPDATE `vendor_price_list` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE inventoryid='$vplid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            if ( !empty($_GET['back_url']) ) {
            	header('Location: '.urldecode($_GET['back_url']));
            } else if ( !empty($contactid) ) {
                header('Location: Vendors/contacts_inbox.php?category='.$category.'&edit='.$contactid);
            } else {
                header('Location: Vendor Price List/inventory.php?type=active&category='.$category.'&filter=Top');
            }
        }
    }

    if(!empty($_GET['order_list_id'])) {
		$order_list_id = $_GET['order_list_id'];
        $contactid = $_GET['contactid'];
        $query_update = "UPDATE `order_lists` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE order_id='$order_list_id'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Vendors/contacts_inbox.php?category=Vendors&edit='.$contactid);
        }
    }

    if(!empty($_GET['assetid'])) {
        $assetid = $_GET['assetid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `asset` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE assetid='$assetid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Asset/asset.php?type=active&category='.$category.'&filter=Top');
        }
    }

	if(!empty($_GET['budgetid'])) {
        $budgetid = $_GET['budgetid'];
        $category = $_GET['category'];
		$status = $_GET['status'];
        $query_update = "UPDATE `budget` SET status='$status' WHERE budgetid='$budgetid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Budget/budget.php?maintype=pending_budget&category='.$category.'&filter=Top');
        }
    }

	if(!empty($_GET['exerciseid'])) {
		$exerciseid = $_GET['exerciseid'];
        $type = mysqli_fetch_array(mysqli_query($dbc, "SELECT `type` FROM `exercise_config` WHERE `exerciseid`='$exerciseid'"))['type'];
        $result_update = mysqli_query($dbc, "UPDATE `exercise_config` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `exerciseid`='$exerciseid'");
		if($_GET['action'] == 'delete') {
			header('Location: Exercise Plan/exercise_config.php?view='.($type == 'Common' ? 'master' : 'private'));
		}
	}

	if(!empty($_GET['timetrackingid'])) {
		$timetrackingid = $_GET['timetrackingid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `time_tracking` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE timetrackingid='$timetrackingid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Time Tracking/time_tracking.php?type=TimeSheet&category='.$category.'&filter=Top');
        }
	}

    if(!empty($_GET['packageid'])) {
		$packageid = $_GET['packageid'];
        $category = $_GET['category'];
        $query_update = "UPDATE package SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE packageid='$packageid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Package/package.php?category='.$category.'&filter=Top');
        }
    }

    if(!empty($_GET['expenseid'])) {
        echo archive_content($dbc, 'expense', 'expenseid', $_GET['expenseid'], 'Expense/expenses.php');
    }
    if(!empty($_GET['promotionid'])) {
		$promotionid = $_GET['promotionid'];
        $category = $_GET['category'];
        $query_update = "UPDATE promotion SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE promotionid='$promotionid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Promotion/promotion.php?type=active&category='.$category.'&filter=Top');
        }
    }

    if(!empty($_GET['serviceid'])) {
		$serviceid = $_GET['serviceid'];
        //$category = $_GET['category'];
        $category = bin2hex($_GET['c']);
        $query_update = "UPDATE services SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE serviceid='$serviceid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            //header('Location: Services/services.php?type=active&category='.$category.'&filter=Top');
            header('Location: Services/index.php?c='.$category);
        }
    }

    if(!empty($_GET['passwordid'])) {
		$passwordid = $_GET['passwordid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `passwords` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE passwordid='$passwordid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Passwords/passwords.php?category='.$category.'&filter=Top');
        }
    }

	if(!empty($_GET['project_workflow_id'])) {
		$project_workflow_id = $_GET['project_workflow_id'];
        $category = $_GET['category'];
        $query_update = "UPDATE `project_workflow` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE project_workflow_id='$project_workflow_id'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Project Workflow/project_workflow.php?type=active&category='.$category.'&filter=Top');
        }
    }

    if(!empty($_GET['labourid'])) {
        $labourid = $_GET['labourid'];
        $category = $_GET['category'];
        $query_update = "UPDATE labour SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE labourid='$labourid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Labour/index.php?category='.$category.'&filter=Top');
        }
    }

    if(!empty($_GET['equipmentid'])) {
        $equipmentid = $_GET['equipmentid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `equipment` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE equipmentid='$equipmentid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Equipment/index.php?status=Active&category='.$category);
        }
    }

    if(!empty($_GET['customid'])) {
		$customid = $_GET['customid'];
        $category = $_GET['category'];
        $query_update = "UPDATE custom SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE customid='$customid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Custom/custom.php?category='.$category.'&filter=Top');
        }
    }

    if(!empty($_GET['certificateid'])) {
        $certificateid = $_GET['certificateid'];
        $category = $_GET['category'];
        $search_params = $_GET['s'];
        $query_update = "UPDATE `certificate` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE certificateid='$certificateid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Certificate/index.php');
        }
    }
	if(!empty($_GET['hrid'])) {
        $hrid = $_GET['hrid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `hr` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE hrid='$hrid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: HR/hr.php?tab='.$_GET['tab'].'&category='.$category.'&filter=Top');
        }
    }
	if(!empty($_GET['ticketid'])) {
        $ticketid = $_GET['ticketid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `tickets` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE ticketid='$ticketid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
			if($_GET['tab'] == 'daysheet') {
				header('Location: Daysheet/daysheet.php?category='.$category.'&filter=Top');
			}
			else {
				header('Location: Ticket/index.php?tile_name='.$category);
			}
        }
    }
	if(!empty($_GET['remove_checklist']) && !empty($_GET['checklistid'])) {
        $checklistid = $_GET['checklistid'];
		$checklist_type = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid`='$checklistid'"));
        $query_update = "UPDATE `checklist` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE checklistid='$checklistid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
			header('Location: Checklist/checklist.php?subtabid='.$checklist_type['subtabid']);
        }
    }
	if(!empty($_GET['remove_item_checklist']) && !empty($_GET['checklistid'])) {
        $checklistid = $_GET['checklistid'];
        $query_update = "UPDATE `item_checklist` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE checklistid='$checklistid'";
		$type = mysqli_fetch_array(mysqli_query($dbc, "SELECT `checklist_item` FROM `item_checklist` WHERE `checklistid`='$checklistid'"))[0];
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete' && $type == 'equipment') {
			header('Location: Equipment/equipment_checklist.php');
        } else if($_GET['action'] == 'delete' && $type == 'inventory') {
			header('Location: Inventory/inventory_checklist.php');
        }
    }
	if(!empty($_GET['manualtypeid'])) {
        $manualtypeid = $_GET['manualtypeid'];
        $category = $_GET['category'];
		$query_update = "UPDATE `manuals` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE manualtypeid='$manualtypeid'";
        $result_update = mysqli_query($dbc, $query_update);
		if($_GET['type'] == 'operations_manual') {
			if($_GET['action'] == 'delete') {
				header('Location: Manuals/operations_manual.php?tab='.$_GET['tab'].'&category='.$category.'&filter=Top');
			}
		}
		elseif($_GET['type'] == 'policy_procedures') {
			if($_GET['action'] == 'delete') {
				header('Location: Manuals/policy_procedures.php?tab='.$_GET['tab'].'&category='.$category.'&filter=Top');
			}
		}
		else {
			if($_GET['action'] == 'delete') {
				header('Location: Manuals/emp_handbook.php?tab='.$_GET['tab'].'&category='.$category.'&filter=Top');
			}
		}

    }
    if(!empty($_GET['marketing_materialid'])) {
		$marketing_materialid = $_GET['marketing_materialid'];
        $category = $_GET['category'];
        $query_update = "UPDATE marketing_material SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE marketing_materialid='$marketing_materialid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
        	if($_GET['from_tile'] == 'documents_all') {
        		header('Location: Documents/index.php?tab=marketing');
        	} else {
	            header('Location: Marketing Material/marketing_material.php?tab='.$_GET['tab'].'&category='.$category.'&filter=Top');
	        }
        }
    }
    if(!empty($_GET['internal_documentsid'])) {
		$internal_documentsid = $_GET['internal_documentsid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `internal_documents` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE internal_documentsid='$internal_documentsid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
        	if($_GET['from_tile'] == 'documents_all') {
        		header('Location: Documents/index.php?tile_name='.$_GET['tile_name'].'&tab=internal');
        	} else {
	            header('Location: Internal Documents/internal_documents.php?tab='.$_GET['tab'].'&category='.$category.'&filter=Top');
        	}
        }
    }
	if(!empty($_GET['documentid'])) {
		$documentid = $_GET['documentid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `documents` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE documentid='$documentid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Document/documents.php?type='.$_GET['type'].'&tile_name='.$_GET['tile_name'].'&tab='.$_GET['tab'].'&sub_tile_name='.$_GET['sub_tile_name'].'&category='.$category.'&filter=Top');
        }
    }
    if(!empty($_GET['client_documentsid'])) {
		$client_documentsid = $_GET['client_documentsid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `client_documents` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE client_documentsid='$client_documentsid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
        	if($_GET['from_tile'] == 'documents_all') {
        		header('Location: Documents/index.php?tile_name='.$_GET['tile_name'].'&tab=client');
        	} else {
	            header('Location: Client Documents/client_documents.php?tab='.$_GET['tab'].'&category='.$category.'&filter=Top');
	        }
        }
    }
	if(!empty($_GET['newsboardid'])) {
		$newsboardid = $_GET['newsboardid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `newsboard` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE newsboardid='$newsboardid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: News Board/newsboard.php?type='.$_GET['type'].'&category='.$category.'&filter=Top');
        }
    }
	if(!empty($_GET['intakeid'])) {
        echo archive_content($dbc, 'intake', 'intakeid', $_GET['intakeid'], 'Intake/intake.php');
    }
	if(!empty($_GET['guideid'])) {
        //include ('How To Guide/db_conn_htg.php');
        include ('database_connection_htg.php');
        $guideid = trim($_GET['guideid']);
        $page = trim($_GET['page']);
        $query_update = "UPDATE `how_to_guide` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `guideid`='$guideid'";
        $result_update = mysqli_query($dbc_htg, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: How To Guide/guides_dashboard.php?page='.$page);
        }
    }
	if(!empty($_GET['noteid'])) {
        include ('How To Guide/db_conn_htg.php');
        $noteid = trim($_GET['noteid']);
        $page = trim($_GET['page']);
        $query_update = "UPDATE `notes` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `noteid`='$noteid'";
        $result_update = mysqli_query($dbc_htg, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: How To Guide/notes.php?page='.$page);
        }
    }

	if(!empty($_GET['email_communicationid'])) {
        $email_communicationid = $_GET['email_communicationid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `email_communication` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE email_communicationid='$email_communicationid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Email Communication/email_communication.php?type='.$_GET['type'].'&category='.$category.'&filter=Top');
        }
    }

	if(!empty($_GET['estimateid'])) {
        $estimateid = $_GET['estimateid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `estimate` SET deleted='$deleted', status='', `date_of_archival` = '$date_of_archival' WHERE estimateid='$estimateid'";
        $result_update = mysqli_query($dbc, $query_update);
	}

	if(!empty($_GET['quoteid'])) {
        $quoteid = $_GET['quoteid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `quote` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE quoteid='$quoteid'";
        $result_update = mysqli_query($dbc, $query_update);
	}

	if(!empty($_GET['couponid'])) {
        echo archive_content($dbc, 'pos_touch_coupons', 'couponid', $_GET['couponid'], 'Point of Sale/coupons.php');
    }

    if(!empty($_GET['uploadid'])) {
        $projectid = $_GET['projectid'];
        $type = $_GET['type'];
        echo archive_content($dbc, 'project_document', 'uploadid', $_GET['uploadid'], 'Project/add_project.php?type='.$type.'&projectid='.$projectid);
    }

    if(!empty($_GET['productid'])) {
        echo archive_content($dbc, 'products', 'productid', $_GET['productid'], 'Products/products.php');
		$productid = $_GET['productid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `products` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE productid='$productid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Products/products.php?&category='.$category.'&filter=Top');
        }
    }

    if(!empty($_GET['matchid'])) {
        $matchid = $_GET['matchid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `match_contact` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `matchid`=$matchid";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Match/index.php');
        }
    }

	if(!empty($_GET['individualsupportplanid'])) {
		$individualsupportplanid = $_GET['individualsupportplanid'];
        $category = $_GET['category'];
        $query_update = "UPDATE individual_support_plan SET deleted='$deleted' , `date_of_archival` = '$date_of_archival' WHERE individualsupportplanid='$individualsupportplanid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Individual Support Plan/individual_support_plan.php?&category='.$category.'&filter=Top');
        }
    }

	if(!empty($_GET['keymethodologiesid'])) {
        $keymethodologiesid = $_GET['keymethodologiesid'];
        $category = $_GET['category'];
        $query_update = "UPDATE key_methodologies SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE keymethodologiesid=$keymethodologiesid";
        $result_update = mysqli_query($dbc, $query_update);
    }

	if(!empty($_GET['medicationid'])) {
        $medicationid = $_GET['medicationid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `medication` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `medicationid`=$medicationid";
        $result_update = mysqli_query($dbc, $query_update);
    }


	if(!empty($_GET['fundingid'])) {
        $fundingid = $_GET['fundingid'];
        $category = $_GET['category'];
        $query_update = "UPDATE fund_development_funding SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE fundingid=$fundingid";
        $result_update = mysqli_query($dbc, $query_update);
    }


    if(!empty($_GET['staff_documentsid'])) {
        $staff_documentsid = $_GET['staff_documentsid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `staff_documents` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE staff_documentsid='$staff_documentsid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
        	if($_GET['from_tile'] == 'documents_all') {
        		header('Location: Documents/index.php?tile_name='.$_GET['tile_name'].'&tab=staff');
        	} else {
		        header('Location: Staff Documents/staff_documents.php');
		    }
        }
    }

    if(!empty($_GET['custom_documentsid'])) {
        $custom_documentsid = $_GET['custom_documentsid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `custom_documents` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE custom_documentsid='$custom_documentsid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
    		header('Location: Documents/index.php?tile_name='.$_GET['tile_name'].'&tab='.$_GET['tab_name']);
        }
    }

    // Website Promotions
    if ( !empty($_GET['webpromoid']) ) {
        $webpromoidid   = $_GET['webpromoid'];
        $query_update   = "UPDATE `website_promotions` SET `deleted`='$deleted', `date_of_archival` = '$date_of_archival' WHERE `promoid`=$webpromoidid";
        $result_update  = mysqli_query ( $dbc, $query_update );

        if ( $_GET['action']=='delete' ) {
            header('Location: Website/website_promotions.php');
        }
    }

	/* "DELETE" (SET ARCHIVED TO 2 INSTEAD OF 1)  */

	if(!empty($_GET['newsboardid'])) {
		$newsboardid = $_GET['newsboardid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `newsboard` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE newsboardid='$newsboardid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: News Board/newsboard.php?category='.$category.'&filter=Top');
        }
    }

	if(!empty($_GET['expenseid'])) {
		$expenseid = $_GET['expenseid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `expense` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE expenseid='$expenseid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Expense/expenses.php?category='.$category.'&filter=Top');
        }
    }

	if(!empty($_GET['posid'])) {
		$posid = $_GET['posid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `point_of_sell` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE posid='$posid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Point of Sale/point_of_sell.php?category='.$category.'&filter=Top');
        }
    }

	if(!empty($_GET['sales_orderid'])) {
		$sales_orderid = $_GET['sales_orderid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `sales_order` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE posid='$sales_orderid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Sales Order/pending.php?category='.$category.'&filter=Top');
        }
    }

	if(!empty($_GET['poid'])) {
		$poid = $_GET['poid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `purchase_orders` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE posid='$poid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Purchase Order/complete.php?category='.$category.'&filter=Top');
        }
    }

    if(!empty($_GET['checkoutid'])) {
		$checkoutid = $_GET['checkoutid'];
        $archive_type = $_GET['archive_type'];
        $query_update = "UPDATE `invoice` SET deleted='$deleted' WHERE invoiceid='$checkoutid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Archived/archived_data.php?archive_type='.$archive_type);
        }
    }

	if(!empty($_GET['archive_checklistid'])) {
        $checklistid = $_GET['archive_checklistid'];
        $archive_type = $_GET['category']; echo $archive_type;
        $query_update = "UPDATE `checklist` SET deleted='$deleted', `date_of_archival`='$date_of_archival' WHERE `checklistid`='$checklistid'";
        $result_update = mysqli_query($dbc, $query_update);
        header('Location: Archived/archived_data.php?archive_type=checklist');
        die();
    }

	if(!empty($_GET['assigntotimerid'])) {
		$assigntotimerid = $_GET['assigntotimerid'];
        $category = $_GET['category'];
        $query_update = "UPDATE `project_manage_assign_to_timer` SET deleted='$deleted', `date_of_archival` = '$date_of_archival' WHERE assigntotimerid='$assigntotimerid'";
        $result_update = mysqli_query($dbc, $query_update);
        if($_GET['action'] == 'delete') {
            header('Location: Time Tracking/time_tracking.php?tab=shop_time_sheets&category='.$category.'&filter=Top');
        }
    }

    if(!empty($_GET['posgiftcardsid'])) {
    	$posgiftcardsid = $_GET['posgiftcardsid'];
    	$query_update = "UPDATE `pos_giftcards` SET `deleted` = '$deleted', `date_of_archival` = '$date_of_archival' WHERE `posgiftcardsid` = '$posgiftcardsid'";
    	$result_update = mysqli_query($dbc, $query_update);
    	if($_GET['action'] == 'delete') {
    		header('Location: POSAdvanced/giftcards.php');
    	}
    }

    if(!empty($_GET['intakeformid'])) {
    	$intakeformid = $_GET['intakeformid'];
    	$query_update = "UPDATE `intake_forms` SET `deleted` = '$deleted', `date_of_archival` = '$date_of_archival' WHERE `intakeformid` = '$intakeformid'";
    	$result_update = mysqli_query($dbc, $query_update);
    	if($_GET['action'] == 'delete') {
    		header('Location: Intake/intake.php?tab=softwareforms');
    	}
    }

    if(!empty($_GET['reviewid'])) {
    	$reviewid = $_GET['reviewid'];
    	$query_update = "UPDATE `performance_review` SET `deleted` = '$deleted', `date_of_archival` = '$date_of_archival' WHERE `reviewid` = '$reviewid'";
    	$result_update = mysqli_query($dbc, $query_update);
    	if($_GET['action'] == 'delete') {
    		header('Location: HR/index.php?performance_review=list');
    	}
    }

    if(!empty($_GET['incidentreportid'])) {
    	$incidentreportid = $_GET['incidentreportid'];
    	$query_update = "UPDATE `incident_report` SET `deleted` = '$deleted', `date_of_archival` = '$date_of_archival' WHERE `incidentreportid` = '$incidentreportid'";
    	$result_update = mysqli_query($dbc, $query_update);
    	if($_GET['action'] == 'delete') {
    		header('Location: Incident Report/incident_report.php');
    	}
    }

	/* END "DELETE" (SET ARCHIVED TO 2 INSTEAD OF 1)  */

	if($_GET['action'] == 'delete_2') {
		header('Location: Archived/archived_data.php?archive_type='.$category.'');
	}
	if($_GET['action'] == 'restore' && empty($_GET['from'])) {
		header('Location: Archived/archived_data.php?archive_type='.$category.'');
	}

	if(!empty($_GET['site_work_order'])) {
		$workorderid = $_GET['site_work_order'];
		$status = $_GET['action'];
		$status = ($status == 'archive' ? 'Archived' : ($status == 'approve' ? 'Approved' : ($status == 'reject' ? 'Rejected' : 'Pending')));
		mysqli_query($dbc, "UPDATE `site_work_orders` SET `status`='$status' WHERE `workorderid`='$workorderid'");
		if($status == 'Approved') {
			header('Location: Site Work Orders/site_work_orders.php?tab=active');
		} else {
			header('Location: Site Work Orders/site_work_orders.php?tab=pending');
		}
	}
?>

<?php include ('footer.php'); ?>
<?php
function archive_content($dbc, $table, $change_id, $change_value, $url) {
    $date_of_archival = date('Y-m-d');
    $result_update = mysqli_query($dbc, "UPDATE `$table` SET deleted='1', `date_of_archival` = '$date_of_archival' WHERE `$change_id` = '$change_value'");
    header('Location: '.$url.'');
}

function archive2_delete($dbc, $table, $change_id, $change_value, $url) {
    $result_update = mysqli_query($dbc, "UPDATE `$table` SET deleted='2' WHERE `$change_id` = '$change_value'");
    header('Location: '.$url.'');
}

function delete_content($dbc, $table, $change_id, $change_value, $url) {
    $result_update = mysqli_query($dbc, "DELETE FROM `$table` WHERE `$change_id` = '$change_value'");
    header('Location: '.$url.'');
}