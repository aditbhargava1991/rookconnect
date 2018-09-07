<?php /* Field Configuration for Budget */
include_once ('../include.php');
checkAuthorised('budget');
$include = explode('#*#', get_config($dbc, 'chart_accts_expense'));
$disable = explode('#*#', get_config($dbc, 'chart_accts_liabilities').'#*#'.get_config($dbc, 'chart_accts_assets')); ?>
<script>
$(document).ready(function() {
	$('[name=chart_items]:not([disabled])').change(function() {
		var chart_items = [];
		$('[name=chart_items]:checked').each(function() {
			chart_items.push(this.value);
		});
		$.post('budget_ajax_all.php?action=general_config', {
			name: 'chart_accts_expense',
			value: chart_items.join('#*#')
		});
	});
});
</script>

<h3>Expenses</h3>
	<label class="form-checkbox"><input type="checkbox" <?= in_array("all_expenses",$include) ? "checked" : (in_array("all_expenses", $disable) || in_array_starts('expense_',$include) || in_array_starts('expense_',$disable) ? 'disabled' : '') ?> value="all_expenses" onclick="if(this.checked) { $('[value^=expense_]').prop('disabled',true); } else { $('[value^=expense_]').removeAttr('disabled'); }" name="chart_items">All Expenses</label>
	<?php $expense_cats = $dbc->query("SELECT `category`, `ec` FROM `expense_categories` WHERE `deleted`=0 AND IFNULL(`category`,'') != '' GROUP BY `category` ORDER BY `ec`,`category`");
	while($cat = $expense_cats->fetch_assoc()) { ?>
		<label class="form-checkbox"><input type="checkbox" <?= in_array("expense_".$cat['category'],$include) ? "checked" : (in_array("expense_".$cat['category'], $disable) || in_array("all_expenses", $disable) || in_array("all_expenses", $include) ? 'disabled' : '') ?> value="expense_<?= $cat['category'] ?>" name="chart_items"><?= (!empty($cat['ec']) ? $cat['ec'].': ' : '').$cat['category'] ?></label>
	<?php } ?>