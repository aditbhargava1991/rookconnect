<script>
function waiting_on_maintenance(sel) {
	var action = sel.value;
	var typeId = sel.id;
	var arr = typeId.split('_');

	$.ajax({
		type: "GET",
		url: "../ajax_all.php?fill=contact_maintenance&contactid="+action,
		dataType: "html",   //expect html to be returned
		success: function(response){
			alert("Patient is now Maintenance Patient.");
			location.reload();
		}
	});
}
</script>
<?php
$contactid = $row['contactid'];

$get_cost = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM contacts_cost WHERE contactid='$contactid'"));

$get_dates = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM contacts_dates WHERE contactid='$contactid'"));

$get_description = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM contacts_description WHERE contactid='$contactid'"));

$intake_results = mysqli_query($dbc, "SELECT `intakeid`, `intake_file` FROM `intake` WHERE `contactid`='$contactid' AND `deleted`=0");
if ( mysqli_num_rows($intake_results) > 0 ) {
	$get_intake = mysqli_fetch_assoc($intake_results);
	$intake_file = $get_intake['intake_file'];
} else {
	$intake_file = '';
}

if (strpos($value_config, ','."Employee ID".',') !== FALSE) {
    echo '<td data-title="Employee ID">'.$contactid.'</a></td>';
}

if (strpos($value_config, ','."Business".',') !== FALSE) {
    if(empty($_GET['customersiteid'])) {
        echo '<td data-title="Business"><a href=\'add_contacts.php?category='.$_GET['category'].'&contactid='.$row['businessid'].'\'>' . get_client($dbc, $row['businessid']) . '</a></td>';
    }
}

if (strpos($value_config, ','."Site Name (Location)".',') !== FALSE) {
    if($row['site_name'] != '') {
        echo '<td data-title="Site Name (Location)">' . $row['site_name'] . '</td>';
    } else {
        echo '<td data-title="Site Name (Location)">' . get_contact($dbc, $row['siteid'], 'site_name') . '</td>';
    }
}

if (strpos($value_config, ','."Name".',') !== FALSE) {
    echo '<td data-title="Business"><a href=\'add_contacts.php?category='.$row['category'].'&contactid='.$row['contactid'].'\'>' . decryptIt($row['name']) . '</a></td>';
}

if (strpos($value_config, ','."Customer(Client/Customer/Business)".',') !== FALSE) {
echo '<td data-title="Customer(Client/Customer/Business)">' . get_client($dbc, $row['siteclientid']) . '</td>';
}

if (strpos($value_config, ','."Display Name".',') !== FALSE) {
echo '<td data-title="Display Name">' . $row['display_name'] . '</td>';
}
if (strpos($value_config, ','."Contact Category".',') !== FALSE) {
echo '<td data-title="Contact Category">' . $row['category_contact'] . '</td>';
}
if (strpos($value_config, ','."First Name".',') !== FALSE) {
echo '<td data-title="First Name">' . decryptIt($row['first_name']) . '</td>';
}

if (strpos($value_config, ','."Last Name".',') !== FALSE) {
echo '<td data-title="Last Name">' .decryptIt($row['last_name']) . '</td>';
}

