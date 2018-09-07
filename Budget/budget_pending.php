<?php /* Budgeting */
include ('../include.php');
checkAuthorised('budget'); ?><br />
<?php 
error_reporting(0);
?>
<?php 
if($_GET['status']) {
	$update_query = "UPDATE `budget` SET `status` = " . $_GET['status'] . " WHERE `budgetid` = " . $_GET['budgetid'];
	mysqli_query($dbc, $update_query);
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

$query_check_credentials = "SELECT * FROM budget WHERE `status` IN (0,1) LIMIT $offset, $rowsPerPage";
$query = "SELECT count(*) as numrows FROM budget WHERE `status` IN (0,1)";

$result = mysqli_query($dbc, $query_check_credentials);
$approvals = approval_visible_function($dbc, 'budget');

$num_rows = mysqli_num_rows($result);
?>
<div class="mobile-100-container">
	<div class="col-sm-12 col-xs-12 col-lg-12 pad-top offset-xs-top-20">
		<a href="add_budget.php" onclick="overlayIFrameSlider(this.href); return false;" class="btn brand-btn mobile-block gap-bottom pull-right">Add Budget</a>
	</div>
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
					<th>Status</th>
					<th>Function</th>
					<th>History</th>
				</tr>
				<?php  while($row = mysqli_fetch_array( $result )) { ?>
					<tr>
						<td><a href = "add_budget.php?budgetid=<?php echo $row['budgetid']; ?>"><?php echo $row['budget_name']; ?></a></td>
						<td><?php echo get_contact($dbc, $row['staff_lead']); ?></td>
						<td><?php echo get_contact($dbc, $row['business'], 'name'); ?></td>
						<td><a href='budget_expense.php?budgetid=<?php echo $row['budgetid']; ?>'>View</a></td>
						<td><a href='budget_income.php?budgetid=<?php echo $row['budgetid']; ?>'>View</a></td>
						<td><a href='budget_pl.php?budgetid=<?php echo $row['budgetid']; ?>'>View</a></td>
						<td><a href='add_budget.php?budgetid=<?php echo $row['budgetid'] ?>&note=add_view'>Add / View</a></td>
						<td>
							<?php if($row['status'] == 0): ?>
								In Development
							<?php elseif($row['status'] == 1): ?>
								Pending Approval
							<?php elseif($row['status'] == 2): ?>
								Approved / Active
							<?php elseif($row['status'] == 3): ?>
								Archived
							<?php endif; ?>
						</td>
						<td>
							<a href='add_budget.php?budgetid=<?php echo $row['budgetid']; ?>'>Edit</a> | 
							<?php if($row['status'] == 2): ?>
								Approved | 
							<?php elseif($approvals > 0): ?>
								<a href='<?php echo addOrUpdateUrlParam('status','2') . '&budgetid=' . $row['budgetid']; ?>'>Approve</a> |
							<?php else: ?>
								Approve |
							<?php endif; ?>	
							<?php if($row['status'] == 3): ?>
								Archive
							<?php else: ?>
								<a href='<?php echo addOrUpdateUrlParam('status','3') . '&budgetid=' . $row['budgetid']; ?>'>Archive</a>
							<?php endif; ?>
						</td>
						<td><span onclick="overlayIFrameSlider('budget_history.php?budgetid=<?= $row['budgetid'] ?>','auto',true,true);" class="cursor-hand">View All</span></td>
					</tr>
				<?php } ?>
			</tbody>
		</table>
	<?php } 
	else {
		echo "<h3>No Pending Budgets Found.</h3>";
	}
	?>
	<div class="col-sm-12 col-xs-12 col-lg-12 pad-top offset-xs-top-20">
		<a href="add_budget.php" onclick="overlayIFrameSlider(this.href); return false;" class="btn brand-btn mobile-block gap-bottom pull-right">Add Budget</a>
	</div>
</div>
