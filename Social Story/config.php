<?php
error_reporting(0);

global $config;

$config['tile_name'] = 'Social Story';

function config_visible_function_social($dbc)
{
	//return (config_visible_function($dbc, 'medication') == 1);
	return true;
}

function vuaed_visible_function_social($dbc)
{
	//return (vuaed_visible_function($dbc, 'medication') == 1);
	return true;
}

$config['tabs'] = array (
    'Key Methodologies' => 'key_methodologies.php',
    'Learning Techniques' => 'learning_techniques.php',
    'Protocols' => 'protocols.php',
    'Patterns' => 'patterns.php',
    'Routines' => 'routines.php',
    'Communication' => 'communication.php',
    'Activities' => 'activities.php'
);

/* Activities */
$config['settings']['Choose Fields for Activities']['config_field'] = 'activities';
$config['settings']['Choose Fields for Activities']['data'] = array(
	'Support Individual' => array(
			array('Contact Category', 'dropdown', 'support_contact_category'),
			array('Contact', 'dropdown', 'support_contact')
		),
	'Likes' => array(
			array('Likes Morning Routine', 'textarea', 'likes_morning_routine'),
			array('Support Documents', 'upload', 'likes_morning_routine_upload'),
		),
	'Wants' => array(
			array('Wants Morning Routine', 'textarea', 'wants_morning_routine'),
			array('Support Documents', 'upload', 'wants_morning_routine_upload'),
		),
	'Dreams' => array(
			array('Dreams Morning Routine', 'textarea', 'dreams_morning_routine'),
			array('Support Documents', 'upload', 'dreams_morning_routine_upload'),
		),
	'Aspirations' => array(
			array('Aspirations Morning Routine', 'textarea', 'aspirations_morning_routine'),
			array('Support Documents', 'upload', 'aspirations_morning_routine_upload'),
		),
	'At Their Best' => array(
			array('At Their Best Morning Routine', 'textarea', 'at_their_best_morning_routine'),
			array('Support Documents', 'upload', 'at_their_best_morning_routine_upload'),
		),
	'Dislikes' => array(
			array('Dislikes Morning Routine', 'textarea', 'dislikes_morning_routine'),
			array('Support Documents', 'upload', 'dislikes_morning_routine_upload'),
		),
	'Stressors' => array(
			array('Stressors Morning Routine', 'textarea', 'stressors_morning_routine'),
			array('Support Documents', 'upload', 'stressors_morning_routine_upload'),
		),
	'At Their Worst' => array(
			array('At Their Worst Morning Routine', 'textarea', 'at_their_worst_morning_routine'),
			array('Support Documents', 'upload', 'at_their_worst_morning_routine_upload'),
		),
	'Triggers' => array(
			array('Triggers Morning Routine', 'textarea', 'triggers_morning_routine'),
			array('Support Documents', 'upload', 'triggers_morning_routine_upload'),
		),
	'Strategies That Work' => array(
			array('Strategies That Work Morning Routine', 'textarea', 'strategies_that_work_morning_routine'),
			array('Support Documents', 'upload', 'strategies_that_work_morning_routine_upload'),
		),
	'Strategies That Don\'t Work' => array(
			array('Strategies That Don\'t Work Morning Routine', 'textarea', 'strategies_that_dont_work_morning_routine'),
			array('Support Documents', 'upload', 'strategies_that_dont_work_morning_routine_upload'),
		),
	'Notes' => array(
			array('Notes', 'textarea', 'notes_details'),
			array('Support Documents', 'upload', 'notes_upload'),
		),
	'Incident Reports' => array(
		array('Incident Reports', 'widget', 'incident_widget'),
		array('Support Documents', 'upload', 'incident_upload'),
	)
);