if (strpos($value_config, ','."Nick Name".',') !== FALSE) {
echo '<td data-title="Nick Name">' .$row['nick_name'] . '</td>';
}
if (strpos($value_config, ','."Assigned Staff".',') !== FALSE) {
echo '<td data-title="Assigned Staff">' .get_contact($dbc,$row['assign_staff']) . '</td>';
}
if (strpos($value_config, ','."Gender".',') !== FALSE) {
echo '<td data-title="gender">' . $row['gender'] . '</td>';
}
if (strpos($value_config, ','."License Number".',') !== FALSE) {
echo '<td data-title="license">' . $row['license'] . '</td>';
}
if (strpos($value_config, ','."Credentials".',') !== FALSE) {
    echo '<td data-title="credential">' . $row['credential'] . '</td>';
}
if (strpos($value_config, ','."Alberta Health Care No".',') !== FALSE) {
    echo '<td data-title="health_care_no">' . decryptIt($row['health_care_no']) . '</td>';
}
if (strpos($value_config, ','."Invoice".',') !== FALSE) {
	echo '<td data-title="Code">-</td>';
}
if (strpos($value_config, ','."MVA".',') !== FALSE) {
    echo '<td data-title="mva"><a href="'.WEBSITE_URL.'/MVA Forms/mva1.php?patientid='.$row['contactid'].'">MVA 1</a><br><a href="'.WEBSITE_URL.'/MVA Forms/mva2.php?patientid='.$row['contactid'].'">MVA 2</a><br><a href="'.WEBSITE_URL.'/MVA Forms/mva3.php?patientid='.$row['contactid'].'">MVA 3</a><br><a href="'.WEBSITE_URL.'/MVA Forms/mva4.php?patientid='.$row['contactid'].'">MVA 4</a></td>';
}
if (strpos($value_config, ','."Maintenance Patient".',') !== FALSE) {
    ?>
	<td data-title="maintenance_patient"><input type="checkbox" <?php if($row['maintenance'] == 1) { echo "checked"; } ?>  onchange="waiting_on_maintenance(this)" value="<?php echo $row['contactid']; ?>"></td>
<?php
}
if (strpos($value_config, ','."Correspondence Language".',') !== FALSE) {
    echo '<td data-title="correspondence_language">' . $row['correspondence_language'] . '</td>';
}
if (strpos($value_config, ','."Amount To Bill".',') !== FALSE) {
    echo '<td data-title="amount_to_bill">' . $row['amount_to_bill'] . '</td>';
}
if (strpos($value_config, ','."Amount Owing".',') !== FALSE) {
    echo '<td data-title="amount_owing">' . $row['amount_owing'] . '</td>';
}
if (strpos($value_config, ','."Amount Credit".',') !== FALSE) {
    echo '<td data-title="amount_credit">' . $row['amount_credit'] . '</td>';
}

if (strpos($value_config, ','."Business Contacts".',') !== FALSE) {
    echo '<td data-title="Business Contacts"><a href=\'contacts.php?category=All&businessid='.$row['contactid'].'\'>View Contacts</a></td>';
}

if (strpos($value_config, ','."Role".',') !== FALSE) {
	$select_value = '';
	$select_value = get_securitylevel($dbc, $row['role']);

echo '<td data-title="Role">' . $select_value . '</td>';
}

if (strpos($value_config, ','."Division".',') !== FALSE) {
echo '<td data-title="Class.">' . $row['classification'] . '</td>';
}

if (strpos($value_config, ','."User Name".',') !== FALSE) {
echo '<td data-title="Username">' . $row['user_name'] . '</td>';
}

if (strpos($value_config, ','."Password".',') !== FALSE) {
echo '<td data-title="Password">' . decryptIt($row['password']) . '</td>';
}

if (strpos($value_config, ','."Name on Account".',') !== FALSE) {
echo '<td data-title="Acct. Name">' . $row['name_on_account'] . '</td>';
}

if (strpos($value_config, ','."Operating As".',') !== FALSE) {
echo '<td data-title="Operating As">' . $row['operating_as'] . '</td>';
}

if (strpos($value_config, ','."Emergency Contact".',') !== FALSE) {
echo '<td data-title="Emergency Contact">' . $row['emergency_contact'] . '</td>';
}

if (strpos($value_config, ','."Occupation".',') !== FALSE) {
echo '<td data-title="Occupation">' . $row['occupation'] . '</td>';
}

if (strpos($value_config, ','."Office Phone".',') !== FALSE) {
    echo '<td data-title="Office Ph.">';
    if (strpos($row['primary_contact'], 'O') !== FALSE) {
        echo '<img src="'.WEBSITE_URL.'/img/filled_star.png" width="15" height="15" border="0" alt="">';
    }
    echo decryptIt($row['office_phone']) . '</td>';
}

if (strpos($value_config, ','."Cell Phone".',') !== FALSE) {
    echo '<td data-title="Cell Ph.">';
    if (strpos($row['primary_contact'], 'C') !== FALSE) {
        echo '<img src="'.WEBSITE_URL.'/img/filled_star.png" width="15" height="15" border="0" alt="">';
    }
    echo decryptIt($row['cell_phone']) . '</td>';
}

if (strpos($value_config, ','."Home Phone".',') !== FALSE) {
    echo '<td data-title="Home Ph.">';
    if (strpos($row['primary_contact'], 'H') !== FALSE) {
        echo '<img src="'.WEBSITE_URL.'/img/filled_star.png" width="15" height="15" border="0" alt="">';
    }
    echo decryptIt($row['home_phone']) . '</td>';
}

