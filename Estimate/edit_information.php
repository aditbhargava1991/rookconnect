<?php include_once('../include.php');
checkAuthorised('estimate');
if(!isset($estimate)) {
	$estimateid = filter_var($estimateid,FILTER_SANITIZE_STRING);
	$estimate = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `estimate` WHERE `estimateid`='$estimateid'"));
} ?>
<script>
$(document).on('change', 'select[name="businessid"]', function() { filterContacts(this); });
$(document).on('change', 'select[name="clientid"]', function() { filterContacts(this); });
$(document).on('change', 'select[name="siteid"]', function() { filterContacts(this); });
function addType() {
	var row = $('[name="estimatetype[]"]').last().closest('.form-group');
	var clone = row.clone();
	resetChosen(clone.find("select[class^=chosen]"));
	row.after(clone);
	$('input,select').off('change', saveField).change(saveField);
	$('[name="estimatetype[]"]').last().focus();
}
function removeType(img) {
	if($('[name="estimatetype[]"]').length <= 1) {
		addType();
	}
	$(img).closest('.form-group').remove();
	$('[name="estimatetype[]"]').last().change();
}
function add_follow_up() {
	$.ajax({
		url: 'estimates_ajax.php?action=estimate_fields',
		method: 'POST',
		data: {
			table: 'estimate_actions',
			id: '',
			id_field: 'id',
			estimate: '<?= $estimateid ?>',
			value: '',
			field: ''
		},
		success: function(response) {
			window.location.reload();
		}
	});
}
function remove_follow_up(elem) {
	var result = confirm("Are you sure you want to delete this follow up?");
	if (result) {
		$.ajax({
            url: 'estimates_ajax.php?action=estimate_fields',
            method: 'POST',
            data: {
                table: 'estimate_actions',
                id: $(elem).data('id'),
                id_field: 'id',
                estimate: '<?= $estimateid ?>',
                value: '',
                field: 'delete'
            },
            success: function(response) {
                window.location.reload();
            }
        });
	}
}
function filterContacts(select) {
	if(select.name == 'businessid') {
		$('[name=clientid]').find('option').hide().filter('[data-businessid='+select.value+']').show();
		$('[name=siteid]').find('option').hide().filter('[data-businessid='+select.value+']').show();
	} else if($(select).find('option:selected').data('businessid') > 0) {
		$('[name=businessid]').val($(select).find('option:selected').data('businessid')).change();
	}
}
</script>
<div class="form-horizontal col-sm-12" data-tab-name="information">
	<h3><?= rtrim(ESTIMATE_TILE, 's') ?> Information</h3>
	<?php $estimate_type_options = explode(',',get_config($dbc, 'estimate_tabs'));
    foreach(explode(',',$estimate['estimatetype']) as $current_type) { ?>
		<div class="form-group">
			<label class="col-sm-4"><?= rtrim(ESTIMATE_TILE, 's') ?> Type:</label>
			<div class="col-sm-7">
				<select class="chosen-select-deselect" name="estimatetype[]" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>">
					<option></option>
					<?php foreach($estimate_type_options as $est_type) {
                        if($est_type == 'one_time_est_type') { ?>
                            <option <?= $current_type == $est_type ? 'selected' : '' ?> value="<?= $est_type ?>">One Time <?= rtrim(ESTIMATE_TILE,'s') ?></option>
                        <?php } else if (strpos($est_type,'ticket_est_') === 0) { ?>
                            <option <?= $current_type == $est_type ? 'selected' : '' ?> value="<?= $est_type ?>"><?= str_replace('ticket_est_','',$est_type) ?></option>
                        <?php } else { ?>
                            <option <?= $current_type == $est_type ? 'selected' : '' ?> value="<?= $est_type ?>"><?= $est_type ?></option>
                        <?php } ?>
					<?php } ?>
				</select>
			</div>
			<div class="col-sm-1">
				<img src="../img/icons/ROOK-add-icon.png" class="inline-img pull-right" onclick="addType();">
				<img src="../img/remove.png" class="inline-img pull-right" onclick="removeType(this);">
			</div>
		</div>
	<?php } ?>
	<?php if(in_array('Business',$config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Business:</label>
			<div class="col-sm-8">
				<select class="chosen-select-deselect" name="businessid" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>">
					<option></option>
					<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `name` FROM `contacts` WHERE `category` LIKE 'Business' AND `deleted`=0 AND `status`>0")) as $business) { ?>
						<option <?= $estimate['businessid'] == $business['contactid'] ? 'selected' : '' ?> value="<?= $business['contactid'] ?>"><?= $business['name'] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php } ?>
	<?php if(in_array('Contact',$config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Contact:</label>
			<div class="col-sm-8">
				<select class="chosen-select-deselect" name="clientid" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>">
					<option></option>
					<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `last_name`, `first_name`, `businessid` FROM `contacts` WHERE `category` NOT IN ('Business','Sites',".STAFF_CATS.") AND `deleted`=0 AND `status`>0")) as $contact) { ?>
						<option <?= $estimate['clientid'] == $contact['contactid'] ? 'selected' : ($estimate['businessid'] > 0 && $estimate['businessid'] != $contact['businessid'] ? 'style="display:none;"' : '') ?> value="<?= $contact['contactid'] ?>" data-businessid="<?= $contact['businessid'] ?>"><?= $contact['first_name'].' '.$contact['last_name'] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php } ?>
	<?php if(in_array('Site',$config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Site:</label>
			<div class="col-sm-8">
				<select class="chosen-select-deselect" name="siteid" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>">
					<option></option>
					<?php foreach(sort_contacts_query(mysqli_query($dbc, "SELECT `contactid`, `site_name`, `businessid` FROM `contacts` WHERE `category` LIKE 'Sites' AND `deleted`=0 AND `status`>0"),'site_name') as $site) { ?>
						<option <?= $estimate['siteid'] == $site['contactid'] ? 'selected' : ($estimate['businessid'] > 0 && $estimate['businessid'] != $site['businessid'] ? 'style="display:none;"' : '') ?> value="<?= $site['contactid'] ?>" data-businessid="<?= $site['businessid'] ?>"><?= $site['site_name'] ?></option>
					<?php } ?>
				</select>
			</div>
		</div>
	<?php } ?>
	<?php if(in_array('AFE',$config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Customer AFE#:</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="afe_number" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" value="<?= $estimate['afe_number'] ?>">
			</div>
		</div>
	<?php } ?>
		<div class="form-group">
			<label class="col-sm-4"><?= rtrim(ESTIMATE_TILE, 's') ?> Name:</label>
			<div class="col-sm-8">
				<input type="text" class="form-control" name="estimate_name" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>" value="<?= $estimate['estimate_name'] ?>">
			</div>
		</div>
	<?php if(in_array('Terms',$config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Payment Terms:</label>
			<div class="col-sm-8">
				<div class="col-sm-12"><select class="chosen-select-deselect" name="payment_terms" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>">
					<option></option>
					<?php $quote_payment_term = explode('#*#', get_config($dbc, 'quote_payment_term'));
					foreach($quote_payment_term as $terms) {
						echo "<option ".($estimate['payment_terms'] == $terms ? 'selected' : '')." value='$terms'>$terms</option>";
					}
					if(!in_array($estimate['payment_terms'], $quote_payment_term)) {
						echo "<option selected value='".$estimate['payment_terms']."'>".$estimate['payment_terms']."</option>";
					} ?>
					<option value="CUSTOM">Custom Terms:</option>
				</select></div>
				<div class="col-sm-11" style="display:none;"><input type="text" class="form-control" name="payment_terms" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>"></div>
				<div class="col-sm-1" style="display:none;"><a href="" onclick="$('select[name=payment_terms]').val('').trigger('change.select2'); $(this).closest('.col-sm-8').find('div[class^=col-sm]').hide(); $(this).closest('.form-group').find('.col-sm-12').show(); $('input[name=payment_terms]').prop('disabled','disabled'); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a></div>
			</div>
		</div>
	<?php } ?>
	<?php if(in_array('Due',$config)) { ?>
		<div class="form-group">
			<label class="col-sm-4">Payment Due:</label>
			<div class="col-sm-8">
				<div class="col-sm-12"><select class="chosen-select-deselect" name="payment_due" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>">
					<option></option>
					<?php $quote_due_period = explode('#*#', get_config($dbc, 'quote_due_period'));
					foreach($quote_due_period as $period) {
						echo "<option ".($estimate['payment_due'] == $period ? 'selected' : '')." value='$period'>$period</option>";
					}
					if(!in_array($estimate['payment_due'], $quote_due_period)) {
						echo "<option selected value='".$estimate['payment_due']."'>".$estimate['payment_due']."</option>";
					} ?>
					<option value="CUSTOM">Custom Due:</option>
				</select></div>
				<div class="col-sm-11" style="display:none;"><input type="text" class="form-control" name="payment_due" data-table="estimate" data-id-field="estimateid" data-id="<?= $estimateid ?>"></div>
				<div class="col-sm-1" style="display:none;"><a href="" onclick="$('select[name=payment_due]').val('').trigger('change.select2'); $(this).closest('.col-sm-8').find('div[class^=col-sm]').hide(); $(this).closest('.form-group').find('.col-sm-12').show(); $('input[name=payment_due]').prop('disabled','disabled'); return false;"><img src="<?= WEBSITE_URL ?>/img/remove.png"></a></div>
			</div>
		</div>
	<?php } ?>
	<?php $actions = mysqli_query($dbc, "SELECT * FROM `estimate_actions` WHERE `estimateid`='$estimateid' AND `deleted`=0 ORDER BY `due_date` ASC");
	while($action = mysqli_fetch_array($actions)) { ?>
		<hr />

        <div class="action-group">
			<div class="form-group">
				<label class="col-sm-4">Next Action:</label>
				<div class="col-sm-8">
					<?php if($action['completed']) {
						switch($action['action']) {
							case 'phone': echo "Phone Call"; break;
							case 'email': echo "Email"; break;
							default: echo "N/A"; break;
						}
					} else { ?>
						<select name="action" class="chosen-select-deselect" data-table="estimate_actions" data-id="<?= $action['id'] ?>" data-id-field="id" data-estimate="<?= $estimateid ?>">
							<option></option>
							<option <?= $action['action'] == 'phone' ? 'selected' : '' ?> value="phone">Phone Call</option>
							<option <?= $action['action'] == 'email' ? 'selected' : '' ?> value="email">Email</option>
						</select>
					<?php } ?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4">Follow Up Date:</label>
				<div class="col-sm-8">
					<input type="text" name="due_date" <?= $action['completed'] ? 'disabled' : '' ?> class="form-control datepicker" value="<?= $action['due_date'] ?>" data-table="estimate_actions" data-id="<?= $action['id'] ?>" data-id-field="id" data-estimate="<?= $estimateid ?>">
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4">Mark Completed:</label>
				<div class="col-sm-8">
					<?php if($action['completed']) { ?>
						<input type="checkbox" name="check_completed" class="form-checkbox" value="1" checked disabled>
					<?php } else { ?>
						<input type="checkbox" name="check_completed" class="form-checkbox" value="1" onchange="$(this).closest('div').find('[name=completed]').focus();">
						<input type="text" name="completed" class="checkbox-text form-control" data-table="estimate_actions" data-id="<?= $action['id'] ?>" data-estimate="<?= $estimateid ?>" data-id-field="id" placeholder="Follow Up Details" onblur="if(this.value == '') { $(this).closest('.form-group').find('[name=check_completed]').removeAttr('checked'); alert('You must provide details about the follow up.'); }">
					<?php } ?>
				</div>
			</div>
            <div class="form-group">
                <a class="pull-right" href="javascript:void(0);" onclick="add_follow_up(); return false;"><img src="../img/icons/ROOK-add-icon.png" class="inline-img image-btn" alt="Add Follow Up" /></a>
                <a class="pull-right" href="javascript:void(0);" data-id="<?= $action['id'] ?>" onclick="remove_follow_up(this); return false;"><img src="../img/remove.png" class="inline-img" alt="Remove Folow Up" width="25" /></a>
            </div>
		</div>
	<?php } ?>

    <div class="clearfix"></div>
    <hr />
</div>
<?php if(basename($_SERVER['SCRIPT_FILENAME']) == 'edit_information.php') { ?>
	<div style="display:none;"><?php include('../footer.php'); ?></div>
<?php } ?>