<?php // Define all Tile Name Constants
// @$_SERVER['page_load_info'] .= 'Session Started: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
session_start();

if(!defined('FOLDER_NAME')) {
    $folder_path = $_SERVER['REQUEST_URI'];
    $each_tab = explode('/', $folder_path);

    DEFINE('FOLDER_NAME', strtolower($each_tab[1]));
    DEFINE('FOLDER_URL', $each_tab[1]);
}

if($_SESSION['CONSTANT_UPDATED'] + 600 < time()) {
	@session_start(['cookie_lifetime' => 518400]);
	// Update SESSION Constants no more than once every 600 seconds
	$inventory_tile_name = explode('#*#',get_config($dbc, 'inventory_tile_name') ?: 'Inventory#*#Inventory');
	$_SESSION['INVENTORY_TILE'] = $inventory_tile_name[0] ?: 'Inventory';
	$_SESSION['INVENTORY_NOUN'] = !empty($inventory_tile_name[1]) ? $inventory_tile_name[1] : ($inventory_tile_name[0] == 'Inventory' ? 'Inventory' : $inventory_tile_name[0]) ?: 'Inventory';

	$task_tile_name = explode('#*#',get_config($dbc, 'task_tile_name') ?: 'Task#*#Task');
	$_SESSION['TASK_TILE'] = $task_tile_name[0] ?: 'Task';
	$_SESSION['TASK_NOUN'] = !empty($task_tile_name[1]) ? $task_tile_name[1] : ($task_tile_name[0] == 'Task' ? 'Task' : $task_tile_name[0]) ?: 'Task';

	$contacts_tile_name = explode('#*#',get_config($dbc, 'contacts_tile_name') ?: 'Contacts#*#Contact');
	$_SESSION['CONTACTS_TILE'] = $contacts_tile_name[0] ?: 'Contacts';
	$_SESSION['CONTACTS_NOUN'] = !empty($contacts_tile_name[1]) ? $contacts_tile_name[1] : ($contacts_tile_name[0] == 'Contacts' ? 'Contact' : $contacts_tile_name[0]) ?: 'Contact';
	$_SESSION['BUSINESS_CAT'] = get_config($dbc, 'business_category');
	$_SESSION['SITES_CAT'] = get_config($dbc, 'site_category');

    $sales_tile_name = explode('#*#',get_config($dbc, 'sales_tile_name') ?: 'Sales#*#Sales Lead');
    $_SESSION['SALES_TILE'] = $sales_tile_name[0] ?: 'Sales';
    $_SESSION['SALES_NOUN'] = !empty($sales_tile_name[1]) ? $sales_tile_name[1] : ($sales_tile_name[0] == 'Sales' ? 'Sales Lead' : $sales_tile_name[0]) ?: 'Sales Lead';

    $sales_order_tile_name = explode('#*#',get_config($dbc, 'sales_order_tile_name') ?: 'Sales Orders#*#Sales Order');
    $_SESSION['SALES_ORDER_TILE'] = $sales_order_tile_name[0] ?: 'Sales Orders';
    $_SESSION['SALES_ORDER_NOUN'] = $sales_order_tile_name[1] ?: 'Sales Order';

	$project_tile_name = explode('#*#',get_config($dbc, 'project_tile_name') ?: 'Projects#*#Project');
	$_SESSION['PROJECT_TILE'] = $project_tile_name[0] ?: 'Projects';
	$_SESSION['PROJECT_NOUN'] = $project_tile_name[1] ?: ($_SESSION['PROJECT_TILE'] == 'Jobs' ? 'Job' : ($_SESSION['PROJECT_TILE'] == 'Projects' ? 'Project' : $_SESSION['PROJECT_TILE']));
	$_SESSION['PROJECT_LABEL'] = get_config($dbc, 'project_label');
	$_SESSION['PROJECT_TYPE_CODES'] = get_config($dbc, 'project_type_codes');
	$_SESSION['PROJECT_TYPES'] = get_config($dbc, 'project_tabs');
	$_SESSION['AFTER_PROJECT'] = get_config($dbc, 'next_step_after_project') ?: 'Ticket';

	$_SESSION['JOBS_TILE'] = get_config($dbc, 'jobs_tile_name') ?: 'Jobs';

	$pos_advance_tile_name = explode('#*#',get_config($dbc, 'pos_advance_tile_name') ?: 'Point of Sale#*#Invoice');
	$_SESSION['POS_ADVANCE_TILE'] = $pos_advance_tile_name[0] ?: 'Point of Sale';
	$_SESSION['POS_ADVANCE_NOUN'] = !empty($pos_advance_tile_name[1]) ? $pos_advance_tile_name[1] : ($pos_advance_tile_name[0] == 'Point of Sale' ? 'Invoice' : $pos_advance_tile_name[0]) ?: 'Invoice';

	$ticket_tile_name = explode('#*#',get_config($dbc, 'ticket_tile_name') ?: 'Tickets#*#Ticket');
	$_SESSION['TICKET_TILE'] = $ticket_tile_name[0] ?: 'Tickets';
	$_SESSION['TICKET_NOUN'] = $ticket_tile_name[1] ?: ($_SESSION['TICKET_TILE'] == 'Work Orders' ? 'Work Order' : ($_SESSION['TICKET_TILE'] == 'Tickets' ? 'Ticket' : $_SESSION['TICKET_TILE']));
	$_SESSION['TICKET_LABEL'] = get_config($dbc, 'ticket_label');
	$_SESSION['ESTIMATE_TILE'] = get_config($dbc, 'estimate_tile_name');
	$_SESSION['VENDOR_TILE'] = get_config($dbc, 'vendor_tile_name');

	$inc_rep_tile_name = explode('#*#',get_config($dbc, 'inc_rep_tile_name') ?: 'Incident Reports#*#Incident Report');
	$_SESSION['INC_REP_TILE'] = $inc_rep_tile_name[0] ?: 'Incident Reports';
	$_SESSION['INC_REP_NOUN'] = $inc_rep_tile_name[1] ?: ($_SESSION['INC_REP_TILE'] == 'Incident Reports' ? 'Incident Report' : $_SESSION['INC_REP_TILE']);
	$_SESSION['START_DAY'] = get_config($dbc, 'timesheet_start_tile');
	$_SESSION['END_DAY'] = get_config($dbc, 'timesheet_end_tile');

	// Define Company's software name:
	$_SESSION['COMPANY_SOFTWARE_NAME'] = get_config($dbc, 'company_name') ?: 'Fresh Focus Media';

	$_SESSION['ACTIVE_DAY_BANNER'] = get_config($dbc, 'timesheet_running_button');
	$_SESSION['ACTIVE_TICKET_BUTTON'] = get_config($dbc, 'active_ticket_button');
	$_SESSION['SHOW_SIGN_IN'] = get_config($dbc, 'timesheet_always_show');

	$_SESSION['CONSTANT_UPDATED'] = time();
	$_SERVER['page_load_info'] .= 'Constants Reloaded: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";
    $staff_email_field = get_config($dbc, 'staff_email_field');
    $staff_email_field = empty($staff_email_field) ? 'email_address' : $staff_email_field;
    $_SESSION['STAFF_EMAIL_FIELD'] = $staff_email_field;
	@session_write_close();
}
// Pull from SESSION instead of Database
DEFINE('INVENTORY_TILE', $_SESSION['INVENTORY_TILE']);
DEFINE('TASK_TILE', $_SESSION['TASK_TILE']);
DEFINE('TASK_NOUN', $_SESSION['TASK_NOUN']);
DEFINE('POS_ADVANCE_TILE', $_SESSION['POS_ADVANCE_TILE']);
DEFINE('POS_ADVANCE_NOUN', $_SESSION['POS_ADVANCE_NOUN']);
DEFINE('INVENTORY_NOUN', $_SESSION['INVENTORY_NOUN']);
DEFINE('CONTACTS_TILE', $_SESSION['CONTACTS_TILE']);
DEFINE('CONTACTS_NOUN', $_SESSION['CONTACTS_NOUN']);
DEFINE('BUSINESS_CAT', $_SESSION['BUSINESS_CAT']);
DEFINE('SITES_CAT', $_SESSION['SITES_CAT']);
DEFINE('SALES_TILE', $_SESSION['SALES_TILE']);
DEFINE('SALES_NOUN', $_SESSION['SALES_NOUN']);
DEFINE('SALES_ORDER_TILE', $_SESSION['SALES_ORDER_TILE']);
DEFINE('SALES_ORDER_NOUN', $_SESSION['SALES_ORDER_NOUN']);
DEFINE('PROJECT_TILE', $_SESSION['PROJECT_TILE']);
DEFINE('PROJECT_NOUN', $_SESSION['PROJECT_NOUN']);
DEFINE('PROJECT_LABEL', $_SESSION['PROJECT_LABEL']);
DEFINE('PROJECT_TYPE_CODES', $_SESSION['PROJECT_TYPE_CODES']);
DEFINE('PROJECT_TYPES', $_SESSION['PROJECT_TYPES']);
DEFINE('AFTER_PROJECT', $_SESSION['AFTER_PROJECT']);
DEFINE('JOBS_TILE', $_SESSION['JOBS_TILE']);
DEFINE('TICKET_TILE', $_SESSION['TICKET_TILE']);
DEFINE('ESTIMATE_TILE', $_SESSION['ESTIMATE_TILE']);
DEFINE('VENDOR_TILE', $_SESSION['VENDOR_TILE']);
DEFINE('TICKET_NOUN', $_SESSION['TICKET_NOUN']);
DEFINE('TICKET_LABEL', $_SESSION['TICKET_LABEL']);
DEFINE('INC_REP_TILE', $_SESSION['INC_REP_TILE']);
DEFINE('INC_REP_NOUN', $_SESSION['INC_REP_NOUN']);
DEFINE('START_DAY', $_SESSION['START_DAY']);
DEFINE('END_DAY', $_SESSION['END_DAY']);
DEFINE('COMPANY_SOFTWARE_NAME', $_SESSION['COMPANY_SOFTWARE_NAME']);
DEFINE('ACTIVE_DAY_BANNER', $_SESSION['ACTIVE_DAY_BANNER']);
DEFINE('ACTIVE_TICKET_BUTTON', $_SESSION['ACTIVE_TICKET_BUTTON']);
DEFINE('SHOW_SIGN_IN', $_SESSION['SHOW_SIGN_IN']);
DEFINE('STAFF_EMAIL_FIELD', $_SESSION['STAFF_EMAIL_FIELD']);
switch($_SESSION['user_preferences']['time_format'] > 0 ? $_SESSION['user_preferences']['time_format'] : get_config($dbc, 'system_time_format')) {
	case 4:
        DEFINE('TIME_FORMAT', 'H:mm');
        DEFINE('TIME_FORMAT_SEC', 'H:mm:ss');
        break;
	case 3:
        DEFINE('TIME_FORMAT', 'HH:mm');
        DEFINE('TIME_FORMAT_SEC', 'HH:mm:ss');
        break;
	case 2:
        DEFINE('TIME_FORMAT', 'h:mm tt');
        DEFINE('TIME_FORMAT_SEC', 'h:mm:ss tt');
        break;
	default:
        DEFINE('TIME_FORMAT', 'hh:mm tt');
        DEFINE('TIME_FORMAT_SEC', 'hh:mm:ss tt');
        break;
}

// Match Contacts
$today_date = date('Y-m-d');
$match_tile_lists = '';
if(in_array(FOLDER_NAME,['posadvanced','invoice','account receivables','project','ticket'])) {
    $match_tile_lists = str_replace(['posadvanced','invoice','account receivables','project','ticket'],
        ['posadvanced','check_out','accounts_receivables','project','ticket'],
        "OR `tile_list` LIKE '%".FOLDER_NAME."%'");
}
$match_contacts_query = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `match_contact` WHERE CONCAT(',',`staff_contact`,',') LIKE '%,".$_SESSION['contactid'].",%' AND `deleted` = 0 AND `match_date` <= '$today_date' AND (IFNULL(`tile_list`,'')='' $match_tile_lists)"),MYSQLI_ASSOC);
$match_contacts = [];
$match_exclude_security = array_filter(explode('#*#', get_config($dbc, 'match_exclude_security')));
$match_exclude = false;
foreach($match_exclude_security as $exclude_security) {
    if(strpos(','.$_SESSION['role'].',',','.$exclude_security.',') !== FALSE) {
        $match_exclude = true;
    }
}
if(!empty($match_contacts_query) && !$match_exclude) {
    $match_contacts[] = $_SESSION['contactid'];
    foreach($match_contacts_query as $match_contact) {
        if(strtotime($match_contact['end_date']) >= strtotime($today_date) && $match_contact['status'] == 'Active') {
            $match_contacts = array_merge($match_contacts, explode(',',$match_contact['support_contact']));
        }
    }
}
DEFINE('MATCH_CONTACTS',implode(',',array_unique(array_filter($match_contacts))));
// echo '<script>console.log("'.MATCH_CONTACTS.' '."SELECT * FROM `match_contact` WHERE CONCAT(',',`staff_contact`,',') LIKE '%,".$_SESSION['contactid'].",%' AND `deleted` = 0 AND `match_date` <= '$today_date' AND (IFNULL(`tile_list`,'')='' $match_tile_lists) ".FOLDER_NAME.'");</script>';
$_SERVER['page_load_info'] .= 'Constants Defined: '.number_format(microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'],5)."\n";

// List all function here
function get_tile_title($dbc) {
    $get_tile_title =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `value` FROM `general_configuration` WHERE	`name`='pos_tile_titler' UNION SELECT 'Point of Sale' `value`"));
    return (empty($get_tile_title['value']) ? POS_ADVANCE_TILE : $get_tile_title['value']);
}

function get_tile_title_vpl($dbc) {
    $get_tile_title =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM	general_configuration WHERE	name='vpl_tile_titler' UNION SELECT 'Vendor Price List' `value`"));
    return (empty($get_tile_title['value']) ? 'Vendor Price List' : $get_tile_title['value']);;
}

function get_tile_title_po($dbc) {
    $get_tile_title =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM	general_configuration WHERE	name='purchaseorder_tile_titler' UNION SELECT 'Purchase Order' `value`"));
    return (empty($get_tile_title['value']) ? 'Purchase Order' : $get_tile_title['value']);$get_tile_title['value'];
}

function get_tile_title_so($dbc) {
    // $get_tile_title =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT value FROM	general_configuration WHERE	name='sales_order_tile_titler' UNION SELECT 'Sales Order' `value`"));
    // return (empty($get_tile_title['value']) ? 'Sales Order' : $get_tile_title['value']);$get_tile_title['value'];
    return SALES_ORDER_TILE;
}

function insert_day_overview($dbc, $contactid, $type, $today_date, $total_time, $description, $tableid = 0) {
    $timestamp = date('Y-m-d h:i;s');
    $query_insert_ca = "INSERT INTO `day_overview` (`contactid`, `type`, `today_date`, `total_time`, `description`, `timestamp`, `tableid`) VALUES ('$contactid', '$type', '$today_date', '$total_time', '$description', '$timestamp', '$tableid')";
    $result_insert_ca = mysqli_query($dbc, $query_insert_ca);
}

function get_multiple_contact($dbc, $contactid) {
    $contact = explode(',', $contactid);
    $contact_name = '';
    foreach($contact as $single_contact)  {
        if($single_contact != '') {
            $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name FROM	contacts WHERE	contactid='$single_contact'"));
            $contact_name .= decryptIt($get_staff['first_name']).' '.decryptIt($get_staff['last_name']).'<br>';
        }
    }
    return $contact_name;
}

function get_multiple_project($dbc, $projectid) {
    $project = explode(',', $projectid);
    $project_name = '';
    foreach($project as $single_project)  {
        if($single_project != '') {
            $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT project_name FROM	project WHERE	projectid='$single_project'"));
            $project_name .= $get_staff['project_name'].'<br>';
        }
    }
    return $project_name;
}

function get_multiple_client_project($dbc, $projectid) {
    $project = explode(',', $projectid);
    $project_name = '';
    foreach($project as $single_project)  {
        if($single_project != '') {
            $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT project_name FROM	client_project WHERE	projectid='$single_project'"));
            $project_name .= $get_staff['project_name'].'<br>';
        }
    }
    return $project_name;
}

function get_multiple_ticket($dbc, $ticketid) {
    $ticket = explode(',', $ticketid);
    $ticket_name = '';
    foreach($ticket as $single_ticket)  {
        if($single_ticket != '') {
            $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT heading FROM	tickets WHERE	ticketid='$single_ticket'"));
            $ticket_name .= $get_staff['heading'].'<br>';
        }
    }
    return $ticket_name;
}

function get_staff($dbc, $contactid) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT first_name, last_name FROM	contacts WHERE	contactid='$contactid'"));
    return decryptIt($get_staff['first_name']).' '.decryptIt($get_staff['last_name']);
}
function get_client($dbc, $clientid) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name FROM	contacts WHERE	contactid='$clientid'"));
    return decryptIt($get_staff['name']);
}
function get_site($dbc, $siteid) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT site_name, display_name FROM	field_sites WHERE	siteid='$siteid'"));
    return $get_staff['display_name'] == '' ? $get_staff['site_name'] : $get_staff['display_name'];
}
function get_equipment($dbc, $equipmentid) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT unit_number, serial_number, category, type FROM equipment WHERE	equipmentid='$equipmentid'"));
    return $get_staff['unit_number'].' : '.$get_staff['type'];
}
function get_equipment_category($dbc, $equipmentid) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT category FROM equipment WHERE	equipmentid='$equipmentid'"));
    return $get_staff['category'];
}
function get_address($dbc, $contactid) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT business_street, business_city, business_state, business_country, business_zip, category  FROM contacts WHERE	contactid='$contactid'"));
    if($get_staff['category'] == 'Patient' || $get_staff['business_street'] != '') {
        if($get_staff['business_street'] != '') {
            return decryptIt($get_staff['business_street']).'<br>'.decryptIt($get_staff['business_city']).($get_staff['business_state'] != '' ? ', ' : '').decryptIt($get_staff['business_state']).'  '.decryptIt($get_staff['business_zip']).'<br>'.decryptIt($get_staff['business_country']);
        } else {
            return '-';
        }
    } else {
        $address = mysqli_query($dbc,"SELECT `contacts` FROM `field_config_contacts` INNER JOIN `contacts` ON `field_config_contacts`.`tab`=`contacts`.`category` WHERE `contacts`.`contactid`='$contactid'");
        $addressType = "business_address";
        while($row = mysqli_fetch_array($address)) {
            if(strpos($row['contacts'],',Mailing Address,') !== false)
                $addressType = "mailing_address";
        }
        $client = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT business_address, country, city, state, province, zip_code, postal_code, mailing_address FROM contacts WHERE contactid='$contactid'"));
        return $client[$addressType].'<br>'.$client['city'].($client['state'].$client['province'] != '' ? ', ' : '').($client['state'] == '' ? $client['province'] : $client['state']).'  '.($client['zip_code'] == '' ? $client['postal_code'] : $client['zip_code']).'<br>'.$client['country'];
    }
}
function get_ship_address($dbc, $contactid) {
	$client = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ship_to_address, ship_country, ship_city, ship_state, ship_zip FROM contacts WHERE contactid='$contactid'"));
    return $client['ship_to_address'].($client['ship_city'] != '' ? ', ' : '').$client['ship_city'].($client['ship_state'] != '' ? ', ' : '').$client['ship_state'].($client['ship_zip'] != '' ? ', ' : '').$client['ship_zip'].($client['ship_country'] != '' ? ', ' : '').$client['ship_country'];
}
function get_marketing_material_uploads($dbc, $certuploadid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	marketing_material_uploads WHERE	certuploadid='$certuploadid'"));
    return $get_staff[$field_name];
}
function get_tasklist($dbc, $type, $board_name, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM field_config_communication WHERE board_name='$board_name' AND type='$type'"));
    return $get_staff[$field_name];
}
function get_tasklist_tabledata($dbc, $tasklistid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	tasklist WHERE	tasklistid='$tasklistid'"));
    return $get_staff[$field_name];
}

function get_field_config_contacts($dbc, $accordion, $field_name, $tab, $subtab = '') {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `$field_name` FROM	field_config_contacts WHERE (tile_name = '".FOLDER_NAME."' OR (tile_name='contacts' AND '".FOLDER_NAME."'='staff')) AND accordion='$accordion' AND tab='$tab' AND (IFNULL(subtab,'') = '$subtab' OR '$subtab' = '')"));
    return $get_staff[$field_name];
}
function get_field_config_vendors($dbc, $accordion, $field_name, $tab, $subtab = '') {
    $get_field = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `$field_name` FROM `field_config_vendors` WHERE `accordion`='$accordion' AND `tab`='$tab' AND (IFNULL(`subtab`,'')='$subtab' OR '$subtab'='')"));
    return $get_field[$field_name];
}
function get_field_config_project_manage($dbc, $accordion, $field_name, $tile, $tab) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `$field_name` FROM	field_config_project_manage WHERE accordion='$accordion' AND tab='$tab' AND tile='$tile'"));
    return $get_staff[$field_name];
}
function get_field_config_inventory($dbc, $accordion, $field_name, $tab) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `$field_name` FROM	field_config_inventory WHERE accordion='$accordion' AND tab='$tab'"));
    return $get_staff[$field_name];
}
function get_field_config_vpl($dbc, $accordion, $field_name, $tab) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `$field_name` FROM	field_config_vpl WHERE	accordion='$accordion' AND tab='$tab'"));
    return $get_staff[$field_name];
}
function get_field_config_asset($dbc, $accordion, $field_name, $tab) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `$field_name` FROM	field_config_asset WHERE	accordion='$accordion' AND tab='$tab'"));
    return $get_staff[$field_name];
}
function get_field_config_equipment($dbc, $accordion, $field_name, $tab) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `$field_name` FROM	field_config_equipment WHERE	accordion='$accordion' AND tab='$tab'"));
    return $get_staff[$field_name];
}

function get_ticketlist($dbc, $board_name, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	field_config_communication WHERE	board_name='$board_name' AND type='Ticket'"));
    return $get_staff[$field_name];
}

function get_passwordconfig($dbc, $category, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	field_config_password WHERE	category='$category'"));
    return $get_staff[$field_name];
}

function get_job($dbc, $jobid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	field_jobs WHERE	jobid='$jobid'"));
    return $get_staff[$field_name];
}

function get_dltimer($dbc, $timerid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	driving_log_timer WHERE	timerid='$timerid'"));
    return $get_staff[$field_name];
}
function get_manual($dbc, $manualtypeid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	manuals WHERE	manualtypeid='$manualtypeid'"));
    return $get_staff[$field_name];
}
function get_contract($dbc, $contractid, $field_name) {
    $value = mysqli_fetch_array(mysqli_query($dbc,"SELECT $field_name FROM	`contracts` WHERE	contractid='$contractid'"),MYSQLI_NUM);
    return $value[0];
}
function get_support($dbc, $supportid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM support WHERE	supportid='$supportid'"));
    return $get_staff[$field_name];
}
function get_email($dbc, $contactid) {
    $email_field = 'email_address';
    if(strtolower(get_contact($dbc, $contactid, 'category')) == 'staff') {
        $email_field = STAFF_EMAIL_FIELD;
    }
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $email_field FROM	contacts WHERE	contactid='$contactid'"));
    return decryptIt($get_staff[$email_field]);
}

function get_multiple_email($dbc, $contactid) {
            $contact_name = '';
            $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT email_address, second_email_address, office_email, company_email FROM	contacts WHERE	contactid='$contactid'"));
            if($get_staff['email_address'] != '') {
                $contact_name .= decryptIt($get_staff['email_address']);
            }
            if($get_staff['second_email_address'] != '') {
                $contact_name .= ','.decryptIt($get_staff['second_email_address']);
            }
            if($get_staff['office_email'] != '') {
                $contact_name .= ','.decryptIt($get_staff['office_email']);
            }
            if($get_staff['company_email'] != '') {
                $contact_name .= ','.decryptIt($get_staff['company_email']);
            }
    return $contact_name;
}

