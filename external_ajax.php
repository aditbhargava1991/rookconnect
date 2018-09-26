<?php $guest_access = true;
include_once('include.php');
ob_clean();
$details = json_decode(decryptIt($_POST['action']));

if($details->action == 'ticket_comm_tags') {
    $ticketid = filter_var($details->ticketid,FILTER_SANITIZE_STRING);
    $comm_tags = filter_var(implode(',',$_POST['comm_tags']),FILTER_SANITIZE_STRING);
    if($ticketid > 0) {
        $dbc->query("UPDATE `tickets` SET `communication_tags`='$comm_tags' WHERE `ticketid`='$ticketid'");
        mysqli_query($dbc, "INSERT INTO `ticket_history` (`ticketid`, `description`) VALUES ('$ticketid','Communication Tags updated from External Access: $comm_tags')");
    }
}