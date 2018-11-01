<?php
/*
Staff Listing
*/
include ('../include.php');
error_reporting(0);
$rookconnect = get_software_name();

/* Export Contacts */
if(isset($_POST['export_contacts'])) {
	include('../Contacts/edit_fields.php');

	set_time_limit(0);
	$category = 'Staff';
	$today_date = date('Y-m-d_h-i-s-a', time());
  	if (!file_exists('exports')) {
  		mkdir('exports', 0777, true);
  	}
	$FileName = "exports/contacts_export_".$today_date.".csv";
	$file = fopen($FileName,"w");

	$field_configs = mysqli_fetch_all(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tab` = '$category' AND `subtab` != '' AND `subtab` IS NOT NULL AND `subtab` != '**no_subtab**'"),MYSQLI_ASSOC);
	$fields = '';
	foreach ($field_configs as $field_config) {
		$fields .= trim($field_config['contacts'],',').',';
	}
	$field_config = array_filter(array_unique(explode(',', $fields)));

	$select_list = ['`contacts`.`contactid`'];
	foreach($tab_list as $tab_label => $tab_data) {
		if(in_array_any($tab_data[1],$field_config)) {
			foreach($tab_data[1] as $key => $field_option) {
				if(in_array($field_option,$field_config) && is_string($key) && $key != 'contactid') {
					$check_field_exists = mysqli_query($dbc, "SELECT * FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = '".DATABASE_NAME."' AND (`TABLE_NAME` = 'contacts' OR `TABLE_NAME` = 'contacts_cost' OR `TABLE_NAME` = 'contacts_dates' OR `TABLE_NAME` = 'contacts_description' OR `TABLE_NAME` = 'contacts_medical') AND `COLUMN_NAME` = '$key'");
					if(mysqli_num_rows($check_field_exists) > 0) {
						$select_list[] = '`'.$key.'`';
					}
				}
			}
		}
	}
	if(($key = array_search('`contactid`', $select_list)) !== FALSE) {
		unset($select_list[$key]);
	}
	$select_list = array_unique($select_list);
	$select_empty = '';
	for ($i = 0; $i < count($select_list); $i++) {
		$select_empty[] = "''";
	}
	$select_empty = implode(',', $select_empty);
	$select_list = implode(',', $select_list);

	$sql = "SELECT * FROM (SELECT $select_list FROM `contacts` LEFT JOIN `contacts_cost` ON `contacts`.`contactid`=`contacts_cost`.`contactid` LEFT JOIN `contacts_dates` ON `contacts`.`contactid`=`contacts_dates`.`contactid` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` LEFT JOIN `contacts_medical` ON `contacts`.`contactid`=`contacts_medical`.`contactid` WHERE `contacts`.`deleted` = 0 AND `contacts`.`status` > 0 AND `category` = 'Staff' UNION SELECT $select_empty) export_table";

	$result = mysqli_query($dbc, $sql);

	$headings = true;
	$HeadingsArray = array();

	while($row = mysqli_fetch_assoc($result)) {
		$valuesArray=array();
		foreach($row as $name => $value){
			if($headings) {
				$HeadingsArray[] = $name;
			}

			if(isEncrypted($name)) {
				$value = decryptIt($value);
			}

			$valuesArray[]=html_entity_decode($value);
		}

		if($headings) {
			fputcsv($file, $HeadingsArray);
		}
		fputcsv($file,$valuesArray);
		$headings = false;
	}

	fclose($file);
	header("Location: $FileName");
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename='.str_replace('exports/','',$FileName));
	header('Pragma: no-cache');

	$update_log = 'All contacts under the '.$category.' category were exported.';

	$today_date = date('Y-m-d H:i:s', time());
	$contactid = $_SESSION['contactid'];
	$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
	while($row = mysqli_fetch_assoc($result)) {
		$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
	}
	$query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Contacts', 'Export', '$update_log', '$today_date', '$name')";
	$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
}
?>
</head>
<script type="text/javascript">
$(document).ready(function() {
	retrieveStaff();
	if($('.active[data-category]').length > 0) {
		loadStaff();
	}

	$('.search_list').keyup(function() {
		if($('.page_title').text() != 'Staff Positions') {
			if(current_staff_search_key != this.value.toLowerCase()) {
				loadStaff();
			}
		}
	});
	$('.staff_form').submit(function(event) {
		if($('.page_title').text() != 'Staff Positions') {
			event.preventDefault();
		}
	});

	$('.staff_anchor').off('click').click(function() {
		current_staff_search_key = '';
		$('.search_list').val('');
		var parent = $(this).closest('.sidebar-higher-level.highest-level');
		$('.sidebar-higher-level.highest-level').not(parent).find('.active').removeClass('active blue');
		$(this).find('li').toggleClass('active blue');
		$('.sidebar-higher-level').each(function() {
			if($(this).find('.active').length > 0) {
				$(this).find('a.cursor-hand').first().addClass('active');
			}
		});
		loadStaff();
		return false;
	});

    $("#report").change(function(){
        url = $(location).attr('hostname');
        value = $(this).val();
        window.location.href = 'staff.php?tab=reporting&report='+value;
    });

	$('#staff_accordions .panel-heading').click(loadPanel);
	$('#search_staff_form').submit(function() {
		window.location.href = $(this).prop('action');
	});
	if($(window).width() > 767) {
		resizeScreen();
		$(window).resize(function() {
			resizeScreen();
		});
	}
});
var ajax_loads = [];
var staff_list = [];
var result_list = [];
var current_staff_search_key = '';
function retrieveStaff() {
	$.ajax({
		url: '../Staff/staff_load_list.php',
		success: function(response) {
			staff_list = JSON.parse(response);
		}
	});
}
function loadStaff() {
	$('.search_list').prop('placeholder', 'Search Staff');
	$('.non_staff_tab').removeClass('active blue');
	$('.standard-body').removeClass('standard-body').addClass('standard-dashboard-body');
	$('.standard-body-title').removeClass('standard-body-title').addClass('standard-dashboard-body-title');
	clearTimeout(continue_loading);
	ajax_loads.forEach(function(call) { call.abort(); });
	$('.standard-dashboard-body-content:visible').empty().html('<h4 class="col-sm-12">Enter a search term to display <?= $ticket_tile ?> here<h4>');
	if(staff_list.length == 0 || staff_list == undefined) {
		setTimeout(function() { loadStaff(); }, 500);
	} else {
		var target = $('.standard-dashboard-body-content:visible');

		var category = [];
		$('.active[data-category]').each(function() {
			if($(this).data('category') != '' && $(this).data('category') != 'ALL') {
				category.push($(this).data('category'));
			}
		});
		var status = [];
		$('.active[data-status]').each(function() {
			if($(this).data('status') >= 0) {
				status.push($(this).data('status'));
			}
		});
		var match_contacts = [];
		$('.active[data-match-contact]').each(function() {
			if($(this).data('match-contact') != '') {
				match_contacts.push($(this).data('match-contact'));
			}
		});

		$('.page_title').text('Staff - '+$('.active[data-category]').first().text());

		target.html('');

		result_list = [];
		var filter_list = [];
		staff_list.forEach(function(staff) {
			if((staff.staff_category.some(function(element) { return this.indexOf(element) >= 0; }, category) || category.length == 0) && (status.indexOf(parseInt(staff.status)) >= 0 || status.length == 0) && (staff.match_contacts.some(function(element) { return this.indexOf(parseInt(element)) >= 0; }, match_contacts) || match_contacts.length == 0)) {
				filter_list.push(staff);
			}
		});
		var key = $('.search_list:visible').val();
		if(key != undefined) {
			key = key.toLowerCase();
		}
		current_staff_search_key = key;
		filter_list.forEach(function(staff) {
			if(key == '' || key == undefined || staff.search_string.toLowerCase().search(key) >= 0) {
				result_list.push(staff);
			}
		});
		
		if(result_list.length > 0) {
			showStaff(result_list, target);
		} else {
			$('.standard-dashboard-body-content:visible').empty().html('<h4 class="col-sm-12">No Staff Found.<h4>');
		}
	}
}
var continue_loading = '';
function showStaff(result_list, target) {
	clearTimeout(continue_loading);
	if($('.standard-dashboard-body-content:visible').text() != 'No Staff Found.') {
		if($('.dashboard-item').length == 0 || $('.dashboard-item').last().offset().top - $(window).scrollTop() < $(window).innerHeight() + 500) {
			var staff = result_list.shift();
			if(staff != undefined && staff.id > 0) {
				ajax_loads.push($.ajax({
					url: 'staff_load.php?contactid='+staff.id,
					success: function(response) {
						target.append(response);
						continue_loading = showStaff(result_list, target);
						initTooltips();
						initIconColors();
					}
				}));
			}
		} else if(result_list.length > 0) {
			continue_loading = setTimeout(function() { showStaff(result_list, target) },1000);
		}
	}
}
function resizeScreen() {
	var view_height = $(window).height() > 800 ? $(window).height() : 800;
	$('#staff_div .scale-to-fill .main-screen,#staff_div .tile-sidebar,#staff_div .scale-to-fill.tile-content').height(view_height - $('#staff_div .scale-to-fill').offset().top - $('#footer').outerHeight());
}
function loadPanel() {
    $('#staff_accordions .panel-heading:not(.higher_level_heading)').closest('.panel').find('.panel-body').html('Loading...');
    if(!$(this).hasClass('higher_level_heading')) {
		body = $(this).closest('.panel').find('.panel-body');
		if($(body).hasClass('staff_mobile')) {
			var category = $(body).data('category');
			var status = $(body).data('status');
			result_list = [];

			staff_list.forEach(function(staff) {
				if((staff.staff_category.some(function(element) { return this.indexOf(element) >= 0; }, category) || category == '') && (status == parseInt(staff.status) || status == '')) {
					result_list.push(staff);
				}
			});

			$(body).empty();
			if(result_list.length > 0) {
				showStaff(result_list, body);
			} else {
				$(body).html('<h4>No Staff Found.</h4>');
			}
		} else {
			$.ajax({
				url: $(body).data('file'),
				response: 'html',
				success: function(response) {
					$(body).html(response);
					$('.pagination_links a').click(pagination_load);
				}
			});	
		}
	}
}
function pagination_load() {
	var target = $(this).closest('.panel').find('.panel-body');
	$.ajax({
		url: this.href,
		method: 'POST',
		response: 'html',
		success: function(response) {
			target.html(response);
			$('.pagination_links a').click(pagination_load);
		}
	});
	return false;
}
</script>
<body>
<?php
include_once ('../navigation.php');
checkAuthorised('staff');

