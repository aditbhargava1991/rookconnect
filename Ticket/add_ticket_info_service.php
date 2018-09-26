<?php include_once('../include.php');
if(!empty($_GET['reload_table']) || !empty($_GET['reload_hidden_services'])) {
	ob_clean();
	$ticketid = $_GET['ticketid'];
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `tickets` WHERE `ticketid` = '$ticketid'"));
	$value_config = get_field_config($dbc, 'tickets');
	if($get_ticket['ticket_type'] != '') {
		$value_config .= get_config($dbc, 'ticket_fields_'.$get_ticket['ticket_type']).',';
	}

	//Action Mode Fields
	if($_GET['action_mode'] == 1) {
		$value_config_all = $value_config;
		$value_config = ','.get_config($dbc, 'ticket_action_fields').',';
		if(!empty($get_ticket['ticket_type'])) {
			$value_config .= get_config($dbc, 'ticket_action_fields_'.$get_ticket['ticket_type']).',';
		}
		if(empty(trim($value_config,','))) {
			$value_config = $value_config_all;
		} else {
			foreach($action_mode_ignore_fields as $action_mode_ignore_field) {
				if(strpos(','.$value_config_all.',',','.$action_mode_ignore_field.',') !== FALSE) {
					$value_config .= ','.$action_mode_ignore_field;
				}
			}
			$value_config = ','.implode(',',array_intersect(explode(',',$value_config), explode(',',$value_config_all))).',';
		}
	}

	$query_daily = "";
	if(strpos($value_config,',Time Tracking Current,') !== FALSE) {
		$query_daily = " AND `date_stamp`='".date('Y-m-d')."' ";
	}
	if(isset($_GET['min_view'])) {
		$value_config = $min_view;
	}

	$ticket_roles = explode('#*#',get_config($dbc, 'ticket_roles'));
	$ticket_role = mysqli_query($dbc, "SELECT `position` FROM `ticket_attached` WHERE `src_table`='Staff' AND `position`!='' AND `item_id`='".$_SESSION['contactid']."' AND `ticketid`='$ticketid' AND `ticketid` > 0 $query_daily");
	if($get_ticket['to_do_date'] > date('Y-m-d') && strpos($value_config,',Ticket Edit Cutoff,') !== FALSE && $config_visible_function($dbc, 'ticket') < 1) {
		$access_services = false;
	} else if($get_ticket['status'] == 'Archive' || $force_readonly) {
		$access_services = false;
	} else if(config_visible_function($dbc, 'ticket') > 0) {
		$access_services = check_subtab_persmission($dbc, 'ticket', ROLE, 'services');
	} else if((count($ticket_roles) > 1 || explode('|',$ticket_roles[0])[0] != '') && mysqli_num_rows($ticket_role) > 0) {
		$ticket_role = html_entity_decode(mysqli_fetch_assoc($ticket_role)['position']);
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',html_entity_decode($ticket_role_level));
			if($ticket_role_level[0] > 0) {
				$ticket_role_level[0] = get_positions($dbc, $ticket_role_level[0], 'name');
			}
			if($ticket_role_level[0] == $ticket_role) {
				$access_services = in_array('services',$ticket_role_level);
			}
		}
	} else if(count(array_filter($arr, function ($var) { return (strpos($var, 'default') !== false); })) > 0) {
		foreach($ticket_roles as $ticket_role_level) {
			$ticket_role_level = explode('|',$ticket_role_level);
			if(in_array('default',$ticket_role_level)) {
				$access_services = in_array('services',$ticket_role_level);
			}
		}
	} else {
		$access_services = check_subtab_persmission($dbc, 'ticket', ROLE, 'services');
	}

	$category = $_POST['category'];
	$service_type = $_POST['service_type'];
	$services = [];
	foreach(explode(',',(!empty($_GET['serviceid']) ? $_GET['serviceid'] : mysqli_fetch_array(mysqli_query($dbc, "SELECT `serviceid` FROM `tickets` WHERE `ticketid`='$ticketid'"))[0])) as $i => $serviceid) {
		if($serviceid > 0) {
			$service = $dbc->query("SELECT * FROM `services` WHERE `serviceid`='$serviceid'")->fetch_assoc();
			if($service['category'] == $category && $service['service_type'] == $service_type) {
				$services[$service['serviceid']] = ['service'=>$service, 'quantity'=>explode(',',$get_ticket['service_qty'])[$i],'fuel_surcharge'=>explode(',',$get_ticket['service_fuel_charge'])[$i],'total_time'=>explode(',',$get_ticket['service_total_time'])[$i]];
			}
		}
	}
	$panel_i = $_GET['panel_i'];
}
?>
<script type="text/javascript">
	$(document).on('change', 'select[name="service_category_group"]', function() { serviceCategoryChange(this); });
	$(document).on('change', 'select[name="service_type_group"]', function() { serviceTypeChange(this); });
	function serviceCategoryChange(select) {
		var block = $(select).closest('.cattype_block');
		var category = $(block).find('[name="service_category_group"]').val();
		$(block).find('[name="service_type_group"] option').show();
		if(category != undefined && category != '') {
			$(block).find('[name="service_type_group"] option').filter(function() { return $(this).data('category') != category; }).hide();
		}
		$(block).find('[name="service_type_group"]').trigger('change.select2');

		serviceAccordionDisplay(block);
	}
	function serviceTypeChange(select) {
		var block = $(select).closest('.cattype_block');
		var service_type = $(block).find('[name="service_type_group"]');
		if(service_type != undefined && service_type.val() != '') {
			$(block).find('[name="service_category_group"]').val($(service_type).find('option:selected').data('category'));
		}
		$(block).find('[name="service_category_group"]').trigger('change.select2');

		serviceAccordionDisplay(block);
	}
	function serviceAccordionDisplay(block) {
		var ticketid = $('#ticketid').val();
		var category = $(block).find('[name="service_category_group"]').val();
		var service_type = $(block).find('[name="service_type_group"]').val();
		var panel_i = $('[name="service_panel_i"]').val();
		$('[name="service_panel_i"]').val(parseInt(panel_i) + 1);

		if(((category != undefined && category != '') || category == undefined) && ((service_type != undefined && service_type != '') || service_type == undefined)) {
			$.ajax({
				url: '../Ticket/add_ticket_info_service_group.php?ticketid='+ticketid+'&reload_table=true&panel_i='+panel_i,
				method: 'POST',
				data: { category: category, service_type: service_type },
				dataType: 'html',
				success: function(response) {
					destroyInputs('.cattype_block');
					$(block).find('.service_group').html(response);
					$(block).find('.service_group').show();
					$(block).find('.service_group_hide').hide();
					initInputs('.cattype_block');
					setSave();
					initSelectOnChanges();

					var title = '';
					if(category != undefined && category != '') {
						title += category;
					}
					if(service_type != undefined && service_type != '') {
						title += ': '+service_type;
					}
					if(title != '') {
						title += ' - Services';
					} else {
						title = 'Services';
					}
					$(block).closest('.cattype_block').find('.panel-title a').text(title);
				}
			});
		} else {
			$(block).find('.service_group').hide();
			$(block).find('.service_group_hide').show();
		}
	}
	function ticketServiceUpdated(input) {
		var tr = $(input).closest('tr');
		if($(input).is(':checked')) {
			$(tr).find('[data-table]').not('[name="serviceid"]').prop('disabled', false);
		} else {
			$(tr).find('[data-table]').not('[name="serviceid"]').prop('disabled', true);
		}
	}
	function addServiceGroup() {
		destroyInputs('.cattype_block');
		var block = $('.cattype_block').last();
		var clone = $(block).clone();
		var panel_i = $('[name="service_panel_i"]').val();
		$('[name="service_panel_i"]').val(parseInt(panel_i) + 1);

		clone.find('input,select').val('');
		clone.find('[name="service_qty"]').val(1);
		clone.find('[name="service_qty_group"]').val(1);
		clone.find('.service_group').html('').hide();
		clone.find('.service_group_hide').show();
		clone.find('.panel-collapse').prop('id', 'collapse_service_group_'+panel_i);
		clone.find('.panel-title a').prop('href', '#collapse_service_group_'+panel_i);
		clone.find('.panel-title a').html('Services<span class="glyphicon glyphicon-plus"></span>');

		block.after(clone);

		initInputs('.cattype_block');
		$(block).find('[name="service_qty_group"]').change();
	}
	function updateServiceQuantity(input) {
		var qty = $(input).val();
		if(qty < 1) {
			$(input).closest('.cattype_block').find('[name="serviceid"]:checked').prop('checked', false);
			$(input).closest('.cattype_block').find('[name="serviceid"]').first().change();
			// qty = 1;
			// $(input).val(1);
		}
		var block = $(input).closest('.cattype_block');
		$(block).find('[name="service_qty"]').val(qty);
		$(block).find('[name="service_qty"]').first().change();
	}
