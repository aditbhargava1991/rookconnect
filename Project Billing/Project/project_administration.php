<?php include_once('../include.php');
include_once('../Project/project_administration_functions.php');
if(isset($_GET['projectid'])) {
	$projectid = $_GET['projectid'];
}
$project_admin_multiday_tickets = get_config($dbc, 'project_admin_multiday_tickets');
$value_config = ','.get_config($dbc, 'project_admin_fields').',';

// Figure out which tab we are on
$name = explode('_',$_GET['tab']);
$status = $name[2];
$id = $name[1];
$filter_region = $name[3];
$filter_class = $name[4];
$filter_site = $name[5];
$filter_business = $name[6];

// Get the approval settings for the current tab
$admin_groups = $dbc->query("SELECT * FROM `field_config_project_admin` WHERE `deleted`=0 AND CONCAT(',',`contactid`,',') LIKE '%,{$_SESSION['contactid']},%'");
for($admin_group = $admin_groups->fetch_assoc(); $admin_group['id'] != $id && !empty($admin_group['name']); $admin_group = $admin_groups->fetch_assoc());
$ticket_db = explode(',',get_field_config($dbc, 'tickets_dashboard'));
$approv_count = $admin_group['precedence'] > 1 ? count(array_filter(explode(',',$admin_group['contactid']))) : 1; ?>
<h3>Administration - <?= $admin_group['name'] ?>: <?= ucfirst($status).($admin_group['region'] != '' ? ' <em><small>'.$admin_group['region'].'</small></em>' : '').($admin_group['classification'] != '' ? ' <em><small>'.$admin_group['classification'].'</small></em>' : '').($admin_group['location'] != '' ? ' <em><small>'.$admin_group['location'].'</small></em>' : '').($admin_group['customer'] > 0 ? ' <em><small>'.get_contact($dbc,$admin_group['customer'],'full_name').'</small></em>' : '') ?></h3>
<script>
$(document).ready(function() {
	$('[name=approvals]').change(function() {
		<?php if($admin_group['signature'] > 0) { ?>
			overlayIFrame('../Project/project_admin_sign.php?table='+$(this).data('table')+'&id='+this.value+'&date='+$(this).data('date')+'&invoice='+$(this).data('invoice'));
		<?php } else { ?>
			$.post('../Project/projects_ajax.php?action=approvals', {
				field: 'approvals',
				table: $(this).data('table'),
				contactid: '<?= $_SESSION['contactid'] ?>',
				status: this.checked ? 1 : 0,
				id: this.value,
				date: $(this).data('date'),
				invoice: $(this).data('invoice')
			}).success(function(response) {
				console.log(response);
			});
		<?php } ?>
	});
});
function revisionStatus(img, status, ticket) {
	$(img).closest('td').find('img').toggle();
	setRevision($(img).data('table'), status, ticket);
}
function setRevision(table, status, ticket, date = '') {
	$.post('../Project/projects_ajax.php?action=approvals', {
		field: 'revision_required',
		table: table,
		contactid: '<?= $_SESSION['contactid'] ?>',
		status: status ? 1 : 0,
		id: ticket,
		date: date
	},function(response) {console.log(response);});
}
</script>
<?php $tickets = get_administration_tickets($dbc, $_GET['tab'], $projectid, isset($ticket_conf_list) ? $ticket_conf_list : []);
if($tickets->num_rows > 0) { ?>
	<div id="no-more-tables">
		<table class="table table-bordered">
			<tr class="hidden-sm hidden-xs">
				<th>Date</th>
				<th><?= empty($ticket_noun) ? TICKET_NOUN : $ticket_noun ?> (Click to View)</th>
				<?php if(strpos($value_config, ',Services,') !== FALSE) { ?>
					<th>Services</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Sub Totals per Service,') !== FALSE) { ?>
					<th>Sub Totals per Service</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Staff Tasks,') !== FALSE) { ?>
					<th>Staff</th>
					<th>Task</th>
					<th>Hours</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Inventory,') !== FALSE) { ?>
					<th>Inventory</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Materials,') !== FALSE) { ?>
					<th>Materials</th>
				<?php } ?>
				<?php if(strpos($value_config, ',Misc Item,') !== FALSE) { ?>
					<th>Miscellaneous</th>
				<?php } ?>
				<th>Total</th>
				<th>Notes</th>
				<?php if(strpos($value_config, ',Extra Billing,') !== FALSE) { ?>
					<th>Extra Billing</th>
				<?php } ?>
				<th>Approve</th>
				<?php foreach($admin_group_managers as $admin_manager_id => $admin_manager_name) {
					if($admin_manager_id != $_SESSION['contactid']) { ?>
						<th><?= $admin_manager_name ?></th>
					<?php }
				} ?>
				<th>In Revision</th>
			</tr>
			<?php while($ticket = $tickets->fetch_assoc()) {
				$active = 0;
				$total_cost = 0.00;
				$services_cost = [];
				$services = [];
				$qty = explode(',',$ticket['service_qty']);
				foreach(explode(',',$ticket['serviceid']) as $i => $service) {
					if($service > 0) {
						$service = $dbc->query("SELECT `services`.`heading`, `rate`.`cust_price` FROM `services` LEFT JOIN `company_rate_card` `rate` ON `services`.`serviceid`=`rate`.`item_id` AND `rate`.`tile_name` LIKE 'Services' WHERE `services`.`serviceid`='$service'")->fetch_assoc();
						$services[] = $service['heading'].($qty[$i] > 0 ? ' x '.$qty[$i] : '');
						$services_cost[] = '$'.number_format($service['cust_price'],2);
					}
				} ?>
				<tr>
					<td data-title="Date"><?= $ticket['ticket_date'] ?></td>
					<td data-title="<?= empty($ticket_noun) ? TICKET_NOUN : $ticket_noun ?>"><a href="../Ticket/index.php?edit=<?= $ticket['ticketid'] ?>" onclick="overlayIFrameSlider(this.href+'&calendar_view=true'); return false;"><?= get_ticket_label($dbc, $ticket) ?></a></td>
					<?php if(strpos($value_config, ',Services,') !== FALSE) {
						foreach($services_cost as $cost_amt) {
							$total_cost += $cost_amt;
						} ?>
						<td data-title="Services"><?= implode('<br />',$services) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Sub Totals per Service,') !== FALSE) { ?>
						<td data-title="Sub Totals per Service"><?= implode('<br />',$services_cost) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Staff Tasks,') !== FALSE) {
						$staff_tasks_staff = [];
						$staff_tasks_task = [];
						$staff_tasks_hours = [];
						$sql = "SELECT * FROM `ticket_attached` WHERE `ticketid` = '{$ticket['ticketid']}' AND `deleted` = 0 AND `src_table` = 'Staff_Tasks'";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `date_stamp` = '{$ticket['ticket_date']}'";
						}
						$query = mysqli_query($dbc, $sql);
						while($row = mysqli_fetch_assoc($query)) {
							$staff_tasks_staff[] = get_contact($dbc, $row['item_id']);
							$staff_tasks_task[] = $row['position'];
							$staff_tasks_hours[] = number_format($row['hours_tracked'],2);
						} ?>
						<td data-title="Staff"><?= implode("<br />", $staff_tasks_staff) ?></td>
						<td data-title="Task"><?= implode("<br />", $staff_tasks_task) ?></td>
						<td data-title="Hours"><?= implode("<br />", $staff_tasks_hours) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Inventory,') !== FALSE) {
						$inventory = [];
						$sql = "SELECT * FROM `ticket_attached` WHERE `ticketid` = '{$ticket['ticketid']}' AND `deleted` = 0 AND `src_table` = 'inventory'";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `date_stamp` = '{$ticket['ticket_date']}'";
						}
						$query = mysqli_query($dbc, $sql);
						while($row = mysqli_fetch_assoc($query)) {
							if($row['description'] != '') {
								$inventory[] = $row['description'].': '.round($row['qty'],3).' @ $'.number_format($row['rate'],2).': $'.number_format($row['qty'] * $row['rate'],2);
								$total_cost += $row['qty'] * $row['rate'];
							} else {
								$inv_row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `product_name`, `name`, `final_retail_price` FROM `inventory` WHERE `inventoryid` = '{$row['item_id']}'"));
								$inventory[] = (empty($inv_row['product_name']) ? $inv_row['name'] : $inv_row['product_name']).': '.round($row['qty'],3).' @ $'.number_format($row['rate'] > 0 ? $row['rate'] : $inv_row['final_retail_price'],2).': $'.number_format($row['qty'] * $inv_row['final_retail_price'],2);
								$total_cost += $row['qty'] * ($row['rate'] > 0 ? $row['rate'] : $inv_row['final_retail_price']);
							}
						} ?>
						<td data-title="Inventory"><?= implode("<br />", $inventory) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Materials,') !== FALSE) {
						$materials = [];
						$sql = "SELECT * FROM `ticket_attached` WHERE `ticketid` = '{$ticket['ticketid']}' AND `deleted` = 0 AND `src_table` = 'material'";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `date_stamp` = '{$ticket['ticket_date']}'";
						}
						$query = mysqli_query($dbc, $sql);
						while($row = mysqli_fetch_assoc($query)) {
							if($row['description'] != '') {
								$materials[] = $row['description'].': '.round($row['qty'],3);
							} else {
								$materials[] = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `material` WHERE `materialid` = '{$row['item_id']}'"))['name'].': '.round($row['qty'],3);
							}
						} ?>
						<td data-title="Materials"><?= implode("<br />", $materials) ?></td>
					<?php } ?>
					<?php if(strpos($value_config, ',Misc Item,') !== FALSE) {
						$misc = [];
						$sql = "SELECT * FROM `ticket_attached` WHERE `ticketid` = '{$ticket['ticketid']}' AND `deleted` = 0 AND `src_table` = 'misc_item'";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `date_stamp` = '{$ticket['ticket_date']}'";
						}
						$query = mysqli_query($dbc, $sql);
						while($row = mysqli_fetch_assoc($query)) {
							$misc[] = $row['description'].': '.round($row['qty'],3).' @ $'.number_format($row['rate'],2).': $'.number_format($row['qty'] * $row['rate'],2);
							$total_cost += $row['qty'] * $row['rate'];
						} ?>
						<td data-title="Miscellaneous"><?= implode("<br />", $misc) ?></td>
					<?php } ?>
					<td data-title="Total">$<?= number_format($total_cost,2); ?></td>
					<td data-title="Notes"><?php $notes = $dbc->query("SELECT * FROM `ticket_comment` WHERE `ticketid`='{$ticket['ticketid']}' AND `type`='administration_note' AND `deleted`=0") ?></td>
					<?php if(strpos($value_config, ',Extra Billing,') !== FALSE) {
						$sql = "SELECT COUNT(*) `num` FROM `ticket_comment` WHERE `ticketid` = '{$ticket['ticketid']}' AND '{$ticket['ticketid']}' > 0 AND `type` = 'service_extra_billing' AND `deleted` = 0";
						if($project_admin_multiday_tickets == 1) {
							$sql .= " AND `created_date` = '{$ticket['ticket_date']}'";
						}
						$extra_billing = mysqli_fetch_assoc(mysqli_query($dbc, $sql)); ?>
						<td data-title="Extra Billing"><?= $extra_billing['num'] > 0 ? '<img class="inline-img small no-toggle" title="Extra Billing" src="../img/icons/ROOK-status-paid.png">' : '' ?></td>
					<?php } ?>
					<td data-title="Approvals"><?php if((strpos(','.$ticket['approvals'].',',','.$_SESSION['contactid'].',') !== FALSE && $project_admin_multiday_tickets != 1) || (strpos(','.$ticket['approvals'].',',','.$_SESSION['contactid'].'#*#'.$ticket['ticket_date'].',') !== FALSE)) {
						$approved_already = [];
						$approved = array_filter(explode(',',$ticket['approvals']));
						foreach($approved as $approvalid) {
							$approvalid = explode('#*#',$approvalid)[0];
							if(!in_array($approvalid,$approved_already)) {
								profile_id($dbc, $approvalid);
								$approved_already[] = $approvalid;
							}
						}
						// if($manager_count != count($approved)) {
							// echo "Missing ".($manager_count - count($approved))." Approval".($manage_count - count($approved) > 1 ? 's' : '');
						// }
					} else if((strpos(','.$ticket['revision_required'].',',','.$_SESSION['contactid'].',') !== FALSE && $project_admin_multiday_tickets != 1) || (strpos(','.$ticket['revision_required'].',',','.$_SESSION['contactid'].'#*#'.$ticket['ticket_date'].',') !== FALSE)) {
						echo "In Revision";
					} else { ?>
						<label class="form-checkbox any-width no-pad"><input type="checkbox" name="approvals" data-invoice="<?= count($approved) >= $approv_count - 1 && !in_array('Invoicing',$ticket_db) ? 'true' : '' ?>" data-table="tickets" <?= $project_admin_multiday_tickets == 1 ? 'data-date="'.$ticket['ticket_date'].'"' : '' ?> value="<?= $ticket['ticketid'] ?>"> Approve</label>
					<?php } ?></td>
					<?php foreach($admin_group_managers as $admin_manager_id => $admin_manager_name) {
						if($admin_manager_id != $_SESSION['contactid']) { ?>
							<td data-title="Approval by <?= $admin_manager_name ?>">
								<?php if(strpos(','.$ticket['revision_required'].',',','.$admin_manager_id.',') !== FALSE) { ?>
									In Revision
								<?php } else if(strpos(','.$ticket['approvals'].',',','.$admin_manager_id.',') !== FALSE) {
									profile_id($dbc, $approvalid);
								} ?>
							</td>
						<?php }
					} ?>
					<td data-title="In Revision">
						<label class="form-checkbox any-width"><input type="checkbox" <?= ((strpos(','.$ticket['revision_required'].',',','.$_SESSION['contactid'].',') !== FALSE && $project_admin_multiday_tickets != 1) || (strpos(','.$ticket['revision_required'].',',','.$_SESSION['contactid'].'#*#'.$ticket['ticket_date'].',') !== FALSE)) ? 'checked' : '' ?> onchange="setRevision('tickets', this.checked,<?= $ticket['ticketid'] ?>, '<?= $project_admin_multiday_tickets == 1 ? $ticket['ticket_date'] : '' ?>');"></label>
						<!--<img class="inline-img cursor-hand" src="../img/icons/ROOK-status-error.png" data-table="tickets" onclick="revisionStatus(this,false,<?= $ticket['ticketid'] ?>);" style="<?= strpos(','.$ticket['revision_required'].',',','.$_SESSION['contactid'].',') !== FALSE ? '' : 'display:none;' ?>">
						<img class="inline-img cursor-hand" src="../img/icons/ROOK-status-completed.png" data-table="tickets" onclick="revisionStatus(this,true,<?= $ticket['ticketid'] ?>);" style="<?= strpos(','.$ticket['revision_required'].',',','.$_SESSION['contactid'].',') !== FALSE ? 'display:none;' : '' ?>">-->
					</td>
				</tr>
			<?php } ?>
		</table>
	</div>
<?php } else {
	echo "<h3>No ".(empty($ticket_tile) ? TICKET_TILE : $ticket_tile)." Found.</h3>";
} ?>
