<?php if(isset($_POST['submit'])) {
	if($_POST['contact_note'] == 1) {
		$clientid = $_POST['contact_clientid'];
		$comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);
		mysqli_query($dbc, "INSERT INTO `contacts_description` (`contactid`) SELECT '$clientid' FROM (SELECT COUNT(*) `rows` FROM `contacts_description` WHERE `contactid` = '$clientid') num WHERE num.rows=0");
		mysqli_query($dbc, "UPDATE `contacts_description` SET `notes` = CONCAT(IFNULL(`notes`,''),'$comment') WHERE `contactid` = '$clientid'"); ?>
		<script>
			$(window.top.document).find('iframe[src*=Ticket]').get(0).contentWindow.reload_contact_notes();
			window.parent.reload_contact_notes();
			window.parent.reload_site();
			$(window.top.document).find('iframe[src*=Ticket]').get(0).contentWindow.reload_sidebar();
			window.parent.reload_sidebar();
		</script>
	<?php } else {
		$ticketid = filter_var($_POST['ticketid'],FILTER_SANITIZE_STRING);
		$type = filter_var($_POST['comment_type'],FILTER_SANITIZE_STRING);
		$comment = filter_var(htmlentities($_POST['comment']),FILTER_SANITIZE_STRING);
		$assign_to = filter_var(implode(',',$_POST['assign']),FILTER_SANITIZE_STRING);
		$reference = filter_var($_POST['reference'],FILTER_SANITIZE_STRING);
		$user = $_SESSION['contactid'];
		if($type == 'member_note') {
			$dbc->query("INSERT INTO `client_daily_log_notes` (`client_id`,`ticketid`,`note_date`,`created_by`,`note`) VALUES ('$reference','$ticketid',NOW(),'$user','$comment')");
		} else {
			$dbc->query("INSERT INTO `ticket_comment` (`ticketid`,`type`,`comment`,`email_comment`,`reference_contact`,`created_date`,`created_by`) VALUES ('$ticketid','$type','$comment','$assign_to','$reference',DATE(NOW()),'$user')");
		}
		echo "<script> $(window.top.document).find('iframe[src*=Ticket]').get(0).contentWindow.reload_needed_notes(); window.parent.reload_needed_notes(); </script>";

		if($_POST['submit'] == 'email') {
			$sender = filter_var($_POST['sender_address'],FILTER_SANITIZE_STRING);
			$sender_name = filter_var($_POST['sender_name'],FILTER_SANITIZE_STRING);
			$subject = $_POST['subject'];
			$ticket = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));
			$body = str_replace(['[REFERENCE]','[TICKETID]','[CLIENT]','[HEADING]','[STATUS]'], [html_entity_decode($comment),$ticketid,get_client($dbc,$ticket['businessid']),$ticket['label'],$ticket['status']],$_POST['body']);
			foreach(array_filter($_POST['assign']) as $address) {
				$address = get_email($dbc, filter_var($address,FILTER_SANITIZE_STRING));
				try {
					send_email([$sender=>$sender_name], $address, '', '', $subject, $body, '');
				} catch(Exception $e) { echo "Unable to send e-mail: ".$e->getMessage(); }
			}
			$role_address = [];
			foreach(array_filter($_POST['assign_role']) as $role) {
				$role_contacts = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE CONCAT(',',`role`,',') LIKE '%,$role,%' AND `deleted` = 1 AND `status` > 0");
				while($row = mysqli_fetch_assoc($role_contacts)) {
					if(!empty(get_email($dbc, $row['contactid']))) {
						$role_address[$row['contactid']] = get_email($dbc, $row['contactid']);
					}
				}
			}
			foreach(array_filter($role_address) as $address) {
				try {
					send_email([$sender=>$sender_name], $address, '', '', $subject, $body, '');
				} catch(Exception $e) { echo "Unable to send e-mail: ".$e->getMessage(); }
			}
		}
	}
}

