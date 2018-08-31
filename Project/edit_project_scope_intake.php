<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
$project_security = get_security($dbc, 'project');
$form_count = 0; ?>
<!-- <h3>Intake Forms</h3> -->
<?php
$forms = mysqli_query($dbc, "SELECT * FROM `intake` WHERE `deleted` = 0 AND `projectid` = '$projectid' ORDER BY `intakeid` DESC");
if($forms->num_rows > 0) { ?>
    <table class="table table-bordered">
        <tr class="hidden-sm hidden-xs">
            <th>Date Completed</th>
            <th>Intake Form</th>
            <th>Created By</th>
            <th></th>
        </tr>
    <?php while($form = mysqli_fetch_assoc($forms)) {
        $intake_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake_forms` WHERE `intakeformid` = '".$form['intakeformid']."'")); ?>
        <tr>
            <td data-title="Date Completed"><?= $form['received_date'] ?></td>
            <td data-title="Intake Form"><a href="../Intake/add_form.php?intakeid=<?= $form['intakeid'] ?>" onclick="overlayIFrameSlider(this.href); return false;"><?= !empty($intake_form['form_name']) ? $intake_form['form_name'] : 'Intake Form' ?> #<?= $form['intakeid'] ?>:  <?= !empty($form['contactid']) ? get_contact($dbc, $form['contactid']) : (!empty($form['name']) ? $form['name'] : 'No Contact') ?></a></td>
            <td data-title="Created By"><?= get_contact($dbc, get_field_value('user_id','user_form_pdf','pdf_id',$form['pdf_id'])) ?></td>
            <td data-title="View Form">
                <img src="../img/icons/ROOK-reminder-icon.png" class="inline-img pull-right cursor-hand" onclick="overlayIFrameSlider('../quick_action_reminders.php?tile=intake&id=<?= $form['intakeid'] ?>','auto',true,true);">
                <img src="../img/icons/ROOK-email-icon.png" class="inline-img pull-right cursor-hand" onclick="overlayIFrameSlider('../Email Communication/add_email.php?type=external&subject=<?= urlencode($intake_form['form_name']) ?>&projectid=<?= $projectid ?><?= file_exists('../Intake/'.$form['intake_file']) ? '&attach_docs='.urlencode('../Intake/'.$form['intake_file']) : '' ?>', 'auto', false, true);">
                <?php $pdf_id = $form['pdf_id'];
                $pdf_list = $dbc->query("SELECT * FROM `user_form_assign` WHERE `pdf_id`='$pdf_id' AND `pdf_id` > 0 AND `deleted`=0 ORDER BY `assign_id` DESC");
                if($pdf_list->num_rows > 0) {
                    $i = 0;
                    $file_name = $form['intake_file'];
                    while($pdf_row = $pdf_list->fetch_assoc()) {
                        $file_name = str_replace('_'.$file_id.'.','_'.$pdf_row['assign_id'].'.',$file_name);
                        $file_id = $pdf_row['assign_id'];
                        if(file_exists('../Intake/'.$file_name)) { ?>
                            <a href="../Intake/<?= $file_name ?>"><?= $i == 0 && $pdf_list->num_rows > 1 ? 'Current' : '' ?> PDF <?= $i > 0 ? '#'.($pdf_list->num_rows - $i) : '' ?> <img src="../img/pdf.png" class="inline-img"></a><br />
                        <?php }
                        $i++;
                    }
                } ?>
            </td>
        </tr>
    <?php } ?>
    </table>
<?php } else {
	echo "<h2>No Intake Forms Found</h2>";
} ?>
<?php include('next_buttons.php'); ?>