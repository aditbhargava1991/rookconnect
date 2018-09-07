<?php include_once('../include.php');
checkAuthorised('estimate');
$estimateid = filter_var($_GET['edit'],FILTER_SANITIZE_STRING);
$estimate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'")); ?>
<script>
$(document).ready(function() {
	$('input,select').change(saveField).keyup(syncUnsaved);
	$('.style-select').click(function() {
        $.ajax({
			url: 'estimates_ajax.php?action=estimate_fields',
			method: 'POST',
			dataType: 'html',
			data: {
				id: $(this).data('id'),
				id_field: $(this).data('id-field'),
				table: $(this).data('table'),
				field: 'pdf_style',
				value: $(this).data('value'),
				estimate: $('[name=estimateid]').val()
			},
			success: function(response) {}
		});
    });
});

function saveField() {
	syncUnsaved(this.name);
	if($(this).is('[data-table]')) {
		var result = this.value;
		var name = this.name;
		var table_name = $(this).data('table');
		var block = $(this).closest('.multi-block');
		if(name.substr(-2) == '[]') {
			result = [];
			$('[name="'+name+'"]').each(function() {
				result.push(this.value);
			});
		}
		if(this.type == 'checkbox' && !this.checked) {
			result = 0;
		}
		syncSaving();
		$.ajax({
			url: 'estimates_ajax.php?action=estimate_fields',
			method: 'POST',
			dataType: 'html',
			data: {
				id: $(this).data('id'),
				id_field: $(this).data('id-field'),
				table: table_name,
				field: name.replace('[]',''),
				value: result,
				estimate: $('[name=estimateid]').val()
			},
			success: function(response) {
				if(table_name == 'estimate' && '<?= $_GET['edit'] ?>' == 'new' && response > 0) {
					$('a.updateable');
					$('[name=estimateid]').val(response);
				} else if(block.length > 0 && response > 0) {
					block.find('[data-id]').not('[data-table=estimate]').data('id',response);
				}
				syncDone(name);
			}
		});
	}
}
</script>
<div class="form-horizontal col-sm-12">
	<h3>Options</h3>
	<div class="form-group">
		<label class="col-sm-2">PDF Style:</label>
		<div class="col-sm-10">
            <div class="row"><?php
                $pdf_styles = mysqli_query($dbc, "SELECT `pdfsettingid`,`style_name`,`style` FROM `estimate_pdf_setting` WHERE `estimateid` IS NULL AND `deleted`=0 ORDER BY `style_name`");
                while($pdf_style = mysqli_fetch_assoc($pdf_styles)) {
                    echo '<div class="col-sm-4 style-select cursor-hand'. ($estimate['pdf_style'] == $pdf_style['pdfsettingid'] ? ' theme-color-border-2x' : '') .'" data-name="pdf_style" data-value="'. $pdf_style['pdfsettingid'] .'" data-table="estimate" data-id-field="estimateid" data-id="'.$estimateid.'" onclick="$(\'.style-select\').removeClass(\'theme-color-border-2x\'); $(this).addClass(\'theme-color-border-2x\'); return false;">';
                        echo '<b>Design: '.$pdf_style['style_name'].'</b><br /><br />';
                        echo '<div style="height:300px; overflow-y:scroll;">';
                            $_GET['style'] = $pdf_style['pdfsettingid'];
                            include('estimate_design_output.php');
                            echo str_replace('[PAGE #]', '<span style="font-size:0.3em">[PAGE #]</span>', $header_html).$html.str_replace('[PAGE #]', '<span style="font-size:0.3em">[PAGE #]</span>', $footer_html);
                        echo '</div>';
                    echo '</div>';
                } ?>
                <div class="clearfix"></div>
            </div>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-2">Page Order:</label>
		<div class="col-sm-10">
			<button class="btn brand-btn pull-right gap-bottom" onclick="add_page(); return false;">Add Content Page</button>
			<div class="clearfix"></div>
			<input type="hidden" name="page_order" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" value="<?= $estimate['page_order'] ?>">
			<ul class="connectedChecklist page_order full-width">
				<?php foreach(explode('#*#',$estimate['page_order'] != '' ? $estimate['page_order'] : 'table_of_contents#*#estimate_scope') as $page_id) {
					if($page_id == 'table_of_contents') { ?>
						<li data-value="table_of_contents">Table of Contents<img class="inline-img pull-right line_handle no-toggle" src="../img/icons/drag_handle.png" title="Drag"></li>
					<?php } else if($page_id == 'estimate_scope') { ?>
						<li data-value="estimate_scope"><?= rtrim(ESTIMATE_TILE, 's') ?> Scope<img class="inline-img pull-right line_handle no-toggle" src="../img/icons/drag_handle.png" title="Drag"></li>
					<?php } else { ?>
						<li data-value="<?= $page_id ?>"><a href="edit_content_page.php?id=<?= explode('_',$page_id)[1] ?>" onclick="overlayIFrameSlider(this.href, 'auto', false, true, $('#estimates_main').height()+20); return false;">Content Page<img class="inline-img no-toggle" src="../img/icons/ROOK-edit-icon.png" title="Edit"></a><img class="inline-img pull-right line_handle no-toggle" src="../img/icons/drag_handle.png" title="Drag"></li>
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
		<label class="col-sm-2">Include Page Numbers:</label>
		<div class="col-sm-10">
			<input type="hidden" id="page_numbers" name="page_numbers" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" value="<?= $estimate['page_numbers'] ?>">
			<a class="<?= $estimate['page_numbers'] == 'none' || $estimate['page_numbers'] == '' ? 'active' : '' ?> col-sm-2 block-item text-center" href="" onclick="$('[name=page_numbers]').val('none').change(); $('.active').removeClass('active'); $(this).addClass('active'); return false;"><img src="../img/icons/cancel.png" class="inline-img text-lg"></a>
			<a class="<?= $estimate['page_numbers'] == 'top_cover' ? 'active' : '' ?> col-sm-2 block-item text-center" href="" onclick="$('[name=page_numbers]').val('top_cover').change(); $('.active').removeClass('active'); $(this).addClass('active'); return false;" style="border-left:0 none;"><img src="../img/top_cover.png" class="inline-img text-lg"></a>
			<a class="<?= $estimate['page_numbers'] == 'top_main' ? 'active' : '' ?> col-sm-2 block-item text-center" href="" onclick="$('[name=page_numbers]').val('top_main').change(); $('.active').removeClass('active'); $(this).addClass('active'); return false;" style="border-left:0 none;"><img src="../img/top_main.png" class="inline-img text-lg"></a>
			<a class="<?= $estimate['page_numbers'] == 'bottom_cover' ? 'active' : '' ?> col-sm-2 block-item text-center" href="" onclick="$('[name=page_numbers]').val('bottom_cover').change(); $('.active').removeClass('active'); $(this).addClass('active'); return false;" style="border-left:0 none;"><img src="../img/bottom_cover.png" class="inline-img text-lg"></a>
			<a class="<?= $estimate['page_numbers'] == 'bottom_main' ? 'active' : '' ?> col-sm-2 block-item text-center" href="" onclick="$('[name=page_numbers]').val('bottom_main').change(); $('.active').removeClass('active'); $(this).addClass('active'); return false;" style="border-left:0 none;"><img src="../img/bottom_main.png" class="inline-img text-lg"></a>
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
			<label class="col-sm-2">Price Display Options:</label>
			<div class="col-sm-10">
				<label><input type="radio" name="quote_mode" <?= !in_array($estimate['quote_mode'],['Category','Total','None']) ? 'checked' : '' ?> value="All" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" style="height:1.5em; width:1.5em;"> Each Line Total</label>
				<label><input type="radio" name="quote_mode" <?= $estimate['quote_mode'] == 'Category' ? 'checked' : '' ?> value="Category" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" style="height:1.5em; width:1.5em;"> Category Totals</label>
				<label><input type="radio" name="quote_mode" <?= $estimate['quote_mode'] == 'Total' ? 'checked' : '' ?> value="Total" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" style="height:1.5em; width:1.5em;"> Estimate Total Only</label>
				<label><input type="radio" name="quote_mode" <?= $estimate['quote_mode'] == 'None' ? 'checked' : '' ?> value="None" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" style="height:1.5em; width:1.5em;"> No Prices</label>
			</div>
		</div>
	<?php } ?>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_display_options.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>