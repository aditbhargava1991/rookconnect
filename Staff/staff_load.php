<?php include_once ('../include.php');
ob_clean();

$db_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contacts_dashboard FROM field_config_contacts WHERE `tab`='Staff' AND contacts_dashboard IS NOT NULL"));
$field_display = explode(",",$db_config['contacts_dashboard']);
$security_access = get_security($dbc, 'staff');
$row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$_GET['contactid']."'")); ?>

<div class="dashboard-item override-dashboard-item set-relative">
	<div class="col-sm-6">
		<img src="../img/person.PNG" class="inline-img"><?= ($security_access['edit'] > 0 || ($view_id && $security_access['visible'] > 0) ? "<a href='staff_edit.php?contactid=".$row['contactid']."&from_url=".rawurlencode($_SERVER['HTTP_REFERER'])."'>" : '').decryptIt($row['first_name']).' '.decryptIt($row['last_name']).($security_access['edit'] > 0 ? '</a>' : '&nbsp;') ?>
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
	<?php if(in_array('Position', $field_display)):
		$position_name = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `name` FROM `positions` WHERE `position_id` = '{$row['position']}'"))['name'];
		if(!empty($position_name)) { ?>
			<div class="col-sm-6">
				<img src="../img/job.png" class="inline-img"><?php echo $position_name; ?>
			</div>
		<?php } ?>
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
				$function_urls[] = '<a href="staff_edit.php?contactid='.$row['contactid']."&from_url=".rawurlencode($_SERVER['HTTP_REFERER']).'">Edit</a>';
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