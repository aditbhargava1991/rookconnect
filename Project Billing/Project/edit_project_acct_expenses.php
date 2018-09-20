<?php include_once('../include.php');
error_reporting(0);
if(!isset($security)) {
	$security = get_security($dbc, $tile);
	$strict_view = strictview_visible_function($dbc, 'project');
	if($strict_view > 0) {
		$security['edit'] = 0;
		$security['config'] = 0;
	}
}
if(!isset($project)) {
	$projectid = filter_var($_GET['projectid'],FILTER_SANITIZE_STRING);
	$project = mysqli_fetch_array(mysqli_query($dbc, "SELECT * FROM `project` WHERE `projectid`='$projectid'"));
} ?>
<h3>Expenses</h3>
<?php $expenses = mysqli_query($dbc, "SELECT IF(`status`='','Submitted',`status`) ex_status, `expense`.* FROM `expense` WHERE `deleted`=0 AND `reimburse` > 0 AND `projectid`='$projectid' ORDER BY IF(`status`='Declined',4,IF(`status`='Paid',3,IF(`status`='Approved',2,1))), `ex_date` DESC");
if(mysqli_num_rows($expenses) > 0) {
	$category_query = mysqli_query($dbc, "SELECT CONCAT('EC ',`ec`,': ',`category`) `ec_code`, `category`, CONCAT('GL ',`gl`,': ',`heading`) `gl_code`, `heading` FROM `expense_categories` WHERE `deleted`=0");
	$category_list = [];
	$heading_list = [];
	while($cat_row = mysqli_fetch_array($category_query)) {
		$category_list[$cat_row['category']] = $cat_row['ec_code'];
		$heading_list[$cat_row['category'].$cat_row['heading']] = $cat_row['gl_code'];
	} ?>
	<div id="no-more-tables"><table class="table table-bordered">
		<tr class="hidden-sm hidden-xs">
			<th>Date</th>
			<th>Category</th>
			<th>Heading</th>
			<th>Staff</th>
			<th>Amount</th>
			<th>Status</th>
		</tr>
		<?php while($expense = mysqli_fetch_assoc($expenses)) { ?>
			<tr>
				<td data-title="Date"><?= $expense['ex_date'] ?></td>
				<td data-title="Category"><?= $category_list[$expense['category']] ?></td>
				<td data-title="Heading"><?= $heading_list[$expense['category'].$expense['title']] ?></td>
				<td data-title="Staff"><?= get_contact($dbc, $expense['staff']) ?></td>
				<td data-title="Amount"><?= $expense['total'] ?></td>
				<td data-title="Status"><a href="" onclick="overlayIFrame('../Expense/edit_expense.php?edit=<?= $expense['expenseid'] ?>'); return false;"><?= $expense['ex_status'] ?></a></td>
			</tr>
		<?php } ?>
	</table></div>
<?php } else { ?>
	<h2>No Expenses Found</h2>
<?php } ?>
<?php include('next_buttons.php'); ?>