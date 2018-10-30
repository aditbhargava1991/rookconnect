<script>
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
	<?php if(strpos($value_config, ',Service Rate Card,') !== FALSE && strpos($value_config, ',Detail Business,') !== FALSE) { ?>
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
	<?php } ?>
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
<?= !$custom_accordion ? (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : '<h3>'.('manual' == $force_project ? PROJECT_NOUN : TICKET_NOUN).' Details</h3>') : '' ?>
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
		<?php if ( strpos($value_config, ',Detail Business,') !== false && $field_sort_field == 'Detail Business') { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><span class="text-red">*</span> Business:</label>
				<div class="col-sm-7">
					<select name="businessid" id="businessid" data-placeholder="Select a Business..." data-category="<?= BUSINESS_CAT ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control" width="380">
						<option value=''></option>
						<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `name`, `region`, `con_locations`, `classification` FROM `contacts` WHERE `category`='".BUSINESS_CAT."' AND `deleted`=0")) as $row) { ?>
							<option data-region="<?= $row['region'] ?>" data-location="<?= $row['con_locations'] ?>" data-classification="<?= $row['classification'] ?>" <?= $row['contactid'] == $businessid ? 'selected' : '' ?> value="<?= $row['contactid'] ?>"><?= $row['name'] ?></option>
						<?php } ?>
						<option value="ADD_NEW">Add New <?= BUSINESS_CAT ?></option>
					</select>
				</div>
				<div class="col-sm-1">
					<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="New Business" src="../img/icons/ROOK-add-icon.png"></a>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Contact,') !== false && $field_sort_field == 'Detail Contact' ) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><!--<span class="text-red">*</span>--> Contact Name:</label>
				<div class="col-sm-7">
					<select name="clientid" id="clientid" data-placeholder="Select a Contact..." data-category="<?= get_config($dbc, 'ticket_business_contact_'.$ticket_type) ?: (get_config($dbc, 'ticket_business_contact') ?: '%') ?>" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control" width="380">
						<option value=''></option>
						<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `businessid`, `region`, `con_locations`, `classification`, `name` FROM `contacts` WHERE `deleted`=0 AND `status`>0 AND (CONCAT(`first_name`,`last_name`) != '' OR `name` != '')")) as $row) {
							$selected = ( $clientid==$row['contactid'] ) ? 'selected="selected"' : ($businessid > 0 && $businessid != $row['businessid'] ? 'style="display:none;"' : '');
							echo '<option data-region="'.$row['region'].'" data-location="'.$row['con_locations'].'" data-classification="'.$row['classification'].'" data-business="'.$row['businessid'].'" '. $selected .' value="'. $row['contactid'] .'">'. $row['full_name'] .'</option>';
						} ?>
						<option value="ADD_NEW">Add New <?= CONTACTS_NOUN ?></option>
					</select>
				</div>
				<div class="col-sm-1">
					<a href="" onclick="viewProfile(this); return false;"><img class="inline-img pull-right no-toggle" src="../img/person.PNG" title="View Profile"></a>
					<a href="" onclick="$(this).closest('.form-group').find('select').val('ADD_NEW').change(); return false;"><img class="inline-img pull-right" data-history-label="New Contact" src="../img/icons/ROOK-add-icon.png"></a>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Contact Phone,') !== false && $field_sort_field == 'Detail Contact Phone' ) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Phone Numbers:</label>
				<div class="col-sm-8">
					<?php $show_names = false;
					if($get_ticket['businessid'] > 0 && $get_ticket['clientid'] > 0) {
						$show_names = true;
					}
					$contact_phone_list = $dbc->query("SELECT `contactid`,`first_name`,`last_name`,`name`,`office_phone`,`home_phone`,`cell_phone` FROM `contacts` WHERE `contactid` > 0 AND `contactid` IN ('{$get_ticket['businessid']}','{$get_ticket['clientid']}')");
					while($contact_phone = $contact_phone_list->fetch_assoc()) {
						if($contact_phone['home_phone'] != '') {
							echo ($show_names ? trim(decryptIt($contact['name']).($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']).': ') : '').'Home Phone: '.decryptIt($contact_phone['home_phone']).'<br />';
						}
						if($contact_phone['cell_phone'] != '') {
							echo ($show_names ? trim(decryptIt($contact['name']).($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']).': ') : '').'Cell Phone: '.decryptIt($contact_phone['cell_phone']).'<br />';
						}
						if($contact_phone['office_phone'] != '') {
							echo ($show_names ? trim(decryptIt($contact['name']).($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']).': ') : '').'Office Phone: '.decryptIt($contact_phone['office_phone']).'<br />';
						}
					} ?>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Rate Card,') !== false && $field_sort_field == 'Detail Rate Card' ) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label">Rate Card:</label>
			  <div class="col-sm-8">
				<select data-placeholder="Select Rate Card..." name="rate_card" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control"><option/>
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

		<?php if ( strpos($value_config, ',Detail Project,') !== false && $field_sort_field == 'Detail Project' && ($force_project == 'manual' || $force_project == '')) { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label"><span class="text-red">*</span> <?= PROJECT_NOUN ?> Name:</label>
			  <div class="col-sm-8">
				<select data-placeholder="Select <?= PROJECT_NOUN ?>..." name="projectid" id="projectid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control">
				  <option value=""></option>
				  <?php $query = mysqli_query($dbc,"SELECT projectid, projecttype, project_name, businessid, clientid, status FROM project WHERE deleted=0 AND (status NOT IN ('Archive') OR `projectid`='$projectid') ORDER BY project_name");
					while($row = mysqli_fetch_array($query)) {
						$project_business = '';
						if($row['businessid'] > 0) {
							$project_business = mysqli_fetch_array(mysqli_query($dbc, "SELECT `region`, `con_locations`, `classification` FROM `contacts` WHERE `contactid` = '{$row['businesssid']}'"));
						}
						echo "<option data-region='".$project_business['region']."' data-location='".$project_business['con_locations']."' data-classification='".$project_business['classification']."' data-business='".$row['businessid']."' data-client='".explode(',',trim($row['clientid'],','))[0]."' ";
						echo ($projectid == $row['projectid'] ? 'selected' : (($businessid > 0 && $businessid == $row['businessid']) || ($clientid > 0 && strpos(','.$row['clientid'].',', ",$clientid,") !== FALSE) || (!($businessid > 0) && !($clientid > 0)) || (trim($row['clientid'],',') == '' && !($row['businessid'] > 0)) ? '' : 'style="display:none;"'));
						echo " value='".$row['projectid']."'>".get_project_label($dbc, $row).'</option>';
					}
				  ?>
				</select>
			  </div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Heading,') !== false && $field_sort_field == 'Detail Heading') { ?>
			<div class="form-group clearfix">
				<label for="heading" class="col-sm-4 control-label text-right"><?= TICKET_NOUN ?> Name:</label>
				<div class="col-sm-8">
					<input name="heading" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control" value="<?= $get_ticket['heading'] ?>">
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Date,') !== false && $field_sort_field == 'Detail Date') { ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Scheduled Date:</label>
				<div class="col-sm-8 date_div">
					<input name="to_do_date" type="text" autocomplete="off" data-placeholder="Select a Date..." data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="datepicker form-control" value="<?= $get_ticket['to_do_date'] ?>" onchange="$(this).closest('.date_div').find('[name=to_do_end_date]').val(this.value).change();">
					<input type="hidden" name="to_do_end_date" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control datepicker" value="<?= date('Y-m-d',strtotime($get_ticket['to_do_end_date'])) ?>">
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Start Date Time,') !== false && $field_sort_field == 'Detail Start Date Time') { ?>
			<script type="text/javascript">
			function updateStartDateTime(input) {
				var date_time_string = input.value.toString();
				var start_date = date_time_string.substr(0,date_time_string.indexOf(' '));
				var start_time = date_time_string.substr(date_time_string.indexOf(' ')+1);
				$('[name="to_do_date"]').val(start_date).change();
				$('[name="start_time"]').val(start_time).change();
			}
			</script>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Scheduled Start Date &amp; Time:</label>
				<div class="col-sm-8">
					<input type="text" name="start_date_time" onchange="updateStartDateTime(this);" value="<?= $get_ticket['to_do_date'].' '.$get_ticket['start_time'] ?>" class="form-control dateandtimepicker">
					<input name="to_do_date" type="hidden" autocomplete="off" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="datepicker form-control" value="<?= $get_ticket['to_do_date'] ?>">
					<input name="start_time" type="hidden" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="datetimepicker-15 form-control" value="<?= $get_ticket['start_time'] ?>">
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail End Date Time,') !== false && $field_sort_field == 'Detail End Date Time') { ?>
			<script type="text/javascript">
			function updateEndDateTime(input) {
				var date_time_string = input.value.toString();
				var end_date = date_time_string.substr(0,date_time_string.indexOf(' '));
				var end_time = date_time_string.substr(date_time_string.indexOf(' ')+1);
				$('[name="to_do_end_date"]').val(end_date).change();
				$('[name="end_time"]').val(end_time).change();
			}
			</script>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Scheduled End Date &amp; Time:</label>
				<div class="col-sm-8">
					<input type="text" name="end_date_time" onchange="updateEndDateTime(this);" value="<?= $get_ticket['to_do_end_date'].' '.$get_ticket['end_time'] ?>" class="form-control dateandtimepicker">
					<input name="to_do_end_date" type="hidden" autocomplete="off" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="datepicker form-control" value="<?= $get_ticket['to_do_end_date'] ?>">
					<input name="end_time" type="hidden" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="datetimepicker-15 form-control" value="<?= $get_ticket['end_time'] ?>">
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Staff,') !== false && $field_sort_field == 'Detail Staff' ) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><!--<span class="text-red">*</span>--> Staff:</label>
				<div class="col-sm-8">
					<select name="contactid" id="contactid" data-placeholder="Select a Staff..." data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect form-control" width="380">
						<option value=''></option><?php
						foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name`, `businessid` FROM `contacts` WHERE `status`>0 AND `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0")) as $row) {
							echo '<option '.(strpos(','.$contactid.',',','.$row['contactid'].',') !== FALSE ? 'selected' : '').' value="'. $row['contactid'] .'">'. $row['first_name'].' '.$row['last_name'].'</option>';
						} ?>
					</select>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Staff Times,') !== false && $field_sort_field == 'Detail Staff Times') { ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Staff Start Time:
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter the time that Staff should arrive. This should be entered as hh:mm pp."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				</label>
				<div class="col-sm-8">
					<input name="start_time" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="datetimepicker-15 form-control" value="<?= $get_ticket['start_time'] ?>">
				</div>
			</div>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Staff End Time:
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter the time that Staff should be done. This should be entered as hh:mm pp."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				</label>
				<div class="col-sm-8">
					<input name="end_time" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="datetimepicker-15 form-control" value="<?= $get_ticket['end_time'] ?>">
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Member Times,') !== false && $field_sort_field == 'Detail Member Times') { ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Member Drop Off:
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter the time that Members will arrive. This should be entered as hh:mm pp."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				</label>
				<div class="col-sm-8">
					<input name="member_start_time" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="datetimepicker-15 form-control" value="<?= $get_ticket['member_start_time'] ?>">
				</div>
			</div>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Member Pick Up:
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter the time that Members will be picked up. This should be entered as hh:mm pp."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				</label>
				<div class="col-sm-8">
					<input name="member_end_time" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="datetimepicker-15 form-control" value="<?= $get_ticket['member_end_time'] ?>">
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Times,') !== false && $field_sort_field == 'Detail Times') { ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Scheduled Start Time:
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter the time the <?= TICKET_NOUN ?> will start. This should be entered as hh:mm pp."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				</label>
				<div class="col-sm-8">
					<input name="to_do_start_time" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="datetimepicker-15 form-control" value="<?= $get_ticket['to_do_start_time'] ?>">
				</div>
			</div>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Scheduled End Time:
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter the time the <?= TICKET_NOUN ?> will end. This should be entered as hh:mm pp."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				</label>
				<div class="col-sm-8">
					<input name="to_do_end_time" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="datetimepicker-15 form-control" value="<?= $get_ticket['to_do_end_time'] ?>">
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Duration,') !== false && $field_sort_field == 'Detail Duration') { ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Scheduled Duration:
					<span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Enter the duration for the <?= TICKET_NOUN ?>. This should be entered as hh:mm."><img src="<?= WEBSITE_URL; ?>/img/info.png" width="20"></a></span>
				</label>
				<div class="col-sm-8">
					<input name="max_time" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="timepicker-5 form-control" value="<?= $get_ticket['max_time'] ?>">
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Notes,') !== false && $field_sort_field == 'Detail Notes') { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Notes:</label>
				<div class="col-sm-12">
					<textarea name="notes" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control"><?= $get_ticket['notes'] ?></textarea>
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Max Capacity,') !== false && $field_sort_field == 'Detail Max Capacity') { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Max Capacity:</label>
				<div class="col-sm-8">
					<input name="max_capacity" type="number" min="0" step="1" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control" value="<?= $get_ticket['max_capacity'] ?>">
				</div>
			</div>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Staff Capacity,') !== false && $field_sort_field == 'Detail Staff Capacity') { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Staff Capacity:</label>
				<div class="col-sm-8">
					<input name="staff_capacity" type="number" min="0" step="1" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="form-control" value="<?= $get_ticket['staff_capacity'] ?>">
				</div>
			</div>
		<?php } ?>

		<?php if(strpos($value_config,',Detail Status,') !== FALSE && $field_sort_field == 'Detail Status') { ?>
			<div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Status:</label>
				<div class="col-sm-8">

					<select data-placeholder="Select a Status..." name="status" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" id="status" class="chosen-select-deselect form-control input-sm">
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

		<?php if ( strpos($value_config, ',Detail Image,') !== false && $field_sort_field == 'Detail Image') { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Attach Image:</label>
				<div class="col-sm-8">
					<?php if(!empty($get_ticket['attached_image'])) {
						if(file_exists('../Ticket/download/'.$get_ticket['attached_image'])) {
							echo '<a href="../Ticket/download/'.$get_ticket['attached_image'].'" target="_blank">View</a>';
						} else if(file_exists('../Calendar/download/'.$get_ticket['attached_image'])) {
							echo '<a href="../Calendar/download/'.$get_ticket['attached_image'].'" target="_blank">View</a>';
						}
					} ?>
					<input name="attached_image" type="file" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-filename-placement="inside" class="form-control" />
				</div>
			</div>
		<?php } ?>

		<?php if(strpos($value_config,',Detail Total Budget Time,') !== FALSE && $field_sort_field == 'Detail Total Budget Time') { ?>
			<div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Total Budget Time:</label>
				<div class="col-sm-8">
					<input type="text" name="total_budget_time" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="timepicker-15 form-control" value="<?= $get_ticket['total_budget_time'] ?>">
				</div>
			</div>
		<?php }
	} else { ?>
		<?php if ( strpos($value_config, ',Detail Business,') !== false && $field_sort_field == 'Detail Business') { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><!--<span class="text-red">*</span>--> Business:</label>
				<div class="col-sm-8">
					<?= get_contact($dbc,$businessid,'name') ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Business', get_contact($dbc,$businessid,'name')]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Contact,') !== false && $field_sort_field == 'Detail Contact' ) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><!--<span class="text-red">*</span>--> Name:</label>
				<div class="col-sm-8">
					<?php $contact_list = [];
					foreach(explode(',',$clientid) as $row) {
						if($row > 0) {
							$contact_list[] = get_contact($dbc, $row);
						}
					}
					echo implode('<br />',$contact_list); ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Name', implode('<br />',$contact_list)]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Contact Phone,') !== false && $field_sort_field == 'Detail Contact Phone' ) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Phone Numbers:</label>
				<div class="col-sm-8">
					<?php $show_names = false;
					if($get_ticket['businessid'] > 0 && $get_ticket['clientid'] > 0) {
						$show_names = true;
					}
					$contact_phone_list = $dbc->query("SELECT `contactid`,`first_name`,`last_name`,`name`,`office_phone`,`home_phone`,`cell_phone` FROM `contacts` WHERE `contactid` > 0 AND `contactid` IN ('{$get_ticket['businessid']}','{$get_ticket['clientid']}')");
					$phone_numbers = '';
					while($contact_phone = $contact_phone_list->fetch_assoc()) {
						if($contact_phone['home_phone'] != '') {
							$phone_numbers .= ($show_names ? trim(decryptIt($contact['name']).($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']).': ') : '').'Home Phone: '.decryptIt($contact_phone['home_phone']).'<br />';
						}
						if($contact_phone['cell_phone'] != '') {
							$phone_numbers .= ($show_names ? trim(decryptIt($contact['name']).($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']).': ') : '').'Cell Phone: '.decryptIt($contact_phone['cell_phone']).'<br />';
						}
						if($contact_phone['office_phone'] != '') {
							$phone_numbers .= ($show_names ? trim(decryptIt($contact['name']).($contact['name'] != '' && $contact['first_name'].$contact['last_name'] != '' ? ': ' : '').decryptIt($contact['first_name']).' '.decryptIt($contact['last_name']).': ') : '').'Office Phone: '.decryptIt($contact_phone['office_phone']).'<br />';
						}
					}
					echo $phone_numbers; ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Phone Numbers', $phone_numbers]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Project,') !== false && $field_sort_field == 'Detail Project' && ($force_project == 'manual' || $force_project == '')  && $access_view_project_details > 0) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><?= PROJECT_NOUN ?> Name:</label>
				<div class="col-sm-8">
					<?= get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='".$get_ticket['projectid']."'"))) ?>
				</div>
			</div>
			<?php $pdf_contents[] = [PROJECT_NOUN.' Name', get_project_label($dbc, mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='".$get_ticket['projectid']."'")))]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Heading,') !== false && $field_sort_field == 'Detail Heading') { ?>
			<div class="form-group clearfix">
				<label for="heading" class="col-sm-4 control-label text-right"><?= TICKET_NOUN ?> Name:</label>
				<div class="col-sm-8">
					<?= $get_ticket['heading'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = [TICKET_NOUN.' Name', $get_ticket['heading']]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Date,') !== false && $field_sort_field == 'Detail Date' && $access_view_project_details > 0) { ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Scheduled Date:</label>
				<div class="col-sm-8">
					<?= $get_ticket['to_do_date'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Date', $get_ticket['to_do_date']]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Start Date Time,') !== false && $field_sort_field == 'Detail Start Date Time') { ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Scheduled Start Date &amp; Time:</label>
				<div class="col-sm-8">
					<?= $get_ticket['to_do_date'].' '.$get_ticket['start_time'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Scheduled Start Date & Time', $get_ticket['to_do_date'].' '.$get_ticket['start_time']]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail End Date Time,') !== false && $field_sort_field == 'Detail End Date Time') { ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Scheduled End Date &amp; Time:</label>
				<div class="col-sm-8">
					<?= $get_ticket['to_do_end_date'].' '.$get_ticket['end_time'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Scheduled End Date & Time', $get_ticket['to_do_end_date'].' '.$get_ticket['end_time']]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Staff,') !== false && $field_sort_field == 'Detail Staff' && $access_view_project_details > 0) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right"><!--<span class="text-red">*</span>--> Staff:</label>
				<div class="col-sm-8">
					<?php $list_staff = [];
					foreach(explode(',',$contactid) as $row_staffid) {
						if($row_staffid > 0) {
							$list_staff[] = get_contact($dbc, $row_staffid);
						}
					}
					echo implode('<br />',$list_staff); ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Staff', implode('<br />',$list_staff)]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Staff Times,') !== false && $field_sort_field == 'Detail Staff Times' && $access_view_project_details > 0) { ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Staff Start Time:</label>
				<div class="col-sm-8">
					<?= $get_ticket['start_time'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Staff Start Time', $get_ticket['start_time']]; ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Staff End Time:</label>
				<div class="col-sm-8">
					<?= $get_ticket['end_time'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Staff End Time', $get_ticket['end_time']]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Member Times,') !== false && $field_sort_field == 'Detail Member Times' && $access_view_project_details > 0) { ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Member Drop Off:</label>
				<div class="col-sm-8">
					<?= $get_ticket['member_start_time'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Member Drop Off', $get_ticket['member_start_time']]; ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Member Pick Up:</label>
				<div class="col-sm-8">
					<?= $get_ticket['member_end_time'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Member Pick Up', $get_ticket['member_end_time']]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Times,') !== false && $field_sort_field == 'Detail Times' && $access_view_project_details > 0) { ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Scheduled Start Time:</label>
				<div class="col-sm-8">
					<?= $get_ticket['to_do_start_time'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Start Time', $get_ticket['to_do_start_time']]; ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Scheduled End Time:</label>
				<div class="col-sm-8">
					<?= $get_ticket['to_do_end_time'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['End Time', $get_ticket['to_do_end_time']]; ?>
			<div class="form-group clearfix">
				<label for="first_name" class="col-sm-4 control-label text-right">Scheduled Duration:</label>
				<div class="col-sm-8">
					<?= $get_ticket['max_time'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Duration', $get_ticket['max_time']]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Notes,') !== false && $field_sort_field == 'Detail Notes') { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Notes:</label>
				<div class="col-sm-8">
					<?= html_entity_decode($get_ticket['notes']) ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Notes', html_entity_decode($get_ticket['notes'])]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Max Capacity,') !== false && $field_sort_field == 'Detail Max Capacity' && $access_view_project_details > 0) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Max Capacity:</label>
				<div class="col-sm-8">
					<?= $get_ticket['max_capacity'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Max Capacity', $get_ticket['max_capacity']]; ?>
		<?php } ?>

		<?php if(strpos($value_config,',Detail Status,') !== FALSE && $field_sort_field == 'Detail Status' && $access_view_project_details > 0) { ?>
			<div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Status:</label>
				<div class="col-sm-8">
					<?= $status ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Status', $status]; ?>
		<?php } ?>

		<?php if ( strpos($value_config, ',Detail Image,') !== false && $field_sort_field == 'Detail Image' && $access_view_project_details > 0) { ?>
			<div class="form-group clearfix completion_date">
				<label for="first_name" class="col-sm-4 control-label text-right">Attach Image:</label>
				<div class="col-sm-8">
					<?php if(!empty($get_ticket['attached_image'])) {
						if(file_exists('../Ticket/download/'.$get_ticket['attached_image'])) {
							echo '<img src="../Ticket/download/'.$get_ticket['attached_image'].'">';
						} else if(file_exists('../Calendar/download/'.$get_ticket['attached_image'])) {
							echo '<img src="../Calendar/download/'.$get_ticket['attached_image'].'">';
						}
					} ?>
					<!-- <input name="attached_image" type="file" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-filename-placement="inside" class="form-control" /> -->
				</div>
			</div>
		<?php } ?>

		<?php if(strpos($value_config,',Detail Total Budget Time,') !== FALSE && $field_sort_field == 'Detail Total Budget Time') { ?>
			<div class="form-group">
				<label for="site_name" class="col-sm-4 control-label">Total Budget Time:</label>
				<div class="col-sm-8">
					<?= $get_ticket['total_budget_time'] ?>
				</div>
			</div>
			<?php $pdf_contents[] = ['Total Budget Time', $get_ticket['total_budget_time']]; ?>
		<?php }
	}
} ?>
