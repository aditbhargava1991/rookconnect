<script type="text/javascript">
$(document).ready(function() {
});
</script>

<div class="col-md-12">

    <div class="form-group clearfix completion_date">
        <label for="first_name" class="col-sm-4 control-label text-right">Business<span class="brand-color">*</span>:</label>
        <div class="col-sm-8">
            <select name="businessid" id="businessid" data-placeholder="Select an Option..." class="chosen-select-deselect form-control" width="380">
                <option value=''></option>
                <?php
                $query = mysqli_query($dbc,"SELECT contactid, name FROM contacts WHERE category='Business' AND deleted=0 ORDER BY category");
                while($row = mysqli_fetch_array($query)) {
                    if ($businessid== $row['contactid']) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $row['contactid']."'>".decryptIt($row['name']).'</option>';
                }
                ?>
            </select>
        </div>
    </div>

    <div class="form-group">
      <label for="site_name" class="col-sm-4 control-label">Contact<span class="text-red">*</span>:</label>
      <div class="col-sm-8">
        <?php
            $project_contactid = !empty($projectid) ? get_project($dbc, $projectid, 'clientid') : '';
        ?>
        <select data-placeholder="Select a Contact..." id="contactid" name="contactid" class="chosen-select-deselect form-control" width="380">
			<option></option>
			<?php $contact_query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT contactid, name, first_name, last_name, email_address, category FROM contacts WHERE businessid='$businessid' AND `deleted`=0 AND `status` > 0 AND `category`!='Sites'"),MYSQLI_ASSOC));
			foreach($contact_query as $row) {
				$email = get_email($dbc, $row);
                echo "<option ".($row == $contactid || $row == $project_contactid ? 'selected' : '')." value='". $row."'>".get_contact($dbc, $row).' &lt;'.(empty($email) ? 'No Email Address' : $email).'&gt;</option>';
            } ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label for="site_name" class="col-sm-4 control-label">Project Name<span class="text-red">*</span>:</label>
      <div class="col-sm-8">
        <select data-placeholder="Select a Project..." name="projectid" id="projectid"  class="chosen-select-deselect form-control" width="380">
          <option value=""></option>
          <?php $project_tabs = get_config($dbc, 'project_tabs');
			if($project_tabs == '') {
				$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
			}
			$project_tabs = explode(',',$project_tabs);
			$project_vars = [];
			foreach($project_tabs as $item) {
				$project_vars[] = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
			}
            //$query = mysqli_query($dbc,"SELECT * FROM (SELECT projectid, projecttype, project_name FROM project WHERE businessid='$businessid' UNION SELECT CONCAT('C',`projectid`), 'Client Project', `project_name` FROM `client_project` WHERE `clientid`='$businessid' AND `deleted`=0) PROJECTS order by project_name");
            $businessid = !empty($projectid) ? get_project($dbc, $projectid, 'businessid') : '';
            $project_query = !empty($projectid) ? "AND businessid='$businessid'" : "";
            $query = mysqli_query($dbc,"SELECT projectid, projecttype, project_name FROM project WHERE deleted=0 $project_query ORDER BY project_name");
            while($row = mysqli_fetch_array($query)) {
				if(substr($row['projectid'],0,1)=='C') {
					echo "<option ".($projectid == $row['projectid'] ? 'selected' : '')." value='".$row['projectid']."'>Client Project: ".$row['project_name'].'</option>';
				}
				/* foreach($project_vars as $key => $type_name) {
					if($type_name == $row['projecttype']) {
						echo "<option ".($projectid == $row['projectid'] ? 'selected' : '')." value='".$row['projectid']."'>".$project_tabs[$key].': '.$row['project_name'].'</option>';
					}
				} */
                echo "<option ".($projectid == $row['projectid'] ? 'selected' : '')." value='".$row['projectid']."'>".$row['project_name'].'</option>';
            }
          ?>
        </select>
      </div>
    </div>

	<!--<div class="form-group clearfix completion_date">
        <label for="first_name" class="col-sm-4 control-label text-right">Date of Call<span class="brand-color">*</span>:</label>
        <div class="col-sm-8">
			<input type="text" name="doc" id="doc" value="<?php echo $followup_date; ?>" class="datepicker form-control">
        </div>
    </div>-->

	<!--
    <div class="form-group">
        <div class="col-sm-4">
            <a href="<?php echo $back_url; ?>" class="btn brand-btn">Back</a>
        </div>
        <div class="col-sm-8">
            <button type="date" name="submit" value="submit" class="btn brand-btn pull-right">Submit</button>
        </div>
    </div>
    -->
</div>
