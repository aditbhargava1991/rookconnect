<?php include_once('../include.php');
$projectid = empty($projectid) ? filter_var($_GET['projectid'],FILTER_SANITIZE_STRING) : $projectid;
$intakeid = empty($intakeid) ? filter_var($_GET['intakeid'],FILTER_SANITIZE_STRING) : $intakeid;
if($projectid > 0) {
    $project_paths = get_project_paths($projectid);
    $intake_milestone = get_field_value('project_milestone','intake','intakeid',$intakeid);
    if(count($project_paths) > 1) {
        $intake_path_id = 0;
        foreach($project_paths as $path_details) {
            foreach($path_details['milestones'] as $path_milestone) {
                if($intake_milestone == $path_milestone['milestone']) {
                    $intake_path_id = $path_details['path_id'];
                }
            }
        } ?>
        <script>
        function filter_milestones(pathid) {
            $('[name=milestone] option').each(function()  {
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
            <select data-placeholder="Select a Path..." name="project_path_list" class="chosen-select-deselect form-control" onchange="filter_milestones(this.value);"><option />
                <?php foreach($project_paths as $path_details) { ?>
                    <option <?= $intake_path_id == $path_details['path_id'] ? 'selected' : '' ?> value="<?= $path_details['path_id'] ?>"><?= $path_details['path_name'] ?></option>
                <?php } ?>
            </select>
          </div>
        </div>
    <?php } ?>
    <div class="form-group">
      <label for="site_name" class="col-sm-4 control-label">Milestone & Timeline:</label>
      <div class="col-sm-8">
        <select data-placeholder="Choose an Option..." name="milestone" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control"><option />
            <?php foreach($project_paths as $path_details) {
                foreach($path_details['milestones'] as $path_milestone) { ?>
                    <option <?= $intake_milestone == $path_milestone['milestone'] ? 'selected' : '' ?> style="<?= $path_details['path_id'] != $intake_path_id && $intake_path_id > 0 ? 'display:none;' : '' ?>" data-path="<?= $path_details['path_id'] ?>" value="<?= $path_milestone['milestone'] ?>"><?= $path_milestone['label'] ?></option>
                <?php }
            } ?>
        </select>
      </div>
    </div>
<?php }