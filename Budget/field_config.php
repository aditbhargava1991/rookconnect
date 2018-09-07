<?php /* Field Configuration for Budget */
include_once ('../include.php');
checkAuthorised('budget');

if (isset($_POST['submit'])) {
    echo '<script type="text/javascript"> window.location.replace("?tab='.$_GET['tab'].'&type='.$_GET['type'].'"); </script>';
} ?>

<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
	<?php switch($_GET['type']) {
		case 'tile': include('field_config_tile.php'); break;
		case 'pending_budget': include('field_config_pending.php'); break;
		case 'active_budget': include('field_config_active.php'); break;
		case 'expense_tracking': include('field_config_expense_tracking.php'); break;
		case 'assets':
		case 'liabilities': include('field_config_chart_accounts.php'); break;
		case 'expense': include('field_config_expenses.php'); break;
	} ?>
<div class="form-group">
    <div class="col-sm-6">
        <a href="budgets.php?tab=<?php echo $tab; ?>" class="btn brand-btn">Back</a>
		<!--<a href="#" class="btn config-btn pull-right" onclick="history.go(-1);return false;">Back</a>-->
    </div>
    <div class="col-sm-6">
        <button	type="submit" name="submit"	value="Submit" class="btn brand-btn pull-right">Submit</button>
    </div>
</div>

</form>
</div>
</div>