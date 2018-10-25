<?php if(!empty($_POST['send_follow_up_email'])) {
	foreach($_POST['send_follow_up_email'] as $send_email) {
		$send_email = explode('.',$send_email);
		if($send_email[1] > 0 && $send_email[2] > 0) {
			if($send_email[0] == 'manual') {
				$assign = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `manuals_staff` WHERE `manualstaffid`='".$send_email[1]."'"));
				$staff = $send_email[2];
				$to = get_email($dbc, $staff);
				if($to != '') {
					$manual = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `manuals` WHERE `manualtypeid`='".$assign['manualtypeid']."'"));
					$heading = $third_heading != '' ? $third_heading_number.' '.$third_heading : ($sub_heading != '' ? $sub_heading_number.' '.$sub_heading : $heading_number.' '.$heading);
					$subject = str_replace(['[CATEGORY]','[HEADING]'],[$category,$heading],$manual['email_subject']);
					$body = html_entity_decode(str_replace(['[CATEGORY]','[HEADING]'],[$category,$heading],$manual['email_message']).'<p>Click <a href="'.WEBSITE_URL.'/HR/index.php?manual='.$assign['manualtypeid'].'">here</a> to review the manual.</p>');
					try {
						send_email('', $to, '', '', $subject, $body, '');
					} catch(Exception $e) { }
				}
			} else if($send_email[0] == 'hr') {
				$assign = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `hr_attendance` WHERE `hrattid`='".$send_email[1]."'"));
				$staff = $send_email[2];
				$to = get_email($dbc, $staff);
				if($to != '') {
					$form = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `hr` WHERE `hrid`='".$assign['hrid']."'"));
					$heading = $third_heading != '' ? $third_heading_number.' '.$third_heading : ($sub_heading != '' ? $sub_heading_number.' '.$sub_heading : $heading_number.' '.$heading);
					$subject = str_replace(['[CATEGORY]','[HEADING]'],[$category,$heading],$form['email_subject']);
					$body = html_entity_decode(str_replace(['[CATEGORY]','[HEADING]'],[$category,$heading],$form['email_message']).'<p>Click <a href="'.WEBSITE_URL.'/HR/index.php?hr='.$assign['hrid'].'">here</a> to complete the form.</p>');
					try {
						send_email('', $to, '', '', $subject, $body, '');
					} catch(Exception $e) { }
				}
			}
		}
	}
} ?>
<div class='scale-to-fill has-main-screen hide-titles-mob'>
	<div class='main-screen' style="padding-top:1em;"><?php
        $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='hr_reporting'"));
        $note = $notes['note'];
            
        if ( !empty($note) ) { ?>
            <div class="notice double-gap-bottom popover-examples">
                <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                <div class="col-sm-11">
                    <span class="notice-name">NOTE:</span>
                    <?= $note; ?>
                </div>
                <div class="clearfix"></div>
            </div><?php
        } ?>

		<form name="form_sites" method="post" action="" class="form-horizontal" role="form">
        <?php $contactid = '';
        $category = '';
        $heading = '';
        $status = '';
        $s_start_date = '';
        $s_end_date = '';

        if(!empty($_POST['contactid'])) {
            $contactid = $_POST['contactid'];
        }
        if(!empty($_POST['category'])) {
            $category = $_POST['category'];
        }
        if(!empty($_POST['heading'])) {
            $heading = $_POST['heading'];
        }
        if(!empty($_POST['status'])) {
            $status = $_POST['status'];
        }
        if(!empty($_POST['s_start_date'])) {
            $s_start_date = $_POST['s_start_date'];
        }
        if(!empty($_POST['s_end_date'])) {
            $s_end_date = $_POST['s_end_date'];
        }
        if (isset($_POST['display_all_asset'])) {
            $contactid = '';
            $category = '';
            $heading = '';
            $status = '';
            $s_start_date = '';
            $s_end_date = '';
        }
        ?>
		<div class="form-group col-sm-6 col-xs-12">
			<label class="col-sm-4">Staff:</label>
			<div class="col-sm-8">
				<select data-placeholder="Select a Staff Member..." name="contactid" class="chosen-select-deselect form-control" width="380">
				  <option value=""></option>
				  <?php $query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE category= 'Staff' AND deleted=0 AND status > 0"));
					foreach($query as $staff) {
						echo "<option ".($contactid == $staff['contactid'] ? 'selected' : '')." value='". $staff['contactid']."'>".$staff['first_name'].' '.$staff['last_name'].'</option>';
					} ?>
				</select>
			</div>
		</div>

		<div class="form-group col-sm-6 col-xs-12">
			<label class="col-sm-4 col-xs-4">Category:</label>
			  <div class="col-sm-8 col-xs-8">
					<select data-placeholder="Select a Category" name="category" class="chosen-select-deselect form-control" width="380">
					  <option value=""></option>
					  <?php foreach(explode(',',get_config($dbc, 'hr_tabs')) as $cat) { ?>
						  <option <?= $cat == $category ? 'selected' : '' ?> value='<?= $cat ?>' ><?= $cat ?></option>
					  <?php } ?>
					</select>
			  </div>
		</div>

		<div class="form-group col-sm-6 col-xs-12">
			<label class="col-sm-4 col-xs-4">Heading:</label>
			  <div class="col-sm-8 col-xs-8">
					<select data-placeholder="Select a Heading..." name="heading" class="chosen-select-deselect form-control" width="380">
					  <option value=""></option>
					  <?php
						$query = mysqli_query($dbc,"SELECT DISTINCT(`heading`) FROM (SELECT `heading` FROM hr WHERE deleted=0 UNION SELECT `heading` FROM `manuals` WHERE `deleted`=0) `heading` ORDER BY `heading`");
						while($row = mysqli_fetch_array($query)) { ?>
							<option <?= $row['heading'] == $heading ? 'selected' : '' ?> value='<?php echo $row['heading']; ?>' ><?php echo $row['heading']; ?></option>
						<?php }
					  ?>
					</select>
			  </div>
		</div>

		<div class="form-group col-sm-6 col-xs-12">
			<label class="col-sm-4 col-xs-4">Status:</label>
			  <div class="col-sm-8 col-xs-8">
					<select data-placeholder="Select a Status..." name="status" class="chosen-select-deselect form-control" width="380">
					  <option value=""></option>
					  <option <?php if ($status=='Deadline Past') echo 'selected="selected"';?> value="Past">Deadline Passed</option>
					  <option <?php if ($status=='Deadline Today') echo 'selected="selected"';?> value="Today">Deadline Today</option>
					</select>
			  </div>
		</div>

		<div class="form-group col-sm-6 col-xs-12">
			<label class="col-sm-4 col-xs-4">Start Date:</label>
			<div class="col-sm-8 col-xs-8">
				<input name="s_start_date" type="text" class="datepicker form-control" value="<?php echo $s_start_date; ?>" style="width:100%;">
			</div>
		</div>

		<div class="form-group col-sm-6 col-xs-12">
			<label class="col-sm-4 col-xs-4">End Date:</label>
			<div class="col-sm-8 col-xs-8">
				<input name="s_end_date" type="text" class="datepicker form-control" value="<?php echo $s_end_date; ?>" style="width:100%;">
			</div>
		</div>

        <div class="form-group pull-right">
			<button type="submit" name="reporting_client" value="Submit" class="btn brand-btn mobile-block">Submit</button>
			<button type="submit" name="display_all_asset" value="Display All" class="btn brand-btn mobile-block">Display All</button>
        </div>
		<div class="clearfix"></div>
		
		<span class="pull-right gap-top">
            <img src="<?php echo WEBSITE_URL;?>/img/block/red.png" width="23" height="23" border="0" alt=""> Deadline Passed
            <img src="<?php echo WEBSITE_URL;?>/img/block/green.png" width="23" height="23" border="0" alt=""> Deadline Today
        </span>
		
		<?php $sql = "SELECT * FROM (SELECT 'manual' `type`, `manuals`.`manualtypeid` `id`, 0 `user_form_id`, `manualstaffid` `entry`, `category`, CONCAT(`heading_number`,' ',`heading`) `heading`, CONCAT(`sub_heading_number`,' ',`sub_heading`) `sub_heading`, CONCAT(`third_heading_number`,' ',`third_heading`) `third_heading`, `manuals_staff`.`staffid`, `deadline`, `done`, `today_date`, 'manual' `formtype` FROM `manuals_staff` LEFT JOIN `manuals` ON `manuals_staff`.`manualtypeid`=`manuals`.`manualtypeid` UNION SELECT 'hr' `type`, `hr`.`hrid` `id`, `hr`.`user_form_id`, `fieldlevelriskid` `entry`, `category`, CONCAT(`heading_number`,' ',`heading`) `heading`, CONCAT(`sub_heading_number`,' ',`sub_heading`) `sub_heading`, CONCAT(`third_heading_number`,' ',`third_heading`) `third_heading`, `hr_attendance`.`assign_staffid` `staffid`, `deadline`, `done`, '' `today_date`, `hr`.`form` `formtype` FROM `hr_attendance` LEFT JOIN `hr` ON `hr_attendance`.`hrid`=`hr`.`hrid`) `hr_list` WHERE `category` != ''";
		if($contactid > 0) {
			$sql .= " AND `staff`='$contactid'";
		}
		if($category != '') {
			$sql .= " AND `category`='$category'";
		}
		if($heading != '') {
			$sql .= " AND `heading` LIKE '%$heading'";
		}
		if($status == 'Past') {
			$sql .= " AND `deadline` < DATE(NOW())";
		}
		if($status == 'Today') {
			$sql .= " AND `deadline`=DATE(NOW())";
		}
		if($s_start_date != '') {
			$sql .= " AND `deadline` >= '$s_start_date'";
		}
		if($s_end_date != '') {
			$sql .= " AND `deadline` <= '$s_end_date'";
		}
        $pageNum = $_GET['page'] > 0 ? filter_var($_GET['page'],FILTER_SANITIZE_STRING) : 1;
        $rowsPerPage = $_GET['pagerows'] > 0 ? filter_var($_GET['pagerows'],FILTER_SANITIZE_STRING) : 25;
		$query = mysqli_query($dbc, $sql." LIMIT ".(($pageNum - 1) * $rowsPerPage).", ".$rowsPerPage);
		$today = date('Y-m-d');
        $rows_sql = str_replace("SELECT * FROM", "SELECT COUNT(*) `numrows` FROM", $sql);
        display_pagination($dbc, $rows_sql, $pageNum, $rowsPerPage, true, 25);
		if(mysqli_num_rows($query) > 0) { ?>
			<div id='no-more-tables' class="col-sm-12">
				<table class='table table-bordered'>
					<tr class="hidden-xs hidden-sm">
						<th>Staff</th>
						<th>Email</th>
						<th>Category</th>
						<th>Heading</th>
						<th>Sub Section</th>
						<th>Deadline</th>
						<th>Status</th>
						<th>Reminder<button type="submit" name="submit" value="Submit" class="btn brand-btn">Send</button></th>
					</tr>
					<?php while($row = mysqli_fetch_assoc($query)) { ?>
						<tr>
							<td data-title="Staff"><?= get_contact($dbc, $row['staffid']) ?></td>
							<td data-title="Email"><?= get_email($dbc, $row['staffid']) ?></td>
							<td data-title="Category"><?= $row['category'] ?></td>
							<td data-title="Heading"><?= $row['heading'] ?></td>
							<td data-title="Sub Section"><?= $row['sub_heading'] ?></td>
							<td data-title="Deadline"><?= $row['deadline'] ?></td>
							<td data-title="Status"><?= $row['done'] > 0 ? '<img src="../img/checkmark.png" class="inline-img">' : ($row['deadline'] >= $today ? '<img src="../img/block/green.png" class="inline-img">' : '<img src="../img/block/red.png" class="inline-img">') ?></td>
							<td data-title="Reminder"><?= $row['done'] == 0 ? '<a href="../quick_action_email.php?tile=hr&type='.$row['type'].'&id='.$row['id'].'&staff='.$row['staffid'].'" onclick="overlayIFrameSlider(this.href, \'auto\', true, true); return false;"><img src="../img/icons/ROOK-email-icon.png" class="inline-img cursor-hand no-toggle gap-bottom" title="Send Email Now"></a> <label class="form-checkbox no-pad any-width"><input type="checkbox" name="send_follow_up_email[]" value="'.$row['type'].'.'.$row['id'].'.'.$row['staffid'].'"> Send</label>' : '<a href="'.($row['formtype'] == 'manual' ? 'download/'.config_safe_str(trim(trim($row['third_heading']) == '' ? (trim($row['sub_heading']) == '' ? $row['heading'] : substr_replace($row['sub_heading'],'',strpos($row['sub_heading'],' '),1)) : $row['third_heading']).'_'.str_replace('-','_',$row['today_date'])).'.pdf' : hr_link($dbc, $row['formtype'], $row['user_form_id'], $row['entry'])).'">'.($row['today_date'] != '' ? $row['today_date'] : 'Complete').'<img class="inline-img" src="../img/pdf.png"></a>' ?></td>
						</tr>
					<?php } ?>
				</table>
			</div>
            <?php display_pagination($dbc, $rows_sql, $pageNum, $rowsPerPage, true, 25);
        } else {
			echo "<h3>No Results Found</h3>";
		} ?>
		</form>
	</div>
