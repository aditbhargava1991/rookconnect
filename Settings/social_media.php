<?php
/*
Customer Listing
*/
include_once('../include.php');

if (isset($_POST['add_social_media'])) {

    $facebook_link = filter_var($_POST['facebook'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='facebook_link'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$facebook_link' WHERE name='facebook_link'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('facebook_link', '$facebook_link')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$google_link = filter_var($_POST['googleplus'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='google_link'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$google_link' WHERE name='google_link'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('google_link', '$google_link')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$twitter_link = filter_var($_POST['twitter'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='twitter_link'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$twitter_link' WHERE name='twitter_link'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('twitter_link', '$twitter_link')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$linkedin_link = filter_var($_POST['linkedin'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='linkedin_link'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$linkedin_link' WHERE name='linkedin_link'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('linkedin_link', '$linkedin_link')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$youtube_link = filter_var($_POST['youtube'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='youtube_link'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$youtube_link' WHERE name='youtube_link'";
        $result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('youtube_link', '$youtube_link')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }

	$instagram_link = filter_var($_POST['instagram'],FILTER_SANITIZE_STRING);
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(configid) AS configid FROM general_configuration WHERE name='instagram_link'"));
    if($get_config['configid'] > 0) {
        $query_update_employee = "UPDATE `general_configuration` SET value = '$instagram_link' WHERE name='instagram_link'";
		$result_update_employee = mysqli_query($dbc, $query_update_employee);
    } else {
        $query_insert_config = "INSERT INTO `general_configuration` (`name`, `value`) VALUES ('instagram_link', '$instagram_link')";
        $result_insert_config = mysqli_query($dbc, $query_insert_config);
    }
}

$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='setting_social_media_links'"));
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

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Facebook:</label>
		<div class="col-sm-8">
		  <input name="facebook" type="text" placeholder="https://www.facebook.com/FreshFocusMediaYYC" value="<?php echo get_config($dbc, 'facebook_link'); ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Google Plus:</label>
		<div class="col-sm-8">
		  <input name="googleplus" type="text" placeholder="https://plus.google.com/+Freshfocusmediayyc/posts" value="<?php echo get_config($dbc, 'google_link'); ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">LinkedIn:</label>
		<div class="col-sm-8">
		  <input name="linkedin" type="text" placeholder="https://www.linkedin.com/company/fresh-focus-media" value="<?php echo get_config($dbc, 'linkedin_link'); ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Twitter:</label>
		<div class="col-sm-8">
		  <input name="twitter" type="text" placeholder="https://twitter.com/freshfocusmedia" value="<?php echo get_config($dbc, 'twitter_link'); ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">YouTube:</label>
		<div class="col-sm-8">
		  <input name="youtube" type="text" placeholder="https://www.youtube.com/watch?v=e69mpxH0pzw" value="<?php echo get_config($dbc, 'youtube_link'); ?>" class="form-control"/>
		</div>
	</div>
	<div class="form-group">
		<label for="fax_number"	class="col-sm-4	control-label">Instagram:</label>
		<div class="col-sm-8">
		  <input name="instagram" type="text" placeholder="https://www.instagram.com/freshfocusmedia" value="<?php echo get_config($dbc, 'instagram_link'); ?>" class="form-control"/>
		</div>
	</div>
	<br><br>
	<div class="form-group">
		<div class="col-sm-12">
			<button	type="submit" name="add_social_media" value="add_social_media" class="btn config-btn btn-lg	pull-right">Submit</button>
		</div>
	</div>
</form>