<form method="POST" action=""><?php
	$search_site = '';
	$search_staff = '';
	$search_service = '';
	$search_from = '';
	$search_to = '';
	$query_clause = '';
	if(isset($_POST['search_submit'])) {
		$search_site = $_POST['search_site'];
		$search_staff = $_POST['search_staff'];
		$search_service = $_POST['search_service'];
		$search_from = $_POST['search_from'];
		$search_to = $_POST['search_to'];
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
	}
?>

<div class="search-group">
	<div class="form-group col-lg-9 col-md-8 col-sm-12 col-xs-12">
		<div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
			<div class="col-sm-4">
				<label for="site_name" class="control-label">Search By Site:</label>
			</div>
			<div class="col-sm-8">
				<select data-placeholder="Select a Site" name="search_site" class="chosen-select-deselect form-control">
					<option></option>
					<?php $query = mysqli_query($dbc,"SELECT `contactid`, `site_name` FROM `contacts` WHERE `contactid` IN (SELECT `siteid` FROM `site_work_orders` WHERE `status`".($wo_type != 'Approved' ? "='$wo_type'" : " NOT IN ('Pending', 'Archived')").") AND `deleted`=0 AND `status`=1 ORDER BY `site_name`");
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
					<?php $query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc,"SELECT `contacts`.`contactid`, `first_name`, `last_name` FROM `contacts` LEFT JOIN `site_work_orders` ON `contacts`.`contactid`=`site_work_orders`.`staff_lead` OR CONCAT(',',`site_work_orders`.`staff_crew`,',') LIKE CONCAT('%,',`contacts`.`contactid`,',%') WHERE `contacts`.`deleted`=0 AND `contacts`.`status`=1 AND `site_work_orders`.`status`".($wo_type != 'Approved' ? "='$wo_type'" : " NOT IN ('Pending', 'Archived')")." GROUP BY `contactid`, `last_name`, `first_name`"), MYSQLI_ASSOC));
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
					<?php $service_query = mysqli_query($dbc, "SELECT `service_heading` FROM `site_work_orders` WHERE `status`".($wo_type != 'Approved' ? "='$wo_type'" : " NOT IN ('Pending', 'Archived')")."");
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

