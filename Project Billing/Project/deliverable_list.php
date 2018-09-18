<?php include_once('../include.php');
if(!is_array($_POST['list'])) {
	$_POST['list'] = explode(',',$_POST['list']);
}
ob_clean();
echo '<input type="hidden" name="list" value="'.implode(',',$_POST['list']).'">';
$output = '';

foreach($_POST['list'] as $deliverable) {
	$deliverable = explode('|',$deliverable);
	if($deliverable[0] == 'tickets') {
		$ticketid = filter_var($deliverable[1],FILTER_SANITIZE_STRING);
		$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));
		$output .= "<p><b>Company: ".get_contact($dbc, $ticket['businessid'], 'name')."</b><br />\n";
		$output .= TICKET_NOUN.": ".get_ticket_label($dbc, $ticket)."<br />\n";
		$output .= "Estimated Development Completion Date: ".$ticket['to_do_date']."<br />\n";
		$output .= "Estimated Internal QA Date: ".$ticket['internal_qa_date']."<br />\n";
		$output .= "Estimated Customer QA Date: ".$ticket['deliverable_date'];
		if($_POST['details'] > 0) {
			$output .= "<br />\n<b>Details</b>\n".html_entity_decode($ticket['assign_work']);
		}
		$output .= "</p>\n";
	} else if($deliverables[0] == 'tasklist') {
		$tasklistid = filter_var($deliverable[1],FILTER_SANITIZE_STRING);
		$task = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE `tasklistid`='$tasklistid'"));
		$output .= "<p><b>Company: ".get_contact($dbc, $task['businessid'], 'name')."</b><br />\n";
		$output .= "Task #".$task['tasklistid'].': '.$task['heading']."<br />\n";
		$output .= "Estimated Completion Date: ".$task['task_tododate'];
		$output .= "</p>\n";
	}
}
echo $output.'#*#'.json_encode($_POST['list']);
