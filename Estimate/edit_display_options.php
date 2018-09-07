<?php include_once('../include.php');
checkAuthorised('estimate');
if(!isset($estimate)) {
	$estimateid = filter_var($estimateid,FILTER_SANITIZE_STRING);
	$estimate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'"));
} ?>
<script>
</script>
<div class="form-horizontal col-sm-12" data-tab-name="display_options">
	<h3>Options</h3>
	<div class="form-group">
		<label class="col-sm-4">PDF Style:</label>
		<div class="col-sm-8">
			<select class="chosen-select-deselect" data-placeholder="Select Style" name="pdf_style" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>"><option />
				<?php $pdf_styles = mysqli_query($dbc, "SELECT `pdfsettingid`,`style_name`,`style` FROM `estimate_pdf_setting` WHERE `estimateid` IS NULL AND `deleted`=0 ORDER BY `style_name`");
				while($pdf_style = mysqli_fetch_assoc($pdf_styles)) { ?>
					<option <?= $estimate['pdf_style'] == $pdf_style['pdfsettingid'] ? 'selected' : '' ?> value="<?= $pdf_style['pdfsettingid'] ?>"><?= $pdf_style['style_name'] ?></option>
				<?php } ?>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Page Order:</label>
		<div class="col-sm-8">
			<button class="btn brand-btn pull-right" onclick="add_page(); return false;">Add Content Page</button>
			<div class="clearfix"></div>
			<input type="hidden" name="page_order" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" value="<?= $estimate['page_order'] ?>">
			<ul class="connectedChecklist page_order">
				<?php foreach(explode('#*#',$estimate['page_order'] != '' ? $estimate['page_order'] : 'table_of_contents#*#estimate_scope') as $page_id) {
					if($page_id == 'table_of_contents') { ?>
						<li data-value="table_of_contents">Table of Contents<img class="inline-img pull-right line_handle no-toggle" src="../img/icons/drag_handle.png" title="Drag"></li>
					<?php } else if($page_id == 'estimate_scope') { ?>
						<li data-value="estimate_scope"><?= rtrim(ESTIMATE_TILE, 's') ?> Scope<img class="inline-img pull-right line_handle no-toggle" src="../img/icons/drag_handle.png" title="Drag"></li>
					<?php } else { ?>
						<li data-value="<?= $page_id ?>"><a href="edit_content_page.php?id=<?= explode('_',$page_id)[1] ?>" onclick="overlayIFrameSlider(this.href, 'auto', false, true); return false;">Content Page<img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit"></a><img class="inline-img pull-right line_handle no-toggle" src="../img/icons/drag_handle.png" title="Drag"></li>
					<?php }
				} ?>
			</ul>
			<script>
			function add_page() {
				$.get('estimates_ajax.php?action=addContentPage', function(response) {
					$('.page_order').append('<li data-value="page_'+response+'"><a href="edit_content_page.php?id='+response+'" onclick="overlayIFrameSlider(this.href, \'auto\', false, true); return false;">Content Page<img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit"></a><img class="inline-img pull-right line_handle no-toggle" src="../img/icons/drag_handle.png" title="Drag"></li>');
					save_pages();
				});
			}
			function save_pages() {
				var str = [];
				$('.connectedChecklist.page_order li').each(function() {
					str.push($(this).data('value'));
				});
				$('[name=page_order]').val(str.join('#*#')).change();
			}
			$(document).ready(function() {
				$('.connectedChecklist.page_order').sortable({
					handle: '.line_handle',
					items: 'li',
					update: save_pages
				})
			});
			</script>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4">Include Page Numbers:</label>
		<div class="col-sm-8">
			<input type="hidden" name="page_numbers" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" value="<?= $estimate['page_numbers'] ?>">
			<a class="<?= $estimate['page_numbers'] == 'none' || $estimate['page_numbers'] == '' ? 'active' : '' ?> col-sm-2 block-item text-center" href="" onclick="$('[name=page_numbers]').val('none').change(); $('.active').removeClass('active'); $(this).addClass('active'); return false;"><img src="../img/icons/cancel.png" class="inline-img text-lg"></a>
			<a class="<?= $estimate['page_numbers'] == 'top_cover' ? 'active' : '' ?> col-sm-2 block-item text-center" href="" onclick="$('[name=page_numbers]').val('top_cover').change(); $('.active').removeClass('active'); $(this).addClass('active'); return false;"><img src="../img/top_cover.png" class="inline-img text-lg"></a>
			<a class="<?= $estimate['page_numbers'] == 'top_main' ? 'active' : '' ?> col-sm-2 block-item text-center" href="" onclick="$('[name=page_numbers]').val('top_main').change(); $('.active').removeClass('active'); $(this).addClass('active'); return false;"><img src="../img/top_main.png" class="inline-img text-lg"></a>
			<a class="<?= $estimate['page_numbers'] == 'bottom_cover' ? 'active' : '' ?> col-sm-2 block-item text-center" href="" onclick="$('[name=page_numbers]').val('bottom_cover').change(); $('.active').removeClass('active'); $(this).addClass('active'); return false;"><img src="../img/bottom_cover.png" class="inline-img text-lg"></a>
			<a class="<?= $estimate['page_numbers'] == 'bottom_main' ? 'active' : '' ?> col-sm-2 block-item text-center" href="" onclick="$('[name=page_numbers]').val('bottom_main').change(); $('.active').removeClass('active'); $(this).addClass('active'); return false;"><img src="../img/bottom_main.png" class="inline-img text-lg"></a>
		</div>
	</div>
	<?php if(in_array('Multiples',$config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Multiples per Line:</label>
			<div class="col-sm-8">
				<input type="number" class="form-control" name="quote_multiple" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" value="<?= $estimate['quote_multiple'] ?>" min=1 step=1>
			</div>
		</div>
	<?php } ?>
	<?php if(in_array('Multiples',$config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Price Display Options:</label>
			<div class="col-sm-8">
				<label><input type="radio" name="quote_mode" <?= !in_array($estimate['quote_mode'],['Category','Total','None']) ? 'checked' : '' ?> value="All" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" style="height:1.5em; width:1.5em;"> Each Line Total</label>
				<label><input type="radio" name="quote_mode" <?= $estimate['quote_mode'] == 'Category' ? 'checked' : '' ?> value="Category" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" style="height:1.5em; width:1.5em;"> Category Totals</label>
				<label><input type="radio" name="quote_mode" <?= $estimate['quote_mode'] == 'Total' ? 'checked' : '' ?> value="Total" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" style="height:1.5em; width:1.5em;"> <?= ESTIMATE_TILE ?> Total Only</label>
				<label><input type="radio" name="quote_mode" <?= $estimate['quote_mode'] == 'None' ? 'checked' : '' ?> value="None" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" style="height:1.5em; width:1.5em;"> No Prices</label>
			</div>
		</div>
	<?php } ?>
    <hr />
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_display_options.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>