<?php
if(!empty($_GET['tile']) || !empty($_GET['tile_add'])) {
    include_once('../include.php');
}

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
        $task_status = 'To Do';
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
        $url = $_POST['from'];
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
<script type="text/javascript">
$(document).ready(function() {
	$("#task_path").change(function() {
		var task_path = $("#task_path").val();
		$.ajax({
			type: "GET",
			url: "ticket_ajax_all.php?fill=task_path_milestone&task_path="+task_path,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#task_milestone_timeline').html(response);
				$("#task_milestone_timeline").trigger("change.select2");
			}
		});
	});


    $("#task_businessid").change(function() {
		var businessid = this.value;

        $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "ticket_ajax_all.php?fill=taskassigncontact&businessid="+businessid,
            dataType: "html",   //expect html to be returned
            success: function(response){
				$('#checklist_clientid').html(response);
				$("#checklist_clientid").trigger("change.select2");
            }
        });


	});

	$(".delete_this_task").click(function() {
			var id = $(this).attr("id");
			var res = confirm('Are you sure you want to delete this task?');
			if(res){
				$.ajax({    //create an ajax request to load_page.php
				type: "GET",
				url: "ticket_ajax_all.php?fill=deletetask&tasklistid="+id,
				dataType: "html",   //expect html to be returned
				success: function(response){
					location.reload();
				}
				});
			}
	});

    $(".worktime").focus(function() {
        //alert(this.id);
    }).blur(function() {
        var worktime_id = this.id;
        var worktime_value = this.value;
        var arr = worktime_id.split('_');
        $.ajax({    //create an ajax request to load_page.php
            type: "GET",
            url: "ticket_ajax_all.php?fill=tasklistaddtime&tasklistid="+arr[1]+'&worktime='+worktime_value,
            dataType: "html",   //expect html to be returned
            success: function(response){
                location.reload();
            }
        });
    });
});


