<?php
/*
 * This should be called from everywhere there is a quick action to add reminders
 * Accept the Tile name in a $_GET['tile']
 */

include_once('include.php');
checkAuthorised();
$html = '';

$id = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
if(isset($_POST['submit'])) {
	$contactid = $_SESSION['contactid'];
	$tile = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
	$staff = filter_var($_POST['staff'],FILTER_SANITIZE_STRING);
    $subject = filter_var($_POST['reminder_heading'],FILTER_SANITIZE_STRING);
	$date = filter_var($_POST['reminder_date'],FILTER_SANITIZE_STRING);

    switch ($tile) {
        case 'projects':
            $projectid = $id;
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`) VALUES ('$staff','$date','Project Reminder','$subject','".htmlentities("This is a reminder about a ".PROJECT_NOUN.". Please log into the software to review the ".PROJECT_NOUN." <a href=\"".WEBSITE_URL."/Project/projects.php?edit=$id\">here</a>.")."','project','$id')");
            break;
        case 'sales':
            $salesid = $id;
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`) VALUES ('$staff','$date','Sales Lead Reminder','$subject','".htmlentities("This is a reminder about a sales lead. Please log into the software to review the lead <a href=\"".WEBSITE_URL."/Sales/sale.php?p=details&id=$id\">here</a>.")."','sales','$id')");
            break;

        case 'intake':
            $salesid = $id;
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`) VALUES ('$staff','$date','Intake Form Reminder','$subject','".htmlentities("This is a reminder about an Intake Form. Please log into the software to review the form <a href=\"".WEBSITE_URL."/Intake/add_form.php?intakeid=$id\">here</a>.")."','intake','$id')");
            break;
        case 'invoice':
            $invoiceid = $id;
            $start_date = empty($_GET['end_date']) ? date('Y-m-d') : filter_var($_GET['start_date'],FILTER_SANITIZE_STRING);
            $end_date = empty($_GET['end_date']) ? date('Y-m-d') : filter_var($_GET['end_date'],FILTER_SANITIZE_STRING);
            $customer = filter_var($_GET['customer'],FILTER_SANITIZE_STRING);
            $url = empty($_GET['ar']) ? "invoice_list.php?search_from=".date('Y-m-d')."&search_to=".date('Y-m-d')."&search_invoice_submit=true" : "patient_account_receivables.php?p1=$start_date&p2=$end_date&p3=$customer&p5=$invoiceid";
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`) VALUES ('$staff','$date','Invoice Reminder','$subject','".htmlentities("This is a reminder about an Invoice. Please log into the software to review the form <a href=\"".WEBSITE_URL."/POSAdvanced/$url\">here</a>.")."','invoice')");
            break;
        case 'tasks':
            $taskid = $id;
	        $sender = get_email($dbc, $_SESSION['contactid']);

		    $body = htmlentities("This is a reminder about the $title task.<br />\n<br />
			<a href=\"".WEBSITE_URL."/Tasks_Updated/index.php?category=$id&tab=$tab\">Click here</a> to see the task board.");
		    $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `list`.`task_board`, `list`.`heading`, `board`.`board_security` FROM `tasklist` AS `list` JOIN `task_board` AS `board` ON (`list`.`task_board`=`board`.`taskboardid`) WHERE `list`.`tasklistid`='$taskid'"));
            $tab = $result['board_security'];

            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`, `sender`) VALUES ('$staff','$date','Task Reminder','$subject','".htmlentities("This is a reminder about a Task. Please log into the software to review the Task <a href=\"".WEBSITE_URL."/Tasks_Updated/index.php?category=$id&tab=$tab\">here</a>.")."','tasklist','$id', '$sender')");

			$note = '<em>Reminder added for '.get_contact($dbc, $staff).' [PROFILE '.$_SESSION['contactid'].']</em>';
			mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$taskid','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");

            break;

        case 'equipment':
            $equipmentid = $id;
            $equipment_label = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `equipmentid` = '".$_GET['id']."'"))['label'];

            $sender = get_email($dbc, $_SESSION['contactid']);
            $body = htmlentities("This is a reminder about ".$equipment_label.".<br />\n<br />
            <a href=\"".WEBSITE_URL."/Equipment/index.php?edit=$id\">Click here</a> to see the equipment.");

            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`, `sender`) VALUES ('$staff','$date','Equipment Reminder','$subject','$body','equipment','$id', '$sender')");
            break;

        case 'tickets':
            $ticketid = $id;
            $ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));

            $sender = get_email($dbc, $_SESSION['contactid']);
            $body = htmlentities("This is a reminder about a ".TICKET_NOUN.".<br />\n<br />
            <a href=\"".WEBSITE_URL."/Ticket/index.php?edit=$id\">Click here</a> to see the ".TICKET_NOUN.".<br />\n<br />");
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`, `sender`) VALUES ('$staff','$date','Ticket Reminder','$subject','$body','tickets','$id', '$sender')");
            break;

        case 'planner':
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`, `sender`) VALUES ('$staff','$date','Planner Reminder','$subject','$body','planner','$id', '$sender')");
            break;

        case 'intake':
            $intakeid = $id;
            $intake = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid`='$intakeid'"));
            $intake_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `intakeformid` = '".$intake['intakeformid']."'"));

            $sender = get_email($dbc, $_SESSION['contactid']);
            $body = htmlentities("This is a reminder about Intake #".$intake['intakeid'].": ".html_entity_decode($intake_form['form_name']).".<br />\n<br />");
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`, `sender`) VALUES ('$staff','$date','Intake Reminder','$subject','$body','intake','$id', '$sender')");
            break;

        case 'email':
            $communication_id = $id;

            $sender = get_email($dbc, $_SESSION['contactid']);
            $body = htmlentities("This is a reminder about an Email Communication.<br />\n<br />
            <a href=\"".WEBSITE_URL."/Email Communication/view_email.php?email_communicationid=$communication_id\">Click here</a> to see the Email Communication.<br />\n<br />");
            $dbc->query("INSERT INTO `reminders` (`contactid`, `reminder_date`, `reminder_type`, `subject`, `body`, `src_table`, `src_tableid`, `sender`) VALUES ('$staff', '$date', 'Email Communication Reminder', '$subject','$body', 'email_communication', '$communication_id', '$sender')");
            break;

        case 'checklist':
            $checklistid = $id;
            $sender = get_email($dbc, $_SESSION['contactid']);

            $body = htmlentities("This is a reminder about the $title checklist.<br />\n<br />
            <a href=\"".WEBSITE_URL."/Checklist/checklist.php?subtabid=$tab&view=$id\">Click here</a> to see the checklist board.");
            $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `list`.`task_board`, `list`.`heading`, `board`.`board_security` FROM `checklist` AS `list` JOIN `task_board` AS `board` ON (`list`.`task_board`=`board`.`taskboardid`) WHERE `list`.`checklistid`='$checklistid'"));
            $tab = $result['board_security'];

            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`, `sender`) VALUES ('$staff','$date','Checklist Reminder','$subject','".htmlentities("This is a reminder about a Checklist. Please log into the software to review the Checklist <a href=\"".WEBSITE_URL."/Checklist/checklist.php?subtabid=$tab&view=$id\">here</a>.")."','checklist','$id', '$sender')");
            break;
        case 'contacts':
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`) VALUES ('$staff','$date','Contacts Reminder','$subject','".htmlentities("This is a reminder about a ".CONTACTS_TILE.". Please log into the software to review the ".CONTACTS_TILE." <a href=\"".WEBSITE_URL."/Contacts/contacts_inbox.php?category=Customers&edit=$id\">here</a>.")."','contacts','$id')");
            break;
        case 'estimates':
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`) VALUES ('$staff','$date','Estimates Reminder','$subject','".htmlentities("This is a reminder about a ".ESTIMATE_TILE.". Please log into the software to review the ".ESTIMATE_TILE." <a href=\"".WEBSITE_URL."/Estimate/estimates.php?edit=$id\">here</a>.")."','estimates','$id')");
            break;
        case 'sales_order':
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`) VALUES ('$staff','$date','Sales orders','$subject','".htmlentities("This is a reminder about a ".SALES_ORDER_NOUN.". Please log into the software to review the ".SALES_ORDER_NOUN." <a href=\"".WEBSITE_URL."/Sales Order/index.php?p=preview&sotid=$id\">here</a>.")."','sales_order','$id')");
            break;
        case 'add_intake':
            $salesid = $id;
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`) VALUES ('$staff','$date','Intake Form Reminder','$subject','".htmlentities("This is a reminder about an Intake Form. Please log into the software to review the form <a href=\"".WEBSITE_URL."/Intake/add_intake.php?edit=$id\">here</a>.")."','add_intake','$id')");
            break;

        default:
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`, `sender`) VALUES ('$staff','$date','Planner Reminder','$subject','$body', '$sender')");
            break;
    }
}
$tile = $_GET['tile'];
switch($tile) {
    case 'projects':
        $project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$_GET['id']."'"));
        $subject = "A reminder about a ".PROJECT_NOUN." - ".get_project_label($dbc, $project);
        break;
    case 'sales':
        $subject = "Sales Lead Reminder";
        break;
    case 'intake':
        $subject = "Intake Form Reminder";
        break;
    case 'invoice':
        $subject = "Invoice Reminder".($id > 0 ? " for Invoice #".$id : '');
        break;
    case 'tasks':
        $type = $_GET['type'];
        $subject = "A reminder about the $type";
        break;
    case 'equipment':
        $equipment_label = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `equipmentid` = '".$_GET['id']."'"))['label'];
        $subject = "A reminder about ".$equipment_label;
        break;
    case 'tickets':
        $ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '".$_GET['id']."'"));
        $subject = "A reminder about a ".TICKET_NOUN." - ".get_ticket_label($dbc, $ticket);
        break;
    case 'intake':
        $intake = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid`='".$_GET['id']."'"));
        $intake_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `intakeformid` = '".$intake['intakeformid']."'"));
        $subject = "A reminder about Intake #".$intake['intakeid'].": ".html_entity_decode($intake_form['form_name']);
        break;
    case 'checklist':
        $subject = "A reminder about the $title checklist";
        break;
    case 'contacts':
        $subject = "Contacts Reminder";
        break;
    case 'estimates':
        $subject = "Estimates Reminder";
        break;
    case 'sales_order':
        $subject = "Sales orders";
        break;
    case 'add_intake':
        $subject = "Add Intake Form Reminder";
    default:
        $subject = ucwords($tile) .' Reminder';
        break;
}
if(empty($_GET['contactid'])) {
    $_GET['contactid'] = $_SESSION['contactid'];
}
?>
<?php if(empty($_GET['view'])) { ?>
    <div class="container">
        <div class="row">
            <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
                <h3 class="inline">Add Reminder</h3>
                <div class="pull-right gap-top"><a href=""><img src="../img/icons/cancel.png" alt="Close" title="Close" class="inline-img" /></a></div>
                <div class="clearfix"></div>
                <hr />

                <input type="hidden" name="tile" value="<?= $_GET['tile'] ?>" />
                <div class="form-group">
                    <label class="col-sm-4 control-label">Staff:</label>
                    <div class="col-sm-8">
                        <select data-placeholder="Select Staff" name="staff" class="chosen-select-deselect"><option></option>
                        <?php foreach(sort_contacts_query($dbc->query("SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY."")) as $staff) { ?>
                            <option <?= $_GET['contactid'] == $staff['contactid'] ? 'selected' : '' ?> value="<?= $staff['contactid'] ?>"><?= $staff['full_name'] ?></option>
                        <?php } ?>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-4 control-label">Reminder Notes:</label>
                    <div class="col-sm-8">
                    	<textarea rows="" cols="" name="reminder_heading" class="form-control"><?= $subject ?></textarea>
                        <!-- <input type="text" name="reminder_heading" class="form-control" value="<?= $subject ?>"> -->
                    </div>
                </div>

            	<div class="form-group">
            		<label class="col-sm-4 control-label">Reminder Date:</label>
            		<div class="col-sm-8">
                        <input type="text" name="reminder_date" class="datepicker form-control">
                    </div>
                </div>

                <div class="form-group">
                    <div class="form-group pull-right">
                        <a href="" class="btn brand-btn">Cancel</a>
                        <button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<?php } else {
    $contactid = filter_var($_GET['contactid'],FILTER_SANITIZE_STRING); ?>
    <div id="no-more-tables" class="col-sm-12">
        <h3>Reminders for <?= get_contact($dbc, $contactid) ?><a href="blank_loading_page.php" class="pull-right"><img src="img/icons/cancel.png" class="inline-img"></a></h3>
        <?php $reminders = $dbc->query("SELECT * FROM `reminders` WHERE `reminder_type`='Project Reminder' AND `src_tableid`='$id' AND `deleted`=0 AND `contactid`='$contactid'");
        if($reminders->num_rows > 0) { ?>
            <table class="table table-bordered">
                <tr class="hidden-sm hidden-xs">
                    <th>Date</th>
                    <th>Subject</th>
                    <th>Status</th>
                </tr>
                <?php while($reminder = $reminders->fetch_assoc()) { ?>
                    <tr>
                        <td data-title="Date"><?= $reminder['reminder_date'] ?></td>
                        <td data-title="Subject"><?= $reminder['subject'] ?></td>
                        <td data-title="Status"><?= $reminder['sent'] > 0 ? 'Sent' : 'Not Sent' ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } else { ?>
            <h3>No Reminders Found</h3>
        <?php } ?>
    </div>
<?php } ?>