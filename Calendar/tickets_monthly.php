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
	toggle_columns();
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
function toggle_columns(type = '', reload_teams = 0) {
    if($('#collapse_teams .block-item.active').length > 0) {
        $('[id^=collapse_staff] .block-item').removeClass('active');
    }
	// Hide deselected columns
	var visibles = [];
    var regions = [];
    var clients = [];
	var teams = [];
	var all_staff = [];
    // Filter selected regions
    $('#collapse_region').find('.block-item.active').each(function() {
        var region = $(this).data('region');
        regions.push(region);
    });
    // Hide clients that are not in selected regions
    $('[id^=collapse_clients]').find('.block-item').each(function() {
        var client_region = $(this).data('region');
        if (regions.indexOf(client_region) == -1 && regions.length > 0) {
            $(this).hide();
            $(this).removeClass('active');
        } else {
            $(this).show();
        }
    });
    // Filter selected clients
    $('[id^=collapse_clients]').find('.block-item.active').each(function() {
        var clientid = $(this).data('client');
        clients.push(parseInt(clientid));
    })
	$('#collapse_teams').find('.block-item.active').each(function() {
		var contactids = $(this).data('contactids').split(',');
		var teamid = $(this).data('teamid');
		teams.push(parseInt(teamid));
		if(type != 'noload') {
	        clear_all_data_month();
	        retrieve_items_month($('#collapse_teams').find('.block-item[data-teamid="'+teamid+'"]'), '', true, teamid);
		}
		contactids.forEach(function (contact_id) {
			if(contact_id > 0) {
				if(all_staff.indexOf(parseInt(contact_id)) == -1) {
					all_staff.push(parseInt(contact_id));
                    var staff_block = $('[id^=collapse_staff]').find('.block-item[data-staff='+contact_id+']');
                    if(!$(staff_block).hasClass('active') && $(staff_block).length > 0 && type != 'noload') {
                        staff_anchor = $(staff_block).closest('a');
                        retrieve_items_month(staff_anchor, '', true);
                    }
				}
			}
		});
	});
	$('[id^=collapse_staff]').find('.block-item.active').each(function() {
		var contact_id = $(this).data('staff');
		if(contact_id > 0) {
			visibles.push(parseInt(contact_id));
			if(all_staff.indexOf(parseInt(contact_id)) == -1) {
				all_staff.push(parseInt(contact_id));
			}
		}
	});
	$('.calendar_table .calendarSortable').filter(function() { return $(this).data('contact') > 0; }).hide();
	<?php if($_GET['mode'] == 'client') { ?>
		clients.forEach(function (contact_id) {
			$('.calendar_table .calendarSortable').filter(function() { return $(this).data('contact') == contact_id; }).show();
		});
	<?php } else { ?>
		all_staff.forEach(function (contact_id) {
			$('.calendar_table .calendarSortable').filter(function() { return $(this).data('contact') == contact_id; }).show();
		});
		teams.forEach(function (contact_id) {
			$('.calendar_table .calendarSortable').filter(function() { return $(this).data('contact') == contact_id; }).show();
		});
	<?php } ?>

    // Filter tickets in Calendar view based on the selected client
    $('.sortable-blocks').each(function() {
        var ticket_businessid = $(this).data('businessid');
        var ticket_clientid = $(this).data('clientid');
        if (clients.indexOf(parseInt(ticket_clientid)) == -1 && clients.indexOf(parseInt(ticket_businessid)) && clients.length > 0) {
            $(this).prevAll('.quick_actions:first').hide();
            $(this).hide();
        } else {
            $(this).prevAll('.quick_actions:first').show();
            $(this).show();
        }
    });
	
	// Save which contacts or staff are active
	$.ajax({
		url: 'calendar_ajax_all.php?fill=selected_contacts&offline='+offline_mode,
		method: 'POST',
		data: { contacts: visibles, teams: teams },
		success: function(response) {
		}
	});

	resize_calendar_view_monthly();

    displayActiveBlocksAuto();

    <?php if($client_staff_freq == 1) { ?>
        displayClientFrequency(clients);
    <?php } ?>
}
</script>
<div class="hide_on_iframe ticket-calendar calendar-screen" style="padding-bottom: 0px;">
	<div class="pull-left collapsible">
		<input type="text" class="search-text form-control" placeholder="Search All">
		<div class="sidebar panel-group block-panels" id="category_accordions" style="margin: 1.5em 0 0.5em; overflow: auto; padding-bottom: 0;">
            <?php if(count($contact_regions) > 0) { ?>
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
	                    $region_list = $allowed_regions;
                        foreach($region_list as $region_line => $region) {
                            $color_styling = '#6DCFF6';
                            if(!empty($region_colours[$region_line])) {
                                $color_styling = $region_colours[$region_line];
                            }
                            $color_box = '<span style="height: 15px; width: 15px; background-color: '.$color_styling.'; border: 1px solid black; float: right;"></span>';
                            echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); return false;'><div class='block-item ".(in_array($region,$active_regions) ? 'active' : '')."' data-activevalue='".$region."' data-region='".$region."'>".$region.$color_box."</div></a>";
                        } ?>
	                    </div>
	                </div>
	            </div>
                <div class="active_blocks" data-accordion="collapse_region" style="display: none;">
                    <?php foreach($region_list as $region) { ?>
                        <div class="block-item active" data-activevalue="<?= $region ?>"><?= $region ?></div> 
                    <?php } ?>
                </div>
            <?php } ?>
            <?php if(get_config($dbc, 'ticket_client_type') !== '') {
                $collapse_in = $_GET['mode'] == 'client' ? 'in' : '';
                foreach(array_filter(explode(',', get_config($dbc, 'ticket_client_type'))) as $client_type) { ?>
                    <div class="panel panel-default">
                        <div class="panel-heading">
                            <h4 class="panel-title">
                                <a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_clients_<?= config_safe_str($client_type) ?>">
                                    <span style="display: inline-block; width: calc(100% - 6em);"><?= $client_type ?></span><span class="glyphicon glyphicon-plus"></span>
                                </a>
                            </h4>
                        </div>

                        <div id="collapse_clients_<?= config_safe_str($client_type) ?>" class="panel-collapse collapse <?php echo $collapse_in; $collapse_in = ''; ?>">
                            <div class="panel-body" style="overflow-y: auto; padding: 0;">
                                <?php $active_clients = array_filter(explode(',',get_user_settings()['appt_calendar_clients']));
                                $client_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 AND `category` = '".$client_type."'".$region_query),MYSQLI_ASSOC));
                                foreach($client_list as $clientid) {
                                    if(get_client($dbc, $clientid) != '' || get_contact($dbc, $clientid) != '-') {
                                        echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\");  ".($_GET['mode'] == 'client' ? 'toggle_columns(""); retrieve_items_month(this);' : 'toggle_columns("noload");')." return false;'><div class='block-item ".(in_array($clientid,$active_clients) ? 'active' : '')."' data-client='".$clientid."' data-region='".get_contact($dbc, $clientid, 'region')."' data-activevalue='".$clientid."'>".(!empty(get_client($dbc, $clientid)) ? get_client($dbc, $clientid) : get_contact($dbc, $clientid))."</div></a>";
                                    }
                                } ?>
                            </div>
                        </div>
                    </div>
                    <div class="active_blocks" data-accordion="collapse_clients_<?= config_safe_str($client_type) ?>" style="display: none;">
                        <?php foreach($client_list as $clientid) {
                            if(get_client($dbc, $clientid) != '' || get_contact($dbc, $clientid) != '-') { ?>
                                <div class="block-item active" data-activevalue="<?= $clientid ?>"><?= (!empty(get_client($dbc, $clientid)) ? get_client($dbc, $clientid) : get_contact($dbc, $clientid)) ?></div> 
                            <?php }
                        } ?>
                    </div>
                <?php }
            } ?>
            <?php if($_GET['mode'] != 'client') { ?>
	            <?php if($staff_split_security != 1) { ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_staff" >
									<span style="display: inline-block; width: calc(100% - 6em);">All Staff</span><span class="glyphicon glyphicon-minus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_staff" class="panel-collapse collapse in">
							<div class="panel-body panel-body-height" style="overflow-y: auto; padding: 0;">
								<?php
								$category_list = mysqli_query($dbc, "SELECT `category_contact` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND `deleted`=0 AND `status`=1 AND IFNULL(`category_contact`,'') != '' AND IFNULL(`calendar_enabled`,1)=1".$region_query." GROUP BY `category_contact` ORDER BY `category_contact`");
								while($display_option = mysqli_fetch_array($category_list)) {
									echo "<a href='' data-category='".$display_option['category_contact']."' onclick='check_contact_category(this); return false;'><div class='block-item'>".$display_option['category_contact']."</div></a>";
								}
								$active_contacts = array_filter(explode(',',get_user_settings()['appt_calendar_staff']));
								if(count($active_contacts) == 0) {
									$active_contacts[] = $_SESSION['contactid'];
								}
								$contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query),MYSQLI_ASSOC));
								foreach($contact_list as $contact_id) {
									echo "<a href='' onclick='$(\"#collapse_teams .block-item\").removeClass(\"active\"); $(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); retrieve_items_month(this); return false;'><div class='block-item ".(in_array($contact_id,$active_contacts) ? 'active' : '')."' data-staff='$contact_id' data-category='".get_contact($dbc, $contact_id, 'category_contact')."' data-activevalue='".$staff_id."'><span style=''>";
									profile_id($dbc, $contact_id);
									echo '</span> '.get_contact($dbc, $contact_id)."<ul class='client_freq' style='font-size: x-small; display: none;'></ul></div></a>";
								}
								?>
							</div>
						</div>
					</div>
	                <div class="active_blocks" data-accordion="collapse_staff" style="display: none;">
	                    <?php foreach($staff_list as $staff_id) { ?>
	                        <div class="block-item active" data-activevalue="<?= $staff_id ?>"><?= get_contact($dbc, $staff_id) ?></div> 
	                    <?php } ?>
	                </div>
	            <?php } else {
	                $collapse_in = 'in';
	                foreach(get_security_levels($dbc) as $security_label => $security_level) { ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_staff_<?= config_safe_str($security_level) ?>" >
										<span style="display: inline-block; width: calc(100% - 6em);"><?= $security_label ?></span><span class="glyphicon glyphicon-minus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_staff_<?= config_safe_str($security_level) ?>" class="panel-collapse collapse <?php echo $collapse_in; $collapse_in = ''; ?>">
								<div class="panel-body panel-body-height" style="overflow-y: auto; padding: 0;">
									<?php
									$category_list = mysqli_query($dbc, "SELECT `category_contact` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND `deleted`=0 AND `status`=1 AND IFNULL(`category_contact`,'') != '' AND IFNULL(`calendar_enabled`,1)=1".$region_query." GROUP BY `category_contact` ORDER BY `category_contact`");
									while($display_option = mysqli_fetch_array($category_list)) {
										echo "<a href='' data-category='".$display_option['category_contact']."' onclick='check_contact_category(this); return false;'><div class='block-item'>".$display_option['category_contact']."</div></a>";
									}
									$active_contacts = array_filter(explode(',',get_user_settings()['appt_calendar_staff']));
									if(count($active_contacts) == 0) {
										$active_contacts[] = $_SESSION['contactid'];
									}
									$contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1 AND CONCAT(',',`role`,',') LIKE '%,$security_level,%'".$region_query),MYSQLI_ASSOC));
									foreach($contact_list as $contact_id) {
										echo "<a href='' onclick='$(\"#collapse_teams .block-item\").removeClass(\"active\"); $(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); retrieve_items_month(this); return false;'><div class='block-item ".(in_array($contact_id,$active_contacts) ? 'active' : '')."' data-staff='$contact_id' data-category='".get_contact($dbc, $contact_id, 'category_contact')."' data-activevalue='".$staff_id."'><span style=''>";
										profile_id($dbc, $contact_id);
										echo '</span> '.get_contact($dbc, $contact_id)."<ul class='client_freq' style='font-size: x-small; display: none;'></ul></div></a>";
									}
									?>
								</div>
							</div>
						</div>
	                    <div class="active_blocks" data-accordion="collapse_staff_<?= config_safe_str($security_level) ?>" style="display: none;">
	                        <?php foreach($staff_list as $staff_id) { ?>
	                            <div class="block-item active" data-activevalue="<?= $staff_id ?>"><?= get_contact($dbc, $staff_id) ?></div> 
	                        <?php } ?>
	                    </div>
	              	<?php }
				} ?>
				<?php if(get_config($dbc, 'ticket_teams') !== '') { ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_teams" >
								<span style="display: inline-block; width: calc(100% - 6em);">Teams</span><span class="glyphicon glyphicon-minus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_teams" class="panel-collapse collapse">
						<div class="panel-body" style="overflow-y: auto; padding: 0;">
	                        <?php include('../Calendar/teams_sidebar.php'); ?>
						</div>
					</div>
				</div>
				<?php } ?>
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
		<div class="block-button">Month</div>
		<a href="" onclick="changeDate('', 'next'); return false;"><div class="block-button">&nbsp;<img src="../img/icons/next-arrow.png" style="height: 1em;"></div></a>
		<a href="?type=ticket&view=daily&mode=<?= $_GET['mode'] ?><?= $region_url ?>"><div class="block-button" style="margin-left: 1em;">Day</div></a>
		<a href="?type=ticket&view=weekly<?= $region_url ?>"><div class="block-button">Week</div></a>
		<a href="?type=ticket&view=monthly<?= $region_url ?>"><div class="block-button active blue">Month</div></a>
		<?php if($ticket_status_color_code_legend == 1 && $wait_list == 'ticket') { ?>
			<div class="block-button legend-block" style="position: relative;">
				<div class="block-button ticket-status-legend" style="display: none; width: 20em; position: absolute; bottom: 1em;"><?= $ticket_status_legend ?></div>
				<img src="../img/legend-icon.png">
			</div>
		<?php } ?>
		<div class="block-button" style="margin-left: 1em;">
			<img src="<?= WEBSITE_URL ?>/img/block/green.png" width="10" height="10" border="0" alt="">&nbsp;&nbsp;Today + Following Day&nbsp;&nbsp;&nbsp;&nbsp;
			<img src="<?= WEBSITE_URL ?>/img/block/orange.png" width="10" height="10" border="0" alt="">&nbsp;&nbsp;Last 2 Days&nbsp;&nbsp;&nbsp;&nbsp;
			<img src="<?= WEBSITE_URL ?>/img/block/red.png" width="10" height="10" border="0" alt="">&nbsp;&nbsp;Older Than 2 Previous Days
		</div>
		<a href="" onclick="$('.set_date').focus(); return false;"><div class="block-button pull-right"><img src="../img/icons/calendar-button.png" style="height: 1em; margin-right: 1em;">Go To Date</div></a>
		<?php unset($page_query['date']); ?>
		<a href="" onclick="changeDate('<?= date('Y-m-d') ?>'); return false;"><div class="block-button pull-right">Today</div></a>
		<input value="<?= $calendar_start ?>" type="text" style="border: 0; width: 0;" class="pull-right datepicker set_date" onchange="changeDate(this.value);">
		<?php $page_query['date'] = $_GET['date']; ?>
	</div>
</div>