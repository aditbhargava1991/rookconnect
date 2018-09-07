<?php /* Export Contacts Include File */

if(isset($_POST['export_contacts'])) {
	set_time_limit(0);
	$category = $_POST['export_contacts'];
	$export_option = $_POST['export_option'];
	if(empty($export_option)) {
		$export_option = 'Contact Information';
	}
	$today_date = date('Y-m-d_h-i-s-a', time());
	if(!file_exists('exports')) {
		mkdir('exports',0777);
	}
	$FileName = "exports/contacts_export_".$today_date." - ".$export_option.".csv";
	$file = fopen($FileName,"w");

	$contact_export_list = sort_contacts_array(mysqli_fetch_all(mysqli_query($dbc, "SELECT `contactid` FROM `contacts` WHERE `tile_name` = '".FOLDER_NAME."' AND `category` = '".$category."' AND `deleted` = 0"),MYSQLI_ASSOC));

	switch($export_option) {
		case 'Individual Service Plan':
			include_once('../Contacts/export_fields.php');
			$field_config = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `individual_support_plan` FROM `field_config`"))['individual_support_plan']);

			$select_list = ['`individualsupportplanid`', '`support_contact`', "'contact_name'"];
			foreach($fields_isp as $key => $field_option) {
				if(in_array($field_option,$field_config) || empty($field_option)) {
					$check_field_exists = mysqli_query($dbc, "SELECT * FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = '".DATABASE_NAME."' AND `TABLE_NAME` = 'individual_support_plan' AND `COLUMN_NAME` = '$key'");
					if(mysqli_num_rows($check_field_exists) > 0) {
						$select_list[] = '`'.$key.'`';
					}
				}
			}
			$select_list = array_unique($select_list);
			$select_empty = '';
			for ($i = 0; $i < count($select_list); $i++) {
				$select_empty .= "'',";
			}
			$select_empty = rtrim($select_empty, ',');
			$select_list = implode(',', $select_list);

			$sql = "SELECT * FROM (SELECT $select_list FROM `individual_support_plan` WHERE `deleted` = 0 AND `support_contact` IN (".implode(',',$contact_export_list).") UNION SELECT $select_empty) export_table";
			$result = mysqli_query($dbc, $sql);

			$headings = true;
			$HeadingsArray = array();

			while($row = mysqli_fetch_assoc($result)) {
				$valuesArray = array();
				foreach($row as $name => $value) {
					if($headings) {
						$HeadingsArray[] = $name;
					}

					if($name == 'contact_name') {
						$value = !empty($row['support_contact']) ? get_contact($dbc, $row['support_contact']) : '';
					}

					$valuesArray[] = html_entity_decode($value);
				}

				if($headings) {
					fputcsv($file, $HeadingsArray);
				}
				fputcsv($file, $valuesArray);
				$headings = false;
			}
			break;
		case 'Medication Details':
			include_once('../Contacts/export_fields.php');
			$field_config = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `medication` FROM `field_config`"))['medication']);

			$select_list = ['`medicationid`', '`contactid`', "'contact_name'"];
			foreach($fields_medication as $key => $field_option) {
				if(in_array($field_option,$field_config) || empty($field_option)) {
					$check_field_exists = mysqli_query($dbc, "SELECT * FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = '".DATABASE_NAME."' AND `TABLE_NAME` = 'medication' AND `COLUMN_NAME` = '$key'");
					if(mysqli_num_rows($check_field_exists) > 0) {
						$select_list[] = '`'.$key.'`';
					}
				}
			}
			$select_list = array_unique($select_list);
			$select_empty = '';
			for ($i = 0; $i < count($select_list); $i++) {
				$select_empty .= "'',";
			}
			$select_empty = rtrim($select_empty, ',');
			$select_list = implode(',', $select_list);

			$sql = "SELECT * FROM (SELECT $select_list FROM `medication` WHERE `deleted` = 0 AND `contactid` IN (".implode(',',$contact_export_list).") UNION SELECT $select_empty) export_table";
			$result = mysqli_query($dbc, $sql);

			$headings = true;
			$HeadingsArray = array();

			while($row = mysqli_fetch_assoc($result)) {
				$valuesArray = array();
				foreach($row as $name => $value) {
					if($headings) {
						$HeadingsArray[] = $name;
					}

					if($name == 'contact_name') {
						$value = !empty($row['contactid']) ? get_contact($dbc, $row['contactid']) : '';
					}

					$valuesArray[] = html_entity_decode($value);
				}

				if($headings) {
					fputcsv($file, $HeadingsArray);
				}
				fputcsv($file, $valuesArray);
				$headings = false;
			}
			break;
		case 'Medical Charts - Bowel Movement':
		case 'Medical Charts - Seizure Record':
		case 'Medical Charts - Daily Water Temp':
		case 'Medical Charts - Blood Glucose':
			if($export_option == 'Medical Charts - Bowel Movement') {
				$chart_type = 'Bowel Movement';
				$tableid = 'bowel_movement_id';
				$tablename = 'bowel_movement';
			}
			if($export_option == 'Medical Charts - Seizure Record') {
				$chart_type = 'Seizure Record';
				$tableid = 'seizure_record_id';
				$tablename = 'seizure_record';
			}
			if($export_option == 'Medical Charts - Daily Water Temp') {
				$chart_type = 'Daily Water Temp';
				$tableid = 'daily_water_temp_id';
				$tablename = 'daily_water_temp';
			}
			if($export_option == 'Medical Charts - Blood Glucose') {
				$chart_type = 'Blood Glucose';
				$tableid = 'blood_glucose_id';
				$tablename = 'blood_glucose';
			}

			include_once('../Contacts/config_mc.php');
			$config_mc_export = $config_mc['settings']['Choose Fields for '.$chart_type];
			$field_config = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `".$config_mc_export['config_field']."` FROM `field_config`"))[$config_mc_export['config_field']]);

			$select_list = ["`$tableid`", '`client`', "'contact_name'"];
			foreach ($config_mc_export['data'] as $tab_name => $tabs) {
				foreach ($tabs as $field) {
					if (in_array($field[2], $field_config) && $field[2] != 'client') {
						$check_field_exists = mysqli_query($dbc, "SELECT * FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = '".DATABASE_NAME."' AND `TABLE_NAME` = '$tablename' AND `COLUMN_NAME` = '".$field[2]."'");
						if(mysqli_num_rows($check_field_exists) > 0) {
							$select_list[] = '`'.$field[2].'`';	
						}
					}
				}
			}
			$select_list = array_unique($select_list);
			$select_empty = '';
			for ($i = 0; $i < count($select_list); $i++) {
				$select_empty .= "'',";
			}
			$select_empty = rtrim($select_empty, ',');
			$select_list = implode(',', $select_list);

			$sql = "SELECT * FROM (SELECT $select_list FROM `$tablename` WHERE `deleted` = 0 AND `client` IN (".implode(',',$contact_export_list).") UNION SELECT $select_empty) export_table";
			$result = mysqli_query($dbc, $sql);

			$headings = true;
			$HeadingsArray = array();

			while($row = mysqli_fetch_assoc($result)) {
				$valuesArray = array();
				foreach($row as $name => $value) {
					if($headings) {
						$HeadingsArray[] = $name;
					}

					if($name == 'contact_name') {
						$value = !empty($row['client']) ? get_contact($dbc, $row['client']) : '';
					}

					$valuesArray[] = html_entity_decode($value);
				}

				if($headings) {
					fputcsv($file, $HeadingsArray);
				}
				fputcsv($file, $valuesArray);
				$headings = false;
			}
			break;
		case 'Activities':
		case 'Communication':
		case 'Protocols':
		case 'Routines':
		case 'Key Methodologies':
			if($export_option == 'Activities') {
				$tableid = 'activities_id';
				$tablename = 'social_story_activities';
			}
			if($export_option == 'Communication') {
				$tableid = 'communication_id';
				$tablename = 'social_story_communication';
			}
			if($export_option == 'Protocols') {
				$tableid = 'protocol_id';
				$tablename = 'social_story_protocols';
			}
			if($export_option == 'Routines') {
				$tableid = 'routine_id';
				$tablename = 'social_story_routines';
			}
			if($export_option == 'Key Methodologies') {
				$tableid = 'keymethodologiesid';
				$tablename = 'key_methodologies';
			}

			include_once('../Contacts/config_contact_ss.php');
			$config_ss = $config_contact_ss['settings']['Choose Fields for '.$export_option];
			$field_config = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `".$config_ss['config_field']."` FROM `field_config`"))[$config_ss['config_field']]);

			$select_list = ["`$tableid`", '`support_contact`', "'contact_name'"];
			foreach ($config_ss['data'] as $tab_name => $tabs) {
				foreach ($tabs as $field) {
					if (in_array($field[2], $field_config) && $field[2] != 'support_contact' && $field[1] != 'upload' && $field[1] != 'widget') {
						$check_field_exists = mysqli_query($dbc, "SELECT * FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = '".DATABASE_NAME."' AND `TABLE_NAME` = '$tablename' AND `COLUMN_NAME` = '".$field[2]."'");
						if(mysqli_num_rows($check_field_exists) > 0) {
							$select_list[] = '`'.$field[2].'`';	
						}
					}
				}
			}
			$select_list = array_unique($select_list);
			$select_empty = '';
			for ($i = 0; $i < count($select_list); $i++) {
				$select_empty .= "'',";
			}
			$select_empty = rtrim($select_empty, ',');
			$select_list = implode(',', $select_list);

			$sql = "SELECT * FROM (SELECT $select_list FROM `$tablename` WHERE `support_contact` IN (".implode(',',$contact_export_list).") UNION SELECT $select_empty) export_table";
			$result = mysqli_query($dbc, $sql);

			$headings = true;
			$HeadingsArray = array();

			while($row = mysqli_fetch_assoc($result)) {
				$valuesArray = array();
				foreach($row as $name => $value) {
					if($headings) {
						$HeadingsArray[] = $name;
					}

					if($name == 'contact_name') {
						$value = !empty($row['support_contact']) ? get_contact($dbc, $row['support_contact']) : '';
					}

					$valuesArray[] = html_entity_decode($value);
				}

				if($headings) {
					fputcsv($file, $HeadingsArray);
				}
				fputcsv($file, $valuesArray);
				$headings = false;
			}
			break;
		case 'Contact Information':
		default:
			$field_config = explode(',', mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name`='".FOLDER_NAME."' AND `tab`='$category' AND `subtab` = '**no_subtab**'"))[0] . ',' . mysqli_fetch_array(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name`='".FOLDER_NAME."' AND `tab`='$category' AND `subtab` = 'additions'"))[0]);

			$select_list = ['`contacts`.`contactid`','`tile_name`','`category`'];
			foreach($tab_list as $tab_label => $tab_data) {
				if(in_array_any($tab_data[1],$field_config)) {
					foreach($tab_data[1] as $key => $field_option) {
						if(in_array($field_option,$field_config) && is_string($key) && $key != 'contactid') {
							$key = trim($key, '#');
							$check_field_exists = mysqli_query($dbc, "SELECT * FROM `information_schema`.`COLUMNS` WHERE `TABLE_SCHEMA` = '".DATABASE_NAME."' AND (`TABLE_NAME` = 'contacts' OR `TABLE_NAME` = 'contacts_cost' OR `TABLE_NAME` = 'contacts_dates' OR `TABLE_NAME` = 'contacts_description' OR `TABLE_NAME` = 'contacts_medical') AND `COLUMN_NAME` = '$key'");
							if(mysqli_num_rows($check_field_exists) > 0) {
								$select_list[] = '`'.$key.'`';	
							}
						}
					}
				}
			}
			$select_list = array_unique($select_list);
			$select_empty = '';
			for ($i = 0; $i < count($select_list); $i++) {
				$select_empty .= "'',";
			}
			$select_empty = rtrim($select_empty, ',');
			$select_list = implode(',', $select_list);

			$sql = "SELECT $select_list FROM `contacts` LEFT JOIN `contacts_cost` ON `contacts`.`contactid`=`contacts_cost`.`contactid` LEFT JOIN `contacts_dates` ON `contacts`.`contactid`=`contacts_dates`.`contactid` LEFT JOIN `contacts_description` ON `contacts`.`contactid`=`contacts_description`.`contactid` LEFT JOIN `contacts_medical` ON `contacts`.`contactid`=`contacts_medical`.`contactid` WHERE `contacts`.`deleted` = 0 AND `contacts`.`tile_name` = '".FOLDER_NAME."' AND `contacts`.`contactid` IN (".implode(',',$contact_export_list).")";
			if($category != '3456780123456971230') {
				$sql .= " AND `contacts`.`category`='$category'";
			}
			$sql = "SELECT * FROM (".$sql." UNION SELECT $select_empty) export_table";
			$result = mysqli_query($dbc, $sql);

			$headings = true;
			$HeadingsArray = array();

			while($row = mysqli_fetch_assoc($result)) {
				$valuesArray=array();
				foreach($row as $name => $value){
					if($name == 'businessid') {
						$name = 'businessid_name';
						$value = get_contact($dbc, $value, 'name');
					} else if($name == 'siteid') {
						$name = 'siteid_name';
						$value = get_contact($dbc, $value, "IFNULL(NULLIF(`site_name`,''),`display_name`)");
					} else if(isEncrypted($name)) {
						$value = decryptIt($value);
					}
					
					if($headings) {
						$HeadingsArray[] = $name;
					}
					$valuesArray[]=html_entity_decode($value);
				}

				if($headings) {
					fputcsv($file, $HeadingsArray);
					$headings = false;
				}
				fputcsv($file,$valuesArray);
			}
			break;
	}

	fclose($file);
	// print_r($contact_export_list);
	header("Location: $FileName");
	header('Content-Type: application/csv');
	header('Content-Disposition: attachment; filename='.str_replace('exports/','',$FileName));
	header('Pragma: no-cache');
	if($category == '3456780123456971230') {
		$update_log = 'All '.$export_option.' exported.';
	} else {
		$update_log = 'All '.$export_option.' under the '.$category.' category exported.';
	}

	$today_date = date('Y-m-d H:i:s', time());
	$contactid = $_SESSION['contactid'];
	$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contactid'");
	while($row = mysqli_fetch_assoc($result)) {
		$name = decryptIt($row['first_name']).' '.decryptIt($row['last_name']).' ('.$row['contactid'].')';
	}
	$query_insert_customer = "INSERT INTO `import_export_log` (table_name, type, description, date_time, contact) VALUES ('Contacts', 'Export', '$update_log', '$today_date', '$name')";
	$result_insert_customer = mysqli_query($dbc, $query_insert_customer);
}
?>

<script type="text/javascript">
$(document).ready(function() {
	window.submitExport = false;
	$('[name="export_contacts"]').click(function() {
		if($('[name="export_option_select"]').find('option').length > 1) {
			return exportDialog(window.submitExport);
		} else {
			return true;
		}
	});

	function exportDialog(submitExport) {
		if(!submitExport){
	        $( "#dialog-export" ).dialog({
	            resizable: true,
	            height: 500,
	            width: ($(window).width() <= 600 ? $(window).width() : 600),
	            modal: true,
	            buttons: {
	                "Export CSV": function() {
	                	window.submitExport = true;
	                    $(this).dialog('close');
	                    $('[name="export_contacts"]').trigger('click');
	                },
	                Cancel: function() {
	                    window.submitExport = false;
	                    $(this).dialog('close');
	                }
	          }
	        });
			return false;
		} else {
	    	$('[name="export_option"]').val($('[name="export_option_select"]').val());
	    	window.submitExport = false;
			return true;
		}
	}
});
</script>
<?php
$additions_config = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT `contacts` FROM `field_config_contacts` WHERE `tile_name` ='".FOLDER_NAME."' AND tab='".$list."' AND subtab = 'additions'"))['contacts'];
$additions_config = explode(',',$additions_config);
$additions_export_list = ['Client Support Plan' => 'Individual Service Plan','Medication Details' => 'Medication Details','Client Medical Charts' => 'Medical Charts','Client Activities Social Story' => 'Activities','Client Communication Social Story' => 'Communication','Client Routines Social Story' => 'Routines','Client Protocols Social Story' => 'Protocols','Client Key Methodologies Social Story' => 'Key Methodologies'];
?>
<div id="dialog-export" title="Export Options" style="display: none;">
	<div class="form-group">
	Please choose the information you would like to be exported:<br /><br />
		<label class="col-sm-4 control-label">Information:</label>
		<div class="col-sm-8">
			<select name="export_option_select" class="chosen-select-deselect form-control">
				<option value="Contact Information" selected>Contact Information</option>
				<?php foreach ($additions_export_list as $key => $value) {
					if(in_array($key, $additions_config)) {
						if ($value == 'Medical Charts') {
							echo '<option value="Medical Charts - Bowel Movement">Charts - Bowel Movement</option>';
							echo '<option value="Medical Charts - Seizure Record">Charts - Seizure Record</option>';
							echo '<option value="Medical Charts - Daily Water Temp">Charts - Client Daily Water Temp</option>';
							echo '<option value="Medical Charts - Blood Glucose">Charts - Blood Glucose</option>';
						} else {
							echo '<option value="'.$value.'">'.$value.'</option>';
						}
					}
				} ?>
			</select>
		</div>
	</div>
</div>