function selectCheckliststaff(sel) {
	var contactid = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "ticket_ajax_all.php?fill=tasklistreassignstaff&tasklistid="+arr[1]+'&contactid='+contactid,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
function selectBoard(sel) {
	var category = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "ticket_ajax_all.php?fill=tasklistboard&tasklistid="+arr[1]+'&category='+category,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}

function selectTaskliststatus(sel) {
	var status = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "ticket_ajax_all.php?fill=tasklistreassignstatus&tasklistid="+arr[1]+'&status='+status,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
</script>
</head>
<body>
    <?php
    if(!empty($_GET['supportid'])) {
        $supportid = $_GET['supportid'];
        $company_name = get_support($dbc, $supportid, 'company_name');
        $get_contact =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contactid FROM	contacts WHERE	name='$company_name'"));
        $task_businessid = $get_contact['contactid'];
        $task_heading = get_support($dbc, $supportid, 'heading');
        $task = html_entity_decode(get_support($dbc, $supportid, 'message'));
        $task_status = 'To Do';
    }
    if(!empty($_GET['tile'])) {
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
        echo '<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">';
        echo '<input type="hidden" name="task_from_tasktile" value="1" />';
        echo '<input type="hidden" name="tasklistid" value="'.$_GET['tasklistid'].'" />';
    } else if(!empty($_GET['tile_add'])) {
        echo '<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">';
        echo '<input type="hidden" name="task_from_tasktile" value="-1" />';
    } else {
        echo '<input type="hidden" name="task_from_tasktile" value="0" />';
    }
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT task FROM field_config"));
    $value_config = ','.$get_field_config['task'].',';
	$back_url = 'add_tickets.php?ticketid='.$ticketid;
	if(!empty($_GET['from'])) {
		$back_url = urldecode($_GET['from']);
	}
	?>
	<input type="hidden" name="from" value="<?php echo $back_url; ?>">
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

    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Heading:</label>
        <div class="col-sm-8">
            <input type="text" name="task_heading" value="<?php echo $task_heading; ?>" class="form-control" width="380" />
        </div>
    </div>

    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Task:</label>
        <div class="col-sm-12">
            <textarea name="task" rows="3" cols="50" class="form-control"><?php echo html_entity_decode($task); ?></textarea>
        </div>
    </div>

    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Staff:</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Staff Member..." id="checklist_userid" name="task_contactid" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY."");
                while($row = mysqli_fetch_array($query)) {
                    if ($task_contactid == $row['contactid']) {
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

    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">To Do Date:</label>
        <div class="col-sm-8">
            <input name="task_tododate" value="<?php echo $task_tododate; ?>" type="text" class="datepicker">
        </div>
    </div>

    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Work Time:</label>
        <div class="col-sm-8">
            <input name="task_work_time" value="<?php echo $task_work_time; ?>" type="text" value="00:00:00" class="form-control">
        </div>
    </div>

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

    <div class="form-group">
      <div class="col-sm-8">
		<span class="popover-examples list-inline pull-right" style="margin-left:-6em;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="The entire form will submit and close if this submit button is pressed.">
			<button onclick="return false;" title="The entire form will submit and close if this submit button is pressed." class="btn brand-btn">Submit</button></a></span>
		<button name="tasklist" value="tasklist" title="The entire form will submit and close if this submit button is pressed." class="btn brand-btn pull-right" style="pointer-events:none;">Submit</button>
      </div>
    </div>

    <?php
    if(empty($_GET['tile'])) {
    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT task_dashboard FROM field_config"));
    $value_config = ','.$get_field_config['task_dashboard'].',';

    $contactid = $_SESSION['contactid'];
    $query_check_credentials = "SELECT * FROM tasklist WHERE ticketid='$ticketid' AND status!='Archive' ORDER BY tasklistid DESC";

    $result = mysqli_query($dbc, $query_check_credentials);

    $num_rows = mysqli_num_rows($result);
    if($num_rows > 0) {
        echo "<div id='no-more-tables'><table class='table table-bordered'>";
        echo "<tr class='hidden-xs hidden-sm'>";
        echo "<th>Contact</th>";
        echo "<th>Scrum Board</th>";
        echo "<th>Heading</th>";
        echo "<th>Created Date</th>";
        echo "<th>Reassign</th>";
        echo "<th>Work time</th>";
        echo "<th>Status</th>";
        echo "</tr>";
    }
    $submit_inc = 0;
    while($row = mysqli_fetch_array( $result ))
    {
        echo '<input type="hidden" name="tasklistid[]" id="tasklistid" value="'.$tasklistid.'">';

        $style = '';
        if($row['status'] == 'Priority') {
            $style = 'style="color: red;"';
        }
        $contactid = $row['contactid'];
        $category = $row['category'];
        $tasklistid = $row['tasklistid'];
        echo "<tr $style>";
        $checked = '';
        if($row['status'] == 'Done') {
            $checked = 'checked disabled';
        }

        $clientid = $row['clientid'];
        $businessid = $row['businessid'];

        echo '<td data-title="Contact">' . get_contact($dbc, $businessid, 'name').'<br>'.get_contact($dbc, $clientid, 'first_name').' '.get_contact($dbc, $clientid, 'last_name') . '</td>';
        ?>
        <td data-title="Scrum Board">
            <select data-placeholder="Choose a Board..." name="task_category123" id="board_<?php echo $tasklistid; ?>" class="chosen-select-deselect1 form-control" width="380">
              <option value=""></option>
              <?php
                $tabs_cat = get_config($dbc, 'task_tab');
                $each_tab_cat = explode(',', $tabs_cat);
                foreach ($each_tab_cat as $cat_tab_cat) {
                    ?>
                    <option <?php if ($category == $cat_tab_cat) { echo  'selected="selected"'; } ?> value='<?php echo $cat_tab_cat; ?>' ><?php echo $cat_tab_cat; ?></option>
                    <?php }
              ?>
            </select>
        </td>
        <?php
        echo '<td data-title="Heading">' . $row['heading'] . '</td>';
        echo '<td data-title="Created Date">' . $row['created_date'] . '</td>';
        ?>

        <td data-title="Reassign">
            <select name="task_reassign123" id="assign_<?php echo $tasklistid; ?>" data-placeholder="Choose a User..." class="chosen-select-deselect1 form-control" width="380">
			  <option value=""></option>
				  <?php
					$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status`>0"),MYSQLI_ASSOC));
					foreach($query as $id) {
						$selected = '';
						$selected = $id == $contactid ? 'selected = "selected"' : '';
						echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
					}
				  ?>
            </select>
        </td>

        <?php
         echo '<td data-title="Work Time"><input name="work_time[]" id="worktime_'.$tasklistid.'" value="'.$row['work_time'].'" type="text" class="form-control worktime">';
        $status = $row['status'];
        ?>
        <td data-title="Status">
            <select name="task_status123" id="status_<?php echo $tasklistid; ?>" data-placeholder="Choose a Status..." class="chosen-select-deselect1 form-control" width="380">
              <option value=""></option>
              <?php
                $tabs = get_config($dbc, 'task_status');
                $each_tab = explode(',', $tabs);
                foreach ($each_tab as $cat_tab) {
                    if ($status == $cat_tab) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
                }
              ?>
            </select>
        </td>

        <?php

        echo "</tr>";
        $submit_inc++;
    }

    echo '</table></div>';

    }

    ?>

</div>
