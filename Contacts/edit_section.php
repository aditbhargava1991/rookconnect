<?php include_once('../include.php');
$field_config_mandate = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name`='".FOLDER_NAME."' AND `tab`='$current_type' AND `subtab`='**no_subtab**' AND `mandatory` = 1"))[0]);
if(!isset($tab_label)) {
	error_reporting(0);
	$tab_label = $_POST['tab_label'];
	$tab_name = $_POST['tab_name'];
	$tab_data = [];
	if($tab_name == 'ALL_FIELDS') {
		$tab_data[0] = 'ALL_FIELDS';
		$tab_data[1] = [];
	}
	include_once('../Contacts/edit_fields.php');
	foreach($tab_list as $label => $data) {
		if($label == $tab_label) {
			$tab_data = $data;
		} else if($tab_name == 'ALL_FIELDS') {
			$tab_data[1] = array_merge($tab_data[1],$data[1]);
		}
	}
	$current_type = filter_var($_POST['type'],FILTER_SANITIZE_STRING);
	$folder = FOLDER_NAME;
	if(isset($_POST['folder'])) {
		$folder = filter_var($_POST['folder'],FILTER_SANITIZE_STRING);
	}
	$current_type = ($_POST['type'] != '' ? filter_var($_POST['type'],FILTER_SANITIZE_STRING) : explode(',',get_config($dbc,$folder.'_tabs'))[0]);
	$field_config = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name`='".$folder."' AND `tab`='$current_type' AND `subtab` = '**no_subtab**'"))[0] . ',' . mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name`='".$folder."' AND `tab`='$current_type' AND `subtab` = 'additions'"))[0]);
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
	}
	checkAuthorised($security_folder);
}
$contactid = $_GET['edit'];
$contact = [];
if($contactid > 0) {
	$contact = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` LEFT JOIN `contacts_cost` ON `contacts`.`contactid`=`contacts_cost`.`contactid` LEFT JOIN `contacts_dates` ON `contacts`.`contactid`=`contacts_dates`.`contactid` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` LEFT JOIN `contacts_medical` ON `contacts`.`contactid`=`contacts_medical`.`contactid` LEFT JOIN `contacts_upload` ON `contacts`.`contactid`=`contacts_upload`.`contactid` WHERE `contacts`.`contactid`='$contactid'"));
}
$security_levels = explode(',',trim(ROLE,','));
$subtabs_hidden = [];
$subtabs_viewonly = [];
$fields_hidden = [];
$fields_Viewonly = [];
$i = 0;
foreach($security_levels as $security_level) {
	if(tile_visible($dbc, $security_folder, $security_level)) {
		$security_config = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_security` WHERE `category`='$current_type' AND `security_level`='$security_level'"));
		if(!empty($security_config)) {
			if($i == 0) {
				$subtabs_hidden = explode(',',$security_config['subtabs_hidden']);
				$subtabs_viewonly = explode(',',$security_config['subtabs_viewonly']);
				$fields_hidden = explode(',',$security_config['fields_hidden']);
				$fields_viewonly = explode(',',$security_config['fields_viewonly']);
			} else {
				$subtabs_hidden = array_intersect(explode(',',$security_config['subtabs_hidden']), $subtabs_hidden);
				$subtabs_viewonly = array_intersect(explode(',',$security_config['subtabs_viewonly']), $subtabs_viewonly);
				$fields_hidden = array_intersect(explode(',',$security_config['fields_hidden']), $fields_hidden);
				$fields_viewonly = array_intersect(explode(',',$security_config['fields_viewonly']), $fields_viewonly);
			}
			$i++;
		}
	}
}
?>
<script>
$(document).ready(function() {
	$('[data-field]').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
	$('.viewonly_fields').each(function() { viewOnlyFields(this); });
});
function viewOnlyFields(div) {
	$(div).find('input,select,textarea,.select2,img,a,button,.sigPad,.mce-container').each(function() {
		$(this).prop('readonly', true);
		if($(this).get(0).tagName.toLowerCase() == 'textarea') {
			$(this).closest('.col-sm-8').css('pointer-events', 'none');
			$(this).closest('.col-sm-8').css('opacity', '0.5');
		} else {
			$(this).css('pointer-events', 'none');
			$(this).css('opacity', '0.5');
		}
	});
}
</script>
<form class="form-horizontal <?= in_array($tab_data[0], $subtabs_viewonly) || $_GET['read'] == 'only' ? 'viewonly_fields' : '' ?>">
    <?php if(!$hide_section_heading) { ?>
        <h4><?= ($tab_label=='Payment Information') ? 'Payment &amp; Billing Information' : $tab_label; ?></h4>
    <?php } ?>
	<?php if ($tab_data[0] == 'emergency_contacts' && in_array('Emergency Contact Multiple',$field_config)) {
		$multiple = count(explode('*#*', $contact['emergency_first_name']));
		$div = '<div class="emergency_contact_multiple">';
		$div_end = '</div>';
	} else if ($tab_data[0] == 'guardian_information' && in_array('Guardians Multiple',$field_config)) {
		$multiple = count(explode('*#*', $contact['guardians_first_name']));
		$div = '<div class="guardians_multiple">';
		$div_end = '</div>';
	} else if ($tab_data[0] == 'sibling_information' && in_array('Siblings Multiple',$field_config)) {
		$multiple = count(explode('*#*', $contact['siblings_first']));
		$div = '<div class="siblings_multiple">';
		$div_end = '</div>';
	} else if ($tab_data[0] == 'specialist' && in_array('Specialists First Name',$field_config)) {
		$multiple = count(explode('*#*', $contact['specialists_first_name']));
		$div = '<div class="specialists_multiple">';
		$div_end = '</div>';
	} else if ($tab_data[0] == 'doctors' && in_array('Family Doctor First Name',$field_config)) {
		$multiple = count(explode('*#*', $contact['family_doctor_first_name']));
		$div = '<div class="doctors_multiple">';
		$div_end = '</div>';
	} else if ($tab_data[0] == 'funding' && in_array('Multiple PDD Contacts',$field_config)) {
		$multiple = count(explode('*#*', $contact['pdd_key_contact']));
		$div = '<div class="pdd_contact_multiple">';
		$div_end = '</div>'; ?>
        <script>
            $(document).ready(function(){
                $('.pdd_contact_multiple:not(:first)').find('.clone_exception_block').closest('.form-group').remove();
            });
        </script><?php
	} else if ($tab_data[0] == 'wcb_information' && in_array('WCB Add Multiple',$field_config)) {
		$multiple = count(explode('*#*', $contact['wcb_claim_number']));
		$div = '<div class="wcb_multiple">';
		$div_end = '</div>';
	} else {
		$multiple = 1;
		$div = '';
		$div_end = '';
	}
	for ($counter = 0; $counter < $multiple; $counter++) {
		echo $div;
		foreach($field_config as $field_option) {
			if(in_array($field_option,$tab_data[1]) && !in_array($field_option, $fields_hidden)) {
				echo "<div class='form-group ".(in_array($field_option, $fields_viewonly) ? 'viewonly_fields' : '')."'>";
					include('../Contacts/edit_costs.php');
					include('../Contacts/edit_dates.php');
					include('../Contacts/edit_descriptions.php');
					include('../Contacts/edit_text_fields.php');
					include('../Contacts/edit_uploads.php');
					include('../Contacts/edit_tile_data.php');
                    include('../Contacts/edit_subtab_config.php');
					include('../Contacts/edit_addition_individual_support_plan.php');
					include('../Contacts/edit_addition_medications.php');
					include('../Contacts/edit_addition_marsheet.php');
					include('../Contacts/edit_addition_medical_charts.php');
					include('../Contacts/edit_addition_bowel_movement.php');
					include('../Contacts/edit_addition_water_temp_chart.php');
					include('../Contacts/edit_addition_blood_glucose_chart.php');
					include('../Contacts/edit_addition_seizure_record.php');
					include('../Contacts/edit_addition_water_temp_chart_bus.php');
					include('../Contacts/edit_addition_daily_fridge_temp.php');
					include('../Contacts/edit_addition_daily_freezer_temp.php');
					include('../Contacts/edit_addition_daily_dishwasher_temp.php');
					include('../Contacts/edit_addition_daily_log_notes.php');
					include('../Contacts/edit_addition_social_story.php');
					include('../Contacts/edit_addition_incident_reports.php');
					include('../Contacts/edit_addition_booking.php');
                    include('../Contacts/edit_addition_order_lists.php');
                    include('../Contacts/edit_addition_vendor_price_lists.php');
				echo "</div>";
			}
		}
		echo '<div class="clearfix"></div>';
		if(in_array('Business Sync To Site',$tab_data[1]) && in_array('Business Sync To Site',$field_config)) {
			echo '<div class="site_address" style="'.($contact['business_site_sync'] > 0 ? '' : 'display:none;').'"></div><div class="clearfix"></div>';
		}
		if(in_array('Mailing Sync To Site',$tab_data[1]) && in_array('Mailing Sync To Site',$field_config)) {
			echo '<div class="site_address" style="'.($contact['mailing_site_sync'] > 0 ? '' : 'display:none;').'"></div><div class="clearfix"></div>';
		}
		if(in_array('Address Sync To Site',$tab_data[1]) && in_array('Address Sync To Site',$field_config)) {
			echo '<div class="site_address" style="'.($contact['address_site_sync'] > 0 ? '' : 'display:none;').'"></div><div class="clearfix"></div>';
			echo '<div class="add_another_site pull-right" style="display:none;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-add-icon.png" title="Add Another Site" onclick="addAnotherSite();"></div><div class="clearfix"></div>';
		}
		echo $div_end;
	}
    if( $tab_label=='Alerts' ) { ?>
        <label class="col-sm-4 control-label"></label>
        <div class="col-sm-8">
			<button class="btn brand-btn pull-right" onclick="send_alert(this); return false;">Send Alert</button>
			<input type="text" value="" name="alert_schedule_date" onchange="if(this.value != '' && confirm('Are you sure you want to schedule an alert for this contact on '+this.value+'?')) { send_alert(this,this.value); }" class="pull-right datepicker" style="width:0;border:0;">
			<button class="btn brand-btn pull-right" onclick="$(this).prevAll('[name=alert_schedule_date]').focus(); return false;">Schedule Alert</button>
		</div>
        <br clear="all" /><br /><?php
    } ?>
</form>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_section.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>
<script>
$(document).ready(function() {
	window.onbeforeunload = function() {
		$('.required').each(function() {
				var target = this;
					if($(target).val() != null && $(target).val().length === 0) {
						if($(target).is('select')) {
							var select2 = $(target).next('.select2');
							$(select2).find('.select2-selection').css('background-color', 'red');
							$(select2).find('.select2-selection__placeholder').css('color', 'white');
						} else {
							$(target).css('background-color', 'red');
						}

						setTimeout(function() {
								alert("Please fill in the "+$(target).closest('.form-group').find('label').text().split("\n")[0].replace(/^[^a-zA-Z0-9()]*/g,'').replace(/[^a-zA-Z0-9()]*$/g,'')+".");
								$(target).focus();
						}, 0);
				}
		});

	}
});
</script>
