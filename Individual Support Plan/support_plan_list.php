	<?php
	$search_contact = '';
	$search_start = '';
	$search_review = '';
	if(isset($_POST['search_contact'])) {
		$search_contact = $_POST['search_contact'];
		$search_start = $_POST['search_start'];
		$search_review = $_POST['search_review'];
	}
	if (isset($_POST['display_all'])) {
		$search_contact = '';
		$search_start = '';
		$search_review = '';
	}
	if(isset($display_contact)) {
		$search_contact = $display_contact;
	} else { ?>
		<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">
			<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
				<label for="site_name" class="control-label">Search By Client:</label>
			</div>
			<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
				<select data-placeholder="Pick a Client" name="search_contact" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
					<option value=""></option>
					<?php $query = mysqli_query($dbc,"SELECT `support_contact`, TRIM(CONCAT(IFNULL(c.`name`,''), ' ', IFNULL(c.`first_name`,''), ' ', IFNULL(c.`last_name`,''))) client FROM individual_support_plan isp LEFT JOIN `contacts` c on isp.`support_contact`=c.`contactid` GROUP BY `support_contact`, c.`name`, c.`first_name`, c.`last_name` ORDER BY c.`last_name`, c.`name`");
					while($row = mysqli_fetch_array($query)) {
						?><option <?php if ($row['support_contact'] == $search_contact) { echo " selected"; } ?> value='<?php echo  $row['support_contact']; ?>' ><?php echo $row['client']; ?></option>
					<?php } ?>
				</select>
			</div>
			<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
				<label for="site_name" class="control-label">Search By Start Date:</label>
			</div>
			<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
				<input type="text" value="<?php echo $search_start; ?>" class="form-control datepicker" name="search_start" style="width:100;">
			</div>
			<div class="col-lg-2 col-md-3 col-sm-4 col-xs-4">
				<label for="site_name" class="control-label">Search By Review Date:</label>
			</div>
			<div class="col-lg-4 col-md-3 col-sm-8 col-xs-8">
				<input type="text" value="<?php echo $search_review; ?>" class="form-control datepicker" name="search_review" style="width:100;">
			</div>
			
			<div class="form-group clearfix">
				<label for="site_name" class="col-sm-4 control-label"></label>
				<div class="col-sm-8">
					<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
					<button type="button" onclick="window.location=''" name="display_all" value="Display All" class="btn brand-btn mobile-block">Display All</button>
				</div>
			</div>
		</form>
	<?php }
	if(vuaed_visible_function($dbc, 'medication') == 1) {
		echo '<a href="../Individual Support Plan/add_individual_support_plan.php?from_url='.$from_url.'" class="btn brand-btn mobile-block pull-right">Add Individual Service Plan</a>';
	}
	?>

<div id="no-more-tables">

<?php
$clause = '';
if($search_contact != '') {
	$clause .= "AND support_contact ='$search_contact' ";
} else if($search_review != '') {
	$clause .= "AND isp_review_date='$search_review'";
} else if($search_start != '') {
	$clause .= "AND isp_start_date='$search_start'";
}
$query_check_credentials = "SELECT * FROM individual_support_plan WHERE deleted = 0 $clause";

$result = mysqli_query($dbc, $query_check_credentials);

$num_rows = mysqli_num_rows($result);
if($num_rows > 0) {
	$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT medication_dashboard FROM field_config"));
	$value_config = ','.$get_field_config['medication_dashboard'].',';

	echo "<table class='table table-bordered table-striped'>";
	echo "<thead><tr class='hidden-xs hidden-sm'>";
		echo '<th>Client</th>';
		echo '<th>Support Team</th>';
		echo '<th>ISP Start Date</th>';
		echo '<th>ISP Review Date</th>';
		echo '<th>ISP End Date</th>';
		echo '<th>ISP Details</th>';
		echo '<th>Log Summary</th>';
		echo '<th>Social Story</th>';
		echo '<th>ISP Notes</th>';
		echo '<th>Function</th>';
		echo "</tr></thead>";
} else {
	echo "<h2>No Record Found.</h2>";
}
while($row = mysqli_fetch_array( $result ))
{
	echo "<tr>";
	$individualsupportplanid = $row['individualsupportplanid'];
	echo '<td data-title="Individual Service Plan Type">' . get_staff($dbc, $row['support_contact']).' '.get_client($dbc, $row['support_contact']). '</td>';

	echo '<td data-title="Function">';
	echo '<a href=\'../Individual Support Plan/add_individual_support_plan.php?individualsupportplanid='.$individualsupportplanid.'&acc=day_program&from_url='.$from_url.'\'>Support Team</a>';
	echo '</td>';

	echo '<td data-title="Individual Support Plan Type">' . $row['isp_start_date']. '</td>';
	echo '<td data-title="Individual Support Plan Type">' . $row['isp_review_date']. '</td>';
	echo '<td data-title="Individual Support Plan Type">' . $row['isp_end_date']. '</td>';

	echo '<td data-title="Function">';
	echo '<a href=\'../Individual Support Plan/add_individual_support_plan.php?individualsupportplanid='.$individualsupportplanid.'&acc=isp_detail&from_url='.$from_url.'\'>ISP Details</a>';
	echo '</td>';

	echo '<td data-title="Function">';
	echo '<a href=\'../Individual Support Plan/add_individual_support_plan.php?individualsupportplanid='.$individualsupportplanid.'&from_url='.$from_url.'\'>Log Summary</a>';
	echo '</td>';

	echo '<td data-title="Function">';
	echo '<a href=\'../Individual Support Plan/add_individual_support_plan.php?individualsupportplanid='.$individualsupportplanid.'&from_url='.$from_url.'\'>Social Story</a>';
	echo '</td>';

	echo '<td data-title="Function">';
	echo '<a href=\'../Individual Support Plan/add_individual_support_plan.php?individualsupportplanid='.$individualsupportplanid.'&acc=isp_notes&from_url='.$from_url.'\'>ISP Notes</a>';
	echo '</td>';

	echo '<td data-title="Function">';
	if(vuaed_visible_function($dbc, 'medication') == 1) {
	echo '<a href=\'../Individual Support Plan/add_individual_support_plan.php?individualsupportplanid='.$individualsupportplanid.'&from_url='.$from_url.'\'>Edit</a> | ';
	echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&individualsupportplanid='.$individualsupportplanid.'&from_url='.$from_url.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
	}
	echo '</td>';

	echo "</tr>";
}

echo '</table></div>';
if(vuaed_visible_function($dbc, 'medication') == 1) {
echo '<a href="../Individual Support Plan/add_individual_support_plan.php?from_url='.$from_url.'" class="btn brand-btn mobile-block pull-right">Add Individual Service Plan</a>';
} ?>