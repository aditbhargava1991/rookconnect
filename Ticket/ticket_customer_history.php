<?php include_once('../include.php');

if(IFRAME_MODE) { ?>
	<script type="text/javascript">
	$(document).ready(function() {
		$('.customer_div a').click(function() {
			window.parent.location.href = $(this).prop('href');
			return false;
		});
	});
	</script>
<?php }

$block = '';
if($history_field == 'Customer History Business Ticket Type') {
	if($businessid > 0) {
		$block .= '<h5 style="color: black;">'.get_client($dbc, $businessid).'</h5>';
		$tickets = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticket_type` = '$ticket_type' AND `businessid` = '$businessid' AND `deleted` = 0 AND `ticketid` != '$ticketid' ORDER BY `ticketid` DESC LIMIT 0, 5");
		if(mysqli_num_rows($tickets) > 0) {
			while($row = mysqli_fetch_assoc($tickets)) {
				$block .= get_ticket_block($dbc, $row, $history_fields);
			}
		} else {
			$block .= '<p>No '.TICKET_TILE.' Found.</p>';
		}
	} else {
		$block .= '<p>No Business Found</p>';
	}
} else if($history_field == 'Customer History Business Project Type') {
	if($businessid > 0 && $projectid > 0) {
		$block .= '<h5 style="color: black;">'.get_client($dbc, $businessid).'</h5>';
		$tickets = mysqli_query($dbc, "SELECT `tickets`.* FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid` = `project`.`projectid` WHERE `tickets`.`businessid` = '$businessid' AND `project`.`projecttype` = '$projecttype' AND `tickets`.`deleted` = 0 AND `tickets`.`ticketid` != '$ticketid' ORDER BY `tickets`.`ticketid` DESC LIMIT 0, 5");
		if(mysqli_num_rows($tickets) > 0) {
			while($row = mysqli_fetch_assoc($tickets)) {
				$block .= get_ticket_block($dbc, $row, $history_fields);
			}
		} else {
			$block .= '<p>No '.TICKET_TILE.' Found.</p>';
		}
	} else {
		$block .= '<p>No Business or '.PROJECT_NOUN.' Found<p>';
	}
} else if($history_field == 'Customer History Business Ticket Project Type') {
	if($businessid > 0 && $projectid > 0) {
		$block .= '<h5 style="color: black;">'.get_client($dbc, $businessid).'</h5>';
		$tickets = mysqli_query($dbc, "SELECT `tickets`.* FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid` = `project`.`projectid` WHERE `tickets`.`businessid` = '$businessid' AND `project`.`projecttype` = '$projecttype' AND `tickets`.`ticket_type` = '$ticket_type' AND `tickets`.`deleted` = 0 AND `tickets`.`ticketid` != '$ticketid' ORDER BY `tickets`.`ticketid` DESC LIMIT 0, 5");
		if(mysqli_num_rows($tickets) > 0) {
			while($row = mysqli_fetch_assoc($tickets)) {
				$block .= get_ticket_block($dbc, $row, $history_fields);
			}
		} else {
			$block .= '<p>No '.TICKET_TILE.' Found.</p>';
		}
	} else {
		$block .= '<p>No Business or '.PROJECT_NOUN.' Found</p>';
	}
} else if($history_field == 'Customer History Customer Ticket Type') {
	if(!empty($clientid)) {
		foreach(explode(',', $clientid) as $client_id) {
			if($client_id > 0) {
				$block .= '<h5 style="color: black;">'.(!empty(get_client($dbc, $client_id)) ? get_client($dbc, $client_id) : get_contact($dbc, $client_id)).'</h5>';
				$tickets = mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticket_type` = '$ticket_type' AND CONCAT(',',`clientid`,',') LIKE ('%,$client_id,%') AND `deleted` = 0 AND `ticketid` != '$ticketid' ORDER BY `ticketid` DESC LIMIT 0, 5");
				if(mysqli_num_rows($tickets) > 0) {
					while($row = mysqli_fetch_assoc($tickets)) {
						$block .= get_ticket_block($dbc, $row, $history_fields);
					}
				} else {
					$block .= '<p>No '.TICKET_TILE.' Found.</p>';
				}
			}
		}
	} else {
		$block .= '<p>No Client Found</p>';
	}
} else if($history_field == 'Customer History Customer Project Type') {
	if(!empty($clientid)) {
		foreach(explode(',', $clientid) as $client_id) {
			if($client_id > 0 && $projectid > 0) {
				$block .= '<h5 style="color: black;">'.(!empty(get_client($dbc, $client_id)) ? get_client($dbc, $client_id) : get_contact($dbc, $client_id)).'</h5>';
				$tickets = mysqli_query($dbc, "SELECT `tickets`.* FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid` = `project`.`projectid` WHERE CONCAT(',',`tickets`.`clientid`,',') LIKE ('%,$client_id,%') AND `project`.`projecttype` = '$projecttype' AND `tickets`.`deleted` = 0 AND `tickets`.`ticketid` != '$ticketid' ORDER BY `tickets`.`ticketid` DESC LIMIT 0, 5");
				if(mysqli_num_rows($tickets) > 0) {
					while($row = mysqli_fetch_assoc($tickets)) {
						$block .= get_ticket_block($dbc, $row, $history_fields);
					}
				} else {
					$block .= '<p>No '.TICKET_TILE.' Found.</p>';
				}
			}
		}
	} else {
		$block .= '<p>No Client or '.PROJECT_NOUN.' Found</p>';
	}
} else if($history_field == 'Customer History Customer Ticket Project Type') {
	if(!empty($clientid)) {
		foreach(explode(',', $clientid) as $client_id) {
			if($client_id > 0 && $projectid > 0) {
				$block .= '<h5 style="color: black;">'.(!empty(get_client($dbc, $client_id)) ? get_client($dbc, $client_id) : get_contact($dbc, $client_id)).'</h5>';
				$tickets = mysqli_query($dbc, "SELECT `tickets`.* FROM `tickets` LEFT JOIN `project` ON `tickets`.`projectid` = `project`.`projectid` WHERE CONCAT(',',`tickets`.`clientid`,',') LIKE ('%,$client_id,%') AND `project`.`projecttype` = '$projecttype' AND `tickets`.`ticket_type` = '$ticket_type' AND `tickets`.`deleted` = 0 AND `tickets`.`ticketid` != '$ticketid' ORDER BY `tickets`.`ticketid` DESC LIMIT 0, 5");
				if(mysqli_num_rows($tickets) > 0) {
					while($row = mysqli_fetch_assoc($tickets)) {
						$block .= get_ticket_block($dbc, $row, $history_fields);
					}
				} else {
					$block .= '<p>No '.TICKET_TILE.' Found.</p>';
				}
			}
		}
	} else {
		$block .= '<p>No Client or '.PROJECT_NOUN.' Found</p>';
	}
}
echo $block;