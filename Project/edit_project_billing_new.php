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

if($security['edit'] > 0) {
	if(!isset($projectid)) {
		$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
		foreach(explode(',',get_config($dbc, "project_tabs")) as $type_name) {
			if($tile == 'project' || $tile == config_safe_str($type_name)) {
				$project_tabs[config_safe_str($type_name)] = $type_name;
			}
		}
	}
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid' AND `projectid` > 0"));
	$project_security = get_security($dbc, 'project');
	include('../Rate Card/line_types.php');
	$heading_order = explode('#*#', get_config($dbc, 'estimate_field_order'));
	$headings = [];
	$query = mysqli_query($dbc, "SELECT `heading` FROM `project_scope` WHERE `projectid`='$projectid' AND `projectid` > 0 AND `deleted`=0 GROUP BY `heading` ORDER BY MIN(`sort_order`)");
	if(mysqli_num_rows($query) > 0) {
		while($row = mysqli_fetch_array($query)) {
			$headings[preg_replace('/[^a-z]*/','',strtolower($row[0]))] = $row[0];
		}
	} else {
		$headings['scope'] = 'Scope';
	}
	$headings['checklists'] = 'Checklists';
	$headings['tasklist'] = TASK_TILE;
	$headings['tickets'] = TICKET_TILE;
	$headings['workorders'] = 'Work Orders';
	$headings['staff_task'] = 'Staff Tasks';
	$headings['time_cards'] = 'Time Sheets'; ?>
	<script>
	$(document).ready(function() {
		$('[name=src_id]').each(function() { fill_selects($(this).closest('tr')); });
		$('[name="qty"],[name="margin"],[name="profit"]').on('keyup', function() { saveBillableLine(this); });
	});
	$(document).on('change', 'select[name="rate_card"]', function() { updateRateCard(this); });
	function updateRateCard(sel) {
		var cost = sel.value;
		$(sel).closest('tr').find('[name="cost"]').val(cost);
		saveBillableLine(sel);
	}
	function saveBillableLine(input) {
		var tr = $(input).closest('tr');
		var cost = parseFloat($(tr).find('[name="cost"]').val()).toFixed(2);
		var qty = parseFloat($(tr).find('[name="qty"]').val()).toFixed(2);
		var margin = parseFloat($(tr).find('[name="margin"]').val()).toFixed(2);
		var profit = parseFloat($(tr).find('[name="profit"]').val()).toFixed(2);
		if(input.name == 'profit') {
			margin = (parseFloat(profit) / parseFloat(cost) * 100).toFixed(2);
			$(tr).find('[name="margin"]').val(margin);
		} else {
			profit = (parseFloat(cost) * parseFloat(margin) / 100).toFixed(2);
			$(tr).find('[name="profit"]').val(profit);
		}
		var price = (parseFloat(cost) + parseFloat(profit)).toFixed(2);
		$(tr).find('[name="price"]').val(price);
		var total = (parseFloat(price) * parseFloat(qty)).toFixed(2);
		$(tr).find('[name="retail"]').val(total);
		$(tr).find('input').trigger('change');
	}
	function remove_line(img) {
		row = $(img).closest('tr');
		$.ajax({
			url: 'projects_ajax.php?action=project_fields',
			method: 'POST',
			data: {
				table: $(img).data('table'),
				id: $(img).data('id'),
				id_field: $(img).data('id-field'),
				field: img.name,
				value: 0
			}
		});
		row.remove();
	}
	var tile_items = [];
	var categories = [];
	var item_types = [];
	<?php $categories = [];
	$item_types = [];
	foreach($src_options as $option) {
		if(!isset($categories[$option['tile_name']])) {
			echo "item_types['".$option['tile_name']."'] = [];\n";
			echo "categories['".$option['tile_name']."'] = [];\n";
			echo "tile_items['".$option['tile_name']."'] = [];\n";
			$categories[$option['tile_name']] = [];
		}
		if($option['category'] != '') {
			if(!in_array($option['category'],$categories[$option['tile_name']])) {
				echo "categories['".$option['tile_name']."'].push('".$option['category']."');\n";
				$categories[$option['tile_name']][] = $option['category'];
			}
		}
		if($option['type'] != '') {
			if(!in_array($option['type'],$item_types[$option['tile_name']])) {
				echo "item_types['".$option['tile_name']."'].push(['".$option['type']."','".$option['category']."']);\n";
				$item_types[$option['tile_name']][] = $option['type'];
			}
		}
		$option_data = json_encode($option);
		if($option_data != '') {
			echo "tile_items['".$option['tile_name']."'][".$option['id']."] = ".$option_data.";\n";
		}
	} ?>
	function fill_selects(row) {
		if(row != undefined) {
			var tile = row.find('[name=src_table]').val();
			var select = $(row).find('.select_div select').empty();
			var cat_select = $(row).find('[name=category]');
			var cat_item = cat_select.val();
			cat_select.empty();
			var type_select = $(row).find('[name=item_type]');
			var type_item = type_select.val();
			type_select.empty();
			var cat_list = categories[tile];
			var type_list = item_types[tile];
			var tile_list = tile_items[tile];
			item = { id: 0, category: '', type: '' };
			if(select.val() > 0) {
				var item = tile_list[select.val()];
			} else if(select.data('line-id') > 0 && tile_list != undefined  && tile_list[select.data('line-id')] != undefined) {
				var item = tile_list[select.data('line-id')];
			}
			cat_select.append($('<option />'));
			if(cat_list == undefined) {
				cat_select.append($('<option />', { value: '', text: 'N/A' }));
			} else {
				cat_list.forEach(function (element) {
					cat_select.append($('<option />', { value: $('<div>').html(element).text(), text: $('<div>').html(element).text(), selected: element == item.category || element == cat_item }));
				});
			}
			var cat_item = cat_select.val();
			type_select.append($('<option />'));
			if(type_list == undefined) {
				type_select.append($('<option />', { value: '', text: 'N/A' }));
			} else {
				type_list.forEach(function (element) {
					if(cat_item == '' || cat_item == undefined || cat_item == htmlDecode(element[1]) || element[0] == type_item) {
						type_select.append($('<option />', { value: $('<div>').html(element[0]).text(), text: $('<div>').html(element[0]).text(), selected: element[0] == item.type || element[0] == type_item }));
					}
				});
			}
			var type_item = type_select.val();
			select.append($('<option />'));
			if(tile_list == undefined) {
				select.append($('<option />', { value: '', text: 'N/A' }));
			} else {
				tile_list.forEach(function (element) {
					if((cat_item == '' || cat_item == undefined || cat_item == htmlDecode(element.category) || element.id == item.id) && (type_item == '' || type_item == undefined || type_item == htmlDecode(element.type) || element.id == item.id)) {
						select.append($('<option />', { value: element.id, text: $('<div>').html(element.label).text(), selected: element.id == item.id }));
					}
				});
			}
			type_select.trigger('change.select2');
			cat_select.trigger('change.select2');
			select.trigger('change.select2');
		}
	}
	function create_bill() {
		var lines = [];
		$('tr:not(.strikethrough)').find('[name=attach_to_new]:checked').each(function() {
			lines.push($(this).data('id'));
		});
		if(lines.length > 0) {
			overlayIFrame('edit_project_bill.php?projectid=<?= $projectid ?>&lines='+JSON.stringify(lines));
		} else {
			alert('You must select one or more lines to create an assignment from the scope.');
		}
	}
	function edit_details(line) {
		line = $(line).closest('tr');
		$.post('../Project/projects_ajax.php?action=billable_edit', {
			id: line.find('[name=billable_id]').val(),
			table: line.find('[name=billable_table]').val(),
			billable: line.find('[name=heading]').data('id')
		}, function() { window.location.reload(); });
	}
	</script>
	<h3>&nbsp;<!-- New Billing --><button onclick="create_bill(); return false;" class="btn brand-btn pull-right">Create</button></h3>
	<div class="form-group"><a href="" class="active" onclick="add_heading(); return false;"><img src="../img/icons/ROOK-add-icon.png" style="height: 1em;"> ADD A HEADING</a>
	<div id="no-more-tables">
		<?php if(in_array('Administration',$tab_config)) {
			$ticket_query = "AND REPLACE(IFNULL(`approvals`,''),',','') != '' AND REPLACE(IFNULL(`revision_required`,''),',','') = ''";
			$task_query = "AND REPLACE(IFNULL(`approvals`,''),',','') != '' AND REPLACE(IFNULL(`revision_required`,''),',','') = ''";
		}
		foreach($headings as $head_id => $heading) {
			$sql = "SELECT * FROM (SELECT `id`, `heading`, `description`, `src_table`, `src_id`, `uom`, `qty`, `cost`, `profit`, `margin`, `price`, `retail`, `sort_order`, `deleted`, 'scope' `table` FROM `project_scope` WHERE `projectid`='$projectid' AND `projectid` > 0 AND `deleted`=0 UNION
				SELECT `id`, `heading`, `description`, `billable_table`, `billable_id`, `uom`, `qty`, `cost`, `profit`, `margin`, `price`, `retail`, `sort_order`, `deleted`, 'scope' `table` FROM `project_billable` WHERE `projectid`='$projectid' AND `projectid` > 0 AND `billable_table`='' AND `billable_id`='' AND `deleted`=0 UNION
				SELECT `tasklistid`, 'Tasks', `heading`, 'tasklist', `tasklistid`, '', '', '', '', '', '', '', `tasklistid`, `deleted`, 'tasklist' `table` FROM `tasklist` WHERE `projectid`='$projectid' AND `projectid` > 0 AND `deleted`=0 $task_query UNION
				SELECT `ticketid`, '".TICKET_TILE."', `heading`, 'tickets', `ticketid`, '', 1, `services_cost` + IF(`billing_discount_type`='%',`billing_discount` * `services_cost` / 100,`billing_discount`), IF(`billing_discount_type`='%',-`billing_discount` * `services_cost` / 100,-`billing_discount`), IF(`billing_discount_type`='%',-`billing_discount`,-`billing_discount` / `services_cost` * 100), `services_cost`, `services_cost`, `ticketid`, `deleted`, 'tickets' `table` FROM `tickets` WHERE `projectid`='$projectid' AND `projectid` > 0 AND `deleted`=0 $ticket_query UNION
				SELECT `ticket_attached`.`id`, 'Task', `ticket_attached`.`position`, 'ticket', `tickets`.`ticketid`, '', '', '', '', '', '', '', `id`, 0, 'staff_task' `table` FROM `ticket_attached` LEFT JOIN `tickets` ON `ticket_attached`.`ticketid`=`tickets`.`ticketid` WHERE `tickets`.`projectid`='$projectid' AND `tickets`.`projectid` > 0 UNION
				SELECT `time_cards_id`, 'Time Sheets', `date`, 'time_cards', 'time_card_id', `type_of_time`, `total_hrs`, '', '', '', '', '', UNIX_TIMESTAMP(`date`), 0, 'time_cards' `table` FROM `time_cards` WHERE `staff` IN ({$project['clientid']}) AND `deleted` = 0 AND `type_of_time` NOT IN ('day_tracking','day_break')) billables WHERE `heading`='$heading' ORDER BY `table`, `sort_order`";
			$rate_cards = mysqli_fetch_all(mysqli_query($dbc, "SELECT `description` heading, `cost` cost FROM `company_rate_card` WHERE `tile_name` = 'Time Sheet' AND `deleted` = 0 AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31') UNION SELECT `rate_card_name` heading, `total_price` cost FROM `rate_card` WHERE `deleted` = 0 AND `clientid` IN ({$project['clientid']}) AND DATE(NOW()) BETWEEN `start_date` AND IFNULL(NULLIF(`end_date`,'0000-00-00'),'9999-12-31')"),MYSQLI_ASSOC);
			$lines = mysqli_query($dbc, $sql);
			if(mysqli_num_rows($lines) > 0) { ?>
				<div class="form-horizontal col-sm-12" data-tab-name="<?= $head_id ?>">
					<div class="form-group">
						<div class="sort_table">
							<table class="table table-bordered">
								<tr>
									<td colspan="10">
										<h3><?= $heading ?></h3>
									</td>
								</tr>
								<tr class="hidden-sm hidden-xs">
									<?php foreach($heading_order as $order_info) {
										$order_info = explode('***',$order_info);
										echo "<th>";
										echo (empty($order_info[1]) ? $order_info[0] : $order_info[1])."</th>";
										if($head_id == 'time_cards' && $order_info[0] == 'Quantity') {
											echo "<th>Rate Card</th>";
										}
									} ?>
									<th data-columns='<?= $columns ?>' data-width='1'></th>
								</tr>
								<?php while($line = mysqli_fetch_array($lines)) {
									if($head_id == 'time_cards') {
										$time_card = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `time_cards` WHERE `time_cards_id` = '{$line['id']}'"));
										$line['description'] = (!empty(get_client($dbc, $time_card['staff'])) ? get_client($dbc, $time_card['staff']) : get_contact($dbc, $time_card['staff'])).' - '.$time_card['date'];
									}
									mysqli_query($dbc, "INSERT INTO `project_billable` (`projectid`, `heading`, `billable_table`, `billable_id`, `description`, `uom`, `qty`, `cost`, `profit`, `margin`, `price`, `retail`, `sort_order`)
										SELECT '$projectid', '$heading', '{$line['table']}', '{$line['id']}', '{$line['description']}', '{$line['uom']}', '{$line['qty']}', '{$line['cost']}', '{$line['profit']}', '{$line['margin']}', '{$line['price']}', '{$line['retail']}', '{$line['sort_order']}' FROM (SELECT COUNT(*) rows FROM `project_billable` WHERE `projectid`='$projectid' AND `projectid` > 0 AND `heading`='$heading' AND `billable_table`='{$line['table']}' AND `billable_id`='{$line['id']}') num WHERE num.rows=0");
									$billable = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project_billable` WHERE `billable_table`='{$line['table']}' AND `billable_id`='{$line['id']}'"));
									if($billable['is_billable'] == 1) { ?>
										<tr class="<?= $billable['bill_id'] > 0 ? 'strikethrough' : '' ?>">
											<input type="hidden" name="billable_table" value="<?= $billable['billable_table'] ?>">
											<input type="hidden" name="billable_id" value="<?= $billable['billable_id'] ?>">
											<input type="hidden" name="heading" value="<?= $heading ?>" data-table="project_billable" data-id="<?= $billable['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>">
											<input type="hidden" name="sort_order" value="<?= $billable['sort_order'] ?>" data-table="project_billable" data-id="<?= $billable['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>">
											<?php foreach($heading_order as $order_info) {
												$order_info = explode('***',$order_info);
												echo "<td data-title='".(empty($order_info[1]) ? $order_info[0] : $order_info[1])."'>";
												switch($order_info[0]) {
													case 'Type':
														if($billable['bill_id'] > 0) { ?>
															<input type="text" readonly value="<?= array_search($line['src_table'], $tiles) ?>" class="form-control">
														<?php } else { ?>
															<select name="src_table" class="chosen-select-deselect form-control" data-id="<?= $billable['id'] ?>" data-id-field="id" data-table="project_billable" data-project="<?= $projectid ?>">
																<option></option>
																<?php foreach($tiles as $label => $tile_name) { ?>
																	<option <?= $line['src_table'] == $tile_name ? 'selected' : '' ?> value="<?= $tile_name ?>"><?= $label ?></option>
																<?php }
																if(!in_array($line['src_table'],$tiles)) { ?>
																	<option selected value="<?= $line['src_table'] ?>"><?= $line['heading'] ?></option>
																<?php } ?>
															</select>
														<?php }
														break;
													case 'UOM': ?>
														<input type="text" name="uom" class="form-control" value="<?= $billable['uom'] ?>" data-id="<?= $billable['id'] ?>" data-id-field="id" data-table="project_billable" data-project="<?= $projectid ?>" <?= $billable['bill_id'] > 0 ? 'readonly' : '' ?>>
														<?php break;
													case 'Quantity': ?>
														<input type="number" name="qty" class="form-control" data-id="<?= $billable['id'] ?>" data-id-field="id" data-table="project_billable" value="<?= $billable['qty'] ?>" min="0" step="0.0001" data-project="<?= $projectid ?>" <?= $billable['bill_id'] > 0 ? 'readonly' : '' ?>>
														<?php if($head_id == 'time_cards') { ?>
															</td>
															<td data-title='Rate Card' <?= $billable['bill_id'] > 0 ? 'style="pointer-events: none; opacity: 0.5;";' : '' ?>>
																<select name="rate_card" class="chosen-select-deselect form-control">
																	<option></option>
																	<?php foreach ($rate_cards as $rate_card) {
																		echo '<option value="'.$rate_card['cost'].'">'.$rate_card['heading'].'</option>';
																	} ?>
																</select>
														<?php } ?>
														<?php break;
													case 'Category': ?>
														<select name="category" class="chosen-select-deselect form-control">
															<option></option>
														</select>
														<?php break;
													case 'Item Type': ?>
														<select name="item_type" class="chosen-select-deselect form-control">
															<option></option>
														</select>
														<?php break;
													case 'Description':
														if($billable['bill_id'] > 0) { ?>
															<input type="text" readonly value="<?php if($billable['description'] != '') {
																echo $billable['description'];
															} else {
																foreach($src_options as $option) {
																	if($option['id'] == $billable['billable_id'] && $option['tile_name'] == strtolower($billable['billable_table'])) {
																		echo $option['label'];
																	}
																}
															} ?>" class="form-control">
														<?php } else { ?>
															<input type="text" name="description" class="form-control" value="<?= $billable['description'] ?>" data-id="<?= $billable['id'] ?>" data-id-field="id" data-table="project_billable" style="<?= in_array($billable['billable_table'],['miscellaneous','tickets','tasklist','project_milestone_checklist','workorder','time_cards']) ? '' : 'display: none;' ?>" data-project="<?= $projectid ?>">
															<div class="select_div" <?= in_array($billable['billable_table'],['miscellaneous','tickets','tasklist','project_milestone_checklist','workorder','time_cards']) ? 'style="display: none;"' : '' ?>>
																<select name="src_id" class="chosen-select-deselect form-control" data-id="<?= $billable['id'] ?>" data-id-field="id" data-table="project_billable" data-project="<?= $projectid ?>" data-line-id="<?= $billable['billable_id'] ?>">
																	<option></option>
																</select>
															</div>
														<?php }
														break;
													case 'Cost': ?>
														<input type="text" name="cost" class="form-control" value="<?= $billable['cost'] ?>" readonly data-table="project_billable" data-id="<?= $billable['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>">
														<?php break;
													case 'Margin': ?>
														<input type="text" name="margin" class="form-control" value="<?= $billable['margin'] ?>" data-table="project_billable" data-id="<?= $billable['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>" <?= $billable['bill_id'] > 0 ? 'readonly' : '' ?>>
														<?php break;
													case 'Profit': ?>
														<input type="text" name="profit" class="form-control" value="<?= $billable['profit'] ?>" data-table="project_billable" data-id="<?= $billable['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>" <?= $billable['bill_id'] > 0 ? 'readonly' : '' ?>>
														<?php break;
													case 'Price': ?>
														<input type="text" name="price" class="form-control" value="<?= $billable['price'] ?>" readonly data-table="project_billable" data-id="<?= $billable['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>">
														<?php break;
													case 'Total': ?>
														<input type="text" name="retail" class="form-control" value="<?= $billable['retail'] ?>" readonly data-table="project_billable" data-id="<?= $billable['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>">
														<?php break;
												}
												echo "</td>";
											} ?>
											<td data-title="Function">
												<a href="" class="breakdown active" <?= $billable['src_table'] == 'miscellaneous' ? '' : 'style="display: none;"' ?> onclick="return false;"><small>+ BREAKDOWN</small></a>
												<img src="../img/remove.png" style="height: 1em;" onclick="remove_line(this);" data-table="project_billable" data-id="<?= $billable['id'] ?>" data-id-field="id" name="is_billable" data-project="<?= $projectid ?>">
												<img src="../img/icons/ROOK-add-icon.png" style="height: 1em;" onclick="add_line(this);">
												<img src="../img/icons/drag_handle.png" style="height: 1em;" class="pull-right line-handle no-toggle" data-table="project_billable" data-id="<?= $billable['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>" title="Drag">
												<?php if($billable['billable_table'] == 'tickets' && !($billable['bill_id'] > 0) && !($billable['profit'] > 0) && !($billable['profit'] < 0)) { ?>
													<img src="../img/icons/ROOK-edit-icon.png" class="inline-img no-toggle" title="Edit as Individual Details" onclick="edit_details(this);">
												<?php } ?>
												<label class="control-checkbox small"><input type="checkbox" name="attach_to_new" data-id="<?= $billable['id'] ?>" <?= $billable['bill_id'] > 0 ? 'disabled' : '' ?>></label>
												</td>
										</tr>
									<?php }
								} ?>
							</table>
						</div>
					</div>
				</div>
			<?php }
		} ?>
	</div>
	<button onclick="create_bill(); return false;" class="btn brand-btn pull-right">Create</button></div>
<?php } ?>
<?php include('next_buttons.php'); ?>