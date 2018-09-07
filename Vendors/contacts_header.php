<?php
if (strpos($value_config, ','."Employee ID".',') !== FALSE) {
	if(isset($_GET['sortby']) && $_GET['sortby'] == 'contactid')
		echo '<th>Employee ID</th>';
	else
		echo '<th><a href='.addOrUpdateUrlParam("sortby","contactid").'>Employee ID</a></th>';
}
if (strpos($value_config, ','."Business".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'businessid')
	echo '<th>'.(get_software_name() == 'breakthebarrier' ? 'Program/Site' : 'Business').'</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","businessid").'>'.(get_software_name() == 'breakthebarrier' ? 'Program/Site' : 'Business').'</a></th>';

}

if (strpos($value_config, ','."Site Name (Location)".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'site_name')
	echo '<th>Site Name</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","site_name").'>Site Name</a></th>';
}

if (strpos($value_config, ','."Name".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'name')
	echo '<th>'.$category.' Name</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","name").'>'.$category.' Name</a></th>';
}

if (strpos($value_config, ','."Customer(Client/Customer/Business)".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'customer')
	echo '<th>Customer</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","customer").'>Display Name</a></th>';
}

if (strpos($value_config, ','."Display Name".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'display_name')
	echo '<th>Display Name</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","display_name").'>Display Name</a></th>';
}

if (strpos($value_config, ','."Contact Category".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'category')
	echo '<th>Contact Category</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","category").'>Contact Category</a></th>';
}

if (strpos($value_config, ','."First Name".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'first_name')
	echo '<th>First Name</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","first_name").'>First Name</a></th>';
}

if (strpos($value_config, ','."Last Name".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'last_name')
	echo '<th>Last Name</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","last_name").'>Last Name</a></th>';
}
if (strpos($value_config, ','."Nick Name".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'nick_name')
	echo '<th>Nick Name</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","nick_name").'>Nick Name</a></th>';
}

if (strpos($value_config, ','."Assigned Staff".',') !== FALSE) {
    echo '<th>Assigned Staff</th>';
}
if (strpos($value_config, ','."Gender".',') !== FALSE) {
    echo '<th>Gender</th>';
}
if (strpos($value_config, ','."License Number".',') !== FALSE) {
    echo '<th>License Number</th>';
}
if (strpos($value_config, ','."Credentials".',') !== FALSE) {
    echo '<th>Credentials</th>';
}
if (strpos($value_config, ','."Alberta Health Care No".',') !== FALSE) {
    echo '<th>Alberta Health Care No</th>';
}
if (strpos($value_config, ','."Invoice".',') !== FALSE) {
    echo '<th>Invoice</th>';
}
if (strpos($value_config, ','."MVA".',') !== FALSE) {
    echo '<th>MVA</th>';
}
if (strpos($value_config, ','."Maintenance Patient".',') !== FALSE) {
    echo '<th>Maintenance Patient</th>';
}

if (strpos($value_config, ','."Correspondence Language".',') !== FALSE) {
    echo '<th>Correspondence Language</th>';
}
if (strpos($value_config, ','."Amount To Bill".',') !== FALSE) {
    echo '<th>Amount To Bill</th>';
}
if (strpos($value_config, ','."Amount Owing".',') !== FALSE) {
    echo '<th>Amount Owing</th>';
}
if (strpos($value_config, ','."Amount Credit".',') !== FALSE) {
    echo '<th>Amount Credit</th>';
}















if (strpos($value_config, ','."Business Contacts".',') !== FALSE) {
	echo '<th>Contacts</th>';
}

if (strpos($value_config, ','."Role".',') !== FALSE) {
echo '<th>Role</th>';
}

if (strpos($value_config, ','."Division".',') !== FALSE) {
echo '<th>Division</th>';
}

if (strpos($value_config, ','."User Name".',') !== FALSE) {
echo '<th>Username</th>';
}

if (strpos($value_config, ','."Password".',') !== FALSE) {
echo '<th>Password</th>';
}

if (strpos($value_config, ','."Name on Account".',') !== FALSE) {
echo '<th>Name on Account</th>';
}

if (strpos($value_config, ','."Operating As".',') !== FALSE) {
echo '<th>Operating As</th>';
}

if (strpos($value_config, ','."Emergency Contact".',') !== FALSE) {
echo '<th>Emergency Contact</th>';
}

if (strpos($value_config, ','."Occupation".',') !== FALSE) {
echo '<th>Occupation</th>';
}

if (strpos($value_config, ','."Office Phone".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'office_phone')
	echo '<th>Office Phone</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","office_phone").'>Office Phone</a></th>';
}

if (strpos($value_config, ','."Cell Phone".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'cell_phone')
	echo '<th>Cell Phone</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","cell_phone").'>Cell Phone</a></th>';
}

