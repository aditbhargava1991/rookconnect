<?php
if(vuaed_visible_function_social($dbc)) {
	echo '<a href="../Social Story/add_key_methodologies.php?from_url='.$from_url.'" class="btn brand-btn mobile-block pull-right">Add Key Methodologies</a>';
}
?>
<br><br>

	<div id="no-more-tables">

	<?php    

	$tb_field = $config['settings']['Choose Fields for Key Methodologies Dashboard']['config_field'];

	$query_check_credentials = "SELECT * FROM key_methodologies WHERE deleted = 0";
	$result = mysqli_query($dbc, $query_check_credentials);

	$num_rows = mysqli_num_rows($result);
	if($num_rows > 0) {

		$get_field_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$tb_field." FROM field_config"));
		$value_config = ','.$get_field_config[$tb_field].',';

		echo "<table class='table table-bordered table-striped'>";
		echo "<thead><tr class='hidden-xs hidden-sm'>";

		foreach($config['settings']['Choose Fields for Key Methodologies Dashboard']['data'] as $tab_name => $tabs) {
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
		$keymethodologiesid = $row['keymethodologiesid'];

		foreach($config['settings']['Choose Fields for Key Methodologies Dashboard']['data'] as $tab_name => $tabs) {
			foreach($tabs as $field) {
				if (strpos($value_config, ','.$field[2].',') !== FALSE) {
					echo '<td>';
					if($field[2] == 'support_contact') {
						echo get_staff($dbc, $row[$field[2]]).' '.get_client($dbc, $row[$field[2]]);
					} else {
						echo $row[$field[2]];    
					}
					echo '</td>';
				}
			}
		}

		echo '<td>';
		if(vuaed_visible_function_social($dbc)) {
		echo '<a href=\'../Social Story/add_key_methodologies.php?keymethodologiesid='.$keymethodologiesid.'&from_url='.$from_url.'\'>Edit</a> | ';
		echo '<a href=\'../Social Story/add_key_methodologies.php?action=delete&keymethodologiesid='.$keymethodologiesid.'&from_url='.$from_url.'\' onclick="return confirm(\'Are you sure?\')">Archive</a>';
		}
		echo '</td>';

		echo "</tr>";
	}

	echo '</table></div>';
	if(vuaed_visible_function_social($dbc)) {
		echo '<a href="../Social Story/add_key_methodologies.php?from_url='.$from_url.'" class="btn brand-btn mobile-block pull-right">Add Key Methodologies</a>';
	}

	?>