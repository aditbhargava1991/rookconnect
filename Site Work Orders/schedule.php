<?php
include('../phpsign/signature-to-image.php');
error_reporting(0);

if(!empty($_POST['submit_transfer_staff'])) {
	$equip_staffid = $_POST['equip_staffid'];

	foreach ($_POST['equip_transfer'] AS $equip_transfer) {
		if (!empty($_POST['transfer_staff_'.$equip_transfer]) && $_POST['transfer_staff_'.$equip_transfer] != $equip_staffid) {
			$contactid = $_SESSION['contactid'];
			$signature = $_POST['transfer_sign'];
			$assigned_staff = $_POST['equip_assigned_staff_'.$equip_transfer];
			$transfer_staff = $_POST['transfer_staff_'.$equip_transfer];
			$history = "Equipment ID $equip_transfer transferred from ".get_contact($dbc, $assigned_staff)." to ".get_contact($dbc, $transfer_staff)." by ".get_contact($dbc, $contactid).".";
			mysqli_query($dbc, "INSERT INTO `equipment_history` (`equipmentid`, `notes`, `signature`) VALUES ('$equip_transfer', '$history', '$signature')");
			mysqli_query($dbc, "UPDATE `equipment` SET `assigned_staff`='$transfer_staff' WHERE `equipmentid`='$equip_transfer'");
		}
	}
}

