<?php if($_GET['page'] > 0 && $_GET['id'] > 0) {
	$template = $dbc->query("SELECT * FROM `ticket_pdf` WHERE `id`='{$_GET['id']}'")->fetch_assoc();
	$page = explode('#*#',$template['pages'])[$_GET['page'] - 1];
	$left_multiply = 2.2;
	$top_multiply = 2.819;
	$width_multiply = 2.177;
	$height_multiply = 2.75;
	if($template['page_orientation'] == 'L') {
		$left_multiply = 2.819;
		$top_multiply = 2.2;
		$width_multiply = 2.75;
		$height_multiply = 2.177;
	} ?>
	<script>
	$(document).ready(function() {
		$('.field_pos [data-id]').resizable({
			handles: 's,e,se',
			stop: function() {
				$.post('ticket_ajax_all.php?action=template_field', {
					id: $(this).data('id'),
					field: 'width',
					value: ($(this).outerWidth() / ($('.field_pos img').innerWidth() - 5) * 100 * <?= $width_multiply ?>)
				});
				$.post('ticket_ajax_all.php?action=template_field', {
					id: $(this).data('id'),
					field: 'height',
					value: ($(this).outerHeight() / ($('.field_pos').innerHeight() - 3) * 100 * <?= $height_multiply ?>)
				});
			}
		});
		$('.field_pos').sortable({
			items: '[data-id]',
			beforeStop: function(e, target) {
				var field = target.item;
				$(field).css('left',(target.offset.left - $('.field_pos').offset().left)+'px');
				$(field).css('top',(target.offset.top - $('.field_pos').offset().top)+'px');
				$.post('ticket_ajax_all.php?action=template_field', {
					id: $(field).data('id'),
					field: 'x',
					value: (target.offset.left - $('.field_pos').offset().left) / ($('.field_pos').innerWidth() + 5) * 100 * <?= $left_multiply ?>
				});
				$.post('ticket_ajax_all.php?action=template_field', {
					id: $(field).data('id'),
					field: 'y',
					value: (target.offset.top - $('.field_pos').offset().top) / ($('.field_pos').innerHeight() + 3) * 100 * <?= $top_multiply ?>
				});
			}
		});
	});
	function addField(pdf, page) {
		$.post('ticket_ajax_all.php?action=template_add_field', {
			id: pdf,
			page: page
		}, function() {
			window.location.reload();
		});
	}
	function getFieldDetails(id) {
		$('.field_details').load('field_config_pdf_field.php?id='+id);
	}
	function saveField() {
		
	}
	</script>
	<div class="col-sm-6">
		<h3>Configure Fields for <?= $template['pdf_name'] ?></h3>
		<div class="col-sm-12 field_details">Select a field to configure the details.</div>
		<a class="btn brand-btn pull-left" href="?settings=forms&id=<?= $_GET['id'] ?>">Back to Form</a>
		<button class="btn brand-btn pull-left" onclick="addField('<?= $_GET['id'] ?>','<?= $_GET['page'] ?>'); return false;">Add New Field</button>
	</div>
	<div class="col-sm-6 field_pos">
		<img src="pdf_contents/<?= $page ?>" style="width: 100%;">
		<?php $fields = $dbc->query("SELECT * FROM `ticket_pdf_fields` WHERE `page`='{$_GET['page']}' AND `pdf_type`='{$_GET['id']}'");
		while($field = $fields->fetch_assoc()) { ?>
			<div data-id="<?= $field['id'] ?>" onclick="getFieldDetails(<?= $field['id'] ?>);" class="cursor-hand" style="border:solid 1px red; background-color: rgb(220,200,200); overflow:hidden; position:absolute; top:calc(3px + <?= $field['y'] / $top_multiply ?>%); left:calc(5px + <?= $field['x'] / $left_multiply ?>%); width:<?= $field['width'] / $width_multiply ?>%; height:<?= $field['height'] / $height_multiply ?>%; font-size: 7pt;"><?= $field['field_label'] ?></div>
		<?php } ?>
	</div>
<?php } else { ?>
	<script>
	$(document).ready(function() {
		$('[data-table]').off('change',saveTemplate).change(saveTemplate);
	});
	var templateID = '<?= $_GET['id'] ?>';
	function saveTemplate() {
		if(this.type == 'file') {
			var file = new FormData();
			file.append('file', this.files[0]);
			file.append('field', this.name);
			file.append('id',templateID);
			$.ajax({
				url: 'ticket_ajax_all.php?action=template_file',
				method: 'POST',
				processData: false,
				contentType: false,
				data: file,
				success: function(response) {
				$('.page_output').html(response.split('#*#')[0]);
				if(response.split('#*#')[1] > 0) {
					templateID = response.split('#*#')[1];
				}
				}
			});
			this.value = '';
		} else {
			var field = this.name;
			$.post('ticket_ajax_all.php?action=template_setting',{
				id: templateID,
				field: this.name,
				value: this.value
			}, function(response) {
				if(response > 0) {
					templateID = response;
				} else if(field == 'deleted') {
					window.location.replace('?settings=forms');
				}
			});
		}
	}
	</script>
	<div class="form-group">
		<label class="col-sm-4 control-label">Template:</label>
		<div class="col-sm-8">
			<select class="chosen-select-deselect" data-placeholder="Select Template" name="templateid" onchange="if(this.value!='<?= $_GET['id'] ?>') { window.location.replace('?settings=forms&id='+(this.value > 0 ? this.value : '')); }"><option />
				<?php $templates = $dbc->query("SELECT `id`, `pdf_name` FROM `ticket_pdf` WHERE `deleted`=0 ORDER BY `pdf_name`");
				while($template = $templates->fetch_assoc()) { ?>
					<option <?= $template['id'] == $_GET['id'] ? 'selected' : '' ?> value="<?= $template['id'] ?>"><?= $template['pdf_name'] ?></option>
				<?php } ?>
				<option <?= $_GET['id'] == '' ? 'selected' : '' ?> value="NEW">New Template</option>
			</select>
		</div>
	</div>
	<?php $template = [];
	if($_GET['id'] > 0) {
		$template = $dbc->query("SELECT * FROM `ticket_pdf` WHERE `id`='{$_GET['id']}'")->fetch_assoc();
	} ?>
	<div class="form-group">
		<label class="col-sm-4 control-label">PDF Name:</label>
		<div class="col-sm-<?= $_GET['id'] > 0 ? 7 : 8 ?>">
			<input type="text" class="form-control" data-table="ticket_pdf" name="pdf_name" value="<?= $template['pdf_name'] ?>">
		</div>
		<?php if($_GET['id'] > 0) { ?>
			<div class="col-sm-1">
				<label class="form-checkbox any-width cursor-hand"><input type="checkbox" class="form-control" data-table="ticket_pdf" name="deleted" value="1" style="display:none;"><img src="../img/icons/ROOK-trash-icon.png" title="Archive Form" class="inline-img no-toggle"></label>
			</div>
		<?php } ?>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Page Orientation:</label>
		<div class="col-sm-8">
			<select name="page_orientation" data-table="ticket_pdf" class="chosen-select-deselect form-control">
				<option value="P" <?= $template['page_orientation'] == 'P' || empty($template['page_orientation']) ? 'selected' : '' ?>>Portrait</option>
				<option value="L" <?= $template['page_orientation'] == 'L' ? 'selected' : '' ?>>Landscape</option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Display Mode:</label>
		<div class="col-sm-8">
			<label class="control-checkbox"><input type="radio" data-table="ticket_pdf" name="target" value="slider" <?= $template['target'] == 'slider' ? 'checked' : '' ?>> Slider Window</label>
			<label class="control-checkbox"><input type="radio" data-table="ticket_pdf" name="target" value="new_tab" <?= $template['target'] == 'new_tab' ? 'checked' : '' ?>> New Tab</label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?= TICKET_NOUN ?> Tab:</label>
		<div class="col-sm-8">
			<select class="chosen-select-deselect" data-table="ticket_pdf" name="ticket_types" data-placeholder="Select <?= TICKET_NOUN ?> Tab"><option />
				<?php foreach($ticket_tabs as $type_name) { ?>
					<option <?= $type_name == $template['ticket_types'] ? 'selected' : '' ?> value="<?= $type_name ?>"><?= $type_name ?></option>
				<?php } ?>
				<option <?= $template['ticket_types'] == '' || $template['ticket_types'] == 'ALL' ? 'selected' : '' ?> value="ALL">All <?= TICKET_TILE ?></option>
			</select>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Show on Dashboard:</label>
		<div class="col-sm-8">
			<label class="control-checkbox"><input type="radio" data-table="ticket_pdf" name="dashboard" value="" <?= $template['dashboard'] == '' ? 'checked' : '' ?>> Yes</label>
			<label class="control-checkbox"><input type="radio" data-table="ticket_pdf" name="dashboard" value="hidden" <?= $template['dashboard'] == 'hidden' ? 'checked' : '' ?>> No</label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Show Each Revision:</label>
		<div class="col-sm-8">
			<label class="control-checkbox"><input type="radio" data-table="ticket_pdf" name="revisions" value="1" <?= $template['revisions'] == '1' ? 'checked' : '' ?>> Yes</label>
			<label class="control-checkbox"><input type="radio" data-table="ticket_pdf" name="revisions" value="" <?= $template['revisions'] == '1' ? '' : 'checked' ?>> No</label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label">Pages:</label>
		<div class="col-sm-8">
			<input type="file" data-table="ticket_pdf" data-concat="#*#" name="pages">
		</div>
		<div class="col-sm-12 page_output">
			<?php foreach(array_filter(explode('#*#',$template['pages'])) as $i => $page) { ?>
				<a href="?settings=forms&id=<?= $_GET['id'] ?>&page=<?= $i + 1 ?>"><img src="pdf_contents/<?= $page ?>" style="width: 30%; margin: 2em;"></a>
			<?php } ?>
		</div>
	</div>
<?php } ?>