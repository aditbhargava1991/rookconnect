<?php 
// Regions and Location Access
// Contacts Security
$contact_security = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_security` WHERE `contactid`='$contactid'"));

// Regions
$contact_regions = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
$allowed_regions = array_filter(explode('#*#',$contact_security['region_access']));
if(count($allowed_regions) == 0) {
    $allowed_regions = $contact_regions;
}

// Locations
$contact_locations = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
$allowed_locations = array_filter(explode('#*#',$contact_security['location_access']));
if(count($allowed_locations) == 0) {
    $allowed_locations = $contact_locations;
}

// Security access query
$access_query_where = '';
$access_query = '';
if(count($contact_regions) > 0) {
	if (count($allowed_regions) > 0) {
	    $all_allowed = "'%*#*".implode("*#*%' OR CONCAT('*#*',`equipment`.`region`,'*#*') LIKE '%*#*", $allowed_regions)."*#*%'";
	    $all_allowed = "(`equipment`.`region` IS NULL OR `equipment`.`region` = '' OR CONCAT('*#*',`equipment`.`region`,'*#*') LIKE ".$all_allowed.")";
	} else {
		$all_allowed = "(`equipment`.`region` IS NULL OR `equipment`.`region` = '')";
	}
    $access_query_where .= " WHERE ".$all_allowed;
    $access_query .= " AND ".$all_allowed;
}
if(count($contact_locations) > 0) {
	if (count($allowed_locations) > 0) {
	    $all_allowed = "'%*#*".implode("*#*%' OR CONCAT('*#*',`equipment`.`location`,'*#*') LIKE '%*#*", $allowed_locations)."*#*%'";
	    $all_allowed = "(`equipment`.`location` IS NULL OR `equipment`.`location` = '' OR CONCAT('*#*',`equipment`.`location`,'*#*') LIKE ".$all_allowed.")";
	} else {
		$all_allowed = "(`equipment`.`location` IS NULL OR `equipment`.`location` = '')";
	}
    $access_query_where .= " AND ".$all_allowed;
    $access_query .= " AND ".$all_allowed;
}
?>