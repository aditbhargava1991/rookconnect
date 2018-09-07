<?php
include ('../include.php');
error_reporting(0);

//echo $_SERVER[REQUEST_URI];

if (isset($_POST['tasklist'])) {
	$project_history = '';
    $supportid = $_POST['supportid'];
    if($supportid != '') {
	    $query_update_project = "UPDATE `support` SET  status='Task' WHERE `supportid` = '$supportid'";
	    $result_update_project = mysqli_query($dbc, $query_update_project);
    }
	$created_date = date('Y-m-d');
    $task_businessid = $_POST['task_businessid'];
    $created_by = $_SESSION['contactid'];
    $ticketid = $_POST['ticketid'];
    $task_path = $_POST['task_path'];
    $task_board = $_POST['task_board'];
    $task_milestone_timeline = filter_var($_POST['task_milestone_timeline'],FILTER_SANITIZE_STRING);

    $task_heading = filter_var($_POST['task_heading'],FILTER_SANITIZE_STRING);
    $task = filter_var(htmlentities($_POST['task']),FILTER_SANITIZE_STRING);
    $task_clientid = $_POST['task_clientid'];
	$task_projectid = $_POST['task_projectid'];
	$task_client_projectid = '';
	if(substr($task_projectid,0,1) == 'C') {
		$task_client_projectid = substr($task_projectid,1);
		$task_projectid = '';
	}
    $task_contactid = $_POST['task_userid'];
    if($task_contactid == '') {
        $task_contactid = $_SESSION['contactid'];
    }
    $task_tododate = $_POST['task_tododate'];
    $task_status = $_POST['task_status'];
    if($task_status == '') {
        $task_status = 'To Do';
    }
    if($task_status == 'Archived') {
        $archived_date = date('Y-m-d');
    }
    $task_category = $_POST['task_category'];

    $task_work_time = $_POST['task_work_time'];

    $task_from_tasktile = $_POST['task_from_tasktile'];
    if(empty($_POST['tasklistid'])) {
        $query_insert_ca = "INSERT INTO `tasklist` (`ticketid`, `businessid`, `clientid`, `projectid`, `client_projectid`, `task`, `contactid`, `created_date`, `created_by`, `task_tododate`, `status`, `category`, `heading`, `work_time`, `task_path`, `task_board`, `task_milestone_timeline`) VALUES ('$ticketid', '$task_businessid', '$task_clientid', '$task_projectid', '$task_client_projectid', '$task', '$task_contactid', '$created_date', '$created_by', '$task_tododate', '$task_status', '$task_category', '$task_heading', '$task_work_time', '$task_path', '$task_board', '$task_milestone_timeline')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
		$tasklistid = mysqli_insert_id($dbc);

        if($task_category = 'Zen Earth Corp' || $task_category = 'Green Earth Energy' || $task_category = 'Green Life Can') {
            if (strpos(WEBSITE_URL, 'zenearthcorp.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenearthenergysolutions.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenlifecan.rookconnect.com') !== FALSE) {

                $zenearth_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'zenearth_rook_db');
                $gees_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'gees_rook_db');
                $glcllc_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'glcllc_rook_db');

                $result_insert_ca = mysqli_query($zenearth_rook_db, $query_insert_ca);
                $result_insert_ca = mysqli_query($gees_rook_db, $query_insert_ca);
                $result_insert_ca = mysqli_query($glcllc_rook_db, $query_insert_ca);
            }
        }
		$project_history .= ($project_history == '' ? '' : '<br />').get_contact($dbc, $_SESSION['contactid']).' added Task #'.$tasklistid.' for this project at '.date('Y-m-d H:i');
        insert_day_overview($dbc, $_SESSION['contactid'], 'Task', date('Y-m-d'), '', 'Created Task #'.$tasklistid.(!empty($task_heading) ? ': '.$task_heading : ''), $tasklistid);

    } else {
        $tasklistid = $_POST['tasklistid'];
        $query_update_vendor = "UPDATE `tasklist` SET `businessid` = '$task_businessid', `clientid` = '$task_clientid', `projectid` = '$task_projectid', `client_projectid` = '$task_client_projectid', `task` = '$task', `contactid` = '$task_contactid', `task_tododate` = '$task_tododate', `status` = '$task_status', `category` = '$task_category', `heading` = '$task_heading', `work_time` = '$task_work_time', `task_path` = '$task_path', `task_board` = '$task_board', `task_milestone_timeline` = '$task_milestone_timeline', `archived_date` = '$archived_date' WHERE `tasklistid` = '$tasklistid'";

        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);

        if($task_category = 'Zen Earth Corp' || $task_category = 'Green Earth Energy' || $task_category = 'Green Life Can') {

        if (strpos(WEBSITE_URL, 'zenearthcorp.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenearthenergysolutions.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenlifecan.rookconnect.com') !== FALSE) {
                $zenearth_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'zenearth_rook_db');
                $gees_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'gees_rook_db');
                $glcllc_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'glcllc_rook_db');

                $result_update_vendor = mysqli_query($zenearth_rook_db, $query_update_vendor);
                $result_update_vendor = mysqli_query($gees_rook_db, $query_update_vendor);
                $result_update_vendor = mysqli_query($glcllc_rook_db, $query_update_vendor);
            }
        }
		$project_history .= ($project_history == '' ? '' : '<br />').get_contact($dbc, $_SESSION['contactid']).' updated Task #'.$tasklistid.' for this project at '.date('Y-m-d H:i');
        insert_day_overview($dbc, $_SESSION['contactid'], 'Task', date('Y-m-d'), '', 'Updated Task #'.$tasklistid.(!empty($task_heading) ? ': '.$task_heading : ''), $tasklistid);
    }

    //Document
    if (!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
        $document = htmlspecialchars($_FILES["upload_document"]["name"][$i], ENT_QUOTES);

        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]) ;

        if($document != '') {
            $query_insert_client_doc = "INSERT INTO `task_document` (`tasklistid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$tasklistid', 'Support Document', '$document', '$created_date', '$created_by')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
        }
    }

    $url = $_POST['from'];

	// Save Project History
	if($task_projectid != '') {
		$user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
		mysqli_query($dbc, "INSERT INTO `project_history` (`updated_by`, `description`, `projectid`) VALUES ('$user', '".htmlentities($project_history)."', '$task_projectid')");
	}
	else if($task_client_projectid != '') {
		$project_history_result = mysqli_query($dbc, "UPDATE `client_project` SET `history`=CONCAT(IFNULL(CONCAT(`history`,'<br />'),''),'".htmlentities($project_history)."') WHERE `projectid` = '$task_client_projectid'");
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

	$("#task_path").change(function() {
		var task_path = $("#task_path").val();
		$.ajax({
			type: "GET",
			url: "task_ajax_all.php?fill=project_path_milestone&project_path="+task_path,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#task_milestone_timeline').html(response);
				$("#task_milestone_timeline").trigger("change.select2");
			}
		});
	});

    $('#add_row_doc').on( 'click', function () {
        var clone = $('.additional_doc').clone();
        clone.find('.form-control').val('');
        clone.removeClass("additional_doc");
        $('#add_here_new_doc').append(clone);
        return false;
    });
    $("[name=task_userid]").change(function() {
		var userid = this.value;
        $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "task_ajax_all.php?fill=filltaskboards&user="+userid,
            dataType: "html",   //expect html to be returned
            success: function(response){
				$('[name=task_board]').html(response).trigger("change.select2");
            }
        });
	});
    $("#task_businessid").change(function() {
		var businessid = this.value;
        $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "task_ajax_all.php?fill=fillcontact&businessid="+businessid,
            dataType: "html",   //expect html to be returned
            success: function(response){
                var arr = response.split('*FFM*');
				$('#checklist_clientid').html(arr[0]);
				$("#checklist_clientid").trigger("change.select2");

				$('#task_projectid').html(arr[1]);
				$("#task_projectid").trigger("change.select2");
            }
        });
	});

});
</script>

