<?php include_once('../include.php');
error_reporting(0);
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($project)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
}
$value_config = array_filter(array_unique(array_merge(explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))[0]),explode(',',mysqli_fetch_array(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL'"))[0]))));
include('../Rate Card/line_types.php');
$heading_order = explode('#*#', get_config($dbc, 'estimate_field_order'));
$headings = [];
$query = mysqli_query($dbc, "SELECT `heading` FROM `project_scope` WHERE `projectid`='$projectid' AND `deleted`=0 GROUP BY `heading` ORDER BY MIN(`sort_order`)");
if(mysqli_num_rows($query) > 0) {
	while($row = mysqli_fetch_array($query)) {
		$headings[preg_replace('/[^a-z]*/','',strtolower($row[0]))] = $row[0];
	}
} else {
	$headings['scope'] = 'Scope';
} ?>
<script>
var total_cost = 0;
var total_price = 0;
$(document).ready(function() {
	$('[data-table]').off('change',update_field).change(update_field);
	$('[name=src_id]').each(function() { fill_selects($(this).closest('tr')); });
});
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
function saveField(src) {
	if(this.value == 'CUSTOM') {
		$(this).closest('.form-group').find('div[class^=col-sm-]').show();
		$(this).closest('.col-sm-12').hide();
		$(this).closest('.form-group').find('input').focus();
	} else {
		var row = $(src).closest('tr');
		var table = $(src).data('table');
		var project = $(src).data('project');
		var id = $(src).data('id');
		var id_field = $(src).data('id-field');
		var field = src.name;
		var value = src.value;
		if(value == undefined) {
			value = 1;
		}
		$.ajax({
			url: 'projects_ajax.php?action=project_fields',
			method: 'POST',
			data: {
				table: table,
				project: project,
				id: id,
				id_field: id_field,
				field: field,
				value: value
			},
			result: 'html',
			success: function(response) {
				if(response != '') {
					row.find('[data-id]').data('id',response);
					row.find('[name=heading]').change();
				}
				if(field == 'src_table') {
					if(value == 'miscellaneous') {
						row.find('.select_div').hide();
						row.find('[name=description]').show();
						row.find('.breakdown').show();
					} else {
						row.find('.breakdown').hide();
						row.find('[name=description]').hide();
						row.find('.select_div').show().find('option').each(function() {
							if((''+$(this).data('tile-name')).toLowerCase() == src.value.toLowerCase()) {
								$(this).show();
							} else {
								$(this).hide();
							}
						});
						row.find('.select_div select').trigger('change.select2');
					}
				}
			}
		});
		if(src.name == 'qty') {
			var row = $(src).closest('tr');
			var price = +row.find('[name=price]').val();
			total_cost = 0;
			$('[name=cost][data-table]').each(function() { total_cost += +this.value * +$(this).closest('tr').find('[name=qty]').val(); });
			$('#total_cost').html('$'+(+total_cost).toFixed(2));
			row.find('[name=retail]').val((price * +src.value).toFixed(2)).change();
		} else if(src.name == 'cost') {
			var row = $(src).closest('tr');
			var cost = +src.value;
			var price = +row.find('[name=price]');
			var profit = price - cost;
			if(row.find('[name=profit]').val() != profit) {
				row.find('[name=profit]').val(profit).change();
			}
			if(cost != 0) {
				var margin = (profit / cost * 100).toFixed(2);
			} else {
				var margin = 0;
			}
			if(row.find('[name=margin]').val() != margin) {
				row.find('[name=margin]').val(margin).change();
			}

			total_cost = 0;
			$('[name=cost][data-table]').each(function() { total_cost += +this.value * +$(this).closest('tr').find('[name=qty]').val(); });
			var profit = +total_price - total_cost;
			if(total_cost != 0) {
				var margin = (profit / total_cost * 100).toFixed(2);
			} else {
				var margin = 0;
			}
			$('#total_cost').html('$'+(+total_cost).toFixed(2));
			$('#total_profit').html('$'+ (+profit).toFixed(2));
			$('#total_margin').html((+margin).toFixed(2)+'%');
			$('#total_price').html('$'+ (+total_price).toFixed(2));
		} else if(src.name == 'margin') {
			var row = $(src).closest('tr');
			var cost = +row.find('[name=cost]').val();
			if(cost == 0 && src.value != 0) {
				$(src).val(0).change();
			} else {
				var profit = (cost * +src.value / 100).toFixed(2);
				if(profit != row.find('[name=profit]').val()) {
					row.find('[name=profit]').val((+profit).toFixed(2)).change();
				}
				var price = cost + +profit;
				if(row.find('[name=price]').val() != price) {
					row.find('[name=price]').val((+price).toFixed(2)).change();
				}
			}
		} else if(src.name == 'profit') {
			var row = $(src).closest('tr');
			var cost = +row.find('[name=cost]').val();
			if(cost != 0) {
				var margin = (+src.value / cost * 100).toFixed(2);
				if(margin != row.find('[name=margin]').val()) {
					row.find('[name=margin]').val((+margin).toFixed(2)).change();
				}
			}
			var price = cost + +src.value;
			if(row.find('[name=price]').val() != price) {
				row.find('[name=price]').val((+price).toFixed(2)).change();
			}
		} else if(src.name == 'price') {
			var row = $(src).closest('tr');
			var price = +src.value;
			var qty = +row.find('[name=qty]').val();
			row.find('[name=retail]').val((price * qty).toFixed(2)).change();
			var cost = +row.find('[name=cost]').val();
			var profit = price - cost;
			if(row.find('[name=profit]').val() != profit) {
				row.find('[name=profit]').val(profit).change();
			}
			if(cost != 0) {
				var margin = (profit / cost * 100).toFixed(2);
			} else {
				var margin = 0;
			}
			if(row.find('[name=margin]').val() != margin) {
				row.find('[name=margin]').val(margin).change();
			}
		} else if(src.name == 'retail') {
			total_price = 0;
			$('[name=retail][data-table]').each(function() { total_price += +this.value; });
			var profit = total_price - +total_cost;
			if(total_cost != 0) {
				var margin = profit / +total_cost * 100;
			} else {
				var margin = 0;
			}
			$('#total_cost').html('$'+ (+total_cost).toFixed(2));
			$('#total_profit').html('$'+ (+profit).toFixed(2));
			$('#total_margin').html((+margin).toFixed(2)+'%');
			$('#total_price').html('$'+ (+total_price).toFixed(2));
		}
	}
}
function update_field() {
	src = this;
	if($(src).data('table') != '') {
		saveField(this);
	}
}
function add_line(src) {
	var line = $(src).closest('tr');
	var clone = line.clone();
	clone.find('input,select').not('[name=heading]').val('');
	clone.find('.breakdown').hide();
	clone.find('[name=description]').hide();
	clone.find('.select_div').show().find('option').hide();
	resetChosen(clone.find("select[class^=chosen]"));
	clone.find('[data-id]').data('id','');

	line.closest('table').append(clone);
	$('[data-table]').off('change',update_field).change(update_field);
}
function remove_line(a) {
	saveField(a);
	$(a).closest('tr').remove();
}
function remove_heading(src) {
	saveField(src);
	window.location.reload();
}
profile_tab = [];
var lock_timeout;
$(document).ready(function() {
	$('input,select,textarea').change(saveField);
	tinymce.on('AddEditor', function (e) {
		tinymce.editors[e.editor.id].on('blur',function() {
			this.save();
			$(this.getElement()).change();
		});
	});
	$('#no-more-tables').sortable({
		connectWith: '.sort_table',
		handle: '.line-handle',
		items: 'tr',
		update: save_sort
	});
});
function save_sort() {
	set_headings();
	var i = 0;
	$('[name=sort_order]').each(function() {
		$(this).val(i++).change();
	});
}
function set_headings() {
	$('[name=heading][data-init]').each(function() {
		$(this).closest('table').find('[name=heading][data-table]').val(this.value).change();
	});
}
function add_heading() {
	$.ajax({
		url: 'projects_ajax.php?action=project_add_heading',
		method: 'POST',
		data: {
			project: '<?= $projectid ?>'
		},
		success: function(response) {
			window.location.reload();
		}
	});
}
function addendum_estimate() {
	$.ajax({
		url: 'projects_ajax.php?action=addendum_estimate&projectid=<?= $projectid ?>',
		success: function(response) {
			if(response > 0) {
				window.location.replace('../Estimate/estimates.php?edit='+response);
			}
		}
	});
}
function create_from_scope() {
	var lines = [];
	$('tr:not(.strikethrough)').find('[name=attach_to_new]:checked').each(function() {
		lines.push($(this).data('id'));
	});
	if(lines.length > 0) {
		overlayIFrame('edit_project_attach.php?projectid=<?= $projectid ?>&lines='+JSON.stringify(lines));
	} else {
		alert('You must select one or more lines to create an assignment from the scope.');
	}
}
</script>
<!-- <h3><?= PROJECT_NOUN ?> Scope -->
	<?php if($security['edit'] > 0) { ?>
		<div class="gap-bottom gap-right pull-right">
			<button onclick="addendum_estimate(); return false;" class="btn brand-btn">Add Addendum Estimate</button>
			<button onclick="create_from_scope(); return false;" class="btn brand-btn">Create</button>
		</div>
		<div class="clearfix"></div>
	<?php } ?>
<!-- </h3> -->
<div id="no-more-tables">
	<?php foreach($headings as $head_id => $heading) { ?>
		<div class="form-horizontal col-sm-12" data-tab-name="<?= $head_id ?>">
			<div class="form-group" style="margin-bottom: 0;">
				<div class="sort_table">
					<table class="table table-bordered">
						<tr>
							<td colspan="10">
								<h3 <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><input placeholder="Heading" type="text" name="heading" value="<?= $heading ?>" onchange="set_headings(this);" data-init="<?= $heading ?>" class="form-control" <?= !($security['edit'] > 0) ? 'readonly disabled' : '' ?>></h3>
							</td>
						</tr>
						<tr class="hidden-sm hidden-xs">
							<?php foreach($heading_order as $order_info) {
								$order_info = explode('***',$order_info);
								echo "<th>";
								echo (empty($order_info[1]) ? $order_info[0] : $order_info[1])."</th>";
							} ?>
							<th data-columns='<?= $columns ?>' data-width='1'></th>
						</tr>
						<?php $scope_type = (!empty($_GET['scope_type']) ? " AND `src_table`='".filter_var($_GET['scope_type'],FILTER_SANITIZE_STRING)."'" : '');
						$lines = mysqli_query($dbc, "SELECT * FROM `project_scope` WHERE `projectid`='$projectid' AND `heading`='$heading' AND `deleted`=0 $scope_type ORDER BY `sort_order`");
						$line = mysqli_fetch_array($lines);
						do { ?>
							<tr class="<?= $line['attach_id'] > 0 ? 'strikethrough' : '' ?>">
								<input type="hidden" name="heading" value="<?= $heading ?>" data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>">
								<input type="hidden" name="sort_order" value="<?= $line['sort_order'] ?>" data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>">
								<?php foreach($heading_order as $order_info) {
									$order_info = explode('***',$order_info);
									echo "<td data-title='".(empty($order_info[1]) ? $order_info[0] : $order_info[1])."' ".(!($security['edit'] > 0) ? 'class="readonly-block"' : '').">";
									switch($order_info[0]) {
										case 'Type':
											if($line['attach_id'] > 0) { ?>
												<input type="text" readonly value="<?= array_search($line['src_table'], $tiles) ?>" class="form-control">
											<?php } else { ?>
												<select name="src_table" class="chosen-select-deselect form-control" data-id="<?= $line['id'] ?>" data-id-field="id" data-table="project_scope" data-project="<?= $projectid ?>">
													<option></option>
													<?php foreach($tiles as $label => $tile_name) { ?>
														<option <?= $line['src_table'] == $tile_name ? 'selected' : '' ?> value="<?= $tile_name ?>"><?= $label ?></option>
													<?php } ?>
												</select>
											<?php }
											break;
										case 'UOM': ?>
											<input type="text" name="uom" class="form-control" value="<?= $line['uom'] ?>" data-id="<?= $line['id'] ?>" data-id-field="id" data-table="project_scope" data-project="<?= $projectid ?>">
											<?php break;
										case 'Quantity': ?>
											<input type="number" name="qty" class="form-control" data-id="<?= $line['id'] ?>" data-id-field="id" data-table="project_scope" value="<?= $line['qty'] ?>" min="0" step="0.0001" data-project="<?= $projectid ?>">
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
											if($line['attach_id'] > 0) { ?>
												<input type="text" readonly value="<?php if($line['description'] != '') {
													echo $line['description'];
												} else {
													foreach($src_options as $option) {
														if($option['id'] == $line['src_id'] && $option['tile_name'] == strtolower($line['src_table'])) {
															echo $option['label'];
														}
													}
												} ?>" class="form-control">
											<?php } else { ?>
												<input type="text" name="description" class="form-control" value="<?= $line['description'] ?>" data-id="<?= $line['id'] ?>" data-id-field="id" data-table="project_scope" style="<?= $line['src_table'] == 'miscellaneous' ? '' : 'display: none;' ?>" data-project="<?= $projectid ?>">
												<div class="select_div" <?= $line['src_table'] == 'miscellaneous' ? 'style="display: none;"' : '' ?>>
													<select name="src_id" class="chosen-select-deselect form-control" data-id="<?= $line['id'] ?>" data-id-field="id" data-table="project_scope" data-project="<?= $projectid ?>" data-line-id="<?= $line['src_id'] ?>">
														<option></option>
													</select>
												</div>
											<?php }
											break;
										case 'Cost': ?>
											<input type="text" name="cost" class="form-control" value="<?= $line['cost'] ?>" readonly data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>">
											<?php break;
										case 'Margin': ?>
											<input type="text" name="margin" class="form-control" value="<?= $line['margin'] ?>" data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>">
											<?php break;
										case 'Profit': ?>
											<input type="text" name="profit" class="form-control" value="<?= $line['profit'] ?>" data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>">
											<?php break;
										case 'Estimate Price': ?>
											<input type="text" name="price" class="form-control" value="<?= $line['price'] ?>" data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>">
											<?php break;
										case 'Total': ?>
											<input type="text" name="retail" class="form-control" value="<?= $line['retail'] ?>" readonly data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>">
											<?php break;
									}
									echo "</td>";
								} ?>
								<td style="min-width: 100px;" data-title="Function">
									<?php if($security['edit'] > 0) { ?>
										<a href="" class="breakdown active" <?= $line['src_table'] == 'miscellaneous' ? '' : 'style="display: none;"' ?> onclick="return false;"><small>+ BREAKDOWN</small></a>
										<img src="../img/remove.png" style="height: 1em;" onclick="remove_line(this);" data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id" name="deleted" data-project="<?= $projectid ?>">
										<img src="../img/icons/ROOK-add-icon.png" style="height: 1em;" onclick="add_line(this);">
										<img src="../img/icons/drag_handle.png" style="height: 1em; margin-top: 0.7em;" class="pull-right line-handle no-toggle" data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id" data-project="<?= $projectid ?>" title="Drag">
									<?php } ?>
									<label class="control-checkbox small <?= !($security['edit'] > 0) ? 'readonly-block' : '' ?>"><input type="checkbox" name="attach_to_new" <?= $line['attach_id'] > 0 ? 'disabled checked' : '' ?> data-id="<?= $line['id'] ?>" style="position: relative; top: 0.5em;"></label>
									</td>
							</tr>
						<?php } while($line = mysqli_fetch_array($lines)); ?>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>
</div>
<div class="form-group gap-right">
	<?php if($security['edit'] > 0) { ?>
		<span class="popover-examples list-inline tooltip-navigation"><a style="margin:5px 5px 0 0;" data-toggle="tooltip" data-placement="bottom" title="" data-original-title="Click here to add a new Section. If there are no Sections in this Project Scope, then an empty Section will always appear."><img src="<?= WEBSITE_URL ?>/img/info.png" width="20"></a></span><a href="" class="active" onclick="add_heading(); return false;"><img src="../img/icons/ROOK-add-icon.png" style="height: 1em;"> ADD NEW SECTION</a>
		<div class="pull-right">
			<button onclick="addendum_estimate(); return false;" class="btn brand-btn">Add Addendum Estimate</button>
			<button onclick="create_from_scope(); return false;" class="btn brand-btn">Create</button>
		</div>
	<?php } ?>
	<div class="clearfix gap-bottom"></div>
	<?php include('next_buttons.php'); ?>
</div>