<?php
/*
Customer Listing
*/
include_once('../include.php');


if (isset($_POST['add_style'])) {
       //Task Status
	$loginstyle = $_POST['loginstyle'];
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='login_style'"));
	if($get_config['configid'] > 0) {
		$query_update_employee = "UPDATE `general_configuration` SET value = '$loginstyle' WHERE name='login_style'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
	} else {
		$query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('login_style', '$loginstyle')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
	}
}

$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_software_login_page'"));
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

<!--<h2>Styling for the Login Page</h2>-->
<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Login Page Style:</label>
		<div class="col-sm-8">
		  <select data-placeholder="Choose style" name="loginstyle" class="chosen-select-deselect form-control inventoryid" width="380">
						<option value=''></option>
						<?php
					   //Get style
						$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='login_style'"));
						if($get_config['configid'] > 0) {
							$get_style = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM general_configuration WHERE name='login_style'"));
							$get_style_file = $get_style['value'];
						} else {
							$get_style_file = '';
						}
						?>
					<option <?php if($get_style_file == 'washt') { echo "selected"; } ?> value="washt">Black</option>
					<option <?php if($get_style_file == 'blackorange') { echo "selected"; } ?> value="blackorange">Black & Orange</option>
					<option <?php if($get_style_file == 'blackgold') { echo "selected"; } ?> value="blackgold">Black & Gold</option>
					<option <?php if($get_style_file == 'blackpurple') { echo "selected"; } ?> value="">Black & Purple</option>
					<option <?php if($get_style_file == 'turq') { echo "selected"; } ?> value="turq">Black & Turquoise</option>
					<option <?php if($get_style_file == 'blackneon') { echo "selected"; } ?> value="blackneon">Black Neon (Blue)</option>
					<option <?php if($get_style_file == 'blackneonred') { echo "selected"; } ?> value="blackneonred">Black Neon (Red)</option>
					<option <?php if($get_style_file == 'btb') { echo "selected"; } ?> value="btb">Break the Barrier</option>
					<option <?php if($get_style_file == 'chrome') { echo "selected"; } ?> value="chrome">Chrome</option>
					<option <?php if($get_style_file == 'bgw') { echo "selected"; } ?> value="bgw">Clinic Ace</option>
					<option <?php if($get_style_file == 'clouds') { echo "selected"; } ?> value="clouds">Clouds</option>
					<option <?php if($get_style_file == 'cosmos') { echo "selected"; } ?> value="cosmos">Cosmic</option>
					<option <?php if($get_style_file == 'purp') { echo "selected"; } ?> value="purp">Cotton Candy</option>
					<option <?php if($get_style_file == 'dots') { echo "selected"; } ?> value="dots">Dots</option>
					<option <?php if($get_style_file == 'flowers') { echo "selected"; } ?> value="flowers">Flowers</option>
					<option <?php if($get_style_file == 'ffm') { echo "selected"; } ?> value="ffm">Fresh Focus Media</option>
					<option <?php if($get_style_file == 'ffmwhite') { echo "selected"; } ?> value="ffmwhite">FFM White</option>
					<option <?php if($get_style_file == 'garden') { echo "selected"; } ?>  value="garden">Garden</option>
					<option <?php if($get_style_file == 'green') { echo "selected"; } ?>  value="green">Green</option>
					<option <?php if($get_style_file == 'silver') { echo "selected"; } ?> value="silver">Green & Grey</option>
					<option <?php if($get_style_file == 'intuatrack') { echo "selected"; } ?>  value="intuatrack">intuaTrack</option>
					<option <?php if($get_style_file == 'leo') { echo "selected"; } ?> value="polka">Leopard Print</option>
					<option <?php if($get_style_file == 'navy') { echo "selected"; } ?> value="navy">Navy</option>
					<option <?php if($get_style_file == 'orangeblue') { echo "selected"; } ?> value="orangeblue">Orange & Blue</option>
					<option <?php if($get_style_file == 'pinkdots') { echo "selected"; } ?> value="pinkdots">Pink Dots</option>
					<option <?php if($get_style_file == 'polka') { echo "selected"; } ?> value="polka">Polka Dots</option>
					<option <?php if($get_style_file == 'bwr') { echo "selected"; } ?> value="bwr">Precision Workflow (Black)</option>
					<option <?php if($get_style_file == 'swr') { echo "selected"; } ?> value="swr">Precision Workflow (White)</option>
					<option <?php if($get_style_file == 'realtordark') { echo "selected"; } ?> value="realtordark">Realtor Navigator (Dark)</option>
					<option <?php if($get_style_file == 'realtorlight') { echo "selected"; } ?> value="realtorlight">Realtor Navigator (Light)</option>
					<option <?php if($get_style_file == 'redsilver') { echo "selected"; } ?> value="redsilver">Red &amp; Silver</option>
					<option <?php if($get_style_file == '' || $get_style_file == 'blw') { echo "selected"; } ?> value="blw">ROOK Connect</option>
					<option <?php if($get_style_file == 'happy') { echo "selected"; } ?> value="happy">Smiley Faces</option>
					<option <?php if($get_style_file == 'transport') { echo "selected"; } ?> value="transport">Transport</option>
		  </select>
		</div>
	</div>
	<br><br>
	<div class="form-group">
		<!--<div class="col-sm-4 clearfix">
			<a href="admin_software_config.php" class="btn config-btn pull-right">Back</a>
			<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>--
		</div>-->
		<div class="col-sm-12">
			<button	type="submit" name="add_style" value="add_style" class="btn config-btn btn-lg	pull-right">Submit</button>
		</div>
	</div>
</form>