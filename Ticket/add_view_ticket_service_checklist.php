<?php include_once('../include.php');
include_once('../Ticket/field_list.php');

if(isset($_GET['ticketid']) && empty($ticketid)) {
	ob_clean();
	$strict_view = strictview_visible_function($dbc, 'ticket');
	$tile_security = get_security($dbc, ($_GET['tile_name'] == '' ? 'ticket' : 'ticket_type_'.$_GET['tile_name']));
	if($strict_view > 0) {
		$tile_security['edit'] = 0;
		$tile_security['config'] = 0;
	}
	$ticketid = filter_var($_GET['ticketid'],FILTER_SANITIZE_STRING);
	$get_ticket = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM tickets WHERE ticketid='$ticketid'"));
	$ticket_type = $get_ticket['ticket_type'];
	$value_config = get_field_config($dbc, 'tickets');
	if(!empty($ticket_type)) {
		$value_config .= get_config($dbc, 'ticket_fields_'.$ticket_type).',';
	}

	//Get Security Permissions
	$ticket_roles = explode('#*#',get_config($dbc, 'ticket_roles'));
	$ticket_role = mysqli_query($dbc, "SELECT `position` FROM `ticket_attached` WHERE `src_table`='Staff' AND `position`!='' AND `item_id`='".$_SESSION['contactid']."' AND `ticketid`='$ticketid' AND `ticketid` > 0 AND `deleted` = 0 $query_daily");
	if(!empty($get_ticket['status']) && strpos($uneditable_statuses, ','.$get_ticket['status'].',') !== FALSE) {
		$strict_view = 1;
	}
	if(($get_ticket['to_do_date'] > date('Y-m-d') && strpos($value_config,',Ticket Edit Cutoff,') !== FALSE && $config_access < 1) || $strict_view > 0) {
		$access_services = false;
	} else if($get_ticket['status'] == 'Archive' || $force_readonly) {
		$access_services = false;
	} else if($config_access > 0) {
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

	//Action Mode Fields
	if($_GET['action_mode'] == 1) {
		$value_config_all = $value_config;
		$value_config = ','.get_config($dbc, 'ticket_action_fields').',';
		if(!empty($ticket_type)) {
			$value_config .= get_config($dbc, 'ticket_action_fields_'.$ticket_type).',';
		}
		if(empty(trim($value_config,','))) {
			$value_config = $value_config_all;
		} else {
			foreach($action_mode_ignore_fields as $action_mode_ignore_field) {
				if(strpos(','.$value_config_all.',',','.$action_mode_ignore_field.',') !== FALSE) {
					$value_config .= ','.$action_mode_ignore_field;
				}
			}
		}
	}
} ?>

<script type="text/javascript">
function checkAllFields(chk) {
	var block = $(chk).closest('table');
	var staffid = $(chk).data('staffid');

	$(block).find('td[data-staffid='+staffid+'] [name=field_checkbox]:not(:checked)').prop('checked', true).change();
}
function checkAllFieldsMobile(chk) {
	var block = $(chk).closest('.service-checklist').find('table');
	var staffid = $(chk).data('staffid');

	$(block).find('td[data-staffid='+staffid+'] [name=field_checkbox]:not(:checked)').prop('checked', true).change();
}
function checkChartField(chk, checked, index = 1) {
	var field = $(chk).closest('td');
	var ticketid = $('#ticketid').val();
	var staffid = $(field).data('staffid');
	var serviceid = $(field).data('serviceid');

	var data = { ticketid: ticketid, staffid: staffid, serviceid: serviceid, checked: checked, index: index };

	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=ticket_service_checklist',
		type: 'POST',
		data: data,
		success: function(response) {
			if(checked == 1) {
				$(field).find('[name="field_checkbox"]').hide();
				$(field).find('.id-circle').show();
			} else {
				$(field).find('[name="field_checkbox"]').prop('checked',false).show();
				$(field).find('.id-circle').hide();
			}
			var history_div = $(field).closest('tr').find('.service_history');
			reloadServiceHistory(history_div, index);
		}
	});
}
function reloadServiceHistory(div, index) {
	var ticketid = $('#ticketid').val();
	var serviceid = $(div).data('serviceid');

	var data = { ticketid: ticketid, serviceid: serviceid, index: index };

	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=ticket_service_checklist_history',
		type: 'POST',
		data: data,
		success: function(response) {
			$(div).html(response);
		}
	});
}
function displayServiceExtraBilling() {
	$('.service_extra_billing_comment').show();
}
function addServiceExtraBilling(a) {
	var ticketid = $('#ticketid').val();
	var comment = $(a).closest('.service_extra_billing_comment').find('[name="service_extra_billing"]').val();

	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=add_service_extra_billing',
		type: 'POST',
		data: { ticketid: ticketid, comment: comment },
		success: function(response) {
			destroyInputs('.service_extra_billing_comment');
			$(a).closest('.service_extra_billing_comment').find('[name="service_extra_billing"]').val('');
			$('.service_extra_billing_comment').hide();
			$('.service_extra_billing').show();
			reload_service_extra_billing();
			initInputs('.service_extra_billing_comment');
		}
	});
}
function addAnotherRoomServiceChecklist(btn) {
	var ticketid = $('#ticketid').val();
	var copy_values = $(btn).data('copy-values');
	var block = $(btn).closest('.service-checklist').find('.service_checklist_table');
	var services = [];
	$(block).find('.service_checklist_row').each(function() {
		var serviceid = $(this).data('serviceid');
		var index = $(this).data('service-index');
		services.push(serviceid+'#*#'+index);
	});
	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=add_another_room_service_checklist',
		type: 'POST',
		data: { ticketid: ticketid, copy_values: copy_values, services: services },
		success: function(response) {
			reload_service_checklist();
		}
	});
}
function removeRoomServiceChecklist(btn) {
	var category = $(btn).data('category');
	var service_type = $(btn).data('service-type');
	$('.cattype_block').each(function() {
		if($(this).find('[name="service_category_group"]').val() == category && $(this).find('[name="service_type_group"]').val() == service_type) {
			var quantity = $(this).find('[name="service_qty_group"]').val();
			$(this).find('[name="service_qty_group"]').val(parseInt(quantity) - 1).change();
			return;
		}
	});
}
var scrolling_to_top = '';
function scrollToChecklist(a) {
	if(self !== top) {
		var block = $(a).closest('.panel').find('.panel-collapse');
		clearInterval(scrolling_to_top);
		scrolling_to_top = setInterval(function() {
			if($(block).hasClass('in')) {
				clearInterval(scrolling_to_top);
				$('html,body').scrollTop($(a).offset().top + $('.standard-body').scrollTop() - $('.standard-body').offset().top);	
			}
		},250);
	} else {
		if($(a).closest('.block-panels').length > 0) {
			$(window).scrollTop($(a).offset().top);
		} else {
			$('.main-screen .main-screen').scrollTop($(a).offset().top + $('.main-screen .main-screen').scrollTop() - $('.main-screen .main-screen').offset().top);
		}
	}
}
</script>

