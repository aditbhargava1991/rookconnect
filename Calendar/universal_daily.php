<script src="appointments.js"></script>
<script>
var page_mode = 'staff';
$(document).ready(function() {
	// $(window).resize(resize_calendar_view).resize();
	toggle_columns();
	reload_all_data();

    //Display active blocks when collapsed
    displayActiveBlocksAuto();
    $('.collapsible .sidebar .panel').on('hidden.bs.collapse', function() {
        $(this).next('.active_blocks').show();
    });
    $('.collapsible .sidebar .panel').on('show.bs.collapse', function() {
        $(this).next('.active_blocks').hide();
    });
});
function check_staff_category(link) {
	$('[id^=collapse_staff]').find('.block-item[data-category="'+$(link).data('category')+'"]').removeClass("active");
	if($(link).find('.block-item').hasClass('active')) {
		$(link).find('.block-item').removeClass('active');
	} else {
		$(link).find('.block-item').addClass('active');
		$('[id^=collapse_staff]').find('.block-item[data-category="'+$(link).data('category')+'"]').addClass("active");
	}
	toggle_columns();
}
function toggle_columns() {
    if($('#collapse_teams .block-item.active').length > 0) {
        $('[id^=collapse_staff] .block-item').removeClass('active');
    }
	// Hide deselected columns
	var visible_staff = [];
	var teams = [];
	var all_contacts = [];
	$('.calendar_view table td, .calendar_view table th').filter(function() { return $(this).data('contact') > 0; }).hide();
	$('#collapse_teams').find('.block-item.active').each(function() {
		var contactids = $(this).data('contactids').split(',');
		var teamid = $(this).data('teamid');
		teams.push(parseInt(teamid));
        if($('.calendar_view table th[data-contact='+teamid+'][data-blocktype=team]').length == 0 || reload_teams) {
            clear_all_data();
            retrieve_items($('#collapse_teams').find('.block-item[data-teamid="'+teamid+'"]'), '', true, '', teamid);
        } else {
            $('.calendar_view table th[data-contact='+teamid+'][data-blocktype=team]').show();
            $('.calendar_view table td[data-contact='+teamid+'][data-blocktype=team]').show();
        }
		contactids.forEach(function (contact_id) {
			if(contact_id > 0) {
				if(all_contacts.indexOf(parseInt(contact_id)) == -1) {
					all_contacts.push(parseInt(contact_id));
                    var staff_block = $('[id^=collapse_staff]').find('.block-item[data-staff='+contact_id+']');
                    if(!$(staff_block).hasClass('active') && $(staff_block).length > 0 && ($('.calendar_view table th[data-contact='+teamid+'][data-blocktype=team]').length == 0 || reload_teams == 1)) {
                        staff_anchor = $(staff_block).closest('a');
                        retrieve_items(staff_anchor, '', true);
                    }
				}
			}
		});
	});
	$('[id^=collapse_staff]').find('.block-item.active').each(function() {
		var staff_id = $(this).data('staff');
		if(staff_id > 0) {
			visible_staff.push(staff_id);
			if(all_contacts.indexOf(parseInt(staff_id)) == -1) {
				all_contacts.push(parseInt(staff_id));
			}
		}
	});
	all_contacts.forEach(function (contact_id) {
		$('.calendar_view table td, .calendar_view table th').filter(function() { return $(this).data('contact') == contact_id; }).show();
	});
	
	// Specify the column width, if it's past min-width it will use that so this can just go down to 1%
	width = 100 / all_contacts.length;
	$('.calendar_view table td, .calendar_view table th').filter(function() { return $(this).data('contact') > 0; }).css('width',width+'%');
	$('.calendar_view table tbody tr').first().find('td').css('padding-top',$('.calendar_view table thead tr').outerHeight() + 8);
	
	<?php if($_GET['type'] != 'my') { ?>
		$.ajax({
			url: 'calendar_ajax_all.php?fill=selected_staff&offline='+offline_mode,
			method: 'POST',
			data: { staff: visible_staff },
			success: function(response) {
			}
		});
	<?php } ?>

    displayActiveBlocksAuto();
}
</script>
<?php $calendar_start = $_GET['date'];
if($calendar_start == '') {
	$calendar_start = date('Y-m-d');
} else {
	$calendar_start = date('Y-m-d', strtotime($calendar_start));
}
$calendar_type = get_config($dbc, 'uni_wait_list');
?>
<div class="calendar-screen set-height">
	<div class="collapsible pull-left">
		<input type="text" class="search-text form-control" placeholder="Search Staff">
		<div class="sidebar panel-group block-panels" id="category_accordions" style="margin: 1.5em 0 0.5em; overflow: hidden; padding-bottom: 0;">
            <?php if($staff_split_security != 1 && $_GET['type'] != 'my') { ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_staff" >
								<span style="display: inline-block; width: calc(100% - 6em);">All Staff</span><span class="glyphicon glyphicon-minus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_staff" class="panel-collapse collapse in <?= get_config($dbc, 'uni_teams') !== '' && strpos(','.$wait_list.',', ',ticket,') !== FALSE ? 'team_assign_div' : '' ?>">
						<div class="panel-body panel-body-height" style="overflow-y: auto; padding: 0;">
							<?php $category_list = mysqli_query($dbc, "SELECT `category_contact` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND `deleted`=0 AND `status`=1 AND IFNULL(`category_contact`,'') != '' AND IFNULL(`calendar_enabled`,1)=1".$region_query." GROUP BY `category_contact` ORDER BY `category_contact`");
							while($display_option = mysqli_fetch_array($category_list)) {
								echo "<a href='' data-category='".$display_option['category_contact']."' onclick='check_staff_category(this); return false;'><div class='block-item'>".$display_option['category_contact']."</div></a>";
							}
							$active_staff = array_filter(explode(',',get_user_settings()['appt_calendar_staff']));
							if(count($active_staff) == 0) {
								$active_staff[] = $_SESSION['contactid'];
							}
							if($_GET['type'] == 'my') {
								$active_staff = [$_SESSION['contactid']];
								$staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid` = '".$_SESSION['contactid']."'"),MYSQLI_ASSOC));
							} else {
								$staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query.$allowed_roles_query),MYSQLI_ASSOC));
							}
							foreach($staff_list as $staff_id) {
								echo "<a href='' onclick='$(\"#collapse_teams .block-item\").removeClass(\"active\"); $(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); retrieve_items(this); return false;'><div class='block-item ".(in_array($staff_id,$active_staff) ? 'active' : '')." ".(get_config($dbc, 'uni_teams') !== '' && strpos(','.$wait_list.',', ',ticket,') !== FALSE ? 'team_assign_draggable' : '')."' data-staff='$staff_id' data-category='".get_contact($dbc, $staff_id, 'category_contact')."' data-activevalue='".$staff_id."'>".(get_config($dbc, 'uni_teams') !== '' && strpos(','.$wait_list.',', ',ticket,') !== FALSE ? "<img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='float: right; width: 2em;' title='Drag'>" : '' );
								profile_id($dbc, $staff_id);
								echo get_contact($dbc, $staff_id)."</div></a>";
							} ?>
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
                foreach(get_security_levels($dbc) as $security_label => $security_level) {
                    if(empty($allowed_roles) || in_array($security_level, $allowed_roles)) { ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_staff_<?= config_safe_str($security_level) ?>" >
										<span style="display: inline-block; width: calc(100% - 6em);"><?= $security_label ?></span><span class="glyphicon glyphicon-minus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_staff_<?= config_safe_str($security_level) ?>" class="panel-collapse collapse <?php echo $collapse_in; $collapse_in = ''; ?> <?= get_config($dbc, 'uni_teams') !== '' && strpos(','.$wait_list.',', ',ticket,') !== FALSE ? 'team_assign_div' : '' ?>">
								<div class="panel-body panel-body-height" style="overflow-y: auto; padding: 0;">
									<?php $category_list = mysqli_query($dbc, "SELECT `category_contact` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND `deleted`=0 AND `status`=1 AND IFNULL(`category_contact`,'') != '' AND IFNULL(`calendar_enabled`,1)=1".$region_query." GROUP BY `category_contact` ORDER BY `category_contact`");
									while($display_option = mysqli_fetch_array($category_list)) {
										echo "<a href='' data-category='".$display_option['category_contact']."' onclick='check_staff_category(this); return false;'><div class='block-item'>".$display_option['category_contact']."</div></a>";
									}
									$active_staff = array_filter(explode(',',get_user_settings()['appt_calendar_staff']));
									if(count($active_staff) == 0) {
										$active_staff[] = $_SESSION['contactid'];
									}
									if($_GET['type'] == 'my') {
										$active_staff = [$_SESSION['contactid']];
										$staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `contactid` = '".$_SESSION['contactid']."'"),MYSQLI_ASSOC));
									} else {
										$staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1 AND `role` LIKE '%,$security_level,%'".$region_query.$allowed_roles_query),MYSQLI_ASSOC));
									}
									foreach($staff_list as $staff_id) {
										echo "<a href='' onclick='$(\"#collapse_teams .block-item\").removeClass(\"active\"); $(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); retrieve_items(this); return false;'><div class='block-item ".(in_array($staff_id,$active_staff) ? 'active' : '')." ".(get_config($dbc, 'uni_teams') !== '' && strpos(','.$wait_list.',', ',ticket,') !== FALSE ? 'team_assign_draggable' : '')."' data-staff='$staff_id' data-category='".get_contact($dbc, $staff_id, 'category_contact')."' data-activevalue='".$staff_id."'>".(get_config($dbc, 'uni_teams') !== '' && strpos(','.$wait_list.',', ',ticket,') !== FALSE ? "<img class='drag-handle no-toggle' src='".WEBSITE_URL."/img/icons/drag_handle.png' style='float: right; width: 2em;' title='Drag'>" : '' );
										profile_id($dbc, $staff_id);
										echo get_contact($dbc, $staff_id)."</div></a>";
									} ?>
								</div>
							</div>
						</div>
                        <div class="active_blocks" data-accordion="collapse_staff_<?= config_safe_str($security_level) ?>" style="display: none;">
                            <?php foreach($staff_list as $staff_id) { ?>
                                <div class="block-item active" data-activevalue="<?= $staff_id ?>"><?= get_contact($dbc, $staff_id) ?></div> 
                            <?php } ?>
                        </div>
                    <?php }
                }
            } ?>
			<?php if($teams !== '' && $_GET['type'] != 'my') { ?>
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
		</div>
		<div class="block-item"><img src="../img/icons/clock-button.png" style="height: 1em; margin-right: 1em;">Break</div>
	</div>
	<?php if($_GET['unbooked']): ?>
		<div class="scalable pull-right unbooked_view" style="height: 30em; overflow: auto; <?= $scale_style ?>">
			<?php include('unbooked.php'); ?>
		</div>
	<?php endif; ?>

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
	<div class="scale-to-fill"><!--col-sm-<?= ($_GET['unbooked'] || $_GET['teamid'] || $_GET['equipment_assignmentid'] || $_GET['shiftid'] || $_GET['bookingid']) ? '6' : '9' ?> col-xs-12">-->
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
		<a href="?type=<?= $_GET['type'] ?>&view=monthly<?= $region_url ?>"><div class="block-button">Month</div></a>
		<?php if($ticket_status_color_code_legend == 1 && strpos(','.$wait_list.',', ',ticket,') !== FALSE) { ?>
			<div class="block-button legend-block" style="position: relative;">
				<div class="block-button ticket-status-legend" style="display: none; width: 20em; position: absolute; bottom: 1em;"><?= $ticket_status_legend ?></div>
				<img src="../img/legend-icon.png">
			</div>
		<?php } ?>
		<a href="" onclick="$('.set_date').focus(); return false;"><div class="block-button pull-right"><img src="../img/icons/calendar-button.png" style="height: 1em; margin-right: 1em;">Go To Date</div></a>
		<?php unset($page_query['date']); ?>
		<a href="" onclick="changeDate('<?= date('Y-m-d') ?>'); return false;"><div class="block-button pull-right">Today</div></a>
		<input value="<?= $calendar_start ?>" type="text" style="border: 0; width: 0;" class="pull-right datepicker set_date" onchange="changeDate(this.value);">
		<?php $page_query['date'] = $_GET['date']; ?>
	</div>
	<div class="clearfix"></div>
</div>