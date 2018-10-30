<?php error_reporting(0);
include_once('../include.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($projectid)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$projecttype = filter_var($_GET['projecttype'],FILTER_SANITIZE_STRING);
	foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
		if($tile == 'project' || $tile == config_safe_str($type_name)) {
			$project_tabs[config_safe_str($type_name)] = $type_name;
		}
	}
	$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
}
$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid' AND '$projectid' > 0"));
$ticket_security = get_security($dbc, 'tickets');
$status_list = explode('#*#',get_config($dbc, 'project_status')); ?>
<script>
$(document).ready(function() {
	filterContacts();
	businessFilter();
    <?php if ( !empty($salesid) && $_GET['edit']==0 ) { ?>
        $('select[name=businessid],select[name="clientid[]"]').first().change();
        var checkid = setInterval(function() {
            if($('[name=projectid]').val() > 0) {
                clearInterval(checkid);
                $('select[name=businessid],select[name="clientid[]"]').change();
            }
        }, 500);
    <?php } ?>
});
$(document).on('change', 'select[name="region"]', function() { filterContacts(); });
$(document).on('change', 'select[name="location"]', function() { filterContacts(); });
$(document).on('change', 'select[name="classification"]', function() { filterContacts(); });
$(document).on('change', 'select[name="businessid"]', function() { businessFilter(); });
$(document).on('change', 'select[name="clientid[]"]', function() { setBusiness(this); });
$(document).on('change', 'select[name="siteid"]', function() { setBusiness(this); });
$(document).on('change', 'select[name="projecttype"]', function() { apply_default_template(); });
$(document).on('change', 'select[name="path_templates[]"]', function() { return apply_template(); });
function filterContacts() {
	$('[name=businessid] option').show();
	$('[name="clientid[]"] option').show();
	$('[name=siteid] option').show();
	var region = $('[name=region]').val();
	if(region != '' && region != undefined && region != null) {
		$('[name=businessid] option').each(function() {
			var this_region = $(this).data('region');
			if(this_region == undefined) {
				this_region = '';
			}
			if(this_region != '' && this_region.indexOf(region) == -1) {
				$(this).hide();
			}
		});
		$('[name="clientid[]"] option').each(function() {
			var this_region = $(this).data('region');
			if(this_region == undefined) {
				this_region = '';
			}
			if(this_region != '' && this_region.indexOf(region) == -1) {
				$(this).hide();
			}
		});
		$('[name=siteid] option').each(function() {
			var this_region = $(this).data('region');
			if(this_region == undefined) {
				this_region = '';
			}
			if(this_region != '' && this_region.indexOf(region) == -1) {
				$(this).hide();
			}
		});
	}
	var location = $('[name=location]').val();
	if(location != '' && location != undefined && location != null) {
		$('[name=businessid] option').filter('[data-location!=""][data-location!="'+location+'"]').hide();
		$('[name="clientid[]"] option').filter('[data-location!=""][data-location!="'+location+'"]').hide();
		$('[name=siteid] option').filter('[data-location!=""][data-location!="'+location+'"]').hide();
	}
	$('[name=classification] option:selected').each(function() {
		var classification = $(this).val();
		$('[name=businessid] option').each(function() {
			var this_class = $(this).data('classification');
			if(this_class == undefined) {
				this_class = '';
			}
			if(this_class != '' && this_class.indexOf(classification) == -1) {
				$(this).hide();
			}
		});
		$('[name="clientid[]"] option').each(function() {
			var this_class = $(this).data('classification');
			if(this_class == undefined) {
				this_class = '';
			}
			if(this_class != '' && this_class.indexOf(classification) == -1) {
				$(this).hide();
			}
		});
		$('[name=siteid] option').each(function() {
			var this_class = $(this).data('classification');
			if(this_class == undefined) {
				this_class = '';
			}
			if(this_class != '' && this_class.indexOf(classification) == -1) {
				$(this).hide();
			}
		});
	});
}
function businessFilter() {
	var business = $('[name=businessid]').val();
	$('[name="clientid[]"]').each(function() {
		if(business > 0) {
			$(this).find('option').hide();
			$(this).find('option[data-business='+business+']').show();
			$(this).trigger('change.select2');
		} else {
			$(this).find('option').show();
			$(this).trigger('change.select2');
		}
	});
	if(business > 0) {
		$('[name=siteid]').find('option').hide();
		$('[name=siteid]').find('option[data-business='+business+']').show();
		$('[name=siteid]').trigger('change.select2');
	} else {
		$('[name=siteid]').find('option').show();
		$('[name=siteid]').trigger('change.select2');
	}
}
function setBusiness(contact) {
	var business = $(contact).find('option:selected').data('business');
	if(business != $('[name=businessid]').val() && business > 0) {
		$('[name=businessid]').val(business).trigger('change.select2');
		setTimeout(function() { $('[name=businessid]').change(); }, 1000);
	}
}

