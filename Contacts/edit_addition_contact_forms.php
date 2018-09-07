<?php include_once('../include.php');
$contactid = $_GET['edit'];
$form_id = $_GET['user_form_id'];
$user_form = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE `form_id` = '$form_id'")); ?>
<form class="form-horizontal">
	<h4><?= $user_form['name'] ?></h4>
	<div class="form-group pull-right">
		<a href="" onclick="addContactForm('<?= $form_id ?>'); return false;" class="btn brand-btn">Add Form</a>
	</div>
    <div class="clearfix"></div>
	<?php $form_pdfs = mysqli_query($dbc, "SELECT * FROM `user_form_pdf` WHERE `form_id` = '$form_id' AND `attached_contactid` = '$contactid' AND `deleted` = 0 ORDER BY `date_created`");
	if(mysqli_num_rows($form_pdfs) > 0) { ?>
		<div id="no-more-tables">
			<table class="table table-bordered">
				<tr class="hidden-xs">
					<th>Date</th>
					<th>Staff</th>
					<th>PDF</th>
					<th>Function</th>
				</tr>
				<?php while($form_pdf = mysqli_fetch_assoc($form_pdfs)) { ?>
					<tr>
						<td data-title="Date"><?= date('Y-m-d H:i a', strtotime($form_pdf['date_created'])) ?></td>
						<td data-title="Staff"><?= get_contact($dbc, $form_pdf['user_id']) ?></td>
						<td data-title="PDF" class="contact_form_pdf"><a href="<?= WEBSITE_URL ?>/Contacts/download/<?= $form_pdf['generated_file'] ?>" target="_blank">View PDF</a></td>
						<td data-title="Function"><a href="" onclick="editContactForm('<?= $form_pdf['pdf_id'] ?>'); return false;">Edit</a> | <a href="" onclick="removeContactForm(this, '<?= $form_pdf['pdf_id'] ?>'); return false;">Archive</a></td>
					</tr>
				<?php } ?>
			</table>
		</div>
	<?php } else {
		echo 'No Forms Found.';
	} ?>
</form>