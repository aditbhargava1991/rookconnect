<?php
/*
Inventory Listing
*/
include ('../include.php');
checkAuthorised('scrum');
if(empty($_GET['tab'])) {
	$_GET['tab'] = 'notes';
} ?>
<script>
<?php if(!IFRAME_PAGE) { ?>
	$(document).ready(function() {
		$(window).resize(function() {
			$('.main-screen').css('padding-bottom',0);
			if($('.main-screen .main-screen').not('.show-on-mob .main-screen').is(':visible')) {
				<?php if(isset($_GET['edit']) && $ticket_layout == 'Accordions') { ?>
					var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.standard-body').offset().top;
				<?php } else { ?>
					var available_height = window.innerHeight - $('footer:visible').outerHeight() - $('.sidebar:visible').offset().top;
				<?php } ?>
				if(available_height > 200) {
					$('.main-screen .main-screen').outerHeight(available_height).css('overflow-y','auto');
					$('.sidebar').outerHeight(available_height).css('overflow-y','auto');
				}
			}
			if($('.scrum_tickets ul').is(':visible')) {
				var height = $('.sidebar').offset().top + $('.sidebar').innerHeight() - $('.scrum_tickets').offset().top - 87;
				$('.scrum_tickets ul').css('display','inline-block').css('overflow-y','auto').outerHeight(height);
			}
		}).resize();
		$('.search_list').change(function() {
			window.location.replace('?tab=search&q='+encodeURIComponent(this.value));
		});
	});
<?php } ?>
function submitForm(thisForm) {
	if (!$('input[name="search_user_submit"]').length) {
		var input = $("<input>")
					.attr("type", "hidden")
					.attr("name", "search_user_submit").val("1");
		$('[name=form_sites]').append($(input));
	}

	$('[name=form_sites]').submit();
}
</script>
</head>
<body>
<?php include_once ('../navigation.php'); ?>
<div class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="ticket_iframe" src=""></iframe>
		</div>
	</div>
	<div class="row">
		<div class="main-screen">
			<div class="tile-header standard-header" style="<?= IFRAME_PAGE ? 'display:none;' : '' ?>">
				<div class="pull-right settings-block">&nbsp;</div>
				<div class="scale-to-fill">
					<h1 class="gap-left"><a href="?">Scrum</a><img class="no-toggle statusIcon pull-right no-margin inline-img small" title="" src="" data-original-title=""></h1>
				</div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

			<div class="clearfix"></div>
			<?php IF(!IFRAME_PAGE) { ?>
				<div class="tile-sidebar sidebar sidebar-override hide-titles-mob standard-collapsible">
					<ul>
						<li class="standard-sidebar-searchbox"><input type="text" class="form-control search_list" value="<?= $_GET['q'] ?>" placeholder="Search Scrum Notes"></li>
						<a href="?tab=notes"><li class="sidebar-higher-level <?= $_GET['tab'] == 'notes' ? 'active' : '' ?>">Notes</li></a>
						<li class="sidebar-higher-level <?= $_GET['tab'] == 'all' ? 'active' : '' ?>"><a class="<?= $_GET['tab'] == 'all' ? '' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#list_all">View All<span class="arrow"></span></a></li>
							<ul class="collapse <?= $_GET['tab'] == 'all' ? 'in' : '' ?>" id="list_all">
								<?php if(tile_enabled($dbc, 'ticket')['user_enabled'] > 0) {
									echo '<li class="sidebar-lower-level"><a class="'.('all' == $_GET['tab'] && 'tickets' == $_GET['subtab'] ? 'active' : '').'" href="?tab=all&subtab=tickets">'.TICKET_TILE.'</a></li>';
								}
								if(tile_enabled($dbc, 'tasks')['user_enabled'] > 0) {
									echo '<li class="sidebar-lower-level"><a class="'.('all' == $_GET['tab'] && 'tasks' == $_GET['subtab'] ? 'active' : '').'" href="?tab=all&subtab=tasks">Tasks</a></li>';
								} ?>
							</ul>
						</li>
						<li class="sidebar-higher-level <?= $_GET['tab'] == 'staff' ? 'active' : '' ?>"><a class="<?= $_GET['tab'] == 'staff' ? '' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#list_staff">Staff<span class="arrow"></span></a></li>
							<ul class="collapse <?= $_GET['tab'] == 'staff' ? 'in' : '' ?>" id="list_staff">
								<?php foreach(sort_contacts_query($dbc->query("SELECT `contactid`,`first_name`,`last_name`,`name` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY)) as $staff) {
									echo '<li class="sidebar-lower-level"><a class="'.('staff' == $_GET['tab'] && $staff['contactid'] == $_GET['subtab'] ? 'active' : '').'" href="?tab=staff&subtab='.$staff['contactid'].'">'.$staff['full_name'].'</a></li>';
								} ?>
							</ul>
						</li>
						<li class="sidebar-higher-level <?= $_GET['tab'] == 'status' ? 'active' : '' ?>"><a class="<?= $_GET['tab'] == 'status' ? '' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#list_status">Status<span class="arrow"></span></a></li>
							<ul class="collapse <?= $_GET['tab'] == 'status' ? 'in' : '' ?>" id="list_status">
								<?php foreach(array_filter(array_unique(explode(',',get_config($dbc,'ticket_status').','.get_config($dbc,'task_status')))) as $status) {
									$id = config_safe_str($status);
									echo '<li class="sidebar-lower-level"><a class="'.('status' == $_GET['tab'] && $id == $_GET['subtab'] ? 'active' : '').'" href="?tab=status&subtab='.$id.'">'.$status.'</a></li>';
								} ?>
							</ul>
						</li>
						<li class="sidebar-higher-level <?= $_GET['tab'] == 'project' ? 'active' : '' ?>"><a class="<?= $_GET['tab'] == 'project' ? '' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-target="#list_project"><?= PROJECT_TILE ?><span class="arrow"></span></a></li>
							<ul class="collapse <?= $_GET['tab'] == 'project' ? 'in' : '' ?>" id="list_project">
								<?php $project_list = $dbc->query("SELECT * FROM `project` WHERE `deleted`=0 AND `status` != 'Archive' ORDER BY `projectid` DESC");
								while($project = $project_list->fetch_assoc()) {
									echo '<li class="sidebar-lower-level"><a class="'.('project' == $_GET['tab'] && $project['projectid'] == $_GET['subtab'] ? 'active' : '').'" href="?tab=project&subtab='.$project['projectid'].'">'.get_project_label($dbc,$project).'</a></li>';
								} ?>
							</ul>
						</li>
					</ul>
				</div>
			<?php } ?>
			<div class="scale-to-fill has-main-screen">
				<div class="main-screen standard-body form-horizontal" id="no-more-tables">
					<?php include('scrum_display.php'); ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<?php include_once('../footer.php'); ?>
