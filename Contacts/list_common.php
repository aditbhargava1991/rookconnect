<?php
error_reporting(1);
include_once('../include.php');
include_once('../Contacts/edit_fields.php');

/* Start Pagination Counting */
$rowsPerPage = 10;
$pageNum = 1;
$folder_name = isset($_GET['tile_name']) ? filter_var($_GET['tile_name'],FILTER_SANITIZE_STRING) : FOLDER_NAME;

if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

/* AJAX request from list_contacts.php on mobile */
if ( isset($_GET['category']) ) {
    $category = filter_var($_GET['category'], FILTER_SANITIZE_STRING);
}

/* End Pagination Counting */
if(isset($_GET['favourite'])) {
	$favcontactid = $_GET['favourite'];
	$query = "UPDATE contacts set is_favourite=REPLACE(CONCAT(IFNULL(`is_favourite`,''),',".$_SESSION['contactid'].",'),',,',',') where contactid = $favcontactid";
	$make_favourite = mysqli_query($dbc,$query);
}
if(isset($_GET['unfavourite'])) {
	$unfavcontactid = $_GET['unfavourite'];
	$query = "UPDATE contacts set is_favourite=REPLACE(IFNULL(`is_favourite`,''),',".$_SESSION['contactid'].",',',') where contactid = $unfavcontactid";
	$make_favourite = mysqli_query($dbc,$query);
}

if($_GET['search_contacts'] != '') {
	$search_contacts = base64_decode($_GET['search_contacts']);
	$id_list = search_contacts_table($dbc, $search_contacts, " AND `tile_name`='".$folder_name."'");
	$query_check_credentials = "SELECT `contactid`, `businessid`, `category`, `name`, `first_name`, `last_name`, `site_name`, `display_name`, `description`, `office_phone`, `cell_phone`, `home_phone`, `email_address`, `website`, `address`, `mailing_address`, `business_address`, `ship_to_address`, `google_maps_address`, `ship_google_link`, `is_favourite`, `preferred_pronoun`, `birth_date`, `linkedin`, `facebook`, `twitter`, `google_plus`, `instagram`, `pinterest`, `youtube`, `blog`, `status` FROM contacts WHERE `contactid` IN ($id_list)";
	$query = "SELECT count(`contactid`) as numrows FROM contacts WHERE `contactid` IN ($id_list)";
}
else if(isset($_POST['search_'. $category.'_submit']) && $_POST['search_'. $category] != '') {
	$search_contacts = $_POST['search_'. $category];
	$id_list = search_contacts_table($dbc, $search_contacts, " AND `tile_name`='".$folder_name."' AND (category LIKE '$category' OR ('$category'='Uncategorized' AND `category` NOT IN ('".implode("','",$lists)."','Staff')))");
	$query_check_credentials = "SELECT `contactid`, `businessid`, `category`, `name`, `first_name`, `last_name`, `site_name`, `display_name`, `description`, `office_phone`, `cell_phone`, `home_phone`, `email_address`, `website`, `address`, `mailing_address`, `business_address`, `ship_to_address`, `google_maps_address`, `ship_google_link`, `is_favourite`, `preferred_pronoun`, `birth_date`, `linkedin`, `facebook`, `twitter`, `google_plus`, `instagram`, `pinterest`, `youtube`, `blog`, `status`, `flag_colour`, `flag_label`, `flag_start`, `flag_end` FROM contacts WHERE `contactid` IN ($id_list)";
	$query = "SELECT count(`contactid`) as numrows FROM contacts WHERE `contactid` IN ($id_list)";
}
else {
	$query_check_credentials = "SELECT `contactid`, `businessid`, `category`, `name`, `first_name`, `last_name`, `site_name`, `display_name`, `site_name`, `display_name`, `description`, `office_phone`, `cell_phone`, `home_phone`, `email_address`, `website`, `address`, `mailing_address`, `business_address`, `ship_to_address`, `google_maps_address`, `ship_google_link`, `is_favourite`, `preferred_pronoun`, `birth_date`, `linkedin`, `facebook`, `twitter`, `google_plus`, `instagram`, `pinterest`, `youtube`, `blog`, `status`, `flag_colour`, `flag_label`, `flag_start`, `flag_end` FROM contacts WHERE (category LIKE '$category' OR ('$category'='Uncategorized' AND `category` NOT IN ('".implode("','",$lists)."','Staff'))) AND `tile_name`='".$folder_name."'";
	$query = "SELECT count(`contactid`) as numrows FROM contacts WHERE (category LIKE '$category' OR ('$category'='Uncategorized' AND `category` NOT IN ('".implode("','",$lists)."','Staff'))) AND `tile_name`='".$folder_name."'";
}

