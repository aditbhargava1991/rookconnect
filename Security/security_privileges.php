<script>
jQuery(document).ready(function($){
	$('.live-search-box').focus();
	$('.live-search-list tr').each(function(){
		var text = $(this).text() + ' ' + $(this).prevAll().andSelf().find('th').last().text();
		$(this).attr('data-search-term', text.toLowerCase());
	});

	$('.live-search-box').on('keyup', function(){
		var searchTerm = $(this).val().toLowerCase();

		$('.live-search-list tr').each(function(){
			if(searchTerm == '' && $(this).data('dashboard') == '' && !$(this).hasClass('dont-hide')) {
				$(this).show();
			} else if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
				$(this).show();
			} else if(!$(this).hasClass('dont-hide')) {
				$(this).hide();
			}
		});
	});
	$('.live-search-box').keyup();

	$('.iframe_open').click(function(){
			var tile = $(this).data('option');
			var level = $('#sub_category').val();
			var title = $(this).parents('tr').children(':first').text();
		   $('#iframe_instead_of_window').attr('src', 'privileges_history.php?tile_name='+tile+'&title='+title+'&level='+level);
		   $('.iframe_title').text('Security Privileges History');
		   $('.iframe_holder').show();
		   $('.hide_on_iframe').hide();
		   $('#iframe_instead_of_window').on('load', function() {
			   $(this).height($(this).get(0).contentWindow.document.body.scrollHeight);
		   });
	});

	$('.close_iframe').click(function(){
		$('.iframe_holder').hide();
		$('.hide_on_iframe').show();
	});

	$('iframe').load(function() {
		this.contentWindow.document.body.style.overflow = 'hidden';
		this.contentWindow.document.body.style.minHeight = '0';
		this.contentWindow.document.body.style.paddingBottom = '5em';
		this.style.height = (this.contentWindow.document.body.offsetHeight + 80) + 'px';
	});
});
$(document).on('change', 'select[name="sub_category"]', function() { changeLevel(this); });

function privilegesConfig(sel) {
	var type = sel.type;
	var name = sel.name;
	var tile_value = sel.value;
	var final_value = '*';
	var level = $("#level_url").val();
	var forwhat = '';
	if($("#"+name+"_hide").prop('checked') == false) {
		final_value += 'hide*';
		forwhat = 'Status';
	}
	if($("#"+name+"_detailed_dash").is(':checked')) {
		final_value += 'detailed_dash*';
		forwhat = 'Dashboard Access';
	}
	if($("#"+name+"_detailed_view").is(':checked')) {
		final_value += 'detailed_view*';
		forwhat = 'View Access';
	}
	if($("#"+name+"_detailed_add").is(':checked')) {
		final_value += 'detailed_add*';
		forwhat = 'Add Access';
	}
	if($("#"+name+"_detailed_edit").is(':checked')) {
		final_value += 'detailed_edit*';
		forwhat = 'Edit Access';
	}
	if($("#"+name+"_detailed_archive").is(':checked')) {
		final_value += 'detailed_archive*';
		forwhat = 'Archive Access';
	}
	if($("#"+name+"_view_use").is(":checked")) {
		final_value += 'view_use*';
	}
	if($("#"+name+"_view_use_add_edit_delete").is(":checked")) {
		final_value += 'view_use_add_edit_delete*';
		forwhat = 'Active Use';
	}
	if($("#"+name+"_configure").is(":checked")) {
		final_value += 'configure*';
		forwhat = 'Setting Permission';
	}
	if($("#"+name+"_approval").is(":checked")) {
		final_value += 'approvals*';
		forwhat = 'Approval Permission';
	}
	if($("#"+name+"_search").is(":checked")) {
		final_value += 'search*';
		forwhat = 'Full Search Permission';
	}
	if($("#"+name+"_strictview").is(":checked")) {
		final_value += 'strictview*';
		forwhat = 'Strict View Only';
	}

	var ischecked= $("#"+name+"_hide").is(':checked');
	if(!ischecked) {
	   var uncheck_staff = name;
	} else {
		var uncheck_staff = '';
	}
	if(ischecked) {
		var check_staff = name;
	} else {
		var check_staff = '';
	}

	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=privileges_config&name="+name+"&level="+level+"&value="+final_value+"&uncheck_staff="+uncheck_staff+"&check_staff="+check_staff,
		dataType: "html",   //expect html to be returned
		success: function(response){
			//alert(forwhat + " is changed successfully for " + name + ".");
		}
	});

	//CHANGE LOG
	var contactid = $('.contacterid').val();
	$.ajax({    //create an ajax request to load_page.php
		type: "GET",
		url: "../ajax_all.php?fill=privileges_config_log&name="+name+"&level="+level+"&value="+final_value+"&contactid="+contactid+"&uncheck_staff="+uncheck_staff+"&check_staff="+check_staff,
		dataType: "html",   //expect html to be returned
		success: function(response){
		}
	});
}
function changeLevel(sel) {
	var stage = sel.value;
	window.location = 'security.php?tab=privileges&level='+stage;
}
function go_to_dashboard(target) {
	window.location.href = '?tab=privileges&dashboard_id='+target;
}
</script>
<div class='iframe_holder' style='display:none;'>
	<img src='<?php echo WEBSITE_URL; ?>/img/icons/close.png' class='close_iframe' width="45px" style='position:relative; right: 10px; float:right;top:58px; cursor:pointer;'>
	<span class='iframe_title' style='color:white; font-weight:bold; position: relative;top:58px; left: 20px; font-size: 30px;'></span>
	<iframe id="iframe_instead_of_window" style='width: 100%; overflow: hidden;' height="200px; border:0;" src=""></iframe>