</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('tasks');
?>
<div class="container">
	<div class="row">

    <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <?php
	$back_url = WEBSITE_URL.'/Tasks/tasks.php?category=All';
	if(!empty($_GET['from'])) {
		$back_url = urldecode($_GET['from']);
	}
	echo "<input type='hidden' name='from' value='$back_url'>";
    if(!empty($_GET['supportid'])) {
        $supportid = $_GET['supportid'];
        $company_name = get_support($dbc, $supportid, 'company_name');
        $get_contact =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contactid FROM	contacts WHERE	name='$company_name'"));
        $task_businessid = $get_contact['contactid'];
        $task_heading = get_support($dbc, $supportid, 'heading');
        $task = html_entity_decode(get_support($dbc, $supportid, 'message'));
        $task_status = 'To Do';
        echo '<input type="hidden" name="supportid" value="'.$_GET['supportid'].'" />';
    }

    if(!empty($_GET['task_path'])) {
        $task_path = $_GET['task_path'];
    }

    if(!empty($_GET['task_milestone_timeline'])) {
        $task_milestone_timeline = $_GET['task_milestone_timeline'];
    }

    if(!empty($_GET['task_board'])) {
        $task_board = $_GET['task_board'];
    }

    $task_contactid = $_SESSION['contactid'];

    if(!empty($_GET['tasklistid'])) {
        $tasklistid = $_GET['tasklistid'];
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tasklist WHERE tasklistid='$tasklistid'"));

        $task_clientid = $get_contact['clientid'];
        $task = $get_contact['task'];
        $task_businessid = $get_contact['businessid'];
        $task_contactid = $get_contact['contactid'];
		$task_projectid = (empty($get_contact['projectid']) ? 'C'.$get_contact['client_projectid'] : $get_contact['projectid']);

        $task_heading = $get_contact['heading'];
        $task_work_time = date('H:i',strtotime($get_contact['work_time']));
        $task_category = $get_contact['category'];
        $task_status = $get_contact['status'];
        $task_tododate = $get_contact['task_tododate'];
        $task_path = $get_contact['task_path'];
        $task_board = $get_contact['task_board'];
        $task_milestone_timeline = $get_contact['task_milestone_timeline'];
        echo '<input type="hidden" name="tasklistid" value="'.$_GET['tasklistid'].'" />';
    } else if(!empty($_GET['projectid'])) {
		$task_projectid = $_GET['projectid'];
		$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT `businessid`, `clientid` FROM `project` WHERE `projectid`='$task_projectid'"));
		$task_businessid = $project['businessid'];
		$task_contactid = $project['clientid'];
	}

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT task FROM field_config"));
    $value_config = ','.$get_field_config['task'].',';
    ?>
	<h1><?= (!empty($_GET['tasklistid']) ? 'Edit' : 'Add a') ?> Task</h1>
	<a href="<?php echo $back_url; ?>" class="btn brand-btn pull-left">Back</a><br /><br />

    <?php // if (strpos($value_config, ','."Business".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Business:</label>
        <div class="col-sm-8">
            <select data-placeholder="Select a Business..." name="task_businessid" id="task_businessid" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $query = mysqli_query($dbc,"SELECT name, contactid FROM contacts WHERE category='Business' AND deleted=0 ORDER BY name");
                while($row = mysqli_fetch_array($query)) {
                    if ($task_businessid == $row['contactid']) {
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
    <?php //} ?>

    <?php //if (strpos($value_config, ','."Contact".',') !== FALSE) { ?>
    <?php if($task_clientid != '') { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Contact:</label>
        <div class="col-sm-8">
            <select data-placeholder="Select a Client..." id="checklist_clientid" name="task_clientid" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
			  <?php
				$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE businessid='$task_businessid'"),MYSQLI_ASSOC));
				foreach($query as $id) {
					$selected = '';
					$selected = $task_clientid == $id ? 'selected = "selected"' : '';
					echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
				}
			  ?>
            </select>
        </div>
    </div>
    <?php } else { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Contact:</label>
        <div class="col-sm-8">
            <select data-placeholder="Select a Client..." id="checklist_clientid" name="task_clientid" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
            </select>
        </div>
    </div>
    <?php } ?>
    <?php //} ?>

    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Attach Project:</label>
        <div class="col-sm-8">
            <select data-placeholder="Select a Project..." name="task_projectid" class="chosen-select-deselect form-control" id="task_projectid" width="380">
              <option></option>
              <?php
				$query = "SELECT * FROM (SELECT `projectid`, `project_name` FROM `project` WHERE ('$task_businessid'='' OR `businessid`='$task_businessid') AND `deleted`=0 UNION SELECT CONCAT('C',`projectid`), `project_name` FROM `client_project` WHERE (`clientid`='$taskbusinessid' OR '$task_businessid'='') AND `deleted`=0) PROJECTS ORDER BY `project_name`";
				echo "<!--$query-->";
                $query = mysqli_query($dbc,$query);
                while($row = mysqli_fetch_array($query)) {
                    if ($task_projectid == $row['projectid']) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $row['projectid']."'>".$row['project_name'].'</option>';
                }
              ?>
            </select>
        </div>
    </div>

    <?php //if (strpos($value_config, ','."Scrum Board".',') !== FALSE) { ?>
        <div class="form-group">
          <label for="site_name" class="col-sm-4 control-label">Assign to Staff:</label>
          <div class="col-sm-8">
				<select data-placeholder="Select a User" name="task_userid" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
				<option value=""></option>
				<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
				foreach($staff_list as $staff_id) { ?>
					<option <?= ($staff_id == $_SESSION['contactid'] ? "selected" : '') ?> value='<?=  $staff_id; ?>' ><?= get_contact($dbc, $staff_id) ?></option>
				<?php } ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="site_name" class="col-sm-4 control-label">Task Board:</label>
          <div class="col-sm-8">
              <select data-placeholder="Select a Task Board" name="task_board" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
				<option value=""></option>
				<?php $query = mysqli_query($dbc, "SELECT * FROM task_board WHERE company_staff_sharing LIKE '%," . $_SESSION['contactid'] . ",%'");
				while($row = mysqli_fetch_array($query)) { ?>
					<option <?php if ($row['taskboardid'] == $task_board) { echo " selected"; } ?> value='<?php echo  $row['taskboardid']; ?>' ><?php echo $row['board_name']; ?></option>
				<?php } ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="site_name" class="col-sm-4 control-label">Task Path:</label>
          <div class="col-sm-8">
              <select data-placeholder="Select a Task Path" id="task_path" name="task_path" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
              <option value=""></option>
              <?php
                $query = mysqli_query($dbc,"SELECT project_path_milestone, project_path FROM project_path_milestone");
                while($row = mysqli_fetch_array($query)) {
                ?><option <?php if ($row['project_path_milestone'] == $task_path) { echo " selected"; } ?> value='<?php echo  $row['project_path_milestone']; ?>' ><?php echo $row['project_path']; ?></option>
            <?php	} ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="site_name" class="col-sm-4 control-label">Milestone & Timeline:</label>
          <div class="col-sm-8">
            <select data-placeholder="Select an Option..." name="task_milestone_timeline" id="task_milestone_timeline"  class="chosen-select-deselect form-control" width="580">
                <option value=""></option>
                <?php
                $each_tab = explode('#*#', get_project_path_milestone($dbc, $task_path, 'milestone'));
                $timeline = explode('#*#', get_project_path_milestone($dbc, $task_path, 'timeline'));
                $j=0;
                foreach ($each_tab as $cat_tab) {
                    if($cat_tab != '') {
                        if ($task_milestone_timeline == $cat_tab) {
                            $selected = 'selected="selected"';
                        } else {
                            $selected = '';
                        }
                        echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.' : '.$timeline[$j].'</option>';
                    }
                    $j++;
                }
              ?>
            </select>
          </div>
        </div>

    <?php //} ?>

    <?php //if (strpos($value_config, ','."Heading".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Heading:</label>
        <div class="col-sm-8">
            <input type="text" name="task_heading" value="<?php echo $task_heading; ?>" class="form-control" width="380" />
        </div>
    </div>
    <?php// } ?>

    <?php// if (strpos($value_config, ','."Task".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Task:</label>
        <div class="col-sm-8">
            <textarea name="task" rows="3" cols="50" class="form-control"><?php echo html_entity_decode($task); ?></textarea>
        </div>
    </div>
    <?php// } ?>

    <?php
    if(!empty($_GET['tasklistid'])) {
        $query_check_credentials = "SELECT * FROM task_document WHERE tasklistid='$tasklistid' ORDER BY taskdocid DESC";
        $result = mysqli_query($dbc, $query_check_credentials);
        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            echo "<table class='table table-bordered'>
            <tr class='hidden-xs hidden-sm'>
            <th>Document</th>
            <th>Date</th>
            <th>Uploaded By</th>
            </tr>";
            while($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                $by = $row['created_by'];
                echo '<td data-title="Schedule"><a href="download/'.$row['document'].'" target="_blank">'.$row['document'].'</a></td>';
                echo '<td data-title="Schedule">'.$row['created_date'].'</td>';
                echo '<td data-title="Schedule">'.get_staff($dbc, $by).'</td>';
                //echo '<td data-title="Schedule"><a href=\'delete_restore.php?action=delete&ticketdocid='.$row['ticketdocid'].'&ticketid='.$row['ticketid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }
    ?>

    <div class="form-group">
        <label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
        </label>
        <div class="col-sm-8">

            <div class="enter_cost additional_doc clearfix">
                <div class="clearfix"></div>

                <div class="form-group clearfix">
                    <div class="col-sm-5">
                        <input name="upload_document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
                    </div>
                </div>

            </div>

            <div id="add_here_new_doc"></div>

            <div class="form-group triple-gapped clearfix">
                <div class="col-sm-offset-4 col-sm-8">
                    <button id="add_row_doc" class="btn brand-btn pull-left">Add Another Document</button>
                </div>
            </div>
        </div>
    </div>

    <?php //if (strpos($value_config, ','."To Do Date".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">To Do Date:</label>
        <div class="col-sm-8">
            <input name="task_tododate" value="<?php echo $task_tododate; ?>" type="text" class="datepicker form-control">
        </div>
    </div>
    <?php// } ?>

    <?php// if (strpos($value_config, ','."Work Time".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Work Time:</label>
        <div class="col-sm-8">
            <input name="task_work_time" value="<?php echo $task_work_time; ?>" type="text" value="00:00" class="timepicker form-control">
        </div>
    </div>
    <?php// } ?>

    <?php //if (strpos($value_config, ','."Status".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Status:</label>
        <div class="col-sm-8">
            <select data-placeholder="Select a Status..." name="task_status" class="chosen-select-deselect form-control" width="380">
				<option value=""></option>
				<?php $tabs = get_config($dbc, 'task_status');
                $each_tab = explode(',', $tabs);
                foreach ($each_tab as $cat_tab) {
                    if ($task_status == $cat_tab) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
                } ?>
            </select>
        </div>
    </div>
    <?php// } ?>
    <div class="form-group">
        <div class="col-sm-4 clearfix">
			<a href="<?php echo $back_url; ?>" class="btn brand-btn pull-left">Back</a>
        </div>
      <div class="col-sm-8">
        <button name="tasklist" value="tasklist" class="btn brand-btn pull-right">Submit</button>
		<?php if(!empty($_GET['tasklistid'])) { ?><button name="" type='button' value="" class="btn delete_task brand-btn pull-right">Delete Task</button><?php } ?>
      </div>
    </div>

    </form>

	<style>
		.ui-datepicker-current:empty {
			display:none;
		}
	</style>

</div>
</div>
<?php include ('../footer.php'); ?>
