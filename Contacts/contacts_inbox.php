<?php // Contacts View

error_reporting(0);
include_once('../include.php'); ?>
</head>
<body>
<?php
$folder_name = FOLDER_NAME;
if($_GET['edit'] > 0) {
	$tile = get_contact($dbc, $_GET['edit'], 'tile_name');
	if($tile != $folder_name) {
		$folder_name = $tile;
	}
}
$security_folder = $folder_name;
$folder_label = FOLDER_URL;
if($security_folder == 'clientinfo') {
	$security_folder = 'client_info';
	$folder_label = 'Client Information';
} else if($security_folder == 'contactsrolodex') {
	$security_folder = 'contacts_rolodex';
	$folder_label = 'Contacts Rolodex';
} else if($security_folder == 'contacts') {
	$folder_label = CONTACTS_TILE;
	$security_folder = 'contacts_inbox';
} else if($security_folder == 'contacts3') {
	$folder_label = "Contacts";
	$security_folder = 'contacts_inbox';
} else if($security_folder == 'vendors') {
	$folder_label = VENDOR_TILE;
	$security_folder = 'vendors';
}
checkAuthorised($security_folder);
$view_access = tile_visible($dbc, $security_folder);
$edit_access = vuaed_visible_function($dbc, $security_folder);
$config_access = config_visible_function($dbc, $security_folder);
include_once ('../navigation.php');

