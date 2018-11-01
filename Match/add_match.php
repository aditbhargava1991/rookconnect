<?php
/*
Add Vendor
*/
include ('../include.php');
error_reporting(0);

if(!empty($_GET['meduploadid'])) {
    $meduploadid = $_GET['meduploadid'];
    $query = mysqli_query($dbc,"DELETE FROM medication_uploads WHERE meduploadid='$meduploadid'");
    $matchid = $_GET['matchid'];

    echo '<script type="text/javascript"> window.location.replace("add_medication.php?matchid='.$matchid.'"); </script>';
}

if (isset($_POST['add_medication'])) {

    $support_contact_category = $_POST['support_contact_category'];
    $support_contact = implode(',', $_POST['support_contact']);
    $staff_contact_category = $_POST['staff_contact_category'];
    $staff_contact = implode(',', $_POST['staff_contact']);
    $match_date = $_POST['match_date'];
    $follow_up_date = $_POST['follow_up_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    if(empty($_POST['matchid'])) {
        $history = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Added on '.date('Y-m-d H:i:s').'<br>';

        $query_insert_vendor = "INSERT INTO `match_contact` (`support_contact_category`, `support_contact`, `staff_contact_category`, `staff_contact`, `match_date`, `follow_up_date`, `end_date`, `status`, `history`) VALUES ('$support_contact_category', '$support_contact', '$staff_contact_category', '$staff_contact', '$match_date', '$follow_up_date', '$end_date', '$status', '$history')";

        $result_insert_vendor = mysqli_query($dbc, $query_insert_vendor);
        $matchid = mysqli_insert_id($dbc);

        if($status == 'Archive') {
           $date_of_archival = date('Y-m-d');
         $query_update_vendor = "UPDATE `match_contact` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `matchid` = '$matchid'";
            $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        }

        $url = 'Added';
    } else {
        $matchid = $_POST['matchid'];
        $history = check_history($dbc, $matchid);
        $query_update_vendor = "UPDATE `match_contact` SET `support_contact_category` = '$support_contact_category', `support_contact` = '$support_contact', `staff_contact_category` = '$staff_contact_category', `staff_contact` = '$staff_contact', `match_date` = '$match_date', `follow_up_date` = '$follow_up_date', `end_date` = '$end_date', `status` = '$status', `history` = CONCAT(IFNULL(history, ''), '$history') WHERE `matchid` = '$matchid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);

        if($status == 'Archive') {
         $date_of_archival = date('Y-m-d');
           $query_update_vendor = "UPDATE `match_contact` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `matchid` = '$matchid'";
            $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        }
        $url = 'Updated';
    }

    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }

    echo '<script type="text/javascript"> window.location.replace("match.php"); </script>';

 //   mysqli_close($dbc);//Close the DB Connection
}

?>
<script type="text/javascript">
$(document).ready(function() {

    $("#isp_quality").change(function() {
        if($( "#isp_quality option:selected" ).text() == 'Other') {
                $( "#isp_quality_name" ).show();
        } else {
            $( "#isp_quality_name" ).hide();
        }
    });

    $("#isp_sis").change(function() {
        if($( "#isp_sis option:selected" ).text() == 'Other') {
                $( "#isp_sis_name" ).show();
        } else {
            $( "#isp_sis_name" ).hide();
        }
    });

    $("#isp_goals").change(function() {
        if($( "#isp_goals option:selected" ).text() == 'Other') {
                $( "#isp_goals_name" ).show();
        } else {
            $( "#isp_goals_name" ).hide();
        }
    });

    $("#form1").submit(function( event ) {
        var medication_type = $("#medication_type").val();
        var category = $("input[name=category]").val();
        var title = $("input[name=title]").val();
        if (medication_type == '' || category == '' || title == '' ) {
            alert("Please make sure you have filled in all of the required fields.");
            return false;
        }
    });
});

