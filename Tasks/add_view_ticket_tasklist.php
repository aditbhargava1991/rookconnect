<?php
include ('../include.php');
error_reporting(0);

if (isset($_POST['tasklist'])) {
	$created_date = date('Y-m-d');
    $task_businessid = $_POST['task_businessid'];
    $created_by = $_SESSION['contactid'];
    $ticketid = $_POST['ticketid'];
    $task_heading = filter_var($_POST['task_heading'],FILTER_SANITIZE_STRING);
    $task = filter_var(htmlentities($_POST['task']),FILTER_SANITIZE_STRING);
    $task_clientid = $_POST['task_clientid'];
    $task_contactid = $_POST['task_contactid'];
    if($task_contactid == '') {
        $task_contactid = $_SESSION['contactid'];
    }
    $task_tododate = $_POST['task_tododate'];
    $task_status = $_POST['task_status'];
    if($task_status == '') {
        $task_status = 'Done';
    }
    $task_category = $_POST['task_category'];

    $task_work_time = $_POST['task_work_time'];

    $task_from_tasktile = $_POST['task_from_tasktile'];
    if($task_from_tasktile == 0 || $task_from_tasktile == -1) {
        $query_insert_ca = "INSERT INTO `tasklist` (`ticketid`, `businessid`, `clientid`, `task`, `contactid`, `created_date`, `created_by`, `task_tododate`, `status`, `category`, `heading`, `work_time`) VALUES ('$ticketid', '$task_businessid', '$task_clientid', '$task', '$task_contactid', '$created_date', '$created_by', '$task_tododate', '$task_status', '$task_category', '$task_heading', '$task_work_time')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

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

        if($task_from_tasktile == 0) {
        $url = 'index.php?edit='.$ticketid;
        echo '<script type="text/javascript"> window.location.replace("'.$url.'"); </script>';
        }
        if($task_from_tasktile == -1) {
            echo '<script type="text/javascript"> window.top.close(); window.opener.location.reload(); </script>';
        }
    } else {
        $tasklistid = $_POST['tasklistid'];
        $query_update_vendor = "UPDATE `tasklist` SET `businessid` = '$task_businessid', `clientid` = '$task_clientid', `task` = '$task', `contactid` = '$task_contactid', `task_tododate` = '$task_tododate', `status` = '$task_status', `category` = '$task_category', `heading` = '$task_heading', `work_time` = '$task_work_time' WHERE `tasklistid` = '$tasklistid'";
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

        echo '<script type="text/javascript"> window.top.close(); window.opener.location.reload(); </script>';
    }
}

?>
<script type="text/javascript" src="tasks.js"></script>

