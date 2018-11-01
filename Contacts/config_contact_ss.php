<?php
error_reporting(0);
global $config_contact_ss;

$config_contact_ss['tile_name'] = 'Social Story';

$config_contact_ss['tabs'] = array (
    'Key Methodologies' => 'key_methodologies.php',
    'Learning Techniques' => 'learning_techniques.php',
    'Protocols' => 'protocols.php',
    'Patterns' => 'patterns.php',
    'Routines' => 'routines.php',
    'Communication' => 'communication.php',
    'Activities' => 'activities.php'
);

/* Activities */
$config_contact_ss['settings']['Choose Fields for Activities']['config_field'] = 'activities';
$config_contact_ss['settings']['Choose Fields for Activities']['data'] = array(
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

$config_contact_ss['settings']['Choose Fields for Activities Dashboard']['config_field'] = 'activities_dashboard';
$config_contact_ss['settings']['Choose Fields for Activities Dashboard']['data'] = array(
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
$config_contact_ss['settings']['Choose Fields for Communication']['config_field'] = 'communication';
$config_contact_ss['settings']['Choose Fields for Communication']['data'] = array(
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

$config_contact_ss['settings']['Choose Fields for Communication Dashboard']['config_field'] = 'communication_dashboard';
$config_contact_ss['settings']['Choose Fields for Communication Dashboard']['data'] = array(
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
$config_contact_ss['settings']['Choose Fields for Routines']['config_field'] = 'routines';
$config_contact_ss['settings']['Choose Fields for Routines']['data'] = array(
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

$config_contact_ss['settings']['Choose Fields for Routines Dashboard']['config_field'] = 'routines_dashboard';
$config_contact_ss['settings']['Choose Fields for Routines Dashboard']['data'] = array(
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

$config_contact_ss['settings']['Choose Fields for Key Methodologies Dashboard']['config_field'] = 'key_methodologies_dashboard';
$config_contact_ss['settings']['Choose Fields for Key Methodologies Dashboard']['data'] = array(
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

$config_contact_ss['settings']['Choose Fields for Key Methodologies']['config_field'] = 'key_methodologies';
$config_contact_ss['settings']['Choose Fields for Key Methodologies']['data'] = array(
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
	'Behaviours' => array(
			array('Do you have any behaviours that we should be aware of?', 'textarea', 'behaviours'),
		),
	'Triggers' => array(
			array('What To Look For', 'textarea', 'trigger_look'),
			array('What To Note', 'textarea', 'trigger_note'),
		),
	'Strategies' => array(
			array('What Works', 'textarea', 'strategy_works'),
			array('What Doesnt', 'textarea', 'strategy_not'),
			array('Next Steps', 'textarea', 'strategy_step'),
		),
	'Toileting' => array(
			array('Do you require extra support going to the bathroom?', 'yes_default_no', 'toileting'),
			array('Washroom Support Information', 'textarea_hidden', 'toileting_info', 'toileting', 'Yes')
		),
	'Goals' => array(
			array('Display Goals', 'textarea', 'goal_display'),
			array('Recommend Future Goal', 'textarea', 'goal_future'),
		),
	'Status' => array(
			array('Status', 'dropdown', 'status')
		)
);


/* Protocols */
$config_contact_ss['settings']['Choose Fields for Protocols']['config_field'] = 'protocols';
$config_contact_ss['settings']['Choose Fields for Protocols']['data'] = array(
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

$config_contact_ss['settings']['Choose Fields for Protocols Dashboard']['config_field'] = 'protocols_dashboard';
$config_contact_ss['settings']['Choose Fields for Protocols Dashboard']['data'] = array(
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