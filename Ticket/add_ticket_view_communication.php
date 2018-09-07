<?php if(!isset($include_folder)) {
    $include_folder = '';
}
include_once($include_folder.'../include.php');
include_once($include_folder.'../Ticket/field_list.php');
if(isset($_GET['ticketid']) && empty($ticketid)) {
	ob_clean();
	$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
	$communication_type = filter_var($_GET['comm_type'],FILTER_SANITIZE_STRING);
	$communication_method = filter_var($_GET['comm_mode'],FILTER_SANITIZE_STRING);
}

if($communication_method == 'email') {
	$msgs = mysqli_query($dbc, "SELECT * FROM `email_communication` WHERE `ticketid`='$ticketid' AND `communication_type`='$communication_type' AND `deleted`=0");
}
$msg_count = mysqli_num_rows($msgs);
$msg_table = '';
if($ticketid > 0 && $msg_count > 0) {
	while($row = mysqli_fetch_array($msgs)) {
		$msg_table .= '<div class="note_block">';
		if($row['businessid'] > 0) {
			$msg_table .= BUSINESS_CAT.': <a href="../Contacts/contacts_inbox.php?edit='.$row['businessid'].'" onclick="overlayIFrameSlider(this.href+\'&fields=all_fields\',\'auto\',true,true); return false;">'.get_contact($dbc, $row['businessid'], 'name_company').'</a><br />';
		}
		$individuals = [];
		foreach(array_filter(explode(',',$row['contactid'])) as $row_contactid) {
			$individuals[] = '<a href="../Contacts/contacts_inbox.php?edit='.$row_contactid.'" onclick="overlayIFrameSlider(this.href+\'&fields=all_fields\',\'auto\',true,true); return false;">'.get_contact($dbc, $row_contactid, 'name_company').'</a>';
		}
		if(count($individuals) > 0) {
			$msg_table .= 'Individuals: '.implode(', ',$individuals).'<br />';
		}
		$msg_table .= profile_id($dbc, $row['created_by'],false);
		$msg_table .= '<div class="pull-right" style="width: calc(100% - 3.5em);">';
		$msg_table .= '<p><b>From: '.$row['from_name'].' &lt;'.$row['from_email'].'&gt;</b><br />';
		$msg_table .= ($hide_recipient ? '' : '<b>To: '.implode('; ',array_filter(explode(',',$row['to_staff'].','.$row['to_contact'].','.$row['new_emailid']))).'</b><br />');
		$msg_table .= ($hide_recipient ? '' : '<b>CC: '.implode('; ',array_filter(explode(',',$row['cc_staff'].','.$row['cc_contact']))).'</b><br />');
		$msg_table .= '<b>Subject: '.$row['subject'].'</b></p>';
		$msg_table .= html_entity_decode($row['email_body']);
		$attachments = $dbc->query("SELECT * FROM `email_communicationid_upload` WHERE `email_communicationid`='".$row['email_communicationid']."'");
		if($attachments->num_rows > 0) {
			$msg_table .= '<h4>Attachments</h4><ul>';
			while($attach_row = $attachments->fetch_assoc()) {
				$msg_table .= '<li><a href="'.$include_folder.'../Email Communication/download/'.$attach_row['document'].'" target="_blank">'.$attach_row['document'].'</a></li>';
			}
			$msg_table .= '</ul>';
		}
		$msg_table .= "<em>Added by ".get_contact($dbc, $row['created_by'])." on ".$row['today_date']."</em>";
		$msg_table .= '</div><div class="clearfix"></div><hr></div>';
	}
}
$pdf_contents[] = [$communication_type.' Communication', $msg_table];
echo $msg_table;