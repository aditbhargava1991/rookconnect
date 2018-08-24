<?php include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if($security['edit'] > 0) {
	$projectid = filter_var($_GET['projectid'], FILTER_SANITIZE_STRING);
	$project_path = filter_var($_GET['path'],FILTER_SANITIZE_STRING);
	$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
	$active_paths = explode(',',$project['project_path']);
	$active_externals = explode(',',$project['external_path']); ?>
	<script>
	var paths = <?= json_encode($active_paths) ?>;
	var ex_paths = <?= json_encode($active_externals) ?>;
	$(document).ready(function() {
		setButtons();
	});
	function setButtons() {
		$('.active_tab.main_path').off('click').click(function() {
			$(this).removeClass('active_tab').addClass('add_tab').text('Add to <?= PROJECT_NOUN ?>');
			setButtons();
			var pathid = paths.indexOf($(this).data('path').toString());
			if(pathid >= 0) {
				paths.splice(pathid, 1);
			}
			savePath('project_path', paths, );
		});
		$('.add_tab.main_path').off('click').click(function() {
			$(this).removeClass('add_tab').addClass('active_tab').text('Remove Path');
			setButtons();
			paths.push($(this).data('path').toString());
            tickets = $(this).closest('.panel-body').find('[name=ticket]:checked').map(function() { return this.value; }).get().join('#*#');
            tasks = $(this).closest('.panel-body').find('[name=task]:checked').map(function() { return this.value; }).get().join('#*#');
            items = $(this).closest('.panel-body').find('[name=item]:checked').map(function() { return this.value; }).get().join('#*#');
            intakes = $(this).closest('.panel-body').find('[name=intake]:checked').map(function() { return this.value; }).get().join('#*#');
			savePath('project_path', paths, $(this).data('path').toString(), tickets, tasks, items, intakes);
		});
		$('.active_tab.external').off('click').click(function() {
			$(this).removeClass('active_tab').addClass('add_tab').text('Add External Path to <?= PROJECT_NOUN ?>');
			setButtons();
			var pathid = ex_paths.indexOf($(this).data('path').toString());
			if(pathid >= 0) {
				ex_paths.splice(pathid, 1);
			}
			savePath('external_path', ex_paths);
		});
		$('.add_tab.external').off('click').click(function() {
			$(this).removeClass('add_tab').addClass('active_tab').text('Remove External Path');
			setButtons();
			ex_paths.push($(this).data('path').toString());
			savePath('external_path', ex_paths);
		});
	}
	function savePath(path, path_list, new_path, tickets, tasks, items, intakes) {
		$.ajax({
			url: 'projects_ajax.php?action=update_path',
			method: 'POST',
			data: {
				projectid: '<?= $projectid ?>',
				path_list: path_list.join(','),
				path: path,
                new_path: new_path,
                tickets: tickets,
                tasks: tasks,
                items: items,
                intakes: intakes
			},
	        success: function(response) {
	            console.log(response);
	        }
		});
	}
	</script>
	<div class="col-sm-12">
		<h1>Add / Remove <?= PROJECT_NOUN ?> Paths for <?= get_project_label($dbc, $project) ?><a href="../blank_loading_page.php"><img src="../img/icons/cancel.png" class="pull-right inline-img"></a></h1>
		<?php $paths = mysqli_query($dbc, "SELECT * FROM `project_path_milestone` ORDER BY project_path");
        echo '<div class="panel-group" id="accordion2">';
		while($path = mysqli_fetch_assoc($paths)) {
			$active = in_array($path['project_path_milestone'],$active_paths);
			$external = in_array($path['project_path_milestone'],$active_externals);

                echo '<div class="panel panel-default">
                    <div class="panel-heading">
                        <h4 class="panel-title">
                            <a data-toggle="collapse" data-parent="#accordion2" href="#c_'.$path['project_path_milestone'].'" >
                                '.$path['project_path'].'<span class="glyphicon glyphicon-plus"></span>
                            </a>
                        </h4>
                    </div>

                    <div id="c_'.$path['project_path_milestone'].'" class="panel-collapse collapse">
                        <div class="panel-body">';
                            echo "<button data-path='".$path['project_path_milestone']."' class='btn brand-btn pull-right main_path ".($active ? 'active_tab' : 'add_tab')."'>".($active ? 'Remove Path' : 'Add to '.PROJECT_NOUN)."</button>";
                            echo "<button data-path='".$path['project_path_milestone']."' class='btn brand-btn pull-right external ".($external ? 'active_tab' : 'add_tab')."'>".($external ? 'Remove External Path' : 'Add External Path to '.PROJECT_NOUN)."</button></h3>";
                            $milestone = explode('#*#', $path['milestone']);
                            $timeline = explode('#*#', $path['timeline']);
                            $ticket = explode('#*#', $path['ticket']);
                            $tasks = explode('#*#', $path['checklist']);
                            // $items = explode('#*#', $path['items']);
                            $intakes = explode('#*#', $path['intakes']);
                            foreach($milestone as $j => $value)  {
                                if($value != '') {
                                    echo $value. (!empty($timeline[$j]) ? ': ' : '').$timeline[$j].'<br>';
                                    if(!empty($tasks[$j]) || !empty($ticket[$j]) || !empty($items[$j]) || !empty($intakes[$j])) {
                                        echo "<ul>";
                                        foreach(explode('*#*', $ticket[$j]) as $i => $item) {
                                            if($item != '' && $item != 'FFMSPLIT') {
                                                $item = explode('FFMSPLIT',$item);
                                                $service = mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(`category`,': ',`heading`) service FROM `services` WHERE `serviceid`='".$item[1]."'"))['service'];
                                                echo "<small><li><label class='form-checkbox any-width'><input type='checkbox' checked name='ticket' value='$j|$i'>".TICKET_NOUN.": ".$item[0]." (Service: ".$service.")</label></li></small>";
                                            }
                                        }
                                        foreach(explode('*#*', $tasks[$j]) as $i => $item) {
                                            if($item != '') {
                                                echo "<small><li><label class='form-checkbox any-width'><input type='checkbox' checked name='task' value='$j|$i'>".$item."</label></li></small>";
                                            }
                                        }
                                        // foreach(explode('*#*', $items[$j]) as $i => $item) {
                                            // if($item != '') {
                                                // echo "<small><li><label class='form-checkbox any-width'><input type='checkbox' checked name='item' value='$j|$i'>".$item."</label></li></small>";
                                            // }
                                        // }
                                        foreach(explode('*#*', $intakes[$j]) as $i => $item) {
                                            if($item != '') {
                                                echo "<small><li><label class='form-checkbox any-width'><input type='checkbox' checked name='intake' value='$j|$i'>Intake Form: ".get_field_value('form_name','intake_forms','intakeformid',$item)."</label></li></small>";
                                            }
                                        }
                                        echo "</ul>";
                                    }
                                }
                            }

                        echo '</div>
                    </div>
                </div>
            ';
		} ?>
	</div></div>
<?php } ?>