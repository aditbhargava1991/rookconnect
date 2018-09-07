<!-- Notes -->
<div class="accordion-block-details padded" id="notes">
    <div class="accordion-block-details-heading"><h4>Notes</h4></div>
    <div class="row">
		<?php $notes = mysqli_query($dbc, "SELECT * FROM `sales_order_notes` WHERE `sales_order_id`='$sotid' AND `deleted`=0");
		if(mysqli_num_rows($notes) > 0) {
            $odd_even = 0; ?>
			<div id="no-more-tables" class="col-sm-12">
				<table class="table table-bordered table-striped">
                    <thead>
                        <tr class="hidden-sm hidden-xs">
                            <th>Note</th>
                            <th>Assigned To</th>
                            <th>Created</th>
                        </tr>
                    </thead>
					<?php while($note = mysqli_fetch_assoc($notes)) { ?>
                        <?php $bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
						<tr class="<?= $bg_class ?>">
							<td data-title="Note"><?= html_entity_decode($note['note']) ?></td>
							<td data-title="Assigned To"><?= $note['email_comment'] > 0 ? get_contact($dbc, $note['email_comment']) : '' ?></td>
							<td data-title="Created"><?= get_contact($dbc, $note['created_by']).($note['created_by'] > 0 ? '<br />' : '') ?><?= $note['created_date'] ?></td>
						</tr>
                        <?php $odd_even++; ?>
					<?php } ?>
				</table>
			</div>
			<div class="clearfix"></div>
		<?php } ?>
		<div class="form-group">
			<label class="col-sm-4">Note:<br /><em>Send this comment <input type="checkbox" name="send_note_email" value="send" onchange="show_email_fields();"></em></label>
			<div class="col-sm-8">
				<textarea name="note_text"></textarea>
			</div>
		</div>
		<div class="note-email" style="display:none;">
			<div class="form-group">
				<label class="col-sm-4">Send Email To:</label>
				<div class="col-sm-8">
					<select class="chosen-select-deselect" data-placeholder="Select a recipient" name="note_email_to"><option></option>
						<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `last_name`, `first_name`, `category`, `contactid` FROM `contacts` WHERE (`category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." OR (`businessid`='$businessid' AND `businessid` > 0)) AND `status`>0 AND `deleted`=0")) as $recipient) { ?>
							<option value="<?= $recipient['contactid'] ?>"><?= $recipient['first_name'].' '.$recipient['last_name'] ?></option>
						<?php } ?>
					</select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4">Sending Email Name:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>" name="note_email_name">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4">Sending Email Address:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" value="<?= get_contact($dbc, $_SESSION['contactid'], 'email_address') ?>" name="note_email_address">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4">Email Subject:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" value="Note regarding <?= $sales_order_name != '' ? $sales_order_name : SALES_ORDER_NOUN ?>" name="note_email_subject">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4">Email Body:</label>
				<div class="col-sm-8">
					<textarea name="note_email_body">
						<p>A note has been added for you on a <?= SALES_ORDER_NOUN ?>:<br />[REFERENCE]</p>
						<p>Please <a href="<?= WEBSITE_URL ?>/Sales Order/order_details.php?sotid=<?= $sotid ?>">click here</a> to review the <?= SALES_ORDER_NOUN ?>.</p>
					</textarea>
				</div>
			</div>
		</div>
		<script>
		function show_email_fields() {
			if($('[name=send_note_email]').is(':checked')) {
				$('.note-email').show();
			} else {
				$('.note-email').hide();
			}
		}
		</script>
    </div>
</div>