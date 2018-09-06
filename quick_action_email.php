<?php include_once('include.php');
checkAuthorised();

if(isset($_POST['submit'])) {
	$sender = get_email($dbc, $_SESSION['contactid']);
	$subject = filter_var($_POST['subject'],FILTER_SANITIZE_STRING);
	$body = filter_var(htmlentities($_POST['body'],FILTER_SANITIZE_STRING));

    $cc_emails = [];
    foreach($_POST['cc_staff'] as $cc_staff) {
        if($cc_staff > 0) {
            $cc_emails[] = get_email($dbc, $cc_staff);
        }
    }
    foreach($_POST['cc_contact'] as $cc_contact) {
        if($cc_contact > 0) {
            $cc_emails[] = get_email($dbc, $cc_contact);
        }
    }
    foreach(array_filter(explode(',',$_POST['cc_other'])) as $cc_other) {
        $cc_other = trim($cc_other);
        if(filter_var($cc_other,FILTER_VALIDATE_EMAIL)) {
            $cc_emails[] = $cc_other;
        }
    }

    $bcc_emails = [];
    foreach($_POST['bcc_staff'] as $bcc_staff) {
        if($bcc_staff > 0) {
            $bcc_emails[] = get_email($dbc, $bcc_staff);
        }
    }
    foreach($_POST['bcc_contact'] as $bcc_contact) {
        if($bcc_contact > 0) {
            $bcc_emails[] = get_email($dbc, $bcc_contact);
        }
    }
    foreach(array_filter(explode(',',$_POST['bcc_other'])) as $bcc_other) {
        $bcc_other = trim($bcc_other);
        if(filter_var($bcc_other,FILTER_VALIDATE_EMAIL)) {
            $bcc_emails[] = $bcc_other;
        }
    }

	$error = '';
    foreach($_POST['to_staff'] as $user) {
        if($user > 0) {
            $body = str_replace(['[STAFF_NAME]'],[get_contact($dbc, $user)],$body);
            $user = get_email($dbc, $user);
            try {
                send_email($sender, $user, $cc_emails, $bcc_emails, $subject, html_entity_decode($body), '');
            } catch (Exception $e) {
                $error .= "Unable to send email: ".$e->getMessage()."\n";
            }
        }
    }
    foreach($_POST['to_contact'] as $user) {
        if($user > 0) {
            $body = str_replace(['[STAFF_NAME]'],[get_contact($dbc, $user)],$body);
            $user = get_email($dbc, $user);
            try {
                send_email($sender, $user, $cc_emails, $bcc_emails, $subject, html_entity_decode($body), '');
            } catch (Exception $e) {
                $error .= "Unable to send email: ".$e->getMessage()."\n";
            }
        }
    }
    foreach(array_filter(explode(',',$_POST['to_staff'])) as $user) {
        $user = trim($user);
        if(filter_var($user,FILTER_VALIDATE_EMAIL)) {
            $body = str_replace(['[STAFF_NAME]'],[$user],$body);
            try {
                send_email($sender, $user, $cc_emails, $bcc_emails, $subject, html_entity_decode($body), '');
            } catch (Exception $e) {
                $error .= "Unable to send email: ".$e->getMessage()."\n";
            }
        }
    }

	echo '<script type="text/javascript"> alert("'.(empty($error) ? 'Successfully sent.' : $error).'"); </script>';
}