$config['settings']['Choose Fields for Activities Dashboard']['config_field'] = 'activities_dashboard';
$config['settings']['Choose Fields for Activities Dashboard']['data'] = array(
	'General' => array(
			array('Contact Category', 'dropdown', 'support_contact_category'),
			array('Contact', 'dropdown', 'support_contact'),
			array('Likes Morning Routine', 'textarea', 'likes_morning_routine'),
			array('Wants Morning Routine', 'textarea', 'wants_morning_routine'),
			array('Dreams Morning Routine', 'textarea', 'dreams_morning_routine'),
			array('Aspirations Morning Routine', 'textarea', 'aspirations_morning_routine'),
			array('At Their Best Morning Routine', 'textarea', 'at_their_best_morning_routine'),
			array('Dislikes Morning Routine', 'textarea', 'dislikes_morning_routine'),
			array('Stressors Morning Routine', 'textarea', 'stressors_morning_routine'),
			array('At Their Worst Morning Routine', 'textarea', 'at_their_worst_morning_routine'),
			array('Triggers Morning Routine', 'textarea', 'triggers_morning_routine'),
			array('Strategies That Work Morning Routine', 'textarea', 'strategies_that_work_morning_routine'),
			array('Strategies That Don\'t Work Morning Routine', 'textarea', 'strategies_that_dont_work_morning_routine'),
			array('Notes', 'textarea', 'notes_details'),
		)
);


/* Communication */
$config['settings']['Choose Fields for Communication']['config_field'] = 'communication';
$config['settings']['Choose Fields for Communication']['data'] = array(
	'Support Individual' => array(
			array('Contact Category', 'dropdown', 'support_contact_category'),
			array('Contact', 'dropdown', 'support_contact')
		),
	'Methods' => array(
			array('Methods Morning Routine', 'textarea', 'methods_morning_routine'),
			array('Support Documents', 'upload', 'methods_morning_routine_upload'),
		),
	'Techniques' => array(
			array('Techniques Morning Routine', 'textarea', 'techniques_morning_routine'),
			array('Support Documents', 'upload', 'techniques_morning_routine_upload'),
		),
	'Capabilities' => array(
			array('Capabilities Morning Routine', 'textarea', 'capabilities_morning_routine'),
			array('Support Documents', 'upload', 'capabilities_morning_routine_upload'),
		),
	'Inabilities' => array(
			array('Inabilities Morning Routine', 'textarea', 'inabilities_morning_routine'),
			array('Support Documents', 'upload', 'inabilities_morning_routine_upload'),
		),
	'Strategies' => array(
			array('Strategies Morning Routine', 'textarea', 'strategies_morning_routine'),
			array('Support Documents', 'upload', 'strategies_morning_routine_upload'),
		),
	'Notes' => array(
			array('Notes', 'textarea', 'notes_details'),
			array('Support Documents', 'upload', 'notes_upload'),
		),
	'Incident Reports' => array(
		array('Incident Reports', 'widget', 'incident_widget'),
		array('Support Documents', 'upload', 'incident_upload'),
	)
);

$config['settings']['Choose Fields for Communication Dashboard']['config_field'] = 'communication_dashboard';
$config['settings']['Choose Fields for Communication Dashboard']['data'] = array(
	'General' => array(
			array('Contact Category', 'dropdown', 'support_contact_category'),
			array('Contact', 'dropdown', 'support_contact'),
			array('Methods', 'textarea', 'methods_morning_routine'),
			array('Techniques', 'textarea', 'techniques_morning_routine'),
			array('Capabilities', 'textarea', 'capabilities_morning_routine'),
			array('Inabilities', 'textarea', 'inabilities_morning_routine'),
			array('Strategies', 'textarea', 'strategies_morning_routine'),
			array('Notes', 'textarea', 'notes_details'),
		)
);

