<?php // Calculate start of week
$date = strtotime($_GET['date'] != '' ? $_GET['date'] : 'today');
$day_num = date('w',$date);
if($_GET['mode'] == 'week') {
	$start_date = strtotime(date('Y-m-d',$date).' -'.$day_num.'days');
	$end_date = strtotime(date('Y-m-d',$start_date).' +6days');
} else {
	$start_date = $end_date = $date;
}
?>
<script>
$(document).ready(function() {
    $('[name="daily_notes"]').change(function() {
        var notes = this.value;
        var date = $(this).data('date');
        var contactid = '<?= $_SESSION['contactid'] ?>';
        $.ajax({
            url: '../Profile/profile_ajax.php?fill=daysheet_notepad',
            method: 'POST',
            data: {
                notes: notes,
                date: date,
                contactid: contactid
            }
        });
    });
    $('[name="prior_notes"]').change(function() {
		var notepad = this;
        var notes = this.value;
        var date = $(this).data('date');
        var contactid = '<?= $_SESSION['contactid'] ?>';
        $.ajax({
            url: '../Profile/profile_ajax.php?fill=daysheet_notepad_add',
            method: 'POST',
            data: {
                notes: notes,
                date: date,
                contactid: contactid
            },
			success: function(response) {
				tinymce.editors[notepad.id].setContent('');
				$(notepad).closest('.weekly-div').find('.add_note').hide();
				$(notepad).closest('.weekly-div').find('.add_note_btn').text('Add Note');
			}
        });
    });
});
</script>
<div class="col-sm-12 gap-top main-screen-header">
	<h1 class="no-margin">My Journal <span class="smaller">
		<a href="?tab=journals&date=<?= $_GET['date'] ?>"><img class="inline-img <?= $_GET['mode'] == 'week' ? 'black-color' : '' ?>" src="../img/month-overview-blue.png"></a>
		<a href="?tab=journals&mode=week&date=<?= $_GET['date'] ?>"><img class="inline-img <?= $_GET['mode'] == 'week' ? '' : 'black-color' ?>" src="../img/weekly-overview-blue.png"></a>
	</span></h1>
	<h4>
		<a href="?tab=journals&mode=<?= $_GET['mode'] ?>&date=<?= $_GET['mode'] == 'week' ? date('Y-m-d', strtotime($_GET['date'].'-7days')) : date('Y-m-d', strtotime($_GET['date'].'-1day')) ?>"><img class="inline-img-height smaller" src="<?= WEBSITE_URL ?>/img/icons/back-arrow.png"></a>
		<a href="" onclick="$('.view_week').focus(); return false;"><?= date('Y-m-d',$start_date) ?><?= $end_date != $start_date ? ' - '.date('Y-m-d',$end_date) : '' ?>
		<img class="inline-img smaller" src="<?= WEBSITE_URL ?>/img/calendar.png"></a>
		<input type="text" style="border:0;width:0;" class="view_week datepicker" onchange="window.location.replace('?tab=journals&mode=<?= $_GET['mode'] ?>&date='+this.value);" value="<?= date('Y-m-d',$date) ?>">
		<a href="?tab=journals&mode=<?= $_GET['mode'] ?>&date=<?= $_GET['mode'] == 'week' ? date('Y-m-d', strtotime($_GET['date'].'+7days')) : date('Y-m-d', strtotime($_GET['date'].'+1day')) ?>"><img class="inline-img-height smaller" src="<?= WEBSITE_URL ?>/img/icons/next-arrow.png"></a>
	</h4>
	<div class="main-screen-white main-screen-details padded" style="height: auto;">
		<?php for(; $start_date <= $end_date; $start_date += 86400) { ?>
			<div class="weekly-div">
				<h4><?= date('l, F j',$start_date) ?></h4>
				<?php $current_date = date('Y-m-d',$start_date);
				$user = $_SESSION['contactid'];
				$notes = mysqli_query($dbc, "SELECT * FROM (SELECT `comment`, 'comment' `src`, 0 `src_id`, 'Budget Comment' `type`, `created_by` `user`, `email_to` `assigned`, `created_date` FROM `budget_comment` WHERE '$user' IN (`created_by`,`email_to`) AND `created_date`='$current_date' UNION
					SELECT `comment`, 'comment' `src`, 0 `src_id`, 'Project Comment' `type`, `created_by` `user`, `email_comment` `assigned`, `created_date` FROM `project_comment` WHERE '$user' IN (`created_by`,`email_comment`) AND `created_date`='$current_date' AND `type`='project_note' UNION
					SELECT `comment`, 'comment' `src`, 0 `src_id`, 'Task Note' `type`, `created_by` `user`, 0 `assigned`, `created_date` FROM `task_comments` WHERE '$user' IN (`created_by`) AND `created_date`='$current_date' AND `deleted`=0 UNION
					SELECT `comment`, 'comment' `src`, 0 `src_id`, '".TICKET_NOUN." Note' `type`, `created_by` `user`, `email_comment` `assigned`, `created_date` FROM `ticket_comment` WHERE CONCAT(',',IFNULL(`created_by`,''),',',IFNULL(`email_comment`,''),',') LIKE '%,$user,%' AND `created_date` LIKE '$current_date' AND `deleted`=0 UNION
					SELECT `notes` `comment`, 'comment' `src`, 0 `src_id`, 'Estimate Note' `type`, `created_by` `user`, `assigned` `assigned`, `note_date` `created_date` FROM `estimate_notes` WHERE '$user' IN (`created_by`,`assigned`) AND `note_date`='$current_date' AND `deleted`=0 UNION
					SELECT `note` `comment`, 'comment' `src`, 0 `src_id`, 'Daily Log Notes' `type`, `created_by` `user`, `client_id` `assigned`, `note_date` `created_date` FROM `client_daily_log_notes` WHERE '$user' IN (`created_by`) AND `note_date` LIKE '$current_date%' AND `deleted`=0 UNION
					SELECT `notes` `comment`, 'comment' `src`, 0 `src_id`, 'Journal Note' `type`, `contactid` `user`, 0 `assigned`, `date` `created_date` FROM `daysheet_notepad` WHERE `date`='$current_date' AND `date` < DATE(NOW()) AND `contactid`='$user' UNION
					SELECT `notes` `comment`, 'comment' `src`, 0 `src_id`, 'Scrum Notes' `type`, `contactid` `user`, 0 `assigned`, `date` `created_date` FROM `daysheet_notepad` WHERE `date`='$current_date' AND `date` != '' AND `contactid`='0' UNION
					SELECT '' `comment`, `type` `src`, `tableid` `src_id`, CONCAT('Worked on ',IF(`type`='Ticket','".TICKET_NOUN."',IF(`type`='Project','".PROJECT_NOUN."',`type`)),': ',`description`) `type`, `contactid` `user`, 0 `assigned`, `timestamp` `created_date` FROM `day_overview` WHERE `contactid`='$user' AND `today_date`='$current_date') notes ORDER BY `created_date` ASC");
				if(mysqli_num_rows($notes) > 0) {
					while($day_note = mysqli_fetch_assoc($notes)) {
						echo "<h5>".($day_note['user'] > 0 ? profile_id($dbc, $day_note['user'],false) : '').($day_note['src'] == 'Task' ? '<a href="" onclick="overlayIFrameSlider(\''.WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$day_note['src_id'].'\'); return false;">' : ($day_note['src'] == 'Project' ? '<a href="../Project/projects.php?edit='.$day_note['src_id'].'">' : ($day_note['src'] == 'Ticket' ? '<a href="'.WEBSITE_URL.'/Ticket/index.php?edit='.$day_note['src_id'].'" onclick="overlayIFrameSlider(this.href+\'&calendar_view=true\'); return false;">' : ''))).$day_note['type'].($day_note['src'] == 'Project' || $day_note['src'] == 'Ticket' || $day_note['src'] == 'Task' ? '</a>' : '')."</h5>".html_entity_decode($day_note['comment']);
						foreach(array_filter(explode(',',$day_note['assigned'])) as $assigned_user) {
							if($assigned_user > 0) {
								echo "<em><small>".profile_id($dbc, $assigned_user, false)." Assigned to ".get_contact($dbc, $assigned_user)."</small></em>";
							}
						}
					}
				} else if($start_date < strtotime('today')) {
					echo "<h5>Nothing Found</h5>";
				} ?>
				<?= $start_date >= strtotime('today') ? '' : '<div class="'.date('Y_m_d',$start_date).' add_note" style="display:none;">' ?>
				<?php $notepad_result = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `notes` FROM `daysheet_notepad` WHERE `contactid` = '".$_SESSION['contactid']."' AND `date` = '".date('Y-m-d',$start_date)."'")); ?>
				<textarea data-date="<?= date('Y-m-d',$start_date) ?>" name="<?= $start_date >= strtotime('today') ? 'daily_notes' : 'prior_notes' ?>"><?= $start_date >= strtotime('today') ? html_entity_decode($notepad_result['notes']) : '' ?></textarea>
				<?= $start_date >= strtotime('today') ? '' : '</div><button onclick="$(\'.'.date('Y_m_d',$start_date).'.add_note\').show(); $(this).text(\'Save\'); return false;" class="btn brand-btn pull-right add_note_btn">Add Journal Note</button>' ?>
				<div class="clearfix"></div>
				<hr>
			</div>
		<?php } ?>
		<a href="daysheet.php?tab=daysheet" class="btn brand-btn pull-right">Submit</a>
	</div>
</div>