/*function get_email($dbc, $contactid) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT email_address, category FROM	contacts WHERE	contactid='$contactid'"));
    if($get_staff['category'] == 'Patient') {
        if($get_staff['email_address'] != '') {
            return decryptIt($get_staff['email_address']);
        } else {
            return '-';
        }
    } else {
        return $get_staff['email_address'];
    }
}*/
function set_field_config($dbc, $name, $value) {
	$name = filter_var($name, FILTER_SANITIZE_STRING);
	$value = filter_var($value, FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_config` (`fieldconfigid`) SELECT 0 FROM (SELECT COUNT(*) rows FROM `field_config`) num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_config` SET `$name`='$value'");
}
function set_field_mandatory_config($dbc, $name, $value) {
	$name = filter_var($name, FILTER_SANITIZE_STRING);
	$value = filter_var($value, FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `field_mandatory_config` (`fieldconfigid`) SELECT 0 FROM (SELECT COUNT(*) rows FROM `field_mandatory_config`) num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `field_mandatory_config` SET `$name`='$value'");
}
function get_field_config($dbc, $name) {
    return ','.mysqli_fetch_array(mysqli_query($dbc,"SELECT `".filter_var($name,FILTER_SANITIZE_STRING)."` FROM `field_config`"))[0].',';
}
function get_mandatory_field_config($dbc, $name) {
    return ','.mysqli_fetch_array(mysqli_query($dbc,"SELECT `".filter_var($name,FILTER_SANITIZE_STRING)."` FROM `field_mandatory_config`"))[0].',';
}

function set_config($dbc, $name, $value, $mandatory=0) {
	$name = filter_var($name, FILTER_SANITIZE_STRING);
	$value = filter_var($value, FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `general_configuration` (`name`) SELECT '$name' FROM (SELECT COUNT(*) rows FROM `general_configuration` WHERE `name`='$name') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `general_configuration` SET `value`='$value',`mandatory`='$mandatory' WHERE `name`='$name'");
	$_SESSION['CONSTANT_UPDATED'] = 0;
}
function get_config($dbc, $name, $multi = false, $separator = ',') {
    // Defined Overrided Settings
    if($name == 'show_category_dropdown_equipment') {
        return 0;
    }

	// Get current values
    $name = filter_var($name, FILTER_SANITIZE_STRING);
    if($name == 'all_contact_tabs') {
        $sql = "SELECT GROUP_CONCAT(`value`) value FROM `general_configuration` WHERE `name` IN ('contacts_tabs','contacts3_tabs','clientinfo_tabs','members_tabs','vendors_tabs','contactsrolodex_tabs')";
        $get_config = mysqli_fetch_assoc(mysqli_query($dbc,$sql));
    } else if($multi) {
        $get_config['value'] = [];
        $separator = filter_var($separator, FILTER_SANITIZE_STRING);
        $query = mysqli_query($dbc, "SELECT `name`,`value` FROM `general_configuration` WHERE `name` LIKE '$name'");
        while($row = mysqli_fetch_assoc($query)) {
            $get_config['value'][$row['name']] = $row['value'];
        }
		if($separator == null) {
			return $get_config['value'];
		}
        $get_config['value'] = implode($separator, $get_config['value']);
    } else {
        $sql = "SELECT `value` FROM `general_configuration` WHERE `name`='$name'";
        $get_config = mysqli_fetch_assoc(mysqli_query($dbc,$sql));
    }

	// Define Defaults for specific fields
	if(str_replace(',','',$get_config['value']) == '') {
		if($name == 'timesheet_tabs') {
			return 'Time Sheets,Pay Period,Holidays,Coordinator Approvals,Manager Approvals,Reporting,Payroll';
		} else if($name == 'time_tracking_tabs') {
			return 'tracking,shop_time_sheets';
		} else if($name == 'staff_field_subtabs') {
			return 'ID Card,Staff Information,Staff Address,Employee Information,Driver Information,Direct Deposit Information,Software ID,Social Media,Emergency,Health,Schedule,Certificates,HR,Ticket,Project,History';
		} else if($name == 'payroll_tabs') {
			return 'compensation,salary,contractor,field_ticket,shop_work_order';
		} else if($name == 'billing_tabs') {
			return 'billing,invoices,accounts_receivable';
		} else if($name == 'project_nav_tabs') {
			return 'projects,scrum,tickets,daysheet';
		} else if($name == 'communication_schedule_tabs') {
			return 'email,phone';
		} else if($name == 'site_work_orders') {
			return 'sites,pending,active,schedule,po';
		} else if($name == 'checklist_tabs') {
			return 'my_ongoing,my_daily,my_weekly,my_monthly,company_ongoing,company_daily,company_weekly,company_monthly,project_tab,reporting';
		} else if($name == 'tile_enable_section') {
			return 'software_settings,profiles,human_resources,sales,inventory_management,collaborative_workflow,estimates,safety,project_management,operations,project_details,project_type,time_tracking,accounting,reporting,medical,point_of_sale,crm,analytics,equipment,digital_forms,common_practice,project_addon,project_tracking,clinic_ace,communication,safety,pos,crm,anlytics';
		} else if($name == 'client_project_tabs') {
			return 'pending,active,archived,tickets,daysheet';
		} else if($name == 'general_flag_colours') {
			return 'FB0D0D*#*Default Flag Colour';
		} else if($name == 'ticket_colour_flags') {
			return 'FF6060';
		} else if($name == 'expense_tabs') {
			return 'budget,current_month,business,customers,clients,staff,sales,manager,payables,report';
		} else if($name == 'rate_card_tabs') {
			return ',company,customer,';
		} else if($name == 'expense_provinces') {
			return 'AB*5*0*0#*#BC*5*7*0#*#MB*8*5*0#*#NB*0*0*15#*#NL*0*0*15#*#NT*5*0*0#*#NS*0*0*15#*#NU*5*0*0#*#ON*0*0*13#*#PE*0*0*15#*#QC*5*9.975*0#*#SK*5*5*0#*#YT*5*0*0';
		} else if($name == 'equipment_tabs') {
			return 'Truck,Trailer';
		} else if($name == 'equipment_main_tabs') {
			return 'Equipment,Inspection,Work Order,Expenses,Requests,Records,Checklists';
		} else if($name == 'equipment_remind_subject') {
			return 'Reminder of a Renewal for Equipment';
		} else if($name == 'equipment_remind_body') {
			return htmlentities('Hi,<p>There is an upcoming renewal for some equipment.</p>');
		} else if($name == 'equipment_service_subject') {
			return 'Request for Service for Equipment';
		} else if($name == 'equipment_service_body') {
			return htmlentities('Hi,<p>During the inspection of the equipment, it was found that service was needed. You will find a PDF with the details attached.</p>');
		} else if($name == 'equipment_expense_fields') {
			return 'Description,Country,Province,Date,Receipt,Amount,HST,PST,GST,Total';
		} else if($name == 'invoice_design') {
			return 4;
		} else if($name == 'pos_design') {
			return 1;
		} else if($name == 'invoice_tabs') {
			return 'today,all,ui_report,refunds,cashout';
		} else if($name == 'invoice_purchase_contact') {
			return 'Patient';
		} else if($name == 'invoice_payer_contact') {
			return 'Insurer';
		} else if($name == 'invoice_fields') {
			return 'invoice_type,customer,injury,staff,appt_type,treatment,service_date,pay_mode,services,service_cat,service_head,service_price,inventory,inventory_name,inventory_type,inventory_price,inventory_qty,packages,packages_cat,packages_name,packages_fee,promo,tips,next_appt,survey,followup';
		} else if($name == 'invoice_payment_types') {
			return 'Master Card,Visa,Debit Card,Cash,Cheque,Amex,Direct Deposit,Gift Certificate Redeem,Pro-Bono';
		} else if($name == 'invoice_dashboard') {
			return 'invoiceid,invoice_date,customer,total_price,payment_type,invoice_pdf,comment,status,send,delivery,invoice_xsl,invoice_xml';
		} else if($name == 'max_timer') {
			return 28800;
		} else if($name == 'appt_day_start') {
			return '06:00 am';
		} else if($name == 'appt_day_end') {
			return '08:00 pm';
		} else if($name == 'appt_increments') {
			return 15;
		} else if($name == 'appt_wait_list') {
			return 'yes';
		} else if($name == 'calendar_default') {
			return 'ticket_wk';
		} else if($name == 'expense_mode') {
			return 'inbox';
		} else if($name == 'estimate_dashboard_length') {
			return 10;
		} else if($name == 'project_status') {
			return "In Development#*#Active Project";
		} else if($name == 'estimate_status') {
			return "Opportunities#*#In Negotiations#*#Closed Successfully";
		} else if($name == 'business_category') {
			return "Business";
		} else if($name == 'site_category') {
			return "Sites";
		} else if($name == 'project_classify') {
			return "Types";
		} else if($name == 'ticket_label') {
			return "[TICKET_NOUN] #[TICKETID] - [TICKET_HEADING]";
		} else if($name == 'project_label') {
			return "#[PROJECTID] [PROJECT_NAME]";
		} else if($name == 'mileage_fields') {
			return "staff,startdate,enddate,category,details,contact,double_mileage";
		} else if($name == 'comp_staff_groups') {
			return 'ALL';
		} else if($name == 'ticket_min_hours' || $name == 'timesheet_hour_intervals') {
			return '0';
		} else if($name == 'hr_fields') {
			return 'Sub Category,First Name,Last Name,Birth Date,Employee Number,Address including Postal Code,Topic (Sub Tab),Sub Section Heading,Third Tier Heading,Detail,Document,Link,Videos,Signature box,Comments,Staff,Review Deadline,Status,Configure Email,Form,Permissions by Position';
		} else if($name == 'volume_units') {
			return 'm&sup3;';
		} else if($name == 'quick_action_icons') {
			return 'edit,flag,reply,attach,alert,email,reminder,time,archive';
		} else if($name == 'manual_subject_completed') {
			return 'Manual Read by [USER]';
		} else if($name == 'manual_body_completed') {
			return htmlentities('<p>Category: [CATEGORY]<br>Heading: [HEADING]</p>[COMMENT]');
		} else if($name == 'manual_completed_email') {
			return mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE `name` LIKE 'manual_%_email'"))['value'];
		} else if($name == 'log_note_categories') {
			return get_config($dbc, 'all_contact_tabs');
		} else if($name == 'staff_tabs') {
			return 'suspended,security,positions,reminders';
		} else if($name == 'inventory_markup') {
			return 0;
		} else if($name == 'calendar_ticket_card_fields') {
			return 'label,project,customer,assigned,preferred,time,available';
		} else if($name == 'tickets_summary') {
			return 'Created,Assigned';
		} else if($name == 'timesheet_include_time') {
			return 'ticket,project,meeting,email,task,checklist';
		} else if($name == 'daysheet_ticket_fields') {
			return 'Project,Time Estimate';
		} else if($name == 'client_accordion_category') {
			return 'Clients';
		} else if($name == 'rate_card_contact') {
			return 'businessid';
		} else if($name == 'transport_carrier_category') {
			return 'Carrier';
		} else if($name == 'expense_default_staff') {
			return 'NA';
		} else if($name == 'inventory_cost') {
			return 'cost';
		} else if($name == 'planner_end_day') {
			return 'show';
		} else if($name == 'ticket_project_function') {
			return 'manual';
		} else if($name == 'inventory_sort') {
			return 'default';
		} else if($name == 'po_tabs') {
			return 'create,pending,receiving,payable,completed,remote,site_po';
		} else if($name == 'ticket_manifest_fields') {
			return 'file,po,vendor,line,qty,site';
		} else if($name == 'report_row_colour_1') {
			return '#BBBBBB';
		} else if($name == 'report_row_colour_2') {
			return '#DDDDDD';
		} else if($name == 'recent_manifests') {
			return '25';
		} else if($name == 'recent_inventory') {
			return '25';
		} else if($name == 'company_rate_card_sections') {
			return 'tasks,material,services,products,staff,position,contractor,clients,customer,vpl,inventory,equipment,labour,timesheet,driving_log';
		} else if($name == 'lead_new_contact_cat') {
			return 'Sales Leads';
		} else if($name == 'ticket_archive_status') {
			return 'Archive#*#Archived';
		} else if($name == 'estimate_tabs') {
			return get_config($dbc, 'project_tabs');
		} else if($name == 'invoice_ticket_append_qty') {
			return 10;
		} else if(strpos($name, 'invoice_ticket_append_qty_') === 0) {
			return get_config($dbc, 'invoice_ticket_append_qty');
		} else if($name == 'cust_support_tab_list') {
            $value = ['services','scrum','new','feedback'];
            foreach(explode(',',get_config($dbc, 'ticket_tabs')) as $ticket_tab) {
                $value[] = config_safe_str($ticket_tab);
            }
            $value[] = 'closed';
			return implode(',',$value);
		} else if($name == 'invoice_email_subject') {
			return 'Attached is your '.POS_ADVANCE_NOUN;
		} else if($name == 'invoice_email_body') {
			return '&lt;p&gt;[CUSTOMER]&lt;/p&gt;&lt;p&gt;Attached is your invoice. Thank you for your business!&lt;/p&gt;';
		} else if($name == 'optimize_stop_types') {
			return get_config($dbc, 'delivery_types');
		}
	}

	return $get_config['value'];
}
function get_mandatory_config($dbc, $name, $multi = false, $separator = ',') {
    // Defined Overrided Settings
    if($name == 'show_category_dropdown_equipment') {
        return 0;
    }

    $sql = "SELECT `value` FROM `general_configuration` WHERE `name`='$name' AND mandatory=1";
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,$sql));

	// Define Defaults for specific fields
	if(str_replace(',','',$get_config['value']) == '') {
		if($name == 'timesheet_tabs') {
			return 'Time Sheets,Pay Period,Holidays,Coordinator Approvals,Manager Approvals,Reporting,Payroll';
		} else if($name == 'time_tracking_tabs') {
			return 'tracking,shop_time_sheets';
		} else if($name == 'staff_field_subtabs') {
			return 'ID Card,Staff Information,Staff Address,Employee Information,Driver Information,Direct Deposit Information,Software ID,Social Media,Emergency,Health,Schedule,Certificates,HR,Ticket,Project,History';
		} else if($name == 'payroll_tabs') {
			return 'compensation,salary,contractor,field_ticket,shop_work_order';
		} else if($name == 'billing_tabs') {
			return 'billing,invoices,accounts_receivable';
		} else if($name == 'project_nav_tabs') {
			return 'projects,scrum,tickets,daysheet';
		} else if($name == 'communication_schedule_tabs') {
			return 'email,phone';
		} else if($name == 'site_work_orders') {
			return 'sites,pending,active,schedule,po';
		} else if($name == 'checklist_tabs') {
			return 'my_ongoing,my_daily,my_weekly,my_monthly,company_ongoing,company_daily,company_weekly,company_monthly,project_tab,reporting';
		} else if($name == 'tile_enable_section') {
			return 'software_settings,profiles,human_resources,sales,inventory_management,collaborative_workflow,estimates,safety,project_management,operations,project_details,project_type,time_tracking,accounting,reporting,medical,point_of_sale,crm,analytics,equipment,digital_forms,common_practice,project_addon,project_tracking,clinic_ace,communication,safety,pos,crm,anlytics';
		} else if($name == 'client_project_tabs') {
			return 'pending,active,archived,tickets,daysheet';
		} else if($name == 'general_flag_colours') {
			return 'FB0D0D*#*Default Flag Colour';
		} else if($name == 'ticket_colour_flags') {
			return 'FF6060';
		} else if($name == 'expense_tabs') {
			return 'budget,current_month,business,customers,clients,staff,sales,manager,payables,report';
		} else if($name == 'rate_card_tabs') {
			return ',company,customer,';
		} else if($name == 'expense_provinces') {
			return 'AB*5*0*0#*#BC*5*7*0#*#MB*8*5*0#*#NB*0*0*15#*#NL*0*0*15#*#NT*5*0*0#*#NS*0*0*15#*#NU*5*0*0#*#ON*0*0*13#*#PE*0*0*15#*#QC*5*9.975*0#*#SK*5*5*0#*#YT*5*0*0';
		} else if($name == 'equipment_tabs') {
			return 'Truck,Trailer';
		} else if($name == 'equipment_main_tabs') {
			return 'Equipment,Inspection,Work Order,Expenses,Requests,Records,Checklists';
		} else if($name == 'equipment_remind_subject') {
			return 'Reminder of a Renewal for Equipment';
		} else if($name == 'equipment_remind_body') {
			return htmlentities('Hi,<p>There is an upcoming renewal for some equipment.</p>');
		} else if($name == 'equipment_service_subject') {
			return 'Request for Service for Equipment';
		} else if($name == 'equipment_service_body') {
			return htmlentities('Hi,<p>During the inspection of the equipment, it was found that service was needed. You will find a PDF with the details attached.</p>');
		} else if($name == 'equipment_expense_fields') {
			return 'Description,Country,Province,Date,Receipt,Amount,HST,PST,GST,Total';
		} else if($name == 'invoice_design') {
			return 4;
		} else if($name == 'pos_design') {
			return 1;
		} else if($name == 'invoice_tabs') {
			return 'today,all,ui_report,refunds,cashout';
		} else if($name == 'invoice_purchase_contact') {
			return 'Patient';
		} else if($name == 'invoice_payer_contact') {
			return 'Insurer';
		} else if($name == 'invoice_fields') {
			return 'invoice_type,customer,injury,staff,appt_type,treatment,service_date,pay_mode,services,service_cat,service_head,service_price,inventory,inventory_name,inventory_type,inventory_price,inventory_qty,packages,packages_cat,packages_name,packages_fee,promo,tips,next_appt,survey,followup';
		} else if($name == 'invoice_payment_types') {
			return 'Master Card,Visa,Debit Card,Cash,Cheque,Amex,Direct Deposit,Gift Certificate Redeem,Pro-Bono';
		} else if($name == 'invoice_dashboard') {
			return 'invoiceid,invoice_date,customer,total_price,payment_type,invoice_pdf,comment,status,send,delivery';
		} else if($name == 'max_timer') {
			return 28800;
		} else if($name == 'appt_day_start') {
			return '06:00 am';
		} else if($name == 'appt_day_end') {
			return '08:00 pm';
		} else if($name == 'appt_increments') {
			return 15;
		} else if($name == 'appt_wait_list') {
			return 'yes';
		} else if($name == 'calendar_default') {
			return 'ticket_wk';
		} else if($name == 'expense_mode') {
			return 'inbox';
		} else if($name == 'estimate_dashboard_length') {
			return 10;
		} else if($name == 'project_status') {
			return "In Development#*#Active Project";
		} else if($name == 'estimate_status') {
			return "Opportunities#*#In Negotiations#*#Closed Successfully";
		} else if($name == 'business_category') {
			return "Business";
		} else if($name == 'site_category') {
			return "Sites";
		} else if($name == 'project_classify') {
			return "Types";
		} else if($name == 'ticket_label') {
			return "[TICKET_NOUN] #[TICKETID] - [TICKET_HEADING]";
		} else if($name == 'project_label') {
			return "#[PROJECTID] [PROJECT_NAME]";
		} else if($name == 'mileage_fields') {
			return "staff,startdate,enddate,category,details,contact,double_mileage";
		} else if($name == 'comp_staff_groups') {
			return 'ALL';
		} else if($name == 'ticket_min_hours' || $name == 'timesheet_hour_intervals') {
			return '0';
		} else if($name == 'hr_fields') {
			return 'Sub Category,First Name,Last Name,Birth Date,Employee Number,Address including Postal Code,Topic (Sub Tab),Sub Section Heading,Third Tier Heading,Detail,Document,Link,Videos,Signature box,Comments,Staff,Review Deadline,Status,Configure Email,Form,Permissions by Position';
		} else if($name == 'volume_units') {
			return 'm&sup3;';
		} else if($name == 'quick_action_icons') {
			return 'edit,flag,reply,attach,alert,email,reminder,time,archive';
		} else if($name == 'manual_subject_completed') {
			return 'Manual Read by [USER]';
		} else if($name == 'manual_body_completed') {
			return htmlentities('<p>Category: [CATEGORY]<br>Heading: [HEADING]</p>[COMMENT]');
		} else if($name == 'manual_completed_email') {
			return mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE `name` LIKE 'manual_%_email'"))['value'];
		} else if($name == 'log_note_categories') {
			return get_config($dbc, 'all_contact_tabs');
		} else if($name == 'staff_tabs') {
			return 'suspended,security,positions,reminders';
		} else if($name == 'inventory_markup') {
			return 0;
		} else if($name == 'calendar_ticket_card_fields') {
			return 'label,project,customer,assigned,preferred,time,available';
		} else if($name == 'tickets_summary') {
			return 'Created,Assigned';
		} else if($name == 'timesheet_include_time') {
			return 'ticket,project,meeting,email,task,checklist';
		} else if($name == 'daysheet_ticket_fields') {
			return 'Project,Time Estimate';
		} else if($name == 'client_accordion_category') {
			return 'Clients';
		} else if($name == 'rate_card_contact') {
			return 'businessid';
		} else if($name == 'transport_carrier_category') {
			return 'Carrier';
		} else if($name == 'expense_default_staff') {
			return 'NA';
		} else if($name == 'inventory_cost') {
			return 'cost';
		} else if($name == 'planner_end_day') {
			return 'show';
		} else if($name == 'ticket_project_function') {
			return 'manual';
		} else if($name == 'inventory_sort') {
			return 'default';
		} else if($name == 'po_tabs') {
			return 'create,pending,receiving,payable,completed,remote,site_po';
		} else if($name == 'ticket_manifest_fields') {
			return 'file,po,vendor,line,qty,site';
		} else if($name == 'report_row_colour_1') {
			return '#BBBBBB';
		} else if($name == 'report_row_colour_2') {
			return '#DDDDDD';
		} else if($name == 'recent_manifests') {
			return '25';
		} else if($name == 'recent_inventory') {
			return '25';
		} else if($name == 'company_rate_card_sections') {
			return 'tasks,material,services,products,staff,position,contractor,clients,customer,vpl,inventory,equipment,labour,timesheet,driving_log';
		} else if($name == 'lead_new_contact_cat') {
			return 'Sales Leads';
		}
	}

	return $get_config['value'];
}
function get_pos($dbc, $posid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	point_of_sell WHERE	posid='$posid'"));
    return $get_staff[$field_name];
}
//
function get_services($dbc, $serviceid, $field_name) {
    $get_service =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	services WHERE	serviceid='$serviceid'"));
    return $get_service[$field_name];
}
function get_vpl($dbc, $vplid, $field_name) {
    $get_service =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM vendor_price_list WHERE inventoryid='$vplid'"));
    return $get_service[$field_name];
}
function get_products($dbc, $productid, $field_name) {
    $get_service =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	products WHERE	productid='$productid'"));
    return $get_service[$field_name];
}
function get_marketing_material($dbc, $marketing_materialid, $field_name) {
    $get_service =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	marketing_material WHERE	marketing_materialid='$marketing_materialid'"));
    return $get_service[$field_name];
}
function get_sred($dbc, $sredid, $field_name) {
    $get_service =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	sred WHERE	sredid='$sredid'"));
    return $get_service[$field_name];
}
function get_client_project_path_milestone($dbc, $project_path_milestone, $field_name) {
    $get_custom =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM client_project_path_milestone WHERE	project_path_milestone='$project_path_milestone'"));
    return $get_custom[$field_name];
}
function get_project_path_milestone($dbc, $project_path_milestone, $field_name) {
    $get_custom =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM project_path_milestone WHERE	project_path_milestone='$project_path_milestone'"));
    return $get_custom[$field_name];
}
// Get the list of actual paths for the Project
function get_project_paths($projectid) {
    $default_template = $_SERVER['DBC']->query("SELECT * FROM `project_path_milestone` WHERE `milestone` LIKE 'to do#*#doing#*#done'");
    if($default_template->num_rows == 0) {
        $_SERVER['DBC']->query("INSERT INTO `project_path_milestone` (`project_path`,`milestone`) VALUES ('Basic Task List','To Do#*#Doing#*#Done')");
    }
    if($projectid > 0) {
        $paths = get_field_value('project_path project_path_name', 'project', 'projectid', $projectid);
        $project_paths = [];
        foreach(explode(',',$paths['project_path']) as $i => $pathid) {
            if($pathid > 0) {
                $path['path_id'] = $pathid;
                $path['path_name'] = explode('#*#',$paths['project_path_name'])[$i];
                if(empty($path['path_name'])) {
                    $path['path_name'] = get_field_value('project_path','project_path_milestone','project_path_milestone',$pathid);
                }

              // Add default milestones, if they have not yet been added
                $milestones = explode('#*#',get_field_value('milestone','project_path_milestone','project_path_milestone',$pathid));
                $prior_sort = 0;
                foreach($milestones as $i => $milestone) {
                    $milestone_rows = $_SERVER['DBC']->query("SELECT `sort` FROM `project_path_custom_milestones` WHERE `projectid`='$projectid' AND `milestone`='$milestone' AND `pathid`='$pathid' AND `path_type`='I'");
                    if($milestone_rows->num_rows > 0) {
                        $prior_sort = $milestone_rows->fetch_assoc()['sort'];
                    } else if($milestone != 'Unassigned') {
                        $_SERVER['DBC']->query("INSERT INTO `project_path_custom_milestones` (`projectid`,`milestone`,`label`,`path_type`,`pathid`,`sort`) VALUES ('$projectid','$milestone','$milestone','I','$pathid','$prior_sort')");
                    }
                }

                // Load the actual list of milestones into the array
                $path['milestones'] = [];
                $milestone_list = $_SERVER['DBC']->query("SELECT `milestones`.`id`, `milestones`.`milestone`, `milestones`.`label`  FROM `project_path_custom_milestones` `milestones` WHERE `milestones`.`projectid`='$projectid' AND `milestones`.`pathid`='$pathid' AND `milestones`.`path_type`='I' AND `milestones`.`deleted`=0 ORDER BY `milestones`.`sort`,`milestones`.`id`");
                while($milestone_row = $milestone_list->fetch_assoc()) {
                    $path['milestones'][] = $milestone_row;
                }
                $project_paths[] = $path;
            }
        }
        return $project_paths;
    }
    return false;
}
function get_jobs_path_milestone($dbc, $project_path_milestone, $field_name) {
    $get_custom =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM jobs_path_milestone WHERE	project_path_milestone='$project_path_milestone'"));
    return $get_custom[$field_name];
}
function get_task_board($dbc, $taskboardid, $field_name) {
    $get_custom =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM task_board WHERE	taskboardid='$taskboardid'"));
    return $get_custom[$field_name];
}
function get_checklist($dbc, $checklistid, $field_name) {
    $get_custom =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM checklist WHERE	checklistid='$checklistid'"));
    return $get_custom[$field_name];
}
function get_checklist_name($dbc, $checklistnameid, $field_name) {
    $get_custom =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM checklist_name WHERE	checklistnameid='$checklistnameid'"));
    return $get_custom[$field_name];
}

function get_labour($dbc, $labourid, $field_name) {
    $get_service =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	labour WHERE	labourid='$labourid'"));
    return $get_service[$field_name];
}
function get_material($dbc, $materialid, $field_name) {
    $get_service =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	material WHERE	materialid='$materialid'"));
    return $get_service[$field_name];
}
function get_rate_card($dbc, $ratecardid, $field_name) {
    $get_service =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	rate_card WHERE	ratecardid='$ratecardid'"));
    return $get_service[$field_name];
}
/*function get_contact($dbc, $contactid, $field_name) {
    $get_contact =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	contacts WHERE	contactid='$contactid'"));
    return $get_contact[$field_name];
}*/
// Return the direct decrypted value from any single database field based on matching a specific field
function set_field_value($value, $field_name, $table_name, $id_field, $id) {
	// Sanitize the parameters
	$value = filter_var($value,FILTER_SANITIZE_STRING);
	if($table_name == 'contacts' && isEncrypted($field_name)) {
		$value = encryptIt($value);
	}
	$field_name = filter_var($field_name,FILTER_SANITIZE_STRING);
	$table_name = filter_var($table_name,FILTER_SANITIZE_STRING);
	$id_field = filter_var($id_field,FILTER_SANITIZE_STRING);

	// If the selected id field is an encrypted field, encrypt the id so that it will match
	if($table_name == 'contacts' && isEncrypted($id_field)) {
		$id = encryptIt($id);
	} else {
		$id = filter_var($id,FILTER_SANITIZE_STRING);
	}

	// Set the matching value
	if(!$_SERVER['DBC']->query("UPDATE `$table_name` SET `$field_name`='$value' WHERE `$id_field`='$id' AND `$id_field` NOT IN (0,'')")) {
		return "<!--Unable to update $field_name in $table_name to $value. Please review the request ($id_field: $id).-->";
	} else {
		return "<!--Successfully updated $field_name in $table_name to $value.-->";
	}
}
function get_field_value($field_name, $table_name, $id_field, $id) {
	// Sanitize the parameters
	if(strpos($field_name,' ') !== FALSE) {
		$field_name = '`'.implode('`,`',explode(' ',filter_var($field_name,FILTER_SANITIZE_STRING))).'`';
	} else {
		$field_name = '`'.filter_var($field_name,FILTER_SANITIZE_STRING).'`';
	}
	$table_name = filter_var($table_name,FILTER_SANITIZE_STRING);
	$id_field = filter_var($id_field,FILTER_SANITIZE_STRING);

	// If the selected id field is an encrypted field, encrypt the id so that it will match
	if($table_name == 'contacts' && isEncrypted($id_field)) {
		$id = encryptIt($id);
	} else {
		$id = filter_var($id,FILTER_SANITIZE_STRING);
	}

	// Get the first matching value, and decrypt it if necessary
	if(!($values = $_SERVER['DBC']->query("SELECT $field_name FROM `$table_name` WHERE `$id_field`='$id' AND `$id_field` NOT IN (0,'')"))) {
		return "<!--Unable to retrieve $field_name from $table_name. Please review the request ($id_field: $id).-->";
	} else {
		$values = $values->fetch_assoc();
	}
	foreach($values as $field => $value) {
		if($table_name == 'contacts' && isEncrypted($field_name)) {
			$values[$field] = decryptIt($value);
		}
	}

	// If multiple fields were selected, return an array, otherwise return the value as a string
	if(count($values) == 1) {
		return $values[trim($field_name,'`')];
	} else {
		return $values;
	}
}
function get_contact($dbc, $contactid, $field_name = '') {
	if($field_name == 'name_company') {
		$get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `name`, `first_name`, `last_name` FROM contacts WHERE contactid='$contactid'"));
		return trim(decryptIt($get_contact['name']).': '.decryptIt($get_contact['first_name']).' '.decryptIt($get_contact['last_name']),': ');
	}
	else if($field_name != '') {
		$get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM contacts WHERE contactid='$contactid'"));
		if(isEncrypted($field_name))
			return decryptIt($get_contact[$field_name]);
		else
			return $get_contact[$field_name];
	}
	else {
		$get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT name, first_name, last_name, site_name, display_name, category FROM	contacts WHERE	contactid='$contactid'"));
		if($get_contact['first_name'] != '' || $get_contact['last_name'] != '') {
			return decryptIt($get_contact['first_name']).' '.decryptIt($get_contact['last_name']);
		} else if($get_contact['name'] != '') {
			return decryptIt($get_contact['name']);
		} else if($get_contact['site_name'] != '') {
            return $get_contact['site_name'];
		} else if($get_contact['display_name'] != '') {
            return $get_contact['display_name'];
        } else {
			return '-';
		}
	}
}

function get_inventory($dbc, $inventoryid, $field_name) {
    $get_inventory =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM inventory WHERE	inventoryid='$inventoryid'"));
    return $get_inventory[$field_name];
}
function get_positions($dbc, $position_id, $field_name) {
    $get_inventory =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM positions WHERE	position_id='$position_id'"));
    return $get_inventory[$field_name];
}
function get_package($dbc, $packageid, $field_name) {
    $get_package =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM package WHERE	packageid='$packageid'"));
    return $get_package[$field_name];
}
function get_promotion($dbc, $promotionid, $field_name) {
    $get_promotion =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM promotion WHERE	promotionid='$promotionid'"));
    return $get_promotion[$field_name];
}
function get_sales($dbc, $salesid, $field_name) {
    $get_promotion =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM sales WHERE	salesid='$salesid'"));
    return $get_promotion[$field_name];
}
function get_custom($dbc, $customid, $field_name) {
    $get_custom =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM custom WHERE	customid='$customid'"));
    return $get_custom[$field_name];
}
function get_equipment_field($dbc, $equipmentid, $field_name) {
    $get_equipment =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM equipment WHERE	equipmentid='$equipmentid'"));
    return $get_equipment[$field_name];
}
function get_equipment_label($dbc, $equipment) {
    return $equipment['category'].(!empty($equipment['make']) ? " ".$equipment['make'] : "").(!empty($equipment['model']) ? " ".$equipment['model'] : "").(!empty($equipment['unit_number']) ? " ".$equipment['unit_number'] : "");
}
function get_vendor_pricelist($dbc, $pricelistid, $field_name) {
    $get_pricelist =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM vendor_pricelist WHERE	pricelistid='$pricelistid'"));
    return $get_pricelist[$field_name];
}
function get_vendor_price_list($dbc, $pricelistid, $field_name) {
    $get_pricelist =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM vendor_price_list WHERE	inventoryid='$pricelistid'"));
    return $get_pricelist[$field_name];
}
function get_staff_field($dbc, $staffid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM contacts WHERE	contactid='$staffid'"));
    return $get_staff[$field_name];
}

function get_cost_estimate($dbc, $estimateid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM cost_estimate WHERE	estimateid='$estimateid'"));
    return $get_staff[$field_name];
}
function get_estimate($dbc, $estimateid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM estimate WHERE	estimateid='$estimateid'"));
    return $get_staff[$field_name];
}
function get_client_project($dbc, $projectid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM client_project WHERE	projectid='$projectid'"));
    return $get_staff[$field_name];
}
function get_project($dbc, $projectid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM project WHERE	projectid='$projectid'"));
    return $get_staff[$field_name];
}
function get_project_label($dbc, $project) {
	if($project['projectid'] > 0) {
		$project_type = $project['projecttype'];
		$code = '';
		if($project_type != '') {
			foreach(explode(',',PROJECT_TYPES) as $i => $type_name) {
				if($project_type == config_safe_str($type_name)) {
					$project_type = $type_name;
					$code = explode(',',PROJECT_TYPE_CODES)[$i];
				}
			}
		}
		return str_replace(['[PROJECT_NOUN]','[PROJECTID]','[PROJECT_NAME]','[PROJECT_TYPE]','[PROJECT_TYPE_CODE]','[PROJECT_START_DATE]','[YYYY]','[YY]','[YYYY-MM]','[YY-MM]','[BUSINESS]','[CONTACT]'],[PROJECT_NOUN,$project['projectid'],$project['project_name'],$project_type,$code,$project['start_date'],date('Y',strtotime($project['start_date'])),date('y',strtotime($project['start_date'])),date('Y-m',strtotime($project['start_date'])),date('y-m',strtotime($project['start_date'])),get_client($dbc,$project['businessid']),get_contact($dbc,explode(',',trim($project['clientid'],','))[0])],($project['status'] == 'Pending' && get_config($dbc, 'project_status_pending') != 'disable' ? 'Pending ' : '').PROJECT_LABEL);
	}
	return '-';
}
function get_ticket_label($dbc, $ticket, $project_type = null, $project_name = null, $custom_label = '') {
	if($ticket['ticketid'] > 0) {
		if($ticket['ticket_label_date'] >= $ticket['last_updated_time'] && $ticket['ticket_label'] != '' && empty($custom_label)) {
			return $ticket['ticket_label'];
		}
		if($project_type == null && $project_name == null) {
			$project = get_field_value('projecttype project_name', 'project', 'projectid', $ticket['projectid']);
			$project_type = $project['projecttype'];
			$project_name = $project['project_name'];
		} else if($project_type == null) {
			$project_type = get_field_value('projecttype', 'project', 'projectid', $ticket['projectid']);
		} else if($project_name == null) {
			$project_name = get_field_value('project_name', 'project', 'projectid', $ticket['projectid']);
		}
		$code = '';
		if($project_type != '') {
			foreach(explode(',',PROJECT_TYPES) as $i => $type_name) {
				if($project_type == config_safe_str($type_name)) {
					$project_type = $type_name;
					$code = explode(',',PROJECT_TYPE_CODES)[$i];
				}
			}
		}
        $ticket_type = '';
        $ticket_noun = TICKET_NOUN;
        if($ticket['ticket_type'] != '') {
            $ticket_tabs = explode(',',get_config($dbc, 'ticket_tabs'));
            $tile_config = $dbc->query("SELECT `value` FROM `general_configuration` WHERE `name` LIKE 'ticket_split_tiles_%' AND (`value` LIKE '%#*#".$ticket['ticket_type']."|%' OR `value` LIKE '%|".$ticket['ticket_type']."|%' OR `value` LIKE '%#*#".$ticket['ticket_type']."')")->fetch_assoc();
            if(!empty($tile_config)) {
                $ticket_noun = explode('#*#',$tile_config)[1];
            }
            foreach($ticket_tabs as $type_name) {
                if($ticket['ticket_type'] == config_safe_str($type_name)) {
                    $ticket_type = $type_name;
                }
            }
        }
        $ticket_label = TICKET_LABEL;
        if(!empty($custom_label)) {
            $ticket_label = $custom_label;
        }
		$label = str_replace(['[PROJECT_NOUN]','[PROJECTID]','[PROJECT_NAME]','[PROJECT_TYPE]','[PROJECT_TYPE_CODE]','[TICKET_NOUN]','[TICKETID]','[TICKETID-4]','[TICKET_HEADING]','[TICKET_DATE]','[YYYY]','[YY]','[YYYY-MM]','[YY-MM]','[TICKET_SCHEDULE_DATE]','[SCHEDULE_YYYY]','[SCHEDULE_YY]','[SCHEDULE_YYYY-MM]','[SCHEDULE_YY-MM]','[BUSINESS]','[CONTACT]', '[SITE_NAME]', '[TICKET_TYPE]', '[STOP_LOCATION]', '[STOP_CLIENT]', '[ORDER_NUM]'],[PROJECT_NOUN,$ticket['projectid'],$project_name,$project_type,$code,TICKET_NOUN,($ticket['main_ticketid'] > 0 ? $ticket['main_ticketid'].' '.$ticket['sub_ticket'] : $ticket['ticketid']),substr('000'.$ticket['ticketid'],-4),$ticket['heading'],$ticket['created_date'],date('Y',strtotime($ticket['created_date'])),date('y',strtotime($ticket['created_date'])),date('Y-m',strtotime($ticket['created_date'])),date('y-m',strtotime($ticket['created_date'])),$ticket['to_do_date'],date('Y',strtotime($ticket['to_do_date'])),date('y',strtotime($ticket['to_do_date'])),date('Y-m',strtotime($ticket['to_do_date'])),date('y-m',strtotime($ticket['to_do_date'])),get_client($dbc,$ticket['businessid']),get_contact($dbc,explode(',',trim($ticket['clientid'],','))[0]),get_contact($dbc, explode(',',trim($ticket['siteid'],','))[0],'site_name'),$ticket_type,$ticket['location_name'],$ticket['client_name'],$ticket['salesorderid']],($ticket['status'] == 'Archive' ? 'Archived ' : ($ticket['status'] == 'Done' ? 'Done ' : '')).$ticket_label);
        if(empty($custom_label)) {
        	$dbc->query("UPDATE `tickets` SET `ticket_label`='".filter_var($label,FILTER_SANITIZE_STRING)."', `ticket_label_date`=CURRENT_TIMESTAMP WHERE `ticketid`='".$ticket['ticketid']."'");
        }
		return html_entity_decode($label);
	}
	return '-';
}
function get_client_project_detail($dbc, $projectid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM client_project_detail WHERE	projectid='$projectid'"));
    return $get_staff[$field_name];
}

function get_company_rate_card($dbc, $item_id, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM company_rate_card WHERE	item_id='$item_id' AND tile_name = 'Services' AND CURDATE() BETWEEN start_date AND end_date"));
    return $get_staff[$field_name];
}

function get_project_detail($dbc, $projectid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM project_detail WHERE	projectid='$projectid'"));
    return $get_staff[$field_name];
}
function get_tickets($dbc, $ticketid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM tickets WHERE	ticketid='$ticketid'"));
    return $get_staff[$field_name];
}
function get_ticket_document($dbc, $ticketdocid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM ticket_document WHERE	ticketdocid='$ticketdocid'"));
    return $get_staff[$field_name];
}
function get_workorder($dbc, $workorderid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM workorder WHERE	workorderid='$workorderid'"));
    return $get_staff[$field_name];
}

function get_safety($dbc, $safetyid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM safety WHERE	safetyid='$safetyid'"));
    return $get_staff[$field_name];
}
function get_infogathering($dbc, $infogatheringid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM infogathering WHERE	infogatheringid='$infogatheringid'"));
    return $get_staff[$field_name];
}
function get_hr($dbc, $hrid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM hr WHERE	hrid='$hrid'"));
    return $get_staff[$field_name];
}
//

function get_security_levels($dbc) {
	if(mysqli_fetch_assoc(mysqli_query($dbc, "SELECT COUNT(*) rows FROM `security_level_names`"))['rows'] == 0) {
		foreach(mysqli_fetch_assoc(mysqli_query ($dbc, "SELECT * FROM `security_level`")) as $field_name => $value) {
			if(strpos($value, 'turn_on') !== FALSE) {
				mysqli_query($dbc, "INSERT INTO `security_level_names` (`label`, `identifier`, `history`) SELECT '".get_securitylevel($dbc, $field_name)."', '".$field_name."', `".$field_name."_history` FROM `security_level`");
			}
		}

		$before_change = '';
    $history = "Security level names have been added. <br />";
    add_update_history($dbc, 'security_history', $history, '', $before_change);
	}

	$on_security = ['Super Admin'=>'super'];
	$security_levels = mysqli_query($dbc, "SELECT * FROM `security_level_names` WHERE `active` > 0");
	while($level = mysqli_fetch_assoc($security_levels)) {
		$on_security[$level['label']] = $level['identifier'];
	}
	return $on_security;
}

function get_security_levels_staff($dbc) {
	$on_security = ['Super Admin'=>'super'];
	$security_levels = mysqli_query($dbc, "SELECT contactid FROM `contacts` WHERE `category` = 'Staff'");
	while($level = mysqli_fetch_assoc($security_levels)) {
		$on_security[get_contact($dbc, $level['contactid'])] = $level['contactid'];
	}

	return $on_security;
}

function get_securitylevel($dbc, $level) {
	$level_row = mysqli_query($dbc, "SELECT * FROM `security_level_names` WHERE `identifier`='$level'");
	if(mysqli_num_rows($level_row) > 0) {
		return mysqli_fetch_assoc($level_row)['label'];
	} else if($level == 'super') {
        return 'Super Admin';
    } else if($level == 'admin') {
        return 'Admin';
    } else if($level == 'genmanager') {
        return 'General Manager';
    } else if($level == 'chairman') {
        return 'Chairman';
    } else if($level == 'president') {
        return 'President';
    } else if($level == 'vicepres') {
        return 'Vice-President';
    } else if($level == 'coo') {
        return 'Chief Operations Officer';
    } else if($level == 'offad') {
        return 'Office Admin';
    }  else if($level == 'ceo') {
        return 'Chief Executive Officer';
    } else if($level == 'cfo') {
        return 'Chief Financial Officer';
    } else if($level == 'cfd') {
        return 'Chief Financial Director';
    } else if($level == 'cod') {
        return 'Chief Operations Director';
    } else if($level == 'exdirect') {
        return 'Executive Director';
    } else if($level == 'findirect') {
        return 'Financial Director';
    } else if($level == 'salesmarketingdirect') {
        return 'Sales & Marketing Director';
    } else if($level == 'salesdirector') {
        return 'Sales Director';
    } else if($level == 'marketingdirector') {
        return 'Marketing Director';
    } else if($level == 'commsalesdirector') {
        return 'Commercial Sales Director';
    } else if($level == 'vpcorpdev') {
        return 'VP Corporate Development';
    } else if($level == 'vpsales') {
        return 'VP Sales';
    } else if($level == 'operationslead') {
        return 'Operations Lead';
    } else if($level == 'suppchainlogist') {
        return 'Supply Chain & Logistics';
    } else if($level == 'fieldopmanager') {
        return 'Field Operations Manager';
    } else if($level == 'regionalmanager') {
        return 'Regional Manager';
    } else if($level == 'officemanager') {
        return 'Office Manager';
    } else if($level == 'businessdevmanager') {
        return 'Business Development Manager';
    } else if($level == 'controller') {
        return 'Controller';
    } else if($level == 'businessdevcoo') {
        return 'Business Development Coordinator';
    } else if($level == 'opcoord') {
        return 'Operations Coordinator';
    } else if($level == 'safetysup') {
        return 'Safety Supervisor';
    } else if($level == 'fluidhaulingman') {
        return 'Fluid Hauling Manager';
    } else if($level == 'teamcolead') {
        return 'Team Co-Lead';
    } else if($level == 'execassist') {
        return 'Executive Assistant';
    } else if($level == 'assist') {
        return 'Assistant';
    } else if($level == 'fieldsup') {
        return 'Field Supervisor';
    } else if($level == 'waterspec') {
        return 'Water Specialist';
    } else if($level == 'opconsult') {
        return 'Operations Consultant';
    } else if($level == 'manager') {
        return 'Manager';
    } else if($level == 'advocate') {
        return 'Advocate';
    } else if($level == 'supporter') {
        return 'Supporter';
    } else if($level == 'client') {
        return 'Client';
    } else if($level == 'customer') {
        return 'Customer';
    } else if($level == 'lead') {
        return 'Lead';
    } else if($level == 'prospect') {
        return 'Prospect';
    } else if($level == 'executive') {
        return 'Executive';
    } else if($level == 'opsmanager') {
        return 'Operations Manager';
    } else if($level == 'manfmanager') {
        return 'Manufacturing Manager';
    } else if($level == 'mrkmanager') {
        return 'Marketing Manager';
    } else if($level == 'salesmanager') {
        return 'Sales Manager';
    } else if($level == 'hrmanager') {
        return 'HR Manager';
    } else if($level == 'accmanager') {
        return 'Accounting Manager';
    } else if($level == 'invmanager') {
        return 'Inventory Manager';
    } else if($level == 'teamlead') {
        return 'Team Lead';
    } else if($level == 'operations') {
        return 'Operations';
    } else if($level == 'marketing') {
        return 'Marketing';
    } else if($level == 'sales') {
        return 'Sales';
    } else if($level == 'humanres') {
        return 'Human Resources';
    } else if($level == 'accounting') {
        return 'Accounting';
    } else if($level == 'safety') {
        return 'Safety';
    } else if($level == 'fieldops') {
        return 'Field Operations';
    } else if($level == 'assembler') {
        return 'Assembler';
    } else if($level == 'contractor') {
        return 'Contractor';
    } else if($level == 'teammember') {
        return 'Team Member';
    } else if($level == 'staff') {
        return 'Staff';
    } else if($level == 'customers') {
        return 'Customers';
    } else if($level == 'daypass') {
        return 'Daypass';
    } else if($level == 'foreman') {
        return 'Foreman';
    } else if($level == 'shopforeman') {
        return 'Shop Foreman';
    } else if($level == 'shopworker') {
        return 'Shop Worker';
    } else if($level == 'mainshop') {
        return 'Main Shop';
    } else if($level == 'paintshop') {
        return 'Paint Shop';
    } else if($level == 'fieldshop') {
        return 'Field Shop';
    } else if($level == 'office_admin') {
		return 'Office Admin';
	} else if($level == 'supervisor') {
		return 'Supervisor';
	} else if($level == 'master') {
		return 'Master';
	} else if($level == 'therapist') {
		return 'Therapist';
	} else if($level == 'executive_front_staff') {
		return 'Executive Front Staff';
	} else if($level == 'trainer') {
		return 'Trainer';
	}
}

function get_privileges($dbc, $tile,$level) {
	$roles = explode(',',$level);
	$return_priv = '*hide*';
	foreach($roles as $role) {
		if(strtolower($role) == 'super') {
			return '*detailed_dash*detailed_view*detailed_add*detailed_edit*detailed_archive*view_use_add_edit_delete*search*configure*approvals*';
		}
		else if($role != '') {
			$get_pri =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT privileges FROM security_privileges WHERE	tile='$tile' AND level LIKE '$role' UNION SELECT ''"));
			$my_priv = '*';
			if(strpos($get_pri['privileges'],'*hide*') === FALSE || strpos($get_pri['privileges'],'*detailed_dash*') !== FALSE || strpos($get_pri['privileges'],'*detailed_view*') !== FALSE) {
				$this_priv = 2;
                if(strpos($get_pri['privileges'],'*hide*') !== FALSE) {
                    $my_priv .= 'hide*';
                }
                if(strpos($get_pri['privileges'].$return_priv,'*detailed_dash*') !== FALSE) {
                    $my_priv .= 'detailed_dash*';
                }
                if(strpos($get_pri['privileges'].$return_priv,'*detailed_view*') !== FALSE) {
                    $my_priv .= 'detailed_view*';
                }
                if(strpos($get_pri['privileges'].$return_priv,'*detailed_add*') !== FALSE) {
                    $my_priv .= 'detailed_add*';
                }
                if(strpos($get_pri['privileges'].$return_priv,'*detailed_edit*') !== FALSE) {
                    $my_priv .= 'detailed_edit*';
                }
                if(strpos($get_pri['privileges'].$return_priv,'*detailed_archive*') !== FALSE) {
                    $my_priv .= 'detailed_archive*';
                }
				if(strpos($get_pri['privileges'].$return_priv,'*view_use_add_edit_delete*') !== FALSE) {
					$my_priv .= 'view_use_add_edit_delete*';
				}
				if(strpos($get_pri['privileges'].$return_priv,'*search*') !== FALSE) {
					$my_priv .= 'search*';
				}
				if(strpos($get_pri['privileges'].$return_priv,'*configure*') !== FALSE) {
					$my_priv .= 'configure*';
				}
				if(strpos($get_pri['privileges'].$return_priv,'*approvals*') !== FALSE) {
					$my_priv .= 'approvals*';
				}
                if(strpos($get_pri['privileges'].$return_priv,'*strictview') !== FALSE) {
                    $my_priv .= 'strictview*';
                }
				$return_priv = $my_priv;
			}
		}
	}
    return $return_priv;
}

function get_privileges_staff($dbc, $tile,$level) {
	$roles = explode(',',$level);
	$return_priv = '*hide*';
	foreach($roles as $role) {
		if(strtolower($role) == 'super') {
			return '*detailed_dash*detailed_view*detailed_add*detailed_edit*detailed_archive*view_use_add_edit_delete*search*configure*approvals*';
		}
		else if($role != '') {
			$get_pri =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT privileges FROM security_privileges_staff WHERE	tile='$tile' AND staff = $role UNION SELECT ''"));
			$my_priv = '*';
			if(strpos($get_pri['privileges'],'*hide*') === FALSE || strpos($get_pri['privileges'],'*detailed_dash*') !== FALSE || strpos($get_pri['privileges'],'*detailed_view*') !== FALSE) {
				$this_priv = 2;
                if(strpos($get_pri['privileges'],'*hide*') !== FALSE) {
                    $my_priv .= 'hide*';
                }
                if(strpos($get_pri['privileges'].$return_priv,'*detailed_dash*') !== FALSE) {
                    $my_priv .= 'detailed_dash*';
                }
                if(strpos($get_pri['privileges'].$return_priv,'*detailed_view*') !== FALSE) {
                    $my_priv .= 'detailed_view*';
                }
                if(strpos($get_pri['privileges'].$return_priv,'*detailed_add*') !== FALSE) {
                    $my_priv .= 'detailed_add*';
                }
                if(strpos($get_pri['privileges'].$return_priv,'*detailed_edit*') !== FALSE) {
                    $my_priv .= 'detailed_edit*';
                }
                if(strpos($get_pri['privileges'].$return_priv,'*detailed_archive*') !== FALSE) {
                    $my_priv .= 'detailed_archive*';
                }
				if(strpos($get_pri['privileges'].$return_priv,'*view_use_add_edit_delete*') !== FALSE) {
					$my_priv .= 'view_use_add_edit_delete*';
				}
				if(strpos($get_pri['privileges'].$return_priv,'*search*') !== FALSE) {
					$my_priv .= 'search*';
				}
				if(strpos($get_pri['privileges'].$return_priv,'*configure*') !== FALSE) {
					$my_priv .= 'configure*';
				}
				if(strpos($get_pri['privileges'].$return_priv,'*approvals*') !== FALSE) {
					$my_priv .= 'approvals*';
				}
                if(strpos($get_pri['privileges'].$return_priv,'*strictview') !== FALSE) {
                    $my_priv .= 'strictview*';
                }
				$return_priv = $my_priv;
			}
		}
	}
    return $return_priv;
}

function insert_privileges($dbc, $tile) {
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'executive', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'opsmanager', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'manfmanager', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'mrkmanager', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'salesmanager', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'hrmanager', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'accmanager', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'invmanager', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'teamlead', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'operations', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'marketing', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'sales', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'humanres', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'accounting', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'safety', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'fieldops', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'assembler', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'contractor', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'teammember', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'staff', '*hide*')");
    $q1 = mysqli_query($dbc, "INSERT INTO `security_privileges` (tile, level, privileges) VALUES ('$tile', 'customers', '*hide*')");
		$before_change = '';
    $history = "Security Privileges have been added. <br />";
    add_update_history($dbc, 'security_history', $history, '', $before_change);
}

