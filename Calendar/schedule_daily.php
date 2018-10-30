<script src="appointments.js"></script>
<script src="https://maps.googleapis.com/maps/api/js?key=<?= DIRECTIONS_KEY ?>"></script>
<script src="map_sorting.js"></script>
<script>
var geocoder_key = '<?= GEOCODER_KEY ?>';
var page_mode = '<?= $_GET['mode'] ?>';
$(document).ready(function() {
	global_type = '<?= $_GET['mode'] == 'staff' || $_GET['mode'] == 'contractors' ? 'staff': '' ?>';
	//$(window).resize(resize_calendar_view).resize();

	if($('#collapse_region .block-item.active').length > 1) {
		$('#collapse_region .block-item.active:not(:first)').removeClass('active');
	}
	<?php if($multi_class_admin == 1 && strpos(','.ROLE.',', ',admin,') === FALSE && strpos(','.ROLE.',', ',super,') === FALSE) { ?>
		if($('#collapse_classifications .block-item.active').length > 1) {
			$('#collapse_classifications .block-item.active:not(:first)').removeClass('active');
		}
	<?php } ?>

	toggle_columns(global_type);
	<?php if($_GET['mode'] == 'staff') { ?>
		$('[id^=collapse_staff]').find('.block-item.active').each(function() {
			retrieve_items($(this).closest('a'));
		});
	<?php } else { ?>
		$('[id^=collapse_equipment]').find('.block-item.active').each(function() {
			retrieve_items($(this).closest('a'));
		});
	<?php } ?>

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
	if(global_type != type) {
		clear_all_data();
	}
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
    var ticket_types = [];
	var regions = [];
    var classifications = [];
    var locations = [];
	var clients = [];
	var teams = [];
	var staff = [];
	var contractors = [];
	var ea_visibles = [];
	
    // Filter Ticket Types
    $('#collapse_ticket_type').find('.block-item.active').each(function() {
        var ticket_type = $(this).data('tickettype');
        ticket_types.push(ticket_type);
    });
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
		var equipment_clientid = $(this).data('client');
        if(equipment_clientid != undefined) {
            equipment_clientid = equipment_clientid.toString().split(',');
        }
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

		var has_client = 0;
		if (clients.length > 0) {
			clients.forEach(function(clientid) {
				if(clientid > 0) {
					if(equipment_clientid.indexOf(clientid.toString()) != -1) {
						has_client = 1;
					}
				}
			});
		} else {
			has_client = 1;
		}
		if (!region_pass || !location_pass || !classification_pass || has_client == 0) {
			$(this).hide();
			$(this).removeClass('active');
		} else {
			$(this).show();
		}
	});
	// Hide teams that are not attached to selected regions/classifications/location
	$('#collapse_teams').find('.block-item').each(function() {
		var region_pass = true;
		var location_pass = true;
		var classification_pass = true;
		var this_regions = $(this).data('region').toString().split(',');
		var this_classifications = $(this).data('classification').toString().split(',');
		var this_locations = $(this).data('location').toString().split(',');

		<?php if(strpos(",$scheduling_item_filters,",",Region,") !== FALSE) { ?>
			if(regions.length > 0) {
				region_pass = false;
				this_regions.forEach(function(this_region) {
					if(regions.indexOf(this_region) > -1) {
						region_pass = true;
					}
				});
			}
		<?php } ?>

		<?php if(strpos(",$scheduling_item_filters,",",Location,") !== FALSE) { ?>
			if(locations.length > 0) {
				location_pass = false;
				this_locations.forEach(function(this_location) {
					if(locations.indexOf(this_location) > -1) {
						location_pass = true;
					}
				});
			}
		<?php } ?>

		<?php if(strpos(",$scheduling_item_filters,",",Classification,") !== FALSE) { ?>
			if(classifications.length > 0) {
				classification_pass = false;
				this_classifications.forEach(function(this_classification) {
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
	// Filter selected teams
	$('#collapse_teams').find('.block-item.active').each(function() {
		var teamid = $(this).data('teamid');
		teams.push(parseInt(teamid));
		<?php if ($_GET['mode'] == 'staff') { ?>
			var contactids = $(this).data('contactids').split(',');
			contactids.forEach(function (contact_id) {
				if(contact_id > 0) {
					if($('[id^=collapse_staff]').find('.block-item[data-staff='+contact_id+']').length > 0) {
						var block = $('[id^=collapse_staff]').find('.block-item[data-staff='+contact_id+']');
						if($(block).css('display') != 'none') {
							block.addClass('active');
							retrieve_items($(block).closest('a'));
						}
					}
					if($('[id^=collapse_contractors]').find('.block-item[data-staff='+contact_id+']').length > 0) {
						var block = $('[id^=collapse_contractors]').find('.block-item[data-staff='+contact_id+']');
						if($(block).css('display') != 'none') {
							block.addClass('active');
							retrieve_items($(block).closest('a'));
						}
					}
					if(staff.indexOf(parseInt(contact_id)) == -1) {
						staff.push(parseInt(contact_id));
					}
					if(contractors.indexOf(parseInt(contact_id)) == -1) {
						contractors.push(parseInt(contact_id));
					}
				}
			});
		<?php } else { ?>
			if(type == 'team') {
				var equipmentids = $(this).data('equipment').toString().split(',');
				equipmentids.forEach(function(equipmentid) {
					if(equipmentid > 0 && $('[id^=collapse_equipment]').find('.block-item[data-equipment='+equipmentid+']').length > 0) {
						var block = $('[id^=collapse_equipment]').find('.block-item[data-equipment='+equipmentid+']');
						if($(block).css('display') != 'none') {
							block.addClass('active');
							retrieve_items($(block).closest('a'));	
						}
					}
				});
				// var equip_assign = $(this).data('equipassign').toString().split(',');
				// equip_assign.forEach(function (equipassign_id) {
				// 	if(equipassign_id > 0) {
				// 		if(ea_visibles.indexOf(parseInt(equipassign_id)) == -1) {
				// 			ea_visibles.push(parseInt(equipassign_id));
				// 		}
				// 	}
				// });
			}
		<?php } ?>

		//Active blocks
		$('.active_blocks_teams .block-item').filter(function() { return $(this).data('teamid') == teamid; }).show();
	});
	// Hide staff that are not attached to selected regions/classifications/location
	$('[id^=collapse_staff],[id^=collapse_contractors]').find('.block-item').each(function() {
		var region_pass = true;
		var location_pass = true;
		var classification_pass = true;
		var this_regions = $(this).data('region').toString().split(',');
		var this_classifications = $(this).data('classification').toString().split(',');
		var this_locations = $(this).data('location').toString().split(',');

		<?php if(strpos(",$scheduling_item_filters,",",Region,") !== FALSE) { ?>
			if(regions.length > 0) {
				region_pass = false;
				this_regions.forEach(function(this_region) {
					if(regions.indexOf(this_region) > -1) {
						region_pass = true;
					}
				});
			}
		<?php } ?>

		<?php if(strpos(",$scheduling_item_filters,",",Location,") !== FALSE) { ?>
			if(locations.length > 0) {
				location_pass = false;
				this_locations.forEach(function(this_location) {
					if(locations.indexOf(this_location) > -1) {
						location_pass = true;
					}
				});
			}
		<?php } ?>

		<?php if(strpos(",$scheduling_item_filters,",",Classification,") !== FALSE) { ?>
			if(classifications.length > 0) {
				classification_pass = false;
				this_classifications.forEach(function(this_classification) {
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
    
	// Filter selected staff
	$('[id^=collapse_staff],[id^=collapse_contractors]').find('.block-item.active').each(function() {
		var staffid = $(this).data('staff');
		staff.push(parseInt(staffid));
		<?php if ($_GET['mode'] != 'staff' && $_GET['mode'] != 'contractors') { ?>
			if(type == 'staff') {
				var equipmentids = $(this).data('equipment').toString().split(',');
				equipmentids.forEach(function(equipmentid) {
					if(equipmentid > 0 && $('[id^=collapse_equipment]').find('.block-item[data-equipment='+equipmentid+']').length > 0) {
						var block = $('[id^=collapse_equipment]').find('.block-item[data-equipment='+equipmentid+']');
						if($(block).css('display') != 'none') {
							block.addClass('active');
							retrieve_items($(block).closest('a'));	
						}
					}
				});
				// var equip_assign = $(this).data('equipassign').toString().split(',');
				// equip_assign.forEach(function (equipassign_id) {
				// 	if(equipassign_id > 0) {
				// 		if(ea_visibles.indexOf(parseInt(equipassign_id)) == -1) {
				// 			ea_visibles.push(parseInt(equipassign_id));
				// 		}
				// 	}
				// });
			}
		<?php } ?>

		//Active blocks
		$('.active_blocks_staff .block-item,.active_blocks_contractors .block-item').filter(function() { return $(this).data('staff') == staffid; }).show();
	});

	// Show selected equipment on calendar view
	$('[id^=collapse_equipment]').find('.block-item.active').each(function() {
		var equipment_id = $(this).data('equipment');
		if (equipment_id > 0) {
			visibles.push(equipment_id);
		}
	});

    // Filter tickets in Calendar view based on the selected client
    $('div.used-block').each(function() {
        var ticket_type = $(this).data('tickettype');
        if (ticket_types.indexOf(ticket_type) == -1 && ticket_types.length > 0) {
            $(this).hide();
        } else {
            $(this).show();
        }
    });

	<?php if ($_GET['mode'] == 'staff' || $_GET['mode'] == 'contractors') { ?>
		staff.forEach(function (contact_id) {
			visibles.push(contact_id);
		});
	<?php } ?>
	
	<?php if ($_GET['mode'] == 'staff' || $_GET['mode'] == 'contractors') { ?>
		// Save which equipment are active
		$.ajax({
			url: 'calendar_ajax_all.php?fill=selected_contacts&offline='+offline_mode,
			method: 'POST',
			data: { category: 'equipment', teams: teams, staff: staff, clients: clients, regions: regions, classifications: classifications, locations: locations, tickettype_list: ticket_types },
			success: function(response) {
			}
		});
	<?php } else { ?>
		// Save which equipment are active
		$.ajax({
			url: 'calendar_ajax_all.php?fill=selected_contacts&offline='+offline_mode,
			method: 'POST',
			data: { contacts: visibles, category: 'equipment', clients: clients, regions: regions, classifications: classifications, locations: locations, tickettype_list: ticket_types },
			success: function(response) {
			}
		});
	<?php } ?>

	$('.calendar_view table td, .calendar_view table th').filter(function() { return $(this).data('contact') > 0; }).hide();

	// if(type == 'staff' || type == 'team') {
	// 	ea_visibles.forEach(function (equipassign_id) {
	// 		$('.calendar_view table td, .calendar_view table th').filter(function() { return $(this).data('equipassign') == equipassign_id; }).show();
	// 	});
	// } else {
		// Show selected equipment on calendar view
		$('[id^=collapse_equipment]').find('.block-item.active').each(function() {
			var equipment_id = $(this).data('equipment');
			if (equipment_id > 0) {
				$('.calendar_view table td, .calendar_view table th').filter(function() { return $(this).data('contact') == equipment_id; }).show();

					//Active blocks
					$('.active_blocks_equipment .block-item').filter(function() { return $(this).data('equipment') == equipment_id; }).show();
			}
		});
	// }

	<?php if ($_GET['mode'] == 'staff' || $_GET['mode'] == 'contractors') { ?>
		staff.forEach(function (contact_id) {
			$('.calendar_view table td, .calendar_view table th').filter(function() { return $(this).data('contact') == contact_id; }).show();
		});
	<?php } ?>

	var num_columns = parseInt($('.table_bordered thead th:visible').length) - 1;
	
	// Specify the column width, if it's past min-width it will use that so this can just go down to 1%
	width = 100 / num_columns;
	$('.calendar_view table td, .calendar_view table th').filter(function() { return $(this).data('contact') > 0; }).css('width',width+'%');
	$('.calendar-column-notes').each(function() { $(this).css('margin-bottom','calc(0.5em + '+this.offsetTop+'px)'); });
	$('.calendar_view table tbody tr').first().find('td').css('padding-top',$('.calendar_view table thead tr').outerHeight() + 8);
	resize_calendar_view();

	displayActiveBlocksAuto();
}
</script>
<div class="calendar-screen set-height">
	<div class="pull-left collapsible">
		<?php include('../Calendar/schedule_sidebar.php'); ?>
	</div>
	<?php if($_GET['unbooked']): ?>
		<div class="pull-right scalable unbooked_view" style="height: 30em; overflow: auto; <?= $scale_style ?>">
			<?php include('unbooked.php'); ?>
		</div>
	<?php endif; ?>

	<?php if($_GET['teamid']): ?>
		<div class="pull-right scalable unbooked_view" style="height: 30em; overflow: auto; <?= $scale_style ?>">
			<?php include('teams.php'); ?>
		</div>
	<?php endif; ?>

	<?php if($_GET['equipment_assignmentid']): ?>
		<div class="pull-right scalable unbooked_view" style="height: 30em; overflow: auto; <?= $scale_style ?>">
			<?php include('equip_assign.php'); ?>
		</div>
	<?php endif; ?>
	<div class="scale-to-fill">
		<div class="col-sm-12 calendar_view" style="background-color: #fff; margin: 0 0 0.4em 0; padding: 0; overflow: auto;" onscroll="scrollHeader();">
			<?php include('load_calendar_empty.php'); ?>
		</div>
		<div class="loading_overlay" style="display: none;"><div class="loading_wheel"></div></div>
		
		<?php if ($_GET['region']) {
			$region_url = '&region='.$_GET['region'];
		} ?>
		<a href="" onclick="changeDate('', 'prev'); return false;"><div class="block-button" style="margin: 0;"><img src="../img/icons/back-arrow.png" style="height: 1em;">&nbsp;</div></a>
		<div class="block-button view_button_string">Day</div>
		<a href="" onclick="changeDate('', 'next'); return false;"><div class="block-button">&nbsp;<img src="../img/icons/next-arrow.png" style="height: 1em;"></div></a>
		<a href="" onclick="changeView('daily', this); return false;"><div class="block-button view_button active blue" style="margin-left: 1em;">Day</div></a>
		<a href="" onclick="changeView('weekly', this); return false;"><div class="block-button view_button">Week</div></a>
		<a href="?type=schedule&view=monthly&mode=<?= $_GET['mode'] ?><?= $region_url ?>"><div class="block-button">Month</div></a>
		<?php if($ticket_status_color_code_legend == 1 && $wait_list == 'ticket') { ?>
			<div class="block-button legend-block" style="position: relative;">
				<div class="block-button ticket-status-legend" style="display: none; width: 20em; position: absolute; bottom: 1em;"><?= $ticket_status_legend ?></div>
				<img src="../img/legend-icon.png">
			</div>
		<?php } ?>
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
	<div class="clearfix"></div>
</div>