<?php if ( $edit_access == 1 ) { ?>
	<div class="col-sm-12 col-xs-12 col-lg-4 pad-top offset-xs-top-20 pull-right">
		<a href="add_work_order.php" class="btn brand-btn mobile-block gap-bottom pull-right">Add Work Order</a>
	</div>
<?php } ?>
<div class="clearfix"></div>
<?php $work_orders = mysqli_query($dbc, "SELECT * FROM `site_work_orders` WHERE `status`".($wo_type == 'Pending' ? "='Pending'" : " NOT IN ('Pending', 'Archived')")." $query_clause ORDER BY `workorderid` DESC");
if(mysqli_num_rows($work_orders) > 0): ?>
<div id="no-more-tables">
	<table class="table table-bordered table-striped">
		<thead>
            <tr class="hidden-sm hidden-xs">
                <th>Work Order #</th>
                <th>Site</th>
                <th>Staff & Crew</th>
                <th>Services</th>
                <th>Equipment</th>
                <th>Material</th>
                <th>PO</th>
                <th>PDF</th>
                <th>Function</th>
            </tr>
        </thead>
		<?php while($work_order = mysqli_fetch_array($work_orders))
		{
			$crew_list = [ 'Lead: '.get_contact($dbc, $work_order['staff_lead']) ];
			$staff_crew = explode(',',$work_order['staff_crew']);
			$staff_pos = explode(',',$work_order['staff_positions']);
			$staff_est = explode(',',$work_order['staff_estimate']);
			foreach($staff_crew as $i => $id) {
				$crew_list[] = get_contact($dbc, $id).': '.get_positions($dbc, $staff_pos[$i], 'name').' - '.$staff_est[$i];
			}
			$service_list = [];
			$service_cat = explode('#*#',$work_order['service_cat']);
			$service_headings = explode('#*#',$work_order['service_heading']);
			foreach($service_headings as $j => $heading) {
				$service_list[] = $service_cat[$j].': '.$heading;
			}
			$equip_list = [];
			$equipments = explode(',',$work_order['equipment_id']);
			$equip_rates = explode(',',$work_order['equipment_rate']);
			foreach($equipments as $i => $id) {
				$equipment = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `type`, `unit_number`, `make`, `model`, `hourly_rate`, `monthly_rate`, `semi_monthly_rate`, `daily_rate`, `status` FROM `equipment` WHERE `equipmentid`='$id'"));
				$rate = $equip_rates[$i];
				$equip_list[] = $equipment['category'].' '.$equipment['type'].' #'.$equipment['unit_number'].': '.$equipment['make'].' '.$equipment['model'].' ($'.$rate.')';
			}
			$material_list = [];
			$materials = explode(',',$work_order['material_id']);
			$material_qty = explode(',',$work_order['material_qty']);
			foreach($materials as $i => $id) {
				$material = mysqli_fetch_array(mysqli_query($dbc, "SELECT `category`, `name`, `quantity` FROM `material` WHERE `materialid`='$id'"));
				$material_list[] = $material['category'].' '.$material['name'].' Qty: '.$material_qty[$i].(!empty($material['quantity']) ? '('.$material['quantity'].' available)' : '');
			}
			$po_list = [];
			$orders = explode(',',$work_order['po_id']);
			foreach($orders as $id) {
				if($id != '') {
					$po = mysqli_fetch_array(mysqli_query($dbc, "SELECT `poid`, `issue_date` FROM `site_work_po` WHERE `poid`='$id'"));
					$po_list[] = '<a href="add_po.php?poid='.$po['poid'].'" target="po_'.$po['poid'].'">PO #'.$po['poid'].': '.$po['issue_date'].'</a>';
				}
			} ?>
			<tr>
				<td data-title="Work Order #:">
				<?php if($edit_access == 1) { ?>
					<a href="add_work_order.php?workorderid=<?= $work_order['workorderid'] ?>"><?= $work_order['id_label'] ?></a>
				<?php } else { ?>
					<a href="view_work_order.php?workorderid=<?= $work_order['workorderid'] ?>"><?= $work_order['id_label'] ?></a>
				<?php } ?></td>
				<td data-title="Site:"><?= $work_order['site_location']; ?></td>
				<td data-title="Staff & Crew:"><?= implode("<br />\n", $crew_list); ?></td>
				<td data-title="Services:"><?= implode("<br />\n", $service_list); ?></td>
				<td data-title="Equipment:"><?= implode("<br />\n", $equip_list); ?></td>
				<td data-title="Materials:"><?= implode("<br />\n", $material_list); ?></td>
				<td data-title="PO:"><?= implode("<br />\n", $po_list); ?></td>
				<td data-title="PDF:"><a href="work_order_pdf.php?workorderid=<?= $work_order['workorderid'] ?>" target="_blank">View PDF</a></td>
				<td data-title="Function:">
				<?php if($edit_access == 1) { ?>
					<a href="add_work_order.php?workorderid=<?= $work_order['workorderid'] ?>">Edit</a> |
					<a href="../delete_restore.php?action=archive&site_work_order=<?= $work_order['workorderid'] ?>">Archive</a>
					<?= ($current_tab == 'pending' ? '|' : '') ?>
				<?php } ?>
				<?php if($current_tab == 'pending') { ?>
					Email For Approval
					<?php if($approval_access == 1) { ?>
						| <a href="../delete_restore.php?action=approve&site_work_order=<?= $work_order['workorderid'] ?>">Approve</a>
						| <a href="../delete_restore.php?action=reject&site_work_order=<?= $work_order['workorderid'] ?>">Reject</a>
					<?php }
				} ?>
				</td>
			</tr>
		<?php } ?>
	</table>
</div>
<?php else:
	echo "<h2>No Work Orders Found</h2>";
endif; ?>
<?php if ( $edit_access == 1 ) { ?>
	<div class="col-sm-12 col-xs-12 col-lg-4 pad-top offset-xs-top-20 pull-right">
		<a href="add_work_order.php" class="btn brand-btn mobile-block gap-bottom pull-right">Add Work Order</a>
	</div>
<?php } ?>