if (strpos($value_config, ','."Fax".',') !== FALSE) {
echo '<td data-title="Fax">' . $row['fax'] . '</td>';
}

if (strpos($value_config, ','."Email Address".',') !== FALSE) {
echo '<td data-title="Email">' . decryptIt($row['email_address']) . '</td>';
}

if (strpos($value_config, ','."Account Balance".',') !== FALSE) {
echo '<td data-title="Email">' . $row['account_balance'] . '</td>';
}

if (strpos($value_config, ','."Website".',') !== FALSE) {
echo '<td data-title="Website">' . $row['website'] . '</td>';
}

if (strpos($value_config, ','."Customer Address".',') !== FALSE) {
echo '<td data-title="Cust. Address">' . $row['customer_address'] . '</td>';
}

if (strpos($value_config, ','."Referred By".',') !== FALSE) {
echo '<td data-title="Referred By">' . $row['referred_by'] . '</td>';
}

if (strpos($value_config, ','."Company".',') !== FALSE) {
echo '<td data-title="Company">' . $row['company'] . '</td>';
}

if (strpos($value_config, ','."Position".',') !== FALSE) {
echo '<td data-title="Position">' . $row['position'] . '</td>';
}

if (strpos($value_config, ','."Title".',') !== FALSE) {
echo '<td data-title="Title">' . $row['title'] . '</td>';
}

if (strpos($value_config, ','."LinkedIn".',') !== FALSE) {
echo '<td data-title="LinkedIn">' . $row['linkedin'] . '</td>';
}

if (strpos($value_config, ','."Twitter".',') !== FALSE) {
echo '<td data-title="Twitter">' . $row['twitter'] . '</td>';
}

if (strpos($value_config, ','."DUNS".',') !== FALSE) {
echo '<td data-title="Twitter">' . $row['duns'] . '</td>';
}

if (strpos($value_config, ','."CAGE".',') !== FALSE) {
echo '<td data-title="Twitter">' . $row['cage'] . '</td>';
}

if (strpos($value_config, ','."Self Identification".',') !== FALSE) {
echo '<td data-title="Twitter">' . $row['self_identification'] . '</td>';
}

if (strpos($value_config, ','."Client Tax Exemption".',') !== FALSE) {
echo '<td data-title="Tax Exempt">' . $row['client_tax_exemption'] . '</td>';
}

if (strpos($value_config, ','."Tax Exemption Number".',') !== FALSE) {
echo '<td data-title="Tax Ex. #">' . $row['part_no'] . '</td>';
}

if (strpos($value_config, ','."AISH Card#".',') !== FALSE) {
echo '<td data-title="AISH Card #">' . $row['aish_card_no'] . '</td>';
}

if (strpos($value_config, ','."License Plate #".',') !== FALSE) {
echo '<td data-title="License Plate">' . $row['license_plate_no'] . '</td>';
}

if (strpos($value_config, ','."CARFAX".',') !== FALSE) {
echo '<td data-title="CARFAX">' . $row['carfax'] . '</td>';
}

if (strpos($value_config, ','."Mailing Address".',') !== FALSE) {
echo '<td data-title="Mailing Addr.">' . $row['mailing_address'] . '</td>';
}

if (strpos($value_config, ','."Business Address".',') !== FALSE) {
echo '<td data-title="Business Addr.">' . $row['business_address']. ' '.decryptIt($row['business_street']) . '<br>'.decryptIt($row['business_city']). ', '.decryptIt($row['business_state']).'<br>'.decryptIt($row['business_country']). ', '.decryptIt($row['business_zip']).'</td>';
}

if (strpos($value_config, ','."Ship To Address".',') !== FALSE) {
echo '<td data-title="Shipping Addr.">' . $row['ship_to_address'] . '</td>';
}

if (strpos($value_config, ','."Postal Code".',') !== FALSE) {
echo '<td data-title="Postal Code">' . $row['postal_code'] . '</td>';
}

if (strpos($value_config, ','."Zip Code".',') !== FALSE) {
echo '<td data-title="Zip/Postal Code">' . $row['zip_code'] . '</td>';
}

if (strpos($value_config, ','."City".',') !== FALSE) {
echo '<td data-title="City">' . $row['city'] . '</td>';
}

