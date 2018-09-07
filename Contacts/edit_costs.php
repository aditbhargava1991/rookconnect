<?php if($field_option == 'Amount To Bill') { ?>
	<label class="col-sm-4 control-label">Amount To Bill:</label>
	<div class="col-sm-8">
		<input type="text" name="amount_to_bill" value="<?= $contact['amount_to_bill'] ?>" data-field="amount_to_bill" data-table="contacts" class="form-control">
	</div>
<?php } else if($field_option == 'Amount Owing') { ?>
	<label class="col-sm-4 control-label">Amount Owing:</label>
	<div class="col-sm-8">
		<input type="text" name="amount_owing" value="<?= $contact['amount_owing'] ?>" data-field="amount_owing" data-table="contacts" class="form-control">
	</div>
<?php } else if($field_option == 'Amount Credit') { ?>
	<label class="col-sm-4 control-label">Amount Credit:</label>
	<div class="col-sm-8">
		<input type="text" name="amount_credit" value="<?= $contact['amount_credit'] ?>" data-field="amount_credit" data-table="contacts" class="form-control">
	</div>
<?php } else if($field_option == 'Accounts Receivable/Credit on Account') { ?>
	<label class="col-sm-4 control-label">Amount Owing:</label>
	<div class="col-sm-8">
		<input type="text" name="amount_owing" value="<?= $contact['amount_owing'] ?>" data-field="amount_owing" data-table="contacts" class="form-control">
	</div>
	<label class="col-sm-4 control-label">Amount Credit:</label>
	<div class="col-sm-8">
		<input type="text" name="amount_credit" value="<?= $contact['amount_credit'] ?>" data-field="amount_credit" data-table="contacts" class="form-control">
	</div>
	<?php $packages_sold = mysqli_query($dbc, "SELECT `package_item_type`, `item_description`, `sold_date`, IFNULL(`used_date`,'On Account') `date_used` FROM `contact_package_sold` WHERE `contactid`='{$_GET['edit']}' AND `deleted`=0");
	if(mysqli_num_rows($packages_sold) > 0) { ?>
		<div class="form-group">
			<label for="company_name" class="col-sm-4 control-label">Services and Inventory From Packages:</label>
			<div class="col-sm-8">
				<div id="no-more-tables">
					<table class="table table-bordered">
						<tr class="hidden-xs hidden-sm">
							<th>Type</th>
							<th>Description</th>
							<th>Date Sold</th>
							<th>Date Used</th>
						</tr>
						<?php while($package_line = mysqli_fetch_array($packages_sold)) { ?>
							<tr>
								<td data-title="Type"><?= $package_line['package_item_type'] ?></td>
								<td data-title="Description"><?= $package_line['item_description'] ?></td>
								<td data-title="Date Sold"><?= $package_line['sold_date'] ?></td>
								<td data-title="Date Used"><?= $package_line['date_used'] ?></td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>
<?php } else if($field_option == 'Patient Accounts Receivable') { ?>
	<h4>Accounts Receivable</h4>
	<?php $receivables = mysqli_query($dbc, "SELECT * FROM invoice_patient WHERE paid IN ('On Account','No') AND patientid = '{$_GET['edit']}' ORDER BY invoiceid");
	if(mysqli_num_rows($receivables) > 0) {
		echo '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
		echo '<tr>
			<th>Invoice#</th>
			<th>Invoice Date</th>
			<th>Amount Receivable</th>
			</tr>';
		while($invoice_receivable = mysqli_fetch_array($receivables)) {
			echo "<tr>
				<td data-title='Invoice #'><a href='../Invoice/Download/invoice_".$invoice_receivable['invoiceid'].".pdf'>".$invoice_receivable['invoiceid']."</a></td>
				<td data-title='Invoice Date'>".$invoice_receivable['invoice_date']."</td>
				<td data-title='Amount Receivable'>".$invoice_receivable['patient_price']."</td>
			</tr>";
		}
		echo '</table>';
	} else {
		echo "<h4>No Unpaid Invoices Found.</h4>";
	} ?>
<?php } else if($field_option == 'Insurer Accounts Receivable for Patient') { ?>
	<h4>Third Party Receivables</h4>
	<?php $receivables = mysqli_query($dbc, "SELECT * FROM invoice_insurer WHERE paid != 'Yes' AND `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE `patientid`='{$_GET['edit']}') ORDER BY invoiceid");
	if(mysqli_num_rows($receivables) > 0) {
		echo '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
		echo '<tr>
			<th>Invoice#</th>
			<th>Insurance Company</th>
			<th>Invoice Date</th>
			<th>Amount Receivable</th>
			</tr>';
		while($invoice_receivable = mysqli_fetch_array($receivables)) {
			echo "<tr>
				<td data-title='Invoice #'><a href='../Invoice/Download/insuranceinvoice_".$invoice_receivable['insurerid']."_".$invoice_receivable['invoiceid'].".pdf'>".$invoice_receivable['invoiceid']."</a></td>
				<td data-title='Insurance Company'>".get_client($dbc, $invoice_receivable['insurerid'])."</td>
				<td data-title='Invoice Date'>".$invoice_receivable['invoice_date']."</td>
				<td data-title='Amount Receivable'>".$invoice_receivable['insurer_price']."</td>
			</tr>";
		}
		echo '</table>';
	} else {
		echo "<h4>No Unpaid Invoices Found.</h4>";
	} ?>
<?php } else if($field_option == 'All Patient Invoices') { ?>
	<h4>All Direct Invoices</h4>
	<?php $receivables = mysqli_query($dbc, "SELECT * FROM invoice_patient WHERE patientid = '{$_GET['edit']}' ORDER BY invoiceid");
	if(mysqli_num_rows($receivables) > 0) {
		echo '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
		echo '<tr>
			<th>Invoice#</th>
			<th>Invoice Date</th>
			<th>Invoice Amount</th>
			<th>Paid</th>
			</tr>';
		while($invoice_receivable = mysqli_fetch_array($receivables)) {
			echo "<tr>
				<td data-title='Invoice #'><a href='../Invoice/Download/invoice_".$invoice_receivable['invoiceid'].".pdf'>".$invoice_receivable['invoiceid']."</a></td>
				<td data-title='Invoice Date'>".$invoice_receivable['invoice_date']."</td>
				<td data-title='Invoice Amount'>".$invoice_receivable['patient_price']."</td>
				<td data-title='Paid'>".$invoice_receivable['paid']."</td>
			</tr>";
		}
		echo '</table>';
	} else {
		echo "<h4>No Direct Invoices Found.</h4>";
	} ?>
<?php } else if($field_option == 'All Insurer Invoices for Patient') { ?>
	<h4>All Third Party Invoices</h4>
	<?php $receivables = mysqli_query($dbc, "SELECT * FROM invoice_insurer WHERE `invoiceid` IN (SELECT `invoiceid` FROM `invoice` WHERE `patientid`='{$_GET['edit']}') ORDER BY invoiceid");
	if(mysqli_num_rows($receivables) > 0) {
		echo '<table border="1px" class="table table-bordered" style="'.$table_style.'">';
		echo '<tr>
			<th>Invoice#</th>
			<th>Insurance Company</th>
			<th>Invoice Date</th>
			<th>Invoice Amount</th>
			<th>Paid</th>
			</tr>';
		while($invoice_receivable = mysqli_fetch_array($receivables)) {
			echo "<tr>
				<td data-title='Invoice #'><a href='../Invoice/Download/insuranceinvoice_".$invoice_receivable['insurerid']."_".$invoice_receivable['invoiceid'].".pdf'>".$invoice_receivable['invoiceid']."</a></td>
				<td data-title='Insurance Company'>".get_client($dbc, $invoice_receivable['insurerid'])."</td>
				<td data-title='Invoice Date'>".$invoice_receivable['invoice_date']."</td>
				<td data-title='Invoice Amount'>".$invoice_receivable['insurer_price']."</td>
				<td data-title='Paid'>".$invoice_receivable['paid']."</td>
			</tr>";
		}
		echo '</table>';
	} else {
		echo "<h4>No Third Party Invoices Found.</h4>";
	} ?>
<?php } else if($field_option == 'Total Monthly Rate') { ?>
	<label class="col-sm-4 control-label">Total Monthly Rate:</label>
	<div class="col-sm-8">
		<input type="text" name="total_monthly_rate" value="<?= $contact['total_monthly_rate'] ?>" data-field="total_monthly_rate" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Total Annual Rate') { ?>
	<label class="col-sm-4 control-label">Total Annual Rate:</label>
	<div class="col-sm-8">
		<input type="text" name="total_annual_rate" value="<?= $contact['total_annual_rate'] ?>" data-field="total_annual_rate" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Condo Fees' || $field_option == 'Condo Fees Property') { ?>
	<label class="col-sm-4 control-label">Condo Fees:</label>
	<div class="col-sm-8">
		<input type="text" name="condo_fees" value="<?= $contact['condo_fees'] ?>" data-field="condo_fees" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Deposit') { ?>
	<label class="col-sm-4 control-label">Deposit:</label>
	<div class="col-sm-8">
		<input type="text" name="deposit" value="<?= $contact['deposit'] ?>" data-field="deposit" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Damage Deposit') { ?>
	<label class="col-sm-4 control-label">Damage Deposit:</label>
	<div class="col-sm-8">
		<input type="text" name="damage_deposit" value="<?= $contact['damage_deposit'] ?>" data-field="damage_deposit" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Cost') { ?>
	<label class="col-sm-4 control-label">Cost:</label>
	<div class="col-sm-8">
		<input type="text" name="cost" value="<?= $contact['cost'] ?>" data-field="cost" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Final Retail Price') { ?>
	<label class="col-sm-4 control-label">Final Retail Price:</label>
	<div class="col-sm-8">
		<input type="text" name="final_retail_price" value="<?= $contact['final_retail_price'] ?>" data-field="final_retail_price" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Admin Price') { ?>
	<label class="col-sm-4 control-label">Admin Price:</label>
	<div class="col-sm-8">
		<input type="text" name="admin_price" value="<?= $contact['admin_price'] ?>" data-field="admin_price" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Wholesale Price') { ?>
	<label class="col-sm-4 control-label">Wholesale Price:</label>
	<div class="col-sm-8">
		<input type="text" name="wholesale_price" value="<?= $contact['wholesale_price'] ?>" data-field="wholesale_price" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Commercial Price') { ?>
	<label class="col-sm-4 control-label">Commercial Price:</label>
	<div class="col-sm-8">
		<input type="text" name="commercial_price" value="<?= $contact['commercial_price'] ?>" data-field="commercial_price" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Client Price') { ?>
	<label class="col-sm-4 control-label">Client Price:</label>
	<div class="col-sm-8">
		<input type="text" name="client_price" value="<?= $contact['client_price'] ?>" data-field="client_price" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Minimum Billable') { ?>
	<label class="col-sm-4 control-label">Minimum Billable:</label>
	<div class="col-sm-8">
		<input type="text" name="minimum_billable" value="<?= $contact['minimum_billable'] ?>" data-field="minimum_billable" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Estimated Hours') { ?>
	<label class="col-sm-4 control-label">Estimated Hours:</label>
	<div class="col-sm-8">
		<input type="text" name="estimated_hours" value="<?= $contact['estimated_hours'] ?>" data-field="estimated_hours" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Actual Hours') { ?>
	<label class="col-sm-4 control-label">Actual Hours:</label>
	<div class="col-sm-8">
		<input type="text" name="actual_hours" value="<?= $contact['actual_hours'] ?>" data-field="actual_hours" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'MSRP') { ?>
	<label class="col-sm-4 control-label">MSRP:</label>
	<div class="col-sm-8">
		<input type="text" name="msrp" value="<?= $contact['msrp'] ?>" data-field="msrp" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Hourly Rate') { ?>
	<label class="col-sm-4 control-label">Hourly Rate:</label>
	<div class="col-sm-8">
		<input type="text" name="hourly_rate" value="<?= $contact['hourly_rate'] ?>" data-field="hourly_rate" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Monthly Rate') { ?>
	<label class="col-sm-4 control-label">Monthly Rate:</label>
	<div class="col-sm-8">
		<input type="text" name="monthly_rate" value="<?= $contact['monthly_rate'] ?>" data-field="monthly_rate" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Semi Monthly Rate') { ?>
	<label class="col-sm-4 control-label">Semi Monthly Rate:</label>
	<div class="col-sm-8">
		<input type="text" name="semi_monthly_rate" value="<?= $contact['semi_monthly_rate'] ?>" data-field="semi_monthly_rate" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Daily Rate') { ?>
	<label class="col-sm-4 control-label">Daily Rate:</label>
	<div class="col-sm-8">
		<input type="text" name="daily_rate" value="<?= $contact['daily_rate'] ?>" data-field="daily_rate" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'HR Rate Work') { ?>
	<label class="col-sm-4 control-label">HR Rate Work:</label>
	<div class="col-sm-8">
		<input type="text" name="hr_rate_work" value="<?= $contact['hr_rate_work'] ?>" data-field="hr_rate_work" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'HR Rate Travel') { ?>
	<label class="col-sm-4 control-label">HR Rate Travel:</label>
	<div class="col-sm-8">
		<input type="text" name="hr_rate_travel" value="<?= $contact['hr_rate_travel'] ?>" data-field="hr_rate_travel" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Field Day Cost') { ?>
	<label class="col-sm-4 control-label">Field Day Cost:</label>
	<div class="col-sm-8">
		<input type="text" name="field_day_cost" value="<?= $contact['field_day_cost'] ?>" data-field="field_day_cost" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Field Day Billable') { ?>
	<label class="col-sm-4 control-label">Field Day Billable:</label>
	<div class="col-sm-8">
		<input type="text" name="field_day_billable" value="<?= $contact['field_day_billable'] ?>" data-field="field_day_billable" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Probation Pay Rate') { ?>
	<label class="col-sm-4 control-label">Probation Pay Rate:</label>
	<div class="col-sm-8">
		<input type="text" name="probation_pay_rate" value="<?= $contact['probation_pay_rate'] ?>" data-field="probation_pay_rate" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Base Pay') { ?>
	<label class="col-sm-4 control-label">Base Pay:</label>
	<div class="col-sm-8">
		<input type="text" name="base_pay" value="<?= $contact['base_pay'] ?>" data-field="base_pay" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Performance Pay') { ?>
	<label class="col-sm-4 control-label">Performance Pay:</label>
	<div class="col-sm-8">
		<input type="text" name="performance_pay" value="<?= $contact['performance_pay'] ?>" data-field="performance_pay" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Unit #') { ?>
	<label class="col-sm-4 control-label">Unit #:</label>
	<div class="col-sm-8">
		<input type="text" name="unit_no" value="<?= $contact['unit_no'] ?>" data-field="unit_no" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Base Rent') { ?>
	<label class="col-sm-4 control-label">Base Rent:</label>
	<div class="col-sm-8">
		<input type="text" name="base_rent" value="<?= $contact['base_rent'] ?>" data-field="base_rent" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Base Rent/Sq. Ft.') { ?>
	<label class="col-sm-4 control-label">Base Rent/Sq. Ft.:</label>
	<div class="col-sm-8">
		<input type="text" name="base_rent_sq_ft" value="<?= $contact['base_rent_sq_ft'] ?>" data-field="base_rent_sq_ft" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'CAC') { ?>
	<label class="col-sm-4 control-label">CAC:</label>
	<div class="col-sm-8">
		<input type="text" name="cac" value="<?= $contact['cac'] ?>" data-field="cac" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'CAC/Sq. Ft.') { ?>
	<label class="col-sm-4 control-label">CAC/Sq. Ft.:</label>
	<div class="col-sm-8">
		<input type="text" name="cac_sq_ft" value="<?= $contact['cac_sq_ft'] ?>" data-field="cac_sq_ft" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Property Tax') { ?>
	<label class="col-sm-4 control-label">Property Tax:</label>
	<div class="col-sm-8">
		<input type="text" name="property_tax" value="<?= $contact['property_tax'] ?>" data-field="property_tax" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Property Tax/Sq. Ft.') { ?>
	<label class="col-sm-4 control-label">Property Tax/Sq. Ft.:</label>
	<div class="col-sm-8">
		<input type="text" name="property_tax_sq_ft" value="<?= $contact['property_tax_sq_ft'] ?>" data-field="property_tax_sq_ft" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Max KM') { ?>
	<label class="col-sm-4 control-label">Allowable KMs:</label>
	<div class="col-sm-8">
		<input type="number" min=0 step="any" placeholder="N/A" name="max_km" value="<?= $contact['max_km'] ?>" data-field="max_km" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Max Pieces') { ?>
	<label class="col-sm-4 control-label">Allowable Pieces:</label>
	<div class="col-sm-8">
		<input type="number" min=0 step="any" placeholder="N/A" name="max_pieces" value="<?= $contact['max_pieces'] ?>" data-field="max_pieces" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Contract Total Value') { ?>
	<label class="col-sm-4 control-label">Total Dollar Value of Contract:</label>
	<div class="col-sm-8">
		<input type="number" min=0 step="any" placeholder="" name="contract_dollar_value" value="<?= $contact['contract_dollar_value'] ?>" data-field="contract_dollar_value" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Total Bill Amount') { ?>
	<label class="col-sm-4 control-label">Total Rate:</label>
	<div class="col-sm-8">
		<input type="number" min=0 step="any" placeholder="" name="total_rate" value="<?= $contact['total_rate'] ?>" data-field="total_rate" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Billable Hours') { ?>
	<label class="col-sm-4 control-label">Total Billable Hours (H:MM):</label>
	<div class="col-sm-8">
		<input type="text" placeholder="" name="billable_hours" value="<?= time_decimal2time($contact['billable_hours']) ?>" data-field="billable_hours" data-table="contacts_cost" class="form-control timepicker-max-5">
	</div>
<?php } else if($field_option == 'Billable Dollars') { ?>
	<label class="col-sm-4 control-label">Total Billable Dollars:</label>
	<div class="col-sm-8">
		<input type="number" min=0 step="any" placeholder="" name="billable_dollars" value="<?= $contact['billable_dollars'] ?>" data-field="billable_dollars" data-table="contacts_cost" class="form-control">
	</div>
<?php } else if($field_option == 'Hours Billed') { ?>
	<label class="col-sm-4 control-label">Dollars Billed to Date:</label>
	<div class="col-sm-8">
		<?php $invoiced_amount = 0;
		$invoiced_amount = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(`final_price`) `total` FROM invoice WHERE deleted=0 AND patientid = '{$_GET['edit']}' ORDER BY invoiceid"))['total'];
		echo '$'.number_format($invoiced_amount, 2); ?>
	</div>
<?php } else if($field_option == 'Hours Tracked') { ?>
	<label class="col-sm-4 control-label">Hours Tracked to Date (H:MM):</label>
	<div class="col-sm-8">
		<?php $tracked_time = 0;
		$time_list = mysqli_query($dbc, "SELECT `ticket_timer`.`ticketid`,TIME_TO_SEC(`ticket_timer`.`timer`) `seconds` FROM `ticket_timer` LEFT JOIN `tickets` ON `ticket_timer`.`ticketid` = `tickets`.`ticketid` WHERE '{$_GET['edit']}' IN (`tickets`.`businessid`,`tickets`.`clientid`) AND `ticket_timer`.`deleted`=0");
		while($time_info = mysqli_fetch_array($time_list)) {
			$tracked_time += $time_info['seconds'];
		}
		$time_list = mysqli_query($dbc, "SELECT `total_hrs` * 3600 `seconds` FROM `time_cards` LEFT JOIN `project` ON `time_cards`.`projectid`=`project`.`projectid` LEFT JOIN `tickets` ON `time_cards`.`ticketid`=`tickets`.`ticketid` WHERE (`time_cards`.`clientid` = '{$_GET['edit']}' OR `time_cards`.`business`='{$_GET['edit']}' OR `tickets`.`clientid`='{$_GET['edit']}' OR `tickets`.`businessid`='{$_GET['edit']}' OR `project`.`businessid`='{$_GET['edit']}' OR `project`.`clientid`='{$_GET['edit']}') AND `time_cards`.`deleted`=0");
		while($time_info = mysqli_fetch_array($time_list)) {
			$tracked_time += $time_info['seconds'];
		}
		echo time_decimal2time($tracked_time / 3600); ?>
	</div>
<?php } else if($field_option == 'Payment Frequency') { ?>
	<label class="col-sm-4 control-label">Frequency:</label>
	<div class="col-sm-8">
		<input type="text" class="form-control" name="payment_frequency[]" value="<?= explode(':',$contact['payment_frequency'])[0] ?>" data-field="payment_frequency" data-delimiter=":" data-table="contacts_cost" style="<?= substr(explode(':',$contact['payment_frequency'])[1],0,4) == 'CUST' ? '' : 'display:none;' ?>">
		<select class="chosen-select-deselect" name="payment_frequency[]" data-field="payment_frequency" data-delimiter=":" data-table="contacts_cost" onchange="if(this.value.substr(0,4) == 'CUST') { $('input[name^=payment_frequency]').show().focus(); } else { $('input[name^=payment_frequency]').hide(); }"><option />
			<option></option>
			<option <?= explode(':',$contact['payment_frequency'])[1] == 'Daily' ? 'selected' : '' ?> value="Daily">Daily</option>
			<option <?= explode(':',$contact['payment_frequency'])[1] == 'Weekly' ? 'selected' : '' ?> value="Weekly">Weekly</option>
			<option <?= explode(':',$contact['payment_frequency'])[1] == 'BiWeekly' ? 'selected' : '' ?> value="BiWeekly">Bi-Weekly</option>
			<option <?= explode(':',$contact['payment_frequency'])[1] == 'Monthly' ? 'selected' : '' ?> value="Monthly">Monthly</option>
			<option <?= explode(':',$contact['payment_frequency'])[1] == 'Quarterly' ? 'selected' : '' ?> value="Quarterly">Quarterly</option>
			<option <?= explode(':',$contact['payment_frequency'])[1] == 'Annually' ? 'selected' : '' ?> value="Annually">Annually</option>
			<option <?= explode(':',$contact['payment_frequency'])[1] == 'CUSTDY' ? 'selected' : '' ?> value="CUSTDY">Days</option>
			<option <?= explode(':',$contact['payment_frequency'])[1] == 'CUSTWK' ? 'selected' : '' ?> value="CUSTWK">Weeks</option>
			<option <?= explode(':',$contact['payment_frequency'])[1] == 'CUSTMN' ? 'selected' : '' ?> value="CUSTMN">Months</option>
		</select>
	</div>
<?php } ?>