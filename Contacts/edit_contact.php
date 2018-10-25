<?php $contactid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
if($_GET['edit'] == $_SESSION['contactid']) {
	$contact_category = get_contact($dbc, $_SESSION['contactid'], 'category');
	$profile_access = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_contacts_security` WHERE `category` = '$contact_category' AND `security_level` = '".ROLE."'"))['profile_access'];
	if($profile_access == 'enable') {
		$edit_access = 1;
	}
}
if($_GET['category'] == '%') {
	$_GET['category'] = '';
}
$current_type = ($contactid > 0 ? get_contact($dbc, $contactid, 'category') : ($_GET['category'] != '' ? $_GET['category'] : ''));
$_GET['category'] = $current_type;
$field_config = [];
if(isset($mandatory_config)) {
	$field_config = $mandatory_config;
} else {
	$field_config = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name`='".$folder_name."' AND `tab`='$current_type' AND `subtab` = '**no_subtab**'"))[0] . ',' . mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name`='".$folder_name."' AND `tab`='$current_type' AND `subtab` = 'additions'"))[0]);
}
if(IFRAME_PAGE && !isset($mandatory_config)) {
	$slider_layout = !empty(get_config($dbc, $folder_name.'_slider_layout')) ? get_config($dbc, $folder_name.'_slider_layout') : 'accordion';
}
$security_levels = explode(',',trim(ROLE,','));
$subtabs_hidden = [];
$subtabs_viewonly = [];
$fields_hidden = [];
$fields_viewonly = [];
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
$contact_subtabs = array_filter(explode(',',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts_subtab` WHERE `contactid` = '$contactid'"))['subtabs']));

//Regions/Locations/Classifications & Contact Security
$contact_regions = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
$contact_locations = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
$contact_classifications = [];
$class_regions = explode(',',get_config($dbc, $folder_name.'_class_regions'));
foreach(explode(',',get_config($dbc, $folder_name.'_classification', true, ',')) as $i => $contact_classification) {
	$contact_classifications[] = $contact_classification;
	$classification_regions[] = $class_regions[$i];
}
$contact_security = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts_security` WHERE `contactid`='$contactid'"));
$sales_lead_id = 0;
if(in_array($current_type,['Sales Lead','Sales Leads'])) {
    $id_card_fields[] = 'Sales Lead';
    $field_config[] = 'Sales Lead Details';
}
if(in_array($current_type, explode(',',get_config($dbc, 'lead_all_contact_cat').',Sales Lead,Sales Leads,'))) {
    $sales_lead_id = $dbc->query("SELECT `salesid` FROM `sales` WHERE `deleted`=0 AND CONCAT(',',`contactid`,',') LIKE '%,".$contactid.",%'")->fetch_assoc()['salesid'];
    if($sales_lead_id > 0) { ?>
        <script src="../Sales/edit.js"></script>
    <?php }
}
include_once('../Contacts/edit_fields.php');
// if(in_array_starts('acc_',$field_config)) {
	// foreach($tab_list as $tab_data) {
		// $field_config[] = 'acc_'.$tab_data[1];
	// }
// } ?>
<script>
var profile_tab = [];
var lock_timeout = null;
$(document).ready(function() {
	$('.panel-heading:not(.no_load)').on('touchstart',loadPanel).click(loadPanel);
	$(window).resize(resizeScreen).resize();
	lockTabs();
	if($('footer').length > 0) {
		scrollScreen();
	}
	$('.standard-dashboard-body').scroll(scrollScreen);
	$('[data-field]').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
	if($('[name=sync_mail_address]').is(':checked')) {
		$('[name^=ship_]').attr('readonly',true);
	}
	if($('[name=sync_payment_address]').is(':checked')) {
		$('[name=payment_address],[name=payment_city],[name=payment_state],[name=payment_postal_code],[name=payment_zip_code]').attr('readonly',true);
	}
	loadSite();
});
var current_fields = [];
var site_ids = [];
<?php if($contactid > 0) {
	$main_siteid = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `main_siteid` FROM `contacts` WHERE `contactid` = '$contactid'"))['main_siteid'];
	$site_query = mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `category`='".SITES_CAT."' AND `businessid`='$contactid' AND `deleted` = 0 AND `contactid` = '".$main_siteid."' UNION SELECT `contactid` FROM `contacts` WHERE `category`='".SITES_CAT."' AND `businessid`='$contactid' AND `deleted` = 0 AND `contactid` != '".$main_siteid."'");
	while($site_result = mysqli_fetch_assoc($site_query)) { ?>
		site_ids.push('<?= $site_result['contactid'] ?>');
	<?php }
} ?>
function jumpTab(tab_name, sub_class) {
	edit_profile();
	if(sub_class == undefined) {
		sub_class = '';
	}
	$('.standard-dashboard-body').last().scrollTop($('[data-tab-name='+tab_name+']'+sub_class).last().offset().top + $('.standard-dashboard-body').last().scrollTop() - $('.standard-dashboard-body').last().offset().top);
	scrollScreen();
}
function resizeScreen() {
	// $('body>.container').css('margin-bottom','-5em');
	$('body>.container .main-screen').css('padding-bottom','0');
	if($('footer').length > 0) {
		var diff = Math.round($(window).height() - $('#footer').offset().top - $('#footer').height()) - 10;
		if($('.tile-sidebar').outerHeight() + diff > 0) {
			$('.tile-sidebar').outerHeight($('.tile-sidebar').outerHeight() + diff);
		}
		$('.tile-sidebar').outerHeight($('#footer').offset().top - $('.tile-sidebar').offset().top);
		$('.main-screen .has-main-screen .main-screen').outerHeight($('#footer').offset().top - $('.main-screen .has-main-screen').offset().top);
		$('.main-screen .has-main-screen').outerHeight('auto').css('overflow-y','hidden');
	} else {
		$('.main-screen').height('auto');
	}
}
function loadPanel() {
	$(this).closest('.panel').find('.panel-body').each(function() {
		if ($(this).parentsUntil($(this),'#view_checklist').length == 0 && $(this).parentsUntil($(this),'#collapse_profile').length == 0) {
			$(this).html('Loading...');
			var panel = this;
			if($(this).data('url') != undefined && $(this).data('url') != '') {
				$.ajax({
					url: '../Contacts/'+$(this).data('url'),
					method: 'POST',
					response: 'html',
					success: function(response) {
						$(panel).html(response);
					}
				});
			} else {
				$.ajax({
					url: '../Contacts/edit_section.php?edit=<?= $_GET['edit'] ?>',
					data: { folder: '<?= $folder_name ?>', type: $('[name=category]').val(), tab_label: $(panel).data('tab-label'), tab_name: $(panel).data('tab-name') },
					method: 'POST',
					response: 'html',
					success: function(response) {
						$('div[data-locked=held]').each(function() {
							releaseLock([$(this).data('tab-name')]);
						});
						$(panel).html(response);
						getLock([$(panel).data('tab-name')]);
						$('[data-field]').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
					}
				});
			}
		} else if ($(this).parentsUntil($(this),'#collapse_profile').length == 1) {
            $('#collapse_profile button').hide();
            $('#collapse_profile .col-sm-6').css('font-size', '0.8em');
            $('#collapse_profile ul li:last-child').css('border-bottom', '1px solid #ddd');
        }
	});
}
// Add a row to a table to be attached to the prior row delimited
function addDelimitedRow(table) {
	destroyInputs(table);
	var tr = table.find('tr').last().clone();
	tr.find('input,select').val('');
	table.append(tr);
	initInputs('#'+table.attr('id'));
	table.find('[data-field]').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
}
function scrollScreen() {
	var current_tab = [];
	$('[data-tab-name]:visible').each(function() {
		if(this.getBoundingClientRect().top != undefined && $('.standard-dashboard-body:visible').offset() != undefined && this.getBoundingClientRect().top < $('.standard-dashboard-body:visible').offset().top + $('.standard-dashboard-body:visible').height() &&
			this.getBoundingClientRect().bottom > $('.standard-dashboard-body:visible').offset().top) {
			current_tab.push($(this).data('tab-name'));
		}
	});
	if(JSON.stringify(current_tab) != JSON.stringify(profile_tab)) {
		profile_tab.forEach(function(tab_name) {
			if(!current_tab.includes(tab_name)) {
				releaseLock([tab_name]);
			}
		});
		profile_tab = current_tab;
		getLock(profile_tab);
	}
}
var first_site = true;
var still_loading_site = false;
var load_site_list = [];
function loadSite() {
	$('.site_address:not(:first)').remove();
	first_site = true;
	still_loading_site = false;
	load_site_list = [];
	site_ids.forEach(function(site_id) {
		if(site_id > 0) {
			if(still_loading_site) {
				load_site_list.push(function() { loadSiteHtml(site_id); });
			} else {
				still_loading_site = true;
				loadSiteHtml(site_id);
			}
		}
	});
	if(site_ids.length > 0) {
		$('.add_another_site').show();
	} else {
		$('.add_another_site').hide();
	}
}
function loadSiteHtml(site_id) {
	$.ajax({
		url: '../Contacts/edit_section.php?edit='+site_id,
		method: 'POST',
		data: {
			folder: '<?= $folder_name ?>',
			type: '<?= SITES_CAT ?>',
			tab_label: 'Site Address',
			tab_name: 'ALL_FIELDS'
		},
		success: function(response) {
			if(!first_site) {
				var clone = $('.site_address').first().clone();
				clone.data('contactid', site_id);
				clone.data('nosync', 1);
				clone.html(response);
				$('.site_address').last().after(clone);
			} else {
				<?php if(in_array('Synced Site Hide Address',$field_config)) { ?>
					if($('[name="address_site_sync"]').is(':checked')) {
						$('[data-tab-name="address"] .form-group').hide();
						$('[data-tab-name="address"] [name="address_site_sync"]').closest('.form-group').show();
					}
				<?php } ?>
				$('.site_address').html(response);
				$('.site_address').data('contactid', site_id);
				setMainSite(site_id);
			}
			first_site = false;
			still_loading_site = false;
			if(load_site_list.length > 0) {
				load_site_list.shift()();
			}
		}
	});
}
function setMainSite(site_id) {
	var contactid = $('[name=contactid]').val();
	$.ajax({
		type: 'POST',
		url: '../Contacts/contacts_ajax.php?action=set_main_site',
		data: {
			contactid: contactid,
			site_id: site_id
		},
		success: function(response) {
		}
	});
}
function saveFieldMethod(field) {
    if(['sales','sales_document','sales_notes'].indexOf($(field).data('table')) >= 0) {
        saveSalesMethod(field);
        exit();
    }
    if(($('[name=contactid]').val() == 'new' || $('[name=contactid]').val() == '' || $('[name=contactid]').val() == undefined) && field.name == 'category') {
    	<?php if(IFRAME_PAGE) { ?>
			window.parent.$('.iframe_overlay iframe').off('load');
		<?php } ?>
		window.location.replace('?<?= IFRAME_PAGE ? 'mode=iframe&' : '' ?><?= $_GET['change'] == 'true' ? 'change=true&' : '' ?>profile=false&businessid=<?= $_GET['businessid'] ?>&category='+field.value+'&edit='+ $('[name=contactid]').val());
		return;
	} else if(($('[name=contactid]').val() == 'new' || $('[name=contactid]').val() == '' || $('[name=contactid]').val() == undefined) && field.name != 'category' && window.previous_field == undefined) {
		current_fields.push(field);
		if($('[name="address_default_site_sync"]') != undefined && $('[name="address_default_site_sync"]').val() == 1 && $('[name="address_site_sync"]') != undefined && !($('[name="address_site_sync"]').is(':checked'))) {
			$('[name="address_site_sync"]').prop('checked', true).change();
		}
		field = $('[name=category]').get(0);
	}
	if($(field).closest('div[data-tab-name]').data('locked') == 'held' || field.name == 'category' || ('<?= IFRAME_PAGE ? 'IFRAME' : '' ?>' != '' || '<?= $contactid ?>' == 'new')) {
		var field_name = $(field).data('field');
		if(field_name != undefined && field_name != '') {
			var table_name = $(field).data('table');
			if(field_name != '' && table_name != '' && field.value != '*NEW_VALUE*') {
				if(field_name != 'primary_contact') {
					$(field).nextAll('input').first().hide();
				}
				$(field).nextAll('div.newfield').hide();
				var field_info = new FormData();
				field_info.append('tile_name', '<?= $folder_name ?>');
				field_info.append('field', field_name);
				field_info.append('table', table_name);
				field_info.append('contactid', $('[name=contactid]').val());
				if($(field).data('row-field') != undefined) {
					field_info.append('row_field', $(field).data('row-field'));
					field_info.append('row_id', $(field).data('row-id'));
				}
				if(field.name.substr(-2) == '[]') {
					var array_values = [];
					if($(field).data('include') == 'checkedonly' || field.type == 'checkbox') {
						$('[name^="'+field.name.slice(0,-2)+'"]:checked').each(function() {
							array_values.push(($(this).data('prepend') == undefined ? '' : $(this).data('prepend'))+this.value+($(this).data('append') == undefined ? '' : $(this).data('append')));
						});
					} else if(field.type == 'select-multiple' && $('[name^="'+field.name.slice(0,-2)+'"]').length == 1) {
						$('[name^="'+field.name.slice(0,-2)+'"] option:selected').each(function() {
							array_values.push(($(this).data('prepend') == undefined ? '' : $(this).data('prepend'))+this.value+($(this).data('append') == undefined ? '' : $(this).data('append')));
						});
					} else {
						if(field.type == 'select-multiple' || ($(field).data('exact-name') != undefined && $(field).data('exact-name') == 1)) {
							$('[name="'+field.name+'"] option:selected').each(function() {
								array_values.push(($(this).data('prepend') == undefined ? '' : $(this).data('prepend'))+this.value+($(this).data('append') == undefined ? '' : $(this).data('append')));
							});
						} else {
							$('[name^="'+field.name.slice(0,-2)+'"]').each(function() {
								array_values.push(($(this).data('prepend') == undefined ? '' : $(this).data('prepend'))+this.value+($(this).data('append') == undefined ? '' : $(this).data('append')));
							});
						}
					}
					// var array_values = $('[name^="'+field.name.slice(0,-2)+'"]').map(function() {debugger;
					// 	return this.value;
					// }).get();
					field_info.append('value', JSON.stringify(array_values));
					if($(field).data('delimiter') != undefined) {
						field_info.append('delimiter', $(field).data('delimiter'));
					} else {
						field_info.append('delimiter', ',');
					}
				} else if($(field).data('ischeckbox') != undefined) {
					field_info.append('value', ($(field).is(':checked') ? $(field).val() : ''));
				} else if(field.type == 'radio') {
					field_info.append('value', ($('[name="'+field.name+'"]:checked').val() == '0' ? '' : $('[name="'+field.name+'"]:checked').val()));
				} else {
					field_info.append('value', ($(field).data('value') == undefined ? $(field).val() : $(field).data('value')));
				}
				if($(field).data('append-last') != undefined) {
					field_info.append('append_last', $(field).data('append-last'));
				}
				if($('[name=sync_mail_address]').is(':checked')) {
					if(field.name == 'business_street') {
						$('[name=ship_to_address]').val(field.value).change();
					} else if(field.name == 'business_city') {
						$('[name=ship_city]').val(field.value).change();
					} else if(field.name == 'business_state') {
						$('[name=ship_state]').val(field.value).change();
					} else if(field.name == 'business_zip') {
						$('[name=ship_zip]').val(field.value).change();
					} else if(field.name == 'business_country') {
						$('[name=ship_country]').val(field.value).change();
					} else if(field.name == 'google_maps_address') {
						$('[name=ship_google_link]').val(field.value).change();
					} else if(field.name == 'sync_mail_address') {
						$('[name^=ship_]').attr('readonly',true);
					}
				}
				if($('[name=sync_payment_address]').is(':checked')) {
					if(field.name == 'business_street') {
						$('[name=payment_address]').val(field.value).change();
					} else if(field.name == 'business_city') {
						$('[name=payment_city]').val(field.value).change();
					} else if(field.name == 'business_state') {
						$('[name=payment_state]').val(field.value).change();
					} else if(field.name == 'business_zip') {
						$('[name=payment_postal_code],[name=payment_zip_code]').val(field.value).change();
					} else if(field.name == 'sync_payment_address') {
						$('[name=payment_address],[name=payment_city],[name=payment_state],[name=payment_postal_code],[name=payment_zip_code]').attr('readonly',true);
					}
				}
				if($('[name=sync_payment_address]').is(':checked')) {
				}
				if($(field).data('contactid-field') != undefined) {
					field_info.append('contactid_field', $(field).data('contactid-field'));
				}
				if($(field).data('contactid-category-field') != undefined) {
					field_info.append('contactid_category_field', $(field).data('contactid-category-field'));
				}
				if($(field).data('contact-category') != undefined) {
					field_info.append('contact_category', $(field).data('contact-category'));
				}
				if($(field).data('field-category') != undefined) {
					field_info.append('field_category', $(field).data('field-category'));
				}
				if($(field).data('replicating-fieldname') != undefined) {
					field_info.append('replicating_fieldname', $(field).data('replicating-fieldname'));
				}
				if($(field).data('contact-id') != undefined) {
					field_info.append('contact_id', $(field).val());
				}
				if($(field).data('new-category') != undefined) {
					field_info.append('new_category', $(field).val());
				}
				if($(field).data('new-firstname') != undefined) {
					field_info.append('new_first_name', $(field).val());
				}
				if($(field).data('new-lastname') != undefined) {
					field_info.append('new_last_name', $(field).val());
				}
				if($(field).data('no-contactid') != undefined) {
					field_info.append('no_contactid', $(field).data('no-contactid'));
				}
				var label = $(field).closest('.form-group').find('label').first().text();
				if(label.substring(label.length - 1) == ':') {
					label = label.substring(0,label.length - 1);
				}
				field_info.append('label', label);
				if($(field).closest('.site_address') != undefined) {
					if($(field).closest('.site_address').data('nosync') == 1) {
						field_info.append('contactid',$(field).closest('.site_address').data('contactid'));
					}
				}
				var ajax_data = {
					processData: false,
					contentType: false,
					url: '../Contacts/contacts_ajax.php?action=contact_values',
					method: 'POST',
					data: field_info,
					success: function(response) {
						var site = response.split('#');
						response = site[0];
						var site = site[1];
						if(($('[name=contactid]').val() == '' || $('[name=contactid]').val() == 'new') && response > 0) {
							$('[name=contactid]').val(response);
							if(field.name != 'category' && $('[name=category]').val() != '') {
								$('[name=category]').change();
							} else {
								window.history.replaceState('',"Software", window.location.href.replace('edit=new','edit='+response));
							}
						}
						if($(field).data('row-field') != undefined) {
							$(field).closest('form').find('input,select,textarea').each(function() {
								if($(this).data('row-id') == '' && $(this).data('table') == $(field).data('table')) {
									$(this).data('row-id', response);
								}
							});
							if(field.type == 'file') {
								$(field).prevAll('li:contains("Uploading file...")').last().remove();
								$(field).before('<li><a href="download/'+$(field)[0].files[0].name+'" target="_blank">'+$(field)[0].files[0].name+'</a></li>');
								$(field).val('');
							}
						} else if(field.type == '') {
							$(field).closest('span').remove();
						} else if(field.type == 'file') {
							$(field).prevAll('li:contains("Uploading file...")').last().remove();
							$(field).before('<li><a href="download/'+$(field)[0].files[0].name+'" target="_blank">'+$(field)[0].files[0].name+'</a></li>');
							$(field).val('');
						} else if(field.name == 'a_label') {
							$(field).prevAll('a').first().text(field.value);
							$(field).hide();
							$(field).val('');
						} else if(field.name == 'category' && field.value != field.defaultValue) {
							if('<?= (IFRAME_PAGE && $_GET['change'] != 'true') ? 'IFRAME' : '' ?>' == '') {
								window.location.replace('?<?= IFRAME_PAGE ? 'mode=iframe&' : '' ?><?= $_GET['change'] == 'true' ? 'change=true&' : '' ?>profile=false&businessid=<?= $_GET['businessid'] ?>&category='+field.value+'&edit='+ $('[name=contactid]').val());
							}
						} else if(field.name == 'primary_contact') {
							if($(field).is(':checked')) {
								$('[name="primary_contact"]').not(field).removeAttr('checked');
							}
						} else if(field.name == 'Name' || field.name == 'first_name' || field.name == 'last_name') {
							setInitials();
						} else if(field.name.substr(-10) == '_site_sync') {
							if(field.checked) {
								$(field).closest('[data-tab-name]').find('.site_address').show();
								$('#nav_'+$(field).closest('[data-tab-name]').attr('id')+'_site').show();
								loadSite();
							} else {
								$(field).closest('[data-tab-name]').find('.site_address').hide();
								$('#nav_'+$(field).closest('[data-tab-name]').attr('id')+'_site').hide();
							}
						}
						if(site > 0) {
							if(site_ids.indexOf(site) == -1) {
								site_ids.push(site);
							}
							loadSite();
						}
						updateContactLabel();
						doneSaving();
					}
				};
				if(field.type == 'file') {
					ajax_data.data.append('value', 'upload');
					ajax_data.data.append('file', $(field)[0].files[0]);
					$(field).before('<li>Uploading file...</li>');
				}
				$.ajax(ajax_data);
				clearTimeout(lock_timeout);
				lock_timeout = setTimeout(function() { releaseLock(profile_tab); }, 600000);
			} else {
				doneSaving();
			}
		} else if(field.value == '*NEW_VALUE*') {
			$(field).hide();
			$(field).nextAll('input').first().show().focus();
			$(field).nextAll('div.newfield').show();
			doneSaving();
		} else {
			doneSaving();
		}
	}
}
function updateContactLabel() {
	var contactid = $('[name=contactid]').val();
	if(contactid > 0) {
		$.ajax({
			url: '../Contacts/contacts_ajax.php?action=get_contact_name&contactid='+contactid,
			method: 'GET',
			success: function(response) {
				$('.breadcrumb_contact').prop('href', '?edit='+contactid);
				$('.breadcrumb_contact').text(response);
			}
		});
	}
}
function getLock(tab_name) {
	clearTimeout(lock_timeout);
	$.ajax({
		method: 'POST',
		url: '../Contacts/contacts_ajax.php?action=table_locks',
		data: {
			contactid: '<?= $_GET['edit'] ?>',
			section: tab_name,
			session_id: '<?= $_SESSION['contactid'] ?>'
		},
		reponse: 'html',
		success: function(response) {
			var locked_tabs = [];
			if(response != '#*#') {
				locked_tabs = response.split('#*#')[0].split(',');
				console.log(response.split('#*#')[1]);
			}
			$('div[data-tab-name]').data('locked','');
			tab_name.forEach(function(tab) {
				if(!locked_tabs.includes(tab)) {
					$('div[data-tab-name="'+tab+'"]').data('locked','held');
				}
			});
			lock_timeout = setTimeout(function() { releaseLock(tab_name); }, 600000);
			lockTabs();
		}
	});
}
function releaseLock(tab_name) {
	tab_name.forEach(function(tab) {
		if(tab != '') {
			$('div[data-tab-name="'+tab+'"]').data('locked','');
			lockTabs();
			$.ajax({
				method: 'POST',
				url: '../Contacts/contacts_ajax.php?action=unlock_table',
				data: {
					contactid: '<?= $_GET['edit'] ?>',
					section: tab,
					session_id: '<?= $_SESSION['contactid'] ?>'
				}
			});
		}
	});
}
function lockTabs() {
	$('.active.blue').removeClass('active').removeClass('blue');
	$('div[data-tab-name]').each(function() {
		if($(this).data('locked') != 'held' && '<?= $_GET['edit'] ?>' != 'new' && '<?= IFRAME_PAGE ?>' == '' && '<?= isset($_GET['fields']) ? 'FIELD_VIEW' : '' ?>' == '') {
			$(this).find('[data-field],a').off('click').click(function() { this.blur(); return false; }).off('keyup').keyup(function() { this.blur(); return false; }).off('keypress').keypress(function() { this.blur(); return false; });
		} else if('<?= $_GET['edit'] ?>' == 'new') {
			$(this).find('[data-field]').off('click').off('keypress').off('keyup').off('focus',unsaved).focus(unsaved);
			if(this.getBoundingClientRect().top != undefined && $('.standard-dashboard-body:visible').offset() != undefined && this.getBoundingClientRect().top < $('.standard-dashboard-body:visible').offset().top + $('.standard-dashboard-body:visible').height() && this.getBoundingClientRect().bottom > $('.standard-dashboard-body:visible').offset().top) {
				$('a[href=#'+$(this).data('tab-name')+'] li').addClass('active blue');
			}
		} else {
			$(this).find('[data-field]').off('click').off('keypress').off('keyup').off('focus',unsaved).focus(unsaved);
			$('a[href=#'+$(this).data('tab-name')+'] li').addClass('active blue');
		}
	});
	if($('#view_profile').is(':visible')) {
		$('[href=#view_profile] li').addClass('active blue');
	}
	if($('#view_checklist').is(':visible')) {
		$('[href=#view_checklist] li').addClass('active blue');
	}
}
function addMultiple(div) {
	destroyInputs('.'+div);
	var row = $('.'+div).last();
	var clone = row.clone();
	clone.find('input').val('');
    clone.find('.clone_exception_block').closest('.form-group').remove();
	row.after(clone);
	initInputs('.'+div);
	$('[data-field]').off('blur',unsaved).blur(unsaved).off('focus',unsaved).focus(unsaved).off('change',saveField).change(saveField);
}
function removeMultiple(div, btn) {
	var row = $('.'+div).first();
	if($('.'+div).length <= 1) {
		addMultiple(div);
	}
	$(btn).closest('.'+div).remove();
	row.find('input').each(function() {$(this).change(); });
}
function send_alert(button, schedule) {
    var to      = $('#alert_staff').val();
    var from    = $('#alert_sending_email_address').val();
    var subject = $('#alert_email_subject').val();
    var body    = $('#alert_email_body').val();
    $.ajax({
		type: "POST",
		url: "../Contacts/contacts_ajax.php?action=send_alert",
		data: {
            to: to,
            from: from,
            subject: subject,
            body: body,
			schedule: schedule
        },
		dataType: "html",//expect html to be returned
		success: function(response){
			alert(response);
		}
	});
}
function setInitials() {
	if((($('[name="category"]').val() == 'Business' || $('[name="category"]').val() == 'Corporation') && $('[name="Name"]').val() != '' && !$('[name="initials"]').is('[readonly]') && $('[name="initials"]').val() == '') || ($('[name="first_name"]').val() != '' && $('[name="last_name"]').val() != '' && $('[name="initials"]').val() == '')) {
		$.ajax({
			type: 'POST',
			url: '../Contacts/contacts_ajax.php?action=set_initials',
			data: {
				contactid: $('[name="contactid"]').val()
			},
			dataType: "html",
			success: function(response){
				$('[name="initials"]').val(response);
			}
		});
	}
}
function autoGenerateUsingEmail() {
	var username = $('[name="email"]').val();
    var alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    var password = '';
    for (var i = 0; i < 8; i++) {
        var rng = Math.floor(Math.random() * alphabet.length);
        password += alphabet.substring((rng - 1), rng);
    }
    $('[name="user_name"]').val(username);
    $('[name="password"]').val(password);
    alert('The generated password is '+password);
    $('[name="user_name"]').trigger('change');
    $('[name="password"]').trigger('change');
}
function emailCredentialsDialog() {
	$('[name="email_creds_recipient"]').val($('[name="email"]').val());
	destroyInputs('#dialog_email_credentials');
	$('#dialog_email_credentials').dialog({
		resizable: true,
		height: "auto",
		width: ($(window).width() <= 800 ? $(window).width() : 800),
		modal: true,
		buttons: {
			"Send Email": function() {
				var contactid = $('[name="contactid"]').val();
				$.ajax({
					type: 'POST',
					url: '../Contacts/contacts_ajax.php?action=email_login_credentials',
					data: {
						contactid: contactid,
						sender_name: $('[name="email_creds_sender_name"]').val(),
						sender: $('[name="email_creds_sender"]').val(),
						recipient: $('[name="email_creds_recipient"]').val(),
						subject: $('[name="email_creds_subject"]').val(),
						body: $('[name="email_creds_body"]').val(),
					},
					success: function(response) {
						alert(response);
					}
				});
				$(this).dialog('close');
			},
	        Cancel: function() {
	        	$(this).dialog('close');
	        }
		}
	});
	initInputs('#dialog_email_credentials');
}
function contactReminderDialog(folder, subject, recipient) {
	$('[name="email_creds_recipient"]').val($('[name="email"]').val());
	destroyInputs('#dialog_contact_reminders');
	$('#dialog_contact_reminders').dialog({
		resizable: true,
		height: "auto",
		width: ($(window).width() <= 800 ? $(window).width() : 800),
		modal: true,
		buttons: {
			"Add Reminder": function() {
				$.ajax({
					type: 'POST',
					url: '../Contacts/contacts_ajax.php?action=add_contact_reminder',
					data: {
						staffid: $('[name="contact_reminder_staff"]').val(),
                        contactid: $('[name="contact_reminder_contactid"]').val(),
						reminder_subject: $('[name="contact_reminder_subject"]').val(),
						reminder_date: $('[name="contact_reminder_date"]').val(),
						reminder_folder: folder,
					},
					success: function(response) {
						alert('Reminder added.');
					}
				});
				$(this).dialog('close');
			},
	        Cancel: function() {
	        	$(this).dialog('close');
	        }
		}
	});
	if(subject != undefined) {
		$('#dialog_contact_reminders [name=contact_reminder_subject]').val(subject);
	}
	if(recipient) {
		$('#dialog_contact_reminders .recipient').show();
	} else {
		$('#dialog_contact_reminders .recipient').hide();
	}
	initInputs('#dialog_contact_reminders');
}
function multipleCategories(field) {
	var contactid = $('[name=contactid]').val();

	var block = $(field).closest('.multi_cat_block');
	var other_contactid = $(block).data('contactid');
	var category = $(block).find('[name="multiple_categories[]"]').val();

	$.ajax({
		url: '../Contacts/contacts_ajax.php?action=multiple_categories',
		method: 'POST',
		data: { contactid: contactid, other_contactid: other_contactid, category: category },
		success:function(response) {
			if(response > 0) {
				$(block).data('contactid', response);
			}
			doneSaving();
		}
	});
}
function addMultipleCategory() {
	destroyInputs('.multi_cat_block');
	var block = $('.multi_cat_block').last();
	var clone = $(block).clone();
	clone.find('select').val('');
	clone.data('contactid', '');
	$(block).after(clone);
	initInputs('.multi_cat_block');
}
function removeMultipleCategory(img) {
	var contactid = $('[name=contactid]').val();

	var block = $(img).closest('.multi_cat_block');
	var other_contactid = $(block).data('contactid');

	if(other_contactid != undefined && other_contactid > 0) {
		var delete_contact = 0;
		if(confirm('Would you like to remove the data for this Category from the software?')) {
			delete_contact = 1;
		}
		$.ajax({
			url: '../Contacts/contacts_ajax.php?action=remove_multiple_categories',
			method: 'POST',
			data: { contactid: contactid, other_contactid: other_contactid, delete_contact: delete_contact },
			success:function(response) {
			}
		});
	}
	if($('.multi_cat_block').length <= 1) {
		addMultipleCategory();
	}
	$(img).closest('.multi_cat_block').remove();
}
function openFullView() {
	var contactid = $('[name=contactid]').val();
	window.top.location.href = '<?= WEBSITE_URL ?>/<?= ucfirst(FOLDER_NAME) ?>/contacts_inbox.php?edit='+contactid;
}
function calculateAllocatedHours() {
	var total_hours = 0;
	$('[name="contract_allocated_hours[]"]').each(function() {
		if(this.value != '') {
			total_hours += parseFloat(this.value);
		}
	});
	$('[name="total_allocated_hours_calc"]').val(total_hours);
}
function addAnotherSite() {
	$('#dialog_add_site').dialog({
		resizable: true,
		height: "auto",
		width: ($(window).width() <= 800 ? $(window).width() : 800),
		modal: true,
		buttons: {
			"Add Site": function() {
				var contactid = $('[name=contactid]').val();
				var another_site_id = $('[name="add_another_site"]').val();
				$.ajax({
					type: 'POST',
					url: '../Contacts/contacts_ajax.php?action=add_another_site',
					data: {
						contactid: contactid,
						another_site_id: another_site_id
					},
					success: function(response) {
						site_ids.push(another_site_id);
						loadSite();
					}
				});
				$(this).dialog('close');
			},
	        Cancel: function() {
	        	$(this).dialog('close');
	        }
		}
	});
}
function addContactForm(form_id) {
	var contactid = $('[name=contactid]').val();
	overlayIFrameSlider('../Contacts/fill_contact_form.php?contactid='+contactid+'&form_id='+form_id, 'auto', false, true);
}
function editContactForm(pdf_id) {
	overlayIFrameSlider('../Contacts/fill_contact_form.php?pdf_id='+pdf_id, 'auto', false, true);
}
function removeContactForm(a, pdf_id) {
	if(confirm('Are you sure you want to archive this form?')) {
		$.ajax({
			url: '../Contacts/contacts_ajax.php?action=archive_contact_form&pdf_id='+pdf_id,
			method: 'GET',
			success: function(response) {
				$(a).closest('tr').remove();
			}
		});
	}
}
</script>
<div id="dialog_add_site" title="Add a Site" style="display:none;">
	<div class="form-group">
		<label class="col-sm-4 control-label">Site:</label>
		<div class="col-sm-8">
			<select name="add_another_site" class="chosen-select-deselect form-control">
				<option></option>
				<?php $list_sites = sort_contacts_query(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = '".SITES_CAT."' AND `deleted` = 0 AND `status` > 0"));
				foreach($list_sites as $list_site) {
					echo '<option value="'.$list_site['contactid'].'">'.$list_site['full_name'].'</option>';
				} ?>
			</select>
		</div>
	</div>
</div>
<?php //if(!IFRAME_PAGE && !isset($_GET['fields'])) {
    if(!isset($mandatory_config)) {
		if(IFRAME_PAGE) { ?>
		    <div class="main-screen standard-body <?= $slider_layout == 'accordion' ? 'iframe_edit' : '' ?>" style="display: none; width: 100%; margin: 0;">
		        <div class="standard-body-title" style="<?= $_GET['fields'] == 'fields_only' ? 'display:none;' : '' ?>">
		            <div class="row">
		                <div class="col-sm-12">
		                	<h3>
		                		<?= ($_GET['edit'] > 0) ? 'Edit' : 'Add' ?> <?= !empty($_GET['category']) ? $_GET['category'] : ($folder_label != 'Contacts' ? $folder_label : CONTACTS_NOUN) ?>
		                		<a href="" onclick="openFullView(); return false;" class="btn brand-btn pull-right">Open Full Window</a>
		                	</h3>
		                </div>
		            </div>
		        </div>
		        <div class="standard-dashboard-body-content">
		<?php } ?>
		<div id='profile_accordions' class='sidebar show-on-mob panel-group block-panels col-xs-12' <?= IFRAME_PAGE ? 'style="background-color: #fff; padding: 0; margin-left: 0.5em; width: calc(100% - 1em);"' : '' ?>>
			<?php if(!isset($mandatory_config)) {
            	$contact['category'] = get_contact($dbc, $_GET['edit'], 'category'); ?>
                <div class="form-horizontal double-pad-top" data-tab-name="mandatory" data-locked="held">
                    <div class="form-group">
                        <label class="col-sm-4 control-label"><?= CONTACTS_TILE ?> Category:</label>
                        <div class="col-sm-8">
							<?php if($folder_name == 'staff') {
								$each_tab = ['Staff'];
							} else {
								$each_tab = explode(',',get_config($dbc, $folder_name.'_tabs'));
							} ?>
							<?php if((!IFRAME_PAGE && !isset($_GET['fields'])) || !in_array($_GET['category'],$each_tab)) { ?>
								<select name="category" data-field="category" data-table="contacts" class="form-control chosen-select-deselect"><option></option>
									<?php foreach ($each_tab as $cat_tab) {
										echo "<option ".($contact['category'] == $cat_tab || (empty($contact['category']) && $_GET['category'] == $cat_tab) ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
									}
									if(!empty($contact['category']) && !in_array($contact['category'], $each_tab)) {
										echo "<option selected value='". $contact['category']."'>".$contact['category'].'</option>';
									} ?>
								</select>
							<?php } else { ?>
								<input class="form-control" name="category" value="<?= $_GET['category'] ?>" readonly data-field="category" data-table="contacts">
							<?php } ?>
                        </div>
                        <div class="clearfix"></div>
                    </div>
                </div>
            <?php } ?>
            <?php if((!empty($contact['category']) || !empty($_GET['category']))) { ?>
				<div class="panel panel-default" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>'>
					<div class="panel-heading">
						<h4 class="panel-title">
							<a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_profile">
								View Profile<span class="glyphicon glyphicon-plus"></span>
							</a>
						</h4>
					</div>

					<div id="collapse_profile" class="panel-collapse collapse">
						<div class="panel-body" data-tab-name="checklist" data-tab-label="Profile">
							<?php include('contact_profile.php'); ?>
						</div>
					</div>
				</div>
				<?php if (in_array('Checklist', $field_config) && $edit_access > 0) { ?>
	                <div class="panel panel-default" style='<?= $_GET['edit'] > 0 && !IFRAME_PAGE ? '' : 'display:none;' ?>'>
	                    <div class="panel-heading">
	                        <h4 class="panel-title">
	                            <a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_checklist">
	                                Checklists<span class="glyphicon glyphicon-plus"></span>
	                            </a>
	                        </h4>
	                    </div>

	                    <div id="collapse_checklist" class="panel-collapse collapse">
	                        <div class="panel-body" data-tab-name="checklist" data-tab-label="Checklists">
	                            Loading...
	                        </div>
	                    </div>
	                </div>
				<?php } ?>
				<?php $security_folder = $folder_name;
				if($security_folder == 'clientinfo') {
					$security_folder = 'client_info';
				} else if($security_folder == 'contactsrolodex') {
					$security_folder = 'contacts_rolodex';
				} else if($security_folder == 'contacts') {
					$security_folder = 'contacts_inbox';
				}
				foreach($field_config as $field_name) {
					if(substr($field_name, 0, 4) == 'acc_' && $edit_access > 0) {
						foreach($tab_list as $tab_label => $tab_data) {
							if(!check_subtab_persmission($dbc, $security_folder, ROLE, $tab_data[0])) {
								unset($tab_list[$tab_label]);
							} else if($field_name == 'acc_'.$tab_data[0] && $tab_label != 'Checklist' && !in_array($tab_data[0], $subtabs_hidden) && (in_array($tab_data[0], $contact_subtabs) || empty($contact_subtabs))) { ?>
								<div class="panel panel-default">
									<div class="panel-heading">
										<h4 class="panel-title">
											<a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_<?= $tab_data[0] ?>">
												<?= $tab_label ?><span class="glyphicon glyphicon-plus"></span>
											</a>
										</h4>
									</div>

									<div id="collapse_<?= $tab_data[0] ?>" class="panel-collapse collapse">
										<div class="panel-body" data-tab-name="<?= $tab_data[0] ?>" data-tab-label="<?= $tab_label ?>">
											Loading...
										</div>
									</div>
								</div>
							<?php }
						}
					}
				} ?>
				<?php foreach($tab_list as $tab_label => $tab_data) {
					if(in_array_any($tab_data[1],$field_config) && !in_array('acc_'.$tab_data[0],$field_config) && $tab_data[0] != 'sibling_information' && $tab_label != 'Checklist' && $edit_access > 0 && !in_array($tab_data[0], $subtabs_hidden) && (in_array($tab_data[0], $contact_subtabs) || empty($contact_subtabs))) { ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_<?= $tab_data[0] ?>">
										<?= $tab_label ?><span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_<?= $tab_data[0] ?>" class="panel-collapse collapse">
								<div class="panel-body" data-tab-name="<?= $tab_data[0] ?>" data-tab-label="<?= $tab_label ?>">
									Loading...
								</div>
							</div>
						</div>
					<?php }
				} ?>
				<?php if(in_array('Attached Contact Forms as Subtabs',$field_config)) {
					$contact_forms = mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE CONCAT(',',`assigned_tile`,',') LIKE '%,attach_contact,%' AND `deleted` = 0 AND (CONCAT(',',`attached_contacts`,',') LIKE '%,$contactid,%' OR (CONCAT(',',`attached_contacts`,',') LIKE '%,ALL_CONTACTS%,' AND (CONCAT(',',`attached_contact_categories`,',') LIKE '%,$current_type,%' OR IFNULL(`attached_contact_categories`,'') = ''))) AND `is_template` = 0 ORDER BY `name`");
					while($contact_form = mysqli_fetch_assoc($contact_forms)) { ?>
						<div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_contactform_<?= $contact_form['form_id'] ?>">
										<?= $contact_form['name'] ?><span class="glyphicon glyphicon-plus"></span>
									</a>
								</h4>
							</div>

							<div id="collapse_contactform_<?= $contact_form['form_id'] ?>" class="panel-collapse collapse">
								<div class="panel-body" data-tab-name="contactform_<?= $contact_form['form_id'] ?>" data-tab-label="<?= $contact_form['name'] ?>" data-url="edit_addition_contact_forms.php?edit=<? $contactid ?>&user_form_id=<?= $contact_form['form_id'] ?>">
									Loading...
								</div>
							</div>
						</div>
					<?php }
				} ?>
				<?php if ((in_array('Sales Lead Details',$field_config) || in_array($contact['category'],['Sales Lead','Sales Leads'])) && $sales_lead_id > 0 && vuaed_visible_function($dbc, 'sales')) {
	                $sales_config = get_field_config($dbc, 'sales');
	                if (strpos($sales_config, ',Staff Information,') !== false) { ?>
	                    <div class="panel panel-default" style='<?= $_GET['edit'] > 0 && !IFRAME_PAGE ? '' : 'display:none;' ?>'>
	                        <div class="panel-heading">
	                            <h4 class="panel-title">
	                                <a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_sales_staffinfo">
	                                    <?= SALES_NOUN ?>: Staff Information<span class="glyphicon glyphicon-plus"></span>
	                                </a>
	                            </h4>
	                        </div>

	                        <div id="collapse_sales_staffinfo" class="panel-collapse collapse">
	                            <div class="panel-body" data-tab-name="sales_staffinfo" data-tab-label="Staff Information" data-url="../Sales/details_staff_info.php?id=<?= $sales_lead_id ?>">
	                                Loading...
	                            </div>
	                        </div>
	                    </div>
	                <?php }
	                if (strpos($sales_config, ',Next Action,') !== false) { ?>
	                    <div class="panel panel-default" style='<?= $_GET['edit'] > 0 && !IFRAME_PAGE ? '' : 'display:none;' ?>'>
	                        <div class="panel-heading">
	                            <h4 class="panel-title">
	                                <a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_sales_nextaction">
	                                    <?= SALES_NOUN ?>: Next Action<span class="glyphicon glyphicon-plus"></span>
	                                </a>
	                            </h4>
	                        </div>

	                        <div id="collapse_sales_nextaction" class="panel-collapse collapse">
	                            <div class="panel-body" data-tab-name="sales_nextaction" data-tab-label="Next Action" data-url="../Sales/details_next_action.php?id=<?= $sales_lead_id ?>">
	                                Loading...
	                            </div>
	                        </div>
	                    </div>
	                <?php }
	                if (strpos($sales_config, ',Service,') !== false) { ?>
	                    <div class="panel panel-default" style='<?= $_GET['edit'] > 0 && !IFRAME_PAGE ? '' : 'display:none;' ?>'>
	                        <div class="panel-heading">
	                            <h4 class="panel-title">
	                                <a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_sales_services">
	                                    <?= SALES_NOUN ?>: Services<span class="glyphicon glyphicon-plus"></span>
	                                </a>
	                            </h4>
	                        </div>

	                        <div id="collapse_sales_services" class="panel-collapse collapse">
	                            <div class="panel-body" data-tab-name="sales_services" data-tab-label="Services" data-url="../Sales/details_services.php?id=<?= $sales_lead_id ?>">
	                                Loading...
	                            </div>
	                        </div>
	                    </div>
	                <?php }
	                if (strpos($sales_config, ',Products,') !== false) { ?>
	                    <div class="panel panel-default" style='<?= $_GET['edit'] > 0 && !IFRAME_PAGE ? '' : 'display:none;' ?>'>
	                        <div class="panel-heading">
	                            <h4 class="panel-title">
	                                <a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_sales_products">
	                                    <?= SALES_NOUN ?>: Products<span class="glyphicon glyphicon-plus"></span>
	                                </a>
	                            </h4>
	                        </div>

	                        <div id="collapse_sales_products" class="panel-collapse collapse">
	                            <div class="panel-body" data-tab-name="sales_products" data-tab-label="Products" data-url="../Sales/details_products.php?id=<?= $sales_lead_id ?>">
	                                Loading...
	                            </div>
	                        </div>
	                    </div>
	                <?php }
	                if (strpos($sales_config, ',Reference Documents,') !== false) { ?>
	                    <div class="panel panel-default" style='<?= $_GET['edit'] > 0 && !IFRAME_PAGE ? '' : 'display:none;' ?>'>
	                        <div class="panel-heading">
	                            <h4 class="panel-title">
	                                <a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_sales_refdocs">
	                                    <?= SALES_NOUN ?>: Reference Documents<span class="glyphicon glyphicon-plus"></span>
	                                </a>
	                            </h4>
	                        </div>

	                        <div id="collapse_sales_refdocs" class="panel-collapse collapse">
	                            <div class="panel-body" data-tab-name="sales_refdocs" data-tab-label="Reference Documents" data-url="../Sales/details_ref_docs.php?id=<?= $sales_lead_id ?>">
	                                Loading...
	                            </div>
	                        </div>
	                    </div>
	                <?php }
	                if (strpos($sales_config, ',Marketing Material,') !== false) { ?>
	                    <div class="panel panel-default" style='<?= $_GET['edit'] > 0 && !IFRAME_PAGE ? '' : 'display:none;' ?>'>
	                        <div class="panel-heading">
	                            <h4 class="panel-title">
	                                <a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_sales_marketing">
	                                    <?= SALES_NOUN ?>: Marketing Material<span class="glyphicon glyphicon-plus"></span>
	                                </a>
	                            </h4>
	                        </div>

	                        <div id="collapse_sales_marketing" class="panel-collapse collapse">
	                            <div class="panel-body" data-tab-name="sales_marketing" data-tab-label="Marketing Material" data-url="../Sales/details_marketing.php?id=<?= $sales_lead_id ?>">
	                                Loading...
	                            </div>
	                        </div>
	                    </div>
	                <?php }
	                if (strpos($sales_config, 'Information Gathering,') !== false) { ?>
	                    <div class="panel panel-default" style='<?= $_GET['edit'] > 0 && !IFRAME_PAGE ? '' : 'display:none;' ?>'>
	                        <div class="panel-heading">
	                            <h4 class="panel-title">
	                                <a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_sales_infogathering">
	                                    <?= SALES_NOUN ?>: Information Gathering<span class="glyphicon glyphicon-plus"></span>
	                                </a>
	                            </h4>
	                        </div>

	                        <div id="collapse_sales_infogathering" class="panel-collapse collapse">
	                            <div class="panel-body" data-tab-name="sales_infogathering" data-tab-label="Information Gathering" data-url="../Sales/details_info_gathering.php?id=<?= $sales_lead_id ?>">
	                                Loading...
	                            </div>
	                        </div>
	                    </div>
	                <?php }
	                if (strpos($sales_config, ',Estimate,') !== false) { ?>
	                    <div class="panel panel-default" style='<?= $_GET['edit'] > 0 && !IFRAME_PAGE ? '' : 'display:none;' ?>'>
	                        <div class="panel-heading">
	                            <h4 class="panel-title">
	                                <a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_sales_estimate">
	                                    <?= SALES_NOUN ?>: Estimate<span class="glyphicon glyphicon-plus"></span>
	                                </a>
	                            </h4>
	                        </div>

	                        <div id="collapse_sales_estimate" class="panel-collapse collapse">
	                            <div class="panel-body" data-tab-name="sales_estimate" data-tab-label="Estimate" data-url="../Sales/details_estimate.php?id=<?= $sales_lead_id ?>">
	                                Loading...
	                            </div>
	                        </div>
	                    </div>
	                <?php }
	                if (strpos($sales_config, ',Lead Notes,') !== false) { ?>
	                    <div class="panel panel-default" style='<?= $_GET['edit'] > 0 && !IFRAME_PAGE ? '' : 'display:none;' ?>'>
	                        <div class="panel-heading">
	                            <h4 class="panel-title">
	                                <a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_sales_leadnotes">
	                                    <?= SALES_NOUN ?>: Lead Notes<span class="glyphicon glyphicon-plus"></span>
	                                </a>
	                            </h4>
	                        </div>

	                        <div id="collapse_sales_leadnotes" class="panel-collapse collapse">
	                            <div class="panel-body" data-tab-name="sales_leadnotes" data-tab-label="Lead Notes" data-url="../Sales/details_lead_notes.php?id=<?= $sales_lead_id ?>">
	                                Loading...
	                            </div>
	                        </div>
	                    </div>
	                <?php }
	                if (strpos($sales_config, ',Tasks,') !== false) { ?>
	                    <div class="panel panel-default" style='<?= $_GET['edit'] > 0 && !IFRAME_PAGE ? '' : 'display:none;' ?>'>
	                        <div class="panel-heading">
	                            <h4 class="panel-title">
	                                <a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_sales_tasks">
	                                    <?= SALES_NOUN ?>: Tasks<span class="glyphicon glyphicon-plus"></span>
	                                </a>
	                            </h4>
	                        </div>

	                        <div id="collapse_sales_tasks" class="panel-collapse collapse">
	                            <div class="panel-body" data-tab-name="sales_tasks" data-tab-label="Tasks" data-url="../Sales/details_tasks.php?id=<?= $sales_lead_id ?>">
	                                Loading...
	                            </div>
	                        </div>
	                    </div>
	                <?php }
	                if (strpos($sales_config, ',Time,') !== false) { ?>
	                    <div class="panel panel-default" style='<?= $_GET['edit'] > 0 && !IFRAME_PAGE ? '' : 'display:none;' ?>'>
	                        <div class="panel-heading">
	                            <h4 class="panel-title">
	                                <a data-toggle="collapse" data-parent="#profile_accordions" href="#collapse_sales_time">
	                                    <?= SALES_NOUN ?>: Time Tracking<span class="glyphicon glyphicon-plus"></span>
	                                </a>
	                            </h4>
	                        </div>

	                        <div id="collapse_sales_time" class="panel-collapse collapse">
	                            <div class="panel-body" data-tab-name="sales_time" data-tab-label="Time Tracking" data-url="../Sales/details_time.php?id=<?= $sales_lead_id ?>">
	                                Loading...
	                            </div>
	                        </div>
	                    </div>
	                <?php } ?>
				<?php } ?>
			<?php } ?>
		</div>
		<?php if(IFRAME_PAGE) { ?>
				</div>
			</div>
		<?php } ?>

		<div class="tile-container" <?= IFRAME_PAGE ? 'style="background: none;"' : '' ?>>
 			<div class="tile-sidebar standard-collapsible hide-titles-mob" <?= IFRAME_PAGE ? 'style="display:none;"' : '' ?>><!-- style='display: block; height: 15em; margin-bottom: 0; overflow-y: auto; padding-left: 15px;' -->
				<div style="height: 100%; overflow: auto;">
					<ul>
						<?php if((!empty($contact['category']) || !empty($_GET['category']))) { ?>
							<a href="#view_profile" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>' onclick='view_profile(); return false;'><li <?= (!isset($_GET['view_checklist']) && !isset($_GET['edit_checklist']) && !isset($_GET['list_checklists']) ? 'class="active blue"' : '') ?>>Profile</li></a>
							<?php if (in_array('Checklist', $field_config) && $edit_access > 0) { ?>
								<a href="#view_checklist" style='<?= $_GET['edit'] > 0 ? '' : 'display:none;' ?>' onclick='view_checklist(); return false;'><li <?= (isset($_GET['view_checklist']) || isset($_GET['edit_checklist']) || isset($_GET['list_checklists']) ? 'class="active blue"' : '') ?>>Checklists</li></a>
							<?php } ?>
							<?php foreach($field_config as $field_name) {
								if(substr($field_name, 0, 4) == 'acc_' && $edit_access > 0) {
									foreach($tab_list as $tab_label => $tab_data) {
										if($field_name == 'acc_'.$tab_data[0] && $tab_label != 'Checklist' && !in_array($tab_data[0], $subtabs_hidden) && (in_array($tab_data[0], $contact_subtabs) || empty($contact_subtabs))) {
	                                        $tab_label = ($tab_label=='Payment Information') ? 'Payment &amp; Billing Information' : $tab_label; ?>
											<a id="nav_<?= strtolower(str_replace(' ', '_', $tab_label)); ?>" href="#<?= $tab_data[0] ?>" onclick="jumpTab('<?= $tab_data[0] ?>'); return false;"><li class=""><?= $tab_label ?></li></a>
											<?php if($tab_data[0] == 'business_address') { ?>
												<a id="nav_<?= strtolower(str_replace(' ', '_', $tab_label)); ?>_site" href="#<?= $tab_data[0] ?>" onclick="jumpTab('<?= $tab_data[0] ?>', ' .site_address'); return false;" style="<?= $contact['business_site_sync'] > 0 ? '' : 'display:none;' ?>"><li class="">Site Address</li></a>
											<?php } else if($tab_data[0] == 'mailing_address') { ?>
												<a id="nav_<?= strtolower(str_replace(' ', '_', $tab_label)); ?>_site" href="#<?= $tab_data[0] ?>" onclick="jumpTab('<?= $tab_data[0] ?>', ' .site_address'); return false;" style="<?= $contact['mailing_site_sync'] > 0 ? '' : 'display:none;' ?>"><li class="">Site Address</li></a>
											<?php } else if($tab_data[0] == 'address') { ?>
												<a id="nav_<?= strtolower(str_replace(' ', '_', $tab_label)); ?>_site" href="#<?= $tab_data[0] ?>" onclick="jumpTab('<?= $tab_data[0] ?>', ' .site_address'); return false;" style="<?= $contact['address_site_sync'] > 0 ? '' : 'display:none;' ?>"><li class="">Site Address</li></a>
											<?php }
										}
									}
								}
							} ?>
							<?php foreach($tab_list as $tab_label => $tab_data) {
								if(in_array_any($tab_data[1],$field_config) && !in_array('acc_'.$tab_data[0],$field_config) && $tab_data[0] != 'sibling_information' && $tab_label != 'Checklist' && $edit_access > 0 && !in_array($tab_data[0], $subtabs_hidden) && (in_array($tab_data[0], $contact_subtabs) || empty($contact_subtabs))) { ?>
									<a id="nav_<?= strtolower(str_replace(' ', '_', $tab_label)); ?>" href="#<?= $tab_data[0] ?>" onclick="jumpTab('<?= $tab_data[0] ?>'); return false;"><li class=""><?= $tab_label ?></li></a>
								<?php }
							} ?>
							<?php if(in_array('Attached Contact Forms as Subtabs',$field_config)) {
								$contact_forms = mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE CONCAT(',',`assigned_tile`,',') LIKE '%,attach_contact,%' AND `deleted` = 0 AND (CONCAT(',',`attached_contacts`,',') LIKE '%,$contactid,%' OR (CONCAT(',',`attached_contacts`,',') LIKE '%,ALL_CONTACTS%,' AND (CONCAT(',',`attached_contact_categories`,',') LIKE '%,$current_type,%' OR IFNULL(`attached_contact_categories`,'') = ''))) AND `is_template` = 0 ORDER BY `name`");
								while($contact_form = mysqli_fetch_assoc($contact_forms)) { ?>
									<a id="nav_contactform_<?= $contact_form['form_id'] ?>" href="#contactform_<?= $contact_form['form_id'] ?>" onclick="jumpTab('contactform_<?= $contact_form['form_id'] ?>'); return false;"><li class=""><?= $contact_form['name'] ?></li></a>
								<?php }
							}
	                        if ((in_array('Sales Lead Details',$field_config) || in_array($contact['category'],['Sales Lead','Sales Leads'])) && $sales_lead_id > 0 && vuaed_visible_function($dbc, 'sales')) {
	                            $sales_config = get_field_config($dbc, 'sales');
	                            if (strpos($sales_config, ',Staff Information,') !== false) { ?>
	                                <a id="nav_sales_staff_info" href="#sales_staff_info" onclick="jumpTab('sales_staff_info'); return false;"><li class=""><?= SALES_NOUN ?>: Staff Information</li></a>
	                            <?php }
	                            if (strpos($sales_config, ',Next Action,') !== false) { ?>
	                                <a id="nav_sales_next_action" href="#sales_next_action" onclick="jumpTab('sales_next_action'); return false;"><li class=""><?= SALES_NOUN ?>: Next Action</li></a>
	                            <?php }
	                            if (strpos($sales_config, ',Service,') !== false) { ?>
	                                <a id="nav_sales_services" href="#sales_services" onclick="jumpTab('sales_services'); return false;"><li class=""><?= SALES_NOUN ?>: Services</li></a>
	                            <?php }
	                            if (strpos($sales_config, ',Products,') !== false) { ?>
	                                <a id="nav_sales_products" href="#sales_products" onclick="jumpTab('sales_products'); return false;"><li class=""><?= SALES_NOUN ?>: Products</li></a>
	                            <?php }
	                            if (strpos($sales_config, ',Reference Documents,') !== false) { ?>
	                                <a id="nav_sales_ref_docs" href="#sales_ref_docs" onclick="jumpTab('sales_ref_docs'); return false;"><li class=""><?= SALES_NOUN ?>: Reference Documents</li></a>
	                            <?php }
	                            if (strpos($sales_config, ',Marketing Material,') !== false) { ?>
	                                <a id="nav_sales_marketing" href="#sales_marketing" onclick="jumpTab('sales_marketing'); return false;"><li class=""><?= SALES_NOUN ?>: Marketing Material</li></a>
	                            <?php }
	                            if (strpos($sales_config, 'Information Gathering,') !== false) { ?>
	                                <a id="nav_sales_info_gathering" href="#sales_ingo_gathering" onclick="jumpTab('sales_ingo_gathering'); return false;"><li class=""><?= SALES_NOUN ?>: Information Gathering</li></a>
	                            <?php }
	                            if (strpos($sales_config, ',Estimate,') !== false) { ?>
	                                <a id="nav_sales_estimate" href="#sales_estimate" onclick="jumpTab('sales_estimate'); return false;"><li class=""><?= SALES_NOUN ?>: Estimate</li></a>
	                            <?php }
	                            if (strpos($sales_config, ',Lead Notes,') !== false) { ?>
	                                <a id="nav_sales_lead_notes" href="#sales_lead_notes" onclick="jumpTab('sales_lead_notes'); return false;"><li class=""><?= SALES_NOUN ?>: Lead Notes</li></a>
	                            <?php }
	                            if (strpos($sales_config, ',Tasks,') !== false) { ?>
	                                <a id="nav_sales_tasks" href="#sales_tasks" onclick="jumpTab('sales_tasks'); return false;"><li class=""><?= SALES_NOUN ?>: Tasks</li></a>
	                            <?php }
	                            if (strpos($sales_config, ',Time,') !== false) { ?>
	                                <a id="nav_sales_time" href="#sales_time" onclick="jumpTab('sales_time'); return false;"><li class=""><?= SALES_NOUN ?>: Time Tracking</li></a>
	                            <?php }
	                        } ?>
	                    <?php } ?>
					</ul>
				</div>
			</div><!-- .tile-sidebar -->
	<?php } ?>
	<?php if(!empty($_GET['section'])) { ?>
		<script>
		$(document).ready(function() {
			setTimeout(function() {
				jumpTab('<?= $_GET['section'] ?>');
			},250);
		});
		</script>
	<?php } ?>
	<div class='scale-to-fill has-main-screen <?= !IFRAME_PAGE && !isset($mandatory_config) ? 'hide-titles-mob' : '' ?>'>
		<?php if(!isset($mandatory_config)) { ?>
			<div id='view_profile' style='<?= ($_GET['edit'] > 0 && $_GET['profile'] != 'false' && !isset($_GET['view_checklist']) && !isset($_GET['edit_checklist']) && !isset($_GET['list_checklists']) ? '' : 'display:none;') ?>'>
				<div class="main-screen standard-dashboard-body" <?= IFRAME_PAGE ? 'style="background: none;"' : '' ?>>
					<?php include('contact_profile.php'); ?>
				</div>
			</div>
		<?php } ?>
		<?php if (in_array('Checklist', $field_config)) { ?>
			<div id='view_checklist' style='padding: 0.5em; <?= ($_GET['edit'] > 0 && (isset($_GET['view_checklist']) || isset($_GET['edit_checklist'])) || isset($_GET['list_checklists'])) ? '' : 'display:none;' ?>'>
				<div class="main-screen double-pad-top">
					<?php include('edit_addition_checklist.php'); ?>
				</div>
			</div>
		<?php }
//} ?>

            <div id='edit_profile' class="<?= $slider_layout == 'full' ? 'iframe_edit' : '' ?>" style='<?= ($_GET['edit'] > 0 && !isset($mandatory_config) && $_GET['profile'] != 'false' && $_GET['fields'] != 'all_fields') || (!isset($mandatory_config) && IFRAME_PAGE && $_GET['edit'] != 'new' && $_GET['profile'] != 'false') ? 'display:none;' : '' ?>'>
                <!--<div <?= $_GET['fields'] == 'all_fields' ? '' : 'class="scale-to-fill tile-content" style="width:calc(100% - 20px);"' ?>>-->
                <div class="main-screen standard-dashboard-body">
                    <div class="standard-dashboard-body-title" style="<?= $_GET['fields'] == 'fields_only' ? 'display:none;' : '' ?>">
                        <div class="row">
                            <div class="col-sm-12"><h3><?= ($_GET['edit'] > 0) ? 'Edit' : 'Add' ?> <?= !empty($_GET['category']) ? $_GET['category'] : ($folder_label != 'Contacts' ? $folder_label : CONTACTS_NOUN) ?></h3></div>
                        </div>
                    </div>
                    <div class="standard-dashboard-body-content">
                    <div class="dashboard-item dashboard-item2" <?= $_GET['fields'] == 'all_fields' ? 'style="min-height:100vh;"' : '' ?>>
                        <?php if(!isset($mandatory_config)) {
                        	$contact['category'] = get_contact($dbc, $_GET['edit'], 'category'); ?>
                            <div class="form-horizontal double-pad-top" data-tab-name="mandatory" data-locked="held">
                                <div class="form-group">
                                    <label class="col-sm-4 control-label"><?= CONTACTS_TILE ?> Category:</label>
                                    <div class="col-sm-8">
										<?php if($folder_name == 'staff') {
											$each_tab = ['Staff'];
										} else {
											$each_tab = explode(',',get_config($dbc, $folder_name.'_tabs'));
										} ?>
										<?php if((!IFRAME_PAGE && !isset($_GET['fields'])) || !in_array($_GET['category'],$each_tab)) { ?>
											<select name="category" data-field="category" data-table="contacts" class="form-control chosen-select-deselect"><option></option>
												<?php foreach ($each_tab as $cat_tab) {
													echo "<option ".($contact['category'] == $cat_tab || (empty($contact['category']) && $_GET['category'] == $cat_tab) ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
												}
												if(!empty($contact['category']) && !in_array($contact['category'], $each_tab)) {
													echo "<option selected value='". $contact['category']."'>".$contact['category'].'</option>';
												} ?>
											</select>
										<?php } else { ?>
											<input class="form-control" name="category" value="<?= $_GET['category'] ?>" readonly data-field="category" data-table="contacts">
										<?php } ?>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <?php $multiple_categories = get_config($dbc, $folder_name.'_multiple_categories');
                            if($multiple_categories == 1 && $folder_name != 'staff' && (!empty($contact['category']) || !empty($_GET['category']))) {
                            	$contacts_sync = explode(',',get_contact($dbc, $contactid, 'contacts_sync'));
                            	foreach($contacts_sync as $sync_i => $contact_sync) {
                            		if($contact_sync == $contactid) {
                            			unset($contacts_sync[$sync_i]);
                            		}
                            	}
                            	if(empty($contacts_sync)) {
                            		$contacts_sync[] = '';
                            	}
                            	foreach($contacts_sync as $contact_sync) {
                        			$other_cat = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$contact_sync'")); ?>
	                            	<div class="form-group multi_cat_block" data-contactid="<?= $other_cat['contactid'] ?>">
	                            		<label class="col-sm-4 control-label">Other Category:</label>
	                            		<div class="col-sm-7">
	                            			<select name="multiple_categories[]" data-placeholder="Select a Category..." onchange="multipleCategories(this);" class="chosen-select-deselect form-control"><option></option>
	                            				<?php foreach($each_tab as $cat_tab) {
													echo "<option ".($other_cat['category'] == $cat_tab ? 'selected' : '')." value='". $cat_tab."'>".$cat_tab.'</option>';
	                            				} ?>
	                            			</select>
	                            		</div>
	                            		<div class="col-sm-1">
					                        <img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addMultipleCategory();">
					                        <img src="../img/remove.png" class="inline-img pull-right" onclick="removeMultipleCategory(this);">
	                            		</div>
	                                    <div class="clearfix"></div>
	                            	</div>
                        		<?php } ?>
                            <?php } ?>
                        <?php } ?>
                        <?php if((!empty($contact['category']) || !empty($_GET['category']))) { ?>
	                        <?php foreach($field_config as $field_name) {
								if(substr($field_name, 0, 4) == 'acc_') {
									foreach($tab_list as $tab_label => $tab_data) {
										if($field_name == 'acc_'.$tab_data[0] && $tab_label != 'Checklist' && !in_array($tab_data[0], $subtabs_hidden) && in_array_any($tab_data[1],$field_config) && (in_array($tab_data[0], $contact_subtabs) || empty($contact_subtabs))) { ?>
											<div data-tab-name='<?= $tab_data[0] ?>' data-locked='' id="<?= $tab_data[0] ?>" class="scroll-section">
												<hr>
												<?php include('edit_section.php'); ?>
											</div>
										<?php }
									}
								}
							}
							foreach($tab_list as $tab_label => $tab_data) {
								if(in_array_any($tab_data[1],$field_config) && !in_array('acc_'.$tab_data[0],$field_config) && $tab_data[0] != 'sibling_information' && $tab_label != 'Checklist' && !in_array($tab_data[0], $subtabs_hidden) && in_array_any($tab_data[1],$field_config) && (in_array($tab_data[0], $contact_subtabs) || empty($contact_subtabs))) { ?>
									<div data-tab-name='<?= $tab_data[0] ?>' data-locked='' id="<?= $tab_data[0] ?>" class="scroll-section">
										<hr>
										<?php include('edit_section.php'); ?>
									</div>
								<?php }
							}
							if(in_array('Attached Contact Forms as Subtabs',$field_config)) {
								$contact_forms = mysqli_query($dbc, "SELECT * FROM `user_forms` WHERE CONCAT(',',`assigned_tile`,',') LIKE '%,attach_contact,%' AND `deleted` = 0 AND (CONCAT(',',`attached_contacts`,',') LIKE '%,$contactid,%' OR (CONCAT(',',`attached_contacts`,',') LIKE '%,ALL_CONTACTS%,' AND (CONCAT(',',`attached_contact_categories`,',') LIKE '%,$current_type,%' OR IFNULL(`attached_contact_categories`,'') = ''))) AND `is_template` = 0 ORDER BY `name`");
								while($contact_form = mysqli_fetch_assoc($contact_forms)) { ?>
									<div data-tab-name='contactform_<?= $contact_form['form_id'] ?>' data-locked='' id="contactform_<?= $contact_form['form_id'] ?>" class="scroll-section">
										<hr>
										<?php $_GET['user_form_id'] = $contact_form['form_id'];
										include('../Contacts/edit_addition_contact_forms.php'); ?>
									</div>
								<?php }
							}
	                        if ((in_array('Sales Lead Details',$field_config) || in_array($contact['category'],['Sales Lead','Sales Leads'])) && $sales_lead_id > 0 && vuaed_visible_function($dbc, 'sales')) {
	                            $value_config = $sales_config = get_field_config($dbc, 'sales'); ?>
	                            <input type="hidden" name="salesid" value="<?= $sales_lead_id ?>">
	                            <h3><?= SALES_NOUN ?></h3>
	                            <?php $_GET['id'] = $sales_lead_id;
	                            if (strpos($sales_config, ',Staff Information,') !== false) { ?>
									<div data-tab-name='sales_staff_info' data-locked='' id="sales_staff_info" class="scroll-section">
										<?php include('../Sales/details_staff_info.php'); ?>
									</div><hr />
	                            <?php }
	                            if (strpos($sales_config, ',Next Action,') !== false) { ?>
									<div data-tab-name='sales_next_action' data-locked='' id="sales_next_action" class="scroll-section">
										<?php include('../Sales/details_next_action.php'); ?>
									</div><hr />
	                            <?php }
	                            if (strpos($sales_config, ',Service,') !== false) { ?>
									<div data-tab-name='sales_services' data-locked='' id="sales_services" class="scroll-section">
										<?php include('../Sales/details_services.php'); ?>
									</div><hr />
	                            <?php }
	                            if (strpos($sales_config, ',Products,') !== false) { ?>
									<div data-tab-name='sales_products' data-locked='' id="sales_products" class="scroll-section">
										<?php include('../Sales/details_products.php'); ?>
									</div><hr />
	                            <?php }
	                            if (strpos($sales_config, ',Reference Documents,') !== false) { ?>
									<div data-tab-name='sales_ref_docs' data-locked='' id="sales_ref_docs" class="scroll-section">
										<?php include('../Sales/details_ref_docs.php'); ?>
									</div><hr />
	                            <?php }
	                            if (strpos($sales_config, ',Marketing Material,') !== false) { ?>
									<div data-tab-name='sales_marketing' data-locked='' id="sales_marketing" class="scroll-section">
										<?php include('../Sales/details_marketing.php'); ?>
									</div><hr />
	                            <?php }
	                            if (strpos($sales_config, 'Information Gathering,') !== false) { ?>
									<div data-tab-name='sales_ingo_gathering' data-locked='' id="sales_ingo_gathering" class="scroll-section">
										<?php include('../Sales/details_info_gathering.php'); ?>
									</div><hr />
	                            <?php }
	                            if (strpos($sales_config, ',Estimate,') !== false) { ?>
									<div data-tab-name='sales_estimate' data-locked='' id="sales_estimate" class="scroll-section">
										<?php include('../Sales/details_estimates.php'); ?>
									</div><hr />
	                            <?php }
	                            if (strpos($sales_config, ',Lead Notes,') !== false) { ?>
									<div data-tab-name='sales_lead_notes' data-locked='' id="sales_lead_notes" class="scroll-section">
										<?php include('../Sales/details_lead_notes.php'); ?>
									</div><hr />
	                            <?php }
	                            if (strpos($sales_config, ',Tasks,') !== false) { ?>
									<div data-tab-name='sales_tasks' data-locked='' id="sales_tasks" class="scroll-section">
										<?php include('../Sales/details_tasks.php'); ?>
									</div><hr />
	                            <?php }
	                            if (strpos($sales_config, ',Time,') !== false) { ?>
									<div data-tab-name='sales_time' data-locked='' id="sales_time" class="scroll-section">
										<?php include('../Sales/details_time.php'); ?>
									</div><hr />
	                            <?php }
	                        } ?>
	                        <div class="clearfix"></div>
							<?php if(IFRAME_PAGE && $_GET['edit'] > 0) {
								echo '<a href="'.($_GET['change'] != 'true' ? 'contacts_inbox.php?list='.$_GET['category'] : '/index.php').'" class="btn brand-btn pull-right">Update</a>';
							} else {
								echo '<a href="'.($_GET['change'] != 'true' ? 'contacts_inbox.php?list='.$_GET['category'] : '/index.php').'" class="btn brand-btn pull-right">Add</a>';
							} ?>
						<?php } ?>
                        <div class="clearfix"></div>
                    </div><!-- .main-screen-white -->
                    </div><!-- .standard-dashboard-body-content -->
                </div><!-- .main-screen .standard-dashboard-body -->
            </div><!-- #edit_profile -->
<?php if(!IFRAME_PAGE && !isset($_GET['fields'])) { ?>
            <div id='view_history' style='display:none;'>
                <?php include('contact_history.php'); ?>
            </div>
        </div>
        <div class="clearfix"></div>
    </div><!-- .tile-container -->
<?php } ?>
<input type="hidden" name="contactid" value="<?= $_GET['edit'] ?>">
<input type="hidden" name="category" value="<?= $_GET['category'] ?>">
<?php function contact_category_call($dbc, $select_id, $select_name, $contact_category_value, $data_field, $data_row_id, $disabled) {
	$contact_tabs = get_config($dbc, FOLDER_NAME.'_tabs');
    if(get_software_name() == 'breakthebarrier') {
        str_replace('Business','Program/Site',$contact_tabs);
    } else if(get_software_name() == 'highland') {
        str_replace('Business','Customer',$contact_tabs);
    } ?>
	<script type="text/javascript">
	$(document).on('change', 'select[name="<?= $select_name ?>"]', function() { selectContactCategory(this); });
	</script>
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label">Contact Category:</label>
        <div class="col-sm-8">
            <select <?php echo $disabled; ?> data-placeholder="Choose a Category..." id="<?php echo $select_id; ?>" name="<?php echo $select_name; ?>" data-field="<?php echo $data_field; ?>" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?php echo $data_row_id; ?>" data-contactid-field="support_contact" data-contactid-category-field="support_contact_category" data-exact-name="1" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php $each_tab = explode(',', $contact_tabs);
                foreach ($each_tab as $cat_tab) {
                    ?>
                    <option <?php if (strpos($contact_category_value, $cat_tab) !== FALSE) {
                    echo " selected"; } ?> value='<?php echo $cat_tab; ?>'><?php echo $cat_tab; ?></option>
                <?php }
              ?>
            </select>
        </div>
    </div>
<?php } ?>

<?php
$all_contacts = [];
function contact_call($dbc, $select_id, $select_name, $contact_value,$multiple, $from_contact, $data_field, $data_row_id, $disabled) { ?>
	<script type="text/javascript">
	$(document).on('change', 'select[name="<?= $select_name ?>"]', function() { checkContactChange(this); });
	</script>
    <div class="form-group">
        <label for="fax_number" class="col-sm-4 control-label">Contact:</label>
        <div class="col-sm-8">

            <select <?php echo $disabled; ?> <?php echo $multiple; ?> data-placeholder="Choose a Contact..." name="<?php echo $select_name; ?>" data-field="<?php echo $data_field; ?>" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?php echo $data_row_id; ?>" id="<?php echo $select_id; ?>" data-value="<?= $contact_value ?>" data-category="<?= $from_contact ?>" data-contactid-field="support_contact" data-contactid-category-field="support_contact_category" data-exact-name="1" class="chosen-select-deselect form-control" width="380">
              <option value="NEW_CONTACT">Add New Contact</option>
            </select>
            <input type="text" name="<?= str_replace('[]','',$select_name) ?>_new_contact<?= preg_replace('/[^\[\]]/','',$select_name) ?>" data-field="<?php echo $data_field; ?>" data-table="individual_support_plan" data-row-field="individualsupportplanid" data-row-id="<?php echo $data_row_id; ?>" data-contactid-field="support_contact" class="form-control" style="display:none;">
        </div>
    </div>
<?php } ?>
