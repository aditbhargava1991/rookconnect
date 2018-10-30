<?php
	/*
	 * Title:		Software Subtab Settings
	 * Function:	Settings for Subtabs within a main Tile
	 */

	include ('../include.php');
	error_reporting(0);
	/* Check and set the $tile variable */
	if ( isset ( $_GET[ 'tile' ] ) ) {
		$tile = trim ( $_GET[ 'tile' ] );
		echo '<input type="hidden" name="tile" id="tile" value="'. $tile .'" />';
	} else {
		header( "Location: software_config.php" );
	}
?>

	<script type="text/javascript">
		$(document).on('change', 'select[name="sub_category"]', function() { changeLevel(this); });

		/* Called whenever the Security Level dropdown menu is changed */
		function changeLevel(sel) {
			var security_level = sel.value;
			var tile = $("#tile").val();
			window.location = 'software_config_subtabs.php?tile='+tile+'&level='+security_level;
		}


		/* Called whenever a turn on/off radio button is clicked */
		function subtabConfig(sel) {
			var tile		= $("#tile").val();
			var level_url	= $("#level_url").val();

			//Alert if a Security Level is not selected from the dropdown menu
			if (level_url === '') {
				alert("Please select a Security Level from the dropdown menu first.");
				window.location.reload();

			} else {
				var type = sel.type;
				var subtab = sel.name;
				var subtabid = subtab.replace(/ /gi, '_');
				var subtab_value = sel.value;
				var final_value = '*';

				if($("#"+subtabid+"_turn_on").is(":checked")) {
					final_value += 'turn_on*';
				}
				if($("#"+subtabid+"_turn_off").is(":checked")) {
					final_value += 'turn_off*';
				}

				var isTurnOff = $("#"+subtabid+"_turn_off").is(':checked');
				if(isTurnOff) {
				   var turnOff = name;
				} else {
					var turnOff = '';
				}

				var isTurnOn = $("#"+subtabid+"_turn_on").is(':checked');
				if(isTurnOn) {
				   var turnOn = name;
				} else {
					var turnOn = '';
				}

				$.ajax({
					type: "GET",
					url: "../ajax_all.php?fill=subtab_config&tile="+tile+"&level="+level_url+"&subtab="+subtab+"&value="+final_value+"&turnOff="+turnOff+"&turnOn="+turnOn,
					dataType: "html",   //expect html to be returned
					success: function(response){
						console.log(response);
						window.location.reload();
					}
				});
			}
		}
	</script>
</head>

<body>
	<?php
		include_once ('../navigation.php');
