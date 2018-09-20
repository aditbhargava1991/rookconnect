<?php
$current_type = $_GET['type'];
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM field_config_incident_report WHERE row_type=''"));
$tile_tabs = explode(',',get_config($dbc, 'incident_report_tabs'));
$current_file = basename($_SERVER['PHP_SELF']);
$approvals = approval_visible_function($dbc, 'incident_report');
$admin_securitys = explode(',',get_config($dbc, 'incident_report_admin_security'));
$admin_staffs = explode(',',get_config($dbc, 'incident_report_admin_staff'));

$admin_access = false;
if(in_array($_SESSION['contactid'],$admin_staffs)) {
	$admin_access = true;
}
foreach(explode(',', $_SESSION['role']) as $session_role) {
	if(in_array($session_role, $admin_securitys)) {
		$admin_access = true;
	}
}
if(empty($current_type) && in_array('Summary',$tile_tabs) && $current_file != 'summary.php') {
	header('Location: summary.php');
}
?>

<ul class="sidebar">
    <li class="sidebar-searchbox">
        <form action="" method="POST">
        	<input name="search_incident_reports" type="text" value="<?= $_POST['search_incident_reports'] ?>" class="form-control search_incident_reports" placeholder="Search <?= INC_REP_TILE ?>">
        	<input type="submit" value="search_submit" name="search_submit" style="display: none;">
        </form>
    </li>
	<?php if(in_array('Summary',$tile_tabs)) { ?>
        <a href="summary.php"><li <?= $current_file == 'summary.php' ? 'class="active"' : '' ?>>Summary</li></a>
	<?php } ?>
    <a href="incident_report.php"><li <?= $current_file == 'incident_report.php' && empty($current_type) ? 'class="active"' : '' ?>>All <?= INC_REP_TILE ?></li></a>
    <?php if(get_config($dbc, 'inc_rep_saved_tab') == 1) { ?>
		<a href="incident_report.php?type=SAVED"><li <?= $current_file == 'incident_report.php' && $current_type == 'SAVED' ? 'class="active"' : '' ?>>Saved <?= INC_REP_TILE ?></li></a>
    <?php } ?>
	<?php if(in_array('Admin',$tile_tabs) && $approvals > 0 && $admin_access) { ?>
		<li class="sidebar-higher-level highest-level"><a class="cursor-hand <?= $current_file == 'admin.php' ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#admin_stat">Administration<span class="arrow pull-right"></span></a>
			<ul id="admin_stat" class="collapse <?= $current_file == 'admin.php' ? 'in' : '' ?>">
				<?php foreach (str_getcsv(html_entity_decode($get_field_config['incident_types']), ',') as $in_type) { ?>
					<?php $counts = $dbc->query("SELECT SUM(IF(`status`='Pending' OR IFNULL(`status`,'')='',1,0)) `pending`, SUM(IF(`status`='Review',1,0)) `review`, SUM(IF(`status`='Revision',1,0)) `revision`, SUM(IF(`status`='Done',1,0)) `done` FROM `incident_report` WHERE `deleted`=0 AND `type`='$in_type'")->fetch_assoc(); ?>
					<li class="sidebar-higher-level"><a class="cursor-hand <?= $in_type == $current_type ? 'active blue' : 'collapsed' ?>" data-toggle="collapse" data-target="#<?= config_safe_str($in_type) ?>"><?= $in_type ?><span class="arrow pull-right"></span></a>
						<ul id="<?= config_safe_str($in_type) ?>" class="collapse <?= $current_type == $in_type ? 'in' : '' ?>">
							<a href="admin.php?status=Pending&type=<?= $in_type ?>"><li <?= $current_file == 'admin.php' && $in_type == $current_type && $_GET['status'] == 'Pending' ? 'class="active"' : '' ?>>Pending<span class="pull-right"><?= $counts['pending'] > 0 ? $counts['pending'] : 0 ?></span></li></a>
							<a href="admin.php?status=Revision&type=<?= $in_type ?>"><li <?= $current_file == 'admin.php' && $in_type == $current_type && $_GET['status'] == 'Revision' ? 'class="active"' : '' ?>>In Revision<span class="pull-right"><?= $counts['revision'] > 0 ? $counts['revision'] : 0 ?></span></li></a>
							<a href="admin.php?status=Review&type=<?= $in_type ?>"><li <?= $current_file == 'admin.php' && $in_type == $current_type && $_GET['status'] == 'Review' ? 'class="active"' : '' ?>>In Review<span class="pull-right"><?= $counts['review'] > 0 ? $counts['review'] : 0 ?></span></li></a>
							<a href="admin.php?status=Done&type=<?= $in_type ?>"><li <?= $current_file == 'admin.php' && $in_type == $current_type && $_GET['status'] == 'Done' ? 'class="active"' : '' ?>>Done<span class="pull-right"><?= $counts['done'] > 0 ? $counts['done'] : 0 ?></span></li></a>
						</ul>
					</li>
				<?php } ?>
			</ul>
		</li>
	<?php } ?>
    <?php foreach (str_getcsv(html_entity_decode($get_field_config['incident_types']), ',') as $in_type) { ?>
        <a href="incident_report.php?type=<?= $in_type ?>"><li <?= $current_file == 'incident_report.php' && $in_type == $current_type ? 'class="active"' : '' ?>><?= $in_type ?></li></a>
    <?php } ?>
</ul>