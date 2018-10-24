<?php if($field_option == 'Contact Since') { ?>
	<label class="col-sm-4 control-label">Contact Since:</label>
	<div class="col-sm-8">
		<input type="text" name="contact_since" value="<?= $contact['contact_since'] ?>" data-field="contact_since" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Date of Last Contact') { ?>
	<label class="col-sm-4 control-label">Date of Last Contact:</label>
	<div class="col-sm-8">
		<input type="text" name="date_of_last_contact" value="<?= $contact['date_of_last_contact'] ?>" data-field="date_of_last_contact" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Start Date') { ?>
	<label class="col-sm-4 control-label">Start Date:</label>
	<div class="col-sm-8">
		<input type="text" name="start_date" value="<?= $contact['start_date'] ?>" data-field="start_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Expiry Date') { ?>
	<label class="col-sm-4 control-label">Expiry Date:</label>
	<div class="col-sm-8">
		<input type="text" name="expiry_date" value="<?= $contact['expiry_date'] ?>" data-field="expiry_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Renewal Date') { ?>
	<label class="col-sm-4 control-label">Renewal Date:</label>
	<div class="col-sm-8">
		<input type="text" name="renewal_date" value="<?= $contact['renewal_date'] ?>" data-field="renewal_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Lease Term Date') { ?>
	<label class="col-sm-4 control-label">Lease Term Date:</label>
	<div class="col-sm-8">
		<input type="text" name="lease_term_date" value="<?= $contact['lease_term_date'] ?>" data-field="lease_term_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Date Contract Signed') { ?>
	<label class="col-sm-4 control-label">Date Contract Signed:</label>
	<div class="col-sm-8">
		<input type="text" name="date_contract_signed" value="<?= $contact['date_contract_signed'] ?>" data-field="date_contract_signed" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Option to Renew Date') { ?>
	<label class="col-sm-4 control-label">Option to Renew Date:</label>
	<div class="col-sm-8">
		<input type="text" name="option_to_renew_date" value="<?= $contact['option_to_renew_date'] ?>" data-field="option_to_renew_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Rate Increase Date') { ?>
	<label class="col-sm-4 control-label">Rate Increase Date:</label>
	<div class="col-sm-8">
		<input type="text" name="rate_increase_date" value="<?= $contact['rate_increase_date'] ?>" data-field="rate_increase_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Insurance Expiry Date') { ?>
	<label class="col-sm-4 control-label">Insurance Expiry Date:</label>
	<div class="col-sm-8">
		<input type="text" name="insurance_expiry_date" value="<?= $contact['insurance_expiry_date'] ?>" data-field="insurance_expiry_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Account Expiry Date') { ?>
	<label class="col-sm-4 control-label">Account Expiry Date:</label>
	<div class="col-sm-8">
		<input type="text" name="account_expiry_date" value="<?= $contact['account_expiry_date'] ?>" data-field="account_expiry_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Hire Date') { ?>
	<label class="col-sm-4 control-label">Hire Date:</label>
	<div class="col-sm-8">
		<input type="text" name="hire_date" value="<?= $contact['hire_date'] ?>" data-field="hire_date" data-table="contacts" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Probation End Date') { ?>
	<label class="col-sm-4 control-label">Probation End Date:</label>
	<div class="col-sm-8">
		<input type="text" name="probation_end_date" value="<?= $contact['probation_end_date'] ?>" data-field="probation_end_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Probation Expiry Reminder Date') { ?>
	<label class="col-sm-4 control-label">Probation Expiry Reminder Date:</label>
	<div class="col-sm-8">
		<input type="text" name="probation_expiry_reminder_date" value="<?= $contact['probation_expiry_reminder_date'] ?>" data-field="probation_expiry_reminder_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Birth Date') { ?>
	<label class="col-sm-4 control-label">Birth Date:</label>
	<div class="col-sm-8">
		<input type="text" name="birth_date" value="<?= $contact['birth_date'] ?>" data-field="birth_date" data-table="contacts" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Date of Birth' || $field_option == 'Profile Date of Birth') { ?>
	<label class="col-sm-4 control-label">Date of Birth:</label>
	<div class="col-sm-8">
		<input type="text" name="birth_date" value="<?= $contact['birth_date'] ?>" data-field="birth_date" data-table="contacts" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Days Per Week') { ?>
	<label class="col-sm-4 control-label"># of Days Per Week:</label>
	<div class="col-sm-8">
		<input type="number" name="days_per_week" value="<?= $contact['days_per_week'] ?>" data-field="days_per_week" data-table="contacts" class="form-control">
	</div>
<?php } else if($field_option == 'Hours Per Week') { ?>
	<label class="col-sm-4 control-label"># of Hours Per Week:</label>
	<div class="col-sm-8">
		<input type="number" name="hours_per_week" value="<?= $contact['hours_per_week'] ?>" data-field="hours_per_week" data-table="contacts" class="form-control">
	</div>
<?php } else if($field_option == 'Company Benefit Start Date') { ?>
	<label class="col-sm-4 control-label">Company Benefit Start Date:</label>
	<div class="col-sm-8">
		<input type="text" name="company_benefit_start_date" value="<?= $contact['company_benefit_start_date'] ?>" data-field="company_benefit_start_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Medications Completed Date') { ?>
	<label class="col-sm-4 control-label">Medications Completed Date:</label>
	<div class="col-sm-8">
		<input type="text" name="medications_completed_date" value="<?= $contact['medications_completed_date'] ?>" data-field="medications_completed_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Medications Management Completed Date') { ?>
	<label class="col-sm-4 control-label">Medications Management Completed Date:</label>
	<div class="col-sm-8">
		<input type="text" name="medications_management_completed_date" value="<?= $contact['medications_management_completed_date'] ?>" data-field="medications_management_completed_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Protocols Completed Date') { ?>
	<label class="col-sm-4 control-label">Protocols Completed Date:</label>
	<div class="col-sm-8">
		<input type="text" name="protocols_completed_date" value="<?= $contact['protocols_completed_date'] ?>" data-field="protocols_completed_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Protocols Management Completed Date') { ?>
	<label class="col-sm-4 control-label">Protocols Management Completed Date:</label>
	<div class="col-sm-8">
		<input type="text" name="protocols_management_completed_date" value="<?= $contact['protocols_management_completed_date'] ?>" data-field="protocols_management_completed_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Routines Completed Date') { ?>
	<label class="col-sm-4 control-label">Routines Completed Date:</label>
	<div class="col-sm-8">
		<input type="text" name="routines_completed_date" value="<?= $contact['routines_completed_date'] ?>" data-field="routines_completed_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Routines Management Completed Date') { ?>
	<label class="col-sm-4 control-label">Routines Management Completed Date:</label>
	<div class="col-sm-8">
		<input type="text" name="routines_management_completed_date" value="<?= $contact['routines_management_completed_date'] ?>" data-field="routines_management_completed_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Communication Completed Date') { ?>
	<label class="col-sm-4 control-label">Communication Completed Date:</label>
	<div class="col-sm-8">
		<input type="text" name="communication_completed_date" value="<?= $contact['communication_completed_date'] ?>" data-field="communication_completed_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Communication Management Completed Date') { ?>
	<label class="col-sm-4 control-label">Communication Management Completed Date:</label>
	<div class="col-sm-8">
		<input type="text" name="communication_management_completed_date" value="<?= $contact['communication_management_completed_date'] ?>" data-field="communication_management_completed_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Activities Completed Date') { ?>
	<label class="col-sm-4 control-label">Activities Completed Date:</label>
	<div class="col-sm-8">
		<input type="text" name="activities_completed_date" value="<?= $contact['activities_completed_date'] ?>" data-field="activities_completed_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Activities Management Completed Date') { ?>
	<label class="col-sm-4 control-label">Activities Management Completed Date:</label>
	<div class="col-sm-8">
		<input type="text" name="activities_management_completed_date" value="<?= $contact['activities_management_completed_date'] ?>" data-field="activities_management_completed_date" data-table="contacts_dates" class="datepicker form-control">
	</div>
<?php } else if($field_option == 'Membership Since') { ?>
	<label class="col-sm-4 control-label">Membership Since:</label>
	<div class="col-sm-8">
		<input type="text" name="membership_since" value="<?= $contact['membership_since'] ?>" data-field="membership_since" data-table="contacts_medical" class="form-control datepicker">
	</div>
<?php } else if($field_option == 'Membership Renewal Date') { ?>
	<label class="col-sm-4 control-label">Membership Renewal Date:</label>
	<div class="col-sm-8">
		<input type="text" name="membership_renewal_date" value="<?= $contact['membership_renewal_date'] ?>" data-field="membership_renewal_date" data-table="contacts_medical" class="form-control datepicker">
	</div>
<?php } else if($field_option == 'Contract End Date') { ?>
	<label class="col-sm-4 control-label">Contract End Date:</label>
	<div class="col-sm-8">
		<input type="text" name="contract_end_date" value="<?= $contact['contract_end_date'] ?>" data-field="contract_end_date" data-table="contacts_dates" class="form-control datepicker">
	</div>
<?php } else if($field_option == 'Contract Start Date') { ?>
	<label class="col-sm-4 control-label">Contract Start Date:</label>
	<div class="col-sm-8">
		<input type="text" name="contract_start_date" value="<?= $contact['contract_start_date'] ?>" data-field="contract_start_date" data-table="contacts_dates" class="form-control datepicker">
	</div>
<?php }