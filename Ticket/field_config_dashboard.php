<script>
$(document).ready(function() {
	$('.form-group label [type=checkbox],[name=slider_button]').change(saveFields);
});
function saveFields() {
	var tickets_dashboard = [];
	$('[name="tickets_dashboard[]"]:checked').each(function() {
		tickets_dashboard.push(this.value);
	});
	var tickets_sort = [];
	$('[name="tickets_sort[]"]:checked').each(function() {
		tickets_sort.push(this.value);
	});
	var tickets_summary = [];
	$('[name="tickets_summary[]"]:checked').each(function() {
		tickets_summary.push(this.value);
	});
	$.ajax({
		url: 'ticket_ajax_all.php?action=ticket_db',
		method: 'POST',
		data: {
			fields: tickets_dashboard,
			sort: tickets_sort,
			summary: tickets_summary,
			slider_button: $('[name=slider_button]').val()
		}
	});
}
</script>
<?php $db_config = get_field_config($dbc, 'tickets_dashboard'); ?>
<div class="form-group">
	<label class="col-sm-4 control-label">
		<span class="popover-examples"><a data-toggle="tooltip" data-original-title="These fields will appear on the <?= TICKET_NOUN ?> card or in the table list of <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Dashboard Fields:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Label".',') !== false) { echo " checked"; } ?> value="Label" style="height: 20px; width: 20px;" name="tickets_dashboard[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will switch whether the card displays the customer <?= TICKET_NOUN ?> label, or simply displays the <?= TICKET_NOUN ?> ID."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_NOUN ?> Label</label>

		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Client As Label".',') !== false) { echo " checked"; } ?> value="Client As Label" style="height: 20px; width: 20px;" name="tickets_dashboard[]">Client As Label</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Created Date As Label".',') !== false) { echo " checked"; } ?> value="Created Date As Label" style="height: 20px; width: 20px;" name="tickets_dashboard[]">Created Date As Label</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Status As Label".',') !== false) { echo " checked"; } ?> value="Status As Label" style="height: 20px; width: 20px;" name="tickets_dashboard[]">Status As Label</label>

		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Extra Billing".',') !== false) { echo " checked"; } ?> value="Extra Billing" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Extra Billing Alert</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Overview Icon".',') !== false) { echo " checked"; } ?> value="Overview Icon" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Overview Icon</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Hide Slider".',') !== false) { echo " checked"; } ?> value="Hide Slider" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Hide Action Mode Button</label>
		<label class="form-checkbox"><input type="text" class="form-control" placeholder="Action Mode Button Label" value="<?= get_config($dbc, 'ticket_slider_button') ?>" name="slider_button"></label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Action Mode Button Eyeball".',') !== false) { echo " checked"; } ?> value="Action Mode Button Eyeball" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Use Eyeball Icon For Action Mode Button</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Project".',') !== false) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="tickets_dashboard[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will list the <?= PROJECT_NOUN ?> attached to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= PROJECT_NOUN ?> Information</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Business".',') !== false) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Business</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Contact".',') !== false) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Contact</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Services".',') !== false) { echo " checked"; } ?> value="Services" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Services</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Heading".',') !== false) { echo " checked"; } ?> value="Heading" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Heading</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Staff".',') !== false) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Staff</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Members".',') !== false) { echo " checked"; } ?> value="Members" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Members</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Clients".',') !== false) { echo " checked"; } ?> value="Clients" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Clients</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Service Template".',') !== false) { echo " checked"; } ?> value="Service Template" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Service Template</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Create Date".',') !== false) { echo " checked"; } ?> value="Create Date" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Created Date</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Ticket Date".',') !== false) { echo " checked"; } ?> value="Ticket Date" style="height: 20px; width: 20px;" name="tickets_dashboard[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will display the date for which the <?= TICKET_NOUN ?> is scheduled."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_NOUN ?> Dates</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Deliverable Date".',') !== false) { echo " checked"; } ?> value="Deliverable Date" style="height: 20px; width: 20px;" name="tickets_dashboard[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will display the scheduled date, the Internal QA date, and the Customer QA date for the  <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Deliverable Dates</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Documents".',') !== false) { echo " checked"; } ?> value="Documents" style="height: 20px; width: 20px;" name="tickets_dashboard[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will display all documents attached to the <?= TICKET_NOUN ?> or the <?= PROJECT_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_NOUN ?> Documents</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Purchase Order".',') !== false) { echo " checked"; } ?> value="Purchase Order" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Purchase Order #</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Customer Order".',') !== false) { echo " checked"; } ?> value="Customer Order" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Customer Order #</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Invoiced".',') !== false) { echo " checked"; } ?> value="Invoiced" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Invoiced (Y/N)</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Status".',') !== false) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Status</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Total Budget Time".',') !== false) { echo " checked"; } ?> value="Total Budget Time" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Total Budget Time</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Service Time Estimate".',') !== false) { echo " checked"; } ?> value="Service Time Estimate" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Service Time Estimate</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."PDF".',') !== false) { echo " checked"; } ?> value="PDF" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> PDF Download</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Export Ticket Log".',') !== false) { echo " checked"; } ?> value="Export Ticket Log" style="height: 20px; width: 20px;" name="tickets_dashboard[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will display a link to the <?= TICKET_NOUN ?> log from the dashboard."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Export <?= TICKET_NOUN ?> Log</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Edit Archive".',') !== false) { echo " checked"; } ?> value="Edit Archive" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Edit / Archive / History Links</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Edit Staff".',') !== false) { echo " checked"; } ?> value="Edit Staff" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Edit Staff</label>

		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Milestone Timeline".',') !== false) { echo " checked"; } ?> value="Milestone Timeline" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Milestone & Timeline</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Site Address".',') !== false) { echo " checked"; } ?> value="Site Address" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Site Address</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Site Notes".',') !== false) { echo " checked"; } ?> value="Site Notes" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Site Notes</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Google Maps Link".',') !== false) { echo " checked"; } ?> value="Google Maps Link" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Google Maps Link</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Ticket Notes".',') !== false) { echo " checked"; } ?> value="Ticket Notes" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> <?= TICKET_NOUN ?> Notes</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Delivery Notes".',') !== false) { echo " checked"; } ?> value="Delivery Notes" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Delivery Notes</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Delivery Status".',') !== false) { echo " checked"; } ?> value="Delivery Status" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Delivery Status Summary</label>

		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Key Number".',') !== false) { echo " checked"; } ?> value="Key Number" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Key Number</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Door Code Number".',') !== false) { echo " checked"; } ?> value="Door Code Number" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Door Code Number</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Alarm Code Number".',') !== false) { echo " checked"; } ?> value="Alarm Code Number" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Alarm Code Number</label>

	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">
		<span class="popover-examples"><a data-toggle="tooltip" data-original-title="These options will control what additional tabs display along the left side of the <?= TICKET_TILE ?> dashboard."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Dashboard Tabs:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Export".',') !== false) { echo " checked"; } ?> value="Export" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Import / Export Button</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Manifest".',') !== false) { echo " checked"; } ?> value="Manifest" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Manifest Printouts</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Administration".',') !== false) { echo " checked"; } ?> value="Administration" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> <?= TICKET_NOUN ?> Approvals</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Invoicing".',') !== false) { echo " checked"; } ?> value="Invoicing" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Accounting</label>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">
		<span class="popover-examples"><a data-toggle="tooltip" data-original-title="These options will control what filters display under the Approvals section of the <?= TICKET_TILE ?> dashboard."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Approvals Sort Fields:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Administration Sort Sites".',') !== false) { echo " checked"; } ?> value="Administration Sort Sites" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Site</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Administration Sort Business".',') !== false) { echo " checked"; } ?> value="Administration Sort Business" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Business</label>
	</div>
