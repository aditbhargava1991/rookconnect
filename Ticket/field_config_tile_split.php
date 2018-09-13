<?php error_reporting(0);
include_once('../include.php');
$ticket_tabs = explode(',',get_config($dbc, 'ticket_tabs')); ?>
<script>
$(document).ready(function() {
	$('input,select').change(saveTypes);
	$('.type_lists').sortable({
        connectWith: '.type_lists',
		handle: '.drag-handle',
		items: '.type-option',
		update: saveTypes
	});
});
function saveTypes() {
    var tile_list = [];
	$('.tile_name').each(function() {
        var details = {};
        details.name = $(this).find('[name=name]').val();
        details.noun = $(this).find('[name=noun]').val();
        details.types = [];
        $(this).find('[data-type]').each(function() {
            details.types.push($(this).data('type'));
        });
        tile_list.push(details);
	});
	$.ajax({
		url: 'ticket_ajax_all.php?action=tile_splitting',
		method: 'POST',
		data: {
            tiles: tile_list
		}
	});
}
function addTile() {
	$('.type_lists').sortable('destroy');
	var clone = $('.tile_name').last().clone();
	clone.find('input').val('');
	clone.find('[data-type]').remove();
	$('.tile_name').last().after(clone);
	
	$('input').off('change').change(saveTypes);
	$('.type_lists').sortable({
        connectWith: '.type_lists',
		handle: '.drag-handle',
		items: '.type-option',
		update: saveTypes
	});
}
function remTile(a) {
	if($('.type-option').length <= 1) {
		addType();
	}
	$(a).closest('.type-option').remove();
	saveTypes();
}
</script>
<?php $tile_lists = $dbc->query("SELECT `value` FROM `general_configuration` WHERE `name` LIKE 'ticket_split_tiles_%'");
$tile_list = $tile_lists->fetch_assoc()['value'];
$assigned_types = [];
$type_labels = [];
foreach($ticket_tabs as $type) {
    $type_labels[config_safe_str($type)] = $type;
}
do {
    $tile_list = explode('#*#',$tile_list);
    $type_list = explode('|',$tile_list[2]);
    $assigned_types = array_merge($assigned_types, $type_list); ?>
    <div class="tile_name col-sm-6 pad-10">
        <div class="form-group">
            <label class="col-sm-3 control-label">Tile Name:</label>
            <div class="col-sm-7"><input type="text" name="name" class="form-control" value="<?= $tile_list[0] ?>"></div>
            <div class="col-sm-2">
                <img src="../img/remove.png" class="inline-img pull-right cursor-hand" onclick="remTile(this);">
                <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right cursor-hand" onclick="addTile();">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Tile Noun:</label>
            <div class="col-sm-9"><input type="text" name="noun" class="form-control" value="<?= $tile_list[1] ?>"></div>
        </div>
        <div class="col-sm-12 type_lists block-group">
            <h4><?= TICKET_NOUN ?> Tabs</h4>
            <?php foreach(array_filter($type_list) as $type) { ?>
                <div class="type-option col-sm-12 block-element" data-type="<?= $type ?>"><?= $type_labels[$type] ?><img class="drag-handle inline-img pull-right" src="../img/icons/drag_handle.png"></div>
            <?php } ?>
        </div>
    </div>
<?php } while($tile_list = $tile_lists->fetch_assoc()['value']); ?>
<div class="col-sm-6 type_lists block-group">
    <h4>Unassigned <?= TICKET_NOUN ?> Tabs</h4>
    <?php foreach($ticket_tabs as $type) {
        if(!in_array(config_safe_str($type),$assigned_types)) { ?>
            <div class="type-option col-sm-12 block-element" data-type="<?= config_safe_str($type) ?>"><?= $type ?><img class="drag-handle inline-img pull-right" src="../img/icons/drag_handle.png"></div>
        <?php }
    } ?>
</div>