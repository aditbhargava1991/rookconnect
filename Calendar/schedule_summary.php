<style>
.today-btn {
  color: #fafafa;
  background: green;
  border: 2px solid #fafafa; }
.highlightCell {
	background-color: rgba(0,0,0,0.2);
}
</style>
<script type="text/javascript" src="calendar.js"></script>
<script>
$(document).ready(function() {
	global_type = '';

	if($('#collapse_region .block-item.active').length > 1) {
		$('#collapse_region .block-item.active:not(:first)').removeClass('active');
	}
	<?php if($multi_class_admin == 1 && strpos(','.ROLE.',', ',admin,') === FALSE && strpos(','.ROLE.',', ',super,') === FALSE) { ?>
		if($('#collapse_classifications .block-item.active').length > 1) {
			$('#collapse_classifications .block-item.active:not(:first)').removeClass('active');
		}
	<?php } ?>

	toggle_columns(global_type);
	reload_all_data_month();

	//Display active blocks when collapsed
	displayActiveBlocksAuto();
	$('.collapsible .sidebar .panel').on('hidden.bs.collapse', function() {
		$(this).next('.active_blocks').show();
	});
	$('.collapsible .sidebar .panel').on('show.bs.collapse', function() {
		$(this).next('.active_blocks').hide();
	});
});
function check_contact_category(link) {
	$('#collapse_contact').find('.block-item[data-category="'+$(link).data('category')+'"]').removeClass("active");
	if($(link).find('.block-item').hasClass('active')) {
		$(link).find('.block-item').removeClass('active');
	} else {
		$(link).find('.block-item').addClass('active');
		$('#collapse_contact').find('.block-item[data-category="'+$(link).data('category')+'"]').addClass("active");
	}
	toggle_columns();
}
function toggle_columns(type = global_type) {
	if(type == 'staff') {
		$('[id^=collapse_equipment]').find('.block-item').removeClass('active');
		$('#collapse_teams').find('.block-item').removeClass('active');
		global_type = 'staff';
	} else if(type == 'team') {
		$('[id^=collapse_equipment]').find('.block-item').removeClass('active');
		$('[id^=collapse_staff]').find('.block-item').removeClass('active');
		$('[id^=collapse_contractors]').find('.block-item').removeClass('active');
		global_type = 'team';
	} else {
		$('[id^=collapse_staff]').find('.block-item').removeClass('active');
		$('#collapse_teams').find('.block-item').removeClass('active');
		$('[id^=collapse_contractors]').find('.block-item').removeClass('active');
		global_type = '';
	}
	$('.active_blocks .block-item,.active_blocks').hide();

	// Hide deselected columns
	var visibles = [];
	var regions = [];
    var classifications = [];
    var locations = [];
	var clients = [];
	var teams = [];
	var staff = [];
	var contractors = [];
	var ea_visibles = [];
	
    // Filter selected regions
	$('#collapse_region').find('.block-item.active').each(function() {
		var region = $(this).data('region');
		if(region == '**UNASSIGNED**') {
			regions.push('');
		}
		regions.push(region);

		//Active blocks
		$('.active_blocks_regions .block-item').filter(function() { return $(this).data('region') == region; }).show();
	});
    // Filter selected classifications
	$('#collapse_classifications').find('.block-item').each(function() {
		//Classification active users
		<?php if($scheduling_classification_loggedin == 1 && get_contact($dbc, $_SESSION['contactid'], 'category') == 'Staff') { ?>
			retrieveClassificationUsers(this);
		<?php } ?>
		
		var class_regions = $(this).data('regions');
		if(class_regions.length == 0) {
			class_regions = [""];
		}
		var classification = this;
		$(this).show();
		class_regions.forEach(function(class_region) {
			if(regions.indexOf(class_region) < 0 && regions.length > 0 && $(classification).data('classification') != '**UNASSIGNED**') {
				$(classification).removeClass('active').hide();
			}
		});
		if($(this).is('.active')) {
			var classification = $(this).data('classification');
			if(classification == '**UNASSIGNED**') {
				classifications.push('');
			}
			classifications.push(classification);

			//Active blocks
			$('.active_blocks_classifications .block-item').filter(function() { return $(this).data('classification') == classification; }).show();
		}
	});
	// Display classification logos
	if(classifications.length > 0) {
		$('.selected_class_logos .id-circle').hide();
		classifications.forEach(function(classification) {
			$('.selected_class_logos').find('.id-circle[data-classification="'+classification+'"]').show();
		});
		$('.selected_class_logos').css('display', 'table');
	} else {
		$('.selected_class_logos').hide();
	}
    // Filter selected locations
	$('#collapse_locations').find('.block-item.active').each(function() {
		var location = $(this).data('location');
		if(location == '**UNASSIGNED**') {
			locations.push('');
		}
		locations.push(location);

		//Active blocks
		$('.active_blocks_locations .block-item').filter(function() { return $(this).data('location') == location; }).show();
	});
	// Filter selected clients
	$('#collapse_customers').find('.block-item.active').each(function() {
		var clientid = $(this).data('client');
		clients.push(parseInt(clientid));
	});
    
	// Hide clients that are not in selected regions/classifications/location
	$('#collapse_customers').find('.block-item').each(function () {
		var clientid = $(this).data('client');
		var client_region = $(this).data('region');
        var client_classification = $(this).data('classification');
        var client_location = $(this).data('location');
		if ( (regions.indexOf(client_region) == -1 && regions.length > 0 && client_region != '') || (locations.indexOf(client_location) == -1 && locations.length > 0 && client_location != '') ) {
			$(this).hide();
			$(this).removeClass('active');
		} else {
			$(this).show();

			//Active blocks
			$('.active_blocks_customers .block-item').filter(function() { return $(this).data('client') == clientid; }).show();
		}
	});
	// Hide equipments that are not attached to selected clients/regions/classifications/location
	$('[id^=collapse_equipment]').find('.block-item').each(function() {
		var region_pass = true;
		var location_pass = true;
		var classification_pass = true;
		var equipment_region = $(this).data('region');
        if(equipment_region != undefined) {
            equipment_region = equipment_region.split('*#*');
        }
		var equipment_classification = $(this).data('classification');
        if(equipment_classification != undefined) {
            equipment_classification = equipment_classification.split('*#*');
        }
		var equipment_location = $(this).data('location')
        if(equipment_location != undefined) {
            equipment_location = equipment_location.split('*#*');
        }

		<?php if(strpos(",$scheduling_item_filters,",",Region,") !== FALSE) { ?>
			if(regions.length > 0) {
				region_pass = false;
				equipment_region.forEach(function(this_region) {
					if(regions.indexOf(this_region) > -1) {
						region_pass = true;
					}
				});
			}
		<?php } ?>

		<?php if(strpos(",$scheduling_item_filters,",",Location,") !== FALSE) { ?>
			if(locations.length > 0) {
				location_pass = false;
				equipment_location.forEach(function(this_location) {
					if(locations.indexOf(this_location) > -1) {
						location_pass = true;
					}
				});
			}
		<?php } ?>

		<?php if(strpos(",$scheduling_item_filters,",",Classification,") !== FALSE) { ?>
			if(classifications.length > 0) {
				classification_pass = false;
				equipment_classification.forEach(function(this_classification) {
					if(classifications.indexOf(this_classification) > -1) {
						classification_pass = true;
					}
				});
			}
		<?php } ?>

		if (!region_pass || !location_pass || !classification_pass) {
			$(this).hide();
			$(this).removeClass('active');
		} else {
			$(this).show();
		}
	});
	// Hide teams that are not attached to selected regions/classifications/location
	$('#collapse_teams').find('.block-item').each(function() {
		var this_region = $(this).data('region');
		var this_classification = $(this).data('classification');
		var this_location = $(this).data('location');
		if (<?= $filter_condition ?>) {
			$(this).hide();
			$(this).removeClass('active');
		} else {
			$(this).show();
		}
	});
	// Filter selected teams
	$('#collapse_teams').find('.block-item.active').each(function() {
		var teamid = $(this).data('teamid');
		teams.push(parseInt(teamid));
		if(type == 'team') {
			var equipmentids = $(this).data('equipment').toString().split(',');
			equipmentids.forEach(function(equipmentid) {
				if(equipmentid > 0 && $('[id^=collapse_equipment]').find('.block-item[data-equipment='+equipmentid+']').length > 0) {
					var block = $('[id^=collapse_equipment]').find('.block-item[data-equipment='+equipmentid+']');
					if($(block).css('display') != 'none') {
						block.addClass('active');
					}
				}
			});
		}

		//Active blocks
		$('.active_blocks_teams .block-item').filter(function() { return $(this).data('teamid') == teamid; }).show();
	});
	// Hide staff that are not attached to selected regions/classifications/location
	$('[id^=collapse_staff],[id^=collapse_contractors]').find('.block-item').each(function() {
		var this_region = $(this).data('region');
		var this_classification = $(this).data('classification');
		var this_location = $(this).data('location');
		if (<?= $filter_condition ?>) {
			$(this).hide();
			$(this).removeClass('active');
		} else {
			$(this).show();
		}
	});
    
	// Filter selected staff
	$('[id^=collapse_staff],[id^=collapse_contractors]').find('.block-item.active').each(function() {
		var staffid = $(this).data('staff');
		staff.push(parseInt(staffid));
		if(type == 'staff') {
			var equipmentids = $(this).data('equipment').toString().split(',');
			equipmentids.forEach(function(equipmentid) {
				if(equipmentid > 0 && $('[id^=collapse_equipment]').find('.block-item[data-equipment='+equipmentid+']').length > 0) {
					var block = $('[id^=collapse_equipment]').find('.block-item[data-equipment='+equipmentid+']');
					if($(block).css('display') != 'none') {
						block.addClass('active');
					}
				}
			});
		}

		//Active blocks
		$('.active_blocks_staff .block-item,.active_blocks_contractors .block-item').filter(function() { return $(this).data('staff') == staffid; }).show();
	});
	// Show selected equipment on calendar view
	$('[id^=collapse_equipment]').find('.block-item.active').each(function() {
		var equipment_id = $(this).data('equipment');
		if (equipment_id > 0) {
			visibles.push(parseInt(equipment_id));
		}

		//Active blocks
		$('.active_blocks_equipment .block-item').filter(function() { return $(this).data('equipment') == equipment_id; }).show();
	});

	// Save which equipment are active
	$.ajax({
		url: 'calendar_ajax_all.php?fill=selected_contacts&offline='+offline_mode,
		method: 'POST',
		data: { contacts: visibles, category: 'equipment', clients: clients, regions: regions, classifications: classifications, locations: locations },
		success: function(response) {
		}
	});

	if(visibles.length > 0) {
		$('.calendar_table .calendarSortable').filter(function() { return $(this).data('contact') > 0; }).hide();

		visibles.forEach(function (contact_id) {
			$('.calendar_table .calendarSortable').filter(function() { return $(this).data('contact') == contact_id; }).show();
		});
	} else {
		$('.calendar_table .calendarSortable').filter(function() { return $(this).data('contact') > 0; }).hide();

		$('[id^=collapse_equipment]').find('.block-item').each(function() {
			if($(this).css('display') != 'none') {
				contact_id = parseInt($(this).data('equipment'));
				$('.calendar_table .calendarSortable').filter(function() { return $(this).data('contact') == contact_id; }).show();
			}
		});
	}
	resize_calendar_view_monthly();
	displayActiveBlocksAuto();
}
</script>
<?php
$client_type = get_config($dbc, 'scheduling_client_type');
?>
<input type="hidden" id="retrieve_summary" name="retrieve_summary" value="1">
<div class="hide_on_iframe ticket-calendar calendar-screen" style="padding-bottom: 0px;">
	<div class="pull-left collapsible">
		<input type="text" class="search-text form-control" placeholder="Search All">
		<div class="sidebar panel-group block-panels" id="category_accordions" style="margin: 1.5em 0 0.5em; overflow: auto; padding-bottom: 0;">
			<?php if(count($contact_regions) > 0 && strpos(",$dispatch_filters,", ",Region,") !== FALSE) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_region" >
							<span style="display: inline-block; width: calc(100% - 6em);">Regions</span><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_region" class="panel-collapse collapse">
					<div class="panel-body panel-body-height" style="overflow-y: auto; padding: 0;">
					<?php $active_regions = array_filter(explode(',',get_user_settings()['appt_calendar_regions']));
					$region_list = $contact_regions;
					foreach($region_list as $region_line => $region) {
						$color_styling = '#6DCFF6';
						if(!empty($region_colours[$region_line])) {
							$color_styling = $region_colours[$region_line];
						}
						$color_box = '<span style="height: 15px; width: 15px; background-color: '.$color_styling.'; border: 1px solid black; float: right; margin-left: 0.5em;"></span>';
						if(in_array($region, $allowed_regions)) {
							echo "<a href='' onclick='$(this).closest(\".panel\").find(\".block-item\").not($(this).find(\".block-item\")).removeClass(\"active\"); $(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); return false;'><div class='block-item ".(in_array($region,$active_regions) ? 'active' : '')."' data-region='".$region."' data-activevalue='".$region."'>".$region.$color_box."</div></a>";
						}
					}
					echo "<a href='' onclick='$(this).closest(\".panel\").find(\".block-item\").not($(this).find(\".block-item\")).removeClass(\"active\"); $(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); return false;'><div class='block-item ".(in_array('**UNASSIGNED**',$active_regions) ? 'active' : '')."' data-region='**UNASSIGNED**' data-activevalue='**UNASSIGNED**'>Unassigned Region</div></a>"; ?>
					</div>
				</div>
			</div>
			<div class="active_blocks_regions active_blocks" data-accordion="collapse_region" style="display: none;">
				<?php foreach($region_list as $region) { ?>
					<div class="block-item active" data-activevalue="<?= $region ?>"><?= $region ?></div> 
				<?php } ?>
				<div class="block-item active" data-activevalue="**UNASSIGNED**">Unassigned Region</div>
			</div>
			<?php } ?>
            
			<?php if(count($contact_locations) > 0 && strpos(",$dispatch_filters,", ",Location,") !== FALSE) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_locations" >
							<span style="display: inline-block; width: calc(100% - 6em);">Locations</span><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_locations" class="panel-collapse collapse">
					<div class="panel-body panel-body-height" style="overflow-y: auto; padding: 0;"><?php $active_locations = array_filter(explode(',',get_user_settings()['appt_calendar_locations']));
					$location_list = $allowed_locations;
					foreach($location_list as $location) {
						echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); return false;'><div class='block-item ".(in_array($location,$active_locations) ? 'active' : '')."' data-location='".$location."' data-activevalue='".$location."'>".$location."</div></a>";
					}
					echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); return false;'><div class='block-item ".(in_array('**UNASSIGNED**',$active_locations) ? 'active' : '')."' data-location='**UNASSIGNED**' data-activevalue='**UNASSIGNED**'>Unassigned Location</div></a>"; ?>
					</div>
				</div>
			</div>
			<div class="active_blocks_locations active_blocks" data-accordion="collapse_locations" style="display: none;">
				<?php foreach($location_list as $location) { ?>
					<div class="block-item active" data-activevalue="<?= $location ?>"><?= $location ?></div> 
				<?php } ?>
				<div class="block-item active" data-activevalue="**UNASSIGNED**">Unassigned Location</div>
			</div>
			<?php } ?>
            
			<?php if(count($contact_classifications) > 0 && strpos(",$dispatch_filters,", ",Classification,") !== FALSE) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_classifications" >
							<span style="display: inline-block; width: calc(100% - 6em);">Classifications</span><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_classifications" class="panel-collapse collapse">
					<div class="panel-body panel-body-height" style="overflow-y: auto; padding: 0; height: auto;"><?php $active_classifications = array_filter(explode(',',get_user_settings()['appt_calendar_classifications']));
					foreach($contact_classifications as $i => $classification) {
						echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); return false;'><div class='block-item ".(in_array($classification,$active_classifications) ? 'active' : '')."' data-regions='".json_encode($classification_regions[$i])."' data-classification='".$classification."' data-activevalue='".$classification."'>".getClassificationLogo($dbc, $classification, $classification_logos[$i]).$classification."<span class='id-circle active_user_count pull-right' style='background-color: #00ff00; font-family: \"Open Sans\"; display: none;' onmouseover='displayActiveUsers(this);' onmouseout='hideActiveUsers();'>0</span></div></a>";
					}
					echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); return false;'><div class='block-item ".(in_array('**UNASSIGNED**',$active_classifications) ? 'active' : '')."' data-regions='[]' data-classification='**UNASSIGNED**' data-activevalue='**UNASSIGNED**'><span data-classification='**UNASSIGNED**' class='id-circle' style='background-color: #6DCFF6; font-family: \"Open Sans\";'>UC</span>Unassigned Classification</div></a>"; ?>
					</div>
				</div>
			</div>
			<div class="active_blocks_classifications active_blocks" data-accordion="collapse_classifications" style="display: none;">
				<?php foreach($contact_classifications as $classification) { ?>
					<div class="block-item active" data-activevalue="<?= $classification ?>"><?= $classification ?></div> 
				<?php } ?>
				<div class="block-item active" data-activevalue="**UNASSIGNED**">Unassigned Classification</div>
			</div>
			<?php } ?>
        
            <?php if(!empty($client_type)) { ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_customers" >
								<span style="display: inline-block; width: calc(100% - 6em);"><?= $client_type ?></span><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_customers" class="panel-collapse collapse">
						<div class="panel-body" style="overflow-y: auto; padding: 0;">
							<?php $active_clients = array_filter(explode(',',get_user_settings()['appt_calendar_clients']));
							$client_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 AND `category` = '".$client_type."'".$region_query),MYSQLI_ASSOC));
							foreach($client_list as $clientid) {
								echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); return false;'><div class='block-item ".(in_array($clientid,$active_clients) ? 'active' : '')."' data-client='".$clientid."' data-region='".get_contact($dbc, $clientid, 'region')."' data-classification='".get_contact($dbc, $clientid, 'classification')."' data-location='".get_contact($dbc, $clientid, 'location')."' data-activevalue='".$clientid."'>".(!empty(get_client($dbc, $clientid)) ? get_client($dbc, $clientid) : get_contact($dbc, $clientid))."</div></a>";
							} ?>
						</div>
					</div>
				</div>
				<div class="active_blocks_customers active_blocks" data-accordion="collapse_customers" style="display: none;">
					<?php foreach($client_list as $clientid) { ?>
						<div class="block-item active" data-activevalue="<?= $clientid ?>"><?= (!empty(get_client($dbc, $clientid)) ? get_client($dbc, $clientid) : get_contact($dbc, $clientid)) ?></div> 
					<?php } ?>
				</div>
			<?php } ?>
			<?php $collapse_in = 'in';
			foreach($equipment_categories as $equipment_category) { ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_equipment_<?= config_safe_str($equipment_category) ?>" >
								<span style="display: inline-block; width: calc(100% - 6em);"><?= $equipment_category ?></span><span class="glyphicon glyphicon-minus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_equipment_<?= config_safe_str($equipment_category) ?>" class="panel-collapse collapse <?php echo $collapse_in; $collapse_in = ''; ?>">
						<div class="panel-body" style="overflow-y: auto; padding: 0;">
							<?php $equip_options = explode(',',get_config($dbc,'equip_options'));
							$active_equipment = array_filter(explode(',',get_user_settings()['appt_calendar_equipment']));
							$equip_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT *, CONCAT(' #', `unit_number`) label FROM `equipment` WHERE `deleted`=0 AND `category` = '$equipment_category' $allowed_equipment_query ORDER BY ".(in_array('region_sort',$equip_options) ? "IFNULL(NULLIF(`region`,''),'ZZZ'), " : '')."`label`"),MYSQLI_ASSOC);
		                    $region = false;
		                    $region_list = explode(',',get_config($dbc, '%_region', true));
		                    $region_colours = explode(',',get_config($dbc, '%_region_colour', true));
							foreach($equip_list as $equipment) {
								$equip_regions = $equipment['region'];
								$equip_locations = $equipment['location'];
								$equip_classifications = $equipment['classification'];
								
								$equip_regions = implode('*#*', array_filter(array_unique(explode('*#*', $equip_regions))));
								$equip_locations = implode('*#*', array_filter(array_unique(explode('*#*', $equip_locations))));
								$equip_classifications = implode('*#*', array_filter(array_unique(explode('*#*', $equip_classifications))));

								$classification_label = '';
								if($equip_display_classification_ticket == 1) {
									$equip_classifications = getEquipmentTicketClassification($dbc, $equipment['equipmentid'], $date_month_start, $date_month_end);
									if(!empty($equip_classifications)) {
										$classification_label = ' - '.implode(', ', $equip_classifications);
									}
								} else if($equip_display_classification == 1 && !empty($equip_classifications)) {
									$classification_label = ' - '.str_replace('*#*', ', ', $equip_classifications);
								}
		                        if(in_array('region_sort',$equip_options) && $region != $equipment['region']) {
		                            $region = $equipment['region'];
		                            $region_colour = '';
		                            if($region == '') {
		                                $region_label = 'No Region';
		                            } else {
		                                $region_label = implode(', ',explode('*#*',$region));
		                                $region_key = array_search($region, $region_list);
		                                if($region_key !== false) {
		                                    $region_colour = 'background-color:'.$region_colours[$region_key].';';
		                                }
		                            }
		                            echo '<div class="block-item small" style="'.$region_colour.'" data-region="'.$region.'">'.$region_label.'</div>';
		                        }
								echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(\"\"); return false;'><div class='block-item ".(in_array($equipment['equipmentid'],$active_equipment) ? 'active' : '')."' data-equipment='".$equipment['equipmentid']."' data-client='".$clientids."' data-region='".$equip_regions."' data-classification='".$equip_classifications."' data-location='".$equip_locations."' data-activevalue='".$equipment['equipmentid']."'>".$equipment['label'].$classification_label."</div></a>";
							} ?>
						</div>
					</div>
				</div>
				<div class="active_blocks_equipment active_blocks" data-accordion="collapse_equipment_<?= config_safe_str($equipment_category) ?>" style="display: none;">
					<?php foreach($equip_list as $equipment) { ?>
						<div class="block-item active" data-activevalue="<?= $equipment['equipmentid'] ?>"><?= $equipment['label'] ?></div> 
					<?php } ?>
				</div>
			<?php } ?>

			<?php if($allowed_dispatch_staff > 0 && $_GET['mode'] != 'contractors') { ?>
				<?php if($staff_split_security != 1) { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_staff" >
									<span style="display: inline-block; width: calc(100% - 6em);">Staff</span><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_staff" class="panel-collapse collapse <?= $_GET['mode'] == 'staff' ? 'in' : '' ?>">
							<div class="panel-body" style="overflow-y: auto; padding: 0;">
							<?php $active_staff = array_filter(explode(',',get_user_settings()['appt_calendar_staff']));
							$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"));
							$contact_category = !empty($get_field_config) ? explode(',', $get_field_config['contact_category']) : '';
							$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".("'".implode("','",$contact_category)."'").") AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query.$allowed_roles_query));
							foreach ($staff_list as $staff_row) {
								$staff_id = $staff_row['contactid'];
								$staff_equipassigns = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT ea.`equipment_assignmentid` SEPARATOR ',') as ea_list, GROUP_CONCAT(DISTINCT ea.`equipmentid` SEPARATOR ',') as eq_list FROM `equipment_assignment` ea LEFT JOIN `equipment_assignment_staff` eas ON ea.`equipment_assignmentid` = eas.`equipment_assignmentid` WHERE ea.`deleted` = 0 AND (DATE(`start_date`) BETWEEN '$date_month_start' AND '$date_month_end' OR DATE(`end_date`) BETWEEN '$date_month_start' AND '$date_month_end') AND ((eas.`contactid` = '$staff_id' AND eas.`deleted` = 0) $teams_query) AND CONCAT(',',ea.`hide_staff`,',') NOT LIKE '%,$staff_id,%'"));
								echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(\"staff\"); return false;'><div class='block-item ".(in_array($staff_id,$active_staff) ? 'active' : '')."' data-staff='$staff_id' data-category='".get_contact($dbc, $staff_id, 'category_contact')."' data-region='".get_contact($dbc, $staff_id, 'region')."' data-classification='".get_contact($dbc, $staff_id, 'classification')."' data-location='".get_contact($dbc, $staff_id, 'location')."' data-equipassign='".$staff_equipassigns['ea_list']."' data-equipment='".$staff_equipassigns['eq_list']."' data-activevalue='".$staff_id."'>";
								profile_id($dbc, $staff_id);
								echo $staff_row['full_name']."</div></a>";
							}

							?>
							</div>
						</div>
					</div>
					<div class="active_blocks_staff active_blocks" data-accordion="collapse_staff" style="display: none;">
						<?php foreach($staff_list as $staff_row) { ?>
							<div class="block-item active" data-activevalue="<?= $staff_row['contactid'] ?>"><?= $staff_row['full_name'] ?></div> 
						<?php } ?>
					</div>
				<?php } else {
					$collapse_in = $_GET['mode'] == 'staff' ? 'in' : '';
					foreach(get_security_levels($dbc) as $security_label => $security_level) {
						if(empty($allowed_roles) || in_array($security_level, $allowed_roles)) { ?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_staff_<?= config_safe_str($security_level) ?>" >
											<span style="display: inline-block; width: calc(100% - 6em);"><?= $security_label ?></span><span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_staff_<?= config_safe_str($security_level) ?>" class="panel-collapse collapse <?php echo $collapse_in; $collapse_in = ''; ?>">
									<div class="panel-body" style="overflow-y: auto; padding: 0;">
									<?php $active_staff = array_filter(explode(',',get_user_settings()['appt_calendar_staff']));
									$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"));
									$contact_category = !empty($get_field_config) ? explode(',', $get_field_config['contact_category']) : '';
									$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".("'".implode("','",$contact_category)."'").") AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1 AND CONCAT(',',`role`,',') LIKE '%,$security_level,%'".$region_query.$allowed_roles_query));
									foreach ($staff_list as $staff_row) {
										$staff_id = $staff_row['contactid'];
										$staff_equipassigns = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT ea.`equipment_assignmentid` SEPARATOR ',') as ea_list, GROUP_CONCAT(DISTINCT ea.`equipmentid` SEPARATOR ',') as eq_list FROM `equipment_assignment` ea LEFT JOIN `equipment_assignment_staff` eas ON ea.`equipment_assignmentid` = eas.`equipment_assignmentid` WHERE ea.`deleted` = 0 AND (DATE(`start_date`) BETWEEN '$date_month_start' AND '$date_month_end' OR DATE(`end_date`) BETWEEN '$date_month_start' AND '$date_month_end') AND ((eas.`contactid` = '$staff_id' AND eas.`deleted` = 0) $teams_query) AND CONCAT(',',ea.`hide_staff`,',') NOT LIKE '%,$staff_id,%'"));
										echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(\"staff\"); return false;'><div class='block-item ".(in_array($staff_id,$active_staff) ? 'active' : '')."' data-staff='$staff_id' data-category='".get_contact($dbc, $staff_id, 'category_contact')."' data-region='".get_contact($dbc, $staff_id, 'region')."' data-classification='".get_contact($dbc, $staff_id, 'classification')."' data-location='".get_contact($dbc, $staff_id, 'location')."' data-equipassign='".$staff_equipassigns['ea_list']."' data-equipment='".$staff_equipassigns['eq_list']."' data-activevalue='".$staff_id."'>";
										profile_id($dbc, $staff_id);
										echo $staff_row['full_name']."</div></a>";
									}

									?>
									</div>
								</div>
							</div>
							<div class="active_blocks_staff active_blocks" data-accordion="collapse_staff_<?= config_safe_str($security_level) ?>" style="display: none;">
								<?php foreach($staff_list as $staff_row) { ?>
									<div class="block-item active" data-activevalue="<?= $staff_row['contactid'] ?>"><?= $staff_row['full_name'] ?></div> 
								<?php } ?>
							</div>
						<?php }
					}
				} ?>
			<?php } ?>
			<?php if($allowed_dispatch_contractors > 0 && !empty($contractor_category) && $_GET['mode'] != 'staff') { ?>
				<?php if($contractor_split_security != 1) { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_contractors" >
									<span style="display: inline-block; width: calc(100% - 6em);">Contractors</span><span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>
            
						<div id="collapse_contractors" class="panel-collapse collapse <?= $_GET['mode'] == 'contractors' ? 'in' : '' ?>">
							<div class="panel-body" style="overflow-y: auto; padding: 0;">
							<?php $active_staff = array_filter(explode(',',get_user_settings()['appt_calendar_staff']));
							$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"));
							$contractor_category = !empty($get_field_config['contractor_category']) ? explode(',', $get_field_config['contractor_category']) : '';
							$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `name`, `category`, `category_contact`, `region`, `classification`, `con_locations` FROM `contacts` WHERE `category` IN (".("'".implode("','",$contractor_category)."'").") AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query.$allowed_roles_query));
							foreach ($staff_list as $staff_row) {
		                        $staff_id = $staff_row['contactid'];
								$staff_equipassigns = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT ea.`equipment_assignmentid` SEPARATOR ',') as ea_list, GROUP_CONCAT(DISTINCT ea.`equipmentid` SEPARATOR ',') as eq_list FROM `equipment_assignment` ea LEFT JOIN `equipment_assignment_staff` eas ON ea.`equipment_assignmentid` = eas.`equipment_assignmentid` WHERE ea.`deleted` = 0 AND (DATE(`start_date`) BETWEEN '$date_month_start' AND '$date_month_end' OR DATE(`end_date`) BETWEEN '$date_month_start' AND '$date_month_end') AND ((eas.`contactid` = '$staff_id' AND eas.`deleted` = 0) $teams_query) AND CONCAT(',',ea.`hide_staff`,',') NOT LIKE '%,$staff_id,%'"));
								echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(\"staff\"); return false;'><div class='block-item ".(in_array($staff_id,$active_staff) ? 'active' : '')."' data-staff='$staff_id' data-category='".$staff_row['category_contact']."' data-region='".$staff_row['region']."' data-classification='".$staff_row['classification']."' data-location='".$staff_row['con_locations']."' data-equipassign='".$staff_equipassigns['ea_list']."' data-equipment='".$staff_equipassigns['eq_list']."' data-contractor='1' data-activevalue='".$staff_id."'>";
								profile_id($dbc, $staff_id);
								echo $staff_row['full_name']."</div></a>";
							}

							?>
							</div>
						</div>
					</div>
					<div class="active_blocks_contractors active_blocks" data-accordion="collapse_contractors" style="display: none;">
						<?php foreach($staff_list as $staff_row) { ?>
							<div class="block-item active" data-activevalue="<?= $staff_row['contactid'] ?>"><?= $staff_row['full_name'] ?></div> 
						<?php } ?>
					</div>
				<?php } else {
					$collapse_in = $_GET['mode'] == 'contractors' ? 'in' : '';
					foreach(get_security_levels($dbc) as $security_label => $security_level) {
						if(empty($allowed_roles) || in_array($security_level, $allowed_roles)) { ?>
							<div class="panel panel-default">
								<div class="panel-heading">
									<h4 class="panel-title">
										<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_contractors_<?= config_safe_str($security_level) ?>" >
											<span style="display: inline-block; width: calc(100% - 6em);">Contractors - <?= $security_label ?></span><span class="glyphicon glyphicon-plus"></span>
										</a>
									</h4>
								</div>

								<div id="collapse_contractors_<?= config_safe_str($security_level) ?>" class="panel-collapse collapse <?= $_GET['mode'] == 'contractors' ? 'in' : '' ?>">
									<div class="panel-body" style="overflow-y: auto; padding: 0;">
									<?php $active_staff = array_filter(explode(',',get_user_settings()['appt_calendar_staff']));
									$get_field_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_equip_assign`"));
									$contractor_category = !empty($get_field_config['contractor_category']) ? explode(',', $get_field_config['contractor_category']) : '';
									$staff_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `name`, `category`, `category_contact`, `region`, `classification`, `con_locations` FROM `contacts` WHERE `category` IN (".("'".implode("','",$contractor_category)."'").") AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1 AND CONCAT(',',`role`,',') LIKE '%,$security_level,%'".$region_query.$allowed_roles_query));
									foreach ($staff_list as $staff_row) {
				                        $staff_id = $staff_row['contactid'];
										$staff_equipassigns = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT ea.`equipment_assignmentid` SEPARATOR ',') as ea_list, GROUP_CONCAT(DISTINCT ea.`equipmentid` SEPARATOR ',') as eq_list FROM `equipment_assignment` ea LEFT JOIN `equipment_assignment_staff` eas ON ea.`equipment_assignmentid` = eas.`equipment_assignmentid` WHERE ea.`deleted` = 0 AND (DATE(`start_date`) BETWEEN '$date_month_start' AND '$date_month_end' OR DATE(`end_date`) BETWEEN '$date_month_start' AND '$date_month_end') AND ((eas.`contactid` = '$staff_id' AND eas.`deleted` = 0) $teams_query) AND CONCAT(',',ea.`hide_staff`,',') NOT LIKE '%,$staff_id,%'"));
										echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(\"staff\"); return false;'><div class='block-item ".(in_array($staff_id,$active_staff) ? 'active' : '')."' data-staff='$staff_id' data-category='".$staff_row['category_contact']."' data-region='".$staff_row['region']."' data-classification='".$staff_row['classification']."' data-location='".$staff_row['con_locations']."' data-equipassign='".$staff_equipassigns['ea_list']."' data-equipment='".$staff_equipassigns['eq_list']."' data-contractor='1' data-activevalue='".$staff_id."'>";
										profile_id($dbc, $staff_id);
										echo $staff_row['full_name']."</div></a>";
									}

									?>
									</div>
								</div>
							</div>
							<div class="active_blocks_contractors active_blocks" data-accordion="collapse_contractors" style="display: none;">
								<?php foreach($staff_list as $staff_row) { ?>
									<div class="block-item active" data-activevalue="<?= $staff_row['contactid'] ?>"><?= $staff_row['full_name'] ?></div> 
								<?php } ?>
							</div>
						<?php }
					}
				} ?>
			<?php } ?>
			<?php if(get_config($dbc, 'scheduling_teams') !== '' && $allowed_dispatch_team > 0) { ?>
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_teams" >
							<span style="display: inline-block; width: calc(100% - 6em);">Teams</span><span class="glyphicon glyphicon-plus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_teams" class="panel-collapse collapse">
					<div class="panel-body" style="overflow-y: auto; padding: 0;">
						<?php 
						$team_list = mysqli_query($dbc, "SELECT * FROM `teams` WHERE `deleted` = 0 AND (DATE(`start_date`) <= DATE(CURDATE()) OR `start_date` IS NULL OR `start_date` = '' OR `start_date` = '0000-00-00') AND (DATE(`end_date`) >= DATE(CURDATE()) OR `end_date` IS NULL OR `end_date` = '' OR `end_date` = '0000-00-00')".$region_query);
						$active_teams = array_filter(explode(',',get_user_settings()['appt_calendar_teams']));
						while($row = mysqli_fetch_array($team_list)) {
							$team_contactids = [];
                            $team_name = get_team_name($dbc, $row['teamid'], '<br />');
                            $team_contacts = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` ='".$row['teamid']."' AND `deleted` = 0"),MYSQLI_ASSOC);
                            foreach ($team_contacts as $team_contact) {
                            	if (get_contact($dbc, $team_contact['contactid'], 'category') == 'Staff') {
                            		$team_contactids[] = $team_contact['contactid'];
                            	}
                            }
                            $team_contactids = implode(',', $team_contactids);
		                    $team_equipassigns = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT ea.`equipment_assignmentid` SEPARATOR ',') as ea_list, GROUP_CONCAT(DISTINCT ea.`equipmentid` SEPARATOR ',') as eq_list FROM `equipment_assignment` ea LEFT JOIN `equipment_assignment_staff` eas ON ea.`equipment_assignmentid` = eas.`equipment_assignmentid` WHERE ea.`deleted` = 0 AND (DATE(`start_date`) BETWEEN '$date_month_start' AND '$date_month_end' OR DATE(`end_date`) BETWEEN '$date_month_start' AND '$date_month_end') AND ((',$team_contactids,' LIKE CONCAT('%,',eas.`contactid`,',%') AND eas.`deleted` = 0) OR `teamid` = '".$row['teamid']."')"));
							// $team_equipassigns = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `equipment_assignmentid` SEPARATOR ',') as ea_list FROM `equipment_assignment` WHERE `deleted` = 0 AND (DATE(`start_date`) BETWEEN '$date_month_start' AND '$date_month_end' OR DATE(`end_date`) BETWEEN '$date_month_start' AND '$date_month_end') AND `teamid` = '".$row['teamid']."'"))['ea_list'];
							echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(\"team\"); return false;'><div class='block-item ".(in_array($row['teamid'],$active_teams) ? 'active' : '')."' data-teamid='".$row['teamid']."' data-contactids='".$team_contactids."' data-region='".$row['region']."' data-location='".$row['location']."' data-classification='".$row['classification']."' data-equipassign='".$team_equipassigns['ea_list']."' data-equipment='".$team_equipassigns['eq_list']."' data-activevalue='".$row['teamid']."'><span style=''>$team_name</span></div></a>";
						} ?>
					</div>
				</div>
			</div>
			<div class="active_blocks_teams active_blocks" data-accordion="collapse_teams" style="display: none;">
				<?php $team_list = mysqli_query($dbc, "SELECT * FROM `teams` WHERE `deleted` = 0 AND (DATE(`start_date`) <= DATE(CURDATE()) OR `start_date` IS NULL OR `start_date` = '' OR `start_date` = '0000-00-00') AND (DATE(`end_date`) >= DATE(CURDATE()) OR `end_date` IS NULL OR `end_date` = '' OR `end_date` = '0000-00-00')".$region_query);
				$active_teams = array_filter(explode(',',get_user_settings()['appt_calendar_teams']));
				while($row = mysqli_fetch_array($team_list)) { ?>
					<div class="block-item active" data-activevalue="<?= $row['teamid'] ?>"><?= get_team_name($dbc, $row['teamid']) ?></div> 
				<?php } ?>
			</div>
			<?php } ?>
		</div>
		<div class="block-item"><img src="../img/icons/clock-button.png" style="height: 1em; margin-right: 1em;">Break</div>
	</div>
	<?php
	$search_month = date('F');
	$search_year = date('Y');
	if(isset($_GET['date'])) {
		$search_month = date('F', strtotime($_GET['date']));
		$search_year = date('Y', strtotime($_GET['date']));
	}
	$calendar_month = date("n", strtotime($search_month));
	$calendar_year = $search_year;

	$page_query = $_GET; ?>

	<?php if($_GET['teamid']): ?>
		<div class="scalable pull-right unbooked_view" style="height: 30em; overflow: auto; <?= $scale_style ?>">
			<?php include('teams.php'); ?>
		</div>
	<?php endif; ?>

	<?php if($_GET['equipment_assignmentid']): ?>
		<div class="scalable pull-right unbooked_view" style="height: 30em; overflow: auto; <?= $scale_style ?>">
			<?php include('equip_assign.php'); ?>
		</div>
	<?php endif; ?>

	<?php if($_GET['shiftid']): ?>
		<div class="scalable pull-right unbooked_view" style="height: 30em; overflow: auto; <?= $scale_style ?>">
			<?php include('shifts.php'); ?>
		</div>
	<?php endif; ?>

	<?php if($_GET['bookingid']): ?>
		<div class="scalable pull-right unbooked_view" style="height: 30em; overflow: auto; <?= $scale_style ?>">
			<?php include('booking.php'); ?>
		</div>
	<?php endif; ?>

	<?php if($_GET['add_reminder']): ?>
		<div class="scalable pull-right unbooked_view" style="height: 30em; overflow: auto; <?= $scale_style ?>">
			<?php include('add_reminder.php'); ?>
		</div>
	<?php endif; ?>

	<?php if ($_GET['region']) {
		$region_url = '&region='.$_GET['region'];
	}
	?>
	<div class="scale-to-fill">
		<div class="col-sm-12 calendar_view" style="position: relative; left: -1px; background-color: #fff; margin: 0 0 0.4em 0; padding: 0; overflow: auto;">
			<?php include('monthly_display.php'); ?>
		</div>
		<div class="loading_overlay" style="display: none;"><div class="loading_wheel"></div></div>

		<a href="" onclick="changeDate('', 'prev'); return false;"><div class="block-button" style="margin: 0;"><img src="../img/icons/back-arrow.png" style="height: 1em;">&nbsp;</div></a>
		<div class="block-button"><?= $_GET['view'] == 'monthly' ? 'Month' : ($_GET['view'] == 'weekly' ? 'Week' : 'Day') ?></div>
		<a href="" onclick="changeDate('', 'next'); return false;"><div class="block-button">&nbsp;<img src="../img/icons/next-arrow.png" style="height: 1em;"></div></a>
		<a href="?type=schedule&view=daily&mode=<?= $_GET['mode'] ?>"><div class="block-button <?= $_GET['view'] == 'daily' ? 'active blue' : '' ?>" style="margin-left: 1em;">Day</div></a>
		<a href="?type=schedule&view=weekly&mode=<?= $_GET['mode'] ?>"><div class="block-button <?= $_GET['view'] == 'weekly' ? 'active blue' : '' ?>">Week</div></a>
		<a href="?type=schedule&view=monthly&mode=<?= $_GET['mode'] ?>"><div class="block-button <?= $_GET['view'] == 'monthly' ? 'active blue' : '' ?>">Month</div></a>
		<div class="block-button selected_class_logos" style="margin-left: 1em; padding: 0 0.5em 0 0.5em; display: table; display: none;">
			<div style="display: table-cell; vertical-align: middle;">
				Selected Classifications: 
			</div>
			<?php foreach($contact_classifications as $i => $classification) {
				echo getClassificationLogo($dbc, $classification, $classification_logos[$i]);
			} echo "<span data-classification='**UNASSIGNED**' class='id-circle' style='background-color: #6DCFF6; font-family: \"Open Sans\";'>UC</span>"; ?>
		</div>
		<a href="" onclick="$('.set_date').focus(); return false;"><div class="block-button pull-right"><img src="../img/icons/calendar-button.png" style="height: 1em; margin-right: 1em;">Go To Date</div></a>
		<?php unset($page_query['date']); ?>
		<a href="" onclick="changeDate('<?= date('Y-m-d') ?>'); return false;"><div class="block-button pull-right">Today</div></a>
		<input value="<?= $calendar_start ?>" type="text" style="border: 0; width: 0;" class="pull-right datepicker set_date" onchange="changeDate(this.value);">
		<?php $page_query['date'] = $_GET['date']; ?>
	</div>
</div>