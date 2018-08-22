<script type="text/javascript">
$(document).on('change', 'select[name="staffid"]', function() { submitForm(); });
$(document).on('change', 'select[name="form_id"]', function() { submitForm(); });
$(document).on('change', 'select[name="status"]', function() { submitForm(); });
</script>

<form name="form_sites" method="post" action="" class="form-inline" role="form">
<div class="notice triple-gap-bottom popover-examples">
	<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
	<div class="col-sm-11"><span class="notice-name">NOTE:</span>
	Following up can be an essential element in protecting your business against staff neglect. Through this section you can schedule follow ups on all aspects, track those follow ups and hold all parties accountable to ensure the company is protected at all times.</div>
	<div class="clearfix"></div>
</div>

<?php if (!empty($_POST['send_follow_up_email']) || !empty($_GET['action'])) {
	$assign_ids = $_POST['check_send_email'];
	$assign_ids[] = $_GET['assign_id'];
	foreach($assign_ids as $assign_id) {
		if($assign_id > 0) {
			$assigned = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_assign` LEFT JOIN `user_forms` ON `user_form_assign`.`form_id`=`user_forms`.`form_id` WHERE `assign_id`='$assign_id'"));
			$email = ($assigned['user_id'] > 0 ? get_email($dbc, $assigned['user_id']) : '');
			if($email == '') {
				$email = $assigned['email_address'];
			}
			
			if($assigned['user_id'] > 0) {
				$body = "<p>This is a reminder that the {$assigned['name']} form has been assigned to you to complete. Please log in to the software and complete the form.</p>
					<p>Click <a href='".WEBSITE_URL."/Form Builder/formbuilder.php?tab=generate_form&id={$assigned['form_id']}'>here</a> to view the form.</p>";
			} else {
				$body = "<p>This is a reminder that you have been asked to complete a {$assigned['name']} form. You can access the form <a href='".WEBSITE_URL."/Form Builder/formbuilder.php?tab=external_form&id={$assigned['form_id']}'>here</a>.</p>";
			}
			$subject = 'Follow Up on Form: '.$assigned['name'];
			
			foreach(explode(',',$email) as $email) {
				try {
					send_email([$_POST['email_address'] => $_POST['email_sender']], trim($email), '', '', $subject, $body, '');
				} catch(Exception $e) {
					echo "<script> alert('Unable to send a reminder to ".trim($email).". Please check the email address, and try again later. If the problem persists, please contact Fresh Focus Media.'); </script>";
				}
			}
		}
	}

    echo '<script type="text/javascript"> window.location.replace("?tab=reporting"); </script>';
}

$staffid = '';
$form_id = '';
$status = 'Deadline Passed';
$start_date = '';
$end_date = date('Y-m-d');
if(!empty($_POST['staffid'])) {
	$staffid = $_POST['staffid'];
}
if(!empty($_POST['form_id'])) {
	$form_id = $_POST['form_id'];
}
if(!empty($_POST['status'])) {
	$status = $_POST['status'];
}
if(!empty($_POST['start_date'])) {
	$start_date = $_POST['start_date'];
}
if(!empty($_POST['end_date'])) {
	$end_date = $_POST['end_date'];
} ?>

<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
  <label for="ship_country" class="control-label">Search by User:</label>
</div>
  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
		<select data-placeholder="Select a User..." name="staffid" class="chosen-select-deselect form-control" width="380"><option></option>
			<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND `contactid` IN (SELECT `user_id` FROM `user_form_assign` WHERE `deleted`=0)"),MYSQLI_ASSOC));
			foreach($query as $id) { ?>
				<option <?= ($staffid == $id ? 'selected' : '') ?> value='<?= $id ?>' ><?= get_contact($dbc, $id) ?></option>
			<?php } ?>
			<option <?= ($staffid == 'OTHERS' ? 'selected' : '') ?> value="OTHERS">Completed by Other Individuals</option>
		</select>

  </div>

<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
  <label for="ship_country" class="control-label">Search by Form:</label>
</div>
  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
		<select data-placeholder="Select a Form" name="form_id" class="chosen-select-deselect form-control" width="380">
		  <option value=""></option>
		  <?php
			$query = mysqli_query($dbc,"SELECT `form_id`, `name` FROM `user_forms` WHERE deleted=0 ORDER BY `name`");
			while($row = mysqli_fetch_array($query)) { ?>
				<option <?= ($form_id == $row['form_id'] ? 'selected' : '') ?> value='<?php echo $row['form_id']; ?>' ><?php echo $row['name']; ?></option>
			<?php }
		  ?>
		</select>
  </div>

<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
  <label for="ship_zip" class="control-label">Search From Date:</label>
</div>
  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
		<input type="text" name="start_date" value="<?= $start_date ?>" class="form-control datepicker" onchange="submitForm()">
  </div>

<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
  <label for="ship_zip" class="control-label">Search To Date:</label>
</div>
  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
		<input type="text" name="end_date" value="<?= $end_date ?>" class="form-control datepicker" onchange="submitForm()">
  </div>

<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
  <label for="ship_zip" class="control-label">Search by Status:</label>
</div>
  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
		<select data-placeholder="Select a Status" name="status" class="chosen-select-deselect form-control" width="380">
		  <option value="ALL">Show All</option>
		  <option <?= ($status == 'Deadline Passed' ? 'selected' : '') ?> value="Deadline Passed">Deadline Passed</option>
		  <option <?= ($status == 'Deadline Today' ? 'selected' : '') ?> value="Deadline Today">Deadline Today</option>
		  <option <?= ($status == 'Completed' ? 'selected' : '') ?> value="Completed">Completed</option>
		</select>
  </div>

<div class="form-group pull-right double-gap-top triple-gap-bottom">
	<!--<button type="submit" name="search_submit" value="Submit" class="btn brand-btn mobile-block">Submit</button>-->
	<a href="" class="btn brand-btn mobile-block">Display All</a>
</div>

<div class="clearfix"></div>

<?php $search = '';
if(isset($_POST['search_submit'])) {
	if($staffid == 'OTHERS') {
		$search .= " AND assigned.`user_id`='0'";
	} else if($staffid != '') {
		$search .= " AND assigned.`user_id`='$staffid'";
	}
	if($form_id != '') {
		$search .= " AND assigned.`form_id`='$form_id'";
	}
	if($status == 'Deadline Passed') {
		$search .= " AND assigned.`due_date` < ".($end_date == '' || $end_date == '0000-00-00' ? "'".date('Y-m-d')."'" : "'$end_date'");
	} else if($status == 'Deadline Today') {
		$search .= " AND assigned.`due_date` = ".($end_date == '' || $end_date == '0000-00-00' ? "'".date('Y-m-d')."'" : "'$end_date'");
	} else if($status == 'Completed') {
		$search .= " AND assigned.`completed_date` IS NOT NULL AND assigned.`pdf_id` > 0";
	}
	if($end_date != '' && $end_date != '0000-00-00') {
		$search .= " AND (assigned.`due_date` <= '$end_date' OR assigned.`completed_date` <= '$end_date' OR assigned.`assign_date` <= '$end_date')";
	}
	if($start_date != '' && $start_date != '0000-00-00') {
		$search .= " AND (assigned.`due_date` <= '$start_date' OR assigned.`completed_date` <= '$start_date' OR assigned.`assign_date` <= '$start_date')";
	}
	$limiter = '';
} else {
	$rowsPerPage = 25;
	$pageNum = (empty($_GET['page']) ? 1 : $_GET['page']);
	$offset = ($pageNum - 1) * $rowsPerPage;
	$limiter = "LIMIT $offset, $rowsPerPage";
}

$result = mysqli_query($dbc, "SELECT * FROM `user_form_assign` assigned LEFT JOIN `user_forms` ON assigned.`form_id`=`user_forms`.`form_id` WHERE assigned.`deleted`=0 AND `user_forms`.deleted=0 $search $limiter");
$query_count = "SELECT COUNT(*) numrows FROM `user_form_assign` assigned LEFT JOIN `user_forms` ON assigned.`form_id`=`user_forms`.`form_id` WHERE assigned.`deleted`=0 AND `user_forms`.deleted=0 $search";

if(mysqli_num_rows($result) > 0) { ?>
	<span class="pull-right">
		<img src="<?php echo WEBSITE_URL;?>/img/block/red.png" width="23" height="23" border="0" alt=""> Deadline Passed
		<img src="<?php echo WEBSITE_URL;?>/img/block/green.png" width="23" height="23" border="0" alt=""> Deadline Today
		<button type='submit' name='send_follow_up_email' value='Submit' class='btn brand-btn hide-me pull-right'>Send Email Reminder</button>
	</span>
	<div class="form-group">
		<label class="col-sm-4">Email Sender's Name</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="email_sender" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Email Sender's Address</label>
		<div class="col-sm-8">
			<input type="text" class="form-control" name="email_address" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
		</div>
	</div>
	<table class='table table-bordered table-striped'>
        <thead>
            <tr class="hidden-xs hidden-sm">
                <th>Form Name</th>
                <th>User / Email Address</th>
                <th>Due Date</th>
                <th>Status</th>
                <th>Completed <button type="submit" name="send_follow_up_email" value="Submit" class="btn brand-btn">Send</button></th>
            </tr>
        </thead>
	<?php while($row = mysqli_fetch_array( $result ))
	{
		$deadline = $row['due_date'];
		$today = date('Y-m-d');
		$color = '';

		if(($today > $row['due_date']) && ($row['completed_date'] == '')) {
			$color = 'style="background-color: lightcoral;"';
		}
		if(($today == $row['due_date']) && ($row['completed_date'] == '')) {
			$color = 'style="background-color: lightgreen;"';
		}
		echo "<tr $color>";
		echo '<td data-title="Form Name">' . $row['name'] . '</td>';
		echo '<td data-title="User / Email Address">' . ($row['user_id'] > 0 ? get_contact($dbc, $row['user_id']) : $row['email_address']) . '</td>';
		echo '<td data-title="Due Date">' . date('Y-m-d', strtotime($row['due_date'])) . '</td>';

		echo '<td data-title="Status">';
		if(($today > $row['due_date']) && ($row['completed_date'] == '')) {
			echo '<img src="'.WEBSITE_URL.'/img/block/red.png" width="22" height="22" border="0" alt=""> '.date('Y-m-d', strtotime($row['due_date']));
		} else if(($today == $row['due_date']) && ($row['completed_date'] == '')) {
			echo '<img src="'.WEBSITE_URL.'/img/block/green.png" width="22" height="22" border="0" alt=""> '.date('Y-m-d', strtotime($row['due_date']));
		} else if($row['completed_date'] != '') {
			echo '<img src="'.WEBSITE_URL.'/img/checkmark.png" width="22" height="22" border="0" alt="">';
		}
		echo '</td>';

		echo '<td data-title="Completed">';
		if($row['completed_date'] != '') {
			$completed = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_pdf` WHERE `pdf_id`='".$row['pdf_id']."'"));
			if($completed['generated_file'] != '' && file_exists('download/'.$completed['generated_file'])) {
				echo '<a href="download/'.$completed['generated_file'].'">'.$row['completed_date'].' <img src="'.WEBSITE_URL.'/img/pdf.png"></a><br />';
			}
			if($completed['scanned_file'] != '' && file_exists('download/'.$completed['scanned_file'])) {
				echo '<a href="download/'.$completed['scanned_file'].'">Scanned Upload</a>';
			}
		} else {
			echo '<a href="?tab=generate_form&id='.$row['form_id'].'&assign_id='.$row['assign_id'].'">Complete Now</a> | ';
			if($row['email_address'] != '' || get_email($dbc, $row['user_id']) != '') {
				echo '<a href="?tab=reporting&assign_id='.$row['assign_id'].'&action=send_followup_email">Send</a>';
				echo ' <input name="check_send_email[]" type="checkbox" value="'.$row['assign_id'].'" class="form-control check_send_email" style="width:25px;"/>';
			}
		}
		echo '</td>';

		echo "</tr>";
	} ?>
	</table>
<?php } else {
	echo "<h2>No Record Found.</h2>";
} ?>
</form>
<script>
    function submitForm(thisForm) {
        if (!$('input[name="search_submit"]').length) {
            var input = $("<input>")
                        .attr("type", "hidden")
                        .attr("name", "search_submit").val("1");
            $('[name=form_sites]').append($(input));
        }

        $('[name=form_sites]').submit();
    }
</script>