/* Routines */
$config['settings']['Choose Fields for Routines']['config_field'] = 'routines';
$config['settings']['Choose Fields for Routines']['data'] = array(
	'Support Individual' => array(
			array('Contact Category', 'dropdown', 'support_contact_category'),
			array('Contact', 'dropdown', 'support_contact')
		),
	'Morning' => array(
			array('Morning Routine', 'textarea', 'morning_routine'),
			array('Morning Snack', 'text', 'morning_snack'),
			array('Support Documents', 'upload', 'morning_routine_upload'),
		),
	'Afternoon' => array(
			array('Afternoon Routine', 'textarea', 'afternoon_routine'),
			array('Afternoon Snack', 'text', 'afternoon_snack'),
			array('Support Documents', 'upload', 'afternoon_routine_upload'),
		),
	'Evening' => array(
			array('Evening Routine', 'textarea', 'evening_routine'),
			array('Evening Snack', 'text', 'evening_snack'),
			array('Support Documents', 'upload', 'evening_routine_upload'),
		),
	'Bedtime' => array(
			array('Bedtime Routine', 'textarea', 'bedtime_routine'),
			array('Bedtime Snack', 'text', 'bedtime_snack'),
			array('Support Documents', 'upload', 'bedtime_routine_upload'),
		),
	'First Aid/CPR' => array(
			array('First Aid/CPR', 'textarea', 'first_aid_cpr'),
			array('Support Documents', 'upload', 'first_aid_cpr_upload'),
		),
	'Toileting' => array(
			array('Toileting Routine', 'textarea', 'toileting_routine'),
			array('Support Documents', 'upload', 'toileting_routine_upload'),
		),
	'Bathing' => array(
			array('Bathing Routine', 'textarea', 'bathing_routine'),
			array('Support Documents', 'upload', 'bathing_routine_upload'),
		),
	'Feeding' => array(
			array('Feeding Routine', 'textarea', 'feeding_routine'),
			array('Support Documents', 'upload', 'feeding_routine_upload'),
		),
	'Notes' => array(
			array('Notes', 'textarea', 'notes_details'),
			array('Support Documents', 'upload', 'notes_upload'),
		),
	'Incident Reports' => array(
			array('Incident Reports', 'widget', 'incident_widget'),
			array('Support Documents', 'upload', 'incident_upload'),
		)
);

$config['settings']['Choose Fields for Routines Dashboard']['config_field'] = 'routines_dashboard';
$config['settings']['Choose Fields for Routines Dashboard']['data'] = array(
	'General' => array(
			array('Contact Category', 'dropdown', 'support_contact_category'),
			array('Contact', 'dropdown', 'support_contact'),
			array('Morning Routine', 'textarea', 'morning_routine'),
			array('Afternoon Routine', 'textarea', 'afternoon_routine'),
			array('Evening Routine', 'textarea', 'evening_routine'),
			array('First Aid/CPR', 'textarea', 'first_aid_cpr_details'),
			array('Toileting Routine', 'textarea', 'toileting_routine'),
			array('Bathing Routine', 'textarea', 'bathing_routine'),
			array('Feeding Routine', 'textarea', 'feeding_routine'),
			array('Notes', 'textarea', 'notes_details'),
		)
);
/* Key Methodologies */

$config['settings']['Choose Fields for Key Methodologies Dashboard']['config_field'] = 'key_methodologies_dashboard';
$config['settings']['Choose Fields for Key Methodologies Dashboard']['data'] = array(
	'General' => array(
			array('Contact Category', 'dropdown', 'support_contact_category'),
			array('Contact', 'dropdown', 'support_contact'),
			array('Likes', 'textarea', 'best_likes'),
			array('Wants', 'textarea', 'best_wants'),
			array('Dreams', 'textarea', 'best_dreams'),
			array('Aspirations', 'textarea', 'best_asp'),
			array('Dislikes', 'textarea', 'worst_dislike'),
			array('Stresses', 'textarea', 'worst_stresses'),
			array('Strategies', 'textarea', 'worst_star'),
			array('What to look for', 'textarea', 'trigger_look'),
			array('What to note', 'textarea', 'trigger_note'),
			array('What Works', 'textarea', 'strategy_works'),
			array('What Doesnt', 'textarea', 'strategy_not'),
			array('Next Steps', 'textarea', 'strategy_step'),
			array('Display Goals', 'textarea', 'goal_display'),
			array('Recommend Future Goal', 'textarea', 'goal_future'),
			array('Status', 'dropdown', 'status')
		)
);

