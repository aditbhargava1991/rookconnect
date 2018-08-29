<?php
/*
Dashboard
*/
include_once('../include.php');
error_reporting(0);
$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
?>
</head>

	<body>
<div class="container">
	<div class="row">
		<a href="" class="pull-right block-group"><img class="inline-img" src="../img/close.png"></a>
        <h1><?= TICKET_NOUN ?> History</h1>
		<table class="table table-bordered">
			<tr>
				<th>Date</th>
				<th>User</th>
				<th>Description</th>
			</tr>
			<tr>
				<?php $ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `created_by`, `created_date` FROM `tickets` WHERE `ticketid`='$ticketid'"));
				$name = get_contact($dbc, $ticket['created_by']);
				if($name == '' || $name == '-') {
					$name = 'Admin';
				} ?>
				<td data-title="Date"><?= $ticket['created_date'] ?></td>
				<td data-title="User"><?= $name ?></td>
				<td data-title="Description"><?= TICKET_NOUN ?> Created</td>
			</tr>
			<?php $result_tickets = mysqli_query($dbc, "SELECT * FROM ticket_history WHERE ticketid ='$ticketid' ORDER BY `date` DESC");
			while($history = mysqli_fetch_assoc($result_tickets)) {
				$name = get_contact($dbc, $history['userid']);
				if($name == '' || $name == '-') {
					$name = 'Admin';
				}
                if ( strpos($history['description'], 'sign_off_signature updated to')!==false ) {
                    $description = substr($history['description'], 0, strpos($history['description'], 'sign_off_signature updated to')) . 'sign_off_signature updated';
                } else {
                    $description = $history['description'];
                } ?>
				<tr>
					<td data-title="Date"><?= $history['date'] ?></td>
					<td data-title="User"><?= $name ?></td>
					<td data-title="Description"><?= html_entity_decode($description); ?></td>
				</tr>
			<?php } ?>
		</table>
		<a href="" class="pull-right btn brand-btn">Back</a>
	</div>
</div>
