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
	var all_staff = [];
	$('#collapse_teams').find('.block-item.active').each(function() {
		var contactids = $(this).data('contactids').split(',');
		var teamid = $(this).data('teamid');
		teams.push(parseInt(teamid));
		contactids.forEach(function (contact_id) {
			if(contact_id > 0) {
				if(all_staff.indexOf(parseInt(contact_id)) == -1) {
					all_staff.push(parseInt(contact_id));
				}
			}
		});
	});
	$('#collapse_staff').find('.block-item.active').each(function() {
		var contact_id = $(this).data('contact');
		if(contact_id > 0) {
			visibles.push(parseInt(contact_id));
			if(all_staff.indexOf(parseInt(contact_id)) == -1) {
				all_staff.push(parseInt(contact_id));
			}
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

	$('.calendar_table .calendarSortable').filter(function() { return $(this).data('contact') > 0; }).hide();
	all_staff.forEach(function (contact_id) {
		$('.calendar_table .calendarSortable').filter(function() { return $(this).data('contact') == contact_id; }).show();
	});
	resize_calendar_view_monthly();
}
</script>

<div class="hide_on_iframe ticket-calendar calendar-screen" style="padding-bottom: 0px;">
	<div class="pull-left collapsible">
		<input type="text" class="search-text form-control" placeholder="Search <?= $shift_client_type ?>">
		<div class="sidebar panel-group block-panels" id="category_accordions" style="margin: 1.5em 0 0.5em; overflow: hidden; padding-bottom: 0;">
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
							echo "<a href='' onclick='$(this).find(\".block-item\").toggleClass(\"active\"); toggle_columns(); retrieve_items_month(this); return false;'><div class='block-item ".(in_array($contact_id,$active_contacts) ? 'active' : '')."' data-contact='$contact_id' data-category='".get_contact($dbc, $contact_id, 'category_contact')."'><span style=''>";
							profile_id($dbc, $contact_id);
							echo '</span> '.get_contact($dbc, $contact_id)."</div></a>";
						}
						?>
					</div>
				</div>
			</div>
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
		<a href="?type=estimates&view=daily&mode=<?= $_GET['mode'] ?><?= $region_url ?>"><div class="block-button" style="margin-left: 1em;">Day</div></a>
		<a href="?type=estimates&view=weekly<?= $region_url ?>"><div class="block-button">Week</div></a>
		<a href="?type=estimates&view=monthly<?= $region_url ?>"><div class="block-button active blue">Month</div></a>
		<a href="" onclick="$('.set_date').focus(); return false;"><div class="block-button pull-right"><img src="../img/icons/calendar-button.png" style="height: 1em; margin-right: 1em;">Go To Date</div></a>
		<?php unset($page_query['date']); ?>
		<a href="" onclick="changeDate('<?= date('Y-m-d') ?>'); return false;"><div class="block-button pull-right">Today</div></a>
		<input value="<?= $calendar_start ?>" type="text" style="border: 0; width: 0;" class="pull-right datepicker set_date" onchange="changeDate(this.value);">
		<?php $page_query['date'] = $_GET['date']; ?>
	</div>
</div>