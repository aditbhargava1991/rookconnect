<?php
if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) {
	if(in_array('TEMPLATE Work Ticket', $all_config) || in_array('TEMPLATE Work Ticket', $value_config)) {
		$value_config = ['TEMPLATE Work Ticket'];
		$all_config = ['Information','PI Business','PI Name','PI Project','PI AFE','PI Sites','Staff','Staff Position','Staff Hours','Staff Overtime','Staff Travel','Staff Subsistence','Services','Service Category','Equipment','Materials','Material Quantity','Material Rates','Purchase Orders','Notes'];
	}
	if(in_array('Documents',$value_config) && !in_array('Documents Docs',$value_config) && !in_array('Documents Links',$value_config)) {
		$value_config[] = 'Documents Docs';
		$value_config[] = 'Documents Links';
	} ?>

	<div class="form-group">
		<h4 class="double-gap-top"><?= TICKET_NOUN ?> Functionality</h4>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("TEMPLATE Work Ticket", $all_config) ? 'checked disabled' : (in_array("TEMPLATE Work Ticket", $value_config) ? "checked" : '') ?> value="TEMPLATE Work Ticket" name="tickets[]" onchange="if(this.checked) { $('[type=checkbox]').not(this).removeAttr('checked'); }">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This is a specific set of fields that will apply."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Use Work Ticket Template</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Hide New Ticketid", $all_config) ? 'checked disabled' : (in_array("Hide New Ticketid", $value_config) ? "checked" : '') ?> value="Hide New Ticketid" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This is to prevent the label from displaying while editing a new <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Hide New <?= TICKET_NOUN ?> Label</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Send Emails", $all_config) ? 'checked disabled' : (in_array("Send Emails", $value_config) ? "checked" : '') ?> value="Send Emails" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This allows users to send notes they add as an email to the assigned user."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Send Notes as Emails</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Tag Notes", $all_config) ? 'checked disabled' : (in_array("Tag Notes", $value_config) ? "checked" : '') ?> value="Tag Notes" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This allows users to assign notes to another user."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Assign Notes to Users</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Additional", $all_config) ? 'checked disabled' : (in_array("Additional", $value_config) ? "checked" : '') ?> value="Additional" name="tickets[]"> Add Additional <?= TICKET_TILE ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Multiple", $all_config) ? 'checked disabled' : (in_array("Multiple", $value_config) ? "checked" : '') ?> value="Multiple" name="tickets[]"> Create Multiple <?= TICKET_TILE ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Export Ticket Log", $all_config) ? 'checked disabled' : (in_array("Export Ticket Log", $value_config) ? "checked" : '') ?> value="Export Ticket Log" name="tickets[]"> Export <?= TICKET_NOUN ?> Log</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Send Archive Email", $all_config) ? 'checked disabled' : (in_array("Send Archive Email", $value_config) ? "checked" : '') ?> value="Send Archive Email" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will result in an email being sent when the <?= TICKET_NOUN ?> is set to the status chosen as complete."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Send Email When Archived</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Ticket Edit Cutoff", $all_config) ? 'checked disabled' : (in_array("Ticket Edit Cutoff", $value_config) ? "checked" : '') ?> value="Ticket Edit Cutoff" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Prevent <?= TICKET_TILE ?> from being edited after the scheduled date."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Cut Off Editing <?= TICKET_TILE ?>: Daily</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Quick Reminder Button", $all_config) ? 'checked disabled' : (in_array("Quick Reminder Button", $value_config) ? "checked" : '') ?> value="Quick Reminder Button" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display a button to send a reminder email for the current <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Quick Reminder Button</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Business Set Delivery", $all_config) ? 'checked disabled' : (in_array("Business Set Delivery", $value_config) ? "checked" : '') ?> value="Business Set Delivery" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="If this is enabled, the first delivery address or origin address will be populated from the <?= BUSINESS_CAT ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Set <?= BUSINESS_CAT ?> as First Delivery / Origin</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Force All Caps", $all_config) ? 'checked disabled' : (in_array("Force All Caps", $value_config) ? "checked" : '') ?> value="Force All Caps" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="If this is enabled, all fields will save as capitalized."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Force All Caps for Fields</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Edit Section Options", $all_config) ? 'checked disabled' : (in_array("Edit Section Options", $value_config) ? "checked" : '') ?> value="Edit Section Options" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="If this is enabled, users with access to the tile can be granted access per section."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Allow Editing for All Users by Section</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Finish Check Out Require Signature", $all_config) ? 'checked disabled' : (in_array("Finish Check Out Require Signature", $value_config) ? "checked" : '') ?> value="Finish Check Out Require Signature" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="If this is enabled, the Finish button to check out all staff requires a Signature."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Finish Button Requires Signature</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Finish Create Recurring Ticket", $all_config) ? 'checked disabled' : (in_array("Finish Create Recurring Ticket", $value_config) ? "checked" : '') ?> value="Finish Create Recurring Ticket" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="If this is enabled, the Finish button will create a Recurring ticket if it has a status of the Recurring status set below."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Finish Button Creates Recurring <?= TICKET_NOUN ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Finish Button Hide", $all_config) ? 'checked disabled' : (in_array("Finish Button Hide", $value_config) ? "checked" : '') ?> value="Finish Button Hide" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="If this is enabled, the Finish button will be hidden, and you will only have the Save Button."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Hide Finish Button</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Delete Button Add Note", $all_config) ? 'checked disabled' : (in_array("Delete Button Add Note", $value_config) ? "checked" : '') ?> value="Delete Button Add Note" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="If this is enabled, the Delete button will prompt the user to add a Note."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Delete Button Add Note</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Create Recurrence Button", $all_config) ? 'checked disabled' : (in_array("Create Recurrence Button", $value_config) ? "checked" : '') ?> value="Create Recurrence Button" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="If this is enabled, the Create Recurrence button will allow creating Recurrences of the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Create Recurrence Button</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Check Out Before Check In", $all_config) ? 'checked disabled' : (in_array("Check Out Before Check In", $value_config) ? "checked" : '') ?> value="Check Out Before Check In" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="If this is enabled, the user must Check Out from the Check Out section before allowing Checking In again."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Must Check Out Before Checking In Again</label>
		<div class="form-group">
			<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This setting will dictate how far ahead the software will create Recurring <?= TICKET_TILE ?> (eg. visible in the software) that are ongoing, and will continue creating <?= TICKET_TILE ?> on an ongoing basis up to the selected amount here. This is required as the software cannot create an infinite number of <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Ongoing Recurring <?= TICKET_TILE ?> Sync Up To:</label>
			<div class="col-sm-8">
				<?php $ticket_recurrence_sync_upto = !empty(get_config($dbc, 'ticket_recurrence_sync_upto')) ? get_config($dbc, 'ticket_recurrence_sync_upto') : '2 years'; ?>
				<select name="ticket_recurrence_sync_upto" class="chosen-select-deselect form-control">
					<?php for($i = 1; $i <= 11; $i++) {
						echo '<option value="'.$i.' months" '.($ticket_recurrence_sync_upto == $i.' months' ? 'selected' : '').'>'.$i.' Month'.($i > 1 ? 's' : '').'</option>';
					}
					for($i = 1; $i <= 5; $i++) {
						echo '<option value="'.$i.' years" '.($ticket_recurrence_sync_upto == $i.' years' ? 'selected' : '').'>'.$i.' Year'.($i > 1 ? 's' : '').'</option>';
					} ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Labels for Multiple <?= TICKET_TILE ?>:<br /><em>Separate labels with a comma. Each successive <?= TICKET_NOUN ?> will use the next label.</em></label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="ticket_multiple_labels" value="<?= get_config($dbc, "ticket_multiple_labels") ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Increments for Hour Fields:<br /><em>Enter the number of minutes (10, 15, 20, 30, any) that the hour inputs should increment by.</em></label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="ticket_hour_increments" value="<?= get_config($dbc, "ticket_hour_increments") ?>">
			</div>
		</div>
		<div class="form-group">
			<?php $ticket_invoice_type = get_config($dbc, 'ticket_invoice_type');
            $invoice_types = [];
            foreach(explode(',',get_config($dbc,'invoice_types')) as $invoice_type) {
                $invoice_types[config_safe_str($invoice_type)] = $invoice_type;
            } ?>
			<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will specify the type of Invoice that will be used for this <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>New Invoice Type
				<?= $ticket_invoice_type != '' && $tab != '' ? '(Default: '.$invoice_types[$ticket_invoice_type].')' : '' ?>:</label>
			<div class="col-sm-8">
				<select name="ticket_invoice_type<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Tab" class="chosen-select-deselect"><option />
					<?php $tab_ticket_invoice_type = $tab == '' ? $ticket_invoice_type : get_config($dbc, 'ticket_invoice_type_'.$tab);
                    foreach($invoice_types as $inv_type_id => $inv_type_name) { ?>
                        <option <?= $inv_type_id == $tab_ticket_invoice_type ? 'selected' : '' ?> value="<?= $inv_type_id ?>"><?= $inv_type_name ?></option>
                    <?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This is the status that will be used to indicate completed <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Status for a Completed <?= TICKET_NOUN ?>:</label>
			<div class="col-sm-8">
				<?php $auto_archive_complete_tickets = get_config($dbc, 'auto_archive_complete_tickets'); ?>
				<select name="auto_archive_complete_tickets" class="chosen-select-deselect" data-placeholder="Select Status">
					<option <?= $auto_archive_complete_tickets == '' ? 'selected' : '' ?> value="">Do not set the status when it has been completed.</option>
					<?php foreach(explode(',',get_config($dbc, 'ticket_status')) as $status) { ?>
						<option <?= $status == $auto_archive_complete_tickets ? 'selected' : '' ?> value="<?= $status ?>"><?= $status ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<?php $incomplete_ticket_status = get_config($dbc, 'incomplete_ticket_status'); ?>
			<label class="col-sm-4 control-label">Status for a <?= TICKET_NOUN ?> that is missing required fields<?= $incomplete_ticket_status != '' && $tab != '' ? ' (Default: '.$incomplete_ticket_status.')' : '' ?>:</label>
			<div class="col-sm-8">
				<select name="incomplete_ticket_status<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Status" class="chosen-select-deselect"><option></option>
					<?php $tab_incomplete_ticket_status = get_config($dbc, 'incomplete_ticket_status'.($tab == '' ? '' : '_'.$tab));
					foreach(explode(',',get_config($dbc, 'ticket_status')) as $status) { ?>
						<option <?= $status == $tab_incomplete_ticket_status ? 'selected' : '' ?> value="<?= $status ?>"><?= $status ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<?php $ticket_default_status = get_config($dbc, 'ticket_default_status'); ?>
			<label class="col-sm-4 control-label">Default status for a new <?= TICKET_NOUN ?><?= $ticket_default_status != '' && $tab != '' ? ' (Default: '.$ticket_default_status.')' : '' ?>:</label>
			<div class="col-sm-8">
				<select name="ticket_default_status<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Status" class="chosen-select-deselect"><option></option>
					<?php $tab_ticket_default_status = get_config($dbc, 'ticket_default_status'.($tab == '' ? '' : '_'.$tab));
					foreach(explode(',',get_config($dbc, 'ticket_status')) as $status) { ?>
						<option <?= $status == $tab_ticket_default_status ? 'selected' : '' ?> value="<?= $status ?>"><?= $status ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<?php $rate_card_contact = get_config($dbc, 'rate_card_contact'); ?>
			<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will specify the contact for which the rate card will pull."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Preferred Customer Rate Card Contact Field
				<?= $rate_card_contact != '' && $tab != '' ? '(Default: '.($rate_card_contact == 'businessid' ? BUSINESS_CAT : ($rate_card_contact == 'agentid' ? 'Additional Contact' : ($rate_card_contact == 'origin:vendor' ? 'Origin - Contact' : ($rate_card_contact == 'destination:vendor' ? 'Destination - Contact' : ($rate_card_contact == 'origin:carrier' ? 'Transport Carrier' : ''))))).')' : '' ?>:</label>
			<div class="col-sm-8">
				<select name="rate_card_contact<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Tab" class="chosen-select-deselect"><option />
					<?php $tab_rate_card_contact = $tab == '' ? $rate_card_contact : get_config($dbc, 'rate_card_contact_'.$tab); ?>
					<option <?= 'businessid' == $tab_rate_card_contact ? 'selected' : '' ?> value="businessid"><?= BUSINESS_CAT ?></option>
					<option <?= 'agentid' == $tab_rate_card_contact ? 'selected' : '' ?> value="agentid">Additional Contact</option>
					<option <?= 'origin:vendor' == $tab_rate_card_contact ? 'selected' : '' ?> value="origin:vendor">Origin - Contact</option>
					<option <?= 'destination:vendor' == $tab_rate_card_contact ? 'selected' : '' ?> value="destination:vendor">Destination - Contact</option>
					<option <?= 'origin:carrier' == $tab_rate_card_contact ? 'selected' : '' ?> value="origin:carrier">Transport Carrier</option>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="If Finish Button Creates Recurring <?= TICKET_NOUN ?> is enabled, this status will indicate that the <?= TICKET_NOUN ?> is a Rccurring ticket."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Status for a Recurring <?= TICKET_NOUN ?>:</label>
			<div class="col-sm-8">
				<?php $ticket_recurring_status = get_config($dbc, 'ticket_recurring_status'); ?>
				<select name="ticket_recurring_status" class="chosen-select-deselect" data-placeholder="Select Status">
					<option <?= $ticket_recurring_status == '' ? 'selected' : '' ?> value="">Do not set the status when it has been completed.</option>
					<?php foreach(explode(',',get_config($dbc, 'ticket_status')) as $status) { ?>
						<option <?= $status == $ticket_recurring_status ? 'selected' : '' ?> value="<?= $status ?>"><?= $status ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
		<label class="form-checkbox any-width"><input type="checkbox" <?= in_array("Email Creator", $all_config) ? 'checked disabled' : (in_array("Email Creator", $value_config) ? "checked" : '') ?> value="Email Creator" name="tickets[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Send an email to the user that created the <?= TICKET_NOUN ?> when it is created."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Send Email to Creator of new <?= TICKET_NOUN ?></label>
		<div class="form-group">
			<?php $ticket_new_email_subject = get_config($dbc, 'ticket_new_email_subject'); ?>
			<label class="col-sm-4 control-label">Subject for Email for new <?= TICKET_TILE ?><?= $ticket_new_email_subject != '' && $tab != '' ? ' (Default: '.$ticket_new_email_subject.')' : '' ?>:<br /><em>You can add [TICKET] to have the <?= TICKET_NOUN ?> label populated into the subject.</em></label>
			<div class="col-sm-8">
				<input type="text" name="ticket_new_email_subject<?= $tab == '' ? '' : '_'.$tab ?>" placeholder="New Email Subject" class="form-control" value="<?= get_config($dbc, 'ticket_new_email_subject'.($tab == '' ? '' : '_'.$tab)) ?>">
			</div>
		</div>
		<div class="form-group">
			<?php $ticket_new_email_body = get_config($dbc, 'ticket_new_email_body'); ?>
			<label class="col-sm-4 control-label">Body for Email for new <?= TICKET_TILE ?><?= $ticket_new_email_body != '' && $tab != '' ? ' (Default set for all '.TICKET_TILE.')' : '' ?>:<br /><em>You can add [TICKET] to have the <?= TICKET_NOUN ?> label populated into the body.</em></label>
			<div class="col-sm-8">
				<textarea name="ticket_new_email_body<?= $tab == '' ? '' : '_'.$tab ?>" placeholder="New Email Subject" class="form-control"><?= get_config($dbc, 'ticket_new_email_body'.($tab == '' ? '' : '_'.$tab)) ?></textarea>
			</div>
		</div>
	</div>
<?php } else { ?>
	<div class="form-group">
		<h4 class="double-gap-top"><?= TICKET_NOUN ?> Functionality</h4>
		<?php if(in_array('Multiple', $merged_config_fields)) { ?>
			<label class="form-checkbox"><input type="checkbox" <?= in_array("Multiple", $all_config) ? 'checked disabled' : (in_array("Multiple", $value_config) ? "checked" : '') ?> value="Multiple" name="tickets[]"> Create Multiple <?= TICKET_TILE ?></label>
		<?php } ?>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Hide Trash Icon", $all_config) ? 'checked disabled' : (in_array("Hide Trash Icon", $value_config) ? "checked" : '') ?> value="Hide Trash Icon" name="tickets[]"> Hide Trash Icon</label>
	</div>
<?php } ?>

<h4 class="double-gap-top"><?= TICKET_NOUN ?><?= $action_mode ? ' Action Mode' : ($overview_mode ? ' Overview' : ($unlock_mode ? ' Unlocked' : ($status_fields ? 'Status' : ($intake_mode ? ' Intake' : ($estimate_mode ? ' '.ESTIMATE_TILE : ''))))) ?> Fields</h4>

<div class="accordions_sortable">
	<?php $current_heading = '';
	$current_heading_closed = true;
	$sort_order = array_filter($sort_order);
	foreach ($sort_order as $sort_field) {
		//Add higher level heading
		$this_heading = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_headings` WHERE `ticket_type` = '".(empty($tab) ? 'tickets' : 'tickets_'.$tab)."' AND `accordion` = '".$sort_field."'"))['heading'];
		if($this_heading != $current_heading) {
			if(!$current_heading_closed) { ?>
					</div>
				</div>
				<?php $current_heading_closed = true;
			}
			if(!empty($this_heading)) { ?>
				<div class="sort_order_heading sort_order_accordion">
					<div class="sort_order_heading_name">
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?><img src="../img/remove.png" class="inline-img" onclick="removeHigherLevelHeading(this);"><?php } ?><label class="control-label">Heading: </label><input type="text" name="sort_order_heading[]" value="<?= $this_heading ?>" class="inline form-control gap-left" onchange="updateHigherLevelHeading(this);" onfocusin="$(this).data('oldvalue', $(this).val());" <?php if($action_mode || $estimate_mode || $status_fields || $overview_mode || $unlock_mode || $intake_mode) { echo 'disabled'; } ?>>
					</div>
					<div class="block-group sort_order_heading_block">
				<?php $current_heading_closed = false;
				$current_heading = $this_heading;
			}
		}

		//Custom accordions
		if(substr($sort_field, 0, strlen('FFMCUST_')) === 'FFMCUST_') {
			include('../Ticket/field_config_field_list_custom.php');
	 	}

		$field_list = $accordion_list[$sort_field];
		$field_sort_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_fields` WHERE `ticket_type` = '".(empty($tab) ? 'tickets' : 'tickets_'.$tab)."' AND `accordion` = '".$sort_field."'"))['fields'];
		$field_sort_order = explode(',', $field_sort_order);
		foreach ($field_list as $default_field) {
			if(!in_array($default_field, $field_sort_order)) {
				$field_sort_order[] = $default_field;
			}
		}

		if($action_mode || $estimate_mode || $status_fields || $overview_mode || $intake_mode) {
			$field_sort_order = array_intersect($field_sort_order, array_merge($all_config_fields,$value_config_fields));
		} else if($unlock_mode) {
			$field_sort_order = $sort_order;
		}

		//Renamed accordions
		$renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($tab) ? 'tickets' : 'tickets_'.$tab)."' AND `accordion` = '".$sort_field."'"))['accordion_name'];

		if($sort_field == 'Customer History') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Customer History">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Customer History Button' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('project_info',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('project_info',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="project_info" data-toggle="<?= in_array('project_info',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('project_info',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('project_info',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Customer History Button' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">

					<label class="form-checkbox"><input type="checkbox" <?= in_array("Customer History", $all_config) ? 'checked disabled' : (in_array("Customer History", $value_config) ? "checked" : '') ?> value="Customer History" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Customer History Business Ticket Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer History Business Ticket Type", $all_config) ? 'checked disabled' : (in_array("Customer History Business Ticket Type", $value_config) ? "checked" : '') ?> value="Customer History Business Ticket Type" name="tickets[]"> Business - Last 5 by <?= TICKET_NOUN ?> Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer History Business Project Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer History Business Project Type", $all_config) ? 'checked disabled' : (in_array("Customer History Business Project Type", $value_config) ? "checked" : '') ?> value="Customer History Business Project Type" name="tickets[]"> Business - Last 5 by <?= PROJECT_NOUN ?> Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer History Business Ticket Project Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer History Business Ticket Project Type", $all_config) ? 'checked disabled' : (in_array("Customer History Business Ticket Project Type", $value_config) ? "checked" : '') ?> value="Customer History Business Ticket Project Type" name="tickets[]"> Business - Last 5 by <?= PROJECT_NOUN ?> Tab and <?= TICKET_NOUN ?> Type</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer History Customer Ticket Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer History Customer Ticket Type", $all_config) ? 'checked disabled' : (in_array("Customer History Customer Ticket Type", $value_config) ? "checked" : '') ?> value="Customer History Customer Ticket Type" name="tickets[]"> Customer - Last 5 by <?= TICKET_NOUN ?> Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer History Customer Project Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer History Customer Project Type", $all_config) ? 'checked disabled' : (in_array("Customer History Customer Project Type", $value_config) ? "checked" : '') ?> value="Customer History Customer Project Type" name="tickets[]"> Customer - Last 5 by <?= PROJECT_NOUN ?> Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer History Customer Ticket Project Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer History Customer Ticket Project Type", $all_config) ? 'checked disabled' : (in_array("Customer History Customer Ticket Project Type", $value_config) ? "checked" : '') ?> value="Customer History Customer Ticket Project Type" name="tickets[]"> Customer - Last 5 by <?= PROJECT_NOUN ?> Tab and <?= TICKET_NOUN ?> Type</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer History Field Display Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer History Field Display Notes", $all_config) ? 'checked disabled' : (in_array("Customer History Field Display Notes", $value_config) ? "checked" : '') ?> value="Customer History Field Display Notes" name="tickets[]"> Display Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer History Field Service Template') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer History Field Service Template", $all_config) ? 'checked disabled' : (in_array("Customer History Field Service Template", $value_config) ? "checked" : '') ?> value="Customer History Field Service Template" name="tickets[]"> Display Service Template</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Information') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Information">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : PROJECT_NOUN.' Information' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('project_info',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('project_info',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="project_info" data-toggle="<?= in_array('project_info',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('project_info',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('project_info',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : PROJECT_NOUN.' Information' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Information", $all_config) ? 'checked disabled' : (in_array("Information", $value_config) ? "checked" : '') ?> value="Information" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'PI Business') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Business", $all_config) ? 'checked disabled' : (in_array("PI Business", $value_config) ? "checked" : '') ?> value="PI Business" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= BUSINESS_CAT ?></label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Name') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Name", $all_config) ? 'checked disabled' : (in_array("PI Name", $value_config) ? "checked" : '') ?> value="PI Name" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= CONTACTS_TILE ?>, excluding <?= BUSINESS_CAT ?> or <?= CONTACTS_TILE ?> attached to the selected <?= BUSINESS_CAT ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Contact</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Guardian') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Guardian", $all_config) ? 'checked disabled' : (in_array("PI Guardian", $value_config) ? "checked" : '') ?> value="PI Guardian" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= CONTACTS_TILE ?> based on the selected tab below, which is the Parent/Guardian of the Main Contact."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Parent/Guardian</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI AFE') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI AFE", $all_config) ? 'checked disabled' : (in_array("PI AFE", $value_config) ? "checked" : '') ?> value="PI AFE" name="tickets[]"> AFE #</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Project') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Project", $all_config) ? 'checked disabled' : (in_array("PI Project", $value_config) ? "checked" : '') ?> value="PI Project" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will display a list of <?= PROJECT_TILE ?> for the selected <?= BUSINESS_CAT ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= PROJECT_NOUN ?></label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Pieces') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Pieces", $all_config) ? 'checked disabled' : (in_array("PI Pieces", $value_config) ? "checked" : '') ?> value="PI Pieces" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This field is an alternative to attaching <?= PROJECT_TILE ?>, and allows you to enter a description."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Piece Work</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Sites') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Sites", $all_config) ? 'checked disabled' : (in_array("PI Sites", $value_config) ? "checked" : '') ?> value="PI Sites" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= SITES_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= SITES_CAT ?></label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Rate Card') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Rate Card", $all_config) ? 'checked disabled' : (in_array("PI Rate Card", $value_config) ? "checked" : '') ?> value="PI Rate Card" name="tickets[]"> Rate Card</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Customer Order') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Customer Order", $all_config) ? 'checked disabled' : (in_array("PI Customer Order", $value_config) ? "checked" : '') ?> value="PI Customer Order" name="tickets[]"> Customer Order #</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Customer Order Slider') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Customer Order Slider", $all_config) ? 'checked disabled' : (in_array("PI Customer Order Slider", $value_config) ? "checked" : '') ?> value="PI Customer Order Slider" name="tickets[]"> Slider Icon for Customer Order #</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Sales Order') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Sales Order", $all_config) ? 'checked disabled' : (in_array("PI Sales Order", $value_config) ? "checked" : '') ?> value="PI Sales Order" name="tickets[]"> <?= SALES_ORDER_NOUN ?> Invoice #</label>
							<?php } ?>
							<?php if($field_sort_field == '"PI Invoice') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Invoice", $all_config) ? 'checked disabled' : (in_array("PI Invoice", $value_config) ? "checked" : '') ?> value="PI Invoice" name="tickets[]"> Invoice # (Text Input)</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Order') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Order", $all_config) ? 'checked disabled' : (in_array("PI Order", $value_config) ? "checked" : '') ?> value="PI Order" name="tickets[]"> Order #</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Purchase Order') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Purchase Order", $all_config) ? 'checked disabled' : (in_array("PI Purchase Order", $value_config) ? "checked" : '') ?> value="PI Purchase Order" name="tickets[]"> Purchase Order #</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Purchase Order Slider') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Purchase Order Slider", $all_config) ? 'checked disabled' : (in_array("PI Purchase Order Slider", $value_config) ? "checked" : '') ?> value="PI Purchase Order Slider" name="tickets[]"> Slider Icon for Purchase Order #</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI WTS Order') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI WTS Order", $all_config) ? 'checked disabled' : (in_array("PI WTS Order", $value_config) ? "checked" : '') ?> value="PI WTS Order" name="tickets[]"> WTS Order #</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Cross Ref') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Cross Ref", $all_config) ? 'checked disabled' : (in_array("PI Cross Ref", $value_config) ? "checked" : '') ?> value="PI Cross Ref" name="tickets[]"> Cross Reference</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Invoiced Out') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Invoiced Out", $all_config) ? 'checked disabled' : (in_array("PI Invoiced Out", $value_config) ? "checked" : '') ?> value="PI Invoiced Out" name="tickets[]"> Invoiced (Y/N)</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Work Order') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Work Order", $all_config) ? 'checked disabled' : (in_array("PI Work Order", $value_config) ? "checked" : '') ?> value="PI Work Order" name="tickets[]"> Work Order #</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Scheduled Date') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Scheduled Date", $all_config) ? 'checked disabled' : (in_array("PI Scheduled Date", $value_config) ? "checked" : '') ?> value="PI Scheduled Date" name="tickets[]"> Scheduled Date</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Date of Entry') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Date of Entry", $all_config) ? 'checked disabled' : (in_array("PI Date of Entry", $value_config) ? "checked" : '') ?> value="PI Date of Entry" name="tickets[]"> Date of Entry</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Time of Entry') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Time of Entry", $all_config) ? 'checked disabled' : (in_array("PI Time of Entry", $value_config) ? "checked" : '') ?> value="PI Time of Entry" name="tickets[]"> Time of Entry</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Agent') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Agent", $all_config) ? 'checked disabled' : (in_array("PI Agent", $value_config) ? "checked" : '') ?> value="PI Agent" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Additional Contact</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Notify Party') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Notify Party", $all_config) ? 'checked disabled' : (in_array("PI Notify Party", $value_config) ? "checked" : '') ?> value="PI Notify Party" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Notify Party</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Status') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Status", $all_config) ? 'checked disabled' : (in_array("PI Status", $value_config) ? "checked" : '') ?> value="PI Status" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of statuses to assign to the current <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Status</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Ban') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Ban", $all_config) ? 'checked disabled' : (in_array("PI Ban", $value_config) ? "checked" : '') ?> value="PI Ban" name="tickets[]"> Ban</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Vendor') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Vendor", $all_config) ? 'checked disabled' : (in_array("PI Vendor", $value_config) ? "checked" : '') ?> value="PI Vendor" name="tickets[]"> Vendor</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Operator') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Operator", $all_config) ? 'checked disabled' : (in_array("PI Operator", $value_config) ? "checked" : '') ?> value="PI Operator" name="tickets[]"> Operator</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Waste Manifest') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Waste Manifest", $all_config) ? 'checked disabled' : (in_array("PI Waste Manifest", $value_config) ? "checked" : '') ?> value="PI Waste Manifest" name="tickets[]"> Waste Manifest #</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI Reference Ticket') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI Reference Ticket", $all_config) ? 'checked disabled' : (in_array("PI Reference Ticket", $value_config) ? "checked" : '') ?> value="PI Reference Ticket" name="tickets[]"> Reference Ticket #</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI TDG Doc Num') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI TDG Doc Num", $all_config) ? 'checked disabled' : (in_array("PI TDG Doc Num", $value_config) ? "checked" : '') ?> value="PI TDG Doc Num" name="tickets[]"> TDG Doc. #</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI VTI Num') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI VTI Num", $all_config) ? 'checked disabled' : (in_array("PI VTI Num", $value_config) ? "checked" : '') ?> value="PI VTI Num" name="tickets[]"> VTI #</label>
							<?php } ?>
							<?php if($field_sort_field == 'PI TEXT FIELD') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PI TEXT FIELD", $all_config) ? 'checked disabled' : (in_array("PI TEXT FIELD", $value_config) ? "checked" : '') ?> value="PI TEXT FIELD" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to create custom details for the current <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Custom Field</label>
							<?php } ?>
						<?php } ?>
						</div>
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<div class="form-group">
								<?php $ticket_business_contact = get_config($dbc, 'ticket_business_contact'); ?>
								<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will specify a tab for the <?= CONTACTS_TILE ?> in the Contact list."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Tab for Main Contact<?= $ticket_business_contact != '' && $tab != '' ? ' (Default: '.$ticket_business_contact.')' : '' ?>:</label>
								<div class="col-sm-8">
									<select name="ticket_business_contact<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Tab" class="chosen-select-deselect"><option></option>
										<?php $tab_ticket_business_contact = get_config($dbc, 'ticket_business_contact'.($tab == '' ? '' : '_'.$tab));
										foreach(explode(',',get_config($dbc, 'all_contact_tabs')) as $category) { ?>
											<option <?= $category == $tab_ticket_business_contact ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<?php $ticket_business_contact_add_pos = get_config($dbc, 'ticket_business_contact_add_pos'); ?>
								<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will specify the position of the Add New Contact option in the dropdown for the Main Contact."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Add New Main Contact Position In Dropdown:</label>
								<div class="col-sm-8">
									<select name="ticket_business_contact_add_pos" data-placeholder="Select Tab" class="chosen-select-deselect">
										<option <?= $ticket_business_contact_add_pos != 'top' ? 'selected' : ''?>>Bottom</option>
										<option value="top" <?= $ticket_business_contact_add_pos == 'top' ? 'selected' : '' ?>>Top</option>
									</select>
								</div>
							</div>
							<div class="form-group">
								<?php $ticket_guardian_contact = get_config($dbc, 'ticket_guardian_contact'); ?>
								<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will specify a tab for the Parent/Guardian in the Contact list."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Tab for Parent/Guardian<?= $ticket_guardian_contact != '' && $tab != '' ? ' (Default: '.$ticket_guardian_contact.')' : '' ?>:</label>
								<div class="col-sm-8">
									<select name="ticket_guardian_contact<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Tab" class="chosen-select-deselect"><option></option>
										<?php $tab_ticket_guardian_contact = get_config($dbc, 'ticket_guardian_contact'.($tab == '' ? '' : '_'.$tab));
										foreach(explode(',',get_config($dbc, 'all_contact_tabs')) as $category) { ?>
											<option <?= $category == $tab_ticket_guardian_contact ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<?php $ticket_project_contact = get_config($dbc, 'ticket_project_contact'); ?>
								<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will specify the tab for the <?= CONTACTS_TILE ?> in the Additional Contact list."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Additional Contact Tab<?= $ticket_project_contact != '' && $tab != '' ? ' (Default: '.$ticket_project_contact.')' : '' ?>:</label>
								<div class="col-sm-8">
									<select name="ticket_project_contact<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Tab" class="chosen-select-deselect"><option></option>
										<?php $tab_ticket_project_contact = get_config($dbc, 'ticket_project_contact'.($tab == '' ? '' : '_'.$tab));
										foreach(explode(',',get_config($dbc, 'all_contact_tabs')) as $category) { ?>
											<option <?= $category == $tab_ticket_project_contact ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<?php $ticket_notify_contact = get_config($dbc, 'ticket_notify_contact'); ?>
								<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will specify the tab for the <?= CONTACTS_TILE ?> in the Notify Party list."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Notify Party Tab<?= $ticket_project_contact != '' && $tab != '' ? ' (Default: '.$ticket_project_contact.')' : '' ?>:</label>
								<div class="col-sm-8">
									<select name="ticket_notify_contact<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Tab" class="chosen-select-deselect"><option></option>
										<?php $tab_ticket_notify_contact = get_config($dbc, 'ticket_notify_contact'.($tab == '' ? '' : '_'.$tab));
										foreach(explode(',',get_config($dbc, 'all_contact_tabs')) as $category) { ?>
											<option <?= $category == $tab_ticket_notify_contact ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<?php $ticket_custom_field = get_config($dbc, 'ticket_custom_field'); ?>
								<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will specify the tab for the <?= CONTACTS_TILE ?> in the Additional Contact list."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Custom Field Label<?= $ticket_custom_field != '' && $tab != '' ? ' (Default: '.$ticket_custom_field.')' : '' ?>:</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="ticket_custom_field<?= $tab == '' ? '' : '_'.$tab ?>" placeholder="Enter Field Name" value="<?= get_config($dbc, 'ticket_custom_field'.($tab == '' ? '' : '_'.$tab)) ?>">
								</div>
							</div>
							<div class="block-group">
								<?php $ticket_custom_field_values = explode('#*#',get_config($dbc, 'ticket_custom_field_values'));
								$tab_ticket_custom_field_values = explode('#*#',get_config($dbc, 'ticket_custom_field_values'.($tab == '' ? '' : '_'.$tab))); ?>
								<div class="col-sm-4"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="If specified, these will appear as options in a dropdown for this field."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Option Label:</div>
								<div class="col-sm-8"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="If specified, these will appear as the values for the options. These must be specified, and must each be unique."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Option Value:</div>
								<?php if($tab != '') {
									foreach($ticket_custom_field_values as $field_value) {
										$field_value = explode('|*|',$field_value); ?>
										<div class="form-group custom_options">
											<div class="col-sm-4"><?= $field_value[0] ?></div>
											<div class="col-sm-8"><?= $field_value[1] ?></div>
										</div>
									<?php }
								}
								foreach($tab_ticket_custom_field_values as $field_value) {
									$field_value = explode('|*|',$field_value); ?>
									<div class="multi-block custom_options">
										<div class="col-sm-4"><input type="text" class="form-control" name="ticket_custom_field_values<?= $tab == '' ? '' : '_'.$tab ?>" value="<?= $field_value[0] ?>"></div>
										<div class="col-sm-7"><input type="text" class="form-control" name="ticket_custom_field_values<?= $tab == '' ? '' : '_'.$tab ?>" value="<?= $field_value[1] ?>"></div>
										<div class="col-sm-1">
											<img class="cursor-hand inline-img" src="../img/icons/ROOK-add-icon.png" onclick="$(this).closest('.multi-block').after($(this).closest('.multi-block').clone());$(this).closest('.multi-block').next().find('input').val('').change(saveFields);">
											<img class="cursor-hand inline-img" src="../img/remove.png" onclick="$(this).closest('.multi-block').remove();">
										</div>
										<div class="clearfix"></div>
									</div>
								<?php } ?>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Purchase Order List') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Purchase Order List">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_po_number',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_po_number',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_po_number" data-toggle="<?= in_array('ticket_po_number',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_po_number',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_po_number',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Purchase Order List", $all_config) ? 'checked disabled' : (in_array("Purchase Order List", $value_config) ? "checked" : '') ?> value="Purchase Order List" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'PO List') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PO List", $all_config) ? 'checked disabled' : (in_array("PO List", $value_config) ? "checked" : '') ?> value="PO List" name="tickets[]"> Purchase Orders</label>
							<?php } ?>
							<?php if($field_sort_field == 'PO Slider Icons') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PO Slider Icons", $all_config) ? 'checked disabled' : (in_array("PO Slider Icons", $value_config) ? "checked" : '') ?> value="PO Slider Icons" name="tickets[]"> Display Slider Icon</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Customer Orders') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Customer Orders">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Customer Orders' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('po_',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_customer_order',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_customer_order" data-toggle="<?= in_array('ticket_customer_order',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_customer_order',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_customer_order',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Customer Orders' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Customer Orders", $all_config) ? 'checked disabled' : (in_array("Customer Orders", $value_config) ? "checked" : '') ?> value="Customer Orders" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'CO List') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("CO List", $all_config) ? 'checked disabled' : (in_array("CO List", $value_config) ? "checked" : '') ?> value="CO List" name="tickets[]"> Customer Orders</label>
							<?php } ?>
							<?php if($field_sort_field == 'CO Slider Icons') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("CO Slider Icons", $all_config) ? 'checked disabled' : (in_array("CO Slider Icons", $value_config) ? "checked" : '') ?> value="CO Slider Icons" name="tickets[]"> Display Slider Icon</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Details') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Details">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : PROJECT_NOUN.' Details' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('project_details',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('project_details',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="project_details" data-toggle="<?= in_array('project_details',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('project_details',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('project_details',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : PROJECT_NOUN.' Details' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Details", $all_config) ? 'checked disabled' : (in_array("Details", $value_config) ? "checked" : '') ?> value="Details" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Detail Business') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Business", $all_config) ? 'checked disabled' : (in_array("Detail Business", $value_config) ? "checked" : '') ?> value="Detail Business" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Business</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Project') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Project", $all_config) ? 'checked disabled' : (in_array("Detail Project", $value_config) ? "checked" : '') ?> value="Detail Project" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= PROJECT_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= PROJECT_NOUN ?></label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Contact') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Contact", $all_config) ? 'checked disabled' : (in_array("Detail Contact", $value_config) ? "checked" : '') ?> value="Detail Contact" name="tickets[]"> Contact</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Contact Phone') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Contact Phone", $all_config) ? 'checked disabled' : (in_array("Detail Contact Phone", $value_config) ? "checked" : '') ?> value="Detail Contact Phone" name="tickets[]"> Contact / Business Phone Numbers</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Rate Card') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Rate Card", $all_config) ? 'checked disabled' : (in_array("Detail Rate Card", $value_config) ? "checked" : '') ?> value="Detail Rate Card" name="tickets[]"> Rate Card</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Heading') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Heading", $all_config) ? 'checked disabled' : (in_array("Detail Heading", $value_config) ? "checked" : '') ?> value="Detail Heading" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to enter a description for the current <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Name</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Date') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Date", $all_config) ? 'checked disabled' : (in_array("Detail Date", $value_config) ? "checked" : '') ?> value="Detail Date" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify the date of the current <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Date</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Start Date Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Start Date Time", $all_config) ? 'checked disabled' : (in_array("Detail Start Date Time", $value_config) ? "checked" : '') ?> value="Detail Start Date Time" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify the start date and time of the current <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Scheduled Start Date &amp; Time</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail End Date Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail End Date Time", $all_config) ? 'checked disabled' : (in_array("Detail End Date Time", $value_config) ? "checked" : '') ?> value="Detail End Date Time" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify the end date and time of the current <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Scheduled End Date &amp; Time</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Staff') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Staff", $all_config) ? 'checked disabled' : (in_array("Detail Staff", $value_config) ? "checked" : '') ?> value="Detail Staff" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of Staff to assign to the current <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Assigned Staff</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Staff Times') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Staff Times", $all_config) ? 'checked disabled' : (in_array("Detail Staff Times", $value_config) ? "checked" : '') ?> value="Detail Staff Times" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify the start and end time for Staff for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Staff Times</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Member Times') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Member Times", $all_config) ? 'checked disabled' : (in_array("Detail Member Times", $value_config) ? "checked" : '') ?> value="Detail Member Times" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify the start and end time for Members for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Member Times</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Times') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Times", $all_config) ? 'checked disabled' : (in_array("Detail Times", $value_config) ? "checked" : '') ?> value="Detail Times" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify the start and end time for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_NOUN ?> Times</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Duration') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Duration", $all_config) ? 'checked disabled' : (in_array("Detail Duration", $value_config) ? "checked" : '') ?> value="Detail Duration" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify the duration of the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_NOUN ?> Duration</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Notes", $all_config) ? 'checked disabled' : (in_array("Detail Notes", $value_config) ? "checked" : '') ?> value="Detail Notes" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add general notes to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Image') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Image", $all_config) ? 'checked disabled' : (in_array("Detail Image", $value_config) ? "checked" : '') ?> value="Detail Image" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add a specific image to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Attach Image</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Max Capacity') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Max Capacity", $all_config) ? 'checked disabled' : (in_array("Detail Max Capacity", $value_config) ? "checked" : '') ?> value="Detail Max Capacity" name="tickets[]"> Max Capacity</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Staff Capacity') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Staff Capacity", $all_config) ? 'checked disabled' : (in_array("Detail Staff Capacity", $value_config) ? "checked" : '') ?> value="Detail Staff Capacity" name="tickets[]"> Staff Capacity</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Status') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Status", $all_config) ? 'checked disabled' : (in_array("Detail Status", $value_config) ? "checked" : '') ?> value="Detail Status" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of statuses to assign to the current <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Status</label>
							<?php } ?>
							<?php if($field_sort_field == 'Detail Total Budget Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Detail Total Budget Time", $all_config) ? 'checked disabled' : (in_array("Detail Total Budget Time", $value_config) ? "checked" : '') ?> value="Detail Total Budget Time" name="tickets[]"> Total Budget Time</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Contact Notes') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Contact Notes">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : CONTACTS_NOUN.' Notes' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('contact_notes',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('project_details',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="contact_notes" data-toggle="<?= in_array('contact_notes',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('contact_notes',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('contact_notes',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : CONTACTS_NOUN.' Notes' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Contact Notes", $all_config) ? 'checked disabled' : (in_array("Contact Notes", $value_config) ? "checked" : '') ?> value="Contact Notes" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Attached Business Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Attached Business Notes", $all_config) ? 'checked disabled' : (in_array("Attached Business Notes", $value_config) ? "checked" : '') ?> value="Attached Business Notes" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will display the notes attached to the <?= BUSINESS_CAT ?> in the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= BUSINESS_CAT ?> Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Attached Contact Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Attached Contact Notes", $all_config) ? 'checked disabled' : (in_array("Attached Contact Notes", $value_config) ? "checked" : '') ?> value="Attached Contact Notes" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will display the notes attached to the <?= CONTACTS_NOUN ?> in the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= CONTACTS_NOUN ?> Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Attached Contact Notes Add Note') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Attached Contact Notes Add Note", $all_config) ? 'checked disabled' : (in_array("Attached Contact Notes Add Note", $value_config) ? "checked" : '') ?> value="Attached Contact Notes Add Note" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add notes to the attached <?= CONTACTS_NOUN ?> in the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= CONTACTS_NOUN ?> Notes - Add Note Button</label>
							<?php } ?>
							<?php if($field_sort_field == 'Attached Contact Notes Anyone Can Add') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Attached Contact Notes Anyone Can Add", $all_config) ? 'checked disabled' : (in_array("Attached Contact Notes Anyone Can Add", $value_config) ? "checked" : '') ?> value="Attached Contact Notes Anyone Can Add" name="tickets[]">
									<?= CONTACTS_NOUN ?> Notes - Anyone Can Add</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Individuals') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Individuals">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Individuals Present' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_individuals',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_individuals',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_individuals" data-toggle="<?= in_array('ticket_individuals',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_individuals',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_individuals',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Individuals Present' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Individuals", $all_config) ? 'checked disabled' : (in_array("Individuals", $value_config) ? "checked" : '') ?> value="Individuals" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify any <?= CONTACTS_TILE ?> for the <?= TICKET_NOUN ?>, or write in additional names."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
						<div class="block-group">
							<div class="hide-titles-mob">
								<label class="col-sm-5">Tile</label>
								<label class="col-sm-5">Tab</label>
							</div>
							<?php foreach(explode('#*#',get_config($dbc, 'ticket_individuals')) as $type) {
								$type = explode('|',$type); ?>
								<div class="form-group ind_type">
									<div class="col-sm-5">
										<label class="show-on-mob">Tile:</label>
										<select name="tile_src" data-placeholder="Select Tile" class="chosen-select-deselect" onchange="filterIndCategories();">
											<?php if(tile_enabled($dbc, 'clientinfo')) { ?>
												<option <?= $type[0] == 'clientinfo' ? 'selected' : '' ?> value="clientinfo">Client Information</option>
											<?php } ?>
											<?php if(tile_enabled($dbc, 'contacts_inbox')) { ?>
												<option <?= $type[0] == 'contacts' ? 'selected' : '' ?> value="contacts">Contacts</option>
											<?php } else if(tile_enabled($dbc, 'contacts')) { ?>
												<option <?= $type[0] == 'contacts' ? 'selected' : '' ?> value="contacts">Contacts</option>
											<?php } ?>
											<?php if(tile_enabled($dbc, 'contacts3')) { ?>
												<option <?= $type[0] == 'contacts3' ? 'selected' : '' ?> value="contacts3">Contacts 3</option>
											<?php } ?>
											<?php if(tile_enabled($dbc, 'contactsrolodex')) { ?>
												<option <?= $type[0] == 'contactsrolodex' ? 'selected' : '' ?> value="contactsrolodex">Contacts Rolodex</option>
											<?php } ?>
											<?php if(tile_enabled($dbc, 'members')) { ?>
												<option <?= $type[0] == 'members' ? 'selected' : '' ?> value="members">Members</option>
											<?php } ?>
											<?php if(tile_enabled($dbc, 'staff')) { ?>
												<option <?= $type[0] == 'staff' ? 'selected' : '' ?> value="staff">Staff</option>
											<?php } ?>
											<?php if(tile_enabled($dbc, 'vendors')) { ?>
												<option <?= $type[0] == 'vendors' ? 'selected' : '' ?> value="vendors"><?= VENDOR_TILE ?></option>
											<?php } ?>
											<option <?= $type[0] == 'custom' ? 'selected' : '' ?> value="custom">Custom Tabs</option>
										</select>
									</div>
									<div class="col-sm-5 cust_div">
										<input type="text" class="form-control" name="individual_categories" value="<?= $type[1] ?>">
									</div>
									<div class="col-sm-5 cat_div">
										<label class="show-on-mob">Tab:</label>
										<select name="individual_categories" data-placeholder="Select Tab" class="chosen-select-deselect"><option></option>
											<?php foreach(explode(',',get_config($dbc, 'clientinfo_tabs')) as $category) { ?>
												<option <?= $type[0] == 'clientinfo' && $type[1] == $category ? 'selected' : '' ?> data-tile="clientinfo" value="<?= $category ?>"><?= $category ?></option>
											<?php } ?>
											<?php foreach(explode(',',get_config($dbc, 'contacts_tabs')) as $category) { ?>
												<option <?= $type[0] == 'contacts' && $type[1] == $category ? 'selected' : '' ?> data-tile="contacts" value="<?= $category ?>"><?= $category ?></option>
											<?php } ?>
											<?php foreach(explode(',',get_config($dbc, 'contacts3_tabs')) as $category) { ?>
												<option <?= $type[0] == 'contacts3' && $type[1] == $category ? 'selected' : '' ?> data-tile="contacts3" value="<?= $category ?>"><?= $category ?></option>
											<?php } ?>
											<?php foreach(explode(',',get_config($dbc, 'contactsrolodex_tabs')) as $category) { ?>
												<option <?= $type[0] == 'contactsrolodex' && $type[1] == $category ? 'selected' : '' ?> data-tile="contactsrolodex" value="<?= $category ?>"><?= $category ?></option>
											<?php } ?>
											<?php foreach(explode(',',get_config($dbc, 'members_tabs')) as $category) { ?>
												<option <?= $type[0] == 'members' && $type[1] == $category ? 'selected' : '' ?> data-tile="members" value="<?= $category ?>"><?= $category ?></option>
											<?php } ?>
											<option <?= $type[0] == 'staff' && $type[1] == 'ALL' ? 'selected' : '' ?> data-tile="staff" value="ALL">All Tabs</option>
											<?php foreach(explode(',',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT GROUP_CONCAT(`categories` SEPARATOR ',') `cats` FROM `field_config_contacts` WHERE `tab`='Staff'"))['cats']) as $category) { ?>
												<option <?= $type[0] == 'staff' && $type[1] == $category ? 'selected' : '' ?> data-tile="staff" value="<?= $category ?>"><?= $category ?></option>
											<?php } ?>
											<?php foreach(explode(',',get_config($dbc, 'vendors_tabs')) as $category) { ?>
												<option <?= $type[0] == 'vendors' && $type[1] == $category ? 'selected' : '' ?> data-tile="vendors" value="<?= $category ?>"><?= $category ?></option>
											<?php } ?>
										</select>
									</div>
									<div class="col-sm-2">
										<img src="../img/icons/drag_handle.png" class="inline-img pull-right drag-handle">
										<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addIndividual();">
										<img src="../img/remove.png" class="inline-img pull-right" onclick="remIndividual(this);">
									</div>
									<div class="clearfix"></div>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Path & Milestone') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Path & Milestone">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Path & Milestone' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_path_milestone',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_path_milestone',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_path_milestone" data-toggle="<?= in_array('ticket_path_milestone',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_path_milestone',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_path_milestone',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Path & Milestone' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Path & Milestone", $all_config) ? 'checked disabled' : (in_array("Path & Milestone", $value_config) ? "checked" : '') ?> value="Path & Milestone" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify the milestone of the <?= TICKET_NOUN ?> for the <?= PROJECT_NOUN ?> Path."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Fees') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Fees">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Fees' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_fees',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_fees',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_fees" data-toggle="<?= in_array('ticket_fees',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_fees',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_fees',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Fees' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Fees", $all_config) ? 'checked disabled' : (in_array("Fees", $value_config) ? "checked" : '') ?> value="Fees" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify fees for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Location') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Location">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Sites' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_location',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_location',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_location" data-toggle="<?= in_array('ticket_location',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_location',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_location',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Sites' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Location", $all_config) ? 'checked disabled' : (in_array("Location", $value_config) ? "checked" : '') ?> value="Location" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Location Site') { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Location Site", $all_config) ? 'checked disabled' : (in_array("Location Site", $value_config) ? "checked" : '') ?> value="Location Site" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= SITES_CAT ?> to attach to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Site</label>
							<?php } ?>
							<?php if($field_sort_field == 'Location Site Info') { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Location Site Info", $all_config) ? 'checked disabled' : (in_array("Location Site Info", $value_config) ? "checked" : '') ?> value="Location Site Info" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will display the <?= SITES_CAT ?> within the <?= TICKET_NOUN ?>, and allow it to be updated."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Site Info</label>
							<?php } ?>
							<?php if($field_sort_field == 'Location Notes') { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Location Notes", $all_config) ? 'checked disabled' : (in_array("Location Notes", $value_config) ? "checked" : '') ?> value="Location Notes" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to create notes for the <?= TICKET_NOUN ?> regarding the <?= SITES_CAT ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Emergency') { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Emergency", $all_config) ? 'checked disabled' : (in_array("Emergency", $value_config) ? "checked" : '') ?> value="Emergency" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will display the emergency information for the <?= SITES_CAT ?> within the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Site Emergency Plan</label>
							<?php } ?>
							<?php if($field_sort_field == 'Location Filter By Client') { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Location Filter By Client", $all_config) ? 'checked disabled' : (in_array("Location Filter By Client", $value_config) ? "checked" : '') ?> value="Location Filter By Client" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will filter out the Sites so only ones connected to the Contact/Client are displayed."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Filter By Client</label>
							<?php } ?>
							<?php if($field_sort_field == 'Location Notes Anyone Can Add') { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Location Notes Anyone Can Add", $all_config) ? 'checked disabled' : (in_array("Location Notes Anyone Can Add", $value_config) ? "checked" : '') ?> value="Location Notes Anyone Can Add" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow anyone to add notes even if they don't have edit access, but cannot remove notes."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Anyone Can Add Notes</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Members ID') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Members ID">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Members ID Card' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_members_id_card',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_members_id_card',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_members_id_card" data-toggle="<?= in_array('ticket_members_id_card',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_members_id_card',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_members_id_card',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Members ID Card' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Members ID", $all_config) ? 'checked disabled' : (in_array("Members ID", $value_config) ? "checked" : '') ?> value="Members ID" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the list of Members for the <?= TICKET_NOUN ?>, and select details to display for that Member. Details added here will be added to the Member's profile."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Members ID Age') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Members ID Age", $all_config) ? 'checked disabled' : (in_array("Members ID Age", $value_config) ? "checked" : '') ?> value="Members ID Age" name="tickets[]"> Age</label>
							<?php } ?>
							<?php if($field_sort_field == 'Members ID Parental Guardian Family Contact') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Members ID Parental Guardian Family Contact", $all_config) ? 'checked disabled' : (in_array("Members ID Parental Guardian Family Contact", $value_config) ? "checked" : '') ?> value="Members ID Parental Guardian Family Contact" name="tickets[]"> Parental/Guardian &amp; Family Contact</label>
							<?php } ?>
							<?php if($field_sort_field == 'Members ID Emergency Contact') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Members ID Emergency Contact", $all_config) ? 'checked disabled' : (in_array("Members ID Emergency Contact", $value_config) ? "checked" : '') ?> value="Members ID Emergency Contact" name="tickets[]"> Emergency Contact</label>
							<?php } ?>
							<?php if($field_sort_field == 'Members ID Medications') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Members ID Medications", $all_config) ? 'checked disabled' : (in_array("Members ID Medications", $value_config) ? "checked" : '') ?> value="Members ID Medications" name="tickets[]"> Medications</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Mileage') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Mileage">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Mileage' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_mileage',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_mileage',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_mileage" data-toggle="<?= in_array('ticket_mileage',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_mileage',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_mileage',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Mileage' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Mileage', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Mileage", $all_config) ? 'checked disabled' : (in_array("Mileage", $value_config) ? "checked" : '') ?> value="Mileage" name="tickets[]">
							<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to track mileage for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable Mileage</label>
					<?php } ?>
					<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Drive Time', $merged_config_fields)) { ?>
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Drive Time", $all_config) ? 'checked disabled' : (in_array("Drive Time", $value_config) ? "checked" : '') ?> value="Drive Time" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to track driving time for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable Drive Time</label>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Staff') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Staff">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_staff_list',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_staff_list',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_staff_list" data-toggle="<?= in_array('ticket_staff_list',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_staff_list',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_staff_list',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Staff", $all_config) ? 'checked disabled' : (in_array("Staff", $value_config) ? "checked" : '') ?> value="Staff" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify Staff for the <?= TICKET_NOUN ?>, and add details about what they are doing."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Staff Rate Positions') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Rate Positions", $all_config) ? 'checked disabled' : (in_array("Staff Rate Positions", $value_config) ? "checked" : '') ?> value="Staff Rate Positions" name="tickets[]"> Only Positions with Rates</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Position') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Position", $all_config) ? 'checked disabled' : (in_array("Staff Position", $value_config) ? "checked" : '') ?> value="Staff Position" name="tickets[]"> Position</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Rate') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Rate", $all_config) ? 'checked disabled' : (in_array("Staff Rate", $value_config) ? "checked" : '') ?> value="Staff Rate" name="tickets[]"> Rate (displayed for individuals with Settings Permissions)</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Start') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Start", $all_config) ? 'checked disabled' : (in_array("Staff Start", $value_config) ? "checked" : '') ?> value="Staff Start" name="tickets[]"> Shift Start Time</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Set Hours') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Set Hours", $all_config) ? 'checked disabled' : (in_array("Staff Set Hours", $value_config) ? "checked" : '') ?> value="Staff Set Hours" name="tickets[]"> Payable Hours</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Hours') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Hours", $all_config) ? 'checked disabled' : (in_array("Staff Hours", $value_config) ? "checked" : '') ?> value="Staff Hours" name="tickets[]"> Hours</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Estimate') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Estimate", $all_config) ? 'checked disabled' : (in_array("Staff Estimate", $value_config) ? "checked" : '') ?> value="Staff Estimate" name="tickets[]"> Estimated Hours</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Overtime') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Overtime", $all_config) ? 'checked disabled' : (in_array("Staff Overtime", $value_config) ? "checked" : '') ?> value="Staff Overtime" name="tickets[]"> Overtime</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Travel') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Travel", $all_config) ? 'checked disabled' : (in_array("Staff Travel", $value_config) ? "checked" : '') ?> value="Staff Travel" name="tickets[]"> Travel Time</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Subsistence') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Subsistence", $all_config) ? 'checked disabled' : (in_array("Staff Subsistence", $value_config) ? "checked" : '') ?> value="Staff Subsistence" name="tickets[]"> Subsistence Pay</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Subsistence Options') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Subsistence Options", $all_config) ? 'checked disabled' : (in_array("Staff Subsistence Options", $value_config) ? "checked" : '') ?> value="Staff Subsistence Options" name="tickets[]"> Subsistence Pay by Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Check In') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Check In", $all_config) ? 'checked disabled' : (in_array("Staff Check In", $value_config) ? "checked" : '') ?> value="Staff Check In" name="tickets[]"> Check In</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Set Hours Time Sheet') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Set Hours Time Sheet", $all_config) ? 'checked disabled' : (in_array("Staff Set Hours Time Sheet", $value_config) ? "checked" : '') ?> value="Staff Set Hours Time Sheet" name="tickets[]"> Track Payable Hours in Time Sheet</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Billing') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Billing", $all_config) ? 'checked disabled' : (in_array("Staff Billing", $value_config) ? "checked" : '') ?> value="Staff Billing" name="tickets[]"> Summary</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Anyone Can Add') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Anyone Can Add", $all_config) ? 'checked disabled' : (in_array("Staff Anyone Can Add", $value_config) ? "checked" : '') ?> value="Staff Anyone Can Add" name="tickets[]"> Anyone Can Add Staff</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Multiple Times') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Multiple Times", $all_config) ? 'checked disabled' : (in_array("Staff Multiple Times", $value_config) ? "checked" : '') ?> value="Staff Multiple Times" name="tickets[]"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow adding multiple Dates/Times to the Staff's Time Sheet. The Time Sheet will take these values over the Check In/Check Out times."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span> Multiple Dates/Times</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Multiple Times Date') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Multiple Times Date", $all_config) ? 'checked disabled' : (in_array("Staff Multiple Times Date", $value_config) ? "checked" : '') ?> value="Staff Multiple Times Date" name="tickets[]"> Multiple Dates/Times - Date</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Multiple Times Start Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Multiple Times Start Time", $all_config) ? 'checked disabled' : (in_array("Staff Multiple Times Start Time", $value_config) ? "checked" : '') ?> value="Staff Multiple Times Start Time" name="tickets[]"> Multiple Dates/Times - Start Time</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Multiple Times End Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Multiple Times End Time", $all_config) ? 'checked disabled' : (in_array("Staff Multiple Times End Time", $value_config) ? "checked" : '') ?> value="Staff Multiple Times End Time" name="tickets[]"> Multiple Dates/Times - End Time</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Multiple Times Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Multiple Times Type", $all_config) ? 'checked disabled' : (in_array("Staff Multiple Times Type", $value_config) ? "checked" : '') ?> value="Staff Multiple Times Type" name="tickets[]"> Multiple Dates/Times - Type of Time</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Multiple Times Set Hours') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Multiple Times Set Hours", $all_config) ? 'checked disabled' : (in_array("Staff Multiple Times Set Hours", $value_config) ? "checked" : '') ?> value="Staff Multiple Times Set Hours" name="tickets[]"> Multiple Dates/Times - Payable Hours</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Check Shifts') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Check Shifts", $all_config) ? 'checked disabled' : (in_array("Staff Check Shifts", $value_config) ? "checked" : '') ?> value="Staff Check Shifts" name="tickets[]"> Check Shifts</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Check Days Off') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Days Off", $all_config) ? 'checked disabled' : (in_array("Staff Days Off", $value_config) ? "checked" : '') ?> value="Staff Days Off" name="tickets[]"> Check Days Off</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Hide No Shift') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Hide No Shift", $all_config) ? 'checked disabled' : (in_array("Staff Hide No Shift", $value_config) ? "checked" : '') ?> value="Staff Hide No Shift" name="tickets[]"> Hide Staff With No Shift</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Hide Days Off') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Hide Days Off", $all_config) ? 'checked disabled' : (in_array("Staff Hide Days Off", $value_config) ? "checked" : '') ?> value="Staff Hide Days Off" name="tickets[]"> Hide Staff With Days Off</label>
							<?php } ?>
						<?php } ?>
						</div>
						<div class="form-group">
							<label class="col-sm-4">Default Travel Time:</label>
							<div class="col-sm-8">
								<?php $ticket_staff_travel_default = get_config($dbc, 'ticket_staff_travel_default'); ?>
								<input type="number" name="ticket_staff_travel_default" class="form-control" value="<?= $ticket_staff_travel_default ?>">
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Staff Tasks') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Staff Tasks">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff by Task' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_staff_assign_tasks',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_staff_assign_tasks',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_staff_assign_tasks" data-toggle="<?= in_array('ticket_staff_assign_tasks',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_staff_assign_tasks',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_staff_assign_tasks',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff by Task' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Staff Tasks", $all_config) ? 'checked disabled' : (in_array("Staff Tasks", $value_config) ? "checked" : '') ?> value="Staff Tasks" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add Staff to the <?= TICKET_NOUN ?>, and assign them custom Tasks."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
						<div class="block-group">
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Ticket Tasks Add Button", $all_config) ? 'checked disabled' : (in_array("Ticket Tasks Add Button", $value_config) ? "checked" : '') ?> value="Ticket Tasks Add Button" name="tickets[]"> Add Manually Button</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Ticket Tasks Auto Check In", $all_config) ? 'checked disabled' : (in_array("Ticket Tasks Auto Check In", $value_config) ? "checked" : '') ?> value="Ticket Tasks Auto Check In" name="tickets[]"> Add and Check In Button</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Ticket Tasks Auto Load New", $all_config) ? 'checked disabled' : (in_array("Ticket Tasks Auto Load New", $value_config) ? "checked" : '') ?> value="Ticket Tasks Auto Load New" name="tickets[]"> Load to New <?= TICKET_NOUN ?> for Extra Billing</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Ticket Tasks Projects", $all_config) ? 'checked disabled' : (in_array("Ticket Tasks Projects", $value_config) ? "checked" : '') ?> onchange="set_task_groups(this.checked);" value="Ticket Tasks Projects" name="tickets[]"> <?= PROJECT_NOUN ?> Tabs as Task Groups</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Ticket Tasks Ticket Type", $all_config) ? 'checked disabled' : (in_array("Ticket Tasks Ticket Type", $value_config) ? "checked" : '') ?> onchange="set_task_groups_tickettype(this.checked);" value="Ticket Tasks Ticket Type" name="tickets[]"> <?= TICKET_NOUN ?> Tabs as Task Groups</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Ticket Tasks Groups", $all_config) ? 'checked disabled' : (in_array("Ticket Tasks Groups", $value_config) ? "checked" : '') ?> value="Ticket Tasks Groups" name="tickets[]"> New <?= PROJECT_NOUN ?> for Extra Billing</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Task Extra Billing", $all_config) ? 'checked disabled' : (in_array("Task Extra Billing", $value_config) ? "checked" : '') ?> value="Task Extra Billing" name="tickets[]"> Extra Billing Configuration</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Extra Billing Create New", $all_config) ? 'checked disabled' : (in_array("Extra Billing Create New", $value_config) ? "checked" : '') ?> value="Extra Billing Create New" name="tickets[]"> New <?= TICKET_NOUN ?> for Extra Billing</label>
							<div class="form-group">
								<label class="col-sm-4">Send Extra Billing Notice:</label>
								<div class="col-sm-8">
									<input type="text" name="ticket_extra_billing_email" value="<?= get_config($dbc, 'ticket_extra_billing_email') ?>" class="form-control">
								</div>
							</div>
							<div class="task_group_projects no_drag" style="display:none;">
								You must use the following as the Task Groups:<ul><li><?= implode('</li><li>',explode(',',PROJECT_TYPES)) ?></li></ul>
							</div>
							<div class="task_group_tickettype no_drag" style="display:none;">
								You must use the following as the Task Groups:<ul><li><?= implode('</li><li>',explode(',',get_config($dbc, 'ticket_tabs'))) ?></li></ul>
							</div>
							<?php /*$task_groups = get_config($dbc, 'ticket_'.(!empty($tab) ? $tab : 'ALL').'_staff_tasks');
							if($task_groups == '') {
								$task_groups = get_config($dbc, 'site_work_order_tasks');
							}
							$task_groups = explode('#*#', $task_groups);
							if(in_array('Ticket Tasks Projects',array_merge($all_config,$value_config))) {
								$my_task_groups = $task_groups;
								$task_groups = [];
								foreach(explode(',',PROJECT_TYPES) as $task_group_name) {
									$added = false;
									foreach($my_task_groups as $task_group_id => $my_task_group) {
										if(explode('*#*',$my_task_group)[0] == $task_group_name) {
											$task_groups[] = $my_task_group;
											unset($my_task_groups[$task_group_id]);
											$added = true;
										}
									}
									if(!$added) {
										$task_groups[] = $task_group_name; ?>
										<script>
										$(document).ready(function() {
											$('[name=task]').first().change();
										});
										</script>
									<?php }
								}
							}
							if(in_array('Ticket Tasks Ticket Type',array_merge($all_config,$value_config))) {
								$my_task_groups = $task_groups;
								$task_groups = [];
								foreach(explode(',',get_config($dbc, 'ticket_tabs')) as $task_group_name) {
									$added = false;
									foreach($my_task_groups as $task_group_id => $my_task_group) {
										if(explode('*#*',$my_task_group)[0] == $task_group_name) {
											$task_groups[] = $my_task_group;
											unset($my_task_groups[$task_group_id]);
											$added = true;
										}
									}
									if(!$added) {
										$task_groups[] = $task_group_name; ?>
										<script>
										$(document).ready(function() {
											$('[name=task]').first().change();
										});
										</script>
									<?php }
								}
							}
							foreach($task_groups as $key => $group) {
								$list = explode('*#*', $group);
								echo "<div class='col-sm-12 task-group' style='border: 1px solid black'><div class='form-group'><label class='col-sm-4 control-label'>Task Group: </label><div class='col-sm-8'><input type='text' ".((in_array('Ticket Tasks Projects',array_merge($all_config,$value_config)) || in_array('Ticket Tasks Ticket Type',array_merge($all_config,$value_config))) ? 'readonly' : '')." name='task' value='".$list[0]."' class='form-control' onchange='set_task_data();'></div></div>";
								unset($list[0]);
								if(count($list) == 0) {
									$list[] = '';
								}
								foreach($list as $task) {
									echo "<div class='form-group col-lg-4 col-md-6'><label class='col-sm-4 control-label'>Task: </label><div class='col-sm-8'><input type='text' name='task' value='".$task."' class='form-control' onchange='set_task_data();'></div></div>";
								}
								echo "<button class='btn brand-btn pull-right' onclick='add_task(this); return false;'>Add Task</button>";
								echo "</div>";
							} ?>
							<input type="hidden" name="ticket_<?= (!empty($tab) ? $tab : 'ALL') ?>_staff_tasks" class="task_data" value="<?= implode('#*#',$task_groups) ?>">
							<button onclick="add_task_group(); return false;" class="btn brand-btn pull-right">Add Group</button>
							<div class="clearfix"></div>
							<script>
							function set_task_groups(use_project_types) {
								if(use_project_types) {
									var project_types = <?= json_encode(explode(',',PROJECT_TYPES)) ?>;
									$('.task_group_projects').show();
									$('.task-group').each(function() {
										var group_name = $(this).find('[name=task]').first();
										console.log(project_types);
										if(project_types.indexOf(group_name.val()) < 0) {
											group_name.val(project_types.shift()).change();
										}
									});
									$('[name=task]').first().change();
								} else {
									$('.task_group_projects').hide();
									$('.task-group').each(function() {
										var group_name = $(this).find('[name=task]').first();
										group_name.removeAttr('readonly');
									});
								}
							}
							function set_task_groups_tickettype(use_project_types) {
								if(use_project_types) {
									var project_types = <?= json_encode(explode(',',get_config($dbc, 'ticket_tabs'))) ?>;
									$('.task_group_tickettype').show();
									$('.task-group').each(function() {
										var group_name = $(this).find('[name=task]').first();
										console.log(project_types);
										if(project_types.indexOf(group_name.val()) < 0) {
											group_name.val(project_types.shift()).change();
										}
									});
									$('[name=task]').first().change();
								} else {
									$('.task_group_tickettype').hide();
									$('.task-group').each(function() {
										var group_name = $(this).find('[name=task]').first();
										group_name.removeAttr('readonly');
									});
								}
							}
							function add_task(btn) {
								var textbox = $(btn).closest('.task-group').find('.form-group').last().clone();
								textbox.find('input').val('');
								$(btn).before(textbox);
								$(btn).closest('.task-group').find('input').last().focus();
							}
							function add_task_group() {
								var group = $('.task-group').last();
								var clone = group.clone();
								clone.find('.form-group').not(':last').not(':first').remove();
								clone.find('input').val('');
								group.after(clone);
							}
							function set_task_data() {
								var data = [];
								$('.task-group').each(function() {
									var tasks = [];
									$(this).find('input').each(function() {
										if(this.value != '') {
											tasks.push(this.value);
										}
									});
									data.push(tasks.join('*#*'));
								});
								$('.task_data').val(data.join('#*#')).change();
							}
							</script><?php */ ?>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Members') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Members">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Members' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_members',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_members',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_members" data-toggle="<?= in_array('ticket_members',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_members',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_members',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Members' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Members", $all_config) ? 'checked disabled' : (in_array("Members", $value_config) ? "checked" : '') ?> value="Members" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to attach Members to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Contact Set Hours') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Contact Set Hours", $all_config) ? 'checked disabled' : (in_array("Contact Set Hours", $value_config) ? "checked" : '') ?> value="Contact Set Hours" name="tickets[]"> Billable Hours</label>
							<?php } ?>
							<?php if($field_sort_field == 'Members Profile') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Members Profile", $all_config) ? 'checked disabled' : (in_array("Members Profile", $value_config) ? "checked" : '') ?> value="Members Profile" name="tickets[]"> Members Profile</label>
							<?php } ?>
							<?php if($field_sort_field == 'Members Parental Guardian Family Contact') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Members Parental Guardian Family Contact", $all_config) ? 'checked disabled' : (in_array("Members Parental Guardian Family Contact", $value_config) ? "checked" : '') ?> value="Members Parental Guardian Family Contact" name="tickets[]"> Parental/Guardian &amp; Family Contact</label>
							<?php } ?>
							<?php if($field_sort_field == 'Members Emergency Contact') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Members Emergency Contact", $all_config) ? 'checked disabled' : (in_array("Members Emergency Contact", $value_config) ? "checked" : '') ?> value="Members Emergency Contact" name="tickets[]"> Emergency Contact</label>
							<?php } ?>
							<?php if($field_sort_field == 'Members Medical Details') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Members Medical Details", $all_config) ? 'checked disabled' : (in_array("Members Medical Details", $value_config) ? "checked" : '') ?> value="Members Medical Details" name="tickets[]"> Medical Details</label>
							<?php } ?>
							<?php if($field_sort_field == 'Members Key Methodologies') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Members Key Methodologies", $all_config) ? 'checked disabled' : (in_array("Members Key Methodologies", $value_config) ? "checked" : '') ?> value="Members Key Methodologies" name="tickets[]"> Key Methodologies</label>
							<?php } ?>
							<?php if($field_sort_field == 'Members Daily Log Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Members Daily Log Notes", $all_config) ? 'checked disabled' : (in_array("Members Daily Log Notes", $value_config) ? "checked" : '') ?> value="Members Daily Log Notes" name="tickets[]"> Daily Log Notes</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Clients') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Clients">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Clients' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_clients',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_clients',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_clients" data-toggle="<?= in_array('ticket_clients',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_clients',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_clients',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Clients' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Clients", $all_config) ? 'checked disabled' : (in_array("Clients", $value_config) ? "checked" : '') ?> value="Clients" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to attach a category of <?= CONTACTS_TILE ?> to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Contact Set Hours') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Contact Set Hours", $all_config) ? 'checked disabled' : (in_array("Contact Set Hours", $value_config) ? "checked" : '') ?> value="Contact Set Hours" name="tickets[]"> Billable Hours</label>
							<?php } ?>
						<?php } ?>
						</div>
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<div class="form-group">
								<?php $client_accordion_category = get_config($dbc, 'client_accordion_category'); ?>
								<label class="col-sm-4 control-label">Contact Tab for <?= !empty($renamed_accordion) ? $renamed_accordion : 'Clients' ?> Accordion<?= $client_accordion_category != '' && $tab != '' ? ' (Default: '.$client_accordion_category.')' : '' ?>:</label>
								<div class="col-sm-8">
									<select name="client_accordion_category<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Tab" class="chosen-select-deselect"><option></option>
										<?php $tab_client_accordion_category = get_config($dbc, 'client_accordion_category'.($tab == '' ? '' : '_'.$tab));
										foreach(explode(',',get_config($dbc, 'all_contact_tabs')) as $category) { ?>
											<option <?= $category == $tab_client_accordion_category ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Wait List') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Wait List">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Wait List' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_wait_list',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_wait_list',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_wait_list" data-toggle="<?= in_array('ticket_wait_list',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_wait_list',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_wait_list',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Wait List' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Wait List", $all_config) ? 'checked disabled' : (in_array("Wait List", $value_config) ? "checked" : '') ?> value="Wait List" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add additional individuals to the <?= TICKET_NOUN ?> that are just on a wait list and do not affect the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Wait List Members Medications') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Wait List Members Medications", $all_config) ? 'checked disabled' : (in_array("Wait List Members Medications", $value_config) ? "checked" : '') ?> value="Wait List Members Medications" name="tickets[]"> Members Medications</label>
							<?php } ?>
							<?php if($field_sort_field == 'Wait List Members Guardians') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Wait List Members Guardians", $all_config) ? 'checked disabled' : (in_array("Wait List Members Guardians", $value_config) ? "checked" : '') ?> value="Wait List Members Guardians" name="tickets[]"> Members Guardians</label>
							<?php } ?>
							<?php if($field_sort_field == 'Wait List Members Emergency Contacts') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Wait List Members Emergency Contacts", $all_config) ? 'checked disabled' : (in_array("Wait List Members Emergency Contacts", $value_config) ? "checked" : '') ?> value="Wait List Members Emergency Contacts" name="tickets[]"> Members Emergency Contacts</label>
							<?php } ?>
							<?php if($field_sort_field == 'Wait List Members Key Methodologies') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Wait List Members Key Methodologies", $all_config) ? 'checked disabled' : (in_array("Wait List Members Key Methodologies", $value_config) ? "checked" : '') ?> value="Wait List Members Key Methodologies" name="tickets[]"> Members Key Methodologies</label>
							<?php } ?>
							<?php if($field_sort_field == 'Wait List Members Daily Log Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Wait List Members Daily Log Notes", $all_config) ? 'checked disabled' : (in_array("Wait List Members Daily Log Notes", $value_config) ? "checked" : '') ?> value="Wait List Members Daily Log Notes" name="tickets[]"> Members Daily Log Notes</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Check In') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Check In">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Check In' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_checkin',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_checkin',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_checkin" data-toggle="<?= in_array('ticket_checkin',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_checkin',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_checkin',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Check In' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Check In', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Check In", $all_config) ? 'checked disabled' : (in_array("Check In", $value_config) ? "checked" : '') ?> value="Check In" name="tickets[]">
							<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to mark individuals, equipment, or other supplies as ready in the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable Check In</label>
					<?php } ?>
					<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Check In Member Drop Off', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Check In Member Drop Off", $all_config) ? 'checked disabled' : (in_array("Check In Member Drop Off", $value_config) ? "checked" : '') ?> value="Check In Member Drop Off" name="tickets[]"> Enable Member Drop Off</label>
					<?php } ?>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Checkin Hide All Button') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkin Hide All Button", $all_config) ? 'checked disabled' : (in_array("Checkin Hide All Button", $value_config) ? "checked" : '') ?> value="Checkin Hide All Button" name="tickets[]"> Hide Check In All Button</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkin Staff') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkin Staff", $all_config) ? 'checked disabled' : (in_array("Checkin Staff", $value_config) ? "checked" : '') ?> value="Checkin Staff" name="tickets[]"> Check In Staff</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkin Staff_Tasks') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkin Staff_Tasks", $all_config) ? 'checked disabled' : (in_array("Checkin Staff_Tasks", $value_config) ? "checked" : '') ?> value="Checkin Staff_Tasks" name="tickets[]"> Check In Staff by Task</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkin Delivery') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkin Delivery", $all_config) ? 'checked disabled' : (in_array("Checkin Delivery", $value_config) ? "checked" : '') ?> value="Checkin Delivery" name="tickets[]"> Check In Deliveries</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkin Clients') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkin Clients", $all_config) ? 'checked disabled' : (in_array("Checkin Clients", $value_config) ? "checked" : '') ?> value="Checkin Clients" name="tickets[]"> Check In Clients</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkin Members') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkin Members", $all_config) ? 'checked disabled' : (in_array("Checkin Members", $value_config) ? "checked" : '') ?> value="Checkin Members" name="tickets[]"> Check In Members</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkin material') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkin material", $all_config) ? 'checked disabled' : (in_array("Checkin material", $value_config) ? "checked" : '') ?> value="Checkin material" name="tickets[]"> Check In Materials</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkin equipment') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkin equipment", $all_config) ? 'checked disabled' : (in_array("Checkin equipment", $value_config) ? "checked" : '') ?> value="Checkin equipment" name="tickets[]"> Check In Equipment</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkin Get To Work') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkin Get To Work", $all_config) ? 'checked disabled' : (in_array("Checkin Get To Work", $value_config) ? "checked" : '') ?> value="Checkin Get To Work" name="tickets[]"> Get To Work Button</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Medication') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Medication">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Medication Administration' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_medications',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_medications',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_medications" data-toggle="<?= in_array('ticket_medications',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_medications',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_medications',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Medication Administration' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Medication", $all_config) ? 'checked disabled' : (in_array("Medication", $value_config) ? "checked" : '') ?> value="Medication" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to manage Medication for Members attached to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
						<div class="block-group">
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Medication Multiple Days", $all_config) ? 'checked disabled' : (in_array("Medication Multiple Days", $value_config) ? "checked" : '') ?> value="Medication Multiple Days" name="tickets[]"> Multiple Days</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Medication Group Days", $all_config) ? 'checked disabled' : (in_array("Medication Group Days", $value_config) ? "checked" : '') ?> value="Medication Group Days" name="tickets[]"> Group Days In Accordion</label>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Deliverables') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Deliverables">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Deliverables' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_deliverables',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_deliverables',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_deliverables" data-toggle="<?= in_array('view_ticket_deliverables',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_deliverables',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_deliverables',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Deliverables' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Deliverables", $all_config) ? 'checked disabled' : (in_array("Deliverables", $value_config) ? "checked" : '') ?> value="Deliverables" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify users, dates, and statuses for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Deliverable Status') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Deliverable Status", $all_config) ? 'checked disabled' : (in_array("Deliverable Status", $value_config) ? "checked" : '') ?> value="Deliverable Status" name="tickets[]"> Status</label>
							<?php } ?>
							<?php if($field_sort_field == 'Deliverable To Do') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Deliverable To Do", $all_config) ? 'checked disabled' : (in_array("Deliverable To Do", $value_config) ? "checked" : '') ?> value="Deliverable To Do" name="tickets[]"> To Do Date</label>
							<?php } ?>
							<?php if($field_sort_field == 'Deliverable Repeat') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Deliverable Repeat", $all_config) ? 'checked disabled' : (in_array("Deliverable Repeat", $value_config) ? "checked" : '') ?> value="Deliverable Repeat" name="tickets[]"> Recurrence</label>
							<?php } ?>
							<?php if($field_sort_field == 'Deliverable Internal') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Deliverable Internal", $all_config) ? 'checked disabled' : (in_array("Deliverable Internal", $value_config) ? "checked" : '') ?> value="Deliverable Internal" name="tickets[]"> Internal QA Date</label>
							<?php } ?>
							<?php if($field_sort_field == 'Deliverable Customer') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Deliverable Customer", $all_config) ? 'checked disabled' : (in_array("Deliverable Customer", $value_config) ? "checked" : '') ?> value="Deliverable Customer" name="tickets[]"> Customer QA Date</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Ticket Details' || ($sort_field == 'Services' && !in_array('Ticket Details', $sort_order))) { ?>
			<div class="form-group sort_order_accordion" data-accordion="Ticket Details">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : TICKET_NOUN.' Details / Services' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_info',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_info',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_info" data-toggle="<?= in_array('ticket_info',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_info',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_info',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : TICKET_NOUN.' Details / Services' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Ticket Details', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Ticket Details", $all_config) ? 'checked disabled' : (in_array("Ticket Details", $value_config) ? "checked" : '') ?> value="Ticket Details" name="tickets[]"> Enable <?= TICKET_NOUN ?> Details</label>
					<?php } ?>
					<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Services', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Services", $all_config) ? 'checked disabled' : (in_array("Services", $value_config) ? "checked" : '') ?> value="Services" name="tickets[]"> Enable Services</label>
					<?php } ?>
					<div class="block-group">
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<div class="block-group">
								<h3><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify a default heading / name for the <?= TICKET_NOUN ?> based on various fields."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Auto-Generated Heading</h3>
								<label class="form-checkbox"><input type="radio" <?= in_array("Heading Blank", $all_config) ? 'checked' : (in_array("Heading Blank", $value_config) ? "checked" : '') ?><?= in_array("Heading Blank", $all_config) ? 'disabled' : '' ?> value="Heading Blank" name="tickets[]"> No Generated Heading</label>
								<label class="form-checkbox"><input type="radio" <?= in_array("Heading Business Invoice", $all_config) ? 'checked disabled' : (in_array("Heading Business Invoice", $value_config) ? "checked" : '') ?> value="Heading Business Invoice" name="tickets[]"> Use Business Name &amp; Invoice#</label>
								<label class="form-checkbox"><input type="radio" <?= in_array("Heading Bus Invoice Date", $all_config) ? 'checked disabled' : (in_array("Heading Bus Invoice Date", $value_config) ? "checked" : '') ?> value="Heading Bus Invoice Date" name="tickets[]"> Use Invoice#, Business Name, &amp; Date</label>
								<label class="form-checkbox"><input type="radio" <?= in_array("Heading Project Invoice Date", $all_config) ? 'checked disabled' : (in_array("Heading Project Invoice Date", $value_config) ? "checked" : '') ?> value="Heading Project Invoice Date" name="tickets[]"> Use Invoice#, <?= PROJECT_NOUN ?> Name, &amp; Date</label>
								<label class="form-checkbox"><input type="radio" <?= in_array("Heading Business Date", $all_config) ? 'checked disabled' : (in_array("Heading Business Date", $value_config) ? "checked" : '') ?> value="Heading Business Date" name="tickets[]"> Use Business &amp; Scheduled Date</label>
								<label class="form-checkbox"><input type="radio" <?= in_array("Heading Contact Date", $all_config) ? 'checked disabled' : (in_array("Heading Contact Date", $value_config) ? "checked" : '') ?> value="Heading Contact Date" name="tickets[]"> Use Contact &amp; Scheduled Date</label>
								<label class="form-checkbox"><input type="radio" <?= in_array("Heading Business", $all_config) ? 'checked disabled' : (in_array("Heading Business", $value_config) ? "checked" : '') ?> value="Heading Business" name="tickets[]"> Use Business</label>
								<label class="form-checkbox"><input type="radio" <?= in_array("Heading Contact", $all_config) ? 'checked disabled' : (in_array("Heading Contact", $value_config) ? "checked" : '') ?> value="Heading Contact" name="tickets[]"> Use Contact</label>
								<label class="form-checkbox"><input type="radio" <?= in_array("Heading Date", $all_config) ? 'checked disabled' : (in_array("Heading Date", $value_config) ? "checked" : '') ?> value="Heading Date" name="tickets[]"> Use Scheduled Date</label>
								<label class="form-checkbox"><input type="radio" <?= in_array("Heading Milestone Date", $all_config) ? 'checked disabled' : (in_array("Heading Milestone Date", $value_config) ? "checked" : '') ?> value="Heading Milestone Date" name="tickets[]"> Use Project Milestone &amp; Date</label>
								<label class="form-checkbox"><input type="radio" <?= in_array("Heading Assigned", $all_config) ? 'checked disabled' : (in_array("Heading Assigned", $value_config) ? "checked" : '') ?> value="Heading Assigned" name="tickets[]"> Use Assigned Staff</label>
							</div>
						<?php } ?>
						<div class="fields_sortable">
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Group Cat Type All Services", $all_config) ? 'checked disabled' : (in_array("Service Group Cat Type All Services", $value_config) ? "checked" : '') ?> value="Service Group Cat Type All Services" name="tickets[]">
							<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will group your Services by Tab and Service Type. All Services will display as a list with an Include checkbox to add it to your <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Group Services by Tab/Service Tab - List Services</label>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Group Cat Type All Services Combine Checklist", $all_config) ? 'checked disabled' : (in_array("Service Group Cat Type All Services Combine Checklist", $value_config) ? "checked" : '') ?> value="Service Group Cat Type All Services Combine Checklist" name="tickets[]">
							<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will group your Services by Tab and Service Type. All Services will display as a list with an Include checkbox to add it to your <?= TICKET_NOUN ?>. The view on the <?= TICKET_NOUN ?> will be the Service Checklist with an Edit button to edit services."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Group Services by Tab/Service Type - List Services - Combine with Service Checklist</label>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Inline", $all_config) ? 'checked disabled' : (in_array("Service Inline", $value_config) ? "checked" : '') ?> value="Service Inline" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will combine the service and tab fields for the tickets into a single line for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Inline Service</label>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Multiple", $all_config) ? 'checked disabled' : (in_array("Service Multiple", $value_config) ? "checked" : '') ?> value="Service Multiple" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add multiple services to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Multiple Services</label>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Limit Service Tab", $all_config) ? 'checked disabled' : (in_array("Service Limit Service Category", $value_config) ? "checked" : '') ?> value="Service Limit Service Category" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will limit the Service Tabs displayed based on the Contact's Property Size (Service Tab) set in their profile."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Limit Service Tab by Contact</label>
						<?php } ?>
						<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Service Customer Template',$merged_config_fields)) { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Customer Template", $all_config) ? 'checked disabled' : (in_array("Service Customer Template", $value_config) ? "checked" : '') ?> value="Service Customer Template" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add all services from a Customer's Service Template to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Load Customer Template</label>
						<?php } ?>
						<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Service Load Template',$merged_config_fields)) { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Load Template", $all_config) ? 'checked disabled' : (in_array("Service Load Template", $value_config) ? "checked" : '') ?> value="Service Load Template" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add all services from a Load's Service Template to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Load Service Template</label>
						<?php } ?>
						<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Service Customer Template In Checklist',$merged_config_fields)) { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Customer Template In Checklist", $all_config) ? 'checked disabled' : (in_array("Service Customer Template In Checklist", $value_config) ? "checked" : '') ?> value="Service Customer Template In Checklist" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add all services from a Customer's Service Template to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Load Customer Template In Service Checklist</label>
						<?php } ?>
						<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Service Load Template In Checklist',$merged_config_fields)) { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Load Template In Checklist", $all_config) ? 'checked disabled' : (in_array("Service Load Template In Checklist", $value_config) ? "checked" : '') ?> value="Service Load Template In Checklist" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add all services from a Load's Service Template to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Load Service Template In Service Checklist</label>
						<?php } ?>
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Details Help Desk') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Details Help Desk", $all_config) ? 'checked disabled' : (in_array("Details Help Desk", $value_config) ? "checked" : '') ?> value="Details Help Desk" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to Add the <?= TICKET_NOUN ?> to the Help Desk."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Help Desk</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Type", $all_config) ? 'checked disabled' : (in_array("Service Type", $value_config) ? "checked" : '') ?> value="Service Type" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to filter services by type."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Service Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Category') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Category", $all_config) ? 'checked disabled' : (in_array("Service Category", $value_config) ? "checked" : '') ?> value="Service Category" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to filter services by tab."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Service Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Heading') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Heading", $all_config) ? 'checked disabled' : (in_array("Service Heading", $value_config) ? "checked" : '') ?> value="Service Heading" name="tickets[]"> Service Heading</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Total Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Total Time", $all_config) ? 'checked disabled' : (in_array("Service Total Time", $value_config) ? "checked" : '') ?> value="Service Total Time" name="tickets[]">Total Time</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Direct Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Direct Time", $all_config) ? 'checked disabled' : (in_array("Service Direct Time", $value_config) ? "checked" : '') ?> value="Service Direct Time" name="tickets[]">Direct Time</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Indirect Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Indirect Time", $all_config) ? 'checked disabled' : (in_array("Service Indirect Time", $value_config) ? "checked" : '') ?> value="Service Indirect Time" name="tickets[]">Indirect Time</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Track Total Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Track Total Time", $all_config) ? 'checked disabled' : (in_array("Service Track Total Time", $value_config) ? "checked" : '') ?> value="Service Track Total Time" name="tickets[]">Track Total Time in Time Sheets</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Track Direct Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Track Direct Time", $all_config) ? 'checked disabled' : (in_array("Service Track Direct Time", $value_config) ? "checked" : '') ?> value="Service Track Direct Time" name="tickets[]">Track Direct Time in Time Sheets</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Track Indirect Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Track Indirect Time", $all_config) ? 'checked disabled' : (in_array("Service Track Indirect Time", $value_config) ? "checked" : '') ?> value="Service Track Indirect Time" name="tickets[]">Track Indirect Time in Time Sheets</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Quantity') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Quantity", $all_config) ? 'checked disabled' : (in_array("Service Quantity", $value_config) ? "checked" : '') ?> value="Service Quantity" name="tickets[]"> Quantity</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service # of Rooms') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service # of Rooms", $all_config) ? 'checked disabled' : (in_array("Service # of Rooms", $value_config) ? "checked" : '') ?> value="Service # of Rooms" name="tickets[]"> # of Rooms</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Rate Card') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Rate Card", $all_config) ? 'checked disabled' : (in_array("Service Rate Card", $value_config) ? "checked" : '') ?> value="Service Rate Card" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will result in services that have no rate for the selected contact being hidden."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Only Show Services with Rates</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Estimated Hours') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Estimated Hours", $all_config) ? 'checked disabled' : (in_array("Service Estimated Hours", $value_config) ? "checked" : '') ?> value="Service Estimated Hours" name="tickets[]"> Service Time Estimate</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Fuel Charge') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Fuel Charge", $all_config) ? 'checked disabled' : (in_array("Service Fuel Charge", $value_config) ? "checked" : '') ?> value="Service Fuel Charge" name="tickets[]"> Service Fuel Surchage</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Preferred Staff') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Preferred Staff", $all_config) ? 'checked disabled' : (in_array("Service Preferred Staff", $value_config) ? "checked" : '') ?> value="Service Preferred Staff" name="tickets[]"> Select Preferred Staff</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Total Price') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Total Price", $all_config) ? 'checked disabled' : (in_array("Service Total Price", $value_config) ? "checked" : '') ?> value="Service Total Price" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will display the total cost of services for the <?= TICKET_NOUN ?> in this section."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Display Total Price</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Total Estimated Hours') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Total Estimated Hours", $all_config) ? 'checked disabled' : (in_array("Service Total Estimated Hours", $value_config) ? "checked" : '') ?> value="Service Total Estimated Hours" name="tickets[]"> Service Total Time Estimate</label>
							<?php } ?>
							<?php if($field_sort_field == 'Details Heading') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Details Heading", $all_config) ? 'checked disabled' : (in_array("Details Heading", $value_config) ? "checked" : '') ?> value="Details Heading" name="tickets[]"> <?= TICKET_NOUN ?> Heading</label>
							<?php } ?>
							<?php if($field_sort_field == 'Service Description') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Description", $all_config) ? 'checked disabled' : (in_array("Service Description", $value_config) ? "checked" : '') ?> value="Service Description" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify general details for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Description</label>
							<?php } ?>
                            <!--
							<?php if($field_sort_field == 'Details Tile') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Details Tile", $all_config) ? 'checked disabled' : (in_array("Details Tile", $value_config) ? "checked" : '') ?> value="Details Tile" name="tickets[]"> Tile Name</label>
							<?php } ?>
							<?php if($field_sort_field == 'Details Tab') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Details Tab", $all_config) ? 'checked disabled' : (in_array("Details Tab", $value_config) ? "checked" : '') ?> value="Details Tab" name="tickets[]"> Tab / Sub Tab</label>
							<?php } ?>
                            -->
							<?php if($field_sort_field == 'Details Where') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Details Where", $all_config) ? 'checked disabled' : (in_array("Details Where", $value_config) ? "checked" : '') ?> value="Details Where" name="tickets[]"> Tile Name</label>
							<?php } ?>
							<?php if($field_sort_field == 'Details Who') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Details Who", $all_config) ? 'checked disabled' : (in_array("Details Who", $value_config) ? "checked" : '') ?> value="Details Who" name="tickets[]"> Tab/Sub Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Details Why') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Details Why", $all_config) ? 'checked disabled' : (in_array("Details Why", $value_config) ? "checked" : '') ?> value="Details Why" name="tickets[]"> URL/Page</label>
							<?php } ?>
							<?php if($field_sort_field == 'Details What') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Details What", $all_config) ? 'checked disabled' : (in_array("Details What", $value_config) ? "checked" : '') ?> value="Details What" name="tickets[]"> What</label>
							<?php } ?>
							<?php if($field_sort_field == 'Details Position') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Details Position", $all_config) ? 'checked disabled' : (in_array("Details Position", $value_config) ? "checked" : '') ?> value="Details Position" name="tickets[]"> Position</label>
							<?php } ?>
							<?php if($field_sort_field == 'Details Checklist') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Details Checklist", $all_config) ? 'checked disabled' : (in_array("Details Checklist", $value_config) ? "checked" : '') ?> value="Details Checklist" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to create a simple list of items with checkboxes for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Checklist of Items</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Service Staff Checklist') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Service Staff Checklist">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Service Checklist' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_service_checklist',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_service_checklist',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_service_checklist" data-toggle="<?= in_array('ticket_service_checklist',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_service_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_service_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Service Checklist' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Service Staff Checklist", $all_config) ? 'checked disabled' : (in_array("Service Staff Checklist", $value_config) ? "checked" : '') ?> value="Service Staff Checklist" name="tickets[]"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to display a Checklist of the Services in the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
						<div class="block-group">
							<div class="fields_sortable">
							<?php foreach ($field_sort_order as $field_sort_field) { ?>
								<?php if($field_sort_field == 'Service Staff Checklist Group Cat Type') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Staff Checklist Group Cat Type", $all_config) ? 'checked disabled' : (in_array("Service Staff Checklist Group Cat Type", $value_config) ? "checked" : '') ?> value="Service Staff Checklist Group Cat Type" name="tickets[]"> Service Checklist Group by Tab/Service Tab</label>
								<?php } ?>
								<?php if($field_sort_field == 'Service Staff Checklist Another Room') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Staff Checklist Another Room", $all_config) ? 'checked disabled' : (in_array("Service Staff Checklist Another Room", $value_config) ? "checked" : '') ?> value="Service Staff Checklist Another Room" name="tickets[]"> Add Another Room Button</label>
								<?php } ?>
								<?php if($field_sort_field == 'Service Staff Checklist Another Room Copy Values') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Staff Checklist Another Room Copy Values", $all_config) ? 'checked disabled' : (in_array("Service Staff Checklist Another Room Copy Values", $value_config) ? "checked" : '') ?> value="Service Staff Checklist Another Room Copy Values" name="tickets[]"> Adding Another Room Copies Checked Boxes</label>
								<?php } ?>
								<?php if($field_sort_field == 'Service Staff Checklist Extra Billing') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Staff Checklist Extra Billing", $all_config) ? 'checked disabled' : (in_array("Service Staff Checklist Extra Billing", $value_config) ? "checked" : '') ?> value="Service Staff Checklist Extra Billing" name="tickets[]"> Extra Billing</label>
								<?php } ?>
								<?php if($field_sort_field == 'Service Staff Checklist Scroll To Accordion') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Staff Checklist Scroll To Accordion", $all_config) ? 'checked disabled' : (in_array("Service Staff Checklist Scroll To Accordion", $value_config) ? "checked" : '') ?> value="Service Staff Checklist Scroll To Accordion" name="tickets[]"> Scroll To Accordion On Click</label>
								<?php } ?>
								<?php if($field_sort_field == 'Service Staff Checklist History') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Staff Checklist History", $all_config) ? 'checked disabled' : (in_array("Service Staff Checklist History", $value_config) ? "checked" : '') ?> value="Service Staff Checklist History" name="tickets[]"> Display History</label>
								<?php } ?>
								<?php if($field_sort_field == 'Service Staff Checklist One Service Template Only') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Staff Checklist One Service Template Only", $all_config) ? 'checked disabled' : (in_array("Service Staff Checklist One Service Template Only", $value_config) ? "checked" : '') ?> value="Service Staff Checklist One Service Template Only" name="tickets[]"> Disable Service Template After One Is Chosen</label>
								<?php } ?>
								<?php if($field_sort_field == 'Service Staff Checklist Checked In Staff') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Staff Checklist Checked In Staff", $all_config) ? 'checked disabled' : (in_array("Service Staff Checklist Checked In Staff", $value_config) ? "checked" : '') ?> value="Service Staff Checklist Checked In Staff" name="tickets[]"> Checked In Staff Only</label>
								<?php } ?>
								<?php if($field_sort_field == 'Service Staff Checklist Default Customer Template') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Staff Checklist Default Customer Template", $all_config) ? 'checked disabled' : (in_array("Service Staff Checklist Default Customer Template", $value_config) ? "checked" : '') ?> value="Service Staff Checklist Default Customer Template" name="tickets[]"> Default Customer Template If Exists</label>
								<?php } ?>
							<?php } ?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Service Extra Billing') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Service Extra Billing">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Service Extra Billing' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_service_checklist',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_service_checklist',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_service_checklist" data-toggle="<?= in_array('ticket_service_checklist',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_service_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_service_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Service Extra Billing' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Service Extra Billing", $all_config) ? 'checked disabled' : (in_array("Service Extra Billing", $value_config) ? "checked" : '') ?> value="Service Extra Billing" name="tickets[]"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to view all of your Service's Extra Billing added."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
						<div class="block-group">
							<div class="fields_sortable">
							<?php foreach ($field_sort_order as $field_sort_field) { ?>
								<?php if($field_sort_field == 'Service Extra Billing Display Only If Exists') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Extra Billing Display Only If Exists", $all_config) ? 'checked disabled' : (in_array("Service Extra Billing Display Only If Exists", $value_config) ? "checked" : '') ?> value="Service Extra Billing Display Only If Exists" name="tickets[]"> Only Display If At Least One Extra Billing</label>
								<?php } ?>
								<?php if($field_sort_field == 'Service Extra Billing Add Option') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Service Extra Billing Add Option", $all_config) ? 'checked disabled' : (in_array("Service Extra Billing Add Option", $value_config) ? "checked" : '') ?> value="Service Extra Billing Add Option" name="tickets[]"> Hidden with Add Option</label>
								<?php } ?>
							<?php } ?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Equipment') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Equipment">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Equipment' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_equipment',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_equipment',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_equipment" data-toggle="<?= in_array('ticket_equipment',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_equipment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_equipment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Equipment' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Equipment", $all_config) ? 'checked disabled' : (in_array("Equipment", $value_config) ? "checked" : '') ?> value="Equipment" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to pull from the Equipment Tile for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Equipment Inline') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Inline", $all_config) ? 'checked disabled' : (in_array("Equipment Inline", $value_config) ? "checked" : '') ?> value="Equipment Inline" name="tickets[]"> Inline Display</label>
							<?php } ?>
							<?php if($field_sort_field == 'Equipment Category') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Category", $all_config) ? 'checked disabled' : (in_array("Equipment Category", $value_config) ? "checked" : '') ?> value="Equipment Category" name="tickets[]"> Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Equipment Make') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Make", $all_config) ? 'checked disabled' : (in_array("Equipment Make", $value_config) ? "checked" : '') ?> value="Equipment Make" name="tickets[]"> Make</label>
							<?php } ?>
							<?php if($field_sort_field == 'Equipment Model') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Model", $all_config) ? 'checked disabled' : (in_array("Equipment Model", $value_config) ? "checked" : '') ?> value="Equipment Model" name="tickets[]"> Model</label>
							<?php } ?>
							<?php if($field_sort_field == 'Equipment Unit') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Unit", $all_config) ? 'checked disabled' : (in_array("Equipment Unit", $value_config) ? "checked" : '') ?> value="Equipment Unit" name="tickets[]"> Unit</label>
							<?php } ?>
							<?php if($field_sort_field == 'Equipment Residue') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Residue", $all_config) ? 'checked disabled' : (in_array("Equipment Residue", $value_config) ? "checked" : '') ?> value="Equipment Residue" name="tickets[]"> Residue</label>
							<?php } ?>
							<?php if($field_sort_field == 'Equipment Hours') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Hours", $all_config) ? 'checked disabled' : (in_array("Equipment Hours", $value_config) ? "checked" : '') ?> value="Equipment Hours" name="tickets[]"> Hours</label>
							<?php } ?>
							<?php if($field_sort_field == 'Equipment Volume') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Volume", $all_config) ? 'checked disabled' : (in_array("Equipment Volume", $value_config) ? "checked" : '') ?> value="Equipment Volume" name="tickets[]"> Volume</label>
							<?php } ?>
							<?php if($field_sort_field == 'Equipment Rate') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Rate", $all_config) ? 'checked disabled' : (in_array("Equipment Rate", $value_config) ? "checked" : '') ?> value="Equipment Rate" name="tickets[]"> Rate</label>
							<?php } ?>
							<?php if($field_sort_field == 'Equipment Rate Options') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Rate Options", $all_config) ? 'checked disabled' : (in_array("Equipment Rate Options", $value_config) ? "checked" : '') ?> value="Equipment Rate Options" name="tickets[]"> Select Rate</label>
							<?php } ?>
							<?php if($field_sort_field == 'Equipment Cost') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Cost", $all_config) ? 'checked disabled' : (in_array("Equipment Cost", $value_config) ? "checked" : '') ?> value="Equipment Cost" name="tickets[]"> Cost</label>
							<?php } ?>
							<?php if($field_sort_field == 'Equipment Status') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Status", $all_config) ? 'checked disabled' : (in_array("Equipment Status", $value_config) ? "checked" : '') ?> value="Equipment Status" name="tickets[]"> Status</label>
							<?php } ?>
							<?php if($field_sort_field == 'Equipment Limit One') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Equipment Limit One", $all_config) ? 'checked disabled' : (in_array("Equipment Limit One", $value_config) ? "checked" : '') ?> value="Equipment Limit One" name="tickets[]"> Limit To One Equipment</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Checklist') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Checklist">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Checklist' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_checklist',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_checklist',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_checklist" data-toggle="<?= in_array('ticket_checklist',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Checklist' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Checklist", $all_config) ? 'checked disabled' : (in_array("Checklist", $value_config) ? "checked" : '') ?> value="Checklist" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to create a Checklist in the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Checklist Items') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Checklist Items">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Attached Checklists' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_view_checklist',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_view_checklist',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_view_checklist" data-toggle="<?= in_array('ticket_view_checklist',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_view_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_view_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Attached Checklists' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Checklist Items", $all_config) ? 'checked disabled' : (in_array("Checklist Items", $value_config) ? "checked" : '') ?> value="Checklist Items" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to display lists from the Checklists Tile in the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Charts') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Charts">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Charts' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_view_charts',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_view_charts',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_view_charts" data-toggle="<?= in_array('ticket_view_charts',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_view_charts',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_view_charts',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Charts' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Charts", $all_config) ? 'checked disabled' : (in_array("Charts", $value_config) ? "checked" : '') ?> value="Charts" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to attach forms from the Treatment Charts tile to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
						<div class="block-group">
							<h3>Attached Charts</h3>
							<label class="col-sm-2">Main Tab</label>
							<label class="col-sm-2">Sub Tab</label>
							<label class="col-sm-3">Heading</label>
							<label class="col-sm-4">Chart</label>
							<?php foreach ($attached_charts as $attached_chart) {
								$attached_chart = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `patientform` WHERE `patientformid` = '$attached_chart'")); ?>
								<div class="form-group attached_chart_block">
									<div class="col-sm-2">
										<select name="attached_chart_tab[]" class="chosen-select-deselect form-control" data-placeholder="Select a Tab...">
											<option></option>
											<?php $main_tabs = ['front_desk' => 'Front Desk', 'physiotherapy' => 'Physiotherapy', 'massage' => 'Massage Therapy', 'mvc' => 'MVC/MVA', 'wcb' => 'WCB'];
											foreach ($main_tabs as $main_tab => $main_tab_label) {
												echo '<option value="'.$main_tab.'" '.($attached_chart['tab'] == $main_tab ? 'selected' : '').'>'.$main_tab_label.'</option>';
											} ?>
										</select>
									</div>
									<div class="col-sm-2">
										<select name="attached_chart_subtab[]" class="chosen-select-deselect form-control" data-placeholder="Select a Sub Tab...">
											<option></option>
											<?php $sub_tabs = ['forms' => 'Patient Forms', 'assess' => 'Assessment', 'treatment' => 'Treatment', 'discharge' => 'Discharge'];
											foreach ($sub_tabs as $sub_tab => $sub_tab_label) {
												echo '<option value="'.$sub_tab.'" '.($attached_chart['category'] == $sub_tab ? 'selected' : '').'>'.$sub_tab_label.'</option>';
											} ?>
										</select>
									</div>
									<div class="col-sm-3">
										<select name="attached_chart_heading[]" class="chosen-select-deselect form-control" data-placeholder="Select a Heading...">
											<option></option>
											<?php $chart_headings = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT CONCAT(`tab`,`category`,`heading`), `tab`, `category`, `heading` FROM `patientform` WHERE `deleted` = 0"),MYSQLI_ASSOC);
											foreach ($chart_headings as $chart_heading) {
												echo '<option value="'.$chart_heading['heading'].'" '.($chart_heading['heading'] == $attached_chart['heading'] ? 'selected' : '').' data-tab="'.$chart_heading['tab'].'" data-subtab="'.$chart_heading['category'].'">'.$chart_heading['heading'].'</option>';
											} ?>
										</select>
									</div>
									<div class="col-sm-3">
										<select name="attached_chart[]" class="chosen-select-deselect form-control" data-placeholder="Select a Chart...">
											<option></option>
											<?php $charts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `patientform` WHERE `deleted` = 0 ORDER BY `sub_heading`"),MYSQLI_ASSOC);
											foreach ($charts as $chart) {
												echo '<option value="'.$chart['patientformid'].'" '.($chart['patientformid'] == $attached_chart['patientformid'] ? 'selected' : '').' data-tab="'.$chart['tab'].'" data-subtab="'.$chart['category'].'" data-heading="'.$chart['heading'].'">'.$chart['sub_heading'].'</option>';
											} ?>
										</select>
									</div>
									<div class="col-sm-2">
										<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addAttachedChart();">
										<img src="../img/remove.png" class="inline-img pull-right" onclick="removeAttachedChart(this);">
									</div>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Safety') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Safety">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Safety' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_safety',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_safety',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_safety" data-toggle="<?= in_array('ticket_safety',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_safety',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_safety',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Safety' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Safety", $all_config) ? 'checked disabled' : (in_array("Safety", $value_config) ? "checked" : '') ?> value="Safety" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to pull details from the Safety tile into the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Timer') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Timer">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Timer' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_timer',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_timer',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_timer" data-toggle="<?= in_array('view_ticket_timer',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_timer',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_timer',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Timer' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Timer", $all_config) ? 'checked disabled' : (in_array("Timer", $value_config) ? "checked" : '') ?> value="Timer" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to track time to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Time Tracking Block') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Block", $all_config) ? 'checked disabled' : (in_array("Time Tracking Block", $value_config) ? "checked" : '') ?> value="Time Tracking Block" name="tickets[]"> Time Tracking</label>
							<?php } ?>

							<?php if($field_sort_field == 'Day Tracking Block') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Day Tracking Block", $all_config) ? 'checked disabled' : (in_array("Day Tracking Block", $value_config) ? "checked" : '') ?> value="Day Tracking Block" name="tickets[]"> Day Tracking</label>
							<?php } ?>

							<?php if($field_sort_field == 'Time Tracking Estimate Complete') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Estimate Complete", $all_config) ? 'checked disabled' : (in_array("Time Tracking Estimate Complete", $value_config) ? "checked" : '') ?> value="Time Tracking Estimate Complete" name="tickets[]"> Estimated Time to Complete</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking Estimate QA') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Estimate QA", $all_config) ? 'checked disabled' : (in_array("Time Tracking Estimate QA", $value_config) ? "checked" : '') ?> value="Time Tracking Estimate QA" name="tickets[]"> Estimated Time to QA</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking Time Allotted') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Time Allotted", $all_config) ? 'checked disabled' : (in_array("Time Tracking Time Allotted", $value_config) ? "checked" : '') ?> value="Time Tracking Time Allotted" name="tickets[]"> Time Allotted</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking Current Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Current Time", $all_config) ? 'checked disabled' : (in_array("Time Tracking Current Time", $value_config) ? "checked" : '') ?> value="Time Tracking Current Time" name="tickets[]"> Current Time Table</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking Timer') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Timer", $all_config) ? 'checked disabled' : (in_array("Time Tracking Timer", $value_config) ? "checked" : '') ?> value="Time Tracking Timer" name="tickets[]"> Timer</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking Timer Manual') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Timer Manual", $all_config) ? 'checked disabled' : (in_array("Time Tracking Timer Manual", $value_config) ? "checked" : '') ?> value="Time Tracking Timer Manual" name="tickets[]"> Timer - Manually Track Time</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Materials') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Materials">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Materials' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_materials',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_materials" data-toggle="<?= in_array('ticket_materials',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_materials',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_materials',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Materials' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Materials", $all_config) ? 'checked disabled' : (in_array("Materials", $value_config) ? "checked" : '') ?> value="Materials" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to pull from the Materials tile for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Material Inline') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Material Inline", $all_config) ? 'checked disabled' : (in_array("Material Inline", $value_config) ? "checked" : '') ?> value="Material Inline" name="tickets[]"> Inline Materials</label>
							<?php } ?>
							<?php if($field_sort_field == 'Material Category') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Material Category", $all_config) ? 'checked disabled' : (in_array("Material Category", $value_config) ? "checked" : '') ?> value="Material Category" name="tickets[]"> Material Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Material Subcategory') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Material Subcategory", $all_config) ? 'checked disabled' : (in_array("Material Subcategory", $value_config) ? "checked" : '') ?> value="Material Subcategory" name="tickets[]"> Material Sub-Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Material Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Material Type", $all_config) ? 'checked disabled' : (in_array("Material Type", $value_config) ? "checked" : '') ?> value="Material Type" name="tickets[]"> Material Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Material Manual') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Material Manual", $all_config) ? 'checked disabled' : (in_array("Material Manual", $value_config) ? "checked" : '') ?> value="Material Manual" name="tickets[]"> Manual Material</label>
							<?php } ?>
							<?php if($field_sort_field == 'Material Quantity') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Material Quantity", $all_config) ? 'checked disabled' : (in_array("Material Quantity", $value_config) ? "checked" : '') ?> value="Material Quantity" name="tickets[]"> Quantity</label>
							<?php } ?>
							<?php if($field_sort_field == 'Material Volume') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Material Volume", $all_config) ? 'checked disabled' : (in_array("Material Volume", $value_config) ? "checked" : '') ?> value="Material Volume" name="tickets[]"> Volume</label>
							<?php } ?>
							<?php if($field_sort_field == 'Material Rate') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Material Rate", $all_config) ? 'checked disabled' : (in_array("Material Rate", $value_config) ? "checked" : '') ?> value="Material Rate" name="tickets[]"> Use Manual Rates</label>
							<?php } ?>
							<?php if($field_sort_field == 'Auto Check In Materials') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Auto Check In Materials", $all_config) ? 'checked disabled' : (in_array("Auto Check In Materials", $value_config) ? "checked" : '') ?> value="Auto Check In Materials" name="tickets[]"> Auto Check In Materials</label>
							<?php } ?>
							<?php if($field_sort_field == 'Auto Check Out Materials') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Auto Check Out Materials", $all_config) ? 'checked disabled' : (in_array("Auto Check Out Materials", $value_config) ? "checked" : '') ?> value="Auto Check Out Materials" name="tickets[]"> Auto Check Out Materials</label>
							<?php } ?>
						<?php } ?>
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Quantity Increment:</label>
								<div class="col-sm-8">
									<input type="number" name="ticket_material_increment" class="form-control" step="0.01" min="0" value="<?= get_config($dbc, 'ticket_material_increment') ?>">
								</div>
							</div>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Location Details') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Location Details">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Location Details' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_residue',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_residue" data-toggle="<?= in_array('ticket_residue',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Location Details' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Location Details", $all_config) ? 'checked disabled' : (in_array("Location Details", $value_config) ? "checked" : '') ?> value="Location Details" name="tickets[]">Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Location Details From') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Location Details From", $all_config) ? 'checked disabled' : (in_array("Location Details From", $value_config) ? "checked" : '') ?> value="Location Details From" name="tickets[]"> Location From</label>
							<?php } ?>
							<?php if($field_sort_field == 'Location Details From Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Location Details From Notes", $all_config) ? 'checked disabled' : (in_array("Location Details From Notes", $value_config) ? "checked" : '') ?> value="Location Details From Notes" name="tickets[]"> Location From Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Location Details To') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Location Details To", $all_config) ? 'checked disabled' : (in_array("Location Details To", $value_config) ? "checked" : '') ?> value="Location Details To" name="tickets[]"> Location To</label>
							<?php } ?>
							<?php if($field_sort_field == 'Location Details To Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Location Details To Notes", $all_config) ? 'checked disabled' : (in_array("Location Details To Notes", $value_config) ? "checked" : '') ?> value="Location Details To Notes" name="tickets[]"> Location To Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Location Details Volume') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Location Details Volume", $all_config) ? 'checked disabled' : (in_array("Location Details Volume", $value_config) ? "checked" : '') ?> value="Location Details Volume" name="tickets[]"> Location Volume</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Residue') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Residue">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Residue' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_residue',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_residue" data-toggle="<?= in_array('ticket_residue',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Residue' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Residue", $all_config) ? 'checked disabled' : (in_array("Residue", $value_config) ? "checked" : '') ?> value="Residue" name="tickets[]">Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Residue Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Residue Type", $all_config) ? 'checked disabled' : (in_array("Residue Type", $value_config) ? "checked" : '') ?> value="Residue Type" name="tickets[]"> Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Residue Quantity') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Residue Quantity", $all_config) ? 'checked disabled' : (in_array("Residue Quantity", $value_config) ? "checked" : '') ?> value="Residue Quantity" name="tickets[]"> Quantity</label>
							<?php } ?>
							<?php if($field_sort_field == 'Residue Volume') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Residue Volume", $all_config) ? 'checked disabled' : (in_array("Residue Volume", $value_config) ? "checked" : '') ?> value="Residue Volume" name="tickets[]"> Volume</label>
							<?php } ?>
							<?php if($field_sort_field == 'Residue Rate') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Residue Rate", $all_config) ? 'checked disabled' : (in_array("Residue Rate", $value_config) ? "checked" : '') ?> value="Residue Rate" name="tickets[]"> Use Manual Rates</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Other List') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Other List">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Other Products' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_residue',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_residue" data-toggle="<?= in_array('ticket_residue',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Other Products' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Other List", $all_config) ? 'checked disabled' : (in_array("Other List", $value_config) ? "checked" : '') ?> value="Other List" name="tickets[]">Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Other Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Other Type", $all_config) ? 'checked disabled' : (in_array("Other Type", $value_config) ? "checked" : '') ?> value="Other Type" name="tickets[]"> Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Other Quantity') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Other Quantity", $all_config) ? 'checked disabled' : (in_array("Other Quantity", $value_config) ? "checked" : '') ?> value="Other Quantity" name="tickets[]"> Quantity</label>
							<?php } ?>
							<?php if($field_sort_field == 'Other Volume') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Other Volume", $all_config) ? 'checked disabled' : (in_array("Other Volume", $value_config) ? "checked" : '') ?> value="Other Volume" name="tickets[]"> Volume</label>
							<?php } ?>
							<?php if($field_sort_field == 'Other Rate') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Other Rate", $all_config) ? 'checked disabled' : (in_array("Other Rate", $value_config) ? "checked" : '') ?> value="Other Rate" name="tickets[]"> Use Manual Rates</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Shipping List') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Shipping List">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Shipping List' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_residue',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_residue" data-toggle="<?= in_array('ticket_residue',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Shipping List' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Shipping List", $all_config) ? 'checked disabled' : (in_array("Shipping List", $value_config) ? "checked" : '') ?> value="Shipping List" name="tickets[]">Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Shipping List Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Shipping List Type", $all_config) ? 'checked disabled' : (in_array("Shipping List Type", $value_config) ? "checked" : '') ?> value="Shipping List Type" name="tickets[]"> Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Shipping List Class') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Shipping List Class", $all_config) ? 'checked disabled' : (in_array("Shipping List Class", $value_config) ? "checked" : '') ?> value="Shipping List Class" name="tickets[]"> Class</label>
							<?php } ?>
							<?php if($field_sort_field == 'Shipping List Subclass') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Shipping List Subclass", $all_config) ? 'checked disabled' : (in_array("Shipping List Subclass", $value_config) ? "checked" : '') ?> value="Shipping List Subclass" name="tickets[]"> Subclass</label>
							<?php } ?>
							<?php if($field_sort_field == 'Shipping List Unit') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Shipping List Unit", $all_config) ? 'checked disabled' : (in_array("Shipping List Unit", $value_config) ? "checked" : '') ?> value="Shipping List Unit" name="tickets[]"> Unit #</label>
							<?php } ?>
							<?php if($field_sort_field == 'Shipping List PG') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Shipping List PG", $all_config) ? 'checked disabled' : (in_array("Shipping List PG", $value_config) ? "checked" : '') ?> value="Shipping List PG" name="tickets[]"> PG</label>
							<?php } ?>
							<?php if($field_sort_field == 'Shipping List Quantity') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Shipping List Quantity", $all_config) ? 'checked disabled' : (in_array("Shipping List Quantity", $value_config) ? "checked" : '') ?> value="Shipping List Quantity" name="tickets[]"> Quantity</label>
							<?php } ?>
							<?php if($field_sort_field == 'Shipping List Volume') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Shipping List Volume", $all_config) ? 'checked disabled' : (in_array("Shipping List Volume", $value_config) ? "checked" : '') ?> value="Shipping List Volume" name="tickets[]"> Volume</label>
							<?php } ?>
							<?php if($field_sort_field == 'Shipping List Rate') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Shipping List Rate", $all_config) ? 'checked disabled' : (in_array("Shipping List Rate", $value_config) ? "checked" : '') ?> value="Shipping List Rate" name="tickets[]"> Use Manual Rates</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Reading') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Reading">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Monitor Readings' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_residue',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_residue" data-toggle="<?= in_array('ticket_residue',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Monitor Readings' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Reading", $all_config) ? 'checked disabled' : (in_array("Reading", $value_config) ? "checked" : '') ?> value="Reading" name="tickets[]">Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Readings CO') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Readings CO", $all_config) ? 'checked disabled' : (in_array("Readings CO", $value_config) ? "checked" : '') ?> value="Readings CO" name="tickets[]"> CO Level</label>
							<?php } ?>
							<?php if($field_sort_field == 'Readings O2') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Readings O2", $all_config) ? 'checked disabled' : (in_array("Readings O2", $value_config) ? "checked" : '') ?> value="Readings O2" name="tickets[]"> O2 Level</label>
							<?php } ?>
							<?php if($field_sort_field == 'Readings LEL') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Readings LEL", $all_config) ? 'checked disabled' : (in_array("Readings LEL", $value_config) ? "checked" : '') ?> value="Readings LEL" name="tickets[]"> LEL Level</label>
							<?php } ?>
							<?php if($field_sort_field == 'Readings H2S') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Readings H2S", $all_config) ? 'checked disabled' : (in_array("Readings H2S", $value_config) ? "checked" : '') ?> value="Readings H2S" name="tickets[]"> H2S Level</label>
							<?php } ?>
							<?php if($field_sort_field == 'Readings Bump') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Readings Bump", $all_config) ? 'checked disabled' : (in_array("Readings Bump", $value_config) ? "checked" : '') ?> value="Readings Bump" name="tickets[]"> Bump Test</label>
							<?php } ?>
							<?php if($field_sort_field == 'Readings Arrival') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Readings Arrival", $all_config) ? 'checked disabled' : (in_array("Readings Arrival", $value_config) ? "checked" : '') ?> value="Readings Arrival" name="tickets[]"> Operator Check-In Arrival</label>
							<?php } ?>
							<?php if($field_sort_field == 'Readings Departure') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Readings Departure", $all_config) ? 'checked disabled' : (in_array("Readings Departure", $value_config) ? "checked" : '') ?> value="Readings Departure" name="tickets[]"> Operator Check-In Departure</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Tank Reading') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Tank Reading">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Tank Readings' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_residue',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_residue" data-toggle="<?= in_array('ticket_residue',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Tank Readings' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Tank Reading", $all_config) ? 'checked disabled' : (in_array("Tank Reading", $value_config) ? "checked" : '') ?> value="Tank Reading" name="tickets[]">Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Tank Readings Tank #') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Tank Readings Tank #", $all_config) ? 'checked disabled' : (in_array("Tank Readings Tank #", $value_config) ? "checked" : '') ?> value="Tank Readings Tank #" name="tickets[]"> Tank #</label>
							<?php } ?>
							<?php if($field_sort_field == 'Tank Readings Opening') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Tank Readings Opening", $all_config) ? 'checked disabled' : (in_array("Tank Readings Opening", $value_config) ? "checked" : '') ?> value="Tank Readings Opening" name="tickets[]"> Opening</label>
							<?php } ?>
							<?php if($field_sort_field == 'Tank Readings Closing') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Tank Readings Closing", $all_config) ? 'checked disabled' : (in_array("Tank Readings Closing", $value_config) ? "checked" : '') ?> value="Tank Readings Closing" name="tickets[]"> Closing</label>
							<?php } ?>
							<?php if($field_sort_field == 'Tank Readings Watercut') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Tank Readings Watercut", $all_config) ? 'checked disabled' : (in_array("Tank Readings Watercut", $value_config) ? "checked" : '') ?> value="Tank Readings Watercut" name="tickets[]"> Watercut</label>
							<?php } ?>
							<?php if($field_sort_field == 'Tank Readings Oil') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Tank Readings Oil", $all_config) ? 'checked disabled' : (in_array("Tank Readings Oil", $value_config) ? "checked" : '') ?> value="Tank Readings Oil" name="tickets[]"> Oil</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Miscellaneous') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Miscellaneous">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Miscellaneous' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_miscellaneous',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_miscellaneous',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_miscellaneous" data-toggle="<?= in_array('ticket_miscellaneous',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_miscellaneous',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_miscellaneous',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Miscellaneous' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Miscellaneous", $all_config) ? 'checked disabled' : (in_array("Miscellaneous", $value_config) ? "checked" : '') ?> value="Miscellaneous" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add Miscellaneous items to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Miscellaneous Inline') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Miscellaneous Inline", $all_config) ? 'checked disabled' : (in_array("Miscellaneous Inline", $value_config) ? "checked" : '') ?> value="Miscellaneous Inline" name="tickets[]"> Inline Items</label>
							<?php } ?>
							<?php if($field_sort_field == 'Miscellaneous Name') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Miscellaneous Name", $all_config) ? 'checked disabled' : (in_array("Miscellaneous Name", $value_config) ? "checked" : '') ?> value="Miscellaneous Name" name="tickets[]"> Name</label>
							<?php } ?>
							<?php if($field_sort_field == 'Miscellaneous Price') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Miscellaneous Price", $all_config) ? 'checked disabled' : (in_array("Miscellaneous Price", $value_config) ? "checked" : '') ?> value="Miscellaneous Price" name="tickets[]"> Unit Price</label>
							<?php } ?>
							<?php if($field_sort_field == 'Miscellaneous Quantity') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Miscellaneous Quantity", $all_config) ? 'checked disabled' : (in_array("Miscellaneous Quantity", $value_config) ? "checked" : '') ?> value="Miscellaneous Quantity" name="tickets[]"> Quantity</label>
							<?php } ?>
							<?php if($field_sort_field == 'Miscellaneous Total') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Miscellaneous Total", $all_config) ? 'checked disabled' : (in_array("Miscellaneous Total", $value_config) ? "checked" : '') ?> value="Miscellaneous Total" name="tickets[]"> Total Price</label>
							<?php } ?>
							<?php if($field_sort_field == 'Miscellaneous Billing') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Miscellaneous Billing", $all_config) ? 'checked disabled' : (in_array("Miscellaneous Billing", $value_config) ? "checked" : '') ?> value="Miscellaneous Billing" name="tickets[]"> Summary</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Inventory') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Inventory">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Inventory' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_inventory',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_inventory',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_inventory" data-toggle="<?= in_array('ticket_inventory',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_inventory',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_inventory',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Inventory' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Inventory", $all_config) ? 'checked disabled' : (in_array("Inventory", $value_config) ? "checked" : '') ?> value="Inventory" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to pull from the <?= INVENTORY_TILE ?> tile for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Piece Tabs</label>
								<div class="col-sm-8">
									<input type="text" name="piece_types" class="form-control" value="<?= get_config($dbc, 'piece_types') ?>">
								</div>
							</div>
						<?php } ?>
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Inventory Basic Inline') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Inline", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Inline", $value_config) ? "checked" : '') ?> value="Inventory Basic Inline" name="tickets[]"> Inline Inventory</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Category') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Category", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Category", $value_config) ? "checked" : '') ?> value="Inventory Basic Category" name="tickets[]"> Inventory Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Part') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Part", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Part", $value_config) ? "checked" : '') ?> value="Inventory Basic Part" name="tickets[]"> Part #</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Inventory') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Inventory", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Inventory", $value_config) ? "checked" : '') ?> value="Inventory Basic Inventory" name="tickets[]"> Inventory Name</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Price') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Price", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Price", $value_config) ? "checked" : '') ?> value="Inventory Basic Price" name="tickets[]"> Unit Price</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Quantity') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Quantity", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Quantity", $value_config) ? "checked" : '') ?> value="Inventory Basic Quantity" name="tickets[]"> Quantity</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Total') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Total", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Total", $value_config) ? "checked" : '') ?> value="Inventory Basic Total" name="tickets[]"> Total Price</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Piece Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Piece Type", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Piece Type", $value_config) ? "checked" : '') ?> value="Inventory Basic Piece Type" name="tickets[]"> Piece Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic PO Line') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic PO Line", $all_config) ? 'checked disabled' : (in_array("Inventory Basic PO Line", $value_config) ? "checked" : '') ?> value="Inventory Basic PO Line" name="tickets[]"> PO Line Item #</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Vendor') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Vendor", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Vendor", $value_config) ? "checked" : '') ?> value="Inventory Basic Vendor" name="tickets[]"> Vendor</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Weight') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Weight", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Weight", $value_config) ? "checked" : '') ?> value="Inventory Basic Weight" name="tickets[]"> Weight</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Units') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Units", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Units", $value_config) ? "checked" : '') ?> value="Inventory Basic Units" name="tickets[]"> Units</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Dimensions') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Dimensions", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Dimensions", $value_config) ? "checked" : '') ?> value="Inventory Basic Dimensions" name="tickets[]"> Dimensions</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Dimension Units') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Dimension Units", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Dimension Units", $value_config) ? "checked" : '') ?> value="Inventory Basic Dimension Units" name="tickets[]"> Dimension Units</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Used') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Used", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Used", $value_config) ? "checked" : '') ?> value="Inventory Basic Used" name="tickets[]"> Picked</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Received') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Received", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Received", $value_config) ? "checked" : '') ?> value="Inventory Basic Received" name="tickets[]"> Received</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Discrepancy') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Discrepancy", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Discrepancy", $value_config) ? "checked" : '') ?> value="Inventory Basic Discrepancy" name="tickets[]"> Discrepancy</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Back Order') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Back Order", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Back Order", $value_config) ? "checked" : '') ?> value="Inventory Basic Back Order" name="tickets[]"> Back Order</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Location') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Location", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Location", $value_config) ? "checked" : '') ?> value="Inventory Basic Location" name="tickets[]"> Location</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Basic Billing') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Basic Billing", $all_config) ? 'checked disabled' : (in_array("Inventory Basic Billing", $value_config) ? "checked" : '') ?> value="Inventory Basic Billing" name="tickets[]"> Summary</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Inventory General') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Inventory General">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'General Cargo / Inventory Information' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_inventory_general',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_inventory_general',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_inventory_general" data-toggle="<?= in_array('ticket_inventory_general',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_inventory_general',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_inventory_general',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'General Cargo / Inventory Information' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Inventory General", $all_config) ? 'checked disabled' : (in_array("Inventory General", $value_config) ? "checked" : '') ?> value="Inventory General" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify Cargo pieces for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Inventory General Piece Count Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Piece Count Type", $all_config) ? 'checked disabled' : (in_array("Inventory General Piece Count Type", $value_config) ? "checked" : '') ?> value="Inventory General Piece Count Type" name="tickets[]"> Piece Count & Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Piece') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Piece", $all_config) ? 'checked disabled' : (in_array("Inventory General Piece", $value_config) ? "checked" : '') ?> value="Inventory General Piece" name="tickets[]"> Shipment Count</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General All Copy') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General All Copy", $all_config) ? 'checked disabled' : (in_array("Inventory General All Copy", $value_config) ? "checked" : '') ?> value="Inventory General All Copy" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify within a <?= TICKET_NOUN ?> that all remaining pieces have the same details as the first piece. It will appear as a checkbox that is only displayed on the first piece."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Copy First Piece Details</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Piece Copy') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Piece Copy", $all_config) ? 'checked disabled' : (in_array("Inventory General Piece Copy", $value_config) ? "checked" : '') ?> value="Inventory General Piece Copy" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify within a <?= TICKET_NOUN ?> that any piece has the same details as the first piece. It will appear as a checkbox that is displayed on all pieces after the first piece."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Copy Details from First Piece</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Piece Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Piece Type", $all_config) ? 'checked disabled' : (in_array("Inventory General Piece Type", $value_config) ? "checked" : '') ?> value="Inventory General Piece Type" name="tickets[]"> Piece Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General PO Number') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General PO Number", $all_config) ? 'checked disabled' : (in_array("Inventory General PO Number", $value_config) ? "checked" : '') ?> value="Inventory General PO Number" name="tickets[]"> Purchase Order Number</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General PO Number Dropdown') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General PO Number Dropdown", $all_config) ? 'checked disabled' : (in_array("Inventory General PO Number Dropdown", $value_config) ? "checked" : '') ?> value="Inventory General PO Number Dropdown" name="tickets[]"> Dropdown Purchase Order Number</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General PO Number Dropdown Multiple') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General PO Number Dropdown Multiple", $all_config) ? 'checked disabled' : (in_array("Inventory General PO Number Dropdown Multiple", $value_config) ? "checked" : '') ?> value="Inventory General PO Number Dropdown Multiple" name="tickets[]"> Dropdown Purchase Order Number - Multiple</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General PO Item') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General PO Item", $all_config) ? 'checked disabled' : (in_array("Inventory General PO Item", $value_config) ? "checked" : '') ?> value="Inventory General PO Item" name="tickets[]"> Purchase Order Item</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General PO Line Item') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General PO Line Item", $all_config) ? 'checked disabled' : (in_array("Inventory General PO Line Item", $value_config) ? "checked" : '') ?> value="Inventory General PO Line Item" name="tickets[]"> Purchase Order Line Item</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General PO Dropdown') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General PO Dropdown", $all_config) ? 'checked disabled' : (in_array("Inventory General PO Dropdown", $value_config) ? "checked" : '') ?> value="Inventory General PO Dropdown" name="tickets[]"> Dropdown PO Line Item</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General PO Dropdown Multiple') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General PO Dropdown Multiple", $all_config) ? 'checked disabled' : (in_array("Inventory General PO Dropdown Multiple", $value_config) ? "checked" : '') ?> value="Inventory General PO Dropdown Multiple" name="tickets[]"> Dropdown PO Line Item - Multiple with Range</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General PO Line Read') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General PO Line Read", $all_config) ? 'checked disabled' : (in_array("Inventory General PO Line Read", $value_config) ? "checked" : '') ?> value="Inventory General PO Line Read" name="tickets[]"> Read Only PO Line Item</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General PO Line Sort') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General PO Line Sort", $all_config) ? 'checked disabled' : (in_array("Inventory General PO Line Sort", $value_config) ? "checked" : '') ?> value="Inventory General PO Line Sort" name="tickets[]"> Sort By PO Line #</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Piece Dim Weight') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Piece Dim Weight", $all_config) ? 'checked disabled' : (in_array("Inventory General Piece Dim Weight", $value_config) ? "checked" : '') ?> value="Inventory General Piece Dim Weight" name="tickets[]"> Dimension / Weight per Piece</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Weight') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Weight", $all_config) ? 'checked disabled' : (in_array("Inventory General Weight", $value_config) ? "checked" : '') ?> value="Inventory General Weight" name="tickets[]"> Weight</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Units') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Units", $all_config) ? 'checked disabled' : (in_array("Inventory General Units", $value_config) ? "checked" : '') ?> value="Inventory General Units" name="tickets[]"> Weight Units</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Weight Convert KG to LB') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Weight Convert KG to LB", $all_config) ? 'checked disabled' : (in_array("Inventory General Weight Convert KG to LB", $value_config) ? "checked" : '') ?> value="Inventory General Weight Convert KG to LB" name="tickets[]"> Weight - Convert KG to LB</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Dimensions') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Dimensions", $all_config) ? 'checked disabled' : (in_array("Inventory General Dimensions", $value_config) ? "checked" : '') ?> value="Inventory General Dimensions" name="tickets[]"> Dimensions</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Dimension Units') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Dimension Units", $all_config) ? 'checked disabled' : (in_array("Inventory General Dimension Units", $value_config) ? "checked" : '') ?> value="Inventory General Dimension Units" name="tickets[]"> Dimension Units</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Site') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Site", $all_config) ? 'checked disabled' : (in_array("Inventory General Site", $value_config) ? "checked" : '') ?> value="Inventory General Site" name="tickets[]"> Site</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Complete') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Complete", $all_config) ? 'checked disabled' : (in_array("Inventory General Complete", $value_config) ? "checked" : '') ?> value="Inventory General Complete" name="tickets[]"> Piece Completed</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Shipment Count Weight') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Shipment Count Weight", $all_config) ? 'checked disabled' : (in_array("Inventory General Shipment Count Weight", $value_config) ? "checked" : '') ?> value="Inventory General Shipment Count Weight" name="tickets[]"> Shipment Count & Weight</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Notes", $all_config) ? 'checked disabled' : (in_array("Inventory General Notes", $value_config) ? "checked" : '') ?> value="Inventory General Notes" name="tickets[]"> Piece Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Total Count Weight') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Total Count Weight", $all_config) ? 'checked disabled' : (in_array("Inventory General Total Count Weight", $value_config) ? "checked" : '') ?> value="Inventory General Total Count Weight" name="tickets[]"> Total Shipment Count & Weight</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Detail') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Detail", $all_config) ? 'checked disabled' : (in_array("Inventory General Detail", $value_config) ? "checked" : '') ?> value="Inventory General Detail" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add specific items into each piece of Cargo for the <?= TICKET_NOUN ?>. It will use the fields from the Detailed Cargo section."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Shipment Piece Details</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Detail by Pallet') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Detail by Pallet", $all_config) ? 'checked disabled' : (in_array("Inventory General Detail by Pallet", $value_config) ? "checked" : '') ?> value="Inventory General Detail by Pallet" name="tickets[]"> List Inventory by Pallet</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Total Summary') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Total Summary", $all_config) ? 'checked disabled' : (in_array("Inventory General Total Summary", $value_config) ? "checked" : '') ?> value="Inventory General Total Summary" name="tickets[]"> Summary Count & Weight</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Pallet Default Locked') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Pallet Default Locked", $all_config) ? 'checked disabled' : (in_array("Inventory General Pallet Default Locked", $value_config) ? "checked" : '') ?> value="Inventory General Pallet Default Locked" name="tickets[]"> Hide Pallets until Unlocked</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Manual Add Pieces') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Manual Add Pieces", $all_config) ? 'checked disabled' : (in_array("Inventory General Manual Add Pieces", $value_config) ? "checked" : '') ?> value="Inventory General Manual Add Pieces" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="If this is active, you will need to click a button before any pieces will be automatically added to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Manually Add Pieces</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory General Manual Remove Pieces') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory General Manual Remove Pieces", $all_config) ? 'checked disabled' : (in_array("Inventory General Manual Remove Pieces", $value_config) ? "checked" : '') ?> value="Inventory General Manual Remove Pieces" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="If this is active, you will see a button that will remove a piece and update the piece count on the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Manually Remove Pieces</label>
							<?php } ?>
						<?php } ?>
						</div>
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Incomplete Inventory Reminder Email:</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="incomplete_inventory_reminder_email" value="<?= get_config($dbc, 'incomplete_inventory_reminder_email') ?>">
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Inventory Detail') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Inventory Detail">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Detailed Cargo / Inventory Information' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_inventory_detailed',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_inventory_detailed',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_inventory_detailed" data-toggle="<?= in_array('ticket_inventory_detailed',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_inventory_detailed',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_inventory_detailed',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Detailed Cargo / Inventory Information' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Inventory Detail", $all_config) ? 'checked disabled' : (in_array("Inventory Detail", $value_config) ? "checked" : '') ?> value="Inventory Detail" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify detailed cargo for the <?= TICKET_NOUN ?> that will be synced into the <?= INVENTORY_NOUN ?> tile."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Inventory Detail Category') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Category", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Category", $value_config) ? "checked" : '') ?> value="Inventory Detail Category" name="tickets[]"> Inventory Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Unique') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Unique", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Unique", $value_config) ? "checked" : '') ?> value="Inventory Detail Unique" name="tickets[]"> No Inventory Dropdown</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Quantity') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Quantity", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Quantity", $value_config) ? "checked" : '') ?> value="Inventory Detail Quantity" name="tickets[]"> Quantity Expected</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Site') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Site", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Site", $value_config) ? "checked" : '') ?> value="Inventory Detail Site" name="tickets[]"> Site</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Piece Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Piece Type", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Piece Type", $value_config) ? "checked" : '') ?> value="Inventory Detail Piece Type" name="tickets[]"> Piece Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Customer Order') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Customer Order", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Customer Order", $value_config) ? "checked" : '') ?> value="Inventory Detail Customer Order" name="tickets[]"> Customer Order #</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail PO Num') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail PO Num", $all_config) ? 'checked disabled' : (in_array("Inventory Detail PO Num", $value_config) ? "checked" : '') ?> value="Inventory Detail PO Num" name="tickets[]"> Line Item PO #</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail PO Line') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail PO Line", $all_config) ? 'checked disabled' : (in_array("Inventory Detail PO Line", $value_config) ? "checked" : '') ?> value="Inventory Detail PO Line" name="tickets[]"> PO Line Item #</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail PO Dropdown') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail PO Dropdown", $all_config) ? 'checked disabled' : (in_array("Inventory Detail PO Dropdown", $value_config) ? "checked" : '') ?> value="Inventory Detail PO Dropdown" name="tickets[]"> Dropdown PO Line Item #</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail PO Read') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail PO Read", $all_config) ? 'checked disabled' : (in_array("Inventory Detail PO Read", $value_config) ? "checked" : '') ?> value="Inventory Detail PO Read" name="tickets[]"> Read Only PO Line Item #</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail PO Sort') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail PO Sort", $all_config) ? 'checked disabled' : (in_array("Inventory Detail PO Sort", $value_config) ? "checked" : '') ?> value="Inventory Detail PO Sort" name="tickets[]"> Sort by PO Line #</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Vendor') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Vendor", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Vendor", $value_config) ? "checked" : '') ?> value="Inventory Detail Vendor" name="tickets[]"> Vendor</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Weight') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Weight", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Weight", $value_config) ? "checked" : '') ?> value="Inventory Detail Weight" name="tickets[]"> Weight</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Units') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Units", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Units", $value_config) ? "checked" : '') ?> value="Inventory Detail Units" name="tickets[]"> Units</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Net Weight') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Net Weight", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Net Weight", $value_config) ? "checked" : '') ?> value="Inventory Detail Net Weight" name="tickets[]"> Net Weight</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Net Units') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Net Units", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Net Units", $value_config) ? "checked" : '') ?> value="Inventory Detail Net Units" name="tickets[]"> Net Weight Units</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Gross Weight') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Gross Weight", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Gross Weight", $value_config) ? "checked" : '') ?> value="Inventory Detail Gross Weight" name="tickets[]"> Gross Weight</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Gross Units') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Gross Units", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Gross Units", $value_config) ? "checked" : '') ?> value="Inventory Detail Gross Units" name="tickets[]"> Gross Weight Units</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Dimensions') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Dimensions", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Dimensions", $value_config) ? "checked" : '') ?> value="Inventory Detail Dimensions" name="tickets[]"> Dimensions</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Dimension Units') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Dimension Units", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Dimension Units", $value_config) ? "checked" : '') ?> value="Inventory Detail Dimension Units" name="tickets[]"> Dimension Units</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Used') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Used", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Used", $value_config) ? "checked" : '') ?> value="Inventory Detail Used" name="tickets[]"> Picked</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Received') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Received", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Received", $value_config) ? "checked" : '') ?> value="Inventory Detail Received" name="tickets[]"> Received</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Discrepancy') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Discrepancy", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Discrepancy", $value_config) ? "checked" : '') ?> value="Inventory Detail Discrepancy" name="tickets[]"> Discrepancy</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Discrepancy Yes No') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Discrepancy Yes No", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Discrepancy Yes No", $value_config) ? "checked" : '') ?> value="Inventory Detail Discrepancy Yes No" name="tickets[]"> Discrepancy - Yes/No</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Back Order') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Back Order", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Back Order", $value_config) ? "checked" : '') ?> value="Inventory Detail Back Order" name="tickets[]"> Back Order</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Location') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Location", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Location", $value_config) ? "checked" : '') ?> value="Inventory Detail Location" name="tickets[]"> Location</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Detail Manual Add') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Detail Manual Add", $all_config) ? 'checked disabled' : (in_array("Inventory Detail Manual Add", $value_config) ? "checked" : '') ?> value="Inventory Detail Manual Add" name="tickets[]"> Manually Add Items</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Inventory Return') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Inventory Return">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Return Information' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_inventory_return',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_inventory_return',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_inventory_return" data-toggle="<?= in_array('ticket_inventory_return',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_inventory_return',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_inventory_return',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Return Information' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Inventory Return", $all_config) ? 'checked disabled' : (in_array("Inventory Return", $value_config) ? "checked" : '') ?> value="Inventory Return" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify that specific cargo for the <?= TICKET_NOUN ?> is new or a returned item, and add additional details."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Inventory Return Same') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Return Same", $all_config) ? 'checked disabled' : (in_array("Inventory Return Same", $value_config) ? "checked" : '') ?> value="Inventory Return Same" name="tickets[]"> Separate Return Information</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Return Item') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Return Item", $all_config) ? 'checked disabled' : (in_array("Inventory Return Item", $value_config) ? "checked" : '') ?> value="Inventory Return Item" name="tickets[]"> Return Item</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Return Details') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Return Details", $all_config) ? 'checked disabled' : (in_array("Inventory Return Details", $value_config) ? "checked" : '') ?> value="Inventory Return Details" name="tickets[]"> Return Details</label>
							<?php } ?>
							<?php if($field_sort_field == 'Inventory Return ATA') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Inventory Return ATA", $all_config) ? 'checked disabled' : (in_array("Inventory Return ATA", $value_config) ? "checked" : '') ?> value="Inventory Return ATA" name="tickets[]"> ATA Carnet</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Purchase Orders') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Purchase Orders">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_purchase_orders',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_purchase_orders',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_purchase_orders" data-toggle="<?= in_array('ticket_purchase_orders',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_purchase_orders',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_purchase_orders',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Purchase Orders", $all_config) ? 'checked disabled' : (in_array("Purchase Orders", $value_config) ? "checked" : '') ?> value="Purchase Orders" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to create and attach Purchase Orders to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Attached Purchase Orders') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Attached Purchase Orders">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_purchase_orders',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_purchase_orders',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_purchase_orders" data-toggle="<?= in_array('ticket_purchase_orders',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_purchase_orders',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_purchase_orders',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Attached Purchase Orders", $all_config) ? 'checked disabled' : (in_array("Attached Purchase Orders", $value_config) ? "checked" : '') ?> value="Attached Purchase Orders" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to create and attach Purchase Orders to the <?= TICKET_NOUN ?> from the Purchase Orders tile."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'PO Name') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PO Name", $all_config) ? 'checked disabled' : (in_array("PO Name", $value_config) ? "checked" : '') ?> value="PO Name" name="tickets[]"> PO</label>
							<?php } ?>
							<?php if($field_sort_field == 'PO 3rd Party') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PO 3rd Party", $all_config) ? 'checked disabled' : (in_array("PO 3rd Party", $value_config) ? "checked" : '') ?> value="PO 3rd Party" name="tickets[]"> 3rd Party Invoice Number</label>
							<?php } ?>
							<?php if($field_sort_field == 'PO Invoice') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PO Invoice", $all_config) ? 'checked disabled' : (in_array("PO Invoice", $value_config) ? "checked" : '') ?> value="PO Invoice" name="tickets[]"> Invoice</label>
							<?php } ?>
							<?php if($field_sort_field == 'PO Price') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PO Price", $all_config) ? 'checked disabled' : (in_array("PO Price", $value_config) ? "checked" : '') ?> value="PO Price" name="tickets[]"> Price</label>
							<?php } ?>
							<?php if($field_sort_field == 'PO Mark Up') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PO Mark Up", $all_config) ? 'checked disabled' : (in_array("PO Mark Up", $value_config) ? "checked" : '') ?> value="PO Mark Up" name="tickets[]"> Mark Up</label>
							<?php } ?>
							<?php if($field_sort_field == 'PO Total') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("PO Total", $all_config) ? 'checked disabled' : (in_array("PO Total", $value_config) ? "checked" : '') ?> value="PO Total" name="tickets[]"> Total</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Delivery') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Delivery">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Delivery Details' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_delivery',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_delivery',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_delivery" data-toggle="<?= in_array('ticket_delivery',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_delivery',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_delivery',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Delivery Details' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Delivery", $all_config) ? 'checked disabled' : (in_array("Delivery", $value_config) ? "checked" : '') ?> value="Delivery" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add additional locations and times to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Assigned Equipment Inline", $all_config) ? 'checked disabled' : (in_array("Assigned Equipment Inline", $value_config) ? "checked" : '') ?> value="Assigned Equipment Inline" name="tickets[]"> Inline Equipment</label>
						<?php } ?>
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Assigned Equipment Category') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Assigned Equipment Category", $all_config) ? 'checked disabled' : (in_array("Assigned Equipment Category", $value_config) ? "checked" : '') ?> value="Assigned Equipment Category" name="tickets[]"> Assigned Equipment Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Assigned Equipment Make') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Assigned Equipment Make", $all_config) ? 'checked disabled' : (in_array("Assigned Equipment Make", $value_config) ? "checked" : '') ?> value="Assigned Equipment Make" name="tickets[]"> Assigned Equipment</label>
							<?php } ?>
							<?php if($field_sort_field == 'Assigned Equipment Model') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Assigned Equipment", $all_config) ? 'checked disabled' : (in_array("Assigned Equipment", $value_config) ? "checked" : '') ?> value="Assigned Equipment" name="tickets[]"> Assigned Equipment</label>
							<?php } ?>
							<?php if($field_sort_field == 'Assigned Equipment') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Assigned Equipment", $all_config) ? 'checked disabled' : (in_array("Assigned Equipment", $value_config) ? "checked" : '') ?> value="Assigned Equipment" name="tickets[]"> Assigned Equipment</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Stops') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", $all_config) ? 'checked disabled' : (in_array("Delivery Stops", $value_config) ? "checked" : '') ?> value="Delivery Stops" name="tickets[]"> Multi-<?= TICKET_NOUN ?> Delivery Stops</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Stops Order') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops Order", $all_config) ? 'checked disabled' : (in_array("Delivery Stops Order", $value_config) ? "checked" : '') ?> value="Delivery Stops Order" name="tickets[]"> Multi-<?= TICKET_NOUN ?> Delivery Order#</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Stops Volume') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops Volume", $all_config) ? 'checked disabled' : (in_array("Delivery Stops Volume", $value_config) ? "checked" : '') ?> value="Delivery Stops Volume" name="tickets[]"> Multi-<?= TICKET_NOUN ?> Delivery Volume</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup", $value_config) ? "checked" : '')) ?> value="Delivery Pickup" name="tickets[]"> Multi-Stop Location</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Address') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Address", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Address", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Address" name="tickets[]"> Multi-Stop Address</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Address Google') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Address Google", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Address Google", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Address Google" name="tickets[]"> Multi-Stop Google Map Link</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Coordinates') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Coordinates", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Coordinates", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Coordinates" name="tickets[]"> Multi-Stop Lat/Lng Coordinates</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Equipment Category') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Equipment Category", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Equipment Category", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Equipment Category" name="tickets[]"> Multi-Stop Equipment Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Equipment') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Equipment Make", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Equipment Make", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Equipment Make" name="tickets[]"> Multi-Stop Equipment Make</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Equipment') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Equipment Model", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Equipment Model", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Equipment Model" name="tickets[]"> Multi-Stop Equipment Model</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Equipment') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Equipment", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Equipment", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Equipment" name="tickets[]"> Multi-Stop Equipment</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Customer') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Customer", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Customer", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Customer" name="tickets[]"> Multi-Stop Customer</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Client') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Client", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Client", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Client" name="tickets[]"> Multi-Stop Client</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Phone') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Phone", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Phone", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Phone" name="tickets[]"> Multi-Stop Contact Info</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Phone2') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Phone2", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Phone2", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Phone2" name="tickets[]"> Multi-Stop Additional Contact</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Type') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Type", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Type", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Type" name="tickets[]"> Multi-Stop Tab</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Volume') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Volume", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Volume", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Volume" name="tickets[]"> Multi-Stop Volume</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Cube') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Cube", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Cube", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Cube" name="tickets[]"> Multi-Stop Cube Size</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup ETA') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup ETA", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup ETA", $value_config) ? "checked" : '')) ?> value="Delivery Pickup ETA" name="tickets[]"> Multi-Stop ETA Window</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Customer Est Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Customer Est Time", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Customer Est Time", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Customer Est Time" name="tickets[]"> Multi-Stop Customer Estimated Time</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Estimate') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Estimate", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Estimate", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Estimate" name="tickets[]"> Multi-Stop Time Estimate</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Date') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Date", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Date", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Date" name="tickets[]"> Multi-Stop Date</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Order') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Order", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Order", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Order" name="tickets[]"> Multi-Stop Order#</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Timeframe') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Timeframe", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Timeframe", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Timeframe" name="tickets[]"> Multi-Stop Time Frame</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Arrival') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Arrival", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Arrival", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Arrival" name="tickets[]"> Multi-Stop Arrival</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Departure') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Departure", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Departure", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Departure" name="tickets[]"> Multi-Stop Departure</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Description') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Description", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Description", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Description" name="tickets[]"> Multi-Stop Details</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Upload') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Upload", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Upload", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Upload" name="tickets[]"> Multi-Stop Upload</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Default Services') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Default Services", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Default Services", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Default Services" name="tickets[]"> Multi-Stop Default Services</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Service List') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Service List", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Service List", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Service List" name="tickets[]"> Multi-Stop Services</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Warehouse Only') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Warehouse Only", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Warehouse Only", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Warehouse Only" name="tickets[]"> Multi-Stop Warehouse Only</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Populate Warehouse Address') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Populate Warehouse Address", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Populate Warehouse Address", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Populate Warehouse Address" name="tickets[]"> Multi-Stop Populate Warehouse Address</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Status') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Status", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Status", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Status" name="tickets[]"> Multi-Stop Status</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Notes", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Notes", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Notes" name="tickets[]"> Multi-Stop Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Calendar History') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Calendar History", $all_config) ? 'checked disabled' : (in_array("Delivery Calendar History", $value_config) ? "checked" : '')) ?> value="Delivery Calendar History" name="tickets[]"> Multi-Stop Calendar History</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Populate Google Link') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Populate Google Link", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Populate Google Link", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Populate Google Link" name="tickets[]"> Auto Populate Google Maps Link</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Pickup Dropoff Map') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Pickup Dropoff Map", $all_config) ? 'checked disabled' : (in_array("Delivery Pickup Dropoff Map", $value_config) ? "checked" : '')) ?> value="Delivery Pickup Dropoff Map" name="tickets[]"> Map of Multi-Stop Locations</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Warehouse Hours of Operation') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Stops", array_merge($all_config,$value_config)) ? 'disabled' : (in_array("Delivery Warehouse Hours of Operation", $all_config) ? 'checked disabled' : (in_array("Delivery Warehouse Hours of Operation", $value_config) ? "checked" : '')) ?> value="Delivery Warehouse Hours of Operation" name="tickets[]"> Warehouse Limit to Hours of Operation</label>
							<?php } ?>
						<?php } ?>
						</div>
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<div class="form-group">
								<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will set the default scheduled time for the Warehouse stops."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Warehouse Scheduled Time</label>
								<div class="col-sm-8">
									<input type="text" name="ticket_warehouse_start_time" class="form-control datetimepicker" value="<?= get_config($dbc, 'ticket_warehouse_start_time') ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will set the minimum time for the Scheduled Time field to this value."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Delivery Scheduled Time Minimum Time</label>
								<div class="col-sm-8">
									<input type="text" name="ticket_delivery_time_mintime" class="form-control datetimepicker" value="<?= get_config($dbc, 'ticket_delivery_time_mintime') ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will set the maximum time for the Scheduled Time field to this value."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Delivery Scheduled Time Maximum Time</label>
								<div class="col-sm-8">
									<input type="text" name="ticket_delivery_time_maxtime" class="form-control datetimepicker" value="<?= get_config($dbc, 'ticket_delivery_time_maxtime') ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will automatically set the Delivery Timeframe based on the Scheduled time for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Delivery Default Timeframe Duration (Hours)</label>
								<div class="col-sm-8">
									<input type="number" name="delivery_timeframe_default" class="form-control" value="<?= get_config($dbc, 'delivery_timeframe_default') ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Delivery Tabs</label>
								<div class="col-sm-8">
									<input type="text" name="delivery_types" class="form-control" value="<?= get_config($dbc, 'delivery_types') ?>" onchange="loadDefaultDeliveryDropdown();">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will automatically set the Delivery Duration for a Specific Delivery tab."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Delivery Default Estimate</label>
								<div class="col-sm-4">
                                    <?php $delivery_default_pickup_type = get_config($dbc, 'delivery_default_pickup_type'); ?>
									<select name="delivery_default_pickup_type" data-placeholder="Select tab" class="chosen-select-deselect"><option />
                                        <?php foreach(explode(',',get_config($dbc, 'delivery_types')) as $del_type) { ?>
                                            <option <?= $del_type == $delivery_default_pickup_type ? 'selected' : '' ?> value="<?= $del_type ?>"><?= $del_type ?></option>
                                        <?php } ?>
                                    </select>
								</div>
								<div class="col-sm-4">
									<input type="text" name="delivery_default_pickup_time" class="form-control timepicker" value="<?= get_config($dbc, 'delivery_default_pickup_time') ?>">
								</div>
							</div>
							<div class="form-group">
								<?php $delivery_default_tabs = get_config($dbc, 'delivery_default_tabs'); ?>
								<label class="col-sm-4 control-label">Populate new <?= TICKET_NOUN ?> with a delivery for each of these tabs<?= $delivery_default_tabs != '' && $tab != '' ? ' (Default: '.$delivery_default_tabs.')' : '' ?>:</label>
								<div class="col-sm-8">
									<input type="text" name="delivery_default_tabs<?= $tab == '' ? '' : '_'.$tab ?>" placeholder="Enter Tabs" class="form-control" value="<?= get_config($dbc, 'delivery_default_tabs'.($tab == '' ? '' : '_'.$tab)) ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Default Delivery Tab</label>
								<div class="col-sm-8">
									<select name="delivery_type_default" class="chosen-select-deselect">
										<option></option>
										<?php $delivery_type_default = get_config($dbc, 'delivery_type_default');
										foreach(array_filter(explode(',', get_config($dbc, 'delivery_types'))) as $delivery_type) { ?>
											<option value="<?= $delivery_type ?>" <?= $delivery_type_default == $delivery_type ? 'selected' : '' ?>><?= $delivery_type ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<?php $delivery_type_contacts = get_config($dbc, 'delivery_type_contacts'); ?>
								<label class="col-sm-4 control-label">Populate Delivery Tabs with <?= CONTACTS_TILE ?>, such as Sites, Warehouses<?= $delivery_type_contacts != '' && $tab != '' ? ' (Default: '.$delivery_type_contacts.')' : '' ?>:</label>
								<div class="col-sm-8">
									<select name="delivery_type_contacts<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Tab" class="chosen-select-deselect" data-default="<?= !empty($tab) ? $delivery_type_contacts : '' ?>" onchange="getRestrictContactsList(this);"><option></option>
										<?php $tab_delivery_type_contacts = get_config($dbc, 'delivery_type_contacts'.($tab == '' ? '' : '_'.$tab));
										foreach(explode(',',get_config($dbc, 'all_contact_tabs')) as $contact_cat) { ?>
											<option <?= $contact_cat == $tab_delivery_type_contacts ? 'selected' : '' ?> value="<?= $contact_cat ?>"><?= $contact_cat ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group delivery_restrict_contacts" <?= empty($tab_delivery_type_contacts) && empty($delivery_type_contacts) ? 'style="display:none;"' : '' ?>>
								<?php $delivery_restrict_contacts = explode(',',get_config($dbc, 'delivery_restrict_contacts')); ?>
								<label class="col-sm-4 control-label">Restrict <?= !empty($tab_delivery_type_contacts) ? $tab_delivery_type_contacts : $delivery_type_contacts ?></label>
								<div class="col-sm-8 delivery_restrict_contacts_div">
									<?php $tab_delivery_restrict_contacts = explode(',',get_config($dbc, 'delivery_restrict_contacts'.($tab == '' ? '' : '_'.$tab)));
									$all_contacts = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `deleted` = 0 AND `status` > 0 AND `category` = '".(!empty($tab_delivery_type_contacts) ? $tab_delivery_type_contacts : $delivery_type_contacts)."'"));
									foreach($all_contacts as $delivery_contact) { ?>
										<label class="form-checkbox"><input type="checkbox" name="delivery_restrict_contacts<?= $tab == '' ? '' : '_'.$tab ?>" value="<?= $delivery_contact['contactid'] ?>" <?= in_array($delivery_contact['contactid'],$delivery_restrict_contacts) && !empty($tab) ? 'checked disabled' : '' ?> <?= in_array($delivery_contact['contactid'],$tab_delivery_restrict_contacts) ? 'checked' : '' ?>> <?= $delivery_contact['full_name'] ?></label>
									<?php } ?>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Delivery Extra KM Service</label>
								<div class="col-sm-8">
									<?php $delivery_km_service = get_config($dbc, 'delivery_km_service'); ?>
									<select class="chosen-select-deselect" data-placeholder="Select a Service" name="delivery_km_service">
										<option></option>
										<?php $service_list = mysqli_query($dbc, "SELECT `category`, `service_type`, `heading`, `serviceid` FROM `services` WHERE `deleted`=0 ORDER BY `category`, `service_type`, `heading`");
										while($service = mysqli_fetch_assoc($service_list)) { ?>
											<option <?= $delivery_km_service == $service['serviceid'] ? 'selected' : '' ?> value="<?= $service['serviceid'] ?>"><?= $service['category'].' '.$services['service_type'].': '.$service['heading'] ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Auto Create Unscheduled Stop On Status Change:</label>
								<div class="col-sm-8">
									<select multiple class="chosen-select-deselect" data-placeholder="Select Statuses" name="auto_create_unscheduled[]">
										<?php $ticket_statuses = explode(',',get_config($dbc, 'ticket_status'));
										foreach ($ticket_statuses as $ticket_status) { ?>
											<option <?= in_array($ticket_status, $auto_create_unscheduled) ? 'selected' : '' ?> value="<?= $ticket_status ?>"><?= $ticket_status ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
							<div class="delivery_type_colors">
								<?php include('../Ticket/field_config_field_list_delivery_colors.php'); ?>
							</div>
							<div class="block-group delivery_restriction_div">
								<?php $delivery_restrictions = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_delivery_restrictions` WHERE `ticket_type` = '$tab'")); ?>
								<h3>Security Level Restrictions</h3>
								<div class="form-group">
									<label class="col-sm-4 control-label">Security Levels:</label>
									<div class="col-sm-8">
										<?php foreach(explode(',', $delivery_restrictions['security_level']) as $restriction_level) { ?>
											<div class="delivery_restriction_role">
												<div class="col-sm-10" style="padding: 0;">
													<select name="delivery_restriction_security_level" class="chosen-select-deselect">
														<option></option>
														<?php $on_security = get_security_levels($dbc);
														foreach($on_security as $category => $value) {
															if($value != 'super') {
																echo '<option value="'.$value.'" '.($restriction_level == $value ? 'selected' : '').'>'.$category.'</option>';
															}
														} ?>
													</select>
												</div>
												<div class="col-sm-2" style="padding: 0;">
													<img src="../img/remove.png" class="inline-img pull-right" onclick="removeDeliveryRestriction(this);">
													<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addDeliveryRestriction();">
												</div>
												<div class="clearfix"></div>
											</div>
										<?php } ?>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Scheduled Date Minimum:</label>
									<div class="col-sm-8">
										<select name="delivery_restriction_to_do_date_min" class="chosen-select-deselect">
											<option></option>
											<?php for($day_i = -31; $day_i <= 31; $day_i++) {
												echo '<option value="'.$day_i.'" '.($delivery_restrictions['to_do_date_min'] == $day_i && $delivery_restrictions['to_do_date_min'] != '' ? 'selected' : '').'>'.($day_i == 0 ? 'Current Day' : ($day_i > 0 ? '+'.$day_i.' Days' : $day_i.' Days')).'</option>';
											} ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Scheduled Date Maximum:</label>
									<div class="col-sm-8">
										<select name="delivery_restriction_to_do_date_max" class="chosen-select-deselect">
											<option></option>
											<?php for($day_i = -31; $day_i <= 31; $day_i++) {
												echo '<option value="'.$day_i.'" '.($delivery_restrictions['to_do_date_max'] == $day_i && $delivery_restrictions['to_do_date_max'] != '' ? 'selected' : '').'>'.($day_i == 0 ? 'Current Day' : ($day_i > 0 ? '+'.$day_i.' Days' : $day_i.' Days')).'</option>';
											} ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Scheduled Time Minimum:</label>
									<div class="col-sm-8">
										<select name="delivery_restriction_to_do_start_time_min" class="chosen-select-deselect">
											<option></option>
											<?php for($hours_i = -24; $hours_i <= 24; $hours_i++) {
												echo '<option value="'.$hours_i.'" '.($delivery_restrictions['to_do_start_time_min'] == $hours_i && $delivery_restrictions['to_do_start_time_min'] != '' ? 'selected' : '').'>'.($hours_i == 0 ? 'Current Time' : ($hours_i > 0 ? '+'.$hours_i.' Hours' : $hours_i.' Hours')).'</option>';
											} ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Scheduled Time Maximum:</label>
									<div class="col-sm-8">
										<select name="delivery_restriction_to_do_start_time_max" class="chosen-select-deselect">
											<option></option>
											<?php for($hours_i = -24; $hours_i <= 24; $hours_i++) {
												echo '<option value="'.$hours_i.'" '.($delivery_restrictions['to_do_start_time_max'] == $hours_i && $delivery_restrictions['to_do_start_time_max'] != '' ? 'selected' : '').'>'.($hours_i == 0 ? 'Current Time' : ($hours_i > 0 ? '+'.$hours_i.' Hours' : $hours_i.' Hours')).'</option>';
											} ?>
										</select>
									</div>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Delivery Summary') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Delivery Summary">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Delivery Summary' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_delivery',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_delivery',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_delivery" data-toggle="<?= in_array('ticket_delivery',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_delivery',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_delivery',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Delivery Summary' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Delivery Summary", $all_config) ? 'checked disabled' : (in_array("Delivery Summary", $value_config) ? "checked" : '') ?> value="Delivery Summary" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add additional locations and times to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Delivery Summary Stop') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Summary Stop", $all_config) ? 'checked disabled' : (in_array("Delivery Summary Stop", $value_config) ? "checked" : '') ?> value="Delivery Summary Stop" name="tickets[]"> Delivery Stop</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Summary Client') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Summary Client", $all_config) ? 'checked disabled' : (in_array("Delivery Summary Client", $value_config) ? "checked" : '') ?> value="Delivery Summary Client" name="tickets[]"> Delivery Client</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Summary Address') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Summary Address", $all_config) ? 'checked disabled' : (in_array("Delivery Summary Address", $value_config) ? "checked" : '') ?> value="Delivery Summary Address" name="tickets[]"> Delivery Address</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Summary Services') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Summary Services", $all_config) ? 'checked disabled' : (in_array("Delivery Summary Services", $value_config) ? "checked" : '') ?> value="Delivery Summary Services" name="tickets[]"> Delivery Services</label>
							<?php } ?>
							<?php if($field_sort_field == 'Delivery Summary Status') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Delivery Summary Status", $all_config) ? 'checked disabled' : (in_array("Delivery Summary Status", $value_config) ? "checked" : '') ?> value="Delivery Summary Status" name="tickets[]"> Delivery Status</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Transport') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Transport">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Transport Log' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Transport Log' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Transport", $all_config) ? 'checked disabled' : (in_array("Transport", $value_config) ? "checked" : '') ?> value="Transport" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify information about the source and the destination for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="transport_group">
							<?php $renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($tab) ? 'tickets' : 'tickets_'.$tab)."' AND `accordion` = 'Transport Origin'"))['accordion_name']; ?>
							<h4 class="accordion_label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify the origin of the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= !empty($renamed_accordion) ? $renamed_accordion : 'Origin' ?><?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
								<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_transport_origin',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_transport_origin',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
									<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_transport_origin" data-toggle="<?= in_array('ticket_transport_origin',$unlocked_tabs) ? 1 : 0 ?>">
									<img class="inline-img" style="<?= in_array('ticket_transport_origin',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
									<img class="inline-img" style="<?= in_array('ticket_transport_origin',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></h4>
							<div class="col-sm-12 accordion_rename" style="display: none;" data-accordion="Transport Origin">
								<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Origin' ?>" onfocusout="updateAccordion(this);" class="form-control">
							</div>
							<div class="fields_sortable">
							<?php foreach ($field_sort_order as $field_sort_field) { ?>
								<?php if($field_sort_field == 'Transport Origin Contact') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Origin Contact", $all_config) ? 'checked disabled' : (in_array("Transport Origin Contact", $value_config) ? "checked" : '') ?> value="Transport Origin Contact" name="tickets[]"> Contact</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Origin Name') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Origin Name", $all_config) ? 'checked disabled' : (in_array("Transport Origin Name", $value_config) ? "checked" : '') ?> value="Transport Origin Name" name="tickets[]"> Name of Location</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Origin') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Origin", $all_config) ? 'checked disabled' : (in_array("Transport Origin", $value_config) ? "checked" : '') ?> value="Transport Origin" name="tickets[]"> Address</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Origin Country') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Origin Country", $all_config) ? 'checked disabled' : (in_array("Transport Origin Country", $value_config) ? "checked" : '') ?> value="Transport Origin Country" name="tickets[]"> Country</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Origin Save Contact') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Origin Save Contact", $all_config) ? 'checked disabled' : (in_array("Transport Origin Save Contact", $value_config) ? "checked" : '') ?> value="Transport Origin Save Contact" name="tickets[]">
										<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to have the option to sync details back to the original contact."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Save Address to Contact</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Origin Link') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Origin Link", $all_config) ? 'checked disabled' : (in_array("Transport Origin Link", $value_config) ? "checked" : '') ?> value="Transport Origin Link" name="tickets[]"> Google Link</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Origin Warehouse') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Origin Warehouse", $all_config) ? 'checked disabled' : (in_array("Transport Origin Warehouse", $value_config) ? "checked" : '') ?> value="Transport Origin Warehouse" name="tickets[]"> Warehouse</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Origin Arrival') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Origin Arrival", $all_config) ? 'checked disabled' : (in_array("Transport Origin Arrival", $value_config) ? "checked" : '') ?> value="Transport Origin Arrival" name="tickets[]"> Arrival</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Origin Departure') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Origin Departure", $all_config) ? 'checked disabled' : (in_array("Transport Origin Departure", $value_config) ? "checked" : '') ?> value="Transport Origin Departure" name="tickets[]"> Departure</label>
								<?php } ?>
							<?php } ?>
							</div>
							<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
								<div class="form-group">
									<?php $transport_log_contact = get_config($dbc, 'transport_log_contact'); ?>
									<label class="col-sm-4 control-label">Origin Contact Tab<?= $transport_log_contact != '' && $tab != '' ? ' (Default: '.$transport_log_contact.')' : '' ?>:</label>
									<div class="col-sm-8">
										<select name="transport_log_contact<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Tab" class="chosen-select-deselect"><option></option>
											<?php $tab_transport_log_contact = get_config($dbc, 'transport_log_contact'.($tab == '' ? '' : '_'.$tab));
											foreach(explode(',',get_config($dbc, 'all_contact_tabs')) as $category) { ?>
												<option <?= $category == $tab_transport_log_contact ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							<?php } ?>
						</div>
						<div class="transport_group">
							<?php $renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($tab) ? 'tickets' : 'tickets_'.$tab)."' AND `accordion` = 'Transport Destination'"))['accordion_name']; ?>
							<h4 class="accordion_label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify the destination of the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= !empty($renamed_accordion) ? $renamed_accordion : 'Destination' ?><?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
								<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_transport_destination',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_transport_destination',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
									<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_transport_destination" data-toggle="<?= in_array('ticket_transport_destination',$unlocked_tabs) ? 1 : 0 ?>">
									<img class="inline-img" style="<?= in_array('ticket_transport_destination',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
									<img class="inline-img" style="<?= in_array('ticket_transport_destination',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></h4>
							<div class="col-sm-12 accordion_rename" style="display: none;" data-accordion="Transport Destination">
								<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Destination' ?>" onfocusout="updateAccordion(this);" class="form-control">
							</div>
							<div class="fields_sortable">
							<?php foreach ($field_sort_order as $field_sort_field) { ?>
								<?php if($field_sort_field == 'Transport Destination Contact') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Destination Contact", $all_config) ? 'checked disabled' : (in_array("Transport Destination Contact", $value_config) ? "checked" : '') ?> value="Transport Destination Contact" name="tickets[]"> Contact</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Destination Name') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Destination Name", $all_config) ? 'checked disabled' : (in_array("Transport Destination Name", $value_config) ? "checked" : '') ?> value="Transport Destination Name" name="tickets[]"> Name of Location</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Destination') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Destination", $all_config) ? 'checked disabled' : (in_array("Transport Destination", $value_config) ? "checked" : '') ?> value="Transport Destination" name="tickets[]"> Address</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Destination Country') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Destination Country", $all_config) ? 'checked disabled' : (in_array("Transport Destination Country", $value_config) ? "checked" : '') ?> value="Transport Destination Country" name="tickets[]"> Country</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Destination Save Contact') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Destination Save Contact", $all_config) ? 'checked disabled' : (in_array("Transport Destination Save Contact", $value_config) ? "checked" : '') ?> value="Transport Destination Save Contact" name="tickets[]">
										<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to have the option to sync details back to the original contact."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Save Address to Contact</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Destination Link') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Destination Link", $all_config) ? 'checked disabled' : (in_array("Transport Destination Link", $value_config) ? "checked" : '') ?> value="Transport Destination Link" name="tickets[]"> Google Link</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Destination Warehouse') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Destination Warehouse", $all_config) ? 'checked disabled' : (in_array("Transport Destination Warehouse", $value_config) ? "checked" : '') ?> value="Transport Destination Warehouse" name="tickets[]"> Warehouse</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Destination Arrival') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Destination Arrival", $all_config) ? 'checked disabled' : (in_array("Transport Destination Arrival", $value_config) ? "checked" : '') ?> value="Transport Destination Arrival" name="tickets[]"> Arrival</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Destination Departure') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Destination Departure", $all_config) ? 'checked disabled' : (in_array("Transport Destination Departure", $value_config) ? "checked" : '') ?> value="Transport Destination Departure" name="tickets[]"> Departure</label>
								<?php } ?>
							<?php } ?>
							</div>
							<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
								<div class="form-group">
									<?php $transport_destination_contact = get_config($dbc, 'transport_destination_contact'); ?>
									<label class="col-sm-4 control-label">Destination Contact Tab<?= $transport_destination_contact != '' && $tab != '' ? ' (Default: '.$transport_destination_contact.')' : '' ?>:</label>
									<div class="col-sm-8">
										<select name="transport_destination_contact<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Tab" class="chosen-select-deselect"><option></option>
											<?php $tab_transport_destination_contact = get_config($dbc, 'transport_destination_contact'.($tab == '' ? '' : '_'.$tab));
											foreach(explode(',',get_config($dbc, 'all_contact_tabs')) as $category) { ?>
												<option <?= $category == $tab_transport_destination_contact ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
							<?php } ?>
						</div>
						<div class="transport_group">
							<?php $renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($tab) ? 'tickets' : 'tickets_'.$tab)."' AND `accordion` = 'Transport Carrier'"))['accordion_name']; ?>
							<h4 class="accordion_label"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify the details about the transportation for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= !empty($renamed_accordion) ? $renamed_accordion : 'Carrier Details' ?><?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
								<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_transport_details',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_transport_details',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
									<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_transport_details" data-toggle="<?= in_array('ticket_transport_details',$unlocked_tabs) ? 1 : 0 ?>">
									<img class="inline-img" style="<?= in_array('ticket_transport_details',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
									<img class="inline-img" style="<?= in_array('ticket_transport_details',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></h4>
							<div class="col-sm-12 accordion_rename" style="display: none;" data-accordion="Transport Carrier">
								<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Carrier Details' ?>" onfocusout="updateAccordion(this);" class="form-control">
							</div>
							<div class="fields_sortable">
							<?php foreach ($field_sort_order as $field_sort_field) { ?>
								<?php if($field_sort_field == 'Transport Carrier') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Carrier", $all_config) ? 'checked disabled' : (in_array("Transport Carrier", $value_config) ? "checked" : '') ?> value="Transport Carrier" name="tickets[]"> Contact</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Type') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Type", $all_config) ? 'checked disabled' : (in_array("Transport Type", $value_config) ? "checked" : '') ?> value="Transport Type" name="tickets[]"> Shipment Tab</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Number') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Number", $all_config) ? 'checked disabled' : (in_array("Transport Number", $value_config) ? "checked" : '') ?> value="Transport Number" name="tickets[]"> Bill of Lading #</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Billed') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Billed", $all_config) ? 'checked disabled' : (in_array("Transport Billed", $value_config) ? "checked" : '') ?> value="Transport Billed" name="tickets[]"> Billed</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Container') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Container", $all_config) ? 'checked disabled' : (in_array("Transport Container", $value_config) ? "checked" : '') ?> value="Transport Container" name="tickets[]"> Container #</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Manifest') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Manifest", $all_config) ? 'checked disabled' : (in_array("Transport Manifest", $value_config) ? "checked" : '') ?> value="Transport Manifest" name="tickets[]"> Manifest #</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Ship Date') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Ship Date", $all_config) ? 'checked disabled' : (in_array("Transport Ship Date", $value_config) ? "checked" : '') ?> value="Transport Ship Date" name="tickets[]"> Ship Date</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Arrive Date') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Arrive Date", $all_config) ? 'checked disabled' : (in_array("Transport Arrive Date", $value_config) ? "checked" : '') ?> value="Transport Arrive Date" name="tickets[]"> Arrival Date</label>
								<?php } ?>
								<?php if($field_sort_field == 'Transport Warehouse') { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Transport Warehouse", $all_config) ? 'checked disabled' : (in_array("Transport Warehouse", $value_config) ? "checked" : '') ?> value="Transport Warehouse" name="tickets[]"> Warehouse Location</label>
								<?php } ?>
							<?php } ?>
							</div>
							<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
								<div class="form-group">
									<?php $transport_carrier_category = get_config($dbc, 'transport_carrier_category'); ?>
									<label class="col-sm-4 control-label">Carrier Contact Tab<?= $transport_carrier_category != '' && $tab != '' ? ' (Default: '.$transport_carrier_category.')' : '' ?>:</label>
									<div class="col-sm-8">
										<select name="transport_carrier_category<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Tab" class="chosen-select-deselect"><option></option>
											<?php $tab_transport_destination_contact = get_config($dbc, 'transport_carrier_category'.($tab == '' ? '' : '_'.$tab));
											foreach(explode(',',get_config($dbc, 'all_contact_tabs')) as $category) { ?>
												<option <?= $category == $tab_transport_destination_contact ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
											<?php } ?>
										</select>
									</div>
								</div>
								<div class="form-group">
									<label class="col-sm-4 control-label">Transport Tabs:</label>
									<div class="col-sm-8">
										<input type="text" name="transport_types" class="form-control" value="<?= get_config($dbc, 'transport_types') ?>">
									</div>
								</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Documents') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Documents">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Documents' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_documents',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_documents',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_documents" data-toggle="<?= in_array('view_ticket_documents',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_documents',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_documents',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Documents' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Documents", $all_config) ? 'checked disabled' : (in_array("Documents", $value_config) ? "checked" : '') ?> value="Documents" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Project Docs') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Project Docs", $all_config) ? 'checked disabled' : (in_array("Project Docs", $value_config) ? "checked" : '') ?> value="Project Docs" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to view files uploaded to the <?= PROJECT_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= PROJECT_NOUN ?> Documents</label>
							<?php } ?>
							<?php if($field_sort_field == 'Contact Docs') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Contact Docs", $all_config) ? 'checked disabled' : (in_array("Contact Docs", $value_config) ? "checked" : '') ?> value="Contact Docs" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to view files uploaded to the <?= CONTACTS_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= CONTACTS_NOUN ?> Documents</label>
							<?php } ?>
							<?php if($field_sort_field == 'Documents Docs') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Documents Docs", $all_config) ? 'checked disabled' : (in_array("Documents Docs", $value_config) ? "checked" : '') ?> value="Documents Docs" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to upload files or attach pictures to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Uploaded Documents</label>
							<?php } ?>
							<?php if($field_sort_field == 'Documents Links') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Documents Links", $all_config) ? 'checked disabled' : (in_array("Documents Links", $value_config) ? "checked" : '') ?> value="Documents Links" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add links to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Attach Links</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Check Out') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Check Out">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Check Out' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_checkout',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_checkout',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_checkout" data-toggle="<?= in_array('ticket_checkout',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_checkout',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_checkout',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Check Out' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Check Out', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Check Out", $all_config) ? 'checked disabled' : (in_array("Check Out", $value_config) ? "checked" : '') ?> value="Check Out" name="tickets[]">
							<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to mark individuals, equipment, or other supplies as done for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable Check Out</label>
					<?php } ?>
					<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Check Out Member Pick Up', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Check Out Member Pick Up", $all_config) ? 'checked disabled' : (in_array("Check Out Member Pick Up", $value_config) ? "checked" : '') ?> value="Check Out Member Pick Up" name="tickets[]"> Enable Member Pick Up</label>
					<?php } ?>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Checkout Hide All Button') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkout Hide All Button", $all_config) ? 'checked disabled' : (in_array("Checkout Hide All Button", $value_config) ? "checked" : '') ?> value="Checkout Hide All Button" name="tickets[]"> Hide Check Out All Button</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkout Show Checked In Only') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkout Show Checked In Only", $all_config) ? 'checked disabled' : (in_array("Checkout Show Checked In Only", $value_config) ? "checked" : '') ?> value="Checkout Show Checked In Only" name="tickets[]"> Show Checked In Only</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkout Staff') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkout Staff", $all_config) ? 'checked disabled' : (in_array("Checkout Staff", $value_config) ? "checked" : '') ?> value="Checkout Staff" name="tickets[]"> Check Out Staff</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkout Staff_Tasks') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkout Staff_Tasks", $all_config) ? 'checked disabled' : (in_array("Checkout Staff_Tasks", $value_config) ? "checked" : '') ?> value="Checkout Staff_Tasks" name="tickets[]"> Check Out Staff by Task</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkout Delivery') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkout Delivery", $all_config) ? 'checked disabled' : (in_array("Checkout Delivery", $value_config) ? "checked" : '') ?> value="Checkout Delivery" name="tickets[]"> Check Out Deliveries</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkout Clients') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkout Clients", $all_config) ? 'checked disabled' : (in_array("Checkout Clients", $value_config) ? "checked" : '') ?> value="Checkout Clients" name="tickets[]"> Check Out Clients</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkout Members') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkout Members", $all_config) ? 'checked disabled' : (in_array("Checkout Members", $value_config) ? "checked" : '') ?> value="Checkout Members" name="tickets[]"> Check Out Members</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkout material') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkout material", $all_config) ? 'checked disabled' : (in_array("Checkout material", $value_config) ? "checked" : '') ?> value="Checkout material" name="tickets[]"> Check Out Materials</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkout equipment') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkout equipment", $all_config) ? 'checked disabled' : (in_array("Checkout equipment", $value_config) ? "checked" : '') ?> value="Checkout equipment" name="tickets[]"> Check Out Equipment</label>
							<?php } ?>
							<?php if($field_sort_field == 'Checkout Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Checkout Notes", $all_config) ? 'checked disabled' : (in_array("Checkout Notes", $value_config) ? "checked" : '') ?> value="Checkout Notes" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify why something was checked out, such as if a staff left early because they were sick. This is a dropdown that pulls from below, or a textbox if there are no options."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Add Checkout Reason</label>
							<?php } ?>
						<?php } ?>
						</div>
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<h3>Check Out Reasons</h3>
							<?php foreach(explode('#*#',get_config($dbc, 'ticket_checkout_info')) as $reason) { ?>
								<div class="form-group checkout_info">
									<label class="col-sm-2 control-label">Reason:</label>
									<div class="col-sm-8">
										<input type="text" name="checkout_info" class="form-control" value="<?= $reason ?>">
									</div>
									<div class="col-sm-2">
										<img src="../img/icons/drag_handle.png" class="inline-img pull-right drag-handle">
										<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addInfo();">
										<img src="../img/remove.png" class="inline-img pull-right" onclick="removeInfo(this);">
									</div>
									<div class="clearfix"></div>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Staff Check Out') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Staff Check Out">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Check Out' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_checkout_staff',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_checkout_staff',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_checkout_staff" data-toggle="<?= in_array('ticket_checkout_staff',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_checkout_staff',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_checkout_staff',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Check Out' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Staff Check Out", $all_config) ? 'checked disabled' : (in_array("Staff Check Out", $value_config) ? "checked" : '') ?> value="Staff Check Out" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Staff Checkout Hide All Button') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Checkout Hide All Button", $all_config) ? 'checked disabled' : (in_array("Staff Checkout Hide All Button", $value_config) ? "checked" : '') ?> value="Staff Checkout Hide All Button" name="tickets[]"> Hide Check Out All Button</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Checkout Staff') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Checkout Staff", $all_config) ? 'checked disabled' : (in_array("Staff Checkout Staff", $value_config) ? "checked" : '') ?> value="Staff Checkout Staff" name="tickets[]"> Check Out Staff</label>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Checkout Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Staff Checkout Notes", $all_config) ? 'checked disabled' : (in_array("Staff Checkout Notes", $value_config) ? "checked" : '') ?> value="Staff Checkout Notes" name="tickets[]"> Add Checkout Reason</label>
							<?php } ?>
						<?php } ?>
						</div>
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<h3>Check Out Reasons</h3>
							<?php foreach(explode('#*#',get_config($dbc, 'ticket_checkout_info_staff')) as $reason) { ?>
								<div class="form-group checkout_info_staff">
									<label class="col-sm-2 control-label">Reason:</label>
									<div class="col-sm-8">
										<input type="text" name="checkout_info_staff" class="form-control" value="<?= $reason ?>">
									</div>
									<div class="col-sm-2">
										<img src="../img/icons/drag_handle.png" class="inline-img pull-right drag-handle">
										<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addInfoStaff();">
										<img src="../img/remove.png" class="inline-img pull-right" onclick="removeInfoStaff(this);">
									</div>
									<div class="clearfix"></div>
								</div>
							<?php } ?>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Billing') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Billing">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Billing' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_billing',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_billing',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_billing" data-toggle="<?= in_array('ticket_billing',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_billing',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_billing',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Billing' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Billing", $all_config) ? 'checked disabled' : (in_array("Billing", $value_config) ? "checked" : '') ?> value="Billing" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to display detailed billing information for the <?= TICKET_NOUN ?> with discounts and totals."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Billing Services') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Billing Services", $all_config) ? 'checked disabled' : (in_array("Billing Services", $value_config) ? "checked" : '') ?> value="Billing Services" name="tickets[]"> List Services</label>
							<?php } ?>
							<?php if($field_sort_field == 'Billing Staff') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Billing Staff", $all_config) ? 'checked disabled' : (in_array("Billing Staff", $value_config) ? "checked" : '') ?> value="Billing Staff" name="tickets[]"> List Staff Hours</label>
							<?php } ?>
							<?php if($field_sort_field == 'Billing Inventory') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Billing Inventory", $all_config) ? 'checked disabled' : (in_array("Billing Inventory", $value_config) ? "checked" : '') ?> value="Billing Inventory" name="tickets[]"> List Inventory</label>
							<?php } ?>
							<?php if($field_sort_field == 'Billing Misc') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Billing Misc", $all_config) ? 'checked disabled' : (in_array("Billing Misc", $value_config) ? "checked" : '') ?> value="Billing Misc" name="tickets[]"> List Miscellaneous Items</label>
							<?php } ?>
							<?php if($field_sort_field == 'Billing Surcharge') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Billing Surcharge", $all_config) ? 'checked disabled' : (in_array("Billing Surcharge", $value_config) ? "checked" : '') ?> value="Billing Surcharge" name="tickets[]"> Fuel Service Charge</label>
							<?php } ?>
							<?php if($field_sort_field == 'Billing Discount') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Billing Discount", $all_config) ? 'checked disabled' : (in_array("Billing Discount", $value_config) ? "checked" : '') ?> value="Billing Discount" name="tickets[]"> Discount per Item</label>
							<?php } ?>
							<?php if($field_sort_field == 'Billing Total Discount') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Billing Total Discount", $all_config) ? 'checked disabled' : (in_array("Billing Total Discount", $value_config) ? "checked" : '') ?> value="Billing Total Discount" name="tickets[]"> Discount on Total</label>
							<?php } ?>
							<?php if($field_sort_field == 'Billing Total') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Billing Total", $all_config) ? 'checked disabled' : (in_array("Billing Total", $value_config) ? "checked" : '') ?> value="Billing Total" name="tickets[]"> Display Editable Total</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Customer Notes') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Customer Notes">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Customer Notes' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_customer_notes',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_customer_notes',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_customer_notes" data-toggle="<?= in_array('ticket_customer_notes',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_customer_notes',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_customer_notes',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Customer Notes' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Customer Notes", $all_config) ? 'checked disabled' : (in_array("Customer Notes", $value_config) ? "checked" : '') ?> value="Customer Notes" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to have a customer add notes for Delivery Stops for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Customer Stop Status') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Stop Status", $all_config) ? 'checked disabled' : (in_array("Customer Stop Status", $value_config) ? "checked" : '') ?> value="Customer Stop Status" name="tickets[]"> Stop Status</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Contacted') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Contacted", $all_config) ? 'checked disabled' : (in_array("Customer Contacted", $value_config) ? "checked" : '') ?> value="Customer Contacted" name="tickets[]"> Customer Contacted</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Reset Details') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Reset Details", $all_config) ? 'checked disabled' : (in_array("Customer Reset Details", $value_config) ? "checked" : '') ?> value="Customer Reset Details" name="tickets[]"> Reset Details</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Driver Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Driver Notes", $all_config) ? 'checked disabled' : (in_array("Customer Driver Notes", $value_config) ? "checked" : '') ?> value="Customer Driver Notes" name="tickets[]"> Driver Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Property Photo') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Property Photo", $all_config) ? 'checked disabled' : (in_array("Customer Property Photo", $value_config) ? "checked" : '') ?> value="Customer Property Photo" name="tickets[]"> Property Photo</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Property Damage') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Property Damage", $all_config) ? 'checked disabled' : (in_array("Customer Property Damage", $value_config) ? "checked" : '') ?> value="Customer Property Damage" name="tickets[]"> Property Damage Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Product Damage') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Product Damage", $all_config) ? 'checked disabled' : (in_array("Customer Product Damage", $value_config) ? "checked" : '') ?> value="Customer Product Damage" onclick="$('[value^=Customer][value*=Product][value*=Damage][value$=Package]').removeAttr('checked').change();" name="tickets[]"> Product Damage Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Product Damage Package') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Product Damage Package", $all_config) ? 'checked disabled' : (in_array("Customer Product Damage Package", $value_config) ? "checked" : '') ?> value="Customer Product Damage Package" onclick="$('[value^=Customer][value*=Product][value$=Damage]').removeAttr('checked').change();" name="tickets[]"> Product or Package Damage Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Rate') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Rate", $all_config) ? 'checked disabled' : (in_array("Customer Rate", $value_config) ? "checked" : '') ?> value="Customer Rate" name="tickets[]"> Rating by Customer</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Delivery Rate') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Delivery Rate", $all_config) ? 'checked disabled' : (in_array("Customer Delivery Rate", $value_config) ? "checked" : '') ?> value="Customer Delivery Rate" name="tickets[]"> Delivery Team Rating by Customer</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Recommend') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Recommend", $all_config) ? 'checked disabled' : (in_array("Customer Recommend", $value_config) ? "checked" : '') ?> value="Customer Recommend" name="tickets[]"> Recommendation</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Recommend Likely') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Recommend Likely", $all_config) ? 'checked disabled' : (in_array("Customer Recommend Likely", $value_config) ? "checked" : '') ?> value="Customer Recommend Likely" name="tickets[]"> Likelihood of Recommending</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Add Details') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Add Details", $all_config) ? 'checked disabled' : (in_array("Customer Add Details", $value_config) ? "checked" : '') ?> value="Customer Add Details" name="tickets[]"> Additional Details</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Sign') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Sign", $all_config) ? 'checked disabled' : (in_array("Customer Sign", $value_config) ? "checked" : '') ?> value="Customer Sign" name="tickets[]"> Signature</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Complete') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Complete", $all_config) ? 'checked disabled' : (in_array("Customer Complete", $value_config) ? "checked" : '') ?> value="Customer Complete" name="tickets[]"> Complete</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Slider') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Slider", $all_config) ? 'checked disabled' : (in_array("Customer Slider", $value_config) ? "checked" : '') ?> value="Customer Slider" name="tickets[]"> Use Slider to Display Options</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Sign Off Complete Status') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Sign Off Complete Status", $all_config) ? 'checked disabled' : (in_array("Customer Sign Off Complete Status", $value_config) ? "checked" : '') ?> value="Customer Sign Off Complete Status" name="tickets[]"> Completing Sets Stop Status as Complete</label>
							<?php } ?>
							<?php if($field_sort_field == 'Customer Complete Exits Ticket') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Customer Complete Exits Ticket", $all_config) ? 'checked disabled' : (in_array("Customer Complete Exits Ticket", $value_config) ? "checked" : '') ?> value="Customer Complete Exits Ticket" name="tickets[]"> Completing Exits Out Of <?= TICKET_NOUN ?></label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Addendum') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Addendum">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Addendum' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('addendum_view_ticket_comment',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('addendum_view_ticket_comment',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="addendum_view_ticket_comment" data-toggle="<?= in_array('addendum_view_ticket_comment',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('addendum_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('addendum_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Addendum' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Addendum", $all_config) ? 'checked disabled' : (in_array("Addendum", $value_config) ? "checked" : '') ?> value="Addendum" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add Addendum notes for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Client Log') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Client Log">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Log Notes' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_log_notes',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_log_notes',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_log_notes" data-toggle="<?= in_array('ticket_log_notes',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_log_notes',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_log_notes',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Log Notes' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Client Log", $all_config) ? 'checked disabled' : (in_array("Client Log", $value_config) ? "checked" : '') ?> value="Client Log" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add notes that get stored to a Staff profile from the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Debrief') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Debrief">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Debrief' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('debrief_view_ticket_comment',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('debrief_view_ticket_comment',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="debrief_view_ticket_comment" data-toggle="<?= in_array('debrief_view_ticket_comment',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('debrief_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('debrief_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Debrief' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Debrief", $all_config) ? 'checked disabled' : (in_array("Debrief", $value_config) ? "checked" : '') ?> value="Debrief" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add Debrief notes to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Debrief Incident Report Reminders',$merged_config_fields)) { ?>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Debrief Incident Report Reminders", $all_config) ? 'checked disabled' : (in_array("Debrief Incident Report Reminders", $value_config) ? "checked" : '') ?> value="Debrief Incident Report Reminders" name="tickets[]"> <?= INC_REP_TILE ?> Reminders</label>
						<?php } ?>
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<div class="form-group">
								<h3><?= INC_REP_TILE ?> Second Reminder Email</h3>
								<label class="col-sm-4 control-label">Second Reminder Email:<br><em>This is the email that will be sent with the second reminder if the first reminder is ignored. Enter emails separated by a comma to have multiple emails.</em></label>
								<div class="col-sm-8">
									<?php $inc_rep_reminder_email = get_config($dbc, 'inc_rep_reminder_email'); ?>
									<input type="text" name="inc_rep_reminder_email" class="form-control" value="<?= $inc_rep_reminder_email ?>" onchange="updateIncidentReportEmail(this);">
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Member Log Notes') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Member Log Notes">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Member Log Notes' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('member_view_ticket_comment',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('member_view_ticket_comment',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="member_view_ticket_comment" data-toggle="<?= in_array('member_view_ticket_comment',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('member_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('member_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Member Log Notes' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Member Log Notes", $all_config) ? 'checked disabled' : (in_array("Member Log Notes", $value_config) ? "checked" : '') ?> value="Member Log Notes" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add notes that get stored to a Contact profile from the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Cancellation') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Cancellation">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Cancellation' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_cancellation',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_cancellation',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_cancellation" data-toggle="<?= in_array('ticket_cancellation',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_cancellation',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_cancellation',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Cancellation' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Cancellation", $all_config) ? 'checked disabled' : (in_array("Cancellation", $value_config) ? "checked" : '') ?> value="Cancellation" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add cancellation details to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<h3>Cancellation Reasons</h3>
							<?php foreach(explode('#*#',get_config($dbc, 'ticket_cancellation_reasons')) as $reason) { ?>
								<div class="form-group cancel_reason">
									<label class="col-sm-2 control-label">Reason:</label>
									<div class="col-sm-8">
										<input type="text" name="cancel_reasons" class="form-control" value="<?= $reason ?>">
									</div>
									<div class="col-sm-2">
										<img src="../img/icons/drag_handle.png" class="inline-img pull-right drag-handle">
										<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addReason();">
										<img src="../img/remove.png" class="inline-img pull-right" onclick="removeReason(this);">
									</div>
									<div class="clearfix"></div>
								</div>
							<?php } ?>
						<?php } ?>
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Cancellation Reason') { ?>
								<label class="form-checkbox"><input type="checkbox" <?= in_array("Cancellation Reason", $all_config) ? 'checked disabled' : (in_array("Cancellation Reason", $value_config) ? "checked" : '') ?> value="Cancellation Reason" name="tickets[]"> Cancellation Reason</label>
							<?php } ?>
							<?php if($field_sort_field == 'Cancellation Notes') { ?>
								<label class="form-checkbox"><input type="checkbox" <?= in_array("Cancellation Notes", $all_config) ? 'checked disabled' : (in_array("Cancellation Notes", $value_config) ? "checked" : '') ?> value="Cancellation Notes" name="tickets[]"> Add Cancellation Notes</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Custom Notes') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Custom Notes">
				<label class="col-sm-4 control-label">Custom Notes:</label>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Custom Notes", $all_config) ? 'checked disabled' : (in_array("Custom Notes", $value_config) ? "checked" : '') ?> value="Custom Notes" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add additional notes with any headings to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
						<div class="block-group">
							<div class="form-group">
								<label class="col-sm-4 control-label">Section Heading:</label>
								<div class="col-sm-8">
									<input type="text" name="custom_notes_heading" class="form-control" value="<?= get_config($dbc, 'ticket_custom_notes_heading') ?>">
								</div>
							</div>
							<?php foreach(explode('#*#',get_config($dbc, 'ticket_custom_notes_type')) as $type) { ?>
								<div class="form-group note-option">
									<label class="col-sm-2 control-label">Note Heading:</label>
									<div class="col-sm-8">
										<input type="text" name="note_types" class="form-control" value="<?= $type ?>">
									</div>
									<div class="col-sm-2">
										<img src="../img/icons/drag_handle.png" class="inline-img pull-right drag-handle">
										<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addNoteType();">
										<img src="../img/remove.png" class="inline-img pull-right" onclick="removeNoteType(this);">
									</div>
									<div class="clearfix"></div>
								</div>
							<?php } ?>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Internal Communication') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Internal Communication">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Internal Communication' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('internal_communication',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('internal_communication',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="internal_communication" data-toggle="<?= in_array('internal_communication',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('internal_communication',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('internal_communication_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Internal Communication' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Internal Communication", $all_config) ? 'checked disabled' : (in_array("Internal Communication", $value_config) ? "checked" : '') ?> value="Internal Communication" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add Internal Communication to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'External Communication') { ?>
			<div class="form-group sort_order_accordion" data-accordion="External Communication">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'External Communication' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('external_communication',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('external_communication',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="external_communication" data-toggle="<?= in_array('external_communication',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('external_communication',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('external_communication',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'External Communication' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("External Communication", $all_config) ? 'checked disabled' : (in_array("External Communication", $value_config) ? "checked" : '') ?> value="External Communication" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add External Communication to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
						<div class="block-group">
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("External Response", $all_config) ? 'checked disabled' : (in_array("External Response", $value_config) ? "checked" : '') ?> value="External Response" name="tickets[]"> Request Response</label>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("External Response Thread", $all_config) ? 'checked disabled' : (in_array("External Response Thread", $value_config) ? "checked" : '') ?> value="External Response Thread" name="tickets[]"> Display Communication Thread</label>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("External Response Status", $all_config) ? 'checked disabled' : (in_array("External Response Status", $value_config) ? "checked" : '') ?> value="External Response Status" name="tickets[]"> Responder Status</label>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("External Response Documents", $all_config) ? 'checked disabled' : (in_array("External Response Documents", $value_config) ? "checked" : '') ?> value="External Response Documents" name="tickets[]"> Include Documents</label>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Notes') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Notes">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : TICKET_NOUN.' Notes' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('notes_view_ticket_comment',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('notes_view_ticket_comment',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="notes_view_ticket_comment" data-toggle="<?= in_array('notes_view_ticket_comment',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('notes_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('notes_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : TICKET_NOUN.' Notes' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Notes", $all_config) ? 'checked disabled' : (in_array("Notes", $value_config) ? "checked" : '') ?> value="Notes" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add general notes to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
						<div class="block-group">
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Notes Anyone Can Add", $all_config) ? 'checked disabled' : (in_array("Notes Anyone Can Add", $value_config) ? "checked" : '') ?> value="Notes Anyone Can Add" name="tickets[]"> Anyone Can Add Notes</label>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Notes Limit", $all_config) ? 'checked disabled' : (in_array("Notes Limit", $value_config) ? "checked" : '') ?> value="Notes Limit" name="tickets[]"> Limit Number of Visible Notes</label>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Notes Alert", $all_config) ? 'checked disabled' : (in_array("Notes Alert", $value_config) ? "checked" : '') ?> value="Notes Alert" name="tickets[]"> Alert Security Levels</label>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Notes Email Default On", $all_config) ? 'checked disabled' : (in_array("Notes Email Default On", $value_config) ? "checked" : '') ?> value="Notes Email Default On" name="tickets[]"> Send Email Default On</label>
							<div class="form-group">
								<h3>Limit Number of Notes</h3>
								<label class="col-sm-4 control-label">Number of Notes:</label>
								<div class="col-sm-8">
									<?php $ticket_notes_limit = get_config($dbc, 'ticket_notes_limit'); ?>
									<input type="number" name="ticket_notes_limit" class="form-control" value="<?= $ticket_notes_limit < 1 ? 1 : $ticket_notes_limit ?>" min="1" onchange="saveFields();">
								</div>
							</div>
							<div class="form-group">
								<h3>Default Security Level to Alert</h3>
								<label class="col-sm-4 control-label">Security Level:</label>
								<div class="col-sm-8">
									<?php $ticket_notes_alert_role = get_config($dbc, 'ticket_notes_alert_role'); ?>
									<select name="ticket_notes_alert_role" class="chosen-select-deselect form-control">
										<option></option>
										<?php $on_security = get_security_levels($dbc);
										foreach($on_security as $category => $value) {
											if($value != 'super') {
												echo '<option value="'.$value.'" '.($ticket_notes_alert_role == $value ? 'selected' : '').'>'.$category.'</option>';
											}
										} ?>
									</select>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Summary') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Summary">
				<label class="col-sm-4 control-label">Summary:</label>
				<div class="col-sm-8">
					<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Staff Summary', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Staff Summary", $all_config) ? 'checked disabled' : (in_array("Staff Summary", $value_config) ? "checked" : '') ?> value="Staff Summary" name="tickets[]">
							<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to display a summary of Staff and other contacts for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable Staff Summary</label>
					<?php } ?>
					<?php if((!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) || in_array('Summary', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Summary", $all_config) ? 'checked disabled' : (in_array("Summary", $value_config) ? "checked" : '') ?> value="Summary" name="tickets[]"> Enable Summary</label>
					<?php } ?>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Summary Times') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Summary Times", $all_config) ? 'checked disabled' : (in_array("Summary Times", $value_config) ? "checked" : '') ?> value="Summary Times" name="tickets[]"> Show Times on Summary</label>
							<?php } ?>
							<?php if($field_sort_field == 'No Track Time Sheets') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("No Track Time Sheets", $all_config) ? 'checked disabled' : (in_array("No Track Time Sheets", $value_config) ? "checked" : '') ?> value="No Track Time Sheets" name="tickets[]"> Track Time to Summary Only <em>(Not Time Sheets)</em></label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking", $all_config) ? 'checked disabled' : (in_array("Time Tracking", $value_config) ? "checked" : '') ?> value="Time Tracking" name="tickets[]"> Time Tracking</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking Hours') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Hours", $all_config) ? 'checked disabled' : (in_array("Time Tracking Hours", $value_config) ? "checked" : '') ?> value="Time Tracking Hours" name="tickets[]"> Time Sheet Tracking</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking Date') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Date", $all_config) ? 'checked disabled' : (in_array("Time Tracking Date", $value_config) ? "checked" : '') ?> value="Time Tracking Date" name="tickets[]"> Date Stamped Time Tracking</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking Time') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Time", $all_config) ? 'checked disabled' : (in_array("Time Tracking Time", $value_config) ? "checked" : '') ?> value="Time Tracking Time" name="tickets[]"> Time Stamped Time Tracking</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking Current') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Current", $all_config) ? 'checked disabled' : (in_array("Time Tracking Current", $value_config) ? "checked" : '') ?> value="Time Tracking Current" name="tickets[]"> Show Current Day Only</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking Set') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Set", $all_config) ? 'checked disabled' : (in_array("Time Tracking Set", $value_config) ? "checked" : '') ?> value="Time Tracking Set" name="tickets[]"> Manual Time Tracking</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking Hrs') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Hrs", $all_config) ? 'checked disabled' : (in_array("Time Tracking Hrs", $value_config) ? "checked" : '') ?> value="Time Tracking Hrs" name="tickets[]"> Automatic Time Tracking</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tracking Edit Past Date') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tracking Edit Past Date", $all_config) ? 'checked disabled' : (in_array("Time Tracking Edit Past Date", $value_config) ? "checked" : '') ?> value="Time Tracking Edit Past Date" name="tickets[]"> Edit Past Hours on Date</label>
							<?php } ?>
							<?php if($field_sort_field == 'Time Tasks') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Time Tasks", $all_config) ? 'checked disabled' : (in_array("Time Tasks", $value_config) ? "checked" : '') ?> value="Time Tasks" name="tickets[]"> Tasks for Time</label>
							<?php } ?>
							<?php if($field_sort_field == 'Summary Materials Summary') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Summary Materials Summary", $all_config) ? 'checked disabled' : (in_array("Summary Materials Summary", $value_config) ? "checked" : '') ?> value="Summary Materials Summary" name="tickets[]"> Materials Summary</label>
							<?php } ?>
							<?php if($field_sort_field == 'Summary Notes') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Summary Notes", $all_config) ? 'checked disabled' : (in_array("Summary Notes", $value_config) ? "checked" : '') ?> value="Summary Notes" name="tickets[]"> Notes</label>
							<?php } ?>
							<?php if($field_sort_field == 'Planned Tracked Payable Staff') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Planned Tracked Payable Staff", $all_config) ? 'checked disabled' : (in_array("Planned Tracked Payable Staff", $value_config) ? "checked" : '') ?> value="Planned Tracked Payable Staff" name="tickets[]"> Planned/Tracked/Payable Hours Table - Staff</label>
							<?php } ?>
							<?php if($field_sort_field == 'Planned Tracked Payable Staff Multiple Times') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Planned Tracked Payable Staff Multiple Times", $all_config) ? 'checked disabled' : (in_array("Planned Tracked Payable Staff Multiple Times", $value_config) ? "checked" : '') ?> value="Planned Tracked Payable Staff Multiple Times" name="tickets[]"> Planned/Tracked/Payable Hours Table - Staff Multiple Dates/Times</label>
							<?php } ?>
							<?php if($field_sort_field == 'Planned Tracked Payable Members') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Planned Tracked Payable Members", $all_config) ? 'checked disabled' : (in_array("Planned Tracked Payable Members", $value_config) ? "checked" : '') ?> value="Planned Tracked Payable Members" name="tickets[]"> Planned/Tracked/Payable Hours Table - Members</label>
							<?php } ?>
							<?php if($field_sort_field == 'Total Time Tracked Staff') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Total Time Tracked Staff", $all_config) ? 'checked disabled' : (in_array("Total Time Tracked Staff", $value_config) ? "checked" : '') ?> value="Total Time Tracked Staff" name="tickets[]"> Total Time Tracked - Staff</label>
							<?php } ?>
							<?php if($field_sort_field == 'Total Time Tracked Members') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Total Time Tracked Members", $all_config) ? 'checked disabled' : (in_array("Total Time Tracked Members", $value_config) ? "checked" : '') ?> value="Total Time Tracked Members" name="tickets[]"> Total Time Tracked - Members</label>
							<?php } ?>
							<?php if($field_sort_field == 'Total Time Tracked Clients') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Total Time Tracked Clients", $all_config) ? 'checked disabled' : (in_array("Total Time Tracked Clients", $value_config) ? "checked" : '') ?> value="Total Time Tracked Clients" name="tickets[]"> Total Time Tracked - Clients</label>
							<?php } ?>
						<?php } ?>
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) {
							$position_list = [];
							$positions = $dbc->query("SELECT `position_id`,`name` FROM `positions` WHERE `deleted`=0");
							while($position = $positions->fetch_assoc()) {
								$position_list[$position['position_id']] = $position['name'];
							} ?>
							<div class="form-group">
								<h3>Hide Positions</h3>
								<?php foreach(array_filter($all_summary_hide_positions) as $all_summary_hide_position) { ?>
									<div class="form-group">
										<label class="col-sm-2 control-label">Position:</label>
										<div class="col-sm-8 readonly-block">
											<select disabled class="chosen-select-deselect form-control">
												<option value="<?= $all_summary_hide_position ?>"><?= $all_summary_hide_position ?></option>
											</select>
										</div>
										<div class="clearfix"></div>
									</div>
								<?php } ?>
								<?php foreach($summary_hide_positions as $summary_hide_position) { ?>
									<div class="position_block form-group">
										<label class="col-sm-2 control-label">Position:</label>
										<div class="col-sm-8">
											<select name="ticket_hide_summary[]" data-placeholder="Select a Position" class="chosen-select-deselect form-control">
												<option></option>
												<?php foreach($position_list as $position) { ?>
													<option value="<?= $position ?>" <?= $summary_hide_position == $position ? 'selected' : '' ?>><?= $position ?></option>
												<?php } ?>
											</select>
										</div>
										<div class="col-sm-2">
											<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addHidePosition();">
											<img src="../img/remove.png" class="inline-img pull-right" onclick="removeHidePosition(this);">
										</div>
										<div class="clearfix"></div>
									</div>
								<?php } ?>
							</div>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Multi-Disciplinary Summary Report') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Multi-Disciplinary Summary Report">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Multi-Disciplinary Summary Report' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_multi_disciplinary_summary_report',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_multi_disciplinary_summary_report',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_multi_disciplinary_summary_report" data-toggle="<?= in_array('view_multi_disciplinary_summary_report',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_multi_disciplinary_summary_report',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_multi_disciplinary_summary_report',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Multi-Disciplinary Summary Report' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Multi-Disciplinary Summary Report", $all_config) ? 'checked disabled' : (in_array("Multi-Disciplinary Summary Report", $value_config) ? "checked" : '') ?> value="Multi-Disciplinary Summary Report" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add details to create a report from the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Child Name') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Child Name", $all_config) ? 'checked disabled' : (in_array("Child Name", $value_config) ? "checked" : '') ?> value="Child Name" name="tickets[]"> Child's Name</label>
							<?php } ?>
							<?php if($field_sort_field == 'Date of Birth') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Date of Birth", $all_config) ? 'checked disabled' : (in_array("Date of Birth", $value_config) ? "checked" : '') ?> value="Date of Birth" name="tickets[]"> Date of Birth</label>
							<?php } ?>
							<?php if($field_sort_field == 'Date of Report') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Date of Report", $all_config) ? 'checked disabled' : (in_array("Date of Report", $value_config) ? "checked" : '') ?> value="Date of Report" name="tickets[]"> Date of Report</label>
							<?php } ?>
							<?php if($field_sort_field == 'Background Information') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Background Information", $all_config) ? 'checked disabled' : (in_array("Background Information", $value_config) ? "checked" : '') ?> value="Background Information" name="tickets[]"> Background Information</label>
							<?php } ?>
							<?php if($field_sort_field == 'Progress') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Progress", $all_config) ? 'checked disabled' : (in_array("Progress", $value_config) ? "checked" : '') ?> value="Progress" name="tickets[]"> Progress</label>
							<?php } ?>
							<?php if($field_sort_field == 'Clinical Impacts') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Clinical Impacts", $all_config) ? 'checked disabled' : (in_array("Clinical Impacts", $value_config) ? "checked" : '') ?> value="Clinical Impacts" name="tickets[]"> Clinical Impacts</label>
							<?php } ?>
							<?php if($field_sort_field == 'Proposed Goal Areas') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Proposed Goal Areas", $all_config) ? 'checked disabled' : (in_array("Proposed Goal Areas", $value_config) ? "checked" : '') ?> value="Proposed Goal Areas" name="tickets[]"> Proposed Goal Areas</label>
							<?php } ?>
							<?php if($field_sort_field == 'Recommendations') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Recommendations", $all_config) ? 'checked disabled' : (in_array("Recommendations", $value_config) ? "checked" : '') ?> value="Recommendations" name="tickets[]"> Recommendations</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Complete') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Complete">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Complete (Sign Off)' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_complete',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_complete',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_complete" data-toggle="<?= in_array('ticket_complete',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_complete',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_complete',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Complete (Sign Off)' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Complete", $all_config) ? 'checked disabled' : (in_array("Complete", $value_config) ? "checked" : '') ?> value="Complete" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow the user to sign off that the <?= TICKET_NOUN ?> is complete."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
						<div class="block-group">
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Complete Hide Signature", $all_config) ? 'checked disabled' : (in_array("Complete Hide Signature", $value_config) ? "checked" : '') ?> value="Complete Hide Signature" name="tickets[]"> Hide Signature</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Complete Hide Sign & Complete", $all_config) ? 'checked disabled' : (in_array("Complete Hide Sign & Complete", $value_config) ? "checked" : '') ?> value="Complete Hide Sign & Complete" name="tickets[]"> Hide Sign & Complete Button</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Complete Sign & Force Complete", $all_config) ? 'checked disabled' : (in_array("Complete Sign & Force Complete", $value_config) ? "checked" : '') ?> value="Complete Sign & Force Complete" name="tickets[]"> Sign & Force Complete</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Complete Do Not Require Notes", $all_config) ? 'checked disabled' : (in_array("Complete Do Not Require Notes", $value_config) ? "checked" : '') ?> value="Complete Do Not Require Notes" name="tickets[]"> Do Not Require Notes To Complete</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Complete Default Session User", $all_config) ? 'checked disabled' : (in_array("Complete Default Session User", $value_config) ? "checked" : '') ?> value="Complete Default Session User" name="tickets[]"> Default Select Logged In User</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Complete Email Users On Complete", $all_config) ? 'checked disabled' : (in_array("Complete Email Users On Complete", $value_config) ? "checked" : '') ?> value="Complete Email Users On Complete" name="tickets[]"> Email Users On Complete</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Complete Combine Checkout Summary", $all_config) ? 'checked disabled' : (in_array("Complete Combine Checkout Summary", $value_config) ? "checked" : '') ?> value="Complete Combine Checkout Summary" name="tickets[]"> Combine Checkout, Staff Summary, and Complete</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Complete Submit Approval", $all_config) ? 'checked disabled' : (in_array("Complete Submit Approval", $value_config) ? "checked" : '') ?> value="Complete Submit Approval" name="tickets[]"> Submit for Approval</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Complete Main Approval", $all_config) ? 'checked disabled' : (in_array("Complete Main Approval", $value_config) ? "checked" : '') ?> value="Complete Main Approval" name="tickets[]"> Approval by Supervisor</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Complete Office Approval", $all_config) ? 'checked disabled' : (in_array("Complete Office Approval", $value_config) ? "checked" : '') ?> value="Complete Office Approval" name="tickets[]"> Approval by Office</label>
							<div class="form-group">
								<label class="col-sm-4 control-label">Approval Email Address:<br /><em>This e-mail address will receive email notification that the <?= TICKET_NOUN ?> has been submitted for approval</em></label>
								<div class="col-sm-8">
									<input type="text" name="ticket_email_approval" class="form-control" value="<?= get_config($dbc, 'ticket_email_approval') ?>">
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Approval Status:<br /><em>The <?= TICKET_NOUN ?> status will be set to this when submitted for Approval</em></label>
								<div class="col-sm-8">
									<?php $approval_status = get_config($dbc, 'ticket_approval_status'); ?>
									<select name="ticket_approval_status" class="chosen-select-deselect" data-placeholder="Select a status"><option />
										<?php foreach(explode(',',get_config($dbc, 'ticket_status')) as $status_name) { ?>
											<option <?= $status_name == $approval_status ? 'selected' : '' ?> value="<?= $status_name ?>"><?= $status_name ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Notifications') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Notifications">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Notifications' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_notifications',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_notifications',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_notifications" data-toggle="<?= in_array('view_ticket_notifications',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_notifications',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_notifications',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Notifications' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Notifications", $all_config) ? 'checked disabled' : (in_array("Notifications", $value_config) ? "checked" : '') ?> value="Notifications" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Notify Business') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Notify Business", $all_config) ? 'checked disabled' : (in_array("Notify Business", $value_config) ? "checked" : '') ?> value="Notify Business" name="tickets[]"> <?= BUSINESS_CAT ?></label>
							<?php } ?>
							<?php if($field_sort_field == 'Notify Client') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Notify Client", $all_config) ? 'checked disabled' : (in_array("Notify Client", $value_config) ? "checked" : '') ?> value="Notify Client" name="tickets[]"> <?= CONTACTS_NOUN ?></label>
							<?php } ?>
							<?php if($field_sort_field == 'Notify Staff') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Notify Staff", $all_config) ? 'checked disabled' : (in_array("Notify Staff", $value_config) ? "checked" : '') ?> value="Notify Staff" name="tickets[]"> Staff</label>
							<?php } ?>
							<?php if($field_sort_field == 'Notify List') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Notify List", $all_config) ? 'checked disabled' : (in_array("Notify List", $value_config) ? "checked" : '') ?> value="Notify List" name="tickets[]"> Include List</label>
							<?php } ?>
							<?php if($field_sort_field == 'Notify PDF') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Notify PDF", $all_config) ? 'checked disabled' : (in_array("Notify PDF", $value_config) ? "checked" : '') ?> value="Notify PDF" name="tickets[]"> Create PDF</label>
							<?php } ?>
							<?php if($field_sort_field == 'Notify Anyone Can Add') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Notify Anyone Can Add", $all_config) ? 'checked disabled' : (in_array("Notify Anyone Can Add", $value_config) ? "checked" : '') ?> value="Notify Anyone Can Add" name="tickets[]">
									Anyone Can Add Notifications</label>
							<?php } ?>
						<?php } ?>
						</div>
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Notification List Title:</label>
								<div class="col-sm-8">
									<input type="text" name="ticket_notify_list" class="form-control" value="<?= get_config($dbc, 'ticket_notify_list') ?>">
								</div>
							</div>
							<div class="block-group">
								<?php foreach(explode('#*#',get_config($dbc, 'ticket_notify_list_items')) as $notify_list_item) { ?>
									<div class="form-group notify_item">
										<label class="col-sm-4 control-label">Notification List Item:</label>
										<div class="col-sm-7">
											<input type="text" name="ticket_notify_list_items" class="form-control" value="<?= $notify_list_item ?>">
										</div>
										<div class="col-sm-1">
											<img src="../img/icons/drag_handle.png" class="inline-img pull-right drag-handle">
											<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addNotifyListItem();">
											<img src="../img/remove.png" class="inline-img pull-right" onclick="removeNotifyListItem(this);">
										</div>
									</div>
								<?php } ?>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Notification PDF Default Content:<br /><em>You can include details from the <?= TICKET_NOUN ?> by including the following: [LIST], [TICKET], [CONTACT], [DATE]</em></label>
								<div class="col-sm-8">
									<textarea name="ticket_notify_pdf_content"><?= html_entity_decode(get_config($dbc, 'ticket_notify_pdf_content')) ?></textarea>
								</div>
							</div>
							<div class="form-group">
								<label class="col-sm-4 control-label">Notification CC Email:</label>
								<div class="col-sm-8">
									<input type="text" name="ticket_notify_cc" class="form-control" value="<?= get_config($dbc, 'ticket_notify_cc') ?>">
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Region Location Classification') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Region Location Classification">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Region/Location/Classification' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_reg_loc_class',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_reg_loc_class',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_reg_loc_class" data-toggle="<?= in_array('ticket_reg_loc_class',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_reg_loc_class',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_reg_loc_class',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Region/Location/Classification' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Region Location Classification", $all_config) ? 'checked disabled' : (in_array("Region Location Classification", $value_config) ? "checked" : '') ?> value="Region Location Classification" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify a region, location, or classification for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Con Region') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Con Region", $all_config) ? 'checked disabled' : (in_array("Con Region", $value_config) ? "checked" : '') ?> value="Con Region" name="tickets[]"> Show Region</label>
							<?php } ?>
							<?php if($field_sort_field == 'Con Location') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Con Location", $all_config) ? 'checked disabled' : (in_array("Con Location", $value_config) ? "checked" : '') ?> value="Con Location" name="tickets[]"> Show Location</label>
							<?php } ?>
							<?php if($field_sort_field == 'Con Classification') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Con Classification", $all_config) ? 'checked disabled' : (in_array("Con Classification", $value_config) ? "checked" : '') ?> value="Con Classification" name="tickets[]"> Show Classification</label>
							<?php } ?>
						<?php } ?>
						</div>
						<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
							<div class="block-group">
								<h3>Filter Options</h3>
								<label class="form-checkbox"><input type="checkbox" <?= in_array("RegLocClass Filters Project", $all_config) ? 'checked disabled' : (in_array("RegLocClass Filters Project", $value_config) ? "checked" : '') ?> value="RegLocClass Filters Project" name="tickets[]"> Filter <?= PROJECT_NOUN ?></label>
								<label class="form-checkbox"><input type="checkbox" <?= in_array("RegLocClass Filters Business", $all_config) ? 'checked disabled' : (in_array("RegLocClass Filters Business", $value_config) ? "checked" : '') ?> value="RegLocClass Filters Business" name="tickets[]"> Filter <?= BUSINESS_CAT ?></label>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Incident Reports') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Incident Reports">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : INC_REP_TILE ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_incident_reports',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_incident_reports',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_incident_reports" data-toggle="<?= in_array('view_ticket_incident_reports',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_incident_reports',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_incident_reports',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : INC_REP_TILE ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Incident Reports", $all_config) ? 'checked disabled' : (in_array("Incident Reports", $value_config) ? "checked" : '') ?> value="Incident Reports" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add <?= INC_REP_TILE ?> from the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Pressure') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Pressure">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Pressure' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_pressure',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_pressure',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_pressure" data-toggle="<?= in_array('view_ticket_pressure',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_pressure',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_pressure',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Pressure' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Pressure", $all_config) ? 'checked disabled' : (in_array("Pressure", $value_config) ? "checked" : '') ?> value="Pressure" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Pressure Pressure Test') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Pressure Pressure Test", $all_config) ? 'checked disabled' : (in_array("Pressure Pressure Test", $value_config) ? "checked" : '') ?> value="Pressure Pressure Test" name="tickets[]"> Pressure Test</label>
							<?php } ?>
							<?php if($field_sort_field == 'Pressure PSV SET') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Pressure PSV SET", $all_config) ? 'checked disabled' : (in_array("Pressure PSV SET", $value_config) ? "checked" : '') ?> value="Pressure PSV SET" name="tickets[]"> PSV SET</label>
							<?php } ?>
							<?php if($field_sort_field == 'Pressure Purge Closed') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Pressure Purge Closed", $all_config) ? 'checked disabled' : (in_array("Pressure Purge Closed", $value_config) ? "checked" : '') ?> value="Pressure Purge Closed" name="tickets[]"> Purge Closed</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Application Report') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Application Report">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Application Report' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_chemicals',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_chemicals',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_chemicals" data-toggle="<?= in_array('ticket_chemicals',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_chemicals',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_chemicals',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Application Report' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Application Report", $all_config) ? 'checked disabled' : (in_array("Application Report", $value_config) ? "checked" : '') ?> value="Application Report" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Application Equipment') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Application Equipment", $all_config) ? 'checked disabled' : (in_array("Application Equipment", $value_config) ? "checked" : '') ?> value="Application Equipment" name="tickets[]"> Equipment Description</label>
							<?php } ?>
							<?php if($field_sort_field == 'Application Wind Speed') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Application Wind Speed", $all_config) ? 'checked disabled' : (in_array("Application Wind Speed", $value_config) ? "checked" : '') ?> value="Application Wind Speed" name="tickets[]"> Wind Speed</label>
							<?php } ?>
							<?php if($field_sort_field == 'Application Humidity') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Application Humidity", $all_config) ? 'checked disabled' : (in_array("Application Humidity", $value_config) ? "checked" : '') ?> value="Application Humidity" name="tickets[]"> Humidity</label>
							<?php } ?>
							<?php if($field_sort_field == 'Application Temperature') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Application Temperature", $all_config) ? 'checked disabled' : (in_array("Application Temperature", $value_config) ? "checked" : '') ?> value="Application Temperature" name="tickets[]"> Temperature</label>
							<?php } ?>
							<?php if($field_sort_field == 'Application Wind Direction') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Application Wind Direction", $all_config) ? 'checked disabled' : (in_array("Application Wind Direction", $value_config) ? "checked" : '') ?> value="Application Wind Direction" name="tickets[]"> Wind Direction</label>
							<?php } ?>
							<?php if($field_sort_field == 'Application Soil') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Application Soil", $all_config) ? 'checked disabled' : (in_array("Application Soil", $value_config) ? "checked" : '') ?> value="Application Soil" name="tickets[]"> Soil</label>
							<?php } ?>
						<?php } ?>
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Chemicals') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Chemicals">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Chemicals' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_chemicals',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_chemicals',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_chemicals" data-toggle="<?= in_array('ticket_chemicals',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_chemicals',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_chemicals',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Chemicals' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Chemicals", $all_config) ? 'checked disabled' : (in_array("Chemicals", $value_config) ? "checked" : '') ?> value="Chemicals" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Chemical Location') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Chemical Location", $all_config) ? 'checked disabled' : (in_array("Chemical Location", $value_config) ? "checked" : '') ?> value="Chemical Location" name="tickets[]"> Chemical Location</label>
							<?php } ?>
							<?php if($field_sort_field == 'Chemical Hours') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Chemical Hours", $all_config) ? 'checked disabled' : (in_array("Chemical Hours", $value_config) ? "checked" : '') ?> value="Chemical Hours" name="tickets[]"> Hours</label>
							<?php } ?>
							<?php if($field_sort_field == 'Chemical Hrs Cost') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Chemical Hrs Cost", $all_config) ? 'checked disabled' : (in_array("Chemical Hrs Cost", $value_config) ? "checked" : '') ?> value="Chemical Hrs Cost" name="tickets[]"> Time Cost</label>
							<?php } ?>
							<?php if($field_sort_field == 'Chemical Volume') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Chemical Volume", $all_config) ? 'checked disabled' : (in_array("Chemical Volume", $value_config) ? "checked" : '') ?> value="Chemical Volume" name="tickets[]"> Chemical Volume</label>
							<?php } ?>
							<?php if($field_sort_field == 'Chemical Vol Cost') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Chemical Vol Cost", $all_config) ? 'checked disabled' : (in_array("Chemical Vol Cost", $value_config) ? "checked" : '') ?> value="Chemical Vol Cost" name="tickets[]"> Cost per Liter</label>
							<?php } ?>
							<?php if($field_sort_field == 'Chemical Total Cost') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Chemical Total Cost", $all_config) ? 'checked disabled' : (in_array("Chemical Total Cost", $value_config) ? "checked" : '') ?> value="Chemical Total Cost" name="tickets[]"> Total Chemical Cost</label>
							<?php } ?>
						<?php } ?>
						</div>
						<?php $ticket_chemical_label = get_config($dbc, 'ticket_chemical_label'); ?>
						<label class="col-sm-4 control-label">Chemical Label<?= $ticket_chemical_label != '' && $tab != '' ? ' (Default: '.$ticket_chemical_label.')' : '' ?>:</label>
						<div class="col-sm-8">
							<?php $tab_ticket_chemical_label = get_config($dbc, 'ticket_chemical_label'.($tab == '' ? '' : '_'.$tab)); ?>
							<input type="text" name="ticket_chemical_label<?= $tab == '' ? '' : '_'.$tab ?>" placeholder="Enter Chemical Label" class="form-control" value="<?= $tab_ticket_chemical_label ?>">
						</div>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Intake') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Intake">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Intake' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_intake',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_intake',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_intake" data-toggle="<?= in_array('view_ticket_intake',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_intake',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_intake',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Intake' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Intake", $all_config) ? 'checked disabled' : (in_array("Intake", $value_config) ? "checked" : '') ?> value="Intake" name="tickets[]"> Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'History') { ?>
			<div class="form-group sort_order_accordion" data-accordion="History">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'History' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_intake',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_intake',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_intake" data-toggle="<?= in_array('view_ticket_intake',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_intake',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_intake',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'History' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("History", $all_config) ? 'checked disabled' : (in_array("History", $value_config) ? "checked" : '') ?> value="History" name="tickets[]"> Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Work History') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Work History">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Work History' ?></span>:<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_work_history',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_work_history',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_work_history" data-toggle="<?= in_array('ticket_work_history',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_work_history',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_work_history',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Work History' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Work History", $all_config) ? 'checked disabled' : (in_array("Work History", $value_config) ? "checked" : '') ?> value="Work History" name="tickets[]"> Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Work History Services') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Work History Services", $all_config) ? 'checked disabled' : (in_array("Work History Services", $value_config) ? "checked" : '') ?> value="Work History Services" name="tickets[]"> Include Services</label>
							<?php } ?>
							<?php if($field_sort_field == 'Work History Service Sub Totals') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Work History Service Sub Totals", $all_config) ? 'checked disabled' : (in_array("Work History Service Sub Totals", $value_config) ? "checked" : '') ?> value="Work History Service Sub Totals" name="tickets[]"> Include Service Sub Totals</label>
							<?php } ?>
							<?php if($field_sort_field == 'Work History Staff Tasks') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Work History Staff Tasks", $all_config) ? 'checked disabled' : (in_array("Work History Staff Tasks", $value_config) ? "checked" : '') ?> value="Work History Staff Tasks" name="tickets[]"> Include Staff Tasks</label>
							<?php } ?>
							<?php if($field_sort_field == 'Work History Materials') { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Work History Materials", $all_config) ? 'checked disabled' : (in_array("Work History Materials", $value_config) ? "checked" : '') ?> value="Work History Materials" name="tickets[]"> Include Materials</label>
							<?php } ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }
	}

	//Close higher level ending if not already closed
	if(!$current_heading_closed) { ?>
			</div>
		</div>
	<?php } ?>
</div>

<?php if(!$action_mode && !$estimate_mode && !$status_fields && !$overview_mode && !$unlock_mode && !$intake_mode) { ?>
	<a href="" onclick="addCustomAccordion(); return false;" class="btn brand-btn pull-right gap-bottom">Add Custom Accordion</a>
	<span class="popover-examples pull-right"><a data-toggle="tooltip" data-original-title="This will allow you to create custom accordions that have certain details in them for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>
	<div class="clearfix"></div>
	<a href="" onclick="addHigherLevelHeading(); return false;" class="btn brand-btn pull-right gap-bottom">Add Higher Level Heading</a>
	<span class="popover-examples pull-right"><a data-toggle="tooltip" data-original-title="This will allow you to rearrange the headings of the <?= TICKET_NOUN ?> into subtabs along the left of the ticket."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>
	<div class="clearfix"></div>
<?php } ?>
