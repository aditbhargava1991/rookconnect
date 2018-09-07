<?php $guest_access = true;
include_once('include.php');
if($_POST['submit'] == 'send') {
	$ticketid = filter_var($_POST['ticketid'],FILTER_SANITIZE_STRING);
	$status = filter_var($_POST['status'],FILTER_SANITIZE_STRING);
	$details = filter_var($_POST['details'],FILTER_SANITIZE_STRING);
	$email_name = filter_var($_POST['email_name'],FILTER_SANITIZE_STRING);
	$email_address = filter_var($_POST['email_address'],FILTER_SANITIZE_STRING);
	$email_body = (empty($status) ? '' : 'Status: '.$status.'<br />').'<b>Details:<b>'.$details;
	$recipients = [];
	if($ticketid > 0) {
		$get_ticket = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
		foreach(explode(',',$get_ticket['contactid'].','.$get_ticket['internal_qa_contactid'].','.$get_ticket['deliverable_contactid']) as $contact) {
			if($contact > 0) {
				$contact = get_email($dbc, $contact);
				if(!empty($contact)) {
					$recipients[] = $contact;
				}
			}
		}
		$subject = 'Response on '.get_ticket_label($dbc, $get_ticket);
		$dbc->query("INSERT INTO `email_communication` (`communication_type`,`businessid`,`contactid`,`projectid`,`ticketid`,`to_staff`,`subject`,`email_body`,`today_date`,`from_email`,`from_name`) SELECT 'External',`businessid`,`clientid`,`projectid`,`ticketid`,'".implode(',',$recipients)."','$subject','".htmlentities($email_body)."',DATE(NOW()),'$email_name','$email_address' FROM `tickets` WHERE `ticketid`='$ticketid'");
	}
	$id = $dbc->insert_id;
	$attach = [];
	foreach($_FILES['upload']['name'] as $i => $filename) {
		if(!file_exists('Email Communication/download')) {
			mkdir('Email Communication/download',0777);
		}
		$filename = file_safe_str($filename, 'Email Communication/download/');
		move_uploaded_file($_FILES['upload']['tmp_name'][$i],'Email Communication/download/'.$filename);
		$dbc->query("INSERT INTO `email_communicationid_upload` (`email_communicationid`, `document`,`created_date`) VALUES ('$id','$filename',DATE(NOW()))");
		$attach[] = 'Email Communication/download/'.$filename;
	}
	send_email([$email_address => $email_name],$recipients,'','',$subject,$email_body,implode('#FFM#',$attach));
}
$details = json_decode(decryptIt($_GET['r'])); ?>
<script>
function addRow(img) {
	var clone = $(img).closest('.form-group').clone();
	clone.find('input').val('');
	$(img).closest('.form-group').after(clone);
	initTooltips();
}
</script>
<div class="container" style="min-height: calc(100vh - 48px)">
	<form class="form-horizontal" action="" method="POST" enctype="multipart/form-data">
		<?php if($details->ticketid > 0) {
			$ticketid = filter_var($details->ticketid,FILTER_SANITIZE_STRING);
			$communication_type = 'External';
			$communication_method = 'email';
			$include_folder = 'Ticket/';
			$hide_recipient = true;
			if(!isset($get_ticket)) {
				$get_ticket = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
			}
			$ticket_options = $dbc->query("SELECT GROUP_CONCAT(`fields` SEPARATOR ',') `field_config` FROM (SELECT `value` `fields` FROM `general_configuration` LEFT JOIN `tickets` ON `general_configuration`.`name` LIKE CONCAT('ticket_fields_',`tickets`.`ticket_type`) WHERE `ticketid`='$ticketid' UNION SELECT `tickets` `fields` FROM `field_config`) `fields`")->fetch_assoc();
			if($get_ticket['status'] != 'Archive' && $get_ticket['deleted'] == 0) { ?>
				<h1>Add Response - <?= get_ticket_label($dbc, $get_ticket) ?></h1>
				<?php if(strpos(','.$ticket_options['field_config'].',',',External Response Thread,') !== FALSE) { ?>
					<h3>Communication Thread</h3>
					<?php include('Ticket/add_ticket_view_communication.php'); ?>
				<?php } ?>
				<input type="hidden" name="ticketid" value="<?= $ticketid ?>">
				<h3>Details</h3>
				<?php if(strpos(','.$ticket_options['field_config'].',',',External Response Status,') !== FALSE) { ?>
					<div class="form-group">
						<label class="col-sm-4">Status:</label>
						<div class="col-sm-8">
							<select class="chosen-select-deselect" data-placeholder="Select Status" name="status"><option />
								<option value="Ready to Push Live">Ready to Push Live</option>
								<option value="QA Dev Needed - Bugs and Minor Changes">QA Dev Needed - Bugs and Minor Changes</option>
								<option value="This is not what I wanted">This is not what I wanted</option>
								<option value="I want additional changes not in the original details">I want additional changes not in the original details</option>
							</select>
						</div>
					</div>
				<?php } ?>
				<div class="form-group">
					<label class="col-sm-4">Details:</label>
					<div class="col-sm-8">
						<textarea name="details"></textarea>
					</div>
				</div>
				<?php if(strpos(','.$ticket_options['field_config'].',',',External Response Status,') !== FALSE) { ?>
					<div class="form-group">
						<label class="col-sm-4">Documents / Images:</label>
						<div class="col-sm-8">
							<input type="file" name="upload[]" class="inline">
							<img class="inline-img cursor-hand no-toggle" src="../img/icons/ROOK-add-icon.png" onclick="addRow(this);" title="Add Another File">
						</div>
					</div>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		<div class="form-group">
			<label class="col-sm-4">Your Name:</label>
			<div class="col-sm-8">
				<input type="text" name="email_name" class="form-control">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4">Your Email:</label>
			<div class="col-sm-8">
				<input type="email" name="email_address" class="form-control">
			</div>
		</div>
		<button class="btn brand-btn pull-right" type="submit" name="submit" value="send">Send Details</button>
		<button class="btn brand-btn pull-left" type="reset" name="reset" value="reset" onclick="$('select').val('').trigger('change.select2');">Cancel</button>
	</form>
</div>
<?php include_once('footer.php');