if (strpos($value_config, ','."Home Phone".',') !== FALSE) {
echo '<th>Home Phone</th>';
}

if (strpos($value_config, ','."Fax".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'fax')
	echo '<th>Fax</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","fax").'>Fax</a></th>';
}

if (strpos($value_config, ','."Email Address".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'email_address')
	echo '<th>Email Address</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","email_address").'>Email Address</a></th>';

}

if (strpos($value_config, ','."Account Balance".',') !== FALSE) {
echo '<th>Account Balance</th>';
}

if (strpos($value_config, ','."Website".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'website')
	echo '<th>Website</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","website").'>Website</a></th>';
}

if (strpos($value_config, ','."Customer Address".',') !== FALSE) {
echo '<th>Customer Address</th>';
}

if (strpos($value_config, ','."Referred By".',') !== FALSE) {
echo '<th>Referred By</th>';
}

if (strpos($value_config, ','."Company".',') !== FALSE) {
echo '<th>Company</th>';
}

if (strpos($value_config, ','."Position".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'position')
	echo '<th>Position</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","position").'>Position</a></th>';
}

if (strpos($value_config, ','."Title".',') !== FALSE) {
echo '<th>Title</th>';
}

if (strpos($value_config, ','."LinkedIn".',') !== FALSE) {
echo '<th>LinkedIn</th>';
}

if (strpos($value_config, ','."Twitter".',') !== FALSE) {
echo '<th>Twitter</th>';
}

if (strpos($value_config, ','."DUNS".',') !== FALSE) {
echo '<th>DUNS</th>';
}

if (strpos($value_config, ','."CAGE".',') !== FALSE) {
echo '<th>CAGE</th>';
}

if (strpos($value_config, ','."Self Identification".',') !== FALSE) {
echo '<th>Self Identification</th>';
}

if (strpos($value_config, ','."Tax Exemption".',') !== FALSE) {
echo '<th>Tax Exemption</th>';
}

if (strpos($value_config, ','."Tax Exemption Number".',') !== FALSE) {
echo '<th>Tax Exemption Number</th>';
}

if (strpos($value_config, ','."AISH Card#".',') !== FALSE) {
echo '<th>AISH Card#</th>';
}

if (strpos($value_config, ','."License Plate #".',') !== FALSE) {
echo '<th>License Plate #</th>';
}

if (strpos($value_config, ','."CARFAX".',') !== FALSE) {
echo '<th>CARFAX</th>';
}

if (strpos($value_config, ','."Mailing Address".',') !== FALSE) {
echo '<th>Mailing Address</th>';
}

if (strpos($value_config, ','."Business Address".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'business_address')
	echo '<th>'.(get_software_name() == 'breakthebarrier' ? 'Program/Site' : 'Business').' Address</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","business_address").'>'.(get_software_name() == 'breakthebarrier' ? 'Program/Site' : 'Business').' Address</a></th>';
}

if (strpos($value_config, ','."Ship To Address".',') !== FALSE) {
echo '<th>Ship To Address</th>';
}

if (strpos($value_config, ','."Postal Code".',') !== FALSE) {
echo '<th>Postal Code</th>';
}

if (strpos($value_config, ','."Zip Code".',') !== FALSE) {
echo '<th>Zip/Postal Code</th>';
}

if (strpos($value_config, ','."City".',') !== FALSE) {
echo '<th>City</th>';
}

if (strpos($value_config, ','."Province".',') !== FALSE) {
echo '<th>Province</th>';
}

if (strpos($value_config, ','."State".',') !== FALSE) {
echo '<th>State</th>';
}

if (strpos($value_config, ','."Country".',') !== FALSE) {
echo '<th>Country</th>';
}

if (strpos($value_config, ','."Ship City".',') !== FALSE) {
echo '<th>Ship City</th>';
}
if (strpos($value_config, ','."Ship State".',') !== FALSE) {
echo '<th>Ship State</th>';
}
if (strpos($value_config, ','."Ship Country".',') !== FALSE) {
echo '<th>Ship Country</th>';
}
if (strpos($value_config, ','."Ship Zip".',') !== FALSE) {
echo '<th>Ship Zip</th>';
}

if (strpos($value_config, ','."Google Maps Address".',') !== FALSE) {
echo '<th>Google Maps Address</th>';
}

if (strpos($value_config, ','."City Part".',') !== FALSE) {
echo '<th>City Part</th>';
}

if (strpos($value_config, ','."Account Number".',') !== FALSE) {
echo '<th>Account Number</th>';
}

if (strpos($value_config, ','."Payment Type".',') !== FALSE) {
echo '<th>Payment Type</th>';
}

if (strpos($value_config, ','."Payment Type".',') !== FALSE) {
echo '<th>Payment Type</th>';
}

