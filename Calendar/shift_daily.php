<script src="appointments.js"></script>
<script>
var page_mode = '<?= $_GET['mode'] ?>';
$(document).ready(function() {
	//$(window).resize(resize_calendar_view).resize();
	toggle_columns();
	reload_all_data();
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
function toggle_columns() {
	// Hide deselected columns
	var visibles = [];
	var teams = [];
	var all_contacts = [];
	$('.calendar_view table td, .calendar_view table th').filter(function() { return $(this).data('contact') > 0; }).hide();
	$('#collapse_teams').find('.block-item.active').each(function() {
		var contactids = $(this).data('contactids').split(',');
		var teamid = $(this).data('teamid');
		teams.push(parseInt(teamid));
		contactids.forEach(function (contact_id) {
			if(contact_id > 0) {
				if(all_contacts.indexOf(parseInt(contact_id)) == -1) {
					all_contacts.push(parseInt(contact_id));
				}
			}
		});
	});
	$('#collapse_contact').find('.block-item.active').each(function() {
		var contact_id = $(this).data('contact');
		if(contact_id > 0) {
			visibles.push(parseInt(contact_id));
			if(all_contacts.indexOf(parseInt(contact_id)) == -1) {
				all_contacts.push(parseInt(contact_id));
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
	
	// Save which contacts or staff are active
	$.ajax({
		url: 'calendar_ajax_all.php?fill=selected_contacts&offline='+offline_mode,
		method: 'POST',
		data: { contacts: visibles, category: '<?= $_GET['mode'] ?>', teams: teams },
		success: function(response) {
		}
	});

	// Display staff/client logos
	if(visibles.length > 0) {
		$('.selected_<?= $_GET['mode'] == 'client' ? 'client' : 'staff' ?>_logos .id-circle').hide();
		visibles.forEach(function(contact_id) {
			$('.selected_<?= $_GET['mode'] == 'client' ? 'client' : 'staff' ?>_logos').find('.id-circle[data-contact="'+contact_id+'"]').show();
		});
		$('.selected_<?= $_GET['mode'] == 'client' ? 'client' : 'staff' ?>_logos').css('display', 'table');
	} else {
		$('.selected_<?= $_GET['mode'] == 'client' ? 'client' : 'staff' ?>_logos').hide();
	}
}
function selectAllStaff() {
	if($('[name="select_all_staff"]').is(':checked')) {
		$('#collapse_contact').find('.block-item:not(.active)').closest('a').click();
	} else {
		$('#collapse_contact').find('.block-item.active').closest('a').click();
	}
}
</script>
<?php $calendar_start = $_GET['date'];
if($calendar_start == '') {
	$calendar_start = date('Y-m-d');
} else {
	$calendar_start = date('Y-m-d', strtotime($calendar_start));
}
$shift_client_type = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_shifts`"))['contact_category'];
$calendar_type = 'shifts';
?>
<div class="calendar-screen set-height">
	<div class="pull-left collapsible">
		<input type="text" class="search-text form-control" placeholder="Search <?= $shift_client_type ?>">
		<div class="sidebar panel-group block-panels" id="category_accordions" style="margin: 1.5em 0 0.5em; overflow: hidden; padding-bottom: 0;">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h4 class="panel-title">
						<a data-toggle="collapse" data-parent="#category_accordions" href="#collapse_contact" >
							<span style="display: inline-block; width: calc(100% - 6em);">All <?= ($_GET['mode'] == 'client' ? (substr($shift_client_type, -1, 1) == 's' ? $shift_client_type : $shift_client_type.'s') : 'Staff') ?></span><span class="glyphicon glyphicon-minus"></span>
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
						if($_GET['mode'] == 'client') {
							$category_list = mysqli_query($dbc, "SELECT `category_contact` FROM `contacts` WHERE `category`='".$shift_client_type."' AND `deleted`=0 AND `status`=1 AND IFNULL(`category_contact`,'') != ''".$region_query." GROUP BY `category_contact` ORDER BY `category_contact`");
						} else {
							$category_list = mysqli_query($dbc, "SELECT `category_contact` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND `deleted`=0 AND `status`=1 AND IFNULL(`category_contact`,'') != '' AND IFNULL(`calendar_enabled`,1)=1".$region_query." GROUP BY `category_contact` ORDER BY `category_contact`");
						}
						while($display_option = mysqli_fetch_array($category_list)) {
							echo "<a href='' data-category='".$display_option['category_contact']."' onclick='check_contact_category(this); return false;'><div class='block-item'>".$display_option['category_contact']."</div></a>";
						}
						if($_GET['mode'] == 'client') {
							$active_contacts = array_filter(explode(',',get_user_settings()['appt_calendar_contacts']));
							if(count($active_contacts) == 0) {
								$active_contacts[] = $_SESSION['contactid'];
							}
							$contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category`='".$shift_client_type."' AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1".$region_query),MYSQLI_ASSOC));
						} else {
							$active_contacts = array_filter(explode(',',get_user_settings()['appt_calendar_staff']));
							if(count($active_contacts) == 0) {
								$active_contacts[] = $_SESSION['contactid'];
							}
							$contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query),MYSQLI_ASSOC));
						}
						foreach($contact_list as $contact_id) {
							echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); retrieve_items(this); return false;'><div class='block-item ".(in_array($contact_id,$active_contacts) ? 'active' : '')."' data-contact='$contact_id' data-category='".get_contact($dbc, $contact_id, 'category_contact')."'><span style=''>";
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
								<span style="display: inline-block; width: calc(100% - 6em);">Available <?= ($_GET['mode'] == 'client' ? 'Staff' : (substr($shift_client_type, -1, 1) == 's' ? $shift_client_type : $shift_client_type.'s')) ?> to Book</span><span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_booking" class="panel-collapse collapse">
						<div class="panel-body bookable" style="overflow-y: auto; padding: 0;">
							<?php if($_GET['mode'] == 'client') {
								$book_contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1 AND IFNULL(`calendar_enabled`,1)=1".$region_query),MYSQLI_ASSOC));
							} else {
								$book_contact_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category`='".$shift_client_type."' AND `deleted`=0 AND `status`=1 AND `show_hide_user`=1".$region_query),MYSQLI_ASSOC));
							}
							foreach($book_contact_list as $contact_id) { ?>
								<div class="block-item" style="border: 1px solid rgba(0,0,0,0.5); margin: 0;" data-type="shift" data-id="<?= $contact_id ?>">
									<img class='drag-handle no-toggle' src='<?= WEBSITE_URL ?>/img/icons/drag_handle.png' style='float: right; width: 2em;' title='Drag'>
									<?= get_contact($dbc, $contact_id) ?></div>
							<?php } ?>
						</div>
					</div>
				</div>
			<?php } ?>
		</div>
		<div class="block-item"><img src="../img/icons/clock-button.png" style="height: 1em; margin-right: 1em;">Break</div>
	</div>

	<?php if($_GET['shiftid']): ?>
		<div class="pull-right scalable unbooked_view" style="height: 30em; overflow: auto; <?= $scale_style ?>">
			<?php include('shifts.php'); ?>
		</div>
	<?php endif; ?>

	<?php if($_GET['add_reminder']): ?>
		<div class="scalable pull-right unbooked_view" style="height: 30em; overflow: auto; <?= $scale_style ?>">
			<?php include('add_reminder.php'); ?>
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
		<a href="?type=shift&view=monthly<?= $region_url ?>"><div class="block-button">Month</div></a>
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
	<div class="clearfix"></div>
</div>