$config['settings']['Choose Fields for Key Methodologies']['config_field'] = 'key_methodologies';
$config['settings']['Choose Fields for Key Methodologies']['data'] = array(
	'Support Individual' => array(
			array('Contact Category', 'dropdown', 'support_contact_category'),
			array('Contact', 'dropdown', 'support_contact'),
			array('Do you require one to one care?', 'yes_default_no', 'one_to_one')
		),
	'At My Best' => array(
			array('Likes', 'textarea', 'best_likes'),
			array('Wants', 'textarea', 'best_wants'),
			array('Dreams', 'textarea', 'best_dreams'),
			array('Aspirations', 'textarea', 'best_asp'),
		),
	'At My Worst' => array(
			array('Dislikes', 'textarea', 'worst_dislike'),
			array('Stresses', 'textarea', 'worst_stresses'),
			array('Strategies', 'textarea', 'worst_star'),
		),
	'Triggers' => array(
			array('What To Look For', 'textarea', 'trigger_look'),
			array('What To Note', 'textarea', 'trigger_note'),
		),
	'Behaviours' => array(
			array('Do you have any behaviours that we should be aware of?', 'textarea', 'behaviours'),
		),
	'Strategies' => array(
			array('What Works', 'textarea', 'strategy_works'),
			array('What Doesnt', 'textarea', 'strategy_not'),
			array('Next Steps', 'textarea', 'strategy_step'),
		),
	'Goals' => array(
			array('Display Goals', 'textarea', 'goal_display'),
			array('Recommend Future Goal', 'textarea', 'goal_future'),
		),
	'Toileting' => array(
			array('Do you require extra support going to the bathroom?', 'yes_default_no', 'toileting'),
			array('Washroom Support Information', 'textarea_hidden', 'toileting_info', 'toileting', 'Yes')
		),
	'Status' => array(
			array('Status', 'dropdown', 'status')
		)
);


/* Protocols */
$config['settings']['Choose Fields for Protocols']['config_field'] = 'protocols';
$config['settings']['Choose Fields for Protocols']['data'] = array(
	'Support Individual' => array(
			array('Contact Category', 'dropdown', 'support_contact_category'),
			array('Contact', 'dropdown', 'support_contact')
		),
	'Seizure' => array(
			array('Seizure Protocol Details', 'textarea', 'seizure_protocol_details'),
			array('Support Documents', 'upload', 'seizure_upload'),
		),
	'Slip & Fall' => array(
			array('Slip & Fall Protocol Details', 'textarea', 'slip_fall_protocol_details'),
			array('Support Documents', 'upload', 'slip_fall_upload'),
		),
	'Transfer' => array(
			array('Transfer Details', 'textarea', 'transfer_protocol_details'),
			array('Support Documents', 'upload', 'transfer_upload'),
		),
	'Toileting' => array(
			array('Toileting Details', 'textarea', 'toileting_protocol_details'),
			array('Support Documents', 'upload', 'toileting_upload'),
		),
	'Bathing' => array(
			array('Bathing Protocol Details', 'textarea', 'bathing_protocol_details'),
			array('Support Documents', 'upload', 'bathing_upload'),
		),
	'G-Tube' => array(
			array('G-Tube Protocol Details', 'textarea', 'gtube_protocol_details'),
			array('Support Documents', 'upload', 'gtube_upload'),
		),
	'Oxygen' => array(
			array('Oxygen Protocol Details', 'textarea', 'oxygen_protocol_details'),
			array('Support Documents', 'upload', 'oxygen_upload'),
		),
	'Notes' => array(
			array('Notes Protocol Details', 'textarea', 'notes_protocol_details'),
			array('Support Documents', 'upload', 'note_upload'),
		),
	'Incident Reports' => array(
			array('Incident Reports', 'widget', 'incident_widget'),
			array('Support Documents', 'upload', 'incident_upload'),
		)
);

