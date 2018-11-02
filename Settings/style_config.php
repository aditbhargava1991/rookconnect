<?php include_once('../include.php');
/*
Software Styling
*/
if($_GET['subtab'] == 'security' && !check_subtab_persmission($dbc, 'software_config', ROLE, 'style_security')) {
	$_GET['subtab'] = '';
} else if($_GET['subtab'] == 'software' && !check_subtab_persmission($dbc, 'software_config', ROLE, 'style_software')) {
	$_GET['subtab'] = '';
}
?>
<script type="text/javascript">
$(document).on('change', 'select[name="sub_category"]', function() { changeLevel(this); });

function changeLevel(sel) {
	var stage = sel.value;
	window.location = '?tab=style&subtab=security&level='+stage;
}

function handleClick(sel) {

    var stagee = sel.value;
	var contactid = $('.contacterid').val();

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "<?php echo WEBSITE_URL; ?>/ajax_all.php?fill=styler_configuration&contactid="+contactid+"&value="+stagee+"&subtab=<?= $_GET['subtab'] ?>&level=<?= $_GET['level'] ?>",
		dataType: "html",   //expect html to be returned
		success: function(response){
			location.reload();
		}
	});

}

function sortTable(){
	if(jQuery("#caltbl").length > 0) {
		var tbl = document.getElementById("caltbl").tBodies[0];
		var store = [];
		for(var i=0, len=tbl.rows.length; i<len; i++){
			var row = tbl.rows[i];
			var sortnr = parseFloat(row.cells[0].textContent || row.cells[0].innerText);
			if(!isNaN(sortnr)) store.push([sortnr, row]);
		}
		store.sort(function(x,y){
			return x[0] - y[0];
		});
		for(var i=0, len=store.length; i<len; i++){
			tbl.appendChild(store[i][1]);
		}
		store = null;
	}
}
sortTable();
</script><?php