$logs = mysqli_query($dbc, "SELECT `log_id` FROM `site_work_driving_log` WHERE `staff`='".$_SESSION['contactid']."' AND IFNULL(`end_drive_time`,'')=''");
$staff_leads = explode(',', get_config($dbc, 'site_work_order_leads'));
$lead_query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category`='Staff' AND (`position`='Team Lead' OR `role` LIKE '%teamlead%') AND `deleted`=0 AND `status`=1"), MYSQLI_ASSOC));
$staff_leads = array_filter(array_merge($staff_leads,$lead_query,['all'])); ?>
<script>
$(document).ready(function() {
	<?php if(!empty($_GET['site'])) { ?>
		$('#work_order_sites').show().addClass('active_tab');
		$('#work_order_all').show().removeClass('active_tab').removeAttr('onclick').attr('onclick',"click_site('all'); return false;").text('All Work Orders');
		click_site('<?= $_GET['site'] ?>');
	<?php } ?>
});
function click_tab(tab) {
	$('#main_back_btn').show();
	$('#back_btn').hide();
	$('#main_tabs').hide();
	if(tab == 'work_orders') {
		$('#equip_list').hide();
		$('#site_safety').hide();
		$('#trans_equip_list').hide();
		$('#site_summary').hide();
		$('#work_orders').show();
		$('.site_btn').show();
		$('[id^=work_orders_]').hide();
		$('#work_order_sites').show().addClass('active_tab');
		$('#work_order_all').show().removeClass('active_tab').removeAttr('onclick').attr('onclick',"click_site('all'); return false;").text('All Work Orders');
		$('#equip_transfer_wo').hide();
		$('#equip_transfer_staff').hide();
	} else if(tab == 'equip_list') {
		$('#work_orders').hide();
		$('#site_safety').hide();
		$('#trans_equip_list').hide();
		$('#site_summary').hide();
		$('#equip_list').show();
		$('#equip_transfer_wo').hide();
		$('#equip_transfer_staff').hide();
	} else if(tab == 'trans_equip_list') {
		$('#work_orders').hide();
		$('#equip_list').hide();
		$('#site_safety').hide();
		$('#site_summary').hide();
		$('#trans_equip_list').show();
		$('#equip_transfer_wo').show().addClass('active_tab').removeAttr('onclick').attr('onclick', "return false;");
		$('#equip_transfer_staff').show();
	} else if(tab == 'site_safety') {
		$('#work_orders').hide();
		$('#equip_list').hide();
		$('#trans_equip_list').hide();
		$('#site_summary').hide();
		$('#site_safety').show();
		$('#equip_transfer_wo').hide();
		$('#equip_transfer_staff').hide();
	} else if(tab == 'site_summary') {
		$('#work_orders').hide();
		$('#equip_list').hide();
		$('#trans_equip_list').hide();
		$('#site_safety').hide();
		$('#site_summary').show();
		$('#equip_transfer_wo').hide();
		$('#equip_transfer_staff').hide();
	}
}
function click_site(site) {
	$('#main_tabs').hide();
	$('#work_orders').show();
	$('#main_back_btn').show();
	$('.site_btn').hide();
	$('[id^=work_orders_]').hide();
	$('#work_orders_'+site).show();
	$('#work_order_all').addClass('active_tab');
	$('#work_order_sites').removeClass('active_tab');
	if(site == 'all') {
		$('#work_order_all').removeAttr('onclick').attr('onclick',"click_site('all'); return false;").text('All Work Orders');
	} else {
		$('#work_order_all').removeAttr('onclick').attr('onclick',"click_site('"+site+"'); return false;").text('Work Orders for '+$('a[data-site-id='+site+']').text());
	}
}
function click_equip_lead(staff) {
	$('#back_btn').show();
	$('#main_back_btn').show();
	$('#equip_sites_'+staff).show();
	$('.site_btn').show();
	$('.lead_btn').hide();
	$('[id^=equip_list_]').hide();
}
function click_equip_site(site) {
	$('#main_tabs').hide();
	$('#work_orders').show();
	$('#back_btn').show();
	$('#main_back_btn').show();
	$('.lead_btn').hide();
	$('.site_btn').hide();
	$('#equip_list_'+site).show().parents('[id^=equip_sites_]').show();
}
function click_back_equip_leads() {
	$('#main_back_btn').show();
	$('#back_btn').hide();
	$('.lead_btn').show();
	$('[id^=sites]').hide();
}
function click_trans_equip_lead(staff) {
	$('#back_btn').show();
	$('#main_back_btn').show();
	$('#trans_equip_sites_'+staff).show();
	$('.site_btn').show();
	$('.lead_btn').hide();
	$('[id^=trans_equip_list_]').hide();
}
function click_trans_equip_site(site) {
	$('#main_tabs').hide();
	$('#work_orders').show();
	$('#back_btn').show();
	$('#main_back_btn').show();
	$('.lead_btn').hide();
	$('.site_btn').hide();
	$('#trans_equip_list_'+site).show().parents('[id^=trans_equip_sites_]').show();
}
function click_trans_equip_wo() {
	$('#trans_equip_list').show();
	$('#trans_equip_list_staff').hide();
	$('#equip_transfer_wo').addClass('active_tab').removeAttr('onclick').attr('onclick', "return false;");
	$('#equip_transfer_staff').removeClass('active_tab').removeAttr('onclick').attr('onclick', "click_trans_equip_staff(); return false;");
}
function click_trans_equip_staff() {
	$('#trans_equip_list').hide();
	$('#trans_equip_list_staff').show();
	$('#equip_transfer_wo').removeClass('active_tab').removeAttr('onclick').attr('onclick', "click_trans_equip_wo(); return false;");
	$('#equip_transfer_staff').addClass('active_tab').removeAttr('onclick').attr('onclick', "return false;");
}
function click_back_trans_equip_leads() {
	$('#main_back_btn').show();
	$('#back_btn').hide();
	$('.lead_btn').show();
	$('[id^=sites]').hide();
}
function click_view_invoice(workorderid) {
	$('#main_back_btn').hide();
	$('#site_summary_all').hide();
	$('#site_summary_'+workorderid).show();
}
function click_approve_site_summary(workorderid, row) {
	$.ajax({
		data: {workorderid: workorderid},
		method: 'POST',
		url: 'site_work_orders_ajax.php?fill=approve_site_summary',
		success: function(result) {
			$('#main_back_btn').hide();
			$('#site_summary_all').hide();
			$('#site_summary_'+workorderid).show();
			$(row).parent().html('<a href="" onclick="click_view_invoice('+workorderid+'); return false;">View Invoice</a>');
		}
	});
}
function click_back_site_summary(workorderid) {
	$('#main_back_btn').show();
	$('#site_summary_all').show();
	$('#site_summary_'+workorderid).hide();
}
</script>
<div class="tab-container mobile-100-container hide-titles-mob">
	<a href="?tab=schedule" id="main_back_btn" class="btn brand-btn mobile-block mobile-100 " style="display:none;">Back to Main Schedule</a>
	<a href="" id="work_order_sites" class="btn brand-btn mobile-block mobile-100 " style="display:none;" onclick="click_tab('work_orders'); return false;">Work Order Sites</a>
	<a href="" id="work_order_all" class="btn brand-btn mobile-block mobile-100 " style="display:none;" data-site-id="all" onclick="click_site('all'); return false;">Work Orders</a>
	<?php $equipment_transfer_staff = get_config($dbc, "equipment_transfer_staff");
	if ($equipment_transfer_staff == 1) { ?>
		<a href="" id="equip_transfer_wo" class="btn brand-btn mobile-block mobile-100 " style="display:none;" data-site-id="all" onclick="click_trans_equip_wo(); return false;">Work Orders</a>
		<a href="" id="equip_transfer_staff" class="btn brand-btn mobile-block mobile-100 " style="display:none;" data-site-id="all" onclick="click_trans_equip_staff(); return false;">Your Assigned Equipment</a>
	<?php } ?>
</div>
<div class="clearfix"></div>
<div id="main_tabs">
	<?php if(mysqli_num_rows($logs) == 0) { ?>
		<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 lead_btn">
			<a href="add_driving_log.php">Driving Log</a>
		</div>
	<?php } else {
		$log_id = mysqli_fetch_array($logs)['log_id']; ?>
		<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 lead_btn">
			<a href="add_driving_log.php?log_id=<?= $log_id ?>">End Driving Log</a>
		</div>
	<?php } ?>
	<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 lead_btn">
		<a href="" onclick="click_tab('site_safety'); return false;">Site Safety</a>
	</div>
	<?php
	$temp_worker_form_enabled = mysqli_fetch_array(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE `name` = 'swo_temp_worker_form'"))['value'];
	$temp_worker_form = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(*) as `num_rows`, `hrid` FROM `hr` WHERE `form` = 'Temporary Worker Orientation'"));
	if ($temp_worker_form_enabled == 1) { ?>
		<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 lead_btn">
			<a href="<?= WEBSITE_URL ?>/HR/add_manual.php?hrid=<?= $temp_worker_form['hrid'] ?>&action=view&formid=&from=<?= WEBSITE_URL ?>/Site%20Work%20Orders/site_work_orders.php?tab=schedule">Temporary Worker Orientation</a>
		</div>
	<?php } ?>
	<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 lead_btn">
		<a href="" onclick="click_tab('equip_list'); return false;">Assigned Equipment</a>
	</div>
	<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 lead_btn">
		<a href="" onclick="click_tab('trans_equip_list'); return false;">Equipment Transfer</a>
	</div>
	<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 lead_btn">
		<a href="" onclick="click_tab('work_orders'); click_site('all'); return false;">Site Work Orders</a>
	</div>
	<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 lead_btn">
		<a href="" onclick="click_tab('site_summary'); return false;">Site Summary</a>
	</div>
</div>
<div id='work_orders' style="display:none;">
	<form method="POST" action="?tab=schedule&site=all"><?php
		$search_site = '';
		$search_staff = '';
		$search_service = '';
		$search_from = '';
		$search_to = '';
		$search_label = '';
		$query_clause = '';
		if(isset($_POST['search_submit'])) {
			$search_site = $_POST['search_site'];
			$search_staff = $_POST['search_staff'];
			$search_service = $_POST['search_service'];
			$search_from = $_POST['search_from'];
			$search_to = $_POST['search_to'];
			$search_label = $_POST['search_label'];
			if($search_site > 0) {
				$query_clause = " AND `siteid`='$search_site'";
			}
			if($search_staff > 0) {
				$query_clause = " AND `staff_lead`='$search_staff'";
			}
			if($search_service != '') {
				$query_clause = " AND `service_heading` LIKE '%$search_service%'";
			}
			if($search_from != '') {
				$query_clause = " AND `work_end_date` >= '$search_from'";
			}
			if($search_to != '') {
				$query_clause = " AND `work_start_date` <= '$search_to'";
			}
			if($search_label != '') {
				$query_clause = " AND `id_label` LIKE '%$search_label%'";
			}
		} ?>

	<div class="search-group">
		<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search By Site:</label>
				</div>
				<div class="col-sm-8">
					<select data-placeholder="Select a Site" name="search_site" class="chosen-select-deselect form-control">
						<option></option>
						<?php if (get_config($dbc, 'swo_display_all_sites')) {
							$query = mysqli_query($dbc,"SELECT `contactid`, `site_name` FROM `contacts` WHERE category = 'Sites' AND `deleted`=0 AND `status`=1 ORDER BY `site_name`");
						} else {
							$query = mysqli_query($dbc,"SELECT `contactid`, `site_name` FROM `contacts` WHERE `contactid` IN (SELECT `siteid` FROM `site_work_orders` WHERE `status` NOT IN ('Pending', 'Archived')) AND `deleted`=0 AND `status`=1 ORDER BY `site_name`");
						}
						while($custid = mysqli_fetch_array($query)) { ?>
							<option <?php if ($custid['contactid'] == $search_site) { echo " selected"; } ?> value='<?php echo  $custid['contactid']; ?>' ><?php echo $custid['site_name']; ?></option><?php
						} ?>
					</select>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search By Staff:</label>
				</div>
				<div class="col-sm-8">
					<select data-placeholder="Select a Staff" name="search_staff" class="chosen-select-deselect form-control">
						<option></option>
						<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contacts`.`contactid`, `first_name`, `last_name` FROM `contacts` LEFT JOIN `site_work_orders` ON `contacts`.`contactid`=`site_work_orders`.`staff_lead` OR CONCAT(',',`site_work_orders`.`staff_crew`,',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') WHERE `contacts`.`deleted`=0 AND `contacts`.`status`=1 AND `site_work_orders`.`status` NOT IN ('Pending', 'Archived') GROUP BY `contactid`, `last_name`, `first_name`"), MYSQLI_ASSOC));
						foreach($query as $contid) { ?>
							<option <?php if ($contid == $search_staff) { echo " selected"; } ?> value='<?php echo  $contid; ?>' ><?php echo get_contact($dbc, $contid); ?></option><?php
						} ?>
					</select>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search By Service:</label>
				</div>
				<div class="col-sm-8">
					<select data-placeholder="Select a Service" name="search_service" class="chosen-select-deselect form-control">
						<option></option>
						<?php $service_query = mysqli_query($dbc, "SELECT `service_heading` FROM `site_work_orders` WHERE `status` NOT IN ('Pending', 'Archived')");
						$service_labels = [];
						while($service_row = mysqli_fetch_array($service_query)) {
							$service_labels = array_unique(array_merge($service_labels, explode('#*#',$service_row['service_heading'])));
						}
						foreach($service_labels as $service_label) { ?>
							<option <?php if ($service_label == $search_service) { echo " selected"; } ?> value='<?php echo  $service_label; ?>' ><?php echo $service_label; ?></option><?php
						} ?>
					</select>
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search By Label:</label>
				</div>
				<div class="col-sm-8">
					<input type="text" name="search_label" value="<?= $search_label ?>" class="form-control">
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search From Date:</label>
				</div>
				<div class="col-sm-8">
					<input type="text" name="search_from" value="<?= $search_from ?>" class="form-control datepicker">
				</div>
			</div>
			<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
				<div class="col-sm-4">
					<label for="site_name" class="control-label">Search To Date:</label>
				</div>
				<div class="col-sm-8">
					<input type="text" name="search_to" value="<?= $search_to ?>" class="form-control datepicker">
				</div>
			</div>
		</div>
		<div class="form-group col-lg-3 col-md-4 col-sm-12 col-xs-12">
			<div style="display:inline-block; padding: 0 0.5em;">
				<button type="submit" name="search_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
				<button type="submit" name="display_all" value="Display All" class="btn brand-btn mobile-block">Display All</button>
			</div>
		</div><!-- .form-group -->
		<div class="clearfix"></div>
	</div>
	</form>
	<?php if (get_config($dbc, 'swo_display_all_sites') == 1) {
		$get_sites = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT sites.`contactid`, sites.`site_name` status, bus.`name` first_name FROM `contacts` sites LEFT JOIN `contacts` bus on sites.`contactid`=bus.`siteid` WHERE sites.`category`='Sites' AND sites.`deleted`=0 AND sites.`status`=1 AND sites.`show_hide_user`=1"), MYSQLI_ASSOC));
		if(count($get_sites) > 0) {
			foreach($get_sites as $site_id) {
				$site = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid`='$site_id'")); ?>
				<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 site_btn">
					<a href="" data-site-id="<?= $site['contactid'] ?>" onclick="click_site('<?= $site['contactid'] ?>'); return false;"><?= $site['site_name'] ?></a>
				</div>
				<div id="work_orders_<?= $site['contactid'] ?>" style="display:none;">
					<?php $workorders = mysqli_query($dbc, "SELECT `workorderid`, `id_label` FROM `site_work_orders` WHERE `siteid`='".$site['contactid']."' AND `status` NOT IN ('Pending', 'Archived') $query_clause");
					while($order = mysqli_fetch_array($workorders)) { ?>
						<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 work_btn">
							<a href="view_work_order.php?workorderid=<?= $order['workorderid'] ?>">WO #<?= $order['id_label'] ?></a>
						</div>
					<?php } ?>
				</div>
			<?php } ?>

			<div id="work_orders_all" style="display:none;">
				<?php $workorders = mysqli_query($dbc, "SELECT `workorderid`, `id_label` FROM `site_work_orders` WHERE `status` NOT IN ('Pending', 'Archived')".$query_clause);
				while($order = mysqli_fetch_array($workorders)) { ?>
					<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 work_btn">
						<a href="view_work_order.php?workorderid=<?= $order['workorderid'] ?>">WO #<?= $order['id_label'] ?></a>
					</div>
				<?php } ?>
			</div>
		<?php } else {
			echo "<h2>No Work Orders Found</h2>";
		}
	} else {
		$get_sites = mysqli_query($dbc, "SELECT `siteid`, `site_location` FROM `site_work_orders` WHERE `status` NOT IN ('Pending', 'Archived') GROUP BY `siteid` ORDER BY `siteid`");
		if(mysqli_num_rows($get_sites) > 0) {
			while($site = mysqli_fetch_array($get_sites)) { ?>
				<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 site_btn">
					<a href="" data-site-id="<?= $site['siteid'] ?>" onclick="click_site('<?= $site['siteid'] ?>'); return false;"><?= $site['site_location'] ?></a>
				</div>
				<div id="work_orders_<?= $site['siteid'] ?>" style="display:none;">
					<?php $workorders = mysqli_query($dbc, "SELECT `workorderid`, `id_label` FROM `site_work_orders` WHERE `siteid`='".$site['siteid']."' AND `status` NOT IN ('Pending', 'Archived') $query_clause");
					while($order = mysqli_fetch_array($workorders)) { ?>
						<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 work_btn">
							<a href="view_work_order.php?workorderid=<?= $order['workorderid'] ?>">WO #<?= $order['id_label'] ?></a>
						</div>
					<?php } ?>
				</div>
			<?php } ?>

			<div id="work_orders_all" style="display:none;">
				<?php $workorders = mysqli_query($dbc, "SELECT `workorderid`, `id_label` FROM `site_work_orders` WHERE `status` NOT IN ('Pending', 'Archived')".$query_clause);
				while($order = mysqli_fetch_array($workorders)) { ?>
					<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 work_btn">
						<a href="view_work_order.php?workorderid=<?= $order['workorderid'] ?>">WO #<?= $order['id_label'] ?></a>
					</div>
				<?php } ?>
			</div>
		<?php } else {
			echo "<h2>No Work Orders Found</h2>";
		}
	} ?>
</div>
<div id='equip_list' style="display:none;">
	<?php foreach($staff_leads as $staff) { ?>
		<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 lead_btn">
			<a href="" onclick="click_equip_lead('<?= $staff ?>'); return false;"><?= ($staff == 'all' ? 'All Work Orders' : get_contact($dbc, $staff)) ?></a>
		</div>
		<div id="equip_sites_<?= $staff ?>" style="display:none;">
			<?php $get_sites = mysqli_query($dbc, "SELECT `siteid`, `site_location` FROM `site_work_orders` WHERE `status` NOT IN ('Pending', 'Archived') AND (`staff_lead`='$staff' OR '$staff'='all')  GROUP BY `siteid` ORDER BY `siteid`");
			if(mysqli_num_rows($get_sites) > 0) {
				while($site = mysqli_fetch_array($get_sites)) { ?>
					<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 site_btn">
						<a href="" onclick="click_equip_site('<?= $site['siteid'] ?>'); return false;"><?= $site['site_location'] ?></a>
					</div>
					<div id="equip_list_<?= $site['siteid'] ?>" style="display:none;">
						<?php $workorders = mysqli_query($dbc, "SELECT `workorderid` FROM `site_work_orders` WHERE `siteid`='".$site['siteid']."' AND `status` NOT IN ('Pending', 'Archived') AND (`staff_lead`='$staff' OR '$staff'='all')");
						while($order = mysqli_fetch_array($workorders)) { ?>
							<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 work_btn">
								<a href="check_equip_list.php?workorderid=<?= $order['workorderid'] ?>">WO #<?= $order['workorderid'] ?></a>
							</div>
						<?php } ?>
					</div>
				<?php }
			} else {
				echo "<h2>No Work Orders Found</h2>";
			}
			?>
		</div>
	<?php } ?>
</div>
<div id='trans_equip_list' style="display:none;">
	<?php $workorders = mysqli_query($dbc, "SELECT * FROM `site_work_orders` WHERE `equipment_id` != ''");
	if(mysqli_num_rows($workorders) > 0) {
	while($order = mysqli_fetch_array($workorders)) { ?>
		<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 work_btn">
			<a href="equip_list_transfer.php?workorderid=<?= $order['workorderid'] ?>">WO #<?= $order['workorderid'] ?></a>
		</div>
	<?php }
	} else {
		echo "<h2>No Equipment is Attached to Work Orders.</h2>";
	} ?>
</div>
<?php if ($equipment_transfer_staff == 1) { ?>
<div id='trans_equip_list_staff' style="display:none;">
	<?php $equip_staffid = $_SESSION['contactid'];
		$equip_staff = mysqli_query($dbc, "SELECT * FROM `equipment` WHERE `assigned_staff` = '$equip_staffid' AND `deleted` = 0");
		if(mysqli_num_rows($equip_staff) > 0) {
	?>
	<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
		<div id="no-more-tables">
			<table class="table table-bordered">
				<tr class="hidden-sm hidden-xs">
					<th>Equipment</th>
					<th>Assigned Staff</th>
					<th>Transfer to Staff</th>
				</tr>
				<?php
				while ($row = mysqli_fetch_array($equip_staff)) {
					$equipmentid = $row['equipmentid'];
					$assigned_staff = $row['assigned_staff'];
					$equip_category = $row['category'];
					$equip_unit = $row['unit_number'];
				?>
				<input type="hidden" name="equip_transfer[]" value="<?= $equipmentid ?>">
				<input type="hidden" name="equip_assigned_staff_<?= $equipmentid ?>" value="<?= $assigned_staff ?>">

				<tr class="hidden-sm hidden-xs">
					<td data-title="Equipment:"><?= $equip_category.": Unit#".$equip_unit ?></td>
					<td data-title="Assigned Staff:"><?= get_contact($dbc, $assigned_staff) ?></td>
					<td data-title="Transfer to Staff:">
						<select data-placeholder="Select a Staff" class="chosen-select-deselect" name="transfer_staff_<?= $equipmentid ?>">
							<option></option>
							<?php
								$staff_list = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = 'Staff' AND `deleted` = 0 AND `status` = 1");
								while ($row = mysqli_fetch_array($staff_list)) {
									echo '<option value="'.$row['contactid'].'">'.get_contact($dbc, $row['contactid']).'</option>';
								}
							?>
						</select>
					</td>
				</tr>
				<?php } ?>
			</table>
		</div>

		<div class="form-group">
			<label class="col-sm-4">Sign off on Transfers:<br /><em>Sign here for any transfers that have been noted above.</em></label>
			<div class="col-sm-8">
				<label class="col-sm-12">I confirm that the above transfers are correct.</label>
				<?php $output_name = 'transfer_sign';
				include('../phpsign/sign_multiple.php'); ?>
			</div>
		</div>

		<div class="form-group">
			<div class="col-sm-12">
				<button	type="submit" name="submit_transfer_staff" value="submit_transfer_staff" class="btn brand-btn btn-lg pull-right">Submit</button>
			</div>
			<div class="clearfix"></div>
		</div>
	</form>
	<?php } else {
		echo "<h2>No Assigned Equipment Found</h2>";
	} ?>
</div>
<?php } ?>
<div id='site_safety' style="display:none;">
	<?php $flhaid = mysqli_fetch_array(mysqli_query($dbc, "SELECT MIN(safetyid) id FROM `safety` WHERE `form`='Field Level Hazard Assessment'"))['id']; ?>
	<div class="dashboard link col-lg-3 col-md-4 col-sm-6 col-xs-12 work_btn">
		<a href="<?= WEBSITE_URL ?>/Safety/add_manual.php?safetyid=<?= $flhaid ?>&action=view&formid=new&return_url=<?= urlencode(WEBSITE_URL.'/Site Work Orders/site_work_orders.php?tab=schedule') ?>">Field Level Hazard Assessment</a>
	</div>
</div>
<div id='site_summary' style="display:none;">
	<div id="no-more-tables">
		<div id="site_summary_all">
			<table class="table table-bordered">
				<tr class="hidden-sm hidden-xs">
					<th>Site</th>
					<th>Work Order #</th>
					<th>Staff & Crew</th>
					<th>Total Hours</th>
					<th><?= TASK_TILE ?> Done On Site</th>
					<th>Function</th>
				</tr>
				<?php
					$work_orders = mysqli_query($dbc, "SELECT * FROM `site_work_orders` WHERE `status` NOT IN ('Pending', 'Archived') ORDER BY `workorderid` DESC");
					while ($work_order = mysqli_fetch_array($work_orders)) {
						$workorderid = $work_order['workorderid'];
						$site_location = $work_order['site_location'];
						$id_label = $work_order['id_label'];

						$crew_list = [ 'Lead: '.get_contact($dbc, $work_order['staff_lead']) ];
						$staff_crew = explode(',',$work_order['staff_crew']);
						$staff_pos = explode(',',$work_order['staff_positions']);
						foreach($staff_crew as $i => $id) {
							$crew_list[] = get_contact($dbc, $id).': '.get_positions($dbc, $staff_pos[$i], 'name');
						}

						$total_hours = 0;
						$all_tasks = [];
						$summary_list = explode('#*#',$work_order['summary']);
						foreach($summary_list as $summary) {
							$summary_staff = explode('**#**',$summary);
							if(!in_array($summary_staff[1], $all_tasks)) {
								$all_tasks[] = $summary_staff[1];
							}
							$total_hours += intval($summary_staff[2]);
						}

						$site_summary_status = $work_order['site_summary_status'];
						?>

						<tr class="hidden-sm hidden-xs">
							<td data-title="Site:"><?= $site_location ?></td>
							<td data-title="Work Order #:"><?= $id_label ?></td>
							<td data-title="Staff & Crew:"><?= implode("<br />\n", $crew_list) ?></td>
							<td data-title="Total Hours:"><?= $total_hours ?></td>
							<td data-title="Tasks Done On Site:"><?= implode("<br />\n", $all_tasks) ?></td>
							<td data-title="Function:">
							<?php if ($site_summary_status == 'Approved') { ?>
								<a href="" onclick="click_view_invoice(<?= $workorderid ?>); return false;">View Invoice</a>
							<?php } else { ?>
								<a href="" onclick="click_approve_site_summary(<?= $workorderid ?>, this); return false;">Approve</a>
								| <a href="view_work_order.php?workorderid=<?= $work_order['workorderid'] ?>">Reject</a>
							<?php } ?>
							</td>
						</tr>
					<?php }
				?>
			</table>
		</div>
		<?php
		$work_orders = mysqli_query($dbc, "SELECT * FROM `site_work_orders` ORDER BY `workorderid` DESC");
		while($work_order = mysqli_fetch_array($work_orders)) {
			$workorderid = $work_order['workorderid'];
			$id_label = $work_order['id_label'];
			$total_hours = 0;
			$total_staff = 0;
			$total_services = 0;
			$total_amount = 0;
		?>
			<div id="site_summary_<?= $workorderid ?>" style="display:none;">
				<div class="tab-container">
					<a href="" id="site_summary_back_btn" class="btn brand-btn" onclick="click_back_site_summary(<?= $workorderid ?>); return false;">Back to Site Summary</a>
				</div>
				<h3>WO #<?= $id_label ?></h3>
				<table class="table table-bordered">
					<tr class="hidden-sm hidden-xs">
						<th>Service Category</th>
						<th>Heading</th>
						<th>Rate</th>
					</tr>
				<?php
					$service_cat = explode('#*#', $work_order['service_cat']);
					$service_heading = explode('#*#', $work_order['service_heading']);
					$service_rates = explode('#*#', $work_order['service_rates']);
					for ($i = 0; $i < count($service_cat); $i++) { ?>
						<tr class="hidden-sm hidden-xs">
							<td data-title="Service Category:"><?= $service_cat[$i] ?></td>
							<td data-title="Heading:"><?= $service_heading[$i] ?></td>
							<td data-title="Rate:"><span style="float:right;">$<?= number_format($service_rates[$i], 2, '.', '') ?></span></td>
						</tr>
					<?php
						$total_services += $service_rates[$i];
						$total_amount += $service_rates[$i];
					} ?>
						<tr class="hidden-sm hidden-xs">
							<td colspan="2"><b style="float:right;">Services Total:</td>
							<td data-title="Total Services:"><span style="float:right;">$<?= number_format($total_services, 2, '.', '') ?></span></td>
						</tr>
				</table>
				<table class="table table-bordered">
					<tr class="hidden-sm hidden-xs">
						<th>Staff</th>
						<th>Task</th>
						<th>Hours</th>
						<th>Amount</th>
					</tr>
				<?php
				$summary_list = explode('#*#',$work_order['summary']);
				foreach($summary_list as $summary) {
					$summary_staff = explode('**#**',$summary);
					$staff_name = get_contact($dbc, $summary_staff[0]);
					$task = $summary_staff[1];
					$hours = $summary_staff[2];
					$staff_rate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `staff_rate_table` WHERE CONCAT(',', `staff_id`,',') LIKE '%,".$summary_staff[0].",%' AND `deleted` = 0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')"))['hourly'];
					$amount = intval($hours) * $staff_rate;
					$total_hours += intval($hours);
					$total_staff += $amount;
					$total_amount += $amount;
				?>
					<tr class="hidden-sm hidden-xs">
						<td data-title="Staff:"><?= $staff_name ?></td>
						<td data-title="Task:"><?= $task ?></td>
						<td data-title="Hours:"><span style="float:right;"><?= $hours ?></span></td>
						<td data-title="Amount:"><span style="float:right;">$<?= number_format($amount, 2, '.', '') ?></span></td>
					</tr>
				<?php } ?>
					<tr class="hidden-sm hidden-xs">
						<td colspan="2"><b style="float:right;">Staff Total:</b></td>
						<td data-title="Total Hours:"><span style="float:right;"><?= $total_hours ?></span></td>
						<td data-title="Total Staff:"><span style="float:right;">$<?= number_format($total_staff, 2, '.', '') ?></span></td>
					</tr>
				</table>
				<h3 class="pull-right">Total: $<?= number_format($total_amount, 2, '.', '') ?></h3>
			</div>
		<?php } ?>
	</div>
</div>