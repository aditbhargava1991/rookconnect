<?php //error_reporting(0);
include_once('../include.php'); ?>
<script>
var service_list = <?php $services = [];
$service_list = mysqli_query($dbc, "SELECT `serviceid`, CONCAT(`category`,': ',`heading`) label FROM `services` WHERE `deleted`=0");
while($service = mysqli_fetch_array($service_list)) {
	$services[] = ['id'=>$service['serviceid'],'label'=>$service['label']];
}
echo json_encode($services); ?>;
$(document).ready(function(){
	init_path();
});
function init_path() {
	$('.form-horizontal').sortable({
		handle: '.block-handle',
		items: '.block-group',
		update: save_path
	});
	$('.sortable_group_block').sortable({
		handle: '.group-handle',
		items: '.sortable_group',
		update: save_individual_order
	});
	$('[name=ticket_service]').each(function() {
		var select = this;
		var id = $(this).data('service');
		service_list.forEach(function(obj) {
			$(select).append($("<option>", {
				value: obj.id,
				text: obj.label,
				selected: id == obj.id
			}));
		});
		$(select).trigger('change.select2');
	});
	$('input,select').off('change').change(save_path);
	initInputs();
}
function save_path() {
	var milestone = '';
	var timeline = '';
	var checklist = '';
	var ticket = '';
	var workorder = '';
	$('[name=milestone]').each(function() {
		var block = $(this).closest('.block-group');
		var delimiter = false;
		if(milestone != '') {
			delimiter = true;
		}
		milestone += (delimiter ? '#*#' : '')+this.value;
		timeline += (delimiter ? '#*#' : '')+block.find('[name=timeline]').val();
		checklist += (delimiter ? '#*#' : '')+block.find('[name=checklist]').map(function() { return this.value; }).get().join('*#*');
		var ticket_list = [];
		block.find('[name=ticket_heading]').each(function() {
			ticket_list.push(this.value+'FFMSPLIT'+$(this).closest('.form-group').find('[name=ticket_service]').val());
		});
		ticket += (delimiter ? '#*#' : '')+ticket_list.join('*#*');
		workorder += (delimiter ? '#*#' : '')+block.find('[name=workorder]').map(function() { return this.value; }).get().join('*#*');
	});
	$.ajax({
		url: 'projects_ajax.php?action=path_template',
		method: 'POST',
		data: {
			templateid: $('[name=templateid]').val(),
			template_name: $('[name=template_name]').val(),
			milestone: milestone,
			timeline: timeline,
			checklist: checklist,
			ticket: ticket,
			workorder: workorder
		},
		success: function(response) {
			if(response > 0) {
				$('[name=templateid]').val(response);
			}
		}
	});
}
function save_individual_order() {
    var milestone = '';
    var checklist = '';
	var ticket = '';
	var workorder = '';

    $('[name=milestone]').each(function() {
        var block = $(this).closest('.block-group');
        var delimiter = false;
		if(milestone != '') {
			delimiter = true;
		}
        milestone += (delimiter ? '#*#' : '')+this.value;
        checklist += (delimiter ? '#*#' : '')+block.find('[name=checklist]').map(function() { return this.value; }).get().join('*#*');
        var ticket_list = [];
		block.find('[name=ticket_heading]').each(function() {
			ticket_list.push(this.value+'FFMSPLIT'+$(this).closest('.form-group').find('[name=ticket_service]').val());
		});
		ticket += (delimiter ? '#*#' : '')+ticket_list.join('*#*');
		workorder += (delimiter ? '#*#' : '')+block.find('[name=workorder]').map(function() { return this.value; }).get().join('*#*');
    });

	$.ajax({
		url: 'projects_ajax.php?action=path_template_individual_order',
		method: 'POST',
		data: {
			templateid: $('[name=templateid]').val(),
			checklist: checklist,
			ticket: ticket,
			workorder: workorder
		},
		success: function(response) {
			if(response > 0) {
				$('[name=templateid]').val(response);
			}
		}
	});
}

