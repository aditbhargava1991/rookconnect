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
	$query_check_credentials = "SELECT `contactid`, `businessid`, `category`, `name`, `first_name`, `last_name`, `site_name`, `display_name`, `description`, `office_phone`, `cell_phone`, `home_phone`, `email_address`, `website`, `address`, `mailing_address`, `business_address`, `ship_to_address`, `google_maps_address`, `ship_google_link`, `is_favourite`, `preferred_pronoun`, `birth_date`, `linkedin`, `facebook`, `twitter`, `google_plus`, `instagram`, `pinterest`, `youtube`, `blog`, `status` FROM contacts WHERE `contactid` IN ($id_list)";
	$query = "SELECT count(`contactid`) as numrows FROM contacts WHERE `contactid` IN ($id_list)";
}
else {
	$query_check_credentials = "SELECT `contactid`, `businessid`, `category`, `name`, `first_name`, `last_name`, `site_name`, `display_name`, `site_name`, `display_name`, `description`, `office_phone`, `cell_phone`, `home_phone`, `email_address`, `website`, `address`, `mailing_address`, `business_address`, `ship_to_address`, `google_maps_address`, `ship_google_link`, `is_favourite`, `preferred_pronoun`, `birth_date`, `linkedin`, `facebook`, `twitter`, `google_plus`, `instagram`, `pinterest`, `youtube`, `blog`, `status` FROM contacts WHERE (category LIKE '$category' OR ('$category'='Uncategorized' AND `category` NOT IN ('".implode("','",$lists)."','Staff'))) AND `tile_name`='".$folder_name."'";
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
?>
<div class="standard-dashboard-body-title">
<h3 class="gap-left"><?php echo $heading; ?>
<div class="pull-right hide-titles-mob col-sm-8">
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
			<button type="submit" value="<?= $category ?>" class="btn brand-btn" name="export_contacts">Export CSV</button>
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
</style>
<div class="hide-on-mobile"><?php include('../Contacts/contacts_export.php'); ?></div>
<div class="standard-dashboard-body-content">
<?php if($_GET['list'] != 'summary') { ?>
<div class="" style="margin:0;">
	<?php if($rows > 0): ?>
		<?php echo "<div class='pagination_links'>";
		if(isset($query))
			echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
		echo "</div>"; ?>
		<!--<table border="0" style="padding:20px;font-size:13px;line-height: 25px !important" width="100%">-->
			<?php
			$list = $category;
			$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contacts_dashboard FROM field_config_contacts WHERE tile_name = '".$folder_name."' AND tab='$list' AND accordion IS NULL UNION SELECT contacts_dashboard FROM `field_config_contacts` WHERE tile_name='".$folder_name."'"));
			$field_display = explode(",",$get_field_config['contacts_dashboard']);
			?>
			<?php foreach($contact_sort as $id): ?>
				<?php $row = $contact_list[array_search($id, array_column($contact_list,'contactid'))]; ?>
				<div class="dashboard-item set-relative">
                        <?php if(!empty($_GET['search_contacts']) || !empty($_POST['search_'.$category])) { ?>
						<div class="col-sm-6">
							<?php echo '<b>'.$row['category'].'</b>'; ?>
						</div>
                        <?php } ?>
					<div class="col-sm-6">
						<img src="../img/person.PNG" class="inline-img"><?= '<a href=\'?category='.$row['category'].'&edit='.$row['contactid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'\'>'.($row['category'] == 'Business' ? decryptIt($row['name']) : ($row['category'] == 'Sites' ? ($row['display_name'] != '' ? $row['display_name'] : $row['site_name']) : ($row['name'] != '' ? decryptIt($row['name']).': ' : '').decryptIt($row['first_name']) . ' ' . decryptIt($row['last_name']))).'</a>' ?>
					</div>
					<?php if(in_array('Business', $field_display) && $row['businessid'] > 0): ?>
						<div class="col-sm-6">
							<img src="../img/business.PNG" class="inline-img"><?php echo get_contact($dbc, $row['businessid'], 'name'); ?>
						</div>
					<?php endif; ?>
					<?php if(in_array('Email Address', $field_display)): ?>
						<div class="col-sm-6">
							<a href="mailto:<?= decryptIt($row['email_address']) ?>"><img src="../img/email.PNG" class="inline-img"><?= decryptIt($row['email_address']) ?></a>
						</div>
					<?php endif; ?>
					<?php if(in_array('Site', $field_display)): ?>
						<div class="col-sm-6">
							<img src="../img/project-path.PNG" class="inline-img"><?= $row['site_name'] ?>
						</div>
					<?php endif; ?>
					<?php if(in_array('Address', $field_display)): ?>
						<div class="col-sm-6">
							<?php $address = ($row['business_address'] ?: ($row['address'] ?: ($row['mailing_address'] ?: ($row['ship_to_address'] ?: get_address($dbc, $row['businessid'])))));
							$address = str_replace("<br>", ", ", $address); ?>
							<a class="show-on-mob" href="maps:<?= trim($address,', ') ?>"><img src="../img/address.PNG" class="inline-img"><?php echo rtrim(trim($address), ','); ?></a>
							<a class="hide-on-mobile" href="https://maps.google.com/maps/place/<?= trim($address,', ') ?>"><img src="../img/address.PNG" class="inline-img"><?php echo rtrim(trim($address), ','); ?></a>
						</div>
						<?php if($row['google_maps_address'].$row['ship_google_link'] != ''): ?>
							<div class="col-sm-6">
								<a href="<?= $row['google_maps_address'] ?: $row['ship_google_link'] ?>"><img src="../img/address.PNG" class="inline-img">Google Maps</a>
							</div>
						<?php endif; ?>
					<?php endif; ?>
					<?php if(in_array('Pronoun', $field_display)): ?>
						<div class="col-sm-6">
							<img src="../img/gender.png" class="inline-img"><?php switch($row['preferred_pronoun']) {
								case 1: echo "She/Her"; break;
								case 2: echo "He/Him"; break;
								case 3: echo "They/Them"; break;
								case 4: echo "Just use my name"; break;
								default: echo "Not Specified"; break;
							} ?>
						</div>
					<?php endif; ?>
					<?php if(in_array('Birthdate', $field_display)): ?>
						<div class="col-sm-6">
							<img src="../img/birthday.png" class="inline-img"><?= $row['birth_date'] ?><?= ( $row['birth_date']=='0000-00-00' || empty($row['birth_date']) ) ? '' : ' Age: '.date_diff(date_create($row['birth_date']), date_create('now'))->y ?>
						</div>
					<?php endif; ?>
					<?php if(in_array_any(['Office Phone','Home Phone','Cell Phone'],$field_display)) { ?>
						<div class="col-sm-6">
							<?php if($row['office_phone'] && in_array('Office Phone', $field_display)): ?>
								<a href="tel:<?= decryptIt($row['office_phone']) ?>"><img src="../img/office_phone.PNG" class="inline-img"><?= decryptIt($row['office_phone']); ?></a>
							<?php endif; ?>
							<?php if($row['home_phone'] && in_array('Home Phone', $field_display)): ?>
								<a href="tel:<?= decryptIt($row['home_phone']) ?>"><img src="../img/home_phone.PNG" class="inline-img"><?= decryptIt($row['home_phone']); ?></a>
							<?php endif; ?>
							<?php if($row['cell_phone'] && in_array('Cell Phone', $field_display)): ?>
								<a href="tel:<?= decryptIt($row['cell_phone']) ?>"><img src="../img/cell_phone.PNG" class="inline-img"><?= decryptIt($row['cell_phone']); ?></a>
							<?php endif; ?>
						</div>
					<?php } ?>
					<?php if(in_array('Social', $field_display)) { ?>
						<div class="col-sm-6">
							<?php if($row['linkedin'] != '') { ?><a href="<?= $row['linkedin'] ?>"><img src="../img/icons/social/linkedin.png" class="inline-img" /> LinkedIn</a><?php } ?>
							<?php if($row['facebook'] != '') { ?><a href="<?= $row['facebook'] ?>"><img src="../img/icons/social/facebook.png" class="inline-img" /> Facebook</a><?php } ?>
							<?php if($row['twitter'] != '') { ?><a href="<?= $row['twitter'] ?>"><img src="../img/icons/social/twitter.png" class="inline-img" /> Twitter</a><?php } ?>
							<?php if($row['google_plus'] != '') { ?><a href="<?= $row['google_plus'] ?>"><img src="../img/icons/social/google+.png" class="inline-img" /> Google+</a><?php } ?>
							<?php if($row['instagram'] != '') { ?><a href="<?= $row['instagram'] ?>"><img src="../img/icons/social/instagram.png" class="inline-img" /> Instagram</a><?php } ?>
							<?php if($row['pinterest'] != '') { ?><a href="<?= $row['pinterest'] ?>"><img src="../img/icons/social/pinterest.png" class="inline-img" /> Pinterest</a><?php } ?>
							<?php if($row['youtube'] != '') { ?><a href="<?= $row['youtube'] ?>"><img src="../img/icons/social/youtube.png" class="inline-img" /> YouTube</a><?php } ?>
							<?php if($row['blog'] != '') { ?><a href="<?= $row['blog'] ?>"><img src="../img/icons/social/rss.png" class="inline-img" /> Blog</a><?php } ?>
						</div>
					<?php } ?>
					<?php if(in_array('Website', $field_display)): ?>
						<div class="col-sm-6">
							<a target="_blank" href="<?= (strpos($row['website'],'http') !== FALSE ? '' : 'http://').$row['website'] ?>"><?= $row['website'] ?></a>
						</div>
					<?php endif; ?>
					<?php if(in_array('Description', $field_display)): ?>
						<div class="col-sm-6">
							<?= html_entity_decode($row['description']) ?>
						</div>
					<?php endif; ?>
					<div class="col-sm-6">
						<img src="../img/setting.PNG" class="inline-img">
						<?php if($edit_access > 0) {
							echo '<a href="" onclick="statusChange(this); return false;" data-status="'.$row['status'].'" data-contactid="'.$row['contactid'].'">'.($row['status'] == 0 ? 'Activate' : 'Deactivate').'</a> | ';
							echo '<a href="?category='.$row['category'].'&edit='.$row['contactid'].'&from='.urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']).'">Edit</a> | ';
							echo '<a href="" onclick="deleteContact(this); return false;" data-contactid="'.$row['contactid'].'">Archive</a>';
						} else {
							echo '<a href="?category='.$row['category'].'&edit='.$row['contactid'].'">View</a>';
						} ?>
					</div>
					<div class="clearfix"></div>
                    <div class="set-favourite">
						<?php if(strpos($row['is_favourite'],",".$_SESSION['contactid'].",") === FALSE): ?>
							<a href="?list=<?php echo $list; ?>&favourite=<?php echo $row['contactid']; ?>"><img src="../img/blank_favourite.png" alt="Favourite" title="Click to make the contact favourite" class="inline-img pull-right small"></a>
						<?php else: ?>
							<a href="?list=<?php echo $list; ?>&unfavourite=<?php echo $row['contactid']; ?>"><img src="../img/full_favourite.png" alt="Favourite" title="Click to make the contact unfavourite" class="inline-img pull-right small"></a>
						<?php endif; ?>
                    </div>
				</div>
				<!--<hr class="hide-on-mobile">-->
			<?php endforeach; ?>
		<!--</table>-->
	<?php
		echo "<div class='pagination_links'>";
		if(isset($query))
			echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
		echo "</div>";
	?>
	<br><br>
	<?php else: ?>
		<?php echo '<h3 class="gap-left">No Record Found.</h3>' ?>
	<?php endif; ?>
</div>
<?php } else {


    echo '<h3 class="double-gap-left">Contact Per Category</h3>';
    $lists = array_filter(explode(',',get_config($dbc, FOLDER_NAME.'_tabs')));
    foreach($lists as $list_name) {
        echo '<div class="col-sm-6">';
            echo '<div class="overview-block">';
                echo '<h4>'.$list_name.'</h4>';
                $active_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) `count` FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='$list_name' AND `status`=1"));
                echo 'Active : '.$active_count['count'];
                echo '<br>';
                $inactive_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) `count` FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='$list_name' AND `status`=0"));
                echo 'Inactive : '.$inactive_count['count'];
            echo '</div>';
        echo '</div>';
    }
    echo '<div class="clearfix"></div>';

    echo '<h3 class="double-gap-left">Contact Per Business</h3>';
    $lists = $dbc->query("SELECT contactid, name FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `category`='Business' AND `status`=0");
    while($list = $lists->fetch_assoc()) {
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

    echo '<h3 class="double-gap-left">Contacts Per Gender</h3>';
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

    echo '<h3 class="double-gap-left">Contact Per Classification</h3>';
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

/*
    echo '<h3>Contact per Regions</h3>';
    $con_regions = array_filter(array_unique(explode(',', get_config($dbc, '%_region', true))));

            if(count($con_regions) > 0) {

                         foreach($con_regions as $con_region):
                            $active_region = explode(',', $_GET['region']);
                            if(!in_array($con_region, $active_region)) {
                                $active_region[] = $con_region;
                            } else {
                                $active_region = array_diff($active_region, [$con_region]);
                            }
                            $active_region = implode(',', $active_region);
                            $region_count = mysqli_fetch_array(mysqli_query($dbc, "SELECT COUNT(`contactid`) count FROM `contacts` WHERE `deleted`=0 AND `tile_name`='".FOLDER_NAME."' AND `region`='$con_region' AND `status`=1"));
                            echo $con_region.' : '.$region_count['count'].'<br />';
                         endforeach;

            } else {
                echo '<h4>No Contacts</h4>';

            }
*/

 } ?>
</div>