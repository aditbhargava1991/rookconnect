<?php include_once('../include.php');
$security = get_security($dbc, 'checklist');
if(isset($_POST['from']) && $_POST['from'] == 'project') {
	$url = '/Project/review_project.php?type=checklist&projectid='.$projectid;
} else if(isset($_GET['from_url'])) {
	$url = urldecode($_GET['from_url']);
} else {
	$url = '?'.($_GET['edit'] > 0 ? 'view='.$_GET['edit'] : '');
}
if($_GET['tab'] == 'checklists' && $_GET['status'] == 'project') {
	$checklistid = $_GET['checklistid'];
	$projectid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
	$businessid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `businessid` FROM `project` WHERE `projectid`='$projectid'"))['businessid'];
	unset($_GET['checklistid']);
	$url = "?".http_build_query($_GET);
	$_GET['edit'] = $checklistid;
}
if (isset($_POST['tasklist'])) {
    $created_by = $_SESSION['contactid'];
    $projectid = $_POST['projectid'];
	$client_projectid = '';
	if(substr($projectid,0,1) == 'C') {
		$client_projectid = substr($projectid,1);
		$projectid = '';
	}
    if($projectid == '') {
		$projectid = 0;
    }
    $project_milestone = filter_var($_POST['project_milestone'],FILTER_SANITIZE_STRING);
	$ticketid = filter_var(implode(',',$_POST['ticketid']),FILTER_SANITIZE_STRING);
    $businessid = $_POST['businessid'];

    if ($_POST['subtab'] == 'ADD NEW') {
        $subtab_name = $_POST['new_subtab'];
        $subtab_shared = ','.implode(',',$_POST['subtab_shared']).',';
        $query_insert_subtab = "INSERT INTO `checklist_subtab` (`name`, `created_by`, `shared`) VALUES ('$subtab_name', '$created_by', '$subtab_shared')";
        $result_insert_subtab = mysqli_query($dbc, $query_insert_subtab);
        $subtabid = mysqli_insert_id($dbc);

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Added Checklist Sub Tab <b>'.$subtab_name.'</b> on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '', '$subtab_name', '', '', '$subtabid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

				$before_change = '';
				$start_word = strpos($report, "Updated");
				$end_word = strpos($report, " on");
				$history = substr($report, $start_word, $end_word - $start_word) . "<br />";
				add_update_history($dbc, 'checklist_history', $history, '', $before_change);

        $update_tab_config = ",".$subtabid."_ongoing,".$subtabid."_daily,".$subtabid."_weekly,".$subtabid."_monthly";
        foreach ($_POST['subtab_shared'] as $tabs_config_row) {
            $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'checklist_tabs_" . $tabs_config_row . "' FROM (SELECT COUNT(*) numrows FROM `general_configuration` WHERE `name`='checklist_tabs_" . $tabs_config_row . "') current_config WHERE numrows=0");
            $query_tab_config = "UPDATE `general_configuration` SET `value` = CONCAT(`value`, '$update_tab_config') WHERE `name` = 'checklist_tabs_" . $tabs_config_row . "'";
            $result_tab_config = mysqli_query($dbc, $query_tab_config);
        }
        $result = mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'checklist_tabs_" . $_SESSION['contactid'] . "' FROM (SELECT COUNT(*) numrows FROM `general_configuration` WHERE `name`='checklist_tabs_" . $_SESSION['contactid'] . "') current_config WHERE numrows=0");
        $query_tab_config = "UPDATE `general_configuration` SET `value` = CONCAT(`value`, '$update_tab_config') WHERE `name` = 'checklist_tabs_" . $_SESSION['contactid'] . "'";
        $result_tab_config = mysqli_query($dbc, $query_tab_config);
    } else {
        $subtabid = filter_var($_POST['subtab'],FILTER_SANITIZE_STRING);
        //$query_retrieve_subtab = "SELECT * FROM `checklist_subtab` WHERE `subtabid` = '$subtabid'";
        //$result_retrieve_subtab = mysqli_fetch_array(mysqli_query($dbc, $query_retrieve_subtab));
        //$subtab_name = $result_retrieve_subtab['name'];
        //$subtab_shared = ','.implode(',',$_POST['subtab_shared']).',';
        //
        //if ($subtab_shared != $result_retrieve_subtab['shared']) {
        //    $query_update_subtab = "UPDATE `checklist_subtab` SET `name` = '$subtab_name', `shared` = '$subtab_shared' WHERE `subtabid` = '$subtabid'";
        //    $result_retrieve_subtab = mysqli_fetch_array(mysqli_query($dbc, $query_update_subtab));
        //
        //    $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Updated Checklist Sub Tab <b>'.$subtab_name.'</b> on '.date('Y-m-d');
        //    $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '', '$subtab_name', '', '', '$subtabid')";
        //    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
        //}
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

    if($_POST['checklistid'] == 'NEW') {
        if($subtabid == '') {
            $subtabid = 0;
        }
        $query_insert_ca = "INSERT INTO `checklist` (`subtabid`, `assign_staff`, `checklist_type`, `reset_day`, `reset_time`, `checklist_name`, `created_by`, `projectid`, `project_milestone`, `client_projectid`, `ticketid`, `businessid`) VALUES ('$subtabid', '$assign_staff', '$checklist_type', '$reset_day', '$reset_time', '$checklist_name', '$created_by', '$projectid', '$project_milestone', '$client_projectid', '$ticketid', '$businessid')";

        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
        $checklistid = mysqli_insert_id($dbc);

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Added Checklist <b>'.$checklist_name.'</b> in '.$subtab_name.' : '.$checklist_type.' on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '$subtab_name', '$checklist_type', '$checklistid', '$subtabid'";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

				$before_change = '';
				$start_word = strpos($report, "Updated");
				$end_word = strpos($report, " on");
				$history = substr($report, $start_word, $end_word - $start_word) . "<br />";
				add_update_history($dbc, 'checklist_history', $history, '', $before_change);

        insert_day_overview($dbc, $_SESSION['contactid'], 'Checklist', date('Y-m-d'), '', 'Created Checklist '.$checklist_name, $checklistid);

    } else {
        $checklistid = $_POST['checklistid'];

				$before_change = capture_before_change($dbc, 'checklist', 'assign_staff', 'checklistid', $checklistid);
				$before_change .= capture_before_change($dbc, 'checklist', 'checklist_type', 'checklistid', $checklistid);
				$before_change .= capture_before_change($dbc, 'checklist', 'reset_day', 'checklistid', $checklistid);
				$before_change .= capture_before_change($dbc, 'checklist', 'reset_time', 'checklistid', $checklistid);
				$before_change .= capture_before_change($dbc, 'checklist', 'checklist_name', 'checklistid', $checklistid);
				$before_change .= capture_before_change($dbc, 'checklist', 'created_by', 'checklistid', $checklistid);
				$before_change .= capture_before_change($dbc, 'checklist', 'projectid', 'checklistid', $checklistid);
				$before_change .= capture_before_change($dbc, 'checklist', 'ticketid', 'checklistid', $checklistid);
				$before_change .= capture_before_change($dbc, 'checklist', 'client_projectid', 'checklistid', $checklistid);
				$before_change .= capture_before_change($dbc, 'checklist', 'businessid', 'checklistid', $checklistid);

        $query_update_vendor = "UPDATE `checklist` SET `subtabid` = '$subtabid', `assign_staff` = '$assign_staff', `checklist_type` = '$checklist_type', `reset_day` = '$reset_day', `reset_time` = '$reset_time', `checklist_name` = '$checklist_name', `created_by` = '$created_by', `projectid` = '$projectid', `project_milestone` = '$project_milestone', `ticketid`='$ticketid', `client_projectid` = '$client_projectid', `businessid` = '$businessid' WHERE `checklistid` = '$checklistid'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Updated Checklist <b>'.$checklist_name.'</b> in '.$subtab_name.' : '.$checklist_type.' on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '$subtab_name', '$checklist_type', '$checklistid', '$subtabid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

				$start_word = strpos($report, "Updated");
				$end_word = strpos($report, " on");
				$history = substr($report, $start_word, $end_word - $start_word) . "<br />";
				add_update_history($dbc, 'checklist_history', $history, '', $before_change);

        insert_day_overview($dbc, $_SESSION['contactid'], 'Checklist', date('Y-m-d'), '', 'Updated Checklist '.$checklist_name, $checklistid);
    }
    // if(!empty($_POST['project_milestone'])) {
    //     mysqli_query($dbc, "UPDATE `checklist` SET `project_milestone` = '".filter_var($_POST['project_milestone'],FILTER_SANITIZE_STRING)."' WHERE `checklistid` = '$checklistid'");
    //     if(!($projectid > 0)) {
    //         mysqli_query($dbc, "UPDATE `checklist` SET `projectid` = '".filter_var($_POST['projectid_from_path'],FILTER_SANITIZE_STRING)."' WHERE `checklistid` = '$checklistid'");
    //     }
    // }
    if(!empty($_POST['sales_milestone'])) {
        mysqli_query($dbc, "UPDATE `checklist` SET `sales_milestone` = '".filter_var($_POST['sales_milestone'],FILTER_SANITIZE_STRING)."' WHERE `checklistid` = '$checklistid'");
    }
    if(!empty($_POST['salesid'])) {
        mysqli_query($dbc, "UPDATE `checklist` SET `salesid` = '".filter_var($_POST['salesid'],FILTER_SANITIZE_STRING)."' WHERE `checklistid` = '$checklistid'");
    }
    if(!empty($_POST['add_to_taskboard'])) {
        mysqli_query($dbc, "UPDATE `checklist` SET `task_milestone_timeline` = '".filter_var($_POST['task_milestone_timeline'],FILTER_SANITIZE_STRING)."', `task_path` = '".filter_var($_POST['task_path'],FILTER_SANITIZE_STRING)."', `task_board` = '".filter_var($_POST['task_board'],FILTER_SANITIZE_STRING)."' WHERE `checklistid` = '$checklistid'");
    }

    if(!empty($_POST['checklistid'])) {
			$before_change = capture_before_change($dbc, 'checklist', 'checklist', 'checklistnameid', $checklistnameid);

        for($i = 0; $i < count($_POST['checklist_update']); $i++) {
            $checklist = filter_var($_POST['checklist_update'][$i],FILTER_SANITIZE_STRING);
            $checklistnameid = $_POST['checklistid_update'][$i];
            $query_update_vendor = "UPDATE `checklist_name` SET `checklist` = CONCAT('$checklist',SUBSTRING(`checklist`,POSITION('".htmlentities("<p>")."' IN checklist))) WHERE `checklistnameid` = '$checklistnameid'";
            $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
        }

        $report = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' Updated Checklist Items in <b>'.$checklist_name.'</b> on '.date('Y-m-d');
        $query_insert_ca = "INSERT INTO `checklist_report` (`report`, `user`, `date`, `checklist_name`, `subtab_name`, `checklist_type`, `checklistid`, `subtabid`) VALUES ('$report', '".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."', '".date('Y-m-d')."', '$checklist_name', '', '', '$checklistid', '$subtabid')";
        $result_insert_ca = mysqli_query($dbc, $query_insert_ca);

				$start_word = strpos($report, "Updated");
				$end_word = strpos($report, " on");
				$history = substr($report, $start_word, $end_word - $start_word) . "<br />";
				add_update_history($dbc, 'checklist_history', $history, '', $before_change);
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

				$before_change = '';
				$start_word = strpos($report, "Updated");
				$end_word = strpos($report, " on");
				$history = substr($report, $start_word, $end_word - $start_word) . "<br />";
				add_update_history($dbc, 'checklist_history', $history, '', $before_change);
    }

    for($i = 0; $i < count($_FILES['upload_document']['name']); $i++) {
        $document = htmlspecialchars($_FILES["upload_document"]["name"][$i], ENT_QUOTES);

        move_uploaded_file($_FILES["upload_document"]["tmp_name"][$i], "download/".$_FILES["upload_document"]["name"][$i]) ;

        if($document != '') {
            $query_insert_client_doc = "INSERT INTO `checklist_document` (`checklistid`, `type`, `document`, `created_date`, `created_by`) VALUES ('$checklistid', 'Support Document', '$document', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
        }
    }
	foreach($_POST['add_link'] as $link) {
		$link = filter_var($link, FILTER_SANITIZE_STRING);
		if($link != '') {
            $query_insert_client_doc = "INSERT INTO `checklist_document` (`checklistid`, `type`, `link`, `created_date`, `created_by`) VALUES ('$checklistid', 'Support Link', '$link', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
            $result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
		}
	}
	if(strip_tags($_POST['add_notes']) !== '') {
		$checklist_notes = filter_var(htmlentities($_POST['add_notes']),FILTER_SANITIZE_STRING);
		$query_insert_client_doc = "INSERT INTO `checklist_document` (`checklistid`, `type`, `notes`, `created_date`, `created_by`) VALUES ('$checklistid', 'Comments', '$checklist_notes', '".date('Y/m/d')."', '".$_SESSION['contactid']."')";
		$result_insert_client_doc = mysqli_query($dbc, $query_insert_client_doc);
	}

	if($error_list != '') {
		echo $error_list;
	} else if($_POST['reload_ticket_checklists'] == 1) {
        echo '<script type="text/javascript"> window.parent.reload_checklists(); </script>';
    } else {
		echo '<script type="text/javascript"> window.location.replace("'.$url.'"); </script>';
	}
} ?>
<script>
$(document).ready(function () {
	$("#businessid").change(function() {
        var businessid = $("#businessid").val();
		$.ajax({
			type: "GET",
			url: "../Checklist/checklist_ajax.php?fill=projectname&businessid="+businessid,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#projectid').html(response);
				$("#projectid").trigger("change.select2");
			}
		});
		$.ajax({
			type: "GET",
			url: "../Checklist/checklist_ajax.php?fill=ticket_list&businessid="+businessid,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$('#ticketid').html(response);
				$("#ticketid").trigger("change.select2");
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
					window.location.href = "add_task.php";
				}
			});
		}
	});

    $('[name="checklist[]"]').bind("keypress", {}, function (e) {
        if (e.keyCode == 13) {
            add_new_task_row();
            return false;
        }
    });

    $('#sortable_items').sortable({
        update: function( event, ui ) {
			var checklistnameid = ui.item.attr("id");
			var after_checklistnameid = ui.item.prev().attr('id');
			$.ajax({
				type: "GET",
				url: "../Checklist/checklist_ajax.php?fill=checklist_priority&checklistnameid="+checklistnameid+"&after_checklistnameid="+after_checklistnameid,
				dataType: "html",
				success: function(response){
				}
			});
		}
    });
});