if($access_any > 0 || $_GET['force_allow'] == 1) {
	$comment_type = filter_var($_GET['comment'],FILTER_SANITIZE_STRING); ?>
	<form class="col-sm-12 form-horizontal" action="" method="POST" enctype="multipart/form-data">
		<h2>Add Note</h2><a class="pull-right" href="../blank_loading_page.php"><img class="slider-close" src="../img/icons/cancel.png"></a>
		<input type="hidden" name="comment_type" value="<?= $comment_type ?>">
		<input type="hidden" name="ticketid" value="<?= $ticketid ?>">
		<?php if(in_array($comment_type,['member_note']) && $_GET['contact_note'] != 1) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label">References:</label>
			  <?php $comment_category = ($comment_type == 'member_note' ? "NOT IN ('Business','Staff','Sites')" : "='Staff'"); ?>
			  <?php $category = ($comment_type == 'member_note' ? $category : "Staff"); ?>
			  <div class="col-sm-8">
				<select data-placeholder="Select <?= $category ?>..." name="reference" class="chosen-select-deselect form-control">
				  <option value=""></option>
					<?php $query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category $comment_category AND CONCAT(IFNULL(`first_name`,''),IFNULL(`last_name`,'')) != '' AND deleted=0 AND `status`>0"));
					foreach($query as $contact) {
						echo "<option value='". $contact['contactid']."'>".$contact['first_name'].' '.$contact['last_name'].'</option>';
					} ?>
				</select>
			  </div>
			</div>
		<?php } ?>

		<?php if($_GET['contact_note'] == 1) { ?>
			<input type="hidden" name="contact_note" value="1">
			<input type="hidden" name="contact_clientid" value="<?= $_GET['clientid'] ?>">
			<div class="form-group">
				<label for="site_name" class="col-sm-4 control-label"><?= get_contact($dbc, $_GET['clientid'], 'category') == 'Sites' ? 'Site' : 'Contact' ?>:</label>
				<div class="col-sm-8">
					<?= !empty(get_client($dbc, $_GET['clientid'])) ? get_client($dbc, $_GET['clientid']) : get_contact($dbc, $_GET['clientid']) ?>
				</div>
			</div>
		<?php } ?>

	  <div class="form-group">
		<label for="site_name" class="col-sm-4 control-label">Note:</label>
		<div class="col-sm-12">
		  <textarea name="comment" rows="4" cols="50" class="form-control"></textarea>
		</div>
	  </div>

	  <?php if((strpos($value_config,',Send Emails,') !== FALSE || (strpos($value_config, ',Notes Alert,') !== FALSE && $comment_type == 'note')) && $_GET['contact_note'] != 1) { ?>
		<div class="form-group" style="<?= in_array($comment_type, ['member_note']) ? "display:none;" : "" ?>">
		  <label for="site_name" class="col-sm-4 control-label">Send Email:</label>
		  <div class="col-sm-8">
			<input type="hidden" name="send_email_on_comment" value="">
			<input type="checkbox" value="Yes" name="check_send_email" onclick="ticket_comment_check_send_email(this);">
		  </div>
		</div>
	  <?php } ?>

		<?php if(!in_array($comment_type,['member_note']) && strpos($value_config,',Tag Notes,') !== FALSE && $_GET['contact_note'] != 1) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label">Assign<?= (strpos($value_config,',Send Emails,') !== FALSE ? "/Email" : "") ?> To:</label>
			  <?php $comment_category = ($comment_type == 'member_note' ? "NOT IN ('Business',".STAFF_CATS.",'Sites')" : " IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""); ?>
			  <?php $category = ($comment_type == 'member_note' ? $category : "Staff"); ?>
			  <div class="col-sm-8">
				<select data-placeholder="Select <?= $category ?>..." name="assign[]" multiple class="chosen-select-deselect form-control">
					<?php $query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category $comment_category AND CONCAT(IFNULL(`first_name`,''),IFNULL(`last_name`,'')) != '' AND deleted=0 AND `status`>0"));
					foreach($query as $contact) {
						echo "<option value='". $contact['contactid']."'>".$contact['first_name'].' '.$contact['last_name'].'</option>';
					} ?>
				</select>
			  </div>
			</div>
		<?php } ?>

		<?php $subject = 'Note added on '.TICKET_NOUN.' for you to Review';
		$body = 'The following note has been added on a '.TICKET_NOUN.' for you:<br>[REFERENCE]<br><br>
				Client: [CLIENT]<br>
				'.TICKET_NOUN.' Heading: [HEADING]<br>
				Status: [STATUS]<br>
				Please click the '.TICKET_NOUN.' link below to view all information.<br>
				<a target="_blank" href="'.WEBSITE_URL.'/Ticket/index.php?edit=[TICKETID]'.(empty($_GET['tile_name']) ? '' : '&tile_name='.$_GET['tile_name']).(empty($_GET['tile_group']) ? '' : '&tile_group='.$_GET['tile_group']).'">'.TICKET_NOUN.' #[TICKETID]</a><br>'; ?>
		<script>
		<?php if(strpos($value_config, ',Notes Email Default On,') !== FALSE && $_GET['contact_note'] != 1 && $comment_type == 'note') { ?>
			$(document).ready(function() {
				$('[name="check_send_email"]').click();
			});
		<?php } ?>
		function ticket_comment_check_send_email(checked) {
			if(checked.checked) {
				$('[name="send_email_on_comment"]').val('Yes');
				$('.ticket_comment_email_send_div').show();
				$('[name=submit][value=add]').hide();
			} else {
				$('[name="send_email_on_comment"]').val('');
				$('.ticket_comment_email_send_div').hide();
				$('[name=submit][value=add]').show();
			}
		}
		</script>
		<div class="ticket_comment_email_send_div email_div" style="display:none;">
			<?php if(strpos($value_config, ',Notes Alert,') !== FALSE && $_GET['contact_note'] != 1 && $comment_type == 'note') { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Alert:</label>
					<div class="col-sm-8">
						<select name="assign_role[]" multiple class="chosen-select-deselect form-control">
							<?php $ticket_notes_alert_role = get_config($dbc, 'ticket_notes_alert_role');
							$on_security = get_security_levels($dbc);
							foreach($on_security as $category => $value) {
								if($value != 'super') {
									echo '<option value="'.$value.'" '.($ticket_notes_alert_role == $value ? 'selected' : '').'>'.$category.'</option>';
								}
							} ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Sender's Name:</label>
				<div class="col-sm-8">
					<input type="text" name="sender_name" class="form-control" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Sender's Address:</label>
				<div class="col-sm-8">
					<input type="text" name="sender_address" class="form-control" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Subject:</label>
				<div class="col-sm-8">
					<input type="text" name="subject" class="form-control" value="<?php echo $subject; ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Body:</label>
				<div class="col-sm-12">
					<textarea name="body" class="form-control"><?php echo $body; ?></textarea>
				</div>
			</div>
			<button class="btn brand-btn pull-right" name="submit" data-history-label="Send Note as Email" value="email">Send Email</button>
		</div>
		<a class="btn brand-btn pull-left" href="../blank_loading_page.php">Cancel</a>
		<button class="btn brand-btn pull-right" name="submit" value="add">Add Note</button>
		<div class="clearfix"></div>
	</form>
<?php } ?>
