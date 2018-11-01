<script>

    /* Timer */
    $('.start-timer-btn').on('click', function() {
        $(this).closest('div').find('.timer').timer({
            editable: true
        });
        $(this).addClass('hidden');
        $(this).next('.stop-timer-btn').removeClass('hidden');
    });

    $('.stop-timer-btn').on('click', function() {
		$(this).closest('div').find('.timer').timer('stop');
		$(this).addClass('hidden');
		$('#timer_value').addClass('hidden');


		//$(this).prev('.start-timer-btn').removeClass('hidden');

        var projectid = $(this).data('id');

        var timer_value = $(this).closest('div').find('#timer_value').val();

		$(this).closest('div').find('.timer').timer('remove');

		if ( projectid!='' && typeof projectid!='undefined' && timer_value!='' ) {
            $.ajax({
                type: "GET",
                url: "projects_ajax.php?action=timer&projectid="+projectid+"&timer_value="+timer_value,
                dataType: "html",
                success: function(response) {
                    alert('Time added');
                }
            });
        }
    });


    /* Timer */


	$('.archive-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		$.ajax({
			url: 'projects_ajax.php?action=archive',
			method: 'POST',
			data: { id: item.data('id') },
            success: function(result) {
						alert('Project Archived');
					}
		});
		item.hide();
	});

	$('.email-icon').off('click').click(function() {
		// var item = $(this).closest('.dashboard-item');
		// var select = item.find('.select_users');
		// select.find('.cancel_button').off('click').click(function() {
		// 	select.find('select option:selected').removeAttr('selected');
		// 	select.hide();
		// 	return false;
		// });
		// select.find('.submit_button').off('click').click(function() {
		// 	if(select.find('select').val() != '' && confirm('Are you sure you want to send an e-mail to the selected user(s)?')) {
		// 		var users = [];
		// 		select.find('select option:selected').each(function() {
		// 			users.push(this.value);
		// 			$(this).removeAttr('selected');
		// 			select.find('select').trigger('change.select2');
		// 		});
		// 		$.ajax({
		// 			method: 'POST',
		// 			url: 'projects_ajax.php?action=quick_actions',
		// 			data: {
		// 				id: item.data('id'),
		// 				id_field: item.data('id-field'),
		// 				table: item.data('table'),
		// 				field: 'email',
		// 				value: users
		// 			},
		// 			success: function(result) {
		// 				select.hide();
		// 				select.find('select').trigger('change.select2');
		// 				item.find('h4').append(result);
		// 			}
		// 		});
		// 	}
		// 	return false;
		// });
		// select.show();
	});

	$('.attach-icon').off('click').click(function() {
		var item = $(this).closest('.dashboard-item');
		item.find('[type=file]').off('change').change(function() {
			var fileData = new FormData();
			fileData.append('file',$(this)[0].files[0]);
			fileData.append('field','document');
			fileData.append('table','project_document');
			fileData.append('folder','download');
			fileData.append('id',item.data('id'));
			fileData.append('id_field','ticketid');
			$.ajax({
				contentType: false,
				processData: false,
				method: "POST",
				url: "projects_ajax.php?action=quick_actions",
				data: fileData
			});
                $(this).hide().val('');
		}).click();
	});

$('.reminder-icon').off('click').click(function() {
    var item = $(this).closest('.dashboard-item');
    item.find('[name=reminder]').change(function() {
        var reminder = $(this).val();
        var select = item.find('.select_users');
        select.find('.cancel_button').off('click').click(function() {
            select.find('select option:selected').removeAttr('selected');
            select.find('select').trigger('change.select2');
            select.hide();
            return false;
        });
        select.find('.submit_button').off('click').click(function() {
            if(select.find('select').val() != '' && confirm('Are you sure you want to schedule reminders for the selected user(s)?')) {
                var users = [];
                select.find('select option:selected').each(function() {
                    users.push(this.value);
                    $(this).removeAttr('selected');
                });
                $.ajax({
                    method: 'POST',
                    url: 'projects_ajax.php?action=quick_actions',
                    data: {
                        id: item.data('id'),
                        id_field: item.data('id-field'),
                        table: item.data('table'),
                        field: 'reminder',
                        value: reminder,
                        users: users,
                        ref_id: item.data('id'),
                        ref_id_field: item.data('id-field')
                    },
                    success: function(result) {
                        select.hide();
                        select.find('select').trigger('change.select2');
                        item.find('h4').append(result);
                        alert("Reminder set");
                    }
                });
            }
            return false;
        });
        select.show();
    }).focus();
});

