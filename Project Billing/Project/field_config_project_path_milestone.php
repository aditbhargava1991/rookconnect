<?php
/*
Dashboard
*/
include_once('../include.php');
error_reporting(0);

if (isset($_POST['submit_general'])) {
	$project_path = filter_var($_POST['project_path'],FILTER_SANITIZE_STRING);

	$milestone_arr = [];
	$timeline_arr = [];
	$temp_checklist_arr = [];
	$temp_ticket_arr = [];
	$temp_workorder_arr = [];
	$checklist_arr = [];
	$ticket_arr = [];
	$workorder_arr = [];

	foreach($_POST as $field => $value) {
		if(strpos($field,'milestone_') !== false) {
			$key = explode('_',$field)[1];
			$milestone_arr[$key] = $value;
		}
		else if(strpos($field,'timeline_') !== false) {
			$key = explode('_',$field)[1];
			$timeline_arr[$key] = $value;
		}
		else if(strpos($field,'checklist_item_') !== false) {
			$key = explode('_',$field)[2];
			$temp_checklist_arr[$key] = implode('*#*',array_filter($value));
		}
		else if(strpos($field,'ticket_item_') !== false) {
			$key = explode('_',$field)[2];
			$tickets_i = [];
			$services_i = $_POST['ticket_service_'.$key];
			foreach($value as $i => $heading) {
				$tickets_i[] = $heading.'FFMSPLIT'.$services_i[$i];
			}
			$temp_ticket_arr[$key] = implode('*#*',array_filter($tickets_i));
		}
		else if(strpos($field,'work_order_item_') !== false) {
			$key = explode('_',$field)[3];
			$temp_workorder_arr[$key] = implode('*#*',array_filter($value));
		}
	}
	foreach($milestone_arr as $key => $value) {
		if($value != '') {
			$checklist_arr[$key] = $temp_checklist_arr[$key];
			$ticket_arr[$key] = $temp_ticket_arr[$key];
			$workorder_arr[$key] = $temp_workorder_arr[$key];
		} else {
			unset($milestone_arr[$key]);
		}
	}

	$milestone = filter_var(implode('#*#',$milestone_arr),FILTER_SANITIZE_STRING);
	$timeline = filter_var(implode('#*#',$timeline_arr),FILTER_SANITIZE_STRING);
	$checklist = filter_var(implode('#*#',$checklist_arr),FILTER_SANITIZE_STRING);
	$ticket = filter_var(implode('#*#',$ticket_arr),FILTER_SANITIZE_STRING);
	$workorder = filter_var(implode('#*#',$workorder_arr),FILTER_SANITIZE_STRING);

    if($_POST['project_path_milestone'] == 'NEW') {
        $query_insert_config = "INSERT INTO `project_path_milestone` (`project_path`, `milestone`, `timeline`, `checklist`, `ticket`, `workorder`)
			VALUES ('$project_path', '$milestone', '$timeline', '$checklist', '$ticket', '$workorder')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    } else {
        $project_path_milestone = $_POST['project_path_milestone'];
        $query_update_vendor = "UPDATE `project_path_milestone` SET `project_path` = '$project_path', `milestone` = '$milestone',`timeline` = '$timeline', `checklist` = '$checklist',
			`ticket` = '$ticket', `workorder` = '$workorder' WHERE `project_path_milestone` = '$project_path_milestone'";
        $result_update_vendor = mysqli_query($dbc, $query_update_vendor);
    }

    echo '<script type="text/javascript"> window.location.replace("?"); </script>';
}
?>
<script>
$(document).ready(function(){
    var count = 1;

    $('#add_new_row').on( 'click', function () {
        $('#deleteservices_0').show();
        var clone = $('.additional_row').clone();
        clone.find('[name^=checklist_item]').closest('.form-group').remove();
        clone.find('[name^=ticket_item]').closest('.form-group').remove();
        clone.find('[name^=work_order_item]').closest('.form-group').remove();
        clone.find('.form-control').val('');
        clone.find('.mt').attr('id', 'mt_'+count);
        clone.find('.milestone').attr('id', 'milestone_'+count);
        clone.find('.timeline').attr('id', 'timeline_'+count);
        clone.find('.milestone').attr('name', 'milestone_'+count);
        clone.find('.timeline').attr('name', 'timeline_'+count);
        clone.find('#deleteservices_0').attr('id', 'deleteservices_'+count);
        clone.find('#add_checklist_item_0').attr('id', 'add_checklist_item_'+count);
        clone.find('#add_ticket_item_0').attr('id', 'add_ticket_item_'+count);
        clone.find('#add_work_order_item_0').attr('id', 'add_work_order_item_'+count);
        clone.removeClass("additional_row");
        $('#add_here_new_data').append(clone);
        count++;
        return false;
    });
	$('[name^=ticket_service_]').each(function() {
		var select = this;
		var id = $(select).data('service');
		$.ajax({
			method: 'GET',
			url: 'project_ajax_all.php?fill=ticket_template_service_list&id='+id,
			success: function(response) {
				$(select).empty().append(response).trigger('change.select2');
			}
		});
	});
});
function deleteService(sel, hide, blank) {
	var typeId = sel.id;
	var arr = typeId.split('_');

    $("#"+hide+arr[1]).hide();
    $("#"+blank+arr[1]).val('');
}
function addChecklistItem(sel) {
	var div = $(sel).closest('.form-group').find('[name^=timeline]').closest('div');
	var id = $(sel).attr('id').split('_')[3];
	var checklist_row = '<div class="form-group"><label class="control-label col-sm-2">Checklist Item:</label>'+
		'<div class="col-sm-9"><input type="text" name="checklist_item_'+id+'[]" class="form-control" placeholder="Enter a Checklist Item"></div>'+
		'<div class="col-sm-1"><button onclick="$(this).closest(\'.form-group\').remove(); return false;" class="btn brand-btn">X</button></div></div>';
	div.append(checklist_row);
}
function addTicket(sel) {
	var div = $(sel).closest('.form-group').find('[name^=timeline]').closest('div');
	var id = $(sel).attr('id').split('_')[3];
	var ticket_row = '<div class="form-group"><label class="control-label col-sm-2"><?= TICKET_NOUN ?> Heading:</label>'+
		'<div class="col-sm-4"><input type="text" name="ticket_item_'+id+'[]" class="form-control" placeholder="Enter a <?= TICKET_NOUN ?> Heading"></div>'+
		'<label class="control-label col-sm-1">Service:</label>'+
		'<div class="col-sm-4"><select name="ticket_service_'+id+'[]" class="form-control chosen-select-deselect" data-placeholder="Select a Service">[OPTIONS]</select></div>'+
		'<div class="col-sm-1"><button onclick="$(this).closest(\'.form-group\').remove(); return false;" class="btn brand-btn">X</button></div></div>';
	$.ajax({
		method: 'GET',
		url: 'project_ajax_all.php?fill=ticket_template_service_list&id=0',
		success: function(response) {
			ticket_row = ticket_row.replace('[OPTIONS]',response);
			div.append(ticket_row);
			resetChosen($('.chosen-select-deselect'));
		}
	});
}
function addWorkOrder(sel) {
	var div = $(sel).closest('.form-group').find('[name^=timeline]').closest('div');
	var id = $(sel).attr('id').split('_')[4];
	var work_order_row = '<div class="form-group"><label class="control-label col-sm-2">Work Order Heading:</label>'+
		'<div class="col-sm-9"><input type="text" name="work_order_item_'+id+'[]" class="form-control" placeholder="Enter Work Order Heading"></div>'+
		'<div class="col-sm-1"><button onclick="$(this).closest(\'.form-group\').remove(); return false;" class="btn brand-btn">X</button></div></div>';
	div.append(work_order_row);
}
</script>
</head>
<body>

