<?php
include ('../include.php');
checkAuthorised('start_day_button');
include 'config.php';

if(isset($_POST['submit'])) {
	$timesheet_start_tile = filter_var($_POST['timesheet_start_tile'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_start_tile' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_start_tile') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_start_tile' WHERE `name`='timesheet_start_tile'");
	$timesheet_running_button = filter_var($_POST['timesheet_running_button'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_running_button' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_running_button') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_running_button' WHERE `name`='timesheet_running_button'");
	$timesheet_track_shifts = filter_var($_POST['timesheet_track_shifts'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_track_shifts' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_track_shifts') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_track_shifts' WHERE `name`='timesheet_track_shifts'");
	$timesheet_hide_others = filter_var($_POST['timesheet_hide_others'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_hide_others' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_hide_others') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_hide_others' WHERE `name`='timesheet_hide_others'");
	$timesheet_end_tile = filter_var($_POST['timesheet_end_tile'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_end_tile' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_end_tile') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_end_tile' WHERE `name`='timesheet_end_tile'");
	$timesheet_always_show = filter_var($_POST['timesheet_always_show'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_always_show' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_always_show') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_always_show' WHERE `name`='timesheet_always_show'");
	$timesheet_add_day_comment = filter_var($_POST['timesheet_add_day_comment'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_add_day_comment' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_add_day_comment') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_add_day_comment' WHERE `name`='timesheet_add_day_comment'");
	$timesheet_direct_indirect = filter_var($_POST['timesheet_direct_indirect'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_direct_indirect' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_direct_indirect') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_direct_indirect' WHERE `name`='timesheet_direct_indirect'");
	$timesheet_hide_groups = filter_var($_POST['timesheet_hide_groups'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_hide_groups' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_hide_groups') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_hide_groups' WHERE `name`='timesheet_hide_groups'");
	$timesheet_track_clients = filter_var($_POST['timesheet_track_clients'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_track_clients' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_track_clients') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_track_clients' WHERE `name`='timesheet_track_clients'");
	$timesheet_client_category = filter_var($_POST['timesheet_client_category'],FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT 'timesheet_client_category' FROM (SELECT COUNT(*) `rows` FROM general_configuration WHERE `name`='timesheet_client_category') CONFIG WHERE `rows`=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$timesheet_client_category' WHERE `name`='timesheet_client_category'");
	set_config($dbc, 'day_tracking_preset_note', $_POST['day_tracking_preset_note']);
	set_config($dbc, 'timesheet_hide_past_days', $_POST['timesheet_hide_past_days']);
	set_config($dbc, 'timesheet_break_option', $_POST['timesheet_break_option']);
	set_config($dbc, 'ticket_force_starts_day', $_POST['ticket_force_starts_day']);
	set_config($dbc, 'active_ticket_button', $_POST['active_ticket_button']);
	set_config($dbc, 'timesheet_force_end_midnight', $_POST['timesheet_force_end_midnight']);
} ?>
</head>
<body>

<?php include ('../navigation.php'); ?>
<div class="container">
<div class="row">
<h1><?= START_DAY ?></h1>
<div class="pad-left gap-top double-gap-bottom"><a href="start_day.php" class="btn config-btn">Back to Dashboard</a></div>

<form id="form1" name="form1" method="post" enctype="multipart/form-data" class="form-horizontal" role="form">
	<div class="panel-group" id="accordion2">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#accordion2" href="#collapse_time_tile" >
						Day Tracking<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>
			<div id="collapse_time_tile" class="panel-collapse collapse in">
				<div class="panel-body">
					<script>
					function disableTimeTracking(value) {
						if(value != '') {
							$('[type=checkbox][name=timesheet_start_tile]').removeAttr('checked');
						} else {
							$('[type=checkbox][name=timesheet_start_tile]').prop('checked',true);
						}
					}
					function displayClientCategory() {
						if($('[name="timesheet_track_clients"]').is(':checked')) {
							$('.timesheet_track_clients').show();
						} else {
							$('.timesheet_track_clients').hide();
						}
					}
					</script>
					<?php $timesheet_start_tile = get_config($dbc, 'timesheet_start_tile');
					echo "<label class='col-sm-4 control-label'>Start Time Tracking Tile:</label><div class='col-sm-8'><input type='text' name='timesheet_start_tile' value='$timesheet_start_tile' class='form-control' onchange='disableTimeTracking(this.value);'></div>";
					echo "<label class='col-sm-4'></label><label class='form-checkbox'><input ".($timesheet_start_tile == ''?'checked':'')." type='checkbox' name='timesheet_start_tile' value=''> Disable Start Time Tracking Tile</label><br>";
					$ticket_force_starts_day = get_config($dbc, 'ticket_force_starts_day');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($ticket_force_starts_day > 0 ? 'checked' : '')." type='checkbox' name='ticket_force_starts_day' value='1'> Force Day Start when Checked In on ".TICKET_NOUN."</label><br>";
					$timesheet_break_option = get_config($dbc, 'timesheet_break_option');
					echo "<label class='col-sm-4'></label><label class='form-checkbox'><input ".($timesheet_break_option > 0 ? 'checked' : '')." type='checkbox' name='timesheet_break_option' value='1'> Enable Break Button</label><br>";
					$timesheet_track_shifts = get_config($dbc, 'timesheet_track_shifts');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($timesheet_track_shifts == '1'?'checked':'')." type='checkbox' name='timesheet_track_shifts' value='1'> Track Time Based On Shifts (If Shift Exists)</label><br>";
					$timesheet_hide_others = get_config($dbc, 'timesheet_hide_others');
					echo "<label class='col-sm-4'></label><div class='col-sm-8'><label class='form-checkbox any-width'><input ".($timesheet_hide_others == '0'?'checked':'')." type='radio' name='timesheet_hide_others' value='0'> Allow Starting/Ending All User's Days</label>
						<label class='form-checkbox any-width'><input ".($timesheet_hide_others == '2'?'checked':'')." type='radio' name='timesheet_hide_others' value='2'> Allow Starting/Ending Other User's Days Within a Group</label>
						<label class='form-checkbox any-width'><input ".($timesheet_hide_others == '1'?'checked':'')." type='radio' name='timesheet_hide_others' value='1'> Don't Allow Starting/Ending Other User's Days</label></div><br>";
					$timesheet_always_show = get_config($dbc, 'timesheet_always_show');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($timesheet_always_show == '1'?'checked':'')." type='checkbox' name='timesheet_always_show' value='1'> Show button on every page</label><br>";
					$timesheet_add_day_comment = get_config($dbc, 'timesheet_add_day_comment');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($timesheet_add_day_comment == '0'?'checked':'')." type='radio' name='timesheet_add_day_comment' value='0'> No Day Tracking Comments</label>";
					echo "<label class='form-checkbox any-width'><input ".($timesheet_add_day_comment == '1'?'checked':'')." type='radio' name='timesheet_add_day_comment' value='1'> Add Comment About Day Ttracking</label>";
					echo "<label class='form-checkbox any-width'><input ".($timesheet_add_day_comment == '3'?'checked':'')." type='radio' name='timesheet_add_day_comment' value='3'> Use Task Types for Day Tracking</label>";
					echo "<label class='form-checkbox any-width'><input ".($timesheet_add_day_comment == '2'?'checked':'')." type='radio' name='timesheet_add_day_comment' value='2'> Preset Day Tracking Comment:</label>";
					echo "<div class='col-sm-8 pull-right'><input type='text' name='day_tracking_preset_note' value='".get_config($dbc, 'day_tracking_preset_note')."' class='form-control' placeholder='Preset Day Tracking Comment...'></div><div class='clearfix'></div>";
					$timesheet_direct_indirect = get_config($dbc, 'timesheet_direct_indirect');
					echo "<label class='col-sm-4'></label><div class='col-sm-8'><label class='form-checkbox any-width'><input ".(!($timesheet_direct_indirect > 0)?'checked':'')." type='radio' name='timesheet_direct_indirect' value='0'> Simple Time Tracking</label>
						<label class='form-checkbox any-width'><input ".($timesheet_direct_indirect == '1'?'checked':'')." type='radio' name='timesheet_direct_indirect' value='1'> Use Direct/Indirect Hours</label>
						<label class='form-checkbox any-width'><input ".($timesheet_direct_indirect == '2'?'checked':'')." type='radio' name='timesheet_direct_indirect' value='2'> Use Position by Day</label></div><br>";
					$timesheet_hide_groups = get_config($dbc, 'timesheet_hide_groups');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($timesheet_hide_groups == '1'?'checked':'')." type='checkbox' name='timesheet_hide_groups' value='1' onchange='displayClientCategory();'> Hide Staff Groups</label><br>";
					$timesheet_hide_past_days = get_config($dbc, 'timesheet_hide_past_days');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($timesheet_hide_past_days == '1'?'checked':'')." type='checkbox' name='timesheet_hide_past_days' value='1'> Hide ".TICKET_TILE." After Sign Out</label><br>";
					$timesheet_track_clients = get_config($dbc, 'timesheet_track_clients');
					echo "<label class='col-sm-4'></label><label class='form-checkbox any-width'><input ".($timesheet_track_clients == '1'?'checked':'')." type='checkbox' name='timesheet_track_clients' value='1' onchange='displayClientCategory();'> Track Clients</label><br>";
					$timesheet_client_category = get_config($dbc,'timesheet_client_category');
					echo "<div class='form-group timesheet_track_clients' ".($timesheet_track_clients != 1 ? 'style="display:none;"' : '').">";
					echo "<label class='col-sm-4 control-label'>Client Category:</label>";
					echo "<div class='col-sm-8'>";
					echo "<select data-placeholder='Select a Client Category...' name='timesheet_client_category' class='chosen-select-deselect form-control'>";
					echo "<option></option>";
					$category_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `category` FROM `contacts` WHERE `deleted` = 0 AND `status` = 1 ORDER BY `category`"),MYSQLI_ASSOC);
                    foreach($category_list as $category) {
                    	echo "<option value='".$category['category']."' ".($timesheet_client_category == $category['category'] ? 'selected' : '').">".$category['category']."</option>";
                    }
					echo "</select>";
					echo "</div>";
					echo "</div>";
					$timesheet_running_button = get_config($dbc, 'timesheet_running_button');
					echo "<div class='clearfix'></div><label class='col-sm-4 control-label'>Active Time Tracking Button:<br /><em>Button in the top of the screen that notifies the user that they are tracking time. Leaving this blank will disable the button.</em></label>";
					echo "<div class='col-sm-8'><input type='text' name='timesheet_running_button' value='$timesheet_running_button' class='form-control'></div>";
					$active_ticket_button = get_config($dbc, 'active_ticket_button');
					echo "<div class='clearfix'></div><label class='col-sm-4 control-label'>Active ".TICKET_NOUN." Button:<br /><em>Button in the top of the screen that notifies the user that they are working on a ".TICKET_NOUN.".</em></label>";
					echo "<div class='col-sm-8'>
						<label class='form-checkbox'><input type='radio' name='active_ticket_button' ".($active_ticket_button == '' ? 'checked' : '')." value='' class='form-control'>Default: Running ".TICKET_NOUN." #</label>
						<label class='form-checkbox'><input type='radio' name='active_ticket_button' ".($active_ticket_button == 'ticket_label' ? 'checked' : '')." value='ticket_label' class='form-control'>".TICKET_NOUN." Label</label>
						<label class='form-checkbox'><input type='radio' name='active_ticket_button' ".($active_ticket_button == 'disable_active_ticket' ? 'checked' : '')." value='disable_active_ticket' class='form-control'>Disable ".TICKET_NOUN." Tracking Button</label>
					</div>";
					$timesheet_end_tile = get_config($dbc, 'timesheet_end_tile');
					echo "<div class='clearfix'></div><label class='col-sm-4 control-label'>End Time Tracking Tile:<br /><em>Only appears after Start Time Tracking Tile has started the time tracking.</em></label>";
					echo "<div class='col-sm-8'><input type='text' name='timesheet_end_tile' value='$timesheet_end_tile' class='form-control'></div>";
					$timesheet_force_end_midnight = get_config($dbc, 'timesheet_force_end_midnight');
					echo "<div class='clearfix'></div><label class='col-sm-4 control-label'>Force End Day at Midnight:</label>";
					echo "<div class='col-sm-8'><label class='form-checkbox'><input ".($timesheet_force_end_midnight > 0 ? 'checked' : '')." type='checkbox' name='timesheet_force_end_midnight' value='1'> Enable</label></div>";
					?>
				</div>
			</div>
		</div>
	</div>

	<div class="form-group">
		<div class="col-sm-6">
			<a href="start_day.php" class="btn config-btn btn-lg">Back</a>
		</div>
		<div class="col-sm-6">
			<button type="submit" name="submit" value="day_tracking" class="btn config-btn btn-lg pull-right">Submit</button>
		</div>
		<div class="clearfix"></div>
	</div>
</form>
</div>
</div>

<?php include ('../footer.php'); ?>