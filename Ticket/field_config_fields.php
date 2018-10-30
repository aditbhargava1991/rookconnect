<?php include_once('../include.php');
include_once('../Ticket/field_list.php');
if(!isset($ticket_tabs)) {
	$ticket_tabs = [];
	foreach(array_filter(explode(',',get_config($dbc, 'ticket_tabs'))) as $ticket_tab) {
		$ticket_tabs[config_safe_str($ticket_tab)] = $ticket_tab;
	}
}
$tab = filter_var($_GET['tile_name'], FILTER_SANITIZE_STRING);
$all_unlocked_tabs = explode(',',get_config($dbc, 'ticket_tab_locks'));
if(empty($_GET['tile_name']) && empty($_GET['type_name'])) {
	$all_config = [];
	$value_config = explode(',',get_field_config($dbc, 'tickets'));
	$value_mandatory_config = explode(',',get_mandatory_field_config($dbc, 'tickets'));
	$sort_order = explode(',',get_config($dbc, 'ticket_sortorder'));
	$unlocked_tabs = $all_unlocked_tabs;
	$all_unlocked_tabs = [];
	$ticket_log_template = get_config($dbc, 'ticket_log_template');
	$attached_charts = explode(',',get_config($dbc, 'ticket_attached_charts'));
	$auto_create_unscheduled = explode(',',get_config($dbc, 'ticket_auto_create_unscheduled'));
	$summary_hide_positions = explode('#*#',get_config($dbc, 'ticket_summary_hide_positions'));
} else if(!empty($_GET['type_name'])) {
	$tab = filter_var($_GET['type_name'], FILTER_SANITIZE_STRING);
	$all_config = explode(',',get_field_config($dbc, 'tickets'));
	$all_mandatory_config = explode(',',get_mandatory_field_config($dbc, 'tickets'));
	$value_config = explode(',',get_config($dbc, 'ticket_fields_'.$tab));
	$value_mandatory_config = explode(',',get_mandatory_config($dbc, 'ticket_fields_'.$tab));
	$sort_order = explode(',',get_config($dbc, 'ticket_sortorder_'.$tab));
	if(empty(get_config($dbc, 'ticket_sortorder_'.$tab))) {
		$sort_order = explode(',',get_config($dbc, 'ticket_sortorder'));
	}
	$unlocked_tabs = explode(',',get_config($dbc, 'ticket_tab_locks_'.$tab));
	$ticket_log_template = get_config($dbc, 'ticket_log_template_'.$tab);
	$attached_charts = explode(',',get_config($dbc, 'ticket_attached_charts_'.$tab));
	$auto_create_unscheduled = explode(',',get_config($dbc, 'ticket_auto_create_unscheduled_'.$tab));
	if(empty(get_config($dbc, 'ticket_auto_create_unscheduled_'.$tab))) {
		$auto_create_unscheduled = explode(',',get_config($dbc, 'ticket_auto_create_unscheduled'));
	}
	$all_summary_hide_positions = explode('#*#',get_config($dbc, 'ticket_summary_hide_positions'));
	$summary_hide_positions = explode('#*#',get_config($dbc, 'ticket_summary_hide_positions_'.$tab));
} else {
	$all_config = explode(',',get_field_config($dbc, 'tickets'));
	$all_mandatory_config = explode(',',get_mandatory_field_config($dbc, 'tickets'));
	$value_config = explode(',',get_config($dbc, 'ticket_fields_'.$tab));
	$value_mandatory_config = explode(',',get_mandatory_config($dbc, 'ticket_fields_'.$tab));
	$sort_order = explode(',',get_config($dbc, 'ticket_sortorder_'.$tab));
	if(empty(get_config($dbc, 'ticket_sortorder_'.$tab))) {
		$sort_order = explode(',',get_config($dbc, 'ticket_sortorder'));
	}
	$unlocked_tabs = explode(',',get_config($dbc, 'ticket_tab_locks_'.$tab));
	$ticket_log_template = get_config($dbc, 'ticket_log_template_'.$tab);
	$attached_charts = explode(',',get_config($dbc, 'ticket_attached_charts_'.$tab));
	$auto_create_unscheduled = explode(',',get_config($dbc, 'ticket_auto_create_unscheduled_'.$tab));
	if(empty(get_config($dbc, 'ticket_auto_create_unscheduled_'.$tab))) {
		$auto_create_unscheduled = explode(',',get_config($dbc, 'ticket_auto_create_unscheduled'));
	}
	$all_summary_hide_positions = explode('#*#',get_config($dbc, 'ticket_summary_hide_positions'));
	$summary_hide_positions = explode('#*#',get_config($dbc, 'ticket_summary_hide_positions_'.$tab));
}
foreach ($accordion_list as $accordion_field => $accordion_field_fields) {
	if(!in_array($accordion_field, $sort_order)) {
		$sort_order[] = $accordion_field;
	}
}
?>
<script>
$(document).ready(function() {
	$('input,select,textarea').change(saveFields);
	$('.main-screen').sortable({
		handle: '.drag-handle',
		items: '.note-option',
		update: saveFields
	});
	$('.main-screen').sortable({
		handle: '.drag-handle',
		items: '.notify_item',
		update: saveFields
	});
	$('.main-screen').sortable({
		handle: '.drag-handle',
		items: '.ind_type',
		update: saveFields
	});
	$('.main-screen').sortable({
		handle: '.drag-handle',
		items: '.cancel_reason',
		update: saveFields
	});
	$('.main-screen').sortable({
		handle: '.drag-handle',
		items: '.checkout_info',
		update: saveFields
	});

	filterIndCategories();

	reloadSortableAccordions();
	$('.fields_sortable').sortable({
		items: '.sort_order_field',
		beforeStop: function(e, block) {
			var div = $(block.item).closest('.sort_order_accordion');
			sortFields(div);
		}
	});
	$('.fields_sortable_custom').sortable({
		items: '.sort_order_field',
		beforeStop: function(e, block) {
			var div = $(block.item).closest('.sort_order_accordion');
			sortFieldsCustom(div);
		}
	});

	$('.dataToggle:not(.disabled)').click(function() {
		$(this).find('input').data('toggle',$(this).find('input').data('toggle') == 1 ? 0 : 1);
		$(this).find('img').toggle();
		saveFields();
	});
	$('select[name="attached_chart_tab[]"],select[name="attached_chart_subtab[]"],select[name="attached_chart_heading[]"],select[name="attached_chart[]"]').change(function() { filterAttachedCharts(this); });
});
$(document).on('change', '.delivery_restriction_div input,.delivery_restriction_div select', saveDeliveryRestrictions);
function reloadSortableAccordions() {
	$('.accordions_sortable,.sort_order_heading_block').sortable({
		connectWith: '.sort_order_heading_block,.accordions_sortable',
		items: '.sort_order_accordion',
		handle: 'label.control-label',
		start: function(e, block) {
			$(block.item).find('.col-sm-8').hide();
		    block.placeholder.height(10);
			$(this).sortable('refreshPositions');
		},
		beforeStop: function(e, block) {
			if($(block.item).parent().hasClass('sort_order_heading_block') && $(block.item).hasClass('sort_order_heading')) {
				$('.accordions_sortable,.sort_order_heading_block').sortable('cancel');
			}
			$(block.item).find('.col-sm-8').show();
			sortAccordions();
		}
	});
}
function saveFields() {
	var mandatory = "<?php echo $mandatory; ?>";
	var mandate = 0;
	if(mandatory == 1) {
		mandate = 1;
	}

	var this_field_name = this.name;
	var ticket_fields = [];
	$('[name="tickets[]"]:checked').not(':disabled').each(function() {
		ticket_fields.push(this.value);
	});
	var task_data = $('.task_data').val();
	var task_data_name = $('.task_data').attr('name');
	var multiple_labels = $('[name=ticket_multiple_labels]').val();
	var extra_billing_email = $('[name=ticket_extra_billing_email]').val();
	var note_heading = $('[name=custom_notes_heading]').val();
	var custom_notes = [];
	$('[name=note_types]').each(function() {
		custom_notes.push(this.value);
	});
	var individuals = [];
	$('[name=individual_categories]:visible').each(function() {
		individuals.push($(this).closest('.ind_type').find('[name=tile_src]').val()+'|'+this.value);
	});
	var cancel_reasons = [];
	$('[name=cancel_reasons]').each(function() {
		cancel_reasons.push(this.value);
	});
	var checkout_info = [];
	$('[name=checkout_info]').each(function() {
		checkout_info.push(this.value);
	});
	var checkout_info_staff = [];
	$('[name=checkout_info_staff]').each(function() {
		checkout_info_staff.push(this.value);
	});
	var attached_charts = [];
	$('[name="attached_chart[]"]').each(function() {
		attached_charts.push(this.value);
	});
	var auto_create_unscheduled = [];
	$('[name="auto_create_unscheduled[]"]').each(function() {
		auto_create_unscheduled.push(this.value);
	});
	var ticket_tab_locks = [];
	$('[name^=ticket_tab_locks]').filter(function() { return $(this).data('toggle') == 1; }).each(function() {
		ticket_tab_locks.push(this.value);
	});
	var ticket_custom_field_values = [];
	$('.multi-block.custom_options').each(function() {
		var options = $(this).find('input').first().val()+'|*|'+$(this).find('input').last().val();
		if(options != '|*|') {
			ticket_custom_field_values.push(options);
		}
	})
	var ticket_delivery_colors = [];
	$('[name="delivery_color[]"]').each(function() {
		var delivery = $(this).data('delivery');
		var color = $(this).val();
		ticket_delivery_colors.push(delivery+'*#*'+color);
	});
	var ticket_notify_list_items = [];
	$('[name="ticket_notify_list_items"]').each(function() {
		var delivery = $(this).data('delivery');
		var color = $(this).val();
		ticket_notify_list_items.push($(this).val());
	});
	var ticket_summary_hide_positions = [];
	$('[name="ticket_hide_summary[]"]').each(function() {
		ticket_summary_hide_positions.push($(this).val());
	});
	var delivery_restrict_contacts = [];
	$('[name^="delivery_restrict_contacts"]:checked').each(function() {
		delivery_restrict_contacts.push(this.value);
	});
	var ticket_notes_limit = $('[name="ticket_notes_limit"]').val();
	var ticket_recurring_status = $('[name="ticket_recurring_status"]').val();
	var ticket_material_increment = $('[name="ticket_material_increment"]').val();
	var ticket_notes_alert_role = $('[name="ticket_notes_alert_role"]').val();
	var ticket_recurrence_sync_upto = $('[name="ticket_recurrence_sync_upto"]').val();
	$.post('ticket_ajax_all.php?action=ticket_fields', {
		fields: ticket_fields,
		field_name: '<?= empty($tab) ? 'tickets' : 'ticket_fields_'.$tab ?>',
		tab: '<?= $tab ?>',
		tasks: task_data,
		tasks_name: task_data_name,
		labels: multiple_labels,
		billing: extra_billing_email,
		cancel_reasons: cancel_reasons,
		checkout_info: checkout_info,
		checkout_info_staff: checkout_info_staff,
		note_heading: note_heading,
		note_types: custom_notes,
		individuals: individuals,
		transport_types: $('[name=transport_types]').val(),
		piece_types: $('[name=piece_types]').val(),
		delivery_types: $('[name=delivery_types]').val(),
		delivery_timeframe_default: $('[name=delivery_timeframe_default]').val(),
		ticket_warehouse_start_time: $('[name=ticket_warehouse_start_time]').val(),
		ticket_custom_field: $('[name^=ticket_custom_field]').attr('name'),
		ticket_custom_field_value: $('[name^=ticket_custom_field]').val(),
		ticket_custom_field_values: $('[name^=ticket_custom_field_values]').attr('name'),
		ticket_custom_field_values_value: ticket_custom_field_values.join('#*#'),
		tab_transport_log_contact: $('[name^=transport_log_contact]').attr('name'),
		tab_transport_log_contact_value: $('[name^=transport_log_contact]').val(),
		transport_destination_contact: $('[name^=transport_destination_contact]').attr('name'),
		transport_destination_contact_value: $('[name^=transport_destination_contact]').val(),
		transport_carrier_category: $('[name^=transport_carrier_category]').attr('name'),
		transport_carrier_category_value: $('[name^=transport_carrier_category]').val(),
		ticket_project_contact: $('[name^=ticket_project_contact]').attr('name'),
		ticket_project_contact_value: $('[name^=ticket_project_contact]').val(),
		ticket_business_contact: $('[name^=ticket_business_contact]').attr('name'),
		ticket_business_contact_value: $('[name^=ticket_business_contact]').val(),
		client_accordion_category: $('[name^=client_accordion_category]').attr('name'),
		client_accordion_category_value: $('[name^=client_accordion_category]').val(),
		incomplete_ticket_status: $('[name^=incomplete_ticket_status]').attr('name'),
		incomplete_ticket_status_value: $('[name^=incomplete_ticket_status]').val(),
		delivery_type_contacts: $('[name^=delivery_type_contacts]').attr('name'),
		delivery_type_contacts_value: $('[name^=delivery_type_contacts]').val(),
		rate_card_contact: $('[name^=rate_card_contact]').attr('name'),
		rate_card_contact_value: $('[name^=rate_card_contact]').val(),
		ticket_chemical_label: $('[name^=ticket_chemical_label]').attr('name'),
		ticket_chemical_label_value: $('[name^=ticket_chemical_label]').val(),
		ticket_tab_locks: $('[name^=ticket_tab_locks]').attr('name'),
		ticket_tab_locks_value: ticket_tab_locks,
		auto_archive_complete_tickets: $('[name^=auto_archive_complete_tickets]').val(),
		delivery_km_service: $('[name^=delivery_km_service]').val(),
		attached_charts: attached_charts,
		incomplete_inventory_reminder_email: $('[name=incomplete_inventory_reminder_email]').val(),
		ticket_notify_list: $('[name=ticket_notify_list]').val(),
		ticket_notify_pdf_content: $('[name=ticket_notify_pdf_content]').val(),
		ticket_notify_cc: $('[name=ticket_notify_cc]').val(),
		ticket_notify_list_items: ticket_notify_list_items,
		auto_create_unscheduled: auto_create_unscheduled,
		ticket_delivery_colors: ticket_delivery_colors,
		ticket_notes_limit: ticket_notes_limit,
		ticket_summary_hide_positions: ticket_summary_hide_positions,
		ticket_delivery_time_mintime: $('[name="ticket_delivery_time_mintime"]').val(),
		ticket_delivery_time_maxtime: $('[name="ticket_delivery_time_maxtime"]').val(),
		delivery_default_pickup_type: $('[name="delivery_default_pickup_type"]').val(),
		delivery_default_pickup_time: $('[name="delivery_default_pickup_time"]').val(),
		ticket_recurring_status: ticket_recurring_status,
		ticket_material_increment: ticket_material_increment,
		ticket_notes_alert_role: ticket_notes_alert_role,
		ticket_business_contact_add_pos: $('[name="ticket_business_contact_add_pos"]').val(),
		ticket_staff_travel_default: $('[name="ticket_staff_travel_default"]').val(),
		ticket_email_approval: $('[name="ticket_email_approval"]').val(),
		ticket_approval_status: $('[name="ticket_approval_status"]').val(),
		ticket_guardian_contact: $('[name^=ticket_guardian_contact]').attr('name'),
		ticket_guardian_contact_value: $('[name^=ticket_guardian_contact]').val(),
		ticket_default_status: $('[name^=ticket_default_status]').attr('name'),
		ticket_default_status_value: $('[name^=ticket_default_status]').val(),
		ticket_new_email_subject: $('[name^=ticket_new_email_subject]').attr('name'),
		ticket_new_email_subject_value: $('[name^=ticket_new_email_subject]').val(),
		ticket_new_email_body: $('[name^=ticket_new_email_body]').attr('name'),
		ticket_new_email_body_value: $('[name^=ticket_new_email_body]').val(),
		delivery_default_tabs: $('[name^=delivery_default_tabs]').attr('name'),
		delivery_default_tabs_value: $('[name^=delivery_default_tabs]').val(),
		ticket_invoice_type: $('[name^=ticket_invoice_type]').attr('name'),
		ticket_invoice_type_value: $('[name^=ticket_invoice_type]').val(),
		ticket_recurrence_sync_upto: ticket_recurrence_sync_upto,
		mandatory: mandate,
		delivery_type_default: $('[name="delivery_type_default"]').val(),
		delivery_restrict_contacts: delivery_restrict_contacts
	}).success(function(response) {
		if(this_field_name == 'delivery_types') {
			reloadDeliveryColors();
		}
	});
	sortAccordions();
}
function reloadDeliveryColors() {
	$.ajax({
		url: '../Ticket/field_config_field_list_delivery_colors.php',
		success: function(response) {
			$('.delivery_type_colors').html(response);
		}
	});
}
function saveHigherLevelHeadings() {
	$('.sort_order_heading').each(function() {
		var heading_name = $(this).find('[name="sort_order_heading[]"]').val();
		var heading_accordions = [];
		$(this).find('.sort_order_accordion').each(function() {
			heading_accordions.push($(this).data('accordion'));
		});
		if(heading_accordions.length > 0) {
			$(this).find('.sort_order_heading_note').remove();
			$.ajax({
				url: '../Ticket/ticket_ajax_all.php?action=ticket_higher_level_headings',
				method: 'POST',
				data: {
					field_name: '<?= empty($tab) ? 'tickets' : 'tickets_'.$tab ?>',
					heading_name: heading_name,
					heading_accordions: heading_accordions
				},
				success: function(response) {

				}
			});
		}
	});
}
function addNoteType() {
	var clone = $('.note-option').last().clone();
	clone.find('input').val('');
	$('.note-option').last().after(clone);

	$('input').off('change',saveFields).change(saveFields);
	$('[name="note_types"]').last().focus();
}
function removeNoteType(a) {
	if($('.note-option').length <= 1) {
		addNoteType();
	}
	$(a).closest('.note-option').remove();
	saveFields();
}
function addNotifyListItem() {
	var clone = $('.notify_item').last().clone();
	clone.find('input').val('');
	$('.notify_item').last().after(clone);

	$('input').off('change',saveFields).change(saveFields);
	$('[name="ticket_notify_list_items"]').last().focus();
}
function removeNotifyListItem(a) {
	if($('.notify_item').length <= 1) {
		addNoteType();
	}
	$(a).closest('.notify_item').remove();
	saveFields();
}
function filterIndCategories() {
	$('.ind_type').each(function() {
		var tile = $(this).find('[name=tile_src]').val();
		if(tile == 'custom') {
			$(this).find('.cust_div').show();
			$(this).find('.cat_div').hide();
		} else {
			$(this).find('.cust_div').hide();
			$(this).find('.cat_div').show();
			$(this).find('[name=individual_categories] option').each(function() {
				if($(this).data('tile') == tile) {
					$(this).show();
				} else {
					$(this).hide();
				}
			});
			$(this).find('[name=individual_categories]').trigger('change.select2');
		}
	});
}
function addIndividual() {
	destroyInputs($('.ind_type'));
	var clone = $('.ind_type').last().clone();
	clone.find('select').val('').trigger('change.select2');
	$('.ind_type').last().after(clone);
	initInputs('.ind_type');

	$('input').off('change',saveFields).change(saveFields);
}
function remIndividual(a) {
	if($('.ind_type').length <= 1) {
		addNoteType();
	}
	$(a).closest('.ind_type').remove();
	saveFields();
}
function addReason() {
	var clone = $('.cancel_reason').last().clone();
	clone.find('input').val('');
	$('.cancel_reason').last().after(clone);
	$('[name=cancel_reasons]').last().focus();

	$('input').off('change',saveFields).change(saveFields);
}
function removeReason(a) {
	if($('.cancel_reason').length <= 1) {
		addReason();
	}
	$(a).closest('.cancel_reason').remove();
	saveFields();
}
function addInfo() {
	var clone = $('.checkout_info').last().clone();
	clone.find('input').val('');
	$('.checkout_info').last().after(clone);
	$('[name=checkout_info]').last().focus();

	$('input').off('change',saveFields).change(saveFields);
}
function removeInfo(a) {
	if($('.checkout_info').length <= 1) {
		addInfo();
	}
	$(a).closest('.checkout_info').remove();
	saveFields();
}
function addInfoStaff() {
	var clone = $('.checkout_info_staff').last().clone();
	clone.find('input').val('');
	$('.checkout_info_staff').last().after(clone);
	$('[name=checkout_info_staff]').last().focus();

	$('input').off('change',saveFields).change(saveFields);
}
function removeInfoStaff(a) {
	if($('.checkout_info_staff').length <= 1) {
		addInfoStaff();
	}
	$(a).closest('.checkout_info_staff').remove();
	saveFields();
}
function updateIncidentReportEmail(input) {
	var email = input.value;
	$.ajax({
		url: 'ticket_ajax_all.php?action=update_inc_rep_email',
		method: 'POST',
		data: { email: email },
		success: function(response) {

		}
	});
}
function filterAttachedCharts(sel) {
	var block = $(sel).closest('.attached_chart_block');
	var tab = $(block).find('[name="attached_chart_tab[]"]');
	var subtab = $(block).find('[name="attached_chart_subtab[]"]');
	var heading = $(block).find('[name="attached_chart_heading[]"]');
	var chart = $(block).find('[name="attached_chart[]"]');
	if(sel.name == 'attached_chart_tab[]' || sel.name == 'attached_chart_subtab[]') {
		var filter = '';
		if($(tab).val() != '' && $(tab).val() != undefined) {
			filter += '[data-tab="'+$(tab).val()+'"]';
		}
		if($(subtab).val() != '' && $(subtab).val() != undefined) {
			filter += '[data-subtab="'+$(subtab).val()+'"]';
		}
		$(heading).find('option').hide();
		$(heading).find('option'+filter).show();
		$(heading).trigger('change.select2');
		if($(heading).val() != '' && $(heading).val() != undefined) {
			filter += '[data-heading="'+$(heading).val()+'"]';
		}
		$(chart).find('option').hide();
		$(chart).find('option'+filter).show();
		$(chart).trigger('change.select2');
	} else if(sel.name == 'attached_chart_heading[]') {
		var heading_tab = $(heading).find('option:selected').data('tab');
		var heading_subtab = $(heading).find('option:selected').data('subtab');
		$(tab).val(heading_tab);
		$(tab).trigger('change.select2');
		$(subtab).val(heading_subtab);
		$(subtab).trigger('change.select2');
		var filter = '[data-tab="'+heading_tab+'"][data-subtab="'+heading_subtab+'"][data-heading="'+$(heading).val()+'"]';
		$(chart).find('option').hide();
		$(chart).find('option'+filter).show();
		$(chart).trigger('change.select2');
	} else {
		var chart_tab = $(chart).find('option:selected').data('tab');
		var chart_subtab = $(chart).find('option:selected').data('subtab');
		var chart_heading = $(chart).find('option:selected').data('heading');
		$(tab).val(chart_tab);
		$(tab).trigger('change.select2');
		$(subtab).val(chart_subtab);
		$(subtab).trigger('change.select2');
		$(heading).val(chart_heading);
		$(heading).trigger('change.select2');
	}
}
function addAttachedChart() {
	destroyInputs('.attached_chart_block');
	var block = $('.attached_chart_block').last();
	var clone = block.clone();

	clone.find('select').val('');
	clone.find('select').trigger('change.select2');

	block.after(clone);
	initInputs('.attached_chart_block');

	$('input,select').change(saveFields);
	$('select[name="attached_chart_tab[]"],select[name="attached_chart_subtab[]"],select[name="attached_chart_heading[]"],select[name="attached_chart[]"]').change(function() { filterAttachedCharts(this); });
}
function removeAttachedChart(img) {
	if($('.attached_chart_block').length <= 1) {
		addAttachedChart();
	}

	$(img).closest('.attached_chart_block').remove();
	saveFields();
}
function addCustomAccordion() {
	$.ajax({
		url: '../Ticket/field_config_field_list_custom.php?ticket_type=<?= $tab ?>',
		method: 'GET',
		dataType: 'html',
		success: function(response) {
			$('.sort_order_accordion').last().after(response);
			$('input,select').change(saveFields);
		}
	});
}
function removeCustomAccordion(img) {
	if(confirm('Are you sure you want to remove this accordion?')) {
		$.ajax({
			url: '../Ticket/ticket_ajax_all.php?action=remove_custom_accordion',
			method: 'POST',
			data: {
				field_name: '<?= empty($tab) ? 'tickets' : 'tickets_'.$tab ?>',
				name: $(img).closest('.sort_order_accordion').data('accordion')
			},
			success: function(response) {
				$(img).closest('.sort_order_accordion').remove();
				saveFields();
			}
		});
	}
}
function updateCustomAccordion(input) {
	var old_name = $(input).closest('.sort_order_accordion').data('accordion');
	$(input).closest('.sort_order_accordion').data('accordion', "FFMCUST_"+$(input).val());
	$(input).closest('.sort_order_accordion').find('input[name="tickets[]"]').val("FFMCUST_"+$(input).val());
	var new_name = $(input).closest('.sort_order_accordion').data('accordion');
	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=update_custom_accordion_name',
		method: 'POST',
		data: {
			field_name: '<?= empty($tab) ? 'tickets' : 'tickets_'.$tab ?>',
			old_name: old_name,
			new_name: new_name
		},
		success: function(response) {

		}
	});
	saveFields();
}
function addHigherLevelHeading() {
	$.ajax({
		url: '../Ticket/field_config_field_list_heading.php',
		method: 'GET',
		dataType: 'html',
		success: function(response) {
			$('.sort_order_accordion').last().after(response);
			$('input,select').change(saveFields);
			reloadSortableAccordions();
		}
	});
}
function updateHigherLevelHeading(input) {
	var old_name = $(input).data('oldvalue');
	var new_name = $(input).val();
	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=update_higher_level_heading',
		method: 'POST',
		data: {
			field_name: '<?= empty($tab) ? 'tickets' : 'tickets_'.$tab ?>',
			old_name: old_name,
			new_name: new_name
		},
		success: function(response) {

		}
	});
}
function removeHigherLevelHeading(img) {
	if(confirm('Are you sure you want to remove this heading?')) {
		$.ajax({
			url: '../Ticket/ticket_ajax_all.php?action=remove_higher_level_heading',
			method: 'POST',
			data: {
				field_name: '<?= empty($tab) ? 'tickets' : 'tickets_'.$tab ?>',
				name: $(img).closest('.sort_order_heading').find('[name="sort_order_heading[]"]').val()
			},
			success: function(response) {
				$(img).closest('.sort_order_heading').find('.sort_order_heading_block').contents().unwrap();
				$(img).closest('.sort_order_heading').contents().unwrap();
				$(img).closest('.sort_order_heading_name').remove();
			}
		});
	}
}
function sortAccordions() {
	var blocks = [];
	$('.sort_order_accordion').each(function() {
		blocks.push($(this).data('accordion'));
	});
	blocks = JSON.stringify(blocks);

	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=ticket_sort_order',
		method: 'POST',
		data: {
			field_name: '<?= empty($tab) ? 'ticket_sortorder' : 'ticket_sortorder_'.$tab ?>',
			blocks: blocks
		},
		success: function(response) {

		}
	});
	saveHigherLevelHeadings();
}
function sortFields(div) {
	var blocks = [];
	$(div).find('.sort_order_field input[type="checkbox"]').each(function() {
		blocks.push($(this).val());
	});
	blocks = JSON.stringify(blocks);

	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=ticket_fields_sort_order',
		method: 'POST',
		data: {
			field_name: '<?= empty($tab) ? 'tickets' : 'tickets_'.$tab ?>',
			accordion: $(div).data('accordion'),
			blocks: blocks
		},
		success: function(response) {

		}
	});
}
function sortFieldsCustom(div) {
	var blocks = [];
	$(div).find('.sort_order_field input[type="checkbox"]:checked').each(function() {
		blocks.push($(this).val());
	});
	blocks = JSON.stringify(blocks);

	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=ticket_fields_sort_order',
		method: 'POST',
		data: {
			field_name: '<?= empty($tab) ? 'tickets' : 'tickets_'.$tab ?>',
			accordion: $(div).data('accordion'),
			blocks: blocks
		},
		success: function(response) {

		}
	});
}
function editAccordion(url) {
	var accordion = $(url).closest('.accordion_label').hide().nextAll('.accordion_rename').first().show().find('input').focus();
	accordion.find('.accordion_label').hide();
	accordion.find('.accordion_rename').show().find('input').focus();
}
function updateAccordion(input) {
	var accordion = $(input).closest('[data-accordion]');
	var accordion_name = $(input).val();
	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=update_ticket_accordion_name',
		method: 'POST',
		data: {
			field_name: '<?= empty($tab) ? 'tickets' : 'tickets_'.$tab ?>',
			accordion: $(accordion).data('accordion'),
			accordion_name: accordion_name
		},
		success: function(response) {
			accordion.find('.accordion_label_text').text(response);
			$(input).val(response);
			accordion.find('.accordion_label').show();
			accordion.find('.accordion_rename').hide();
		}
	});

}
function addHidePosition() {
	destroyInputs('.position_block');
	var block = $('.position_block').last();
	var clone = $(block).clone();

	clone.find('select').val('').trigger('change.select2');
	$(block).after(clone);
	initInputs('.position_block');
	$('input,select,textarea').change(saveFields);
}
function removeHidePosition(sel) {
	if($('.position_block').length <= 1) {
		addHidePosition();
	}
	$(sel).closest('.position_block').remove();
	saveFields();
}
function loadDefaultDeliveryDropdown() {
	destroyInputs('[name="delivery_type_default"]');
	var default_type = $('[name="delivery_type_default"]').val();
	var delivery_types = $('[name="delivery_types"]').val().split(',');
	var default_html = '';
	delivery_types.forEach(function(delivery_type) {
		default_html += '<option value="'+delivery_type+'" '+(delivery_type == default_type ? 'selected' : '')+'>'+delivery_type+'</option>';
	});
	$('[name="delivery_type_default"]').html(default_html);
	initInputs('[name="delivery_type_default"]');
	$('[name="delivery_type_default"]').change();
}
function getRestrictContactsList(select) {
	var category = $(select).val();
	if(category == undefined || category == '') {
		category = $(select).data('default');
	}

	if(category == undefined || category == '') {
		$('.delivery_restrict_contacts').hide();
	} else {
		$.ajax({
			url: '../Ticket/ticket_ajax_all.php?action=get_restrict_contacts_list',
			method: 'POST',
			data: {
				tab: '<?= $tab ?>',
				category: category
			},
			success: function(response) {
				$('.delivery_restrict_contacts_div').html(response);
				$('.delivery_restrict_contacts').find('.control-label').text('Restrict '+category);
				$('input,select,textarea').change(saveFields);
			}
		});
	}
}
function saveDeliveryRestrictions() {
	var tab = '<?= $tab ?>';
	var security_level = [];
	$('[name="delivery_restriction_security_level"]').each(function() {
		security_level.push(this.value);
	});
	security_level = security_level.join(',');
	var to_do_date_min = $('[name="delivery_restriction_to_do_date_min"]').val();
	var to_do_date_max = $('[name="delivery_restriction_to_do_date_max"]').val();
	var to_do_start_time_min = $('[name="delivery_restriction_to_do_start_time_min"]').val();
	var to_do_start_time_max = $('[name="delivery_restriction_to_do_start_time_max"]').val();
	$.ajax({
		url: '../Ticket/ticket_ajax_all.php?action=save_delivery_restriction',
		method: 'POST',
		data: { tab: tab, security_level: security_level, to_do_date_min: to_do_date_min, to_do_date_max: to_do_date_max, to_do_start_time_min: to_do_start_time_min, to_do_start_time_max: to_do_start_time_max },
		success: function(response) {

		}
	});
}
function addDeliveryRestriction() {
	destroyInputs('.delivery_restriction_role');
	var block = $('.delivery_restriction_role').last();
	var clone = $(block).clone();
	$(clone).find('select').val('');
	$(block).after(clone);
	initInputs('.delivery_restriction_role');
}
function removeDeliveryRestriction(select) {
	if($('.delivery_restriction_role').length <= 1) {
		addDeliveryRestriction();
	}

	$(select).closest('.delivery_restriction_role').remove();
	$('.delivery_restriction_role').first().change();
}
</script>
<!-- <h1><?= (!empty($tab) ? $ticket_tabs[$tab].' Fields' : 'All '.TICKET_NOUN.' Fields') ?></h1> -->

<?php if($mandatory == 1): ?>
	<?php include('field_config_mandatory_field_list.php'); ?>
<?php else: ?>
	<?php if(empty($_GET['tile_name'])) {
		echo '<a href="?settings=fields" class="btn brand-btn '.(empty($tab) ? 'active_tab' : '').'">All '.TICKET_TILE.'</a>';
		foreach($ticket_tabs as $tab_id => $tab_label) {
			echo '<a href="?settings=fields&type_name='.$tab_id.'" class="btn brand-btn '.($tab_id == $tab ? 'active_tab' : '').'">'.$tab_label.'</a>';
		}
	} ?>
	<?php include('field_config_field_list.php'); ?>
<?php endif; ?>
