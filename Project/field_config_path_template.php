<?php //error_reporting(0);
include_once('../include.php'); ?>
<script>
var service_list = <?php $services = [];
$service_list = mysqli_query($dbc, "SELECT `serviceid`, CONCAT(`category`,': ',`heading`) label FROM `services` WHERE `deleted`=0");
while($service = mysqli_fetch_array($service_list)) {
	$services[] = ['id'=>$service['serviceid'],'label'=>$service['label']];
}
echo json_encode($services); ?>;
var intake_list = <?php $intake_lists_arr = [];
$intake_lists = mysqli_query($dbc, "SELECT `intakeformid`, `form_name` FROM `intake_forms` WHERE `deleted`=0");
while($intake_form = mysqli_fetch_array($intake_lists)) {
    $intake_lists_arr[] = ['id'=>$intake_form['intakeformid'],'label'=>$intake_form['form_name']];
}
echo json_encode($intake_lists_arr); ?>;
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
		update: save_path
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
	$('input,select').off('change',save_path).change(save_path);
	initInputs();
}
function save_path() {
	var milestone = '';
	var timeline = '';
	var tasks = '';
	var ticket = '';
	var check_list = '';
	var intake_form = '';
    var intakes = '';

	$('[name=milestone]').each(function() {
		var block = $(this).closest('.block-group');
		var delimiter = false;
		if(milestone != '') {
			delimiter = true;
		}
		milestone += (delimiter ? '#*#' : '')+this.value;
		timeline += (delimiter ? '#*#' : '')+block.find('[name=timeline]').val();
		tasks += (delimiter ? '#*#' : '')+block.find('[name=checklist]').filter(function() { return this.value != ''; }).map(function() { return this.value; }).get().join('*#*');
		var ticket_list = [];
		block.find('[name=ticket_heading]').filter(function() { return this.value != ''; }).each(function() {
			ticket_list.push(this.value+'FFMSPLIT'+$(this).closest('.form-group').find('[name=ticket_service]').val());
		});
		ticket += (delimiter ? '#*#' : '')+ticket_list.join('*#*');
		check_list += (delimiter ? '#*#' : '')+block.find('[name=check_list]').map(function() { return this.value; }).get().join('*#*');
		intake_form += (delimiter ? '#*#' : '')+block.find('[name=intake_form]').map(function() { return this.value; }).get().join('*#*');
		// items += (delimiter ? '#*#' : '')+block.find('[name=items]').filter(function() { return this.value != ''; }).map(function() { return this.value; }).get().join('*#*');
		//intakes += (delimiter ? '#*#' : '')+block.find('[name=intake]').filter(function() { return this.value > 0; }).map(function() { return this.value; }).get().join('*#*');

	});
	$.ajax({
		url: 'projects_ajax.php?action=path_template',
		method: 'POST',
		data: {
			templateid: $('[name=templateid]').val(),
			template_name: $('[name=template_name]').val(),
			milestone: milestone,
			timeline: timeline,
			checklist: tasks,
			tasks: tasks,
			ticket: ticket,
			check_list: check_list,
            intakes: intakes,
			intake_form: intake_form
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
			tasks: tasks,
			ticket: ticket,
			intakes: intakes
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
	// clone.find('.block-group').find('.form-group').remove();
    clone.find('.block-group .form-group').each(function() { remove_group($(this).find('label').first()); });
	clone.find('input,select').val('');
	block.after(clone);
    destroyInputs();
	init_path();
}
function remove_block(img) {
	if($('[name=milestone]').length <= 1) {
		add_block();
	}
	$(img).closest('.block-group').remove();
	save_path();
}
function add_group(img) {
    destroyInputs();
    $('.form-horizontal').sortable('destroy');
	$('.sortable_group_block').sortable('destroy');
    var type = $(img).closest('.form-group').attr('class').split(' ')[0];
    var clone = $(img).closest('.block-group').find('.'+type).last().clone();
    clone.find('input,select').val('');
    $(img).closest('.block-group').find('.'+type).last().after(clone);
    init_path();
}
function remove_group(img) {
    var type = $(img).closest('.form-group').attr('class').split(' ')[0];
    if($(img).closest('.block-group').find('.'+type).length == 1) {
        add_group(img);
    }
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
function add_check(btn) {
	var item = '<div class="form-group sortable_group">' +
		'<label class="col-sm-4">Check List:</label>' +
		'<div class="col-sm-7">' +
			'<input type="text" class="form-control" name="check_list">' +
		'</div>' +
		'<div class="col-sm-1">' +
			'<img src="../img/remove.png" class="inline-img pull-right" onclick="remove_group(this);">' +
			'<img src="../img/icons/drag_handle.png" class="inline-img pull-right group-handle no-toggle" title="Drag" />' +
		'</div>' +
	'</div>';
	$(btn).closest('.block-group').find('button').first().before(item);
	init_path();
}
function add_intake(btn) {
	var item = '<div class="form-group sortable_group">' +
    	'<label class="col-sm-4">Intake Form:</label>' +
    	'<div class="col-sm-6">' +
    		'<select data-placeholder="Select Form" class="chosen-select-deselect" name="intake_form"><option></option>';
    		intake_list.forEach(function(obj) {
            	item += '<option value="'+obj.id+'">'+obj.label+'</option>';
            });
    item += '</select>' +
    	'</div>' +
    	'<div class="col-sm-2">' +
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
	$timelines = explode('#*#', $template['timeline']);
	$tickets = explode('#*#', $template['ticket']);
	$tasks = explode('#*#', $template['checklist']);
	// $checklists = explode('#*#', $template['items']);
	$intakes = explode('#*#', $template['intakes']);
    $form_list = $dbc->query("SELECT `intakeformid`, `form_name` FROM `intake_forms` WHERE `deleted`=0")->fetch_all(MYSQLI_ASSOC);
	foreach(explode('#*#',$template['milestone']) as $i => $milestone) { ?>
		<div class="block-group">
			<div class="form-group">
				<label class="col-sm-4">Milestone:</label>
				<div class="col-sm-6">
					<input type="text" class="form-control" name="milestone" value="<?= $milestone ?>">
				</div>
				<div class="col-sm-1">
					<img src="../img/icons/drag_handle.png" class="inline-img pull-right block-handle no-toggle" title="Drag">
					<img src="../img/remove.png" class="inline-img pull-right" onclick="remove_block(this);">
					<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_block();">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4">Timeline:</label>
				<div class="col-sm-8">
					<input type="text" class="form-control" name="timeline" value="<?= $timelines[$i] ?>">
				</div>
			</div>
			<div class="block-group sortable_group_block">
				<?php foreach(explode('*#*',$tickets[$i]) as $ticket) {
                    $ticket = explode('FFMSPLIT',$ticket); ?>
                    <div class="ticket form-group sortable_group">
                        <label class="col-sm-4"><?= TICKET_NOUN ?> Heading &amp; Service:</label>
                        <div class="col-sm-3"><input type="text" class="form-control" name="ticket_heading" value="<?= $ticket[0] ?>"></div>
                        <div class="col-sm-3"><select class="chosen-select-deselect" name="ticket_service" data-service="<?= $ticket[1] ?>"><option></option></select></div>
                        <div class="col-sm-2">
                            <img src="../img/icons/drag_handle.png" class="inline-img pull-right group-handle no-toggle" title="Drag" />
                            <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_group(this);" />
                            <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_group(this);" />
                        </div>
                    </div>
				<?php } ?>
				<?php foreach(explode('*#*',$tasks[$i]) as $task) { ?>
                    <div class="task form-group sortable_group">
                        <label class="col-sm-4">Task:</label>
                        <div class="col-sm-6"><input type="text" class="form-control" name="checklist" value="<?= $task ?>" /></div>
                        <div class="col-sm-2">
                            <img src="../img/icons/drag_handle.png" class="inline-img pull-right group-handle" />
                            <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_group(this);" />
                            <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_group(this);" />
                        </div>
                    </div>
				<?php } ?>
				<?php /*foreach(explode('*#*',$checklists[$i]) as $checklist) { ?>
                    <div class="checklist form-group sortable_group">
                        <label class="col-sm-4">Checklist:</label>
                        <div class="col-sm-6"><input type="text" class="form-control" name="items" value="<?= $checklist ?>" /></div>
                        <div class="col-sm-2">
                            <img src="../img/icons/drag_handle.png" class="inline-img pull-right group-handle" />
                            <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_group(this);" />
                            <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_group(this);" />
                        </div>
                    </div>
				<?php }*/ ?>
				<?php foreach(explode('*#*',$intakes[$i]) as $intake) { ?>
                    <div class="intake form-group sortable_group">
                        <label class="col-sm-4">Intake Form:</label>
                        <div class="col-sm-6"><select data-placeholder="Select Form" class="chosen-select-deselect" name="intake_form"><option />
                                <?php foreach($form_list as $form) { ?>
                                    <option <?= $intake == $form['intakeformid'] ? 'selected' : '' ?> value="<?= $form['intakeformid'] ?>"><?= $form['form_name'] ?></option>
                                <?php } ?>
                            </select></div>
                        <div class="col-sm-2">
                            <img src="../img/icons/drag_handle.png" class="inline-img pull-right group-handle" />
                            <img src="../img/remove.png" class="inline-img pull-right" onclick="remove_group(this);" />
                            <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="add_group(this);" />
                        </div>
                    </div>
				<?php } ?>

				<button class="btn brand-btn pull-right" onclick="add_intake(this); return false;">New Intake Forms</button>
				<button class="btn brand-btn pull-right" onclick="add_check(this); return false;">New Checklist</button>
				<button class="btn brand-btn pull-right" onclick="add_workorder(this); return false;">New Work Order</button>
				<button class="btn brand-btn pull-right" onclick="add_ticket(this); return false;">New <?= TICKET_NOUN ?></button>
				<button class="btn brand-btn pull-right" onclick="add_checklist(this); return false;">New Task</button>

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
		$tasks = explode('#*#', $row['checklist']);
		// $items = explode('#*#', $row['items']);
		$intakes = explode('#*#', $row['intakes']);
		foreach($milestone as $j => $value)  {
			if($value != '') {
				echo $value. (!empty($timeline[$j]) ? ': ' : '').$timeline[$j].'<br>';
				if(!empty($tasks[$j]) || !empty($ticket[$j]) || !empty($items[$j]) || !empty($intakes[$j])) {
					echo "<ul>";
					foreach(explode('*#*', $ticket[$j]) as $item) {
						if($item != '' && $item != 'FFMSPLIT') {
							$item = explode('FFMSPLIT',$item);
							$service = mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(`category`,': ',`heading`) service FROM `services` WHERE `serviceid`='".$item[1]."'"))['service'];
							echo "<small><li>".TICKET_NOUN.": ".$item[0]." (Service: ".$service.")</li></small>";
						}
					}
					foreach(explode('*#*', $tasks[$j]) as $item) {
						if($item != '') {
							echo "<small><li>".$item."</li></small>";
						}
					}
					// foreach(explode('*#*', $items[$j]) as $item) {
						// if($item != '') {
							// echo "<small><li>".$item."</li></small>";
						// }
					// }
					foreach(explode('*#*', $intakes[$j]) as $item) {
						if($item != '') {
							echo "<small><li>Intake Form: ".get_field_value('form_name','intake_forms','intakeformid',$item)."</li></small>";
						}
					}
					echo "</ul>";
				}
			}
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