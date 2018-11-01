<?php
/*
Dashboard
*/
include_once('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['submit'])) {
	$created_date = date('Y-m-d');
    $created_by = $_SESSION['contactid'];

    $index = $_POST['index'];

    for($i=0;$i<$index;$i++) {
        if(isset($_POST['tempticketid'][$i])) {
            $tempticketid_all = $_POST['tempticketid'][$i];
            $type = explode('_', $tempticketid_all);
            $tempticketid = $type[1];

            $get_tt = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM temp_ticket WHERE tempticketid='$tempticketid'"));
            $businessid = $get_tt['businessid'];
            $clientid = $get_tt['clientid'];

            $projectid = $get_tt['projectid'];
            $client_projectid = '';
			if(substr($projectid,0,1) == 'C') {
				$client_projectid = substr($projectid,1);
				$projectid = '';
			}
            $service = $get_tt['category'];
            $service_type = $get_tt['service_type'];

            $contactid = ','.implode(',',$_POST['contactid_'.$tempticketid]).',';
            $heading = $_POST['heading_'.$tempticketid];

            $a_work = htmlentities($_POST['assignwork_'.$tempticketid]);
            $assign_work = filter_var($a_work,FILTER_SANITIZE_STRING);

            $status = $_POST['status_'.$tempticketid];
            $to_do_date = $_POST['to_do_date_'.$tempticketid];
            $internal_qa_date = $_POST['internal_qa_date_'.$tempticketid];
            $deliverable_date = $_POST['deliverable_date_'.$tempticketid];

            if($type[0] == 'ticket') {
                $query_insert_ca = "INSERT INTO `tickets` (`businessid`, `clientid`, `projectid`, `client_projectid`, `contactid`, `service`, `heading`, `created_date`, `created_by`, `assign_work`, `service_type`, `status`, `to_do_date`, `internal_qa_date`, `deliverable_date`) VALUES ('$businessid', '$clientid', '$projectid', '$client_projectid', '$contactid', '$service', '$heading', '$created_date', '$created_by', '$assign_work', '$service_type', '$status', '$to_do_date', '$internal_qa_date', '$deliverable_date')";
                $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
                $ticketid = mysqli_insert_id($dbc);

                echo insert_day_overview($dbc, $created_by, 'Ticket', date('Y-m-d'), '', 'Created '.TICKET_NOUN.' #'.$ticketid);

                //deliverables
                if($status != '') {
                    $query_insert_ca = "INSERT INTO `ticket_deliverables` (`ticketid`, `status`, `contactid`, `created_date`, `created_by`) VALUES ('$ticketid', '$status', '$contactid', '$created_date', '$created_by')";
                    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

                    //Mail
                    $get_user = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT email_address FROM contacts WHERE contactid='$contactid'"));
                    $to = $get_user['email_address'];

                    $ticket_url = $_SERVER['SERVER_NAME'].'/Ticket/index.php?edit='.$ticketid.'&from=daysheet';

                    $subject = 'FFM - '.TICKET_NOUN.' Assigned to You';

                    $message = " A ticket has been assigned to you.<br/><br/>";

                    $message .= "<a target='_blank' href='".$ticket_url."'>Go</a><br/><br/><br/>";
                    $message .= '<img src="'.WEBSITE_URL.'/img/ffm-signature.png" width="154" height="77" border="0" alt="">';
                }
            }

            if($type[0] == 'wo') {
                $query_insert_ca = "INSERT INTO `workorder` (`businessid`, `clientid`, `projectid`, `contactid`, `service`, `heading`, `created_date`, `created_by`, `assign_work`, `service_type`, `status`, `to_do_date`, `internal_qa_date`, `deliverable_date`) VALUES ('$businessid', '$clientid', '$projectid', '$contactid', '$service', '$heading', '$created_date', '$created_by', '$assign_work', '$service_type', '$status', '$to_do_date', '$internal_qa_date', '$deliverable_date')";
                $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
                $workorderid = mysqli_insert_id($dbc);

                echo insert_day_overview($dbc, $created_by, 'Work Order', date('Y-m-d'), '', 'Created Work Order #'.$workorderid);

                //deliverables
                if($status != '') {
                    $query_insert_ca = "INSERT INTO `workorder_deliverables` (`workorderid`, `status`, `contactid`, `created_date`, `created_by`) VALUES ('$workorderid', '$status', '$contactid', '$created_date', '$created_by')";
                    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

                    //Mail
                    $get_user = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT email_address FROM contacts WHERE contactid='$contactid'"));
                    $to = $get_user['email_address'];

                    $ticket_url = $_SERVER['SERVER_NAME'].'/Work Order/add_workorder.php?workorderid='.$workorderid.'&from=daysheet';

                    $subject = 'FFM - Work Order Assigned to You';

                    $message = " Below Work Order Assigned To you.<br/><br/>";

                    $message .= "<a target='_blank' href='".$ticket_url."'>Go</a><br/><br/><br/>";
                    $message .= '<img src="'.WEBSITE_URL.'/img/ffm-signature.png" width="154" height="77" border="0" alt="">';
                }

            }

        }
    }

    mysqli_query($dbc, "DELETE FROM temp_ticket WHERE projectid='$projectid'");

    if($type[0] == 'ticket') {
        echo '<script type="text/javascript"> window.location.replace("tickets.php"); </script>';
    } else {
        echo '<script type="text/javascript"> window.location.replace("../Work Order/workorder.php"); </script>';
    }
}
?>
<script type="text/javascript">

