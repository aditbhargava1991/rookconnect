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
var page_mode = '<?= $_GET['mode'] ?>';
$(document).ready(function() {
	// toggle_columns();
	reload_all_data_month();
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
function toggle_columns(no_fetch = 0) {
	// Hide deselected columns
	var visibles = [];
	var visibles_clients = [];
	var all_staff = [];
	var all_clients = [];
	var all_client_staff = [];
	$('#collapse_contact').find('.block-item.active').each(function() {
		var contact_id = $(this).data('contact');
		if(contact_id > 0) {
			visibles.push(parseInt(contact_id));
			if(all_staff.indexOf(parseInt(contact_id)) == -1) {
				all_staff.push(parseInt(contact_id));
			}
		}
	});
	$('#collapse_booking').find('.block-item.active').each(function() {
		var contact_id = $(this).data('contact');
		if(contact_id > 0) {
			visibles_clients.push(parseInt(contact_id));
			if(all_clients.indexOf(parseInt(contact_id)) == -1) {
				all_clients.push(parseInt(contact_id));
			}
			if(no_fetch == 0) {
				var client_staff = getClientStaff(contact_id, 'shift');
				client_staff.success(function(response) {
					var staffids = JSON.parse(response);
					staffids.forEach(function(staffid) {
						if(visibles.indexOf(parseInt(staffid)) == -1 && staffid > 0 && all_client_staff.indexOf(parseInt(staffid)) == -1) {
							all_client_staff.push(parseInt(staffid));
							$('#collapse_contact .block-item:not(.active)').filter(function() { return $(this).data('contact') == staffid }).each(function() {
								retrieve_items_month($(this).closest('a'), '', true);
							});
						}
					});
				});
			}
		}
	});
	
	// Save which contacts or staff are active
	$.ajax({
		url: 'calendar_ajax_all.php?fill=selected_contacts&offline='+offline_mode,
		method: 'POST',
		data: { contacts: visibles, category: 'staff' },
		success: function(response) {
		}
	});
	$.ajax({
		url: 'calendar_ajax_all.php?fill=selected_contacts&offline='+offline_mode,
		method: 'POST',
		data: { contacts: visibles_clients, category: 'client' },
		success: function(response) {
		}
	});

	// Display staff logos
	if(visibles.length > 0) {
		$('.selected_staff_logos .id-circle').hide();
		visibles.forEach(function(contact_id) {
			$('.selected_staff_logos').find('.id-circle[data-contact="'+contact_id+'"]').show();
		});
		$('.selected_staff_logos').css('display', 'table');
	} else {
		$('.selected_staff_logos').hide();
	}
	// Display client logos
	if(visibles_clients.length > 0) {
		$('.selected_client_logos .id-circle').hide();
		visibles_clients.forEach(function(contact_id) {
			$('.selected_client_logos').find('.id-circle[data-contact="'+contact_id+'"]').show();
		});
		$('.selected_client_logos').css('display', 'table');
	} else {
		$('.selected_client_logos').hide();
	}

	$('.calendar_table .calendarSortable').filter(function() { return $(this).data('contact') > 0; }).hide();
	if(all_clients.length == 0) {
		$('.calendar_table .sortable-blocks').show();
	} else {
		$('.calendar_table .sortable-blocks').hide();
		all_clients.forEach(function (contact_id) {
			$('.calendar_table .sortable-blocks').filter(function() { return $(this).data('clientid') == contact_id; }).show().closest('.calendarSortable').show();
		});
	}
	all_staff.forEach(function (contact_id) {
		$('.calendar_table .calendarSortable').filter(function() { return $(this).data('contact') == contact_id; }).each(function() {
			$(this).show();
			if($(this).find('.sortable-blocks:visible').length == 0) {
				$(this).hide();
			}
		});
	});
	resize_calendar_view_monthly();
}
function selectAllStaff() {
	if($('[name="select_all_staff"]').is(':checked')) {
		$('#collapse_contact').find('.block-item:not(.active)').closest('a').click();
	} else {
		$('#collapse_contact').find('.block-item.active').closest('a').click();
	}
}
</script>
<?php
	$shift_client_type = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"))['contact_category'];
?>
<div class="hide_on_iframe ticket-calendar calendar-screen" style="padding-bottom: 0px;">
	<div class="pull-left collapsible">
		<input type="text" class="search-text form-control" placeholder="Search <?= $shift_client_type ?>">
		<div class="sidebar panel-group block-panels" id="category_accordions" style="margin: 1.5em 0 0.5em; overflow: hidden; padding-bottom: 0;">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_contact" >
							<span style="display: inline-block; width: calc(100% - 6em);">All Staff</span><span class="glyphicon glyphicon-minus"></span>
						</a>
					</h4>
				</div>

				<div id="collapse_contact" class="panel-collapse collapse in">
					<div class="panel-body panel-body-height" style="overflow-y: auto; padding: 0;">
						<?php $shift_select_all_staff = get_config($dbc, 'shift_select_all_staff');
						if($shift_select_all_staff == 1) { ?>
							<span class="pull-right">Select All <input type="checkbox" name="select_all_staff" onclick="selectAllStaff();" style="margin-top: 0px; position: relative; top: 2px;"></span>
							<div class="clearfix"></div>
						<?php } ?>
						<?php
						$category_list = mysqli_query($dbc, "SELECT `category_contact` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND `deleted`=0 AND `status`=1 AND IFNULL(`category_contact`,'') != '' AND IFNULL(`calendar_enabled`,1)=1".$region_query." GROUP BY `category_contact` ORDER BY `category_contact`");
						while($display_option = mysqli_fetch_array($category_list)) {
							echo "<a href='' data-category='".$display_option['category_contact']."' onclick='check_contact_category(this); return false;'><div class='block-item'>".$display_option['category_contact']."</div></a>";
						}
						$active_contacts = array_filter(explode(',',get_user_settings()['appt_calendar_staff']));
						// if(count($active_contacts) == 0) {
						// 	$active_contacts[] = $_SESSION['contactid'];
						// }
						$contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query),MYSQLI_ASSOC));
						foreach($contact_list as $contact_id) {
							echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); if($(this).find(\".block-item\").hasClass(\"active\")) { toggle_columns(1); } else { toggle_columns(); } retrieve_items_month(this); return false;'><div class='block-item ".(in_array($contact_id,$active_contacts) ? 'active' : '')."' data-contact='$contact_id' data-category='".get_contact($dbc, $contact_id, 'category_contact')."'><span style=''>";
							profile_id($dbc, $contact_id);
							echo '</span> '.get_contact($dbc, $contact_id)."</div></a>";
						}
						?>
					</div>
				</div>
			</div>
			<?php if(!empty($shift_client_type)) { ?>
				<div class="panel panel-default">
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_booking" >
								<span style="display: inline-block; width: calc(100% - 6em);">All <?= (substr($shift_client_type, -1, 1) == 's' ? $shift_client_type : $shift_client_type.'s') ?></span><span class="glyphicon glyphicon-minus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_booking" class="panel-collapse collapse">
						<div class="panel-body panel-body-height" style="overflow-y: auto; padding: 0;">
							<?php
							$category_list = mysqli_query($dbc, "SELECT `category_contact` FROM `contacts` WHERE `category`='".$shift_client_type."' AND `deleted`=0 AND `status`=1 AND IFNULL(`category_contact`,'') != ''".$region_query." GROUP BY `category_contact` ORDER BY `category_contact`");
							while($display_option = mysqli_fetch_array($category_list)) {
								echo "<a href='' data-category='".$display_option['category_contact']."' onclick='check_contact_category(this); return false;'><div class='block-item'>".$display_option['category_contact']."</div></a>";
							}
							$active_contacts = array_filter(explode(',',get_user_settings()['appt_calendar_contacts']));
							$contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category`='".$shift_client_type."' AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1".$region_query),MYSQLI_ASSOC));
							foreach($contact_list as $contact_id) {
								echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); if($(this).find(\".block-item\").hasClass(\"active\")) { toggle_columns(); } else { toggle_columns(1); } return false;'><div class='block-item ".(in_array($contact_id,$active_contacts) ? 'active' : '')."' data-contact='$contact_id' data-category='".get_contact($dbc, $contact_id, 'category_contact')."'><span style=''>";
								profile_id($dbc, $contact_id);
								echo '</span> '.get_contact($dbc, $contact_id)."</div></a>";
							}
							?>
						</div>
					</div>
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

	$page_query = $_GET;
	if(isset($_GET['shiftid'])) {
		echo '<div class="scalable calendar-month-sidebar">';
		include('shifts.php');
		echo '</div>';	
	}

	if ($_GET['region']) {
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
		<a href="?type=shift&view=daily&mode=<?= $_GET['mode'] ?><?= $region_url ?>"><div class="block-button" style="margin-left: 1em;">Day</div></a>
		<a href="?type=shift&view=weekly<?= $region_url ?>"><div class="block-button">Week</div></a>
		<a href="?type=shift&view=monthly<?= $region_url ?>"><div class="block-button active blue">Month</div></a>
		<?php if($selected_staff_icons == 1) { ?>
			<div class="block-button selected_staff_logos" style="margin-left: 1em; padding: 0 0.5em 0 0.5em; display: table; display: none;">
				<div style="display: table-cell; vertical-align: middle;">
					Selected Staff: 
				</div>
				<?php $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query),MYSQLI_ASSOC));
				foreach($contact_list as $contact_id) {
					echo getContactLogo($dbc, $contact_id);
				} ?>
			</div>
		<?php } ?>
		<?php if($selected_client_icons == 1 && !empty($shift_client_type)) { ?>
			<div class="block-button selected_client_logos" style="margin-left: 1em; padding: 0 0.5em 0 0.5em; display: table; display: none;">
				<div style="display: table-cell; vertical-align: middle;">
					Selected <?= $shift_client_type ?>: 
				</div>
				<?php $contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category`='".$shift_client_type."' AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1".$region_query),MYSQLI_ASSOC));
				foreach($contact_list as $contact_id) {
					echo getContactLogo($dbc, $contact_id);
				} ?>
			</div>
		<?php } ?>
		<a href="" onclick="$('.set_date').focus(); return false;"><div class="block-button pull-right"><img src="../img/icons/calendar-button.png" style="height: 1em; margin-right: 1em;">Go To Date</div></a>
		<?php unset($page_query['date']); ?>
		<a href="" onclick="changeDate('<?= date('Y-m-d') ?>'); return false;"><div class="block-button pull-right">Today</div></a>
		<input value="<?= $calendar_start ?>" type="text" style="border: 0; width: 0;" class="pull-right datepicker set_date" onchange="changeDate(this.value);">
		<?php $page_query['date'] = $_GET['date']; ?>
	</div>
</div>