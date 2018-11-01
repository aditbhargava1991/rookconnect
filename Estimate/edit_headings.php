<?php include_once('../include.php');
checkAuthorised('estimate');
error_reporting(0);
if(!isset($estimate)) {
	$estimateid = filter_var($estimateid,FILTER_SANITIZE_STRING);
	$estimate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'"));
	$rates = [];
	$query = mysqli_query($dbc, "SELECT `heading` FROM `rate_card` WHERE `estimateid`='$estimateid' GROUP BY `rate_card`");
	if(mysqli_num_rows($query) > 0) {
		while($row = mysqli_fetch_array($query)) {
			$rates[bin2hex($row[0])] = explode(':',$row[0]);
		}
	} else {
		$rates[''] = '';
	}
	$current_rate = $_GET['rate'];
	$scope_list = [];
	$query = mysqli_query($dbc, "SELECT `heading` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `rate_card`='".implode(':',$rates[$current_rate])."' AND `deleted`=0 GROUP BY `heading` ORDER BY MIN(`sort_order`)");
	if($query->num_rows > 0) {
		while($row = mysqli_fetch_array($query)) {
			$scope_list[config_safe_str($row[0])] = $row[0];
		}
	} else {
		$scope_list['scope_1'] = 'Scope 1';
	}
	$scope_id = filter_var($_GET['status'],FILTER_SANITIZE_STRING);
	$scope = $scope_list[$scope_id];
	$us_exchange = json_decode(file_get_contents('https://www.bankofcanada.ca/valet/observations/group/FX_RATES_DAILY/json'), TRUE);
}
$us_rate_no_auto = get_config($dbc, 'disable_us_auto_convert');
$us_rate = $us_exchange['observations'][count($us_exchange['observations']) - 1]['FXUSDCAD']['v'];
$heading_order = explode('#*#', get_config($dbc, 'estimate_field_order'));
if(in_array('Scope Detail',$config) && !in_array_starts('Detail',$heading_order)) {
	$heading_order[] = 'Detail***Scope Detail';
}
if(in_array('Scope Billing',$config) && !in_array_starts('Billing Frequency',$heading_order)) {
	$heading_order[] = 'Billing Frequency***Billing Frequency';
}
$columns = 0;
$displayed_column = [];
foreach($heading_order as $heading_title) {
	if(strpos_any(['UOM','Quantity','Description','Detail','Billing Frequency','Estimate Price','Total'],explode('***',$heading_title)[0]) && !in_array(explode('***',$heading_title)[0],$displayed_column)) {
		$columns++;
        $displayed_column[] = explode('***',$heading_title)[0];
	}
}
// $columns = count($heading_order);
// if(in_array_starts('Detail',$heading_order)) {
	// $columns++;
// }
// if(in_array_starts('Category',$heading_order)) {
	// $columns--;
// }
// if(in_array_starts('Item Type',$heading_order)) {
	// $columns--;
// }
// if(in_array_starts('Type',$heading_order)) {
	// $columns--;
