<?php
/*
Privileges
*/
include ('include.php');
?>
<script>
jQuery(document).ready(function($){
			$('.live-search-box2').focus();
			$('.live-search-list2 tr').each(function(){
			$(this).attr('data-search-term', $(this).text().toLowerCase());
			});

			$('.live-search-box2').on('keyup', function(){

			var searchTerm = $(this).val().toLowerCase();

				$('.live-search-list2 tr').each(function(){

					if ($(this).filter('[data-search-term *= ' + searchTerm + ']').length > 0 || searchTerm.length < 1) {
						$(this).show();
					} else {
						if($(this).hasClass('dont-hide')) {
						} else { $(this).hide(); }
					}

				});

			});

});
$(document).on('change', 'select[name="sub_category"]', function() { changeLevel(this); });

    function privilegesConfig(sel) {
        var type = sel.type;
        var name = sel.name;
        var tile_value = sel.value;
        var final_value = '*';
        var level = $("#level_url").val();
        if($("#"+name+"_hide").prop('checked') == false) {
            final_value += 'hide*';
        }
        if($("#"+name+"_view_use").is(":checked")) {
            final_value += 'view_use*';
        }
        if($("#"+name+"_view_use_add_edit_delete").is(":checked")) {
            final_value += 'view_use_add_edit_delete*';
        }
        if($("#"+name+"_configure").is(":checked")) {
            final_value += 'configure*';
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
			url: "ajax_all.php?fill=privileges_config&name="+name+"&level="+level+"&value="+final_value+"&uncheck_staff="+uncheck_staff+"&check_staff="+check_staff,
			dataType: "html",   //expect html to be returned
			success: function(response){

			}
		});

		//CHANGE LOG
		var contactid = $('.contacterid').val();
		$.ajax({    //create an ajax request to load_page.php
			type: "GET",
			url: "ajax_all.php?fill=privileges_config_log&name="+name+"&level="+level+"&value="+final_value+"&contactid="+contactid+"&uncheck_staff="+uncheck_staff+"&check_staff="+check_staff,
			dataType: "html",   //expect html to be returned
			success: function(response){
			}
		});
    }
    function changeLevel(sel) {
        var stage = sel.value;
        window.location = 'security_privileges.php?level='+stage;
    }
</script>
</head>
<body>
<?php include_once ('navigation.php');
checkAuthorised();
?>

