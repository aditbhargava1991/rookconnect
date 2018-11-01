<?php include('../../include.php');
$id = filter_var($_GET['hrid'],FILTER_SANITIZE_STRING);
$request = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `hr_time_off_request` WHERE `fieldlevelriskid`='$id'"));
$fields = explode('**FFM**',$request['fields']);
$status = $_GET['status'];
$ticket_booking_conflicts = '';
if($status == 'Denied') {
	$message = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `rejected_subject`, `rejected_message` FROM `hr` WHERE `hrid`='".$request['hrid']."'"));
	mysqli_query($dbc, "UPDATE `hr_time_off_request` SET `status`='Denied' WHERE `fieldlevelriskid`='$id'");
	send_email('', get_email($dbc, $request['contactid']), '', '', $message['rejected_subject'], html_entity_decode($message['rejected_message']), '');
} else if($status == 'Approved') {
	$message = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `approval_subject`, `approval_message` FROM `hr` WHERE `hrid`='".$request['hrid']."'"));
	mysqli_query($dbc, "UPDATE `hr_time_off_request` SET `status`='Approved' WHERE `fieldlevelriskid`='$id'");
	mysqli_query($dbc, "INSERT INTO `contacts_shifts` (`contactid`, `startdate`, `enddate`, `starttime`, `endtime`, `dayoff_type`) VALUES ('".$request['contactid']."', '".$fields[2]."', '".$fields[3]."', '12:00 am', '11:59 pm', '".($fields[0] == 'Other' ? $fields[1] : ($fields[0] == '' ? 'Other' : $fields[0]))."')");
	for($current_date = $fields[2]; strtotime($current_date) <= strtotime($fields[3]); $current_date = date('Y-m-d', strtotime($current_date.'+ 1 day'))) {
		mysqli_query($dbc, "INSERT INTO `time_cards` (`staff`, `date`, `type_of_time`, `total_hrs`) VALUES ('".$request['contactid']."', '$current_date', 'Vac Hrs.', '24')");
	}
	try {
		send_email('', get_email($dbc, $request['contactid']), '', '', $message['approval_subject'], html_entity_decode($message['approval_messsage']), '');
	} catch (Excpetion $e) {}
}

$refer = $_SERVER['HTTP_REFERER'];
if($refer == '') {
	$refer = WEBSITE_URL.'/Staff/staff_edit.php?contactid='.$request['contactid'].'&subtab=time_off_requests';
} else if(strpos($refer,'Staff/staff_edit.php?') !== FALSE) {
	$refer .= '&subtab=time_off_requests';
}
if($status == 'Approved') { ?>
	<script type="text/javascript">
	$(document).ready(function() {
		var contactid = '<?= $request['contactid'] ?>';
		var startdate = '<?= $fields[2] ?>';
		var enddate = '<?= $fields[3] ?>';
		$.ajax({
			url: '<?= WEBSITE_URL ?>/Calendar/calendar_ajax_all.php?fill=check_ticket_booking_conflicts',
			method: 'POST',
			data: { contactid: contactid, startdate: startdate, enddate: enddate },
			success: function(response) {
				if(response != '') {
					response += "\nWould you like to unassign <?= get_contact($dbc, $request['contactid']) ?> from these conflicting items?";
					if(confirm(response)) {
						$.ajax({
							url: '<?= WEBSITE_URL ?>/Calendar/calendar_ajax_all.php?fill=unassign_ticket_booking_conflicts',
							method: 'POST',
							data: { contactid: contactid, startdate: startdate, enddate: enddate },
							success: function(response) {
								window.location.replace('<?= $refer ?>');
							}
						});
					} else {
						window.location.replace('<?= $refer ?>');	
					}
				} else {
					window.location.replace('<?= $refer ?>');
				}
			}
		});
	});
	</script>
<?php } else {
	echo "<script> window.location.replace('$refer'); </script>";
}