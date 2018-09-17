<?php

function get_tabs_contact_ss($tab = '')
{
	global $config_contact_ss;

	$html = '';
	foreach($config_contact_ss['tabs'] as $title => $url) {
		$active = '';
		if($title == $tab) {
			$active = 'active_tab';
		}
		$html .= "<a href='".$url."'><button type='button' class='btn brand-btn mobile-block ".$active."' >".$title."</button></a>";
	}
	return $html;
}

function get_all_inputs_contact_ss($data) {
	global $config_contact_ss;
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

function get_post_inputs_contact_ss($data) {
	global $config_contact_ss;
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

function get_post_uploads_contact_ss($data) {
	global $config_contact_ss;
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

function move_files_contact_ss($files) {
	foreach($files as $file => $name) {
		move_uploaded_file($_FILES[$file]["tmp_name"], "download/". $_FILES[$file]["name"]);
	}
}

function prepare_insert_contact_ss($ins_data = array(), $table = '') {
	$columns = implode(", ",array_keys($ins_data));
	$escaped_values = array_map('mysql_real_escape_string', array_values($ins_data));
	$values = '';
	foreach($escaped_values as $tmp) {
		$values .= "'".$tmp."', ";
	}
	$values = trim($values,', ');
	$sql = "INSERT INTO `".$table."` ($columns) VALUES ($values)";
	return $sql;
}

function prepare_update_contact_ss($up_data = array(), $table = '', $key = '', $value = '') {
	$fields = array();
	foreach($up_data as $field => $val) {
		$fields[] = "$field = '$val'";
	}
	$sql = "UPDATE ".$table." SET " . join(', ', $fields) . " WHERE ".$key." = '".$value."'";
	return $sql;
}

function get_field_contact_ss($field, $value, $dbc = '', $contact = 0, $other = '', $disabled = '', $data_table, $data_row_field, $data_row_id, $data_contactid_field)
{
	$html = '';

	if($field[2] == 'incident_widget') {

		$query_check_credentials = "SELECT * FROM incident_report WHERE (CONCAT(',',`contactid`,',') LIKE '%,$contact,%' AND `contactid` != '') OR (CONCAT(',',`clientid`,',') LIKE '%,$contact,%' AND `clientid` != '') ORDER BY incidentreportid DESC";
		$result = mysqli_query($dbc, $query_check_credentials);
		$num_rows = mysqli_num_rows($result);

        if($num_rows > 0) {
			$html .= '<table class="table table-bordered">';

			$html .= '<tr>';
			$html .= '<th></th>';
			$html .= '<th>Name of Client</th>';
			$html .= '<th>Completed By: Staff Name</th>';
			$html .= '<th>Date of Incident</th>';
			$html .= '<th>Location of Incident</th>';
			$html .= '<th>PDF</th>';
			$html .= '</tr>';
			while($row = mysqli_fetch_array( $result ))
        	{
        		$checked = '';
        		if(in_array($row['incidentreportid'], $value)) {
        			$checked = 'checked';
        		}

        		$name_of_file = 'incident_report_'.$row['incidentreportid'].'.pdf';
				$html .= '<tr>';
				$html .= '<td><input type="checkbox" '.$checked.' name="incident_widget[]" value="'.$row['incidentreportid'].'"/></td>';
				$html .= '<td>'.get_staff($dbc, $row['contactid']).'</td>';
				$html .= '<td>'.get_staff($dbc, $row['contactid']).'</td>';
				$html .= '<td>'.$row['today_date'].'</td>';
				$html .= '<td>'.$row['location'].'</td>';
				$html .= '<td><a href="'.WEBSITE_URL.'/Incident Report/download/'.$name_of_file.'" target="_blank" >PDF</a></td>';
				$html .= '</tr>';
			}

			$html .= '</table><br>';
		} else {
			$html .= '<p>No '.INC_REP_NOUN.' found.</p>';
		}
	} else if($field[2] == 'support_contact_category') {
   		return contact_category_call_contact_ss($dbc, 'contact_category_0', 'support_contact_category', $value, $disabled);
   	} else if($field[2] == 'support_contact') {
   		return contact_call_contact_ss($dbc, 'contact_0', 'support_contact', $value, '', $other, $disabled);
   	} else if($field[2] == 'status') {

   		if($value == 'Suspend') {
   			$options = '<option value=""></option><option value="Suspend" selected>Suspend</option><option value="Active">Active</option><option value="Archive">Archive</option>';
   		} elseif($value == 'Active') {
   			$options = '<option value=""></option><option value="Suspend" selected>Suspend</option><option value="Active" selected>Active</option><option value="Archive">Archive</option>';
   		} elseif($value == 'Archive') {
   			$options = '<option value=""></option><option value="Suspend" selected>Suspend</option><option value="Active">Active</option><option value="Archive" selected>Archive</option>';
   		} else {
   			$options = '<option value=""></option><option value="Suspend">Suspend</option><option value="Active">Active</option><option value="Archive">Archive</option>';
   		}

   		$html .= '<div class="form-group">
                <label for="travel_task" class="col-sm-4 control-label">Status:</label>
                <div class="col-sm-8">
                  <select id="status" name="status" class="chosen-select-deselect form-control" width="380" data-field="'.$field[2].'" data-table="'.$data_table.'" data-row-field="'.$data_row_field.'" data-row-id="'.$data_row_id.'" data-contactid-field="'.$data_contactid_field.'" data-contactid="'.$contact.'">
                    '.$options.'
                  </select>
                </div>
              </div>';
   	} else if($field[1] == 'text') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <input type="text" name="'.$field[2].'" class="form-control" data-field="'.$field[2].'" data-table="'.$data_table.'" data-row-field="'.$data_row_field.'" data-row-id="'.$data_row_id.'" data-contactid-field="'.$data_contactid_field.'" data-contactid="'.$contact.'" value="'.$value.'">
                    </div>
                  </div>';
   	} else if($field[1] == 'textarea') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <textarea name="'.$field[2].'" rows="5" cols="50" class="form-control" data-field="'.$field[2].'" data-table="'.$data_table.'" data-row-field="'.$data_row_field.'" data-row-id="'.$data_row_id.'" data-contactid-field="'.$data_contactid_field.'" data-contactid="'.$contact.'">'.$value.'</textarea>
                    </div>
                  </div>';
    } else if($field[1] == 'textarea_hidden') {
    $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM key_methodologies WHERE support_contact='".$contact."' ORDER BY `keymethodologiesid` DESC"));
    $html .= '<script type="text/javascript">
          $(document).ready(function() {
              $(\'[name="'.$field[3].'"]\').change(function() {
                if($(\'[name="'.$field[3].'"]:checked\').val() == \''.$field[4].'\') {
                  $(\'.ss_'.$field[2].'\').show();
                } else {
                  $(\'.ss_'.$field[2].'\').hide();
                }
            });
          });
        </script>';
    $html .= '<div class="form-group ss_'.$field[2].'" '.($get_contact[$field[3]] == $field[4] ? '' : 'style="display:none;"').'>
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <textarea name="'.$field[2].'" rows="5" cols="50" class="form-control" data-field="'.$field[2].'" data-table="'.$data_table.'" data-row-field="'.$data_row_field.'" data-row-id="'.$data_row_id.'" data-contactid-field="'.$data_contactid_field.'" data-contactid="'.$contact.'">'.$value.'</textarea>
                    </div>
                  </div>';
   	} else if($field[1] == 'yes_default_no') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <label class="form-checkbox"><input type="radio" name="'.$field[2].'" '.($value == 'Yes' ? 'checked' : '').' data-field="'.$field[2].'" data-table="'.$data_table.'" data-row-field="'.$data_row_field.'" data-row-id="'.$data_row_id.'" data-contactid-field="'.$data_contactid_field.'" data-contactid="'.$contact.'" value="Yes">Yes</label>
                      <label class="form-checkbox"><input type="radio" name="'.$field[2].'" '.($value == 'Yes' ? '' : 'checked').' data-field="'.$field[2].'" data-table="'.$data_table.'" data-row-field="'.$data_row_field.'" data-row-id="'.$data_row_id.'" data-contactid-field="'.$data_contactid_field.'" data-contactid="'.$contact.'" value="No">No</label>
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
	                    <input name="'.$field[2].'" type="file" data-filename-placement="inside" class="form-control" data-field="'.$field[2].'" data-table="'.$data_table.'" data-row-field="'.$data_row_field.'" data-row-id="'.$data_row_id.'" data-contactid-field="'.$data_contactid_field.'" data-contactid="'.$contact.'" />';
                    } else {
                    	$html .= '<input name="'.$field[2].'" type="file" data-filename-placement="inside" class="form-control" data-field="'.$field[2].'" data-table="'.$data_table.'" data-row-field="'.$data_row_field.'" data-row-id="'.$data_row_id.'" data-contactid-field="'.$data_contactid_field.'" data-contactid="'.$contact.'" />';
                    }
                    $html .= '</div>
                 </div>';
    }
	return $html;
}


function contact_category_call_contact_ss($dbc, $select_id, $select_name, $contact_category_value, $disabled) {
    ?>
    <script type="text/javascript">
    $(document).on('change', 'select[name="<?= $select_name ?>"]', function() { selectContactCategorySS(this); });
    </script>
    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">Contact Category:</label>
        <div class="col-sm-8">
            <select <?php echo $disabled; ?> data-placeholder="Choose a Category..." id="<?php echo $select_id; ?>" name="<?php echo $select_name; ?>" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $tabs = get_config($dbc, 'contacts_tabs');
                $each_tab = explode(',', $tabs);
                foreach ($each_tab as $cat_tab) {
                    ?>
                    <option <?php if (strpos($contact_category_value, $cat_tab) !== FALSE) {
			        echo " selected"; } ?> value='<?php echo $cat_tab; ?>'><?php echo $cat_tab; ?></option>
                <?php }
                if (!in_array($each_tab, $contact_category_value)) {
                    echo "<option selected value='$contact_category_value'>$contact_category_value</option>";
                }
              ?>
            </select>
        </div>
    </div>
<?php }

function contact_call_contact_ss($dbc, $select_id, $select_name, $contact_value,$multiple, $from_contact, $disabled) {
    ?>
    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">Contact:</label>
        <div class="col-sm-8">
            <select <?php echo $disabled; ?> <?php echo $multiple; ?> data-placeholder="Choose a Contact..." name="<?php echo $select_name; ?>" id="<?php echo $select_id; ?>" class="chosen-select-deselect form-control" width="380">
              <?php if($contact_value != '') {

                $query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE category = '$from_contact' order by name");
                echo '<option value=""></option>';
                $selected_contact = false;
                while($row = mysqli_fetch_array($query)) {
                    if(decryptIt($row['name']) != '') { ?>
                        <option <?php if (strpos($contact_value, $row['contactid']) !== FALSE) {
			            echo " selected"; $selected_contact = true; } ?> value='<?php echo $row['contactid']; ?>'><?php echo decryptIt($row['name']); ?></option>
                    <?php } else { ?>
                        <option <?php if (strpos($contact_value, $row['contactid']) !== FALSE) {
			            echo " selected"; $selected_contact = true; } ?> value='<?php echo $row['contactid']; ?>'><?php echo decryptIt($row['first_name']).' '.decryptIt($row['last_name']); ?></option>
                    <?php
                    }
                }
                if (!$selected_contact) {
                    echo "<option selected value='$contact_value'>".get_contact($dbc, $contact_value)."</option>";
                }
             } ?>
            </select>
        </div>
    </div>
<?php }
?>