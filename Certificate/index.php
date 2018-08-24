<?php // Certificates View
error_reporting(0);
include_once('../include.php'); ?>
<script>
$(document).ready(function() {
	$(window).resize(function() {
		$('.main-screen').css('padding-bottom',0);
		if($('.main-screen .main-screen').is(':visible')) {
			var available_height = window.innerHeight - $(footer).outerHeight() - $('.tile-sidebar:visible').offset().top;
			if(available_height > 200) {
				$('.main-screen .main-screen').outerHeight(available_height - $('.filter_div:visible').outerHeight()).css('overflow-y','auto');
				$('.tile-sidebar').outerHeight(available_height).css('overflow-y','auto');
				$('.search-results').outerHeight(available_height - $('.filter_div:visible').outerHeight()).css('overflow-y','auto');
			}
			var sidebar_height = $('.tile-sidebar').outerHeight(true);
			$('.has-main-screen .main-screen').css('min-height', sidebar_height - $('.filter_div:visible').outerHeight());
		}
	}).resize();
});
var show_certificates = function(status) { return true; }
</script>
<style>
span.arrow {
	float: right;
	margin-right: -1em;
}
</style>
</head>
<body>
<?php 
checkAuthorised('certificate');
$access = get_security($dbc, 'certificate');
include_once ('../navigation.php'); ?>
<div class="container">
	<div class="row">
		<div class="main-screen" style="border-width: 0; height: auto; margin-top: -20px;">
			<div class="tile-header">
				<h3 class="no-gap-top padded"><a href="?">Certificates</a><?= $_GET['edit'] > 0 ? ': Edit Certificate #'.$_GET['edit'] : isset($_GET['edit']) ? ': New Certificate' : '' ?><?php if($access['config'] > 0) {
					echo "<div class='pull-right'><a href='?settings=fields'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me settings-icon'></a></div>";
				} ?>
				<?php if(isset($_GET['status'])) {
					echo "<div class='pull-right' style='height: 1em; padding: 0 0.25em;'><button class='btn brand-btn' onclick='$(this).toggleClass(\"active_tab\"); $(\".filter_div\").toggle(); $(window).resize(); return false;'>Filter</button></div>";
				}
				if($access['edit'] > 0) {
					echo "<div class='pull-right' style='height: 1em; padding: 0 0.25em;'><a href='?edit=0' style='font-size: 0.5em;'><button class='btn brand-btn hide-titles-mob'>New Certificate</button>";
					echo "<img src='".WEBSITE_URL."/img/icons/ROOK-add-icon.png' class='show-on-mob add-icon-lg'></a></div>";
				} ?>
				<img class="no-toggle statusIcon pull-right no-margin inline-img" title="" src="" />
				<?php if(!isset($_GET['settings']) && !isset($_GET['edit']) && !isset($_GET['status']) && !isset($_GET['report'])) { ?>
					<img class="inline-img pull-right btn-horizontal-collapse small no-toggle" src="../img/icons/pie-chart.png" title="View Summary">
				<?php } ?>
				</h3>
				<?php if(!isset($_GET['settings']) && !isset($_GET['edit']) && !isset($_GET['status']) && !isset($_GET['report'])) { ?>
					<div class="collapsible-horizontal collapsed hide-titles-mob">
						<?php $summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*), SUM(IF(`issue_date` > NOW() OR IFNULL(`issue_date`,'0000-00-00') = '0000-00-00',1,0)) `pending`, SUM(IF(`issue_date` < NOW() AND IFNULL(`issue_date`,'0000-00-00') != '0000-00-00' AND `reminder_date` > NOW(),1,0)) `active`, SUM(IF(`reminder_date` < NOW() AND `expiry_date` > NOW(),1,0)) `expiring`, SUM(IF(`expiry_date` < NOW() AND `issue_date` < NOW() AND IFNULL(`issue_date`,'0000-00-00') != '0000-00-00',1,0)) `expired` FROM `certificate` WHERE `deleted`=0")); ?>
						<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 gap-top">
							<div class="summary-block">
								<span class="text-lg"><?= $summary['active'] ?></span><br />Completed
							</div>
						</div>
						<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 gap-top">
							<div class="summary-block">
								<span class="text-lg"><?= $summary['pending'] ?></span><br />Pending Completion
							</div>
						</div>
						<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 gap-top">
							<div class="summary-block">
								<span class="text-lg"><?= $summary['expiring'] ?></span><br />Expiry Pending
							</div>
						</div>
						<div class="col-xs-12 col-sm-4 col-md-3 col-lg-3 gap-top">
							<div class="summary-block">
								<span class="text-lg"><?= $summary['expired'] ?></span><br />Expired
							</div>
						</div>
					</div>
				<?php } ?>
				<div class="clearfix"></div>
			</div>
			<?php if(($_GET['settings']) && $access['config'] > 0) {
				include('field_config.php');
			} else { ?>
				<div id="certificate_accordions" class="show-on-mob panel-group block-panels col-xs-12">
				</div>
				<div class="tile-sidebar inherit-height sidebar sidebar-override double-gap-top hide-titles-mob collapsible">
					<ul>
						<?php if ( check_subtab_persmission($dbc, 'certificate', ROLE, 'cert_active') === TRUE ) { ?>
							<li class="sidebar-searchbox"><input type="text" class="form-control search_list" placeholder="Search Certificates"></li>
							<a href="?"><li class="<?= !isset($_GET['edit']) && !isset($_GET['status']) && !isset($_GET['report']) ? 'active blue' : '' ?>">Dashboard</li></a>
							<li class="cursor-hand <?= strpos($_GET['status'],'active_') === FALSE ? 'collapsed' : '' ?>" data-toggle="collapse" data-target="#collapse_active">Active Staff<span class="arrow"></span></li>
							<ul id="collapse_active" class="no-scroll collapse <?= strpos($_GET['status'],'active_') === FALSE ? '' : 'in' ?>" style="height: auto;">
								<?php $summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*), SUM(IF(`issue_date` > NOW() OR IFNULL(`issue_date`,'0000-00-00') = '0000-00-00',1,0)) `pending`, SUM(IF(`issue_date` < NOW() AND IFNULL(`issue_date`,'0000-00-00') != '0000-00-00' AND `reminder_date` > NOW(),1,0)) `active`, SUM(IF(`reminder_date` < NOW() AND `expiry_date` > NOW(),1,0)) `expiring`, SUM(IF(`expiry_date` < NOW() AND `issue_date` < NOW() AND IFNULL(`issue_date`,'0000-00-00') != '0000-00-00',1,0)) `expired` FROM `certificate` WHERE `deleted`=0 AND `contactid` IN (SELECT `contactid` FROM `contacts` WHERE `status`=1)")); ?>
								<li class="<?= $_GET['status'] == 'active_complete' ? 'active blue' : '' ?>"><a href="?status=active_complete" data-status="active_complete" onclick="return show_certificates(this);">Completed<span class="pull-right"><?= $summary['active'] ?></span></a></li>
								<li class="<?= $_GET['status'] == 'active_pending' ? 'active blue' : '' ?>"><a href="?status=active_pending" data-status="active_pending" onclick="return show_certificates(this);">Pending<span class="pull-right"><?= $summary['pending'] ?></span></a></li>
								<li class="<?= $_GET['status'] == 'active_expiring' ? 'active blue' : '' ?>"><a href="?status=active_expiring" data-status="active_expiring" onclick="return show_certificates(this);">Expiry Pending<span class="pull-right"><?= $summary['expiring'] ?></span></a></li>
								<li class="<?= $_GET['status'] == 'active_expired' ? 'active blue' : '' ?>"><a href="?status=active_expired" data-status="active_expired" onclick="return show_certificates(this);">Expired<span class="pull-right"><?= $summary['expired'] ?></span></a></li>
							</ul>
						<?php } ?>
						<?php if ( check_subtab_persmission($dbc, 'certificate', ROLE, 'cert_suspended') === TRUE ) { ?>
							<li class="cursor-hand <?= strpos($_GET['status'],'suspend_') === FALSE ? 'collapsed' : '' ?>" data-toggle="collapse" data-target="#collapse_suspend">Suspended Staff<span class="arrow"></span></li>
							<ul id="collapse_suspend" class="no-scroll collapse <?= strpos($_GET['status'],'suspend_') === FALSE ? '' : 'in' ?>" style="height: auto;">
								<?php $summary = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*), SUM(IF(`issue_date` > NOW() OR IFNULL(`issue_date`,'0000-00-00') = '0000-00-00',1,0)) `pending`, SUM(IF(`issue_date` < NOW() AND IFNULL(`issue_date`,'0000-00-00') != '0000-00-00' AND `reminder_date` > NOW(),1,0)) `active`, SUM(IF(`reminder_date` < NOW() AND `expiry_date` > NOW(),1,0)) `expiring`, SUM(IF(`expiry_date` < NOW() AND `issue_date` < NOW() AND IFNULL(`issue_date`,'0000-00-00') != '0000-00-00',1,0)) `expired` FROM `certificate` WHERE `deleted`=0 AND `contactid` NOT IN (SELECT `contactid` FROM `contacts` WHERE `status`=1)")); ?>
								<li class="<?= $_GET['status'] == 'suspend_complete' ? 'active blue' : '' ?>"><a href="?status=suspend_complete" data-status="suspend_complete" onclick="return show_certificates(this);">Completed<span class="pull-right"><?= $summary['active'] ?></span></a></li>
								<li class="<?= $_GET['status'] == 'suspend_pending' ? 'active blue' : '' ?>"><a href="?status=suspend_pending" data-status="suspend_pending" onclick="return show_certificates(this);">Pending<span class="pull-right"><?= $summary['pending'] ?></span></a></li>
								<li class="<?= $_GET['status'] == 'suspend_expiring' ? 'active blue' : '' ?>"><a href="?status=suspend_expiring" data-status="suspend_expiring" onclick="return show_certificates(this);">Expiry Pending<span class="pull-right"><?= $summary['expiring'] ?></span></a></li>
								<li class="<?= $_GET['status'] == 'suspend_expired' ? 'active blue' : '' ?>"><a href="?status=suspend_expired" data-status="suspend_expired" onclick="return show_certificates(this);">Expired<span class="pull-right"><?= $summary['expired'] ?></span></a></li>
							</ul>
						<?php } ?>
						<?php if ( check_subtab_persmission($dbc, 'certificate', ROLE, 'cert_followup') === TRUE ) { ?> 
							<li class="<?= $_GET['report'] == 'followup' ? 'active blue' : '' ?>"><a href="?report=followup">Follow Up</a></li>
						<?php } ?>
						<?php if ( check_subtab_persmission($dbc, 'certificate', ROLE, 'cert_reporting') === TRUE ) { ?>
							<li class="<?= $_GET['report'] == 'overview' ? 'active blue' : '' ?>"><a href="?report=overview">Reporting</a></li>
						<?php } ?>
					</ul>
				</div>
				<div class="main-content-screen scale-to-fill has-main-screen hide-titles-mob">
					<?php if($access['edit'] > 0) { ?>
						<div class='col-sm-12 text-center pad-vertical small filter_div' style='display:none;'>
							<div class="col-sm-3">
								<label class="col-sm-4">Staff:</label>
								<div class="col-sm-8">
									<select class="chosen-select-deselect" data-placeholder="Select Staff" name="filter_staff"><option></option>
										<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid` IN (SELECT `contactid` FROM `certificate` WHERE `deleted`=0)")) as $staff) {
											echo "<option value='".$staff['contactid']."'>".$staff['first_name'].' '.$staff['last_name']."</option>";
										} ?>
									</select>
								</div>
							</div>
							<div class="col-sm-3">
								<label class="col-sm-4">Type:</label>
								<div class="col-sm-8">
									<select class="chosen-select-deselect" data-placeholder="Select Type" name="filter_type"><option></option>
										<?php $type_list = mysqli_query($dbc, "SELECT `certificate_type` FROM `certificate` WHERE `deleted`=0 GROUP BY `certificate_type` ORDER BY `certificate_type`");
										while($type = mysqli_fetch_assoc($type_list)) {
											echo "<option value='".$type['certificate_type']."'>".$type['certificate_type']."</option>";
										} ?>
									</select>
								</div>
							</div>
							<div class="col-sm-5">
								<label class="col-sm-6">Expiry Date Range:</label>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" name="filter_start" placeholder="Start Date">
								</div>
								<div class="col-sm-3">
									<input type="text" class="form-control datepicker" name="filter_end" placeholder="End Date">
								</div>
							</div>
							<div class="col-sm-1">
								<button class="pull-right btn brand-btn" onclick="show_certificates($('.active.blue a').get(0)); return false;" style="max-width:100%; min-width:3em;">Search</button>
							</div>
						</div>
					<?php } ?>
					<div class="main-screen override-main-screen form-horizontal">
						<?php if(isset($_GET['edit']) && $access['edit'] > 0) {
							include('edit_certificate.php');
						} else if(isset($_GET['status'])) {
							include('certificates.php');
						} else if($_GET['report'] == 'overview') {
							include('reporting.php');
						} else if($_GET['report'] == 'followup') {
							include('followup.php');
						} else {
							include('dashboard.php');
						} ?>
					</div>
				</div>
			<?php } ?>
		</div>
	</div>
</div>
<div class="clearfix"></div>
<?php include('../footer.php'); ?>
