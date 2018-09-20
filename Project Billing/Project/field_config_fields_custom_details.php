<?php include_once('../include.php');
if($_GET['new_detail'] == 1) {
	$custom_tab['headings'] = [''=>['fields'=>['']]];
} ?>
<div class="custom_detail_block">
	<div class="col-sm-3"><img src="../img/remove.png" class="inline-img <?= $custom_tab['disabled'] ? 'field-disabled' : '' ?>" onclick="removeCustomTab(this);"> <input name="custom_detail_tab[]" placeholder="Tab Label" <?= $custom_tab['disabled'] ? 'disabled' : '' ?> type="text" class="form-control inline <?= $custom_tab['disabled'] ? 'field-disabled' : '' ?>" value="<?= $tab_name ?>"></div>
	<label class="col-sm-1" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<?php foreach($custom_tab['headings'] as $heading_name => $custom_detail) { ?>
			<div class="custom_heading_block">
				<img src="../img/remove.png" class="inline-img <?= $custom_detail['disabled'] ? 'field-disabled' : '' ?>" onclick="removeCustomHeading(this);"> <input name="custom_detail_heading[]" placeholder="Heading Label" <?= $custom_detail['disabled'] ? 'disabled' : '' ?> type="text" class="form-control inline <?= $custom_detail['disabled'] ? 'field-disabled' : '' ?>" value="<?= $heading_name ?>">
				<div class="block-group">
					<?php foreach($custom_detail['fields'] as $custom_field) { ?>
						<div class="custom_field_block">
							<div class="col-sm-6">
								<input name="custom_detail_field[]" placeholder="Field Label" <?= $custom_field['disabled'] ? 'disabled' : '' ?> type="text" class="form-control <?= $custom_field['disabled'] ? 'field-disabled' : '' ?>" value="<?= $custom_field['label'] ?>">
							</div>
							<div class="col-sm-4 <?= $custom_field['disabled'] ? 'field-disabled' : '' ?>">
								<select name="custom_detail_fieldtype[]" data-placeholder="Select a Field Type" <?= $custom_field['disabled'] ? 'disabled' : '' ?> class="chosen-select-deselect form-control">
									<option value="textarea" <?= empty($custom_field['type']) || $custom_field['type'] == 'textarea' ? 'selected' : '' ?>>Text Area</option>
									<option value="uploader" <?= $custom_field['type'] == 'uploader' ? 'selected' : '' ?>>Uploader</option>
								</select>
							</div>
							<div class="col-sm-2 pull-right">
								<img src="../img/remove.png" class="inline-img <?= $custom_field['disabled'] ? 'field-disabled' : '' ?>" onclick="removeCustomField(this);"><img src="../img/icons/ROOK-add-icon.png" class="inline-img" onclick="addCustomField(this);">
							</div>
							<div class="clearfix"></div>
						</div>
						<div class="clearfix"></div>
					<?php } ?>
					<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addCustomHeading(this);">
					<div class="clearfix"></div>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="clearfix"></div>
</div>