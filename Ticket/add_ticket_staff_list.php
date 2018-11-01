<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Staff</h3>') ?>
<input type="hidden" name="contactid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $get_ticket['contactid'] ?>">
<div class="hide-titles-mob">
	<label class="col-sm-4 text-center">Staff</label>

	<?php foreach($field_sort_order as $field_sort_field) { ?>
		<?php if($field_sort_field == 'Staff Position') { ?>
			<label class="col-sm-3 text-center" style="<?= strpos($value_config,',Staff Position,') === FALSE ? 'display: none;' : '' ?>">
				<span class="popover-examples list-inline" style="margin:0 3px 0 0;"><a data-toggle="tooltip" data-placement="top" title="<?php if(count($roles) > 1 || explode('|',$roles[0])[0] != '') {
					echo "Each of these positions has security associated with it, giving the staff access to certain parts of the ".TICKET_NOUN.". ";
					foreach($roles as $i => $ticket_role) {
						$ticket_role = explode('|',$ticket_role);
						if($ticket_role[0] > 0) {
							$ticket_role[0] = get_field_value('name', 'positions', 'position_id', $ticket_role[0]).'###'.$ticket_role[0];
							$roles[$i] = implode('|',$ticket_role);
						} else {
							$ticket_role[0] = $ticket_role[0].'###'.get_field_value('position_id', 'positions', 'name', $ticket_role[0]);
							$roles[$i] = implode('|',$ticket_role);
						}
						echo explode('###',$ticket_role[0])[0]." has access to the following: ";
						$access_output = [];
						if(in_array('staff_list',$ticket_role)) {
							$access_output[] = "Staff Information";
						}
						if(in_array('contact_list',$ticket_role)) {
							$access_output[] = "Clients / Members";
						}
						if(in_array('wait_list',$ticket_role)) {
							$access_output[] = "Wait List";
						}
						if(in_array('staff_checkin',$ticket_role)) {
							$access_output[] = "Staff Check In / Out";
						}
						if(in_array('all_checkin',$ticket_role)) {
							$access_output[] = "Other Check In / Out";
						}
						if(in_array('medication',$ticket_role)) {
							$access_output[] = "Medication Administration";
						}
						if(in_array('complete',$ticket_role)) {
							$access_output[] = "Complete ".TICKET_NOUN;
						}
						if(in_array('ticket',$ticket_role)) {
							$access_output[] = "All Other";
						}
						echo implode(', ',$access_output).'. ';
					}
				} else {
					echo "These are positions that can be defined in the Staff tile.";
				} ?>"><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				Position</label>
		<?php } ?>
		<?php if($field_sort_field == 'Staff Rate Positions') { ?>
			<label class="col-sm-2 text-center" style="<?= strpos($value_config,',Staff Rate Positions,') === FALSE ? 'display: none;' : '' ?>">Position</label>
		<?php } ?>
		<?php if($field_sort_field == 'Staff Rate') { ?>
			<label class="col-sm-1 text-center" style="<?= strpos($value_config,',Staff Rate,') === FALSE ? 'display: none;' : '' ?>">Rate</label>
		<?php } ?>
		<?php if($field_sort_field == 'Staff Start') { ?>
			<label class="col-sm-3 text-center" style="<?= strpos($value_config,',Staff Start,') === FALSE ? 'display: none;' : '' ?>">Start Shift</label>
		<?php } ?>
		<?php if($field_sort_field == 'Staff Set Hours') { ?>
			<label class="col-sm-1 text-center" style="<?= strpos($value_config,',Staff Set Hours,') === FALSE ? 'display: none;' : '' ?>">Payable Hours</label>
		<?php } ?>
		<?php if($field_sort_field == 'Staff Hours') { ?>
			<label class="col-sm-1 text-center" style="<?= strpos($value_config,',Staff Hours,') === FALSE ? 'display: none;' : '' ?>">Hours</label>
		<?php } ?>
		<?php if($field_sort_field == 'Staff Overtime') { ?>
			<label class="col-sm-1 text-center" style="<?= strpos($value_config,',Staff Overtime,') === FALSE ? 'display: none;' : '' ?>">Overtime</label>
		<?php } ?>
		<?php if($field_sort_field == 'Staff Travel') { ?>
			<label class="col-sm-1 text-center" style="<?= strpos($value_config,',Staff Travel,') === FALSE ? 'display: none;' : '' ?>">Travel Time</label>
		<?php } ?>
		<?php if($field_sort_field == 'Staff Subsistence') { ?>
			<label class="col-sm-1 text-center" style="<?= strpos($value_config,',Staff Subsistence,') === FALSE ? 'display: none;' : '' ?>">Subsistence Pay</label>
		<?php } ?>
		<?php if($field_sort_field == 'Staff Subsistence Options') { ?>
			<label class="col-sm-1 text-center" style="<?= strpos($value_config,',Staff Subsistence,') === FALSE ? 'display: none;' : '' ?>">Subsistence Pay</label>
		<?php } ?>
		<?php if($field_sort_field == 'Staff Check In' && strpos($value_config,',Staff Check In,') !== FALSE) { ?>
			<label class="col-sm-2 text-center" style="<?= strpos($value_config,',Staff Check In,') === FALSE ? 'display: none;' : '' ?>">Check In</label>
		<?php } ?>
	<?php } ?>
