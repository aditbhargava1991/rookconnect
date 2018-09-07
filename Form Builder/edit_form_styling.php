<?php //Form Builder Styling
$default_settings = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `field_config_user_forms`"));

$header = !empty($form['header']) ? $form['header'] : $default_settings['default_header'];
$header_logo = !empty($form['header_logo']) ? $form['header_logo'] : $default_settings['default_head_logo'];
$header_align = !empty($form['header_align']) ? $form['header_align'] : $default_settings['default_head_align'];
$header_font = !empty($form['header_font']) ? $form['header_font'] : $default_settings['default_head_font'];
$header_size = !empty($form['header_size']) ? $form['header_size'] : $default_settings['default_head_size'];
$header_color = !empty($form['header_color']) ? $form['header_color'] : $default_settings['default_head_color'];
$header_styling = !empty($form['header_styling']) ? $form['header_styling'] : $default_settings['default_head_styling'];
$header_skip_first_page = $form['header_skip_first_page'];

$footer = !empty($form['footer']) ? $form['footer'] : $default_settings['default_footer'];
$footer_logo = !empty($form['footer_logo']) ? $form['footer_logo'] : $default_settings['default_foot_logo'];
$footer_align = !empty($form['footer_align']) ? $form['footer_align'] : $default_settings['default_foot_align'];
$footer_font = !empty($form['footer_font']) ? $form['footer_font'] : $default_settings['default_foot_font'];
$footer_size = !empty($form['footer_size']) ? $form['footer_size'] : $default_settings['default_foot_size'];
$footer_color = !empty($form['footer_color']) ? $form['footer_color'] : $default_settings['default_foot_color'];
$footer_styling = !empty($form['footer_styling']) ? $form['footer_styling'] : $default_settings['default_foot_styling'];

$section_heading_font = !empty($form['section_heading_font']) ? $form['section_heading_font'] : $default_settings['default_section_heading_font'];
$section_heading_size = !empty($form['section_heading_size']) ? $form['section_heading_size'] : $default_settings['default_section_heading_size'];
$section_heading_color = !empty($form['section_heading_color']) ? $form['section_heading_color'] : $default_settings['default_section_heading_color'];
$section_heading_styling = !empty($form['section_heading_styling']) ? $form['section_heading_styling'] : $default_settings['default_section_heading_styling'];

$body_heading_font = !empty($form['body_heading_font']) ? $form['body_heading_font'] : $default_settings['default_body_heading_font'];
$body_heading_size = !empty($form['body_heading_size']) ? $form['body_heading_size'] : $default_settings['default_body_heading_size'];
$body_heading_color = !empty($form['body_heading_color']) ? $form['body_heading_color'] : $default_settings['default_body_heading_color'];
$body_heading_styling = !empty($form['body_heading_styling']) ? $form['body_heading_styling'] : $default_settings['default_body_heading_styling'];

$body_font = !empty($form['font']) ? $form['font'] : $default_settings['default_font'];
$body_size = !empty($form['body_size']) ? $form['body_size'] : $default_settings['default_body_size'];
$body_color = !empty($form['body_color']) ? $form['body_color'] : $default_settings['default_body_color'];
$body_styling = !empty($form['body_styling']) ? $form['body_styling'] : $default_settings['default_body_styling'];

$page_format = !empty($form['page_format']) ? $form['page_format'] : $default_settings['default_page_format'];

$advanced_styling = !empty($form['advanced_styling']) ? $form['advanced_styling'] : '0';
$page_by_page = !empty($form['page_by_page']) ? $form['page_by_page'] : '0';
$hide_labels = !empty($form['hide_labels']) ? $form['hide_labels'] : '0';
$contents = !empty($form['contents']) ? $form['contents'] : '';
$display_form = !empty($form['display_form']) ? $form['display_form'] : '0';

