<?php include_once('../include.php');
ob_clean();

//Exclude George & FFM from showing up on SEA Contacts
if($rookconnect == 'sea') {
	$sea_constraint = " AND (user_name!='FFMAdmin' AND user_name!='georgev' AND user_name!='salimc' OR user_name IS NULL) ";
} else {
	$sea_constraint = '';
}

$staff_sql = "SELECT * FROM `contacts` WHERE `category`='Staff' AND IFNULL(`user_name`,'')!='FFMAdmin' AND `deleted`=0 AND `show_hide_user`='1'".$sea_constraint;

$staff_list = sort_contacts_query(mysqli_query($dbc, $staff_sql));
$staffs = [];
foreach($staff_list as $staff) {
	$match_contacts = [];
	$match_contact_list = mysqli_query($dbc, "SELECT * FROM `match_contact` WHERE `deleted` = 0 AND CONCAT(',',`staff_contact`,',') LIKE '%,".$staff['contactid'].",%'");
	while($match_contact = mysqli_fetch_assoc($match_contact_list)) {
		foreach(explode(',', $match_contact['support_contact']) as $support_contact) {
			if(!in_array($support_contact, $match_contacts)) {
				$match_contacts[] = $support_contact;
			}
		}
	}
	$staff_categories = [];
	foreach(explode(',', $staff['staff_category']) as $staff_category) {
		if(!empty($staff_category)) {
			$staff_categories[] = $staff_category;
		}
	}
	$staffs[] = [
		'id'=>$staff['contactid'],
		'status'=>$staff['status'],
		'staff_category'=>$staff_categories,
		'match_contacts'=>$match_contacts,
		'search_string'=>strtolower($staff['contactid'].$staff['first_name'].$staff['last_name'].$staff['name'].$staff['email_address'].$staff['office_phone'].$staff['role'])
	];
}
echo json_encode($staffs);