$detect			= new Mobile_Detect;
$is_mobile		= ( $detect->isMobile() ) ? true : false;
$mobile_view	= false;

// Setup tabs
$tab_list = [ 'active' => false, 'probation' => false, 'suspended' => false, 'security' => false, 'positions' => false, 'reminders' => false, 'reporting' => false ];
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'active';
$tab_note = '';
$security_access = get_security($dbc, 'staff');
$staff_categories = array_filter(explode(',',str_replace(',,',',',str_replace('Staff','',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT categories FROM field_config_contacts WHERE tab='Staff' AND `categories` IS NOT NULL"))['categories']))));
$staff_categories = array_merge(['ALL'],$staff_categories);
if(empty($staff_categories)) {
	$staff_categories = ['ALL'];
}
switch($tab) {
	case 'suspended':
		$body_title = 'Staff';
		$search_action = 'suspended';
		$tab_list['suspended'] = true;
		$tab_note = "A listing of all suspended staff. Suspended staff can be edited, reactivated or archived from here.";
		$staff_cat = $_GET['staff_cat'];
		break;
	case 'probation':
		$body_title = 'Staff';
		$search_action = 'probation';
		$tab_list['probation'] = true;
		$tab_note = "A listing of all staff on probation. Staff on probation can be edited, taken off probation, or archived from here.";
		$staff_cat = $_GET['staff_cat'];
		break;
	case 'security':
		$body_title = 'Security Privileges';
		$search_action = 'active';
		$tab_list['security'] = true;
		$tab_note = "Once you've activated security levels, you can now assign specific security privileges to active security levels. Select the security level you wish to assign security privileges to from the drop down menu and choose the security privileges for each tile that you want for that security level. Each sub tab or sub set of functionality for each tile can be set by clicking the gear in the sub tab permission column. Once you've assigned security privileges to a security level and that level is assigned to a staff or third party accessing the system, you're designating or restricting view as you've selected.";
		break;
	case 'positions':
		$body_title = 'Staff Positions';
		$search_action = 'positions';
		$tab_list['positions'] = true;
		$tab_note = "Below you can add/edit/delete positions that can be assigned to staff. Multiple positions can be assigned to any one staff. Positions can be used in invoicing, project management tools and rate cards for assigning and tracking cost structures.";
		break;
	case 'reminders':
		$body_title = 'Reminders';
		$search_action = 'reminders';
		$tab_list['reminders'] = true;
		$tab_note = "View all reminders in the selected date range.";
		break;
	case 'reporting':
		$body_title = 'Reporting';
		$search_action = 'reporting';
		$tab_list['reporting'] = true;
		$tab_note = "This report provides a log of everything done and all time tracked per staff";
		break;
	default:
		$body_title = 'Staff';
		$search_action = 'active';
		$_GET['tab'] = 'active';
		$tab_list['active'] = true;
		$tab = 'active';
		$tab_note = "A listing of all Active Users within your software.";
		$staff_cat = $_GET['staff_cat'];
		break;
}
if(empty($_GET['staff_cat']) && in_array($_GET['tab'], ['active','suspended','probation'])) {
	$_GET['staff_cat'] = 'ALL';
}
$db_tabs = explode(',','active,'.get_config($dbc, 'staff_tabs'));
?>
<div id="staff_div" class="container">
	<div class="iframe_overlay" style="display:none;">
		<div class="iframe">
			<div class="iframe_loading">Loading...</div>
			<iframe name="staff_iframe" src=""></iframe>
		</div>
	</div>
	<div class="row">
		<!-- <div id="no-more-tables" class="main-screen contacts-list"> -->
		<div class="main-screen contacts-list">
            <!-- Tile Header -->
            <div class="tile-header standard-header">
                <div class="col-xs-12 col-sm-4">
                    <h1>
                        <span class="pull-left" style="margin-top: -5px;"><a href="staff.php" class="default-color">Staff Dashboard</a></span>
                        <span class="clearfix"></span>
                    </h1>
                </div>
                <div class="col-xs-12 col-sm-8 text-right settings-block">
                    <?php if ( $security_access['config'] > 0 ) { ?>
                        <div class="pull-right gap-left top-settings">
                            <a href="?settings=dashboard" class="mobile-block pull-right "><img title="Tile Settings" src="<?= WEBSITE_URL; ?>/img/icons/settings-4.png" class="settings-classic wiggle-me" width="30"></a>
                            <span class="popover-examples list-inline pull-right" style="margin:5px 5px 0 0;"><a data-toggle="tooltip" data-placement="top" title="Click here for the settings within this tile. Any changes made will appear on your dashboard."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
                        </div><?php
                    } ?>
					<!-- <?php if(isset($_POST['search_user_submit'])) { ?>
						<input placeholder="Search Marketing Material" type="text" name="search_vendor" class="form-control pull-left" value="<?php echo $_POST['search_vendor']; ?>" style="width: 40%;">
					<?php } else { ?>
						<input placeholder="Search Marketing Material" type="text" name="search_vendor" class="form-control pull-left" style="width: 40%;">
					<?php } ?>
					<button type="submit" name="search_user_submit" class="btn brand-btn pull-left" style="position: relative; left: 1em;">Filter</button>
					<button type="submit" name="display_all_inventory" class="btn brand-btn pull-left" style="position: relative; left: 1em;">Display All</button> -->
					<?php if($security_access['edit'] > 0 || $security_access['add'] > 0) { ?>
						<?php if($security_access['edit'] > 0) { ?>
							<form action="" method="POST" class="form-horizontal">
								<button type="submit" name="export_contacts" value="Staff" class="btn brand-btn pull-right">Export Staff</button>
							</form>
						<?php } ?>
						<?php if($tab == 'positions' && $security_access['add'] > 0) { ?>
							<a href="position_edit.php" class="btn brand-btn pull-right">New Position</a>
						<?php } else if($tab == 'reminders' && $security_access['add'] > 0) { ?>
							<a href="add_reminder.php" class="btn brand-btn pull-right">New Reminder</a>
						<?php } else if($security_access['add'] > 0) { ?>
                    		<a href="staff_edit.php" class="btn brand-btn pull-right">New Staff</a>
                    	<?php } ?>
                    <?php } ?>
                </div>
                <div class="clearfix"></div>
            </div><!-- .tile-header -->

            <?php if(!isset($_GET['settings'])) { ?>
				<div id='staff_accordions' class='sidebar show-on-mob panel-group block-panels col-xs-12'>
					<?php foreach($staff_categories as $staff_category) { ?>
						<div class="panel panel-default">
							<div class="panel-heading higher_level_heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#staff_accordions" href="#collapse_<?= config_safe_str($staff_category) ?>">
										<?= ($staff_category == 'ALL' ? 'All Staff' : $staff_category) ?><span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_<?= config_safe_str($staff_category) ?>" class="panel-collapse collapse">
                                <div class="panel-body" style="padding: 0; margin: -1px;" id="collapse_<?= config_safe_str($staff_category) ?>_body">
                                	<?php $staff_tabs = ['active','probation','suspended'];
									foreach($staff_tabs as $staff_tab) {
										if(in_array($staff_tab,$db_tabs) && check_subtab_persmission($dbc, 'staff', ROLE, 'active') === TRUE) { ?>
											<div class="panel panel-default">
												<div class="panel-heading">
													<h4 class="panel-title">
														<a data-toggle="collapse" data-parent="#collapse_<?= config_safe_str($staff_category) ?>_body" href="#collapse_<?= $staff_tab ?>_<?= config_safe_str($staff_category) ?>" class="double-pad-left">
															<?= ($staff_tab == 'active' ? 'Active Users' : ($staff_tab == 'probation' ? 'Users on Probation' : 'Suspended Users')) ?><span class="glyphicon glyphicon-plus"></span>
														</a>
													</h4>
												</div>

												<div id="collapse_<?= $staff_tab ?>_<?= config_safe_str($staff_category) ?>" class="panel-collapse collapse">
													<div class="panel-body staff_mobile" data-category="<?= ($staff_category == 'ALL' ? '' : $staff_category) ?>" data-status="<?= ($staff_tab == 'active' ? '1' : ($staff_tab == 'probation' ? '2' : '0')) ?>">
														Loading...
													</div>
												</div>
											</div>
										<?php }
									} ?>
								</div>
							</div>
						</div>
					<?php } ?>
					<?php if(in_array('security',$db_tabs) && check_subtab_persmission($dbc, 'staff', ROLE, 'security') === TRUE) { ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#staff_accordions" href="#collapse_security">
										Security Privileges<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_security" class="panel-collapse collapse">
								<div class="panel-body" data-file="staff_security.php">
									Loading...
								</div>
							</div>
						</div>
					<?php } ?>
					<?php if(in_array('positions',$db_tabs) && check_subtab_persmission($dbc, 'staff', ROLE, 'positions') === TRUE) { ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#staff_accordions" href="#collapse_positions">
										Staff Positions<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_positions" class="panel-collapse collapse">
								<div class="panel-body" data-file="positions.php">
									Loading...
								</div>
							</div>
						</div>
					<?php } ?>
					<?php if(in_array('reminders',$db_tabs) && check_subtab_persmission($dbc, 'staff', ROLE, 'reminders') === TRUE) { ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#staff_accordions" href="#collapse_reminders">
										Reminders<span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_reminders" class="panel-collapse collapse">
								<div class="panel-body" data-file="staff_reminder.php">
									Loading...
								</div>
							</div>
						</div>
					<?php } ?>

                    <?php if(in_array('reporting',$db_tabs) && check_subtab_persmission($dbc, 'staff', ROLE, 'reporting') === TRUE) { ?>
                                <div class="panel panel-default">
                                    <div class="panel-heading">
                                        <h4 class="panel-title">
                                            <a data-toggle="collapse" data-parent="#staff_accordions" href="#collapse_report">
                                                Reporting<span class="glyphicon glyphicon-plus"></span>
                                            </a>
                                        </h4>
                                    </div>

                                    <div id="collapse_report" class="panel-collapse collapse">
                                        <div class="panel-body" data-file="staff_reminder.php">
                                            Loading...
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>

				</div>
			<?php } ?>

            <?php
			$tab_info = [ 'active' => 'Manage currently active Staff members.',
				'probation' => 'Manage Staff members currently on Probation.',
				'suspended' => 'Manage suspended Staff members.',
				'security' => 'View user and function security settings.',
				'positions' => 'Manage user and Staff positions.',
				'reminders' => 'Create reminders to update the Staff profile.' ];
			$tab_name = [ 'active' => 'Active Users',
				'probation' => 'Users on Probation',
				'suspended' => 'Suspended Users',
				'security' => 'Security Privileges',
				'positions' => 'Positions',
				'reminders' => 'Reminders',
                'reporting' => 'Reporting', ]; ?>

			<?php if(!isset($_GET['settings'])) {
            $report = '';
            if(!empty($_GET['report'])) {
                $report = '&report='.$_GET['report'];
            }
            ?>
				<form id="form1" name="form1" method="post"	action="staff.php?tab=<?= $search_action.$report ?>&filter=All&staff_cat=<?= $_GET['staff_cat'] ?>" enctype="multipart/form-data" class="form-horizontal staff_form" role="form">
					<!-- Sidebar -->
					<div class="standard-collapsible tile-sidebar sidebar hide-titles-mob">
						<ul>
							<li class="standard-sidebar-searchbox"><input type="text" name="search_contacts" value="" class="form-control search_list" placeholder="Search <?= $_GET['tab'] == 'positions' ? 'Positions' : 'Staff' ?>"></li>
							<input type="hidden" name="search_contacts_submit" value="1">
							<?php $db_tabs = explode(',','active,'.get_config($dbc, 'staff_tabs'));
							$db_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contacts_dashboard FROM field_config_contacts WHERE `tab`='Staff' AND contacts_dashboard IS NOT NULL"));
							$field_display = explode(",",$db_config['contacts_dashboard']);
							foreach($staff_categories as $staff_category) {
								echo '<li class="sidebar-higher-level highest-level"><a data-category="'.$staff_category.'" class="cursor-hand '.($_GET['staff_cat'] == $staff_category ? 'active' : 'collapsed').'" data-toggle="collapse" data-target="#staff_'.config_safe_str($staff_category).'">'.($staff_category == 'ALL' ? 'All Staff' : $staff_category).'<span class="arrow"></span></a>';
								echo '<ul id="staff_'.config_safe_str($staff_category).'" class="collapse '.($_GET['staff_cat'] == $staff_category ? 'in' : '').'">';
								if(in_array("Sort Match Contacts", $field_display)) {
									echo '<li class="sidebar-higher-level"><a class="cursor-hand '.($_GET['staff_cat'] == $staff_category && $_GET['match_contact'] > 0 ? 'active' : 'collapsed').'" data-toggle="collapse" data-target="#staff_'.config_safe_str($staff_category).'_match">Matched Contacts<span class="arrow"></span></a>';
									echo '<ul class="collapse '.($_GET['staff_cat'] == $staff_category && $_GET['match_contact'] > 0 ? 'in' : '').'" id="staff_'.config_safe_str($staff_category).'_match">';
									$match_contacts = [];
									$sorted_match_contacts = [];
									$match_contacts_query = mysqli_query($dbc, "SELECT * FROM `match_contact` WHERE `deleted` = 0");
									while($match_contacts_result = mysqli_fetch_assoc($match_contacts_query)) {
										foreach(explode(',', $match_contacts_result['support_contact']) as $support_contact) {
											foreach(explode(',', $match_contacts_result['staff_contact']) as $staff_contact) {
												if($staff_category == 'ALL' || strpos(','.get_contact($dbc, $staff_contact, 'staff_category').',', ','.$staff_category.',') !== FALSE) {
													if(!in_array($support_contact,
														$sorted_match_contacts)) {
														$sorted_match_contacts[] = $support_contact;
													}
													if(!in_array($staff_contact, $match_contacts[$support_contact])) {
														$match_contacts[$support_contact][] = $staff_contact;
													}
												}
											}
										}
									}
									$sorted_match_contacts = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` IN (".implode(',', $sorted_match_contacts).")"));
									foreach($sorted_match_contacts as $match_contact) {
										echo '<a href="" class="staff_anchor"><li data-match-contact="'.$match_contact['contactid'].'" class="'.($_GET['staff_cat'] == $staff_category && $_GET['match_contact'] == $match_contact['contactid'] ? 'active blue' : '').'">'.$match_contact['full_name'].'<span class="pull-right">'.count(array_filter($match_contacts[$support_contact])).'</span></li></a>';
									}
									echo '</ul></li>';
								}
								$staff_tabs = ['active','probation','suspended'];
								foreach($staff_tabs as $staff_tab) {
									if(in_array($staff_tab,$db_tabs) && check_subtab_persmission($dbc, 'staff', ROLE, 'active') === TRUE) {
										$filter_query = '';
										if($staff_category != 'ALL') {
											$filter_query = " AND CONCAT(',',`staff_category`,',') LIKE '%,$staff_category,%'";
										}
										$count = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(`contactid`) `count` FROM `contacts` WHERE `category` = 'Staff' AND `deleted` = 0 AND `status` = '".($staff_tab == 'active' ? '1' : ($staff_tab == 'probation' ? '2' : '0'))."' AND IFNULL(`user_name`,'')!='FFMAdmin' AND `show_hide_user`='1'".$filter_query))['count'];
										echo '<a href="" class="staff_anchor"><li data-status="'.($staff_tab == 'active' ? '1' : ($staff_tab == 'probation' ? '2' : '0')).'" class="'.($_GET['tab'] == $staff_tab && $_GET['staff_cat'] == $staff_category && empty($_GET['match_contact']) ? 'active blue' : '').'">'.($staff_tab == 'active' ? 'Active Users' : ($staff_tab == 'probation' ? 'Users on Probation' : 'Suspended Users')).'<span class="pull-right">'.$count.'</span></li></a>';
									}
								}
								echo '</ul></li>';
							}


							foreach ($tab_list as $staff_tab => $tab_status) {
								if (in_array($staff_tab,$db_tabs) && check_subtab_persmission($dbc, 'staff', ROLE, $staff_tab) === TRUE && !in_array($staff_tab,['active','probation','suspended'])) {
									echo '<a href="staff.php?tab='.$staff_tab.'"><li class="non_staff_tab '.($tab_status && empty($_GET['staff_cat']) ? 'active blue' : '').'">'.$tab_name[$staff_tab].'</li></a>';
								} else if(in_array($staff_tab,$db_tabs) && !in_array($staff_tab,['active','probation','suspended'])) {
									echo '<li>'.$tab_name[$staff_tab].'</li>';
								}
							} ?>
						</ul>
					</div><!-- .tile-sidebar -->

	                <!-- Main Content -->
	                <div class="scale-to-fill has-main-screen tile-content hide-titles-mob">
	            		<div class="main-screen override-main-screen <?= !in_array($_GET['tab'], ['active','suspended','probation']) ? 'standard-body' : 'standard-dashboard-body' ?>" style="height: inherit;">
							<div class='standard-body-title' style="<?= in_array($_GET['tab'], ['active','suspended','probation']) ? 'border-bottom: none;' : '' ?>">
								<h3 class="page_title"><?= $body_title ?><?= !empty($_GET['staff_cat']) ? ' - '.$_GET['staff_cat'] : '' ?></h3>
							</div>
							<div class='standard-dashboard-body-content pad-left pad-right'>
				            	<!-- Notice -->
				                <div class="notice gap-bottom gap-top popover-examples">
				                    <div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL; ?>/img/info.png" class="wiggle-me" width="25"></div>
				                    <div class="col-sm-11"><span class="notice-name">NOTE:</span>
									<?php echo $tab_note; ?></div>
				                    <div class="clearfix"></div>
				                </div>
								<?php // Active and Suspended tabs are just separated by the category
								if($tab_list['active'] || $tab_list['probation'] || $tab_list['suspended']) :
									echo 'Loading...';
									// include('staff_list.php');
								// Display the Security tab
								elseif($tab_list['security']) :
									include('staff_security.php');
								// Display the Positions tab
								elseif($tab_list['positions']) :
									include('positions.php');
								elseif($tab_list['reminders']) :
									include('staff_reminder.php');
                                elseif($tab_list['reporting']) :
									include('staff_reporting.php');
								endif; ?>
							</div>
						</div>
					</div>
		        	<div class="clearfix"></div>
				</form>
        	<?php } else { ?>
				<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
					<?php include('field_config.php'); ?>
				</form>
        	<?php } ?>
		</div>
	</div>
</div>

<?php include ('../footer.php'); ?>
