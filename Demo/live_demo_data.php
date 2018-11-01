<?php include_once('../include.php');
if(stripos(','.$role.',',',super,') === false) {
	header('location: ../admin_software_config.php?software_settings');
	die();
}
$db_all = @mysqli_connect(DATABASE_HOST, DATABASE_USER, DATABASE_PASSWORD); ?>
<script>
function syncTable(link) {
	var table = $(link).closest('tr').data('table');
	console.log(table);
	$(link).closest('td').load('../ajax_all.php?action=sync_data&table='+encodeURI(table));
	$(link).closest('td').text('Syncing Data...');
}
</script>

<div id="no-more-tables">
	<div class="notice double-gap-bottom popover-examples">
	<div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25px"></div>
	<div class="col-sm-16"><span class="notice-name">NOTE:</span>
		View the list of tables in you Live Software vs Demo Software. A list is displayed of the number of rows in each table, with the option to sync the databases.</div>
		<div class="clearfix"></div>
	</div>
	
	<?php if(!$dbc2) {
		echo '<h3>Second database not configured. Please configure the second database before you compare configurations.</h3>';
	} else { ?>
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Table Name</th>
				<th>Rows in Live</th>
				<th>Rows in Demo</th>
				<th>Function</th>
			</tr>
			<?php if(tile_enabled($dbc, 'project')['user_enabled'] > 0) { ?>
				<tr data-table="project">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`project`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`project`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='project'")->fetch_assoc(); ?>
					<td data-title="Table Name"><?= PROJECT_TILE ?></td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> <?= PROJECT_TILE ?>, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> <?= PROJECT_TILE ?>, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'ticket')['user_enabled'] > 0) { ?>
				<tr data-table="tickets">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`tickets`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`tickets`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='tickets'")->fetch_assoc(); ?>
					<td data-title="Table Name"><?= TICKET_TILE ?></td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> <?= TICKET_TILE ?>, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> <?= TICKET_TILE ?>, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<tr data-table="contacts">
				<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`contacts` WHERE `category` != 'Staff'")->fetch_assoc();
				$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` != 'Staff'")->fetch_assoc();
				$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='contacts'")->fetch_assoc(); ?>
				<td data-title="Table Name"><?= CONTACTS_TILE ?></td>
				<td data-title="Rows in Live Software"><?= $count_live['active'] ?> <?= CONTACTS_TILE ?>, <?= $count_live['archived'] ?> Archived</td>
				<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> <?= CONTACTS_TILE ?>, <?= $count_demo['archived'] ?> Archived</td>
				<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
			</tr>
			<tr data-table="staff">
				<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`contacts` WHERE `category` = 'Staff'")->fetch_assoc();
				$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`contacts` WHERE `category` = 'Staff'")->fetch_assoc();
				$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='contacts'")->fetch_assoc(); ?>
				<td data-title="Table Name">Staff</td>
				<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Staff, <?= $count_live['archived'] ?> Archived</td>
				<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Staff, <?= $count_demo['archived'] ?> Archived</td>
				<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
			</tr>
			<?php if(tile_enabled($dbc, 'agenda_meeting')['user_enabled'] > 0) { ?>
				<tr data-table="agenda_meeting">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`agenda_meeting`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`agenda_meeting`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='agenda_meeting'")->fetch_assoc(); ?>
					<td data-title="Table Name">Agendas &amp; Meetings</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Agendas &amp; Meetings, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Agendas &amp; Meetings, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'assets')['user_enabled'] > 0) { ?>
				<tr data-table="asset">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`asset`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`asset`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='asset'")->fetch_assoc(); ?>
					<td data-title="Table Name">Assets</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Assets, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Assets, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'budget')['user_enabled'] > 0) { ?>
				<tr data-table="budget">
					<?php $count_live = $db_all->query("SELECT COUNT(*) `active` FROM `".DATABASE_NAME2."`.`budget`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT COUNT(*) `active` FROM `".DATABASE_NAME."`.`budget`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='budget'")->fetch_assoc(); ?>
					<td data-title="Table Name">Budgets</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Budgets</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Budgets</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<tr data-table="booking">
				<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM (SELECT `bookingid`, `deleted` FROM `".DATABASE_NAME2."`.`booking` UNION SELECT `waitlistid`, `deleted` FROM `".DATABASE_NAME2."`.`waitlist`) `lines`")->fetch_assoc();
				$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM (SELECT `bookingid`, `deleted` FROM `".DATABASE_NAME."`.`booking` UNION SELECT `waitlistid`, `deleted` FROM `".DATABASE_NAME."`.`waitlist`) `lines`")->fetch_assoc();
				$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='booking'")->fetch_assoc(); ?>
				<td data-title="Table Name">Appointments &amp; Wait Lists</td>
				<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Appointments &amp; Wait Lists, <?= $count_live['archived'] ?> Archived</td>
				<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Appointments &amp; Wait Lists, <?= $count_demo['archived'] ?> Archived</td>
				<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
			</tr>
			<?php if(tile_enabled($dbc, 'certificate')['user_enabled'] > 0) { ?>
				<tr data-table="certificate">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`certificate`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`certificate`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='certificate'")->fetch_assoc(); ?>
					<td data-title="Table Name">Certificates</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Certificates, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Certificates, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'checklist')['user_enabled'] > 0) { ?>
				<tr data-table="checklist">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`checklist`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`checklist`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='checklist'")->fetch_assoc(); ?>
					<td data-title="Table Name">Checklists</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Checklists, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Checklists, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'daily_log_notes')['user_enabled'] > 0) { ?>
				<tr data-table="client_daily_log_notes">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`client_daily_log_notes`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`client_daily_log_notes`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='client_daily_log_notes'")->fetch_assoc(); ?>
					<td data-title="Table Name">Log Notes</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Log Notes, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Log Notes, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'rate_card')['user_enabled'] > 0) { ?>
				<tr data-table="rate_card">
					<?php $count_live = $db_all->query("SELECT COUNT(DISTINCT `rate_card_name`) `active` FROM (SELECT `rate_card_name` FROM `".DATABASE_NAME2."`.`company_rate_card` WHERE `deleted`=0 UNION SELECT `ratecardid` `rate_card_name` FROM `".DATABASE_NAME2."`.`rate_card` WHERE `deleted`=0) `lines`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT COUNT(DISTINCT `rate_card_name`) `active` FROM (SELECT `rate_card_name` FROM `".DATABASE_NAME."`.`company_rate_card` WHERE `deleted`=0 UNION SELECT `ratecardid` `rate_card_name` FROM `".DATABASE_NAME."`.`rate_card` WHERE `deleted`=0) `lines`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name` IN ('company_rate_card','rate_card')")->fetch_assoc(); ?>
					<td data-title="Table Name">Rate Cards</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Rate Cards</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Rate Cards</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'contracts')['user_enabled'] > 0) { ?>
				<tr data-table="contracts">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`contracts`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`contracts`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='contracts'")->fetch_assoc(); ?>
					<td data-title="Table Name">Contracts</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Contracts, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Contracts, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'email_communication')['user_enabled'] > 0 || tile_enabled($dbc, 'phone_communication')['user_enabled'] > 0) { ?>
				<tr data-table="communication">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM (SELECT `deleted` FROM `".DATABASE_NAME2."`.`phone_communication` UNION SELECT `deleted` FROM `".DATABASE_NAME2."`.`email_communication`) `lines`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM (SELECT `deleted` FROM `".DATABASE_NAME."`.`phone_communication` UNION SELECT `deleted` FROM `".DATABASE_NAME."`.`email_communication`) `lines`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name` IN ('phone_communication','email_communication')")->fetch_assoc(); ?>
					<td data-title="Table Name">Communications</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Communications, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Communications, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'incident_report')['user_enabled'] > 0) { ?>
				<tr data-table="incident_report">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`incident_report`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`incident_report`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='incident_report'")->fetch_assoc(); ?>
					<td data-title="Table Name"><?= INC_REP_TILE ?></td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> <?= INC_REP_TILE ?>, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> <?= INC_REP_TILE ?>, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'labour')['user_enabled'] > 0) { ?>
				<tr data-table="labour">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`labour`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`labour`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='labour'")->fetch_assoc(); ?>
					<td data-title="Table Name">Labour</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Labour, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Labour, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'material')['user_enabled'] > 0) { ?>
				<tr data-table="material">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`material`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`material`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='material'")->fetch_assoc(); ?>
					<td data-title="Table Name">Materials</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Materials, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Materials, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'match')['user_enabled'] > 0) { ?>
				<tr data-table="match_contact">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`match_contact`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`match_contact`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='match_contact'")->fetch_assoc(); ?>
					<td data-title="Table Name">Match Tile</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Matches, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Matches, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'package')['user_enabled'] > 0) { ?>
				<tr data-table="package">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`package`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`package`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='package'")->fetch_assoc(); ?>
					<td data-title="Table Name">Packages</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Packages, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Packages, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'passwords')['user_enabled'] > 0) { ?>
				<tr data-table="passwords">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`passwords`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`passwords`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='passwords'")->fetch_assoc(); ?>
					<td data-title="Table Name">Passwords</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Passwords, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Passwords, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'products')['user_enabled'] > 0) { ?>
				<tr data-table="products">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`products`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`products`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='products'")->fetch_assoc(); ?>
					<td data-title="Table Name">Products</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Products, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Products, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'properties')['user_enabled'] > 0) { ?>
				<tr data-table="properties">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`properties`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`properties`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='properties'")->fetch_assoc(); ?>
					<td data-title="Table Name">Properties</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Properties, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Properties, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'equipment')['user_enabled'] > 0) { ?>
				<tr data-table="equipment">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`equipment`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`equipment`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='equipment'")->fetch_assoc(); ?>
					<td data-title="Table Name">Equipment</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Equipment, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Equipment, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'estimate')['user_enabled'] > 0) { ?>
				<tr data-table="estimate">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`estimate`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`estimate`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='estimate'")->fetch_assoc(); ?>
					<td data-title="Table Name"><?= ESTIMATE_TILE ?></td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> <?= ESTIMATE_TILE ?>, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> <?= ESTIMATE_TILE ?>, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'expense')['user_enabled'] > 0) { ?>
				<tr data-table="expense">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`expense`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`expense`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='expense'")->fetch_assoc(); ?>
					<td data-title="Table Name">Expenses</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Expenses, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Expenses, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'field_jobs')['user_enabled'] > 0) { ?>
				<tr data-table="field_jobs">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`field_jobs`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`field_jobs`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='field_jobs'")->fetch_assoc(); ?>
					<td data-title="Table Name">Field Jobs</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Field Jobs, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Field Jobs, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'fund_development')['user_enabled'] > 0) { ?>
				<tr data-table="fund_development_funder">
					<?php $count_live = $db_all->query("SELECT COUNT(*) `active` FROM `".DATABASE_NAME2."`.`fund_development_funder`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT COUNT(*) `active` FROM `".DATABASE_NAME."`.`fund_development_funder`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='fund_development_funder'")->fetch_assoc(); ?>
					<td data-title="Table Name">Fund Development</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Funds</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Funds</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'hr')['user_enabled'] > 0) { ?>
				<tr data-table="hr">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`hr`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`hr`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='hr'")->fetch_assoc(); ?>
					<td data-title="Table Name">HR</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Forms, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Forms, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
				<tr data-table="manuals">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`manuals`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`manuals`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='manuals'")->fetch_assoc(); ?>
					<td data-title="Table Name">Manuals</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Manuals, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Manuals, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'social_story')['user_enabled'] > 0) { ?>
				<tr data-table="social_story">
					<?php $count_live = $db_all->query("SELECT COUNT(*) `active` FROM (SELECT `activities_id` FROM `".DATABASE_NAME2."`.`social_story_activities` UNION SELECT `communication_id` FROM `".DATABASE_NAME2."`.`social_story_communication` UNION SELECT `protocol_id` FROM `".DATABASE_NAME2."`.`social_story_protocols` UNION SELECT `routine_id` FROM `".DATABASE_NAME2."`.`social_story_routines`) `stories`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT COUNT(*) `active` FROM (SELECT `activities_id` FROM `".DATABASE_NAME."`.`social_story_activities` UNION SELECT `communication_id` FROM `".DATABASE_NAME."`.`social_story_communication` UNION SELECT `protocol_id` FROM `".DATABASE_NAME."`.`social_story_protocols` UNION SELECT `routine_id` FROM `".DATABASE_NAME."`.`social_story_routines`) `stories`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name` IN ('social_story_activities','social_story_communication','social_story_protocols','social_story_routines')")->fetch_assoc(); ?>
					<td data-title="Table Name">Social Story</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Stories</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Stories</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'infogathering')['user_enabled'] > 0) { ?>
				<tr data-table="infogathering">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`infogathering`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`infogathering`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='infogathering'")->fetch_assoc(); ?>
					<td data-title="Table Name">Information Gathering</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Records, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Records, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'vendors')['user_enabled'] > 0) { ?>
				<tr data-table="vendor_price_list">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`vendor_price_list`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`vendor_price_list`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='vendor_price_list'")->fetch_assoc(); ?>
					<td data-title="Table Name">Vendor Price Lists</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Lists, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Lists, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'timesheet')['user_enabled'] > 0) { ?>
				<tr data-table="time_cards">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`time_cards`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`time_cards`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='time_cards'")->fetch_assoc(); ?>
					<td data-title="Table Name">Time Sheets</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Times, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Times, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'tasks')['user_enabled'] > 0) { ?>
				<tr data-table="tasklist">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`tasklist`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`tasklist`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='tasklist'")->fetch_assoc(); ?>
					<td data-title="Table Name">Tasks</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Tasks, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Tasks, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'services')['user_enabled'] > 0) { ?>
				<tr data-table="services">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`services`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`services`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='services'")->fetch_assoc(); ?>
					<td data-title="Table Name">Services</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Services, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Services, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'sales_order')['user_enabled'] > 0) { ?>
				<tr data-table="sales_order">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`sales_order`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`sales_order`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='sales_order'")->fetch_assoc(); ?>
					<td data-title="Table Name">Sales Orders</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Orders, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Orders, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'sales')['user_enabled'] > 0) { ?>
				<tr data-table="sales">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`sales`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`sales`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='sales'")->fetch_assoc(); ?>
					<td data-title="Table Name">Sales</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Leads, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Leads, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'inventory')['user_enabled'] > 0) { ?>
				<tr data-table="inventory">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`inventory`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`inventory`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='inventory'")->fetch_assoc(); ?>
					<td data-title="Table Name"><?= INVENTORY_TILE ?></td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> <?= INVENTORY_NOUN ?>, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> <?= INVENTORY_NOUN ?>, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
				<tr data-table="pick_lists">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`pick_lists`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`pick_lists`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='pick_lists'")->fetch_assoc(); ?>
					<td data-title="Table Name">Pick Lists</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Lists, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Lists, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'check_out')['user_enabled'] > 0 || tile_enabled($dbc, 'posadvanced')['user_enabled'] > 0) { ?>
				<tr data-table="invoice">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`invoice`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`invoice`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='invoice'")->fetch_assoc(); ?>
					<td data-title="Table Name">Invoices</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Invoices, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Invoices, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'marketing_material')['user_enabled'] > 0) { ?>
				<tr data-table="marketing_material">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`marketing_material`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`marketing_material`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='marketing_material'")->fetch_assoc(); ?>
					<td data-title="Table Name">Marketing Materials</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Materials, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Materials, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'medication')['user_enabled'] > 0) { ?>
				<tr data-table="medication">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`medication`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`medication`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='medication'")->fetch_assoc(); ?>
					<td data-title="Table Name">Medications</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Medications, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Medications, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
				<tr data-table="marsheet">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`marsheet`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`marsheet`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='marsheet'")->fetch_assoc(); ?>
					<td data-title="Table Name">MAR Sheets</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Sheets, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Sheets, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'orientation')['user_enabled'] > 0) { ?>
				<tr data-table="orientation">
					<?php $count_live = $db_all->query("SELECT COUNT(*) `active` FROM `".DATABASE_NAME2."`.`orientation`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT COUNT(*) `active` FROM `".DATABASE_NAME."`.`orientation`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='orientation'")->fetch_assoc(); ?>
					<td data-title="Table Name">Orientation</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Orientations</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Orientations</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'purchase_order')['user_enabled'] > 0) { ?>
				<tr data-table="purchase_orders">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`purchase_orders`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`purchase_orders`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='purchase_orders'")->fetch_assoc(); ?>
					<td data-title="Table Name">Purchase Orders</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Orders, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Orders, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php if(tile_enabled($dbc, 'safety')['user_enabled'] > 0) { ?>
				<tr data-table="safety">
					<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`safety`")->fetch_assoc();
					$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`safety`")->fetch_assoc();
					$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='safety'")->fetch_assoc(); ?>
					<td data-title="Table Name">Safety</td>
					<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Forms, <?= $count_live['archived'] ?> Archived</td>
					<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Forms, <?= $count_demo['archived'] ?> Archived</td>
					<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
				</tr>
			<?php } ?>
			<?php
			$tables_config = mysqli_query($dbc, "SELECT table_name FROM information_schema.tables WHERE table_type = 'base table' AND table_schema='demo_rookconnect_db' AND table_name LIKE '%_config%'");
			while($resTables = mysqli_fetch_assoc($tables_config)) {
				$table_config = $resTables['table_name'];
			?>
				<?php if(tile_enabled($dbc, $table_config)['user_enabled'] > 0) { ?>
					<tr data-table="<?php echo $table_config; ?>">
						<?php $count_live = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME2."`.`".$table_config."`")->fetch_assoc();
						$count_demo = $db_all->query("SELECT SUM(IF(`deleted`=0,1,0)) `active`, SUM(IF(`deleted`=0,0,1)) `archived` FROM `".DATABASE_NAME."`.`$table_config`")->fetch_assoc();
						$cols = $db_all->query("SELECT SUM(IF(`table_schema`='".DATABASE_NAME2."',1,0)) `live_cols`, SUM(IF(`table_schema`='".DATABASE_NAME."',1,0)) `demo_cols` FROM `information_schema`.`columns` WHERE `table_name`='$table_config'")->fetch_assoc(); ?>
						<td data-title="Table Name"><?php echo $table_config; ?></td>
						<td data-title="Rows in Live Software"><?= $count_live['active'] ?> Rows, <?= $count_live['archived'] ?> Archived</td>
						<td data-title="Rows in Demo Software"><?= $count_demo['active'] ?> Rows, <?= $count_demo['archived'] ?> Archived</td>
						<td data-title="Function"><?php if($cols['live_cols'] == $cols['demo_cols']) { ?><a class="cursor-hand" onclick="syncTable(this);">Sync Live data to Demo</a><?php } else { ?>The number of columns in your Live Sofware (<?= $cols['live_cols'] ?>) does not match your Demo Software (<?= $cols['demo_cols'] ?>)<?php } ?></td>
					</tr>
				<?php } ?>
			<?php } ?>
		</table>
	<?php } ?>
</div>