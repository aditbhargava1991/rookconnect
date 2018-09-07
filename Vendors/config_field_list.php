<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Contact Descriptions."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_1" >
				Contact Description<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>
	<div id="collapse_1" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Employee ID,') !== false) { echo " checked"; } ?> value="Employee ID" name="vendors[]">&nbsp;&nbsp;Employee ID</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Business,') !== false) { echo " checked"; } ?> value="Business" name="vendors[]">&nbsp;&nbsp;Business</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Contact Prefix,') !== false) { echo " checked"; } ?> value="Contact Prefix" name="vendors[]">&nbsp;&nbsp;Prefix</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Name,') !== false) { echo " checked"; } ?> value="Name" name="vendors[]">&nbsp;&nbsp;Name</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',First Name,') !== false) { echo " checked"; } ?> value="First Name" name="vendors[]">&nbsp;&nbsp;First Name</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Last Name,') !== false) { echo " checked"; } ?> value="Last Name" name="vendors[]">&nbsp;&nbsp;Last Name</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Preferred Name,') !== false) { echo " checked"; } ?> value="Preferred Name" name="vendors[]">&nbsp;&nbsp;Preferred Name</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Preferred Pronoun,') !== false) { echo " checked"; } ?> value="Preferred Pronoun" name="vendors[]">&nbsp;&nbsp;Preferred Pronoun</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Role,') !== false) { echo " checked"; } ?> value="Role" name="vendors[]">&nbsp;&nbsp;Security Level</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Region,') !== false) { echo " checked"; } ?> value="Region" name="vendors[]">&nbsp;&nbsp;Region</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Classification,') !== false) { echo " checked"; } ?> value="Classification" name="vendors[]">&nbsp;&nbsp;Classification</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Division,') !== false) { echo " checked"; } ?> value="Division" name="vendors[]">&nbsp;&nbsp;Division</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Name on Account,') !== false) { echo " checked"; } ?> value="Name on Account" name="vendors[]">&nbsp;&nbsp;Name on Account</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Operating As,') !== false) { echo " checked"; } ?> value="Operating As" name="vendors[]">&nbsp;&nbsp;Operating As</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Emergency Contact,') !== false) { echo " checked"; } ?> value="Emergency Contact" name="vendors[]">&nbsp;&nbsp;Emergency Contact</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Occupation,') !== false) { echo " checked"; } ?> value="Occupation" name="vendors[]">&nbsp;&nbsp;Occupation</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Office Phone,') !== false) { echo " checked"; } ?> value="Office Phone" name="vendors[]">&nbsp;&nbsp;Office Phone</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Cell Phone,') !== false) { echo " checked"; } ?> value="Cell Phone" name="vendors[]">&nbsp;&nbsp;Cell Phone</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Home Phone,') !== false) { echo " checked"; } ?> value="Home Phone" name="vendors[]">&nbsp;&nbsp;Home Phone</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Fax,') !== false) { echo " checked"; } ?> value="Fax" name="vendors[]">&nbsp;&nbsp;Fax</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Email Address,') !== false) { echo " checked"; } ?> value="Email Address" name="vendors[]">&nbsp;&nbsp;Email Address</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Company Email Address,') !== false) { echo " checked"; } ?> value="Company Email Address" name="vendors[]">&nbsp;&nbsp;Company Email Address</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Website,') !== false) { echo " checked"; } ?> value="Website" name="vendors[]">&nbsp;&nbsp;Website</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Customer Address,') !== false) { echo " checked"; } ?> value="Customer Address" name="vendors[]">&nbsp;&nbsp;Customer Address</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Application,') !== false) { echo " checked"; } ?> value="Upload Application" name="vendors[]">&nbsp;&nbsp;Upload Application</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Contact Image,') !== false) { echo " checked"; } ?> value="Contact Image" name="vendors[]">&nbsp;&nbsp;Contact Image</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Description,') !== false) { echo " checked"; } ?> value="Description" name="vendors[]">&nbsp;&nbsp;Description</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Contact Since,') !== false) { echo " checked"; } ?> value="Contact Since" name="vendors[]">&nbsp;&nbsp;Contact Since</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Date of Last Contact,') !== false) { echo " checked"; } ?> value="Date of Last Contact" name="vendors[]">&nbsp;&nbsp;Date of Last Contact</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Referred By,') !== false) { echo " checked"; } ?> value="Referred By" name="vendors[]">&nbsp;&nbsp;Referred By</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Company,') !== false) { echo " checked"; } ?> value="Company" name="vendors[]">&nbsp;&nbsp;Company</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Position,') !== false) { echo " checked"; } ?> value="Position" name="vendors[]">&nbsp;&nbsp;Position</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Title,') !== false) { echo " checked"; } ?> value="Title" name="vendors[]">&nbsp;&nbsp;Title</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',License #,') !== false) { echo " checked"; } ?> value="License #" name="vendors[]">&nbsp;&nbsp;Licence #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Credential ,') !== false) { echo " checked"; } ?> value="Credential " name="vendors[]">&nbsp;&nbsp;Credential</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Alberta Health Care No,') !== false) { echo " checked"; } ?> value="Alberta Health Care No" name="vendors[]">&nbsp;&nbsp;Alberta Health Care #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',MVA,') !== false) { echo " checked"; } ?> value="MVA" name="vendors[]">&nbsp;&nbsp;MVA</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Maintenance Patient,') !== false) { echo " checked"; } ?> value="Maintenance Patient" name="vendors[]">&nbsp;&nbsp;Maintenance Patient</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Invoice,') !== false) { echo " checked"; } ?> value="Invoice" name="vendors[]">&nbsp;&nbsp;Invoice</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Tax Exemption,') !== false) { echo " checked"; } ?> value="Client Tax Exemption" name="vendors[]">&nbsp;&nbsp;Client Tax Exemption</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Tax Exemption Number,') !== false) { echo " checked"; } ?> value="Tax Exemption Number" name="vendors[]">&nbsp;&nbsp;Tax Exemption #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',AISH Card#,') !== false) { echo " checked"; } ?> value="AISH Card#" name="vendors[]">&nbsp;&nbsp;AISH Card#</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',BIO,') !== false) { echo " checked"; } ?> value="BIO" name="vendors[]">&nbsp;&nbsp;BIO</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',DUNS,') !== false) { echo " checked"; } ?> value="DUNS" name="vendors[]">&nbsp;&nbsp;DUNS</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',CAGE,') !== false) { echo " checked"; } ?> value="CAGE" name="vendors[]">&nbsp;&nbsp;CAGE</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Self Identification,') !== false) { echo " checked"; } ?> value="Self Identification" name="vendors[]">&nbsp;&nbsp;Self Identification</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',SIN,') !== false) { echo " checked"; } ?> value="SIN" name="vendors[]">&nbsp;&nbsp;SIN</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Employee Number,') !== false) { echo " checked"; } ?> value="Employee Number" name="vendors[]">&nbsp;&nbsp;Employee #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Credit Card on File,') !== false) { echo " checked"; } ?> value="Credit Card on File" name="vendors[]">&nbsp;&nbsp;Credit Card on File</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Intake Form,') !== false) { echo " checked"; } ?> value="Intake Form" name="vendors[]">&nbsp;&nbsp;Intake Form</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Show/Hide User,') !== false) { echo " checked"; } ?> value="Show/Hide User" name="vendors[]">&nbsp;&nbsp;Show/Hide User</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Nick Name,') !== false) { echo " checked"; } ?> value="Nick Name" name="vendors[]">&nbsp;&nbsp;Nickname</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Profile Link,') !== false) { echo " checked"; } ?> value="Profile Link" name="vendors[]">&nbsp;&nbsp;Profile Link</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Assigned Staff,') !== false) { echo " checked"; } ?> value="Assigned Staff" name="vendors[]">&nbsp;&nbsp;Assigned Staff</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Contact Category,') !== false) { echo " checked"; } ?> value="Contact Category" name="vendors[]">&nbsp;&nbsp;Contact Category</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Staff Category,') !== false) { echo " checked"; } ?> value="Staff Category" name="vendors[]">&nbsp;&nbsp;Staff Category</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Gender,') !== false) { echo " checked"; } ?> value="Gender" name="vendors[]">&nbsp;&nbsp;Gender</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',License,') !== false) { echo " checked"; } ?> value="License" name="vendors[]">&nbsp;&nbsp;License</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Insurer,') !== false) { echo " checked"; } ?> value="Insurer" name="vendors[]">&nbsp;&nbsp;Insurer</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Scheduled Days/Hours,') !== false) { echo " checked"; } ?> value="Scheduled Days/Hours" name="vendors[]">&nbsp;&nbsp;Scheduled Days/Hours</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Correspondence Language,') !== false) { echo " checked"; } ?> value="Correspondence Language" name="vendors[]">&nbsp;&nbsp;Correspondence Language</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Accepts to receive emails,') !== false) { echo " checked"; } ?> value="Accepts to receive emails" name="vendors[]">&nbsp;&nbsp;Agrees To Receive Emails</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Amount To Bill,') !== false) { echo " checked"; } ?> value="Amount To Bill" name="vendors[]">&nbsp;&nbsp;Amount To Bill</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Amount Owing,') !== false) { echo " checked"; } ?> value="Amount Owing" name="vendors[]">&nbsp;&nbsp;Amount Owing</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Amount Credit,') !== false) { echo " checked"; } ?> value="Amount Credit" name="vendors[]">&nbsp;&nbsp;Amount To Credit</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Accounts Receivable Data,') !== false) { echo " checked"; } ?> value="Accounts Receivable Data" name="vendors[]">&nbsp;&nbsp;Accounts Receivable Data</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Account History Data,') !== false) { echo " checked"; } ?> value="Account History Data" name="vendors[]">&nbsp;&nbsp;Account History Data</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Patient Block Booking,') !== false) { echo " checked"; } ?> value="Patient Block Booking" name="vendors[]">&nbsp;&nbsp;Patient Block Booking</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Extended Health Benefits,') !== false) { echo " checked"; } ?> value="Extended Health Benefits" name="vendors[]">&nbsp;&nbsp;Extended Health Benefits</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Date of Birth,') !== false) { echo " checked"; } ?> value="Date of Birth" name="vendors[]">&nbsp;&nbsp;Date of Birth</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',School,') !== false) { echo " checked"; } ?> value="School" name="vendors[]">&nbsp;&nbsp;School</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',FSCD Number,') !== false) { echo " checked"; } ?> value="FSCD Number" name="vendors[]">&nbsp;&nbsp;FSCD Number</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Hear About,') !== false) { echo " checked"; } ?> value="Hear About" name="vendors[]">&nbsp;&nbsp;How did you hear about us?</div>

			<br clear="all"><br>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Rating,') !== false) { echo " checked"; } ?> value="Rating" name="vendors[]">&nbsp;&nbsp;Rating</div>

			<br clear="all"><br>

			<div class="col-sm-3">Rating Colors</div><br>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Bronze Rating,') !== false) { echo " checked"; } ?> value="Bronze Rating" name="vendors[]">&nbsp;&nbsp;Bronze</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Silver Rating,') !== false) { echo " checked"; } ?> value="Silver Rating" name="vendors[]">&nbsp;&nbsp;Silver</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Gold Rating,') !== false) { echo " checked"; } ?> value="Gold Rating" name="vendors[]">&nbsp;&nbsp;Gold</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Platinum Rating,') !== false) { echo " checked"; } ?> value="Platinum Rating" name="vendors[]">&nbsp;&nbsp;Platinum</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Diamond Rating,') !== false) { echo " checked"; } ?> value="Diamond Rating" name="vendors[]">&nbsp;&nbsp;Diamond</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Green Rating,') !== false) { echo " checked"; } ?> value="Green Rating" name="vendors[]">&nbsp;&nbsp;Green</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Yellow Rating,') !== false) { echo " checked"; } ?> value="Yellow Rating" name="vendors[]">&nbsp;&nbsp;Yellow</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Light blue Rating,') !== false) { echo " checked"; } ?> value="Light blue Rating" name="vendors[]">&nbsp;&nbsp;Light blue</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Dark blue Rating,') !== false) { echo " checked"; } ?> value="Dark blue Rating" name="vendors[]">&nbsp;&nbsp;Dark blue</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Red Rating,') !== false) { echo " checked"; } ?> value="Red Rating" name="vendors[]">&nbsp;&nbsp;Red</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Pink Rating,') !== false) { echo " checked"; } ?> value="Pink Rating" name="vendors[]">&nbsp;&nbsp;Pink</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Purple Rating,') !== false) { echo " checked"; } ?> value="Purple Rating" name="vendors[]">&nbsp;&nbsp;Purple</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field"	value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Site Information."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_site" >
				Site Information<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>
	<div id="collapse_site" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Customer(Client/Customer/Business),') !== false) { echo " checked"; } ?> value="Customer(Client/Customer/Business)" name="vendors[]">&nbsp;&nbsp;Customer(Client/Customer/Business)</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Site Name (Location),') !== false) { echo " checked"; } ?> value="Site Name (Location)" name="vendors[]">&nbsp;&nbsp;Site Name (Location)</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Display Name,') !== false) { echo " checked"; } ?> value="Display Name" name="vendors[]">&nbsp;&nbsp;Display Name</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Business Sites,') !== false) { echo " checked"; } ?> value="Business Sites" name="vendors[]">&nbsp;&nbsp;Business Sites</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Site LSD,') !== false) { echo " checked"; } ?> value="Site LSD" name="vendors[]">&nbsp;&nbsp;Site LSD</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field"	value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>

    	</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Vehicle Descriptions."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_2" >
			   Vehicle Description<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_2" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',License Plate #,') !== false) { echo " checked"; } ?> value="License Plate #" name="vendors[]">&nbsp;&nbsp;License Plate #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload License Plate,') !== false) { echo " checked"; } ?> value="Upload License Plate" name="vendors[]">&nbsp;&nbsp;Upload License Plate</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',CARFAX,') !== false) { echo " checked"; } ?> value="CARFAX" name="vendors[]">&nbsp;&nbsp;CARFAX</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field"	value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Location Descriptions."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_location">
				Location<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_location" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Address,') !== false) { echo " checked"; } ?> value="Address" name="vendors[]">&nbsp;&nbsp;Address</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Mailing Address,') !== false) { echo " checked"; } ?> value="Mailing Address" name="vendors[]">&nbsp;&nbsp;Mailing Address</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Business Address,') !== false) { echo " checked"; } ?> value="Business Address" name="vendors[]">&nbsp;&nbsp;Business Address</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Ship To Address,') !== false) { echo " checked"; } ?> value="Ship To Address" name="vendors[]">&nbsp;&nbsp;Ship To Address</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Postal Code,') !== false) { echo " checked"; } ?> value="Postal Code" name="vendors[]">&nbsp;&nbsp;Postal Code</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Zip Code,') !== false) { echo " checked"; } ?> value="Zip Code" name="vendors[]">&nbsp;&nbsp;Zip/Postal Code</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',City,') !== false) { echo " checked"; } ?> value="City" name="vendors[]">&nbsp;&nbsp;City</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Province,') !== false) { echo " checked"; } ?> value="Province" name="vendors[]">&nbsp;&nbsp;Province</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',State,') !== false) { echo " checked"; } ?> value="State" name="vendors[]">&nbsp;&nbsp;State</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Country,') !== false) { echo " checked"; } ?> value="Country" name="vendors[]">&nbsp;&nbsp;Country</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Ship Country,') !== false) { echo " checked"; } ?> value="Ship Country" name="vendors[]">&nbsp;&nbsp;Ship Country</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Ship City,') !== false) { echo " checked"; } ?> value="Ship City" name="vendors[]">&nbsp;&nbsp;Ship City</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Ship State,') !== false) { echo " checked"; } ?> value="Ship State" name="vendors[]">&nbsp;&nbsp;Ship State</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Ship Zip,') !== false) { echo " checked"; } ?> value="Ship Zip" name="vendors[]">&nbsp;&nbsp;Ship Zip</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Google Maps Address,') !== false) { echo " checked"; } ?> value="Google Maps Address" name="vendors[]">&nbsp;&nbsp;Google Maps Address</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',City Part,') !== false) { echo " checked"; } ?> value="City Part" name="vendors[]">&nbsp;&nbsp;City Part</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Payment Descriptions."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_4" >
				Payment Description<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_4" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Account Number,') !== false) { echo " checked"; } ?> value="Account Number" name="vendors[]">&nbsp;&nbsp;Account Number</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Payment Type,') !== false) { echo " checked"; } ?> value="Payment Type" name="vendors[]">&nbsp;&nbsp;Payment Type</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Payment Name,') !== false) { echo " checked"; } ?> value="Payment Name" name="vendors[]">&nbsp;&nbsp;Payment Name</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Payment Address,') !== false) { echo " checked"; } ?> value="Payment Address" name="vendors[]">&nbsp;&nbsp;Payment Address</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Payment City,') !== false) { echo " checked"; } ?> value="Payment City" name="vendors[]">&nbsp;&nbsp;Payment City</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Payment State,') !== false) { echo " checked"; } ?> value="Payment State" name="vendors[]">&nbsp;&nbsp;Payment State</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Payment Postal Code,') !== false) { echo " checked"; } ?> value="Payment Postal Code" name="vendors[]">&nbsp;&nbsp;Payment Postal Code</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Payment Zip Code,') !== false) { echo " checked"; } ?> value="Payment Zip Code" name="vendors[]">&nbsp;&nbsp;Payment Zip/Postal Code</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',GST #,') !== false) { echo " checked"; } ?> value="GST #" name="vendors[]">&nbsp;&nbsp;GST #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',PST #,') !== false) { echo " checked"; } ?> value="PST #" name="vendors[]">&nbsp;&nbsp;PST #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Vendor GST #,') !== false) { echo " checked"; } ?> value="Vendor GST #" name="vendors[]">&nbsp;&nbsp;<?= VENDOR_TILE ?> GST #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Payment Information,') !== false) { echo " checked"; } ?> value="Payment Information" name="vendors[]">&nbsp;&nbsp;Payment Information</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Account Number,') !== false) { echo " checked"; } ?> value="Account Number" name="vendors[]">&nbsp;&nbsp;Account Number</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Total Monthly Rate,') !== false) { echo " checked"; } ?> value="Total Monthly Rate" name="vendors[]">&nbsp;&nbsp;Total Monthly Rate</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Total Annual Rate,') !== false) { echo " checked"; } ?> value="Total Annual Rate" name="vendors[]">&nbsp;&nbsp;Total Annual Rate</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Condo Fees,') !== false) { echo " checked"; } ?> value="Condo Fees" name="vendors[]">&nbsp;&nbsp;Condo Fees</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Deposit,') !== false) { echo " checked"; } ?> value="Deposit" name="vendors[]">&nbsp;&nbsp;Deposit</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Damage Deposit,') !== false) { echo " checked"; } ?> value="Damage Deposit" name="vendors[]">&nbsp;&nbsp;Damage Deposit</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Quote Description,') !== false) { echo " checked"; } ?> value="Quote Description" name="vendors[]">&nbsp;&nbsp;Quote Description</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Pricing Level,') !== false) { echo " checked"; } ?> value="Pricing Level" name="vendors[]">&nbsp;&nbsp;Pricing Level</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Cost,') !== false) { echo " checked"; } ?> value="Cost" name="vendors[]">&nbsp;&nbsp;Cost</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Final Retail Price,') !== false) { echo " checked"; } ?> value="Final Retail Price" name="vendors[]">&nbsp;&nbsp;Final Retail Price</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Admin Price,') !== false) { echo " checked"; } ?> value="Admin Price" name="vendors[]">&nbsp;&nbsp;Admin Price</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Wholesale Price,') !== false) { echo " checked"; } ?> value="Wholesale Price" name="vendors[]">&nbsp;&nbsp;Wholesale Price</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Commercial Price,') !== false) { echo " checked"; } ?> value="Commercial Price" name="vendors[]">&nbsp;&nbsp;Commercial Price</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Price,') !== false) { echo " checked"; } ?> value="Client Price" name="vendors[]">&nbsp;&nbsp;Client Price</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Minimum Billable,') !== false) { echo " checked"; } ?> value="Minimum Billable" name="vendors[]">&nbsp;&nbsp;Minimum Billable</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Estimated Hours,') !== false) { echo " checked"; } ?> value="Estimated Hours" name="vendors[]">&nbsp;&nbsp;Estimated Hours</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Actual Hours,') !== false) { echo " checked"; } ?> value="Actual Hours" name="vendors[]">&nbsp;&nbsp;Actual Hours</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',MSRP,') !== false) { echo " checked"; } ?> value="MSRP" name="vendors[]">&nbsp;&nbsp;MSRP</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Hourly Rate,') !== false) { echo " checked"; } ?> value="Hourly Rate" name="vendors[]">&nbsp;&nbsp;Hourly Rate</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Monthly Rate,') !== false) { echo " checked"; } ?> value="Monthly Rate" name="vendors[]">&nbsp;&nbsp;Monthly Rate</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Semi Monthly Rate,') !== false) { echo " checked"; } ?> value="Semi Monthly Rate" name="vendors[]">&nbsp;&nbsp;Semi Monthly Rate</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Daily Rate,') !== false) { echo " checked"; } ?> value="Daily Rate" name="vendors[]">&nbsp;&nbsp;Daily Rate</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',HR Rate Work,') !== false) { echo " checked"; } ?> value="HR Rate Work" name="vendors[]">&nbsp;&nbsp;HR Rate Work</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',HR Rate Travel,') !== false) { echo " checked"; } ?> value="HR Rate Travel" name="vendors[]">&nbsp;&nbsp;HR Rate Travel</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Field Day Cost,') !== false) { echo " checked"; } ?> value="Field Day Cost" name="vendors[]">&nbsp;&nbsp;Field Day Cost</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Field Day Billable,') !== false) { echo " checked"; } ?> value="Field Day Billable" name="vendors[]">&nbsp;&nbsp;Field Day Billable</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Probation Pay Rate,') !== false) { echo " checked"; } ?> value="Probation Pay Rate" name="vendors[]">&nbsp;&nbsp;Probation Pay Rate</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Base Pay,') !== false) { echo " checked"; } ?> value="Base Pay" name="vendors[]">&nbsp;&nbsp;Base Pay</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Performance Pay,') !== false) { echo " checked"; } ?> value="Performance Pay" name="vendors[]">&nbsp;&nbsp;Performance Pay</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Property Descriptions."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_5" >
				Property Description<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_5" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Property Information,') !== false) { echo " checked"; } ?> value="Property Information" name="vendors[]">&nbsp;&nbsp;Property Information</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Property Information,') !== false) { echo " checked"; } ?> value="Upload Property Information" name="vendors[]">&nbsp;&nbsp;Upload Property Information</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Unit #,') !== false) { echo " checked"; } ?> value="Unit #" name="vendors[]">&nbsp;&nbsp;Unit #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Condo Fees,') !== false) { echo " checked"; } ?> value="Condo Fees" name="vendors[]">&nbsp;&nbsp;Condo Fees</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Base Rent,') !== false) { echo " checked"; } ?> value="Base Rent" name="vendors[]">&nbsp;&nbsp;Base Rent</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Base Rent/Sq. Ft.,') !== false) { echo " checked"; } ?> value="Base Rent/Sq. Ft." name="vendors[]">&nbsp;&nbsp;Base Rent/Sq. Ft.</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',CAC,') !== false) { echo " checked"; } ?> value="CAC" name="vendors[]">&nbsp;&nbsp;CAC</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',CAC/Sq. Ft.,') !== false) { echo " checked"; } ?> value="CAC/Sq. Ft." name="vendors[]">&nbsp;&nbsp;CAC/Sq. Ft.</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Property Tax,') !== false) { echo " checked"; } ?> value="Property Tax" name="vendors[]">&nbsp;&nbsp;Property Tax</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Property Tax/Sq. Ft.,') !== false) { echo " checked"; } ?> value="Property Tax/Sq. Ft." name="vendors[]">&nbsp;&nbsp;Property Tax/Sq. Ft.</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Inspection,') !== false) { echo " checked"; } ?> value="Upload Inspection" name="vendors[]">&nbsp;&nbsp;Upload Inspection</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Bay #,') !== false) { echo " checked"; } ?> value="Bay #" name="vendors[]">&nbsp;&nbsp;Bay #</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Contract/Form Information."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_6" >
				Contract/Form Info<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_6" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Letter of Intent,') !== false) { echo " checked"; } ?> value="Upload Letter of Intent" name="vendors[]">&nbsp;&nbsp;Upload Letter of Intent</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Vendor Documents,') !== false) { echo " checked"; } ?> value="Upload Vendor Documents" name="vendors[]">&nbsp;&nbsp;Upload <?= VENDOR_TILE ?> Documents</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Marketing Material,') !== false) { echo " checked"; } ?> value="Upload Marketing Material" name="vendors[]">&nbsp;&nbsp;Upload Marketing Material</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Purchase Contract,') !== false) { echo " checked"; } ?> value="Upload Purchase Contract" name="vendors[]">&nbsp;&nbsp;Upload Purchase Contract</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Support Contract,') !== false) { echo " checked"; } ?> value="Upload Support Contract" name="vendors[]">&nbsp;&nbsp;Upload Support Contract</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Support Terms,') !== false) { echo " checked"; } ?> value="Upload Support Terms" name="vendors[]">&nbsp;&nbsp;Upload Support Terms</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Rental Contract,') !== false) { echo " checked"; } ?> value="Upload Rental Contract" name="vendors[]">&nbsp;&nbsp;Upload Rental Contract</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Management Contract,') !== false) { echo " checked"; } ?> value="Upload Management Contract" name="vendors[]">&nbsp;&nbsp;Upload Management Contract</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Articles of Incorporation,') !== false) { echo " checked"; } ?> value="Upload Articles of Incorporation" name="vendors[]">&nbsp;&nbsp;Upload Articles of Incorporation</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Option to Renew,') !== false) { echo " checked"; } ?> value="Option to Renew" name="vendors[]">&nbsp;&nbsp;Option to Renew</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Dates."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_7" >
				Dates<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_7" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Start Date,') !== false) { echo " checked"; } ?> value="Start Date" name="vendors[]">&nbsp;&nbsp;Start Date</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Expiry Date,') !== false) { echo " checked"; } ?> value="Expiry Date" name="vendors[]">&nbsp;&nbsp;Expiry Date</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Renewal Date,') !== false) { echo " checked"; } ?> value="Renewal Date" name="vendors[]">&nbsp;&nbsp;Renewal Date</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Lease Term Date,') !== false) { echo " checked"; } ?> value="Lease Term Date" name="vendors[]">&nbsp;&nbsp;Lease Term Date</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Lease Term - # of years,') !== false) { echo " checked"; } ?> value="Lease Term - # of years" name="vendors[]">&nbsp;&nbsp;Lease Term - # of years</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Date Contract Signed,') !== false) { echo " checked"; } ?> value="Date Contract Signed" name="vendors[]">&nbsp;&nbsp;Date Contract Signed</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Option to Renew Date,') !== false) { echo " checked"; } ?> value="Option to Renew Date" name="vendors[]">&nbsp;&nbsp;Option to Renew Date</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Rate Increase Date,') !== false) { echo " checked"; } ?> value="Rate Increase Date" name="vendors[]">&nbsp;&nbsp;Rate Increase Date</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Insurance Expiry Date,') !== false) { echo " checked"; } ?> value="Insurance Expiry Date" name="vendors[]">&nbsp;&nbsp;Insurance Expiry Date</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Account Expiry Date,') !== false) { echo " checked"; } ?> value="Account Expiry Date" name="vendors[]">&nbsp;&nbsp;Account Expiry Date</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Hire Date,') !== false) { echo " checked"; } ?> value="Hire Date" name="vendors[]">&nbsp;&nbsp;Hire Date</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Probation End Date,') !== false) { echo " checked"; } ?> value="Probation End Date" name="vendors[]">&nbsp;&nbsp;Probation End Date</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Probation Expiry Reminder Date,') !== false) { echo " checked"; } ?> value="Probation Expiry Reminder Date" name="vendors[]">&nbsp;&nbsp;Probation Expiry Reminder Date</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Birth Date,') !== false) { echo " checked"; } ?> value="Birth Date" name="vendors[]">&nbsp;&nbsp;Birth Date</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Company Benefit Start Date,') !== false) { echo " checked"; } ?> value="Company Benefit Start Date" name="vendors[]">&nbsp;&nbsp;Company Benefit Start Date</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>

		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Insurance."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_8" >
				Insurance<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_8" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Commercial Insurance,') !== false) { echo " checked"; } ?> value="Upload Commercial Insurance" name="vendors[]">&nbsp;&nbsp;Upload Commercial Insurance</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Commercial Insurer,') !== false) { echo " checked"; } ?> value="Commercial Insurer" name="vendors[]">&nbsp;&nbsp;Commercial Insurer</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload Residential Insurance,') !== false) { echo " checked"; } ?> value="Upload Residential Insurance" name="vendors[]">&nbsp;&nbsp;Upload Residential Insurance</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Residential Insurer,') !== false) { echo " checked"; } ?> value="Residential Insurer" name="vendors[]">&nbsp;&nbsp;Residential Insurer</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',WCB #,') !== false) { echo " checked"; } ?> value="WCB #" name="vendors[]">&nbsp;&nbsp;WCB #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Upload WCB,') !== false) { echo " checked"; } ?> value="Upload WCB" name="vendors[]">&nbsp;&nbsp;Upload WCB</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Comments."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_comments" >
				Comments<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_comments" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',General Comments,') !== false) { echo " checked"; } ?> value="General Comments" name="vendors[]">&nbsp;&nbsp;General Comments</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Comments,') !== false) { echo " checked"; } ?> value="Comments" name="vendors[]">&nbsp;&nbsp;Comments</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Notes,') !== false) { echo " checked"; } ?> value="Notes" name="vendors[]">&nbsp;&nbsp;Notes</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Status."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_15" >
				Status<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_15" class="panel-collapse collapse">
		<div class="panel-body">

			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Status,') !== false) { echo " checked"; } ?> value="Status" name="vendors[]">&nbsp;&nbsp;Status</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Social Media Links."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_sm" >
				Social Media Links<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_sm" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',LinkedIn,') !== false) { echo " checked"; } ?> value="LinkedIn" name="vendors[]">&nbsp;&nbsp;LinkedIn</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Facebook,') !== false) { echo " checked"; } ?> value="Facebook" name="vendors[]">&nbsp;&nbsp;Facebook</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Twitter,') !== false) { echo " checked"; } ?> value="Twitter" name="vendors[]">&nbsp;&nbsp;Twitter</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Login Information."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_16" >
				Login Information<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_16" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',User Name,') !== false) { echo " checked"; } ?> value="User Name" name="vendors[]">&nbsp;&nbsp;Username</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Password,') !== false) { echo " checked"; } ?> value="Password" name="vendors[]">&nbsp;&nbsp;Password</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Client Information."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_17" >
				Client Information<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_17" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client First Name,') !== false) { echo " checked"; } ?> value="Client First Name" name="vendors[]">&nbsp;&nbsp;First Name</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Last Name,') !== false) { echo " checked"; } ?> value="Client Last Name" name="vendors[]">&nbsp;&nbsp;Last Name</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Work Phone,') !== false) { echo " checked"; } ?> value="Client Work Phone" name="vendors[]">&nbsp;&nbsp;Work Phone</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Home Phone,') !== false) { echo " checked"; } ?> value="Client Home Phone" name="vendors[]">&nbsp;&nbsp;Home Phone</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Cell Phone,') !== false) { echo " checked"; } ?> value="Client Cell Phone" name="vendors[]">&nbsp;&nbsp;Cell Phone</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Fax,') !== false) { echo " checked"; } ?> value="Client Fax" name="vendors[]">&nbsp;&nbsp;Fax #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Email Address,') !== false) { echo " checked"; } ?> value="Client Email Address" name="vendors[]">&nbsp;&nbsp;Email Address</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Date of Birth,') !== false) { echo " checked"; } ?> value="Client Date of Birth" name="vendors[]">&nbsp;&nbsp;Date of Birth</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Height,') !== false) { echo " checked"; } ?> value="Client Height" name="vendors[]">&nbsp;&nbsp;Height</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Weight,') !== false) { echo " checked"; } ?> value="Client Weight" name="vendors[]">&nbsp;&nbsp;Weight</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client SIN,') !== false) { echo " checked"; } ?> value="Client SIN" name="vendors[]">&nbsp;&nbsp;SIN #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Client ID,') !== false) { echo " checked"; } ?> value="Client Client ID" name="vendors[]">&nbsp;&nbsp;Client ID #</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Support Documents,') !== false) { echo " checked"; } ?> value="Client Support Documents" name="vendors[]">&nbsp;&nbsp;Support Documents</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Client Address."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_18" >
			   Client Address<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_18" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Address,') !== false) { echo " checked"; } ?> value="Client Address" name="vendors[]">&nbsp;&nbsp;Address</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Zip Code,') !== false) { echo " checked"; } ?> value="Client Zip Code" name="vendors[]">&nbsp;&nbsp;Postal/Zip Code</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client City,') !== false) { echo " checked"; } ?> value="Client City" name="vendors[]">&nbsp;&nbsp;City/Town</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Province,') !== false) { echo " checked"; } ?> value="Client Province" name="vendors[]">&nbsp;&nbsp;Province/State</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Country,') !== false) { echo " checked"; } ?> value="Client Country" name="vendors[]">&nbsp;&nbsp;Country</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Client Program Address,') !== false) { echo " checked"; } ?> value="Client Country" name="vendors[]">&nbsp;&nbsp;Home/Program Address</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Classification."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_19" >
			   Classification<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_19" class="panel-collapse collapse">
		<div class="panel-body">
		   <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Classification Group Home 1,') !== false) { echo " checked"; } ?> value="Classification Group Home 1" name="vendors[]">&nbsp;&nbsp;Group Home 1</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Classification Group Home 2,') !== false) { echo " checked"; } ?> value="Classification Group Home 2" name="vendors[]">&nbsp;&nbsp;Group Home 2</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Classification Day Program 1,') !== false) { echo " checked"; } ?> value="Classification Day Program 1" name="vendors[]">&nbsp;&nbsp;Day Program 1</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Classification Day Program 2,') !== false) { echo " checked"; } ?> value="Classification Day Program 2" name="vendors[]">&nbsp;&nbsp;Day Program 2</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Transportation."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_20" >
			   Transportation<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_20" class="panel-collapse collapse">
		<div class="panel-body">
		   <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Transportation Mode of Transportation,') !== false) { echo " checked"; } ?> value="Transportation Mode of Transportation" name="vendors[]">&nbsp;&nbsp;Mode of Transportation</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Transportation Transit Access,') !== false) { echo " checked"; } ?> value="Transportation Transit Access" name="vendors[]">&nbsp;&nbsp;Transit Access</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Transportation Access Password,') !== false) { echo " checked"; } ?> value="Transportation Access Password" name="vendors[]">&nbsp;&nbsp;Access Password</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Transportation Drivers License,') !== false) { echo " checked"; } ?> value="Transportation Drivers License" name="vendors[]">&nbsp;&nbsp;Driver's Licence</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Drivers License Class,') !== false) { echo " checked"; } ?> value="Drivers License Class" name="vendors[]">&nbsp;&nbsp;Driver's Licence Class</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Drive Manual Transmission,') !== false) { echo " checked"; } ?> value="Drive Manual Transmission" name="vendors[]">&nbsp;&nbsp;Drive Manual Transmission</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Transportation Drivers Glasses,') !== false) { echo " checked"; } ?> value="Transportation Drivers Glasses" name="vendors[]">&nbsp;&nbsp;Driver Requires Glasses/Contacts</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Transportation Upload License,') !== false) { echo " checked"; } ?> value="Transportation Upload License" name="vendors[]">&nbsp;&nbsp;Transportation Upload Licence</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Transportation Support Documents,') !== false) { echo " checked"; } ?> value="Transportation Support Documents" name="vendors[]">&nbsp;&nbsp;Support Documents</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Financial."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_financial" >
			   Financial<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_financial" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Void Cheque,') !== false) { echo " checked"; } ?> value="Void Cheque" name="vendors[]">&nbsp;&nbsp;Void Cheque</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Bank Name,') !== false) { echo " checked"; } ?> value="Bank Name" name="vendors[]">&nbsp;&nbsp;Bank Name</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Bank Institution Number,') !== false) { echo " checked"; } ?> value="Bank Institution Number" name="vendors[]">&nbsp;&nbsp;Bank Institution Number</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Bank Transit Number,') !== false) { echo " checked"; } ?> value="Bank Transit Number" name="vendors[]">&nbsp;&nbsp;Bank Transit Number</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Bank Account Number,') !== false) { echo " checked"; } ?> value="Bank Account Number" name="vendors[]">&nbsp;&nbsp;Bank Account Number</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Emergency Fields."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_emergency" >
			   Emergency Information<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_emergency" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Primary Emergency Contact First Name,') !== false) { echo " checked"; } ?> value="Primary Emergency Contact First Name" name="vendors[]">&nbsp;&nbsp;Primary Emergency Contact First Name</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Primary Emergency Contact Last Name,') !== false) { echo " checked"; } ?> value="Primary Emergency Contact Last Name" name="vendors[]">&nbsp;&nbsp;Primary Emergency Contact Last Name</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Primary Emergency Contact Cell Phone,') !== false) { echo " checked"; } ?> value="Primary Emergency Contact Cell Phone" name="vendors[]">&nbsp;&nbsp;Primary Emergency Contact Cell Phone</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Primary Emergency Contact Home Phone,') !== false) { echo " checked"; } ?> value="Primary Emergency Contact Home Phone" name="vendors[]">&nbsp;&nbsp;Primary Emergency Contact Home Phone</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Primary Emergency Contact Email,') !== false) { echo " checked"; } ?> value="Primary Emergency Contact Email" name="vendors[]">&nbsp;&nbsp;Primary Emergency Contact Email</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Primary Emergency Contact Relationship,') !== false) { echo " checked"; } ?> value="Primary Emergency Contact Relationship" name="vendors[]">&nbsp;&nbsp;Primary Emergency Contact Relationship</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Secondary Emergency Contact First Name,') !== false) { echo " checked"; } ?> value="Secondary Emergency Contact First Name" name="vendors[]">&nbsp;&nbsp;Secondary Emergency Contact First Name</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Secondary Emergency Contact Last Name,') !== false) { echo " checked"; } ?> value="Secondary Emergency Contact Last Name" name="vendors[]">&nbsp;&nbsp;Secondary Emergency Contact Last Name</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Secondary Emergency Contact Cell Phone,') !== false) { echo " checked"; } ?> value="Secondary Emergency Contact Cell Phone" name="vendors[]">&nbsp;&nbsp;Secondary Emergency Contact Cell Phone</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Secondary Emergency Contact Home Phone,') !== false) { echo " checked"; } ?> value="Secondary Emergency Contact Home Phone" name="vendors[]">&nbsp;&nbsp;Secondary Emergency Contact Home Phone</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Secondary Emergency Contact Email,') !== false) { echo " checked"; } ?> value="Secondary Emergency Contact Email" name="vendors[]">&nbsp;&nbsp;Secondary Emergency Contact Email</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Secondary Emergency Contact Relationship,') !== false) { echo " checked"; } ?> value="Secondary Emergency Contact Relationship" name="vendors[]">&nbsp;&nbsp;Secondary Emergency Contact Relationship</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Emergency Contact First Name,') !== false) { echo " checked"; } ?> value="Emergency Contact First Name" name="vendors[]">&nbsp;&nbsp;Emergency Contact First Name</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Emergency Contact Last Name,') !== false) { echo " checked"; } ?> value="Emergency Contact Last Name" name="vendors[]">&nbsp;&nbsp;Emergency Contact Last Name</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Emergency Contact Contact Number,') !== false) { echo " checked"; } ?> value="Emergency Contact Contact Number" name="vendors[]">&nbsp;&nbsp;Emergency Contact Contact Number</div>
            <div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Emergency Contact Relationship,') !== false) { echo " checked"; } ?> value="Emergency Contact Relationship" name="vendors[]">&nbsp;&nbsp;Emergency Contact Relationship</div>
            <div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Emergency Contact Multiple,') !== false) { echo " checked"; } ?> value="Emergency Contact Multiple" name="vendors[]">&nbsp;&nbsp;Multiple Emergency Contacts</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Equipment."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_29" >
			   Equipment<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_29" class="panel-collapse collapse">
		<div class="panel-body">
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Medical Details Equipment,') !== false) { echo " checked"; } ?> value="Medical Details Equipment" name="vendors[]">&nbsp;&nbsp;Equipment</div>
			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Equipment Support Documents,') !== false) { echo " checked"; } ?> value="Equipment Support Documents" name="vendors[]">&nbsp;&nbsp;Support Documents</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
					<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Account Details."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_39" >
			   Account Details<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_39" class="panel-collapse collapse">
		<div class="panel-body">

			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Accounts Receivable/Credit on Account,') !== false) { echo " checked"; } ?> value="Accounts Receivable/Credit on Account" name="vendors[]">&nbsp;&nbsp;Accounts Receivable/Credit on Account</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Patient Accounts Receivable,') !== false) { echo " checked"; } ?> value="Patient Accounts Receivable" name="vendors[]">&nbsp;&nbsp;Patient Accounts Receivable</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Insurer Accounts Receivable for Patient,') !== false) { echo " checked"; } ?> value="Insurer Accounts Receivable for Patient" name="vendors[]">&nbsp;&nbsp;Insurer Accounts Receivable for Patient</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',All Patient Invoices,') !== false) { echo " checked"; } ?> value="All Patient Invoices" name="vendors[]">&nbsp;&nbsp;All Patient Invoices</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',All Insurer Invoices for Patient,') !== false) { echo " checked"; } ?> value="All Insurer Invoices for Patient" name="vendors[]">&nbsp;&nbsp;All Insurer Invoices for Patient</div>
			<div class="col-sm-4"><input type="checkbox" <?php if (strpos($fields_config, ',Account Statement,') !== false) { echo " checked"; } ?> value="Account Statement" name="vendors[]">&nbsp;&nbsp;Account Statement</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="contacts.php?category=Business&filter=Top" class="btn config-btn btn-lg">Back</a>
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
	<div class="panel-heading">
		<h4 class="panel-title">
			<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Profile Documents."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
			<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_prof_docs" >
			   Profile Documents<span class="glyphicon glyphicon-plus"></span>
			</a>
		</h4>
	</div>

	<div id="collapse_prof_docs" class="panel-collapse collapse">
		<div class="panel-body">

			<div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Profile Documents,') !== false) { echo " checked"; } ?> value="Profile Documents" name="vendors[]">&nbsp;&nbsp;Profile Documents</div>

			<br clear="all"><br>
			<div class="form-group">
				<div class="col-sm-6 clearfix">
					<a href="<?= WEBSITE_URL ?>/Contacts/contacts.php?category=Patient&filter=Top" class="btn config-btn btn-lg">Back</a>
				</div>
				<div class="col-sm-6">
					<button	type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
				</div>
			</div>
		</div>
	</div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Checklist."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_checklist" >
               Checklist<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_checklist" class="panel-collapse collapse">
        <div class="panel-body">

            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ',Checklist,') !== false) { echo " checked"; } ?> value="Checklist" name="vendors[]">&nbsp;&nbsp;Checklist</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Product Description."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_inv_desc" >
                Product Description<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_inv_desc" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Description".',') !== false) { echo " checked"; } ?> value="Description" name="vendors[]">&nbsp;&nbsp;Description</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Category".',') !== false) { echo " checked"; } ?> value="Category" name="vendors[]">&nbsp;&nbsp;Category</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Subcategory".',') !== false) { echo " checked"; } ?> value="Subcategory" name="vendors[]">&nbsp;&nbsp;Subcategory</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Name".',') !== false) { echo " checked"; } ?> value="Name" name="vendors[]">&nbsp;&nbsp;Name</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Product Name".',') !== false) { echo " checked"; } ?> value="Product Name" name="vendors[]">&nbsp;&nbsp;Product Name</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Type".',') !== false) { echo " checked"; } ?> value="Type" name="vendors[]">&nbsp;&nbsp;Type</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Product's Unique Identifier."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_ui" >
                Unique Identifier<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_ui" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Code".',') !== false) { echo " checked"; } ?> value="Code" name="vendors[]">&nbsp;&nbsp;Code</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."ID #".',') !== false) { echo " checked"; } ?> value="ID #" name="vendors[]">&nbsp;&nbsp;ID #</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Part #".',') !== false) { echo " checked"; } ?> value="Part #" name="vendors[]">&nbsp;&nbsp;Part #</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Product Cost."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_prod_cost" >
                Product Cost<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_prod_cost" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Cost".',') !== false) { echo " checked"; } ?> value="Cost" name="vendors[]">&nbsp;&nbsp;Cost</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."CDN Cost Per Unit".',') !== false) { echo " checked"; } ?> value="CDN Cost Per Unit" name="vendors[]">&nbsp;&nbsp;CDN Cost Per Unit</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."USD Cost Per Unit".',') !== false) { echo " checked"; } ?> value="USD Cost Per Unit" name="vendors[]">&nbsp;&nbsp;USD Cost Per Unit</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."COGS".',') !== false) { echo " checked"; } ?> value="COGS" name="vendors[]">&nbsp;&nbsp;COGS GL Code</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Average Cost".',') !== false) { echo " checked"; } ?> value="Average Cost" name="vendors[]">&nbsp;&nbsp;Average Cost</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."USD Invoice".',') !== false) { echo " checked"; } ?> value="USD Invoice" name="vendors[]">&nbsp;&nbsp;USD Invoice</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Product Purchase Info."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pur_info" >
                Purchase Info<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_pur_info" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Vendor".',') !== false) { echo " checked"; } ?> value="Vendor" name="vendors[]">&nbsp;&nbsp;<?= VENDOR_TILE ?></div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Purchase Cost".',') !== false) { echo " checked"; } ?> value="Purchase Cost" name="vendors[]">&nbsp;&nbsp;Purchase Cost</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Date Of Purchase".',') !== false) { echo " checked"; } ?> value="Date Of Purchase" name="vendors[]">&nbsp;&nbsp;Date Of Purchase</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Product Shipping &amp; Receiving."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_shipping" >
                Shipping &amp; Receiving<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_shipping" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Shipping Rate".',') !== false) { echo " checked"; } ?> value="Shipping Rate" name="vendors[]">&nbsp;&nbsp;Shipping Rate</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Shipping Cash".',') !== false) { echo " checked"; } ?> value="Shipping Cash" name="vendors[]">&nbsp;&nbsp;Shipping Cash</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Freight Charge".',') !== false) { echo " checked"; } ?> value="Freight Charge" name="vendors[]">&nbsp;&nbsp;Freight Charge</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Exchange Rate".',') !== false) { echo " checked"; } ?> value="Exchange Rate" name="vendors[]">&nbsp;&nbsp;Exchange Rate</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Exchange $".',') !== false) { echo " checked"; } ?> value="Exchange $" name="vendors[]">&nbsp;&nbsp;Exchange $</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Product Pricing."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_pricing" >
                Pricing<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_pricing" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Sell Price".',') !== false) { echo " checked"; } ?> value="Sell Price" name="vendors[]">&nbsp;&nbsp;Sell Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Final Retail Price".',') !== false) { echo " checked"; } ?> value="Final Retail Price" name="vendors[]">&nbsp;&nbsp;Final Retail Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Wholesale Price".',') !== false) { echo " checked"; } ?> value="Wholesale Price" name="vendors[]">&nbsp;&nbsp;Wholesale Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Commercial Price".',') !== false) { echo " checked"; } ?> value="Commercial Price" name="vendors[]">&nbsp;&nbsp;Commercial Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Client Price".',') !== false) { echo " checked"; } ?> value="Client Price" name="vendors[]">&nbsp;&nbsp;Client Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Preferred Price".',') !== false) { echo " checked"; } ?> value="Preferred Price" name="vendors[]">&nbsp;&nbsp;Preferred Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Admin Price".',') !== false) { echo " checked"; } ?> value="Admin Price" name="vendors[]">&nbsp;&nbsp;Admin Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Web Price".',') !== false) { echo " checked"; } ?> value="Web Price" name="vendors[]">&nbsp;&nbsp;Web Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Commission Price".',') !== false) { echo " checked"; } ?> value="Commission Price" name="vendors[]">&nbsp;&nbsp;Commission Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."MSRP".',') !== false) { echo " checked"; } ?> value="MSRP" name="vendors[]">&nbsp;&nbsp;MSRP</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Unit Price".',') !== false) { echo " checked"; } ?> value="Unit Price" name="vendors[]">&nbsp;&nbsp;Unit Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Unit Cost".',') !== false) { echo " checked"; } ?> value="Unit Cost" name="vendors[]">&nbsp;&nbsp;Unit Cost</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Purchase Order Price".',') !== false) { echo " checked"; } ?> value="Purchase Order Price" name="vendors[]">&nbsp;&nbsp;Purchase Order Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Sales Order Price".',') !== false) { echo " checked"; } ?> value="Sales Order Price" name="vendors[]">&nbsp;&nbsp;<?= SALES_ORDER_NOUN ?> Price</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Product Markup."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_markup" >
                Markup<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_markup" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Markup By $".',') !== false) { echo " checked"; } ?> value="Markup By $" name="vendors[]">&nbsp;&nbsp;Markup By $</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Markup By %".',') !== false) { echo " checked"; } ?> value="Markup By %" name="vendors[]">&nbsp;&nbsp;Markup By %</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Stock."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_stock" >
                Stock<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_stock" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Current Stock".',') !== false) { echo " checked"; } ?> value="Current Stock" name="vendors[]">&nbsp;&nbsp;Current Stock</div>
            <!-- Taken out to remove confusion between quantity and current inventory <div class="col-sm-3"><input type="checkbox" <?php //if (strpos($fields_config, ','."Current Inventory".',') !== false) { echo " checked"; } ?> value="Current Inventory" name="vendors[]">&nbsp;&nbsp;Current Inventory</div>-->
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Quantity".',') !== false) { echo " checked"; } ?> value="Quantity" name="vendors[]">&nbsp;&nbsp;Quantity</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Variance".',') !== false) { echo " checked"; } ?> value="Variance" name="vendors[]">&nbsp;&nbsp;GL Code</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Write-offs".',') !== false) { echo " checked"; } ?> value="Write-offs" name="vendors[]">&nbsp;&nbsp;Write-offs</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Buying Units".',') !== false) { echo " checked"; } ?> value="Buying Units" name="vendors[]">&nbsp;&nbsp;Buying Units</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Selling Units".',') !== false) { echo " checked"; } ?> value="Selling Units" name="vendors[]">&nbsp;&nbsp;Selling Units</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Stocking Units".',') !== false) { echo " checked"; } ?> value="Stocking Units" name="vendors[]">&nbsp;&nbsp;Stocking Units</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Product Location."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_9" >
                Product Location<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_9" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Location".',') !== false) { echo " checked"; } ?> value="Location" name="vendors[]">&nbsp;&nbsp;Location</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."LSD".',') !== false) { echo " checked"; } ?> value="LSD" name="vendors[]">&nbsp;&nbsp;LSD</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Product Dimensions."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dimensions" >
                Dimensions<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_dimensions" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Size".',') !== false) { echo " checked"; } ?> value="Size" name="vendors[]">&nbsp;&nbsp;Size</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Weight".',') !== false) { echo " checked"; } ?> value="Weight" name="vendors[]">&nbsp;&nbsp;Weight</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Alerts."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_alerts" >
                Alerts<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_alerts" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Min Max".',') !== false) { echo " checked"; } ?> value="Min Max" name="vendors[]">&nbsp;&nbsp;Min Max</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Min Bin".',') !== false) { echo " checked"; } ?> value="Min Bin" name="vendors[]">&nbsp;&nbsp;Min Bin</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Time Allocations."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_time" >
                Time Allocation<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_time" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Estimated Hours".',') !== false) { echo " checked"; } ?> value="Estimated Hours" name="vendors[]">&nbsp;&nbsp;Estimated Hours</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Actual Hours".',') !== false) { echo " checked"; } ?> value="Actual Hours" name="vendors[]">&nbsp;&nbsp;Actual Hours</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Admin Fees."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_adminfee" >
                Admin Fees<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_adminfee" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Minimum Billable".',') !== false) { echo " checked"; } ?> value="Minimum Billable" name="vendors[]">&nbsp;&nbsp;Minimum Billable</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."GL Revenue".',') !== false) { echo " checked"; } ?> value="GL Revenue" name="vendors[]">&nbsp;&nbsp;GL Revenue</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."GL Assets".',') !== false) { echo " checked"; } ?> value="GL Assets" name="vendors[]">&nbsp;&nbsp;GL Assets</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Quote description."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_quote" >
                Quote<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_quote" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Quote Description".',') !== false) { echo " checked"; } ?> value="Quote Description" name="vendors[]">&nbsp;&nbsp;Quote Description</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose General details."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_general" >
                General<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_general" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Notes".',') !== false) { echo " checked"; } ?> value="Notes" name="vendors[]">&nbsp;&nbsp;Notes</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Comments".',') !== false) { echo " checked"; } ?> value="Comments" name="vendors[]">&nbsp;&nbsp;Comments</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Rental details."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_rental" >
                Rental<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_rental" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Rent Price".',') !== false) { echo " checked"; } ?> value="Rent Price" name="vendors[]">&nbsp;&nbsp;Rent Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Rental Days".',') !== false) { echo " checked"; } ?> value="Rental Days" name="vendors[]">&nbsp;&nbsp;Rental Days</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Rental Weeks".',') !== false) { echo " checked"; } ?> value="Rental Weeks" name="vendors[]">&nbsp;&nbsp;Rental Weeks</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Rental Months".',') !== false) { echo " checked"; } ?> value="Rental Months" name="vendors[]">&nbsp;&nbsp;Rental Months</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Rental Years".',') !== false) { echo " checked"; } ?> value="Rental Years" name="vendors[]">&nbsp;&nbsp;Rental Years</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Reminder/Alert".',') !== false) { echo " checked"; } ?> value="Reminder/Alert" name="vendors[]">&nbsp;&nbsp;Reminder/Alert</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Day/Week/Month/Year."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_dwmy" >
                Day/Week/Month/Year<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_dwmy" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Daily".',') !== false) { echo " checked"; } ?> value="Daily" name="vendors[]">&nbsp;&nbsp;Daily</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Weekly".',') !== false) { echo " checked"; } ?> value="Weekly" name="vendors[]">&nbsp;&nbsp;Weekly</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Monthly".',') !== false) { echo " checked"; } ?> value="Monthly" name="vendors[]">&nbsp;&nbsp;Monthly</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Annually".',') !== false) { echo " checked"; } ?> value="Annually" name="vendors[]">&nbsp;&nbsp;Annually</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."#Of Days".',') !== false) { echo " checked"; } ?> value="#Of Days" name="vendors[]">&nbsp;&nbsp;#Of Days</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."#Of Hours".',') !== false) { echo " checked"; } ?> value="#Of Hours" name="vendors[]">&nbsp;&nbsp;#Of Hours</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Vehicle details."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_vehicle" >
                Vehicle<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_vehicle" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."#Of Kilometers".',') !== false) { echo " checked"; } ?> value="#Of Kilometers" name="vendors[]">&nbsp;&nbsp;#Of Kilometers</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."#Of Miles".',') !== false) { echo " checked"; } ?> value="#Of Miles" name="vendors[]">&nbsp;&nbsp;#Of Miles</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose inclusions."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_inclusion" >
                Inclusion<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_inclusion" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Include in P.O.S.".',') !== false) {
                echo " checked"; } ?> value="Include in P.O.S." name="vendors[]">&nbsp;&nbsp;Include in <?= POS_ADVANCE_TILE ?></div>
                <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Include in Purchase Orders".',') !== false) {
                echo " checked"; } ?> value="Include in Purchase Orders" name="vendors[]">&nbsp;&nbsp;Include in Purchase Orders</div>
                <div class="col-sm-3"><input type="checkbox" <?php if (strpos($fields_config, ','."Include in Sales Orders".',') !== false) {
                echo " checked"; } ?> value="Include in Sales Orders" name="vendors[]">&nbsp;&nbsp;Include in <?= SALES_ORDER_TILE ?></div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_field" value="inv_field" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>
</div>