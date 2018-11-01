<?php
/*
Dashboard
*/
include ('../include.php');
include_once('../tcpdf/tcpdf.php');
error_reporting(0);

if (isset($_POST['submit'])) {
	$created_date = date('Y-m-d');
    $created_by = $_SESSION['contactid'];

    $index = $_POST['index'];

    for($i=0;$i<$index;$i++) {
        if(isset($_POST['tempworkorderid'][$i])) {
            $tempworkorderid = $_POST['tempworkorderid'][$i];
            $get_tt = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM temp_workorder WHERE tempworkorderid='$tempworkorderid'"));
            $clientid = $get_tt['clientid'];

            $projectid = $get_tt['projectid'];
            $service = $get_tt['category'];
            $service_type = $get_tt['service_type'];

            $contactid = ','.implode(',',$_POST['contactid_'.$tempworkorderid]).',';
            $heading = $_POST['heading_'.$tempworkorderid];

            $a_work = htmlentities($_POST['assignwork_'.$tempworkorderid]);
            $assign_work = filter_var($a_work,FILTER_SANITIZE_STRING);

            $status = $_POST['status_'.$tempworkorderid];
            $to_do_date = $_POST['to_do_date_'.$tempworkorderid];
            $internal_qa_date = $_POST['internal_qa_date_'.$tempworkorderid];
            $deliverable_date = $_POST['deliverable_date_'.$tempworkorderid];

            $query_insert_ca = "INSERT INTO `workorder` (`clientid`, `projectid`, `contactid`, `service`, `heading`, `created_date`, `created_by`, `assign_work`, `service_type`, `status`, `to_do_date`, `internal_qa_date`, `deliverable_date`) VALUES ('$clientid', '$projectid', '$contactid', '$service', '$heading', '$created_date', '$created_by', '$assign_work', '$service_type', '$status', '$to_do_date', '$internal_qa_date', '$deliverable_date')";
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

                $workorder_url = $_SERVER['SERVER_NAME'].'/Work Order/add_workorder.php?workorderid='.$workorderid;

                $subject = 'FFM - Work Order Assigned to You';

                $message = "FFM - Work Order Assigned To You.<br/><br/>";

                $message .= "<a target='_blank' href='".$workorder_url."'>Go</a><br/><br/><br/>";
                $message .= '<img src="'.WEBSITE_URL.'/img/ffm-signature.png" width="154" height="77" border="0" alt="">';

                //send_email('', $to, '', '', $subject, $message, '');
                //Mail
            }
        }
    }

    mysqli_query($dbc, "DELETE FROM temp_workorder WHERE projectid='$projectid'");
    echo '<script type="text/javascript"> window.location.replace("workorder.php"); </script>';
}

?>
<script type="text/javascript">

</script>

</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
	<div class="row">

    <div class="col-md-12">

        <form id="form1" name="form1" method="post" action="create_project_workorder.php" enctype="multipart/form-data" class="form-horizontal" role="form">

        <h1><?php echo AFTER_PROJECT; ?></h1>
        <div class="panel-group" id="accordion2">
        <?php
        $projectid = $_GET['pid'];
        $query_check_credentials = "SELECT r.*, c.name FROM temp_workorder r, contacts c WHERE r.clientid = c.contactid AND r.projectid = '$projectid' ORDER BY r.category";

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

                        <div class="form-group">
                            <label for="first_name" class="col-sm-4 control-label">Create <?php echo AFTER_PROJECT; ?>:</label>
                            <div class="col-sm-8">
                                <input style="height: 30px; width: 30px;" type="checkbox" value="<?php echo $row['tempworkorderid']; ?>" name="tempworkorderid[]" id="nal">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="first_name" class="col-sm-4 control-label">Heading:</label>
                            <div class="col-sm-8">
                                <input name="heading_<?php echo $row['tempworkorderid']; ?>" type="text" value="<?php echo $heading; ?>" class="form-control">
                            </div>
                        </div>

                      <div class="form-group">
                        <label for="site_name" class="col-sm-4 control-label">Status:</label>
                        <div class="col-sm-8">
                            <select data-placeholder="Choose a Status..." name="status_<?php echo $row['tempworkorderid']; ?>" class="chosen-select-deselect form-control input-sm">
                                <option value=""></option>
                                <option value="Sales/Estimate/RFP">Sales/Estimate/RFP</option>
                                <option value="Strategy Needed">Strategy Needed</option>
                                <option value="Last Minute Priority">Last Minute Priority</option>
                                <option value="Information Gathering">Information Gathering</option>
                                <option value="To Be Scheduled">To Be Scheduled</option>
                                <option value="Scheduled/To Do">Scheduled/To Do</option>
                                <option value="Doing">Doing</option>
                                <option value="Internal QA">Internal QA</option>
                                <option value="Client QA">Client QA</option>
                                <option value="Waiting On Client">Waiting On Client</option>
                                <option value="Done">Done</option>
                                <option value="Archived">Archived</option>
                            </select>
                        </div>
                      </div>

                         <div class="form-group">
                          <label for="site_name" class="col-sm-4 control-label">Assign To:</label>
                          <div class="col-sm-8">
                            <select data-placeholder="Select a Staff Member..." multiple name="contactid_<?php echo $row['tempworkorderid']; ?>[]" class="chosen-select-deselect form-control" width="380">
							  <?php
								$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Staff' AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
								foreach($query as $id) {
									$selected = '';
									//$selected = $id == $contactid ? 'selected = "selected"' : '';
									echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
								}
							  ?>
                            </select>
                          </div>
                        </div>

                        <div class="form-group">
                            <label for="first_name" class="col-sm-4 control-label">Assign Work:</label>
                            <div class="col-sm-8">
                                <textarea name="assignwork_<?php echo $row['tempworkorderid']; ?>" rows="4" cols="50" class="form-control" ><?php echo $row['desc']; ?></textarea>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label for="first_name" class="col-sm-4 control-label text-right">TO DO Date:</label>
                            <div class="col-sm-8">
                                <input name="to_do_date_<?php echo $row['tempworkorderid']; ?>" type="text" class="datepicker"></p>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label for="first_name" class="col-sm-4 control-label text-right">Internal QA Date:</label>
                            <div class="col-sm-8">
                                <input name="internal_qa_date_<?php echo $row['tempworkorderid']; ?>" type="text" class="datepicker"></p>
                            </div>
                        </div>

                        <div class="form-group clearfix">
                            <label for="first_name" class="col-sm-4 control-label text-right">Deliverable Date:</label>
                            <div class="col-sm-8">
                                <input name="deliverable_date_<?php echo $row['tempworkorderid']; ?>" type="text" class="datepicker"></p>
                            </div>
                        </div>

                        <?php //include ('add_view_workorder_documents.php'); ?>
                        <?php //include ('add_view_workorder_deliverables.php'); ?>
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
                <!--<a href="projects.php" class="btn brand-btn pull-right">Back</a>-->
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>
            </div>
            <div class="col-sm-8">
                <button	type="submit" name="submit"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
            </div>
        </div>



        </form>
        </div>

	</div>
</div>

<?php include ('../footer.php'); ?>
