<?php include_once('include.php');
checkAuthorised();

if(isset($_POST['submit'])) {
	$sender = get_email($dbc, $_SESSION['contactid']);
	$subject = filter_var($_POST['subject'],FILTER_SANITIZE_STRING);
	$body = filter_var(htmlentities($_POST['body'],FILTER_SANITIZE_STRING));
	$error = '';
	foreach($_POST['staff'] as $user) {
		if($user > 0) {
            $body = str_replace(['[STAFF_NAME]'],[get_contact($dbc, $user)],$body);
			$user = get_email($dbc, $user);
			try {
				send_email($sender, $user, '', '', $subject, html_entity_decode($body), '');
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
            <div class="pull-right"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img" /></a></div>
            <div class="clearfix"></div>
            <hr />

        	<div class="form-group">
        		<label class="col-sm-4 control-label">Staff:</label>
        		<div class="col-sm-8">
        			<select name="staff[]" multiple class="chosen-select-deselect form-control">
        				<option></option>
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