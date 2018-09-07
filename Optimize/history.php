<div class="main-screen standard-body override-main-screen form-horizontal">
	<div class="standard-body-title">
		<h3>Optimization History</h3>
	</div>
	<div class="standard-body-content pad-top">
		<div class="col-sm-12" id="no-more-tables">
			<?php $rowsPerPage = $_GET['pagerows'] > 0 ? $_GET['pagerows'] : 25;
			$offset = ($_GET['page'] > 0 ? $_GET['page'] - 1 : 0) * $rowsPerPage;
			$history_query = $dbc->query("SELECT * FROM `ticket_history` WHERE `src`='optimizer' AND `deleted`=0 ORDER BY `date` DESC LIMIT $offset,$rowsPerPage");
			if($history_query->num_rows > 0) {
				$history_count = "SELECT COUNT(*) `numrows` FROM `ticket_history` WHERE `src`='optimizer' AND `deleted`=0";
				display_pagination($dbc, $history_count, $_GET['page'], ($_GET['pagerows'] > 0 ? $_GET['pagerows'] : $rowsPerPage), true, 25); ?>
				<table class="table table-bordered">
					<tr class="hidden-sm hidden-xs">
						<th><?= TICKET_NOUN ?></th>
						<th>User</th>
						<th>Date / Time</th>
						<th>Details</th>
					</tr>
					<?php while($row = $history_query->fetch_assoc()) {
						$ticket = [];
						if($row['ticketid'] > 0) {
							$ticket = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='{$row['ticketid']}'")->fetch_assoc();
						} ?>
						<tr>
							<td data-title="<?= TICKET_NOUN ?>"><?= $row['ticketid'] > 0 ? '<a href="../Ticket/index.php?edit='.$row['ticketid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\',\'auto\',true,true); return false;">'.get_ticket_label($dbc, $ticket).'</a>' : 'N/A' ?></td>
							<td data-title="User"><?= get_contact($dbc, $row['userid']) ?></td>
							<td data-title="Date / Time"><?= $row['date'] ?></td>
							<td data-title="Details"><?= html_entity_decode($row['description']) ?></td>
						</tr>
					<?php } ?>
				</table>
				<?php display_pagination($dbc, $history_count, $_GET['page'], ($_GET['pagerows'] > 0 ? $_GET['pagerows'] : $rowsPerPage), true, 25);
			} else {
				echo '<h3>No History Found for the Optimizer Tile</h3>';
			} ?>
		</div>
	</div>
</div>