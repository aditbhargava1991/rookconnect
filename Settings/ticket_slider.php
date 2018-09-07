<?php
/*
Software Format
*/
include_once('../include.php');
?>
<script type="text/javascript">
$(document).on('change', '[name="calendar_ticket_slider"],[name="daysheet_ticket_slider"]', function() { updateTicketSlider(this); });
function updateTicketSlider(sel) {
	var field_name = sel.name;
	var value = $(sel).val();
	var contactid = $('.contacterid').val();
	$.ajax({
		url: "<?php echo WEBSITE_URL; ?>/Settings/settings_ajax.php?fill=ticket_slider",
		method: "POST",
		data: { field_name: field_name, value: value, contactid: contactid },
		success: function(response){
			location.reload();
		}
	});
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
<?php
$contactidfortile = $_SESSION['contactid'];
$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM user_settings WHERE contactid='$contactidfortile'"));
$calendar_ticket_slider = $get_config['calendar_ticket_slider'];
$daysheet_ticket_slider = $get_config['daysheet_ticket_slider'];

$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_ticket_slider'"));
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
<div id="">

	<table class='table table-bordered'>
		<tr class=''>
			<th  data-title="Software Style" width="60%">Tile</th>
			<th  data-title="Activation" width="40%"><?= TICKET_NOUN ?> Slider View</th>
		</tr>
		<tr>
			<td>Planner</td>
			<td>
                <label class="form-checkbox"><input type="radio" name="calendar_ticket_slider" value="full" <?= $calendar_ticket_slider != 'accordion' ? 'checked="checked"' : '' ?>"> Full View</label>
                <label class="form-checkbox"><input type="radio" name="calendar_ticket_slider" value="accordion" <?= $calendar_ticket_slider == 'accordion' ? 'checked="checked"' : '' ?>"> Accordion View</label>
            </td>
		</tr>
		<tr>
			<td>Calendar</td>
			<td>
                <label class="form-checkbox"><input type="radio" name="daysheet_ticket_slider" value="full" <?= $daysheet_ticket_slider != 'accordion' ? 'checked="checked"' : '' ?>"> Full View</label>
                <label class="form-checkbox"><input type="radio" name="daysheet_ticket_slider" value="accordion" <?= $daysheet_ticket_slider == 'accordion' ? 'checked="checked"' : '' ?>"> Accordion View</label>
            </td>
		</tr>
	</table>
</div>
<input type='hidden' value='<?php echo $_SESSION['contactid']; ?>' class='contacterid'>
</div>