<div class="container triple-pad-bottom">
<input type='hidden' value='<?php echo $_SESSION['contactid']; ?>' class='contacterid'>
    <div class="row">
		<div class="col-md-12">

        <?php include('Settings/settings_navigation.php'); ?>
        <br><br><?php $date = date('Y/m/d', time()); if($date <= '2016/09/17') { ?>
		<img class='wiggle-me' src='img/icons/star-1.png' width="25px"> <b><em>New!</em></b> <?php } ?><a href='History/privileges_history.php'><button type="button" class="btn brand-btn mobile-block " >View History</button></a>
		<br><br>
		<?php  echo '<div class="row live-search-list2">';

		?>
        <form id="form1" name="form1" method="post"	action="add_services.php" enctype="multipart/form-data" class="form-horizontal" role="form">

        <?php
        $sql=mysqli_query($dbc,"SELECT * FROM  security_level");
        $on_security = '';

        while ($fieldinfo=mysqli_fetch_field($sql))
        {
            $field_name = $fieldinfo->name;
            $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM security_level WHERE $field_name LIKE '%*turn_on*%'"));
            if($get_config[$field_name]) {
               $on_security[] = $field_name;
            }
        }
        $level_url = '';
        if(!empty($_GET['level'])) {
            $level_url = $_GET['level'];
        } else {
			$contacterid = $_SESSION['contactid'];
			$result = mysqli_query($dbc, "SELECT * FROM contacts WHERE contactid= '$contacterid'");
			while($row = mysqli_fetch_assoc($result)) {
				$role = $row['role'];
			}
			if(stripos(','.$role.',',',super,') !== false) {
				$level_url = 'admin';
			} else {
				$level_url = $role;
			}
		}
        ?>
        <input type="hidden" id="level_url" name="level_url" value="<?php echo $level_url ?>" />
        <div class="form-group">
            <label for="travel_task" class="col-sm-4 control-label">Select the Security Level you wish to set tile access privileges to:</label>
            <div class="col-sm-8">
            <select id="sub_category" name="sub_category" class="chosen-select-deselect form-control" width="380">
                <option value=''></option>
                <?php
                $selected = '';
                $disabled = '';
                foreach($on_security as $category => $value)  {
                    $select_value = get_securitylevel($dbc, $value);
                    if($value == $level_url) {
                        $selected = ' selected';
                    } else {
                        $selected = '';
                    }
                    if($value == 'super') {
                        $disabled = ' disabled';
                    } else {
                        $disabled = '';
                    }
                  ?>
                  <option <?php echo $selected.' '.$disabled; ?> value="<?php echo $value; ?>"><?php echo $select_value; ?></option>
                <?php } ?>
            </select>
          </div>
        </div>

       <?php

	   echo "<center><input type='text' name='x' class=' form-control live-search-box2' placeholder='Search for a tile...' style='max-width:300px; margin-bottom:20px;'></center>";
        $level = $level_url;
        $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM security_privileges WHERE level='$level_url'"));
        $tile = $get_config['tile'];

        //$privileges = get_privileges($dbc, $tile);
        ?>

        <table class='table table-bordered'>
            <tr class='hidden-sm hidden-xs dont-hide'>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="The only visible tiles accessible in this section are tiles activated in the Enable/Disable Tiles section."><img src="img/info.png" width="20"></a>
                </span>
                Tiles Accessible</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="Security levels marked here will have access to see the tile outlined."><img src="img/info.png" width="20"></a>
                </span>
                Show Tile</th>
                <!-- <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="This security setting allows staff assigned the ability to work within the tiles functionality."><img src="img/info.png" width="20"></a>
                </span>
                View & Use</th>
                -->
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="This security setting allows those assigned the ability to access, add, edit and delete all functionality relevant to the particular tile."><img src="img/info.png" width="20"></a>
                </span>
                View/Use/Add/Edit/Delete</th>
                <th>
                <span class="popover-examples list-inline">&nbsp;
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="This security setting allows those assigned the ability to configure the view, settings, and structure relevant to the particular tile."><img src="img/info.png" width="20"></a>
                </span>
                Configure Tile</th>
				<th>Subtab Permissions</th>
            </tr>
			<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Software Settings:</div></th></tr>
            <?php
            $sql=mysqli_query($dbc,"SELECT * FROM  tile_config");
            $on_security = ',';

            while ($fieldinfo=mysqli_fetch_field($sql))
            {
                $field_name = $fieldinfo->name;
                $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM tile_config WHERE $field_name LIKE '%turn_on%'"));
                if($get_config[$field_name]) {
                    $on_security .= $field_name.',';
                }
            }
            ?>
			<?php if(strpos($on_security, ',archiveddata,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Archived Data</td>
                <?php echo security_tile_config_function('archiveddata', get_privileges($dbc, 'archiveddata',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',ffmsupport,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">FFM Support</td>
                <?php echo security_tile_config_function('ffmsupport', get_privileges($dbc, 'ffmsupport',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',helpdesk,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Help Desk</td>
                <?php echo security_tile_config_function('helpdesk', get_privileges($dbc, 'helpdesk',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if(strpos($on_security, ',software_config,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Settings</td>
                <?php echo security_tile_config_function('software_config', get_privileges($dbc, 'software_config',$level), 1, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if(strpos($on_security, ',staff,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Staff</td>
                <?php echo security_tile_config_function('staff', get_privileges($dbc, 'staff',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Common Practice:</div></th></tr>

			<?php if (strpos($on_security, ',agenda_meeting,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Agendas & Meetings</td>
                <?php echo security_tile_config_function('agenda_meeting', get_privileges($dbc, 'agenda_meeting',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($on_security, ',client_documents,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Client Documents</td>
                <?php echo security_tile_config_function('client_documents', get_privileges($dbc, 'client_documents',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',contacts,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Contacts</td>
                <?php echo security_tile_config_function('contacts', get_privileges($dbc, 'contacts',$level), 1, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',documents,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Documents</td>
                <?php echo security_tile_config_function('documents', get_privileges($dbc, 'documents',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if (strpos($on_security, ',internal_documents,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Internal Documents</td>
                <?php echo security_tile_config_function('internal_documents', get_privileges($dbc, 'internal_documents',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',passwords,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Passwords</td>
                <?php echo security_tile_config_function('passwords', get_privileges($dbc, 'passwords',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if(strpos($on_security, ',profile,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Profile</td>
                <?php echo security_tile_config_function('profile', get_privileges($dbc, 'profile',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>

			<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Human Resources:</div></th></tr>

			<?php if (strpos($on_security, ',certificate,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Certificate</td>
                <?php echo security_tile_config_function('certificate', get_privileges($dbc, 'certificate',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',emp_handbook,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Employee Handbook</td>
                <?php echo security_tile_config_function('emp_handbook', get_privileges($dbc, 'emp_handbook',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',how_to_guide,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">How to Guide</td>
                <?php echo security_tile_config_function('how_to_guide', get_privileges($dbc, 'how_to_guide',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',hr,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">HR</td>
                <?php echo security_tile_config_function('hr', get_privileges($dbc, 'hr',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',incident_report,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?= INC_REP_TILE ?></td>
                <?php echo security_tile_config_function('incident_report', get_privileges($dbc, 'incident_report',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',ops_manual,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Operations Manual</td>
                <?php echo security_tile_config_function('ops_manual', get_privileges($dbc, 'ops_manual',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',orientation,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Orientation</td>
                <?php echo security_tile_config_function('orientation', get_privileges($dbc, 'orientation',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',policy_procedure,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Policy & Procedure</td>
                <?php echo security_tile_config_function('policy_procedure', get_privileges($dbc, 'policy_procedure',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>

			<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Sales:</div></th></tr>

            <?php if(strpos($on_security, ',infogathering,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Information Gathering</td>
                <?php echo security_tile_config_function('infogathering', get_privileges($dbc, 'infogathering',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($on_security, ',marketing_material,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Marketing Material</td>
                <?php echo security_tile_config_function('marketing_material', get_privileges($dbc, 'marketing_material',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($on_security, ',sales,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Sales</td>
                <?php echo security_tile_config_function('sales', get_privileges($dbc, 'sales',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',sales_order,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?= SALES_ORDER_TILE ?></td>
                <?php echo security_tile_config_function('sales_order', get_privileges($dbc, 'sales_order',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>

			<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Inventory Management:</div></th></tr>

			<?php if(strpos($on_security, ',assets,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Assets</td>
                <?php echo security_tile_config_function('assets', get_privileges($dbc, 'assets',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',equipment,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Equipment</td>
                <?php echo security_tile_config_function('equipment', get_privileges($dbc, 'equipment',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',inventory,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Inventory</td>
                <?php echo security_tile_config_function('inventory', get_privileges($dbc, 'inventory',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',material,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Material</td>
                <?php echo security_tile_config_function('material', get_privileges($dbc, 'material',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>

			<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Collaborative:</div></th></tr>

            <?php if (strpos($on_security, ',communication,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Communication</td>
                <?php echo security_tile_config_function('communication', get_privileges($dbc, 'communication',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if (strpos($on_security, ',email_communication,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Email Communication</td>
                <?php echo security_tile_config_function('email_communication', get_privileges($dbc, 'email_communication',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if(strpos($on_security, ',newsboard,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">News Board</td>
                <?php echo security_tile_config_function('newsboard', get_privileges($dbc, 'newsboard',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>

            <?php if(strpos($on_security, ',tasks,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?= TASK_TILE ?></td>
                <?php echo security_tile_config_function('tasks', get_privileges($dbc, 'tasks',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <!--
            <?php if(strpos($on_security, ',tasks_updated,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Tasks (Updated)</td>
                <?php echo security_tile_config_function('tasks_updated', get_privileges($dbc, 'tasks_updated',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            -->

            <tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'><?= ESTIMATE_TILE ?>/Quotes:</div></th></tr>

			<?php if(strpos($on_security, ',estimate,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?= ESTIMATE_TILE ?></td>
                <?php echo security_tile_config_function('estimate', get_privileges($dbc, 'estimate',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if(strpos($on_security, ',quote,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Quote</td>
                <?php echo security_tile_config_function('quote', get_privileges($dbc, 'quote',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>

            <tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Safety:</div></th></tr>

            <?php if(strpos($on_security, ',driving_log,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Driving Log</td>
                <?php echo security_tile_config_function('driving_log', get_privileges($dbc, 'driving_log',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',safety,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Safety</td>
                <?php echo security_tile_config_function('safety', get_privileges($dbc, 'safety',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>

			<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Project Add Ons:</div></th></tr>

			<?php if(strpos($on_security, ',addendum,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Addendum</td>
                <?php echo security_tile_config_function('addendum', get_privileges($dbc, 'addendum',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',addition,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Addition</td>
                <?php echo security_tile_config_function('addition', get_privileges($dbc, 'addition',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>

			<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Operations:</div></th></tr>

			<?php if(strpos($on_security, ',field_job,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Field Jobs</td>
                <?php echo security_tile_config_function('field_job', get_privileges($dbc, 'field_job',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',project,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?php if (PROJECT_TILE=='Projects') { echo "Project"; } else { echo PROJECT_TILE; } ?></td>
                <?php echo security_tile_config_function('project', get_privileges($dbc, 'project',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>

			<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Project/Job Details:</div></th></tr>

			<?php if(strpos($on_security, ',custom,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Custom</td>
                <?php echo security_tile_config_function('custom', get_privileges($dbc, 'custom',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',labour,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Labour</td>
                <?php echo security_tile_config_function('labour', get_privileges($dbc, 'labour',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',package,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Packages</td>
                <?php echo security_tile_config_function('package', get_privileges($dbc, 'package',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if(strpos($on_security, ',products,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Products</td>
                <?php echo security_tile_config_function('products', get_privileges($dbc, 'products',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',promotion,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Promotions</td>
                <?php echo security_tile_config_function('promotion', get_privileges($dbc, 'promotion',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',rate_card,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Rate Cards</td>
                <?php echo security_tile_config_function('rate_card', get_privileges($dbc, 'rate_card',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if(strpos($on_security, ',services,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Services</td>
                <?php echo security_tile_config_function('services', get_privileges($dbc, 'services',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>

            <tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Project/Job Type:</div></th></tr>

			<?php if(strpos($on_security, ',assembly,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Assembly</td>
                <?php echo security_tile_config_function('assembly', get_privileges($dbc, 'assembly',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',business_development,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Business Development</td>
                <?php echo security_tile_config_function('business_development', get_privileges($dbc, 'business_development',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',internal,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Internal</td>
                <?php echo security_tile_config_function('internal', get_privileges($dbc, 'internal',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',manufacturing,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Manufacturing</td>
                <?php echo security_tile_config_function('manufacturing', get_privileges($dbc, 'manufacturing',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',marketing,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Marketing</td>
                <?php echo security_tile_config_function('marketing', get_privileges($dbc, 'marketing',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',process_development,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Process Development</td>
                <?php echo security_tile_config_function('process_development', get_privileges($dbc, 'process_development',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',rd,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">R&D</td>
                <?php echo security_tile_config_function('rd', get_privileges($dbc, 'rd',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if(strpos($on_security, ',sred,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">SR&ED</td>
                <?php echo security_tile_config_function('sred', get_privileges($dbc, 'sred',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>

			<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Project/Job Tracking:</div></th></tr>

			<?php if(strpos($on_security, ',daysheet,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Daysheet</td>
                <?php echo security_tile_config_function('daysheet', get_privileges($dbc, 'daysheet',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',punch_card,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Punch Card</td>
                <?php echo security_tile_config_function('punch_card', get_privileges($dbc, 'punch_card',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',ticket,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?= TICKET_TILE ?></td>
                <?php echo security_tile_config_function('ticket', get_privileges($dbc, 'ticket',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',time_tracking,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Time Tracking</td>
                <?php echo security_tile_config_function('time_tracking', get_privileges($dbc, 'time_tracking',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',work_order,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Work Order</td>
                <?php echo security_tile_config_function('work_order', get_privileges($dbc, 'work_order',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',expense,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Expense</td>
                <?php echo security_tile_config_function('expense', get_privileges($dbc, 'expense',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if(strpos($on_security, ',pos,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment"><?= POS_ADVANCE_TILE ?></td>
                <?php echo security_tile_config_function('pos', get_privileges($dbc, 'pos',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if(strpos($on_security, ',purchase_order,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Purchase Order</td>
                <?php echo security_tile_config_function('purchase_order', get_privileges($dbc, 'purchase_order',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
            <?php if(strpos($on_security, ',vpl,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Vendor Price List</td>
                <?php echo security_tile_config_function('vpl', get_privileges($dbc, 'vpl',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',gantt_chart,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Gantt Chart</td>
                <?php echo security_tile_config_function('gantt_chart', get_privileges($dbc, 'gantt_chart',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
			<?php if(strpos($on_security, ',report,') !== FALSE) { ?>
            <tr>
                <td data-title="Comment">Report</td>
                <?php echo security_tile_config_function('report', get_privileges($dbc, 'report',$level), 0, $level_url); ?>
            </tr>
            <?php } ?>
        </table>

        <?php
        function security_tile_config_function($field, $value, $subtab, $level_url) { ?>
            <td data-title="Unit Number"><input type="checkbox" <?php if (strpos($value, 'hide*') == FALSE) {
                echo " checked"; } ?> onclick='privilegesConfig(this);' value="hide" style="height: 20px; width: 20px;" id="<?php echo $field;?>_hide" name="<?php echo $field;?>">
            </td>
            <!--
            <td data-title="Unit Number"><input type="checkbox" <?php if (strpos($value, '*view_use*') !== FALSE) {
                echo " checked"; } ?> onclick='privilegesConfig(this);' value="view_use" style="height: 20px; width: 20px;" id="<?php echo $field;?>_view_use" name="<?php echo $field;?>">
            </td>
            -->
            <td data-title="Unit Number"><input type="checkbox" <?php if (strpos($value, '*view_use_add_edit_delete*') !== FALSE) {
                echo " checked"; } ?> onclick='privilegesConfig(this);' value="view_use_add_edit_delete" style="height: 20px; width: 20px;" id="<?php echo $field;?>_view_use_add_edit_delete" name="<?php echo $field;?>">
            </td>
            <td data-title="Unit Number"><input type="checkbox" <?php if (strpos($value, '*configure*') !== FALSE) {
                echo " checked"; } ?> onclick='privilegesConfig(this);' value="configure" style="height: 20px; width: 20px;" id="<?php echo $field;?>_configure" name="<?php echo $field;?>">
            </td><?php
			if ( $subtab == 1 ) { ?>
				<td data-title="Subtab Settings" align="center">
					<a class="" href="software_config_subtabs.php?tile=<?= $field; ?>&level=<?= $level_url; ?>"><img class="settings-classic wiggle-me" src="img/icons/settings-4.png" title="Subtab Settings" style="width:30px;"></a>
				</td><?php
			} else { ?>
				<td>&nbsp;</td><?php
			}
		} ?>

        </form>
		</div>
        </div>
    </div>
</div>
<?php include ('footer.php'); ?>