function apply_default_template() {
    var default_project_path = $('[name=default_project_path]').val();
    var set_default_path = $('[name=set_default_path]').val();

    var pid = $('[name=projectid]').val();
    if(default_project_path == '' || default_project_path == 0) {
        $.ajax({
            url: 'projects_ajax.php?action=update_path',
            method: 'POST',
            data: {
                projectid: pid,
                path_list: set_default_path,
                path: 'project_path',
                new_path: set_default_path
            },
            success: function(){
                window.location.replace("projects.php?edit="+pid+"&tab=info");
            }
        });
    }
}

function apply_template() {
	if(!($('[name=projectid]').val() > 0)) {
		$('[name=status]').val('Pending').trigger('change.select2').change();
	}
	var path_list = [];
	$('[name="path_templates[]"] option:selected').each(function() {
		path_list.push(this.value);
	});
	var interval = setInterval(function() {
		if($('[name=projectid]').val() > 0) {
			clearInterval(interval);
			$.ajax({
				url: 'projects_ajax.php?action=update_path',
				method: 'POST',
				data: {
					projectid: $('[name=projectid]').val(),
					path: 'project_path',
					path_list: path_list.join(',')
				}
			});
		}
	}, 250);
}
</script>
<?php
$field_mandatory_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_mandatory_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_mandatory_project WHERE type='ALL'"))[0]))));
$default_project_path = get_project($dbc, $projectid, 'project_path');

$default_path = mysqli_fetch_array(mysqli_query($dbc,"SELECT `project_path_milestone` FROM project_path_milestone WHERE milestone='To Do#*#Doing#*#Done'"));
$set_default_path = $default_path['project_path_milestone'];

