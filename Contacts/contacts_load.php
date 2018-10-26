<?php include_once ('../include.php');
ob_clean();

$folder_name = $_POST['folder'];
$security_folder = $folder_name;
if($security_folder == 'clientinfo') {
	$security_folder = 'client_info';
} else if($security_folder == 'contactsrolodex') {
	$security_folder = 'contacts_rolodex';
} else if($security_folder == 'contacts') {
	$security_folder = 'contacts_inbox';
} else if($security_folder == 'contacts3') {
	$security_folder = 'contacts_inbox';
} else if($security_folder == 'vendors') {
	$security_folder = 'vendors';
}
checkAuthorised($security_folder);
$view_access = tile_visible($dbc, $security_folder);
$edit_access = vuaed_visible_function($dbc, $security_folder);
$config_access = config_visible_function($dbc, $security_folder);

$row = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$_GET['contactid']."'"));
$bg_color = '';
if($row['flag_label'] == '' && $row['flag_colour'] != '') {
    $bg_color = "background-color: #".$row['flag_colour'];
}
$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT contacts_dashboard FROM field_config_contacts WHERE tile_name = '".$folder_name."' AND tab='".$row['category']."' AND accordion IS NULL UNION SELECT contacts_dashboard FROM `field_config_contacts` WHERE tile_name='".$folder_name."'"));
$field_display = explode(",",$get_field_config['contacts_dashboard']); ?>

