<script>
$(document).ready(function() {
	$('[data-table]').off('change',saveField).change(saveField).focus(unsaved).blur(unsaved);
	$('.panel-heading.mobile_load').click(loadPanel);
	$('.search_list').off('keyup',search_project).keyup(search_project);
});
var today_date = '<?= date('Y-m-d') ?>';
var current_user = '<?= get_contact($dbc, $_SESSION['contactid']) ?>';
var salesid = '<?= $_GET['salesid'] ?>';
function search_project() {
	var key = $('.search_list').val();
	if(key == '') {
		$('.scale-to-fill .main-screen.search_results').hide();
		$('.scale-to-fill .main-screen.default_screen').show();
	} else {
		$('.scale-to-fill .main-screen.search_results').html('<h2><em>Loading results for '+key+'...</em></h2>').show();
		$('.scale-to-fill .main-screen.default_screen').hide();
		$.ajax({
			url: 'edit_project_search.php',
			method: 'POST',
			data: { key: key, project: '<?= $projectid ?>', url: '<?= WEBSITE_URL.$_SERVER['REQUEST_URI'] ?>' },
			success: function(response) {
				if(key == $('.search_list').val()) {
					$('.scale-to-fill .main-screen.search_results').html(response);
				}
			}
		});
	}
}
</script>
<?php
$security = get_security($dbc, $tile);
$strict_view = strictview_visible_function($dbc, 'project');
if($strict_view > 0) {
	$security['edit'] = 0;
	$security['config'] = 0;
}
$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
if(!empty(MATCH_CONTACTS) && !in_array($project['businessid'],explode(',',MATCH_CONTACTS)) && !in_array_any(array_filter(explode(',',$project['clientid'])),explode(',',MATCH_CONTACTS))) {
	ob_clean();
	header('Location: projects.php');
	exit();
}
$projecttype = $project['projecttype'];
$base_config = array_filter(array_unique(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype' UNION
	SELECT `config_fields`  FROM `field_config_project` WHERE `fieldconfigprojectid` IN (SELECT MAX(`fieldconfigprojectid`) FROM `field_config_project` WHERE `type` IN ('".preg_replace('/[^a-z_,\']/','',str_replace(' ','_',str_replace(',',"','",strtolower(get_config($dbc,'project_tabs')))))."'))"))[0])));
