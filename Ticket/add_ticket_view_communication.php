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
	$msgs = mysqli_query($dbc, "SELECT * FROM `email_communication` WHERE `ticketid`='$ticketid' AND `communication_type`='$communication_type' AND `deleted`=0 ORDER BY `email_communicationid` DESC");
}
$msg_count = mysqli_num_rows($msgs);
$msg_table = '<a href="" target="_blank" class="pull-right no-toggle" title="Download Communication Log PDF"><img src="'.WEBSITE_URL.'/img/pdf.png" class="inline-img"></a><div class="clearfix"></div>';
if($ticketid > 0 && $msg_count > 0) {
    $logo_upload = get_config($dbc, 'logo_upload');
    $logo_upload_icon = get_config($dbc, 'logo_upload_icon');
    $software_icon = empty($logo_upload_icon) ? (empty($logo_upload) ? WEBSITE_URL.'/img/logo.png' : WEBSITE_URL.'/Settings/download/'.$logo_upload) : WEBSITE_URL.'/Settings/download/'.$logo_upload_icon;
	while($row = mysqli_fetch_array($msgs)) {
		$msg_table .= '<div class="note_block">';
		$msg_table .= ($_SESSION['contactid'] > 0 && $row['created_by'] > 0 ? profile_id($dbc, $row['created_by'],false) : ($row['created_by'] > 0 ? '<img class="id-circle no-toggle" title="'.$row['from_name'].'" src="'.$software_icon.'">' : profile_id($dbc, $row['businessid'],false)));
		$msg_table .= '<div class="pull-right" style="width: calc(100% - 3.5em);">';
		$msg_table .= '<p><b>From: '.$row['from_name'].' &lt;'.$row['from_email'].'&gt;</b><br />';
		$msg_table .= (empty(trim($row['to_staff'].$row['to_contact'].$row['new_emailid'],';, ')) || $hide_recipient ? '' : '<b>To: '.implode('; ',array_filter(explode(',',$row['to_staff'].','.$row['to_contact'].','.$row['new_emailid']))).'</b><br />');
		$msg_table .= (empty(trim($row['cc_staff'].$row['cc_contact'],';, ')) || $hide_recipient ? '' : '<b>CC: '.implode('; ',array_filter(explode(',',$row['cc_staff'].','.$row['cc_contact']))).'</b><br />');
		$msg_table .= '<p><b>Subject: '.$row['subject'].'</b></p>';
		$msg_table .= html_entity_decode($row['email_body']).'</p>';
		$attachments = $dbc->query("SELECT * FROM `email_communicationid_upload` WHERE `email_communicationid`='".$row['email_communicationid']."'");
		if($attachments->num_rows > 0) {
			$msg_table .= '<h4>Attachments</h4><ul>';
			while($attach_row = $attachments->fetch_assoc()) {
				$msg_table .= '<li><a href="'.$include_folder.'../Email Communication/download/'.$attach_row['document'].'" target="_blank">'.$attach_row['document'].'</a></li>';
			}
			$msg_table .= '</ul>';
		}
		$msg_table .= "<em>Added by ".($row['created_by'] > 0 && $_SESSION['contactid'] > 0 ? get_contact($dbc, $row['created_by']) : $row['from_name'])." on ".date('Y-m-d',strtotime($row['today_date'])).(!empty(trim(date('Hi',strtotime($row['today_date'])),'0')) ? ' at '.date('H:i a',strtotime($row['today_date'])) : '')."</em>";
		$msg_table .= '</div><div class="clearfix"></div><hr></div>';
	}
} else {
    $msg_table = '<h3>No Communication Found</h3>';
}
$pdf_contents[] = [$communication_type.' Communication', $msg_table];
echo $msg_table;