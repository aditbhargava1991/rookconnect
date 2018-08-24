<?php include_once('../include.php');
checkAuthorised('staff');
// Setup tabs
if($_GET['staff_cat'] == 'ALL') {
	$_GET['staff_cat'] = '';
}
$tab_list = [ 'active' => false, 'probation' => false, 'suspended' => false, 'security' => false, 'positions' => false, 'reminders' => false ];
$tab = isset($_GET['tab']) ? $_GET['tab'] : 'active';
$tab_note = '';
$security_access = get_security($dbc, 'staff');
$staff_categories = array_filter(explode(',',str_replace(',,',',',str_replace('Staff','',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT categories FROM field_config_contacts WHERE tab='Staff' AND `categories` IS NOT NULL"))['categories']))));
switch($tab) {
	case 'suspended':
		$body_title = 'Suspended Staff';
		$search_action = 'suspended';
		$tab_list['suspended'] = true;
		$tab_note = "A listing of all suspended staff. Suspended staff can be edited, reactivated or archived from here.";
		$staff_cat = $_GET['staff_cat'];
		if(!empty($staff_cat)) {
			$staff_cat_query = " AND CONCAT(',',`staff_category`,',') LIKE ('%,$staff_cat,%')";
		}
		break;
	case 'probation':
		$body_title = 'Staff on Probation';
		$search_action = 'probation';
		$tab_list['probation'] = true;
		$tab_note = "A listing of all staff on probation. Staff on probation can be edited, taken off probation, or archived from here.";
		$staff_cat = $_GET['staff_cat'];
		if(!empty($staff_cat)) {
			$staff_cat_query = " AND CONCAT(',',`staff_category`,',') LIKE ('%,$staff_cat,%')";
		}
		break;
	default:
		$body_title = 'Active Staff';
		$search_action = 'active';
		$_GET['tab'] = 'active';
		$tab_list['active'] = true;
		$tab = 'active';
		$tab_note = "A listing of all Active Users within your software.";
		$staff_cat = $_GET['staff_cat'];
		if(!empty($staff_cat)) {
			$staff_cat_query = " AND CONCAT(',',`staff_category`,',') LIKE ('%,$staff_cat,%')";
		}
		break;
}
include_once('../Contacts/contacts_search_function.php');
//Exclude George & FFM from showing up on SEA Contacts
if($rookconnect == 'sea') {
	$sea_constraint = " AND (user_name!='FFMAdmin' AND user_name!='georgev' AND user_name!='salimc' OR user_name IS NULL) ";
} else {
	$sea_constraint = '';
}

// This will pull the staff members in the current tab
$query_clause = "WHERE `category`='Staff' AND IFNULL(`user_name`,'')!='FFMAdmin' AND `deleted`=0 AND `show_hide_user`='1' AND `status`=".($tab_list['active'] ? '1' : ($tab_list['probation'] ? '2' : '0'));

// Pagination Config
$rowsPerPage = 10;
$pageNum = 1;

if(isset($_GET['page'])) {
	$pageNum = $_GET['page'];
}
$offset = ($pageNum - 1) * $rowsPerPage;

// Check for and apply filters
$filter_clause = '';
if(isset($_GET['filter'])) {
	$filter = mysqli_real_escape_string($dbc, $_GET['filter']);
} else {
	$filter = 'All';
}
$id_list = '';
if($filter == 'Top')
	$filter_clause .= "INNER JOIN (SELECT contactid TOP_ID FROM contacts WHERE category='Staff' AND `show_hide_user`='1' AND `deleted`='0' AND `status`=".($tab_list['active'] ? '1' : ($tab_list['probation'] ? '2' : '0'))." ORDER BY contactid DESC LIMIT 0, 25) TOP ON contacts.contactid=TOP.TOP_ID";
else if($filter != 'All') {
	$id_list = search_contacts_table($dbc, $filter, $sea_constraint." AND `category` LIKE 'Staff'", 'START');
}
// Check for and apply search strings
if(isset($_POST['search_contacts'])) {
	$query = '';
	if($id_list != '') {
		$query = " AND `contactid` IN ($id_list) ";
	}
	$search = mysqli_real_escape_string($dbc, $_POST['search_contacts']);
	$id_list = search_contacts_table($dbc, $search, $query.$sea_constraint." AND `category` LIKE 'Staff'");
	$query_clause = "WHERE `contactid` IN ($id_list) ";
}
// $favourites_clause = ' ORDER BY is_favourite DESC, `contactid`';

$match_query = '';
if($_GET['match_contact'] > 0) {
	$match_contacts = [];
	$match_contact_list = mysqli_query($dbc, "SELECT * FROM `match_contact` WHERE `deleted` = 0 AND CONCAT(',',`support_contact`,',') LIKE '%,".$_GET['match_contact'].",%'");
	while($match_contact = mysqli_fetch_assoc($match_contact_list)) {
		foreach(explode(',', $match_contact['staff_contact']) as $staff_contact) {
			if(!in_array($staff_contact, $match_contacts)) {
				$match_contacts[] = $staff_contact;
			}
		}
	}
	$match_contacts = implode(',',array_filter($match_contacts));
	$match_query .=  " AND `contactid` IN (".$match_contacts.")";
}

if(!empty(MATCH_CONTACTS)) {
	$match_query .= " AND `contactid` IN (".MATCH_CONTACTS.")";
}

$sql = "SELECT * FROM contacts $filter_clause $query_clause $sea_constraint $staff_cat_query $match_query";
$result = mysqli_query($dbc, $sql);
$sql_count = "SELECT COUNT(*) `numrows` FROM `contacts` $filter_clause $query_clause $sea_constraint $staff_cat_query $match_query";
$numrows = mysqli_fetch_array(mysqli_query($dbc, $sql_count));
$view_id = check_subtab_persmission($dbc, 'staff', ROLE, 'id_card');
?>
<!-- <form name="form_sites" method="post" action="staff.php?tab=<?php echo ($tab_list['active'] ? 'active' : 'suspended'); ?>&filter=All" class="form-inline" role="form">
	<div class="col-xs-12 col-sm-4 col-lg-2 pad-top" style="margin-top:7px;">
		<span class="popover-examples list-inline"><a style="margin:5px 5px 0 0;" data-toggle="tooltip" data-placement="top" title="This will search within the tab you have selected at the top."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<label for="search_contacts">Search Within Tab:</label>
	</div>
	<div class="col-sm-4 col-xs-12 col-lg-3 pad-top"><?php
		if ( isset ( $_POST[ 'search_contacts_submit' ] ) ) { ?>
			<input type="text" name="search_contacts" value="<?php echo $_POST['search_contacts']?>" class="form-control"><?php
		} else { ?>
			<input type="text" name="search_contacts" class="form-control"><?php
		} ?>
	</div>
	<div class="col-sm-4 col-xs-12 col-lg-3 pad-top pull-xs-right">
		<button type="submit" name="search_contacts_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
		<span class="popover-examples list-inline"><a style="margin:5px 0 0 15px;" data-toggle="tooltip" data-placement="top" title="Refreshes the page to display all contact information under the specific tab."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
		<a href="" name="display_all_contacts" value="Display All" class="btn brand-btn mobile-block">Display All</a>
	</div>
</form> -->
<div class="clearfix"></div>

<!--Staff List-->
<div>
	<?php //if ( $is_mobile === false ) { echo display_filter_param('staff.php?tab='.($tab_list['active'] ? 'active' : 'suspended')); }
	if($result->num_rows > 0):
		$db_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contacts_dashboard FROM field_config_contacts WHERE `tab`='Staff' AND contacts_dashboard IS NOT NULL"));
		$field_display = explode(",",$db_config['contacts_dashboard']);

		$contact_list = [];
		$contact_sort = [];
		$contact_list = array_merge($contact_list, mysqli_fetch_all($result, MYSQLI_ASSOC));
		$contact_sort = array_splice(sort_contacts_array($contact_list), $offset, $rowsPerPage);

		echo '<div class="pagination_links">';
		echo display_pagination($dbc, $sql_count, $pageNum, $rowsPerPage);
		echo '</div>';
		foreach($contact_sort as $id): ?>
			<?php $row = $contact_list[array_search($id, array_column($contact_list,'contactid'))]; ?>
			<div class="dashboard-item override-dashboard-item set-relative">
				<div class="col-sm-6">
					<img src="../img/person.PNG" class="inline-img"><?= ($security_access['edit'] > 0 || ($view_id && $security_access['visible'] > 0) ? "<a href='staff_edit.php?contactid=".$row['contactid']."&from_url=".rawurlencode($_SERVER['REQUEST_URI'])."'>" : '').decryptIt($row['first_name']).' '.decryptIt($row['last_name']).($security_access['edit'] > 0 ? '</a>' : '&nbsp;') ?>
					<?php if(!($security_access['edit'] > 0) && $row['contactid'] == $_SESSION['contactid']) { ?>
						<a href="<?= WEBSITE_URL ?>/Profile/my_profile.php?edit_contact=true&from_staff_tile=true" title="Edit My Profile"><img src="../img/icons/ROOK-edit-icon.png" class="inline-img"></a>
					<?php } ?>
				</div>
				<?php if(in_array('Employee ID', $field_display) && $row['contactid'] > 0): ?>
					<div class="col-sm-6">
						<img src="" class="inline-img"># <?php echo $row['contactid']; ?>
					</div>
				<?php endif; ?>
				<?php if(in_array('Employee #', $field_display) && $row['employee_num'] != ''): ?>
					<div class="col-sm-6">
						<img src="" class="inline-img"># <?php echo $row['employee_num']; ?>
					</div>
				<?php endif; ?>
				<?php if(in_array('License', $field_display) && $row['license'] != ''): ?>
					<div class="col-sm-6">
						<img src="" class="inline-img">Licence: <?php echo $row['license']; ?>
					</div>
				<?php endif; ?>
				<?php if(in_array('Category', $field_display) && $row['category_contact'] != ''): ?>
					<div class="col-sm-6">
						<img src="" class="inline-img">Category: <?php echo $row['category_contact']; ?>
					</div>
				<?php endif; ?>
				<?php if(in_array('Staff Category', $field_display) && $row['staff_category'] != ''): ?>
					<div class="col-sm-6">
						<img src="" class="inline-img">Staff Category: <?php echo $row['staff_category']; ?>
					</div>
				<?php endif; ?>
				<?php if(in_array('Login', $field_display) && $row['user_name'] != ''): ?>
					<div class="col-sm-6">
						<img src="" class="inline-img">Username: <?php echo $row['user_name']; ?>
					</div>
				<?php endif; ?>
				<?php if(in_array('Business', $field_display) && $row['businessid'] > 0): ?>
					<div class="col-sm-6">
						<img src="../img/business.PNG" class="inline-img"><?php echo get_contact($dbc, $row['businessid'], 'name'); ?>
					</div>
				<?php endif; ?>
				<?php if(in_array('Email', $field_display) && $row['email_address'] != ''): ?>
					<div class="col-sm-6">
						<a href="mailto:<?= decryptIt($row['email_address']) ?>"><img src="../img/email.PNG" class="inline-img"><?php echo decryptIt($row['email_address']); ?></a>
					</div>
				<?php endif; ?>
				<?php if(in_array('Company Email', $field_display) && $row['office_email'] != ''): ?>
					<div class="col-sm-6">
						<a href="mailto:<?= decryptIt($row['office_email']) ?>"><img src="../img/email.PNG" class="inline-img"><?php echo decryptIt($row['office_email']); ?></a>
					</div>
				<?php endif; ?>
				<?php $address = str_replace("<br>", ", ", get_address($dbc, $row['contactid']));
				if(in_array('Address', $field_display) && trim($address,', ') != ''): ?>
					<div class="col-sm-6">
						<img src="../img/address.PNG" class="inline-img"><?php echo rtrim(trim($address), ','); ?>
					</div>
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
				<?php if(in_array('Birthdate', $field_display) && $row['birth_date'] != '' && $row['birth_date'] != '0000-00-00'): ?>
					<div class="col-sm-6">
						<img src="../img/birthday.png" class="inline-img"><?= $row['birth_date'] ?><?= ( $row['birth_date']=='0000-00-00' || empty($row['birth_date']) ) ? '' : ' Age: '.date_diff(date_create($row['birth_date']), date_create('now'))->y ?>
					</div>
				<?php endif; ?>
				<?php if(in_array_any(['Office Phone','Home Phone','Cell Phone'],$field_display)) { ?>
					<div class="col-sm-6">
						<?php if($row['office_phone'] && in_array('Office Phone', $field_display)): ?>
							<img src="../img/office_phone.PNG" class="inline-img"><a href="tel:<?php echo decryptIt($row['office_phone']); ?>"><?php echo decryptIt($row['office_phone']); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php else: ?>
							<?php echo ""; ?>
						<?php endif; ?>
						<?php if($row['home_phone'] && in_array('Home Phone', $field_display)): ?>
							<img src="../img/home_phone.PNG" class="inline-img"><a href="tel:<?php echo decryptIt($row['home_phone']); ?>"><?php echo decryptIt($row['home_phone']); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php else: ?>
							<?php echo ""; ?>
						<?php endif; ?>
						<?php if($row['cell_phone'] && in_array('Cell Phone', $field_display)): ?>
							<img src="../img/cell_phone.PNG" class="inline-img"><a href="tel:<?php echo decryptIt($row['cell_phone']); ?>"><?php echo decryptIt($row['cell_phone']); ?></a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
						<?php else: ?>
							<?php echo ""; ?>
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

				<?php  
                $rc_view_access = tile_visible($dbc, 'rate_card');
                $rc_edit_access = vuaed_visible_function($dbc, 'rate_card');
                $rc_subtab_access = check_subtab_persmission($dbc, 'rate_card', ROLE, 'staff');
				if(($view_id && $security_access['visible'] > 0) || $security_access['edit'] > 0 || $security_access['archive'] > 0 || (in_array('Rate Card', $field_display) && check_dashboard_persmission($dbc, 'staff', ROLE, 'Staff Rate Card') && $rc_view_access > 0)) { ?>
					<div class="col-sm-6">
						<img src="../img/setting.PNG" class="inline-img">
						<?php $function_urls = [];
						if(($security_access['visible'] > 0 && $view_id) && !($security_access['edit'] > 0)) {
							$function_urls[] = '<a href="staff_edit.php?contactid='.$row['contactid'].'">View</a>';
						}
						if($security_access['edit'] > 0) {
							$function_urls[] = '<a href="staff_edit.php?status='.($row['status'] == 1 ? 'suspend' : 'activate').'&contactid='.$row['contactid'].'" onclick="return confirm(\'Are you sure you want to '.($row['status'] == 1 ? 'suspend' : 'activate').' this user?\');">'.($row['status'] == 0 ? 'Activate' : 'Deactivate').'</a>';
							$function_urls[] = '<a href="staff_edit.php?contactid='.$row['contactid']."&from_url=".rawurlencode($_SERVER['REQUEST_URI']).'">Edit</a>';
						}
						if($security_access['archive'] > 0) {
							$function_urls[] = '<a href="staff_edit.php?status=archive&contactid='.$row['contactid'].'" onclick="return confirm(\'Are you sure you want to archive this user?\');">Archive</a>';
						}
                        if(in_array('Rate Card', $field_display) && check_dashboard_persmission($dbc, 'staff', ROLE, 'Staff Rate Card') && $rc_view_access > 0) {
                        	$function_urls[] = '<a href="" onclick="overlayIFrameSlider(\'edit_staff_rate_card.php?id='.$row['contactid'].'&from_type=dashboard\', \'auto\', false, true, $(\'#staff_div\').height() + 20); return false;">View'.($rc_edit_access > 0 && $rc_subtab_access ? '/Edit': '').' Rate Card</a>';
                        }
                        echo implode(' | ', $function_urls);
                        ?>
					</div>
				<?php } ?>
				<div class="clearfix"></div>
                <div class="set-favourite">
					<?php if(strpos($row['is_favourite'],",".$_SESSION['contactid'].",") === FALSE && $tab != 'suspended'): ?>
						<a href="staff_edit.php?favourite=<?php echo $row['contactid']; ?>"><img src="../img/blank_favourite.png" alt="Favourite" title="Click to make the staff favourite" class="inline-img pull-right no-toggle"></a>
					<?php elseif($tab != 'suspended'): ?>
						<a href="staff_edit.php?unfavourite=<?php echo $row['contactid']; ?>"><img src="../img/full_favourite.png" alt="Favourite" title="Click to make the staff unfavourite" class="inline-img pull-right no-toggle"></a>
					<?php endif; ?>
                </div>
			</div>
		<?php endforeach; ?>
		<?php echo '<div class="pagination_links">';
		echo display_pagination($dbc, $sql_count, $pageNum, $rowsPerPage);
		echo '</div>'; ?>
	<?php else:
		echo "<h2>No Contacts found</h2>";
	endif; ?>
</div>
<script>$(document).ready(function() {
});
</script>