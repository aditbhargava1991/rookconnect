<input type="hidden" id="form_name" name="form_name" value="Manual">
<div class="form-group">
	<label for="fax_number" class="col-sm-4 control-label">Tab/Category:</label>
	<div class="col-sm-8">
		<select data-placeholder="Select Tab..." id="tab_field" name="tab_field" class="chosen-select-deselect form-control" width="380">
			<option value=""></option>
			<option <?php if ($tab == "Toolbox") { echo " selected"; } ?> value="Toolbox">Toolbox</option>
			<option <?php if ($tab == "Tailgate") { echo " selected"; } ?> value="Tailgate">Tailgate</option>
			<option <?php if ($tab == "Form") { echo " selected"; } ?> value="Form">Form</option>
			<option <?php if ($tab == "Manual") { echo " selected"; } ?> value="Manual">Manual</option>
			<?php foreach($categories as $custom_cat) {
				if(!in_array($custom_cat, ['Driving Log','FLHA','Toolbox','Tailgate','Form','Manuals','Incident Reports','Pinned','Favourites'])) { ?>
					<option <?php if ($tab == $custom_cat) { echo " selected"; } ?> value="<?= $custom_cat ?>"><?= $custom_cat ?></option>
				<?php }
			} ?>
		</select>
	</div>
</div>
<?php include ('manual_basic_field.php'); ?>

<?php if (strpos($value_config, ','."Detail".',') !== FALSE) { ?>
	<div class="form-group">
		<label for="first_name[]" class="col-sm-4 control-label">Detail:</label>
		<div class="col-sm-8">
			<textarea name="description" rows="5" cols="50" class="form-control"><?php echo $description; ?></textarea>
		</div>
	</div>
<?php } ?>

<?php if (strpos($value_config, ','."Document".',') !== FALSE) {
	include ('manual_document_field.php');
} ?>

<?php if (strpos($value_config, ','."Link".',') !== FALSE) {
	include ('manual_link_field.php');
} ?>

<?php if (strpos($value_config, ','."Videos".',') !== FALSE) {
	include ('manual_video_field.php');
} ?>

<?php if (strpos($value_config, ','."Staff".',') !== FALSE) { ?>
	<div class="form-group clearfix completion_date">
		<label for="first_name" class="col-sm-4 control-label text-right">Assign Staff:</label>
		<div class="col-sm-8">
			<select name="assign_staff[]" data-placeholder="Choose a Staff Member..." class="chosen-select-deselect form-control" multiple width="380">
				<?php
				$query = mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND deleted=0 ORDER BY first_name");
				while($row = mysqli_fetch_array($query)) {
					if ( !empty ( $assign_staff ) ) { ?>
						<option <?php if (strpos(','.$assign_staff.',', ','.$row['contactid'].',') !== FALSE) { echo ' selected="selected"'; } ?> value="<?php echo $row['contactid']; ?>"><?php echo decryptIt($row['first_name']) . ' ' . decryptIt($row['last_name']); ?></option><?php
					} else { ?>
						<option value="<?= $row['contactid']; ?>"><?= decryptIt($row['first_name']) . ' ' . decryptIt($row['last_name']); ?></option><?php
					}
				} ?>
			</select>
		</div>
	</div>
<?php } ?>

<?php if (strpos($value_config, ','."Review Deadline".',') !== FALSE) { ?>
    <div class="form-group clearfix">
        <label for="first_name" class="col-sm-4 control-label text-right">Review Deadline:</label>
        <div class="col-sm-8">
            <input name="deadline" type="text" class="datepicker form-control" value="<?php echo $deadline; ?>"></p>
        </div>
    </div>
<?php } ?>

<div class="form-group">
  <p><span class="hp-red"><em>Required Fields *</em></span></p>
  <button type="submit" name="add_manual" value="Submit" class="btn brand-btn pull-right">Submit</button>
</div>