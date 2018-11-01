<?php
error_reporting(0);

//echo $_SERVER[REQUEST_URI];

if (isset($_POST['tasklist'])) {
    $created_by = $_GET['contactid'];
    $projectid = $_POST['projectid'];
	$client_projectid = '';
	if(substr($projectid,0,1) == 'C') {
		$client_projectid = substr($projectid,1);
		$projectid = '';
	}
    $businessid = $_POST['businessid'];

    if ($_POST['checklist_subtab'] == 'ADD NEW') {
        $subtab_name = $_POST['new_subtab'];
        $subtab_shared = ','.implode(',',$_POST['subtab_shared']).',';
        $query_insert_subtab = "INSERT INTO `checklist_subtab` (`name`, `created_by`, `shared`) VALUES ('$subtab_name', '$created_by', '$subtab_shared')";
        $result_insert_subtab = mysqli_query($dbc, $query_insert_subtab);
        $subtabid = mysqli_insert_id($dbc);

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Added Checklist Sub Tab <b>'.$subtab_name.'</b> on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '', '$subtab_name', '', '', '$subtabid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

        $update_tab_config = ",".$subtabid."_ongoing,".$subtabid."_daily,".$subtabid."_weekly,".$subtabid."_monthly";
        foreach ($_POST['subtab_shared'] as $tabs_config_row) {
            $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'checklist_tabs_" . $tabs_config_row . "' FROM (SELECT COUNT(*) numrows FROM `general_configuration` WHERE `name`='checklist_tabs_" . $tabs_config_row . "') current_config WHERE numrows=0");
            $query_tab_config = "UPDATE `general_configuration` SET `value` = CONCAT(`value`, '$update_tab_config') WHERE `name` = 'checklist_tabs_" . $tabs_config_row . "'";
            $result_tab_config = mysqli_query($dbc, $query_tab_config);
        }
        $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'checklist_tabs_" . $_GET['contactid'] . "' FROM (SELECT COUNT(*) numrows FROM `general_configuration` WHERE `name`='checklist_tabs_" . $_GET['contactid'] . "') current_config WHERE numrows=0");
        $query_tab_config = "UPDATE `general_configuration` SET `value` = CONCAT(`value`, '$update_tab_config') WHERE `name` = 'checklist_tabs_" . $_GET['contactid'] . "'";
        $result_tab_config = mysqli_query($dbc, $query_tab_config);
    } else {
        $subtabid = filter_var($_POST['checklist_subtab'],FILTER_SANITIZE_STRING);
        $query_retrieve_subtab = "SELECT * FROM `checklist_subtab` WHERE `subtabid` = '$subtabid'";
        $result_retrieve_subtab = mysqli_fetch_array(mysqli_query($dbc, $query_retrieve_subtab));
        $subtab_name = $result_retrieve_subtab['name'];
        $subtab_shared = ','.implode(',',$_POST['subtab_shared']).',';

        if ($subtab_shared != $result_retrieve_subtab['shared']) {
            $query_update_subtab = "UPDATE `checklist_subtab` SET `name` = '$subtab_name', `shared` = '$subtab_shared' WHERE `subtabid` = '$subtabid'";
            $result_retrieve_subtab = mysqli_fetch_array(mysqli_query($dbc, $query_update_subtab));

            $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Updated Checklist Sub Tab <b>'.$subtab_name.'</b> on '.date('Y-m-d');
            $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '', '$subtab_name', '', '', '$subtabid')";
            $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
        }
    }

    $checklist_type = filter_var($_POST['checklist_type'],FILTER_SANITIZE_STRING);
    $checklist_name = filter_var($_POST['checklist_name'],FILTER_SANITIZE_STRING);

	$reset_time = '12:00 am';
	$reset_day = '1';
	switch($checklist_type) {
		case 'daily':
			$reset_time = filter_var($_POST['checklist_reset_day_time'],FILTER_SANITIZE_STRING);
			break;
		case 'weekly':
			$reset_time = filter_var($_POST['checklist_reset_week_time'],FILTER_SANITIZE_STRING);
			$reset_day = filter_var($_POST['checklist_reset_week_day'],FILTER_SANITIZE_STRING);
			break;
		case 'monthly':
			$reset_time = filter_var($_POST['checklist_reset_month_time'],FILTER_SANITIZE_STRING);
			$reset_day = filter_var($_POST['checklist_reset_month_day'],FILTER_SANITIZE_STRING);
			break;
	}
    $assign_staff = ','.implode(',',$_POST['assign_staff']).',';

    if(empty($_POST['checklistid'])) {
        $query_insert_ca = "INSERT INTO `checklist` (`subtabid`, `assign_staff`, `checklist_type`, `reset_day`, `reset_time`, `checklist_name`, `created_by`, `projectid`, `client_projectid`, `businessid`) VALUES ('$subtabid', '$assign_staff', '$checklist_type', '$reset_day', '$reset_time', '$checklist_name', '$created_by', '$projectid', '$client_projectid', '$businessid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
        $checklistid = mysqli_insert_id($dbc);

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Added Checklist <b>'.$checklist_name.'</b> in '.$subtab_name.' : '.$checklist_type.' on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '$subtab_name', '$checklist_type', '$checklistid', '$subtabid'";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

    } else {
        $checklistid = $_POST['checklistid'];
        $query_update_vendor = "UPDATE `checklist` SET `subtabid` = '$subtabid', `assign_staff` = '$assign_staff', `checklist_type` = '$checklist_type', `reset_day` = '$reset_day', `reset_time` = '$reset_time', `checklist_name` = '$checklist_name', `created_by` = '$created_by', `projectid` = '$projectid', `client_projectid` = '$client_projectid', `businessid` = '$businessid' WHERE `checklistid` = '$checklistid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Updated Checklist <b>'.$checklist_name.'</b> in '.$subtab_name.' : '.$checklist_type.' on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '$subtab_name', '$checklist_type', '$checklistid', '$subtabid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
    }

    if(!empty($_POST['checklistid'])) {
        for($i = 0; $i < count($_POST['checklist_update']); $i++) {
            $checklist = $_POST['checklist_update'][$i];
            $checklistnameid = $_POST['checklistid_update'][$i];
            $query_update_vendor = "UPDATE `checklist_name` SET `checklist` = CONCAT('$checklist',SUBSTRING(`checklist`,POSITION('".htmlentities("<p>")."' IN checklist))) WHERE `checklistnameid` = '$checklistnameid'";
            $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        }

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Updated Checklist Items in <b>'.$checklist_name.'</b> on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
    }

    $item = 0;
	$error_list = '';
    for($i = 0; $i < count($_POST['checklist']); $i++) {
        $checklist = filter_var($_POST['checklist'][$i],FILTER_SANITIZE_STRING);
		$priority = count($_POST['checklist_update'])+$i+1;

        if($checklist != '') {
            $query_insert_client_doc = "INSERT INTO `checklist_name` (`checklistid`, `checklist`, `priority`) VALUES ('$checklistid', '$checklist', '$priority')";
            if(!mysqli_query($dbc, $query_insert_client_doc)) {
				$error_list .= mysqli_errno($dbc).': '.mysqli_error($dbc)."<br />\n";
			}
            $item = 1;
        }
    }

    if($item == 1) {
        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Added Checklist Items in <b>'.$checklist_name.'</b> on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
    }



    if(isset($_POST['from']) && $_POST['from'] == 'project') {
        $url = '/Project/review_project.php?type=checklist&projectid='.$projectid;
    } else {
        $url = addOrUpdateCurrentUrlParam(array('type','update_type','subtab','checklistid','subtabid'),array($checklist_type,'','Checklists',$checklistid,$subtabid));
    }

    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
        $document = htmlspecialchars($_FILES["upload_document"]["name"][$i], ENT_QUOTES);

        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]) ;

        if($document != '') {
            $query_insert_client_doc = "INSERT INTO `checklist_document` (`checklistid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$checklistid', 'Support Document', '$document', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
        }
    }

	if($error_list != '') {
		echo $error_list;
	} else {
		echo '<script type="text/javascript"> window.location.replace("'.$url.'"); </script>';
	}
}
?>