function get_security($dbc, $tile) {
	$security = [];
	$privileges = get_privileges($dbc, $tile, ROLE);
    $security['dashboard'] = (strpos($privileges,'*detailed_dash*') !== FALSE ? 1 : 0);
	$security['visible'] = ($tile == 'admin_settings' ? (stripos(','.ROLE.',',',super,') !== FALSE || stripos(','.ROLE.',',',admin,') !== FALSE ? 1 : 0) : (strpos($privileges,'*hide*') === FALSE || strpos($privileges,'*detailed_view*') !== FALSE ? 1 : 0));
    $security['add'] = (strpos($privileges,'*detailed_add*') !== FALSE ? 1 : 0);
    $security['archive'] = (strpos($privileges,'*detailed_archive*') !== FALSE ? 1 : 0);
	$security['edit'] = (strpos($privileges,'*view_use_add_edit_delete*') !== FALSE || strpos($privileges,'*detailed_edit*') !== FALSE ? 1 : 0);
	$security['search'] = (strpos($privileges,'*search*') !== FALSE ? 1 : 0);
	$security['config'] = (strpos($privileges,'*configure*') !== FALSE ? 1 : 0);
	$security['approval'] = (strpos($privileges,'*approvals*') !== FALSE ? 1 : 0);
	return $security;
}
function approval_visible_function($dbc, $tile) {
	if(strpos(get_privileges($dbc, $tile, ROLE), '*approvals*') !== FALSE) {
		return 1;
	}
	return 0;
}

function config_visible_function($dbc, $tile) {
	if(strpos(get_privileges($dbc, $tile, ROLE), '*configure*') !== FALSE) {
		return 1;
	}
	return 0;
}

function search_visible_function($dbc, $tile) {
	if(strpos(get_privileges($dbc, $tile, ROLE), '*search*') !== FALSE) {
		return 1;
	}
	return 0;
}

function vuaed_visible_function($dbc, $tile) {
	if(strpos(get_privileges($dbc, $tile, ROLE), '*view_use_add_edit_delete*') !== FALSE) {
		return 1;
	}
	return 0;
}

function view_visible_function($dbc, $tile) {
    if(strpos(get_privileges($dbc, $tile, ROLE), '*detailed_view*') !== FALSE) {
        return 1;
    }
    return 0;
}

function dashboard_visible_function($dbc, $tile) {
    if(strpos(get_privileges($dbc, $tile, ROLE), '*detailed_dash*') !== FALSE) {
        return 1;
    }
    return 0;
}

function edit_visible_function($dbc, $tile) {
    if(strpos(get_privileges($dbc, $tile, ROLE), '*detailed_edit*') !== FALSE) {
        return 1;
    }
    return 0;
}
function add_visible_function($dbc, $tile) {
    if(strpos(get_privileges($dbc, $tile, ROLE), '*detailed_add*') !== FALSE) {
        return 1;
    }
    return 0;
}

function archive_visible_function($dbc, $tile) {
    if(strpos(get_privileges($dbc, $tile, ROLE), '*detailed_archive*') !== FALSE) {
        return 1;
    }
    return 0;
}

function strictview_visible_function($dbc, $tile) {
    if(strpos(get_privileges($dbc, $tile, ROLE), '*strictview*') !== FALSE) {
        return 1;
    }
    return 0;
}
function vuaed_links($dbc, $tile, $id, $functions = [ ]) {
	if(!vuaed_visible_function($dbc, $tile)) {
		return "<!--Functions Restricted-->";
	}
	$link = "";
	foreach($functions as $function => $status) {
		if($status === true) {
			if($link != '') {
				$link .= " | ";
			}
			switch($function) {
				case 'view': $link .= "<span data-id='$id' data-action='view' class='vuaed_link'><a>View</a></span>"; break;
				case 'use': $link .= "<span data-id='$id' data-action='use' class='vuaed_link'><a>Use</a></span>"; break;
				case 'add': $link .= "<span data-id='$id' data-action='add' class='vuaed_link'><a>Add</a></span>"; break;
				case 'edit': $link .= "<span data-id='$id' data-action='edit' class='vuaed_link'><a>Edit</a></span>"; break;
				case 'delete': $link .= "<span data-id='$id' data-action='delete' class='vuaed_link'><a>Delete</a></span>"; break;
			}
		}
	}
	return $link;
}

function tile_enabled($dbc, $tile) {
	if(substr($tile, 0, 13) == 'project_type_') {
		return @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `admin_enabled`, `user_enabled` FROM `tile_security` WHERE `tile_name`='project'"));
	} else {
		return @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `admin_enabled`, `user_enabled` FROM `tile_security` WHERE `tile_name`='$tile'"));
	}
}

function tile_visible($dbc, $tile, $level = ROLE, $main_tile = '') {
	// Set main_tile
	if($main_tile == '') {
		$main_tile = $tile;
	}

	// Manual Tile Permission Overrides
	switch($main_tile) {
		case 'admin_settings': return (stripos(','.$level.',',',super,') !== FALSE || stripos(','.$level.',',',admin,') !== FALSE ? 1 : 0); break;
	}

	// Check if the tile is in use, and the user has rights to see it
    if (tile_enabled($dbc, $main_tile)['user_enabled'] == 1 && (strpos(get_privileges($dbc, $tile, $level), '*hide*') === FALSE || strpos(get_privileges($dbc, $tile, $level), '*detailed_dash*') !== FALSE || strpos(get_privileges($dbc, $tile, $level), '*detailed_view*') !== FALSE)) {
		return 1;
	} else if($main_tile == 'orientation' && $dbc->query("SELECT * FROM `orientation_staff` WHERE `staffid`='{$_SESSION['contactid']}' AND `start_date` <= DATE(NOW()) AND `completed`=0")->num_rows > 0) {
		return 1;
	}

	// If it doesn't display, return 0
    return 0;
}

/* Limit description length */
function limit_text($desc, $limit) {
  if ( str_word_count( $desc, 0 ) > $limit ) {
	  $words	= str_word_count( $desc, 2 );
	  $pos		= array_keys( $words );
	  $desc		= substr( $desc, 0, $pos[ $limit ] ) . '...';
  }
  return $desc;
}

function tile_config_function($dbc,$field,$mode='user') {
    $result = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `admin_enabled`, `user_enabled` FROM `tile_security` WHERE `tile_name`='$field'"));
    $config = $result['user_enabled'];
	$admin = $result['admin_enabled'];
	$value = ($mode == 'user' ? $config : $admin);
	$status = ($config == 1 ? 'Used' : ($admin == 1 ? 'Active' : 'Inactive'));
	$disable = ($mode == 'user' && $admin != 1 ? 'disabled' : '');
    ?>
    <td data-title="Turn On"><input type="radio" <?php echo ($value == 1 ? 'checked' : $disable); ?> onchange="tileConfig(this)" name="<?php echo $field;?>" value="turn_on" id="<?php echo $field;?>_turn_on" style="height:20px;width:20px;"></td>
     <?php if($field != 'software_config' && $field != 'profile') { ?>
        <td data-title="Turn Off"><input type="radio" <?php  echo ($value != 1 ? ' checked' : ''); ?> onchange="tileConfig(this)" name="<?php echo $field;?>" value="turn_off" id="<?php echo $field;?>_turn_off" style="height:20px;width:20px;">
        </td>
    <?php } else { ?>
        <td data-title="Turn Off">-</td>
    <?php } ?>
    <td data-title="History"><a><span data-option='<?php echo $field; ?>' class='iframe_open'>View All</span></a></td>
	<td data-title="Status"><?php echo ($status == 'Active' ? 'Active' : ($status == 'Used' ? 'Active - In Use' : 'Inactive - Contact Support')); ?></td><?php
}

