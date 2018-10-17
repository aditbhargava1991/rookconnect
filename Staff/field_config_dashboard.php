<?php include_once('../include.php');
checkAuthorised('staff'); ?>
<script>
$(document).ready(function() {
	$('input[type=checkbox]').change(function() {
		if(this.name == 'dashboard' || this.name == 'staff_tabs' || this.name == 'id_card_fields' || this.name == 'db_tabs') {
			var value = [];
			$('[name="'+this.name+'"]:checked').each(function() {
				value.push(this.value);
			});
			$.ajax({
				url: 'staff_ajax.php?action='+(this.name == 'dashboard' ? 'dashboard_fields' : (this.name == 'staff_tabs' ? 'staff_tabs' : (this.name == 'db_tabs' ? 'db_tabs' : 'id_card_fields'))),
				method: 'POST',
				data: {
					value: ','+value.join(',')+','
				},
				success: function(response) {
					console.log(response);
				}
			});
		}
	});
});
</script>

<h3>Dashboard Fields</h3>
<?php $db_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts_dashboard` FROM `field_config_contacts` WHERE `tab`='Staff' AND `contacts_dashboard` IS NOT NULL"),MYSQLI_NUM); ?>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Business,') !== false ? 'checked' : '' ?> value="Business">Business</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Employee ID,') !== false ? 'checked' : '' ?> value="Employee ID">Employee ID</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Employee #,') !== false ? 'checked' : '' ?> value="Employee #">Employee #</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Category,') !== false ? 'checked' : '' ?> value="Category">Category</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Staff Category,') !== false ? 'checked' : '' ?> value="Staff Category">Staff Category</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',License,') !== false ? 'checked' : '' ?> value="License">License#</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Login,') !== false ? 'checked' : '' ?> value="Login">Login</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Email,') !== false ? 'checked' : '' ?> value="Email">Email</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Company Email,') !== false ? 'checked' : '' ?> value="Company Email">Company Email</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Home Phone,') !== false ? 'checked' : '' ?> value="Home Phone">Home Phone</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Office Phone,') !== false ? 'checked' : '' ?> value="Office Phone">Office Phone</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Cell Phone,') !== false ? 'checked' : '' ?> value="Cell Phone">Cell Phone</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Address,') !== false ? 'checked' : '' ?> value="Address">Address</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Position,') !== false ? 'checked' : '' ?> value="Position">Position</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Pronoun,') !== false ? 'checked' : '' ?> value="Pronoun">Preferred Pronoun</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Birthdate,') !== false ? 'checked' : '' ?> value="Birthdate">Date of Birth / Age</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Social,') !== false ? 'checked' : '' ?> value="Social">Social Media Links</label>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Rate Card,') !== false ? 'checked' : '' ?> value="Rate Card">Rate Card</label>
<!--<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Rate Card Price,') !== false ? 'checked' : '' ?> value="Rate Card Price">Rate Card Price</label>-->

<h3>Dashboard Sort Fields</h3>
<label class="form-checkbox"><input type="checkbox" name="dashboard" <?= strpos($db_config[0], ',Sort Match Contacts,') !== false ? 'checked' : '' ?> value="Sort Match Contacts">Match Contacts</label>

<h3>Dashboard Tabs</h3>
<label class="form-checkbox"><input type="checkbox" name="db_tabs" <?= in_array('probation', $db_tabs) ? 'checked' : '' ?> value="probation">Staff on Probation</label>
<label class="form-checkbox"><input type="checkbox" name="db_tabs" <?= in_array('suspended', $db_tabs) ? 'checked' : '' ?> value="suspended">Suspended Staff</label>
<label class="form-checkbox"><input type="checkbox" name="db_tabs" <?= in_array('security', $db_tabs) ? 'checked' : '' ?> value="security">Security Privileges</label>
<label class="form-checkbox"><input type="checkbox" name="db_tabs" <?= in_array('positions', $db_tabs) ? 'checked' : '' ?> value="positions">Positions</label>
<label class="form-checkbox"><input type="checkbox" name="db_tabs" <?= in_array('reminders', $db_tabs) ? 'checked' : '' ?> value="reminders">Reminders</label>
<label class="form-checkbox"><input type="checkbox" name="db_tabs" <?= in_array('reporting', $db_tabs) ? 'checked' : '' ?> value="reporting">Reporting</label>

<h3>Staff Tabs</h3>
<?php $staff_field_subtabs = ','.get_config($dbc, 'staff_field_subtabs').','; ?>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',ID Card,') !== false ? 'checked' : '' ?> value="ID Card">ID Card</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Staff Information,') !== false ? 'checked' : '' ?> value="Staff Information">Profile</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Staff Bio,') !== false ? 'checked' : '' ?> value="Staff Bio">Staff Bio</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Staff Address,') !== false ? 'checked' : '' ?> value="Staff Address">Staff Address</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Position,') !== false ? 'checked' : '' ?> value="Position">Position</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Employee Information,') !== false ? 'checked' : '' ?> value="Employee Information">Employee Information</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Driver Information,') !== false ? 'checked' : '' ?> value="Driver Information">Driver Information</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Direct Deposit Information,') !== false ? 'checked' : '' ?> value="Direct Deposit Information">Direct Deposit Information</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" checked disabled value="Software ID">Software ID</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" checked disabled value="Software Access">Software Access</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Social Media,') !== false ? 'checked' : '' ?> value="Social Media">Social Media</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Emergency,') !== false ? 'checked' : '' ?> value="Emergency">Emergency</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Health,') !== false ? 'checked' : '' ?> value="Health">Health Care</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Health Concerns,') !== false ? 'checked' : '' ?> value="Health Concerns">Health Concerns</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Allergies,') !== false ? 'checked' : '' ?> value="Allergies">Allergies</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Company Benefits,') !== false ? 'checked' : '' ?> value="Company Benefits">Company Benefits</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Schedule,') !== false ? 'checked' : '' ?> value="Schedule">Staff Schedule</label>
<?php if(tile_enabled($dbc, 'project')['user_enabled'] > 0) { ?>
	<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Project,') !== false ? 'checked' : '' ?> value="Project"><?= PROJECT_TILE ?></label>
<?php }
if(tile_enabled($dbc, 'ticket')['user_enabled'] > 0) { ?>
	<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Ticket,') !== false ? 'checked' : '' ?> value="Ticket"><?= TICKET_TILE ?></label>
<?php } ?>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',HR,') !== false ? 'checked' : '' ?> value="HR">HR Record</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Staff Documents,') !== false ? 'checked' : '' ?> value="Staff Documents">Staff Documents</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Incident Reports,') !== false ? 'checked' : '' ?> value="Incident Reports"><?= INC_REP_TILE ?></label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Time Off,') !== false ? 'checked' : '' ?> value="Time Off">Time Off Requests</label>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Certificates,') !== false ? 'checked' : '' ?> value="Certificates">Certificates</label>
<?php if(tile_enabled($dbc, 'project')['user_enabled'] > 0) { ?>
    <label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Rate Card,') !== false ? 'checked' : '' ?> value="Rate Card">Rate Card</label>
<?php } ?>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',History,') !== false ? 'checked' : '' ?> value="History">History</label>

<h3>Profile Tabs</h3>
<label class="form-checkbox"><input type="checkbox" name="staff_tabs" <?= strpos($staff_field_subtabs, ',Goals and Objectives,') !== false ? 'checked' : '' ?> value="Goals and Objectives">Goals and Objectives</label>

<h3>ID Card Fields</h3>
<?php $id_card_fields = get_config($dbc, 'staff_id_card_fields');
$field_config = '';
$config_query = mysqli_query($dbc,"SELECT contacts FROM field_config_contacts WHERE tab='Staff' AND `accordion` IS NOT NULL AND `order` IS NOT NULL ORDER BY `subtab`, `order`");
while($config_row = mysqli_fetch_assoc($config_query)) {
	$field_config .= ','.$config_row['contacts'].',';
}
$field_config = explode(',',$field_config);
if($id_card_fields == '') {
	$id_card_fields = explode(',',$field_config);
} else {
	$id_card_fields = explode(',',$id_card_fields);
} ?>
<?php if(in_array_any(['Employee Number','Employee ID','Employee #'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Employee Number','Employee ID','Employee #'], $id_card_fields) ? 'checked' : '' ?> value="Employee Number">Employee Number</label><?php } ?>
<?php if(in_array_any(['First Name','Last Name','Profile First Name','Profile Last Name'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['ID Card Name','First Name','Last Name','Profile First Name','Profile Last Name'], $id_card_fields) ? 'checked' : '' ?> value="ID Card Name">Name</label><?php } ?>
<?php if(in_array_any(['Position'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Position'], $id_card_fields) ? 'checked' : '' ?> value="Position">Position</label><?php } ?>
<?php if(in_array_any(['Home Phone','Profile Home Phone'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Home Phone','Profile Home Phone'], $id_card_fields) ? 'checked' : '' ?> value="Home Phone">Home Phone</label><?php } ?>
<?php if(in_array_any(['Office Phone','Profile Office Phone'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Office Phone','Profile Office Phone'], $id_card_fields) ? 'checked' : '' ?> value="Office Phone">Office Phone</label><?php } ?>
<?php if(in_array_any(['Cell Phone','Profile Cell Phone'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Cell Phone','Profile Cell Phone'], $id_card_fields) ? 'checked' : '' ?> value="Cell Phone">Cell Phone</label><?php } ?>
<?php if(in_array_any(['Email Address','Profile Email Address'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Email Address','Profile Email Address'], $id_card_fields) ? 'checked' : '' ?> value="Email Address">Email Address</label><?php } ?>
<?php if(in_array_any(['Company Email Address','Profile Company Email Address'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Company Email Address','Profile Company Email Address'], $id_card_fields) ? 'checked' : '' ?> value="Company Email Address">Company Email Address</label><?php } ?>
<?php if(in_array_any(['Start Date'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Start Date'], $id_card_fields) ? 'checked' : '' ?> value="Start Date">Start Date</label><?php } ?>
<?php if(in_array_any(['Business','Program Business'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Business','Program Business'], $id_card_fields) ? 'checked' : '' ?> value="Business"><?= BUSINESS_CAT ?></label><?php } ?>
<?php if(in_array_any(['Name'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Name'], $id_card_fields) ? 'checked' : '' ?> value="Name">Name</label><?php } ?>
<?php if(in_array_any(['Location','Profile Location'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Location','Profile Location'], $id_card_fields) ? 'checked' : '' ?> value="Location">Location</label><?php } ?>
<?php if(in_array_any(['Business Address'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Business Address'], $id_card_fields) ? 'checked' : '' ?> value="Business Address">Business Address</label><?php } ?>
<?php if(in_array_any(['Mailing Address'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Mailing Address'], $id_card_fields) ? 'checked' : '' ?> value="Mailing Address">Mailing Address</label><?php } ?>
<?php if(in_array_any(['Address'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Address'], $id_card_fields) ? 'checked' : '' ?> value="Address">Address</label><?php } ?>
<?php if(in_array_any(['Birth Date','Date of Birth','Profile Date of Birth'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Birth Date','Date of Birth','Profile Date of Birth'], $id_card_fields) ? 'checked' : '' ?> value="Birth Date">Birth Date</label><?php } ?>
<label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Star Rating'], $id_card_fields) ? 'checked' : '' ?> value="Star Rating">Star Rating</label>
<?php if(in_array_any(['LinkedIn','Profile LinkedIn'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['LinkedIn','Profile LinkedIn'], $id_card_fields) ? 'checked' : '' ?> value="LinkedIn">LinkedIn</label><?php } ?>
<?php if(in_array_any(['Facebook','Profile Facebook'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Facebook','Profile Facebook'], $id_card_fields) ? 'checked' : '' ?> value="Facebook">Facebook</label><?php } ?>
<?php if(in_array_any(['Twitter','Profile Twitter'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Twitter','Profile Twitter'], $id_card_fields) ? 'checked' : '' ?> value="Twitter">Twitter</label><?php } ?>
<?php if(in_array_any(['Google+','Profile Google+'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Google+','Profile Google+'], $id_card_fields) ? 'checked' : '' ?> value="Google+">Google+</label><?php } ?>
<?php if(in_array_any(['Instagram','Profile Instagram'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Instagram','Profile Instagram'], $id_card_fields) ? 'checked' : '' ?> value="Instagram">Instagram</label><?php } ?>
<?php if(in_array_any(['Pinterest','Profile Pinterest'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Pinterest','Profile Pinterest'], $id_card_fields) ? 'checked' : '' ?> value="Pinterest">Pinterest</label><?php } ?>
<?php if(in_array_any(['YouTube','Profile YouTube'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['YouTube','Profile YouTube'], $id_card_fields) ? 'checked' : '' ?> value="YouTube">YouTube</label><?php } ?>
<?php if(in_array_any(['Blog','Profile Blog'], $field_config)) { ?><label class="form-checkbox"><input type="checkbox" name="id_card_fields" <?= in_array_any(['Blog','Profile Blog'], $id_card_fields) ? 'checked' : '' ?> value="Blog">Blog</label><?php } ?>