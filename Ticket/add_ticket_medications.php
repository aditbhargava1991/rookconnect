<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Medication Administration</h3>') ?>
<div class="hide-titles-mob">
	<label class="col-sm-2 text-center">Member</label>
	<label class="col-sm-2 text-center">Medication</label>
	<label class="col-sm-2 text-center">Dosage</label>
	<?php if (strpos($value_config, ',Medication Multiple Days,') !== false) { ?>
		<label class="col-sm-2 text-center">Date</label>
	<?php } ?>
	<label class="col-sm-<?= strpos($value_config, ',Medication Multiple Days,') !== false ? '2' : '3' ?> text-center"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter the time that Medication was administered. This should be entered as hh:mm pp."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Time</label>
	<label class="col-sm-2 text-center">Administered</label>
	<div class="clearfix"></div>
</div>
<?php if($get_ticket['status'] !== 'Archive' && !$generate_pdf) {
	$available_staff_query = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticket_attached`.`item_id` > 0 AND `src_table`='Staff' AND `deleted`=0 AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `tile_name`='".FOLDER_NAME."' $query_daily ORDER BY `position` = 'Team Lead' DESC, `position` = 'Primary' DESC, `position` = 'Assigned'");
	$available_staff = [];
	while($available_staff_row = mysqli_fetch_assoc($available_staff_query)) {
		$available_staff[] = $available_staff_row['item_id'];
	}

	if (strpos($value_config, ',Medication Multiple Days,') !== false) {
		if(!empty($get_ticket['to_do_date'])) {
			$ticket_start_date = $get_ticket['to_do_date'];
			$ticket_end_date = empty(str_replace('0000-00-00','',$get_ticket['to_do_end_date'])) ? $get_ticket['to_do_date'] : $get_ticket['to_do_end_date'];
			for($cur_date = $ticket_start_date; strtotime($cur_date) <= strtotime($ticket_end_date); $cur_date = date('Y-m-d', strtotime($cur_date.' + 1 day'))) {
				$cur_start_time = $get_ticket['start_time'];
				$cur_end_time = $get_ticket['end_time'];
				if($cur_date != $ticket_start_date) {
					$cur_start_time = '00:00 am';
				}
				if($cur_date != $ticket_end_date) {
					$cur_end_time = '12:00 am';
				}
				mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `item_id`, `src_table`, `position`, `description`, `shift_start`, `date_stamp`) SELECT `ticket_attached`.`ticketid`, `ticket_attached`.`item_id`, 'medication', `medication`.`title`, `medication`.`dosage`, `medication`.`administration_times`, '$cur_date' FROM `ticket_attached` LEFT JOIN `medication` ON `ticket_attached`.`item_id`=`medication`.`clientid` LEFT JOIN `ticket_attached` med_attached ON `med_attached`.`item_id`=`ticket_attached`.`item_id` AND `med_attached`.`position`=`medication`.`title` AND `med_attached`.`ticketid`=`ticket_attached`.`ticketid` AND `med_attached`.`date_stamp` = '$cur_date' WHERE `ticket_attached`.`src_table`='Members' AND `ticket_attached`.`ticketid`='$ticketid' AND `medication`.`deleted`=0 AND (TIME_TO_SEC(`medication`.`administration_times`) IS NULL OR TIME_TO_SEC(`medication`.`administration_times`) BETWEEN TIME_TO_SEC('".$cur_start_time."') AND TIME_TO_SEC('".$cur_end_time."')) AND `med_attached`.`item_id` IS NULL AND IFNULL(`medication`.`title`,'') != ''");
			}
		}
	} else {
		mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `item_id`, `src_table`, `position`, `description`, `shift_start`) SELECT `ticket_attached`.`ticketid`, `ticket_attached`.`item_id`, 'medication', `medication`.`title`, `medication`.`dosage`, `medication`.`administration_times` FROM `ticket_attached` LEFT JOIN `medication` ON `ticket_attached`.`item_id`=`medication`.`clientid` LEFT JOIN `ticket_attached` med_attached ON `med_attached`.`item_id`=`ticket_attached`.`item_id` AND `med_attached`.`position`=`medication`.`title` AND `med_attached`.`ticketid`=`ticket_attached`.`ticketid` WHERE `ticket_attached`.`src_table`='Members' AND `ticket_attached`.`ticketid`='$ticketid' AND `medication`.`deleted`=0 AND (TIME_TO_SEC(`medication`.`administration_times`) IS NULL OR TIME_TO_SEC(`medication`.`administration_times`) BETWEEN TIME_TO_SEC('".$get_ticket['start_time']."') AND TIME_TO_SEC('".$get_ticket['end_time']."')) AND `med_attached`.`item_id` IS NULL AND IFNULL(`medication`.`title`,'') != ''");
	}

	$medications = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table`='medication' AND `line_id`='0' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0".$query_daily." ORDER BY `date_stamp`");
	$medication = mysqli_fetch_assoc($medications);
	$current_date = 'FIRST_DATE';
	if(strpos($value_config, ',Medication Group Days,') !== FALSE) { ?>
		<div id="panel_medication_groups" class="panel-group">
	<?php }
	do {
		if(strpos($value_config, ',Medication Group Days,') !== FALSE && $current_date != $medication['date_stamp']) {
			if($current_date != 'FIRST_DATE') { ?>
						</div>
					</div>
				</div>
			<?php }
			$current_date = $medication['date_stamp']; ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#panel_medication_groups" href="#collapse_medication_<?= $current_date ?>">
							<?= (!empty($current_date) && $current_date != 'FIRST_DATE' ? $current_date : 'No Date') ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_medication_<?= $current_date ?>" class="panel-collapse collapse">
					<div class="panel-body">
		<?php } ?>
		<div class="multi-block">
			<div class="col-sm-2">
				<label class="show-on-mob">Member:</label>
				<?php if($medication['arrived'] > 0) { ?>
					<input type="text" class="form-control" readonly value="<?= get_contact($dbc, $medication['item_id']) ?>">
				<?php } else { ?>
					<select class="chosen-select-deselect form-control" name="item_id" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table">
						<option></option>
						<?php foreach($member_list as $member) { ?>
							<option <?= $member['contactid'] == $medication['item_id'] ? 'selected' : '' ?> value="<?= $member['contactid'] ?>"><?= $member['first_name'] ?> <?= $member['last_name'] ?></option>
						<?php } ?>
					</select>
				<?php } ?>
			</div>
			<div class="col-sm-2">
				<label class="show-on-mob">Medication:</label>
				<input type="text" class="form-control" name="position" <?= $medication['arrived'] > 0 ? 'readonly' : 'data-table="ticket_attached" data-id="'.$medication['id'].'" data-id-field="id" data-type="medication" data-type-field="src_table"' ?> value="<?= $medication['position'] ?>">
			</div>
			<div class="col-sm-2">
				<label class="show-on-mob">Dosage:</label>
				<input type="text" class="form-control" name="description" <?= $medication['arrived'] > 0 ? 'readonly' : 'data-table="ticket_attached" data-id="'.$medication['id'].'" data-id-field="id" data-type="medication" data-type-field="src_table"' ?> value="<?= $medication['description'] ?>">
			</div>
			<?php if (strpos($value_config, ',Medication Multiple Days,') !== false) { ?>
				<div class="col-sm-2">
					<label class="show-on-mob">Date:</label>
					<input type="text" class="form-control datepicker" name="date_stamp" <?= $medication['arrived'] > 0 ? 'readonly' : 'data-table="ticket_attached" data-id="'.$medication['id'].'" data-id-field="id" data-type="medication" data-type-field="src_table"' ?> value="<?= $medication['date_stamp'] ?>">
				</div>
			<?php } ?>
			<div class="col-sm-<?= strpos($value_config, ',Medication Multiple Days,') !== false ? '2' : '3' ?>">
				<label class="show-on-mob">Time:</label>
				<input type="text" class="form-control datetimepicker" name="shift_start" <?= $medication['arrived'] > 0 ? 'readonly' : 'data-table="ticket_attached" data-id="'.$medication['id'].'" data-id-field="id" data-type="medication" data-type-field="src_table"' ?> value="<?= $medication['shift_start'] ?>">
			</div>
			<div class="col-sm-2">
				<div class="<?= $medication['arrived'] > 0 ? '' : 'toggleSwitch mobile-lg' ?>">
					<input type="hidden" name="arrived" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table" value="<?= $medication['arrived'] ?>" class="toggle">
					<span style="<?= $medication['arrived'] > 0 ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img no-margin"> Not Administered</span>
					<span style="<?= $medication['arrived'] == 1 ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img no-margin"> Administered</span>
					<span style="<?= $medication['arrived'] > 1 ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-8.png" class="text-lg inline-img no-margin"> Not Administered</span>
				</div>
			</div>
			<div class="col-sm-1 pull-right" style="<?= $medication['arrived'] > 0 ? 'display:none;' : '' ?>">
				<input type="hidden" name="deleted" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table" value="0">
				<img class="inline-img pull-right" data-history-label="Medication" onclick="addMulti(this, 'inline');" src="../img/icons/ROOK-add-icon.png">
				<img class="inline-img pull-right" data-history-label="Medication" onclick="noMeds(this);" src="../img/remove.png">
				<a href="" class="all_meds pull-right btn brand-btn" onclick="linkMeds(this); return false;">All Medications</a>
			</div>
			<div class="clearfix"></div>
			<div class="form-group col-sm-12 comment" style="<?= $medication['arrived'] > 1 ? '' : 'display:none;' ?>">
				<label class="col-sm-2">Signed Off By:</label>
				<div class="col-sm-4 img-div" <?= $medication['signature'] != '' ? '' : 'style="display:none;"' ?>>
					<input type="text" class="form-control" name="sign_name" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table" value="<?= $medication['sign_name'] ?>">
					<?php if($medication['signature'] != '' && !file_exists('download/signature_'.$medication['id'].'.png')) {
						include_once('../phpsign/signature-to-image.php');
						$signature = sigJsonToImage(html_entity_decode($medication['signature']));
						imagepng($signature, 'download/signature_'.$medication['id'].'.png');
					}
					if($medication['signature'] != '') { ?>
						<img src="download/signature_<?= $medication['id'] ?>.png">
					<?php } ?>
				</div>
				<div class="col-sm-4 sig-div" <?= $medication['signature'] != '' ? 'style="display:none;"' : '' ?>>
					<div class="select-div" style="<?= $medication['sign_name'] != '' ? 'display:none;' : '' ?>">
						<select name="sign_name" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table" class="chosen-select-deselect"><option></option>
							<?php foreach($staff_list as $staff) {
								if(in_array($staff['contactid'],$available_staff)) { ?>
									<option value="<?= $staff['first_name'].' '.$staff['last_name'] ?>"><?= $staff['first_name'].' '.$staff['last_name'] ?></option>
								<?php }
							} ?>
							<option value="MANUAL">Other Name</option>
						</select>
					</div>
					<div class="manual-div" style="<?= $medication['sign_name'] != '' ? '' : 'display:none;' ?>">
						<input type="text" class="form-control" name="sign_name" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table" value="<?= $medication['sign_name'] ?>" onchange="return signNameUpdate(this);">
					</div>
					<?php $output_name = "signature";
					$sign_output_options = 'data-table="ticket_attached" data-id="'.$medication['id'].'" data-id-field="id" data-type="medication" data-type-field="src_table"';
					include('../phpsign/sign_multiple.php'); ?>
					<button class="btn brand-btn pull-right" onclick="$(this).closest('.multi-block').find('[name=signature]').change(); return false;">Save Signature</button>
				</div>
				<label class="col-sm-2">Explanation:</label>
				<div class="col-sm-6">
					<textarea name="notes" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table"><?= $medication['notes'] ?></textarea>
				</div>
			</div>
			<div class="form-group col-sm-6 administer" style="<?= $medication['arrived'] > 1 ? 'display:none;' : '' ?>">
				<label class="col-sm-4">Administered By:</label>
				<div class="col-sm-8 img-div" <?= $medication['signature'] != '' ? '' : 'style="display:none;"' ?>>
					<input type="text" class="form-control" name="sign_name" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table" value="<?= $medication['sign_name'] ?>">
					<?php if($medication['signature'] != '' && !file_exists('download/signature_'.$medication['id'].'.png')) {
						include_once('../phpsign/signature-to-image.php');
						$signature = sigJsonToImage(html_entity_decode($medication['signature']));
						imagepng($signature, 'download/signature_'.$medication['id'].'.png');
					}
					if($medication['signature'] != '') { ?>
						<img src="download/signature_<?= $medication['id'] ?>.png">
					<?php } ?>
				</div>
				<div class="col-sm-8 sig-div" <?= $medication['signature'] != '' ? 'style="display:none;"' : '' ?>>
					<div class="select-div" style="<?= $medication['sign_name'] != '' ? 'display:none;' : '' ?>">
						<select name="sign_name" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table" class="chosen-select-deselect"><option></option>
							<?php foreach($staff_list as $staff) {
								if(in_array($staff['contactid'],$available_staff)) { ?>
									<option value="<?= $staff['first_name'].' '.$staff['last_name'] ?>"><?= $staff['first_name'].' '.$staff['last_name'] ?></option>
								<?php }
							} ?>
							<option value="MANUAL">Other Name</option>
						</select>
					</div>
					<div class="manual-div" style="<?= $medication['sign_name'] != '' ? '' : 'display:none;' ?>">
						<input type="text" class="form-control" name="sign_name" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table" value="<?= $medication['sign_name'] ?>" onchange="return signNameUpdate(this);">
					</div>
					<?php $output_name = "signature";
					$sign_output_options = 'data-table="ticket_attached" data-id="'.$medication['id'].'" data-id-field="id" data-type="medication" data-type-field="src_table"';
					include('../phpsign/sign_multiple.php'); ?>
					<button class="btn brand-btn pull-right" onclick="$(this).closest('.multi-block').find('[name=signature]').change(); return false;">Save Signature</button>
				</div>
			</div>
			<div class="form-group col-sm-6 witness" style="<?= $medication['arrived'] > 1 ? 'display:none;' : '' ?>">
				<label class="col-sm-4">Witnessed By:</label>
				<div class="col-sm-8 img-div" <?= $medication['witnessed'] != '' ? '' : 'style="display:none;"' ?>>
					<input type="text" class="form-control" name="witness_name" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table" value="<?= $medication['witness_name'] ?>">
					<?php if($medication['witnessed'] != '' && !file_exists('download/witnessed_'.$medication['id'].'.png')) {
						include_once('../phpsign/signature-to-image.php');
						$signature = sigJsonToImage(html_entity_decode($medication['witnessed']));
						imagepng($signature, 'download/witnessed_'.$medication['id'].'.png');
					}
					if($medication['witnessed'] != '') { ?>
						<img src="download/witnessed_<?= $medication['id'] ?>.png">
					<?php } ?>
				</div>
				<div class="col-sm-8 sig-div" <?= $medication['witnessed'] != '' ? 'style="display:none;"' : '' ?>>
					<div class="select-div" style="<?= $medication['witness_name'] != '' ? 'display:none;' : '' ?>">
						<select name="witness_name" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table" class="chosen-select-deselect"><option></option>
							<?php foreach($staff_list as $staff) {
								if(in_array($staff['contactid'],$available_staff)) { ?>
									<option value="<?= $staff['first_name'].' '.$staff['last_name'] ?>"><?= $staff['first_name'].' '.$staff['last_name'] ?></option>
								<?php }
							} ?>
							<option value="MANUAL">Other Name</option>
						</select>
					</div>
					<div class="manual-div" style="<?= $medication['witness_name'] != '' ? '' : 'display:none;' ?>">
						<input type="text" class="form-control" name="witness_name" data-table="ticket_attached" data-id="<?= $medication['id'] ?>" data-id-field="id" data-type="medication" data-type-field="src_table" value="<?= $medication['witness_name'] ?>" onchange="return witnessNameUpdate(this);">
					</div>
					<?php $output_name = "witnessed";
					$sign_output_options = 'data-table="ticket_attached" data-id="'.$medication['id'].'" data-id-field="id" data-type="medication" data-type-field="src_table"';
					include('../phpsign/sign_multiple.php'); ?>
					<button class="btn brand-btn pull-right" onclick="$(this).closest('.multi-block').find('[name=witnessed]').change(); return false;">Save Signature</button>
				</div>
			</div>
			<div class="clearfix"></div>
		</div>
	<?php } while($medication = mysqli_fetch_assoc($medications));
	if(strpos($value_config, ',Medication Group Days,') !== FALSE) { ?>
					</div>
				</div>
			</div>
		</div>
	<?php }
} else {
	$medications = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table`='medication' AND `line_id`='0' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0".$query_daily." ORDER BY `date_stamp`");
	$current_date = 'FIRST_DATE';
	if(strpos($value_config, ',Medication Group Days,') !== FALSE && mysqli_num_rows($medications) > 0) { ?>
		<div id="panel_medication_groups" class="panel-group">
	<?php }
	while($medication = mysqli_fetch_assoc($medications)) {
		if(strpos($value_config, ',Medication Group Days,') !== FALSE && $current_date != $medication['date_stamp']) {
			if($current_date != 'FIRST_DATE') { ?>
						</div>
					</div>
				</div>
			<?php }
			$current_date = $medication['date_stamp']; ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#panel_medication_groups" href="#collapse_medication_<?= $current_date ?>">
							<?= (!empty($current_date) && $current_date != 'FIRST_DATE' ? $current_date : 'No Date') ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>
				<div id="collapse_medication_<?= $current_date ?>" class="panel-collapse collapse">
					<div class="panel-body">
		<?php } ?>
		<div class="multi-block">
			<div class="col-sm-2">
				<label class="show-on-mob">Member:</label>
				<?= get_contact($dbc, $medication['item_id']) ?>
			</div>
			<?php $pdf_contents[] = ['Member', get_contact($dbc, $medication['item_id'])]; ?>
			<div class="col-sm-2">
				<label class="show-on-mob">Medication:</label>
				<input type="text" class="form-control" name="position" readonly value="<?= $medication['position'] ?>">
			</div>
			<?php $pdf_contents[] = ['Medication', $medication['position']]; ?>
			<div class="col-sm-2">
				<label class="show-on-mob">Dosage:</label>
				<input type="text" class="form-control" name="description" readonly value="<?= $medication['description'] ?>">
			</div>
			<?php $pdf_contents[] = ['Dosage', $medication['description']]; ?>
			<?php if (strpos($value_config, ',Medication Multiple Days,') !== false) { ?>
				<div class="col-sm-2">
					<label class="show-on-mob">Date:</label>
					<input type="text" class="form-control" readonly name="date_stamp" value="<?= $medication['date_stamp'] ?>">
				</div>
				<?php $pdf_contents[] = ['Date', $medication['date_stamp']]; ?>
			<?php } ?>
			<div class="col-sm-<?= strpos($value_config, ',Medication Multiple Days,') !== false ? '2' : '3' ?>">
				<label class="show-on-mob">Time:</label>
				<input type="text" class="form-control" name="shift_start" readonly value="<?= $medication['shift_start'] ?>">
			</div>
			<?php $pdf_contents[] = ['Time', $medication['shift_start']]; ?>
			<div class="col-sm-2">
				<span style="<?= $medication['arrived'] > 0 ? 'display: none;' : '' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" class="text-lg inline-img"> Not Administered</span>
				<span style="<?= $medication['arrived'] > 0 ? '' : 'display: none;' ?>"><img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" class="text-lg inline-img"> Administered</span>
			</div>
			<?php $pdf_contents[] = ['Administered?', $medication['arrived'] > 0 ? 'Administered' : 'Not Administered']; ?>
			<div class="clearfix"></div>
			<div class="form-group col-sm-6">
				<label class="col-sm-4">Administered By:</label>
				<div class="col-sm-8 img-div">
					<?= $medication['sign_name'] != '' ? $medication['sign_name'].'<br />' : '' ?>
					<?php if($medication['signature'] != '') {
						if(!file_exists('download/signature_'.$medication['id'].'.png')) {
							include_once('../phpsign/signature-to-image.php');
							$signature = sigJsonToImage(html_entity_decode($medication['signature']));
							imagepng($signature, 'download/signature_'.$medication['id'].'.png');
						}
						echo '<img src="download/signature_'.$medication['id'].'.png">';
					} ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Administered By', ($medication['sign_name'] != '' ? $medication['sign_name'].'<br />' : '').'<img src="download/signature_'.$medication['id'].'.png">']; ?>
			<div class="form-group col-sm-6">
				<label class="col-sm-4">Witnessed By:</label>
				<div class="col-sm-8 img-div">
					<?= $medication['witness_name'] != '' ? $medication['witness_name'].'<br />' : '' ?>
					<?php if($medication['witnessed'] != '') {
						if(!file_exists('download/witnessed_'.$medication['id'].'.png')) {
							include_once('../phpsign/signature-to-image.php');
							$signature = sigJsonToImage(html_entity_decode($medication['witnessed']));
							imagepng($signature, 'download/witnessed_'.$medication['id'].'.png');
						}
						echo '<img src="download/witnessed_'.$medication['id'].'.png">';
					} ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Witnessed By', ($medication['witness_name'] != '' ? $medication['witness_name'].'<br />' : '').'<img src="download/witnessed_'.$medication['id'].'.png">']; ?>
			<div class="clearfix"></div>
		</div>
	<?php }
	if(strpos($value_config, ',Medication Group Days,') !== FALSE && mysqli_num_rows($medications) > 0) { ?>
					</div>
				</div>
			</div>
		</div>
	<?php }
} ?>