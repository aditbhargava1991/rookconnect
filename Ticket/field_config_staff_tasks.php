<?php include_once('../include.php'); ?>
<?php $any_config = get_field_config($dbc, 'tickets');
foreach(explode(',',get_config($dbc, 'ticket_tabs')) as $ticket_type) {
	$any_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
}
$any_config = explode(',',$any_config);
$group_names = [];
if(in_array('Ticket Tasks Projects',$any_config)) {
	foreach(explode(',',PROJECT_TYPES) as $project_type) {
		$group_names[] = $project_type;
	}
	$dbc->query("UPDATE `task_types` SET `deleted`=0 WHERE `category` NOT IN ('".implode("','",$group_names)."')");
} else if(in_array('Ticket Tasks Ticket Type',$any_config)) {
	foreach(explode(',',get_config($dbc, 'ticket_tabs')) as $ticket_type) {
		$group_names[] = $ticket_type;
	}
	$dbc->query("UPDATE `task_types` SET `deleted`=0 WHERE `category` NOT IN ('".implode("','",$group_names)."')");
} else {
	$groups = $dbc->query("SELECT `category` FROM `task_types` WHERE `deleted`=0 GROUP BY `category` ORDER BY MIN(`sort`), MIN(`id`)");
	while($group = $groups->fetch_array()) {
		$group_names[] = $group[0];
	}
}
$rate_security = get_security($dbc, 'rate_cards'); ?>
<div class="form-group task_list" id="no-more-tables">
	<?php foreach($group_names as $group) {
		$tasks = $dbc->query("SELECT `task_types`.`id`, `task_types`.`description`, `task_types`.`details`, `rate`.`companyrcid`, `rate`.`cust_price`, `rate`.`uom` FROM `task_types` LEFT JOIN `company_rate_card` `rate` ON `rate`.`tile_name`='Tasks' AND (`task_types`.`id`=`rate`.`item_id` OR (`rate`.`item_id`=0 AND `task_types`.`description`=`rate`.`description` AND `task_types`.`category`=`rate`.`heading`)) AND `rate`.`deleted`=0 WHERE `task_types`.`deleted`=0 AND `task_types`.`category`='$group' ORDER BY `task_types`.`sort`,`task_types`.`id`"); ?>
		<div class='col-sm-12 task-group'>
			<div class='form-group'>
				<label class='col-sm-3 control-label'>Tab:</label>
				<div class='col-sm-8'>
					<input type='text' <?= ((in_array('Ticket Tasks Projects',array_merge($all_config,$value_config)) || in_array('Ticket Tasks Ticket Type',array_merge($all_config,$value_config))) ? 'readonly' : '') ?> name='category' value='<?= $group ?>' class='form-control' onchange='set_task_data();'>
				</div>
				<div class="col-sm-1"><img class="inline-img group_handle cursor-hand" src="../img/icons/drag_handle.png"></div>
			</div>
			<table class="table table-bordered">
				<tr class="hidden-sm hidden-xs">
					<th>Heading</th>
					<th>Description</th>
					<th>Rate</th>
					<th></th>
				</tr>
			<?php $task = $tasks->fetch_assoc();
			do { ?>
				<tr>
					<td data-title="Heading"><input type='text' name='task' data-id="<?= $task['id'] ?>" value='<?= $task['description'] ?>' class='form-control' onchange='set_task_data();'></td>
					<td data-title="Description"><input type='text' name='details' data-id="<?= $task['id'] ?>" value='<?= $task['details'] ?>' class='form-control' onchange='set_task_data();'></td>
					<td data-title="Rate">$<?= number_format($task['cust_price'],2).' '.$task['uom'] ?> <?= $rate_security['edit'] > 0 ? '<a href="../Rate Card/ratecards.php?card=tasks&type=tasks&'.($task['companyrcid'] > 0 ? 'id='.$task['companyrcid'] : 'task='.$task['id']).'" onclick="overlayIFrameSlider(this.href,\'auto\',true,true); return false;">Edit</a>' : '' ?></td>
					<td data-title="">
						<input type="hidden" name="deleted" value="0">
						<img src="../img/remove.png" class="inline-img cursor-hand" onclick="$(this).closest('tr').hide().find('[name=deleted]').val(1); set_task_data();">
						<img src="../img/icons/ROOK-add-icon.png" class="inline-img cursor-hand" onclick="add_task(this);">
						<img src="../img/icons/drag_handle.png" class="inline-img cursor-hand handle">
					</td>
				</tr>
			<?php } while($task = $tasks->fetch_assoc()); ?>
			</table>
		</div>
	<?php } ?>
	<button onclick="add_task_group(); return false;" class="btn brand-btn pull-right">Add Tab</button>
	<div class="clearfix"></div>
	<script>
	function add_task(btn) {
		var row = $(btn).closest('tr');
		var clone = row.clone();
		clone.find('input').val('').data('id','');
		row.after(clone);
		clone.find('input').first().focus();
	}
	function add_task_group() {
		var group = $('.task-group').last();
		var clone = group.clone();
		clone.find('tr').not(':last').not(':first').remove();
		clone.find('input').val('').data('id','');
		group.after(clone);
		set_task_data();
	}
	function set_task_data() {
		var data = [];
		$('.task-group').each(function() {
			var cat = $(this).find('[name=category]').val();
			if(cat != '') {
				$(this).find('tr').each(function() {
					if($(this).find('[name=task]').length > 0) {
						var id = $(this).find('[name=task]').data('id');
						var heading = $(this).find('[name=task]').val();
						var details = $(this).find('[name=details]').val();
                        if(heading != '' && details != '') {
                            data.push({'id':id,'category':cat,'task':heading,'details':details});
                        }
					}
				});
			}
		});
		$.post('../Ticket/ticket_ajax_all.php?action=task_types', { tasks: data }, function(response) {
			if(response > 0) {
				$('[name=task]').filter(function() { return !($(this).data('id') > 0); }).first().data('id',response);
			}
		});
	}
	$(document).ready(function() {
		$('.standard-body-content').sortable({
			handle: '.group_handle',
			items: '.task-group',
			update: set_task_data
		});
		$('.task_list.form-group').sortable({
			handle: '.handle',
			items: 'tr',
			update: set_task_data
		});
	});
	</script>
</div>