if (strpos($value_config, ','."Province".',') !== FALSE) {
echo '<td data-title="Province">' . $row['province'] . '</td>';
}

if (strpos($value_config, ','."State".',') !== FALSE) {
echo '<td data-title="State">' . $row['state'] . '</td>';
}

if (strpos($value_config, ','."Country".',') !== FALSE) {
echo '<td data-title="Country">' . $row['country'] . '</td>';
}

if (strpos($value_config, ','."Ship City".',') !== FALSE) {
echo '<td data-title="Ship. City">' . $row['ship_city'] . '</td>';
}
if (strpos($value_config, ','."Ship State".',') !== FALSE) {
echo '<td data-title="Ship. State">' . $row['ship_state'] . '</td>';
}
if (strpos($value_config, ','."Ship Country".',') !== FALSE) {
echo '<td data-title="Ship. Country">' . $row['ship_country'] . '</td>';
}
if (strpos($value_config, ','."Ship Zip".',') !== FALSE) {
echo '<td data-title="Ship Zip">' . $row['ship_zip'] . '</td>';
}

if (strpos($value_config, ','."Google Maps Address".',') !== FALSE) {
echo '<td data-title="Maps URL">' . $row['google_maps_address'] . '</td>';
}

if (strpos($value_config, ','."City Part".',') !== FALSE) {
echo '<td data-title="Quadrant">' . $row['city_part'] . '</td>';
}

if (strpos($value_config, ','."Account Number".',') !== FALSE) {
echo '<td data-title="Acct. #">' . $row['account_number'] . '</td>';
}

if (strpos($value_config, ','."Payment Type".',') !== FALSE) {
echo '<td data-title="Pmt Type">' . $row['payment_type'] . '</td>';
}

if (strpos($value_config, ','."Payment Type".',') !== FALSE) {
echo '<td data-title="Pmt Name">' . $row['payment_name'] . '</td>';
}

if (strpos($value_config, ','."Payment Address".',') !== FALSE) {
echo '<td data-title="Pmt Address">' . $row['payment_address'] . '</td>';
}

if (strpos($value_config, ','."Payment City".',') !== FALSE) {
echo '<td data-title="Pmt City">' . $row['payment_city'] . '</td>';
}

if (strpos($value_config, ','."Payment State".',') !== FALSE) {
echo '<td data-title="Pmt State">' . $row['payment_state'] . '</td>';
}

if (strpos($value_config, ','."Payment Postal Code".',') !== FALSE) {
echo '<td data-title="Pmt Postal.">' . $row['payment_postal_code'] . '</td>';
}

if (strpos($value_config, ','."Payment Zip Code".',') !== FALSE) {
echo '<td data-title="Pmt Zip">' . $row['payment_zip_code'] . '</td>';
}

if (strpos($value_config, ','."GST #".',') !== FALSE) {
echo '<td data-title="GST #">' . $row['gst_no'] . '</td>';
}

if (strpos($value_config, ','."PST #".',') !== FALSE) {
echo '<td data-title="PST #">' . $row['pst_no'] . '</td>';
}

if (strpos($value_config, ','."Vendor GST #".',') !== FALSE) {
echo '<td data-title="Vendor GST #">' . $row['vendor_gst_no'] . '</td>';
}

if (strpos($value_config, ','."Payment Information".',') !== FALSE) {
echo '<td data-title="Pmt Info.">' . $row['payment_information'] . '</td>';
}

if (strpos($value_config, ','."Pricing Level".',') !== FALSE) {
echo '<td data-title="Pricing Lvl">' . $row['pricing_level'] . '</td>';
}

if (strpos($value_config, ','."Unit #".',') !== FALSE) {
echo '<td data-title="Unit #">' . $row['unit_no'] . '</td>';
}

if (strpos($value_config, ','."Bay #".',') !== FALSE) {
echo '<td data-title="Bay #">' . $row['bay_no'] . '</td>';
}

if (strpos($value_config, ','."Option to Renew".',') !== FALSE) {
echo '<td data-title="Option Renew">' . $row['option_to_renew'] . '</td>';
}

if (strpos($value_config, ','."Lease Term - # of years".',') !== FALSE) {
echo '<td data-title="Lease Term(yrs)">' . $row['lease_term_no_of_years'] . '</td>';
}

if (strpos($value_config, ','."Commercial Insurer".',') !== FALSE) {
echo '<td data-title="Comm. Insurer">' . $row['commercial_insurer'] . '</td>';
}