</div>
<?php $db_sort = ','.(get_config($dbc, 'tickets_sort') ?: $db_config).','; ?>
<div class="form-group">
	<label class="col-sm-4 control-label">
		<span class="popover-examples"><a data-toggle="tooltip" data-original-title="These options will control what filters display along the left side of the <?= TICKET_TILE ?> dashboard."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Sort Fields:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_sort, ','."Top 25 All".',') !== false) { echo " checked"; } ?> value="Top 25 All" style="height: 20px; width: 20px;" name="tickets_sort[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display a tab with the last 25 created <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Top <?= TICKET_TILE ?></label>
		<?php foreach($ticket_tabs as $type => $label) { ?>
			<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_sort, ",Top 25 $type,") !== false) { echo " checked"; } ?> value="Top 25 <?= $type ?>" style="height: 20px; width: 20px;" name="tickets_sort[]">
				<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display a tab with the last 25 created <?= $label.' '.TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Top <?= $label ?></label>
		<?php } ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_sort, ','."Top 25 Forms".',') !== false) { echo " checked"; } ?> value="Top 25 Forms" style="height: 20px; width: 20px;" name="tickets_sort[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display a tab with the last 25 created custom <?= TICKET_NOUN ?> forms."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Top Forms</label>
		<?php $forms = $dbc->query("SELECT `pdf_name`, `id` FROM `ticket_pdf` ORDER BY `pdf_name`");
		while($form = $forms->fetch_assoc()) { ?>
			<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_sort, ','."Top 25 Form ".$form['id'].',') !== false) { echo " checked"; } ?> value="Top 25 Form <?= $form['id'] ?>" style="height: 20px; width: 20px;" name="tickets_sort[]">
				<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display a tab with the last 25 created <?= $form['pdf_name'] ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Top <?= $form['pdf_name'] ?></label>
		<?php } ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_sort, ','."Project".',') !== false) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="tickets_sort[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to filter <?= TICKET_TILE ?> by <?= PROJECT_NOUN ?> Tab."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= PROJECT_NOUN ?> Type</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_sort, ','."Project ID".',') !== false) { echo " checked"; } ?> value="Project ID" style="height: 20px; width: 20px;" name="tickets_sort[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to filter <?= TICKET_TILE ?> by <?= PROJECT_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= PROJECT_NOUN ?></label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_sort, ','."Business".',') !== false) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="tickets_sort[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to filter <?= TICKET_TILE ?> by <?= BUSINESS_CAT ?> or by Contact if there is no <?= BUSINESS_CAT ?> field enabled."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= BUSINESS_CAT ?> / Contact</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_sort, ','."Staff".',') !== false) { echo " checked"; } ?> value="Staff" style="height: 20px; width: 20px;" name="tickets_sort[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to filter <?= TICKET_TILE ?> by Staff Members assigned."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Staff - Assigned</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_sort, ','."Staff Create".',') !== false) { echo " checked"; } ?> value="Staff Create" style="height: 20px; width: 20px;" name="tickets_sort[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to filter <?= TICKET_TILE ?> by who created the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Staff - Created</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_sort, ','."Purchase Order".',') !== false) { echo " checked"; } ?> value="Purchase Order" style="height: 20px; width: 20px;" name="tickets_sort[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to filter <?= TICKET_TILE ?> by the Purchase Order field, if it is turned on."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Purchase Order</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_sort, ','."Status".',') !== false) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="tickets_sort[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to filter <?= TICKET_TILE ?> by the current Status."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Status</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_sort, ','."ALL".',') !== false) { echo " checked"; } ?> value="ALL" style="height: 20px; width: 20px;" name="tickets_sort[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to display all <?= TICKET_TILE ?> of a particular type."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>View All</label>
	</div>
