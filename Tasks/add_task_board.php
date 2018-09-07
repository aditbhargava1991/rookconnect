<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('tasks');
error_reporting(0);

if (isset($_POST['add_tab'])) {
    $board_name = filter_var($_POST['board_name'],FILTER_SANITIZE_STRING);
    $board_security = filter_var($_POST['board_security'],FILTER_SANITIZE_STRING);
    if($board_security == 'Private') {
        $company_staff_sharing = ','.$_SESSION['contactid'].',';
    } else {
	    $company_staff_sharing = ','.implode(',',$_POST['company_staff_sharing']).',';
    }
    $businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
	$contactid = implode(',',$_POST['contactid']);
    $task_path = filter_var($_POST['task_path'],FILTER_SANITIZE_STRING);
    $milestone_timeline = filter_var($_POST['milestone_timeline'],FILTER_SANITIZE_STRING);
    $software_url = filter_var($_POST['software_url'],FILTER_SANITIZE_STRING);

    if($board_security == 'Community') {
        $ffm_rook_db = @mysqli_connect('localhost', 'ffm_rook_user', 'mIghtyLion!542', 'ffm_rook_db');

        if(empty($_POST['taskboardid'])) {
            $query_insert_config = "INSERT INTO `task_board` (`board_name`, `board_security`, `company_staff_sharing`, `businessid`, `contactid`, `task_path`, `milestone_timeline`, `software_url`) VALUES ('$board_name', '$board_security', '$company_staff_sharing', '$businessid', '$contactid', '$task_path', '$milestone_timeline', '$software_url')";
            $result_insert_config = mysqli_query($ffm_rook_db, $query_insert_config);
        } else {
            $taskboardid = $_POST['taskboardid'];
            $query_update_vendor = "UPDATE `task_board` SET `board_name` = '$board_name', `board_security` = '$board_security',`company_staff_sharing` = '$company_staff_sharing', `businessid` = '$businessid', `contactid` = '$contactid', `task_path` = '$task_path', `milestone_timeline` = '$milestone_timeline', `software_url` = '$software_url' WHERE `taskboardid` = '$taskboardid'";
            $result_update_vendor = mysqli_query($ffm_rook_db, $query_update_vendor);
        }
    } else {
        if($board_name != '') {
            if(empty($_POST['taskboardid'])) {
                $query_insert_config = "INSERT INTO `task_board` (`board_name`, `board_security`, `company_staff_sharing`, `businessid`, `contactid`, `task_path`, `milestone_timeline`, `software_url`) VALUES ('$board_name', '$board_security', '$company_staff_sharing', '$businessid', '$contactid', '$task_path', '$milestone_timeline', '$software_url')";
                $result_insert_config = mysqli_query($dbc, $query_insert_config);
            } else {
                $taskboardid = $_POST['taskboardid'];
                $query_update_vendor = "UPDATE `task_board` SET `board_name` = '$board_name', `board_security` = '$board_security',`company_staff_sharing` = '$company_staff_sharing', `businessid` = '$businessid', `contactid` = '$contactid', `task_path` = '$task_path', `milestone_timeline` = '$milestone_timeline', `software_url` = '$software_url' WHERE `taskboardid` = '$taskboardid'";
                $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
            }
        }
    }
	foreach($_POST['path_id'] as $key => $path_id) {
		$name = $_POST['path_name'];
		$milestone_list = [];
		$timeline_list = [];
		$count = count($_POST['milestone_'.$path_id]);
		for($i = 0; $i < $count; $i++) {
			if($_POST['milestone_'.$path_id][$i] != '' || $_POST['timeline_'.$path_id][$i] != '') {
				$milestone_list[] = $_POST['milestone_'.$path_id][$i];
				$timeline_list[] = $_POST['timeline_'.$path_id][$i];
			}
		}
		$milestones = implode('#*#',$milestone_list);
		$timelines = implode('#*#',$timeline_list);
		if($name != '' && $milestones != '' && $timelines != '') {
			$query = "UPDATE `project_path_milestone` SET `milestone`='$milestones', `timeline`='$timelines' WHERE `project_path_milestone`='$path_id'";
			if($path_id == '') {
				$query = "INSERT INTO `project_path_milestone` (`project_path`, `milestone`, `timeline`) VALUES ('$name', '$milestones', '$timelines')";
			}
			mysqli_query($dbc, $query);
		}
	}

    echo '<script type="text/javascript"> window.location.replace("add_task_board.php"); </script>';
}
else if(isset($_GET['deleteid']) && $_GET['deleteid'] != '') {
	$id = $_GET['deleteid'];
	    $date_of_archival = date('Y-m-d');
$query = "UPDATE `task_board` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `taskboardid`='$id'";
	mysqli_query($dbc, $query);
	echo "<script>alert('Task board deleted successfully!');</script>";
}
?>
</head>
<body>

