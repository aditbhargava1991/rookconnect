<?php require_once('../include.php');
checkAuthorised('sales');
$lead_convert_to = get_config($dbc, 'lead_convert_to');

function invalid_id() { ?>
	<script>
		alert("Invalid Sales Lead, unable to save to <?= $lead_convert_to ?>.");
		window.history.back();
	</script>
	exit();
<?php }

if(empty($_GET['leadid'])) {
	invalid_id();
} else {
    $won_status = get_config($dbc, 'lead_status_won');
    $lost_status = get_config($dbc, 'lead_status_lost');
	if($result = mysqli_query($dbc, "SELECT * FROM `sales` WHERE `salesid`='".$_GET['leadid']."'")) {
		$lead_info = mysqli_fetch_array($result);
	} else {
		invalid_id();
	}
	if($_GET['won_lead'] == 'true' && $result = mysqli_query($dbc, "UPDATE `sales` SET `status`='".$won_status."' WHERE `salesid`='".$lead_info['salesid']."'")) {
		$businessid = $lead_info['businessid'];
		$contactid = $lead_info['contactid'];
		$phone = $lead_info['primary_number'];
		$email = $lead_info['email_address'];
		if($contactid == 0) {
			$result = mysqli_query($dbc, "INSERT INTO `contacts` (`category`) VALUES ('Customers')");
			$lead_info['contactid'] = mysqli_insert_id($dbc);
		}
        foreach(explode(',',$contactid) as $contact) {
            mysqli_query($dbc, "UPDATE `contacts` SET `category`='$lead_convert_to' WHERE `contactid`='".$contact."'");
        }
		echo "<h3>Sales Lead saved to $lead_convert_to!</h3>";
		echo "<script>setTimeout(function() { window.location.replace('".WEBSITE_URL."/Contacts/contacts_inbox.php?category=".$lead_convert_to."') }, 1000);</script>";
	} else if($_GET['won_lead'] == 'lost' && $result = mysqli_query($dbc, "UPDATE `sales` SET `status`='".$lost_status."' WHERE `salesid`='".$lead_info['salesid']."'")) {
		echo "<h3>Sales Lead marked as $lost_status</h3>";
		echo "<script>setTimeout(function() { window.location.replace('index.php') }, 1000);</script>";
	} else if($_GET['won_lead'] == 'keep') {
		$contactid = $lead_info['contactid'];
        foreach(explode(',',$contactid) as $contact) {
            mysqli_query($dbc, "UPDATE `contacts` SET `category`='$lead_convert_to' WHERE `contactid`='".$contact."'");
        }
        
		echo "<h3>Sales Lead saved to $lead_convert_to!</h3>";
		echo "<script>setTimeout(function() { window.location.replace('".WEBSITE_URL."/Contacts/contacts_inbox.php?category=".$lead_convert_to."') }, 1000);</script>";
	} else {
		invalid_id();
	}
}