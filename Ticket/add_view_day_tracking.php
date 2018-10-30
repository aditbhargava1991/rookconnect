<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Day Tracking</h3>') : '' ?>
<?php foreach($field_sort_order as $field_sort_field) {
	if($field_sort_field == 'FFMCUSTOM Day Tracking' || !$custom_accordion) { ?>
		<div class="form-group">
			<label for="first_name" class="col-sm-4 control-label">Total Days:<br><em>(e.g. - 5/9/40/120)</em></label>
			<div class="col-sm-8">
				<input name="total_days" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?php echo $total_days; ?>" class="form-control">
			</div>
		</div>

		<div class="form-group">
			<label for="first_name" class="col-sm-4 control-label">Remaining Days:</label>
			<div class="col-sm-8">
				<?php
				$total_done = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(ticketcommid) AS total_d FROM ticket_comment WHERE ticketid='$ticketid' AND type='day'"));
				$remain_days = ($total_days-$total_done['total_d']);
				echo $remain_days.' Days';
				?>
			</div>
		</div>

		<?php
		if(!empty($_GET['ticketid']) || !empty($_GET['edit'])) {
			$query_check_credentials = "SELECT * FROM ticket_comment WHERE ticketid='$ticketid' AND type='day' ORDER BY ticketcommid DESC";
			$result = mysqli_query($dbc, $query_check_credentials);
			$num_rows = mysqli_num_rows($result);
			if($num_rows > 0) {
				echo "<table class='table table-bordered'>
				<tr class='hidden-xs hidden-sm'>
				<th>Note</th>
				<th>Assign To</th>
				<th>Date</th>
				<th>Added By</th>
				</tr>";
				while($row = mysqli_fetch_array($result)) {
					echo '<tr>';
					$by = $row['created_by'];
					$to = $row['email_comment'];
					echo '<td data-title="Schedule">'.html_entity_decode($row['comment']).'</td>';
					echo '<td data-title="Schedule">'.get_staff($dbc, $to).'</td>';
					echo '<td data-title="Schedule">'.$row['created_date'].'</td>';
					echo '<td data-title="Schedule">'.get_staff($dbc, $by).'</td>';
					echo '</tr>';
				}
				echo '</table>';
			}
		}
		?>

		<div class="col-md-12 multi-block">
		      <div class="form-group">
		        <label for="site_name" class="col-sm-4 control-label">Day End Note:</label>
		        <div class="col-sm-12">
		          <textarea name="comment" data-table="ticket_comment" data-id="" data-id-field="ticketcommid" data-type="day" rows="4" cols="50" class="form-control" ></textarea>
		        </div>
		      </div>

		        <div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label">Send Email:</label>
				  <div class="col-sm-8">
					<input type="checkbox" value="Yes" name="send_email_on_comment">
				  </div>
				</div>

		        <div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label">Assign/Email To:</label>
				  <div class="col-sm-8">
					<select data-placeholder="Choose a Staff Member..." name="email_comment" data-table="ticket_comment" data-id="" data-id-field="ticketcommid" data-type="day" class="chosen-select-deselect form-control email_recipient" width="380">
					  <option value=""></option>
					  <?php foreach($staff_list as $staff) {
							echo "<option value='". $staff['contactid']."'>".$staff['first_name'].' '.$staff['last_name'].'</option>';
						}
					  ?>
					</select>
				  </div>
				</div><?php
			$sender = get_email($dbc, $_SESSION['contactid']);
			$subject = 'Note added on '.TICKET_NOUN.' for you to Review';
			$body = 'The following note has been added on a '.TICKET_NOUN.' for you:<br>[REFERENCE]<br><br>
					Client: [CLIENT]<br>
					'.TICKET_NOUN.' Heading: [HEADING]<br>
					Status: [STATUS]<br>
					Please click the '.TICKET_NOUN.' link below to view all information.<br>
					<a target="_blank" href="'.WEBSITE_URL.'/Ticket/index.php?edit=[TICKETID]'.(empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']).'">'.TICKET_NOUN.' #[TICKETID]</a><br>';
			?>
			<script>
			function ticket_comment_check_send_email(checked) {
				if(checked.checked) {
					$(checked).closest('.col-sm-8').find('[name="send_email_on_comment[]"]').val('Yes');
					$(checked).closest('.multi-block').find('.ticket_comment_email_send_div').show();
				} else {
					$(checked).closest('.col-sm-8').find('[name="send_email_on_comment[]"]').val('');
					$(checked).closest('.multi-block').find('.ticket_comment_email_send_div').hide();
				}
			}
			</script>
			<div class="ticket_comment_email_send_div email_div" style="display:none;">
				<div class="form-group">
					<label class="col-sm-4 control-label">Sending Email Address:</label>
					<div class="col-sm-8">
						<input type="text" name="ticket_comment_email_sender[]" class="form-control email_sender" value="<?php echo $sender; ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Email Subject:</label>
					<div class="col-sm-8">
						<input type="text" name="ticket_comment_email_subject[]" class="form-control email_subject" value="<?php echo $subject; ?>">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Email Body:</label>
					<div class="col-sm-12">
						<textarea name="ticket_comment_email_body[]" class="form-control email_body"><?php echo $body; ?></textarea>
					</div>
				</div>
				<button class="btn brand-btn pull-right" onclick="send_comment_email" data-table="ticket_comment" data-id-field="ticketcommid" data-id="" data-field="comment" data-history-label="Send Day Note Email" onclick="send_email(this); return false;">Send Email</button>
			</div>
		</div>
	<?php }
	if(!$custom_accordion) {
		break;
	}
} ?>
