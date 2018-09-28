<div class="form-group">
	<label class="col-sm-4">Security Level / User:</label>
	<div class="col-sm-8">
		<select class="chosen-select-deselect" data-placeholder="Select Security Level or User" onchange="window.location.replace('?settings=summary_security&security='+this.value);">
			<option />
			<?php foreach(get_security_levels($dbc) as $sec_level_name => $sec_level) {
				$level_name = 'seclevel_'.config_safe_str($sec_level); ?>
				<option <?= $_GET['security'] == $level_name ? 'selected' : '' ?> value="<?= $level_name ?>"><?= $sec_level_name ?></option>
			<?php } ?>
			<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`, `first_name`, `last_name`, `name` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND IFNULL(`user_name`,'') != ''")) as $user) { ?>
				<option <?= $_GET['security'] == 'userid_'.$user['contactid'] ? 'selected' : '' ?> value="userid_<?= $user['contactid'] ?>"><?= $user['full_name'] ?></option>
			<?php } ?>
		</select>
	</div>
</div>
<?php if(!empty($_GET['security'])) { ?>
	<?php $db_summary = ','.get_config($dbc, 'tickets_summary').',';
	$security = filter_var($_GET['security'],FILTER_SANITIZE_STRING);
	$access = ','.get_config($dbc, 'ticket_summary_'.$security).','; ?>
	<script>
	$(document).ready(function() {
		$('.form-group label [type=checkbox],[name=slider_button]').change(saveFields);
	});
	function saveFields() {
		var tickets_summary = [];
		$('[name="tickets_summary[]"]:checked').each(function() {
			tickets_summary.push(this.value);
		});
		$.ajax({
			url: 'ticket_ajax_all.php?action=ticket_summary_security',
			method: 'POST',
			data: {
				security: 'ticket_summary_<?= $security ?>',
				summary: tickets_summary
			}
		});
	}
	</script>
	<div class="form-group">
		<label class="col-sm-4 control-label">
			<span class="popover-examples"><a data-toggle="tooltip" data-original-title="These settings will enable a special summary view for the <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Summary View:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Disable".',') !== false) { echo " checked"; } ?> value="Disable" style="height: 20px; width: 20px;" name="tickets_summary[]">
				<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Turn off the Summary View entirely."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Disable Summary</label>
			<?php if(strpos($db_summary,'Estimated') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Estimated".',') !== false) { echo " checked"; } ?> value="Estimated" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the total estimated time for all <?= TICKET_TILE ?> assigned to the current user for the current day."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>My Estimated Time Today</label>
			<?php } ?>
			<?php if(strpos($db_summary,'Tracked') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Tracked".',') !== false) { echo " checked"; } ?> value="Tracked" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the total time worked for all <?= TICKET_TILE ?> for the current user for the current day."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>My Tracked Time Today</label>
			<?php } ?>
			<?php if(strpos($db_summary,'Today') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Today".',') !== false) { echo " checked"; } ?> value="Today" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display all <?= TICKET_TILE ?> assigned to the current user for the current day."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>My <?= TICKET_TILE ?> For Today</label>
			<?php } ?>
			<?php if(strpos($db_summary,'Day Overview') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Day Overview".',') !== false) { echo " checked"; } ?> value="Day Overview" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Displays all <?= TICKET_TILE ?> for the day."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Day Overview</label>
			<?php } ?>
			<?php if(strpos($db_summary,'Week Overview') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Week Overview".',') !== false) { echo " checked"; } ?> value="Week Overview" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Displays all <?= TICKET_TILE ?> for the week."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Week Overview</label>
			<?php } ?>
			<?php if(strpos($db_summary,'Month Overview') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Month Overview".',') !== false) { echo " checked"; } ?> value="Month Overview" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Displays all <?= TICKET_TILE ?> for the month."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Month Overview</label>
			<?php } ?>
			<?php if(strpos($db_summary,'Business') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Business".',') !== false) { echo " checked"; } ?> value="Business" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by <?= BUSINESS_CAT ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> Per <?= BUSINESS_CAT ?></label>
			<?php } ?>
			<?php if(strpos($db_summary,'Contact') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Contact".',') !== false) { echo " checked"; } ?> value="Contact" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by Contact."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> Per <?= get_config($dbc, 'ticket_project_contact') ?: 'Contact' ?></label>
			<?php } ?>
			<?php if(strpos($db_summary,'Created') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Created".',') !== false) { echo " checked"; } ?> value="Created" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by who created them."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> Created By Staff</label>
			<?php } ?>
			<?php if(strpos($db_summary,'Assigned') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Assigned".',') !== false) { echo " checked"; } ?> value="Assigned" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by who is assigned to them."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> Assigned To Staff</label>
			<?php } ?>
			<?php if(strpos($db_summary,'Project') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Project".',') !== false) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by <?= PROJECT_NOUN ?> Tab."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> By <?= PROJECT_NOUN ?> Type</label>
			<?php } ?>
			<?php if(strpos($db_summary,'Project ID') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Project".',') !== false) { echo " checked"; } ?> value="Project" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by <?= PROJECT_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> By <?= PROJECT_NOUN ?></label>
			<?php } ?>
			<?php if(strpos($db_summary,'Status') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Status".',') !== false) { echo " checked"; } ?> value="Status" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the number of <?= TICKET_TILE ?> by Status."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_TILE ?> By Status</label>
			<?php } ?>
			<?php if(strpos($db_summary,'Top 25 All') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Top 25 All".',') !== false) { echo " checked"; } ?> value="Top 25 All" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the last 25 created <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Last 25 <?= TICKET_TILE ?></label>
			<?php } ?>
			<?php foreach($ticket_tabs as $type => $label) { ?>
				<?php if(strpos($db_summary,"Top 25 $type") !== FALSE) { ?>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ",Top 25 $type,") !== false) { echo " checked"; } ?> value="Top 25 <?= $type ?>" style="height: 20px; width: 20px;" name="tickets_summary[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the last 25 created <?= $label.' '.TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Last 25 <?= $label ?></label>
				<?php } ?>
			<?php } ?>
			<?php if(strpos($db_summary,'Top 25 Forms') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Top 25 Forms".',') !== false) { echo " checked"; } ?> value="Top 25 Forms" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the last 25 created custom <?= TICKET_NOUN ?> forms."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Last 25 Forms</label>
			<?php } ?>
			<?php $forms = $dbc->query("SELECT `pdf_name`, `id` FROM `ticket_pdf` ORDER BY `pdf_name`");
			while($form = $forms->fetch_assoc()) { ?>
				<?php if(strpos($db_summary,'Top 25 Form '.$form['id']) !== FALSE) { ?>
					<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Top 25 Form ".$form['id'].',') !== false) { echo " checked"; } ?> value="Top 25 Form <?= $form['id'] ?>" style="height: 20px; width: 20px;" name="tickets_summary[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the last 25 created <?= $form['pdf_name'] ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Last 25 <?= $form['pdf_name'] ?></label>
				<?php } ?>
			<?php } ?>
			<?php if(strpos($db_summary,'Mine') !== FALSE) { ?>
				<label class="form-checkbox"><input type="checkbox" <?php if (strpos($access, ','."Mine".',') !== false) { echo " checked"; } ?> value="Mine" style="height: 20px; width: 20px;" name="tickets_summary[]">
					<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display all <?= TICKET_TILE ?> assigned to the current user."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>All My <?= TICKET_TILE ?></label>
			<?php } ?>
		</div>
	</div>
<?php } ?>