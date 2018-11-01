<?php include_once('../include.php');
$projectid = empty($projectid) ? filter_var($_GET['projectid'],FILTER_SANITIZE_STRING) : $projectid;
$checklistid = empty($checklistid) ? filter_var($_GET['checklistid'],FILTER_SANITIZE_STRING) : $checklistid;
if($projectid > 0) {
    $project_paths = get_project_paths($projectid);
    $checklist_milestone = get_field_value('project_milestone','checklist','checklistid',$checklistid);
    $checklist_milestone = empty($checklist_milestone) ? urldecode($_GET['project_milestone']) : $checklist_milestone;
    if(count($project_paths) > 1) {
        $checklist_path_id = 0;
        foreach($project_paths as $path_details) {
            foreach($path_details['milestones'] as $path_milestone) {
                if($checklist_milestone == $path_milestone['milestone']) {
                    $checklist_path_id = $path_details['path_id'];
                }
            }
        } ?>
        <script>
        function filter_milestones(pathid) {
            $('[name=project_milestone] option').each(function()  {
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
                    <option <?= $checklist_path_id == $path_details['path_id'] ? 'selected' : '' ?> value="<?= $path_details['path_id'] ?>"><?= $path_details['path_name'] ?></option>
                <?php } ?>
            </select>
          </div>
        </div>
    <?php } ?>
    <div class="form-group">
      <label for="site_name" class="col-sm-4 control-label">Milestone & Timeline:</label>
      <div class="col-sm-8">
        <select data-placeholder="Choose an Option..." name="project_milestone" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control"><option />
            <?php foreach($project_paths as $path_details) {
                foreach($path_details['milestones'] as $path_milestone) { ?>
                    <option <?= $checklist_milestone == $path_milestone['milestone'] ? 'selected' : '' ?> style="<?= $path_details['path_id'] != $checklist_path_id && $checklist_path_id > 0 ? 'display:none;' : '' ?>" data-path="<?= $path_details['path_id'] ?>" value="<?= $path_milestone['milestone'] ?>"><?= $path_milestone['label'] ?></option>
                <?php }
            } ?>
        </select>
      </div>
    </div>
<?php }