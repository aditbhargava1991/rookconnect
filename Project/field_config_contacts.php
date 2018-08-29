<?php error_reporting(0);
include_once('../include.php');
$lead_cats = explode(',',get_config($dbc, 'project_lead_cats'));
$co_cats = explode(',',get_config($dbc, 'project_co_lead_cats'));
$team_cats = explode(',',get_config($dbc, 'project_team_cats'));
$cat_list = array_filter(array_unique(explode(',',str_replace('Staff','',mysqli_fetch_assoc(mysqli_query($dbc,"SELECT categories FROM field_config_contacts WHERE tab='Staff' AND `categories` IS NOT NULL"))['categories']).','.get_config($dbc, 'all_contact_tabs')))); ?>
<script>
$(document).ready(function() {
	$('select').change(saveContacts);
});
function saveContacts() {
    var lead_cats = [];
	$('[name=lead_cat]').each(function() {
		lead_cats.push(this.value);
	});
    var co_cats = [];
	$('[name=co_cats]').each(function() {
		co_cats.push(this.value);
	});
    var team_cats = [];
	$('[name=team_cat]').each(function() {
		team_cats.push(this.value);
	});
	$.ajax({
		url: 'projects_ajax.php?action=setting_contacts',
		method: 'POST',
		data: {
			lead: lead_cats.join(','),
			co: co_cats.join(','),
			team: team_cats.join(',')
		}
	});
}
function addRow(a) {
    destroyInputs();
	var clone = $(a).closest('.contact-group').find('.contact-option').last().clone();
	clone.find('select').val('');
	$(a).closest('.contact-group').find('.contact-option').last().after(clone);
    initInputs();
	
	$('select').off('change',saveContacts).change(saveContacts);
}
function remRow(a) {
	if($(a).closest('.contact-group').find('.contact-option').length <= 1) {
		addRow(a);
	}
	$(a).closest('.contact-option').remove();
	saveContacts();
}
</script>
<div class="contact-group">
    <?php foreach($lead_cats as $cat) { ?>
        <div class="form-group contact-option">
            <label class="col-sm-4">Lead Category:</label>
            <div class="col-sm-7">
                <select class="chosen-select-deselect" name="lead_cat" data-placeholder="Select Category..."><option />
                    <option <?= 'Staff' == $cat ? 'selected' : '' ?> value="Staff">All Staff</option>
                    <?php foreach($cat_list as $cat_name) { ?>
                        <option <?= $cat_name == $cat ? 'selected' : '' ?> value="<?= $cat_name ?>"><?= $cat_name ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-1">
                <img src="../img/remove.png" class="pull-right inline-img" onclick="remRow(this);">
                <img src="../img/icons/ROOK-add-icon.png" class="pull-right inline-img" onclick="addRow(this);">
            </div>
            <div class="clearfix"></div>
        </div>
    <?php } ?>
    <div class="clearfix"></div>
</div>
<hr />
<div class="contact-group">
    <?php foreach($co_cats as $cat) { ?>
        <div class="form-group contact-option">
            <label class="col-sm-4">Co-Lead Category:</label>
            <div class="col-sm-7">
                <select class="chosen-select-deselect" name="co_cats" data-placeholder="Select Category..."><option />
                    <option <?= 'Staff' == $cat ? 'selected' : '' ?> value="Staff">All Staff</option>
                    <?php foreach($cat_list as $cat_name) { ?>
                        <option <?= $cat_name == $cat ? 'selected' : '' ?> value="<?= $cat_name ?>"><?= $cat_name ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-1">
                <img src="../img/remove.png" class="pull-right inline-img" onclick="remRow(this);">
                <img src="../img/icons/ROOK-add-icon.png" class="pull-right inline-img" onclick="addRow(this);">
            </div>
            <div class="clearfix"></div>
        </div>
    <?php } ?>
    <div class="clearfix"></div>
</div>
<hr />
<div class="contact-group">
    <?php foreach($team_cats as $cat) { ?>
        <div class="form-group contact-option">
            <label class="col-sm-4">Team Category:</label>
            <div class="col-sm-7">
                <select class="chosen-select-deselect" name="team_cat" data-placeholder="Select Category..."><option />
                    <option <?= 'Staff' == $cat ? 'selected' : '' ?> value="Staff">All Staff</option>
                    <?php foreach($cat_list as $cat_name) { ?>
                        <option <?= $cat_name == $cat ? 'selected' : '' ?> value="<?= $cat_name ?>"><?= $cat_name ?></option>
                    <?php } ?>
                </select>
            </div>
            <div class="col-sm-1">
                <img src="../img/remove.png" class="pull-right inline-img" onclick="remRow(this);">
                <img src="../img/icons/ROOK-add-icon.png" class="pull-right inline-img" onclick="addRow(this);">
            </div>
            <div class="clearfix"></div>
        </div>
    <?php } ?>
    <div class="clearfix"></div>
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'field_config_status.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>