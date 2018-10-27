<div class="form-group">
	<label class="col-sm-4 control-label">Frequency Type:</label>
	<div class="col-sm-8">
		<select name="frequency_type" class="chosen-select-deselect form-control" data-placeholder="Select Frequency Type">
			<option></option>
			<option value="Day" <?= $frequency_type == 'Day' ? 'selected' : '' ?>>Day</option>
			<option value="Week" <?= $frequency_type == 'Week' ? 'selected' : '' ?>>Week</option>
			<option value="Month" <?= $frequency_type == 'Month' ? 'selected' : '' ?>>Month</option>
			<option value="Quarter" <?= $frequency_type == 'Quarter' ? 'selected' : '' ?>>Quarter</option>
			<option value="Year" <?= $frequency_type == 'Year' ? 'selected' : '' ?>>Year</option>
		</select>
	</div>
</div>

<div class="form-group">
	<label class="col-sm-4 control-label">Frequency Interval:</label>
	<div class="col-sm-8">
		<input type="number" name="frequency_interval" class="form-control" value="<?= $frequency_interval ?>" min="0" placeholder="Enter Frequency">
	</div>
</div>