</div><?php
function hr_link($dbc, $form, $user_form_id, $fieldlevelriskid) {
	if($user_form_id > 0) {
        $user_pdf = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_pdf` WHERE `pdf_id` = '$fieldlevelriskid'"));
        $pdf_path = 'download/'.$user_pdf['generated_file'];
        return $pdf_path;
    } else {
        if($form == 'AVS Near Miss') {
            $pdf_path = 'avs_near_miss/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employee Information Form') {
            $pdf_path = 'employee_information_form/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employee Driver Information Form') {
            $pdf_path = 'employee_driver_information_form/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Time Off Request') {
            $pdf_path = 'time_off_request/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Confidential Information') {
            $pdf_path = 'confidential_information/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Work Hours Policy') {
            $pdf_path = 'work_hours_policy/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Direct Deposit Information') {
            $pdf_path = 'direct_deposit_information/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employee Substance Abuse Policy') {
            $pdf_path = 'employee_substance_abuse_policy/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employee Right to Refuse Unsafe Work') {
            $pdf_path = 'employee_right_to_refuse_unsafe_work/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Shop Yard and Office Orientation') {
            $pdf_path = 'employee_shop_yard_office_orientation/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == "Copy of Drivers Licence and Safety Tickets") {
            $pdf_path = 'copy_of_drivers_licence_safety_tickets/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'PPE Requirements') {
            $pdf_path = 'ppe_requirements/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Verbal Training in Emergency Response Plan') {
            $pdf_path = 'verbal_training_in_emergency_response_plan/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Eligibility for General Holidays and General Holiday Pay') {
            $pdf_path = 'eligibility_for_general_holidays_general_holiday_pay/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Maternity Leave and Parental Leave') {
            $pdf_path = 'maternity_leave_parental_leave/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employment Verification Letter') {
            $pdf_path = 'employment_verification_letter/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Background Check Authorization') {
            $pdf_path = 'background_check_authorization/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Disclosure of Outside Clients') {
            $pdf_path = 'disclosure_of_outside_clients/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employment Agreement') {
            $pdf_path = 'employment_agreement/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Independent Contractor Agreement') {
            $pdf_path = 'independent_contractor_agreement/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Letter of Offer') {
            $pdf_path = 'letter_of_offer/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employee Non-Disclosure Agreement') {
            $pdf_path = 'employee_nondisclosure_agreement/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employee Self Evaluation') {
            $pdf_path = 'employee_self_evaluation/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'HR Complaint') {
            $pdf_path = 'hr_complaint/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Exit Interview') {
            $pdf_path = 'exit_interview/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employee Expense Reimbursement') {
            $pdf_path = 'employee_expense_reimbursement/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Absence Report') {
            $pdf_path = 'absence_report/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employee Accident Report Form') {
            $pdf_path = 'employee_accident_report_form/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Trucking Information') {
            $pdf_path = 'trucking_information/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Contractor Orientation') {
            $pdf_path = 'contractor_orientation/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Contract Welder Inspection Checklist') {
            $pdf_path = 'avs_near_miss/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Contractor Pay Agreement') {
            $pdf_path = 'avs_near_miss/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employee Holiday Request Form') {
            $pdf_path = 'avs_near_miss/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
        if($form == 'Employee Coaching Form') {
            $pdf_path = 'avs_near_miss/download/hr_'.$fieldlevelriskid.'.pdf';
            return $pdf_path;
        }
    }
} ?>