function selectContactCategory(sel) {
	var stage = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({
		type: "GET",
		url: "isp_ajax_all.php?fill=contact_category&category="+stage,
		dataType: "html",   //expect html to be returned
		success: function(response){
            $("#contact_"+arr[2]).html(response);
			$("#contact_"+arr[2]).trigger("change.select2");
		}
	});
}
</script>
</head>

<body>
<?php include_once ('../navigation.php');
checkAuthorised('match');
?>
<div class="container">
  <div class="row">

    <h1 class="triple-pad-bottom">Match</h1>

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
        $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT match_contact FROM field_config"));
        $value_config = ','.$get_field_config['match'].',';

        $support_contact_category = '';
        $support_contact = '';
        $staff_contact_category = '';
        $staff_contact = '';
        $match_date = '';
        $follow_up_date = '';
        $end_date = '';
        $status = '';

        $timer = '';
        $planned_activity = '';
        $day_obj = '';
        $completed_activity = '';
        $day_notes = '';
        $eme_contact = '';

        if($_GET['acc'] == 'match') {
            $acc_match = ' in';
        }
        if($_GET['acc'] == 'isp_detail') {
            $acc_isp_detail = ' in';
        }
        if($_GET['acc'] == 'isp_notes') {
            $acc_isp_notes = ' in';
        }

        if(!empty($_GET['matchid'])) {

            $matchid = $_GET['matchid'];
            $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM match_contact WHERE matchid='$matchid'"));

            $support_contact_category = $get_contact['support_contact_category'];
            $support_contact = explode(',', $get_contact['support_contact']);
            $staff_contact_category = $get_contact['staff_contact_category'];
            $staff_contact = explode(',', $get_contact['staff_contact']);
            $match_date = $get_contact['match_date'];
            $follow_up_date = $get_contact['follow_up_date'];
            $end_date = $get_contact['end_date'];
            $status = $get_contact['status'];
        ?>
        <input type="hidden" id="matchid" name="matchid" value="<?php echo $matchid ?>" />
        <?php   }      ?>

    <div class="panel-group" id="accordion2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse13" >
                        Staff<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse13" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    echo contact_category_call($dbc, 'contact_category_1', 'staff_contact_category', $staff_contact_category); ?>

                    <?php echo contact_call($dbc, 'contact_1', 'staff_contact[]', $staff_contact, 'multiple', $staff_contact_category); ?>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse1" >
                        Contact<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse1" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    echo contact_category_call($dbc, 'contact_category_0', 'support_contact_category', $support_contact_category); ?>

                    <?php echo contact_call($dbc, 'contact_0', 'support_contact[]', $support_contact, 'multiple', $support_contact_category); ?>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse2" >
                        Dates<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse2" class="panel-collapse collapse <?php echo $acc_match; ?>">
                <div class="panel-body">

                    <div class="form-group clearfix">
                        <label for="match_date" class="col-sm-4 control-label text-right">Match Start Date:</label>
                        <div class="col-sm-8">
                            <input name="match_date" value="<?php echo $match_date; ?>" type="text" class="datepicker">
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label for="follow_up_date" class="col-sm-4 control-label text-right">Match Follow Up Date:</label>
                        <div class="col-sm-8">
                            <input name="follow_up_date" value="<?php echo $follow_up_date; ?>" type="text" class="datepicker">
                        </div>
                    </div>

                    <div class="form-group clearfix">
                        <label for="end_date" class="col-sm-4 control-label text-right">Match End Date:</label>
                        <div class="col-sm-8">
                            <input name="end_date" value="<?php echo $end_date; ?>" type="text" class="datepicker">
                        </div>
                    </div>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse6" >
                        Status<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse6" class="panel-collapse collapse <?php echo $acc_isp_detail; ?>">
                <div class="panel-body">

                   <div class="form-group">
                    <label for="travel_task" class="col-sm-4 control-label">Status:</label>
                    <div class="col-sm-8">
                      <select id="status" name="status" class="chosen-select-deselect form-control" width="380">
                        <option value=''></option>
                        <option value='Suspend'>Suspend</option>
                        <option value='Active'>Active</option>
                        <option value='Archive'>Archive</option>
                      </select>
                    </div>
                  </div>

                </div>
            </div>
        </div>

    </div>

        <div class="form-group">
          <div class="col-sm-4">
              <p><span class="hp-red pull-right"><em>Required Fields *</em></span></p>
          </div>
          <div class="col-sm-8"></div>
        </div>

        <div class="form-group">
            <div class="col-sm-4 clearfix">
                <a href="match.php" class="btn brand-btn pull-right">Back</a>
            </div>
          <div class="col-sm-8">
            <button type="submit" name="add_medication" value="Submit" class="btn brand-btn btn-lg pull-right">Submit</button>
		  </div>
        </div>



    </form>

  </div>
