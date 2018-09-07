<?php //Form Builder Fields
if(isset($_POST['img_upload'])) {
	$form_id = $_POST['form_id'];
	$page_id = $_POST['page_id'];
	$file_type = $_POST['file_type'];
    if(!file_exists('../Form Builder/download')) {
        mkdir('../Form Builder/download', 0777, true);
    }
	if($file_type == 'img') {
		if(!empty($_FILES['img_upload']['name'])) {
			$new_name = 'bg_'.$form_id.'_'.$page_id.'.png';
			$filename = $new_name;
			for($i = 1; file_exists('download/'.$filename); $i++) {
				$filename = preg_replace('/(\.[A-Za-z0-9_]*)/', ' ('.$i.')$1', preg_replace('/[^\.A-Za-z0-9_]/','',$new_name));
			}
			$new_name = 'download/'.explode('.png',$filename)[0];
			$new_file = resize_image_convert_png('2550','3300',$new_name,$_FILES['img_upload']['tmp_name']);
			$full_path = '../Form Builder/'.$new_file;
		}
	} else if($file_type == 'pdf') {
		$pdf_page_number = $_POST['pdf_page_number'];
		if(!empty($_FILES['img_upload']['name'])) {
			$temp_file = 'download/bg_'.$form_id.'_'.$page_id.'_temp.png';
			exec('gs -dSAFER -dBATCH -dNOPAUSE -sDEVICE=png16m -r300 -dFirstPage='.$pdf_page_number.' -dLastPage='.$pdf_page_number.' -sOutputFile="'.$temp_file.'" "'.$_FILES['img_upload']['tmp_name'].'"');
			$new_name = 'bg_'.$form_id.'_'.$page_id.'.png';
			$filename = $new_name;
			for($i = 1; file_exists('download/'.$filename); $i++) {
				$filename = preg_replace('/(\.[A-Za-z0-9_]*)/', ' ('.$i.')$1', preg_replace('/[^\.A-Za-z0-9_]/','',$new_name));
			}
			$new_name = 'download/'.explode('.png',$filename)[0];
			$new_file = resize_image_convert_png('2550','3300',$new_name,$temp_file);
			unlink($temp_file);
			$full_path = '../Form Builder/'.$new_file;
		}
	}
	if(!empty($full_path)) {
		mysqli_query($dbc, "UPDATE `user_form_page` SET `img` = '$full_path' WHERE `page_id` = '$page_id'");
	}
}

$get_page = 1;
if($_GET['page'] > 1) {
	$get_page = $_GET['page'];
}

