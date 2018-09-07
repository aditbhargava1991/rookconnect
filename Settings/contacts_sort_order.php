<?php
/*
Contacts Sort Order Setting
*/
include_once('../include.php');
?>
<script type="text/javascript">
$(document).ready(function() {
	$('[data-target]').change(user_setting);
});
function user_setting() {
	if($(this).data('target') == 'user') {
		$.ajax({    //create an ajax request to settings_ajax.php
			url: "<?php echo WEBSITE_URL; ?>/Settings/settings_ajax.php?fill=display_pref&name="+this.name+"&value="+this.value
		});
	} else {
		$.ajax({    //create an ajax request to settings_ajax.php
			url: "<?php echo WEBSITE_URL; ?>/Settings/settings_ajax.php?fill=system_display",
			method: 'POST',
			data: {
				name: this.name,
				value: this.value
			}
		});
	}
}
</script>
<style>
.settings-classic, .settings-dropdown, .settings-tiles, .settings-newsboard {
	cursor:pointer;
}
.settings-classic-config,.settings-dropdownmenu-config,.settings-tile-config, .settings-newsboard-config {
	width: 100%;
	margin:auto;
	max-width:400px;
	padding:10px;
	background-color:lightgrey;
	color:black;
	top:0px;
	position:relative;
	border: 10px outset grey;
	border-radius:10px;
	margin-bottom:20px;
	padding-bottom: 40px;
}


</style>
<?php $preferences = $_SESSION['user_preferences'];
$system_sort = get_config($dbc, 'system_contact_sort');
$system_time = get_config($dbc, 'system_time_format');

$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_contact_sort_order'"));
$note = $notes['note'];
    
if ( !empty($note) ) { ?>
    <div class="notice double-gap-bottom popover-examples">
        <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
        <div class="col-sm-11">
            <span class="notice-name">NOTE:</span>
            <?= $note; ?>
        </div>
        <div class="clearfix"></div>
    </div><?php
} ?>

<div class="col-md-12">

	<table class='table table-bordered'>
		<tr class='hidden-sm hidden-xs'>
			<th width="40%">Display Preference</th>
			<th width="30%">User Option</th>
			<th width="30%">System Option</th>
		</tr>
		<tr>
			<td data-title="Preference">Contacts Sort Order:</td>
			<td data-title="User Setting"><select name="contacts_sort_order" data-target="user" class="form-control chosen-select"><option></option>
				<option <?php echo ($preferences['contacts_sort_order'] > 0 ? '' : 'selected'); ?> value="0">System Default</option>
				<option <?php echo ($preferences['contacts_sort_order'] == 1 ? 'selected' : ''); ?> value="1">First Name</option>
				<option <?php echo ($preferences['contacts_sort_order'] > 1 ? 'selected' : ''); ?> value="2">Last Name</option>
			<select></td>
			<td data-title="System Setting"><select name="system_contact_sort" data-target="system" class="form-control chosen-select"><option></option>
				<option <?php echo ($system_sort == 1 ? 'selected' : ''); ?> value="1">First Name</option>
				<option <?php echo ($system_sort == 1 ? '' : 'selected'); ?> value="2">Last Name</option>
			<select></td>
		</tr>
		<tr>
			<td data-title="Preference">Time Format:</td>
			<td data-title="User Setting"><select name="time_format" data-target="user" class="form-control chosen-select"><option></option>
				<option <?php echo ($preferences['time_format'] > 0 ? '' : 'selected'); ?> value="0">System Default</option>
				<option <?php echo ($preferences['time_format'] == 1 ? 'selected' : ''); ?> value="1">HH:MM - 24 hour clock with 2-digit hour</option>
				<option <?php echo ($preferences['time_format'] == 2 ? 'selected' : ''); ?> value="2">H:MM - 24 hour clock with 1-digit hour</option>
				<option <?php echo ($preferences['time_format'] == 3 ? 'selected' : ''); ?> value="3">HH:MM AM - 12 hour clock with AM/PM</option>
				<option <?php echo ($preferences['time_format'] == 4 ? 'selected' : ''); ?> value="4">H:MM AM - 12 hour clock with 1-digit hour and AM/PM</option>
			<select></td>
			<td data-title="User Setting"><select name="system_time_format" data-target="system" class="form-control chosen-select"><option></option>
				<option <?php echo ($system_time > 1 ? '' : 'selected'); ?> value="1">HH:MM AM - 12 hour clock with AM/PM</option>
				<option <?php echo ($system_time == 2 ? 'selected' : ''); ?> value="2">H:MM AM - 12 hour clock with 1-digit hour and AM/PM</option>
				<option <?php echo ($system_time == 3 ? 'selected' : ''); ?> value="3">HH:MM - 24 hour clock with 2-digit hour</option>
				<option <?php echo ($system_time == 4 ? 'selected' : ''); ?> value="4">H:MM - 24 hour clock with 1-digit hour</option>
			<select></td>
		</tr>
		<tr>
			<td data-title="Preference">Report Row Colour Styling: <em>Reports (that have been designed to use this) will alternate between these two colours.</em></td>
			<td data-title="User Setting"></td>
			<td data-title="User Setting">
				<div class="col-sm-6"><input type="color" name="report_row_colour_1" data-target="system" class="form-control" value="<?= get_config($dbc, 'report_row_colour_1') ?>"></div>
				<div class="col-sm-6"><input type="color" name="report_row_colour_2" data-target="system" class="form-control" value="<?= get_config($dbc, 'report_row_colour_2') ?>"></div>
			</td>
		</tr>
	</table>
</div>