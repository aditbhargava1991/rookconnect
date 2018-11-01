<?php error_reporting(0);
include_once('../include.php');
$ticket_status = explode(',',get_config($dbc, 'ticket_status'));
$ticket_status_icons = explode(',',get_config($dbc, 'ticket_status_icons'));
$task_status = explode(',',get_config($dbc, 'task_status'));
$ticket_status_color = explode(',',get_config($dbc, 'ticket_status_color')); ?>
<script>
$(document).ready(function() {
	$('input').change(saveTypes);
	$('.ticket_group').sortable({
		handle: '.drag-handle',
		items: '.ticket_status',
		update: saveTypes
	});
	$('.task_group').sortable({
		handle: '.drag-handle',
		items: '.task_status',
		update: saveTypes
	});
});
function saveTypes() {
	var ticket_status = [];
	$('[name="ticket_status[]"]').each(function() {
		ticket_status.push(this.value);
		var ticket_block = $(this).closest('.ticket_status');
		$(ticket_block).find('.id-circle-small').html(getInitials($(this).val()));
	});
	var ticket_status_icons = [];
	$('[name="ticket_status_icons[]"]').each(function() {
		ticket_status_icons.push(this.value);
	});
	var task_status = [];
	$('[name="task_status[]"]').each(function() {
		task_status.push(this.value);
	});
	var ticket_status_color = [];
	$('[name="ticket_status_color[]"]').each(function() {
		ticket_status_color.push(this.value);
	});
	$.ajax({
		url: 'ticket_ajax_all.php?action=ticket_status_list',
		method: 'POST',
		data: {
			tickets: ticket_status,
			ticket_status_icons: ticket_status_icons,
			tasks: task_status,
			ticket_status_color: ticket_status_color
		}
	});
}
function addOpt(type) {
	var clone = $('.'+type).last().clone();
	clone.find('input').val('').removeAttr('checked');
	clone.find('.icon_link').html('<button class="btn brand-btn">Choose Icon</button>');
	$('.'+type).last().after(clone);

	$('input').off('change',saveTypes).change(saveTypes);
	$('.'+type+' input').last().focus();
}
function removeOpt(a, type) {
	if($('.'+type).length <= 1) {
		addOpt(type);
	}
	$(a).closest('.'+type).remove();
	saveTypes();
}
function chooseStatusIcon(a) {
	var ticket_block = $(a).closest('.ticket_status');
	var icon = $(ticket_block).find('[name="ticket_status_icons[]"]').val();
	$('.li-icon img').removeClass('hover');
	$('.li-icon img[data-icon='+icon+']').addClass('hover');
	$('#dialog_status_icon').dialog({
		resizable: true,
		height: "auto",
		width: ($(window).width() <= 800 ? $(window).width() : 800),
		modal: true,
		buttons: {
			"Save Icon": function() {
				icon = $('.li-icon img.hover').data('icon');
				icon_url = $('.li-icon img.hover').prop('src');
				$(ticket_block).find('[name="ticket_status_icons[]"]').val(icon);
				if(icon != undefined) {
					$(ticket_block).find('a.icon_link').html('<img src="'+icon_url+'" class="inline-img">');
				} else {
					$(ticket_block).find('a.icon_link').html('<button class="btn brand-btn">Choose Icon</button>');
				}
				saveTypes();
				$(this).dialog('close');
			},
			"Remove Icon": function() {
				$(ticket_block).find('[name="ticket_status_icons[]"]').val('');
				$(ticket_block).find('a.icon_link').html('<button class="btn brand-btn">Choose Icon</button>');
				saveTypes();
				$(this).dialog('close');
			},
			"Use Initials": function() {
				$(ticket_block).find('[name="ticket_status_icons[]"]').val('initials');
				var status = $(ticket_block).find('[name="ticket_status[]"]').val();
				var initials = getInitials(status);
				$(ticket_block).find('a.icon_link').html('<span class="id-circle-small" style="margin: 0.5em; background-color: #6DCFF6; font-family: \'Open Sans\';">'+initials+'</span>');
				saveTypes();
				$(this).dialog('close');
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
	});
}
function setStatusIcon(img) {
	$('.li-icon img').removeClass('hover');
	$(img).addClass('hover');
}
function getInitials(string) {
	var matches = string.toUpperCase().match(/\b(\w)/g);
	return matches.join('');
}
</script>
<div id="dialog_status_icon" title="Select an Icon" style="display: none;">
	<div class="iconpicker">
		<ul style="text-align: center;">
			<?php $status_icons = ['complete','incomplete','alert','ongoing'];
			foreach($status_icons as $status_icon) { ?>
				<li class="li-icon"><img data-icon="<?= $status_icon ?>" src="<?= get_ticket_status_icon_url($status_icon) ?>" onclick="setStatusIcon(this);"></li>
			<?php } ?>
		</ul>
	</div>
</div>
<div class="ticket_group">
    <h3><?= TICKET_NOUN ?> Statuses</h3>
    <?php foreach($ticket_status as $i => $status) { ?>
        <div class="form-group ticket_status">
            <div style="width:7em;" class="pull-right">
                <img src="../img/icons/drag_handle.png" class="inline-img pull-right drag-handle">
                <img src="../img/remove.png" class="inline-img pull-right" onclick="removeOpt(this, 'ticket_status');">
                <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addOpt('ticket_status');">
            </div>
            <div class="scale-to-fill">
                <label class="col-sm-3"><span class="popover-examples"><a data-toggle="tooltip" data-original-title="These statuses allow you to organize your <?= TICKET_TILE ?> by moving them from status to status."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span><?= TICKET_NOUN ?> Status:</label>
                <div class="col-sm-5">
                    <input type="text" name="ticket_status[]" class="form-control" value="<?= $status ?>">
                </div>
                <div class="col-sm-2" style="text-align: center;">
                    <input type="hidden" name="ticket_status_icons[]" value="<?= $ticket_status_icons[$i] ?>">
                    <span class="popover-examples"><a data-toggle="tooltip" data-original-title="This will allow you to associate an icon or initials with a particular status, making it easier to see at a glance where your <?= TICKET_TILE ?> are."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>
                    <span class="show-on-mob"><b>Icon:</b> </span><a href="" class="icon_link" onclick="chooseStatusIcon(this); return false;">
                        <?php if(empty(get_ticket_status_icon($dbc, $status))) {
                            echo '<button class="btn brand-btn">Choose Icon</button>';
                        } else if(get_ticket_status_icon($dbc, $status) == 'initials') {
                            echo '<span class="id-circle-small" style="margin: 0.5em; background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($status).'</span>';
                        } else {
                            echo '<img src="'.get_ticket_status_icon($dbc, $status).'" class="inline-img">';
                        } ?>
                    </a>
                </div>
                <div class="col-sm-2">
                        <input type="color" name="ticket_status_color[]" class="form-control" value="<?= $ticket_status_color[$i] ?>">
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php } ?>
</div>
<hr>
<div class="task_group">
    <h3>Task Statuses</h3>
    <?php foreach($task_status as $status) { ?>
        <div class="form-group task_status">
            <div style="width:7em;" class="pull-right">
                <img src="../img/icons/drag_handle.png"  class="inline-img pull-right drag-handle">
                <img src="../img/remove.png" class="inline-img pull-right" onclick="removeOpt(this, 'task_status');">
                <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addOpt('task_status');">
            </div>
            <div class="scale-to-fill">
                <label class="col-sm-3">Task Status:</label>
                <div class="col-sm-9">
                    <input type="text" name="task_status[]" class="form-control" value="<?= $status ?>">
                </div>
            </div>
            <div class="clearfix"></div>
        </div>
    <?php } ?>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_types.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>