<div id="dialog-service-template" title="Add or Replace Services" style="display:none;">
	<b>Would you like to add or replace services?</b>
</div>
<?php if(!empty($_GET['customer_service_template']) && $ticketid > 0) {
	if($_GET['replace_services'] == 1) {
		$ticket_services = [];
		$ticekt_service_qty = [];
	} else {
		$ticket_services = explode(',',$get_ticket['serviceid']);
		if(empty(array_filter($ticket_services))) {
			$_GET['replace_services'] = 1;
		}
	}
	$template_services = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services_service_templates` WHERE `templateid` = '".$_GET['customer_service_template']."'"));
	$template_items = explode(',', $template_services['serviceid']);
	$rate_card = mysqli_query($dbc, "SELECT * FROM `rate_card` WHERE `clientid` ='{$get_ticket['clientid']}' AND `deleted` = 0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')")->fetch_assoc();

	foreach(explode('**',$rate_card['services']) as $service_i => $service) {
		$service_line = explode('#',$service);
		if(!in_array($service_line[0],$ticket_services) && in_array($service_line[0],$template_items)) {
			$ticket_services[] = $service_line[0];
		}
	}
	$ticket_services = implode(',',array_unique(array_filter($ticket_services)));
	mysqli_query($dbc, "UPDATE `tickets` SET `serviceid` = '$ticket_services', `service_templateid` = '".$_GET['customer_service_template']."' WHERE `ticketid` = '$ticketid'");
	if($_GET['mark_loaded'] == 1) {
		mysqli_query($dbc, "UPDATE `tickets` SET `service_templateid_loaded` = '1' WHERE `ticketid` = '$ticketid'");
	}
	if($_GET['replace_services'] == 1) {
		foreach(array_filter(explode(',',$ticket_services)) as $ticket_service) {
			$ticket_service_qty[] = 1;
		}
		$ticket_service_qty = implode(',', $ticket_service_qty);
		mysqli_query($dbc, "UPDATE `tickets` SET `service_qty` = '$ticket_service_qty' WHERE `ticketid` = '$ticketid'");
		echo '<script type="text/javascript">reload_billing();reload_service_checklist();</script>';
	} else {
		echo '<script type="text/javascript">reload_billing();$(\'[name="service_qty_group"]\').change();</script>';
	}
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
} else if(!empty($_GET['load_service_template']) && $ticketid > 0) {
	if($_GET['replace_services'] == 1) {
		$ticket_services = [];
		$ticekt_service_qty = [];
	} else {
		$ticket_services = explode(',',$get_ticket['serviceid']);
		if(empty(array_filter($ticket_services))) {
			$_GET['replace_services'] = 1;
		}
	}
	$template_services = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services_service_templates` WHERE `templateid` = '".$_GET['load_service_template']."'"));
	$template_items = explode(',', $template_services['serviceid']);
	foreach($template_items as $template_item) {
		if(!in_array($template_item,$ticket_services)) {
			$ticket_services[] = $template_item;
		}
	}
	$ticket_services = implode(',',array_unique(array_filter($ticket_services)));
	mysqli_query($dbc, "UPDATE `tickets` SET `serviceid` = '$ticket_services', `service_templateid` = '".$_GET['load_service_template']."' WHERE `ticketid` = '$ticketid'");
	if($_GET['mark_loaded'] == 1) {
		mysqli_query($dbc, "UPDATE `tickets` SET `service_templateid_loaded` = '1' WHERE `ticketid` = '$ticketid'");
	}
	if($_GET['replace_services'] == 1) {
		foreach(array_filter(explode(',',$ticket_services)) as $ticket_service) {
			$ticket_service_qty[] = 1;
		}
		$ticket_service_qty = implode(',', $ticket_service_qty);
		mysqli_query($dbc, "UPDATE `tickets` SET `service_qty` = '$ticket_service_qty' WHERE `ticketid` = '$ticketid'");
		echo '<script type="text/javascript">reload_billing();reload_service_checklist();</script>';
	} else {
		echo '<script type="text/javascript">reload_billing();$(\'[name="service_qty_group"]\').change();</script>';
	}
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
} ?>
<h3><?= (!empty($renamed_accordion) ? '<h3>'.$renamed_accordion.'</h3>' : (strpos($value_config, ',Ticket Details,') !== FALSE ? TICKET_NOUN.' Details' : 'Services')) ?></h3>
<script type="text/javascript">
$(document).ready(function() {
	setServiceFilters();
	calculateTimeEstimate();
	<?php if(strpos($value_config,',Service Limit Service Category,') !== FALSE) { ?>
		if($('#ticketid').val() > 0) {
			limitServiceCategory();
		}
	<?php } ?>
});
function setServiceFilters() {
    $(".service_category").off('change',service_category_filter).change(service_category_filter).change();
	$(".service_type").off('change',service_type_filter).change(service_type_filter).change();
	$('[name=serviceid],[name=service_qty],[name=service_total_time]').off('change',chooseServices).change(chooseServices);
	$('[name=serviceid],[name=service_qty],[name=service_total_time]').change(calculateTimeEstimate);
	<?php if(strpos($value_config,',Service Limit Service Category,') !== FALSE) { ?>
		$('[name="clientid"]').off('change',limitServiceCategory).change(limitServiceCategory);
	<?php } ?>
}
function service_category_filter() {
	var block = $(this).closest('.multi-block');
	var category = $(this).val();
	var type = block.find('.service_type').val();
	if(type == undefined) {
		type = '';
	}
	block.find('.service_type option').show().filter(function() { return (category != '' && $(this).data('category') != category); }).hide();
	block.find('.service_type').trigger('change.select2');
	block.find('.serviceid option').show().filter(function() { return ((type != '' && $(this).data('type') != type) || (category != '' && $(this).data('category') != category)); }).hide();
	block.find('.serviceid').trigger('change.select2');
}
function service_type_filter() {
	var block = $(this).closest('.multi-block');
	var category = block.find('.service_category').val();
	var type = $(this).val();
	block.find('.serviceid option').show().filter(function() { return ((type != '' && $(this).data('type') != type) || (category != '' && $(this).data('category') != category)); }).hide();
	block.find('.serviceid').trigger('change.select2');
}
function chooseServices() {
	if($('[name=services_cost]').data('manual') > 0 || !(ticketid > 0 || ticketid == 'multi')) {
		return;
	}
	var total_cost = 0;
	$('[name=serviceid]').each(function() {
		var qty = $(this).closest('.multi-block').find('[name=service_qty]').val();
		if(!(qty > 0)) {
			qty = 1;
		}
		var time_estimate = $(this).find('option:selected').data('estimated-hours');
		if(time_estimate != undefined) {
			var minutes = time_estimate.split(':');
			minutes = (parseInt(minutes[0])*60) + parseInt(minutes[1]);
			minutes = minutes * qty;
			var new_hours = parseInt(minutes / 60);
			var new_minutes = parseInt(minutes % 60);
			new_hours = new_hours.toString().length > 1 ? new_hours : '0'+new_hours.toString();
			new_minutes = new_minutes.toString().length > 1 ? new_minutes : '0'+new_minutes.toString();
			time_estimate = new_hours+':'+new_minutes;
		} else {
			time_estimate = '00:00';
		}
		$(this).closest('.multi-block').find('[name="service_estimated_hours"]').val(time_estimate);
        $('[name=est_time]').each(function() {
            if($(this).closest('.scheduled_stop').find('[name=type]').val() != '<?= get_config($dbc, 'delivery_default_pickup_type') ?>' && $(this).closest('.scheduled_stop').find('[name=type] option:selected').data('warehouse') != 'yes') {
                $(this).val(time_estimate).change();
            } else if ($(this).closest('.scheduled_stop').find('[name=type]').val() == '<?= get_config($dbc, 'delivery_default_pickup_type') ?>' && $(this).closest('.scheduled_stop').find('[name=type] option:selected').data('warehouse') != 'yes') {
                $(this).val('<?= time_decimal2time(get_config($dbc, 'delivery_default_pickup_time')) ?>').change();
            }
        });
		total_cost += ($(this).find('option:selected').data('rate-price') * qty);
	});
	$('[name=services_cost]').val(total_cost).change();

	//Update time estimate in billing
	var this_serviceid = $(this).closest('.multi-block').find('[name=serviceid]').val();
	var this_time_estimate = $(this).closest('.multi-block').find('[name="service_estimated_hours"]').val();
	$('[name="service_time_estimate"][data-serviceid="'+this_serviceid+'"]').val(this_time_estimate).trigger('change');
}
function changeDesc(cb) {
		var serviceids = [];
		$('[name="serviceid"]').each(function() {
			if($(this).val() != '' && $(this).val() != undefined) {
				serviceids.push($(this).val());
			}
		});

        $.ajax({    //create an ajax request to load_page.php
			type: "POST",
			url: "ticket_ajax_all.php?fill=ticketdesc",
			data: { serviceids: serviceids },
			dataType: "html",   //expect html to be returned
			success: function(response){
				console.log(response);
               tinyMCE.get('assign_work').setContent(response, {format : 'raw'});
			}
		});

    //}
}
var calculatingTimeEstimate = false;
function calculateTimeEstimate() {
	// var total_minutes = 0;
	// $('[name="service_estimated_hours"]').each(function() {
	// 	var minutes = $(this).val().split(':');
	// 	minutes = (parseInt(minutes[0])*60) + parseInt(minutes[1]);
 //        total_minutes += minutes;
	// });
	// var new_hours = parseInt(total_minutes / 60);
	// var new_minutes = parseInt(total_minutes % 60);
	// new_hours = new_hours.toString().length > 1 ? new_hours : '0'+new_hours.toString();
	// new_minutes = new_minutes.toString().length > 1 ? new_minutes : '0'+new_minutes.toString();

	// total_time_estimate = new_hours+':'+new_minutes;
	// $('.service_total_time_estimate').val(total_time_estimate);
	var ticketid = $('#ticketid').val();
    if(ticketid > 0 && !calculatingTimeEstimate) {
        calculatingTimeEstimate = true;
        $.ajax({
            url: '../Ticket/ticket_ajax_all.php?action=get_service_time_estimate',
            method: 'POST',
            data: { ticketid: ticketid },
            success:function(response) {
                $('.service_total_time_estimate').val(response);
                calculatingTimeEstimate = false;
            }
        });
    }
}
function limitServiceCategory() {
	<?php if(strpos($value_config,',Service Limit Service Category,') !== FALSE) { ?>
		var ticketid = $('#ticketid').val();
		$.ajax({
			url: '../Ticket/ticket_ajax_all.php?action=get_contact_service_category&ticketid='+ticketid,
			method: 'GET',
			success:function(response) {
				if(response != '') {
					$('[name="service_category_group"] option').hide();
					$('[name="service_category_group"] option[value="'+response+'"]').show();
					$('.service_category option').hide();
					$('.service_category option[value="'+response+'"]').show();
					$('[name="load_service_template"] option').hide();
					$('[name="load_service_template"] option[data-service-category="'+response+'"]').show();
				} else {
					$('[name="service_category_group"] option').show();
					$('.service_category option').show();
					$('[name="load_service_template"] option').show();
				}
				$('[name="service_category_group"]').trigger('change.select2');
				$('.service_category').trigger('change.select2');
				$('[name="load_service_template"]').trigger('change.select2');
			}
		});
	<?php } ?>
}
</script><?php

