<?php /* Field Configuration for Budget */
include_once ('../include.php');
checkAuthorised('budget');
$tab = filter_var($_GET['type'],FILTER_SANITIZE_STRING);

// Variables
$default_db = 'Budget,Category,Heading,Date,Staff,Receipt,Amount,Tax,Total';
$default_fields = 'Budget,Category,Heading,Staff,Heading,Date,Receipt,Amount,Tax,Total';

$config_sql = "SELECT budget_fields, budget_dashboard WHERE `tab`='$tab' UNION SELECT
	'$default_fields', '$default_db'";
$get_budget_config = mysqli_fetch_assoc(mysqli_query($dbc,$config_sql));
$value_config = ','.$get_budget_config['budget_fields'].',';
$db_config = ','.$get_budget_config['budget_dashboard'].','; ?>
<!--<div class="panel-group" id="accordion2">
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_config" >
					Budget Configuration<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_config" class="panel-collapse collapse">
			<div class="panel-body">
				<h3>Enable Tabs</h3>
				<div id='no-more-tables'>
				<table border='2' cellpadding='10' class='table'>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($tab_config, ','."budget".',') !== FALSE) { echo " checked"; } ?> value="budget" style="height: 20px; width: 20px;" name="budget_tabs[]">&nbsp;&nbsp;Use Budget Tab
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($tab_config, ','."current_month".',') !== FALSE) { echo " checked"; } ?> value="current_month" style="height: 20px; width: 20px;" name="budget_tabs[]">&nbsp;&nbsp;Current Month
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($tab_config, ','."business".',') !== FALSE) { echo " checked"; } ?> value="business" style="height: 20px; width: 20px;" name="budget_tabs[]">&nbsp;&nbsp;Business budgets
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($tab_config, ','."customers".',') !== FALSE) { echo " checked"; } ?> value="customers" style="height: 20px; width: 20px;" name="budget_tabs[]">&nbsp;&nbsp;Customer budgets
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($tab_config, ','."clients".',') !== FALSE) { echo " checked"; } ?> value="clients" style="height: 20px; width: 20px;" name="budget_tabs[]">&nbsp;&nbsp;Client budgets
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($tab_config, ','."staff".',') !== FALSE) { echo " checked"; } ?> value="staff" style="height: 20px; width: 20px;" name="budget_tabs[]">&nbsp;&nbsp;Staff budgets
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($tab_config, ','."sales".',') !== FALSE) { echo " checked"; } ?> value="sales" style="height: 20px; width: 20px;" name="budget_tabs[]">&nbsp;&nbsp;Sales budgets
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($tab_config, ','."manager".',') !== FALSE) { echo " checked"; } ?> value="manager" style="height: 20px; width: 20px;" name="budget_tabs[]">&nbsp;&nbsp;Require Manager Approval
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($tab_config, ','."payables".',') !== FALSE) { echo " checked"; } ?> value="payables" style="height: 20px; width: 20px;" name="budget_tabs[]">&nbsp;&nbsp;Payables
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($tab_config, ','."report".',') !== FALSE) { echo " checked"; } ?> value="report" style="height: 20px; width: 20px;" name="budget_tabs[]">&nbsp;&nbsp;Enable Reporting
						</td>
					</tr>
				</table>
				</div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field" >
					Choose Fields for Budget<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_field" class="panel-collapse collapse">
			<div class="panel-body">
				<div id='no-more-tables'>
				<table border='2' cellpadding='10' class='table'>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Staff".',') !== FALSE) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;budget Staff
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Contact".',') !== FALSE) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;budget Contact
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."budget For".',') !== FALSE) { echo " checked"; } ?> value="budget For" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;budget Tab
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;budget Category
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Heading".',') !== FALSE) { echo " checked"; } ?> value="Heading" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;budget Heading
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Description
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Date".',') !== FALSE) { echo " checked"; } ?> value="Date" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;budget Date
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Receipt".',') !== FALSE) { echo " checked"; } ?> value="Receipt" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Receipt
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;budget Type
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Day budget".',') !== FALSE) { echo " checked"; } ?> value="Day budget" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Day budget
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Amount".',') !== FALSE) { echo " checked"; } ?> value="Amount" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Amount
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Local Tax".',') !== FALSE) { echo " checked"; } ?> value="Local Tax" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;<?php echo $pst_name; ?>
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Tax".',') !== FALSE) { echo " checked"; } ?> value="Tax" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;<?php echo $gst_name; ?>
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Total".',') !== FALSE) { echo " checked"; } ?> value="Total" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Total
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Budget".',') !== FALSE) { echo " checked"; } ?> value="Budget" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Budget
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Signature".',') !== FALSE) { echo " checked"; } ?> value="Signature" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Signature
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Flight".',') !== FALSE) { echo " checked"; } ?> value="Flight" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Flight
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Hotel".',') !== FALSE) { echo " checked"; } ?> value="Hotel" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Hotel
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Breakfast".',') !== FALSE) { echo " checked"; } ?> value="Breakfast" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Breakfast
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Lunch".',') !== FALSE) { echo " checked"; } ?> value="Lunch" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Lunch
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Dinner".',') !== FALSE) { echo " checked"; } ?> value="Dinner" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Dinner
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Beverages".',') !== FALSE) { echo " checked"; } ?> value="Beverages" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Beverages
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Transportation".',') !== FALSE) { echo " checked"; } ?> value="Transportation" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Transportation
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Entertainment".',') !== FALSE) { echo " checked"; } ?> value="Entertainment" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Entertainment
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Gas".',') !== FALSE) { echo " checked"; } ?> value="Gas" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Gas
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($value_config, ','."Misc".',') !== FALSE) { echo " checked"; } ?> value="Misc" style="height: 20px; width: 20px;" name="budget[]">&nbsp;&nbsp;Misc
						</td>
					</tr>
				</table>
				</div>
				  <div class="form-group">
					<label for="office_zip" class="col-sm-4 control-label">Additional Types<br><em>(separated by a comma, no spaces)</em>:</label>
					<div class="col-sm-8">
					  <input name="budget_types" value="<?php echo $budget_types; ?>" type="text" class="form-control office_zip" />
					</div>
				  </div>

				  <div class="form-group">
					<label for="office_zip" class="col-sm-4 control-label">Tax Name:<br><em>(e.g. - GST, HST, etc.)</em></label>
					<div class="col-sm-8">
					  <input name="gst_name" value="<?php echo $gst_name; ?>" type="text" class="form-control" />
					</div>
				  </div>
				  <div class="form-group">
					<label for="office_zip" class="col-sm-4 control-label">Tax Amount %:<br><em>(e.g. - 5)</em></label>
					<div class="col-sm-8">
					  <input name="gst_amt" value="<?php echo $gst_amt; ?>" type="text" class="form-control" />
					</div>
				  </div>
				  <div class="form-group">
					<label for="office_zip" class="col-sm-4 control-label">Additional Tax Name:<br><em>(e.g. - PST, Sales Tax, etc.)</em></label>
					<div class="col-sm-8">
					  <input name="pst_name" value="<?php echo $pst_name; ?>" type="text" class="form-control" />
					</div>
				  </div>
				  <div class="form-group">
					<label for="office_zip" class="col-sm-4 control-label">Additional Tax Amount %:<br><em>(e.g. - 5)</em></label>
					<div class="col-sm-8">
					  <input name="pst_amt" value="<?php echo $pst_amt; ?>" type="text" class="form-control" />
					</div>
				  </div>
			</div>
		</div>
	</div>

	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_field2" >
					Choose Fields for Budget Dashboard<span class="glyphicon glyphicon-plus"></span>
				</a>
			</h4>
		</div>

		<div id="collapse_field2" class="panel-collapse collapse">
			<div class="panel-body">
				<div id='no-more-tables'>
				<table border='2' cellpadding='10' class='table'>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Contact".',') !== FALSE) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;budget Contact
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."budget For".',') !== FALSE) { echo " checked"; } ?> value="budget For" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;budget Tab
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Category".',') !== FALSE) { echo " checked"; } ?> value="Category" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;budget Category
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Heading".',') !== FALSE) { echo " checked"; } ?> value="Heading" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;budget Heading
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Description".',') !== FALSE) { echo " checked"; } ?> value="Description" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;Description
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Date".',') !== FALSE) { echo " checked"; } ?> value="Date" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;budget Date
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Receipt".',') !== FALSE) { echo " checked"; } ?> value="Receipt" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;Receipt
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Type".',') !== FALSE) { echo " checked"; } ?> value="Type" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;budget Type
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Day budget".',') !== FALSE) { echo " checked"; } ?> value="Day budget" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;Day budget
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Amount".',') !== FALSE) { echo " checked"; } ?> value="Amount" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;Amount
						</td>
					</tr>
					<tr>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Local Tax".',') !== FALSE) { echo " checked"; } ?> value="Local Tax" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;<?php echo $pst_name; ?>
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Tax".',') !== FALSE) { echo " checked"; } ?> value="Tax" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;<?php echo $gst_name; ?>
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Total".',') !== FALSE) { echo " checked"; } ?> value="Total" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;Total
						</td>
						<td>
							<input type="checkbox" <?php if (strpos($db_config, ','."Budget".',') !== FALSE) { echo " checked"; } ?> value="Budget" style="height: 20px; width: 20px;" name="budget_dashboard[]">&nbsp;&nbsp;Budget
						</td>
					</tr>
				</table>
			   </div>
			</div>
		</div>
	</div>
</div>-->