<?php if(!empty($_GET['settings'])) {
	switch($_GET['settings']) {
		case 'tabs':
			$page_title = 'Enable Tabs';
			break;
		case 'promo':
			$page_title = $tile_title.' - Promotion';
			break;
		default:
			$page_title = 'Field Settings';
			break;
	} ?>
	<a href="?settings=fields"><li class="<?= $_GET['settings'] == 'fields' || empty($_GET['settings']) ? 'active blue' : '' ?>">Fields</li></a>
	<a href="?settings=tabs"><li class="<?= $_GET['settings'] == 'tabs' ? 'active blue' : '' ?>">Enable Tabs</li></a>
	<a href="?settings=promo"><li class="<?= $_GET['settings'] == 'promo' ? 'active blue' : '' ?>">Promotions</li></a>
<?php } else {
	$po_tabs = explode(',',get_config($dbc,'po_tabs'));
    $ticket_tabs = [ '' => TICKET_TILE ];
    foreach(explode(',',get_config($dbc,'ticket_tabs')) as $ticket_tab) {
        $ticket_tabs[config_safe_str($ticket_tab)] = $ticket_tab;
    }

	if(in_array_starts('summary ',$po_tabs) && check_subtab_persmission($dbc, 'purchase_order', ROLE, 'summary') === TRUE ) {
		if(empty($_GET['tab'])) {
			$_GET['tab'] = 'summary';
		} ?>
		<li class="<?= ($_GET['tab'] == 'summary' ? 'active blue' : '') ?>"><a href="?tab=summary">Summary</a></li>
	<?php }
    if(in_array('create',$po_tabs) && check_subtab_persmission($dbc, 'purchase_order', ROLE, 'create') === TRUE ) { ?>
		<li class="<?= ($_GET['tab'] == 'create' ? 'active blue' : '') ?>"><a href="?tab=create">Create an Order</a></li>
	<?php }
	if (in_array('pending',$po_tabs) && check_subtab_persmission($dbc, 'purchase_order', ROLE, 'pending') === TRUE ) {
		if(!in_array_any(['project','business','ticket','site','vendor'],$po_tabs)) { ?>
			<a href="?tab=pending"><li class="<?= $_GET['tab'] == 'pending' ? 'active blue' : '' ?>">Pending Orders</li></a>
		<?php } else { ?>
			<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'pending' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'pending' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#pending_tabs">Pending Orders<span class="arrow" /></a>
				<ul class="collapse <?= $_GET['tab'] == 'pending' ? 'in' : '' ?>" id="pending_tabs">
					<?php if(in_array('project',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'pending' && $_GET['subtab'] == 'project' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'pending' && $_GET['subtab'] == 'project' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#pending_project"><?= PROJECT_TILE ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'pending' && $_GET['subtab'] == 'project' ? 'in' : '' ?>" id="pending_project">
								<?php $project_list = $dbc->query("SELECT `p`.* FROM `purchase_orders` `po` LEFT JOIN `project` `p` ON `po`.`projectid`=`p`.`projectid` WHERE `po`.`deleted`=0 AND `p`.`deleted`=0 AND `po`.`status`='Pending'");
								if($project_list->num_rows) {
									while($project = $project_list->fetch_assoc()) { ?>
										<li><a href="?tab=pending&subtab=project&projectid=<?= $project['projectid'] ?>"><?= get_project_label($dbc,$project) ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= PROJECT_TILE ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('ticket',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'pending' && $_GET['subtab'] == 'ticket' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'pending' && $_GET['subtab'] == 'ticket' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#pending_ticket"><?= TICKET_TILE ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'pending' && $_GET['subtab'] == 'ticket' ? 'in' : '' ?>" id="pending_ticket">
								<?php $ticket_list = $dbc->query("SELECT `t`.`ticket_type` FROM `purchase_orders` `po` LEFT JOIN `tickets` `t` ON `po`.`ticketid`=`t`.`ticketid` WHERE `po`.`deleted`=0 AND `t`.`deleted`=0 AND `po`.`status`='Pending' GROUP BY `t`.`ticket_type`");
								if($ticket_list->num_rows) {
									while($ticket = $ticket_list->fetch_assoc()) { ?>
										<li><a href="?tab=pending&subtab=ticket&ticket=<?= $ticket['ticket_type'] ?>"><?= $ticket_tabs[$ticket['ticket_type']] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= TICKET_TILE ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('business',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'pending' && $_GET['subtab'] == 'business' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'pending' && $_GET['subtab'] == 'business' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#pending_business"><?= BUSINESS_CAT ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'pending' && $_GET['subtab'] == 'business' ? 'in' : '' ?>" id="pending_business">
								<?php $business_list = sort_contacts_query($dbc->query("SELECT `c`.`contactid`, `c`.`first_name`, `c`.`last_name`, `c`.`name` FROM `purchase_orders` `po` LEFT JOIN `contacts` `c` ON `po`.`businessid`=`c`.`contactid` WHERE `po`.`deleted`=0 AND `c`.`deleted`=0 AND `c`.`status` > 0 AND `po`.`status`='Pending'"));
								if(count($business_list) > 0) {
									foreach($business_list as $business) { ?>
										<li><a href="?tab=pending&subtab=business&businessid=<?= $business['contactid'] ?>"><?= $business['full_name'] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= BUSINESS_CAT ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('site',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'pending' && $_GET['subtab'] == 'site' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'pending' && $_GET['subtab'] == 'site' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#pending_site"><?= SITES_CAT ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'pending' && $_GET['subtab'] == 'site' ? 'in' : '' ?>" id="pending_site">
								<?php $site_list = sort_contacts_query($dbc->query("SELECT `c`.`contactid`, `c`.`first_name`, `c`.`last_name`, `c`.`name` FROM `purchase_orders` `po` LEFT JOIN `contacts` `c` ON `po`.`siteid`=`c`.`contactid` WHERE `po`.`deleted`=0 AND `c`.`deleted`=0 AND `c`.`status` > 0 AND `po`.`status`='Pending'"));
								if(count($site_list) > 0) {
									foreach($site_list as $site) { ?>
										<li><a href="?tab=pending&subtab=site&siteid=<?= $site['contactid'] ?>"><?= $site['full_name'] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= SITES_CAT ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('vendor',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'pending' && $_GET['subtab'] == 'vendor' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'pending' && $_GET['subtab'] == 'vendor' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#pending_vendor"><?= VENDOR_TILE ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'pending' && $_GET['subtab'] == 'vendor' ? 'in' : '' ?>" id="pending_vendor">
								<?php $vendor_list = sort_contacts_query($dbc->query("SELECT `c`.`contactid`, `c`.`first_name`, `c`.`last_name`, `c`.`name` FROM `purchase_orders` `po` LEFT JOIN `contacts` `c` ON `po`.`contactid`=`c`.`contactid` WHERE `po`.`deleted`=0 AND `c`.`deleted`=0 AND `c`.`status` > 0 AND `po`.`status`='Pending'"));
								if(count($vendor_list) > 0) {
									foreach($vendor_list as $vendor) { ?>
										<li><a href="?tab=pending&subtab=vendor&vendorid=<?= $vendor['contactid'] ?>"><?= $vendor['full_name'] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= VENDOR_TILE ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
				</ul>
			</li>
		<?php }
	}
	if (in_array('receiving',$po_tabs) && check_subtab_persmission($dbc, 'purchase_order', ROLE, 'receiving') === TRUE ) {
		if(!in_array_any(['project','business','ticket','site','vendor'],$po_tabs)) { ?>
			<a href="?tab=receiving"><li class="<?= $_GET['tab'] == 'receiving' ? 'active blue' : '' ?>">Receiving</li></a>
		<?php } else { ?>
			<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'receiving' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'receiving' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#receiving_tabs">Receiving<span class="arrow" /></a>
				<ul class="collapse <?= $_GET['tab'] == 'receiving' ? 'in' : '' ?>" id="receiving_tabs">
					<?php if(in_array('project',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'receiving' && $_GET['subtab'] == 'project' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'receiving' && $_GET['subtab'] == 'project' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#receiving_project"><?= PROJECT_TILE ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'receiving' && $_GET['subtab'] == 'project' ? 'in' : '' ?>" id="receiving_project">
								<?php $project_list = $dbc->query("SELECT `p`.* FROM `purchase_orders` `po` LEFT JOIN `project` `p` ON `po`.`projectid`=`p`.`projectid` WHERE `po`.`deleted`=0 AND `p`.`deleted`=0 AND `po`.`status`='Receiving'");
								if($project_list->num_rows) {
									while($project = $project_list->fetch_assoc()) { ?>
										<li><a href="?tab=receiving&subtab=project&projectid=<?= $project['projectid'] ?>"><?= get_project_label($dbc,$project) ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= PROJECT_TILE ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('ticket',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'receiving' && $_GET['subtab'] == 'ticket' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'receiving' && $_GET['subtab'] == 'ticket' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#receiving_ticket"><?= TICKET_TILE ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'receiving' && $_GET['subtab'] == 'ticket' ? 'in' : '' ?>" id="receiving_ticket">
								<?php $ticket_list = $dbc->query("SELECT `t`.`ticket_type` FROM `purchase_orders` `po` LEFT JOIN `tickets` `t` ON `po`.`ticketid`=`t`.`ticketid` WHERE `po`.`deleted`=0 AND `t`.`deleted`=0 AND `po`.`status`='Receiving' GROUP BY `t`.`ticket_type`");
								if($ticket_list->num_rows) {
									while($ticket = $ticket_list->fetch_assoc()) { ?>
										<li><a href="?tab=receiving&subtab=ticket&ticket=<?= $ticket['ticket_type'] ?>"><?= $ticket_tabs[$ticket['ticket_type']] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= TICKET_TILE ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('business',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'receiving' && $_GET['subtab'] == 'business' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'receiving' && $_GET['subtab'] == 'business' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#receiving_business"><?= BUSINESS_CAT ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'receiving' && $_GET['subtab'] == 'business' ? 'in' : '' ?>" id="receiving_business">
								<?php $business_list = sort_contacts_query($dbc->query("SELECT `c`.`contactid`, `c`.`first_name`, `c`.`last_name`, `c`.`name` FROM `purchase_orders` `po` LEFT JOIN `contacts` `c` ON `po`.`businessid`=`c`.`contactid` WHERE `po`.`deleted`=0 AND `c`.`deleted`=0 AND `c`.`status` > 0 AND `po`.`status`='Receiving'"));
								if(count($business_list) > 0) {
									foreach($business_list as $business) { ?>
										<li><a href="?tab=receiving&subtab=business&businessid=<?= $business['contactid'] ?>"><?= $business['full_name'] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= BUSINESS_CAT ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('site',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'receiving' && $_GET['subtab'] == 'site' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'receiving' && $_GET['subtab'] == 'site' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#receiving_site"><?= SITES_CAT ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'receiving' && $_GET['subtab'] == 'site' ? 'in' : '' ?>" id="receiving_site">
								<?php $site_list = sort_contacts_query($dbc->query("SELECT `c`.`contactid`, `c`.`first_name`, `c`.`last_name`, `c`.`name` FROM `purchase_orders` `po` LEFT JOIN `contacts` `c` ON `po`.`siteid`=`c`.`contactid` WHERE `po`.`deleted`=0 AND `c`.`deleted`=0 AND `c`.`status` > 0 AND `po`.`status`='Receiving'"));
								if(count($site_list) > 0) {
									foreach($site_list as $site) { ?>
										<li><a href="?tab=receiving&subtab=site&siteid=<?= $site['contactid'] ?>"><?= $site['full_name'] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= SITES_CAT ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('vendor',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'receiving' && $_GET['subtab'] == 'vendor' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'receiving' && $_GET['subtab'] == 'vendor' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#receiving_vendor"><?= VENDOR_TILE ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'receiving' && $_GET['subtab'] == 'vendor' ? 'in' : '' ?>" id="receiving_vendor">
								<?php $vendor_list = sort_contacts_query($dbc->query("SELECT `c`.`contactid`, `c`.`first_name`, `c`.`last_name`, `c`.`name` FROM `purchase_orders` `po` LEFT JOIN `contacts` `c` ON `po`.`contactid`=`c`.`contactid` WHERE `po`.`deleted`=0 AND `c`.`deleted`=0 AND `c`.`status` > 0 AND `po`.`status`='Receiving'"));
								if(count($vendor_list) > 0) {
									foreach($vendor_list as $vendor) { ?>
										<li><a href="?tab=receiving&subtab=vendor&vendorid=<?= $vendor['contactid'] ?>"><?= $vendor['full_name'] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= VENDOR_TILE ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
				</ul>
			</li>
		<?php }
	}
	if (in_array('payable',$po_tabs) && check_subtab_persmission($dbc, 'purchase_order', ROLE, 'payable') === TRUE ) {
		if(!in_array_any(['project','business','ticket','site','vendor'],$po_tabs)) { ?>
			<a href="?tab=payable"><li class="<?= $_GET['tab'] == 'payable' ? 'active blue' : '' ?>">Accounts Payable</li></a>
		<?php } else { ?>
			<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'payable' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'payable' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#payable_tabs">Accounts Payable<span class="arrow" /></a>
				<ul class="collapse <?= $_GET['tab'] == 'payable' ? 'in' : '' ?>" id="payable_tabs">
					<?php if(in_array('project',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'payable' && $_GET['subtab'] == 'project' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'payable' && $_GET['subtab'] == 'project' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#payable_project"><?= PROJECT_TILE ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'payable' && $_GET['subtab'] == 'project' ? 'in' : '' ?>" id="payable_project">
								<?php $project_list = $dbc->query("SELECT `p`.* FROM `purchase_orders` `po` LEFT JOIN `project` `p` ON `po`.`projectid`=`p`.`projectid` WHERE `po`.`deleted`=0 AND `p`.`deleted`=0 AND `po`.`status`='Paying'");
								if($project_list->num_rows) {
									while($project = $project_list->fetch_assoc()) { ?>
										<li><a href="?tab=payable&subtab=project&projectid=<?= $project['projectid'] ?>"><?= get_project_label($dbc,$project) ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= PROJECT_TILE ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('ticket',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'payable' && $_GET['subtab'] == 'ticket' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'payable' && $_GET['subtab'] == 'ticket' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#payable_ticket"><?= TICKET_TILE ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'payable' && $_GET['subtab'] == 'ticket' ? 'in' : '' ?>" id="payable_ticket">
								<?php $ticket_list = $dbc->query("SELECT `t`.`ticket_type` FROM `purchase_orders` `po` LEFT JOIN `tickets` `t` ON `po`.`ticketid`=`t`.`ticketid` WHERE `po`.`deleted`=0 AND `t`.`deleted`=0 AND `po`.`status`='Paying' GROUP BY `t`.`ticket_type`");
								if($ticket_list->num_rows) {
									while($ticket = $ticket_list->fetch_assoc()) { ?>
										<li><a href="?tab=payable&subtab=ticket&ticket=<?= $ticket['ticket_type'] ?>"><?= $ticket_tabs[$ticket['ticket_type']] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= TICKET_TILE ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('business',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'payable' && $_GET['subtab'] == 'business' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'payable' && $_GET['subtab'] == 'business' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#payable_business"><?= BUSINESS_CAT ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'payable' && $_GET['subtab'] == 'business' ? 'in' : '' ?>" id="payable_business">
								<?php $business_list = sort_contacts_query($dbc->query("SELECT `c`.`contactid`, `c`.`first_name`, `c`.`last_name`, `c`.`name` FROM `purchase_orders` `po` LEFT JOIN `contacts` `c` ON `po`.`businessid`=`c`.`contactid` WHERE `po`.`deleted`=0 AND `c`.`deleted`=0 AND `c`.`status` > 0 AND `po`.`status`='Paying'"));
								if(count($business_list) > 0) {
									foreach($business_list as $business) { ?>
										<li><a href="?tab=payable&subtab=business&businessid=<?= $business['contactid'] ?>"><?= $business['full_name'] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= BUSINESS_CAT ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('site',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'payable' && $_GET['subtab'] == 'site' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'payable' && $_GET['subtab'] == 'site' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#payable_site"><?= SITES_CAT ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'payable' && $_GET['subtab'] == 'site' ? 'in' : '' ?>" id="payable_site">
								<?php $site_list = sort_contacts_query($dbc->query("SELECT `c`.`contactid`, `c`.`first_name`, `c`.`last_name`, `c`.`name` FROM `purchase_orders` `po` LEFT JOIN `contacts` `c` ON `po`.`siteid`=`c`.`contactid` WHERE `po`.`deleted`=0 AND `c`.`deleted`=0 AND `c`.`status` > 0 AND `po`.`status`='Paying'"));
								if(count($site_list) > 0) {
									foreach($site_list as $site) { ?>
										<li><a href="?tab=payable&subtab=site&siteid=<?= $site['contactid'] ?>"><?= $site['full_name'] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= SITES_CAT ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('vendor',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'payable' && $_GET['subtab'] == 'vendor' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'payable' && $_GET['subtab'] == 'vendor' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#payable_vendor"><?= VENDOR_TILE ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'payable' && $_GET['subtab'] == 'vendor' ? 'in' : '' ?>" id="payable_vendor">
								<?php $vendor_list = sort_contacts_query($dbc->query("SELECT `c`.`contactid`, `c`.`first_name`, `c`.`last_name`, `c`.`name` FROM `purchase_orders` `po` LEFT JOIN `contacts` `c` ON `po`.`contactid`=`c`.`contactid` WHERE `po`.`deleted`=0 AND `c`.`deleted`=0 AND `c`.`status` > 0 AND `po`.`status`='Paying'"));
								if(count($vendor_list) > 0) {
									foreach($vendor_list as $vendor) { ?>
										<li><a href="?tab=payable&subtab=vendor&vendorid=<?= $vendor['contactid'] ?>"><?= $vendor['full_name'] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= VENDOR_TILE ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
				</ul>
			</li>
		<?php }
	}

	// Pending cross-software P.O.'
	if(in_array('remote',$po_tabs) && vuaed_visible_function($dbc, 'purchase_order') == 1 && check_subtab_persmission($dbc, 'purchase_order', ROLE, 'remote') === TRUE) {
		$num_of_rows = 0;
		$pending_rows = 0;
		// **** NOTE: THE $number_of_connections variable is set only in the database_connection.php file. You must put this variable in manually for this to work. Please see one of SEA's database_connection.php files in order to see how these variables are set up. If you are trying to copy this cross-software functionality, it is advised that you use the exact same format/variable names that SEA's database_connection.php file contains.
		if(isset($number_of_connections) && $number_of_connections > 0) {
			foreach (range(1, $number_of_connections) as $i) {
				$dbc_cross = ${'dbc_cross_'.$i};
				$check_po_query = "SELECT * FROM purchase_orders WHERE cross_software != '' AND cross_software IS NOT NULL AND software_seller = 'main' AND deleted = 0";
				$resulx = mysqli_query($dbc_cross, $check_po_query) or die(mysqli_error($dbc_cross));
				$num_rowss = mysqli_num_rows($resulx);
				if($num_rowss > 0) {
					$num_of_rows = $num_of_rows+$num_rowss;
				}
				 while($rowie = mysqli_fetch_array( $resulx )) {
					 if($rowie['cross_software_approval'] == '' || $rowie['cross_software_approval'] == NULL) {
						 $pending_rows++;
					 }
				 }
			}
			if($num_of_rows > 0) {
				if($pending_rows > 0) {
					$pending_alert = "(".$pending_rows." Pending Approval)";
				} else {
					$pending_alert = "";
				} ?>
				<a href="?tab=remote"><li class="<?= $_GET['tab'] == 'remote' ? 'active blue' : '' ?>">
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="These are the uncompleted Purchase Orders created by Remote Software."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>Remote Purchase Orders <?= $pending_alert ?></li></a>
			<?php }
		}
	}

	if (in_array('completed',$po_tabs) && check_subtab_persmission($dbc, 'purchase_order', ROLE, 'completed') === TRUE ) {
		if(empty($_GET['tab'])) {
			$_GET['tab'] = 'completed';
		}
		if(!in_array_any(['project','business','ticket','site','vendor'],$po_tabs)) { ?>
			<a href="?tab=completed"><li class="<?= $_GET['tab'] == 'completed' || empty($_GET['tab']) ? 'active blue' : '' ?>">Completed Purchase Orders</li></a>
		<?php } else { ?>
			<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'completed' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'completed' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#completed_tabs">Completed Purchase Orders<span class="arrow" /></a>
				<ul class="collapse <?= $_GET['tab'] == 'completed' ? 'in' : '' ?>" id="completed_tabs">
					<?php if(in_array('project',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'completed' && $_GET['subtab'] == 'project' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'completed' && $_GET['subtab'] == 'project' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#completed_project"><?= PROJECT_TILE ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'completed' && $_GET['subtab'] == 'project' ? 'in' : '' ?>" id="completed_project">
								<?php $project_list = $dbc->query("SELECT `p`.* FROM `purchase_orders` `po` LEFT JOIN `project` `p` ON `po`.`projectid`=`p`.`projectid` WHERE `po`.`deleted`=0 AND `p`.`deleted`=0 AND `po`.`status`='Completed'");
								if($project_list->num_rows) {
									while($project = $project_list->fetch_assoc()) { ?>
										<li><a href="?tab=completed&subtab=project&projectid=<?= $project['projectid'] ?>"><?= get_project_label($dbc,$project) ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= PROJECT_TILE ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('ticket',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'completed' && $_GET['subtab'] == 'ticket' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'completed' && $_GET['subtab'] == 'ticket' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#completed_ticket"><?= TICKET_TILE ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'completed' && $_GET['subtab'] == 'ticket' ? 'in' : '' ?>" id="completed_ticket">
								<?php $ticket_list = $dbc->query("SELECT `t`.`ticket_type` FROM `purchase_orders` `po` LEFT JOIN `tickets` `t` ON `po`.`ticketid`=`t`.`ticketid` WHERE `po`.`deleted`=0 AND `t`.`deleted`=0 AND `po`.`status`='Completed' GROUP BY `t`.`ticket_type`");
								if($ticket_list->num_rows) {
									while($ticket = $ticket_list->fetch_assoc()) { ?>
										<li><a href="?tab=completed&subtab=ticket&ticket=<?= $ticket['ticket_type'] ?>"><?= $ticket_tabs[$ticket['ticket_type']] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= TICKET_TILE ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('business',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'completed' && $_GET['subtab'] == 'business' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'completed' && $_GET['subtab'] == 'business' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#completed_business"><?= BUSINESS_CAT ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'completed' && $_GET['subtab'] == 'business' ? 'in' : '' ?>" id="completed_business">
								<?php $business_list = sort_contacts_query($dbc->query("SELECT `c`.`contactid`, `c`.`first_name`, `c`.`last_name`, `c`.`name` FROM `purchase_orders` `po` LEFT JOIN `contacts` `c` ON `po`.`businessid`=`c`.`contactid` WHERE `po`.`deleted`=0 AND `c`.`deleted`=0 AND `c`.`status` > 0 AND `po`.`status`='Completed'"));
								if(count($business_list) > 0) {
									foreach($business_list as $business) { ?>
										<li><a href="?tab=completed&subtab=business&businessid=<?= $business['contactid'] ?>"><?= $business['full_name'] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= BUSINESS_CAT ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('site',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'completed' && $_GET['subtab'] == 'site' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'completed' && $_GET['subtab'] == 'site' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#completed_site"><?= SITES_CAT ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'completed' && $_GET['subtab'] == 'site' ? 'in' : '' ?>" id="completed_site">
								<?php $site_list = sort_contacts_query($dbc->query("SELECT `c`.`contactid`, `c`.`first_name`, `c`.`last_name`, `c`.`name` FROM `purchase_orders` `po` LEFT JOIN `contacts` `c` ON `po`.`siteid`=`c`.`contactid` WHERE `po`.`deleted`=0 AND `c`.`deleted`=0 AND `c`.`status` > 0 AND `po`.`status`='Completed'"));
								if(count($site_list) > 0) {
									foreach($site_list as $site) { ?>
										<li><a href="?tab=completed&subtab=site&siteid=<?= $site['contactid'] ?>"><?= $site['full_name'] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= SITES_CAT ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
					<?php if(in_array('vendor',$po_tabs)) { ?>
						<li class="sidebar-higher-level highest-level"><a class="<?= ($_GET['tab'] == 'completed' && $_GET['subtab'] == 'vendor' ? 'active blue' : '') ?> cursor-hand <?= ($_GET['tab'] == 'completed' && $_GET['subtab'] == 'vendor' ? '' : 'collapsed') ?>" data-toggle="collapse" data-target="#completed_vendor"><?= VENDOR_TILE ?><span class="arrow" /></a>
							<ul class="collapse <?= $_GET['tab'] == 'completed' && $_GET['subtab'] == 'vendor' ? 'in' : '' ?>" id="completed_vendor">
								<?php $vendor_list = sort_contacts_query($dbc->query("SELECT `c`.`contactid`, `c`.`first_name`, `c`.`last_name`, `c`.`name` FROM `purchase_orders` `po` LEFT JOIN `contacts` `c` ON `po`.`contactid`=`c`.`contactid` WHERE `po`.`deleted`=0 AND `c`.`deleted`=0 AND `c`.`status` > 0 AND `po`.`status`='Completed'"));
								if(count($vendor_list) > 0) {
									foreach($vendor_list as $vendor) { ?>
										<li><a href="?tab=completed&subtab=vendor&vendorid=<?= $vendor['contactid'] ?>"><?= $vendor['full_name'] ?></a></li>
									<?php }
								} else { ?>
									<li>No <?= VENDOR_TILE ?> Found</li>
								<?php } ?>
							</ul>
						</li>
					<?php } ?>
				</ul>
			</li>
		<?php }
	}
	if (in_array('cust_po',$po_tabs) && check_subtab_persmission($dbc, 'purchase_order', ROLE, 'cust_po') === TRUE ) {
		if(empty($_GET['tab'])) {
			$_GET['tab'] = 'cust_po';
		}
		$po_numbers = $dbc->query("SELECT `detail` FROM `contact_order_numbers` WHERE `category`='po_number' AND `deleted`=0 AND `contactid` > 0 UNION SELECT `po_num` FROM `ticket_attached` WHERE `deleted`=0 AND `po_num` != '' AND `src_table`='inventory' GROUP BY LPAD(`po_num`,100,0)");
		if($po_numbers->num_rows > 0) { ?>
			<li class="sidebar-higher-level highest-level"><a data-toggle="collapse" data-target="#po_numbers" class="cursor-hand <?= $_GET['tab'] == 'cust_po' ? 'active blue' : 'collapsed' ?>">Purchase Order #<span class="arrow" /></a>
				<ul class="collapse <?= $_GET['tab'] == 'cust_po' ? 'in' : '' ?>" id="po_numbers">
					<a href="?tab=cust_po"><li class="<?= empty($_GET['po']) && $_GET['tab'] == 'cust_po' ? 'active blue' : '' ?>">PO #s</li></a>
					<?php while($po_num = $po_numbers->fetch_array()) {
						$po_num = $po_num[0]; ?>
						<a href="?tab=cust_po&po=<?= config_safe_str($po_num) ?>"><li class="<?= $_GET['po'] == config_safe_str($po_num) ? 'active blue' : '' ?>"><?= $po_num ?></li></a>
					<?php } ?>
				</ul>
			</li>
		<?php } else { ?>
			<a href="?tab=cust_po"><li class="<?= $_GET['tab'] == 'cust_po' ? 'active blue' : '' ?>">Purchase Order #</li></a>
		<?php }
	}
	switch($_GET['tab']) {
		case 'summary':
			$page_title = 'Summary';
			break;
		case 'create':
			$page_title = 'Create an Order';
			break;
		case 'pending':
			$page_title = 'Pending Orders';
			break;
		case 'receiving':
			$page_title = 'Receiving';
			break;
		case 'payable':
			$page_title = 'Accounts Payable';
			break;
		case 'remote':
			$page_title = 'Remote Purchase Orders';
			break;
		case 'cust_po':
			$page_title = 'Purchase Order #';
			break;
		default:
			$page_title = 'Completed Purchase Orders';
			break;
	}
}