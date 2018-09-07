<?php
/*
Software Format
*/
include_once('../include.php');
?>
<script type="text/javascript">
$(document).on('change', 'select[name="classic_size"]', function() { classicmenusize(this); });
$(document).on('change', 'select[name="dropdown_size"]', function() { dropdownmenusize(this); });
$(document).on('change', 'select[name="tile_size"]', function() { tilesize(this); });
function classicmenusize(sel) {
	var stagee = sel.value;
	var contactid = $('.contacterid').val();
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "<?php echo WEBSITE_URL; ?>/ajax_all.php?fill=tile_menu_settings&settingtype=classic&contactid="+contactid+"&value="+stagee,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}

function dropdownmenusize(sel) {
	var stagee = sel.value;
	var contactid = $('.contacterid').val();
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "<?php echo WEBSITE_URL; ?>/ajax_all.php?fill=tile_menu_settings&settingtype=dropdown&contactid="+contactid+"&value="+stagee,
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}

function tilesize(sel) {
	var stagee = sel.value;
	var contactid = $('.contacterid').val();
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "<?php echo WEBSITE_URL; ?>/ajax_all.php?fill=tile_menu_settings&settingtype=tilesize&contactid="+contactid+"&value="+stagee,
		dataType: "html",   //expect html to be returned
		success: function(response){
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
$classicmenusize = '';
$dropdownmenusize = '';
$tilesize = '';
$contactidfortile = $_SESSION['contactid'];
$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM user_settings WHERE contactid='$contactidfortile'"));
$classicmenusize = $get_config['classic_menu_size'];
$dropdownmenusize = $get_config['dropdown_menu_size'];
$tilesize = $get_config['tile_size'];

$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_menu_formatting'"));
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
			<th  data-title="Software Style" width="60%">Available Software Formatting</th>
			<th  data-title="Activation" width="40%">Size</th>
		</tr>
		<tr>
			<td>Classic Menu Size</td>
			<td><select name="classic_size" class="form-control chosen-select"><option></option>
				<option <?php echo ($classicmenusize == '1' || $classicmenusize == '' || $classicmenusize == NULL ? 'selected' : ''); ?> value="1">Small</option>
				<option <?php echo ($classicmenusize == '2' ? 'selected' : ''); ?> value="2">Large</option>
			<select></td>
		</tr>
		<tr>
			<td>Drop Down Menu Size</td>
			<td><select name="dropdown_size" class="form-control chosen-select"><option></option>
				<option <?php echo ($dropdownmenusize == '1' || $dropdownmenusize == '' || $dropdownmenusize == NULL ? 'selected' : ''); ?> value="1">Small</option>
				<option <?php echo ($dropdownmenusize == '2' ? 'selected' : ''); ?> value="2">Large</option>
			<select></td>
		</tr>
		<tr>
			<td>Tile Menu Size</td>
			<td><select name="tile_size" class="form-control chosen-select"><option></option>
				<option <?php echo ($tilesize == '1' ? 'selected' : ''); ?> value="1">Extra Small</option>
				<option <?php echo ($tilesize == '2' ? 'selected' : ''); ?> value="2">Small</option>
				<option <?php echo ($tilesize == '3' || $tilesize == '' || $tilesize == NULL ? 'selected' : ''); ?> value="3">Medium</option>
				<option <?php echo ($tilesize == '4' ? 'selected' : ''); ?> value="4">Large</option>
			<select></td>
		</tr>


	</table>
</div>
<input type='hidden' value='<?php echo $_SESSION['contactid']; ?>' class='contacterid'>
<input type='hidden' value='<?php echo $_SESSION['newsboard_menu_choice']; ?>'>
</div>