<script type="text/javascript">
$(document).ready(function () {

	$("#businessid").change(function() {
        var businessid = $("#businessid").val();
		$.ajax({
			type: "GET",
			url: "checklist_ajax.php?fill=projectname&businessid="+businessid,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#projectid').html(response);
				$("#projectid").trigger("change.select2");
			}
		});
	});

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

    $('#first_task').bind("keypress", {}, function (e) {
        if (e.keyCode == 13) {
            var clone = $('.additional_doc').clone();
            clone.find('input').val('').removeAttr('checked').removeAttr('id');
            clone.find('.popover-examples').css('display', 'none');
            clone.find('#add_row_doc').css('display', 'none');
            clone.removeClass("additional_doc");
            $('#add_here_new_doc').append(clone);
            $('#add_here_new_doc').find('input').last().focus();
            return false;
        }
    });

    $('#add_row_doc').on( 'click', function () {
        var clone = $('.additional_doc').clone();
        clone.find('input').val('').removeAttr('checked');
		clone.find('.popover-examples').css('display', 'none');
		clone.find('#add_row_doc').css('display', 'none');
        clone.removeClass("additional_doc");
        $('#add_here_new_doc').append(clone);
		$('#add_here_new_doc').find('input').last().focus();
		return false;
    });
});

function changeSubTab(sel) {
    var stage = sel.value;
    if(stage == 'ADD NEW') {
        $('.new_subtab').show();
    } else {
        $('.new_subtab').hide();
    }

    $.ajax({
        type: "GET",
        url: "checklist_ajax.php?fill=subtab_change&subtabid=" + sel.value,
        dataType: "html",
        success: function(response){
            $('#subtab_shared').html(response);
            $('#subtab_shared').trigger("change.select2");
        }
    });
}