<script type="text/javascript">
$(document).ready(function() {

	$("#project_path").change(function() {
		var project_path = $("#project_path").val();
		$.ajax({
			type: "GET",
			url: "task_ajax_all.php?fill=project_path_milestone&project_path="+project_path,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#milestone_timeline').html(response);
				$("#milestone_timeline").trigger("change.select2");
			}
		});
	});

    var taskboard = $("#taskboard").val();
    if(taskboard == 'Private') {
            $( "#businessid_show" ).hide();
            $( "#contactid_show" ).hide();
            $( "#company_staff_sharing" ).hide();
    } else if(taskboard == 'Company') {
            $( "#company_staff_sharing" ).show();
            $( "#businessid_show" ).hide();
            $( "#contactid_show" ).hide();
    } else if(taskboard == 'Community') {
            $( "#businessid_show" ).show();
            $( "#contactid_show" ).show();
            $( "#company_staff_sharing" ).hide();
    }

    $("#board_security").change(function() {
        if($("#board_security option:selected").text() == 'Company') {
            $( "#company_staff_sharing" ).show();
            $( "#businessid_show" ).hide();
            $( "#contactid_show" ).hide();
        } else if($("#board_security option:selected").text() == 'Community') {
            $( "#businessid_show" ).show();
            $( "#contactid_show" ).show();
            $( "#company_staff_sharing" ).hide();
        } else {
            $( "#businessid_show" ).hide();
            $( "#contactid_show" ).hide();
            $( "#company_staff_sharing" ).hide();
        }
    });
});
$(document).on('change', 'select[name="path_name"]', function() { changeLevel(this); });
function deleteLine(btn) {
	$(btn).parents('[name=path_line]').remove();
}
function addLine(src) {
	var clone=$('[name='+src+']').clone();
	clone.css('display','');
	clone.attr('name','path_line');
	$('[name='+src+']').before(clone);
	clone.find('[name^=milestone]').focus();
}
function changeLevel(sel) {
    var security_level = sel.value;
    //alert(security_level);
    $(".all_path").hide();
    $("#path_"+security_level).show();
}
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
	<div class="row">