$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_styling'"));
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
	<div class="gap-bottom">
		<a href='settings.php?tab=style'><button type='button' class='btn brand-btn mobile-block mobile-100<?php echo (empty($_GET['subtab']) ? ' active_tab' : ''); ?>' >User Theme</button></a>
		<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'style_software')) { ?>
			<a href='settings.php?tab=style&subtab=software'><button type='button' class='btn brand-btn mobile-block mobile-100<?php echo ($_GET['subtab'] == 'software' ? ' active_tab' : ''); ?>' >Software Default Theme</button></a>
		<?php } ?>
		<?php if(check_subtab_persmission($dbc, 'software_config', ROLE, 'style_security')) { ?>
			<a href='settings.php?tab=style&subtab=security'><button type='button' class='btn brand-btn mobile-block mobile-100<?php echo ($_GET['subtab'] == 'security' ? ' active_tab' : ''); ?>' >Theme by Security Level</button></a>
		<?php } ?>
	</div>
	<?php if($_GET['subtab'] == 'security') {
		$sql=mysqli_query($dbc,"SELECT * FROM  security_level");
		$on_security = get_security_levels($dbc);
		$level_url = '';
		if(!empty($_GET['level'])) {
			$level_url = $_GET['level'];
		} else {
			$contacterid = $_SESSION['contactid'];
			$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '{$_SESSION['contactid']}'");
			while($row = mysqli_fetch_assoc($result)) {
				$role = $row['role'];
			}
			if(stripos(','.$role.',',',super,') !== false) {
				$level_url = 'admin';
			} else {
				$level_url = explode(',',trim($role,','))[0];
			}
		}
		$software_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `theme` FROM `field_config_security_level_theme` WHERE `security_level` = '$level_url'"))['theme'];
		?>
		<div class="form-group">
			<label for="travel_task" class="col-sm-4 control-label">Select the Security Level you wish to set the default theme for:</label>
			<div class="col-sm-8">
			<select id="sub_category" name="sub_category" class="chosen-select-deselect form-control" width="380">
				<option value=''></option>
				<?php foreach($on_security as $security_name => $value)  { ?>
					<option <?php echo ($value == $level_url ? 'selected' : '').' '.($value == 'super' ? 'disabled' : ''); ?> value="<?php echo $value; ?>"><?= $security_name ?></option>
				<?php } ?>
			</select>
		  </div>
		</div><div class='clearfix gap-bottom'></div>
	<?php } else if($_GET['subtab'] == 'software') {
		$software_config = get_config($dbc, 'software_default_theme');
	} else {
		$contactidfortile = $_SESSION['contactid'];
		$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactidfortile'");
	    while($row = mysqli_fetch_assoc($result)) {
			$software_config = $row['software_styler_choice'];
	    }
	}
        ?>
		<!-- If you change anything here, it should also be changed in the login_page_style.php, header.php, admin_software_config.php as well. -->
        <table class='table table-bordered table-striped' id="caltbl">
            <thead>
                <tr class='hidden-sm '>
                    <th  data-title="Software Style">Available Software Styles</th>
                    <th  data-title="Activation">Activate Style</th>
                </tr>
            </thead>
			<tr>
				<td>Default</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == '') { echo "checked"; } ?> value=''></td>
			</tr>
			<tr>
				<td>Black</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'washt') { echo "checked"; } ?> value='washt'></td>
			</tr>
			<tr>
				<td>Black & Orange</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blackorange') { echo "checked"; } ?> value='blackorange'></td>
			</tr>
			<tr>
				<td>Black & Gold</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blackgold') { echo "checked"; } ?> value='blackgold'></td>
			</tr>
			<tr>
				<td>Black & Purple</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blackpurple') { echo "checked"; } ?> value='blackpurple'></td>
			</tr>
			<tr>
				<td>Black & Red</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blackred') { echo "checked"; } ?> value='blackred'></td>
			</tr>
			<tr>
				<td>Black & Turquoise</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'turq') { echo "checked"; } ?> value='turq'></td>
			</tr>
			<tr>
				<td>Black Neon (Blue)</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blackneon') { echo "checked"; } ?> value='blackneon'></td>
			</tr>
			<tr>
				<td>Black Neon (Red)</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blackneonred') { echo "checked"; } ?> value='blackneonred'></td>
			</tr>
			<tr>
				<td>Break the Barrier</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'btb') { echo "checked"; } ?> value='btb'></td>
			</tr>
			<tr>
				<td>Chrome</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'chrome') { echo "checked"; } ?> value='chrome'></td>
			</tr>
			<tr>
				<td>Clinic Ace</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'bgw') { echo "checked"; } ?> value='bgw'></td>
			</tr>
			<tr>
				<td>Clouds</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'clouds') { echo "checked"; } ?> value='clouds'></td>
			</tr>
			<tr>
				<td>Cosmic</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'cosmos') { echo "checked"; } ?> value='cosmos'></td>
			</tr>
			<tr>
				<td>Cotton Candy</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'purp') { echo "checked"; } ?> value='purp'></td>
			</tr>
			<tr>
				<td>Custom Delivery</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'transport') { echo "checked"; } ?> value='transport'></td>
			</tr>
			<tr>
				<td>Dots</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'dots') { echo "checked"; } ?> value='dots'></td>
			</tr>
			<tr>
				<td>Flowers</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'flowers') { echo "checked"; } ?> value='flowers'></td>
			</tr>
			<tr>
				<td>Fresh Focus Media</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'ffm') { echo "checked"; } ?> value='ffm'></td>
			</tr>
			<tr>
				<td>FFM White</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'ffmwhite') { echo "checked"; } ?> value='ffmwhite'></td>
			</tr>
			<tr>
				<td>Garden</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'garden') { echo "checked"; } ?> value='garden'></td>
			</tr>
			<tr>
				<td>Green</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'green') { echo "checked"; } ?> value='green'></td>
			</tr>
			<tr>
				<td>Green & Grey</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'silver') { echo "checked"; } ?> value='silver'></td>
			</tr>
			<tr>
				<td>intuaTrack</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'intuatrack') { echo "checked"; } ?> value='intuatrack'></td>
			</tr>
			<tr>
				<td>Leopard Print</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'leo') { echo "checked"; } ?> value='leo'></td>
			</tr>
			<tr>
				<td>Navy</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'navy') { echo "checked"; } ?> value='navy'></td>
			</tr>
			<tr>
				<td>Orange &amp; Blue</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'orangeblue') { echo "checked"; } ?> value='orangeblue'></td>
			</tr>
			<tr>
				<td>Pink Dots</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'pinkdots') { echo "checked"; } ?> value='pinkdots'></td>
			</tr>
			<tr>
				<td>Polka Dots</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'polka') { echo "checked"; } ?> value='polka'></td>
			</tr>
			<tr>
				<td>Precision Workflow (Black)</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'bwr') { echo "checked"; } ?> value='bwr'></td>
			</tr>
			<tr>
				<td>Precision Workflow (White)</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'swr') { echo "checked"; } ?> value='swr'></td>
			</tr>
			<tr>
				<td>Realtor Navigator (Dark)</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'realtordark') { echo "checked"; } ?> value='realtordark'></td>
			</tr>
			<tr>
				<td>Realtor Navigator (Light)</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'realtorlight') { echo "checked"; } ?> value='realtorlight'></td>
			</tr>
			<tr>
				<td>Red &amp; Silver</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'redsilver') { echo "checked"; } ?> value='redsilver'></td>
			</tr>
			<tr>
				<td>ROOK Connect</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'blw') { echo "checked"; } ?> value='blw'></td>
			</tr>
			<tr>
				<td>Smiley Faces</td>
				<td><input type='radio' onclick="handleClick(this);" name='styler' style='width:20px; height:20px;' <?php if($software_config == 'happy') { echo "checked"; } ?> value='happy'></td>
			</tr>

        </table>

<input type='hidden' value='<?php echo $_SESSION['contactid']; ?>' class='contacterid'>
	</div>
</div>
