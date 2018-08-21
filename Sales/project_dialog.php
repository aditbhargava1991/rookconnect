<!-- Dialog -->
<div id="dialog_choose_project" title="Select <?= PROJECT_NOUN ?> to Assign" class="dialog" style="display:none;">
    <?php $project_fields = ','.mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0].',';
    $project_configs = mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project");
    while($project_config = mysqli_fetch_array($project_configs)[0]) {
        $project_fields .= $project_config.',';
    }
    $project_fields = explode(',',$project_fields);
    if(in_array('Information Business', $project_fields)) { ?>
        <div class="form-group">
            <label class="col-sm-4 control-label">Filter <?= PROJECT_TILE ?> by <?= BUSINESS_CAT ?>:</label>
            <div class="col-sm-8">
                <select name="businessid" data-placeholder="Select <?= BUSINESS_CAT ?>" class="chosen-select-deselect form-control"><option />
                    <?php foreach(sort_contacts_query($dbc->query("SELECT `contacts`.`contactid`, `contacts`.`name`, `contacts`.`first_name`, `contacts`.`last_name` FROM `contacts` LEFT JOIN `project` ON `contacts`.`contactid`=`project`.`businessid` WHERE `contacts`.`deleted`=0 AND `contacts`.`status`=1 AND `project`.`deleted`=0 GROUP BY `contacts`.`contactid`")) as $bus_row) { ?>
                        <option value="<?= $bus_row['contactid'] ?>"><?= $bus_row['full_name'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="clearfix"></div>
    <?php }
    if(in_array('Information Contact', $project_fields)) { ?>
        <div class="form-group">
            <label class="col-sm-4 control-label">Filter <?= PROJECT_TILE ?> by <?= CONTACTS_NOUN ?>:</label>
            <div class="col-sm-8">
                <select name="clientid" data-placeholder="Select <?= CONTACTS_NOUN ?>" class="chosen-select-deselect form-control"><option />
                    <?php foreach(sort_contacts_query($dbc->query("SELECT `contacts`.`contactid`, `contacts`.`businessid`, `contacts`.`name`, `contacts`.`first_name`, `contacts`.`last_name` FROM `contacts` LEFT JOIN `project` ON CONCAT(',',`project`.`clientid`,',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') WHERE `contacts`.`deleted`=0 AND `contacts`.`status`=1 AND `project`.`deleted`=0 GROUP BY `contacts`.`contactid`")) as $cont_row) { ?>
                        <option data-business="<?= $cont_row['businessid'] ?>" value="<?= $cont_row['contactid'] ?>"><?= $cont_row['full_name'] ?></option>
                    <?php } ?>
                </select>
            </div>
        </div>
        <div class="clearfix"></div>
    <?php } ?>
    <div class="form-group">
        <label class="col-sm-4 control-label"><?= PROJECT_TILE ?>:</label>
        <div class="col-sm-8">
            <select name="projectid" data-placeholder="Select <?= PROJECT_NOUN ?>" class="chosen-select-deselect form-control">
                <option></option><?php
                $get_projects = mysqli_query($dbc, "SELECT `projectid`, `businessid`, `clientid`, `project_name` FROM `project` WHERE project_name<>'' AND deleted=0 ORDER BY project_name");
                if ($get_projects->num_rows>0) {
                    while ($row_project=mysqli_fetch_assoc($get_projects)) { ?>
                        <option data-business="<?= $row_project['businessid'] ?>" data-client=",<?= $row_project['clientid'] ?>," value="<?=$row_project['projectid']?>"><?=$row_project['project_name']?></option><?php
                    }
                } ?>
            </select>
        </div>
    </div>
</div>