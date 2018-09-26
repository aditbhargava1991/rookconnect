<?php
if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) {
	if(in_array('TEMPLATE Work Ticket', $all_mandatory_config) || in_array('TEMPLATE Work Ticket', $value_config)) {
		$value_config = ['TEMPLATE Work Ticket'];
		$all_config = ['Information','PI Business','PI Name','PI Project','PI AFE','PI Sites','Staff','Staff Position','Staff Hours','Staff Overtime','Staff Travel','Staff Subsistence','Services','Service Category','Equipment','Materials','Material Quantity','Material Rates','Purchase Orders','Notes'];
	}
	if(in_array('Documents',$value_config) && !in_array('Documents Docs',$value_config) && !in_array('Documents Links',$value_config)) {
		$value_config[] = 'Documents Docs';
		$value_config[] = 'Documents Links';
	} ?>
	<div class="form-group">
<?php } else { ?>
	<div class="form-group">
		<h4 class="double-gap-top"><?= TICKET_NOUN ?> Functionality</h4>
		<?php if(in_array('Multiple', $merged_config_fields)) { ?>
			<label class="form-checkbox"><input type="checkbox" <?= in_array("Multiple", $all_mandatory_config) ? 'checked disabled' : (in_array("Multiple", $value_mandatory_config) ? "checked" : '') ?> value="Multiple" name="tickets[]"> Create Multiple <?= TICKET_TILE ?></label>
		<?php } ?>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("Hide Trash Icon", $all_mandatory_config) ? 'checked disabled' : (in_array("Hide Trash Icon", $value_mandatory_config) ? "checked" : '') ?> value="Hide Trash Icon" name="tickets[]"> Hide Trash Icon</label>
	</div>
<?php } ?>

<h4 class="double-gap-top"><?= TICKET_NOUN ?><?= $action_mode ? ' Action Mode' : ($overview_mode ? ' Overview' : ($unlock_mode ? ' Unlocked' : ($status_fields ? 'Status' : ''))) ?> Fields</h4>

