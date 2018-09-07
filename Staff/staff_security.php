<?php include_once('../include.php');
checkAuthorised('staff');?>
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

	$('tr.row_heading').each(function() {
		if($(this).next('tr').hasClass('row_heading') || $(this).next('tr').length == 0) {
			$(this).hide();
		}
	});
});
	$(document).on('change', 'select[name="sub_category"]', function() { changeLevel(this); });
    function changeLevel(sel) {
        var stage = sel.value;
        window.location = 'staff.php?tab=security&level='+stage;
    }
</script>

<div class="container triple-pad-bottom">
<input type='hidden' value='<?php echo $_SESSION['contactid']; ?>' class='contacterid'>
    <div class="row">
		<div class="col-md-12">
		<br><br>
		<?php  echo '<div class="row live-search-list2">';

		?>
        <form id="form1" name="form1" method="post"	action="add_services.php" enctype="multipart/form-data" class="form-horizontal" role="form">

        <?php
        $sql=mysqli_query($dbc,"SELECT * FROM  security_level");
        $on_security = get_security_levels($dbc);

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
            <label for="travel_task" class="col-sm-4 control-label">Select a Security Level to view:</label>
            <div class="col-sm-8">
            <select id="sub_category" name="sub_category" class="chosen-select-deselect form-control" width="380">
                <?php
                $selected = '';
                $disabled = '';
                foreach($on_security as $category => $value)  {
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
                  <option <?php echo $selected.' '.$disabled; ?> value="<?php echo $value; ?>"><?php echo $category; ?></option>
                <?php } ?>
            </select>
          </div>
        </div>

       <?php

	   echo "<center><input type='text' name='x' class=' form-control live-search-box2' placeholder='Search for a tile...' style='max-width:300px; margin-bottom:20px;'></center>";
        $level = $level_url;
        $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT * FROM security_privileges WHERE level='$level_url'"));
        $tile = $get_config['tile'];
        ?>

        <table class='table table-bordered'>
            <tr class='hidden-sm hidden-xs dont-hide'>
                <th><span class="popover-examples list-inline">
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="This lists all tiles that are visible to the selected security level"><img src="../img/info-w.png" width="20"></a>
                </span>Tiles Available</th>
                <th><span class="popover-examples list-inline">
                <a href="#job_file" data-toggle="tooltip" data-placement="top" title="This security setting allows those assigned the ability to access, add, edit and delete all entries to the particular tile."><img src="../img/info-w.png" width="20"></a>
                </span>View/Use/Add/Edit/Archive</th>
				<th>Sub Tab Permissions</th>
				<th>Settings Permissions</th>
            </tr>
            <?php
            //$sql=mysqli_query($dbc,"SELECT * FROM  tile_config");
            $on_security = ','.mysqli_fetch_array(mysqli_query($dbc, "SELECT GROUP_CONCAT(`tile_name`) enabled FROM `tile_security` WHERE `user_enabled`=1"))['enabled'].',';

            //while ($fieldinfo=mysqli_fetch_field($sql))
            //{
            //    $field_name = $fieldinfo->name;
            //    $get_config = mysqli_fetch_assoc(mysqli_query($dbc,"SELECT $field_name FROM tile_config WHERE $field_name LIKE '%turn_on%'"));
            //    if($get_config[$field_name]) {
            //        $on_security .= $field_name.',';
            //    }
            //}

            if(any_string_found(['archiveddata','ffmsupport','helpdesk','software_config','staff'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Software Settings:</div></th></tr>
			<?php }

			if(strpos($on_security, ',archiveddata,') !== FALSE) {
				echo security_tile_config_function('Archived Data','archiveddata', get_privileges($dbc, 'archiveddata',$level), 0, $level_url);
            }
			if(strpos($on_security, ',ffmsupport,') !== FALSE) {
				echo security_tile_config_function('FFM Support','ffmsupport', get_privileges($dbc, 'ffmsupport',$level), 0, $level_url);
            }
			if(strpos($on_security, ',helpdesk,') !== FALSE) {
				echo security_tile_config_function('Help Desk','helpdesk', get_privileges($dbc, 'helpdesk',$level), 0, $level_url);
            }
			if(strpos($on_security, ',software_config,') !== FALSE) {
				echo security_tile_config_function('Settings','software_config', get_privileges($dbc, 'software_config',$level), 1, $level_url);
            }
			if(strpos($on_security, ',staff,') !== FALSE) {
				echo security_tile_config_function('Staff','staff', get_privileges($dbc, 'staff',$level), 1, $level_url);
            }

			if(any_string_found(['agenda_meeting','client_documents','contacts','documents','internal_documents','passwords','profile'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Common Practice:</div></th></tr>
			<?php }

			if (strpos($on_security, ',agenda_meeting,') !== FALSE) {
				echo security_tile_config_function('Agenda & Meetings','agenda_meeting', get_privileges($dbc, 'agenda_meeting',$level), 0, $level_url);
			}
			if (strpos($on_security, ',client_documents,') !== FALSE) {
				echo security_tile_config_function('Client Documents','client_documents', get_privileges($dbc, 'client_documents',$level), 0, $level_url);
			}
			if(strpos($on_security, ',contacts,') !== FALSE) {
				echo security_tile_config_function('Contacts','contacts', get_privileges($dbc, 'contacts',$level), 1, $level_url);
			}
			if(strpos($on_security, ',documents,') !== FALSE) {
				echo security_tile_config_function('Documents','documents', get_privileges($dbc, 'documents',$level), 0, $level_url);
			}
			if (strpos($on_security, ',internal_documents,') !== FALSE) {
				echo security_tile_config_function('Internal Documents','internal_documents', get_privileges($dbc, 'internal_documents',$level), 0, $level_url);
			}
			if(strpos($on_security, ',passwords,') !== FALSE) {
				echo security_tile_config_function('Passwords','passwords', get_privileges($dbc, 'passwords',$level), 0, $level_url);
			}
			if(strpos($on_security, ',profile,') !== FALSE) {
				echo security_tile_config_function('Profile','profile', get_privileges($dbc, 'profile',$level), 0, $level_url);
			}

			if(any_string_found(['certificate','emp_handbook','how_to_guide','hr','incident_report','ops_manual','orientation','policy_procedure'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Human Resources:</div></th></tr>
			<?php }

			if (strpos($on_security, ',certificate,') !== FALSE) {
				echo security_tile_config_function('Certificate','certificate', get_privileges($dbc, 'certificate',$level), 0, $level_url);
			}
			if(strpos($on_security, ',emp_handbook,') !== FALSE) {
				echo security_tile_config_function('Employee Handbook','emp_handbook', get_privileges($dbc, 'emp_handbook',$level), 0, $level_url);
			}
			if(strpos($on_security, ',how_to_guide,') !== FALSE) {
				echo security_tile_config_function('How to Guide','how_to_guide', get_privileges($dbc, 'how_to_guide',$level), 0, $level_url);
			}
			if(strpos($on_security, ',hr,') !== FALSE) {
				echo security_tile_config_function('HR','hr', get_privileges($dbc, 'hr',$level), 0, $level_url);
			}
			if(strpos($on_security, ',incident_report,') !== FALSE) {
				echo security_tile_config_function('Incident Reports','incident_report', get_privileges($dbc, 'incident_report',$level), 0, $level_url);
			}
			if(strpos($on_security, ',ops_manual,') !== FALSE) {
				echo security_tile_config_function('Operations Manual','ops_manual', get_privileges($dbc, 'ops_manual',$level), 0, $level_url);
			}
			if(strpos($on_security, ',orientation,') !== FALSE) {
				echo security_tile_config_function('Orientation','orientation', get_privileges($dbc, 'orientation',$level), 0, $level_url);
			}
			if(strpos($on_security, ',policy_procedure,') !== FALSE) {
				echo security_tile_config_function('Policy & Procedure','policy_procedure', get_privileges($dbc, 'policy_procedure',$level), 0, $level_url);
			}

			if(any_string_found(['infogathering','marketing_material','sales','sales_order'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Sales:</div></th></tr>
			<?php }

			if(strpos($on_security, ',infogathering,') !== FALSE) {
				echo security_tile_config_function('Information Gathering','infogathering', get_privileges($dbc, 'infogathering',$level), 0, $level_url);
			}
			if (strpos($on_security, ',marketing_material,') !== FALSE) {
				echo security_tile_config_function('Marketing Material','marketing_material', get_privileges($dbc, 'marketing_material',$level), 0, $level_url);
			}
			if (strpos($on_security, ',sales,') !== FALSE) {
				echo security_tile_config_function('Sales','sales', get_privileges($dbc, 'sales',$level), 0, $level_url);
			}
			if(strpos($on_security, ',sales_order,') !== FALSE) {
				echo security_tile_config_function('Sales Order','sales_order', get_privileges($dbc, 'sales_order',$level), 0, $level_url);
			}

			if(any_string_found(['assets','equipment','inventory','material'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Inventory Management:</div></th></tr>
			<?php }

			if(strpos($on_security, ',assets,') !== FALSE) {
				echo security_tile_config_function('Assets','assets', get_privileges($dbc, 'assets',$level), 0, $level_url);
			}
			if(strpos($on_security, ',equipment,') !== FALSE) {
				echo security_tile_config_function('Equipment','equipment', get_privileges($dbc, 'equipment',$level), 0, $level_url);
			}
			if(strpos($on_security, ',inventory,') !== FALSE) {
				echo security_tile_config_function('Inventory','inventory', get_privileges($dbc, 'inventory',$level), 0, $level_url);
			}
			if(strpos($on_security, ',material,') !== FALSE) {
				echo security_tile_config_function('Material','material', get_privileges($dbc, 'material',$level), 0, $level_url);
			}

			if(any_string_found(['communication','email_communication','newsboard','tasks'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Collaborative:</div></th></tr>
			<?php }

			if (strpos($on_security, ',communication,') !== FALSE) {
				echo security_tile_config_function('Communication','communication', get_privileges($dbc, 'communication',$level), 0, $level_url);
			}
			if (strpos($on_security, ',email_communication,') !== FALSE) {
				echo security_tile_config_function('Email Communication','email_communication', get_privileges($dbc, 'email_communication',$level), 0, $level_url);
			}
			if(strpos($on_security, ',newsboard,') !== FALSE) {
				echo security_tile_config_function('News Board','newsboard', get_privileges($dbc, 'newsboard',$level), 0, $level_url);
			}
			if(strpos($on_security, ',tasks,') !== FALSE) {
				echo security_tile_config_function('Tasks','tasks', get_privileges($dbc, 'tasks',$level), 0, $level_url);
			}

			if(any_string_found(['estimate','quote'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Estimates/Quotes:</div></th></tr>
			<?php }

			if(strpos($on_security, ',estimate,') !== FALSE) {
				echo security_tile_config_function('Estimate','estimate', get_privileges($dbc, 'estimate',$level), 0, $level_url);
			}
			if(strpos($on_security, ',quote,') !== FALSE) {
				echo security_tile_config_function('Quote','quote', get_privileges($dbc, 'quote',$level), 0, $level_url);
			}

			if(any_string_found(['driving_log','safety'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Safety:</div></th></tr>
			<?php }

			if(strpos($on_security, ',driving_log,') !== FALSE) {
				echo security_tile_config_function('Driving Log','driving_log', get_privileges($dbc, 'driving_log',$level), 0, $level_url);
			}
			if(strpos($on_security, ',safety,') !== FALSE) {
				echo security_tile_config_function('Safety','safety', get_privileges($dbc, 'safety',$level), 0, $level_url);
			}

			if(any_string_found(['addendum','addition'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Project Add Ons:</div></th></tr>
			<?php }

			if(strpos($on_security, ',addendum,') !== FALSE) {
				echo security_tile_config_function('Addendum','addendum', get_privileges($dbc, 'addendum',$level), 0, $level_url);
			}
			if(strpos($on_security, ',addition,') !== FALSE) {
				echo security_tile_config_function('Addition','addition', get_privileges($dbc, 'addition',$level), 0, $level_url);
			}

			if(any_string_found(['field_job','project'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Operations:</div></th></tr>
			<?php }

			if(strpos($on_security, ',field_job,') !== FALSE) {
				echo security_tile_config_function('Field Jobs','field_job', get_privileges($dbc, 'field_job',$level), 0, $level_url);
			}
			if(strpos($on_security, ',project,') !== FALSE) {
				echo security_tile_config_function((PROJECT_TILE == 'Projects' ? 'Project' : PROJECT_TILE),'project', get_privileges($dbc, 'project',$level), 0, $level_url);
			}

			if(any_string_found(['custom','labour','package','products','promotion','rate_card','services'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Project/Job Details:</div></th></tr>
			<?php }

			if(strpos($on_security, ',custom,') !== FALSE) {
				echo security_tile_config_function('Custom','custom', get_privileges($dbc, 'custom',$level), 0, $level_url);
			}
			if(strpos($on_security, ',labour,') !== FALSE) {
				echo security_tile_config_function('Labour','labour', get_privileges($dbc, 'labour',$level), 0, $level_url);
			}
			if(strpos($on_security, ',package,') !== FALSE) {
				echo security_tile_config_function('Packages','package', get_privileges($dbc, 'package',$level), 0, $level_url);
			}
			if(strpos($on_security, ',products,') !== FALSE) {
				echo security_tile_config_function('Products','products', get_privileges($dbc, 'products',$level), 0, $level_url);
			}
			if(strpos($on_security, ',promotion,') !== FALSE) {
				echo security_tile_config_function('Promotions','promotion', get_privileges($dbc, 'promotion',$level), 0, $level_url);
			}
			if(strpos($on_security, ',rate_card,') !== FALSE) {
				echo security_tile_config_function('Rate Cards','rate_card', get_privileges($dbc, 'rate_card',$level), 0, $level_url);
			}
			if(strpos($on_security, ',services,') !== FALSE) {
				echo security_tile_config_function('Services','services', get_privileges($dbc, 'services',$level), 0, $level_url);
			}

			if(any_string_found(['assembly','business_development','internal','manufacturing','marketing','process_development','rd','sred'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Project/Job Type:</div></th></tr>
			<?php }

			if(strpos($on_security, ',assembly,') !== FALSE) {
				echo security_tile_config_function('Assembly','assembly', get_privileges($dbc, 'assembly',$level), 0, $level_url);
			}
			if(strpos($on_security, ',business_development,') !== FALSE) {
				echo security_tile_config_function('Business Development','business_development', get_privileges($dbc, 'business_development',$level), 0, $level_url);
			}
			if(strpos($on_security, ',internal,') !== FALSE) {
				echo security_tile_config_function('Internal','internal', get_privileges($dbc, 'internal',$level), 0, $level_url);
			}
			if(strpos($on_security, ',manufacturing,') !== FALSE) {
				echo security_tile_config_function('Manufacturing','manufacturing', get_privileges($dbc, 'manufacturing',$level), 0, $level_url);
			}
			if(strpos($on_security, ',marketing,') !== FALSE) {
				echo security_tile_config_function('Marketing','marketing', get_privileges($dbc, 'marketing',$level), 0, $level_url);
			}
			if(strpos($on_security, ',process_development,') !== FALSE) {
				echo security_tile_config_function('Process Development','process_development', get_privileges($dbc, 'process_development',$level), 0, $level_url);
			}
			if(strpos($on_security, ',rd,') !== FALSE) {
				echo security_tile_config_function('R&D','rd', get_privileges($dbc, 'rd',$level), 0, $level_url);
			}
			if(strpos($on_security, ',sred,') !== FALSE) {
				echo security_tile_config_function('SR&ED','sred', get_privileges($dbc, 'sred',$level), 0, $level_url);
			}

			if(any_string_found(['daysheet','punch_card','ticket','time_tracking','work_order','expense','pos','purchase_order','vpl','gantt_chart','report'],$on_security)) { ?>
				<tr class="row_heading"><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Project/Job Tracking:</div></th></tr>
			<?php }

			if(strpos($on_security, ',daysheet,') !== FALSE) {
				echo security_tile_config_function('Daysheet','daysheet', get_privileges($dbc, 'daysheet',$level), 0, $level_url);
			}
			if(strpos($on_security, ',punch_card,') !== FALSE) {
				echo security_tile_config_function('Punch Card','punch_card', get_privileges($dbc, 'punch_card',$level), 0, $level_url);
			}
			if(strpos($on_security, ',ticket,') !== FALSE) {
				echo security_tile_config_function('Ticket','ticket', get_privileges($dbc, 'ticket',$level), 0, $level_url);
			}
			if(strpos($on_security, ',time_tracking,') !== FALSE) {
				echo security_tile_config_function('Time Tracking','time_tracking', get_privileges($dbc, 'time_tracking',$level), 0, $level_url);
			}
			if(strpos($on_security, ',work_order,') !== FALSE) {
				echo security_tile_config_function('Work Order','work_order', get_privileges($dbc, 'work_order',$level), 0, $level_url);
			}
			if(strpos($on_security, ',expense,') !== FALSE) {
				echo security_tile_config_function('Expense','expense', get_privileges($dbc, 'expense',$level), 0, $level_url);
			}
			if(strpos($on_security, ',pos,') !== FALSE) {
				echo security_tile_config_function(POS_ADVANCE_TILE,'pos', get_privileges($dbc, 'pos',$level), 0, $level_url);
			}
			if(strpos($on_security, ',purchase_order,') !== FALSE) {
				echo security_tile_config_function('Purchase Order','purchase_order', get_privileges($dbc, 'purchase_order',$level), 0, $level_url);
			}
			if(strpos($on_security, ',vpl,') !== FALSE) {
				echo security_tile_config_function('Vendor Price List','vpl', get_privileges($dbc, 'vpl',$level), 0, $level_url);
			}
			if(strpos($on_security, ',gantt_chart,') !== FALSE) {
				echo security_tile_config_function('Gantt Chart','gantt_chart', get_privileges($dbc, 'gantt_chart',$level), 0, $level_url);
			}
			if(strpos($on_security, ',report,') !== FALSE) {
				echo security_tile_config_function('Report','report', get_privileges($dbc, 'report',$level), 0, $level_url);
			} ?>
        </table>

        <?php
        function security_tile_config_function($title, $field, $value, $subtab, $level_url) {
			if (strpos($value, '*hide*') !== FALSE) { return false; } ?>
			<tr>
				<td data-title="Comment"><?php echo $title; ?></td>
				<td data-title="VUAED"><input type="checkbox" onclick="return false;" <?php if (strpos($value, '*view_use_add_edit_delete*') !== FALSE) {
					echo " checked"; } ?> value="vuaed" style="height: 20px; width: 20px;" id="<?php echo $field;?>_vuaed" name="<?php echo $field;?>">
				</td>
				<td data-title="Subtab"><input type="checkbox" onclick="return false;" <?php if (strpos($value, '*configure*') !== FALSE) {
					echo " checked"; } ?> value="configure" style="height: 20px; width: 20px;" id="<?php echo $field;?>_configure" name="<?php echo $field;?>">
				</td>
				<?php
				$subtab_config = '';
				if($subtab == 1) {
					$sql = "SELECT subtab, status FROM subtab_config WHERE tile='$field' AND security_level='$level_url'";
					$result = mysqli_query($dbc, $sql);
					if(mysqli_num_rows($result) > 0) {
						$subtab_config = '<i>Default</i><!--'.mysqli_error($result).' - '.$sql.'('.mysqli_num_rows($result).')-->';
					}
					else {
						$tabs = [];
						if($field == 'software_config') {
							$tabs = [ 'enable_disable_tiles' => 'Enable/Disable Tiles',
								'security_levels' => 'Activate Security Levels',
								'security_privileges' => 'Set Security Privileges',
								'software_style' => 'Software Style',
								'software_format' => 'Software Format' ];
						}
						else if($field == 'contacts') {

						}
						while($row = mysqli_fetch_array($result)) {
							if(strpos($row['status'],'turn_off') !== false) {
								unset($tabs[$row['subtab']]);
							}
							else {
								$tabs[$row['subtab']] = $row['status'];
							}
						}
						$subtab_config = implode(",<br />\n", $tabs);
					}
				}
				?>
				<td data-title="Settings"><?php echo $subtab_config; ?></td>
			</tr>
		<?php }
		function any_string_found($arr, $haystack) {
			foreach($arr as $needle) {
				if(strpos($haystack,$needle) !== false)
					return true;
			}
			return false;
		}
		?>

        </form>
		</div>
        </div>
    </div>
</div>