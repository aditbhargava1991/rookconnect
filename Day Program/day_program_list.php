<form id="form1" name="form1" method="post"	action="" enctype="multipart/form-data" class="form-horizontal" role="form">

		<?php
		$search_client = '';
		$search_date = '';
		if(isset($_POST['search_user_submit'])) {
			$search_client = $_POST['search_client'];
			$search_date = $_POST['search_date'];
		}
		if (isset($_POST['display_all_inventory'])) {
			$search_client = '';
			$search_date = '';
		}
		?>

		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label">Search By Client:</label>
			<div class="col-sm-8" style="width:auto">
			  <select data-placeholder="Pick a Client" name="search_client" class="chosen-select-deselect form-control" width="380">
			  <option value=""></option>
			  <?php
				$query = mysqli_query($dbc,"SELECT distinct(support_contact) FROM day_program WHERE deleted=0 and support_contact != '' order by support_contact");
				while($row1 = mysqli_fetch_array($query)) {
				?><option <?php if ($row1['support_contact'] == $search_client) { echo " selected"; } ?> value='<?php echo  $row1['support_contact']; ?>' ><?php echo get_client($dbc, $row1['support_contact']); ?></option>
			<?php   }
			?>
			</select>
		  </div>

		  <b>Search By Date:</b>
			<input type="text" class="datepicker" name="search_date" value="<?php echo $search_date; ?>">

			<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
			<button type="button" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
		</div>

		<?php
		if(vuaed_visible_function($dbc, 'medication') == 1) {
			echo '<a href="../Day Program/add_day_program.php?from_url='.$from_url.'" class="btn brand-btn mobile-block pull-right">Add Day Program</a>';
		}
		?>

	<div id="no-more-tables">

	<?php
	$filter = '';
	if($search_client != '') {
		$filter = " AND support_contact ='$search_client'";
	}
	if($search_date != '') {
		$filter = " AND date='$search_date'";
	}
	$query_check_credentials = "SELECT * FROM day_program WHERE deleted = 0".$filter;

	$result = mysqli_query($dbc, $query_check_credentials);

	$num_rows = mysqli_num_rows($result);
	if($num_rows > 0) {
		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT medication_dashboard FROM field_config"));
		$value_config = ','.$get_field_config['medication_dashboard'].',';

		echo "<table class='table table-bordered table-striped'>";
		echo "<thead><tr class='hidden-xs hidden-sm'>";
			echo '<th>Client</th>';
			echo '<th>Date</th>';
			echo '<th>Planned Activity</th>';
			echo '<th>Completed Activity</th>';
			echo '<th>Function</th>';
			echo "</tr></thead>";
	} else {
		echo "<h2>No Record Found.</h2>";
	}
	while($row = mysqli_fetch_array( $result ))
	{
		echo "<tr>";
		$dayprogramid = $row['dayprogramid'];
		echo '<td data-title="Client">' . get_staff($dbc, $row['support_contact']).' '.get_client($dbc, $row['support_contact']). '</td>';

		echo '<td data-title="Date">'. $row['date']. '</td>';
		echo '<td data-title="Planned Activity">'. $row['planned_activity']. '</td>';
		echo '<td data-title="Completed Activity">'. $row['completed_activity']. '</td>';

		echo '<td data-title="Function">';
		if(vuaed_visible_function($dbc, 'medication') == 1) {
		echo '<a href=\'../Day Program/add_day_program.php?dayprogramid='.$dayprogramid.'&from_url='.$from_url.'\'>Edit</a> | ';
		echo '<a href=\''.WEBSITE_URL.'/delete_restore.php?action=delete&medicationid='.$dayprogramid.'&from_url='.$from_url.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
		}
		echo '</td>';

		echo "</tr>";
	}

	echo '</table></div>';
	if(vuaed_visible_function($dbc, 'medication') == 1) {
		echo '<a href="../Day Program/add_day_program.php?from_url='.$from_url.'" class="btn brand-btn mobile-block pull-right">Add Day Program</a>';
	}

	?>
</form>