<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Deliverables</h3>') ?>
<?php if($access_all === TRUE) { ?>
	<div class="col-md-12">
		<?php
		if(!strpos_any(['Deliverable To Do','Deliverable Internal','Deliverable Customer'], $value_config)) {
			$value_config .= "Deliverable To Do,Deliverable Internal,Deliverable Customer,";
		}
		?>
		<?php foreach ($field_sort_order as $field_sort_field) { ?>
			<?php if(strpos($value_config,',Deliverable Status,') !== FALSE && $field_sort_field == 'Deliverable Status') { ?>
				<div class="form-group">
					<label for="site_name" class="col-sm-4 control-label">Status:</label>
					<div class="col-sm-8">

						<select data-placeholder="Select a Status..." name="status" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" id="status" class="chosen-select-deselect form-control input-sm">
						  <option value=""></option>
						  <?php
							$tabs = get_config($dbc, 'ticket_status');
							$each_tab = explode(',', $tabs);
							foreach ($each_tab as $cat_tab) {
								if ($status == $cat_tab) {
									$selected = 'selected="selected"';
								} else {
									$selected = '';
								}
								echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
							}
						  ?>
						</select>
					</div>
				</div>
			<?php } ?>

			<?php if(strpos($value_config,',Deliverable To Do,') !== FALSE && $field_sort_field == 'Deliverable To Do') {
				if($_GET['new_ticket_calendar'] == 'true') {
					$contactid = $calendar_contactid;
				}
                ?>
				<div class="email-block">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">Start Date, End Date & Assign To:</label>
						<div class="col-sm-6">
							<input name="to_do_date" id="to_do_date" value="<?php echo $to_do_date; ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" type="text" class="datepicker email_date_info">
							<input name="to_do_start_time" id="to_do_start_time" value="<?php echo $to_do_start_time; ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" type="text" class="datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '' ?>" placeholder="Start Time"><br />
							<input name="to_do_end_date" id="to_do_end_date" value="<?php echo $to_do_end_date; ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" type="text" class="datepicker">
							<input name="to_do_end_time" id="to_do_end_time" value="<?php echo $to_do_end_time; ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" type="text" class="datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '' ?>" placeholder="End Time">

                            <div class="start-ticket-staff">
                            <div class="col-sm-7">
							<select data-placeholder="Select a Staff..." id="contactid" name="contactid[]" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control email_recipient" width="380">
							  <option value=""></option>
							  <?php $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""));
								foreach($staff_query as $row) { ?>
									<option <?php if (strpos($contactid, ','.$row['contactid'].',') !== FALSE) {
									echo " selected"; } ?> value="<?php echo $row['contactid']; ?>"><?php echo $row['first_name'].' '.$row['last_name']; ?></option>
								<?php }
							  ?>
							</select>
                            </div>

                            <div class="col-sm-2">
                                <img class="inline-img pull-right" onclick="startTicketStaff(this);" src="../img/icons/ROOK-add-icon.png">
                                <img class="inline-img pull-right" onclick="deletestartTicketStaff(this);" src="../img/remove.png">
                            </div>
                            </div>

						<label class="form-checkbox"><input type="checkbox" value="1" name="doing_email" onclick="doing_check_send_email(this);"> Send Email</label>
						</div>
					</div>
					<?php
					$sender = get_contact($dbc, $_SESSION['contactid'], 'email_address');
						$subject = 'FFM - '.TICKET_NOUN.' assigned to you for Doing';
						$body = 'A ticket has been assigned to you.<br/><br/>
                            <b><a target="_blank" href="'.WEBSITE_URL.'/Ticket/index.php?edit=[TICKETID]">'.TICKET_NOUN.' :  #[TICKETID]</a></b><br/><br/>
							Business : [CLIENT]<br>
                            Project : [PROJECT]<br>
							'.TICKET_NOUN.' Heading : [HEADING]<br><br>
                            Description : [DESC]<br>
							Status : [STATUS]<br>
                            Start Date : [START_DATE]<br>
                            End Date : [END_DATE]<br><br>

							<img src="'.WEBSITE_URL.'/img/ffm-signature.png" width="154" height="77" border="0" alt="">';
					?>
					<script>
					function doing_check_send_email(checked) {
						if(checked.checked) {
							$('.doing_email_send_div').show();
						} else {
							$('.doing_email_send_div').hide();
						}
					}
					</script>
					<div class="doing_email_send_div email_div" style="display:none;">
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Sender's Name:</label>
							<div class="col-sm-8">
								<input type="text" name="ticket_comment_email_sender_name" class="form-control email_sender_name" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Sender's Address:</label>
							<div class="col-sm-8">
								<input type="text" name="ticket_comment_email_sender" class="form-control email_sender" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Subject:</label>
							<div class="col-sm-8">
								<input type="text" name="ticket_comment_email_subject" class="form-control email_subject" value="<?php echo $subject; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Body:</label>
							<div class="col-sm-12">
								<textarea name="ticket_comment_email_body" class="form-control email_body"><?php echo $body; ?></textarea>
							</div>
						</div>
						<button class="btn brand-btn pull-right" data-table="tickets" data-id-field="ticketid" data-id="<?= $ticketid ?>" data-field="to_do_date" onclick="send_email(this); return false;">Send Email</button>
					</div>
					<div class="clearfix"></div>
				</div>
			<?php } ?>

			<?php if(strpos($value_config,',Deliverable Repeat,') !== FALSE && ($_GET['mode'] == 'new_ticket' || !($ticketid > 0)) && $field_sort_field == 'Deliverable Repeat') { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Number of times to repeat this <?= TICKET_NOUN ?>:<br /><em>This is how many additional <?= TICKET_TILE ?> will be created with the details from this <?= TICKET_NOUN ?>.</em></label>
					<div class="col-sm-8">
						<input type="number" min=0 step=1 value=0 name="recurrence" class="form-control">
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Repeat Frequency:</label>
					<div class="col-sm-8">
						<select class="chosen-select-deselect" name="recur_frequency"><option></option>
							<option value="daily">Daily</option>
							<option value="weekly">Weekly</option>
							<option value="monthly">Monthly</option>
							<option value="quarterly">Quarterly</option>
						</select>
					</div>
				</div>
				<div class="form-group recur_days" style="display:none;">
					<label class="col-sm-4 control-label">Repeat Days:</label>
					<div class="col-sm-8">
						<label class="form-checkbox"><input type="checkbox" name="recur_days" value="monday">Monday</label>
						<label class="form-checkbox"><input type="checkbox" name="recur_days" value="tuesday">Tuesday</label>
						<label class="form-checkbox"><input type="checkbox" name="recur_days" value="wednesday">Wednesday</label>
						<label class="form-checkbox"><input type="checkbox" name="recur_days" value="thursday">Thursday</label>
						<label class="form-checkbox"><input type="checkbox" name="recur_days" value="friday">Friday</label>
						<label class="form-checkbox"><input type="checkbox" name="recur_days" value="saturday">Saturday</label>
						<label class="form-checkbox"><input type="checkbox" name="recur_days" value="sunday">Sunday</label>
					</div>
				</div>
				<button class="btn brand-btn pull-right" onclick="apply_repeat(); return false;">Create Recurrence</button>
				<script>
				apply_repeat = function() {
					if(ticketid > 0 && $('[name=recurrence]').val() > 0) {
						var days = [];
						$('[name=recur_days]:checked').each(function() {
							days.push(this.value);
						});
						$.ajax({
							url: 'ticket_ajax_all.php?action=create_recurrence',
							method: 'POST',
							data: {
								ticketid: ticketid,
								number: $('[name=recurrence]').val(),
								frequency: $('[name=recur_frequency]').val(),
								recur_days: days
							},
							success: function(response) {
								if(response != '') {
									alert(response);
									console.log(response);
								} else {
									alert($('[name=recurrence]').val()+' <?= TICKET_TILE ?> have been created!');
									$('[name=recur_frequency]').val('').trigger('change.select2');
									$('[name=recur_days]').removeAttr('checked');
									$('[name=recurrence]').val(0);
								}
							}
						})
					} else if($('[name=recurrence]').val() > 0) {
						alert('No ticket details have been added!');
					} else if($('[name=recurrence]').val() > 0) {
						alert('You have not selected to repeat this any times!');
					}
				}
				</script>
				<div class="clearfix"></div>
			<?php } ?>

			<?php if(strpos($value_config,',Deliverable Internal,') !== FALSE && $field_sort_field == 'Deliverable Internal') { ?>
				<div class="email-block">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">Internal QA Date & Assign To:</label>
						<div class="col-sm-8">
							<input name="internal_qa_date" value="<?php echo $internal_qa_date; ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" type="text" class="datepicker email_date_info">
							<input name="internal_qa_start_time" id="internal_qa_start_time" value="<?php echo $internal_qa_start_time; ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" type="text" class="datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '' ?>" placeholder="Start Time">
							<input name="internal_qa_end_time" id="internal_qa_end_time" value="<?php echo $internal_qa_end_time; ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" type="text" class="datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '' ?>" placeholder="End Time">

                            <div class="internal-ticket-staff">
                            <div class="col-sm-7">

							<select data-placeholder="Select a Staff..." id="contactid" name="internal_qa_contactid[]" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control email_recipient" width="380">
							  <option value=""></option>
							  <?php foreach($staff_query as $row) { ?>
									<option <?php if (strpos($internal_qa_contactid, ','.$row['contactid'].',') !== FALSE) {
									echo " selected"; } ?> value="<?php echo $row['contactid']; ?>"><?php echo $row['first_name'].' '.$row['last_name']; ?></option>
								<?php }
							  ?>
							</select>


                            </div>

                            <div class="col-sm-2">
                                <img class="inline-img pull-right" onclick="internalTicketStaff(this);" src="../img/icons/ROOK-add-icon.png">
                                <img class="inline-img pull-right" onclick="deleteinternalTicketStaff(this);" src="../img/remove.png">
                            </div>
                            </div>

                            <div class="clearfix"></div>
							<label class="form-checkbox"><input type="checkbox" value="1" name="internal_qa_email" onclick="internal_check_send_email(this);"> Send Email</label>
						</div>
					</div>
					<?php
					$sender = get_contact($dbc, $_SESSION['contactid'], 'email_address');
					$subject = 'FFM - '.TICKET_NOUN.' assigned to you for Internal Review';
					$body = 'A ticket has been assigned to you for Internal QA on [REFERENCE].<br/><br/>
						Client: [CLIENT]<br>
						'.TICKET_NOUN.' Heading: [HEADING]<br>
						Status: [STATUS]<br>
						<a target="_blank" href="'.WEBSITE_URL.'/Ticket/index.php?edit=[TICKETID]">'.TICKET_NOUN.' #[TICKETID]</a><br/><br/><br/>
						<img src="'.WEBSITE_URL.'/img/ffm-signature.png" width="154" height="77" border="0" alt="">';
					?>
					<script>
					function internal_check_send_email(checked) {
						if(checked.checked) {
							$('.internal_email_send_div').show();
						} else {
							$('.internal_email_send_div').hide();
						}
					}
					</script>
					<div class="internal_email_send_div email_div" style="display:none;">
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Sender's Name:</label>
							<div class="col-sm-8">
								<input type="text" name="ticket_comment_email_sender_name" class="form-control email_sender_name" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Sender's Address:</label>
							<div class="col-sm-8">
								<input type="text" name="ticket_comment_email_sender" class="form-control email_sender" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Subject:</label>
							<div class="col-sm-8">
								<input type="text" name="ticket_comment_email_subject" class="form-control email_subject" value="<?php echo $subject; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Body:</label>
							<div class="col-sm-12">
								<textarea name="ticket_comment_email_body" class="form-control email_body"><?php echo $body; ?></textarea>
							</div>
						</div>
						<button class="btn brand-btn pull-right" data-table="tickets" data-id-field="ticketid" data-id="<?= $ticketid ?>" data-field="internal_qa_date" onclick="send_email(this); return false;">Send Email</button>
					</div>
					<div class="clearfix"></div>
				</div>
			<?php } ?>

			<?php if(strpos($value_config,',Deliverable Customer,') !== FALSE && $field_sort_field == 'Deliverable Customer') { ?>
				<div class="email-block">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">Customer QA/Deliverable Date & Assign To:</label>
						<div class="col-sm-8">
							<input name="deliverable_date" value="<?php echo $deliverable_date; ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" type="text" class="datepicker email_date_info">
							<input name="deliverable_start_time" id="deliverable_start_time" value="<?php echo $deliverable_start_time; ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" type="text" class="datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '' ?>" placeholder="Start Time">
							<input name="deliverable_end_time" id="deliverable_end_time" value="<?php echo $deliverable_end_time; ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" type="text" class="datetimepicker<?= $calendar_window > 0 ? '-'.$calendar_window : '' ?>" placeholder="End Time">

                            <div class="customer-ticket-staff">
                            <div class="col-sm-7">

							<select data-placeholder="Select a Staff..." id="contactid" name="deliverable_contactid[]" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control email_recipient" width="380">
							  <option value=""></option>
							  <?php foreach($staff_query as $row) {
									$selected = '';
									$selected = strpos($deliverable_contactid, ','.$row['contactid'].',') !== FALSE ? 'selected = "selected"' : '';
									echo "<option " . $selected . "value='". $row['contactid']."'>".$row['first_name'].' '.$row['last_name'].'</option>';
								}
							  ?>
							</select>
                            </div>

                            <div class="col-sm-2">
                                <img class="inline-img pull-right" onclick="customerTicketStaff(this);" src="../img/icons/ROOK-add-icon.png">
                                <img class="inline-img pull-right" onclick="deletecustomerTicketStaff(this);" src="../img/remove.png">
                            </div>
                            </div>
                            <div class="clearfix"></div>
							<label class="form-checkbox"><input type="checkbox" value="1" name="client_qa_email" onclick="client_check_send_email(this);">Send Email</label>
						</div>
					</div>
					<?php
					$sender = get_contact($dbc, $_SESSION['contactid'], 'email_address');
					$subject = 'FFM - '.TICKET_NOUN.' assigned to you for Customer QA';
					$body = 'A ticket has been assigned to you for Customer QA on [REFERENCE].<br/><br/>
						Client: [CLIENT]<br>
						'.TICKET_NOUN.' Heading: [HEADING]<br>
						Status: [STATUS]<br>
						<a target="_blank" href="'.WEBSITE_URL.'/Ticket/index.php?edit=[TICKETID]">'.TICKET_NOUN.' #[TICKETID]</a><br/><br/><br/>
						<img src="'.WEBSITE_URL.'/img/ffm-signature.png" width="154" height="77" border="0" alt="">';
					?>
					<script>
					function client_check_send_email(checked) {
						if(checked.checked) {
							$('.client_email_send_div').show();
						} else {
							$('.client_email_send_div').hide();
						}
					}
					</script>
					<div class="client_email_send_div email_div" style="display:none;">
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Sender's Name:</label>
							<div class="col-sm-8">
								<input type="text" name="ticket_comment_email_sender_name" class="form-control email_sender_name" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Sender's Address:</label>
							<div class="col-sm-8">
								<input type="text" name="ticket_comment_email_sender" class="form-control email_sender" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Subject:</label>
							<div class="col-sm-8">
								<input type="text" name="ticket_comment_email_subject" class="form-control email_subject" value="<?php echo $subject; ?>">
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4 control-label">Email Body:</label>
							<div class="col-sm-12">
								<textarea name="ticket_comment_email_body" class="form-control email_body"><?php echo $body; ?></textarea>
							</div>
						</div>
						<button class="btn brand-btn pull-right" data-table="tickets" data-id-field="ticketid" data-id="<?= $ticketid ?>" data-field="deliverable_date" onclick="send_email(this); return false;">Send Email</button>
					</div>
					<div class="clearfix"></div>
				</div>
			<?php } ?>
		<?php } ?>

	</div>
