<?php
include ('../include.php');
error_reporting(0);

//echo $_SERVER[REQUEST_URI];

if (isset($_POST['tasklist'])) {
    $created_by = $_SESSION['contactid'];
    $projectid = $_POST['projectid'];
    $security = filter_var($_POST['security'],FILTER_SANITIZE_STRING);
    $checklist_type = filter_var($_POST['checklist_type'],FILTER_SANITIZE_STRING);
    $checklist_name = filter_var($_POST['checklist_name'],FILTER_SANITIZE_STRING);
    $assign_staff = ','.implode(',',$_POST['assign_staff']).',';

    if(empty($_POST['checklistid'])) {
        $query_insert_ca = "INSERT INTO `checklist` (`security`, `assign_staff`, `checklist_type`, `checklist_name`, `created_by`, `client_projectid`) VALUES ('$security', '$assign_staff', '$checklist_type', '$checklist_name', '$created_by', '$projectid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
        $checklistid = mysqli_insert_id($dbc);

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Added Checklist <b>'.$checklist_name.'</b> in '.$security.' : '.$checklist_type.' on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`) VALUES ('$report')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

    } else {
        $checklistid = $_POST['checklistid'];
        $query_update_vendor = "UPDATE `checklist` SET `security` = '$security', `assign_staff` = '$assign_staff', `checklist_type` = '$checklist_type', `checklist_name` = '$checklist_name', `created_by` = '$created_by', `client_projectid` = '$projectid' WHERE `checklistid` = '$checklistid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Updated Checklist <b>'.$checklist_name.'</b> in '.$security.' : '.$checklist_type.' on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`) VALUES ('$report')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
    }

    if(!empty($_POST['checklistid'])) {
        for($i = 0; $i < count($_POST['checklist_update']); $i++) {
            $checklist = $_POST['checklist_update'][$i];
            $checklistnameid = $_POST['checklistid_update'][$i];
            $query_update_vendor = "UPDATE `checklist_name` SET `checklist` = '$checklist' WHERE `checklistnameid` = '$checklistnameid'";
            $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        }

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Updated Checklist Items in <b>'.$checklist_name.'</b> on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`) VALUES ('$report')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
    }

    $item = 0;
    for($i = 0; $i < count($_POST['checklist']); $i++) {
        $checklist = $_POST['checklist'][$i];

        if(!empty($_POST['checklistid'])) {
            $priority = count($_POST['checklist_update'])+$i+1;
        } else {
            $priority = $i+1;
        }

        if($checklist != '') {
            $query_insert_client_doc = "INSERT INTO `checklist_name` (`checklistid`, `checklist`, `priority`) VALUES ('$checklistid', '$checklist', '$priority')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
            $item = 1;
        }
    }

    if($item == 1) {
        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Added Checklist Items in <b>'.$checklist_name.'</b> on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`) VALUES ('$report')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
    }

    if($security == 'My Checklist') {
        $url = 'review_project.php?type=checklist&projectid='.$projectid.'&category='.$checklist_type.'&checklistid='.$checklistid;
    } else {
        $url = 'review_project.php?type=company_checklist&projectid='.$projectid.'&category='.$checklist_type.'&checklistid='.$checklistid;
    }

    echo '<script type="text/javascript"> window.location.replace("'.$url.'"); </script>';
}
?>

<script type="text/javascript">
$(document).ready(function () {

	$('.delete_task').click(function(){
		var result = confirm("Are you sure you want to delete this task?");
		if (result) {
			$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "task_ajax_all.php?fill=delete_task&taskid=<?php echo $_GET['tasklistid']; ?>",
				dataType: "html",   //expect html to be returned
				success: function(response){
					alert('You have successfully deleted this task.');
					window.location.href = "add_tasklist.php";
				}
			});
		}
	});

    $('#add_row_doc').on( 'click', function () {
        var clone = $('.additional_doc').clone();
        clone.find('.form-control').val('');
		clone.find('.popover-examples').css('display', 'none');
		clone.find('#add_row_doc').css('display', 'none');
        clone.removeClass("additional_doc");
        $('#add_here_new_doc').append(clone);
        return false;
    });

    var security = $("#security").val();
    if(security == 'Company Checklist') {
        $('.assign_staff').show();
    } else {
        $('.assign_staff').hide();
    }

});

function changeSecurity(sel) {
    var stage = sel.value;
    if(stage == 'Company Checklist') {
        $('.assign_staff').show();
    } else {
        $('.assign_staff').hide();
    }
}
</script>

