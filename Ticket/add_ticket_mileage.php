<h3><?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : (strpos($value_config, ','."Mileage".',') !== FALSE ? 'Mileage' : 'Drive Time')) ?></h3>
<?php $mileage_list = mysqli_query($dbc, "SELECT * FROM `mileage` WHERE `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted`=0");
$mileage = mysqli_fetch_assoc($mileage_list);
$mile_config = explode(',',get_config($dbc, 'mileage_fields'));
do {
	if($access_all > 0) { ?>
		<div class="multi-block">
			<?php if(in_array('staff',$mile_config)) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Driver:</label>
					<div class="col-sm-8">
						<select class="chosen-select-deselect" data-placeholder="Select a Driver..." name="staffid" data-table="mileage" data-id="<?= $mileage['id'] ?>" data-id-field="id" data-attach="<?= $ticketid ?>" data-attach-field="ticketid" ><option></option>
							<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `contactid` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND `deleted`=0")) as $contact) { ?>
								<option <?= $contact['contactid'] == $mileage['staffid'] ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['first_name'].' '.$contact['last_name'] ?></option>
							<?php } ?>
						</select>
					</div>
				</div><?php } ?>
			<?php if(in_array('startdate',$mile_config)) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Start:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control dateandtimepicker" name="start" data-table="mileage" data-id="<?= $mileage['id'] ?>" data-id-field="id" data-attach="<?= $ticketid ?>" data-attach-field="ticketid" value="<?= empty($mileage['start']) ? date('Y-m-d h:i a') : date('Y-m-d h:i a', strtotime($mileage['start'])) ?>">
					</div>
				</div><?php } ?>
			<?php if(in_array('enddate',$mile_config)) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">End:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control dateandtimepicker" name="end" data-table="mileage" data-id="<?= $mileage['id'] ?>" data-id-field="id" data-attach="<?= $ticketid ?>" data-attach-field="ticketid" value="<?= empty($mileage['start']) && empty($mileage['end']) ? date('Y-m-d h:i a') : date('Y-m-d h:i a', strtotime($mileage['end'])) ?>">
					</div>
				</div><?php } ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Mileage:</label>
				<div class="col-sm-8">
					<input type="number" min="0" max="86400" step="0.05" class="form-control" name="mileage" data-table="mileage" data-id="<?= $mileage['id'] ?>" data-id-field="id" data-attach="<?= $ticketid ?>" data-attach-field="ticketid" value="<?= $mileage['mileage'] ?>">
				</div>
			</div>
			<?php if(in_array('category',$mile_config)) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Tab:</label>
					<div class="col-sm-8">
					<select class="chosen-select-deselect" data-placeholder="Select a Tab..." name="category" data-table="mileage" data-id="<?= $mileage['id'] ?>" data-id-field="id" data-attach="<?= $ticketid ?>" data-attach-field="ticketid" ><option></option>
						<?php foreach($categories as $category) { ?>
							<option <?= $category == $mileage['category'] ? 'selected' : '' ?> value="<?= $category ?>"><?= $category ?></option>
						<?php } ?>
						<option value="MANUAL">Add Tab</option>
					</select>
						<input type="text" class="form-control" name="category" data-table="mileage" data-id="<?= $mileage['id'] ?>" data-id-field="id" data-attach="<?= $ticketid ?>" data-attach-field="ticketid" value="" style="display:none;">
					</div>
				</div><?php } ?>
			<?php if(in_array('details',$mile_config)) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Details:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="details" data-table="mileage" data-id="<?= $mileage['id'] ?>" data-id-field="id" data-attach="<?= $ticketid ?>" data-attach-field="ticketid" value="<?= $mileage['details'] ?>">
					</div>
				</div><?php } ?>
			<?php if(in_array('rate',$mile_config)) {
				if($projectid > 0) {
					$rate = explode('*',get_field_value('ratecardid','project','projectid',$projectid));
					if($rate[0] > 0) {
						$rate['cust_price'] = 0;
					} else if($rate[0] == 'company') {
						$rate = $dbc->query("SELECT `cust_price` FROM `company_rate_card` WHERE `deleted`=0 AND `tile_name`='Mileage' AND `rate_card_name` IN (SELECT `rate_card_name` FROM `company_rate_card` WHERE `companyrcid`='{$rate[1]}')")->fetch_assoc();
					} else {
						$rate = $dbc->query("SELECT `cust_price` FROM `company_rate_card` WHERE `deleted`=0 AND `tile_name`='Mileage' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')")->fetch_assoc();
					}
				} else {
					$rate = $dbc->query("SELECT `cust_price` FROM `company_rate_card` WHERE `deleted`=0 AND `tile_name`='Mileage' AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')")->fetch_assoc();
				} ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Cost:</label>
					<div class="col-sm-8">
						<input type="text" class="form-control" name="cost" data-rate="<?= $rate['cust_price'] ?>" readonly value="<?= number_format($mileage['mileage']*$rate['cust_price'],2) ?>">
					</div>
				</div><?php } ?>
			<?php if(in_array('contact',$mile_config)) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Client:</label>
					<div class="col-sm-8">
						<select class="chosen-select-deselect" data-placeholder="Select a Client..." name="contactid" data-table="mileage" data-id="<?= $mileage['id'] ?>" data-id-field="id" data-attach="<?= $ticketid ?>" data-attach-field="ticketid" ><option></option>
							<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `name`, `contactid` FROM `contacts` WHERE `category` NOT IN (".STAFF_CATS.") AND `status`>0 AND `deleted`=0 AND CONCAT(`name`,`last_name`,`first_name`) != ''")) as $contact) { ?>
								<option <?= $contact['contactid'] == $mileage['contactid'] ? 'selected' : '' ?> value="<?= $contact['contactid'] ?>"><?= $contact['name'] != '' ? $contact['name'] : $contact['first_name'].' '.$contact['last_name'] ?></option>
							<?php } ?>
						</select>
					</div>
				</div><?php } ?>
			<?php if(in_array('double_mileage',$mile_config)) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">KMx2:</label>
					<div class="col-sm-8">
						<input type="number" readonly class="form-control" name="double_mileage" data-table="mileage" data-id="<?= $mileage['id'] ?>" data-id-field="id" data-attach="<?= $ticketid ?>" data-attach-field="ticketid" value="<?= $mileage['double_mileage'] ?>">
					</div>
				</div><?php } ?>
			<div class="col-sm-1 pull-right">
					<input type="hidden" name="deleted" data-table="mileage" data-id="<?= $mileage['id'] ?>" data-id-field="id" data-attach="<?= $ticketid ?>" data-attach-field="ticketid" value="0">
				<img class="inline-img pull-right" src="../img/icons/ROOK-add-icon.png" data-history-label="Mileage" onclick="addMulti(this);">
				<img class="inline-img pull-right" src="../img/remove.png" data-history-label="Mileage" onclick="remMulti(this);">
			</div>
			<div class="clearfix"></div>
		</div>
	<?php } else { ?>
		<?php if(in_array('staff',$mile_config)) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Driver:</label>
				<div class="col-sm-8">
					<?= get_contact($dbc, $mileage['staffid']) ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Driver', get_contact($dbc, $mileage['contactid'])]; ?>
		<?php } ?>
		<?php if(in_array('startdate',$mile_config)) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Start:</label>
				<div class="col-sm-8">
					<?= !empty($mileage['start']) ? date('Y-m-d h:i a', strotime($mileage['start'])) : '' ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Start', !empty($mileage['start']) ? date('Y-m-d h:i a', strotime($mileage['start'])) : '']; ?>
		<?php } ?>
		<?php if(in_array('enddate',$mile_config)) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">End:</label>
				<div class="col-sm-8">
					<?= !empty($mileage['end']) ? date('Y-m-d h:i a', strotime($mileage['end'])) : '' ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['End', $mileage['end']]; ?>
		<?php } ?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Mileage:</label>
			<div class="col-sm-8">
				<?= !empty($mileage['end']) ? date('Y-m-d h:i a', strotime($mileage['end'])) : '' ?>
			</div>
		</div>
		<?php $pdf_contents[] = ['Mileage', $mileage['mileage']]; ?>
		<?php if(in_array('category',$mile_config)) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Tab:</label>
				<div class="col-sm-8">
					<?= $category ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Category', $category]; ?>
		<?php } ?>
		<?php if(in_array('details',$mile_config)) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Details:</label>
				<div class="col-sm-8">
					<?= $mileage['details'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Details', $mileage['details']]; ?>
		<?php } ?>
		<?php if(in_array('contact',$mile_config)) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Client:</label>
				<div class="col-sm-8">
					<?= !empty(get_client($dbc, $mileage['contactid'])) ? get_client($dbc, $mileage['contactid']) : get_contact($dbc,$mileage['contactid']) ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Client', !empty(get_client($dbc, $mileage['contactid'])) ? get_client($dbc, $mileage['contactid']) : get_contact($dbc,$mileage['contactid'])]; ?>
		<?php } ?>
		<?php if(in_array('double_mileage',$mile_config)) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">KMx2:</label>
				<div class="col-sm-8">
					<?= $mileage['double_mileage'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['KMx2', $mileage['double_mileage']]; ?>
		<?php } ?>
		<div class="clearfix"></div>
	<?php }
} while($mileage = mysqli_fetch_assoc($mileage_list)); ?>