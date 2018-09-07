<?php include('../Rate Card/line_types.php');
$heading_order = explode('#*#', get_config($dbc, 'estimate_field_order')); ?>
<script>
$(document).ready(function() {
	$('input,select').change(update_field);
    $('[name=src_table]').change(function(){
        //window.location.reload();
    });
	$('#no-more-tables').sortable({
		handle: '.heading-handle',
		items: 'table',
		update: save_sort
	});
	$('.sort_table').sortable({
		connectWith: '.sort_table',
		handle: '.line-handle',
		items: 'tr',
		update: save_sort
	});
	$('[name=src_id]').each(function() {
		fill_selects($(this).closest('tr'));
		$(this).change(function() {
			$(this).closest('tr').find('[name=src_id]').data('line-id','');
		});
		$(this).closest('tr').find('[name=category],[name=item_type]').change(function() {
			$(this).closest('tr').find('[name=src_id]').data('line-id','');
			fill_selects($(this).closest('tr'));
		});
	});
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
function save_field(src) {
	var table = $(src).data('table');
	var id = $(src).data('id');
	var heading = $(src).closest('table').data('heading');
	var template = $('[name=id]').val();
	var field = src.name;
	var value = src.value;
	if(value == undefined) {
		value = 1;
	}
	$.ajax({
		url: 'estimates_ajax.php?action=save_template_field',
		method: 'POST',
		data: {
			table_name: table,
			line_id: id,
			heading_id: heading,
			template_id: template,
			field_name: field,
			value: value
		},
		result: 'html',
		success: function(response) {
			if(response != '') {
				if(table == 'estimate_templates') {
					$('[name=id]').val(response);
					save_sort();
					$('[name=heading_name]').first().change()
				} else if(table == 'estimate_template_headings') {
					$(src).closest('table').data('heading',response);
				} else if(table == 'estimate_template_lines') {
					$(src).closest('tr').find('[data-id]').data('id',response);
				}
			}
		}
	});
}
function update_field() {
	src = this;
	if(src.name == 'src_table' && src.value == 'vpl') {
		var heading_id = $(src).closest('table').data('heading');
		overlayIFrameSlider('<?= WEBSITE_URL ?>/Estimate/template_edit_vpl.php?heading_id='+heading_id);
	} else {
		if(src.name == 'src_table') {
			if(src.value == 'miscellaneous') {
				$(src).closest('tr').find('.select_div').hide();
				$(src).closest('tr').find('[name=description]').show();
				$(src).closest('tr').find('.breakdown').show();
			} else {
				$(src).closest('tr').find('.breakdown').hide();
				$(src).closest('tr').find('[name=description]').hide();
				$(src).closest('tr').find('.select_div').show().find('option').each(function() {
					if((''+$(this).data('tile-name')).toLowerCase() == src.value.toLowerCase()) {
						$(this).show();
					} else {
						$(this).hide();
					}
				});
				$(src).closest('tr').find('.select_div select').trigger('change.select2');
			}
	        window.location.reload();
		}
		if($(src).data('table') != '') {
			save_field(this);
		}
	}
}
function add_line(src) {
	var line = $(src).closest('tr');
	var clone = line.clone();
	clone.find('input,select').val('');
	clone.find('.breakdown').hide();
	clone.find('[name=description]').hide();
	clone.find('.select_div').show().find('option').hide();
	resetChosen(clone.find("select[class^=chosen]"));
	clone.find('[data-id]').data('id','');
	
	line.closest('table').append(clone);
	$('input,select').off('change', update_field).change(update_field);
}
function remove_line(a) {
	if($(a).closest('table').find('[name=src_table]').length <= 1) {
		add_line(a);
	}
	save_field(a);
	$(a).closest('tr').remove();
}
function add_heading() {
	var clone = $('table').last().clone();
	clone.find('input').val('');
	clone.data('heading','');
	
	$('table').last().after(clone);
	$('table').last().find('.line-handle').each(function() {
		remove_line(this);
	});
	$('input,select').off('change', update_field).change(update_field);
}
function remove_heading(src) {
	if($('table[data-heading]').length <= 1) {
		add_heading();
	}
	save_field(src);
	$(src).closest('table').remove();
}

function save_sort() {
	var ids = [];
	$('table').each(function() {
		ids.push($(this).data('heading'));
	});
	$.ajax({
		url: 'estimates_ajax.php?action=set_sort_order',
		method: 'POST',
		data: {
			table_name: 'estimate_template_headings',
			sort_ids: ids
		}
	});
	
	var ids = [];
	$('[name=src_table]').each(function() {
		ids.push($(this).data('id'));
		if(this.value != '' && $(this).closest('tr').find('[name=src_id]').val() != '') {
			save_field(this);
		}
	});
	$.ajax({
		url: 'estimates_ajax.php?action=set_sort_order',
		method: 'POST',
		data: {
			table_name: 'estimate_template_lines',
			sort_ids: ids
		}
	});
}
</script>
<form class="form-horizontal">
	<div class="col-sm-12">
		<?php $templateid = filter_var($_GET['template'],FILTER_SANITIZE_STRING);
		if($templateid > 0) {
			$template = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate_templates` WHERE `id`='$templateid'"));
		}
		$headings = mysqli_query($dbc, "SELECT `id`, `heading_name`, `sort_order` FROM `estimate_template_headings` WHERE `template_id`='$templateid' AND `deleted`=0 ORDER BY `sort_order`"); ?>
		<h3><?= ($templateid > 0 ? $template['template_name'].' Template' : ($templateid == 'list' ? 'Please select an option' : 'Create New Template')) ?></h3>
		<?php if($templateid == 'new' || $templateid > 0) { ?>
			<div class="form-group">
				<label class="col-sm-4 control-label">Template Name</label>
				<div class="col-sm-8">
					<input type="text" name="template_name" value="<?= $template['template_name'] ?>" class="form-control" data-table="estimate_templates">
					<input type="hidden" name="id" value="<?= $template['id'] ?>" data-table="estimate_templates">
				</div>
			</div>
			<?php if(count($regions) > 0) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Region</label>
					<div class="col-sm-8">
						<select name="region" class="chosen-select-deselect form-control" data-table="estimate_templates">
							<option></option>
							<?php foreach($regions as $region) { ?>
								<option <?= $template['region'] == $region ? 'selected' : '' ?> value="<?= $region ?>"><?= $region ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php }
			if(count($locations) > 0) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Location</label>
					<div class="col-sm-8">
						<select name="location" class="chosen-select-deselect form-control" data-table="estimate_templates">
							<option></option>
							<?php foreach($locations as $location) { ?>
								<option <?= $template['location'] == $location ? 'selected' : '' ?> value="<?= $location ?>"><?= $location ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php }
			if(count($classifications) > 0) { ?>
				<div class="form-group">
					<label class="col-sm-4 control-label">Classification</label>
					<div class="col-sm-8">
						<select name="classification" class="chosen-select-deselect form-control" data-table="estimate_templates">
							<option></option>
							<?php foreach($classifications as $classification) { ?>
								<option <?= $template['classification'] == $classification ? 'selected' : '' ?> value="<?= $classification ?>"><?= $classification ?></option>
							<?php } ?>
						</select>
					</div>
				</div>
			<?php } ?>
			<div id="no-more-tables" class="form-group">
				<?php $heading = mysqli_fetch_array($headings);
				do { ?>
					<div class="sort_table">
						<table class="table table-bordered" data-heading="<?= $heading['id'] ?>">
							<tr>
								<td data-title="Heading Name" colspan="10">
									<input type="text" name="heading_name" value="<?= $heading['heading_name'] ?>" data-table="estimate_template_headings" class="form-control" style="display: inline; width: calc(100% - 4em);">
									<img src="../img/icons/drag_handle.png" width="20" class="cursor-hand pull-right gap-left gap-top heading-handle no-toggle" title="Drag">
									<img src="../img/remove.png" width="19" class="cursor-hand pull-right gap-top heading-handle" onclick="remove_heading(this);" data-table="estimate_template_headings" name="deleted">
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
							<?php $lines = mysqli_query($dbc, "SELECT `id`, `description`, `src_table`, `src_id`, `qty`, `sort_order` FROM `estimate_template_lines` WHERE `heading_id`='{$heading['id']}' AND `deleted`=0 ORDER BY `sort_order`");
							$line = mysqli_fetch_array($lines);
							do { ?>
								<tr>
									<input type="hidden" name="heading" value="<?= $heading ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
									<input type="hidden" name="sort_order" value="<?= $line['sort_order'] ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
									<?php foreach($heading_order as $order_info) {
										$order_info = explode('***',$order_info);
										echo "<td data-title='".(empty($order_info[1]) ? $order_info[0] : $order_info[1])."'>";
										switch($order_info[0]) {
											case 'Type': ?>
												<select name="src_table" class="chosen-select-deselect form-control" data-id="<?= $line['id'] ?>" data-table="estimate_template_lines">
													<option></option>
													<?php foreach($tiles as $label => $tile_name) { ?>
														<option <?= $line['src_table'] == $tile_name ? 'selected' : '' ?> value="<?= $tile_name ?>"><?= $label ?></option>
													<?php } ?>
												</select>
												<?php break;
											case 'UOM': ?>
												<input type="text" name="rate_uom" class="form-control" readonly>
												<?php break;
											case 'Quantity': ?>
												<input type="number" name="qty" class="form-control" data-id="<?= $line['id'] ?>" data-table="estimate_template_lines" value="<?= round($line['qty'],2) ?>" min="0" step="1">
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
											case 'Description': ?>
												<input type="text" name="description" class="form-control" value="<?= $line['description'] ?>" data-id="<?= $line['id'] ?>" data-table="estimate_template_lines" style="<?= $line['src_table'] == 'miscellaneous' ? '' : 'display: none;' ?>">
												<div class="select_div" <?= $line['src_table'] == 'miscellaneous' ? 'style="display: none;"' : '' ?>><select name="src_id" class="chosen-select-deselect form-control" data-id="<?= $line['id'] ?>" data-table="estimate_template_lines" data-line-id="<?= $line['src_id'] ?>" data-line-table="<?= $line['src_table'] ?>">
													<option></option>
												</select></div>
												<?php break;
											case 'Cost': ?>
												<input type="text" name="rate_cost" class="form-control" readonly>
												<?php break;
											case 'Margin': ?>
												<input type="text" name="rate_margin" class="form-control" readonly>
												<?php break;
											case 'Profit': ?>
												<input type="text" name="rate_profit" class="form-control" readonly>
												<?php break;
											case 'Estimate Price': ?>
												<input type="text" name="rate_total" class="form-control" readonly>
												<?php break;
											case 'Total': ?>
												<input type="text" name="rate_retail" class="form-control" readonly>
												<?php break;
										}
										echo "</td>";
									} ?>
									<td data-title="Function" align="ceter">
										<a href="" class="breakdown active" <?= $line['src_table'] == 'miscellaneous' ? '' : 'style="display: none;"' ?> onclick="return false;"><small>+ BREAKDOWN</small></a>
										<img src="../img/remove.png" width="15" class="cursor-hand" onclick="remove_line(this);" data-table="estimate_template_lines" data-id="<?= $line['id'] ?>" name="deleted" /><br />
										<img src="../img/icons/ROOK-add-icon.png" width="17" class="cursor-hand offset-top-5" onclick="add_line(this);" /><br />
										<img src="../img/icons/drag_handle.png" width="15" class="cursor-hand offset-top-5 pull-right line-handle no-toggle" data-table="estimate_template_lines" data-id="<?= $line['id'] ?>" title="Drag" />
										</td>
								</tr>
							<?php } while($line = mysqli_fetch_array($lines)); ?>
						</table>
					</div>
				<?php } while($heading = mysqli_fetch_array($headings)); ?>
				<a href="" class="active" onclick="add_heading(); return false;"><img src="../img/icons/ROOK-add-icon.png" style="height: 1em;"> ADD A HEADING</a>
			</div>
		<?php } ?>
	</div>
</form>