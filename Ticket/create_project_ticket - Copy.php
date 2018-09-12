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
            $tempticketid = $_POST['tempticketid'][$i];
            $get_tt = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM temp_ticket WHERE tempticketid='$tempticketid'"));
            $clientid = $get_tt['clientid'];

            $projectid = $get_tt['projectid'];
            $service = $get_tt['category'];
            $service_type = $get_tt['service_type'];

            $contactid = ','.implode(',',$_POST['contactid_'.$tempticketid]).',';
            $heading = $_POST['heading_'.$tempticketid];

            $a_work = htmlentities($_POST['assignwork_'.$tempticketid]);
            $assign_work = filter_var($a_work,FILTER_SANITIZE_STRING);

            $query_insert_ca = "INSERT INTO `tickets` (`clientid`, `projectid`, `contactid`, `service`, `heading`, `created_date`, `created_by`, `assign_work`, `service_type`) VALUES ('$clientid', '$projectid', '$contactid', '$service', '$heading', '$created_date', '$created_by', '$assign_work', '$service_type')";
            $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
            $ticketid = mysqli_insert_id($dbc);

            $query_insert_ca = "INSERT INTO `ticket_deliverables` (`ticketid`, `status`, `contactid`, `created_date`, `created_by`) VALUES ('$ticketid', 'To Be Scheduled', '$contactid', '$created_date', '$created_by')";
            $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
        }
    }

    mysqli_query($dbc, "DELETE FROM temp_ticket WHERE projectid='$projectid'");
    echo '<script type="text/javascript"> window.location.replace("tickets.php"); </script>';
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
        $query_check_credentials = "SELECT r.*, c.name FROM temp_ticket r, contacts c WHERE r.clientid = c.contactid AND r.projectid = '$projectid' ORDER BY r.category";

        $result = mysqli_query($dbc, $query_check_credentials);
        $test = 0;
        $ms_loop = '';
        $col = 0;
        $index = 0;
        while($row = mysqli_fetch_array( $result )) {
            if($row['category'] != $ms_loop) {
                if(($test == 2) || ($test == 1)) {
                    echo '</div>
                        </div>
                        </div>';
                }
                echo '<div class="panel panel-default">

                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_'.$col.'" >
                                '.$row['category'].'<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                <div id="collapse_'.$col.'" class="panel-collapse collapse">
                    <div class="panel-body">';

                $ms_loop = $row['category'];
                $col++;
                $test = 1;
            } else {
                $test = 2;
            }
            ?>

            <div class="form-group">
                <div class="form-group">
                    <label for="first_name" class="col-sm-4 control-label">Create <?php echo AFTER_PROJECT; ?>:</label>
                    <div class="col-sm-8">
                        <input style="height: 30px; width: 30px;" type="checkbox" value="<?php echo $row['tempticketid']; ?>" name="tempticketid[]" id="nal">
                    </div>
                </div>

                <div class="form-group">
                    <label for="first_name" class="col-sm-4 control-label">Heading:</label>
                    <div class="col-sm-8">
                        <input name="heading_<?php echo $row['tempticketid']; ?>" type="text" value="<?php echo $heading; ?>" class="form-control">
                    </div>
                </div>

                 <div class="form-group">
                  <label for="site_name" class="col-sm-4 control-label">Assign To:</label>
                  <div class="col-sm-8">
                    <select data-placeholder="Choose a Staff Member..." multiple name="contactid_<?php echo $row['tempticketid']; ?>[]" class="chosen-select-deselect form-control" width="380">
						  <?php
							$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
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
                <?php include ('add_view_ticket_documents.php'); ?>
                <?php include ('add_view_ticket_deliverables.php'); ?>

            </div>

            <?php
            $index++;
            }
            if((count($tags) == 1) || ($test == 2) || ($test == 1)) {
                echo '</div>
                    </div>
                    </div>';
            }
        echo '</table>';
        ?>
        <input type="hidden" name="index" value="<?php echo $index; ?>">
        </div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 clearfix">
                <!--<a href="projects.php" class="btn brand-btn pull-right">Back</a>-->
				<a href="#" class="btn brand-btn pull-right" onclick="history.go(-1);return false;">Back</a>
            </div>
            <div class="col-sm-8">
                <button	type="submit" name="submit"	value="Submit" title="The entire form will submit and close if this submit button is pressed." class="btn brand-btn btn-lg	pull-right">Submit</button>
            </div>
        </div>



        </form>
        </div>

	</div>
</div>

<?php include_once('../footer.php'); ?>