</div>
<?php $db_summary = ','.get_config($dbc, 'tickets_summary').','; ?>
<div class="form-group">
	<label class="col-sm-4 control-label">
		<span class="popover-examples"><a data-toggle="tooltip" data-original-title="These settings will enable a special summary view for the <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Summary View:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Disable".',') !== false) { echo " checked"; } ?> value="Disable" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Turn off the Summary View entirely."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Disable Summary</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Time Graph".',') !== false) { echo " checked"; } ?> value="Time Graph" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display a graph for the estimates vs tracked time for <?= TICKET_TILE ?> for the current user for the current day."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>My Time Graph</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Estimated".',') !== false) { echo " checked"; } ?> value="Estimated" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the total estimated time for all <?= TICKET_TILE ?> assigned to the current user for the current day."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>My Estimated Time Today</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Tracked".',') !== false) { echo " checked"; } ?> value="Tracked" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the total time worked for all <?= TICKET_TILE ?> for the current user for the current day."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>My Tracked Time Today</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Today".',') !== false) { echo " checked"; } ?> value="Today" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display all <?= TICKET_TILE ?> assigned to the current user for the current day."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>My <?= TICKET_TILE ?> For Today</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Day Overview".',') !== false) { echo " checked"; } ?> value="Day Overview" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Displays all <?= TICKET_TILE ?> for the day."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Day Overview</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Week Overview".',') !== false) { echo " checked"; } ?> value="Week Overview" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Displays all <?= TICKET_TILE ?> for the week."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Week Overview</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Month Overview".',') !== false) { echo " checked"; } ?> value="Month Overview" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Displays all <?= TICKET_TILE ?> for the month."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Month Overview</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Business".',') !== false) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by <?= BUSINESS_CAT ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> Per <?= BUSINESS_CAT ?></label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Contact".',') !== false) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by Contact."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> Per <?= get_config($dbc, 'ticket_project_contact') ?: 'Contact' ?></label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Created".',') !== false) { echo " checked"; } ?> value="Created" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by who created them."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> Created By Staff</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Assigned".',') !== false) { echo " checked"; } ?> value="Assigned" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by who is assigned to them."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> Assigned To Staff</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Project".',') !== false) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by <?= PROJECT_NOUN ?> Tab."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> By <?= PROJECT_NOUN ?> Type</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Project ID".',') !== false) { echo " checked"; } ?> value="Project ID" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by <?= PROJECT_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> By <?= PROJECT_NOUN ?></label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Status".',') !== false) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by Status."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> By Status</label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Top 25 All".',') !== false) { echo " checked"; } ?> value="Top 25 All" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the last 25 created <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Last 25 <?= TICKET_TILE ?></label>
		<?php foreach($ticket_tabs as $type => $label) { ?>
			<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ",Top 25 $type,") !== false) { echo " checked"; } ?> value="Top 25 <?= $type ?>" style="height: 20px; width: 20px;" name="tickets_summary[]">
				<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the last 25 created <?= $label.' '.TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Last 25 <?= $label ?></label>
		<?php } ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Top 25 Forms".',') !== false) { echo " checked"; } ?> value="Top 25 Forms" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the last 25 created custom <?= TICKET_NOUN ?> forms."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Last 25 Forms</label>
		<?php $forms = $dbc->query("SELECT `pdf_name`, `id` FROM `ticket_pdf` ORDER BY `pdf_name`");
		while($form = $forms->fetch_assoc()) { ?>
			<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Top 25 Form ".$form['id'].',') !== false) { echo " checked"; } ?> value="Top 25 Form <?= $form['id'] ?>" style="height: 20px; width: 20px;" name="tickets_summary[]">
				<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the last 25 created <?= $form['pdf_name'] ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Last 25 <?= $form['pdf_name'] ?></label>
		<?php } ?>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_summary, ','."Mine".',') !== false) { echo " checked"; } ?> value="Mine" style="height: 20px; width: 20px;" name="tickets_summary[]">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display all <?= TICKET_TILE ?> assigned to the current user."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>All My <?= TICKET_TILE ?></label>
	</div>
</div>
<div class="form-group">
	<label class="col-sm-4 control-label">
		<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to set default fields when importing <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Export Options:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Export Business".',') !== false) { echo " checked"; } ?> value="Export Business" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Import <?= TICKET_NOUN ?> to <?= BUSINESS_CAT ?></label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Export Project".',') !== false) { echo " checked"; } ?> value="Export Project" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Import <?= TICKET_NOUN ?> to <?= PROJECT_NOUN ?></label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Export Equipment".',') !== false) { echo " checked"; } ?> value="Export Equipment" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Import <?= TICKET_NOUN ?> to <?= Equipment ?></label>
		<label class="form-checkbox"><input type="checkbox" <?php if (strpos($db_config, ','."Export Date".',') !== false) { echo " checked"; } ?> value="Export Date" style="height: 20px; width: 20px;" name="tickets_dashboard[]"> Import <?= TICKET_NOUN ?> to Date</label>
	</div>
</div>