$config['settings']['Choose Fields for Protocols Dashboard']['config_field'] = 'protocols_dashboard';
$config['settings']['Choose Fields for Protocols Dashboard']['data'] = array(
	'General' => array(
			array('Contact Category', 'dropdown', 'support_contact_category'),
			array('Contact', 'dropdown', 'support_contact'),
			array('Seizure Protocol Details', 'textarea', 'seizure_protocol_details'),
			array('Slip & Fall Protocol Details', 'textarea', 'slip_fall_protocol_details'),
			array('Transfer Details', 'textarea', 'transfer_protocol_details'),
			array('Transfer Details', 'textarea', 'toileting_protocol_details'),
			array('Bathing Protocol Details', 'textarea', 'bathing_protocol_details'),
			array('G-Tube Protocol Details', 'textarea', 'gtube_protocol_details'),
			array('Oxygen Protocol Details', 'textarea', 'oxygen_protocol_details'),
			array('Notes Protocol Details', 'textarea', 'notes_protocol_details'),
		)
);

/* function get_tabs_social($tab = '')
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
} */

function get_tabs_social($tab = '')
{
	global $config;

	$html = '';
	foreach($config['tabs'] as $title => $url) {
		$active = '';
		$title_lower = strtolower(str_replace(' ', '_', $title));
        if ( check_subtab_persmission( $_SERVER['DBC'], 'social_story', ROLE, $title_lower ) === true ) {
            if($title == $tab) {
                $active = 'active_tab';
            }
            $html .= "<a href='".$url."'><button type='button' class='btn brand-btn mobile-block ".$active."' >".$title."</button></a>";
        } else {
            $html .= "<button type='button' class='btn brand-btn mobile-block disabled-btn'>".$title."</button></a>";
        }

	}
	return $html;
}

function get_all_inputs_social($data) {
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

function get_post_inputs_social($data) {
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
				$fields[$field[2]] = filter_var(htmlentities($_POST[$field[2]]), FILTER_SANITIZE_STRING);
			}
		}
	}
	return $fields;
}

