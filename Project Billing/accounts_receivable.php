<div class="container">
	<div class="row">
		<div id='no-more-tables'>
		<?php
		if(empty($_GET['projectid'])) {
			$sql = "SELECT * FROM `project_invoice` LEFT JOIN `project` ON `project_invoice`.`projectid`=`project`.`projectid` WHERE `project_invoice`.`deleted`=0 AND `project_invoice`.`status`!='Not Sent'";
			$query = "SELECT COUNT(*) `numrows` FROM `project_invoice` LEFT JOIN `project` ON `project_invoice`.`projectid`=`project`.`projectid` WHERE `project_invoice`.`deleted`=0 AND `project_invoice`.`status`!='Not Sent'";
		} else {
			$sql = "SELECT * FROM `project_invoice` LEFT JOIN `project` ON `project_invoice`.`projectid`=`project`.`projectid` WHERE `projectid`='".$_GET['projectid']."' AND `project_invoice`.`deleted`=0 AND `project_invoice`.`status`!='Not Sent'";
			$query = "SELECT COUNT(*) `numrows` FROM `project_invoice` LEFT JOIN `project` ON `project_invoice`.`projectid`=`project`.`projectid` WHERE `projectid`='".$_GET['projectid']."' AND `project_invoice`.`deleted`=0 AND `project_invoice`.`status`!='Not Sent'";
		}
		$result = mysqli_query($dbc, $sql);

		$rowsPerPage = 25;
		$pageNum = 1;

		if(isset($_GET['page'])) {
			$pageNum = $_GET['page'];
		}

		$offset = ($pageNum - 1) * $rowsPerPage;

		$num_rows = mysqli_num_rows($result);
		if($num_rows > 0) {

			// Added Pagination //
			if($search_client == '') {
			echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
			}
			// Pagination Finish //

			echo '<table class="table table-bordered table-striped">';
			if(PROJECT_TILE == 'Projects') {
				$project_tile_title = 'Project';
			} else if(PROJECT_TILE == 'Jobs') {
				$project_tile_title = 'Job';
			} else {
				$project_tile_title = PROJECT_TILE;
			}
			echo '<thead><tr class="hidden-xs hidden-sm">
				<th>'.$project_tile_title.' #</th>
				<th>Business</th>
				<th>Contact</th>
				<th>Invoice #</th>
				<th>Invoice</th>
				<th>Function</th>
				<th>Status</th>
				</tr></thead>';

			// Get Project Types
			$project_tabs = get_config($dbc, 'project_tabs');
			$project_tabs = explode(',',$project_tabs);
			$project_vars = [];
			foreach($project_tabs as $item) {
				$project_vars[] = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
			}

			while($row = mysqli_fetch_array($result)) {
				echo '<tr class="hidden-xs hidden-sm">';

				foreach($project_vars as $key => $type_name) {
					if($row['projecttype'] == $type_name) {
						echo '<td data-title="'.(PROJECT_TILE == 'Projects' ? 'Project' : (PROJECT_TILE == 'Jobs' ? 'Job' : PROJECT_TILE)).' #"><a href="'.WEBSITE_URL.'/Project/review_project.php?type=project_path&projectid='.$row['projectid'].'&from='.$row['projecttype'].'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">#'.$row['projectid'].'<br>'.$project_tabs[$key].''.($row['status'] == 'Pending' ? ' (Pending)' : '').'</a></td>';
					}
				}
				$clientid = explode(',',$row['clientid']);
				$businessid = $row['businessid'];
				if($businessid ==  '' || $businessid ==  0) {
					$businessid = get_contact($dbc, $clientid, 'businessid');
				}
				$client_name = [];
				$client_emails = [];
				foreach($clientid as $client) {
					$client_name[] = get_contact($dbc, $client, "");
					$email = get_contact($dbc, $client, 'email_address');
					if($email != '') {
						$client_emails[] = $email;
					}
				}
				echo '<td data-title="Business">'.get_contact($dbc, $businessid, 'name').'</td>
					<td data-title="Contact">'.implode('<br />', $client_name).'</td>
					<td data-title="Invoice #">'.$row['invoiceid'].'</td>
					<td data-title="Invoice">'.($row['invoice'] != '' ? '<a href="'.$row['invoice'].'"><img src="'.WEBSITE_URL.'/img/pdf.png"> Invoice</a>' : '').'</td>
					<td data-title="Function">'.(count($client_emails) > 0 ? '<a href="Send Invoice" onclick="send_invoice(\''.implode(',',$client_emails).'\', \''.$row['invoice'].'\'); return false;">Send to Client ('.implode(',',$client_emails).')</a>' : '').'</td>
					<td data-title="Status">'.$row['status'].'</td>
					</tr>';
			} ?>
			</table>
			<div class="form-group">
				<label class="col-sm-4 control-label">Sending Email Address for Invoices:</label>
				<div class="col-sm-8">
					<input type="text" name="sender" class="form-control" value="<?php echo get_contact($dbc, $_SESSION['contactid'], 'email_address'); ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Subject for Invoices:</label>
				<div class="col-sm-8">
					<input type="text" name="subject" class="form-control" value="Invoice">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Body for Invoices:</label>
				<div class="col-sm-8">
					<textarea name="body" class="form-control">Attached is your invoice.</textarea>
				</div>
			</div>
		<?php } else {
			echo "<h2>No Invoices Found</h2>";
		} ?>
		</div>
	</div>
</div>