<h4>Service Checklist</h4>
<?php if(strpos($value_config,','."Service Staff Checklist".',') !== FALSE || strpos($value_config,',Service Group Cat Type All Services Combine Checklist,') !== FALSE) {
	if(!empty($get_ticket['serviceid'])) {
		if(strpos($value_config, ','."Service Staff Checklist Group Cat Type".',') !== FALSE) { ?>
			<div class="panel-group collapse-others" id="service_checklist_panel">
				<?php $services = $get_ticket['serviceid'];
				$staffs_list = [];

				$staffs_attached = mysqli_query($dbc, "SELECT * FROM `ticket_attached` WHERE `src_table` LIKE 'Staff%' AND `ticketid` = '".$get_ticket['ticketid']."' AND `deleted` = 0");
				while($row = mysqli_fetch_assoc($staffs_attached)) {
					if((strpos($value_config, ','."Service Staff Checklist Checked In Staff".',') === FALSE || $row['arrived'] > 0) && !in_array($staffs_list) && $row['item_id'] > 0) {
						$staffs_list[] = $row['item_id'];
					}
				}
				if(strpos($value_config, ','."Service Staff Checklist Checked In Staff".',') !== FALSE) {
					echo '<input type="hidden" name="reload_checklist_on_checkin" value="1">';
				}

				$checklist_services = [];
				$service_qty = explode(',',$get_ticket['service_qty']);
				foreach(explode(',', $services) as $i => $serviceid) {
					if($serviceid > 0) {
						$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$serviceid'"));

						if(!empty($service)) {
							for($service_i = 1; $service_i <= ($service_qty[$i] > 0 ? $service_qty[$i] : 1); $service_i++) {
								$checklist_services[$service['category'].'#*#'.$service['service_type'].'#*#'.$service_i][$service['serviceid']] = $service;
							}
						}
					}
				}

				$service_checklist_i = 0;
				foreach($checklist_services as $cattype => $services) {
					$category = explode('#*#',$cattype)[0];
					$service_type = explode('#*#',$cattype)[1];
					$index = explode('#*#',$cattype)[2]; ?>
					<div class="panel panel-default">
						<div class="panel-heading no_load">
							<h4 class="panel-title">
								<a data-toggle="collapse" data-parent="#service_checklist_panel" href="#collapse_service_checklist_<?= $service_checklist_i ?>" <?= strpos($value_config, ','."Service Staff Checklist Scroll To Accordion".',') !== FALSE ? 'onclick="scrollToChecklist(this);"' : '' ?>>
									<?= $category.(!empty($service_type) ? ': '.$service_type.($index > 1 ? ' #'.$index : '') : '') ?> - Services<span class="glyphicon glyphicon-plus"></span>
								</a>
							</h4>
						</div>

						<div id="collapse_service_checklist_<?= $service_checklist_i ?>" class="panel-collapse collapse">
							<div class="panel-body white-background" style="overflow-x: auto;">
								<div id="no-more-tables" class="service-checklist">
									<div class="show-on-mob pull-right">								
										<?php foreach($staffs_list as $staff_id) {
											echo '<label'.($strict_view > 0 ? 'class="readonly-block"' : '').'><input type="checkbox" name="check_all_services" onclick="checkAllFieldsMobile(this)" data-staffid="'.$staff_id.'" '.($strict_view > 0 ? 'readonly disabled' : '' ).'><b> Check All for '.get_contact($dbc, $staff_id).'</b></label><div class="clearfix"></div>';
										} ?>
										<div class="clearfix"></div>
									</div>
									<div class="clearfix"></div>

									<table class="table table-bordered service_checklist_table">
										<?php $pdf_content .= 'Category: '.$category.'<br>';
										$pdf_content .= 'Service Type: '.$service_type.($index > 1 ? ' #'.$index : '').'<br>';
										$pdf_content .= '<table cellpadding="1" border="1">';
										$pdf_content .= '<tr>';
										$pdf_content .= '<th>Service</th>'; ?>
										<tr class="hidden-xs">
											<th style="min-width: 15em;">Service</th>
											<?php foreach($staffs_list as $staff_id) {
												echo '<th style="min-width: 5em;">'.get_contact($dbc, $staff_id).'<br><label'.($strict_view > 0 ? 'class="readonly-block"' : '').'><input type="checkbox" name="check_all_services" onclick="checkAllFields(this)" data-staffid="'.$staff_id.'" '.($strict_view > 0 ? 'readonly disabled' : '' ).'> Check All</label></th>';
												$pdf_content .= '<th>'.get_contact($dbc, $staff_id).'</th>';
											} ?>
											<th style="min-width: 15em; <?= strpos($value_config, ','."Service Staff Checklist History".',') !== FALSE ? '' : 'display:none;' ?>">History</th>
										</tr>
										<?php $pdf_content .= '<th>History</th>';
										$pdf_content .= '</tr>';
										foreach($services as $service) {
											$serviceid = $service['serviceid'];
											$pdf_content .= '<tr>';
											$pdf_content .= '<td>'.$service['heading'].'</td>'; ?>
											<tr class="service_checklist_row" data-serviceid="<?= $serviceid ?>" data-service-index="<?= $index ?>">
												<td data-title="Service"><?= $service['heading'] ?></td>
												<?php foreach($staffs_list as $staff_id) {
													$staff = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$staff_id'"));
													$initials = ($staff['initials'] == '' ? ($staff['first_name'].$staff['last_name'] == '' ? $staffid : substr(decryptIt($staff['first_name']),0,1).substr(decryptIt($staff['last_name']),0,1)) : $staff['initials']);
													$colour = ($staff['calendar_color'] == '' ? '#6DCFF6' : $staff['calendar_color']);
													$checklist_checked = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_service_checklist` WHERE `ticketid` = '$ticketid' AND `deleted` = 0 AND `serviceid` = '{$service['serviceid']}' AND `index` = '$index' AND `contactid` = '$staff_id'")); ?>
													<td data-serviceid="<?= $serviceid ?>" data-staffid="<?= $staff_id ?>" data-title="<?= get_contact($dbc, $staff_id) ?>" <?= $strict_view > 0 ? 'class="readonly-block"' : '' ?>>
														<span class="id-circle right-aligned-mobile" style="background-color: <?= $colour ?>; font-family: 'Open Sans'; cursor: pointer; <?= (empty($checklist_checked) ? 'display:none;' : '') ?>" <?= !($strict_view > 0) ? 'onclick="checkChartField(this, 0, '.$index.');"' : '' ?> title="Click to Uncheck."><?= $initials ?></span>
														<input type="checkbox" name="field_checkbox" style="width: 20px; height: 20px; <?= (!empty($checklist_checked) ? 'display:none;' : '') ?>" value="1" onchange="checkChartField(this, 1, <?= $index ?>);" class="right-aligned-mobile" <?= $strict_view > 0 ? 'readonly disabled' : '' ?>>
														<div class="clearfix"></div>
													</td>
													<?php $pdf_content .= '<td>'.(!empty($checklist_checked) ? $initials : '').'</td>';
												}
												$history = '';
												$history = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `ticket_service_checklist_history` WHERE `ticketid` = '$ticketid' AND `serviceid` = '$serviceid' AND `index` = '$index' ORDER BY `id` ASC"))['history'];
												$history = rtrim($history, '<br>');
												echo '<td data-title="History" data-serviceid="'.$serviceid.'" class="service_history" '.(strpos($value_config, ','."Service Staff Checklist History".',') !== FALSE ? '' : 'style="display:none;"').'>'.$history.'</td>';
												$pdf_content .= '<td>'.$history.'</td>'; ?>
											</tr>
											<?php $pdf_content .= '</tr>';
										} ?>
									</table>
									<?php if((strpos($value_config, ',Service Staff Checklist Another Room,') !== FALSE || $access_services === TRUE) && !($strict_view > 0)) { ?>
										<div class="pull-right form-group">
											<?php if($access_services === TRUE) { ?>
												<a href="" data-category="<?= $category ?>" data-service-type="<?= $service_type ?>" onclick="removeRoomServiceChecklist(this); return false;" class="btn brand-btn">Remove Room</a>
											<?php } ?>
											<?php if(strpos($value_config, ',Service Staff Checklist Another Room,') !== FALSE) { ?>
												<a href="" onclick="addAnotherRoomServiceChecklist(this); return false;" data-copy-values="<?= strpos($value_config, ',Service Staff Checklist Another Room Copy Values,') !== FALSE ? 1 : 0 ?>" class="btn brand-btn">Add Another Room</a>
											<?php } ?>
										</div>
									<?php } ?>
								</div>
							</div>
						</div>
					</div>
					<?php $service_checklist_i++;
					$pdf_content .= '</table>';
					$pdf_contents[] = ['Staff Checklist',$pdf_content];
				} ?>
			</div>
		<?php } else {
			$services = $get_ticket['serviceid'];
			$staffs_list = array_filter(array_unique(explode(',',$get_ticket['contactid'])));
			$service_checklists = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `ticket_service_checklist` WHERE `ticketid` = '$ticketid' AND `deleted` = 0"),MYSQLI_ASSOC);
			$staff_checklist = [];
			foreach($service_checklists as $service_checklist) {
				$staff_checklist[$service_checklist['serviceid']][$service_checklist['contactid']] = 1;
			} ?>
			<table class="table table-bordered service_checklist_table">
			<?php $pdf_content = '<table cellpadding="1" border="1">';
			$pdf_content .= '<tr>';
			$pdf_content .= '<th>Service</th>'; ?>
				<tr>
					<th style="min-width: 15em;">Service</th>
					<?php foreach($staffs_list as $staff_id) {
						echo '<th style="min-width: 5em;">'.get_contact($dbc, $staff_id).'</th>';
						$pdf_content .= '<th>'.get_contact($dbc, $staff_id).'</th>';
					} ?>
				</tr>
				<?php $pdf_content .= '</tr>';
				foreach(explode(',', $services) as $serviceid) {
					if($serviceid > 0) {
						$service = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `services` WHERE `serviceid` = '$serviceid'"));
						$pdf_content .= '<tr>';
						$pdf_content .= '<td>'.$service['heading'].'</td>'; ?>
						<tr>
							<td data-title="Service"><?= $service['heading'] ?></td>
							<?php foreach($staffs_list as $staff_id) {
								$staff = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `contactid` = '$staff_id'"));
								$initials = ($staff['initials'] == '' ? ($staff['first_name'].$staff['last_name'] == '' ? $staffid : substr(decryptIt($staff['first_name']),0,1).substr(decryptIt($staff['last_name']),0,1)) : $staff['initials']);
								$colour = ($staff['calendar_color'] == '' ? '#6DCFF6' : $staff['calendar_color']); ?>
								<td data-serviceid="<?= $serviceid ?>" data-staffid="<?= $staff_id ?>" data-title="<?= get_contact($dbc, $staff_id) ?>">
									<span class="id-circle" style="background-color: <?= $colour ?>; font-family: 'Open Sans'; cursor: pointer; <?= (empty($staff_checklist[$serviceid][$staff_id]) ? 'display:none;' : '') ?>" onclick="checkChartField(this, 0);" title="Click to Uncheck."><?= $initials ?></span>
									<input type="checkbox" name="field_checkbox" style="width: 20px; height: 20px; <?= (!empty($staff_checklist[$serviceid][$staff_id]) ? 'display:none;' : '') ?>" value="1" onchange="checkChartField(this, 1);">
								</td>
								<?php $pdf_content .= '<td>'.(!empty($staff_checklist[$serviceid][$staff_id]) ? $initials : '').'</td>';
							} ?>
						</tr>
						<?php $pdf_content .= '</tr>';
					}
				} ?>
			</table>
			<?php $pdf_content .= '</table>';
			$pdf_contents[] = ['Staff Checklist',$pdf_content];
		}
		if(strpos($value_config, ',Service Staff Checklist Extra Billing,') !== FALSE && !($strict_view > 0)) { ?>
			<div class="double-gap-bottom">
				<a href="?" onclick="displayServiceExtraBilling(); return false;" data-history-label="Extra Billing Detail" class="btn brand-btn pull-right">Add Extra Billing</a>
				<div class="clearfix"></div>
				<div class="service_extra_billing_comment" style="display:none;">
					<label class="col-sm-8 control-label">Extra Billing Comment:</label>
					<div class="col-sm-12">
						<textarea name="service_extra_billing" class="form-control"></textarea>
						<a href="?" onclick="addServiceExtraBilling(this); return false;" data-history-label="Save Extra Billing Detail" class="btn brand-btn pull-right">Submit</a>
					</div>
				</div>
				<div class="clearfix"></div>
			</div>
		<?php }
	} else {
		echo '<h4>No Services Found.</h4>';
	}
} ?>