<?php
$task_tab_back = explode(",",get_config($dbc, 'task_tab'));
?>
<h1>My Tasks</h1>
<div class="gap-top double-gap-bottom"><a href="tasks.php?category=All" class="btn config-btn">Back to Dashboard</a></div>
<?php //include ('field_config_project_manage.php'); ?>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
<div class="panel-group" id="accordion2">
    <?php
    $board_name = '';
    $board_security = '';
    $company_staff_sharing = '';
    $businessid = '';
    $contactid = '';
    $task_path = '';
    $milestone_timeline = '';
    $software_url = '';
    if(!empty($_GET['security'])) {
        $board_security = $_GET['security'];
    }

    if(!empty($_GET['taskboardid'])) {
        $taskboardid = $_GET['taskboardid'];
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM task_board WHERE taskboardid='$taskboardid' AND `deleted`=0"));

        $board_name = $get_contact['board_name'];
        $board_security = $get_contact['board_security'];
        $company_staff_sharing = $get_contact['company_staff_sharing'];
        $businessid = $get_contact['businessid'];
        $contactid = $get_contact['contactid'];
        $task_path = $get_contact['task_path'];
        $milestone_timeline = $get_contact['milestone_timeline'];
        $software_url = $get_contact['software_url'];
    ?>
    <input type="hidden" id="taskboard" name="taskboard" value="<?php echo $board_security ?>" />
    <input type="hidden" id="taskboardid" name="taskboardid" value="<?php echo $taskboardid ?>" />
    <?php   }      ?>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse3" >
                    How To<span class="glyphicon glyphicon-minus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse3" class="panel-collapse collapse">
            <div class="panel-body">

            <b>Step 1:</b><br>
            Add your own Task Board. You will want to name it something short and easy to find because once you have many boards, it will get harder to distinguish them from one another. <br><br>

            <b>Step 2:</b> <br>
            Choose your Board category (from the categories you set in the task settings). <br><br>

            <b>Step 3:</b><br>
            Choose Board Security (who will be able to view the board). Please select the staff members you would like to assign to this Task Board. <br><br>

            <b>Step 4:</b><br>
            Click Submit to finalize your changes. If you click Back, it will not save your changes.

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add your own task board."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_1" >
                    Add Task Board<span class="glyphicon glyphicon-minus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_1" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="form-group">
                    <label for="fax_number"	class="col-sm-4	control-label">Board Name:</label>
                    <div class="col-sm-8">
                      <input name="board_name" value="<?php echo $board_name; ?>" type="text" class="form-control"/>
                    </div>
                </div>

                <div class="form-group">
                    <label for="fax_number"	class="col-sm-4	control-label">Board Type:</label>
                    <div class="col-sm-8">
                        <select name="board_security" id="board_security" data-placeholder="Choose a Security..." class="chosen-select-deselect form-control" width="380">
                          <option value=""></option>
                          <option <?php if ($board_security == 'Private') { echo  'selected="selected"'; } ?> value="Private">Private</option>
                          <option <?php if ($board_security == 'Company') { echo  'selected="selected"'; } ?> value="Company">Company</option>
                          <option <?php if ($board_security == 'Community') { echo  'selected="selected"'; } ?> value="Community">Rook Community</option>
                        </select>
                    </div>
                </div>

                <?php
                $display = '';
                if(!empty($_GET['security'])) {
                    if($_GET['security'] == 'Private') {
                        $display = 'display: none;';
                    }
                }
                ?>
                <div class="form-group" id="company_staff_sharing" style="<?php echo $display; ?>">
                    <label for="fax_number"	class="col-sm-4	control-label">Board Security:</label>
                    <div class="col-sm-8">
                        <select multiple name="company_staff_sharing[]" data-placeholder="Choose a User..." class="chosen-select-deselect form-control" width="380">
                          <?php
                            $query1 = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." order by first_name");
                            while($row1 = mysqli_fetch_array($query1)) {
                                ?>
                                <option <?php if (strpos(','.$company_staff_sharing.',', ','.$row1['contactid'].',') !== FALSE) { echo  'selected="selected"'; } ?> value='<?php echo $row1['contactid']; ?>' ><?php echo decryptIt($row1['first_name']).' '.decryptIt($row1['last_name']); ?></option>
                            <?php }
                          ?>
                        </select>
                    </div>
                </div>

                <!--
                <div class="form-group" id="businessid_show" style="display: none;">
                    <label for="first_name" class="col-sm-4 control-label text-right">Business:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Choose a Business..." name="businessid" id="businessid" class="chosen-select-deselect form-control1" width="380">
                          <option value=""></option>
                          <?php
                            $query = mysqli_query($dbc,"SELECT name, contactid FROM contacts WHERE name != '' AND deleted=0 ORDER BY name");
                            echo "<option value = 'New Business'>New Business</option>";
                            while($row = mysqli_fetch_array($query)) {
                                if ($businessid == $row['contactid']) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
                            }
                          ?>
                        </select>
                    </div>
                </div>

                <div class="form-group" id="contactid_show" style="display: none;">
                    <label for="first_name" class="col-sm-4 control-label text-right">Contact:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Choose a Client..." multiple id="contactid" name="contactid[]" class="chosen-select-deselect form-control1" width="380">
                          <option value=""></option>
                          <?php
                            $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE businessid = '$businessid' order by first_name");
                            while($row = mysqli_fetch_array($query)) {
                                if ($contactid == $row['contactid']) {
                                    $selected = 'selected="selected"';
                                } else {
                                    $selected = '';
                                }
                                echo "<option ".$selected." value='".$row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
                            }
                          ?>
                        </select>
                    </div>
                </div>
                -->

                <div class="form-group" id="contactid_show" style="display: none;">
                    <label for="first_name" class="col-sm-4 control-label text-right">Business:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Choose a Client..." name="software_url" class="chosen-select-deselect form-control1" width="380">
                          <option value=""></option>

                              <option <?php if (strpos($software_url, 'ffm.rookconnect.com') !== FALSE) { echo  'selected="selected"'; } ?> value='ffm.rookconnect.com' >ffm.rookconnect.com</option>
                              <option <?php if (strpos($software_url, 'zendemo.rookconnect.com') !== FALSE) { echo  'selected="selected"'; } ?> value='zendemo.rookconnect.com' >zendemo.rookconnect.com</option>
                              <option <?php if (strpos($software_url, 'zenearthcorp.rookconnect.com') !== FALSE) { echo  'selected="selected"'; } ?> value='zenearthcorp.rookconnect.com' >zenearthcorp.rookconnect.com</option>
                              <option <?php if (strpos($software_url, 'greenearthenergysolutions.rookconnect.com') !== FALSE) { echo  'selected="selected"'; } ?> value='greenearthenergysolutions.rookconnect.com' >greenearthenergysolutions.rookconnect.com</option>
                              <option <?php if (strpos($software_url, 'greenlifecan.rookconnect.com') !== FALSE) { echo  'selected="selected"'; } ?> value='greenlifecan.rookconnect.com' >greenlifecan.rookconnect.com</option>
                              <option <?php if (strpos($software_url, 'demo.rookconnect.com') !== FALSE) { echo  'selected="selected"'; } ?> value='demo.rookconnect.com' >demo.rookconnect.com</option>
                              <option <?php if (strpos($software_url, 'localhost') !== FALSE) { echo  'selected="selected"'; } ?> value='localhost' >localhost</option>

                        </select>
                    </div>
                </div>

                <!--
                <div class="form-group">
                  <label for="site_name" class="col-sm-4 control-label">Task Path:</label>
                  <div class="col-sm-8">
                      <select data-placeholder="Add Task Board Path" id="project_path" name="task_path" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
                      <option value=""></option>
                      <?php
                        $query = mysqli_query($dbc,"SELECT project_path_milestone, project_path FROM project_path_milestone order by project_path");
                        while($row = mysqli_fetch_array($query)) {
                        ?><option <?php if ($row['project_path_milestone'] == $task_path) { echo " selected"; } ?> value='<?php echo  $row['project_path_milestone']; ?>' ><?php echo $row['project_path']; ?></option>
                    <?php	} ?>
                    </select>
                  </div>
                </div>
                -->

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_paths" >
                    Add or Edit Task Paths<span class="glyphicon glyphicon-minus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_paths" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="form-group">
                  <label for="site_name" class="col-sm-4 control-label">Path Name:</label>
                  <div class="col-sm-8">
                      <select data-placeholder="Select to View" name="path_name" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
                      <option value=""></option>
                      <?php
                        $query = mysqli_query($dbc,"SELECT project_path_milestone, project_path FROM project_path_milestone order by project_path");
                        while($row = mysqli_fetch_array($query)) {
                        ?><option <?php if ($row['project_path_milestone'] == $task_path) { echo " selected"; } ?> value='<?php echo  $row['project_path_milestone']; ?>' ><?php echo $row['project_path']; ?></option>
                    <?php	} ?>
                    </select>
                  </div>
                </div>

				<?php $query = "SELECT * FROM project_path_milestone";
				$results = mysqli_query($dbc, $query);
				while($row = mysqli_fetch_array($results)) {
					$path_name = $row['project_path'];
					$milestones = explode('#*#',$row['milestone']);
					$timelines = explode('#*#',$row['timeline']);
					$count = count($milestones);
					$path_id = $row['project_path_milestone'];
					?>

                    <input type='hidden' name='path_id[]' value='<?php echo $path_id; ?>'>
                    <!--
					<div class='form-group'><label class='col-sm-4 control-label'><a href="#" onclick="$('[name=path_<?php echo $path_id; ?>]').toggle(); return false;" data-toggle="tooltip" data-placement="top" title="Click here to edit this path.">
						<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></span>
						Path Name:</a></label>
						<div class='col-sm-8'><input name='path_name[]' type='text' class='form-control' value='<?php echo $path_name; ?>'></div>
					</div>
                    -->
					<div name="path_<?php echo $path_id; ?>" id="path_<?php echo $path_id; ?>"  style="display:none;" class="all_path">
						<div class="form-group clearfix">
							<label class="col-sm-3 text-center">Milestone</label>
							<label class="col-sm-5 text-center">Timeline</label>
						</div>
						<?php for($i = 0; $i < $count; $i++) {
							if($milestones[$i] != '' || $timelines[$i] != '') { ?>
								<div class="form-group clearfix" name="path_line">
									<div class="col-sm-3">
										<input name="milestone_<?php echo $path_id; ?>[]" id="milestone_<?php echo $path_id; ?>" value = "<?php echo $milestones[$i]; ?>" type="text" class="form-control milestone">
									</div>
									<div class="col-sm-5">
										<input name="timeline_<?php echo $path_id; ?>[]" value = "<?php echo $timelines[$i]; ?>" type="text" class="form-control">
									</div>
									<div class="col-sm-1 m-top-mbl" >
										<a href="#" onclick="deleteLine(this); return false;"class="btn brand-btn">Delete</a>
									</div>
								</div>

							<?php } ?>
						<?php } ?>
						<div class="form-group clearfix" name="add_path_<?php echo $path_id; ?>" style="display:none;">
							<div class="col-sm-3">
								<input name="milestone_<?php echo $path_id; ?>[]" id="milestone_<?php echo $path_id; ?>" value = "" type="text" class="form-control milestone">
							</div>
							<div class="col-sm-5">
								<input name="timeline_<?php echo $path_id; ?>[]" value = "" type="text" class="form-control">
							</div>
							<div class="col-sm-1 m-top-mbl" >
								<a href="#" onclick="deleteLine(this); return false;"class="btn brand-btn">Delete</a>
							</div>
						</div>
						<button class="btn brand-btn" onclick="addLine('add_path_<?php echo $path_id; ?>');return false;">Add Milestone</button>
					</div>
				<?php } ?>
				<div name="new_path" style="display:none;">
					<input type='hidden' name='path_id[]' value=''>
					<div class='form-group'>
						<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to edit this path."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
						<label class='col-sm-4 control-label'><a href="#" onclick="$('[name=path_]').toggle(); return false;">Path Name:</a></label>
						<div class='col-sm-8'><input name='path_name' type='text' class='form-control' value=''></div>
					</div>
					<div name="path_">
						<div class="form-group clearfix">
							<label class="col-sm-3 text-center">Milestone</label>
							<label class="col-sm-5 text-center">Timeline</label>
						</div>
						<div class="form-group clearfix" name="add_path_" style="display:none;">
							<div class="col-sm-3">
								<input name="milestone_[]" id="milestone_" value = "" type="text" class="form-control milestone">
							</div>
							<div class="col-sm-5">
								<input name="timeline_[]" value = "" type="text" class="form-control">
							</div>
							<div class="col-sm-1 m-top-mbl" >
								<a href="#" onclick="deleteLine(this); return false;"class="btn brand-btn">Delete</a>
							</div>
						</div>
						<button class="btn brand-btn" onclick="addLine('add_path_');return false;">Add Milestone</button>
					</div>
				</div>
				<button class="btn brand-btn pull-right" onclick="$('[name=new_path]').show();addLine('add_path_');$(this).hide();return false;">Add New Path</button>
				<button class="btn brand-btn pull-right" onclick="$('[name=path_name]').change();$(this).hide();return false;">Edit Current Path</button>
			</div>
		</div>
	</div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <span class="popover-examples" style="margin:15px 0 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to view/edit all Task Boards."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_2" >
                    View All Task Boards<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_2" class="panel-collapse collapse">
            <div class="panel-body">

                <?php
                $query_check_credentials = "SELECT * FROM task_board WHERE `deleted`=0";
                $result = mysqli_query($dbc, $query_check_credentials);
                echo "<table class='table table-bordered'>";
                echo "<tr class='hidden-xs hidden-sm'>";
                echo '<th>Board Name</th>
                    <th>Board Security</th>
                    <th>Task Path</th>';
                    echo '<th>Function</th>';
                    echo "</tr>";

                while($row = mysqli_fetch_array( $result )) {
                    echo "<tr>";
                    echo '<td data-title="Code">' . $row['board_name'] . '</td>';
                    echo '<td data-title="Code">' . $row['board_security'] . '</td>';
                    echo '<td data-title="Code">' . get_project_path_milestone($dbc, $row['task_path'], 'project_path'). '</td>';

                    echo '<td data-title="Function">';
                    echo '<a href=\'add_task_board.php?taskboardid='.$row['taskboardid'].'\'>Edit</a> | ';
					echo '<a href="add_task_board.php?deleteid='.$row['taskboardid'].'" onclick="return confirm(\'Are you sure you want to delete this task board?\');">Delete</a>';
                    echo '</td>';

                    echo "</tr>";
                }

                echo '</table></div>';
            ?>

            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-6 clearfix">
			<span class="popover-examples" style="margin:15px 0 0 0;"><a data-toggle="tooltip" data-placement="top" title="If you click this, your current Task Board will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="tasks.php?category=All" class="btn config-btn btn-lg">Back</a>
        </div>
        <div class="col-sm-6">
            <button	type="submit" name="add_tab" value="add_tab" class="btn config-btn btn-lg pull-right">Submit</button>
			<span class="popover-examples pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save your Task Board."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        </div>
    </div>
</div>
</form>
</div>
</div>

<?php include ('../footer.php'); ?>