if (strpos($value_config, ','."Residential Insurer".',') !== FALSE) {
echo '<td data-title="Res. Insurer">' . $row['residential_insurer'] . '</td>';
}

if (strpos($value_config, ','."WCB #".',') !== FALSE) {
echo '<td data-title="WCB #">' . $row['wcb_no'] . '</td>';
}

if (strpos($value_config, ','."Total Monthly Rate".',') !== FALSE) {
echo '<td data-title="Monthly Rate">' . $get_cost['total_monthly_rate'] . '</td>';
}

if (strpos($value_config, ','."Total Annual Rate".',') !== FALSE) {
echo '<td data-title="Annual Rate">' . $get_cost['total_annual_rate'] . '</td>';
}

if (strpos($value_config, ','."Condo Fees".',') !== FALSE) {
echo '<td data-title="Condo Fees">' . $get_cost['condo_fees'] . '</td>';
}

if (strpos($value_config, ','."Deposit".',') !== FALSE) {
echo '<td data-title="Deposit">' . $get_cost['deposit'] . '</td>';
}

if (strpos($value_config, ','."Damage Deposit".',') !== FALSE) {
echo '<td data-title="Dmg. Deposit">' . $get_cost['damage_deposit'] . '</td>';
}

if (strpos($value_config, ','."Cost".',') !== FALSE) {
echo '<td data-title="Cost">' . $get_cost['cost'] . '</td>';
}

if (strpos($value_config, ','."Final Retail Price".',') !== FALSE) {
echo '<td data-title="Retail Price">' . $get_cost['final_retail_price'] . '</td>';
}

if (strpos($value_config, ','."Admin Price".',') !== FALSE) {
echo '<td data-title="Admin Price">' . $get_cost['admin_price'] . '</td>';
}

if (strpos($value_config, ','."Wholesale Price".',') !== FALSE) {
echo '<td data-title="Wholesale">' . $get_cost['wholesale_price'] . '</td>';
}

if (strpos($value_config, ','."Commercial Price".',') !== FALSE) {
echo '<td data-title="Comm. Price">' . $get_cost['commercial_price'] . '</td>';
}

if (strpos($value_config, ','."Client Price".',') !== FALSE) {
echo '<td data-title="Client Price">' . $get_cost['client_price'] . '</td>';
}

if (strpos($value_config, ','."Minimum Billable".',') !== FALSE) {
echo '<td data-title="Min. Billable">' . $get_cost['minimum_billable'] . '</td>';
}

if (strpos($value_config, ','."Estimated Hours".',') !== FALSE) {
echo '<td data-title="Est. Hours">' . $get_cost['estimated_hours'] . '</td>';
}

if (strpos($value_config, ','."Actual Hours".',') !== FALSE) {
echo '<td data-title="Actual Hours">' . $get_cost['actual_hours'] . '</td>';
}

if (strpos($value_config, ','."MSRP".',') !== FALSE) {
echo '<td data-title="MSRP">' . $get_cost['msrp'] . '</td>';
}

if (strpos($value_config, ','."Hourly Rate".',') !== FALSE) {
echo '<td data-title="Hr. Rate">' . $get_cost['hourly_rate'] . '</td>';
}

if (strpos($value_config, ','."Monthly Rate".',') !== FALSE) {
echo '<td data-title="Mth. Rate">' . $get_cost['monthly_rate'] . '</td>';
}

if (strpos($value_config, ','."Semi Monthly Rate".',') !== FALSE) {
echo '<td data-title="Semi-Monthly">' . $get_cost['semi_monthly_rate'] . '</td>';
}

if (strpos($value_config, ','."Daily Rate".',') !== FALSE) {
echo '<td data-title="Daily Rate">' . $get_cost['daily_rate'] . '</td>';
}

if (strpos($value_config, ','."HR Rate Work".',') !== FALSE) {
echo '<td data-title="HR Work Rate">' . $get_cost['hr_rate_work'] . '</td>';
}

if (strpos($value_config, ','."HR Rate Travel".',') !== FALSE) {
echo '<td data-title="HR Travel Rate">' . $get_cost['hr_rate_travel'] . '</td>';
}

if (strpos($value_config, ','."Field Day Cost".',') !== FALSE) {
echo '<td data-title="Field Day Cost">' . $get_cost['field_day_cost'] . '</td>';
}

