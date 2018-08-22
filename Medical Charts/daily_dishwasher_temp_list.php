<?php $value = $config['settings']['Choose Fields for Daily Dishwasher Temp Dashboard'];
	$search_staff = '';
	$search_business = '';
	$search_date = '';
	$charts_time_format = get_config($dbc, 'charts_time_format');

	if(isset($_GET['search_staff']) && $_GET['search_staff']!='') {
		$search_staff = $_GET['search_staff'];    
	} 
	if(isset($_GET['search_business']) && $_GET['search_business']!='') {
		$search_business = $_GET['search_business'];    
	}
	if(isset($_GET['search_date']) && $_GET['search_date']!='') {
		$search_date = $_GET['search_date'];    
	}
	if(isset($display_contact)) {
		$search_business = $display_contact;
	} else { ?>  
		<form id="form1" name="form1" method="get" enctype="multipart/form-data" class="form-horizontal" role="form">
		  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
			<label for="site_name" class="control-label">Search By Staff:</label>
		  </div>
		  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12 gap-bottom">
			  <select data-placeholder="Select a Staff" name="search_staff" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
			  <option value=""></option>
			  <?php
			  	$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
			  	foreach ($query as $id) { ?>
			  		<option <?= ($id == $search_staff ? 'selected' : '') ?> value="<?= $id ?>"><?= get_contact($dbc, $id) ?></option>
			  	<?php }
			  ?>
			</select>
		  </div>

		  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
			<label for="site_name" class="control-label">Search By Program:</label>
		  </div>
		  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12 gap-bottom">
			  <select data-placeholder="Select a Program" name="search_business" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
			  <option value=""></option>
			  <?php
			  	$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = 'Business' AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
			  	foreach ($query as $id) { ?>
			  		<option <?= ($id == $search_business ? 'selected' : '') ?> value="<?= $id ?>"><?= get_client($dbc, $id) ?></option>
			  	<?php }
			  ?>
			</select>
		  </div>

		  <div class="col-lg-2 col-md-3 col-sm-4 col-xs-12">
			<label for="site_name" class="control-label">Search By Date:</label>
		  </div>
		  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12">
			  <input name="search_date" value="<?php echo $search_date; ?>" type="text" class="form-control datepicker">
		  </div>


		<div class="form-group">
		  <label for="site_name" class="col-sm-4 control-label"></label>
		  <div class="col-sm-8">
			<button type="submit" name="search_user_submit" value="Search" class="btn brand-btn mobile-block">Search</button>
			<button type="button" onclick="window.location='daily_dishwasher_temp.php'" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
		  </div>
		</div>
	</form>
	<?php }
	
if(vuaed_visible_function_custom($dbc)) {
	echo '<a href="../Medical Charts/add_daily_dishwasher_temp.php?from_url='.$return_url.'" class="btn brand-btn mobile-block pull-right mobile-full-width">Add Daily Dishwasher Temp</a>';
	echo '<a href="../Medical Charts/monthly_chart.php?type=daily_dishwasher_temp&edit='.$search_business.'" class="btn brand-btn mobile-block pull-right mobile-full-width">Daily Dishwasher Temp Chart</a>';
}
?>
<br><br><br>

	<div id="no-more-tables">

	<?php    

	$tb_field = $value['config_field'];

	$filter = '';
	if($search_staff != '') {
		$filter .= ' AND (staff = "'.$search_staff.'")';
	}
	if($search_business != '') {
		$filter .= ' AND (business = "'.$search_business.'")';
	}
	if($search_date != '') {
		$filter .= ' AND date = "'.$search_date.'"';
	}

	$query_check_credentials = 'SELECT * FROM daily_dishwasher_temp WHERE 1=1 and deleted=0'.$filter.' ORDER BY `date` DESC, IFNULL(STR_TO_DATE(`time`, "%l:%i %p"),STR_TO_DATE(`time`, "%H:%i")) ASC';;

	$result = mysqli_query($dbc, $query_check_credentials);

	$num_rows = mysqli_num_rows($result);
	if($num_rows > 0) {

		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$tb_field." FROM field_config"));
		$value_config = ','.$get_field_config[$tb_field].',';

		echo "<table class='table table-bordered table-striped'>";
		echo "<thead><tr class='hidden-xs hidden-sm'>";

		foreach($value['data'] as $tab_name => $tabs) {
			foreach($tabs as $field) {
				if (strpos($value_config, ','.$field[2].',') !== FALSE) {
					echo '<th>'.strip_tags($field[0]).'</th>';
				}
			}
		}
			echo '<th>Function</th>';
		echo "</tr></thead>";
	} else {
		echo "<h2>No Record Found.</h2>";
	}
	while($row = mysqli_fetch_array( $result ))
	{
		echo "<tr>";
		$daily_dishwasher_temp_id = $row['daily_dishwasher_temp_id'];
		$businessid = $row['business'];

		foreach($value['data'] as $tab_name => $tabs) {
			foreach($tabs as $field) {
				if (strpos($value_config, ','.$field[2].',') !== FALSE) {
					echo '<td data-title="'.$field[0].'">';
					if($field[2] == 'staff') {
						echo get_staff($dbc, $row[$field[2]]);
					}elseif($field[2] == 'business') {
						echo get_client($dbc, $row[$field[2]]);
					} else {
						if($field[2] == 'time') {
							if($charts_time_format == '24h') {
								$row[$field[2]] = date('H:i', strtotime(date('Y-m-d').' '.$row[$field[2]]));
							} else {
								$row[$field[2]] = date('h:i a', strtotime(date('Y-m-d').' '.$row[$field[2]]));
							}
						}
						echo strip_tags(htmlspecialchars_decode($row[$field[2]]));    
					}
					echo '</td>';
				}
			}
		}

		echo '<td data-title="Function">';
		if(vuaed_visible_function_custom($dbc)) {
		echo '<a href=\'../Medical Charts/add_daily_dishwasher_temp.php?daily_dishwasher_temp_id='.$daily_dishwasher_temp_id.'&from_url='.$return_url.'\'>Edit</a> | ';
		echo '<a href=\'../Medical Charts/monthly_chart.php?type=daily_dishwasher_temp&edit='.$businessid.'\'>Program Monthly Chart</a> | ';
		echo '<a href=\'../Medical Charts/add_daily_dishwasher_temp.php?action=delete&daily_dishwasher_temp_id='.$daily_dishwasher_temp_id.'&from_url='.$return_url.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
		}
		echo '</td>';

		echo "</tr>";
	}

	echo '</table></div>';
	if(vuaed_visible_function_custom($dbc)) {
		echo '<a href="../Medical Charts/add_daily_dishwasher_temp.php?from_url='.$return_url.'" class="btn brand-btn mobile-block pull-right mobile-full-width">Add Daily Dishwasher Temp</a>';
		echo '<a href="../Medical Charts/monthly_chart.php?type=daily_dishwasher_temp&edit='.$search_business.'" class="btn brand-btn mobile-block pull-right mobile-full-width">Daily Dishwasher Temp Chart</a>';
	}

	?>