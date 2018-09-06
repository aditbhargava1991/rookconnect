<?php include_once('../include.php');
checkAuthorised('tasks');
$comments_taskid = filter_var($_GET['tasklistid'],FILTER_SANITIZE_STRING);
$comments = $dbc->query("SELECT `created_by`, `created_date`, `comment` FROM `task_comments` WHERE `tasklistid`='$comments_taskid' AND `tasklistid` > 0 AND `deleted`=0 ORDER BY `taskcommid` DESC");
if($comments->num_rows > 0) {
    $odd_even = 0; ?>
	<div class="col-sm-12 double-gap-top">
		<?php while($row_comment = $comments->fetch_assoc()) { ?>
			<?php $bg_class = $odd_even % 2 == 0 ? 'row-even-bg' : 'row-odd-bg'; ?>
            <div class="note_block row <?= $bg_class ?>">
				<div class="col-xs-2 col-sm-1"><?php profile_id($dbc, $row_comment['created_by']); ?></div>
				<div class="col-xs-10 col-sm-11">
					<div><?= preg_replace_callback('/\[PROFILE ([0-9]+)\]/',profile_callback,html_entity_decode($row_comment['comment'])); ?></div>
					<div class="gap-top"><em>Added by <?= get_contact($dbc, $row_comment['created_by']); ?> on <?= $row_comment['created_date']; ?></em></div>
				</div>
				<div class="clearfix"></div>
			</div>
			<?php $odd_even++; ?>
		<?php } ?>
	</div>
	<div class="clearfix"></div><?php
}