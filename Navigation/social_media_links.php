<?php 
	$resulterr = mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='facebook_link'");
	$get_config = mysqli_fetch_assoc($resulterr);
	$numy_rowsers = mysqli_num_rows($resulterr);
	if($numy_rowsers > 0 && $get_config['value'] !== '' && $get_config['value'] !== NULL) {
		$facebook_link = $get_config['value'];
    } else {
		$facebook_link = 'turn_off';
	}
	$resulterr = mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='google_link'");
	$get_config = mysqli_fetch_assoc($resulterr);
	$numy_rowsers = mysqli_num_rows($resulterr);
	if($numy_rowsers > 0 && $get_config['value'] !== '' && $get_config['value'] !== NULL) {
		$google_link = $get_config['value'];
    } else {
		$google_link = 'turn_off';
	}
	$resulterr = mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='twitter_link'");
	$get_config = mysqli_fetch_assoc($resulterr);
	$numy_rowsers = mysqli_num_rows($resulterr);
	if($numy_rowsers > 0 && $get_config['value'] !== '' && $get_config['value'] !== NULL) {
		$twitter_link = $get_config['value'];
    } else {
		$twitter_link = 'turn_off';
	}
	$resulterr = mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='linkedin_link'");
	$get_config = mysqli_fetch_assoc($resulterr);
	$numy_rowsers = mysqli_num_rows($resulterr);
	if($numy_rowsers > 0 && $get_config['value'] !== '' && $get_config['value'] !== NULL) {
		$linkedin_link = $get_config['value'];
    } else {
		$linkedin_link = 'turn_off';
	}
	$resulterr = mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='youtube_link'");
	$get_config = mysqli_fetch_assoc($resulterr);
	$numy_rowsers = mysqli_num_rows($resulterr);
	if($numy_rowsers > 0 && $get_config['value'] !== '' && $get_config['value'] !== NULL) {
		$youtube_link = $get_config['value'];
    } else {
		$youtube_link = 'turn_off';
	}
	$resulterr = mysqli_query($dbc,"SELECT value FROM general_configuration WHERE name='instagram_link'");
	$get_config = mysqli_fetch_assoc($resulterr);
	$numy_rowsers = mysqli_num_rows($resulterr);
	if($numy_rowsers > 0 && $get_config['value'] !== '' && $get_config['value'] !== NULL) {
		$instagram_link = $get_config['value'];
    } else {
		$instagram_link = 'turn_off';
	}
?>	
	<?php 
	$login_page = $_SERVER['PHP_SELF'] == '/index.php';
	if(!$login_page && ($_SESSION['contactid'] > 0 || $guest_access || isset($_SESSION['intake_ticket']))) {
		$social_off = false;
        if($facebook_link !== 'turn_off' || $linkedin_link !== 'turn_off' || $twitter_link !== 'turn_off' || $google_link !== 'turn_off' || $youtube_link !== 'turn_off' || $instagram_link !== 'turn_off') {
            $social_off = true; ?>
            <span class="popover-examples list-inline tooltip-navigation pull-left"><a style="margin:5px 5px 0 0;" class="info_i_sm" data-toggle="tooltip" data-placement="top" title="Social Media links are editable through the Admin Settings tile. If you would like to change these social media links, but don't have access to the Admin Settings tile, then please contact your Software Admins."><img src="<?php echo WEBSITE_URL; ?>/img/info-w.png" width="20"></a></span><?php 
		}
	} ?>
    <?= $social_off==true ? '<ul class="social-media pull-left">' : ''; ?>
        <?php if($facebook_link !== 'turn_off') { ?>
            <li><a href="<?php echo $facebook_link; ?>" <?php if($login_page) { echo 'style=" background-color: lightgrey;"'; } ?> class="social-icon facebook hide-text" target="_blank">Facebook</a></li>
        <?php } ?>
        <?php if($linkedin_link !== 'turn_off') { ?>
            <li><a href="<?php echo $linkedin_link; ?>" <?php if($login_page) { echo 'style=" background-color: lightgrey;"'; } ?> class="social-icon linkedin hide-text" target="_blank">LinkedIn</a></li>
        <?php } ?>
        <?php if($twitter_link !== 'turn_off') { ?>
            <li><a href="<?php echo $twitter_link; ?>" <?php if($login_page) { echo 'style=" background-color: lightgrey;"'; } ?> class="social-icon twitter hide-text" target="_blank">Twitter</a></li>
        <?php } ?>
        <?php if($google_link !== 'turn_off') { ?>
            <li><a href="<?php echo $google_link; ?>" <?php if($login_page) { echo 'style=" background-color: lightgrey;"'; } ?> class="social-icon google hide-text" target="_blank">Google+</a></li>
        <?php } ?>
        <?php if($youtube_link !== 'turn_off') { ?>
            <li><a href="<?php echo $youtube_link; ?>" <?php if($login_page) { echo 'style=" background-color: lightgrey;"'; } ?> class="social-icon youtube hide-text" target="_blank">Youtube</a></li>
        <?php } ?>
        <?php if($instagram_link !== 'turn_off') { ?>
            <li><a href="<?php echo $instagram_link; ?>" <?php if($login_page) { echo 'style=" background-color: lightgrey;"'; } ?> class="social-icon instagram hide-text" target="_blank">Instagram</a></li>
        <?php } ?>
    <?= $social_off==true ? '</ul>' : ''; ?>