<?php $file_name = $pdf_settings['file_name'];
$font_size = $pdf_settings['font_size'];
$font_type = $pdf_settings['font_type'];
$font = $pdf_settings['font'];
$pdf_logo = $pdf_settings['pdf_logo'];
$pdf_size = $pdf_settings['pdf_size'];
$page_ori = $pdf_settings['page_ori'];
$units = $pdf_settings['units'];
$left_margin = $pdf_settings['left_margin'];
$right_margin = $pdf_settings['right_margin'];
$top_margin = $pdf_settings['top_margin'];
$header_margin = $pdf_settings['header_margin'];
$bottom_margin = $pdf_settings['bottom_margin'];
$pdf_color = $pdf_settings['pdf_color']; ?>
<h3>PDF Settings</h3>
<div class="dashboard-item">
	<div class="col-sm-7">
		<h4>Document Settings</h4>
		<div class="form-group">
			<label class="col-sm-3 control-label">File Name:</label>
			<div class="col-sm-9">
				<div style="width: calc(100% - 6em);" class="pull-left"><input name="file_name" type="text" value="<?php echo $file_name; ?>" class="form-control"/></div>_[ID].pdf
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">Heading Font:</label>
			<div class="col-sm-9">
				<div class="col-sm-3">
					<select class="form-control" name="font_size">
						<?php for($i=9;$i<50;$i++): ?>
							<option <?= $i == $font_size ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>pt</option>
						<?php endfor; ?>
					</select>
				</div>
				<div class="col-sm-4">
					<?php $font_type_array = array('regular'=>'Regular','bold'=>'Bold','italic'=>'Italic','bold_italic'=>'Bold Italic'); ?>
					<select class="form-control" name="font_type">
						<?php foreach($font_type_array as $font_type_value => $font_types): ?>
							<option <?= $font_type_value == $font_type ? 'selected' : '' ?> value="<?= $font_type_value ?>"><?= $font_types ?></option>
						<?php endforeach; ?>					
					</select>
				</div>
				<div class="col-sm-5">
					<?php $font_array = array('courier'=>'Courier','helvetica'=>'Helvetica','times'=>'Times New Roman','zapfdingbats'=>'Zapf Dingbats','OpenSans'=>'Open Sans','Roboto'=>'Roboto','Encode Sans, sans-serif' => 'Encode Sans, sans-serif', 'Slabo, serif' => 'Slabo', 'Montserrat, sans-serif' => 'Montserrat', 'Raleway, sans-serif' => 'Raleway', 'Merriweather, sans-serif' => 'Merriweather', 'Lora, sans-serif' => 'Lora', 'Nunito, sans-serif' => 'Nunito', 'Karla, sans-serif' => 'Karla');
					ksort($font_array); ?>
					<select name="font" class="form-control">
						<?php foreach($font_array as $font_value => $fonts): ?>
							<option <?= $font == $font_value ? 'selected' : '' ?> value="<?= $font_value ?>"><?= $fonts ?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label">PDF Colour:</label>
			<div class="col-sm-9">
				<input type="color" class="form-control" name="pdf_color" value="<?= $pdf_color ?>">
			</div>
		</div>
	</div>
	<div class="col-sm-4 pull-right">
		<h4>Page Settings</h4>
		<!--<div class="form-group">
			<label class="col-sm-6 control-label">Page Size:</label>
			<div class="col-sm-6">
				<select class="form-control" name="pdf_size">
					<?php for($i=9;$i<50;$i++): ?>
						<option <?= $i == $pdf_size ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>pt</option>
					<?php endfor; ?>
				</select>
			</div>
		</div>-->
		<div class="form-group">
			<label class="col-sm-6 control-label">Page Orientation:</label>
			<div class="col-sm-6">
				<select class="form-control" name="page_ori">
					<option <?= 'portrait' == $page_ori ? 'selected' : '' ?> value="portrait">Portrait</option>
					<option <?= 'landscape' == $page_ori ? 'selected' : '' ?> value="landscape">Landscape</option>
				</select>
			</div>
		</div>
		<!--<div class="form-group">
			<label class="col-sm-6 control-label">Units:</label>
			<div class="col-sm-6">
				<select class="form-control" name="units">
					<?php for($i=9;$i<50;$i++): ?>
						<option <?= $i == $units ? 'selected' : '' ?> value="<?= $i ?>"><?= $i ?>pt</option>
					<?php endfor; ?>
				</select>
			</div>
		</div>-->
		<div class="form-group">
			<label class="col-sm-6 control-label">Left Margin:</label>
			<div class="col-sm-6">
				<select class="form-control" name="left_margin">
					<?php for($i=9;$i<150;$i+=9): ?>
						<option <?= $left_margin >= $i && $left_margin < $i + 9 ? 'selected' : '' ?> value="<?= $i ?>"><?= getFraction($i / 72) ?>"</option>
					<?php endfor; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-6 control-label">Right Margin:</label>
			<div class="col-sm-6">
				<select class="form-control" name="right_margin">
					<?php for($i=9;$i<150;$i+=9): ?>
						<option <?= $right_margin >= $i && $right_margin < $i + 9 ? 'selected' : '' ?> value="<?= $i ?>"><?= getFraction($i / 72) ?>"</option>
					<?php endfor; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-6 control-label">Top Margin:</label>
			<div class="col-sm-6">
				<select class="form-control" name="top_margin">
					<?php for($i=9;$i<150;$i+=9): ?>
						<option <?= $top_margin >= $i && $top_margin < $i + 9 ? 'selected' : '' ?> value="<?= $i ?>"><?= getFraction($i / 72) ?>"</option>
					<?php endfor; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-6 control-label">Header Margin:</label>
			<div class="col-sm-6">
				<select class="form-control" name="header_margin">
					<?php for($i=9;$i<150;$i+=9): ?>
						<option <?= $header_margin >= $i && $header_margin < $i + 9 ? 'selected' : '' ?> value="<?= $i ?>"><?= getFraction($i / 72) ?>"</option>
					<?php endfor; ?>
				</select>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-6 control-label">Bottom Margin:</label>
			<div class="col-sm-6">
				<select class="form-control" name="bottom_margin">
					<?php for($i=9;$i<150;$i+=9): ?>
						<option <?= $bottom_margin >= $i && $bottom_margin < $i + 9 ? 'selected' : '' ?> value="<?= $i ?>"><?= getFraction($i / 72) ?>"</option>
					<?php endfor; ?>
				</select>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
</div>