<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>'.PROJECT_NOUN.' Path & Milestone</h3>') : '' ?>
<?php foreach($field_sort_order as $field_sort_field) {
	if($field_sort_field == 'FFMCUSTOM Path & Milestone' || (!$custom_accordion && $field_sort_field == 'Path & Milestone')) { ?>
		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Milestone & Timeline:</label>
		  <div class="col-sm-8">
			<?php if($access_all === TRUE) { ?>
				<select data-placeholder="Choose an Option..." name="milestone_timeline" id="milestone_timeline" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control" width="580">
					<option value=""></option>
					<?php foreach(explode(',',$project_path) as $pathid) {
						if($pathid > 0) {
							$milestones = explode('#*#',get_field_value('milestone','project_path_milestone','project_path_milestone',$pathid));
							$prior_sort = 0;
							foreach($milestones as $i => $milestone) {
								$milestone_rows = $dbc->query("SELECT `sort` FROM `project_path_custom_milestones` WHERE `projectid`='$projectid' AND `milestone`='$milestone' AND `pathid`='$pathid' AND `path_type`='I'");
								if($milestone_rows->num_rows > 0) {
									$prior_sort = $milestone_rows->fetch_assoc()['sort'];
								} else if($milestone != 'Unassigned') {
									$dbc->query("INSERT INTO `project_path_custom_milestones` (`projectid`,`milestone`,`label`,`path_type`,`pathid`,`sort`) VALUES ('$projectid','$milestone','$milestone','I','$pathid','$prior_sort')");
								}
							}
						}
					}
					$milestone_list = $dbc->query("SELECT `milestones`.`id`, `milestones`.`milestone`, `milestones`.`label`, `milestones`.`sort`  FROM `project_path_custom_milestones` `milestones` LEFT JOIN `project` ON `milestones`.`projectid`=`project`.`projectid` AND CONCAT(',',`project`.`project_path`,',') LIKE CONCAT('%,',`milestones`.`pathid`,',%') WHERE `project`.`projectid`='$projectid' AND `milestones`.`path_type`='I' AND `milestones`.`deleted`=0 ORDER BY `milestones`.`pathid`,`milestones`.`path_type`,`milestones`.`sort`,`milestones`.`id`");
					while($milestone_row = $milestone_list->fetch_assoc()) {
						echo "<option ".($milestone_timeline == $milestone_row['milestone'] ? 'selected' : '')." value='". $milestone_row['milestone']."'>".$milestone_row['label'].'</option>';
					}
				  ?>
				</select>
			<?php } else {
				$milestone_timeline_label = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project_path_custom_milestones` WHERE `milestone` = '$milestone_timeline'"))['label'];
				echo $milestone_timeline_label;
				$pdf_contents[] = ['Milestone & Timeline', $milestone_timeline_label];
			} ?>
		  </div>
		</div>
	<?php }
} ?>