//New tickets should not show deleted Services but old tickets with deleted Services should
$query_services = '';
if(strpos($value_config, ',Service Rate Card,') !== FALSE) {
	$services = explode('**',mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `services` FROM `rate_card` WHERE `clientid` IN ('$rate_contact', '$businessid') AND `clientid` != '' AND `deleted`=0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') ORDER BY `clientid`='$rate_contact' DESC"))['services']);
	$service_list = [];
	$service_price = [];
	foreach($services as $service) {
		$service = explode('#',$service);
		if($service[0] > 0) {
			$service_list[] = $service[0];
			$service_price[] = $service[1];
		}
	}
	$query_services = "`serviceid` IN (".implode(',',$service_list).") AND "; ?>
    <script>
    reload_services = true;
    </script>
<?php }
$oldservice = mysqli_fetch_array(mysqli_query($dbc, "SELECT `serviceid` FROM `services` WHERE `heading`='{$get_ticket['sub_heading']}' AND `category`='{$get_ticket['service']}' AND `service_type`='{$get_ticket['service_type']}'"))[0];
if($oldservice > 0) {
	mysqli_query($dbc, "UPDATE `tickets` SET `service_type`='', `service`='', `sub_heading`='', `serviceid`=CONCAT('$oldservice,',`serviceid`), `service_total_time` = '' WHERE `ticketid`='$ticketid'");
}
$service_fields = (strpos($value_config,',Service Category,') !== FALSE ? 1 : 0) + (strpos($value_config,',Service Type,') !== FALSE ? 1 : 0) + (strpos($value_config,',Service Heading,') !== FALSE ? 1 : 0)  + (strpos($value_config,',Service Total Time,') !== FALSE ? 1 : 0) + (strpos($value_config,',Service Direct Time,') !== FALSE ? 1 : 0) + (strpos($value_config,',Service Indirect Time,') !== FALSE ? 1 : 0) + ((strpos($value_config,',Service Quantity,') !== FALSE || strpos($value_config,',Service # of Rooms') !== FALSE) ? 1 : 0) + (strpos($value_config,',Service Estimated Hours,') !== FALSE ? 1 : 0) + (strpos($value_config,',Service Fuel Charge,') !== FALSE ? 1 : 0);

if((strpos($value_config,',Service Customer Template,') !== FALSE || strpos($value_config,',Service Customer Template In Service Checklist,') !== FALSE) && !($strict_view > 0)) { ?>
	<script type="text/javascript">
	function getCustomerServiceTemplate() {
		var clientid = $('[name=clientid] option:selected').val();
		$.ajax({
			url: 'ticket_ajax_all.php?action=get_customer_service_templates&clientid='+clientid,
			type: 'GET',
			dataType: 'html',
			success: function(response) {
				$('[name="customer_service_template"]').html(response);
				$('[name="customer_service_template"]').trigger('change.select2');
				initSelectOnChanges();
				<?php if(strpos($value_config, ',Service Staff Checklist Default Customer Template,') !== FALSE) { ?>
					var templateid = '';
					$('[name="customer_service_template"] option').each(function() {
						if($(this).val() != undefined && $(this).val() != '') {
							templateid = $(this).val();
							return;
						}
					});
					loadCustomerServiceTemplate(templateid, 1);
				<?php } ?>
			}
		});
	}
	function addCustomerServiceTemplate() {
		var templateid = $('[name="customer_service_template"]').val();
		if(templateid != undefined && templateid > 0) {
		    $( "#dialog-service-template" ).dialog({
				resizable: false,
				height: "auto",
				width: ($(window).width() <= 500 ? $(window).width() : 500),
				modal: true,
				buttons: {
					"Add Services": function() {
						loadCustomerServiceTemplate(templateid, 0, 1);
						$(this).dialog('close');
					},
					"Replace Services": function() {
						loadCustomerServiceTemplate(templateid, 1, 1);
						$(this).dialog('close');
					},
			        Cancel: function() {
			        	$(this).dialog('close');
			        }
				}
			});
		}
	}
	function loadCustomerServiceTemplate(templateid, replace = 0, mark_loaded = 0) {
		$('#collapse_ticket_info,#tab_section_ticket_info').load('../Ticket/edit_ticket_tab.php?tab=ticket_info&customer_service_template='+templateid+'&ticketid='+ticketid+'&add_service_iframe=<?= $_GET['add_service_iframe'] ?>&mark_loaded='+mark_loaded+'&replace_services='+replace, function() {
			setSave();
			initSelectOnChanges();
			initInputs('#collapse_ticket_info');
			initInputs('#tab_section_ticket_info');
			setBilling();
			window.parent.$('[name="customer_service_template"]').val(templateid).trigger('change.select2');
			window.parent.$('[name="load_service_template"]').val(templateid).trigger('change.select2');
		});
	}
	</script>
	<div class="form-group">
		<label class="col-sm-4 control-label">Load Customer Template:</label>
		<div class="col-sm-8 <?= (strpos($value_config, ','."Service Staff Checklist One Service Template Only".',') === FALSE && strpos($value_config,',Service Group Cat Type All Services Combine Checklist,') !== FALSE) || !empty($_GET['add_service_iframe']) || !($get_ticket['service_templateid_loaded'] > 0) ? '' : 'readonly-block' ?>">
			<select name="customer_service_template" data-placeholder="Select a Customer Template" class="chosen-select-deselect form-control">
				<option></option>
				<?php $customer_templates = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '".(!empty($get_ticket['clientid']) ? $get_ticket['clientid'] : $service_contact)."'"))['service_templates'];
				if(!empty($customer_templates)) {
					foreach(explode(',',$customer_templates) as $customer_template) {
						$customer_template = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `services_service_templates` WHERE `templateid` = '$customer_template'"));
						if(!empty($customer_template)) { ?>
							<option value="<?= $customer_template['templateid'] ?>" <?= strpos($value_config, ','."Service Staff Checklist One Service Template Only".',') !== FALSE && $get_ticket['service_templateid'] == $customer_template['templateid'] ? 'selected' : '' ?>><?= $customer_template['name'] ?></option>
						<?php }
					}
				} ?>
			</select>
		</div>
	</div>
	<div class="clearfix"></div>
<?php }
if((strpos($value_config,',Service Load Template,') !== FALSE || strpos($value_config,',Service Load Template In Service Checklist,') !== FALSE) && !($strict_view > 0)) { ?>
	<script type="text/javascript">
	function loadServiceTemplate() {
		var templateid = $('[name="load_service_template"]').val();
		if(templateid != undefined && templateid > 0) {
		    $( "#dialog-service-template" ).dialog({
				resizable: false,
				height: "auto",
				width: ($(window).width() <= 500 ? $(window).width() : 500),
				modal: true,
				buttons: {
					"Add Services": function() {
						$('#collapse_ticket_info,#tab_section_ticket_info').load('../Ticket/edit_ticket_tab.php?tab=ticket_info&load_service_template='+templateid+'&ticketid='+ticketid+'&add_service_iframe=<?= $_GET['add_service_iframe'] ?>&mark_loaded=1', function() {
							setSave();
							initSelectOnChanges();
							initInputs('#collapse_ticket_info');
							initInputs('#tab_section_ticket_info');
							setBilling();
							window.parent.$('[name="customer_service_template"]').val(templateid).trigger('change.select2');
							window.parent.$('[name="load_service_template"]').val(templateid).trigger('change.select2');
						});
						$(this).dialog('close');
					},
					"Replace Services": function() {
						$('#collapse_ticket_info,#tab_section_ticket_info').load('../Ticket/edit_ticket_tab.php?tab=ticket_info&load_service_template='+templateid+'&ticketid='+ticketid+'&add_service_iframe=<?= $_GET['add_service_iframe'] ?>&mark_loaded=1&replace_services=1', function() {
							setSave();
							initSelectOnChanges();
							initInputs('#collapse_ticket_info');
							initInputs('#tab_section_ticket_info');
							setBilling();
							window.parent.$('[name="customer_service_template"]').val(templateid).trigger('change.select2');
							window.parent.$('[name="load_service_template"]').val(templateid).trigger('change.select2');
						});
						$(this).dialog('close');
					},
			        Cancel: function() {
			        	$('[name="load_service_template"]').val('').trigger('change.select2');
			        	$(this).dialog('close');
			        }
				}
			});
		}
	}
	</script>
	<div class="form-group">
		<label class="col-sm-4 control-label">Load Service Template:</label>
		<div class="col-sm-8 <?= (strpos($value_config, ','."Service Staff Checklist One Service Template Only".',') === FALSE && strpos($value_config,',Service Group Cat Type All Services Combine Checklist,') !== FALSE) || !empty($_GET['add_service_iframe']) || !($get_ticket['service_templateid_loaded'] > 0) ? '' : 'readonly-block' ?>">
			<select name="load_service_template" data-placeholder="Select a Service Template" class="chosen-select-deselect form-control">
				<option></option>
				<?php $client_service_category = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `service_category` FROM `contacts` WHERE `contactid` = '{$get_ticket['clientid']}'"))['service_category'];
				$cat_query = '';
				if(!empty($client_service_category) && strpos($value_config,',Service Limit Service Category,') !== FALSE) {
					$cat_query = " AND `service_category` = '$client_service_category'";
				}
				$service_templates = mysqli_query($dbc, "SELECT `services_service_templates`.* FROM `services_service_templates` WHERE IFNULL(NULLIF(`contactid`, 0),'') = '' AND `deleted` = 0".$cat_query);
				while($service_template = mysqli_fetch_assoc($service_templates)) { ?>
					<option data-service-category="<?= $service_template['service_category'] ?>" value="<?= $service_template['templateid'] ?>" <?= strpos($value_config, ','."Service Staff Checklist One Service Template Only".',') !== FALSE && $get_ticket['service_templateid'] == $service_template['templateid'] ? 'selected' : '' ?>><?= $service_template['name'] ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="clearfix"></div>
<?php }

if(!empty($_GET['add_service_iframe'])) { ?>
	<div class="main-screen" style="margin: 0; padding: 0; width: calc(100%); background: none; border: 0px;">
		<?php include('../Ticket/add_ticket_info_service.php'); ?>
	</div>
<?php } else {
	if(strpos($value_config,',Service Group Cat Type All Services Combine Checklist,') !== FALSE && !$generate_pdf) { ?>
		<div class="hidden_services" style="display:none;">
			<?php include('../Ticket/add_ticket_info_service.php') ?>
		</div>
		<?php if($access_services === TRUE) { ?>
			<div class="pull-right">
				<span class="popover-examples"><a data-toggle="tooltip" data-original-title="If the <?= TICKET_NOUN ?> is predetermined the services will show. If you would like to change the <?= TICKET_NOUN ?>, click this Edit button."><img src="<?= WEBSITE_URL ?>/img/info.png" class="inline-img small"></a></span>
				<a href="" onclick="addServices(this); return false;"><img src="../img/icons/ROOK-edit-icon.png" class="inline-img theme-color-icon" title="Edit Services"></a>
			</div>
			<div class="clearfix"></div>
		<?php } ?>
		<div class="service_checklist" style="overflow-x: auto;">
			<?php include('../Ticket/add_view_ticket_service_checklist.php'); ?>
		</div>
	<?php } else if(strpos($value_config,',Service Group Cat Type All Services,') !== FALSE && !$generate_pdf) {
		include('../Ticket/add_ticket_info_service.php');
	} else {
		if(strpos($value_config,',Service Inline,') !== FALSE) { ?>
			<div class="form-group">
				<div class="hide-titles-mob">
					<h4>Services</h4>
					<?php foreach ($field_sort_order as $field_sort_field) { ?>
						<?php if(strpos($value_config,',Service Category,') !== FALSE && $field_sort_field == 'Service Category') { ?>
							<label class="text-center col-sm-<?= floor(12 / $service_fields) ?>">Tab</label>
						<?php } ?>
						<?php if(strpos($value_config,',Service Type,') !== FALSE && $field_sort_field == 'Service Type') { ?>
							<label class="text-center col-sm-<?= floor(12 / $service_fields) ?>">Tab</label>
						<?php } ?>
						<?php if(strpos($value_config,',Service Heading,') !== FALSE && $field_sort_field == 'Service Heading') { ?>
							<label class="text-center col-sm-<?= floor(12 / $service_fields) ?>">Heading</label>
						<?php } ?>
						<?php if(strpos($value_config,',Service Total Time,') !== FALSE && $field_sort_field == 'Service Total Time') { ?>
							<label class="text-center col-sm-<?= floor(12 / $service_fields) ?>">Total Time</label>
						<?php } ?>
						<?php if(strpos($value_config,',Service Direct Time,') !== FALSE && $field_sort_field == 'Service Direct Time') { ?>
							<label class="text-center col-sm-<?= floor(12 / $service_fields) ?>">Direct Time</label>
						<?php } ?>
						<?php if(strpos($value_config,',Service Indirect Time,') !== FALSE && $field_sort_field == 'Service Indirect Time') { ?>
							<label class="text-center col-sm-<?= floor(12 / $service_fields) ?>">Indirect Time</label>
						<?php } ?>
						<?php if(strpos($value_config,',Service Estimated Hours,') !== FALSE && $field_sort_field == 'Service Estimated Hours') { ?>
							<label class="text-center col-sm-<?= floor(12 / $service_fields) ?>">Time Estimate</label>
						<?php } ?>
						<?php if(strpos($value_config,',Service Fuel Charge,') !== FALSE && $field_sort_field == 'Service Fuel Charge') { ?>
							<label class="text-center col-sm-<?= floor(12 / $service_fields) ?>">Fuel Surcharge</label>
						<?php } ?>
						<?php if((strpos($value_config,',Service Quantity,') !== FALSE && $field_sort_field == 'Service Quantity') || (strpos($value_config,',Service # of Rooms,') !== FALSE && $field_sort_field == 'Service # of Rooms')) { ?>
							<label class="text-center col-sm-<?= floor(12 / $service_fields) ?>"><?= $field_sort_field == 'Service # of Rooms' ? '# of Rooms' : 'Quantity' ?></label>
						<?php } ?>
					<?php } ?>
				</div>
                <div class="clearfix"></div>
		<?php }
		$total_time_estimate = 0;
        $service_list = array_filter();
		foreach(explode(',',(!empty($_GET['serviceid']) ? $_GET['serviceid'] : mysqli_fetch_array(mysqli_query($dbc, "SELECT `serviceid` FROM `tickets` WHERE `ticketid`='$ticketid'"))[0])) as $i => $serviceid) {
			if(!empty($serviceid) || $i == 0) {
				$query_mod = $query_services."(`deleted`=0 OR `serviceid`='$serviceid')";
				$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid`='$serviceid'"));
				if($_GET['from_type'] == 'customer_rate_services' && !($ticketid > 0)) {
					$service_contact = !empty($_GET['bid']) ? $_GET['bid'] : $_GET['clientid'];
					$get_ticket['service_qty'] = explode(',',$get_ticket['service_qty']);
					$get_ticket['service_qty'][$i] = mysqli_fetch_array(mysqli_query($dbc, "SELECT `num_rooms` FROM `contacts_services` WHERE `contactid` = '$service_contact' AND `serviceid` = '$serviceid'"))['num_rooms'];
					$get_ticket['service_qty'] = implode(',',$get_ticket['service_qty']);
				} ?>
				<div class="multi-block">
					<?php if($access_services === TRUE) { ?>
						<?php if(strpos($value_config,',Service Inline,') !== FALSE) {
							$col_num = 0; ?>
							<?php foreach ($field_sort_order as $field_sort_field) { ?>
								<?php if(strpos($value_config,',Service Category,') !== FALSE && $field_sort_field == 'Service Category') { ?>
									<div class="col-sm-<?= floor(12 / $service_fields) - (++$col_num == $service_fields && floor(12 / $service_fields) == (12 / $service_fields) ? 1 : 0) ?>"><label class="show-on-mob">Tab:</label>
										<select data-placeholder="Select a Tab..." name="service" class="chosen-select-deselect form-control service_category">
										  <option value=""></option>
										  <?php $query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE ". $query_mod ." order by category");
											while($row = mysqli_fetch_array($query)) {
												if($service['category'] == $row['category']) {
													$selected = ' selected';
												} else {
													$selected = '';
												}
												echo "<option ".$selected." value='". $row['category']."'>".$row['category'].'</option>';
											}
										  ?>
										</select>
									</div>
								<?php } ?>
								<?php if(strpos($value_config,',Service Type,') !== FALSE && $field_sort_field == 'Service Type') { ?>
									<div class="col-sm-<?= floor(12 / $service_fields) - (++$col_num == $service_fields && floor(12 / $service_fields) == (12 / $service_fields) ? 1 : 0) ?>"><label class="show-on-mob">Tab:</label>
										<select data-placeholder="Select a Type..." name="service_type" class="chosen-select-deselect form-control service_type">
										  <option value=""></option>
										  <?php $query = mysqli_query($dbc,"SELECT `service_type`, `category` FROM `services` WHERE ". $query_mod ." GROUP BY `service_type`, `category` ORDER BY `service_type`");
											while($row = mysqli_fetch_array($query)) {
												if($service['service_type'] == $row['service_type']) {
													$selected = ' selected';
												} else {
													$selected = '';
												}
												echo "<option ".$selected." data-category='".$row['category']."' value='". $row['service_type']."'>".$row['service_type'].'</option>';
											}
										  ?>
										</select>
									</div>
								<?php } ?>
								<?php if(strpos($value_config,',Service Heading,') !== FALSE && $field_sort_field == 'Service Heading') { ?>
									<div class="col-sm-<?= floor(12 / $service_fields) - (++$col_num == $service_fields && floor(12 / $service_fields) == (12 / $service_fields) ? 1 : 0) ?>"><label class="show-on-mob">Heading:</label>
										<select data-placeholder="Select a Heading..." name="serviceid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," class="chosen-select-deselect form-control serviceid">
										  <option value=""></option>
										  <?php $query = mysqli_query($dbc,"SELECT serviceid, heading, service_type, category, estimated_hours FROM services WHERE ". $query_mod ." order by heading");
											while($row = mysqli_fetch_array($query)) {
												if($serviceid == $row['serviceid']) {
													$selected = ' selected';
												} else {
													$selected = '';
												}
												$row_price = 0;
												if(strpos($value_config, ',Service Rate Card,') !== FALSE) {
													foreach($service_list as $j => $id) {
														if($row['serviceid'] == $id) {
															$row_price = $service_price[$j];
														}
													}
												}
												echo "<option ".$selected." data-rate-price='".$row_price."' data-category='".$row['category']."' data-type='".$row['service_type']."' data-estimated-hours='".(empty($row['estimated_hours']) ? '00:00' : $row['estimated_hours'])."' value='". $row['serviceid']."'>".$row['heading'].'</option>';
											}
										  ?>
										</select>
									</div>
								<?php } ?>

								<?php if(strpos($value_config,',Service Total Time,') !== FALSE && $field_sort_field == 'Service Total Time') { ?>
									<div class="col-sm-<?= floor(12 / $service_fields) - (++$col_num == $service_fields && floor(12 / $service_fields) == (12 / $service_fields) ? 1 : 0) ?>"><label class="show-on-mob">Total Time:</label>
										<select data-placeholder="Select a Time..." name="service_total_time" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," class="chosen-select-deselect form-control">
											<option value=""></option>
											<?php for($hours = 0.25; $hours <= 3; $hours += 0.25) { ?>
												<option <?php if (explode(',',$get_ticket['service_total_time'])[$i] == $hours) { echo  'selected="selected"'; } ?> value="<?= $hours ?>"><?= time_time2string($hours); ?></option>
											<?php } ?>
										</select>
									</div>
								<?php } ?>

								<?php if(strpos($value_config,',Service Direct Time,') !== FALSE && $field_sort_field == 'Service Direct Time') { ?>
									<div class="col-sm-<?= floor(12 / $service_fields) - (++$col_num == $service_fields && floor(12 / $service_fields) == (12 / $service_fields) ? 1 : 0) ?>"><label class="show-on-mob">Direct Time:</label>
										<select data-placeholder="Select a Time..." name="service_direct_time" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," class="chosen-select-deselect form-control">
											<option value=""></option>
											<?php for($hours = 0.25; $hours <= 3; $hours += 0.25) { ?>
												<option <?php if (explode(',',$get_ticket['service_direct_time'])[$i] == $hours) { echo  'selected="selected"'; } ?> value="<?= $hours ?>"><?= time_time2string($hours); ?></option>
											<?php } ?>
										</select>
									</div>
								<?php } ?>

								<?php if(strpos($value_config,',Service Indirect Time,') !== FALSE && $field_sort_field == 'Service Indirect Time') { ?>
									<div class="col-sm-<?= floor(12 / $service_fields) - (++$col_num == $service_fields && floor(12 / $service_fields) == (12 / $service_fields) ? 1 : 0) ?>"><label class="show-on-mob">Indirect Time:</label>
										<select data-placeholder="Select a Time..." name="service_indirect_time" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," class="chosen-select-deselect form-control">
											<option value=""></option>
											<?php for($hours = 0.25; $hours <= 3; $hours += 0.25) { ?>
												<option <?php if (explode(',',$get_ticket['service_indirect_time'])[$i] == $hours) { echo  'selected="selected"'; } ?> value="<?= $hours ?>"><?= time_time2string($hours); ?></option>
											<?php } ?>
										</select>
									</div>
								<?php } ?>

								<?php if(strpos($value_config,',Service Estimated Hours,') !== FALSE && $field_sort_field == 'Service Estimated Hours') {
									$estimated_hours = empty($service['estimated_hours']) ? '00:00' : $service['estimated_hours'];
									$qty = empty(explode(',',$get_ticket['service_qty'])[$i]) ? 1 : explode(',',$get_ticket['service_qty'])[$i];
									$minutes = explode(':', $estimated_hours);
									$minutes = ($minutes[0]*60) + $minutes[1];
									$minutes = $qty * $minutes;
									$new_hours = $minutes / 60;
									$new_minutes = $minutes % 60;
									$new_hours = sprintf('%02d', $new_hours);
									$new_minutes = sprintf('%02d', $new_minutes);
									$estimated_hours = $new_hours.':'.$new_minutes; ?>
									<div class="col-sm-<?= floor(12 / $service_fields) - (++$col_num == $service_fields && floor(12 / $service_fields) == (12 / $service_fields) ? 1 : 0) ?>"><label class="show-on-mob">Time Estimate:</label>
										<input name="service_estimated_hours" value="<?= $estimated_hours ?>" type="text" class="timepicker-5 form-control" disabled />
									</div>
								<?php } ?>
								<?php if((strpos($value_config,',Service Fuel Charge,') !== FALSE && $field_sort_field == 'Service Fuel Charge')) { ?>
									<div class="col-sm-<?= floor(12 / $service_fields) - (++$col_num == $service_fields && floor(12 / $service_fields) == (12 / $service_fields) ? 1 : 0) ?>"><label class="show-on-mob">Fuel Surcharge:</label>
										<input type="number" min=0 step="any" class="form-control" name="service_fuel_charge" data-group="service" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," value="<?= explode(',',$get_ticket['service_fuel_charge'])[$i] ?: 0 ?>">
									</div>
								<?php } ?>
								<?php if((strpos($value_config,',Service Quantity,') !== FALSE && $field_sort_field == 'Service Quantity') || (strpos($value_config,',Service # of Rooms,') !== FALSE && $field_sort_field == 'Service # of Rooms')) { ?>
									<div class="col-sm-<?= floor(12 / $service_fields) - (++$col_num == $service_fields && floor(12 / $service_fields) == (12 / $service_fields) ? 1 : 0) ?>"><label class="show-on-mob"><?= $field_sort_field == 'Service # of Rooms' ? '# of Rooms' : 'Quantity' ?>:</label>
										<input type="number" min=0 step="any" class="form-control" name="service_qty" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," value="<?= explode(',',$get_ticket['service_qty'])[$i] ?: 1 ?>">
									</div>
								<?php } ?>
							<?php } ?>
							<?php if(strpos($value_config,',Service Multiple,') !== FALSE) { ?>
								<div class="col-sm-1">
									<img class="inline-img pull-right" data-history-label="Service" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
									<img class="inline-img pull-right" data-history-label="Service" onclick="remMulti(this);" src="../img/remove.png">
								</div>
							<?php } ?>
						<?php } else { ?>
							<?php foreach ($field_sort_order as $field_sort_field) { ?>
								<?php if(strpos($value_config,',Details Help Desk,') !== FALSE && $field_sort_field == 'Details Help Desk') { ?>
									<?php if(empty($_GET['supportid'])) {
										if(empty($_GET['ticketid']) && empty($_GET['edit'])) { ?>
											<div class="form-group">
												<label for="site_name" class="col-sm-4 control-label">Add to Help Desk</label>
												<div class="col-sm-8">
													<input type="checkbox" value="1" name="add_to_helpdesk">
												</div>
											</div>
										<?php }
									} ?>
								<?php } ?>

								<?php if(strpos($value_config,',Service Category,') !== FALSE && $field_sort_field == 'Service Category') { ?>
									<div class="form-group">
									  <label for="site_name" class="col-sm-4 control-label">Service Tab:</label>
									  <div class="col-sm-8">
										<select data-placeholder="Select a Tab..." name="service" class="chosen-select-deselect form-control service_category">
										  <option value=""></option>
										  <?php $query = mysqli_query($dbc,"SELECT distinct(category) FROM services WHERE ". $query_mod ." order by category");
											while($row = mysqli_fetch_array($query)) {
												if($service['category'] == $row['category']) {
													$selected = ' selected';
												} else {
													$selected = '';
												}
												echo "<option ".$selected." value='". $row['category']."'>".$row['category'].'</option>';
											}
										  ?>
										</select>
									  </div>
									</div>
								<?php } ?>

								<?php if(strpos($value_config,',Service Type,') !== FALSE && $field_sort_field == 'Service Type') { ?>
									<div class="form-group">
									  <label for="site_name" class="col-sm-4 control-label">Service Tab:</label>
									  <div class="col-sm-8">
										<select data-placeholder="Select a Type..." name="service_type" class="chosen-select-deselect form-control service_type">
										  <option value=""></option>
										  <?php $query = mysqli_query($dbc,"SELECT `service_type`, `category` FROM `services` WHERE ". $query_mod ." GROUP BY `service_type`, `category` ORDER BY `service_type`");
											while($row = mysqli_fetch_array($query)) {
												if($service['service_type'] == $row['service_type']) {
													$selected = ' selected';
												} else {
													$selected = '';
												}
												echo "<option ".$selected." data-category='".$row['category']."' value='". $row['service_type']."'>".$row['service_type'].'</option>';
											}
										  ?>
										</select>
									  </div>
									</div>
								<?php } ?>

								<?php if(strpos($value_config,',Service Heading,') !== FALSE && $field_sort_field == 'Service Heading') { ?>
									<div class="form-group">
									  <label for="site_name" class="col-sm-4 control-label"><!--<span class="text-red">*</span>--> Service Heading:</label>
									  <div class="col-sm-<?= strpos($value_config,',Service Multiple,') !== FALSE ? '6' : '8' ?>">
										<select data-placeholder="Select a Heading..." name="serviceid" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," class="chosen-select-deselect form-control serviceid">
										  <option value=""></option>
										  <?php $query = mysqli_query($dbc,"SELECT serviceid, heading, service_type, category, estimated_hours FROM services WHERE ". $query_mod ." order by heading");
											while($row = mysqli_fetch_array($query)) {
												if($serviceid == $row['serviceid']) {
													$selected = ' selected';
												} else {
													$selected = '';
												}
												$row_price = 0;
												if(strpos($value_config, ',Service Rate Card,') !== FALSE) {
													foreach($service_list as $j => $id) {
														if($row['serviceid'] == $id) {
															$row_price = $service_price[$j];
														}
													}
												}
												echo "<option ".$selected." data-rate-price='".$row_price."' data-category='".$row['category']."' data-type='".$row['service_type']."' data-estimated-hours='".(empty($row['estimated_hours']) ? '00:00' : $row['estimated_hours'])."' value='". $row['serviceid']."'>".$row['heading'].'</option>';
											}
										  ?>
										</select>
									  </div>
									  <?php if(strpos($value_config,',Service Multiple,') !== FALSE) { ?>
										<div class="col-sm-2">
											<img class="inline-img pull-right" data-history-label="Service" onclick="addMulti(this);" src="../img/icons/ROOK-add-icon.png">
											<img class="inline-img pull-right" data-history-label="Service" onclick="remMulti(this);" src="../img/remove.png">
                                            <a href="" onclick="viewService(this); return false;"><img class="inline-img" src="../img/icons/eyeball.png"></a>

										</div>
									  <?php } ?>
									</div>
								<?php } ?>

								<?php if(strpos($value_config,',Service Estimated Hours,') !== FALSE && $field_sort_field == 'Service Estimated Hours') {
									$estimated_hours = empty($service['estimated_hours']) ? '00:00' : $service['estimated_hours'];
									$qty = empty(explode(',',$get_ticket['service_qty'])[$i]) ? 1 : explode(',',$get_ticket['service_qty'])[$i];
									$minutes = explode(':', $estimated_hours);
									$minutes = ($minutes[0]*60) + $minutes[1];
									$minutes = $qty * $minutes;
									$new_hours = $minutes / 60;
									$new_minutes = $minutes % 60;
									$new_hours = sprintf('%02d', $new_hours);
									$new_minutes = sprintf('%02d', $new_minutes);
									$estimated_hours = $new_hours.':'.$new_minutes; ?>
									<div class="form-group">
										<label class="col-sm-4 control-label">Time Estimate:</label>
										<div class="col-sm-8">
											<input name="service_estimated_hours" value="<?= $estimated_hours ?>" type="text" class="timepicker-5 form-control" disabled />
										</div>
									</div>
								<?php } ?>

								<?php if(strpos($value_config,',Service Fuel Charge,') !== FALSE && $field_sort_field == 'Service Fuel Charge') { ?>
									<div class="form-group">
									  <label for="site_name" class="col-sm-4 control-label">Fuel Surcharge:</label>
									  <div class="col-sm-8">
										<input type="number" min=0 step="any" class="form-control" name="service_fuel_charge" data-group="service" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," value="<?= explode(',',$get_ticket['service_fuel_charge'])[$i] ?: 0 ?>">
									  </div>
									</div>
								<?php } ?>

								<?php if((strpos($value_config,',Service Quantity,') !== FALSE && $field_sort_field == 'Service Quantity') || (strpos($value_config,',Service # of Rooms,') !== FALSE && $field_sort_field == 'Service # of Rooms')) { ?>
									<div class="form-group">
									  <label for="site_name" class="col-sm-4 control-label"><!--<span class="text-red">*</span>--> <?= $field_sort_field == 'Service # of Rooms' ? '# of Rooms' : 'Quantity' ?>:</label>
									  <div class="col-sm-8">
										<input type="number" min=0 step="any" class="form-control" name="service_qty" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" data-concat="," value="<?= explode(',',$get_ticket['service_qty'])[$i] ?: 1 ?>">
									  </div>
									</div>
								<?php } ?>
							<?php } ?>
						<?php } ?>
					<?php } else { ?>
						<?php foreach ($field_sort_order as $field_sort_field) { ?>
							<?php if(strpos($value_config,',Service Category,') !== FALSE && $field_sort_field == 'Service Category') { ?>
								<div class="form-group">
								  <label for="site_name" class="col-sm-4 control-label">Service Tab:</label>
								  <div class="col-sm-8">
									<?= $service['category'] ?>
								  </div>
								</div>
								<?php $pdf_contents[] = ['Service Category', $service['category']]; ?>
							<?php } ?>

							<?php if(strpos($value_config,',Service Type,') !== FALSE && $field_sort_field == 'Service Type') { ?>
								<div class="form-group">
								  <label for="site_name" class="col-sm-4 control-label">Service Tab:</label>
								  <div class="col-sm-8">
									<?= $service['service_type'] ?>
								  </div>
								</div>
								<?php $pdf_contents[] = ['Service Type', $service['service_type']]; ?>
							<?php } ?>

							<?php if(strpos($value_config,',Service Heading,') !== FALSE && $field_sort_field == 'Service Heading') { ?>
								<div class="form-group">
								  <label for="site_name" class="col-sm-4 control-label">Service Heading:</label>
								  <div class="col-sm-7">
									<?= $service['heading'] ?>
								  </div>
								</div>
								<?php $pdf_contents[] = ['Service Heading', $service['heading']]; ?>
							<?php } ?>

							<?php if(strpos($value_config,',Service Total Time,') !== FALSE && $field_sort_field == 'Service Total Time') { ?>
								<div class="form-group">
								  <label for="site_name" class="col-sm-4 control-label">Total Time:</label>
								  <div class="col-sm-7">
									<?= explode(',',$get_ticket['service_total_time'])[$i] ?>
								  </div>
								</div>
								<?php $pdf_contents[] = ['Total Time', time_time2string(explode(',',$get_ticket['service_total_time'])[$i])]; ?>
							<?php } ?>

							<?php if(strpos($value_config,',Service Direct Time,') !== FALSE && $field_sort_field == 'Service Direct Time') { ?>
								<div class="form-group">
								  <label for="site_name" class="col-sm-4 control-label">Direct Time:</label>
								  <div class="col-sm-7">
									<?= time_time2string(explode(',',$get_ticket['service_direct_time'])[$i]) ?>
								  </div>
								</div>
								<?php $pdf_contents[] = ['Direct Time', time_time2string(explode(',',$get_ticket['service_direct_time'])[$i])]; ?>
							<?php } ?>

							<?php if(strpos($value_config,',Service Indirect Time,') !== FALSE && $field_sort_field == 'Service Indirect Time') { ?>
								<div class="form-group">
								  <label for="site_name" class="col-sm-4 control-label">Indirect Time:</label>
								  <div class="col-sm-7">
									<?= time_time2string(explode(',',$get_ticket['service_indirect_time'])[$i]) ?>
								  </div>
								</div>
								<?php $pdf_contents[] = ['Indirect Time', time_time2string(explode(',',$get_ticket['service_indirect_time'])[$i])]; ?>
							<?php } ?>

							<?php if(strpos($value_config,',Service Estimated Hours,') !== FALSE && $field_sort_field == 'Service Estimated Hours') {
								$estimated_hours = empty($service['estimated_hours']) ? '00:00' : $service['estimated_hours'];
								$qty = empty(explode(',',$get_ticket['service_qty'])[$i]) ? 1 : explode(',',$get_ticket['service_qty'])[$i];
								$minutes = explode(':', $estimated_hours);
								$minutes = ($minutes[0]*60) + $minutes[1];
								$minutes = $qty * $minutes;
								$new_hours = $minutes / 60;
								$new_minutes = $minutes % 60;
								$new_hours = sprintf('%02d', $new_hours);
								$new_minutes = sprintf('%02d', $new_minutes);
								$estimated_hours = $new_hours.':'.$new_minutes;

								$total_time_estimate += $minutes; ?>
								<div class="form-group">
									<label class="col-sm-4 control-label">Time Estimate:</label>
									<div class="col-sm-8">
										<?= $estimated_hours ?>
									</div>
								</div>
								<?php $pdf_contents[] = ['Time Estimate', $estimated_hours]; ?>
							<?php } ?>

							<?php if((strpos($value_config,',Service Quantity,') !== FALSE && $field_sort_field == 'Service Quantity') || (strpos($value_config,',Service # of Rooms,') !== FALSE && $field_sort_field == 'Service # of Rooms')) { ?>
								<div class="form-group">
								  <label for="site_name" class="col-sm-4 control-label"><?= $field_sort_field == 'Service # of Rooms' ? '# of Rooms' : 'Quantity' ?>:</label>
								  <div class="col-sm-8">
									<input type="number" readonly class="form-control" name="service_qty" value="<?= explode(',',$get_ticket['service_qty'])[$i] ?>">
								  </div>
								</div>
								<?php $pdf_contents[] = ['Quantity', explode(',',$get_ticket['service_qty'])[$i]]; ?>
							<?php } ?>

							<?php if(strpos($value_config,',Service Fuel Charge,') !== FALSE && $field_sort_field == 'Service Fuel Charge') { ?>
								<div class="form-group">
								  <label for="site_name" class="col-sm-4 control-label">Fuel Surcharge:</label>
								  <div class="col-sm-8">
									<input type="number" readonly class="form-control" name="Fuel Charge" value="<?= explode(',',$get_ticket['Fuel Charge'])[$i] ?>">
								  </div>
								</div>
								<?php $pdf_contents[] = ['Fuel Surcharge', explode(',',$get_ticket['Fuel Charge'])[$i]]; ?>
							<?php } ?>
						<?php } ?>
					<?php } ?>
				</div>
				<?php if(strpos($value_config,',Service Inline,') === FALSE) { ?>
					<hr>
				<?php } ?>
			<?php }
		}
		if(strpos($value_config,',Service Inline,') !== FALSE) { ?>
			</div>
			<hr>
		<?php } ?>
	<?php } ?>
	<?php if(strpos($value_config,',Service Total Estimated Hours,') !== FALSE) { ?>
		<div class="form-group">
			<label for="site_name" class="col-sm-4 control-label">Total Time Estimate of Services:</label>
			<div class="col-sm-8">
				<input type="text" name="service_total_time_estimate" readonly class="form-control timepicker service_total_time_estimate" value="">
			</div>
		</div>
		<?php $new_hours = $total_time_estimate / 60;
		$new_minutes = $total_time_estimate % 60;
		$new_hours = sprintf('%02d', $new_hours);
		$new_minutes = sprintf('%02d', $new_minutes);
		$total_time_estimate = $new_hours.':'.$new_minutes;
		$pdf_contents[] = ['Total Time Estimate of Services', $total_time_estimate]; ?>
	<?php } ?>

	<?php if($access_services > 0) { ?>
		<?php foreach ($field_sort_order as $field_sort_field) { ?>
		    <?php if(strpos($value_config,',Details Heading,') !== FALSE && $field_sort_field == 'Details Heading') { ?>
		<div class="form-group">
			<label for="first_name" class="col-sm-4 control-label"><!--<span class="text-red">*</span>--> Heading:</label>
			<div class="col-sm-8">
				<input name="heading" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?php echo $heading; ?>" class="form-control">
				<input name="heading_auto" type="hidden" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $heading_auto ?>">
			</div>
		</div>
		    <?php } ?>

		    <?php if(strpos($value_config,',Details Where,') !== FALSE && $field_sort_field == 'Details Where') { ?>
		        <div class="form-group">
		            <label for="site_name" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Tile Name of the software that should be affected."><img src="../img/info.png" width="20"></a></span> Tile Name:</label>
		            <div class="col-sm-8">
		                <input name="details_where" class="form-control" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $details_where ?>" />
		            </div>
		        </div>
		    <?php } ?>
		    <!--
            <?php if(strpos($value_config,',Details Tile,') !== FALSE && $field_sort_field == 'Details Tile') { ?>
		        <div class="form-group">
		            <label for="site_name" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Tile Name of the software that should be affected"><img src="../img/info.png" width="20"></a></span> Tile Name:</label>
		            <div class="col-sm-8">
		                <input name="details_tile" class="form-control" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $get_ticket['details_tile'] ?>" />
		            </div>
		        </div>
		    <?php } ?>
		    <?php if(strpos($value_config,',Details Tab,') !== FALSE && $field_sort_field == 'Details Tab') { ?>
		        <div class="form-group">
		            <label for="site_name" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Tab, and Subtab for each part of the software that should be affected"><img src="../img/info.png" width="20"></a></span> Tab / Sub Tab:</label>
		            <div class="col-sm-8">
		                <input name="details_tab" class="form-control" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $get_ticket['details_tab'] ?>" />
		            </div>
		        </div>
		    <?php } ?>
            -->
		    <?php if(strpos($value_config,',Details Who,') !== FALSE && $field_sort_field == 'Details Who') { ?>
		        <div class="form-group">
		            <label for="site_name" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Tab, and Sub Tab for each part of the software that should be affected."><img src="../img/info.png" width="20"></a></span> Tab/Sub Tab:</label>
		            <div class="col-sm-8">
		                <input name="details_who" class="form-control" type="text" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" value="<?= $details_who ?>" />
		            </div>
		        </div>
		    <?php } ?>
		    <?php if(strpos($value_config,',Details Why,') !== FALSE && $field_sort_field == 'Details Why') { ?>
		        <div class="form-group">
		            <label for="site_name" class="col-sm-12 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Software URL of the exact page this is for."><img src="../img/info.png" width="20"></a></span> URL/Page:</label>
		            <div class="col-sm-12">
		                <textarea name="details_why" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" id="details_why" rows="4" cols="50" class="form-control" ><?= $details_why; ?></textarea>
		            </div>
		        </div>
		    <?php } ?>
		    <?php if(strpos($value_config,',Details What,') !== FALSE && $field_sort_field == 'Details What') { ?>
		        <div class="form-group">
		            <label for="site_name" class="col-sm-12 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Details of what needs to be done, changed, or added."><img src="../img/info.png" width="20"></a></span> What:</label>
		            <div class="col-sm-12">
		                <textarea name="details_what" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" id="details_what" rows="4" cols="50" class="form-control" ><?= $details_what; ?></textarea>
		            </div>
		        </div>
		    <?php } ?>

		    <?php if(strpos($value_config,',Service Description,') !== FALSE && $field_sort_field == 'Service Description') { ?>
              <div class="form-group">
                <label for="site_name" class="col-sm-4 control-label">Description:</label>
                <div class="col-sm-12">
                    <label class="form-checkbox">Use Service Description: <input type="checkbox" onclick='changeDesc(this);'></label>
                    <textarea name="assign_work" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" id="assign_work" rows="4" cols="50" class="form-control" ><?php echo $assign_work; ?></textarea>
                </div>
              </div>
		    <?php } ?>

		    <?php if(strpos($value_config,',Details Position,') !== FALSE && $field_sort_field == 'Details Position') { ?>
		        <div class="form-group">
		            <label for="site_name" class="col-sm-12 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="The position on the screen of the new field or button, what field it should be added after, etc."><img src="../img/info.png" width="20"></a></span> Position:</label>
		            <div class="col-sm-12">
		                <textarea name="details_position" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" id="details_position" rows="4" cols="50" class="form-control" ><?= $details_position; ?></textarea>
		            </div>
		        </div>
		    <?php } ?>
		  <?php if(strpos($value_config,',Service Preferred Staff,') !== FALSE && $field_sort_field == 'Service Preferred Staff') { ?>
				<div class="form-group">
				  <label for="site_name" class="col-sm-4 control-label">Preferred Staff:</label>
				  <div class="col-sm-8">
					<select name="preferred_staff" data-placeholder="Select Staff" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" class="chosen-select-deselect"><option></option>
						<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `status`>0 AND `deleted`=0")) as $staff_option) { ?>
							<option <?= $get_ticket['preferred_staff'] == $staff_option['contactid'] ? 'selected' : '' ?> value="<?= $staff_option['contactid'] ?>"><?= $staff_option['first_name'].' '.$staff_option['last_name'] ?></option>
						<?php } ?>
					</select>
				  </div>
				</div>
			<?php } ?>
		  <?php if(strpos($value_config,',Service Total Price,') !== FALSE && $field_sort_field == 'Service Total Price') { ?>

					<div class="form-group">
						<label for="site_name" class="col-sm-4 control-label">Total Price of Services:</label>
						<div class="col-sm-8">
							<input type="number" data-manual="<?= $get_ticket['service_cost_manual'] ?>" data-manual-field="service_cost_manual" min="0" step="any" name="services_cost" data-table="tickets" data-id="<?= $ticketid ?>" data-id-field="ticketid" <?= $editable ? '' : 'readonly' ?> class="form-control" value="<?= $get_ticket['services_cost'] ?>">
						</div>
					</div>
			<?php } ?>
		<?php } ?>
	<?php } else { ?>
		<?php foreach ($field_sort_order as $field_sort_field) { ?>
			<?php if(strpos($value_config,',Details Heading,') !== FALSE && $field_sort_field == 'Details Heading') { ?>
		<div class="form-group">
			<label for="first_name" class="col-sm-4 control-label">Heading:</label>
			<div class="col-sm-8">
				<?php echo $heading; ?>
			</div>
		</div>
				<?php $pdf_contents[] = ['Heading', $heading]; ?>
			<?php } ?>

			<?php if(strpos($value_config,',Service Preferred Staff,') !== FALSE && $field_sort_field == 'Service Preferred Staff') { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label">Preferred Staff:</label>
			  <div class="col-sm-8">
				<?= get_contact($dbc, $get_ticket['preferred_staff']) ?>
			  </div>
			</div>
			<?php $pdf_contents[] = ['Preferred Staff', get_contact($dbc, $get_ticket['preferred_staff'])]; ?>
		<?php } ?>
	    <?php if(strpos($value_config,',Details Where,') !== FALSE && $field_sort_field == 'Details Where') { ?>
	        <div class="form-group">
	            <label for="site_name" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Name of the Tile(s) this is for."><img src="../img/info.png" width="20"></a></span> Tile Name:</label>
	            <div class="col-sm-8">
	                <?= $details_where ?>"
	            </div>
	        </div>
	        <?php $pdf_contents[] = ['Where', $details_where]; ?>
	    <?php } ?>
	    <?php if(strpos($value_config,',Details Tile,') !== FALSE && $field_sort_field == 'Details Tile') { ?>
	        <div class="form-group">
	            <label for="site_name" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Tile Name of the software that should be affected"><img src="../img/info.png" width="20"></a></span> Tile Name:</label>
	            <div class="col-sm-8">
	                <?= $get_ticket['details_tile'] ?>"
	            </div>
	        </div>
	        <?php $pdf_contents[] = ['Tile Name', $get_ticket['details_tile']]; ?>
	    <?php } ?>
	    <?php if(strpos($value_config,',Details Tab,') !== FALSE && $field_sort_field == 'Details Tab') { ?>
	        <div class="form-group">
	            <label for="site_name" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Tab, and Subtab for each part of the software that should be affected"><img src="../img/info.png" width="20"></a></span> Tab / Sub Tab:</label>
	            <div class="col-sm-8">
	                <?= $get_ticket['details_tab'] ?>"
	            </div>
	        </div>
	        <?php $pdf_contents[] = ['Tab / Sub Tab', $get_ticket['details_tab']]; ?>
	    <?php } ?>
	    <?php if(strpos($value_config,',Details Who,') !== FALSE && $field_sort_field == 'Details Who') { ?>
	        <div class="form-group">
	            <label for="site_name" class="col-sm-4 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Any customers that will or may use this feature"><img src="../img/info.png" width="20"></a></span> Sub tile/Tab name:</label>
	            <div class="col-sm-8">
	                <?= $details_who ?>"
	            </div>
	        </div>
	        <?php $pdf_contents[] = ['Who', $details_who]; ?>
	    <?php } ?>
	    <?php if(strpos($value_config,',Details Why,') !== FALSE && $field_sort_field == 'Details Why') { ?>
	        <div class="form-group">
	            <label for="site_name" class="col-sm-12 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="How do they want to use the feature, or what do they want it for"><img src="../img/info.png" width="20"></a></span> URL/Page:</label>
	            <div class="col-sm-12">
	                <?= $details_why; ?>
	            </div>
	        </div>
	        <?php $pdf_contents[] = ['Why', $details_why]; ?>
	    <?php } ?>
	    <?php if(strpos($value_config,',Details What,') !== FALSE && $field_sort_field == 'Details What') { ?>
	        <div class="form-group">
	            <label for="site_name" class="col-sm-12 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="Details of what needs to be done, changed, or added"><img src="../img/info.png" width="20"></a></span> What:</label>
	            <div class="col-sm-12">
	                <?= $details_what; ?>
	            </div>
	        </div>
	        <?php $pdf_contents[] = ['What', $details_what]; ?>
	    <?php } ?>

			<?php if(strpos($value_config,',Service Description,') !== FALSE && $field_sort_field == 'Service Description') { ?>
	  <div class="form-group">
		<label for="site_name" class="col-sm-4 control-label">Description:</label>
		<div class="col-sm-8">
		  <?php echo html_entity_decode($assign_work); ?>
		</div>
	  </div>
		<?php $pdf_contents[] = ['Description', html_entity_decode($assign_work)]; ?>
			<?php } ?>

	    <?php if(strpos($value_config,',Details Position,') !== FALSE && $field_sort_field == 'Details Position') { ?>
	        <div class="form-group">
	            <label for="site_name" class="col-sm-12 control-label"><span class="popover-examples list-inline"><a data-toggle="tooltip" data-placement="top" title="The position on the screen of the new field or button, what field it should be added after, etc."><img src="../img/info.png" width="20"></a></span> Position:</label>
	            <div class="col-sm-12">
	                <?= $details_position; ?>
	            </div>
	        </div>
	        <?php $pdf_contents[] = ['Position', $details_position]; ?>
	    <?php } ?>
	  <?php if(strpos($value_config,',Service Preferred Staff,') !== FALSE && $field_sort_field == 'Service Preferred Staff') { ?>
			<div class="form-group">
			  <label for="site_name" class="col-sm-4 control-label">Preferred Staff:</label>
			  <div class="col-sm-8">
			  	<?php $preferred_staff = [];
			  	foreach(array_filter(explode(',', $get_ticket['preferred_staff'])) as $staff_id) {
			  		if($staff_id > 0) {
				  		$preferred_staff[] = get_contact($dbc, $staff_id);
			  		}
			  	}
			  	echo implode('<br />', $preferred_staff); ?>
			  </div>
			</div>
	        <?php $pdf_contents[] = ['Preferred Staff', implode('<br />', $preferred_staff)]; ?>
		<?php } ?>
	  <?php if(strpos($value_config,',Service Total Price,') !== FALSE && $field_sort_field == 'Service Total Price') { ?>
			<?php $editable = check_subtab_persmission($dbc, 'ticket', ROLE, 'edit_service_total');
			if(check_subtab_persmission($dbc, 'ticket', ROLE, 'view_service_total')) { ?>
				<div class="form-group">
					<label for="site_name" class="col-sm-4 control-label">Total Price of Services:</label>
					<div class="col-sm-8">
						<?= $get_ticket['services_cost'] ?>
					</div>
				</div>
		        <?php $pdf_contents[] = ['Total Price of Services', $get_ticket['service_cost']]; ?>
			<?php } ?>
		<?php } ?>
	<?php } ?>
	<?php } ?>
	<?php include('add_view_ticket_checklist.php'); ?>
<?php } ?>
