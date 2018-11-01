<?php if($_POST['submit'] == 'submit') {
	set_config($dbc, 'po_tabs', implode(',',$_POST['po_tabs']));
}
$po_tabs = explode(',',get_config($dbc,'po_tabs')); ?>
<form class="form-horizontal" action="" method="POST">
	<div class="form-group">
		<label class="col-sm-4">Group Purchase Orders:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('project',$po_tabs) ? 'checked' : '' ?> value="project"> <?= PROJECT_NOUN ?></label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('business',$po_tabs) ? 'checked' : '' ?> value="business"> <?= BUSINESS_CAT ?></label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('ticket',$po_tabs) ? 'checked' : '' ?> value="ticket"> <?= TICKET_TILE ?> by Tab</label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('site',$po_tabs) ? 'checked' : '' ?> value="site"> <?= SITES_CAT ?></label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('vendor',$po_tabs) ? 'checked' : '' ?> value="vendor"> Vendor</label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Enable Tabs:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('create',$po_tabs) ? 'checked' : '' ?> value="create"> Create Orders</label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('pending',$po_tabs) ? 'checked' : '' ?> value="pending"> Pending Orders</label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('receiving',$po_tabs) ? 'checked' : '' ?> value="receiving"> Receiving</label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('payable',$po_tabs) ? 'checked' : '' ?> value="payable"> Accounts Payable</label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('remote',$po_tabs) ? 'checked' : '' ?> value="remote"> Remote Purchase Orders</label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('completed',$po_tabs) ? 'checked' : '' ?> value="completed"> Completed Purchase Orders</label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('cust_po',$po_tabs) ? 'checked' : '' ?> value="cust_po"> Customer Purchase Orders</label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Summary Options:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('summary amts',$po_tabs) ? 'checked' : '' ?> value="summary amts"> Outstanding Amounts</label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('summary outstanding',$po_tabs) ? 'checked' : '' ?> value="summary outstanding"> Outstanding Purchase Orders</label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('summary date',$po_tabs) ? 'checked' : '' ?> value="summary date"> Date Sent</label>
			<label class="form-checkbox"><input type="checkbox" name="po_tabs[]" <?= in_array('summary details',$po_tabs) ? 'checked' : '' ?> value="summary details"> View PO Details (Eye)</label>
		</div>
	</div>
	<button class="btn brand-btn pull-right" type="submit" name="submit" value="submit">Submit</button>
</form>