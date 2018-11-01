<?php if($field_option == 'Client Activities Social Story') {
	include_once ('../Contacts/config_contact_ss.php');
	include_once ('../Contacts/config_contact_ss_functions.php');

	$value = $config_contact_ss['settings']['Choose Fields for Activities'];
	

    $inputs = get_all_inputs_contact_ss($value['data']);

    foreach($inputs as $input) {
        $$input = '';
    }

    if(!empty($_GET['edit'])) {
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM social_story_activities WHERE support_contact='".$_GET['edit']."' ORDER BY `activities_id` DESC"));

        foreach($inputs as $input) {
            $$input = $get_contact[$input];
        }
        $row_id = $get_contact['activities_id'];
        $support_contact = $_GET['edit'];
        $support_contact_category = FOLDER_URL;
    } ?>
	<script type="text/javascript">

	function selectContactCategorySS(sel) {
		var stage = sel.value;
		var typeId = sel.id;
		var arr = typeId.split('_');
		$.ajax({
			type: "GET",
			url: "../Social Story/isp_ajax_all.php?fill=contact_category&category="+stage,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$("#contact_"+arr[2]).html(response);
				$("#contact_"+arr[2]).trigger("change.select2");
			}
		});
	}
	</script>
	<?php
	$k=0;
	$get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
	$value_config = ','.$get_field_config[$value['config_field']].',';
	foreach($value['data'] as $tab_name => $tabs) {
		if(in_array_any(array_column($tabs,2),explode(',',$value_config))) {
			echo "<h3>".$tab_name."</h3>";
		}
		foreach($tabs as $field) {
			if (strpos($value_config, ','.$field[2].',') !== FALSE) {
				if($field[2] == 'support_contact') {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, $support_contact_category, 'disabled="true"');
				} else if($field[2] == 'support_contact_category') {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, '', 'disabled="true"');
				} else {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, '', '', 'social_story_activities', 'activities_id', $row_id, 'support_contact');
				}
			}
		}
		$k++;
	}
}
if($field_option == 'Client Communication Social Story') {
	include_once ('../Contacts/config_contact_ss.php');
	include_once ('../Contacts/config_contact_ss_functions.php');

	$value = $config_contact_ss['settings']['Choose Fields for Communication'];
	

    $inputs = get_all_inputs_contact_ss($value['data']);

    foreach($inputs as $input) {
        $$input = '';
    }

    if(!empty($_GET['edit'])) {
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM social_story_communication WHERE support_contact='".$_GET['edit']."' ORDER BY `communication_id` DESC"));

        foreach($inputs as $input) {
            $$input = $get_contact[$input];
        }
        $row_id = $get_contact['communication_id'];
        $support_contact = $_GET['edit'];
        $support_contact_category = FOLDER_URL;
    } ?>
	<script type="text/javascript">

	function selectContactCategorySS(sel) {
		var stage = sel.value;
		var typeId = sel.id;
		var arr = typeId.split('_');
		$.ajax({
			type: "GET",
			url: "../Social Story/isp_ajax_all.php?fill=contact_category&category="+stage,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$("#contact_"+arr[2]).html(response);
				$("#contact_"+arr[2]).trigger("change.select2");
			}
		});
	}
	</script>
	<?php
	$k=0;
	$get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
	$value_config = ','.$get_field_config[$value['config_field']].',';
	foreach($value['data'] as $tab_name => $tabs) {
		if(in_array_any(array_column($tabs,2),explode(',',$value_config))) {
			echo "<h3>".$tab_name."</h3>";
		}
		foreach($tabs as $field) {
			if (strpos($value_config, ','.$field[2].',') !== FALSE) {
				if($field[2] == 'support_contact') {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, $support_contact_category, 'disabled="true"');
				} else if($field[2] == 'support_contact_category') {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, '', 'disabled="true"');
				} else {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, '', '', 'social_story_communication', 'communication_id', $row_id, 'support_contact');
				}
			}
		}
		$k++;
	}
}
if($field_option == 'Client Protocols Social Story') {
	include_once ('../Contacts/config_contact_ss.php');
	include_once ('../Contacts/config_contact_ss_functions.php');

	$value = $config_contact_ss['settings']['Choose Fields for Protocols'];
	

    $inputs = get_all_inputs_contact_ss($value['data']);

    foreach($inputs as $input) {
        $$input = '';
    }

    if(!empty($_GET['edit'])) {
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM social_story_protocols WHERE support_contact='".$_GET['edit']."' ORDER BY `protocol_id` DESC"));

        foreach($inputs as $input) {
            $$input = $get_contact[$input];
        }
        $row_id = $get_contact['protocol_id'];
        $support_contact = $_GET['edit'];
        $support_contact_category = FOLDER_URL;
    } ?>
	<script type="text/javascript">

	function selectContactCategorySS(sel) {
		var stage = sel.value;
		var typeId = sel.id;
		var arr = typeId.split('_');
		$.ajax({
			type: "GET",
			url: "../Social Story/isp_ajax_all.php?fill=contact_category&category="+stage,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$("#contact_"+arr[2]).html(response);
				$("#contact_"+arr[2]).trigger("change.select2");
			}
		});
	}
	</script>
	<?php
	$k=0;
	$get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
	$value_config = ','.$get_field_config[$value['config_field']].',';
	foreach($value['data'] as $tab_name => $tabs) {
		if(in_array_any(array_column($tabs,2),explode(',',$value_config))) {
			echo "<h3>".$tab_name."</h3>";
		}
		foreach($tabs as $field) {
			if (strpos($value_config, ','.$field[2].',') !== FALSE) {
				if($field[2] == 'support_contact') {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, $support_contact_category, 'disabled="true"');
				} else if($field[2] == 'support_contact_category') {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, '', 'disabled="true"');
				} else {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, '', '', 'social_story_protocols', 'protocol_id', $row_id, 'support_contact');
				}
			}
		}
		$k++;
	}
}
if($field_option == 'Client Routines Social Story') {
	include_once ('../Contacts/config_contact_ss.php');
	include_once ('../Contacts/config_contact_ss_functions.php');

	$value = $config_contact_ss['settings']['Choose Fields for Routines'];

    $inputs = get_all_inputs_contact_ss($value['data']);

    foreach($inputs as $input) {
        $$input = '';
    }

    if(!empty($_GET['edit'])) {
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM social_story_routines WHERE support_contact='".$_GET['edit']."' ORDER BY `routine_id` DESC"));

        foreach($inputs as $input) {
            $$input = $get_contact[$input];
        }
        $row_id = $get_contact['routine_id'];
        $support_contact = $_GET['edit'];
        $support_contact_category = FOLDER_URL;
    } ?>
	<script type="text/javascript">

	function selectContactCategorySS(sel) {
		var stage = sel.value;
		var typeId = sel.id;
		var arr = typeId.split('_');
		$.ajax({
			type: "GET",
			url: "../Social Story/isp_ajax_all.php?fill=contact_category&category="+stage,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$("#contact_"+arr[2]).html(response);
				$("#contact_"+arr[2]).trigger("change.select2");
			}
		});
	}
	</script>
	<?php
	$k=0;
	$get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
	$value_config = ','.$get_field_config[$value['config_field']].',';
	foreach($value['data'] as $tab_name => $tabs) {
		if(in_array_any(array_column($tabs,2),explode(',',$value_config))) {
			echo "<h3>".$tab_name."</h3>";
		}
		foreach($tabs as $field) {
			if (strpos($value_config, ','.$field[2].',') !== FALSE) {
				if($field[2] == 'support_contact') {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, $support_contact_category, 'disabled="true"');
				} else if($field[2] == 'support_contact_category') {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, '', 'disabled="true"');
				} else {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, '', '', 'social_story_routines', 'routine_id', $row_id, 'support_contact');
				}
			}
		}
		$k++;
	}
}
if($field_option == 'Client Key Methodologies Social Story' || $field_option == 'Client Key Methodologies Social Story Member Support') {
	include_once ('../Contacts/config_contact_ss.php');
	include_once ('../Contacts/config_contact_ss_functions.php');

	$value = $config_contact_ss['settings']['Choose Fields for Key Methodologies'];

    $inputs = get_all_inputs_contact_ss($value['data']);

    foreach($inputs as $input) {
        $$input = '';
    }

    if(!empty($_GET['edit'])) {
        $get_contact = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM key_methodologies WHERE support_contact='".$_GET['edit']."' ORDER BY `keymethodologiesid` DESC"));

        foreach($inputs as $input) {
            $$input = $get_contact[$input];
        }
        $row_id = $get_contact['keymethodologiesid'];
        $support_contact = $_GET['edit'];
        $support_contact_category = FOLDER_URL;
    } ?>
	<script type="text/javascript">

	function selectContactCategorySS(sel) {
		var stage = sel.value;
		var typeId = sel.id;
		var arr = typeId.split('_');
		$.ajax({
			type: "GET",
			url: "../Social Story/isp_ajax_all.php?fill=contact_category&category="+stage,
			dataType: "html",   //expect html to be returned
			success: function(response){
				$("#contact_"+arr[2]).html(response);
				$("#contact_"+arr[2]).trigger("change.select2");
			}
		});
	}
	</script>
	<?php
	$k=0;
	$get_field_config = @mysqli_fetch_assoc(mysqli_query($dbc,"SELECT ".$value['config_field']." FROM field_config"));
	$value_config = ','.$get_field_config[$value['config_field']].',';
	foreach($value['data'] as $tab_name => $tabs) {
		if(in_array_any(array_column($tabs,2),explode(',',$value_config))) {
			echo "<h3>".$tab_name."</h3>";
		}
		foreach($tabs as $field) {
			if (strpos($value_config, ','.$field[2].',') !== FALSE) {
				if($field[2] == 'support_contact') {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, $support_contact_category, 'disabled="true"');
				} else if($field[2] == 'support_contact_category') {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, '', 'disabled="true"');
				} else {
					echo get_field_contact_ss($field, $$field[2], $dbc, $support_contact, '', '', 'key_methodologies', 'keymethodologiesid', $row_id, 'support_contact');
				}
			}
		}
		$k++;
	}
} ?>