if (strpos($value_config, ','."Field Day Billable".',') !== FALSE) {
echo '<td data-title="Field Billable">' . $get_cost['field_day_billable'] . '</td>';
}

if (strpos($value_config, ','."Probation Pay Rate".',') !== FALSE) {
echo '<td data-title="Probation Rate">' . $get_cost['probation_pay_rate'] . '</td>';
}

if (strpos($value_config, ','."Base Pay".',') !== FALSE) {
echo '<td data-title="Base Pay">' . $get_cost['base_pay'] . '</td>';
}

if (strpos($value_config, ','."Performance Pay".',') !== FALSE) {
echo '<td data-title="Perf. Pay">' . $get_cost['performance_pay'] . '</td>';
}

if (strpos($value_config, ','."Unit #".',') !== FALSE) {
echo '<td data-title="Unit #">' . $get_cost['unit_no'] . '</td>';
}

if (strpos($value_config, ','."Base Rent".',') !== FALSE) {
echo '<td data-title="Base Rent">' . $get_cost['base_rent'] . '</td>';
}

if (strpos($value_config, ','."Base Rent/Sq. Ft.".',') !== FALSE) {
echo '<td data-title="Base/Sq. Ft.">' . $get_cost['base_rent_sq_ft'] . '</td>';
}

if (strpos($value_config, ','."CAC".',') !== FALSE) {
echo '<td data-title="CAC">' . $get_cost['cac'] . '</td>';
}

if (strpos($value_config, ','."CAC/Sq. Ft.".',') !== FALSE) {
echo '<td data-title="CAC/Sq. Ft.">' . $get_cost['cac_sq_ft'] . '</td>';
}

if (strpos($value_config, ','."Property Tax".',') !== FALSE) {
echo '<td data-title=Property Tax">' . $get_cost['property_tax'] . '</td>';
}

if (strpos($value_config, ','."Property Tax/Sq. Ft.".',') !== FALSE) {
echo '<td data-title="Prop. Tax/Sq. Ft.">' . $get_cost['property_tax_sq_ft'] . '</td>';
}

if (strpos($value_config, ','."Contact Since".',') !== FALSE) {
echo '<td data-title="Contact Since">' . $get_dates['contact_since'] . '</td>';
}

if (strpos($value_config, ','."Date of Last Contact".',') !== FALSE) {
echo '<td data-title="Last Contact">' . $get_dates['date_of_last_contact'] . '</td>';
}

if (strpos($value_config, ','."Start Date".',') !== FALSE) {
echo '<td data-title="Start Date">' . $get_dates['start_date'] . '</td>';
}

if (strpos($value_config, ','."Expiry Date".',') !== FALSE) {
echo '<td data-title="Expiry">' . $get_dates['expiry_date'] . '</td>';
}

if (strpos($value_config, ','."Renewal Date".',') !== FALSE) {
echo '<td data-title="Renewal">' . $get_dates['renewal_date'] . '</td>';
}

if (strpos($value_config, ','."Lease Term Date".',') !== FALSE) {
echo '<td data-title="Term Date">' . $get_dates['lease_term_date'] . '</td>';
}

if (strpos($value_config, ','."Date Contract Signed".',') !== FALSE) {
echo '<td data-title="Contract Date">' . $get_dates['date_contract_signed'] . '</td>';
}

if (strpos($value_config, ','."Option to Renew Date".',') !== FALSE) {
echo '<td data-title="Option Renew Date">' . $get_dates['option_to_renew_date'] . '</td>';
}

if (strpos($value_config, ','."Rate Increase Date".',') !== FALSE) {
echo '<td data-title="Rate Inc. Date">' . $get_dates['rate_increase_date'] . '</td>';
}

if (strpos($value_config, ','."Insurance Expiry Date".',') !== FALSE) {
echo '<td data-title="Insurance Exp.">' . $get_dates['insurance_expiry_date'] . '</td>';
}

if (strpos($value_config, ','."Account Expiry Date".',') !== FALSE) {
echo '<td data-title="Acct. Expiry">' . $get_dates['account_expiry_date'] . '</td>';
}

if (strpos($value_config, ','."Hire Date".',') !== FALSE) {
echo '<td data-title="Hire Date">' . $get_dates['hire_date'] . '</td>';
}