$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
if(count($value_config) == 0) {
	$value_config = explode(',','Information Contact Region,Information Contact Location,Information Contact Classification,Information Business,Information Contact,Information Rate Card,Information Project Type,Information Project Short Name,Details Detail,Dates Project Created Date,Dates Project Start Date,Dates Estimate Completion Date,Dates Effective Date,Dates Time Clock Start Date');
}
$current_tab = '';
$project_counts = [];
$query_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(IF(`type` NOT LIKE 'detail_%',1,0)) notes, SUM(IF(`type` NOT LIKE 'detail_%',0,1)) details FROM `project_comment` WHERE `projectid`='$projectid' AND '$projectid' > 0"));
$project_counts['notes'] = intval($query_count['notes']);
$project_counts['details'] = intval($query_count['details']);
$query_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project_detail` WHERE `projectid`='$projectid' AND '$projectid' > 0"));
foreach($query_count as $query_label => $query_value) {
	if(!in_array($query_label,['detailid','projectid']) && $query_value != '') {
		$project_counts['details']++;
	}
}
$project_counts['documents'] = intval(mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) docs FROM `project_document` WHERE `projectid`='$projectid' AND '$projectid' > 0 AND `deleted` = 0"))['docs']);
$query_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `project_path`,`external_path`,`project_lead`,`project_colead`,`project_team` FROM `project` WHERE `projectid`='$projectid' AND '$projectid' > 0 AND `deleted`=0"));
$project_counts['paths'] = count(array_filter(explode(',',$query_count['project_path'].','.$query_count['external_path'])));
$project_counts['staff'] = count(array_filter(explode(',',$query_count['project_lead'].','.$query_count['project_colead'].','.$query_count['project_team'])));
if(!IFRAME_PAGE || $_GET['iframe_slider'] == 1) { ?>
	<?php if($_GET['iframe_slider'] == 1) { ?>
		<h1><?= $label ?></h1>
	<?php } ?>
	<div id='project_accordions' class='sidebar <?= $_GET['iframe_slider'] == 1 ? '' : 'show-on-mob' ?> panel-group block-panels col-xs-12 form-horizontal'>
		<?php echo '<span class="pull-right">';
		include('quick_actions.php');
		echo '</span><span class="clearfix"></span>'; ?>
		<div class="panel panel-default" style='<?= in_array('Summary',$tab_config) && $projectid > 0 ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_info">
						<?= PROJECT_NOUN ?> Summary<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_info" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_summary.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>">
					Loading...
				</div>
			</div>
		</div>
		<?php if($security['edit'] > 0) {
			foreach(array_filter(explode('#*#',mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`milestone` SEPARATOR '#*#') milestones FROM `project_path_milestone` WHERE `project_path_milestone` IN (".($project['project_path'] == '' ? '0' : $project['project_path']).")"))['milestones'])) as $path_tab) {
				$tab_id = 'path_'.preg_replace('/[^a-z]/','',strtolower($path_tab));
				$tab_count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM (SELECT `ticketid` FROM `tickets` WHERE `projectid`='$projectid' AND `milestone_timeline`='$path_tab' AND `status` NOT IN ('Archive','Archived','Done') AND `deleted`=0 UNION SELECT `tasklistid` FROM `tasklist` WHERE `projectid`='$projectid' AND `project_milestone`='$path_tab' AND `status` != 'Done' AND `deleted`=0) `list`"))['count']; ?>
				<div class="panel panel-default" style='<?= $_GET['edit'] > 0 && in_array('Path',$tab_config) ? '' : 'display:none;' ?>'>
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_path_<?= $tab_id ?>">
								<?= PROJECT_NOUN ?> Path: <?= $path_tab ?><span class="pull-right"><?= $tab_count ?></span><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_path_<?= $tab_id ?>" class="panel-collapse collapse">
						<div class="panel-body" data-file-name="edit_project_path.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>&tab=<?= $tab_id ?>">
							Loading...
						</div>
					</div>
				</div>
			<?php }
			foreach(array_filter(explode('#*#',mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`milestone` SEPARATOR '#*#') milestones FROM `project_path_milestone` WHERE `project_path_milestone` IN (".($project['external_path'] == '' ? '0' : $project['external_path']).")"))['milestones'])) as $path_tab) {
				$tab_id = 'path_external_path_'.preg_replace('/[^a-z]/','',strtolower($path_tab)); ?>
				<div class="panel panel-default" style='<?= $_GET['edit'] > 0 && in_array('Path',$tab_config) ? '' : 'display:none;' ?>'>
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_<?= $tab_id ?>">
								External <?= PROJECT_NOUN ?> Path: <?= $path_tab ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_<?= $tab_id ?>" class="panel-collapse collapse">
						<div class="panel-body" data-file-name="edit_project_path.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>&tab=<?= $tab_id ?>">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php $user_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'project_path' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
			foreach($user_forms as $user_form) { ?>
				<div class="panel panel-default" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>'>
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_form_<?= $user_form['id'] ?>">
								<?= $user_form['subtab_name'] ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_form_<?= $user_form['id'] ?>" class="panel-collapse collapse">
						<div class="panel-body" data-file-name="edit_project_user_form.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&project_form_id=<?= $user_form['id'] ?>">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<div class="panel panel-default" style='<?= in_array('Information',$tab_config) ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_info">
							<?= PROJECT_NOUN ?> Information<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_info" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_info.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default" style='<?= in_array('Estimate Info',$tab_config) ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_est_info">
							<?= ESTIMATE_TILE ?> Information<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_est_info" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_estimate.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default" style='<?= in_array('Staff',$tab_config) ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_info">
							<?= PROJECT_NOUN ?> Staff<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_info" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_staff.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default" style='<?= in_array('Details Path',$tab_config) ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_info">
							<?= PROJECT_NOUN ?> Path Templates<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_info" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_path_templates.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default" style='<?= in_array('Details',$tab_config) ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_details">
							<?= PROJECT_NOUN ?> Details<span class="glyphicon glyphicon-plus"></span><span class="pull-right"><?= $project_counts['details'] ?></span>
						</a>
					</h4>
				</div>

				<div id="collapse_details" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_details.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default" style='<?= in_array('Notes',$tab_config) ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_notes">
							<?= PROJECT_NOUN ?> Notes<span class="glyphicon glyphicon-plus"></span><span class="pull-right"><?= $project_counts['notes'] ?></span>
						</a>
					</h4>
				</div>

				<div id="collapse_notes" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_notes.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default" style='<?= in_array('Notes',$tab_config) ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_notes">
							<?= PROJECT_NOUN ?> Notes<span class="glyphicon glyphicon-plus"></span><span class="pull-right"><?= $project_counts['notes'] ?></span>
						</a>
					</h4>
				</div>

				<div id="collapse_notes" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_notes.php?projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default" style='<?= in_array('Documents',$tab_config) ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_docs">
							<?= PROJECT_NOUN ?> Documents<span class="glyphicon glyphicon-plus"></span><span class="pull-right"><?= $project_counts['documents'] ?></span>
						</a>
					</h4>
				</div>

				<div id="collapse_docs" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_documents.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
						Loading...
					</div>
				</div>
			</div>
			<div class="panel panel-default" style='<?= in_array('Dates',$tab_config) ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_dates">
							<?= PROJECT_NOUN ?> Dates<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_dates" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_dates.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
						Loading...
					</div>
				</div>
			</div>
			<?php $user_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'project_details' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
			foreach($user_forms as $user_form) { ?>
				<div class="panel panel-default" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>'>
					<div class="panel-heading mobile_load">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_form_<?= $user_form['id'] ?>">
								<?= $user_form['subtab_name'] ?><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_form_<?= $user_form['id'] ?>" class="panel-collapse collapse">
						<div class="panel-body" data-file-name="edit_project_user_form.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&project_form_id=<?= $user_form['id'] ?>">
							Loading...
						</div>
					</div>
				</div>
			<?php } ?>
			<?php $all_custom_details = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT(`tab`) FROM `field_config_project_custom_details` WHERE (`type` = 'ALL' OR `type` = '$projecttype') ORDER BY `fieldconfigid` ASC"),MYSQLI_ASSOC);
			$config_i = 0;
			foreach($all_custom_details as $custom_tab) {
				if(check_subtab_persmission($dbc, 'project', ROLE, 'custom_'.config_safe_str($custom_tab))) { ?>
					<div class="panel panel-default" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>'>
						<div class="panel-heading mobile_load">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_custom_<?= config_safe_str($custom_tab['tab']) ?>">
									<?= $custom_tab['tab'] ?><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_custom_<?= config_safe_str($custom_tab['tab']) ?>" class="panel-collapse collapse">
							<div class="panel-body" data-file-name="edit_project_custom_details.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&custom_tab=<?= $custom_tab['tab'] ?>">
								Loading...
							</div>
						</div>
					</div>
				<?php }
			} ?>
		<?php } ?>
		<div class="panel panel-default" style='<?= in_array('Scope',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_scope">
						<?= PROJECT_NOUN ?> Scope<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_scope" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_scope.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Sales Orders',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_sales_orders">
						<?= SALES_ORDER_TILE ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_sales_orders" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_scope_sales_orders.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Intake',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_intake">
						Intake Forms<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_intake" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_scope_intake.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Info Gathering',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_info_gather">
						Information Gathering<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_info_gather" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_scope_info_gathering.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Incident Reports',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_incident_reports">
						<?= INC_REP_TILE ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_incident_reports" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_scope_incident_reports.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Time Sheets',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_time_sheets">
						Time Sheets<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_time_sheets" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_scope_time_sheets.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<?php $user_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'scope_of_work' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms as $user_form) { ?>
			<div class="panel panel-default" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_form_<?= $user_form['id'] ?>">
							<?= $user_form['subtab_name'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_form_<?= $user_form['id'] ?>" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_user_form.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&project_form_id=<?= $user_form['id'] ?>">
						Loading...
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="panel panel-default" style='<?= in_array('Tickets',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_tickets">
						<?= TICKET_TILE ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_tickets" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_scope_tickets.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Work Orders',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_workorders">
						Work Orders<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_workorders" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_scope_workorders.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Checklists',$tab_config) || in_array('Tasks',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_tasks">
						<?= TASK_TILE ?><span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_tasks" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_scope_checklists.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= (in_array('Checklists',$tab_config) || in_array('Tasks',$tab_config)) && tile_visible($dbc,'checklist') ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_checklist">
						Checklists<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_checklist" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_scope_checklists.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Report Action Items',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_action_status_report">
						Action Item Status Report<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_action_status_report" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_scope_status_report.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Time Clock',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_time_clock">
						Time Clock<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_time_clock" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_scope_time.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<?php $user_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'action_item' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms as $user_form) { ?>
			<div class="panel panel-default" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_form_<?= $user_form['id'] ?>">
							<?= $user_form['subtab_name'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_form_<?= $user_form['id'] ?>" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_user_form.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&project_form_id=<?= $user_form['id'] ?>">
						Loading...
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="panel panel-default" style='<?= in_array('Administration',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_administration">
						Administration<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_administration" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="project_administration.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>">
					Loading...
				</div>
			</div>
		</div>
		<?php $user_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'administration' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms as $user_form) { ?>
			<div class="panel panel-default" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_form_<?= $user_form['id'] ?>">
							<?= $user_form['subtab_name'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_form_<?= $user_form['id'] ?>" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_user_form.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&project_form_id=<?= $user_form['id'] ?>">
						Loading...
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="panel panel-default" style='<?= in_array('Email',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_email">
						Email Communications<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_email" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_comm_email.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Phone',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_phone">
						Phone Communications<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_phone" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_comm_phone.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Agendas',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_agenda">
						Agendas<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_agenda" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_comm_agendas.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Meetings',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_meeting">
						Meetings<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_meeting" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_comm_meetings.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Meetings',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_comm_log">
						Communication History<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_comm_log" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_comm_log.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<?php $user_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'communications' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms as $user_form) { ?>
			<div class="panel panel-default" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_form_<?= $user_form['id'] ?>">
							<?= $user_form['subtab_name'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_form_<?= $user_form['id'] ?>" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_user_form.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&project_form_id=<?= $user_form['id'] ?>">
						Loading...
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="panel panel-default" style='<?= in_array('Timesheets',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_timesheet">
						Time Sheets<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_timesheet" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_acct_timesheet.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Payroll',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_payroll">
						Payroll<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_payroll" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_acct_payroll.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Expenses',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_payable">
						Expenses<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_payable" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_acct_expenses.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Payables',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_payable">
						Accounts Payable<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_payable" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_acct_payables.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<?php $user_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'accounting' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms as $user_form) { ?>
			<div class="panel panel-default" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_form_<?= $user_form['id'] ?>">
							<?= $user_form['subtab_name'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_form_<?= $user_form['id'] ?>" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_user_form.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&project_form_id=<?= $user_form['id'] ?>">
						Loading...
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="panel panel-default" style='<?= in_array('Deliverables',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_deliverables">
						Deliverables Report<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_deliverables" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_report_deliverables.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Gantt',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_gantt">
						Gantt Chart<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_gantt" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_report_gantt.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Profit Loss',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_profit">
						Profit &amp; Loss<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_profit" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_report_profit_loss.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Reminders',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_reminder">
						Reminders<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_reminder" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_comm_reminders.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Estimated Time',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_estimate_time">
						Estimated Time<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_estimate_time" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_report_estimated.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Tracked Time',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_tracked">
						Tracked Time<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_tracked" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_report_tracked.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Time Tracked',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_tracked">
						Total Time Tracked<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_tracked" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_report_time.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<?php $user_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'reporting' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms as $user_form) { ?>
			<div class="panel panel-default" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_form_<?= $user_form['id'] ?>">
							<?= $user_form['subtab_name'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_form_<?= $user_form['id'] ?>" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_user_form.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&project_form_id=<?= $user_form['id'] ?>">
						Loading...
					</div>
				</div>
			</div>
		<?php } ?>
		<?php if($security['edit'] > 0) { ?>
			<div class="panel panel-default" style='<?= in_array('Billing New',$tab_config) ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_new_bill">
							Create New Billing<span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_new_bill" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_billing_new.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
						Loading...
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="panel panel-default" style='<?= in_array('Payment Schedule',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_payment_schedule">
						Payment Schedule<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_payment_schedule" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_billing_invoices.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>&tab=payment_schedule">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Field Service Tickets',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_field_service_ticket">
						Field Service Tickets<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_field_service_ticket" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_billing_invoices.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>&tab=field_service_ticket">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Purchase Orders',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_purchase_orders">
						Purchase Orders<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_purchase_orders" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_invoices.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>&tab=purchase_order">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Work Tickets',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_work_tickets">
						Work Tickets<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_work_tickets" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_billing_invoices.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>&tab=work_ticket">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Invoices',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_invoices">
						Invoices<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_invoices" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_billing_invoices.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>&tab=invoice">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Outstanding',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_outstanding">
						Outstanding<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_outstanding" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_billing_invoices.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>&tab=outstanding">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Paid',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_paid">
						Paid<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_paid" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_billing_invoices.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>&tab=paid">
					Loading...
				</div>
			</div>
		</div>
		<div class="panel panel-default" style='<?= in_array('Invoice Reminders',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_recurring">
						Invoice Reminders<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_recurring" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_billing_reminders.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
		<?php $user_forms = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'billing' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms as $user_form) { ?>
			<div class="panel panel-default" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>'>
				<div class="panel-heading mobile_load">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_form_<?= $user_form['id'] ?>">
							<?= $user_form['subtab_name'] ?><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_form_<?= $user_form['id'] ?>" class="panel-collapse collapse">
					<div class="panel-body" data-file-name="edit_project_user_form.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&project_form_id=<?= $user_form['id'] ?>">
						Loading...
					</div>
				</div>
			</div>
		<?php } ?>
		<div class="panel panel-default" style='<?= in_array('History',$tab_config) ? '' : 'display:none;' ?>'>
			<div class="panel-heading mobile_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#project_accordions" href="#collapse_history">
						Activity History<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_history" class="panel-collapse collapse">
				<div class="panel-body" data-file-name="edit_project_report_history.php?tile_name=<?= $_GET['tile_name'] ?>&projectid=<?= $projectid ?>&projecttype=<?= $projecttype ?>">
					Loading...
				</div>
			</div>
		</div>
	</div>
<?php } ?>
<?php if(!IFRAME_PAGE) { ?>
<div class="standard-collapsible hide-titles-mob sidebar tile-sidebar sidebar-override inherit-height double-gap-top">
	<ul>
		<?php if($security['search'] > 0) { ?>
			<li class="standard-sidebar-searchbox"><input type="text" class="form-control search_list" placeholder="Search <?= get_project_label($dbc, $project) ?>"></li>
		<?php } ?>
		<?php $no_sub_shown = ($_GET['tab'] == '');
		$next_tab = '';
		$prev_set = false;
		$previous_tab = '';
		$next_set = false;
		$ticket_bypass = true;
		$sub_tabs = ['Summary', 'Project', 'Tickets', 'Tasks', 'Checklists', 'Projections'];
		foreach($sub_tabs as $i => $tab) {
			if(!in_array($tab,$tab_config)) {
				unset($sub_tabs[$i]);
			}
		}
		if(count($sub_tabs) > 0 && $projectid > 0) {
			$ticket_bypass = false;
			$show_sub = in_array($_GET['tab'],['summary', 'summary_project', 'summary_tickets', 'summary_tasks', 'summary_checklists', 'summary_projections']) || $no_sub_shown;
			$no_sub_shown = false;
            $_GET['tab'] = ($_GET['tab'] == '' ? 'summary' : $_GET['tab']); ?>
			<a href="?edit=<?= $_GET['edit'] ?>&tab=summary" onclick="$('.standard-collapsible ul ul:visible').not($(this).next('ul')).toggle().prev('a').find('li').toggleClass('collapsed'); $(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;" style="<?= count($sub_tabs) > 0 ? '' : 'display:none;' ?>">
				<li class="sidebar-higher-level <?= $show_sub ? 'active blue' : 'collapsed' ?>">Summary<span class="arrow"></span></li></a>
			<ul id="ul_summ" style="<?= $show_sub ? (count($sub_tabs) > 0 ? '' : 'padding-left:0;') : 'display: none;' ?>">
				<a href="?edit=<?= $_GET['edit'] ?>&tab=summary"><li class="sidebar-lower-level <?= $_GET['tab'] == 'summary' ? 'active blue' : '' ?>"><?php echo PROJECT_NOUN ?></li></a>
				<?php if(in_array('Summary',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'summary_tickets' : $_GET['tab']); $next_tab = (!$next_set ? 'summary_tickets' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'summary_tickets' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'summary_tickets'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=summary_tickets"><li class="sidebar-lower-level <?= $_GET['tab'] == 'summary_tickets' ? 'active blue' : '' ?>">Tickets</li></a><?php } ?>
				<?php if(in_array('Summary',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'summary_tasks' : $_GET['tab']); $next_tab = (!$next_set ? 'summary_tasks' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'summary_tasks' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'summary_tasks'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=summary_tasks"><li class="sidebar-lower-level <?= $_GET['tab'] == 'summary_tasks' ? 'active blue' : '' ?>">Tasks</li></a><?php } ?>
				<?php if(in_array('Summary',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'summary_checklists' : $_GET['tab']); $next_tab = (!$next_set ? 'summary_checklists' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'summary_checklists' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'summary_checklists'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=summary_checklists"><li class="sidebar-lower-level <?= $_GET['tab'] == 'summary_checklists' ? 'active blue' : '' ?>">Checklists</li></a><?php } ?>
				<?php if(in_array('Summary',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'summary_projections' : $_GET['tab']); $next_tab = (!$next_set ? 'summary_projections' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'summary_projections' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'summary_projections'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=summary_projections"><li class="sidebar-lower-level <?= $_GET['tab'] == 'summary_projections' ? 'active blue' : '' ?>">Projections</li></a><?php } ?>
			</ul>
		<?php }
		if($security['edit'] > 0) {
			$user_forms = [];
			$user_forms_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'project_path' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
			foreach($user_forms_sql as $user_form_sql) {
				$user_forms[$user_form_sql['id']] = $user_form_sql['subtab_name'];
			}
			if(check_subtab_persmission($dbc, 'project', ROLE, 'view_path') && (in_array_any(['Path','Scrum Board','External Path'],$tab_config) || count($user_forms) > 0)) {
				$ticket_bypass = false;
				$sub_tabs = [];
				$show_sub = ($_GET['tab'] == '' || in_array($_GET['tab'],['path']) || $no_sub_shown || array_key_exists($_GET['project_form_id'], $user_forms));
				$no_sub_shown = false;
				if(in_array('Scrum Board',$tab_config) && $_GET['edit'] > 0) {
                    $status_list = array_unique(array_merge(explode(',',get_config($dbc, 'ticket_status')),explode(',',get_config($dbc, 'task_status'))));
                    $tab_count = $dbc->query("SELECT COUNT(*) `count` FROM (SELECT `ticketid` FROM `tickets` WHERE `deleted`=0 AND `projectid`='$projectid' AND `status` IN ('".implode("','",$status_list)."') UNION SELECT `tasklistid` FROM `tasklist` WHERE `projectid`='$projectid' AND `status` IN ('".implode("','",$status_list)."') AND `deleted`=0) `items`")->fetch_assoc()['count'];
                    $sub_tabs[] = [PROJECT_NOUN.' Scrum Board<span class="pull-right">'.$tab_count.'</span>','path'];
					$next_tab = (!$next_set ? 'path' : $next_tab);
					$next_set = ($prev_set ? true : false);
					$prev_set = ($_GET['tab'] == 'path' ? true : $prev_set);
					$previous_tab = ($prev_set ? $previous_tab : 'path');
					$_GET['tab'] = ($_GET['tab'] == '' ? $path_tab[1] : $_GET['tab']);
                    foreach($status_list as $i => $status) {
                        $tab_count = $dbc->query("SELECT COUNT(*) `count` FROM (SELECT `ticketid` FROM `tickets` WHERE `deleted`=0 AND `projectid`='$projectid' AND `status`='$status' UNION SELECT `tasklistid` FROM `tasklist` WHERE `projectid`='$projectid' AND `deleted`=0 AND `status`='$status') `items`")->fetch_assoc()['count'];
                        $tab_id = 'path&pathid=scrum|'.config_safe_str($status);
                        $sub_tabs[] = [$status.'<span class="pull-right">'.$tab_count.'</span>',$tab_id];
                        if($_GET['tab'].'&pathid='.$_GET['pathid'] == $tab_id) {
                            $show_sub = true;
                            $no_sub_shown = false;
                        }
                    }
				}
				if(in_array('Path',$tab_config) && $_GET['edit'] > 0) {
					foreach(explode(',',$project['project_path']) as $path_i => $projectpathid) {
						if($projectpathid > 0) {
							$path_count = 0;
							$path_label = explode('#*#',$project['project_path_name'])[$path_i];
							if(empty($path_label)) {
								$path_label = get_field_value('project_path','project_path_milestone','project_path_milestone',$projectpathid);
							}
							$path_tab_id = count($sub_tabs);
							$sub_tabs[] = [$path_label,'path&pathid=I|'.$projectpathid];
							$next_tab = (!$next_set ? 'path' : $next_tab);
							$next_set = ($prev_set ? true : false);
							$prev_set = ($_GET['tab'] == 'path' ? true : $prev_set);
							$previous_tab = ($prev_set ? $previous_tab : 'path');
							$_GET['pathid'] = ($_GET['tab'] == '' ? 'I|'.$projectpathid : $_GET['pathid']);
							$_GET['tab'] = ($_GET['tab'] == '' ? 'path' : $_GET['tab']);

							if($_GET['pathid'] == 'I|'.$projectpathid) {
								$pathid = explode('|',filter_var($_GET['pathid'],FILTER_SANITIZE_STRING))[1];
							}
							if($projectpathid > 0) {
								$milestones = explode('#*#',get_field_value('milestone','project_path_milestone','project_path_milestone',$projectpathid));
								$prior_sort = 0;
								foreach($milestones as $i => $milestone) {
									$milestone_rows = $dbc->query("SELECT `sort` FROM `project_path_custom_milestones` WHERE `projectid`='$projectid' AND `milestone`='$milestone' AND `pathid`='$projectpathid' AND `path_type`='I'");
									if($milestone_rows->num_rows > 0) {
										$prior_sort = $milestone_rows->fetch_assoc()['sort'];
									} else if($milestone != 'Unassigned') {
										$dbc->query("INSERT INTO `project_path_custom_milestones` (`projectid`,`milestone`,`label`,`path_type`,`pathid`,`sort`) VALUES ('$projectid','$milestone','$milestone','I','$projectpathid','$prior_sort')");
									}
								}
							}

							$path_milestones = $dbc->query("SELECT `milestones`.`milestone`,`milestones`.`label`,SUM(IF(`milestones`.`path_type`='E' AND `milestones`.`milestone`=`list`.`external`,1,IF(`milestones`.`path_type`='I' AND `milestones`.`milestone`=`list`.`milestone`,1,0))) `count` FROM `project_path_custom_milestones` `milestones` LEFT JOIN (SELECT `ticketid` `id`, '' `external`, `milestone_timeline` `milestone`, `projectid` FROM `tickets` WHERE `status` NOT IN ('Done','Archive','Archived') AND `deleted`=0 UNION SELECT `tasklistid` `id`, `external`, `project_milestone` `milestone`, `projectid` FROM `tasklist` WHERE archived_date IS NULL AND `deleted`=0) `list` ON `milestones`.`projectid`=`list`.`projectid` WHERE `milestones`.`projectid`='$projectid' AND `milestones`.`pathid`='$projectpathid' AND `milestones`.`path_type`='I' AND `deleted`=0 GROUP BY `milestones`.`projectid`, `milestones`.`milestone`, `milestones`.`pathid`, `milestones`.`path_type` ORDER BY `milestones`.`sort`, `milestones`.`id`");
							while($path_milestone = $path_milestones->fetch_assoc()) {
								if($path_milestone['milestone'] != '') {
									$tab_count = $path_milestone['count'];
									$tab_id = ($path_milestone['path_type'] == 'E' ? 'path_external_path_' : 'path_').config_safe_str($path_milestone['milestone']).'&pathid=I|'.$projectpathid;
									$sub_tabs[] = [($path_milestone['path_type'] == 'E' ? 'External: ' : '').$path_milestone['label'].'<span class="pull-right">'.$tab_count.'</span>',$tab_id];
									$path_count += $tab_count;
									if($_GET['tab'].'&pathid=I|'.$projectpathid == $tab_id) {
										$show_sub = true;
										$no_sub_shown = false;
									}
								}
							}
							$sub_tabs[$path_tab_id][0] .= '<span class="pull-right">'.$path_count.'</span>';
						}
					}
				}
				if(in_array('External Path',$tab_config) && $_GET['edit'] > 0) {
					foreach(explode(',',$project['external_path']) as $projectpathid) {
						if($projectpathid > 0) {
							$path_count = 0;
							$path_tab_id = count($sub_tabs);
							$sub_tabs[] = ['External: '.get_field_value('project_path','project_path_milestone','project_path_milestone',$projectpathid),'path&pathid=E|'.$projectpathid];
							$next_tab = (!$next_set ? 'path' : $next_tab);
							$next_set = ($prev_set ? true : false);
							$prev_set = ($_GET['tab'] == 'path' ? true : $prev_set);
							$previous_tab = ($prev_set ? $previous_tab : 'path');
							$_GET['pathid'] = ($_GET['tab'] == '' ? 'E|'.$projectpathid : $_GET['pathid']);
							$_GET['tab'] = ($_GET['tab'] == '' ? 'path' : $_GET['tab']);

							if($_GET['pathid'] == 'E|'.$projectpathid) {
								$pathid = explode('|',filter_var($_GET['pathid'],FILTER_SANITIZE_STRING))[1];
							}
							if($projectpathid > 0) {
								$milestones = explode('#*#',get_field_value('milestone','project_path_milestone','project_path_milestone',$projectpathid));
								$prior_sort = 0;
								foreach($milestones as $i => $milestone) {
									$milestone_rows = $dbc->query("SELECT `sort` FROM `project_path_custom_milestones` WHERE `projectid`='$projectid' AND `milestone`='$milestone' AND `pathid`='$projectpathid' AND `path_type`='E' AND `deleted`=0");
									if($milestone_rows->num_rows > 0) {
										$prior_sort = $milestone_rows->fetch_assoc()['sort'];
									} else if($milestone != 'Unassigned') {
										$dbc->query("INSERT INTO `project_path_custom_milestones` (`projectid`,`milestone`,`label`,`path_type`,`pathid`,`sort`) VALUES ('$projectid','$milestone','$milestone','E','$projectpathid','$prior_sort')");
									}
								}
							}
							$path_milestones = $dbc->query("SELECT `milestones`.`milestone`,`milestones`.`label`,SUM(IF(`milestones`.`path_type`='E' AND `milestones`.`milestone`=`list`.`external`,1,IF(`milestones`.`path_type`='I' AND `milestones`.`milestone`=`list`.`milestone`,1,0))) `count` FROM `project_path_custom_milestones` `milestones` LEFT JOIN (SELECT `ticketid` `id`, '' `external`, `milestone_timeline` `milestone`, `projectid` FROM `tickets` WHERE `status` NOT IN ('Done','Archive','Archived') AND `deleted`=0 UNION SELECT `tasklistid` `id`, `external`, `project_milestone` `milestone`, `projectid` FROM `tasklist` WHERE `status` != 'Done' AND `deleted`=0) `list` ON `milestones`.`projectid`=`list`.`projectid` WHERE `milestones`.`projectid`='$projectid' AND `milestones`.`pathid`='$projectpathid' AND `milestones`.`path_type`='E' AND `deleted`=0 GROUP BY `milestones`.`projectid`, `milestones`.`milestone`, `milestones`.`pathid`, `milestones`.`path_type` ORDER BY `milestones`.`sort`, `milestones`.`id`");
							while($path_milestone = $path_milestones->fetch_assoc()) {
								if($path_milestone['milestone'] != '') {
									$tab_count = $path_milestone['count'];
									$tab_id = ($path_milestone['path_type'] == 'E' ? 'path_external_path_' : 'path_').preg_replace('/[^a-z]/','',strtolower($path_milestone['milestone'])).'&pathid=E|'.$projectpathid;
									$sub_tabs[] = [($path_milestone['path_type'] == 'E' ? 'External: ' : '').$path_milestone['label'].'<span class="pull-right">'.$tab_count.'</span>',$tab_id];
									$path_count += $tab_count;
									if($_GET['tab'].'&pathid=E|'.$projectpathid == $tab_id) {
										$show_sub = true;
										$no_sub_shown = false;
									}
								}
							}
							$sub_tabs[$path_tab_id][0] .= '<span class="pull-right">'.$path_count.'</span>';
						}
					}
				}
				if((count($sub_tabs) > 0 || count($user_forms) > 0) && $_GET['edit'] > 0) { ?>
					<a href="?edit=<?= $_GET['edit'] ?>&tab=info" onclick="$('.standard-collapsible ul ul:visible').not($(this).next('ul')).toggle().prev('a').find('li').toggleClass('collapsed'); $(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;" style="<?= (count($sub_tabs) > 0 || count($user_forms) > 0) ? '' : 'display:none;' ?>">
						<li class="sidebar-higher-level <?= $show_sub || $_GET['edit'] == 0 ? 'active blue' : 'collapsed' ?>"><?= PROJECT_NOUN ?> Path<span class="arrow"></span></li></a>
					<ul style="<?= $show_sub || $_GET['edit'] == 0 ? (count($sub_tabs) > 0 ? '' : 'padding-left:0;') : 'display: none;' ?>">
					<?php foreach($sub_tabs as $path_tab) { ?>
						<?php $_GET['tab'] = ($_GET['tab'] == '' ? $path_tab[1] : $_GET['tab']); $next_tab = (!$next_set ? $path_tab[1] : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'].'&pathid='.$_GET['pathid'] == $path_tab[1] ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : $path_tab[1]); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=<?= $path_tab[1] ?>"><li class="sidebar-lower-level <?= $path_tab[1] ?> <?= ($path_tab[1] == $_GET['tab'] && empty($_GET['pathid'])) || $path_tab[1] == $_GET['tab'].'&pathid='.$_GET['pathid'] ? 'active blue' : '' ?>" style="<?= in_array(explode('&',$path_tab[1])[0],['path','path_external_path','scrum_board']) && explode('|',explode('&',$path_tab[1])[1])[0] != 'pathid=scrum' ? '' : 'padding-left: 50px;' ?>"><?= $path_tab[0] ?></li></a>
					<?php }
					if(count($user_forms) > 0 && $_GET['edit'] > 0) {
						foreach($user_forms as $project_form_id => $subtab_name) { ?>
							<a href="?edit=<?= $_GET['edit'] ?>&tab=user_forms&project_form_id=<?= $project_form_id ?>"><li class="sidebar-lower-level <?= $_GET['project_form_id'] == $project_form_id ? 'active blue' : '' ?>"><?= $subtab_name ?></li></a>
						<?php }
					} ?>
					</ul>
				<?php }
			}

            if(check_subtab_persmission($dbc, 'project', ROLE, 'view_projection') && (count($sub_tabs) > 0 || count($user_forms) > 0)) {
			$ticket_bypass = false;
			$show_sub = in_array($_GET['tab'],['services','products','ptasks','inventory','admin']) || $no_sub_shown || array_key_exists($_GET['project_form_id'], $user_forms);
			$no_sub_shown = false; ?>
			<a href="?edit=<?= $_GET['edit'] ?>&tab=services" onclick="$('.standard-collapsible ul ul:visible').not($(this).next('ul')).toggle().prev('a').find('li').toggleClass('collapsed'); $(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;" style="<?= count($sub_tabs) > 0 ? '' : 'display:none;' ?>">
				<li class="sidebar-higher-level <?= $show_sub ? 'active blue' : 'collapsed' ?>">Projections<span class="arrow"></span></li></a>
			<ul id="ul_projection" style="<?= $show_sub ? (count($sub_tabs) > 0 ? '' : 'padding-left:0;') : 'display: none;' ?>">
				<?php $_GET['tab'] = ($_GET['tab'] == '' ? 'services' : $_GET['tab']); $next_tab = (!$next_set ? 'services' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'services' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'services'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=services"><li class="sidebar-lower-level <?= $_GET['tab'] == 'services' ? 'active blue' : '' ?>">Services</li></a>
				<?php $_GET['tab'] = ($_GET['tab'] == '' ? 'products' : $_GET['tab']); $next_tab = (!$next_set ? 'products' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'products' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'products'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=products"><li class="sidebar-lower-level <?= $_GET['tab'] == 'products' ? 'active blue' : '' ?>">Products</li></a>

				<?php $_GET['tab'] = ($_GET['tab'] == '' ? 'ptasks' : $_GET['tab']); $next_tab = (!$next_set ? 'ptasks' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'ptasks' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'ptasks'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=ptasks"><li class="sidebar-lower-level <?= $_GET['tab'] == 'ptasks' ? 'active blue' : '' ?>"><?= TASK_TILE ?></li></a>

				<?php $_GET['tab'] = ($_GET['tab'] == '' ? 'inventory' : $_GET['tab']); $next_tab = (!$next_set ? 'inventory' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'inventory' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'inventory'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=inventory"><li class="sidebar-lower-level <?= $_GET['tab'] == 'inventory' ? 'active blue' : '' ?>">Inventory</li></a>

				<?php $_GET['tab'] = ($_GET['tab'] == '' ? 'admin' : $_GET['tab']); $next_tab = (!$next_set ? 'admin' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'admin' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'admin'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=admin"><li class="sidebar-lower-level <?= $_GET['tab'] == 'admin' ? 'active blue' : '' ?>">Admin</li></a>

			</ul>
		<?php }

			$sub_tabs = ['Information','Estimate Info','Staff','Details Path','Notes','Details','Documents','Dates'];
			foreach($sub_tabs as $i => $tab) {
				if(!in_array($tab,$tab_config)) {
					unset($sub_tabs[$i]);
				}
			}
			$user_forms = [];
			$user_forms_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'project_details' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
			foreach($user_forms_sql as $user_form_sql) {
				$user_forms[$user_form_sql['id']] = $user_form_sql['subtab_name'];
			}
			if(check_subtab_persmission($dbc, 'project', ROLE, 'view_details') && (count($sub_tabs) > 0 || count($user_forms) > 0) || $_GET['edit'] == 0) {
				$ticket_bypass = false;
				$show_sub = in_array($_GET['tab'],['info','estimate','details_path','staff','details','notes','documents','dates']) || $no_sub_shown || empty($_GET['tab']) || array_key_exists($_GET['project_form_id'], $user_forms);
				$no_sub_shown = false; ?>
				<a href="?edit=<?= $_GET['edit'] ?>&tab=info" onclick="$('.standard-collapsible ul ul:visible').not($(this).next('ul')).toggle().prev('a').find('li').toggleClass('collapsed'); $(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;" style="<?= count($sub_tabs) > 0 ? '' : 'display:none;' ?>">
					<li class="sidebar-higher-level <?= $show_sub || $_GET['edit'] == 0 ? 'active blue' : 'collapsed' ?>"><?= PROJECT_NOUN ?> Details<span class="arrow"></span></li></a>
				<ul id="ul_details" style="<?= $show_sub || $_GET['edit'] == 0 ? (count($sub_tabs) > 0 ? '' : 'padding-left:0;') : 'display: none;' ?>">
					<?php if(in_array('Information',$tab_config) || $_GET['edit'] == 0) { $_GET['tab'] = ($_GET['tab'] == '' ? 'info' : $_GET['tab']); $next_tab = (!$next_set ? 'info' : $next_tab); $next_set = ($prev_set && !$show_sub ? true : false); $prev_set = ($_GET['tab'] == 'info' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'info'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=info"><li class="sidebar-lower-level <?= $_GET['tab'] == 'info' ? 'active blue' : '' ?>">Information</li></a><?php } ?>
					<?php if(mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) FROM `estimate` WHERE `projectid`='$projectid' AND `projectid` > 0 AND `deleted`=0"))[0] > 0) {
						$estimate_list = true; ?>
						<?php if(in_array('Estimate Info',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'estimate' : $_GET['tab']); $next_tab = (!$next_set ? 'estimate' : $next_tab); $next_set = ($prev_set && !$show_sub ? true : false); $prev_set = ($_GET['tab'] == 'estimate' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'estimate'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=estimate"><li class="sidebar-lower-level <?= $_GET['tab'] == 'estimate' ? 'active blue' : '' ?>">Estimate Details</li></a><?php } ?>
					<?php } ?>
					<?php if(in_array('Staff',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'path' : $_GET['tab']); $next_tab = (!$next_set ? 'staff' : $next_tab); $next_set = ($prev_set && !$show_sub ? true : false); $prev_set = ($_GET['tab'] == 'staff' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'staff'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=staff"><li class="sidebar-lower-level <?= $_GET['tab'] == 'staff' ? 'active blue' : '' ?>">Staff <span class="pull-right"><?= $project_counts['staff'] ?></span></li></a><?php } ?>
					<?php if(in_array('Details Path',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'details_path' : $_GET['tab']); $next_tab = (!$next_set ? 'details_path' : $next_tab); $next_set = ($prev_set && !$show_sub ? true : false); $prev_set = ($_GET['tab'] == 'details_path' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'details_path'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=details_path"><li class="sidebar-lower-level <?= $_GET['tab'] == 'details_path' ? 'active blue' : '' ?>">Path Template <span class="pull-right"><?= $project_counts['paths'] ?></span></li></a><?php } ?>
					<?php if(in_array('Notes',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'notes' : $_GET['tab']); $next_tab = (!$next_set ? 'notes' : $next_tab); $next_set = ($prev_set && !$show_sub ? true : false); $prev_set = ($_GET['tab'] == 'notes' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'notes'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=notes"><li class="sidebar-lower-level <?= $_GET['tab'] == 'notes' ? 'active blue' : '' ?>">Notes <span class="pull-right"><?= $project_counts['notes'] ?></span></li></a><?php } ?>
					<?php if(in_array('Details',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'details' : $_GET['tab']); $next_tab = (!$next_set ? 'details' : $next_tab); $next_set = ($prev_set && !$show_sub ? true : false); $prev_set = ($_GET['tab'] == 'details' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'details'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=details"><li class="sidebar-lower-level <?= $_GET['tab'] == 'details' ? 'active blue' : '' ?>">Details <span class="pull-right"><?= $project_counts['details'] ?></span></li></a><?php } ?>
					<?php if(in_array('Documents',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'documents' : $_GET['tab']); $next_tab = (!$next_set ? 'documents' : $next_tab); $next_set = ($prev_set && !$show_sub ? true : false); $prev_set = ($_GET['tab'] == 'documents' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'documents'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=documents"><li class="sidebar-lower-level <?= $_GET['tab'] == 'documents' ? 'active blue' : '' ?>">Documents <span class="pull-right"><?= $project_counts['documents'] ?></span></li></a><?php } ?>
					<?php if(in_array('Dates',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'dates' : $_GET['tab']); $next_tab = (!$next_set ? 'dates' : $next_tab); $next_set = ($prev_set && !$show_sub ? true : false); $prev_set = ($_GET['tab'] == 'dates' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'dates'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=dates"><li class="sidebar-lower-level <?= $_GET['tab'] == 'dates' ? 'active blue' : '' ?>">Dates</li></a><?php }
					if(count($user_forms) > 0 && $_GET['edit'] > 0) {
						foreach($user_forms as $project_form_id => $subtab_name) { ?>
							<a href="?edit=<?= $_GET['edit'] ?>&tab=user_forms&project_form_id=<?= $project_form_id ?>"><li class="sidebar-lower-level <?= $_GET['project_form_id'] == $project_form_id ? 'active blue' : '' ?>"><?= $subtab_name ?></li></a>
						<?php }
					} ?>
				</ul>
			<?php }
		}

		//CUSTOM DETAILS START
		$all_custom_details = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_custom_details` WHERE (`type` = 'ALL' OR `type` = '$projecttype') ORDER BY `fieldconfigid` ASC"),MYSQLI_ASSOC);
		$custom_details = [];
		foreach($all_custom_details as $custom_detail) {
			if(!in_array($custom_detail['heading'],$custom_details[$custom_detail['tab']])) {
				$custom_details[$custom_detail['tab']][] = $custom_detail['heading'];
			}
		}

		foreach($custom_details as $custom_tab => $custom_headings) {
			if(check_subtab_persmission($dbc, 'project', ROLE, 'custom_'.config_safe_str($custom_tab))) {
				$show_sub = ($_GET['tab'] == 'custom_details' && $_GET['custom_tab'] == $custom_tab) || $no_sub_shown;
				$no_sub_shown = false; ?>
				<a href="?edit=<?= $_GET['edit'] ?>&tab=custom_details&custom_tab=<?= $custom_tab ?>" onclick="$('.standard-collapsible ul ul:visible').not($(this).next('ul')).toggle().prev('a').find('li').toggleClass('collapsed'); $(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;">
					<li class="sidebar-higher-level <?= $show_sub ? 'active blue' : 'collapsed' ?>"><?= $custom_tab ?><span class="arrow"></span></li></a>
				<ul <?= $_GET['custom_tab'] == $custom_tab ? 'id="active_custom_tab"' : '' ?> style="<?= $show_sub ? 'padding-left:0;' : 'display:none;' ?>">
					<?php foreach($custom_headings as $custom_heading) { ?>
						<a data-configsafestr="<?= config_safe_str($custom_heading) ?>" href="?edit=<?= $_GET['edit'] ?>&tab=custom_details&custom_tab=<?= $custom_tab ?>&custom_heading=<?= $custom_heading ?>"><li class="sidebar-lower-level <?= $_GET['custom_heading'] == $custom_heading ? 'active blue' : '' ?>"><?= $custom_heading ?></li>
					<?php } ?>
				</ul>
			<?php }
		}
		//CUSTOM DETAILS END

		$sub_tabs = ['Scope','Scope Types','Estimates','Sales Orders','Intake','Info Gathering','Incident Reports','Time Sheets'];
		foreach($sub_tabs as $i => $tab) {
			if(!in_array($tab,$tab_config)) {
				unset($sub_tabs[$i]);
			}
		}
		$user_forms = [];
		$user_forms_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'scope_of_work' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms_sql as $user_form_sql) {
			$user_forms[$user_form_sql['id']] = $user_form_sql['subtab_name'];
		}
		if(check_subtab_persmission($dbc, 'project', ROLE, 'view_scope') && (count($sub_tabs) > 0 || count($user_forms) > 0)) {
			$ticket_bypass = false;
			$show_sub = in_array($_GET['tab'],['scope','scope_type','est_scope','sales_order','intake','info_gathering','incident_reports','time_sheets']) || $no_sub_shown || array_key_exists($_GET['project_form_id'], $user_forms);
			$no_sub_shown = false; ?>
			<a href="?edit=<?= $_GET['edit'] ?>&tab=scope" onclick="$('.standard-collapsible ul ul:visible').not($(this).next('ul')).toggle().prev('a').find('li').toggleClass('collapsed'); $(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;" style="<?= count($sub_tabs) > 0 ? '' : 'display:none;' ?>">
				<li class="sidebar-higher-level <?= $show_sub ? 'active blue' : 'collapsed' ?>">Scope of Work<span class="arrow"></span></li></a>
			<ul style="<?= $show_sub ? (count($sub_tabs) > 0 ? '' : 'padding-left:0;') : 'display: none;' ?>">
				<?php if(in_array('Scope',$tab_config)) {
                    $scope_items = $dbc->query("SELECT COUNT(*) `num` FROM `project_scope` WHERE `deleted`=0 AND `projectid`='$projectid'")->fetch_assoc();
                    $_GET['tab'] = ($_GET['tab'] == '' ? 'scope' : $_GET['tab']); $next_tab = (!$next_set ? 'scope' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'scope' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'scope'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=scope"><li class="sidebar-lower-level <?= $_GET['tab'] == 'scope' ? 'active blue' : '' ?>"><?= PROJECT_NOUN ?> Scope<span class="pull-right"><?= $scope_items['num'] ?></span></li></a>
                <?php } ?>
				<?php if(in_array('Scope Types',$tab_config)) {
					$scope_items = $dbc->query("SELECT src_table, COUNT(*) `num` FROM `project_scope` WHERE `deleted`=0 AND `projectid`='$projectid' AND `src_table` != '' GROUP BY `src_table` ORDER BY MIN(`sort_order`)");
					while($scope_type = $scope_items->fetch_assoc()) {
						$_GET['tab'] = ($_GET['tab'] == '' ? 'scope_type' : $_GET['tab']);
						switch($scope_type['src_table']) {
							case 'clients': $scope_label = 'Clients'; break;
							case 'equipment': $scope_label = 'Equipment'; break;
							case 'inventory': $scope_label = 'Inventory'; break;
							case 'labour': $scope_label = 'Labour'; break;
							case 'material': $scope_label = 'Material'; break;
							case 'position': $scope_label = 'Position'; break;
							case 'products': $scope_label = 'Products'; break;
							case 'services': $scope_label = 'Services'; break;
							case 'staff': $scope_label = 'Staff'; break;
							case 'vpl">Vendor Pricelist': $scope_label = 'Vendor Pricelist'; break;
							case 'miscellaneous': $scope_label = 'Miscellaneous'; break;
							default: $scope_label = $scope_type['src_table']; break;
						}
						$scope_tab_label = $_GET['scope_type'] == $scope_type['src_table'] ? $scope_label : $scope_tab_label;
						$next_tab = (!$next_set ? 'scope_type&scope_type='+$scope_type['src_table'] : $next_tab);
						$next_set = ($prev_set ? true : false);
						$prev_set = ($_GET['tab'] == 'scope_type&scope_type='+$scope_type['src_table'] ? true : $prev_set);
						$previous_tab = ($prev_set ? $previous_tab : 'scope_type&scope_type='+$scope_type['src_table']); ?>
						<a href="?edit=<?= $_GET['edit'] ?>&tab=scope_type&scope_type=<?= $scope_type['src_table'] ?>"><li class="sidebar-lower-level <?= $_GET['tab'] == 'scope_type' && $_GET['scope_type'] == $scope_type['src_table'] ? 'active blue' : '' ?>"><?= PROJECT_NOUN ?> Scope: <?= $scope_label ?><span class="pull-right"><?= $scope_type['num'] ?></span></li></a>
					<?php } ?>
				<?php } ?>
				<?php if(in_array('Estimates',$tab_config)) {
                    $estimates = mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `projectid`='$projectid' AND `projectid` > 0 AND `deleted`=0");
                    while($estimate = mysqli_fetch_array($estimates)) {
                        $scope_items = $dbc->query("SELECT COUNT(*) `num` FROM `estimate_scope` WHERE `deleted`=0 AND `estimateid`='".$estimate['estimateid']."'")->fetch_assoc(); ?>
                        <?php $_GET['tab'] = ($_GET['tab'] == '' ? 'est_scope' : $_GET['tab']); $next_tab = (!$next_set ? 'est_scope' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'est_scope' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'est_scope'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=est_scope&estimate=<?= $estimate['estimateid'] ?>"><li class="sidebar-lower-level <?= $_GET['tab'] == 'est_scope' && $_GET['estimate'] == $estimate['estimateid'] ? 'active blue' : '' ?>">Estimate: <?= $estimate['estimate_name'] ?><span class="pull-right"><?= $scope_items['num'] ?></span></li></a>
                    <?php } ?>
				<?php } ?>
				<?php $sales_orders = mysqli_query($dbc, "SELECT * FROM `sales_order` WHERE `projectid`='$projectid'");
				while($sales_order = mysqli_fetch_array($sales_orders)) { ?>
					<?php if(in_array('Sales Orders',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'sales_order' : $_GET['tab']); $next_tab = (!$next_set ? 'sales_order' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'sales_order' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'sales_order'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=sales_order&posid=<?= $sales_order['posid'] ?>"><li class="sidebar-lower-level <?= $_GET['tab'] == 'sales_order' && $_GET['posid'] == $sales_order['posid'] ? 'active blue' : '' ?>"><?= SALES_ORDER_NOUN ?>: <?= $sales_order['name'] ?><span class="pull-right"><?= $dbc->query("SELECT COUNT(*) `num` FROM `sales_order_product` WHERE `posid`='".$sales_order['posid']."'")->fetch_assoc()['num'] ?></span></li></a><?php } ?>
				<?php } ?>
				<?php if(in_array('Intake',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'intake' : $_GET['tab']); $next_tab = (!$next_set ? 'intake' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'intake' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'intake'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=intake"><li class="sidebar-lower-level <?= $_GET['tab'] == 'intake' ? 'active blue' : '' ?>">Intake Forms<span class="pull-right"><?= $dbc->query("SELECT COUNT(*) `num` FROM `intake` WHERE `deleted` = 0 AND `projectid` = '$projectid'")->fetch_assoc()['num'] ?></span></li></a><?php } ?>
				<?php if(in_array('Info Gathering',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'info_gathering' : $_GET['tab']); $next_tab = (!$next_set ? 'info_gathering' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'info_gathering' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'info_gathering'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=info_gathering"><li class="sidebar-lower-level <?= $_GET['tab'] == 'info_gathering' ? 'active blue' : '' ?>">Information Gathering<span class="pull-right"><?= $dbc->query("SELECT COUNT(*) `num` FROM `infogathering_pdf` WHERE `deleted`=0 AND (`projectid`='$projectid' OR (`company`='".get_client($dbc, $project['businessid'])."' AND `company` != '') OR `businessid`='".$project['businessid']."') AND `infogatheringid` IN (SELECT `infogatheringid` FROM `infogathering` WHERE `deleted`=0)")->fetch_assoc()['num'] ?></span></li></a><?php } ?>
				<?php if(in_array('Incident Reports',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'incident_reports' : $_GET['tab']); $next_tab = (!$next_set ? 'incident_reports' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'incident_reports' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'incident_reports'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=incident_reports"><li class="sidebar-lower-level <?= $_GET['tab'] == 'incident_reports' ? 'active blue' : '' ?>"><?= INC_REP_TILE ?><span class="pull-right"><?= $dbc->query("SELECT COUNT(*) `num` FROM `incident_report` WHERE `projectid`='$projectid'")->fetch_assoc()['num'] ?></span></li></a><?php } ?>
				<?php if(in_array('Time Sheets',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'time_sheets' : $_GET['tab']); $next_tab = (!$next_set ? 'time_sheets' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'time_sheets' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'time_sheets'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=time_sheets"><li class="sidebar-lower-level <?= $_GET['tab'] == 'time_sheets' ? 'active blue' : '' ?>">Time Sheets<span class="pull-right"><?= $dbc->query("SELECT COUNT(DISTINCT `staff`,`date`) `num` FROM `time_cards` WHERE `deleted`=0 AND `projectid`='$projectid'")->fetch_assoc()['num'] ?></span></li></a><?php }
				if(count($user_forms) > 0 && $_GET['edit'] > 0) {
					foreach($user_forms as $project_form_id => $subtab_name) { ?>
						<a href="?edit=<?= $_GET['edit'] ?>&tab=user_forms&project_form_id=<?= $project_form_id ?>"><li class="sidebar-lower-level <?= $_GET['project_form_id'] == $project_form_id ? 'active blue' : '' ?>"><?= $subtab_name ?></li></a>
					<?php }
				} ?>
			</ul>
		<?php }
		$sub_tabs = ['Tickets','Custom PDF','Work Orders','Tasks','Checklists','Time Clock','Report Action Items'];
		foreach($sub_tabs as $i => $tab) {
			if(!in_array($tab,$tab_config)) {
				unset($sub_tabs[$i]);
			}
		}
		$user_forms = [];
		$user_forms_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'action_item' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms_sql as $user_form_sql) {
			$user_forms[$user_form_sql['id']] = $user_form_sql['subtab_name'];
		}
		if(check_subtab_persmission($dbc, 'project', ROLE, 'view_action') && (count($sub_tabs) > 0 || count($user_forms) > 0)) {
			if(count($sub_tabs) > 0 || !in_array('Tickets',$tab_config)) {
				$ticket_bypass = false;
			}
			$milestones = explode('#*#',get_project_path_milestone($dbc, $project['project_path'], 'milestone'));
			$unassigned_sql = "SELECT 'Ticket', `ticketid` FROM tickets WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(milestone_timeline,'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '')";
			$show_sub = in_array($_GET['tab'],['tickets','workorders','tasks','checklists','unassigned','time_clock','custom_pdf','action_item_report']) || $no_sub_shown || array_key_exists($_GET['project_form_id'], $user_forms);
			$no_sub_shown = false; ?>
			<a href="?edit=<?= $_GET['edit'] ?>&tab=actions" onclick="$('.standard-collapsible ul ul:visible').not($(this).next('ul')).toggle().prev('a').find('li').toggleClass('collapsed'); $(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;" style="<?= count($sub_tabs) > 0 ? '' : 'display:none;' ?>">
				<li class="sidebar-higher-level <?= $show_sub ? 'active blue' : 'collapsed' ?>">Action Items<span class="arrow"></span></li></a>
			<ul style="<?= $show_sub ? (count($sub_tabs) > 0 ? '' : 'padding-left:0;') : 'display: none;' ?>">
				<?php if(in_array('Tickets',$tab_config)) {
					$count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`ticketid`) `num_rows` FROM `tickets` WHERE `projectid` = '$projectid' AND `deleted` = 0"))['num_rows'];
					$_GET['tab'] = ($_GET['tab'] == '' ? 'tickets' : $_GET['tab']); $next_tab = (!$next_set ? 'tickets' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'tickets' && !isset($_GET['ticket_type']) ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'tickets'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=tickets"><li class="sidebar-lower-level <?= $_GET['tab'] == 'tickets' && $_GET['ticket_type'] == $type_value ? 'active blue' : '' ?>"><?= (count(array_filter(explode(',',get_config($dbc, 'ticket_tabs')))) > 0 ? 'All ' : '') ?><?= TICKET_TILE ?><span class="pull-right"><?= $count ?></span></li></a><?php
                    $type_value = 'unassigned';
                    $next_tab = (!$next_set ? 'tickets&ticket_type='.$type_value : $next_tab);
                    $next_set = ($prev_set ? true : false);
                    $prev_set = ($_GET['tab'] == 'tickets' && $_GET['ticket_type'] == $type_value ? true : $prev_set);
                    $previous_tab = ($prev_set ? $previous_tab : 'tickets&ticket_type='.$type_value);
                    $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `num_rows` FROM tickets WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(milestone_timeline, '&gt;','>'), '&lt;','<'), '&nbsp;',' '), '&amp;','&'), '&quot;','\"'),'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '')"))['num_rows'];
                    ?><a href="?edit=<?= $_GET['edit'] ?>&tab=tickets&ticket_type=<?= $type_value ?>"><li class="sidebar-lower-level <?= $_GET['tab'] == 'tickets' && $_GET['ticket_type'] == $type_value ? 'active blue' : '' ?>">Unassigned<span class="pull-right"><?= $count ?></span></li></a><?php
					foreach(array_filter(explode(',',get_config($dbc, 'ticket_tabs'))) as $ticket_type) {
						$type_value = config_safe_str($ticket_type);
						$next_tab = (!$next_set ? 'tickets&ticket_type='.$type_value : $next_tab);
						$next_set = ($prev_set ? true : false);
						$prev_set = ($_GET['tab'] == 'tickets' && $_GET['ticket_type'] == $type_value ? true : $prev_set);
						$previous_tab = ($prev_set ? $previous_tab : 'tickets&ticket_type='.$type_value);
						$count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`ticketid`) `num_rows` FROM `tickets` WHERE `projectid` = '$projectid' AND `deleted` = 0 AND `ticket_type` = '$ticket_type'"))['num_rows'];
						?><a href="?edit=<?= $_GET['edit'] ?>&tab=tickets&ticket_type=<?= $type_value ?>"><li class="sidebar-lower-level <?= $_GET['tab'] == 'tickets' && $_GET['ticket_type'] == $type_value ? 'active blue' : '' ?>"><?= $ticket_type ?><span class="pull-right"><?= $count ?></span></li></a><?php
					}
				} ?>
				<?php if(in_array('Custom PDF',$tab_config)) {
					$pdfs = $dbc->query("SELECT `id`, `pdf_name` FROM `ticket_pdf` WHERE `deleted`=0");
					while($pdf = $pdfs->fetch_assoc()) {
						$count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`ticketid`) `num_rows` FROM `tickets` WHERE `projectid`='$projectid' AND `deleted`=0 AND `tickets`.`status` != 'Archive' AND `ticketid` IN (SELECT `ticketid` FROM `ticket_pdf_field_values` WHERE `pdf_type`='".$pdf['id']."' AND `deleted`=0)"))['num_rows'];
						$_GET['tab'] = ($_GET['tab'] == '' ? 'custom_pdf' : $_GET['tab']); $next_tab = (!$next_set ? 'custom_pdf' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'custom_pdf' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'custom_pdf'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=custom_pdf&id=<?= $pdf['id'] ?>"><li class="sidebar-lower-level <?= $_GET['tab'] == 'custom_pdf' && $_GET['id'] == $pdf['id'] ? 'active blue' : '' ?>"><?= $pdf['pdf_name'] ?><span class="pull-right"><?= $count ?></span></li></a><?php
					}
				} ?>
				<?php if(in_array('Work Orders',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'workorders' : $_GET['tab']); $next_tab = (!$next_set ? 'workorders' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'workorders' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'workorders'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=workorders"><li class="sidebar-lower-level <?= $_GET['tab'] == 'workorders' ? 'active blue' : '' ?>">Work Orders</li></a><?php } ?>
				<?php if(in_array('Checklists',$tab_config) || in_array('Tasks',$tab_config)) {
					$_GET['tab'] = ($_GET['tab'] == '' ? 'tasks' : $_GET['tab']); $next_tab = (!$next_set ? 'tasks' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'tasks' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'tasks');
					$count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `num_rows` FROM `tasklist` WHERE `projectid` = '$projectid' AND `deleted`=0"))['num_rows']; ?>
					<a href="?edit=<?= $_GET['edit'] ?>&tab=tasks"><li class="sidebar-lower-level <?= $_GET['tab'] == 'tasks' && $_GET['status'] != 'project' ? 'active blue' : '' ?>"><?= TASK_TILE ?><span class="pull-right"><?= $count ?></span></li></a>
				<?php } ?>
				<?php if((in_array('Checklists',$tab_config) || in_array('Tasks',$tab_config)) && tile_visible($dbc, 'checklist')) { $_GET['tab'] = ($_GET['tab'] == '' ? 'tasks' : $_GET['tab']); $next_tab = (!$next_set ? 'tasks' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'tasks' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'tasks'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=tasks&status=project"><li class="sidebar-lower-level <?= $_GET['tab'] == 'tasks' && $_GET['status'] == 'project' ? 'active blue' : '' ?>">Checklists</li></a><?php } ?>
				<?php if(in_array('Path',$tab_config) && !in_array('Unassigned Hide',$tab_config) && mysqli_num_rows(mysqli_query($dbc, $unassigned_sql)) > 0) {
					$_GET['tab'] = ($_GET['tab'] == '' ? 'unassigned' : $_GET['tab']); $next_tab = (!$next_set ? 'unassigned' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'unassigned' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'unassigned');
					$count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`ticketid`) `num_rows` FROM tickets WHERE projectid='$projectid' AND `projectid` > 0 AND `deleted`=0 AND `status` != 'Archive' AND (`status` = '' OR IFNULL(milestone_timeline,'') NOT IN (SELECT `milestone` FROM `project_path_custom_milestones` WHERE `deleted`=0 AND `projectid`='$projectid') OR IFNULL(to_do_date,'0000-00-00') = '0000-00-00' OR REPLACE(IFNULL(contactid,''),',','') = '')"))['num_rows']; ?>
					<a href="?edit=<?= $_GET['edit'] ?>&tab=unassigned"><li class="sidebar-lower-level <?= $_GET['tab'] == 'unassigned' ? 'active blue' : '' ?>">Unassigned<span class="pull-right"><?= $count ?></span></li></a>
				<?php } ?>
				<?php if(in_array('Time Clock',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'time_clock' : $_GET['tab']); $next_tab = (!$next_set ? 'time_clock' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'time_clock' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'time_clock'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=time_clock"><li class="sidebar-lower-level <?= $_GET['tab'] == 'time_clock' ? 'active blue' : '' ?>">Time Clock</li></a><?php } ?>
				<?php if(in_array('Report Action Items',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'action_item_report' : $_GET['tab']); $next_tab = (!$next_set ? 'action_item_report' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'action_item_report' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'action_item_report'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=action_item_report"><li class="sidebar-lower-level <?= $_GET['tab'] == 'action_item_report' ? 'active blue' : '' ?>">Status Report</li></a><?php }
				if(count($user_forms) > 0 && $_GET['edit'] > 0) {
					foreach($user_forms as $project_form_id => $subtab_name) { ?>
						<a href="?edit=<?= $_GET['edit'] ?>&tab=user_forms&project_form_id=<?= $project_form_id ?>"><li class="sidebar-lower-level <?= $_GET['project_form_id'] == $project_form_id ? 'active blue' : '' ?>"><?= $subtab_name ?></li></a>
					<?php }
				} ?>
			</ul>
		<?php }
		$sub_tabs = ['Administration'];
		foreach($sub_tabs as $i => $tab) {
			if(!in_array($tab,$tab_config)) {
				unset($sub_tabs[$i]);
			} else if($tab == 'Administration') {
				unset($sub_tabs[$i]);
				$admin_groups = $dbc->query("SELECT `name` FROM `field_config_project_admin` WHERE CONCAT(',',`contactid`,',') LIKE '%".$_SESSION['contactid']."%' AND `deleted`=0");
				while($admin_group = $admin_groups->fetch_assoc()) {
					$sub_tabs[] = $admin_group['name'];
				}
			}
		}
		$user_forms = [];
		$user_forms_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'administration' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms_sql as $user_form_sql) {
			$user_forms[$user_form_sql['id']] = $user_form_sql['subtab_name'];
		}
		$admin_group_tab = 'administration';
		if(check_subtab_persmission($dbc, 'project', ROLE, 'view_administration') && (count($sub_tabs) > 0 || count($user_forms) > 0)) {
			$ticket_bypass = false;
			$show_sub = strpos($_GET['tab'],'administration_') !== FALSE || array_key_exists($_GET['project_form_id'], $user_forms);
			$no_sub_shown = false; ?>
			<a href="?edit=<?= $_GET['edit'] ?>&tab=administration" onclick="$('.standard-collapsible ul ul:visible').not($(this).next('ul')).toggle().prev('a').find('li').toggleClass('collapsed'); $(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;" style="<?= count($sub_tabs) > 0 ? '' : 'display:none;' ?>">
				<li class="sidebar-higher-level <?= $show_sub ? 'active blue' : 'collapsed' ?>">Administration<span class="arrow"></span></li></a>
			<ul id="ul_admin" style="<?= $show_sub ? '' : 'display: none;' ?>">
				<?php foreach($sub_tabs as $admin_group) { ?>
					<a href="?edit=<?= $_GET['edit'] ?>&tab=administration" onclick="$(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;"><li class="sidebar-higher-level <?= strpos($_GET['tab'],'administration_'.config_safe_str($admin_group)) !== FALSE ? 'active blue' : 'collapsed' ?>"><?= $admin_group ?><span class="arrow"></span></li></a>
					<ul style="<?= strpos($_GET['tab'],'administration_'.config_safe_str($admin_group)) !== FALSE ? '' : 'display: none;' ?> padding-left:1em;">
						<?php $admin_group_id = 'administration_'.config_safe_str($admin_group).'_pending';
						$_GET['tab'] = ($_GET['tab'] == '' ? $admin_group_id : $_GET['tab']);
						$admin_group_tab = $_GET['tab'] == $admin_group_id ? $admin_group_id : $admin_group_tab;
						$next_tab = (!$next_set ? $admin_group_id : $next_tab);
						$next_set = ($prev_set ? true : false);
						$prev_set = ($_GET['tab'] == $admin_group_id ? true : $prev_set);
						$previous_tab = ($prev_set ? $previous_tab : $admin_group_id); ?>
						<a href="?edit=<?= $_GET['edit'] ?>&tab=<?= $admin_group_id ?>"><li class="sidebar-lower-level <?= $_GET['tab'] == $admin_group_id ? 'active blue' : '' ?>">Pending</li></a>
						<?php $admin_group_id = 'administration_'.config_safe_str($admin_group).'_approved';
						$_GET['tab'] = ($_GET['tab'] == '' ? $admin_group_id : $_GET['tab']);
						$admin_group_tab = $_GET['tab'] == $admin_group_id ? $admin_group_id : $admin_group_tab;
						$next_tab = (!$next_set ? $admin_group_id : $next_tab);
						$next_set = ($prev_set ? true : false);
						$prev_set = ($_GET['tab'] == $admin_group_id ? true : $prev_set);
						$previous_tab = ($prev_set ? $previous_tab : $admin_group_id); ?>
						<a href="?edit=<?= $_GET['edit'] ?>&tab=<?= $admin_group_id ?>"><li class="sidebar-lower-level <?= $_GET['tab'] == $admin_group_id ? 'active blue' : '' ?>">Approved</li></a>
						<?php $admin_group_id = 'administration_'.config_safe_str($admin_group).'_revision';
						$_GET['tab'] = ($_GET['tab'] == '' ? $admin_group_id : $_GET['tab']);
						$admin_group_tab = $_GET['tab'] == $admin_group_id ? $admin_group_id : $admin_group_tab;
						$next_tab = (!$next_set ? $admin_group_id : $next_tab);
						$next_set = ($prev_set ? true : false);
						$prev_set = ($_GET['tab'] == $admin_group_id ? true : $prev_set);
						$previous_tab = ($prev_set ? $previous_tab : $admin_group_id); ?>
						<a href="?edit=<?= $_GET['edit'] ?>&tab=<?= $admin_group_id ?>"><li class="sidebar-lower-level <?= $_GET['tab'] == $admin_group_id ? 'active blue' : '' ?>">In Revision</li></a>
					</ul>
				<?php }
				if(count($user_forms) > 0 && $_GET['edit'] > 0) {
					foreach($user_forms as $project_form_id => $subtab_name) { ?>
						<a href="?edit=<?= $_GET['edit'] ?>&tab=user_forms&project_form_id=<?= $project_form_id ?>"><li class="sidebar-lower-level <?= $_GET['project_form_id'] == $project_form_id ? 'active blue' : '' ?>"><?= $subtab_name ?></li></a>
					<?php }
				} ?>
			</ul>
		<?php }
		$sub_tabs = ['Email','Phone','Agendas','Meetings','Comm Log'];
		foreach($sub_tabs as $i => $tab) {
			if(!in_array($tab,$tab_config)) {
				unset($sub_tabs[$i]);
			}
		}
		$user_forms = [];
		$user_forms_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'communications' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms_sql as $user_form_sql) {
			$user_forms[$user_form_sql['id']] = $user_form_sql['subtab_name'];
		}
		if(check_subtab_persmission($dbc, 'project', ROLE, 'view_communications') && (count($sub_tabs) > 0 || count($user_forms) > 0)) {
			$ticket_bypass = false;
			$show_sub = in_array($_GET['tab'],['email','phone','agendas','meetings','comm_log']) || $no_sub_shown || array_key_exists($_GET['project_form_id'], $user_forms);
			$no_sub_shown = false; ?>
			<a href="?edit=<?= $_GET['edit'] ?>&tab=email" onclick="$('.standard-collapsible ul ul:visible').not($(this).next('ul')).toggle().prev('a').find('li').toggleClass('collapsed'); $(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;" style="<?= count($sub_tabs) > 0 ? '' : 'display:none;' ?>">
				<li class="sidebar-higher-level <?= $show_sub ? 'active blue' : 'collapsed' ?>">Communications<span class="arrow"></span></li></a>
			<ul id="ul_comm" style="<?= $show_sub ? (count($sub_tabs) > 0 ? '' : 'padding-left:0;') : 'display: none;' ?>">
				<?php if(in_array('Email',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'email' : $_GET['tab']); $next_tab = (!$next_set ? 'email' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'email' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'email'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=email"><li class="sidebar-lower-level <?= $_GET['tab'] == 'email' ? 'active blue' : '' ?>">Email</li></a><?php } ?>
				<?php if(in_array('Phone',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'phone' : $_GET['tab']); $next_tab = (!$next_set ? 'phone' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'phone' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'phone'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=phone"><li class="sidebar-lower-level <?= $_GET['tab'] == 'phone' ? 'active blue' : '' ?>">Phone</li></a><?php } ?>
				<?php if(in_array('Agendas',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'agendas' : $_GET['tab']); $next_tab = (!$next_set ? 'agendas' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'agendas' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'agendas'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=agendas"><li class="sidebar-lower-level <?= $_GET['tab'] == 'agendas' ? 'active blue' : '' ?>">Agendas</li></a><?php } ?>
				<?php if(in_array('Meetings',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'meetings' : $_GET['tab']); $next_tab = (!$next_set ? 'meetings' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'meetings' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'meetings'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=meetings"><li class="sidebar-lower-level <?= $_GET['tab'] == 'meetings' ? 'active blue' : '' ?>">Meetings</li></a><?php } ?>
				<?php if(in_array('Comm Log',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'comm_log' : $_GET['tab']); $next_tab = (!$next_set ? 'comm_log' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'comm_log' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'comm_log'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=comm_log"><li class="sidebar-lower-level <?= $_GET['tab'] == 'comm_log' ? 'active blue' : '' ?>">Communication History</li></a><?php }
				if(count($user_forms) > 0 && $_GET['edit'] > 0) {
					foreach($user_forms as $project_form_id => $subtab_name) { ?>
						<a href="?edit=<?= $_GET['edit'] ?>&tab=user_forms&project_form_id=<?= $project_form_id ?>"><li class="sidebar-lower-level <?= $_GET['project_form_id'] == $project_form_id ? 'active blue' : '' ?>"><?= $subtab_name ?></li></a>
					<?php }
				} ?>
			</ul>
		<?php }
		$sub_tabs = ['Timesheets','Payroll','Expenses','Payables'];
		foreach($sub_tabs as $i => $tab) {
			if(!in_array($tab,$tab_config)) {
				unset($sub_tabs[$i]);
			}
		}
		$user_forms = [];
		$user_forms_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'accounting' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms_sql as $user_form_sql) {
			$user_forms[$user_form_sql['id']] = $user_form_sql['subtab_name'];
		}
		if(check_subtab_persmission($dbc, 'project', ROLE, 'view_accounting') && (count($sub_tabs) > 0 || count($user_forms) > 0)) {
			$ticket_bypass = false;
			$show_sub = in_array($_GET['tab'],['timesheet','payroll','expenses','payables']) || $no_sub_shown || array_key_exists($_GET['project_form_id'], $user_forms);
			$no_sub_shown = false; ?>
			<a href="?edit=<?= $_GET['edit'] ?>&tab=gantt" onclick="$('.standard-collapsible ul ul:visible').not($(this).next('ul')).toggle().prev('a').find('li').toggleClass('collapsed'); $(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;" style="<?= count($sub_tabs) > 0 ? '' : 'display:none;' ?>">
				<li class="sidebar-higher-level <?= $show_sub ? 'active blue' : 'collapsed' ?>">Accounting<span class="arrow"></span></li></a>
			<ul style="<?= $show_sub ? (count($sub_tabs) > 0 ? '' : 'padding-left:0;') : 'display: none;' ?>">
				<?php if(in_array('Timesheets',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'timesheet' : $_GET['tab']); $next_tab = (!$next_set ? 'timesheet' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'timesheet' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'timesheet'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=timesheet"><li class="sidebar-lower-level <?= $_GET['tab'] == 'timesheet' ? 'active blue' : '' ?>">Time Sheets</li></a><?php } ?>
				<?php if(in_array('Payroll',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'payroll' : $_GET['tab']); $next_tab = (!$next_set ? 'payroll' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'payroll' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'payroll'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=payroll"><li class="sidebar-lower-level <?= $_GET['tab'] == 'payroll' ? 'active blue' : '' ?>">Payroll</li></a><?php } ?>
				<?php if(in_array('Expenses',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'expenses' : $_GET['tab']); $next_tab = (!$next_set ? 'expenses' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'expenses' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'expenses'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=expenses"><li class="sidebar-lower-level <?= $_GET['tab'] == 'expenses' ? 'active blue' : '' ?>">Expenses</li></a><?php } ?>
				<?php if(in_array('Payables',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'payables' : $_GET['tab']); $next_tab = (!$next_set ? 'payables' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'payables' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'payables'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=payables"><li class="sidebar-lower-level <?= $_GET['tab'] == 'payables' ? 'active blue' : '' ?>">Accounts Payable</li></a><?php }
				if(count($user_forms) > 0 && $_GET['edit'] > 0) {
					foreach($user_forms as $project_form_id => $subtab_name) { ?>
						<a href="?edit=<?= $_GET['edit'] ?>&tab=user_forms&project_form_id=<?= $project_form_id ?>"><li class="sidebar-lower-level <?= $_GET['project_form_id'] == $project_form_id ? 'active blue' : '' ?>"><?= $subtab_name ?></li></a>
					<?php }
				} ?>
			</ul>
		<?php }
		$sub_tabs = ['Deliverables','Gantt','Profit','Reminders','Estimated Time','Tracked Time','Time Tracked'];
		foreach($sub_tabs as $i => $tab) {
			if(!in_array($tab,$tab_config)) {
				unset($sub_tabs[$i]);
			}
		}
		$user_forms = [];
		$user_forms_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'reporting' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms_sql as $user_form_sql) {
			$user_forms[$user_form_sql['id']] = $user_form_sql['subtab_name'];
		}
		if(check_subtab_persmission($dbc, 'project', ROLE, 'view_reporting') && (count($sub_tabs) > 0 || count($user_forms) > 0)) {
			$ticket_bypass = false;
			$show_sub = in_array($_GET['tab'],['deliverables','gantt','profitloss','reminders','track_time','estimate_time','time_track']) || $no_sub_shown || array_key_exists($_GET['project_form_id'], $user_forms);
			$no_sub_shown = false; ?>
			<a href="?edit=<?= $_GET['edit'] ?>&tab=gantt" onclick="$('.standard-collapsible ul ul:visible').not($(this).next('ul')).toggle().prev('a').find('li').toggleClass('collapsed'); $(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;" style="<?= count($sub_tabs) > 0 ? '' : 'display:none;' ?>">
				<li class="sidebar-higher-level <?= $show_sub ? 'active blue' : 'collapsed' ?>">Reporting<span class="arrow"></span></li></a>
			<ul style="<?= $show_sub ? (count($sub_tabs) > 0 ? '' : 'padding-left:0;') : 'display: none;' ?>">
				<?php if(in_array('Deliverables',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'deliverables' : $_GET['tab']); $next_tab = (!$next_set ? 'deliverables' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'deliverables' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'deliverables'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=deliverables"><li class="sidebar-lower-level <?= $_GET['tab'] == 'deliverables' ? 'active blue' : '' ?>">Deliverables</li></a><?php } ?>
				<?php if(in_array('Gantt',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'gantt' : $_GET['tab']); $next_tab = (!$next_set ? 'gantt' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'gantt' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'gantt'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=gantt"><li class="sidebar-lower-level <?= $_GET['tab'] == 'gantt' ? 'active blue' : '' ?>">Gantt Chart</li></a><?php } ?>
				<?php if(in_array('Profit',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'profitloss' : $_GET['tab']); $next_tab = (!$next_set ? 'profitloss' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'profitloss' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'profitloss'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=profitloss"><li class="sidebar-lower-level <?= $_GET['tab'] == 'profitloss' ? 'active blue' : '' ?>">Profit & Loss</li></a><?php } ?>
				<!--<?php // if(in_array('Report Checklist',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'report_checklist' : $_GET['tab']); $next_tab = (!$next_set ? 'report_checklist' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'report_checklist' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'report_checklist'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=report_checklist"><li class="sidebar-lower-level <?= $_GET['tab'] == 'report_checklist' ? 'active blue' : '' ?>">Checklists</li></a><?php // } ?>-->
				<?php if(in_array('Reminders',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'reminders' : $_GET['tab']); $next_tab = (!$next_set ? 'reminders' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'reminders' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'reminders'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=reminders"><li class="sidebar-lower-level <?= $_GET['tab'] == 'reminders' ? 'active blue' : '' ?>">Reminders</li></a><?php } ?>
				<?php if(in_array('Estimated Time',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'estimate_time' : $_GET['tab']); $next_tab = (!$next_set ? 'estimate_time' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'estimate_time' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'estimate_time'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=estimate_time"><li class="sidebar-lower-level <?= $_GET['tab'] == 'estimate_time' ? 'active blue' : '' ?>">Estimated Time</li></a><?php } ?>
				<?php if(in_array('Tracked Time',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'track_time' : $_GET['tab']); $next_tab = (!$next_set ? 'track_time' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'track_time' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'track_time'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=track_time"><li class="sidebar-lower-level <?= $_GET['tab'] == 'track_time' ? 'active blue' : '' ?>">Tracked Time</li></a><?php } ?>
				<?php if(in_array('Time Tracked',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'time_track' : $_GET['tab']); $next_tab = (!$next_set ? 'time_track' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'time_track' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'time_track'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=time_track"><li class="sidebar-lower-level <?= $_GET['tab'] == 'time_track' ? 'active blue' : '' ?>">Total Time Tracked</li></a><?php } ?>
				<?php if(count($user_forms) > 0 && $_GET['edit'] > 0) {
					foreach($user_forms as $project_form_id => $subtab_name) { ?>
						<a href="?edit=<?= $_GET['edit'] ?>&tab=user_forms&project_form_id=<?= $project_form_id ?>"><li class="sidebar-lower-level <?= $_GET['project_form_id'] == $project_form_id ? 'active blue' : '' ?>"><?= $subtab_name ?></li></a>
					<?php }
				} ?>
			</ul>
		<?php }
		$sub_tabs = ['Billing','Field Service Tickets','Purchase Orders','Invoices','WCB Invoices','Outstanding','Paid','Invoice Reminders'];
		foreach($sub_tabs as $i => $tab) {
			if(!in_array($tab,$tab_config)) {
				unset($sub_tabs[$i]);
			}
		}
		$user_forms = [];
		$user_forms_sql = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `user_form_id` > 0 AND IFNULL(`subtab_name`,'') != '' AND `project_heading` = 'billing' AND (`project_type` = 'ALL' OR `project_type` = '$projecttype') ORDER BY `project_type` <> 'ALL'"),MYSQLI_ASSOC);
		foreach($user_forms_sql as $user_form_sql) {
			$user_forms[$user_form_sql['id']] = $user_form_sql['subtab_name'];
		}
		if(check_subtab_persmission($dbc, 'project', ROLE, 'view_billing') && (count($sub_tabs) > 0 || count($user_forms) > 0)) {
			$ticket_bypass = false;
			$show_sub = in_array($_GET['tab'],['payment_schedule','field_service_ticket','purchase_order','work_ticket','invoice','wcb_invoice','outstanding','paid','billing_new','billing_reminders']) || $no_sub_shown || array_key_exists($_GET['project_form_id'], $user_forms);
			$no_sub_shown = false; ?>
			<a href="?edit=<?= $_GET['edit'] ?>&tab=billing_new" onclick="$('.standard-collapsible ul ul:visible').not($(this).next('ul')).toggle().prev('a').find('li').toggleClass('collapsed'); $(this).next('ul').toggle(); $(this).find('li').toggleClass('collapsed'); return false;" style="<?= count($sub_tabs) > 0 ? '' : 'display:none;' ?>">
				<li class="sidebar-higher-level <?= $show_sub ? 'active blue' : 'collapsed' ?>">Billing<span class="arrow"></span></li></a>
			<ul style="<?= $show_sub ? (count($sub_tabs) > 0 ? '' : 'padding-left:0;') : 'display: none;' ?>">
				<?php if(in_array('Billing',$tab_config) && $security['edit'] > 0) { $_GET['tab'] = ($_GET['tab'] == '' ? 'billing_new' : $_GET['tab']); $next_tab = (!$next_set ? 'billing_new' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'billing_new' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'billing_new'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=billing_new"><li class="sidebar-lower-level <?= $_GET['tab'] == 'billing_new' ? 'active blue' : '' ?>">Create New</li></a><?php } ?>
				<?php if(in_array('Payment Schedule',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'payment_schedule' : $_GET['tab']); $next_tab = (!$next_set ? 'payment_schedule' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'payment_schedule' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'payment_schedule'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=payment_schedule"><li class="sidebar-lower-level <?= $_GET['tab'] == 'payment_schedule' ? 'active blue' : '' ?>">Payment Schedule</li></a><?php } ?>
				<?php if(in_array('Field Service Tickets',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'field_service_ticket' : $_GET['tab']); $next_tab = (!$next_set ? 'field_service_ticket' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'field_service_ticket' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'field_service_ticket'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=field_service_ticket"><li class="sidebar-lower-level <?= $_GET['tab'] == 'field_service_ticket' ? 'active blue' : '' ?>">Field Service Tickets</li></a><?php } ?>
				<?php if(in_array('Purchase Orders',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'purchase_order' : $_GET['tab']); $next_tab = (!$next_set ? 'purchase_order' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'purchase_order' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'purchase_order'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=purchase_order"><li class="sidebar-lower-level <?= $_GET['tab'] == 'purchase_order' ? 'active blue' : '' ?>">Purchase Orders</li></a><?php } ?>
				<?php if(in_array('Work Tickets',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'work_ticket' : $_GET['tab']); $next_tab = (!$next_set ? 'work_ticket' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'work_ticket' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'work_ticket'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=work_ticket"><li class="sidebar-lower-level <?= $_GET['tab'] == 'work_ticket' ? 'active blue' : '' ?>">Work Tickets</li></a><?php } ?>
				<?php if(in_array('Invoices',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'invoice' : $_GET['tab']); $next_tab = (!$next_set ? 'invoice' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'invoice' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'invoice'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=invoice"><li class="sidebar-lower-level <?= $_GET['tab'] == 'invoice' ? 'active blue' : '' ?>">Invoices</li></a><?php } ?>
				<?php if(in_array('WCB Invoices',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'wcb_invoice' : $_GET['tab']); $next_tab = (!$next_set ? 'wcb_invoice' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'wcb_invoice' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'wcb_invoice'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=wcb_invoice"><li class="sidebar-lower-level <?= $_GET['tab'] == 'wcb_invoice' ? 'active blue' : '' ?>">WCB Invoices</li></a><?php } ?>
				<?php if(in_array('Outstanding',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'outstanding' : $_GET['tab']); $next_tab = (!$next_set ? 'outstanding' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'outstanding' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'outstanding'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=outstanding"><li class="sidebar-lower-level <?= $_GET['tab'] == 'outstanding' ? 'active blue' : '' ?>">Outstanding</li></a><?php } ?>
				<?php if(in_array('Paid',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'paid' : $_GET['tab']); $next_tab = (!$next_set ? 'paid' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'paid' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'paid'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=paid"><li class="sidebar-lower-level <?= $_GET['tab'] == 'paid' ? 'active blue' : '' ?>">Paid</li></a><?php } ?>
				<?php if(in_array('Invoice Reminders',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'billing_reminders' : $_GET['tab']); $next_tab = (!$next_set ? 'billing_reminders' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'billing_reminders' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'billing_reminders'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=billing_reminders"><li class="sidebar-lower-level <?= $_GET['tab'] == 'billing_reminders' ? 'active blue' : '' ?>">Invoice Reminders</li></a><?php }
				if(count($user_forms) > 0 && $_GET['edit'] > 0) {
					foreach($user_forms as $project_form_id => $subtab_name) { ?>
						<a href="?edit=<?= $_GET['edit'] ?>&tab=user_forms&project_form_id=<?= $project_form_id ?>"><li class="sidebar-lower-level <?= $_GET['project_form_id'] == $project_form_id ? 'active blue' : '' ?>"><?= $subtab_name ?></li></a>
					<?php }
				} ?>
			</ul>
		<?php }
        if(in_array('History',$tab_config)) { $_GET['tab'] = ($_GET['tab'] == '' ? 'history' : $_GET['tab']); $next_tab = (!$next_set ? 'history' : $next_tab); $next_set = ($prev_set ? true : false); $prev_set = ($_GET['tab'] == 'history' ? true : $prev_set); $previous_tab = ($prev_set ? $previous_tab : 'history'); ?><a href="?edit=<?= $_GET['edit'] ?>&tab=history"><li class="sidebar-higher-level <?= $_GET['tab'] == 'history' ? 'active blue' : '' ?>">History</li></a><?php } ?>
		<li>Created <?= $project['created_by'] > 0 ? 'by<br />'.get_contact($dbc, $project['created_by']).'<br />' : '' ?> on <?= $projectid > 0 ? $project['created_date'] : date('Y-m-d') ?></li>
	</ul>
</div>
<?php } ?>
<div class='scale-to-fill has-main-screen <?= IFRAME_PAGE && $_GET['iframe_slider'] != 1 ? '' : 'hide-titles-mob' ?>'>
	<div class='main-screen search_results form-horizontal standard-body' style="display:none;"></div>
	<div class='main-screen default_screen form-horizontal standard-body'>
		<?php $include_files = [];
		$body_title = '';
		switch($_GET['tab']) {
			case 'info':
			case 'estimate':
			case 'notes':
			case 'details_path':
			case 'staff':
			case 'details':
			case 'documents':
			case 'dates':
				$body_title = 'Project Details';
				if(in_array('Information',$tab_config) || $_GET['edit'] == 0) { $include_files[] = 'edit_project_info.php'; }
				if($estimate_list && in_array('Estimate Info',$tab_config)) { $include_files[] = 'edit_project_estimate.php'; }
				if(in_array('Staff',$tab_config)) { $include_files[] = 'edit_project_staff.php'; }
				if(in_array('Details Path',$tab_config)) { $include_files[] = 'edit_project_details_path.php'; }
				if(in_array('Notes',$tab_config)) { $include_files[] = 'edit_project_notes.php'; }
				if(in_array('Details',$tab_config)) { $include_files[] = 'edit_project_details.php'; }
				if(in_array('Documents',$tab_config)) { $include_files[] = 'edit_project_documents.php'; }
				if(in_array('Dates',$tab_config)) { $include_files[] = 'edit_project_dates.php'; }
				$include_files[] = 'next_buttons.php'; ?>
				<script>
				$(document).ready(function() {
					setTimeout(function() { $('.main-screen .default_screen').scrollTop($('#head_<?= $_GET['tab'] ?>').position().top) }, 250);
					$('#ul_details').find('a').off('click').click(function() {
						var tab = this.href.split('tab=')[1];
						$('.main-screen .default_screen').scrollTop($('#head_'+tab).position().top + $('.main-screen .default_screen').scrollTop()).scroll();
						return false;
					});
					$('.main-screen .default_screen').scroll(function() {
						var heading = [];
                        $('.default_screen [id^=head]').filter(function() { return $(this).position().top < 10 }).last().each(function() { heading.push(this.id); });
                        $('.default_screen [id^=head]').filter(function() { return $(this).position().top >= 10 && $(this).position().top < $('.default_screen').innerHeight(); }).each(function() { heading.push(this.id); });
						$('#ul_details li.active.blue').removeClass('active blue');
                        heading.forEach(function(heading) { $('#ul_details').find('a[href$='+heading.substr(5)+']').find('li').addClass('active blue'); });
					}).scroll();
				});
				</script>
				<?php break;
			case 'scope':
				$body_title = PROJECT_NOUN.' Scope';
				$include_files[] = 'edit_project_scope.php'; break;
			case 'scope_type':
				$body_title = PROJECT_NOUN.' Scope: '.$scope_tab_label;
				$include_files[] = 'edit_project_scope.php'; break;
			case 'est_scope':
				$body_title = ESTIMATE_TILE.' Scope';
				$include_files[] = 'edit_project_scope_estimate.php'; break;
			case 'sales_order':
				$body_title = 'Sales Orders';
				$include_files[] = 'edit_project_scope_sales_order.php'; break;
			case 'intake':
				$body_title = 'Intake Forms';
				$include_files[] = 'edit_project_scope_intake.php'; break;
			case 'info_gathering':
				$body_title = 'Information Gathering';
				$include_files[] = 'edit_project_scope_info_gathering.php'; break;
			case 'incident_reports':
				$body_title = INC_REP_TILE;
				$include_files[] = 'edit_project_scope_incident_reports.php'; break;
			case 'time_sheets':
				$body_title = 'Time Sheets';
				$include_files[] = 'edit_project_scope_time_sheets.php'; break;
			case 'tickets':
				$body_title = TICKET_TILE;
				$include_files[] = 'edit_project_scope_tickets.php'; break;
			case 'custom_pdf':
				$body_title = 'Custom PDF';
				$include_files[] = 'edit_project_scope_pdf.php'; break;
			case 'workorders':
				$body_title = 'Work Orders';
				$include_files[] = 'edit_project_scope_workorders.php'; break;
			case 'action_item_report':
				$body_title = 'Status Report';
				$include_files[] = 'edit_project_scope_status_report.php'; break;
			case 'tasks':
				$body_title = TASK_TILE;
				$include_files[] = 'edit_project_scope_checklists.php'; break;
			case 'unassigned':
				$body_title = 'Unassigned';
				$include_files[] = 'edit_project_scope_unassigned.php'; break;
			case 'time_clock':
				$body_title = 'Time Clock';
				$include_files[] = 'edit_project_scope_time.php'; break;

			case 'services':
			case 'products':
			case 'ptasks':
			case 'inventory':
			case 'admin':
				$body_title = 'Projections';
				$include_files[] = 'edit_project_projections.php';
                ?>
				<script>
				$(document).ready(function() {
					$('#ul_projection').find('a').off('click').click(function() {
						var tab = this.href.split('tab=')[1];
						$('.main-screen .default_screen').scrollTop($('#head_'+tab).position().top + $('.main-screen .default_screen').scrollTop());
						return false;
					});
					$('.main-screen .default_screen').scroll(function() {
						var heading = [];
                        $('.default_screen [id^=head]').filter(function() { return $(this).position().top < 10 }).last().each(function() { heading.push(this.id); });
                        $('.default_screen [id^=head]').filter(function() { return $(this).position().top >= 10 && $(this).position().top < $('.default_screen').innerHeight(); }).each(function() { heading.push(this.id); });
						$('#ul_projection li.active.blue').removeClass('active blue');
                        heading.forEach(function(heading) { $('#ul_projection').find('a[href$='+heading.substr(5)+']').find('li').addClass('active blue'); });
					}).scroll();
					setTimeout(function() { $('.main-screen .default_screen').scrollTop($('#head_<?= $_GET['tab'] ?>').position().top); },100);
				});
				</script>
                <?php
                break;
			case 'email':
			case 'phone':
			case 'agendas':
			case 'meetings':
			case 'comm_log':
				$body_title = 'Communications';
				if(in_array('Email',$tab_config)) { $include_files[] = 'edit_project_comm_email.php'; }
				if(in_array('Phone',$tab_config)) { $include_files[] = 'edit_project_comm_phone.php'; }
				if(in_array('Agendas',$tab_config)) { $include_files[] = 'edit_project_comm_agendas.php'; }
				if(in_array('Meetings',$tab_config)) { $include_files[] = 'edit_project_comm_meetings.php'; }
				if(in_array('Comm Log',$tab_config)) { $include_files[] = 'edit_project_comm_log.php'; }
				$include_files[] = 'next_buttons.php'; ?>
				<script>
				$(document).ready(function() {
					$('#ul_comm').find('a').off('click').click(function() {
						var tab = this.href.split('tab=')[1];
						$('.main-screen .default_screen').scrollTop($('#head_'+tab).position().top + $('.main-screen .default_screen').scrollTop());
						return false;
					});
					$('.main-screen .default_screen').scroll(function() {
						var heading = [];
                        $('.default_screen [id^=head]').filter(function() { return $(this).position().top < 10 }).last().each(function() { heading.push(this.id); });
                        $('.default_screen [id^=head]').filter(function() { return $(this).position().top >= 10 && $(this).position().top < $('.default_screen').innerHeight(); }).each(function() { heading.push(this.id); });
						$('#ul_comm li.active.blue').removeClass('active blue');
                        heading.forEach(function(heading) { $('#ul_comm').find('a[href$='+heading.substr(5)+']').find('li').addClass('active blue'); });
					}).scroll();
					setTimeout(function() { $('.main-screen .default_screen').scrollTop($('#head_<?= $_GET['tab'] ?>').position().top); },100);
				});
				</script>
				<?php break;
			case 'reminders':
				$body_title = 'Reminders';
				$include_files[] = 'edit_project_comm_reminders.php'; break;
			case 'gantt':
				$body_title = 'Gantt Chart';
				$include_files[] = 'edit_project_report_gantt.php'; break;
			case 'profitloss':
				$body_title = 'Profit & Loss';
				$include_files[] = 'edit_project_report_profit_loss.php'; break;
			case 'report_checklist':
				$body_title = 'Checklists';
				$include_files[] = 'edit_project_report_checklists.php'; break;
			case 'history':
				$body_title = 'History';
				$include_files[] = 'edit_project_report_history.php'; break;
			case 'estimate_time':
				$body_title = 'Estimated Time';
				$include_files[] = 'edit_project_report_estimated.php'; break;
			case 'track_time':
				$body_title = 'Tracked Time';
				$include_files[] = 'edit_project_report_tracked.php'; break;
			case 'time_track':
				$body_title = 'Total Time Tracked';
				$include_files[] = 'edit_project_report_time.php'; break;
			case 'timesheet':
				$body_title = 'Time Sheets';
				$include_files[] = 'edit_project_acct_timesheet.php'; break;
			case 'expenses':
				$body_title = 'Expenses';
				$include_files[] = 'edit_project_acct_expenses.php'; break;
			case 'payables':
				$body_title = 'Payables';
				$include_files[] = 'edit_project_acct_payables.php'; break;
			case 'payroll':
				$body_title = 'Payroll';
				$include_files[] = 'edit_project_acct_payroll.php'; break;
			case 'billing_new':
				$body_title = 'New Billing';
				$include_files[] = 'edit_project_billing_new.php'; break;
			case 'billing_reminders':
				$body_title = 'Recurring Billing Reminders';
				$include_files[] = 'edit_project_billing_reminders.php'; break;
			case 'billing_details':
				$body_title = 'Billing Details';
				$include_files[] = 'edit_project_billing_details.php'; break;
			case 'wcb_invoice':
				$body_title = 'WCB Invoices';
				$include_files[] = 'edit_project_billing_wcb.php'; break;
			case 'payment_schedule':
				$body_title = 'Payment Schedule';
				$include_files[] = 'edit_project_billing_pay_schedule.php'; break;
			case 'field_service_ticket':
			case 'purchase_order':
			case 'work_ticket':
			case 'invoice':
			case 'outstanding':
			case 'paid':
				if($_GET['tab'] == 'field_service_ticket') {
					$body_title = 'Field Service Tickets';
				} else if($_GET['tab'] == 'purchase_order') {
					$body_title = 'Purchase Orders';
				} else if($_GET['tab'] == 'work_ticket') {
					$body_title = 'Work Tickets';
				} else if($_GET['tab'] == 'invoice') {
					$body_title = 'Invoices';
				} else if($_GET['tab'] == 'outstanding') {
					$body_title = 'Outstanding Invoices';
				} else if($_GET['tab'] == 'paid') {
					$body_title = 'Paid Invoices';
				}
				$include_files[] = 'edit_project_billing_invoices.php'; break;
			case 'deliverables':
				$body_title = PROJECT_NOUN.' Deliverables';
				$include_files[] = 'edit_project_report_deliverables.php'; break;
			case $admin_group_tab:
				$body_title = 'Administration';
				$include_files[] = 'project_administration.php'; break;
			case 'scrum_board':
				$body_title = PROJECT_NOUN.' Scrum Board';
				$include_files[] = 'edit_project_path_scrum.php'; break;
			case 'user_forms':
				$body_title = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_project_form` WHERE `id` = '".$_GET['project_form_id']."'"))['subtab_name'];
				$include_files[] = 'edit_project_user_form.php'; break;
			case 'summary':
				$body_title = PROJECT_NOUN.' Summary';
				$include_files[] = 'edit_project_summary.php'; break;
			case 'summary_project':
				$body_title = 'Project Summary';
				//$include_files[] = 'edit_project_summary_project.php'; break;
				$include_files[] = 'edit_project_summary.php'; break;
			case 'summary_tickets':
				$body_title = 'Tickets Summary';
				$include_files[] = 'edit_project_summary_tickets.php'; break;
			case 'summary_tasks':
				$body_title = 'Tasks Summary';
				$include_files[] = 'edit_project_summary_tasks.php'; break;
			case 'summary_checklists':
				$body_title = 'Checklists Summary';
				$include_files[] = 'edit_project_summary_checklists.php'; break;
			case 'summary_projections':
				$body_title = 'Projections Summary';
				$include_files[] = 'edit_project_summary_projections.php'; break;
			case 'custom_details':
				$body_title = $_GET['custom_tab'];
				$include_files[] = 'edit_project_custom_details.php'; ?>
				<script>
				$(document).ready(function() {
					$('.main-screen .default_screen').scrollTop($('#custom_<?= config_safe_str($_GET['custom_heading']) ?>').position().top);
					$('#active_custom_tab').find('a').off('click').click(function() {
						var tab = this.href.split('custom_heading=')[1];
						$('.main-screen .default_screen').scrollTop($('#custom_'+$(this).data('configsafestr')).position().top + $('.main-screen .default_screen').scrollTop()).scroll();
						return false;
					});
					$('.main-screen .default_screen').scroll(function() {
						var heading = $('.default_screen [id^=custom]').filter(function() { return $(this).position().top < 10 }).last().attr('id').split('custom_')[1];
						$('#active_custom_tab li.active.blue').removeClass('active blue');
						$('#active_custom_tab').find('a[data-configsafestr='+heading+']').find('li').addClass('active blue');
					});
				});
				</script>
				<?php break;
			default:
				$body_title = '**NO_TITLE**';
				$include_files[] = 'edit_project_path.php'; break;
		} ?>
		<?php if($body_title != '**NO_TITLE**') {
			$quick_actions = explode(',',get_config($dbc, 'quick_action_icons'));
			$flag_label = '';
			if($project['flag_colour'] != '' && $project['flag_colour'] != 'FFFFFF') {
				if(in_array('flag_manual',$quick_actions)) {
					if(time() < strtotime($project['flag_start']) || time() > strtotime($project['flag_end'].' + 1 day')) {
						$project['flag_colour'] = '';
					} else {
						$flag_label = $project['flag_label'];
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
			<div class='standard-body-title' data-colour="<?= $project['flag_colour'] ?>" data-table="project" data-id-field="projectid" style="<?= $project['flag_colour'] != '' ? 'background-color: #'.$project['flag_colour'].';' : '' ?>">
				<h3 style="margin:12px 18px;">
                    <div class="pull-left" style="margin-top:7px;"><?= $body_title ?></div>
                    <div class="pull-left" style="height:30px; position:relative; margin:-6px 0 0 3px;">
                        <?php foreach(explode(',',$project['project_lead'].','.$project['project_colead'].','.$project['project_team']) as $project_staff) {
                            if($project_staff > 0) {
                                echo '<div class="pull-left">'.profile_id($dbc,$project_staff,false).'</div>';
                            }
                        } ?>
                    </div>
                    <div class="pull-right"><?php include('quick_actions.php'); ?></div>
                    <span class="flag-label"><?= $flag_label ?></span>
                    <div class="clearfix"></div>
                </h3>
			</div>
		<?php } ?>
		<div class='standard-dashboard-body-content <?= $body_title != '**NO_TITLE**' ? 'pad-top pad-left pad-right' : '' ?>'>
			<?php foreach($include_files as $i => $include_file) {
				include($include_file);
                if($i < count($include_files) - 1) {
                    echo '<div class="clearfix"></div><hr>';
                }
			} ?>
		</div>
	</div>
</div>