</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('tasks');
?>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <?php
    if(!empty($_GET['tasklistid'])) {
        $tasklistid = $_GET['tasklistid'];
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tasklist WHERE tasklistid='$tasklistid'"));

        $task_clientid = $get_contact['clientid'];
        $task = $get_contact['task'];
        $task_businessid = $get_contact['businessid'];
        $task_contactid = $get_contact['contactid'];

        $task_heading = $get_contact['heading'];
        $task_work_time = $get_contact['work_time'];
        $task_category = $get_contact['category'];
        $task_status = $get_contact['status'];
        $task_tododate = $get_contact['task_tododate'];
        echo '<input type="hidden" name="task_from_tasktile" value="1" />';
        echo '<input type="hidden" name="tasklistid" value="'.$_GET['tasklistid'].'" />';
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT task FROM field_config"));
    $value_config = ','.$get_field_config['task'].',';
    ?>

    <?php if (strpos($value_config, ','."Business".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Business:</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Business..." name="task_businessid" id="task_businessid" class="chosen-select-deselect form-control" width="380">
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
    <?php } ?>

    <?php if (strpos($value_config, ','."Contact".',') !== FALSE) { ?>
    <?php if($task_clientid != '') { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Contact:</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Client..." id="checklist_clientid" name="task_clientid" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $query = mysqli_query($dbc,"SELECT first_name,last_name, contactid FROM contacts WHERE businessid='$task_businessid' order by first_name");
                while($row = mysqli_fetch_array($query)) {
                    if ($task_clientid == $row['contactid']) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
                }
              ?>
            </select>
        </div>
    </div>
    <?php } else { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Contact:</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Client..." id="checklist_clientid" name="task_clientid" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
            </select>
        </div>
    </div>
    <?php } ?>
    <?php } ?>

    <?php if (strpos($value_config, ','."Scrum Board".',') !== FALSE) { ?>
        <div class="form-group">
          <label for="site_name" class="col-sm-4 control-label">Task Path:</label>
          <div class="col-sm-8">
              <select data-placeholder="Pick a Project Path" id="task_path" name="task_path" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
              <option value=""></option>
              <?php
                $query = mysqli_query($dbc,"SELECT project_path_milestone, project_path FROM project_path_milestone order by project_path");
                while($row = mysqli_fetch_array($query)) {
                ?><option <?php if ($row['project_path_milestone'] == $task_path) { echo " selected"; } ?> value='<?php echo  $row['project_path_milestone']; ?>' ><?php echo $row['project_path']; ?></option>
            <?php	} ?>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label for="site_name" class="col-sm-4 control-label">Milestone & Timeline:</label>
          <div class="col-sm-8">
            <select data-placeholder="Choose an Option..." name="task_milestone_timeline" id="task_milestone_timeline"  class="chosen-select-deselect form-control" width="580">
                <option value=""></option>
                <?php
                $each_tab = explode('#*#', get_project_path_milestone($dbc, $task_path, 'milestone'));
                $timeline = explode('#*#', get_project_path_milestone($dbc, $task_path, 'timeline'));
                $j=0;
                foreach ($each_tab as $cat_tab) {
                    if ($task_milestone_timeline == $cat_tab) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.' : '.$timeline[$j].'</option>';
                    $j++;
                }
              ?>
            </select>
          </div>
        </div>

    <?php } ?>

    <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Heading:</label>
        <div class="col-sm-8">
            <input type="text" name="task_heading" value="<?php echo $task_heading; ?>" class="form-control" width="380" />
        </div>
    </div>
    <?php } ?>

    <?php if (strpos($value_config, ','."Task".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Task:</label>
        <div class="col-sm-8">
            <textarea name="task" rows="3" cols="50" class="form-control"><?php echo html_entity_decode($task); ?></textarea>
        </div>
    </div>
    <?php } ?>

    <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Staff:</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Staff Member..." id="checklist_userid" name="task_contactid" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
				<?php
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
					foreach($query as $id) {
						$selected = '';
						$selected = $task_contactid == $id ? 'selected = "selected"' : '';
						echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
					}
				  ?>
            </select>
        </div>
    </div>
    <?php } ?>

    <?php if (strpos($value_config, ','."To Do Date".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">To Do Date:</label>
        <div class="col-sm-8">
            <input name="task_tododate" value="<?php echo $task_tododate; ?>" type="text" class="datepicker">
        </div>
    </div>
    <?php } ?>

    <?php if (strpos($value_config, ','."Work Time".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Work Time:</label>
        <div class="col-sm-8">
            <input name="task_work_time" value="<?php echo $task_work_time; ?>" type="text" value="00:00:00" class="form-control">
        </div>
    </div>
    <?php } ?>

    <?php if (strpos($value_config, ','."Status".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Status:</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Vendor..." name="task_status" class="chosen-select-deselect1 form-control" width="380">
              <option value=""></option>
              <?php
                $tabs = get_config($dbc, 'task_status');
                $each_tab = explode(',', $tabs);
                foreach ($each_tab as $cat_tab) {
                    if ($task_status == $cat_tab) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
                }
              ?>
            </select>
        </div>
    </div>
    <?php } ?>

    <div class="form-group">
        <div class="col-sm-4 clearfix">
        </div>
      <div class="col-sm-8">
        <button name="tasklist" value="tasklist" class="btn brand-btn">Submit</button>
      </div>
    </div>

    </form>

</div>
<?php include ('../footer.php'); ?>