function get_post_uploads_social($data) {
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

function move_files_social($files) {
	foreach($files as $file => $name) {
		move_uploaded_file($_FILES[$file]["tmp_name"], "download/". $_FILES[$file]["name"]);
	}
}

function prepare_insert_social($ins_data = array(), $table = '') {
	$columns = "`".implode("`, `",array_keys($ins_data))."`";
	$values = '';
	foreach($ins_data as $tmp) {
		$values .= "'".$tmp."', ";
	}
	$values = trim($values,', ');
	$sql = "INSERT INTO `".$table."` ($columns) VALUES ($values)";
	return $sql;
}

function prepare_update_social($up_data = array(), $table = '', $key = '', $value = '') {
	$fields = array();
	foreach($up_data as $field => $val) {
		$fields[] = "$field = '$val'";
	}
	$sql = "UPDATE ".$table." SET " . join(', ', $fields) . " WHERE ".$key." = '".$value."'";
	return $sql;
}

function get_field_social($field, $value, $dbc = '', $contact = 0, $other = '')
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
				$html .= '<td>'.$row['ir1'].'</td>';
				$html .= '<td><a href="'.WEBSITE_URL.'/Incident Report/download/'.$name_of_file.'" target="_blank" >PDF</a></td>';
				$html .= '</tr>';
			}

			$html .= '</table><br>';
		} else {
			$html .= '<p>No incident report found.</p>';
		}
	} else if($field[2] == 'support_contact_category') {
   		return contact_category_call_social($dbc, 'contact_category_0', 'support_contact_category', $value);
   	} else if($field[2] == 'support_contact') {
   		return contact_call_social($dbc, 'contact_0', 'support_contact', $value, '', $other);
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
                  <select id="status" name="status" class="chosen-select-deselect form-control" width="380">
                    '.$options.'
                  </select>
                </div>
              </div>';
   	} else if($field[1] == 'text') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <input type="text" name="'.$field[2].'" class="form-control" value="'.$value.'">
                    </div>
                  </div>';
   	} else if($field[1] == 'textarea') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <textarea name="'.$field[2].'" rows="5" cols="50" class="form-control">'.$value.'</textarea>
                    </div>
                  </div>';
   	} else if($field[1] == 'textarea_hidden') {
   		$html .= '<script type="text/javascript">
   					$(document).ready(function() {
   						$(\'[name="'.$field[3].'"]\').change(function() {
   							if($(\'[name="'.$field[3].'"]:checked\').val() == \''.$field[4].'\') {
   								$(\'.ss_'.$field[2].'\').show();
   							} else {
   								$(\'.ss_'.$field[2].'\').hide();
   							}
						}).change();
   					});
   				</script>';
		$html .= '<div class="form-group ss_'.$field[2].'" style="display:none;">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <textarea name="'.$field[2].'" rows="5" cols="50" class="form-control">'.$value.'</textarea>
                    </div>
                  </div>';
   	} else if($field[1] == 'yes_default_no') {
		$html .= '<div class="form-group">
                    <label for="'.$field[2].'" class="col-sm-4 control-label">'.$field[0].': </label>
                    <div class="col-sm-8">
                      <label class="form-checkbox"><input type="radio" name="'.$field[2].'" '.($value == 'Yes' ? 'checked' : '').' value="Yes">Yes</label>
                      <label class="form-checkbox"><input type="radio" name="'.$field[2].'" '.($value == 'Yes' ? '' : 'checked').' value="No">No</label>
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


function contact_category_call_social($dbc, $select_id, $select_name, $contact_category_value) {
    ?>
    <script type="text/javascript">
	$(document).on('change', 'select[name="<?= $select_name ?>"]', function() { selectContactCategory(this); });
    </script>
    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">Contact Category:</label>
        <div class="col-sm-8">
            <select data-placeholder="Choose a Category..." id="<?php echo $select_id; ?>" name="<?php echo $select_name; ?>" class="chosen-select-deselect form-control" width="380">
              <option value=""></option>
              <?php
                $tabs = get_config($dbc, 'contacts_tabs');
                $each_tab = explode(',', $tabs);
                foreach ($each_tab as $cat_tab) {
                    ?>
                    <option <?php if (strpos($contact_category_value, $cat_tab) !== FALSE) {
			        echo " selected"; } ?> value='<?php echo $cat_tab; ?>'><?php echo $cat_tab; ?></option>
                <?php }
              ?>
            </select>
        </div>
    </div>
<?php }

function contact_call_social($dbc, $select_id, $select_name, $contact_value,$multiple, $from_contact) {
    ?>
    <div class="form-group">
        <label for="fax_number"	class="col-sm-4	control-label">Contact:</label>
        <div class="col-sm-8">
            <select <?php echo $multiple; ?> data-placeholder="Choose a Contact..." name="<?php echo $select_name; ?>" id="<?php echo $select_id; ?>" class="chosen-select-deselect form-control" width="380">
              <?php if($contact_value != '') {

                $query = mysqli_query($dbc,"SELECT contactid, name, first_name, last_name FROM contacts WHERE category = '$from_contact' order by name");
                echo '<option value=""></option>';
                while($row = mysqli_fetch_array($query)) {
                    if(decryptIt($row['name']) != '') { ?>
                        <option <?php if (strpos($contact_value, $row['contactid']) !== FALSE) {
			            echo " selected"; } ?> value='<?php echo $row['contactid']; ?>'><?php echo decryptIt($row['name']); ?></option>
                    <?php } else { ?>
                        <option <?php if (strpos($contact_value, $row['contactid']) !== FALSE) {
			            echo " selected"; } ?> value='<?php echo $row['contactid']; ?>'><?php echo decryptIt($row['first_name']).' '.decryptIt($row['last_name']); ?></option>
                    <?php
                    }
                }
             } ?>
            </select>
        </div>
    </div>
<?php }
