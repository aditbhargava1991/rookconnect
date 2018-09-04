<?php include_once ('../include.php');
ob_clean(); ?>
<script>initInputs();</script>
<?php
$strict_view = strictview_visible_function($dbc, 'ticket');
$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
$ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid`='$ticketid'"));
$tile_security = get_security($dbc, $_GET['tile'] == '' ? 'ticket' : 'ticket_type_'.$_GET['tile']);
$ticket_status_list = explode(',',get_config($dbc, 'ticket_status'));
$quick_actions = explode(',',get_config($dbc, 'quick_action_icons'));
$db_config = get_field_config($dbc, 'tickets_dashboard');
if($db_config == '') {
	$db_config = 'Business,Contact,Heading,Services,Status,Deliverable Date';
}
$db_config = explode(',',$db_config);
$flag_label = '';
if($ticket['flag_colour'] != '' && $ticket['flag_colour'] != 'FFFFFF') {
	if(in_array('flag_manual',$quick_actions)) {
		if(time() < strtotime($ticket['flag_start']) || time() > strtotime($ticket['flag_end'].' + 1 day')) {
			$ticket['flag_colour'] = '';
		} else {
			$flag_label = html_entity_decode($dbc->query("SELECT `comment` FROM `ticket_comment` WHERE `deleted`=0 AND `ticketid`='$ticketid' AND `type`='flag_comment' ORDER BY `ticketcommid` DESC")->fetch_assoc()['comment']);
		}
	} else {
		$ticket_flag_names = [''=>''];
		$flag_names = explode('#*#', get_config($dbc, 'ticket_colour_flag_names'));
		foreach(explode(',',get_config($dbc, 'ticket_colour_flags')) as $i => $colour) {
			$ticket_flag_names[$colour] = $flag_names[$i];
		}
		$flag_label = $ticket_flag_names[$ticket['flag_colour']];
	}
} ?>
<div class="dashboard-item" data-id="<?= $ticketid ?>" data-colour="<?= $ticket['flag_colour'] ?>" data-table="tickets" data-id-field="ticketid" style="<?= $ticket['flag_colour'] != '' ? 'background-color: #'.$ticket['flag_colour'].';' : '' ?>">
	<span class="flag-label"><?= $flag_label ?></span>
	<?php if(in_array('Extra Billing',$db_config)) {
		$extra_billing = $dbc->query("SELECT COUNT(*) `num` FROM `ticket_comment` WHERE `ticketid` = '$ticketid' AND '$ticketid' > 0 AND `type` = 'service_extra_billing' AND `deleted` = 0 ORDER BY `ticketcommid` DESC")->fetch_assoc();
	} else {
		$extra_billing['num'] = 0;
	}
	if(in_array('Total Budget Time',$db_config) && $ticket['total_budget_time'] != '00:00:00' && !empty($ticket['total_budget_time'])) {
		$total_budget_time = time_time2decimal($ticket['total_budget_time']);
		$total_staff_time = 0;
		$staff_hours = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table` LIKE '%Staff%' AND `deleted` = 0 AND `ticketid` = '".$ticket['ticketid']."'");
		while($staff_hour = mysqli_fetch_assoc($staff_hours)) {
			if($staff_hour['hours_tracked'] > 0) {
				$total_staff_time += $staff_hour['hours_tracked'];
			} else if(!empty($staff_hour['checked_out']) && !empty($staff_hour['checked_in'])) {
				$total_staff_time += (time_time2decimal($staff_hour['checked_out']) - time_time2decimal($staff_hour['checked_in']));
			}
		}
		$total_budget_time_exceeded = number_format($total_staff_time - $total_budget_time,2);
	} else {
		$total_budget_time_exceeded = 0;
	}
	?>
	<h3 <?= $extra_billing['num'] > 0 ? 'style="color:red;"' : '' ?>><?= $tile_security['edit'] > 0 || in_array('Hide Slider',$db_config) ? '<a href=\'../Ticket/index.php?tile_name='.$_GET['tile'].'&edit='.$ticket['ticketid'].'&from='.urlencode($_GET['from']).'\' '.($extra_billing['num'] > 0 ? 'style="color:red;"' : '').'>' : '' ?>
		<?= !in_array('Label',$db_config) ? TICKET_NOUN.' #'.($ticket['main_ticketid'] > 0 ? $ticket['main_ticketid'].' '.$ticket['sub_ticket'] : $ticket['ticketid']) : get_ticket_label($dbc, $ticket) ?>
        <?php
				foreach(array_filter(explode(',',$ticket['clientid'])) as $clientid) {
					$all_client = get_contact($dbc, $clientid);
				}
        ?>
		<?= in_array('Client As Label',$db_config) ? ' - '.$all_client : '' ?>
		<?= in_array('Created Date As Label',$db_config) ? ' - '.substr($ticket['created_date'], 0, 10) : '' ?>
		<?= in_array('Status As Label',$db_config) ? ' - Finished' : '' ?>

		<?= $extra_billing['num'] > 0 ? '<img class="inline-img small no-toggle" title="Extra Billing" src="../img/icons/ROOK-status-paid.png">' : '' ?>
		<img class="inline-img small no-toggle total_budget_time_icon" title="Total Budget Time exceeded by <?= $total_budget_time_exceeded ?> hours." src="../img/icons/ROOK-status-paid.png" style="filter: invert(30%) sepia(94%) saturate(50000%) hue-rotate(356deg) brightness(103%) contrast(117%); <?= $total_budget_time_exceeded > 0 ? '' : 'display:none;' ?>">
		<?= $tile_security['edit'] > 0 ? '</a>' : '' ?><?= !in_array('Hide Slider',$db_config) ? '<a href="../Ticket/index.php?tile_name='.$_GET['tile'].'&edit='.$ticket['ticketid'].'&action_mode=1&from='.urlencode($_GET['from']).'" '.(!in_array('Action Mode Button Eyeball',$db_config) ? 'class="btn brand-btn"' : '').' onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\',\'auto\',false,true); return false;">'.(in_array('Action Mode Button Eyeball',$db_config) ? '<img src="../img/icons/eyeball.png" class="inline-img">' : (!empty(get_config($dbc, 'ticket_slider_button')) ? get_config($dbc, 'ticket_slider_button') : 'Sign In')).'</a>' : '' ?>
		<?= $tile_security['edit'] > 0 ? '</a>' : '' ?><?= in_array('Overview Icon',$db_config) ? '<a href="../Ticket/index.php?tile_name='.$_GET['tile'].'&edit='.$ticket['ticketid'].'&overview_mode=1&from='.urlencode($_GET['from']).'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\',\'auto\',false,true); return false;"><img src="../img/icons/eyeball.png" class="inline-img no-toggle" title="'.TICKET_NOUN.' Overview"></a>' : '' ?></h3>
	<!-- Quick Action inputs -->

	<?php if($tile_security['edit'] > 0) { ?>
		<div class="action-icons">
			<?php echo (in_array('flag_manual',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img manual-flag-icon" title="Flag This!">' : '');
			echo (!in_array('flag_manual',$quick_actions) && in_array('flag',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" class="inline-img flag-icon" title="Flag This!">' : '');
			echo (in_array('alert',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-alert-icon.png" class="inline-img alert-icon" title="Activate Alerts &amp; Get Notified">' : '');
			echo (in_array('email',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" class="inline-img email-icon" title="Send Email">' : '');
			echo (in_array('reminder',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reminder-icon.png" class="inline-img reminder-icon" title="Schedule Reminder">' : '');
			echo (in_array('attach',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" class="inline-img attach-icon" title="Attach File">' : '');
			echo (in_array('reply',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" class="inline-img reply-icon" title="Add Note">' : '');
			echo (in_array('archive',$quick_actions) && $tile_security['edit'] > 0 ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-trash-icon.png" class="inline-img archive-icon" title="Archive">' : '');

			//echo (in_array('reply',$quick_actions) ? '<a href="../Ticket/ticket_pdf.php?action=notopen&ticketid='.$ticket['ticketid'].'"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" class="inline-img emailpdf-icon" title="Add Note"></a>' : '');

            //echo (in_array('reply',$quick_actions) ? '<img src="'.WEBSITE_URL.'/img/icons/ROOK-email-icon.png" class="inline-img emailpdf-icon" title="Email PDF">' : '');

			$status_icon = get_ticket_status_icon($dbc, $ticket['status']);
			if(!empty($status_icon)) {
	            if($status_icon == 'initials') {
	                echo '<span class="id-circle-large pull-right" style="background-color: #6DCFF6; font-family: \'Open Sans\';">'.get_initials($ticket['status']).'</span>';
	            } else {
	                echo '<img src="'.$status_icon.'" class="pull-right" style="max-height: 30px;">';
	            }
			} ?>
		</div><br />
	<?php } ?>

	<?php if(in_array('flag_manual',$quick_actions)) {
		$colours = explode(',', get_config($dbc, "ticket_colour_flags")); ?>
		<span class="col-sm-3 text-center flag_field_labels" style="display:none;">Label</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">Colour</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">Start Date</span><span class="col-sm-3 text-center flag_field_labels" style="display:none;">End Date</span>
		<div class="col-sm-3"><input type='text' name='label' value='<?= $flag_label ?>' class="form-control" style="display:none;"></div>
		<div class="col-sm-3"><select name='colour' class="form-control" style="display:none;background-color:#<?= $ticket['flag_colour'] ?>;font-weight:bold;" onchange="$(this).css('background-color','#'+$(this).find('option:selected').val());">
				<option value="FFFFFF" style="background-color:#FFFFFF;">No Flag</option>
				<?php foreach($colours as $flag_colour) { ?>
					<option <?= $ticket['flag_colour'] == $flag_colour ? 'selected' : '' ?> value="<?= $flag_colour ?>" style="background-color:#<?= $flag_colour ?>;"></option>
				<?php } ?>
			</select></div>
		<div class="col-sm-3"><input type='text' name='flag_start' value='<?= $ticket['flag_start'] ?>' class="form-control datepicker" style="display:none;"></div>
		<div class="col-sm-3"><input type='text' name='flag_end' value='<?= $ticket['flag_end'] ?>' class="form-control datepicker" style="display:none;"></div>
		<button class="btn brand-btn pull-right" name="flag_it" onclick="return false;" style="display:none;">Flag This</button>
		<button class="btn brand-btn pull-right" name="flag_cancel" onclick="return false;" style="display:none;">Cancel</button>
		<button class="btn brand-btn pull-right" name="flag_off" onclick="return false;" style="display:none;">Remove Flag</button>
	<?php } ?>
	<input type='text' name='reply' value='' class="form-control" style="display:none;">
	<input type='text' name='emailpdf' value='' class="form-control" style="display:none;">
	<input type='text' name='reminder' value='' class="form-control datepicker" style="border:0;height:0;margin:0;padding:0;width:0;">
	<div class="select_users" style="display:none;">
		<select data-placeholder="Select Staff" multiple class="chosen-select-deselect"><option></option>
		<?php foreach($staff_list as $staff) { ?>
			<option value="<?= $staff['contactid'] ?>"><?= $staff['first_name'].' '.$staff['last_name'] ?></option>
		<?php } ?>
		</select>
		<button class="submit_button btn brand-btn pull-right">Submit</button>
		<button class="cancel_button btn brand-btn pull-right">Cancel</button>
	</div>
	<input type='file' name='document' value='' data-table="<?= $doc_table ?>" data-folder="<?= $doc_folder ?>" style="display:none;">
	<div class="clearfix"></div>
	<?php if(in_array('Project',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">'.PROJECT_NOUN.' Information:</label>
			<div class="col-sm-8">';
				if($ticket['projectid'] > 0) {
					echo get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='".$ticket['projectid']."'")));
				} else {
					echo 'No '.PROJECT_NOUN;
				}
			echo '</div>
		</div>';
	}
	if(in_array('Business',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">'.BUSINESS_CAT.':</label>
			<div class="col-sm-8">
				'.get_client($dbc, $ticket['businessid']).'
			</div>
		</div>';
	}
	if(in_array('Contact',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Contact:</label>
			<div class="col-sm-8">';
				foreach(array_filter(explode(',',$ticket['clientid'])) as $clientid) {
					echo get_contact($dbc, $clientid).'<br />';
				}
			echo '</div>
		</div>';
	}
	if(in_array('Services',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Services:</label>
			<div class="col-sm-8">';
			foreach(array_filter(explode(',',$ticket['serviceid'])) as $service) {
				$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `category`, `heading` FROM `services` WHERE `serviceid`='$service'"));
				echo ($service['category'] == '' ? '' : $service['category'].': ').$service['heading'].'<br />';
			}
			echo '</div>
		</div>';
	}
	if(in_array('Heading',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">'.TICKET_NOUN.' Heading:</label>
			<div class="col-sm-8">
				'.$ticket['heading'].'
			</div>
		</div>';
	}
	if(in_array('Staff',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Staff:</label>
			<div class="col-sm-8">';
			foreach(array_filter(explode(',',$ticket['contactid'])) as $staff) {
				echo '<a href="../Staff/staff_edit.php?contactid='.$staff.'">'.get_contact($dbc, $staff).'</a><br />';
			}
			foreach(array_filter(explode(',',$ticket['internal_qa_contactid'])) as $staff) {
				echo '<a href="../Staff/staff_edit.php?contactid='.$staff.'">'.get_contact($dbc, $staff).'</a> (Internal QA)<br />';
			}
			foreach(array_filter(explode(',',$ticket['deliverable_contactid'])) as $staff) {
				echo '<a href="../Staff/staff_edit.php?contactid='.$staff.'">'.get_contact($dbc, $staff).'</a> (Deliverable)<br />';
			}
			echo '</div>
		</div>';
	}
	if(in_array('Members',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Members:</label>
			<div class="col-sm-8">';
			$member_list = mysqli_query($dbc, "SELECT `item_id` FROM `ticket_attached` WHERE `src_table`='members' AND `ticketid`='{$ticket['ticketid']}' AND `deleted`=0");
			while($member = mysqli_fetch_assoc($member_list)['item_id']) {
				echo '<a href="../Members/contact_inbox.php?edit='.$member.'">'.get_contact($dbc, $member).'</a><br />';
			}
			echo '</div>
		</div>';
	}
	if(in_array('Clients',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Clients:</label>
			<div class="col-sm-8">';
			$member_list = mysqli_query($dbc, "SELECT `item_id` FROM `ticket_attached` WHERE `src_table`='clients' AND `ticketid`='{$ticket['ticketid']}' AND `deleted`=0");
			while($member = mysqli_fetch_assoc($member_list)['item_id']) {
				echo '<a href="../Members/contact_inbox.php?edit='.$member.'">'.get_contact($dbc, $member).'</a><br />';
			}
			echo '</div>
		</div>';
	}
	if(in_array('Create Date',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Date Created:</label>
			<div class="col-sm-8">';
				echo $ticket['created_date'];
			echo '</div>
		</div>';
	}
	if(in_array('Ticket Date',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">'.TICKET_NOUN.' Date:</label>
			<div class="col-sm-8">';
				$dates = mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE IFNULL(`to_do_date`,'0000-00-00')!='0000-00-00' AND `ticketid`='".$ticket['ticketid']."'");
				if($dates->num_rows > 0) {
					while($date_row = $dates->fetch_assoc()) {
						switch($date_row['type']) {
							case 'origin': echo 'Shipment Date: '; break;
							case 'destination': echo 'Delivery Date: '; break;
							case '': break;
							default: echo $date_row['type'].': '; break;
						}
						echo $date_row['to_do_date']."<br />\n";
					}
				} else {
					echo $ticket['to_do_date'];
				}
			echo '</div>
		</div>';
	}
	if(in_array('Deliverable Date',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">To Do Date:</label>
			<div class="col-sm-8">';
				echo ($ticket['to_do_date'] == '' ? '' : $ticket['to_do_date'].'<br />');
				foreach(array_filter(explode(',', $ticket['contactid'])) as $staff) {
					echo get_contact($dbc, $staff).'<br />';
				}
				echo '('.$ticket['max_time'].')';
			echo '</div>
		</div>';
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Internal QA Date:</label>
			<div class="col-sm-8">';
				echo ($ticket['internal_qa_date'] == '' ? '' : $ticket['internal_qa_date'].'<br />');
				foreach(array_filter(explode(',', $ticket['internal_qa_contactid'])) as $staff) {
					echo get_contact($dbc, $staff).'<br />';
				}
				echo '('.$ticket['max_qa_time'].')';
			echo '</div>
		</div>';
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Deliverable Date:</label>
			<div class="col-sm-8">';
				echo ($ticket['deliverable_date'] == '' ? '' : $ticket['deliverable_date'].'<br />');
				foreach(array_filter(explode(',', $ticket['deliverable_contactid'])) as $staff) {
					echo get_contact($dbc, $staff).'<br />';
				}
			echo '</div>
		</div>';
	}
	if(in_array('Documents',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Documents:</label>
			<div class="col-sm-8">';
				$documents = mysqli_query($dbc, "SELECT CONCAT('".TICKET_NOUN.": ',IFNULL(CONCAT(NULLIF(NULLIF(`type`,'Link'),''),': '),''),IFNULL(NULLIF(`label`,''),`document`)) `label`, CONCAT('download/',`document`) `link` FROM `ticket_document` WHERE `ticketid`='".$ticket['ticketid']."' AND `deleted`=0 AND IFNULL(`document`,'') != '' UNION
					SELECT CONCAT('".PROJECT_NOUN.": ',IFNULL(CONCAT(NULLIF(NULLIF(`category`,''),'undefined'),': '),''),IFNULL(NULLIF(`label`,''),`upload`)) `label`, CONCAT('../Project/download/',`upload`) `link` FROM `project_document` WHERE `projectid`='".$ticket['projectid']."' AND `deleted`=0 AND IFNULL(`upload`,'') != ''");
				while($document = $documents->fetch_assoc()) {
					echo '<a href="'.$document['link'].'">'.$document['label']."</a><br />\n";
				}
			echo '</div>
		</div>';
	}
	if(in_array('Purchase Order',$db_config)) {
		$line_po = explode('#*#',$ticket['purchase_order']);
		$line_po_list = $dbc->query("SELECT `po_num` FROM `ticket_attached` WHERE `deleted`=0 AND IFNULL(`po_num`,'') != '' AND `ticketid`='$ticketid' AND `src_table`='inventory' GROUP BY `po_num` ORDER BY LPAD(`po_num`,20,0)");
		while($line_po_num = $line_po_list->fetch_assoc()) {
			$line_po[] = $line_po_num['po_num'];
		}
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Purchase Order #:</label>
			<div class="col-sm-8">
				'.implode('<br />',array_filter(array_unique($line_po))).'
			</div>
		</div>';
	}
	if(in_array('Customer Order',$db_config)) {
		$line_co = explode('#*#',$ticket['customer_order_num']);
		$line_co_list = $dbc->query("SELECT `position` FROM `ticket_attached` WHERE `deleted`=0 AND IFNULL(`position`,'') != '' AND `ticketid`='$ticketid' AND `src_table`='inventory' GROUP BY `position` ORDER BY LPAD(`position`,100,0)");
		while($line_co_num = $line_co_list->fetch_assoc()) {
			$line_co[] = $line_co_num['position'];
		}
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Customer Order #:</label>
			<div class="col-sm-8">
				'.implode('<br />',array_filter(array_unique($line_co))).'
			</div>
		</div>';
	}
	if(in_array('Invoiced',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Invoiced:</label>
			<div class="col-sm-8">
				'.($ticket['invoiced'] > 0 ? 'Yes' : 'No').'
			</div>
		</div>';
	}
	if(in_array('Status',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Status:</label>
			<div class="col-sm-8">';
			if($tile_security['edit'] > 0) {
				echo '<select name="status[]" data-id="'.$ticket['ticketid'].'" onchange="setStatus(this);" class="chosen-select-deselect1 form-control">
						<option value=""></option>';
						foreach ($ticket_status_list as $cat_tab) {
							echo "<option ".($ticket['status'] == $cat_tab ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
						}
				echo '</select>';
			} else {
				echo $ticket['status'];
			}
			echo '</div>
		</div>';
	}
	if(in_array('Milestone Timeline',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Milestone & Timeline:</label>
		  <div class="col-sm-8">';
            ?>
			<?php if($tile_security['edit'] > 0) { ?>
				<select data-placeholder="Choose an Option..." name="milestone_timeline" id="milestone_timeline" data-table="tickets" data-id="<?= $ticket['ticketid'] ?>" data-id-field="ticketid" onchange="setMilestoneTimeline(this);" class="chosen-select-deselect form-control" width="580">
					<option value=""></option>
					<?php
                    $projectid = $ticket['projectid'];

					$milestone_list = $dbc->query("SELECT `milestones`.`id`, `milestones`.`milestone`, `milestones`.`label`, `milestones`.`sort`  FROM `project_path_custom_milestones` `milestones` LEFT JOIN `project` ON `milestones`.`projectid`=`project`.`projectid` AND CONCAT(',',`project`.`project_path`,',') LIKE CONCAT('%,',`milestones`.`pathid`,',%') WHERE `project`.`projectid`='$projectid' AND `milestones`.`path_type`='I' AND `milestones`.`deleted`=0 ORDER BY `milestones`.`pathid`,`milestones`.`path_type`,`milestones`.`sort`,`milestones`.`id`");
					while($milestone_row = $milestone_list->fetch_assoc()) {
						echo "<option ".($ticket['milestone_timeline'] == $milestone_row['milestone'] ? 'selected' : '')." value='". $milestone_row['milestone']."'>".$milestone_row['label'].'</option>';
					}
				  ?>
				</select>
			<?php } ?>
		  </div>

		</div>
        <?php
	}
	if(in_array('Total Budget Time',$db_config)) {
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Total Budget Time:</label>
			<div class="col-sm-8">';
			if($tile_security['edit'] > 0) {
				echo '<input type="text" name="total_budget_time" data-id="'.$ticket['ticketid'].'" onchange="setTotalBudgetTime(this);" class="timepicker-15 form-control" value="'.$ticket['total_budget_time'].'">';
			} else {
				echo $ticket['total_budget_time'];
			}
			echo '</div>
		</div>';
	}
	if(in_array('Service Time Estimate',$db_config)) {
		$serviceids = explode(',', $ticket['serviceid']);
		$service_qtys = explode(',', $ticket['service_qty']);

		$time_est = 0;
		foreach($serviceids as $i => $serviceid) {
			$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$serviceid'"));
			$estimated_hours = empty($service['estimated_hours']) ? '00:00' : $service['estimated_hours'];
			$qty = empty($service_qtys[$i]) ? 1 : $service_qtys[$i];
			$minutes = explode(':', $estimated_hours);
			$minutes = ($minutes[0]*60) + $minutes[1];
			$minutes = $qty * $minutes;
			$time_est += $minutes;
		}
		$new_hours = $time_est / 60;
		$new_minutes = $time_est % 60;
		$new_hours = sprintf('%02d', $new_hours);
		$new_minutes = sprintf('%02d', $new_minutes);
		$time_est = $new_hours.':'.$new_minutes;
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Total Time Estimate:</label>
			<div class="col-sm-8">';
				echo '<input type="text" name="service_time_estimate" disabled class="form-control" value="'.$time_est.'">';
			echo '</div>
		</div>';
	}
	if(in_array('Site Address',$db_config)) {
		$site = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$ticket['siteid']."' AND '".$ticket['siteid']."' > 0"));
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Site Address:</label>
			<div class="col-sm-8">'.!empty($site['address']) ? $site['address'] : $site['mailing_address'].'</div>
		</div>';
	}
	if(in_array('Site Notes',$db_config)) {
		$site = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts_description` WHERE `contactid` = '".$ticket['siteid']."' AND '".$ticket['siteid']."' > 0"));
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Site Notes:</label>
			<div class="col-sm-8">'.html_entity_decode($site['notes']).'</div>
		</div>';
	}
	if(in_array('Google Maps Link',$db_config)) {
        $map_link = !empty($ticket['map_link']) ? $ticket['map_link'] : 'http://maps.google.com/maps/place/'.$ticket['address'].','.$ticket['city'];
        if(empty($ticket['map_link']) && empty($ticket['address']) && !empty($ticket['siteid'])) {
			$site = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$ticket['siteid']."' AND '".$ticket['siteid']."' > 0"));
        	$map_link = !empty($site['google_maps_address']) ? $site['google_maps_address'] : 'http://maps.google.com/maps/place/'.(!empty($site['address']) ? $site['address'] : $site['mailing_address']).','.$site['city'];
        }
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Google Maps Link:</label>
			<div class="col-sm-8">'.(!empty($ticket['address']) || !empty($ticket['map_link'] || !empty($site['address']) || !empty($site['mailing_address']) || !empty($site['google_maps_address'])) ? '<a href="'.$map_link.'" target="_blank">Click Here</a>' : '').'</div>
		</div>';
	}

	if(in_array('Key Number',$db_config)) {
		$site = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT key_number FROM `contacts` WHERE `contactid` = '".$ticket['siteid']."' AND '".$ticket['siteid']."' > 0"));
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Key Number:</label>
			<div class="col-sm-8">'.$site['key_number'].'</div>
		</div>';
	}

	if(in_array('Door Code Number',$db_config)) {
		$site = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT door_code_number FROM `contacts` WHERE `contactid` = '".$ticket['siteid']."' AND '".$ticket['siteid']."' > 0"));
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Door Code Number:</label>
			<div class="col-sm-8">'.$site['door_code_number'].'</div>
		</div>';
	}

	if(in_array('Alarm Code Number',$db_config)) {
		$site = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT alarm_code_number FROM `contacts` WHERE `contactid` = '".$ticket['siteid']."' AND '".$ticket['siteid']."' > 0"));
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Alarm Code Number:</label>
			<div class="col-sm-8">'.$site['alarm_code_number'].'</div>
		</div>';
	}

	if(in_array('Ticket Notes',$db_config)) {
		$ticket_notes = mysqli_query($dbc, "SELECT * FROM `ticket_comment` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0");
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Notes:</label>
			<div class="col-sm-8">';
			while($ticket_note = mysqli_fetch_assoc($ticket_notes)) {
				echo trim(trim(html_entity_decode($ticket_note['comment']),"<p>"),"</p>")."<br />";
				echo "<em>Added by ".get_contact($dbc, $ticket_note['created_by'])." at ".$ticket_note['created_date']."</em><br />";
			}
			echo '</div>
		</div>';
	}
	if(in_array('Delivery Notes',$db_config)) {
		$ticket_stops = mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid` = '".$ticket['ticketid']."' AND `deleted` = 0 AND `type` != 'origin' AND `type` != 'destination'");
		$stop_count = 0;
		$delivery_stops = [];
		while($stop = mysqli_fetch_assoc($ticket_stops)) {
			$stop_count++;
			if(!empty($stop['notes'])) {
				$delivery_stops[] = 'Stop #'.$stop_count.': '.trim(trim(html_entity_decode($stop['notes']),'<p>'),'</p>');
			}
		}
		echo '<div class="col-sm-6">
			<label class="col-sm-4">Delivery Notes:</label>
			<div class="col-sm-8">'.implode('<br>',$delivery_stops).'
			</div>
		</div>';
	}
	if(in_array('Edit Archive',$db_config) || (in_array('Edit Staff',$db_config) && $tile_security['edit'] > 0)) { ?>
		<div class="col-sm-6">
			<?php $functions = [];
			if(in_array('Edit Archive',$db_config)) {
				if(in_array('Export Ticket Log',$db_config)) {
					$ticket_log_template = !empty(get_config($dbc, 'ticket_log_template')) ? get_config($dbc, 'ticket_log_template') : 'template_a';
					$functions[] = '<a href="../Ticket/ticket_log_templates/'.$ticket_log_template.'_pdf.php?ticketid='.$ticket['ticketid'].'">Export '.TICKET_NOUN.' Log</a>';
				}
				if(in_array('PDF',$db_config) && check_subtab_persmission($dbc, 'ticket', ROLE, 'view_pdf')) {
					$functions[] = '<a href="../Ticket/ticket_pdf.php?ticketid='.$ticket['ticketid'].'">View PDF <img src="../img/pdf.png" class="inline-img small"></a>';
				}
				if($tile_security['edit'] == 1) {
					$functions[] = '<a href=\'../Ticket/index.php?tile_name='.$_GET['tile'].'&edit='.$ticket['ticketid'].'&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].'\'>Edit</a>';
					$functions[] = '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&ticketid='.$ticket['ticketid'].'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
				} else {
					$functions[] = '<a href=\'../Ticket/index.php?tile_name='.$_GET['tile'].'&edit='.$ticket['ticketid'].'&from='.WEBSITE_URL.$_SERVER['REQUEST_URI'].'\'>View</a>';
				}
				if(check_subtab_persmission($dbc, 'ticket', ROLE, 'view_history') && !($strict_view > 0)) {
					$functions[] = '<span onclick="overlayIFrameDiv(\'ticket_history.php?ticketid='.$ticket['ticketid'].'\', true);" style="cursor:pointer">View History</span>';
				}
			}
			if(in_array('Edit Staff',$db_config) && $tile_security['edit'] > 0) {
				$functions[] = '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].'&calendar_view=true&edit_staff_dashboard=1\',\'auto\',false,true); return false;">Edit Staff</a>';
			}
			echo implode(' | ',$functions); ?>
		</div>
	<?php } ?>
	<div class="clearfix"></div>
</div>