$(document).on('change', 'select[name="subtab_shared[]"]', function() { changeSubTabShared(this); });
$(document).on('change', 'select[name="checklist_type"]', function() { changeType(this.value); });
$(document).on('change', 'select[name="assign_staff[]"]', function() { changeAssignedStaff(this); });
$(document).on('change', 'select[name="subtab"]', function() { changeSubTab(this); });
$(document).on('click', '#add_row_doc, .add_row_doc', function() {
    add_new_task_row();
    return false;
});
$(document).on('click', '.add_row_doc2', function() {
    var clone = $(this).closest('.form-group').clone();
	clone.find('.col-xs-1').first().remove();
	clone.find('.col-sm-10').addClass('col-md-10 col-sm-11 col-xs-10').removeClass('col-md-9 col-sm-10 col-xs-9');
	clone.find('input').attr('name', 'checklist[]');
	clone.find('input').val('');
    clone.find('#item_remove').contents().unwrap();
    clone.find('.item_remove_img').addClass('cursor-hand').attr('onclick', 'removeNewRow2(this)');
    clone.find('img.drag_handle').remove();
	$(this).closest('.form-group').append(clone);
	clone.find('input').focus();
    return false;
});

function add_new_task_row() {
	var clone = $('.checklist .additional_doc').first().clone();
	clone.find('input').val('').removeAttr('checked');
	clone.find('.popover-examples').css('display', 'none');
	clone.find('#add_row_doc').css('display', 'none');
	$('#add_here_new_doc').append(clone);
	$('#add_here_new_doc').find('input').last().focus();
	if($('.block-group').height() > $('.block-panels').height()+200) {
		$('.conditional_button').show();
	} else {
		$('.conditional_button').hide();
	}
	$('[name="checklist[]"]').off('keypress').bind("keypress", {}, function (e) {
        if (e.keyCode == 13) {
            add_new_task_row();
            return false;
        }
    });
}

