<?php $guest_access = true;
include_once('include.php');
if($_POST['submit'] == 'send') {
	$ticketid = filter_var($_POST['ticketid'],FILTER_SANITIZE_STRING);
	$next_step = filter_var($_POST['next_step'],FILTER_SANITIZE_STRING);
	$details = filter_var($_POST['details'],FILTER_SANITIZE_STRING);
	$email_name = filter_var($_POST['email_name'],FILTER_SANITIZE_STRING);
	$email_address = filter_var($_POST['email_address'],FILTER_SANITIZE_STRING);
	$email_body = (empty($next_step) ? '' : 'Next Steps: '.$next_step.'<br />').'<b>Details:</b><br />'.$details;
    if(!empty(array_filter($_POST['links']))) {
        $email_body .= '<p>';
        foreach(array_filter($_POST['links']) as &$link) {
            $email_body .= '<a target="_blank" href="'.$link.'">'.$link.'</a><br />';
        }
        $email_body .= '</p>';
    }
	$recipients = [];
	if($ticketid > 0) {
		$get_ticket = $dbc->query("SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'")->fetch_assoc();
		foreach(explode(',',$get_ticket['communication_tags']) as $contact) {
			if($contact > 0) {
				$contact = get_email($dbc, $contact);
				if(!empty($contact)) {
					$recipients[] = $contact;
				}
			}
		}
		$subject = 'Response on '.get_ticket_label($dbc, $get_ticket);
		$dbc->query("INSERT INTO `email_communication` (`communication_type`,`businessid`,`contactid`,`projectid`,`ticketid`,`to_staff`,`subject`,`email_body`,`today_date`,`from_email`,`from_name`) SELECT 'External',`businessid`,`clientid`,`projectid`,`ticketid`,'".implode(',',$recipients)."','$subject','".htmlentities($email_body)."',NOW(),'$email_address','$email_name' FROM `tickets` WHERE `ticketid`='$ticketid'");
	}
	$id = $dbc->insert_id;
	$attach = [];
	foreach(array_filter($_FILES['upload']['name']) as $i => $filename) {
		if(!file_exists('Email Communication/download')) {
			mkdir('Email Communication/download',0777);
		}
		$filename = file_safe_str($filename, 'Email Communication/download/');
		move_uploaded_file($_FILES['upload']['tmp_name'][$i],'Email Communication/download/'.$filename);
		$dbc->query("INSERT INTO `email_communicationid_upload` (`email_communicationid`, `document`,`created_date`) VALUES ('$id','$filename',DATE(NOW()))");
		$attach[] = 'Email Communication/download/'.$filename;
	}
	send_email([$email_address => $email_name],array_unique($recipients),'','',$subject,html_entity_decode($email_body),implode('#FFM#',$attach)); ?>
    <script>
    window.location.replace('?r=<?= urlencode($_GET['r']) ?>');
    </script>
<?php }
$details = json_decode(decryptIt($_GET['r'])); ?>
<?php include('navigation.php'); ?>
<div class="container double-gap-bottom" style="min-height: calc(100vh - 48px)">
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
			$ticket_options = $dbc->query("SELECT GROUP_CONCAT(`fields` SEPARATOR ',') `field_config` FROM (SELECT `value` `fields` FROM `general_configuration` LEFT JOIN `tickets` ON `general_configuration`.`name` LIKE CONCAT('ticket_fields_',`tickets`.`ticket_type`) WHERE `ticketid`='$ticketid' UNION SELECT `tickets` `fields` FROM `field_config`) `fields`")->fetch_assoc(); ?>
            <?php $logo_upload = get_config($dbc, 'logo_upload');
            echo '<img src="'.(empty($logo_upload) || !file_exists('Settings/download/'.$logo_upload) ? 'img/logo.png' : 'Settings/download/'.$logo_upload).'" alt="'.(empty($logo_upload) || !file_exists('Settings/download/'.$logo_upload) ? 'Fresh Focus Media' : get_config($dbc, 'company_name')).'" class="center-block" width="300">'; ?>
            <script>
            $(document).ready(function() {
                $('[name=tag_group] select').change(saveTags);
            });
            function addRow(img) {
                destroyInputs();
                var clone = $(img).closest('.form-group').clone();
                clone.find('input,select').val('');
                $(img).closest('.form-group').after(clone);
                initTooltips();
                initInputs();
                $('[name=tag_group] select').off('change',saveTags).change(saveTags);
                clone.find('input').focus();
            }
            function remRow(img) {
                var row = $(img).closest('.form-group');
                if($('[name='+$(row).attr('name')+']').length <= 1) {
                    addRow(img);
                }
                row.remove();
                saveTags();
            }
            function saveTags() {
                var tags = [];
                $('[name=tagged]').each(function() {
                    tags.push(this.value);
                });
                $.post('external_ajax.php', {
                    action: '<?= encryptIt(json_encode(['ticketid'=>$ticketid,'action'=>'ticket_comm_tags'])) ?>',
                    comm_tags: tags
                });
            }
            </script>
            <h1><?= get_ticket_label($dbc, $get_ticket) ?></h1>
			<?php if($get_ticket['status'] != 'Archive' && $get_ticket['deleted'] == 0) { ?>
                <h3>Tagged Individuals</h3>
                <?php $tags = explode(',',$get_ticket['communication_tags']);
                $contact_list = sort_contacts_query($dbc->query("SELECT `contactid`,`category`,`name`,`last_name`,`first_name`,`email_address` FROM `contacts` WHERE `contactid` IN ('".implode("','",$tags)."') OR (`status` > 0 AND `deleted`=0 AND `contactid` IN ('".implode("','",array_filter(explode(',',$get_ticket['businessid'].','.$get_ticket['clientid'].','.$get_ticket['contactid'].','.$get_ticket['internal_qa_contactid'].','.$get_ticket['deliverable_contactid'])))."'))"));
                foreach($tags as $tag_contact) {
                    if(get_contact($dbc, $tag_contact, 'category') == 'Staff') { ?>
                        <input type="hidden" name="tagged" value="<?= $tag_contact ?>">
                    <?php } else { ?>
                        <div class="form-group" name="tag_group">
                            <input type="hidden" name="tagged" value="<?= $tag_contact ?>">
                            <label class="col-sm-4"><?= CONTACTS_NOUN ?>:</label>
                            <div class="col-sm-7">
                                <!--<select class="chosen-select-deselect" data-placeholder="Select <?= CONTACTS_NOUN ?>"><option />
                                    <?php foreach($contact_list as $contact) { ?>
                                        <option <?= $contact['contactid'] == $tag_contact ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['full_name'] ?></option>
                                    <?php } ?>
                                </select>-->
                                <?= get_contact($dbc, $tag_contact, 'name_company') ?>
                            </div>
                            <div class="col-sm-1">
                                <img class="pull-right inline-img cursor-hand no-toggle" src="img/remove.png" title="Remove <?= CONTACTS_NOUN ?>" onclick="remRow(this);">
                                <!--<img class="pull-right inline-img cursor-hand no-toggle" src="img/icons/ROOK-add-icon.png" title="Add <?= CONTACTS_NOUN ?>" onclick="addRow(this);">-->
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
				<?php if(strpos(','.$ticket_options['field_config'].',',',External Response Thread,') !== FALSE) { ?>
					<h3>Communication Log</h3>
					<?php include('Ticket/add_ticket_view_communication.php'); ?>
				<?php } ?>
				<input type="hidden" name="ticketid" value="<?= $ticketid ?>">
				<h3>Details</h3>
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
				<div class="form-group">
					<label class="col-sm-4">Details:</label>
					<div class="col-sm-8">
						<textarea name="details" class="no_tools"></textarea>
					</div>
				</div>
				<?php if(strpos(','.$ticket_options['field_config'].',',',External Response Status,') !== FALSE) { ?>
					<div class="form-group">
						<label class="col-sm-4">Documents / Images:</label>
						<div class="col-sm-7">
							<input type="file" name="upload[]" class="form-control">
                        </div>
                        <div class="col-sm-1">
							<img class="inline-img cursor-hand no-toggle" src="../img/icons/ROOK-add-icon.png" onclick="addRow(this);" title="Add Another File">
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4">Links:</label>
						<div class="col-sm-7">
							<input type="text" name="links[]" class="form-control">
						</div>
                        <div class="col-sm-1">
							<img class="inline-img cursor-hand no-toggle" src="../img/icons/ROOK-add-icon.png" onclick="addRow(this);" title="Add Another Link">
						</div>
					</div>
				<?php } ?>
				<?php if(strpos(','.$ticket_options['field_config'].',',',External Response Status,') !== FALSE) { ?>
					<div class="form-group">
						<label class="col-sm-4">Next Step:</label>
						<div class="col-sm-8">
							<select class="chosen-select-deselect" data-placeholder="Select Status" name="next_step"><option />
								<option value="Ready to Push Live">Ready to Push Live</option>
								<option value="QA Dev Needed - Bugs and Minor Changes">QA Dev Needed - Bugs and Minor Changes</option>
								<option value="This is not what I wanted">This is not what I wanted</option>
								<option value="I want additional changes not in the original details">I want additional changes not in the original details</option>
							</select>
						</div>
					</div>
				<?php } ?>
                <button class="btn brand-btn pull-right" type="submit" name="submit" value="send">Submit</button>
                <button class="btn brand-btn pull-left" type="reset" name="reset" value="reset" onclick="$('select').val('').trigger('change.select2');">Cancel</button>
			<?php } else { ?>
                <h3>This <?= TICKET_NOUN ?> has been archived.</h3>
            <?php } ?>
		<?php } ?>
        <div class="clearfix"></div>
	</form>
</div>
<?php include('footer.php');