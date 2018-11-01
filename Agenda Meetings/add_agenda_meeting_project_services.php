<?php if (strpos($value_config, ','."Project".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Project:</label>
    <div class="col-sm-8">
        <select name="projectid[]" multiple <?php echo $disable_client; ?> data-placeholder="Select a Project..." class="chosen-select-deselect form-control" width="380">
            <?php $query = mysqli_query($dbc,"SELECT * FROM (SELECT projectid, project_name FROM project WHERE `deleted`=0 AND (`businessid`='$businessid' OR '$businessid' = '') UNION SELECT CONCAT('C',`projectid`), CONCAT('Client Project: ',`project_name`) FROM `client_project` WHERE `deleted`=0 AND (`clientid`='$businessid' OR '$businessid'='')) PROJECTS ORDER BY project_name");
            while($row = mysqli_fetch_array($query)) {
                ?>
                <option <?php if (strpos(','.$projectid.',', ','.$row['projectid'].',') !== FALSE) {
                echo " selected"; } ?> value="<?php echo $row['projectid']; ?>"><?php echo $row['project_name']; ?></option>
            <?php }
            ?>
        </select>
    </div>
</div>
<?php } ?>

<?php if (strpos($value_config, ','."Service".',') !== FALSE) { ?>
<div class="form-group clearfix">
    <label for="first_name" class="col-sm-4 control-label text-right">Service(s):</label>
    <div class="col-sm-8">
        <select name="servicecategory[]" multiple <?php echo $disable_client; ?> data-placeholder="Select a Service..." class="chosen-select-deselect form-control" width="380">
            <?php $query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE deleted=0 ORDER BY category");
            while($row = mysqli_fetch_array($query)) {
                ?>
                <option <?php if (strpos('*#*'.$servicecategory.'*#*', '*#*'.$row['category'].'*#*') !== FALSE) {
                echo " selected"; } ?> value="<?php echo $row['category']; ?>"><?php echo $row['category']; ?></option>
            <?php }
            ?>
        </select>
    </div>
</div>
<?php } ?>
