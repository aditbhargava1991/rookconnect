<?php error_reporting(0);
include_once('../include.php');
$ticket_tabs = explode(',',get_config($dbc, 'ticket_tabs'));
$ticket_tabs_color = explode(',',get_config($dbc, 'ticket_tabs_color'));
?>
<script>
$(document).ready(function() {
	$('input,select').change(saveTypes);
	$('.main-screen').sortable({
		handle: '.drag-handle',
		items: '.type-option',
		update: saveTypes
	});
});
function saveTypes() {
	var type_list = [];
	$('[name="ticket_tabs[]"]').each(function() {
		type_list.push(this.value);
	});

	var color_list = [];
	$('[name="ticket_tabs_color[]"]').each(function() {
		color_list.push(this.value);
	});

    var tile_list = [];
	$('[name=ticket_type_tiles][value]:checked').each(function() {
		tile_list.push(this.value);
	});
	$.ajax({
		url: 'ticket_ajax_all.php?action=ticket_types',
		method: 'POST',
		data: {
			types: type_list,
			color_lists: color_list,
			tiles: ($('[name=ticket_type_tiles]').not('[value]').is(':checked') ? 'SHOW' : tile_list.join(',')),
			default_ticket_type: $('[name=default_ticket_type]').val()
		}
	});
}
function addType() {
	var clone = $('.type-option').last().clone();
	clone.find('input').val('').removeAttr('checked');
	clone.find('input[type=checkbox]').closest('.form-checkbox').hide();
	$('.type-option').last().after(clone);

	$('input').off('change').change(saveTypes);
	$('[name="ticket_tabs[]"]').last().focus();
}
function removeType(a) {
	if($('.type-option').length <= 1) {
		addType();
	}
	$(a).closest('.type-option').remove();
	saveTypes();
}
</script>
<span class="popover-examples"><a data-toggle="tooltip" data-original-title="Each of the below tabs of <?= TICKET_TILE ?> can become a label, a tab, and have specific options."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>
<?php $type_tiles = get_config($dbc, 'ticket_type_tiles');
$i=0;
foreach($ticket_tabs as $type) {
    ?>
	<div class="form-group type-option">
		<label class="col-sm-3"><?= TICKET_NOUN ?> Tab:</label>
		<div class="col-sm-4">
			<input type="text" name="ticket_tabs[]" class="form-control" value="<?= $type ?>">
		</div>
        <div class="col-sm-1">
                <input type="color" name="ticket_tabs_color[]" class="form-control" value="<?= $ticket_tabs_color[$i] ?>">
        </div>
		<div class="col-sm-3">
            <label class="form-checkbox any-width"><input name="ticket_type_tiles" type="checkbox" value="<?= config_safe_str($type) ?>" <?= (in_array(config_safe_str($type),explode(',',$type_tiles)) ? 'checked' : '') ?>> Create Tile</label>
			<img src="../img/icons/drag_handle.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right drag-handle">
			<img src="../img/icons/ROOK-add-icon.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="addType();">
			<img src="../img/remove.png" style="height: 1.5em; margin: 0 0.25em;" class="pull-right" onclick="removeType(this);">
		</div>
		<div class="clearfix"></div>
	</div>
<?php $i++; } ?>
<hr>
<label class="col-sm-3"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="Enabling this option will add additional tiles to the menus for all <?= TICKET_NOUN ?> tabs, which can have specific security and display only the <?= TICKET_TILE ?> of a given type."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Display Tiles for ALL <?= TICKET_NOUN ?> Tabs:</label>
<label class="form-checkbox"><input name="ticket_type_tiles" type="checkbox" <?= ($type_tiles == 'SHOW' ? 'checked' : '') ?>> Include ALL <?= TICKET_NOUN ?> Tabs</label>
<div class="form-group">
	<label class="col-sm-3"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="Setting a default <?= TICKET_NOUN ?> will set the type for all newly created <?= TICKET_TILE ?>."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>Default <?= TICKET_NOUN ?> Tab:</label>
	<div class="col-sm-9">
		<?php $default_ticket_type = get_config($dbc, 'default_ticket_type'); ?>
		<select name="default_ticket_type" class="chosen-select-deselect">
			<option <?= in_array($default_ticket_type, ['','na']) ? 'selected' : '' ?> value="na">No Default Tab</option>
			<?php foreach($ticket_tabs as $type) { ?>
				<option <?= config_safe_str($type) == $default_ticket_type ? 'selected' : '' ?> value="<?= config_safe_str($type) ?>"><?= $type ?></option>
			<?php } ?>
		</select>
	</div>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_types.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>