</div>
<div class="row hide_on_iframe">
	<input type='hidden' value='<?php echo $_SESSION['contactid']; ?>' class='contacterid'>

	<div class="col-md-12">
	<form id="form1" name="form1" method="post"	action="add_services.php" enctype="multipart/form-data" class="form-horizontal" role="form">

	<?php
	$sql=mysqli_query($dbc,"SELECT * FROM  security_level");
	$on_security = get_security_levels($dbc);
	$level_url = '';
	if(!empty($_GET['level'])) {
		$level_url = $_GET['level'];
	} else {
		$contacterid = $_SESSION['contactid'];
		$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '{$_SESSION['contactid']}'");
		while($row = mysqli_fetch_assoc($result)) {
			$role = $row['role'];
		}
		if(stripos(','.$role.',',',super,') !== false) {
			$level_url = 'admin';
		} else {
			$level_url = explode(',',trim($role,','))[0];
		}
	}

	$section_display = ','.get_config($dbc, 'tile_enable_section').',';

    $notes = mysqli_fetch_assoc(mysqli_query($dbc, "SELECT note FROM notes_setting WHERE subtab='security_set_security_previleges'"));
    $note = $notes['note'];

    if ( !empty($note) ) { ?>
        <div class="notice double-gap-bottom popover-examples">
            <div class="col-sm-1 notice-icon"><img src="../img/info.png" class="wiggle-me" width="25"></div>
            <div class="col-sm-11">
                <span class="notice-name">NOTE:</span>
                <?= $note; ?>
            </div>
            <div class="clearfix"></div>
        </div><?php
    } ?>

	<input type="hidden" id="level_url" name="level_url" value="<?php echo $level_url ?>" />
	<div class="form-group">
		<label for="travel_task" class="col-sm-4 control-label">Select the Security Level you wish to set tile access privileges to:</label>
		<div class="col-sm-8">
		<select id="sub_category" name="sub_category" class="chosen-select-deselect form-control" width="380">
			<option value=''></option>
			<?php foreach($on_security as $security_name => $value)  { ?>
				<option <?php echo ($value == $level_url ? 'selected' : '').' '.($value == 'super' ? 'disabled' : ''); ?> value="<?php echo $value; ?>"><?= $security_name ?></option>
			<?php } ?>
		</select>
	  </div>
	</div><div class='clearfix'></div>

	<div class="row live-search-list"><div class="col-sm-4" style="font-size: 2em;">
	<?php if(!empty($_GET['dashboard_id']) && $_GET['dashboard_id'] != 'all') {
		$dashboard = mysqli_fetch_array(mysqli_query($dbc, "SELECT td.`dashboard_id`, td.`tile_sort`, td.`name` FROM `tile_dashboards` td LEFT JOIN `contacts_tile_sort` cts ON cts.`default_dashboard`=td.`dashboard_id` WHERE `dashboard_id`='".$_GET['dashboard_id']."' OR `contactid`='".$_SESSION['contactid']."' ORDER BY `contactid` ASC"));
		$dashboard_list = explode('*#*',$dashboard['tile_sort']);
		echo "Dashboard Tiles: ".$dashboard['name'];
		$_GET['dashboard_id'] = $dashboard['dashboard_id'];
	} else {
		echo "<div style='display:none;'>";
		include_once('../tiles.php');
		echo "</div>";
		$dashboard_list = $user_tile_list;
	}
	echo "</div>";
	$dashboard_list[] = 'calendar_rook';

	echo "<div class='col-sm-4'><center><input type='text' name='x' class=' form-control live-search-box' placeholder='Search for a tile...' style='max-width:300px;'></center></div>";

	$dashboards = mysqli_query($dbc, "SELECT `dashboard_id`, `name` FROM `tile_dashboards` WHERE `deleted`=0");
	if(mysqli_num_rows($dashboards) > 0) {
		echo '<div class="col-sm-4" style="text-align: right;"><img src="'.WEBSITE_URL.'/img/icons/ROOK-Speedometer.png" style="cursor: pointer; height: 2em;" title="Dashboards" class="dashboard_menu" onclick="$(\'#dashboard_menu\').toggle();">';
		echo "<div class='col-sm-8' id='dashboard_menu' style='display: none; margin: 0 1em; text-align: center;'>";
		echo "<select class='form-control chosen-select-deselect' data-placeholder='Select a Dashboard' onchange='go_to_dashboard(this.value);'><option ".($_GET['dashboard_id'] == 'all' ? 'selected' : '')." value='all'></option><option value='all'>Show All Tiles</option>";
		while($db_row = mysqli_fetch_array($dashboards)) {
			echo '<option '.($db_row['dashboard_id'] == $_GET['dashboard_id'] ? 'selected' : '').' value="'.$db_row['dashboard_id'].'">'.$db_row['name'].'</option>';
		}
		echo "</select></div></div>";
	}
	echo "<div class='clearfix'></div><br />";

	$level = $level_url;
	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM security_privileges WHERE level='$level_url'"));
	$tile = $get_config['tile'];

	//$privileges = get_privileges($dbc, $tile);
	?>

	<table class='table table-bordered table-striped'>
        <thead>
            <tr class='hidden-sm hidden-xs dont-hide'>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="The only visible tiles accessible in this section are tiles activated in the Enable/Disable Tiles section."><img src="../img/info.png" width="20"></a>
                </span>
                Software Functionality</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Security levels marked here will have access to see the tile dashboard."><img src="../img/info-w.png" width="20"></a>
                </span>
                Dashboard Access</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Security levels marked here will have access to see the tile outlined."><img src="../img/info-w.png" width="20"></a>
                </span>
                View Access</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="This security setting allows those assigned the ability to add all functionality relevant to the particular tile."><img src="../img/info-w.png" width="20"></a>
                </span>
                Add Access</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="This security setting allows those assigned the ability to edit all functionality relevant to the particular tile."><img src="../img/info-w.png" width="20"></a>
                </span>
                Edit Access</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="This security setting allows those assigned the ability to delete all functionality relevant to the particular tile."><img src="../img/info-w.png" width="20"></a>
                </span>
                Archive Access</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="This security setting allows those assigned the ability to configure the view, settings, and structure relevant to the particular tile."><img src="../img/info-w.png" width="20"></a>
                </span>
                Settings Permission</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="This security setting allows those assigned the ability to search through all results, not just those assigned to them for the particular tile."><img src="../img/info-w.png" width="20"></a>
                </span>
                Full Search Permission</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="This security setting allows those assigned the ability to use approval functions within the particular tile."><img src="../img/info-w.png" width="20"></a>
                </span>
                Approvals Permission</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="This security setting forces those assigned to strictly have view only with no functionality at all (eg. for Customer logins)."><img src="../img/info-w.png" width="20"></a>
                </span>
                Strict View Only</th>
                <th>Sub Tab Permissions</th>
                <th>Dashboard Permissions</th>
                <th>Field Permissions</th>
                <th>History</th>
            </tr>
        </thead>
		<?php if(strpos($section_display,',software_settings,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Software Settings:</div></th></tr></thead>
			<?php
			//$sql=mysqli_query($dbc,"SELECT * FROM  tile_config");
            // $on_security = ','.mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`tile_name`) enabled FROM `tile_security` WHERE `user_enabled`=1"))['enabled'].',';
            $on_security_sql = mysqli_query($dbc, "SELECT `tile_name` FROM `tile_security` WHERE `user_enabled`=1");
            while ($row = mysqli_fetch_array($on_security_sql)) {
            	$on_security .= ','.$row['tile_name'].',';
            }

			//while ($fieldinfo=mysqli_fetch_field($sql))
			//{
			//	$field_name = $fieldinfo->name;
			//	$get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM tile_config WHERE $field_name LIKE '%turn_on%'"));
			//	if($get_config[$field_name]) {
			//		$on_security .= $field_name.',';
			//	}
			//}
			?>
			<?php if(strpos($on_security, ',archiveddata,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('archiveddata', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Archived Data</td>
				<?php echo security_tile_config_function('archiveddata', get_privileges($dbc, 'archiveddata',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',customer_support,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('customer_support', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Customer Support</td>
				<?php echo security_tile_config_function('customer_support', get_privileges($dbc, 'customer_support',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',ffmsupport,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('ffmsupport', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">FFM Support</td>
				<?php echo security_tile_config_function('ffmsupport', get_privileges($dbc, 'ffmsupport',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',passwords,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('passwords', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Passwords</td>
				<?php echo security_tile_config_function('passwords', get_privileges($dbc, 'passwords',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',profile,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('profile', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Profile</td>
				<?php echo security_tile_config_function('profile', get_privileges($dbc, 'profile',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',security,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('security', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Security</td>
				<?php echo security_tile_config_function('security', get_privileges($dbc, 'security',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',software_config,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('software_config', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Settings</td>
				<?php echo security_tile_config_function('software_config', get_privileges($dbc, 'software_config',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>
		<?php if(strpos($section_display,',human_resources,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Human Resources:</div></th></tr></thead>

			<?php if (strpos($on_security, ',certificate,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('certificate', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Certificates</td>
				<?php echo security_tile_config_function('certificate', get_privileges($dbc, 'certificate',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',emp_handbook,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('emp_handbook', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Employee Handbook</td>
				<?php echo security_tile_config_function('emp_handbook', get_privileges($dbc, 'emp_handbook',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',employee_handbook,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('employee_handbook', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Employee Handbook</td>
				<?php echo security_tile_config_function('employee_handbook', get_privileges($dbc, 'employee_handbook',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',gao,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('gao', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Goals & Objectives</td>
				<?php echo security_tile_config_function('gao', get_privileges($dbc, 'gao',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',how_to_checklist,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('how_to_checklist', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">How to Checklist</td>
				<?php echo security_tile_config_function('how_to_checklist', get_privileges($dbc, 'how_to_checklist',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',how_to_guide,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('how_to_guide', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">All Software Guide</td>
				<?php echo security_tile_config_function('how_to_guide', get_privileges($dbc, 'how_to_guide',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',hr,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('hr', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">HR</td>
				<?php echo security_tile_config_function('hr', get_privileges($dbc, 'hr',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',manual,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('manual', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Manuals</td>
				<?php echo security_tile_config_function('manual', get_privileges($dbc, 'manual',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',ops_manual,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('ops_manual', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Operations Manual</td>
				<?php echo security_tile_config_function('ops_manual', get_privileges($dbc, 'ops_manual',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',orientation,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('orientation', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Orientation</td>
				<?php echo security_tile_config_function('orientation', get_privileges($dbc, 'orientation',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>

			<?php if(strpos($on_security, ',preformance_review,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('preformance_review', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Performance Reviews</td>
				<?php echo security_tile_config_function('preformance_review', get_privileges($dbc, 'preformance_review',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>

			<?php if(strpos($on_security, ',policies,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('policies', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Policies</td>
				<?php echo security_tile_config_function('policies', get_privileges($dbc, 'policies',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',policy_procedure,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('policy_procedure', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Policies & Procedures</td>
				<?php echo security_tile_config_function('policy_procedure', get_privileges($dbc, 'policy_procedure',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>

			<?php if(strpos($on_security, ',safety_manual,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('safety_manual', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Safety Manual</td>
				<?php echo security_tile_config_function('safety_manual', get_privileges($dbc, 'safety_manual',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>

			<?php if(strpos($on_security, ',software_guide,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('software_guide', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Software Guide</td>
				<?php echo security_tile_config_function('software_guide', get_privileges($dbc, 'software_guide',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>

			<?php if(strpos($on_security, ',staff,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('staff', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Staff</td>
				<?php echo security_tile_config_function_detailed('staff', get_privileges($dbc, 'staff',$level), 1, $level_url, 0, 1, 1, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',training_quiz,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('training_quiz', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Training & Quizzes</td>
				<?php echo security_tile_config_function('training_quiz', get_privileges($dbc, 'training_quiz',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>

		<?php endif; ?>

		<?php if(strpos($section_display,',profiles,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Profiles:</div></th></tr></thead>

			<?php if(strpos($on_security, ',client_info,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('client_info', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Client Information</td>
				<?php echo security_tile_config_function('client_info', get_privileges($dbc, 'client_info',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',contacts,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('contacts', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Contacts</td>
				<?php echo security_tile_config_function('contacts', get_privileges($dbc, 'contacts',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',contacts_inbox,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('contacts_inbox', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Contacts (Updated)</td>
				<?php echo security_tile_config_function('contacts_inbox', get_privileges($dbc, 'contacts_inbox',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',contacts3,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('contacts3', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Contacts 3</td>
				<?php echo security_tile_config_function('contacts3', get_privileges($dbc, 'contacts3',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>

			<?php if(strpos($on_security, ',contacts_rolodex,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('contacts_rolodex', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Contacts Rolodex</td>
				<?php echo security_tile_config_function('contacts_rolodex', get_privileges($dbc, 'contacts_rolodex',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>

			<?php if(strpos($on_security, ',fund_development,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('fund_development', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Fund Development</td>
				<?php echo security_tile_config_function('fund_development', get_privileges($dbc, 'fund_development',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',members,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('members', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Members</td>
				<?php echo security_tile_config_function('members', get_privileges($dbc, 'members',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',therapist,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('therapist', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">PT Day Sheet</td>
				<?php echo security_tile_config_function('therapist', get_privileges($dbc, 'therapist',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>

		<?php if(strpos($section_display,',accounting,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Accounting:</div></th></tr></thead>

			<?php if(strpos($on_security, ',accounts_receivables,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('accounts_receivables', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Accounts Receivable</td>
				<?php echo security_tile_config_function('accounts_receivables', get_privileges($dbc, 'accounts_receivables',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',budget,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('budget', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Budget</td>
				<?php echo security_tile_config_function('budget', get_privileges($dbc, 'budget',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',expense,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('expense', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Expenses</td>
				<?php echo security_tile_config_function('expense', get_privileges($dbc, 'expense',$level), 1, $level_url, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',goals_compensation,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('goals_compensation', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Compensation</td>
				<?php echo security_tile_config_function('goals_compensation', get_privileges($dbc, 'goals_compensation',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',payables,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('payables', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Payables</td>
				<?php echo security_tile_config_function('payables', get_privileges($dbc, 'payables',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',payroll,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('payroll', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Payroll</td>
				<?php echo security_tile_config_function('payroll', get_privileges($dbc, 'payroll',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',profit_loss,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('profit_loss', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Profit / Loss</td>
				<?php echo security_tile_config_function('profit_loss', get_privileges($dbc, 'profit_loss',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',billing,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('billing', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Project Billing & Invoices</td>
				<?php echo security_tile_config_function('billing', get_privileges($dbc, 'billing',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',purchase_order,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('purchase_order', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Purchase Order</td>
				<?php echo security_tile_config_function('purchase_order', get_privileges($dbc, 'purchase_order',$level), 1, $level_url, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',report,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('report', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Reports</td>
				<?php echo security_tile_config_function('report', get_privileges($dbc, 'report',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',vpl,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('vpl', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Vendor Price List</td>
				<?php echo security_tile_config_function('vpl', get_privileges($dbc, 'vpl',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>
		<?php if(strpos($section_display,',time_tracking,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Time Tracking:</div></th></tr></thead>
			<?php if(strpos($on_security, ',daysheet,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('daysheet', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Planner</td>
				<?php echo security_tile_config_function('daysheet', get_privileges($dbc, 'daysheet',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',sign_in_time,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('sign_in_time', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Sign In</td>
				<?php echo security_tile_config_function('sign_in_time', get_privileges($dbc, 'sign_in_time',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',punch_card,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('punch_card', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Time Clock</td>
				<?php echo security_tile_config_function('punch_card', get_privileges($dbc, 'punch_card',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',start_day_button,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('start_day_button', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment"><?= START_DAY ?></td>
				<?php echo security_tile_config_function('start_day_button', get_privileges($dbc, 'start_day_button',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',timesheet,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('timesheet', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Time Sheets</td>
				<?php echo security_tile_config_function('timesheet', get_privileges($dbc, 'timesheet',$level), 1, $level_url, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',time_tracking,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('time_tracking', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Time Tracking</td>
				<?php echo security_tile_config_function('time_tracking', get_privileges($dbc, 'time_tracking',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>

		<?php if(strpos($section_display,',inventory_management,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Inventory Management:</div></th></tr></thead>

			<?php if(strpos($on_security, ',assets,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('assets', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Assets</td>
				<?php echo security_tile_config_function('assets', get_privileges($dbc, 'assets',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',inventory,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('inventory', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Inventory</td>
				<?php echo security_tile_config_function('inventory', get_privileges($dbc, 'inventory',$level), 1, $level_url, 0, 0, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',material,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('material', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Material</td>
				<?php echo security_tile_config_function('material', get_privileges($dbc, 'material',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',vendors,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('vendors', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment"><?= VENDOR_TILE ?></td>
				<?php echo security_tile_config_function('vendors', get_privileges($dbc, 'vendors',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>

		<?php if(strpos($section_display,',equipment,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Equipment:</div></th></tr></thead>
			<?php if(strpos($on_security, ',equipment,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('equipment', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Equipment</td>
				<?php echo security_tile_config_function('equipment', get_privileges($dbc, 'equipment',$level), 1, $level_url, 1, 1); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>
		<?php if(strpos($section_display,',collaborative_workflow,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Collaborative Workflow:</div></th></tr></thead>

			<?php if (strpos($on_security, ',agenda_meeting,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('agenda_meeting', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Agenda & Meetings</td>
				<?php echo security_tile_config_function('agenda_meeting', get_privileges($dbc, 'agenda_meeting',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',appointment_calendar,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('appointment_calendar', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Appointment Calendar</td>
				<?php echo security_tile_config_function('appointment_calendar', get_privileges($dbc, 'appointment_calendar',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',booking,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('booking', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Booking</td>
				<?php echo security_tile_config_function('booking', get_privileges($dbc, 'booking',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',calendar_rook,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('calendar_rook', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Calendar</td>
				<?php echo security_tile_config_function('calendar_rook', get_privileges($dbc, 'calendar_rook',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if (strpos($on_security, ',checklist,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('checklist', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Checklist</td>
				<?php echo security_tile_config_function('checklist', get_privileges($dbc, 'checklist',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',interactive_calendar,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('interactive_calendar', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Interactive Calendar">Interactive Calendar</td>
				<?php echo security_tile_config_function('interactive_calendar', get_privileges($dbc, 'interactive_calendar',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',newsboard,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('newsboard', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">News Board</td>
				<?php echo security_tile_config_function('newsboard', get_privileges($dbc, 'newsboard',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',tasks,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('tasks', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment"><?= TASK_TILE ?></td>
				<?php echo security_tile_config_function('tasks', get_privileges($dbc, 'tasks',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>

            <!--
			<?php if(strpos($on_security, ',tasks_updated,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('tasks_updated', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Tasks (Updated)</td>
				<?php echo security_tile_config_function('tasks_updated', get_privileges($dbc, 'tasks_updated',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
            -->

			<?php if(strpos($on_security, ',optimize,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('optimize', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Trip Optimizer</td>
				<?php echo security_tile_config_function('optimize', get_privileges($dbc, 'optimize',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>
		<?php if(strpos($section_display,',digital_forms,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Digital Forms:</div></th></tr></thead>
			<?php if(strpos($on_security, ',client_documentation,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('client_documentation', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Client Documentation</td>
				<?php echo security_tile_config_function('client_documentation', get_privileges($dbc, 'client_documentation',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if (strpos($on_security, ',contracts,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('contracts', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Contracts</td>
				<?php echo security_tile_config_function('contracts', get_privileges($dbc, 'contracts',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',daily_log_notes,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('daily_log_notes', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Daily Log Notes</td>
				<?php echo security_tile_config_function('daily_log_notes', get_privileges($dbc, 'daily_log_notes',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',day_program,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('day_program', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Day Program</td>
				<?php echo security_tile_config_function('day_program', get_privileges($dbc, 'day_program',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',documents_all,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('documents_all', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Documents</td>
				<?php echo security_tile_config_function('documents_all', get_privileges($dbc, 'documents_all',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php $documents_all_tiles = explode(',',get_config($dbc, 'documents_all_tiles'));
			foreach($documents_all_tiles as $documents_all_tile) {
				if(strpos($on_security, ',documents_all,') !== FALSE && $documents_all_tile != '') { ?>
					<tr data-dashboard='<?= (in_array('documents_all#*#'.$documents_all_tile, $dashboard_list) ? 'current' : '') ?>'>
						<td data-title="Comment">Documents: <?= $documents_all_tile ?></td>
						<?php echo security_tile_config_function('documents_all_'.config_safe_str($documents_all_tile), get_privileges($dbc, 'documents_all_'.config_safe_str($documents_all_tile), $level), 0, $level_url); ?>
					</tr>
				<?php }
			} ?>
			<?php if(strpos($on_security, ',exercise_library,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('exercise_library', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Exercise Library</td>
				<?php echo security_tile_config_function('exercise_library', get_privileges($dbc, 'exercise_library',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',form_builder,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('form_builder', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Form Builder</td>
				<?php echo security_tile_config_function('form_builder', get_privileges($dbc, 'form_builder',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',individual_support_plan,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('individual_support_plan', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Individual Service Plan (ISP)</td>
				<?php echo security_tile_config_function('individual_support_plan', get_privileges($dbc, 'individual_support_plan',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',charts,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('charts', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Charts</td>
				<?php echo security_tile_config_function('charts', get_privileges($dbc, 'charts',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',medication,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('medication', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Medication</td>
				<?php echo security_tile_config_function('medication', get_privileges($dbc, 'medication',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',routine,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('routine', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Routine Creator</td>
				<?php echo security_tile_config_function('routine', get_privileges($dbc, 'routine',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',social_story,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('social_story', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Social Story</td>
				<?php echo security_tile_config_function('social_story', get_privileges($dbc, 'social_story',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',treatment_charts,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('treatment_charts', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Treatment Charts</td>
				<?php echo security_tile_config_function('treatment_charts', get_privileges($dbc, 'treatment_charts',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>
		<?php if(strpos($section_display,',estimates,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'><?= ESTIMATE_TILE ?> / Quoting:</div></th></tr></thead>

			<?php if(strpos($on_security, ',cost_estimate,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('cost_estimate', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Cost Estimates</td>
				<?php echo security_tile_config_function('cost_estimate', get_privileges($dbc, 'cost_estimate',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',estimate,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('estimate', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment"><?= ESTIMATE_TILE ?></td>
				<?php echo security_tile_config_function('estimate', get_privileges($dbc, 'estimate',$level), 0, $level_url, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',field_ticket_estimates,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('field_ticket_estimates', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Field Ticket Estimates</td>
				<?php echo security_tile_config_function('field_ticket_estimates', get_privileges($dbc, 'field_ticket_estimates',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',quote,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('quote', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Quotes</td>
				<?php echo security_tile_config_function('quote', get_privileges($dbc, 'quote',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>
		<?php if(strpos($section_display,',sales,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Sales:</div></th></tr></thead>

			<?php if(strpos($on_security, ',calllog,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('calllog', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Cold Call</td>
				<?php echo security_tile_config_function('calllog', get_privileges($dbc, 'calllog',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',drop_off_analysis,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('drop_off_analysis', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Drop Off Analysis</td>
				<?php echo security_tile_config_function('drop_off_analysis', get_privileges($dbc, 'drop_off_analysis',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',infogathering,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('infogathering', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Information Gathering</td>
				<?php echo security_tile_config_function('infogathering', get_privileges($dbc, 'infogathering',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',intake,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('intake', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Intake Form</td>
				<?php echo security_tile_config_function('intake', get_privileges($dbc, 'intake',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if (strpos($on_security, ',sales,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('sales', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Sales</td>
				<?php echo security_tile_config_function('sales', get_privileges($dbc, 'sales',$level), 1, $level_url, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',sales_order,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('sales_order', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment"><?= SALES_ORDER_NOUN ?></td>
				<?php echo security_tile_config_function('sales_order', get_privileges($dbc, 'sales_order',$level), 1, $level_url, 0, 1); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>

		<?php if(strpos($section_display,',project_management,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'><?= PROJECT_TILE ?> Management:</div></th></tr></thead>

			<?php if(strpos($on_security, ',addendum,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('addendum', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Addendum</td>
				<?php echo security_tile_config_function('addendum', get_privileges($dbc, 'addendum',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',addition,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('addition', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Addition</td>
				<?php echo security_tile_config_function('addition', get_privileges($dbc, 'addition',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',assembly,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('assembly', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Assembly</td>
				<?php echo security_tile_config_function('assembly', get_privileges($dbc, 'assembly',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',business_development,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('business_development', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Business Development</td>
				<?php echo security_tile_config_function('business_development', get_privileges($dbc, 'business_development',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',client_projects,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('client_projects', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Client Projects</td>
				<?php echo security_tile_config_function('client_projects', get_privileges($dbc, 'client_projects',$level), 1, $level_url, 1, 1); ?>
			</tr>
			<?php } ?>
			<?php if (strpos($on_security, ',communication_schedule,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('communication_schedule', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Communication</td>
				<?php echo security_tile_config_function('communication_schedule', get_privileges($dbc, 'communication_schedule',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if (strpos($on_security, ',communication,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('communication', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Communication Tasks</td>
				<?php echo security_tile_config_function('communication', get_privileges($dbc, 'communication',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if (strpos($on_security, ',dispatch,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('dispatch', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Dispatch">Dispatch</td>
				<?php echo security_tile_config_function('dispatch', get_privileges($dbc, 'dispatch',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if (strpos($on_security, ',email_communication,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('email_communication', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Email Communication</td>
				<?php echo security_tile_config_function('email_communication', get_privileges($dbc, 'email_communication',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',field_job,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('field_job', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Field Jobs</td>
				<?php echo security_tile_config_function('field_job', get_privileges($dbc, 'field_job',$level), 1, $level_url, 1, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',gantt_chart,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('gantt_chart', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Gantt Chart</td>
				<?php echo security_tile_config_function('gantt_chart', get_privileges($dbc, 'gantt_chart',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',injury,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('injury', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Injury">Injury</td>
				<?php echo security_tile_config_function('injury', get_privileges($dbc, 'injury',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',internal,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('internal', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Internal</td>
				<?php echo security_tile_config_function('internal', get_privileges($dbc, 'internal',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php //if(strpos($on_security, ',jobs,') !== FALSE) { ?>
			<!--
            <tr data-dashboard='<?//= (in_array('jobs', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Jobs</td>
				<?php //echo security_tile_config_function('jobs', get_privileges($dbc, 'jobs',$level), 1, $level_url, 1, 1); ?>
			</tr>
            -->
			<?php //} ?>
			<?php if(strpos($on_security, ',manufacturing,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('manufacturing', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Manufacturing</td>
				<?php echo security_tile_config_function('manufacturing', get_privileges($dbc, 'manufacturing',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',marketing,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('marketing', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Marketing</td>
				<?php echo security_tile_config_function('marketing', get_privileges($dbc, 'marketing',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if (strpos($on_security, ',phone_communication,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('phone_communication', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Phone Communication</td>
				<?php echo security_tile_config_function('phone_communication', get_privileges($dbc, 'phone_communication',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',process_development,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('process_development', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Process Development</td>
				<?php echo security_tile_config_function('process_development', get_privileges($dbc, 'process_development',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',products,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('products', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Products</td>
				<?php echo security_tile_config_function('products', get_privileges($dbc, 'products',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',project,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('project', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment"><?= PROJECT_TILE ?></td>
				<?php echo security_tile_config_function('project', get_privileges($dbc, 'project',$level), 1, $level_url, 0, 1, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php $project_types = explode(',', get_config($dbc, 'project_tabs'));
			foreach($project_types as $project_type) {
				if(strpos($on_security, ',project,') !== FALSE) { ?>
					<tr data-dashboard='<?= (in_array('project#*#'.$project_type, $dashboard_list) ? 'current' : '') ?>'>
						<td data-title="Comment"><?= PROJECT_NOUN.': '.$project_type ?></td>
						<?php echo security_tile_config_function('project_type_'.config_safe_str($project_type), get_privileges($dbc, 'project_type_'.config_safe_str($project_type), $level), 0, $level_url, 0, 1); ?>
					</tr>
				<?php }
			} ?>
			<?php if(strpos($on_security, ',project_workflow,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('project_workflow', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Project Workflow</td>
				<?php echo security_tile_config_function('project_workflow', get_privileges($dbc, 'project_workflow',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',properties,') !== FALSE) { ?>
			<tr>
				<td data-title="Properties">Properties</td>
				<?php echo security_tile_config_function('properties', get_privileges($dbc, 'properties',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',rd,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('archiveddata', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">R&D</td>
				<?php echo security_tile_config_function('rd', get_privileges($dbc, 'rd',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',scrum,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('scrum', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Scrum</td>
				<?php echo security_tile_config_function('scrum', get_privileges($dbc, 'scrum',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',services,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('services', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Services</td>
				<?php echo security_tile_config_function('services', get_privileges($dbc, 'services',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',shop_work_orders,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('shop_work_orders', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Shop Work Orders</td>
				<?php echo security_tile_config_function('shop_work_orders', get_privileges($dbc, 'shop_work_orders',$level), 1, $level_url, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',site_work_orders,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('site_work_orders', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Site Work Orders</td>
				<?php echo security_tile_config_function('site_work_orders', get_privileges($dbc, 'site_work_orders',$level), 1, $level_url, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',sred,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('sred', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">SR&ED</td>
				<?php echo security_tile_config_function('sred', get_privileges($dbc, 'sred',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',ticket,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('ticket', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment"><?= TICKET_TILE ?></td>
				<?php echo security_tile_config_function('ticket', get_privileges($dbc, 'ticket',$level), 1, $level_url, 1, 1, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php $ticket_type_tile_list = get_config($dbc, 'ticket_type_tiles');
            if($ticket_type_tile_list == 'SHOW') {
                $ticket_type_tiles = array_filter(explode(',', get_config($dbc, 'ticket_tabs')));
            } else {
                $ticket_type_tiles = [];
                foreach(explode(',', get_config($dbc, 'ticket_tabs')) as $ticket_type) {
                    if(in_array(config_safe_str($ticket_type),explode(',',$ticket_type_tile_list))) {
                        $ticket_type_tiles[] = $ticket_type;
                    }
                }
            }
			foreach($ticket_type_tiles as $ticket_type) {
				if(strpos($on_security, ',ticket,') !== FALSE) { ?>
					<tr data-dashboard='<?= (in_array('ticket#*#'.$ticket_type, $dashboard_list) ? 'current' : '') ?>'>
						<td data-title="Comment"><?= TICKET_TILE.': '.$ticket_type ?></td>
						<?php echo security_tile_config_function('ticket_type_'.config_safe_str($ticket_type), get_privileges($dbc, 'ticket_type_'.config_safe_str($ticket_type), $level), 0, $level_url, 1); ?>
					</tr>
				<?php }
			} ?>
            <?php $ticket_group_tiles = $dbc->query("SELECT `value` FROM `general_configuration` WHERE `name` LIKE 'ticket_split_tiles_%'");
            while($ticket_group_tile = $ticket_group_tiles->fetch_assoc()['value']) {
                $ticket_group_tile = explode('#*#',$ticket_group_tile); ?>
                <tr data-dashboard='<?= (in_array('ticket#*#'.$ticket_type, $dashboard_list) ? 'current' : '') ?>'>
                    <td data-title="Comment"><?= TICKET_NOUN.' Group: '.$ticket_group_tile[0] ?></td>
                    <?php echo security_tile_config_function('ticket_tile_'.config_safe_str($ticket_group_tile[0]), get_privileges($dbc, 'ticket_tile_'.config_safe_str($ticket_group_tile[0]), $level), 0, $level_url, 1); ?>
                </tr>
            <?php } ?>
		<?php endif; ?>
		<?php if(strpos($section_display,',safety,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Safety:</div></th></tr></thead>

			<?php if(strpos($on_security, ',driving_log,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('driving_log', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Driving Log</td>
				<?php echo security_tile_config_function('driving_log', get_privileges($dbc, 'driving_log',$level), 0, $level_url, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',incident_report,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('incident_report', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment"><?= INC_REP_TILE ?></td>
				<?php echo security_tile_config_function('incident_report', get_privileges($dbc, 'incident_report',$level), 0, $level_url, 1, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',match,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('match', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Match</td>
				<?php echo security_tile_config_function('match', get_privileges($dbc, 'match',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',safety,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('safety', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Safety</td>
				<?php echo security_tile_config_function('safety', get_privileges($dbc, 'safety',$level), 1, $level_url, 1, 0); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>

		<?php if(strpos($section_display,',point_of_sale,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'><?= POS_ADVANCE_TILE ?>:</div></th></tr></thead>

			<?php if(strpos($on_security, ',check_in,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('check_in', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Check In</td>
				<?php echo security_tile_config_function('check_in', get_privileges($dbc, 'check_in',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',check_out,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('check_out', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Check Out</td>
				<?php echo security_tile_config_function('check_out', get_privileges($dbc, 'check_out',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',custom,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('custom', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Custom</td>
				<?php echo security_tile_config_function('custom', get_privileges($dbc, 'custom',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',invoicing,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('invoicing', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Invoicing</td>
				<?php echo security_tile_config_function('invoicing', get_privileges($dbc, 'invoicing',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',labour,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('labour', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Labour</td>
				<?php echo security_tile_config_function('labour', get_privileges($dbc, 'labour',$level), 0, $level_url, 0, 0, 1); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',package,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('package', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Packages</td>
				<?php echo security_tile_config_function('package', get_privileges($dbc, 'package',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',posadvanced,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('posadvanced', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment"><?= POS_ADVANCE_TILE ?></td>
				<?php echo security_tile_config_function('posadvanced', get_privileges($dbc, 'posadvanced',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',promotion,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('promotion', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Promotions & Coupons</td>
				<?php echo security_tile_config_function('promotion', get_privileges($dbc, 'promotion',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',rate_card,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('rate_card', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Rate Cards</td>
				<?php echo security_tile_config_function('rate_card', get_privileges($dbc, 'rate_card',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',service_queue,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('service_queue', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Service Queue</td>
				<?php echo security_tile_config_function('service_queue', get_privileges($dbc, 'service_queue',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>

		<?php if(strpos($section_display,',crm,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Customer Relationship Management:</div></th></tr></thead>

			<?php if(strpos($on_security, ',confirmation,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('confirmation', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Notifications</td>
				<?php echo security_tile_config_function('confirmation', get_privileges($dbc, 'confirmation',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',confirm,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('confirm', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Confirmation</td>
				<?php echo security_tile_config_function('confirm', get_privileges($dbc, 'confirm',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',crm,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('crm', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">CRM</td>
				<?php echo security_tile_config_function('crm', get_privileges($dbc, 'crm',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',reactivation,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('reactivation', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Follow Up</td>
				<?php echo security_tile_config_function('reactivation', get_privileges($dbc, 'reactivation',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',helpdesk,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('helpdesk', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Help Desk</td>
				<?php echo security_tile_config_function('helpdesk', get_privileges($dbc, 'helpdesk',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',website,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('website', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Website</td>
				<?php echo security_tile_config_function('website', get_privileges($dbc, 'website',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
			<?php if(strpos($on_security, ',treatment,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('treatment', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Treatment</td>
				<?php echo security_tile_config_function('treatment', get_privileges($dbc, 'treatment',$level), 0, $level_url); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>

        <!-- Analytics -->
		<?php if(strpos($section_display,',analytics,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Analytics:</div></th></tr></thead>

			<?php if(strpos($on_security, ',report,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('report', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Reports</td>
				<?php echo security_tile_config_function('report', get_privileges($dbc, 'report',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>

        <!-- Communication -->
		<?php if(strpos($section_display,',communication,') !== FALSE): ?>
			<thead><tr><th colspan='14'><div style='text-align:left;width:100%;font-size:20px;'>Communication:</div></th></tr></thead>

			<?php if(strpos($on_security, ',non_verbal_communication,') !== FALSE) { ?>
			<tr data-dashboard='<?= (in_array('non_verbal_communication', $dashboard_list) ? 'current' : '') ?>'>
				<td data-title="Comment">Emoji Comm</td>
				<?php echo security_tile_config_function('non_verbal_communication', get_privileges($dbc, 'non_verbal_communication',$level), 1, $level_url); ?>
			</tr>
			<?php } ?>
		<?php endif; ?>
	</table>

	<?php
	function security_tile_config_function($field, $value, $subtab, $level_url, $full_search = 0, $approvals = 0, $dashboard = 0, $strict_view) { ?>
		<td data-title="Dashboard & View Access" colspan="2"><input type="checkbox" <?php if (strpos($value, 'hide*') == FALSE) {
			echo " checked"; } ?> onclick='privilegesConfig(this);' value="hide" style="height: 20px; width: 20px;" id="<?php echo $field;?>_hide" name="<?php echo $field;?>">
		</td>
		<td data-title="Add, Edit, & Archive Access" colspan="3"><input type="checkbox" <?php if (strpos($value, '*view_use_add_edit_delete*') !== FALSE) {
			echo " checked"; } ?> onclick='privilegesConfig(this);' value="view_use_add_edit_delete" style="height: 20px; width: 20px;" id="<?php echo $field;?>_view_use_add_edit_delete" name="<?php echo $field;?>">
		</td>
		<td data-title="Settings Permission"><input type="checkbox" <?php if (strpos($value, '*configure*') !== FALSE) {
			echo " checked"; } ?> onclick='privilegesConfig(this);' value="configure" style="height: 20px; width: 20px;" id="<?php echo $field;?>_configure" name="<?php echo $field;?>">
		</td>
		<td data-title="Full Search Permission">
		<?php if ( $full_search == 1 ) { ?><input type="checkbox" <?php if (strpos($value, '*search*') !== FALSE) {
				echo " checked"; } ?> onclick='privilegesConfig(this);' value="search" style="height: 20px; width: 20px;" id="<?php echo $field;?>_search" name="<?php echo $field;?>">
		<?php } ?>
		</td>
		<td data-title="Approvals Permission">
		<?php if ( $approvals == 1 ) { ?><input type="checkbox" <?php if (strpos($value, '*approvals*') !== FALSE) {
				echo " checked"; } ?> onclick='privilegesConfig(this);' value="approvals" style="height: 20px; width: 20px;" id="<?php echo $field;?>_approval" name="<?php echo $field;?>">
		<?php } ?>
		</td>
		<td data-title="Strict View Only">
		<?php if ( $strict_view == 1 ) { ?><input type="checkbox" <?php if (strpos($value, '*strictview*') !== FALSE) {
				echo " checked"; } ?> onclick='privilegesConfig(this);' value="strictview" style="height: 20px; width: 20px;" id="<?php echo $field;?>_strictview" name="<?php echo $field;?>">
		<?php } ?>
		</td>
		<?php if ( $subtab == 1 ) { ?>
			<td data-title="Subtab Permissions" align="center">
				<a class="" href="software_config_subtabs.php?tile=<?= $field; ?>&level=<?= $level_url; ?>"><img class="settings-classic wiggle-me" src="../img/icons/settings-4.png" title="Sub Tab Settings" style="width:30px;"></a>
			</td><?php
		} else { ?>
			<td>&nbsp;</td><?php
		} ?>
		<td>&nbsp;</td>
		<?php if ( $dashboard == 1 ) { ?>
			<td data-title="Dashboard Permissions" align="center">
				<a class="" href="software_config_dashboard.php?tile=<?= $field; ?>&level=<?= $level_url; ?>"><img class="settings-classic wiggle-me" src="../img/icons/settings-4.png" title="Sub Tab Settings" style="width:30px;"></a>
			</td><?php
		} else { ?>
			<td>&nbsp;</td><?php
		} ?>
		<td data-title="History" align="center"><a><span data-option="<?php echo $field; ?>" class="iframe_open">View All</span></a></td><?php
	}

	function security_tile_config_function_detailed($field, $value, $subtab, $level_url, $full_search = 0, $approvals = 0, $dashboard = 0, $strict_view = 0, $field_permissions = 0) { ?>
		<input type="checkbox" value="hide" style="display:none;" id="<?php echo $field;?>_hide" name="<?php echo $field;?>">
		</td>
		<td data-title="Dashboard Access"><input type="checkbox" <?php if (strpos($value, 'detailed_dash*') !== FALSE) {
			echo " checked"; } ?> onclick='privilegesConfig(this);' value="detailed_dash" style="height: 20px; width: 20px;" id="<?php echo $field;?>_detailed_dash" name="<?php echo $field;?>">
		</td>
		<td data-title="View Access"><input type="checkbox" <?php if (strpos($value, 'detailed_view*') !== FALSE) {
			echo " checked"; } ?> onclick='privilegesConfig(this);' value="detailed_view" style="height: 20px; width: 20px;" id="<?php echo $field;?>_detailed_view" name="<?php echo $field;?>">
		</td>
		<td data-title="Add Access"><input type="checkbox" <?php if (strpos($value, '*detailed_add*') !== FALSE) {
			echo " checked"; } ?> onclick='privilegesConfig(this);' value="detailed_add" style="height: 20px; width: 20px;" id="<?php echo $field;?>_detailed_add" name="<?php echo $field;?>">
		</td>
		<td data-title="Edit Access"><input type="checkbox" <?php if (strpos($value, '*detailed_edit*') !== FALSE) {
			echo " checked"; } ?> onclick='privilegesConfig(this);' value="detailed_edit" style="height: 20px; width: 20px;" id="<?php echo $field;?>_detailed_edit" name="<?php echo $field;?>">
		</td>
		<td data-title="Archive Access"><input type="checkbox" <?php if (strpos($value, '*detailed_archive*') !== FALSE) {
			echo " checked"; } ?> onclick='privilegesConfig(this);' value="detailed_archive" style="height: 20px; width: 20px;" id="<?php echo $field;?>_detailed_archive" name="<?php echo $field;?>">
		</td>
		<td data-title="Settings Permission"><input type="checkbox" <?php if (strpos($value, '*configure*') !== FALSE) {
			echo " checked"; } ?> onclick='privilegesConfig(this);' value="configure" style="height: 20px; width: 20px;" id="<?php echo $field;?>_configure" name="<?php echo $field;?>">
		</td>
		<td data-title="Full Search Permission">
		<?php if ( $full_search == 1 ) { ?><input type="checkbox" <?php if (strpos($value, '*search*') !== FALSE) {
				echo " checked"; } ?> onclick='privilegesConfig(this);' value="search" style="height: 20px; width: 20px;" id="<?php echo $field;?>_search" name="<?php echo $field;?>">
		<?php } ?>
		</td>
		<td data-title="Approvals Permission">
		<?php if ( $approvals == 1 ) { ?><input type="checkbox" <?php if (strpos($value, '*approvals*') !== FALSE) {
				echo " checked"; } ?> onclick='privilegesConfig(this);' value="approvals" style="height: 20px; width: 20px;" id="<?php echo $field;?>_approval" name="<?php echo $field;?>">
		<?php } ?>
		</td>
		<td data-title="Strict View Only">
		<?php if ( $strict_view == 1 ) { ?><input type="checkbox" <?php if (strpos($value, '*strictview*') !== FALSE) {
				echo " checked"; } ?> onclick='privilegesConfig(this);' value="strictview" style="height: 20px; width: 20px;" id="<?php echo $field;?>_strictview" name="<?php echo $field;?>">
		<?php } ?>
		</td>
		<?php if ( $subtab == 1 ) { ?>
			<td data-title="Subtab Permissions" align="center">
				<a class="" href="software_config_subtabs.php?tile=<?= $field; ?>&level=<?= $level_url; ?>"><img class="settings-classic wiggle-me" src="../img/icons/settings-4.png" title="Sub Tab Settings" style="width:30px;"></a>
			</td><?php
		} else { ?>
			<td>&nbsp;</td><?php
		} ?>
		<?php if ( $dashboard == 1 ) { ?>
			<td data-title="Dashboard Permissions" align="center">
				<a class="" href="software_config_dashboard.php?tile=<?= $field; ?>&level=<?= $level_url; ?>"><img class="settings-classic wiggle-me" src="../img/icons/settings-4.png" title="Sub Tab Settings" style="width:30px;"></a>
			</td><?php
		} else { ?>
			<td>&nbsp;</td><?php
		} ?>
		<?php if ( $field_permissions == 1 ) { ?>
			<td data-title="Field Permissions" align="center">
				<a class="" href="software_config_fields.php?tile=<?= $field; ?>&level=<?= $level_url; ?>"><img class="settings-classic wiggle-me" src="../img/icons/settings-4.png" title="Field Permission Settings" style="width:30px;"></a>
			</td><?php
		} else { ?>
			<td>&nbsp;</td><?php
		} ?>
		<td data-title="History" align="center"><a><span data-option="<?php echo $field; ?>" class="iframe_open">View All</span></a></td><?php
	}
	?>

	</div>
	</form>
	</div>
</div>
