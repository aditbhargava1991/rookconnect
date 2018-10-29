<?php include_once('config.php');
$sql = "SELECT `status`, COUNT(*) `tickets` FROM `tickets` WHERE `ticketid` > 0 AND `businessid` > 0 AND `deleted`=0 AND `businessid`='$user' AND `status` NOT IN ('Archive','Archived','".implode("','",explode('#*#',get_config($dbc, "ticket_archive_status")))."') GROUP BY `status`";
$ticket_count = $dbc_support->query($sql);
$status_list = explode(',',get_config($dbc_support,'ticket_status'));
$status_counts = [];
while($status_count = $ticket_count->fetch_assoc()) {
    foreach($status_list as $i => $status) {
        if($status == $status_count['status']) {
            $status_counts[$i] = ['status' => $status, 'count' => $status_count['tickets']];
        }
    }
}
$sql = "SELECT `status`, COUNT(*) `tasklist` FROM `tasklist` WHERE `tasklistid` > 0 AND (`businessid`='$user' OR `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `businessid`='$user' AND `businessid` > 0 AND `deleted`=0) OR `projectid` IN (SELECT `projectid` FROM `project` WHERE `businessid`='$user' AND `businessid` > 0)) AND `deleted`=0 AND `status` NOT IN ('Archive') and is_sync = 1 GROUP BY `status`";
$tasklist_count = $dbc_support->query($sql);
$tasklist_list = explode(',',get_config($dbc_support,'task_scrum_status'));
$status_tasklist_counts = [];
while($status_tasklist_count = $tasklist_count->fetch_assoc()) {
    foreach($tasklist_list as $i => $status) {
        if($status == $status_tasklist_count['status']) {
            $status_tasklist_counts[$i] = ['status' => $status, 'count' => $status_tasklist_count['tasklist']];
        }
    }
}

ksort($status_tasklist_counts);
ksort($status_counts); ?>
<script>
$(document).ready(function() {
    resizePath();
});
function resizePath() {
	$('.double-scroller div').width($('.dashboard-container').get(0).scrollWidth);
	$('.double-scroller').off('scroll',doubleScroll).scroll(doubleScroll);
	$('.dashboard-container').off('scroll',setDoubleScroll).scroll(setDoubleScroll);
	if($(window).width() > 767 && $('ul.dashboard-list').length > 0 && ($('.tile-content').outerHeight() + $('.tile-content').offset().top) - ($($('ul.dashboard-list').first()).offset().top + ($('.dashboard-container').outerHeight() - $('.dashboard-container').prop('clientHeight'))) - 2 > 250) {
		$('ul.dashboard-list').outerHeight(($('.tile-content').outerHeight() + $('.tile-content').offset().top) - ($($('ul.dashboard-list').first()).offset().top + ($('.dashboard-container').outerHeight() - $('.dashboard-container').prop('clientHeight'))) - 2);
	} else {
		var height = 0;
		$('ul.dashboard-list').each(function() {
			height = $(this).height() > height ? $(this).height() : height;
		});
		$('ul.dashboard-list').outerHeight(height);
	}
}
function doubleScroll() {
	$('.dashboard-container').scrollLeft(this.scrollLeft).scroll();
}
function setDoubleScroll() {
	$('.double-scroller').scrollLeft(this.scrollLeft);
}
</script>
<div class="double-scroller"><div></div></div>
<div class="has-dashboard form-horizontal dashboard-container" style="<?= $pathid == 'MS' ? 'overflow-y:hidden;' : '' ?>">
    <?php foreach($status_counts as $status_count) { ?>
        <div class="dashboard-list item_list" style="margin-bottom: -10px;">
            <div class="info-block-header"><h4><?= $status_count['status'] ?></h4>
                <div class="small">Active: <?= $status_count['count'] ?></div>
            </div>
            <ul class="dashboard-list">
                <?php $ticket_list = $dbc_support->query("SELECT * FROM `tickets` WHERE `ticketid` > 0 AND `businessid` > 0 AND `deleted`=0 AND `businessid`='$user' AND `status`='".$status_count['status']."'");
                while($row = mysqli_fetch_array($ticket_list)) { ?>
                    <li class="dashboard-item " data-id="9" data-table="tickets" data-name="milestone_timeline" data-id-field="ticketid" data-colour="" style=""><span>
                        <?php if($user_category == 'Staff') { ?>
                            <a href="../Ticket/index.php?edit=<?= $row['ticketid'] ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true','auto',true,true)"><?= get_ticket_label($dbc, $row) ?><img src="<?= WEBSITE_URL ?>/img/icons/ROOK-edit-icon.png" class="no-toggle inline-img cursor-hand" title="Edit <?= TICKET_NOUN ?>"></a>
                        <?php } else {
                            echo get_ticket_label($dbc, $row);
                        } ?><br />
                        Scheduled Date: <?= $row['to_do_date'] ?><br />
                        Internal QA Date: <?= $row['internal_qa_date'] ?><br />
                        Estimated Completion Date: <?= $row['deliverable_date'] ?>
                    </span></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
    <?php foreach($status_tasklist_counts as $status_count) { ?>
        <div class="dashboard-list item_list" style="margin-bottom: -10px;">
            <div class="info-block-header"><h4><?= $status_count['status'] ?></h4>
                <div class="small">Active: <?= $status_count['count'] ?></div>
            </div>
            <ul class="dashboard-list">
                <?php $tasklist_list = $dbc_support->query("SELECT * FROM `tasklist` WHERE `tasklistid` > 0 AND `deleted`=0 AND (`businessid`='$user' OR `ticketid` IN (SELECT `ticketid` FROM `tickets` WHERE `businessid`='$user' AND `businessid` > 0 AND `deleted`=0) OR `projectid` IN (SELECT `projectid` FROM `project` WHERE `businessid`='$user' AND `businessid` > 0)) AND `is_sync`=1 AND `status`='".$status_count['status']."'");
                while($row = mysqli_fetch_array($tasklist_list)) { ?>
                    <li class="dashboard-item " data-id="9" data-table="tickets" data-name="milestone_timeline" data-id-field="ticketid" data-colour="" style=""><span>
                        <span>
                          Task #<?= $row['tasklistid'] ?> - <?= $row['heading'] ?><br>
                          Task Created Date : <?= $row['created_date'] ?><br>
                          Task To-Do Date : <?= $row['task_tododate'] ?>
                        </span>
                    </li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>
</div>
