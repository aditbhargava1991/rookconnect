<?php $pr_fields = ','.get_config($dbc, 'performance_review_fields').',';
$pr_positions = ','.get_config($dbc, 'performance_review_positions').',';
// $pr_forms = ','.get_config($dbc, 'performance_review_forms').',';
$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1"));
?>
<script>
$(document).ready(function() {
	$('input,select').change(saveFields);
});
function saveFields() {
	var pr_fields = [];
	$('[name="pr_fields[]"]:checked').each(function() {
		pr_fields.push(this.value);
	});
	var pr_positions = [];
	$('[name="pr_positions[]"]:checked').each(function() {
		pr_positions.push(this.value);
	});
	// var pr_forms = [];
	// $('[name="pr_forms[]"]:checked').each(function() {
	// 	pr_forms.push(this.value);
	// });
	var pr_forms = [];
	$('.pr_div').each(function() {
		var user_form_id = $(this).find('[name="user_form_id"]').val();
		var enabled = $(this).find('[name="pr_forms[]"]:checked').val();
		var limit_staff = [];
		$(this).find('[name="limit_staff"] option:selected').each(function() {
			limit_staff.push(this.value);
		});
		limit_staff = limit_staff.join(',');

		var pr_form = { user_form_id: user_form_id, enabled: enabled, limit_staff: limit_staff };
		pr_forms.push(pr_form);
	});
	pr_forms = JSON.stringify(pr_forms);

	$.ajax({
		url: 'hr_ajax.php?action=pr_settings',
		method: 'POST',
		data: {
			pr_fields: pr_fields,
			pr_positions: pr_positions,
			pr_forms: pr_forms
		}
	});
}
</script>
<div class="standard-body-title">
    <h1>Performance Reviews</h1>
</div>
<div class="standard-body-content">
	<div class="form-group">
		<label class="control-label col-sm-4">Enable Performance Reviews:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="pr_fields[]" class="form-control" value="Enable Performance Reviews" <?= strpos($pr_fields, ',Enable Performance Reviews,') !== FALSE ? 'checked' : '' ?>></label>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-4">Use As Tile:</label>
		<div class="col-sm-8">
			<label class="form-checkbox"><input type="checkbox" name="pr_fields[]" class="form-control" value="Use As Tile" <?= strpos($pr_fields, ',Use As Tile,') !== FALSE ? 'checked' : '' ?>></label>
		</div>
	</div>
	<div class="form-group">
		<label class="control-label col-sm-4">Positions:</label>
		<div class="col-sm-8">
			<div class="block-group">
				<?php $positions = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `positions` WHERE `deleted` = 0"),MYSQLI_ASSOC);
				foreach ($positions as $position) { ?>
					<label class="form-checkbox"><input type="checkbox" name="pr_positions[]" class="form-control" value="<?= $position['name'] ?>" <?= strpos($pr_positions, ','.$position['name'].',') !== FALSE ? 'checked' : '' ?>> <?= $position['name'] ?></label>
				<?php } ?>
			</div>
		</div>
	</div>
	<hr>

	<h1>Forms</h1>
	<?php $user_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE CONCAT(',',`assigned_tile`,',') LIKE '%,performance_review,%' AND `deleted` = 0 AND `is_template` = 0"),MYSQLI_ASSOC);
		if(!empty($user_forms)) {
			foreach ($user_forms as $user_form) {
				$pr_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_performance_reviews` WHERE `user_form_id` = '".$user_form['form_id']."'")); ?>
				<div class="form-group pr_div">
					<input type="hidden" name="user_form_id" value="<?= $user_form['form_id'] ?>">
					<label class="control-label col-sm-4"><?= $user_form['name'] ?>:</label>
					<div class="col-sm-8">
						<label class="form-checkbox"><input type="checkbox" name="pr_forms[]" class="form-control" value="1" <?= $pr_config['enabled'] == 1 ? 'checked' : '' ?> onchange="if($(this).is(':checked')) { $(this).closest('.pr_div').find('.pr_settings').show(); } else { $(this).closest('.pr_div').find('.pr_settings').hide(); }"> Enable</label>
						<div class="block-group pr_settings" <?= $pr_config['enabled'] != 1 ? 'style="display:none;"' : '' ?>>
							<div class="form-group">
								<label class="col-sm-4 control-label">Limit Staff View:</label>
								<div class="col-sm-8">
									<select name="limit_staff" multiple class="chosen-select-deselect">
										<?php foreach($staff_list as $staff) {
											echo '<option value="'.$staff['contactid'].'" '.(strpos(','.$pr_config['limit_staff'].',', ','.$staff['contactid'].',') !== FALSE ? 'selected' : '').'>'.$staff['full_name'].'</option>';
										} ?>
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
				<hr>
			<?php }
		} else {
			echo '<h4>No Performance Review Forms Found.</h4>';
		} ?>
</div>