function pathDefault(sel) {
	var tile_value = sel.value;
	var id = sel.id;

	$.ajax({    //create an ajax request to ajax_all.php
		type: "GET",
		url: "projects_ajax.php?action=dafault_path&project_path_milestone="+tile_value,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}

function add_block() {
	var block = $('[name=milestone]').last().closest('.block-group');
	var clone = block.clone();
	clone.find('.block-group').find('.form-group').remove();
	clone.find('input').val('');
	block.after(clone);
	init_path();
}
function remove_block(img) {
	if($('[name=milestone]').length <= 1) {
		add_block();
	}
	$(img).closest('.block-group').remove();
	save_path();
}
function remove_group(img) {
	$(img).closest('.form-group').remove();
	save_path();
}
function add_checklist(btn) {
	var item = '<div class="form-group sortable_group">' +
		'<label class="col-sm-4">Task:</label>' +
		'<div class="col-sm-7">' +
			'<input type="text" class="form-control" name="checklist">' +
		'</div>' +
		'<div class="col-sm-1">' +
			'<img src="../img/remove.png" class="inline-img pull-right" onclick="remove_group(this);">' +
			'<img src="../img/icons/drag_handle.png" class="inline-img pull-right group-handle no-toggle" title="Drag" />' +
		'</div>' +
	'</div>';
	$(btn).closest('.block-group').find('button').first().before(item);
	init_path();
}
function add_ticket(btn) {
	var item = '<div class="form-group sortable_group">' +
		'<label class="col-sm-4"><?= TICKET_NOUN ?> Heading &amp; Service:</label>' +
		'<div class="col-sm-4">' +
			'<input type="text" class="form-control" name="ticket_heading">' +
		'</div>' +
		'<div class="col-sm-3">' +
			'<select class="chosen-select-deselect" name="ticket_service"><option></option>';
	service_list.forEach(function(obj) {
		item += '<option value="'+obj.id+'">'+obj.label+'</option>';
	});
	item += '</select>' +
		'</div>' +
		'<div class="col-sm-1">' +
			'<img src="../img/remove.png" class="inline-img pull-right" onclick="remove_group(this);">' +
			'<img src="../img/icons/drag_handle.png" class="inline-img pull-right group-handle no-toggle" title="Drag" />' +
		'</div>' +
	'</div>';
	$(btn).closest('.block-group').find('button').first().before(item);
	resetChosen($('select'));
	init_path();
}
function add_workorder(btn) {
	var item = '<div class="form-group sortable_group">' +
		'<label class="col-sm-4">Work Order Heading:</label>' +
		'<div class="col-sm-7">' +
			'<input type="text" class="form-control" name="workorder">' +
		'</div>' +
		'<div class="col-sm-1">' +
			'<img src="../img/remove.png" class="inline-img pull-right" onclick="remove_group(this);">' +
			'<img src="../img/icons/drag_handle.png" class="inline-img pull-right group-handle no-toggle" title="Drag" />' +
		'</div>' +
	'</div>';
	$(btn).closest('.block-group').find('button').first().before(item);
	init_path();
}
</script>
<div class="form-horizontal">
<?php if(!empty($_GET['path'])):
    $templateid = filter_var($_GET['path'],FILTER_SANITIZE_STRING);
	$template = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM project_path_milestone WHERE project_path_milestone='$templateid'")); ?>
	<input type="hidden" id="templateid" name="templateid" value="<?php echo $templateid ?>" />
	<div class="form-group">
		<label class="col-sm-4">Template Name:</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="template_name" value="<?= $template['project_path'] ?>">
		</div>
	</div>
	<?php $tab_config = array_filter(array_unique(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT GROUP_CONCAT(`config_tabs` SEPARATOR ',') `config` FROM field_config_project"))['config'])));
	$timelines = explode('#*#', $template['timelines']);
	$tickets = explode('#*#', $template['ticket']);
	$workorders = explode('#*#', $template['workorder']);
	$checklists = explode('#*#', $template['checklist']);
	foreach(explode('#*#',$template['milestone']) as $i => $milestone) { ?>
		<div class="block-group">
			<div class="form-group">
				<label class="col-sm-4">Milestone:</label>
				<div class="col-sm-7">
					<input type="text" class="form-control" name="milestone" value="<?= $milestone ?>">
				</div>
				<div class="col-sm-1">
					<img src="../img/icons/drag_handle.png" class="inline-img pull-right block-handle no-toggle" title="Drag">
					<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_block();">
					<img src="../img/remove.png" class="inline-img pull-right" onclick="remove_block(this);">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4">Timeline:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="timeline" value="<?= $timeline[$i] ?>">
				</div>
			</div>
			<div class="block-group sortable_group_block">
				<?php foreach(explode('*#*',$checklists[$i]) as $checklist) {
					if($checklist != '') { ?>
						<div class="form-group sortable_group">
                            <label class="col-sm-4">Task:</label>
                            <div class="col-sm-7"><input type="text" class="form-control" name="checklist" value="<?= $checklist ?>" /></div>
                            <div class="col-sm-1">
                                <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_group(this);" />
                                <img src="../img/icons/drag_handle.png" class="inline-img pull-right group-handle no-toggle" title="Drag" />
                            </div>
						</div>
					<?php } ?>
				<?php } ?>
				<?php foreach(explode('*#*',$tickets[$i]) as $ticket) {
					if($ticket != '') {
						$ticket = explode('FFMSPLIT',$ticket); ?>
						<div class="form-group sortable_group">
							<label class="col-sm-4"><?= TICKET_NOUN ?> Heading &amp; Service:</label>
							<div class="col-sm-4"><input type="text" class="form-control" name="ticket_heading" value="<?= $ticket[0] ?>"></div>
							<div class="col-sm-3"><select class="chosen-select-deselect" name="ticket_service" data-service="<?= $ticket[1] ?>"><option></option></select></div>
							<div class="col-sm-1">
								<img src="../img/remove.png" class="inline-img pull-right" onclick="remove_group(this);" />
                                <img src="../img/icons/drag_handle.png" class="inline-img pull-right group-handle no-toggle" title="Drag" />
							</div>
						</div>
					<?php } ?>
				<?php } ?>
				<?php foreach(explode('*#*',$workorders[$i]) as $workorder) {
					if($workorder != '') { ?>
						<div class="form-group sortable_group">
							<label class="col-sm-4">Work Order Heading:</label>
							<div class="col-sm-7"><input type="text" class="form-control" name="workorder" value="<?= $workorder ?>" /></div>
							<div class="col-sm-1">
								<img src="../img/remove.png" class="inline-img pull-right" onclick="remove_group(this);" />
                                <img src="../img/icons/drag_handle.png" class="inline-img pull-right group-handle no-toggle" title="Drag" />
							</div>
						</div>
					<?php } ?>
				<?php } ?>
				<?php if(in_array('Work Orders',$tab_config)) { ?><button class="btn brand-btn pull-right" onclick="add_workorder(this); return false;">New Work Order</button><?php } ?>
				<?php if(in_array('Tickets',$tab_config)) { ?><button class="btn brand-btn pull-right" onclick="add_ticket(this); return false;">New <?= TICKET_NOUN ?></button><?php } ?>
				<?php if(in_array('Checklists',$tab_config) || in_array('Tasks',$tab_config)) { ?><button class="btn brand-btn pull-right" onclick="add_checklist(this); return false;">New Task</button><?php } ?>
				<div class="clearfix"></div>
			</div>
		</div>
	<?php } ?>
	<div class="clearfix"></div>
<?php else: ?>
	<script>
	function remove_path(a) {
		if(confirm("Are you sure you want to remove this template ("+$(a).closest('tr').find('td').first().text()+")?")) {
			$.ajax({
				url: 'projects_ajax.php?action=remove_template',
				method: 'POST',
				data: {
					id: $(a).data('id')
				},
				success: function(response) {
					$(a).closest('tr').remove();
				}
			});
		}
	}
	</script>
	<a href="?settings=path&path=new" class="btn brand-btn pull-right">Add Path Template</a>
	<?php $query_check_credentials = "SELECT * FROM project_path_milestone ORDER BY `project_path`";
	$result = mysqli_query($dbc, $query_check_credentials);
	echo "<table class='table table-bordered'>";
	echo "<tr class='hidden-xs hidden-sm'>";
	echo "<th>Default</th><th>".(PROJECT_TILE=='Projects' ? "Project" : PROJECT_TILE)." Path</th>";
	echo "<th>Milestone & Timeline</th>
	<th>Function</th>";
	echo "</tr>";
    $checked = 0;
	while($row = mysqli_fetch_array($result)) {
		echo '<tr>';

        $checked = ( $row['default_path'] == 1 ) ? ' checked="checked"' : '';

        echo '<td data-title="Default"><input onchange="pathDefault(this)" type="radio" '. $checked . ' name="project_sorting" value="'.$row['project_path_milestone'].'"></td>';
		echo '<td data-title="'.(PROJECT_TILE=='Projects' ? "Project" : PROJECT_TILE).' Path">' . $row['project_path']. '</td>';

		echo '<td data-title="Milestone & Timeline">';
		$milestone = explode('#*#', $row['milestone']);
		$timeline = explode('#*#', $row['timeline']);
		$ticket = explode('#*#', $row['ticket']);
		$workorder = explode('#*#', $row['workorder']);
		$checklist = explode('#*#', $row['checklist']);
		$j=0;
		foreach($milestone as $value)  {
			if($value != '') {
				echo $value. (!empty($timeline[$j]) ? ': ' : '').$timeline[$j].'<br>';
				if(!empty($checklist[$j]) || !empty($ticket[$j]) || !empty($workorder[$j])) {
					echo "<ul>";
					foreach(explode('*#*', $ticket[$j]) as $item) {
						if($item != '' && $item != 'FFMSPLIT') {
							$item = explode('FFMSPLIT',$item);
							$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(`category`,': ',`heading`) service FROM `services` WHERE `serviceid`='".$item[1]."'"))['service'];
							echo "<small><li>".TICKET_NOUN.": ".$item[0]." (Service: ".$service.")</li></small>";
						}
					}
					foreach(explode('*#*', $workorder[$j]) as $item) {
						if($item != '') {
							echo "<small><li>Work Order: ".$item."</li></small>";
						}
					}
					foreach(explode('*#*', $checklist[$j]) as $item) {
						if($item != '') {
							echo "<small><li>".$item."</li></small>";
						}
					}
					echo "</ul>";
				}
			}
			$j++;
		}
		echo '</td>';
		echo '<td data-title="Function">';
		echo '<a href=\'?settings=path&path='.$row['project_path_milestone'].'\'>Edit</a> | ';
		echo '<a href="" onclick="remove_path(this); return false;" data-id="'.$row['project_path_milestone'].'">Delete</a>';
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>
	<a href="?settings=path&path=new" class="btn brand-btn pull-right">Add Path Template</a><br />
	<div class="clearfix"></div>';
endif; ?>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_path_template.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>