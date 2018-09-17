<?php
/*
Add Vendor
*/
include ('../include.php');
error_reporting(0);

if (isset($_POST['send_drive_logs_approve'])) {
	$salesid_marketing_materialid = $_POST['send_drive_logs_approve'];
    $sm = explode('_', $salesid_marketing_materialid );
    $salesid = $sm[0];
    $marketing_materialid = $sm[1];

	$email_list = $_POST['getemailsapprove123_'.$marketing_materialid];
    $certuploadid = $_POST['certuploadid'];

	$query = mysqli_query($dbc,"SELECT document_link FROM marketing_material_uploads WHERE marketing_materialid='$marketing_materialid' AND type = 'Document'");
	$url = array();
	while($row = mysqli_fetch_array($query)) {
		$url[] = WEBSITE_URL . '/Marketing Material/download/' . $row['document_link'];
	}

    if ($email_list !== '') {
        $emails_arr = explode(',', $email_list );
        foreach( $emails_arr as $email )
        {
            if (!filter_var(trim($email), FILTER_VALIDATE_EMAIL) === false) {
            } else {
                 echo '<script type="text/javascript"> alert("One or more of the email addresses you have provided is not a proper email address.");
                        window.location.replace("add_sales.php?salesid='.$salesid.'"); </script>';
                 exit();
            }
        }

		$urls = implode('<br>', $url);
		$to_email = $email_list;
		$to = explode(',', $to_email);
		$from = 'info@rookconnect.com';
		$subject ="Marketing Material";
		$message = "Click on below link to download Marketing Material PDF. <br><br><br>" . $urls;
		send_email($from, $to, '', '', $subject, $message, '');
        echo '<script type="text/javascript"> alert("Marketing Material Sent.");
	    window.location.replace("add_sales.php?salesid='.$salesid.'"); </script>';
	} else {
        echo '<script type="text/javascript"> alert("Please enter Email");
	    window.location.replace("add_sales.php?salesid='.$salesid.'"); </script>';
    }
}

