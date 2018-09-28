<?php include('../Rate Card/line_types.php');
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
$heading_order = explode('#*#', get_config($dbc, 'estimate_field_order'));
$estimateid = filter_var($_GET['estimate'],FILTER_SANITIZE_STRING);
$headings = [];
$query = mysqli_query($dbc, "SELECT `heading` FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 GROUP BY `heading` ORDER BY MIN(`sort_order`)");
if(mysqli_num_rows($query) > 0) {
	while($row = mysqli_fetch_array($query)) {
		$headings[preg_replace('/[^a-z]*/','',strtolower($row[0]))] = $row[0];
	}
} ?>
<h3><a href="../Estimate/estimates.php?edit=<?= $estimateid ?>&tab=scope">Estimate #<?= $estimateid.' '.mysqli_fetch_array(mysqli_query($dbc, "SELECT `estimate_name` FROM `estimate` WHERE `estimateid`='$estimateid'"))['estimate_name'] ?></a> Scope</h3>
<div id="no-more-tables">
	<?php foreach($headings as $head_id => $heading) { ?>
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
							<th style="width: 10%;">Tile</th>
							<th style="width: 8%;">UOM</th>
							<th style="width: 8%;">Qty</th>
							<th style="width: 19%;">Description</th>
							<th style="width: 8%;">Cost</th>
							<th style="width: 8%;">% Margin</th>
							<th style="width: 8%;">$ Profit</th>
							<th style="width: 8%;">Total</th>
							<th style="width: 8%;">Final Retail Price</th>
						</tr>
						<?php $lines = mysqli_query($dbc, "SELECT * FROM `estimate_scope` WHERE `estimateid`='$estimateid' AND `deleted`=0 AND `heading`='$heading' ORDER BY `sort_order`");
						while($line = mysqli_fetch_array($lines)) { ?>
							<tr>
								<input type="hidden" name="heading" value="<?= $heading ?>" data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
								<input type="hidden" name="sort_order" value="<?= $line['sort_order'] ?>" data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id">
								<td data-title="Tile" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><select name="src_table" class="chosen-select-deselect form-control" data-id="<?= $line['id'] ?>" data-id-field="id" data-table="project_scope">
										<option></option>
										<?php foreach($tiles as $label => $tile_name) { ?>
											<option <?= $line['src_table'] == $tile_name ? 'selected' : '' ?> value="<?= $tile_name ?>"><?= $label ?></option>
										<?php } ?>
									</select></td>
								<td data-title="Unit of Measure" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><input type="text" name="uom" class="form-control" value="<?= $line['uom'] ?>" data-id="<?= $line['id'] ?>" data-id-field="id"></td>
								<td data-title="Quantity" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><input type="number" name="qty" class="form-control" data-id="<?= $line['id'] ?>" data-id-field="id" data-table="project_scope" value="<?= $line['qty'] ?>" min="0" step="0.0001"></td>
								<td data-title="Description" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>>
									<input type="text" name="description" class="form-control" value="<?= $line['description'] ?>" data-id="<?= $line['id'] ?>" data-id-field="id" data-table="project_scope" style="<?= $line['src_table'] == 'miscellaneous' ? '' : 'display: none;' ?>">
									<div class="select_div" <?= $line['src_table'] == 'miscellaneous' ? 'style="display: none;"' : '' ?>><select name="src_id" class="chosen-select-deselect form-control" data-id="<?= $line['id'] ?>" data-id-field="id" data-table="project_scope">
										<option></option>
										<?php foreach($src_options as $option) {
											if(strtolower($option['tile_name']) == strtolower($line['src_table'])) { ?>
												<option <?= $option['id'] == $line['src_id'] ? 'selected' : '' ?> value="<?= $option['id'] ?>"><?= $option['label'] ?></option>
											<?php }
										} ?>
									</select></div>
								</td>
								<td data-title="Cost" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><input type="text" name="cost" class="form-control" value="<?= $line['cost'] ?>" readonly data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id"></td>
								<td data-title="Margin" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><input type="text" name="margin" class="form-control" value="<?= $line['margin'] ?>" data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id"></td>
								<td data-title="Profit" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><input type="text" name="profit" class="form-control" value="<?= $line['profit'] ?>" data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id"></td>
								<td data-title="Total" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><input type="text" name="price" class="form-control" value="<?= $line['price'] ?>" data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id"></td>
								<td data-title="Final Retail Price" <?= !($security['edit'] > 0) ? 'class="readonly-block"' : '' ?>><input type="text" name="retail" class="form-control" value="<?= $line['retail'] ?>" readonly data-table="project_scope" data-id="<?= $line['id'] ?>" data-id-field="id"></td>
							</tr>
						<?php } ?>
					</table>
				</div>
			</div>
		</div>
	<?php } ?>
	<?php include('next_buttons.php'); ?>
</div>