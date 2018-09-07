<!-- Daysheet My Support Requests -->
<?php
	$rowsPerPage = $_GET['pagerows'] > 0 ? $_GET['pagerows'] : 25;
	$_GET['page'] = $_GET['page'] ?: 1;
	$offset = ($_GET['page'] > 0 ? $_GET['page'] - 1 : 0) * $rowsPerPage;
    $comm_query = "SELECT * FROM `support` WHERE `deleted`=0 AND (`assigned`='' OR CONCAT(',',`assigned`,',') LIKE '%,".$_SESSION['contactid'].",%') ORDER BY `supportid` DESC LIMIT $offset, $rowsPerPage";
    $comm_result = mysqli_query($dbc, $comm_query);
	if($comm_result->num_rows == 0 && $_GET['page'] > 1) {
		$_GET['page'] = 1;
		$offset = 0;
		$comm_query = "SELECT * FROM `support` WHERE `deleted`=0 AND (`assigned`='' OR CONCAT(',',`assigned`,',') LIKE '%,".$_SESSION['contactid'].",%') ORDER BY `supportid` DESC LIMIT $offset, $rowsPerPage";
		$comm_result = mysqli_query($dbc, $comm_query);
	}
    $num_rows = mysqli_num_rows($comm_result);
?>
<div class="col-xs-12">
	<div class="weekly-div" style="overflow-y: hidden;">
		<?php if($num_rows > 0) {
			display_pagination($dbc, "SELECT COUNT(*) `numrows` FROM `support` WHERE `deleted`=0 AND (`assigned`='' OR CONCAT(',',`assigned`,',') LIKE '%,".$_SESSION['contactid'].",%')", $_GET['page'], $rowsPerPage, true, 25);
			echo '<ul class="option-list">';
			while($row = mysqli_fetch_array( $comm_result )) {
				echo '<span class="display-field"><b><a href="'.WEBSITE_URL.'/Support/customer_support.php?tab=requests&type='.$row['support_type'].'#'.$row['supportid'].'">Date of Request: '.$row['current_date']."</a></b><br />
					Software Link: <a href='".$row['software_url']."'>".$row['software_url']."</a><br />
					User Name: ".$row['software_user_name']."<br />Security Level: ".$row['software_role']."<br />
					Support Request #".$row['supportid']."<br />".$row['heading']."<hr>".html_entity_decode($row['message']).'</span>
					<div class="clearfix"></div><hr></div>';
			}
			echo '</ul>';
			display_pagination($dbc, "SELECT COUNT(*) `numrows` FROM `support` WHERE `deleted`=0 AND (`assigned`='' OR CONCAT(',',`assigned`,',') LIKE '%,".$_SESSION['contactid'].",%')", $_GET['page'], $rowsPerPage, true, 25);
		} else {
			echo "<h2>No Support Requests Found.</h2>";
		} ?>
	</div>
</div>