</div>
<?php include ('../footer.php'); ?>

<?php
function contact_category_call($dbc, $select_id, $select_name, $contact_category_value) {
    ?>
    <script type="text/javascript">
    $(document).on('change', 'select[name="<?= $select_name ?>"]', function() { selectContactCategory(this); });
    </script>
    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">Contact Category:</label>
        <div class="col-sm-8">
            <select data-placeholder="Select Category..." id="<?php echo $select_id; ?>" name="<?php echo $select_name; ?>" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $tabs = get_config($dbc, 'contacts_tabs');
                $each_tab = explode(',', $tabs);
                foreach ($each_tab as $cat_tab) {
                    ?>
                    <option <?php if (strpos($contact_category_value, $cat_tab) !== FALSE) {
			        echo " selected"; } ?> value='<?php echo $cat_tab; ?>'><?php echo $cat_tab; ?></option>
                <?php }
              ?>
            </select>
        </div>
    </div>
<?php } ?>

<?php
function contact_call($dbc, $select_id, $select_name, $contact_value,$multiple, $from_contact) {
    ?>
    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">Contact:</label>
        <div class="col-sm-8">
            <select <?php echo $multiple; ?> data-placeholder="Select Contact..." name="<?php echo $select_name; ?>" id="<?php echo $select_id; ?>" class="chosen-select-deselect form-control" width="380">
              <?php if($contact_value != '') {

                $query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE category = '$from_contact' order by name");
                echo '<option value=""></option>';
                while($row = mysqli_fetch_array($query)) {
                    if($row['name'] != '') { ?>
                        <option <?php if (strpos($contact_value, $row['contactid']) !== FALSE) {
			            echo " selected"; } ?> value='<?php echo $row['contactid']; ?>'><?php echo decryptIt($row['name']); ?></option>
                    <?php } else { ?>
                        <option <?php if (strpos($contact_value, $row['contactid']) !== FALSE) {
			            echo " selected"; } ?> value='<?php echo $row['contactid']; ?>'><?php echo decryptIt($row['first_name']).' '.decyptIt($row['last_name']); ?></option>
                    <?php
                    }
                }
             } ?>
            </select>
        </div>
    </div>
<?php } ?>

<?php
function check_history($dbc, $matchid) {
    $match = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `match_contact` WHERE `matchid` = '$matchid'"));

    $support_contact_category = $_POST['support_contact_category'];
    $support_contact = implode(',', $_POST['support_contact']);
    $staff_contact_category = $_POST['staff_contact_category'];
    $staff_contact = implode(',', $_POST['staff_contact']);
    $match_date = $_POST['match_date'];
    $follow_up_date = $_POST['follow_up_date'];
    $end_date = $_POST['end_date'];
    $status = $_POST['status'];

    $history = '';

    if ($match['staff_contact_category'] != $staff_contact_category) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Staff Category on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['staff_contact'] != $staff_contact) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Staff on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['support_contact_category'] != $support_contact_category) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Contact Category on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['support_contact'] != $support_contact) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Contact on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['match_date'] != $match_date) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Match Start Date on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['follow_up_date'] != $follow_up_date) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Match Follow Up Date on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['end_date'] != $end_date) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Match End Date on '.date('Y-m-d H:i:s').'<br>';
    }
    if ($match['status'] != $status) {
        $history .= decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Changed Status on '.date('Y-m-d H:i:s').'<br>';
    }

    return $history;
}
?>