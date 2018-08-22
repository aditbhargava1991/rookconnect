<?php $value = $config['settings']['Choose Fields for Bowel Movement Dashboard'];
	$search_staff = '';
	$search_client = '';
	$search_date = '';
	$charts_time_format = get_config($dbc, 'charts_time_format');

	if(isset($_GET['search_staff']) && $_GET['search_staff']!='') {
		$search_staff = $_GET['search_staff'];    
	} 
	if(isset($_GET['search_client']) && $_GET['search_client']!='') {
		$search_client = $_GET['search_client'];    
	}
	if(isset($_GET['search_date']) && $_GET['search_date']!='') {
		$search_date = $_GET['search_date'];    
	} 
	if(isset($display_contact)) {
		$search_client = $display_contact;
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
			<label for="site_name" class="control-label">Search By Client:</label>
		  </div>
		  <div class="col-lg-4 col-md-3 col-sm-8 col-xs-12 gap-bottom">
			  <select data-placeholder="Select a Client" name="search_client" class="chosen-select-deselect form-control" style="width: 20%;float: left;margin-right: 10px;" width="380">
			  <option value=""></option>
			  <?php
			  	$query = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT * FROM `contacts` WHERE `category` = 'Clients' AND `deleted` = 0 AND `status` > 0 AND `show_hide_user` = 1"),MYSQLI_ASSOC));
			  	foreach ($query as $id) { ?>
			  		<option <?= ($id == $search_client ? 'selected' : '') ?> value="<?= $id ?>"><?= get_contact($dbc, $id) ?></option>
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
			<button type="button" onclick="window.location='bowel_movement.php'" name="display_all_inventory" value="Display All" class="btn brand-btn mobile-block">Display All</button>
		  </div>
		</div>
	</form>
	<?php }
if(vuaed_visible_function_custom($dbc)) {
	echo '<a href="../Medical Charts/add_bowel_movement.php?from_url='.$return_url.'" class="btn brand-btn mobile-block pull-right mobile-full-width">Add Bowel Movement</a>';
	echo '<a href="../Medical Charts/monthly_chart.php?type=bowel_movement&edit='.$search_client.'" class="btn brand-btn mobile-block pull-right mobile-full-width">Bowel Movement Chart</a>';
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
	if($search_client != '') {
		$filter .= ' AND (client = "'.$search_client.'")';
	}
	if($search_date != '') {
		$filter .= ' AND date = "'.$search_date.'"';
	}

	$query_check_credentials = 'SELECT * FROM bowel_movement WHERE 1=1 and deleted = 0'.$filter.' ORDER BY `date` DESC, IFNULL(STR_TO_DATE(`time`, "%l:%i %p"),STR_TO_DATE(`time`, "%H:%i")) ASC';;

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
					echo '<th>'.$field[0].'</th>';
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
		$bowel_movement_id = $row['bowel_movement_id'];
		$clientid = $row['client'];

		foreach($value['data'] as $tab_name => $tabs) {
			foreach($tabs as $field) {
				if (strpos($value_config, ','.$field[2].',') !== FALSE) {
					echo '<td data-title="'.$field[0].'">';
					if($field[2] == 'staff') {
						echo get_staff($dbc, $row[$field[2]]);
					}elseif($field[2] == 'client') {
						echo get_contact($dbc, $row[$field[2]]);
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
		echo '<a href=\'../Medical Charts/add_bowel_movement.php?bowel_movement_id='.$bowel_movement_id.'&from_url='.$return_url.'\'>Edit</a> | ';
		echo '<a href=\'../Medical Charts/monthly_chart.php?type=bowel_movement&edit='.$clientid.'\'>Client Monthly Chart</a> | ';
		echo '<a href=\'../Medical Charts/add_bowel_movement.php?action=delete&bowel_movement_id='.$bowel_movement_id.'&from_url='.$return_url.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
		}
		echo '</td>';

		echo "</tr>";
	}

	echo '</table></div>';
	if(vuaed_visible_function_custom($dbc)) {
		echo '<a href="../Medical Charts/add_bowel_movement.php?from_url='.$return_url.'" class="btn brand-btn mobile-block pull-right mobile-full-width">Add Bowel Movement</a>';
		echo '<a href="../Medical Charts/monthly_chart.php?type=bowel_movement&edit='.$search_client.'" class="btn brand-btn mobile-block pull-right mobile-full-width">Bowel Movement Chart</a>';
	}

	?>