function changeAssignedStaff(sel) {
    if($(sel).find('option[value="ALL"]').is(':selected')) {
        $(sel).find('option').attr('selected','selected');
        $(sel).find('option[value="ALL"]').removeAttr('selected');
        $(sel).trigger('change.select2');
    }
}

function changeSubTabShared(sel) {
    if($(sel).find('option[value="ALL"]').is(':selected')) {
        $(sel).find('option').attr('selected','selected');
        $(sel).find('option[value="ALL"]').removeAttr('selected');
        $(sel).trigger('change.select2');
    }
}
</script>

</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('client_info');
$tab_list = get_config($dbc, 'checklist_tabs_' . $_SESSION['contactid']); ?>
<div style="width:95%" class="container">
	<div class="row">

	<h1>Add Checklist</h1>
	<div class="gap-top double-gap-bottom"><a href="checklist.php" class="btn config-btn">Back to Dashboard</a></div>

    <form id="form2" name="form2" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
    <?php
    $task_contactid = $_SESSION['contactid'];

    if(!empty($_GET['checklistid'])) {
        $checklistid = $_GET['checklistid'];
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM checklist WHERE checklistid='$checklistid'"));

        $subtab = $get_contact['subtabid'];
        $assign_staff = $get_contact['assign_staff'];
        $checklist_type = $get_contact['checklist_type'];
        $checklist_name = $get_contact['checklist_name'];
        $checklist_reset_day = $get_contact['reset_day'];
        $checklist_reset_time = $get_contact['reset_time'];
        $projectid = $get_contact['projectid'];
        $client_projectid = 'C'.$get_contact['client_projectid'];
        $businessid = $get_contact['businessid'];

        $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_subtab` WHERE `subtabid` = '$subtab'"));
        $subtab_shared = $get_subtab['shared'];

        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM checklist_name WHERE checklistid='$checklistid' AND `deleted`=0"));

        echo '<input type="hidden" name="checklistid" value="'.$_GET['checklistid'].'" />';
    }
    if(!empty($_GET['subtabid'])) {
        $subtab = $_GET['subtabid'];
        $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_subtab` WHERE `subtabid` = '$subtab'"));
        $subtab_shared = $get_subtab['shared'];
    }
    if(!empty($_GET['projectid'])) {
        $projectid = $_GET['projectid'];
        $get_project = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM project WHERE projectid='$projectid'"));

        $businessid = $get_project['businessid'];

        echo '<input type="hidden" id="from" name="from" value="project" />';
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT task FROM field_config"));
    $value_config = ','.$get_field_config['task'].',';
    ?>

    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Sub Tab:
		</label>
        <div class="col-sm-8">
            <select data-placeholder="Select a Sub Tab..." onChange='changeSubTab(this)' name="checklist_subtab" class="chosen-select-deselect form-control" width="380">
                <option value=""></option>
                <option value="ADD NEW">ADD NEW</option>
                <?php
                $contactid = $_SESSION['contactid'];
                $query = mysqli_query($dbc, "SELECT DISTINCT `subtabid`, `name` FROM `checklist_subtab` WHERE `created_by` = '$contactid' AND `deleted`=0 ORDER BY `name`");
                while ($row = mysqli_fetch_array($query)) {
                    if ($subtab == $row['subtabid']) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='".$row['subtabid']."'>".$row['name'].'</option>';
                }
                ?>
            </select>
            <input type="hidden" name="subtab_name" value="" />
        </div>
    </div>

    <div class="form-group clearfix new_subtab" hidden="true">
        <label for="new_subtab" class="col-sm-4 control-label text-right">
            New Sub Tab:
        </label>
        <div class="col-sm-8">
            <input type="text" name="new_subtab" class="form-control" width="380" />
        </div>
    </div>

    <div class="form-group clearfix">
        <label for="subtab_shared" class="col-sm-4 control-label text-right">
            <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Choose the contacts that you would like to share this Sub Tab with."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            Sub Tab Shared Contacts:
        </label>
        <div class="col-sm-8">
            <select name="subtab_shared[]" id="subtab_shared" multiple data-placeholder="Select Shared Contacts..." class="chosen-select-deselect form-control" onchange="changeSubTabShared(this);">
                <option value='ALL'>Share with Everyone</option>
                <?php
                $cat = '';
                $query1 = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE category='Staff' AND deleted=0 AND status = 1 ORDER BY category"), MYSQLI_ASSOC));
                foreach($query1 as $row1) {
                    echo "<option ".((strpos($subtab_shared, ','.$row1.',') !== false) ? 'selected' : '')." value='". $row1."'>".get_contact($dbc, $row1).'</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="form-group clearfix assign_staff">
        <label for="first_name" class="col-sm-4 control-label text-right">Business:</label>
        <div class="col-sm-8">
            <select name="businessid" id="businessid" data-placeholder="Select a Business..." class="chosen-select-deselect form-control" width="380">
                <option value=''></option>
                <?php
                $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Business' AND deleted=0 ORDER BY category");
                while($row = mysqli_fetch_array($query)) {
                    if ($businessid== $row['contactid']) {
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

    <div class="form-group assign_staff">
      <label for="site_name" class="col-sm-4 control-label">Project Name:</label>
      <div class="col-sm-8">
        <select data-placeholder="Select a Project..." name="projectid" id="projectid"  class="chosen-select-deselect form-control" width="380">
          <option value=""></option>
          <?php $project_tabs = get_config($dbc, 'project_tabs');
			if($project_tabs == '') {
				$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
			}
			$project_tabs = explode(',',$project_tabs);
			$project_vars = [];
			foreach($project_tabs as $item) {
				$project_vars[] = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
			}
            $query = mysqli_query($dbc,"SELECT * FROM (SELECT projectid, projecttype, project_name FROM project WHERE businessid= '$businessid' AND deleted=0 UNION SELECT CONCAT('C',`projectid`), 'Client Project', `project_name` FROM `client_project` WHERE `deleted`=0) PROJECTS ORDER BY project_name");
            while($row = mysqli_fetch_array($query)) {
				if(substr($row['projectid'],0,1) == 'C') {
					echo "<option ".($client_projectid == $row['projectid'] ? 'selected' : '')." value='".$row['projectid']."'>Client Project: ".$row['project_name'].'</option>';
				} else {
					foreach($project_vars as $key => $type_name) {
						if($type_name == $row['projecttype']) {
							echo "<option ".($projectid == $row['projectid'] ? 'selected' : '')." value='".$row['projectid']."'>".$project_tabs[$key].': '.$row['project_name'].'</option>';
						}
					}
				}
            }
          ?>
        </select>
      </div>
    </div>

    <div class="form-group clearfix assign_staff">
        <label for="first_name" class="col-sm-4 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to assign specific staff members. They will be able to view and edit this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Assign to Staff:
		</label>
        <div class="col-sm-8">
            <select name="assign_staff[]" multiple data-placeholder="Select Assigned Staff..." class="chosen-select-deselect form-control" onchange="changeAssignedStaff(this);">
                <option value='ALL'>Assign All Staff</option>
                <?php
                $cat = '';
                $query1 = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE category='Staff' AND deleted=0 AND status = 1 ORDER BY category"), MYSQLI_ASSOC));
				foreach($query1 as $row1) {
                    echo "<option ".((strpos($assign_staff, ','.$row1.',') !== false) || ($row1 == $_SESSION['contactid']) ? 'selected' : '')." value='". $row1."'>".get_contact($dbc, $row1).'</option>';
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
            <select data-placeholder="Choose a Type..." name="checklist_type" class="chosen-select-deselect form-control" width="380" onchange="changeType(this.value);">
                <option value=""></option>
                <option <?php if($checklist_type == 'ongoing') { echo "selected"; } ?> value="ongoing">Ongoing</option>
				<option <?php if($checklist_type == 'daily') { echo "selected"; } ?> value="daily">Daily</option>
				<option <?php if($checklist_type == 'weekly') { echo "selected"; } ?> value="weekly">Weekly</option>
				<option <?php if($checklist_type == 'monthly') { echo "selected"; } ?> value="monthly">Monthly</option>
            </select>
        </div>
    </div>
	<script>
	function changeType(type) {
		if(type == 'ongoing') {
			$('.reset_time').hide();
		} else {
			$('.reset_time').show();
			if(type == 'daily') {
				$('.reset_daily_time').show();
				$('.reset_weekly_time').hide();
				$('.reset_monthly_time').hide();
			} else if(type == 'weekly') {
				$('.reset_daily_time').hide();
				$('.reset_weekly_time').show();
				$('.reset_monthly_time').hide();
			} else if(type == 'monthly') {
				$('.reset_daily_time').hide();
				$('.reset_weekly_time').hide();
				$('.reset_monthly_time').show();
			}
		}
	}
	$(document).ready(function() {
		$('[name=checklist_type]').change();
	});
	</script>

    <div class="form-group clearfix reset_time" style="display:none;">
        <label for="first_name" class="col-sm-4 control-label text-right">
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Daily, Weekly, and Monthly checklists will roll over to unchecked at the specified reset time."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			Reset Timing:
		</label>
        <div class="col-sm-8">
			<div class="form-group clearfix reset_daily_time">
				<label for="first_name" class="col-sm-4 control-label text-right">
					Time of Day:
				</label>
				<div class="col-sm-8">
					<input type="text" name="checklist_reset_day_time" value="<?php echo $checklist_reset_time; ?>" class="form-control datetimepicker" width="380" />
				</div>
			</div>
			<div class="form-group clearfix reset_weekly_time">
				<label for="first_name" class="col-sm-4 control-label text-right">
					Day of the Week:
				</label>
				<div class="col-sm-8">
					<select name="checklist_reset_week_day" class="form-control chosen-select-deselect" width="380" /><option></option>
						<?php $weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
						for($i = 0; $i < 7; $i++) {
							echo "<option ".($checklist_reset_day == $i ? 'selected' : '')." value='$i'>".$weekdays[$i]."</option>";
						} ?>
					</select>
				</div>
				<label for="first_name" class="col-sm-4 control-label text-right">
					Time of Day:
				</label>
				<div class="col-sm-8">
					<input type="text" name="checklist_reset_week_time" value="<?php echo $checklist_reset_time; ?>" class="form-control datetimepicker" width="380" />
				</div>
			</div>
			<div class="form-group clearfix reset_monthly_time">
				<label for="first_name" class="col-sm-4 control-label text-right">
					Day of the Month:
				</label>
				<div class="col-sm-8">
					<select name="checklist_reset_month_day" class="form-control chosen-select-deselect" width="380" /><option></option>
						<?php for($i = 1; $i <= 28; $i++) {
							echo "<option ".($checklist_reset_day == $i ? 'selected' : '')." value='$i'>$i</option>";
						} ?>
					</select>
				</div>
				<label for="first_name" class="col-sm-4 control-label text-right">
					Time of Day:
				</label>
				<div class="col-sm-8">
					<input type="text" name="checklist_reset_month_time" value="<?php echo $checklist_reset_time; ?>" class="form-control datetimepicker" width="380" />
				</div>
			</div>
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
    <?php // }

    if(!empty($_GET['checklistid'])) {
		$query_check_credentials = "SELECT * FROM checklist_document WHERE checklistid='$checklistid' ORDER BY checklistdocid DESC";
		$result = mysqli_query($dbc, $query_check_credentials);
		$num_rows = mysqli_num_rows($result);
		if($num_rows > 0) {
			echo "<table class='table table-bordered' style='width:100%;'>
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
	} ?>

    <div class="form-group">
        <label for="additional_note" class="col-sm-4 control-label">Upload Document(s):
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="File name cannot contain apostrophes, quotations or commas"><img src="<?php echo WEBSITE_URL;?>/img/info.png" width="20"></a>
                </span>
        </label>
        <div class="col-sm-8">
			<div class="form-group clearfix">
				<div class="col-sm-5">
					<input name="upload_document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
				</div>
			</div>
        </div>
    </div>

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
                            $query_check_credentials = "SELECT * FROM checklist_name WHERE checklistid='$checklistid' AND `deleted`=0 ORDER BY priority";
                            $result = mysqli_query($dbc, $query_check_credentials);
                            $num_rows = mysqli_num_rows($result);
                            if($num_rows > 0) {
                                while($row = mysqli_fetch_array($result)) {
                                    $checked = '';
                                    if($row['checked'] == 1) {
                                        $checked = ' checked';
                                    }
                                    echo '<input disabled type="checkbox" '.$checked.' value="" style="" ><input type="text" name="checklist_update[]" class="form-control" value= "';
									echo explode('<p>',html_entity_decode($row['checklist']))[0];
									echo '" style="width: 80%; display:inline;" width="380" /><a class="btn brand-btn" href=\'../delete_restore.php?action=delete&checklistnameid='.$row['checklistnameid'].'&checklistid='.$row['checklistid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a><br>';
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
                        <input type="text" id="first_task" name="checklist[]" class="form-control" style="width: 95%; display:inline;" width="380" />
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
			<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="If you click this, the current Checklist will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a href="checklist.php" class="btn brand-btn btn-lg">Back</a>
		</div>
		<div class="col-sm-6">
			<button name="tasklist" value="tasklist" class="btn brand-btn btn-lg pull-right">Submit</button>
			<span class="popover-examples list-inline pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save the Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
        </div>
    </div>

    </form>

</div>
</div>