<?php } else { ?>
	<div class="col-md-12">
		<?php foreach ($field_sort_order as $field_sort_field) { ?>
			<?php if(strpos($value_config,',Deliverable Status,') !== FALSE && $field_sort_field == 'Deliverable Status') { ?>
				<div class="form-group">
					<label for="site_name" class="col-sm-4 control-label">Status:</label>
					<div class="col-sm-8"><?= $status ?></div>
				</div>
				<?php $pdf_contents[] = ['Status', $status]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Deliverable To Do,') !== FALSE && $field_sort_field == 'Deliverable To Do') { ?>
				<div class="email-block">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">Start Date, End Date & Assign To:</label>
						<div class="col-sm-8">
							<?php echo $to_do_date; ?>
							<?php echo $to_do_start_time; ?>
							<?php echo $to_do_end_date; ?>
							<?php echo $to_do_end_time; ?>
							<?php $pdf_content_staff = []; ?>
							<?php foreach(explode(',',$contactid) as $contact) {
								if($contact > 0) {
									$pdf_content_staff[] = get_contact($dbc, $contact);
									echo get_contact($dbc, $contact)."<br />";
								}
							} ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<?php $pdf_contents[] = ['Start Date', $to_do_date]; ?>
				<?php $pdf_contents[] = ['Start Time', $to_do_start_time]; ?>
				<?php $pdf_contents[] = ['End Date', $to_do_end_date]; ?>
				<?php $pdf_contents[] = ['End Time', $to_do_end_time]; ?>
				<?php $pdf_contents[] = ['Staff', implode('<br>',$pdf_content_staff)]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Deliverable Internal,') !== FALSE && $field_sort_field == 'Deliverable Internal') { ?>
				<div class="email-block">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">Internal QA Date & Assign To:</label>
						<div class="col-sm-8">
							<?php echo $internal_qa_date; ?>
							<?php echo $internal_qa_start_time; ?>
							<?php echo $internal_qa_end_time; ?>
							<?php $pdf_content_staff = []; ?>
							<?php foreach(explode(',',$internal_qa_contactid) as $contact) {
								if($contact > 0) {
									$pdf_content_staff[] = get_contact($dbc, $contact);
									echo get_contact($dbc, $contact)."<br />";
								}
							} ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<?php $pdf_contents[] = ['Internal QA Date', $internal_qa_date]; ?>
				<?php $pdf_contents[] = ['Internal QA Start Time', $internal_qa_start_time]; ?>
				<?php $pdf_contents[] = ['Internal QA End Time', $internal_qa_end_time]; ?>
				<?php $pdf_contents[] = ['Internal QA Staff', implode('<br>',$pdf_content_staff)]; ?>
			<?php } ?>
			<?php if(strpos($value_config,',Deliverable Customer,') !== FALSE && $field_sort_field == 'Deliverable Customer') { ?>
				<div class="email-block">
					<div class="form-group clearfix">
						<label for="first_name" class="col-sm-4 control-label text-right">Internal QA Date & Assign To:</label>
						<div class="col-sm-8">
							<?php echo $deliverable_date; ?>
							<?php echo $deliverable_start_time; ?>
							<?php echo $deliverable_end_time; ?>
							<?php $pdf_content_staff = []; ?>
							<?php foreach(explode(',',$deliverable_contactid) as $contact) {
								if($contact > 0) {
									$pdf_content_staff[] = get_contact($dbc, $contact);
									echo get_contact($dbc, $contact)."<br />";
								}
							} ?>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
				<?php $pdf_contents[] = ['Customer QA Date', $deliverable_date]; ?>
				<?php $pdf_contents[] = ['Customer QA Start Time', $deliverable_start_time]; ?>
				<?php $pdf_contents[] = ['Customer QA End Time', $deliverable_end_time]; ?>
				<?php $pdf_contents[] = ['Customer QA Staff', implode('<br>',$pdf_content_staff)]; ?>
			<?php } ?>
		<?php } ?>
	</div>
<?php } ?>