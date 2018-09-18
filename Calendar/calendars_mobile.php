<?php include('../include.php');

include('calendar_defaults.php');
include_once('calendar_functions_inc.php');
include_once('calendar_settings_inc.php');

$calendar_add_urls = [
	'ticket' => [TICKET_NOUN, WEBSITE_URL."/Ticket/index.php?edit=0&calendar_view=true&region=".$_GET['region']],
	'appt' => ['Appointment', WEBSITE_URL."/Calendar/booking.php?action=edit&bookingid=NEW&region=".$_GET['region']],
	'shift' => ['Shift', WEBSITE_URL."/Calendar/shifts.php?shiftid=NEW&region=".$_GET['region']],
	'reminder' => ['Reminder', WEBSITE_URL."/Calendar/add_reminder.php&region=".$_GET['region']],
	'team' => ['Teams', WEBSITE_URL."/Calendar/teams.php?teamid=NEW&region=".$_GET['region']],
	'equip_assign' => [$equipment_category.' Assignment', WEBSITE_URL."/Calendar/equip_assign.php?equipment_assignmentid=NEW&region=".$_GET['region']],
	'shift_tickets' => ['Shift '.TICKET_TILE, WEBSITE_URL."/Calendar/tickets_shift.php?region=".$_GET['region']."&weekly_days=".implode(',',$weekly_days)]
];

include_once('calendars_mobile_data.php');
// var_dump($calendar_table['2018-08-29']['18979']);
include_once('calendar_js_inc.php');