function changeAssignedStaff(sel) {
    if($(sel).find('option[value="ALL"]').is(':selected')) {
        $(sel).find('option').removeAttr('selected');
        $(sel).find('option[value="ALL"]').prop('selected','selected');
        $(sel).trigger('change.select2');
    }
}

function changeSubTabShared(sel) {
    if($(sel).find('option[value="ALL"]').is(':selected')) {
        $(sel).find('option').removeAttr('selected');
        $(sel).find('option[value="ALL"]').prop('selected','selected');
        $(sel).trigger('change.select2');
    }
}
function changeSubTab(sel) {
	if(sel.value == 'ADD NEW') {
		$('.new_subtab').show();
		$('.new_subtab_shared').show();
	} else {
		$('.new_subtab').hide();
		$('.new_subtab_shared').hide();
	}
}
function removeNewRow(button) {
	if($('[name="checklist[]"]').length == 1) {
		$('#add_row_doc').click();
	}

	$(button).closest('.form-group').remove();
	if($('.block-group').height() > $('.block-panels').height()+200) {
		$('.conditional_button').show();
	} else {
		$('.conditional_button').hide();
	}
}
function removeNewRow2(button) {
    $(button).closest('.form-group').remove();
}
</script>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal <?= basename($_SERVER['SCRIPT_FILENAME']) == 'edit_checklist.php' ? 'main-screen' : '' ?>" role="form">
    <?php $task_contactid = $_SESSION['contactid'];

    if(!empty($_GET['edit'])) {
        $checklistid = $_GET['edit'];
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM checklist WHERE checklistid='$checklistid'"));

        $subtab = $get_contact['subtabid'];
        $assign_staff = $get_contact['assign_staff'];
        $checklist_type = $get_contact['checklist_type'];
        $checklist_name = $get_contact['checklist_name'];
        $checklist_reset_day = $get_contact['reset_day'];
        $checklist_reset_time = $get_contact['reset_time'];
		if($_GET['tab'] != 'checklists' && $_GET['status'] != 'project') {
			$projectid = $get_contact['projectid'];
			$client_projectid = 'C'.$get_contact['client_projectid'];
			$businessid = $get_contact['businessid'];
		}
		$ticketid = $get_contact['ticketid'];

        $get_subtab = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist_subtab` WHERE `subtabid` = '$subtab'"));
        $subtab_shared = $get_subtab['shared'];

        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM checklist_name WHERE checklistid='$checklistid' AND `deleted`=0"));

        echo '<input type="hidden" name="checklistid" value="'.$checklistid.'" />';
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
    // if(!empty($_GET['project_milestone'])) {
    //     echo '<input type="hidden" name="project_milestone" value="'.urldecode($_GET['project_milestone']).'">';
    //     echo '<input type="hidden" name="projectid_from_path" value="'.$_GET['projectid'].'">';
    // }
    if(!empty($_GET['salesid'])) {
        echo '<input type="hidden" name="salesid" value="'.$_GET['salesid'].'">';
    }
    if(!empty($_GET['sales_milestone'])) {
        echo '<input type="hidden" name="sales_milestone" value="'.$_GET['sales_milestone'].'">';
    }
    if(!empty($_GET['add_to_taskboard'])) {
        $task_milestone_timeline = $_GET['task_milestone_timeline'];
        $task_milestone_timeline = str_replace("FFMEND","&",$task_milestone_timeline);
        $task_milestone_timeline = str_replace("FFMSPACE"," ",$task_milestone_timeline);
        $task_milestone_timeline = str_replace("FFMHASH","#",$task_milestone_timeline);
        echo '<input type="hidden" name="add_to_taskboard" value="'.$_GET['add_to_taskboard'].'">';
        echo '<input type="hidden" name="task_milestone_timeline" value="'.$task_milestone_timeline.'">';
        echo '<input type="hidden" name="task_path" value="'.$_GET['task_path'].'">';
        echo '<input type="hidden" name="task_board" value="'.$_GET['task_board'].'">';
    }
    if(!empty($_GET['ticketid'])) {
        $ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
        $get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tickets WHERE ticketid='$ticketid'"));

        $businessid = $get_ticket['businessid'];
		$projectid = ($get_ticket['projectid'] > 0 && !($projectid > 0) ? $get_ticket['projectid'] : $projectid);
		$assign_staff = ',ALL,';
    }
    if(!empty($_GET['reload_ticket_checklists'])) {
        echo '<input type="hidden" name="reload_ticket_checklists" value="1">';
    }

    $get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT task FROM field_config"));
    $value_config = ','.$get_field_config['task'].',';
    ?>

    <!--
	<div class="standard-body-title">
        <a href="?<?= $checklistid > 0 ? 'view='.$checklistid : '' ?>" class="show-on-mob pull-left gap-right"><img src="../img/icons/ROOK-back-icon.png" style="height:100%; width:47%;"></a>
        <h3>Settings: <?php /* echo isset($_GET['edit']) && $_GET['edit']=='NEW' ? 'Add' : 'Edit' ?> <?= $checklist_name */ ?></h3>
    </div>
    -->

    <div class="row">
        <h3 class="col-xs-10 pad-left">Settings: <?= isset($_GET['edit']) && $_GET['edit']=='NEW' ? 'Add' : 'Edit' ?> <?= $checklist_name ?></h3>
        <div class="col-xs-2 text-right gap-top"><a href="<?= $url ?>"><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-status-rejected.jpg" class="no-toggle cursor-hand inline-img gap-right" alt="Close" title="Close" data-placement="bottom" /></a></div>
    </div>

    <div class="row">
        <div class="col-sm-12 padded"><input type="text" class="form-control create-input" value="<?= $checklist_name ?>" name="checklist_name" placeholder="Name your checklist..." /></div>
    </div>

	<div class="clearfix"></div>

    <div class="padded">
        <div class="col-xs-12 panel-group block-panels" id="edit_accordions">
            <div class="panel panel-name">
                Checklist Settings
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#edit_accordions" href="#collapse_category">
                            Category<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_category" class="panel-collapse collapse">
                    <div class="panel-body">
                        <div class="form-group clearfix">
                            <label for="first_name" class="col-sm-4 control-label text-right">
                                <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Sub Tab folder of this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                Category:
                            </label>
                            <div class="col-sm-8">
                                <select data-placeholder="Select a Category..." name="subtab" class="chosen-select-deselect form-control" width="380">
                                    <option value=""></option>
                                    <option value="ADD NEW">Add New Category</option>
                                    <?php
                                    $contactid = $_SESSION['contactid'];
                                    $query = mysqli_query($dbc, "SELECT DISTINCT `subtabid`, `name` FROM `checklist_subtab` WHERE (`shared` LIKE '%,$contactid,%' OR `shared` LIKE '%ALL%') AND `deleted`=0 ORDER BY `name`");
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
                        <div class="form-group clearfix new_subtab" style="display:none;">
                            <label for="new_subtab" class="col-sm-4 control-label text-right">
                                New Category:
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="new_subtab" class="form-control" width="380" />
                            </div>
                        </div>
                        <div class="form-group clearfix new_subtab_shared" style="display:none;">
                            <label for="subtab_shared" class="col-sm-4 control-label text-right">
                                <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Choose the contacts that you would like to share this Sub Tab with."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                Category Shared Contacts:
                            </label>
                            <div class="col-sm-8">
                                <select name="subtab_shared[]" id="subtab_shared" multiple data-placeholder="Select Shared Contacts..." class="chosen-select-deselect form-control">
                                    <option value=''></option>
                                    <option value='ALL'>Share with Everyone</option>
                                    <?php
                                    $cat = '';
                                    $query1 = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status>0 ORDER BY category"), MYSQLI_ASSOC));
                                    foreach($query1 as $row1) {
                                        echo "<option ".((strpos($subtab_shared, ','.$row1.',') !== false) ? 'selected' : '')." value='". $row1."'>".get_contact($dbc, $row1).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#edit_accordions" href="#collapse_frequency">
                            Frequency<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_frequency" class="panel-collapse collapse">
                    <div class="panel-body">
                        <div class="form-group clearfix">
                            <label for="first_name" class="col-sm-4 control-label text-right">
                                <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Choose from these options in order to select Checklist type."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                Type:
                            </label>
                            <div class="col-sm-8">
                                <select data-placeholder="Choose a Type..." name="checklist_type" class="chosen-select-deselect form-control" width="380">
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
                            <label for="first_name" class="col-sm-12 clearfix" style="text-align:center;">
                                <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Daily, Weekly, and Monthly checklists will roll over to unchecked at the specified reset time."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                Reset Timing
                            </label>
                            <div class="form-group clearfix reset_daily_time">
                                <label for="first_name" class="col-sm-4 control-label text-right">
                                    Time of Day:
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="checklist_reset_day_time" value="<?php echo $checklist_reset_time; ?>" class="form-control datetimepicker" width="380" />
                                </div>
                            </div>
                            <div class="form-group reset_weekly_time">
                                <label for="first_name" class="col-sm-4 control-label text-right">
                                    Day of the Week:
                                </label>
                                <div class="clearfix col-sm-8">
                                    <select name="checklist_reset_week_day" class="form-control chosen-select-deselect" width="380" /><option></option>
                                        <?php $weekdays = array('Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday');
                                        for($i = 0; $i < 7; $i++) {
                                            echo "<option ".($checklist_reset_day == $i ? 'selected' : '')." value='$i'>".$weekdays[$i]."</option>";
                                        } ?>
                                    </select>
                                </div><div class="clearfix"></div>
                                <label for="first_name" class="col-sm-4 control-label text-right">
                                    Time of Day:
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="checklist_reset_week_time" value="<?php echo $checklist_reset_time; ?>" class="form-control datetimepicker" width="380" />
                                </div><div class="clearfix"></div>
                            </div>
                            <div class="form-group clearfix reset_monthly_time">
                                <label for="first_name" class="col-sm-4 control-label text-right">
                                    Day of the Month:
                                </label>
                                <div class="col-sm-8">

                                    <select name="subtab_shared[]" id="subtab_shared" multiple data-placeholder="Select Shared Contacts..." class="chosen-select-deselect form-control">
                                        <option value='ALL'>Share with Everyone</option>
                                        <?php
                                        $cat = '';
                                        $query1 = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status>0 ORDER BY category"), MYSQLI_ASSOC));
                                        foreach($query1 as $row1) {
                                            echo "<option ".((strpos($subtab_shared, ','.$row1.',') !== false) ? 'selected' : '')." value='". $row1."'>".get_contact($dbc, $row1).'</option>';
                                        }
                                        ?>
                                                              </select>
                                    <select name="checklist_reset_month_day" class="form-control chosen-select-deselect" width="380" /><option></option>
                                        <?php for($i = 1; $i <= 28; $i++) {
                                            echo "<option ".($checklist_reset_day == $i ? 'selected' : '')." value='$i'>$i</option>";
                                        } ?>

                                    </select>
                                </div><div class="clearfix"></div>
                                <label for="first_name" class="col-sm-4 control-label text-right">
                                    Time of Day:
                                </label>
                                <div class="col-sm-8">
                                    <input type="text" name="checklist_reset_month_time" value="<?php echo $checklist_reset_time; ?>" class="form-control datetimepicker" width="380" />
                                </div><div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#edit_accordions" href="#collapse_company">
                            Company<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_company" class="panel-collapse collapse">
                    <div class="panel-body">
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
                    </div>
                </div>
            </div>
            <?php //if(tile_enabled($dbc, 'project')) { ?>
                <!--<div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#edit_accordions" href="#collapse_project">
                                <?= PROJECT_NOUN ?><span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_project" class="panel-collapse collapse">
                        <div class="panel-body">
                            <div class="form-group assign_staff">
                              <label for="site_name" class="col-sm-4 control-label"><?= PROJECT_NOUN ?> Name:</label>
                              <div class="col-sm-8">
                                <select data-placeholder="Select a <?= PROJECT_NOUN ?>..." name="projectid" id="projectid"  class="chosen-select-deselect form-control" width="380">
                                  <option value=""></option>
                                  <?php /* $project_tabs = get_config($dbc, 'project_tabs');
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
                                    } */
                                  ?>
                                </select>
                              </div>
                            </div>
                        </div>
                    </div>
                </div>-->
            <?php //} ?>
				<?php if(tile_enabled($dbc, 'project')) { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#edit_accordions" href="#collapse_project">
									<?= PROJECT_NOUN ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_project" class="panel-collapse collapse">
							<div class="panel-body">
								<div class="form-group assign_staff">
								  <label for="site_name" class="col-sm-4 control-label"><?= PROJECT_NOUN ?> Name:</label>
								  <div class="col-sm-8">
									<select data-placeholder="Select a <?= PROJECT_NOUN ?>..." name="projectid" id="projectid"  class="chosen-select-deselect form-control" width="380">
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
										$query = mysqli_query($dbc,"SELECT * FROM (SELECT projectid, projecttype, project_name FROM project WHERE (businessid= '$businessid' || (`projectid` = '$projectid' AND '$projectid' > 0)) AND deleted=0 UNION SELECT CONCAT('C',`projectid`), 'Client Project', `project_name` FROM `client_project` WHERE `deleted`=0) PROJECTS ORDER BY project_name");
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
                                <?php $tab_config = explode(',',implode(',',array_column(mysqli_fetch_all(mysqli_query($dbc,"SELECT `config_tabs` FROM field_config_project")),0)));
                                if(in_array('Checklists In Path',$tab_config)) { ?>
                                    <script>
                                    $(document).ready(function() {
                                        $('select[name=projectid]').change(function() {
                                            projectid = $(this).val();
                                            $('#project_path').show().load('project_path.php?projectid='+projectid+'&checklistid=<?= $checklistid ?>');
                                        });
                                    });
                                    </script>
                                    <div id="project_path"><?php include('project_path.php'); ?></div>
                                <?php } ?>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php //if(tile_enabled($dbc, 'ticket')) { ?>
					<!--<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#edit_accordions" href="#collapse_ticket_list">
									<?= TICKET_NOUN ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_ticket_list" class="panel-collapse collapse">
							<div class="panel-body">
								<div class="form-group assign_staff">
								  <label for="site_name" class="col-sm-4 control-label"><?= TICKET_NOUN ?>:</label>
								  <div class="col-sm-8">
									<select data-placeholder="Select a <?= TICKET_NOUN ?>..." name="ticketid[]" multiple id="ticketid"  class="chosen-select-deselect form-control">
									  <?php /* $query = mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted`=0 OR `ticketid`='$ticketid'");
										while($row = mysqli_fetch_array($query)) {
											echo "<option ".(in_array($row['ticketid'],explode(',',$ticketid)) ? 'selected' : '')." value='".$row['ticketid']."'>".TICKET_NOUN."# ".$row['ticketid'].' '.$row['heading'].'</option>';
										} */
									  ?>
									</select>
								  </div>
								</div>
							</div>
						</div>
					</div>-->
				<?php //} ?>
            <?php if(tile_enabled($dbc, 'ticket')) { ?>
                <div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#edit_accordions" href="#collapse_ticket_list">
                                <?= TICKET_NOUN ?><span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="collapse_ticket_list" class="panel-collapse collapse">
                        <div class="panel-body">

                            <div class="form-group clearfix assign_staff">
                                <label for="first_name" class="col-sm-4 control-label text-right">
                                    <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to assign specific staff members. They will be able to view and edit this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                    Assign to Staff:
                                </label>
                                <div class="col-sm-8">
                                    <select name="assign_staff[]" multiple data-placeholder="Select Assigned Staff..." class="chosen-select-deselect form-control">
                                        <option <?= strpos($assign_staff,'ALL') !== FALSE ? 'selected' : '' ?> value='ALL'>Assign All Staff</option>
                                        <?php
                                        $cat = '';
                                        $query1 = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status>0 ORDER BY category"), MYSQLI_ASSOC));
                                        foreach($query1 as $row1) {
                                            echo "<option ".((strpos($assign_staff, ','.$row1.',') !== false) || (($row1 == $_SESSION['contactid']) && strpos($assign_staff,'ALL') === FALSE) ? 'selected' : '')." value='". $row1."'>".get_contact($dbc, $row1).'</option>';
                                        }
                                        ?>
                                    </select>
                                </div>
                            </div>

                            <div class="form-group assign_staff">
                              <label for="site_name" class="col-sm-4 control-label"><?= TICKET_NOUN ?>:</label>
                              <div class="col-sm-8">
                                <select data-placeholder="Select a <?= TICKET_NOUN ?>..." name="ticketid[]" multiple id="ticketid"  class="chosen-select-deselect form-control">
                                  <?php $query = mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted`=0 OR `ticketid`='$ticketid'");
                                    while($row = mysqli_fetch_array($query)) {
                                        echo "<option ".(in_array($row['ticketid'],explode(',',$ticketid)) ? 'selected' : '')." value='".$row['ticketid']."'>".TICKET_NOUN."# ".$row['ticketid'].' '.$row['heading'].'</option>';
                                    }
                                  ?>
                                </select>
                              </div>

                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#edit_accordions" href="#collapse_users">
                            Users<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_users" class="panel-collapse collapse">
                    <div class="panel-body">
                        <div class="form-group clearfix assign_staff">
                            <label for="first_name" class="col-sm-4 control-label text-right">
                                <span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to assign specific staff members. They will be able to view and edit this Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                                Share with Staff:
                            </label>
                            <div class="col-sm-8">
                                <select name="assign_staff[]" multiple data-placeholder="Select Assigned Staff..." class="chosen-select-deselect form-control">
                                    <option value=''></option>
                                    <option <?= strpos($assign_staff,'ALL') !== FALSE ? 'selected' : '' ?> value='ALL'>Assign All Staff</option>
                                    <?php
                                    $cat = '';
                                    $query1 = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name, category, email_address FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND status>0 ORDER BY category"), MYSQLI_ASSOC));
                                    foreach($query1 as $row1) {
                                        echo "<option ".((strpos($assign_staff, ','.$row1.',') !== false) || (($row1 == $_SESSION['contactid']) && strpos($assign_staff,'ALL') === FALSE) ? 'selected' : '')." value='". $row1."'>".get_contact($dbc, $row1).'</option>';
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h4 class="panel-title">
                        <a data-toggle="collapse" data-parent="#edit_accordions" href="#collapse_checklist_notes">
                            Notes<span class="glyphicon glyphicon-plus"></span>
                        </a>
                    </h4>
                </div>

                <div id="collapse_checklist_notes" class="panel-collapse collapse">
                    <div class="panel-body">
                        <?php if($checklistid > 0) { ?>
                            <script>
                            function archive_checklist_document(a) {
                                $.ajax({
                                    method: 'POST',
                                    url: '../Checklist/checklist_ajax.php?fill=checklist_doc_remove',
                                    data: { doc: $(a).data('docid')},
                                    success: function() {
                                        $(a).closest('tr').remove();
                                    }
                                });
                            }
                            </script>
                            <?php $query_check_credentials = "SELECT * FROM checklist_document WHERE checklistid='$checklistid' AND `deleted`=0 ORDER BY checklistdocid DESC";
                            $result = mysqli_query($dbc, $query_check_credentials);
                            $num_rows = mysqli_num_rows($result);
                            if($num_rows > 0) {
                                echo "<table class='table table-bordered' style='width:100%;'>
                                <tr class='hidden-xs hidden-sm'>
                                <th>Document / Link / Note</th>
                                <th>Date</th>
                                <th>Added By</th>
                                <th>Function</th>
                                </tr>";
                                while($row = mysqli_fetch_array($result)) {
                                    echo '<tr>';
                                    $by = $row['created_by'];
                                    echo '<td data-title="Document / Link / Note">';
                                    if($row['link'] != '') {
                                        echo '<a href="'.(strpos($row['link'],'http') === FALSE ? 'http://' : '').$row['link'].'" target="_blank">'.$row['link'].'</a>';
                                    } else if($row['document'] != '') {
                                        echo '<a href="download/'.$row['document'].'" target="_blank">'.$row['document'].'</a>';
                                    } else {
                                        echo html_entity_decode($row['notes']);
                                    }
                                    echo '</td>';
                                    echo '<td data-title="Date">'.$row['created_date'].'</td>';
                                    echo '<td data-title="Added By">'.get_staff($dbc, $by).'</td>';
                                    echo '<td data-title="Function"><a href="" data-docid="'.$row['checklistdocid'].'" onclick="archive_checklist_document(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" class="no-toggle" title="Archive" style="height:1.5em;"></a></td>';
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
                                    <input name="upload_document[]" multiple type="file" data-filename-placement="inside" class="form-control" />
                                </div>
                            </div>
                        </div>

                        <div class="form-group links">
                            <label for="additional_note" class="col-sm-4 control-label">Attach Link:</label>
                            <div class="col-sm-7">
                                <div class="form-group clearfix">
                                    <input name="add_link[]" type="text" data-filename-placement="inside" class="form-control" />
                                </div>
                            </div>
                            <div class="col-sm-1">
                                <img class="inline-img" src="../img/icons/ROOK-add-icon.png" onclick="addLink();">
                            </div>
                        </div>
                        <script>
                        function addLink() {
                            var link = $('.links').last();
                            var clone = link.clone();
                            clone.find('input').val('');
                            link.after(clone);
                            $('.links').last().find('input').focus();
                        }
                        </script>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Notes:</label>
                            <div class="col-sm-8">
                                <textarea name="add_notes" class="form-control"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="conditional_button pull-right" style="display:none;">
                <button name="tasklist" value="tasklist" class="btn brand-btn btn-lg pull-right">Submit</button>
                <span class="popover-examples list-inline pull-right" style="margin:15px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save the Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div>
        </div><!-- #edit_accordions -->

        <div class="form-group col-xs-12 block-group" style="margin-top:7px;">
            <div id="sortable_items" class="form-group clearfix">
                <?php
                if($_GET['edit'] > 0) {
                    $query_check_credentials = "SELECT * FROM checklist_name WHERE checklistid='$checklistid' AND `deleted`=0 ORDER BY priority";
                    $result = mysqli_query($dbc, $query_check_credentials);
                    $num_rows = mysqli_num_rows($result);
                    if($num_rows > 0) {
                        while($row = mysqli_fetch_array($result)) {
                            echo '<div class="form-group" id="'. $row['checklistnameid'] .'">';
                            echo '<div class="col-sm-1 col-xs-1">'.($row['checked'] == 1 ? '<input disabled type="checkbox" checked value="" style="height: 1em;"> ' : '').'#'.$row['checklistnameid'].': </div><div class="col-md-9 col-sm-10 col-xs-9">';
                            echo '<input type="text" name="checklist_update[]" class="form-control" value= "'.explode('<p>',html_entity_decode($row['checklist']))[0].'"/></div>';
                            echo '<div class="col-md-2 col-sm-1 col-xs-2">
                                <img src="'.WEBSITE_URL.'/img/icons/ROOK-add-icon.png" class="cursor-hand add_row_doc2" style="height:1.5em;" />
                                <a href=\'../delete_restore.php?action=delete&checklistnameid='.$row['checklistnameid'].'&checklistid='.$row['checklistid'].'\' onclick="return confirm(\'Are you sure you want to archive this item?\')" id="item_remove"><img class="item_remove_img" style="height:1.5em;" src="'.WEBSITE_URL.'/img/remove.png"></a>
                                <img class="drag_handle no-toggle" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" style="margin:0.25em; height:1.25em; width:1.25em;" title="Drag" />
                            </div>';
                            echo '<input type="hidden" name="checklistid_update[]" value="'.$row['checklistnameid'].'" /></div>';

                        }
                    }
                }
                ?>
            </div>
            <div class="checklist clearfix">
                <div class="form-group additional_doc clearfix">
                    <div class="col-md-10 col-sm-11 col-xs-10">
                        <input type="text" id="first_task" name="checklist[]" class="form-control" width="380" />
                    </div>
                    <div class="col-md-2 col-sm-1 col-xs-2 pad-5">
                        <!-- <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png" style="height:1.5em;" onclick="removeNewRow(this);"> -->
                        <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-add-icon.png" class="cursor-hand add_row_doc" style="height:1.5em;" />
                        <img src="<?= WEBSITE_URL ?>/img/remove.png" class="cursor-hand" style="height:1.5em;" onclick="removeNewRow(this);" />
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div id="add_here_new_doc"></div>
                <div id="add_row_doc"></div>
                <!--<button id="add_row_doc" class="btn brand-btn pull-right">Add Checklist</button>
                <span class="popover-examples list-inline pull-right" style="margin:5px 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a field."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>-->
            </div>
        </div><!-- .block-group -->

        <div class="clearfix"></div>

        <div class="form-group clearfix double-gap-top">
            <div class="pull-left">
                <span class="popover-examples list-inline" style="margin:0;"><a data-toggle="tooltip" data-placement="top" title="If you click this, the current Checklist will not be saved."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                <a href="<?= $url ?>" class=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="no-toggle" width="30" /></a>
            </div>
            <div class="pull-right">
                <button name="tasklist" value="tasklist" class="image-btn pull-right no-toggle" title="Save"><img src="../img/icons/save.png" alt="Save" width="30" /></button>
                <span class="popover-examples list-inline pull-right" style="margin:2px -2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to save the Checklist."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            </div>
            <div class="clearfix"></div>
        </div>
    </div><!-- .main-screen-container -->

	<div class="clearfix"></div>
</form>
