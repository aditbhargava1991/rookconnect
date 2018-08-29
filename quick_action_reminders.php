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
        case 'project':
            $projectid = $id;
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`) VALUES ('$staff','$date','Project Reminder','".PROJECT_NOUN." Reminder','".htmlentities("This is a reminder about a ".PROJECT_NOUN.". Please log into the software to review the ".PROJECT_NOUN." <a href=\"".WEBSITE_URL."/Project/projects.php?edit=$id\">here</a>.")."','project','$id')");
            break;
        case 'sales':
            $salesid = $id;
            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`) VALUES ('$staff','$date','Sales Lead Reminder','$subject','".htmlentities("This is a reminder about a sales lead. Please log into the software to review the lead <a href=\"".WEBSITE_URL."/Sales/sale.php?p=details&id=$id\">here</a>.")."','sales','$id')");
            break;

        case 'tasks':
            $taskid = $id;
	        $sender = get_email($dbc, $_SESSION['contactid']);

		    $body = htmlentities("This is a reminder about the $title task.<br />\n<br />
			<a href=\"".WEBSITE_URL."/Tasks_Updated/index.php?category=$id&tab=$tab\">Click here</a> to see the task board.");
		    $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `list`.`task_board`, `list`.`heading`, `board`.`board_security` FROM `tasklist` AS `list` JOIN `task_board` AS `board` ON (`list`.`task_board`=`board`.`taskboardid`) WHERE `list`.`tasklistid`='$taskid'"));
            $tab = $result['board_security'];

            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`, `sender`) VALUES ('$staff','$date','Task Reminder','$subject','".htmlentities("This is a reminder about a Task. Please log into the software to review the Task <a href=\"".WEBSITE_URL."/Tasks_Updated/index.php?category=$id&tab=$tab\">here</a>.")."','tasklist','$id', '$sender')");
            break;

        case 'equipment':
            $equipmentid = $id;
            $equipment_label = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `equipmentid` = '".$_GET['id']."'"))['label'];

            $sender = get_email($dbc, $_SESSION['contactid']);
            $body = htmlentities("This is a reminder about ".$equipment_label.".<br />\n<br />
            <a href=\"".WEBSITE_URL."/Equipment/index.php?edit=$id\">Click here</a> to see the equipment.");

            $dbc->query("INSERT INTO `reminders` (`contactid`,`reminder_date`,`reminder_type`,`subject`,`body`,`src_table`,`src_tableid`, `sender`) VALUES ('$staff','$date','Equipment Reminder','$subject','$body','equipment','$id', '$sender')");
            break;

        default:
            break;
    }
}
$tile = $_GET['tile'];
switch($tile) {
    case 'sales':
        $subject = "Sales Lead Reminder";
        break;
    case 'tasks':
        $subject = "A reminder about the $title task";
        break;
    case 'equipment':
        $equipment_label = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT *, CONCAT(`category`, ' #', `unit_number`) label FROM `equipment` WHERE `equipmentid` = '".$_GET['id']."'"))['label'];
        $subject = "A reminder about ".$equipment_label;
        break;
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
            </div>
            <div class="form-group">
                <label class="col-sm-4 control-label">Reminder Heading:</label>
                <div class="col-sm-8">
                    <input type="text" name="reminder_heading" class="form-control" value="<?= $subject ?>">
                </div>
            </div>
        	<div class="form-group">
        		<label class="col-sm-4 control-label">Reminder Date:</label>
        		<div class="col-sm-8">
                    <input type="text" name="reminder_date" class="datepicker form-control">
                </div>
                <div class="form-group pull-right">
                    <a href="" class="btn brand-btn">Back</a>
                    <button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
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