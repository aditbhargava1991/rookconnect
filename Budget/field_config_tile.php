<?php include_once('../include.php');
checkAuthorised('budget');
if(empty($tab_config)) {
	$tab_config = get_config($dbc, 'budget_tabs');
	if(empty(trim($tab_config,','))) {
		$tab_config = ',pending_budget,active_budget,expense_tracking,';
	}
	$tab_config = array_values(array_filter(explode(',',$tab_config)));
} ?>
<script>
$(document).ready(function() {
	$('[name=budget_tabs]').change(function() {
		var tabs = [];
		$('[name=budget_tabs]:checked').each(function() {
			tabs.push(this.value);
		});
		$.post('budget_ajax_all.php?action=general_config', {
			name: 'budget_tabs',
			value: tabs.join(',')
		});
	});
});
</script>
<h3>Enable Tabs</h3>
<label class="form-checkbox"><input type="checkbox" <?= in_array("pending_budget",$tab_config) ? "checked" : "" ?> value="pending_budget" style="height: 20px; width: 20px;" name="budget_tabs">Pending Budgets</label>
<label class="form-checkbox"><input type="checkbox" <?= in_array("active_budget",$tab_config) ? "checked" : "" ?> value="active_budget" style="height: 20px; width: 20px;" name="budget_tabs">Active Budgets</label>
<label class="form-checkbox"><input type="checkbox" <?= in_array("expense_tracking",$tab_config) ? "checked" : "" ?> value="expense_tracking" style="height: 20px; width: 20px;" name="budget_tabs">Expense Tracking</label>
<label class="form-checkbox"><input type="checkbox" <?= in_array("assets",$tab_config) ? "checked" : "" ?> value="assets" style="height: 20px; width: 20px;" name="budget_tabs">Chart of Assets</label>
<label class="form-checkbox"><input type="checkbox" <?= in_array("liabilities",$tab_config) ? "checked" : "" ?> value="liabilities" style="height: 20px; width: 20px;" name="budget_tabs">Chart of Liabilities</label>
<label class="form-checkbox"><input type="checkbox" <?= in_array("expense",$tab_config) ? "checked" : "" ?> value="expense" style="height: 20px; width: 20px;" name="budget_tabs">Chart of Expenses</label>