<div class="col-md-12">
   <?php
    if(!empty($_GET['estimateid'])) {
        $query_check_credentials = "SELECT * FROM bid_comment WHERE estimateid='$estimateid' AND type='note' ORDER BY estimatecommid DESC";
        $result = mysqli_query($dbc, $query_check_credentials);
        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            echo "<table class='table table-bordered'>
            <tr class='hidden-xs hidden-sm'>
            <th>Heading</th>
            <th>Note</th>
            <th>Assign To</th>
            <th>Date</th>
            <th>Added By</th>
            </tr>";
            while($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                $by = $row['created_by'];
                $to = $row['email_comment'];
                echo '<td data-title="Schedule">'.$row['note_heading'].'</td>';
                echo '<td data-title="Schedule">'.html_entity_decode($row['comment']).'</td>';
                echo '<td data-title="Schedule">'.get_staff($dbc, $to).'</td>';
                echo '<td data-title="Schedule">'.$row['created_date'].'</td>';
                echo '<td data-title="Schedule">'.get_staff($dbc, $by).'</td>';
                //echo '<td data-title="Schedule"><a href=\'delete_restore.php?action=delete&ticketcommid='.$row['ticketcommid'].'&ticketid='.$row['ticketid'].'\' onclick="return confirm(\'Are you sure?\')">Delete</a></td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }
    ?>
    <?php

    ?>
        <div class="form-group">
            <label for="first_name" class="col-sm-4 control-label">Note Heading:</label>
            <div class="col-sm-8">
                <select data-placeholder="Choose a Heading..." name="note_heading" class="chosen-select-deselect form-control" width="380">
                  <option value=""></option>
                  <option value="detail_issue">Issue</option>
                  <option value="detail_problem">Problem</option>
                  <option value="detail_gap">GAP</option>
                  <option value="detail_technical_uncertainty">Technical Uncertainty</option>
                  <option value="detail_base_knowledge">Base Knowledge</option>
                  <option value="detail_do">Do</option>
                  <option value="detail_already_known">Already Known</option>
                  <option value="detail_sources">Sources</option>
                  <option value="detail_current_designs">Current Designs</option>
                  <option value="detail_known_techniques">Known Techniques</option>
                  <option value="detail_review_needed">Review Needed</option>
                  <option value="detail_looking_to_achieve">Looking to Achieve</option>
                  <option value="detail_plan">Plan</option>
                  <option value="detail_next_steps">Next Steps</option>
                  <option value="detail_learnt">Learned</option>
                  <option value="detail_discovered">Discovered</option>
                  <option value="detail_tech_advancements">Tech Advancements</option>
                  <option value="detail_work">Work</option>
                  <option value="detail_adjustments_needed">Adjustments Needed</option>
                  <option value="detail_future_designs">Future Designs</option>
                  <option value="detail_targets">Targets</option>
                  <option value="detail_audience">Audience</option>
                  <option value="detail_strategy">Strategy</option>
                  <option value="detail_desired_outcome">Desired Outcome</option>
                  <option value="detail_actual_outcome">Actual Outcome</option>
                  <option value="detail_check">Check</option>
                  <option value="detail_objective">Objective</option>
                  <option value="General">General</option>
                </select>
            </div>
        </div>

      <div class="form-group">
        <label for="site_name" class="col-sm-4 control-label">Note:</label>
        <div class="col-sm-8">
          <textarea name="estimate_comment" rows="4" cols="50" class="form-control" ></textarea>
        </div>
      </div>

        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Send Email:</label>
		  <div class="col-sm-8">
			<input type="checkbox" value="Yes" name="send_email_on_comment">
		  </div>
		</div>

        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Assign/Email To:</label>
		  <div class="col-sm-8">
			<select data-placeholder="Choose a Staff Member..." name="email_comment" class="chosen-select-deselect form-control" width="380">
			  <option value=""></option>
			  <?php
                $cat = '';
                $query = mysqli_query($dbc,"SELECT contactid, first_name, last_name, category FROM contacts WHERE deleted=0 AND (category='Staff' OR businessid='$businessid')  ORDER BY category");

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
		</div>

    <div class="form-group">
        <div class="col-sm-4">
            <!--<a href="<?php //echo $back_url; ?>" class="btn brand-btn">Back</a>-->
			<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;">Back</a>
        </div>
        <div class="col-sm-8">
            <button type="submit" name="submit" value="submit" class="btn brand-btn pull-right">Submit</button>
        </div>
    </div>
</div>