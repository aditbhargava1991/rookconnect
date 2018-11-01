<?php
error_reporting(0);

global $config;

$config['tile_name'] = 'Daily Log Notes';

function config_visible_function_log_notes($dbc)
{
	//return (config_visible_function($dbc, 'medication') == 1);
	return true;
}

function vuaed_visible_function_log_notes($dbc)
{
	//return (vuaed_visible_function($dbc, 'medication') == 1);
	return true;
}

$config['tabs'] = array (
    'Daily Log Notes' => 'daily_log_notes.php',
    'Reporting' => 'reporting.php'
);

/* Daily Log Notes */
$config['settings']['Choose Fields for Daily Log Notes']['config_field'] = 'daily_log_notes';
$config['settings']['Choose Fields for Daily Log Notes']['data'] = array(
	'General' => array(
			array('Daily Log Notes', 'textarea', 'notes'),
			array('Staff', 'dropdown', 'staff'),
			array('Completed Date', 'date', 'completed_date'),
			array('Start Time', 'text', 'start_time'),
			array('End Time', 'text', 'end_time'),
			array('Completed By', 'text', 'completed_by'),
			array('Signature Box', 'sign', 'signature_box')
		)
);

$config['settings']['Choose Fields for Daily Log Notes Dashboard']['config_field'] = 'daily_log_notes_dashboard';
$config['settings']['Choose Fields for Daily Log Notes Dashboard']['data'] = array(
	'General' => array(
			array('Daily Log Notes', 'textarea', 'notes'),
			array('Staff', 'dropdown', 'staff'),
			array('Completed Date', 'text', 'completed_date'),
			array('Start Time', 'text', 'start_time'),
			array('End Time', 'text', 'end_time'),
			array('Completed By', 'text', 'completed_by'),
			array('Signature Box', 'sign', 'signature_box')
		)
);

function get_tabs_log_notes($tab = '')
{
	global $config;

	$html = '';
	foreach($config['tabs'] as $title => $url) {
		$active = '';
		if($title == $tab) {
			$active = 'active_tab';
		}
		$html .= "<a href='".$url."'><button type='button' class='btn brand-btn mobile-block ".$active."' >".$title."</button></a>";
	}
	return $html;
}

function get_all_inputs_log_notes($data) {
	global $config;
	$fields = array();

	if(isset($data)) {
		foreach($data as $tabs) {
			foreach($tabs as $field) {
				$fields[] = $field[2];

			}
		}
	}
	return $fields;
}

function get_post_inputs_log_notes($data) {
	global $config;
	$fields = array();
	foreach($data as $tabs) {
		foreach($tabs as $field) {
			if($field[1] == 'upload') {
				$fields[$field[2]] = $_FILES[$field[2]]["name"];
				if($fields[$field[2]] == '') {
					if(isset($_POST[$field[2].'_hidden'])) {
						$fields[$field[2]] = $_POST[$field[2].'_hidden'];
					}
				}
			} elseif($field[1] == 'widget') {
				$fields[$field[2]] = serialize($_POST[$field[2]]);
			} else {
				$fields[$field[2]] = filter_var(htmlentities($_POST[$field[2]], FILTER_SANITIZE_STRING));
			}
		}
	}
	return $fields;
}

function get_post_uploads_log_notes($data) {
	global $config;
	$fields = array();

	foreach($data as $tabs) {
		foreach($tabs as $field) {
			if($field[1] == 'upload') {
				$fields[$field[2]] = $_FILES[$field[2]]["name"];
			}
		}
	}
	return $fields;
}

function move_files_log_notes($files) {
	foreach($files as $file => $name) {
		move_uploaded_file($_FILES[$file]["tmp_name"], "download/". $_FILES[$file]["name"]);
	}
}

function prepare_insert_log_notes($ins_data = array(), $table = '') {
	$columns = implode(", ",array_keys($ins_data));
	//$escaped_values = array_map('mysql_real_escape_string', array_values($ins_data));
	$values = '';
	foreach($ins_data as $tmp) {
		$values .= "'".$tmp."', ";
	}
	$values = trim($values,', ');
	$sql = "INSERT INTO `".$table."` ($columns) VALUES ($values)";
	return $sql;
}

function prepare_update_log_notes($up_data = array(), $table = '', $key = '', $value = '') {
	$fields = array();
	foreach($up_data as $field => $val) {
		$fields[] = "$field = '$val'";
	}
	$sql = "UPDATE ".$table." SET " . join(', ', $fields) . " WHERE ".$key." = '".$value."'";
	return $sql;
}

function get_field_log_notes($field, $value, $dbc = '', $other = '')
{
	$html = '';

	if($field[2] == 'staff') {
   		$html .= contact_call_log_notes($dbc, 'staff', 'staff', $value, '', 'Staff');
   	} else if($field[1] == 'text') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <input name="'.$field[2].'" class="form-control '.(strpos($field[2],'time') !== FALSE ? 'datetimepicker' : '').'" value="'.$value.'" />
                    </div>
                  </div>';
   	} else if($field[1] == 'sign') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <input name="'.$field[2].'" class="form-control" value="'.$value.'" />
                    </div>
                  </div>';
   	} else if($field[1] == 'date') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <input name="'.$field[2].'" class="datepicker form-control" value="'.$value.'" />
                    </div>
                  </div>';
   	} else if($field[1] == 'textarea') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <textarea name="'.$field[2].'" rows="5" cols="50" class="form-control">'.$value.'</textarea>
                    </div>
                  </div>';
    } else if($field[1] == 'upload') {
    	$html .= '<div class="form-group">
                    <label for="file" class="col-sm-4 control-label">Support Documents
                    <span class="popover-examples list-inline">&nbsp;

                    </span>
                    </label>
                    <div class="col-sm-8">';
                    if($value!='') {
                    	$html .= $value.' <a href="download/'.$value.'" target="_blank">View</a>';
	                    $html .= '<input type="hidden" name="'.$field[2].'_hidden" value="'.$value.'" />
	                    <input name="'.$field[2].'" type="file" data-filename-placement="inside" class="form-control" />';
                    } else {
                    	$html .= '<input name="'.$field[2].'" type="file" data-filename-placement="inside" class="form-control" />';
                    }
                    $html .= '</div>
                 </div>';
    }
	return $html;
}


function contact_call_log_notes($dbc, $select_id, $select_name, $contact_value,$multiple, $from_contact) {
    ?>
    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">Contact:</label>
        <div class="col-sm-8">
            <select <?php echo $multiple; ?> data-placeholder="Select a Contact..." name="<?php echo $select_name; ?>" id="<?php echo $select_id; ?>" class="chosen-select-deselect form-control" width="380">
              <?php
              	$query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE category IN (".STAFF_CATS.") AND ".STAFF_CATS_HIDE_QUERY." order by name");
                echo '<option value=""></option>';
                while($row = mysqli_fetch_array($query)) {
                    if($row['name'] != '') { ?>
                        <option <?php if (strpos($contact_value, $row['contactid']) !== FALSE) {
			            echo " selected"; } ?> value='<?php echo $row['contactid']; ?>'><?php echo decryptIt($row['name']); ?></option>
                    <?php } else { ?>
                        <option <?php if (strpos($contact_value, $row['contactid']) !== FALSE) {
			            echo " selected"; } ?> value='<?php echo $row['contactid']; ?>'><?php echo decryptIt($row['first_name']).' '.decryptIt($row['last_name']); ?></option>
                    <?php
                    }
                }
             ?>
            </select>
        </div>
    </div>
<?php }
