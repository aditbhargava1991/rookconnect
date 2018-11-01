<script>
$(document).ready(function() {
	$('input').off('change',saveGroups).change(saveGroups);
});
function saveGroups() {
	var ticket_accounting_fields = [];
	$('[name="ticket_accounting_fields[]"]:checked').each(function() {
		ticket_accounting_fields.push(this.value);
	});
	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=setting_tile',
		method: 'POST',
		data: {
			field: 'ticket_accounting_fields',
			value: ticket_accounting_fields.join(',')
		}
	});
}
</script>
<?php $ticket_accounting_fields = explode(',',get_config($dbc, 'ticket_accounting_fields')); ?>
<div class="form-group">
	<label class="col-sm-4 control-label">Search Fields:</label>
	<div class="col-sm-8">
		<label class="form-checkbox"><input type="checkbox" name="ticket_accounting_fields[]" <?= in_array('Region',$ticket_accounting_fields) ? 'checked' : '' ?> value="Region"> Region</label>
		<label class="form-checkbox"><input type="checkbox" name="ticket_accounting_fields[]" <?= in_array('Classification',$ticket_accounting_fields) ? 'checked' : '' ?> value="Classification"> Classification</label>
		<label class="form-checkbox"><input type="checkbox" name="ticket_accounting_fields[]" <?= in_array('Business',$ticket_accounting_fields) ? 'checked' : '' ?> value="Business"> <?= BUSINESS_CAT ?></label>
		<label class="form-checkbox"><input type="checkbox" name="ticket_accounting_fields[]" <?= in_array('All Contact',$ticket_accounting_fields) ? 'checked' : '' ?> value="All Contact"> Any <?= CONTACTS_NOUN ?></label>
		<label class="form-checkbox"><input type="checkbox" name="ticket_accounting_fields[]" <?= in_array('Contact',$ticket_accounting_fields) ? 'checked' : '' ?> value="Contact"> <?= CONTACTS_TILE ?></label>
		<label class="form-checkbox"><input type="checkbox" name="ticket_accounting_fields[]" <?= in_array('Ticket Contact Type',$ticket_accounting_fields) ? 'checked' : '' ?> value="Ticket Contact Type"> <?= CONTACTS_NOUN ?> Types</label>
		<label class="form-checkbox"><input type="checkbox" name="ticket_accounting_fields[]" <?= in_array('Ticket Type',$ticket_accounting_fields) ? 'checked' : '' ?> value="Ticket Type"> <?= TICKET_NOUN ?> Type</label>
		<label class="form-checkbox"><input type="checkbox" name="ticket_accounting_fields[]" <?= in_array('Dates',$ticket_accounting_fields) ? 'checked' : '' ?> value="Dates"> Dates</label>
	</div>
</div>