<script>
	$(document).on('change', 'select[name="role[]"]', function() { changeRole(this); });
	function changeRole(sel) {
		var role     = $(sel).val().join(',');
		var category = $(sel).data('category');

		$.ajax({
			type: "GET",
			url: "security_ajax_all.php?fill=change_role_contact_cat&role="+role+"&category="+category,
			dataType: "html",
			success: function(response){
			}
		});
	}
</script>

<div id="no-more-tables">
	<table class="table table-bordered table-striped">
        <thead>
            <tr class="hidden-xs">
                <th>Contact Category</th>
                <th>Default Security Level</th>
            </tr>
        </thead>
		<?php $on_security = get_security_levels($dbc);
		$category_list = mysqli_query($dbc, "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 ORDER BY `category`");
		while($category = mysqli_fetch_assoc($category_list)) {
			if(!empty($category['category'])) { ?>
				<tr>
					<td data-title="Contact Category"><?= $category['category'] ?></td>
					<td data-title="Default Security Level">
						<select name="role[]" multiple data-category="<?= $category['category'] ?>" class="chosen-select-deselect form-control">
							<?php $category_roles = explode(',',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_security_contact_categories` WHERE `category` = '{$category['category']}'"))['role']);
							foreach($on_security as $label => $value) { ?>
								<option <?= $value == 'super' ? 'disabled' : '' ?> <?= in_array($value, $category_roles) ? 'selected' : '' ?> value="<?= $value ?>"><?= $label ?></option>
							<?php } ?>
						</select>
					</td>
			<?php }
		} ?>
	</table>
</div>