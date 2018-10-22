<?php // Functions to output details onto pages
// Callback function that can be used to get the html for the profile id
function profile_callback($contactid) {
	return profile_id($_SERVER['DBC'], $contactid[1], false);
}
// Callback function that can be used to output the profile id
function profile_call_echo($contactid) {
	profile_id($_SERVER['DBC'], $contactid[1]);
}
// Output or Return a circle that has either an image or the user's initials
function profile_id($dbc, $contactid, $echo = true, $class) {
	$output = '';
	// Make sure the contactid passed is valid
	if(!(intval($contactid) > 0)) {
		$contactid = 0;
	} else {
		$contactid = intval($contactid);
	}
	// Check for an avatar chosen by the user
	$profile_photo = WEBSITE_URL."/Profile/download/profile_pictures/".$contactid.".jpg";
	// Check if an image has been uploaded
	if(url_exists($profile_photo)) {
		$user = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `first_name`, `last_name`, `name` FROM `contacts` LEFT JOIN `user_settings` ON `contacts`.`contactid`=`user_settings`.`contactid` WHERE `contacts`.`contactid` = '$contactid'"));
        $output = '<img class="id-circle no-toggle '.$class.'" src="'.$profile_photo.'" title="'.decryptIt($user['name']).decryptIt($user['first_name']).' '.decryptIt($user['last_name']).'" data-placement="bottom">';
	} else {
		// If no image has been uploaded, and an avatar has been selected, use the avatar
		$user = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `preset_profile_picture`, `first_name`, `last_name`, `name`, `initials`, `calendar_color` FROM `contacts` LEFT JOIN `user_settings` ON `contacts`.`contactid`=`user_settings`.`contactid` WHERE `contacts`.`contactid` = '$contactid'"));
		if(!empty($user['preset_profile_picture']) && url_exists(WEBSITE_URL.'/img/avatars/'.$user['preset_profile_picture'])) {
			$output = '<img class="id-circle no-toggle '.$class.'" title="'.decryptIt($user['name']).decryptIt($user['first_name']).' '.decryptIt($user['last_name']).'" src="'.WEBSITE_URL.'/img/avatars/'.$user['preset_profile_picture'].'" data-placement="bottom">';
		// If nothing else has been set, use the contact's initials
		} else {
			$initials = ($user['initials'] == '' ? ($user['first_name'].$user['last_name'].$user['name'] == '' ? '?' : substr(decryptIt($user['name']),0,1).substr(decryptIt($user['first_name']),0,1).substr(decryptIt($user['last_name']),0,1)) : $user['initials']);
			$colour = ($user['calendar_color'] == '' ? '#6DCFF6' : $user['calendar_color']);
			$output = '<span title="'.decryptIt($user['name']).decryptIt($user['first_name']).' '.decryptIt($user['last_name']).'" class="id-circle no-toggle '.$class.'" data-placement="bottom" style="background-color:'.$colour.'; font-family: \'Open Sans\';">'.(!empty($initials) ? $initials : '&nbsp;').'</span>';
		}
	}
	if($echo) {
		echo $output;
	} else {
		return $output;
	}
}

// Return the notes for the specific subtab that you are currently on
function software_notes($tile, $subtab) {
	// Use the details from to connect to the HowToGuide database
	include('How To Guide/db_conn_htg.php');
	$tile = filter_var($tile,FILTER_SANITIZE_STRING);
	$subtab = filter_var($subtab,FILTER_SANITIZE_STRING);
	// Get the notes for the correct tile and subtab
	$notes = mysqli_fetch_assoc(mysqli_query($dbc_htg, "SELECT `description` FROM `notes` WHERE `tile`='$tile' AND `subtab`='$subtab' AND `deleted`=0"));
	// Replace the notes with the correct wording
	$notes = str_replace(['[PROJECT TILE]','[PROJECT]','[AFTER_PROJECT]','[TICKET TILE]','[TICKET]','[COMPANY]','[SOFTWARE URL]','[BUSINESS]','[SITE]'],
		[PROJECT_TILE,PROJECT_NOUN,AFTER_PROJECT,TICKET_TILE,TICKET_NOUN,COMPANY_NAME,WEBSITE_URL,BUSINESS_CAT,SITES_CAT],
		html_entity_decode($notes['description']));
	if($notes == '') {
		return '';
	}
	return '<div class="notice double-gap-bottom popover-examples">
			<div class="pull-left"><img src="'.WEBSITE_URL.'/img/info.png" class="wiggle-me inline-img"></div>
			<div class="notice-text"><span class="notice-name">NOTE:</span>'.$notes.'</div>
			<div class="clearfix"></div>
		</div>';
}

// Get the Text Templates for a field, and display them in a dropdown
// When they are selected, they should fill the next text area
function get_text_templates($dbc, $tile, $tab, $field) {
	$templates = mysqli_query($dbc, "SELECT * FROM `text_templates` WHERE `tile`='$tile' AND `tab`='$tab' AND `field`='$field' AND `deleted`=0 ORDER BY `sort`");
	if($templates->num_rows > 0) {
		$output = '<select name="template_'.$field.'" data-placeholder="Select a Template" class="chosen-select-deselect" onchange="applyTemplate(this);"><option></option>';
		while($template = $templates->fetch_assoc()) {
			$output .= '<option value="'.$template['template'].'">'.$template['description'].'</option>';
		}
		$output .= '</select>';
	} else {
		$output = '';
	}
	return $output;
}