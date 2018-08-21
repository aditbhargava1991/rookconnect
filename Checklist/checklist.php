<?php
/*
Inventory Listing
*/
include ('../include.php');
error_reporting(0);
if($_GET['archivetab'] > 0) {
	$tabid = $_GET['archivetab'];
    $date_of_archival = date('Y-m-d');
	mysqli_query($dbc, "UPDATE `checklist_subtab` SET `deleted`=1, `date_of_archival` = '$date_of_archival' WHERE `subtabid`='$tabid'");
	unset($_GET['archivetab']);
}
$security = get_security($dbc, 'checklist'); ?>
<style>
@media (max-width:767px) {
    .show-on-mob2 { display:block; }
}
</style>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		$('.has-main-screen .main-screen').outerHeight($(window).height() - $('.has-main-screen').offset().top - $('footer').outerHeight());
		$('.sidebar').outerHeight($(window).height() - $('.sidebar ul').offset().top - $('footer').outerHeight());
	}).resize();
});
function filter_checklists(string) {
	if(string == '') {
		$('.option-list a').hide().filter(function() { return $(this).data('visible') == 'visible'; }).show();
	} else {
		$('.option-list a').hide().filter(function() { return ($(this).data('subtab')+$(this).data('users')+$(this).data('name')+$(this).data('project')).toLowerCase().includes(string.toLowerCase()); }).show();
	}
}
function add_remove_hidden_category(button) {
	$.ajax({
		method: 'POST',
		url: 'checklist_ajax.php?fill=mark_hidden',
		data: { category: $(button).data('category') },
		success: function(result) {
			if($('a:contains(Show Main Categories)').length == 0) {
				if($(button).closest('.panel').length == 0) {
					$(button).closest('a').toggle();
				} else {
					$(button).closest('.panel').toggle();
				}
			}
			if($(button).closest('.panel').length == 0) {
				$(button).closest('a').toggleClass('non-visible');
			} else {
				$(button).closest('.panel').toggleClass('non-visible');
			}
			button.src = (button.src.includes('/img/remove.png') ? '../img/plus.png' : '../img/remove.png');console.log(result);
		}
	});
}
</script>
</head>
<body>
<?php include_once ('../navigation.php');
checkAuthorised('checklist');
$tab_config = get_config($dbc, 'checklist_tabs_' . $_SESSION['contactid']);
$user_settings = get_user_settings();
$hidden_categories = explode(',',$user_settings['checklist_hidden']);
$tab_counts = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(IF(`checklistid` IN (0".implode(',',array_filter(explode(',',$user_settings['checklist_fav'])))."),1,0)) favourites,
	SUM(IF(`assign_staff`=',{$_SESSION['contactid']},',1,0)) private,
	SUM(IF(`assign_staff` LIKE '%,{$_SESSION['contactid']},%',IF(`assign_staff`!=',{$_SESSION['contactid']},',1,0),0)) shared,
	SUM(IF(`projectid`>0,1,IF(`client_projectid`>0,1,0))) project, SUM(IF(`assign_staff` LIKE '%ALL%',1,0)) company,
	SUM(IF(`checklist_type`='daily',1,0)) daily, SUM(IF(`checklist_type`='weekly',1,0)) weekly,
	SUM(IF(`checklist_type`='monthly',1,0)) monthly, SUM(IF(`checklist_type`='ongoing',1,0)) ongoing
	FROM `checklist` WHERE (`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `assign_staff` LIKE '%ALL%') AND `deleted`=0"));
$tab_counts['equipment'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `item_checklist` WHERE `deleted`=0 AND `checklist_item`='equipment'"))['count'];
$tab_counts['inventory'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `item_checklist` WHERE `deleted`=0 AND `checklist_item`='inventory'"))['count'];
if(empty($_GET['subtabid']) && empty($_GET['edit']) && empty($_GET['view']) && empty($_GET['reports'])) {
	$_GET['subtabid'] = 'favourites';
} ?>
<?php if($_GET['iframe_slider'] != 1) { ?>
<div class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="checklist_iframe" src=""></iframe>
		</div>
	</div>
	<div class="iframe_holder" style="display:none;">
		<img src="<?= WEBSITE_URL ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
		<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
		<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
	</div>
	<div class="row hide_on_iframe">
		<div class="main-screen">
			<div class="tile-header standard-header"><div class="scale-to-fill">
				<h1 class="gap-left <?= (empty($_GET['view']) && empty($_GET['edit']) && empty($_GET['edittab']) ? '' : 'hide-titles-mob') ?>"><a href="?">Checklists</a>
					<?php
					if($security['config'] == 1) {
						echo '<div class="pull-right">';
							echo '<span class="popover-examples list-inline" style="margin:0 0 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes will appear on your dashboard.">';
							echo '<img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';
							echo '<a href="field_config.php" class="mobile-block"><img title="Tile Settings" src="../img/icons/settings-4.png" class="settings-classic wiggle-me inline-img" width="30" style="margin-top:0"></a>';
						echo '</div>';
					}
					if($security['edit'] > 0) { ?>
						<div class="pull-right show-on-mob">
							<span class="popover-examples list-inline" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Checklist."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span>
							<a href="?edit=NEW"><img src="../img/icons/ROOK-add-icon.png" class="small inline-img"></a>
						</div><?php
					} ?>
                    
					<div class="pull-right hide-titles-mob">
						<a href="" class="btn brand-btn mobile-block gap-bottom pull-right offset-right-5" onclick="$('.not_filter').toggle(); $('.filter_box').toggle().focus(); $(this).text($(this).text() == 'Filter Checklists' ? 'Close Filter Options' : 'Filter Checklists'); filter_checklists(''); return false;">Filter Checklists</a>
					</div>

					<?php if($security['edit'] > 0) { ?>
						<div class="pull-right not_filter hide-titles-mob">
							<a href="?edit=NEW" class="btn brand-btn mobile-block gap-bottom pull-right offset-right-5">Add Checklist</a>
							<span class="popover-examples list-inline pull-right" style="margin:0 2px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Checklist."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span>
						</div>
					<?php } ?>

					<div class='pull-right not_filter report_link'>
						<span class='popover-examples list-inline'><a data-toggle='tooltip' data-placement='top' title='Click here to see all Checklist activity.'><img src='<?= WEBSITE_URL ?>/img/info.png' width='20'></a></span>
						<?php if ( strpos($tab_config, 'reporting') !== false && check_subtab_persmission($dbc, 'checklist', ROLE, 'reporting')===true ) { ?>
							<a href='?reports=view'><img src="../img/icons/pie-chart.png" alt="Reporting" title="Reporting" class="show-on-mob offset-right-5" /></a>
                            <a href='?reports=view'><button type='button' class='btn brand-btn mobile-block icon-pie-chart hide-titles-mob <?= (!empty($_GET['reports']) ? 'active_tab' : '') ?>'>Reporting</button></a>
						<?php } else { ?>
							<img src="../img/icons/pie-chart.png" alt="Reporting" title="Reporting" class="show-on-mob offset-right-5" />
                            <button type="button" class="btn disabled-btn mobile-block icon-pie-chart hide-titles-mob">Reporting</button>
						<?php } ?>
					</div>

                    <?php
                    $get_checklist = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(checklistid) AS checklistid FROM checklist WHERE checklist_tile=1"));
                    if($get_checklist['checklistid'] > 0) {
                       echo 	'<a href="checklist_tile.php" class="btn brand-btn mobile-block gap-bottom pull-right offset-right-5">Back to Dashboard</a>';
                    }
                    ?>

					<label class="filter_box" style="display:none;">Type to Filter Checklists by Name, User, <?= $_GET['subtabid'] == 'project' ? 'Project, ' : '' ?>or Category:</label>
					<input type="text" class="filter_box form-control pull-right" style="display:none;" onkeyup="filter_checklists(this.value);" />
					<div class="clearfix"></div>

				</h1><?php

                $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='checklist_checklist'"));
                $note = $notes['note'];

                if ( !empty($note) && !$_GET['reports'] ) { ?>
                    <div class="notice double-gap-bottom popover-examples hide-titles-mob">
                        <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
                        <div class="col-sm-11">
                            <span class="notice-name">NOTE:</span>
                            <?= $note; ?>
                        </div>
                        <div class="clearfix"></div>
                    </div><?php
                } ?>

                <!--
				<div class="notice double-gap-bottom popover-examples">
					<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
					<div class="col-sm-11"><span class="notice-name">NOTE:</span>
										<?php
											/* $notes = mysqli_fetch_assoc(mysqli_query($dbc, "select note from notes_setting where subtab = 'checklist_checklist'"));
											$note = $notes['note']; */
										?>
					<?php //(!empty($_GET['reports']) ? "Categories, Checklist Types, Checklist Names, and Date Ranges can be selected below to narrow down the reports. Checklist Names will appear when a Category or Checklist Type is selected." : $note  ) ?></div>
					<div class="clearfix"></div>
				</div>
                -->
			</div></div>

			<!-- Mobile View -->
            <div class="sidebar show-on-mob panel-group block-panels col-xs-12" <?= (empty($_GET['view']) && empty($_GET['edit']) && empty($_GET['edittab']) && empty($_GET['reports']) ? '' : 'style="display:none;"') ?> id="category_accordions">
				<div class="panel panel-default <?= (in_array('favourites',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_favourites" >
								<span style="display: inline-block; width: calc(100% - 6em);">Favourites</span><span class="glyphicon glyphicon-plus"></span><img src="<?= (in_array('favourites',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0.5em;" data-category="favourites" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['favourites'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_favourites" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'favourites'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div>
				<?php if(strpos($tab_config,'private') !== FALSE) { ?>
				<div class="panel panel-default <?= (in_array('private',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_private" >
								<span style="display: inline-block; width: calc(100% - 6em);">Private</span><span class="glyphicon glyphicon-plus"></span><img src="<?= (in_array('private',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0.5em;" data-category="private" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['private'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_private" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'private'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'shared') !== FALSE) { ?>
				<div class="panel panel-default <?= (in_array('shared',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_shared" >
								<span style="display: inline-block; width: calc(100% - 6em);">Shared</span><span class="glyphicon glyphicon-plus"></span><img src="<?= (in_array('shared',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0.5em;" data-category="shared" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['shared'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_shared" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'shared'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'project') !== FALSE) { ?>
				<div class="panel panel-default <?= (in_array('project',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_project_category" >
								<span style="display: inline-block; width: calc(100% - 6em);">Project</span><span class="glyphicon glyphicon-plus"></span><img src="<?= (in_array('project',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0.5em;" data-category="project" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['project'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_project_category" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'project'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'company') !== FALSE) { ?>
				<div class="panel panel-default <?= (in_array('company',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_company_category" >
								<span style="display: inline-block; width: calc(100% - 6em);">Company</span><span class="glyphicon glyphicon-plus"></span><img src="<?= (in_array('company',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0.5em;" data-category="company" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['company'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_company_category" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'company'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'ongoing') !== FALSE) { ?>
				<div class="panel panel-default <?= (in_array('ongoing',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_ongoing" >
								<span style="display: inline-block; width: calc(100% - 6em);">Ongoing</span><span class="glyphicon glyphicon-plus"></span><img src="<?= (in_array('ongoing',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0.5em;" data-category="ongoing" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['ongoing'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ongoing" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'ongoing'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'daily') !== FALSE) { ?>
				<div class="panel panel-default <?= (in_array('daily',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_daily" >
								<span style="display: inline-block; width: calc(100% - 6em);">Daily</span><span class="glyphicon glyphicon-plus"></span><img src="<?= (in_array('daily',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0.5em;" data-category="daily" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['daily'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_daily" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'daily'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'weekly') !== FALSE) { ?>
				<div class="panel panel-default <?= (in_array('weekly',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_weekly" >
								<span style="display: inline-block; width: calc(100% - 6em);">Weekly</span><span class="glyphicon glyphicon-plus"></span><img src="<?= (in_array('weekly',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0.5em;" data-category="weekly" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['weekly'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_weekly" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'weekly'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'monthly') !== FALSE) { ?>
				<div class="panel panel-default <?= (in_array('monthly',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_monthly" >
								<span style="display: inline-block; width: calc(100% - 6em);">Monthly</span><span class="glyphicon glyphicon-plus"></span><img src="<?= (in_array('monthly',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0.5em;" data-category="monthly" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['monthly'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_monthly" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'monthly'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php $query_retrieve_subtabs = mysqli_query($dbc, "SELECT `checklist_subtab`.`subtabid`, `checklist_subtab`.`name`, COUNT(`checklist`.`checklistid`) subtab_count FROM `checklist_subtab` LEFT JOIN `checklist` ON `checklist_subtab`.`subtabid`=`checklist`.`subtabid` AND (`checklist`.`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `checklist`.`assign_staff`=',ALL,') AND `checklist`.`deleted`=0 WHERE (`checklist_subtab`.`created_by` = ".$_SESSION['contactid']." OR `checklist_subtab`.`shared` LIKE '%,".$_SESSION['contactid'].",%' OR `checklist_subtab`.`shared` LIKE ',ALL,') GROUP BY `checklist_subtab`.`subtabid`, `checklist_subtab`.`name`");
				while ($row = mysqli_fetch_array($query_retrieve_subtabs)) { ?>
					<div class="panel panel-default <?= (in_array($row['subtabid'],$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_subtab_<?= $row['subtabid'] ?>" >
									<span style="display: inline-block; width: calc(100% - 6em);"><?= $row['name'] ?></span><span class="glyphicon glyphicon-plus"></span><img src="<?= (in_array($row['subtabid'],$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0.5em;" data-category="<?= $row['subtabid'] ?>" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right' style='margin: 0 0.5em;'><?= $row['subtab_count'] ?></span>
								</a>
							</h4>
						</div>

						<div id="collapse_subtab_<?= $row['subtabid'] ?>" class="panel-collapse collapse">
							<div class="panel-body"><?php
								$links_for = $row['subtabid'];
								include('link_checklists.php'); ?>
							</div>
						</div>
					</div>
				<?php } ?>
				<?php if(strpos($tab_config,'equipment') !== FALSE) { ?>
				<div class="panel panel-default <?= (in_array('equipment',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_equipment" >
								<span style="display: inline-block; width: calc(100% - 6em);">Equipment</span><span class="glyphicon glyphicon-plus"></span><img src="<?= (in_array('equipment',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0.5em;" data-category="equipment" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['equipment'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_equipment" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'equipment'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'inventory') !== FALSE) { ?>
				<div class="panel panel-default <?= (in_array('inventory',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_inventory" >
								<span style="display: inline-block; width: calc(100% - 6em);">Inventory</span><span class="glyphicon glyphicon-plus"></span><img src="<?= (in_array('inventory',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0.5em;" data-category="inventory" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['inventory'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_inventory" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'inventory'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(count($hidden_categories) > 0) { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a href="" onclick="$('.non-visible').toggle(); $(this).text($(this).text() == 'Show All Categories' ? 'Show Main Categories' : 'Show All Categories'); return false;">Show All Categories</a>
							</h4>
						</div>
					</div>
				<?php } ?>
			</div>
            
            <!-- Desktop View -->
			<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
				<ul>
					<a href="?subtabid=favourites" class="<?= (in_array('favourites',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'favourites' ? 'class="active blue"' : '' ?>>Favourites<img src="<?= (in_array('favourites',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0 0 0.5em;" data-category="favourites" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right'><?= $tab_counts['favourites'] ?></span></li></a>
					<?php if(strpos($tab_config,'private') !== FALSE) { ?><a href="?subtabid=private" class="<?= (in_array('private',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'private' ? 'class="active blue"' : '' ?>>Private<img src="<?= (in_array('private',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0 0 0.5em;" data-category="private" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right'><?= $tab_counts['private'] ?></span></li></a><?php } ?>
					<?php if(strpos($tab_config,'shared') !== FALSE) { ?><a href="?subtabid=shared" class="<?= (in_array('shared',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'shared' ? 'class="active blue"' : '' ?>>Shared<img src="<?= (in_array('shared',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0 0 0.5em;" data-category="shared" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right'><?= $tab_counts['shared'] ?></span></li></a><?php } ?>
					<?php if(strpos($tab_config,'project') !== FALSE) { ?><a href="?subtabid=project" class="<?= (in_array('project',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'project' ? 'class="active blue"' : '' ?>>Project<img src="<?= (in_array('project',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0 0 0.5em;" data-category="project" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right'><?= $tab_counts['project'] ?></span></li></a><?php } ?>
					<?php if(strpos($tab_config,'company') !== FALSE) { ?><a href="?subtabid=company" class="<?= (in_array('company',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'company' ? 'class="active blue"' : '' ?>>Company<img src="<?= (in_array('company',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0 0 0.5em;" data-category="company" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right'><?= $tab_counts['company'] ?></span></li></a><?php } ?>
					<?php if(strpos($tab_config,'ongoing') !== FALSE) { ?><a href="?subtabid=ongoing" class="<?= (in_array('ongoing',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'ongoing' ? 'class="active blue"' : '' ?>>Ongoing<img src="<?= (in_array('ongoing',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0 0 0.5em;" data-category="ongoing" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right'><?= $tab_counts['ongoing'] ?></span></li></a><?php } ?>
					<?php if(strpos($tab_config,'daily') !== FALSE) { ?><a href="?subtabid=daily" class="<?= (in_array('daily',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'daily' ? 'class="active blue"' : '' ?>>Daily<img src="<?= (in_array('daily',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0 0 0.5em;" data-category="daily" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right'><?= $tab_counts['daily'] ?></span></li></a><?php } ?>
					<?php if(strpos($tab_config,'weekly') !== FALSE) { ?><a href="?subtabid=weekly" class="<?= (in_array('weekly',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'weekly' ? 'class="active blue"' : '' ?>>Weekly<img src="<?= (in_array('weekly',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0 0 0.5em;" data-category="weekly" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right'><?= $tab_counts['weekly'] ?></span></li></a><?php } ?>
					<?php if(strpos($tab_config,'monthly') !== FALSE) { ?><a href="?subtabid=monthly" class="<?= (in_array('monthly',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'monthly' ? 'class="active blue"' : '' ?>>Monthly<img src="<?= (in_array('monthly',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0 0 0.5em;" data-category="monthly" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right'><?= $tab_counts['monthly'] ?></span></li></a><?php } ?>
					<?php $query_retrieve_subtabs = mysqli_query($dbc, "SELECT `checklist_subtab`.`subtabid`, `checklist_subtab`.`name`, COUNT(`checklist`.`checklistid`) subtab_count FROM `checklist_subtab` LEFT JOIN `checklist` ON `checklist_subtab`.`subtabid`=`checklist`.`subtabid` AND (`checklist`.`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `checklist`.`assign_staff`=',ALL,') AND `checklist`.`deleted`=0 WHERE (`checklist_subtab`.`created_by` = ".$_SESSION['contactid']." OR `checklist_subtab`.`shared` LIKE '%,".$_SESSION['contactid'].",%' OR `checklist_subtab`.`shared` LIKE ',ALL,') GROUP BY `checklist_subtab`.`subtabid`, `checklist_subtab`.`name`");
					while ($row = mysqli_fetch_array($query_retrieve_subtabs)) {
						echo "<a href='?subtabid={$row['subtabid']}' class='".(in_array($row['subtabid'],$hidden_categories) ? "non-visible' style='display:none;" : '')."'><li ".($_GET['subtabid'] == $row['subtabid'] ? 'class="active blue"' : '').">{$row['name']}<img src='".(in_array($row['subtabid'],$hidden_categories) ? '../img/plus.png' : '../img/remove.png')."' class='pull-right' style='height:1em; margin: 0 0 0 0.5em;' data-category='{$row['subtabid']}' onclick='add_remove_hidden_category(this); return false;'><span class='pull-right'>{$row['subtab_count']}</span></li></a>";
					} ?>
					<?php if(strpos($tab_config,'equipment') !== FALSE) { ?><a href="?subtabid=equipment" class="<?= (in_array('equipment',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'equipment' ? 'class="active blue"' : '' ?>>Equipment<img src="<?= (in_array('equipment',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0 0 0.5em;" data-category="equipment" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right'><?= $tab_counts['equipment'] ?></span></li></a><?php } ?>
					<?php if(strpos($tab_config,'inventory') !== FALSE) { ?><a href="?subtabid=inventory" class="<?= (in_array('inventory',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'inventory' ? 'class="active blue"' : '' ?>>Inventory<img src="<?= (in_array('inventory',$hidden_categories) ? '../img/plus.png' : '../img/remove.png') ?>" class="pull-right" style="height:1em; margin: 0 0 0 0.5em;" data-category="inventory" onclick="add_remove_hidden_category(this); return false;"><span class='pull-right'><?= $tab_counts['inventory'] ?></span></li></a><?php } ?>
					<?php if(count($hidden_categories) > 0) { ?><a href="" onclick="$('.non-visible').toggle(); $(this).find('li').text($(this).find('li').text() == 'Show All Categories' ? 'Show Main Categories' : 'Show All Categories'); return false;"><li>Show All Categories</li></a><?php } ?>
					<?php if($security['edit'] > 0) { ?><a href="?edittab=NEW"><li>Add New Category</li></a><?php } ?>
				</ul>
			</div>
<?php } ?>
			<div class="scale-to-fill has-main-screen <?= (empty($_GET['view']) && empty($_GET['edit']) && empty($_GET['edittab']) && empty($_GET['reports']) ? 'hide-titles-mob' : '') ?>">
				<div class="checklist_screen main-screen standard-body" data-querystring='<?= $_SERVER['QUERY_STRING'] ?>'><?php
                if(!empty($_GET['view'])) {
					include('view_checklist.php');
				} else if(!empty($_GET['edit'])) {
					include('edit_checklist.php');
				} else if(!empty($_GET['edittab'])) {
					include('edit_subtabs.php');
				} else if(!empty($_GET['reports'])) {
					include('reporting.php');
				} else if(!empty($_GET['item_view'])) {
					$checklistid = filter_var($_GET['item_view'],FILTER_SANITIZE_STRING);
					$checklist = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `item_checklist` WHERE `checklistid`='$checklistid'"));
					include('item_checklist_view.php');
				} else {
					include('list_checklists.php');
				} ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include ('../footer.php'); ?>
