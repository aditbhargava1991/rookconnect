<?php /* Budgeting */
include ('../include.php');
checkAuthorised('budget'); ?><br />
<?php 

if(!empty($_GET['status']) && $_GET['status'] == 'approve') {
	$update_query = "UPDATE `budget` SET `status` = 1 WHERE `budgetid` = $budgetid";
	mysqli_query($dbc, $update_query);
	echo "<script>window.location.replace('?maintype=active_budget');</script>";
}
else if(!empty($_GET['status'])) {
	$update_query = "UPDATE `budget` SET `status` = " . $_GET['status'] . " WHERE `budgetid` = " . $_GET['budgetid'];
	mysqli_query($dbc, $update_query);
	echo "<script>window.location.replace('?maintype=active_budget');</script>";
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

$query_check_credentials = "SELECT * FROM `budget` WHERE `status` = 2 LIMIT $offset, $rowsPerPage";
$query = "SELECT COUNT(*) as numrows FROM budget";

$result = mysqli_query($dbc, $query_check_credentials);

$num_rows = mysqli_num_rows($result);
?>
<div class="mobile-100-container">
	<!--<div class="col-sm-12 col-xs-12 col-lg-12 pad-top offset-xs-top-20">
		<a href="add_budget.php" class="btn brand-btn mobile-block gap-bottom pull-right">Add Budget</a>
	</div>-->
	<?php
	if($num_rows > 0) {
    	echo display_pagination($dbc, $query, $pageNum, $rowsPerPage);
    ?>
		<table class="table table-bordered">
			<tbody>
				<tr class="hidden-xs hidden-sm">
					<th>Budget Name (AFE#)</th>
					<th>Staff Lead</th>
					<th>Business</th>
					<th>Expense Summary</th>
					<th>Income Summary</th>
					<th>P/L Summary</th>
					<th>Notes</th>
					<th>History</th>
					<th>Function</th>
				</tr>
				<?php  while($row = mysqli_fetch_array( $result )) { ?>
					<tr>
						<td><a href = "add_budget.php?budgetid=<?php echo $row['budgetid']; ?>"><?php echo $row['budget_name']; ?></a></td>
						<td><?php echo get_contact($dbc, $row['staff_lead']); ?></td>
						<td><?php echo get_contact($dbc, $row['business'], 'name'); ?></td>
						<td><a href='budget_expense.php?budgetid=<?php echo $row['budgetid']; ?>'>View</a></td>
						<td><a href='budget_income.php?budgetid=<?php echo $row['budgetid']; ?>'>View</a></td>
						<td><a href='budget_pl.php?budgetid=<?php echo $row['budgetid']; ?>'>View</a></td>
						<td><a href='add_budget.php?note=add_view'>Add / View</a></td>
						<td>
							Approved | <a href='<?php echo '?maintype=active_budget&status=3&budgetid=' . $row['budgetid']; ?>'>Archive</a>
						</td>
						<td><span onclick="overlayIFrameSlider('budget_history.php?budgetid=<?= $row['budgetid'] ?>','auto',true,true);" class="cursor-hand">View All</span></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php } 
	else {
		echo "<h3>No Active Budgets Found.</h3>";
	}
	?>
	<!--<div class="col-sm-12 col-xs-12 col-lg-12 pad-top offset-xs-top-20">
		<a href="add_contacts.php?category=Business" class="btn brand-btn mobile-block gap-bottom pull-right">Add Budget</a>
	</div>-->
</div>
