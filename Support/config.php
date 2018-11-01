<?php include_once('../include.php');
$dbc_support = mysqli_connect('localhost', 'ffm_rook_user', 'mIghtyLion!542', 'ffm_rook_db');
// $dbc_support = mysqli_connect('localhost', 'root', 'FreshFocus007', 'local_1_rook');

$user = get_config($dbc, 'company_name');
$url = WEBSITE_URL;
$user_name = $user;
$user_category = '';
$ticket_types = explode(',',get_config($dbc_support,'ticket_tabs'));
$security = get_security($dbc, 'customer_support');
if($user == 'ROOK Connect' && $_SERVER['SERVER_NAME'] == 'ffm.rookconnect.com') {
	$user = $_SESSION['contactid'];
	$user_name = get_contact($dbc, $user);
	$user_category = get_contact($dbc, $user, 'category');
} else {
	$user = mysqli_fetch_array(mysqli_query($dbc_support, "SELECT * FROM `contacts` WHERE ('".WEBSITE_URL."' LIKE CONCAT('%',`website`) AND IFNULL(`website`,'') NOT LIKE '') OR `software_url`='".WEBSITE_URL."' OR `name`='".encryptIt($user)."' ORDER BY `software_url`='".WEBSITE_URL."' DESC, `name`='".encryptIt($user)."'"))['contactid'];
	$user_category = 'REMOTE_'.get_contact($dbc, $_SESSION['contactid'], 'category');
	if($user_category != 'REMOTE_Staff') {
		$user_category = 'USER_CUSTOMER';
		$user = $_SESSION['contactid'];
		$name = get_contact($dbc, $user);
		$user_name = ($name == '' ? get_client($dbc, $user) : $name);
		$dbc_support = $dbc;
	}
}
$tab_list = explode(',',get_config($dbc_support,'cust_support_tab_list'));
if(!in_array('new',$tab_list)) {
    $tab_list[] = 'new';
}
$current_tab = (empty($_GET['tab']) ? ($user_category == 'USER_CUSTOMER' && in_array('customer',$tab_list) ? 'customer' : 'requests') : $_GET['tab']);
$request_tab = (!empty($_GET['type']) ? $_GET['type'] : (in_array('closed',$tab_list) ? 'closed' : 'new'));

$request_tab_name = 'Feedback &amp; Ideas';
foreach($ticket_types as $type) {
    if(config_safe_str($type) == $request_tab) {
        $request_tab_name = $type;
    }
}
if($request_tab == 'closed') {
    $request_tab_name = 'Closed Requests';
} else if($request_tab == 'closed') {
    $request_tab_name = 'Closed Requests';
} else if($request_tab == 'new') {
    $request_tab_name = 'Add New Request';
    foreach($ticket_types as $type_name) {
        if($_GET['new_type'] == config_safe_str($type_name)) {
            $request_tab_name .= ' '.$type_name;
        }
    }
}
