<div class="col-md-12">
   <?php
    if(!empty($_GET['ticketid']) || !empty($_GET['edit'])) {
        $query_check_credentials = "SELECT * (SELECT * FROM project_comment WHERE projectid='$projectid' AND type='note' UNION SELECT * FROM `client_project_comment` WHERE CONCAT('C',`projectid`)='$projectid') ORDER BY projectcommid DESC";

        $result = mysqli_query($dbc, $query_check_credentials);
        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            echo "<table class='table table-bordered'>
            <tr class='hidden-xs hidden-sm'>
            <th>Note</th>
            <th>Assign To</th>
            <th>Date</th>
            <th>Added By</th>
            </tr>";
            while($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                $by = $row['created_by'];
                $to = $row['email_comment'];
                echo '<td data-title="Schedule">'.html_entity_decode($row['comment']).'</td>';
                echo '<td data-title="Schedule">'.get_staff($dbc, $to).'</td>';
                echo '<td data-title="Schedule">'.$row['created_date'].'</td>';
                echo '<td data-title="Schedule">'.get_staff($dbc, $by).'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }
    ?>
    <?php

        $base_field_config_all = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_project WHERE type='$projecttype'"));
        $base_field_config = ','.$base_field_config_all['config_fields'].',';

    ?>

        <div class="form-group">
            <label for="first_name" class="col-sm-4 control-label">Note Heading:</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Heading..." name="project_note_heading" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Issue".',') !== FALSE) { ?>
                  <option value="detail_issue">Issue</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Problem".',') !== FALSE) { ?>
                  <option value="detail_problem">Problem</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details GAP".',') !== FALSE) { ?>
                  <option value="detail_gap">GAP</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Technical Uncertainty".',') !== FALSE) { ?>
                  <option value="detail_technical_uncertainty">Technical Uncertainty</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Base Knowledge".',') !== FALSE) { ?>
                  <option value="detail_base_knowledge">Base Knowledge</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Do".',') !== FALSE) { ?>
                  <option value="detail_do">Do</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Already Known".',') !== FALSE) { ?>
                  <option value="detail_already_known">Already Known</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Sources".',') !== FALSE) { ?>
                  <option value="detail_sources">Sources</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Current Designs".',') !== FALSE) { ?>
                  <option value="detail_current_designs">Current Designs</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Known Techniques".',') !== FALSE) { ?>
                  <option value="detail_known_techniques">Known Techniques</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Review Needed".',') !== FALSE) { ?>
                  <option value="detail_review_needed">Review Needed</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Looking to Achieve".',') !== FALSE) { ?>
                  <option value="detail_looking_to_achieve">Looking to Achieve</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Plan".',') !== FALSE) { ?>
                  <option value="detail_plan">Plan</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Next Steps".',') !== FALSE) { ?>
                  <option value="detail_next_steps">Next Steps</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Learned".',') !== FALSE) { ?>
                  <option value="detail_learnt">Learned</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Discovered".',') !== FALSE) { ?>
                  <option value="detail_discovered">Discovered</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Tech Advancements".',') !== FALSE) { ?>
                  <option value="detail_tech_advancements">Tech Advancements</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Work".',') !== FALSE) { ?>
                  <option value="detail_work">Work</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Adjustments Needed".',') !== FALSE) { ?>
                  <option value="detail_adjustments_needed">Adjustments Needed</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Future Designs".',') !== FALSE) { ?>
                  <option value="detail_future_designs">Future Designs</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Targets".',') !== FALSE) { ?>
                  <option value="detail_targets">Targets</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Audience".',') !== FALSE) { ?>
                  <option value="detail_audience">Audience</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Strategy".',') !== FALSE) { ?>
                  <option value="detail_strategy">Strategy</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Desired Outcome".',') !== FALSE) { ?>
                  <option value="detail_desired_outcome">Desired Outcome</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Actual Outcome".',') !== FALSE) { ?>
                  <option value="detail_actual_outcome">Actual Outcome</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Check".',') !== FALSE) { ?>
                  <option value="detail_check">Check</option>
                  <?php } ?>
                  <?php if (strpos(','.$base_field_config.',', ','."Details Objective".',') !== FALSE) { ?>
                  <option value="detail_objective">Objective</option>
                  <?php } ?>
                  <option value="General">General</option>
                </select>

            </div>
        </div>


      <div class="form-group">
        <label for="site_name" class="col-sm-4 control-label">Note:</label>
        <div class="col-sm-12">
          <textarea name="project_comment" rows="4" cols="50" class="form-control" ></textarea>
        </div>
      </div>

        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Send Email:</label>
		  <div class="col-sm-8">
			<input type="checkbox" value="Yes" name="send_email_on_project_comment" onclick="project_comment_check_send_email(this);">
		  </div>
		</div>

        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Assign/Email To:</label>
		  <div class="col-sm-8">
			<select data-placeholder="Choose a Staff Member..." name="project_email_comment" class="chosen-select-deselect form-control" width="380">
			  <option value=""></option>
			  <?php
                $cat = '';
                $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name, category FROM contacts WHERE deleted=0 AND status > 0 AND (category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." OR businessid='$businessid')  ORDER BY category");
				while($row = mysqli_fetch_array($query)) {
                    if($cat != $row['category']) {
                        echo '<optgroup label="'.$row['category'].'">';
                        $cat = $row['category'];
                    }
					echo "<option value='". $row['contactid']."'>".decryptIt($row['first_name']).' '.decryptIt($row['last_name']).'</option>';
				}
			  ?>
			</select>
		  </div>
		</div><?php
		$sender = get_email($dbc, $_SESSION['contactid']);
		$subject = 'Note added on Project for you to Review';
		if(substr($projectid,0,1) == 'C') {
			$body = 'Note : [NOTE]<br><br>
                Please click below Project link to view all information.<br>
                <a target="_blank" href="'.WEBSITE_URL.'/Client Project/add_project.php?projectid=[PROJECTID]&note=add_view">Project #[PROJECTID]</a><br>';
		} else {
			$body = 'Note : [NOTE]<br><br>
                Please click below Project link to view all information.<br>
                <a target="_blank" href="'.WEBSITE_URL.'/Project/add_project.php?projectid=[PROJECTID]&note=add_view">Project #[PROJECTID]</a><br>';
		}
		?>
		<script>
		function project_comment_check_send_email(checked) {
			if(checked.checked) {
				$('#project_comment_email_send_div').show();
			} else {
				$('#project_comment_email_send_div').hide();
			}
		}
		</script>
		<div id="project_comment_email_send_div" style="display:none;">
			<div class="form-group">
				<label class="col-sm-4 control-label">Sending Email Address:</label>
				<div class="col-sm-8">
					<input type="text" name="project_comment_email_sender" class="form-control" value="<?php echo $sender; ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Subject:</label>
				<div class="col-sm-8">
					<input type="text" name="project_comment_email_subject" class="form-control" value="<?php echo $subject; ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Body:</label>
				<div class="col-sm-12">
					<textarea name="project_comment_email_body" class="form-control"><?php echo $body; ?></textarea>
				</div>
			</div>
		</div>

    <div class="form-group">
        <div class="col-sm-4">
			<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>
        </div>
        <div class="col-sm-8">
			<span class="popover-examples list-inline pull-right" style="margin-left:-6em;"><a data-toggle="tooltip" data-placement="top" title="" data-original-title="The entire form will submit and close if this submit button is pressed.">
				<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed." onclick="return false;" class="btn brand-btn">Submit</button></a></span>
			<button type="submit" name="submit" value="submit" title="The entire form will submit and close if this submit button is pressed." class="btn brand-btn pull-right">Submit</button>
        </div>
    </div>
</div>