$region = $_GET['region'];
$classification = $_GET['classification'];
$location = $_GET['location'];
$title = $_GET['title'];
$security_folder = $folder_name;
if($security_folder == 'clientinfo') {
	$security_folder = 'client_info';
} else if($security_folder == 'contactsrolodex') {
	$security_folder = 'contacts_rolodex';
} else if($security_folder == 'contacts') {
	$security_folder = 'contacts_inbox';
}
checkAuthorised($security_folder);
$edit_access = ($edit_access > 1 ? $edit_access : vuaed_visible_function($dbc, $security_folder, ROLE));

/* Filter Conditions start */
if($region != '')  {
	$region = trim($region, ",");
	$regions_exs = explode(",", $region);
	foreach($regions_exs as $regions_ex)
		$region_exs[] = "'".$regions_ex."'";
	$region_ex = implode(",",$region_exs);
	$query_check_credentials .= " AND region IN ($region_ex)";
	$query .= " AND region IN ($region_ex)";
} else if($classification != '') {
	$classification = trim($classification, ",");
	$classifications_exs = explode(",", $classification);
	foreach($classifications_exs as $classifications_ex)
		$classification_exs[] = $classifications_ex;
	$classification_ex = implode(",%' OR CONCAT(',',classification,',') LIKE '%,",$classification_exs);
	$query_check_credentials .= " AND (CONCAT(',',classification,',') LIKE '%,$classification_ex,%')";
	$query .= " AND (CONCAT(',',classification,',') LIKE '%,$classification_ex,%')";
} else if($location != '') {
	$query_check_credentials .= " AND con_locations='$location'";
	$query .= " AND con_locations='$classification'";
} else if($title != '') {
	$title = trim($title, ",");
	$titles_exs = explode(",", $title);
	foreach($titles_exs as $titles_ex)
		$title_exs[] = "'".$titles_ex."'";
	$title_ex = implode(",",$title_exs);
	$query_check_credentials .= " AND title IN ($title_ex)";
	$query .= " AND title IN ($title_ex)";
}

//Filter by Status
if($status == 'inactive') {
	$query_check_credentials .= " AND deleted=0 AND `status` = 0";
	$query .= " AND deleted=0 AND `status` = 0";
} else if($status == 'archive') {
	$query_check_credentials .= " AND `deleted` = 1";
	$query .= " AND `deleted` = 1";
} else {
	$query_check_credentials .= " AND deleted=0 AND `status` > 0";
	$query .= " AND deleted=0 AND `status` > 0";
}

//Filter by Match Staff
if($_GET['match_staff'] > 0) {
	$match_contacts = [];
	$match_contact_list = mysqli_query($dbc, "SELECT * FROM `match_contact` WHERE `deleted` = 0 AND CONCAT(',',`staff_contact`,',') LIKE '%,".$_GET['match_staff'].",%' AND `support_contact_category` = '".$category."'");
	while($match_contact = mysqli_fetch_assoc($match_contact_list)) {
		foreach(explode(',', $match_contact['support_contact']) as $support_contact) {
			if(!in_array($support_contact, $match_contacts)) {
				$match_contacts[] = $support_contact;
			}
		}
	}
	$match_contacts = implode(',',array_filter($match_contacts));
	$query_check_credentials .= " AND `contactid` IN (".$match_contacts.")";
	$query .= " AND `contactid` IN (".$match_contacts.")";
}

