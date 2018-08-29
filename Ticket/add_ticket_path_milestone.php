<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>'.PROJECT_NOUN.' Path & Milestone</h3>') : '' ?>
<?php foreach($field_sort_order as $field_sort_field) {
	if($field_sort_field == 'FFMCUSTOM Path & Milestone' || (!$custom_accordion && $field_sort_field == 'Path & Milestone')) {
        $project_paths = get_project_paths($projectid);
        if(count($project_paths) > 1) {
            $ticket_path_id = 0;
            $ticket_path_name = '';
            foreach($project_paths as $path_details) {
                foreach($path_details['milestones'] as $path_milestone) {
                    if($milestone_timeline == $path_milestone['milestone']) {
                        $ticket_path_id = $path_details['path_id'];
                        $ticket_path_name = $path_details['path_name'];
                        $milestone_timeline_label = $path_milestone['label'];
                    }
                }
            } ?>
            <script>
            function filter_milestones(pathid) {
                $('[name=milestone_timeline] option').each(function()  {
                    if($(this).data('path') != pathid && pathid > 0) {
                        $(this).hide();
                    } else {
                        $(this).show();
                    }
                }).trigger('select2.change');
            }
            </script>
            <div class="form-group">
              <label for="site_name" class="col-sm-4 control-label"><?= PROJECT_NOUN ?> Path:</label>
              <div class="col-sm-8">
                <?php if($access_all === TRUE) { ?>
                    <select data-placeholder="Select a Path..." name="project_path_list" class="chosen-select-deselect form-control" onchange="filter_milestones(this.value);"><option />
                        <?php foreach($project_paths as $path_details) { ?>
                            <option <?= $ticket_path_id == $path_details['path_id'] ? 'selected' : '' ?> value="<?= $path_details['path_id'] ?>"><?= $path_details['path_name'] ?></option>
                        <?php } ?>
                    </select>
                <?php } else {
                    echo $ticket_path_name;
                    $pdf_contents[] = [PROJECT_NOUN.' Path', $ticket_path_name];
                } ?>
              </div>
            </div>
        <?php } ?>
		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Milestone & Timeline:</label>
		  <div class="col-sm-8">
			<?php if($access_all === TRUE) { ?>
				<select data-placeholder="Choose an Option..." name="milestone_timeline" id="milestone_timeline" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control"><option />
                    <?php foreach($project_paths as $path_details) {
                        foreach($path_details['milestones'] as $path_milestone) { ?>
                            <option <?= $milestone_timeline == $path_milestone['milestone'] ? 'selected' : '' ?> style="<?= $path_details['path_id'] != $ticket_path_id && $ticket_path_id > 0 ? 'display:none;' : '' ?>" data-path="<?= $path_details['path_id'] ?>" value="<?= $path_milestone['milestone'] ?>"><?= $path_milestone['label'] ?></option>
                        <?php }
                    } ?>
				</select>
			<?php } else {
				echo $milestone_timeline_label;
				$pdf_contents[] = ['Milestone & Timeline', $milestone_timeline_label];
			} ?>
		  </div>
		</div>
	<?php }
} ?>