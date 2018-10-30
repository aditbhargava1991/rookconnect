<?php include_once('config.php');
if($request_tab == 'new'): ?>
	<?php if(!empty($_POST['new_request'])) {
		$errors = '';
		$type = $_POST['type'];
		$customer = filter_var($_POST['customer'],FILTER_SANITIZE_STRING);
		$contactid = filter_var($_POST['contactid'],FILTER_SANITIZE_STRING);
		$date = date('Y-m-d');
		$incident_date = filter_var($_POST['incident_date'],FILTER_SANITIZE_STRING);
		$reported_date = date('Y-m-d h:i:s');
		$business = filter_var($_POST['business'],FILTER_SANITIZE_STRING);
		$businessid = filter_var($_POST['businessid'],FILTER_SANITIZE_STRING);
		$software_user = filter_var($_POST['src_user'],FILTER_SANITIZE_STRING);
		$software_userid = filter_var($_POST['src_contactid'],FILTER_SANITIZE_STRING);
		$software_role = filter_var($_POST['src_security'],FILTER_SANITIZE_STRING);
		$software_url = filter_var($_POST['software'],FILTER_SANITIZE_STRING);
		$email = filter_var($_POST['email'],FILTER_SANITIZE_STRING);
		$cc = filter_var(implode(';',$_POST['ccemail']),FILTER_SANITIZE_STRING);
		$heading = filter_var($_POST['heading'],FILTER_SANITIZE_STRING);
		$details = filter_var(htmlentities($_POST['details']),FILTER_SANITIZE_STRING);
        $link_list = [];
		$plan = filter_var(htmlentities($_POST['plan']),FILTER_SANITIZE_STRING);
		$discovery = filter_var(htmlentities($_POST['discovery']),FILTER_SANITIZE_STRING);
		$action = filter_var(htmlentities($_POST['action']),FILTER_SANITIZE_STRING);
		$check = filter_var(htmlentities($_POST['check']),FILTER_SANITIZE_STRING);
		$adjustments = filter_var(htmlentities($_POST['adjustments']),FILTER_SANITIZE_STRING);
		$support_insert = "INSERT INTO `support` (`name`, `contactid`, `company_name`, `businessid`, `software_url`, `software_userid`, `software_user_name`, `software_role`, `current_date`, `critical_incident`, `email`, `cc_email`, `heading`, `message`, `critical_plan`, `critical_discovery`, `critical_action`, `critical_check`, `critical_adjustments`, `support_type`)
			VALUES ('$customer', '$contactid', '$business', '$businessid', '$software_url', '$software_userid', '$software_user', '$software_role', '$date', '$incident_date', '$email', '$cc', '$heading', '$details', '$plan', '$discovery', '$action', '$check', '$adjustments', '$type')";
		if(!mysqli_query($dbc_support, $support_insert)) {
			$errors .= "Error Saving Support Request: ".mysqli_error($dbc_support)."\n";
		}
		
		$supportid = mysqli_insert_id($dbc_support);
		$email_attachments = '';
		foreach($_FILES['documents']['name'] as $row => $filename) {
			if($filename != '') {
				if (!file_exists('download')) {
					mkdir('download', 0777, true);
				}
				$filename = file_safe_str($filename);
				if(!move_uploaded_file($_FILES['documents']['tmp_name'][$row], 'download/'.$filename)) {
					$errors .= "Error Saving Attachment: ".$filename."\n";
				}
				$email_attachments .= 'download/'.$filename.'*#FFM#*';
				if(!mysqli_query($dbc_support, "INSERT INTO `support_uploads` (`supportid`, `document`, `created_by`) VALUES ('$supportid', '".WEBSITE_URL."/Support/download/$filename', '".get_contact($dbc, $_SESSION['contactid'])."')")) {
					$errors .= "Error Recording Attachment: ".mysqli_error($dbc_support)."\n";
				}
			}
		}
		foreach($_POST['links'] as $link) {
			if($link != '') {
                if(strpos($link,'http') === false) {
                    $link = 'http://'.$link;
                }
				if(!mysqli_query($dbc_support, "INSERT INTO `support_uploads` (`supportid`, `document`, `created_by`) VALUES ('$supportid', '$link', '".get_contact($dbc, $_SESSION['contactid'])."')")) {
					$errors .= "Error Recording Attachment: ".mysqli_error($dbc_support)."\n";
				}
                $link_list[] = '<a href="'.$link.'">'.$link.'</a>';
			}
		}

	    if (mysqli_affected_rows($dbc_support) == 1) {
			if($type == 'feedback') {
				$subject = "Feedback from $business";
				$body = "Feedback has been sent.<br />
					Name: $customer<br />
					Company: $business<br />
					Software URL: <a href='$software_url'>$software_url</a><br />
					User: $software_user_name<br />
					Security Level: $software_role<br />
					Email: $email<br />
					CC: $cc<br />
					Heading: $heading<br />
					Details<hr>\n".html_entity_decode($details)."\n
					Please review it as soon as possible. It can be found <a href='https://ffm.rookconnect.com/Support/customer_support.php?tab=feedback&type=feedback#$supportid'>here</a>.";
				$cust_subject = 'Confirmation of Your Feedback';
				$cust_body = "Hello $customer,
					<p>Your feedback has been received. The feedback is currently under review by our support team, and you may be contacted
					for any further details. For your records, you will find a copy of your original request below.</p>
					<p>Thank you,<br />
					Fresh Focus Media Support Team</p>
					<p>----------------------BEGIN ORIGINAL MESSAGE-----------------------------</p>
					<p>Heading: $heading<br />
					Details<hr>
					".html_entity_decode($details);
			}
			else if($type == 'last_minute_priority') {
				$subject = "Last Minute Priority from $business";
				$body = "A Last Minute Priority has been reported.<br />
					Who initiated the report: $customer<br />
					Company: $business<br />
					Software URL: <a href='$software_url'>$software_url</a><br />
					User: $software_user_name<br />
					Security Level: $software_role<br />
					Date of Emergency: $incident_date<br />
					Issue<hr>\n".html_entity_decode($details)."\n
					Please review it as soon as possible. It can be found <a href='https://ffm.rookconnect.com/Support/customer_support.php?tab=requests&type=last_minute_priority#$supportid'>here</a>.";
				$cust_subject = 'Confirmation of Your Last Minute Priority';
				$cust_body = "Hello $customer,
					<p>Your Last Minute Priority has been received. The request is currently under review by our support team, and
					you will be contacted shortly. For your records, you will find a copy of your original request below.</p>
					<p>Thank you,<br />
					Fresh Focus Media Support Team</p>
					<p>----------------------BEGIN ORIGINAL MESSAGE-----------------------------</p>
					<p>Date of Incident: $incident_date<br />
					Issue<hr>
					".html_entity_decode($details);
			}
			else {
				$subject = "Support Request from $business";
				$body = "A support request has been sent.<br />
					<h3>Date of Request: $date</h3>
					Type: $type<br />
					Name: $customer<br />
					Company: $business<br />
					Software URL: <a href='$software_url'>$software_url</a><br />
					User: $software_user_name<br />
					Security Level: $software_role<br />
					Email: $email<br />
					CC: $cc<br />
					Heading: $heading<br />
					Details<hr>\n".html_entity_decode($details)."\n
					Please review it as soon as possible. It can be found <a href='https://ffm.rookconnect.com/Support/customer_support.php?tab=requests&type=$type#$supportid'>here</a>.";
				$cust_subject = 'Confirmation of Your Support Request';
				$cust_body = "Hello $customer,
					<p>Your support request has been received. The request is currently under review by our support team, and
					you will be contacted shortly. For your records, you will find a copy of your original request below.</p>
					<p>Thank you,<br />
					Fresh Focus Media Support Team</p>
					<p>----------------------BEGIN ORIGINAL MESSAGE-----------------------------</p>
					<h3>Date of Request: $date</h3>
					<p>Heading: $heading<br />
					Details<hr>
					".html_entity_decode($details);
			}
            if(!empty($link_list)) {
                $body .= '<p><b>Links:</b><br />'.implode('<br />',$link_list).'</p>';
                $cust_body .= '<p><b>Links:</b><br />'.implode('<br />',$link_list).'</p>';
            }
			
			// Email to FFM staff.
			$default = get_config($dbc_support, 'support_recipients_default');
			$all = get_config($dbc_support, 'support_recipients_all');
			$address = get_config($dbc_support, 'support_recipients_'.$type);
			if(empty($address)) {
				$address = $default;
			}
			$address .= ';'.$all;
			try {
				send_email('info@rookconnect',explode(';',$address),'','',$subject,$body,$email_attachments);
			} catch(Exception $e) { $errors .= "Error sending notification to $address.\n"; }
			
			// Thank you Email to sender and CC email.
			$to = array_filter(array_unique(explode(';',$email)));
			$cc_list = array_filter(array_unique(explode(';',$cc)));
			try {
				send_email('info@rookconnect',$to,$cc_list,'',$cust_subject,$cust_body,$email_attachments);
			} catch(Exception $e) { $errors .= "Error sending notification to ".implode('; ',array_merge($to,$cc_list)).".\n"; }
		}
		
		if($errors != '') {
			echo "<script> alert('$errors'); </script>";
		}
		echo "<script> window.location.replace('customer_support.php?tab=requests&type=$type'); </script>";
	}
	$new_type = (!empty($_GET['new_type']) ? $_GET['new_type'] : '');
	$source = (!empty($_GET['source']) ? $_GET['source'] : ''); ?>
	<script>
	$(document).ready(function() {
		$('select[name=type]').off('change',selectType).change(selectType);
		$('select[name="set_staff[]"]').off('change',assign_staff).change(assign_staff);
	});
	function add_uploader(button) {
		var block = $('[name="documents[]"]:visible').last().closest('.form-group');
		var clone = block.clone();
		clone.find('input').val('');
		$(block).after(clone);
	}
	function add_link(button) {
		var block = $('[name="links[]"]:visible').last().closest('.form-group');
		var clone = block.clone();
		clone.find('input').val('');
		$(block).after(clone);
		$('[name="links[]"]:visible').last().focus();
	}
	function add_cc(button) {
		var block = $('[name="ccemail[]"]:visible').last().closest('.form-group');
		var clone = block.clone();
		clone.find('input').val('');
		$(block).after(clone);
		$('[name="ccemail[]"]:visible').last().focus();
	}
	function rem_uploader(button) {
        if($('[name="documents[]"]:visible').length <= 1) {
            add_uploader(button);
        }
		$(button).closest('.form-group').remove();
	}
	function rem_link(button) {
        if($('[name="links[]"]:visible').length <= 1) {
            add_link(button);
        }
		$(button).closest('.form-group').remove();
	}
	function rem_cc(button) {
        if($('[name="ccemail[]"]:visible').length <= 1) {
            add_cc(button);
        }
		$(button).closest('.form-group').remove();
	}
	function validate(form) {
		tinymce.triggerSave();
		if($('[name=email]').val() == '') {
			alert('Please provide your email address.');
			$(form).find('[name=email]').focus();
			return false;
		}
		if($('[name=details]').val() == '') {
			alert('Please provide details.');
			tinymce.execCommand('mceFocus',false,'details')
			return false;
		}
		$('.container form').hide().after('<h1>Submitting Request...</h1>');
	}
	function selectType() {
		type = this.value;
		<?php foreach(get_config($dbc_support, 'support_alert_%', true, null) as $name => $alert_value) {
			if($alert_value != '') {
				$type = substr($name,14); ?>
				if(type == '<?= $type ?>' && '<?= $type ?>' != 'feedback' && !confirm('<?= str_replace(['FFMNEWLINE'],['\n'],$alert_value) ?>')) {
					type = 'feedback';
				}
			<?php }
		} ?>
		window.location.replace('?tab=requests&type=new&new_type='+type);
	}
	</script>
	<div class="notice double-gap-bottom popover-examples">
		<img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" style="width:3em;">
		<div style="float:right; width:calc(100% - 4em);"><span class="notice-name">Note:</span>
		<?= empty($new_type) ? 'Please select a Request Type' : get_config($dbc_support, 'support_note_'.$new_type) ?></div>
		<div class="clearfix"></div>
	</div>
	<form class="form" method="POST" action="" enctype="multipart/form-data" onsubmit="return validate(this);">
		<div class="form-group clearfix">
			<label class="col-sm-4" for="type">Request Type:</label>
            <?php if(empty($new_type) || empty($source)) { ?>
                <div class="col-sm-8">
					<select name="type" id="type" class="chosen-select-deselect form-control"><option></option>
						<option <?= ($new_type == 'feedback' ? 'selected' : '') ?> value="feedback">Feedback & Ideas</option>
						<?php foreach($ticket_types as $type) { ?>
							<option <?= ($new_type == config_safe_str($type) ? 'selected' : '') ?> value="<?= config_safe_str($type) ?>"><?= $type ?></option>
						<?php } ?>
					</select>
                </div>
            <?php } else {
                echo '<div class="col-sm-8 pad-top-5">';
                echo '<input type="hidden" name="type" value="'.$new_type.'">';
                if($new_type == 'feedback') {
                    echo "Feedback &amp; Ideas";
                } else {
                    foreach($ticket_types as $type) {
                        if($new_type == config_safe_str($type)) {
                            echo $type;
                        }
                    }
                }
                echo '</div>';
            } ?>
		</div>
		<input type="hidden" name="src_user" value="<?= $_SESSION['user_name'] ?>">
		<input type="hidden" name="src_contactid" value="<?= $_SESSION['contactid'] ?>">
		<input type="hidden" name="software" value="<?= WEBSITE_URL ?>">
		<input type="hidden" name="src_security" value="<?= ROLE ?>">
		
		<?php if($new_type == 'last_minute_priority'): ?>
			<input type="hidden" name="heading" value="Critical Incident">
			<div class="form-group clearfix">
				<label class="col-sm-4" for="business">Business:</label>
				<div class="col-sm-8">
					<input type="text" name="business" id="business" value="<?= $user_name ?>" class="form-control">
					<input type="hidden" name="businessid" value="<?= $user ?>">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="customer">Who initiated the Priority:</label>
				<div class="col-sm-8">
					<input type="text" readonly name="customer" id="customer" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>" class="form-control">
					<input type="hidden" name="contactid" value="<?= $_SESSION['contactid'] ?>">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="email">Email<span class="text-red">*</span>:</label>
				<div class="col-sm-8">
					<input type="text" name="email" id="email" value="<?= get_email($dbc, $_SESSION['contactid']) ?>" class="form-control">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="ccemail">CC Email:</label>
				<div class="col-sm-7">
					<input type="text" name="ccemail[]" id="ccemail" value="" class="form-control">
				</div>
				<div class="col-sm-1">
					<img onclick="rem_cc(this); return false;" class="inline-img cursor-hand pull-right" src="../img/remove.png">
					<img onclick="add_cc(this); return false;" class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="date">Date and Time of Incident:</label>
				<div class="col-sm-8">
					<input type="text" name="incident_date" id="incident_date" value="<?= date('Y-m-d') ?>" class="form-control">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="date">Date and Time Reported to FFM:</label>
				<div class="col-sm-8">
					<input type="text" readonly name="date" id="date" value="<?= date('Y-m-d h:i') ?>" class="form-control">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="details">Issue as it was explained<span class="text-red">*</span>:</label>
				<div class="col-sm-8">
					<textarea name="details" id="details" class="form-control"></textarea>
				</div>
			</div>
				
			<div class="form-group clearfix">
				<label class="col-sm-4">Upload Documents:</label>
				<div class="col-sm-7">
					<input type="file" multiple name="documents[]" data-filename-placement="inside" class="form-control">
				</div>
				<div class="col-sm-1">
					<img onclick="rem_uploader(this); return false;" class="inline-img cursor-hand pull-right" src="../img/remove.png">
					<img onclick="add_uploader(this); return false;" class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png">
				</div>
			</div>
			<div class="form-group clearfix">
				<label class="col-sm-4">Attach Link:</label>
				<div class="col-sm-7">
					<input type="text" name="links[]" class="form-control">
				</div>
				<div class="col-sm-1">
					<img onclick="rem_link(this); return false;" class="inline-img cursor-hand pull-right" src="../img/remove.png">
					<img onclick="add_link(this); return false;" class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png">
				</div>
			</div>
		<?php elseif($new_type == 'feedback'): ?>
			<div class="form-group clearfix">
				<label class="col-sm-4" for="customer">Customer:</label>
				<div class="col-sm-8">
					<input type="text" name="customer" id="customer" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>" class="form-control">
					<input type="hidden" name="contactid" value="<?= $_SESSION['contactid'] ?>">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="date">Date:</label>
				<div class="col-sm-8">
					<input type="text" readonly name="date" id="date" value="<?= date('Y-m-d') ?>" class="form-control">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="business">Business:</label>
				<div class="col-sm-8">
					<input type="text" name="business" id="business" value="<?= $user_name ?>" class="form-control">
					<input type="hidden" name="businessid" value="<?= $user ?>">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="email">Email<span class="text-red">*</span>:</label>
				<div class="col-sm-8">
					<input type="text" name="email" id="email" value="<?= get_email($dbc, $_SESSION['contactid']) ?>" class="form-control">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="ccemail">CC Email:</label>
				<div class="col-sm-7">
					<input type="text" name="ccemail[]" id="ccemail" value="" class="form-control">
				</div>
				<div class="col-sm-1">
					<img onclick="rem_cc(this); return false;" class="inline-img cursor-hand pull-right" src="../img/remove.png">
					<img onclick="add_cc(this); return false;" class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="heading">Feedback Heading:</label>
				<div class="col-sm-8">
					<select name="heading" id="heading" class="form-control chosen-select-deselect"><option></option>
						<option value="Creative Design">Creative Design</option>
						<option value="Digital Advertising - SEO and SEM">Digital Advertising (SEO &amp; SEM)</option>
						<option value="Hosting, Domains and Emails">Hosting, Domains &amp; Emails</option>
						<option value="Marketing Strategies">Marketing Strategies</option>
						<option value="Meeting Request">Meeting Request</option>
						<option value="New Idea">New Idea</option>
						<option value="New Software Functionality">New Software Functionality</option>
						<option value="Social Media and Blog Work">Social Media &amp; Blog Work</option>
						<option value="Software Revision - Bug">Software Revision (Bug)</option>
						<option value="Web Design">Web Design</option>
					</select>
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="details">Details<span class="text-red">*</span>:</label>
				<div class="col-sm-8">
					<textarea name="details" id="details" class="form-control"></textarea>
				</div>
			</div>
				
			<div class="form-group clearfix">
				<label class="col-sm-4">Upload Documents:</label>
				<div class="col-sm-7">
					<input type="file" multiple name="documents[]" data-filename-placement="inside" class="form-control">
				</div>
				<div class="col-sm-1">
					<img onclick="rem_uploader(this); return false;" class="inline-img cursor-hand pull-right" src="../img/remove.png">
					<img onclick="add_uploader(this); return false;" class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png">
				</div>
			</div>
			<div class="form-group clearfix">
				<label class="col-sm-4">Attach Link:</label>
				<div class="col-sm-7">
					<input type="text" name="links[]" class="form-control">
				</div>
				<div class="col-sm-1">
					<img onclick="rem_link(this); return false;" class="inline-img cursor-hand pull-right" src="../img/remove.png">
					<img onclick="add_link(this); return false;" class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png">
				</div>
			</div>
		<?php else: ?>
			<div class="form-group clearfix">
				<label class="col-sm-4" for="customer">Customer:</label>
				<div class="col-sm-8">
					<input type="text" name="customer" id="customer" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>" class="form-control">
					<input type="hidden" name="contactid" value="<?= $_SESSION['contactid'] ?>">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="date">Date:</label>
				<div class="col-sm-8">
					<input type="text" readonly name="date" id="date" value="<?= date('Y-m-d') ?>" class="form-control">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="business">Business:</label>
				<div class="col-sm-8">
					<input type="text" name="business" id="business" value="<?= $user_name ?>" class="form-control">
					<input type="hidden" name="businessid" value="<?= $user ?>">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="email">Email<span class="text-red">*</span>:</label>
				<div class="col-sm-8">
					<input type="text" name="email" id="email" value="<?= get_email($dbc, $_SESSION['contactid']) ?>" class="form-control">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="ccemail">CC Email:</label>
				<div class="col-sm-7">
					<input type="text" name="ccemail[]" id="ccemail" value="" class="form-control">
				</div>
				<div class="col-sm-1">
					<img onclick="rem_cc(this); return false;" class="inline-img cursor-hand pull-right" src="../img/remove.png">
					<img onclick="add_cc(this); return false;" class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png">
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="heading">Support Request Heading:</label>
				<div class="col-sm-8">
					<select name="heading" id="heading" class="form-control chosen-select-deselect"><option></option>
						<option value="Creative Design">Creative Design</option>
						<option value="Digital Advertising - SEO and SEM">Digital Advertising (SEO &amp; SEM)</option>
						<option value="Hosting, Domains and Emails">Hosting, Domains &amp; Emails</option>
						<option value="Marketing Strategies">Marketing Strategies</option>
						<option value="Meeting Request">Meeting Request</option>
						<option value="New Idea">New Idea</option>
						<option value="New Software Functionality">New Software Functionality</option>
						<option value="Social Media and Blog Work">Social Media &amp; Blog Work</option>
						<option value="Software Revision - Bug">Software Revision (Bug)</option>
						<option value="Web Design">Web Design</option>
					</select>
				</div>
			</div>
			
			<div class="form-group clearfix">
				<label class="col-sm-4" for="details">Details<span class="text-red">*</span>:</label>
				<div class="col-sm-8">
					<textarea name="details" id="details" class="form-control"></textarea>
				</div>
			</div>
				
			<div class="form-group clearfix">
				<label class="col-sm-4">Upload Documents:</label>
				<div class="col-sm-7">
					<input type="file" multiple name="documents[]" data-filename-placement="inside" class="form-control">
				</div>
				<div class="col-sm-1">
					<img onclick="rem_uploader(this); return false;" class="inline-img cursor-hand pull-right" src="../img/remove.png">
					<img onclick="add_uploader(this); return false;" class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png">
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="form-group clearfix">
				<label class="col-sm-4">Attach Link:</label>
				<div class="col-sm-7">
					<input type="text" name="links[]" class="form-control">
				</div>
				<div class="col-sm-1">
					<img onclick="rem_link(this); return false;" class="inline-img cursor-hand pull-right" src="../img/remove.png">
					<img onclick="add_link(this); return false;" class="inline-img cursor-hand pull-right" src="../img/icons/ROOK-add-icon.png">
				</div>
			</div>
		<?php endif; ?>
		
		<button type="submit" name="new_request" value="new" class="btn brand-btn pull-right double-gap-bottom">Submit Request</button>
        <div class="clearfix"></div>
	</form>