if(!empty(MATCH_CONTACTS)) {
	$query_check_credentials .= " AND `contactid` IN (".MATCH_CONTACTS.")";
	$query .= " AND `contactid` IN (".MATCH_CONTACTS.")";
}
/* Filter Conditions end */

/* Default Orderby start */
// if(!isset($_GET['sortby'])) {
	// $query_check_credentials .= " ORDER BY is_favourite LIKE ',".$_SESSION['contactid'].",' DESC, `contactid`";
// }

/* Filter Conditions end */

$rows = mysqli_fetch_array(mysqli_query($dbc,$query))['numrows'];
if($rows > 2500) {
	$results[] = mysqli_query($dbc, $query_check_credentials.' LIMIT '.$offset.', '.$rowsPerPage);
} else {
	for($i = 0; $i * 1000 < $rows; $i++) {
		$results[] = mysqli_query($dbc, $query_check_credentials.' LIMIT '.($i * 1000).', 1000');
	}
}

$contact_list = [];
$contact_sort = [];
foreach($results as $result) {
	$contact_list = array_merge($contact_list, mysqli_fetch_all($result, MYSQLI_ASSOC));
}
if($rows > 2500) {
	$contact_sort = array_column($contact_list, 'contactid');
} else {
	if(isset($_GET['sortby']))
		$contact_sort = array_splice(sort_contacts_array($contact_list, $_GET['sortby']), $offset, $rowsPerPage);
	else
		$contact_sort = array_splice(sort_contacts_array($contact_list), $offset, $rowsPerPage);
}
$i = 0;
$heading = ucwords($category);
if(ucwords($category) == 'Vendors') {
    $heading = VENDOR_TILE;
}
?>ROOK
<div class="contact_content" <?= $hide_contacts ? 'style="display: none;"' : '' ?>>
	<div class="standard-dashboard-body-title">
		<h3 class="gap-left"><span class="title_label">Summary</span>
			<div class="pull-right hide-titles-mob col-sm-8 title_options" style="display: none;">
				<form action="" method="POST">
					<!--
			        <span class="pull-left col-sm-8">
						<?php //if($_POST['search_'.$category]): ?>
							<input name="search_<?php //echo $category; ?>" type="text" value="<?php //echo $_POST['search_'.$category]; ?>" class="form-control"/>
						<?php //else: ?>
							<input name="search_<?php //echo $category; ?>" type="text" value="" placeholder="Search <?php //echo $category; ?>" class="form-control"/>
						<?php //endif; ?>
					</span>
			        -->
					<span class="pull-right">
						<!--
			            ** Moved to sidebar
			            ** Archived is no longer needed as it's under Archived Data tile
			            <a href="?list=<?= $_GET['list'] ?>&status=active" class="btn brand-btn <?= $status == 'active' ? 'active_tab' : '' ?>">Active</a>
						<a href="?list=<?= $_GET['list'] ?>&status=inactive" class="btn brand-btn <?= $status == 'inactive' ? 'active_tab' : '' ?>">Inactive</a>
						<a href="?list=<?= $_GET['list'] ?>&status=archive" class="btn brand-btn <?= $status == 'archive' ? 'active_tab' : '' ?>">Archived</a>
						-->
			            <!-- <input type="submit" value="Filter" class="btn brand-btn" name="search_<?php //echo $category; ?>_submit"> -->
						
						<a href="?edit=new&category=<?= $category ?>" class="btn brand-btn pull-right new_contact_url">New <?= $category ?></a>
						<button type="submit" value="<?= $category ?>" class="image-btn no-toggle" name="export_contacts" title="Export CSV"><img src="../img/icons/csv.png" width="30" /></button>
						<input type="hidden" name="export_option" value="Contact Information">
					</span>
				</form>
			</div>
			<div class="clearfix"></div>
		</h3>
	</div><?php
	$subtab = '';
	switch($status) {
	    case 'summary':
	        $subtab = 'contacts_summary';
	        break;
	    case 'active':
	        $subtab = 'contacts_active';
	        break;
	    case 'inactive':
	        $subtab = 'contacts_inactive';
	        break;
	    case 'archive':
	        $subtab = 'contacts_archived';
	        break;
	    default:
	        //$subtab = 'contacts_active';
	        break;
	}
	$notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `note` FROM `notes_setting` WHERE `tile`='contacts' AND `subtab`='$subtab'"));
	$note = $notes['note'];

	if ( !empty($note) ) { ?>
	    <div class="notice popover-examples">
	        <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
	        <div class="col-sm-11"><span class="notice-name">NOTE:</span>
	        <?= $note ?></div>
	        <div class="clearfix"></div>
	    </div><?php
	} ?>
	<style>
	@media (min-width:768px) {
		.sidebar {
			margin:10px;
			padding:25px;
		}
	}
	@media (max-width:767px) {
		.sidebar {
			margin-left:0;
			padding-right:0;
		}
	}
	.dashboard-icon { margin-right:8px; width:18px; }
	</style>
	<div class="hide-on-mobile"><?php include('../Contacts/contacts_export.php'); ?></div>
	<div class="standard-dashboard-body-content">
		<div class="contact_list" style="margin:0; display: none;">
		</div>
		<div class="summary_list">
		<?php $heading = FOLDER_NAME.'_summary';
		$contacts_summary_config = get_config($dbc, $heading);

		if(strpos($contacts_summary_config,'Per Category') !== false) {
		    echo '<h3 class="double-gap-left">'.CONTACTS_TILE.' Per Category</h3>';
		    $summary_lists = array_filter(explode(',',get_config($dbc, FOLDER_NAME.'_tabs')));
		    foreach($summary_lists as $list_name) {
		        echo '<div class="col-sm-6">';
		            echo '<div class="overview-block">';
		                echo '<h4>'.$list_name.'</h4>';
		                $active_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) `count` FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='$list_name' AND `status`=1"))['count'];
		                $inactive_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) `count` FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='$list_name' AND `status`=0"))['count'];
		                $all_count = $active_count + $inactive_count;
		                $active_percent = $all_count == 0 ? '00' : number_format((($active_count / $all_count) * 100), 0);
		                
		                $theme_color = get_calendar_today_color($dbc);
		                ?>
		                <div class="row">
		                    <div class="col-xs-6">
		                        <div class="radial_chart radial-chart" data-percent="<?= $active_percent ?>" data-duration="500" data-color="#bdc3c7,#<?= $theme_color ?>"></div>
		                    </div>
		                    <div class="col-xs-6 radial-chart-desc">
		                        <span>
		                            Active: <?= $active_count ?><br />
		                            Inactive: <?= $inactive_count ?>
		                        </span>
		                    </div>
		                </div><?php
		            echo '</div>';
		        echo '</div>';
		    }
		    echo '<div class="clearfix"></div>';
		}

		if(strpos($contacts_summary_config,'Per Business') !== false) {
		    echo '<h3 class="double-gap-left">'.CONTACTS_TILE.' Per Business</h3>';
		    $summary_lists = $dbc->query("SELECT contactid, name FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='Business' AND `status`=0");
		    while($list = $summary_lists->fetch_assoc()) {
		                $cid = $list['contactid'];
		                $active_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) `count` FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `businessid`='$cid' AND `status`=1"));
		                if($active_count['count'] > 0) {
		                    echo '<div class="col-sm-6">';
		                        echo '<div class="overview-block">';
		                             echo decryptIt($list['name']).' : '.$active_count['count'].'<br />';
		                        echo '</div>';
		                    echo '</div>';
		                }
		    }
		    echo '<div class="clearfix"></div>';
		}

		if(strpos($contacts_summary_config,'Per Gender') !== false) {
		    echo '<h3 class="double-gap-left">'.CONTACTS_TILE.' Per Gender</h3>';
		    $service_categories = $dbc->query("SELECT `name`, `first_name`, `last_name`, COUNT(contactid) AS total_gender, `gender` FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `status`=1 GROUP BY `gender`");
		    while($service_row = $service_categories->fetch_assoc()) {
		        echo '<div class="col-sm-6">';
		            echo '<div class="overview-block">';
		                if($service_row['gender'] == '') {
		                    $service_row['gender'] = 'Not specified';
		                }
		                echo $service_row['gender'].': '.$service_row['total_gender'];
		            echo '</div>';
		        echo '</div>';
		    }
		    echo '<div class="clearfix"></div>';
		}

		if(strpos($contacts_summary_config,'Per Classification') !== false) {
		    echo '<h3 class="double-gap-left">'.CONTACTS_TILE.' Per Classification</h3>';
		    $con_classifications = array_filter(explode(",", get_config($dbc, FOLDER_NAME.'_classification')));
		    if(count($con_classifications) > 0) {
		        foreach($con_classifications as $con_classification):
		            echo '<div class="col-sm-6">';
		                echo '<div class="overview-block">';
		                    $active_classification = explode(',', $_GET['classification']);
		                    if(!in_array($con_classification, $active_classification)) {
		                        $active_classification[] = $con_classification;
		                    } else {
		                        $active_classification = array_diff($active_classification, [$con_classification]);
		                    }
		                    $active_classification = implode(',', $active_classification);
		                    $classifications_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) count FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `classification`='$con_classification' AND `status`=1"));
		                    echo $con_classification.': '.$classifications_count['count'];
		                echo '</div>';
		            echo '</div>';
		        endforeach;
		    }
		    echo '<div class="clearfix"></div>';
		}

		if(strpos($contacts_summary_config,'Per City') !== false) {
		    echo '<h3 class="double-gap-left">'.CONTACTS_TILE.' Per City</h3>';
		    $service_categories = $dbc->query("SELECT `name`, `first_name`, `last_name`, COUNT(contactid) AS total_city, `city` FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `status`=1 GROUP BY `city`");
		    while($service_row = $service_categories->fetch_assoc()) {
		        echo '<div class="col-sm-6">';
		            echo '<div class="overview-block">';
		                if($service_row['city'] == '') {
		                    $service_row['city'] = 'Not specified';
		                }
		                echo $service_row['city'].': '.$service_row['total_city'];
		            echo '</div>';
		        echo '</div>';
		    }
		    echo '<div class="clearfix"></div>';
		}

		$get_current_url = "$_SERVER[REQUEST_URI]";
		if(strpos($contacts_summary_config,'Per Archived Data') !== false) {
		    echo '<h3 class="double-gap-left">'.CONTACTS_TILE.' Per Archived Data</h3>';
			$query = mysqli_query($dbc,"SELECT contactid, `name`, `first_name`, `last_name` FROM `contacts` WHERE `deleted`=1 AND `tile_name`='".FOLDER_NAME."'");
				echo '<div class="col-sm-6">';
		            echo '<div class="overview-block">';
		                while($row = mysqli_fetch_array($query)) {
		                    if($row['name'] != '' || $row['first_name'] != '') {
		                            echo decryptIt($row['name']).decryptIt($row['first_name']).' '.decryptIt($row['last_name']);
		                            echo ' : <a href=\'../delete_restore.php?action=restore&from='.$get_current_url.'&contactid='.$row['contactid'].'\' onclick="return confirm(\'Are you sure?\')">Restore</a><br>';
		                    }
		                }
		            echo '</div>';
		        echo '</div>';
		    echo '<div class="clearfix"></div>';
		} ?>
		</div>
	</div>
</div>