//Field list for sidebar
$field_list = mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `deleted` = 0 AND `type` != 'OPTION' ORDER BY `sort_order`");
$page_fields = [];
foreach ($field_list as $page_field) {
	$page_fields[$page_field['name']] = $page_field['name'].': '.$page_field['label'];
	if($page_field['type'] == 'CONTACTINFO') {
		$contact_fields = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `name` = '".$page_field['name']."' AND `type` = 'OPTION' AND `deleted` = 0 ORDER BY `sort_order`"),MYSQLI_ASSOC);
		foreach($contact_fields as $contact_field) {
			$page_fields[$page_field['name'].'['.$contact_field['source_conditions'].']'] = $page_field['name'].'['.$contact_field['source_conditions'].']'.': '.$page_field['label'].': '.$contact_field['label'];
		}
	} else if($page_field['type'] == 'TEXTBLOCK') {
		$textblock_inputs = substr_count($page_field['content'], '[[input]]');
		for($i = 0; $i < $textblock_inputs; $i++) {
			$page_fields[$page_field['name'].'['.$i.']'] = $page_field['name'].'['.$i.']'.': '.$page_field['label'].': Input '.($i+1);
		}
	} else if($page_field['type'] == 'CHECKBOX') {
		$checkbox_fields = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `name` = '".$page_field['name']."' AND `type` = 'OPTION' AND `deleted` = 0 ORDER BY `sort_order`"),MYSQLI_ASSOC);
		foreach($checkbox_fields as $i => $checkbox_field) {
			$page_fields[$page_field['name'].'['.$i.']'] = $page_field['name'].'['.$i.']'.': '.$page_field['label'].': '.$checkbox_field['label'];
			$page_fields[$page_field['name'].'['.$i.',chk]'] = $page_field['name'].'['.$i.']'.': '.$page_field['label'].': '.$checkbox_field['label'].' (Checkbox only)';
		}
	} else if($page_field['type'] == 'TABLEADV') {
		$table_fields = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_fields` WHERE `form_id` = '$formid' AND `name` = '".$page_field['name']."' AND `type` = 'OPTION' AND `deleted` = 0 ORDER BY `sort_order`"),MYSQLI_ASSOC);
        for($i = 0; $i < count($table_fields); $i++) {
            $table_rows = explode('*#*', $table_fields[$i]['label']);
            for($j = 0; $j < count($table_rows); $j++) {
            	$page_fields[$page_field['name'].'['.$i.','.$j.']'] = $page_field['name'].'['.($i+1).','.($j+1).']: '.$page_field['label'].' Row '.($i+1).' Col '.($j+1);
            }
    	}
	}
}

//Page info
mysqli_query($dbc, "INSERT INTO `user_form_page` (`form_id`, `page`) SELECT '$formid', '$get_page' FROM (SELECT COUNT(*) rows FROM `user_form_page` WHERE `form_id` = '$formid' AND `page` = '$get_page' AND `deleted` = 0) num WHERE num.rows = 0");
$page = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `user_form_page` WHERE `form_id` = '$formid' AND `page` = '$get_page' AND `deleted` = 0"));
$page_id = $page['page_id'];
$background_img = $page['img'];

//Page settings
$page_settings = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_page` WHERE `form_id` = '$formid' AND `deleted` = 0 ORDER BY `page`"),MYSQLI_ASSOC);
$new_page = mysqli_fetch_array(mysqli_query($dbc, "SELECT (MAX(`page`) + 1) new_page FROM `user_form_page` WHERE `form_id` = '$formid' AND `deleted` = 0 ORDER BY `page`"))['new_page'];

//Page details
$page_details = mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `user_form_page_detail` WHERE `page_id` = '$page_id' AND `deleted` = 0 ORDER BY `white_space` DESC"),MYSQLI_ASSOC);
?>
<script type="text/javascript" src="formbuilder_page.js"></script>
<div class="standard-collapsible tile-sidebar" style="height: 100%;">
	<ul class="sidebar page_field_sidebar" style="max-width: 15vw;">
		<div class="block-item page_field_draggable" data-whitespace="1" style="display: none;">Add White Space</div>
		<?php foreach($page_fields as $field_name => $field_label) { ?>
			<div class="block-item page_field_draggable" data-fieldname="<?= $field_name ?>" data-label="<?= trim(substr($field_label, strpos($field_label, ':') + 1)) ?>">
				<?= $field_label ?>
			</div>
		<?php } ?>
	</ul>
</div>
<div class="scalable" style="background-color: #ffffff;">
	<form name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form" style="min-width: 20em;">
		<input type="hidden" name="form_id" value="<?= $formid ?>">
		<input type="hidden" id="page_id" name="page_id" value="<?= $page_id ?>">
		<input type="hidden" name="file_type" value="">
		<div class="notice pull-left popover-examples">
			<div class="col-sm-1 notice-icon"><img src="<?= WEBSITE_URL ?>/img/info.png" class="wiggle-me" width="25"></div>
			<div class="col-sm-11"><span class="notice-name">NOTE:</span> Drag items onto the page to position fields. Resize items to set the height and width. Drag items onto the trashcan to delete. Toggle white space to add white space onto your page.</div>
			<div class="clearfix"></div>
		</div>
		<h4 style="margin-bottom: 0;">Page Settings</h4>
		<div class="col-sm-12">
			<label class="control-label">Toggle White Space: <input type="checkbox" name="toggle_white_space" value="1" onclick="toggleWhiteSpace();" style="width: 20px; height: 20px; position: relative; top: 5px;"></label>
		</div>
		<div class="col-sm-12">
			<label class="control-label">Page:</label>
		</div>
		<div class="col-sm-12">
			<select name="page_number" class="chosen-select-deselect form-control">
				<option value="1" <?= $get_page == 1 ? 'selected' : '' ?>>Page 1</option>
				<?php foreach ($page_settings as $page_setting) {
					if($page_setting['page'] > 1) { ?>
						<option value="<?= $page_setting['page'] ?>" <?= $get_page == $page_setting['page'] ? 'selected' : '' ?>>Page <?= $page_setting['page'] ?></option>
					<?php }
				} ?>
				<option value="<?= $new_page ?>">Add Page</option>
			</select>
			<div class="pull-right"><a href="" onclick="deletePage(); return false;" class="btn brand-btn">Delete Page</a></div>
		</div>
		<div class="col-sm-12">
			<label class="control-label">Upload Background Image:</label><br><em>Upload an image or PDF. Keep aspect ratio at 8.5 x 11 (LETTER) or the image may appear deformed.</em>
		</div>
		<div class="col-sm-12">
			<input type="file" name="img_upload" class="form-control" onchange="checkFileExtension();">
		</div>
		<div class="pdf_page_settings" style="display: none;">
			<div class="col-sm-12">
				<label class="control-label">PDF Page Number:</label>
			</div>
			<div class="col-sm-12">
				<input name="pdf_page_number" type="number" min="1" class="form-control" placeholder="PDF Page Number" value="1">
			</div>
		</div>
		<div class="col-sm-12 double-gap-bottom">
			<div class="pull-right"><button type="submit" name="img_upload" value="img_upload" class="btn brand-btn" onclick="return uploadPageImage();">Upload</button></div>
		</div>
		<h4>Page Sort Order</h4>
		<div class="col-sm-12 page_order_div">
			<?php foreach ($page_settings as $page_setting) { ?>
				<div class="block-item page_order_sortable" data-id="<?= $page_setting['page_id'] ?>">Page <?= $page_setting['page'] ?><img class='drag-handle' src='<?= WEBSITE_URL ?>/img/icons/drag_handle.png' style='float: right; width: 2em;'></div>
			<?php } ?>
		</div>
	</form>
</div>
<div class="scale-to-fill has-main-screen">
	<div class="main-screen">
		<img class="page_trashcan" src="<?= WEBSITE_URL ?>/img/icons/ROOK-trash-icon.png">
		<div class="form-horizontal col-sm-12">
			<div class="page_field_div" style="width:612px; height:792px; border: 1px solid black; background-color: white; margin: 1em; position: relative; background-image: url('<?= $background_img ?>'); background-repeat: no-repeat; background-size: 100%; margin-right: auto; margin-left: auto;">
				<?php foreach($page_details as $page_detail) { ?>
					<div class="block-item page_field_sortable <?= $page_detail['white_space'] != 1 ? '' : 'page_field_whitespace' ?>" data-id="<?= $page_detail['page_detail_id'] ?>" style="top: <?= $page_detail['top'] ?>px; left: <?= $page_detail['left'] ?>px; width: <?= $page_detail['width'] ?>px; height: <?= $page_detail['height'] ?>px;"><?= $page_detail['white_space'] != 1 ? $page_detail['field_name'].': '.$page_detail['field_label'] : '' ?></div>
				<?php } ?>
			</div>
		</div>
	</div>
</div>