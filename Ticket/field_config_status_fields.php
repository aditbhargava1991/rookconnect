<?php include_once('../include.php');
include_once('../Ticket/field_list.php');
if(!isset($ticket_tabs)) {
	$ticket_tabs = [];
	foreach(array_filter(explode(',',get_config($dbc, 'ticket_tabs'))) as $ticket_tab) {
		$ticket_tabs[config_safe_str($ticket_tab)] = $ticket_tab;
	}
}
$tab = filter_var(empty($_GET['tile_name']) ? $_GET['type_name'] : $_GET['tile_name'], FILTER_SANITIZE_STRING);
$status = filter_var($_GET['status'], FILTER_SANITIZE_STRING);
if(empty($tab)) {
	$all_config = $all_config_fields = [];
	$value_config_fields = explode(',',get_field_config($dbc, 'tickets'));
	$value_config = explode(',',get_config($dbc, 'ticket_status_fields_'.$status));
	$sort_order = explode(',',get_config($dbc, 'ticket_sortorder'));
} else {
	$all_config_fields = explode(',',get_field_config($dbc, 'tickets'));
	$value_config_fields = explode(',',get_config($dbc, 'ticket_fields_'.$tab));
	$all_config = explode(',',get_config($dbc, 'ticket_status_fields_'.$status));
	$value_config = explode(',',get_config($dbc, 'ticket_status_fields_'.$status.'_'.$tab));
	$sort_order = explode(',',get_config($dbc, 'ticket_sortorder_'.$tab));
	if(empty(get_config($dbc, 'ticket_sortorder_'.$tab))) {
		$sort_order = explode(',',get_config($dbc, 'ticket_sortorder'));
	}
}

//Remove any sort_fields from sort_order array if they are not turned on
$merged_config_fields = array_merge($all_config_fields,$value_config_fields);
if(!in_array('Mileage',$merged_config_fields) && in_array('Drive Time',$merged_config_fields)) {
	$key = array_search('Drive Time',$merged_config_fields);
	$merged_config_fields[$key] = 'Mileage';
}
if(!in_array('Check In',$merged_config_fields) && in_array('Member Drop Off',$merged_config_fields)) {
	$key = array_search('Member Drop Off',$merged_config_fields);
	$merged_config_fields[$key] = 'Check In';
}
if(!in_array('Ticket Details',$merged_config_fields) && in_array('Services',$merged_config_fields)) {
	$key = array_search('Services',$merged_config_fields);
	$merged_config_fields[$key] = 'Ticket Details';
}
if(!in_array('Check Out',$merged_config_fields) && in_array('Check Out Member Pick Up',$merged_config_fields)) {
	$key = array_search('Check Out Member Pick Up',$merged_config_fields);
	$merged_config_fields[$key] = 'Check Out';
}
if(!in_array('Summary',$merged_config_fields) && in_array('Staff Summary',$merged_config_fields)) {
	$key = array_search('Staff Summary',$merged_config_fields);
	$merged_config_fields[$key] = 'Summary';
}
$sort_order = array_intersect($sort_order, $merged_config_fields);

if(empty(array_filter($value_config))) {
	$value_config = $merged_config_fields;
}
foreach ($accordion_list as $accordion_field => $accordion_field_fields) {
	if(!in_array($accordion_field, $sort_order)) {
		$sort_order[] = $accordion_field;
	}
}

//Reset merged_config_fields
$merged_config_fields = array_merge($all_config_fields,$value_config_fields);
?>
<script>
$(document).ready(function() {
	$('input,select,textarea').not('[name=status]').change(saveFields);
	$('.transport_group').each(function() {
		var block = $(this).find('.fields_sortable');
		if($.trim($(block).text()) == '') {
			$(this).remove();
		}
	});
	$('.sort_order_accordion:not(.sort_order_heading)').each(function() {
		var block = $(this).find('.block-group');
		if($.trim($(block).text()) == '') {
			$(block).remove();
		}
	});
});
function saveFields() {
	var this_field_name = this.name;
	var ticket_fields = [];
	$('[name="tickets[]"]:checked').not(':disabled').each(function() {
		ticket_fields.push(this.value);
	});
	$.post('ticket_ajax_all.php?action=ticket_action_fields', {
		fields: ticket_fields,
		field_name: '<?= empty($tab) ? 'ticket_status_fields_'.$status : 'ticket_status_fields_'.$status.'_'.$tab ?>'
	}).success(function() {
	});
}
</script>
<!-- <h1><?= (!empty($tab) ? $ticket_tabs[$tab].' Fields' : 'All '.TICKET_NOUN.' Fields') ?></h1> -->
<?php if(empty($_GET['tile_name'])) {
	echo '<a href="?settings=status_fields" class="btn brand-btn '.(empty($tab) ? 'active_tab' : '').'">All '.TICKET_TILE.'</a>';
	foreach($ticket_tabs as $tab_id => $tab_label) {
		echo '<a href="?settings=status_fields&type_name='.$tab_id.'" class="btn brand-btn '.($tab_id == $tab ? 'active_tab' : '').'">'.$tab_label.'</a>';
	}
} ?>
<div class="notice double-gap-bottom popover-examples">
    <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
    <div class="col-sm-11">
        <span class="notice-name">NOTE:</span>
        Configure what Fields can be seen when viewing <? TICKET_TILE ?> with a particular Status. Only Fields that are turned on will be displayed here.
    </div>
    <div class="clearfix"></div>
</div>
<div class="form-group">
    <label class="col-sm-4">Status:</label>
    <div class="col-sm-8">
        <select name="status" data-placeholder="Select Status" class="chosen-select-deselect" onchange="window.location.replace('?settings=status_fields&type_name=<?= $_GET['type_name'] ?>&tile_name=<?= $_GET['tile_name'] ?>&status='+this.value);"><option />
            <?php foreach(explode(',',get_config($dbc,'ticket_status')) as $status_name) { ?>
                <option <?= $status == config_safe_str($status_name) ? 'selected' : '' ?> value="<?= config_safe_str($status_name) ?>"><?= $status_name ?></option>
            <?php } ?>
        </select>
    </div>
</div>
<?php $status_fields = true;
if(empty($status)) {
    echo '<h3>Please Select a Status</h3>';
} else {
    include('field_config_field_list.php');
} ?>