<?php include_once('../navigation.php'); ?>

<div class="container">
<div class="row">
<h1><?php echo PROJECT_TILE; ?></h1>
<a href="project.php" class="btn brand-btn">Back to Dashboard</a>
<br><br>

<?php $project_tabs = get_config($dbc, 'project_tabs');
if($project_tabs == '') {
	$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
}
mysqli_query($dbc, "UPDATE `field_config_project` SET `type`=LOWER(REPLACE(REPLACE(`type`,' ','_'),'&',''))");

$project_tabs = explode(',',$project_tabs);
$project_vars = [];

foreach($project_tabs as $item) {
	$var_name = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
	$project_vars[] = $var_name;
} ?>

<div class="mobile-100-container">
	<span class="nav-subtab no-popover"><a href="field_config_project.php?type=Pending"><button type="button" class="btn brand-btn mobile-block mobile-100">General</button></a>&nbsp;&nbsp;</span>
	<span class="nav-subtab">
		<span class="popover-examples list-inline" style="margin:0 2px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to create a new Project Path and Milestone."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<a href="field_config_project_path_milestone.php"><button type="button" class="btn brand-btn mobile-block mobile-100 active_tab">Project Template</button></a>&nbsp;&nbsp;
	</span>
	<?php foreach($project_tabs as $key => $tab_name): ?>
		<span class="nav-subtab no-popover"><a href="field_config_project.php?type=<?php echo $project_vars[$key]; ?>"><button type="button" class="btn brand-btn mobile-block mobile-100"><?php echo $tab_name; ?></button></a>&nbsp;&nbsp;</span>
	<?php endforeach; ?>
</div><!-- .mobile-100-container -->

<br><br>
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