</script>
<?php $total_time_estimate = 0;
$ticket_services = [];
$cattype_qty = [];
foreach(explode(',',(!empty($_GET['serviceid']) ? $_GET['serviceid'] : mysqli_fetch_array(mysqli_query($dbc, "SELECT `serviceid` FROM `tickets` WHERE `ticketid`='$ticketid'"))[0])) as $i => $serviceid) {
	if($serviceid > 0 || $i == 0) {
		$query_mod = $query_services."(`deleted`=0 OR `serviceid`='$serviceid')";
		$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid`='$serviceid'"));
		if($_GET['from_type'] == 'customer_rate_services' && !($ticketid > 0)) {
			$service_contact = !empty($_GET['bid']) ? $_GET['bid'] : $_GET['clientid'];
			$get_ticket['service_qty'] = explode(',',$get_ticket['service_qty']);
			$get_ticket['service_qty'][$i] = mysqli_fetch_array(mysqli_query($dbc, "SELECT `num_rooms` FROM `contacts_services` WHERE `contactid` = '$service_contact' AND `serviceid` = '$serviceid'"))['num_rooms'];
			$get_ticket['service_qty'] = implode(',',$get_ticket['service_qty']);
		}
		$ticket_services[$service['category'].'#*#'.$service['service_type']][$service['serviceid']] = ['service'=>$service, 'quantity'=>explode(',',$get_ticket['service_qty'])[$i],'fuel_surcharge'=>explode(',',$get_ticket['service_fuel_charge'])[$i],'total_time'=>explode(',',$get_ticket['service_total_time'])[$i]];
		if(!($cattype_qty[$service['category'].'#*#'.$service['service_type']] > 0)) {
			$cattype_qty[$service['category'].'#*#'.$service['service_type']] = explode(',',$get_ticket['service_qty'])[$i];
		}
	}
} ?>

<div class="panel-group collapse-others <?= $_GET['add_service_iframe'] == 1 ? 'standard-body-content' : '' ?>" id="service_group_panel">
	<?php $panel_i = 0;
	foreach($ticket_services as $cattype => $services) {
		$category = explode('#*#',$cattype)[0];
		$service_type = explode('#*#',$cattype)[1];
		$service_table_display = false; ?>
		<div id="service_block_<?= $panel_i ?>" class="cattype_block panel panel-default">
			<div class="panel-heading no_load">
				<h4 class="panel-title">
					<a data-toggle="collapse" data-parent="#service_group_panel" href="#collapse_service_group_<?= $panel_i ?>">
						<?= $category.(!empty($service_type) ? ': '.$service_type : '') ?> - Services<span class="glyphicon glyphicon-plus"></span>
					</a>
				</h4>
			</div>

			<div id="collapse_service_group_<?= $panel_i ?>" class="panel-collapse collapse">
				<div class="panel-body white-background">
					<?php if(strpos($value_config,',Service Category,') !== FALSE) {
						$service_table_display = !empty($category) ? true : false; ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Category:</label>
							<div class="col-sm-8 <?= !$access_services ? 'readonly-block' : '' ?>">
								<select name="service_category_group" data-placeholder="Select a Category" class="chosen-select-deselect form-control" <?= !$access_services ? 'readonly' : '' ?>><option></option>
									<?php $service_categories = $dbc->query("SELECT `category` FROM `services` WHERE `deleted`=0 GROUP BY `category` ORDER BY `category`");
									while($row_cat = $service_categories->fetch_assoc()['category']) { ?>
										<option <?= $row_cat == $category ? 'selected' : '' ?> value="<?= $row_cat ?>"><?= $row_cat ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php } ?>
					<?php if(strpos($value_config,',Service Type,') !== FALSE) {
						$service_table_display = (!empty($category) && !empty($service_type)) ? true : false; ?>
						<div class="form-group">
							<label class="col-sm-4 control-label">Service Tab:</label>
							<div class="col-sm-8 <?= !$access_services ? 'readonly-block' : '' ?>">
								<select name="service_type_group" data-placeholder="Select a Service Tab" class="chosen-select-deselect form-control" <?= !$access_services ? 'readonly' : '' ?>><option></option>
									<?php $service_categories = $dbc->query("SELECT `category`, `service_type` FROM `services` WHERE `deleted`=0 GROUP BY CONCAT(`category`,`service_type`) ORDER BY `service_type`");
									while($service_row = $service_categories->fetch_assoc()) { ?>
										<option data-category="<?= $service_row['category'] ?>" value="<?= $service_row['service_type'] ?>" <?= !empty($category) && $category != $service_row['category'] ? 'style="display:none;"' : '' ?> <?= $category == $service_row['category'] && $service_type == $service_row['service_type'] ? 'selected' : '' ?>><?= $service_row['service_type'] ?></option>
									<?php } ?>
								</select>
							</div>
						</div>
						<div class="clearfix"></div>
					<?php } ?>
					<?php if(strpos($value_config,',Service Quantity,') !== FALSE || strpos($value_config,',Service # of Rooms,') !== FALSE) { ?>
						<div class="form-group">
							<label class="col-sm-4 control-label"><?= strpos($value_config,',Service # of Rooms,') !== FALSE ? '# of Rooms' : 'Quantity' ?>:</label>
							<div class="col-sm-8 <?= !$access_services ? 'readonly-block' : '' ?>">
								<input type="number" class="form-control" name="service_qty_group" value="<?= $cattype_qty[$category.'#*#'.$service_type] > 0 ? $cattype_qty[$category.'#*#'.$service_type] : 1 ?>" onchange="updateServiceQuantity(this);">
							</div>
						</div>
						<div class="clearfix"></div>
					<?php } ?>
					<div class="service_group" <?= $service_table_display ? '' : 'style="display:none;"' ?>>
						<?php include('../Ticket/add_ticket_info_service_group.php'); ?>
					</div>
					<div class="service_group_hide" <?= !$service_table_display ? '' : 'style="display:none;"' ?>>
						Please select a Tab and Service Tab to display Services.
					</div>
				</div>
			</div>
		</div>
		<?php $panel_i++;
	} ?>
</div>
<input type="hidden" name="service_panel_i" value="<?= $panel_i ?>">
<?php if($access_all === TRUE && strpos($value_config,',Service Multiple,') !== FALSE) { ?>
	<button class="btn brand-btn pull-right gap-bottom" onclick="addServiceGroup(); return false;">Add Service Group</button>
	<div class="clearfix"></div>
<?php } ?>