$font_array = array('arial'=>'Arial','courier'=>'Courier','helvetica'=>'Helvetica','times'=>'Times New Roman','zapfdingbats'=>'Zapf Dingbats','OpenSans'=>'Open Sans','Roboto'=>'Roboto','Encode Sans, sans-serif' => 'Encode Sans, sans-serif', 'Slabo, serif' => 'Slabo', 'Montserrat, sans-serif' => 'Montserrat', 'Raleway, sans-serif' => 'Raleway', 'Merriweather, sans-serif' => 'Merriweather', 'Lora, sans-serif' => 'Lora', 'Nunito, sans-serif' => 'Nunito', 'Karla, sans-serif' => 'Karla');
ksort($font_array);
?>
<script type="text/javascript">
$(document).ready(function() {
	$('.form-content input,select,textarea').on('change', function() { saveStyling(this); });
	tinyMCE.DOM.setStyle(tinyMCE.DOM.get('form_contents'), 'height', '500px');
});
function loadContent(link) {
	$('.styling_div').hide();
	var div_id = $(link).data('id');
	$('#'+div_id).show();
	$('.styling_sidebar li').removeClass('active');
	$(link).find('li').addClass('active');
}
function colorCodeChange(sel) {
    $(sel).closest('.form-group').find('[name$="color"]').val(sel.value);
}
function changeStylingType() {
	var advanced_styling = $('[name="advanced_styling"]:checked').val();
	if(advanced_styling == 0) {
		$('.content_styling_simple').show();
		$('.content_styling_advanced').hide();
	} else {
		$('.content_styling_simple').hide();
		$('.content_styling_advanced').show();
	}
}
function enablePageByPage() {
	if($('[name="page_by_page"]').is(':checked') && $('[name="advanced_styling"]:checked').val() != 1) {
		$('.formbuilder_tabs a.page_by_page_styling').show();
	} else {
		$('.formbuilder_tabs a.page_by_page_styling').hide();
	}
}
function insertField(btn, textarea) {
	tinyMCE.get(textarea).execCommand('mceInsertContent', false, '[['+$(btn).data('name')+']]');
	return false;
}
function deleteLogo(logo) {
	if(confirm('Are you sure you want to delete this logo?')) {
		var formid = $('#formid').val();
		$.ajax({
			url: '../Form Builder/form_ajax.php?fill=delete_logo',
			type: 'POST',
			data: { formid: formid, logo: logo, type: 'form' },
			success: function(response) {
				console.log(response);
				if(logo == 'header') {
					$('.header_logo_url').html('');
				} else if(logo == 'footer') {
					$('.footer_logo_url').html('');
				}
			}
		});
	}
}
function saveStyling(field) {
	var field_data = new FormData();
	field_data.append('formid', $('#formid').val());

	field_data.append('header_logo', $('[name="header_logo"]')[0].files[0]);
	field_data.append('header', $('[name="header_text"]').val());
	field_data.append('header_align', $('[name="header_align"]').val());
	field_data.append('header_font', $('[name="header_font"]').val());
	field_data.append('header_size', $('[name="header_size"]').val());
	field_data.append('header_color', $('[name="header_color"]').val());
	var header_styling = [];
	$('[name="header_styling[]"]').each(function() {
		if($(this).is(':checked')) {
			header_styling.push($(this).val());
		}
	});
	field_data.append('header_styling', JSON.stringify(header_styling));
	if($('[name="header_skip_first_page"]').is(':checked')) {
		field_data.append('header_skip_first_page', $('[name="header_skip_first_page"]').val());
	} else {
		field_data.append('header_skip_first_page', 0);
	}

	field_data.append('footer_logo', $('[name="footer_logo"]')[0].files[0]);
	field_data.append('footer', $('[name="footer_text"]').val());
	field_data.append('footer_align', $('[name="footer_align"]').val());
	field_data.append('footer_font', $('[name="footer_font"]').val());
	field_data.append('footer_size', $('[name="footer_size"]').val());
	field_data.append('footer_color', $('[name="footer_color"]').val());
	var footer_styling = [];
	$('[name="footer_styling[]"]').each(function() {
		if($(this).is(':checked')) {
			footer_styling.push($(this).val());
		}
	});
	field_data.append('footer_styling', JSON.stringify(footer_styling));

	field_data.append('section_heading_font', $('[name="section_heading_font"]').val());
	field_data.append('section_heading_size', $('[name="section_heading_size"]').val());
	field_data.append('section_heading_color', $('[name="section_heading_color"]').val());
	var section_heading_styling = [];
	$('[name="section_heading_styling[]"]').each(function() {
		if($(this).is(':checked')) {
			section_heading_styling.push($(this).val());
		}
	});
	field_data.append('section_heading_styling', JSON.stringify(section_heading_styling));

	field_data.append('body_heading_font', $('[name="body_heading_font"]').val());
	field_data.append('body_heading_size', $('[name="body_heading_size"]').val());
	field_data.append('body_heading_color', $('[name="body_heading_color"]').val());
	var body_heading_styling = [];
	$('[name="body_heading_styling[]"]').each(function() {
		if($(this).is(':checked')) {
			body_heading_styling.push($(this).val());
		}
	});
	field_data.append('body_heading_styling', JSON.stringify(body_heading_styling));

	field_data.append('body_font', $('[name="body_font"]').val());
	field_data.append('body_size', $('[name="body_size"]').val());
	field_data.append('body_color', $('[name="body_color"]').val());
	var body_styling = [];
	$('[name="body_styling[]"]').each(function() {
		if($(this).is(':checked')) {
			body_styling.push($(this).val());
		}
	});
	field_data.append('body_styling', JSON.stringify(body_styling));

	field_data.append('page_format', $('[name="page_format"]').val());

	field_data.append('advanced_styling', $('[name="advanced_styling"]:checked').val());
	if($('[name="page_by_page"]').is(':checked')) {
		field_data.append('page_by_page', $('[name="page_by_page"]').val());
	} else {
		field_data.append('page_by_page', 0);
	}
	if($('[name="hide_labels"]').is(':checked')) {
		field_data.append('hide_labels', $('[name="hide_labels"]').val());
	} else {
		field_data.append('hide_labels', 0);
	}
	field_data.append('contents', $('[name="contents"]').val());
	if($('[name="display_form"]').is(':checked')) {
		field_data.append('display_form', $('[name="display_form"]').val());
	} else {
		field_data.append('display_form', 0);
	}

	$.ajax({
		processData: false,
		contentType: false,
		url: '../Form Builder/form_ajax.php?fill=update_styling',
		type: 'POST',
		data: field_data,
		success: function(response) {
			response_arr = response.split('*#*');
			if(response_arr[0] == 'header_logo') {
				$('[name="header_logo"]').val('');
				$('.header_logo_url').html('<a href="'+response_arr[1]+'">View</a> | <a href="" onclick="deleteLogo(\'header\'); return false;">Delete</a>');
			} else if(response_arr[0] == 'footer_logo') {
				$('[name="footer_logo"]').val('');
				$('.footer_logo_url').html('<a href="'+response_arr[1]+'">View</a> | <a href="" onclick="deleteLogo(\'footer\'); return false;">Delete</a>');
			}
		}

	});
}
function showFormFields(link, type) {
	if(type == 'show') {
		$(link).closest('div').find('.form_fields').show();
		$(link).closest('div').find('.show_form_fields').hide();
		$(link).closest('div').find('.hide_form_fields').show();
	} else {
		$(link).closest('div').find('.form_fields').hide();
		$(link).closest('div').find('.show_form_fields').show();
		$(link).closest('div').find('.hide_form_fields').hide();
	}
}
</script>
<div class="standard-collapsible tile-sidebar" style="height: 100%;">
	<ul class="sidebar styling_sidebar">
		<a href="" data-id="header_styling" onclick="loadContent(this); return false;"><li class="active">Header Styling</li></a>
		<a href="" data-id="footer_styling" onclick="loadContent(this); return false;"><li>Footer Styling</li></a>
		<a href="" data-id="content_styling" onclick="loadContent(this); return false;"><li>Content Styling</li></a>
	</ul>
