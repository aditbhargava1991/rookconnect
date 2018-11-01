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
<script>
function get_notes() {
    $.ajax({
        url: 'edit_project_notes.php?projectid=<?= $projectid ?>&start_date='+$('[name=notes_start_date]').val()+'&end_date='+$('[name=notes_end_date]').val(),
        success: function(response) {
            $('.notes_table').html($(response).find('.notes_table').html());
        }
    });
}
function addNewNote() {
    $('.add_note').show();
}
</script>
<div id="head_notes" class="form-horizontal col-sm-12 new_group" data-tab-name="details">
	<h3><?= PROJECT_NOUN ?> Notes</h3>
    <div id="no-more-tables" class="notes_table">
        <div class="form-group">
            <label class="col-md-2 col-sm-4">Start Date:</label>
            <div class="col-md-3 col-sm-6"><input type="text" class="form-control datepicker" name="notes_start_date" value="<?= $_GET['start_date'] ?>"></div>
            <label class="col-md-2 col-sm-4">End Date:</label>
            <div class="col-md-3 col-sm-6"><input type="text" class="form-control datepicker" name="notes_end_date" value="<?= $_GET['end_date'] ?>"></div>
            <div class="col-sm-2">
                <a href="project_notes_pdf.php?projectid=<?= $projectid ?>&start_date=<?= $_GET['start_date'] ?>&end_date=<?= $_GET['end_date'] ?>" target="_blank" class="pull-right"><img class="inline-img" src="../img/pdf.png"></a>
                <a href="" onclick="get_notes(); return false;" class="btn brand-btn pull-right">Search</a>
            </div>
        </div>
        <?php $note_start_date = (empty($_GET['start_date']) ? '0000-00-00' : date('Y-m-d',strtotime($_GET['start_date'])));
        $note_end_date = (empty($_GET['end_date']) ? '9999-12-31' : date('Y-m-d',strtotime($_GET['end_date'])));
        $project_notes = mysqli_query($dbc, "SELECT * FROM `project_comment` WHERE `projectid`='$projectid' AND '$projectid' > 0 AND `type` NOT LIKE 'detail_%' AND `created_date` BETWEEN '".$note_start_date."' AND '".$note_end_date."'");
        if(mysqli_num_rows($project_notes) > 0) {
            $odd_even = 0; ?>
            <table class='table table-bordered'>
                <tr class='hidden-xs hidden-sm'>
                    <th>Date Created</th>
                    <th>Tagged</th>
                    <th>Sent To</th>
                    <th>Created By</th>
                    <th>Note</th>
                    <th style="width:3em;"></th>
                </tr>
                <?php while($note = mysqli_fetch_assoc($project_notes)) { ?>
                    <?php $bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
                    <tr class="<?= $bg_class ?>">
                        <td data-title="Date Created"><?= $note['created_date'] ?></td>
                        <td data-title="Tagged"><?= get_contact($dbc, $note['email_comment']) ?></td>
                        <td data-title="Sent To"><?= get_contact($dbc, $note['email_comment']) ?></td>
                        <td data-title="Created By"><?= get_contact($dbc, $note['created_by']) ?></td>
                        <td data-title="Note"><?= html_entity_decode($note['comment']) ?></td>
                        <td data-tile="Function"><img class="cursor-hand pull-right inline-img no-toggle" src="../img/icons/ROOK-add-icon.png" title="Add Note" onclick="addNewNote();"></td>
                    </tr>
                    <?php $odd_even++; ?>
                <?php } ?>
            </table>
        <?php } else {
            echo '<button class="btn brand-btn" onclick="addNewNote(); return false;">Add Note</button>';
        } ?>
    </div>

	<?php if($security['edit'] > 0) { ?>
        <div class="add_note" style="display:none;">
            <div class="col-sm-8 pull-right text-md">
                <img class="inline-img cursor-hand no-toggle" src="../img/icons/ROOK-share-icon.png" onclick="$('.tag_group').toggle();" title="Share with Users">
                <img class="inline-img cursor-hand no-toggle" src="../img/icons/ROOK-email-icon.png" onclick="show_email_options(this);" title="Send Email">
                <img class="inline-img cursor-hand no-toggle" src="../img/icons/ROOK-reminder-icon.png" onclick="addReminder();" title="Schedule Reminder">
            </div>
            <div class="clearfix"></div>

            <div class="form-group tag_group" style="display:none;">
              <label for="site_name" class="col-sm-4 control-label">Tag:</label>
              <div class="col-sm-7">
                <select data-placeholder="Select <?= $comment_category ?>..." name="email_comment" data-concat="," data-table="project_comment" data-id="" data-id-field="projectcommid" data-project="<?= $projectid ?>" data-type="project_note" class="chosen-select-deselect form-control email_recipient" width="380">
                  <option value=""></option>
                  <?php
                    $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
                    foreach($query as $id) {
                        echo "<option value='". $id."'>".get_contact($dbc, $id).'</option>';
                    }
                  ?>
                </select>
              </div>
              <div class="col-sm-1">
                <img class="inline-img pull-right no-toggle cursor-hand" src="../img/remove.png" title="Remove this <?= CONTACTS_NOUN ?> from the note" onclick="removeTag(this);">
                <img class="inline-img pull-right no-toggle cursor-hand current" src="../img/icons/ROOK-add-icon.png" title="Select an additional <?= CONTACTS_NOUN ?> on the note" onclick="addTag();">
              </div>
              <script>
                function addTag() {
                    var last = $('.tag_group').last();
                    var clone = last.clone();
                    clone.find('select').val('');
                    resetChosen(clone.find('.chosen-select-deselect'));
                    last.after(clone);
                    $('[data-table]').change(saveField);
                }
                function removeTag(img) {
                    if($('.tag_group').length <= 1) {
                        addTag();
                    }
                    $(img).closest('.tag_group').remove();
                    $('[name="email_comment[]"]').last().change();
                }
              </script>
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
            function show_email_options(img) {
                $(img).closest('[data-tab-name]').find('.project_email_options').toggle();
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
                  <label for="site_name" class="col-sm-4 control-label">To:</label>
                  <div class="col-sm-8">
                    <select data-placeholder="Select <?= $comment_category ?>..." name="email_comment[]" data-table="project_comment" data-id="" data-id-field="projectcommid" data-project="<?= $projectid ?>" data-type="project_note" class="chosen-select-deselect form-control email_recipient" width="380">
                      <option value=""></option>
                      <?php
                        $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
                        foreach($query as $id) {
                            echo "<option value='". $id."'>".get_contact($dbc, $id).'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="site_name" class="col-sm-4 control-label">CC:</label>
                  <div class="col-sm-8">
                    <select data-placeholder="Select <?= $comment_category ?>..." name="email_comment[]" data-table="project_comment" data-id="" data-id-field="projectcommid" data-project="<?= $projectid ?>" data-type="project_note" class="chosen-select-deselect form-control email_recipient" width="380">
                      <option value=""></option>
                      <?php
                        $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
                        foreach($query as $id) {
                            echo "<option value='". $id."'>".get_contact($dbc, $id).'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
                <div class="form-group">
                  <label for="site_name" class="col-sm-4 control-label">BCC:</label>
                  <div class="col-sm-8">
                    <select data-placeholder="Select <?= $comment_category ?>..." name="email_comment[]" data-table="project_comment" data-id="" data-id-field="projectcommid" data-project="<?= $projectid ?>" data-type="project_note" class="chosen-select-deselect form-control email_recipient" width="380">
                      <option value=""></option>
                      <?php
                        $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 AND `status` > 0"),MYSQLI_ASSOC));
                        foreach($query as $id) {
                            echo "<option value='". $id."'>".get_contact($dbc, $id).'</option>';
                        }
                      ?>
                    </select>
                  </div>
                </div>
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
                <div class="clearfix"></div>
            </div>

            <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">Add Note:</label>
                <div class="col-sm-8">
                    <input type="hidden" name="ticket_comment_type" value="project_note">
                  <textarea name="comment" data-table="project_comment" data-id="" data-id-field="projectcommid" data-project="<?= $projectid ?>" data-type="project_note" rows="4" cols="50" class="form-control" ></textarea>
                </div>
            </div>
            <a href="" class="btn brand-btn pull-right gap-bottom" onclick="if(waitForSave(this)) { $(this).text('Save Note'); get_notes(); $('.add_note').hide().find('[data-id]').data('id',0); tinyMCE.get('comment').setContent(''); } return false;">Save Note</a>
            <div class="clearfix"></div>
        </div>
	<?php } ?>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_project_notes.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>