$page_query = $_GET;
$edit_access = vuaed_visible_function($dbc, 'calendar_rook');
unset($page_query['mobile_tab']);
// Calendar Main Screen ?>
</head>
<script type="text/javascript" src="appointments.js"></script>
<script>
var page_mode = '<?= $_GET['mode'] ?>';
$(document).ready(function() {
	$('#calendar-menu-wrapper').height($('#calendar-menu').height());
	$('#calendar-view-day table td,#calendar-view-day table th').filter(function() { return $(this).data('date') == 0 }).width('10%');
	$('#calendar-view-day table td,#calendar-view-day table th').filter(function() { return $(this).data('date') != 0 }).width('auto');
	$('#calendar-view-day table thead tr').css('position', '');

	//Header fixed
    $(window).scroll( function() {
        if ($(window).scrollTop() > $('#calendar-menu-wrapper').offset().top) {
            $('#calendar-menu').addClass('floating-fixed');
        } else {
            $('#calendar-menu').removeClass('floating-fixed');
        }
    } );

	//Adjust contact label size
	var contact_label = $('#contact_label').text();
	var fontsize = 14;
	var maxwidth = $('#contact_label_div').width() - 30;
	var maxheight = $('#contact_label_div').height();
	do {
		$('#contact_label').css('font-size', fontsize);
		fontsize = fontsize - 1;
	} while (($('#contact_label').width() > maxwidth || $('#contact_label').height() > maxheight) && fontsize > 3)
});
$(document).on("overlayIFrameSliderInit", function() {
	$('.hide_on_iframe').hide();
	$('.iframe_overlay').height($('.iframe_overlay').closest('.container').height() + 20);
	$('.iframe_overlay .iframe').width('100%');
});
$(document).on("overlayIFrameSliderLoad", function() {
	$('[name="calendar_iframe"]').contents().find('h3').first().prepend('<img src="<?= WEBSITE_URL ?>/img/icons/ROOK-status-rejected.jpg" class="pull-right offset-top-5 offset-left-5 offset-right-5" width="25" onclick="window.parent.$(\'[name=calendar_iframe]\').load();">');
	window.parent.$('[name="calendar_iframe"]').off('load').load(function() {
		$('.iframe_overlay').hide();
		$('.hide_on_iframe').show();
		$(this).off('load').attr('src', '/blank_loading_page.php');
		resizeBlocks();
	});
});
</script>
<body>
<div class="loading_overlay" style="display: none;"><div class="loading_wheel"></div></div>
<?php include_once ('../navigation.php');
checkAuthorised('calendar_rook');
?>
<div class="container">
	<a style="display:none;"><div class="block-item active" id="mobile_active_contact" data-<?= $retrieve_contact ?>="<?= $contact_id ?>"></div></a>
	<div class="mobile-calendar-main-screen" style="float: left; width: 100%; height: 100%;">
		<div id="dialog-calendartype" title="Select a Calendar" style="display: none;">
			<div class="col-sm-12">Select a Calendar:</div>
			<div class="col-sm-12">
				<select name="calendar_type" data-placeholder="Select a Calendar..." class="chosen-select-deselect form-control">
					<option></option>
					<?php if(in_array('My Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'My Calendar')) { ?><option value="?type=my&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>">My Calendar</option><?php } ?>
					<?php if(in_array('Universal Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Universal Calendar')) { ?><option value="?type=uni&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>">Universal Calendar</option><?php } ?>
					<?php if(in_array('Appointment Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Appointment Calendar')) { ?><option value="?type=appt&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>">Appointment Calendar</option><?php } ?>
					<?php if(in_array('Dispatch Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Dispatch Calendar')) { ?><option value="?type=schedule&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>">Dispatch  Calendar</option><?php } ?>
					<?php if(in_array('Staff Schedule Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Staff Schedule Calendar')) { ?><option value="?type=staff&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>">Staff Schedule Calendar</option><?php } ?>
					<?php if(in_array('Sales Estimates Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Sales Estimates Calendar')) { ?><option value="?type=estimates&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>">Sales Estimates Calendar</option><?php } ?>
					<?php if(in_array('Ticket Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Ticket Calendar')) { ?><option value="?type=ticket&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>"><?= TICKET_NOUN ?> Calendar</option><?php } ?>
					<?php if(in_array('Shift Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Shift Calendar')) { ?><option value="?type=shift&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>">Shift Calendar</option><?php } ?>
					<?php if(in_array('Events Calendar', $calendar_types) && check_subtab_persmission($dbc, 'calendar_rook', ROLE, 'Events Calendar')) { ?><option value="?type=event&view=<?= $_GET['view'] ?>&region=<?= $_GET['region'] ?>">Events Calendar</option><?php } ?>
				</select>
			</div>
		</div>
		<div id="dialog-calendarview" title="Select a View" style="display: none;">
			<div class="col-sm-12">Select a View:</div>
		</div>
		<div id="dialog-calendarcontact" title="Select a <?= $mobile_calendar_contact_cat ?>" style="display: none;">
			<?php if(count($contact_regions) > 0 && $_GET['type'] != 'event') { ?>
				<div class="col-sm-12">Filter by Region:</div>
				<div class="col-sm-12 gap-bottom">
					<select name="calendar_region" data-placeholder="Select a Region..." class="chosen-select-deselect form-control">
						<option></option>
						<?php foreach ($allowed_regions as $region_name) { ?>
							<option value="<?= $region_name ?>"><?= $region_name ?></option>
						<?php } ?>
					</select>
				</div>
			<?php } ?>
			<div class="col-sm-12">Select a <?= $mobile_calendar_contact_cat ?>:</div>
			<div class="col-sm-12">
				<select name="calendar_contact" data-placeholder="Select a <?= $mobile_calendar_contact_cat ?>..." class="chosen-select-deselect form-control">
					<option></option>
					<?php if($_GET['type'] != 'event') {
						foreach($all_contacts as $calendar_contactid => $calendar_contact) {
							$calendar_contactlabel = $calendar_contact[0];
							$calendar_contactregion = $calendar_contact[1];
							$page_query['contactid'] = $calendar_contactid;
							echo '<option data-region="'.$calendar_contactregion.'" value="?'.http_build_query($page_query).'">'.$calendar_contactlabel.'</option>';
							$page_query['contactid'] = $_GET['contactid'];
						}
					} else {
						foreach($all_contacts as $calendar_projecttype => $calendar_projects) {
							echo '<optgroup label="'.$calendar_projecttype.'">';
							foreach($calendar_projects as $calendar_projectid => $calendar_projectlabel) {
								$page_query['contactid'] = $calendar_projectid;
								echo '<option value="?'.http_build_query($page_query).'">'.$calendar_projectlabel.'</option>';
								$page_query['contactid'] = $_GET['contactid'];
							}
							echo '</optgroup>';
						}
					} ?>
				</select>
			</div>
		</div>
		<div id="dialog-calendaradd" title="Select a Type" style="display:none;">
			What would you like to add?
		</div>
		<div class="iframe_overlay" style="display:none; margin-top: -20px;margin-left:-15px;">
			<div class="iframe">
				<div class="iframe_loading">Loading...</div>
				<iframe name="calendar_iframe" src=""></iframe>
			</div>
		</div>
		<div class="iframe_holder" style="display:none;">
			<img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
			<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
			<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
		</div>
		<div class="row hide_on_iframe">
			<div class="main-screen" style="background-color: #fff; border-width: 0; margin-top: -20px;">
				<div id="calendar-menu-wrapper">
					<div id="calendar-menu" style="width: 100%; background-color: white; z-index: 999999;">
						<?php include('../Calendar/calendars_mobile_menu.php'); ?>
						<div id="calendar-month-block">
							<div class="calendar-mobile-block-item" style="width: 10%; border: none; padding: 0.75em;"><?php $page_query['date'] = date('Y-m-d', strtotime($calendar_start.' - 1month')); ?><a href="?<?= http_build_query($page_query) ?>" onclick="changeMobileMonth(this); return false;"><?php $page_query['date'] = $_GET['date']; ?><img src="../img/icons/back-arrow.png" class="pull-left" style="max-height: 100%;"></a></div><div class="calendar-mobile-block-item" style="width: 80%; border: none;"><span style="font-family: 'Arial';font-weight: bold;"><?= $search_month.' '.$search_year ?></span></div><div class="calendar-mobile-block-item" style="width: 10%; border: none; padding: 0.75em;"><?php $page_query['date'] = date('Y-m-d', strtotime($calendar_start.' + 1month')); ?><a href="?<?= http_build_query($page_query) ?>" onclick="changeMobileMonth(this); return false;"><?php $page_query['date'] = $_GET['date']; ?><img src="../img/icons/next-arrow.png" class="pull-right" style="max-height: 100%;"></a></div>
						</div>
						<?php for($cur_day = $first_day; strtotime($cur_day) <= strtotime($last_day); $cur_day = date('Y-m-d', strtotime($cur_day.'+ 1 day'))) { ?>
							<div class="calendar-day-block" style="display:none;" data-prev="<?= $cur_day != $first_day ? date('Y-m-d', strtotime($cur_day.'- 1 day')) : '' ?>" data-date="<?= $cur_day ?>" data-next="<?= $cur_day != $last_day ? date('Y-m-d', strtotime($cur_day.'+ 1 day')) : '' ?>">
								<div class="calendar-mobile-block-item" style="width: 10%; border: none; padding: 0.75em;"><a href="" onclick="toggleMobileView(this); return false;" data-type="prev" class="calendar_menu_day"><img src="../img/icons/back-arrow.png" class="pull-left" style="max-height: 100%; <?= $cur_day == $first_day ? 'opacity: 0.2;' : '' ?>"></a></div><div class="calendar-mobile-block-item" style="width: 80%; border: none;"><span style="font-family: 'Arial';font-weight: bold;"><?= date('l, F d, Y', strtotime($cur_day)) ?></span></div><div class="calendar-mobile-block-item" style="width: 10%; border: none; padding: 0.75em;"><?php $page_query['date'] = date('Y-m-d', strtotime($calendar_start.' + 1month')); ?><a href="" onclick="toggleMobileView(this); return false;" data-type="next" class="calendar_menu_day"><img src="../img/icons/next-arrow.png" class="pull-right" style="max-height: 100%; <?= $cur_day == $last_day ? 'opacity: 0.2;' : '' ?>"></a></div>
							</div>
						<?php } ?>
						<div class="clearfix"></div>
					</div>
				</div>

				<div id="calendar-view">

					<div id="calendar-view-month">
						<?php include('../Calendar/calendars_mobile_month.php'); ?>
					</div>
					<div class="clearfix"></div>

					<div id="calendar-view-list">
						<?php include('../Calendar/calendars_mobile_list.php'); ?>
					</div>
					<div class="clearfix"></div>

					<div id="calendar-view-day" class="calendar_view" style="display: none;">
						<?php include('../Calendar/load_calendar_table.php');
							include('../Calendar/load_calendar_table_display_mobile.php'); ?>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
<?php include('../footer.php'); ?>