</div>
<div class="clearfix"></div>
<?php if($project_lead > 0 && $ticketid == 0) { ?>
	<script>
	$(document).ready(function() {
		var select = $('#collapse_staff,#tab_section_ticket_staff_list').find('[name=item_id]').first();
		if(!(select.val() > 0)) {
			select.val(<?= $project_lead ?>).change().trigger('change.select2');
		}
	});
	</script>
<?php }
$query = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `src_table`='Staff' AND `deleted`=0 AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `tile_name`='".FOLDER_NAME."' $query_daily ORDER BY `position` = 'Team Lead' DESC, `position` = 'Primary' DESC, `position` = 'Assigned'");
$staff = mysqli_fetch_assoc($query);
$rate_card_name = '%';
if($projectid > 0) {
	$rate = explode('*',get_field_value('ratecardid','project','projectid',$projectid));
	if($rate[0] > 0) {
		$rate_card_name = '%';
	} else if($rate[0] == 'company') {
		$rate_card_name = get_field_value('rate_card_name','company_rate_card','companyrcid',$rate[1]);
	}
}
if($_GET['new_ticket_calendar'] == 'true' && empty($_GET['edit']) && !($_GET['ticketid'] > 0)) {
	$contactid = [];
	if(!empty($_GET['calendar_contactid'])) {
		foreach(explode(',', $_GET['calendar_contactid']) as $calendar_contactid) {
			$contactid[] = $calendar_contactid;
		}
	}
	$contactid = array_filter(array_unique($contactid));
	$contact_query = '';
	foreach($contactid as $contact_id) {
		$contact_query[] = "SELECT '$contact_id' `item_id`";
	}
	$query = mysqli_query($dbc, implode(" UNION ", $contact_query));
	$staff = mysqli_fetch_assoc($query);
}
do {
	$positions_allowed = [];
	$position_rate = 0; ?>
	<div class="multi-block staff_block">
		<?php if(($access_staff === TRUE || strpos($value_config, ',Staff Anyone Can Add,') !== FALSE) && !($strict_view > 0)) {
        if($staff['item_id'] == '' && !($ticketid > 0) && get_config($dbc, 'ticket_default_session_user') != 'no_user') { $staff['item_id'] = $_SESSION['contactid']; }
        ?>
			<div class="col-sm-4">
				<label class="show-on-mob control-label">Staff:</label>
				<select name="item_id" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="chosen-select-deselect"><option></option>
					<?php foreach($staff_list as $staff_row) { ?>
						<option <?= $staff_row['contactid'] == $staff['item_id'] ? 'selected' : '' ?> data-positions-allowed="<?= $staff_row['positions_allowed'] ?>" data-position="<?= $staff_row['position'] ?>" value="<?= $staff_row['contactid'] ?>"><?= $staff_row['first_name'].' '.$staff_row['last_name'] ?></option>
						<?php if($staff_row['contactid'] == $staff['item_id']) {
							$positions_allowed = array_filter(explode(',',$staff_row['positions_allowed']));
						}
					} ?>
				</select>
			</div>

			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if($field_sort_field == 'Staff Position') {?>
					<div class="col-sm-3" style="<?= strpos($value_config,',Staff Position,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Position:</label>
						<select name="position" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="chosen-select-deselect"><option></option>
							<?php if(count($roles) > 1 || explode('|',$roles[0])[0] != '') {
								foreach($roles as $ticket_role) {
									$ticket_role = explode('|',$ticket_role); ?>
									<option <?= html_entity_decode(explode('###',$ticket_role[0])[0]) == html_entity_decode($staff['position']) ? 'selected' : '' ?> data-id="<?= explode('###',$ticket_role[0])[1] ?>" value="<?= explode('###',$ticket_role[0])[0] ?>" <?= count($positions_allowed) > 0 && !in_array(explode('###',$ticket_role[0])[1],$positions_allowed) ? 'style="display:none;"' : '' ?>><?= explode('###',$ticket_role[0])[0] ?></option>
								<?php }
							} else { ?>
								<option <?= 'Team Lead' == $staff['position'] ? 'selected' : '' ?> value="Team Lead">Team Lead</option>
								<option <?= 'Primary' == $staff['position'] ? 'selected' : '' ?> value="Primary">Primary</option>
								<?php $positions = mysqli_query($dbc, "SELECT `name`, `position_id` FROM `positions` ORDER BY `name`");
								while($position = mysqli_fetch_assoc($positions)) { ?>
									<option <?= $position['name'] == $staff['position'] ? 'selected' : '' ?> value="<?= $position['name'] ?>" <?= count($positions_allowed) > 0 && !in_array($position['position_id'],$positions_allowed) ? 'style="display:none;"' : '' ?>><?= $position['name'] ?></option>
								<?php }
							} ?>
						</select>
					</div>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Rate Positions') { ?>
					<div class="col-sm-2" style="<?= strpos($value_config,',Staff Rate Positions,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Position:</label>
						<select name="position" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="chosen-select-deselect"><option></option>
							<?php $positions = mysqli_query($dbc, "SELECT `positions`.`name`, `positions`.`position_id`, MAX(IF(`rate_card_types`='Regular',`hourly`,0)) `regular`, MAX(IF(`rate_card_types`='Overtime',`hourly`,0)) `overtime`, MAX(IF(`rate_card_types`='Travel',`hourly`,0)) `travel`, MAX(`company_rate_card`.`hourly`) `hourly`, MAX(`company_rate_card`.`daily`) `daily` FROM `positions` LEFT JOIN `company_rate_card` ON `positions`.`name`=`company_rate_card`.`description` AND DATE(NOW()) BETWEEN `company_rate_card`.`start_date` AND IFNULL(NULLIF(`company_rate_card`.`end_date`,'0000-00-00'),'9999-12-31') WHERE `rate_card_name` LIKE '$rate_card_name' AND `company_rate_card`.`deleted`=0 AND `positions`.`deleted`=0 GROUP BY `position_id`, `positions`.`name` HAVING MAX(`hourly`) > 0 ORDER BY `positions`.`name`");
							while($position = mysqli_fetch_assoc($positions)) { ?>
								<option <?= $position['name'] == $staff['position'] ? 'selected' : '' ?> data-hourly="<?= $position['hourly'] ?>" data-daily="<?= $position['daily'] ?>" data-regular="<?= $position['regular'] ?>" data-overtime="<?= $position['overtime'] ?>" data-travel="<?= $position['travel'] ?>" value="<?= $position['name'] ?>"><?= $position['name'] ?>
								<?php if($position['name'] == $staff['position'] && $position_rate == 0 && $position['regular'] > 0) {
									$position_rate = $position['regular'];
								} else if($position['name'] == $staff['position'] && $position_rate == 0 && $position['hourly'] > 0) {
									$position_rate = $position['hourly'];
								} ?></option>
							<?php } ?>
						</select>
					</div>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Rate') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Rate,') === FALSE || $config_access == 0 ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Rate:</label>
						<input type="text" name="rate" readonly class="form-control" value="<?= $position_rate ?>">
					</div>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Start') { ?>
					<div class="col-sm-3" style="<?= strpos($value_config,',Staff Start,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Start Shift:</label>
						<input type="text" name="shift_start" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="timepicker form-control" value="<?= $staff['shift_start'] ?>">
					</div>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Set Hours') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Set Hours,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Payable Hours:</label>
						<input type="number" min=0 step="<?= $hour_increment ?>" name="hours_set" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" data-track-timesheet="<?= strpos($value_config,',Staff Set Hours Time Sheet,') !== FALSE ? '1' : '' ?>" <?= strpos($value_config,',Time Tracking Edit Past Date') !== FALSE && $get_ticket['to_do_date'] != '' ? 'data-date="'.$get_ticket['to_do_date'].'"' : '' ?> class="form-control" value="<?= $staff['hours_set'] ?>">
					</div>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Hours') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Hours,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Hours:</label>
						<input type="number" min=0 step="<?= $hour_increment ?>" name="hours_estimated" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control" value="<?= $staff['hours_estimated'] ?>">
					</div>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Estimate') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Estimate,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Estimated Time:</label>
						<input type="number" min=0 step="<?= $hour_increment ?>" name="hours_estimated" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control" value="<?= $staff['hours_estimated'] ?>">
					</div>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Overtime') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Overtime,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Overtime:</label>
						<input type="number" min=0 step="<?= $hour_increment ?>" name="hours_ot" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" class="form-control" value="<?= $staff['hours_ot'] ?>">
					</div>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Travel') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Travel,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Travel Time:</label>
						<input type="number" min=0 step="<?= $hour_increment ?>" name="hours_travel" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" data-default="<?= get_config($dbc, 'ticket_staff_travel_default') ?>" class="form-control" value="<?= empty($staff['hours_travel']) ? get_config($dbc, 'ticket_staff_travel_default') : $staff['hours_travel'] ?>">
					</div>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Subsistence') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Subsistence,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Subsistence Pay:</label>
						<div class="toggleSwitch mobile-lg">
							<input type="hidden" name="hours_subsist" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" value="<?= $staff['hours_subsist'] ?>" class="toggle">
							<span style="<?= $staff['hours_subsist'] > 0 ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img"> No</span>
							<span style="<?= $staff['hours_subsist'] > 0 ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img"> Yes</span>
						</div>
					</div>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Subsistence Options') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Subsistence,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Subsistence Pay:</label>
						<select name="hours_subsist" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" data-placeholder="Select Rate" class="chosen-select-deselect">
							<option value="0">None</option>
							<?php if(!isset($subsist_list)) {
								$subsist_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT `daily`, IFNULL(NULLIF(`heading`,''),`rate_card_types`) `type` FROM `company_rate_card` WHERE DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') AND `rate_card_name` LIKE '$rate_card_name' AND `deleted`=0 ORDER BY `heading`,`rate_card_types`"),MYSQLI_ASSOC);
							}
							foreach($subsist_list as $subsist_row) { ?>
								<option <?= $subsist_row['type'] == $staff['subsist'] ? 'selected' : '' ?> data-rate="<?= $subsist_row['daily'] ?>" value="<?= $subsist_row['type'] ?>"><?= $subsist_row['type'] ?>
							<?php } ?>
						</select>
					</div>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Check In' && strpos($value_config,',Staff Check In,') !== FALSE) { ?>
					<div class="col-sm-2" style="<?= strpos($value_config,',Staff Check In,') === FALSE ? 'display: none;' : '' ?>">
						<div class="<?= $access_staff_checkin == TRUE || $staff['item_id'] == $_SESSION['contactid'] ? 'toggleSwitch staffSwitch mobile-lg' : '' ?>">
							<input type="hidden" name="checkin_id[]" value="<?= $staff['item_id'] ?>">
							<input type="hidden" name="arrived" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" value="<?= $staff['arrived'] ?>" class="toggle" <?= strpos($value_config,',Check Out Before Check In,') !== FALSE ? 'data-no-toggle-off="1"' : '' ?>>
							<span style="<?= $staff['arrived'] > 0 && (!empty($staff['timer_start']) || strpos($value_config,',Check Out Before Check In,') === FALSE) ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img on_img"> Not Checked In</span>
							<span style="<?= $staff['arrived'] > 0 && (!empty($staff['timer_start']) || strpos($value_config,',Check Out Before Check In,') === FALSE) ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img off_img"> Checked In</span>
						</div>
					</div>
				<?php } ?>
			<?php } ?>
			<div class="col-sm-1 pull-right">
				<span class="show-on-mob pull-left" onclick="$(this).closest('div').find('img').first().click();">More details</span>
				<img class="inline-img pull-left black-color counterclockwise small" onclick="showStaff(this);" src="../img/icons/dropdown-arrow.png">
				<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $staff['id'] ?>" data-id-field="id" data-type="Staff" data-type-field="src_table" value="0">
				<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
				<img class="inline-img pull-right" data-history-label="Staff" onclick="addMulti(this, '', 'after');" src="../img/icons/ROOK-add-icon.png">
				<?php if($access_staff === TRUE) { ?>
					<img class="inline-img pull-right" data-history-label="Staff" onclick="remMulti(this);" src="../img/remove.png">
				<?php } ?>
			</div>
		<?php } else if($staff['item_id'] > 0) { ?>
			<div class="col-sm-4">
				<label class="show-on-mob control-label">Staff:</label>
				<?= get_contact($dbc, $staff['item_id']) ?>
			</div>
			<?php $pdf_contents[] = ['Staff', get_contact($dbc, $staff['item_id'])]; ?>
			<?php foreach($field_sort_order as $field_sort_field) { ?>
				<?php if($field_sort_field == 'Staff Position') { ?>
					<div class="col-sm-3" style="<?= strpos($value_config,',Staff Position,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Position:</label>
						<?= $staff['position'] ?>
					</div>
					<?php if(strpos($value_config,',Staff Position,') !== FALSE) {
						$pdf_contents[] = ['Position', $staff['position']];
					} ?>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Start') { ?>
					<div class="col-sm-3" style="<?= strpos($value_config,',Staff Start,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Start Shift:</label>
						<?= $staff['shift_start'] ?>
					</div>
					<?php if(strpos($value_config,',Staff Start,') !== FALSE) {
						$pdf_contents[] = ['Start Shift', $staff['shift_start']];
					} ?>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Set Hours') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Set Hours,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Payable Hours:</label>
						<?= $staff['hours_set'] ?>
					</div>
					<?php if(strpos($value_config,',Staff Set Hours,') !== FALSE) {
						$pdf_contents[] = ['Payable Hours', $staff['hours_set']];
					} ?>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Hours') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Hours,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Hours:</label>
						<?= $staff['hours_estimated'] ?>
					</div>
					<?php if(strpos($value_config,',Staff Hours,') !== FALSE) {
						$pdf_contents[] = ['Hours', $staff['hours_estimated']];
					} ?>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Overtime') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Overtime,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Overtime:</label>
						<?= $staff['hours_ot'] ?>
					</div>
					<?php if(strpos($value_config,',Staff Overtime,') !== FALSE) {
						$pdf_contents[] = ['Overtime', $staff['hours_ot']];
					} ?>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Travel') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Travel,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Travel Time:</label>
						<?= $staff['hours_travel'] ?>
					</div>
					<?php if(strpos($value_config,',Staff Travel,') !== FALSE) {
						$pdf_contents[] = ['Travel Time', $staff['hours_travel']];
					} ?>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Subsistence') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Subsistence,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Subsistence Pay:</label>
						<span style="<?= $staff['hours_subsist'] > 0 ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="inline-img"> No</span>
						<span style="<?= $staff['hours_subsist'] > 0 ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="inline-img"> Yes</span>
					</div>
					<?php if(strpos($value_config,',Staff Subsistence,') !== FALSE) {
						$pdf_contents[] = ['Subsistence Pay', $staff['hours_subsist'] > 0 ? 'Yes' : 'No'];
					} ?>
				<?php } ?>
				<?php if($field_sort_field == 'Staff Subsistence Options') { ?>
					<div class="col-sm-1" style="<?= strpos($value_config,',Staff Subsistence,') === FALSE ? 'display: none;' : '' ?>">
						<label class="show-on-mob control-label">Subsistence Pay:</label>
						<?= $staff['hours_subsist'] ?>
					</div>
					<?php if(strpos($value_config,',Staff Subsistence,') !== FALSE) {
						$pdf_contents[] = ['Subsistence Pay', $staff['hours_subsist']];
					} ?>
				<?php } ?>
			<?php } ?>
		<?php } ?>
		<div class="clearfix"></div>
		<div class="iframe_div" style="display:none">
			<span>Loading...</span>
			<iframe name="staff_iframe" style="height: 0; width: 100%;" src=""></iframe>
		</div>
		<?php if(strpos($value_config,',Staff Multiple Times,') !== FALSE) {
			$staff_times = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table` = 'Multiple_Timesheet_Row' AND `item_id` = '".$staff['id']."' AND '".$staff['id']."' > 0 AND `deleted` = 0"); ?>
			<div class="clearfix"></div>
			<div id="no-more-tables" class="staff_multiple_times">
				<table class="table table-bordered">
					<tr class="hidden-xs">
						<?php foreach($field_sort_order as $field_sort_field) { ?>
							<?php if($field_sort_field == 'Staff Multiple Times Date') { ?>
								<th>Date</th>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Multiple Times Start Time') { ?>
								<th>Start Time</th>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Multiple Times End Time') { ?>
								<th>End Time</th>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Multiple Times Type') { ?>
								<th>Type of Time</th>
							<?php } ?>
							<?php if($field_sort_field == 'Staff Multiple Times Set Hours') { ?>
								<th>Payable Hours</th>
							<?php } ?>
						<?php } ?>
						<?php if($access_staff === TRUE) { ?>
							<th>Function</th>
						<?php } ?>
					</tr>
					<?php $staff_time = mysqli_fetch_assoc($staff_times);
					do { ?>
						<tr class="staff-multi-time">
							<input type="hidden" name="item_id" data-table="ticket_attached" data-id="<?= $staff_time['id'] ?>" data-id-field="id" data-type="Multiple_Timesheet_Row" data-type-field="src_table" value="<?= $staff['id'] ?>">
							<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $staff_time['id'] ?>" data-id-field="id" data-type="Multiple_Timesheet_Row" data-type-field="src_table" value="0">
							<?php if($access_staff === TRUE) {
								foreach($field_sort_order as $field_sort_field) { ?>
									<?php if($field_sort_field == 'Staff Multiple Times Date') { ?>
										<td data-title="Date">
											<input type="text" name="date_stamp" data-table="ticket_attached" data-id="<?= $staff_time['id'] ?>" data-id-field="id" data-type="Multiple_Timesheet_Row" data-type-field="src_table" class="datepicker form-control" value="<?= $staff_time['date_stamp'] ?>">
										</td>
									<?php } ?>
									<?php if($field_sort_field == 'Staff Multiple Times Start Time') { ?>
										<td data-title="Start Time">
											<input type="text" name="start_time" data-table="ticket_attached" data-id="<?= $staff_time['id'] ?>" data-id-field="id" data-type="Multiple_Timesheet_Row" data-type-field="src_table" class="datetimepicker form-control" value="<?= $staff_time['start_time'] ?>">
										</td>
									<?php } ?>
									<?php if($field_sort_field == 'Staff Multiple Times End Time') { ?>
										<td data-title="End Time">
											<input type="text" name="end_time" data-table="ticket_attached" data-id="<?= $staff_time['id'] ?>" data-id-field="id" data-type="Multiple_Timesheet_Row" data-type-field="src_table" class="datetimepicker form-control" value="<?= $staff_time['end_time'] ?>">
										</td>
									<?php } ?>
									<?php if($field_sort_field == 'Staff Multiple Times Type') { ?>
										<td data-title="Type of Time">
											<select name="position" data-table="ticket_attached" data-id="<?= $staff_time['id'] ?>" data-id-field="id" data-type="Multiple_Timesheet_Row" data-type-field="src_table" class="chosen-select-deselect form-control" value="<?= $staff_time['position'] ?>">
												<option></option>
												<option <?= $staff_time['position'] == 'Regular Hrs.' ? 'selected' : '' ?> value="Regular Hrs.">Regular Hrs.</option>
												<option <?= $staff_time['position'] == 'Sleep Hrs.' ? 'selected' : '' ?> value="Sleep Hrs.">Sleep Hrs.</option>
											</select>
										</td>
									<?php } ?>
									<?php if($field_sort_field == 'Staff Multiple Times Set Hours') { ?>
										<td data-title="Payable Hours">
											<input type="number" min=0 step="<?= $hour_increment ?>" name="hours_set" data-table="ticket_attached" data-id="<?= $staff_time['id'] ?>" data-id-field="id" data-type="Multiple_Timesheet_Row" data-type-field="src_table" class="form-control" value="<?= $staff_time['hours_set'] ?>">
										</td>
									<?php } ?>
								<?php } ?>
								<td data-title="Function">
									<img class="inline-img pull-right" onclick="addStaffMultiTime(this, '', 'after');" data-history-label="Staff Time" src="../img/icons/ROOK-add-icon.png">
									<img class="inline-img pull-right" data-history-label="Staff Time" onclick="remStaffMultiTIme(this);" src="../img/remove.png">
								</td>
							<?php } else {
								foreach($field_sort_order as $field_sort_field) { ?>
									<?php if($field_sort_field == 'Staff Multiple Times Date') { ?>
										<td data-title="Date"><?= $staff_time['date_stamp'] ?></td>
									<?php } ?>
									<?php if($field_sort_field == 'Staff Multiple Times Start Time') { ?>
										<td data-title="Start Time"><?= $staff_time['start_time'] ?></td>
									<?php } ?>
									<?php if($field_sort_field == 'Staff Multiple Times End Time') { ?>
										<td data-title="End Time"><?= $staff_time['end_time'] ?></td>
									<?php } ?>
									<?php if($field_sort_field == 'Staff Multiple Times Type') { ?>
										<td data-title="Type of Time"><?= $staff_time['position'] ?></td>
									<?php } ?>
									<?php if($field_sort_field == 'Staff Multiple Times Set Hours') { ?>
										<td data-title="Payable Hours"><?= $staff_time['hours_set'] ?></td>
									<?php } ?>
								<?php }
							} ?>
						</tr>
					<?php } while($staff_time = mysqli_fetch_assoc($staff_times)); ?>
				</table>
			</div>
		<?php } ?>
	</div>
	<hr class="visible-xs">
<?php } while($staff = mysqli_fetch_assoc($query)); ?>
<?php if(strpos($value_config,',Staff Billing,') !== FALSE) { ?>
	<div class="staff_billing_summary"></div>
<?php } ?>