if (strpos($value_config, ','."Probation End Date".',') !== FALSE) {
echo '<td data-title="Probation End">' . $get_dates['probation_end_date'] . '</td>';
}

if (strpos($value_config, ','."Probation Expiry Reminder Date".',') !== FALSE) {
echo '<td data-title="Prob. Reminder">' . $get_dates['probation_expiry_reminder_date'] . '</td>';
}

if (strpos($value_config, ','."Birth Date".',') !== FALSE) {
echo '<td data-title="Date of Birth">' . $get_dates['birth_date'] . '</td>';
}

if (strpos($value_config, ','."Quote Description".',') !== FALSE) {
echo '<td data-title="Quote Desc.">' . $get_description['quote_description'] . '</td>';
}

if (strpos($value_config, ','."Description".',') !== FALSE) {
echo '<td data-title="Description">' . $get_description['description'] . '</td>';
}

if (strpos($value_config, ','."Property Information".',') !== FALSE) {
echo '<td data-title="Prop. Info">' . $get_description['property_information'] . '</td>';
}

if (strpos($value_config, ','."General Comments".',') !== FALSE) {
echo '<td data-title="Gen. Comments">' . $get_description['general_comments'] . '</td>';
}

if (strpos($value_config, ','."Comments".',') !== FALSE) {
echo '<td data-title="Comments">' . $get_description['comments'] . '</td>';
}

if (strpos($value_config, ','."Notes".',') !== FALSE) {
echo '<td data-title="Notes">' . $get_description['notes'] . '</td>';
}

if ( strpos ( $value_config, ',' . "Credit Card on File" . ',' ) !== FALSE ) {
	$cconfile = ( $row['cc_on_file'] == '1' ) ? 'Yes' : 'No';
	echo '<td data-title="CC on File">' . $cconfile . '</td>';
}

if ( strpos ( $value_config, ',' . "Intake Form" . ',' ) !== FALSE ) {
	if ( $intake_file != '' ) {
		echo '<td data-title="Intake Form"><img src="' . WEBSITE_URL . '/img/pdf.png" width="16" height="16" border="0" alt="View"> <a href="' . WEBSITE_URL . '/Intake/' . $get_intake['intake_file'] . '" target="_blank">View</a></td>';
	} else {
		echo '<td data-title="Intake Form">-</td>';
	}
}

if (strpos($value_config, ','."Total Sites".',') !== FALSE) {
    $get_desc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(contactid) AS contactid FROM contacts WHERE businessid='$contactid' AND category='Sites' AND deleted=0"));
    if($get_desc['contactid'] > 0) {
	    echo '<td data-title="Total Sites"><a href="contacts.php?category=Sites&sitebusinessid='.$contactid.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">View Sites ('.$get_desc['contactid'].')</a></td>';
    } else {
        echo '<td data-title="Total Customers">-</td>';
    }
}

if (strpos($value_config, ','."Total Customers".',') !== FALSE) {
    if($_GET['category'] == 'Business') {
        $get_desc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(contactid) AS contactid FROM contacts WHERE businessid='$contactid' AND category='Customer' AND deleted=0"));
        $pass = 'sitebusinessid';
    } else {
        $get_desc = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(contactid) AS contactid FROM contacts WHERE siteid='$contactid' AND deleted=0"));
        $pass = 'customersiteid';
    }
    if($get_desc['contactid'] > 0) {
	    echo '<td data-title="Total Customers"><a href="contacts.php?category=Customer&'.$pass.'='.$contactid.'&from_url='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">View Customers ('.$get_desc['contactid'].')</a></td>';
    } else {
        echo '<td data-title="Total Customers">-</td>';
    }
}

if (strpos($value_config, ','."Status".',') !== FALSE) {
	$categorystatus=$_GET['category'];
	$filterstatus=$_GET['filter'];
	if($row['status'] != 0)
	{
		echo '<td data-title="Status">Active | <a href="contacts.php?filter='.$filterstatus.'&category='.$categorystatus.'&contactid='.$contactid.'&status=0" onclick="return confirm(\'Are you sure Deactivate?\')">Deactivate</a></td>';
	}
	else{
		echo '<td data-title="Status">Deactivated | <a href="contacts.php?filter='.$filterstatus.'&category='.$categorystatus.'&contactid='.$contactid.'&status=1" onclick="return confirm(\'Are you sure Activate?\')">Activate</a></td>';
	}
}
