<?php include_once('../include.php');
checkAuthorised('staff'); ?>
<script>
$(document).ready(function() {
	$('input[type=checkbox]').change(function() {
		var tab = 'Profile';
		var subtab = $(this).data('subtab');
		var accordion = $(this).data('accordion');
		var field = 'contacts';
		var value = [];
		$('input[data-subtab="'+subtab+'"][data-accordion="'+accordion+'"]:checked').each(function() {
			value.push(this.value);
		});
		value = ','+value.join(',')+',';
		$.ajax({
			url: 'staff_ajax.php?action=field_config',
			method: 'POST',
			data: {
				tab: tab,
				subtab: subtab,
				accordion: accordion,
				field: field,
				value: value
			}
		});
	});
});
</script>
<div class="notice double-gap-bottom double-gap-top popover-examples">
	<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
	<div class="col-sm-16"><span class="notice-name">NOTE:</span>
	All of these fields are visible in the My Profile tile. Checking or unchecking them here will make them editable through the My Profile tile by the currently logged in Staff Member.</div>
</div>
<div class="col-sm-12">
	<?php $staff_categories = array_filter(explode(',',str_replace(',,',',',str_replace('Staff','',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT categories FROM field_config_contacts WHERE tab='Staff' AND `categories` IS NOT NULL"))['categories']))));
	$query_all_fields = "SELECT `tab`, `subtab`, `accordion`, `contacts`, `order` FROM `field_config_contacts` `main_table` WHERE `tab` LIKE 'Staff%' AND REPLACE(`contacts`,',','') != '' AND `subtab` != '' AND `subtab` != '**no_subtab**' AND (`tab` = 'Staff' OR `accordion` NOT IN (SELECT `accordion` FROM `field_config_contacts` `other_table` WHERE `tab` = 'Staff' AND IFNULL(`accordion`,'') != '' AND `main_table`.`subtab` = `other_table`.`subtab`)) ORDER BY IFNULL(`order`,`configcontactid`)";
	$result_all_fields = mysqli_query($dbc, $query_all_fields);
	$i = 0;
	while($row_fields = mysqli_fetch_assoc($result_all_fields)) {
		$i++;
		$row_edit = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab`='Profile' AND `subtab` = '{$row_fields['subtab']}' AND `accordion` = '{$row_fields['accordion']}'")); ?>
		<h3><?= ucwords($row_fields['accordion']) ?></h3>
		<input type="hidden" name="subtab_<?php echo $i; ?>" value="<?php echo $row_fields['subtab']; ?>">
		<input type="hidden" name="accordion_<?php echo $i; ?>" value="<?php echo $row_fields['accordion']; ?>">
		<?php $value_config = explode(',', trim($row_fields['contacts'],','));
		if($row_fields['tab'] == 'Staff') {
			foreach($staff_categories as $staff_category) {
				$cat_value_config = explode(',', mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab` = 'Staff_".config_safe_str($staff_category)."' AND `subtab` = '".$row_fields['subtab']."' AND `accordion` = '".$row_fields['accordion']."'"))['contacts']);
				$value_config = array_merge($value_config, $cat_value_config);
			}
		}
		$value_config = array_filter(array_unique($value_config));
		foreach($value_config as $field): ?>
			<label class="form-checkbox"><input data-subtab="<?= $row_fields['subtab'] ?>" data-accordion="<?= $row_fields['accordion'] ?>" type="checkbox" name="contacts<?php echo "_$i"; ?>[]" <?= strpos($row_edit['contacts'], ','.$field.',') !== false ? 'checked' : ''; ?> value="<?php echo $field; ?>"><?php echo $field; ?></label>
		<?php endforeach; ?>
		<hr>
	<?php } ?>
</div>