<script>
$(document).ready(function() {
	$('#project_fields').find('input,select').change(saveFields);
	saveFields();
});
$(document).on('change', 'select[name="project_type_dropdown"]', function() { window.location.href='?settings=fields&type='+this.value; });
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
			url: 'projects_ajax.php?action=setting_fields',
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
	<?php $field_config = array_filter(array_unique(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='$projecttype'"))['config_fields'])));
	$all_config = array_filter(array_unique(explode(',',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT `config_fields` FROM field_config_project WHERE type='ALL' AND '$projecttype' != 'ALL'"))['config_fields'])));
	if(strpos(','.implode(',',$field_config).','.implode(',',$all_config),',DB ') === FALSE) {
		$field_config = array_merge($field_config,['DB Project','DB Review','DB Business','DB Contact','DB Status','DB Billing','DB Type','DB Follow Up','DB Assign','DB Colead']);
	}
	if(count($field_config) == 0 && count($all_config) == 0) {
		$field_config = explode(',','Information Contact Region,Information Contact Location,Information Contact Classification,Information Business,Information Contact,Information Rate Card,Information Project Type,Information Project Short Name,Details Detail,Dates Project Created Date,Dates Project Start Date,Dates Estimate Completion Date,Dates Effective Date,Dates Time Clock Start Date');
	} ?>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_TILE ?> Dashboard Summary<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?= in_array('SUMM Favourite', $all_config) ? 'checked disabled' : (in_array('SUMM Favourite',$field_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="SUMM Favourite">Favourite <?= PROJECT_TILE ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('SUMM Types', $all_config) ? 'checked disabled' : (in_array('SUMM Types',$field_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="SUMM Types"><?= PROJECT_TILE ?> by Type</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('SUMM Colors', $all_config) ? 'checked disabled' : (in_array('SUMM Colors',$field_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="SUMM Colors">Color Blocks</label>

		<label class="form-checkbox"><input type="checkbox" <?= in_array('SUMM Region', $all_config) ? 'checked disabled' : (in_array('SUMM Region',$field_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="SUMM Region"><?= PROJECT_TILE ?> by Region</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('SUMM Leads', $all_config) ? 'checked disabled' : (in_array('SUMM Leads',$field_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="SUMM Leads"><?= PROJECT_NOUN ?> Leads</label>

		<label class="form-checkbox"><input type="checkbox" <?= in_array('SUMM Colead', $all_config) ? 'checked disabled' : (in_array('SUMM Colead',$field_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="SUMM Colead"><?= PROJECT_NOUN ?> Co-Lead</label>

		<label class="form-checkbox"><input type="checkbox" <?= in_array('SUMM Estimated', $all_config) ? 'checked disabled' : (in_array('SUMM Estimated',$field_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="SUMM Estimated"><?= PROJECT_NOUN ?> Estimated Time</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('SUMM Tracked', $all_config) ? 'checked disabled' : (in_array('SUMM Tracked',$field_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="SUMM Tracked"><?= PROJECT_NOUN ?> Actual Time</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('SUMM Status', $all_config) ? 'checked disabled' : (in_array('SUMM Status',$field_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="SUMM Status"><?= PROJECT_NOUN ?> Status</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('SUMM Business', $all_config) ? 'checked disabled' : (in_array('SUMM Business',$field_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="SUMM Business"><?= BUSINESS_CAT ?></label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('SUMM Contacts', $all_config) ? 'checked disabled' : (in_array('SUMM Contacts',$field_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="SUMM Contacts">Contacts</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('SUMM Piece', $all_config) ? 'checked disabled' : (in_array('SUMM Piece',$field_config) && $projecttype == 'ALL' ? 'checked' : ($projecttype != 'ALL' ? 'disabled' : '')) ?> name="project_fields[]" value="SUMM Piece">Piece Work <?= TICKET_NOUN ?></label>

	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_TILE ?> Dashboard<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<label class="form-checkbox"><input type="checkbox" checked onclick="return false;" name="project_fields[]" value="DB Project"><?= PROJECT_NOUN ?> Name</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Review', $all_config) ? 'checked disabled' : (in_array('DB Review',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Review"><?= PROJECT_NOUN ?> Review</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Action Items', $all_config) ? 'checked disabled' : (in_array('DB Action Items',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Action Items"><?= PROJECT_NOUN ?> Action Items</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Business', $all_config) ? 'checked disabled' : (in_array('DB Business',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Business">Business Name</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Contact', $all_config) ? 'checked disabled' : (in_array('DB Contact',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Contact">Contact Name</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Status', $all_config) ? 'checked disabled' : (in_array('DB Status',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Status"><?= PROJECT_NOUN ?> Status</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Billing', $all_config) ? 'checked disabled' : (in_array('DB Billing',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Billing"><?= PROJECT_NOUN ?> Billing</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Type', $all_config) ? 'checked disabled' : (in_array('DB Type',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Type"><?= PROJECT_NOUN ?> Type</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Follow Up', $all_config) ? 'checked disabled' : (in_array('DB Follow Up',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Follow Up"><?= PROJECT_NOUN ?> Follow Up</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Deadline', $all_config) ? 'checked disabled' : (in_array('DB Deadline',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Deadline"><?= PROJECT_NOUN ?> Deadline</label>

		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Assign', $all_config) ? 'checked disabled' : (in_array('DB Assign',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Assign"><?= PROJECT_NOUN ?> Lead</label>

		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Colead', $all_config) ? 'checked disabled' : (in_array('DB Colead',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Colead"><?= PROJECT_NOUN ?> Co-Lead</label>

		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Milestones', $all_config) ? 'checked disabled' : (in_array('DB Milestones',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Milestones"><?= PROJECT_NOUN ?> Milestones</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('DB Status List', $all_config) ? 'checked disabled' : (in_array('DB Status List',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Status List">Scrum Board</label>

        <label class="form-checkbox"><input type="checkbox" <?= in_array('DB Total Tickets', $all_config) ? 'checked disabled' : (in_array('DB Total Tickets',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="DB Total Tickets">Total Tickets</label>

        <select class="chosen-select-deselect" name="project_sorting"><?php $sorting = get_config($dbc, 'project_sorting'); ?>
			<option <?= $sorting == 'newest' ? 'selected' : '' ?> value="newest">Newest to Oldest</option>
			<option <?= $sorting == 'oldest' ? 'selected' : '' ?> value="oldest">Oldest to Newest</option>
			<option <?= $sorting == 'project' ? 'selected' : '' ?> value="project">Alphabetical by Project Name</option>
			<option <?= $sorting == 'business' ? 'selected' : '' ?> value="business">Alphabetical by Business</option>
			<option <?= $sorting == 'sites' ? 'selected' : '' ?> value="sites">Alphabetical by Site</option>
			<option <?= $sorting == 'contact' ? 'selected' : '' ?> value="contact">Alphabetical by First Contact</option>
		</select>
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Information<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Contact Region', $all_config) ? 'checked disabled' : (in_array('Information Contact Region',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Contact Region">Region</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Contact Location', $all_config) ? 'checked disabled' : (in_array('Information Contact Location',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Contact Location">Location</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Contact Classification', $all_config) ? 'checked disabled' : (in_array('Information Contact Classification',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Contact Classification">Classification</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Project Short Name', $all_config) ? 'checked disabled' : (in_array('Information Project Short Name',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Project Short Name"><?= PROJECT_NOUN ?> Short Name</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Business', $all_config) ? 'checked disabled' : (in_array('Information Business',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Business">Business</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Contact', $all_config) ? 'checked disabled' : (in_array('Information Contact',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Contact">Contact</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Site', $all_config) ? 'checked disabled' : (in_array('Information Site',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Site">Site</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Rate Card', $all_config) ? 'checked disabled' : (in_array('Information Rate Card',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Rate Card">Rate Card</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Project Type', $all_config) ? 'checked disabled' : (in_array('Information Project Type',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Project Type">Type</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information AFE', $all_config) ? 'checked disabled' : (in_array('Information AFE',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information AFE">AFE #</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Color Code', $all_config) ? 'checked disabled' : (in_array('Color Code',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Color Code">Color Code</label>
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Staff<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Assign', $all_config) ? 'checked disabled' : (in_array('Information Assign',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Assign"><?= PROJECT_NOUN ?> Lead</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Colead', $all_config) ? 'checked disabled' : (in_array('Information Colead',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Colead"><?= PROJECT_NOUN ?> Co-Lead</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Team', $all_config) ? 'checked disabled' : (in_array('Information Team',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Team"><?= PROJECT_NOUN ?> Team</label>
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Details<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<?php include('../Estimate/arr_detail_types.php');
		foreach($detail_types as $config_str => $field) { ?>
			<label class="form-checkbox"><input type="checkbox" <?= in_array($config_str, $all_config) ? 'checked disabled' : (in_array($config_str,$field_config) ? 'checked' : '') ?> name="project_fields[]" value="<?= $config_str ?>"><?= $field[0] ?></label>
		<?php }
		if($projecttype != 'ALL') {
			foreach(explode('#*#',get_config($dbc, 'project_ALL_detail_types')) as $detail_type) { ?>
				<label class="form-checkbox"><input type="checkbox" checked disabled name="project_fields[]" value="<?= $detail_type ?>"><?= $detail_type ?></label>
			<?php }
		}
		foreach(explode('#*#',get_config($dbc, 'project_'.$projecttype.'_detail_types')) as $detail_type) { ?>
			<label class="form-checkbox">
				<div class="col-sm-8"><input type="text" name="detail_types" class="form-control" data-config="project_<?= $projecttype ?>_detail_types" value="<?= $detail_type ?>"></div>
				<div class="col-sm-4"><img src="../img/remove.png" class="inline-img" onclick="remDetail(this);"><img src="../img/icons/ROOK-add-icon.png" class="inline-img" onclick="addDetail();"></div>
			</label>
		<?php } ?>
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Dates<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Dates Project Created Date', $all_config) ? 'checked disabled' : (in_array('Dates Project Created Date',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Dates Project Created Date">Created Date</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Dates Project Start Date', $all_config) ? 'checked disabled' : (in_array('Dates Project Start Date',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Dates Project Start Date">Start Date</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Information Followup', $all_config) ? 'checked disabled' : (in_array('Information Followup',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Information Followup"><?= PROJECT_NOUN ?> Follow Up Date</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Dates Deadline', $all_config) ? 'checked disabled' : (in_array('Dates Deadline',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Dates Deadline">Deadline</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Dates Estimate Completion Date', $all_config) ? 'checked disabled' : (in_array('Dates Estimate Completion Date',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Dates Estimate Completion Date">Estimated Completion Date</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Dates Effective Date', $all_config) ? 'checked disabled' : (in_array('Dates Effective Date',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Dates Effective Date">Effective Date</label>
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Dates Time Clock Start Date', $all_config) ? 'checked disabled' : (in_array('Dates Time Clock Start Date',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Dates Time Clock Start Date">Time Clock Start Date</label>
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Reporting<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Reporting Track Time', $all_config) ? 'checked disabled' : (in_array('Reporting Track Time',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Reporting Track Time">Get To Work / Track Time</label>
	</div>
	<div class="clearfix"></div>
	<label class="col-sm-4" onclick="$(this).next('div').toggle(); $(this).find('img').toggleClass('counterclockwise');"><?= PROJECT_NOUN ?> Billing<img class="pull-right black-color inline-img" src="../img/icons/dropdown-arrow.png"></label>
	<div class="block-group col-sm-8">
		<label class="form-checkbox"><input type="checkbox" <?= in_array('Billing Ticket Lines', $all_config) ? 'checked disabled' : (in_array('Billing Ticket Lines',$field_config) ? 'checked' : '') ?> name="project_fields[]" value="Billing Ticket Lines"><?= TICKET_NOUN ?> Lines</label>
	</div>
	<div class="clearfix"></div>
</div>
<?php include('../Project/field_config_fields_custom.php'); ?>