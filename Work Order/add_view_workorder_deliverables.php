<div class="col-md-12">
    <?php
    if(!empty($_GET['workorderid'])) {
        $query_check_credentials = "SELECT * FROM workorder_deliverables WHERE workorderid='$workorderid' ORDER BY deliverablesid DESC";
        $result = mysqli_query($dbc, $query_check_credentials);
        $num_rows = mysqli_num_rows($result);
        if($num_rows > 0) {
            echo "<table class='table table-bordered'>
            <tr class='hidden-xs hidden-sm'>
            <th>Status</th>
            <th>Assigned To</th>
            <th>Date</th>
            <th>Assigned By</th>
            </tr>";
            while($row = mysqli_fetch_array($result)) {
                echo '<tr>';
                $to = explode(',', $row['contactid']);
                $staff = '';
                foreach($to as $category => $value)  {
                    if($value != '') {
                        $staff .= get_staff($dbc, $value).'<br>';
                    }
                }
                $by = $row['created_by'];
                echo '<td data-title="Schedule">'.$row['status'].'</td>';
                echo '<td data-title="Schedule">'.$staff.'</td>';
                echo '<td data-title="Schedule">'.$row['created_date'].'</td>';
                echo '<td data-title="Schedule">'.get_staff($dbc, $by).'</td>';
                echo '</tr>';
            }
            echo '</table>';
        }
    }
    ?>
      <div class="form-group">
        <label for="site_name" class="col-sm-4 control-label">Status:</label>
        <div class="col-sm-8">
            <select data-placeholder="Select a Heading..." name="status" data-table="workorder" data-id="<?= $workorderid ?>" data-id-field="workorderid" id="status" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $tabs = get_config($dbc, 'workorder_status');
                $each_tab = explode(',', $tabs);
                foreach ($each_tab as $cat_tab) {
                    if ($heading == $cat_tab) {
                        $selected = 'selected="selected"';
                    } else {
                        $selected = '';
                    }
                    echo "<option ".$selected." value='". $cat_tab."'>".$cat_tab.'</option>';
                }
              ?>
            </select>
        </div>
      </div>

        <div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Assign To:</label>
		  <div class="col-sm-8">
			<select data-placeholder="Select a Staff Member..." multiple id="contactid[]" name="contactid[]" data-table="workorder" data-id="<?= $workorderid ?>" data-id-field="workorderid" class="chosen-select-deselect form-control" width="380">
			  <?php
                echo "<option value='Assign to All'>Assign to All</option>";
			  ?>
			  <?php
				$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category='Staff' AND deleted=0 AND `status`=1"),MYSQLI_ASSOC));
				foreach($query as $id) {
					$selected = '';
					echo "<option " . $selected . "value='". $id."'>".get_contact($dbc, $id).'</option>';
				}
			  ?>
			</select>
		  </div>
		</div>

        <div class="form-group clearfix">
            <label for="first_name" class="col-sm-4 control-label text-right">TO DO Date:</label>
            <div class="col-sm-8">
                <input name="to_do_date" value="<?php echo $to_do_date; ?>" data-table="workorder" data-id="<?= $workorderid ?>" data-id-field="workorderid" type="text" class="datepicker"></p>
            </div>
        </div>

        <!--
        <div class="form-group clearfix">
            <label for="first_name" class="col-sm-4 control-label text-right">Internal QA Date:</label>
            <div class="col-sm-8">
                <input name="internal_qa_date" value="<?php echo $internal_qa_date; ?>" type="text" class="datepicker"></p>
            </div>
        </div>
        -->

        <div class="form-group clearfix">
            <label for="first_name" class="col-sm-4 control-label text-right">Deliverable Date:</label>
            <div class="col-sm-8">
                <input name="deliverable_date" value="<?php echo $deliverable_date; ?>" data-table="workorder" data-id="<?= $workorderid ?>" data-id-field="workorderid" type="text" class="datepicker"></p>
            </div>
        </div>
		<?php
		$sender = get_contact($dbc, $_SESSION['contactid'], 'email_address');
		$subject = 'FFM - Work Order Assigned To You';
		$body = 'FFM - Work Order Assigned To You.<br/><br/>
			<a target="_blank" href="'.WEBSITE_URL.'/Work Order/add_workorder.php?workorderid=[WORKORDERID]">Work Order #[WORKORDERID]</a><br/><br/><br/>
			<img src="'.WEBSITE_URL.'/img/ffm-signature.png" width="154" height="77" border="0" alt="">';
		?>
		<div class="form-group">
			<label class="col-sm-4 control-label">Sending Email Address:</label>
			<div class="col-sm-8">
				<input type="text" name="deliverable_email_sender" class="form-control" value="<?php echo $sender; ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Email Subject:</label>
			<div class="col-sm-8">
				<input type="text" name="deliverable_email_subject" class="form-control" value="<?php echo $subject; ?>">
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label">Email Body:</label>
			<div class="col-sm-8">
				<textarea name="deliverable_email_body" class="form-control"><?php echo $body; ?></textarea>
			</div>
		</div>

        <?php if(empty($_GET['pid'])) { ?>
        <div class="form-group">
            <div class="col-sm-4">
                <!--<a href="<?php //echo $back_url; ?>" class="btn brand-btn">Back</a>-->
				<a href="#" class="btn brand-btn" onclick="history.go(-1);return false;" title="The entire form will close without submit if this back button is pressed.">Back</a>
            </div>
            <div class="col-sm-8">
                <button type="submit" name="submit" value="submit" class="btn brand-btn pull-right" title="The entire form will submit and close if this submit button is pressed.">Submit</button>
            </div>
        </div>
        <?php } ?>
</div>
