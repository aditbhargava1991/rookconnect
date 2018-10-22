<?php include('include.php');

//If main Contacts tile is already enabled, we will need to keep the data, otherwise we delete all data
if(tile_enabled($dbc, 'contacts_inbox')) {
	$contacts_tabs = explode(',',get_config($dbc, 'contacts_tabs'));
} else {
	$contacts_tabs = []; //empty Contacts tab if the tile isn't enabled
	$main_contacts_tabs = [];
	// mysqli_query($dbc, "UPDATE `contacts` SET `deleted` = 1 WHERE `tile_name` = 'contacts' AND `category` != 'Staff'");
}
$main_contacts_tabs = $contacts_tabs;

//All other contacts tiles in an array
$tiles = [
	'members'=>['members', 'Members', 'Member'],
	'client_info'=>['clientinfo', 'Client Information', 'Client'],
	'contacts_rolodex'=>['contactsrolodex'],
	'contacts3'=>['contacts3']
];

foreach($tiles as $key => $tile) {
	//For each tile, we need to merge the contacts_tabs to the main contacts_tabs in case multiple tiles are turned on
	$contacts_tabs = array_merge($contacts_tabs, explode(',',get_config($dbc, $tile[0].'_tabs')));

	//Get all the field_config_contacts and change it into the main contacts tile_name. If main contacts tile is enabled and the category exists, then we will need to merge the fields together so there are no missing fields
	foreach(explode(',',get_config($dbc, $tile[0].'_tabs')) as $tab) {
		if(!empty($tab)) {
			if(in_array($tab, $main_contacts_tabs)) {
				//field_config_contacts
				$field_config = mysqli_query($dbc, "SELECT * FROM `field_config_contacts` WHERE `tile_name` = '".$tile[0]."' AND `tab` = '".$tile[0]."' AND (`subtab` = '**no_subtab**' OR `subtab` = 'additions')");
				while($row = mysqli_fetch_assoc($field_config)) {
					$config_exists = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_contacts` WHERE `tile_name` = 'contacts' AND `tab` = '".$tile[0]."' AND `subtab` = '".$row['subtab']."'"));
					if(!empty($config_exists)) {
						$contacts_fields = implode(',',array_filter(array_unique(array_merge(explode(',',$row['contacts']),explode(',',$config_exists['contacts']])))));
						$contacts_dashboard = implode(',',array_filter(array_unique(array_merge(explode(',',$row['contacts_dashboard']),explode(',',$config_exists['contacts_dashboard']])))));
						mysqli_query($dbc, "UPDATE `field_config_contacts` SET `contacts` = '$contacts_fields', `contacts_dashboard` = '$contacts_dashboard' WHERE `tile_name` = 'contacts' AND `tab` = '".$tile[0]."' AND AND `subtab` = '".$row['subtab']."'");
					} else {
						mysqli_query($dbc, "UPDATE `field_config_contacts` SET `tile_name` = 'contacts' WHERE `tile_name` = '".$tile[0]."' AND `tab` = '".$tile[0]."' AND AND `subtab` = '".$row['subtab']."'");
					}
				}

				//field_config_contacts_security
				$field_config = mysqli_query($dbc, "SELECT * FROM `field_config_contacts_security` WHERE `tile_name` = '".$tile[0]."' AND `category` = '".$tile[0]."' AND `deleted` = 0");
				while($row = mysqli_fetch_assoc($field_config)) {
					$config_exists = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_security` WHERE `tile_name` = 'contacts' AND `category` = '".$tile[0]."' AND `security_level` = '".$row['security_level']."' AND `deleted` = 0"));
					if(!empty($config_exists)) {
						$subtabs_hidden = implode(',',array_filter(array_unique(array_merge(explode(',',$row['subtabs_hidden']),explode(',',$config_exists['subtabs_hidden']])))));
						$subtabs_view_only = implode(',',array_filter(array_unique(array_merge(explode(',',$row['subtabs_view_only']),explode(',',$config_exists['subtabs_view_only']])))));
						$fields_hidden = implode(',',array_filter(array_unique(array_merge(explode(',',$row['fields_hidden']),explode(',',$config_exists['fields_hidden']])))));
						$fields_view_only = implode(',',array_filter(array_unique(array_merge(explode(',',$row['fields_view_only']),explode(',',$config_exists['fields_view_only']])))));

						//STOPPED HERE
						mysqli_query($dbc, "UPDATE `field_config_contacts` SET `contacts` = '$contacts_fields', `contacts_dashboard` = '$contacts_dashboard' WHERE `tile_name` = 'contacts' AND `tab` = '".$tile[0]."' AND AND `subtab` = '".$row['subtab']."'");
					} else {
						mysqli_query($dbc, "UPDATE `field_config_contacts` SET `tile_name` = 'contacts' WHERE `tile_name` = '".$tile[0]."' AND `tab` = '".$tile[0]."' AND AND `subtab` = '".$row['subtab']."'");
					}
				}
			} else {
				//field_config_contacts
				mysqli_query($dbc, "DELETE FROM `field_config_contacts` WHERE `tile_name` = 'contacts' AND `tab` = '".$tile[0]."' AND (`subtab` = '**no_subtab**' OR `subtab` = 'additions')");
				mysqli_query($dbc, "UPDATE `field_config_contacts` SET `tile_name` = 'contacts' WHERE `tile_name` = '".$tile[0]."' AND `tab` = '".$tile[0]."' AND (`subtab` = '**no_subtab**' OR `subtab` = 'additions')");
				//field_config_contacts_security
				mysqli_query($dbc, "DELETE FROM `field_config_contacts_security` WHERE `tile_name` = 'contacts' AND `category` = '".$tile[0]."'");
				mysqli_query($dbc, "UPDATE `field_config_contacts_security` SET `tile_name` = 'contacts' WHERE `tile_name` = '".$tile[0]."' AND `category` = '".$tile[0]."'");

			}
		}
	}
}

$contacts_tabs = array_filter(array_unique($contacts_tabs));