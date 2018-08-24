<?php include_once('../include.php');
checkAuthorised('calendar_rook');
include_once('../Calendar/calendar_settings_inc.php');
ob_clean();
if($_GET['type'] == 'uni') {
	$wait_list = get_config($dbc, 'uni_wait_list');
	$wait_list = explode(',', $wait_list)[0];
	if(!empty($_GET['booking_type'])) {
		$wait_list = $_GET['booking_type'];
	}
} else if($_GET['type'] == 'ticket') {
	$wait_list = get_config($dbc, 'ticket_wait_list');
} else if($_GET['type'] == 'schedule') {
	$wait_list = get_config($dbc, 'scheduling_wait_list');
} else if($_GET['type'] == 'staff') {
	$wait_list = get_config($dbc, 'staff_schedule_wait_list');
} else {
	$wait_list = get_config($dbc, 'appt_wait_list');
}
$load_all = $_GET['load_all'];
if($wait_list == 'ticket' || $wait_list == 'ticket_multi') { ?>
	<script>
	var ticket_list = [];
	var continue_loading = '';
	$(document).ready(function() {
		destroyInputs('.calendar-screen .unbooked_view');
		var active_region = $('#collapse_region .block-item.active').first().data('region');
		var active_location = $('#collapse_locations .block-item.active').first().data('location');
		var active_classification = $('#collapse_classifications .block-item.active').first().data('classification');
		$('[name="filter_unbooked_region"]').val(active_region);
		$('[name="filter_unbooked_location"]').val(active_location);
		$('[name="filter_unbooked_classification"]').val(active_classification);
		retrieveUnbookedData();
	});
	function retrieveUnbookedData() {
		$('#unbooked_list').html('Loading...');
		$.ajax({
			url: '../Calendar/unbooked_load_list.php?<?= http_build_query($_GET); ?>&wait_list=<?= $wait_list ?>',
			method: 'POST',
			success: function(response) {
				ticket_list = JSON.parse(response);
				loadFilterCount();
				filterTickets();
				initInputs('.calendar-screen .unbooked_view');
			}
		});
	}
	function filterProjects() {
		var projecttype = $('[name="filter_unbooked_projecttype"]').val();
		$('[name="filter_unbooked_project"] option').hide();
		$('[name="filter_unbooked_project"] option[data-projecttype="'+projecttype+'"]').show();
		$('[name="filter_unbooked_project"]').val('');
		$('[name="filter_unbooked_project"]').trigger('change.select2');
	}
	function filterTickets() {
		clearTimeout(continue_loading);
		$('#unbooked_list').html('Loading...');
		if($('[name=filter_unbooked_region]').val() != '' && $('[name=filter_unbooked_region]').val() != undefined) {
			$('[name=filter_unbooked_classification] option').each(function() {
				var class_regions = $(this).data('regions');
				if(class_regions != undefined && class_regions.length > 0) {
					var classification = this;
					$(classification).hide();
					class_regions.forEach(function(class_region) {
						if(class_region == $('[name=filter_unbooked_region]').val()) {
							$(classification).show();
						}
					});	
				}
			});
			$('[name=filter_unbooked_classification]').trigger('change.select2');

			$('[name=filter_unbooked_cust] option').each(function() {
				var cust_region = $(this).data('region');
				$(this).show();
				if(cust_region != '' && cust_region != undefined && cust_region != $('[name=filter_unbooked_region]').val()) {
					$(this).hide();
				}
			});
			$('[name=filter_unbooked_cust]').trigger('change.select2');
		}

		projecttype_filter = $('[name=filter_unbooked_projecttype').val();
		project_filter = $('[name=filter_unbooked_project]').val();
		region_filter = $('[name=filter_unbooked_region]').val();
		location_filter = $('[name=filter_unbooked_location]').val();
		classification_filter = $('[name=filter_unbooked_classification]').val();
		cust_filter = $('[name=filter_unbooked_cust]').val();
		client_filter = $('[name=filter_unbooked_client]').val();
		staff_filter = $('[name=filter_unbooked_staff]').val();
		status_filter = $('[name=filter_unbooked_status]').val();
		from_date_filter = $('[name=filter_from_date]').val();
		to_date_filter = $('[name=filter_to_date]').val();
		search_filter = $('[name=filter_unbooked_searchbox]').val();

		var result_list = [];
		ticket_list['tickets'].forEach(function(ticket) {
			var ticket_pass = true;
			ticket.region = ticket.region == null ? '' : ticket.region;
			ticket.location = ticket.location == null ? '' : ticket.location;
			ticket.classification = ticket.classification == null ? '' : ticket.classification;
			ticket.client = ticket.client == null ? '' : ticket.client;
			if(projecttype_filter != '' && projecttype_filter != undefined && ticket.projecttype != projecttype_filter) {
				ticket_pass = false;
			} else if(project_filter != '' && project_filter != undefined && ticket.project != project_filter) {
				ticket_pass = false;
			} else if(region_filter != '' && region_filter != undefined && ticket.region.indexOf(region_filter) == -1) {
				ticket_pass = false;
			} else if(location_filter != '' && location_filter != undefined && ticket.location.indexOf(location_filter) == -1) {
				ticket_pass = false;
			} else if(classification_filter != '' && classification_filter != undefined && ticket.classification.indexOf(classification_filter) == -1) {
				ticket_pass = false;
			} else if(cust_filter != '' && cust_filter != undefined && ticket.cust != cust_filter) {
				ticket_pass = false;
			} else if(client_filter != '' && client_filter != undefined && ticket.client.indexOf(client_filter) == -1) {
				ticket_pass = false;
			} else if(staff_filter != '' && staff_filter != undefined && (','+ticket.staff+',').indexOf(','+staff_filter+',') == -1) {
				ticket_pass = false;
			} else if(status_filter != '' && status_filter != undefined && ticket.status != status_filter) {
				ticket_pass = false;
			} else if(search_filter != '' && search_filter != undefined) {
				var search_string = search_filter.toLowerCase();
				if(ticket.text.toString().toLowerCase().indexOf(search_string) == -1 && ticket.staffnames.toString().toLowerCase().indexOf(search_string) == -1 && ticket.id.toString().toLowerCase().indexOf(search_string) == -1) {
					ticket_pass = false;
				}
			}
			if(Date.parse(from_date_filter) && Date.parse(to_date_filter)) {
				if(ticket.startdate != undefined && ticket.startdate != '' && ticket.startdate != '0000-00-00' && ((new Date(ticket.startdate).getTime() < new Date(from_date_filter).getTime()) || (new Date(ticket.startdate).getTime() > new Date(to_date_filter).getTime()))) {
				 	ticket_pass = false;
				}
			} else if(Date.parse(from_date_filter)) {
				if(ticket.startdate != undefined && ticket.startdate != '' && ticket.startdate != '0000-00-00' && (new Date(ticket.startdate).getTime() < new Date(from_date_filter).getTime())) {
				 	ticket_pass = false;
				}
			} else if(Date.parse(to_date_filter)) {
				if(ticket.startdate != undefined && ticket.startdate != '' && ticket.startdate != '0000-00-00' && (new Date(ticket.startdate).getTime() > new Date(to_date_filter).getTime())) {
				 	ticket_pass = false;
				}
			}
			if(ticket_pass) {
				result_list.push([ticket.id_value,ticket.id_field]);
			}
		});
		showResults(result_list);
	}
	function loadFilterCount() {
		ticket_list['filter_count'].project_filters.forEach(function(filter) {
			var projectid = filter.id;
			var count = filter.count;
			if(projectid > 0) {
				var option = $('[name="filter_unbooked_project"] option').filter(function() { return $(this).val() == projectid });
				option_text = option.text();
				option_text = option_text.substr(0, option_text.lastIndexOf('('))+'('+count+')';
				option.text(option_text);
			}
		});
		$('[name="filter_unbooked_projecttype"]').trigger('change.select2');
		ticket_list['filter_count'].projecttype_filters.forEach(function(filter) {
			var projecttype = filter.id;
			var count = filter.count;
			if(projecttype != '') {
				var option = $('[name="filter_unbooked_projecttype"] option').filter(function() { return $(this).val() == projecttype });
				option_text = option.text();
				option_text = option_text.substr(0, option_text.lastIndexOf('('))+'('+count+')';
				option.text(option_text);
			}
		});
		$('[name="filter_unbooked_project"]').trigger('change.select2');
		ticket_list['filter_count'].region_filters.forEach(function(filter) {
			var region = filter.id;
			var count = filter.count;
			if(region != '') {
				var option = $('[name="filter_unbooked_region"] option').filter(function() { return $(this).val() == region });
				option_text = option.text();
				option_text = option_text.substr(0, option_text.lastIndexOf('('))+'('+count+')';
				option.text(option_text);
			}
		});
		$('[name="filter_unbooked_region"]').trigger('change.select2');
		ticket_list['filter_count'].location_filters.forEach(function(filter) {
			var location = filter.id;
			var count = filter.count;
			if(location != '') {
				var option = $('[name="filter_unbooked_location"] option').filter(function() { return $(this).val() == location });
				option_text = option.text();
				option_text = option_text.substr(0, option_text.lastIndexOf('('))+'('+count+')';
				option.text(option_text);
			}
		});
		$('[name="filter_unbooked_location"]').trigger('change.select2');
		ticket_list['filter_count'].classification_filters.forEach(function(filter) {
			var classification = filter.id;
			var count = filter.count;
			if(classification != '') {
				var option = $('[name="filter_unbooked_classification"] option').filter(function() { return $(this).val() == classification });
				option_text = option.text();
				option_text = option_text.substr(0, option_text.lastIndexOf('('))+'('+count+')';
				option.text(option_text);
			}
		});
		$('[name="filter_unbooked_classification"]').trigger('change.select2');
		ticket_list['filter_count'].cust_filters.forEach(function(filter) {
			var businessid = filter.id;
			var count = filter.count;
			if(businessid > 0) {
				var option = $('[name="filter_unbooked_cust"] option').filter(function() { return $(this).val() == businessid });
				option_text = option.text();
				option_text = option_text.substr(0, option_text.lastIndexOf('('))+'('+count+')';
				option.text(option_text);
			}
		});
		$('[name="filter_unbooked_cust"]').trigger('change.select2');
		ticket_list['filter_count'].client_filters.forEach(function(filter) {
			var clientid = filter.id;
			var count = filter.count;
			if(clientid > 0) {
				var option = $('[name="filter_unbooked_client"] option').filter(function() { return $(this).val() == clientid });
				option_text = option.text();
				option_text = option_text.substr(0, option_text.lastIndexOf('('))+'('+count+')';
				option.text(option_text);
			}
		});
		$('[name="filter_unbooked_client"]').trigger('change.select2');
		ticket_list['filter_count'].staff_filters.forEach(function(filter) {
			var staffid = filter.id;
			var count = filter.count;
			if(staffid > 0) {
				var option = $('[name="filter_unbooked_staff"] option').filter(function() { return $(this).val() == staffid });
				option_text = option.text();
				option_text = option_text.substr(0, option_text.lastIndexOf('('))+'('+count+')';
				option.text(option_text);
			}
		});
		$('[name="filter_unbooked_staff"]').trigger('change.select2');
		ticket_list['filter_count'].status_filters.forEach(function(filter) {
			var status = filter.id;
			var count = filter.count;
			if(status != '') {
				var option = $('[name="filter_unbooked_status"] option').filter(function() { return $(this).val() == status });
				option_text = option.text();
				option_text = option_text.substr(0, option_text.lastIndexOf('('))+'('+count+')';
				option.text(option_text);
			}
		});
		$('[name="filter_unbooked_status"]').trigger('change.select2');
	}
	function showResults(result_list) {
		clearTimeout(continue_loading);
		if($('#unbooked_list').html() == 'Loading...') {
			$('#unbooked_list').html('');
		}
		if($('#unbooked_list .block-item').length == 0 || $('#unbooked_list .block-item').last().offset().top - $(window).scrollTop() < $(window).innerHeight() + 500) {
			var ticket_arr = result_list.shift();
			if(ticket_arr != undefined) {
				ticketid = ticket_arr[0];
				ticket_id_field = ticket_arr[1];
				if(ticketid > 0) {
					$.ajax({
						url: '../Calendar/unbooked_load.php?<?= http_build_query($_GET) ?>&wait_list=<?= $wait_list ?>&ticketid='+ticketid+'&id_field='+ticket_id_field,
						method: 'GET',
						dataType: 'html',
						success: function(response) {
							$('#unbooked_list').append(response);
							continue_loading = showResults(result_list);
						}
					});
				} else {
					continue_loading = showResults(result_list);
				}
			}
		} else {
			continue_loading = setTimeout(function() { showResults(result_list); }, 1000);
		}
		itemsHoverInit();
	}
	</script>
	<h3><?= $_GET['load_all'] == 1 ? 'All' : 'Unbooked' ?> <?= TICKET_TILE ?></h3>
	<div class="block-group unbooked" style="height: calc(100% - 4.5em); overflow-y: auto;">
		<?php $unbooked_filters = ','.get_config($dbc, 'unbooked_ticket_filters').',';

		$search_placeholder = [TICKET_TILE]; ?>
		<?php if(strpos($unbooked_filters, ',project,') !== FALSE) {
		$search_placeholder[] = PROJECT_TILE.' Type'; ?>
		<label class="super-label"><?= PROJECT_NOUN ?> Type:
			<select name="filter_unbooked_projecttype" data-placeholder="Select a <?= PROJECT_NOUN ?> Type" class="chosen-select-deselect unbooked_ticket_projecttype"><option></option>
				<?php $project_tabs = get_config($dbc, 'project_tabs');
				if($project_tabs == '') {
					$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
				}
				$project_tabs = explode(',',$project_tabs);
				$project_vars = [];
				foreach($project_tabs as $item) {
					$project_vars[] = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
				}
				$active_projects = array_filter(explode(',',get_user_settings()['events_calendar_projects']));
				
				foreach($project_tabs as $project_i => $project_tab) {
					if(!check_subtab_persmission($dbc, 'project', ROLE, $project_vars[$project_i])) {
						unset($project_tabs[$project_i]);
						unset($project_vars[$project_i]);
					}
				}
				foreach($project_tabs as $project_i => $project_tab) { ?>
					<option value="<?= $project_vars[$project_i] ?>"><?= $project_tabs[$project_i] ?> (<?= !empty($projecttype_filters[$project_vars[$project_i]]) ? $projecttype[$project_vars[$project_i]] : '0' ?>)</option>
				<?php } ?>
			</select></label>
		<?php } ?>
		<?php if(strpos($unbooked_filters, ',project,') !== FALSE) {
		$search_placeholder[] = PROJECT_TILE; ?>
		<label class="super-label"><?= PROJECT_NOUN ?>:
			<select name="filter_unbooked_project" data-placeholder="Select a <?= PROJECT_NOUN ?>" class="chosen-select-deselect unbooked_ticket_project"><option></option>
				<?php $projects = mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid` IN (SELECT `projectid` FROM `tickets` WHERE (IFNULL(`to_do_date`,'0000-00-00') IN ('0000-00-00','') OR `tickets`.`status` = 'To Be Scheduled')  AND `deleted`=0)");
				while($project = mysqli_fetch_array($projects)) { ?>
					<option data-projecttype="<?= $project['projecttype'] ?>" value="<?= $project['projectid'] ?>"><?= get_project_label($dbc, $project) ?> (<?= !empty($project_filters[$project['projectid']]) ? $project_filters[$project['projectid']] : '0' ?>)</option>
				<?php } ?>
			</select></label>
		<?php } ?>
		<?php if(strpos($unbooked_filters, ',region,') !== FALSE) {
		$search_placeholder[] = 'Regions'; ?>
		<label class="super-label">Region:
			<select name="filter_unbooked_region" data-placeholder="Select a Region" class="chosen-select-deselect unbooked_ticket_region"><option></option>
				<?php foreach($allowed_regions as $region_search) { ?>
					<option value="<?= $region_search ?>"><?= $region_search ?> (<?= !empty($region_filters[$region_search]) ? $region_filters[$region_search] : '0' ?>)</option>
				<?php } ?>
			</select></label>
		<?php } ?>
		<?php if(strpos($unbooked_filters, ',location,') !== FALSE) {
		$search_placeholder[] = 'Locations'; ?>
		<label class="super-label">Location:
			<select name="filter_unbooked_location" data-placeholder="Select a Location" class="chosen-select-deselect unbooked_ticket_location"><option></option>
				<?php foreach($allowed_locations as $location_search) { ?>
					<option value="<?= $location_search['con_location'] ?>"><?= $location_search ?> (<?= !empty($location_filters[$location_search]) ? $location_filters[$location_search] : '0' ?>)</option>
				<?php } ?>
			</select></label>
		<?php } ?>
		<?php if(strpos($unbooked_filters, ',classification,') !== FALSE) {
		$search_placeholder[] = 'Classifications'; ?>
		<label class="super-label">Classification:
			<select name="filter_unbooked_classification" data-placeholder="Select a Classification" class="chosen-select-deselect unbooked_ticket_classification"><option></option><?php foreach($contact_classifications as $i => $classification_search) { ?>
					<option data-regions='<?= json_encode($classification_regions[$i]) ?>' value="<?= $classification_search ?>"><?= $classification_search ?> (<?= !empty($classification_filters[$classification_search]) ? $classification_filters[$classification_search] : '0' ?>)</option>
				<?php } ?>
			</select></label>
		<?php } ?>
		<?php if(strpos($unbooked_filters, ',customer,') !== FALSE) {
		$search_placeholder[] = 'Customers'; ?>
		<label class="super-label">Customer:
			<select name="filter_unbooked_cust" data-placeholder="Select a Customer" class="chosen-select-deselect unbooked_ticket_customer"><option></option>
				<?php $customers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `contactid` IN (SELECT `businessid` FROM `tickets` WHERE (IFNULL(`to_do_date`,'0000-00-00') IN ('0000-00-00','') OR `tickets`.`status` = 'To Be Scheduled') AND `deleted`=0)"),MYSQLI_ASSOC));
				foreach($customers as $cust_id) { ?>
					<option value="<?= $cust_id ?>"><?= get_client($dbc, $cust_id) ?> (<?= !empty($cust_filters[$cust_id]) ? $cust_filters[$cust_id] : '0' ?>)</option>
				<?php } ?>
			</select></label>
		<?php } ?>
		<?php if(strpos($unbooked_filters, ',corporation,') !== FALSE || strpos($unbooked_filters, ',business,') !== FALSE) {
		$search_placeholder[] = strpos($unbooked_filters, ',corporation,') !== FALSE ? 'Corporations' : 'Business'; ?>
		<label class="super-label"><?= strpos($unbooked_filters, ',corporation,') !== FALSE ? 'Corporation' : 'Business' ?>:
			<select name="filter_unbooked_cust" data-placeholder="Select a <?= strpos($unbooked_filters, ',corporation,') !== FALSE ? 'Corporation' : 'Business' ?>" class="chosen-select-deselect unbooked_ticket_corporation"><option></option>
				<?php $customers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `contactid` IN (SELECT `businessid` FROM `tickets` WHERE (IFNULL(`to_do_date`,'0000-00-00') IN ('0000-00-00','') OR `tickets`.`status` = 'To Be Scheduled') AND `deleted`=0)"),MYSQLI_ASSOC));
				foreach($customers as $cust_id) { ?>
					<option data-region="<?= get_contact($dbc, $cust_id, 'region') ?>" value="<?= $cust_id ?>"><?= get_client($dbc, $cust_id) ?> (<?= !empty($cust_filters[$cust_id]) ? $cust_filters[$cust_id] : '0' ?>)</option>
				<?php } ?>
			</select></label>
		<?php } ?>
		<?php if(strpos($unbooked_filters, ',client,') !== FALSE) {
		$search_placeholder[] = 'Client'; ?>
		<label class="super-label">Client:
			<select name="filter_unbooked_client" data-placeholder="Select a Client" class="chosen-select-deselect unbooked_ticket_client"><option></option>
				<?php $clients = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `name`, `first_name`, `last_name` FROM `contacts` WHERE `contactid` IN (SELECT `clientid` FROM `tickets` WHERE (IFNULL(`to_do_date`,'0000-00-00') IN ('0000-00-00','') OR `tickets`.`status` = 'To Be Scheduled' OR REPLACE(IFNULL(`tickets`.`contactid`,''),',','') = '') AND `deleted`=0)"));
				foreach($clients as $client) { ?>
					<option value="<?= $client['contactid'] ?>"><?= $client['full_name'] ?> (<?= !empty($client_filters[$client['contactid']]) ? $client_filters[$client['contactid']] : '0' ?>)</option>
				<?php } ?>
			</select></label>
		<?php } ?>
		<?php if(strpos($unbooked_filters, ',staff,') !== FALSE) {
		$search_placeholder[] = 'Staff'; ?>
		<label class="super-label">Staff:
			<select name="filter_unbooked_staff" data-placeholder="Select a Staff" class="chosen-select-deselect unbooked_ticket_staff"><option></option>
				<?php $staff_list_side = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE (SELECT CONCAT(',',GROUP_CONCAT(TRIM(BOTH ',' FROM `contactid`)),',') FROM `tickets` WHERE (IFNULL(`to_do_date`,'0000-00-00') IN ('0000-00-00','') OR `tickets`.`status` = 'To Be Scheduled') AND `deleted`=0) LIKE CONCAT('%,',`contactid`,',%')"),MYSQLI_ASSOC));
				foreach($staff_list_side as $staff_id) { ?>
					<option value="<?= $staff_id ?>"><?= get_contact($dbc, $staff_id) ?> (<?= !empty($staff_filters[$staff_id]) ? $staff_filters[$staff_id] : '0' ?>)</option>
				<?php } ?>
			</select></label>
		<?php } ?>
		<?php if(strpos($unbooked_filters, ',status,') !== FALSE) {
		$search_placeholder[] = 'Status'; ?>
		<label class="super-label">Status:
			<select name="filter_unbooked_status" data-placeholder="Select a Status" class="chosen-select-deselect unbooked_ticket_status"><option></option>
				<?php $ticket_statuses = explode(',',get_config($dbc, 'ticket_status'));
				foreach($ticket_statuses as $ticket_status) { ?>
					<option value="<?= $ticket_status ?>"><?= $ticket_status ?> (<?= !empty($status_filters[$ticket_status]) ? $status_filters[$ticket_status] : '0' ?>)</option>
				<?php } ?>
			</select></label>
		<?php } ?>
		<?php if(strpos($unbooked_filters, ',date_range,') !== FALSE) {
			$search_placeholder[] = 'Date'; ?>
		<label class="super-label">From Date:
			<input type="text" name="filter_from_date" class="form-control datepicker" value="<?= date('Y-m-d') ?>" onchange="filterTickets();"></label>
		<label class="super-label">To Date:
			<input type="text" name="filter_to_date" class="form-control datepicker" value="<?= date('Y-m-d') ?>" onchange="filterTickets();"></label>
		<?php } ?>
		<?php if(strpos($unbooked_filters, ',searchbox,') !== FALSE) { ?>
		<label class="super-label">Search:
			<input placeholder="Search <?= implode(', ', $search_placeholder) ?>" type="text" name="filter_unbooked_searchbox" class="form-control" onkeyup="filterTickets();">
		</label>
		<?php } ?>
		<h4><?= TICKET_TILE ?>:</h4>
		<div id="unbooked_list">
			Loading...
		</div>
	</div>
<?php } else if($wait_list == 'workorder') { ?>
	<script>
	function filterWorkOrders(data, key) {
		$('.unbooked .block-item').show().each(function() {
			if($('[name=filter_unbooked_region]').val() != '' && $(this).data('region') != $('[name=filter_unbooked_region]').val()) {
				$(this).hide();
			}
			else if($('[name=filter_unbooked_project]').val() != '' && $(this).data('project') != $('[name=filter_unbooked_project]').val()) {
				$(this).hide();
			}
			else if($('[name=filter_unbooked_cust]').val() != '' && $(this).data('cust') != $('[name=filter_unbooked_cust]').val()) {
				$(this).hide();
			}
			else if($('[name=filter_unbooked_staff]').val() != '' && $(this).data('staff') != $('[name=filter_unbooked_staff]').val()) {
				$(this).hide();
			}
		});
	}
	</script>
	<h3>Unbooked Work Orders</h3>
	<div class="block-group unbooked" style="height: calc(100% - 4.5em); overflow-y: auto;">
		<label class="super-label">Region:
			<select name="filter_unbooked_project" data-placeholder="Select a Project" class="chosen-select-deselect unbooked_wo_project"><option></option>
				<?php $projects = mysqli_query($dbc, "SELECT `projectid`, `project_name` FROM `project` WHERE `projectid` IN (SELECT `projectid` FROM `workorder` WHERE IFNULL(`to_do_date`,'0000-00-00') IN ('0000-00-00',''))");
				while($project = mysqli_fetch_array($projects)) { ?>
					<option value="<?= $project['projectid'] ?>">Project #<?= $project['projectid'] ?>: <?= $project['project_name'] ?></option>
				<?php } ?>
			</select></label>
		<label class="super-label">Customer:
			<select name="filter_unbooked_cust" data-placeholder="Select a Customer" class="chosen-select-deselect unbooked_wo_customer"><option></option>
				<?php $customers = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `contactid` IN (SELECT `businessid` FROM `workorder` WHERE IFNULL(`to_do_date`,'0000-00-00') IN ('0000-00-00',''))"),MYSQLI_ASSOC));
				foreach($customers as $cust_id) { ?>
					<option value="<?= $cust_id ?>"><?= get_client($dbc, $cust_id) ?></option>
				<?php } ?>
			</select></label>
		<label class="super-label">Staff:
			<select name="filter_unbooked_staff" data-placeholder="Select a Staff" class="chosen-select-deselect unbooked_wo_staff"><option></option>
				<?php $assigned_staff = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid` IN (SELECT TRIM(BOTH ',' FROM `contactid`) FROM `workorder` WHERE IFNULL(`to_do_date`,'0000-00-00') IN ('0000-00-00',''))"),MYSQLI_ASSOC));
				foreach($assigned_staff as $staff_id) { ?>
					<option value="<?= $staff_id ?>"><?= get_contact($dbc, $staff_id) ?></option>
				<?php } ?>
			</select></label>
		<h4>Work Orders:</h4>
		<?php $work_list = mysqli_query($dbc, "SELECT * FROM `workorder` WHERE IFNULL(`to_do_date`,'0000-00-00') IN ('0000-00-00','')");
		while($workorder = mysqli_fetch_array($work_list)) { ?>
			<a href="" onclick='overlayIFrameSlider("<?= WEBSITE_URL ?>/Work Order/edit_workorder.php?action=view&workorderid=<?= $workorder['workorderid'] ?>"); return false;' style="text-decoration: none;"><div class="block-item active" style="border: 1px solid rgba(0,0,0,0.5); margin: 0.25em 0 0;" data-type="workorder" data-id="<?= $workorder['workorderid'] ?>" data-text="<?= $workorder['heading'] ?> " data-project="<?= $workorder['projectid'] ?>" data-cust="<?= $workorder['businessid'] ?>" data-staff="<?= $workorder['contactid'] ?>">
				<img class='drag-handle no-toggle' src='<?= WEBSITE_URL ?>/img/icons/drag_handle.png' style='filter: brightness(200%); float: right; width: 2em;' title='Drag'>
				Work Order #<?= $workorder['ticketid'].' '.$workorder['heading'] ?><br />
				Project #<?= $workorder['projectid'] ?><br />
				Customer: <?= get_client($dbc, $workorder['businessid']) ?></div></a>
		<?php } ?>
	</div>
<?php } else if($wait_list == 'appt') { ?>
	<?php if(isset($_GET['waitlistid'])) {
		unset($page_query['waitlistid']);
		if(isset($_POST['waitlist_submit'])) {
			$wait_from_date = filter_var($_POST['wait_from_date'],FILTER_SANITIZE_STRING);
			$wait_until_date = filter_var($_POST['wait_until_date'],FILTER_SANITIZE_STRING);
			$wait_from_time = filter_var($_POST['wait_from_time'],FILTER_SANITIZE_STRING);
			$wait_until_time = filter_var($_POST['wait_until_time'],FILTER_SANITIZE_STRING);
			$wait_days = filter_var(implode(',',$_POST['wait_days']),FILTER_SANITIZE_STRING);
			$wait_patientid = filter_var($_POST['wait_patientid'],FILTER_SANITIZE_STRING);
			$wait_injury = filter_var($_POST['wait_injury'],FILTER_SANITIZE_STRING);
			$wait_appt_type = filter_var($_POST['wait_appt_type'],FILTER_SANITIZE_STRING);
			$wait_notes = filter_var(htmlentities($_POST['wait_notes']),FILTER_SANITIZE_STRING);
			
			if($_GET['waitlistid'] > 0) {
				$query = "UPDATE `waitlist` SET `desired_date`='$wait_from_date', `end_wait_date`='$wait_until_date', `start_time`='$wait_from_time', `end_time`='$wait_until_time', `available_days`='$wait_days', `patientid`='$wait_patientid', `injuryid`='$wait_injury', `appt_type`='$wait_appt_type', `comment`='$wait_notes' WHERE `waitlistid`='{$_GET['waitlistid']}'";
			} else {
				$query = "INSERT INTO `waitlist` (`desired_date`, `end_wait_date`, `start_time`, `end_time`, `available_days`, `patientid`, `injuryid`, `appt_type`, `comment`, `today_date`)
					VALUES ('$wait_from_date', '$wait_until_date', '$wait_from_time', '$wait_until_time', '$wait_days', '$wait_patientid', '$wait_injury', '$wait_appt_type', '$wait_notes', '".date('Y-m-d')."')";
			}
			
			mysqli_query($dbc, $query);
			unset($page_query['waitlistid']);
			echo "<script> window.location.replace('?".http_build_query($page_query)."'); </script>";
		}
		$waitlist = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `waitlist` WHERE `waitlistid`='{$_GET['waitlistid']}'")); ?>
		<h3>Wait List</h3>
		<script>
		function filterInjuries(select) {
			var patient = select.value;
			$('[name=wait_injury] option').each(function() {
				if($(this).data('contact') == patient) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
			$('[name=wait_injury]').trigger('change.select2');
		}
		</script>
		<div class="block-group unbooked" style="height: calc(100% - 4.5em); overflow-y: auto;">
			<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
				<script>
				function setWaitlist(start_date, end_date) {
					$('[name=wait_from_date]').val(start_date);
					$('[name=wait_until_date]').val(end_date);
					$('[name=wait_from_time]').val($('tbody tr:first-child td:first-child').text());
					$('[name=wait_until_time]').val($('tbody tr:last-child td:first-child').text());
				}
				</script>
				<label for="wait_patientid" class="super-label">Patient <a href="" onclick="return false;" class="block-label pull-right">New Patient</a>
				<select data-placeholder="Select Patient" name="wait_patientid" class="chosen-select-deselect waitlist_patient">
					<option></option>
					<?php $query = "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` = 'Patient' $region_query AND `deleted` = 0 AND `status` = 1";
					$result = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, $query),MYSQLI_ASSOC));
					foreach($result as $patientid) {
						echo "<option ".($patientid == $waitlist['patientid'] ? 'selected' : '')." value='$patientid'>".get_contact($dbc, $patientid)."</option>";
					} ?>
				</select></label>
				<label for="wait_injury" class="super-label">Injury
				<select data-placeholder="Select Injury" name="wait_injury" class="chosen-select-deselect">
					<option></option>
					<?php $query = "SELECT * FROM `patient_injury` WHERE `deleted` = 0";
					$result = mysqli_query($dbc, $query);
					while($injury = mysqli_fetch_array($result)) {
						echo "<option data-contact='".$injury['contactid']."' ".($injury['injuryid'] == $waitlist['injuryid'] ? 'selected' : '')." ".($injury['patientid'] == $waitlist['patientid'] ? '' : 'style="display: none;"')." value='{$injury['injuryid']}'>".$injury['injury_type'].' - '.$injury['injury_name']."</option>";
					} ?>
				</select></label>
				<label for="wait_appt_type" class="super-label">Appointment Type
				<select data-placeholder="Select Appointment Type" name="wait_appt_type" class="chosen-select-deselect">
					<option></option>
                    <?php $appointment_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `appointment_type` WHERE `deleted` = 0"),MYSQLI_ASSOC);
                    foreach ($appointment_types as $appointment_type) {
                        echo '<option '.($waitlist['appt_type'] == $appointment_type['id'] ? 'selected' : '').' value="'.$appointment_type['id'].'">'.$appointment_type['name'].'</option>';
                    } ?>
				</select></label>
				<label class="super-label">Availability</label>
				<div>
					<button onclick="setWaitlist('<?= date('Y-m-d') ?>', '<?= date('Y-m-d', strtotime('+7days')) ?>'); return false;" class="block-join smaller">Anytime for a week</button>
					<button onclick="setWaitlist('<?= date('Y-m-d') ?>', '<?= date('Y-m-d', strtotime('+30days')) ?>'); return false;" class="block-join smaller">Anytime for a month</button>
				</div>
				<label for="wait_from_time" class="super-label">Available From
				<input type="text" name="wait_from_time" class="form-control datetimepicker" value="<?= $waitlist['start_time'] ?>"></label>
				<label for="wait_until_time" class="super-label">Available Until
				<input type="text" name="wait_until_time" class="form-control datetimepicker" value="<?= $waitlist['end_time'] ?>"></label>
				<label for="wait_from_date" class="super-label">First Available Date
				<input type="text" name="wait_from_date" class="form-control datepicker" value="<?= $waitlist['desired_date'] ?>"></label>
				<label for="wait_until_date" class="super-label">Keep on Waitlist Until
				<input type="text" name="wait_until_date" class="form-control datepicker" value="<?= $waitlist['end_wait_date'] ?>"></label>
				<label for="wait_days" class="super-label">Available Days<br />
				<label style="padding-right: 0.5em;"><input type="checkbox" name="wait_days[]" value="Sunday">Sunday</label>
				<label style="padding-right: 0.5em;"><input type="checkbox" name="wait_days[]" value="Monday">Monday</label>
				<label style="padding-right: 0.5em;"><input type="checkbox" name="wait_days[]" value="Tuesday">Tuesday</label>
				<label style="padding-right: 0.5em;"><input type="checkbox" name="wait_days[]" value="Wednesday">Wednesday</label>
				<label style="padding-right: 0.5em;"><input type="checkbox" name="wait_days[]" value="Thursday">Thursday</label>
				<label style="padding-right: 0.5em;"><input type="checkbox" name="wait_days[]" value="Friday">Friday</label>
				<label style="padding-right: 0.5em;"><input type="checkbox" name="wait_days[]" value="Saturday">Saturday</label>
				</label>
				<label for="wait_notes" class="super-label">Notes
				<textarea name="wait_notes" class="form-control noMceEditor"><?= $waitlist['comment'] ?></textarea></label>
				<button name="waitlist_submit" class="btn brand-btn pull-right">Submit</button>
				<a href="?<?= http_build_query($page_query) ?>" class="btn brand-btn pull-right">Back</a>
				<div class="clearfix"></div>
			</form>
		</div>
		<?php $page_query['waitlistid'] = $_GET['waitlistid'];
	} else {
		$page_query['waitlistid'] = 'NEW'; ?>
		<script>
		function filterWaitList(data, key) {
			$('.unbooked .block-item').show().each(function() {
				if($('[name=filter_unbooked_cust]').val() != '' && $('[name=filter_unbooked_cust]').val() != undefined && $(this).data('customer') != $('[name=filter_unbooked_cust]').val()) {
					$(this).hide();
				}
				else if($('[name=filter_unbooked_injury]').val() != '' && $('[name=filter_unbooked_injury]').val() != undefined && $(this).data('injury') != $('[name=filter_unbooked_injury]').val()) {
					$(this).hide();
				}
				else if($('[name=filter_unbooked_type]').val() != '' && $('[name=filter_unbooked_type]').val() != '' != undefined && $(this).data('type') != $('[name=filter_unbooked_type]').val()) {
					$(this).hide();
				}
				else if($('[name=filter_unbooked_searchbox]').val() != '' && $('[name=filter_unbooked_searchbox]').val() != undefined) {
					var search_string = $('[name=filter_unbooked_searchbox]').val().toLowerCase();;
					if($(this).data('text').toString().toLowerCase().indexOf(search_string) == -1 && $(this).data('id').toString().toLowerCase().indexOf(search_string) == -1) {
						$(this).hide();
					}
				}
			});
		}
		</script>
		<a href="?<?= http_build_query($page_query) ?>" class="btn brand-btn pull-right">Add</a>
		<h3>Wait List</h3>
		<div class="block-group unbooked" style="height: calc(100% - 4.5em); overflow-y: auto;">
			<?php $weekday = date('l',strtotime($calendar_start));
			$unbooked_html = '';
			$unbooked_filters = ','.get_config($dbc, 'unbooked_appt_filters').',';
			$patient_filters = [];
			$injuirytype_filters = [];
			$appttype_filters = [];
			$waitlist = mysqli_query($dbc, "SELECT * FROM `waitlist` LEFT JOIN `patient_injury` ON `waitlist`.`injuryid`=`patient_injury`.`injuryid` WHERE `waitlist`.`deleted`=0 AND (`desired_date` <= '$calendar_start' OR IFNULL(`desired_date`,'') IN ('','0000-00-00')) AND (`end_wait_date` >= '$calendar_start' OR IFNULL(`end_wait_date`,'') IN ('','0000-00-00')) AND (`available_days` LIKE '%%' OR IFNULL(`available_days`,'') = '')");
			while($wait_line = mysqli_fetch_array($waitlist)) {
				$page_query['waitlistid'] = $wait_line['waitlistid'];
				$patient_filters[$wait_line['patientid']]++;
				$injurytype_filters[$wait_line['injury_type']]++;
				$appttype_filters[$wait_line['appt_type']]++;
				$search_text = get_contact($dbc, $wait_line['patientid']).' '.$wait_line['injury_name'].' '.$wait_line['injury_type'].' '.get_type_from_booking($dbc, $wait_line['appt_type']);
				$injury_name = $wait_line['injury_name'];

				$unbooked_html .= '<a href="?'.http_build_query($page_query).'"><div class="block-item active" style="position: relative; border: 1px solid rgba(0,0,0,0.5); margin: 0.25em 0 0;" data-type="waitlist" data-id="'.$wait_line['waitlistid'].'" data-customer="'.$wait_line['patientid'].'" data-injury="'.$wait_line['injury_type'].' " data-type="'.$wait_line['appt_type'].'" data-text="'.$search_text.'" data-title="View Appointment">
                	<div class="drag-handle full-height" title="Drag Me!">
						<img class="drag-handle no-toggle" src="'.WEBSITE_URL.'/img/icons/drag_handle.png" style="filter: brightness(200%); float: right; width: 2em;" title="Drag">
					</div>
					Patient: '.get_contact($dbc, $wait_line['patientid']).'<br />
					Injury: '.$wait_line['injury_type'].' - '.$wait_line['injury_name'].'<br />
					Date Range: '.$wait_line['desired_date'].' - '.$wait_line['end_wait_date'].'<br />
					Time Range: '.$wait_line['start_time'].' - '.$wait_line['end_time'].'<br />
					Days Available: '.implode(', ',(explode(',', $wait_line['available_days']))).'</div></a>';
			}
			unset($page_query['waitlistid']); ?>

			<?php if(strpos($unbooked_filters, ',patient,') !== FALSE) { ?>
			<label class="super-label">Patient:
				<select name="filter_unbooked_cust" data-placeholder="Select a Patient" class="chosen-select-deselect unbooked_waitlist_patient"><option></option>
					<?php $contacts = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid` IN (SELECT `patientid` FROM `waitlist` WHERE `deleted`=0 AND (`desired_date` <= '$calendar_start' OR IFNULL(`desired_date`,'') IN ('','0000-00-00')) AND (`end_wait_date` >= '$calendar_start' OR IFNULL(`end_wait_date`,'') IN ('','0000-00-00')))"),MYSQLI_ASSOC));
					foreach($contacts as $contactid) { ?>
						<option value="<?= $contactid ?>"><?= get_contact($dbc, $contactid) ?> (<?= !empty($patient_filters[$contactid]) ? $patient_filters[$contactid] : '0' ?>)</option>
					<?php } ?>
				</select></label>
			<?php } ?>
			<?php if(strpos($unbooked_filters, ',injurytype,') !== FALSE) { ?>
			<label class="super-label">Injury Type:
				<select name="filter_unbooked_injury" data-placeholder="Select an Injury" class="chosen-select-deselect unbooked_waitlist_injury"><option></option>
					<?php $injuries = mysqli_query($dbc, "SELECT `injury_type` FROM `patient_injury` WHERE `injuryid` IN (SELECT `injuryid` FROM `waitlist` WHERE `deleted`=0 AND (`desired_date` <= '$calendar_start' OR IFNULL(`desired_date`,'') IN ('','0000-00-00')) AND (`end_wait_date` >= '$calendar_start' OR IFNULL(`end_wait_date`,'') IN ('','0000-00-00'))) GROUP BY `injury_type`");
					while($injury = mysqli_fetch_array($injuries)) { ?>
						<option value="<?= $injury['injury_type'] ?>"><?= $injury['injury_type'] ?> (<?= !empty($injurytype_filters[$injury['injury_type']]) ? $injurytype_filters[$injury['injury_type']] : '0' ?>)</option>
					<?php } ?>
				</select></label>
			<?php } ?>
			<?php if(strpos($unbooked_filters, ',appttype,') !== FALSE) { ?>
			<label class="super-label">Appointment Type:
				<select name="filter_unbooked_type" data-placeholder="Select an Appointment Type" class="chosen-select-deselect unbooked_waitlist_type"><option></option>
                    <?php $appointment_types = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `appointment_type` WHERE `deleted` = 0"),MYSQLI_ASSOC);
                    foreach ($appointment_types as $appointment_type) { ?>
                        <option value="<?= $appointment_type['id'] ?>"><?= $appointment_type['name'] ?> (<?= !empty($appttype_filters[$appointment_type['id']]) ? $appttype_filters[$appointment_type['id']] : '0' ?>)</option>
                    <?php } ?>
				</select></label>
			<?php } ?>
			<?php if(strpos($unbooked_filters, ',searchbox,') !== FALSE) { ?>
			<label class="super-label">Search:
				<input type="text" name="filter_unbooked_searchbox" class="form-control" onkeyup="filterTickets();">
			</label>
			<?php } ?>
			<h4>Patients:</h4>
		</div>
	<?php } ?>
<?php }