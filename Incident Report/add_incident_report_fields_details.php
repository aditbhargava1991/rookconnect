
  <div class="form-group" <?= (strpos($hide_config, ',Type,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="company_name" class="col-sm-4 control-label">Type<span class="text-red">*</span>:</label>
	<div class="col-sm-8">
		<select id="category" name="<?= $is_userform ?>type" class="chosen-select-deselect form-control" width="380">
			<option value=''></option>
			<?php foreach(str_getcsv(html_entity_decode($get_field_config['incident_types']), ',') as $in_type) {
				echo "<option ".($in_type == $type ? 'selected' : '')." value='$in_type'>$in_type</option>";
			} ?>
		</select>
	</div>
  </div>

  <?php if (strpos($value_config, ','."Completed By".',') !== FALSE) { ?>
	<?php foreach(explode(',',$completed_by) as $completed_id) { ?>
	  <div class="form-group patient" <?= (strpos($hide_config, ',Completed By,') !== FALSE ? 'style="display:none;"' : '') ?>>
		<label for="clientid" class="col-sm-4 control-label">Completed By<span class="text-red">*</span>:</label>
		<div class="col-sm-7">
			<select data-placeholder="Select Staff..." id="completed_by" name="<?= $is_userform ?>completed_by[]" class="chosen-select-deselect form-control" width="380">
				<option value=""></option>
				<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
				foreach($staff_list as $id) {
					$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$id'"));
					echo "<option ".($completed_id == $id ? 'selected' : '')." value='". $id."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
				} ?>
			</select>
		</div>
		<div class="col-sm-1">
			<img class="inline-img cursor-hand" src="../img/icons/ROOK-add-icon.png" onclick="addRow(this);">
		</div>
	  </div>
	<?php } ?>
  <?php } ?>

  <?php if (strpos($value_config, ','."Date of Happening".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Date of Happening,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label">Date of Happening:</label>
	<div class="col-sm-8">
		<input type="text" name="<?= $is_userform ?>date_of_happening" class="form-control datepicker" value="<?php echo $date_of_happening; ?>">
	</div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Date of Report".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Date of Report,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label">Date of Report:</label>
	<div class="col-sm-8">
		<input type="text" name="<?= $is_userform ?>date_of_report" class="form-control datepicker" value="<?php echo $date_of_report; ?>">
	</div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Program".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Program,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="programid" class="col-sm-4 control-label">Program<span class="text-red">*</span>:</label>
	<div class="col-sm-8">
		<select data-placeholder="Select Program..." id="programid" name="<?= $is_userform ?>programid" class="chosen-select-deselect form-control" width="380">
			<option value=""></option>
			<?php $program_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category='Programs' AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
			foreach($program_list as $id) {
				$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$id'"));
				echo "<option ".($programid == $id ? 'selected' : '')." value='". $id."'>".(empty($row['name']) ? decryptIt($row['first_name']).' '.decryptIt($row['last_name']) : decryptIt($row['name'])).'</option>';
			} ?>
		</select>
	</div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Project Type".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Project Type,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label"><?= PROJECT_NOUN ?> Type:</label>
	<div class="col-sm-8">
		<select data-placeholder="Select <?= PROJECT_NOUN ?> Type..." id="project_type" name="<?= $is_userform ?>project_type" class="chosen-select-deselect form-control" width="380">
			<option value=""></option>
			<?php $project_tabs = get_config($dbc, 'project_tabs');
			if($project_tabs == '') {
				$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
			}
			$project_tabs = explode(',',$project_tabs);
			$project_vars = [];
			foreach($project_tabs as $item) {
				$project_vars[] = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
			}
			foreach($project_tabs as $project_i => $project_tab) {
				echo "<option ".($project_type == $project_vars[$project_i] ? 'selected' : '')." value='".$project_vars[$project_i]."'>".$project_tab."</option>";
			} ?>
		</select>
	</div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Project".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Project,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label"><?= PROJECT_NOUN ?>:</label>
	<div class="col-sm-8">
		<select data-placeholder="Select <?= PROJECT_NOUN ?>..." id="projectid" name="<?= $is_userform ?>projectid" class="chosen-select-deselect form-control" width="380">
			<option value=""></option>
			<?php $project_list = mysqli_fetch_all(mysqli_query($dbc,"SELECT * FROM `project` WHERE `deleted` = 0 ORDER BY `project_name`"),MYSQLI_ASSOC);
			foreach($project_list as $project) {
				echo "<option data-projecttype='".$project['projecttype']."' ".($projectid == $project['projectid'] ? 'selected' : '')." value='". $project['projectid']."'>".get_project_label($dbc, $project).'</option>';
			} ?>
		</select>
	</div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Ticket".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Ticket,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label"><?= TICKET_NOUN ?>:</label>
	<div class="col-sm-8">
		<select data-placeholder="Select <?= TICKET_TILE ?>..." id="ticketid" name="<?= $is_userform ?>ticketid" class="chosen-select-deselect form-control" width="380">
			<option value=""></option>
			<?php $ticket_list = mysqli_fetch_all(mysqli_query($dbc,"SELECT * FROM `tickets` WHERE `deleted` = 0 AND (`status` NOT IN ('Done','Archive') OR `ticketid` = '".$ticketid."') ORDER BY `heading`"),MYSQLI_ASSOC);
			foreach($ticket_list as $ticket) {
				$project_type = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` = '".$ticket['projectid']."'"))['projecttype'];
				echo "<option data-projecttype='".$project_type."' data-projectid='".$ticket['projectid']."' ".($ticketid == $ticket['ticketid'] ? 'selected' : '')." value='". $ticket['ticketid']."' ".(!empty($projectid) && $projectid != $ticket['projectid'] ? 'style="display:none;"' : '').">".get_ticket_label($dbc, $ticket).'</option>';
			} ?>
		</select>
	</div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Client".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Client,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="clientid" class="col-sm-4 control-label">Client(s) Involved<span class="text-red">*</span>:</label>
	<div class="col-sm-8">
		<select data-placeholder="Select Clients..." multiple id="clientid" name="<?= $is_userform ?>clientid[]" class="chosen-select-deselect form-control" width="380">
			<?php $client_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category NOT IN (".STAFF_CATS.") AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
			foreach($client_list as $id) {
				$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name`, `first_name`, `last_name` FROM `contacts` WHERE `contactid`='$id'"));
				echo "<option ".(strpos(','.$clientid.',',','.$id.',') !== FALSE ? 'selected' : '')." value='". $id."'>".(empty($row['name']) ? decryptIt($row['first_name']).' '.decryptIt($row['last_name']) : decryptIt($row['name'])).'</option>';
			} ?>
		</select>
	</div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Member".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Member,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="memberid" class="col-sm-4 control-label">Member(s) Involved<span class="text-red">*</span>:</label>
	<div class="col-sm-8">
		<select data-placeholder="Select Members..." multiple id="memberid" name="<?= $is_userform ?>memberid[]" class="chosen-select-deselect form-control" width="380">
			<?php $member_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category='Members' AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
			foreach($member_list as $id) {
				$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT `name`, `first_name`, `last_name`, `businessid` FROM `contacts` WHERE `contactid`='$id'"));
				echo "<option data-businessid='".$row['businessid']."' ".(strpos(','.$memberid.',',','.$id.',') !== FALSE ? 'selected' : '')." value='". $id."'>".(empty($row['name']) ? decryptIt($row['first_name']).' '.decryptIt($row['last_name']) : decryptIt($row['name'])).'</option>';
			} ?>
		</select>
	</div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Equipment".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Equipment,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="clientid" class="col-sm-4 control-label">Equipment<span class="text-red"></span>:</label>
	<div class="col-sm-8">
		<select multiple data-placeholder="Select Equipment..." id="clientid" name="<?= $is_userform ?>equipmentid[]" class="chosen-select-deselect form-control" width="380">
			<?php $equip_query = mysqli_query($dbc, "SELECT `equipmentid`,`unit_number`,`make`,`model`,`licence_plate` FROM `equipment` WHERE `deleted`=0");
			while($equip_row = mysqli_fetch_array($equip_query)) {
				echo "<option ".(strpos(','.$equipmentid.',', ','.$equip_row['equipmentid'].',') !== FALSE ? 'selected' : '')." value='".$equip_row['equipmentid']."'>".$equip_row['make']." ".$equip_row['model']." Unit #".$equip_row['unit_number']." (Licence Plate ".$equip_row['licence_plate'].")</option>";
			} ?>
		</select>
	</div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Driver".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Driver,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label"><?= (strpos($value_config, ','."Driver_WorkerLabel".',') !== FALSE ? 'Worker/ Operator' : 'Driver') ?><span class="text-red">*</span>:</label>
	<div class="col-sm-8">
		<select data-placeholder="Select Staff..." multiple id="contactid" name="<?= $is_userform ?>contactid[]" class="chosen-select-deselect form-control" width="380">
			<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
			foreach($staff_list as $id) {
				$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$id'"));
				echo "<option ".(strpos(','.$contactid.',',','.$id.',') !== FALSE ? 'selected' : '')." value='". $id."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' (Licence #'.$row['license'].', Address: ';
				echo (!empty($row['address']) ? $row['address'] : (!empty($row['mailing_address']) ? $row['mailing_address'] : (!empty($row['business_address']) ? $row['business_address'] : $row['ship_to_address']))).')</option>';
			} ?>
		</select>
	</div>
  </div>
  <?php } else if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Staff,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label"><?= (strpos($value_config, ','."Staff_InvolvedLabel".',') !== FALSE ? 'Staff Involved' : 'Staff') ?><span class="text-red">*</span>:</label>
	<div class="col-sm-8">
		<select data-placeholder="Select Staff..." multiple id="contactid" name="<?= $is_userform ?>contactid[]" class="chosen-select-deselect form-control" width="380">
			<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
			foreach($staff_list as $id) {
				$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$id'"));
				echo "<option ".(strpos(','.$contactid.',',','.$id.',') !== FALSE ? 'selected' : '')." value='". $id."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
			} ?>
		</select>
	</div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Workers Involved".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Workers Involved,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label">Workers Involved:</label>
	<div class="col-sm-8">
		<select data-placeholder="Select Workers..." multiple id="workerid" name="<?= $is_userform ?>workerid[]" class="chosen-select-deselect form-control" width="380">
			<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, name, first_name, last_name, category FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND deleted=0"),MYSQLI_ASSOC));
			foreach($staff_list as $id) {
				$row = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$id'"));
				echo "<option ".(strpos(','.$workerid.',',','.$id.',') !== FALSE ? 'selected' : '')." value='". $id."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
			} ?>
		</select>
	</div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Others".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Others,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label">Others:</label>
	<div class="col-sm-8">
		<input type="text" name="<?= $is_userform ?>other_names" class="form-control" value="<?= $other_names ?>">
	</div>
  </div>
  <?php } ?>

  <?php if (strpos($value_config, ','."Other Driver".',') !== FALSE) { ?>
  <div class="form-group patient" <?= (strpos($hide_config, ',Other Driver,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label">Other Driver's Name:</label>
	<div class="col-sm-8">
		<input type="text" name="<?= $is_userform ?>other_driver_name" class="form-control" value="<?= $other_driver_name ?>">
	</div>
  </div>
  <div class="form-group patient" <?= (strpos($hide_config, ',Other Driver,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label">Other Driver's Address:</label>
	<div class="col-sm-8">
		<input type="text" name="<?= $is_userform ?>other_driver_address" class="form-control" value="<?= $other_driver_address ?>">
	</div>
  </div>
  <div class="form-group patient" <?= (strpos($hide_config, ',Other Driver,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label">Other Driver's Licence #:</label>
	<div class="col-sm-8">
		<input type="text" name="<?= $is_userform ?>other_driver_licence" class="form-control" value="<?= $other_driver_licence ?>">
	</div>
  </div>
  <div class="form-group patient" <?= (strpos($hide_config, ',Other Driver,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label">Other Driver's Insurance Company:</label>
	<div class="col-sm-8">
		<input type="text" name="<?= $is_userform ?>other_driver_ins_company" class="form-control" value="<?= $other_driver_ins_company ?>">
	</div>
  </div>
  <div class="form-group patient" <?= (strpos($hide_config, ',Other Driver,') !== FALSE ? 'style="display:none;"' : '') ?>>
	<label for="site_name" class="col-sm-4 control-label">Other Driver's Insurance Policy:</label>
	<div class="col-sm-8">
		<input type="text" name="<?= $is_userform ?>other_driver_ins_policy" class="form-control" value="<?= $other_driver_ins_policy ?>">
	</div>
  </div>
  <?php } ?>

	<?php if (strpos($value_config, ','."Individuals Witnesses".',') !== FALSE) { ?>
		<h4>Witness(s) Statement</h4>

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Witness(s) Name / Phone Number:</label>
			<div class="col-sm-8">
			  <input type="text" name="<?= $is_userform ?>witness_names" class="form-control" value="<?php echo $witness_names; ?>">
			</div>
		  </div>

		  <div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Witness(s) Statement:</label>
			<div class="col-sm-8">
			  <textarea name="<?= $is_userform ?>ir5" rows="4" cols="50" class="form-control" ><?php echo $ir5; ?></textarea>
			</div>
		  </div>
	<?php } ?>
