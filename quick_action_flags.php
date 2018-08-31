<?php
/*
 * This should be called from everywhere there is a quick action to add notes/comments/replies
 * Accept the Tile name in a $_GET['tile']
 */
 
include_once('include.php');
checkAuthorised();
$html = '';

$id = filter_var($_GET['id'],FILTER_SANITIZE_STRING);
if(isset($_POST['submit'])) {
	$contactid = $_SESSION['contactid'];
	$tile = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
    if($_POST['submit'] == 'remove') {
        $flag_label = '';
        $flag_colour = '';
        $flag_start = '';
        $flag_end = '';
    } else {
        $flag_label = filter_var($_POST['flag_label'],FILTER_SANITIZE_STRING);
        $flag_colour = filter_var($_POST['flag_colour'],FILTER_SANITIZE_STRING);
        $flag_start = filter_var($_POST['flag_start'],FILTER_SANITIZE_STRING);
        $flag_end = filter_var($_POST['flag_end'],FILTER_SANITIZE_STRING);
    }
	$error = '';
    
    switch ($tile) {
        case 'projects':
            $projectid = $id;
            $before_change = capture_before_change($dbc, 'project', 'flag_colour', 'projectid', $id);
            $before_change .= capture_before_change($dbc, 'project', 'flag_start', 'projectid', $id);
            $before_change .= capture_before_change($dbc, 'project', 'flag_end', 'projectid', $id);
            $before_change .= capture_before_change($dbc, 'project', 'flag_label', 'projectid', $id);
            mysqli_query($dbc, "UPDATE `project` SET `flag_colour`='$flag_colour', `flag_start`='$flag_start', `flag_end`='$flag_end', `flag_label`='$flag_label' WHERE `projectid`='$id'");
            $history = capture_after_change('flag_colour', $flag_colour);
            $history .= capture_after_change('flag_start', $flag_start);
            $history .= capture_after_change('flag_end', $flag_end);
            $history .= capture_after_change('flag_label', $flag_label);
            add_update_history($dbc, 'project_history', $history, '', $before_change, $projectid); ?>
            <script>
            $(window.top.document).find('.flag_target').data('colour','<?= $flag_colour ?>');
            $(window.top.document).find('.flag_target').css('background-color','<?= empty($flag_colour) ? '' : '#'.$flag_colour ?>');
            $(window.top.document).find('.flag_target').find('.flag-label').text('<?= $flag_label ?>');
            $(window.top.document).find('.flag_target').removeClass('flag_target');
            </script>
            <?php break;
        case 'sales':
            $salesid = $id;
            $before_change = capture_before_change($dbc, 'sales', 'flag_colour', 'salesid', $id);
            $before_change .= capture_before_change($dbc, 'sales', 'flag_start', 'salesid', $id);
            $before_change .= capture_before_change($dbc, 'sales', 'flag_end', 'salesid', $id);
            $before_change .= capture_before_change($dbc, 'sales', 'flag_label', 'salesid', $id);
            mysqli_query($dbc, "UPDATE `sales` SET `flag_colour`='$flag_colour', `flag_start`='$flag_start', `flag_end`='$flag_end', `flag_label`='$flag_label' WHERE `salesid`='$id'");
            $history = capture_after_change('flag_colour', $flag_colour);
            $history .= capture_after_change('flag_start', $flag_start);
            $history .= capture_after_change('flag_end', $flag_end);
            $history .= capture_after_change('flag_label', $flag_label);
            add_update_history($dbc, 'sales_history', $history, '', $before_change, $salesid); ?>
            <script>
            $(window.top.document).find('.flag_target').data('colour','<?= $flag_colour ?>');
            $(window.top.document).find('.flag_target').css('background-color','<?= empty($flag_colour) ? '' : '#'.$flag_colour ?>');
            $(window.top.document).find('.flag_target').find('.flag-label').text('<?= $flag_label ?>');
            $(window.top.document).find('.flag_target').removeClass('flag_target');
            </script>
            <?php break;
        case 'tickets':
            $ticketid = $id;
            mysqli_query($dbc, "UPDATE `tickets` SET `flag_colour`='$flag_colour', `flag_start`='$flag_start', `flag_end`='$flag_end' WHERE `ticketid`='$id'");
            mysqli_query($dbc, "UPDATE `ticket_comment` SET `deleted`=1, `date_of_archival`=DATE(NOW()) WHERE `ticketid`='$id' AND `type`='flag_comment'");
            if(!empty($flag_label)) {
                mysqli_query($dbc, "INSERT INTO `ticket_comment` (`ticketid`,`type`,`comment`,`created_date`,`created_by`) VALUES ('$id','flag_comment','$flag_label',DATE(NOW()),'".$_SESSION['contactid']."')");
            }
            echo '<script type="text/javascript"> window.parent.setManualFlag(\''.$ticketid.'\', \''.$flag_colour.'\', \''.$flag_label.'\'); </script>';
            break;
        case 'tasklist':
            $tasklistid = $id;
            mysqli_query($dbc, "UPDATE `tasklist` SET `flag_colour`='$flag_colour', `flag_start`='$flag_start', `flag_end`='$flag_end', `flag_label`='$flag_label' WHERE `tasklistid`='$id'"); ?>
            <script>
            $(window.top.document).find('.flag_target').data('colour','<?= $flag_colour ?>');
            $(window.top.document).find('.flag_target').css('background-color','<?= empty($flag_colour) ? '' : '#'.$flag_colour ?>');
            $(window.top.document).find('.flag_target').find('.flag-label').text('<?= $flag_label ?>');
            $(window.top.document).find('.flag_target').removeClass('flag_target');
            </script>
            <?php break;
        case 'intake':
            $intakeid = $id;
            mysqli_query($dbc, "UPDATE `intake` SET `flag_colour`='$flag_colour', `flag_start`='$flag_start', `flag_end`='$flag_end', `flag_label`='$flag_label' WHERE `intakeid`='$id'"); ?>
            <script>
            $(window.top.document).find('.flag_target').data('colour','<?= $flag_colour ?>');
            $(window.top.document).find('.flag_target').css('background-color','<?= empty($flag_colour) ? '' : '#'.$flag_colour ?>');
            $(window.top.document).find('.flag_target').find('.flag-label').text('<?= $flag_label ?>');
            $(window.top.document).find('.flag_target').removeClass('flag_target');
            </script>
            <?php break;
        case 'checklist':
            $checklistid = $id;
            mysqli_query($dbc, "UPDATE `checklist` SET `flag_colour`='$flag_colour', `flag_start`='$flag_start', `flag_end`='$flag_end', `flag_label`='$flag_label' WHERE `checklistid`='$id'"); ?>
            <script>
            $(window.top.document).find('.flag_target').data('colour','<?= $flag_colour ?>');
            $(window.top.document).find('.flag_target').css('background-color','<?= empty($flag_colour) ? '' : '#'.$flag_colour ?>');
            $(window.top.document).find('.flag_target').find('.flag-label').text('<?= $flag_label ?>');
            $(window.top.document).find('.flag_target').removeClass('flag_target');
            </script>
            <?php break;
        default:
            break;
    }
} ?>

<?php switch ($_GET['tile']) {
    case 'projects':
        $row = $dbc->query("SELECT `flag_colour`,`flag_label`,`flag_start`,`flag_end` FROM `project` WHERE `projectid`='$id'")->fetch_assoc();
        break;
    case 'sales':
        $row = $dbc->query("SELECT `flag_colour`,`flag_label`,`flag_start`,`flag_end` FROM `sales` WHERE `salesid`='$id'")->fetch_assoc();
        break;
    case 'tickets':
        $row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `tickets`.*, `ticket_comment`.`comment` `flag_label` FROM `tickets` LEFT JOIN `ticket_comment` ON `tickets`.`ticketid` = `ticket_comment`.`ticketid` AND `ticket_comment`.`type` = 'flag_comment' WHERE `tickets`.`ticketid` = '".$_GET['id']."' ORDER BY `ticket_comment`.`ticketcommid` DESC"));
        break;
    case 'tasklist':
        $row = $dbc->query("SELECT `flag_colour`,`flag_label`,`flag_start`,`flag_end` FROM `tasklist` WHERE `tasklistid`='$id'")->fetch_assoc();
        break;
    case 'intake':
        $row = $dbc->query("SELECT `flag_colour`,`flag_label`,`flag_start`,`flag_end` FROM `intake` WHERE `intakeid`='$id'")->fetch_assoc();
        break;
    case 'checklist':
        $row = $dbc->query("SELECT `flag_colour`,`flag_label`,`flag_start`,`flag_end` FROM `checklist` WHERE `checklistid`='$id'")->fetch_assoc();
        break;
    default:
        break;
} ?>

<div class="container">
	<div class="row">
        <form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
        	<h3 class="inline">Add Flag</h3>
            <div class="pull-right gap-top"><a href=""><img src="../img/icons/ROOK-status-rejected.jpg" alt="Close" title="Close" class="inline-img" /></a></div>
            <div class="clearfix"></div>
            <hr />
            
            <input type="hidden" name="tile" value="<?= $_GET['tile'] ?>" />
        	<div class="form-group">
        		<label class="col-sm-4 control-label">Flag Label:</label>
        		<div class="col-sm-8">
                    <input type="text" name="flag_label" value="<?= $row['flag_label'] ?>" class="form-control">
                </div>
        	</div>
        	<div class="form-group">
        		<label class="col-sm-4 control-label">Flag Colour:</label>
        		<div class="col-sm-8">
                    <select name='flag_colour' class="form-control" style="background-color:#<?= $row['flag_colour'] ?>;font-weight:bold;" onchange="$(this).css('background-color','#'+$(this).find('option:selected').val());">
                        <option value="" style="background-color:#FFFFFF;">No Flag</option>
                        <?php foreach(explode(',', get_config($dbc, "ticket_colour_flags")) as $flag_colour) { ?>
                            <option <?= $row['flag_colour'] == $flag_colour ? 'selected' : '' ?> value="<?= $flag_colour ?>" style="background-color:#<?= $flag_colour ?>;"><?= $flag_colour ?></option>
                        <?php } ?>
                    </select>
                </div>
        	</div>
        	<div class="form-group">
        		<label class="col-sm-4 control-label">Start Date:</label>
        		<div class="col-sm-8">
                    <input type="text" name="flag_start" value="<?= $row['flag_start'] ?>" class="datepicker form-control">
                </div>
        	</div>
        	<div class="form-group">
        		<label class="col-sm-4 control-label">End Date:</label>
        		<div class="col-sm-8">
                    <input type="text" name="flag_end" value="<?= $row['flag_end'] ?>" class="datepicker form-control">
                </div>
        	</div>
        	<div class="form-group pull-right">
        		<a href="" class="btn brand-btn">Cancel</a>
        		<button type="submit" name="submit" value="remove" class="btn brand-btn">Remove Flag</button>
        		<button type="submit" name="submit" value="flag" class="btn brand-btn">Add Flag</button>
        	</div>
        </form>
    </div>
</div>