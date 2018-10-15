<?php // Projects View
error_reporting(0);
include_once('../include.php'); ?>
<script src="project.js"></script>
<script>
$(document).ready(function() {
    <?php if(!empty($_GET['type']) && !isset($_GET['edit'])) { ?>
        selectType('<?= $_GET['type']?>');
    <?php } ?>
	$(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.main-screen .main-screen').not('.show-on-mob .main-screen').is(':visible')) {
			<?php if(isset($_GET['edit']) && $ticket_layout == 'Accordions') { ?>
				var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.standard-body').offset().top;
			<?php } else if(isset($_GET['fill_user_form'])) { ?>
				var available_height = 0;
			<?php } else { ?>
				var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
			<?php } ?>
			if(available_height > 200) {
				$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
				$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height).css('overflow-y','auto');
			}
		}
	}).resize();
});
</script>
</head>
<body>
<?php if(!IFRAME_PAGE) {
	include_once ('../navigation.php');
}
$project_type = filter_var($_GET['type'],FILTER_SANITIZE_STRING);
if($project_type == '') {
	$project_type = 'favourite';
}
$project_business = filter_var($_GET['businessid'],FILTER_SANITIZE_STRING);
$project_region = filter_var($_GET['region_name'],FILTER_SANITIZE_STRING);
$project_class = filter_var($_GET['classification'],FILTER_SANITIZE_STRING);
$project_classify = explode(',',get_config($dbc, "project_classify"));
include_once('../Project/config.php');
$project = [];
$label = ($tile == 'project' ? PROJECT_TILE : $project_tabs[$tile]);
if($_GET['edit'] > 0 || isset($_GET['fill_user_form'])) {
	$project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
	$label .= '</a>: '.($project['projecttype'] != '' && in_array('Types',$project_classify) ? '<a href="?tile_name='.$tile.'&type='.$project['projecttype'].'">'.$project_tabs[$project['projecttype']].'</a> - ' : '').' <a class="project_name" href="?edit='.$projectid.'">'.get_project_label($dbc, $project);
} else if(isset($_GET['edit'])) {
	$label .= '</a>: <a class="project_name" href="">New '.PROJECT_NOUN;
}
$salesid = isset($_GET['salesid']) ? $_GET['salesid'] : '';
if(!IFRAME_PAGE) { ?>
	<div class="container" <?= isset($_GET['fill_user_form']) ? 'style="padding: 0; margin: 0;"' : '' ?>>
		<div class="iframe_overlay" style="display:none; margin-top:-20px;">
			<div class="iframe">
				<div class="iframe_loading">Loading...</div>
				<iframe src=""></iframe>
			</div>
		</div>
		<div class="row">
			<div class="<?= !isset($_GET['fill_user_form']) ? 'main-screen' : '' ?> main_full_screen">
				<div class="tile-header standard-header">
					<div class="pull-right settings-block"><?php
					if($security['config'] > 0 && $_GET['settings']!='fields') {
							echo "<div class='pull-right gap-left'><a href='?settings=fields'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me' width='30' /></a></div>";
						}
						if($security['edit'] > 0) {
							if(in_array('Tickets',$tab_config)) {
								echo "<div class='pull-right gap-left'><a href='../Ticket/index.php?edit=0&from=".urlencode(WEBSITE_URL.'/Project/projects.php?'.$_SERVER['QUERY_STRING']).($_GET['edit'] > 0 ? '&projectid='.$_GET['edit'] : '')."' style='font-size: 0.5em;'><button class='btn brand-btn hide-titles-mob'>New ".TICKET_NOUN."</button>";
								echo "<img src='".WEBSITE_URL."/img/icons/ROOK-add-icon.png' class='show-on-mob' style='height: 2.5em;'></a></div>";
								echo "<div class='pull-right gap-left'><a href='?edit=0&type=".$project_type."' class='new-btn'><button class='btn brand-btn hide-titles-mob'>New ".PROJECT_NOUN."</button></a></div>";
							} else {
								echo "<div class='pull-right gap-left'><a href='?edit=0&type=".$project_type."' class='new-btn'><button class='btn brand-btn hide-titles-mob'>New ".PROJECT_NOUN."</button>";
								echo "<img src='".WEBSITE_URL."/img/icons/ROOK-add-icon.png' class='show-on-mob' style='height: 2.5em;'></a></div><div class='clearfix'></div>";
							}
							if(in_array('Scrum Tile',$tab_config)) {
								echo "<div class='pull-right gap-left'><a href='../Scrum/scrum.php?scrum_tab=company' style='font-size: 0.5em;'><button class='btn brand-btn hide-titles-mob'>Scrum</button></a></div>";
							}
							if(in_array('Ticket Tile',$tab_config)) {
								echo "<div class='pull-right gap-left'><a href='../Ticket/index.php' style='font-size: 0.5em;'><button class='btn brand-btn hide-titles-mob'>".TICKET_TILE."</button></a></div>";
							}
							if(in_array('Daysheet Tile',$tab_config)) {
								echo "<div class='pull-right gap-left'><a href='../Profile/daysheet.php' style='font-size: 0.5em;'><button class='btn brand-btn hide-titles-mob'>Planner</button></a></div>";
							}
						} ?>
						<!-- <img class="no-toggle statusIcon pull-right no-margin inline-img" title="" src="" /> -->
						<?php if($_GET['edit'] > 0 && ($_GET['tab'] == 'path' || ($_GET['tab'] == '' && in_array('Path',$tab_config)))) { ?>
							<img class="inline-img pull-right btn-horizontal-collapse" src="../img/icons/pie-chart.png">
						<?php } ?>
					</div>
					<div class="scale-to-fill"><h1 class="gap-left"><a href="?tile_name=<?= $tile ?>"><?= $label ?></a></h1></div>
					<?php if($_GET['edit'] > 0 && ($_GET['tab'] == 'path' || ($_GET['tab'] == '' && in_array('Path',$tab_config)))) { ?>
						<div class="notice double-gap-top double-gap-bottom popover-examples">
							<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
							<div class="col-sm-11"><span class="notice-name">NOTE: </span>This is where you can view all checklists, tickets, and tasks attached to each project, sorted into timelines depending on when they were scheduled for. You can move each item by clicking on the orange hand icon in the bottom right corner of the item and dragging to the new time block.</div>
							<div class="clearfix"></div>
						</div>
						<div class="collapsible-horizontal collapsed hide-titles-mob">
							<?php $summary_tickets = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) tickets, SUM(IF(`tickets`.`status`='Archive',1,0)) complete, SUM(TIME_TO_SEC(`max_time`)) est_ticket_time, SUM(`ticket_timer`) spent_ticket_time FROM `tickets` LEFT JOIN (SELECT `ticketid`, SUM(TIMEDIFF(`end_time`,`start_time`)) `ticket_timer` FROM `ticket_timer` WHERE `deleted` = 0 GROUP BY `ticketid`) timers ON `tickets`.`ticketid`=`timers`.`ticketid` WHERE `projectid`='$projectid' AND `deleted`=0"));
							$summary_workorders = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) workorders, SUM(IF(`workorder`.`status`='Archive',1,0)) complete, SUM(TIME_TO_SEC(`max_time`)) est_workorder_time, SUM(`workorder_timer`) spent_workorder_time FROM `workorder` LEFT JOIN (SELECT `workorderid`, SUM(TIMEDIFF(`end_time`,`start_time`)) `workorder_timer` FROM `workorder_timer` GROUP BY `workorderid`) timers ON `workorder`.`workorderid`=`timers`.`workorderid` WHERE `projectid`='$projectid'"));
							$summary_tasks = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) tasks, SUM(IF(`tasklist`.`status`='Done',1,0)) complete, SUM(TIME_TO_SEC(`work_time`)) task_time FROM `tasklist` WHERE `projectid`='$projectid' AND `deleted`=0")); ?>
							<?php if($summary_tasks['tasks'] > 0) { ?>
								<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 gap-top">
									<div class="summary-block">
										<a href="?edit=<?= $projectid ?>&tab=tasks"><span class="text-lg"><?= number_format($summary_tasks['tasks'],0) ?></span><br />Total <?= TASK_TILE ?></a>
									</div>
								</div>
							<?php } ?>
							<?php if($summary_tickets['tickets'] > 0) { ?>
								<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 gap-top">
									<div class="summary-block">
										<a href="?edit=<?= $projectid ?>&tab=tickets"><span class="text-lg"><?= number_format($summary_tickets['tickets'],0) ?></span><br />Total <?= TICKET_TILE ?></a>
									</div>
								</div>
							<?php } ?>
							<?php if($summary_workorders['workorders'] > 0) { ?>
								<div class="col-xs-6 col-sm-3 col-md-2 col-lg-2 gap-top">
									<div class="summary-block">
										<a href="?edit=<?= $projectid ?>&tab=workorders"><span class="text-lg"><?= number_format($summary_workorders['workorders'],0) ?></span><br />Total Work Orders</a>
									</div>
								</div>
							<?php } ?>
							<div class="col-xs-12 col-sm-3 col-md-2 col-lg-2 gap-top">
								<div class="summary-block">
									<span class="text-lg"><?= number_format(($summary_tickets['complete'] + $summary_workorders['complete'] + $summary_tasks['complete']) / ($summary_tickets['tickets'] + $summary_workorders['workorders'] + $summary_tasks['tasks']) * 100,0) ?>%</span><br />Complete
								</div>
							</div>
							<?php if($project['estimated_completed_date'] != '' && $project['estimated_completed_date'] != '0000-00-00') { ?>
								<div class="col-xs-12 col-sm-4 col-md-2 col-lg-2 gap-top">
									<div class="summary-block">
										<span class="text-lg"><?= date('M j', strtotime($project['estimated_completed_date'])) ?></span><br />Estimated Project Completion Date
									</div>
								</div>
							<?php } ?>
							<div class="col-xs-12 col-sm-4 col-md-2 col-lg-2 gap-top">
								<div class="summary-block">
									<span class="text-lg"><?= number_format($summary_tickets['spent_ticket_time'] + $summary_workorders['spent_workorder_time'] + $summary_tasks['task_time'],0) ?> / <?= number_format($summary_tickets['est_ticket_time'] + $summary_workorders['est_workorder_time'] + $summary_tasks['task_time'],0) ?></span><br />Hours Spent / Hours Estimated
								</div>
							</div>
						</div>
					<?php } ?>
					<div class="clearfix"></div>
				</div><!-- .tile-header -->

				<div class="clearfix"></div>
			<?php } ?>
			<?php if(isset($_GET['edit'])) {
				include('edit_projects.php');
			} else if(!empty($_GET['settings']) && $security['config'] > 0) {
				include('field_config.php');
			} else if(isset($_GET['fill_user_form'])) {
				include('fill_user_form.php');
			} else {
				include('project_dashboard.php');
			} ?>
			<?php if(!IFRAME_PAGE) { ?>
			</div>
			<div class="loading_overlay" style="display: none; margin-left: -15px; margin-top: -20px;"><div class="loading_wheel"></div></div>
		</div>
	</div>
<?php } ?>
<div class="clearfix"></div>
<?php include('../footer.php'); ?>