function saveNote(sel) {
    var projectid = $(sel).data('projectid');
    var note = sel.value;
    if (note!='') {
        $.ajax({
            url: 'projects_ajax.php?action=saveNote&projectid='+projectid+'&note='+note,
            success: function(response) {
               alert("Note saved.");
            }
        });
    }
}

</script>

<?php error_reporting(0);
include_once('../include.php');
ob_clean();
$tile = filter_var($_POST['tile'],FILTER_SANITIZE_STRING);
$security = get_security($dbc, $tile);
$strict_view = strictview_visible_function($dbc, 'project');
if($strict_view > 0) {
	$security['edit'] = 0;
	$security['config'] = 0;
	$security['approval'] = 0;
}
$project_count = 0;
$project_tabs = ['favourite'=>'Favourite'];
$pending_projects = get_config($dbc, 'project_status_pending');
if($pending_projects != 'disable') {
	$project_tabs['pending'] = 'Pending';
}
foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
	$project_tabs[config_safe_str($type_name)] = $type_name;
}
$status_list = explode('#*#',get_config($dbc, 'project_status'));
$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status` > 0 AND `show_hide_user`=1"));
$project_slider = get_config($dbc, 'project_slider');
$project_slider_label = get_config($dbc, 'project_slider_label');
foreach($_POST['projectids'] as $projectid) {
	if($projectid > 0) {
		$project_count++;
		$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
		$project_type = $project['projecttype'];
		$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$project_type'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
		$action = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project_actions` WHERE `projectid`='$projectid' AND `deleted`=0 AND `completed`=0 ORDER BY `due_date` ASC"));
		$invoices = mysqli_fetch_array(mysqli_query($dbc, "SELECT `paid` FROM `invoice` WHERE `projectid`='$projectid'"));
		if($invoices['paid'] == '')
			$invoices['paid'] = 'No';
		?>
		<div class="dashboard-item override-dashboard-item" data-id="<?= $project['projectid'] ?>">
			<h4>
				<?php $subtab_config = get_config($dbc, 'project_subtab');
					$config_value = $subtab_config;
					if($config_value != '')
						$subtab_value = '&tab='.$subtab_config;
					else
						$subtab_value = '';
				?>
				<a href="?edit=<?= $project['projectid'] ?>&tile_name=<?= $tile ?><?= $subtab_value ?>"><?= get_project_label($dbc, $project) ?>
					<span class="small">(<?= $project_tabs[$project['projecttype']] ?>)
						<?php if((in_array('DB Review',$value_config) || !in_array_any(['DB Project','DB Review','DB Status','DB Business','DB Contact','DB Billing','DB Type','DB Follow Up','DB Assign','DB Milestones'],$value_config)) && $security['edit'] > 0) { ?>
							<span class="review_date">Last Reviewed: <?= $project['reviewer_id'] > 0 ? date('Y-m-d', strtotime($project['review_date'])).' by '.get_contact($dbc, $project['reviewer_id']) : 'Never' ?></span>
						<?php } ?>
					</span>
				</a>
				<?php if($project_slider == 'button') { ?>
					<a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Project/projects.php?edit=<?= $project['projectid'] ?>&iframe_slider=1', 'auto', false, true); return false;" class="btn brand-btn"><?= (!empty($project_slider_label) ? $project_slider_label : 'Sign In') ?></a>
				<?php } else if($project_slider == 'icon') { ?>
					<a href="" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/Project/projects.php?edit=<?= $project['projectid'] ?>&iframe_slider=1', 'auto', false, true); return false;"><img src="../img/icons/eyeball.png" class="inline-img"></a>
				<?php } ?>
				<img class="inline-img pull-right" src="../img/full_favourite.png" style="<?= strpos($project['favourite'],','.$_SESSION['contactid'].',') !== FALSE ? '' : 'display: none' ?>" onclick="markFavourite(this);">
				<img class="inline-img pull-right" src="../img/blank_favourite.png" style="<?= strpos($project['favourite'],','.$_SESSION['contactid'].',') !== FALSE ? 'display: none' : '' ?>" onclick="markFavourite(this);">
				<?php if((in_array('DB Review',$value_config) || !in_array_any(['DB Project','DB Review','DB Status','DB Business','DB Contact','DB Billing','DB Type','DB Follow Up','DB Assign','DB Milestones'],$value_config)) && $security['edit'] > 0) { ?>
					<button class="inline-img pull-right image-btn double-gap-right" onclick="markReviewed($(this).closest('.dashboard-item')); return false;"><img src="../img/icons/ROOK-review-icon.png" alt="Review Now" title="Review Now" width="30" /></button>
				<?php } ?>
				<div  class="clearfix"></div>
			</h4>

            <div class="action-icons">
                <!-- All icons -->
                <!-- Email -->

                <a href="Add Email" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_email.php?tile=projects&id='+id,'auto',false,true); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-email-icon.png" class="inline-img email-icon" title="Send Email"></a>
                <!-- Email -->

                <!--<a href="Add Note" onclick="$(this).closest('.dashboard-item').find('[name=notes]').show().focus(); return false;">--><a href="#" onclick="overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_notes.php?tile=projects&id=<?= $projectid ?>','auto', false, true); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-reply-icon.png" class="inline-img reply-icon" title="Add Note" /></a>
                <!-- Note -->

                 <a href="Add Reminder" onclick="$(this).closest('.dashboard-item').find('[name=reminder]').show().focus(); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-reminder-icon.png" class="inline-img reminder-icon" title="Schedule Reminder"></a>
                <!-- reminder -->

                <a href="Add Reminder" onclick="$(this).closest('.dashboard-item').find('[name=document]').show().focus(); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-attachment-icon.png" class="inline-img attach-icon" title="Attach File"></a>
                <!-- document -->

                <a href="Add Timer" onclick="$(this).closest('.dashboard-item').find('.timer').show().focus(); return false;"><img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-timer2-icon.png" class="inline-img timer-icon" title="Start Timer" /></a>
                <!-- Timer -->

                <!-- archive -->
                <img src="<?= WEBSITE_URL; ?>/img/icons/ROOK-trash-icon.png" class="inline-img archive-icon" title="Archive">
                <!-- archive -->

                 <!-- All icons -->
            </div>

                <!-- Timer -->
                <div class="timer" style="display:none;">
                    <input type="text" name="timer_<?= $projectid ?>" id="timer_value" class="form-control timer" placeholder="0 sec" />
                    <a class="btn btn-success start-timer-btn brand-btn mobile-block">Start</a>
                    <a class="btn stop-timer-btn hidden brand-btn mobile-block" data-id="<?= $projectid ?>">Stop</a><br />
                    <input type="hidden" value="" name="track_time" />
                    <span class="added-time"></span>
                </div>
                <!-- Timer -->

            <!-- Note -->
            <input type="text" class="form-control gap-top" name="notes" id="notes" value="" style="display:none;" data-table="project_comment" data-projectid="<?= $projectid; ?>" onkeypress="javascript:if(event.keyCode==13){ saveNote(this); $(this).val('').hide(); };" onblur="saveNote(this); $(this).val('').hide();">

            <!-- reminder -->
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

            <!-- document -->
            <input type='file' name='document' value='' style="display:none;">

            <br>
            <div class="clearfix"></div>

			<?php if(in_array('DB Business',$value_config) || !in_array_any(['DB Project','DB Review','DB Status','DB Business','DB Contact','DB Billing','DB Type','DB Follow Up','DB Assign','DB Milestones'],$value_config)) { ?>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4"><?= BUSINESS_CAT ?>:</label>
						<div class="col-sm-8">
							<?= get_client($dbc, $project['businessid']) ?>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('DB Contact',$value_config) || !in_array_any(['DB Project','DB Review','DB Status','DB Business','DB Contact','DB Billing','DB Type','DB Follow Up','DB Assign','DB Milestones'],$value_config)) { ?>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4">Contact:</label>
						<div class="col-sm-8">
							<?= get_contact($dbc, $project['clientid']) ?>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('DB Status',$value_config) || !in_array_any(['DB Project','DB Review','DB Status','DB Business','DB Contact','DB Billing','DB Type','DB Follow Up','DB Assign','DB Milestones'],$value_config)) { ?>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4">Status:</label>
						<div class="col-sm-8">
							<?php if(($security['approval'] > 0 || $project['status'] != 'Pending') && $security['edit'] > 0) { ?>
								<select class="chosen-select-deselect" name="status" data-placeholder="Select Status" data-table="project" data-identifier="projectid" data-id="<?= $project['projectid'] ?>"><option></option>
									<?php if($pending_projects != 'disable') { ?>
										<option <?= 'Pending' == $project['status'] ? 'selected' : '' ?> value="Pending">Pending</option>
									<?php } ?>
									<?php foreach($status_list as $status_name) { ?>
										<option <?= $status_name == $project['status'] ? 'selected' : '' ?> value="<?= $status_name ?>"><?= $status_name ?></option>
									<?php } ?>
									<option <?= 'Archive' == $project['status'] ? 'selected' : '' ?> value="Archive">Archive</option>
								</select>
							<?php } else {
								echo $project['status'];
							} ?>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('DB Billing',$value_config) || !in_array_any(['DB Project','DB Review','DB Status','DB Business','DB Contact','DB Billing','DB Type','DB Follow Up','DB Assign','DB Milestones'],$value_config)) { ?>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4">Paid:</label>
						<div class="<?php if($security['edit'] > 0) { ?>toggle-switch<?php } ?> form-group col-sm-1"><?php if($security['edit'] > 0) { ?><input type="hidden" name="paid" value="<?= $invoices['paid'] ?>" data-table="invoice" data-identifier="projectid" data-id="<?= $project['projectid'] ?>"><?php } ?>
							<img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" style="height: 2em; <?= $invoices['paid'] == 'No' ? '' : 'display: none;' ?>">
							<img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" style="height: 2em; <?= $invoices['paid'] == 'Yes' ? '' : 'display: none;' ?>">
						</div>
						<a href="edit_project_billing_pay_schedule.php?projectid=<?= $projectid ?>&tab=payment_schedule" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><img class="inline-img" src="../img/icons/eyeball.png"></a>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('DB Type',$value_config) || !in_array_any(['DB Project','DB Review','DB Status','DB Business','DB Contact','DB Billing','DB Type','DB Follow Up','DB Assign','DB Milestones'],$value_config)) { ?>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4"><?= PROJECT_NOUN ?> Type:</label>
						<div class="col-sm-8">
							<?php if($security['edit'] > 0) { ?>
								<select name="projecttype" class="chosen-select-deselect" data-table="project" data-identifier="projectid" data-id="<?= $project['projectid'] ?>" data-project="<?= $project['projectid'] ?>">
									<option></option>
									<?php foreach($project_tabs as $value => $type) {
										if($type != '' && $value != 'favourite' && $value != 'pending') { ?>
											<option <?= $value == $project['projecttype'] ? 'selected' : '' ?> value="<?= $value ?>"><?= $type ?></option>
										<?php }
									} ?>
								</select>
							<?php } else {
								echo $project_tabs[$project['projecttype']];
							} ?>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('DB Follow Up',$value_config) || !in_array_any(['DB Project','DB Review','DB Status','DB Business','DB Contact','DB Billing','DB Type','DB Follow Up','DB Assign','DB Milestones'],$value_config)) { ?>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4">Follow Up Date:</label>
						<div class="col-sm-8">
							<?php if($security['edit'] > 0) { ?>
								<input type="text" class="form-control datepicker" name="followup" value="<?= $project['followup'] ?>" data-table="project" data-identifier="projectid" data-id="<?= $project['projectid'] ?>" data-project="<?= $project['projectid'] ?>">
							<?php } else {
								echo $project['followup'];
							} ?>
						</div>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('DB Assign',$value_config) || !in_array_any(['DB Project','DB Review','DB Status','DB Business','DB Contact','DB Billing','DB Type','DB Follow Up','DB Assign','DB Milestones'],$value_config)) { ?>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4"><?= PROJECT_NOUN ?> Lead:</label>
						<div class="col-sm-8">
							<?php if($security['edit'] > 0) { ?>
								<select name="project_lead" class="chosen-select-deselect" data-table="project" data-identifier="projectid" data-id="<?= $project['projectid'] ?>" data-project="<?= $project['projectid'] ?>">
									<option></option>
									<?php foreach($staff_list as $staff) { ?>
										<option <?= $staff['contactid'] == $project['project_lead'] ? 'selected' : '' ?> value="<?= $staff['contactid'] ?>"><?= $staff['first_name'].' '.$staff['last_name'] ?></option>
									<?php } ?>
								</select>
							<?php } else {
								echo get_contact($dbc, $project['project_lead']);
							} ?>
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if(in_array('DB Colead',$value_config) || !in_array_any(['DB Project','DB Review','DB Status','DB Business','DB Contact','DB Billing','DB Type','DB Follow Up','DB Colead','DB Milestones'],$value_config)) { ?>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4"><?= PROJECT_NOUN ?> Co-Lead:</label>
						<div class="col-sm-8">
							<?php if($security['edit'] > 0) { ?>
								<select name="project_colead" class="chosen-select-deselect" data-table="project" data-identifier="projectid" data-id="<?= $project['projectid'] ?>" data-project="<?= $project['projectid'] ?>">
									<option></option>
									<?php foreach($staff_list as $staff) { ?>
										<option <?= $staff['contactid'] == $project['project_colead'] ? 'selected' : '' ?> value="<?= $staff['contactid'] ?>"><?= $staff['first_name'].' '.$staff['last_name'] ?></option>
									<?php } ?>
								</select>
							<?php } else {
								echo get_contact($dbc, $project['project_colead']);
							} ?>
						</div>
					</div>
				</div>
			<?php } ?>


			<?php if(in_array('DB Total Tickets',$value_config) || !in_array_any(['DB Project','DB Review','DB Status','DB Business','DB Contact','DB Billing','DB Type','DB Follow Up','DB Colead','DB Milestones','DB Total Tickets'],$value_config)) { ?>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4">Total Tickets:</label>
						<div class="col-sm-8">
                        <?php
                                    $projectid = $project['projectid'];
								    $active_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(projectid) AS total_id FROM tickets WHERE `projectid` = '$projectid' AND `deleted`=0 AND `status` NOT IN ('Archive','Archived','Done')"));
                                    echo 'Active - '.$active_ticket['total_id'].' : ';
								    $inactive_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(projectid) AS total_id FROM tickets WHERE `projectid` = '$projectid' AND `deleted`=0 AND `status` IN ('Archive','Archived','Done')"));
                                    echo 'Archived/Done - '.$inactive_ticket['total_id'];
                         ?>
						</div>
					</div>
				</div>
			<?php } ?>

			<?php if(in_array('DB Milestones',$value_config)) { ?>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4"><?= PROJECT_NOUN ?> Milestones:</label>
						<?php $milestone_list = [];
						$unassigned_sql = "SELECT SUM(IF(`type`='Ticket',`count`,0)) `tickets`, SUM(IF(`type`='Intake',`count`,0)) `intake` FROM (SELECT 'Ticket' `type`, COUNT(*) `count` FROM tickets WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(milestone_timeline, '&gt;','>'), '&lt;','<'), '&nbsp;',' '), '&amp;','&'), '&quot;','\"'),'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '') UNION
							SELECT 'Intake' `type`, COUNT(*) `count` FROM intake WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND (`project_milestone` = '' OR IFNULL(project_milestone,'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid'))) `unassigned`";
						$unassigned = $dbc->query($unassigned_sql)->fetch_assoc();
						if($unassigned['tickets'] > 0 || $unassigned['intake'] > 0) {
							$milestone_list[] = trim('Unassigned: '.($unassigned['tickets'] > 0 ? $unassigned['tickets'].' '.TICKET_NOUN.' ' : '').($unassigned['intake'] > 0 ? $unassigned['intake'].' Forms' : ''));
						}
						$milestones = $dbc->query("SELECT `miles`.`milestone`, `label`, `pathid`, SUM(IF(`list`.`type`='Ticket' AND `list`.`id` > 0,1,0)) `tickets`, SUM(IF(`list`.`type`='Task' AND `list`.`id` > 0,1,0)) `tasks`, SUM(IF(`list`.`type`='Intake' AND `list`.`id` > 0,1,0)) `intake` FROM `project_path_custom_milestones` `miles` LEFT JOIN (SELECT 'Ticket' `type`, `ticketid` `id`, `projectid`, `milestone_timeline` `milestone` FROM `tickets` WHERE `status` NOT IN ('Archive','Archived','Done') AND `deleted`=0 UNION SELECT 'Task' `type`, `tasklistid` `id`, `projectid`, `project_milestone` `milestone` FROM `tasklist` WHERE `status` != 'Done' AND `deleted`=0 UNION SELECT 'Intake' `type`, `intakeid` `id`, `projectid`, `project_milestone` `milestone` FROM `intake` WHERE `deleted`=0) `list` ON `miles`.`milestone`=`list`.`milestone` AND `miles`.`projectid`=`list`.`projectid` WHERE `miles`.`projectid`='".$project['projectid']."' AND `miles`.`deleted`=0 GROUP BY `miles`.`projectid`, `miles`.`milestone` ORDER BY `miles`.`sort`,`miles`.`id`");
						while($milestone = $milestones->fetch_assoc()) {
							$milestone_list[] = trim($milestone['label'].': '.($milestone['tickets']+$milestone['tasks']+$milestone['intake'] == 0 ? 'Empty' : '').($milestone['tickets'] > 0 ? $milestone['tickets'].' '.TICKET_NOUN.' ' : '').($milestone['tasks'] > 0 ? $milestone['tasks'].' Tasks ' : '').($milestone['intake'] > 0 ? $milestone['intake'].' Forms' : ''));
						}
						if(empty($milestone_list)) { ?>
							<div class="col-sm-8">No Milestones Found</div>
						<?php } else { ?>
							<div class="col-sm-7">
								<select class="form-control">
									<?php foreach($milestone_list as $i => $milestone) { ?>
										<option value="<?= $i ?>"><?= $milestone ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-sm-1"><a href="?edit=<?= $projectid ?>&tab=path&pathid=MS" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><img class="inline-img" src="../img/icons/eyeball.png"></a></div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>
			<?php if(in_array('DB Status List',$value_config)) { ?>
				<div class="col-sm-6">
					<div class="form-group">
						<label class="col-sm-4">Scrum Board:</label>
						<?php $status_list = [];
						$statuses = $dbc->query("SELECT `status`, SUM(IF(`list`.`type`='Ticket' AND `list`.`id` > 0,1,0)) `tickets`, SUM(IF(`list`.`type`='Task' AND `list`.`id` > 0,1,0)) `tasks`, SUM(IF(`list`.`type`='Intake' AND `list`.`id` > 0,1,0)) `intake` FROM (SELECT 'Ticket' `type`, `ticketid` `id`, `projectid`, `status` FROM `tickets` WHERE `status` NOT IN ('Archive','Archived','Done') AND `deleted`=0 UNION SELECT 'Task' `type`, `tasklistid` `id`, `projectid`, `status` FROM `tasklist` WHERE `status` != 'Done' AND `deleted`=0) `list` WHERE `list`.`projectid`='".$project['projectid']."' GROUP BY `list`.`status` ORDER BY `list`.`status`");
						while($status = $statuses->fetch_assoc()) {
							$status_list[] = trim($status['status'].': '.($status['tickets'] > 0 ? $status['tickets'].' '.TICKET_NOUN.' ' : '').($status['tasks'] > 0 ? $status['tasks'].' Tasks ' : ''));
						}
						if(empty($status_list)) { ?>
							<div class="col-sm-8">No Statuses Found</div>
						<?php } else { ?>
							<div class="col-sm-7">
								<select class="form-control">
									<?php foreach($status_list as $i => $status) { ?>
										<option value="<?= $i ?>"><?= $status ?></option>
									<?php } ?>
								</select>
							</div>
							<div class="col-sm-1"><a href="?edit=<?= $projectid ?>&tab=path&pathid=AllSB" onclick="overlayIFrameSlider(this.href,'auto',true,true); return false;"><img class="inline-img" src="../img/icons/eyeball.png"></a></div>
						<?php } ?>
					</div>
				</div>
			<?php } ?>

			<div class="clearfix"></div>

		</div>
	<?php }
}
if($project_count == 0) {
	echo "<h4>No projects to display.";
} ?>