// }
$col_spanned = $columns; ?>
<div class="form-horizontal col-sm-12" data-tab-name="<?= config_safe_str($heading) ?>">
	<div class="form-group">
		<script>
		$(document).ready(function() {
			$('[data-table]').off('change', saveField).change(saveField);
		});
		function saveFieldMethod(src) {
			var table = $(src).data('table');
			var id = $(src).data('id');
			var id_field = $(src).data('id-field');
			var field = src.name;
			var value = src.value;
			if(value == undefined) {
				value = 1;
			}
			if(src.name.substr(-2) == '[]') {
				field = src.name.split('[]')[0];
				if(src.type == 'select-one' || src.type == 'select-multiple') {
					value = [];
					$('[name="'+src.name+'"] option:selected').each(function() {
						value.push(this.value);
					});
					value = value.join(',');
				}
			}
			$.ajax({
				url: 'estimates_ajax.php?action=estimate_fields',
				method: 'POST',
				data: {
					table: table,
					id: id,
					id_field: id_field,
					field: field,
					value: value,
					estimate: '<?= $estimateid ?>'
				},
				result: 'html',
				success: function(response) {
					if(response > 0) {
						$(src).closest('tr').find('[data-id]').data('id',response);
						$(src).closest('table').find('[name=heading]').change();
						save_sort();
					}
					if(src.name == 'qty') {
						var row = $(src).closest('tr');
						var price = +row.find('[name=price]').val();
						total_cost = 0;
						$('[name=cost][data-table]').each(function() { total_cost += +this.value * +$(this).closest('tr').find('[name=qty]').val(); });
						$('#total_cost').html('$'+(+total_cost).toFixed(2));
						if(parseFloat(row.find('[name=retail]').val()) != price * +src.value) {
							row.find('[name=retail]').val((price * +src.value).toFixed(2)).change();
						}
					} else if(src.name == 'cost' && parseFloat(src.value) != 0) {
						var row = $(src).closest('tr');
						var cost = +src.value;
						var price = +row.find('[name=price]');
						var profit = price - cost;
						if(parseFloat(row.find('[name=profit]').val()) != profit) {
							row.find('[name=profit]').val(profit).change();
						}
						var margin = (profit / cost * 100).toFixed(2);
						if(parseFloat(row.find('[name=margin]').val()) != margin) {
							row.find('[name=margin]').val(margin).change();
						}

						total_cost = 0;
						$('[name=cost][data-table]').each(function() { total_cost += +this.value * +$(this).closest('tr').find('[name=qty]').val(); });
						var profit = +total_price - total_cost;
						var margin = (total_cost != 0 ? (profit / total_cost * 100).toFixed(2) : '0.00');
						$('#total_cost').html('$'+(+total_cost).toFixed(2));
						$('#total_profit').html('$'+ (+profit).toFixed(2));
						$('#total_margin').html((+margin).toFixed(2)+'%');
						$('#total_price').html('$'+ (+total_price).toFixed(2));
					} else if(src.name == 'margin' && parseFloat(src.value) != 0) {
						var row = $(src).closest('tr');
						var cost = +row.find('[name=cost]').val();
						var profit = (parseFloat(cost) == 0 ? 0 : cost * +src.value / 100).toFixed(2);
						if(profit != parseFloat(row.find('[name=profit]').val())) {
							row.find('[name=profit]').val((+profit).toFixed(2)).change();
						}
						var price = cost + +profit;
						if(parseFloat(row.find('[name=price]').val()) != price) {
							row.find('[name=price]').val((+price).toFixed(2)).change();
						}
					} else if(src.name == 'profit') {
						var row = $(src).closest('tr');
						var cost = +row.find('[name=cost]').val();
						var margin = (parseFloat(cost) == 0 ? 0 : +src.value / cost * 100).toFixed(2);
						if(margin != parseFloat(row.find('[name=margin]').val())) {
							row.find('[name=margin]').val((+margin).toFixed(2)).change();
						}
						var price = cost + +src.value;
						if(parseFloat(row.find('[name=price]').val()) != price) {
							row.find('[name=price]').val((+price).toFixed(2)).change();
						}
					} else if(src.name == 'price') {
						var row = $(src).closest('tr');
						var price = +src.value;
						var qty = +row.find('[name=qty]').val();
						if(parseFloat(row.find('[name=retail]').val()) != price * qty) {
							row.find('[name=retail]').val((price * qty).toFixed(2)).change();
						}
						var cost = +row.find('[name=cost]').val();
						var profit = price - cost;
						if(parseFloat(row.find('[name=profit]').val()) != profit) {
							row.find('[name=profit]').val(profit).change();
						}
						var margin = (parseFloat(cost) == 0 ? 0 : profit / cost * 100).toFixed(2);
						if(parseFloat(row.find('[name=margin]').val()) != margin) {
							row.find('[name=margin]').val(margin).change();
						}
					} else if(src.name == 'retail') {
						total_price = 0;
						$('[name=retail][data-table]').each(function() { total_price += +this.value; });
						var profit = total_price - +total_cost;
						var margin = (parseFloat(total_cost) == 0 ? 0 : profit / +total_cost * 100);
						$('#total_cost').html('$'+ (+total_cost).toFixed(2));
						$('#total_profit').html('$'+ (+profit).toFixed(2));
						$('#total_margin').html((+margin).toFixed(2)+'%');
						$('#total_price').html('$'+ (+total_price).toFixed(2));
					} else if(src.name == 'discount' || src.name == 'discount_type') {
						$.get('edit_summary.php', {edit: <?= $estimateid ?>}, function(response) {
							$('[data-tab-name=summary]').after(response).remove()
						});
					}
					doneSaving();
				}
			});
		}
		function update_field() {
			src = this;
			if(src.name == 'src_table') {
				if(src.value == 'miscellaneous') {
					$(src).closest('tr').find('.select_div').hide();
					$(src).closest('tr').find('[name=description]').show();
					$(src).closest('tr').find('.breakdown').show();
				} else {
					$(src).closest('tr').find('.breakdown').hide();
					$(src).closest('tr').find('[name=description]').hide();
					fill_selects($(src).closest('tr'));
				}
			}
			if($(src).data('table') != '' && (this.name != 'scope_name' || $(src).data('id') > 0)) {
				save_scope(this);
			}
		}
		</script>
		<div class="sort_table" id="no-more-tables">
            <a name="<?= config_safe_str($heading) ?>"></a>
			<div class="pull-right">
				<img src="../img/icons/drag_handle.png" class="inline-img pull-right scope-handle no-toggle" title="Drag">
				<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right cursor-hand" onclick="add_scope();">
				<img src="../img/remove.png" class="inline-img pull-right cursor-hand" onclick="rem_scope(this);">
			</div>
			<h3 class="scale-to-fill no-margin pad-bottom">
                <label class="col-sm-4">Scope Name:</label>
                <div class="col-sm-8"><input type="text" placeholder="Scope Description" name="scope_name" value="<?= $scope ?>" onchange="set_scopes(this);" data-placeholder="Scope Name" data-init="<?= $scope ?>" class="form-control"></div>
            </h3>
			<?php $heading_list = $dbc->query("SELECT `heading` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `scope_name`='$scope' AND `deleted`=0 GROUP BY `heading` ORDER BY MIN(`sort_order`)");
			$heading = $heading_list->fetch_assoc();
			do {
				$us_pricing = mysqli_query($dbc, "SELECT `pricing` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `estimateid` > 0 AND `scope_name`='$scope' AND `heading`='".$heading['heading']."' AND `pricing` = 'usd_cpu' AND `deleted`=0 GROUP BY `pricing`")->num_rows; ?>
			<table class="table table-bordered">
				<tr>
					<td colspan="<?= $col_spanned+($us_pricing > 0 ? 1 : 0) ?>" data-title="Heading">
						<h3 class="no-margin">
                            <label class="col-sm-4 hide-titles-mob">Heading:</label>
                            <div class="col-sm-8"><input type="text" placeholder="Heading" name="heading" value="<?= empty($heading['heading']) ? 'Heading 1' : $heading['heading'] ?>" onchange="set_headings(this);" data-init="<?= $heading['heading'] ?>" class="form-control"></div>
                        </h3>
					</td>
					<td>
						<img src="../img/icons/drag_handle.png" class="inline-img pull-right heading-handle no-toggle" title="Drag">
						<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right cursor-hand" onclick="add_heading('<?= $scope ?>');">
						<img src="../img/remove.png" class="inline-img pull-right cursor-hand" onclick="rem_heading(this);">
						<?php if($_GET['tab'] != 'scope') { ?>
							<a href="estimate_scope_add.php?estimateid=<?= $estimateid ?>&scope=<?= $scope_id ?>&heading=<?= config_safe_str($heading['heading']) ?>" onclick="window.history.replaceState('','Software', '?edit=<?= $estimateid ?>&status=templates');overlayIFrameSlider(this.href, '75%', true, false, 'auto', true); return false;"><img class="inline-img pull-right no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit"></a>
						<?php } ?>
					</td>
				</tr>
				<tr class="hidden-sm hidden-xs">
					<?php $displayed_column = [];
                    foreach($heading_order as $order_info) {
						$order_info = explode('***',$order_info);
						if(!in_array($order_info[0],['UOM','Quantity','Description','Detail','Billing Frequency','Estimate Price','Total']) || in_array($order_info[0],$displayed_column)) {
							continue;
						}
                        $displayed_column[] = $order_info[0];
						echo "<th>".(empty($order_info[1]) ? $order_info[0] : $order_info[1])."</th>";
                        if($order_info[0] == 'Estimate Price' && $us_pricing > 0 && $us_rate_no_auto != 'true') {
                          echo "<th>USD Price</th>";
                        } else if($order_info[0] == 'Estimate Price' && $us_pricing > 0) {
                          echo "<th>CAD Price</th>";
                        }
					} ?>
					<th data-columns='<?= $columns ?>' data-width='1' style="width:8em;"></th>
				</tr>
				<?php $lines = mysqli_query($dbc, "SELECT * FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `estimateid` > 0 AND `scope_name`='$scope' AND `heading`='".$heading['heading']."' AND `deleted`=0 ORDER BY `sort_order`");
				$line = mysqli_fetch_array($lines);
				do {
					if(empty($line['src_table'])) { ?>
						<tr>
							<input type="hidden" name="scope_name" value="<?= $scope ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
							<input type="hidden" name="heading" value="<?= $heading['heading'] ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
							<input type="hidden" name="sort_order" value="<?= $line['sort_order'] ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
							<input type="hidden" name="deleted" value="<?= $line['deleted'] ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
							<td colspan="<?= $col_spanned+($us_pricing > 0 ? 1 : 0) ?>">
								<em>Please add details</em>
							</td>
							<td data-title="Function" align="center">
								<a href="estimate_scope_add.php?estimateid=<?= $estimateid ?>&scope=<?= $scope_id ?>&heading=<?= config_safe_str($heading['heading']) ?>" onclick="overlayIFrameSlider(this.href, '75%', true, false, 'auto', true); return false;"><img src="../img/icons/ROOK-add-icon.png" class="inline-img cursor-hand" width="20"></a>
							</td>
						</tr>
					<?php } else { ?>
						<tr>
							<input type="hidden" name="scope_name" value="<?= $scope ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
							<input type="hidden" name="heading" value="<?= $heading['heading'] ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
							<input type="hidden" name="sort_order" value="<?= $line['sort_order'] ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
							<?php if($line['src_table'] == 'notes') { ?>
								<td colspan="<?= $col_spanned+($us_pricing > 0 ? 1 : 0) ?>">
									<?= html_entity_decode($line['description']) ?>
								</td>
							<?php } else {
                                $displayed_column = [];
								foreach($heading_order as $order_info) {
									$order_info = explode('***',$order_info);
									if(!in_array($order_info[0],['UOM','Quantity','Description','Detail','Billing Frequency','Estimate Price','Total']) || in_array($order_info[0],$displayed_column)) {
										continue;
									}
                                    $displayed_column[] = $order_info[0];
									echo "<td data-title='".(empty($order_info[1]) ? $order_info[0] : $order_info[1])."'>";
									switch($order_info[0]) {
										case 'UOM': ?>
											<input type="text" name="uom" class="form-control" value="<?= $line['uom'] ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
											<?php break;
										case 'Quantity': ?>
											<input type="number" name="qty" class="form-control" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id" data-table="estimate_scope" value="<?= round($line['qty'],2) ?>" min="0" step="any">
											<?php break;
										case 'Description': ?>
											<?php if($line['src_table'] == 'miscellaneous') {
												echo $line['description'];
											} else {
												foreach($tiles as $label => $tile_name) {
													if($tile_name == $line['src_table']) {
														echo $label.': ';
													}
												}
												if($line['src_id'] > 0 && in_array($line['src_table'],['staff','clients','contractor','customer'])) {
													echo get_contact($dbc, $line['src_id']);
												} else {
													foreach($src_options as $option) {
														if($option['tile_name'] == $line['src_table'] && $option['id'] == $line['src_id']) {
															echo $option['label'];
														}
													}
												}
											} ?>
											<?php break;
										case 'Detail': ?>
											<input type="text" name="detail" class="form-control" value="<?= $line['detail'] ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
											<?php break;
										case 'Billing Frequency': ?>
											<select name="billing" class="chosen-select-deselect" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id"><option></option>
												<option <?=  $line['billing'] == 'Per Season' ? 'selected' : '' ?> value="Per Season">Per Season</option>
												<option <?=  $line['billing'] == 'Per Visit' ? 'selected' : '' ?> value="Per Visit">Per Visit</option>
												<option <?=  $line['billing'] == 'Per Hour' ? 'selected' : '' ?> value="Per Hour">Per Hour</option>
											</select>
											<?php break;
										/*case 'Cost': ?>
											<input type="text" name="cost" class="form-control" value="<?= $line['cost'] ?>" readonly data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
											<?php break;
										case 'Margin': ?>
											<input type="text" name="margin" class="form-control" value="<?= round($line['margin'],2) ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
											<?php break;
										case 'Profit': ?>
											<input type="text" name="profit" class="form-control" value="<?= $line['profit'] ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
											<?php break;*/
										case 'Estimate Price': ?>
											<input type="text" name="price" class="form-control" value="<?= $line['pricing'] != 'usd_cpu' || $line['price'] > 0 ? $line['price'] : number_format($line['cost'] * ($us_rate_no_auto == 'true' ? 1 : $us_rate),2,'.','') ?>" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
											<?php if($us_pricing > 0) { ?>
												</td><td data-title="<?= $us_rate_no_auto == 'true' ? 'CAD' : 'USD' ?> Pricing">
												<?php if($line['pricing'] == 'usd_cpu' && $us_rate_no_auto == 'true') { ?>
													Approx $<?= number_format($line['cost'] * $us_rate,2,'.','') ?> CAD
												<?php } else if($line['pricing'] == 'usd_cpu') { ?>
													$<?= number_format($line['cost'],2) ?> @<?= round($us_rate,2) ?> ($<?= number_format($line['cost'] * $us_rate,2,'.','') ?> CAD)
													<?php if(!($line['price'] > 0)) {
														$line['price'] = $line['cost'] * $us_rate;
													}
												} else {
													echo '';
												}
											}
											break;
										case 'Total': ?>
											<input type="text" name="retail" class="form-control" value="<?= round($line['retail']) != round($line['qty'] * $line['price']) ? number_format($line['qty'] * $line['price'],2,'.','') : $line['retail'] ?>" readonly data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
											<?php break;
									}
									echo "</td>";
								}
							} ?>
							<td data-title="Function" align="center">
								<a href="" class="breakdown active" <?= $line['src_table'] == 'miscellaneous' ? '' : 'style="display: none;"' ?> onclick="return false;"><small>+ BREAKDOWN</small></a>
								<img src="../img/remove.png" class="inline-img cursor-hand" onclick="remove_line(this);" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id" name="deleted" width="20">
								<a href="estimate_scope_add.php?estimateid=<?= $estimateid ?>&scope=<?= $scope_id ?>&heading=<?= config_safe_str($heading['heading']) ?>" onclick="overlayIFrameSlider(this.href, '75%', true, false, 'auto', true); return false;"><img src="../img/icons/ROOK-add-icon.png" class="inline-img cursor-hand" width="20"></a>
								<img src="../img/icons/drag_handle.png" class="inline-img cursor-hand line-handle no-toggle" data-table="estimate_scope" data-id="<?= $line['id'] ?>" data-id-field="id" width="20" title="Drag">
							</td>
						</tr>
					<?php } ?>
				<?php } while($line = mysqli_fetch_array($lines)); ?>
			</table>
			<?php } while($heading = $heading_list->fetch_assoc()); ?>
		</div>
		<hr>
	</div>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_headings.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>