<?php include_once('../include.php');
$security = get_security($dbc, 'sales');
$strict_view = strictview_visible_function($dbc, 'sales');
if($strict_view > 0) {
    $security['edit'] = 0;
    $security['config'] = 0;
}
if($security['edit'] > 0) {
	$salesid = filter_var($_GET['id'], FILTER_SANITIZE_STRING);
	$sales = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `sales` WHERE `salesid`='$salesid'"));
    $default_staff = $sales['primary_staff'];
	$active_externals = explode(',',$project['external_path']);
    $staff_list = sort_contacts_query($dbc->query("SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY)); ?>
	<script>
	function addPath(btn) {
        var milestone_list = [];
        $(btn).closest('.panel-body').find('.milestone').each(function() {
            var milestone = { name: $(this).find('[name=milestone]').val(), tasks: [], task_staff: [], intakes: [] };
            $(this).find('[name=task]').each(function() {
                milestone.tasks.push(this.value);
                milestone.task_staff.push($(this).closest('li').find('[name=task_staff]').val());
            });
            $(this).find('[name=intake]').each(function() {
                milestone.intakes.push(this.value);
            });
            milestone_list.push(milestone);
        });
		$.ajax({
			url: 'sales_ajax_all.php?action=sales_path',
			method: 'POST',
			data: {
				salesid: '<?= $salesid ?>',
                milestones: milestone_list
			},
	        success: function(response) {
	            console.log(response);
	        }
		});
	}
	</script>
	<div class="col-sm-12">
		<h1>Add <?= SALES_NOUN ?> Task Path<a href="../blank_loading_page.php"><img src="../img/icons/cancel.png" class="pull-right inline-img"></a></h1>
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
                            $milestone = explode('#*#', $path['milestone']);
                            $timeline = explode('#*#', $path['timeline']);
                            $tasks = explode('#*#', $path['checklist']);
                            $intakes = explode('#*#', $path['intakes']);
                            foreach($milestone as $j => $value)  {
                                if($value != '') { ?>
                                    <div class="milestone">
                                        <input type="hidden" name="milestone" value="<?= $value ?>"><?= $value.(!empty($timeline[$j]) ? ': ' : '').$timeline[$j] ?><br>
                                        <?php if(!empty($tasks[$j]) || !empty($items[$j]) || !empty($intakes[$j])) {
                                            echo "<ul class='no-bullet'>";
                                            foreach(explode('*#*', $tasks[$j]) as $i => $item) {
                                                if($item != '') {
                                                    echo "<li><input type='hidden' name='task' value='".urlencode($item)."'>Task: $item <img src='../img/remove.png' class='cursor-hand inline-img' onclick='$(this).closest(\"li\").remove();'><div class='col-sm-4'><select name='task_staff' class='chosen-select-deselect' data-placeholder='Select Staff'><option />";
                                                    foreach($staff_list as $staff) {
                                                        echo '<option '.($default_staff == $staff['contactid'] ? 'selected' : '').' value="'.$staff['contactid'].'">'.$staff['full_name'].'</option>';
                                                    }
                                                    echo "</select></div><div class='clearfix'></div></li>";
                                                }
                                            }
                                            foreach(explode('*#*', $intakes[$j]) as $i => $item) {
                                                if($item != '') {
                                                    echo "<li><input type='hidden' name='intake' value='$item'>Intake Form: ".get_field_value('form_name','intake_forms','intakeformid',$item)." <img src='../img/remove.png' class='cursor-hand inline-img' onclick='$(this).closest(\"li\").remove();'><div class='clearfix'></div></li>";
                                                }
                                            }
                                            echo "</ul>";
                                        } ?>
                                    </div>
                                <?php }
                            }
                            echo "<br /><button class='btn brand-btn pull-right main_path' onclick='addPath(this);'>Add Path Milestones</button>";

                        echo '</div>
                    </div>
                </div>
            ';
		} ?>
	</div></div>
<?php } ?>