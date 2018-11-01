<?php include_once('config.php');
$project_list = mysqli_query($dbc_support, "SELECT `tasklist`.`projectid`, 'project' `table` FROM `tasklist` LEFT JOIN `project` ON `tasklist`.`projectid`=`project`.`projectid` WHERE (`project`.`businessid`='$user' OR CONCAT(',',`project`.`clientid`,',') LIKE '%,$user,%') AND `tasklist`.`assign_client`=1 GROUP BY `projectid`");
if(mysqli_num_rows($project_list) > 0) {
	$projects = [];
	while($project = mysqli_fetch_assoc($project_list)) {
		$project_info = [];
		if($project['table'] == 'project') {
			$project_info = mysqli_fetch_assoc(mysqli_query($dbc_support, "SELECT `project_name`, `milestone` FROM `project` LEFT JOIN `project_path_milestone` ON `project`.`external_path`=`project_path_milestone`.`project_path_milestone` WHERE `projectid`='".$project['projectid']."'"));
			$projects[] = [$project['projectid'],'project','tasklist', explode('#*#',$project_info['milestone'])];
		} ?>
		<button class="btn brand-btn project_btn <?= count($projects) > 1 ? '' : 'active_tab' ?>" onclick="$('.project_div').hide(); $('.project_btn.active_tab').removeClass('active_tab'); $(this).addClass('active_tab'); $('.<?= $project['table'].$project['projectid'] ?>').show();"><?= $project_info['project_name'] ?></button>
	<?php }
	foreach($projects as $i => $project) {
		$projectid = $project[0];
		$project_table = $project[1];
		$task_table = $project[2];
		$milestones = $project[3]; ?>
		<div class="margin-vertical has-dashboard form-horizontal dashboard-container project_div <?= $project[1].$project[0] ?>" style="<?= $i > 0 ? 'display:none;' : '' ?>">
			<?php foreach($milestones as $milestone) { ?>
				<ul class="<?= $project_table == 'project' ? 'dashboard-list' : 'connectedChecklist' ?>">
					<li class="ui-state-default ui-state-disabled no-sort"><?= $milestone ?></li>
					<?php $milestone = ($project_table == 'project' ? " AND `external`='$milestone' " : '');
					$item_list = mysqli_query($dbc_support, "SELECT *, '$project_table' `table` FROM `$task_table` WHERE `projectid`='$projectid' AND `deleted`=0 AND `assign_client`=1 $milestone");
					while($row = mysqli_fetch_array($item_list)) {
						echo '<a name="'.$row['tasklistid'].'"></a><li id="'.$row['tasklistid'].'" class="ui-state-default" style="'.($row['flag_colour'] == '' ? '' : 'background-color: #'.$row['flag_colour'].';').' border: solid #000000 2px;">';
						echo '<span>';
						echo '<span style="cursor:pointer; display:block; width:100%;" data-support="'.$row['tasklistid'].'" data-table="'.$project_table.'">';
						echo '<span style="display:inline-block; text-align:center; width:33%;" title="Flag This!" onclick="flag_item(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-flag-icon.png" style="height:1.5em;" onclick="return false;"></span>';
						echo '<span style="display:inline-block; text-align:center; width:33%;" title="Attach File" onclick="attach_file(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-attachment-icon.png" style="height:1.5em;" onclick="return false;"></span>';
						echo '<span style="display:inline-block; text-align:center; width:33%;" title="Reply" onclick="send_reply(this); return false;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-reply-icon.png" style="height:1.5em;" onclick="return false;"></span>';
						echo '</span>';
						echo '<input type="text" name="reply_'.$row['tasklistid'].'" style="display:none;" class="form-control" />';
						echo '<input type="file" name="attach_'.$row['tasklistid'].'" style="display:none;" class="form-control" />';
						echo '<span class="display-field">'.html_entity_decode($row['task']);
						$documents = mysqli_query($dbc_support, "SELECT * FROM `".$task_table."_document` WHERE tasklistid='".$row['tasklistid']."'");
						while($doc = mysqli_fetch_array($documents)) {
							$link = $doc['document'];
							$project_folder = '';
							if($project_table == 'project') {
								$project_folder = '../Project/';
							}
							echo '<a href="'.$project_folder.'download/'.$link.'">'.$link.' (Attached by '.$doc['created_by'].' on '.$doc['created_date'].')</a><br />';
						}
						echo '</span></span></li>';
					} ?>
				</ul>
			<?php } ?>
		</div>
	<?php }
} else {
	echo "<h3>No Items Found</h3>";
} ?>
<script>
function send_reply(support) {
	support_id = $(support).parents('span').data('support');
	table = $(support).parents('span').data('table');
	$('[name=reply_'+support_id+']').show().keyup(function(e) {
		if(e.which == 13) {
			$(this).blur();
		}
	}).blur(function() {
		$(this).hide();
		var reply = $(this).val().trim();
		$(this).val('');
		if(reply != '') {
			var today = new Date();
			var save_reply = reply + "<p><em>Reply added by <?php echo decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']); ?> at "+today.toLocaleString()+"</em></p>";
			$.ajax({
				method: 'POST',
				url: 'support_ajax.php?fill=checklistreply',
				data: { id: support_id, reply: save_reply, table_name: table },
				complete: function(result) { window.location.reload(); }
			})
		}
	}).focus();
}
function attach_file(support) {
	support_id = $(support).parents('span').data('support');
	table = $(support).parents('span').data('table');
	$('[name=attach_'+support_id+']').change(function() {
		var fileData = new FormData();
		fileData.append('file',$('[name=attach_'+support_id+']')[0].files[0]);
		$.ajax({
			contentType: false,
			processData: false,
			type: "POST",
			url: "support_ajax.php?fill=checklistupload&table_name="+table+"&id="+support_id,
			data: fileData,
			complete: function(result) {
				console.log(result.responseText);
				window.location.reload();
			}
		});
	}).click();
}
function flag_item(support) {
	support_id = $(support).parents('span').data('support');
	table = $(support).parents('span').data('table');
	$.ajax({
		method: "POST",
		url: "support_ajax.php?fill=checklistflag",
		data: { id: support_id, table_name: table },
		complete: function(result) {
			console.log(result.responseText);
			$(support).closest('li').css('background-color',(result.responseText == '' ? '' : '#'+result.responseText));
		}
	});
}
</script>