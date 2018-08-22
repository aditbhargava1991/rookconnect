<?php include_once('../include.php');
checkAuthorised('expense');
error_reporting(0);
if(isset($_POST['submit'])) {
	$policy_id = $_POST['policy_id'];
	if($policy_id == 'NEW') {
		mysqli_query($dbc, "INSERT INTO `expense_policy` (`name`) VALUES ('NEW')");
		$policy_id = mysqli_insert_id($dbc);
		$before_change = "";
		$history = "Expense Policy entry has been added. <br />";
		add_update_history($dbc, 'expenses_history', $history, '', $before_change);
	}
	$fields = [];

	foreach($_POST as $field_name => $value) {
		switch($field_name) {
			case 'name': $fields[] = "`name`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'applies_to': $fields[] = "`applies_to`='".filter_var(implode(',',$value), FILTER_SANITIZE_STRING)."'"; break;
			case 'reimburse': $fields[] = "`reimburse`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'receipt': $fields[] = "`receipt`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'category': $fields[] = "`category`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'description': $fields[] = "`description`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'max_amt': $fields[] = "`max_amt`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
			case 'type': $fields[] = "`type`='".filter_var($value, FILTER_SANITIZE_STRING)."'"; break;
		}
	}
	mysqli_query($dbc,"UPDATE `expense_policy` SET ".implode(',', $fields)." WHERE `policy_id`='$policy_id'");
	$before_change = "";
	$history = "Expense policy entry has been updated for policy id $policy_id. <br />";
	add_update_history($dbc, 'expenses_history', $history, '', $before_change);
}

$policy_id = $_GET['edit'];
if($policy_id == 'NEW') {
	$policy = ['type' => $_GET['type'],'applies_to' => 'All','reimburse' => 2,'receipt' => '_%','category' => '_%','description' => '_%','max_amt' => 0];
} else if($policy_id > 0) {
	$policy = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `expense_policy` WHERE `policy_id`='$policy_id'"));
} ?>
<script>
$(document).ready(function() {
	$('.toggle-switch').click(function() {
		$(this).find('img').toggle();
		$(this).find('input').val($(this).find('input').val() == '%' ? '_%' : '%');
	});
});
$(document).on('change', 'select[name="applies_to[]"]', function() { applyToAll(this); });
function applyToAll(select) {
	if($(select).find('option[value=All]:selected').length > 0) {
		$(select).find('option:selected').removeAttr('selected');
		$(select).find('[value=All]').prop('selected','selected');
		$(select).trigger('change.select2');
	}
}
</script>
<div class="full-width-screen main-screen">
<form id="form1" name="form1" method="post" action="" enctype="multipart/form-data" class="form-horizontal" role="form">
<input type="hidden" name="policy_id" value="<?= $policy_id ?>">
	<ul class="chained-field" style="font-size: 0.65em;">
		<li class="heading-blue">Add Rule</li>
		<li class="double-pad-left double-pad-right double-pad-top">Rule Description...<br />
			<input type="text" class="form-control" name="name" value="<?= $policy['name'] ?>"></li>
		<li class="double-pad-left double-pad-right double-pad-top">This rule applies to...<br />
			<div class="col-sm-8">
				<select class="chosen-select-deselect" data-placeholder="Select Staff" name="applies_to[]" multiple><option <?= (in_array('All', explode(',',$policy['applies_to'])) ? 'selected' : '') ?> value="All">All Users</option>
					<?php $staff_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid`, `first_name`, `last_name` FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted`=0 AND `status`>0"),MYSQLI_ASSOC));
					foreach($staff_list as $staff_id) { ?>
						<option <?= (in_array($staff_id, explode(',',$policy['applies_to'])) ? 'selected' : '') ?> value="<?= $staff_id ?>"><?= get_contact($dbc, $staff_id) ?></option>
					<?php } ?>
				</select>
			</div>
		</li>
		<li class="double-pad-left double-pad-right double-pad-top">For...<br />
			<div>
                <div class="middle-valign"><input type="radio" name="reimburse" <?= $policy['reimburse'] == 2 ? 'checked' : '' ?> value="2" /></div>
                <label class="form-checkbox middle-valign">Every Expense</label>
            </div>
            <div>
                <div class="middle-valign"><input type="radio" name="reimburse" <?= $policy['reimburse'] == 1 ? 'checked' : '' ?> value="1" /></div>
                <label class="form-checkbox middle-valign">Non-Reimburseable Expenses</label>
			</div>
            <div>
                <div class="middle-valign"><input type="radio" name="reimburse" <?= $policy['reimburse'] == 0 ? 'checked' : '' ?> value="0" /></div>
                <label class="form-checkbox middle-valign"> Reimburseable Expenses</label>
            </div>
		</li>
		<li class="double-pad-left double-pad-right double-pad-top">Rule Type...<br />
			<div class="toggle-switch form-group"><input type="hidden" name="receipt" value="<?= $policy['receipt'] ?>">
				<img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" style="height: 2em; <?= $policy['receipt'] == '_%' ? 'display: none;' : '' ?>">
				<img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" style="height: 2em; <?= $policy['receipt'] == '_%' ? '' : 'display: none;' ?>"> Receipt is Required</div>
			<div class="toggle-switch form-group"><input type="hidden" name="category" value="<?= $policy['category'] ?>">
				<img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" style="height: 2em; <?= $policy['category'] == '_%' ? 'display: none;' : '' ?>">
				<img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" style="height: 2em; <?= $policy['category'] == '_%' ? '' : 'display: none;' ?>"> Category is Required</div>
			<div class="toggle-switch form-group"><input type="hidden" name="description" value="<?= $policy['description'] ?>">
				<img src="<?= WEBSITE_URL ?>/img/icons/switch-6.png" style="height: 2em; <?= $policy['description'] == '_%' ? 'display: none;' : '' ?>">
				<img src="<?= WEBSITE_URL ?>/img/icons/switch-7.png" style="height: 2em; <?= $policy['description'] == '_%' ? '' : 'display: none;' ?>"> Description is Required</div>
		</li>
		<li class="double-pad-left double-pad-right double-pad-top">Apply this rule if the Expense Amount is Greater Than or Equal To...<br />
			<input type="number" min=0 step=0.01 class="form-control" name="max_amt" value="<?= $policy['max_amt'] ?>">
		</li>
		<li class="double-pad-left double-pad-right double-pad-top">Warn or Block...<br />
			<div>
                <div class="middle-valign"><input type="radio" name="type" <?= $policy['type'] == 'Block' ? 'checked' : '' ?> value="Block" /></div>
                <label class="form-checkbox middle-valign">Block</label>
            </div>
            <div>
                <div class="middle-valign"><input type="radio" name="type" <?= $policy['type'] == 'Warn' ? 'checked' : '' ?> value="Warn" /></div>
                <label class="form-checkbox middle-valign">Warn</label>
            </div>
		</li>
	</ul>
	<div class="double-pad-left double-pad-right double-pad-top">
        <a href="" class="btn brand-btn pull-left">Cancel</a>
        <button name="submit" value="Add" class="btn brand-btn pull-right">Add</button>
        <div class="clearfix"></div>
    </div>
</form>
</div>
<div style="display:none;"><?php include('../footer.php'); ?></div>