</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('client_projects');
?>
<div class="container">
	<div class="row">

	<h1>Add Checklist</h1>

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <?php
    $task_contactid = $_SESSION['contactid'];

    if(!empty($_GET['projectid'])) {
        $projectid = $_GET['projectid'];
    }

	echo '<div class="gap-top double-gap-bottom"><a href="review_project.php?type=checklist&projectid='.$projectid.'&category=ongoing" class="btn config-btn">Back to Dashboard</a></div>';

    if(!empty($_GET['checklistid'])) {
        $checklistid = $_GET['checklistid'];
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM checklist WHERE checklistid='$checklistid'"));

        $security = $get_contact['security'];
        $assign_staff = $get_contact['assign_staff'];
        $checklist_type = $get_contact['checklist_type'];
        $checklist_name = $get_contact['checklist_name'];
        $projectid = $get_contact['projectid'];

        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM checklist_name WHERE checklistid='$checklistid'"));

        echo '<input type="hidden" name="checklistid" value="'.$_GET['checklistid'].'" />';
        echo '<input type="hidden" id="security" value="'.$security.'" />';
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT task FROM field_config"));
    $value_config = ','.$get_field_config['task'].',';
    ?>

    <div class="form-group">
      <label for="site_name" class="col-sm-4 control-label">Project Name:</label>
      <div class="col-sm-8">
        <select data-placeholder="Choose a Project..." name="projectid" id="projectid"  class="chosen-select-deselect form-control" width="380">
          <option value=""></option>
          <?php $query = mysqli_query($dbc,"SELECT projectid, project_name FROM client_project WHERE  deleted=0 ORDER BY project_name");
            while($row = mysqli_fetch_array($query)) {
				echo "<option ".($projectid == $row['projectid'] ? 'selected' : '')." value='".$row['projectid']."'>".$row['project_name'].'</option>';
            }
          ?>
        </select>
      </div>
    </div>

    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose who can view this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Security:
		</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Security..." onChange='changeSecurity(this)' name="security" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <option <?php if($security == 'My Checklist') { echo "selected"; } ?> value="My Checklist">My Checklist</option>
              <option <?php if($security == 'Company Checklist') { echo "selected"; } ?> value="Company Checklist">Company Checklist</option>
            </select>
        </div>
    </div>

    <div class="form-group clearfix assign_staff">
        <label for="first_name" class="col-sm-4 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to assign specific staff members. They will be able to view and edit this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Assign to Staff:
		</label>
        <div class="col-sm-8">
            <select name="assign_staff[]" multiple data-placeholder="Choose an Option..." class="chosen-select-deselect form-control" width="380">
                <?php
					$cat = '';
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Staff' AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
					foreach($query as $id) {
						$selected = '';
						$selected = strpos($assign_staff, ','.$id.',') !== false ? 'selected = "selected"' : '';
						echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
					}
				  ?>
            </select>
        </div>
    </div>

    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Choose from these options in order to select Checklist type."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Type:
		</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Type..." name="checklist_type" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <option <?php if($checklist_type == 'ongoing') { echo "selected"; } ?> value="ongoing">Ongoing</option>
              <option <?php if($checklist_type == 'daily') { echo "selected"; } ?> value="daily">Daily</option>
              <option <?php if($checklist_type == 'weekly') { echo "selected"; } ?> value="weekly">Weekly</option>
              <option <?php if($checklist_type == 'monthly') { echo "selected"; } ?> value="monthly">Monthly</option>
            </select>
        </div>
    </div>

    <?php //if (strpos($value_config, ','."Heading".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="This will be the title of the Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Name:
		</label>
        <div class="col-sm-8">
            <input type="text" name="checklist_name" value="<?php echo $checklist_name; ?>" class="form-control" width="380" />
        </div>
    </div>
    <?php// } ?>

    <div class="form-group">
        <label for="additional_note" class="col-sm-4 control-label">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add the content of the Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Checklist:
        </label>
        <div class="col-sm-8">
            <div class="form-group clearfix">
                <div class="col-sm-8">
                        <?php
                        if(!empty($_GET['checklistid'])) {
                            $query_check_credentials = "SELECT * FROM checklist_name WHERE checklistid='$checklistid' ORDER BY checklistnameid";
                            $result = mysqli_query($dbc, $query_check_credentials);
                            $num_rows = mysqli_num_rows($result);
                            if($num_rows > 0) {
                                while($row = mysqli_fetch_array($result)) {
                                    $checked = '';
                                    if($row['checked'] == 1) {
                                        $checked = ' checked';
                                    }
                                    echo '<input disabled type="checkbox" '.$checked.' value="" style="" ><input type="text" name="checklist_update[]" class="form-control" value= "'.$row['checklist'].'" style="width: 80%; display:inline;" width="380" /><a class="btn brand-btn" href=\'../delete_restore.php?action=delete&checklistnameid='.$row['checklistnameid'].'&checklistid='.$row['checklistid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a><br>';
                                    //echo '<td data-title="Schedule"><a href=\'delete_restore.php?action=delete&ticketdocid='.$row['ticketdocid'].'&ticketid='.$row['ticketid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a></td>';

                                    echo '<input type="hidden" name="checklistid_update[]" value="'.$row['checklistnameid'].'" />';

                                }
                            }
                        }
                        ?>
                </div>
            </div>
            <div class="enter_cost additional_doc clearfix">
                <div class="clearfix"></div>

                <div class="form-group clearfix">
                    <div class="col-sm-8">
                        <input type="checkbox" value="Bids" style="" >
                        <input type="text" name="checklist[]" class="form-control" style="width: 95%; display:inline;" width="380" />
                    </div>
					<div class="col-sm-4">
						<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a field."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						<button id="add_row_doc" class="btn brand-btn">Add</button>
					</div>
                </div>

            </div>

            <div id="add_here_new_doc"></div>

            <!--
			<div class="form-group triple-gapped clearfix">
                <div class="col-sm-offset-4 col-sm-8">
                    <button id="add_row_doc" class="btn brand-btn pull-left">Add</button>
                </div>
            </div>
			-->
        </div>
    </div>

    <div class="form-group clearfix">
        <div class="col-sm-6">
			<!--<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="If you click this, the current Checklist will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			 <a href="my_checklist.php?type=ongoing&checklistid=<?php echo $checklistid; ?>" class="btn brand-btn btn-lg">Back</a> -->
		</div>
		<div class="col-sm-6">
			<button name="tasklist" value="tasklist" class="btn brand-btn btn-lg pull-right">Submit</button>
			<span class="popover-examples list-inline pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save the Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        </div>
    </div>

    </form>

</div>
</div>
<?php include ('../footer.php'); ?>
