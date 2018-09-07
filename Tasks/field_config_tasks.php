<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('tasks');

error_reporting(0);

if (isset($_POST['add_tab'])) {
    $task_tab = filter_var($_POST['task_tab'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='task_tab'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$task_tab' WHERE name='task_tab'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('task_tab', '$task_tab')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $query = mysqli_query($dbc,"DELETE FROM `field_config_communication` WHERE type='Task'");

    $tabs_cat = get_config($dbc, 'task_tab');
    $each_tab_cat = explode(',', $tabs_cat);
    $i = 0;
    foreach ($each_tab_cat as $cat_tab_cat) {
        $board_assign = ','.implode(',',$_POST['board_assign_'.$i]).',';
        $query_insert_config = "INSERT INTO `field_config_communication` (`type`, `board_name`, `board_assign`) VALUES ('Task', '$cat_tab_cat', '$board_assign')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
        $i++;
    }

    $query = mysqli_query($dbc,"DELETE FROM `field_config_communication` WHERE type='Community Task'");

    $community_task_tab = filter_var($_POST['community_task_tab'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='community_task_tab'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$community_task_tab' WHERE name='community_task_tab'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('community_task_tab', '$community_task_tab')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $tabs_cat = get_config($dbc, 'community_task_tab');
    $each_tab_cat = explode(',', $tabs_cat);
    $i = 0;
    foreach ($each_tab_cat as $cat_tab_cat) {
        $board_assign = ','.implode(',',$_POST['community_board_assign_'.$i]).',';
        $query_insert_config = "INSERT INTO `field_config_communication` (`type`, `board_name`, `board_assign`) VALUES ('Community Task', '$cat_tab_cat', '$board_assign')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
        $i++;
    }

    if($task_category = 'Zen Earth Corp' || $task_category = 'Green Earth Energy' || $task_category = 'Green Life Can') {
        if (strpos(WEBSITE_URL, 'zenearthcorp.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenearthenergysolutions.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenlifecan.rookconnect.com') !== FALSE) {

            $zenearth_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'zenearth_rook_db');
            $gees_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'gees_rook_db');
            $glcllc_rook_db = @mysqli_connect('localhost', 'zen_rook_user', 'R0bot587tw3ak', 'glcllc_rook_db');

            $query = mysqli_query($zenearth_rook_db,"DELETE FROM `field_config_communication` WHERE type='Community Task'");
            $query = mysqli_query($gees_rook_db,"DELETE FROM `field_config_communication` WHERE type='Community Task'");
            $query = mysqli_query($glcllc_rook_db,"DELETE FROM `field_config_communication` WHERE type='Community Task'");

            $community_task_tab = filter_var($_POST['community_task_tab'],FILTER_SANITIZE_STRING);
            $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='community_task_tab'"));
            if($get_config['configid'] > 0) {
                $query_update_employee = "UPDATE `general_configuration` SET value = '$community_task_tab' WHERE name='community_task_tab'";
                $result_update_employee = mysqli_query($zenearth_rook_db, $query_update_employee);
                $result_update_employee = mysqli_query($gees_rook_db, $query_update_employee);
                $result_update_employee = mysqli_query($glcllc_rook_db, $query_update_employee);
            } else {
                $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('community_task_tab', '$community_task_tab')";
                $result_insert_config = mysqli_query($zenearth_rook_db, $query_insert_config);
                $result_insert_config = mysqli_query($gees_rook_db, $query_insert_config);
                $result_insert_config = mysqli_query($glcllc_rook_db, $query_insert_config);
            }

            $tabs_cat = get_config($dbc, 'community_task_tab');
            $each_tab_cat = explode(',', $tabs_cat);
            $i = 0;
            foreach ($each_tab_cat as $cat_tab_cat) {
                $board_assign = ','.implode(',',$_POST['community_board_assign_'.$i]).',';
                $query_insert_config = "INSERT INTO `field_config_communication` (`type`, `board_name`, `board_assign`) VALUES ('Community Task', '$cat_tab_cat', '$board_assign')";
                $result_insert_config = mysqli_query($zenearth_rook_db, $query_insert_config);
                $result_insert_config = mysqli_query($gees_rook_db, $query_insert_config);
                $result_insert_config = mysqli_query($glcllc_rook_db, $query_insert_config);
                $i++;
            }
        }
    }

    echo '<script type="text/javascript"> window.location.replace("field_config_tasks.php?type=tab"); </script>';
}

?>
<script type="text/javascript">
</script>
</head>
<body>

<?php include ('../navigation.php'); ?>

<div class="container">
<div class="row">
<?php
$task_tab_back = explode(",",get_config($dbc, 'task_tab'));
?>
<a href="tasks.php?category=<?php echo $task_tab_back[0]; ?>" class="btn config-btn">Back</a>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->
<br><br>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<div class="panel-group" id="accordion2">

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_1" >
                    Company Tasks<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_1" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="form-group">
                    <label for="fax_number"	class="col-sm-4	control-label">Board Names:</label>
                    <div class="col-sm-8">
                      <input name="task_tab" type="text" value="<?php echo get_config($dbc, 'task_tab'); ?>" class="form-control"/>
                    </div>
                </div>

                <?php
                $tabs_cat = get_config($dbc, 'task_tab');
                $each_tab_cat = explode(',', $tabs_cat);
                $i = 0;
                foreach ($each_tab_cat as $cat_tab_cat) {
                    $board_assign = get_tasklist($dbc, 'Task', $cat_tab_cat, 'board_assign');
                    ?>
                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label"><?php echo $cat_tab_cat; ?>:</label>
                        <div class="col-sm-8">
                            <select multiple name="board_assign_<?php echo $i; ?>[]" data-placeholder="Choose a User..." class="chosen-select-deselect form-control" width="380">
							  <?php
								$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
								foreach($query as $id) {
									$selected = '';
									$selected = strpos($board_assign, ','.$id.',') !== FALSE ? 'selected = "selected"' : '';
									echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
								}
							  ?>
                            </select>
                        </div>
                    </div>
                    <?php $i++;
                }
            ?>

            </div>
        </div>
    </div>

    <div class="panel panel-default">
        <div class="panel-heading">
            <h4 class="panel-title">
                <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_2" >
                    Community Tasks<span class="glyphicon glyphicon-plus"></span>
                </a>
            </h4>
        </div>

        <div id="collapse_2" class="panel-collapse collapse">
            <div class="panel-body">

                <div class="form-group">
                    <label for="fax_number"	class="col-sm-4	control-label">Board Names:</label>
                    <div class="col-sm-8">
                      <input name="community_task_tab" type="text" value="<?php echo get_config($dbc, 'community_task_tab'); ?>" class="form-control"/>
                    </div>
                </div>

                <?php
                $tabs_cat = get_config($dbc, 'community_task_tab');
                $each_tab_cat = explode(',', $tabs_cat);
                $i = 0;
                foreach ($each_tab_cat as $cat_tab_cat) {
                    $community_board_assign = get_tasklist($dbc, 'Community Task', $cat_tab_cat, 'board_assign');
                    ?>
                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label"><?php echo $cat_tab_cat; ?>:</label>
                        <div class="col-sm-8">
                            <select multiple name="community_board_assign_<?php echo $i; ?>[]" data-placeholder="Choose a Software to Share..." class="chosen-select-deselect form-control" width="380">
                              <?php if (strpos(WEBSITE_URL, 'zenearthcorp.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenearthenergysolutions.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'greenlifecan.rookconnect.com') !== FALSE || strpos(WEBSITE_URL, 'localhost') !== FALSE || strpos(WEBSITE_URL, 'demo.rookconnect.com') !== FALSE) { ?>
                              <option <?php if (strpos($community_board_assign, ',zenearthcorp.rookconnect.com,') !== FALSE) { echo  'selected="selected"'; } ?> value='zenearthcorp.rookconnect.com' >zenearthcorp.rookconnect.com</option>
                              <option <?php if (strpos($community_board_assign, ',greenearthenergysolutions.rookconnect.com,') !== FALSE) { echo  'selected="selected"'; } ?> value='greenearthenergysolutions.rookconnect.com' >greenearthenergysolutions.rookconnect.com</option>
                              <option <?php if (strpos($community_board_assign, ',greenlifecan.rookconnect.com,') !== FALSE) { echo  'selected="selected"'; } ?> value='greenlifecan.rookconnect.com' >greenlifecan.rookconnect.com</option>
                              <option <?php if (strpos($community_board_assign, ',demo.rookconnect.com,') !== FALSE) { echo  'selected="selected"'; } ?> value='demo.rookconnect.com' >demo.rookconnect.com</option>
                              <option <?php if (strpos($community_board_assign, ',localhost,') !== FALSE) { echo  'selected="selected"'; } ?> value='localhost' >localhost</option>

                              <?php } ?>
                            </select>
                        </div>
                    </div>
                    <?php $i++;
                }
            ?>

            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-4 clearfix">
            <a href="tasks.php?category=<?php echo $task_tab_back[0]; ?>" class="btn config-btn">Back</a>
			<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->
        </div>
        <div class="col-sm-8">
            <button	type="submit" name="add_tab"	value="add_tab" class="btn config-btn btn-lg	pull-right">Submit</button>
        </div>
    </div>
</div>
</form>
</div>
</div>

<?php include ('../footer.php'); ?>
