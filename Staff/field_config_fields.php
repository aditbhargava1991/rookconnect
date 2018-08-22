<?php if($_GET['staff_category'] == 'All Staff') {
	$_GET['staff_category'] = '';
}
$staff_cat = $_GET['staff_category']; ?>
<script>
$(document).ready(function() {
	sortSections();
	$('#field_accordions input').change(function() {
		var tab = $(this).closest('.panel-body').data('tab');
		var subtab = $(this).closest('.panel-body').data('subtab');
		var accordion = $(this).closest('.panel-body').data('accordion');
		var field = this.name;
		var value = this.value;
		if(field == 'contacts') {
			var values = [];
			$(this).closest('.panel-body').find('input:checked:not(:disabled)').each(function() {
				values.push(this.value);
			});
			value = ','+values.join(',')+',';
		}
		$.ajax({
			url: 'staff_ajax.php?action=field_config',
			method: 'POST',
			data: {
				tab: tab,
				subtab: subtab,
				accordion: accordion,
				field: field,
				value: value
			},
			success: function(response) {
				console.log(response);
			}
		});
	});
	$('[name="staff_category"]').change(function() {
		window.location.href = "?settings=fields&staff_category="+this.value;
	});
});
function addSection(subtab) {
	$.ajax({
		url: 'staff_ajax.php?action=add_section',
		method: 'POST',
		data: {
			tab: 'Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>',
			subtab: subtab
		},
		success: function(response) {
			window.location.reload();
		}
	});
}
function deleteSection(configcontactid, img) {
	if(confirm('Are you sure you want to delete this section?')) {
		$.ajax({
			url: 'staff_ajax.php?action=delete_section',
			method: 'POST',
			data: {
				configcontactid: configcontactid
			},
			success: function(response) {
				$(img).closest('.panel').remove();
			}
		});
	}
}
function sortSections() {
	var field_order = [];
	var counter = 0;
	$('#field_accordions [name="configcontactid_order[]"]').each(function() {
		var configcontactid = $(this).val();
		field_order[counter] = configcontactid;
		counter++;
	});
	field_order = JSON.stringify(field_order);
	$.ajax({
		url: 'staff_ajax.php?action=sort_fields',
		type: 'POST',
		data: { field_order: field_order },
		success: function(response) {
			// console.log(response);
		}
	});
}
</script>
<?php $staff_categories = array_filter(explode(',',str_replace(',,',',',str_replace('Staff','',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT categories FROM field_config_contacts WHERE tab='Staff' AND `categories` IS NOT NULL"))['categories']))));
if(!empty($staff_categories)) { ?>
	<div class="form-group">
		<label class="col-sm-4 control-label">Staff Category:</label>
		<div class="col-sm-8">
			<select name="staff_category" class="chosen-select-deselect">
				<option>All Staff</option>
				<?php foreach($staff_categories as $staff_category) { ?>
					<option value="<?= config_safe_str($staff_category) ?>" <?= $staff_cat == config_safe_str($staff_category) ? 'selected' : '' ?>><?= $staff_category ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
<?php }
$staff_field_subtabs = ','.get_config($dbc, 'staff_field_subtabs').',';
$i = 0; ?>
<div id="field_accordions" class="standard-body-content panel-group col-xs-12">
	<?php if(strpos($staff_field_subtabs, ',Staff Information,') !== false) {
		$subtab = 'staff_information'; ?>
		<h3>Profile<button class="btn brand-btn small pull-right" onclick="addSection('staff_information');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='staff_information' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='staff_information' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='staff_information' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
								<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="staff_information" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='staff_information' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Staff Bio,') !== false) {
		$subtab = 'staff_bio'; ?>
		<h3>Staff Bio<button class="btn brand-btn small pull-right" onclick="addSection('staff_bio');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='staff_bio' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='staff_bio' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='staff_bio' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
								<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="staff_bio" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='staff_bio' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Staff Address,') !== false) {
		$subtab = 'staff_address'; ?>
		<h3>Staff Address<button class="btn brand-btn small pull-right" onclick="addSection('staff_address');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='staff_address' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='staff_address' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='staff_address' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="staff_address" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='staff_address' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Position,') !== false) {
		$subtab = 'position'; ?>
		<h3>Positions<button class="btn brand-btn small pull-right" onclick="addSection('position');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='position' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='position' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='position' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="position" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='position' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Employee Information,') !== false) {
		$subtab = 'employee_information'; ?>
		<h3>Employee Information<button class="btn brand-btn small pull-right" onclick="addSection('employee_information');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='employee_information' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='employee_information' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='employee_information' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="employee_information" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='employee_information' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Driver Information,') !== false) {
		$subtab = 'driver_information'; ?>
		<h3>Driver Information<button class="btn brand-btn small pull-right" onclick="addSection('driver_information');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='driver_information' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='driver_information' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='driver_information' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="driver_information" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='driver_information' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Direct Deposit Information,') !== false) {
		$subtab = 'direct_deposit_information'; ?>
		<h3>Direct Deposit Information<button class="btn brand-btn small pull-right" onclick="addSection('direct_deposit_information');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='direct_deposit_information' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='direct_deposit_information' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='direct_deposit_information' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="direct_deposit_information" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='direct_deposit_information' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php $subtab = 'software_id'; ?>
	<h3>Software ID<button class="btn brand-btn small pull-right" onclick="addSection('software_id');">Add Section</button></h3>
	<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='software_id' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='software_id' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='software_id' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
	while($accordion = mysqli_fetch_assoc($accordions)) { ?>
		<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
						<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
					<?php } ?>
					<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
						<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
				<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="software_id" data-accordion="<?= $accordion['accordion'] ?>">
					<?php $contacts_config = $accordion['contacts'];
					if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
						$main_contacts_config = $contacts_config;
						$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='software_id' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
					} else if(!empty($staff_cat)) {
						$main_contacts_config = '';
					}
					include('config_field_list.php'); ?>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php $subtab = 'software_access'; ?>
	<h3>Software Access<button class="btn brand-btn small pull-right" onclick="addSection('software_access');">Add Section</button></h3>
	<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='software_access' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='software_access' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='software_access' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
	while($accordion = mysqli_fetch_assoc($accordions)) { ?>
		<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
						<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
					<?php } ?>
					<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
						<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
				<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="software_access" data-accordion="<?= $accordion['accordion'] ?>">
					<?php $contacts_config = $accordion['contacts'];
					if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
						$main_contacts_config = $contacts_config;
						$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='software_access' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
					} else if(!empty($staff_cat)) {
						$main_contacts_config = '';
					}
					include('config_field_list.php'); ?>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Social Media,') !== false) {
		$subtab = 'social_media'; ?>
		<h3>Social Media<button class="btn brand-btn small pull-right" onclick="addSection('social_media');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='social_media' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='social_media' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='social_media' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="social_media" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='social_media' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Emergency,') !== false) {
		$subtab = 'emergency'; ?>
		<h3>Emergency<button class="btn brand-btn small pull-right" onclick="addSection('emergency');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='emergency' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='emergency' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='emergency' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="emergency" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='emergency' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Health,') !== false) {
		$subtab = 'health'; ?>
		<h3>Health Care<button class="btn brand-btn small pull-right" onclick="addSection('health');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='health' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='health' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='health' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="health" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='health' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Health Concerns,') !== false) {
		$subtab = 'health_concerns'; ?>
		<h3>Health Concerns<button class="btn brand-btn small pull-right" onclick="addSection('health_concerns');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='health_concerns' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='health_concerns' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='health_concerns' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="health_concerns" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='health_concerns' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Allergies,') !== false) {
		$subtab = 'allergies'; ?>
		<h3>Allrgies<button class="btn brand-btn small pull-right" onclick="addSection('health_concerns');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='allergies' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='allergies' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='allergies' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="allergies" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='allergies' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Company Benefits,') !== false) {
		$subtab = 'company_benefits'; ?>
		<h3>Company Benefits<button class="btn brand-btn small pull-right" onclick="addSection('health_concerns');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='company_benefits' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='company_benefits' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='company_benefits' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="company_benefits" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='company_benefits' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Schedule,') !== false) {
		$subtab = 'schedule'; ?>
		<h3>Staff Schedule<button class="btn brand-btn small pull-right" onclick="addSection('schedule');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='schedule' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='schedule' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='schedule' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="schedule" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='schedule' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',HR,') !== false) {
		$subtab = 'hr'; ?>
		<h3>HR Record<button class="btn brand-btn small pull-right" onclick="addSection('hr');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='hr' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='hr' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='hr' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="hr" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='hr' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Staff Documents,') !== false) {
		$subtab = 'staff_docs'; ?>
		<h3>Staff Documents<button class="btn brand-btn small pull-right" onclick="addSection('staff_docs');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='staff_docs' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='staff_docs' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='staff_docs' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="staff_docs" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='staff_docs' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($staff_field_subtabs, ',Incident Reports,') !== false) {
		$subtab = 'incident_reports'; ?>
		<h3><?= INC_REP_TILE ?><button class="btn brand-btn small pull-right" onclick="addSection('incident_reports');">Add Section</button></h3>
		<?php $accordions = mysqli_query($dbc, "SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='incident_reports' ORDER BY IFNULL(`order`,`configcontactid`) ASC) `main_table` UNION SELECT * FROM (SELECT `configcontactid`, `accordion`, `contacts`, `tab` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='incident_reports' AND '$staff_cat' != '' AND `accordion` NOT IN (SELECT DISTINCT `accordion` FROM `field_config_contacts` WHERE `tab`='Staff' AND `subtab`='incident_reports' AND IFNULL(`accordion`,'') != '') ORDER BY IFNULL(`order`,`configcontactid`) ASC) `cat_table`");
		while($accordion = mysqli_fetch_assoc($accordions)) { ?>
			<input type="hidden" name="configcontactid_order[]" value="<?= $accordion['configcontactid'] ?>">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<?php if(empty($staff_cat) || $accordion['tab'] != 'Staff') { ?>
							<img src="../img/remove.png" onclick="deleteSection('<?= $accordion['configcontactid'] ?>', this);" style="cursor: pointer; margin: 0px; height: 1em; min-height: 1em;">&nbsp;&nbsp;
						<?php } ?>
						<a data-toggle="collapse" data-parent="#field_accordions" href="#collapse_fields_<?= $i ?>">
							<?= $accordion['accordion'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_fields_<?= $i++ ?>" class="panel-collapse collapse">
					<div class="panel-body" data-tab="Staff<?= !empty($staff_cat) ? '_'.$staff_cat : '' ?>" data-subtab="incident_reports" data-accordion="<?= $accordion['accordion'] ?>">
						<?php $contacts_config = $accordion['contacts'];
						if(!empty($staff_cat) && $accordion['tab'] == 'Staff') {
							$main_contacts_config = $contacts_config;
							$contacts_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Staff_$staff_cat' AND `subtab`='incident_reports' AND `accordion`='".$accordion['accordion']."'"))['contacts'];
						} else if(!empty($staff_cat)) {
							$main_contacts_config = '';
						}
						include('config_field_list.php'); ?>
					</div>
				</div>
			</div>
		<?php } ?>
	<?php } ?>
</div>