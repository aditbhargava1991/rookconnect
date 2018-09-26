<?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>Individuals Present</h3>') ?>
<script>
function individual_set(select) {
	var block = $(select).closest('.individual_present');
	if(select.value == 'MANUAL') {
		block.find('.select_contact').hide();
		block.find('.manual_entry').show();
	} else {
		block.find('.select_contact').show();
		block.find('.manual_entry').hide();
	}
}
</script>
<?php foreach(explode('#*#',get_config($dbc, 'ticket_individuals')) as $ind_row => $type) {
	$type = explode('|',$type);
	$individual = explode('#*#',$get_ticket['other_ind'])[$ind_row]; ?>
	<div class="form-group individual_present">
		<label class="col-sm-4 control-label"><?= $type[0] == 'staff' && $type[1] == 'ALL' ? 'Staff' : $type[1] ?>:</label>
		<div class="col-sm-7 select_contact <?= $access_all ? '' : 'readonly-block' ?>" style="<?= ($individual > 0 || $individual == '') && $type[0] != 'custom' ? '' : 'display:none;' ?>">
			<select name="other_ind" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-placeholder="Select Individual" class="chosen-select-deselect" onchange="individual_set(this);" <?= $access_all ? '' : 'readonly disabled' ?>><option></option>
				<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `status`>0 AND `deleted`=0 AND (`tile_name`='".$type[0]."' AND `category`='".$type[1]."') OR ('".$type[0]."'='staff' AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND (`category_contact`='".$type[1]."' OR '".$type[1]."'='ALL')) AND CONCAT(`first_name`,`last_name`) != ''")) as $row) { ?>
					<option <?= $row['contactid'] == $individual ? 'selected' : '' ?> value="<?= $row['contactid'] ?>"><?= $row['first_name'].' '.$row['last_name'] ?></option>
				<?php } ?>
				<!--<option value="NEW_LINE">Add New</option>-->
				<option <?= $type[0] == 'custom' ? 'selected' : '' ?> value="MANUAL">Custom Entry</option>
			</select>
		</div>
		<div class="col-sm-1 select_contact">
			<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
			<?php if ($access_all) { ?>
				<a href="" onclick="$(this).closest('.form-group').find('select').val('MANUAL').change(); return false;"><img class="inline-img pull-right" data-history-label="New <?= $type[0] == 'staff' && $type[1] == 'ALL' ? 'Staff' : $type[1] ?>" src="../img/icons/ROOK-add-icon.png"></a>
			<?php } ?>
		</div>
		<!--<div class="col-sm-8 add_contact" style="display:none;">
			<input type="text" name="other_ind" class="form-control" value="">
		</div>-->
		<div class="<?= $type[0] == 'custom' ? 'col-sm-8' : 'col-sm-7' ?> manual_entry" style="<?= ($individual > 0 || $individual == '') && $type[0] != 'custom' ? 'display:none;' : '' ?>">
			<input type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" name="other_ind" class="form-control" value="<?= $individual ?>">
		</div>
		<div class="col-sm-1 manual_entry" style="<?= ($individual > 0 || $individual == '') || $type[0] == 'custom' ? 'display:none;' : '' ?>">
			<img src="../img/remove.png" class="inline-img" onclick="$(this).closest('.individual_present').find('select[name=other_ind]').val('').trigger('change.select2');">
		</div>
	</div>
	<?php $pdf_contents[] = [$type[0] == 'staff' && $type[1] == 'ALL' ? 'Staff' : $type[1], $type[0] == 'custom' ? $individual : get_contact($dbc, $individual)]; ?>
<?php } ?>