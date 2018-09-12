<?php // Search Results
$time = microtime(true);
include_once('include.php');
// Set the number and offset of results to display
$rows = 500;
$page = ($_GET['page'] > 0 ? $_GET['page'] : 1);
if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	// If it's an ajax call, only show 10 results and remove the header
	$rows = isset($_GET['rows']) ? $_GET['rows'] * 1 : 10;
	ob_clean();
} else {
	// If it's not ajax, include the navigation header
	include_once('navigation.php');
}
$offset = ($page * $rows) - $rows;
$i = 0;
$key = strtolower(filter_var($_GET['search_query'],FILTER_SANITIZE_STRING));

$matched_contacts = [];
$search_results = [];
echo "<!--Starting Search (at ".(microtime(true) - $time).")\n";

if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
    if(($_GET['category'] ?: 'tiles') == 'tiles') {
        echo "Loading Tile List (at ".(microtime(true) - $time).")\n";
        $no_display = true;
        include_once('tiles.php');
        $no_display = false;
        $tile_list = [];
        foreach($_SESSION['tile_list'] as $line) {
            if(substr(strtolower($line['key']),0,strlen($key)) == $key) {
                $tile_list[$line['label'].'<!--'.$line['link'].'-->'] = $line['link'];
            }
        }
        ksort($tile_list);
        foreach($tile_list as $label => $link) {
            $search_results[] = ['label'=>$label,'link'=>$link];
        }
    }
    if(($_GET['category'] ?: 'contacts') == 'contacts') {
        echo "Loading Contacts (at ".(microtime(true) - $time).")\n";
        $from_address = '&from='.(!empty($_GET['return']) ? urlencode($_GET['return']) : urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']));
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'contacts_inbox')) {
            $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name`='contacts' AND `category` != 'Staff'"));
            foreach($contact_list as $contact) {
                if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                    $search_results[] = ['label'=>CONTACTS_TILE.': ' . $contact['category'].': '.($contact['category'] == 'Business' ? $contact['name'] : $contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').($contact['display_name'] == '' ? $contact['site_name'] : $contact['display_name']).' '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : '')),'link'=>WEBSITE_URL.'/Contacts/contacts_inbox.php?edit='.$contact['contactid']];
                    $matched_contacts[] = $contact['contactid'];
                }
            }
        } else if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'contacts')) {
            $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name`='contacts' AND `category` != 'Staff'"));
            foreach($contact_list as $contact) {
                if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                    $search_results[] = ['label'=>CONTACTS_TILE.': ' . $contact['category'].': '.($contact['category'] == 'Business' ? $contact['name'] : $contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').($contact['display_name'] == '' ? $contact['site_name'] : $contact['display_name']).' '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : '')),'link'=>WEBSITE_URL.'/Contacts/add_contacts.php?contactid='.$contact['contactid']];
                    $matched_contacts[] = $contact['contactid'];
                }
            }
        }
    }
    if(($_GET['category'] ?: 'contacts3') == 'contacts3') {
        echo "Loading Contacts3 (at ".(microtime(true) - $time).")\n";
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'contacts3')) {
            $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name`='contacts3' AND `category` != 'Staff'"));
            foreach($contact_list as $contact) {
                if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                    $search_results[] = ['label'=>CONTACTS_TILE.': ' . $contact['category'].': '.($contact['category'] == 'Business' ? $contact['name'] : $contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').($contact['display_name'] == '' ? $contact['site_name'] : $contact['display_name']).' '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : '')),'link'=>WEBSITE_URL.'/Contacts3/contacts_inbox.php?edit='.$contact['contactid']];
                    $matched_contacts[] = $contact['contactid'];
                }
            }
        }
    }
    if(($_GET['category'] ?: 'contactsrolodex') == 'contactsrolodex') {
        echo "Loading Rolodex (at ".(microtime(true) - $time).")\n";
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'contacts_rolodex')) {
            $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name`='contactsrolodex' AND `category` != 'Staff'"));
            foreach($contact_list as $contact) {
                if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                    $search_results[] = ['label'=>CONTACTS_TILE.': ' . $contact['category'].': '.($contact['category'] == 'Business' ? $contact['name'] : $contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').($contact['display_name'] == '' ? $contact['site_name'] : $contact['display_name']).' '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : '')),'link'=>WEBSITE_URL.'/ContactsRolodex/contacts_inbox.php?edit='.$contact['contactid']];
                    $matched_contacts[] = $contact['contactid'];
                }
            }
        }
    }
    if(($_GET['category'] ?: 'members') == 'members') {
        echo "Loading Members (at ".(microtime(true) - $time).")\n";
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'members')) {
            $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name`='members' AND `category` != 'Staff'"));
            foreach($contact_list as $contact) {
                if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                    $search_results[] = ['label'=>'Members: ' . $contact['category'].': '.($contact['category'] == 'Business' ? $contact['name'] : $contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').($contact['display_name'] == '' ? $contact['site_name'] : $contact['display_name']).' '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : '')),'link'=>WEBSITE_URL.'/Members/contacts_inbox.php?edit='.$contact['contactid']];
                    $matched_contacts[] = $contact['contactid'];
                }
            }
        }
    }
    if(($_GET['category'] ?: 'clients') == 'clients') {
        echo "Loading Clients (at ".(microtime(true) - $time).")\n";
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'client_info')) {
            $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name`='clientinfo' AND `category` != 'Staff'"));
            foreach($contact_list as $contact) {
                if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                    $search_results[] = ['label'=>'Clients: ' . $contact['category'].': '.($contact['category'] == 'Business' ? $contact['name'] : $contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').($contact['display_name'] == '' ? $contact['site_name'] : $contact['display_name']).' '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : '')),'link'=>WEBSITE_URL.'/ClientInfo/contacts_inbox.php?edit='.$contact['contactid']];
                    $matched_contacts[] = $contact['contactid'];
                }
            }
        }
    }
    if(($_GET['category'] ?: 'staff') == 'staff') {
        echo "Loading Staff (at ".(microtime(true) - $time).")\n";
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'staff') && (vuaed_visible_function($dbc, 'staff') || check_subtab_persmission($dbc, 'staff', ROLE, 'id_card'))) {
            $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `category` = 'Staff'"));
            foreach($contact_list as $contact) {
                if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                    $search_results[] = ['label'=>'Staff: '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : ''),'link'=>WEBSITE_URL.'/Staff/staff_edit.php?contactid='.$contact['contactid']];
                    $matched_contacts[] = $contact['contactid'];
                }
            }
        }
    }
    if(($_GET['category'] ?: 'projects') == 'projects') {
        echo "Loading Projects (at ".(microtime(true) - $time).")\n";
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'project')) {
            $query_options = "`projectid` = '$key' OR `project_name` LIKE '$key%' OR `status` LIKE '$key%' OR `projecttype` LIKE '$key%'";
            foreach($matched_contacts as $contactid) {
                $query_options .= " OR `businessid`='$contactid' OR CONCAT(',',`clientid`,',') LIKE '%,$contactid,%' OR `project_lead`='$contactid'";
            }
            $query_match_program = '1=1';
            if(!empty(MATCH_CONTACTS)) {
                $query_match_program = '1=0';
                foreach(explode(',',MATCH_CONTACTS) as $contactid) {
                    $query_match_program .= " OR `businessid`='$contactid' OR CONCAT(',',`clientid`,',') LIKE '%,$contactid,%'";
                }
            }
            $project_list = mysqli_query($dbc, "SELECT `projectid`, `project_name`, `businessid`, `clientid`, `status`, `projecttype`, `project_lead` FROM `project` WHERE `deleted`=0 AND ($query_options) AND ($query_match_program)");
            $editable = vuaed_visible_function($dbc, 'project');
            while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $project = mysqli_fetch_assoc($project_list)) {
                $clients = [];
                foreach(explode(',',$project['clientid']) as $clientid) {
                    if($clientid > 0) {
                        $clients[] = get_contact($dbc, $clientid);
                    }
                }
                $project_label = get_project_label($dbc, $project);
                $search_results[] = ['label'=>'Project: ' . $project_label.' ('.($project['businessid'] > 0 ? get_client($dbc, $project['businessid']) : implode(', ',$clients)).')','link'=>$editable ? WEBSITE_URL.'/Project/projects.php?edit='.$project['projectid'] : ''];
            }
            if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $editable) {
                $checklists = mysqli_query($dbc, "SELECT * FROM `project_milestone_checklist` WHERE `checklist` LIKE '$key%' AND `deleted`=0");
                while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $checklist = mysqli_fetch_assoc($checklists)) {
                    $project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `projectid`, `projecttype`, `project_name`, `status`, `project_path`, `deleted`, `businessid`, `clientid` FROM `project` WHERE `projectid`='{$checklist['projectid']}'"));
                    if($project['deleted'] == 0 && in_array($checklist['milestone'],explode('#*#',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `milestone` FROM `project_path_milestone` WHERE `project_path_milestone`='{$project['project_path']}'"))['milestone']))) {
                        $search_results[] = ['label'=>'Project: Item #'.$checklist['checklistid'].' on '.get_project_label($dbc, $project),'link'=>WEBSITE_URL.'/Project/projects.php?edit='.$checklist['projectid'].'&tab=path_'.preg_replace('/[^a-z]*/','',strtolower($checklist['milestone']))];
                    }
                }
            }
        }
    }
    if(($_GET['category'] ?: 'tickets') == 'tickets') {
        echo "Loading Tickets (at ".(microtime(true) - $time).")\n";
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'ticket')) {
            foreach($matched_contacts as $contactid) {
                $query_options .= " OR `businessid`='$contactid' OR `siteid`='$contactid' OR CONCAT(',',`clientid`,',') LIKE '%,$contactid,%' OR CONCAT(',',`contactid`,',') LIKE '%,$contactid,%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,$contactid,%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,$contactid,%'";
            }
            $query_match_program = '1=1';
            if(!empty(MATCH_CONTACTS)) {
                $query_match_program = '1=0';
                foreach(explode(',',MATCH_CONTACTS) as $contactid) {
                    $query_match_program .= " OR `businessid`='$contactid' OR CONCAT(',',`clientid`,',') LIKE '%,$contactid,%'";
                }
            }
            echo "SELECT `ticketid`, `heading`, `businessid`, `clientid`, `contactid`, `ticket_type`, `to_do_date`, `created_date`, `status`, `projectid`, `main_ticketid`, `sub_ticket`, `ticket_label`, `ticket_label_date`, `last_updated_time` FROM `tickets` WHERE `deleted`=0 AND `status` NOT IN ('Archive','Archived','Done') AND ($query_match_program) ORDER BY `ticketid` DESC";
            $ticket_list = mysqli_query($dbc, "SELECT `ticketid`, `heading`, `businessid`, `clientid`, `contactid`, `ticket_type`, `to_do_date`, `created_date`, `status`, `projectid`, `main_ticketid`, `sub_ticket`, `ticket_label`, `ticket_label_date`, `last_updated_time` FROM `tickets` WHERE `deleted`=0 AND `status` NOT IN ('Archive','Archived','Done') AND ($query_match_program) ORDER BY `ticketid` DESC");
            while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $ticket = mysqli_fetch_assoc($ticket_list)) {
                $label = get_ticket_label($dbc, $ticket);
                if($key == $ticket['ticketid'] || $key == $ticket['projectid'] || substr(strtolower($label),0,strlen($key)) == $key || in_array_any($matched_contacts,array_filter(explode($ticket['businessid'].','.$ticket['siteid'].','.$ticket['clientid'].','.$ticket['contactid'].','.$ticket['internal_qa_contactid'].','.$ticket['deliverable_contactid'])))) {
                    $ticket_contacts = ($ticket['businessid'] > 0 ? get_client($dbc, $ticket['businessid']) : get_contact($dbc, $ticket['clientid']));
                    $search_results[] = ['label'=>'Ticket: ' . $label.' ('.$ticket_contacts.')','key'=>$label.' '.$ticket_contacts,'link'=>WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].$from_address];
                }
            }
        }
    }
    if(($_GET['category'] ?: 'checklists') == 'checklists') {
        echo "Loading Checklists (at ".(microtime(true) - $time).")\n";
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'checklist')) {
            $checklists = mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklist_name` LIKE '$key%' AND (`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `assign_staff` LIKE '%ALL%') AND `deleted`=0");
            while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $checklist = mysqli_fetch_assoc($checklists)) {
                $search_results[] = ['label'=>'Checklist: '.$checklist['checklist_name'],'link'=>WEBSITE_URL.'/Checklist/checklist.php?view='.$checklist['checklistid']];
            }
            $checklists = mysqli_query($dbc, "SELECT `checklist`.`checklistid`, `checklist_name`, `checklist`, `checklistnameid` FROM `checklist_name` LEFT JOIN `checklist` ON `checklist_name`.`checklistid`=`checklist`.`checklistid` WHERE `checklist`.`deleted`=0 AND `checklist_name`.`deleted`=0 AND `checklist` LIKE '$key%' AND (`checklist`.`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `checklist`.`assign_staff` LIKE '%ALL%')");
            while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $checklist = mysqli_fetch_assoc($checklists)) {
                $search_results[] = ['label'=>'Checklist: Item #'.$checklist['checklistnameid'].' on Checklist: '.$checklist['checklist_name'],'link'=>WEBSITE_URL.'/Checklist/checklist.php?view='.$checklist['checklistid']];
            }
        }
    }


    if(($_GET['category'] ?: 'tasks') == 'tasks') {
        echo "Loading Tasks (at ".(microtime(true) - $time).")\n";
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'tasks')) {
            $tasks = mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE (`tasklistid`='$key' OR `heading` LIKE '$key%' OR `task` LIKE '$key%') AND `deleted`=0");
            while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $task = mysqli_fetch_assoc($tasks)) {
                $search_results[] = ['label'=>'Task: #'.$task['tasklistid'].' '.$task['heading'],'link'=>WEBSITE_URL.'/Tasks_Updated/add_task_full_view.php?tasklistid='.$task['tasklistid']];
            }
        }
    }

    if(($_GET['category'] ?: 'demo') == 'demo') {
        echo "Loading Tasks (at ".(microtime(true) - $time).")\n";
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'demo')) {
            $tasks = mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE (`tasklistid`='$key' OR `heading` LIKE '$key%' OR `task` LIKE '$key%') AND `deleted`=0");
            while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $task = mysqli_fetch_assoc($tasks)) {
                $search_results[] = ['label'=>'Demo: #'.$task['tasklistid'].' '.$task['heading'],'link'=>WEBSITE_URL.'/Demo/index.php'];
            }
        }
    }

    /*
    if(($_GET['category'] ?: 'tasks_updated') == 'tasks_updated') {
        echo "Loading Tasks (Updated) (at ".(microtime(true) - $time).")\n";
        if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'tasks_updated')) {
            $tasks_updated = mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE (`tasklistid`='$key' OR `heading` LIKE '$key%' OR `task` LIKE '$key%') AND `deleted`=0");
            while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $task = mysqli_fetch_assoc($tasks_updated)) {
                $search_results[] = ['label'=>'Task: #'.$task['tasklistid'].' '.$task['heading'],'link'=>WEBSITE_URL.'/Tasks_Updated/add_task.php?tasklistid='.$task['tasklistid']];
            }
        }
    }

    */
} else {
    // Counter
	$all_count = [];

	$tile_count = [];
	foreach($_SESSION['tile_count'] as $line) {
		if(substr(strtolower($line['key']),0,strlen($key)) == $key) {
			$tile_count[$line['label'].'<!--'.$line['link'].'-->'] = $line['link'];
			$all_count[$line['label'].'<!--'.$line['link'].'-->'] = $line['link'];
		}
	}

	$contact_count = [];
    $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name` IN ('contacts', 'contacts3', 'contacts_rolodex', 'members', 'clientinfo') AND `category` != 'Staff'"));

    foreach($contact_list as $contact) {
        if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
            $contact_count[] = $contact['contactid'];
            $all_count[] = $contact['contactid'];
        }
    }


	$staff_count = [];
    $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `category` = 'Staff'"));
    foreach($contact_list as $contact) {
        if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
            $staff_count[] = $contact['contactid'];
            $all_count[] = $contact['contactid'];
        }
    }

	$project_count = [];
    $query_options = "`projectid` = '$key' OR `project_name` LIKE '$key%' OR `status` LIKE '$key%' OR `projecttype` LIKE '$key%'";
    $query_match_program = '1=1';
    if(!empty(MATCH_CONTACTS)) {
        $query_match_program = '1=0';
        foreach(explode(',',MATCH_CONTACTS) as $contactid) {
            $query_match_program .= " OR `businessid`='$contactid' OR CONCAT(',',`clientid`,',') LIKE '%,$contactid,%'";
        }
    }
    $project_list = mysqli_query($dbc, "SELECT `projectid`, `project_name`, `businessid`, `clientid`, `status`, `projecttype`, `project_lead` FROM `project` WHERE `deleted`=0 AND ($query_options) AND ($query_match_program)");
    while($project = mysqli_fetch_assoc($project_list)) {
        $project_count[] = $project['projectid'];
        $all_count[] = $project['projectid'];
    }

    $checklists = mysqli_query($dbc, "SELECT * FROM `project_milestone_checklist` WHERE `checklist` LIKE '$key%' AND `deleted`=0");
    while($checklist = mysqli_fetch_assoc($checklists)) {
        $project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `projectid`, `projecttype`, `project_name`, `status`, `project_path`, `deleted`, `businessid`, `clientid` FROM `project` WHERE `projectid`='{$checklist['projectid']}'"));
        if($project['deleted'] == 0 && in_array($checklist['milestone'],explode('#*#',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `milestone` FROM `project_path_milestone` WHERE `project_path_milestone`='{$project['project_path']}'"))['milestone']))) {
            $project_count[] = $checklist['projectid'];
            $all_count[] = $checklist['projectid'];
        }
    }

	$ticket_count = [];

        $query_match_program = '1=1';
		if(!empty(MATCH_CONTACTS)) {
			$query_match_program = '1=0';
			foreach(explode(',',MATCH_CONTACTS) as $contactid) {
				$query_match_program .= " OR `businessid`='$contactid' OR CONCAT(',',`clientid`,',') LIKE '%,$contactid,%'";
			}
		}
		$ticket_list = mysqli_query($dbc, "SELECT `ticketid`, `heading`, `businessid`, `clientid`, `contactid`, `ticket_type`, `to_do_date`, `created_date`, `status`, `projectid`, `main_ticketid`, `sub_ticket`, `ticket_label`, `ticket_label_date`, `last_updated_time` FROM `tickets` WHERE `deleted`=0 AND `status` NOT IN ('Archive','Archived','Done') AND ($query_match_program) ORDER BY `ticketid` DESC");
		while($ticket = mysqli_fetch_assoc($ticket_list)) {
			$label = get_ticket_label($dbc, $ticket);
			if($key == $ticket['ticketid'] || $key == $ticket['projectid'] || substr(strtolower($label),0,strlen($key)) == $key || in_array_any($matched_contacts,array_filter(explode($ticket['businessid'].','.$ticket['siteid'].','.$ticket['clientid'].','.$ticket['contactid'].','.$ticket['internal_qa_contactid'].','.$ticket['deliverable_contactid'])))) {
				$ticket_count[] =$ticket['ticketid'];
				$all_count[] =$ticket['ticketid'];
			}
		}

	    $checklist_count = [];

		$checklists = mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklist_name` LIKE '$key%' AND (`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `assign_staff` LIKE '%ALL%') AND `deleted`=0");
		while($checklist = mysqli_fetch_assoc($checklists)) {
			$checklist_count[] = $checklist['checklistid'];
				$all_count[] =$checklist['checklistid'];
		}
		$checklists = mysqli_query($dbc, "SELECT `checklist`.`checklistid`, `checklist_name`, `checklist`, `checklistnameid` FROM `checklist_name` LEFT JOIN `checklist` ON `checklist_name`.`checklistid`=`checklist`.`checklistid` WHERE `checklist`.`deleted`=0 AND `checklist_name`.`deleted`=0 AND `checklist` LIKE '$key%' AND (`checklist`.`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `checklist`.`assign_staff` LIKE '%ALL%')");
		while($checklist = mysqli_fetch_assoc($checklists)) {
			$checklist_count[] = $checklist['checklistid'];
				$all_count[] =$checklist['checklistid'];
		}

	    $task_count = [];

		$tasks = mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE (`tasklistid`='$key' OR `heading` LIKE '$key%' OR `task` LIKE '$key%') AND `deleted`=0");
		while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $task = mysqli_fetch_assoc($tasks)) {
			$task_count[] = $task['tasklistid'];
		    $all_count[] =$task['tasklistid'];
		}
    // Counter


        if( $_GET['search_type']=='ALL' || $_GET['search_type']=='TILES') {
            if(($_GET['category'] ?: 'tiles') == 'tiles') {
                echo "Loading Tile List (at ".(microtime(true) - $time).")\n";
                $no_display = true;
                include_once('tiles.php');
                $no_display = false;
                $tile_list = [];
                foreach($_SESSION['tile_list'] as $line) {
                    if(substr(strtolower($line['key']),0,strlen($key)) == $key) {
                        $tile_list[$line['label'].'<!--'.$line['link'].'-->'] = $line['link'];
                    }
                }
                ksort($tile_list);
                foreach($tile_list as $label => $link) {
                    $search_results[] = ['label'=>$label,'link'=>$link];
                }
            }
        }
        if( $_GET['search_type']=='ALL' || $_GET['search_type']=='CONTACTS') {
            if(($_GET['category'] ?: 'contacts') == 'contacts') {
                echo "Loading Contacts (at ".(microtime(true) - $time).")\n";
                $from_address = '&from='.(!empty($_GET['return']) ? urlencode($_GET['return']) : urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']));
                if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'contacts_inbox')) {
                    $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name`='contacts' AND `category` != 'Staff'"));
                    foreach($contact_list as $contact) {
                        if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                            $search_results[] = ['label'=>CONTACTS_TILE.': ' . $contact['category'].': '.($contact['category'] == 'Business' ? $contact['name'] : $contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').($contact['display_name'] == '' ? $contact['site_name'] : $contact['display_name']).' '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : '')),'link'=>WEBSITE_URL.'/Contacts/contacts_inbox.php?edit='.$contact['contactid']];
                            $matched_contacts[] = $contact['contactid'];
                        }
                    }
                } else if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'contacts')) {
                    $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name`='contacts' AND `category` != 'Staff'"));
                    foreach($contact_list as $contact) {
                        if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                            $search_results[] = ['label'=>CONTACTS_TILE.': ' . $contact['category'].': '.($contact['category'] == 'Business' ? $contact['name'] : $contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').($contact['display_name'] == '' ? $contact['site_name'] : $contact['display_name']).' '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : '')),'link'=>WEBSITE_URL.'/Contacts/add_contacts.php?contactid='.$contact['contactid']];
                            $matched_contacts[] = $contact['contactid'];
                        }
                    }
                }
            }
        }
        if( $_GET['search_type']=='ALL' || $_GET['search_type']=='CONTACTS') {
            if(($_GET['category'] ?: 'contacts3') == 'contacts3') {
                echo "Loading Contacts3 (at ".(microtime(true) - $time).")\n";
                if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'contacts3')) {
                    $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name`='contacts3' AND `category` != 'Staff'"));
                    foreach($contact_list as $contact) {
                        if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                            $search_results[] = ['label'=>CONTACTS_TILE.': ' . $contact['category'].': '.($contact['category'] == 'Business' ? $contact['name'] : $contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').($contact['display_name'] == '' ? $contact['site_name'] : $contact['display_name']).' '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : '')),'link'=>WEBSITE_URL.'/Contacts3/contacts_inbox.php?edit='.$contact['contactid']];
                            $matched_contacts[] = $contact['contactid'];
                        }
                    }
                }
            }
        }
        if( $_GET['search_type']=='ALL' || $_GET['search_type']=='CONTACTS') {
            if(($_GET['category'] ?: 'contactsrolodex') == 'contactsrolodex') {
                echo "Loading Rolodex (at ".(microtime(true) - $time).")\n";
                if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'contacts_rolodex')) {
                    $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name`='contactsrolodex' AND `category` != 'Staff'"));
                    foreach($contact_list as $contact) {
                        if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                            $search_results[] = ['label'=>CONTACTS_TILE.': ' . $contact['category'].': '.($contact['category'] == 'Business' ? $contact['name'] : $contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').($contact['display_name'] == '' ? $contact['site_name'] : $contact['display_name']).' '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : '')),'link'=>WEBSITE_URL.'/ContactsRolodex/contacts_inbox.php?edit='.$contact['contactid']];
                            $matched_contacts[] = $contact['contactid'];
                        }
                    }
                }
            }
        }
        if( $_GET['search_type']=='ALL' || $_GET['search_type']=='CONTACTS') {
            if(($_GET['category'] ?: 'members') == 'members') {
                echo "Loading Members (at ".(microtime(true) - $time).")\n";
                if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'members')) {
                    $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name`='members' AND `category` != 'Staff'"));
                    foreach($contact_list as $contact) {
                        if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                            $search_results[] = ['label'=>'Members: ' . $contact['category'].': '.($contact['category'] == 'Business' ? $contact['name'] : $contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').($contact['display_name'] == '' ? $contact['site_name'] : $contact['display_name']).' '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : '')),'link'=>WEBSITE_URL.'/Members/contacts_inbox.php?edit='.$contact['contactid']];
                            $matched_contacts[] = $contact['contactid'];
                        }
                    }
                }
            }
        }
        if( $_GET['search_type']=='ALL' || $_GET['search_type']=='CONTACTS') {
            if(($_GET['category'] ?: 'clients') == 'clients') {
                echo "Loading Clients (at ".(microtime(true) - $time).")\n";
                if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'client_info')) {
                    $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `display_name`, `site_name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `tile_name`='clientinfo' AND `category` != 'Staff'"));
                    foreach($contact_list as $contact) {
                        if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                            $search_results[] = ['label'=>'Clients: ' . $contact['category'].': '.($contact['category'] == 'Business' ? $contact['name'] : $contact['name'].($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').($contact['display_name'] == '' ? $contact['site_name'] : $contact['display_name']).' '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : '')),'link'=>WEBSITE_URL.'/ClientInfo/contacts_inbox.php?edit='.$contact['contactid']];
                            $matched_contacts[] = $contact['contactid'];
                        }
                    }
                }
            }
        }
        if( $_GET['search_type']=='ALL' || $_GET['search_type']=='STAFF') {
            if(($_GET['category'] ?: 'staff') == 'staff') {
                echo "Loading Staff (at ".(microtime(true) - $time).")\n";
                if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'staff') && (vuaed_visible_function($dbc, 'staff') || check_subtab_persmission($dbc, 'staff', ROLE, 'id_card'))) {
                    $contact_list = sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `prefer_name`, `name`, `email_address`, `office_phone`, `home_phone`, `cell_phone`, `business_street`, `business_city`, `business_state`, `business_country`, `businessid`, `business_zip`, `category` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND `category` = 'Staff'"));
                    foreach($contact_list as $contact) {
                        if($contact['contactid'] == $key || $contact['office_phone'] == $key || $contact['home_phone'] == $key || $contact['cell_phone'] == $key || $contact['businessid'] == $key || substr(strtolower($contact['category']),0,strlen($key)) == $key || substr(strtolower($contact['first_name'].' '.$contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['first_name']),0,strlen($key)) == $key || substr(strtolower($contact['last_name']),0,strlen($key)) == $key || substr(strtolower($contact['prefer_name']),0,strlen($key)) == $key || substr(strtolower($contact['name']),0,strlen($key)) == $key || substr(strtolower($contact['display_name']),0,strlen($key)) == $key || substr(strtolower($contact['site_name']),0,strlen($key)) == $key || strtolower($contact['email_address']) == $key || substr(strtolower($contact['business_street']),0,strlen($key)) == $key || strtolower($contact['business_city']) == $key || strtolower($contact['business_state']) == $key || strtolower($contact['business_country']) == $key || substr(strtolower(get_contact($dbc, $contact['businessid'],'name')),0,strlen($key)) == $key) {
                            $search_results[] = ['label'=>'Staff: '.$contact['first_name'].' '.$contact['last_name'].($contact['prefer_name'] != '' ? ' ('.$contact['prefer_name'].')' : ''),'link'=>WEBSITE_URL.'/Staff/staff_edit.php?contactid='.$contact['contactid']];
                            $matched_contacts[] = $contact['contactid'];
                        }
                    }
                }
            }
        }
        if( $_GET['search_type']=='ALL' || $_GET['search_type']=='PROJECTS') {
            if(($_GET['category'] ?: 'projects') == 'projects') {
                echo "Loading Projects (at ".(microtime(true) - $time).")\n";
                if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'project')) {
                    $query_options = "`projectid` = '$key' OR `project_name` LIKE '$key%' OR `status` LIKE '$key%' OR `projecttype` LIKE '$key%'";
                    foreach($matched_contacts as $contactid) {
                        $query_options .= " OR `businessid`='$contactid' OR CONCAT(',',`clientid`,',') LIKE '%,$contactid,%' OR `project_lead`='$contactid'";
                    }
                    $query_match_program = '1=1';
                    if(!empty(MATCH_CONTACTS)) {
                        $query_match_program = '1=0';
                        foreach(explode(',',MATCH_CONTACTS) as $contactid) {
                            $query_match_program .= " OR `businessid`='$contactid' OR CONCAT(',',`clientid`,',') LIKE '%,$contactid,%'";
                        }
                    }
                    $project_list = mysqli_query($dbc, "SELECT `projectid`, `project_name`, `businessid`, `clientid`, `status`, `projecttype`, `project_lead` FROM `project` WHERE `deleted`=0 AND ($query_options) AND ($query_match_program)");
                    $editable = vuaed_visible_function($dbc, 'project');
                    while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $project = mysqli_fetch_assoc($project_list)) {
                        $clients = [];
                        foreach(explode(',',$project['clientid']) as $clientid) {
                            if($clientid > 0) {
                                $clients[] = get_contact($dbc, $clientid);
                            }
                        }
                        $project_label = get_project_label($dbc, $project);
                        $search_results[] = ['label'=>'Project: ' . $project_label.' ('.($project['businessid'] > 0 ? get_client($dbc, $project['businessid']) : implode(', ',$clients)).')','link'=>$editable ? WEBSITE_URL.'/Project/projects.php?edit='.$project['projectid'] : ''];
                    }
                    if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $editable) {
                        $checklists = mysqli_query($dbc, "SELECT * FROM `project_milestone_checklist` WHERE `checklist` LIKE '$key%' AND `deleted`=0");
                        while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $checklist = mysqli_fetch_assoc($checklists)) {
                            $project = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `projectid`, `projecttype`, `project_name`, `status`, `project_path`, `deleted`, `businessid`, `clientid` FROM `project` WHERE `projectid`='{$checklist['projectid']}'"));
                            if($project['deleted'] == 0 && in_array($checklist['milestone'],explode('#*#',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `milestone` FROM `project_path_milestone` WHERE `project_path_milestone`='{$project['project_path']}'"))['milestone']))) {
                                $search_results[] = ['label'=>'Project: Item #'.$checklist['checklistid'].' on '.get_project_label($dbc, $project),'link'=>WEBSITE_URL.'/Project/projects.php?edit='.$checklist['projectid'].'&tab=path_'.preg_replace('/[^a-z]*/','',strtolower($checklist['milestone']))];
                            }
                        }
                    }
                }
            }
        }
        if( $_GET['search_type']=='ALL' || $_GET['search_type']=='TICKETS') {
            if(($_GET['category'] ?: 'tickets') == 'tickets') {
                echo "Loading Tickets (at ".(microtime(true) - $time).")\n";
                if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'ticket')) {
                    foreach($matched_contacts as $contactid) {
                        $query_options .= " OR `businessid`='$contactid' OR `siteid`='$contactid' OR CONCAT(',',`clientid`,',') LIKE '%,$contactid,%' OR CONCAT(',',`contactid`,',') LIKE '%,$contactid,%' OR CONCAT(',',`internal_qa_contactid`,',') LIKE '%,$contactid,%' OR CONCAT(',',`deliverable_contactid`,',') LIKE '%,$contactid,%'";
                    }
                    $query_match_program = '1=1';
                    if(!empty(MATCH_CONTACTS)) {
                        $query_match_program = '1=0';
                        foreach(explode(',',MATCH_CONTACTS) as $contactid) {
                            $query_match_program .= " OR `businessid`='$contactid' OR CONCAT(',',`clientid`,',') LIKE '%,$contactid,%'";
                        }
                    }
                    echo "SELECT `ticketid`, `heading`, `businessid`, `clientid`, `contactid`, `ticket_type`, `to_do_date`, `created_date`, `status`, `projectid`, `main_ticketid`, `sub_ticket`, `ticket_label`, `ticket_label_date`, `last_updated_time` FROM `tickets` WHERE `deleted`=0 AND `status` NOT IN ('Archive','Archived','Done') AND ($query_match_program) ORDER BY `ticketid` DESC";
                    $ticket_list = mysqli_query($dbc, "SELECT `ticketid`, `heading`, `businessid`, `clientid`, `contactid`, `ticket_type`, `to_do_date`, `created_date`, `status`, `projectid`, `main_ticketid`, `sub_ticket`, `ticket_label`, `ticket_label_date`, `last_updated_time` FROM `tickets` WHERE `deleted`=0 AND `status` NOT IN ('Archive','Archived','Done') AND ($query_match_program) ORDER BY `ticketid` DESC");
                    while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $ticket = mysqli_fetch_assoc($ticket_list)) {
                        $label = get_ticket_label($dbc, $ticket);
                        if($key == $ticket['ticketid'] || $key == $ticket['projectid'] || substr(strtolower($label),0,strlen($key)) == $key || in_array_any($matched_contacts,array_filter(explode($ticket['businessid'].','.$ticket['siteid'].','.$ticket['clientid'].','.$ticket['contactid'].','.$ticket['internal_qa_contactid'].','.$ticket['deliverable_contactid'])))) {
                            $ticket_contacts = ($ticket['businessid'] > 0 ? get_client($dbc, $ticket['businessid']) : get_contact($dbc, $ticket['clientid']));
                            $search_results[] = ['label'=>'Ticket: ' . $label.' ('.$ticket_contacts.')','key'=>$label.' '.$ticket_contacts,'link'=>WEBSITE_URL.'/Ticket/index.php?edit='.$ticket['ticketid'].$from_address];
                        }
                    }
                }
            }
        }
        if( $_GET['search_type']=='ALL' || $_GET['search_type']=='CHECKLISTS') {
            if(($_GET['category'] ?: 'checklists') == 'checklists') {
                echo "Loading Checklists (at ".(microtime(true) - $time).")\n";
                if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'checklist')) {
                    $checklists = mysqli_query($dbc, "SELECT * FROM `checklist` WHERE `checklist_name` LIKE '$key%' AND (`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `assign_staff` LIKE '%ALL%') AND `deleted`=0");
                    while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $checklist = mysqli_fetch_assoc($checklists)) {
                        $search_results[] = ['label'=>'Checklist: '.$checklist['checklist_name'],'link'=>WEBSITE_URL.'/Checklist/checklist.php?view='.$checklist['checklistid']];
                    }
                    $checklists = mysqli_query($dbc, "SELECT `checklist`.`checklistid`, `checklist_name`, `checklist`, `checklistnameid` FROM `checklist_name` LEFT JOIN `checklist` ON `checklist_name`.`checklistid`=`checklist`.`checklistid` WHERE `checklist`.`deleted`=0 AND `checklist_name`.`deleted`=0 AND `checklist` LIKE '$key%' AND (`checklist`.`assign_staff` LIKE '%,{$_SESSION['contactid']},%' OR `checklist`.`assign_staff` LIKE '%ALL%')");
                    while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $checklist = mysqli_fetch_assoc($checklists)) {
                        $search_results[] = ['label'=>'Checklist: Item #'.$checklist['checklistnameid'].' on Checklist: '.$checklist['checklist_name'],'link'=>WEBSITE_URL.'/Checklist/checklist.php?view='.$checklist['checklistid']];
                    }
                }
            }
        }
        if( $_GET['search_type']=='ALL' || $_GET['search_type']=='TASKS') {
            if(($_GET['category'] ?: 'tasks') == 'tasks') {
                echo "Loading Tasks (at ".(microtime(true) - $time).")\n";
                if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'tasks')) {
                    $tasks = mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE (`tasklistid`='$key' OR `heading` LIKE '$key%' OR `task` LIKE '$key%') AND `deleted`=0");
                    while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $task = mysqli_fetch_assoc($tasks)) {
                        $search_results[] = ['label'=>'Task 1: #'.$task['tasklistid'].' '.$task['heading'],'link'=>WEBSITE_URL.'/Tasks_Updated/add_task_full_view.php?tasklistid='.$task['tasklistid']];
                    }
                }
            }
        }
        if( $_GET['search_type']=='ALL' || $_GET['search_type']=='DEMO') {
            if(($_GET['category'] ?: 'demo') == 'demo') {
                echo "Loading Tasks (at ".(microtime(true) - $time).")\n";
                if(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && tile_visible($dbc, 'demo')) {
                    $tasks = mysqli_query($dbc, "SELECT * FROM `tasklist` WHERE (`tasklistid`='$key' OR `heading` LIKE '$key%' OR `task` LIKE '$key%') AND `deleted`=0");
                    while(($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest' || count($search_results) < ($offset + $rows)) && $task = mysqli_fetch_assoc($tasks)) {
                        $search_results[] = ['label'=>'Demo: #'.$task['tasklistid'].' '.$task['heading'],'link'=>WEBSITE_URL.'/Demo/index.php'];
                    }
                }
            }
        }
}
echo "Done Searching-->";
$results = count($search_results);
$results_count = "SELECT $results `numrows`";