checkAuthorised('security');
	?>

	<div class="container triple-pad-bottom">
		<div class="row">
			<div class="col-md-12">

				<?php /* Set headings */
				switch($tile) {
				case 'contacts':
					echo '<h2 class="double-pad-bottom">Contacts Sub Tab Settings</h2>';
					break;
				case 'contacts3':
					echo '<h2 class="double-pad-bottom">Contacts 3 Sub Tab Settings</h2>';
					break;
				case 'client_info':
					echo '<h2 class="double-pad-bottom">Client Information Sub Tab Settings</h2>';
					break;
				case 'software_config':
					echo '<h2 class="double-pad-bottom">Settings Sub Tab Permissions</h2>';
					break;
				case 'shop_work_orders':
					echo '<h2 class="double-pad-bottom">Shop Work Orders Sub Tab Permissions</h2>';
					break;
				case 'site_work_orders':
					echo '<h2 class="double-pad-bottom">Site Work Orders Sub Tab Permissions</h2>';
					break;
				case 'field_job':
					echo '<h2 class="double-pad-bottom">Field Jobs Sub Tab Permissions</h2>';
					break;
				case 'inventory':
					echo '<h2 class="double-pad-bottom">Inventory Sub Tab Permissions</h2>';
					break;
				case 'passwords':
					echo '<h2 class="double-pad-bottom">Passwords Sub Tab Permissions</h2>';
					break;
				case 'rate_card':
					echo '<h2 class="double-pad-bottom">Rate Cards Sub Tab Permissions</h2>';
					break;
				case 'sales':
					echo '<h2 class="double-pad-bottom">Sales Sub Tab Permissions</h2>';
					break;
				case 'client_documentation':
					echo '<h2 class="double-pad-bottom">Client Documentation Sub Tab Permissions</h2>';
					break;
				case 'client_projects':
					echo '<h2 class="double-pad-bottom">Client Projects Sub Tab Permissions</h2>';
					break;
				case 'contracts':
					echo '<h2 class="double-pad-bottom">Contract Sub Tab Permissions</h2>';
					break;
				case 'staff':
					echo '<h2 class="double-pad-bottom">Staff Sub Tab Permissions</h2>';
					break;
				case 'equipment':
					echo '<h2 class="double-pad-bottom">Equipment Sub Tab Permissions</h2>';
					break;
				case 'report':
					echo '<h2 class="double-pad-bottom">Report Permissions</h2>';
					break;
				case 'invoice':
					echo '<h2 class="double-pad-bottom">Payment Tile Permissions</h2>';
					break;
				case 'posadvanced':
					echo '<h2 class="double-pad-bottom">'.POS_ADVANCE_TILE.' Tile Permissions</h2>';
					break;
				case 'form_builder':
					echo '<h2 class="double-pad-bottom">Form Builder Permissions</h2>';
					break;
				case 'checklist':
					echo '<h2 class="double-pad-bottom">Checklist Permissions</h2>';
					break;
				case 'ticket':
					echo '<h2 class="double-pad-bottom">'.TICKET_TILE.' Permissions</h2>';
					break;
				default:
					echo '<h2 class="double-pad-bottom">Sub Tab Permissions</h2>';
					break;
				} ?>

				<!-- Populate security level -->
				<div class="form-group">
					<label for="travel_task" class="col-sm-5 control-label">Select the Security Level you wish to set sub tab access privileges to:</label>
					<div class="col-sm-7 double-pad-bottom">

						<?php
							if ( !empty ( $_GET[ 'level' ] ) ) {
								$level_url = $_GET[ 'level' ];

							} else {
								$contacterid	= $_SESSION['contactid'];
								$result			= mysqli_query ( $dbc, "SELECT * FROM contacts WHERE contactid='$contacterid'" );

								while ( $row = mysqli_fetch_assoc( $result ) ) {
									$role = $row[ 'role' ];
								}

								$level_url = (stripos(','.$role.',',',super,') !== false) ? 'admin' : $role;
							}
						?>
						<input type="hidden" name="level_url" id="level_url" value="<?= $level_url; ?>" />

						<select id="sub_category" name="sub_category" class="chosen-select-deselect form-control" width="380">
							<option value=""></option><?php foreach(get_security_levels($dbc) as $security_name => $security_level) { ?>
								<option <?= $security_level == 'super' ? 'disabled' : '' ?> <?= $security_level == $level_url ? 'selected' : '' ?> value="<?= $security_level ?>"><?= $security_name ?></option>
							<?php } ?>
						</select>
					</div><!-- .col-sm-6 -->
				</div><!-- .form-group -->

				<table class="table table-bordered">
					<!-- Table headers -->
					<tr class="hidden-sm">
						<th width="40%">Sub Tab Name</th>
						<th width="20%">Enable Sub Tab</th>
						<th width="20%">Disable Sub Tab</th>
						<th width="20%">Last Date Edited</th>
					</tr><?php

					/* Tabbed View Contacts tile subtab settings */
					if ( $tile == 'contacts' ) {
						echo '<tr><td>Summary</td>';
						echo subtab_config_function( $dbc, $tile, $level_url, 'summary' ).'</tr>';
						$tabs		= get_config ( $dbc, 'contacts_tabs' );
						$each_tab	= explode ( ',', $tabs );
						foreach ( $each_tab as $subtab ) {
							echo '<tr><td>Category: '.$subtab.'</td>';
							echo subtab_config_function( $dbc, $tile, $level_url, $subtab ).'</tr>';
						}
						include_once('../Contacts/edit_fields.php');
						foreach($tab_list as $tab_label => $tab_data) {
							echo '<tr><td>Tab: '.$tab_label.'</td>';
							echo subtab_config_function( $dbc, $tile, $level_url, $tab_data[0]).'</tr>';
						}
					}
					/* End Tabbed View Contacts tile subtab settigns */

					/* Contacts tile subtab settings */
					if ( $tile == 'contacts_inbox' ) {
						echo '<tr><td>Summary</td>';
						echo subtab_config_function( $dbc, $tile, $level_url, 'summary' ).'</tr>';
						$tabs		= get_config ( $dbc, 'contacts_tabs' );
						$each_tab	= explode ( ',', $tabs );
						foreach ( $each_tab as $subtab ) {
							echo '<tr><td>Category: '.$subtab.'</td>';
							echo subtab_config_function( $dbc, $tile, $level_url, $subtab ).'</tr>';
						}
						include_once('../Contacts/edit_fields.php');
						foreach($tab_list as $tab_label => $tab_data) {
							echo '<tr><td>Tab: '.$tab_label.'</td>';
							echo subtab_config_function( $dbc, $tile, $level_url, $tab_data[0]).'</tr>';
						}
					}
					/* End Contacts tile subtab settigns */

					/* Client Information tile subtab settings */
					if ( $tile == 'client_info' ) {
						$tabs		= get_config ( $dbc, 'clientinfo_tabs' );
						$each_tab	= explode ( ',', $tabs );
						foreach ( $each_tab as $subtab ) {
							echo '<tr>';
								echo '<td>' . $subtab . '</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, $subtab );
							echo '</tr>';
							if ( $subtab == 'Clients' ) {
								echo '<tr><td>&nbsp;&nbsp;- Details (Limited Access)</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'clients_details_limited' );


								echo '<tr><td>&nbsp;&nbsp;- Details (Full Access)</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'clients_details_full' );


								echo '<tr><td>&nbsp;&nbsp;- Individual Service Plan</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'clients_isp' );


								echo '<tr><td>&nbsp;&nbsp;- Medical Details</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'clients_medical_details' );


								echo '<tr><td>&nbsp;&nbsp;- Medications</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'clients_medications' );


								echo '<tr><td>&nbsp;&nbsp;- Key Methodologies</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'clients_key_methodologies' );


								echo '<tr><td>&nbsp;&nbsp;- Protocols</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'clients_protocols' );


								echo '<tr><td>&nbsp;&nbsp;- Routine</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'clients_routine' );


								echo '<tr><td>&nbsp;&nbsp;- Communication</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'clients_communication' );


								echo '<tr><td>&nbsp;&nbsp;- Activities</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'clients_activities' );


								echo '<tr><td>&nbsp;&nbsp;- Charts</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'clients_medical_charts' );


								echo '<tr><td>&nbsp;&nbsp;- Daily Log Notes</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'clients_daily_log_notes' );

								include_once('../Contacts/edit_fields.php');
								foreach($tab_list as $tab_label => $tab_data) {
									echo '<tr><td>Tab: '.$tab_label.'</td>';
									echo subtab_config_function( $dbc, $tile, $level_url, $tab_data[0]).'</tr>';
								}
							}
						}
					}
					/* End Client Information tile subtab settigns */
					/* Members tile subtab settings */
					if ( $tile == 'members' ) {
						$tabs		= get_config ( $dbc, 'members_tabs' );
						$each_tab	= explode ( ',', $tabs );
						foreach ( $each_tab as $subtab ) {
							echo '<tr>';
								echo '<td>' . $subtab . '</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, $subtab );
							echo '</tr>';
							if ( $subtab == 'Members' ) {
								echo '<tr><td>&nbsp;&nbsp;- Details (Limited Access)</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'members_details_limited' );


								echo '<tr><td>&nbsp;&nbsp;- Details (Full Access)</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'members_details_full' );


								echo '<tr><td>&nbsp;&nbsp;- Medical Details</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'members_medical_details' );


								echo '<tr><td>&nbsp;&nbsp;- Key Methodologies</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'members_key_methodologies' );


								echo '<tr><td>&nbsp;&nbsp;- Activities</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'members_activities' );


								echo '<tr><td>&nbsp;&nbsp;- Daily Log Notes</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, 'members_daily_log_notes' );

								include_once('../Contacts/edit_fields.php');
								foreach($tab_list as $tab_label => $tab_data) {
									echo '<tr><td>Tab: '.$tab_label.'</td>';
									echo subtab_config_function( $dbc, $tile, $level_url, $tab_data[0]).'</tr>';
								}
							}
						}
					}
					/* End Members tile subtab settigns */
					/* Shop Work Orders tile subtab settings */
					if ( $tile == 'shop_work_orders' ): ?>
						<tr><td>Pending Work Order</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pending_work_order' ); ?></tr>
						<tr><td>Shop Work Order</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'shop_work_order' ); ?></tr>
						<tr><td>Shop Time Clock</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'shop_time_clock' ); ?></tr>
						<tr><td>Shop Time Sheets</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'shop_time_sheets' ); ?></tr>
						<tr><td>Payroll</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'payroll' ); ?></tr>
						<tr><td>Accounts Payable</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'accounts_payable' ); ?></tr>
						<tr><td>Billables</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'billables' ); ?></tr>
					<?php
					/* End Shop Work Orders tile subtab settigns */
					/* Site Work Orders tile subtab settings */
					elseif ( $tile == 'site_work_orders' ): ?>
						<tr><td>Work Sites</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sites' ); ?></tr>
						<tr><td>Pending Work Orders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pending' ); ?></tr>
						<tr><td>Site Work Orders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'active' ); ?></tr>
						<tr><td>Work Order Schedule</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'schedule' ); ?></tr>
						<tr><td>Purchase Orders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'po' ); ?></tr>
					<?php
					/* End Site Work Orders tile subtab settigns */
					/* Field Jobs tile subtab settings */
					elseif ( $tile == 'field_job' ): ?>
						<tr><td>Sites</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sites' ); ?></tr>
						<tr><td>Jobs</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'jobs' ); ?></tr>
						<tr><td>Foreman Sheet</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'foreman_sheet' ); ?></tr>
						<tr><td>PO</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'po' ); ?></tr>
						<tr><td>Work Ticket</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'work_ticket' ); ?></tr>
						<tr><td>Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'invoices' ); ?></tr>
						<tr><td>Payroll</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'payroll' ); ?></tr>
					<?php
					/* End Field Jobs tile subtab settigns */
					/* Inventory tile subtab settings */
					elseif ($tile == 'inventory'): ?>
						<tr><td>Inventory</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'inventory' ); ?></tr>
						<tr><td>Warehousing</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'warehouse' ); ?></tr>
						<tr><td>Create Pick Lists</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pick_list_create' ); ?></tr>
						<tr><td>Fill Pick Lists</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pick_list_fill' ); ?></tr>
						<tr><td>Receive Shipment</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'receive_shipment' ); ?></tr>
						<tr><td>Bill of Material</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'bill_of_material' ); ?></tr>
						<tr><td>Bill of Material (Consumables)</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'bill_of_material_consumables' ); ?></tr>
						<tr><td>Waste / Write-Off</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'waste_write_off' ); ?></tr>
						<tr><td>Inventory Checklist</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'checklist' ); ?></tr>
						<tr><td>Order Lists</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'orderlist' ); ?></tr>
						<tr><td>Order Checklists</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'checklist_orders' ); ?></tr>
					<?php
					/* End Inventory tile subtab settigns */
					/* Staff tile subtab settings */
					elseif ($tile == 'staff'):
						/*
						$sql = mysqli_query ( $dbc, "SELECT * FROM  security_level" );
						$on_security = [];

						while ( $fieldinfo = mysqli_fetch_field ( $sql ) ) {
							$field_name = $fieldinfo->name;
							$get_config = mysqli_fetch_assoc ( mysqli_query ( $dbc, "SELECT $field_name FROM security_level WHERE $field_name LIKE '%*turn_on*%'" ) );
							if ( $get_config[$field_name] ) {
								$on_security[] = $field_name;
							}
						}

						foreach ( $on_security as $category => $value ) {
							$select_value = get_securitylevel ( $dbc, $value ); ?>
							<tr><td>Software Access for <?php echo $select_value; ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'software_access_'.$value ); ?></tr><?php
						}
						*/ ?>
						<tr><td>Active Users</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'active' ); ?></tr>
						<tr><td>Suspended Users</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'suspended' ); ?></tr>
						<tr><td>Security Privileges</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'security' ); ?></tr>
						<tr><td>Positions</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'positions' ); ?></tr>
						<tr><td>Reminders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reminders' ); ?></tr>
						<tr><td>ID Card</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'id_card' ); ?></tr>
						<tr><td>Staff Information</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_info' ); ?></tr>
						<tr><td>Staff Bio</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_bio' ); ?></tr>
						<tr><td>Staff Address</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_address' ); ?></tr>
						<tr><td>Employee Information</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'employee' ); ?></tr>
						<tr><td>Driver Information</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'driver' ); ?></tr>
						<tr><td>Direct Deposit Information</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'direct_deposit' ); ?></tr>
						<tr><td>Software ID</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'software_id' ); ?></tr>
						<tr><td>Software Access</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'software_access' ); ?></tr>
						<tr><td>Social Media</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'social' ); ?></tr>
						<tr><td>Emergency</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'emergency' ); ?></tr>
						<tr><td>Health Care</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'health' ); ?></tr>
						<tr><td>Health Concerns</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'health_concerns' ); ?></tr>
						<tr><td>Company Benefits</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'company_benefits' ); ?></tr>
						<tr><td>Schedule</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'schedule' ); ?></tr>
						<tr><td><?= PROJECT_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'projects' ); ?></tr>
						<tr><td><?= TICKET_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'tickets' ); ?></tr>
						<tr><td>HR Record</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'hr_record' ); ?></tr>
						<tr><td>Staff Documents</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_docs' ); ?></tr>
						<tr><td><?= INC_REP_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'incident_reports' ); ?></tr>
						<tr><td>Certificates</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'certificates' ); ?></tr>
						<tr><td>History</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'history' ); ?></tr>
					<?php
					/* End Staff tile subtab settigns */
					/* Goals & Objective tile subtab settings */
					elseif ($tile == 'gao'): ?>
						<tr><td>The Company</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'company' ); ?></tr>
						<tr><td>My Department Goals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'department' ); ?></tr>
						<tr><td>My Goals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'my' ); ?></tr>
					<?php
					/* End Goals & Objective tile subtab settigns */
					/* Profit & Loss tile subtab settings */
					elseif ($tile == 'profit_loss'): ?>
						<tr><td>Revenue</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'revenue' ); ?></tr>
						<tr><td>Receivables</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'receivables' ); ?></tr>
						<tr><td>Compensation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'compensation' ); ?></tr>
						<tr><td>Expenses</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'expenses' ); ?></tr>
						<tr><td>Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'summary' ); ?></tr>
					<?php
					/* End Inventory tile subtab settigns */
					/* Reports tile subtab settings */
					elseif ($tile == 'report'):
						$reports = ','.get_config($dbc, 'reports_dashboard').','; ?>

						<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Operations:</div></th></tr><?php
						if(strpos($reports,',Daysheet,') !== false) { ?>
							<tr><td>Therapist Day Sheet</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pt_daysheet' ); ?></tr>
						<?php }
						if(strpos($reports,',Therapist Stats,') !== false) { ?>
							<tr><td>Therapist Stats</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pt_stats' ); ?></tr>
						<?php }
						if(strpos($reports,',Block Booking vs Not Block Booking,') !== false) { ?>
							<tr><td>Block Booking vs Not Block Booking</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'bb_v_nbb' ); ?></tr>
						<?php }
						if(strpos($reports,',Injury Type,') !== false) { ?>
							<tr><td>Injury Type</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'injury_type' ); ?></tr>
						<?php }
						if(strpos($reports,',Treatment Report,') !== false) { ?>
							<tr><td>Treatment Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'treatment' ); ?></tr>
						<?php }
						if(strpos($reports,',Equipment List,') !== false) { ?>
							<tr><td>Equipment List</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'equipment_list' ); ?></tr>
						<?php }
						if(strpos($reports,',Equipment Transfer,') !== false) { ?>
							<tr><td>Equipment Transfer History</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'equip_transfer' ); ?></tr>
						<?php }
						if(strpos($reports,',Work Order,') !== false) { ?>
							<tr><td>Work Order</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'work_order' ); ?></tr>
						<?php }
						if(strpos($reports,',Staff Tickets,') !== false) { ?>
							<tr><td>Staff <?= TICKET_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_tickets' ); ?></tr>
						<?php }
						if(strpos($reports,',Day Sheet Report,') !== false) { ?>
							<tr><td>Day Sheet Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'day_sheet_report' ); ?></tr>
						<?php }
						if(strpos($reports,',Appointment Summary,') !== false) { ?>
							<tr><td>Appointment Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'appt_summary' ); ?></tr>
						<?php }
						if(strpos($reports,',Patient Block Booking,') !== false) { ?>
							<tr><td>Block Booking</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'block_booking' ); ?></tr>
						<?php }
						if(strpos($reports,',Assessment Tally Board,') !== false) { ?>
							<tr><td>Assessment Tally Board</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'assessment_tallyboard' ); ?></tr>
						<?php }
						if(strpos($reports,',Assessment Follow Up,') !== false) { ?>
							<tr><td>Assessment Follow Up</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'assessment_followup' ); ?></tr>
						<?php }
						if(strpos($reports,',Field Jobs,') !== false) { ?>
							<tr><td>Field Jobs</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'field_jobs' ); ?></tr>
						<?php }
						if(strpos($reports,',Shop Work Orders,') !== false) { ?>
							<tr><td>Shop Work Orders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'shop_work_orders' ); ?></tr>
						<?php }
						if(strpos($reports,',Site Work Orders,') !== false) { ?>
							<tr><td>Site Work Orders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'site_work_orders' ); ?></tr>
						<?php }
						if(strpos($reports,',Scrum Business Productivity Summary,') !== false) { ?>
							<tr><td>Scrum Business Productivity Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'scrum_business_productivity_summary' ); ?></tr>
						<?php }
						if(strpos($reports,',Scrum Staff Productivity Summary,') !== false) { ?>
							<tr><td>Scrum Staff Productivity Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'scrum_staff_productivity_summary' ); ?></tr>
						<?php }
						if(strpos($reports,',Scrum Status Report,') !== false) { ?>
							<tr><td>Scrum Status Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'scrum_status_report' ); ?></tr>
						<?php }
						if(strpos($reports,',Drop Off Analysis,') !== false) { ?>
							<tr><td>Drop Off Analysis</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'dropoff_analysis' ); ?></tr>
						<?php }
						if(strpos($reports,',Discharge Report,') !== false) { ?>
							<tr><td>Discharge Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'discharge' ); ?></tr>
						<?php }
						if(strpos($reports,',Ticket Report,') !== false) { ?>
							<tr><td><?= TICKET_NOUN ?> Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ticket_report' ); ?></tr>
						<?php }
						if(strpos($reports,',Site Work Time,') !== false) { ?>
							<tr><td>Site Work Order Time on Site</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'site_work_time' ); ?></tr>
						<?php }
						if(strpos($reports,',Site Work Driving,') !== false) { ?>
							<tr><td>Site Work Order Driving Logs</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'site_work_driving_logs' ); ?></tr>
						<?php }
						if(strpos($reports,',Purchase Orders,') !== false) { ?>
							<tr><td>Purchase Orders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'purchase_orders' ); ?></tr>
						<?php }
						if(strpos($reports,',Inventory Log,') !== false) { ?>
							<tr><td>Inventory Log</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'inventory_log' ); ?></tr>
						<?php }
						if(strpos($reports,',Point of Sale,') !== false) {
							$pos_title = get_tile_title($dbc);
							$pos_title = (empty($pos_title) ? POS_ADVANCE_TILE : $pos_title); ?>
							<tr><td><?= $pos_title ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'point_of_sale' ); ?></tr>
						<?php }
						if(strpos($reports,',POS,') !== false) { ?>
							<tr><td><?= POS_ADVANCE_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'point_of_sale_advanced' ); ?></tr>
						<?php }
						if(strpos($reports,',Credit Card on File,') !== false) { ?>
							<tr><td>Credit Card on File</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'credit_card_on_file' ); ?></tr>
						<?php }
						if(strpos($reports,',Checklist Time,') !== false) { ?>
							<tr><td>Checklist Time Tracking</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'checklist_time' ); ?></tr>
						<?php }
						if(strpos($reports,',Ticket Time Summary,') !== false) { ?>
							<tr><td><?= TICKET_NOUN ?> Time Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ticket_time_summary' ); ?></tr>
						<?php }
						if(strpos($reports,',Ticket Deleted Notes,') !== false) { ?>
							<tr><td>Archived <?= TICKET_NOUN ?> Notes</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ticket_deleted_notes' ); ?></tr>
						<?php }
						if(strpos($reports,',Service Usage Report,') !== false) { ?>
							<tr><td>Service Usage</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'service_usage' ); ?></tr>
						<?php }
						if(strpos($reports,',Ticket Attached,') !== false) { ?>
							<tr><td>Attached to <?= TICKET_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ticket_attached' ); ?></tr>
						<?php }
						if(strpos($reports,',Time Sheet,') !== false) { ?>
							<tr><td>Time Sheets Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'time_sheet' ); ?></tr>
						<?php }
						if(strpos($reports,',Ticket by Task,') !== false) { ?>
							<tr><td><?= TICKET_NOUN ?> by Task</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ticket_by_task' ); ?></tr>
						<?php }
						if(strpos($reports,',Ticket Activity Report,') !== false) { ?>
							<tr><td><?= TICKET_NOUN ?> Activity Report per Customer</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ticket_activity_report' ); ?></tr>
						<?php }
						if(strpos($reports,',Rate Card Report,') !== false) { ?>
							<tr><td>Rate Cards Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'rate_card_report' ); ?></tr>
						<?php }
						if(strpos($reports,',Import Summary,') !== false) { ?>
							<tr><td>Import Summary Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'import_summary' ); ?></tr>
						<?php }
						if(strpos($reports,',Import Details,') !== false) { ?>
							<tr><td>Detailed Import Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'import_details' ); ?></tr>
						<?php } ?>

						<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Sales:</div></th></tr><?php
						if(strpos($reports,',Validation by Therapist,') !== false) { ?>
							<tr><td>Validation by Therapist</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pt_validation' ); ?></tr>
						<?php }
						if(strpos($reports,',POS Validation,') !== false) { ?>
							<tr><td>POS Basic Validation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'validation' ); ?></tr>
						<?php }
						if(strpos($reports,',POS Advanced Validation,') !== false) { ?>
							<tr><td>POS Advanced Validation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'validation_advanced' ); ?></tr>
						<?php }
						if(strpos($reports,',Daily Deposit Report,') !== false) { ?>
							<tr><td>Daily Deposit Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'daily_deposit' ); ?></tr>
						<?php }
						if(strpos($reports,',Monthly Sales by Injury Type,') !== false) { ?>
							<tr><td>Monthly Sales by Injury Type</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_injury_monthly' ); ?></tr>
						<?php }
						if(strpos($reports,',Invoice Sales Summary,') !== false) { ?>
							<tr><td>Invoice Sales Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_invoice' ); ?></tr>
						<?php }
						if(strpos($reports,',Sales by Customer Summary,') !== false) { ?>
							<tr><td>Sales by Customer Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_customer' ); ?></tr>
						<?php }
						if(strpos($reports,',Sales History by Customer,') !== false) { ?>
							<tr><td>Sales History by Customer</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_customer_history' ); ?></tr>
						<?php }
						if(strpos($reports,',Sales by Service Summary,') !== false) { ?>
							<tr><td>Sales by Service Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_service' ); ?></tr>
						<?php }
						if(strpos($reports,',Sales by Service Category,') !== false) { ?>
							<tr><td>Sales by Service Category</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_service_category' ); ?></tr>
						<?php }
						if(strpos($reports,',Sales by Inventory Summary,') !== false) { ?>
							<tr><td>Sales by Inventory Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_inventory' ); ?></tr>
						<?php }
						if(strpos($reports,',Sales Summary by Injury Type,') !== false) { ?>
							<tr><td>Sales Summary by Injury Type</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_injury' ); ?></tr>
						<?php }
						if(strpos($reports,',Inventory Analysis,') !== false) { ?>
							<tr><td>Inventory Analysis</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'inventory_analysis' ); ?></tr>
						<?php }
						if(strpos($reports,',Unassigned/Error Invoices,') !== false) { ?>
							<tr><td>Unassigned/Error Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'error_invoice' ); ?></tr>
						<?php }
						if(strpos($reports,',Staff Revenue Report,') !== false) { ?>
							<tr><td>Staff Revenue Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_revenue' ); ?></tr>
						<?php }
						if(strpos($reports,',Expense Summary Report,') !== false) { ?>
							<tr><td>Expense Summary Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'expense_report' ); ?></tr>
						<?php }
						if(strpos($reports,',Sales by Inventory/Service Detail,') !== false) { ?>
							<tr><td>Sales by Inventory/Service Detail</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_inv_service_detail' ); ?></tr>
						<?php }
						if(strpos($reports,',Payment Method List,') !== false) { ?>
							<tr><td>Payment Method List</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pay_methods' ); ?></tr>
						<?php }
						if(strpos($reports,',Patient History,') !== false) { ?>
							<tr><td>Patient History</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'patient_history' ); ?></tr>
						<?php }
						if(strpos($reports,',Receipts Summary Report,') !== false) { ?>
							<tr><td>Receipts Summary Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_receipts' ); ?></tr>
						<?php }
						if(strpos($reports,',Gross Revenue by Staff,') !== false) { ?>
							<tr><td>Gross Revenue by Staff</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_gross_revenue' ); ?></tr>
						<?php }
						if(strpos($reports,',Patient Invoices,') !== false) { ?>
							<tr><td>Customer Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'patient_invoice' ); ?></tr>
						<?php }
						if(strpos($reports,',POS Sales Summary,') !== false) { ?>
							<tr><td>POS (Basic) Sales Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_summary' ); ?></tr>
						<?php }
						if(strpos($reports,',POS Advanced Sales Summary,') !== false) { ?>
							<tr><td>POS (Advanced) Sales Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_summary_advanced' ); ?></tr>
						<?php }
						if(strpos($reports,',Profit-Loss,') !== false) { ?>
							<tr><td>Profit-Loss</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'profit_loss' ); ?></tr>
						<?php }
						if(strpos($reports,',Profit-Loss POS Advanced,') !== false) { ?>
							<tr><td>Profit-Loss (POS Advanced)</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'profit_loss_pos_advanced' ); ?></tr>
						<?php }
						if(strpos($reports,',Transaction List by Customer,') !== false) { ?>
							<tr><td>Transaction List by Customer</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'transaction_list' ); ?></tr>
						<?php }
						if(strpos($reports,',Unbilled Invoices,') !== false) { ?>
							<tr><td>Unbilled Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'unbilled_invoices' ); ?></tr>
						<?php }
						if(strpos($reports,',Deposit Detail,') !== false) { ?>
							<tr><td>Deposit Detail</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'deposit_detail' ); ?></tr>
						<?php }
						if(strpos($reports,',Sales Estimates,') !== false) { ?>
							<tr><td>Sales Estimates</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_estimates' ); ?></tr>
						<?php } ?>

						<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Accounts Receivable:</div></th></tr><?php
						if(strpos($reports,',A/R Aging Summary,') !== false) { ?>
							<tr><td>A/R Aging Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ar_aging' ); ?></tr>
						<?php }
						if(strpos($reports,',Patient Aging Receivable Summary,') !== false) { ?>
							<tr><td>Patient Aging Receivable Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ar_patient_aging' ); ?></tr>
						<?php }
						if(strpos($reports,',Insurer Aging Receivable Summary,') !== false) { ?>
							<tr><td>Insurer Aging Receivable Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ar_insurer_aging' ); ?></tr>
						<?php }
						if(strpos($reports,',By Invoice#,') !== false) { ?>
							<tr><td>By Invoice#</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ar_invoice' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Balance Summary,') !== false) { ?>
							<tr><td>Customer Balance Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ar_customer_balance' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Balance by Invoice,') !== false) { ?>
							<tr><td>Customer Balance by Invoice</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ar_customer_invoice' ); ?></tr>
						<?php }
						if(strpos($reports,',Collections Report by Customer,') !== false) { ?>
							<tr><td>Collections Report by Customer</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ar_customer_collections' ); ?></tr>
						<?php }
						if(strpos($reports,',Invoice List,') !== false) { ?>
							<tr><td>Invoice List</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'invoice_list' ); ?></tr>
						<?php }
						if(strpos($reports,',POS Receivables,') !== false) { ?>
							<tr><td>POS Receivables</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'receivables' ); ?></tr>
						<?php }
						if(strpos($reports,',UI Invoice Report,') !== false) { ?>
							<tr><td>UI Invoice Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ar_ui_invoice' ); ?></tr>
						<?php } ?>

                        <!-- Profit & Loss -->
                        <tr><th colspan="4"><div style="text-align:left;width:100%;font-size:20px;">Profit &amp; Loss:</div></th></tr><?php
						if ( strpos($reports, ',Revenue Receivables,') !== false ) { ?>
							<tr><td>Revenue &amp; Receivables</td><?= subtab_config_function( $dbc, $tile, $level_url, 'revenue_receivables' ); ?></tr><?php
                        }
						if ( strpos($reports, ',Staff Compensation,') !== false ) { ?>
							<tr><td>Staff &amp; Compensation</td><?= subtab_config_function( $dbc, $tile, $level_url, 'staff_compensation' ); ?></tr><?php
                        }
						if ( strpos($reports, ',Expenses,') !== false ) { ?>
							<tr><td>Expenses</td><?= subtab_config_function( $dbc, $tile, $level_url, 'expenses' ); ?></tr><?php
                        }
						if ( strpos($reports, ',Costs,') !== false ) { ?>
							<tr><td>Costs</td><?= subtab_config_function( $dbc, $tile, $level_url, 'costs' ); ?></tr><?php
                        }
						if ( strpos($reports, ',Summary,') !== false ) { ?>
							<tr><td>Summary</td><?= subtab_config_function( $dbc, $tile, $level_url, 'summary' ); ?></tr><?php
                        } ?>

                        <!-- Marketing -->
						<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Marketing:</div></th></tr><?php
						if(strpos($reports,',Customer Contact List,') !== false) { ?>
							<tr><td>Customer Contact List</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_list' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Stats,') !== false) { ?>
							<tr><td>Customer Stats</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_stats' ); ?></tr>
						<?php }
						if(strpos($reports,',Demographics,') !== false) { ?>
							<tr><td>Demographics</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'demographs' ); ?></tr>
						<?php }
						if(strpos($reports,',Cart Abandonment,') !== false) { ?>
							<tr><td>Cart Abandonment</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'cart_abandonment' ); ?></tr>
						<?php }
						if(strpos($reports,',CRM Recommendations - By Date,') !== false) { ?>
							<tr><td>CRM Recommendations - By Date</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'crm_recommend_date' ); ?></tr>
						<?php }
						if(strpos($reports,',CRM Recommendations - By Customer,') !== false) { ?>
							<tr><td>CRM Recommendations - By Customer</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'crm_recommend_customer' ); ?></tr>
						<?php }
						if(strpos($reports,',POS Coupons,') !== false) { ?>
							<tr><td>POS Coupons</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pos_coupons' ); ?></tr>
						<?php }
						if(strpos($reports,',Net Promoter Score,') !== false) { ?>
							<tr><td>Net Promoter Score</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'net_promoter_score' ); ?></tr>
						<?php }
						if(strpos($reports,',Postal Code,') !== false) { ?>
							<tr><td>Postal Code</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'postal_code' ); ?></tr>
						<?php }
						if(strpos($reports,',Pro Bono Report,') !== false) { ?>
							<tr><td>Pro-Bono</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pro_bono' ); ?></tr>
						<?php }
						if(strpos($reports,',Promotions and Coupons,') !== false) { ?>
							<tr><td>Promotions & Coupons</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'promotions_coupons' ); ?></tr>
						<?php }
						if(strpos($reports,',Referral,') !== false) { ?>
							<tr><td>Referrals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'referral' ); ?></tr>
						<?php }
						if(strpos($reports,',Site Visitors,') !== false) { ?>
							<tr><td>Website Visitors</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'site_visitors' ); ?></tr>
						<?php }
						if(strpos($reports,',Web Referrals Report,') !== false) { ?>
							<tr><td>Web Referrals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'web_referrals' ); ?></tr>
						<?php } ?>

						<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Compensation:</div></th></tr><?php
						if(strpos($reports,',Adjustment Compensation,') !== false) { ?>
							<tr><td>Adjustment Compensation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'compensation_adjust' ); ?></tr>
						<?php }
						if(strpos($reports,',Hourly Compensation,') !== false) { ?>
							<tr><td>Hourly Compensation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'compensation_hourly' ); ?></tr>
						<?php }
						if(strpos($reports,',Therapist Compensation,') !== false) { ?>
							<tr><td>Therapist Compensation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'compensation_pt' ); ?></tr>
						<?php }
						if(strpos($reports,',Statutory Holiday Pay Breakdown,') !== false) { ?>
							<tr><td>Statutory Holiday Pay Breakdown</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'compensation_statutory_breakdown' ); ?></tr>
						<?php } ?>

						<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Customer:</div></th></tr><?php
						if(strpos($reports,',Customer Sales by Customer Summary,') !== false) { ?>
							<tr><td>Sales by Customer Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_sales_customer' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Sales History by Customer,') !== false) { ?>
							<tr><td>Sales History by Customer</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_sales_customer_history' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Patient Invoices,') !== false) { ?>
							<tr><td>Customer Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_patient_invoice' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Transaction List by Customer,') !== false) { ?>
							<tr><td>Transaction List by Customer</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_transaction_list' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Patient History,') !== false) { ?>
							<tr><td>Patient History</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_patient_history' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Customer Balance Summary,') !== false) { ?>
							<tr><td>Customer Balance Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_ar_customer_balance' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Customer Balance by Invoice,') !== false) { ?>
							<tr><td>Customer Balance by Invoice</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_ar_customer_invoice' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Collections Report by Customer,') !== false) { ?>
							<tr><td>Collections Report by Customer</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_ar_customer_collections' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Patient Aging Receivable Summary,') !== false) { ?>
							<tr><td>Patient Aging Receivable Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_ar_patient_aging' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Customer Contact List,') !== false) { ?>
							<tr><td>Customer Contact List</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_customer_list' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Customer Stats,') !== false) { ?>
							<tr><td>Customer Stats</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_customer_stats' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer CRM Recommendations - By Customer,') !== false) { ?>
							<tr><td>CRM Recommendations - By Customer</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_crm_recommend_customer' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Contact Postal Code,') !== false) { ?>
							<tr><td>Contact Postal Code</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_contact_postal_code' ); ?></tr>
						<?php }
						if(strpos($reports,',Customer Service Rates,') !== false) { ?>
							<tr><td>Service Rates</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_service_rates' ); ?></tr>
						<?php } ?>

						<tr><th colspan='4'><div style='text-align:left;width:100%;font-size:20px;'>Staff:</div></th></tr><?php
						if(strpos($reports,',Staff Staff Tickets,') !== false) { ?>
							<tr><td>Staff Tickets</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_staff_tickets' ); ?></tr>
						<?php }
						if(strpos($reports,',Staff Scrum Staff Productivity Summary,') !== false) { ?>
							<tr><td>Scrum Staff Productivity Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_scrum_staff_productivity_summary' ); ?></tr>
						<?php }
						if(strpos($reports,',Staff Staff Daysheet,') !== false) { ?>
							<tr><td>Staff Therapist Day Sheet</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_pt_daysheet' ); ?></tr>
						<?php }
						if(strpos($reports,',Staff Therapist Stats,') !== false) { ?>
							<tr><td>Therapist Stats</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_pt_stats' ); ?></tr>
						<?php }
						if(strpos($reports,',Staff Day Sheet Report,') !== false) { ?>
							<tr><td>Day Sheet Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_day_sheet_report' ); ?></tr>
						<?php }
						if(strpos($reports,',Staff Staff Revenue Report,') !== false) { ?>
							<tr><td>Staff Revenue Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_staff_revenue' ); ?></tr>
						<?php }
						if(strpos($reports,',Staff Gross Revenue by Staff,') !== false) { ?>
							<tr><td>Gross Revenue by Staff</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_staff_gross_revenue' ); ?></tr>
						<?php }
						if(strpos($reports,',Staff Validation by Therapist,') !== false) { ?>
							<tr><td>Validation by Therapist</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_pt_validation' ); ?></tr>
						<?php }
						if(strpos($reports,',Staff Staff Compensation,') !== false) { ?>
							<tr><td>Staff Compensation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_staff_compensation' ); ?></tr>
						<?php }
					/* End Reports tile subtab settigns */
					endif;

					/* Passwords tile subtab settings */
					if ( $tile == 'passwords' ) {
						$tabs		= get_config ( $dbc, 'password_category' );
						$each_tab	= explode ( ',', $tabs );
						foreach ( $each_tab as $subtab ) {
							echo '<tr>';
								echo '<td>' . $subtab . '</td>';
								echo subtab_config_function( $dbc, $tile, $level_url, $subtab );
							echo '</tr>';
						}
					}
					/* End Passwords tile subtab settigns */

					/* Rate Cards tile subtab settings */
					if ( $tile == 'rate_card' ) { ?>
						<tr><td>My Company</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'company' ); ?></tr>
						<tr><td><?= ESTIMATE_TILE ?> Scope Template</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'estimate' ); ?></tr>
						<tr><td>Customer Specific</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer' ); ?></tr>
						<tr><td>Position</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'position' ); ?></tr>
						<tr><td>Staff</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff' ); ?></tr>
						<tr><td>Equipment</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'equipment' ); ?></tr>
						<tr><td>Equipment Category</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'category' ); ?></tr>
						<tr><td>Labour</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'labour' ); ?></tr><?php
					}

					/* HR tile subtab settings */
					if ( $tile == 'hr' ) {
						$hr_tabs = get_config($dbc, 'hr_tabs');
						foreach (explode(',', $hr_tabs) as $hr_tab) {?>
							<tr><td><?= $hr_tab ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, $hr_tab ); ?></tr><?php
						}
						?><tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr>
						<tr><td>Request an Update</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'request_update' ); ?></tr><?php
					}

					/* Expenses tile subtab settings */
					if ( $tile == 'expense' ) { ?>
						<tr><td>Budget</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'budget' ); ?></tr>
						<tr><td>Current Month</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'current_month' ); ?></tr>
						<tr><td>Businesses</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'business' ); ?></tr>
						<tr><td>Customers</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customers' ); ?></tr>
						<tr><td>Clients</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'clients' ); ?></tr>
						<tr><td>Staff</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff' ); ?></tr>
						<tr><td>Sales Leads</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales' ); ?></tr>
						<tr><td>Manager Approval</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'manager' ); ?></tr>
						<tr><td>Payables</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'payables' ); ?></tr>
						<tr><td>Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'report' ); ?></tr><?php
					}

					/* Point of Sale Basic tile subtab settings */
					if ( $tile == 'pos' ) { ?>
						<tr><td>Sell</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sell' ); ?></tr>
						<tr><td>Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'invoices' ); ?></tr>
						<tr><td>Returns</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'returns' ); ?></tr>
						<tr><td>Accounts Receivable</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'unpaid' ); ?></tr>
						<tr><td>Voided Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'voided' ); ?></tr><?php
					}

					/* Invoicing tile subtab settings */
					if ( $tile == 'invoicing' ) { ?>
						<tr><td>Sell</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sell' ); ?></tr>
						<tr><td>Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'invoices' ); ?></tr>
						<tr><td>Returns</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'returns' ); ?></tr>
						<tr><td>Accounts Receivable</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'unpaid' ); ?></tr>
						<tr><td>Voided Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'voided' ); ?></tr><?php
					}

					/* Invoice tile subtab settings */
					if ( $tile == 'invoice' ) { ?>
						<tr><td>Sell</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sell' ); ?></tr>
						<tr><td>Today's Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'today' ); ?></tr>
						<tr><td>All Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'all' ); ?></tr>
						<tr><td>Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'invoices' ); ?></tr>
						<tr><td>Unbilled <?= TICKET_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'unbilled_tickets' ); ?></tr>
						<tr><td>Refund / Adjustments</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'refunds' ); ?></tr>
						<tr><td>Accounts Receivable</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'unpaid' ); ?></tr>
						<tr><td>Voided Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'voided' ); ?></tr>
						<tr><td>Unpaid Insurer Invoices Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ui_report' ); ?></tr>
						<tr><td>Cash Out</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'cashout' ); ?></tr><?php
					}

					/* Point of Sale Advanced tile subtab settings */
					if ( $tile == 'posadvanced' ) { ?>
						<tr><td>Sell</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sell' ); ?></tr>
						<tr><td>Today's Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'today' ); ?></tr>
						<tr><td>All Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'all' ); ?></tr>
						<tr><td>Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'invoices' ); ?></tr>
						<tr><td>Unbilled <?= TICKET_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'unbilled_tickets' ); ?></tr>
						<tr><td>Refund / Adjustments</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'refunds' ); ?></tr>
						<tr><td>Accounts Receivable</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'unpaid' ); ?></tr>
						<tr><td>Voided Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'voided' ); ?></tr>
						<tr><td>Unpaid Insurer Invoices Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ui_report' ); ?></tr>
						<tr><td>Cash Out</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'cashout' ); ?></tr><?php
					}

					/* Sales tile subtab settings */
					if ( $tile == 'sales' ) { ?>
						<tr><td>How to Guide</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'how_to_guide' ); ?></tr>
						<tr><td>Sales Pipeline</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sales_pipeline' ); ?></tr>
						<tr><td>Schedule</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'schedule' ); ?></tr>
						<tr><td>Reports</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reports' ); ?></tr><?php
					}

					/* Sales Order tile subtab settings */
					if ( $tile == 'sales_order' ) { ?>
						<tr><td>Create an Order</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'create' ); ?></tr>
						<tr><td>Pending Orders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pending' ); ?></tr>
						<tr><td>Completed <?= SALES_ORDER_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'completed' ); ?></tr><?php
					}

					/* Projects/Jobs tile subtab settings */
					if ( $tile == 'project' ) {
						$project_tabs = get_config($dbc, 'project_tabs');
						if($project_tabs == '') {
							$project_tabs = 'Client,SR&ED,Internal,R&D,Business Development,Process Development,Addendum,Addition,Marketing,Manufacturing,Assembly';
						}
						$project_tabs = explode(',',$project_tabs);
						$project_vars = [];
						foreach($project_tabs as $item) {
							$var_name = preg_replace('/[^a-z_]/','',str_replace(' ','_',strtolower($item)));
							$project_vars[] = $var_name;
						} ?>
						<tr><td><?php echo PROJECT_TILE; ?> Tab</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'nav_projects' ); ?></tr>
						<tr><td>SCRUM</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'nav_scrum' ); ?></tr>
						<tr><td><?= TICKET_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'nav_tickets' ); ?></tr>
						<tr><td>Planner</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'nav_daysheet' ); ?></tr>
						<tr><td>Pending Projects</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pending' ); ?></tr>
						<tr><td>Administration</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'administration' ); ?></tr>
						<?php foreach($project_tabs as $key => $var): ?>
							<tr><td><?php echo $var.' '.PROJECT_TILE; ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, $project_vars[$key] ); ?></tr>
						<?php endforeach; ?>
						<tr><td>Project Path</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'view_path' ); ?></tr>
						<tr><td>Project Details</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'view_details' ); ?></tr>
						<tr><td>Scope of Work</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'view_scope' ); ?></tr>
						<tr><td>Action Items</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'view_action' ); ?></tr>
						<tr><td>Communications</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'view_communications' ); ?></tr>
						<tr><td>Administration</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'view_administration' ); ?></tr>
						<tr><td>Accounting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'view_accounting' ); ?></tr>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'view_reporting' ); ?></tr>
						<tr><td>Billing</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'view_billing' ); ?></tr>
						<?php $field_config_project_custom_details = mysqli_fetch_all(mysqli_query($dbc, "SELECT DISTINCT `tab` FROM `field_config_project_custom_details`"),MYSQLI_ASSOC);
						foreach ($field_config_project_custom_details as $field_config_project_custom_detail) { ?>
							<tr><td><?= $field_config_project_custom_detail['tab'] ?></td><?php subtab_config_function( $dbc, $tile, $level_url, 'custom_'.config_safe_str($field_config_project_custom_detail['tab']) ); ?></tr>
						<?php } ?>
					<?php }

					/* My Profile tile subtab settings */
					if ( $tile == 'profile' ) { ?>
						<!-- <tr><td>Staff</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff' ); ?></tr>
						<tr><td>Profile</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'profile' ); ?></tr> -->
						<tr><td>Profile</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_information' ); ?></tr>
						<tr><td>Staff Bio</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_bio' ); ?></tr>
						<tr><td>Staff Address</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_address' ); ?></tr>
						<tr><td>Employee Information</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'employee_information' ); ?></tr>
						<tr><td>Driver Information</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'driver_information' ); ?></tr>
						<tr><td>Direct Deposit Information</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'direct_deposit_information' ); ?></tr>
						<tr><td>Social Media</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'social_media' ); ?></tr>
						<tr><td>Emergency</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'emergency' ); ?></tr>
						<tr><td>Health Care</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'health' ); ?></tr>
						<tr><td>Health Concerns</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'health_concerns' ); ?></tr>
						<tr><td>Company Benefits</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'company_benefits' ); ?></tr>
						<tr><td>Staff Schedule</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'schedule' ); ?></tr>
						<tr><td>HR Record</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'hr' ); ?></tr>
						<tr><td>Software ID</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'software_id' ); ?></tr>
						<tr><td>Software Access</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'software_access' ); ?></tr>
						<tr><td>Accreditation &amp; Certificates</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'certificates' ); ?></tr>
						<tr><td>Goals &amp; Objectives</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'goals' ); ?></tr><?php
					}

					/* Tasks tile subtab settings */
					if ( $tile == 'tasks' || $tile == 'tasks_updated') { ?>
						<tr><td>My <?= TASK_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'my' ); ?></tr>
						<tr><td>Company <?= TASK_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'company' ); ?></tr>
						<tr><td>Community <?= TASK_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'community' ); ?></tr>
						<tr><td>Business <?= TASK_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'business' ); ?></tr>
						<tr><td>Client <?= TASK_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'client' ); ?></tr>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr><?php
					}

					/* Day Sheet tile subtab settings */
					if ( $tile == 'daysheet' ) { ?>
						<tr><td><?= TICKET_NOUN ?> Day Sheet</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ticket' ); ?></tr>
						<tr><td>Work Order Day Sheet</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'work_order' ); ?></tr>
						<tr><td>Day Overview</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'overview' ); ?></tr><?php
					}

					/* Information Gathering tile subtab settings */
					if ( $tile == 'infogathering' ) { ?>
						<tr><td>Forms</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'forms' ); ?></tr>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr><?php
					}

					/* Gantt Chart tile subtab settings */
					if ( $tile == 'gantt_chart' ) { ?>
						<tr><td>Estimated</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'estimated' ); ?></tr>
						<tr><td>Gantt Chart</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'chart' ); ?></tr><?php
					}

					/* Purchase Order tile subtab settings */
					if ( $tile == 'purchase_order' ) { ?>
						<tr><td>Create an Order</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'create' ); ?></tr>
						<tr><td>Pending Orders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pending' ); ?></tr>
						<tr><td>Receiving</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'receiving' ); ?></tr>
						<tr><td>Accounts Payable</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'payable' ); ?></tr>
						<tr><td>Remote Purchase Orders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'remote' ); ?></tr>
						<tr><td>Completed Purchase Orders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'completed' ); ?></tr><?php
					}

					/* Operations Manual tile subtab settings */
					if ( $tile == 'ops_manual' ) { ?>
						<tr><td>Dashboard</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'dashboard' ); ?></tr>
						<tr><td>Follow Up</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'followup' ); ?></tr>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr><?php
					}

					/* Payroll tile subtab settings */
					if ( $tile == 'payroll' ) { ?>
						<tr><td>Staff Compensation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'compensation' ); ?></tr>
						<tr><td>Staff Salary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'salary' ); ?></tr>
						<tr><td>Contractor Compensation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'contractor' ); ?></tr>
						<tr><td>Field Tickets Payroll</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'field_ticket' ); ?></tr>
						<tr><td>Shop Work Orders Payroll</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'shop_work_order' ); ?></tr><?php
					}

					/* Email Communication tile subtab settings */
					if ( $tile == 'email_communication' ) { ?>
						<tr><td>Internal</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'internal' ); ?></tr>
						<tr><td>External</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'external' ); ?></tr>
						<tr><td>Log</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'log' ); ?></tr><?php
					}

					/* Equipment tile subtab settings */
					if ( $tile == 'equipment' ) { ?>
						<tr><td>Equipment Lists</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'equipment' ); ?></tr>
						<tr><td>Assigned Equipment</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'inspection' ); ?></tr>
						<tr><td>Inspections</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'assign' ); ?></tr>
						<tr><td>Work Orders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'work_orders' ); ?></tr>
						<tr><td>Expenses &amp; Balance Sheets</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'expenses' ); ?></tr>
						<tr><td>Service Schedules</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'schedules' ); ?></tr>
						<tr><td>Service Requests</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'requests' ); ?></tr>
						<tr><td>Service Records</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'records' ); ?></tr>
						<tr><td>Checklist</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'checklist' ); ?></tr>
						<tr><td>Equipment Assignment</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'equip_assign' ); ?></tr><?php
					}

					/* Time Tracking subtab settings */
					if ( $tile == 'time_tracking' ) { ?>
						<tr><td>Time Tracking</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'tracking' ); ?></tr>
						<tr><td>Shop Time Sheets</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'shop_time_sheets' ); ?></tr><?php
					}

					/* Project Billing & Invoices subtab settings */
					if ( $tile == 'billing' ) { ?>
						<tr><td>Billing</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'billing' ); ?></tr>
						<tr><td>Generated Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'invoices' ); ?></tr>
						<tr><td>Accounts Receivable</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'accounts_receivable' ); ?></tr>
					<?php }

					/* Communication subtab settings */
					if ( $tile == 'communication_schedule' ) { ?>
						<tr><td>Email Schedule</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'email' ); ?></tr>
						<tr><td>Phone Schedule</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'phone' ); ?></tr>
					<?php }

					/* Client Documentation subtab settings */
					if ( $tile == 'client_documentation' ) { ?>
						<tr><td>Medication</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'medication' ); ?></tr>
						<tr><td>Charts</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'charts' ); ?></tr>
						<tr><td>Day Program</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'day_program' ); ?></tr>
						<tr><td>Individual Service Plan</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'individual_support_plan' ); ?></tr>
						<tr><td>Daily Log Notes</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'daily_log_notes' ); ?></tr>
					<?php }
					// End of Client Documentation subtab settings

					/* Settings tile subtab settings */
					if ( $tile == 'software_config' ) { ?>
						<tr><td>Styling</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'style' ); ?></tr>
						<tr><td>Styling: Software Default</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'style_software' ); ?></tr>
						<tr><td>Styling: Security Level</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'style_security' ); ?></tr>
						<tr><td>Formatting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'format' ); ?></tr>
						<tr><td>Menu Formatting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'menus' ); ?></tr>
						<tr><td>Tile Sort Order</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'tile_order' ); ?></tr>
						<tr><td>Dashboards</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'dashboard' ); ?></tr>
						<tr><td>Software Identity</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'identity' ); ?></tr>
						<tr><td>Software Login Page</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'login' ); ?></tr>
						<tr><td>Social Media Links</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'social' ); ?></tr>
						<tr><td>URL Favicon</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'favicon' ); ?></tr>
						<tr><td>Logo</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'logo' ); ?></tr>
						<tr><td>Contacts Sort Order</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'contact_sort' ); ?></tr>
						<tr><td>Font Settings</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'font' ); ?></tr>
						<tr><td>Data Usage</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'data_use' ); ?></tr>
						<tr><td>Notes</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'notes' ); ?></tr>
						<tr><td>Notifications: Software Default</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'notifications_software' ); ?></tr>
					<?php }
					// include ('../Settings/settings_subtab_settings.php');
					/* End of Settings tile subtab settings */

					// Client Projects Subtab Settings
					if ( $tile == 'client_projects' ) { ?>
						<tr><td>Pending Projects</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pending' ); ?></tr>
						<tr><td>Active Projects</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'active' ); ?></tr>
						<tr><td>Archived Projects</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'archived' ); ?></tr>
						<tr><td><?= TICKET_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'tickets' ); ?></tr>
						<tr><td>Daysheet</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'daysheet' ); ?></tr>
					<?php }
					// Client Projects

					// Contracts Subtab Settings
					if ( $tile == 'contracts' ) {
						$contract_tabs = explode('#*#',mysqli_fetch_array(mysqli_query($dbc, "SELECT `contract_tabs` FROM `field_config_contracts`"))['contract_tabs']);
						foreach ($contract_tabs as $contract_tab) { ?>
							<tr><td><?= $contract_tab ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, config_safe_str($contract_tab) ); ?></tr>
						<?php } ?>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr>
					<?php }
					// Contracts Projects

					/* Communication subtab settings */
					if ( $tile == 'certificate' ) { ?>
						<tr><td>Active</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'cert_active' ); ?></tr>
						<tr><td>Suspended</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'cert_suspended' ); ?></tr>
						<tr><td>Follow Up</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'cert_followup' ); ?></tr>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'cert_reporting' ); ?></tr><?php
                    }

					/* Non Verbal Communication/Emoji Comm subtab settings */
					if ( $tile == 'non_verbal_communication' ) { ?>
						<tr><td>Emotions</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'emotions' ); ?></tr>
						<tr><td>Activities</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'activities' ); ?></tr><?php
                    }

					/* Form Builder subtab settings */
					if ( $tile == 'form_builder' ) { ?>
						<tr><td>Custom Forms</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'form_list' ); ?></tr>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr><?php
                    }

					/* Policies & Procedures subtab settings */
					if ( $tile == 'policy_procedure' ) { ?>
						<tr><td>Manuals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'manuals' ); ?></tr>
						<tr><td>Follow Up</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'follow_up' ); ?></tr>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr><?php
                    }

					/* Security subtab settings */
					if ( $tile == 'security' ) { ?>
						<tr><td>Software Functionality</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'tiles' ); ?></tr>
						<tr><td>Security Levels &amp; Groups</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'levels' ); ?></tr>
						<tr><td>Set Security Privileges</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'privileges' ); ?></tr>
						<tr><td>User Privileges</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'assign' ); ?></tr>
						<tr><td>Contact Category Default Levels</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'contact_cat' ); ?></tr>
						<tr><td>Password Reset</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'password' ); ?></tr>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr><?php
                    }

					/* Employee Handbook subtab settings */
					if ( $tile == 'emp_handbook' ) { ?>
						<tr><td>Manuals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'manuals' ); ?></tr>
						<tr><td>Follow Up</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'follow_up' ); ?></tr>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr><?php
                    }

					/* Safety Manual subtab settings */
					if ( $tile == 'safety_manual' ) { ?>
						<tr><td>Manuals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'manuals' ); ?></tr>
						<tr><td>Follow Up</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'follow_up' ); ?></tr>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr><?php
                    }

					/* Project Workflow subtab settings */
					if ( $tile == 'project_workflow' ) { ?>
						<tr><td>Active Workflow</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'active' ); ?></tr>
						<tr><td>Add/Edit Workflow</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'add_edit' ); ?></tr><?php
                    }

					/* Safety subtab settings */
					if ( $tile == 'safety' ) {
                        $safety_main        = get_config($dbc, 'safety_main_site_tabs');
                        $each_safety_main   = explode(',', $safety_main);
                        foreach ($each_safety_main as $cat_safety_main) { ?>
                            <tr><td><?= $cat_safety_main; ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, strtolower(str_replace(' ', '_', $cat_safety_main)) ); ?></tr><?php
                        } ?>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr>
						<tr><td>Driving Log</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'driving_log' ); ?></tr>
						<tr><td>FLHA</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'FLHA' ); ?></tr>
						<tr><td>Toolbox</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'toolbox' ); ?></tr>
						<tr><td>Tailgate</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'tailgate' ); ?></tr>
						<tr><td>Forms</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'forms' ); ?></tr>
						<tr><td>Manuals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'manuals' ); ?></tr>
						<tr><td><?= INC_REP_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'incidents' ); ?></tr>
                    <?php }

					/* Checklist subtab settings */
					if ( $tile == 'checklist' ) { ?>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr><?php
                    }

					/* Time Clock subtab settings */
					if ( $tile == 'sign_in_time' ) { ?>
						<tr><td>How To Guide</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'how_to_guide' ); ?></tr>
						<tr><td>Time Clock</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'time_clock' ); ?></tr><?php
                    }
					if ( $tile == 'punch_card' ) { ?>
						<tr><td>How To Guide</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'how_to_guide' ); ?></tr>
						<tr><td>Time Clock</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'time_clock' ); ?></tr><?php
                    }

					/* Cold Call subtab settings */
					if ( $tile == 'calllog' ) { ?>
						<tr><td>How To Guide</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'how_to_guide' ); ?></tr>
						<tr><td>Preparation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'preparation' ); ?></tr>
						<tr><td>Cold Call Pipeline</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pipeline' ); ?></tr>
						<tr><td>Schedule</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'schedule' ); ?></tr>
						<tr><td>Lead Bank</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'lead_bank' ); ?></tr>
						<tr><td>Goals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'goals' ); ?></tr>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reporting' ); ?></tr><?php
                    }

					/* Field Ticket Estimates subtab settings */
					if ( $tile == 'field_ticket_estimates' ) { ?>
						<tr><td>Bid</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'bid' ); ?></tr>
						<tr><td>Cost Estimate</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'cost_estimate' ); ?></tr><?php
                    }

					/* Services subtab settings */
					if ( $tile == 'services' ) {
                        $sql = mysqli_query($dbc, "SELECT DISTINCT(`category`) FROM `services` WHERE `category`!='' ORDER BY `category`");
                        while ( $row=mysqli_fetch_assoc($sql) ) {
                            $row_cat_subtab = str_replace ( ['&', '/', ',', ' ', '___', '__'], ['', '_', '', '_', '_', '_'], $row['category'] ); ?>
                            <tr><td><?= $row['category']; ?></td><?= subtab_config_function( $dbc, $tile, $level_url, $row_cat_subtab ); ?></tr><?php
                        }
                    }

					/* Cost Estimates subtab settings */
					if ( $tile == 'cost_estimate' ) { ?>
						<tr><td>Internal Cost Estimates</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'internal_cost_estimate' ); ?></tr>
						<tr><td>Customer Cost Estimates</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'customer_cost_estimates' ); ?></tr><?php
                    }

					/* Time Sheets subtab settings */
					if ( $tile == 'timesheet' ) { ?>
						<tr><td>Time Sheets</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'Time_Sheets' ); ?></tr>
						<?php if(in_array('search_staff', explode(',',get_field_config($dbc, 'time_cards')))) { ?>
							<tr><td>Time Sheets - Search by Staff</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'search_staff' ); ?></tr>
						<?php } ?>
						<tr><td>Pay Period</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'Pay_Period' ); ?></tr>
						<tr><td>Holidays</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'Holidays' ); ?></tr>
						<tr><td>Coordinator Approvals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'Coordinator_Approvals' ); ?></tr>
						<tr><td>Manager Approvals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'Manager_Approvals' ); ?></tr>
						<tr><td>Reporting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'Reporting' ); ?></tr>
						<tr><td>Payroll</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'Payroll' ); ?></tr><?php
                    }

					/* Treatment Charts subtab settings */
					if ( $tile == 'treatment_charts' ) { ?>
						<tr><td>Front Desk</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'front_desk' ); ?></tr>
						<tr><td>Physiotherapy</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'physiotherapy' ); ?></tr>
						<tr><td>Massage Therapy</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'massage' ); ?></tr>
						<tr><td>MVC/MVA</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'mvc' ); ?></tr>
						<tr><td>WCB</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'wcb' ); ?></tr>
						<tr><td>Uncategorized Forms</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'uncategorized' ); ?></tr><?php
                    }

					/* Ticket subtab settings */
					if ( $tile == 'ticket' ) { ?>
						<?php foreach(array_filter(explode(',',get_config($dbc, 'ticket_tabs'))) as $ticket_subtab) { ?>
							<tr><td>Subtab: <?= $ticket_subtab ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ticket_type_'.config_safe_str($ticket_subtab) ); ?></tr>
						<?php } ?>
						<?php foreach(array_filter(explode(',',get_config($dbc, 'ticket_status'))) as $status) { ?>
							<tr><td>Status: <?= $status ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ticket_status'.config_safe_str($status) ); ?></tr>
						<?php } ?>
						<tr><td>Print PDF</td><?php echo subtab_config_function($dbc, $tile, $level_url, 'view_pdf'); ?></tr>
						<tr><td>View <?= PROJECT_NOUN ?> Information</td><?php echo subtab_config_function($dbc, $tile, $level_url, 'view_project_info'); ?></tr>
						<tr><td>View <?= PROJECT_NOUN ?> Details</td><?php echo subtab_config_function($dbc, $tile, $level_url, 'view_project_details'); ?></tr>
						<tr><td>View Staff List</td><?php echo subtab_config_function($dbc, $tile, $level_url, 'view_staff'); ?></tr>
						<tr><td>View Summary</td><?php echo subtab_config_function($dbc, $tile, $level_url, 'view_summary'); ?></tr>
						<tr><td>View Notifications</td><?php echo subtab_config_function($dbc, $tile, $level_url, 'view_notifications'); ?></tr>
						<tr><td>View History</td><?php echo subtab_config_function($dbc, $tile, $level_url, 'view_history'); ?></tr>
						<tr><td>Edit Service Price</td><?php echo subtab_config_function($dbc, $tile, $level_url, 'edit_service_total'); ?></tr>
						<tr><td>View Service Price</td><?php echo subtab_config_function($dbc, $tile, $level_url, 'view_service_total'); ?></tr>
						<tr><td>Administration</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'administration' ); ?></tr>
						<tr><td>Accounting</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'invoice' ); ?></tr>
						<tr><td>Import / Export <?= TICKET_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'export' ); ?></tr>
						<tr><td><?= PROJECT_NOUN ?> Information</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'project' ); ?></tr>
						<tr><td>Staff Information</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_list' ); ?></tr>
						<tr><td>Client / Member Information</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'contact_list' ); ?></tr>
						<tr><td>Wait List</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'wait_list' ); ?></tr>
						<tr><td>Check In / Check Out / Staff Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_checkin' ); ?></tr>
						<tr><td>Check In / Check Out All</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'all_checkin' ); ?></tr>
						<tr><td>Medication</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'medication' ); ?></tr>
						<tr><td>Complete <?= TICKET_NOUN ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'complete' ); ?></tr>
						<tr><td>Services</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'services' ); ?></tr>
						<tr><td>View Completion</td><?php echo subtab_config_function($dbc, $tile, $level_url, 'view_complete'); ?></tr>
						<tr><td>Delete <?= TICKET_NOUN ?> Notes</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'delete_notes' ); ?></tr>
						<tr><td>View Payable Hours in Planned/Tracked/Payable Table</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'view_payable' ); ?></tr>
						<tr><td>All Other Accordions</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'all_access' ); ?></tr><?php
                    }

					/* PT Day Sheet tile subtab settings */
					if ($tile == 'therapist') { ?>
						<tr><td>Day Sheet</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'daysheet' ); ?></tr>
						<tr><td>Patient History</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'history' ); ?></tr>
						<tr><td>Patient Block Booking</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'block_booking' ); ?></tr>
						<tr><td>Compensation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'compensation' ); ?></tr>
						<tr><td>Appointment Summary</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'summary' ); ?></tr><?php
                    }

					/* CRM tile subtab settings */
					if ($tile == 'crm') { ?>
						<tr><td>Referrals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'referrals' ); ?></tr>
						<tr><td>Recommendations</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'recommendations' ); ?></tr>
						<tr><td>Surveys</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'surveys' ); ?></tr>
						<tr><td>Birthdays &amp; Promotions</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'birthdays_promotions' ); ?></tr>
						<tr><td>6 Month Follow Up Email</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'follow_up_email' ); ?></tr>
						<tr><td>Newsletter</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'newsletter' ); ?></tr>
						<tr><td>Reminders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'reminders' ); ?></tr><?php
                    }

					/* Budget tile subtab settings */
					if ($tile == 'budget') { ?>
						<tr><td>How To Guide</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'how_to_guide' ); ?></tr>
						<tr><td>Pending Budgets</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'pending_budget' ); ?></tr>
						<tr><td>Active Budgets</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'active_budget' ); ?></tr>
						<tr><td>Budget Expense Tracking</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'expense_tracking' ); ?></tr><?php
                    }

					/* Injury tile subtab settings */
					if ($tile == 'injury') { ?>
						<tr><td>Active</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'active' ); ?></tr>
						<tr><td>Discharged</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'discharged' ); ?></tr><?php
                    }

                    /* Calendar subtab settings */
                    if ($tile == 'calendar_rook') {
                    	$calendar_types = explode(',',get_config($dbc, 'calendar_types'));
                    	foreach ($calendar_types as $calendar_type) { ?>
                    		<tr><td><?= $calendar_type ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, $calendar_type ); ?></tr><?php
                    	}
                    }

					/* Notifications tile subtab settings */
					if ($tile == 'confirmation') { ?>
						<tr><td>Appointment Confirmations</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'appointments' ); ?></tr>
						<tr><td>- Email Confirmation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'email' ); ?></tr>
						<tr><td>- Call Confirmation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'call' ); ?></tr>
						<tr><td>Ticket Notifications</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'tickets' ); ?></tr>
						<tr><td>Follow Up</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'followup' ); ?></tr>
						<tr><td>- Add Follow Up Feedback</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'add_feedback' ); ?></tr>
						<tr><td>- View Follow Up Feedback</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'view_feedback' ); ?></tr><?php
                    }

					/* Reactivation/Follo Up tile subtab settings */
					if ($tile == 'reactivation') { ?>
						<tr><td>Active Reactivations</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'active' ); ?></tr>
						<tr><td>Inactive Reactivations</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'inactive' ); ?></tr>
						<tr><td>Cancellations</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'cancellations' ); ?></tr>
						<tr><td>Cold Call</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'cold_call' ); ?></tr>
						<tr><td>Assessment Follow-Up</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'assessment_followup' ); ?></tr>
						<tr><td>Deactivated Contacts</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'deactivated_contacts' ); ?></tr><?php
                    }

					/* Exercise Library tile subtab settings */
					if ($tile == 'exercise_library') { ?>
						<tr><td>Send Exercise Plan</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'send_plan' ); ?></tr>
						<tr><td>Company Library</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'company' ); ?></tr>
						<tr><td>My Private Library</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'private' ); ?></tr><?php
                    }

					/* Fund Development tile subtab settings */
					if ($tile == 'fund_development') { ?>
						<tr><td>Funders</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'funders' ); ?></tr>
						<tr><td>Funding</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'funding' ); ?></tr><?php
                    }

                    /* Performance Review tile subtab settings */
                    if ($tile == 'preformance_review') {
                    	$pr_positions = explode(',', get_config($dbc, 'performance_review_positions'));
                    	foreach ($pr_positions as $pr_position) { ?>
                    		<tr><td><?= $pr_position ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, $pr_position ); ?></tr><?php
                    	}
	                }

                    /* Social Story tile subtab settings */
                    if ($tile == 'social_story') { ?>
                    	<tr><td>Key Methodologies</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'key_methodologies' ); ?></tr>
                    	<tr><td>Learning Techniques</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'learning_techniques' ); ?></tr>
                    	<tr><td>Protocols</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'protocols' ); ?></tr>
                    	<tr><td>Patterns</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'patterns' ); ?></tr>
                    	<tr><td>Routines</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'routines' ); ?></tr>
                    	<tr><td>Communication</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'communication' ); ?></tr>
                    	<tr><td>Activities</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'activities' ); ?></tr><?php
	                }

					/* Client Documentation subtab settings */
					if ( $tile == 'charts' ) { ?>
						<tr><td>Bowel Movement</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'bowel_movement' ); ?></tr>
						<tr><td>Seizure Record</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'seizure_record' ); ?></tr>
						<tr><td>Blood Glucose</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'blood_glucose' ); ?></tr>
						<tr><td>Daily Water Temp (Client)</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'daily_water_temp' ); ?></tr>
						<tr><td>Daily Water Temp (Program)</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'daily_water_temp_bus' ); ?></tr>
						<tr><td>Daily Fridge Temp</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'daily_fridge_temp' ); ?></tr>
						<tr><td>Daily Freezer Temp</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'daily_freezer_temp' ); ?></tr>
						<tr><td>Daily Dishwasher Temp</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'daily_dishwasher_temp' ); ?></tr>
						<tr><td>Custom Charts</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'custom_chart' ); ?></tr><?php
                    }

					/* Check Out subtab settings */
					if ( $tile == 'check_out' ) { ?>
						<tr><td>Create Invoice</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'sell' ); ?></tr>
						<tr><td>Today's Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'today' ); ?></tr>
						<tr><td>All Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'all' ); ?></tr>
						<tr><td>Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'invoices' ); ?></tr>
						<tr><td>Unbilled <?= TICKET_TILE ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'unbilled_tickets' ); ?></tr>
						<tr><td>Accounts Receivable</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'unpaid' ); ?></tr>
						<tr><td>Voided Invoices</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'voided' ); ?></tr>
						<tr><td>Refund / Adjustments</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'refunds' ); ?></tr>
						<tr><td>Unpaid Insurer Invoice Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ui_report' ); ?></tr>
						<tr><td>Cash Out</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'cashout' ); ?></tr>
						<tr><td>Gift Card</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'gf' ); ?></tr><?php
                    }

					/* Accounts Receivable subtab settings */
					if ( $tile == 'accounts_receivables' ) { ?>
						<tr><td>Insurer A/R</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'insurer_ar' ); ?></tr>
						<tr><td>Patient A/R</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'patient_ar' ); ?></tr>
						<tr><td>UI Reports</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'ui_invoice_report' ); ?></tr>
						<tr><td>Insurer Paid A/R Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'insurer_ar_report' ); ?></tr>
						<tr><td>Insurer A/R Clinic Master</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'insurer_ar_cm' ); ?></tr><?php
                    }

					/* Agendas & Meetings subtab settings */
					if ( $tile == 'agenda_meeting' ) { ?>
						<tr><td>How To Guide</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'how_to_guide' ); ?></tr>
						<tr><td>Agendas</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'agenda' ); ?></tr>
						<tr><td>Meetings</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'meeting' ); ?></tr><?php
                    }

                    /* Documents */
					if ( $tile == 'documents_all' ) {
						$documents_all_tabs = explode(',',get_config($dbc, 'documents_all_tabs'));
						foreach ($documents_all_tabs as $documents_all_tab) { ?>
							<tr><td><?= $documents_all_tab ?></td><?php echo subtab_config_function( $dbc, $tile, $level_url, $documents_all_tab ); ?></tr><?php
						}
                    }

					/* Compensation subtab settings */
					if ( $tile == 'goals_compensation' ) { ?>
						<tr><td>Staff Goals</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_goals' ); ?></tr>
						<tr><td>Staff Compensation</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'staff_comp' ); ?></tr>
						<tr><td>Stat Report</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'stat_report' ); ?></tr>
						<tr><td>Hourly Pay</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'hourly_pay' ); ?></tr><?php
                    }

					/* Trip Optimizer subtab settings */
					if ( $tile == 'optimize' ) { ?>
						<tr><td>Upload</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'upload' ); ?></tr>
						<tr><td>Macros</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'macros' ); ?></tr>
						<tr><td>Assign</td><?php echo subtab_config_function( $dbc, $tile, $level_url, 'assign' ); ?></tr><?php
                    } ?>

				</table>

				<div class="double-pad-top"><a class="btn brand-btn btn-lg" href="security.php?tab=privileges<?php if(isset($_GET['level']) && $_GET['level'] !== '') { echo '&level='.$_GET['level']; } ?>">Back</a></div>

			</div><!-- .col-md-12 -->
        </div><!-- .row -->
    </div><!-- .container -->

<?php include ('../footer.php'); ?>