<div class="accordions_sortable">
	<?php $current_heading = '';
	$current_heading_closed = true;
	$sort_order = array_filter($sort_order);
	foreach ($sort_order as $sort_field) {
		//Add higher level heading
		$this_heading = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_headings` WHERE `ticket_type` = '".(empty($tab) ? 'tickets' : 'tickets_'.$tab)."' AND `accordion` = '".$sort_field."'"))['heading'];
		if($this_heading != $current_heading) {
			if(!$current_heading_closed) { ?>
					</div>
				</div>
				<?php $current_heading_closed = true;
			}
			if(!empty($this_heading)) { ?>
				<div class="sort_order_heading sort_order_accordion">
					<div class="sort_order_heading_name">
						<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?><img src="../img/remove.png" class="inline-img" onclick="removeHigherLevelHeading(this);"><?php } ?><label class="control-label">Heading: </label><input type="text" name="sort_order_heading[]" value="<?= $this_heading ?>" class="inline form-control gap-left" onchange="updateHigherLevelHeading(this);" onfocusin="$(this).data('oldvalue', $(this).val());" <?php if($action_mode || $status_fields || $overview_mode || $unlock_mode) { echo 'disabled'; } ?>>
					</div>
					<div class="block-group sort_order_heading_block">
				<?php $current_heading_closed = false;
				$current_heading = $this_heading;
			}
		}

		//Custom accordions
		if(substr($sort_field, 0, strlen('FFMCUST_')) === 'FFMCUST_') {
			include('../Ticket/field_config_field_list_custom.php');
	 	}

		$field_list = $accordion_list[$sort_field];
		$field_sort_order = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_fields` WHERE `ticket_type` = '".(empty($tab) ? 'tickets' : 'tickets_'.$tab)."' AND `accordion` = '".$sort_field."'"))['fields'];
		$field_sort_order = explode(',', $field_sort_order);
		foreach ($field_list as $default_field) {
			if(!in_array($default_field, $field_sort_order)) {
				$field_sort_order[] = $default_field;
			}
		}

		if($action_mode || $status_fields || $overview_mode) {
			$field_sort_order = array_intersect($field_sort_order, array_merge($all_config_fields,$value_config_fields));
		} else if($unlock_mode) {
			$field_sort_order = $sort_order;
		}

		//Renamed accordions
		$renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($tab) ? 'tickets' : 'tickets_'.$tab)."' AND `accordion` = '".$sort_field."'"))['accordion_name'];

		if($sort_field == 'Customer History') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Customer History">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Customer History Button' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('project_info',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('project_info',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="project_info" data-toggle="<?= in_array('project_info',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('project_info',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('project_info',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Customer History Button' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Information') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Information">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : PROJECT_NOUN.' Information' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('project_info',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('project_info',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="project_info" data-toggle="<?= in_array('project_info',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('project_info',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('project_info',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : PROJECT_NOUN.' Information' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<div class="block-group">
						<div class="fields_sortable">
						<?php
						$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
						?>
						<?php foreach ($selected_field_order as $selected_field_field) { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }
		if($sort_field == 'Details') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Details">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : PROJECT_NOUN.' Details' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('project_details',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('project_details',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="project_details" data-toggle="<?= in_array('project_details',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('project_details',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('project_details',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : PROJECT_NOUN.' Details' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Location') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Location">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Sites' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_location',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_location',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_location" data-toggle="<?= in_array('ticket_location',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_location',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_location',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Sites' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Members ID') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Members ID">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Members ID Card' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_members_id_card',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_members_id_card',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_members_id_card" data-toggle="<?= in_array('ticket_members_id_card',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_members_id_card',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_members_id_card',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Members ID Card' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Members ID", $all_mandatory_config) ? 'checked disabled' : (in_array("Members ID", $value_mandatory_config) ? "checked" : '') ?> value="Members ID" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Display the list of Members for the <?= TICKET_NOUN ?>, and select details to display for that Member. Details added here will be added to the Member's profile."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Mileage') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Mileage">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Mileage' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_mileage',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_mileage',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_mileage" data-toggle="<?= in_array('ticket_mileage',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_mileage',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_mileage',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Mileage' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<?php if((!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) || in_array('Mileage', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Mileage", $all_mandatory_config) ? 'checked disabled' : (in_array("Mileage", $value_mandatory_config) ? "checked" : '') ?> value="Mileage" name="tickets[]">
							<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to track mileage for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable Mileage</label>
					<?php } ?>
					<?php if((!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) || in_array('Drive Time', $merged_config_fields)) { ?>
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Drive Time", $all_mandatory_config) ? 'checked disabled' : (in_array("Drive Time", $value_mandatory_config) ? "checked" : '') ?> value="Drive Time" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to track driving time for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable Drive Time</label>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Staff') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Staff">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_staff_list',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_staff_list',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_staff_list" data-toggle="<?= in_array('ticket_staff_list',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_staff_list',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_staff_list',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Staff", $all_mandatory_config) ? 'checked disabled' : (in_array("Staff", $value_mandatory_config) ? "checked" : '') ?> value="Staff" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify Staff for the <?= TICKET_NOUN ?>, and add details about what they are doing."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Staff Tasks') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Staff Tasks">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff by Task' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_staff_assign_tasks',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_staff_assign_tasks',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_staff_assign_tasks" data-toggle="<?= in_array('ticket_staff_assign_tasks',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_staff_assign_tasks',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_staff_assign_tasks',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff by Task' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Staff Tasks", $all_mandatory_config) ? 'checked disabled' : (in_array("Staff Tasks", $value_mandatory_config) ? "checked" : '') ?> value="Staff Tasks" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add Staff to the <?= TICKET_NOUN ?>, and assign them custom Tasks."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>
						<div class="block-group">
							<?php
							$field_sort_order = array("Ticket Tasks Add Button", "Ticket Tasks Auto Check In", "Ticket Tasks Auto Load New", "Ticket Tasks Projects", "Ticket Tasks Ticket Type", "Ticket Tasks Groups", "Task Extra Billing", "Extra Billing Create New");
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Members') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Members">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Members' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_members',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_members',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_members" data-toggle="<?= in_array('ticket_members',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_members',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_members',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Members' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Members", $all_mandatory_config) ? 'checked disabled' : (in_array("Members", $value_mandatory_config) ? "checked" : '') ?> value="Members" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to attach Members to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Clients') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Clients">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Clients' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_clients',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_clients',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_clients" data-toggle="<?= in_array('ticket_clients',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_clients',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_clients',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Clients' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Clients", $all_mandatory_config) ? 'checked disabled' : (in_array("Clients", $value_mandatory_config) ? "checked" : '') ?> value="Clients" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to attach a category of <?= CONTACTS_TILE ?> to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
						<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>
							<div class="form-group">
								<?php $client_accordion_category = get_config($dbc, 'client_accordion_category'); ?>
								<label class="col-sm-4 control-label">Contact Category for <?= !empty($renamed_accordion) ? $renamed_accordion : 'Clients' ?> Accordion<?= $client_accordion_category != '' && $tab != '' ? ' (Default: '.$client_accordion_category.')' : '' ?>:</label>
								<div class="col-sm-8">
									<select name="client_accordion_category<?= $tab == '' ? '' : '_'.$tab ?>" data-placeholder="Select Category" class="chosen-select-deselect"><option></option>
										<?php $tab_client_accordion_category = get_config($dbc, 'client_accordion_category'.($tab == '' ? '' : '_'.$tab));
										foreach(explode(',',get_config($dbc, 'all_contact_tabs')) as $category) { ?>
											<option <?= $category == $tab_client_accordion_category ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
										<?php } ?>
									</select>
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Wait List') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Wait List">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Wait List' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_wait_list',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_wait_list',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_wait_list" data-toggle="<?= in_array('ticket_wait_list',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_wait_list',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_wait_list',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Wait List' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Wait List", $all_mandatory_config) ? 'checked disabled' : (in_array("Wait List", $value_mandatory_config) ? "checked" : '') ?> value="Wait List" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add additional individuals to the <?= TICKET_NOUN ?> that are just on a wait list and do not affect the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Check In') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Check In">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Check In' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_checkin',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_checkin',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_checkin" data-toggle="<?= in_array('ticket_checkin',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_checkin',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_checkin',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Check In' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<?php if((!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) || in_array('Check In', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Check In", $all_mandatory_config) ? 'checked disabled' : (in_array("Check In", $value_mandatory_config) ? "checked" : '') ?> value="Check In" name="tickets[]">
							<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to mark individuals, equipment, or other supplies as ready in the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable Check In</label>
					<?php } ?>

					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Medication') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Medication">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Medication Administration' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_medications',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_medications',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_medications" data-toggle="<?= in_array('ticket_medications',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_medications',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_medications',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Medication Administration' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Medication", $all_mandatory_config) ? 'checked disabled' : (in_array("Medication", $value_mandatory_config) ? "checked" : '') ?> value="Medication" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to manage Medication for Members attached to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>
						<div class="block-group">
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Medication Multiple Days", $all_mandatory_config) ? 'checked disabled' : (in_array("Medication Multiple Days", $value_mandatory_config) ? "checked" : '') ?> value="Medication Multiple Days" name="tickets[]"> Multiple Days</label>
							<label class="form-checkbox"><input type="checkbox" <?= in_array("Medication Group Days", $all_mandatory_config) ? 'checked disabled' : (in_array("Medication Group Days", $value_mandatory_config) ? "checked" : '') ?> value="Medication Group Days" name="tickets[]"> Group Days In Accordion</label>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Deliverables') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Deliverables">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Deliverables' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_deliverables',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_deliverables',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_deliverables" data-toggle="<?= in_array('view_ticket_deliverables',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_deliverables',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_deliverables',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Deliverables' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Deliverables", $all_mandatory_config) ? 'checked disabled' : (in_array("Deliverables", $value_mandatory_config) ? "checked" : '') ?> value="Deliverables" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify users, dates, and statuses for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Ticket Details' || ($sort_field == 'Services' && !in_array('Ticket Details', $sort_order))) { ?>
			<div class="form-group sort_order_accordion" data-accordion="Ticket Details">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : TICKET_NOUN.' Details / Services' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_info',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_info',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_info" data-toggle="<?= in_array('ticket_info',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_info',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_info',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : TICKET_NOUN.' Details / Services' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">


					<div class="block-group">
						<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>

						<?php } ?>
						<div class="fields_sortable">
						<?php
						$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
						?>
						<?php foreach ($selected_field_order as $selected_field_field) {
							if($selected_field_field != '' || $selected_field_field != null) { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
						<?php }
						} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Service Staff Checklist') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Service Staff Checklist">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Service Checklist' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_service_checklist',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_service_checklist',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_service_checklist" data-toggle="<?= in_array('ticket_service_checklist',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_service_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_service_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Service Checklist' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Service Staff Checklist", $all_mandatory_config) ? 'checked disabled' : (in_array("Service Staff Checklist", $value_mandatory_config) ? "checked" : '') ?> value="Service Staff Checklist" name="tickets[]"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to display a Checklist of the Services in the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>
						<div class="block-group">
							<div class="fields_sortable">
								<?php
								$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
								?>
								<?php foreach ($selected_field_order as $selected_field_field) {
									if($selected_field_field != '' || $selected_field_field != null) { ?>
										<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
										<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
								<?php }
								} ?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Service Extra Billing') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Service Extra Billing">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Service Extra Billing' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_service_checklist',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_service_checklist',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_service_checklist" data-toggle="<?= in_array('ticket_service_checklist',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_service_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_service_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Service Extra Billing' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Service Extra Billing", $all_mandatory_config) ? 'checked disabled' : (in_array("Service Extra Billing", $value_mandatory_config) ? "checked" : '') ?> value="Service Extra Billing" name="tickets[]"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to view all of your Service's Extra Billing added."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>
						<div class="block-group">
							<div class="fields_sortable">
								<?php
								$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
								?>
								<?php foreach ($selected_field_order as $selected_field_field) {
									if($selected_field_field != '' || $selected_field_field != null) { ?>
										<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
										<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
								<?php }
								} ?>
							</div>
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Equipment') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Equipment">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Equipment' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_equipment',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_equipment',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_equipment" data-toggle="<?= in_array('ticket_equipment',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_equipment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_equipment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Equipment' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Equipment", $all_mandatory_config) ? 'checked disabled' : (in_array("Equipment", $value_mandatory_config) ? "checked" : '') ?> value="Equipment" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to pull from the Equipment Tile for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Checklist') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Checklist">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Checklist' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_checklist',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_checklist',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_checklist" data-toggle="<?= in_array('ticket_checklist',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Checklist' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Checklist", $all_mandatory_config) ? 'checked disabled' : (in_array("Checklist", $value_mandatory_config) ? "checked" : '') ?> value="Checklist" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to create a Checklist in the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Checklist Items') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Checklist Items">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Attached Checklists' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_view_checklist',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_view_checklist',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_view_checklist" data-toggle="<?= in_array('ticket_view_checklist',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_view_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_view_checklist',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Attached Checklists' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Checklist Items", $all_mandatory_config) ? 'checked disabled' : (in_array("Checklist Items", $value_mandatory_config) ? "checked" : '') ?> value="Checklist Items" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to display lists from the Checklists Tile in the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Charts') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Charts">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Charts' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_view_charts',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_view_charts',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_view_charts" data-toggle="<?= in_array('ticket_view_charts',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_view_charts',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_view_charts',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Charts' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Charts", $all_mandatory_config) ? 'checked disabled' : (in_array("Charts", $value_mandatory_config) ? "checked" : '') ?> value="Charts" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to attach forms from the Treatment Charts tile to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>

						<div class="block-group">
				</div>
			</div>
		<?php }

		if($sort_field == 'Safety') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Safety">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Safety' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_safety',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_safety',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_safety" data-toggle="<?= in_array('ticket_safety',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_safety',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_safety',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Safety' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Safety", $all_mandatory_config) ? 'checked disabled' : (in_array("Safety", $value_mandatory_config) ? "checked" : '') ?> value="Safety" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to pull details from the Safety tile into the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Timer') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Timer">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Timer' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_timer',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_timer',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_timer" data-toggle="<?= in_array('view_ticket_timer',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_timer',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_timer',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Timer' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Timer", $all_mandatory_config) ? 'checked disabled' : (in_array("Timer", $value_mandatory_config) ? "checked" : '') ?> value="Timer" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to track time to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Materials') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Materials">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Materials' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_materials',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_materials" data-toggle="<?= in_array('ticket_materials',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_materials',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_materials',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Materials' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Materials", $all_mandatory_config) ? 'checked disabled' : (in_array("Materials", $value_mandatory_config) ? "checked" : '') ?> value="Materials" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to pull from the Materials tile for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Quantity Increment:</label>
								<div class="col-sm-8">
									<input type="number" name="ticket_material_increment" class="form-control" step="0.01" min="0" value="<?= get_config($dbc, 'ticket_material_increment') ?>">
								</div>
							</div>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Location Details') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Location Details">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Location Details' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_residue',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_residue" data-toggle="<?= in_array('ticket_residue',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Location Details' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Location Details", $all_mandatory_config) ? 'checked disabled' : (in_array("Location Details", $value_mandatory_config) ? "checked" : '') ?> value="Location Details" name="tickets[]">Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Residue') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Residue">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Residue' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_residue',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_residue" data-toggle="<?= in_array('ticket_residue',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Residue' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Residue", $all_mandatory_config) ? 'checked disabled' : (in_array("Residue", $value_mandatory_config) ? "checked" : '') ?> value="Residue" name="tickets[]">Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Other List') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Other List">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Other Products' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_residue',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_residue" data-toggle="<?= in_array('ticket_residue',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Other Products' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Other List", $all_mandatory_config) ? 'checked disabled' : (in_array("Other List", $value_mandatory_config) ? "checked" : '') ?> value="Other List" name="tickets[]">Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Shipping List') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Shipping List">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Shipping List' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_residue',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_residue" data-toggle="<?= in_array('ticket_residue',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Shipping List' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Shipping List", $all_mandatory_config) ? 'checked disabled' : (in_array("Shipping List", $value_mandatory_config) ? "checked" : '') ?> value="Shipping List" name="tickets[]">Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Reading') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Reading">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Monitor Readings' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_residue',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_residue" data-toggle="<?= in_array('ticket_residue',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Monitor Readings' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Reading", $all_mandatory_config) ? 'checked disabled' : (in_array("Reading", $value_mandatory_config) ? "checked" : '') ?> value="Reading" name="tickets[]">Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Tank Reading') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Tank Reading">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Tank Readings' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_residue',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_materials',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_residue" data-toggle="<?= in_array('ticket_residue',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_residue',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Tank Readings' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Tank Reading", $all_mandatory_config) ? 'checked disabled' : (in_array("Tank Reading", $value_mandatory_config) ? "checked" : '') ?> value="Tank Reading" name="tickets[]">Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Miscellaneous') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Miscellaneous">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Miscellaneous' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_miscellaneous',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_miscellaneous',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_miscellaneous" data-toggle="<?= in_array('ticket_miscellaneous',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_miscellaneous',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_miscellaneous',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Miscellaneous' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Miscellaneous", $all_mandatory_config) ? 'checked disabled' : (in_array("Miscellaneous", $value_mandatory_config) ? "checked" : '') ?> value="Miscellaneous" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add Miscellaneous items to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Inventory') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Inventory">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Inventory' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_inventory',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_inventory',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_inventory" data-toggle="<?= in_array('ticket_inventory',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_inventory',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_inventory',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Inventory' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Inventory", $all_mandatory_config) ? 'checked disabled' : (in_array("Inventory", $value_mandatory_config) ? "checked" : '') ?> value="Inventory" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to pull from the <?= INVENTORY_TILE ?> tile for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>

						<?php } ?>
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Inventory General') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Inventory General">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'General Cargo / Inventory Information' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_inventory_general',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_inventory_general',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_inventory_general" data-toggle="<?= in_array('ticket_inventory_general',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_inventory_general',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_inventory_general',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'General Cargo / Inventory Information' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Inventory General", $all_mandatory_config) ? 'checked disabled' : (in_array("Inventory General", $value_mandatory_config) ? "checked" : '') ?> value="Inventory General" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify Cargo pieces for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
						<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>
							<div class="form-group">
								<label class="col-sm-4 control-label">Incomplete Inventory Reminder Email:</label>
								<div class="col-sm-8">
									<input type="text" class="form-control" name="incomplete_inventory_reminder_email" value="<?= get_config($dbc, 'incomplete_inventory_reminder_email') ?>">
								</div>
							</div>
						<?php } ?>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Inventory Detail') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Inventory Detail">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Detailed Cargo / Inventory Information' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_inventory_detailed',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_inventory_detailed',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_inventory_detailed" data-toggle="<?= in_array('ticket_inventory_detailed',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_inventory_detailed',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_inventory_detailed',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Detailed Cargo / Inventory Information' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Inventory Detail", $all_mandatory_config) ? 'checked disabled' : (in_array("Inventory Detail", $value_mandatory_config) ? "checked" : '') ?> value="Inventory Detail" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify detailed cargo for the <?= TICKET_NOUN ?> that will be synced into the <?= INVENTORY_NOUN ?> tile."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Inventory Return') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Inventory Return">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Return Information' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle smaller <?= in_array('ticket_inventory_return',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_inventory_return',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_inventory_return" data-toggle="<?= in_array('ticket_inventory_return',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_inventory_return',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_inventory_return',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Return Information' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Inventory Return", $all_mandatory_config) ? 'checked disabled' : (in_array("Inventory Return", $value_mandatory_config) ? "checked" : '') ?> value="Inventory Return" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify that specific cargo for the <?= TICKET_NOUN ?> is new or a returned item, and add additional details."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Purchase Orders') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Purchase Orders">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_purchase_orders',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_purchase_orders',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_purchase_orders" data-toggle="<?= in_array('ticket_purchase_orders',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_purchase_orders',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_purchase_orders',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Purchase Orders", $all_mandatory_config) ? 'checked disabled' : (in_array("Purchase Orders", $value_mandatory_config) ? "checked" : '') ?> value="Purchase Orders" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to create and attach Purchase Orders to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Attached Purchase Orders') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Attached Purchase Orders">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_purchase_orders',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_purchase_orders',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_purchase_orders" data-toggle="<?= in_array('ticket_purchase_orders',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_purchase_orders',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_purchase_orders',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Purchase Orders' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Attached Purchase Orders", $all_mandatory_config) ? 'checked disabled' : (in_array("Attached Purchase Orders", $value_mandatory_config) ? "checked" : '') ?> value="Attached Purchase Orders" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to create and attach Purchase Orders to the <?= TICKET_NOUN ?> from the Purchase Orders tile."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Delivery') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Delivery">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Delivery Details' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_delivery',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_delivery',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_delivery" data-toggle="<?= in_array('ticket_delivery',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_delivery',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_delivery',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Delivery Details' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Delivery", $all_mandatory_config) ? 'checked disabled' : (in_array("Delivery", $value_mandatory_config) ? "checked" : '') ?> value="Delivery" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add additional locations and times to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>
							<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array("Assigned Equipment Inline", $all_mandatory_config) ? 'checked disabled' : (in_array("Assigned Equipment Inline", $value_mandatory_config) ? "checked" : '') ?> value="Assigned Equipment Inline" name="tickets[]"> Inline Equipment</label>
						<?php } ?>
						<?php
						$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
						?>
						<?php foreach ($selected_field_order as $selected_field_field) {
							if($selected_field_field != '' || $selected_field_field != null) { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
						<?php }
						} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Transport') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Transport">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Transport Log' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Transport Log' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Transport", $all_mandatory_config) ? 'checked disabled' : (in_array("Transport", $value_mandatory_config) ? "checked" : '') ?> value="Transport" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify information about the source and the destination for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="transport_group">
							<?php $renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($tab) ? 'tickets' : 'tickets_'.$tab)."' AND `accordion` = 'Transport Origin'"))['accordion_name']; ?>

							<div class="col-sm-12 accordion_rename" style="display: none;" data-accordion="Transport Origin">
								<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Origin' ?>" onfocusout="updateAccordion(this);" class="form-control">
							</div>
							<div class="fields_sortable">
								<?php
								$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
								?>
								<?php foreach ($selected_field_order as $selected_field_field) {
									if($selected_field_field != '' || $selected_field_field != null) { ?>
										<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
										<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
								<?php }
								} ?>
							</div>
							<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>
								<div class="form-group">
									<?php $transport_log_contact = get_config($dbc, 'transport_log_contact'); ?>
								</div>
							<?php } ?>
						</div>
						<div class="transport_group">
							<?php $renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($tab) ? 'tickets' : 'tickets_'.$tab)."' AND `accordion` = 'Transport Destination'"))['accordion_name']; ?>

							<div class="fields_sortable">
								<?php
								$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
								?>
								<?php foreach ($selected_field_order as $selected_field_field) {
									if($selected_field_field != '' || $selected_field_field != null) { ?>
										<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
										<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
								<?php }
								} ?>
							</div>
						</div>
						<div class="transport_group">
							<?php $renamed_accordion = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_accordion_names` WHERE `ticket_type` = '".(empty($tab) ? 'tickets' : 'tickets_'.$tab)."' AND `accordion` = 'Transport Carrier'"))['accordion_name']; ?>

							<div class="fields_sortable">
								<?php
								$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
								?>
								<?php foreach ($selected_field_order as $selected_field_field) {
									if($selected_field_field != '' || $selected_field_field != null) { ?>
										<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
										<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
								<?php }
								} ?>
							</div>

						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Documents') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Documents">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Documents' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_documents',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_documents',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_documents" data-toggle="<?= in_array('view_ticket_documents',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_documents',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_documents',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Documents' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Check Out') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Check Out">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Check Out' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_checkout',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_checkout',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_checkout" data-toggle="<?= in_array('ticket_checkout',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_checkout',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_checkout',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Check Out' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<?php if((!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) || in_array('Check Out', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Check Out", $all_mandatory_config) ? 'checked disabled' : (in_array("Check Out", $value_mandatory_config) ? "checked" : '') ?> value="Check Out" name="tickets[]">
							<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to mark individuals, equipment, or other supplies as done for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable Check Out</label>
					<?php } ?>

					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Staff Check Out') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Staff Check Out">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Check Out' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_checkout_staff',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_checkout_staff',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_checkout_staff" data-toggle="<?= in_array('ticket_checkout_staff',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_checkout_staff',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_checkout_staff',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Check Out' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>

					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Billing') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Billing">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Billing' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_billing',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_billing',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_billing" data-toggle="<?= in_array('ticket_billing',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_billing',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_billing',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Billing' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Billing", $all_mandatory_config) ? 'checked disabled' : (in_array("Billing", $value_mandatory_config) ? "checked" : '') ?> value="Billing" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to display detailed billing information for the <?= TICKET_NOUN ?> with discounts and totals."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Customer Notes') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Customer Notes">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Customer Notes' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_customer_notes',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_customer_notes',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_customer_notes" data-toggle="<?= in_array('ticket_customer_notes',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_customer_notes',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_customer_notes',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Customer Notes' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Customer Notes", $all_mandatory_config) ? 'checked disabled' : (in_array("Customer Notes", $value_mandatory_config) ? "checked" : '') ?> value="Customer Notes" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to have a customer add notes for Delivery Stops for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Addendum') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Addendum">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Addendum' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('addendum_view_ticket_comment',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('addendum_view_ticket_comment',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="addendum_view_ticket_comment" data-toggle="<?= in_array('addendum_view_ticket_comment',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('addendum_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('addendum_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Addendum' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Addendum", $all_mandatory_config) ? 'checked disabled' : (in_array("Addendum", $value_mandatory_config) ? "checked" : '') ?> value="Addendum" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add Addendum notes for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Client Log') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Client Log">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Log Notes' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_log_notes',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_log_notes',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_log_notes" data-toggle="<?= in_array('ticket_log_notes',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_log_notes',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_log_notes',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Staff Log Notes' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Client Log", $all_mandatory_config) ? 'checked disabled' : (in_array("Client Log", $value_mandatory_config) ? "checked" : '') ?> value="Client Log" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add notes that get stored to a Staff profile from the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Debrief') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Debrief">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Debrief' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('debrief_view_ticket_comment',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('debrief_view_ticket_comment',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="debrief_view_ticket_comment" data-toggle="<?= in_array('debrief_view_ticket_comment',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('debrief_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('debrief_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Debrief' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Debrief", $all_mandatory_config) ? 'checked disabled' : (in_array("Debrief", $value_mandatory_config) ? "checked" : '') ?> value="Debrief" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add Debrief notes to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Member Log Notes') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Member Log Notes">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Member Log Notes' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('member_view_ticket_comment',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('member_view_ticket_comment',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="member_view_ticket_comment" data-toggle="<?= in_array('member_view_ticket_comment',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('member_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('member_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Member Log Notes' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Member Log Notes", $all_mandatory_config) ? 'checked disabled' : (in_array("Member Log Notes", $value_mandatory_config) ? "checked" : '') ?> value="Member Log Notes" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add notes that get stored to a Contact profile from the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Cancellation') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Cancellation">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Cancellation' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_cancellation',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_cancellation',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_cancellation" data-toggle="<?= in_array('ticket_cancellation',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_cancellation',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_cancellation',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Cancellation' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Cancellation", $all_mandatory_config) ? 'checked disabled' : (in_array("Cancellation", $value_mandatory_config) ? "checked" : '') ?> value="Cancellation" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add cancellation details to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Custom Notes') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Custom Notes">
				<label class="col-sm-4 control-label">Custom Notes:</label>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Custom Notes", $all_mandatory_config) ? 'checked disabled' : (in_array("Custom Notes", $value_mandatory_config) ? "checked" : '') ?> value="Custom Notes" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add additional notes with any headings to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>
						<div class="block-group">
						</div>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Internal Communication') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Internal Communication">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Internal Communication' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('internal_communication',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('internal_communication',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="internal_communication" data-toggle="<?= in_array('internal_communication',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('internal_communication',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('internal_communication_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Internal Communication' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Internal Communication", $all_mandatory_config) ? 'checked disabled' : (in_array("Internal Communication", $value_mandatory_config) ? "checked" : '') ?> value="Internal Communication" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add Internal Communication to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'External Communication') { ?>
			<div class="form-group sort_order_accordion" data-accordion="External Communication">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'External Communication' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('external_communication',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('external_communication',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="external_communication" data-toggle="<?= in_array('external_communication',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('external_communication',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('external_communication',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'External Communication' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("External Communication", $all_mandatory_config) ? 'checked disabled' : (in_array("External Communication", $value_mandatory_config) ? "checked" : '') ?> value="External Communication" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add External Communication to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
						<?php
						$field_sort_order = array("External Response", "External Response Thread", "External Response Status", "External Response Documents");
						$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
						?>
						<?php foreach ($selected_field_order as $selected_field_field) {
							if($selected_field_field != '' || $selected_field_field != null) { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
						<?php }
						} ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Notes') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Notes">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : TICKET_NOUN.' Notes' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('notes_view_ticket_comment',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('notes_view_ticket_comment',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="notes_view_ticket_comment" data-toggle="<?= in_array('notes_view_ticket_comment',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('notes_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('notes_view_ticket_comment',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : TICKET_NOUN.' Notes' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Notes", $all_mandatory_config) ? 'checked disabled' : (in_array("Notes", $value_mandatory_config) ? "checked" : '') ?> value="Notes" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add general notes to the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>

						<?php
						$field_sort_order = array("Notes Anyone Can Add", "Notes Limit", "Notes Alert", "Notes Email Default On");
						$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
						?>
						<?php foreach ($selected_field_order as $selected_field_field) {
							if($selected_field_field != '' || $selected_field_field != null) { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
						<?php }
						} ?>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Summary') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Summary">
				<label class="col-sm-4 control-label">Summary:</label>
				<div class="col-sm-8">
					<?php if((!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) || in_array('Staff Summary', $merged_config_fields)) { ?>
						<label class="form-checkbox"><input type="checkbox" <?= in_array("Staff Summary", $all_mandatory_config) ? 'checked disabled' : (in_array("Staff Summary", $value_mandatory_config) ? "checked" : '') ?> value="Staff Summary" name="tickets[]">
							<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to display a summary of Staff and other contacts for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable Staff Summary</label>
					<?php } ?>

					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) {
							$position_list = [];
							$positions = $dbc->query("SELECT `position_id`,`name` FROM `positions` WHERE `deleted`=0");
							while($position = $positions->fetch_assoc()) {
								$position_list[$position['position_id']] = $position['name'];
							} ?>
						<?php } ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Multi-Disciplinary Summary Report') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Multi-Disciplinary Summary Report">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Multi-Disciplinary Summary Report' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_multi_disciplinary_summary_report',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_multi_disciplinary_summary_report',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_multi_disciplinary_summary_report" data-toggle="<?= in_array('view_multi_disciplinary_summary_report',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_multi_disciplinary_summary_report',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_multi_disciplinary_summary_report',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Multi-Disciplinary Summary Report' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Multi-Disciplinary Summary Report", $all_mandatory_config) ? 'checked disabled' : (in_array("Multi-Disciplinary Summary Report", $value_mandatory_config) ? "checked" : '') ?> value="Multi-Disciplinary Summary Report" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add details to create a report from the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Complete') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Complete">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Complete (Sign Off)' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_complete',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_complete',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_complete" data-toggle="<?= in_array('ticket_complete',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_complete',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_complete',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Complete (Sign Off)' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Complete", $all_mandatory_config) ? 'checked disabled' : (in_array("Complete", $value_mandatory_config) ? "checked" : '') ?> value="Complete" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow the user to sign off that the <?= TICKET_NOUN ?> is complete."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>
						<?php
						$field_sort_order = array("Complete Hide Signature","Complete Hide Sign & Complete","Complete Sign & Force Complete","Complete Do Not Require Notes","Complete Default Session User","Complete Email Users On Complete","Complete Combine Checkout Summary","Complete Submit Approval","Complete Main Approval","Complete Office Approval");
						$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
						?>
						<?php foreach ($selected_field_order as $selected_field_field) {
							if($selected_field_field != '' || $selected_field_field != null) { ?>
								<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
								<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
						<?php }
						} ?>
					<?php } ?>
				</div>
			</div>
		<?php }

		if($sort_field == 'Notifications') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Notifications">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Notifications' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_notifications',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_notifications',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_notifications" data-toggle="<?= in_array('view_ticket_notifications',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_notifications',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_notifications',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Notifications' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Region Location Classification') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Region Location Classification">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Region/Location/Classification' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_reg_loc_class',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_reg_loc_class',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_reg_loc_class" data-toggle="<?= in_array('ticket_reg_loc_class',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_reg_loc_class',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_reg_loc_class',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Region/Location/Classification' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Region Location Classification", $all_mandatory_config) ? 'checked disabled' : (in_array("Region Location Classification", $value_mandatory_config) ? "checked" : '') ?> value="Region Location Classification" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to specify a region, location, or classification for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Incident Reports') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Incident Reports">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : INC_REP_TILE ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_incident_reports',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_incident_reports',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_incident_reports" data-toggle="<?= in_array('view_ticket_incident_reports',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_incident_reports',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_incident_reports',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : INC_REP_TILE ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" <?= in_array("Incident Reports", $all_mandatory_config) ? 'checked disabled' : (in_array("Incident Reports", $value_mandatory_config) ? "checked" : '') ?> value="Incident Reports" name="tickets[]">
						<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to add <?= INC_REP_TILE ?> from the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Enable</label>
				</div>
			</div>
		<?php }

		if($sort_field == 'Pressure') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Pressure">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Pressure' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_pressure',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_pressure',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_pressure" data-toggle="<?= in_array('view_ticket_pressure',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_pressure',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_pressure',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Pressure' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Chemicals') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Chemicals">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Chemicals' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_chemicals',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_chemicals',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_chemicals" data-toggle="<?= in_array('ticket_chemicals',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_chemicals',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_chemicals',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Chemicals' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
						<?php $ticket_chemical_label = get_config($dbc, 'ticket_chemical_label'); ?>

						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		<?php }

		if($sort_field == 'Intake') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Intake">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Intake' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_intake',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_intake',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_intake" data-toggle="<?= in_array('view_ticket_intake',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_intake',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_intake',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Intake' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>

			</div>
		<?php }

		if($sort_field == 'History') { ?>
			<div class="form-group sort_order_accordion" data-accordion="History">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'History' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('view_ticket_intake',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('view_ticket_intake',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="view_ticket_intake" data-toggle="<?= in_array('view_ticket_intake',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('view_ticket_intake',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('view_ticket_intake',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'History' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>

			</div>
		<?php }

		if($sort_field == 'Work History') { ?>
			<div class="form-group sort_order_accordion" data-accordion="Work History">
				<label class="col-sm-4 control-label accordion_label"><span class="accordion_label_text"><?= !empty($renamed_accordion) ? $renamed_accordion : 'Work History' ?></span>:<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?> <a href="" onclick="editAccordion(this); return false;"><span class="subscript-edit">EDIT</span></a>
					<span class="dataToggle cursor-hand no-toggle <?= in_array('ticket_work_history',$all_unlocked_tabs) ? 'disabled' : '' ?>" title="Locking a tab will hide the contents of that tab on all new <?= TICKET_TILE ?>. A user with access to edit the <?= TICKET_NOUN ?> can then unlock that tab for that <?= TICKET_NOUN ?>.<?= in_array('ticket_work_history',$all_unlocked_tabs) ? ' This tab has been locked for all '.TICKET_TILE.'.' : '' ?>">
						<input type="hidden" name="ticket_tab_locks<?= empty($tab) ? '' : '_'.$tab ?>" value="ticket_work_history" data-toggle="<?= in_array('ticket_work_history',$unlocked_tabs) ? 1 : 0 ?>">
						<img class="inline-img" style="<?= in_array('ticket_work_history',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? '' : 'display:none;' ?>" src="../img/icons/lock.png">
						<img class="inline-img" style="<?= in_array('ticket_work_history',array_merge($unlocked_tabs,$all_unlocked_tabs)) ? 'display:none;' : '' ?>" src="../img/icons/lock-open.png"></span><?php } ?></label>
				<div class="col-sm-4 accordion_rename" style="display: none;">
					<input type="text" name="renamed_accordion[]" value="<?= !empty($renamed_accordion) ? $renamed_accordion : 'Work History' ?>" onfocusout="updateAccordion(this);" class="form-control">
				</div>
				<div class="col-sm-8">
					<div class="block-group">
						<div class="fields_sortable">
							<?php
							$selected_field_order = array_unique(array_intersect($field_sort_order, $value_config));
							?>
							<?php foreach ($selected_field_order as $selected_field_field) {
								if($selected_field_field != '' || $selected_field_field != null) { ?>
									<label class="form-checkbox sort_order_field"><input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $value_mandatory_config) ? "checked" : '') ?> value="<?php echo $selected_field_field; ?>" name="tickets[]">
									<span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will create a list of <?= BUSINESS_CAT ?> <?= CONTACTS_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?php echo $selected_field_field; ?></label>
							<?php }
							} ?>
						</div>
					</div>
				</div>
			</div>
		<?php }
	}

	//Close higher level ending if not already closed
	if(!$current_heading_closed) { ?>
			</div>
		</div>
	<?php } ?>
</div>

<?php if(!$action_mode && !$status_fields && !$overview_mode && !$unlock_mode) { ?>
	<a href="" onclick="addCustomAccordion(); return false;" class="btn brand-btn pull-right gap-bottom">Add Custom Accordion</a>
	<span class="popover-examples pull-right"><a data-toggle="tooltip" data-original-title="This will allow you to create custom accordions that have certain details in them for the <?= TICKET_NOUN ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>
	<div class="clearfix"></div>
	<a href="" onclick="addHigherLevelHeading(); return false;" class="btn brand-btn pull-right gap-bottom">Add Higher Level Heading</a>
	<span class="popover-examples pull-right"><a data-toggle="tooltip" data-original-title="This will allow you to rearrange the headings of the <?= TICKET_NOUN ?> into subtabs along the left of the ticket."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>
	<div class="clearfix"></div>
<?php } ?>