if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { ?>
	<div class="container">
		<div class="row">
			<h2>Search Results</h2><?php print_r($page_load_times);
            $key = strtolower(filter_var($_GET['search_query'],FILTER_SANITIZE_STRING));

            ?>

            <div class="tab pull-left"><a href="search.php?search_query=<?php echo $key ?>&search_type=ALL"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= ( $_GET['search_type']=='ALL' || empty($_GET['search_type']) ? 'active_tab' : '' ) ?>">ALL (<?php echo count($all_count); ?>)</button></a></div>
            <div class="tab pull-left"><a href="search.php?search_query=<?php echo $key ?>&search_type=TILES"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= ( $_GET['search_type']=='TILES' ? 'active_tab' : '' ) ?>">TILES (<?php echo count($tile_count); ?>)</button></a></div>
            <div class="tab pull-left"><a href="search.php?search_query=<?php echo $key ?>&search_type=CONTACTS"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= ( $_GET['search_type']=='CONTACTS' ? 'active_tab' : '' ) ?>">CONTACTS (<?php echo count($contact_count); ?>)</button></a></div>
            <div class="tab pull-left"><a href="search.php?search_query=<?php echo $key ?>&search_type=STAFF"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= ( $_GET['search_type']=='STAFF' ? 'active_tab' : '' ) ?>">STAFF (<?php echo count($staff_count); ?>)</button></a></div>
            <div class="tab pull-left"><a href="search.php?search_query=<?php echo $key ?>&search_type=PROJECTS"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= ( $_GET['search_type']=='PROJECTS' ? 'active_tab' : '' ) ?>">PROJECTS (<?php echo count($project_count); ?>)</button></a></div>
            <div class="tab pull-left"><a href="search.php?search_query=<?php echo $key ?>&search_type=TICKETS"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= ( $_GET['search_type']=='TICKETS' ? 'active_tab' : '' ) ?>">TICKETS (<?php echo count($ticket_count); ?>)</button></a></div>
            <div class="tab pull-left"><a href="search.php?search_query=<?php echo $key ?>&search_type=CHECKLISTS"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= ( $_GET['search_type']=='CHECKLISTS' ? 'active_tab' : '' ) ?>">CHECKLISTS (<?php echo count($checklist_count); ?>)</button></a></div>
            <div class="tab pull-left"><a href="search.php?search_query=<?php echo $key ?>&search_type=TASKS"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= ( $_GET['search_type']=='TASKS' ? 'active_tab' : '' ) ?>">TASKS (<?php echo count($task_count); ?>)</button></a></div>
            <div class="tab pull-left"><a href="search.php?search_query=<?php echo $key ?>&search_type=DEMO"><button type="button" class="btn brand-btn mobile-block mobile-100 <?= ( $_GET['search_type']=='DEMO' ? 'active_tab' : '' ) ?>">DEMO (<?php echo count($task_count); ?>)</button></a></div>

            <br><br>
            <!--
			<form class="form-horizontal" action="" method="GET">
				<label class="col-sm-4 control-label">Search:</label>
				<div class="col-sm-4"><input type="text" name="search_query" class="form-control" value="<?= $key ?>"></div>
				<div class="col-sm-4"><button class="btn brand-btn" type="submit">Search</button></div>
                <br><br>
			</form>
            -->
			<div class="clearfix"></div>
			<?php //display_pagination($dbc, $results_count, $page, $rows); ?>
			<ul class="connectedChecklist">
<?php }
if($results == 0) {
	$search_results[] = ['label'=>'No Results Found for '.$key,'link'=>''];
}
foreach($search_results as $i => $line) {
	if($i >= $offset && $i < $offset + $rows) {
		if($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
			if($line['link'] != '') {
				echo '<a href="'.$line['link'].'">'.$line['label'].'</a>';
			} else {
				echo '<span class="small no_results">'.$line['label'].'</span>';
			}
		} else {
			if($line['link'] != '') {
				echo '<a href="'.$line['link'].'"><li>'.$line['label'].'</li></a>';
			} else {
				echo '<li class="small">'.$line['label'].'</li>';
			}
		}
	}
}
if($_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') { ?>
			</ul>
			<?php //display_pagination($dbc, $results_count, $page, $rows); ?>
		</div>
	</div>
    <br><br>
	<?php include_once('footer.php');
} else {
	echo '<a class="display_all" href="'.WEBSITE_URL.'/search.php?search_query='.$key.'&search_type=ALL">Display All Results</a>';
}
