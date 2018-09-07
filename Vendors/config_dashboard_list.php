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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Employee ID,') !== false) { echo " checked"; } ?> value="Employee ID" name="dashboard[]">&nbsp;&nbsp;Employee ID</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Business,') !== false) { echo " checked"; } ?> value="Business" name="dashboard[]">&nbsp;&nbsp;Business</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Prefix,') !== false) { echo " checked"; } ?> value="Prefix" name="dashboard[]">&nbsp;&nbsp;Prefix</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Name,') !== false) { echo " checked"; } ?> value="Name" name="dashboard[]">&nbsp;&nbsp;Name</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',First Name,') !== false) { echo " checked"; } ?> value="First Name" name="dashboard[]">&nbsp;&nbsp;First Name</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Last Name,') !== false) { echo " checked"; } ?> value="Last Name" name="dashboard[]">&nbsp;&nbsp;Last Name</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Assigned Staff,') !== false) { echo " checked"; } ?> value="Assigned Staff" name="dashboard[]">&nbsp;&nbsp;Assigned Staff</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Role,') !== false) { echo " checked"; } ?> value="Role" name="dashboard[]">&nbsp;&nbsp;Role</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Division,') !== false) { echo " checked"; } ?> value="Division" name="dashboard[]">&nbsp;&nbsp;Division</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Name on Account,') !== false) { echo " checked"; } ?> value="Name on Account" name="dashboard[]">&nbsp;&nbsp;Name on Account</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Business Contacts,') !== false) { echo " checked"; } ?> value="Business Contacts" name="dashboard[]">&nbsp;&nbsp;Business Contacts</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Operating As,') !== false) { echo " checked"; } ?> value="Operating As" name="dashboard[]">&nbsp;&nbsp;Operating As</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Emergency Contact,') !== false) { echo " checked"; } ?> value="Emergency Contact" name="dashboard[]">&nbsp;&nbsp;Emergency Contact</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Occupation,') !== false) { echo " checked"; } ?> value="Occupation" name="dashboard[]">&nbsp;&nbsp;Occupation</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Office Phone,') !== false) { echo " checked"; } ?> value="Office Phone" name="dashboard[]">&nbsp;&nbsp;Office Phone</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Cell Phone,') !== false) { echo " checked"; } ?> value="Cell Phone" name="dashboard[]">&nbsp;&nbsp;Cell Phone</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Home Phone,') !== false) { echo " checked"; } ?> value="Home Phone" name="dashboard[]">&nbsp;&nbsp;Home Phone</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Fax,') !== false) { echo " checked"; } ?> value="Fax" name="dashboard[]">&nbsp;&nbsp;Fax</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Email Address,') !== false) { echo " checked"; } ?> value="Email Address" name="dashboard[]">&nbsp;&nbsp;Email Address</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Website,') !== false) { echo " checked"; } ?> value="Website" name="dashboard[]">&nbsp;&nbsp;Website</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Customer Address,') !== false) { echo " checked"; } ?> value="Customer Address" name="dashboard[]">&nbsp;&nbsp;Customer Address</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Application,') !== false) { echo " checked"; } ?> value="Application" name="dashboard[]">&nbsp;&nbsp;Application</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Contact Image,') !== false) { echo " checked"; } ?> value="Contact Image" name="dashboard[]">&nbsp;&nbsp;Contact Image</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Description,') !== false) { echo " checked"; } ?> value="Description" name="dashboard[]">&nbsp;&nbsp;Description</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Contact Since,') !== false) { echo " checked"; } ?> value="Contact Since" name="dashboard[]">&nbsp;&nbsp;Contact Since</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Date of Last Contact,') !== false) { echo " checked"; } ?> value="Date of Last Contact" name="dashboard[]">&nbsp;&nbsp;Date of Last Contact</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Referred By,') !== false) { echo " checked"; } ?> value="Referred By" name="dashboard[]">&nbsp;&nbsp;Referred By</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Company,') !== false) { echo " checked"; } ?> value="Company" name="dashboard[]">&nbsp;&nbsp;Company</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Position,') !== false) { echo " checked"; } ?> value="Position" name="dashboard[]">&nbsp;&nbsp;Position</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Title,') !== false) { echo " checked"; } ?> value="Title" name="dashboard[]">&nbsp;&nbsp;Title</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Client Tax Exemption,') !== false) { echo " checked"; } ?> value="Client Tax Exemption" name="dashboard[]">&nbsp;&nbsp;Client Tax Exemption</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Tax Exemption Number,') !== false) { echo " checked"; } ?> value="Tax Exemption Number" name="dashboard[]">&nbsp;&nbsp;Tax Exemption #</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',DUNS,') !== false) { echo " checked"; } ?> value="DUNS" name="dashboard[]">&nbsp;&nbsp;DUNS</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',CAGE,') !== false) { echo " checked"; } ?> value="CAGE" name="dashboard[]">&nbsp;&nbsp;CAGE</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Self Identification,') !== false) { echo " checked"; } ?> value="Self Identification" name="dashboard[]">&nbsp;&nbsp;Self Identification</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Credit Card on File,') !== false) { echo " checked"; } ?> value="Credit Card on File" name="dashboard[]">&nbsp;&nbsp;Credit Card on File</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Intake Form,') !== false) { echo " checked"; } ?> value="Intake Form" name="dashboard[]">&nbsp;&nbsp;Intake Form</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Rating,') !== false) { echo " checked"; } ?> value="Rating" name="dashboard[]">&nbsp;&nbsp;Rating</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Nick Name,') !== false) { echo " checked"; } ?> value="Nick Name" name="dashboard[]">&nbsp;&nbsp;Nickname</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Profile Link,') !== false) { echo " checked"; } ?> value="Profile Link" name="dashboard[]">&nbsp;&nbsp;Profile Link</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Contact Category,') !== false) { echo " checked"; } ?> value="Contact Category" name="dashboard[]">&nbsp;&nbsp;Contact Category</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Staff Category,') !== false) { echo " checked"; } ?> value="Staff Category" name="dashboard[]">&nbsp;&nbsp;Staff Category</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Gender,') !== false) { echo " checked"; } ?> value="Gender" name="dashboard[]">&nbsp;&nbsp;Gender</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',License,') !== false) { echo " checked"; } ?> value="License" name="dashboard[]">&nbsp;&nbsp;License</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Credentials,') !== false) { echo " checked"; } ?> value="Credentials" name="dashboard[]">&nbsp;&nbsp;Credentials</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Alberta Health Care No,') !== false) { echo " checked"; } ?> value="Alberta Health Care No" name="dashboard[]">&nbsp;&nbsp;Alberta Health Care #</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Invoice,') !== false) { echo " checked"; } ?> value="Invoice" name="dashboard[]">&nbsp;&nbsp;Invoice</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',MVA,') !== false) { echo " checked"; } ?> value="MVA" name="dashboard[]">&nbsp;&nbsp;MVA</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Maintenance Patient,') !== false) { echo " checked"; } ?> value="Maintenance Patient" name="dashboard[]">&nbsp;&nbsp;Maintenance Patient</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Correspondence Language,') !== false) { echo " checked"; } ?> value="Correspondence Language" name="dashboard[]">&nbsp;&nbsp;Correspondence Language</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Amount To Bill,') !== false) { echo " checked"; } ?> value="Amount To Bill" name="dashboard[]">&nbsp;&nbsp;Amount To Bill</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Amount Owing,') !== false) { echo " checked"; } ?> value="Amount Owing" name="dashboard[]">&nbsp;&nbsp;Amount Owing</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Amount Credit,') !== false) { echo " checked"; } ?> value="Amount Credit" name="dashboard[]">&nbsp;&nbsp;Amount To Credit</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Account Balance,') !== false) { echo " checked"; } ?> value="Account Balance" name="dashboard[]">&nbsp;&nbsp;Account Balance</div>
            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Customer(Client/Customer/Business),') !== false) { echo " checked"; } ?> value="Customer(Client/Customer/Business)" name="dashboard[]">&nbsp;&nbsp;Customer(Client/Customer/Business)</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Site Name (Location),') !== false) { echo " checked"; } ?> value="Site Name (Location)" name="dashboard[]">&nbsp;&nbsp;Site Name (Location)</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Display Name,') !== false) { echo " checked"; } ?> value="Display Name" name="dashboard[]">&nbsp;&nbsp;Display Name</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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

            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',License Plate #,') !== false) { echo " checked"; } ?> value="License Plate #" name="dashboard[]">&nbsp;&nbsp;License Plate #</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload License Plate,') !== false) { echo " checked"; } ?> value="Upload License Plate" name="dashboard[]">&nbsp;&nbsp;Upload License Plate</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',CARFAX,') !== false) { echo " checked"; } ?> value="CARFAX" name="dashboard[]">&nbsp;&nbsp;CARFAX</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Location Descriptions."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_3" >
                Location<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_3" class="panel-collapse collapse">
        <div class="panel-body">
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Address,') !== false) { echo " checked"; } ?> value="Address" name="dashboard[]">&nbsp;&nbsp;Address</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Mailing Address,') !== false) { echo " checked"; } ?> value="Mailing Address" name="dashboard[]">&nbsp;&nbsp;Mailing Address</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Business Address,') !== false) { echo " checked"; } ?> value="Business Address" name="dashboard[]">&nbsp;&nbsp;Business Address</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Ship To Address,') !== false) { echo " checked"; } ?> value="Ship To Address" name="dashboard[]">&nbsp;&nbsp;Ship To Address</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Postal Code,') !== false) { echo " checked"; } ?> value="Postal Code" name="dashboard[]">&nbsp;&nbsp;Postal Code</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Zip Code,') !== false) { echo " checked"; } ?> value="Zip Code" name="dashboard[]">&nbsp;&nbsp;Zip/Postal Code</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',City,') !== false) { echo " checked"; } ?> value="City" name="dashboard[]">&nbsp;&nbsp;City</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Province,') !== false) { echo " checked"; } ?> value="Province" name="dashboard[]">&nbsp;&nbsp;Province</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',State,') !== false) { echo " checked"; } ?> value="State" name="dashboard[]">&nbsp;&nbsp;State</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Country,') !== false) { echo " checked"; } ?> value="Country" name="dashboard[]">&nbsp;&nbsp;Country</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Ship Country,') !== false) { echo " checked"; } ?> value="Ship Country" name="dashboard[]">&nbsp;&nbsp;Ship Country</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Ship City,') !== false) { echo " checked"; } ?> value="Ship City" name="dashboard[]">&nbsp;&nbsp;Ship City</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Ship State,') !== false) { echo " checked"; } ?> value="Ship State" name="dashboard[]">&nbsp;&nbsp;Ship State</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Ship Zip,') !== false) { echo " checked"; } ?> value="Ship Zip" name="dashboard[]">&nbsp;&nbsp;Ship Zip</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Google Maps Address,') !== false) { echo " checked"; } ?> value="Google Maps Address" name="dashboard[]">&nbsp;&nbsp;Google Maps Address</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',City Part,') !== false) { echo " checked"; } ?> value="City Part" name="dashboard[]">&nbsp;&nbsp;City Part</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Account Number,') !== false) { echo " checked"; } ?> value="Account Number" name="dashboard[]">&nbsp;&nbsp;Account Number</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Payment Type,') !== false) { echo " checked"; } ?> value="Payment Type" name="dashboard[]">&nbsp;&nbsp;Payment Type</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Payment Name,') !== false) { echo " checked"; } ?> value="Payment Name" name="dashboard[]">&nbsp;&nbsp;Payment Name</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Payment Address,') !== false) { echo " checked"; } ?> value="Payment Address" name="dashboard[]">&nbsp;&nbsp;Payment Address</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Payment City,') !== false) { echo " checked"; } ?> value="Payment City" name="dashboard[]">&nbsp;&nbsp;Payment City</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Payment State,') !== false) { echo " checked"; } ?> value="Payment State" name="dashboard[]">&nbsp;&nbsp;Payment State</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Payment Postal Code,') !== false) { echo " checked"; } ?> value="Payment Postal Code" name="dashboard[]">&nbsp;&nbsp;Payment Postal Code</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Payment Zip Code,') !== false) { echo " checked"; } ?> value="Payment Zip Code" name="dashboard[]">&nbsp;&nbsp;Payment Zip/Postal Code</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',GST #,') !== false) { echo " checked"; } ?> value="GST #" name="dashboard[]">&nbsp;&nbsp;GST #</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',PST #,') !== false) { echo " checked"; } ?> value="PST #" name="dashboard[]">&nbsp;&nbsp;PST #</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Vendor GST #,') !== false) { echo " checked"; } ?> value="Vendor GST #" name="dashboard[]">&nbsp;&nbsp;Vendor GST #</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Payment Information,') !== false) { echo " checked"; } ?> value="Payment Information" name="dashboard[]">&nbsp;&nbsp;Payment Information</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Account Number,') !== false) { echo " checked"; } ?> value="Account Number" name="dashboard[]">&nbsp;&nbsp;Account Number</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Total Monthly Rate,') !== false) { echo " checked"; } ?> value="Total Monthly Rate" name="dashboard[]">&nbsp;&nbsp;Total Monthly Rate</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Total Annual Rate,') !== false) { echo " checked"; } ?> value="Total Annual Rate" name="dashboard[]">&nbsp;&nbsp;Total Annual Rate</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Condo Fees,') !== false) { echo " checked"; } ?> value="Condo Fees" name="dashboard[]">&nbsp;&nbsp;Condo Fees</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Deposit,') !== false) { echo " checked"; } ?> value="Deposit" name="dashboard[]">&nbsp;&nbsp;Deposit</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Damage Deposit,') !== false) { echo " checked"; } ?> value="Damage Deposit" name="dashboard[]">&nbsp;&nbsp;Damage Deposit</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Quote Description,') !== false) { echo " checked"; } ?> value="Quote Description" name="dashboard[]">&nbsp;&nbsp;Quote Description</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Pricing Level,') !== false) { echo " checked"; } ?> value="Pricing Level" name="dashboard[]">&nbsp;&nbsp;Pricing Level</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Cost,') !== false) { echo " checked"; } ?> value="Cost" name="dashboard[]">&nbsp;&nbsp;Cost</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Final Retail Price,') !== false) { echo " checked"; } ?> value="Final Retail Price" name="dashboard[]">&nbsp;&nbsp;Final Retail Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Admin Price,') !== false) { echo " checked"; } ?> value="Admin Price" name="dashboard[]">&nbsp;&nbsp;Admin Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Wholesale Price,') !== false) { echo " checked"; } ?> value="Wholesale Price" name="dashboard[]">&nbsp;&nbsp;Wholesale Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Commercial Price,') !== false) { echo " checked"; } ?> value="Commercial Price" name="dashboard[]">&nbsp;&nbsp;Commercial Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Client Price,') !== false) { echo " checked"; } ?> value="Client Price" name="dashboard[]">&nbsp;&nbsp;Client Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Minimum Billable,') !== false) { echo " checked"; } ?> value="Minimum Billable" name="dashboard[]">&nbsp;&nbsp;Minimum Billable</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Estimated Hours,') !== false) { echo " checked"; } ?> value="Estimated Hours" name="dashboard[]">&nbsp;&nbsp;Estimated Hours</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Actual Hours,') !== false) { echo " checked"; } ?> value="Actual Hours" name="dashboard[]">&nbsp;&nbsp;Actual Hours</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',MSRP,') !== false) { echo " checked"; } ?> value="MSRP" name="dashboard[]">&nbsp;&nbsp;MSRP</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Hourly Rate,') !== false) { echo " checked"; } ?> value="Hourly Rate" name="dashboard[]">&nbsp;&nbsp;Hourly Rate</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Monthly Rate,') !== false) { echo " checked"; } ?> value="Monthly Rate" name="dashboard[]">&nbsp;&nbsp;Monthly Rate</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Semi Monthly Rate,') !== false) { echo " checked"; } ?> value="Semi Monthly Rate" name="dashboard[]">&nbsp;&nbsp;Semi Monthly Rate</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Daily Rate,') !== false) { echo " checked"; } ?> value="Daily Rate" name="dashboard[]">&nbsp;&nbsp;Daily Rate</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',HR Rate Work,') !== false) { echo " checked"; } ?> value="HR Rate Work" name="dashboard[]">&nbsp;&nbsp;HR Rate Work</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',HR Rate Travel,') !== false) { echo " checked"; } ?> value="HR Rate Travel" name="dashboard[]">&nbsp;&nbsp;HR Rate Travel</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Field Day Cost,') !== false) { echo " checked"; } ?> value="Field Day Cost" name="dashboard[]">&nbsp;&nbsp;Field Day Cost</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Field Day Billable,') !== false) { echo " checked"; } ?> value="Field Day Billable" name="dashboard[]">&nbsp;&nbsp;Field Day Billable</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Probation Pay Rate,') !== false) { echo " checked"; } ?> value="Probation Pay Rate" name="dashboard[]">&nbsp;&nbsp;Probation Pay Rate</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Base Pay,') !== false) { echo " checked"; } ?> value="Base Pay" name="dashboard[]">&nbsp;&nbsp;Base Pay</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Performance Pay,') !== false) { echo " checked"; } ?> value="Performance Pay" name="dashboard[]">&nbsp;&nbsp;Performance Pay</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Property Information,') !== false) { echo " checked"; } ?> value="Property Information" name="dashboard[]">&nbsp;&nbsp;Property Information</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Property Information,') !== false) { echo " checked"; } ?> value="Upload Property Information" name="dashboard[]">&nbsp;&nbsp;Upload Property Information</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Unit #,') !== false) { echo " checked"; } ?> value="Unit #" name="dashboard[]">&nbsp;&nbsp;Unit #</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Condo Fees,') !== false) { echo " checked"; } ?> value="Condo Fees" name="dashboard[]">&nbsp;&nbsp;Condo Fees</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Base Rent,') !== false) { echo " checked"; } ?> value="Base Rent" name="dashboard[]">&nbsp;&nbsp;Base Rent</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Base Rent/Sq. Ft.,') !== false) { echo " checked"; } ?> value="Base Rent/Sq. Ft." name="dashboard[]">&nbsp;&nbsp;Base Rent/Sq. Ft.</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',CAC,') !== false) { echo " checked"; } ?> value="CAC" name="dashboard[]">&nbsp;&nbsp;CAC</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',CAC/Sq. Ft.,') !== false) { echo " checked"; } ?> value="CAC/Sq. Ft." name="dashboard[]">&nbsp;&nbsp;CAC/Sq. Ft.</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Property Tax,') !== false) { echo " checked"; } ?> value="Property Tax" name="dashboard[]">&nbsp;&nbsp;Property Tax</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Property Tax/Sq. Ft.,') !== false) { echo " checked"; } ?> value="Property Tax/Sq. Ft." name="dashboard[]">&nbsp;&nbsp;Property Tax/Sq. Ft.</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Inspection,') !== false) { echo " checked"; } ?> value="Upload Inspection" name="dashboard[]">&nbsp;&nbsp;Upload Inspection</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Bay #,') !== false) { echo " checked"; } ?> value="Bay #" name="dashboard[]">&nbsp;&nbsp;Bay #</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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

            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Letter of Intent,') !== false) { echo " checked"; } ?> value="Upload Letter of Intent" name="dashboard[]">&nbsp;&nbsp;Upload Letter of Intent</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Vendor Documents,') !== false) { echo " checked"; } ?> value="Upload Vendor Documents" name="dashboard[]">&nbsp;&nbsp;Upload <?= VENDOR_TILE ?> Documents</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Marketing Material,') !== false) { echo " checked"; } ?> value="Upload Marketing Material" name="dashboard[]">&nbsp;&nbsp;Upload Marketing Material</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Purchase Contract,') !== false) { echo " checked"; } ?> value="Upload Purchase Contract" name="dashboard[]">&nbsp;&nbsp;Upload Purchase Contract</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Support Contract,') !== false) { echo " checked"; } ?> value="Upload Support Contract" name="dashboard[]">&nbsp;&nbsp;Upload Support Contract</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Support Terms,') !== false) { echo " checked"; } ?> value="Upload Support Terms" name="dashboard[]">&nbsp;&nbsp;Upload Support Terms</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Rental Contract,') !== false) { echo " checked"; } ?> value="Upload Rental Contract" name="dashboard[]">&nbsp;&nbsp;Upload Rental Contract</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Management Contract,') !== false) { echo " checked"; } ?> value="Upload Management Contract" name="dashboard[]">&nbsp;&nbsp;Upload Management Contract</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Articles of Incorporation,') !== false) { echo " checked"; } ?> value="Upload Articles of Incorporation" name="dashboard[]">&nbsp;&nbsp;Upload Articles of Incorporation</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Option to Renew,') !== false) { echo " checked"; } ?> value="Option to Renew" name="dashboard[]">&nbsp;&nbsp;Option to Renew</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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

            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Start Date,') !== false) { echo " checked"; } ?> value="Start Date" name="dashboard[]">&nbsp;&nbsp;Start Date</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Expiry Date,') !== false) { echo " checked"; } ?> value="Expiry Date" name="dashboard[]">&nbsp;&nbsp;Expiry Date</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Renewal Date,') !== false) { echo " checked"; } ?> value="Renewal Date" name="dashboard[]">&nbsp;&nbsp;Renewal Date</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Lease Term Date,') !== false) { echo " checked"; } ?> value="Lease Term Date" name="dashboard[]">&nbsp;&nbsp;Lease Term Date</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Lease Term - # of years,') !== false) { echo " checked"; } ?> value="Lease Term - # of years" name="dashboard[]">&nbsp;&nbsp;Lease Term - # of years</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Date Contract Signed,') !== false) { echo " checked"; } ?> value="Date Contract Signed" name="dashboard[]">&nbsp;&nbsp;Date Contract Signed</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Option to Renew Date,') !== false) { echo " checked"; } ?> value="Option to Renew Date" name="dashboard[]">&nbsp;&nbsp;Option to Renew Date</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Rate Increase Date,') !== false) { echo " checked"; } ?> value="Rate Increase Date" name="dashboard[]">&nbsp;&nbsp;Rate Increase Date</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Insurance Expiry Date,') !== false) { echo " checked"; } ?> value="Insurance Expiry Date" name="dashboard[]">&nbsp;&nbsp;Insurance Expiry Date</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Account Expiry Date,') !== false) { echo " checked"; } ?> value="Account Expiry Date" name="dashboard[]">&nbsp;&nbsp;Account Expiry Date</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Hire Date,') !== false) { echo " checked"; } ?> value="Hire Date" name="dashboard[]">&nbsp;&nbsp;Hire Date</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Probation End Date,') !== false) { echo " checked"; } ?> value="Probation End Date" name="dashboard[]">&nbsp;&nbsp;Probation End Date</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Probation Expiry Reminder Date,') !== false) { echo " checked"; } ?> value="Probation Expiry Reminder Date" name="dashboard[]">&nbsp;&nbsp;Probation Expiry Reminder Date</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Birth Date,') !== false) { echo " checked"; } ?> value="Birth Date" name="dashboard[]">&nbsp;&nbsp;Birth Date</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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

            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Commercial Insurance,') !== false) { echo " checked"; } ?> value="Upload Commercial Insurance" name="dashboard[]">&nbsp;&nbsp;Upload Commercial Insurance</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Commercial Insurer,') !== false) { echo " checked"; } ?> value="Commercial Insurer" name="dashboard[]">&nbsp;&nbsp;Commercial Insurer</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload Residential Insurance,') !== false) { echo " checked"; } ?> value="Upload Residential Insurance" name="dashboard[]">&nbsp;&nbsp;Upload Residential Insurance</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Residential Insurer,') !== false) { echo " checked"; } ?> value="Residential Insurer" name="dashboard[]">&nbsp;&nbsp;Residential Insurer</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',WCB #,') !== false) { echo " checked"; } ?> value="WCB #" name="dashboard[]">&nbsp;&nbsp;WCB #</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Upload WCB,') !== false) { echo " checked"; } ?> value="Upload WCB" name="dashboard[]">&nbsp;&nbsp;Upload WCB</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose the Comments."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_9" >
                Comments<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_9" class="panel-collapse collapse">
        <div class="panel-body">

            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',General Comments,') !== false) { echo " checked"; } ?> value="General Comments" name="dashboard[]">&nbsp;&nbsp;General Comments</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Comments,') !== false) { echo " checked"; } ?> value="Comments" name="dashboard[]">&nbsp;&nbsp;Comments</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Notes,') !== false) { echo " checked"; } ?> value="Notes" name="dashboard[]">&nbsp;&nbsp;Notes</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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

            <input type="checkbox" <?php if (strpos($dashboard_config, ',Status,') !== false) { echo " checked"; } ?> value="Status" name="dashboard[]">&nbsp;&nbsp;Status

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to choose Social Media Links."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_sm" >
                Social Media Links<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_sm" class="panel-collapse collapse">
        <div class="panel-body">

            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',LinkedIn,') !== false) { echo " checked"; } ?> value="LinkedIn" name="dashboard[]">&nbsp;&nbsp;LinkedIn</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Twitter,') !== false) { echo " checked"; } ?> value="Twitter" name="dashboard[]">&nbsp;&nbsp;Twitter</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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

            <input type="checkbox" <?php if (strpos($dashboard_config, ',User Name,') !== false) { echo " checked"; } ?> value="User Name" name="dashboard[]">&nbsp;&nbsp;Username

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-heading">
        <h4 class="panel-title">
            <span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Click here to count pop ups."><img src=" <?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
            <a data-toggle="collapse" data-parent="#accordion2" href="#collapse_count" >
                Count Pop Ups<span class="glyphicon glyphicon-plus"></span>
            </a>
        </h4>
    </div>

    <div id="collapse_count" class="panel-collapse collapse">
        <div class="panel-body">

            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Total Sites,') !== false) { echo " checked"; } ?> value="Total Sites" name="dashboard[]">&nbsp;&nbsp;Total Sites</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Total Customers,') !== false) { echo " checked"; } ?> value="Total Customers" name="dashboard[]">&nbsp;&nbsp;Total Customers</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6">
                    <!--<a href="inventory.php?category=All" class="btn config-btn pull-right">Back</a>-->
                    <a href="#" class="btn config-btn btn-lg" onclick="history.go(-1);return false;">Back</a>
                </div>
                <div class="col-sm-6">
                    <button	type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Description,') !== false) { echo " checked"; } ?> value="Description" name="dashboard[]">&nbsp;&nbsp;Description</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Category,') !== false) { echo " checked"; } ?> value="Category" name="dashboard[]">&nbsp;&nbsp;Category</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Subcategory,') !== false) { echo " checked"; } ?> value="Subcategory" name="dashboard[]">&nbsp;&nbsp;Subcategory</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Name,') !== false) { echo " checked"; } ?> value="Name" name="dashboard[]">&nbsp;&nbsp;Name</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Product Name,') !== false) { echo " checked"; } ?> value="Product Name" name="dashboard[]">&nbsp;&nbsp;Product Name</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Type,') !== false) { echo " checked"; } ?> value="Type" name="dashboard[]">&nbsp;&nbsp;Type</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Code,') !== false) { echo " checked"; } ?> value="Code" name="dashboard[]">&nbsp;&nbsp;Code</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',ID #,') !== false) { echo " checked"; } ?> value="ID #" name="dashboard[]">&nbsp;&nbsp;ID #</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Part #,') !== false) { echo " checked"; } ?> value="Part #" name="dashboard[]">&nbsp;&nbsp;Part #</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Cost,') !== false) { echo " checked"; } ?> value="Cost" name="dashboard[]">&nbsp;&nbsp;Cost</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',CDN Cost Per Unit,') !== false) { echo " checked"; } ?> value="CDN Cost Per Unit" name="dashboard[]">&nbsp;&nbsp;CDN Cost Per Unit</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',USD Cost Per Unit,') !== false) { echo " checked"; } ?> value="USD Cost Per Unit" name="dashboard[]">&nbsp;&nbsp;USD Cost Per Unit</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',COGS,') !== false) { echo " checked"; } ?> value="COGS" name="dashboard[]">&nbsp;&nbsp;COGS GL Code</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Average Cost,') !== false) { echo " checked"; } ?> value="Average Cost" name="dashboard[]">&nbsp;&nbsp;Average Cost</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',USD Invoice,') !== false) { echo " checked"; } ?> value="USD Invoice" name="dashboard[]">&nbsp;&nbsp;USD Invoice</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Vendor,') !== false) { echo " checked"; } ?> value="Vendor" name="dashboard[]">&nbsp;&nbsp;<?= VENDOR_TILE ?></div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Purchase Cost,') !== false) { echo " checked"; } ?> value="Purchase Cost" name="dashboard[]">&nbsp;&nbsp;Purchase Cost</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Date Of Purchase,') !== false) { echo " checked"; } ?> value="Date Of Purchase" name="dashboard[]">&nbsp;&nbsp;Date Of Purchase</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Shipping Rate,') !== false) { echo " checked"; } ?> value="Shipping Rate" name="dashboard[]">&nbsp;&nbsp;Shipping Rate</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Shipping Cash,') !== false) { echo " checked"; } ?> value="Shipping Cash" name="dashboard[]">&nbsp;&nbsp;Shipping Cash</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Freight Charge,') !== false) { echo " checked"; } ?> value="Freight Charge" name="dashboard[]">&nbsp;&nbsp;Freight Charge</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Exchange Rate,') !== false) { echo " checked"; } ?> value="Exchange Rate" name="dashboard[]">&nbsp;&nbsp;Exchange Rate</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Exchange $,') !== false) { echo " checked"; } ?> value="Exchange $" name="dashboard[]">&nbsp;&nbsp;Exchange $</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Sell Price,') !== false) { echo " checked"; } ?> value="Sell Price" name="dashboard[]">&nbsp;&nbsp;Sell Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Final Retail Price,') !== false) { echo " checked"; } ?> value="Final Retail Price" name="dashboard[]">&nbsp;&nbsp;Final Retail Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Wholesale Price,') !== false) { echo " checked"; } ?> value="Wholesale Price" name="dashboard[]">&nbsp;&nbsp;Wholesale Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Commercial Price,') !== false) { echo " checked"; } ?> value="Commercial Price" name="dashboard[]">&nbsp;&nbsp;Commercial Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Client Price,') !== false) { echo " checked"; } ?> value="Client Price" name="dashboard[]">&nbsp;&nbsp;Client Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Preferred Price,') !== false) { echo " checked"; } ?> value="Preferred Price" name="dashboard[]">&nbsp;&nbsp;Preferred Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Admin Price,') !== false) { echo " checked"; } ?> value="Admin Price" name="dashboard[]">&nbsp;&nbsp;Admin Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Web Price,') !== false) { echo " checked"; } ?> value="Web Price" name="dashboard[]">&nbsp;&nbsp;Web Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Commission Price,') !== false) { echo " checked"; } ?> value="Commission Price" name="dashboard[]">&nbsp;&nbsp;Commission Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',MSRP,') !== false) { echo " checked"; } ?> value="MSRP" name="dashboard[]">&nbsp;&nbsp;MSRP</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Unit Price,') !== false) { echo " checked"; } ?> value="Unit Price" name="dashboard[]">&nbsp;&nbsp;Unit Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Unit Cost,') !== false) { echo " checked"; } ?> value="Unit Cost" name="dashboard[]">&nbsp;&nbsp;Unit Cost</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Purchase Order Price,') !== false) { echo " checked"; } ?> value="Purchase Order Price" name="dashboard[]">&nbsp;&nbsp;Purchase Order Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Sales Order Price,') !== false) { echo " checked"; } ?> value="Sales Order Price" name="dashboard[]">&nbsp;&nbsp;<?= SALES_ORDER_NOUN ?> Price</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Markup By $,') !== false) { echo " checked"; } ?> value="Markup By $" name="dashboard[]">&nbsp;&nbsp;Markup By $</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Markup By %,') !== false) { echo " checked"; } ?> value="Markup By %" name="dashboard[]">&nbsp;&nbsp;Markup By %</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Current Stock,') !== false) { echo " checked"; } ?> value="Current Stock" name="dashboard[]">&nbsp;&nbsp;Current Stock</div>
            <!-- Taken out to remove confusion between quantity and current inventory <div class="col-sm-3"><input type="checkbox" <?php //if (strpos($dashboard_config, ',Current Inventory,') !== false) { echo " checked"; } ?> value="Current Inventory" name="dashboard[]">&nbsp;&nbsp;Current Inventory</div>-->
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Quantity,') !== false) { echo " checked"; } ?> value="Quantity" name="dashboard[]">&nbsp;&nbsp;Quantity</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Variance,') !== false) { echo " checked"; } ?> value="Variance" name="dashboard[]">&nbsp;&nbsp;GL Code</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Write-offs,') !== false) { echo " checked"; } ?> value="Write-offs" name="dashboard[]">&nbsp;&nbsp;Write-offs</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Buying Units,') !== false) { echo " checked"; } ?> value="Buying Units" name="dashboard[]">&nbsp;&nbsp;Buying Units</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Selling Units,') !== false) { echo " checked"; } ?> value="Selling Units" name="dashboard[]">&nbsp;&nbsp;Selling Units</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Stocking Units,') !== false) { echo " checked"; } ?> value="Stocking Units" name="dashboard[]">&nbsp;&nbsp;Stocking Units</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Location,') !== false) { echo " checked"; } ?> value="Location" name="dashboard[]">&nbsp;&nbsp;Location</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',LSD,') !== false) { echo " checked"; } ?> value="LSD" name="dashboard[]">&nbsp;&nbsp;LSD</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Size,') !== false) { echo " checked"; } ?> value="Size" name="dashboard[]">&nbsp;&nbsp;Size</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Weight,') !== false) { echo " checked"; } ?> value="Weight" name="dashboard[]">&nbsp;&nbsp;Weight</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Min Max,') !== false) { echo " checked"; } ?> value="Min Max" name="dashboard[]">&nbsp;&nbsp;Min Max</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Min Bin,') !== false) { echo " checked"; } ?> value="Min Bin" name="dashboard[]">&nbsp;&nbsp;Min Bin</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Estimated Hours,') !== false) { echo " checked"; } ?> value="Estimated Hours" name="dashboard[]">&nbsp;&nbsp;Estimated Hours</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Actual Hours,') !== false) { echo " checked"; } ?> value="Actual Hours" name="dashboard[]">&nbsp;&nbsp;Actual Hours</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Minimum Billable,') !== false) { echo " checked"; } ?> value="Minimum Billable" name="dashboard[]">&nbsp;&nbsp;Minimum Billable</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',GL Revenue,') !== false) { echo " checked"; } ?> value="GL Revenue" name="dashboard[]">&nbsp;&nbsp;GL Revenue</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',GL Assets,') !== false) { echo " checked"; } ?> value="GL Assets" name="dashboard[]">&nbsp;&nbsp;GL Assets</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Quote Description,') !== false) { echo " checked"; } ?> value="Quote Description" name="dashboard[]">&nbsp;&nbsp;Quote Description</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Notes,') !== false) { echo " checked"; } ?> value="Notes" name="dashboard[]">&nbsp;&nbsp;Notes</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Comments,') !== false) { echo " checked"; } ?> value="Comments" name="dashboard[]">&nbsp;&nbsp;Comments</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Rent Price,') !== false) { echo " checked"; } ?> value="Rent Price" name="dashboard[]">&nbsp;&nbsp;Rent Price</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Rental Days,') !== false) { echo " checked"; } ?> value="Rental Days" name="dashboard[]">&nbsp;&nbsp;Rental Days</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Rental Weeks,') !== false) { echo " checked"; } ?> value="Rental Weeks" name="dashboard[]">&nbsp;&nbsp;Rental Weeks</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Rental Months,') !== false) { echo " checked"; } ?> value="Rental Months" name="dashboard[]">&nbsp;&nbsp;Rental Months</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Rental Years,') !== false) { echo " checked"; } ?> value="Rental Years" name="dashboard[]">&nbsp;&nbsp;Rental Years</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Reminder/Alert,') !== false) { echo " checked"; } ?> value="Reminder/Alert" name="dashboard[]">&nbsp;&nbsp;Reminder/Alert</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Daily,') !== false) { echo " checked"; } ?> value="Daily" name="dashboard[]">&nbsp;&nbsp;Daily</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Weekly,') !== false) { echo " checked"; } ?> value="Weekly" name="dashboard[]">&nbsp;&nbsp;Weekly</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Monthly,') !== false) { echo " checked"; } ?> value="Monthly" name="dashboard[]">&nbsp;&nbsp;Monthly</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Annually,') !== false) { echo " checked"; } ?> value="Annually" name="dashboard[]">&nbsp;&nbsp;Annually</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',#Of Days,') !== false) { echo " checked"; } ?> value="#Of Days" name="dashboard[]">&nbsp;&nbsp;#Of Days</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',#Of Hours,') !== false) { echo " checked"; } ?> value="#Of Hours" name="dashboard[]">&nbsp;&nbsp;#Of Hours</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',#Of Kilometers,') !== false) { echo " checked"; } ?> value="#Of Kilometers" name="dashboard[]">&nbsp;&nbsp;#Of Kilometers</div>
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',#Of Miles,') !== false) { echo " checked"; } ?> value="#Of Miles" name="dashboard[]">&nbsp;&nbsp;#Of Miles</div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
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
            <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Include in P.O.S.,') !== false) {
                echo " checked"; } ?> value="Include in P.O.S." name="dashboard[]">&nbsp;&nbsp;Include in <?= POS_ADVANCE_TILE ?></div>
                <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Include in Purchase Orders,') !== false) {
                echo " checked"; } ?> value="Include in Purchase Orders" name="dashboard[]">&nbsp;&nbsp;Include in Purchase Orders</div>
                <div class="col-sm-3"><input type="checkbox" <?php if (strpos($dashboard_config, ',Include in Sales Orders,') !== false) {
                echo " checked"; } ?> value="Include in Sales Orders" name="dashboard[]">&nbsp;&nbsp;Include in <?= SALES_ORDER_TILE ?></div>

            <br clear="all"><br>
            <div class="form-group">
                <div class="col-sm-6 clearfix">
                    <a href="contacts.php?category=Members&filter=Top" class="btn config-btn btn-lg">Back</a>
                </div>
                <div class="col-sm-6">
                    <button type="submit" name="inv_dashboard" value="inv_dashboard" class="btn config-btn btn-lg pull-right">Submit</button>
                </div>
            </div>
        </div>
    </div>
</div>