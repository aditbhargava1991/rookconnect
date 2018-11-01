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
    $date = date('Y/m/d');

    switch ($tile) {
        case 'tasks':
            $taskid = $id;

		    $result = mysqli_fetch_array(mysqli_query($dbc, "SELECT `list`.`task_board`, `list`.`heading`, `board`.`board_security` FROM `tasklist` AS `list` JOIN `task_board` AS `board` ON (`list`.`task_board`=`board`.`taskboardid`) WHERE `list`.`tasklistid`='$taskid'"));
            $tab = $result['board_security'];
	        $link = WEBSITE_URL."/Tasks_Updated/index.php?category=".$id."&tab=".$tab;
	        $sql = mysqli_query($dbc, "INSERT INTO `alerts` (`alert_date`, `alert_link`, `alert_text`, `alert_user`) VALUES ('$date', '$link', 'Task Alert', '$staff')");

			$note = "<em>Alert Assigned to ".get_contact($dbc, $staff)." by ".get_contact($dbc, $_SESSION['contactid'])." [PROFILE ".$_SESSION['contactid']."]</em>";
			mysqli_query($dbc, "INSERT INTO `task_comments` (`tasklistid`, `comment`, `created_by`, `created_date`) VALUES ('$id','".filter_var(htmlentities($note),FILTER_SANITIZE_STRING)."','".$_SESSION['contactid']."','".date('Y-m-d')."')");

            break;

        default:
            break;
    }
} ?>

<div class="container">
	<div class="row">
        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
        	<h3 class="inline">Add Alert</h3>
            <div class="pull-right gap-top"><a href=""><img src="../img/icons/cancel.png" alt="Close" title="Close" class="inline-img" /></a></div>
            <div class="clearfix"></div>
            <hr />

            <input type="hidden" name="tile" value="<?= $_GET['tile'] ?>" />
        	<div class="form-group">
        		<label class="col-sm-4 control-label">Staff:</label>
        		<div class="col-sm-8">
                    <select data-placeholder="Select Staff" name="staff" class="chosen-select-deselect"><option></option>
                    <?php foreach(sort_contacts_query($dbc->query("SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY."")) as $staff) { ?>
                        <option value="<?= $staff['contactid'] ?>"><?= $staff['full_name'] ?></option>
                    <?php } ?>
                    </select>
                </div>
        	</div>

        	<div class="form-group pull-right">
        		<a href="" class="btn brand-btn">Back</a>
        		<button type="submit" name="submit" value="Submit" class="btn brand-btn">Submit</button>
        	</div>
        </form>
    </div>
</div>