if (strpos($value_config, ','."Payment Address".',') !== FALSE) {
echo '<th>Payment Address</th>';
}

if (strpos($value_config, ','."Payment City".',') !== FALSE) {
echo '<th>Payment City</th>';
}

if (strpos($value_config, ','."Payment State".',') !== FALSE) {
echo '<th>Payment State</th>';
}

if (strpos($value_config, ','."Payment Postal Code".',') !== FALSE) {
echo '<th>Payment Postal Code</th>';
}

if (strpos($value_config, ','."Payment Zip Code".',') !== FALSE) {
echo '<th>Payment Zip/Postal Code</th>';
}

if (strpos($value_config, ','."GST #".',') !== FALSE) {
echo '<th>GST #</th>';
}

if (strpos($value_config, ','."PST #".',') !== FALSE) {
echo '<th>PST #</th>';
}

if (strpos($value_config, ','."Vendor GST #".',') !== FALSE) {
echo '<th>'.VENDOR_TILE.' GST #</th>';
}

if (strpos($value_config, ','."Payment Information".',') !== FALSE) {
echo '<th>Payment Information</th>';
}

if (strpos($value_config, ','."Pricing Level".',') !== FALSE) {
echo '<th>Pricing Level</th>';
}

if (strpos($value_config, ','."Unit #".',') !== FALSE) {
echo '<th>Unit #</th>';
}

if (strpos($value_config, ','."Bay #".',') !== FALSE) {
echo '<th>Bay #</th>';
}

if (strpos($value_config, ','."Option to Renew".',') !== FALSE) {
echo '<th>Option to Renew</th>';
}

if (strpos($value_config, ','."Lease Term - # of years".',') !== FALSE) {
echo '<th>Lease Term - # of years</th>';
}

if (strpos($value_config, ','."Commercial Insurer".',') !== FALSE) {
echo '<th>Commercial Insurer</th>';
}

if (strpos($value_config, ','."Residential Insurer".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'residential_insurer')
	echo '<th>Residential Insurer</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","residential_insurer").'>Residential Insurer</a></th>';
}

if (strpos($value_config, ','."WCB #".',') !== FALSE) {
echo '<th>WCB #</th>';
}

if (strpos($value_config, ','."Total Monthly Rate".',') !== FALSE) {
echo '<th>Total Monthly Rate</th>';
}

if (strpos($value_config, ','."Total Annual Rate".',') !== FALSE) {
echo '<th>Total Annual Rate</th>';
}

if (strpos($value_config, ','."Condo Fees".',') !== FALSE) {
echo '<th>Condo Fees</th>';
}

if (strpos($value_config, ','."Deposit".',') !== FALSE) {
echo '<th>Deposit</th>';
}

if (strpos($value_config, ','."Damage Deposit".',') !== FALSE) {
echo '<th>Damage Deposit</th>';
}

if (strpos($value_config, ','."Cost".',') !== FALSE) {
echo '<th>Cost</th>';
}

if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
echo '<th>Final Retail Price</th>';
}

if (strpos($value_config, ','."Admin Price".',') !== FALSE) {
echo '<th>Admin Price</th>';
}

if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) {
echo '<th>Wholesale Price</th>';
}

if (strpos($value_config, ','."Commercial Price".',') !== FALSE) {
echo '<th>Commercial Price</th>';
}

if (strpos($value_config, ','."Client Price".',') !== FALSE) {
echo '<th>Client Price</th>';
}

if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
echo '<th>Minimum Billable</th>';
}

if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) {
echo '<th>Estimated Hours</th>';
}

if (strpos($value_config, ','."Actual Hours".',') !== FALSE) {
echo '<th>Actual Hours</th>';
}

if (strpos($value_config, ','."MSRP".',') !== FALSE) {
echo '<th>MSRP</th>';
}

if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) {
echo '<th>Hourly Rate</th>';
}

if (strpos($value_config, ','."Monthly Rate".',') !== FALSE) {
echo '<th>Monthly Rate</th>';
}

if (strpos($value_config, ','."Semi Monthly Rate".',') !== FALSE) {
echo '<th>Semi Monthly Rate</th>';
}

if (strpos($value_config, ','."Daily Rate".',') !== FALSE) {
echo '<th>Daily Rate</th>';
}

if (strpos($value_config, ','."HR Rate Work".',') !== FALSE) {
echo '<th>HR Rate Work</th>';
}

if (strpos($value_config, ','."HR Rate Travel".',') !== FALSE) {
echo '<th>HR Rate Travel</th>';
}

if (strpos($value_config, ','."Field Day Cost".',') !== FALSE) {
echo '<th>Field Day Cost</th>';
}

if (strpos($value_config, ','."Field Day Billable".',') !== FALSE) {
echo '<th>Field Day Billable</th>';
}

