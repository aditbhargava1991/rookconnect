<script type="text/javascript">
current_business = '<?= $businessid ?>';
var businessFilter = function() {
	var option = $('[name=businessid] option:selected');
	if(option.val() > 0) {
		if($('[name=projectid] option[data-business='+option.val()+']').length > 0) {
			$('[name=projectid] option').hide().filter('[data-business='+option.val()+']').show();
		} else {
			$('[name=projectid] option').show();
		}
		$('[name=projectid]').trigger('change.select2');
		setTimeout(function() {
			$('[name=projectid]').change();
		},1000);
		$('[name=clientid] option').hide().filter('[data-business='+option.val()+']').show();
		$('[name=clientid]').trigger('change.select2');
		$('[name=rate_card] option[data-business]').hide().filter('[data-business='+option.val()+']').show();
		$('[name=rate_card]').trigger('change.select2');
        if($(option).data('region') != '' && $(option).data('region') != undefined) {
            $('[name=region]').val($(option).data('region')).change();
        }
        if($(option).data('location') != '' && $(option).data('location') != undefined) {
            $('[name=con_location]').val($(option).data('location')).change();
        }
        if($(option).data('classification') != '' && $(option).data('classification') != undefined) {
            $('[name=classification]').val($(option).data('classification')).change();
        }
	} else {
		$('[name=projectid] option').show();
		$('[name=projectid]').trigger('change.select2');
		$('[name=clientid] option').show();
		$('[name=clientid]').trigger('change.select2');
		$('[name=rate_card] option').show();
		$('[name=rate_card]').trigger('change.select2');
	}
	<?php if(strpos($value_config, ',Delivery Pickup,') !== FALSE) { ?>
		$.ajax({
			url: 'ticket_ajax_all.php?action=business_address_details&business='+option.val(),
			dataType: 'json',
			success: function(response) {
				var address = response;
				var interval = setInterval(function() {
					if(ticketid > 0) {
						clearInterval(interval);
						$('[name=pickup_name]').val(response.name).change();
						$('[name=pickup_address]').val(response.mailing_address).change();
						$('[name=pickup_city]').val(response.city).change();
						$('[name=pickup_postal_code]').val(response.postal_code).change();
						$('[name=pickup_link]').val(response.google_maps_address).change();
					}
				},500);
			}
		});
	<?php } ?>

	<?php if(strpos($value_config, ',Service Rate Card,') !== FALSE && strpos($value_config, ',PI Business,') !== FALSE) { ?>
		$.ajax({
			url: 'ticket_ajax_all.php?action=business_services&agentid='+$('[name=agentid]').val()+'&carrierid='+$('[id$=transport_details] [name=carrier]').val()+'&originvendor='+$('[id$=transport_origin] [name=vendor]').val()+'&destvendor='+$('[id$=transport_destination] [name=vendor]').val()+'&business='+option.val(),
			dataType: 'html',
			success: function(response) {
				$('.serviceid').each(function() {
					var service = this.value;
					$(this).html(response).val(service).trigger('change.select2');
				});
			}
		});
	<?php  } ?>

	if(typeof filterRegLocClass == 'function') {
		filterRegLocClass(1);
	}
}
var clientFilter = function() {
	if(ticketid > 0) {
		var option = $('[name=clientid] option:selected');
		if(option.val() > 0) {
			if(!($('[name=businessid]').val > 0) && option.data('business') > 0 && $('[name=businessid]').val() != option.data('business')) {
				$('[name=businessid]').val(option.data('business')).trigger('change.select2').change();
			}
			$('[name=projectid] option').hide().filter('[data-client*='+option.val()+'],[data-business='+$('[name=businessid] option:selected').val()+']').show();
			$('[name=projectid]').trigger('change.select2');
            if($(option).data('region') != '' && $(option).data('region') != undefined) {
                $('[name=region]').val($(option).data('region')).change();
            }
            if($(option).data('location') != '' && $(option).data('location') != undefined) {
                $('[name=con_location]').val($(option).data('location')).change();
            }
            if($(option).data('classification') != '' && $(option).data('classification') != undefined) {
                $('[name=classification]').val($(option).data('classification')).change();
            }
		}
		if(typeof filterRegLocClass == 'function') {
			filterRegLocClass(1);
		}
		if(typeof getCustomerServiceTemplate == 'function') {
			getCustomerServiceTemplate();
		}
	} else {
		setTimeout(clientFilter, 250);
	}
}
var projectFilter = function() {
	if(ticketid > 0) {
		var option = $('[name=projectid] option:selected');
		if(option.val() > 0) {
			if(!($('[name=businessid]').val() > 0) && option.data('business') > 0 && $('[name=businessid]').val() != option.data('business')) {
				$('[name=businessid]').val(option.data('business')).change().trigger('change.select2');
			}
			if(!($('[name=clientid]').val() > 0) && option.data('client') != '' && !$('[name=clientid][value="'+option.data('client')+'"]').is(':selected')) {
				$('[name=clientid]').val(option.data('client').toString().split(',')[0]).change().trigger('change.select2');
			}
			if(typeof filterRegLocClass == 'function') {
				filterRegLocClass(1);
			}
		}
	} else {
		setTimeout(projectFilter, 250);
	}
}
</script>
<?php
$value_mandatory_config = explode(',',get_mandatory_field_config($dbc, 'tickets'));
?>
<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>'.('manual' == $force_project ? PROJECT_NOUN : TICKET_NOUN).' Information</h3>') : '' ?>
<?php if (strpos($value_config_all, ','."Con Region".',') !== FALSE && strpos($value_config, ','."Con Region".',') === FALSE) { ?>
    <input type="hidden" name="region" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['region'] ?>">
<?php } ?>
<?php if (strpos($value_config_all, ','."Con Location".',') !== FALSE && strpos($value_config, ','."Con Location".',') === FALSE) { ?>
    <input type="hidden" name="con_location" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['con_location'] ?>">
<?php } ?>
<?php if (strpos($value_config_all, ','."Con Classification".',') !== FALSE && strpos($value_config, ','."Con Classification".',') === FALSE) { ?>
    <input type="hidden" name="classification" data-table="tickets" data-id="<?= $get_ticket['ticketid'] ?>" data-id-field="ticketid" value="<?= $get_ticket['classification'] ?>">
<?php } ?>
<?php foreach($field_sort_order as $field_sort_field) {
	if($access_project == TRUE) { ?>
		<?php if (( strpos($value_config, ',PI Business,') !== false  || ( strpos($value_config, ',PI ') === false) ) && $field_sort_field == 'PI Business' ) {
			$businessid_inserted = true; ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array('PI Business', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><?= BUSINESS_CAT ?> Name:</label>
				<div class="col-sm-7">
					<select name="businessid" id="businessid" data-placeholder="Select a <?= (substr(BUSINESS_CAT, -1)=='s' && substr(BUSINESS_CAT, -2)!='ss') ? substr(BUSINESS_CAT, 0, -1) : BUSINESS_CAT; ?>..." data-category="<?= BUSINESS_CAT ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control <?php echo (in_array('PI Business', $value_mandatory_config) ? 'required' : ''); ?>" width="380">
						<option value=''></option>
						<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `name`, `region`, `con_locations`, `classification` FROM `contacts` WHERE `category`='".BUSINESS_CAT."' AND `deleted`=0")) as $row) { ?>
							<option data-region="<?= $row['region'] ?>" data-location="<?= $row['con_locations'] ?>" data-classification="<?= $row['classification'] ?>" <?= $row['contactid'] == $businessid ? 'selected' : '' ?> value="<?= $row['contactid'] ?>"><?= $row['name'] ?></option>
						<?php } ?>
						<option value="ADD_NEW">Add New <?= BUSINESS_CAT ?></option>
					</select>
				</div>
				<div class="col-sm-1">
					<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="New <?= BUSINESS_CAT ?>" src="../img/icons/ROOK-add-icon.png"></a>
				</div>
			</div>
		<?php } else if(empty($businessid_inserted)) {
			$businessid_inserted = true; ?>
			<input type="hidden" name="businessid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $businessid ?>">
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Name,') !== false && $field_sort_field == 'PI Name' ) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array('PI Name', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><!----> Contact Name:</label>
				<div class="col-sm-7">
					<select name="clientid" id="clientid" data-placeholder="Select a Contact specific to above <?= BUSINESS_CAT ?>..." data-table="tickets" data-id="<?= $ticketid ?>" data-category="<?= get_config($dbc, 'ticket_business_contact_'.$ticket_type) ?: (get_config($dbc, 'ticket_business_contact') ?: '%') ?>" data-id-field="ticketid" class="chosen-select-deselect form-control <?php echo (in_array('PI Name', $value_mandatory_config) ? 'required' : ''); ?>" width="380">
						<option value=''></option>
						<?php if(get_config($dbc, 'ticket_business_contact_add_pos') == 'top') { ?>
							<option value="ADD_NEW">Add New <?= CONTACTS_NOUN ?></option>
						<?php } ?>
						<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `businessid`, `region`, `con_locations`, `classification` FROM `contacts` WHERE (`first_name` != '' OR `last_name` != '') AND `category` NOT IN ('".BUSINESS_CAT."',".STAFF_CATS.") AND `deleted`=0")) as $row) {
							$selected = ( $clientid==$row['contactid'] ) ? 'selected="selected"' : ($businessid > 0 && $businessid != $row['businessid'] ? 'style="display:none;"' : '');
							echo '<option data-region="'.$row['region'].'" data-location="'.$row['con_locations'].'" data-classification="'.$row['classification'].'" data-business="'.$row['businessid'].'" '. $selected .' value="'. $row['contactid'] .'">'. ($row['first_name']) . ' ' . ($row['last_name']) .'</option>';
						} ?>
						<?php if(get_config($dbc, 'ticket_business_contact_add_pos') != 'top') { ?>
							<option value="ADD_NEW">Add New <?= CONTACTS_NOUN ?></option>
						<?php } ?>
					</select>
				</div>
				<div class="col-sm-1">
					<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="New Contact" src="../img/icons/ROOK-add-icon.png"></a>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Guardian,') !== false && $field_sort_field == 'PI Guardian' ) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array('PI Guardian', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><!----> Parent/Guardian:</label>
				<div class="col-sm-7">
					<select name="guardianid" id="guardianid" data-placeholder="Select a Parent/Guardian..." data-table="tickets" data-id="<?= $ticketid ?>" data-category="<?= get_config($dbc, 'ticket_guardian_contact_'.$ticket_type) ?: (get_config($dbc, 'ticket_guardian_contact') ?: '%') ?>" data-id-field="ticketid" class="chosen-select-deselect form-control <?php echo (in_array('PI Guardian', $value_mandatory_config) ? 'required' : ''); ?>" width="380">
						<option value=''></option>
						<?php if(get_config($dbc, 'ticket_business_contact_add_pos') == 'top') { ?>
							<option value="ADD_NEW">Add New Parent/Guardian</option>
						<?php } ?>
						<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `businessid`, `region`, `con_locations`, `classification` FROM `contacts` WHERE (`first_name` != '' OR `last_name` != '') AND `category` NOT IN ('".BUSINESS_CAT."',".STAFF_CATS.") ".(get_config($dbc, 'ticket_guardian_contact_'.$ticket_type) ? " AND `category` = '".get_config($dbc, 'ticket_guardian_contact_'.$ticket_type)."'" : (get_config($dbc, 'ticket_guardian_contact') ? " AND `category` = '".get_config($dbc, 'ticket_guardian_contact')."'" : ''))." AND `deleted`=0")) as $row) {
							$selected = ($get_ticket['guardianid']==$row['contactid'] ? 'selected="selected"' : '');
							echo '<option data-region="'.$row['region'].'" data-location="'.$row['con_locations'].'" data-classification="'.$row['classification'].'" data-business="'.$row['businessid'].'" '. $selected .' value="'. $row['contactid'] .'">'. ($row['first_name']) . ' ' . ($row['last_name']) .'</option>';
						} ?>
						<?php if(get_config($dbc, 'ticket_business_contact_add_pos') != 'top') { ?>
							<option value="ADD_NEW">Add New Parent/Guardian</option>
						<?php } ?>
					</select>
				</div>
				<div class="col-sm-1">
					<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="New Parent/Guardian" src="../img/icons/ROOK-add-icon.png"></a>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI AFE,') !== false && $field_sort_field == 'PI AFE' ) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array('PI AFE', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>AFE#:</label>
				<div class="col-sm-8">
					<input type="text" name="afe_number" id="clientid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control <?php echo (in_array('PI AFE', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= $get_ticket['afe_number'] ?>">
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Project,') !== false && $field_sort_field == 'PI Project' && ($force_project == 'manual' || $force_project == '')) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Project', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?> <?= PROJECT_NOUN ?> Name:</label>
			  <div class="col-sm-7">
				<select data-placeholder="Select <?= PROJECT_NOUN ?> related to file #'s (e.g. 67, 77, etc.)..." name="projectid" id="projectid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control <?php echo (in_array('PI Project', $value_mandatory_config) ? 'required' : ''); ?>" width="380">
				  <option value=""></option>
				  <?php $query = mysqli_query($dbc,"SELECT projectid, projecttype, project_name, businessid, clientid, status FROM project WHERE deleted=0 AND (status NOT IN ('Archive') OR `projectid`='$projectid') order by `projectid` DESC");
					while($row = mysqli_fetch_array($query)) {
						$project_business = '';
						if($row['businessid'] > 0) {
							$project_business = mysqli_fetch_array(mysqli_query($dbc, "SELECT `region`, `con_locations`, `classification` FROM `contacts` WHERE `contactid` = '{$row['businessid']}'"));
						}
						echo "<option data-region='".$project_business['region']."' data-location='".$project_business['con_locations']."' data-classification='".$project_business['classification']."' data-business='".$row['businessid']."' data-client='".explode(',',trim($row['clientid'],','))[0]."' ";
						echo ($projectid == $row['projectid'] ? 'selected' : (($businessid > 0 && $businessid == $row['businessid']) || ($clientid > 0 && strpos(','.$row['clientid'].',', ",$clientid,") !== FALSE) || (!($businessid > 0) && !($clientid > 0)) || (explode(',',trim($row['clientid'],','))[0] == '' && !($row['businessid'] > 0)) ? '' : 'style="display:none;"'));
						echo " value='".$row['projectid']."'>".get_project_label($dbc,$row).'</option>';
					}
				  ?>
				</select>
			  </div>

				<div class="col-sm-1">
					<a href="" onclick="viewProject(this); return false;"><img class="inline-img pull-right" src="../img/icons/eyeball.png"></a>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Pieces,') !== false && $field_sort_field == 'PI Pieces' ) { ?>
			<div class="form-group">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array('PI Pieces', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Piece Work:</label>
				<div class="col-sm-8">
					<input name="piece_work" value="<?php echo $piece_work; ?>" id="project_name" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" type="text" class="form-control <?php echo (in_array('PI Pieces', $value_mandatory_config) ? 'required' : ''); ?>"></p>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Sites,') !== false && $field_sort_field == 'PI Sites' ) { ?>
			<div class="site_group">
				<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Sites', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Site:</label>
				  <div class="col-sm-7">
					<select data-placeholder="Select Site..." multiple name="siteid[]" id="siteid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," class="chosen-select-deselect form-control <?php echo (in_array('PI Sites', $value_mandatory_config) ? 'required' : ''); ?>">
						<?php if(empty($site_list)) {
							$site_list = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, site_name, `display_name`, businessid FROM `contacts` WHERE `category`='".SITES_CAT."' AND deleted=0 ORDER BY IFNULL(NULLIF(`display_name`,''),`site_name`)"));
						}
						foreach($site_list as $site_row) {
							echo "<option data-business='".$site_row['businessid']."' ".(strpos(','.$get_ticket['siteid'].',',','.$site_row['contactid'].',') !== FALSE ? 'selected' : ($get_ticket['businessid'] > 0 && $get_ticket['businessid'] != $site_row['businessid'] && $site_row['businessid'] > 0 ? 'style="display:none;"' : ''))." value='".$site_row['contactid']."'>".$site_row['full_name'].'</option>';
						} ?>
						<option value="MANUAL">Add New Site</option>
					</select>
				  </div>
				  <div class="col-sm-1">
					<a href="" onclick="viewSite(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('MANUAL').change(); return false;"><img class="inline-img pull-right" data-history-label="New Site" src="../img/icons/ROOK-add-icon.png"></a>
				  </div>
				</div>
				<div class="form-group clearfix site_name" style="display:none;">
					<label class="control-label col-sm-4">Name of Location:</label>
					<div class="col-sm-8">
						<input type="text" name="site_name" data-table="contacts" data-id="" data-id-field="contactid" data-attach="Sites" data-attach-field="category" class="form-control">
					</div>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Rate Card,') !== false && $field_sort_field == 'PI Rate Card' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Rate Card', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Rate Card:</label>
			  <div class="col-sm-8">
				<select data-placeholder="Select Rate Card..." name="rate_card" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control <?php echo (in_array('PI Rate Card', $value_mandatory_config) ? 'required' : ''); ?>"><option/>
					<?php $query = mysqli_query($dbc,"SELECT ratecardid, clientid, rate_card_name FROM `rate_card` WHERE `on_off`=1 AND `hide`=0 AND deleted=0 ORDER BY `rate_card_name`");
					while($row = mysqli_fetch_array($query)) { ?>
						<option data-type="customer" data-business='<?= $row['clientid'] ?>' <?= $get_ticket['rate_card'] == 'cust*'.$row['ratecardid'] ? 'selected' : ($get_ticket['businessid'] > 0 && $get_ticket['businessid'] != $row['clientid'] ? 'style="display:none;"' : '') ?> value='cust*<?= $row['ratecardid'] ?>'><?= $row['rate_card_name'] ?></option>
					<?php } ?>
					<?php $query = mysqli_query($dbc,"SELECT MIN(`companyrcid`) `id`, rate_card_name FROM `company_rate_card` WHERE `deleted`=0 AND IFNULL(`rate_card_name`,'') != '' GROUP BY `rate_card_name` ORDER BY `rate_card_name`");
					while($row = mysqli_fetch_array($query)) { ?>
						<option data-type="company" <?= $get_ticket['rate_card'] == 'company*'.$row['id'] ? 'selected' : '' ?> value='company*<?= $row['id'] ?>'><?= $row['rate_card_name'] ?></option>
					<?php } ?>
				</select>
			  </div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Customer Order,') !== false && $field_sort_field == 'PI Customer Order' ) {
			foreach(explode('#*#',trim($get_ticket['customer_order_num'],'#*')) as $customer_order_line) { ?>
				<div class="multi-block form-group">
				  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Customer Order', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Customer Order #:</label>
				  <div class="col-sm-7">
					<input type="text" name="customer_order_num" id="customer_order_num" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="#*#" class="form-control <?php echo (in_array('PI Customer Order', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= $customer_order_line ?>" placeholder="The Customer Order # provided by the customer">
				  </div>
				  <div class="col-sm-1">
						<img class="inline-img pull-right" data-history-label="Customer Order #" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
						<img class="inline-img pull-right" data-history-label="Customer Order #" onclick="remMulti(this);" src="../img/remove.png">
				  </div>
				</div>
			<?php } ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Sales Order,') !== false && $field_sort_field == 'PI Sales Order' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Sales Order', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Invoice #:</label>
			  <div class="col-sm-8">
				<select data-placeholder="Select Invoice..." name="salesorderid" id="salesorderid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control <?php echo (in_array('PI Sales Order', $value_mandatory_config) ? 'required' : ''); ?>">
					<option value=""></option>
					<?php $query = mysqli_query($dbc,"SELECT posid, invoice_date FROM sales_order WHERE deleted=0 ORDER BY invoice_date");
					while($row = mysqli_fetch_array($query)) {
						echo "<option ".($get_ticket['salesorderid'] == $row['posid'] ? 'selected' : '')." value='".$row['posid']."'>#".$row['posid'].' - '.$row['invoice_date'].'</option>';
					} ?>
				</select>
			  </div>
			</div>
		<?php } else if ( strpos($value_config, ',PI Invoice,') !== false && $field_sort_field == 'PI Invoice' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Invoice', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Invoice #:</label>
			  <div class="col-sm-8">
				<input type="text" name="salesorderid" id="salesorderid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control <?php echo (in_array('PI Invoice', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= $get_ticket['salesorderid'] ?>">
			  </div>
			</div>
		<?php } else if ( strpos($value_config, ',PI Order,') !== false && $field_sort_field == 'PI Order' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Order', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Order #:</label>
			  <div class="col-sm-8">
				<input type="text" name="salesorderid" id="salesorderid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control <?php echo (in_array('PI Order', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= $get_ticket['salesorderid'] ?>">
			  </div>
			</div>
		<?php } else if ( strpos($value_config, ',PI WTS Order,') !== false && $field_sort_field == 'PI WTS Order' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI WTS Order', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>WTS Order #:</label>
			  <div class="col-sm-8">
				<input type="text" name="salesorderid" id="salesorderid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control <?php echo (in_array('PI WTS Order', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= $get_ticket['salesorderid'] ?>" placeholder="PO issued to WTS for logistics related charges (e.g. Blanket PO)...">
			  </div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Purchase Order,') !== false && $field_sort_field == 'PI Purchase Order' ) {
			foreach(explode('#*#',trim($get_ticket['purchase_order'],'#*')) as $po_num_line) { ?>
				<div class="multi-block form-group">
				  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Purchase Order', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Purchase Order #:</label>
				  <div class="col-sm-7">
					<input type="text" name="purchase_order" id="purchase_order" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="#*#" class="form-control <?php echo (in_array('PI Purchase Order', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= $po_num_line ?>" placeholder="The PO# provided by the customer">
				  </div>
				  <div class="col-sm-1">
						<img class="inline-img pull-right" data-history-label="Purchase Order #" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
						<img class="inline-img pull-right" data-history-label="Purchase Order #" onclick="remMulti(this);" src="../img/remove.png">
				  </div>
				</div>
			<?php } ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Cross Ref,') !== false && $field_sort_field == 'PI Cross Ref' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Cross Ref', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Cross Reference #:</label>
			  <div class="col-sm-8">
				<input type="text" name="notes" id="notes" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control <?php echo (in_array('PI Cross Ref', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= $get_ticket['notes'] ?>" placeholder="Internal reference tracking...">
			  </div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Invoiced Out,') !== false && $field_sort_field == 'PI Invoiced Out' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Invoiced Out', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Invoiced:</label>
			  <div class="col-sm-8">
				<label class="form-checkbox"><input type="radio" name="invoiced" <?= $get_ticket['invoiced'] > 0 ? 'checked' : '' ?> data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control <?php echo (in_array('PI Invoiced Out', $value_mandatory_config) ? 'required' : ''); ?>" value="1"> Yes</label>
				<label class="form-checkbox"><input type="radio" name="invoiced" <?= $get_ticket['invoiced'] > 0 ? '' : 'checked' ?> data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control <?php echo (in_array('PI Invoiced Out', $value_mandatory_config) ? 'required' : ''); ?>" value="0"> No</label>
			  </div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Work Order,') !== false && $field_sort_field == 'PI Work Order' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Work Order', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Work Order #:</label>
			  <div class="col-sm-8">
				<input type="text" name="heading" id="heading" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control <?php echo (in_array('PI Work Order', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= $get_ticket['heading'] ?>" placeholder="Turn around only...">
			  </div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Scheduled Date,') !== false && $field_sort_field == 'PI Scheduled Date' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Scheduled Date', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Scheduled Date:</label>
			  <div class="col-sm-8 date_div">
				<input type="text" name="to_do_date" class="form-control datepicker" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= date('Y-m-d',strtotime($get_ticket['to_do_date'] != '' ? $get_ticket['to_do_date'] : 'today')) ?>" onchange="$(this).closest('.date_div').find('[name=to_do_end_date]').val(this.value).change();">
				<input type="hidden" name="to_do_end_date" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control datepicker" value="<?= date('Y-m-d',strtotime($get_ticket['to_do_end_date'])) ?>">
			  </div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Date of Entry,') !== false && $field_sort_field == 'PI Date of Entry' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Date of Entry', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Date of Entry:</label>
			  <div class="col-sm-8">
				<input type="text" name="created_date" readonly class="form-control <?php echo (in_array('PI Date of Entry', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= date('Y-m-d',strtotime($get_ticket['created_date'] != '' ? $get_ticket['created_date'] : 'today')) ?>">
			  </div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Time of Entry,') !== false && $field_sort_field == 'PI Time of Entry' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI AFE', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Time of Entry:</label>
			  <div class="col-sm-8">
				<input type="text" name="created_date" readonly class="form-control <?php echo (in_array('PI Time of Entry', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= date('h:i a',strtotime($get_ticket['created_date'])) ?>">
			  </div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Agent,') !== false && $field_sort_field == 'PI Agent' ) {
			$default_contact_category = get_config($dbc, 'ticket_project_contact');
			$contact_category = ($ticket_type == '' ? $default_contact_category : get_config($dbc, 'ticket_project_contact_'.$ticket_type)); ?>
			<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Agent', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><?= $contact_category ?>:</label>
				  <div class="col-sm-7 select-div" style="<?= trim($get_ticket['agentid'],',') > 0 || $get_ticket['agentid'] == '' ? '' : 'display:none;' ?>">
					<select data-placeholder="Select <?= $contact_category ?>..." name="agentid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-category="<?= $contact_category ?>" class="chosen-select-deselect form-control <?php echo (in_array('PI Agent', $value_mandatory_config) ? 'required' : ''); ?>" width="380">
					  <option value=""></option>
					  <?php $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name, name FROM contacts WHERE deleted=0 AND status>0 AND category='".$contact_category."'"));
						foreach($staff_query as $row) { ?>
							<option <?= trim($get_ticket['agentid'],',')==$row['contactid'] ? "selected" : '' ?> value="<?php echo $row['contactid']; ?>"><?php echo $row['name'].' '.$row['first_name'].' '.$row['last_name']; ?></option>
						<?php } ?>
						<option value="ADD_NEW">Add New <?= $contact_category ?></option>
						<option value="MANUAL">One Time <?= $contact_category ?></option>
					</select>
				  </div>
				  <div class="col-sm-1 select-div" style="<?= trim($get_ticket['agentid'],',') > 0 || $get_ticket['agentid'] == '' ? '' : 'display:none;' ?>">
					<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="New <?= $contact_category ?>" src="../img/icons/ROOK-add-icon.png"></a>
				  </div>
				<div class="col-sm-8 manual-div" style="<?= trim($get_ticket['agentid'],',') > 0 || $get_ticket['agentid'] == '' ? 'display:none;' : '' ?>">
					<input type="text" name="agentid" class="form-control <?php echo (in_array('PI Agent', $value_mandatory_config) ? 'required' : ''); ?>" data-one-time="true" data-category="<?= $contact_category ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $get_ticket['agentid'] > 0 ? '' : $get_ticket['agentid'] ?>">
					<label class="form-checkbox"><input checked type="checkbox" name="one_time" onchange="if(!this.checked) { $(this).closest('.form-group').find('.select-div').show(); $(this).closest('.manual-div').hide(); }"> One Time Only <?= $contact_category ?></label>
				</div>
			</div>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Notify Party,') !== false && $field_sort_field == 'PI Notify Party' ) {
			$default_contact_category = get_config($dbc, 'ticket_notify_contact');
			$contact_category = ($ticket_type == '' ? $default_contact_category : get_config($dbc, 'ticket_notify_contact_'.$ticket_type)); ?>
			<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Notify Party', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Notify Party:</label>
				  <div class="col-sm-7 select-div" style="<?= trim($get_ticket['notifyid'],',') > 0 || $get_ticket['notifyid'] == '' ? '' : 'display:none;' ?>">
					<select data-placeholder="Select <?= $contact_category ?>..." name="notifyid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-category="<?= (!empty($contact_category) ? $contact_category : '%') ?>" class="chosen-select-deselect form-control <?php echo (in_array('PI Notify Party', $value_mandatory_config) ? 'required' : ''); ?>" width="380">
					  <option value=""></option>
					  <?php $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name, name FROM contacts WHERE deleted=0 AND status>0".(!empty($contact_category) ? "AND category='".$contact_category."'" : '')));
						foreach($staff_query as $row) { ?>
							<option <?= trim($get_ticket['notifyid'],',')==$row['contactid'] ? "selected" : '' ?> value="<?php echo $row['contactid']; ?>"><?php echo $row['name'].' '.$row['first_name'].' '.$row['last_name']; ?></option>
						<?php } ?>
						<option value="ADD_NEW">Add New <?= $contact_category ?></option>
						<option value="MANUAL">One Time <?= $contact_category ?></option>
					</select>
				  </div>
				  <div class="col-sm-1 select-div" style="<?= trim($get_ticket['notifyid'],',') > 0 || $get_ticket['notifyid'] == '' ? '' : 'display:none;' ?>">
					<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="New <?= $contact_category ?>" src="../img/icons/ROOK-add-icon.png"></a>
				  </div>
				<div class="col-sm-8 manual-div" style="<?= trim($get_ticket['notifyid'],',') > 0 || $get_ticket['notifyid'] == '' ? 'display:none;' : '' ?>">
					<input type="text" name="notifyid" class="form-control <?php echo (in_array('PI Notify Party', $value_mandatory_config) ? 'required' : ''); ?>" data-one-time="true" data-category="<?= (!empty($contact_category) ? $contact_category : '%') ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $get_ticket['notifyid'] > 0 ? '' : $get_ticket['notifyid'] ?>">
					<label class="form-checkbox"><input checked type="checkbox" name="one_time" onchange="if(!this.checked) { $(this).closest('.form-group').find('.select-div').show(); $(this).closest('.manual-div').hide(); }"> One Time Only <?= $contact_category ?></label>
				</div>
			</div>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Ban,') !== false && $field_sort_field == 'PI Ban' ) {
			$contact_category = 'Ban'; ?>
			<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Ban', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><?= $contact_category ?>:</label>
				  <div class="col-sm-7 select-div" style="<?= trim($get_ticket['banid'],',') > 0 || $get_ticket['banid'] == '' ? '' : 'display:none;' ?>">
					<select data-placeholder="Select <?= $contact_category ?>..." name="banid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-category="<?= $contact_category ?>" class="chosen-select-deselect form-control <?php echo (in_array('PI Ban', $value_mandatory_config) ? 'required' : ''); ?>" width="380">
					  <option value=""></option>
					  <?php $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name, name FROM contacts WHERE deleted=0 AND status>0 AND category='".$contact_category."'"));
						foreach($staff_query as $row) { ?>
							<option <?= trim($get_ticket['banid'],',')==$row['contactid'] ? "selected" : '' ?> value="<?php echo $row['contactid']; ?>"><?php echo $row['name'].' '.$row['first_name'].' '.$row['last_name']; ?></option>
						<?php } ?>
						<option value="ADD_NEW">Add New <?= $contact_category ?></option>
						<option value="MANUAL">One Time <?= $contact_category ?></option>
					</select>
				  </div>
				  <div class="col-sm-1 select-div" style="<?= trim($get_ticket['banid'],',') > 0 || $get_ticket['banid'] == '' ? '' : 'display:none;' ?>">
					<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="New <?= $contact_category ?>" src="../img/icons/ROOK-add-icon.png"></a>
				  </div>
				<div class="col-sm-8 manual-div" style="<?= trim($get_ticket['banid'],',') > 0 || $get_ticket['banid'] == '' ? 'display:none;' : '' ?>">
					<input type="text" name="banid" class="form-control <?php echo (in_array('PI Ban', $value_mandatory_config) ? 'required' : ''); ?>" data-one-time="true" data-category="<?= $contact_category ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $get_ticket['banid'] > 0 ? '' : $get_ticket['banid'] ?>">
					<label class="form-checkbox"><input checked type="checkbox" name="one_time" onchange="if(!this.checked) { $(this).closest('.form-group').find('.select-div').show(); $(this).closest('.manual-div').hide(); }"> One Time Only <?= $contact_category ?></label>
				</div>
			</div>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Vendor,') !== false && $field_sort_field == 'PI Vendor' ) {
			$contact_category = 'Vendor'; ?>
			<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Vendor', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><?= $contact_category ?>:</label>
				  <div class="col-sm-7 select-div" style="<?= trim($get_ticket['vendorid'],',') > 0 || $get_ticket['vendorid'] == '' ? '' : 'display:none;' ?>">
					<select data-placeholder="Select <?= $contact_category ?>..." name="vendorid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-category="<?= $contact_category ?>" class="chosen-select-deselect form-control <?php echo (in_array('PI Vendor', $value_mandatory_config) ? 'required' : ''); ?>" width="380">
					  <option value=""></option>
					  <?php $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name, name FROM contacts WHERE deleted=0 AND status>0 AND category='".$contact_category."'"));
						foreach($staff_query as $row) { ?>
							<option <?= trim($get_ticket['vendorid'],',')==$row['contactid'] ? "selected" : '' ?> value="<?php echo $row['contactid']; ?>"><?php echo $row['name'].' '.$row['first_name'].' '.$row['last_name']; ?></option>
						<?php } ?>
						<option value="ADD_NEW">Add New <?= $contact_category ?></option>
						<option value="MANUAL">One Time <?= $contact_category ?></option>
					</select>
				  </div>
				  <div class="col-sm-1 select-div" style="<?= trim($get_ticket['vendorid'],',') > 0 || $get_ticket['vendorid'] == '' ? '' : 'display:none;' ?>">
					<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="New <?= $contact_category ?>" src="../img/icons/ROOK-add-icon.png"></a>
				  </div>
				<div class="col-sm-8 manual-div" style="<?= trim($get_ticket['vendorid'],',') > 0 || $get_ticket['vendorid'] == '' ? 'display:none;' : '' ?>">
					<input type="text" name="vendorid" class="form-control <?php echo (in_array('PI Vendor', $value_mandatory_config) ? 'required' : ''); ?>" data-one-time="true" data-category="<?= $contact_category ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $get_ticket['vendorid'] > 0 ? '' : $get_ticket['vendorid'] ?>">
					<label class="form-checkbox"><input checked type="checkbox" name="one_time" onchange="if(!this.checked) { $(this).closest('.form-group').find('.select-div').show(); $(this).closest('.manual-div').hide(); }"> One Time Only <?= $contact_category ?></label>
				</div>
			</div>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Operator,') !== false && $field_sort_field == 'PI Operator' ) { ?>
			<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Operator', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Operators:</label>
				  <div class="col-sm-8">
					<select data-placeholder="Select Operator..." multiple id="contactid" name="contactid[]" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control <?php echo (in_array('PI Operator', $value_mandatory_config) ? 'required' : ''); ?>" width="380">
						  <?php $staff_query = sort_contacts_query(mysqli_query($dbc,"SELECT contactid, first_name, last_name FROM contacts WHERE deleted=0 AND status>0 AND category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY.""));
							foreach($staff_query as $row) { ?>
								<option <?php if (strpos($get_ticket['contactid'], ','.$row['contactid'].',') !== FALSE) {
								echo " selected"; } ?> value="<?php echo $row['contactid']; ?>"><?php echo $row['first_name'].' '.$row['last_name']; ?></option>
							<?php }
						  ?>
					</select>
				  </div>
			</div>
		<?php } ?>
		<?php if(strpos($value_config,',PI Status,') !== FALSE && $field_sort_field == 'PI Status') { ?>
			<div class="form-group">
				<label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Status', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Status:</label>
				<div class="col-sm-8">
                    <?php $invoice_status = '';
                    if(get_config($dbc, 'ticket_invoice_status') > 0 && $ticketid > 0) {
                        $invoice = $dbc->query("SELECT * FROM `invoice` WHERE `deleted`=0 AND CONCAT(',',`ticketid`,',') LIKE '%,$ticketid,%'");
                        if($invoice->num_rows > 0) {
                            $invoice = $invoice->fetch_assoc();
                            switch($invoice['status']) {
                                case 'Completed':
                                    $invoice_status = 'Paid';
                                    break;
                                case 'Void':
                                    $invoice_status = 'Voided';
                                    break;
                                case 'Saved':
                                    $invoice_status = 'Unbilled';
                                    break;
                                case 'Posted':
                                default:
                                    $invoice_status = 'Accounts Receivable';
                                    break;
                            }
                        }
                    }
                    if(!empty($invoice_status)) {
                        echo 'Invoice Status: '.$invoice_status.'<br />';
                    } ?>

					<select data-placeholder="Select a Status..." name="status" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" id="status" class="chosen-select-deselect form-control <?php echo (in_array('PI Status', $value_mandatory_config) ? 'required' : ''); ?> input-sm">
					  <option value=""></option>
					  <?php
                        $tabs = get_config($dbc, 'ticket_status');
                        $each_tab = explode(',', $tabs);
                        $status_list = [];
                        foreach ($each_tab as $cat_tab) {
                            if(check_subtab_persmission($dbc, 'ticket', ROLE, 'ticket_status'.config_safe_str($cat_tab))) {
                                $status_list[] = $cat_tab;
                            }
                        }
                        if(!in_array($status, $status_list) && $status != '') { ?>
                            <option selected value="<?= $status ?>"><?= $status ?></option>
                        <?php } else {
                            foreach($status_list as $status_option) { ?>
                                <option <?= $status_option == $status ? 'selected' : '' ?> value="<?= $status_option ?>"><?= $status_option ?></option>
                            <?php }
                        }
					  ?>
					</select>
				</div>
			</div>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Waste Manifest,') !== false && $field_sort_field == 'PI Waste Manifest' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Waste Manifest', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Waste Manifest #:</label>
			  <div class="col-sm-8">
				<input type="text" name="waste_manifest" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control <?php echo (in_array('PI Waste Manifest', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= $get_ticket['waste_manifest'] ?>">
			  </div>
			</div>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Reference Ticket,') !== false && $field_sort_field == 'PI Reference Ticket' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Reference Ticket', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Reference Ticket #:</label>
			  <div class="col-sm-8">
				<input type="text" name="ref_ticket" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control <?php echo (in_array('PI Reference Ticket', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= $get_ticket['ref_ticket'] ?>">
			  </div>
			</div>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI TDG Doc Num,') !== false && $field_sort_field == 'PI TDG Doc Num' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI TDG Doc Num', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>TDG Doc. #:</label>
			  <div class="col-sm-8">
				<input type="text" name="tdg_doc_num" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control <?php echo (in_array('PI TDG Doc Num', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= $get_ticket['tdg_doc_num'] ?>">
			  </div>
			</div>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI VTI Num,') !== false && $field_sort_field == 'PI VTI Num' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI VTI Num', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>VTI #:</label>
			  <div class="col-sm-8">
				<input type="text" name="vti_num" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control <?php echo (in_array('PI VTI Num', $value_mandatory_config) ? 'required' : ''); ?>" value="<?= $get_ticket['vti_num'] ?>">
			  </div>
			</div>
		<?php } ?>
		<?php if(strpos($value_config,',PI TEXT FIELD,') !== FALSE && $field_sort_field == 'PI TEXT FIELD') {
			$ticket_custom_field = get_config($dbc, 'ticket_custom_field');
			$tab_ticket_custom_field = get_config($dbc, 'ticket_custom_field'.($ticket_type == '' ? '' : '_'.$ticket_type));
			$ticket_custom_field_values = explode('#*#',get_config($dbc, 'ticket_custom_field_values'));
			$tab_ticket_custom_field_values = explode('#*#',get_config($dbc, 'ticket_custom_field_values'.($ticket_type == '' ? '' : '_'.$ticket_type)));
			$value_list = array_filter(array_unique(array_merge($ticket_custom_field_values,$tab_ticket_custom_field_values))); ?>
			<div class="form-group">
				<label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI TEXT FIELD', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><?= $tab_ticket_custom_field != '' ? $tab_ticket_custom_field : $ticket_custom_field ?>:</label>
				<div class="col-sm-8">
					<?php if(count($value_list) > 0) { ?>
						<select name="custom_field" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-placeholder="Select <?= $tab_ticket_custom_field != '' ? $tab_ticket_custom_field : $ticket_custom_field ?>" class="chosen-select-deselect form-control <?php echo (in_array('PI TEXT FIELD', $value_mandatory_config) ? 'required' : ''); ?>"><option />
							<?php foreach($value_list as $field_value) {
								$field_value = explode('|*|',$field_value); ?>
								<option <?= $field_value[1] == $get_ticket['custom_field'] ? 'selected' : '' ?> value="<?= $field_value[1] ?>"><?= $field_value[0] ?></option>
							<?php } ?>
						</select>
					<?php } else { ?>
						<input type="text" name="custom_field" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control " value="<?= $get_ticket['custom_field'] ?>">
					<?php } ?>
				</div>
			</div>
		<?php } ?>
	<?php } else { ?>
		<?php if (( strpos($value_config, ',PI Business,') !== false  || ( strpos($value_config, ',PI ') === false) ) && $field_sort_field == 'PI Business' ) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array('PI Business', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Business Name:</label>
				<div class="col-sm-8">
					<?= get_client($dbc, $businessid) ?>
					<input type="hidden" name="businessid" id="businessid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $businessid ?>">
				</div>
			</div>
			<?php $pdf_contents[] = ['Business Name', get_client($dbc, $businessid)]; ?>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Name,') !== false && $field_sort_field == 'PI Name') { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array('PI Name', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Contact Name:</label>
				<div class="col-sm-8">
					<?= get_contact($dbc, $clientid) ?>
					<input type="hidden" name="clientid" id="clientid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $clientid ?>">
				</div>
			</div>
			<?php $pdf_contents[] = ['Contact Name', get_contact($dbc, $clientid)]; ?>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Guardian,') !== false && $field_sort_field == 'PI Guardian') { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array('PI Guardian', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Parent/Guardian:</label>
				<div class="col-sm-8">
					<?= get_contact($dbc, $get_ticket['guardianid']) ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Parent/Guardian', get_contact($dbc, $get_ticket['guardianid'])]; ?>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI AFE,') !== false && $field_sort_field == 'PI AFE') { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array('PI AFE', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>AFE#:</label>
				<div class="col-sm-8">
					<?= $get_ticket['afe_number'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['AFE#', $get_ticket['afe_number']]; ?>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Project,') !== false && $field_sort_field == 'PI Project' && ($force_project == 'manual' || $force_project == '')) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array($field_sort_field, $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><?= PROJECT_NOUN ?> Name:</label>
				<div class="col-sm-8">
					<?= get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='".$get_ticket['projectid']."'"))) ?>
				</div>
			</div>
			<?php $pdf_contents[] = [PROJECT_NOUN.' Name', get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='".$get_ticket['projectid']."'")))]; ?>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Pieces,') !== false && $field_sort_field == 'PI Pieces' ) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array('PI Pieces', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Piece Work:</label>
				<div class="col-sm-8">
					<?= $piece_work ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Piece Work', $piece_work]; ?>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Sites,') !== false && $field_sort_field == 'PI Sites' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Sites', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Site:</label>
			  <div class="col-sm-8">
				<?php $site_names = [];
				foreach(array_filter(explode(',',$get_ticket['siteid'])) as $site_id) {
					$query = mysqli_query($dbc,"SELECT contactid, site_name, `display_name`, businessid FROM `contacts` WHERE `category`='Sites' AND deleted=0 AND `contactid`='".$site_id."' ORDER BY IFNULL(NULLIF(`display_name`,''),`site_name`)");
					$row = mysqli_fetch_array($query);
					$site_names[] = ($row['display_name'] == '' ? $row['site_name'] : $row['display_name']);
				}
				$site_names = implode(', ',$site_names);
				echo $site_names; ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['Site', $site_names]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Customer Order,') !== false && $field_sort_field == 'PI Customer Order' ) {
			foreach(array_filter(explode('#*#',$get_ticket['customer_order_num'])) as $customer_order_line) { ?>
				<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Customer Order', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Customer Order #:</label>
				  <div class="col-sm-8">
					<?= $customer_order_line ?>
				  </div>
				</div>
				<?php $pdf_contents[] = ['Customer Order #', $customer_order_line]; ?>
			<?php }
		} ?>
		<?php if ( strpos($value_config, ',PI Sales Order,') !== false && $field_sort_field == 'PI Sales Order') { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array('PI Sales Order', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><?= SALES_ORDER_NOUN ?> Invoice #:</label>
				<div class="col-sm-8">
					<?= mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(`posid`,' - ',`invoice_date`) label FROM `sales_order` WHERE `posid`='{$get_ticket['salesorderid']}'"))['label'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = [SALES_ORDER_NOUN.' Invoice #', mysqli_fetch_assoc(mysqli_query($dbc, "SELECT CONCAT(`posid`,' - ',`invoice_date`) label FROM `sales_order` WHERE `posid`='{$get_ticket['salesorderid']}'"))['label']]; ?>
		<?php } else if ( strpos($value_config, ',PI Invoice,') !== false && $field_sort_field == 'PI Invoice' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI AFE', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Invoice #:</label>
			  <div class="col-sm-8">
				<?= $get_ticket['salesorderid'] ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['Invoice #', $get_ticket['salesorderid']]; ?>
		<?php } else if ( strpos($value_config, ',PI Order,') !== false && $field_sort_field == 'PI Order' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Order', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Order #:</label>
			  <div class="col-sm-8">
				<?= $get_ticket['salesorderid'] ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['Order #', $get_ticket['salesorderid']]; ?>
		<?php } else if ( strpos($value_config, ',PI WTS Order,') !== false && $field_sort_field == 'PI WTS Order' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI WTS Order', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>WTS Order #:</label>
			  <div class="col-sm-8">
				<?= $get_ticket['salesorderid'] ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['WTS Order #', $get_ticket['salesorderid']]; ?>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Purchase Order,') !== false && $field_sort_field == 'PI Purchase Order' ) {
			foreach(array_filter(explode('#*#',$get_ticket['purchase_order'])) as $po_num_line) { ?>
				<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Purchase Order', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Purchase Order #:</label>
				  <div class="col-sm-8">
					<?= $po_num_line ?>
				  </div>
				</div>
				<?php $pdf_contents[] = ['Purchase Order #', $po_num_line]; ?>
			<?php }
		} ?>
		<?php if ( strpos($value_config, ',PI Cross Ref,') !== false && $field_sort_field == 'PI Cross Ref' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Cross Ref', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Cross Reference #:</label>
			  <div class="col-sm-8">
				<?= $get_ticket['notes'] ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['Cross Reference #', $get_ticket['notes']]; ?>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI Invoiced Out,') !== false && $field_sort_field == 'PI Invoiced Out') { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?php echo (in_array('PI Invoiced Out', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Invoiced:</label>
				<div class="col-sm-8">
					<?= $get_ticket['invoiced'] ? 'Yes' : 'No' ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Invoiced', $get_ticket['invoiced'] ? 'Yes' : 'No']; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Work Order,') !== false && $field_sort_field == 'PI Work Order' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Work Order', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Work Order #:</label>
			  <div class="col-sm-8">
				<?= $get_ticket['heading'] ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['Work Order #', $get_ticket['heading']]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Scheduled Date,') !== false && $field_sort_field == 'PI Scheduled Date' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Scheduled Date', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Scheduled Date:</label>
			  <div class="col-sm-8">
				<?= date('Y-m-d',strtotime($get_ticket['to_do_date'])) ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['Scheduled Date', date('Y-m-d',strtotime($get_ticket['to_do_date']))]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Date of Entry,') !== false && $field_sort_field == 'PI Date of Entry' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Date of Entry', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Date of Entry:</label>
			  <div class="col-sm-8">
				<?= date('Y-m-d',strtotime($get_ticket['created_date'])) ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['Date of Entry', date('Y-m-d',strtotime($get_ticket['created_date']))]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Time of Entry,') !== false && $field_sort_field == 'PI Time of Entry' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Time of Entry', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Time of Entry:</label>
			  <div class="col-sm-8">
				<?= date('h:i a',strtotime($get_ticket['created_date'])) ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['Time of Entry', date('h:i a',strtotime($get_ticket['created_date']))]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Agent,') !== false && $field_sort_field == 'PI Agent' ) {
			$default_contact_category = get_config($dbc, 'ticket_project_contact');
			$contact_category = ($ticket_type == '' ? $default_contact_category : get_config($dbc, 'ticket_project_contact_'.$ticket_type)); ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Agent', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><?= ($ticket_type == '' ? $default_contact_category : $contact_category) ?>:</label>
			  <div class="col-sm-8">
				<?php $echo_agent = '';
				foreach(explode(',',$get_ticket['agentid']) as $this_agentid) {
					if($this_agentid > 0) {
						$echo_agent .= get_contact($dbc, $this_agentid)."<br />";
					} else if($this_agentid != '') {
						$echo_agent .= $this_agentid.'<br />';
					}
				}
				echo $echo_agent; ?>
			  </div>
			</div>
			<?php $pdf_contents[] = [($ticket_type == '' ? $default_contact_category : $contact_category), $echo_agent]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Ban,') !== false && $field_sort_field == 'PI Ban' ) {
			$contact_category = 'Ban'; ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Ban', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><?= $contact_category ?>:</label>
			  <div class="col-sm-8">
				<?php $echo_agent = '';
				foreach(explode(',',$get_ticket['banid']) as $this_agentid) {
					if($this_agentid > 0) {
						$echo_agent .= !empty(get_client($dbc, $this_agentid)) ? get_client($dbc, $this_agentid) : get_contact($dbc, $this_agentid)."<br />";
					} else if($this_agentid != '') {
						$echo_agent .= $this_agentid.'<br />';
					}
				}
				echo $echo_agent; ?>
			  </div>
			</div>
			<?php $pdf_contents[] = [$contact_category, $echo_agent]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Vendor,') !== false && $field_sort_field == 'PI Vendor' ) {
			$contact_category = 'Vendor'; ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Vendor', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><?= $contact_category ?>:</label>
			  <div class="col-sm-8">
				<?php $echo_agent = '';
				foreach(explode(',',$get_ticket['vendorid']) as $this_agentid) {
					if($this_agentid > 0) {
						$echo_agent .= !empty(get_client($dbc, $this_agentid)) ? get_client($dbc, $this_agentid) : get_contact($dbc, $this_agentid)."<br />";
					} else if($this_agentid != '') {
						$echo_agent .= $this_agentid.'<br />';
					}
				}
				echo $echo_agent; ?>
			  </div>
			</div>
			<?php $pdf_contents[] = [$contact_category, $echo_agent]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Vendor,') !== false && $field_sort_field == 'PI Vendor' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Vendor', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Operators:</label>
			  <div class="col-sm-8">
				<?php $echo_agent = '';
				foreach(explode(',',$get_ticket['contactid']) as $this_agentid) {
					if($this_agentid > 0) {
						$echo_agent .= get_contact($dbc, $this_agentid)."<br />";
					}
				}
				echo $echo_agent; ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['Operators', $echo_agent]; ?>
		<?php } ?>

		<?php if(strpos($value_config,',PI Status,') !== FALSE && $field_sort_field == 'PI Status') { ?>
			<div class="form-group">
				<label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Status', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Status:</label>
				<div class="col-sm-8"><?= $status ?></div>
			<?php $pdf_contents[] = ['Status', $status]; ?>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Waste Manifest,') !== false && $field_sort_field == 'PI Waste Manifest' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Waste Manifest', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Waste Manifest #:</label>
			  <div class="col-sm-8">
				<?= $get_ticket['waste_manifest'] ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['Waste Manifest #', $get_ticket['waste_manifest']]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI Reference Ticket,') !== false && $field_sort_field == 'PI Reference Ticket' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI Reference Ticket', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>Reference Ticket #:</label>
			  <div class="col-sm-8">
				<?= $get_ticket['ref_ticket'] ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['Reference Ticket #', $get_ticket['ref_ticket']]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',PI TDG Doc Num,') !== false && $field_sort_field == 'PI TDG Doc Num' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI TDG Doc Num', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>TDG Doc. #:</label>
			  <div class="col-sm-8">
				<?= $get_ticket['tdg_doc_num'] ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['TDG Doc. #', $get_ticket['tdg_doc_num']]; ?>
		<?php } ?>
		<?php if ( strpos($value_config, ',PI VTI Num,') !== false && $field_sort_field == 'PI VTI Num' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI VTI Num', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?>VTI #:</label>
			  <div class="col-sm-8">
				<?= $get_ticket['vti_num'] ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['VTI #', $get_ticket['vti_num']]; ?>
		<?php } ?>
		<?php if(strpos($value_config,',PI TEXT FIELD,') !== FALSE && $field_sort_field == 'PI TEXT FIELD') {
			$ticket_custom_field = get_config($dbc, 'ticket_custom_field');
			$tab_ticket_custom_field = get_config($dbc, 'ticket_custom_field'.($ticket_type == '' ? '' : '_'.$ticket_type));
			$ticket_custom_field_values = explode('#*#',get_config($dbc, 'ticket_custom_field_values'));
			$tab_ticket_custom_field_values = explode('#*#',get_config($dbc, 'ticket_custom_field_values'.($ticket_type == '' ? '' : '_'.$ticket_type)));
			$value_list = array_filter(array_unique(array_merge($ticket_custom_field_values,$tab_ticket_custom_field_values))); ?>
			<div class="form-group">
				<label for="site_name" class="col-sm-4 control-label"><?php echo (in_array('PI TEXT FIELD', $value_mandatory_config) ? '<span class="text-red">* </span>' : ''); ?><?= $tab_ticket_custom_field != '' ? $tab_ticket_custom_field : $ticket_custom_field ?>:</label>
					<?php foreach($value_list as $field_value) {
						if($field_value[1] == $get_ticket['custom_field']) {
							$output_field_value = $field_value[0];
						} else {
							$output_field_value = $get_ticket['custom_field'];
						}
					}
					echo $output_field_value; ?>
				</div>
			</div>
			<?php $pdf_contents[] = [$tab_ticket_custom_field != '' ? $tab_ticket_custom_field : $ticket_custom_field, $output_field_value]; ?>
		<?php } ?>
	<?php }
} ?>