<?php
$project_path = '';
$milestone = '';
$timeline = '';
$checklist = '';
$ticket = '';
$workorder = '';

if(!empty($_GET['project_path_milestone'])) {
    $project_path_milestone = $_GET['project_path_milestone'];
    $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM project_path_milestone WHERE project_path_milestone='$project_path_milestone'"));

    $project_path = $get_contact['project_path'];
    $milestone = $get_contact['milestone'];
    $timeline = $get_contact['timeline'];
    $checklist = $get_contact['checklist'];
    $ticket = $get_contact['ticket'];
    $workorder = $get_contact['workorder'];
?>
<input type="hidden" id="project_path_milestone" name="project_path_milestone" value="<?php echo $project_path_milestone ?>" />
<?php   }      ?>
<?php if(!empty($_GET['project_path_milestone'])): ?>
	<div class="panel-group" id="accordion2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_tilename" >
						<?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Template<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_tilename" class="panel-collapse collapse">
				<div class="panel-body">
					  <div class="form-group">
						<label for="fax_number"	class="col-sm-4	control-label"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?> Template Name:</label>
						<div class="col-sm-8">
							<input name="project_path" type="text" value = "<?php echo $project_path; ?>" class="form-control">
						</div>
					  </div>
				</div>
			</div>
		</div>

		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_next" >
						Milestone<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_next" class="panel-collapse collapse">
				<div class="panel-body">
				<div class="form-group clearfix">
					<label class="col-sm-3 text-center">Milestone</label>
					<label class="col-sm-5 text-center">Timeline</label>
				</div>
				<?php
					$each_milestone = explode('#*#',$milestone);
					$each_timeline = explode('#*#',$timeline);
					$each_ticket = explode('#*#',$ticket);
					$each_workorder = explode('#*#',$workorder);
					$each_checklist = explode('#*#',$checklist);

					$total_count = mb_substr_count($milestone,'#*#');
					$mt_id = 500;
					for($emp_loop=0; $emp_loop<=$total_count; $emp_loop++) {
						$ms = '';
						$tl = '';
						$tk = '';
						$wo = '';
						$cl = '';

						if(isset($each_milestone[$emp_loop])) {
							$ms = $each_milestone[$emp_loop];
						}
						if(isset($each_timeline[$emp_loop])) {
							$tl = $each_timeline[$emp_loop];
						}
						if(isset($each_ticket[$emp_loop])) {
							$tk = $each_ticket[$emp_loop];
						}
						if(isset($each_workorder[$emp_loop])) {
							$wo = $each_workorder[$emp_loop];
						}
						if(isset($each_checklist[$emp_loop])) {
							$cl = $each_checklist[$emp_loop];
						}
						if($ms != '') {
						?>

						<div class="form-group clearfix mt" id="mt_<?php echo $mt_id; ?>">
							<div class="col-sm-3">
								<input name="milestone_<?php echo $mt_id; ?>" id="milestone_<?php echo $mt_id; ?>" value = "<?php echo $ms; ?>" type="text" class="form-control milestone">
							</div>
							<div class="col-sm-7">
								<input name="timeline_<?php echo $mt_id; ?>" id="timeline_<?php echo $mt_id; ?>" value="<?php echo $tl; ?>" type="text" class="form-control timeline">
								<?php $items = explode('*#*',$cl);
								foreach($items as $checklist_item):
									if($checklist_item != ''): ?>
										<div class="form-group">
											<label class="control-label col-sm-2">Checklist Item:</label>
											<div class="col-sm-9"><input type="text" name="checklist_item_<?php echo $mt_id; ?>[]" class="form-control" placeholder="Enter a Checklist Item" value="<?php echo $checklist_item; ?>"></div>
											<div class="col-sm-1"><button onclick="$(this).closest('.form-group').remove(); return false;" class="btn brand-btn">X</button></div>
										</div>
									<?php endif;
								endforeach;
								$items = explode('*#*',$tk);
								foreach($items as $ticket):
									if($ticket != ''):
										$ticket = explode('FFMSPLIT', $ticket);
										$heading = $ticket[0];
										$service = $ticket[1]; ?>
										<div class="form-group">
											<label class="control-label col-sm-2"><?= TICKET_NOUN ?> Heading:</label>
											<div class="col-sm-4"><input type="text" name="ticket_item_<?php echo $mt_id; ?>[]" class="form-control" placeholder="Enter a <?= TICKET_NOUN ?> Heading" value="<?php echo $heading; ?>"></div>
											<label class="control-label col-sm-1">Service:</label>
											<div class="col-sm-4"><select name="ticket_service_<?php echo $mt_id; ?>[]" class="form-control chosen-select-deselect" data-placeholder="Select a Service" data-service="<?php echo $service; ?>"></select></div>
											<div class="col-sm-1"><button onclick="$(this).closest('.form-group').remove(); return false;" class="btn brand-btn">X</button></div>
										</div>
									<?php endif;
								endforeach;
								$items = explode('*#*',$wo);
								foreach($items as $work_order):
									if($work_order != ''): ?>
										<div class="form-group">
											<label class="control-label col-sm-2">Work Order Heading:</label>
											<div class="col-sm-9"><input type="text" name="work_order_item_<?php echo $mt_id; ?>[]" class="form-control" placeholder="Enter a Work Order Heading" value="<?php echo $work_order; ?>"></div>
											<div class="col-sm-1"><button onclick="$(this).closest('.form-group').remove(); return false;" class="btn brand-btn">X</button></div>
										</div>
									<?php endif;
								endforeach; ?>
							</div>
							<div class="col-sm-2 m-top-mbl" >
								<a href="#" onclick="deleteService(this,'mt_','milestone_'); return false;" id="deleteservices_<?php echo $mt_id; ?>" class="btn brand-btn">Delete</a>
								<a href="#" onclick="addChecklistItem(this); return false;" id="add_checklist_item_<?php echo $mt_id; ?>" class="btn brand-btn">Checklist</a>
								<a href="#" onclick="addTicket(this); return false;" id="add_ticket_item_<?php echo $mt_id; ?>" class="btn brand-btn"><?= TICKET_NOUN ?></a>
								<?php if(tile_visible($dbc, 'work_order')) { ?>
									<a href="#" onclick="addWorkOrder(this); return false;" id="add_work_order_item_<?php echo $mt_id; ?>" class="btn brand-btn">Work Order</a>
								<?php } ?>
							</div>
						</div>
				<?php
						}
						$mt_id++; } ?>

				<div class="additional_row">
					<div class="clearfix"></div>
					<div class="form-group clearfix mt" id="mt_0">
						<div class="col-sm-3">
							<input name="milestone_0" id="milestone_0" type="text" class="form-control milestone">
						</div>
						<div class="col-sm-7">
							<input name="timeline_0" type="text" class="form-control timeline">
						</div>
						<div class="col-sm-2 m-top-mbl" >
							<a href="#" onclick="deleteService(this,'mt_','milestone_'); return false;" id="deleteservices_0" class="btn brand-btn">Delete</a>
							<a href="#" onclick="addChecklistItem(this); return false;" id="add_checklist_item_0" class="btn brand-btn">Checklist</a>
							<a href="#" onclick="addTicket(this); return false;" id="add_ticket_item_0" class="btn brand-btn"><?= TICKET_NOUN ?></a>
							<?php if(tile_visible($dbc, 'work_order')) { ?>
								<a href="#" onclick="addWorkOrder(this); return false;" id="add_work_order_item_0" class="btn brand-btn">Work Order</a>
							<?php } ?>
						</div>
					</div>
				</div>

				<div id="add_here_new_data"></div>
				<button id="add_new_row" class="btn brand-btn">Add Another Milestone</button>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-4 clearfix">
			<a href="?" class="btn brand-btn btn-lg">Back</a>
		</div>
		<div class="col-sm-8">
			<button	type="submit" name="submit_general"	value="Submit" class="btn brand-btn btn-lg	pull-right">Submit</button>
		</div>
	</div>