</script>

</head>
<body>

<?php include_once('../navigation.php'); ?>

<div class="container">
	<div class="row">

    <div class="col-md-12">

        <form id="form1" name="form1" method="post" action="create_project_ticket.php" enctype="multipart/form-data" class="form-horizontal" role="form">

        <h1><?php echo AFTER_PROJECT; ?></h1>
        <div class="panel-group" id="accordion2">
        <?php
        $projectid = $_GET['pid'];
        $query_check_credentials = "SELECT r.*, c.name FROM temp_ticket r, contacts c WHERE r.businessid = c.contactid AND r.projectid = '$projectid' ORDER BY r.category";

        $result = mysqli_query($dbc, $query_check_credentials);
        $test = 0;
        $col = 0;
        $index = 0;
        while($row = mysqli_fetch_array( $result )) { ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_<?php echo $col; ?>" >
                            <?php echo $row['category']; ?><span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_<?php echo $col; ?>" class="panel-collapse collapse">
                    <div class="panel-body">

                        <?php if(AFTER_PROJECT == 'Work Order') { ?>
                        <div class="form-group">
                            <label for="first_name" class="col-sm-4 control-label">Create <?php echo AFTER_PROJECT; ?>:</label>
                            <div class="col-sm-8">
                                <input style="height: 30px; width: 30px;" type="checkbox" value="wo_<?php echo $row['tempticketid']; ?>" name="tempticketid[]" id="nal">
                            </div>
                        </div>
                        <?php } ?>

                        <?php if(AFTER_PROJECT == 'Ticket') { ?>
                        <div class="form-group">
                            <label for="first_name" class="col-sm-4 control-label">Create <?php echo AFTER_PROJECT; ?>:</label>
                            <div class="col-sm-8">
                                <input style="height: 30px; width: 30px;" type="checkbox" value="ticket_<?php echo $row['tempticketid']; ?>" name="tempticketid[]" id="nal">
                            </div>
                        </div>
                        <?php } ?>

                        <?php if(AFTER_PROJECT == 'Ticket/Work Order') { ?>
                        <div class="form-group">
                            <label for="first_name" class="col-sm-4 control-label">Create <?= TICKET_NOUN ?>:</label>
                            <div class="col-sm-8">
                                <input style="height: 30px; width: 30px;" type="checkbox" value="ticket_<?php echo $row['tempticketid']; ?>" name="tempticketid[]" id="nal">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="first_name" class="col-sm-4 control-label">Create Work Order:</label>
                            <div class="col-sm-8">
                                <input style="height: 30px; width: 30px;" type="checkbox" value="wo_<?php echo $row['tempticketid']; ?>" name="tempticketid[]" id="nal">
                            </div>
                        </div>
                        <?php } ?>

                        <div class="form-group">
                            <label for="first_name" class="col-sm-4 control-label">Heading:</label>
                            <div class="col-sm-8">
                                <input name="heading_<?php echo $row['tempticketid']; ?>" type="text" value="<?php echo $heading; ?>" class="form-control">
                            </div>
                        </div>

                      <div class="form-group">
                        <label for="site_name" class="col-sm-4 control-label">Status:</label>
                        <div class="col-sm-8">
                            <select data-placeholder="Choose a Status..." name="status_<?php echo $row['tempticketid']; ?>" class="chosen-select-deselect form-control input-sm">
                                <option value=""></option>
                                <option value="Sales/Estimate/RFP">Sales/Estimate/RFP</option>
                                <option value="Strategy Needed">Strategy Needed</option>
                                <option value="Last Minute Priority">Last Minute Priority</option>
                                <option value="Information Gathering">Information Gathering</option>
                                <option value="To Be Scheduled">To Be Scheduled</option>
                                <option value="Scheduled/To Do">Scheduled/To Do</option>
                                <option value="Doing Today">Doing Today</option>
                                <option value="Internal QA">Internal QA</option>
                                <option value="Customer QA">Customer QA</option>
                                <option value="Waiting On Customer">Waiting On Customer</option>
                                <option value="Done">Done</option>
                                <option value="Archive">Archive</option>
                            </select>
                        </div>
                      </div>

                         <div class="form-group">
                          <label for="site_name" class="col-sm-4 control-label">Assign To:</label>
                          <div class="col-sm-8">
                            <select data-placeholder="Choose a Staff Member..." multiple name="contactid_<?php echo $row['tempticketid']; ?>[]" class="chosen-select-deselect form-control" width="380">
								  <?php
									$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
									foreach($query as $id) {
										$selected = '';
										echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
									}
								  ?>
                            </select>
                          </div>
                        </div>

                        <div class="form-group">
                            <label for="first_name" class="col-sm-4 control-label">Assign Work:</label>
                            <div class="col-sm-8">
                                <textarea name="assignwork_<?php echo $row['tempticketid']; ?>" rows="4" cols="50" class="form-control" ><?php echo $row['desc']; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label for="first_name" class="col-sm-4 control-label text-right">TO DO Date:</label>
                            <div class="col-sm-8">
                                <input name="to_do_date_<?php echo $row['tempticketid']; ?>" type="text" class="datepicker"></p>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label for="first_name" class="col-sm-4 control-label text-right">Internal QA Date:</label>
                            <div class="col-sm-8">
                                <input name="internal_qa_date_<?php echo $row['tempticketid']; ?>" type="text" class="datepicker"></p>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label for="first_name" class="col-sm-4 control-label text-right">Deliverable Date:</label>
                            <div class="col-sm-8">
                                <input name="deliverable_date_<?php echo $row['tempticketid']; ?>" type="text" class="datepicker"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php
            $index++;
            $col++;
            }
        ?>
        <input type="hidden" name="index" value="<?php echo $index; ?>">

        </div>

        <div class="form-group">
            <div class="col-sm-4 clearfix">
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>
            </div>
            <div class="col-sm-8">
                <button	type="submit" name="submit"	title="The entire form will submit and close if this submit button is pressed." value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
            </div>
        </div>



        </form>
        </div>

	</div>
</div>

<?php include_once('../footer.php'); ?>
