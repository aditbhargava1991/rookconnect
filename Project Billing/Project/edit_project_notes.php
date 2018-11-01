<?php include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($project)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
} ?>
<div id="head_notes" class="form-horizontal col-sm-12" data-tab-name="details">
	<h3><?= PROJECT_NOUN ?> Notes</h3>
	<?php $project_notes = mysqli_query($dbc, "SELECT * FROM `project_comment` WHERE `projectid`='$projectid' AND '$projectid' > 0 AND `type` NOT LIKE 'detail_%'");
	if(mysqli_num_rows($project_notes) > 0) {
        $odd_even = 0; ?>
		<table class='table table-bordered'>
            <tr class='hidden-xs hidden-sm'>
				<th>Note</th>
				<th>Assigned To</th>
				<th>Date</th>
				<th>Added By</th>
            </tr>
			<?php while($note = mysqli_fetch_assoc($project_notes)) { ?>
                <?php $bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
				<tr class="<?= $bg_class ?>">
					<td data-title="Note"><?= html_entity_decode($note['comment']) ?></td>
					<td data-title="Assigned To"><?= get_contact($dbc, $note['email_comment']) ?></td>
					<td data-title="Date"><?= $note['created_date'] ?></td>
					<td data-title="Added By"><?= get_contact($dbc, $note['created_by']) ?></td>
				</tr>
                <?php $odd_even++; ?>
			<?php } ?>
		</table>
	<?php } ?>

	<?php if($security['edit'] > 0) { ?>
	  <div class="form-group">
		<label for="site_name" class="col-sm-4 control-label">Add Note:</label>
		<div class="col-sm-8">
			<input type="hidden" name="ticket_comment_type" value="project_note">
		  <textarea name="comment" data-table="project_comment" data-id="" data-id-field="projectcommid" data-project="<?= $projectid ?>" data-type="project_note" rows="4" cols="50" class="form-control" ></textarea>
		</div>
	  </div>

		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Send Email:</label>
		  <div class="col-sm-8">
			<input type="hidden" name="send_email_on_comment" value="">
			<input type="checkbox" value="Yes" name="check_send_email" onclick="show_email_options(this);">
		  </div>
		</div>

		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Assign/Email To:</label>
		  <div class="col-sm-8">
			<select data-placeholder="Select <?= $comment_category ?>..." name="email_comment" data-table="project_comment" data-id="" data-id-field="projectcommid" data-project="<?= $projectid ?>" data-type="project_note" class="chosen-select-deselect form-control email_recipient" width="380">
			  <option value=""></option>
			  <?php
				$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
				foreach($query as $id) {
					$selected = '';
					echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
				}
			  ?>
			</select>
		  </div>
		</div>

		<?php $subject = 'Note added on '.PROJECT_NOUN.' for you to Review';
		$body = 'The following note has been added on a '.PROJECT_NOUN.' for you:<br>[REFERENCE]<br><br>
				Client: [CLIENT]<br>
				'.PROJECT_NOUN.' Name: [HEADING]<br>
				Status: [STATUS]<br>
				Please click the '.PROJECT_NOUN.' link below to view all information.<br>
				<a target="_blank" href="'.WEBSITE_URL.'/Project/projects.php?edit=[PROJECTID]">'.PROJECT_NOUN.' #[PROJECTID]</a><br>';
		?>
		<script>
		function show_email_options(checked) {
			if(checked.checked) {
				$(checked).closest('.col-sm-8').find('[name="send_email_on_comment"]').val('Yes');
				$(checked).closest('[data-tab-name]').find('.project_email_options').show();
			} else {
				$(checked).closest('.col-sm-8').find('[name="send_email_on_comment"]').val('');
				$(checked).closest('[data-tab-name]').find('.project_email_options').hide();
			}
		}
		function send_email(button) {
			$.ajax({
				url: 'projects_ajax.php?action=send_email',
				method: 'POST',
				data: {
					table: $(button).data('table'),
					id_field: $(button).data('id-field'),
					id: $(button).data('id'),
					field: $(button).data('field'),
					recipient: $(button).closest('.email-block').find('.email_recipient').val(),
					sender: $(button).closest('.email_div').find('.email_sender').val(),
					sender_name: $(button).closest('.email_div').find('.email_sender_name').val(),
					subject: $(button).closest('.email_div').find('.email_subject').val(),
					body: $(button).closest('.email_div').find('.email_body').val()
				},
				success: function(response) {
					if(response != '') {
						alert(response);
					}
				}
			});
			$(button).closest('.email_div').hide().closest('[data-tab-name]').find('[name=check_send_email]').removeAttr('checked');
		}
		</script>
		<div class="project_email_options email_div" style="display:none;">
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Sender's Name:</label>
				<div class="col-sm-8">
					<input type="text" name="email_sender_name" class="form-control email_sender_name" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Sender's Address:</label>
				<div class="col-sm-8">
					<input type="text" name="email_sender" class="form-control email_sender" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Subject:</label>
				<div class="col-sm-8">
					<input type="text" name="email_subject" class="form-control email_subject" value="<?php echo $subject; ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Body:</label>
				<div class="col-sm-8">
					<textarea name="email_body" class="form-control email_body"><?php echo $body; ?></textarea>
				</div>
			</div>
			<button class="btn brand-btn pull-right" data-table="project_comment" data-id-field="projectcommid" data-id="" data-field="comment" onclick="send_email(this); return false;">Send Email</button>
		</div>
		<a href="" class="btn brand-btn pull-right" onclick="return waitForSave(this);">Save Note</a>
		<div class="clearfix"></div>
	<?php } ?>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_project_notes.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>