/*
 * Title:		Subtab Configuration
 * File:		software_config_subtabs.php
 * Function:	Load saved subtab settings from the database
 */
function subtab_config_function ( $dbc, $tile, $level_url, $subtab ) {
	$row = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `status` FROM `subtab_config` WHERE `tile`='$tile' AND `security_level`='$level_url' AND `subtab`='$subtab'" ) );

    $subtabid   = str_replace ( ['&', '/', ',', ' ', '___', '__'], ['', '_', '', '_', '_', '_'], $subtab );
	//$subtabid	= str_replace( ' ', '_', $subtab );
	$status 	= $row[ 'status' ];
	$date		= explode ( '*#*', $status );

	if ( $status != NULL ) { ?>
		<td align="center"><input type="radio" name="<?= $subtab; ?>" id="<?= $subtabid; ?>_turn_on" value="turn_on" <?= ( strpos ( $status, 'turn_on' ) !== FALSE ) ? ' checked' : ''; ?> onchange="subtabConfig(this)" /></td>
		<td align="center"><input type="radio" name="<?= $subtab; ?>" id="<?= $subtabid; ?>_turn_off" value="turn_off" <?= ( strpos ( $status, 'turn_off' ) !== FALSE ) ? ' checked' : ''; ?> onchange="subtabConfig(this)" /></td><?php

	} else { ?>
		<td align="center"><input type="radio" name="<?= $subtab; ?>" id="<?= $subtabid; ?>_turn_on" value="turn_on" onchange="subtabConfig(this)" /></td>
		<td align="center"><input type="radio" name="<?= $subtab; ?>" id="<?= $subtabid; ?>_turn_off" value="turn_off" onchange="subtabConfig(this)" /></td><?php
	} ?>

	<td align="center"><?php echo ( !empty( $date[1] ) ) ? $date[1] : '-'; ?></td><?php
}

function subtab_staff_config_function ( $dbc, $tile, $level_url, $subtab ) {
	$row = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `status` FROM `subtab_staff_config` WHERE `tile`='$tile' AND `security_level`='$level_url' AND `subtab`='$subtab'" ) );

    $subtabid   = str_replace ( ['&', '/', ',', ' ', '___', '__'], ['', '_', '', '_', '_', '_'], $subtab );
	//$subtabid	= str_replace( ' ', '_', $subtab );
	$status 	= $row[ 'status' ];
	$date		= explode ( '*#*', $status );

	if ( $status != NULL ) { ?>
		<td align="center"><input type="radio" name="<?= $subtab; ?>" id="<?= $subtabid; ?>_turn_on" value="turn_on" <?= ( strpos ( $status, 'turn_on' ) !== FALSE ) ? ' checked' : ''; ?> onchange="subtabConfig(this)" /></td>
		<td align="center"><input type="radio" name="<?= $subtab; ?>" id="<?= $subtabid; ?>_turn_off" value="turn_off" <?= ( strpos ( $status, 'turn_off' ) !== FALSE ) ? ' checked' : ''; ?> onchange="subtabConfig(this)" /></td><?php

	} else { ?>
		<td align="center"><input type="radio" name="<?= $subtab; ?>" id="<?= $subtabid; ?>_turn_on" value="turn_on" onchange="subtabConfig(this)" /></td>
		<td align="center"><input type="radio" name="<?= $subtab; ?>" id="<?= $subtabid; ?>_turn_off" value="turn_off" onchange="subtabConfig(this)" /></td><?php
	} ?>

	<td align="center"><?php echo ( !empty( $date[1] ) ) ? $date[1] : '-'; ?></td><?php
}

/*
 * Title:		Check subtab persmission
 * File:		Multiple files can call this function
 * Function:	Check for subtab persmission within a tile sent to the function
 */
function check_subtab_persmission($dbc, $tile, $level, $subtab) {
	foreach(explode(',',trim($level,',')) as $role) {
		if($role == 'super') {
			return true;
		} else {
			$result = mysqli_query($dbc, "SELECT `status`, `subtab`, `security_level` FROM `subtab_config` WHERE `tile`='$tile' AND `subtab`='$subtab' AND CONCAT(',',`security_level`,',') LIKE '%,$role,%' AND `status` LIKE '%turn_%' UNION SELECT '*turn_on*by_default*' `status`, '$subtab' `subtab`, '$role' `security_level`");
			$config = mysqli_fetch_assoc($result);
			if(strpos($config['status'],'turn_on') !== FALSE) {
				return true;
			}
		}
	}

	return false;
}

/*
 * Title:       Dashboard Permission Configuration
 * File:        software_config_dashboard.php
 * Function:    Load saved dashboard permission settings from the database
 */
function dashboard_config_function ( $dbc, $tile, $level_url, $field ) {
    $row = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT `status` FROM `dashboard_permission_config` WHERE `tile`='$tile' AND `security_level`='$level_url' AND `field`='$field'" ) );

    $fieldid   = str_replace ( ['&', '/', ',', ' ', '___', '__'], ['', '_', '', '_', '_', '_'], $field );
    //$subtabid = str_replace( ' ', '_', $subtab );
    $status     = $row[ 'status' ];
    $date       = explode ( '*#*', $status );

    if ( $status != NULL ) { ?>
        <td align="center"><input type="radio" name="<?= $field; ?>" id="<?= $fieldid; ?>_turn_on" value="turn_on" <?= ( strpos ( $status, 'turn_on' ) !== FALSE ) ? ' checked' : ''; ?> onchange="dashboardPermissionConfig(this)" /></td>
        <td align="center"><input type="radio" name="<?= $field; ?>" id="<?= $fieldid; ?>_turn_off" value="turn_off" <?= ( strpos ( $status, 'turn_off' ) !== FALSE ) ? ' checked' : ''; ?> onchange="dashboardPermissionConfig(this)" /></td><?php

    } else { ?>
        <td align="center"><input type="radio" name="<?= $field; ?>" id="<?= $fieldid; ?>_turn_on" value="turn_on" onchange="dashboardPermissionConfig(this)" /></td>
        <td align="center"><input type="radio" name="<?= $field; ?>" id="<?= $fieldid; ?>_turn_off" value="turn_off" onchange="dashboardPermissionConfig(this)" /></td><?php
    } ?>

    <td align="center"><?php echo ( !empty( $date[1] ) ) ? $date[1] : '-'; ?></td><?php
}

/*
 * Title:       Check dashboard persmission
 * File:        Multiple files can call this function
 * Function:    Check for dashboard persmission within a tile sent to the function
 */
function check_dashboard_persmission($dbc, $tile, $level, $field) {
    foreach(explode(',',trim($level,',')) as $role) {
        if($role == 'super') {
            return true;
        } else {
            $result = mysqli_query($dbc, "SELECT `status`, `field`, `security_level` FROM `dashboard_permission_config` WHERE `tile`='$tile' AND `field`='$field' AND CONCAT(',',`security_level`,',') LIKE '%,$role,%' AND `status` LIKE '%turn_%' UNION SELECT '*turn_on*by_default*' `status`, '$field' `field`, '$role' `security_level`");
            $config = mysqli_fetch_assoc($result);
            if(strpos($config['status'],'turn_on') !== FALSE) {
                return true;
            }
        }
    }

    return false;
}

/*
 * Title:		Get Software Name
 * File:		Any file can call this function
 * Function:	Check for software URL and return the software name
 */
function get_software_name() {
	$software_url	= $_SERVER['SERVER_NAME'];
	$rookconnect	= 'default';

	if ( $software_url == 'sea-alberta.rookconnect.com' || $software_url == 'sea-regina.rookconnect.com' || $software_url == 'sea-saskatoon.rookconnect.com' || $software_url == 'sea-vancouver.rookconnect.com' || $software_url == 'sea.freshfocussoftware.com' ) {
		$rookconnect = 'sea';
	} else if ( $software_url == 'led.rookconnect.com' ) {
		$rookconnect = 'led';
	} else if ( $software_url == 'washtech.precisionworkflow.com' || $software_url == 'washtech.freshfocuscrm.com' || $software_url == 'www.washtechsoftware.com' || $software_url == 'washtechsoftware.com' ) {
		$rookconnect = 'washtech';
	} else if ( $software_url == 'highland.precisionworkflow.com' || $software_url == 'www.highland.precisionworkflow.com' || $software_url == 'highlandprojectssoftware.com' || $software_url == 'www.highlandprojectssoftware.com' ) {
		$rookconnect = 'highland';
	} else if ( stripos( $software_url, 'breakthebarrierinnovation.com' ) !== false ) {
		$rookconnect = 'breakthebarrier';
	} else if ( stripos( $software_url, 'beirut.rookconnect.com' ) !== false ) {
		$rookconnect = 'beirut';
	} else if ( stripos( $software_url, 'prime.rookconnect.com' ) !== false ) {
		$rookconnect = 'prime';
	} else if ( stripos( $software_url, 'clinicace.com' ) !== false ) {
		$rookconnect = 'clinicace';
	} else if ( stripos( $software_url, 'calla.rookconnect.com' ) !== false ) {
		$rookconnect = 'calla';
	} else if ( stripos( $software_url, 'precisionworkflow.com' ) !== false ) {
		$rookconnect = 'precisionworkflow';
	} else if ( stripos( $software_url, 'realtornavigator.com' ) !== false ) {
		$rookconnect = 'realtornavigator';
	} else if ( stripos( $software_url, 'genuine.breakthebarrierinnovation.com' ) !== false ) {
		$rookconnect = 'genuine';
    } else if ( stripos( $software_url, 'ffm.rookconnect.com' ) !== false || stripos( $software_url, 'demo.rookconnect.com' ) !== false ) {
        $rookconnect = 'rook';
	} else if ( $software_url == 'localhost' || $software_url == 'local.rookconnect' ) {
		$rookconnect = 'localhost';
	}

	return $rookconnect;
}

/*
 * Title:		Get Tile Names
 * File:		Multiple files can call this function
 * Function:	Return the list of Tile names
 */
function get_tile_names($tile_list) {
	$tiles = [];
	foreach($tile_list as $tile_name) {
		switch($tile_name) {
			case 'software_config':
				$tiles[] = 'Settings';
				break;
			case 'profile':
				$tiles[] = 'Profile';
				break;
			case 'security':
				$tiles[] = 'Security';
				break;
			case 'client_info':
				$tiles[] = 'Client Information';
				break;
			case 'contacts':
				$tiles[] = 'Contacts';
				break;
			case 'contacts3':
				$tiles[] = 'Contacts 3';
				break;
            case 'contacts_rolodex':
				$tiles[] = 'Contacts Rolodex';
				break;
			case 'documents':
				$tiles[] = 'Documents';
				break;
			case 'infogathering':
				$tiles[] = 'Information Gathering';
				break;
			case 'hr':
				$tiles[] = 'HR';
				break;
			case 'package':
				$tiles[] = 'Packages';
				break;
			case 'promotion':
				$tiles[] = 'Promotions & Coupons';
				break;
			case 'services':
				$tiles[] = 'Services';
				break;
			case 'preformance_review':
				$tiles[] = 'Performance Reviews';
				break;
			case 'training_quiz':
				$tiles[] = 'Training & Quizzes';
				break;
			case 'passwords':
				$tiles[] = 'Passwords';
				break;
			case 'sred':
				$tiles[] = 'SR&ED Projects';
				break;
			case 'labour':
				$tiles[] = 'Labour';
				break;
			case 'material':
				$tiles[] = 'Materials';
				break;
			case 'inventory':
				$tiles[] = 'Inventory';
				break;
			case 'assets':
				$tiles[] = 'Assets';
				break;
			case 'equipment':
				$tiles[] = 'Equipment';
				break;
			case 'custom':
				$tiles[] = 'Custom';
				break;
			case 'invoicing':
				$tiles[] = 'Invoicing';
				break;
			case 'pos':
				$tiles[] = POS_ADVANCE_TILE;
				break;
			case 'incident_report':
				$tiles[] = INC_REP_TILE;
				break;
			case 'policy_procedure':
				$tiles[] = 'Policies & Procedures';
				break;
			case 'ops_manual':
				$tiles[] = 'Operations Manual';
				break;
			case 'emp_handbook':
				$tiles[] = 'Employee Handbook';
				break;
			case 'how_to_guide':
				$tiles[] = 'All Software Guide';
				break;
			case 'software_guide':
				$tiles[] = 'Software Guide';
				break;
			case 'safety':
				$tiles[] = 'Safety';
				break;
			case 'rate_card':
				$tiles[] = 'Rate Cards';
				break;
			case 'estimate':
				$tiles[] = ESTIMATE_TILE;
				break;
			case 'quote':
				$tiles[] = 'Quotes';
				break;
			case 'cost_estimate':
				$tiles[] = 'Cost Estimates';
				break;
			case 'project':
				$tiles[] = 'Projects';
				break;
			case 'jobs':
				$tiles[] = 'Jobs';
				break;
			case 'project_workflow':
				$tiles[] = 'Project Workflow';
				break;
			case 'ticket':
				$tiles[] = TICKET_TILE;
				break;
			case 'field_job':
				$tiles[] = 'Field Jobs';
				break;
			case 'report':
				$tiles[] = 'Reports';
				break;
			case 'field_ticket_estimates':
				$tiles[] = 'Field Ticket Estimates';
				break;
			case 'driving_log':
				$tiles[] = 'Driving Log';
				break;
			case 'expense':
				$tiles[] = 'Expenses';
				break;
			case 'marketing':
				$tiles[] = 'Marketing Projects';
				break;
			case 'internal':
				$tiles[] = 'Internal Projects';
				break;
			case 'rd':
				$tiles[] = 'R&D Projects';
				break;
			case 'business_development':
				$tiles[] = 'Business Development Projects';
				break;
			case 'process_development':
				$tiles[] = 'Process Development Projects';
				break;
			case 'addendum':
				$tiles[] = 'Addendum Projects';
				break;
			case 'addition':
				$tiles[] = 'Addition Projects';
				break;
			case 'manufacturing':
				$tiles[] = 'Manufacturing Projects';
				break;
			case 'assembly':
				$tiles[] = 'Assembly Projects';
				break;
			case 'work_order':
				$tiles[] = 'Work Orders';
				break;
			case 'daysheet':
				$tiles[] = 'Planner';
				break;
			case 'punch_card':
				$tiles[] = 'Time Clock';
				break;
			case 'certificate':
				$tiles[] = 'Certificates';
				break;
			case 'marketing_material':
				$tiles[] = 'Marketing Materials';
				break;
			case 'internal_documents':
				$tiles[] = 'Internal Documents';
				break;
			case 'client_documents':
				$tiles[] = 'Client Documents';
				break;
			case 'contracts':
				$tiles[] = 'Contracts';
				break;
			case 'products':
				$tiles[] = 'Products';
				break;

			case 'tasks':
				$tiles[] = TASK_TILE;
				break;

			/*
            case 'tasks_updated':
				$tiles[] = 'Tasks (Updated)';
				break;
                */
			case 'agenda_meeting':
				$tiles[] = 'Agendas & Meetings';
				break;
			case 'sales':
				$tiles[] = 'Sales';
				break;
			case 'gantt_chart':
				$tiles[] = 'Gantt Chart';
				break;
			case 'communication':
				$tiles[] = 'Communication';
				break;
			case 'purchase_order':
				$tiles[] = 'Purchase Order';
				break;
			case 'orientation':
				$tiles[] = 'Orientation';
				break;
			case 'sales_order':
				$tiles[] = SALES_ORDER_TILE;
				break;
			case 'website':
				$tiles[] = 'Website';
				break;
			case 'vpl':
				$tiles[] = 'Vendor Price List';
				break;
			case 'helpdesk':
				$tiles[] = 'Help Desk';
				break;
			case 'time_tracking':
				$tiles[] = 'Time Tracking';
				break;
			case 'newsboard':
				$tiles[] = 'News Board';
				break;
			case 'customer_support':
				$tiles[] = 'Customer Support';
				break;
			case 'ffmsupport':
				$tiles[] = 'FFM Support';
				break;
			case 'archiveddata':
				$tiles[] = 'Archived Data';
				break;
			case 'email_communication':
				$tiles[] = 'Email Communication';
				break;
			case 'scrum':
				$tiles[] = 'Scrum';
				break;
			case 'charts':
				$tiles[] = 'Charts';
				break;
			case 'daily_log_notes':
				$tiles[] = 'Daily Log Notes';
				break;
			case 'timesheet':
				$tiles[] = 'Time Sheets';
				break;
			case 'staff':
				$tiles[] = 'Staff';
				break;
			case 'checklist':
				$tiles[] = 'Checklist';
				break;
			case 'calllog':
				$tiles[] = 'Cold Call';
				break;
			case 'budget':
				$tiles[] = 'Budget';
				break;
			case 'gao':
				$tiles[] = 'Goals & Objectives';
				break;
			case 'routine':
				$tiles[] = 'Routine Creator';
				break;
			case 'day_program':
				$tiles[] = 'Day Program';
				break;
			case 'match':
				$tiles[] = 'Match';
				break;
			case 'fund_development':
				$tiles[] = 'Fund Development';
				break;
			case 'medication':
				$tiles[] = 'Medication';
				break;
			case 'client_documentation':
				$tiles[] = 'Client Documentation';
				break;
			case 'individual_support_plan':
				$tiles[] = 'Individual Service Plan';
				break;
			case 'social_story':
				$tiles[] = 'Social Story';
				break;
			case 'intake':
				$tiles[] = 'Intake Forms';
				break;
			case 'interactive_calendar':
				$tiles[] = 'Interactive Calendar';
				break;
			case 'client_projects':
				$tiles[] = 'Client Projects';
				break;
			case 'non_verbal_communication':
				$tiles[] = 'Non Verbal Communication';
				break;
			case 'form_builder':
				$tiles[] = 'Form Builder';
				break;
			case 'vendors':
				$tiles[] = VENDOR_TILE;
				break;
			case 'reactivation':
				$tiles[] = 'Follow Up';
				break;
			case 'confirmation':
				$tiles[] = 'Notifications';
				break;
            case 'calendar_rook':
                $tiles[] = 'Calendar';
                break;
            case 'documents_all':
                $tiles[] = 'Documents';
                break;
		}
	}
	//$tiles .= ( !empty ( $get_tiles['properties'] ) ) ? 'Properties,' : '';

	// Sort Tiles alphabetically
	asort($tiles);
	return $tiles;
}

/*
 * Title:		Get Sub Tab Names
 * File:		Multiple files can call this function
 * Function:	Return the list of Sub Tabs in each Tile when the tile name is sent to the function
 * Notes        Update this when a new tile is added / get_tile_names function is updated
 */
function get_subtabs($tile_name) {
	$subtabs = [];
    switch($tile_name) {
        case 'software_config':
            $subtabs = array('Style', 'Formatting', 'Menu Formatting', 'Tile Sort Order', 'My Dashboards', 'Dashboards', 'Software Identity', 'Software Login Page', 'Social Media Links', 'Url Favicon', 'Logo', 'Display Preferences', 'Font Settings', 'Data Usage', 'Notes', 'Ticket Slider');
            break;
        case 'profile':
            $subtabs = array('ID Card');
            break;
        case 'security':
        case 'client_info':
        case 'contacts':
        case 'contacts3':
        case 'contacts_rolodex':
            $subtabs = array('Summary', 'Active', 'Inactive', 'Regions', 'Locations', 'Classifications', 'Titles');
            break;
        case 'documents':
            $subtabs = array('Dashboard', 'Create Tile');
            break;
        case 'infogathering':
            $subtabs = array('Dashboard', 'Reporting', 'PDF Style');
            break;
        case 'hr':
            $hr_tabs = explode(',', get_config($dbc, 'hr_tabs'));
            $general_tabs = array('Summary', 'Favourites', 'Reporting');
            $subtabs = array_merge($hr_tabs, $general_tabs);
            break;
        case 'package':
        case 'promotion':
        case 'labour':
        case 'assets':
        case 'custom':
        case 'training_quiz':
        case 'passwords':
        case 'software_guide':
        case 'marketing_material':
        case 'internal_documents':
        case 'client_documents':
        case 'archiveddata':
        case 'daily_log_notes':
        case 'checklist':
        case 'routine':
        case 'day_program':
        case 'match':
        case 'medication':
        case 'individual_support_plan':
        case 'documents_all':
        case 'quote':
            $subtabs = array('Dashboard');
            break;
        case 'services':
            $subtabs = array('Dashboard', 'Import/Export', 'Pdf Styling', 'Export Templates', 'Service Templates');
            break;
        /* case 'preformance_review':
            $subtabs = 'Performance Reviews';
            break; */
        case 'sred':
            $subtabs = array('Dashboard', 'Favourite', 'Pending');
            break;
        case 'material':
            $subtabs = array('Dashboard', 'Order Lists');
            break;
        case 'inventory':
            $subtabs = array('Dashboard', 'Summary', 'Warehousing', 'Purchase Orders', 'Customer Orders', 'Pallet Nos', 'Pick Lists', 'Inventory Without Cost', 'Receive Shipment', 'Bill Of Material', 'Bill of Material Consumables', 'Waste/Write-Off', 'Checklists', 'Order Lists', 'Order Checklists', 'Import/Export', 'Templates', 'PDF Styling');
            break;
        case 'equipment':
            $subtabs = explode(',',get_config($dbc,'equipment_main_tabs'));
            break;
        case 'invoicing':
            $subtabs = array('Sell', 'Invoices', 'Returns', 'Accounts Receivable', 'Voided Invoices');
            break;
        case 'pos':
            $subtabs = array('Create Invoice', 'Today\'s Invoices', 'All Invoices', 'Invoices', 'Accounts Receivable', 'Voided Invoices', 'Refund/Adjustments', 'Cash Out', 'Gift Card');
            break;
        case 'incident_report':
            $subtabs = array('All Incident Reports', 'Summary', 'Motor Vehicle Accident Form', 'Incident Investigation Form', 'Near Miss');
            break;
        case 'policy_procedure':
        case 'ops_manual':
        case 'emp_handbook':
            $subtabs = array('Manuals', 'Follow Up', 'Reporting');
            break;
        case 'safety':
            $subtabs = 'Favourites';
            break;
        case 'rate_card':
            $subtabs = array('Dashboard');
            $rate_card_tabs = get_config($dbc, 'rate_card_tabs');
            if ( strpos($rate_card_tabs,',customer,') !== false ) { $subtabs[] = array_push($subtabs, 'Customer Specific'); };
            if ( strpos($rate_card_tabs,',company,') !== false ) { $subtabs[] = array_push($subtabs, 'My Company'); };
            if ( strpos($rate_card_tabs,',universal,') !== false ) { $subtabs[] = array_push($subtabs, 'Universal'); };
            if ( strpos($rate_card_tabs,',position,') !== false ) { $subtabs[] = array_push($subtabs, 'Position'); };
            if ( strpos($rate_card_tabs,',staff,') !== false ) { $subtabs[] = array_push($subtabs, 'Staff'); };
            if ( strpos($rate_card_tabs,',category,') !== false ) { $subtabs[] = array_push($subtabs, 'Equipment by Category'); };
            if ( strpos($rate_card_tabs,',services,') !== false ) { $subtabs[] = array_push($subtabs, 'Services'); };
            if ( strpos($rate_card_tabs,',labour,') !== false ) { $subtabs[] = array_push($subtabs, 'Labour'); };
            if ( strpos($rate_card_tabs,',holiday,') !== false ) { $subtabs[] = array_push($subtabs, 'Holiday Pay'); };
            if ( strpos($rate_card_tabs,',expense,') !== false ) { $subtabs[] = array_push($subtabs, 'Expense'); };
            break;
        case 'estimate':
            $subtabs = array('Dashboard', 'Templates', 'Reporting');
            break;
        case 'cost_estimate':
            $subtabs = array('Internal Cost Estimates', 'Customer Cost Estimates');
            break;
        case 'project':
            $subtabs = array('Summary');
            $project_tabs = explode(',', get_config($dbc, 'project_classify'));
            if ( ($key = array_search('All', $project_tabs)) !== false ) {
                unset($project_tabs[$key]);
            }
            $project_tabs = array_values($project_tabs);
            $subtabs = array_merge($subtabs, $project_tabs);
            break;
        /* case 'jobs':
            $subtabs = 'Jobs';
            break; */
        case 'project_workflow':
            $subtabs = array('Active Workflow', 'Add/Edit Workflow');
            break;
        case 'ticket':
            $subtabs = array('Summary', 'Reports', 'Import/Export');
            break;
        case 'field_job':
            $subtabs = array('Sites', 'Jobs', 'Foreman Sheet', 'PO', 'Work Ticket', 'Outstanding Invoices', 'Paid Invoices', 'Payroll');
            break;
        case 'report':
            $subtabs = array('% Breakdown of Services Sold', 'Appointment Summary', 'Archived Ticket Notes', 'Assessment Follow Ups', 'Assessment Tally Board', 'Attached to Tickets', 'Block Booking', 'Block Booking vs Not Block Booking', 'Checklist Time Tracking', 'Credit Card on File', 'Day Sheet Report', 'Detailed Import Report', 'Discharge Report', 'Dispatch Ticket Travel Time', 'Download Tracker', 'Drop Off Analysis', 'Equipment List', 'Equipment Transfer History', 'Field Jobs', 'Import Summary Report', 'Injury Type', 'Inventory Log', 'Manifest Daily Summary ', POS_ADVANCE_TILE, 'Purchase Orders', 'Rate Cards Report', 'Scrum Business Productivity Summary', 'Scrum Staff Productivity Summary', 'Scrum Status Report', 'Shop Work Order Task Time', 'Shop Work Order Time', 'Shop Work Orders', 'Site Work Order Driving Logs', 'Site Work Order Time on Site', 'Site Work Orders', 'Staff Tickets', 'Task Time Tracking', 'Therapist Day Sheet', 'Therapist Stats', 'Ticket Activity Report per Customer', 'Ticket Report', 'Ticket Time Summary', 'Ticket Transport of Inventory', 'Ticket by Task', 'Time Sheets Report', 'Treatment Report', 'Work Order', '*#*', 'Customer History', 'Customer Invoices', 'Daily Deposit Report', 'Deposit Detail', 'Estimate Item Closing % By Quantity ', 'Expense Summary Report', 'Gross Revenue by Staff', 'Inventory Analysis', 'Invoice Sales Summary', 'Monthly Sales by Injury Type', 'POS (Advanced) Sales Summary', 'POS (Advanced) Validation', 'Payment Method List', 'Phone Communication', 'Profit-Loss', 'Receipts Summary Report', 'Sales Estimates', 'Sales History by Customer', 'Sales Summary by Injury Type', 'Sales by Customer Summary', 'Sales by Inventory Summary', 'Sales by Inventory/Service Detail', 'Sales by Service Category', 'Sales by Service Summary', 'Staff Revenue Report', 'Transaction List by Customer', 'Unassigned/Error Invoices', 'Unbilled Invoices', 'Validation by Therapist', '*#*', 'A/R Aging Summary', 'By Invoice# ', 'Collections Report by Customer', 'Customer Aging Receivable Summary', 'Customer Balance Summary', 'Customer Balance by Invoice', 'Insurer Aging Receivable Summary', 'Invoice List', 'POS Receivables', 'UI Invoice Report', '*#*', 'Costs', 'Dollars By Service ', 'Expenses', 'Labour Report', 'Revenue & Receivables', 'Staff & Compensation', 'Summary', '*#*', 'CRM Recommendations - By Customer', 'CRM Recommendations - By Date', 'Cart Abandonment', 'Contact Postal Code', 'Contact Report by Status ', 'Customer Contact List', 'Customer Stats', 'Demographics', 'Driver Report', 'Net Promoter Score', 'POS Coupons', 'Postal Code', 'Pro-Bono', 'Referrals', 'Web Referrals Report', 'Website Visitors', '*#*', 'Adjustment Compensation', 'Compensation: Print Appt. Reports Button', 'Hourly Compensation', 'Statutory Holiday Pay Breakdown', 'Therapist Compensation', 'Time Sheet Payroll', '*#*', 'CRM Recommendations - By Customer', 'Collections Report by Customer', 'Contact Postal Code', 'Customer Balance Summary', 'Customer Balance by Invoice', 'Customer Contact List', 'Customer Invoices', 'Customer Stats', 'Patient Aging Receivable Summary', 'Patient History', 'Sales History by Customer', 'Sales by Customer Summary', 'Service Rates & Hours ', 'Transaction List by Customer', '*#*', 'Day Sheet Report', 'Gross Revenue by Staff', 'Scrum Staff Productivity Summary', 'Staff Compensation ', 'Staff Revenue Report', 'Staff Tickets', 'Therapist Day Sheet', 'Therapist Stats', 'Validation by Therapist');
            break;
        case 'field_ticket_estimates':
            $subtabs = array('Bid', 'Cost Estimate');
            break;
        case 'driving_log':
            $subtabs = array('Dashboard', 'Start New Driving Log', 'Edit/View Driving Logs', '14 Day Driving Logs', 'Log Time Off', 'Mileage');
            break;
        case 'expense':
            $subtabs = array('Dashboard', 'Pending', 'Approved', 'Paid', 'Declined', 'Expense List', 'Reporting');
            break;
        /* case 'marketing':
            $subtabs = 'Marketing Projects';
            break;
        case 'internal':
            $subtabs = 'Internal Projects';
            break;
        case 'rd':
            $subtabs = 'R&D Projects';
            break;
        case 'business_development':
            $subtabs = 'Business Development Projects';
            break;
        case 'process_development':
            $subtabs = 'Process Development Projects';
            break;
        case 'addendum':
            $subtabs = 'Addendum Projects';
            break;
        case 'addition':
            $subtabs = 'Addition Projects';
            break;
        case 'manufacturing':
            $subtabs = 'Manufacturing Projects';
            break;
        case 'assembly':
            $subtabs = 'Assembly Projects';
            break;
        case 'work_order':
            $subtabs = 'Work Orders';
            break; */
        case 'daysheet':
            $subtabs = array('Day Sheet', 'My Journal', 'My Alerts', 'My Projects', 'My Tickets', 'My Tasks', 'My Checklists');
            break;
        /* case 'punch_card':
            $subtabs = 'Time Clock';
            break; */
        case 'certificate':
            $subtabs = array('Dashboard', 'Active Staff - Completed', 'Active Staff - Pending', 'Active Staff - Expiry Pending', 'Active Staff - Expired', 'Suspended Staff - Completed', 'Suspended Staff - Pending', 'Suspended Staff - Expiry Pending', 'Suspended Staff - Expired', 'Follow Up', 'Reporting');
            break;
        case 'contracts':
            $subtabs = array('Favourites');
            break;
        case 'products':
            $subtabs = array('Dashboard', 'Add Multiple Products');
            break;

        case 'tasks':
            $subtabs = array('Summary', 'Private Tasks', 'Shared Tasks', 'Project Tasks', 'Contact Tasks', 'Sales Tasks', 'Reporting');
            break;

        /*case 'tasks_updated':
            $subtabs = array('Summary', 'Private Tasks', 'Shared Tasks', 'Project Tasks', 'Contact Tasks', 'Sales Tasks', 'Reporting');
            break;*/
        case 'agenda_meeting':
            $subtabs = array('Agendas', 'Meetings');
            break;
        case 'sales':
            $subtabs = array('Dashboard', 'Status', 'Staff', 'Region', 'Location', 'Classification');
            break;
        case 'gantt_chart':
            $subtabs = array('Estimated', 'Gantt Chart');
            break;
        case 'communication':
            $subtabs = array('Email Schedule', 'Phone Schedule', 'Internal', 'External', 'Log');
            break;
        case 'purchase_order':
            $subtabs = array('Create an Order', 'Pending Orders', 'Receiving', 'Accounts Payable', 'Completed Purchase Orders');
            break;
        /* case 'orientation':
            $subtabs = 'Orientation';
            break; */
        case 'sales_order':
            $subtabs = array('Dashboard', 'Status', 'Region', 'Location', 'Classification');
            break;
        /* case 'website':
            $subtabs = 'Website';
            break;
        case 'vpl':
            $subtabs = 'Vendor Price List';
            break;
        case 'helpdesk':
            $subtabs = 'Help Desk';
            break; */
        case 'time_tracking':
            $subtabs = array('Time Tracking', 'Shop Time Sheets');
            break;
        case 'newsboard':
            $subtabs = array('Dashboard');
            break;
        /* case 'ffmsupport':
            $subtabs = 'FFM Support';
            break; */
        case 'email_communication':
            $subtabs = array('Internal', 'External', 'Log');
            break;
        case 'scrum':
            $subtabs = array('Notes', 'Tickets', 'Tasks', 'Staff', 'Status', 'Projects');
            break;
        case 'charts':
            $subtabs = array('Blood Glucose', 'Bowel Movement', 'Daily Dishwasher Temp', 'Daily Freezer Temp', 'Daily Fridge Temp', 'Daily Water Temp (Client)', 'Daily Water Temp (Program)', 'New Custom Chart', 'Seizure Record');
            break;
        case 'timesheet':
            $subtabs = array('Coordinator Approvals', 'Holidays', 'Manager Approvals', 'Pay Period', 'Payroll', 'Reporting', 'Time Sheets');
            break;
        case 'staff':
            $subtabs = array('Active Users', 'Suspended Users', 'Security Privileges', 'Positions', 'Reminders');
            break;
        case 'calllog':
            $subtabs = array('Target Market', 'Objections', 'Scripts', '*#*', 'Not Scheduled', 'Scheduled', 'Missed Call', 'Past Due', '*#*', 'Schedule', '*#*', 'Available Leads', 'Abandoned Leads', '*#*', 'Daily', 'Weekly', 'Bi-Monthly', 'Monthly', 'Quarterly', 'Semi-Annually', 'Yearly', '*#*', 'Reporting');
            break;
        case 'budget':
            $subtabs = array('Pending Budgets', 'Active Budgets', 'Budget Expense Tracking');
            break;
        case 'gao':
            $subtabs = array('Company Goals', 'Department Goals', 'My Goals');
            break;
        case 'fund_development':
            $subtabs = array('Funders', 'Funding');
            break;
        /* case 'client_documentation':
            $subtabs = ''; //Files pulled from each directory
            break; */
        case 'social_story':
            $subtabs = array('Activities', 'Communication', 'Key Methodologies', 'Learning Techniques', 'Patterns', 'Protocols', 'Routines');
            break;
        case 'intake':
            $subtabs = array('Website Forms', 'Forms');
            break;
        /* case 'interactive_calendar':
            $subtabs = 'Interactive Calendar';
            break;
        case 'client_projects':
            $subtabs = 'Client Projects';
            break; */
        case 'non_verbal_communication':
            $subtabs = array('Emotions', 'Activities');
            break;
        case 'form_builder':
            $subtabs = array('Custom Forms', 'Reporting');
            break;
        case 'vendors':
            $subtabs = array('Summary', 'Active', 'Inactive', 'Regions', 'Locations', 'Classifications', 'Vendor Price List');
            break;
        /* case 'reactivation':
            $subtabs = 'Follow Up';
            break; */
        case 'confirmation':
            $subtabs = array('48-Hour Confirmation Email', '1 Month Confirmation Email');
            break;
        /* case 'calendar_rook':
            $subtabs = 'Calendar';
            break; */
    }

	// Sort Sub Tabs alphabetically
	asort($subtabs);
	return $subtabs;
}

