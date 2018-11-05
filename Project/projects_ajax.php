<?php include('../include.php');
error_reporting(0);
checkAuthorised();
ob_clean();

if($_GET['action'] == 'mark_favourite') {
	$projectid = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$favourites = explode(',',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `favourite` FROM `project` WHERE `projectid`='$projectid'"))['favourite']);
	if(in_array($_SESSION['contactid'],$favourites)) {
		unset($favourites[array_search($_SESSION['contactid'],$favourites)]);
	} else {
		$favourites = array_merge($favourites,[$_SESSION['contactid']]);
	}
	mysqli_query($dbc, "UPDATE `project` SET `favourite`=',".implode(',',array_filter($favourites)).",' WHERE `projectid`='$projectid'");
	$favourited = mysqli_query($dbc, "SELECT `projectid` FROM `project` WHERE CONCAT(',',`favourite`,',') LIKE '%,".$_SESSION['contactid'].",%'");
	$favourites = [];
	while($favourite = mysqli_fetch_array($favourited)) {
		$favourites[] = $favourite[0];
	}
	echo json_encode($favourites);

} else if($_GET['action'] == 'quick_actions') {

	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);

    if($field == 'reminder') {
            $sender = get_email($dbc, $_SESSION['contactid']);
            $subject = "A reminder about a ".PROJECT_TILE;
            foreach($_POST['users'] as $i => $user) {
                $user = filter_var($user,FILTER_SANITIZE_STRING);
                $contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$user'");
                $body = filter_var(htmlentities("This is a reminder about a ".PROJECT_TILE.".<br />\n<br />
                    <a href='".WEBSITE_URL."/Project/projects.php?edit=$id&tile_name=project'>Click here</a> to see the ".PROJECT_TILE.".<br />\n<br />
                    $item"), FILTER_SANITIZE_STRING);
                mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$user' AND `src_table` = 'project' AND `src_tableid` = '".$id."' AND `src_table` != '' AND `src_table` IS NOT NULL");
                $result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
                    VALUES ('$user', '$value', '08:00:00', 'QUICK', '$subject', '$body', '$sender', 'project', '".$id."')");
            }
            add_update_history($dbc,'project_history',"Reminder added.",'','',$id);
    } else if($field == 'document') {
		$folder = 'download/';
		$basename = preg_replace('/[^\.A-Za-z0-9]/','',$_FILES['file']['name']);
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', $basename);
		for($i = 1; file_exists($folder.$filename); $i++) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.$i.')$1', $basename);
		}
		move_uploaded_file($_FILES['file']['tmp_name'],$folder.$filename);
		mysqli_query($dbc, "INSERT INTO `project_document` (`projectid`,`upload`,`created_by`) VALUES ('$id','$filename','".$_SESSION['contactid']."')");
        add_update_history($dbc,'project_history',"Document: $filename added.",'','',$id);
	} else if($field == 'email') {
		$sender = get_email($dbc, $_SESSION['contactid']);
		$subject = "A reminder about a ".PROJECT_TILE;
		foreach($_POST['value'] as $user) {
			$user = get_email($dbc,$user);
			$body = "This is a reminder about a ".PROJECT_TILE.".<br />\n<br />
                    <a href='".WEBSITE_URL."/Project/projects.php?edit=$id&tile_name=project'>Click here</a> to see the ".PROJECT_TILE.".<br />\n<br />";
			send_email($sender, $user, '', '', $subject, $body, '');
		}
        add_update_history($dbc,'project_history',"Email sent.",'','',$id);
	}
} else if($_GET['action'] == 'setting_status') {
	$status = filter_var(implode('#*#',array_filter($_POST['status'])),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'project_status' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='project_status') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$status' WHERE `name`='project_status'");
	set_config($dbc, 'project_status_pending', $_POST['project_status_pending']);
} else if($_GET['action'] == 'setting_types') {
	$types = filter_var(implode(',',array_filter($_POST['types'])),FILTER_SANITIZE_STRING);

	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'project_tabs' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='project_tabs') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$types' WHERE `name`='project_tabs'");

	$tiles = filter_var($_POST['tiles'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'project_type_tiles' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='project_type_tiles') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$tiles' WHERE `name`='project_type_tiles'");


	set_config($dbc, 'project_type_codes', filter_var(implode(',',$_POST['codes']),FILTER_SANITIZE_STRING));

	set_config($dbc, 'project_type_color', filter_var(implode(',',$_POST['color']),FILTER_SANITIZE_STRING));

} else if($_GET['action'] == 'setting_tabs') {
	$type = filter_var($_POST['projects'],FILTER_SANITIZE_STRING);
	$tabs = filter_var(implode(',',array_filter($_POST['tabs'])),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_project` (`type`) SELECT '$type' FROM (SELECT COUNT(*) rows FROM `field_config_project` WHERE `type`='$type') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config_project` SET `config_tabs`='$tabs' WHERE `type`='$type'");
} else if($_GET['action'] == 'setting_fields') {
	$type = filter_var($_POST['projects'],FILTER_SANITIZE_STRING);
	$fields = filter_var(implode(',',array_filter($_POST['fields'])),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_project` (`type`) SELECT '$type' FROM (SELECT COUNT(*) rows FROM `field_config_project` WHERE `type`='$type') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config_project` SET `config_fields`='$fields' WHERE `type`='$type'");
	$config = filter_var($_POST['detail_config'],FILTER_SANITIZE_STRING);
	if($config != '') {
		$details = filter_var(implode('#*#',array_filter($_POST['details'])),FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT '$config' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='$config') num WHERE num.rows=0");
		mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$details' WHERE `name`='$config'");
	}
} else if($_GET['action'] == 'setting_mandatory_fields') {
	$type = filter_var($_POST['projects'],FILTER_SANITIZE_STRING);
	$fields = filter_var(implode(',',array_filter($_POST['fields'])),FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config_mandatory_project` (`type`) SELECT '$type' FROM (SELECT COUNT(*) rows FROM `field_config_mandatory_project` WHERE `type`='$type') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config_mandatory_project` SET `config_fields`='$fields' WHERE `type`='$type'");
} else if($_GET['action'] == 'setting_tile') {
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT '$field' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='$field') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$value' WHERE `name`='$field'");
} else if($_GET['action'] == 'review_project') {
	$contactid = $_SESSION['contactid'];
	$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
	$query_review = "UPDATE `project` SET `review_date` = CURRENT_TIMESTAMP, `reviewer_id` = '$contactid' WHERE `projectid`='$projectid'";
	$result_review = mysqli_query($dbc, $query_review);
    $user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
	echo "Last Reviewed: ".date('Y-m-d')." by ".$contact_name;
    add_update_history($dbc,'project_history',PROJECT_NOUN." marked as reviewed.",'','',$projectid);
} else if($_GET['action'] == 'project_add_heading') {
	$projectid = filter_var($_POST['project'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `project_scope` (`projectid`) VALUES ('$projectid')");
} else if($_GET['action'] == 'project_actions') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$id_field = filter_var($_POST['id_field'],FILTER_SANITIZE_STRING);
	$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	$pid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
	$new_colour = filter_var($_POST['new_colour'],FILTER_SANITIZE_STRING);
	if($field == 'flag_colour') {
		$colours = [];
		$labels = [];
		/*if($table == 'task_list') {
			$colours = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `flag_colours` FROM `task_dashboard`"))['flag_colours']);
			$labels = explode('#*#', $get_config['flag_names']);
		} else {
			$colours = explode(',', get_config($dbc, "ticket_colour_flags"));
			$labels = explode('#*#', get_config($dbc, "ticket_colour_flag_names"));
		}
		$colour_key = array_search($value, $colours);
		$new_colour = ($colour_key === FALSE ? $colours[0] : ($colour_key + 1 < count($colours) ? $colours[$colour_key + 1] : ($table == 'tasklist' ? 'F2F2F2' : 'FFFFFF')));
		$label = ($colour_key === FALSE ? $labels[0] : ($colour_key + 1 < count($colours) ? $labels[$colour_key + 1] : ''));
		echo $new_colour.html_entity_decode($label);*/
		mysqli_query($dbc, "UPDATE `$table` SET `flag_colour`='$new_colour' WHERE `$id_field`='$id'");

        $note = '<em>Flag added by '.get_contact($dbc, $_SESSION['contactid']).' [PROFILE '.$_SESSION['contactid'].']: '.$value.'</em>';

        mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$refid','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");
        echo $new_colour;
	} else if($field == 'flag_manual') {
		$colour = filter_var($_POST['colour'],FILTER_SANITIZE_STRING);
		$label = filter_var($_POST['label'],FILTER_SANITIZE_STRING);
		$start = filter_var($_POST['start'],FILTER_SANITIZE_STRING);
		$end = filter_var($_POST['end'],FILTER_SANITIZE_STRING);
		if($table == 'tickets') {
			mysqli_query($dbc, "UPDATE `$table` SET `flag_colour`='$colour',`flag_start`='$start',`flag_end`='$end' WHERE `$id_field`='$id'");
			mysqli_query($dbc, "UPDATE `ticket_comment` SET `deleted`=1, `date_of_archival`=DATE(NOW()) WHERE `ticketid`='$id' AND `type`='flag_comment'");
			if(!empty($label)) {
				mysqli_query($dbc, "INSERT INTO `ticket_comment` (`ticketid`,`type`,`comment`,`created_date`,`created_by`) VALUES ('$id','flag_comment','$label',DATE(NOW()),'".$_SESSION['contactid']."')");
			}
		} else {
			mysqli_query($dbc, "UPDATE `$table` SET `flag_colour`='$colour',`flag_label`='$label',`flag_start`='$start',`flag_end`='$end' WHERE `$id_field`='$id'");
		}

        $note = '<em>Manual Flag added by '.get_contact($dbc, $_SESSION['contactid']).' [PROFILE '.$_SESSION['contactid'].']: '.$value.'</em>';

        mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$refid','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");

	} else if($field == 'work_time') {
		if($table == 'tasklist') {
			$time = strtotime($value);
			$total_time = date('H:i:s', $time + strtotime(mysqli_fetch_array(mysqli_query($dbc, "SELECT `work_time` FROM `tasklist` WHERE `tasklistid`='$id'"))['work_time']) - strtotime('00:00:00'));
			$result = mysqli_query($dbc, "UPDATE `tasklist` SET `work_time` = '$total_time' WHERE tasklistid='$id'");
			insert_day_overview($dbc, $_SESSION['contactid'], 'Task', date('Y-m-d'), '', "Updated Task #$id - Added Time : ".$value);
		} else if($table == 'tasklist_time') {
			$hours = (strtotime($value) - strtotime('00:00:00')) / 3600;
			$result = mysqli_query($dbc, "INSERT INTO `tasklist_time` (`tasklistid`, `work_time`, `contactid`, `timer_date`) VALUES ('$id', '$value', '".$_SESSION['contactid']."', '".date('Y-m-d')."')");
			mysqli_query($dbc, "INSERT INTO `time_cards` (`projectid`,`staff`,`date`,`type_of_time`,`total_hrs`,`timer_tracked`,`comment_box`) VALUES ('$pid','".$_SESSION['contactid']."','".date('Y-m-d')."','Regular Hrs.','$hours','0','Time Added on Task #$id')");
			insert_day_overview($dbc, $_SESSION['contactid'], 'Task', date('Y-m-d'), '', "Updated Task #$id on Project #$pid - Added Time: $value");
			$note = '<em>Time added by '.get_contact($dbc, $_SESSION['contactid']).' [PROFILE '.$_SESSION['contactid'].']: '.$value.'</em>';
			echo '<p><small>'.profile_id($dbc, $_SESSION['contactid'], false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right">'.$note.'<em class="block-top-5">Added by '.get_contact($dbc, $_SESSION['contactid']).' at '.date('Y-m-d').'</em></span></small></p>';
			$ref = filter_var($_POST['ref'],FILTER_SANITIZE_STRING);
			$refid = filter_var($_POST['ref_id'],FILTER_SANITIZE_STRING);
			$refidfield = filter_var($_POST['ref_id_field'],FILTER_SANITIZE_STRING);
			$total_time = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT TIME_TO_SEC(SUM(SEC_TO_TIME(`work_time`))) `total` FROM `tasklist_time` WHERE `tasklistid`='$refid'"))['total'];
			mysqli_query($dbc, "UPDATE `$ref` SET `work_time`='$total_time' WHERE `$refidfield`='$refid'");
			mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$refid','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");
		}
	} else if($field == 'document') {
		$folder = filter_var($_POST['folder'],FILTER_SANITIZE_STRING);
		$basename = preg_replace('/[^\.A-Za-z0-9]/','',$_FILES['file']['name']);
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', $basename);
		for($i = 1; file_exists($folder.$filename); $i++) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.$i.')$1', $basename);
		}
		move_uploaded_file($_FILES['file']['tmp_name'],$folder.$filename);
		mysqli_query($dbc, "INSERT INTO `$table` (`$id_field`,`document`,`created_by`,`created_date`) VALUES ('$id','$filename','".$_SESSION['contactid']."',DATE(NOW()))");
		$docid = $dbc->insert_id;
		$note = '<p><small>'.profile_id($dbc, $_SESSION['contactid'], false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right"><a href="'.$folder.$filename.'">'.$filename.'</a><br /><br />';
		$note .= '<em class="block-top-5">Added by '.get_contact($dbc, $_SESSION['contactid']).' at '.date('Y-m-d').'</em></span></small></p>';
		echo str_replace('[PROFILE '.$_SESSION['contactid'].']',profile_id($dbc, $_SESSION['contactid'], false), $note);
		if($table == 'tasklist_document') {
			mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$id','document:$docid','".$_SESSION['contactid']."','".date('Y-m-d')."')");
		}
	} else if($field == 'reminder') {
		$sender = get_email($dbc, $_SESSION['contactid']);
		$result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `$table` WHERE `$id_field` = '$id'"));
		$id = $result['projectid'];
		$item = ($result['checklist'] != '' ? $result['checklist'] : ($result['task'] != '' ? $result['task'] : $result['assign_work']));
		$milestone = ($result['milestone'] != '' ? $result['milestone'] : ($result['milestone_timeline'] != '' ? $result['milestone_timeline'] : $result['project_milestone']));
		$subject = "A reminder about the Project $milestone";
		foreach($_POST['users'] as $i => $user) {
			$user = filter_var($user,FILTER_SANITIZE_STRING);
			$contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$user'");
			$body = filter_var(htmlentities("This is a reminder about the Project $milestone on the Project.<br />\n<br />
				<a href='".WEBSITE_URL."/Project/projects.php?edit=$id'>Click here</a> to see the Project.<br />\n<br />
				$item"), FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "UPDATE `reminders` SET `done` = 1 WHERE `contactid` = '$user' AND `src_table` = '$table' AND `src_tableid` = '".$result[$id_field]."' AND `src_table` != '' AND `src_table` IS NOT NULL");
			$result = mysqli_query($dbc, "INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_time`, `reminder_type`, `subject`, `body`, `sender`, `src_table`, `src_tableid`)
				VALUES ('$user', '$value', '08:00:00', 'QUICK', '$subject', '$body', '$sender', '$table', '".$result[$id_field]."')");

			$note = '<em>Reminder added for '.get_contact($dbc, $user).' [PROFILE '.$user.'] for '.$value.'</em>';
			echo '<p><small>'.profile_id($dbc, $user, false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right">'.$note.'<em class="block-top-5">Added by '.get_contact($dbc, $_SESSION['contactid']).' at '.date('Y-m-d').'</em></span></small></p>';
			$refid = filter_var($_POST['ref_id'],FILTER_SANITIZE_STRING);
			$refidfield = filter_var($_POST['ref_id_field'],FILTER_SANITIZE_STRING);
			mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$refid','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");
		}
	} else if($field == 'alert') {
		$item = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `$table` WHERE `$id_field`='$id'"));
		$projectid = $item['projectid'];
		foreach($_POST['value'] as $user) {
			$user = filter_var($user,FILTER_SANITIZE_STRING);
			$link = WEBSITE_URL."/Project/projects.php?edit=$projectid";
			$text = "Project";
			$date = date('Y/m/d');
			$sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', '$text', '$user')");
		}
		if($table == 'tasklist') {
			$alerts = filter_var(implode(',',$_POST['value']),FILTER_SANITIZE_STRING);
			$contact = filter_var($_POST['value'][0],FILTER_SANITIZE_STRING);
			$sql = mysqli_query($dbc, "UPDATE `$table` SET `alerts_enabled`='".$alerts."', `contactid`='$contact' WHERE `$id_field`='$id'");
		} else {
			$alerts = filter_var(implode(',',$_POST['value']),FILTER_SANITIZE_STRING);
			$sql = mysqli_query($dbc, "UPDATE `$table` SET `alerts_enabled`='".$alerts."' WHERE `$id_field`='$id'");
		}
		if($table == 'tasklist') {
			$assignees = [];
			foreach($_POST['value'] as $assignee) {
				if($assignee > 0) {
					$assignees[] = get_contact($dbc, $assignee);
				}
			}
			$note = "<em>Assigned to ".implode(', ',$assignees)." by ".get_contact($dbc, $_SESSION['contactid'])." [PROFILE ".$_SESSION['contactid']."]</em>";
			echo '<p><small>'.profile_id($dbc, $user, false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right">'.$note.'<em class="block-top-5">Added by '.get_contact($dbc, $_SESSION['contactid']).' at '.date('Y-m-d').'</em></span></small></p>';
			mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$id','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");
		}
	} else if($field == 'email') {
		$sender = get_email($dbc, $_SESSION['contactid']);
		$result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `$table` WHERE `$id_field`='$id'"));
		$refid = $id;
		$item = ($result['checklist'] != '' ? $result['checklist'] : ($result['task'] != '' ? $result['task'] : $result['assign_work']));
		$milestone = ($result['milestone'] != '' ? $result['milestone'] : ($result['milestone_timeline'] != '' ? $result['milestone_timeline'] : $result['project_milestone']));
		$subject = "A reminder about the Project $milestone";
		$id = $result['projectid'];
		foreach($_POST['value'] as $user) {
			$user = get_email($dbc,$user);
			$body = "This is a reminder about the Project $milestone on the Project.<br />\n<br />
				<a href='".WEBSITE_URL."/Project/projects.php?edit=$id'>Click here</a> to see the Project.<br />\n<br />
				$item";
			send_email($sender, $user, '', '', $subject, $body, '');
		}
		if($table == 'tasklist') {
			$recips = [];
			foreach($_POST['value'] as $recip) {
				if($recip > 0) {
					$recips[] = get_contact($dbc, $recip);
				}
			}
			$note = "<em>Sent by Email to ".implode(', ',$recips)." by ".get_contact($dbc, $_SESSION['contactid'])." [PROFILE ".$_SESSION['contactid']."]</em>";
			echo '<p><small>'.profile_id($dbc, $user, false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right">'.$note.'<em class="block-top-5">Added by '.get_contact($dbc, $_SESSION['contactid']).' at '.date('Y-m-d').'</em></span></small></p>';
			mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$refid','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");
		}
	} else if($field == 'external') {
		$assigned = 1;
		if($value == 'unassign') {
			$value = '';
			$assigned = '';
		}
		$sql = mysqli_query($dbc, "UPDATE `$table` SET `external`='".$value."', `assign_client`='".$assigned."' WHERE `$id_field`='$id'");
		if($table == 'tasklist') {
			$note = "<em>Assigned to $value on External Path by ".get_contact($dbc, $_SESSION['contactid'])." [PROFILE ".$_SESSION['contactid']."]</em>";
			echo '<p><small>'.profile_id($dbc, $user, false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right">'.$note.'</span></small></p>';
			mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$id','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");
		}
	} else if($field == 'comment') {
		if($table == 'intake_comments') {
			mysqli_query($dbc, "INSERT INTO `intake_comments` (`intakeid`, `comment`, `created_by`, `created_date`) VALUES ('$id','".filter_var(htmlentities($value),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");
			echo '<p><small>'.profile_id($dbc, $_SESSION['contactid'], false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right">'.$value.'<em class="block-top-5">Added by '.get_contact($dbc, $_SESSION['contactid']).' at '.date('Y-m-d').'</em></span></small></p>';
		} else if($table == 'tickets') {
			mysqli_query($dbc, "INSERT INTO `ticket_comment` (`ticketid`, `comment`, `created_by`, `created_date`,`type`) VALUES ('$id','".filter_var(htmlentities('<p>'.$value.'</p>'),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."','note')");
			echo '<p><small>'.profile_id($dbc, $_SESSION['contactid'], false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right">'.$value.'<em class="block-top-5">Added by '.get_contact($dbc, $_SESSION['contactid']).' at '.date('Y-m-d').'</em></span></small></p>';
		} else {
			mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$id','".filter_var(htmlentities($value),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");
			echo '<p><small>'.profile_id($dbc, $_SESSION['contactid'], false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right">'.$value.'<em class="block-top-5">Added by '.get_contact($dbc, $_SESSION['contactid']).' at '.date('Y-m-d').'</em></span></small></p>';
		}
	} else if($field == 'track_time') {
        $note = "<em>Tracked time: $value";
        echo '<p><small>'.profile_id($dbc, $_SESSION['contactid'], false).'<span style="display:inline-block; width:calc(100% - 3em);" class="pull-right">'.$note.'<em class="block-top-5">Added by '.get_contact($dbc, $_SESSION['contactid']).' at '.date('Y-m-d').'</em></span></small></p>';
    }
} else if($_GET['action'] == 'project_fields') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$id_field = filter_var($_POST['id_field'],FILTER_SANITIZE_STRING);
	$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);
	$type_field = filter_var($_POST['type_field'],FILTER_SANITIZE_STRING);
	if(is_array($_POST['value'])) {
		$value = filter_var(implode(',',$_POST['value']),FILTER_SANITIZE_STRING);
	} else {
		$value = filter_var(htmlentities($_POST['value']),FILTER_SANITIZE_STRING);
	}
	if($_POST['mode'] == 'append') {
		if($field == 'task') {
			$value .= "<br /><em><small>Reply added by ".get_contact($dbc, $_SESSION['contactid'])." [PROFILE ".$_SESSION['contactid']."] on ".date('Y-m-d')."</small></em>";
			echo '<p>'.$value.'</p>';
		}
		$value = "CONCAT(`$field`,'".htmlentities("<p>$value</p>")."')";
	} else {
		$value = "'".$value."'";
	}
	$project = filter_var($_POST['project'],FILTER_SANITIZE_STRING);
	if(!($id > 0)) {
		$project_info = $dbc->query("SELECT * FROM `project` WHERE `projectid`='$project'")->fetch_assoc();
		mysqli_query($dbc, "INSERT INTO `$table` (`projectid`) VALUES ('$project')");
		$id = mysqli_insert_id($dbc);
        if($table == 'tasklist' && $field == 'heading') {
            $task_path = explode('|', $_POST['task_path'])[1];
            $default_task = get_config($dbc, 'task_default_status');
            mysqli_query($dbc, "UPDATE `tasklist` SET `status` = '$default_task', `task_path` = '$task_path', `task_tododate`='".date('Y-m-d')."', `contactid`='".$_SESSION['contactid']."' WHERE `tasklistid`='$id'");
        }
		if($type != '' && $type_field != '') {
			mysqli_query($dbc, "UPDATE `$table` SET `$type_field`='$type' WHERE `$id_field`='$id'");
		} else if($type != '') {
			mysqli_query($dbc, "UPDATE `$table` SET `type`='$type' WHERE `$id_field`='$id'");
		}
		mysqli_query($dbc, "UPDATE `$table` SET `created_date`='".date('Y-m-d')."' WHERE `$id_field`='$id'");
		mysqli_query($dbc, "UPDATE `$table` SET `created_by`='".$_SESSION['contactid']."' WHERE `$id_field`='$id'");
		$taskboard = get_project_task_board($project);
		$taskboardid = $taskboard['id'];
		$task_path = explode('#*#',$taskboard['path'])[0];
		mysqli_query($dbc, "UPDATE `$table` SET  `contactid`='".$_SESSION['contactid']."', `businessid`='".$project_info['businessid']."', `clientid`='".$project_info['clientid']."', `task_board`='$taskboardid' WHERE `$id_field`='$id' AND 'tasklist' = '$table'");
		mysqli_query($dbc, "UPDATE `$table` SET  `created_by`='".$_SESSION['contactid']."' WHERE `$id_field`='$id'");
		mysqli_query($dbc, "UPDATE `$table` SET `created_date`='".date('Y-m-d')."' WHERE `$id_field`='$id'");
		$history = get_contact($dbc, $_SESSION['contactid'])." added ".($table == 'project' ? PROJECT_NOUN : ($table == 'tasklist' ? 'Task' : $table))." #$id on ".date('Y-m-d h:i a');
		echo $id;
		if($table == 'project') {
			insert_day_overview($dbc, $_SESSION['contactid'], 'Project', date('Y-m-d'), '', 'Added '.PROJECT_NOUN.' #'.$id, $id);
            $project = $id;
		}
	}
	if($table == 'project_actions') {
		if($field == 'completed') {
			$value = htmlentities("Follow Up #$id Completed by ".get_contact($dbc, $_SESSION['contactid'])." on ".date('Y-m-d')."<br />".$value);
			mysqli_query($dbc, "INSERT INTO `project_comment` (`projectid`, `notes`, `created_by`) VALUES ('$project', '$value', '{$_SESSION['contactid']}')");
			$value = 1;
			$history = get_contact($dbc, $_SESSION['contactid'])." completed follow up action $id on ".date('Y-m-d h:i a');
		} else if($field == '') {
			$history = get_contact($dbc, $_SESSION['contactid'])." added follow up action $id on ".date('Y-m-d h:i a');
		} else {
			$history = get_contact($dbc, $_SESSION['contactid'])." set follow up $field to '$value' for action $id on ".date('Y-m-d h:i a');
		}
	} else if($field == 'status' && $table == 'tasklist') {
		$_POST['value'] = empty($_POST['value']) ? explode(',',get_config($dbc,'task_status'))[0] : filter_var($_POST['value'],FILTER_SANITIZE_STRING);
		$note = "<small><em>Marked ".$_POST['value']." by ".get_contact($dbc, $_SESSION['contactid'])." on ".date('Y-m-d')."</em></small>";
		mysqli_query($dbc, "INSERT INTO `task_comment` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$id','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");
		mysqli_query($dbc, "UPDATE `reminders` SET `done` = '".($_POST['value'] > 0 ? 1 : 0)."' WHERE `src_table` = 'tasklist' AND `src_tableid` = '$id'");

		$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `projectid` FROM `tasklist` WHERE `tasklistid` = '$id'"))['projectid'];
		$project_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `project_name` FROM `project` WHERE `projectid` = '$project'"))['project_name'];
		$milestone_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT('Task #',`tasklistid`,': ',`task`) as milestone_name FROM `tasklist` WHERE `tasklistid` = '$id'"))['milestone_name'];
		$milestone_name = explode('&lt;br /&gt;', $milestone_name)[0];
		$milestone_name = explode('&lt;p&gt;', $milestone_name)[0];
        insert_day_overview($dbc, $_SESSION['contactid'], 'Project', date('Y-m-d'), '', 'Updated '.PROJECT_NOUN.' #'.$project.(!empty($project_name) ? ': '.$project_name : '').' - '.($_POST['value'] > 0 ? 'Completed' : 'Unchecked').' '.$milestone_name, $id);
        $history = get_contact($dbc, $_SESSION['contactid']).' Updated '.PROJECT_NOUN.' #'.$project.(!empty($project_name) ? ': '.$project_name : '').' - '.($_POST['value'] > 0 ? 'Completed' : 'Unchecked').' '.$milestone_name;
	} else {
		$history .= ($history == '' ? '' : "<br />\n").get_contact($dbc, $_SESSION['contactid'])." set $field to '$value' on ".date('Y-m-d h:i a');
	}
	mysqli_query($dbc, "UPDATE `$table` SET `$field`=$value WHERE `$id_field`='$id'");
    if($field == 'number_of_days' && $value != '') {
        $today_date = date('Y-m-d');
        $dbc->query("UPDATE `project` SET `number_of_days_start_date`='$today_date' WHERE `$id_field`='$id'");
    }
    $user = decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']);
	add_update_history($dbc,'project_history',$history,'','',$project);

	// Add Services to the Customer Rate Card, if configured
	if($table == 'project_scope' && get_config($dbc, 'project_services_add_to_rates') == 'true') {
		$scope = $dbc->query("SELECT `businessid`, `src_id`, `src_table`, `price` FROM `project_scope` LEFT JOIN `project` ON `project_scope`.`projectid`=`project`.`projectid` WHERE `$id_field`='$id'")->fetch_assoc();
		if($scope['src_table'] == 'services') {
			$rate_card = $dbc->query("SELECT `ratecardid`,`services` FROM `rate_card` WHERE `clientid`='{$scope['businessid']}' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
			if($rate_card->num_rows == 0) {
				$dbc->query("INSERT INTO `rate_card` (`clientid`, `rate_card_name`, `when_added`, `who_added`) VALUES ('{$scope['businessid']}', '".get_contact($dbc, $scope['businessid'],'name_company')."', DATE(NOW()), '{$_SESSION['contactid']}')");
				$rate_card = $dbc->query("SELECT `ratecardid`,`services` FROM `rate_card` WHERE `clientid`='{$scope['businessid']}' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')");
			}
			$rate_card = $rate_card->fetch_assoc();
			$services = explode('**',$rate_card['services']);
			$service_row = '';
			foreach($services as $i => $row) {
				$row = explode('#',$row);
				if($row[0] == $scope['src_id']) {
					$service_row = $i;
					$services[$i] = $scope['src_id'].'#'.$scope['price'];
				}
			}
			if($service_row == '') {
				$services = trim(implode('**',$services).'**'.$scope['src_id'].'#'.$scope['price'],'*');
				$dbc->query("UPDATE `rate_card` SET `services`='$services' WHERE `clientid`='{$scope['businessid']}'");
			}
		}
	}

	// Send Archive Emails for Tickets, if needed
	if($value == 'Archive' && $table == 'tickets' && $field == 'status') {
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$id'"));
		$ticket_config = get_field_config($dbc, 'tickets');
		if($ticket['ticket_type'] != '') {
			$ticket_config .= ','.get_config($dbc, 'ticket_fields_'.$ticket['ticket_type']).',';
		}
		if(strpos($ticket_config,',Send Archive Email,') !== FALSE) {
			$ticket_label = get_ticket_label($dbc, $ticket);
			foreach(explode(',',$ticket['contactid'].','.$ticket['internal_qa_contactid'].','.$ticket['deliverable_contactid']) as $staffid) {
				if($staffid > 0) {
					$email = get_email($dbc, $staffid);
					if($email != '') {
						$subject = $ticket_label." has been Archived";
						$body = "You are receiving this email because you were involved in $ticket_label, and it has been archived.<br />
							To review this ".TICKET_NOUN.", <a href='".WEBSITE_URL."/Ticket/index.php?edit=".$ticket['ticketid']."&tile_name=".$ticket['ticket_type']."'>click here</a>.";
						send_email('', $email, '', '', $subject, $body);
					}
				}
			}
		}
	}

	//Insert into day overview if last edit was not within 15 minutes
	if($table == 'project') {
		$project = $id;
		$day_overview_last = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `timestamp` FROM `day_overview` WHERE `type` = 'Project' AND `tableid` = '$project' AND `contactid` = '".$_SESSION['contactid']."' ORDER BY `timestamp` DESC"));
		$timestamp_now = date('Y-m-d h:i:s');
		$timediff = strtotime($timestamp_now) - strtotime($day_overview_last['timestamp']);
		if($timediff > 900) {
			$project_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `project_name` FROM `project` WHERE `projectid` = '$project'"))['project_name'];
			insert_day_overview($dbc, $_SESSION['contactid'], 'Project', date('Y-m-d'), '', 'Edited '.PROJECT_NOUN.' #'.$project.(!empty($project_name) ? ': '.$project_name : ''), $project);
		}
	}
} else if($_GET['action'] == 'update_path') {
	$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
	$path_list = filter_var(trim($_POST['path_list'],','),FILTER_SANITIZE_STRING);
	$path = filter_var($_POST['path'],FILTER_SANITIZE_STRING);
	$new_path = filter_var($_POST['new_path'],FILTER_SANITIZE_STRING);
	$taskboardid = get_project_task_board($projectid)['id'];
    $project_lead = get_field_value('project_lead','project','projectid',$projectid);

	if($path == 'project_path') {
		$prior_path = explode(',',get_project($dbc, $projectid, 'project_path'));
		$milestones = [];
		foreach(explode(',',$path_list) as $pathid) {
			$template = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project_path_milestone` WHERE `project_path_milestone`='$pathid'"));
            if((empty($new_path) && !in_array($pathid, $prior_path)) || $pathid == $new_path) {
                $tickets = explode('#*#',$_POST['tickets']);
                $tasks = explode('#*#',$_POST['tasks']);
                // $items = explode('#*#',$_POST['items']);
                $intakes = explode('#*#',$_POST['intakes']);
                foreach(explode('#*#',$template['milestone']) as $i => $milestone) {
                    $milestones[] = $milestone;
					foreach(array_filter(explode('*#*',explode('#*#',$template['ticket'])[$i])) as $j => $ticket) {
                        if(in_array($i.'|'.$j,$tickets)) {
                            $ticket = explode('FFMSPLIT',$ticket);
                            $heading = $ticket[0];
                            $serviceid = $ticket[1];
                            $staff = empty($_POST['ticket_staff'][$i.'|'.$j]) ? $project_lead : filter_var($_POST['ticket_staff'][$i.'|'.$j],FILTER_SANITIZE_STRING);
                            mysqli_query($dbc, "INSERT INTO `tickets` (`projectid`, `heading`, `serviceid`, `milestone_timeline`, `contactid`, `to_do_date`) VALUES ('$projectid', '$heading', '$serviceid', '$milestone', '$staff', DATE(NOW()))");
                        }
					}
                    foreach(array_filter(explode('*#*',explode('#*#',$template['checklist'])[$i])) as $j => $task) {
                        if(in_array($i.'|'.$j,$tasks)) {
                            $staff = empty($_POST['task_staff'][$i.'|'.$j]) ? $project_lead : filter_var($_POST['task_staff'][$i.'|'.$j],FILTER_SANITIZE_STRING);
                            mysqli_query($dbc, "INSERT INTO `tasklist` (`task_path`, `task_board`, `projectid`, `contactid`, `heading`, `task`, `project_milestone`, `task_tododate`) VALUES ('$pathid', '$taskboardid', '$projectid', '$staff', '$task', '$task', '$milestone', DATE(NOW()))");
                        }
					}
                    // foreach(array_filter(explode('*#*',explode('#*#',$template['items'])[$i])) as $j => $item) {
                        // if(in_array($i.'|'.$j),$items) {
                            // mysqli_query($dbc, "");
                        // }
					// }
                    foreach(array_filter(explode('*#*',explode('#*#',$template['intakes'])[$i])) as $j => $intake) {
                        if(in_array($i.'|'.$j,$intakes)) {
                            mysqli_query($dbc, "INSERT INTO `intake` (`projectid`, `project_milestone`, `intakeformid`) VALUES ('$projectid', '$milestone', '$intake')");
                        }
					}
				}
			}
		}
		mysqli_query($dbc, "UPDATE `tasklist` SET `project_milestone`='' WHERE `project_milestone` NOT IN ('".implode("','",$milestones)."') AND `projectid`='$projectid' AND `deleted`=0");
		mysqli_query($dbc, "UPDATE `tickets` SET `milestone_timeline`='' WHERE `milestone_timeline` NOT IN ('".implode("','",$milestones)."') AND `projectid`='$projectid' AND `deleted`=0");
		mysqli_query($dbc, "UPDATE `workorder` SET `status`='' WHERE `status` NOT IN ('".implode("','",$milestones)."') AND `projectid`='$projectid' AND `deleted`=0");
        mysqli_query($dbc, "UPDATE task_board SET task_path='$pathid' WHERE taskboardid='$taskboardid'");
	}
	mysqli_query($dbc, "UPDATE `project` SET `$path`='$path_list' WHERE `projectid`='$projectid'");
}  else if($_GET['action'] == 'archive') {
	$date_of_archival = date('Y-m-d');
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `project` SET `status`='Archive', `deleted`=1 WHERE `projectid`='$id'");
    add_update_history($dbc,'project_history',PROJECT_NOUN." archived.",'','',$id);
    $dbc->query("UPDATE `tickets` SET `deleted`=1 WHERE `projectid`='$id'");
    $dbc->query("UPDATE `tasklist` SET `deleted`=1 WHERE `projectid`='$id'");
    $dbc->query("UPDATE `checklist` SET `deleted`=1 WHERE `projectid`='$id'");
    $dbc->query("UPDATE `email_communication` SET `deleted`=1 WHERE `projectid`='$id'");
} else if($_GET['action'] == 'path_template') {
	$id = filter_var($_POST['templateid'],FILTER_SANITIZE_STRING);
	$project_path = filter_var($_POST['template_name'],FILTER_SANITIZE_STRING);
	$milestone = filter_var($_POST['milestone'],FILTER_SANITIZE_STRING);
	$timeline = filter_var($_POST['timeline'],FILTER_SANITIZE_STRING);
	$tasks = filter_var($_POST['tasks'],FILTER_SANITIZE_STRING);
	$ticket = filter_var($_POST['ticket'],FILTER_SANITIZE_STRING);
	$check_lists = filter_var($_POST['check_list'],FILTER_SANITIZE_STRING);
	$intake_forms = filter_var($_POST['intake_form'],FILTER_SANITIZE_STRING);
	$items = filter_var($_POST['items'],FILTER_SANITIZE_STRING);
	//$intakes = filter_var($_POST['intakes'],FILTER_SANITIZE_STRING);
	$intakes = filter_var($_POST['intakes'],FILTER_SANITIZE_STRING);

	if($id == 'new') {
		mysqli_query($dbc, "INSERT INTO `project_path_milestone` () VALUES ()");
		$id = mysqli_insert_id($dbc);
		echo $id;
	}

  // mysqli_query($dbc, "UPDATE `project_path_milestone` SET `project_path`='$project_path',`milestone`='$milestone',`timeline`='$timeline',`checklist`='$tasks',`ticket`='$ticket',`intake_form`='$intake_forms',`intakes`='$intakes', `check_list`='$check_lists' WHERE `project_path_milestone`='$id'");
  mysqli_query($dbc, "UPDATE `project_path_milestone` SET `project_path`='$project_path',`milestone`='$milestone',`timeline`='$timeline',`checklist`='$tasks',`ticket`='$ticket',`intakes`='$intakes' WHERE `project_path_milestone`='$id'");


} else if($_GET['action'] == 'path_template_individual_order') {
	$id = filter_var($_POST['templateid'],FILTER_SANITIZE_STRING);
	$tasks = filter_var($_POST['checklist'],FILTER_SANITIZE_STRING);
	$ticket = filter_var($_POST['ticket'],FILTER_SANITIZE_STRING);
	$workorder = filter_var($_POST['workorder'],FILTER_SANITIZE_STRING);

	if($id == 'new') {
		mysqli_query($dbc, "INSERT INTO `project_path_milestone` () VALUES ()");
		$id = mysqli_insert_id($dbc);
		echo $id;
	}

    if ( $tasks !== '' ) {
        mysqli_query($dbc, "UPDATE `project_path_milestone` SET `checklist`='$tasks' WHERE `project_path_milestone`='$id'");
    }
    if ( $ticket !== '' ) {
        mysqli_query($dbc, "UPDATE `project_path_milestone` SET `ticket`='$ticket' WHERE `project_path_milestone`='$id'");
    }
    if ( $workorder !== '' ) {
        mysqli_query($dbc, "UPDATE `project_path_milestone` SET `workorder`='$workorder' WHERE `project_path_milestone`='$id'");
    }

} else if($_GET['action'] == 'remove_template') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);

	mysqli_query($dbc, "DELETE FROM `project_path_milestone` WHERE `project_path_milestone`='$id'");
} else if($_GET['action'] == 'apply_template') {
	$project = filter_var($_POST['project'],FILTER_SANITIZE_STRING);
	$template = filter_var($_POST['template'],FILTER_SANITIZE_STRING);

	mysqli_query($dbc, "UPDATE `project` SET `project_path`='$template' WHERE `projectid`='$project'");
	mysqli_query($dbc, "UPDATE `tasklist` SET `project_milestone`='' WHERE `projectid`='$project' AND `deleted`=0");
	mysqli_query($dbc, "UPDATE `tickets` SET `milestone_timeline`='' WHERE `projectid`='$project' AND `deleted`=0");
	mysqli_query($dbc, "UPDATE `workorder` SET `status`='' WHERE `projectid`='$project' AND `deleted`=0");

	$template = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project_path_milestone` WHERE `project_path_milestone`='$template'"));
	foreach(explode('#*#',$template['milestone']) as $i => $milestone) {
		foreach(array_filter(explode('*#*',explode('#*#',$template['checklist'])[$i])) as $task) {
			mysqli_query($dbc, "INSERT INTO `tasklist` (`projectid`, `heading`, `task`, `milestone`) VALUES ('$project', '$task', '$task', '$milestone')");
		}
		foreach(array_filter(explode('*#*',explode('#*#',$template['ticket'])[$i])) as $ticket) {
			$ticket = explode('FFMSPLIT',$ticket);
			$heading = $ticket[0];
			$serviceid = $ticket[1];
			mysqli_query($dbc, "INSERT INTO `tickets` (`projectid`, `heading`, `serviceid`, `milestone_timeline`) VALUES ('$project', '$heading', '$serviceid', '$milestone')");
		}
		foreach(array_filter(explode('*#*',explode('#*#',$template['workorder'])[$i])) as $workorder) {
			mysqli_query($dbc, "INSERT INTO `workorder` (`projectid`, `heading`, `milestone_timeline`) VALUES ('$project', '$workorder', '$milestone')");
		}
	}
} else if($_GET['action'] == 'project_uploads') {
	$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$project = filter_var($_POST['project'],FILTER_SANITIZE_STRING);
	$type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);
	$basename = $_FILES['file']['name'];
	if($table == 'project_document') {
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', '$1', $basename);
		$i = 0;
		while(file_exists('download/'.$filename)) {
			$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.++$i.')$1', $basename);
		}
		move_uploaded_file($_FILES['file']['tmp_name'],'download/'.$filename);
		mysqli_query($dbc, "INSERT INTO `project_document` (`projectid`,`upload`,`category`,`created_by`) VALUES ('$project','$filename','$type','".$_SESSION['contactid']."')");
	}
    add_update_history($dbc,'project_history',"Document: $filename added.",'','',$project);
} else if($_GET['action'] == 'create_from_scope') {
	$projectid = filter_var($_POST['projectid'], FILTER_SANITIZE_STRING);
	$description = '';
	foreach($_POST['scope_lines'] as $id) {
		$id = filter_var($id,FILTER_SANITIZE_STRING);
		$line = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project_scope` WHERE `id`='$id'"));
		$description .= $line['qty']." ".$line['uom']." of ";
		switch($line['src_table']) {
		case 'services':
			$description .= mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `services` WHERE `serviceid`='".$line['src_id']."'"))['label'];
			break;
		case 'equipment':
			$description .= mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`make`,''),' ',IFNULL(`model`,''),' ',IFNULL(`label`,''),' ',IFNULL(`unit_number`,'')) label FROM `equipment` WHERE `equipmentid`='".$line['src_id']."'"))['label'];
			break;
		default:
		case 'inventory':
			$description .= mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`product_name`,'')) label FROM `inventory` WHERE `inventoryid`='".$line['src_id']."'"))['label'];
			break;
		default:
		case 'labour':
			$description .= mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`labour_type`,''),' ',IFNULL(`category`,''),' ',IFNULL(`heading`,''),' ',IFNULL(`name`,'')) label FROM `labour` WHERE `labourid`='".$line['src_id']."'"))['label'];
			break;
		default:
		case 'material':
			$description .= mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`sub_category`,''),' ',IFNULL(`name`,'')) label FROM `material` WHERE `materialid`='".$line['src_id']."'"))['label'];
			break;
		default:
		case 'position':
			$description .= mysqli_fetch_array(mysqli_query($dbc, "SELECT `name` label FROM `positions` WHERE `position_id`='".$line['src_id']."'"))['label'];
			break;
		default:
		case 'products':
			$description .= mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `products` WHERE `productid`='".$line['src_id']."'"))['label'];
			break;
		default:
		case 'vpl':
			$description .= mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`product_name`,'')) label FROM `vendor_price_list` WHERE `inventoryid`='".$line['src_id']."'"))['label'];
			break;
		default:
			$description .= $line['description'];
			break;
		}
		$description .= "<br />\n";
	}
	$description = filter_var(htmlentities($description),FILTER_SANITIZE_STRING);
	if($_POST['object'] == 'ticket') {
		mysqli_query($dbc, "INSERT INTO `tickets` (`projectid`,`businessid`,`clientid`,`assign_work`,`created_by`) SELECT `projectid`,`businessid`,`clientid`,'$description','".$_SESSION['contactid']."' FROM `project` WHERE `projectid`='$projectid'");
		$result_id = mysqli_insert_id($dbc);
		foreach($_POST['scope_lines'] as $id) {
			$scope_line = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project_scope` WHERE `id`='$id'"));
			$table = $scope_line['src_table'];
			if($table == 'services') {
				$table = 'service';
			}
			mysqli_query($dbc, "UPDATE `tickets` SET `{$table}id`=CONCAT(`{$table}id`,'{$scope_line['src_id']},') WHERE `ticketid`='$result_id'");
		}
		echo WEBSITE_URL."/Ticket/index.php?edit=".$result_id."&from=".urlencode(WEBSITE_URL.'/Project/projects.php?edit='.$projectid.'&tab=scope');
	} else if($_POST['object'] == 'workorder') {
		mysqli_query($dbc, "INSERT INTO `workorder` (`projectid`,`businessid`,`clientid`,`assign_work`,`created_by`) SELECT `projectid`,`businessid`,`clientid`,'$description','".$_SESSION['contactid']."' FROM `project` WHERE `projectid`='$projectid'");
		$result_id = mysqli_insert_id($dbc);
		foreach($_POST['scope_lines'] as $id) {
			$scope_line = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project_scope` WHERE `id`='$id'"));
			$table = $scope_line['src_table'];
			if($table == 'services') {
				$table = 'service';
			}
			mysqli_query($dbc, "UPDATE `workorder` SET `{$table}id`=CONCAT(`{$table}id`,'{$scope_line['src_id']},') WHERE `ticketid`='$result_id'");
		}
		echo WEBSITE_URL."/Work Order/add_workorder.php?workorderid=".$result_id."&from=".urlencode(WEBSITE_URL.'/Project/projects.php?edit='.$projectid.'&tab=scope');
	} else if($_POST['object'] == 'task') {
		mysqli_query($dbc, "INSERT INTO `tasklist` (`projectid`,`businessid`,`clientid`,`task`,`created_by`) SELECT `projectid`,`businessid`,`clientid`,'$description','".$_SESSION['contactid']."' FROM `project` WHERE `projectid`='$projectid'");
		$result_id = mysqli_insert_id($dbc);
		echo WEBSITE_URL."/Tasks_Updated/add_task.php?tasklistid=".$result_id."&from=".urlencode(WEBSITE_URL.'/Project/projects.php?edit='.$projectid.'&tab=scope');
	}
	foreach($_POST['scope_lines'] as $id) {
		$object = filter_var($_POST['object'],FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "UPDATE `project_scope` SET `attach_type`='$object', `attach_id`='$result_id' WHERE `id`='$id'");
	}
} else if($_GET['action'] == 'create_bill') {
	$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
	$type = filter_var($_POST['object'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `invoice` (`tile_name`,`projectid`,`businessid`,`patientid`,`invoice_date`) SELECT '$type',`projectid`,`businessid`,`clientid`,DATE(NOW()) FROM `project` WHERE `projectid`='$projectid'");
	$final_price = 0;
	$invoiceid = mysqli_insert_id($dbc);
	$bill_lines = json_decode($_POST['bill_lines']);
	foreach($bill_lines as $billable) {
		mysqli_query($dbc, "UPDATE `project_billable` SET `bill_type`='$type', `bill_id`='$invoiceid' WHERE `id`='$billable'");
		$billable = mysqli_fetch_array(mysqli_query($dbc, "SELECT IFNULL(scope.`src_id`,bill.`billable_id`) item_id, IFNULL(scope.`src_table`,bill.`billable_table`) category, bill.`description`, bill.`qty`, bill.`price`, bill.`uom`, bill.`retail` FROM `project_billable` bill LEFT JOIN `project_scope` scope ON bill.`billable_table`='scope' AND bill.`billable_id`=scope.`id` WHERE bill.`id`='$billable'"));
		if($billable['category'] == 'tickets') {
			$ticket = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='{$billable['item_id']}'")->fetch_assoc();
			$description = $ticket['ticket_label'];
			$services = '';
			$service_fee = '';
			$inventory = '';
			$inv_price = '';
			$inv_qty = '';
			foreach(explode(',',$ticket['serviceid']) as $i => $serviceid) {
				if($serviceid > 0) {
					$qty = explode(',',$ticket['service_qty'])[$i];
					$price = $dbc->query("SELECT `cust_price` FROM `company_rate_card` WHERE `deleted`=0 AND `item_id`='$serviceid' AND `tile_name`='Services' AND `start_date` < DATE(NOW()) AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') > DATE(NOW()) ORDER BY `start_date`")->fetch_assoc()['cust_price'];
					mysqli_query($dbc, "INSERT INTO `invoice_lines` (`invoiceid`, `item_id`, `category`, `heading`, `description`, `quantity`, `unit_price`, `uom`, `sub_total`,`total`)
						VALUES ('$invoiceid', '$serviceid', 'services', '".TICKET_TILE."', '$description', '$qty', '$price', 'each', '".($qty * $price)."', '".($qty * $price)."')");
						$services .= $serviceid.',';
						$service_fee .= $price.',';
				}
			}
			$ticket_lines = $dbc->query("SELECT * FROM `ticket_attached` WHERE `ticketid`='{$ticket['ticketid']}' and `src_table` IN ('equipment','inventory','material')");
			while($ticket_line = $ticket_lines->fetch_assoc()) {
				if($ticket_line['src_table'] == 'inventory') {
					$item = $dbc->query("SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`product_name`,'')) label, `final_retail_price` `price` FROM `inventory` WHERE `inventoryid`='{$ticket_line['item_id']}'")->fetch_assoc();
					$inventory .= $ticket_line['item_id'].',';
					$inv_price .= $item['price'].',';
					$inv_qty .= $ticket_line['qty'].',';
				} else if($ticket_line['src_table'] == 'material') {
					$item = $dbc->query("SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`sub_category`,''),' ',IFNULL(`name`,'')) label, `price` FROM `material` WHERE `materialid`='{$ticket_line['item_id']}'")->fetch_assoc();
				} else if($ticket_line['src_table'] == 'equipment') {
					$item = $dbc->query("SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`make`,''),' ',IFNULL(`model`,''),' ',IFNULL(`label`,''),' ',IFNULL(`unit_number`,'')) label, 0 `price` FROM `equipment` WHERE `equipmentid`='{$ticket_line['item_id']}'")->fetch_assoc();
				}
				mysqli_query($dbc, "INSERT INTO `invoice_lines` (`invoiceid`, `item_id`, `category`, `heading`, `description`, `quantity`, `unit_price`, `uom`, `sub_total`, `total`)
					VALUES ('$invoiceid', '{$ticket_line['item_id']}', '{$ticket_line['src_table']}', '".TICKET_TILE."', '{$item['label']}', '{$ticket_line['qty']}', '{$item['price']}', 'Each', '".($ticket_line['qty'] * $item['price'])."', '".($ticket_line['qty'] * $item['price'])."')");
			}
			$dbc->query("UPDATE `invoice` SET `ticketid`='{$ticket['ticketid']}', `patientid`='".(explode(',',$ticket['clientid'])[0])."', `service_date`='".date('Y-m-d',strtotime($ticket['created_date']))."', `therapistsid`='".(trim(explode(',',$ticket['contactid']),',')[0])."', `serviceid`='$services', `fee`='$service_fee', `$inventoryid`='$inventory', `sell_price`='$inv_price', `quantity`='$inv_qty' WHERE `invoiceid`='$invoiceid");
		} else {
			$description = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`make`,''),' ',IFNULL(`model`,''),' ',IFNULL(`label`,''),' ',IFNULL(`unit_number`,'')) label FROM `equipment` WHERE `equipmentid`='{$billable['item_id']}' AND 'equipment'='{$billable['category']}' UNION
				SELECT CONCAT(IFNULL(`category`,''),': ',IFNULL(`product_name`,'')) label FROM `inventory` WHERE `inventoryid`='{$billable['item_id']}' AND 'inventory'='{$billable['category']}' UNION
				SELECT CONCAT(IFNULL(`labour_type`,''),' ',IFNULL(`category`,''),' ',IFNULL(`heading`,''),' ',IFNULL(`name`,'')) label FROM `labour` WHERE `labourid`='{$billable['item_id']}' AND 'labour'='{$billable['category']}' UNION
				SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`sub_category`,''),' ',IFNULL(`name`,'')) label FROM `material` WHERE `materialid`='{$billable['item_id']}' AND 'material'='{$billable['category']}' UNION
				SELECT `name` label FROM `positions` WHERE `position_id`='{$billable['item_id']}' AND 'position'='{$billable['category']}' UNION
				SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `products` WHERE `productid`='{$billable['item_id']}' AND 'products'='{$billable['category']}' UNION
				SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`heading`,'')) label FROM `services` WHERE `serviceid`='{$billable['item_id']}' AND 'services'='{$billable['category']}' UNION
				SELECT CONCAT(IFNULL(`category`,''),' ',IFNULL(`product_name`,'')) label FROM `vendor_price_list` WHERE `inventoryid`='{$billable['item_id']}' AND 'vpl'='{$billable['category']}'"))['label'];
				$description = filter_var($description,FILTER_SANITIZE_STRING);
				if(empty($description)) {
					$description = $billable['description'];
				}
				mysqli_query($dbc, "INSERT INTO `invoice_lines` (`invoiceid`, `item_id`, `category`, `heading`, `description`, `quantity`, `unit_price`, `uom`, `sub_total`, `total`)
					VALUES ('$invoiceid', '{$billable['item_id']}', '{$billable['category']}', '{$billable['description']}', '$description', '{$billable['qty']}', '{$billable['price']}', '{$billable['uom']}', '{$billable['retail']}', '{$billable['retail']}')");
		}
		$final_price += $billable['retail'];
	}
	$final_price = number_format($final_price, 2);
	mysqli_query($dbc, "UPDATE `invoice` SET `total_price` = '$total_price', `final_price` = '$final_price' WHERE `invoiceid` = '$invoiceid'");

	// PDF
	$invoice_design = get_config($dbc, 'invoice_design');
	switch($invoice_design) {
		case 1:
			include('pos_invoice_1.php');
			break;
		case 2:
			include('pos_invoice_2.php');
			break;
		case 3:
			include('pos_invoice_3.php');
			break;
		case 4:
			include ('patient_invoice_pdf.php');
			if($insurerid != '') {
				include ('insurer_invoice_pdf.php');
			}
			break;
		case 5:
			include ('pos_invoice_small.php');
			break;
		case 'service':
			include ('pos_invoice_service.php');
			break;
		case 'pink':
			include ('pos_invoice_pink.php');
			break;
		case 'cnt1':
			include ('pos_invoice_contractor_1.php');
			break;
		case 'cnt2':
			include ('pos_invoice_contractor_2.php');
			break;
		case 'cnt3':
			include ('pos_invoice_contractor_3.php');
			break;
        default:
			include('pos_invoice_1.php');
			break;
	}
    add_update_history($dbc,'project_history',"Invoice #$invoiceid created.",'','',$projectid);
	ob_clean();
	echo WEBSITE_URL."/Project/projects.php?edit=".$projectid."&tab=".$type;
} else if($_GET['action'] == 'approve_time') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$staff = get_contact($dbc, filter_var($_SESSION['contactid'],FILTER_SANITIZE_STRING));
	mysqli_query($dbc, "UPDATE `time_cards` SET `manager_name`='$staff', `date_manager`=DATE(NOW()) WHERE `time_cards_id`='$id'");
} else if($_GET['action'] == 'send_email') {
	$table_name = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$field_src = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$id_field = filter_var($_POST['id_field'],FILTER_SANITIZE_STRING);
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$sender = filter_var($_POST['sender'],FILTER_SANITIZE_STRING);
	$sender_name = filter_var($_POST['sender_name'],FILTER_SANITIZE_STRING);
	$subject = $_POST['subject'];
	$body = $_POST['body'];

	$value = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `$table_name` WHERE `$id_field`='$id'"));
	$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='{$value['projectid']}'"));
	$body = str_replace(['[REFERENCE]','[PROJECTID]','[CLIENT]','[HEADING]','[STATUS]'], [html_entity_decode($value[$field_src]),$value['projectid'],get_client($dbc,$project['businessid']),$project['project_name'],$project['status']],$body);
	$recipient = $_POST['recipient'];
	if(!is_array($_POST['recipient'])) {
		$recipient = [$_POST['recipient']];
	}
	foreach($recipient as $address) {
		$address = get_email($dbc, filter_var($address,FILTER_SANITIZE_STRING));
		try {
			send_email([$sender=>$sender_name], $address, '', '', $subject, $body, '');
		} catch(Exception $e) { echo "Unable to send e-mail: ".$e->getMessage(); }
	}
} else if($_GET['action'] == 'addendum_estimate') {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `estimate` (`businessid`,`clientid`,`estimatetype`,`estimate_name`,`created_date`,`afe_number`,`add_to_project`) SELECT `businessid`,`clientid`,`projecttype`,CONCAT(`project_name`,' - Addendum to Project #',`projectid`),DATE(NOW()),`afe_number`,`projectid` FROM `project` WHERE `projectid`='$projectid'");
	echo mysqli_insert_id($dbc);
} else if($_GET['action'] == 'quick_action_settings') {
	set_config($dbc, 'quick_action_icons', filter_var($_POST['quick_action_icons'], FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_colour_flags', filter_var($_POST['flags'], FILTER_SANITIZE_STRING));
	set_config($dbc, 'ticket_colour_flag_names', filter_var($_POST['names'], FILTER_SANITIZE_STRING));
} else if($_GET['action'] == 'administration_settings') {
	if($_POST['id'] > 0) {
		$id = $_POST['id'];
	} else {
		$dbc->query("INSERT INTO `field_config_project_admin` () VALUES ()");
		$id = $dbc->insert_id;
	}
	$name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
	$contactid = filter_var($_POST['contactid'], FILTER_SANITIZE_STRING);
	$signature = filter_var($_POST['signature'], FILTER_SANITIZE_STRING);
	$precedence = filter_var($_POST['precedence'], FILTER_SANITIZE_STRING);
	$status = filter_var($_POST['status'], FILTER_SANITIZE_STRING);
	$options = filter_var($_POST['options'], FILTER_SANITIZE_STRING);
	$action_items = filter_var($_POST['action_items'], FILTER_SANITIZE_STRING);
	$region = filter_var($_POST['region'], FILTER_SANITIZE_STRING);
	$location = filter_var($_POST['location'], FILTER_SANITIZE_STRING);
	$classification = filter_var($_POST['classification'], FILTER_SANITIZE_STRING);
	$customer = filter_var($_POST['customer'], FILTER_SANITIZE_STRING);
	$staff = filter_var($_POST['staff'], FILTER_SANITIZE_STRING);
	$unlocked_fields = filter_var($_POST['fields'], FILTER_SANITIZE_STRING);
	$deleted = filter_var($_POST['deleted'], FILTER_SANITIZE_STRING);
	$dbc->query("UPDATE `field_config_project_admin` SET `name`='$name', `contactid`='$contactid', `signature`='$signature', `options`='$options', `precedence`='$precedence', `action_items`='$action_items', `region`='$region', `location`='$location', `classification`='$classification', `customer`='$customer', `staff`='$staff', `status`='$status', `unlocked_fields`='$unlocked_fields', `deleted`='$deleted'  WHERE `id`='$id'");
	echo $id;
} else if($_GET['action'] == 'approvals') {
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$contactid = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$signature = filter_var($_POST['signature'],FILTER_SANITIZE_STRING);
	$date = filter_var($_POST['date'],FILTER_SANITIZE_STRING);
	if(!empty($date)) {
		$contactid .= '#*#'.$date;
		$signature .= '#*#'.$date;
	}
	if($id > 0) {
		if($_POST['status'] > 0) {
			$dbc->query("UPDATE `$table` SET `$field`=REPLACE(CONCAT(IFNULL(`$field`,''),',$contactid,'),',,',',') ".(empty($signature) ? '' : ", `approval_sign`=CONCAT(IFNULL(CONCAT(`approval_sign`,','),''),'$signature')")." WHERE `".($table == 'tickets' ? 'ticketid' : 'tasklistid')."`='$id'");
			echo "UPDATE `$table` SET `$field`=REPLACE(CONCAT(IFNULL(`$field`,''),',$contactid,'),',,',',') ".(empty($signature) ? '' : ", `approval_sign`=CONCAT(IFNULL(CONCAT(`approval_sign`,','),''),'$signature')")." WHERE `".($table == 'tickets' ? 'ticketid' : 'tasklistid')."`='$id'";
		} else {
			$dbc->query("UPDATE `$table` SET `$field`=REPLACE(REPLACE(`$field`,',$contactid,',','),',,',',') WHERE `".($table == 'tickets' ? 'ticketid' : 'tasklistid')."`='$id'");
		}
	}
	if($_POST['invoice'] == 'true' && $table == 'tickets') {
		$_POST['ticketid'] = $id;
		include('../Invoice/create_ticket_invoice.php');
	}
} else if($_GET['action'] == 'show_notes') {
    $subtab = filter_var($_GET['subtab'],FILTER_SANITIZE_STRING);
    $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `note` FROM `notes_setting` WHERE `subtab`='$subtab'"));
    $html = '';
    if ( !empty($notes['note']) ) {
        $html .= '
            <div class="notice popover-examples">
                <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                <div class="col-sm-11"><span class="notice-name">NOTE:</span>
                '.$notes['note'].'</div>
                <div class="clearfix"></div>
            </div>';
    }
    echo $html;
} else if($_GET['action'] == 'billable_edit') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$billable = filter_var($_POST['billable'],FILTER_SANITIZE_STRING);
	$services = $dbc->query("SELECT `serviceid`, `service_qty`, `service_discount`, `service_discount_type`, `businessid`, `agentid` FROM `tickets` WHERE `ticketid`='$id'")->fetch_assoc();
	$customer_rates = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `services`, `staff`, `staff_position` FROM `rate_card` WHERE `clientid`='{$services['businessid']}' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')"));
	$serviceid = explode(',',$services['serviceid']);
	$qty = explode(',',$services['qty']);
	$discount = explode(',',$services['service_discount']);
	$type = explode(',',$services['service_discount_type']);
	foreach($serviceid as $i => $service) {
		if($service > 0) {
			$rate = 0;
			foreach(explode('**',$customer_rates['services']) as $rate_line) {
				$rate_line = explode('#',$rate_line);
				if($rate_line[0] == $id) {
					$rate = $rate_line[1];
				}
			}
			if($rate == 0) {
				$rate = $_SERVER['DBC']->query("SELECT `cust_price` FROM `company_rate_card` WHERE `item_id`='$service' AND `tile_name` LIKE 'Services' AND `deleted`=0 AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-99-99') > NOW() ORDER BY `start_date` DESC")->fetch_assoc()['cust_price'];
			}
			$service = $dbc->query("SELECT * FROM `services` WHERE `serviceid`='$service'")->fetch_assoc();
			$profit = -$discount[$i];
			if($type[$i] == '%') {
				$profit = -$discount * $rate / 100;
			}
			$margin = $profit / $rate * 100;
			$price = $rate + $profit;
			$description = ($service['category'].(!empty($service['category'].$service['service_type']) ? ' ('.$service['service_type'].')' : '').(!empty($service['category'].$service['service_type']) ? ': ' : '').$service['heading']);
			$dbc->query("INSERT INTO `project_billable` (`projectid`, `heading`, `description`, `billable_table`, `billable_id`, `uom`, `qty`, `cost`, `profit`, `margin`, `price`, `retail`, `sort_order`) SELECT `projectid`, `heading`, '$description', 'services', '{$service['serviceid']}', '', '{$qty[$i]}', '$rate', '$profit', '$margin', '$price', '$price', `sort_order` FROM `project_billable` WHERE `id`='$billable'");
		}
	}
	$dbc->query("UPDATE `project_billable` SET `deleted`=0 WHERE `id`='$billable'");
} else if($_GET['action'] == 'milestone_edit') {
	if($_POST['id'] > 0) {
		$id = $_POST['id'];
		$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
		$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
		$dbc->query("UPDATE `project_path_custom_milestones` SET `$field`='$value' WHERE `id`='$id'");
	} else if($_POST['field'] == 'sort') {
		$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
		$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
		$pathid = filter_var($_POST['pathid'],FILTER_SANITIZE_STRING);
		$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
		$dbc->query("INSERT INTO `project_path_custom_milestones` (`$field`,`pathid`,`projectid`) VALUES ('$value','$pathid','$projectid')");
		$id = $dbc->insert_id;
		echo $id;
		$dbc->query("UPDATE `project_path_custom_milestones` SET `milestone`='new milestone.$id', `label`='New Milestone' WHERE `id`='$id'");
	}
} else if($_GET['action'] == 'save_user_form') {
	$project_type = $_POST['project_type'];
	$project_form_id = $_POST['project_form_id'];
	$project_heading = $_POST['project_heading'];
	$user_form_id = $_POST['user_form_id'];
	$subtab_name = $_POST['subtab_name'];
	if($project_form_id > 0) {
		mysqli_query($dbc, "UPDATE `field_config_project_form` SET `user_form_id` = '$user_form_id', `subtab_name` = '$subtab_name' WHERE `id` = '$project_form_id'");
	} else if($user_form_id > 0 || !empty($subtab_name)) {
		mysqli_query($dbc, "INSERT INTO `field_config_project_form` (`project_type`, `project_heading`, `user_form_id`, `subtab_name`) VALUES ('$project_type', '$project_heading', '$user_form_id', '$subtab_name')");
		echo mysqli_insert_id($dbc);
	}
} else if($_GET['action'] == 'delete_user_form') {
	$project_form_id = $_POST['project_form_id'];
	if($project_form_id > 0) {
		mysqli_query($dbc, "DELETE FROM `field_config_project_form` WHERE `id` = '$project_form_id'");
	}
} else if($_GET['action'] == 'saveNote') {
	$projectid = $_GET['projectid'];
	$note = $_GET['note'];
    $created_date = date('Y-m-d');
    $who_added = $_SESSION['contactid'];
    mysqli_query($dbc, "INSERT INTO `project_comment` (`projectid`, `comment`, `created_date`, `created_by`, `type`) VALUES ('$projectid', '$note', '$created_date', '$who_added', 'project_note')");
} else if($_GET['action'] == 'archive_project_form') {
	$projectform = $_POST['projectform'];
        $date_of_archival = date('Y-m-d');
	mysqli_query($dbc, "UPDATE `project_form` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `id` = '$projectform'");
} else if($_GET['action'] == 'get_category_list') {
	$category = filter_var(($_POST['category'] ?: $_GET['category']),FILTER_SANITIZE_STRING);
	echo '<option></option>';
	foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `name`, `first_name`, `last_name`, `site_name`, `display_name`, `businessid`, `region`, `classification`, `con_locations` FROM `contacts` WHERE `deleted`=0 AND `status`=1 AND `category` LIKE '$category'")) as $contact) {
		echo '<option data-region="'.$contact['region'].'" data-location="'.$contact['con_locations'].'" data-classification="'.$contact['classification'].'" data-business="'.$contact['businessid'].'" value="'.$contact['contactid'].'">'.$contact['full_name'].'</option>';
	}
} else if($_GET['action'] == 'sales_docs_upload') {
	$projectid = $_POST['projectid'];
    $salesid = $_POST['salesid'];
	$doc_query = mysqli_query($dbc, "SELECT upload FROM project_document WHERE deleted=0");
    if ($doc_query->num_rows>0) {
        $doc_arr = array();
        while ( $doc_row=mysqli_fetch_array($doc_query) ) {
            $doc_arr[] = $doc_row['upload'];
        }
        $doc_arr = array_filter($doc_arr);
    }
    $row_sales_docs_query = mysqli_query($dbc, "SELECT salesdocid, document_type, document FROM sales_document WHERE salesid='$salesid'");
    if ($row_sales_docs_query->num_rows>0) {
        while ($row_sales_doc=mysqli_fetch_assoc($row_sales_docs_query)) {
            if ( !in_array($row_sales_doc['document'], $doc_arr) ) {
                if ( copy('../Sales/download/'.$row_sales_doc['document'], 'download/'.$row_sales_doc['document']) ) {
                    mysqli_query($dbc, "INSERT INTO project_document(projectid, category, upload, created_by) VALUES('$projectid', '{$row_sales_doc['document_type']}', '{$row_sales_doc['document']}', '{$_SESSION['contactid']}')");
                }
            }
        }
    }
} else if($_GET['action'] == 'deliverable_date') {
	$table = filter_var($_POST['table'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
	if($table == 'tasklist') {
		$dbc->query("UPDATE `tasklist` SET `$field`='$value' WHERE `tasklistid`='$id'");
	} else {
		$dbc->query("UPDATE `tickets` SET `$field`='$value' WHERE `ticketid`='$id'");
	}
} else if($_GET['action'] == 'set_custom_fields') {
	$type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);
	$custom_details = json_decode($_POST['custom_details']);
	$fieldconfigids = [];
	foreach($custom_details as $custom_detail) {
		$custom_detail = json_decode(json_encode($custom_detail), true);
		$tab = $custom_detail['tab'];
		$heading = $custom_detail['heading'];
		$fields = implode('****',$custom_detail['fields']);

		if($fields != '####textarea' && $fields != '####uploader' && !empty($fields)) {
			$config_exists = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_project_custom_details` WHERE `type` = '$type' AND `tab` = '$tab' AND `heading` = '$heading'"));
			if(!empty($config_exists)) {
				$fieldconfigids[] = $config_exists['fieldconfigid'];
				mysqli_query($dbc, "UPDATE `field_config_project_custom_details` SET `fields` = '$fields' WHERE `fieldconfigid` = '{$config_exists['fieldconfigid']}'");
			} else {
				mysqli_query($dbc, "INSERT INTO `field_config_project_custom_details` (`type`, `tab`, `heading`, `fields`) VALUES ('$type', '$tab', '$heading', '$fields')");
				$fieldconfigids[] = mysqli_insert_id($dbc);
			}
		}
	}
	$fieldconfigids = implode(',', $fieldconfigids);
	$fieldconfigids = empty($fieldconfigids) ? "''" : $fieldconfigids;
	mysqli_query($dbc, "DELETE FROM `field_config_project_custom_details` WHERE `type` = '$type' AND `fieldconfigid` NOT IN ($fieldconfigids)");
} else if($_GET['action'] == 'remove_custom_field') {
	$id = $_POST['id'];
	if($id > 0) {
	        $date_of_archival = date('Y-m-d');
	mysqli_query($dbc, "UPDATE `project_custom_details` SET `deleted` = 1, `date_of_archival` = '$date_of_archival' WHERE `id` = '$id'");
	}
} else if($_GET['action'] == 'add_custom_field_upload') {
	$id = $_POST['id'];

    if(!file_exists('download')) {
        mkdir('download', 0777, true);
    }
    if(!empty($_FILES['value']['name'])) {
        $value = $basename = preg_replace('/[^a-z0-9.]*/','',strtolower($_FILES['value']['name']));
        $j = 0;
        while(file_exists('download/'.$value)) {
            $value = preg_replace('/(\.[a-z0-9]*)/', ' ('.++$j.')$1', $basename);
        }
        move_uploaded_file($_FILES['value']['tmp_name'], 'download/'.$value);
        mysqli_query($dbc, "UPDATE `project_custom_details` SET `value` = '$value' WHERE `id` = '$id'");
        echo $value;
    }
} else if($_GET['action'] == 'remove_custom_field_upload') {
	$id = $_POST['id'];
	if($id > 0) {
		mysqli_query($dbc, "UPDATE `project_custom_details` SET `value` = '' WHERE `id` = '$id'");
	}
} else if($_GET['action'] == 'dafault_path') {
    $project_path_milestone = $_GET['project_path_milestone'];

	$query_update_project = "UPDATE `project_path_milestone` SET  default_path=0";
	$result_update_project = mysqli_query($dbc, $query_update_project);

	$query_update_project = "UPDATE `project_path_milestone` SET  default_path=1 WHERE `project_path_milestone` = '$project_path_milestone'";
	$result_update_project = mysqli_query($dbc, $query_update_project);
} else if($_GET['action'] == 'set_path_names') {
	$type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);
	$name = filter_var($_POST['name'],FILTER_SANITIZE_STRING);
	$key = filter_var($_POST['key'],FILTER_SANITIZE_STRING);
	$project = filter_var($_POST['project'],FILTER_SANITIZE_STRING);
	$path_names = explode('#*#',get_field_value($type,'project','projectid',$project));
	for($i = 0; $i <= $key; $i++) {
		$path_names[$i] = (empty($path_names[$i]) ? '' : $path_names[$i]);
	}
	$path_names[$key] = $name;
	$path_names = implode('#*#',$path_names);
	$dbc->query("UPDATE `project` SET `$type`='$path_names' WHERE `projectid`='$project'");
} else if($_GET['action'] == 'payment_details') {
	$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
	if($_POST['table'] == 'invoice') {
		$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
		$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
		$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
		if($field == 'due_date') {
			$dbc->query("UPDATE `invoice` SET `due_date`='$value', `history`=CONCAT(IFNULL(CONCAT(`history`,'%lt;br /&gt;'),''),'Due Date set to $value by ".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."') WHERE `invoiceid`='$id'");
		} else if($field == 'date_paid') {
			$dbc->query("UPDATE `invoice` LEFT JOIN `invoice_payment` ON `invoice_payment`.`invoiceid`=`invoice`.`invoiceid` SET `invoice`.`paid`='Yes', `invoice_payment`.`paid`=1, `invoice_payment`.`date_paid`='$value', `invoice`.`history`=CONCAT(IFNULL(CONCAT(`history`,'%lt;br /&gt;'),''),'Marked Paid as $value on ".date('Y-m-d')." by ".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."') WHERE `invoiceid`='$id'");
		}
	} else if($_POST['table'] == 'project_payments') {
		$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
		if(!($id > 0)) {
			$dbc->query("INSERT INTO `project_payments` (`projectid`) VALUES ('$projectid')");
			$id = $dbc->insert_id;
			echo $id;
		}
		$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
		$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);
		$dbc->query("UPDATE `project_payments` SET `$field`='$value', `history`=CONCAT(IFNULL(CONCAT(`history`,'&lt;br /&gt;'),''),'$field set to $value by ".decryptIt($_SESSION['first_name'])." ".decryptIt($_SESSION['last_name'])."') WHERE `id`='$id'");
	}
} else if($_GET['action'] == 'project_label') {
	if($_POST['projectid'] > 0) {
		echo get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='".$_POST['projectid']."'")));
	} else {
		echo 'New '.PROJECT_NOUN;
	}
} else if($_GET['action'] == 'toggle_time_tracking') {
	$projectid = filter_var($_POST['projectid'],FILTER_SANITIZE_STRING);
	$staff = $_SESSION['contactid'];
	$current_time = $dbc->query("SELECT * FROM `time_cards` WHERE (`projectid`='$projectid' OR `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid')) AND `staff`='$staff' AND `deleted`=0 AND `timer_start` > 0");
	$seconds = time();
	$time = date('H:i');
	$today = date('Y-m-d');
	$time_minimum = get_config($dbc, 'ticket_min_hours');
	$time_interval = get_config($dbc, 'timesheet_hour_intervals');
	if($current_time->num_rows > 0) {
		$row = $current_rows->fetch_assoc();
		if($row['ticketid'] > 0) {
			$dbc->query("UPDATE `ticket_attached` `hours_tracked`=($sceonds - `timer_start`) / 3600, `timer_start`=0, `checked_out`='$time' WHERE `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid` AND `deleted`=0) AND `deleted`=0 AND `timer_start` > 0");
		}
		mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs`=GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `type_of_time`=IF(`type_of_time`='day_tracking',IF(`day_tracking_type` IS NULL OR `day_tracking_type` = '', 'Regular Hrs.', `day_tracking_type`),''), `end_time`='$time' WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `staff`='$staff'");
	} else {
		// Sign out of Day Tracking and create a new row to resume Day Tracking
		mysqli_query($dbc, "INSERT INTO `time_cards` (`timer_start`, `type_of_time`, `start_time`, `staff`, `date`, `day_tracking_type`, `created_by`) SELECT '$seconds', 'day_tracking', '$time', `staff`, '$today', CONCAT('Work:',MAX(`time_cards_id`)), 0 FROM `time_cards` WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `day_tracking_type` NOT LIKE 'Work:%' AND `staff`='$staff'");
		mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs`=GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `type_of_time`=IF(`day_tracking_type` IS NULL OR `day_tracking_type` = '', 'Regular Hrs.', `day_tracking_type`), `end_time`='$time' WHERE `timer_start` > 0 AND `type_of_time`='day_tracking' AND `day_tracking_type` NOT LIKE 'Work:%' AND `staff`='$staff'");
		// Sign into the Project
		mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$time' WHERE `type_of_time` NOT IN ('day_tracking','day_break') AND `timer_start` > 0 AND `staff`='$staff'");
		mysqli_query($dbc, "INSERT INTO `time_cards` (`business`, `projectid`, `staff`, `date`, `start_time`, `timer_start`, `type_of_time`, `comment_box`, `ticket_attached_id`) SELECT `businessid`, `projectid`, '$staff', '$today', '$time', '$seconds', '".PROJECT_NOUN." Time', 'Checked in on ".PROJECT_NOUN." #$projectid', '$staff' FROM `project` WHERE `projectid`='$projectid'");
		mysqli_query($dbc, "UPDATE `time_cards` SET `total_hrs` = GREATEST(IF('$time_interval' > 0,CEILING(((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600) / '$time_interval') * '$time_interval',((($seconds - `timer_start`) + IFNULL(NULLIF(`timer_tracked`,'0'),IFNULL(`total_hrs`,0))) / 3600)),'$time_minimum'), `timer_tracked` = (($seconds - `timer_start`) + IFNULL(`timer_tracked`,0)) / 3600, `timer_start`=0, `end_time`='$time', `comment_box`=CONCAT(IFNULL(`comment_box`,''),'Signed in on ".get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'")))."') WHERE `type_of_time` NOT IN ('day_tracking','day_break') AND `projectid`!='$projectid' AND `staff`='$staff' AND `timer_start` > 0");
	}
} else if($_GET['action'] == 'timer') {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$timer_value = filter_var($_GET['timer_value'],FILTER_SANITIZE_STRING);
	$staff = $_SESSION['contactid'];
    $today_date = date('Y-m-d');
	mysqli_query($dbc, "INSERT INTO `project_timer` (`projectid`, `staff`, `today_date`, `timer_value`) VALUES ('$projectid', '$staff', '$today_date', '$timer_value')");
    mysqli_query($dbc, "INSERT INTO `time_cards` (`projectid`,`staff`,`date`,`type_of_time`,`total_hrs`,`timer_tracked`,`comment_box`) VALUES ('$projectid','$staff','$today_date','Regular Hrs.','".((strtotime($timer_value) - strtotime('00:00:00')) / 3600)."','0','Time Added on Project #$projectid')");
	insert_day_overview($dbc, $staff, 'Project', $today_date, '', "Updated Project #$projectid - Added Time : $timer_value");
} else if($_GET['action'] == 'load_sales_scope') {
	$projectid = filter_var($_POST['project'],FILTER_SANITIZE_STRING);
	$salesid = filter_var($_POST['sales'],FILTER_SANITIZE_STRING);
    // Add Sales Lead Communication to Project
    $dbc->query("UPDATE `email_communication` SET `projectid`='$projectid' WHERE `projectid`=0 AND `salesid`='$salesid'");

    // Add Scope from Sales Lead to Project
	$sales_scope = $dbc->query("SELECT `serviceid`,`productid` FROM `sales` WHERE `salesid`='$salesid'")->fetch_assoc();
	foreach(explode(',',$sales_scope['serviceid']) as $service) {
		if($service > 0) {
			$service_rate = $dbc->query("SELECT * FROM `company_rate_card` WHERE `item_id`='$service' AND `tile_name`='Services' AND DATE(NOW()) > `start_date` AND DATE(NOW()) < IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') AND `deleted`=0 ORDER BY `start_date` DESC")->fetch_assoc();
			$dbc->query("INSERT INTO `project_scope` (`projectid`,`src_table`,`src_id`,`qty`,`cost`,`price`) VALUES ('$projectid','services','$service','1','{$service_rate['cost']}','{$service_rate['cust_price']}');");
		}
	}
	foreach(explode(',',$sales_scope['productid']) as $product) {
		if($product > 0) {
			$product_rate = $dbc->query("SELECT `final_retail_price` `cust_price`,`cost` FROM `products` WHERE `productid`='$product'")->fetch_assoc();
			if(empty($product_rate['final_retail_price'])) {
				$product_rate = $dbc->query("SELECT `cust_price`,`cost` FROM `company_rate_card` WHERE `item_id`='$product' AND `tile_name`='Products' AND DATE(NOW()) > `start_date` AND DATE(NOW()) < IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') AND `deleted`=0 ORDER BY `start_date` DESC")->fetch_assoc();
			}
			$dbc->query("INSERT INTO `project_scope` (`projectid`,`src_table`,`src_id`,`qty`,`cost`,`price`) VALUES ('$projectid','services','$service','1','{$service_rate['cost']}','{$service_rate['cust_price']}');");
		}
	}
} else if($_GET['action'] == 'setting_contacts') {
    set_config($dbc, 'project_lead_cats', $_POST['lead']);
    set_config($dbc, 'project_co_lead_cats', $_POST['co']);
    set_config($dbc, 'project_team_cats', $_POST['team']);
} else if($_GET['action'] == 'flag_colour') {
	$id = filter_var($_POST['id'],FILTER_SANITIZE_STRING);
	$field = filter_var($_POST['field'],FILTER_SANITIZE_STRING);
	$value = filter_var($_POST['value'],FILTER_SANITIZE_STRING);

	$colours = explode(',', get_config($dbc, "ticket_colour_flags"));
	$labels = explode('#*#', get_config($dbc, "ticket_colour_flag_names"));
	$colour_key = array_search($value, $colours);
	$new_colour = ($colour_key === FALSE ? $colours[0] : ($colour_key + 1 < count($colours) ? $colours[$colour_key + 1] : 'FFFFFF'));
	$label = ($colour_key === FALSE ? $labels[0] : ($colour_key + 1 < count($colours) ? $labels[$colour_key + 1] : ''));
	echo $new_colour.html_entity_decode($label);
	$before_change = capture_before_change($dbc, 'project', 'flag_colour', 'projectid', $id);
	$before_change .= capture_before_change($dbc, 'project', 'flag_start', 'projectid', $id);
	$before_change .= capture_before_change($dbc, 'project', 'flag_end', 'projectid', $id);
	mysqli_query($dbc, "UPDATE `project` SET `flag_colour`='$new_colour', `flag_start`='0000-00-00', `flag_end`='9999-12-31' WHERE `projectid`='$id'");
	$history = capture_after_change('flag_colour', $new_colour);
	$history .= capture_after_change('flag_start', '0000-00-00');
	$history .= capture_after_change('flag_end', '9999-12-31');
	add_update_history($dbc, 'project_history', $history, '', $before_change, $projectid);
}
