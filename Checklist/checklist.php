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
@supports (zoom:2) {
	input[type=checkbox] { zoom:2; }
}
@supports not (zoom:2) {
	input[type=checkbox] { transform:scale(2); margin:15px; }
}
.large-checkbox-label { font-weight:normal; margin-top:5px; vertical-align:top; }
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
    
    $('input[name=search]').keypress(function(event) {
        if (event.which == 13) {
            event.preventDefault();
            $("form").submit();
        }
    });
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
$remove_hidden = mysqli_query($dbc, "UPDATE user_settings SET checklist_hidden = NULL");
$tab_counts = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT SUM(IF(`checklistid` IN (0".implode(',',array_filter(explode(',',$user_settings['checklist_fav'])))."),1,0)) favourites,
	SUM(IF(`assign_staff`=',{$_SESSION['contactid']},',1,0)) private,
	SUM(IF(`assign_staff` LIKE '%,{$_SESSION['contactid']},%',IF(`assign_staff`!=',{$_SESSION['contactid']},',1,0),0)) shared,
	SUM(IF(`projectid`>0,1,IF(`client_projectid`>0,1,0))) project, SUM(IF(`assign_staff` LIKE '%ALL%',1,0)) company,
	SUM(IF(`checklist_type`='daily',1,0)) daily, SUM(IF(`checklist_type`='weekly',1,0)) weekly,
	SUM(IF(`checklist_type`='monthly',1,0)) monthly, SUM(IF(`checklist_type`='ongoing',1,0)) ongoing
	FROM `checklist` WHERE (`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `assign_staff` LIKE '%ALL%') AND `deleted`=0"));
$tab_counts['equipment'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `item_checklist` WHERE `deleted`=0 AND `checklist_item`='equipment'"))['count'];
$tab_counts['inventory'] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) `count` FROM `item_checklist` WHERE `deleted`=0 AND `checklist_item`='inventory'"))['count'];
if(empty($_GET['subtabid']) && empty($_GET['edit']) && empty($_GET['view']) && empty($_GET['reports']) && empty($_GET['search'])) {
	$_GET['subtabid'] = 'favourites';
    //$_GET['categories'] = 'all';
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
			<div class="tile-header standard-header">
                <div class="row">
                    <div class="col-xs-6 col-sm-7"><h1 class="<?= (empty($_GET['view']) && empty($_GET['edit']) && empty($_GET['edittab']) ? '' : '') ?>"><a href="?">Checklists</a></h1></div>
                    <div class="col-xs-6 col-sm-5"><?php
                        if($security['config'] == 1) {
                            echo '<div class="pull-right">';
                                /*echo '<span class="popover-examples list-inline hide-titles-mob" style="margin:0 0 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes will appear on your dashboard.">';
                                echo '<img src="' . WEBSITE_URL . '/img/info.png" width="20"></a></span>';*/
                                echo '<a href="field_config.php" class="mobile-block"><img title="Tile Settings" src="../img/icons/settings-4.png" class="no-toggle settings-classic wiggle-me offset-top-12 gap-right" width="30"></a>';
                            echo '</div>';
                        }
                        if($security['edit'] > 0) { ?>
                            <div class="pull-right show-on-mob offset-top-12 offset-left-5 gap-right">
                                <!--<span class="popover-examples list-inline" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Checklist."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span>-->
                                <a href="?edit=NEW"><img src="../img/icons/ROOK-add-icon.png" class="no-toggle" title="Add Checklist"></a>
                            </div><?php
                        } ?>
                        
                        <!--
                        <div class="pull-right hide-titles-mob">
                            <a href="" class="btn brand-btn mobile-block gap-bottom pull-right offset-right-5" onclick="$('.not_filter').toggle(); $('.filter_box').toggle().focus(); $(this).text($(this).text() == 'Filter Checklists' ? 'Close Filter Options' : 'Filter Checklists'); filter_checklists(''); return false;">Filter Checklists</a>
                        </div>
                        -->

                        <?php if($security['edit'] > 0) { ?>
                            <div class="pull-right not_filter hide-titles-mob gap-top">
                                <a href="?edit=NEW" class="btn brand-btn mobile-block pull-right gap-right">Add Checklist</a>
                                <span class="popover-examples list-inline pull-right" style="margin:5px 2px 0 5px;"><a data-toggle="tooltip" data-placement="top" title="Click here to add a Checklist."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span>
                            </div>
                        <?php } ?>

                        <div class='pull-right not_filter report_link'>
                            <!-- <span class='popover-examples list-inline  hide-titles-mob'><a data-toggle='tooltip' data-placement='top' title='Click here to see all Checklist activity.'><img src='<?= WEBSITE_URL ?>/img/info.png' width='20'></a></span> -->
                            <?php if ( strpos($tab_config, 'reporting') !== false && check_subtab_persmission($dbc, 'checklist', ROLE, 'reporting')===true ) { ?>
                                <a href='?reports=view'><img src="../img/icons/pie-chart.png" alt="Reporting" title="Reporting" class="no-toggle offset-top-15 offset-right-5" /></a>
                                <!-- <a href='?reports=view'><button type='button' class='btn brand-btn mobile-block icon-pie-chart hide-titles-mob gap-top <?= (!empty($_GET['reports']) ? 'active_tab' : '') ?>'>Reporting</button></a> -->
                            <?php } else { ?>
                                <img src="../img/icons/pie-chart.png" alt="Reporting" title="Reporting" class="cursor-hand no-toggle offset-top-15 offset-right-5" />
                                <!-- <button type="button" class="btn disabled-btn mobile-block icon-pie-chart hide-titles-mob gap-top">Reporting</button> -->
                            <?php } ?>
                        </div>

                        <?php
                        /* $get_checklist = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(checklistid) AS checklistid FROM checklist WHERE checklist_tile=1"));
                        if($get_checklist['checklistid'] > 0) {
                           echo 	'<a href="checklist_tile.php" class="btn brand-btn mobile-block gap-bottom pull-right offset-right-5">Back to Dashboard</a>';
                        } */
                        ?>

                        <!--
                        <label class="filter_box" style="display:none;">Type to Filter Checklists by Name, User, <?= $_GET['subtabid'] == 'project' ? 'Project, ' : '' ?>or Category:</label>
                        <input type="text" class="filter_box form-control pull-right" style="display:none;" onkeyup="filter_checklists(this.value);" />
                        -->
                        <div class="clearfix"></div>
                    </div>
                </div><?php

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
			</div>

			<!-- Mobile View -->
            <div class="sidebar show-on-mob panel-group block-panels col-xs-12 auto-height" <?= (empty($_GET['view']) && empty($_GET['edit']) && empty($_GET['edittab']) && empty($_GET['reports']) ? '' : 'style="display:none;"') ?> id="category_accordions">
				<div class="panel panel-default <?= (in_array('favourites',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_favourites" >
								<span style="display: inline-block; width: calc(100% - 6em);">Favourites</span><span class="glyphicon glyphicon-plus"></span><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['favourites'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_favourites" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'favourites'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div>
				<?php if(strpos($tab_config,'private') !== false) { ?>
				<div class="panel panel-default <?= (in_array('private',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_private" >
								<span style="display: inline-block; width: calc(100% - 6em);">Private</span><span class="glyphicon glyphicon-plus"></span><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['private'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_private" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'private'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'shared') !== false) { ?>
				<div class="panel panel-default <?= (in_array('shared',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_shared" >
								<span style="display: inline-block; width: calc(100% - 6em);">Shared</span><span class="glyphicon glyphicon-plus"></span><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['shared'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_shared" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'shared'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'project') !== false) { ?>
				<div class="panel panel-default <?= (in_array('project',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_project_category" >
								<span style="display: inline-block; width: calc(100% - 6em);">Project</span><span class="glyphicon glyphicon-plus"></span><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['project'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_project_category" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'project'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'company') !== false) { ?>
				<div class="panel panel-default <?= (in_array('company',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_company_category" >
								<span style="display: inline-block; width: calc(100% - 6em);">Company</span><span class="glyphicon glyphicon-plus"></span><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['company'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_company_category" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'company'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'ongoing') !== false) { ?>
				<div class="panel panel-default <?= (in_array('ongoing',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_ongoing" >
								<span style="display: inline-block; width: calc(100% - 6em);">Ongoing</span><span class="glyphicon glyphicon-plus"></span><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['ongoing'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_ongoing" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'ongoing'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'daily') !== false) { ?>
				<div class="panel panel-default <?= (in_array('daily',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_daily" >
								<span style="display: inline-block; width: calc(100% - 6em);">Daily</span><span class="glyphicon glyphicon-plus"></span><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['daily'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_daily" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'daily'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'weekly') !== false) { ?>
				<div class="panel panel-default <?= (in_array('weekly',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_weekly" >
								<span style="display: inline-block; width: calc(100% - 6em);">Weekly</span><span class="glyphicon glyphicon-plus"></span><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['weekly'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_weekly" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'weekly'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'monthly') !== false) { ?>
				<div class="panel panel-default <?= (in_array('monthly',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_monthly" >
								<span style="display: inline-block; width: calc(100% - 6em);">Monthly</span><span class="glyphicon glyphicon-plus"></span><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['monthly'] ?></span>
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
									<span style="display: inline-block; width: calc(100% - 6em);"><?= $row['name'] ?></span><span class="glyphicon glyphicon-plus"></span><span class='pull-right' style='margin: 0 0.5em;'><?= $row['subtab_count'] ?></span>
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
				<?php if(strpos($tab_config,'equipment') !== false) { ?>
				<div class="panel panel-default <?= (in_array('equipment',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_equipment" >
								<span style="display: inline-block; width: calc(100% - 6em);">Equipment</span><span class="glyphicon glyphicon-plus"></span><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['equipment'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_equipment" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'equipment'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<?php if(strpos($tab_config,'inventory') !== false) { ?>
				<div class="panel panel-default <?= (in_array('inventory',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_inventory" >
								<span style="display: inline-block; width: calc(100% - 6em);">Inventory</span><span class="glyphicon glyphicon-plus"></span><span class='pull-right' style='margin: 0 0.5em;'><?= $tab_counts['inventory'] ?></span>
							</a>
						</h4>
					</div>

					<div id="collapse_inventory" class="panel-collapse collapse">
						<div class="panel-body">
							<?php $links_for = 'inventory'; include('link_checklists.php'); ?>
						</div>
					</div>
				</div><?php } ?>
				<!--
                <?php if(count($hidden_categories) > 0) { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a href="" onclick="$('.non-visible').toggle(); $(this).text($(this).text() == 'Show All Categories' ? 'Show Main Categories' : 'Show All Categories'); return false;">Show All Categories</a>
							</h4>
						</div>
					</div>
				<?php } ?>
                -->
			</div>
            
            <!-- Desktop View -->
			<div class="tile-sidebar sidebar hide-titles-mob standard-collapsible">
				<ul id="category_accordions_desktop" class="panel-group">
					<li class="standard-sidebar-searchbox"><form method="get" action=""><input type="text" name="search" class="search-text form-control" placeholder="Search Checklists" /></form></li>
					<li class="sidebar-higher-level"><a href="?subtabid=categories&categories=all" class="<?= $_GET['categories'] == 'all' ? 'active' : '' ?> cursor-hand">Tabs</a></li>
                    <li class="sidebar-higher-level">
                        <a class="<?= $_GET['subtabid'] == 'favourites' ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_favourites_desktop">Favourites<span class="arrow"></span></a><?php
                        $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE checklistid IN (0".implode(',',array_filter(explode(',',$user_settings['checklist_fav']))).") AND deleted='0' AND checklist_name <> ''"); ?>
                        <ul id="collapse_favourites_desktop" class="panel-collapse collapse <?= $_GET['subtabid'] == 'favourites' ? 'in' : '' ?>"><?php
                            while ( $row = mysqli_fetch_assoc($query) ) {
                                $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(checklistnameid) count FROM checklist_name WHERE checklistid='{$row['checklistid']}'"));?>
                                <li><a href="?subtabid=favourites&view=<?= $row['checklistid'] ?>" class="<?= isset($_GET['view']) && $_GET['view'] == $row['checklistid'] ? 'active' : '' ?>"><?= $row['checklist_name'] ?><span class="pull-right offset-right-5"><?= $count['count'] ?></span></a></li><?php
                            } ?>
                        </ul>
                    </li><?php
                    if (strpos($tab_config,'private') !== false) { ?>
                        <li class="sidebar-higher-level">
                            <a class="<?= $_GET['subtabid'] == 'private' ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_private_desktop">Private<span class="arrow"></span></a><?php
                            $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE assign_staff=',{$_SESSION['contactid']},' AND deleted='0' AND checklist_name <> ''"); ?>
                            <ul id="collapse_private_desktop" class="panel-collapse collapse <?= $_GET['subtabid'] == 'private' ? 'in' : '' ?>"><?php
                                while ( $row = mysqli_fetch_assoc($query) ) {
                                    $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(checklistnameid) count FROM checklist_name WHERE checklistid='{$row['checklistid']}'"));?>
                                    <li><a href="?subtabid=private&view=<?= $row['checklistid'] ?>" class="<?= isset($_GET['view']) && $_GET['view'] == $row['checklistid'] ? 'active' : '' ?>"><?= $row['checklist_name'] ?><span class="pull-right offset-right-5"><?= $count['count'] ?></span></a></li><?php
                                } ?>
                            </ul>
                        </li><?php
                    }
                    if(strpos($tab_config,'shared') !== false) { ?>
                        <li class="sidebar-higher-level">
                            <a class="<?= $_GET['subtabid'] == 'shared' ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_shared_desktop">Shared<span class="arrow"></span></a><?php
                            $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE assign_staff LIKE '%,{$_SESSION['contactid']},%' AND deleted='0' AND checklist_name <> ''"); ?>
                            <ul id="collapse_shared_desktop" class="panel-collapse collapse <?= $_GET['subtabid'] == 'shared' ? 'in' : '' ?>"><?php
                                while ( $row = mysqli_fetch_assoc($query) ) {
                                    $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(checklistnameid) count FROM checklist_name WHERE checklistid='{$row['checklistid']}'"));?>
                                    <li><a href="?subtabid=shared&view=<?= $row['checklistid'] ?>" class="<?= isset($_GET['view']) && $_GET['view'] == $row['checklistid'] ? 'active' : '' ?>"><?= $row['checklist_name'] ?><span class="pull-right offset-right-5"><?= $count['count'] ?></span></a></li><?php
                                } ?>
                            </ul>
                        </li><?php
                    }
                    if(strpos($tab_config,'project') !== false) { ?>
                        <li class="sidebar-higher-level">
                            <a class="<?= $_GET['subtabid'] == 'project' ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_project_desktop">Project<span class="arrow"></span></a><?php
                            $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE assign_staff LIKE '%,{$_SESSION['contactid']},%' projectid > 0 AND deleted='0' AND checklist_name <> ''"); ?>
                            <ul id="collapse_project_desktop" class="panel-collapse collapse <?= $_GET['subtabid'] == 'project' ? 'in' : '' ?>"><?php
                                while ( $row = mysqli_fetch_assoc($query) ) {
                                    $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(checklistnameid) count FROM checklist_name WHERE checklistid='{$row['checklistid']}'"));?>
                                    <li><a href="?subtabid=shared&view=<?= $row['checklistid'] ?>" class="<?= isset($_GET['view']) && $_GET['view'] == $row['checklistid'] ? 'active' : '' ?>"><?= $row['checklist_name'] ?><span class="pull-right offset-right-5"><?= $count['count'] ?></span></a></li><?php
                                } ?>
                            </ul>
                        </li><?php
                    }
                    if(strpos($tab_config,'company') !== false) { ?>
                        <li class="sidebar-higher-level">
                            <a class="<?= $_GET['subtabid'] == 'company' ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_company_desktop">Company<span class="arrow"></span></a><?php
                            $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE assign_staff LIKE '%ALL%' AND deleted='0' AND checklist_name <> ''"); ?>
                            <ul id="collapse_company_desktop" class="panel-collapse collapse <?= $_GET['subtabid'] == 'company' ? 'in' : '' ?>"><?php
                                while ( $row = mysqli_fetch_assoc($query) ) {
                                    $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(checklistnameid) count FROM checklist_name WHERE checklistid='{$row['checklistid']}'"));?>
                                    <li><a href="?subtabid=shared&view=<?= $row['checklistid'] ?>" class="<?= isset($_GET['view']) && $_GET['view'] == $row['checklistid'] ? 'active' : '' ?>"><?= $row['checklist_name'] ?><span class="pull-right offset-right-5"><?= $count['count'] ?></span></a></li><?php
                                } ?>
                            </ul>
                        </li><?php
                    }
                    if(strpos($tab_config,'ongoing') !== false) { ?>
                        <li class="sidebar-higher-level">
                            <a class="<?= $_GET['subtabid'] == 'ongoing' ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_ongoing_desktop">Ongoing<span class="arrow"></span></a><?php
                            $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE assign_staff LIKE '%,{$_SESSION['contactid']},%' AND checklist_type = 'ongoing' AND deleted='0' AND checklist_name <> ''"); ?>
                            <ul id="collapse_ongoing_desktop" class="panel-collapse collapse <?= $_GET['subtabid'] == 'ongoing' ? 'in' : '' ?>"><?php
                                while ( $row = mysqli_fetch_assoc($query) ) {
                                    $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(checklistnameid) count FROM checklist_name WHERE checklistid='{$row['checklistid']}'"));?>
                                    <li><a href="?subtabid=shared&view=<?= $row['checklistid'] ?>" class="<?= isset($_GET['view']) && $_GET['view'] == $row['checklistid'] ? 'active' : '' ?>"><?= $row['checklist_name'] ?><span class="pull-right offset-right-5"><?= $count['count'] ?></span></a></li><?php
                                } ?>
                            </ul>
                        </li><?php
                    }
                    if(strpos($tab_config,'daily') !== false) { ?>
                        <li class="sidebar-higher-level">
                            <a class="<?= $_GET['subtabid'] == 'daily' ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_daily_desktop">Daily<span class="arrow"></span></a><?php
                            $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE assign_staff LIKE '%,{$_SESSION['contactid']},%' AND checklist_type = 'daily' AND deleted='0' AND checklist_name <> ''"); ?>
                            <ul id="collapse_daily_desktop" class="panel-collapse collapse <?= $_GET['subtabid'] == 'daily' ? 'in' : '' ?>"><?php
                                while ( $row = mysqli_fetch_assoc($query) ) {
                                    $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(checklistnameid) count FROM checklist_name WHERE checklistid='{$row['checklistid']}'"));?>
                                    <li><a href="?subtabid=shared&view=<?= $row['checklistid'] ?>" class="<?= isset($_GET['view']) && $_GET['view'] == $row['checklistid'] ? 'active' : '' ?>"><?= $row['checklist_name'] ?><span class="pull-right offset-right-5"><?= $count['count'] ?></span></a></li><?php
                                } ?>
                            </ul>
                        </li><?php
                    }
                    if(strpos($tab_config,'weekly') !== false) { ?>
                        <li class="sidebar-higher-level">
                            <a class="<?= $_GET['subtabid'] == 'weekly' ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_weekly_desktop">Weekly<span class="arrow"></span></a><?php
                            $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE assign_staff LIKE '%,{$_SESSION['contactid']},%' AND checklist_type = 'weekly' AND deleted='0' AND checklist_name <> ''"); ?>
                            <ul id="collapse_weekly_desktop" class="panel-collapse collapse <?= $_GET['subtabid'] == 'weekly' ? 'in' : '' ?>"><?php
                                while ( $row = mysqli_fetch_assoc($query) ) {
                                    $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(checklistnameid) count FROM checklist_name WHERE checklistid='{$row['checklistid']}'"));?>
                                    <li><a href="?subtabid=shared&view=<?= $row['checklistid'] ?>" class="<?= isset($_GET['view']) && $_GET['view'] == $row['checklistid'] ? 'active' : '' ?>"><?= $row['checklist_name'] ?><span class="pull-right offset-right-5"><?= $count['count'] ?></span></a></li><?php
                                } ?>
                            </ul>
                        </li><?php
                    }
                    if(strpos($tab_config,'monthly') !== false) { ?>
                        <li class="sidebar-higher-level">
                            <a class="<?= $_GET['subtabid'] == 'monthly' ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_monthly_desktop">Monthly<span class="arrow"></span></a><?php
                            $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE assign_staff LIKE '%,{$_SESSION['contactid']},%' AND checklist_type = 'monthly' AND deleted='0' AND checklist_name <> ''"); ?>
                            <ul id="collapse_monthly_desktop" class="panel-collapse collapse <?= $_GET['subtabid'] == 'monthly' ? 'in' : '' ?>"><?php
                                while ( $row = mysqli_fetch_assoc($query) ) {
                                    $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(checklistnameid) count FROM checklist_name WHERE checklistid='{$row['checklistid']}'"));?>
                                    <li><a href="?subtabid=shared&view=<?= $row['checklistid'] ?>" class="<?= isset($_GET['view']) && $_GET['view'] == $row['checklistid'] ? 'active' : '' ?>"><?= $row['checklist_name'] ?><span class="pull-right offset-right-5"><?= $count['count'] ?></span></a></li><?php
                                } ?>
                            </ul>
                        </li><?php
                    }
                    if(strpos($tab_config,'equipment') !== false) { ?>
                        <li class="sidebar-higher-level">
                            <a class="<?= $_GET['subtabid'] == 'equipment' ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_equipment_desktop">Equipment<span class="arrow"></span></a><?php
                            $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE assign_staff LIKE '%,{$_SESSION['contactid']},%' AND deleted='0' AND checklist_name <> ''"); ?>
                            <ul id="collapse_equipment_desktop" class="panel-collapse collapse <?= $_GET['subtabid'] == 'equipment' ? 'in' : '' ?>"><?php
                                while ( $row = mysqli_fetch_assoc($query) ) {
                                    $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(checklistnameid) count FROM checklist_name WHERE checklistid='{$row['checklistid']}'"));?>
                                    <li><a href="?subtabid=shared&view=<?= $row['checklistid'] ?>" class="<?= isset($_GET['view']) && $_GET['view'] == $row['checklistid'] ? 'active' : '' ?>"><?= $row['checklist_name'] ?><span class="pull-right offset-right-5"><?= $count['count'] ?></span></a></li><?php
                                } ?>
                            </ul>
                        </li><?php
                    }
                    if(strpos($tab_config,'inventory') !== false) { ?>
                        <li class="sidebar-higher-level">
                            <a class="<?= $_GET['subtabid'] == 'inventory' ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_inventory_desktop">Inventory<span class="arrow"></span></a><?php
                            $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE assign_staff LIKE '%,{$_SESSION['contactid']},%' AND deleted='0' AND checklist_name <> ''"); ?>
                            <ul id="collapse_inventory_desktop" class="panel-collapse collapse <?= $_GET['subtabid'] == 'inventory' ? 'in' : '' ?>"><?php
                                while ( $row = mysqli_fetch_assoc($query) ) {
                                    $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(checklistnameid) count FROM checklist_name WHERE checklistid='{$row['checklistid']}'"));?>
                                    <li><a href="?subtabid=shared&view=<?= $row['checklistid'] ?>" class="<?= isset($_GET['view']) && $_GET['view'] == $row['checklistid'] ? 'active' : '' ?>"><?= $row['checklist_name'] ?><span class="pull-right offset-right-5"><?= $count['count'] ?></span></a></li><?php
                                } ?>
                            </ul>
                        </li><?php
                    }
                    
                    $query_retrieve_subtabs = mysqli_query($dbc, "SELECT `checklist_subtab`.`subtabid`, `checklist_subtab`.`name`, COUNT(`checklist`.`checklistid`) subtab_count, `checklist`.`checklistid` FROM `checklist_subtab` LEFT JOIN `checklist` ON `checklist_subtab`.`subtabid`=`checklist`.`subtabid` AND (`checklist`.`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `checklist`.`assign_staff`=',ALL,') AND `checklist`.`deleted`=0 WHERE (`checklist_subtab`.`created_by` = ".$_SESSION['contactid']." OR `checklist_subtab`.`shared` LIKE '%,".$_SESSION['contactid'].",%' OR `checklist_subtab`.`shared` LIKE ',ALL,') GROUP BY `checklist_subtab`.`subtabid`, `checklist_subtab`.`name`");
					while ($row = mysqli_fetch_array($query_retrieve_subtabs)) { ?>
                        <li class="sidebar-higher-level">
                            <a class="<?= $_GET['subtabid'] == $row['subtabid'] ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_<?= $row['subtabid'] ?>"><?= $row['name'] ?><span class="arrow"></span></a><?php
                            $query = mysqli_query($dbc, "SELECT checklistid, checklist_name FROM checklist WHERE assign_staff LIKE '%,{$_SESSION['contactid']},%' AND deleted='0' AND subtabid = '{$row['subtabid']}' AND checklist_name <> ''"); ?>
                            <ul id="collapse_<?= $row['subtabid'] ?>" class="panel-collapse collapse <?= $_GET['subtabid'] == $row['subtabid'] ? 'in' : '' ?>"><?php
                                while ( $row2 = mysqli_fetch_assoc($query) ) {
                                    $count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(checklistnameid) count FROM checklist_name WHERE checklistid='{$row2['checklistid']}'"));?>
                                    <li><a href="?subtabid=<?= $row['subtabid'] ?>&view=<?= $row2['checklistid'] ?>" class="<?= isset($_GET['view']) && $_GET['view'] == $row2['checklistid'] ? 'active' : '' ?>"><?= $row2['checklist_name'] ?><span class="pull-right offset-right-5"><?= $count['count'] ?></span></a></li><?php
                                } ?>
                            </ul>
                        </li><?php
					} ?>
                    
                    <!--
					<?php if(strpos($tab_config,'equipment') !== false) { ?><a href="?subtabid=equipment" class="<?= (in_array('equipment',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'equipment' ? 'class="active blue"' : '' ?>>Equipment<span class='pull-right'><?= $tab_counts['equipment'] ?></span></li></a><?php } ?>
					<?php if(strpos($tab_config,'inventory') !== false) { ?><a href="?subtabid=inventory" class="<?= (in_array('inventory',$hidden_categories) ? 'non-visible" style="display:none;' : '') ?>"><li <?= $_GET['subtabid'] == 'inventory' ? 'class="active blue"' : '' ?>>Inventory<span class='pull-right'><?= $tab_counts['inventory'] ?></span></li></a><?php } ?>
                    -->
                    <?php
                    /*
                    foreach ( explode(',', $tab_config) as $category ) {
                        if ( $category != 'reporting' ) {
                            $cat_collapse = strtolower(str_replace(' ', '_', $category)); ?>
                            <li class="sidebar-higher-level">
                                <a class="<?= $_GET['subtabid'] == $category ? 'active' : 'collapsed' ?> cursor-hand" data-toggle="collapse" data-parent="#category_accordions_desktop" data-target="#collapse_<?= $cat_collapse ?>"><?= ucwords($category) ?><span class="arrow"></span></a><?php
                                $subtab_list = mysqli_query($dbc, "SELECT * FROM (SELECT `checklist`.`checklistid`,`checklist`.`businessid`,`checklist`.`projectid`,`checklist`.`ticketid`,`checklist`.`client_projectid`,`checklist`.`security`,`checklist`.`assign_staff`,`checklist`.`checklist_type`,`checklist`.`reset_day`,`checklist`.`reset_time`,`checklist`.`checklist_name`,`checklist`.`flag_colour`,`checklist`.`alerts_enabled`,`checklist`.`created_by`,`checklist`.`deleted`,`checklist`.`subtabid`, `checklist_subtab`.`name`, IFNULL(`project`.`project_name`,`client_project`.`project_name`) `final_project_name`, IFNULL(`project`.`projectid`,`client_project`.`projectid`) `final_projectid` FROM `checklist` LEFT JOIN `checklist_subtab` ON `checklist`.`subtabid`=`checklist_subtab`.`subtabid` LEFT JOIN `project` ON `checklist`.`projectid`=`project`.`projectid` LEFT JOIN `client_project` ON `checklist`.`client_projectid`=`client_project`.`projectid` WHERE (`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `assign_staff`=',ALL,') AND `checklist`.`deleted`=0 $tab_filter ORDER BY `final_project_name`) `checklists` UNION SElECT `checklistid`,'','0','','','','ALL',`checklist_item` `checklist_type`,'','',`checklist_name`,'','','',`deleted`,'',`checklist_name` `name`,'','' FROM `item_checklist`"); ?>
                                <ul id="collapse_<?= $cat_collapse ?>" class="panel-collapse collapse <?= !empty($_GET['subtabid']) && $_GET['subtabid'] == $category ? 'in' : '' ?>"><?php
                                    while ( $checklist = mysqli_fetch_array($subtab_list) ) {
                                        echo '<a href="'.(in_array($checklist['checklist_type'],['equipment','inventory']) ? str_replace('view=','item_view=',$link) : $link).$checklist['checklistid'].'" data-projectid="'.$checklist['final_projectid'].'" data-project="'.$checklist['final_project_name'].'" data-subtab="'.$checklist['name'].'" data-users="'.$users.'" data-name="'.$checklist['checklist_name'].'" data-visible="'.$visibility.'"><li>';
                                        $additional = array_values(array_unique(array_filter(explode(',',str_replace(",{$checklist['created_by']},",',',','.$checklist['assign_staff'].',')))));
                                        echo (count($additional) > 0 ? ($additional[0] == 'ALL' ? '+All Staff: ' : '+'.count($additional).' ') : '').' '.($_GET['subtabid'] == 'project' ? 'Project #'.$checklist['final_projectid'].': '.$checklist['final_project_name'].': ' : '').$checklist['checklist_name'].'</li></a>';
                                    } ?>
                                </ul>
                            </li><?php
                        }
                    }
                    
                    $query_retrieve_subtabs = mysqli_query($dbc, "SELECT `checklist_subtab`.`subtabid`, `checklist_subtab`.`name`, COUNT(`checklist`.`checklistid`) subtab_count FROM `checklist_subtab` LEFT JOIN `checklist` ON `checklist_subtab`.`subtabid`=`checklist`.`subtabid` AND (`checklist`.`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `checklist`.`assign_staff`=',ALL,') AND `checklist`.`deleted`=0 WHERE (`checklist_subtab`.`created_by` = ".$_SESSION['contactid']." OR `checklist_subtab`.`shared` LIKE '%,".$_SESSION['contactid'].",%' OR `checklist_subtab`.`shared` LIKE ',ALL,') GROUP BY `checklist_subtab`.`subtabid`, `checklist_subtab`.`name`");
					while ($row = mysqli_fetch_array($query_retrieve_subtabs)) {
						echo "<a href='?subtabid={$row['subtabid']}' class='".(in_array($row['subtabid'],$hidden_categories) ? "non-visible' style='display:none;" : '')."'><li ".($_GET['subtabid'] == $row['subtabid'] ? 'class="active blue"' : '').">{$row['name']}<span class='pull-right'>{$row['subtab_count']}</span></li></a>";
					} 
                    */?>
				</ul>
			</div>
<?php } ?>
			<div class="scale-to-fill has-main-screen <?= (empty($_GET['view']) && empty($_GET['edit']) && empty($_GET['edittab']) && empty($_GET['reports']) ? 'hide-titles-mob' : '') ?> <?= (!empty($_GET['edit']) ? 'no-gap-pad' : '') ?>">
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
                } else if (!empty($_GET['categories'])) {
                    include('list_categories.php');
                } else if (!empty($_GET['search'])) {
                    include('search_results.php');
				} else {
					include('list_checklists.php');
				} ?>
				</div>
			</div>
		</div>
	</div>
</div>

<?php include ('../footer.php'); ?>
