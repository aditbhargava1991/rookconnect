<?php include_once('../include.php');
$output = '<p>'.$_POST['deliver_list'].'</p><p>'.$_POST['deliver_comment'].'</p>';

foreach(json_decode($_POST['output_list']) as $deliverable) {
	$deliverable = explode('|',$deliverable);
	$recipient = filter_var($_POST['deliver_to'],FILTER_SANITIZE_STRING);
	$subject = filter_var($_POST['deliver_subject'],FILTER_SANITIZE_STRING);
	$id_field = ($deliverable[0] == 'tickets' ? 'ticketid' : 'tasklistid');
	$id = filter_var($deliverable[1],FILTER_SANITIZE_STRING);
	$dbc->query("INSERT INTO `project_deliverables_output` (`userid`, `output_type`, `$id_field`, `recipient`, `subject`) VALUES ('{$_SESSION['contactid']}', 'email', '$id', '$recipient', '$subject')");
}
send_email([$_POST['deliver_from']=>$_POST['deliver_from_name']], $_POST['deliver_to'], '', '', $_POST['deliver_subject'], $output); ?>

<script>
window.location.replace('projects.php?edit=<?= $_POST['projectid'] ?>&tab=deliverables');
</script>