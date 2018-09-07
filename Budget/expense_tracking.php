<?php /* Budgeting */
include ('../include.php');
checkAuthorised('budget'); ?>
<br><br>
<?php
if($_GET['delete']) {
	$delete_query = "delete from budget_expense where budget_expenseid = " . $_GET['delete'];
	mysqli_query($dbc, $delete_query);
}
?>
<?php
/* Pagination Counting */
$rowsPerPage = 25;
$pageNum = 1;

if(isset($_GET['page'])) {
    $pageNum = $_GET['page'];
}

$offset = ($pageNum - 1) * $rowsPerPage;

$query_check_credentials = "SELECT * FROM budget_expense LIMIT $offset, $rowsPerPage";

$query = "SELECT count(*) as numrows FROM budget_expense";

$result = mysqli_query($dbc, $query_check_credentials);

$num_rows = mysqli_num_rows($result);
?>



<div class="mobile-100-container">
	<div class="col-sm-12 col-xs-12 col-lg-12 pad-top offset-xs-top-20">
		<a href="<?php echo WEBSITE_URL; ?>/Budget/add_expense.php?from_url=<?php echo urlencode(WEBSITE_URL.$_SERVER['REQUEST_URI']); ?>" onclick="overlayIFrameSlider(this.href); return false;" class="btn brand-btn mobile-block gap-bottom pull-right">Add Expense</a>
	</div>
	<?php
		if($num_rows > 0) {
			echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
	?>
	<table class="table table-bordered">
		<tbody>
			<tr class="hidden-xs hidden-sm">
				<th>Budget</th>
				<th>Budget Category</th>
				<th>Budget Heading</th>
				<th>Expense Heading</th>
				<th>Expense Date</th>
				<th>Staff</th>
				<th>Receipt</th>
				<th>Actual Amount</th>
				<th>Tax</th>
				<th>Total</th>
				<th>Function</th>
			</tr>
			<?php while($row = mysqli_fetch_array($result)) { ?>
				<?php
					$budget_categoryid = $row['budget_categoryid'];
					$budget_category_query = "select category, expense, budgetid from budget_category where budget_categoryid = $budget_categoryid";
					$budget_category_result = mysqli_fetch_array(mysqli_query($dbc, $budget_category_query));
					$budgetid = $budget_category_result['budgetid'];
					$budget_heading = $budget_category_result['expense'];
					$category = $budget_category_result['category'];
				?> 
				<?php
					$select_budget_query = "select budget_name from budget where budgetid = $budgetid";
					$budget_result = mysqli_fetch_array(mysqli_query($dbc, $select_budget_query));
					$budget_name = $budget_result['budget_name'];
				?>
				<?php 
					$contactid = $row['submit_staff'];
					$select_contact_query = "select first_name, last_name from contacts where contactid = $contactid";
					$contact_result = mysqli_fetch_array(mysqli_query($dbc, $select_contact_query));
					$staff = decryptIt($contact_result['first_name']) . ' ' . decryptIt($contact_result['last_name']);
				?>
				<tr>
					<td data-title="Budget"><?php echo $budget_name; ?></td>
					<td data-title="Budget Category"><?php echo $category; ?></td>
					<td data-title="Budget Headings"><?php echo $budget_heading; ?></td>
					<td data-title="Expense Heading"><?php echo $row['expense_heading']; ?></td>
					<td data-title="Expense Date"><?php echo $row['expense_date']; ?></td>
					<td data-title="Staff"><?php echo $staff; ?></td>
					<td data-title="Receipt"><a href="download/<?php echo $row['reciept']; ?>" target="_blank"><?php echo $row['reciept']; ?></a></td>
					<td data-title="Actual Amount"><p>$<?php echo $row['actual_amount']; ?></p></td>
					<td data-title="Tax">$<?php echo $row['tax']; ?></td>
					<td data-title="Total">$<?php echo $row['total']; ?></td>
					<td><a href="<?php echo addOrUpdateUrlParam('delete', $row['budget_expenseid']); ?>" onclick="return confirm('Are you sure?')">Archive</a></td>
				</tr>
			<?php } ?>
		</tbody>
	</table>
	<?php 
	} else {
		echo "<h2>No Records Found.</h2>";
	} ?>
	<div class="col-sm-12 col-xs-12 col-lg-12 pad-top offset-xs-top-20">
		<a href="add_expense.php" onclick="overlayIFrameSlider(this.href); return false;" class="btn brand-btn mobile-block gap-bottom pull-right">Add Expense</a>
	</div>
</div>