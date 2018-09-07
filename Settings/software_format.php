<?php
/*
Software Format
*/
include_once('../include.php');
?>
<script type="text/javascript">
	function tileclick(sel) {

		var stagee = sel.value;
		var contactid = $('.contacterid').val();

		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "<?php echo WEBSITE_URL; ?>/ajax_all.php?fill=tile_menu_choice&contactid="+contactid+"&value="+stagee,
			dataType: "html",   //expect html to be returned
			success: function(response){
				location.reload();
			}
		});

	}

	function newsboardredirect(sel) {
		var stagee = sel.value;
		if(stagee == '1') {
			$('#calendarredirectoff').trigger('click');
			$('#daysheetredirectoff').trigger('click');
		}
		var contactid = $('.contacterid').val();
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "<?php echo WEBSITE_URL; ?>/ajax_all.php?fill=tile_menu_settings&settingtype=newsboardredirect&contactid="+contactid+"&value="+stagee,
			dataType: "html",   //expect html to be returned
			success: function(response){
			}
		});
	}

	function newsboardclick(sel) {
		var newsboard = sel.value;
		var contactid = $('.contacterid').val();

		$.ajax({
			type: "GET",
			url: "<?php echo WEBSITE_URL; ?>/ajax_all.php?fill=newsboard_menu_choice&contactid="+contactid+"&value="+newsboard,
			dataType: "html",
			success: function(response){
				location.reload();
			}
		});
	}

	function calendarredirect(sel) {
		var stagee = sel.value;
		if(stagee == '1') {
			$('#newsboardredirectoff').trigger('click');
			$('#daysheetredirectoff').trigger('click');
		}
		var contactid = $('.contacterid').val();
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "<?php echo WEBSITE_URL; ?>/ajax_all.php?fill=tile_menu_settings&settingtype=calendarredirect&contactid="+contactid+"&value="+stagee,
			dataType: "html",   //expect html to be returned
			success: function(response){
			}
		});
	}

	function daysheetredirect(sel) {
		var stagee = sel.value;
		if(stagee == '1') {
			$('#calendarredirectoff').trigger('click');
			$('#newsboardredirectoff').trigger('click');
		}
		var contactid = $('.contacterid').val();
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "<?php echo WEBSITE_URL; ?>/ajax_all.php?fill=tile_menu_settings&settingtype=daysheetredirect&contactid="+contactid+"&value="+stagee,
			dataType: "html",   //expect html to be returned
			success: function(response){
			}
		});
	}

	function alerticon(sel) {
		var alerticon = sel.value;
		var contactid = $('.contacterid').val();

		$.ajax({
			type: "GET",
			url: "<?php echo WEBSITE_URL; ?>/ajax_all.php?fill=tile_menu_settings&settingtype=alerticon&contactid="+contactid+"&value="+alerticon,
			dataType: "html",
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
$newsboardredirect = '';
$contactidfortile = $_SESSION['contactid'];
$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactidfortile'");

while($row = mysqli_fetch_assoc($result)) {
	$software_config2	= $row['software_tile_menu_choice'];
	$newsboard_config	= $row['newsboard_menu_choice'];
}
$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM user_settings WHERE contactid='$contactidfortile'"));
$newsboardredirect = $get_config['newsboard_redirect'];
$calendarredirect = $get_config['calendar_redirect'];
$daysheetredirect = $get_config['daysheet_redirect'];
$alerticon = $get_config['alert_icon'];

$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_formatting'"));
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
			<th  data-title="Software Style" width="85%">Available Software Formatting</th>
			<th  data-title="Activation" width="15%">Activate Formatting</th>
		</tr>
		<tr>
			<td>Classic Software Menu</td>
			<td><input type='radio' onclick="tileclick(this);" name='stylertiles' style='width:20px; height:20px;' <?php if($software_config2 == '2') { echo "checked"; } ?> value='2'></td>
		</tr>
		<tr>
			<td>Drop Down Menu & Tile Menu</td>
			<td><input type='radio' onclick="tileclick(this);" name='stylertiles' style='width:20px; height:20px;' <?php if($software_config2 == '1') { echo "checked"; } ?> value='1'></td>
		</tr>
		<tr>
			<td>Tile Menu</td>
			<td><input type='radio' onclick="tileclick(this);" name='stylertiles' style='width:20px; height:20px;' <?php if($software_config2 == '') { echo "checked"; } ?> value=''></td>
		</tr>


	</table>

	<table class="table table-bordered">
		<tr class="">
			<th data-title="Newsboard" width="70%">News Board</th>
			<th data-title="Activate" width="15%">Activate</th>
			<th data-title="Disable" width="15%">Disable</th>
		</tr>
		<tr>
			<td>News Board Menu</td>
			<td><input type="radio" onclick="newsboardclick(this);" name="newsboard" style="width:20px; height:20px;" <?php echo ($newsboard_config == '1') ? "checked" : ""; ?> value="1"></td>
			<td><input type="radio" onclick="newsboardclick(this);" name="newsboard" style="width:20px; height:20px;" <?php echo ($newsboard_config == NULL) ? "checked" : ""; ?> value=""></td>
		</tr>
		<tr>
			<td>View News Board on Login</td>
			<td><input type='radio' onclick="newsboardredirect(this);" name='newsboardredirect' id='newsboardredirecton' style='width:20px; height:20px;' <?php if($newsboardredirect == '1') { echo "checked"; } ?> value='1'></td>
			<td><input type='radio' onclick="newsboardredirect(this);" name='newsboardredirect' id='newsboardredirectoff' style='width:20px; height:20px;' <?php if($newsboardredirect != '1') { echo "checked"; } ?> value='2'></td>
		</tr>
	</table>

	<table class="table table-bordered">
		<tr class="">
			<th data-title="Calendar" width="70%">Calendar</th>
			<th data-title="Activate" width="15%">Activate</th>
			<th data-title="Disable" width="15%">Disable</th>
		</tr>
		<tr>
			<td>View Calendar on Login</td>
			<td><input type='radio' onclick="calendarredirect(this);" name='calendarredirect' id='calendarredirecton' style='width:20px; height:20px;' <?php if($calendarredirect == '1') { echo "checked"; } ?> value='1'></td>
			<td><input type='radio' onclick="calendarredirect(this);" name='calendarredirect' id='calendarredirectoff' style='width:20px; height:20px;' <?php if($calendarredirect != '1') { echo "checked"; } ?> value='2'></td>
		</tr>
	</table>

	<table class="table table-bordered">
		<tr class="">
			<th data-title="Day Sheet" width="70%">Planner</th>
			<th data-title="Activate" width="15%">Activate</th>
			<th data-title="Disable" width="15%">Disable</th>
		</tr>
		<tr>
			<td>View Planner on Login</td>
			<td><input type='radio' onclick="daysheetredirect(this);" name='daysheetredirect' id='daysheetredirecton' style='width:20px; height:20px;' <?php if($daysheetredirect == '1') { echo "checked"; } ?> value='1'></td>
			<td><input type='radio' onclick="daysheetredirect(this);" name='daysheetredirect' id='daysheetredirectoff' style='width:20px; height:20px;' <?php if($daysheetredirect != '1') { echo "checked"; } ?> value='2'></td>
		</tr>
	</table>

	<table class="table table-bordered">
		<tr class="">
			<th data-title="Alert Icon" width="70%">Alert Icon</th>
			<th data-title="Activate" width="15%">Activate</th>
			<th data-title="Disable" width="15%">Disable</th>
		</tr>
		<tr>
			<td>Alert Icon in Header</td>
			<td><input type='radio' onclick="alerticon(this);" name='alerticon' id='alerticonon' style='width:20px; height:20px;' <?php if($alerticon == '0') { echo "checked"; } ?> value='0'></td>
			<td><input type='radio' onclick="alerticon(this);" name='alerticon' id='alerticonoff' style='width:20px; height:20px;' <?php if($alerticon != '0') { echo "checked"; } ?> value='1'></td>
		</tr>
	</table>
</div>
<input type='hidden' value='<?php echo $_SESSION['contactid']; ?>' class='contacterid'>
<input type='hidden' value='<?php echo $_SESSION['newsboard_menu_choice']; ?>'>
</div>