<?php else: ?>
	<div class="form-group">
		<div class="col-sm-12">
			<a href="?project_path_milestone=NEW" class="btn brand-btn btn-lg	pull-right">Add Template</a>
		</div>
	</div>
	<?php $query_check_credentials = "SELECT * FROM project_path_milestone ORDER BY `project_path`";
	$result = mysqli_query($dbc, $query_check_credentials);
	echo "<table class='table table-bordered'>";
	echo "<tr class='hidden-xs hidden-sm'>";
	echo "<th>".(PROJECT_TILE=='Projects' ? "Project" : PROJECT_TILE)." Path</th>";
	echo "<th>Milestone & Timeline</th>
	<th>Function</th>";
	echo "</tr>";
	while($row = mysqli_fetch_array($result)) {
		echo '<tr>';
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
				echo $value. (!empty($timeline[$j]) ? ' : ' : '').$timeline[$j].'<br>';
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
		echo '<a href=\'field_config_project_path_milestone.php?project_path_milestone='.$row['project_path_milestone'].'\'>Edit</a>';
		echo '</td>';
		echo '</tr>';
	}
	echo '</table>'; ?>
	<div class="form-group">
		<div class="col-sm-4 clearfix">
			<a href="project.php" class="btn brand-btn btn-lg">Back</a>
		</div>
		<div class="col-sm-8">
			<a href="?project_path_milestone=NEW" class="btn brand-btn btn-lg	pull-right">Add Template</a>
		</div>
	</div>
<?php endif; ?>

</form>
</div>
</div>
<?php include_once('../footer.php'); ?>