</div>
<div class="scale-to-fill has-main-screen">
	<div class="main-screen form-content">
		<input type="hidden" id="formid" name="formid" value="<?= $formid ?>">
		<div id="header_styling" class="form-horizontal col-sm-12 styling_div">
			<h3>Header Styling</h3>
			<div class="form-group">
				<label class="col-sm-4 control-label">Header Logo:</label>
				<div class="col-sm-8">
					<div class="header_logo_url">
						<?php if(!empty($header_logo) && file_exists('download/'.$header_logo)) { ?>
							<a href="download/<?= $header_logo ?>" target="_blank">View</a> | <a href="" onclick="deleteLogo('header'); return false;">Delete</a>
						<?php } ?>
					</div>
					<input name="header_logo" type="file" data-filename-placement="inside" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-4 control-label">
					<label>Header Text:</label><br>
					<a href="" onclick="showFormFields(this, 'show'); return false;" class="show_form_fields">Show Form Fields</a>
					<a href="" onclick="showFormFields(this, 'hide'); return false;" class="hide_form_fields" style="display:none;">Hide Form Fields</a>
					<div class="form_fields" style="display:none;">
						<?php $field_list = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `deleted` = 0 AND `type` != 'OPTION' AND `type` != 'CONTACTINFO' ORDER BY `sort_order`");
						while ($row = mysqli_fetch_array($field_list)) { ?>
							<button class="btn brand-btn" data-name="<?= $row['name'] ?>" onclick="return insertField(this, 'header_text');"><?= $row['label'] ?></button>
						<?php } ?>
						<?php $contact_field_list = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `deleted` = 0 AND `type` = 'CONTACTINFO' ORDER BY `sort_order`");
						while ($row = mysqli_fetch_array($contact_field_list)) { ?>
							<h4><?= $row['label'] ?> Fields</h4>
							<?php $contact_fields = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `name` = '".$row['name']."' AND `type` = 'OPTION' AND `deleted` = 0 ORDER BY `sort_order`"),MYSQLI_ASSOC);
							foreach ($contact_fields as $contact_field) { ?>
								<button class="btn brand-btn" data-name="<?= $row['name'].'['.$contact_field['source_conditions'].']' ?>" onclick="return insertField(this, 'header_text');"><?= $contact_field['label'] ?></button>
							<?php }
						} ?>
					</div>
				</div>
				<div class="col-sm-8">
					<textarea name="header_text" class="form-control"><?= html_entity_decode($header) ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Header Align:</label>
				<div class="col-sm-8">
                    <select name="header_align" class="chosen-select-deselect form-control">
                        <option></option>
                        <option <?= $header_align == 'L' ? 'selected' : '' ?> value="L">Left</option>
                        <option <?= $header_align == 'C' ? 'selected' : '' ?> value="C">Center</option>
                        <option <?= $header_align == 'R' ? 'selected' : '' ?> value="R">Right</option>
                    </select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Header Font:</label>
				<div class="col-sm-8">
                    <select name="header_font" class="chosen-select-deselect form-control">
                        <option></option>
                        <?php 
                            foreach($font_array as $font_value => $font) { ?>
                                <option <?= $header_font == $font_value ? 'selected' : '' ?> value="<?= $font_value ?>"><?= $font ?></option>
                            <?php }
                        ?>
                    </select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Header Size:</label>
				<div class="col-sm-8">
                    <select name="header_size" class="chosen-select-deselect form-control">
                        <option></option>
                        <?php for($i = 6; $i < 50; $i++) { ?>
                            <option <?= $header_size == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>pt</option>
                        <?php } ?>
                    </select>
				</div>
			</div>
            <div class="form-group">
                <label for="header_color" class="col-sm-4 control-label">Header Color:</label>
                <div class="col-sm-1">
                    <input type="color" name="header_color_picker" value="<?= $header_color ?>" class="form-control" onchange="colorCodeChange(this);">
                </div>
                <div class="col-sm-7">
                    <input type="text" name="header_color" value="<?= $header_color ?>" class="form-control">
                </div>
            </div>
            <div class="form-group">
            	<label for="header_skip_first_page" class="col-sm-4 control-label">Header Skip First Page:</label>
            	<div class="col-sm-8">
            		<label class="form-checkbox"><input type="checkbox" name="header_skip_first_page" class="form-control" value="1" <?= $header_skip_first_page > 0 ? 'checked' : '' ?>> Enable</label>
            	</div>
            </div>
            <div class="form-group">
				<label class="col-sm-4 control-label">Header Styling:</label>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" name="header_styling[]" value="Bold" <?= strpos(','.$header_styling.',', ',Bold,') !== FALSE ? 'checked' : '' ?>> Bold</label>
					<label class="form-checkbox"><input type="checkbox" name="header_styling[]" value="Underline" <?= strpos(','.$header_styling.',', ',Underline,') !== FALSE ? 'checked' : '' ?>> Underline</label>
					<label class="form-checkbox"><input type="checkbox" name="header_styling[]" value="Italic" <?= strpos(','.$header_styling.',', ',Italic,') !== FALSE ? 'checked' : '' ?>> Italic</label>
				</div>
            </div>
		</div>
		<div id="footer_styling" class="form-horizontal col-sm-12 styling_div" style="display: none;">
			<h3>Footer Styling</h3>
			<div class="form-group">
				<label class="col-sm-4 control-label">Footer Logo:</label>
				<div class="col-sm-8">
					<div class="footer_logo_url">
						<?php if(!empty($footer_logo) && file_exists('download/'.$footer_logo)) { ?>
							<a href="download/<?= $footer_logo ?>" target="_blank">View</a> | <a href="" onclick="deleteLogo('footer'); return false;">Delete</a>
						<?php } ?>
					</div>
					<input name="footer_logo" type="file" data-filename-placement="inside" class="form-control" />
				</div>
			</div>
			<div class="form-group">
				<div class="col-sm-4 control-label">
					<label>Footer Text:</label><br>
					<a href="" onclick="showFormFields(this, 'show'); return false;" class="show_form_fields">Show Form Fields</a>
					<a href="" onclick="showFormFields(this, 'hide'); return false;" class="hide_form_fields" style="display:none;">Hide Form Fields</a>
					<div class="form_fields" style="display:none;">
						<?php $field_list = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `deleted` = 0 AND `type` != 'OPTION' AND `type` != 'CONTACTINFO' ORDER BY `sort_order`");
						while ($row = mysqli_fetch_array($field_list)) { ?>
							<button class="btn brand-btn" data-name="<?= $row['name'] ?>" onclick="return insertField(this, 'footer_text');"><?= $row['label'] ?></button>
						<?php } ?>
						<?php $contact_field_list = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `deleted` = 0 AND `type` = 'CONTACTINFO' ORDER BY `sort_order`");
						while ($row = mysqli_fetch_array($contact_field_list)) { ?>
							<h4><?= $row['label'] ?> Fields</h4>
							<?php $contact_fields = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `name` = '".$row['name']."' AND `type` = 'OPTION' AND `deleted` = 0 ORDER BY `sort_order`"),MYSQLI_ASSOC);
							foreach ($contact_fields as $contact_field) { ?>
								<button class="btn brand-btn" data-name="<?= $row['name'].'['.$contact_field['source_conditions'].']' ?>" onclick="return insertField(this, 'footer_text');"><?= $contact_field['label'] ?></button>
							<?php }
						} ?>
					</div>
				</div>
				<div class="col-sm-8">
					<textarea name="footer_text" class="form-control"><?= html_entity_decode($footer) ?></textarea>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Footer Align:</label>
				<div class="col-sm-8">
                    <select name="footer_align" class="chosen-select-deselect form-control">
                        <option></option>
                        <option <?= $footer_align == 'L' ? 'selected' : '' ?> value="L">Left</option>
                        <option <?= $footer_align == 'C' ? 'selected' : '' ?> value="C">Center</option>
                        <option <?= $footer_align == 'R' ? 'selected' : '' ?> value="R">Right</option>
                    </select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Footer Font:</label>
				<div class="col-sm-8">
                    <select name="footer_font" class="chosen-select-deselect form-control">
                        <option></option>
                        <?php 
                            foreach($font_array as $font_value => $font) { ?>
                                <option <?= $footer_font == $font_value ? 'selected' : '' ?> value="<?= $font_value ?>"><?= $font ?></option>
                            <?php }
                        ?>
                    </select>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label">Footer Size:</label>
				<div class="col-sm-8">
                    <select name="footer_size" class="chosen-select-deselect form-control">
                        <option></option>
                        <?php for($i = 6; $i < 50; $i++) { ?>
                            <option <?= $footer_size == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>pt</option>
                        <?php } ?>
                    </select>
				</div>
			</div>
            <div class="form-group">
                <label for="footer_color" class="col-sm-4 control-label">Footer Color:</label>
                <div class="col-sm-1">
                    <input type="color" name="footer_color_picker" value="<?= $footer_color ?>" class="form-control" onchange="colorCodeChange(this);">
                </div>
                <div class="col-sm-7">
                    <input type="text" name="footer_color" value="<?= $footer_color ?>" class="form-control">
                </div>
            </div>
            <div class="form-group">
				<label class="col-sm-4 control-label">Footer Styling:</label>
				<div class="col-sm-8">
					<label class="form-checkbox"><input type="checkbox" name="footer_styling[]" value="Bold" <?= strpos(','.$footer_styling.',', ',Bold,') !== FALSE ? 'checked' : '' ?>> Bold</label>
					<label class="form-checkbox"><input type="checkbox" name="footer_styling[]" value="Underline" <?= strpos(','.$footer_styling.',', ',Underline,') !== FALSE ? 'checked' : '' ?>> Underline</label>
					<label class="form-checkbox"><input type="checkbox" name="footer_styling[]" value="Italic" <?= strpos(','.$footer_styling.',', ',Italic,') !== FALSE ? 'checked' : '' ?>> Italic</label>
				</div>
            </div>
            <div class="form-group">
            	<label for="page_format" class="col-sm-4 control-label">Page Number Format:<br><em>Enter how you want Page Numbers to appear. You can enter [[CURRENT_PAGE]], [[TOTAL_PAGE]].</em></label>
            	<div class="col-sm-8">
            		<input type="text" name="page_format" value="<?= $page_format ?>" class="form-control">
            	</div>
            </div>
		</div>
		<div id="content_styling" class="form-horizontal col-sm-12 styling_div" style="display: none;">
			<h3>Content Styling</h3>
			<div class="form-group">
				<label class="col-sm-4 control-label">Content Styling:</label>
				<div class="col-sm-8">
					<input name="advanced_styling" type="radio" value="0" <?= $advanced_styling == 0 ? 'checked' : '' ?> onclick="changeStylingType(); enablePageByPage();">Simple&nbsp;&nbsp;
					<input name="advanced_styling" type="radio" value="1" <?= $advanced_styling == 1 ? 'checked' : '' ?> onclick="changeStylingType(); enablePageByPage();">Advanced&nbsp;&nbsp;
				</div>
			</div>
			<div class="form-group content_styling_simple" <?= $advanced_styling != 0 ? 'style="display:none;"' : '' ?>>
				<label class="col-sm-4 control-label">Page-by-Page Customization:</label>
				<div class="col-sm-8">
					<input name="page_by_page" type="checkbox" value="1"  <?= $page_by_page == 1 ? 'checked' : '' ?> onclick="enablePageByPage();" style="height: 20px; width: 20px;"> Enable
				</div>
			</div>
			<div class="form-group content_styling_simple" <?= $advanced_styling != 0 ? 'style="display:none;"' : '' ?>>
				<label class="col-sm-4 control-label">Hide Labels:</label>
				<div class="col-sm-8">
					<input name="hide_labels" type="checkbox" value="1"  <?= $hide_labels == 1 ? 'checked' : '' ?> style="height: 20px; width: 20px;"> Enable
				</div>
			</div>
			<div class="content_styling_simple" <?= $advanced_styling != 0 ? 'style="display:none;"' : '' ?>>
				<h3>Section Heading (Accordion) Styling</h3>
				<div class="form-group">
					<label class="col-sm-4 control-label">Section Heading Font:</label>
					<div class="col-sm-8">
	                    <select name="section_heading_font" class="chosen-select-deselect form-control">
	                        <option></option>
	                        <?php 
	                            foreach($font_array as $font_value => $font) { ?>
	                                <option <?= $section_heading_font == $font_value ? 'selected' : '' ?> value="<?= $font_value ?>"><?= $font ?></option>
	                            <?php }
	                        ?>
	                    </select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Section Heading Size:</label>
					<div class="col-sm-8">
	                    <select name="section_heading_size" class="chosen-select-deselect form-control">
	                        <option></option>
	                        <?php for($i = 6; $i < 50; $i++) { ?>
	                            <option <?= $section_heading_size == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>pt</option>
	                        <?php } ?>
	                    </select>
					</div>
				</div>
	            <div class="form-group">
	                <label for="section_heading_color" class="col-sm-4 control-label">Section Heading Color:</label>
	                <div class="col-sm-1">
	                    <input type="color" name="section_heading_color_picker" value="<?= $section_heading_size_color ?>" class="form-control" onchange="colorCodeChange(this);">
	                </div>
	                <div class="col-sm-7">
	                    <input type="text" name="section_heading_color" value="<?= $section_heading_color ?>" class="form-control">
	                </div>
	            </div>
	            <div class="form-group">
					<label class="col-sm-4 control-label">Section Heading Styling:</label>
					<div class="col-sm-8">
						<label class="form-checkbox"><input type="checkbox" name="section_heading_styling[]" value="Bold" <?= strpos(','.$section_heading_styling.',', ',Bold,') !== FALSE ? 'checked' : '' ?>> Bold</label>
						<label class="form-checkbox"><input type="checkbox" name="section_heading_styling[]" value="Underline" <?= strpos(','.$section_heading_styling.',', ',Underline,') !== FALSE ? 'checked' : '' ?>> Underline</label>
						<label class="form-checkbox"><input type="checkbox" name="section_heading_styling[]" value="Italic" <?= strpos(','.$section_heading_styling.',', ',Italic,') !== FALSE ? 'checked' : '' ?>> Italic</label>
					</div>
	            </div>

				<h3>Body Heading Styling</h3>
				<div class="form-group">
					<label class="col-sm-4 control-label">Body Heading Font:</label>
					<div class="col-sm-8">
	                    <select name="body_heading_font" class="chosen-select-deselect form-control">
	                        <option></option>
	                        <?php 
	                            foreach($font_array as $font_value => $font) { ?>
	                                <option <?= $body_heading_font == $font_value ? 'selected' : '' ?> value="<?= $font_value ?>"><?= $font ?></option>
	                            <?php }
	                        ?>
	                    </select>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Body Heading Size:</label>
					<div class="col-sm-8">
	                    <select name="body_heading_size" class="chosen-select-deselect form-control">
	                        <option></option>
	                        <?php for($i = 6; $i < 50; $i++) { ?>
	                            <option <?= $body_heading_size == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>pt</option>
	                        <?php } ?>
	                    </select>
					</div>
				</div>
	            <div class="form-group">
	                <label for="body_heading_color" class="col-sm-4 control-label">Body Heading Color:</label>
	                <div class="col-sm-1">
	                    <input type="color" name="body_heading_color_picker" value="<?= $body_heading_color ?>" class="form-control" onchange="colorCodeChange(this);">
	                </div>
	                <div class="col-sm-7">
	                    <input type="text" name="body_heading_color" value="<?= $body_heading_color ?>" class="form-control">
	                </div>
	            </div>
	            <div class="form-group">
					<label class="col-sm-4 control-label">Body Heading Styling:</label>
					<div class="col-sm-8">
						<label class="form-checkbox"><input type="checkbox" name="body_heading_styling[]" value="Bold" <?= strpos(','.$body_heading_styling.',', ',Bold,') !== FALSE ? 'checked' : '' ?>> Bold</label>
						<label class="form-checkbox"><input type="checkbox" name="body_heading_styling[]" value="Underline" <?= strpos(','.$body_heading_styling.',', ',Underline,') !== FALSE ? 'checked' : '' ?>> Underline</label>
						<label class="form-checkbox"><input type="checkbox" name="body_heading_styling[]" value="Italic" <?= strpos(','.$body_heading_styling.',', ',Italic,') !== FALSE ? 'checked' : '' ?>> Italic</label>
					</div>
	            </div>
			</div>
			<div class="content_styling_both">
				<h3>Body Styling</h3>
				<div class="form-group">
					<label class="col-sm-4 control-label">Body Font:</label>
					<div class="col-sm-8">
	                    <select name="body_font" class="chosen-select-deselect form-control">
	                        <option></option>
	                        <?php 
	                            foreach($font_array as $font_value => $font) { ?>
	                                <option <?= $body_font == $font_value ? 'selected' : '' ?> value="<?= $font_value ?>"><?= $font ?></option>
	                            <?php }
	                        ?>
	                    </select>
					</div>
				</div>
			</div>
			<div class="content_styling_simple" <?= $advanced_styling != 0 ? 'style="display:none;"' : '' ?>>
				<div class="form-group">
					<label class="col-sm-4 control-label">Body Size:</label>
					<div class="col-sm-8">
	                    <select name="body_size" class="chosen-select-deselect form-control">
	                        <option></option>
	                        <?php for($i = 6; $i < 50; $i++) { ?>
	                            <option <?= $body_size == $i ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>pt</option>
	                        <?php } ?>
	                    </select>
					</div>
				</div>
	            <div class="form-group">
	                <label for="body_color" class="col-sm-4 control-label">Body Color:</label>
	                <div class="col-sm-1">
	                    <input type="color" name="body_color_picker" value="<?= $body_color ?>" class="form-control" onchange="colorCodeChange(this);">
	                </div>
	                <div class="col-sm-7">
	                    <input type="text" name="body_color" value="<?= $body_color ?>" class="form-control">
	                </div>
	            </div>
	            <div class="form-group">
					<label class="col-sm-4 control-label">Body Styling:</label>
					<div class="col-sm-8">
						<label class="form-checkbox"><input type="checkbox" name="body_styling[]" value="Bold" <?= strpos(','.$body_styling.',', ',Bold,') !== FALSE ? 'checked' : '' ?>> Bold</label>
						<label class="form-checkbox"><input type="checkbox" name="body_styling[]" value="Underline" <?= strpos(','.$body_styling.',', ',Underline,') !== FALSE ? 'checked' : '' ?>> Underline</label>
						<label class="form-checkbox"><input type="checkbox" name="body_styling[]" value="Italic" <?= strpos(','.$body_styling.',', ',Italic,') !== FALSE ? 'checked' : '' ?>> Italic</label>
					</div>
	            </div>
	        </div>
			<div class="content_styling_advanced" <?= $advanced_styling != 1 ? 'style="display:none;"' : '' ?>>
				<h3>Form Contents (Click to insert into Textarea)</h3>
				<div class="form-group">
					<div class="col-sm-4 advanced_styling_buttons" style="overflow-y: auto;">
						<?php $field_list = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `deleted` = 0 AND `type` != 'OPTION' AND `type` != 'CONTACTINFO' ORDER BY `sort_order`");
						while ($row = mysqli_fetch_array($field_list)) { ?>
							<button class="btn brand-btn" data-name="<?= $row['name'] ?>" onclick="return insertField(this, 'form_contents');"><?= $row['label'] ?></button>
						<?php } ?>
						<?php $contact_field_list = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `deleted` = 0 AND `type` = 'CONTACTINFO' ORDER BY `sort_order`");
						while ($row = mysqli_fetch_array($contact_field_list)) { ?>
							<h4><?= $row['label'] ?> Fields</h4>
							<?php $contact_fields = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `name` = '".$row['name']."' AND `type` = 'OPTION' AND `deleted` = 0 ORDER BY `sort_order`"),MYSQLI_ASSOC);
							foreach ($contact_fields as $contact_field) { ?>
								<button class="btn brand-btn" data-name="<?= $row['name'].'['.$contact_field['source_conditions'].']' ?>" onclick="return insertField(this, 'form_contents');"><?= $contact_field['label'] ?></button>
							<?php }
						} ?>
					</div>
					<div class="col-sm-8">
						<textarea id="form_contents" name="contents" class="form-control" cols="80" rows="150"><?= html_entity_decode($contents) ?></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label">Display Form Contents:</label>
					<div class="col-sm-8">
						<label class="form-checkbox">
							<input type="checkbox" name="display_form" value="1" <?= $display_form == 1 ? 'checked' : '' ?> class="form-control">
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>