switch($_GET['tile']) {
    case 'checklists':
        $id = $_GET['id'];
        $type = $_GET['type'];
        if($type == 'checklist') {
            $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist_name WHERE checklistnameid='$id'"));
            $title = explode('<p>',html_entity_decode($result['checklist']))[0];
            $subject = "A reminder about the $title on the checklist";
        }
        else {
            $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM checklist WHERE checklistid = '$id'"));
            $title = $result['checklist_name'];
            $subject = "A reminder about the $title checklist";
        }
        $body = "Hi [STAFF_NAME]<br />\n<br />
            This is a reminder about the $title on the checklist.<br />\n<br />
            <a href='".WEBSITE_URL."/Checklist/checklist.php?checklistid=$id'>Click here</a> to see the checklist.";
        break;
    case 'daily_log_notes':
        $id = $_GET['id'];
        $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `client_daily_log_notes` WHERE note_id='$id'"));
        $subject = "A reminder about a Daily Log Note";
        $body = "Hi [STAFF_NAME]<br />\n<br />
            This is a reminder about a Daily Log Note for ".(!empty(get_client($dbc, $result['client_id'])) ? get_client($dbc, $result['client_id']) : get_contact($dbc, $result['client_id'])).".<br />\n<br />
            ".html_entity_decode($result['note']).".<br />\n<br />
            <a href='".WEBSITE_URL."/Daily Log Notes/index.php?tab=".strtolower(get_contact($dbc, $result['client_id'], 'category'))."&display_contact=".$result['client_id']."'>Click here</a> to see the Daily Log Notes.";
        break;
    case 'sales_intake':
        $salesid = $_GET['salesid'];
        $intakeid = $_GET['intakeid'];
        $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid`='$intakeid'"));
        $milestone = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_path_custom_milestones` WHERE `salesid` = '$salesid' AND `milestone` = '".$result['sales_milestone']."'"))['label'];
        $subject = "A reminder about Intake #".$intakeid." in ".SALES_NOUN." #".$salesid."  $milestone";
        $body = "This is a reminder about Intake #".$intakeid." in ".SALES_NOUN." #".$salesid." $milestone<br />\n<br />
            <a href='".WEBSITE_URL."/Sales/sale.php?p=salespath&id=$salesid'>Click here</a> to see the ".SALES_NOUN.".<br />\n";
        break;
    case 'sales_checklist':
        $salesid = $_GET['salesid'];
        $checklistid = $_GET['checklistid'];
        $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid`='$checklistid'"));
        $milestone = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_path_custom_milestones` WHERE `salesid` = '$salesid' AND `milestone` = '".$result['sales_milestone']."'"))['label'];
        $subject = "A reminder about Checklist #".$checklistid.": ".$result['checklist_name']." in ".SALES_NOUN." #".$salesid."  $milestone";
        $body = "This is a reminder about checklist #".$checklistid.": ".$result['checklist_name']." in ".SALES_NOUN." #".$salesid." $milestone<br />\n<br />
            <a href='".WEBSITE_URL."/Sales/sale.php?p=salespath&id=$salesid'>Click here</a> to see the ".SALES_NOUN.".<br />\n";
        break;
    case 'sales_task':
        $salesid = $_GET['salesid'];
        $id = $_GET['id'];
        $type = $_GET['type'];
        if($type == 'task') {
            $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `tasklistid`='$id'"));
            $title = $result['heading'];
            $milestone = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales_path_custom_milestones` WHERE `salesid` = '$salesid' AND `milestone` = '".$result['sales_milestone']."'"))['label'];
            $subject = "A reminder about the $title in ".SALES_NOUN." #".$salesid." $milestone";
            $body = "Hi [STAFF_NAME]<br />\n<br />
                This is a reminder about the $title in ".SALES_NOUN." #".$salesid." $milestone.<br />\n<br />
                <a href='".WEBSITE_URL."/Sales/sale.php?p=salespath&id=$salesid'>Click here</a> to see the ".SALES_NOUN.".";
        } else {
            $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM task_board WHERE taskboardid = '$id'"));
            $title = $result['board_name'];
            $tab = $result['board_security'];
            $subject = "A reminder about the $title task board";
            $body = "Hi [STAFF_NAME]<br />\n<br />
                This is a reminder about the $title.<br />\n<br />
                <a href='".WEBSITE_URL."/Tasks/index.php?category=$id&tab=$tab'>Click here</a> to see the task board.";
        }
        break;
    case 'sales':
        $salesid = $_GET['salesid'];
        $subject = "A reminder about a ".SALES_NOUN;
        $body = "This is a reminder about a ".SALES_NOUN.".<br />\n<br />
            <a href='".WEBSITE_URL."/Sales/sale.php?p=preview&id=$salesid'>Click here</a> to see the ".SALES_NOUN.".<br />\n<br />
            $item";
        break;
    case 'tasks':
        $id = $_GET['id'];
        $type = $_GET['type'];
        if($type == 'task') {
            $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `list`.`task_board`, `list`.`heading`, `board`.`board_security` FROM `tasklist` AS `list` JOIN `task_board` AS `board` ON (`list`.`task_board`=`board`.`taskboardid`) WHERE `list`.`tasklistid`='$id'"));
            $id = $result['task_board'];
            $title = $result['heading'];
            $tab = $result['board_security'];
            $subject = "A reminder about the $title on the task board";
        } else {
            $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM task_board WHERE taskboardid = '$id'"));
            $title = $result['board_name'];
            $tab = $result['board_security'];
            $subject = "A reminder about the $title task board";
        }
        $body = "Hi [STAFF_NAME]<br />\n<br />
            This is a reminder about the $title.<br />\n<br />
            <a href='".WEBSITE_URL."/Tasks/index.php?category=$id&tab=$tab'>Click here</a> to see the task board.";
        break;
    case 'task_checklist':
        $taskboardid = $_GET['task_board'];
        $checklistid = $_GET['checklistid'];
        $result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklistid`='$checklistid'"));
        $task_board = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `task_board` WHERE `taskboardid` = '$taskboardid'"));
        $board_name = $task_board['board_name'];
        $tab = $task_board['board_security'];
        $milestone = $result['task_milestone_timeline'];
        $subject = "A reminder about Checklist #".$checklistid.": ".$result['checklist_name']." in $board_name task board  $milestone";
        $body = "This is a reminder about Checklist #".$checklistid.": ".$result['checklist_name']." in $board_name task board  $milestone<br />\n<br />
            <a href='".WEBSITE_URL."/Tasks/index.php?category=$taskboardid&tab=$tab'>Click here</a> to see the task board.<br />\n";
        break;
    case 'tickets':
        $id = $_GET['id'];
        $subject = "A reminder about a ".TICKET_NOUN;
        $body = "This is a reminder about a ".TICKET_NOUN.".<br />\n<br />
            <a href='".WEBSITE_URL."/Ticket/index.php?edit=$id'>Click here</a> to see the ".TICKET_NOUN.".<br />\n<br />";
        break;
	case 'projects':
		$id = $_GET['id'];
        $subject = "A reminder about a ".PROJECT_NOUN;
		$body = "This is a reminder about a ".PROJECT_TILE.".<br />\n<br />
                <a href='".WEBSITE_URL."/Project/projects.php?edit=$id&tile_name=project'>Click here</a> to see the ".PROJECT_TILE.".<br />\n<br />";
		break;
} ?>

<div class="container">
	<div class="row">
        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

        	<h3 class="inline">Send Email</h3>
            <div class="pull-right gap-top"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img" /></a></div>
            <div class="clearfix"></div>
            <hr />

            <h5>To:</h5>
            <div class="form-group">
                <label class="col-sm-4 control-label">Staff:</label>
                <div class="col-sm-8">
                    <select name="to_staff[]" multiple class="chosen-select-deselect form-control">
                        <?php $staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"));
                        foreach($staff_list as $staff) {
                            if(!empty($staff['full_name']) && $staff['full_name'] != '-') { ?>
                                <option value="<?= $staff['contactid']; ?>"><?= $staff['full_name'] ?></option>
                            <?php }
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label">Contact:</label>
                <div class="col-sm-8">
                    <select name="to_contact[]" multiple class="chosen-select-deselect form-control">
                        <?php $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.") AND `deleted`=0 AND `status`>0 AND IFNULL(`email_address`,'') != ''"));
                        foreach($contact_list as $contact) {
                            if(!empty($contact['full_name']) && $contact['full_name'] != '-') { ?>
                                <option value="<?= $contact['contactid']; ?>"><?= $contact['full_name'] ?></option>
                            <?php }
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label">Other Email:<br><em>Enter emails separated by a comma</em></label>
                <div class="col-sm-8">
                    <input type="text" name="to_other" class="form-control" value="">
                </div>
            </div>

            <div class="clearfix"></div><hr>

            <h5>CC:</h5>
            <div class="form-group">
                <label class="col-sm-4 control-label">Staff:</label>
                <div class="col-sm-8">
                    <select name="cc_staff[]" multiple class="chosen-select-deselect form-control">
                        <?php $staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"));
                        foreach($staff_list as $staff) {
                            if(!empty($staff['full_name']) && $staff['full_name'] != '-') { ?>
                                <option value="<?= $staff['contactid']; ?>"><?= $staff['full_name'] ?></option>
                            <?php }
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label">Contact:</label>
                <div class="col-sm-8">
                    <select name="cc_contact[]" multiple class="chosen-select-deselect form-control">
                        <?php $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.") AND `deleted`=0 AND `status`>0 AND IFNULL(`email_address`,'') != ''"));
                        foreach($contact_list as $contact) {
                            if(!empty($contact['full_name']) && $contact['full_name'] != '-') { ?>
                                <option value="<?= $contact['contactid']; ?>"><?= $contact['full_name'] ?></option>
                            <?php }
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label">Other Email:<br><em>Enter emails separated by a comma</em></label>
                <div class="col-sm-8">
                    <input type="text" name="cc_other" class="form-control" value="">
                </div>
            </div>

            <div class="clearfix"></div><hr>

            <h5>BCC:</h5>
            <div class="form-group">
                <label class="col-sm-4 control-label">Staff:</label>
                <div class="col-sm-8">
                    <select name="bcc_staff[]" multiple class="chosen-select-deselect form-control">
                        <?php $staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"));
                        foreach($staff_list as $staff) {
                            if(!empty($staff['full_name']) && $staff['full_name'] != '-') { ?>
                                <option value="<?= $staff['contactid']; ?>"><?= $staff['full_name'] ?></option>
                            <?php }
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label">Contact:</label>
                <div class="col-sm-8">
                    <select name="bcc_contact[]" multiple class="chosen-select-deselect form-control">
                        <?php $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.") AND `deleted`=0 AND `status`>0 AND IFNULL(`email_address`,'') != ''"));
                        foreach($contact_list as $contact) {
                            if(!empty($contact['full_name']) && $contact['full_name'] != '-') { ?>
                                <option value="<?= $contact['contactid']; ?>"><?= $contact['full_name'] ?></option>
                            <?php }
                        } ?>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-4 control-label">Other Email:<br><em>Enter emails separated by a comma</em></label>
                <div class="col-sm-8">
                    <input type="text" name="bcc_other" class="form-control" value="">
                </div>
            </div>

            <div class="clearfix"></div><hr>

            <h5>Email Details:</h5>
        	<div class="form-group">
        		<label class="col-sm-4 control-label">Subject:</label>
        		<div class="col-sm-8">
        			<input type="text" name="subject" class="form-control" value="<?= $subject ?>">
        		</div>
        	</div>

        	<div class="form-group">
        		<label class="col-sm-4 control-label">Body:</label>
        		<div class="col-sm-8">
        			<textarea name="body" class="form-control"><?= $body ?></textarea>
        		</div>
        	</div>

        	<div class="form-group pull-right">
        		<a href="" class="btn brand-btn">Back</a>
        		<button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
        	</div>

        </form>
    </div>
</div>