<div class="dashboard-item set-relative" style="<?= $bg_color ?>">
	<?php if($row['flag_label'] != '') { ?>
        <span class="block-label flag-label-block" style="font-weight: bold; background-color: <?php echo '#'.$row['flag_colour']; ?>">Flagged: <?= $row['flag_label'] ?></span>
	<?php } ?>

    <?php if(!empty($_GET['search_contacts']) || !empty($_POST['search_'.$category])) { ?>
		<div class="col-sm-6">
			<?php echo '<b>'.$row['category'].'</b>'; ?>
		</div>
    <?php } ?>
	<div class="col-sm-6">
		<img src="../img/person.PNG" class="inline-img dashboard-icon"><?= '<a href=\'?category='.$row['category'].'&edit='.$row['contactid'].'\'>'.($row['category'] == 'Business' ? decryptIt($row['name']) : ($row['category'] == 'Sites' ? ($row['display_name'] != '' ? $row['display_name'] : $row['site_name']) : ($row['name'] != '' ? decryptIt($row['name']).': ' : '').decryptIt($row['first_name']) . ' ' . decryptIt($row['last_name']))).'</a>' ?>
	</div>
	<?php if(in_array('Business', $field_display) && $row['businessid'] > 0): ?>
		<div class="col-sm-6">
			<img src="../img/business.PNG" class="inline-img dashboard-icon"><?php echo get_contact($dbc, $row['businessid'], 'name'); ?>
		</div>
	<?php endif; ?>
	<?php if(in_array('Email Address', $field_display)): ?>
		<div class="col-sm-6">
			<a href="mailto:<?= decryptIt($row['email_address']) ?>"><img src="../img/email.PNG" class="inline-img dashboard-icon"><?= decryptIt($row['email_address']) ?></a>
		</div>
	<?php endif; ?>
	<?php if(in_array('Site', $field_display)): ?>
		<div class="col-sm-6">
			<img src="../img/project-path.png" class="inline-img dashboard-icon"><?= $row['site_name'] ?>
		</div>
	<?php endif; ?>
	<?php if(in_array('Address', $field_display)): ?>
		<div class="col-sm-6">
			<?php $address = ($row['business_address'] ?: ($row['address'] ?: ($row['mailing_address'] ?: ($row['ship_to_address'] ?: get_address($dbc, $row['businessid'])))));
			$address = str_replace("<br>", ", ", $address); ?>
			<a class="show-on-mob" href="maps:<?= trim($address,', ') ?>"><img src="../img/address.PNG" class="inline-img dashboard-icon"><?php echo rtrim(trim($address), ','); ?></a>
			<a class="hide-on-mobile" href="https://maps.google.com/maps/place/<?= trim($address,', ') ?>"><img src="../img/address.PNG" class="inline-img dashboard-icon"><?php echo rtrim(trim($address), ','); ?></a>
		</div>
		<?php if($row['google_maps_address'].$row['ship_google_link'] != ''): ?>
			<div class="col-sm-6">
				<a href="<?= $row['google_maps_address'] ?: $row['ship_google_link'] ?>"><img src="../img/address.PNG" class="inline-img dashboard-icon">Google Maps</a>
			</div>
		<?php endif; ?>
	<?php endif; ?>
	<?php if(in_array('Pronoun', $field_display)): ?>
		<div class="col-sm-6">
			<img src="../img/gender.png" class="inline-img dashboard-icon"><?php switch($row['preferred_pronoun']) {
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
			<img src="../img/birthday.png" class="inline-img dashboard-icon"><?= $row['birth_date'] ?><?= ( $row['birth_date']=='0000-00-00' || empty($row['birth_date']) ) ? '' : ' Age: '.date_diff(date_create($row['birth_date']), date_create('now'))->y ?>
		</div>
	<?php endif; ?>
	<?php if(in_array_any(['Office Phone','Home Phone','Cell Phone'],$field_display)) { ?>
		<div class="col-sm-6">
			<?php if($row['office_phone'] && in_array('Office Phone', $field_display)): ?>
				<a href="tel:<?= decryptIt($row['office_phone']) ?>"><img src="../img/office_phone.PNG" class="inline-img dashboard-icon"><?= decryptIt($row['office_phone']); ?></a>
			<?php endif; ?>
			<?php if($row['home_phone'] && in_array('Home Phone', $field_display)): ?>
				<a href="tel:<?= decryptIt($row['home_phone']) ?>"><img src="../img/home_phone.PNG" class="inline-img dashboard-icon"><?= decryptIt($row['home_phone']); ?></a>
			<?php endif; ?>
			<?php if($row['cell_phone'] && in_array('Cell Phone', $field_display)): ?>
				<a href="tel:<?= decryptIt($row['cell_phone']) ?>"><img src="../img/cell_phone.PNG" class="inline-img dashboard-icon"><?= decryptIt($row['cell_phone']); ?></a>
			<?php endif; ?>
		</div>
	<?php } ?>
	<?php if(in_array('Social', $field_display)) { ?>
		<div class="col-sm-6">
			<?php if($row['linkedin'] != '') { ?><a href="<?= $row['linkedin'] ?>"><img src="../img/icons/social/linkedin.png" class="inline-img dashboard-icon" /> LinkedIn</a><?php } ?>
			<?php if($row['facebook'] != '') { ?><a href="<?= $row['facebook'] ?>"><img src="../img/icons/social/facebook.png" class="inline-img dashboard-icon" /> Facebook</a><?php } ?>
			<?php if($row['twitter'] != '') { ?><a href="<?= $row['twitter'] ?>"><img src="../img/icons/social/twitter.png" class="inline-img dashboard-icon" /> Twitter</a><?php } ?>
			<?php if($row['google_plus'] != '') { ?><a href="<?= $row['google_plus'] ?>"><img src="../img/icons/social/google+.png" class="inline-img dashboard-icon" /> Google+</a><?php } ?>
			<?php if($row['instagram'] != '') { ?><a href="<?= $row['instagram'] ?>"><img src="../img/icons/social/instagram.png" class="inline-img dashboard-icon" /> Instagram</a><?php } ?>
			<?php if($row['pinterest'] != '') { ?><a href="<?= $row['pinterest'] ?>"><img src="../img/icons/social/pinterest.png" class="inline-img dashboard-icon" /> Pinterest</a><?php } ?>
			<?php if($row['youtube'] != '') { ?><a href="<?= $row['youtube'] ?>"><img src="../img/icons/social/youtube.png" class="inline-img dashboard-icon" /> YouTube</a><?php } ?>
			<?php if($row['blog'] != '') { ?><a href="<?= $row['blog'] ?>"><img src="../img/icons/social/rss.png" class="inline-img dashboard-icon" /> Blog</a><?php } ?>
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
			echo '<a href="?category='.$row['category'].'&edit='.$row['contactid'].'">Edit</a> | ';
			echo '<a href="" onclick="deleteContact(this); return false;" data-contactid="'.$row['contactid'].'">Archive</a>';
		} else {
			echo '<a href="?category='.$row['category'].'&edit='.$row['contactid'].'">View</a>';
		} ?>
	</div>
    <div class="clearfix"></div>
    <?php
    echo '<span class="action-icons gap-top" style="width: 40%;" data-task="'.$row['contactid'].'">';
    $quick_actions = explode(',',get_config($dbc, 'contact_quick_action_icons'));

    echo in_array('flag_manual', $quick_actions) ? '<span title="Flag This!" onclick="flag_item_manual(this); return false;"><img title="Flag This!" src="../img/icons/ROOK-flag-icon.png" class="inline-img no-toggle" onclick="return false;"></span>' : '';
    echo in_array('flag', $quick_actions) ? '<span title="Highlight" onclick="highlight_item(this); return false;"><img src="../img/icons/color-wheel.png" class="inline-img no-toggle" title="Highlight" onclick="return false;"></span>' : '';
    ?>
    <input type="color" class="color_picker" onchange="choose_color(this); return false;" id="color_<?=$row['contactid']?>" data-taskid="<?=$row['contactid']?>" name="color_<?=$row['contactid']?>" style="display:none;" />
    <?php
    echo '</span>';

    ?>
	<div class="clearfix"></div>
    <div class="set-favourite">
		<?php if(strpos($row['is_favourite'],",".$_SESSION['contactid'].",") === FALSE): ?>
			<a href="?list=<?php echo $list; ?>&favourite=<?php echo $row['contactid']; ?>"><img src="../img/blank_favourite.png" alt="Favourite" title="Click to make the contact favourite" class="inline-img pull-right small no-toggle"></a>
		<?php else: ?>
			<a href="?list=<?php echo $list; ?>&unfavourite=<?php echo $row['contactid']; ?>"><img src="../img/full_favourite.png" alt="Favourite" title="Click to make the contact unfavourite" class="inline-img pull-right small no-toggle"></a>
		<?php endif; ?>
    </div>
</div>