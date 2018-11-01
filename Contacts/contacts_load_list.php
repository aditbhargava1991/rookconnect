<?php include_once('../include.php');
ob_clean();

$folder_name = $_POST['folder'];
$lists = array_filter(explode(',',get_config($dbc, $folder_name.'_tabs')));
$hide_lists = ['Staff'];
foreach($lists as $i => $list_name) {
	if($list_name == 'Staff' || !check_subtab_persmission($dbc, $security_folder, ROLE, $list_name)) {
		$hide_lists[] = $list_name;
		unset($lists[$i]);
	}
}
$lists = array_values($lists);
$hide_lists = array_values($hide_lists);

$contact_sql = "SELECT * FROM contacts WHERE `category` NOT IN ('".implode("','",$hide_lists)."') AND `tile_name`='".$folder_name."' AND `deleted` = 0";

$contact_list = sort_contacts_query(mysqli_query($dbc, $contact_sql));
$contacts = [];
foreach($contact_list as $contact) {
	$match_staffs = [];
	$match_staff_list = mysqli_query($dbc, "SELECT * FROM `match_contact` WHERE `deleted` = 0 AND CONCAT(',',`support_contact`,',') LIKE '%,".$contact['contactid'].",%'");
	while($match_staff = mysqli_fetch_assoc($match_staff_list)) {
		foreach(explode(',', $match_staff['staff_contact']) as $staff_contact) {
			if(!in_array($staff_contact, $match_staffs)) {
				$match_staffs[] = $staff_contact;
			}
		}
	}
	$regions = [];
	foreach(explode(',', $contact['region']) as $contact_region) {
		$regions[] = $contact_region;
	}
	$locations = [];
	foreach(explode(',', $contact['con_locations']) as $contact_location) {
		$locations[] = $contact_location;
	}
	$classifications = [];
	foreach(explode(',', $contact['classification']) as $contact_classification) {
		$classifications[] = $contact_classification;
	}
	if(!in_array($contact['category'],$lists) || empty($contact['category'])) {
		$contact['category'] = 'Uncategorized';
	}

	$contacts[] = [
		'id'=>$contact['contactid'],
		'category'=>$contact['category'],
		'status'=>$contact['status'],
		'region'=>$regions,
		'location'=>$locations,
		'classification'=>$classifications,
		'title'=>$contact['title'],
		'match_staffs'=>$match_staffs,
		'search_string'=>strtolower($contact['contactid'].$contact['first_name'].$contact['last_name'].$contact['name'].$contact['email_address'].$contact['office_phone'].$contact['role'])
	];
}
echo json_encode($contacts);