if (strpos($value_config, ','."Probation Pay Rate".',') !== FALSE) {
echo '<th>Probation Pay Rate</th>';
}

if (strpos($value_config, ','."Base Pay".',') !== FALSE) {
echo '<th>Base Pay</th>';
}

if (strpos($value_config, ','."Performance Pay".',') !== FALSE) {
echo '<th>Performance Pay</th>';
}

if (strpos($value_config, ','."Unit #".',') !== FALSE) {
echo '<th>Unit #</th>';
}

if (strpos($value_config, ','."Base Rent".',') !== FALSE) {
echo '<th>Base Rent</th>';
}

if (strpos($value_config, ','."Base Rent/Sq. Ft.".',') !== FALSE) {
echo '<th>Base Rent/Sq. Ft.</th>';
}

if (strpos($value_config, ','."CAC".',') !== FALSE) {
echo '<th>CAC</th>';
}

if (strpos($value_config, ','."CAC/Sq. Ft.".',') !== FALSE) {
echo '<th>CAC/Sq. Ft.</th>';
}

if (strpos($value_config, ','."Property Tax".',') !== FALSE) {
echo '<th>Property Tax</th>';
}

if (strpos($value_config, ','."Property Tax/Sq. Ft.".',') !== FALSE) {
echo '<th>Property Tax/Sq. Ft.</th>';
}

if (strpos($value_config, ','."Contact Since".',') !== FALSE) {
echo '<th>Contact Since</th>';
}

if (strpos($value_config, ','."Date of Last Contact".',') !== FALSE) {
echo '<th>Date of Last Contact</th>';
}

if (strpos($value_config, ','."Start Date".',') !== FALSE) {
echo '<th>Start Date</th>';
}

if (strpos($value_config, ','."Expiry Date".',') !== FALSE) {
echo '<th>Expiry Date</th>';
}

if (strpos($value_config, ','."Renewal Date".',') !== FALSE) {
echo '<th>Renewal Date</th>';
}

if (strpos($value_config, ','."Lease Term Date".',') !== FALSE) {
echo '<th>Lease Term Date</th>';
}

if (strpos($value_config, ','."Date Contract Signed".',') !== FALSE) {
echo '<th>Date Contract Signed</th>';
}

if (strpos($value_config, ','."Option to Renew Date".',') !== FALSE) {
echo '<th>Option to Renew Date</th>';
}

if (strpos($value_config, ','."Rate Increase Date".',') !== FALSE) {
echo '<th>Rate Increase Date</th>';
}

if (strpos($value_config, ','."Insurance Expiry Date".',') !== FALSE) {
echo '<th>Insurance Expiry Date</th>';
}

if (strpos($value_config, ','."Account Expiry Date".',') !== FALSE) {
echo '<th>Account Expiry Date</th>';
}

if (strpos($value_config, ','."Hire Date".',') !== FALSE) {
echo '<th>Hire Date</th>';
}

if (strpos($value_config, ','."Probation End Date".',') !== FALSE) {
echo '<th>Probation End Date</th>';
}

if (strpos($value_config, ','."Probation Expiry Reminder Date".',') !== FALSE) {
echo '<th>Probation Expiry Reminder Date</th>';
}

if (strpos($value_config, ','."Birth Date".',') !== FALSE) {
echo '<th>Birth Date</th>';
}

if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
echo '<th>Quote Description</th>';
}

if (strpos($value_config, ','."Description".',') !== FALSE) {
echo '<th>Description</th>';
}

if (strpos($value_config, ','."Property Information".',') !== FALSE) {
echo '<th>Property Information</th>';
}

if (strpos($value_config, ','."General Comments".',') !== FALSE) {
echo '<th>General Comments</th>';
}

if (strpos($value_config, ','."Comments".',') !== FALSE) {
echo '<th>Comments</th>';
}

if (strpos($value_config, ','."Notes".',') !== FALSE) {
echo '<th>Notes</th>';
}

if ( strpos ( $value_config, ',' . "Credit Card on File" . ',' ) !== FALSE ) {
	echo '<th>CC on File</th>';
}

if ( strpos ( $value_config, ',' . "Intake Form" . ',' ) !== FALSE ) {
	echo '<th>Intake Form</th>';
}

if (strpos($value_config, ','."Total Sites".',') !== FALSE) {
	echo '<th>Sites</th>';
}

if (strpos($value_config, ','."Total Customers".',') !== FALSE) {
	echo '<th>Customers</th>';
}

if (strpos($value_config, ','."Status".',') !== FALSE) {
if(isset($_GET['sortby']) && $_GET['sortby'] == 'status')
	echo '<th>Status</th>';
else
	echo '<th><a href='.addOrUpdateUrlParam("sortby","status").'>Status</a></th>';

}