/*
 * Title:		Get How To Guide
 * Function:	Return the list result
 */
function get_how_to_guide($dbc, $tile_name) {
    $query		= "SELECT * FROM `how_to_guide` WHERE `deleted`=0 AND `tile`='$tile_name' ORDER BY `sort_order`";
    $result	    = mysqli_query ( $dbc, $query );
    return $result;
}

function project_workflow_function($dbc,$field) {
    $value = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `user_enabled` FROM `tile_security` WHERE `tile_name`='$field'"))['user_enabled']; ?>
    <td data-title="Unit Number"><input type="radio" <?= ($value == 1 ? 'checked' : '') ?> onchange="tileConfig(this)" name="<?= $field ?>" value="turn_on" id="<?= $field ?>_turn_on" style="height:20px;width:20px;"></td>
     <?php if($field != 'software_config' && $field != 'profile') { ?>
        <td data-title="Unit Number"><input type="radio" <?= ($value == 0 ? 'checked' : '') ?> onchange="tileConfig(this)" name="<?= $field ?>" value="turn_off" id="<?= $field ?>_turn_off" style="height:20px;width:20px;">
        </td>
    <?php } else { ?>
        <td>-</td>
    <?php } ?>
<?php }

function addOrUpdateUrlParam($name, $value)
{
    $params = $_GET;
    unset($params[$name]);
    $params[$name] = $value;
    return basename($_SERVER['PHP_SELF']).'?'.http_build_query($params);
}

function addOrUpdateCurrentUrlParam($names, $values)
{
    $params = $_GET;
    $count = 0;
    foreach($names as $name) {
        unset($params[$name]);
        $params[$name] = $values[$count];
        $count++;
    }

    return basename($_SERVER['PHP_SELF']).'?'.http_build_query($params);
}

function get_project_manage($dbc, $projectid, $field) {
	$sql = "SELECT * FROM `project_manage` WHERE `projectmanageid`='$projectid'";
	$result = mysqli_fetch_array(mysqli_query($dbc, $sql));
	return $result[$field];
}

//Clinic Ace Function
function get_all_form_contact($dbc, $contactid, $field_name) {
    $get_staff = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM	contacts WHERE	contactid='$contactid'"));
	if(isEncrypted($field_name))
		return decryptIt($get_staff[$field_name]);
	else
		return $get_staff[$field_name];
}

function get_patientform($dbc, $patientformid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM patientform WHERE	patientformid='$patientformid'"));
    return $get_staff[$field_name];
}

function get_contact_phone($dbc, $contactid) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT office_phone, cell_phone, home_phone, category FROM	contacts WHERE	contactid='$contactid'"));
        $phone = '';
        if($get_staff['cell_phone'] != '') {
            $phone .= '(M)'.decryptIt($get_staff['cell_phone']);
            $phone .= '<br>';
        }

        if($get_staff['office_phone'] != '') {
            $phone .= '(O)'.decryptIt($get_staff['office_phone']);
            $phone .= '<br>';
        }

        if($get_staff['home_phone'] != '') {
            $phone .= '(H)'.decryptIt($get_staff['home_phone']);
        }
        return $phone;
}

function get_contact_first_phone($dbc, $contactid) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT office_phone, cell_phone, home_phone, category FROM	contacts WHERE	contactid='$contactid'"));
	return $get_staff['cell_phone'] != '' ? decryptIt($get_staff['cell_phone']) : ($get_staff['office_phone'] != '' ? decryptIt($get_staff['office_phone']) : decryptIt($get_staff['home_phone']));
}

function get_formid_from_patientform($dbc, $patientformid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	patientform WHERE	patientformid='$patientformid'"));
    return $get_staff[$field_name];
}
function get_patient_from_booking($dbc, $bookingid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	booking WHERE	bookingid='$bookingid'"));
    return $get_staff[$field_name];
}
function get_all_from_inventory($dbc, $inventoryid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM inventory WHERE	inventoryid='$inventoryid'"));
    return $get_staff[$field_name];
}
function get_all_from_invoice($dbc, $invoiceid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	invoice WHERE	invoiceid='$invoiceid'"));
    return $get_staff[$field_name];
}

function get_all_from_referralid($dbc, $referralid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	crm_referrals WHERE	referralid='$referralid'"));
    return $get_staff[$field_name];
}

function get_history($dbc, $patientid) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT COUNT(invoiceid) AS total_invoice FROM	invoice WHERE	patientid='$patientid'"));
    return $get_staff['total_invoice'];
}
function get_all_from_service($dbc, $serviceid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	services WHERE	serviceid='$serviceid'"));
    return $get_staff[$field_name];
}
function get_id_from_servicetype($dbc, $service_type) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT cost FROM	services WHERE	service_type='$service_type'"));
    return $get_staff['cost'];
}
function get_all_from_patient($dbc, $contactid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM patients WHERE	contactid='$contactid'"));
    return $get_staff[$field_name];
}
function get_all_from_invoice_insurer($dbc, $invoiceinsurerid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM invoice_insurer WHERE	invoiceinsurerid='$invoiceinsurerid'"));
    return $get_staff[$field_name];
}

function get_all_from_invoice_patient($dbc, $invoicepatientid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM invoice_patient WHERE	invoicepatientid='$invoicepatientid'"));
    return $get_staff[$field_name];
}

function get_all_from_injury($dbc, $injuryid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM	patient_injury WHERE	injuryid='$injuryid'"));
    return $get_staff[$field_name];
}
function get_all_from_assessment($dbc, $injuryid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM assessment WHERE	injuryid='$injuryid'"));
    return $get_staff[$field_name];
}
function get_all_from_treatment($dbc, $injuryid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM treatment WHERE	injuryid='$injuryid'"));
    return $get_staff[$field_name];
}
function get_all_from_exeplan($dbc, $injuryid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM treatment_exercise_plan WHERE	injuryid='$injuryid'"));
    return $get_staff[$field_name];
}
function get_all_from_treatmentplan($dbc, $injuryid, $field_name) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM treatment_plan WHERE	injuryid='$injuryid'"));
    return $get_staff[$field_name];
}

function get_type_from_booking($dbc, $type) {
    $booking_type = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `appointment_type` WHERE `id` = '$type'"))['name'];
    return $booking_type;

    //OLD BOOKING TYPES FOR CALLA HAIR
    // if($type == 'A') {
    //     $booking_type = 'Ladies Haircuts';
    // }
    // if($type == 'B') {
    //     $booking_type = 'Mens Haircuts';
    // }
    // if($type == 'C') {
    //     $booking_type = 'Color Services';
    // }
    // if($type == 'D') {
    //     $booking_type = 'Perms';
    // }
    // if($type == 'E') {
    //     $booking_type = 'Break';
    // }
    // if($type == 'F') {
    //     $booking_type = 'Updos and Makeup';
    // }
    // if($type == 'G') {
    //     $booking_type = 'Waxing Tinting and Threading';
    // }
    // if($type == 'H') {
    //     $booking_type = 'Eyelash Extensions';
    // }
    // if($type == 'I') {
    //     $booking_type = 'Holiday';
    // }
    // if($type == 'J') {
    //     $booking_type = 'Nail Enhancements';
    // }
    // if($type == 'K') {
    //     $booking_type = 'Manicures and Pedicures';
    // }
    // if($type == 'L') {
    //     $booking_type = 'Facials';
    // }
    // if($type == 'M') {
    //     $booking_type = 'Extras';
    // }
    // if($type == 'N') {
    //     $booking_type = 'OPI-OXXIUM-Shellac Gel Polish';
    // }
    // if($type == 'O') {
    //     $booking_type = 'Piercing';
    // }
    // if($type == 'P') {
    //     $booking_type = '';
    // }
    // if($type == 'Q') {
    //     $booking_type = 'No Book Days';
    // }
    // if($type == 'R') {
    //     $booking_type = 'Vacation';
    // }
    // if($type == 'S') {
    //     $booking_type = '';
    // }
    // if($type == 'T') {
    //     $booking_type = '';
    // }
    // if($type == 'U') {
    //     $booking_type = '';
    // }
    // if($type == 'V') {
    //     $booking_type = '';
    // }
    // if($type == 'W') {
    //     $booking_type = '';
    // }
    // if($type == 'X') {
    //     $booking_type = '';
    // }
    // if($type == 'Y') {
    //     $booking_type = '';
    // }
    // if($type == 'Z') {
    //     $booking_type = '';
    // }

    //OLD BOOKING TYPES FOR CLINIC ACE SOFTWARE
    // if($type == 'A') {
    //     $booking_type = 'Private-PT-Assessment';
    // }
    // if($type == 'B') {
    //     $booking_type = 'Private-PT-Treatment';
    // }
    // if($type == 'C') {
    //     $booking_type = 'MVC-IN-PT-Assessment';
    // }
    // if($type == 'D') {
    //     $booking_type = 'MVC-IN-PT-Treatment';
    // }
    // if($type == 'E') {
    //     $booking_type = 'Break';
    // }
    // if($type == 'F') {
    //     $booking_type = 'MVC-OUT-PT-Assessment';
    // }
    // if($type == 'G') {
    //     $booking_type = 'MVC-OUT-PT-Treatment';
    // }
    // if($type == 'H') {
    //     $booking_type = 'WCB-PT-Assessment';
    // }
    // if($type == 'I') {
    //     $booking_type = 'Holiday';
    // }
    // if($type == 'J') {
    //     $booking_type = 'WCB-PT-Treatment';
    // }
    // if($type == 'K') {
    //     $booking_type = 'Private-MT';
    // }
    // if($type == 'L') {
    //     $booking_type = 'MVC-IN-MT';
    // }
    // if($type == 'M') {
    //     $booking_type = 'MVC-OUT-MT';
    // }
    // if($type == 'N') {
    //     $booking_type = 'AHS-PT-Assessment';
    // }
    // if($type == 'O') {
    //     $booking_type = 'AHS-PT-Treatment';
    // }
    // if($type == 'P') {
    //     $booking_type = '';
    // }
    // if($type == 'Q') {
    //     $booking_type = 'No Book Days';
    // }
    // if($type == 'R') {
    //     $booking_type = 'Vacation';
    // }
    // if($type == 'S') {
    //     $booking_type = 'Reassessment';
    // }
    // if($type == 'T') {
    //     $booking_type = 'Post-Reassessment';
    // }
    // if($type == 'U') {
    //     $booking_type = 'Private-MT-Assessment';
    // }
    // if($type == 'V') {
    //     $booking_type = 'Orthotics';
    // }
    // if($type == 'W') {
    //     $booking_type = 'Osteopathic-Assessment';
    // }
    // if($type == 'X') {
    //     $booking_type = 'Osteopathic-Treatment';
    // }
    // if($type == 'Y') {
    //     $booking_type = 'LT-Assessment';
    // }
    // if($type == 'Z') {
    //     $booking_type = 'LT-Treatment';
    // }
}

function isEncrypted($field_name) {
	return in_array($field_name, ['name', 'first_name', 'last_name', 'prefer_name', 'password', 'office_phone', 'cell_phone', 'home_phone', 'email_address', 'second_email_address', 'office_email','company_email', 'business_street', 'business_city', 'business_state', 'business_country', 'business_zip', 'health_care_no']);
}

function encryptIt( $q ) {
    if($q != '') {
        $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
        $qEncoded      = base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), $q, MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ) );
        return( $qEncoded );
    }
}

function decryptIt( $q ) {
    if($q != '') {
        $cryptKey  = 'qJB0rGtIn5UB1xG03efyCp';
        $qDecoded      = rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_256, md5( $cryptKey ), base64_decode( $q ), MCRYPT_MODE_CBC, md5( md5( $cryptKey ) ) ), "\0");
        return( $qDecoded );
    }
}

function get_calid_from_bookingid($dbc, $bookingid) {
    $get_staff =	mysqli_fetch_assoc(mysqli_query($dbc,"SELECT calid FROM booking WHERE	bookingid='$bookingid'"));
    return $get_staff['calid'];
}

function get_user_settings() {
	if($user = mysqli_fetch_assoc(mysqli_query($_SERVER['DBC'], "SELECT * FROM `user_settings` WHERE `contactid`='".$_SESSION['contactid']."'"))) {
		return $user;
	} else {
		return [];
	}
}
function set_user_settings($dbc, $name, $value) {
	session_start(['cookie_lifetime' => 518400]);
	$_SESSION['user_preferences']['loaded_time'] = 0;
	session_write_close();
	$name = filter_var($name, FILTER_SANITIZE_STRING);
	$value = filter_var($value, FILTER_SANITIZE_STRING);
	$contactid = $_SESSION['contactid'];
	mysqli_query($dbc, "INSERT INTO `user_settings` (`contactid`) SELECT '$value' FROM (SELECT COUNT(*) rows FROM `user_settings` WHERE `contactid`='$contactid') num WHERE num.rows=0");
	mysqli_query($dbc, "UPDATE `user_settings` SET `$name`='$value' WHERE `contactid`='$contactid'");
}

// Convert user strings to and from config safe strings
function config_safe_str($str) {
	return preg_replace('/[^a-z0-9_]/','',str_replace(' ','_',strtolower($str)));
}
function config_user_str($str, $user_list) {
	foreach($user_list as $user_str) {
		if(config_safe($user_str) == $str) {
			return $user_str;
		}
	}
}
// Prepare a filename to be used that will not overlap, and will not contain any special characters
function file_safe_str($str, $folder = 'download/') {
	if($str == '') {
		return '';
	}
	$filename = $str = preg_replace('/[^\.A-Za-z0-9]/','',$str);
	for($i = 1; file_exists($folder.$filename); $i++) {
		$filename = preg_replace('/(\.[A-Za-z0-9]*)/', ' ('.$i.')$1', $str);
	}
	return $filename;
}

// Contacts Search and Sort Functions
function search_contacts_table($dbc, $search_key, $search_constraints, $search_pos = 'ANY') {
	$id_list = [];

	$search_encrypted = encryptIt($search_key);
	$clause = " WHERE `deleted`=0 AND `show_hide_user`=1 AND `status`=1 AND (`first_name`='$search_encrypted' OR `last_name`='$search_encrypted' OR `name`='$search_encrypted' OR `email_address`='$search_encrypted' OR `office_phone`='$search_encrypted' OR `role`='$search_key') ";
	$query = "SELECT `contactid` FROM `contacts`".$clause.$search_constraints;

	$full_result = mysqli_query($dbc, $query);
	if(mysqli_num_rows($full_result) > 0) {
		while($row = mysqli_fetch_array($full_result)) {
			$id_list[] = $row['contactid'];
		}

		return implode(',',$id_list);
	} else {
		$query = "SELECT `contactid`, `first_name`, `last_name`, `name`, `email_address`, `office_phone`, `role`, `businessid` FROM `contacts` WHERE `deleted`=0 AND `show_hide_user`=1 ".$search_constraints;
		$encrypted_result = mysqli_query($dbc, $query);

		if(mysqli_num_rows($encrypted_result) > 0) {
			while($row = mysqli_fetch_assoc($encrypted_result)) {
				if(($search_pos == 'ANY' && stripos(decryptIt($row['first_name']), $search_key) !== FALSE) || ($search_pos == 'START' && stripos(decryptIt($row['first_name']), $search_key) === 0) || ($search_pos == 'FIRST' && stripos(decryptIt($row['first_name']), $search_key) !== FALSE) ||
					($search_pos == 'ANY' && stripos(decryptIt($row['last_name']), $search_key) !== FALSE) || ($search_pos == 'START' && stripos(decryptIt($row['last_name']), $search_key) === 0) || ($search_pos == 'LAST' && stripos(decryptIt($row['last_name']), $search_key) !== FALSE) ||
					($search_pos == 'ANY' && stripos(decryptIt($row['name']), $search_key) !== FALSE) || ($search_pos == 'START' && stripos(decryptIt($row['name']), $search_key) === 0) || ($search_pos == 'NAME' && stripos(decryptIt($row['name']), $search_key) !== FALSE) ||
					($search_pos == 'ANY' && stripos(decryptIt($row['email_address']), $search_key) !== FALSE) || ($search_pos == 'START' && stripos(decryptIt($row['email_address']), $search_key) === 0) ||( $search_pos == 'EMAIL' && stripos(decryptIt($row['email_address']), $search_key) !== FALSE) ||
					($search_pos == 'ANY' && stripos(decryptIt($row['office_phone']), $search_key) !== FALSE) || ($search_pos == 'START' && stripos(decryptIt($row['office_phone']), $search_key) === 0) || ($search_pos == 'PHONE' && stripos(decryptIt($row['office_phone']), $search_key) !== FALSE) ||
					($search_pos == 'ANY' && stripos(decryptIt($row['role']), $search_key) !== FALSE) || ($search_pos == 'START' && stripos(decryptIt($row['role']), $search_key) === 0) || ($search_pos == 'ROLE' && stripos(decryptIt($row['role']), $search_key) !== FALSE)) {
					$id_list[] = $row['contactid'];
                    if(!empty($row['businessid'])) {
                        $id_list[] = $row['businessid'];
                    }
                    $result2 = mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `businessid` = '".$row['contactid']."'");
                    while($row2 = mysqli_fetch_assoc($result2)) {
                        $id_list[] = $row2['contactid'];
                    }
				}
			}

            array_unique($id_list);
			if(count($id_list) > 0) {
				return implode(',',$id_list);
			}
		}
	}
	return '0';
}

