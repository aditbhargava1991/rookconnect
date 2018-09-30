<script>
$(document).ready(function() {
	$('#project_fields').find('input,select').change(saveFields);
	saveFields();
});
$(document).on('change', 'select[name="project_type_dropdown"]', function() { window.location.href='?settings=mandatory_fields&type='+this.value; });
function saveFields() {
	if(this.name == 'project_sorting') {
		$.ajax({
			url: 'projects_ajax.php?action=setting_tile',
			method: 'POST',
			data: {
				field: 'project_sorting',
				value: this.value
			}
		});
	} else {
		var field_list = [];
		$('[name="project_fields[]"]:checked').not(':disabled').each(function() {
			field_list.push(this.value);
		});
		var detail_list = [];
		var config = '';
		$('[name=detail_types]').each(function() {
			// if(this.value != '') {
				detail_list.push(this.value);
				config = $(this).data('config');
			// }
		});
		$.ajax({
			url: 'projects_ajax.php?action=setting_mandatory_fields',
			method: 'POST',
			data: {
				projects: $('[name=project_type]').val(),
				fields: field_list,
				detail_config: config,
				details: detail_list
			},
			success: function(response) {console.log(response);}
		});
	}
}
function addDetail() {
	var detail = $('[name=detail_types]').last().closest('label');
	var clone = detail.clone();
	clone.find('input').val('');
	detail.after(clone);
	$('#project_fields').find('input').off('change').change(saveFields);
	setTimeout(function() {	$('[name=detail_types]').last().focus(); }, 1);
}
function remDetail(img) {
	if($('[name=detail_types]').length <= 1) {
		addDetail();
	}
	$(img).closest('label').remove();
	saveFields();
}
</script>
<h3>Activate Fields</h3>
<div id="project_fields">
	<label class="col-sm-4"><?= PROJECT_NOUN ?> Type</label>
	<?php $projecttype = filter_var($_GET['type'],FILTER_SANITIZE_STRING); ?>
	<div class="col-sm-8">
		<select name="project_type_dropdown" class="chosen-select-deselect">
			<option></option>
			<?php $projecttype = (empty($projecttype) ? 'ALL' : $projecttype); ?>
			<option <?= $projecttype == 'ALL' ? 'selected' : '' ?> value="ALL">Activate Fields for All <?= PROJECT_TILE ?></option>
			<?php foreach(explode(',',get_config($dbc, 'project_tabs')) as $type_name) {
				$type = preg_replace('/[^a-z_,\']/','',str_replace(' ','_',strtolower($type_name))); ?>
				<option <?= $projecttype == $type ? 'selected' : '' ?> value="<?= $type ?>"><?= $type_name ?></option>
			<?php } ?>
		</select>
	</div>
	<input type="hidden" name="project_type" value="<?= $projecttype ?>">
	<div class="clearfix"></div>
	<?php
	$field_config = array_filter(array_unique(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))['config_fields'])));
	$all_config = array_filter(array_unique(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL' AND '$projecttype' != 'ALL'"))['config_fields'])));
	$field_mandatory_config = array_filter(array_unique(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_mandatory_project WHERE type='$projecttype'"))['config_fields'])));
	$all_mandatory_config = array_filter(array_unique(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_mandatory_project WHERE type='ALL' AND '$projecttype' != 'ALL'"))['config_fields'])));
	if(strpos(','.implode(',',$field_config).','.implode(',',$all_config),',DB ') === FALSE) {
		$field_config = array_merge($field_config,['DB Project','DB Review','DB Business','DB Contact','DB Status','DB Billing','DB Type','DB Follow Up','DB Assign','DB Colead']);
	}
	if(count($field_config) == 0 && count($all_config) == 0) {
		$field_config = explode(',','Information Contact Region,Information Contact Location,Information Contact Classification,Information Business,Information Contact,Information Rate Card,Information Project Type,Information Project Short Name,Details Detail,Dates Project Created Date,Dates Project Start Date,Dates Estimate Completion Date,Dates Effective Date,Dates Time Clock Start Date');
	} ?>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_TILE ?> Dashboard Summary<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<?php
		$field_sort_order = array('SUMM Favourite','SUMM Types','SUMM Colors','SUMM Region','SUMM Leads','SUMM Colead','SUMM Estimated','SUMM Tracked','SUMM Status','SUMM Business','SUMM Contacts','SUMM Piece');
		$selected_field_order = array_unique(array_merge(array_intersect($field_sort_order, $field_config), array_intersect($field_sort_order, $all_config)));
		?>
		<?php foreach ($selected_field_order as $selected_field_field) {
			if($selected_field_field != '' || $selected_field_field != null) { ?>
				<label class="form-checkbox">
					<input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $field_mandatory_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="<?php echo $selected_field_field; ?>">
					<?= PROJECT_TILE ?> <?php echo substr($selected_field_field, 5); ?>
				</label>
		<?php }
		} ?>
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_TILE ?> Dashboard<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<?php
		$field_sort_order = array('DB Review','DB Action Items','DB Business','DB Contact','DB Status','DB Billing','DB Type','DB Follow Up','DB Deadline','DB Assign',
		'DB Colead','DB Milestones','DB Status List','DB Total Tickets');
		$selected_field_order = array_unique(array_merge(array_intersect($field_sort_order, $field_config), array_intersect($field_sort_order, $all_config)));
		?>
		<?php foreach ($selected_field_order as $selected_field_field) {
			if($selected_field_field != '' || $selected_field_field != null) { ?>
				<label class="form-checkbox">
					<input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $field_mandatory_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="<?php echo $selected_field_field; ?>">
					<?= PROJECT_TILE ?> <?php echo substr($selected_field_field, 3); ?>
				</label>
		<?php }
		} ?>
  </div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Information<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<?php
		$field_sort_order = array('Information Contact Region','Information Contact Location','Information Contact Classification',
		'Information Project Short Name','Information Business',
		'Information Contact','Information Site','Information Rate Card','Information Project Type','Information AFE');
		$selected_field_order = array_unique(array_merge(array_intersect($field_sort_order, $field_config), array_intersect($field_sort_order, $all_config)));
		?>
		<?php foreach ($selected_field_order as $selected_field_field) {
			if($selected_field_field != '' || $selected_field_field != null) { ?>
				<label class="form-checkbox">
					<input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $field_mandatory_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="<?php echo $selected_field_field; ?>">
					<?php echo substr($selected_field_field, 12); ?>
				</label>
		<?php }
		} ?>
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Staff<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<?php
		$field_sort_order = array('Information Assign','Information Colead','Information Team');
		$selected_field_order = array_unique(array_merge(array_intersect($field_sort_order, $field_config), array_intersect($field_sort_order, $all_config)));
		?>
		<?php foreach ($selected_field_order as $selected_field_field) {
			if($selected_field_field != '' || $selected_field_field != null) { ?>
				<label class="form-checkbox">
					<input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $field_mandatory_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="<?php echo $selected_field_field; ?>">
					<?php echo substr($selected_field_field, 12); ?>
				</label>
		<?php }
		} ?>
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Details<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<?php include('../Estimate/arr_detail_types.php');
		foreach($detail_types as $config_str => $field) { ?>
			<?php if(in_array($config_str, $all_config) || in_array($config_str, $field_config)): ?>
				<label class="form-checkbox"><input type="checkbox" <?= in_array($config_str, $all_mandatory_config) ? 'checked disabled' : (in_array($config_str,$field_mandatory_config) ? 'checked' : '') ?> name="project_fields[]" value="<?= $config_str ?>"><?= $field[0] ?></label>
			<?php endif; ?>
		<?php }
		if($projecttype != 'ALL') {
			foreach(explode('#*#',get_config($dbc, 'project_ALL_detail_types')) as $detail_type) { ?>
				<label class="form-checkbox"><input type="checkbox" checked disabled name="project_fields[]" value="<?= $detail_type ?>"><?= $detail_type ?></label>
			<?php }
		}
	?>
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Dates<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<?php
		$field_sort_order = array('Dates Project Created Date','Dates Project Start Date','Dates Deadline','Dates Estimate Completion Date','Dates Effective Date','Dates Time Clock Start Date');
		$selected_field_order = array_unique(array_merge(array_intersect($field_sort_order, $field_config), array_intersect($field_sort_order, $all_config)));
		?>
		<?php foreach ($selected_field_order as $selected_field_field) {
			if($selected_field_field != '' || $selected_field_field != null) { ?>
				<label class="form-checkbox">
					<input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $field_mandatory_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="<?php echo $selected_field_field; ?>">
					<?php echo substr($selected_field_field, 14); ?>
				</label>
		<?php }
		} ?>
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Reporting<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<?php
		$field_sort_order = array('Reporting Track Time');
		$selected_field_order = array_unique(array_merge(array_intersect($field_sort_order, $field_config), array_intersect($field_sort_order, $all_config)));
		?>
		<?php foreach ($selected_field_order as $selected_field_field) {
			if($selected_field_field != '' || $selected_field_field != null) { ?>
				<label class="form-checkbox">
					<input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $field_mandatory_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="<?php echo $selected_field_field; ?>">
					<?php echo "Get To Work / Track Time"; ?>
				</label>
		<?php }
		} ?>
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Billing<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<?php
		$field_sort_order = array('Billing Ticket Lines');
		$selected_field_order = array_unique(array_merge(array_intersect($field_sort_order, $field_config), array_intersect($field_sort_order, $all_config)));
		?>
		<?php foreach ($selected_field_order as $selected_field_field) {
			if($selected_field_field != '' || $selected_field_field != null) { ?>
				<label class="form-checkbox">
					<input type="checkbox" <?= in_array($selected_field_field, $all_mandatory_config) ? 'checked disabled' : (in_array($selected_field_field, $field_mandatory_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="<?php echo $selected_field_field; ?>">
					<?= TICKET_NOUN ?> Lines
				</label>
		<?php }
		} ?>
	</div>
	<div class="clearfix"></div>
</div>