if (isset($_POST['add_sales'])) {
	$created_date = date('Y-m-d');
    $created_by = $_SESSION['contactid'];

    $lead_created_by = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
    $primary_staff = $_POST['primary_staff'];
    $share_lead = implode(',',$_POST['share_lead']);

    $m = 0;
	if($_POST['new_business'] != '') {
		$name = encryptIt($_POST['new_business']);
        //$query_insert_inventory = "INSERT INTO `contacts` (`category`, `name`) VALUES ('Sales Leads', '$name')";
        //$result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);
        //$businessid = mysqli_insert_id($dbc);
        $m = 1;
	} else {
        $businessid = $_POST['businessid'];
	}

	if($_POST['new_contact'] != '') {
		$first_name = encryptIt($_POST['new_contact']);
        //$query_insert_inventory = "INSERT INTO `contacts` (`category`, `businessid`, `name`, `first_name`, `office_phone`, `email_address`) VALUES ('Sales Leads', '$businessid', '$name', '$first_name', '$office_phone', '$email_address')";
        //$result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);
        $m = 1;
	} else {
        $contactid = $_POST['contactid'];
	}

	if($_POST['new_number'] != '') {
		$primary_number = filter_var($_POST['new_number'],FILTER_SANITIZE_STRING);
        $m = 1;
	} else {
        $primary_number = filter_var($_POST['primary_number'],FILTER_SANITIZE_STRING);
	}
	$office_phone = encryptIt($primary_number);

	if($_POST['new_email'] != '') {
		$email_address = $_POST['new_email'];
		$email_address1 = encryptIt($_POST['new_email']);
        $m = 1;
	} else {
        $email_address = filter_var($_POST['email_address'],FILTER_SANITIZE_STRING);
		$email_address1 = encryptIt($email_address);
	}

    if($m == 1) {
        $query_insert_inventory = "INSERT INTO `contacts` (`category`, `name`, `first_name`, `office_phone`, `email_address`) VALUES ('Sales Leads', '$name', '$first_name', '$office_phone', '$email_address1')";
        $result_insert_inventory = mysqli_query($dbc, $query_insert_inventory);
        $businessid = mysqli_insert_id($dbc);

        $query_update_inventory = "UPDATE `contacts` SET `businessid` = '$businessid' WHERE `contactid` = '$businessid'";
        $result_update_inventory	= mysqli_query($dbc, $query_update_inventory);
        $contactid = $businessid;
    }

	$marketingmatids = $_POST['marketingmaterialid'];
	$marketcount = 0;
	foreach($marketingmatids as $marketingmatid) {
		$query = mysqli_query($dbc,"SELECT document_link FROM marketing_material_uploads WHERE marketing_materialid='$marketingmatid' AND type = 'Document'");
		$url = array();
		while($row = mysqli_fetch_array($query)) {
			$url[] = WEBSITE_URL . '/Marketing Material/download/' . $row['document_link'];
		}

		$urls = implode('<br>', $url);
		$email = $_POST['email_address'][$marketcount];
		$from = 'info@rookconnect.com';
		$subject ="Marketing Material";
		$message = "Click on below link to download Marketing Material PDF. <br><br><br>" . $urls;
		if(!empty($email)) {
			send_email($from, $email, '', '', $subject, $message, '');
		}
		$marketcount++;
	}

    $lead_value = filter_var($_POST['lead_value'],FILTER_SANITIZE_STRING);
    $estimated_close_date = filter_var($_POST['estimated_close_date'],FILTER_SANITIZE_STRING);
    $serviceid = implode(',',$_POST['serviceid']);
    $productid = implode(',',$_POST['productid']);
    $marketingmaterialid = implode(',',$_POST['marketingmaterialid']);
    $lead_source = filter_var($_POST['lead_source'],FILTER_SANITIZE_STRING);
    $next_action = filter_var($_POST['next_action'],FILTER_SANITIZE_STRING);
    $new_reminder = filter_var($_POST['new_reminder'],FILTER_SANITIZE_STRING);
    $number_of_days = filter_var($_POST['number_of_days'],FILTER_SANITIZE_STRING);
    if($number_of_days > 0) {
        $number_of_days_start_date = date('Y-m-d');
    }
    $status = $_POST['status'];

    if(empty($_POST['salesid'])) {
        $query_insert_vendor = "INSERT INTO `sales` (`created_date`, `lead_created_by`, `primary_staff`, `share_lead`, `businessid`, `contactid`, `primary_number`, `email_address`, `lead_value`, `estimated_close_date`, `serviceid`, `productid`, `lead_source`, `marketingmaterialid`, `next_action`, `new_reminder`, `status`, `number_of_days`, `number_of_days_start_date`) VALUES ('$created_date', '$lead_created_by', '$primary_staff', '$share_lead', '$businessid', '$contactid', '$primary_number', '$email_address', '$lead_value', '$estimated_close_date', '$serviceid', '$productid', '$lead_source', '$marketingmaterialid', '$next_action', '$new_reminder', '$status', '$number_of_days', '$number_of_days_start_date')";
        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $salesid = mysqli_insert_id($dbc);
				$before_change = '';
				$history = "Sales Added.";
				add_update_history($dbc, 'sales_history', $history, '', $before_change, $salesid);
        $url = 'Added';
		$old_action = '';
    } else {
        $salesid = $_POST['salesid'];

				$before_change = capture_before_change($dbc, 'sales', 'primary_staff', 'salesid', $salesid);
				$before_change .= capture_before_change($dbc, 'sales', 'share_lead', 'salesid', $salesid);
				$before_change .= capture_before_change($dbc, 'sales', 'businessid', 'salesid', $salesid);
				$before_change .= capture_before_change($dbc, 'sales', 'contactid', 'salesid', $salesid);
				$before_change .= capture_before_change($dbc, 'sales', 'primary_number', 'salesid', $salesid);
				$before_change .= capture_before_change($dbc, 'sales', 'email_address', 'salesid', $salesid);
				$before_change .= capture_before_change($dbc, 'sales', 'lead_value', 'salesid', $salesid);
        $query_update_vendor = "UPDATE `sales` SET `primary_staff` = '$primary_staff', `share_lead` = '$share_lead', `businessid` = '$businessid', `contactid` = '$contactid', `primary_number` = '$primary_number', `email_address` = '$email_address', `lead_value` = '$lead_value', `estimated_close_date` = '$estimated_close_date', `serviceid` = '$serviceid', `productid` = '$productid', `lead_source` = '$lead_source', `marketingmaterialid` = '$marketingmaterialid', `next_action` = '$next_action', `new_reminder` = '$new_reminder', `status` = '$status', `number_of_days` = '$number_of_days', `number_of_days_start_date` = '$number_of_days_start_date' WHERE `salesid` = '$salesid'";
				$history = "Sales Updated.";
				$history .= capture_after_change('primary_staff', $primary_staff);
				$history .= capture_after_change('share_lead', $share_lead);
				$history .= capture_after_change('businessid', $businessid);
				$history .= capture_after_change('contactid', $contactid);
				$history .= capture_after_change('primary_number', $primary_number);
				$history .= capture_after_change('email_address', $email_address);
				$history .= capture_after_change('lead_value', $lead_value);
				add_update_history($dbc, 'sales_history', $history, '', $before_change, $salesid);
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        $url = 'Updated';
		$old_action = mysqli_fetch_array(mysqli_query($dbc, "SELECT `next_action` FROM `sales` WHERE `salesid`='$salesid'"))['next_action'];
    }

	//Schedule Reminders
	if($new_reminder != '' && $new_reminder != '0000-00-00' && $old_action != $next_action) {
		$body = filter_var(htmlentities('This is a reminder about a sales lead that needs to be followed up with.<br />
			The scheduled next action is: '.$next_action.'<br />
			Click <a href="'.WEBSITE_URL.'/Sales/add_sales.php?salesid='.$salesid.'">here</a> to review the lead.'), FILTER_SANITIZE_STRING);
		$verify = "sales#*#next_action#*#salesid#*#".$salesid."#*#".$next_action;
        mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$primary_staff' AND `src_table` = 'sales' AND `src_tableid` = '$salesid'");
		$reminder_result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_type`, `subject`, `body`, `src_table`, `src_tableid`)
			VALUES ('$primary_staff', '$new_reminder', 'Sales Reminder', 'Reminder of Sales Lead', '$body', 'sales', '$salesid')");
	}


    //Notes
    $note_heading = filter_var($_POST['note_heading'],FILTER_SANITIZE_STRING);
    $ticket_comment = htmlentities($_POST['comment']);
    $t_comment = filter_var($ticket_comment,FILTER_SANITIZE_STRING);

    if($t_comment != '') {
        $email_comment = $_POST['email_comment'];
        $query_insert_ca = "INSERT INTO `sales_notes` (`salesid`, `comment`, `email_comment`, `created_date`, `created_by`, `note_heading`) VALUES ('$salesid', '$t_comment', '$email_comment', '$created_date', '$created_by', '$note_heading')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

        if ($_POST['send_email_on_comment'] == 'Yes') {
            $email = get_email($dbc, $email_comment);
            $subject = 'Note Added on Sales.';

            $email_body = 'Note : '.$_POST['comment'].'<br><br>';

            //send_email('', $email, '', '', $subject, $email_body, '');
        }
    }
    //Notes

    //Document
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    foreach($_FILES['upload_document']['name'] as $i => $document) {
        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]) ;

        if($document != '') {
			$label = filter_var($_POST['document_label'][$i], FILTER_SANITIZE_STRING);
            $query_insert_client_doc = "INSERT INTO `sales_document` (`salesid`, `label`, `document`, `created_date`, `created_by`) VALUES ('$salesid', '$label', '$document', '$created_date', '$created_by')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
        }
    }

    foreach($_POST['support_link'] as $i => $support_link) {
        if($support_link != '') {
			$label = filter_var($_POST['link_label'][$i], FILTER_SANITIZE_STRING);
            $query_insert_client_doc = "INSERT INTO `sales_document` (`salesid`, `label`, `link`, `created_date`, `created_by`) VALUES ('$salesid', '$label', '$support_link', '$created_date', '$created_by')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
        }
    }
    //Document

    echo '<script type="text/javascript"> window.location.replace("sales.php"); </script>';

 //   mysqli_close($dbc);//Close the DB Connection
}
?>
<script type="text/javascript">
$(document).ready(function() {
    $("#form1").submit(function( event ) {
        var service_type = $("#service_type").val();
        var category = $("input[name=category]").val();
        var heading = $("input[name=heading]").val();
        if (service_type == '' || category == '' || heading == '' ) {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });
});
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('sales');
?>
<div class="container">
	<div class="row hide_on_iframe">

    <h1>Add Sales</h1>
	<div class="gap-top triple-gap-bottom"><a href="sales.php" class="btn config-btn">Back to Dashboard</a></div>

	<div class="notice">
		<img src="<?= WEBSITE_URL; ?>/img/info-w.png" width="20">&nbsp;&nbsp;
		If you don't see any fields below, please make sure you enable the required fields from Settings (gear icon on top right) on the Sales Dashboard.
	</div>

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT sales FROM field_config"));
        $value_config = ','.$get_field_config['sales'].',';

        $lead_created_by = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
        $primary_staff = $_SESSION['contactid'];
        $share_lead = '';
        $businessid = '';
        $contactid = '';
        $primary_number = '';
        $email_address = '';
        $number_of_days = '';
        $number_of_days_start_date = '';
        $lead_value = '';
        $estimated_close_date = '';
        $serviceid = '';
        $productid = '';
        $marketingmaterialid = '';
        $lead_source = '';
        $next_action = '';
        $new_reminder = '';
        $status = '';

        if(!empty($_GET['businessid'])) {
            $businessid = $_GET['businessid'];
        }

        if(!empty($_GET['salesid'])) {

            $salesid = $_GET['salesid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM sales WHERE salesid='$salesid'"));

            $lead_created_by = $get_contact['lead_created_by'];
            $primary_staff = $get_contact['primary_staff'];
            $share_lead = $get_contact['share_lead'];
            $businessid = $get_contact['businessid'];
            $contactid = $get_contact['contactid'];
            $primary_number = $get_contact['primary_number'];
            $number_of_days = $get_contact['number_of_days'];
            $number_of_days_start_date = $get_contact['number_of_days_start_date'];
            $email_address = $get_contact['email_address'];
            $lead_value = $get_contact['lead_value'];
            $estimated_close_date = $get_contact['estimated_close_date'];
            $serviceid = $get_contact['serviceid'];
            $productid = $get_contact['productid'];
            $marketingmaterialid = $get_contact['marketingmaterialid'];
            $lead_source = $get_contact['lead_source'];
            $next_action = $get_contact['next_action'];
            $new_reminder = $get_contact['new_reminder'];
            $status = $get_contact['status'];

        ?>
        <input type="hidden" id="salesid" name="salesid" value="<?php echo $salesid ?>" />
        <?php   }      ?>

        <div class="panel-group" id="accordion2">

            <?php if (strpos($value_config, ','."Staff Information".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <span class="popover-examples list-inline" style="margin:0 5px 0 0;">
							<a data-toggle="tooltip" data-placement="top" title="Select the staff providing the sales lead."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a>
						</span>
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info" >
                            Staff Information<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_info" class="panel-collapse collapse">
                    <div class="panel-body">

                        <?php
                        include ('add_sales_staff_info.php');
                        ?>

                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Lead Information".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <span class="popover-examples list-inline" style="margin:0 5px 0 0;">
							<a data-toggle="tooltip" data-placement="top" title="Enter the lead information here."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a>
						</span>
						<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_desc" >
                            Lead Information<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_desc" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php
                        include ('add_sales_lead_info.php');
                        ?>

                     </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Service".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_cost" >
                            Services<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_cost" class="panel-collapse collapse">
                    <div class="panel-body">

                        <?php
                        include ('add_sales_service.php');
                        ?>

                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Products".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_services" >
                            Products<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_services" class="panel-collapse collapse">
                    <div class="panel-body">

                        <?php
                        include ('add_sales_products.php');
                        ?>

                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Lead Source".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <span class="popover-examples list-inline" style="margin:0 5px 0 0;">
							<a data-toggle="tooltip" data-placement="top" title="Select the lead source. Make sure to add them first from within Sales Dashboard settings."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a>
						</span>
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_client" >
                            Lead Source<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_client" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php
                        include ('add_sales_lead_source.php');
                        ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Reference Documents".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <span class="popover-examples list-inline" style="margin:0 5px 0 0;">
							<a data-toggle="tooltip" data-placement="top" title="Add any documents/links related to this sales lead."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a>
						</span>
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_saleser" >
                            Reference Documents<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_saleser" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php
                        include ('add_sales_ref_doc.php');
                        ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Marketing Material".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_vendor" >
                            Marketing Material<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_vendor" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php
                        include ('add_sales_marketing_material.php');
                        ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Information Gathering".',') !== FALSE) {
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_inv" >
                            Information Gathering<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_inv" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php
                        include ('add_sales_info_gathering.php');
                        ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Estimate".',') !== FALSE) {
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_equipment" >
                            Estimate<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_equipment" class="panel-collapse collapse">
                    <div class="panel-body estimate">

                        <?php
                        include ('add_sales_estimate.php');
                        ?>

                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Quote".',') !== FALSE) {
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_eq_cat" >
                            Quote<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_eq_cat" class="panel-collapse collapse">
                    <div class="panel-body quote">

                        <?php
                        include ('add_sales_quote.php');
                        ?>

                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Next Action".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <span class="popover-examples list-inline" style="margin:0 5px 0 0;">
							<a data-toggle="tooltip" data-placement="top" title="Select the next action you have to take and the reminder date."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a>
						</span>
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff" >
                            Next Action<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_staff" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php
                        include ('add_sales_next_action.php');
                        ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Lead Status".',') !== FALSE) { ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <span class="popover-examples list-inline" style="margin:0 5px 0 0;">
							<a data-toggle="tooltip" data-placement="top" title="Select the status of the sale lead as it stands."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a>
						</span>
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_status" >
                            Lead Status<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_status" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php
                        include ('add_sales_lead_status.php');
                        ?>
                    </div>
                </div>
            </div>
            <?php } ?>

            <?php if (strpos($value_config, ','."Lead Notes".',') !== FALSE) {
            ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <span class="popover-examples list-inline" style="margin:0 5px 0 0;">
							<a data-toggle="tooltip" data-placement="top" title="Add any notes related to this sales lead."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a>
						</span>
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_staff_pos" >
                            Lead Notes<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <?php
                $accord = '';
                if(!empty($_GET['go'])) {
                    $accord = ' in';
                } ?>
                <div id="collapse_staff_pos" class="panel-collapse collapse <?php echo $accord; ?>">
                    <div class="panel-body">

                        <?php
                        include ('add_sales_note.php');
                        ?>

                    </div>
                </div>
            </div>
            <?php } ?>

        </div>

        <div class="form-group">
          <div class="col-sm-12">
              <p><span class="hp-red"><em>Required Fields *</em></span></p>
          </div>
        </div>

        <div class="pull-left">
            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Clicking here will discard this lead."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a href="sales.php" class="btn brand-btn btn-lg">Back</a>
			<!--<a href="#" class="btn brand-btn btn-lg pull-right" onclick="history.go(-1);return false;">Back</a>-->
		</div>
        <div class="pull-right">
            <span class="popover-examples list-inline"><a style="margin:0 5px 0 0;" data-toggle="tooltip" data-placement="top" title="Click here to submit this lead."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<button type="submit" name="add_sales" value="Submit" class="btn brand-btn btn-lg">Submit</button>
        </div>



    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>
