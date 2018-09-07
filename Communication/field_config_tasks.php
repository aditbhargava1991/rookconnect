<?php
/*
Dashboard
*/
include ('../include.php');
checkAuthorised('communication');

error_reporting(0);

if (isset($_POST['add_tab'])) {
    //Task/Ticekt
    $task_ticket = implode(',',$_POST['task_ticket']);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='task_ticket'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$task_ticket' WHERE name='task_ticket'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('task_ticket', '$task_ticket')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Task/Ticekt

    //Task
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

    //Ticket
    $ticket_tab = filter_var($_POST['ticket_tab'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_tab'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$ticket_tab' WHERE name='ticket_tab'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_tab', '$ticket_tab')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

    $query = mysqli_query($dbc,"DELETE FROM `field_config_communication` WHERE type='Ticket'");

    $tabs_cat = get_config($dbc, 'ticket_tab');
    $each_tab_cat = explode(',', $tabs_cat);
    $i = 0;
    foreach ($each_tab_cat as $cat_tab_cat) {
        $board_assign = ','.implode(',',$_POST['ticket_board_assign_'.$i]).',';
        $query_insert_config = "INSERT INTO `field_config_communication` (`type`, `board_name`, `board_assign`) VALUES ('Ticket', '$cat_tab_cat', '$board_assign')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
        $i++;
    }

    //Task Status
    $task_status = filter_var($_POST['task_status'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='task_status'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$task_status' WHERE name='task_status'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('task_status', '$task_status')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Task Status

    //Ticket Status
    $ticket_status = filter_var($_POST['ticket_status'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='ticket_status'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$ticket_status' WHERE name='ticket_status'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('ticket_status', '$ticket_status')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
    //Ticket Status

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
<h1>Communication</h1>
<div class="pad-left gap-top double-gap-bottom"><a href="tasks.php?category=All" class="btn config-btn">Back to Dashboard</a></div>
<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

    <?php
    $value_config = ','.get_config($dbc, 'task_ticket').',';
    ?>
    <div class="panel-group" id="accordion2">

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
                        Task & <?= TICKET_NOUN ?><span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_field" class="panel-collapse collapse">
                <div class="panel-body">

                <div id='no-more-tables'>
                    <table border='2' cellpadding='10' class='table'>
                        <tr>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Task".',') !== FALSE) { echo " checked"; } ?> value="Task" style="height: 20px; width: 20px;" name="task_ticket[]">&nbsp;&nbsp;Task
                            </td>
                            <td>
                                <input type="checkbox" <?php if (strpos($value_config, ','."Ticket".',') !== FALSE) { echo " checked"; } ?> value="Ticket" style="height: 20px; width: 20px;" name="task_ticket[]">&nbsp;&nbsp;<?= TICKET_NOUN ?>
                            </td>
                        </tr>
                    </table>
                </div>
                </div>
            </div>
        </div>

        <?php if (strpos($value_config, ','."Task".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_info" >
                        Task Boards<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_info" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    $task_tab_back = explode(",",get_config($dbc, 'task_tab'));
                    ?>
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
                                        $query1 = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." order by first_name");
                                        while($row1 = mysqli_fetch_array($query1)) {
                                            ?>
                                            <option <?php if (strpos($board_assign, ','.$row1['contactid'].',') !== FALSE) { echo  'selected="selected"'; } ?> value='<?php echo $row1['contactid']; ?>' ><?php echo decryptIt($row1['first_name']).' '.decryptIt($row1['last_name']); ?></option>
                                        <?php }
                                      ?>
                                    </select>
                                </div>
                            </div>
                            <?php $i++; }
                    ?>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field3" >
                        Task Status/Heading<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_field3" class="panel-collapse collapse">
                <div class="panel-body">

                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label">Add Headings separated by a comma:</label>
                        <div class="col-sm-8">
                          <input name="task_status" type="text" value="<?php echo get_config($dbc, 'task_status'); ?>" class="form-control"/>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php } ?>

        <?php if (strpos($value_config, ','."Ticket".',') !== FALSE) { ?>
        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ticket" >
                        <?= TICKET_NOUN ?> Boards<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_ticket" class="panel-collapse collapse">
                <div class="panel-body">

                    <?php
                    $task_tab_back = explode(",",get_config($dbc, 'ticket_tab'));
                    ?>
                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label">Board Names:</label>
                        <div class="col-sm-8">
                          <input name="ticket_tab" type="text" value="<?php echo get_config($dbc, 'ticket_tab'); ?>" class="form-control"/>
                        </div>
                    </div>

                    <?php
                    $tabs_cat = get_config($dbc, 'ticket_tab');
                    $each_tab_cat = explode(',', $tabs_cat);
                    $i = 0;
                    foreach ($each_tab_cat as $cat_tab_cat) {
                        $board_assign = get_ticketlist($dbc, $cat_tab_cat, 'board_assign');
                        if($cat_tab_cat != '') {
                        ?>
                        <div class="form-group">
                            <label for="fax_number"	class="col-sm-4	control-label"><?php echo $cat_tab_cat; ?>:</label>
                            <div class="col-sm-8">
                                <select multiple name="ticket_board_assign_<?php echo $i; ?>[]" data-placeholder="Choose a User..." class="chosen-select-deselect form-control" width="380">
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

                    }
                    ?>

                </div>
            </div>
        </div>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h4 class="panel-title">
                    <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field2" >
                        <?= TICKET_NOUN ?> Status/Heading<span class="glyphicon glyphicon-plus"></span>
                    </a>
                </h4>
            </div>

            <div id="collapse_field2" class="panel-collapse collapse">
                <div class="panel-body">

                    <div class="form-group">
                        <label for="fax_number"	class="col-sm-4	control-label">Add Headings separated by a comma:</label>
                        <div class="col-sm-8">
                          <input name="ticket_status" type="text" value="<?php echo get_config($dbc, 'ticket_status'); ?>" class="form-control"/>
                        </div>
                    </div>

                </div>
            </div>
        </div>
        <?php } ?>

    </div>

    <div class="form-group">
        <div class="col-sm-6">
            <a href="tasks.php?category=All" class="btn config-btn btn-lg">Back</a>
			<!--<a href="#" class="btn config-btn" onclick="history.go(-1);return false;">Back</a>-->
        </div>
        <div class="col-sm-6">
            <button	type="submit" name="add_tab" value="add_tab" class="btn config-btn btn-lg pull-right">Submit</button>
        </div>
    </div>
    </div>
</form>
</div>
</div>

<?php include ('../footer.php'); ?>