$breadcrumbs = '';
if(empty($_GET['settings'])) {
	if(!empty($_GET['edit']) && $_GET['edit'] > 0) {
		$contact_category = get_contact($dbc, $_GET['edit'], 'category');
		$breadcrumbs .= ': <a href="?list='.$contact_category.'&status=active">'.$contact_category.'</a>';
		$contact_name = array_shift(sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".$_GET['edit']."'")))['full_name'];
		$breadcrumbs .= ': <a href="?edit='.$_GET['edit'].'" class="breadcrumb_contact">'.$contact_name.'</a>';
	} else if($_GET['edit'] == 'new') {
		if(!empty($_GET['category'])) {
			$breadcrumbs .= ': <a href="?list='.$_GET['category'].'&status=active">'.$_GET['category'].'</a>';
		}
		$breadcrumbs .= ': <a href="?edit=new" class="breadcrumb_contact">New '.(!empty($_GET['category']) ? $_GET['category'] : ($folder_label != 'Contacts' ? $folder_label : CONTACTS_NOUN)).'</a>';
	}
}
?>
<?php if(!IFRAME_PAGE) { ?>
	<div class="container">
		<div id="dialog_copy_contact" title="Choose a Category" style="display: none;">
			<div class="form-group">
				<label class="col-sm-4 control-label">Contact Category:</label>
				<div class="col-sm-8">
					<select name="copy_contact_category" data-placeholder="Select a Category..." class="chosen-select-deselect">
						<option></option>
						<?php $each_tab = explode(',',get_config($dbc, $folder_name.'_tabs'));
						foreach ($each_tab as $cat_tab) {
							echo "<option value='". $cat_tab."'>".$cat_tab.'</option>';
						} ?>
					</select>
				</div>
			</div>
		</div>
		<div id="dialog_email_credentials" title="Email Credentials" style="display:none;">
			<?php $email_creds_subjectbject = 'Login Credentials for '.WEBSITE_URL;
				$email_body = 'Your login credentials are:<br>
					<ul>
					<li>Username: [USERNAME]</li>
					<li>Password: [PASSWORD]</li>
					</ul>
					Please use these credentials to log in to '.WEBSITE_URL.'.'; ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Sender's Name:</label>
				<div class="col-sm-8">
					<input type="text" name="email_creds_sender_name" class="form-control" value="<?= get_contact($dbc, $_SESSION['contactid']) ?>">
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Sender's Email:</label>
				<div class="col-sm-8">
					<input type="text" name="email_creds_sender" class="form-control" value="<?= get_email($dbc, $_SESSION['contactid']) ?>">
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Recipient's Email:</label>
				<div class="col-sm-8">
					<input type="text" name="email_creds_recipient" class="form-control" value="">
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Subject:</label>
				<div class="col-sm-8">
					<input type="text" name="email_creds_subject" class="form-control" value="<?= $email_subject ?>">
				</div>
				<div class="clearfix"></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Email Body:</label>
				<div class="col-sm-8">
					<textarea name="email_creds_body" class="form-control"><?= $email_body ?></textarea>
				</div>
				<div class="clearfix"></div>
			</div>
		</div>

		<div id="dialog_contact_reminders" title="Add Reminder" style="display:none;">
            <div class="form-group recipient" style="display:none;">
				<label class="col-sm-4 control-label">Recipient:</label>
				<div class="col-sm-8"><select name="contact_reminder_staff" data-placeholder="Select Staff" class="chosen-select-deselect" onchange="$(this).closest('#dialog_contact_reminders').find('[name=contact_reminder_staff]').last().val(this.value);"><option />
					<?php foreach(sort_contacts_query($dbc->query("SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY."")) as $staff) { ?>
						<option value="<?= $staff['contactid'] ?>"><?= $staff['full_name'] ?></option>
					<?php } ?>
				</select></div>
			</div>
            <div class="form-group">
				<label class="col-sm-4 control-label">Reminder:</label>
				<div class="col-sm-8"><input type="text" name="contact_reminder_subject" class="form-control" value=""></div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Reminder Date:</label>
				<div class="col-sm-8"><input type="text" name="contact_reminder_date" class="form-control datepicker" value=""></div>
			</div>
			<input type="hidden" name="contact_reminder_staff" class="form-control" value="<?= $_SESSION['contactid'] ?>">
			<input type="hidden" name="contact_reminder_contactid" class="form-control" value="<?= $_GET['edit'] ?>">
            <input type="hidden" name="contact_reminder_folder" class="form-control" value="<?= $folder_name ?>">
		</div><!-- #dialog_contact_reminders -->

		<div class="iframe_overlay" style="display:none; margin-top:-20px; padding-bottom:20px;">
			<div class="iframe">
				<div class="iframe_loading">Loading...</div>
				<iframe name="contacts_iframe" src=""></iframe>
			</div>
		</div>
		<div class="iframe_holder" style="display:none;">
			<img src="<?php echo WEBSITE_URL; ?>/img/icons/close.png" class="close_iframer" width="45px" style="position:relative; right:10px; float:right; top:58px; cursor:pointer;">
			<span class="iframe_title" style="color:white; font-weight:bold; position:relative; top:58px; left:20px; font-size:30px;"></span>
			<iframe id="iframe_instead_of_window" style="width:100%; overflow:hidden; height:200px; border:0;" src=""></iframe>
		</div>
		<div class="row hide_on_iframe icons_div" data-id="<?= $_GET['edit'] ?>">
			<div class="main-screen" style="background-color: #fff; border-width: 0; height: auto; margin-top: -20px;">
				<h1 class="no-gap-top padded"><a href="<?= !empty($_GET['from']) ? urldecode(strpos($_GET['from'],'list_common') !== FALSE ? '?'.explode('?',$_GET['from'])[1] : $_GET['from']) : '?' ?>"><?= $folder_label ?></a><?= $breadcrumbs ?><?php if($config_access > 0) {
					echo "<div class='pull-right'><a href='?settings=fields'><img src='".WEBSITE_URL."/img/icons/settings-4.png' class='settings-classic wiggle-me settings-icon'></a></div>";
				} ?>
				<?php
				if($edit_access > 0) {
					echo "<div class='pull-right' style='height: 1em; padding: 0 0.25em;'><a href='?edit=new' style='font-size: 0.5em;'><button class='btn brand-btn hide-titles-mob'>New ".($folder_label != 'Contacts' ? $folder_label : CONTACTS_NOUN)."</button>";
					echo "<img src='".WEBSITE_URL."/img/icons/ROOK-add-icon.png' class='show-on-mob add-icon-lg'></a></div>";
					if($_GET['category']=='Customers' && $_GET['edit']!=''){
						echo "<div class='pull-right'><a href='Add Reminder' onclick='return false;'><img src='".WEBSITE_URL."/img/icons/ROOK-reminder-icon.png' class='no-toggle reminder-icon' title='Schedule Reminder' style='width:1.5em;'></a></div>";
					}
				} ?>
				<!--
                <?php /* if($view_access > 0) { ?>
				<div class="pull-right col-sm-6 col-xs-12">
					<!--<form action="contacts_inbox.php" method="GET">-->
						<span class="pull-left col-xs-8">
							<?php if($_GET['search_contacts']): ?>
								<input name="search_contacts" style="background-color:#ebebeb" type="text" value="<?php echo base64_decode($_GET['search_contacts']); ?>" class="form-control form-control-silver"/>
							<?php else: ?>
								<input name="search_contacts" type="text" value="" placeholder="Search contacts" class="form-control form-control-silver"/>
							<?php endif; ?>
						</span>
						<span class="col-xs-4">
							<input type="submit" onclick="searchContact()" value="Filter" class="btn brand-btn" name="search_contacts_submit">
						</span>
					<!--</form>--
				</div>
				<?php } */ ?>
                -->
				<img class="no-toggle statusIcon pull-right no-margin inline-img" title="" src="" />
				</h1>
				<div class="clearfix"></div>
<?php } ?>
			<?php if(($_GET['edit'] > 0 && $view_access > 0 && (empty(MATCH_CONTACTS) || in_array($_GET['edit'],explode(',',MATCH_CONTACTS)))) || ($_GET['edit'] == $_SESSION['contactid']) || ($_GET['edit'] == 'new' && $edit_access > 0)) {
				include('../Contacts/edit_contact.php');
			} else if(!empty($_GET['settings']) && $config_access > 0) {
				include('../Contacts/field_config.php');
			} else if($view_access > 0) {
				include('../Contacts/list_contacts.php');
			} else {
				echo '<h3>You do not have access to view Contacts.</h3>';
			} ?>
<?php if(!IFRAME_PAGE) { ?>
			</div>
		</div>
	</div>
<?php } ?>
<div class="clearfix"></div>
<script>
function searchContact()
{
		var search_parameter = btoa($("input[name=search_contacts]").val());
		var search_url = updateQueryStringParameter(window.location.href, "search_contacts", search_parameter);
		window.location.href = search_url;
}
$(document).ready(function() {
	$('.icons_div .reminder-icon').off('click').click(function() {
        var item = $(this).closest('.icons_div');
        overlayIFrameSlider('<?= WEBSITE_URL ?>/quick_action_reminders.php?tile=contacts&id='+item.data('id'), 'auto', false, true);
    });
})
</script>
<?php include('../footer.php'); ?>