<?php elseif($request_tab == 'closed'): ?>
	<div class="notice double-gap-bottom popover-examples">
		<img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" style="width:3em;">
		<div style="float:right; width:calc(100% - 4em);"><span class="notice-name">Note:</span>
		All completed Support Requests will be displayed here for two months. After two months completed requests are moved to the archive, still accessible upon request.</div>
		<div class="clearfix"></div>
	</div>
    <img src="../img/icons/ROOK-3dot-icon.png" class="no-toggle cursor-hand offset-left-5 theme-color-icon pull-right show-on-mob" title="" width="25" data-title="Show/Hide Search" onclick="$('.search-group').toggle();"><br />&nbsp;<div class="clearfix"></div>
	<form name='search_form' method='POST' action=''>
	<?php $date = date('Y-m-d',strtotime('-2month'));
	$search_string = '';
	$search_cust = '';
	$search_start = '';
	$search_end = date('Y-m-d');
	$search_head = '';
	$search_details = '';
	if(!empty($_POST['search'])) {
		$search_cust = $_POST['search_cust'];
		$search_start = $_POST['search_start'];
		$search_end = $_POST['search_end'];
		$search_head = $_POST['search_head'];
		$search_details = $_POST['search_details'];
		
		$search_string = " AND `current_date` >= '$search_start' AND `current_date` <= '$search_end' AND `company_name` LIKE '$search_cust%' AND `heading` LIKE '%$search_head%' AND IFNULL(`message`,'') LIKE '%$search_details%'";
	} if(!empty($_GET['search'])) {
		$search_cust = $_GET['search_cust'];
        if($search_cust == 'undefined') {
            $search_cust = '';
        }
		$search_start = $_GET['search_start'];
        if($search_start == 'undefined') {
            $search_start = '';
        }
		$search_end = $_GET['search_end'];
        if($search_end == 'undefined') {
            $search_end = date('Y-m-d');
        }
		$search_head = $_GET['search_head'];
        if($search_head == 'undefined') {
            $search_head = '';
        }
		$search_details = $_GET['search_details'];
        if($search_details == 'undefined') {
            $search_details = '';
        }
		
		$search_string = " AND `current_date` >= '$search_start' AND `current_date` <= '$search_end' AND `company_name` LIKE '$search_cust%' AND `heading` LIKE '%$search_head%' AND IFNULL(`message`,'') LIKE '%$search_details%'";
	} ?>
	<div class="search-group" style="display:none;">
		<div class="form-group col-sm-12">
			<?php if($user_category == 'Staff') { ?>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="col-sm-4">
						<label for="site_name" class="control-label">Search By Customer:</label>
					</div>
					<div class="col-sm-8">
						<select data-placeholder="Select a Customer" name="search_cust" class="chosen-select-deselect form-control">
							<option></option>
							<?php $query = mysqli_query($dbc_support,"SELECT DISTINCT `company_name` FROM `support` WHERE `deleted`=1 ORDER BY `company_name`");
							while($custid = mysqli_fetch_array($query)['company_name']) { ?>
								<option <?php if ($custid == $search_cust) { echo " selected"; } ?> value='<?php echo  $custid; ?>' ><?= $custid ?></option><?php
							} ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search From Date:</label>
				</div>
				<div class="col-sm-8">
					<input type="text" name="search_start" value="<?= $search_start ?>" class="form-control datepicker">
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search To Date:</label>
				</div>
				<div class="col-sm-8">
					<input type="text" name="search_end" value="<?= $search_end ?>" class="form-control datepicker">
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search By Heading:</label>
				</div>
				<div class="col-sm-8">
						<select data-placeholder="Select a Heading" name="search_head" class="chosen-select-deselect form-control">
							<option></option>
							<?php $query = mysqli_query($dbc_support,"SELECT DISTINCT `heading` FROM `support` WHERE `deleted`=1 ORDER BY `heading`");
							while($heading = mysqli_fetch_array($query)['heading']) { ?>
								<option <?php if ($heading == $search_head) { echo " selected"; } ?> value='<?php echo  $heading; ?>' ><?= $heading ?></option><?php
							} ?>
						</select>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search By Details:</label>
				</div>
				<div class="col-sm-8">
					<input type="text" name="search_details" value="<?= $search_details ?>" class="form-control">
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<div class="pull-right gap-right gap-top">
				<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click to refresh the page and see closed requests from the past two months."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a href="" class="btn brand-btn mobile-block">Display All</a>
				<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here after you have entered search criteria."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<button type="submit" name="search" value="Search" class="hide-titles-mob btn brand-btn mobile-block">Search</button>
                <a type="submit" name="search" value="Search" class="show-on-mob btn brand-btn mobile-block" onclick="loadPanel($(this).closest('.panel').find('.panel-heading'), 'requests.php?type=closed&search=Search&search_cust='+$('[name=search_cust]:visible').val()+'&search_start='+$('[name=search_start]:visible').val()+'&search_end='+$('[name=search_end]:visible').val()+'&search_head='+$('.search_head:visible').find('[name=search_head]').val()+'&search_details='+$('[name=search_details]').val());">Search</a>
			</div>
		</div><!-- .form-group -->
		<div class="clearfix"></div>
	</div>
	</form>
	<?php $support_list = mysqli_query($dbc_support, "SELECT * FROM `support` WHERE (`businessid`='$user' OR `contactid`='$user' OR '$user_category' IN (".STAFF_CATS.")) AND `deleted`=1 AND `archived_date` > '$date'".$search_string." ORDER BY `archived_date` DESC, `supportid` DESC");
    if(mysqli_num_rows($support_list) > 0) { ?>
        <div class="has-dashboard dashboard-container gap-top">
            <div class="item-list">
                <div class="info-block-header"><h4>Closed Support Requests</h4>
                    <div class="small">REQUESTS: <?= $support_list->num_rows ?></div>
                </div>
                <ul class="connectedChecklist full-width">
                    <?php while($row = mysqli_fetch_array($support_list)) {
                        echo '<li id="'.$row['supportid'].'" class="dashboard-item padded" style="'.($row['flag_colour'] == '' ? '' : 'background-color: #'.$row['flag_colour'].';').' border: solid #FF0000 2px; margin-bottom: 1em;">';
                        echo '<span>';
                        if($user_category == 'Staff') { ?>
                            <span class="action-icons pad-bottom" data-support="<?= $row['supportid'] ?>">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-flag-icon.png" class="no-toggle inline-img cursor-hand" title="Flag This!" onclick="flag_item(this);">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-attachment-icon.png" class="no-toggle inline-img cursor-hand" title="Attach File" onclick="attach_file(this);">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-reply-icon.png" class="no-toggle inline-img cursor-hand" title="Reply" onclick="send_reply(this);">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-email-icon.png" class="no-toggle inline-img cursor-hand" title="Send Email" onclick="send_email(this);">
                            </span>
                            <input type="text" name="reply_<?= $row['supportid'] ?>" style="display:none; margin-top: 2em;" class="form-control" />
                            <input type="text" name="checklist_time_<?= $row['supportid'] ?>" style="display:none; margin-top: 2em;" class="form-control timepicker" />
                            <input type="text" name="reminder_<?= $row['supportid'] ?>" style="display:none; margin-top: 2em;" class="form-control datepicker" />
                            <input type="file" name="attach_<?= $row['supportid'] ?>" style="display:none;" class="form-control" />
                        <?php }
                        echo '<span class="display-field"><b>Date of Request: '.$row['current_date']."</b><br />
                        Software Link: <a href='".$row['software_url']."'>".$row['software_url']."</a><br />
                        User Name: ".$row['software_user_name']."<br />
                        Security Level: ".ucwords(implode(', ', array_filter(array_unique(explode(',',$row['software_role'])))))."<br />
                        Support Request #".$row['supportid']."<br />".$row['heading']."<hr>".html_entity_decode($row['message']).'</span>';
                        $documents = mysqli_query($dbc_support, "SELECT * FROM support_uploads WHERE supportid='".$row['supportid']."'");
                        while($doc = mysqli_fetch_array($documents)) {
                            $link = $doc['document'];
                            echo '<br /><a target="_blank" href="'.(strpos($link,'http') === false ? 'http://' : '').$link.'">'.(strpos($link, WEBSITE_URL.'/Support/download/') !== false ? str_replace(WEBSITE_URL.'/Support/download/', '', $link) : $link).' (Attached by '.$doc['created_by'].' on '.$doc['created_date'].')</a>';
                        }
                        echo '</span></li>';
                    } ?>
                </ul>
            </div>
        </div>
	<?php } else {
		echo "<h4 class='pad-10'>No Support Requests Found</h4>";
	} ?>
<?php else: ?>
	<div class="notice double-gap-bottom popover-examples">
		<img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" style="width:3em;">
		<div style="float:right; width:calc(100% - 4em);"><span class="notice-name">Note:</span>
		All active <?= ($request_tab == 'feedback' ? 'Feedback & Ideas' : ($request_tab == 'requests' ? 'Support Requests' : 'Critical Incidents')) ?> display here. As we complete support requests, require your approval or simply want to provide you with work ready for your approval, all Active Requests will display here.</div>
		<div class="clearfix"></div>
	</div>
    <img src="../img/icons/ROOK-3dot-icon.png" class="no-toggle cursor-hand offset-left-5 theme-color-icon pull-right show-on-mob" title="" width="25" data-title="Show/Hide Search" onclick="$('.search-group').toggle();"><br />&nbsp;<div class="clearfix"></div>
	
	<form name='search_form' method='POST' action=''>
	<?php $date = date('Y-m-d',strtotime('-2month'));
	$search_string = '';
	$search_cust = '';
	$search_start = '';
	$search_end = date('Y-m-d');
	$search_head = '';
	$search_details = '';
	if(!empty($_POST['search'])) {
		$search_cust = $_POST['search_cust'];
		$search_start = $_POST['search_start'];
		$search_end = $_POST['search_end'];
		$search_head = $_POST['search_head'];
		$search_details = $_POST['search_details'];
		
		$search_string = " AND `current_date` >= '$search_start' AND `current_date` <= '$search_end' AND `company_name` LIKE '$search_cust%' AND `heading` LIKE '%$search_head%' AND IFNULL(`message`,'') LIKE '%$search_details%'";
	} if(!empty($_GET['search'])) {
		$search_cust = $_GET['search_cust'];
        if($search_cust == 'undefined') {
            $search_cust = '';
        }
		$search_start = $_GET['search_start'];
        if($search_start == 'undefined') {
            $search_start = '';
        }
		$search_end = $_GET['search_end'];
        if($search_end == 'undefined') {
            $search_end = date('Y-m-d');
        }
		$search_head = $_GET['search_head'];
        if($search_head == 'undefined') {
            $search_head = '';
        }
		$search_details = $_GET['search_details'];
        if($search_details == 'undefined') {
            $search_details = '';
        }
		
		$search_string = " AND `current_date` >= '$search_start' AND `current_date` <= '$search_end' AND `company_name` LIKE '$search_cust%' AND `heading` LIKE '%$search_head%' AND IFNULL(`message`,'') LIKE '%$search_details%'";
	} ?>
	<div class="search-group" style="display:none;">
		<div class="form-group col-sm-12">
			<?php if($user_category == 'Staff') { ?>
				<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
					<div class="col-sm-4">
						<label for="site_name" class="control-label">Search By Customer:</label>
					</div>
					<div class="col-sm-8 search_cust">
						<select data-placeholder="Select a Customer" name="search_cust" class="chosen-select-deselect form-control">
							<option></option>
							<?php $query = mysqli_query($dbc_support,"SELECT DISTINCT `company_name` FROM `support` WHERE `support_type`='$request_tab' AND `deleted`=0 ORDER BY `company_name`");
							while($custid = mysqli_fetch_array($query)['company_name']) { ?>
								<option <?php if ($custid == $search_cust) { echo " selected"; } ?> value='<?php echo  $custid; ?>' ><?= $custid ?></option><?php
							} ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search From Date:</label>
				</div>
				<div class="col-sm-8">
					<input type="text" name="search_start" value="<?= $search_start ?>" class="form-control datepicker">
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search To Date:</label>
				</div>
				<div class="col-sm-8">
					<input type="text" name="search_end" value="<?= $search_end ?>" class="form-control datepicker">
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search By Heading:</label>
				</div>
				<div class="col-sm-8 search_head">
						<select data-placeholder="Select a Heading" name="search_head" class="chosen-select-deselect form-control">
							<option></option>
							<?php $query = mysqli_query($dbc_support,"SELECT DISTINCT `heading` FROM `support` WHERE `support_type`='$request_tab' AND `deleted`=0 ORDER BY `heading`");
							while($heading = mysqli_fetch_array($query)['heading']) { ?>
								<option <?php if ($heading == $search_head) { echo " selected"; } ?> value='<?php echo  $heading; ?>' ><?= $heading ?></option><?php
							} ?>
						</select>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search By Details:</label>
				</div>
				<div class="col-sm-8">
					<input type="text" name="search_details" value="<?= $search_details ?>" class="form-control">
				</div>
			</div>
		</div>
		<div class="form-group col-sm-12">
			<div class="pull-right gap-right gap-top">
				<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click to refresh the page and see all active requests."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<a href="" class="btn brand-btn mobile-block">Display All</a>
				<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here after you have entered search criteria."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				<button type="submit" name="search" value="Search" class="hide-titles-mob btn brand-btn mobile-block">Search</button>
				<a type="submit" name="search" value="Search" class="show-on-mob btn brand-btn mobile-block" onclick="loadPanel($(this).closest('.panel').find('.panel-heading'), 'requests.php?type=<?= $_GET['type'] ?>&search=Search&search_cust='+$('[name=search_cust]:visible').val()+'&search_start='+$('[name=search_start]:visible').val()+'&search_end='+$('[name=search_end]:visible').val()+'&search_head='+$('.search_head:visible').find('[name=search_head]').val()+'&search_details='+$('[name=search_details]').val());">Search</a>
			</div>
		</div><!-- .form-group -->
		<div class="clearfix"></div>
	</div>
	</form>
	<?php $support_list = mysqli_query($dbc_support, "SELECT * FROM `support` WHERE (`businessid`='$user' OR `contactid`='$user' OR '$user_category' IN (".STAFF_CATS.")) AND `support_type`='$request_tab' AND `deleted`=0".$search_string." ORDER BY `current_date` DESC, `supportid` DESC");
	$staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
	if(mysqli_num_rows($support_list) > 0) { ?>
        <div class="has-dashboard dashboard-container gap-top">
            <div class="item-list">
                <div class="info-block-header"><h4><?= $request_tab_name ?></h4>
                    <div class="small">REQUESTS: <?= $support_list->num_rows ?></div>
                </div>
                <ul class="connectedChecklist full-width">
                    <?php while($row = mysqli_fetch_array($support_list)) {
                        echo '<a name="'.$row['supportid'].'"></a>
                        <li id="'.$row['supportid'].'" class="dashboard-item padded" style="'.($row['flag_colour'] == '' ? '' : 'background-color: #'.$row['flag_colour'].';').' border: solid #FF0000 2px; margin-bottom: 1em;">';
                        echo '<span>';
                        if($user_category == 'Staff') { ?>
                            <span class="action-icons pad-bottom" data-support="<?= $row['supportid'] ?>">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-flag-icon.png" class="inline-img cursor-hand no-toggle" title="Flag This!" onclick="flag_item(this);">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-attachment-icon.png" class="inline-img cursor-hand no-toggle" title="Upload File" onclick="attach_file(this);">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-reply-icon.png" class="inline-img cursor-hand no-toggle" title="Add Comment" onclick="send_reply(this);">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-email-icon.png" class="inline-img cursor-hand no-toggle" title="Send Email" onclick="send_email(this);">
                                <img src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png" class="inline-img cursor-hand no-toggle" title="Close" onclick="archive(this);">
                            </span>
                            <div class="form-group no-pad no-margin">
                                <label class="col-sm-4">Staff Responsible:</label>
                                <div class="col-sm-8"><select name="set_staff[]" multiple data-placeholder="Select Staff to Assign" class="chosen-select-deselect form-control"><option></option>
                                    <?php foreach($staff_list as $id) {
                                        echo '<option '.(strpos(','.$row['assigned'].',', ','.$id.',') !== false ? 'selected' : '').' value="'.$id.'">'.get_contact($dbc, $id).'</option>';
                                    } ?>
                                    </select>
                                </div>
                            </div>
                            <?php if($row['ticketid'] > 0) {
                                echo '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$row['ticketid'].'&from='.urlencode(WEBSITE_URL.'/Support/customer_support.php?tab=requests&type='.$request_tab.'#'.$row['supportid']).'">Open '.TICKET_NOUN.' #'.$row['ticketid'].'</a>';
                            } else {
                                echo '<br /><button value="'.$row['supportid'].'" class="btn brand-btn pull-right" onclick="create_ticket(this); return false;">Create and Edit '.TICKET_NOUN.'</button>';
                            } ?>
                            <input type="text" name="reply_<?= $row['supportid'] ?>" style="display:none; margin-top: 2em;" class="form-control" />
                            <input type="text" name="checklist_time_<?= $row['supportid'] ?>" style="display:none; margin-top: 2em;" class="form-control timepicker" />
                            <input type="text" name="reminder_<?= $row['supportid'] ?>" style="display:none; margin-top: 2em;" class="form-control datepicker" />
                            <input type="file" name="attach_<?= $row['supportid'] ?>" style="display:none;" class="form-control" />
                            <div class="clearfix"></div>
                        <?php }
                        echo '<span class="display-field"><b>Date of Request: '.$row['current_date']."</b><br />
                        Software Link: <a href='".$row['software_url']."'>".$row['software_url']."</a><br />
                        User Name: ".$row['software_user_name']."<br />
                        Security Level: ".ucwords(implode(', ', array_filter(array_unique(explode(',',$row['software_role'])))))."<br />
                        Support Request #".$row['supportid']."<br />".$row['heading']."<hr>".html_entity_decode($row['message']).'</span>';
                        $documents = mysqli_query($dbc_support, "SELECT * FROM support_uploads WHERE supportid='".$row['supportid']."'");
                        while($doc = mysqli_fetch_array($documents)) {
                            $link = $doc['document'];
                            echo '<br /><a target="_blank" href="'.(strpos($link,'http') === false ? 'http://' : '').$link.'">'.(strpos($link, WEBSITE_URL.'/Support/download/') !== false ? str_replace(WEBSITE_URL.'/Support/download/', '', $link) : $link).' (Attached by '.$doc['created_by'].' on '.$doc['created_date'].')</a>';
                        }
                        echo '</span></li>';
                    } ?>
                </ul>
            </div>
        </div>
	<?php } else {
		echo "<h4 class='pad-10'>No $request_tab_name Found</h4>";
	} ?>
<?php endif; ?>
<script>
function assign_staff() {
	support = this;
	support_id = $(support).parents('span').data('support');
	staff_id = $(support).val();
	$.ajax({
		method: 'POST',
		url: 'support_ajax.php?fill=assign',
		data: { id: support_id, staff: staff_id },
		complete: function(result) { console.log(result.responseText); }
	});
}
function send_email(support) {
	if(confirm("Please confirm that you wish to send an email to the <?= ($user_category == 'Staff' ? 'user' : 'support staff') ?>.")) {
		support_id = $(support).parents('span').data('support');
		$.ajax({
			method: 'POST',
			url: 'support_ajax.php?fill=email',
			data: { id: support_id, user_category: '<?= $user_category ?>' },
			complete: function(result) { console.log(result.responseText); }
		});
	}
}
function send_reply(support) {
	support_id = $(support).parents('span').data('support');
	$('[name=reply_'+support_id+']').show().focus();
	$('[name=reply_'+support_id+']').keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	});
	$('[name=reply_'+support_id+']').blur(function() {
		$(this).hide();
		var reply = $(this).val().trim();
		$(this).val('');
		if(reply != '') {
			var today = new Date();
			var save_reply = reply + " (Reply added by <?php echo decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?> at "+today.toLocaleString()+")";
			$.ajax({
				method: 'POST',
				url: 'support_ajax.php?fill=reply',
				data: { id: support_id, reply: save_reply },
				complete: function(result) { window.location.reload(); }
			})
		}
	});
}
function attach_file(support) {
	support_id = $(support).parents('span').data('support');
	$('[name=attach_'+support_id+']').change(function() {
		var fileData = new FormData();
		fileData.append('file',$('[name=attach_'+support_id+']')[0].files[0]);
		$.ajax({
			contentType: false,
			processData: false,
			type: "POST",
			url: "support_ajax.php?fill=upload&id="+support_id,
			data: fileData,
			complete: function(result) {
				console.log(result);
				window.location.reload();
			}
		});
	});
	$('[name=attach_'+support_id+']').click();
}
function flag_item(support) {
	support_id = $(support).parents('span').data('support');
	$.ajax({
		method: "POST",
		url: "support_ajax.php?fill=flag",
		data: { id: support_id },
		complete: function(result) {
			console.log(result);
			$(support).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
		}
	});
}
function archive(support) {
	support_id = $(support).parents('span').data('support');
	if(confirm("Are you sure you want to mark this item as completed?")) {
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "support_ajax.php?fill=archive&supportid="+support_id,
			dataType: "html",   //expect html to be returned
			success: function(response){
				console.log(response);
				window.location.reload();
			}
		});
	}
}
function create_ticket(support) {
	$.ajax({
		method: "POST",
		url: "support_ajax.php?fill=create_ticket",
		data: { id: support.value },
		complete: function(result) {
			window.location.href = "../Ticket/index.php?edit="+result.responseText+"&from=<?= urlencode(WEBSITE_URL.'/Support/customer_support.php?tab=requests&type=active#') ?>"+support.value;
		}
	});
}
</script>