?>
<input type="hidden" name="projectid" value="<?= $projectid ?>">
<input type="hidden" name="default_project_path" value="<?= $default_project_path ?>">
<input type="hidden" name="set_default_path" value="<?= $set_default_path ?>">
<div id="head_info">
	<h3><?= PROJECT_NOUN ?> Information</h3>
	<?php if (in_array("Information Project Short Name",$value_config)) { ?>
		<div class="form-group">
			<label class="col-sm-4"><?php echo (in_array('Information Project Short Name', $field_mandatory_config) ? '<font color="red">* </font>' : ''); ?><?= PROJECT_NOUN ?> Name:</label>
			<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
				<input name="project_name" value="<?= $project['project_name'] ?>" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" type="text" class="form-control <?php echo (in_array('Information Project Short Name', $field_mandatory_config) ? 'required' : ''); ?>"></p>
			</div>
		</div>
	<?php } ?>

	<?php $contactid_intake = ( isset($_GET['clientid']) ) ? trim($_GET['clientid']) : '';
	$intakeid = ( isset($_GET['intakeid']) ) ? trim($_GET['intakeid']) : '';
    if (!empty($salesid)) {
        $row_sales = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT primary_staff, share_lead, region, location, classification, businessid, contactid, serviceid, productid, marketingmaterialid FROM sales WHERE salesid='$salesid'"));
        $sales_contactids = array_filter(explode(',', $row_sales['contactid']));
    }
	$filters_query = mysqli_query($dbc, "SELECT `contacts`.`region`,`contacts`.`con_locations`,`contacts`.`classification` FROM `project` LEFT JOIN `contacts` ON `project`.`businessid`=`contacts`.`contactid` OR CONCAT(',',`project`.`clientid`,',') LIKE CONCAT('%,',`contacts`.`contactid`,'%,') WHERE `projectid`='$projectid'");
	$region = $location = $classification = [];
	while($filter_info = mysqli_fetch_assoc($filters_query)) {
		foreach(array_filter(explode(',',$filter_info['region'])) as $region_name) {
			$region[] = $region_name;
		}
		$location[] = $filter_info['con_locations'];
		$classification = array_merge($classification,explode(',',$filter_info['classification']));
	}

	if (in_array("Information Contact Region",$value_config)) { ?>
	<div class="form-group">
		<label class="col-sm-4"><?php echo (in_array('Information Contact Region', $field_mandatory_config) ? '<font color="red">* </font>' : ''); ?>Region:</label>
		<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
			<select name="region" multiple id="contact_region" data-placeholder="Select a Region..." class="chosen-select-deselect form-control <?php echo (in_array('Information Contact Region', $field_mandatory_config) ? 'required' : ''); ?>">
				<?php $contact_regions = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`value` SEPARATOR ',') FROM `general_configuration` WHERE `name` LIKE '%_region'"))[0])));
				foreach ($contact_regions as $contact_region) {
					echo "<option ".(in_array($contact_region, $region) ? 'selected' : '')." value='".$contact_region."'>".$contact_region.'</option>';
				} ?>
			</select>
		</div>
	</div>
	<?php } ?>

	<?php if (in_array("Information Contact Location",$value_config)) { ?>
	<div class="form-group">
		<label class="col-sm-4"><?php echo (in_array('Information Contact Location', $field_mandatory_config) ? '<font color="red">* </font>' : ''); ?>Location:</label>
		<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
			<select name="location" id="contact_location" data-placeholder="Select a Location..." class="chosen-select-deselect form-control <?php echo (in_array('Information Contact Location', $field_mandatory_config) ? 'required' : ''); ?>">
				<option value=''></option>
				<?php $contact_locations = array_filter(array_unique(explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(DISTINCT `con_locations` SEPARATOR ',') FROM `field_config_contacts`"))[0])));
				foreach ($contact_locations as $contact_location) {
					echo "<option ".(in_array($contact_location, $location) ? 'selected' : '')." value='".$contact_location."'>".$contact_location.'</option>';
				} ?>
			</select>
		</div>
	</div>
	<?php } ?>

	<?php if (in_array("Information Contact Classification",$value_config)) { ?>
	<div class="form-group">
		<label class="col-sm-4"><?php echo (in_array('Information Contact Classification', $field_mandatory_config) ? '<font color="red">* </font>' : ''); ?>Classification:</label>
		<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
			<select name="classification1" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid"  id="classification1" data-placeholder="Select a Classification..." class="chosen-select-deselect form-control <?php echo (in_array('Information Contact Classification', $field_mandatory_config) ? 'required' : ''); ?>">
				<?php $contact_classifications = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `value` FROM `general_configuration` WHERE `name` LIKE '%_classification'"));
                $project_class = explode(',',$contact_classifications['value']);
				foreach ($project_class as $project_classification) {
					echo "<option ".($project['classification1'] == $project_classification ? ' selected' : '')." value='".$project_classification."'>".$project_classification.'</option>';
				} ?>
			</select>
		</div>
	</div>
	<?php }?>

	<?php if (in_array("Information Project Type",$value_config)) { ?>
	<div class="form-group">
		<label class="col-sm-4"><?php echo (in_array('DB Type', $field_mandatory_config) ? '<font color="red">* </font>' : ''); ?><?= PROJECT_NOUN ?> Type:</label>
		<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
			<select name="projecttype" id="projecttype" data-placeholder="Select a Type..." data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" class="chosen-select-deselect form-control <?php echo (in_array('Information Project Type', $field_mandatory_config) ? 'required' : ''); ?>">
				<option value=''></option>
				<?php $project_tabs = get_config($dbc, 'project_tabs');
				$project_tabs = explode(',',$project_tabs);
				foreach($project_tabs as $item) {
					$var_name = config_safe_str($item);
					if($var_name == 'client' || check_subtab_persmission($dbc, 'project', ROLE, $var_name) == 1) {
						echo "<option ".($project['projecttype'] == $var_name || $project['projecttype'] == $item ? ' selected' : '')." value='$var_name'>$item</option>";
					}
				} ?>
			</select>
		</div>
	</div>
	<?php } ?>

	<div class="form-group">
		<label class="col-sm-4"><?php echo (in_array('DB Status', $field_mandatory_config) ? '<font color="red">* </font>' : ''); ?><?= PROJECT_NOUN ?> Status:</label>
		<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
			<?php if(approval_visible_function($dbc, 'project') > 0 || $project['status'] != 'Pending') { ?>
				<select name="status" id="status" data-placeholder="Select a <?= PROJECT_NOUN ?> Status..." data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" class="chosen-select-deselect form-control <?php echo (in_array('DB Status', $field_mandatory_config) ? 'required' : ''); ?>">
					<option></option>
					<option <?= 'Pending' == $project['status'] ? 'selected' : '' ?> value="Pending">Pending</option>
					<?php foreach($status_list as $status_name) { ?>
						<option <?= $status_name == $project['status'] ? 'selected' : '' ?> value="<?= $status_name ?>"><?= $status_name ?></option>
					<?php } ?>
					<option <?= 'Archive' == $project['status'] ? 'selected' : '' ?> value="Archive">Archive</option>
				</select>
			<?php } else {
				echo $project['status'];
			} ?>
		</div>
	</div>

	<?php if (in_array("Information Business",$value_config)) { ?>
	<div class="form-group">
		<label class="col-sm-4"><?php echo (in_array('Information Business', $field_mandatory_config) ? '<font color="red">* </font>' : ''); ?><?= BUSINESS_CAT ?>:</label>
		<div class="col-sm-7 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">

			<select name="businessid" id="businessid" data-placeholder="Select a <?= BUSINESS_CAT ?>..." data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" class="chosen-select-deselect form-control <?php echo (in_array('Information Business', $field_mandatory_config) ? 'required' : ''); ?>" onchange="if(this.value > 0) { $(this).closest('.form-group').find('.current').show(); $(this).closest('.form-group').find('.new').hide(); } else { $(this).closest('.form-group').find('.current').hide(); $(this).closest('.form-group').find('.new').show(); }">

				<option></option>
				<?php foreach(sort_contacts_query(mysqli_query($dbc,"SELECT contactid, name, region, con_locations, classification FROM contacts WHERE (category='".BUSINESS_CAT."' AND deleted=0 AND `status`=1) OR `contactid`='".$project['businessid']."'")) as $business) {
					echo "<option ".(($project['businessid'] == $business['contactid'] || ($business['contactid'] == $row_sales['businessid'] && $_GET['edit']==0)) ? 'selected' : '')." value='". $business['contactid']."' data-region='".$business['region']."' data-location='".$business['con_locations']."' data-classification='".$business['classification']."'>".$business['name'].'</option>';
				} ?>
			</select>
		</div>
		<div class="col-sm-1">
			<?php if($security['edit'] > 0) { ?>
				<img class="inline-img pull-right no-toggle new" src="../img/icons/ROOK-add-icon.png" title="Create a new <?= BUSINESS_CAT ?> for this <?= PROJECT_NOUN ?>" onclick="newContact(this, '<?= BUSINESS_CAT ?>');" style="<?= $project['businessid'] > 0 ? 'display:none;' : '' ?>">
			<?php } ?>
			<img class="inline-img pull-right no-toggle current" src="../img/person.PNG" title="View this contact's profile" onclick="viewProfile(this, '<?= BUSINESS_CAT ?>');" style="<?= $project['businessid'] > 0 ? '' : 'display:none;' ?>">
		</div>
	</div>
	<?php } ?>

	<?php if (in_array("Information Contact",$value_config)) {
		$contact_list = array_filter(array_unique(explode(',',$project['clientid'].','.$contactid_intake.','.$intakeid)));
		for($i = 0; $i < count($contact_list) || $i == 0; $i++) {
			$clientid = $contact_list[$i]; ?>
			<div class="form-group">
				<label class="col-sm-4"><?php echo (in_array('DB Contact', $field_mandatory_config) ? '<font color="red">* </font>' : ''); ?><?= CONTACTS_TILE ?>:</label>
				<div class="col-sm-7 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
					<select name="clientid[]" data-placeholder="Select a <?= CONTACTS_NOUN ?>..." data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" class="chosen-select-deselect form-control <?php echo (in_array('DB Contact', $field_mandatory_config) ? 'required' : ''); ?>" onchange="if(this.value > 0) { $(this).closest('.form-group').find('.current').show(); $(this).closest('.form-group').find('.new').hide(); } else { $(this).closest('.form-group').find('.current').hide(); $(this).closest('.form-group').find('.new').show(); }">
						<option></option>
						<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT contactid, first_name, last_name, category, region, con_locations, classification, businessid FROM contacts WHERE (category!='Business' AND deleted=0 AND `status` > 0) OR `contactid`='$clientid'")) as $contact) {
							echo "<option ".(($clientid == $contact['contactid'] || (in_array($contact['contactid'],$sales_contactids) && $_GET['edit']==0)) ? 'selected' : '')." value='". $contact['contactid']."' data-region='".$contact['region']."' data-location='".$contact['con_locations']."' data-classification='".$contact['classification']."' data-business='".$contact['businessid']."'>".$contact['first_name'].' '.$contact['last_name'].'</option>';
							if($clientid == $contact['contactid'] && $contact['region'] != '') { ?>

							<?php }
							if($clientid == $contact['contactid'] && $contact['con_locations'] != '') { ?>

							<?php }
							if($clientid == $contact['contactid'] && $contact['classification'] != '') { ?>
								<script> $('[name=classification]').val('<?= $contact['classification'] ?>'); </script>
							<?php }
						} ?>
					</select>
				</div>
				<div class="col-sm-1">
					<?php if($security['edit'] > 0) { ?>
						<img class="inline-img pull-right" src="../img/remove.png" onclick="removeClient(this);">
						<img class="inline-img pull-right no-toggle current" src="../img/icons/ROOK-add-icon.png" title="Select an additional Contact for this <?= PROJECT_NOUN ?>" onclick="addClient();" style="<?= $clientid > 0 ? '' : 'display:none;' ?>">
					<?php } ?>
					<img class="inline-img pull-right no-toggle current" src="../img/person.PNG" title="View this contact's profile" onclick="viewProfile(this, '%');" style="<?= $clientid > 0 ? '' : 'display:none;' ?>">
					<?php if($security['edit'] > 0) { ?>
						<img class="inline-img pull-right no-toggle new" src="../img/icons/ROOK-add-icon.png" title="Create a new Contact for this <?= PROJECT_NOUN ?>" onclick="newContact(this, '%');" style="<?= $clientid > 0 ? 'display:none;' : '' ?>">
					<?php } ?>
				</div>
			</div>
		<?php } ?>
		<script>
		function addClient() {
			var last = $('[name="clientid[]"]').last().closest('.form-group');
			var clone = last.clone();
			clone.find('select').val('');
			resetChosen(clone.find('.chosen-select-deselect'));
			last.after(clone);
			$('[data-table]').change(saveField);
		}
		function removeClient(img) {
			if($('[name="clientid[]"]').length <= 1) {
				addClient();
			}
			$(img).closest('.form-group').remove();
			$('[name="clientid[]"]').last().change();
		}
		</script>
	<?php } ?>

	<?php if (in_array("Information Site",$value_config)) { ?>
	<div class="form-group">
		<label class="col-sm-4"><?php echo (in_array('Information Site', $field_mandatory_config) ? '<font color="red">* </font>' : ''); ?>Site:</label>
		<div class="col-sm-7 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">

			<select name="siteid" id="siteid" data-placeholder="Select a Site..." data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" class="chosen-select-deselect form-control <?php echo (in_array('Information Site', $field_mandatory_config) ? 'required' : ''); ?>" onchange="if(this.value > 0) { $(this).closest('.form-group').find('.current').show(); $(this).closest('.form-group').find('.new').hide(); } else { $(this).closest('.form-group').find('.current').hide(); $(this).closest('.form-group').find('.new').show(); }">

				<option></option>
				<?php foreach(sort_contacts_query(mysqli_query($dbc,"SELECT contactid, businessid, IF(IFNULL(`display_name`,'')='',`site_name`,`display_name`) display, region, con_locations, classification FROM contacts WHERE (category='Sites' AND deleted=0 AND `status` > 0 AND '".$project['businessid']."' IN (`businessid`,'')) OR `contactid`='".$project['siteid']."'")) as $site) {
					echo "<option ".($project['siteid'] == $site['contactid'] ? 'selected' : '')." value='". $site['contactid']."' data-region='".$site['region']."' data-location='".$site['con_locations']."' data-classification='".$site['classification']."' data-business='".$site['businessid']."'>".$site['display'].'</option>';
				} ?>
			</select>
		</div>
		<div class="col-sm-1">
			<?php if($security['edit'] > 0) { ?>
				<img class="inline-img pull-right no-toggle new" src="../img/icons/ROOK-add-icon.png" title="Create a new <?= SITES_CAT ?> for this <?= PROJECT_NOUN ?>" onclick="newContact(this, '<?= SITES_CAT ?>');" style="<?= $project['siteid'] > 0 ? 'display:none;' : '' ?>">
			<?php } ?>
			<img class="inline-img pull-right no-toggle current" src="../img/person.PNG" title="View this Site's profile" onclick="viewProfile(this, '<?= SITES_CAT ?>');" style="<?= $project['siteid'] > 0 ? '' : 'display:none;' ?>">
		</div>
	</div>
	<?php } ?>

	<?php if (in_array("Information Rate Card",$value_config)) { ?>
	<div class="form-group">
		<label class="col-sm-4"><?php echo (in_array('Information Rate Card', $field_mandatory_config) ? '<font color="red">* </font>' : ''); ?>Rate Card:</label>
		<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
			<select name="ratecardid" id="ratecardid" data-placeholder="Select a Rate Card..." data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" class="chosen-select-deselect form-control <?php echo (in_array('Information Rate Card', $field_mandatory_config) ? 'required' : ''); ?>">
				<option value=''></option>
				<?php $query = mysqli_query($dbc,"SELECT ratecardid, rate_card_name FROM rate_card WHERE on_off=1 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION SELECT CONCAT('company*',MIN(`companyrcid`)), `rate_card_name` FROM `company_rate_card` WHERE `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') GROUP BY `rate_card_name`");
				while($row = mysqli_fetch_array($query)) {
					echo "<option ".($project['ratecardid'] == $row['ratecardid'] ? 'selected' : '')." value='". $row['ratecardid']."'>".$row['rate_card_name'].'</option>';
				} ?>
			</select>
		</div>
	</div>
	<?php } ?>

	<?php if (in_array("Information AFE",$value_config)) { ?>
		<div class="form-group">
			<label class="col-sm-4"><?php echo (in_array('Information AFE', $field_mandatory_config) ? '<font color="red">* </font>' : ''); ?>AFE #:</label>
			<div class="col-sm-8 <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>">
				<input name="afe_number" value="<?= $project['afe_number'] ?>" data-table="project" data-id="<?= $project['projectid'] ?>" data-id-field="projectid" type="text" class="form-control <?php echo (in_array('Information AFE', $field_mandatory_config) ? 'required' : ''); ?>"></p>
			</div>
		</div>
	<?php } ?>
	<div class="clearfix"></div>
</div>