function sort_contacts_array($contact_list, $sortby = null) {
	$sorted = [];
	$sort_mode = get_user_settings()['contacts_sort_order'];
	$sort_mode = ($sort_mode > 0 ? $sort_mode : get_config($_SERVER['DBC'], 'system_contact_sort'));
	if($sortby == null) {
		$sortby = ($sort_mode == 1 ? 'first_name' : 'last_name');
	}
	for($i = 0; $i < count($contact_list); $i++) {
		$id = $contact_list[$i]['contactid'];
        if(empty(MATCH_CONTACTS) || in_array($id, explode(',', MATCH_CONTACTS))) {
    		$status = 'Last';
    		if($sortby != null) {
    			if(strpos($contact_list[$i]['is_favourite'],",".$_SESSION['contactid'].",") !== FALSE) {
    				$status = ($contact_list[$i]['status'] == 0 ? 'Last' : 'First');
    			} else if(!empty($contact_list[$i]['status'])) {
    				$status = ($contact_list[$i]['status'] == 0 ? 'Last' : 'First');
    			}

    			if(isEncrypted($sortby)) {
    				if(decryptIt($contact_list[$i][$sortby]) == '')
    					$key = $status.'zzz'.decryptIt($contact_list[$i]['name']).$contact_list[$i]['display_name'].$contact_list[$i]['site_name'].$contact_list[$i]['contactid'];
    				else
    					$key = $status.decryptIt($contact_list[$i][$sortby]).decryptIt($contact_list[$i]['name']).$contact_list[$i]['display_name'].$contact_list[$i]['site_name'].$contact_list[$i]['contactid'];
    			}
    			else {
    				if($sortby == 'businessid')
    					$contact_list[$i][$sortby] = get_client($_SERVER['DBC'], $contact_list[$i][$sortby]);
    				if(trim($contact_list[$i][$sortby]) == '')
    					$key = $status.'zzz'.decryptIt($contact_list[$i]['name']).$contact_list[$i]['display_name'].$contact_list[$i]['site_name'].$contact_list[$i]['contactid'];
    				else
    					$key = $status.$contact_list[$i][$sortby].decryptIt($contact_list[$i]['name']).$contact_list[$i]['display_name'].$contact_list[$i]['site_name'].$contact_list[$i]['contactid'];
    			}
    		}
    		else {
                $contacts_sort_order = mysqli_fetch_array(mysqli_query($_SERVER['DBC'], "SELECT `contacts_sort_order` FROM `user_settings` WHERE `contactid` = '" . $_SESSION['contactid'] . "'"))['contacts_sort_order'];
    			if(strpos($contact_list[$i]['is_favourite'],",".$_SESSION['contactid'].",") !== FALSE) {
    				$status = ($contact_list[$i]['status'] == 0 ? 'Last' : 'Favourite');
    			} else if(!empty($contact_list[$i]['status'])) {
    				$status = ($contact_list[$i]['status'] == 0 ? 'Last' : 'First');
    			}
    			if(stripos($contact_list[$i]['category'], 'Business') !== FALSE || stripos($contact_list[$i]['category'], 'Vendor') !== FALSE) {
    				$key = $status.$contact_list[$i]['category'].decryptIt($contact_list[$i]['name']).decryptIt($contact_list[$i]['last_name']).decryptIt($contact_list[$i]['first_name']).$contact_list[$i]['contactid'];
    			} else if(stripos($contact_list[$i]['category'], SITES_CAT) !== FALSE) {
    				$key = $status.$contact_list[$i]['category'].$contact_list[$i]['display_name'].$contact_list[$i]['site_name'].$contact_list[$i]['contactid'];
    			} else if ($contacts_sort_order == 1) {
                    $key = $status.$contact_list[$i]['category'].decryptIt($contact_list[$i]['first_name']).decryptIt($contact_list[$i]['last_name']).decryptIt($contact_list[$i]['name']).$business.$contact_list[$i]['contactid'];
                } else {
    				$key = $status.$contact_list[$i]['category'].decryptIt($contact_list[$i]['last_name']).decryptIt($contact_list[$i]['first_name']).decryptIt($contact_list[$i]['name']).$business.$contact_list[$i]['contactid'];
    			}
    		}
    		$sorted[$key] = $id;
        }
	}

	ksort($sorted, SORT_STRING);
	return $sorted;
}
function sort_contacts_query($contact_query, $sort_order = 'auto') {
	$sorted = [];
	$sort_mode = get_user_settings()['contacts_sort_order'];
	$sort_mode = ($sort_mode > 0 ? $sort_mode : get_config($_SERVER['DBC'], 'system_contact_sort'));
	$sort_order = ($sort_order == 'auto' && $sort_mode == 1 ? 'first_name' : $sort_order);
	while($contact = mysqli_fetch_assoc($contact_query)) {
        if(empty(MATCH_CONTACTS) || in_array($contact['contactid'], explode(',', MATCH_CONTACTS))) {
            $status = 'Last';
    		foreach($contact as $field => $value) {
    			if(isEncrypted($field)) {
    				$contact[$field] = decryptIt($value);
    			}
    		}
    		$key = ($contact['status'] == 0 ? 'inactive' : 'active');
    		$key .= $contact['category'];
    		if($sort_order != 'auto' && isEncrypted($sort_order)) {
    			$key .= $contact[$sort_order];
    		} else if($sort_order != 'auto') {
    			$key .= $contact[$sort_order];
    		} else if(strpos_any(['Business','Vendor'], $contact['category'])) {
    			$key .= $contact['name'];
    		} else if(strpos($contact['category'],SITES_CAT) !== FALSE) {
    			$key .= $contact['display_name'].$contact['site_name'];
    		}
    		$key .= $contact['last_name'];
    		$key .= $contact['first_name'];
    		$key .= $contact['prefer_name'];
    		$key .= $contact['nick_name'];
    		$key .= $contact['name'];
    		$key .= $contact['display_name'];
    		$key .= $contact['site_name'];
    		$key .= $contact['contactid'];
    		$contact['full_name'] = trim($contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').$contact['first_name'].' '.$contact['last_name'].' '.(empty($contact['display_name']) ? $contact['site_name'] : $contact['display_name']));
    		if($contact['full_name'] == '') {
    			$contact['full_name'] = '-';
    		}
    		$contact['person_name'] = trim($contact['first_name'].' '.$contact['last_name']);
    		if($contact['person_name'] == '') {
    			$contact['person_name'] = '-';
    		}

    		$sorted[$key] = $contact;
        }
	}
	ksort($sorted, SORT_STRING);
	return $sorted;
}

function in_array_starts($needle, $array) {
	return count(array_filter($array, function($line) use ($needle) { return (strpos($line, $needle) === 0); })) > 0;
}
function strpos_any($arr_needle, $string) {
	foreach($arr_needle as $needle) {
		if(strpos($string, $needle) !== false) {
			return true;
		}
	}
	return false;
}
function in_array_any($arr_needles, $arr_haystack) {
	foreach($arr_needles as $needle) {
		if(in_array($needle, $arr_haystack)) {
			return true;
		}
	}
	return false;
}
function sortByLastName($a) {
	$tmp = $a;
	foreach($tmp as $k => $v){
		$tmp[$k] = substr($v,strrpos($v, ' ')+1);
	}
	asort($tmp);
	$ret = array();
	foreach($tmp as $k => $v){
		$ret[$k] = $a[$k];
	}
	return $ret;
}

/* Convert Decimal Hours to Hours:Minutes */
function time_decimal2time($decimal_time, $pad = false) {
	$minutes = round($decimal_time * 60);
	$hours = ($pad ? sprintf('%02d',floor($minutes / 60)) : floor($minutes / 60));
	$minutes -= ($hours * 60);
	return $hours.':'.sprintf('%02d',$minutes);
}

/* Convert Time to Decimal */
function time_time2decimal($time) {
    if(strpos(strtolower($time), 'am') !== FALSE || strpos(strtolower($time), 'pm') !== FALSE) {
        $hms = explode(':', $time);
        $ampm = explode(' ',$hms[1]);
        if(strtolower($ampm[1]) == 'pm' && $hms[0] != 12) {
            $hms[0] += 12;
        }
        return ($hms[0] + ($ampm[0]/60));
    } else {
        $hms = explode(':', $time);
        return ($hms[0] + ($hms[1]/60) + ($hms[2]/3600));
    }
}

// Check if resource exists at URL
function url_exists($url) {
	if(stripos($url,WEBSITE_URL) === FALSE) {
		return stripos(get_headers($url)[0], '404') === FALSE;
	} else {
		return file_exists($_SERVER['DOCUMENT_ROOT'].substr($url,stripos($url,WEBSITE_URL)+strlen(WEBSITE_URL)));
	}
}

// Get users with alerts enabled
function alerts_enabled($dbc, $id, $type) {
	switch($type) {
	case 'checklist':
		return array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `alerts_enabled` users FROM `checklist` WHERE `checklistid`='$id'"))['users'])));
		break;
	case 'checklist_name':
		return array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT CONCAT(IFNULL(cn.`alerts_enabled`,''),',',IFNULL(cl.`alerts_enabled`,'')) users FROM `checklist_name` cn LEFT JOIN `checklist` cl ON cn.`checklistid`=cl.`checklistid` WHERE cn.`checklistnameid`='$id'"))['users'])));
		break;
	}
}
// Convert Bytes to the nearest Binary Multiple size
function roundByteSize($bytes) {
	$label = $bytes.' B';
	if($bytes > 1024) {
		$bytes /= 1024;
		$label = round($bytes,2).' KiB';
	}
	if($bytes > 1024) {
		$bytes /= 1024;
		$label = round($bytes,2).' MiB';
	}
	if($bytes > 1024) {
		$bytes /= 1024;
		$label = round($bytes,2).' GiB';
	}
	if($bytes > 1024) {
		$bytes /= 1024;
		$label = round($bytes,2).' TiB';
	}
	if($bytes > 1024) {
		$bytes /= 1024;
		$label = round($bytes,2).' PiB';
	}
	if($bytes > 1024) {
		$bytes /= 1024;
		$label = round($bytes,2).' EiB';
	}
	if($bytes > 1024) {
		$bytes /= 1024;
		$label = round($bytes,2).' ZiB';
	}
	if($bytes > 1024) {
		$bytes /= 1024;
		$label = round($bytes,2).' YiB';
	}
	return $label;
}

function track_download($dbc, $table, $id, $link, $description = '') {
	$table = filter_var($table, FILTER_SANITIZE_STRING);
	$id = filter_var($id, FILTER_SANITIZE_STRING);
	$staff = $_SESSION['contactid'];
	$link = filter_var($link, FILTER_SANITIZE_STRING);
	$description = filter_var($description, FILTER_SANITIZE_STRING);
	mysqli_query($dbc, "INSERT INTO `download_tracking` (`table_name`, `tableid`, `staffid`, `download_link`, `description`) VALUES ('$table', '$id', '$staff', '$link', '$description')");
}
function getFraction($float) {
	for($i = 1; $i<=100; $i++) {
		if(($i*$float*1000) % 1000 == 0) {
			return ($float >= 1 ? floor($float).($i > 1 ? ' ' : '') : '').($i > 1 ? ($float*$i%$i).'/'.$i : '');
		}
	}
}
function resize_image_convert_png($newWidth, $newHeight, $targetFile, $originalFile) {
    $info = getimagesize($originalFile);
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image_create_func = 'imagecreatefromjpeg';
            $image_save_func = 'imagepng';
            $new_image_ext = 'png';
            break;

        case 'image/png':
            $image_create_func = 'imagecreatefrompng';
            $image_save_func = 'imagepng';
            $new_image_ext = 'png';
            break;

        case 'image/gif':
            $image_create_func = 'imagecreatefromgif';
            $image_save_func = 'imagepng';
            $new_image_ext = 'png';
            break;

        default:
            throw new Exception('Unknown image type.');
    }

    $img = $image_create_func($originalFile);
    list($width, $height) = getimagesize($originalFile);

    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    if (file_exists($targetFile)) {
        unlink($targetFile);
    }
    $image_save_func($tmp, "$targetFile.$new_image_ext");
    return "$targetFile.$new_image_ext";
}
function resize_image_convert_jpg($newWidth, $newHeight, $targetFile, $originalFile) {
    $info = getimagesize($originalFile);
    $mime = $info['mime'];

    switch ($mime) {
        case 'image/jpeg':
            $image_create_func = 'imagecreatefromjpeg';
            $image_save_func = 'imagejpeg';
            $new_image_ext = 'jpg';
            break;

        case 'image/png':
            $image_create_func = 'imagecreatefrompng';
            $image_save_func = 'imagejpeg';
            $new_image_ext = 'jpg';
            break;

        case 'image/gif':
            $image_create_func = 'imagecreatefromgif';
            $image_save_func = 'imagejpeg';
            $new_image_ext = 'jpg';
            break;

        default:
            throw new Exception('Unknown image type.');
    }

    $img = $image_create_func($originalFile);
    list($width, $height) = getimagesize($originalFile);

    $tmp = imagecreatetruecolor($newWidth, $newHeight);
    imagecopyresampled($tmp, $img, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

    if (file_exists($targetFile)) {
        unlink($targetFile);
    }
    $image_save_func($tmp, "$targetFile.$new_image_ext");
    return "$targetFile.$new_image_ext";
}
function get_reminder_url($dbc, $reminder, $slider = 0) {
    $check_project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `reminder_type`, `body` FROM `reminders` WHERE `reminderid`='".$reminder['reminderid']."' AND (`reminder_type`='QUICK' OR `reminder_type` LIKE 'PROJECT%')"));
    $reminder_projectid = '';
    if ( $check_project['reminder_type']=='QUICK' ) {
        preg_match("/edit=([0-9]+)/", $check_project['body'], $output);
        $reminder_projectid = $output[1];
    } else {
        $reminder_projectid = preg_replace('/[^0-9]/', '', $check_project['reminder_type']);
    }

    $reminder_url = '';
    if(!empty($reminder_projectid)) {
        if($slider == 1) {
            $reminder_url = WEBSITE_URL.'/Project/projects.php?iframe_slider=1&edit='.$reminder_projectid;
        } else {
            $reminder_url = WEBSITE_URL.'/Project/projects.php?edit='.$reminder_projectid;
        }
    } else if(!empty($reminder['src_table'])) {
        if($slider == 1) {
            switch($reminder['src_table']) {
                case 'tickets':
                    $reminder_url = WEBSITE_URL.'/Ticket/index.php?calendar_view=true&edit='.$reminder['src_tableid'];
                    break;
                case 'ticket_alerts':
                    $reminder_url = WEBSITE_URL.'/Ticket/index.php?calendar_view=true&edit='.$reminder['src_tableid'].'&from_alert=1';
                    break;
                case 'checklist_name':
                    $reminder_url = WEBSITE_URL.'/Checklist/checklist.php?iframe_slider=1&view='.$reminder['src_tableid'];
                    break;
                case 'client_daily_log_notes':
                    $reminder_url = WEBSITE_URL.'/Daily Log Notes/log_note_list.php?display_contact='.$reminder['contactid'];
                    break;
                case 'equipment':
                case 'equipment_insurance':
                case 'equipment_registration':
                    $reminder_url = WEBSITE_URL.'/Equipment/edit_equipment.php?edit='.$reminder['src_tableid']+'&iframe_slider=1';
                    break;
                case 'projects':
                    $reminder_url = WEBSITE_URL.'/Project/projects.php?iframe_slider=1&edit='.$reminder['src_tableid'];
                    break;
                case 'sales':
                    $reminder_url = WEBSITE_URL.'/Sales/sale.php?iframe_slider=1&p=details&id='.$reminder['src_tableid'];
                    break;
                case 'hr':
                    $reminder_url = WEBSITE_URL.'/HR/index.php?hr='.$reminder['src_tableid'];
                    break;
                case 'manuals':
                    $reminder_url = WEBSITE_URL.'/HR/index.php?manual='.$reminder['src_tableid'];
                    break;
                case 'contacts':
                    $reminder_url = WEBSITE_URL.'/'.ucwords($reminder['reminder_type']).'/contacts_inbox.php?category='.$reminder['reminder_type'].'&edit='.$reminder['src_tableid'];
                    break;
                case 'position_rate_table':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=position&status=add&id='.$reminder['src_tableid'];
                    break;
                case 'staff_rate_table':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=staff&status=add&id='.$reminder['src_tableid'];
                    break;
                case 'equipment_rate_table':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=equipment&status=add&id='.$reminder['src_tableid'];
                    break;
                case 'category_rate_table':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=category&status=add&id='.$reminder['src_tableid'];
                    break;
                case 'tile_rate_card':
                    $rate_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tile_rate_card` WHERE `ratecardid` = '".$reminder['src_tableid']."'"));
                    switch($rate_card['tile_name']) {
                        case 'labour':
                            $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=labour&status=add&id='.$reminder['src_tableid'];
                            break;
                    }
                    break;
                case 'service_rate_card':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=services&status=add&id='.$reminder['src_tableid'];
                    break;
                case 'company_rate_card':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=company&status=add&id='.$reminder['src_tableid'];
                    break;
                case 'rate_card':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=customer&status=add&ratecardid='.$reminder['src_tableid'];
                    break;
                case 'intake':
                    $intake = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid`='".$reminder['src_tableid']."'"));
                    if($intake['projectid'] > 0) {
                        $reminder_url = WEBSITE_URL.'/Project/projects.php?iframe_slider=1&edit='.$intake['projectid'];
                    } else if($intake['ticketid'] > 0) {
                        $reminder_url = WEBSITE_URL.'/Ticket/index.php?calendar_view=true&edit='.$intake['ticketid'];
                    } else if($intake['salesid'] > 0) {
                        $reminder_url = WEBSITE_URL.'/Sales/sale.php?iframe_slider=1&p=details&id='.$intake['salesid'];
                    } else if($intake['contactid'] > 0) {
                        $reminder_url = WEBSITE_URL.'/'.ucwords(get_contact($dbc, $intake['contactid'], 'tile_name')).'/contacts_inbox.php?edit='.$reminder['src_tableid'];
                    }
                    break;
                case 'email_communication':
                    $reminder_url = WEBSITE_URL.'/Email Communication/view_email.php?email_communicationid='.$reminder['src_tableid'];
                    break;
            }
        } else {
            switch($reminder['src_table']) {
                case 'tickets':
                    $reminder_url = WEBSITE_URL.'/Ticket/index.php?edit='.$reminder['src_tableid'];
                    break;
                case 'ticket_alerts':
                    $reminder_url = WEBSITE_URL.'/Ticket/index.php?edit='.$reminder['src_tableid'].'&from_alert=1';
                    break;
                case 'checklist_name':
                    $reminder_url = WEBSITE_URL.'/Checklist/checklist.php?view='.$reminder['src_tableid'];
                    break;
                case 'calllog':
                    $reminder_url = WEBSITE_URL.'/Cold Call/add_call_log.php?calllogid='.$reminder['src_tableid'];
                    break;
                case 'calllog_goals':
                    $reminder_url = WEBSITE_URL.'/Cold Call/field_config_call_log_goals.php?calllog_goal='.$reminder['src_tableid'];
                    break;
                case 'client_daily_log_notes':
                    $reminder_url = WEBSITE_URL.'/Daily Log Notes/index.php?display_contact='.$reminder['contactid'];
                    break;
                case 'equipment':
                case 'equipment_insurance':
                case 'equipment_registration':
                    $reminder_url = WEBSITE_URL.'/Equipment/index.php?edit='.$reminder['src_tableid'];
                    break;
                case 'projects':
                    $reminder_url = WEBSITE_URL.'/Project/projects.php?edit='.$reminder['src_tableid'];
                    break;
                case 'sales':
                    $reminder_url = WEBSITE_URL.'/Sales/sale.php?p=preview&id='.$reminder['src_tableid'];
                    break;
                case 'task_board':
                    $reminder_url = WEBSITE_URL.'/Tasks_Updated/index.php?category='.$reminder['src_tableid'].'&tab='.get_task_board($dbc, $reminder['src_tableid'], 'board_security');
                    break;
                case 'calendar':
                    $reminder_url = WEBSITE_URL.'/Calendar/calendars.php';
                    break;
                case 'staff_reminders':
                    $reminder_url = WEBSITE_URL.'/Staff/add_reminder.php?reminderid='.$reminder['reminderid'];
                    break;
                case 'hr':
                    $reminder_url = WEBSITE_URL.'/HR/index.php?hr='.$reminder['src_tableid'];
                    break;
                case 'manuals':
                    $reminder_url = WEBSITE_URL.'/HR/index.php?manual='.$reminder['src_tableid'];
                    break;
                case 'contacts':
                    $reminder_url = WEBSITE_URL.'/'.ucwords($reminder['reminder_type']).'/contacts_inbox.php?category='.$reminder['reminder_type'].'&edit='.$reminder['src_tableid'];
                    break;
                case 'position_rate_table':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=position&status=add&id='.$reminder['src_tableid'];
                    break;
                case 'staff_rate_table':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=staff&status=add&id='.$reminder['src_tableid'];
                    break;
                case 'equipment_rate_table':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=equipment&status=add&id='.$reminder['src_tableid'];
                    break;
                case 'category_rate_table':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=category&status=add&id='.$reminder['src_tableid'];
                    break;
                case 'tile_rate_card':
                    $rate_card = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tile_rate_card` WHERE `ratecardid` = '".$reminder['src_tableid']."'"));
                    switch($rate_card['tile_name']) {
                        case 'labour':
                            $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=labour&status=add&id='.$reminder['src_tableid'];
                            break;
                    }
                    break;
                case 'service_rate_card':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=services&status=add&id='.$reminder['src_tableid'];
                    break;
                case 'company_rate_card':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=company&status=add&id='.$reminder['src_tableid'];
                    break;
                case 'rate_card':
                    $reminder_url = WEBSITE_URL.'/Rate Card/ratecards.php?type=customer&status=add&ratecardid='.$reminder['src_tableid'];
                    break;
                case 'holidays_update':
                    $reminder_url = WEBSITE_URL.'/Timesheet/holidays.php';
                    break;
                case 'intake':
                    $intake = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `intake` WHERE `intakeid`='".$reminder['src_tableid']."'"));
                    if($intake['projectid'] > 0) {
                        $reminder_url = WEBSITE_URL.'/Project/projects.php?edit='.$intake['projectid'];
                    } else if($intake['ticketid'] > 0) {
                        $reminder_url = WEBSITE_URL.'/Ticket/index.php?edit='.$intake['ticketid'];
                    } else if($intake['salesid'] > 0) {
                        $reminder_url = WEBSITE_URL.'/Sales/sale.php?p=preview&id='.$intake['salesid'];
                    } else if($intake['contactid'] > 0) {
                        $reminder_url = WEBSITE_URL.'/'.ucwords(get_contact($dbc, $intake['contactid'], 'tile_name')).'/contacts_inbox.php?edit='.$reminder['src_tableid'];
                    }
                    break;
                case 'email_communication':
                    $email_comm = mysqli_fetch_array(mysqli_query($dbc, "SELECT `projectid` FROM `email_communication` WHERE `email_communicationid`='".$reminder['src_tableid']."'"));
                    if($email_comm['projectid'] > 0) {
                        $reminder_url = WEBSITE_URL.'/Project/projects.php?edit='.$email_comm['projectid'].'&tab=email';
                    } else {
                        $reminder_url = WEBSITE_URL.'/Email Communication/view_email.php?email_communicationid='.$reminder['src_tableid'];
                    }
                    break;
            }
        }
    }

    return $reminder_url;
}
function is_id($var) {
	return $var > 0;
}
function set_last_active($dbc, $contactid) {
    $last_active = date('Y-m-d H:i:s');
    mysqli_query($dbc, "INSERT INTO `contacts_last_active` (`contactid`) SELECT '$contactid' FROM (SELECT COUNT(*) rows FROM `contacts_last_active` WHERE `contactid` = '$contactid') num WHERE num.rows = 0");
    mysqli_query($dbc, "UPDATE `contacts_last_active` SET `last_active` = '$last_active' WHERE `contactid` = '$contactid'");
}
function get_last_active($dbc, $contactid) {
    $last_active = mysqli_fetch_array(mysqli_query($dbc, "SELECT `last_active` FROM `contacts_last_active` WHERE `contactid` = '$contactid'"));
    return $last_active;
}

// Track How Long the Page took to load, and register it to run when the script completes
function track_page_load() {
	$duration = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
	$url = filter_var((isset($_SERVER["HTTPS"]) ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].$_SERVER['SCRIPT_NAME'].(!empty($_SERVER['QUERY_STRING']) ? '?'.$_SERVER['QUERY_STRING'] : ''),FILTER_SANITIZE_STRING);
	$ip = $_SERVER['REMOTE_ADDR'];
	$user = filter_var(decryptIt($_SESSION['first_name']).' '.decryptIt($_SESSION['last_name']).' - '.$_SESSION['user_name'],FILTER_SANITIZE_STRING);
	$_SERVER['page_load_time'] = microtime(true) - $_SERVER['REQUEST_TIME_FLOAT'];
	$_SERVER['page_load_info'] .= 'Execution Complete: '.number_format($_SERVER['page_load_time'],5)."\n";
	$info = filter_var($_SERVER['page_load_info'],FILTER_SANITIZE_STRING);
	$_SERVER['DBC']->query("INSERT INTO `page_load_times` (`url`,`duration`,`ip`,`user`,`info`) VALUES ('$url','$duration','$ip','$user','$info')");
}
register_shutdown_function('track_page_load');

function get_ticket_status_icon($dbc, $status) {
    $ticket_statuses = explode(',',get_config($dbc, 'ticket_status'));
    $ticket_status_icons = explode(',',get_config($dbc, 'ticket_status_icons'));
    foreach($ticket_statuses as $i => $ticket_status) {
        if($ticket_status == $status) {
            if($ticket_status_icons[$i] == 'initials') {
                return 'initials';
            } else {
                return get_ticket_status_icon_url($ticket_status_icons[$i]);
            }
        }
    }
}
function get_ticket_status_icon_url($icon) {
    $icon_url = '';
    switch($icon) {
        case 'complete':
            $icon_url = WEBSITE_URL.'/img/icons/submitted.png';
            break;
        case 'incomplete':
            $icon_url = WEBSITE_URL.'/img/icons/cancel.png';
            break;
        case 'alert':
            $icon_url = WEBSITE_URL.'/img/icons/yellow-alert.png';
            break;
        case 'ongoing':
            $icon_url = WEBSITE_URL.'/img/icons/in-progress.png';
            break;
    }
    return $icon_url;
}
function get_project_task_board($projectid) {
    $get_project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT projecttype, project_name FROM project WHERE projectid='$projectid'"));
    $projecttype = ucwords(str_replace('_', ' ', $get_project['projecttype']));
    $task_board_name = $projecttype.': '.$get_project['project_name'];
    $check_task_board = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT taskboardid, COUNT(taskboardid), `task_path` count FROM task_board WHERE board_name='$task_board_name'"));
    if ( $check_task_board['count']>0 ) {
        $taskboardid = $check_task_board['taskboardid'];
    } else {
        mysqli_query($dbc, "INSERT INTO task_board (board_name, board_security, company_staff_sharing, task_path) VALUES ('$task_board_name', 'Project', ',".$_SESSION['contactid'].",', '".explode(',',$get_project['project_path'])[0]."')");
        $taskboardid = mysqli_insert_id($dbc);
		$check_task_board['task_path'] = explode(',',$get_project['project_path'])[0];
    }
	return ['id'=>$taskboardid,'path'=>get_field_value('milestone', 'project_path_milestone', 'project_path_milestone', $check_task_board['task_path'])];
}
function get_calendar_today_color($dbc) {
    $contactid = $_SESSION['contactid'];
    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM contacts WHERE contactid='$contactid'"));
    $software_config = $get_config['software_styler_choice'];
    if ($software_config == '' && @$default_style != '') {
        $software_config = $default_style;
    }

    if($software_config == 'swr') {
        return 'b8bac6';
    } else if ($software_config == 'bwr'){
        return '580003';
    } else if ($software_config == 'blw'){
        return '00aeef';
    } else if ($software_config == 'bgw'){
        return '31A844';
    } else if ($software_config == 'silver'){
        return '6cb993';
    } else if ($software_config == 'blackpurple'){
        return '5f008b';
    } else if ($software_config == 'blackred'){
        return 'cc0000';
    } else if ($software_config == 'washt'){
        return '000000';
    } else if ($software_config == 'btb'){
        return '5ce8c7';
    } else if ($software_config == 'blackneonred'){
        return 'e83324';
    } else if ($software_config == 'blackneon'){
        return '006565';
    } else if ($software_config == 'blackgold'){
        return 'ab8036';
    } else if ($software_config == 'blackorange'){
        return 'dc6214';
    } else if ($software_config == 'ffm'){
        return '43D6DD';
    } else if ($software_config == 'garden'){
        return '6e78b0';
    } else if ($software_config == 'green'){
        return '228B22';
    } else if ($software_config == 'navy'){
        return '455884';
    } else if ($software_config == 'purp'){
        return '5f008b';
    } else if ($software_config == 'turq'){
        return '04EBB0';
    } else if ($software_config == 'leo'){
        return 'B37220';
    } else if ($software_config == 'polka'){
        return '000000';
    } else if ($software_config == 'chrome'){
        return 'a1a1a1';
    } else if ($software_config == 'cosmos'){
        return '2933A9';
    } else if ($software_config == 'flowers'){
        return 'E6B6EB';
    } else if ($software_config == 'realtordark'){
        return '0d9fb3';
    } else if ($software_config == 'realtorlight'){
        return '0d9fb3';
    } else if ($software_config == 'clouds'){
        return '0066b3';
    } else if ($software_config == 'orangeblue'){
        return 'dc6a2e';
    } else if ($software_config == 'dots'){
        return '78c6ce';
    } else if ($software_config == 'pinkdots'){
        return 'df5a87';
    } else if ($software_config == 'intuatrack'){
        return 'f04345';
    } else if ($software_config == 'happy'){
        return 'FFC300';
    } else if ($software_config == 'transport'){
        return 'b1d349';
    } else {
        return '00aeef';
    }
}
function darken_color($hex, $num = 20, $override_max = false) {
    list($r, $g, $b) = sscanf($hex, "%02x%02x%02x");
    if($override_max) {
        $max_subtract = $num;
        if($r <= $max_subtract) {
            $max_subtract = $r;
        }
        if($g <= $max_subtract) {
            $max_subtract = $g;
        }
        if($b <= $max_subtract) {
            $max_subtract = $b;
        }
        $r -= $max_subtract;
        $g -= $max_subtract;
        $b -= $max_subtract;
    } else {
        $max_subtract = $num;
        $r -= $max_subtract;
        $g -= $max_subtract;
        $b -= $max_subtract;
        if($r < 0) {
            $r = 0;
        }
        if($g < 0) {
            $g = 0;
        }
        if($b < 0) {
            $b = 0;
        }
    }
    $new_color = sprintf("%02x%02x%02x", $r, $g, $b);

    if($new_color == $hex && $new_color != '000000') {
        return darken_color($hex, 10, true);
    } else {
        return $new_color;
    }
}
function get_initials($string) {
    $words = explode(' ',$string);
    $initials = '';
    foreach($words as $word) {
        $initials .= substr($word,0,1);
    }
    return $initials;
}
function get_delivery_color($dbc, $type) {
    $color = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_ticket_delivery_color` WHERE `delivery` = '$type'"))['color'];
    return $color;
}
function convert_timestamp_mysql($dbc, $timestamp, $local = false) {
    $mysql_time_offset = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT TIMEDIFF(NOW(), UTC_TIMESTAMP) `time_offset`"))['time_offset'];
    $time_arr = explode(':', $mysql_time_offset);
    $mysql_offset_seconds = ($time_arr[0] * 3600) + ($time_arr[1] * 60) + $time_arr[2];

    $timezone = new DateTimeZone(date_default_timezone_get());
    $datenow = new DateTime("now", $timezone);
    $offset_seconds = $timezone->getOffset($datenow);

    $offset_diff = $mysql_offset_seconds - ($local ? $_SESSION['time_offset'] : $offset_seconds);
    $new_timestamp = date('Y-m-d H:i:s', strtotime($timestamp) + $offset_diff);

    return $new_timestamp;
}
function get_recurrence_days($limit = 0, $start_date, $end_date, $repeat_type, $repeat_interval, $repeat_days, $repeat_monthly, $create_starting_at) {
    if(empty($create_starting_at)) {
        $create_starting_at = $start_date;
    }
    $recurring_dates = [];
    $reached_limit = 0;
    if(date('l', strtotime($start_date)) != 'Sunday') {
        $compare_start_date = date('Y-m-d', strtotime('last Sunday', strtotime($start_date)));
    } else {
        $compare_start_date = $start_date;
    }
    for($cur = $compare_start_date; strtotime($cur) <= strtotime($end_date) && ($reached_limit <= $limit || $limit == 0); $cur = date('Y-m-d', strtotime($cur.' + '.$repeat_interval.' '.$repeat_type))) {
        if($repeat_type == 'week') {
            foreach($repeat_days as $repeat_day) {
                if($repeat_day == date('l', strtotime($cur))) {
                    $recurring_date = $cur;
                } else {
                    $recurring_date = date('Y-m-d', strtotime('next '.$repeat_day, strtotime($cur)));
                }
                if(strtotime($recurring_date) >= strtotime($start_date) && strtotime($recurring_date) <= strtotime($end_date) && strtotime($recurring_date) >= strtotime($create_starting_at)) {
                    $recurring_dates[] = $recurring_date;
                    $reached_limit++;
                }
            }
        } else if($repeat_type == 'month' && in_array($repeat_monthly,['first','second','third','fourth','last'])) {
            $year_month = date('Y-m', strtotime($cur));
            foreach($repeat_days as $repeat_day) {
                $recurring_date = date('Y-m-d', strtotime($repeat_monthly.' '.$repeat_day.' of '.$year_month));
                if(strtotime($recurring_date) >= strtotime($start_date) && strtotime($recurring_date) <= strtotime($end_date) && strtotime($recurring_date) >= strtotime($create_starting_at)) {
                    $recurring_dates[] = $recurring_date;
                    $reached_limit++;
                }
            }
        } else {
            if(strtotime($cur) >= strtotime($create_starting_at)) {
                $recurring_dates[] = $cur;
                $reached_limit++;
            }
        }
    }
    return $recurring_dates;
}

function create_recurring_tickets($dbc, $ticketid, $start_date, $end_date, $repeat_type, $repeat_interval, $repeat_days, $repeat_monthly, $skip_first = '', $create_starting_at) {
    //Get all ticket rows from tickets, ticket_attached, ticket_schedule, and ticket_comment
    $ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid' AND `deleted` = 0"));
    $ticket_attacheds = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `deleted` = 0"),MYSQLI_ASSOC);
    $ticket_schedules = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid` = '$ticketid' AND `deleted` = 0"),MYSQLI_ASSOC);
    $ticket_comments = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_comment` WHERE `ticketid` = '$ticketid' AND `deleted` = 0"),MYSQLI_ASSOC);

    //Insert all rows with recurring date
    if(empty(str_replace(['0000-00-00','1969-12-31'],'',$end_date))) {
        $sync_upto = !empty(get_config($dbc, 'ticket_recurrence_sync_upto')) ? get_config($dbc, 'ticket_recurrence_sync_upto') : '2 years';
        $end_date = date('Y-m-d', strtotime(date('Y-m-d').' + '.$sync_upto));
    }
    $recurring_dates = get_recurrence_days(0, $start_date, $end_date, $repeat_type, $repeat_interval, $repeat_days, $repeat_monthly, $create_starting_at);
    if($skip_first == 1) {
        array_shift($recurring_dates);
    }
    foreach($recurring_dates as $recurring_date) {
        $date_exists = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `main_ticketid` = '$ticketid' AND `to_do_date` = '$recurring_date' AND `deleted` = 0"));
        if(empty($date_exists)) {
            //Insert into tickets with to_do_date/to_do_end_date as the recurring date
            mysqli_query($dbc, "INSERT INTO `tickets` (`main_ticketid`, `to_do_date`, `to_do_end_date`, `is_recurrence`) VALUES ('$ticketid', '$recurring_date', '$recurring_date', 1)");
            $new_ticketid = mysqli_insert_id($dbc);

            //Insert all ticket_attached records with the new ticketid
            foreach($ticket_attacheds as $ticket_attached) {
                mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`, `main_id`, `is_recurrence`) VALUES ('$new_ticketid', '".$ticket_attached['id']."', 1)");
            }

            //Insert all ticket_schedule records with the new ticketid
            foreach($ticket_schedules as $ticket_schedule) {
                mysqli_query($dbc, "INSERT INTO `ticket_schedule` (`ticketid`, `main_id`, `is_recurrence`) VALUES ('$new_ticketid', '".$ticket_schedule['id']."', 1)");
            }

            //Insert all ticket_comment records with the new ticketid
            foreach($ticket_comments as $ticket_comment) {
                mysqli_query($dbc, "INSERT INTO `ticket_comment` (`ticketid`, `main_id`, `is_reccurence`) VALUES ('$new_ticketid', '".$ticket_comment['ticketcommid']."', 1)");
            }
        } else if(empty($date_exists['to_do_end_date'])) {
            mysqli_query($dbc, "UPDATE `tickets` SET `to_do_end_date` = '".$date_exists['to_do_date']."' WHERE `ticketid` = '".$date_exists['ticketid']."'");
        }
        //Set last added date to the latest added date
        mysqli_query($dbc, "UPDATE `ticket_recurrences` SET `last_added_date` = '$recurring_date' WHERE `ticketid` = '$ticketid'");
    }
}
function sync_recurring_tickets($dbc, $ticketid) {
    $ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
    if($ticket['main_ticketid'] > 0 && $ticket['is_recurrence'] == 1) {
        //Set main_id and is_recurrence for any new records
        mysqli_query($dbc, "UPDATE `ticket_attached` SET `main_id` = `id`, `is_recurrence` = 1 WHERE `ticketid` = '$ticketid' AND `deleted` = 0 AND `main_id` = 0");
        mysqli_query($dbc, "UPDATE `ticket_schedule` SET `main_id` = `id`, `is_recurrence` = 1 WHERE `ticketid` = '$ticketid' AND `deleted` = 0 AND `main_id` = 0");
        mysqli_query($dbc, "UPDATE `ticket_comment` SET `main_id` = `ticketcommid`, `is_recurrence` = 1 WHERE `ticketid` = '$ticketid' AND `deleted` = 0 AND `main_id` = 0");

        //Get all ticket rows from ticket_attached, ticket_schedule, and ticket_comment
        $ticket_attacheds = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '$ticketid' AND `main_id` > 0 AND `is_recurrence` = 1"),MYSQLI_ASSOC);
        $ticket_schedules = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid` = '$ticketid' AND `main_id` > 0 AND `is_recurrence` = 1"),MYSQLI_ASSOC);
        $ticket_comments = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_comment` WHERE `ticketid` = '$ticketid' AND `main_id` > 0 AND `is_recurrence` = 1"),MYSQLI_ASSOC);

        //Get all fields from tickets table except ticketid, to_do_date, and to_do_end_date, and then create the query for it
        $ticket_columns = mysqli_fetch_all(mysqli_query($dbc, "SHOW COLUMNS FROM `tickets` WHERE `Field` NOT IN ('ticketid','to_do_date','to_do_end_date','ticket_label','ticket_label_date')"),MYSQLI_ASSOC);
        $ticket_query = [];
        foreach($ticket_columns as $ticket_column) {
            $ticket_query[] = "`".$ticket_column['Field']."` = '".$ticket[$ticket_column['Field']]."'";
        }
        $ticket_query = implode(', ', $ticket_query);

        //Get all fields from ticket_attached table except id, ticketid and then create the queries for it
        $ticket_attached_columns = mysqli_fetch_all(mysqli_query($dbc, "SHOW COLUMNS FROM `ticket_attached` WHERE `Field` NOT IN ('id','ticketid')"),MYSQLI_ASSOC);
        $ticket_attached_queries = [];
        foreach($ticket_attacheds as $ticket_attached) {
            $ticket_attached_query = [];
            foreach($ticket_attached_columns as $ticket_column) {
                $ticket_attached_query[] = "`".$ticket_column['Field']."` = '".$ticket_attached[$ticket_column['Field']]."'";
            }
            $ticket_attached_queries[$ticket_attached['main_id']] = implode(', ', $ticket_attached_query);
        }

        //Get all fields from ticket_schedule table except id, ticketid and then create the queries for it
        $ticket_schedule_columns = mysqli_fetch_all(mysqli_query($dbc, "SHOW COLUMNS FROM `ticket_schedule` WHERE `Field` NOT IN ('id','ticketid')"),MYSQLI_ASSOC);
        $ticket_schedule_queries = [];
        foreach($ticket_schedules as $ticket_schedule) {
            $ticket_schedule_query = [];
            foreach($ticket_schedule_columns as $ticket_column) {
                $ticket_schedule_query[] = "`".$ticket_column['Field']."` = '".$ticket_schedule[$ticket_column['Field']]."'";
            }
            $ticket_schedule_queries[$ticket_schedule['main_id']] = implode(', ', $ticket_schedule_query);
        }

        //Get all fields from ticket_comment table except ticketcommid, ticketid and then create the queries for it
        $ticket_comment_columns = mysqli_fetch_all(mysqli_query($dbc, "SHOW COLUMNS FROM `ticket_comment` WHERE `Field` NOT IN ('ticketcommid','ticketid')"),MYSQLI_ASSOC);
        $ticket_comment_queries = [];
        foreach($ticket_comments as $ticket_comment) {
            $ticket_comment_query = [];
            foreach($ticket_comment_columns as $ticket_column) {
                $ticket_comment_query[] = "`".$ticket_column['Field']."` = '".$ticket_comment[$ticket_column['Field']]."'";
            }
            $ticket_comment_queries[$ticket_comment['main_id']] = implode(', ', $ticket_comment_query);
        }

        //Update all rows with recurring date
        $recurring_tickets = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `main_ticketid` = '".$ticket['main_ticketid']."' AND `is_recurrence` = 1 AND `deleted` = 0"),MYSQLI_ASSOC);
        foreach($recurring_tickets as $recurring_ticket) {
            mysqli_query($dbc, "UPDATE `tickets` SET $ticket_query WHERE `ticketid` = '".$recurring_ticket['ticketid']."'");
            mysqli_query($dbc, "UPDATE `tickets` SET `ticket_label_date` = '' WHERE `ticketid` = '".$recurring_ticket['ticketid']."'");

            //Insert all ticket_attached records with the new ticketid
            foreach($ticket_attached_queries as $id => $ticket_attached_query) {
                $existing = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `ticketid` = '".$recurring_ticket['ticketid']."' AND `main_id` = '".$id."'"));
                if(empty($existing)) {
                    mysqli_query($dbc, "INSERT INTO `ticket_attached` (`ticketid`) VALUES ('".$recurring_ticket['ticketid']."')");
                    $existing_id = mysqli_insert_id($dbc);
                } else {
                    $existing_id = $existing['id'];
                }
                mysqli_query($dbc, "UPDATE `ticket_attached` SET $ticket_attached_query WHERE `id` = '$existing_id'");
            }

            //Insert all ticket_schedule records with the new ticketid
            foreach($ticket_schedule_queries as $id => $ticket_schedule_query) {
                $existing = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_schedule` WHERE `ticketid` = '".$recurring_ticket['ticketid']."' AND `main_id` = '".$id."'"));
                if(empty($existing)) {
                    mysqli_query($dbc, "INSERT INTO `ticket_schedule` (`ticketid`) VALUES ('".$recurring_ticket['ticketid']."')");
                    $existing_id = mysqli_insert_id($dbc);
                } else {
                    $existing_id = $existing['id'];
                }
                mysqli_query($dbc, "UPDATE `ticket_schedule` SET $ticket_schedule_query WHERE `id` = '$existing_id'");
            }

            //Insert all ticket_comment records with the new ticketid
            foreach($ticket_comment_queries as $id => $ticket_comment_query) {
                $existing = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_comment` WHERE `ticketid` = '".$recurring_ticket['ticketid']."' AND `main_id` = '".$id."'"));
                if(empty($existing)) {
                    mysqli_query($dbc, "INSERT INTO `ticket_comment` (`ticketid`) VALUES ('".$recurring_ticket['ticketid']."')");
                    $existing_id = mysqli_insert_id($dbc);
                } else {
                    $existing_id = $existing['ticketcommid'];
                }
                mysqli_query($dbc, "UPDATE `ticket_comment` SET $ticket_comment_query WHERE `ticketcommid` = '$existing_id'");
            }
        }
    }
}
function get_teams($dbc, $limits = '') {
    $today_date = date('Y-m-d');
    $teams = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `deleted` = 0 AND (IF(`start_date` = `end_date` AND `start_date` != '0000-00-00',1,0) = 0 OR (DATE(`start_date`) <= '$today_date' AND (`start_date` != `end_date` OR `start_date` = '0000-00-00')))".$limits),MYSQLI_ASSOC);
    return $teams;
}
function get_team_contactids($dbc, $teamid) {
    $team_contactids = array_filter(array_unique(array_column(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC),'contactid')));
    return $team_contactids;
}
function get_team_name($dbc, $teamid, $separator = ", ", $get_contacts = 0) {
    $team_name = '';
    $team = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `teams` WHERE `teamid` = '$teamid'"));
    if(!empty($team['team_name']) && $get_contacts == 0) {
        $team_name = $team['team_name'];
    } else {
        $contact_list = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `teams_staff` WHERE `teamid` = '$teamid' AND `deleted` = 0"),MYSQLI_ASSOC);
        $team_staff = [];
        foreach ($contact_list as $contact) {
            $team_staff[] = get_contact($dbc, $contact['contactid']);
        }
        $team_name = implode($separator, $team_staff);
    }

    return $team_name;
}
function get_contact_teams($dbc, $contactid) {
    return $teams = mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `teamid` SEPARATOR ',') as teams_list FROM `teams_staff` WHERE `contactid` = '$contactid' AND `deleted` = 0"))['teams_list'];
}

function add_update_history($dbc, $table, $history, $contactid, $before_change, $tableid = '') {
		$user_name = get_contact($dbc, $_SESSION['contactid']);
		$history = filter_var(htmlentities($history),FILTER_SANITIZE_STRING);
		if($table == 'sales_history') {
			mysqli_query($dbc, "INSERT INTO `$table` (`updated_by`) SELECT '$user_name' FROM (SELECT COUNT(*) rows FROM `$table` WHERE `updated_by`='$user_name' AND TIMEDIFF(CURRENT_TIMESTAMP,`updated_at`) < '00:30:00') num WHERE num.rows = 0");
			mysqli_query($dbc, "UPDATE `$table` SET `salesid`=$tableid, `before_change`=CONCAT(IFNULL(`before_change`,''),'$before_change'), `history`=CONCAT(IFNULL(`history`,''),'$history'), `updated_at` = now() WHERE `updated_by`='$user_name' AND TIMEDIFF(CURRENT_TIMESTAMP,`updated_at`) < '00:15:00'");
		}
		else if($table == 'project_history') {
			mysqli_query($dbc, "INSERT INTO `$table` (`updated_at`,`updated_by`,`projectid`) SELECT NOW(), '$user_name', '$tableid' FROM (SELECT COUNT(*) rows FROM `$table` WHERE `updated_by`='$user_name' AND TIMEDIFF(CURRENT_TIMESTAMP,`updated_at`) < '00:15:00' AND `projectid`='$tableid') num WHERE num.rows = 0");
			mysqli_query($dbc, "UPDATE `$table` SET `description`=CONCAT(IFNULL(CONCAT(`description`,'&lt;br&gt;\n'),''),'$history'), `updated_at` = NOW() WHERE `updated_by`='$user_name' AND `projectid`='$tableid' AND TIMEDIFF(CURRENT_TIMESTAMP,`updated_at`) < '00:15:00'");
		}
		else {
			mysqli_query($dbc, "INSERT INTO `$table` (`updated_by`, `contactid`) SELECT '$user_name', '$contactid' FROM (SELECT COUNT(*) rows FROM `$table` WHERE `updated_by`='$user_name' AND `contactid`='$contactid' AND TIMEDIFF(CURRENT_TIMESTAMP,`updated_at`) < '00:30:00') num WHERE num.rows = 0");
			mysqli_query($dbc, "UPDATE `$table` SET `before_change`=CONCAT(IFNULL(`before_change`,''),'$before_change'), `description`=CONCAT(IFNULL(`description`,''),'$history'), `updated_at` = now() WHERE `updated_by`='$user_name' AND `contactid`='$contactid' AND TIMEDIFF(CURRENT_TIMESTAMP,`updated_at`) < '00:15:00'");
		}
}
function capture_before_change($dbc, $table, $find, $where, $wherevalue, $where2 = '', $wherevalue2 = '', $where3 = '', $wherevalue3 = '') {
	if($where2 != '' && $where3 != '') {
			$before_change_query = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT $find FROM `$table` WHERE `$where` = '$wherevalue' and `$where2` = '$wherevalue2' and `$where3` = '$wherevalue3'"));
	}
	else if($where2 != '') {
			$before_change_query = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT $find FROM `$table` WHERE `$where` = '$wherevalue' and `$where2` = '$wherevalue2'"));
	}
	else {
			$before_change_query = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT $find FROM `$table` WHERE `$where` = '$wherevalue'"));
	}

	$find_index = "'".$find."'";
	return "$find value was " . $before_change_query[$find] . ".<br />";
}
function capture_after_change($find, $value) {
	return "$find is set to " . $value . ".<br />";
}
function time_time2string($time) {
    $hours = !empty(floor($time)) ? floor($time).' Hr' : '';
    $minutes = !empty($time - floor($time)) ? (($time - floor($time)) * 60).' Min' : '';
    return implode(' ', [$hours,$minutes]);
}
function get_staff_schedule_lock_date($dbc) {
    $override_security = array_filter(explode(',',get_config($dbc, 'staff_schedule_autolock_override_security')));
    $overrided = false;
    foreach(array_filter(explode(',',$_SESSION['role'])) as $role) {
        if(in_array($role, $override_security)) {
            $overrided = true;
        }
    }

    if($overrided) {
        return '';
    } else {
        return get_config($dbc, 'staff_schedule_lock_date');
    }
}
function get_offset_from_zone($zone) {
    switch($zone) {
        case 'Africa/Abidjan': return date('I') ? 0 : 0; break;
        case 'Africa/Accra': return date('I') ? 0 : 0; break;
        case 'Africa/Addis_Ababa': return date('I') ? 10800 : 10800; break;
        case 'Africa/Algiers': return date('I') ? 3600 : 3600; break;
        case 'Africa/Asmara': return date('I') ? 10800 : 10800; break;
        case 'Africa/Bamako': return date('I') ? 0 : 0; break;
        case 'Africa/Bangui': return date('I') ? 3600 : 3600; break;
        case 'Africa/Banjul': return date('I') ? 0 : 0; break;
        case 'Africa/Bissau': return date('I') ? 0 : 0; break;
        case 'Africa/Blantyre': return date('I') ? 7200 : 7200; break;
        case 'Africa/Brazzaville': return date('I') ? 3600 : 3600; break;
        case 'Africa/Bujumbura': return date('I') ? 7200 : 7200; break;
        case 'Africa/Cairo': return date('I') ? 7200 : 7200; break;
        case 'Africa/Casablanca': return date('I') ? 0 : 3600; break;
        case 'Africa/Ceuta': return date('I') ? 3600 : 7200; break;
        case 'Africa/Conakry': return date('I') ? 0 : 0; break;
        case 'Africa/Dakar': return date('I') ? 0 : 0; break;
        case 'Africa/Dar_es_Salaam': return date('I') ? 10800 : 10800; break;
        case 'Africa/Djibouti': return date('I') ? 10800 : 10800; break;
        case 'Africa/Douala': return date('I') ? 3600 : 3600; break;
        case 'Africa/El_Aaiun': return date('I') ? 0 : 3600; break;
        case 'Africa/Freetown': return date('I') ? 0 : 0; break;
        case 'Africa/Gaborone': return date('I') ? 7200 : 7200; break;
        case 'Africa/Harare': return date('I') ? 7200 : 7200; break;
        case 'Africa/Johannesburg': return date('I') ? 7200 : 7200; break;
        case 'Africa/Juba': return date('I') ? 10800 : 10800; break;
        case 'Africa/Kampala': return date('I') ? 10800 : 10800; break;
        case 'Africa/Khartoum': return date('I') ? 7200 : 7200; break;
        case 'Africa/Kigali': return date('I') ? 7200 : 7200; break;
        case 'Africa/Kinshasa': return date('I') ? 3600 : 3600; break;
        case 'Africa/Lagos': return date('I') ? 3600 : 3600; break;
        case 'Africa/Libreville': return date('I') ? 3600 : 3600; break;
        case 'Africa/Lome': return date('I') ? 0 : 0; break;
        case 'Africa/Luanda': return date('I') ? 3600 : 3600; break;
        case 'Africa/Lubumbashi': return date('I') ? 7200 : 7200; break;
        case 'Africa/Lusaka': return date('I') ? 7200 : 7200; break;
        case 'Africa/Malabo': return date('I') ? 3600 : 3600; break;
        case 'Africa/Maputo': return date('I') ? 7200 : 7200; break;
        case 'Africa/Maseru': return date('I') ? 7200 : 7200; break;
        case 'Africa/Mbabane': return date('I') ? 7200 : 7200; break;
        case 'Africa/Mogadishu': return date('I') ? 10800 : 10800; break;
        case 'Africa/Monrovia': return date('I') ? 0 : 0; break;
        case 'Africa/Nairobi': return date('I') ? 10800 : 10800; break;
        case 'Africa/Ndjamena': return date('I') ? 3600 : 3600; break;
        case 'Africa/Niamey': return date('I') ? 3600 : 3600; break;
        case 'Africa/Nouakchott': return date('I') ? 0 : 0; break;
        case 'Africa/Ouagadougou': return date('I') ? 0 : 0; break;
        case 'Africa/Porto-Novo': return date('I') ? 3600 : 3600; break;
        case 'Africa/Sao_Tome': return date('I') ? 3600 : 3600; break;
        case 'Africa/Timbuktu': return date('I') ? 0 : 0; break;
        case 'Africa/Tripoli': return date('I') ? 7200 : 7200; break;
        case 'Africa/Tunis': return date('I') ? 3600 : 3600; break;
        case 'Africa/Windhoek': return date('I') ? 7200 : 7200; break;
        case 'America/Adak': return date('I') ? -36000 : -32400; break;
        case 'America/Anchorage': return date('I') ? -32400 : -28800; break;
        case 'America/Anguilla': return date('I') ? -14400 : -14400; break;
        case 'America/Antigua': return date('I') ? -14400 : -14400; break;
        case 'America/Araguaina': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/Buenos_Aires': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/Catamarca': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/ComodRivadavia': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/Cordoba': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/Jujuy': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/La_Rioja': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/Mendoza': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/Rio_Gallegos': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/Salta': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/San_Juan': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/San_Luis': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/Tucuman': return date('I') ? -10800 : -10800; break;
        case 'America/Argentina/Ushuaia': return date('I') ? -10800 : -10800; break;
        case 'America/Aruba': return date('I') ? -14400 : -14400; break;
        case 'America/Asuncion': return date('I') ? -14400 : -10800; break;
        case 'America/Atikokan': return date('I') ? -18000 : -18000; break;
        case 'America/Atka': return date('I') ? -36000 : -32400; break;
        case 'America/Bahia': return date('I') ? -10800 : -10800; break;
        case 'America/Bahia_Banderas': return date('I') ? -21600 : -18000; break;
        case 'America/Barbados': return date('I') ? -14400 : -14400; break;
        case 'America/Belem': return date('I') ? -10800 : -10800; break;
        case 'America/Belize': return date('I') ? -21600 : -21600; break;
        case 'America/Blanc-Sablon': return date('I') ? -14400 : -14400; break;
        case 'America/Boa_Vista': return date('I') ? -14400 : -14400; break;
        case 'America/Bogota': return date('I') ? -18000 : -18000; break;
        case 'America/Boise': return date('I') ? -25200 : -21600; break;
        case 'America/Buenos_Aires': return date('I') ? -10800 : -10800; break;
        case 'America/Cambridge_Bay': return date('I') ? -25200 : -21600; break;
        case 'America/Campo_Grande': return date('I') ? -14400 : -10800; break;
        case 'America/Cancun': return date('I') ? -18000 : -18000; break;
        case 'America/Caracas': return date('I') ? -14400 : -14400; break;
        case 'America/Catamarca': return date('I') ? -10800 : -10800; break;
        case 'America/Cayenne': return date('I') ? -10800 : -10800; break;
        case 'America/Cayman': return date('I') ? -18000 : -18000; break;
        case 'America/Chicago': return date('I') ? -21600 : -18000; break;
        case 'America/Chihuahua': return date('I') ? -25200 : -21600; break;
        case 'America/Coral_Harbour': return date('I') ? -18000 : -18000; break;
        case 'America/Cordoba': return date('I') ? -10800 : -10800; break;
        case 'America/Costa_Rica': return date('I') ? -21600 : -21600; break;
        case 'America/Creston': return date('I') ? -25200 : -25200; break;
        case 'America/Cuiaba': return date('I') ? -14400 : -10800; break;
        case 'America/Curacao': return date('I') ? -14400 : -14400; break;
        case 'America/Danmarkshavn': return date('I') ? 0 : 0; break;
        case 'America/Dawson': return date('I') ? -28800 : -25200; break;
        case 'America/Dawson_Creek': return date('I') ? -25200 : -25200; break;
        case 'America/Denver': return date('I') ? -25200 : -21600; break;
        case 'America/Detroit': return date('I') ? -18000 : -14400; break;
        case 'America/Dominica': return date('I') ? -14400 : -14400; break;
        case 'America/Edmonton': return date('I') ? -25200 : -21600; break;
        case 'America/Eirunepe': return date('I') ? -18000 : -18000; break;
        case 'America/El_Salvador': return date('I') ? -21600 : -21600; break;
        case 'America/Ensenada': return date('I') ? -28800 : -25200; break;
        case 'America/Fort_Nelson': return date('I') ? -25200 : -25200; break;
        case 'America/Fort_Wayne': return date('I') ? -18000 : -14400; break;
        case 'America/Fortaleza': return date('I') ? -10800 : -10800; break;
        case 'America/Glace_Bay': return date('I') ? -14400 : -10800; break;
        case 'America/Godthab': return date('I') ? -10800 : -7200; break;
        case 'America/Goose_Bay': return date('I') ? -14400 : -10800; break;
        case 'America/Grand_Turk': return date('I') ? -14400 : -14400; break;
        case 'America/Grenada': return date('I') ? -14400 : -14400; break;
        case 'America/Guadeloupe': return date('I') ? -14400 : -14400; break;
        case 'America/Guatemala': return date('I') ? -21600 : -21600; break;
        case 'America/Guayaquil': return date('I') ? -18000 : -18000; break;
        case 'America/Guyana': return date('I') ? -14400 : -14400; break;
        case 'America/Halifax': return date('I') ? -14400 : -10800; break;
        case 'America/Havana': return date('I') ? -18000 : -14400; break;
        case 'America/Hermosillo': return date('I') ? -25200 : -25200; break;
        case 'America/Indiana/Indianapolis': return date('I') ? -18000 : -14400; break;
        case 'America/Indiana/Knox': return date('I') ? -21600 : -18000; break;
        case 'America/Indiana/Marengo': return date('I') ? -18000 : -14400; break;
        case 'America/Indiana/Petersburg': return date('I') ? -18000 : -14400; break;
        case 'America/Indiana/Tell_City': return date('I') ? -21600 : -18000; break;
        case 'America/Indiana/Vevay': return date('I') ? -18000 : -14400; break;
        case 'America/Indiana/Vincennes': return date('I') ? -18000 : -14400; break;
        case 'America/Indiana/Winamac': return date('I') ? -18000 : -14400; break;
        case 'America/Indianapolis': return date('I') ? -18000 : -14400; break;
        case 'America/Inuvik': return date('I') ? -25200 : -21600; break;
        case 'America/Iqaluit': return date('I') ? -18000 : -14400; break;
        case 'America/Jamaica': return date('I') ? -18000 : -18000; break;
        case 'America/Jujuy': return date('I') ? -10800 : -10800; break;
        case 'America/Juneau': return date('I') ? -32400 : -28800; break;
        case 'America/Kentucky/Louisville': return date('I') ? -18000 : -14400; break;
        case 'America/Kentucky/Monticello': return date('I') ? -18000 : -14400; break;
        case 'America/Knox_IN': return date('I') ? -21600 : -18000; break;
        case 'America/Kralendijk': return date('I') ? -14400 : -14400; break;
        case 'America/La_Paz': return date('I') ? -14400 : -14400; break;
        case 'America/Lima': return date('I') ? -18000 : -18000; break;
        case 'America/Los_Angeles': return date('I') ? -28800 : -25200; break;
        case 'America/Louisville': return date('I') ? -18000 : -14400; break;
        case 'America/Lower_Princes': return date('I') ? -14400 : -14400; break;
        case 'America/Maceio': return date('I') ? -10800 : -10800; break;
        case 'America/Managua': return date('I') ? -21600 : -21600; break;
        case 'America/Manaus': return date('I') ? -14400 : -14400; break;
        case 'America/Marigot': return date('I') ? -14400 : -14400; break;
        case 'America/Martinique': return date('I') ? -14400 : -14400; break;
        case 'America/Matamoros': return date('I') ? -21600 : -18000; break;
        case 'America/Mazatlan': return date('I') ? -25200 : -21600; break;
        case 'America/Mendoza': return date('I') ? -10800 : -10800; break;
        case 'America/Menominee': return date('I') ? -21600 : -18000; break;
        case 'America/Merida': return date('I') ? -21600 : -18000; break;
        case 'America/Metlakatla': return date('I') ? -32400 : -28800; break;
        case 'America/Mexico_City': return date('I') ? -21600 : -18000; break;
        case 'America/Miquelon': return date('I') ? -10800 : -7200; break;
        case 'America/Moncton': return date('I') ? -14400 : -10800; break;
        case 'America/Monterrey': return date('I') ? -21600 : -18000; break;
        case 'America/Montevideo': return date('I') ? -10800 : -10800; break;
        case 'America/Montreal': return date('I') ? -18000 : -14400; break;
        case 'America/Montserrat': return date('I') ? -14400 : -14400; break;
        case 'America/Nassau': return date('I') ? -18000 : -14400; break;
        case 'America/New_York': return date('I') ? -18000 : -14400; break;
        case 'America/Nipigon': return date('I') ? -18000 : -14400; break;
        case 'America/Nome': return date('I') ? -32400 : -28800; break;
        case 'America/Noronha': return date('I') ? -7200 : -7200; break;
        case 'America/North_Dakota/Beulah': return date('I') ? -21600 : -18000; break;
        case 'America/North_Dakota/Center': return date('I') ? -21600 : -18000; break;
        case 'America/North_Dakota/New_Salem': return date('I') ? -21600 : -18000; break;
        case 'America/Ojinaga': return date('I') ? -25200 : -21600; break;
        case 'America/Panama': return date('I') ? -18000 : -18000; break;
        case 'America/Pangnirtung': return date('I') ? -18000 : -14400; break;
        case 'America/Paramaribo': return date('I') ? -10800 : -10800; break;
        case 'America/Phoenix': return date('I') ? -25200 : -25200; break;
        case 'America/Port_of_Spain': return date('I') ? -14400 : -14400; break;
        case 'America/Port-au-Prince': return date('I') ? -18000 : -14400; break;
        case 'America/Porto_Acre': return date('I') ? -18000 : -18000; break;
        case 'America/Porto_Velho': return date('I') ? -14400 : -14400; break;
        case 'America/Puerto_Rico': return date('I') ? -14400 : -14400; break;
        case 'America/Punta_Arenas': return date('I') ? -10800 : -10800; break;
        case 'America/Rainy_River': return date('I') ? -21600 : -18000; break;
        case 'America/Rankin_Inlet': return date('I') ? -21600 : -18000; break;
        case 'America/Recife': return date('I') ? -10800 : -10800; break;
        case 'America/Regina': return date('I') ? -21600 : -21600; break;
        case 'America/Resolute': return date('I') ? -21600 : -18000; break;
        case 'America/Rio_Branco': return date('I') ? -18000 : -18000; break;
        case 'America/Rosario': return date('I') ? -10800 : -10800; break;
        case 'America/Santa_Isabel': return date('I') ? -28800 : -25200; break;
        case 'America/Santarem': return date('I') ? -10800 : -10800; break;
        case 'America/Santiago': return date('I') ? -14400 : -10800; break;
        case 'America/Santo_Domingo': return date('I') ? -14400 : -14400; break;
        case 'America/Sao_Paulo': return date('I') ? -10800 : -7200; break;
        case 'America/Scoresbysund': return date('I') ? -3600 : 0; break;
        case 'America/Shiprock': return date('I') ? -25200 : -21600; break;
        case 'America/Sitka': return date('I') ? -32400 : -28800; break;
        case 'America/St_Barthelemy': return date('I') ? -14400 : -14400; break;
        case 'America/St_Johns': return date('I') ? -12600 : -9000; break;
        case 'America/St_Kitts': return date('I') ? -14400 : -14400; break;
        case 'America/St_Lucia': return date('I') ? -14400 : -14400; break;
        case 'America/St_Thomas': return date('I') ? -14400 : -14400; break;
        case 'America/St_Vincent': return date('I') ? -14400 : -14400; break;
        case 'America/Swift_Current': return date('I') ? -21600 : -21600; break;
        case 'America/Tegucigalpa': return date('I') ? -21600 : -21600; break;
        case 'America/Thule': return date('I') ? -14400 : -10800; break;
        case 'America/Thunder_Bay': return date('I') ? -18000 : -14400; break;
        case 'America/Tijuana': return date('I') ? -28800 : -25200; break;
        case 'America/Toronto': return date('I') ? -18000 : -14400; break;
        case 'America/Tortola': return date('I') ? -14400 : -14400; break;
        case 'America/Vancouver': return date('I') ? -28800 : -25200; break;
        case 'America/Virgin': return date('I') ? -14400 : -14400; break;
        case 'America/Whitehorse': return date('I') ? -28800 : -25200; break;
        case 'America/Winnipeg': return date('I') ? -21600 : -18000; break;
        case 'America/Yakutat': return date('I') ? -32400 : -28800; break;
        case 'America/Yellowknife': return date('I') ? -25200 : -21600; break;
        case 'Antarctica/Casey': return date('I') ? 39600 : 39600; break;
        case 'Antarctica/Davis': return date('I') ? 25200 : 25200; break;
        case 'Antarctica/DumontDUrville': return date('I') ? 36000 : 36000; break;
        case 'Antarctica/Macquarie': return date('I') ? 39600 : 39600; break;
        case 'Antarctica/Mawson': return date('I') ? 18000 : 18000; break;
        case 'Antarctica/McMurdo': return date('I') ? 43200 : 46800; break;
        case 'Antarctica/Palmer': return date('I') ? -10800 : -10800; break;
        case 'Antarctica/Rothera': return date('I') ? -10800 : -10800; break;
        case 'Antarctica/South_Pole': return date('I') ? 43200 : 46800; break;
        case 'Antarctica/Syowa': return date('I') ? 10800 : 10800; break;
        case 'Antarctica/Troll': return date('I') ? 0 : 7200; break;
        case 'Antarctica/Vostok': return date('I') ? 21600 : 21600; break;
        case 'Arctic/Longyearbyen': return date('I') ? 3600 : 7200; break;
        case 'Asia/Aden': return date('I') ? 10800 : 10800; break;
        case 'Asia/Almaty': return date('I') ? 21600 : 21600; break;
        case 'Asia/Amman': return date('I') ? 7200 : 10800; break;
        case 'Asia/Anadyr': return date('I') ? 43200 : 43200; break;
        case 'Asia/Aqtau': return date('I') ? 18000 : 18000; break;
        case 'Asia/Aqtobe': return date('I') ? 18000 : 18000; break;
        case 'Asia/Ashgabat': return date('I') ? 18000 : 18000; break;
        case 'Asia/Ashkhabad': return date('I') ? 18000 : 18000; break;
        case 'Asia/Atyrau': return date('I') ? 18000 : 18000; break;
        case 'Asia/Baghdad': return date('I') ? 10800 : 10800; break;
        case 'Asia/Bahrain': return date('I') ? 10800 : 10800; break;
        case 'Asia/Baku': return date('I') ? 14400 : 14400; break;
        case 'Asia/Bangkok': return date('I') ? 25200 : 25200; break;
        case 'Asia/Barnaul': return date('I') ? 25200 : 25200; break;
        case 'Asia/Beirut': return date('I') ? 7200 : 10800; break;
        case 'Asia/Bishkek': return date('I') ? 21600 : 21600; break;
        case 'Asia/Brunei': return date('I') ? 28800 : 28800; break;
        case 'Asia/Calcutta': return date('I') ? 19800 : 19800; break;
        case 'Asia/Chita': return date('I') ? 32400 : 32400; break;
        case 'Asia/Choibalsan': return date('I') ? 28800 : 28800; break;
        case 'Asia/Chongqing': return date('I') ? 28800 : 28800; break;
        case 'Asia/Chungking': return date('I') ? 28800 : 28800; break;
        case 'Asia/Colombo': return date('I') ? 19800 : 19800; break;
        case 'Asia/Dacca': return date('I') ? 21600 : 21600; break;
        case 'Asia/Damascus': return date('I') ? 7200 : 10800; break;
        case 'Asia/Dhaka': return date('I') ? 21600 : 21600; break;
        case 'Asia/Dili': return date('I') ? 32400 : 32400; break;
        case 'Asia/Dubai': return date('I') ? 14400 : 14400; break;
        case 'Asia/Dushanbe': return date('I') ? 18000 : 18000; break;
        case 'Asia/Famagusta': return date('I') ? 7200 : 7200; break;
        case 'Asia/Gaza': return date('I') ? 7200 : 10800; break;
        case 'Asia/Harbin': return date('I') ? 28800 : 28800; break;
        case 'Asia/Hebron': return date('I') ? 7200 : 10800; break;
        case 'Asia/Ho_Chi_Minh': return date('I') ? 25200 : 25200; break;
        case 'Asia/Hong_Kong': return date('I') ? 28800 : 28800; break;
        case 'Asia/Hovd': return date('I') ? 25200 : 25200; break;
        case 'Asia/Irkutsk': return date('I') ? 28800 : 28800; break;
        case 'Asia/Istanbul': return date('I') ? 10800 : 10800; break;
        case 'Asia/Jakarta': return date('I') ? 25200 : 25200; break;
        case 'Asia/Jayapura': return date('I') ? 32400 : 32400; break;
        case 'Asia/Jerusalem': return date('I') ? 7200 : 10800; break;
        case 'Asia/Kabul': return date('I') ? 16200 : 16200; break;
        case 'Asia/Kamchatka': return date('I') ? 43200 : 43200; break;
        case 'Asia/Karachi': return date('I') ? 18000 : 18000; break;
        case 'Asia/Kashgar': return date('I') ? 21600 : 21600; break;
        case 'Asia/Kathmandu': return date('I') ? 20700 : 20700; break;
        case 'Asia/Katmandu': return date('I') ? 20700 : 20700; break;
        case 'Asia/Khandyga': return date('I') ? 32400 : 32400; break;
        case 'Asia/Kolkata': return date('I') ? 19800 : 19800; break;
        case 'Asia/Krasnoyarsk': return date('I') ? 25200 : 25200; break;
        case 'Asia/Kuala_Lumpur': return date('I') ? 28800 : 28800; break;
        case 'Asia/Kuching': return date('I') ? 28800 : 28800; break;
        case 'Asia/Kuwait': return date('I') ? 10800 : 10800; break;
        case 'Asia/Macao': return date('I') ? 28800 : 28800; break;
        case 'Asia/Macau': return date('I') ? 28800 : 28800; break;
        case 'Asia/Magadan': return date('I') ? 39600 : 39600; break;
        case 'Asia/Makassar': return date('I') ? 28800 : 28800; break;
        case 'Asia/Manila': return date('I') ? 28800 : 28800; break;
        case 'Asia/Muscat': return date('I') ? 14400 : 14400; break;
        case 'Asia/Novokuznetsk': return date('I') ? 25200 : 25200; break;
        case 'Asia/Novosibirsk': return date('I') ? 25200 : 25200; break;
        case 'Asia/Omsk': return date('I') ? 21600 : 21600; break;
        case 'Asia/Oral': return date('I') ? 18000 : 18000; break;
        case 'Asia/Phnom_Penh': return date('I') ? 25200 : 25200; break;
        case 'Asia/Pontianak': return date('I') ? 25200 : 25200; break;
        case 'Asia/Pyongyang': return date('I') ? 32400 : 32400; break;
        case 'Asia/Qatar': return date('I') ? 10800 : 10800; break;
        case 'Asia/Qyzylorda': return date('I') ? 21600 : 21600; break;
        case 'Asia/Rangoon': return date('I') ? 23400 : 23400; break;
        case 'Asia/Riyadh': return date('I') ? 10800 : 10800; break;
        case 'Asia/Saigon': return date('I') ? 25200 : 25200; break;
        case 'Asia/Sakhalin': return date('I') ? 39600 : 39600; break;
        case 'Asia/Samarkand': return date('I') ? 18000 : 18000; break;
        case 'Asia/Seoul': return date('I') ? 32400 : 32400; break;
        case 'Asia/Shanghai': return date('I') ? 28800 : 28800; break;
        case 'Asia/Singapore': return date('I') ? 28800 : 28800; break;
        case 'Asia/Srednekolymsk': return date('I') ? 39600 : 39600; break;
        case 'Asia/Taipei': return date('I') ? 28800 : 28800; break;
        case 'Asia/Tashkent': return date('I') ? 18000 : 18000; break;
        case 'Asia/Tbilisi': return date('I') ? 14400 : 14400; break;
        case 'Asia/Tehran': return date('I') ? 12600 : 16200; break;
        case 'Asia/Tel_Aviv': return date('I') ? 7200 : 10800; break;
        case 'Asia/Thimbu': return date('I') ? 21600 : 21600; break;
        case 'Asia/Thimphu': return date('I') ? 21600 : 21600; break;
        case 'Asia/Tokyo': return date('I') ? 32400 : 32400; break;
        case 'Asia/Tomsk': return date('I') ? 25200 : 25200; break;
        case 'Asia/Ujung_Pandang': return date('I') ? 28800 : 28800; break;
        case 'Asia/Ulaanbaatar': return date('I') ? 28800 : 28800; break;
        case 'Asia/Ulan_Bator': return date('I') ? 28800 : 28800; break;
        case 'Asia/Urumqi': return date('I') ? 21600 : 21600; break;
        case 'Asia/Ust-Nera': return date('I') ? 36000 : 36000; break;
        case 'Asia/Vientiane': return date('I') ? 25200 : 25200; break;
        case 'Asia/Vladivostok': return date('I') ? 36000 : 36000; break;
        case 'Asia/Yakutsk': return date('I') ? 32400 : 32400; break;
        case 'Asia/Yangon': return date('I') ? 23400 : 23400; break;
        case 'Asia/Yekaterinburg': return date('I') ? 18000 : 18000; break;
        case 'Asia/Yerevan': return date('I') ? 14400 : 14400; break;
        case 'Atlantic/Azores': return date('I') ? -3600 : 0; break;
        case 'Atlantic/Bermuda': return date('I') ? -14400 : -10800; break;
        case 'Atlantic/Canary': return date('I') ? 0 : 3600; break;
        case 'Atlantic/Cape_Verde': return date('I') ? -3600 : -3600; break;
        case 'Atlantic/Faeroe': return date('I') ? 0 : 3600; break;
        case 'Atlantic/Faroe': return date('I') ? 0 : 3600; break;
        case 'Atlantic/Jan_Mayen': return date('I') ? 3600 : 7200; break;
        case 'Atlantic/Madeira': return date('I') ? 0 : 3600; break;
        case 'Atlantic/Reykjavik': return date('I') ? 0 : 0; break;
        case 'Atlantic/South_Georgia': return date('I') ? -7200 : -7200; break;
        case 'Atlantic/St_Helena': return date('I') ? 0 : 0; break;
        case 'Atlantic/Stanley': return date('I') ? -10800 : -10800; break;
        case 'Australia/Adelaide': return date('I') ? 34200 : 37800; break;
        case 'Australia/Brisbane': return date('I') ? 36000 : 36000; break;
        case 'Australia/Broken_Hill': return date('I') ? 34200 : 37800; break;
        case 'Australia/Canberra': return date('I') ? 36000 : 39600; break;
        case 'Australia/Currie': return date('I') ? 36000 : 39600; break;
        case 'Australia/Darwin': return date('I') ? 34200 : 34200; break;
        case 'Australia/Eucla': return date('I') ? 31500 : 31500; break;
        case 'Australia/Hobart': return date('I') ? 36000 : 39600; break;
        case 'Australia/Lindeman': return date('I') ? 36000 : 36000; break;
        case 'Australia/Lord_Howe': return date('I') ? 37800 : 39600; break;
        case 'Australia/Melbourne': return date('I') ? 36000 : 39600; break;
        case 'Australia/Perth': return date('I') ? 28800 : 28800; break;
        case 'Australia/Sydney': return date('I') ? 36000 : 39600; break;
        case 'Australia/Yancowinna': return date('I') ? 34200 : 37800; break;
        case 'Etc/GMT': return date('I') ? 0 : 0; break;
        case 'Etc/GMT+0': return date('I') ? 0 : 0; break;
        case 'Etc/GMT+1': return date('I') ? -3600 : -3600; break;
        case 'Etc/GMT+10': return date('I') ? -36000 : -36000; break;
        case 'Etc/GMT+11': return date('I') ? -39600 : -39600; break;
        case 'Etc/GMT+12': return date('I') ? -43200 : -43200; break;
        case 'Etc/GMT+2': return date('I') ? -7200 : -7200; break;
        case 'Etc/GMT+3': return date('I') ? -10800 : -10800; break;
        case 'Etc/GMT+4': return date('I') ? -14400 : -14400; break;
        case 'Etc/GMT+5': return date('I') ? -18000 : -18000; break;
        case 'Etc/GMT+6': return date('I') ? -21600 : -21600; break;
        case 'Etc/GMT+7': return date('I') ? -25200 : -25200; break;
        case 'Etc/GMT+8': return date('I') ? -28800 : -28800; break;
        case 'Etc/GMT+9': return date('I') ? -32400 : -32400; break;
        case 'Etc/GMT0': return date('I') ? 0 : 0; break;
        case 'Etc/GMT-0': return date('I') ? 0 : 0; break;
        case 'Etc/GMT-1': return date('I') ? 3600 : 3600; break;
        case 'Etc/GMT-10': return date('I') ? 36000 : 36000; break;
        case 'Etc/GMT-11': return date('I') ? 39600 : 39600; break;
        case 'Etc/GMT-12': return date('I') ? 43200 : 43200; break;
        case 'Etc/GMT-13': return date('I') ? 46800 : 46800; break;
        case 'Etc/GMT-14': return date('I') ? 50400 : 50400; break;
        case 'Etc/GMT-2': return date('I') ? 7200 : 7200; break;
        case 'Etc/GMT-3': return date('I') ? 10800 : 10800; break;
        case 'Etc/GMT-4': return date('I') ? 14400 : 14400; break;
        case 'Etc/GMT-5': return date('I') ? 18000 : 18000; break;
        case 'Etc/GMT-6': return date('I') ? 21600 : 21600; break;
        case 'Etc/GMT-7': return date('I') ? 25200 : 25200; break;
        case 'Etc/GMT-8': return date('I') ? 28800 : 28800; break;
        case 'Etc/GMT-9': return date('I') ? 32400 : 32400; break;
        case 'Etc/UTC': return date('I') ? 0 : 0; break;
        case 'Europe/Amsterdam': return date('I') ? 3600 : 7200; break;
        case 'Europe/Andorra': return date('I') ? 3600 : 7200; break;
        case 'Europe/Astrakhan': return date('I') ? 14400 : 14400; break;
        case 'Europe/Athens': return date('I') ? 7200 : 10800; break;
        case 'Europe/Belfast': return date('I') ? 0 : 3600; break;
        case 'Europe/Belgrade': return date('I') ? 3600 : 7200; break;
        case 'Europe/Berlin': return date('I') ? 3600 : 7200; break;
        case 'Europe/Bratislava': return date('I') ? 3600 : 7200; break;
        case 'Europe/Brussels': return date('I') ? 3600 : 7200; break;
        case 'Europe/Bucharest': return date('I') ? 7200 : 10800; break;
        case 'Europe/Budapest': return date('I') ? 3600 : 7200; break;
        case 'Europe/Busingen': return date('I') ? 3600 : 7200; break;
        case 'Europe/Chisinau': return date('I') ? 7200 : 10800; break;
        case 'Europe/Copenhagen': return date('I') ? 3600 : 7200; break;
        case 'Europe/Dublin': return date('I') ? 0 : 3600; break;
        case 'Europe/Gibraltar': return date('I') ? 3600 : 7200; break;
        case 'Europe/Guernsey': return date('I') ? 0 : 3600; break;
        case 'Europe/Helsinki': return date('I') ? 7200 : 10800; break;
        case 'Europe/Isle_of_Man': return date('I') ? 0 : 3600; break;
        case 'Europe/Istanbul': return date('I') ? 10800 : 10800; break;
        case 'Europe/Jersey': return date('I') ? 0 : 3600; break;
        case 'Europe/Kaliningrad': return date('I') ? 7200 : 7200; break;
        case 'Europe/Kiev': return date('I') ? 7200 : 10800; break;
        case 'Europe/Kirov': return date('I') ? 10800 : 10800; break;
        case 'Europe/Lisbon': return date('I') ? 0 : 3600; break;
        case 'Europe/Ljubljana': return date('I') ? 3600 : 7200; break;
        case 'Europe/London': return date('I') ? 0 : 3600; break;
        case 'Europe/Luxembourg': return date('I') ? 3600 : 7200; break;
        case 'Europe/Madrid': return date('I') ? 3600 : 7200; break;
        case 'Europe/Malta': return date('I') ? 3600 : 7200; break;
        case 'Europe/Mariehamn': return date('I') ? 7200 : 10800; break;
        case 'Europe/Minsk': return date('I') ? 10800 : 10800; break;
        case 'Europe/Monaco': return date('I') ? 3600 : 7200; break;
        case 'Europe/Moscow': return date('I') ? 10800 : 10800; break;
        case 'Europe/Nicosia': return date('I') ? 7200 : 10800; break;
        case 'Europe/Oslo': return date('I') ? 3600 : 7200; break;
        case 'Europe/Paris': return date('I') ? 3600 : 7200; break;
        case 'Europe/Podgorica': return date('I') ? 3600 : 7200; break;
        case 'Europe/Prague': return date('I') ? 3600 : 7200; break;
        case 'Europe/Riga': return date('I') ? 7200 : 10800; break;
        case 'Europe/Rome': return date('I') ? 3600 : 7200; break;
        case 'Europe/Samara': return date('I') ? 14400 : 14400; break;
        case 'Europe/San_Marino': return date('I') ? 3600 : 7200; break;
        case 'Europe/Sarajevo': return date('I') ? 3600 : 7200; break;
        case 'Europe/Saratov': return date('I') ? 14400 : 14400; break;
        case 'Europe/Simferopol': return date('I') ? 10800 : 10800; break;
        case 'Europe/Skopje': return date('I') ? 3600 : 7200; break;
        case 'Europe/Sofia': return date('I') ? 7200 : 10800; break;
        case 'Europe/Stockholm': return date('I') ? 3600 : 7200; break;
        case 'Europe/Tallinn': return date('I') ? 7200 : 10800; break;
        case 'Europe/Tirane': return date('I') ? 3600 : 7200; break;
        case 'Europe/Tiraspol': return date('I') ? 7200 : 10800; break;
        case 'Europe/Ulyanovsk': return date('I') ? 14400 : 14400; break;
        case 'Europe/Uzhgorod': return date('I') ? 7200 : 10800; break;
        case 'Europe/Vaduz': return date('I') ? 3600 : 7200; break;
        case 'Europe/Vatican': return date('I') ? 3600 : 7200; break;
        case 'Europe/Vienna': return date('I') ? 3600 : 7200; break;
        case 'Europe/Vilnius': return date('I') ? 7200 : 10800; break;
        case 'Europe/Volgograd': return date('I') ? 10800 : 10800; break;
        case 'Europe/Warsaw': return date('I') ? 3600 : 7200; break;
        case 'Europe/Zagreb': return date('I') ? 3600 : 7200; break;
        case 'Europe/Zaporozhye': return date('I') ? 7200 : 10800; break;
        case 'Europe/Zurich': return date('I') ? 3600 : 7200; break;
        case 'GMT': return date('I') ? 0 : 0; break;
        case 'Indian/Antananarivo': return date('I') ? 10800 : 10800; break;
        case 'Indian/Chagos': return date('I') ? 21600 : 21600; break;
        case 'Indian/Christmas': return date('I') ? 25200 : 25200; break;
        case 'Indian/Cocos': return date('I') ? 23400 : 23400; break;
        case 'Indian/Comoro': return date('I') ? 10800 : 10800; break;
        case 'Indian/Kerguelen': return date('I') ? 18000 : 18000; break;
        case 'Indian/Mahe': return date('I') ? 14400 : 14400; break;
        case 'Indian/Maldives': return date('I') ? 18000 : 18000; break;
        case 'Indian/Mauritius': return date('I') ? 14400 : 14400; break;
        case 'Indian/Mayotte': return date('I') ? 10800 : 10800; break;
        case 'Indian/Reunion': return date('I') ? 14400 : 14400; break;
        case 'Pacific/Apia': return date('I') ? 46800 : 50400; break;
        case 'Pacific/Auckland': return date('I') ? 43200 : 46800; break;
        case 'Pacific/Bougainville': return date('I') ? 39600 : 39600; break;
        case 'Pacific/Chatham': return date('I') ? 45900 : 49500; break;
        case 'Pacific/Chuuk': return date('I') ? 36000 : 36000; break;
        case 'Pacific/Easter': return date('I') ? -21600 : -18000; break;
        case 'Pacific/Efate': return date('I') ? 39600 : 39600; break;
        case 'Pacific/Enderbury': return date('I') ? 46800 : 46800; break;
        case 'Pacific/Fakaofo': return date('I') ? 46800 : 46800; break;
        case 'Pacific/Fiji': return date('I') ? 43200 : 46800; break;
        case 'Pacific/Funafuti': return date('I') ? 43200 : 43200; break;
        case 'Pacific/Galapagos': return date('I') ? -21600 : -21600; break;
        case 'Pacific/Gambier': return date('I') ? -32400 : -32400; break;
        case 'Pacific/Guadalcanal': return date('I') ? 39600 : 39600; break;
        case 'Pacific/Guam': return date('I') ? 36000 : 36000; break;
        case 'Pacific/Honolulu': return date('I') ? -36000 : -36000; break;
        case 'Pacific/Johnston': return date('I') ? -36000 : -36000; break;
        case 'Pacific/Kiritimati': return date('I') ? 50400 : 50400; break;
        case 'Pacific/Kosrae': return date('I') ? 39600 : 39600; break;
        case 'Pacific/Kwajalein': return date('I') ? 43200 : 43200; break;
        case 'Pacific/Majuro': return date('I') ? 43200 : 43200; break;
        case 'Pacific/Marquesas': return date('I') ? -34200 : -34200; break;
        case 'Pacific/Midway': return date('I') ? -39600 : -39600; break;
        case 'Pacific/Nauru': return date('I') ? 43200 : 43200; break;
        case 'Pacific/Niue': return date('I') ? -39600 : -39600; break;
        case 'Pacific/Norfolk': return date('I') ? 39600 : 39600; break;
        case 'Pacific/Noumea': return date('I') ? 39600 : 39600; break;
        case 'Pacific/Pago_Pago': return date('I') ? -39600 : -39600; break;
        case 'Pacific/Palau': return date('I') ? 32400 : 32400; break;
        case 'Pacific/Pitcairn': return date('I') ? -28800 : -28800; break;
        case 'Pacific/Pohnpei': return date('I') ? 39600 : 39600; break;
        case 'Pacific/Ponape': return date('I') ? 39600 : 39600; break;
        case 'Pacific/Port_Moresby': return date('I') ? 36000 : 36000; break;
        case 'Pacific/Rarotonga': return date('I') ? -36000 : -36000; break;
        case 'Pacific/Saipan': return date('I') ? 36000 : 36000; break;
        case 'Pacific/Samoa': return date('I') ? -39600 : -39600; break;
        case 'Pacific/Tahiti': return date('I') ? -36000 : -36000; break;
        case 'Pacific/Tarawa': return date('I') ? 43200 : 43200; break;
        case 'Pacific/Tongatapu': return date('I') ? 46800 : 50400; break;
        case 'Pacific/Truk': return date('I') ? 36000 : 36000; break;
        case 'Pacific/Wake': return date('I') ? 43200 : 43200; break;
        case 'Pacific/Wallis': return date('I') ? 43200 : 43200; break;
        case 'Pacific/Yap': return date('I') ? 36000 : 36000; break;
        default: return date('I') ? -25200 : -21600; break;
    }
}