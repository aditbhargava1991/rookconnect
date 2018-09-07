<?php
/*
Contacts Sort Order Setting
*/
include_once('../include.php');
$font_sizes = ['xx-small' => 'Extra Extra Small', 'x-small' => 'Extra Small', 'small' => 'Small', '' => 'Normal', 'medium' => 'Large', 'large' => 'Extra Large', 'x-large' => 'Extra Extra Large'];
$font_families = ['' => 'Default', 'Arial, Helvetica, sans-serif' => 'Sans Serif', '"Times New Roman", serif' => 'Serif', 'Monospace, monospace' => 'Fixed Width', '"Arial Black", sans-serif' => 'Wide', '"Arial Narrow", sans-serif' => 'Narrow', '"Comic Sans MS", cursive, sans-serif' => 'Comic Sans MS', 'Garamond, serif' => 'Garamond', 'Georgia, serif' => 'Georgia', 'Tahoma, sans-serif' => 'Tahoma', '"Trebuchet MS", sans-serif' => 'Trebuchet MS', 'Verdana, sans-serif' => 'Verdana'];
?>
<script type="text/javascript">
$(document).on('change', 'select[name="font_type"]', function() { font_type(this); });
$(document).on('change', 'select[name="font_size"]', function() { font_size(this); });
function font_type(sel) {
	var font_type = sel.value;
	$.ajax({    //create an ajax request to load_page.php
		type: "POST",
		url: "<?php echo WEBSITE_URL; ?>/Settings/settings_ajax.php?fill=font_type",
		data: { value: font_type },
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});
}
function font_size(sel) {
	var font_size = sel.value;
	$.ajax({    //create an ajax request to load_page.php
		type: "POST",
		url: "<?php echo WEBSITE_URL; ?>/Settings/settings_ajax.php?fill=font_size",
		data: { value: font_size },
		dataType: "html",   //expect html to be returned
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
$font_type = $get_config['font_type'];
$font_size = $get_config['font_size'];

$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_font_setting'"));
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
			<th  data-title="Font Settings" width="60%">Font Settings</th>
			<th  data-title="Settings" width="40%">Settings</th>
		</tr>
		<tr>
			<td>Font Type</td>
			<td><select name="font_type" class="form-control chosen-select">
				<?php
				foreach ($font_families as $key => $value) {
					echo '<option '.(htmlspecialchars($key) == $font_type ? 'selected' : '').' value="'.htmlspecialchars($key).'" style="font-family: '.htmlspecialchars($key).';">'.$value.'</option>';
				}
				?>
			<select></td>
		</tr>
		<tr>
			<td>Font Size</td>
			<td><select name="font_size" class="form-control chosen-select">
				<?php
				foreach ($font_sizes as $key => $value) {
					echo '<option '.($key == $font_size ? 'selected' : '').' value="'.$key.'" style="font-size: '.$key.';">'.$value.'</option>';
				}
				?>
			</select></td>
		</tr>

	</table>
</div>
<input type='hidden' value='<?php echo $_SESSION['contactid']; ?>' class='contacterid'>
</div>