<?php if(count($user_forms) > 0) {
	echo '<h4>Add Forms as Subtabs</h4>';
	$project_forms = mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `project_heading` = '".$project_heading."' AND (`project_type` = '".$projecttype."' OR `project_type` = 'ALL') ORDER BY `project_type` <> 'ALL'");
	$row = mysqli_fetch_array($project_forms);
	do { ?>
		<div class="user_form_block">
			<input type="hidden" name="project_form_id" value="<?= $row['id'] ?>">
			<input type="hidden" name="project_heading" value="<?= $project_heading ?>">
			<div class="col-sm-5 <?= $row['project_type'] == 'ALL' && $projecttype != 'ALL' ? 'readonly-block' : '' ?>">
				<select name="user_form_id" data-placeholder="Select a Form" class="chosen-select-deselect">
					<option></option>
					<?php foreach($user_forms as $user_form) { ?>
						<option value="<?= $user_form['form_id'] ?>" <?= $row['user_form_id'] == $user_form['form_id'] ? 'selected' : '' ?>><?= $user_form['name'] ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-sm-5 <?= $row['project_type'] == 'ALL' && $projecttype != 'ALL' ? 'readonly-block' : '' ?>">
				<input type="text" name="subtab_name" placeholder="Subtab Name" value="<?= $row['subtab_name'] ?>" class="form-control">
			</div>
			<div class="col-sm-2">
				<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addUserForm(this);">
				<img src="../img/remove.png" class="inline-img pull-right <?= $row['project_type'] == 'ALL' && $projecttype != 'ALL' ? 'readonly-block' : '' ?>" onclick="removeUserForm(this);">
			</div>
		</div>
	<?php } while($row = mysqli_fetch_array($project_forms));
} ?>